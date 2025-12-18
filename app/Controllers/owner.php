<?php

namespace App\Controllers;

use App\Models\EmployeeModel;
use App\Models\IzinModel;
use App\Models\CutiModel;
use CodeIgniter\Database\Exceptions\DatabaseException;
use Dompdf\Dompdf;
use Dompdf\Options;
use App\Models\CalonKaryawanModel;
use App\Models\KaryawanModel;   // diasumsikan sudah ada
use App\Models\UserModel;       // model user untuk login



class Owner extends BaseController
{
    protected $calonModel;
    protected $karyawanModel;
    protected $userModel;

    private function currentPemilikId(): ?int
    {
        // cari id pemilik berdasarkan users.id yang login
        $uid = (int) session('user_id');
        if (!$uid) return null;
        $db = \Config\Database::connect();
        $row = $db->table('pemilik')->where('user_id', $uid)->get()->getRowArray();
        return $row['id'] ?? null;
    }

    public function calonKaryawanIndex()
    {
        $model = new CalonKaryawanModel();
        $data = [
            'title' => 'Calon Karyawan',
            'rows'  => $model->orderBy('created_at', 'DESC')->findAll(),
        ];

        return view('owner/calon_karyawan_index', $data);
    }

    public function index()
    {
        $db = \Config\Database::connect();

        // --- Jam kerja baku
        $jamMasukBaku  = '09:00:00';
        $jamPulangBaku = '17:00:00';

        // --- Ringkasan Karyawan
        $total = (int) $db->query('SELECT COUNT(*) c FROM karyawan')->getRow('c');
        $aktif = (int) $db->query("
        SELECT COUNT(*) c
        FROM karyawan k JOIN users u ON u.id=k.user_id
        WHERE u.is_active=1
    ")->getRow('c');
        $nonaktif = (int) $db->query("
        SELECT COUNT(*) c
        FROM karyawan k JOIN users u ON u.id=k.user_id
        WHERE u.is_active=0
    ")->getRow('c');

        // --- Pending izin/cuti
        $pendingIzin = (int) $db->query("SELECT COUNT(*) c FROM izin WHERE status='menunggu'")->getRow('c');
        $pendingCuti = (int) $db->query("SELECT COUNT(*) c FROM cuti WHERE status='menunggu'")->getRow('c');

        // --- Status harian (hari ini)
        $today = date('Y-m-d');

        $hadirToday = (int) $db->query("
        SELECT COUNT(DISTINCT a.karyawan_id) c
        FROM absensi a
        WHERE a.tanggal = ? AND a.waktu_masuk IS NOT NULL
    ", [$today])->getRow('c');

        $terlambatToday = (int) $db->query("
        SELECT COUNT(*) c
        FROM absensi a
        WHERE a.tanggal = ? AND a.waktu_masuk IS NOT NULL AND a.waktu_masuk > ?
    ", [$today, $jamMasukBaku])->getRow('c');

        $pulangCepatToday = (int) $db->query("
        SELECT COUNT(*) c
        FROM absensi a
        WHERE a.tanggal = ? AND a.waktu_keluar IS NOT NULL AND a.waktu_keluar < ?
    ", [$today, $jamPulangBaku])->getRow('c');

        // Cuti/Izin yang overlap hari ini (disetujui)
        $onCutiToday = (int) $db->query("
        SELECT COUNT(DISTINCT karyawan_id) c
        FROM cuti
        WHERE status='disetujui' AND tgl_mulai <= ? AND tgl_selesai >= ?
    ", [$today, $today])->getRow('c');

        $onIzinToday = (int) $db->query("
        SELECT COUNT(DISTINCT karyawan_id) c
        FROM izin
        WHERE status='disetujui' AND tgl_mulai <= ? AND tgl_selesai >= ?
    ", [$today, $today])->getRow('c');

        $onLeaveToday = $onCutiToday + $onIzinToday;
        $alphaToday   = max(0, $aktif - ($hadirToday + $onLeaveToday)); // kira-kira yang tidak hadir & bukan cuti/izin

        // --- Check-in terbaru (Top 5)
        $latestCheckins = $db->query("
        SELECT u.name AS nama, a.waktu_masuk
        FROM absensi a
        JOIN karyawan k ON k.id=a.karyawan_id
        JOIN users u ON u.id=k.user_id
        WHERE a.tanggal=? AND a.waktu_masuk IS NOT NULL
        ORDER BY a.waktu_masuk DESC
        LIMIT 5
    ", [$today])->getResultArray();

        // --- Grafik tren 14 hari
        $days     = 14;
        $startDay = date('Y-m-d', strtotime('-' . ($days - 1) . ' days', strtotime($today)));
        $trendRows = $db->query("
        SELECT a.tanggal,
               COUNT(DISTINCT a.karyawan_id)                              AS hadir,
               SUM(a.waktu_masuk  > ?)                                    AS terlambat,
               SUM(a.waktu_keluar IS NOT NULL AND a.waktu_keluar < ?)     AS pulang_cepat
        FROM absensi a
        WHERE a.tanggal BETWEEN ? AND ?
        GROUP BY a.tanggal
        ORDER BY a.tanggal ASC
    ", [$jamMasukBaku, $jamPulangBaku, $startDay, $today])->getResultArray();

        $map = [];
        foreach ($trendRows as $r) {
            $map[$r['tanggal']] = [
                'hadir' => (int)$r['hadir'],
                'terlambat' => (int)$r['terlambat'],
                'pulang_cepat' => (int)$r['pulang_cepat'],
            ];
        }
        $labels = $d1 = $d2 = $d3 = [];
        for ($ts = strtotime($startDay); $ts <= strtotime($today); $ts = strtotime('+1 day', $ts)) {
            $d = date('Y-m-d', $ts);
            $labels[] = date('d M', $ts);
            $d1[] = $map[$d]['hadir'] ?? 0;
            $d2[] = $map[$d]['terlambat'] ?? 0;
            $d3[] = $map[$d]['pulang_cepat'] ?? 0;
        }
        $trend = [
            'labels' => $labels,
            'hadir' => $d1,
            'terlambat' => $d2,
            'pulangCepat' => $d3,
            'range_caption' => $startDay . ' s/d ' . $today
        ];

        // --- Rekap bulanan URL
        $y = date('Y');
        $m = date('n');
        $rekapAbsensiUrl = site_url("/owner/attendance/rekap?year={$y}&month={$m}");

        // --- Ringkasan RESIGN & PENSIUN (Total & Bulan ini)
        $resignTotal  = (int) $db->query("SELECT COUNT(*) c FROM karyawan WHERE status='resign'")->getRow('c');
        $pensiunTotal = (int) $db->query("SELECT COUNT(*) c FROM karyawan WHERE status='pensiun'")->getRow('c');

        $bulanIni = date('Y-m');
        $resignBulanIni  = (int) $db->query("
        SELECT COUNT(*) c
        FROM karyawan
        WHERE status='resign' AND DATE_FORMAT(tanggal_keluar,'%Y-%m')=?
    ", [$bulanIni])->getRow('c');

        $pensiunBulanIni = (int) $db->query("
        SELECT COUNT(*) c
        FROM karyawan
        WHERE status='pensiun' AND DATE_FORMAT(tanggal_keluar,'%Y-%m')=?
    ", [$bulanIni])->getRow('c');

        // URL daftar sesuai status
        $resignListUrl  = site_url('/owner/employees/status/resign');
        $pensiunListUrl = site_url('/owner/employees/status/pensiun');

        // --- Kirim ke view
        return view('dashboard/owner', [
            'title'            => 'Dashboard Pemilik',
            'total'            => $total,
            'aktif'            => $aktif,
            'nonaktif'         => $nonaktif,
            'pendingIzin'      => $pendingIzin,
            'pendingCuti'      => $pendingCuti,
            'today'            => $today,
            'hadirToday'       => $hadirToday,
            'terlambatToday'   => $terlambatToday,
            'pulangCepatToday' => $pulangCepatToday,
            'onLeaveToday'     => $onLeaveToday,
            'alphaToday'       => $alphaToday,
            'latestCheckins'   => $latestCheckins,
            'trend'            => $trend,
            'rekapAbsensiUrl'  => $rekapAbsensiUrl,
            // Resign & Pensiun
            'resignTotal'      => $resignTotal,
            'pensiunTotal'     => $pensiunTotal,
            'resignBulanIni'   => $resignBulanIni,
            'pensiunBulanIni'  => $pensiunBulanIni,
            'resignListUrl'    => $resignListUrl,
            'pensiunListUrl'   => $pensiunListUrl,
            // Link cepat lain (opsional)
            'izinUrl'          => site_url('/owner/izin?status=menunggu'),
            'cutiUrl'          => site_url('/owner/cuti?status=menunggu'),
        ]);
    }


    public function izinIndex()
    {
        $m       = new IzinModel();
        $q       = trim((string)$this->request->getGet('q'));
        $status  = trim((string)$this->request->getGet('status')); // '', menunggu, disetujui, ditolak
        $perPage = (int)($this->request->getGet('pp') ?? 10);

        $list = $m->getPaged($q, $status, $perPage);

        return view('owner/izin_index', [
            'title'   => 'Persetujuan Izin',
            'list'    => $list,
            'pager'   => $m->pager,
            'q'       => $q,
            'status'  => $status,
            'perPage' => $perPage,
        ]);
    }

    public function izinShow(int $id)
    {
        $m = new IzinModel();
        $row = $m->select('izin.*, users.name, users.email')
            ->join('karyawan', 'karyawan.id = izin.karyawan_id', 'left')
            ->join('users',    'users.id = karyawan.user_id',   'left')
            ->find($id);

        if (!$row) return redirect()->to('/owner/izin')->with('error', 'Pengajuan izin tidak ditemukan.');
        return view('owner/izin_show', ['title' => 'Detail Izin', 'row' => $row]);
    }

    public function izinApprove(int $id)
    {
        $m = new IzinModel();
        $row = $m->find($id);
        if (!$row) return redirect()->to('/owner/izin')->with('error', 'Pengajuan tidak ditemukan.');

        $pemilikId = $this->currentPemilikId(); // bisa null, tidak masalah
        $m->update($id, ['status' => 'disetujui', 'pemilik_id' => $pemilikId]);
        return redirect()->to("/owner/izin/{$id}")->with('success', 'Izin disetujui.');
    }

    public function izinReject(int $id)
    {
        $m = new IzinModel();
        $row = $m->find($id);
        if (!$row) return redirect()->to('/owner/izin')->with('error', 'Pengajuan tidak ditemukan.');

        $pemilikId = $this->currentPemilikId();
        $m->update($id, ['status' => 'ditolak', 'pemilik_id' => $pemilikId]);
        return redirect()->to("/owner/izin/{$id}")->with('success', 'Izin ditolak.');
    }

    public function cutiIndex()
    {
        $m       = new CutiModel();
        $q       = trim((string)$this->request->getGet('q'));
        $status  = trim((string)$this->request->getGet('status'));
        $perPage = (int)($this->request->getGet('pp') ?? 10);

        $list = $m->getPaged($q, $status, $perPage);

        return view('owner/cuti_index', [
            'title'   => 'Persetujuan Cuti',
            'list'    => $list,
            'pager'   => $m->pager,
            'q'       => $q,
            'status'  => $status,
            'perPage' => $perPage,
        ]);
    }

    public function cutiShow(int $id)
    {
        $m = new CutiModel();
        $row = $m->select('cuti.*, users.name, users.email')
            ->join('karyawan', 'karyawan.id = cuti.karyawan_id', 'left')
            ->join('users',    'users.id = karyawan.user_id',   'left')
            ->find($id);

        if (!$row) return redirect()->to('/owner/cuti')->with('error', 'Pengajuan cuti tidak ditemukan.');
        return view('owner/cuti_show', ['title' => 'Detail Cuti', 'row' => $row]);
    }

    public function cutiApprove(int $id)
    {
        $m = new CutiModel();
        if (!$m->find($id)) return redirect()->to('/owner/cuti')->with('error', 'Pengajuan tidak ditemukan.');

        $pemilikId = $this->currentPemilikId();
        $m->update($id, ['status' => 'disetujui', 'pemilik_id' => $pemilikId]);
        return redirect()->to("/owner/cuti/{$id}")->with('success', 'Cuti disetujui.');
    }

    public function cutiReject(int $id)
    {
        $m = new CutiModel();
        if (!$m->find($id)) return redirect()->to('/owner/cuti')->with('error', 'Pengajuan tidak ditemukan.');

        $pemilikId = $this->currentPemilikId();
        $m->update($id, ['status' => 'ditolak', 'pemilik_id' => $pemilikId]);
        return redirect()->to("/owner/cuti/{$id}")->with('success', 'Cuti ditolak.');
    }

    public function reports()
    {
        $mI = new IzinModel();
        $mC = new CutiModel();

        $izin = [
            'menunggu'  => $mI->countByStatus('menunggu'),
            'disetujui' => $mI->countByStatus('disetujui'),
            'ditolak'   => $mI->countByStatus('ditolak'),
        ];
        $cuti = [
            'menunggu'  => $mC->countByStatus('menunggu'),
            'disetujui' => $mC->countByStatus('disetujui'),
            'ditolak'   => $mC->countByStatus('ditolak'),
        ];

        return view('owner/reports_index', [
            'title' => 'Laporan Ringkas',
            'izin'  => $izin,
            'cuti'  => $cuti,
        ]);
    }

    public function reportsRekap()
    {
        $db   = \Config\Database::connect();

        // Ambil bulan/tahun dari query, default: bulan ini
        $year  = (int) ($this->request->getGet('year')  ?? date('Y'));
        $month = (int) ($this->request->getGet('month') ?? date('n'));

        // Range tanggal [start, end]
        $start = sprintf('%04d-%02d-01', $year, $month);
        $end   = date('Y-m-d', strtotime("$start +1 month -1 day"));

        // Kartu ringkas izin (berdasarkan tgl_mulai di periode tsb)
        $izinMenunggu  = (int) $db->query(
            "SELECT COUNT(*) c FROM izin WHERE status='menunggu' AND tgl_mulai BETWEEN ? AND ?",
            [$start, $end]
        )->getRow('c');
        $izinSetuju    = (int) $db->query(
            "SELECT COUNT(*) c FROM izin WHERE status='disetujui' AND tgl_mulai BETWEEN ? AND ?",
            [$start, $end]
        )->getRow('c');
        $izinTolak     = (int) $db->query(
            "SELECT COUNT(*) c FROM izin WHERE status='ditolak' AND tgl_mulai BETWEEN ? AND ?",
            [$start, $end]
        )->getRow('c');

        // Kartu ringkas cuti
        $cutiMenunggu  = (int) $db->query(
            "SELECT COUNT(*) c FROM cuti WHERE status='menunggu' AND tgl_mulai BETWEEN ? AND ?",
            [$start, $end]
        )->getRow('c');
        $cutiSetuju    = (int) $db->query(
            "SELECT COUNT(*) c FROM cuti WHERE status='disetujui' AND tgl_mulai BETWEEN ? AND ?",
            [$start, $end]
        )->getRow('c');
        $cutiTolak     = (int) $db->query(
            "SELECT COUNT(*) c FROM cuti WHERE status='ditolak' AND tgl_mulai BETWEEN ? AND ?",
            [$start, $end]
        )->getRow('c');

        // Tabel per karyawan (join users)
        $rows = $db->query("
        SELECT
            u.id        AS user_id,
            u.name      AS nama,
            u.email,
            SUM(COALESCE(iz_jml,0)) AS izin_total,
            SUM(COALESCE(ct_jml,0)) AS cuti_total
        FROM karyawan k
        JOIN users u ON u.id = k.user_id
        LEFT JOIN (
            SELECT i.karyawan_id, COUNT(*) iz_jml
            FROM izin i
            WHERE i.tgl_mulai BETWEEN ? AND ?
            GROUP BY i.karyawan_id
        ) iz ON iz.karyawan_id = k.id
        LEFT JOIN (
            SELECT c.karyawan_id, COUNT(*) ct_jml
            FROM cuti c
            WHERE c.tgl_mulai BETWEEN ? AND ?
            GROUP BY c.karyawan_id
        ) ct ON ct.karyawan_id = k.id
        GROUP BY u.id, u.name, u.email
        ORDER BY u.name ASC
    ", [$start, $end, $start, $end])->getResultArray();

        return view('owner/reports_rekap', [
            'title'        => 'Rekap Bulanan',
            'year'         => $year,
            'month'        => $month,
            'start'        => $start,
            'end'          => $end,
            'izin'         => ['menunggu' => $izinMenunggu, 'setuju' => $izinSetuju, 'tolak' => $izinTolak],
            'cuti'         => ['menunggu' => $cutiMenunggu, 'setuju' => $cutiSetuju, 'tolak' => $cutiTolak],
            'rows'         => $rows,
        ]);
    }

    public function reportsRekapPdf()
    {
        $db    = \Config\Database::connect();
        $year  = (int) ($this->request->getGet('year')  ?? date('Y'));
        $month = (int) ($this->request->getGet('month') ?? date('n'));

        $start = sprintf('%04d-%02d-01', $year, $month);
        $end   = date('Y-m-d', strtotime("$start +1 month -1 day"));

        // Ringkas izin
        $izinMenunggu = (int) $db->query("SELECT COUNT(*) c FROM izin WHERE status='menunggu' AND tgl_mulai BETWEEN ? AND ?", [$start, $end])->getRow('c');
        $izinSetuju   = (int) $db->query("SELECT COUNT(*) c FROM izin WHERE status='disetujui' AND tgl_mulai BETWEEN ? AND ?", [$start, $end])->getRow('c');
        $izinTolak    = (int) $db->query("SELECT COUNT(*) c FROM izin WHERE status='ditolak'   AND tgl_mulai BETWEEN ? AND ?", [$start, $end])->getRow('c');

        // Ringkas cuti
        $cutiMenunggu = (int) $db->query("SELECT COUNT(*) c FROM cuti WHERE status='menunggu' AND tgl_mulai BETWEEN ? AND ?", [$start, $end])->getRow('c');
        $cutiSetuju   = (int) $db->query("SELECT COUNT(*) c FROM cuti WHERE status='disetujui' AND tgl_mulai BETWEEN ? AND ?", [$start, $end])->getRow('c');
        $cutiTolak    = (int) $db->query("SELECT COUNT(*) c FROM cuti WHERE status='ditolak'   AND tgl_mulai BETWEEN ? AND ?", [$start, $end])->getRow('c');

        // Tabel per karyawan
        $rows = $db->query("
        SELECT
            u.name  AS nama,
            u.email AS email,
            COALESCE(iz.iz_setuju,0)  AS izin_setuju,
            COALESCE(iz.iz_tolak,0)   AS izin_tolak,
            COALESCE(ct.ct_setuju,0)  AS cuti_setuju,
            COALESCE(ct.ct_tolak,0)   AS cuti_tolak
        FROM karyawan k
        JOIN users u ON u.id = k.user_id
        LEFT JOIN (
            SELECT karyawan_id,
                   SUM(status='disetujui') AS iz_setuju,
                   SUM(status='ditolak')   AS iz_tolak
            FROM izin
            WHERE tgl_mulai BETWEEN ? AND ?
            GROUP BY karyawan_id
        ) iz ON iz.karyawan_id = k.id
        LEFT JOIN (
            SELECT karyawan_id,
                   SUM(status='disetujui') AS ct_setuju,
                   SUM(status='ditolak')   AS ct_tolak
            FROM cuti
            WHERE tgl_mulai BETWEEN ? AND ?
            GROUP BY karyawan_id
        ) ct ON ct.karyawan_id = k.id
        ORDER BY u.name ASC
    ", [$start, $end, $start, $end])->getResultArray();

        // Render HTML view khusus PDF
        $html = view('owner/reports_rekap_pdf', [
            'title'   => 'Rekap Izin/Cuti Bulanan',
            'year'    => $year,
            'month'   => $month,
            'start'   => $start,
            'end'     => $end,
            'izin'    => ['menunggu' => $izinMenunggu, 'setuju' => $izinSetuju, 'tolak' => $izinTolak],
            'cuti'    => ['menunggu' => $cutiMenunggu, 'setuju' => $cutiSetuju, 'tolak' => $cutiTolak],
            'rows'    => $rows,
            'owner'   => session('name') ?? 'Pemilik', // opsional
        ]);

        // Konfigurasi Dompdf
        $options = new Options();
        $options->set('isRemoteEnabled', true);     // kalau pakai logo eksternal
        $options->set('defaultFont', 'DejaVu Sans'); // aman untuk UTF-8

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html, 'UTF-8');
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $filename = 'rekap_' . $year . '-' . sprintf('%02d', $month) . '.pdf';
        return $this->response
            ->setHeader('Content-Type', 'application/pdf')
            ->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->setBody($dompdf->output());
    }

    public function reportsRekapCsv()
    {
        $db   = \Config\Database::connect();
        $year  = (int) ($this->request->getGet('year')  ?? date('Y'));
        $month = (int) ($this->request->getGet('month') ?? date('n'));
        $start = sprintf('%04d-%02d-01', $year, $month);
        $end   = date('Y-m-d', strtotime("$start +1 month -1 day"));

        $rows = $db->query("
        SELECT
            u.name  AS Nama,
            u.email AS Email,
            COALESCE(iz.iz_setuju,0)  AS Izin_Disetuju,
            COALESCE(iz.iz_tolak,0)   AS Izin_Ditolak,
            COALESCE(ct.ct_setuju,0)  AS Cuti_Disetuju,
            COALESCE(ct.ct_tolak,0)   AS Cuti_Ditolak
        FROM karyawan k
        JOIN users u ON u.id = k.user_id
        LEFT JOIN (
            SELECT karyawan_id,
                   SUM(status='disetujui') AS iz_setuju,
                   SUM(status='ditolak')   AS iz_tolak
            FROM izin
            WHERE tgl_mulai BETWEEN ? AND ?
            GROUP BY karyawan_id
        ) iz ON iz.karyawan_id = k.id
        LEFT JOIN (
            SELECT karyawan_id,
                   SUM(status='disetujui') AS ct_setuju,
                   SUM(status='ditolak')   AS ct_tolak
            FROM cuti
            WHERE tgl_mulai BETWEEN ? AND ?
            GROUP BY karyawan_id
        ) ct ON ct.karyawan_id = k.id
        ORDER BY u.name ASC
    ", [$start, $end, $start, $end])->getResultArray();

        // Output CSV
        $filename = "rekap_{$year}-" . sprintf('%02d', $month) . ".csv";
        header('Content-Type: text/csv; charset=utf-8');
        header("Content-Disposition: attachment; filename=\"$filename\"");
        $out = fopen('php://output', 'w');
        fputcsv($out, ['Nama', 'Email', 'Izin Disetujui', 'Izin Ditolak', 'Cuti Disetujui', 'Cuti Ditolak']);
        foreach ($rows as $r) {
            fputcsv($out, [$r['Nama'], $r['Email'], $r['Izin_Disetuju'], $r['Izin_Ditolak'], $r['Cuti_Disetuju'], $r['Cuti_Ditolak']]);
        }
        fclose($out);
        exit;
    }

    public function profile()
    {
        return view('owner/profile', ['title' => 'Profil Pemilik', 'user' => [
            'name'  => session('name'),
            'email' => session('email'),
            'role'  => session('role'),
        ]]);
    }

    public function passwordForm()
    {
        return view('owner/password_form', ['title' => 'Ganti Password']);
    }

    public function passwordUpdate()
    {
        $userId = (int) session('user_id');
        if (!$userId) return redirect()->to('/login');

        $old = (string) $this->request->getPost('old_password');
        $new = (string) $this->request->getPost('new_password');
        $cfm = (string) $this->request->getPost('confirm_password');

        if (strlen($new) < 6) {
            return redirect()->back()->with('error', 'Password baru minimal 6 karakter.');
        }
        if ($new !== $cfm) {
            return redirect()->back()->with('error', 'Konfirmasi password tidak cocok.');
        }

        $db = \Config\Database::connect();
        $row = $db->table('users')->where('id', $userId)->get()->getRowArray();
        if (!$row) return redirect()->to('/login');

        $stored = $row['password'];

        // verifikasi password lama: dukung hashed & legacy plaintext
        $ok = (strpos($stored, '$2y$') === 0) ? password_verify($old, $stored) : ($old === $stored);
        if (!$ok) {
            return redirect()->back()->with('error', 'Password lama salah.');
        }

        // simpan hash bcrypt
        $hash = password_hash($new, PASSWORD_BCRYPT);
        $db->table('users')->where('id', $userId)->update([
            'password'              => $hash,
            'must_change_password'  => 0,
            'password_updated_at'   => date('Y-m-d H:i:s'),
            'updated_at'            => date('Y-m-d H:i:s'),
        ]);

        return redirect()->to('/owner')->with('success', 'Password berhasil diperbarui.');
    }

    /**
     * Sekali pakai: hash semua password plaintext yang belum di-hash.
     * Jalankan dari browser: /owner/tools/hash-passwords (lalu hapus/komentari route ini).
     */
    public function hashPasswordsOnce()
    {
        $db = \Config\Database::connect();
        $users = $db->table('users')->select('id,password')->get()->getResultArray();
        $count = 0;
        foreach ($users as $u) {
            if (strpos($u['password'], '$2y$') === 0) continue; // sudah hashed
            $db->table('users')->where('id', $u['id'])->update([
                'password'             => password_hash($u['password'], PASSWORD_BCRYPT),
                'password_updated_at'  => date('Y-m-d H:i:s'),
                'updated_at'           => date('Y-m-d H:i:s'),
            ]);
            $count++;
        }
        return $this->response->setBody("Hashed {$count} akun. (Hapus route ini setelah selesai!)");
    }

    public function attendanceRekap()
    {
        $db    = \Config\Database::connect();
        $year  = (int) ($this->request->getGet('year')  ?? date('Y'));
        $month = (int) ($this->request->getGet('month') ?? date('n'));

        $start = sprintf('%04d-%02d-01', $year, $month);
        $end   = date('Y-m-d', strtotime("$start +1 month -1 day"));

        // parameter aturan
        $jamMasukBaku  = '09:00:00'; // > ini dianggap terlambat
        $jamPulangBaku = '17:00:00'; // < ini dianggap pulang cepat

        $rows = $db->query("
        SELECT
            u.id   AS user_id,
            u.name AS nama,
            COALESCE(h.hadir,0)          AS hadir,
            COALESCE(h.terlambat,0)      AS terlambat,
            COALESCE(h.pulang_cepat,0)   AS pulang_cepat,
            SEC_TO_TIME(COALESCE(h.total_detik,0)) AS durasi_total,
            SEC_TO_TIME(COALESCE(h.rata_detik,0))  AS durasi_rata,
            COALESCE(iz.izin,0)          AS izin,
            COALESCE(ct.cuti,0)          AS cuti
        FROM karyawan k
        JOIN users u ON u.id = k.user_id
        LEFT JOIN (
          SELECT
            a.karyawan_id,
            COUNT(*) AS hadir,
            SUM(a.waktu_masuk > ?) AS terlambat,
            SUM(a.waktu_keluar IS NOT NULL AND a.waktu_keluar < ?) AS pulang_cepat,
            SUM(TIMESTAMPDIFF(SECOND,
                  CONCAT(a.tanggal,' ',a.waktu_masuk),
                  CONCAT(a.tanggal,' ',COALESCE(a.waktu_keluar,a.waktu_masuk))
            )) AS total_detik,
            AVG(TIMESTAMPDIFF(SECOND,
                  CONCAT(a.tanggal,' ',a.waktu_masuk),
                  CONCAT(a.tanggal,' ',COALESCE(a.waktu_keluar,a.waktu_masuk))
            )) AS rata_detik
          FROM absensi a
          WHERE a.tanggal BETWEEN ? AND ?
          GROUP BY a.karyawan_id
        ) h ON h.karyawan_id = k.id
        LEFT JOIN (
          SELECT i.karyawan_id, COUNT(*) AS izin
          FROM izin i
          WHERE i.status='disetujui'
            AND NOT (i.tgl_selesai < ? OR i.tgl_mulai > ?)
          GROUP BY i.karyawan_id
        ) iz ON iz.karyawan_id = k.id
        LEFT JOIN (
          SELECT c.karyawan_id, COUNT(*) AS cuti
          FROM cuti c
          WHERE c.status='disetujui'
            AND NOT (c.tgl_selesai < ? OR c.tgl_mulai > ?)
          GROUP BY c.karyawan_id
        ) ct ON ct.karyawan_id = k.id
        ORDER BY u.name ASC
    ", [
            $jamMasukBaku,
            $jamPulangBaku,
            $start,
            $end,
            $start,
            $end,
            $start,
            $end,
        ])->getResultArray();

        return view('owner/attendance_rekap', [
            'title' => 'Rekap Absensi',
            'year'  => $year,
            'month' => $month,
            'start' => $start,
            'end'   => $end,
            'rows'  => $rows,
        ]);
    }

    public function attendanceRekapCsv()
    {
        $db    = \Config\Database::connect();
        $year  = (int) ($this->request->getGet('year')  ?? date('Y'));
        $month = (int) ($this->request->getGet('month') ?? date('n'));
        $start = sprintf('%04d-%02d-01', $year, $month);
        $end   = date('Y-m-d', strtotime("$start +1 month -1 day"));

        $jamMasukBaku  = '09:00:00';
        $jamPulangBaku = '17:00:00';

        $rows = $db->query("
        SELECT u.name AS Nama,
               COALESCE(h.hadir,0)        AS Hadir,
               COALESCE(h.terlambat,0)    AS Terlambat,
               COALESCE(h.pulang_cepat,0) AS Pulang_Cepat,
               SEC_TO_TIME(COALESCE(h.total_detik,0)) AS Durasi_Total,
               SEC_TO_TIME(COALESCE(h.rata_detik,0))  AS Durasi_Rata2,
               COALESCE(iz.izin,0)        AS Izin_Setuju,
               COALESCE(ct.cuti,0)        AS Cuti_Setuju
        FROM karyawan k
        JOIN users u ON u.id = k.user_id
        LEFT JOIN (
          SELECT a.karyawan_id,
                 COUNT(*) AS hadir,
                 SUM(a.waktu_masuk > ?) AS terlambat,
                 SUM(a.waktu_keluar IS NOT NULL AND a.waktu_keluar < ?) AS pulang_cepat,
                 SUM(TIMESTAMPDIFF(SECOND, CONCAT(a.tanggal,' ',a.waktu_masuk), CONCAT(a.tanggal,' ',COALESCE(a.waktu_keluar,a.waktu_masuk)))) AS total_detik,
                 AVG(TIMESTAMPDIFF(SECOND, CONCAT(a.tanggal,' ',a.waktu_masuk), CONCAT(a.tanggal,' ',COALESCE(a.waktu_keluar,a.waktu_masuk)))) AS rata_detik
          FROM absensi a
          WHERE a.tanggal BETWEEN ? AND ?
          GROUP BY a.karyawan_id
        ) h ON h.karyawan_id = k.id
        LEFT JOIN (
          SELECT karyawan_id, COUNT(*) izin
          FROM izin
          WHERE status='disetujui' AND NOT (tgl_selesai < ? OR tgl_mulai > ?)
          GROUP BY karyawan_id
        ) iz ON iz.karyawan_id = k.id
        LEFT JOIN (
          SELECT karyawan_id, COUNT(*) cuti
          FROM cuti
          WHERE status='disetujui' AND NOT (tgl_selesai < ? OR tgl_mulai > ?)
          GROUP BY karyawan_id
        ) ct ON ct.karyawan_id = k.id
        ORDER BY Nama ASC
    ", [$jamMasukBaku, $jamPulangBaku, $start, $end, $start, $end, $start, $end])->getResultArray();

        $filename = "rekap_absensi_{$year}-" . sprintf('%02d', $month) . ".csv";
        header('Content-Type: text/csv; charset=utf-8');
        header("Content-Disposition: attachment; filename=\"$filename\"");
        $out = fopen('php://output', 'w');
        fputcsv($out, array_keys($rows[0] ?? [
            'Nama',
            'Hadir',
            'Terlambat',
            'Pulang_Cepat',
            'Durasi_Total',
            'Durasi_Rata2',
            'Izin_Setuju',
            'Cuti_Setuju'
        ]));
        foreach ($rows as $r) fputcsv($out, $r);
        fclose($out);
        exit;
    }

    public function attendanceRekapPdf()
    {
        $db    = \Config\Database::connect();
        $year  = (int) ($this->request->getGet('year')  ?? date('Y'));
        $month = (int) ($this->request->getGet('month') ?? date('n'));
        $start = sprintf('%04d-%02d-01', $year, $month);
        $end   = date('Y-m-d', strtotime("$start +1 month -1 day"));

        $jamMasukBaku  = '09:00:00';
        $jamPulangBaku = '17:00:00';

        $rows = $db->query("
        SELECT u.name AS nama,
               COALESCE(h.hadir,0)        AS hadir,
               COALESCE(h.terlambat,0)    AS terlambat,
               COALESCE(h.pulang_cepat,0) AS pulang_cepat,
               SEC_TO_TIME(COALESCE(h.total_detik,0)) AS durasi_total,
               SEC_TO_TIME(COALESCE(h.rata_detik,0))  AS durasi_rata,
               COALESCE(iz.izin,0)        AS izin,
               COALESCE(ct.cuti,0)        AS cuti
        FROM karyawan k
        JOIN users u ON u.id = k.user_id
        LEFT JOIN (
          SELECT a.karyawan_id,
                 COUNT(*) AS hadir,
                 SUM(a.waktu_masuk > ?) AS terlambat,
                 SUM(a.waktu_keluar IS NOT NULL AND a.waktu_keluar < ?) AS pulang_cepat,
                 SUM(TIMESTAMPDIFF(SECOND, CONCAT(a.tanggal,' ',a.waktu_masuk), CONCAT(a.tanggal,' ',COALESCE(a.waktu_keluar,a.waktu_masuk)))) AS total_detik,
                 AVG(TIMESTAMPDIFF(SECOND, CONCAT(a.tanggal,' ',a.waktu_masuk), CONCAT(a.tanggal,' ',COALESCE(a.waktu_keluar,a.waktu_masuk)))) AS rata_detik
          FROM absensi a
          WHERE a.tanggal BETWEEN ? AND ?
          GROUP BY a.karyawan_id
        ) h ON h.karyawan_id = k.id
        LEFT JOIN (
          SELECT karyawan_id, COUNT(*) izin
          FROM izin
          WHERE status='disetujui' AND NOT (tgl_selesai < ? OR tgl_mulai > ?)
          GROUP BY karyawan_id
        ) iz ON iz.karyawan_id = k.id
        LEFT JOIN (
          SELECT karyawan_id, COUNT(*) cuti
          FROM cuti
          WHERE status='disetujui' AND NOT (tgl_selesai < ? OR tgl_mulai > ?)
          GROUP BY karyawan_id
        ) ct ON ct.karyawan_id = k.id
        ORDER BY nama ASC
    ", [$jamMasukBaku, $jamPulangBaku, $start, $end, $start, $end, $start, $end])->getResultArray();

        $html = view('owner/attendance_rekap_pdf', [
            'title' => 'Rekap Absensi',
            'year'  => $year,
            'month' => $month,
            'start' => $start,
            'end'   => $end,
            'rows'  => $rows,
        ]);

        $opt = new Options();
        $opt->set('defaultFont', 'DejaVu Sans');
        $dom = new Dompdf($opt);
        $dom->loadHtml($html, 'UTF-8');
        $dom->setPaper('A4', 'portrait');
        $dom->render();
        $filename = 'rekap_absensi_' . $year . '-' . sprintf('%02d', $month) . '.pdf';
        return $this->response
            ->setHeader('Content-Type', 'application/pdf')
            ->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->setBody($dom->output());
    }

    // LIST
    public function employees()
    {
        $db = \Config\Database::connect();

        $rows = $db->query("
        SELECT k.id, u.name, u.email, u.is_active,
               k.nip, k.nik, k.jabatan, k.no_telp, k.tanggal_masuk, k.status
        FROM karyawan k
        JOIN users u ON u.id = k.user_id
        ORDER BY u.name ASC
    ")->getResultArray();

        $summary = $db->query("
        SELECT
          COUNT(*)                           AS total,
          SUM(u.is_active = 1)               AS aktif,
          SUM(u.is_active = 0)               AS nonaktif
        FROM karyawan k
        JOIN users u ON u.id = k.user_id
    ")->getRowArray();

        return view('owner/employees/index', [
            'title'   => 'Data Karyawan',
            'rows'    => $rows,
            'summary' => $summary
        ]);
    }


    // FORM CREATE
    public function employeeCreate()
    {
        return view('owner/employees/form', [
            'title' => 'Tambah Karyawan',
            'mode'  => 'create',
            'data'  => [
                'user_id'       => null,
                'name'          => '',
                'email'         => '',
                'is_active'     => 1,
                'nip'           => '',
                'nik'           => '',
                'jabatan'       => '',
                'no_telp'       => '',
                'tanggal_masuk' => date('Y-m-d'),
                'status'        => 'aktif',
            ],
        ]);
    }

    // SIMPAN CREATE
    public function employeeStore()
    {
        $request = $this->request;

        // Validasi (CI4 Validation)
        $rules = [
            'name'          => 'required|min_length[3]',
            'email'         => 'required|valid_email|is_unique[users.email]',
            'password'      => 'required|min_length[6]',
            'nip'           => 'permit_empty|max_length[20]',
            'nik'           => 'permit_empty|exact_length[16]|numeric',
            'jabatan'       => 'permit_empty|max_length[100]',
            'no_telp'       => 'permit_empty|max_length[20]',
            'tanggal_masuk' => 'required|valid_date[Y-m-d]',
            'status'        => 'required|in_list[aktif,nonaktif,resign,pensiun]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $db = \Config\Database::connect();
        $db->transStart();

        // 1) Buat user
        $userData = [
            'name'      => $request->getPost('name'),
            'email'     => $request->getPost('email'),
            'password'  => password_hash($request->getPost('password'), PASSWORD_BCRYPT),
            'role'      => 'karyawan',
            'is_active' => (int) ($request->getPost('is_active') ?? 1),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];
        $db->table('users')->insert($userData);
        $user_id = $db->insertID();

        // 2) Buat karyawan
        $kData = [
            'user_id'       => $user_id,
            'pemilik_id'    => null, // isi kalau pakai FK pemilik
            'nip'           => $request->getPost('nip') ?: null,
            'nik'           => $request->getPost('nik') ?: null,
            'jabatan'       => $request->getPost('jabatan') ?: null,
            'no_telp'       => $request->getPost('no_telp') ?: null,
            'tanggal_masuk' => $request->getPost('tanggal_masuk'),
            'tanggal_keluar' => null,
            'status'        => $request->getPost('status'),
        ];
        $db->table('karyawan')->insert($kData);

        $db->transComplete();
        if (! $db->transStatus()) {
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan data.');
        }

        return redirect()->to('/owner/employees')->with('success', 'Karyawan berhasil ditambahkan.');
    }

    // FORM EDIT
    public function employeeEdit($id)
    {
        $db = \Config\Database::connect();
        $row = $db->query("
        SELECT k.id, k.user_id, u.name, u.email, u.is_active,
               k.nip, k.nik, k.jabatan, k.no_telp, k.tanggal_masuk, k.tanggal_keluar, k.status
        FROM karyawan k
        JOIN users u ON u.id = k.user_id
        WHERE k.id = ?
        LIMIT 1
    ", [$id])->getRowArray();

        if (! $row) return redirect()->to('/owner/employees')->with('error', 'Data tidak ditemukan.');

        return view('owner/employees/form', [
            'title' => 'Edit Karyawan',
            'mode'  => 'edit',
            'data'  => $row,
        ]);
    }

    // UPDATE EDIT
    public function employeeUpdate($id)
    {
        $request = $this->request;
        $db = \Config\Database::connect();

        // ambil user_id untuk validasi unique email
        $row = $db->table('karyawan')->select('user_id')->where('id', $id)->get()->getRowArray();
        if (! $row) return redirect()->to('/owner/employees')->with('error', 'Data tidak ditemukan.');
        $user_id = (int) $row['user_id'];

        $rules = [
            'name'          => 'required|min_length[3]',
            'email'         => "required|valid_email|is_unique[users.email,id,{$user_id}]",
            'password'      => 'permit_empty|min_length[6]',
            'nip'           => 'permit_empty|max_length[20]',
            'nik'           => 'permit_empty|exact_length[16]|numeric',
            'jabatan'       => 'permit_empty|max_length[100]',
            'no_telp'       => 'permit_empty|max_length[20]',
            'tanggal_masuk' => 'required|valid_date[Y-m-d]',
            'tanggal_keluar' => 'permit_empty|valid_date[Y-m-d]',
            'status'        => 'required|in_list[aktif,nonaktif,resign,pensiun]',
            'is_active'     => 'required|in_list[0,1]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $db->transStart();

        // update users
        $userUpd = [
            'name'      => $request->getPost('name'),
            'email'     => $request->getPost('email'),
            'is_active' => (int) $request->getPost('is_active'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];
        if ($request->getPost('password')) {
            $userUpd['password'] = password_hash($request->getPost('password'), PASSWORD_BCRYPT);
        }
        $db->table('users')->where('id', $user_id)->update($userUpd);

        // update karyawan
        $kUpd = [
            'nip'            => $request->getPost('nip') ?: null,
            'nik'            => $request->getPost('nik') ?: null,
            'jabatan'        => $request->getPost('jabatan') ?: null,
            'no_telp'        => $request->getPost('no_telp') ?: null,
            'tanggal_masuk'  => $request->getPost('tanggal_masuk'),
            'tanggal_keluar' => $request->getPost('tanggal_keluar') ?: null,
            'status'         => $request->getPost('status'),
        ];
        $db->table('karyawan')->where('id', $id)->update($kUpd);

        $db->transComplete();
        if (! $db->transStatus()) {
            return redirect()->back()->withInput()->with('error', 'Gagal mengupdate data.');
        }

        return redirect()->to('/owner/employees')->with('success', 'Perubahan berhasil disimpan.');
    }

    public function employeeToggle(int $id)
    {
        $m   = new EmployeeModel();
        $row = $m->where('role', 'karyawan')->find($id);
        if (!$row) {
            return redirect()->to('/owner/employees')->with('error', 'Data karyawan tidak ditemukan.');
        }
        $new = (int)!((int)$row['is_active']);
        $m->update($id, ['is_active' => $new]);

        return redirect()->to('/owner/employees')->with('success', 'Status karyawan diperbarui.');
    }

    public function employeeDelete(int $id)
    {
        $m   = new EmployeeModel();
        $row = $m->where('role', 'karyawan')->find($id);
        if (!$row) {
            return redirect()->to('/owner/employees')->with('error', 'Data karyawan tidak ditemukan.');
        }
        $m->delete($id);
        return redirect()->to('/owner/employees')->with('success', 'Karyawan berhasil dihapus.');
    }

    public function employeesExportCsv()
    {
        $db = \Config\Database::connect();
        $rows = $db->query("
        SELECT u.name, u.email, u.is_active,
               k.nip, k.nik, k.jabatan, k.no_telp, k.tanggal_masuk, k.status
        FROM karyawan k
        JOIN users u ON u.id = k.user_id
        ORDER BY u.name ASC
    ")->getResultArray();

        // Tulis ke buffer (aman di CI4)
        $fp = fopen('php://temp', 'r+');

        // BOM UTF-8 supaya Excel tidak “acak-acakan” karakter
        fwrite($fp, "\xEF\xBB\xBF");

        // Header kolom
        fputcsv($fp, ['Nama', 'Email', 'Is Active', 'NIP', 'NIK', 'Jabatan', 'No. Telp', 'Tgl Masuk', 'Status']);

        // Isi data
        foreach ($rows as $r) {
            fputcsv($fp, [
                $r['name'],
                $r['email'],
                (int)$r['is_active'],
                $r['nip'],
                $r['nik'],
                $r['jabatan'],
                $r['no_telp'],
                $r['tanggal_masuk'],
                $r['status'],
            ]);
        }

        rewind($fp);
        $csv = stream_get_contents($fp);
        fclose($fp);

        $filename = 'karyawan-' . date('Ymd-His') . '.csv';
        return $this->response
            ->setHeader('Content-Type', 'text/csv; charset=UTF-8')
            ->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->setBody($csv);
    }

    public function employeesByStatus(string $status)
    {
        $status = strtolower($status);
        if (!in_array($status, ['resign', 'pensiun'])) {
            return redirect()->to('/owner/employees')->with('error', 'Status tidak dikenal.');
        }

        $db = \Config\Database::connect();
        $rows = $db->query("
        SELECT k.id, u.name, u.email, u.is_active,
               k.nip, k.nik, k.jabatan, k.no_telp, k.tanggal_masuk, k.tanggal_keluar, k.status
        FROM karyawan k
        JOIN users u ON u.id = k.user_id
        WHERE k.status=?
        ORDER BY k.tanggal_keluar DESC, u.name ASC
    ", [$status])->getResultArray();

        return view('owner/employees/list_status', [
            'title' => 'Karyawan ' . ucfirst($status),
            'rows'  => $rows,
            'status' => $status,
        ]);
    }

    public function employeeChangeStatusForm($id)
    {
        $db  = \Config\Database::connect();
        $row = $db->query("
        SELECT k.id, k.user_id, u.name, u.email, k.status, k.tanggal_keluar
        FROM karyawan k JOIN users u ON u.id=k.user_id
        WHERE k.id=? LIMIT 1
    ", [$id])->getRowArray();

        if (!$row) return redirect()->to('/owner/employees')->with('error', 'Data karyawan tidak ditemukan.');

        return view('owner/employees/change_status', [
            'title' => 'Ubah Status Karyawan',
            'data'  => $row,
        ]);
    }

    public function employeeChangeStatus($id)
    {
        $req = $this->request;
        $statusBaru   = strtolower($req->getPost('status')); // 'resign' atau 'pensiun'
        $tanggalKeluar = $req->getPost('tanggal_keluar');

        if (!in_array($statusBaru, ['resign', 'pensiun'])) {
            return redirect()->back()->withInput()->with('error', 'Status tidak valid.');
        }
        if (!$this->validate([
            'tanggal_keluar' => 'required|valid_date[Y-m-d]'
        ])) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $db = \Config\Database::connect();
        $db->transStart();

        // 1) Update tabel karyawan
        $db->table('karyawan')->where('id', $id)->update([
            'status'         => $statusBaru,
            'tanggal_keluar' => $tanggalKeluar,
        ]);

        // 2) (opsional/umum) Nonaktifkan akun login
        $userId = (int) $db->table('karyawan')->select('user_id')->where('id', $id)->get()->getRow('user_id');
        if ($userId) {
            $db->table('users')->where('id', $userId)->update(['is_active' => 0, 'updated_at' => date('Y-m-d H:i:s')]);
        }

        $db->transComplete();
        if (!$db->transStatus()) {
            return redirect()->back()->withInput()->with('error', 'Gagal mengubah status.');
        }

        return redirect()->to('/owner/employees/status/' . $statusBaru)->with('success', 'Status karyawan berhasil diubah.');
    }

    // ----------------- HELPER GENERIC (pakai COALESCE untuk tgl pengajuan) -----------------
    private function _exitsIndexUsing(string $table, string $tipe)
    {
        $db = \Config\Database::connect();

        if ($table === 'pensiun') {
            $sql = "
            SELECT pd.id, pd.status,
                   DATE(pd.created_at) AS tgl_pengajuan,   -- pensiun pakai created_at
                   pd.tanggal_efektif,
                   u.name AS nama, u.email, k.jabatan
            FROM pensiun pd
            JOIN karyawan k ON k.id = pd.karyawan_id
            JOIN users u    ON u.id = k.user_id
            ORDER BY pd.status='menunggu' DESC, tgl_pengajuan DESC, pd.id DESC
        ";
            $rows = $db->query($sql)->getResultArray();
        } else { // pengunduran_diri
            $sql = "
            SELECT pd.id, pd.status,
                   pd.tanggal_pengajuan AS tgl_pengajuan,  -- resign punya tanggal_pengajuan
                   pd.tanggal_efektif,
                   u.name AS nama, u.email, k.jabatan
            FROM pengunduran_diri pd
            JOIN karyawan k ON k.id = pd.karyawan_id
            JOIN users u    ON u.id = k.user_id
            ORDER BY pd.status='menunggu' DESC, tgl_pengajuan DESC, pd.id DESC
        ";
            $rows = $db->query($sql)->getResultArray();
        }

        return view('owner/exits/index', [
            'title' => 'Pengajuan ' . ucfirst($tipe),
            'rows'  => $rows,
            'tipe'  => $tipe,
        ]);
    }

    private function _exitsShowUsing(string $table, int $id, string $tipe)
    {
        $db = \Config\Database::connect();

        if ($table === 'pensiun') {
            $sql = "
            SELECT pd.*, DATE(pd.created_at) AS tgl_pengajuan,    -- pensiun pakai created_at
                   u.name, u.email,
                   k.id AS karyawan_id, k.jabatan, k.tanggal_masuk, k.status AS status_karyawan
            FROM pensiun pd
            JOIN karyawan k ON k.id = pd.karyawan_id
            JOIN users u    ON u.id = k.user_id
            WHERE pd.id = ? LIMIT 1
        ";
        } else { // pengunduran_diri
            $sql = "
            SELECT pd.*, pd.tanggal_pengajuan AS tgl_pengajuan,   -- resign punya tanggal_pengajuan
                   u.name, u.email,
                   k.id AS karyawan_id, k.jabatan, k.tanggal_masuk, k.status AS status_karyawan
            FROM pengunduran_diri pd
            JOIN karyawan k ON k.id = pd.karyawan_id
            JOIN users u    ON u.id = k.user_id
            WHERE pd.id = ? LIMIT 1
        ";
        }

        $row = $db->query($sql, [$id])->getRowArray();
        if (!$row) {
            return redirect()->to("/owner/{$tipe}")->with('error', 'Pengajuan tidak ditemukan.');
        }

        return view('owner/exits/show', [
            'title' => 'Detail Pengajuan ' . ucfirst($tipe),
            'data'  => $row,
            'tipe'  => $tipe,
        ]);
    }


    private function _exitsDecideUsing(string $table, int $id, string $tipe, bool $approve)
    {
        $db = \Config\Database::connect();
        $db->transStart();

        $pk = $db->table($table)->where('id', $id)->get()->getRowArray();
        if (!$pk) {
            $db->transComplete();
            return redirect()->to("/owner/{$tipe}")->with('error', 'Pengajuan tidak ditemukan.');
        }
        if (($pk['status'] ?? 'menunggu') !== 'menunggu') {
            $db->transComplete();
            return redirect()->to("/owner/{$tipe}/{$id}")->with('error', 'Pengajuan sudah diproses.');
        }

        if ($approve) {
            // 1) setujui pengajuan
            $db->table($table)->where('id', $id)->update(['status' => 'disetujui']);

            // 2) update karyawan
            $db->table('karyawan')->where('id', $pk['karyawan_id'])->update([
                'status'         => $tipe, // 'resign' atau 'pensiun'
                'tanggal_keluar' => $pk['tanggal_efektif'],
            ]);

            // 3) nonaktifkan akun
            $userId = (int) $db->table('karyawan')->select('user_id')->where('id', $pk['karyawan_id'])->get()->getRow('user_id');
            if ($userId) {
                $db->table('users')->where('id', $userId)->update([
                    'is_active'  => 0,
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
            }
            $msg = 'Pengajuan disetujui. Akun dinonaktifkan.';
        } else {
            $db->table($table)->where('id', $id)->update(['status' => 'ditolak']);
            $msg = 'Pengajuan ditolak.';
        }

        $db->transComplete();
        if (!$db->transStatus()) {
            return redirect()->to("/owner/{$tipe}/{$id}")->with('error', 'Gagal memproses pengajuan.');
        }
        return redirect()->to("/owner/{$tipe}/{$id}")->with('success', $msg);
    }

    // ----------------- WRAPPER RESIGN -----------------
    public function resignIndex()
    {
        return $this->_exitsIndexUsing('pengunduran_diri', 'resign');
    }
    public function resignShow($id)
    {
        return $this->_exitsShowUsing('pengunduran_diri', (int)$id, 'resign');
    }
    public function resignApprove($id)
    {
        return $this->_exitsDecideUsing('pengunduran_diri', (int)$id, 'resign', true);
    }
    public function resignReject($id)
    {
        return $this->_exitsDecideUsing('pengunduran_diri', (int)$id, 'resign', false);
    }

    // ----------------- WRAPPER PENSIUN -----------------
    public function pensiunIndex()
    {
        return $this->_exitsIndexUsing('pensiun', 'pensiun');
    }
    public function pensiunShow($id)
    {
        return $this->_exitsShowUsing('pensiun', (int)$id, 'pensiun');
    }
    public function pensiunApprove($id)
    {
        return $this->_exitsDecideUsing('pensiun', (int)$id, 'pensiun', true);
    }
    public function pensiunReject($id)
    {
        return $this->_exitsDecideUsing('pensiun', (int)$id, 'pensiun', false);
    }

    public function calonKaryawanDetail($id)
    {
        $model = new CalonKaryawanModel();
        $data = [
            'title' => 'Detail Calon Karyawan',
            'row'   => $model->find($id) // Ambil data berdasarkan ID
        ];

        return view('owner/calon_karyawan_detail', $data);
    }

    public function promoteCalon($id)
    {
        $calon = $this->calonModel->find($id);
        if (! $calon) {
            return redirect()->back()->with('error', 'Calon karyawan tidak ditemukan.');
        }

        $db = \Config\Database::connect();
        $db->transStart();

        // 1) Buat akun user baru
        $userModel = new \App\Models\UserModel();
        $userId = $userModel->insert([
            'name'      => $calon['nama'],          // <-- PENTING: mapping nama calon ke user
            // kalau di DB kamu kolomnya nama_lengkap, ganti menjadi:
            // 'nama_lengkap' => $calon['nama_lengkap'],
            'email'     => $calon['email'],
            'password'  => password_hash('123456', PASSWORD_DEFAULT),
            'role'      => 'karyawan',
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        // 2) Masukkan ke tabel karyawan
        $karyawanModel = new \App\Models\KaryawanModel();
        $karyawanModel->insert([
            'user_id'        => $userId,
            'pemilik_id'     => session('pemilik_id'),
            'nip'            => null,
            'nik'            => $calon['nik'],
            'no_telp'        => $calon['no_hp'],
            'jabatan'        => $calon['posisi'],     // atau kolom posisi di calon_karyawan
            'tanggal_masuk'  => date('Y-m-d'),
            'status'         => 'aktif',
        ]);

        // 3) Update status calon
        $this->calonModel->update($id, [
            'status' => 'diterima',
        ]);

        $db->transComplete();

        return redirect()->to(site_url('owner/calon-karyawan'))
            ->with('success', 'Calon karyawan berhasil dijadikan karyawan.');
    }



    public function __construct()
    {
        $this->calonModel    = new CalonKaryawanModel();
        $this->karyawanModel = new KaryawanModel();
        $this->userModel     = new UserModel(); // kalau pakai
    }

    public function employeesDelete($id)
    {
        $karyawan = $this->karyawanModel->find($id);
        if (! $karyawan) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $status = strtolower($karyawan['status'] ?? '');

        // Hanya boleh hapus jika status resign atau pensiun
        if (! in_array($status, ['resign', 'pensiun'])) {
            return redirect()->back()
                ->with('error', 'Hanya karyawan dengan status Resign atau Pensiun yang boleh dihapus.');
        }

        $this->karyawanModel->delete($id);

        return redirect()->to(site_url('owner/employees'))
            ->with('success', 'Data karyawan berhasil dihapus.');
    }
}
