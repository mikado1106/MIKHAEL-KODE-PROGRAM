<?= $this->extend('layout/app') ?>
<?= $this->section('content') ?>

<h3 class="mb-3"><?= esc($title) ?></h3>

<div class="mb-3 d-flex gap-2">
    <a href="<?= site_url('owner/employees') ?>" class="btn btn-secondary">Kembali</a>
    <a href="<?= site_url('owner/employees/export/csv') ?>" class="btn btn-outline-secondary">
        <i class="fas fa-file-csv me-1"></i> Export CSV
    </a>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped align-middle">
                <thead class="table-light">
                    <tr>
                        <th style="width:50px;">#</th>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Jabatan</th>
                        <th>Tgl Masuk</th>
                        <th>Tgl Keluar</th>
                        <th>Status</th>
                        <th style="width:120px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1;
                    foreach ($rows as $r): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= esc($r['name']) ?></td>
                            <td class="small text-muted"><?= esc($r['email']) ?></td>
                            <td><?= esc($r['jabatan'] ?? '-') ?></td>
                            <td><?= esc($r['tanggal_masuk'] ?? '-') ?></td>
                            <td><?= esc($r['tanggal_keluar'] ?? '-') ?></td>
                            <td><span class="badge bg-secondary"><?= esc(ucfirst($r['status'])) ?></span></td>
                            <td>
                                <a href="<?= site_url('owner/employees/' . $r['id'] . '/change-status') ?>"
                                    class="btn btn-sm btn-outline-primary">Ubah Status</a>
                            </td>
                        </tr>
                    <?php endforeach;
                    if (empty($rows)): ?>
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">Belum ada data.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= $this->endSection() ?>