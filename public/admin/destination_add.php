<?php
// public/admin/destination_add.php
require_once __DIR__ . '/../../app/helpers.php';
require_once __DIR__ . '/../../app/db.php';

require_login();

// Cek apakah user adalah admin
if (($_SESSION['user_role'] ?? '') !== 'admin') {
    flash_set('error', 'Anda tidak memiliki akses ke halaman ini.');
    header('Location: ../index.php'); // PERBAIKAN: Gunakan ../
    exit;
}

// Ambil data kategori untuk dropdown
$stmt = $pdo->query("SELECT * FROM categories");
$categories = $stmt->fetchAll();
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Tambah Destinasi</title>
    <link rel="stylesheet" href="../assets/css/style.css?v=4">
</head>
<body>
    <nav class="nav">
        <a class="brand" href="../index.php">Admin Panel</a>
        
        <div class="nav-right">
            <a href="../destinations.php" style="text-decoration:none; color:#555;">&larr; Kembali ke List</a>
            
            <div class="nav-separator"></div>

            <?php 
                $nav_foto = '../assets/images/placeholder.jpg'; 
                if (!empty($_SESSION['user_avatar']) && file_exists(__DIR__ . '/../../public/uploads/avatars/' . $_SESSION['user_avatar'])) {
                    $nav_foto = '../uploads/avatars/' . $_SESSION['user_avatar'];
                }
            ?>
            <a href="../profile.php" class="nav-profile">
                <img src="<?= $nav_foto ?>" class="nav-avatar">
                <span class="nav-name"><?= e($_SESSION['user_name']) ?></span>
            </a>
        </div>
    </nav>

    <main class="container">
        <div class="card" style="max-width: 600px; margin: 0 auto;">
            <h2>+ Tambah Wisata Baru</h2>
            
            <?php if($m=flash_get('error')): ?>
                <div class="alert alert-danger" style="background:#fce7f3; color:#9d174d; padding:10px; margin-bottom:15px; border-radius:4px;">
                    <?= e($m) ?>
                </div>
            <?php endif; ?>

            <form action="process_add.php" method="post" enctype="multipart/form-data">
                <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
                
                <label>Nama Destinasi</label>
                <input name="title" required placeholder="Contoh: Pantai Kuta">
                
                <label>Kategori</label>
                <select name="category_id" required>
                    <option value="">-- Pilih Kategori --</option>
                    <?php foreach($categories as $c): ?>
                        <option value="<?= $c['id'] ?>"><?= e($c['name']) ?></option>
                    <?php endforeach; ?>
                </select>
                
                <label>Lokasi</label>
                <input name="location" required placeholder="Contoh: Badung, Bali">
                
                <label>Deskripsi Lengkap</label>
                <textarea name="description" rows="5" required></textarea>
                
                <label>Foto Utama</label>
                <input type="file" name="image" accept="image/*" required>
                <p class="muted" style="font-size:0.8rem; margin-top:-10px;">Format: JPG/PNG/GIF. Maks 2MB.</p>
                
                <button class="btn" style="width:100%; margin-top:10px;">Simpan Data</button>
            </form>
        </div>
    </main>
</body>
</html>