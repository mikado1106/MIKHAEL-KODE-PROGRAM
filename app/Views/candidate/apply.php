<?= $this->extend('layout/app') ?>
<?= $this->section('content') ?>

<h3 class="mb-3">Lamaran Kerja</h3>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success"><?= esc(session()->getFlashdata('success')) ?></div>
<?php endif; ?>

<div class="card">
    <div class="card-body">

        <form action="<?= site_url('karir/simpan') ?>" method="post" enctype="multipart/form-data">
            <?= csrf_field() ?>

            <div class="mb-3">
                <label class="form-label">Nama Lengkap</label>
                <input type="text" name="nama" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Nomor HP</label>
                <input type="text" name="no_hp" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">NIK</label>
                <input type="text" name="nik" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Posisi yang Dilamar</label>
                <input type="text" name="posisi" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">CV (PDF / JPG / PNG, maks 4 MB, boleh kosong)</label>
                <input type="file" name="cv" class="form-control">
            </div>

            <button class="btn btn-primary">Kirim Lamaran</button>
        </form>
    </div>
</div>

<?= $this->endSection() ?>