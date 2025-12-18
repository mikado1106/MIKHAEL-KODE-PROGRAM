<?php

namespace App\Controllers\Employee;

use CodeIgniter\Controller;
use Config\Database;

class Exits extends Controller
{
    protected $db;

    public function __construct()
    {
        $this->db = Database::connect();
        helper(['form', 'url']);
    }

    /** Ambil id karyawan dari user login (pakai session yang sudah ada) */
    private function karyawanIdFromSession(): int
    {
        $uid = (int) session('idUser'); // sesuaikan dengan key session Anda
        if (!$uid) return 0;

        $row = $this->db->table('karyawan')
            ->select('id')
            ->where('user_id', $uid)
            ->get()->getRowArray();

        return (int) ($row['id'] ?? 0);
    }

    /* ===================== RESIGN ===================== */

    // pakai: app/Views/employee/exits/resign_form.php
    public function resignForm()
    {
        return view('employee/exits/resign_form', [
            'title' => 'Pengajuan Pengunduran Diri',
        ]);
    }

    public function resignStore()
    {
        $kid = $this->karyawanIdFromSession();
        if (!$kid) {
            return redirect()->back()->with('error', 'Data karyawan tidak ditemukan.');
        }

        $data = [
            'karyawan_id'       => $kid,
            'tanggal_pengajuan' => date('Y-m-d'),
            'tanggal_efektif'   => $this->request->getPost('tanggal_efektif'),
            'alasan'            => $this->request->getPost('alasan'),
            'dokumen_path'      => null,
            'status'            => 'menunggu',
            'created_at'        => date('Y-m-d H:i:s'),
        ];

        // upload opsional
        $file = $this->request->getFile('dokumen');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $new = $file->getRandomName();
            $file->move(FCPATH . 'uploads/exits', $new);
            $data['dokumen_path'] = 'uploads/exits/' . $new;
        }

        $this->db->table('pengunduran_diri')->insert($data);

        return redirect()->to(site_url('employee/exits/resign'))
            ->with('success', 'Pengajuan pengunduran diri dikirim.');
    }

    /* ===================== PENSIUN ===================== */

    // pakai: app/Views/employee/exits/pension_form.php
    public function pensionForm()
    {
        return view('employee/exits/pension_form', [
            'title' => 'Pengajuan Pensiun',
        ]);
    }

    public function pensionStore()
    {
        $kid = $this->karyawanIdFromSession();
        if (!$kid) {
            return redirect()->back()->with('error', 'Data karyawan tidak ditemukan.');
        }

        $data = [
            'karyawan_id'   => $kid,
            'tanggal_efektif' => $this->request->getPost('tanggal_efektif'),
            'dokumen_path'  => null,
            'status'        => 'menunggu',
            'created_at'    => date('Y-m-d H:i:s'),
        ];

        // upload opsional
        $file = $this->request->getFile('dokumen');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $new = $file->getRandomName();
            $file->move(FCPATH . 'uploads/exits', $new);
            $data['dokumen_path'] = 'uploads/exits/' . $new;
        }

        $this->db->table('pensiun')->insert($data);

        return redirect()->to(site_url('employee/exits/pension'))
            ->with('success', 'Pengajuan pensiun dikirim.');
    }
}
