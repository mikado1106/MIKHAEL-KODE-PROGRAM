<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class RoleFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();
        if (!$session->get('logged_in')) {
            return redirect()->to('/login')->with('error', 'Silakan login dulu.');
        }

        // role yang diizinkan dilewatkan via argumen filter, contoh: ['pemilik'] atau ['karyawan']
        $allowed = $arguments ?? [];
        $userRole = $session->get('role');

        if (!in_array($userRole, $allowed, true)) {
            // Tidak punya akses → arahkan balik ke dashboard role-nya sendiri
            return $userRole === 'pemilik'
                ? redirect()->to('/owner')->with('error', 'Akses ditolak untuk halaman tersebut.')
                : redirect()->to('/employee')->with('error', 'Akses ditolak untuk halaman tersebut.');
        }
        return null;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // no-op
    }
}
