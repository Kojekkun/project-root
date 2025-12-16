<?php
require_once __DIR__ . '/../../app/helpers.php';
require_once __DIR__ . '/../../app/db.php';

require_login();

if (($_SESSION['user_role'] ?? '') !== 'admin') {
    flash_set('error', 'Akses ditolak.');
    header('Location: ../index.php');
    exit;
}

$id = $_GET['id'] ?? 0;

$stmt = $pdo->prepare("SELECT * FROM destinations WHERE id = ?");
$stmt->execute([$id]);
$dest = $stmt->fetch();

if (!$dest) {
    flash_set('error', 'Data wisata tidak ditemukan.');
    header('Location: ../destinations.php');
    exit;
}

$stmt_cat = $pdo->query("SELECT * FROM categories");
$categories = $stmt_cat->fetchAll();

// Cek apakah gambar saat ini adalah link URL?
$is_url = strpos($dest['image'], 'http') === 0;
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Edit - <?= e($dest['title']) ?></title>
    <link rel="stylesheet" href="../assets/css/style.css?v=7">
</head>
<body>
    <nav class="nav">
        <a class="brand" href="../index.php">Admin Panel</a>
        <div class="nav-right">
            <a href="../destination_detail.php?id=<?= $dest['id'] ?>" style="text-decoration:none; color:#555;">← Batal & Kembali</a>
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
                
                <label>Lokasi (Teks)</label>
                <input name="location" required value="<?= e($dest['location']) ?>">
                
                <label>Deskripsi Lengkap</label>
                <textarea name="description" rows="6" required><?= e($dest['description']) ?></textarea>

                <label>Google Maps Embed HTML</label>
                <textarea name="map_embed" rows="3"><?= $dest['map_embed'] ?></textarea>
                
                <hr style="margin: 20px 0; border:0; border-top:1px dashed #ddd;">

                <label>Foto Saat Ini</label>
                <div style="margin-bottom: 20px;">
                    <img src="../<?= get_image_url($dest['image']) ?>" style="height: 150px; border-radius: 8px; border: 1px solid #ddd; object-fit: cover;">
                </div>
                
                <label>Ganti Foto (Biarkan kosong jika tidak ingin mengganti)</label>
                
                <div style="background: #f0fdf4; padding: 15px; border: 1px solid #bbf7d0; border-radius: 8px; margin-bottom: 10px;">
                    <span style="font-size:0.9rem; font-weight:bold; color:#166534; display:block; margin-bottom:5px;">Opsi A: Upload File Baru</span>
                    <input type="file" name="image" accept="image/*" style="background:white; margin-bottom:0;">
                </div>

                <div style="text-align:center; font-weight:bold; color:#999; margin: 10px 0;">ATAU</div>

                <div style="background: #eff6ff; padding: 15px; border: 1px solid #bfdbfe; border-radius: 8px; margin-bottom: 20px;">
                    <span style="font-size:0.9rem; font-weight:bold; color:#1e40af; display:block; margin-bottom:5px;">Opsi B: Gunakan Link Gambar (URL)</span>
                    <input type="url" name="image_url" placeholder="https://..." value="<?= $is_url ? e($dest['image']) : '' ?>" style="margin-bottom:0; background:white;">
                </div>
                
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