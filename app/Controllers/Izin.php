<?php

namespace App\Controllers;

use App\Models\IzinModel;
use App\Models\KaryawanModel;

class Izin extends BaseController
{

    // List untuk Pemilik (approval)
    public function index()
    {
        $this->authorize('pemilik');
        $m = new IzinModel();
        $data['rows'] = $m->orderBy('created_at', 'desc')->findAll(50);
        return view('izin/index', $data);
    }

    public function setStatus($id)
    {
        $this->authorize('pemilik');
        $status = $this->request->getPost('status'); // 'disetujui' | 'ditolak'
        (new IzinModel())->update($id, ['status' => $status]);
        return redirect()->back()->with('success', 'Status izin diperbarui');
    }

    // === Area Karyawan ===
    public function myIndex()
    {
        $this->authorize('karyawan');
        $kid = $this->myKaryawanId();
        $data['rows'] = (new IzinModel())->where('karyawan_id', $kid)->orderBy('created_at', 'desc')->findAll(30);
        return view('izin/index', $data);
    }

    public function myForm()
    {
        $this->authorize('karyawan');
        return view('izin/form');
    }

    public function createMy()
    {
        $this->authorize('karyawan');
        $kid = $this->myKaryawanId();

        $data = [
            'karyawan_id' => $kid,
            'tgl_pengajuan' => date('Y-m-d'),
            'tgl_mulai' => $this->request->getPost('tgl_mulai'),
            'tgl_selesai' => $this->request->getPost('tgl_selesai'),
            'jenis' => $this->request->getPost('jenis'),
            'keterangan' => $this->request->getPost('keterangan'),
            'status' => 'menunggu',
        ];
        (new IzinModel())->insert($data);
        return redirect()->to('/me/izin')->with('success', 'Pengajuan izin terkirim');
    }

    private function myKaryawanId()
    {
        $u = session('user');
        $k = (new KaryawanModel())->where('user_id', $u['id'])->first();
        if (!$k) {
            throw new \RuntimeException('Karyawan tidak ditemukan');
        }
        return $k['id'];
    }

    private function authorize($role)
    {
        if (session('user.role') !== $role) {
            redirect()->to('/dashboard')->with('error', 'Akses ditolak')->send();
            exit;
        }
    }
}
