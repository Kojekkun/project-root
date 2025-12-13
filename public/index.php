<?php require_once __DIR__.'/../app/db.php'; require_once __DIR__.'/../app/helpers.php';
$stm = $pdo->query('SELECT d.*, c.name as category FROM destinations d LEFT JOIN categories c ON d.category_id=c.id ORDER BY d.created_at DESC LIMIT 8'); $dest = $stm->fetchAll();
?>
<!doctype html>
<html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Pariwisata Lokal</title><link rel="stylesheet" href="assets/css/style.css"></head><body>
<nav class="nav"><a class="brand" href="/">Pariwisata</a><div class="nav-right"><?php if(is_logged()): ?><a href="/profile.php">Hi, <?= e($_SESSION['user_name']) ?></a> | <a href="/logout.php">Logout</a><?php else: ?><a href="/login.php">Login</a> | <a href="/register.php">Daftar</a><?php endif; ?></div></nav>
<main class="container">
    <h1>Destinasi Pilihan</h1>
    <div class="grid">
        <?php foreach($dest as $d): ?>
            <article class="card">
                <img src="/uploads/<?= e($d['image'] ?? 'placeholder.jpg') ?>" alt="<?= e($d['title']) ?>">
                <h3><?= e($d['title']) ?></h3>
                <p class="muted"><?= e($d['category']) ?> • <?= e($d['location']) ?></p>
                <a class="btn" href="/destination_detail.php?id=<?= $d['id'] ?>">Lihat</a>
            </article>
        <?php endforeach; ?>
    </div>
</main></body></html>