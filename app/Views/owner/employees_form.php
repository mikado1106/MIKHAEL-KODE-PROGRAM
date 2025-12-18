<?= $this->extend('layout/app') ?>
<?= $this->section('content') ?>

<h4 class="mb-3"><?= esc($title ?? 'Form Karyawan') ?></h4>

<?php if (session('error')): ?>
    <div class="alert alert-danger"><?= esc(session('error')) ?></div>
<?php endif; ?>

<form method="post" action="<?php
                            if (($mode ?? 'create') === 'edit') {
                                echo site_url('/owner/employees/' . ($row['id']) . '/update');
                            } else {
                                echo site_url('/owner/employees');
                            }
                            ?>">
    <?= csrf_field() ?>

    <div class="mb-3">
        <label class="form-label">Nama</label>
        <input type="text" name="name" class="form-control" value="<?= esc($row['name'] ?? '') ?>" required>
    </div>

    <div class="mb-3">
        <label class="form-label">Email</label>
        <input type="email" name="email" class="form-control" value="<?= esc($row['email'] ?? '') ?>" required>
    </div>

    <div class="mb-3">
        <label class="form-label">
            Password
            <?php if (($mode ?? '') === 'edit'): ?>
                <small class="text-muted">(kosongkan jika tidak diubah)</small>
            <?php endif; ?>
        </label>
        <input type="text" name="password" class="form-control" value="">
    </div>

    <div class="mb-3">
        <label class="form-label">Status</label>
        <select name="is_active" class="form-select">
            <option value="1" <?= (int)($row['is_active'] ?? 1) === 1 ? 'selected' : '' ?>>Aktif</option>
            <option value="0" <?= (int)($row['is_active'] ?? 1) === 0 ? 'selected' : '' ?>>Nonaktif</option>
        </select>
    </div>

    <div class="d-flex gap-2">
        <a href="<?= site_url('/owner/employees') ?>" class="btn btn-outline-secondary">Kembali</a>
        <button class="btn btn-primary">Simpan</button>
    </div>
</form>

<?= $this->endSection() ?>