<?= $this->extend('layout/app') ?>
<?= $this->section('content') ?>

<h4 class="mb-3"><?= esc($title ?? 'Ajukan Cuti') ?></h4>

<?php $errors = session('errors') ?? [];
if ($e = session('error')): ?>
    <div class="alert alert-danger"><?= esc($e) ?></div>
<?php endif; ?>

<div class="card">
    <div class="card-body">
        <form method="post" action="<?= site_url('/employee/leaves/cuti') ?>">
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
                    <label class="form-label">Jenis Cuti</label>
                    <select name="jenis" class="form-select <?= isset($errors['jenis']) ? 'is-invalid' : '' ?>" required>
                        <option value="">- pilih -</option>
                        <option value="tahunan" <?= old('jenis') === 'tahunan' ? 'selected' : '' ?>>Tahunan</option>
                        <option value="melahirkan" <?= old('jenis') === 'melahirkan' ? 'selected' : '' ?>>Melahirkan</option>
                        <option value="besar" <?= old('jenis') === 'besar' ? 'selected' : '' ?>>Besar</option>
                        <option value="cuti_lain" <?= old('jenis') === 'cuti_lain' ? 'selected' : '' ?>>Lainnya</option>
                    </select>
                    <div class="invalid-feedback"><?= esc($errors['jenis'] ?? '') ?></div>
                </div>
                <div class="col-md-9">
                    <label class="form-label">Alasan</label>
                    <input type="text" name="alasan" class="form-control <?= isset($errors['alasan']) ? 'is-invalid' : '' ?>" value="<?= old('alasan') ?>" required>
                    <div class="invalid-feedback"><?= esc($errors['alasan'] ?? '') ?></div>
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