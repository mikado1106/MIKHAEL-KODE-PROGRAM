<?php

namespace App\Models;

use CodeIgniter\Model;

class EmployeeModel extends Model
{
    protected $table         = 'users';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = ['name', 'email', 'password', 'role', 'is_active', 'created_at', 'updated_at'];

    // Biar aman: selalu filter role = karyawan di query builder
    protected function applyRoleScope()
    {
        return $this->where('role', 'karyawan');
    }

    public function getPagedList(string $keyword = '', int $perPage = 10)
    {
        $builder = $this->applyRoleScope()->orderBy('id', 'DESC');
        if ($keyword !== '') {
            $builder->groupStart()
                ->like('name', $keyword)
                ->orLike('email', $keyword)
                ->groupEnd();
        }
        return $builder->paginate($perPage);
    }

    public function countByStatus(?int $isActive = null): int
    {
        $builder = $this->applyRoleScope();
        if (!is_null($isActive)) {
            $builder->where('is_active', $isActive);
        }
        return $builder->countAllResults();
    }

    public function insertEmployee(array $data): bool
    {
        // pastikan role karyawan
        $data['role'] = 'karyawan';
        // jika password diisi plaintext: tetap simpan plaintext (DEV)
        // jika ingin hash: pakai password_hash($data['password'], PASSWORD_BCRYPT)
        return $this->insert($data) !== false;
    }

    public function updateEmployee(int $id, array $data): bool
    {
        $data['role'] = 'karyawan'; // jaga-jaga
        // kalau password dikosongkan di form edit, jangan update kolom password
        if (!isset($data['password']) || $data['password'] === '') {
            unset($data['password']);
        }
        return $this->update($id, $data);
    }
}
