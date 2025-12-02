<?php require_once __DIR__ . '/../app/helpers.php'; ?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Daftar - Pariwisata</title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
    <main class="container">
        <section class="card">
            <h2>Buat Akun</h2>
            <?php if($m=flash_get('error')): ?><div class="alert alert-danger"><?= e($m) ?></div><?php endif; ?>
            <form method="post" action="/register_handler.php">
                <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
                <label>Nama</label>
                <input name="name" required>
                <label>Email</label>
                <input name="email" type="email" required>
                <label>Password</label>
                <input name="password" type="password" minlength="6" required>
                <button class="btn">Daftar</button>
            </form>
            <p>Sudah punya akun? <a href="/login.php">Login</a></p>
        </section>
    </main>
</body>
</html>