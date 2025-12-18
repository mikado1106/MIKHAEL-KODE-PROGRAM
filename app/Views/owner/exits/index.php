<?= $this->extend('layout/app') ?>
<?= $this->section('content') ?>

<h3 class="mb-3"><?= esc($title ?? 'Pengajuan') ?></h3>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Jabatan</th>
                        <th>Tgl Pengajuan</th>
                        <th>Tgl Efektif</th>
                        <th>Status</th>
                        <th style="width:120px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($rows)): $no = 1;
                        foreach ($rows as $r): ?>
                            <?php
                            $badge = 'bg-warning text-dark';
                            if (($r['status'] ?? '') === 'disetujui') $badge = 'bg-success';
                            if (($r['status'] ?? '') === 'ditolak')   $badge = 'bg-danger';
                            ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td class="fw-medium"><?= esc($r['nama']) ?></td>
                                <td class="small text-muted"><?= esc($r['email']) ?></td>
                                <td><?= esc($r['jabatan'] ?? '-') ?></td>
                                <td><?= esc($r['tgl_pengajuan']) ?></td>
                                <td><?= esc($r['tanggal_efektif']) ?></td>
                                <td><span class="badge <?= $badge ?>"><?= esc(ucfirst($r['status'])) ?></span></td>
                                <td>
                                    <a class="btn btn-sm btn-outline-primary"
                                        href="<?= site_url('owner/' . ($tipe ?? 'resign') . '/' . $r['id']) ?>">
                                        Detail
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach;
                    else: ?>
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">Belum ada pengajuan.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= $this->endSection() ?>