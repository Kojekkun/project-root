<?php
// public/profile.php (FULL VERSION)
require_once __DIR__ . '/../app/db.php';
require_once __DIR__ . '/../app/helpers.php';

require_login(); // Pastikan sudah login

// Ambil data user TERBARU dari database (termasuk avatar)
$stmt = $pdo->prepare('SELECT id, name, email, avatar FROM users WHERE id = ?');
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

if (!$user) { header('Location: logout.php'); exit; }
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Profil Saya</title>
    <link rel="stylesheet" href="assets/css/style.css?v=5">
</head>
<body>
    <nav class="nav">
        <a class="brand" href="index.php">Pariwisata</a>
        <div class="nav-right">
            
            <a href="index.php" style="text-decoration:none; color:#555;">Home</a>
            <a href="tours.php" style="text-decoration:none; color:#555; margin-left:15px;">Paket Tour</a>
            
            <span style="margin: 0 10px; color:#ccc;">|</span>
            <?php 
                $nav_foto = 'assets/images/placeholder.jpg'; 
                if (!empty($user['avatar']) && file_exists(__DIR__ . '/uploads/avatars/' . $user['avatar'])) {
                    $nav_foto = 'uploads/avatars/' . $user['avatar'];
                }
            ?>
            <a href="profile.php" class="nav-user-link" style="display:inline-flex; align-items:center; text-decoration:none; color:#333;">
                <img src="<?= $nav_foto ?>" class="nav-avatar" style="width:30px; height:30px; border-radius:50%; object-fit:cover; margin-right:8px; border:1px solid #ccc;">
                <span style="font-weight:bold;"><?= e($user['name']) ?></span>
            </a>
            
            <div class="nav-separator"></div> <span style="color:#ccc; margin:0 5px;">|</span>
            
            <a href="logout.php" class="btn-logout">Logout</a>
        </div>
    </nav>

    <main class="container">
        <section class="card" style="max-width: 600px; margin: 0 auto;">
            <h2 style="border-bottom: 1px solid #eee; padding-bottom: 10px;">Update Profil</h2>
            
            <?php if($m=flash_get('success')): ?>
                <div class="alert alert-success"><?= e($m) ?></div>
            <?php endif; ?>
            <?php if($m=flash_get('error')): ?>
                <div class="alert alert-danger"><?= e($m) ?></div>
            <?php endif; ?>
            
            <form method="post" action="profile_handler.php" enctype="multipart/form-data">
                <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
                
                <div style="text-align: center; margin: 20px 0;">
                    <?php 
                        $foto_profil = 'assets/images/placeholder.jpg';
                        if(!empty($user['avatar']) && file_exists(__DIR__ . '/uploads/avatars/' . $user['avatar'])) {
                            $foto_profil = 'uploads/avatars/' . $user['avatar'];
                        }
                    ?>
                    <img src="<?= $foto_profil ?>" style="width: 120px; height: 120px; border-radius: 50%; object-fit: cover; border: 4px solid #f3f4f6; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
                </div>

                <div style="background: #f9fafb; padding: 15px; border-radius: 6px; margin-bottom: 20px; border: 1px dashed #ccc;">
                    <label style="display:block; margin-bottom:5px; font-weight:bold;">Ganti Foto Profil</label>
                    <input type="file" name="avatar" accept="image/*" style="margin-bottom:0; padding:5px; background:white;">
                    <p class="muted" style="font-size: 0.8rem; margin: 5px 0 0 0; color:#666;">Format: JPG/PNG. Maks 2MB.</p>
                </div>

                <label>Nama Lengkap</label>
                <input name="name" value="<?= e($user['name']) ?>" required>
                
                <label>Email (Tidak bisa diubah)</label>
                <input value="<?= e($user['email']) ?>" disabled style="background: #e5e7eb; color: #6b7280; cursor: not-allowed;">
                
                <hr style="border: 0; border-top: 1px solid #eee; margin: 20px 0;">
                
                <label>Password Baru (Opsional)</label>
                <input name="new_password" type="password" placeholder="Kosongkan jika tidak ingin mengubah password">
                
                <button class="btn" style="width: 100%; margin-top: 10px; padding: 12px; font-size: 1rem;">Simpan Perubahan</button>
            </form>
        </section>
    </main>
</body>
</html>