<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title><?= esc($title ?? 'App') ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f7f9fc;
        }

        .nav-role-badge {
            font-size: .85rem;
        }

        .card-quick {
            transition: .2s;
            cursor: pointer;
        }

        .card-quick:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 24px rgba(0, 0, 0, .08);
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="<?= site_url('/') ?>">HR PT Papande Jaya Teknik</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navHR">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navHR">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <?php $role = session('role'); ?>
                    <?php if ($role === 'pemilik'): ?>
                        <li class="nav-item"><a class="nav-link" href="<?= site_url('/owner') ?>">Dashboard</a></li>
                        <li class="nav-item"><a class="nav-link" href="<?= site_url('/owner/employees') ?>">Karyawan</a></li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">Laporan</a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="<?= site_url('/owner/reports') ?>">Ringkas</a></li>
                                <li><a class="dropdown-item" href="<?= site_url('/owner/reports/rekap') ?>">Rekap Izin/Cuti</a></li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li><a class="dropdown-item" href="<?= site_url('/owner/attendance/rekap') ?>">Rekap Absensi</a></li> <!-- ⬅️ baru -->
                            </ul>
                        <li class="nav-item"><a class="nav-link" href="<?= site_url('owner/resign') ?>">
                                <i class="fas fa-door-open me-1"></i> Pengajuan Resign
                            </a></li>
                        <li class="nav-item"><a class="nav-link" href="<?= site_url('owner/pensiun') ?>">
                                <i class="fas fa-user-clock me-1"></i> Pengajuan Pensiun
                            </a></li>

                    <?php elseif ($role === 'karyawan'): ?>
                        <li class="nav-item"><a class="nav-link" href="<?= site_url('/employee') ?>">Beranda</a></li>
                        <li class="nav-item"><a class="nav-link" href="<?= site_url('/employee/leaves') ?>">Izin/Cuti</a></li>
                        <li class="nav-item"><a class="nav-link" href="<?= site_url('/employee/attendance') ?>">Absensi</a></li>
                    <?php endif; ?>
                    <?php if (session('role') === 'karyawan'): ?>
                        <li class="nav-item"><a class="nav-link" href="<?= site_url('employee/resign') ?>">
                                <i class="fas fa-door-open me-1"></i> Pengunduran Diri
                            </a></li>
                        <li class="nav-item"><a class="nav-link" href="<?= site_url('employee/pensiun') ?>">
                                <i class="fas fa-user-clock me-1"></i> Pensiun
                            </a></li>
                    <?php endif; ?>

                </ul>
                <ul class="navbar-nav ms-auto">
                    <?php if (session('logged_in')): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                                <?= esc(session('name')) ?> <span class="badge bg-info text-dark ms-1"><?= esc(session('role')) ?></span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <?php if (session('role') === 'pemilik'): ?>
                                    <li><a class="dropdown-item" href="<?= site_url('/owner/profile') ?>">Profil</a></li>
                                    <li><a class="dropdown-item" href="<?= site_url('/owner/password') ?>">Ganti Password</a></li>
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
                                <?php endif; ?>
                                <li><a class="dropdown-item text-danger" href="<?= site_url('/logout') ?>">Logout</a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item"><a class="nav-link" href="<?= site_url('/login') ?>">Login</a></li>
                    <?php endif; ?>
                </ul>



            </div>
        </div>
    </nav>

    <div class="container my-4">
        <?php if (session('success')): ?>
            <div class="alert alert-success"><?= esc(session('success')) ?></div>
        <?php endif; ?>
        <?php if (session('error')): ?>
            <div class="alert alert-danger"><?= esc(session('error')) ?></div>
        <?php endif; ?>

        <?= $this->renderSection('content') ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>