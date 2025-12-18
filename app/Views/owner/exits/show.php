<?= $this->extend('layout/app') ?>
<?= $this->section('content') ?>
<?php helper('form'); ?>

<h3 class="mb-3"><?= esc($title ?? 'Detail Pengajuan') ?></h3>

<?php if (session('success')): ?><div class="alert alert-success"><?= esc(session('success')) ?></div><?php endif; ?>
<?php if (session('error')):   ?><div class="alert alert-danger"><?= esc(session('error')) ?></div><?php endif; ?>

<div class="card mb-3">
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-6">
                <div class="text-muted small mb-1">Nama</div>
                <div class="fw-semibold"><?= esc($data['name']) ?> <span class="text-muted">(&lt;<?= esc($data['email']) ?>&gt;)</span></div>
            </div>
            <div class="col-md-3">
                <div class="text-muted small mb-1">Jabatan</div>
                <div><?= esc($data['jabatan'] ?? '-') ?></div>
            </div>
            <div class="col-md-3">
                <?php
                $badge = 'bg-warning text-dark';
                if (($data['status'] ?? '') === 'disetujui') $badge = 'bg-success';
                if (($data['status'] ?? '') === 'ditolak')   $badge = 'bg-danger';
                ?>
                <div class="text-muted small mb-1">Status Pengajuan</div>
                <div><span class="badge <?= $badge ?>"><?= esc(ucfirst($data['status'])) ?></span></div>
            </div>

            <div class="col-md-3">
                <div class="text-muted small mb-1">Tgl Pengajuan</div>
                <div><?= esc($data['tgl_pengajuan']) ?></div>
            </div>
            <div class="col-md-3">
                <div class="text-muted small mb-1">Tgl Efektif</div>
                <div><?= esc($data['tanggal_efektif']) ?></div>
            </div>

            <?php if (!empty($data['alasan'])): ?>
                <div class="col-12">
                    <div class="text-muted small mb-1">Alasan</div>
                    <div class="border rounded p-2 bg-light" style="min-height:60px;"><?= nl2br(esc($data['alasan'])) ?></div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php if (($data['status'] ?? '') === 'menunggu'): ?>
    <div class="card">
        <div class="card-body">
            <form method="post" action="<?= site_url('owner/' . ($tipe ?? 'resign') . '/' . $data['id'] . '/ok') ?>" class="d-inline">
                <?= csrf_field() ?>
                <button class="btn btn-success me-2">Terima</button>
            </form>
            <form method="post" action="<?= site_url('owner/' . ($tipe ?? 'resign') . '/' . $data['id'] . '/no') ?>" class="d-inline">
                <?= csrf_field() ?>
                <button class="btn btn-outline-danger">Tolak</button>
            </form>
        </div>
    </div>
<?php else: ?>
    <div class="alert alert-info mt-3">
        Pengajuan <strong><?= esc($data['status']) ?></strong>.
    </div>
<?php endif; ?>

<a href="<?= site_url('owner/' . ($tipe ?? 'resign')) ?>" class="btn btn-secondary mt-3">Kembali</a>

<?= $this->endSection() ?>