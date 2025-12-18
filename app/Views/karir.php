<?= $this->extend('layout/app') ?>
<?= $this->section('content') ?>

<h3 class="mb-3">Formulir Lamaran Kerja2</h3>

<?php if ($msg = session('success')): ?>
    <div class="alert alert-success"><?= esc($msg) ?></div>
<?php endif; ?>
<?php if ($msg = session('error')): ?>
    <div class="alert alert-danger"><?= esc($msg) ?></div>
<?php endif; ?>

<form action="<?= site_url('karir/submit') ?>" method="post" enctype="multipart/form-data">
    <?= csrf_field() ?>
    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label">Nama Lengkap</label>
            <input type="text" name="nama" class="form-control" required>
        </div>
        <div class="col-md-6">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" required>
        </div>
        <div class="col-md-6">
            <label class="form-label">No. Telepon</label>
            <input type="text" name="no_telp" class="form-control" required>
        </div>
        <div class="col-md-6">
            <label class="form-label">Posisi yang Dilamar</label>
            <input type="text" name="posisi" class="form-control" required>
        </div>
        <div class="col-md-6">
            <label class="form-label">Sumber Lowongan</label>
            <input type="text" name="sumber" class="form-control">
        </div>
        <div class="col-md-6">
            <label class="form-label">Upload CV (pdf/jpg/png, max 4MB)</label>
            <input type="file" name="cv" class="form-control" required>
        </div>
    </div>
    <button type="submit" class="btn btn-primary mt-3">Kirim Lamaran</button>
</form>

<?= $this->endSection() ?>