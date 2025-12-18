<?= $this->extend('layout/app') ?>
<?= $this->section('content') ?>

<h4 class="mb-3"><?= esc($title ?? 'Ajukan Izin') ?></h4>

<?php $errors = session('errors') ?? [];
if ($e = session('error')): ?>
    <div class="alert alert-danger"><?= esc($e) ?></div>
<?php endif; ?>

<div class="card">
    <div class="card-body">
        <form method="post" enctype="multipart/form-data" action="<?= site_url('/employee/leaves/izin') ?>">
            <?= csrf_field() ?>
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Tanggal Mulai</label>
                    <input type="date" name="tgl_mulai" class="form-control <?= isset($errors['tgl_mulai']) ? 'is-invalid' : '' ?>" value="<?= old('tgl_mulai') ?>" required>
                    <div class="invalid-feedback"><?= esc($errors['tgl_mulai'] ?? '') ?></div>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Tanggal Selesai</label>
                    <input type="date" name="tgl_selesai" class="form-control <?= isset($errors['tgl_selesai']) ? 'is-invalid' : '' ?>" value="<?= old('tgl_selesai') ?>" required>
                    <div class="invalid-feedback"><?= esc($errors['tgl_selesai'] ?? '') ?></div>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Jenis Izin</label>
                    <select name="jenis" class="form-select <?= isset($errors['jenis']) ? 'is-invalid' : '' ?>" required>
                        <option value="">- pilih -</option>
                        <option value="sakit" <?= old('jenis') === 'sakit' ? 'selected' : '' ?>>Sakit</option>
                        <option value="pribadi" <?= old('jenis') === 'pribadi' ? 'selected' : '' ?>>Pribadi</option>
                        <option value="izin_lain" <?= old('jenis') === 'izin_lain' ? 'selected' : '' ?>>Lainnya</option>
                    </select>
                    <div class="invalid-feedback"><?= esc($errors['jenis'] ?? '') ?></div>
                </div>
                <div class="col-md-9">
                    <label class="form-label">Keterangan</label>
                    <input type="text" name="keterangan" class="form-control" value="<?= old('keterangan') ?>" placeholder="Opsional">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Lampiran (jpg/png/pdf, maks 2MB)</label>
                    <input type="file" name="lampiran" accept=".png,.jpg,.jpeg,.pdf" class="form-control">
                </div>
            </div>
            <div class="mt-3 d-flex gap-2">
                <a class="btn btn-outline-secondary" href="<?= site_url('/employee/leaves') ?>">Batal</a>
                <button class="btn btn-primary">Kirim Pengajuan</button>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>