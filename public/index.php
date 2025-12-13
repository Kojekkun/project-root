<?php 
// public/index.php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__.'/../app/db.php'; 
require_once __DIR__.'/../app/helpers.php';

$dest = [];
$tours = []; // Variabel untuk menampung data tour
$error_db = "";

try {
    // 1. Ambil data DESTINASI
    $sql_dest = 'SELECT d.*, c.name as category 
            FROM destinations d 
            LEFT JOIN categories c ON d.category_id = c.id 
            ORDER BY d.id DESC';
    $stm = $pdo->query($sql_dest);
    if ($stm) {
        $dest = $stm->fetchAll();
    }

    // 2. Ambil data TOUR (Baru)
    $sql_tour = "SELECT * FROM tours ORDER BY id DESC";
    $stm_tour = $pdo->query($sql_tour);
    if ($stm_tour) {
        $tours = $stm_tour->fetchAll();
    }

} catch (Exception $e) {
    $error_db = $e->getMessage();
}
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Pariwisata Lokal</title>
    <link rel="stylesheet" href="assets/css/style.css?v=5"> 
</head>
<body>

    <nav class="nav">
        <a class="brand" href="index.php">Pariwisata</a>
        <div class="nav-right">
            <a href="index.php" style="text-decoration:none; color:#2563eb; font-weight:bold;">Home</a>
            
            <?php if(is_logged()): ?>
                <div class="nav-separator"></div>

                <?php 
                    $nav_foto = 'assets/images/placeholder.jpg'; 
                    if (!empty($_SESSION['user_avatar']) && file_exists(__DIR__ . '/uploads/avatars/' . $_SESSION['user_avatar'])) {
                        $nav_foto = 'uploads/avatars/' . $_SESSION['user_avatar'];
                    }
                ?>

                <a href="profile.php" class="nav-profile">
                    <img src="<?= $nav_foto ?>" class="nav-avatar" alt="Profil">
                    <span class="nav-name"><?= e($_SESSION['user_name']) ?></span>
                </a>
                
                <div class="nav-separator"></div>
                <a href="logout.php" class="btn-logout">Logout</a>

            <?php else: ?>
                <span style="margin: 0 10px; color:#ccc;">|</span>
                <a href="login.php" style="color:#333; margin-right:15px; text-decoration:none;">Login</a>
                <a href="register.php" class="btn">Daftar</a>
            <?php endif; ?>
        </div>
    </nav>

    <main class="container">
        
        <?php if ($error_db): ?>
            <div class="alert alert-danger">
                <h3>⚠️ Terjadi Error Database:</h3>
                <p><?= e($error_db) ?></p>
            </div>
        <?php endif; ?>

        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h1 style="margin: 0;">Destinasi Pilihan</h1>
            
            <?php if(!empty($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
                <a href="admin/destination_add.php" class="btn" style="box-shadow: 0 2px 5px rgba(0,0,0,0.2);">
                    + Tambah Destinasi
                </a>
            <?php endif; ?>
        </div>

        <?php if (count($dest) == 0): ?>
            <p class="muted">Belum ada data destinasi.</p>
        <?php endif; ?>

        <div class="grid">
            <?php foreach($dest as $d): ?>
                <article class="card" style="padding: 0; overflow: hidden; display: flex; flex-direction: column;">
                    <div style="position: relative;">
                        <img src="uploads/<?= e($d['image'] ?? 'placeholder.jpg') ?>" 
                             alt="<?= e($d['title']) ?>"
                             style="width: 100%; height: 200px; object-fit: cover;">
                        <span style="position: absolute; top: 10px; right: 10px; background: rgba(0,0,0,0.6); color: white; padding: 4px 8px; border-radius: 4px; font-size: 0.8rem;">
                            <?= e($d['category'] ?? 'Umum') ?>
                        </span>
                    </div>
                    
                    <div style="padding: 20px; flex-grow: 1; display: flex; flex-direction: column;">
                        <h3 style="margin-top: 0; margin-bottom: 10px;"><?= e($d['title']) ?></h3>
                        <p class="muted" style="font-size: 0.9rem; margin-bottom: 15px;">
                            📍 <?= e($d['location']) ?>
                        </p>
                        
                        <div style="margin-top: auto;">
                            <a class="btn" href="destination_detail.php?id=<?= $d['id'] ?>" style="display: block; text-align: center;">
                                Lihat Detail
                            </a>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>


        <br><br>
        <hr style="border:0; border-top:1px solid #ddd;">
        <br><br>


        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h1 style="margin: 0;">📦 Paket Tour & Open Trip</h1>

            <?php if(!empty($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
                <a href="admin/tour_add.php" class="btn" style="box-shadow: 0 2px 5px rgba(0,0,0,0.2); background-color: #f59e0b;">
                    + Tambah Tour Baru
                </a>
            <?php endif; ?>    
        </div>

        <?php if (count($tours) == 0): ?>
            <div class="alert" style="background: #f3f4f6; color: #555; text-align: center;">
                Belum ada paket tour yang tersedia saat ini.
            </div>
        <?php endif; ?>

        <div class="grid">
            <?php foreach($tours as $t): ?>
            <article class="card" style="padding:0; overflow:hidden; display:flex; flex-direction:column; border: 1px solid #e5e7eb;">
                <div style="position:relative;">
                    <img src="uploads/<?= e($t['image'] ?? 'placeholder.jpg') ?>" 
                         style="width:100%; height:200px; object-fit:cover;">
                    
                    <span style="position:absolute; bottom:10px; right:10px; background:#2563eb; color:white; padding:5px 12px; border-radius:20px; font-weight:bold; font-size:0.9rem; box-shadow: 0 2px 4px rgba(0,0,0,0.2);">
                        Rp <?= number_format($t['price'], 0, ',', '.') ?>
                    </span>
                </div>
                
                <div style="padding:20px; flex-grow:1; display:flex; flex-direction:column;">
                    <h3 style="margin-top:0; margin-bottom:10px; color:#1f2937;"><?= e($t['title']) ?></h3>
                    
                    <p class="muted" style="font-size:0.9rem; margin-bottom:20px; flex-grow:1; line-height:1.5;">
                        <?= substr(e($t['description']), 0, 100) ?>...
                    </p>
                    
                    <div style="display:flex; gap:10px; margin-top:auto;">
                        <a href="tour_detail.php?id=<?= $t['id'] ?>" class="btn" style="flex:1; text-align:center;">
                            Lihat Detail
                        </a>
                    </div>

                </div>
            </article>
            <?php endforeach; ?>
        </div>

        <br><br> </main>
</body>
</html>