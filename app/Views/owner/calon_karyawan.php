<?= $this->extend('layout/app') ?>
<?= $this->section('content') ?>

<h3 class="mb-3">Daftar Calon Karyawan</h3>

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
                    <th>Tgl Lamaran</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($calonKaryawan as $index => $calon): ?>
                    <tr>
                        <td><?= esc($index + 1) ?></td>
                        <td><?= esc($calon['nama']) ?></td>
                        <td><?= esc($calon['email']) ?></td>
                        <td><?= esc($calon['posisi']) ?></td>
                        <td><?= esc(ucfirst($calon['status'])) ?></td>
                        <td><?= esc($calon['created_at']) ?></td>
                        <td>
                            <?php if ($calon['status'] != 'diterima'): ?>
                                <a href="<?= site_url('owner/calon-karyawan/' . $calon['id'] . '/convert') ?>" class="btn btn-sm btn-success">Jadikan Karyawan</a>
                            <?php else: ?>
                                <span class="badge bg-success">Sudah Karyawan</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?= $this->endSection() ?>