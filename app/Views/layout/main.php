<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <title>HR PT Papande</title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="/dashboard">HR PT Papande</a>
            <div class="d-flex">
                <?php if (session('user')): ?>
                    <span class="navbar-text me-3">Halo, <?= esc(session('user.name')) ?> (<?= esc(session('user.role')) ?>)</span>
                    <a class="btn btn-outline-light btn-sm" href="/logout">Logout</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>
    <main class="container py-4">
        <?php if (session('error')): ?><div class="alert alert-danger"><?= session('error') ?></div><?php endif; ?>
        <?php if (session('success')): ?><div class="alert alert-success"><?= session('success') ?></div><?php endif; ?>
        <?= $this->renderSection('content') ?>
    </main>
</body>

</html>