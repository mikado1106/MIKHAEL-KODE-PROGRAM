<?php

namespace App\Controllers;

use App\Models\IzinModel;
use App\Models\CutiModel;
use App\Models\AbsensiModel;

class Employee extends BaseController
{

    private function currentKaryawanId(): ?int
    {
        $uid = (int) session('user_id');
        if (!$uid) return null;
        $db = \Config\Database::connect();
        $row = $db->table('karyawan')->where('user_id', $uid)->get()->getRowArray();
        return $row['id'] ?? null;
    }

    public function resignForm()
    {
        $karyawanId = $this->currentKaryawanId();
        if (!$karyawanId) {
            return redirect()->to('/login')->with('error', 'Sesi habis. Silakan login lagi.');
        }

        $db = \Config\Database::connect();

        // riwayat saya
        $hist = $db->query("
            SELECT id, tanggal_pengajuan, tanggal_efektif, status, dokumen_path
            FROM pengunduran_diri
            WHERE karyawan_id = ?
            ORDER BY id DESC
        ", [$karyawanId])->getResultArray();

        return view('employee/resign', [
            'title' => 'Pengunduran Diri',
            'history' => $hist,
        ]);
    }

    public function resignSave()
    {
        helper(['form']);

        $karyawanId = $this->currentKaryawanId();
        if (!$karyawanId) {
            return redirect()->to('/login')->with('error', 'Sesi habis. Silakan login lagi.');
        }

        $rules = [
            'tanggal_efektif' => 'required|valid_date[Y-m-d]',
            'alasan'          => 'permit_empty|string|max_length[1000]',
            'dokumen'         => 'uploaded[dokumen]|max_size[dokumen,4096]|ext_in[dokumen,pdf,jpg,jpeg,png]'
        ];

        // dokumen opsional: jika tidak diupload, hapus aturan uploaded
        if (empty($_FILES['dokumen']['name'])) {
            unset($rules['dokumen']);
        }

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Input tidak valid: ' . implode(', ', $this->validator->getErrors()));
        }

        $tanggalEfektif = $this->request->getPost('tanggal_efektif');
        $alasan         = $this->request->getPost('alasan');

        // Validasi tanggal efektif >= hari ini
        if (strtotime($tanggalEfektif) < strtotime(date('Y-m-d'))) {
            return redirect()->back()->withInput()->with('error', 'Tanggal efektif tidak boleh sebelum hari ini.');
        }

        $db = \Config\Database::connect();

        // Larang pengajuan ganda yang "menunggu"
        $sudah = (int) $db->query("
            SELECT COUNT(*) c FROM pengunduran_diri
            WHERE karyawan_id=? AND status='menunggu'
        ", [$karyawanId])->getRow('c');
        if ($sudah > 0) {
            return redirect()->back()->with('error', 'Masih ada pengajuan resign yang MENUNGGU. Tunggu keputusan dulu.');
        }

        // Upload dokumen (opsional)
        $path = null;
        $file = $this->request->getFile('dokumen');
        if ($file && $file->isValid()) {
            $dir = WRITEPATH . 'uploads/exits';
            if (!is_dir($dir)) @mkdir($dir, 0777, true);
            $newName = 'resign_' . $karyawanId . '_' . time() . '.' . $file->getExtension();
            $file->move($dir, $newName);
            $path = 'uploads/exits/' . $newName; // simpan RELATIF dari WRITEPATH
        }

        // Insert
        $db->table('pengunduran_diri')->insert([
            'karyawan_id'       => $karyawanId,
            'tanggal_pengajuan' => date('Y-m-d'),
            'tanggal_efektif'   => $tanggalEfektif,
            'alasan'            => $alasan ?: null,
            'dokumen_path'      => $path,
            'status'            => 'menunggu',
            'created_at'        => date('Y-m-d H:i:s'),
        ]);

        return redirect()->to('/employee/resign')->with('success', 'Pengajuan resign terkirim. Menunggu keputusan atasan.');
    }

    // ===================== PENSIUN =====================

    public function pensiunForm()
    {
        $karyawanId = $this->currentKaryawanId();
        if (!$karyawanId) {
            return redirect()->to('/login')->with('error', 'Sesi habis. Silakan login lagi.');
        }

        $db = \Config\Database::connect();
        $hist = $db->query("
            SELECT id, DATE(created_at) AS tanggal_pengajuan, tanggal_efektif, status, dokumen_path
            FROM pensiun
            WHERE karyawan_id = ?
            ORDER BY id DESC
        ", [$karyawanId])->getResultArray();

        return view('employee/pensiun', [
            'title'   => 'Pensiun',
            'history' => $hist,
        ]);
    }

    public function pensiunSave()
    {
        helper(['form']);

        $karyawanId = $this->currentKaryawanId();
        if (!$karyawanId) {
            return redirect()->to('/login')->with('error', 'Sesi habis. Silakan login lagi.');
        }

        $rules = [
            'tanggal_efektif' => 'required|valid_date[Y-m-d]',
            'dokumen'         => 'uploaded[dokumen]|max_size[dokumen,4096]|ext_in[dokumen,pdf,jpg,jpeg,png]'
        ];
        if (empty($_FILES['dokumen']['name'])) {
            unset($rules['dokumen']);
        }

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Input tidak valid: ' . implode(', ', $this->validator->getErrors()));
        }

        $tanggalEfektif = $this->request->getPost('tanggal_efektif');
        if (strtotime($tanggalEfektif) < strtotime(date('Y-m-d'))) {
            return redirect()->back()->withInput()->with('error', 'Tanggal efektif tidak boleh sebelum hari ini.');
        }

        $db = \Config\Database::connect();
        $sudah = (int) $db->query("
            SELECT COUNT(*) c FROM pensiun
            WHERE karyawan_id=? AND status='menunggu'
        ", [$karyawanId])->getRow('c');
        if ($sudah > 0) {
            return redirect()->back()->with('error', 'Masih ada pengajuan pensiun yang MENUNGGU. Tunggu keputusan dulu.');
        }

        $path = null;
        $file = $this->request->getFile('dokumen');
        if ($file && $file->isValid()) {
            $dir = WRITEPATH . 'uploads/exits';
            if (!is_dir($dir)) @mkdir($dir, 0777, true);
            $newName = 'pensiun_' . $karyawanId . '_' . time() . '.' . $file->getExtension();
            $file->move($dir, $newName);
            $path = 'uploads/exits/' . $newName;
        }

        $db->table('pensiun')->insert([
            'karyawan_id'   => $karyawanId,
            'tanggal_efektif' => $tanggalEfektif,
            'dokumen_path'  => $path,
            'status'        => 'menunggu',
            'created_at'    => date('Y-m-d H:i:s'),
        ]);

        return redirect()->to('/employee/pensiun')->with('success', 'Pengajuan pensiun terkirim. Menunggu keputusan atasan.');
    }

    public function index()
    {
        $db   = \Config\Database::connect();
        $uid  = (int) session('id');              // sesuaikan dengan nama session id user-mu
        if (!$uid) return redirect()->to('/login');

        // Profil + data karyawan (join users)
        $me = $db->query("
            SELECT u.id AS user_id, u.name, u.email, u.is_active,
                   k.id AS karyawan_id, k.nip, k.nik, k.jabatan, k.no_telp,
                   k.tanggal_masuk, k.tanggal_keluar, k.status
            FROM karyawan k JOIN users u ON u.id = k.user_id
            WHERE u.id = ?
            LIMIT 1
        ", [$uid])->getRowArray();

        if (!$me) return redirect()->to('/login'); // belum terdaftar sebagai karyawan

        $today        = date('Y-m-d');
        $jamMasuk     = '09:00:00';
        $jamPulang    = '17:00:00';

        // Absensi hari ini
        $abs = $db->query("
            SELECT waktu_masuk, waktu_keluar, catatan
            FROM absensi WHERE karyawan_id = ? AND tanggal = ?
            LIMIT 1
        ", [$me['karyawan_id'], $today])->getRowArray();

        $statusHari = [
            'cekIn'       => $abs['waktu_masuk'] ?? null,
            'cekOut'      => $abs['waktu_keluar'] ?? null,
            'hadir'       => !empty($abs['waktu_masuk']),
            'terlambat'   => !empty($abs['waktu_masuk']) && $abs['waktu_masuk'] > $jamMasuk,
            'pulangCepat' => !empty($abs['waktu_keluar']) && $abs['waktu_keluar'] < $jamPulang,
            'catatan'     => $abs['catatan'] ?? null,
        ];

        // Pending milik karyawan
        $pendingIzin = (int) $db->query("SELECT COUNT(*) c FROM izin WHERE karyawan_id=? AND status='menunggu'", [$me['karyawan_id']])->getRow('c');
        $pendingCuti = (int) $db->query("SELECT COUNT(*) c FROM cuti WHERE karyawan_id=? AND status='menunggu'", [$me['karyawan_id']])->getRow('c');

        // Jadwal izin/cuti disetujui (yang akan datang)
        $upIzin = $db->query("
            SELECT 'Izin' jenis, tgl_mulai, tgl_selesai, status
            FROM izin
            WHERE karyawan_id=? AND status='disetujui' AND tgl_mulai>=?
            ORDER BY tgl_mulai ASC LIMIT 5
        ", [$me['karyawan_id'], $today])->getResultArray();

        $upCuti = $db->query("
            SELECT 'Cuti' jenis, tgl_mulai, tgl_selesai, status
            FROM cuti
            WHERE karyawan_id=? AND status='disetujui' AND tgl_mulai>=?
            ORDER BY tgl_mulai ASC LIMIT 5
        ", [$me['karyawan_id'], $today])->getResultArray();

        $upcoming = array_merge($upIzin, $upCuti);

        // Ringkas 30 hari terakhir
        $from = date('Y-m-d', strtotime('-29 days'));
        $hist = $db->query("
            SELECT
              COUNT(DISTINCT CASE WHEN waktu_masuk IS NOT NULL THEN tanggal END) AS hadir,
              SUM(waktu_masuk  > ?) AS terlambat,
              SUM(waktu_keluar IS NOT NULL AND waktu_keluar < ?) AS pulang_cepat
            FROM absensi
            WHERE karyawan_id=? AND tanggal BETWEEN ? AND ?
        ", [$jamMasuk, $jamPulang, $me['karyawan_id'], $from, $today])->getRowArray();

        return view('dashboard/employee', [
            'me'          => $me,
            'today'       => $today,
            'statusHari'  => $statusHari,
            'pendingIzin' => $pendingIzin,
            'pendingCuti' => $pendingCuti,
            'upcoming'    => $upcoming,
            'hist'        => $hist,
        ]);
    }

    public function leavesIndex()
    {
        $kid = $this->currentKaryawanId();
        if (!$kid) return redirect()->to('/login');

        $db = \Config\Database::connect();

        // Gabungkan izin & cuti untuk riwayat
        $rows = $db->query("
            SELECT 'izin' AS tipe, i.id, i.tgl_pengajuan, i.tgl_mulai, i.tgl_selesai, i.jenis, i.status
            FROM izin i WHERE i.karyawan_id = ?
            UNION ALL
            SELECT 'cuti' AS tipe, c.id, c.tgl_pengajuan, c.tgl_mulai, c.tgl_selesai, c.jenis, c.status
            FROM cuti c WHERE c.karyawan_id = ?
            ORDER BY tgl_pengajuan DESC, id DESC
        ", [$kid, $kid])->getResultArray();

        return view('employee/leaves_index', [
            'title' => 'Pengajuan Izin & Cuti',
            'rows'  => $rows,
        ]);
    }

    // ---------- Izin ----------
    public function izinCreate()
    {
        return view('employee/izin_form', ['title' => 'Ajukan Izin']);
    }

    public function izinStore()
    {
        $kid = $this->currentKaryawanId();
        if (!$kid) return redirect()->to('/login');

        $rules = [
            'tgl_mulai'   => 'required|valid_date[Y-m-d]',
            'tgl_selesai' => 'required|valid_date[Y-m-d]', // hapus check_end_after
            'jenis'       => 'required|in_list[sakit,pribadi,izin_lain]',
            'keterangan'  => 'permit_empty|max_length[255]',
            'lampiran'    => 'permit_empty|uploaded[lampiran]|max_size[lampiran,2048]|ext_in[lampiran,png,jpg,jpeg,pdf]',
        ];
        if (!$this->validate($rules)) {
            return redirect()->back()->with('errors', $this->validator->getErrors())->withInput();
        }

        $tglMulai   = $this->request->getPost('tgl_mulai');
        $tglSelesai = $this->request->getPost('tgl_selesai');

        // Tambahkan cek manual: end >= start
        if (strtotime($tglSelesai) < strtotime($tglMulai)) {
            return redirect()->back()->with('error', 'Tanggal selesai harus >= tanggal mulai.')->withInput();
        }

        // Cegah overlap tanggal dengan izin/cuti lain (menunggu/disetujui)
        $db = \Config\Database::connect();
        $overlap = $db->query("
            SELECT COUNT(*) c FROM (
              SELECT tgl_mulai,tgl_selesai FROM izin WHERE karyawan_id=? AND status IN('menunggu','disetujui')
              UNION ALL
              SELECT tgl_mulai,tgl_selesai FROM cuti WHERE karyawan_id=? AND status IN('menunggu','disetujui')
            ) x
            WHERE NOT (x.tgl_selesai < ? OR x.tgl_mulai > ?)
        ", [$kid, $kid, $tglMulai, $tglSelesai])->getRow('c');

        if ($overlap > 0) {
            return redirect()->back()->with('error', 'Range tanggal bertabrakan dengan pengajuan lain.')->withInput();
        }

        // Upload lampiran (opsional)
        $lampiranPath = null;
        $file = $this->request->getFile('lampiran');
        if ($file && $file->isValid()) {
            $newName = 'izin_' . time() . '_' . $file->getRandomName();
            $file->move(WRITEPATH . 'uploads/izin', $newName);
            $lampiranPath = 'writable/uploads/izin/' . $newName; // agar bisa diakses
        }

        $m = new IzinModel();
        $m->insert([
            'karyawan_id'  => $kid,
            'pemilik_id'   => null,
            'tgl_pengajuan' => date('Y-m-d'),
            'tgl_mulai'    => $tglMulai,
            'tgl_selesai'  => $tglSelesai,
            'jenis'        => $this->request->getPost('jenis'),
            'keterangan'   => $this->request->getPost('keterangan'),
            'lampiran_path' => $lampiranPath,
            'status'       => 'menunggu',
            'created_at'   => date('Y-m-d H:i:s'),
        ]);

        return redirect()->to('/employee/leaves')->with('success', 'Izin berhasil diajukan.');
    }

    public function izinShow(int $id)
    {
        $kid = $this->currentKaryawanId();
        $m = new IzinModel();
        $row = $m->where('karyawan_id', $kid)->find($id);
        if (!$row) return redirect()->to('/employee/leaves')->with('error', 'Data tidak ditemukan.');
        return view('employee/izin_show', ['title' => 'Detail Izin', 'row' => $row]);
    }

    // ---------- Cuti ----------
    public function cutiCreate()
    {
        return view('employee/cuti_form', ['title' => 'Ajukan Cuti']);
    }

    public function cutiStore()
    {
        $kid = $this->currentKaryawanId();
        if (!$kid) return redirect()->to('/login');

        $rules = [
            'tgl_mulai'   => 'required|valid_date[Y-m-d]',
            'tgl_selesai' => 'required|valid_date[Y-m-d]', // hapus check_end_after
            'jenis'       => 'required|in_list[tahunan,melahirkan,besar,cuti_lain]',
            'alasan'      => 'required|max_length[255]',
        ];
        if (!$this->validate($rules)) {
            return redirect()->back()->with('errors', $this->validator->getErrors())->withInput();
        }

        $tglMulai   = $this->request->getPost('tgl_mulai');
        $tglSelesai = $this->request->getPost('tgl_selesai');

        if (strtotime($tglSelesai) < strtotime($tglMulai)) {
            return redirect()->back()->with('error', 'Tanggal selesai harus >= tanggal mulai.')->withInput();
        }

        // Cegah overlap tanggal
        $db = \Config\Database::connect();
        $overlap = $db->query("
            SELECT COUNT(*) c FROM (
              SELECT tgl_mulai,tgl_selesai FROM cuti WHERE karyawan_id=? AND status IN('menunggu','disetujui')
              UNION ALL
              SELECT tgl_mulai,tgl_selesai FROM izin WHERE karyawan_id=? AND status IN('menunggu','disetujui')
            ) x
            WHERE NOT (x.tgl_selesai < ? OR x.tgl_mulai > ?)
        ", [$kid, $kid, $tglMulai, $tglSelesai])->getRow('c');

        if ($overlap > 0) {
            return redirect()->back()->with('error', 'Range tanggal bertabrakan dengan pengajuan lain.')->withInput();
        }

        $m = new CutiModel();
        $m->insert([
            'karyawan_id'  => $kid,
            'pemilik_id'   => null,
            'tgl_pengajuan' => date('Y-m-d'),
            'tgl_mulai'    => $tglMulai,
            'tgl_selesai'  => $tglSelesai,
            'jenis'        => $this->request->getPost('jenis'),
            'alasan'       => $this->request->getPost('alasan'),
            'status'       => 'menunggu',
            'created_at'   => date('Y-m-d H:i:s'),
        ]);

        return redirect()->to('/employee/leaves')->with('success', 'Cuti berhasil diajukan.');
    }

    public function cutiShow(int $id)
    {
        $kid = $this->currentKaryawanId();
        $m = new CutiModel();
        $row = $m->where('karyawan_id', $kid)->find($id);
        if (!$row) return redirect()->to('/employee/leaves')->with('error', 'Data tidak ditemukan.');
        return view('employee/cuti_show', ['title' => 'Detail Cuti', 'row' => $row]);
    }

    public function attendanceIndex()
    {
        $kid = $this->currentKaryawanId();
        if (!$kid) return redirect()->to('/login');

        $m = new AbsensiModel();
        $today = date('Y-m-d');
        $todayRow = $m->todayFor($kid, $today);

        // Riwayat: 30 hari terakhir
        $db = \Config\Database::connect();
        $rows = $db->query("
        SELECT a.*,
               CASE
                 WHEN a.waktu_masuk IS NOT NULL AND a.waktu_keluar IS NOT NULL
                 THEN SEC_TO_TIME(TIMESTAMPDIFF(SECOND,
                         CONCAT(a.tanggal,' ',a.waktu_masuk),
                         CONCAT(a.tanggal,' ',a.waktu_keluar)))
                 ELSE NULL
               END AS durasi
        FROM absensi a
        WHERE a.karyawan_id = ?
        ORDER BY a.tanggal DESC
        LIMIT 30
    ", [$kid])->getResultArray();

        return view('employee/attendance_index', [
            'title'    => 'Absensi',
            'today'    => $today,
            'todayRow' => $todayRow,
            'rows'     => $rows,
        ]);
    }

    public function attendanceCheckin()
    {
        $kid = $this->currentKaryawanId();
        if (!$kid) return redirect()->to('/login');

        $m = new AbsensiModel();
        $today = date('Y-m-d');

        // Sudah absen hari ini?
        if ($m->todayFor($kid, $today)) {
            return redirect()->to('/employee/attendance')->with('error', 'Kamu sudah melakukan absensi hari ini.');
        }

        $m->insert([
            'karyawan_id' => $kid,
            'tanggal'     => $today,
            'waktu_masuk' => date('H:i:s'),
            'catatan'     => trim((string)$this->request->getPost('catatan'))
        ]);

        return redirect()->to('/employee/attendance')->with('success', 'Berhasil absen masuk.');
    }

    public function attendanceCheckout()
    {
        $kid = $this->currentKaryawanId();
        if (!$kid) return redirect()->to('/login');

        $m = new AbsensiModel();
        $today = date('Y-m-d');
        $row = $m->todayFor($kid, $today);

        if (!$row) {
            return redirect()->to('/employee/attendance')->with('error', 'Belum absen masuk.');
        }
        if (!empty($row['waktu_keluar'])) {
            return redirect()->to('/employee/attendance')->with('error', 'Sudah absen pulang hari ini.');
        }

        $m->update($row['id'], [
            'waktu_keluar' => date('H:i:s'),
            // opsional: tambahkan catatan checkout kalau mau
        ]);

        return redirect()->to('/employee/attendance')->with('success', 'Berhasil absen pulang.');
    }
}
