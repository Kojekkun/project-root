<?php
// public/admin/destination_edit.php
require_once __DIR__ . '/../../app/helpers.php';
require_once __DIR__ . '/../../app/db.php';

require_login();

// 1. Cek Admin
if (($_SESSION['user_role'] ?? '') !== 'admin') {
    flash_set('error', 'Akses ditolak.');
    header('Location: /index.php');
    exit;
}

// 2. Ambil ID dari URL
$id = $_GET['id'] ?? 0;

// 3. Ambil Data Wisata Lama
$stmt = $pdo->prepare("SELECT * FROM destinations WHERE id = ?");
$stmt->execute([$id]);
$dest = $stmt->fetch();

if (!$dest) {
    flash_set('error', 'Data wisata tidak ditemukan.');
    header('Location: /destinations.php');
    exit;
}

// 4. Ambil Data Kategori
$stmt_cat = $pdo->query("SELECT * FROM categories");
$categories = $stmt_cat->fetchAll();
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Edit - <?= e($dest['title']) ?></title>
    <link rel="stylesheet" href="../assets/css/style.css?v=4">
</head>
<body>
    <nav class="nav">
        <a class="brand" href="/index.php">Admin Panel</a>
        <div class="nav-right">
            <a href="../destination_detail.php?id=<?= $dest['id'] ?>" style="text-decoration:none; color:#555;">&larr; Batal & Kembali</a>
            
            <div class="nav-separator"></div>

            <?php 
                // PERBAIKAN: Path default placeholder (../)
                $nav_foto = '../assets/images/placeholder.jpg'; 
                
                // Cek fisik file pakai path server (__DIR__), tapi path HTML pakai relatif (../)
                if (!empty($_SESSION['user_avatar']) && file_exists(__DIR__ . '/../../public/uploads/avatars/' . $_SESSION['user_avatar'])) {
                    $nav_foto = '../uploads/avatars/' . $_SESSION['user_avatar'];
                }
            ?>
            <a href="/profile.php" class="nav-profile">
                <img src="<?= $nav_foto ?>" class="nav-avatar">
                <span class="nav-name"><?= e($_SESSION['user_name']) ?></span>
            </a>
        </div>
    </nav>

    <main class="container">
        <div class="card" style="max-width: 600px; margin: 0 auto;">
            <h2>✏️ Edit Destinasi</h2>
            
            <?php if($m=flash_get('error')): ?>
                <div class="alert alert-danger"><?= e($m) ?></div>
            <?php endif; ?>

            <form action="process_edit.php" method="post" enctype="multipart/form-data">
                <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
                <input type="hidden" name="id" value="<?= $dest['id'] ?>">
                
                <label>Nama Destinasi</label>
                <input name="title" required value="<?= e($dest['title']) ?>">
                
                <label>Kategori</label>
                <select name="category_id" required>
                    <option value="">-- Pilih Kategori --</option>
                    <?php foreach($categories as $c): ?>
                        <option value="<?= $c['id'] ?>" <?= $c['id'] == $dest['category_id'] ? 'selected' : '' ?>>
                            <?= e($c['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                
                <label>Lokasi</label>
                <input name="location" required value="<?= e($dest['location']) ?>">
                
                <label>Deskripsi Lengkap</label>
                <textarea name="description" rows="6" required><?= e($dest['description']) ?></textarea>
                
                <label>Foto Saat Ini</label>
                <div style="margin-bottom: 10px;">
                    <img src="../uploads/<?= e($dest['image'] ?? 'placeholder.jpg') ?>" style="height: 100px; border-radius: 4px;">
                </div>
                
                <label>Ganti Foto (Opsional)</label>
                <input type="file" name="image" accept="image/*">
                
                <button class="btn" style="width:100%; margin-top:20px;">Simpan Perubahan</button>
            </form>
            
            <hr style="margin: 30px 0; border:0; border-top:1px dashed #ccc;">
            
            <form action="process_delete.php" method="post" onsubmit="return confirm('⚠️ PERINGATAN KERAS!\n\nApakah Anda yakin ingin MENGHAPUS wisata ini selamanya?\nData yang dihapus tidak dapat dikembalikan.');">
                <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
                <input type="hidden" name="id" value="<?= $dest['id'] ?>">
                
                <button style="background: #fee2e2; color: #b91c1c; width: 100%; border: 1px solid #fca5a5; padding: 12px; border-radius: 5px; cursor: pointer; font-weight:bold;">
                    🗑️ Hapus Destinasi Ini
                </button>
            </form>
            
        </div>
    </main>
</body>
</html>