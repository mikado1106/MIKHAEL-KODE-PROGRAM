<?= $this->extend('layout/app') ?>
<?= $this->section('content') ?>

<h4 class="mb-3"><?= esc($title ?? 'Rekap Bulanan') ?></h4>

<form class="row g-2 mb-3" method="get" action="<?= site_url('/owner/reports/rekap') ?>">
    <div class="col-md-3">
        <label class="form-label">Bulan</label>
        <select name="month" class="form-select" onchange="this.form.submit()">
            <?php for ($m = 1; $m <= 12; $m++): ?>
                <option value="<?= $m ?>" <?= (int)$month === $m ? 'selected' : '' ?>><?= date('F', mktime(0, 0, 0, $m, 1)) ?></option>
            <?php endfor; ?>
        </select>
    </div>
    <div class="col-md-3">
        <label class="form-label">Tahun</label>
        <select name="year" class="form-select" onchange="this.form.submit()">
            <?php for ($y = date('Y') - 2; $y <= date('Y') + 1; $y++): ?>
                <option value="<?= $y ?>" <?= (int)$year === $y ? 'selected' : '' ?>><?= $y ?></option>
            <?php endfor; ?>
        </select>
    </div>
    <div class="col-md-3 d-flex align-items-end gap-2">
        <a class="btn btn-outline-success"
            href="<?= site_url('/owner/reports/rekap/csv?year=' . $year . '&month=' . $month) ?>">
            Unduh CSV
        </a>
        <a class="btn btn-primary"
            href="<?= site_url('/owner/reports/rekap/pdf?year=' . $year . '&month=' . $month) ?>">
            Unduh PDF
        </a>
    </div>
</form>

<div class="row g-3 mb-3">
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-header">Ringkas Izin (<?= esc($start) ?> s/d <?= esc($end) ?>)</div>
            <div class="card-body">
                <div>Menunggu: <span class="badge bg-warning text-dark"><?= esc($izin['menunggu']) ?></span></div>
                <div>Disetujui: <span class="badge bg-success"><?= esc($izin['setuju']) ?></span></div>
                <div>Ditolak: <span class="badge bg-secondary"><?= esc($izin['tolak']) ?></span></div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-header">Ringkas Cuti (<?= esc($start) ?> s/d <?= esc($end) ?>)</div>
            <div class="card-body">
                <div>Menunggu: <span class="badge bg-warning text-dark"><?= esc($cuti['menunggu']) ?></span></div>
                <div>Disetujui: <span class="badge bg-success"><?= esc($cuti['setuju']) ?></span></div>
                <div>Ditolak: <span class="badge bg-secondary"><?= esc($cuti['tolak']) ?></span></div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>Nama</th>
                    <th>Email</th>
                    <th class="text-center">Izin</th>
                    <th class="text-center">Cuti</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($rows)): foreach ($rows as $r): ?>
                        <tr>
                            <td><?= esc($r['nama']) ?></td>
                            <td><small class="text-muted"><?= esc($r['email']) ?></small></td>
                            <td class="text-center"><?= esc($r['izin_total']) ?></td>
                            <td class="text-center"><?= esc($r['cuti_total']) ?></td>
                        </tr>
                    <?php endforeach;
                else: ?>
                    <tr>
                        <td colspan="4" class="text-center text-muted">Tidak ada data di periode ini.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?= $this->endSection() ?>