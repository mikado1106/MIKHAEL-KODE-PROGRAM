<?= $this->extend('layout/app') ?>
<?= $this->section('content') ?>

<div class="d-flex align-items-center justify-content-between mb-3">
    <h4 class="mb-0"><?= esc($title ?? 'Kelola Karyawan') ?></h4>
    <a href="<?= site_url('/owner/employees/create') ?>" class="btn btn-primary">+ Tambah Karyawan</a>
</div>

<form class="row g-2 mb-3" method="get">
    <div class="col-md-4">
        <input type="text" name="q" class="form-control" placeholder="Cari nama/email..." value="<?= esc($keyword ?? '') ?>">
    </div>
    <div class="col-md-2">
        <select name="pp" class="form-select" onchange="this.form.submit()">
            <?php foreach ([10, 20, 50] as $pp): ?>
                <option value="<?= $pp ?>" <?= (int)($perPage ?? 10) === $pp ? 'selected' : '' ?>><?= $pp ?>/hal</option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-2">
        <button class="btn btn-outline-secondary">Cari</button>
    </div>
</form>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th style="width:60px">ID</th>
                    <th>Nama</th>
                    <th>Email</th>
                    <th style="width:120px">Status</th>
                    <th style="width:220px">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($list)): ?>
                    <?php foreach ($list as $row): ?>
                        <tr>
                            <td><?= esc($row['id']) ?></td>
                            <td><?= esc($row['name']) ?></td>
                            <td><?= esc($row['email']) ?></td>
                            <td>
                                <?php if ((int)$row['is_active'] === 1): ?>
                                    <span class="badge bg-success">Aktif</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Nonaktif</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a class="btn btn-sm btn-outline-primary" href="<?= site_url('/owner/employees/' . $row['id'] . '/edit') ?>">Edit</a>

                                <form action="<?= site_url('/owner/employees/' . $row['id'] . '/toggle') ?>" method="post" class="d-inline">
                                    <?= csrf_field() ?>
                                    <button class="btn btn-sm btn-outline-warning" onclick="return confirm('Ubah status karyawan?')">
                                        <?= (int)$row['is_active'] === 1 ? 'Nonaktifkan' : 'Aktifkan' ?>
                                    </button>
                                </form>

                                <form action="<?= site_url('/owner/employees/' . $row['id'] . '/delete') ?>" method="post" class="d-inline">
                                    <?= csrf_field() ?>
                                    <button class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus karyawan ini?')">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="text-center text-muted">Belum ada data.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="mt-3">
    <?= $pager->links() ?>
</div>

<?= $this->endSection() ?>