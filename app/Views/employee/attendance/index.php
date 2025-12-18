<?= $this->extend('layout/app') ?>
<?= $this->section('content') ?>

<h4 class="mb-3"><?= esc($title ?? 'Absensi') ?></h4>

<?php if ($msg = session()->getFlashdata('success')): ?>
    <div class="alert alert-success"><?= esc($msg) ?></div>
<?php endif; ?>
<?php if ($msg = session()->getFlashdata('error')): ?>
    <div class="alert alert-danger"><?= esc($msg) ?></div>
<?php endif; ?>

<div class="card mb-3">
    <div class="card-body d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <div><strong>Hari ini:</strong> <?= esc($today) ?></div>
            <?php if ($todayRow): ?>
                <div class="text-muted">
                    Masuk: <?= esc($todayRow['waktu_masuk'] ?? '-') ?>,
                    Pulang: <?= esc($todayRow['waktu_keluar'] ?? '-') ?>
                </div>
            <?php else: ?>
                <div class="text-muted">Belum ada absensi.</div>
            <?php endif; ?>
        </div>

        <div class="d-flex gap-2">
            <?php if (!$todayRow): ?>
                <form method="post" action="<?= site_url('employee/attendance/checkin') ?>" class="d-flex gap-2">
                    <?= csrf_field() ?>
                    <input type="text" name="catatan" class="form-control" placeholder="Catatan (opsional)" style="max-width:260px">
                    <button class="btn btn-primary">Absen Masuk</button>
                </form>
            <?php elseif (empty($todayRow['waktu_keluar'])): ?>
                <form method="post" action="<?= site_url('employee/attendance/checkout') ?>">
                    <?= csrf_field() ?>
                    <button class="btn btn-success">Absen Pulang</button>
                </form>
            <?php else: ?>
                <span class="badge bg-success">Absensi hari ini sudah lengkap</span>
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">Riwayat 30 Hari</div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>Tanggal</th>
                    <th>Masuk</th>
                    <th>Pulang</th>
                    <th>Durasi</th>
                    <th>Catatan</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($rows)): foreach ($rows as $r): ?>
                        <tr>
                            <td><?= esc($r['tanggal']) ?></td>
                            <td><?= esc($r['waktu_masuk'] ?? '-') ?></td>
                            <td><?= esc($r['waktu_keluar'] ?? '-') ?></td>
                            <td><?= esc($r['durasi'] ?? '-') ?></td>
                            <td><small class="text-muted"><?= esc($r['catatan'] ?? '-') ?></small></td>
                        </tr>
                    <?php endforeach;
                else: ?>
                    <tr>
                        <td colspan="5" class="text-center text-muted">Belum ada data.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?= $this->endSection() ?>