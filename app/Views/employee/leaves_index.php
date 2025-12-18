<?= $this->extend('layout/app') ?>
<?= $this->section('content') ?>

<h4 class="mb-3"><?= esc($title ?? 'Pengajuan Izin & Cuti') ?></h4>

<div class="mb-3 d-flex gap-2">
    <a class="btn btn-primary" href="<?= site_url('/employee/leaves/izin/new') ?>">+ Ajukan Izin</a>
    <a class="btn btn-outline-primary" href="<?= site_url('/employee/leaves/cuti/new') ?>">+ Ajukan Cuti</a>
</div>

<?php if ($msg = session()->getFlashdata('success')): ?>
    <div class="alert alert-success"><?= esc($msg) ?></div>
<?php endif; ?>
<?php if ($msg = session()->getFlashdata('error')): ?>
    <div class="alert alert-danger"><?= esc($msg) ?></div>
<?php endif; ?>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>Jenis</th>
                    <th>Periode</th>
                    <th>Status</th>
                    <th>Tgl Pengajuan</th>
                    <th style="width:120px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($rows)): foreach ($rows as $r): ?>
                        <tr>
                            <td class="text-capitalize"><?= esc($r['tipe']) ?> (<?= esc($r['jenis']) ?>)</td>
                            <td><?= esc($r['tgl_mulai']) ?> → <?= esc($r['tgl_selesai']) ?></td>
                            <td>
                                <?php $b = ['menunggu' => 'warning', 'disetujui' => 'success', 'ditolak' => 'secondary']; ?>
                                <span class="badge bg-<?= $b[$r['status']] ?? 'light' ?>"><?= ucfirst($r['status']) ?></span>
                            </td>
                            <td><small class="text-muted"><?= esc($r['tgl_pengajuan']) ?></small></td>
                            <td>
                                <?php if ($r['tipe'] === 'izin'): ?>
                                    <a class="btn btn-sm btn-outline-primary" href="<?= site_url('/employee/leaves/izin/' . $r['id']) ?>">Detail</a>
                                <?php else: ?>
                                    <a class="btn btn-sm btn-outline-primary" href="<?= site_url('/employee/leaves/cuti/' . $r['id']) ?>">Detail</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach;
                else: ?>
                    <tr>
                        <td colspan="5" class="text-center text-muted">Belum ada pengajuan.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?= $this->endSection() ?>