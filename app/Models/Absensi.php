<?php

namespace App\Controllers;

use App\Models\AbsensiModel;
use App\Models\KaryawanModel;

class Absensi extends BaseController
{
    // List rekap untuk Pemilik
    public function index()
    {
        $this->authorize('pemilik');
        $m = new AbsensiModel();
        $data['rows'] = $m->orderBy('tanggal', 'desc')->findAll(50);
        return view('absensi/index', $data);
    }

    // Form untuk Karyawan isi harian
    public function myForm()
    {
        $this->authorize('karyawan');
        return view('absensi/form');
    }

    public function saveMy()
    {
        $this->authorize('karyawan');

        $userId = session('user.id');
        // ambil karyawan_id dari tabel karyawan berdasarkan user_id
        $k = (new KaryawanModel())->where('user_id', $userId)->first();
        if (!$k) return redirect()->back()->with('error', 'Data karyawan tidak ditemukan');

        $tanggal = $this->request->getPost('tanggal');
        $masuk   = $this->request->getPost('waktu_masuk');
        $keluar  = $this->request->getPost('waktu_keluar');
        $catatan = $this->request->getPost('catatan');

        $m = new AbsensiModel();
        // cek unik per (karyawan_id, tanggal)
        $exists = $m->where(['karyawan_id' => $k['id'], 'tanggal' => $tanggal])->first();
        if ($exists) {
            return redirect()->back()->with('error', 'Anda sudah mengisi absensi untuk tanggal ini');
        }

        $m->insert([
            'karyawan_id' => $k['id'],
            'tanggal' => $tanggal,
            'waktu_masuk' => $masuk ?: null,
            'waktu_keluar' => $keluar ?: null,
            'catatan' => $catatan ?: null
        ]);

        return redirect()->to('/me/absensi')->with('success', 'Absensi tersimpan');
    }

    private function authorize($role)
    {
        if (session('user.role') !== $role) {
            redirect()->to('/dashboard')->with('error', 'Akses ditolak')->send();
            exit;
        }
    }
}
