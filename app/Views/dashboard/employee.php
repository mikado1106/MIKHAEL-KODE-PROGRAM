<?= $this->extend('layout/app') ?>
<?= $this->section('content') ?>

<style>
    /* Sedikit sentuhan gaya untuk dashboard karyawan */
    .emp-hero-card {
        border-radius: 1rem;
        background: linear-gradient(135deg, #2563eb, #06b6d4);
        color: #fff;
        box-shadow: 0 10px 30px rgba(15, 23, 42, 0.25);
    }

    .emp-hero-card h3 {
        font-weight: 600;
    }

    .emp-stat-card {
        border-radius: 0.9rem;
        border: 1px solid #e5e7eb;
        transition: all .15s ease-in-out;
    }

    .emp-stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(15, 23, 42, 0.12);
    }

    .emp-menu-card {
        border-radius: 1rem;
        border: 1px solid #e5e7eb;
        transition: all .18s ease-in-out;
        cursor: pointer;
        height: 100%;
    }

    .emp-menu-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 30px rgba(15, 23, 42, 0.18);
        border-color: #2563eb;
    }

    .emp-menu-card .emp-menu-icon {
        width: 40px;
        height: 40px;
        border-radius: 999px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: #eff6ff;
        color: #2563eb;
        font-size: 1.25rem;
    }

    @media (max-width: 767.98px) {
        .emp-hero-card {
            text-align: center;
        }
    }
</style>

<div class="container-fluid px-0">

    <!-- Header / Hero -->
    <div class="emp-hero-card p-4 p-md-5 mb-4">
        <div class="row g-3 align-items-center">
            <div class="col-md-8">
                <div class="mb-1 small text-white-50">Dashboard Karyawan</div>
                <h3 class="mb-2">
                    Selamat Datang 👋
                </h3>
                <p class="mb-0 text-white-75">
                    Jangan lupa untuk melakukan absen tepat waktu.
                </p>
            </div>
            <div class="col-md-4 text-md-end">
                <div class="small text-white-75">Hari ini</div>
                <div class="fw-semibold fs-5">
                    <?= esc(date('l, d M Y')) ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Ringkasan singkat -->
    <!-- <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="emp-stat-card p-3">
                <div class="small text-muted mb-1">Izin Tahun Ini</div>
                <div class="fw-semibold fs-4">
                    <?= esc($totalIzin ?? 0) ?>
                </div>
                <div class="small text-muted">pengajuan</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="emp-stat-card p-3">
                <div class="small text-muted mb-1">Cuti Tahun Ini</div>
                <div class="fw-semibold fs-4">
                    <?= esc($totalCuti ?? 0) ?>
                </div>
                <div class="small text-muted">hari</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="emp-stat-card p-3">
                <div class="small text-muted mb-1">Kehadiran Bulan Ini</div>
                <div class="fw-semibold fs-4">
                    <?= esc($hadirBulanIni ?? 0) ?>
                </div>
                <div class="small text-muted">hari hadir</div>
            </div>
        </div> -->
</div>

<!-- Menu utama -->
<div class="row g-3">
    <!-- Izin / Cuti -->
    <div class="col-md-4">
        <a href="<?= site_url('/employee/leaves') ?>" class="text-decoration-none text-reset">
            <div class="emp-menu-card p-4">
                <div class="d-flex align-items-start gap-3">
                    <div class="emp-menu-icon">
                        <i class="bi bi-calendar-check"></i>
                    </div>
                    <div>
                        <h5 class="mb-1">Izin &amp; Cuti</h5>
                        <p class="mb-2 text-muted small">
                            Ajukan izin atau cuti, serta pantau status persetujuan dari pemilik.
                        </p>
                        <span class="small fw-semibold text-primary">
                            Buat pengajuan baru →
                        </span>
                    </div>
                </div>
            </div>
        </a>
    </div>


    <!-- Absensi -->
    <div class="col-md-4">
        <a href="<?= site_url('/employee/attendance') ?>" class="text-decoration-none text-reset">
            <div class="emp-menu-card p-4">
                <div class="d-flex align-items-start gap-3">
                    <div class="emp-menu-icon">
                        <i class="bi bi-clock-history"></i>
                    </div>
                    <div>
                        <h5 class="mb-1">Absensi</h5>
                        <p class="mb-2 text-muted small">
                            Lakukan absen masuk dan pulang, serta lihat riwayat kehadiran Anda.
                        </p>
                        <span class="small fw-semibold text-primary">
                            Lihat riwayat &amp; status →
                        </span>
                    </div>
                </div>
            </div>
        </a>
    </div>

    <!-- Pengunduran Diri & Pensiun -->
    <div class="col-md-4">
        <a href="<?= site_url('/employee/exits/resign') ?>" class="text-decoration-none text-reset">
            <div class="emp-menu-card p-4">
                <div class="d-flex align-items-start gap-3">
                    <div class="emp-menu-icon">
                        <i class="bi bi-door-open"></i>
                    </div>
                    <div>
                        <h5 class="mb-1">Pengunduran Diri &amp; Pensiun</h5>
                        <p class="mb-2 text-muted small">
                            Ajukan pengunduran diri atau pensiun dan pantau proses persetujuan.
                        </p>
                        <span class="small fw-semibold text-primary">
                            Buat pengajuan baru →
                        </span>
                    </div>
                </div>
            </div>
        </a>
    </div>
    <div class="col-6 col-md-3">
        <div class="emp-stat-card p-3">
            <div class="small text-muted mb-1">Status Karyawan</div>
            <div class="fw-semibold fs-5 text-success">
                <?= esc($statusKaryawan ?? 'Aktif') ?>
            </div>
        </div>
    </div>
</div>

</div>

<?= $this->endSection() ?>