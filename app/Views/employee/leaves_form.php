<?= $this->extend('layout/app') ?>
<?= $this->section('content') ?>

<h4 class="mb-3"><?= esc($title ?? 'Pengajuan Izin') ?></h4>

<form method="POST" action="<?= site_url('/employee/leaves/izin') ?>">
    <?= csrf_field() ?>

    <div class="mb-3">
        <label for="jenis" class="form-label">Jenis Izin</label>
        <select name="jenis" class="form-select">
            <option value="sakit">Sakit</option>
            <option value="alasan_pribadi">Alasan Pribadi</option>
            <option value="acara_keluarga">Acara Keluarga</option>
        </select>
    </div>

    <div class="mb-3">
        <label for="tgl_mulai" class="form-label">Tanggal Mulai</label>
        <input type="date" name="tgl_mulai" class="form-control" required>
    </div>

    <div class="mb-3">
        <label for="tgl_selesai" class="form-label">Tanggal Selesai</label>
        <input type="date" name="tgl_selesai" class="form-control" required>
    </div>

    <div class="mb-3">
        <label for="catatan" class="form-label">Catatan (opsional)</label>
        <textarea name="catatan" class="form-control" rows="3"></textarea>
    </div>

    <button type="submit" class="btn btn-primary">Ajukan Izin</button>
</form>

<?= $this->endSection() ?>