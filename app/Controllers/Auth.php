<?php

namespace App\Controllers;

use App\Models\UserModel;
use Config\Database;

class Auth extends BaseController
{
    public function login()
    {
        return view('auth/login'); // pastikan view-nya ada di app/Views/auth/login.php
    }

    public function attempt()
    {
        $email = trim((string)$this->request->getPost('email'));
        $pass  = (string)$this->request->getPost('password');

        $db  = \Config\Database::connect();
        $row = $db->table('users')->where('email', $email)->get()->getRowArray();

        if (!$row || (int)$row['is_active'] !== 1) {
            return redirect()->back()->with('error', 'Email atau password salah.');
        }

        $stored = $row['password'];
        $ok = (strpos($stored, '$2y$') === 0) ? password_verify($pass, $stored) : ($pass === $stored);

        if (!$ok) {
            return redirect()->back()->with('error', 'Email atau password salah.');
        }

        // Auto-upgrade kalau masih plaintext
        if (strpos($stored, '$2y$') !== 0) {
            $db->table('users')->where('id', $row['id'])->update([
                'password'             => password_hash($pass, PASSWORD_BCRYPT),
                'password_updated_at'  => date('Y-m-d H:i:s'),
                'updated_at'           => date('Y-m-d H:i:s'),
            ]);
        }

        // set session
        session()->set([
            'user_id' => $row['id'],
            'name'    => $row['name'],
            'email'   => $row['email'],
            'role'    => $row['role'],
            'logged_in' => true,
        ]);

        // Paksa ganti password bila must_change_password=1
        if ((int)($row['must_change_password'] ?? 0) === 1) {
            return redirect()->to('/owner/password')->with('error', 'Silakan ganti password terlebih dahulu.');
        }

        // redirect per role
        return ($row['role'] === 'pemilik') ? redirect()->to('/owner') : redirect()->to('/employee');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login')->with('success', 'Anda sudah logout.');
    }

    // OPTIONAL: endpoint test koneksi DB saat dev
    public function dbTest()
    {
        try {
            $db = Database::connect();
            $row = $db->query('SELECT 1 AS ok')->getRowArray();
            return $this->response->setJSON(['database' => 'connected', 'result' => $row]);
        } catch (\Throwable $e) {
            return $this->response->setStatusCode(500)->setJSON(['database' => 'error', 'message' => $e->getMessage()]);
        }
    }
}
