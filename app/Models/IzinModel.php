<?php

namespace App\Models;

use CodeIgniter\Model;

class IzinModel extends Model
{
    protected $table         = 'izin';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = [
        'karyawan_id',
        'pemilik_id',
        'tgl_pengajuan',
        'tgl_mulai',
        'tgl_selesai',
        'jenis',
        'keterangan',
        'lampiran_path',
        'status',
        'created_at'
    ];

    public function getPaged(string $q = '', string $status = '', int $perPage = 10)
    {
        $builder = $this->select('izin.*, karyawan.id AS k_id, users.name, users.email, users.id AS u_id')
            ->join('karyawan', 'karyawan.id = izin.karyawan_id', 'left')
            ->join('users',    'users.id = karyawan.user_id',   'left')
            ->orderBy('izin.id', 'DESC');

        if ($q !== '') {
            $builder->groupStart()
                ->like('users.name', $q)
                ->orLike('users.email', $q)
                ->orLike('izin.keterangan', $q)
                ->groupEnd();
        }
        if ($status !== '') {
            $builder->where('izin.status', $status); // menunggu/disetujui/ditolak
        }
        return $builder->paginate($perPage);
    }

    public function countByStatus(string $status): int
    {
        return $this->where('status', $status)->countAllResults();
    }
}
