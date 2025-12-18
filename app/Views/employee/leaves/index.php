<?= $this->extend('layout/app') ?>
<?= $this->section('content') ?>

<h4 class="mb-3"><?= esc($title ?? 'Izin dan Cuti') ?></h4>

<?php if ($msg = session()->getFlashdata('success')): ?>
    <div class="alert alert-success"><?= esc($msg) ?></div>
<?php endif; ?>
<?php if ($msg = session()->getFlashdata('error')): ?>
    <div class="alert alert-danger"><?= esc($msg) ?></div>
<?php endif; ?>

<div class="card">
    <div class="card-body">
        <h5>Daftar Izin dan Cuti</h5>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th>Jenis</th>
                        <th>Mulai</th>
                        <th>Selesai</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($leaves)): foreach ($leaves as $leave): ?>
                            <tr>
                                <td><?= esc($leave['jenis']) ?></td>
                                <td><?= esc($leave['tgl_mulai']) ?></td>
                                <td><?= esc($leave['tgl_selesai']) ?></td>
                                <td><span class="badge <?= $leave['status'] == 'disetujui' ? 'bg-success' : 'bg-warning' ?>"><?= esc($leave['status']) ?></span></td>
                            </tr>
                        <?php endforeach;
                    else: ?>
                        <tr>
                            <td colspan="4" class="text-center text-muted">Belum ada data.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= $this->endSection() ?>