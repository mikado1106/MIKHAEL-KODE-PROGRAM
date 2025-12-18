<?= $this->extend('layout/app') ?>
<?= $this->section('content') ?>

<h3 class="mb-3">Calon Karyawan</h3>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Posisi</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($rows)): $no = 1;
                    foreach ($rows as $r): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= esc($r['nama']) ?></td>
                            <td><?= esc($r['email']) ?></td>
                            <td><?= esc($r['posisi']) ?></td>
                            <td><?= esc($r['status']) ?></td>

                            <td>
                                <a href="<?= site_url('owner/calon-karyawan/' . $r['id'] . '/promote') ?>" class="btn btn-sm btn-success">Jadikan Karyawan</a>
                            </td>
                        </tr>
                    <?php endforeach;
                else: ?>
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">Belum ada pelamar.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?= $this->endSection() ?>