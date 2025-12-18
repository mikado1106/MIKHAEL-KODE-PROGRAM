<?= $this->extend('layout/app') ?>
<?= $this->section('content') ?>

<div class="d-flex align-items-center justify-content-between mb-3">
    <h4 class="mb-0"><?= esc($title ?? 'Persetujuan Cuti') ?></h4>
</div>

<form class="row g-2 mb-3" method="get">
    <div class="col-md-4">
        <input type="text" name="q" value="<?= esc($q ?? '') ?>" class="form-control" placeholder="Cari nama/email/alasan">
    </div>
    <div class="col-md-3">
        <select name="status" class="form-select" onchange="this.form.submit()">
            <?php $opt = ['' => 'Semua', 'menunggu' => 'Menunggu', 'disetujui' => 'Disetujui', 'ditolak' => 'Ditolak']; ?>
            <?php foreach ($opt as $k => $v): ?>
                <option value="<?= $k ?>" <?= ($status ?? '') === $k ? 'selected' : '' ?>><?= $v ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-2">
        <select name="pp" class="form-select" onchange="this.form.submit()">
            <?php foreach ([10, 20, 50] as $pp): ?>
                <option value="<?= $pp ?>" <?= (int)($perPage ?? 10) === $pp ? 'selected' : '' ?>><?= $pp ?>/hal</option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-2"><button class="btn btn-outline-secondary">Filter</button></div>
</form>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th style="width:80px;">ID</th>
                    <th>Karyawan</th>
                    <th>Jenis Cuti</th>
                    <th>Periode</th>
                    <th>Status</th>
                    <th style="width:130px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($list)): foreach ($list as $r): ?>
                        <tr>
                            <td>#<?= esc($r['id']) ?></td>
                            <td><?= esc($r['name']) ?><br><small class="text-muted"><?= esc($r['email']) ?></small></td>
                            <td class="text-capitalize"><?= esc($r['jenis']) ?></td>
                            <td><?= esc($r['tgl_mulai']) ?> → <?= esc($r['tgl_selesai']) ?></td>
                            <td>
                                <?php $badge = ['menunggu' => 'warning', 'disetujui' => 'success', 'ditolak' => 'secondary']; ?>
                                <span class="badge bg-<?= $badge[$r['status']] ?? 'light' ?>"><?= ucfirst($r['status']) ?></span>
                            </td>
                            <td><a class="btn btn-sm btn-outline-primary" href="<?= site_url('/owner/cuti/' . $r['id']) ?>">Detail</a></td>
                        </tr>
                    <?php endforeach;
                else: ?>
                    <tr>
                        <td colspan="6" class="text-center text-muted">Belum ada pengajuan.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="mt-3"><?= $pager->links() ?></div>

<?= $this->endSection() ?>