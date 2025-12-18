<?= $this->extend('layout/app') ?>
<?= $this->section('content') ?>

<?php
$errors = session('errors') ?? [];
$data   = $data ?? [];
$mode   = $mode ?? 'create';
$action = $mode === 'create'
    ? site_url('owner/employees')
    : site_url('owner/employees/' . $data['id'] . '/update');
?>

<h3 class="mb-3"><?= esc($title ?? 'Form Karyawan') ?></h3>

<?php if (session('success')): ?>
    <div class="alert alert-success"><?= esc(session('success')) ?></div>
<?php endif; ?>
<?php if (session('error')): ?>
    <div class="alert alert-danger"><?= esc(session('error')) ?></div>
<?php endif; ?>
<?php if ($errors): ?>
    <div class="alert alert-warning">
        <ul class="mb-0">
            <?php foreach ($errors as $e): ?><li><?= esc($e) ?></li><?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<form action="<?= $action ?>" method="post" novalidate>
    <?= csrf_field() ?>

    <div class="row g-3">
        <!-- USER -->
        <div class="col-md-6">
            <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
            <input type="text" name="name" class="form-control" value="<?= set_value('name', $data['name'] ?? '') ?>" required>
        </div>
        <div class="col-md-6">
            <label class="form-label">Email <span class="text-danger">*</span></label>
            <input type="email" name="email" class="form-control" value="<?= set_value('email', $data['email'] ?? '') ?>" required>
        </div>

        <div class="col-md-6">
            <label class="form-label">Password <?= $mode === 'create' ? '<span class="text-danger">*</span>' : '<small class="text-muted">(kosongkan jika tidak ganti)</small>' ?></label>
            <input type="password" name="password" class="form-control" <?= $mode === 'create' ? 'required' : '' ?>>
        </div>
        <div class="col-md-6">
            <label class="form-label">Status Akun</label>
            <select name="is_active" class="form-select">
                <option value="1" <?= set_select('is_active', '1', ($data['is_active'] ?? 1) == 1) ?>>Aktif</option>
                <option value="0" <?= set_select('is_active', '0', ($data['is_active'] ?? 1) == 0) ?>>Nonaktif</option>
            </select>
        </div>

        <!-- KARYAWAN -->
        <div class="col-md-4">
            <label class="form-label">NIP</label>
            <input type="text" name="nip" class="form-control" value="<?= set_value('nip', $data['nip'] ?? '') ?>">
        </div>
        <div class="col-md-4">
            <label class="form-label">NIK (16 digit)</label>
            <input type="text" name="nik" maxlength="16" class="form-control" value="<?= set_value('nik', $data['nik'] ?? '') ?>">
        </div>
        <div class="col-md-4">
            <label class="form-label">No. Telp</label>
            <input type="text" name="no_telp" class="form-control" value="<?= set_value('no_telp', $data['no_telp'] ?? '') ?>">
        </div>

        <div class="col-md-6">
            <label class="form-label">Jabatan</label>
            <input type="text" name="jabatan" class="form-control" value="<?= set_value('jabatan', $data['jabatan'] ?? '') ?>">
        </div>
        <div class="col-md-3">
            <label class="form-label">Tanggal Masuk <span class="text-danger">*</span></label>
            <input type="date" name="tanggal_masuk" class="form-control" value="<?= set_value('tanggal_masuk', $data['tanggal_masuk'] ?? date('Y-m-d')) ?>" required>
        </div>
        <?php if ($mode === 'edit'): ?>
            <div class="col-md-3">
                <label class="form-label">Tanggal Keluar</label>
                <input type="date" name="tanggal_keluar" class="form-control" value="<?= set_value('tanggal_keluar', $data['tanggal_keluar'] ?? '') ?>">
            </div>
        <?php endif; ?>

        <div class="col-md-3">
            <label class="form-label">Status Karyawan <span class="text-danger">*</span></label>
            <select name="status" class="form-select" required>
                <?php foreach (['aktif', 'nonaktif', 'resign', 'pensiun'] as $opt): ?>
                    <option value="<?= $opt ?>" <?= set_select('status', $opt, ($data['status'] ?? 'aktif') === $opt) ?>><?= ucfirst($opt) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

    </div>

    <div class="mt-4 d-flex gap-2">
        <a href="<?= site_url('owner/employees') ?>" class="btn btn-secondary">Kembali</a>
        <button class="btn btn-primary"><?= $mode === 'create' ? 'Simpan' : 'Update' ?></button>
    </div>
</form>

<?= $this->endSection() ?>