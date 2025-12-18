<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Login - HR</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f7f9fc;
        }

        .login-card {
            max-width: 420px;
            margin: 8vh auto;
        }
    </style>
</head>

<body>
    <div class="card shadow login-card">
        <div class="card-body">
            <h3 class="mb-3 text-center">Login</h3>

            <?php if (session('error')): ?>
                <div class="alert alert-danger"><?= esc(session('error')) ?></div>
            <?php endif; ?>

            <form method="post" action="<?= site_url('/login') ?>">
                <?= csrf_field() ?>
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" placeholder="you@mail.com" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                </div>
                <button class="btn btn-primary w-110">Masuk</button>
                <a href="<?= site_url('karir') ?>" class="btn btn-primary">Lamar Pekerjaan</a>
            </form>
        </div>
    </div>

</body>

</html>