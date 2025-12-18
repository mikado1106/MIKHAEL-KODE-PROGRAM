<?= $this->extend('layout/app') ?>
<?= $this->section('content') ?>
<?php helper('form'); ?>

<h3 class="mb-3">Pengunduran Diri (Resign)</h3>

<?php if (session('success')): ?><div class="alert alert-success"><?= esc(session('success')) ?></div><?php endif; ?>
<?php if (session('error')):   ?><div class="alert alert-danger"><?= esc(session('error')) ?></div><?php endif; ?>

<div class="card mb-4">
    <div class="card-body">
        <form method="post" action="<?= site_url('employee/resign/save') ?>" enctype="multipart/form-data">
            <?= csrf_field() ?>
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Tanggal Efektif <span class="text-danger">*</span></label>
                    <input type="date" name="tanggal_efektif" class="form-control" value="<?= esc(old('tanggal_efektif')) ?>" required>
                </div>
                <div class="col-md-8">
                    <label class="form-label">Alasan (opsional)</label>
                    <textarea name="alasan" rows="3" class="form-control" placeholder="Tulis alasan jika perlu"><?= esc(old('alasan')) ?></textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Dokumen Pendukung (pdf/jpg/png, maks 4MB, opsional)</label>
                    <input type="file" name="dokumen" class="form-control">
                </div>
            </div>
            <div class="mt-3">
                <button class="btn btn-primary">Kirim Pengajuan</button>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header">Riwayat Pengajuan Saya</div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Tgl Pengajuan</th>
                        <th>Tgl Efektif</th>
                        <th>Status</th>
                        <!-- <th>Dokumen</th> -->
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($history)): $no = 1;
                        foreach ($history as $h): ?>
                            <?php
                            $badge = 'bg-warning text-dark';
                            if ($h['status'] === 'disetujui') $badge = 'bg-success';
                            if ($h['status'] === 'ditolak')   $badge = 'bg-danger';
                            ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= esc($h['tanggal_pengajuan']) ?></td>
                                <td><?= esc($h['tanggal_efektif']) ?></td>
                                <td><span class="badge <?= $badge ?>"><?= esc(ucfirst($h['status'])) ?></span></td>
                                <!-- <td>
                                    <?php if (!empty($h['dokumen_path'])): ?>
                                        <a class="btn btn-sm btn-outline-secondary" target="_blank" href="<?= site_url('writable/' . $h['dokumen_path']) ?>">Lihat</a>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td> -->
                            </tr>
                        <?php endforeach;
                    else: ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">Belum ada pengajuan.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= $this->endSection() ?>