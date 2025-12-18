<?= $this->extend('layout/app') ?>
<?= $this->section('content') ?>
<h4 class="mb-3"><?= esc($title ?? 'Profil') ?></h4>

<div class="card">
    <div class="card-body">
        <div class="mb-2"><strong>Nama:</strong> <?= esc($user['name'] ?? '-') ?></div>
        <div class="mb-2"><strong>Email:</strong> <?= esc($user['email'] ?? '-') ?></div>
        <div class="mb-2"><strong>Role:</strong> <?= esc($user['role'] ?? '-') ?></div>
        <a href="<?= site_url('/owner/password') ?>" class="btn btn-primary mt-2">Ganti Password</a>
    </div>
</div>
<?= $this->endSection() ?>