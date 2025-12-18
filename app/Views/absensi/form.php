<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<h3>Form Absensi</h3>
<form method="post" action="/me/absensi/save">
    <label>Tanggal</label>
    <input type="date" name="tanggal" required>
    <label>Waktu Masuk</label>
    <input type="time" name="waktu_masuk">
    <label>Waktu Keluar</label>
    <input type="time" name="waktu_keluar">
    <label>Catatan</label>
    <input type="text" name="catatan" placeholder="Opsional">
    <button type="submit">Simpan</button>
</form>
<?= $this->endSection() ?>