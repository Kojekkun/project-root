<?php require_once __DIR__.'/../app/db.php'; require_once __DIR__.'/../app/helpers.php';
$stmt=$pdo->query('SELECT d.*, c.name as category FROM destinations d LEFT JOIN categories c ON d.category_id=c.id ORDER BY d.title'); $list=$stmt->fetchAll();
?>
<!doctype html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Destinasi</title><link rel="stylesheet" href="/assets/css/style.css"></head><body>
<main class="container"><h1>Semua Destinasi</h1><?php if(!empty($_SESSION['user_role']) && $_SESSION['user_role']==='admin'): ?><a class="btn" href="/admin/destination_add.php">Tambah Destinasi</a><?php endif; ?><div class="grid"><?php foreach($list as $d): ?><article class="card">
    <img src="/uploads/<?= e($d['image'] ?? 'placeholder.jpg') ?>">
    <h3><?= e($d['title']) ?></h3><p class="muted"><?= e($d['category']) ?></p><a class="btn" href="/destination_detail.php?id=<?= $d['id'] ?>">Detail</a></article><?php endforeach; ?></div></main></body></html>