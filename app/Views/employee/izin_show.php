<?= $this->extend('layout/app') ?>
<?= $this->section('content') ?>

<h4 class="mb-3">Detail Izin</h4>

<div class="card">
    <div class="card-body">
        <div class="mb-2"><strong>Jenis:</strong> <?= esc(ucfirst($row['jenis'])) ?></div>
        <div class="mb-2"><strong>Periode:</strong> <?= esc($row['tgl_mulai']) ?> s/d <?= esc($row['tgl_selesai']) ?></div>
        <div class="mb-2"><strong>Keterangan:</strong> <?= esc($row['keterangan'] ?? '-') ?></div>
        <?php if (!empty($row['lampiran_path'])): ?>
            <div class="mb-2"><strong>Lampiran:</strong> <a href="<?= base_url($row['lampiran_path']) ?>" target="_blank">Lihat</a></div>
        <?php endif; ?>
        <div class="mb-2">
            <strong>Status:</strong>
            <?php $b = ['menunggu' => 'warning', 'disetujui' => 'success', 'ditolak' => 'secondary']; ?>
            <span class="badge bg-<?= $b[$row['status']] ?? 'light' ?>"><?= ucfirst($row['status']) ?></span>
        </div>
        <a class="btn btn-outline-secondary mt-2" href="<?= site_url('/employee/leaves') ?>">Kembali</a>
    </div>
</div>

<?= $this->endSection() ?>