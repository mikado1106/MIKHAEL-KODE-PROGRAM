<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<h3 class="mb-4">Dashboard</h3>

<div class="row">
    <!-- Izin -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5>Izin</h5>
            </div>
            <div class="card-body">
                <?php if (!empty($izin)): ?>
                    <ul class="list-group">
                        <?php foreach ($izin as $i): ?>
                            <li class="list-group-item">
                                <?= esc($i['jenis']) ?> - <?= esc($i['status']) ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p class="text-muted">Tidak ada data izin.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Cuti -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5>Cuti</h5>
            </div>
            <div class="card-body">
                <?php if (!empty($cuti)): ?>
                    <ul class="list-group">
                        <?php foreach ($cuti as $c): ?>
                            <li class="list-group-item">
                                <?= esc($c['jenis']) ?> - <?= esc($c['status']) ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p class="text-muted">Tidak ada data cuti.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Absensi -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5>Absensi</h5>
            </div>
            <div class="card-body">
                <?php if (!empty($absen)): ?>
                    <ul class="list-group">
                        <?php foreach ($absen as $a): ?>
                            <li class="list-group-item">
                                <?= esc($a['tanggal']) ?> - <?= esc($a['status']) ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p class="text-muted">Tidak ada data absensi.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>