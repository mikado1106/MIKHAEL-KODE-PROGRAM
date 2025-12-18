<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\Exceptions\PageNotFoundException;

class Leaves extends BaseController
{
    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
        helper(['form', 'text']);
    }

    /**
     * Ambil baris karyawan berdasar session('idUser').
     * Wajib ada relasi user->karyawan.
     */
    private function currentEmployee(): ?array
    {
        $userId = (int) session('idUser');
        if (!$userId) {
            return null;
        }

        return $this->db->query("
            SELECT k.*
            FROM karyawan k
            WHERE k.user_id = ?
            LIMIT 1
        ", [$userId])->getRowArray();
    }

    /**
     * Halaman index (daftar gabungan izin + cuti milik karyawan saat ini)
     */
    public function index()
    {
        $emp = $this->currentEmployee();
        if (!$emp) {
            // Kalau tidak ada sesi atau mapping user->karyawan hilang
            return redirect()->to('/login')->with('error', 'Silakan login.');
        }

        // Gabungkan izin & cuti agar cocok dengan leaves_index.php Anda
        // kolom disamakan: tipe, id, jenis, tgl_mulai, tgl_selesai, status, tgl_pengajuan
        $rows = $this->db->query("
            SELECT 'izin'  AS tipe, i.id, i.jenis, i.tgl_mulai, i.tgl_selesai, i.status, i.tgl_pengajuan
            FROM izin i
            WHERE i.karyawan_id = ?
            UNION ALL
            SELECT 'cuti'  AS tipe, c.id, c.jenis, c.tgl_mulai, c.tgl_selesai, c.status, c.tgl_pengajuan
            FROM cuti c
            WHERE c.karyawan_id = ?
            ORDER BY tgl_pengajuan DESC
        ", [$emp['id'], $emp['id']])->getResultArray();

        return view('employee/leaves_index', [
            'title' => 'Pengajuan Izin & Cuti',
            'rows'  => $rows,
        ]);
    }

    /**
     * Form ajukan izin
     */
    public function izinNew()
    {
        $emp = $this->currentEmployee();
        if (!$emp) return redirect()->to('/login')->with('error', 'Silakan login.');

        return view('employee/izin_form', [
            'title' => 'Ajukan Izin',
        ]);
    }

    /**
     * Simpan pengajuan izin
     */
    public function izinCreate()
    {
        $emp = $this->currentEmployee();
        if (!$emp) return redirect()->to('/login')->with('error', 'Silakan login.');

        // Validasi sederhana
        $rules = [
            'tgl_mulai'   => 'required|valid_date[Y-m-d]',
            'tgl_selesai' => 'required|valid_date[Y-m-d]',
            'jenis'       => 'required|max_length[50]',
            // 'keterangan'  => 'permit empty|max_length[255]',
        ];
        if (!$this->validate($rules)) {
            return redirect()->back()->with('error', 'Input tidak valid.')->withInput();
        }

        // Upload lampiran (jika ada)
        $lampiranPath = null;
        $file = $this->request->getFile('lampiran');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $newName = 'izin_' . date('Ymd_His') . '_' . $file->getRandomName();
            $file->move(WRITEPATH . 'uploads/leaves', $newName);
            $lampiranPath = 'writable/uploads/leaves/' . $newName;
        }

        // Siapkan data insert sesuai tabel izin Anda
        $data = [
            'karyawan_id'  => (int) $emp['id'],
            'pemilik_id'   => (int) ($emp['pemilik_id'] ?? 1), // fallback 1
            'tgl_pengajuan' => date('Y-m-d'),
            'tgl_mulai'    => $this->request->getPost('tgl_mulai'),
            'tgl_selesai'  => $this->request->getPost('tgl_selesai'),
            'jenis'        => $this->request->getPost('jenis'),
            'keterangan'   => $this->request->getPost('keterangan') ?: null,
            'lampiran_path' => $lampiranPath,
            'status'       => 'menunggu',
            'created_at'   => date('Y-m-d H:i:s'),
            'catatan'      => null, // kolom opsional
        ];

        $this->db->table('izin')->insert($data);

        return redirect()->to('/employee/leaves')->with('success', 'Pengajuan izin dikirim.');
    }

    /**
     * Detail izin (opsional)
     */
    public function izinShow($id)
    {
        $emp = $this->currentEmployee();
        if (!$emp) return redirect()->to('/login')->with('error', 'Silakan login.');

        $row = $this->db->table('izin')
            ->where('id', (int) $id)
            ->where('karyawan_id', (int) $emp['id'])
            ->get()->getRowArray();

        if (!$row) {
            throw new PageNotFoundException('Data izin tidak ditemukan.');
        }

        return view('employee/izin_show', [
            'title' => 'Detail Izin',
            'row'   => $row,
        ]);
    }

    /**
     * Form ajukan cuti
     */
    public function cutiNew()
    {
        $emp = $this->currentEmployee();
        if (!$emp) return redirect()->to('/login')->with('error', 'Silakan login.');

        return view('employee/cuti_form', [
            'title' => 'Ajukan Cuti',
        ]);
    }

    /**
     * Simpan pengajuan cuti
     */
    public function cutiCreate()
    {
        $emp = $this->currentEmployee();
        if (!$emp) return redirect()->to('/login')->with('error', 'Silakan login.');

        $rules = [
            'tgl_mulai'   => 'required|valid_date[Y-m-d]',
            'tgl_selesai' => 'required|valid_date[Y-m-d]',
            'jenis'       => 'required|max_length[50]',   // contoh: tahunan/besar/dsb
            'alasan'      => 'required|max_length[255]',
        ];
        if (!$this->validate($rules)) {
            return redirect()->back()->with('error', 'Input tidak valid.')->withInput();
        }

        $data = [
            'karyawan_id'  => (int) $emp['id'],
            'pemilik_id'   => (int) ($emp['pemilik_id'] ?? 1),
            'tgl_pengajuan' => date('Y-m-d'),
            'tgl_mulai'    => $this->request->getPost('tgl_mulai'),
            'tgl_selesai'  => $this->request->getPost('tgl_selesai'),
            'jenis'        => $this->request->getPost('jenis'),   // contoh: tahunan/besar
            'alasan'       => $this->request->getPost('alasan'),
            'status'       => 'menunggu',
            'created_at'   => date('Y-m-d H:i:s'),
            'catatan'      => null,
        ];

        $this->db->table('cuti')->insert($data);

        return redirect()->to('/employee/leaves')->with('success', 'Pengajuan cuti dikirim.');
    }

    /**
     * Detail cuti (opsional)
     */
    public function cutiShow($id)
    {
        $emp = $this->currentEmployee();
        if (!$emp) return redirect()->to('/login')->with('error', 'Silakan login.');

        $row = $this->db->table('cuti')
            ->where('id', (int) $id)
            ->where('karyawan_id', (int) $emp['id'])
            ->get()->getRowArray();

        if (!$row) {
            throw new PageNotFoundException('Data cuti tidak ditemukan.');
        }

        return view('employee/cuti_show', [
            'title' => 'Detail Cuti',
            'row'   => $row,
        ]);
    }
}
