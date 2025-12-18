<?= $this->extend('layout/app') ?>
<?= $this->section('content') ?>

<style>
    .metric {
        border: 0;
        border-radius: 14px;
        box-shadow: 0 6px 20px rgba(16, 24, 40, .06);
        transition: .15s
    }

    .metric:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 24px rgba(16, 24, 40, .1)
    }

    .metric .icon-wrap {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center
    }

    .bg-soft-primary {
        background: #eff4ff;
        color: #1b4ad7
    }

    .bg-soft-success {
        background: #eafaf1;
        color: #117a43
    }

    .bg-soft-warning {
        background: #fff7e6;
        color: #b85c00
    }

    .bg-soft-danger {
        background: #feefef;
        color: #b42318
    }

    .bg-soft-secondary {
        background: #f2f4f7;
        color: #475467
    }

    .value {
        font-size: 34px;
        font-weight: 700
    }

    .mini {
        font-size: 12px;
        color: #667085
    }

    .badge-dot {
        display: inline-flex;
        align-items: center;
        gap: 6px
    }

    .badge-dot::before {
        content: '';
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: currentColor
    }
</style>

<h3 class="mb-4"><?= esc($title ?? 'Dashboard Pemilik') ?></h3>

<div class="row g-3">

    <!-- Ringkasan Karyawan -->
    <div class="col-12 col-md-4">
        <div class="card metric">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <div class="mini mb-1">Total Karyawan</div>
                    <div class="value"><?= esc($total) ?></div>
                </div>
                <div class="icon-wrap bg-soft-primary"><i class="fas fa-users"></i></div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4">
        <div class="card metric">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <div class="mini mb-1">Aktif</div>
                    <div class="value"><?= esc($aktif) ?></div>
                </div>
                <div class="icon-wrap bg-soft-success"><i class="fas fa-user-check"></i></div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4">
        <div class="card metric">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <div class="mini mb-1">Nonaktif</div>
                    <div class="value"><?= esc($nonaktif) ?></div>
                </div>
                <div class="icon-wrap bg-soft-secondary"><i class="fas fa-user-slash"></i></div>
            </div>
        </div>
    </div>
    <!-- Resign & Pensiun -->
    <div class="col-12 col-lg-6">
        <a href="<?= esc($resignListUrl) ?>" class="text-decoration-none text-reset">
            <div class="card metric">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <div class="mini mb-1">Resign</div>
                        <div class="h5 mb-1">Total: <?= esc($resignTotal) ?></div>
                        <div class="mini text-muted">Bulan ini: <?= esc($resignBulanIni) ?></div>
                    </div>
                    <div class="icon-wrap bg-soft-secondary"><i class="fas fa-door-open"></i></div>
                </div>
            </div>
        </a>
    </div>
    <div class="col-12 col-lg-6">
        <a href="<?= esc($pensiunListUrl) ?>" class="text-decoration-none text-reset">
            <div class="card metric">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <div class="mini mb-1">Pensiun</div>
                        <div class="h5 mb-1">Total: <?= esc($pensiunTotal) ?></div>
                        <div class="mini text-muted">Bulan ini: <?= esc($pensiunBulanIni) ?></div>
                    </div>
                    <div class="icon-wrap bg-soft-secondary"><i class="fas fa-user-clock"></i></div>
                </div>
            </div>
        </a>
    </div>


    <!-- Pending Approval -->
    <div class="col-12 col-lg-4">
        <a href="<?= esc($izinUrl) ?>" class="text-decoration-none text-reset">
            <div class="card metric">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <div class="mini mb-1">Pending Izin</div>
                        <div class="value"><?= esc($pendingIzin) ?></div>
                    </div>
                    <div class="icon-wrap bg-soft-warning"><i class="fas fa-hourglass-half"></i></div>
                </div>
            </div>
        </a>
    </div>
    <div class="col-12 col-lg-4">
        <a href="<?= esc($cutiUrl) ?>" class="text-decoration-none text-reset">
            <div class="card metric">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <div class="mini mb-1">Pending Cuti</div>
                        <div class="value"><?= esc($pendingCuti) ?></div>
                    </div>
                    <div class="icon-wrap bg-soft-warning"><i class="far fa-calendar-minus"></i></div>
                </div>
            </div>
        </a>
    </div>
    <div class="col-12 col-lg-4">
        <a href="<?= esc($rekapAbsensiUrl) ?>" class="text-decoration-none text-reset">
            <div class="card metric">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <div class="mini mb-1">Rekap Bulan Ini</div>
                        <div class="h5 mb-0">Lihat & Unduh CSV/PDF</div>
                        <div class="mini">klik untuk membuka</div>
                    </div>
                    <div class="icon-wrap bg-soft-primary"><i class="fas fa-file-download"></i></div>
                </div>
            </div>
        </a>
    </div>

    <!-- Status Hari Ini -->
    <div class="col-12">
        <div class="card metric">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div class="h6 mb-0">Status Hari Ini (<?= esc($today) ?>)</div>
                    <span class="mini">Jam kerja: 09:00–17:00</span>
                </div>
                <div class="row g-3">
                    <div class="col-6 col-md-2">
                        <div class="p-3 rounded bg-soft-success text-center">
                            <div class="mini">Hadir</div>
                            <div class="h3 mb-0"><?= esc($hadirToday) ?></div>
                        </div>
                    </div>
                    <div class="col-6 col-md-2">
                        <div class="p-3 rounded bg-soft-warning text-center">
                            <div class="mini">Terlambat</div>
                            <div class="h3 mb-0"><?= esc($terlambatToday) ?></div>
                        </div>
                    </div>
                    <div class="col-6 col-md-2">
                        <div class="p-3 rounded bg-soft-warning text-center">
                            <div class="mini">Pulang Cepat</div>
                            <div class="h3 mb-0"><?= esc($pulangCepatToday) ?></div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="p-3 rounded bg-soft-primary text-center">
                            <div class="mini">Sedang Cuti/Izin</div>
                            <div class="h3 mb-0"><?= esc($onLeaveToday) ?></div>
                        </div>
                    </div>
                    <div class="col-12 col-md-3">
                        <div class="p-3 rounded bg-soft-danger text-center">
                            <div class="mini">Alpha (Tidak Hadir)</div>
                            <div class="h3 mb-0"><?= esc($alphaToday) ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Check-in terbaru -->
    <div class="col-12 col-xl-6">
        <div class="card metric h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div class="h6 mb-0">Absensi Terbaru</div>
                    <span class="mini">Top 5</span>
                </div>
                <div class="table-responsive">
                    <table class="table table-sm mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Nama</th>
                                <th style="width:140px;">Waktu Masuk</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($latestCheckins)): foreach ($latestCheckins as $r): ?>
                                    <tr>
                                        <td><?= esc($r['nama']) ?></td>
                                        <td><span class="badge bg-light text-dark"><?= esc($r['waktu_masuk']) ?></span></td>
                                    </tr>
                                <?php endforeach;
                            else: ?>
                                <tr>
                                    <td colspan="2" class="text-muted">Belum ada check-in hari ini.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Grafik Tren -->
    <div class="col-12 col-xl-6">
        <div class="card metric h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div class="h6 mb-0">Tren Absensi 14 Hari</div>
                    <span class="mini"><?= esc($trend['range_caption'] ?? '') ?></span>
                </div>
                <canvas id="trendChart" height="95"></canvas>
                <div class="mini mt-2">Hadir = karyawan check-in per hari; Terlambat > 09:00; Pulang Cepat &lt; 17:00.</div>
            </div>
        </div>
    </div>



</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    (function() {
        const L = <?= json_encode($trend['labels'] ?? []) ?>;
        const D1 = <?= json_encode($trend['hadir'] ?? []) ?>;
        const D2 = <?= json_encode($trend['terlambat'] ?? []) ?>;
        const D3 = <?= json_encode($trend['pulangCepat'] ?? []) ?>;

        new Chart(document.getElementById('trendChart').getContext('2d'), {
            type: 'line',
            data: {
                labels: L,
                datasets: [{
                        label: 'Hadir',
                        data: D1,
                        borderWidth: 2,
                        tension: .25,
                        pointRadius: 2
                    },
                    {
                        label: 'Terlambat',
                        data: D2,
                        borderWidth: 2,
                        tension: .25,
                        pointRadius: 2
                    },
                    {
                        label: 'Pulang Cepat',
                        data: D3,
                        borderWidth: 2,
                        tension: .25,
                        pointRadius: 2
                    },
                ]
            },
            options: {
                responsive: true,
                interaction: {
                    mode: 'index',
                    intersect: false
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                }
            }
        });
    })();
</script>

<?= $this->endSection() ?>