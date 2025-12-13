<?php
// public/tours.php
require_once __DIR__ . '/../app/db.php';
require_once __DIR__ . '/../app/helpers.php';

// Ambil data Tours dari database
$stmt = $pdo->query("SELECT * FROM tours ORDER BY id DESC");
$tours = $stmt->fetchAll();
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Paket Tour & Travel Buddy</title>
    <link rel="stylesheet" href="assets/css/style.css?v=5">
</head>
<body>
    <nav class="nav">
        <a class="brand" href="index.php">Pariwisata</a>
        <div class="nav-right">
            <a href="index.php" style="text-decoration:none; color:#555;">Home</a>
            <a href="destinations.php" style="text-decoration:none; color:#555; margin-left:15px;">Destinasi</a>
            <a href="tours.php" style="text-decoration:none; color:#2563eb; font-weight:bold; margin-left:15px;">Paket Tour</a>
            
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
        <h1 style="border-bottom: 2px solid #eee; padding-bottom: 10px;">📦 Paket Tour & Teman Jalan</h1>
        
        <div class="grid">
            <?php foreach($tours as $t): ?>
            <article class="card" style="padding:0; overflow:hidden; display:flex; flex-direction:column;">
                <div style="position:relative;">
                    <img src="uploads/<?= e($t['image'] ?? 'placeholder.jpg') ?>" 
                         style="width:100%; height:200px; object-fit:cover;">
                    <span style="position:absolute; bottom:10px; right:10px; background:#2563eb; color:white; padding:5px 10px; border-radius:20px; font-weight:bold; font-size:0.9rem;">
                        Rp <?= number_format($t['price'], 0, ',', '.') ?>
                    </span>
                </div>
                
                <div style="padding:20px; flex-grow:1; display:flex; flex-direction:column;">
                    <h3 style="margin-top:0; margin-bottom:10px;"><?= e($t['title']) ?></h3>
                    <p class="muted" style="font-size:0.9rem; margin-bottom:15px; flex-grow:1;">
                        <?= substr(e($t['description']), 0, 100) ?>...
                    </p>
                    
                    <a href="https://wa.me/<?= e($t['contact']) ?>?text=Halo, saya tertarik dengan paket <?= e($t['title']) ?>" 
                       target="_blank"
                       class="btn" 
                       style="background-color:#25d366; text-align:center; display:block;">
                       📱 Chat WhatsApp
                    </a>
                </div>
            </article>
            <?php endforeach; ?>
        </div>
        
        <?php if(count($tours) == 0): ?>
            <p style="text-align:center; padding:50px; color:#888;">Belum ada paket tour tersedia saat ini.</p>
        <?php endif; ?>

    </main>
</body>
</html>