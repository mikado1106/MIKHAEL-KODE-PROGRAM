<?= $this->extend('layout/app') ?>
<?= $this->section('content') ?>

<h4 class="mb-3"><?= esc($title ?? 'Laporan') ?></h4>

<div class="row g-3">
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-header">Ringkas Izin</div>
            <div class="card-body">
                <ul class="list-group">
                    <li class="list-group-item d-flex justify-content-between">
                        Menunggu <span class="badge bg-warning text-dark"><?= esc($izin['menunggu'] ?? 0) ?></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        Disetujui <span class="badge bg-success"><?= esc($izin['disetujui'] ?? 0) ?></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        Ditolak <span class="badge bg-secondary"><?= esc($izin['ditolak'] ?? 0) ?></span>
                    </li>
                </ul>
                <div class="mt-3">
                    <a class="btn btn-sm btn-outline-primary" href="<?= site_url('/owner/izin?status=menunggu') ?>">Lihat Izin (Menunggu)</a>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-header">Ringkas Cuti</div>
            <div class="card-body">
                <ul class="list-group">
                    <li class="list-group-item d-flex justify-content-between">
                        Menunggu <span class="badge bg-warning text-dark"><?= esc($cuti['menunggu'] ?? 0) ?></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        Disetujui <span class="badge bg-success"><?= esc($cuti['disetujui'] ?? 0) ?></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        Ditolak <span class="badge bg-secondary"><?= esc($cuti['ditolak'] ?? 0) ?></span>
                    </li>
                </ul>
                <div class="mt-3">
                    <a class="btn btn-sm btn-outline-primary" href="<?= site_url('/owner/cuti?status=menunggu') ?>">Lihat Cuti (Menunggu)</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>