<?= $this->extend('layout/app') ?>
<?= $this->section('content') ?>
<?php helper('form'); ?>
<h3 class="mb-3"><?= esc($title) ?></h3>

<?php if (session('error')): ?><div class="alert alert-danger"><?= esc(session('error')) ?></div><?php endif; ?>
<?php if ($errs = session('errors')): ?>
    <div class="alert alert-warning">
        <ul class="mb-0"><?php foreach ($errs as $e): ?><li><?= esc($e) ?></li><?php endforeach; ?></ul>
    </div>
<?php endif; ?>

<div class="card">
    <div class="card-body">
        <div class="mb-3">
            <div class="text-muted small">Nama</div>
            <div class="fw-semibold"><?= esc($data['name']) ?> <span class="text-muted">(&lt;<?= esc($data['email']) ?>&gt;)</span></div>
        </div>

        <form action="<?= site_url('owner/employees/' . $data['id'] . '/change-status') ?>" method="post">
            <?= csrf_field() ?>

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Status Baru</label>
                    <select name="status" class="form-select" required>
                        <option value="resign">Resign</option>
                        <option value="pensiun">Pensiun</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Tanggal Keluar <span class="text-danger">*</span></label>
                    <input type="date" name="tanggal_keluar" class="form-control" value="<?= old('tanggal_keluar', $data['tanggal_keluar'] ?? '') ?>" required>
                </div>
            </div>

            <div class="mt-4 d-flex gap-2">
                <a href="<?= previous_url() ?: site_url('owner/employees') ?>" class="btn btn-secondary">Batal</a>
                <button class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>