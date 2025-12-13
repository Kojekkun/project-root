<?php
// public/destination_detail.php (VERSI DENGAN REKOMENDASI TOUR)
require_once __DIR__ . '/../app/db.php';
require_once __DIR__ . '/../app/helpers.php';

$id = $_GET['id'] ?? 0;

// 1. Ambil Data Destinasi
$stmt = $pdo->prepare('SELECT d.*, c.name as category FROM destinations d LEFT JOIN categories c ON d.category_id = c.id WHERE d.id = ?');
$stmt->execute([$id]);
$destination = $stmt->fetch();

if (!$destination) { header('Location: index.php'); exit; }

// 2. [BARU] Cari Paket Tour yang terhubung dengan Destinasi ini
$stmt_tours = $pdo->prepare('SELECT * FROM tours WHERE destination_id = ? ORDER BY price ASC');
$stmt_tours->execute([$id]);
$related_tours = $stmt_tours->fetchAll();
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title><?= e($destination['title']) ?> - Pariwisata</title>
    <link rel="stylesheet" href="assets/css/style.css?v=5">
</head>
<body>
    <nav class="nav">
        <a class="brand" href="index.php">Pariwisata</a>
        <div class="nav-right">
            <a href="index.php" style="text-decoration:none; color:#555;">Home</a>
            <a href="tours.php" style="text-decoration:none; color:#555; margin-left:15px;">Paket Tour</a>
            
            <?php if(is_logged()): ?>
                <div class="nav-separator"></div>
                <?php 
                    $nav_foto = 'assets/images/placeholder.jpg'; 
                    if (!empty($_SESSION['user_avatar']) && file_exists(__DIR__ . '/uploads/avatars/' . $_SESSION['user_avatar'])) {
                        $nav_foto = 'uploads/avatars/' . $_SESSION['user_avatar'];
                    }
                ?>
                <a href="profile.php" class="nav-profile">
                    <img src="<?= $nav_foto ?>" class="nav-avatar">
                    <span class="nav-name"><?= e($_SESSION['user_name']) ?></span>
                </a>
                <div class="nav-separator"></div>
                <a href="logout.php" class="btn-logout">Logout</a>
            <?php else: ?>
                <span style="margin: 0 10px; color:#ccc;">|</span>
                <a href="login.php" style="color:#333; margin-right:15px;">Login</a>
                <a href="register.php" class="btn">Daftar</a>
            <?php endif; ?>
        </div>
    </nav>

    <main class="container">
        <article class="card">
            <img src="uploads/<?= e($destination['image'] ?? 'placeholder.jpg') ?>" 
                 style="width: 100%; height: 400px; object-fit: cover; border-radius: 5px; margin-bottom: 20px;">
            
            <h1 style="margin-bottom: 0.5rem;"><?= e($destination['title']) ?></h1>
            <p class="muted" style="margin-top: 0;">
                Kategori: <strong><?= e($destination['category']) ?></strong> • 
                Lokasi: <?= e($destination['location']) ?>
            </p>
            <hr style="border: 0; border-top: 1px solid #eee; margin: 1.5rem 0;">
            <div style="line-height: 1.8; font-size: 1.1rem; text-align: justify;">
                <?= nl2br(e($destination['description'])) ?>
            </div>
            
            <?php if(!empty($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
                <br>
                <a href="admin/destination_edit.php?id=<?= $destination['id'] ?>" class="btn">✏️ Edit Destinasi Ini</a>
            <?php endif; ?>
        </article>

        <?php if(count($related_tours) > 0): ?>
            <div style="margin-top: 50px;">
                <h2 style="border-left: 5px solid #2563eb; padding-left: 15px; margin-bottom: 20px;">
                    🎒 Paket Tour Tersedia untuk <?= e($destination['title']) ?>
                </h2>
                
                <div class="grid">
                    <?php foreach($related_tours as $t): ?>
                        <div class="card" style="padding:0; overflow:hidden; border:1px solid #ddd;">
                            <img src="uploads/<?= e($t['image'] ?? 'placeholder.jpg') ?>" 
                                 style="width:100%; height:180px; object-fit:cover;">
                            
                            <div style="padding:15px;">
                                <h4 style="margin:0 0 10px 0;"><?= e($t['title']) ?></h4>
                                <p style="color:#2563eb; font-weight:bold; font-size:1.1rem; margin-bottom:15px;">
                                    Rp <?= number_format($t['price'], 0, ',', '.') ?>
                                </p>
                                <a href="tour_detail.php?id=<?= $t['id'] ?>" class="btn" style="width:100%; display:block; text-align:center;">
                                    Lihat Detail & Jadwal
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php else: ?>
            <div style="margin-top: 40px; padding: 20px; background: #f9fafb; border-radius: 8px; text-align: center; color: #666;">
                <p>Belum ada paket Open Trip / Private Tour yang terdaftar untuk destinasi ini.</p>
            </div>
        <?php endif; ?>

    </main>
</body>
</html>