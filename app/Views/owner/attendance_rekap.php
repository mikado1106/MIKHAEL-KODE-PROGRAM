<?= $this->extend('layout/app') ?>
<?= $this->section('content') ?>

<h4 class="mb-3"><?= esc($title ?? 'Rekap Absensi') ?></h4>

<form class="row g-2 mb-3" method="get" action="<?= site_url('/owner/attendance/rekap') ?>">
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
    <div class="col-md-4 d-flex align-items-end gap-2">
        <a class="btn btn-outline-success" href="<?= site_url('/owner/attendance/rekap/csv?year=' . $year . '&month=' . $month) ?>">Unduh CSV</a>
        <a class="btn btn-primary" href="<?= site_url('/owner/attendance/rekap/pdf?year=' . $year . '&month=' . $month) ?>">Unduh PDF</a>
    </div>
</form>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>Nama</th>
                    <th class="text-center">Hadir</th>
                    <th class="text-center">Terlambat</th>
                    <th class="text-center">Pulang Cepat</th>
                    <th class="text-center">Durasi Total</th>

                    <th class="text-center">Izin</th>
                    <th class="text-center">Cuti</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($rows): foreach ($rows as $r): ?>
                        <tr>
                            <td><?= esc($r['nama']) ?></td>
                            <td class="text-center"><?= (int)$r['hadir'] ?></td>
                            <td class="text-center"><?= (int)$r['terlambat'] ?></td>
                            <td class="text-center"><?= (int)$r['pulang_cepat'] ?></td>
                            <td class="text-center"><?= esc($r['durasi_total']) ?></td>

                            <td class="text-center"><?= (int)$r['izin'] ?></td>
                            <td class="text-center"><?= (int)$r['cuti'] ?></td>
                        </tr>
                    <?php endforeach;
                else: ?>
                    <tr>
                        <td colspan="8" class="text-center text-muted">Tidak ada data.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?= $this->endSection() ?>