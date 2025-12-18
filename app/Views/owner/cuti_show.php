<?= $this->extend('layout/app') ?>
<?= $this->section('content') ?>

<h4 class="mb-3">Detail Cuti</h4>

<div class="card mb-3">
    <div class="card-body">
        <div class="mb-2"><strong>Karyawan:</strong> <?= esc($row['name']) ?> <small class="text-muted">(<?= esc($row['email']) ?>)</small></div>
        <div class="mb-2"><strong>Jenis Cuti:</strong> <?= esc(ucfirst($row['jenis'])) ?></div>
        <div class="mb-2"><strong>Periode:</strong> <?= esc($row['tgl_mulai']) ?> s/d <?= esc($row['tgl_selesai']) ?></div>
        <div class="mb-2"><strong>Alasan:</strong><br><?= nl2br(esc($row['alasan'] ?? '-')) ?></div>
        <div class="mb-2">
            <strong>Status:</strong>
            <?php $badge = ['menunggu' => 'warning', 'disetujui' => 'success', 'ditolak' => 'secondary']; ?>
            <span class="badge bg-<?= $badge[$row['status']] ?? 'light' ?>"><?= ucfirst($row['status']) ?></span>
        </div>
    </div>
</div>

<div class="d-flex gap-2">
    <a href="<?= site_url('/owner/cuti') ?>" class="btn btn-outline-secondary">Kembali</a>
    <?php if ($row['status'] === 'menunggu'): ?>
        <form method="post" action="<?= site_url('/owner/cuti/' . $row['id'] . '/ok') ?>">
            <?= csrf_field() ?>
            <button class="btn btn-success" onclick="return confirm('Setujui pengajuan ini?')">Approve</button>
        </form>
        <form method="post" action="<?= site_url('/owner/cuti/' . $row['id'] . '/no') ?>">
            <?= csrf_field() ?>
            <button class="btn btn-danger" onclick="return confirm('Tolak pengajuan ini?')">Reject</button>
        </form>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>