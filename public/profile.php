<?php
// public/profile.php (BARU: View untuk melihat profil)
require_once __DIR__ . '/../app/db.php';
require_once __DIR__ . '/../app/helpers.php';

require_login(); // Pastikan sudah login

// Ambil data user
$stmt = $pdo->prepare('SELECT id, name, email FROM users WHERE id = ?');
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

if (!$user) { header('Location: /logout.php'); exit; }
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Profil Saya</title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
    <nav class="nav"><a class="brand" href="/">Pariwisata</a><div class="nav-right"><a href="/profile.php">Hi, <?= e($_SESSION['user_name']) ?></a> | <a href="/logout.php">Logout</a></div></nav>
    <main class="container">
        <section class="card">
            <h2>Update Profil</h2>
            <?php if($m=flash_get('success')): ?><div class="alert alert-success"><?= e($m) ?></div><?php endif; ?>
            <?php if($m=flash_get('error')): ?><div class="alert alert-danger"><?= e($m) ?></div><?php endif; ?>
            
            <form method="post" action="/profile_handler.php">
                <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
                <label>Nama</label>
                <input name="name" value="<?= e($user['name']) ?>" required>
                <label>Email</label>
                <input value="<?= e($user['email']) ?>" disabled>
                
                <p>Ubah Password (kosongkan jika tidak ingin diubah):</p>
                <label>Password Baru</label>
                <input name="new_password" type="password" minlength="6">
                
                <button class="btn">Update Profil</button>
            </form>
        </section>
    </main>
</body>
</html>