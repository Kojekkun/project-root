// public/login.php (view)
<?php require_once __DIR__.'/../app/helpers.php'; ?>
<!doctype html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width,initial-scale=1">
        <title>Login</title><link rel="stylesheet" href="/assets/css/style.css">
    </head>
    <body>
        <main class="container">
            <section class="card"><h2>Login</h2><?php if($m=flash_get('error')): ?><div class="alert alert-danger"><?= e($m) ?></div><?php endif; ?>
                <form method="post" action="/login_handler.php">
                    <input type="hidden" name="csrf" value="<?= csrf_token() ?>"><label>Email</label>
                    <input name="email" type="email" required><label>Password</label>
                    <input name="password" type="password" required><button class="btn">Login</button></form>
                    <p>Belum punya akun? <a href="/register.php">Daftar</a></p>
            </section>
        </main>
    </body>
</html>