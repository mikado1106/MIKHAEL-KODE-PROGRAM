<?= $this->extend('layout/app') ?>
<?= $this->section('content') ?>

<h4 class="mb-3">Detail Cuti</h4>

<div class="card">
    <div class="card-body">
        <div class="mb-2"><strong>Jenis:</strong> <?= esc(ucfirst($row['jenis'])) ?></div>
        <div class="mb-2"><strong>Periode:</strong> <?= esc($row['tgl_mulai']) ?> s/d <?= esc($row['tgl_selesai']) ?></div>
        <div class="mb-2"><strong>Alasan:</strong> <?= esc($row['alasan'] ?? '-') ?></div>
        <div class="mb-2">
            <strong>Status:</strong>
            <?php $b = ['menunggu' => 'warning', 'disetujui' => 'success', 'ditolak' => 'secondary']; ?>
            <span class="badge bg-<?= $b[$row['status']] ?? 'light' ?>"><?= ucfirst($row['status']) ?></span>
        </div>
        <a class="btn btn-outline-secondary mt-2" href="<?= site_url('/employee/leaves') ?>">Kembali</a>
    </div>
</div>

<?= $this->endSection() ?>