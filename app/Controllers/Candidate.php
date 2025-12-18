<?php

namespace App\Controllers;

use App\Models\CalonKaryawanModel;

class Candidate extends BaseController
{
    public function form()
    {
        return view('candidate/apply', [
            'title' => 'Lamaran Kerja'
        ]);
    }

    public function save()
    {
        $model = new CalonKaryawanModel();

        // --- upload CV (opsional) ---
        $file   = $this->request->getFile('cv');
        $cvPath = null;

        if ($file && $file->isValid() && ! $file->hasMoved()) {
            $newName = $file->getRandomName();
            // simpan di public/uploads/cv (bisa kamu sesuaikan)
            $file->move(FCPATH . 'uploads/cv', $newName);
            $cvPath = 'uploads/cv/' . $newName;
        }

        // --- simpan ke database (tanpa validasi ribet) ---
        $model->insert([
            'nama'             => $this->request->getPost('nama'),
            'email'            => $this->request->getPost('email'),
            'no_hp'            => $this->request->getPost('no_hp'),
            'nik'              => $this->request->getPost('nik'),
            'posisi'           => $this->request->getPost('posisi'),
            'cv_path'          => $cvPath,
            'status'           => 'baru',
            'jadwal_interview' => null,
            'created_at'       => date('Y-m-d H:i:s'),
        ]);

        return redirect()->to(site_url('karir'))
            ->with('success', 'Lamaran berhasil dikirim.');
    }
}
