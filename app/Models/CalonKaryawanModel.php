<?php

namespace App\Models;

use CodeIgniter\Model;

class CalonKaryawanModel extends Model
{
    protected $table      = 'calon_karyawan';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'nama',
        'email',
        'no_hp',
        'nik',
        'posisi',
        'cv_path',
        'status',
        'jadwal_interview',
        'created_at',
    ];

    protected $useTimestamps = false;   // supaya tidak pakai created_at/updated_at otomatis
}
