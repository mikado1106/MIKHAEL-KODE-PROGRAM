<?= $this->extend('layout/app') ?>
<?= $this->section('content') ?>

<h3 class="mb-3">Ajukan Izin / Cuti</h3>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger"><?= esc(session()->getFlashdata('error')) ?></div>
<?php endif; ?>

<div class="card">
    <div class="card-body">
        <form action="<?= site_url('/employee/leaves/store') ?>" method="post">
            <?= csrf_field() ?>

            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Jenis</label>
                    <select name="jenis" class="form-select" required>
                        <option value="izin">Izin</option>
                        <option value="cuti">Cuti</option>
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Tanggal Mulai</label>
                    <input type="date" name="tgl_mulai" class="form-control" required>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Tanggal Selesai</label>
                    <input type="date" name="tgl_selesai" class="form-control" required>
                </div>

                <div class="col-12">
                    <label class="form-label">Alasan</label>
                    <textarea name="alasan" class="form-control" rows="3" placeholder="Tuliskan alasan singkat"></textarea>
                </div>
            </div>

            <div class="mt-3">
                <a href="<?= site_url('/employee/leaves') ?>" class="btn btn-light">Batal</a>
                <button class="btn btn-primary">Kirim Pengajuan</button>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>