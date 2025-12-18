<?php

namespace App\Models;

use CodeIgniter\Model;

class KaryawanModel extends Model
{
    protected $table         = 'karyawan';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = [
        'user_id',
        'pemilik_id',
        'nip',
        'nik',          // <-- pastikan ada
        'jabatan',
        'no_telp',      // <-- pastikan ada
        'tanggal_masuk',
        'tanggal_keluar',
        'status',
    ];


    public function countTotal(): int
    {
        // semua baris karyawan
        return $this->countAllResults();
    }

    public function countActive(): int
    {
        // aktif mengacu ke users.is_active = 1
        return $this->join('users', 'users.id = karyawan.user_id', 'left')
            ->where('users.is_active', 1)
            ->countAllResults();
    }

    public function countNonActive(): int
    {
        return $this->join('users', 'users.id = karyawan.user_id', 'left')
            ->where('users.is_active', 0)
            ->countAllResults();
    }
}
