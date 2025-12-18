<?php

namespace App\Controllers;

class Dashboard extends BaseController
{
    public function owner()
    {
        return view('dashboard/owner', [
            'title' => 'Dashboard Pemilik'
        ]);
    }

    public function employee()
    {
        return view('dashboard/employee', [
            'title' => 'Beranda Karyawan'
        ]);
    }
}
