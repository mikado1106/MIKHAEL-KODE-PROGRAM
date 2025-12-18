<?php

namespace App\Models;

use CodeIgniter\Model;

class CutiModel extends Model
{
    protected $table         = 'cuti';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = [
        'karyawan_id',
        'pemilik_id',
        'tgl_pengajuan',
        'tgl_mulai',
        'tgl_selesai',
        'jenis',
        'alasan',
        'status',
        'created_at'
    ];

    public function getPaged(string $q = '', string $status = '', int $perPage = 10)
    {
        $builder = $this->select('cuti.*, karyawan.id AS k_id, users.name, users.email, users.id AS u_id')
            ->join('karyawan', 'karyawan.id = cuti.karyawan_id', 'left')
            ->join('users',    'users.id = karyawan.user_id',   'left')
            ->orderBy('cuti.id', 'DESC');

        if ($q !== '') {
            $builder->groupStart()
                ->like('users.name', $q)
                ->orLike('users.email', $q)
                ->orLike('cuti.alasan', $q)
                ->groupEnd();
        }
        if ($status !== '') {
            $builder->where('cuti.status', $status); // menunggu/disetujui/ditolak
        }
        return $builder->paginate($perPage);
    }

    public function countByStatus(string $status): int
    {
        return $this->where('status', $status)->countAllResults();
    }
}
