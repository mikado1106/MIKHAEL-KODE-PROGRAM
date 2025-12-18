<?= $this->extend('layout/app') ?>
<?= $this->section('content') ?>

<style>
    /* kartu ringkasan */
    .metric {
        border: 0;
        border-radius: 14px;
        box-shadow: 0 6px 20px rgba(16, 24, 40, .06);
        transition: .15s
    }

    .metric:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 24px rgba(16, 24, 40, .1)
    }

    .icon-wrap {
        width: 42px;
        height: 42px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center
    }

    .bg-soft-primary {
        background: #eff4ff;
        color: #1b4ad7
    }

    .bg-soft-success {
        background: #eafaf1;
        color: #117a43
    }

    .bg-soft-secondary {
        background: #f2f4f7;
        color: #475467
    }

    .value {
        font-size: 28px;
        font-weight: 700
    }

    .table thead th {
        position: sticky;
        top: 0;
        background: #f8fafc;
        z-index: 1
    }

    .badge-status {
        font-weight: 600
    }
</style>

<h3 class="mb-3"><?= esc($title ?? 'Data Karyawan') ?></h3>

<!-- Ringkasan -->
<div class="row g-3 mb-3">
    <div class="col-12 col-md-4">
        <div class="card metric">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small">Total Karyawan</div>
                    <div class="value"><?= esc($summary['total'] ?? 0) ?></div>
                </div>
                <div class="icon-wrap bg-soft-primary"><i class="fas fa-users"></i></div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4">
        <div class="card metric">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small">Aktif</div>
                    <div class="value"><?= esc($summary['aktif'] ?? 0) ?></div>
                </div>
                <div class="icon-wrap bg-soft-success"><i class="fas fa-user-check"></i></div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4">
        <div class="card metric">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small">Nonaktif</div>
                    <div class="value"><?= esc($summary['nonaktif'] ?? 0) ?></div>
                </div>
                <div class="icon-wrap bg-soft-secondary"><i class="fas fa-user-slash"></i></div>
            </div>
        </div>
    </div>
</div>

<!-- Toolbar -->
<div class="d-flex flex-wrap gap-2 align-items-center mb-3">
    <a href="<?= site_url('owner/employees/create') ?>" class="btn btn-primary">
        <i class="fas fa-plus me-1"></i> Tambah Karyawan
    </a>

    <a href="<?= site_url('owner/calon-karyawan') ?>" class="btn btn-outline-primary">+ Tambah Karyawan Dari Calon Karyawan</a>

    <a href="<?= site_url('owner/employees/export/csv') ?>" class="btn btn-outline-secondary">
        <i class="fas fa-file-csv me-1"></i> Export CSV
    </a>
    <div class="ms-auto">
        <input id="tblSearch" type="text" class="form-control" placeholder="Cari nama, email, jabatan…">
    </div>
</div>


<!-- Tabel -->
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover table-striped align-middle" id="tblKaryawan">
                <thead>
                    <tr>
                        <th style="width:50px;">#</th>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>NIP</th>
                        <th>NIK</th>
                        <th>Jabatan</th>
                        <th>No. Telp</th>
                        <th>Tgl Masuk</th>
                        <th>Status</th>
                        <th>Aksi</th>
                <tbody>
                    <?php $no = 1;
                    foreach ($rows as $r): ?>
                        <?php
                        $st = strtolower($r['status'] ?? 'aktif');
                        $badge = 'bg-success';
                        if ($st === 'nonaktif') $badge = 'bg-secondary';
                        if ($st === 'resign')   $badge = 'bg-danger';
                        if ($st === 'pensiun')  $badge = 'bg-warning text-dark';
                        ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td class="fw-medium"><?= esc($r['name']) ?></td>
                            <td class="small text-muted"><?= esc($r['email']) ?></td>
                            <td><?= esc($r['nip'] ?? '-') ?></td>
                            <td><?= esc($r['nik'] ?? '-') ?></td>
                            <td><?= esc($r['jabatan'] ?? '-') ?></td>
                            <td><?= esc($r['no_telp'] ?? '-') ?></td>
                            <td><?= esc($r['tanggal_masuk'] ?? '-') ?></td>
                            <td><span class="badge badge-status <?= $badge ?>"><?= esc(ucfirst($r['status'])) ?></span></td>

                            <!-- Aksi -->
                            <td>
                                <a href="<?= site_url('owner/employees/' . $r['id'] . '/edit') ?>"
                                    class="btn btn-sm btn-outline-primary">
                                    Edit
                                </a>
                                <?php if ($r['status'] === 'resign' || $r['status'] === 'pensiun'): ?>
                                    <a href="<?= site_url('owner/employees/delete/' . $r['id']) ?>"
                                        class="btn btn-sm btn-outline-danger"
                                        onclick="return confirm('Yakin ingin menghapus karyawan ini?');">
                                        Hapus
                                    </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>

                    </tr>
                    </thead>
            </table>
        </div>
    </div>
</div>

<script>
    (function() {
        const q = document.getElementById('tblSearch');
        const rows = [...document.querySelectorAll('#tblKaryawan tbody tr')];
        q.addEventListener('input', function() {
            const v = this.value.toLowerCase();
            rows.forEach(tr => {
                const text = tr.innerText.toLowerCase();
                tr.style.display = text.indexOf(v) > -1 ? '' : 'none';
            });
        });
    })();
</script>

<?= $this->endSection() ?>