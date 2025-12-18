<?php

namespace App\Models;

use CodeIgniter\Model;

class AbsensiModel extends Model
{
    protected $table         = 'absensi';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = ['karyawan_id', 'tanggal', 'waktu_masuk', 'waktu_keluar', 'catatan', 'created_at'];

    // bantu: ambil record hari ini untuk karyawan tertentu
    public function todayFor(int $karyawanId, string $today): ?array
    {
        return $this->where(['karyawan_id' => $karyawanId, 'tanggal' => $today])->first();
    }
}
