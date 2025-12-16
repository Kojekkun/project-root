<?php
require_once __DIR__ . '/../../app/helpers.php';
require_once __DIR__ . '/../../app/db.php';

require_login();

if (($_SESSION['user_role'] ?? '') !== 'admin') {
    header('Location: ../index.php'); exit;
}

$stmt = $pdo->query("SELECT * FROM categories ORDER BY name ASC");
$categories = $stmt->fetchAll();
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Tambah Destinasi</title>
    <link rel="stylesheet" href="../assets/css/style.css?v=7">
</head>
<body>
    <nav class="nav">
        <a class="brand" href="../index.php">Admin Panel</a>
        <div class="nav-right">
            <a href="../destinations.php" style="text-decoration:none; color:#555;">← Kembali</a>
        </div>
    </nav>

    <main class="container">
        <div class="card" style="max-width: 600px; margin: 0 auto;">
            <h2>+ Tambah Wisata Baru</h2>
            
            <?php if($m=flash_get('error')): ?>
                <div class="alert alert-danger"><?= e($m) ?></div>
            <?php endif; ?>

            <form action="process_add.php" method="post" enctype="multipart/form-data">
                <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
                
                <label>Nama Destinasi</label>
                <input name="title" required placeholder="Contoh: Pulau Derawan">
                
                <label>Kategori</label>
                <select name="category_id" required>
                    <option value="">-- Pilih Kategori --</option>
                    <?php foreach($categories as $c): ?>
                        <option value="<?= $c['id'] ?>"><?= e($c['name']) ?></option>
                    <?php endforeach; ?>
                </select>
                
                <label>Lokasi (Teks)</label>
                <input name="location" required placeholder="Contoh: Berau, Kalimantan Timur">
                
                <label>Deskripsi</label>
                <textarea name="description" rows="5" required></textarea>

                <label>Google Maps Embed HTML (Opsional)</label>
                <textarea name="map_embed" rows="3" placeholder='<iframe src="...'></iframe>'></textarea>

                <hr style="margin: 20px 0; border:0; border-top:1px dashed #ddd;">

                <label>Foto Utama (Pilih Salah Satu)</label>
                
                <div style="background: #f0fdf4; padding: 15px; border: 1px solid #bbf7d0; border-radius: 8px; margin-bottom: 10px;">
                    <span style="font-size:0.9rem; font-weight:bold; color:#166534; display:block; margin-bottom:5px;">Opsi A: Upload File</span>
                    <input type="file" name="image" accept="image/*" style="background:white; margin-bottom:0;">
                </div>

                <div style="text-align:center; font-weight:bold; color:#999; margin: 10px 0;">ATAU</div>

                <div style="background: #eff6ff; padding: 15px; border: 1px solid #bfdbfe; border-radius: 8px; margin-bottom: 20px;">
                    <span style="font-size:0.9rem; font-weight:bold; color:#1e40af; display:block; margin-bottom:5px;">Opsi B: Link Gambar (URL)</span>
                    <input type="url" name="image_url" placeholder="https://..." style="margin-bottom:0; background:white;">
                </div>
                
                <button class="btn" style="width:100%;">Simpan Data</button>
            </form>
        </div>
    </main>
</body>
</html>