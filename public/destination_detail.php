<?php
// public/destination_detail.php
require_once __DIR__ . '/../app/db.php';
require_once __DIR__ . '/../app/helpers.php';

$id = $_GET['id'] ?? 0;

// Ambil data destinasi
$stmt = $pdo->prepare('SELECT d.*, c.name as category_name 
                       FROM destinations d 
                       LEFT JOIN categories c ON d.category_id = c.id 
                       WHERE d.id = ?');
$stmt->execute([$id]);
$dest = $stmt->fetch();

if (!$dest) {
    header('Location: index.php');
    exit;
}

// Ambil rekomendasi tour
$stmt_tours = $pdo->prepare('SELECT * FROM tours WHERE destination_id = ? ORDER BY id DESC LIMIT 3');
$stmt_tours->execute([$id]);
$related_tours = $stmt_tours->fetchAll();
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title><?= e($dest['title']) ?> - Travel Buddies</title>
    <link rel="stylesheet" href="assets/css/style.css?v=11">
    <style>
        .map-container iframe { width: 100%; height: 400px; border: 0; border-radius: 16px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
    </style>
</head>
<body>
    <nav class="nav">
        <a class="brand" href="index.php">Travel Buddies</a>
        <div class="nav-right">
            <a href="index.php" style="text-decoration:none; color:var(--text-main);">Beranda</a>
            <a href="tours.php" style="text-decoration:none; color:var(--text-main);">Paket Tour</a>
            <?php if(is_logged()): ?>
                <div class="nav-separator"></div>
                <?php if($_SESSION['user_role'] === 'admin'): ?>
                    <a href="admin/index.php" class="btn" style="padding: 8px 15px; font-size: 0.8rem; background: var(--secondary);">⚙️ Admin Panel</a>
                <?php endif; ?>
                <a href="profile.php" class="nav-profile">
                    <?php 
                        $avatar_url = !empty($_SESSION['user_avatar']) ? get_image_url('avatars/' . $_SESSION['user_avatar']) : 'assets/images/placeholder.jpg';
                        if (strpos($avatar_url, 'http') === false && !file_exists($avatar_url)) $avatar_url = 'assets/images/placeholder.jpg';
                    ?>
                    <img src="<?= $avatar_url ?>" class="nav-avatar" alt="Profil">
                    <span class="nav-name"><?= e($_SESSION['user_name']) ?></span>
                </a>
            <?php else: ?>
                <span style="margin: 0 10px; color:#ccc;">|</span>
                <a href="login.php" style="color:var(--text-main);">Masuk</a>
                <a href="register.php" class="btn">Daftar</a>
            <?php endif; ?>
        </div>
    </nav>

    <main class="container">
        
        <article class="card" style="padding:0; overflow:hidden; border:none; box-shadow:none; background:transparent;">
            <div style="position: relative; border-radius: 20px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.15);">
                <img src="<?= get_image_url($dest['image']) ?>" style="width: 100%; height: 450px; object-fit: cover;">
                <div style="position: absolute; bottom: 0; left: 0; right: 0; background: linear-gradient(to top, rgba(0,0,0,0.8), transparent); padding: 40px 30px 30px;">
                    <span style="background:var(--primary); color:white; padding:5px 15px; border-radius:20px; font-size:0.9rem; font-weight:bold;">
                        <?= e($dest['category_name'] ?? 'Umum') ?>
                    </span>
                    <h1 style="color: white; font-size: 2.5rem; margin: 10px 0 5px; font-family:'Playfair Display', serif;">
                        <?= e($dest['title']) ?>
                    </h1>
                    <p style="color: #eee; margin: 0; font-size: 1.1rem;">📍 <?= e($dest['location']) ?></p>
                </div>
            </div>

            <?php if(!empty($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
                <div style="margin-top: 20px; text-align: right;">
                    <a href="admin/destination_edit.php?id=<?= $dest['id'] ?>" class="btn" style="background: #f59e0b; padding: 12px 25px; display:inline-flex; align-items:center; gap:8px;">
                        <span>✏️</span> Edit Destinasi Ini
                    </a>
                </div>
            <?php endif; ?>
            <div style="background: white; padding: 40px; border-radius: 20px; margin-top: <?= (!empty($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin') ? '20px' : '-30px' ?>; position: relative; z-index: 10; box-shadow: 0 5px 20px rgba(0,0,0,0.05);">
                <h3 style="margin-top:0;">📝 Tentang Destinasi</h3>
                <div style="line-height: 1.8; color: #4b5563; font-size: 1.05rem; margin-bottom: 30px;">
                    <?= nl2br(e($dest['description'])) ?>
                </div>

                <?php if (!empty($dest['map_embed'])): ?>
                    <h3 style="margin-bottom: 15px;">🗺️ Lokasi Peta</h3>
                    <div class="map-container"><?= $dest['map_embed'] ?></div>
                <?php endif; ?>
            </div>
        </article>

        <?php if(count($related_tours) > 0): ?>
            <div style="margin-top: 50px;">
                <h2 style="text-align:center; margin-bottom: 30px;">🎒 Paket Trip ke Sini</h2>
                <div class="grid">
                    <?php foreach($related_tours as $t): ?>
                        <article class="card" style="padding:0; overflow:hidden; display:flex; flex-direction:column; border: 1px solid #e5e7eb;">
                            <div style="position:relative;">
                                <img src="<?= get_image_url($dest['image']) ?>" style="width:100%; height:200px; object-fit:cover;">
                                <span style="position:absolute; bottom:15px; right:15px; background:var(--primary); color:white; padding:5px 15px; border-radius:20px; font-weight:bold; font-size:0.9rem;">Rp <?= number_format($t['price'], 0, ',', '.') ?></span>
                            </div>
                            <div style="padding:20px; flex-grow:1; text-align:left;">
                                <h3 style="margin:0 0 10px; font-size:1.2rem;"><?= e($t['title']) ?></h3>
                                <a href="tour_detail.php?id=<?= $t['id'] ?>" class="btn" style="width:100%; text-align:center;">Lihat Detail</a>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

    </main>

    <footer style="background:var(--bg-card); padding:40px 20px; text-align:center; border-top:1px solid #eee; margin-top:80px;">
        <h3 class="brand" style="font-size:1.5rem; margin-bottom:10px;">Travel Buddies</h3>
        <p class="muted">&copy; <?= date('Y') ?> Travel Buddies.</p>
    </footer>
</body>
</html>