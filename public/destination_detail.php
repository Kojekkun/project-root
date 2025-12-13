<?php
// public/destination_detail.php
require_once __DIR__ . '/../app/db.php';
require_once __DIR__ . '/../app/helpers.php';

// 1. Ambil ID dari URL (contoh: destination_detail.php?id=1)
$id = $_GET['id'] ?? 0;

// 2. Ambil Data dari Database (Join dengan tabel kategori agar nama kategori muncul)
$stmt = $pdo->prepare('SELECT d.*, c.name as category FROM destinations d LEFT JOIN categories c ON d.category_id = c.id WHERE d.id = ?');
$stmt->execute([$id]);
$destination = $stmt->fetch();

// Jika data tidak ditemukan (misal ID ngawur), kembalikan ke Home
if (!$destination) {
    header('Location: index.php');
    exit;
}
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title><?= e($destination['title']) ?> - Pariwisata</title>
    
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <nav class="nav">
        <a class="brand" href="index.php">Pariwisata</a>
        <div class="nav-right">
            <a href="index.php">Semua Destinasi</a>
            <?php if(is_logged()): ?>
                <a href="profile.php">Hi, <?= e($_SESSION['user_name']) ?></a> | 
                <a href="logout.php">Logout</a>
            <?php else: ?>
                <a href="login.php">Login</a> | 
                <a href="register.php">Daftar</a>
            <?php endif; ?>
        </div>
    </nav>

    <main class="container">
        <article class="card">
            <img src="uploads/<?= e($destination['image'] ?? 'placeholder.jpg') ?>" 
                 alt="<?= e($destination['title']) ?>" 
                 style="width: 100%; height: 400px; object-fit: cover; border-radius: 5px; margin-bottom: 20px;">
            
            <h1 style="margin-bottom: 0.5rem;"><?= e($destination['title']) ?></h1>
            <p class="muted" style="margin-top: 0;">
                Kategori: <strong><?= e($destination['category']) ?></strong> • 
                Lokasi: <?= e($destination['location']) ?>
            </p>
            
            <hr style="border: 0; border-top: 1px solid #eee; margin: 1.5rem 0;">

            <div style="line-height: 1.8; font-size: 1.1rem;">
                <?= nl2br(e($destination['description'])) ?>
            </div>

            <br><br>
            
            <a href="index.php" class="btn" style="background-color: #6b7280;">&larr; Kembali</a>
            
            <?php if(!empty($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
                <a href="admin/destination_edit.php?id=<?= $destination['id'] ?>" class="btn" style="margin-left: 10px;">Edit Data</a>
            <?php endif; ?>
        </article>
    </main>
</body>
</html>