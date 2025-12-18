<?= $this->extend('layout/app') ?>
<?= $this->section('content') ?>

<h3 class="mb-3">Pengunduran Diri (Resign)</h3>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success"><?= esc(session()->getFlashdata('success')) ?></div>
<?php endif; ?>
<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger"><?= esc(session()->getFlashdata('error')) ?></div>
<?php endif; ?>

<div class="card">
    <div class="card-body">
        <form action="<?= site_url('/employee/exit/resign/submit') ?>" method="post" enctype="multipart/form-data">
            <?= csrf_field() ?>

            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Tanggal Efektif</label>
                    <input type="date" name="tanggal_efektif" class="form-control" required>
                </div>
                <div class="col-md-8">
                    <label class="form-label">Alasan</label>
                    <input type="text" name="alasan" class="form-control" placeholder="Alasan pengunduran diri" required>
                </div>
                <div class="col-12">
                    <label class="form-label">Dokumen (opsional)</label>
                    <input type="file" name="dokumen" class="form-control">
                </div>
            </div>

            <div class="mt-3">
                <a href="<?= site_url('/employee') ?>" class="btn btn-light">Batal</a>
                <button class="btn btn-danger">Kirim Pengajuan Resign</button>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>