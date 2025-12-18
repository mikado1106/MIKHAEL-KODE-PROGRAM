<?= $this->extend('layout/app') ?>
<?= $this->section('content') ?>

<h4 class="mb-3"><?= esc($title ?? 'Ganti Password') ?></h4>

<?php if ($msg = session()->getFlashdata('error')): ?>
    <div class="alert alert-danger"><?= esc($msg) ?></div>
<?php endif; ?>
<?php if ($msg = session()->getFlashdata('success')): ?>
    <div class="alert alert-success"><?= esc($msg) ?></div>
<?php endif; ?>

<div class="card">
    <div class="card-body">
        <form method="post" action="<?= site_url('/owner/password') ?>">
            <?= csrf_field() ?>
            <div class="mb-3">
                <label class="form-label">Password Lama</label>
                <input type="password" name="old_password" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Password Baru</label>
                <input type="password" name="new_password" class="form-control" minlength="6" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Konfirmasi Password Baru</label>
                <input type="password" name="confirm_password" class="form-control" minlength="6" required>
            </div>
            <div class="d-flex gap-2">
                <a href="<?= site_url('/owner/profile') ?>" class="btn btn-outline-secondary">Batal</a>
                <button class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>