<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->get('/', 'Auth::login');
$routes->get('/login', 'Auth::login');
$routes->post('/login', 'Auth::attempt');
$routes->get('/logout', 'Auth::logout');

// Dashboard umum (jika mau akses langsung tanpa group)

$routes->get('/employee', 'Dashboard::employee', ['filter' => 'auth']);

$routes->get('karir', 'Candidate::form');        // Form lamaran calon karyawan
$routes->post('karir', 'Candidate::save');       // Mengirimkan lamaran calon

// --- Halaman karir & form lamaran (tanpa login) ---
$routes->get('karir',        'Candidate::form');
$routes->post('karir/simpan', 'Candidate::save');






// Group menu PEMILIK (hanya role 'pemilik')
$routes->group('owner', ['filter' => 'role:pemilik'], static function ($routes) {
    $routes->get('/', 'Owner::index'); // alias ke dashboard pemilik

    $routes->get('employees', 'Owner::employees');               // list
    $routes->get('reports', 'Owner::reports');        // laporan
    $routes->get('employees/create', 'Owner::employeeCreate');   // form create
    $routes->post('employees', 'Owner::employeeStore');          // simpan
    $routes->get('employees/(:num)/edit', 'Owner::employeeEdit/$1');   // form edit
    $routes->post('employees/(:num)/update', 'Owner::employeeUpdate/$1'); // update
    $routes->post('employees/(:num)/toggle', 'Owner::employeeToggle/$1'); // aktif/nonaktif
    $routes->post('employees/(:num)/delete', 'Owner::employeeDelete/$1'); // hapus
    $routes->get('employees/export/csv', 'Owner::employeesExportCsv');

    $routes->get('reports', 'Owner::reports');

    // Persetujuan Izin
    $routes->get('izin',              'Owner::izinIndex');
    $routes->get('izin/(:num)',       'Owner::izinShow/$1');
    $routes->post('izin/(:num)/ok',   'Owner::izinApprove/$1');
    $routes->post('izin/(:num)/no',   'Owner::izinReject/$1');

    // Persetujuan Cuti
    $routes->get('cuti',              'Owner::cutiIndex');
    $routes->get('cuti/(:num)',       'Owner::cutiShow/$1');
    $routes->post('cuti/(:num)/ok',   'Owner::cutiApprove/$1');
    $routes->post('cuti/(:num)/no',   'Owner::cutiReject/$1');

    // Rekap bulanan + export CSV
    $routes->get('reports/rekap', 'Owner::reportsRekap');
    $routes->get('reports/rekap/pdf',  'Owner::reportsRekapPdf');
    $routes->get('reports/rekap/csv', 'Owner::reportsRekapCsv');

    // Profil & Password Pemilik
    $routes->get('profile', 'Owner::profile');              // ringkas profil
    $routes->get('password', 'Owner::passwordForm');        // form ganti password
    $routes->post('password', 'Owner::passwordUpdate');     // submit ganti password

    // Rekap Absensi (Pemilik)
    $routes->get('attendance/rekap',      'Owner::attendanceRekap');
    $routes->get('attendance/rekap/csv',  'Owner::attendanceRekapCsv');
    $routes->get('attendance/rekap/pdf',  'Owner::attendanceRekapPdf');

    // Daftar menurut status
    $routes->get('employees/status/(:alpha)', 'Owner::employeesByStatus/$1');

    // Form ubah status -> resign/pensiun
    $routes->get('employees/(:num)/change-status',  'Owner::employeeChangeStatusForm/$1');   // tampil form
    $routes->post('employees/(:num)/change-status', 'Owner::employeeChangeStatus/$1');       // submit form

    // === Resign ===
    $routes->get('resign',              'Owner::resignIndex');       // list
    $routes->get('resign/(:num)',       'Owner::resignShow/$1');     // detail
    $routes->post('resign/(:num)/ok',   'Owner::resignApprove/$1');  // terima
    $routes->post('resign/(:num)/no',   'Owner::resignReject/$1');   // tolak

    // === Pensiun ===
    $routes->get('pensiun',             'Owner::pensiunIndex');      // list
    $routes->get('pensiun/(:num)',      'Owner::pensiunShow/$1');    // detail
    $routes->post('pensiun/(:num)/ok',  'Owner::pensiunApprove/$1'); // terima
    $routes->post('pensiun/(:num)/no',  'Owner::pensiunReject/$1');  // tolak

    // 1. Daftar calon karyawan
    $routes->get('calon-karyawan', 'Owner::calonKaryawanIndex');

    // 2. Detail calon karyawan tertentu
    $routes->get('calon-karyawan/(:num)', 'Owner::calonKaryawanDetail/$1');

    // 3. Promote calon karyawan menjadi karyawan
    //    URL: /owner/calon-karyawan/{id}/promote  (GET)
    $routes->get('calon-karyawan/(:num)/promote', 'Owner::promoteCalon/$1');

    $routes->get('employees/delete/(:num)', 'Owner::employeesDelete/$1');
});

// Group menu KARYAWAN (hanya role 'karyawan')
$routes->group('employee', ['filter' => 'role:karyawan'], static function ($routes) {
    $routes->get('/', 'Employee::index'); // ke dashboard karyawan

    $routes->get('leaves', 'Employee::leavesIndex');      // pengajuan cuti/izin
    // Izin
    $routes->get('leaves/izin/new',  'Employee::izinCreate');
    $routes->post('leaves/izin',     'Employee::izinStore');
    $routes->get('leaves/izin/(:num)', 'Employee::izinShow/$1');

    // Cuti
    $routes->get('leaves/cuti/new',  'Employee::cutiCreate');
    $routes->post('leaves/cuti',     'Employee::cutiStore');
    $routes->get('leaves/cuti/(:num)', 'Employee::cutiShow/$1');

    $routes->get('attendance', 'Employee::attendanceIndex');          // halaman & riwayat
    $routes->post('attendance/checkin', 'Employee::attendanceCheckin');  // absen masuk
    $routes->post('attendance/checkout', 'Employee::attendanceCheckout'); // absen pulang

    // Pengunduran Diri (Resign)
    $routes->get('resign',        'Employee::resignForm');   // form + riwayat
    $routes->post('resign/save',  'Employee::resignSave');

    // Pensiun
    $routes->get('pensiun',       'Employee::pensiunForm');  // form + riwayat
    $routes->post('pensiun/save', 'Employee::pensiunSave');

    // dst...
});
