<?php
// public/admin/tour_edit.php
require_once __DIR__ . '/../../app/helpers.php';
require_once __DIR__ . '/../../app/db.php';

require_login();

// 1. Cek Admin
if (($_SESSION['user_role'] ?? '') !== 'admin') {
    header('Location: ../index.php');
    exit;
}

$id = $_GET['id'] ?? 0;

// 2. Ambil Data Tour
$stmt = $pdo->prepare("SELECT * FROM tours WHERE id = ?");
$stmt->execute([$id]);
$tour = $stmt->fetch();

if (!$tour) { header('Location: ../tours.php'); exit; }

// 3. Ambil Data Destinasi (Untuk Dropdown Relasi)
$stmt_dest = $pdo->query("SELECT id, title FROM destinations");
$destinations = $stmt_dest->fetchAll();
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Edit Tour - <?= e($tour['title']) ?></title>
    <link rel="stylesheet" href="../assets/css/style.css?v=5">
</head>
<body>
    <nav class="nav">
        <a class="brand" href="../index.php">Admin Panel</a>
        <div class="nav-right">
            <a href="../tour_detail.php?id=<?= $tour['id'] ?>" style="text-decoration:none; color:#555;">&larr; Batal</a>
        </div>
    </nav>

    <main class="container">
        <div class="card" style="max-width: 700px; margin: 0 auto;">
            <h2>✏️ Edit Paket Tour</h2>
            
            <?php if($m=flash_get('error')): ?>
                <div class="alert alert-danger"><?= e($m) ?></div>
            <?php endif; ?>

            <form action="process_tour_edit.php" method="post" enctype="multipart/form-data">
                <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
                <input type="hidden" name="id" value="<?= $tour['id'] ?>">
                
                <label>Nama Paket Tour</label>
                <input name="title" required value="<?= e($tour['title']) ?>">
                
                <label>Hubungkan dengan Destinasi (Opsional)</label>
                <select name="destination_id">
                    <option value="">-- Tidak Terhubung --</option>
                    <?php foreach($destinations as $d): ?>
                        <option value="<?= $d['id'] ?>" <?= $d['id'] == $tour['destination_id'] ? 'selected' : '' ?>>
                            <?= e($d['title']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                
                <label>Harga (Rp)</label>
                <input type="number" name="price" required value="<?= e($tour['price']) ?>">
                
                <label>Nomor WhatsApp Kontak (Format: 628...)</label>
                <input type="number" name="contact" required value="<?= e($tour['contact']) ?>">

                <label>Deskripsi Singkat</label>
                <textarea name="description" rows="3" required><?= e($tour['description']) ?></textarea>

                <label>Jadwal Perjalanan (Itinerary)</label>
                <textarea name="itinerary" rows="6" placeholder="Hari 1: ... &#10;Hari 2: ..."><?= e($tour['itinerary']) ?></textarea>
                
                <label>Foto Saat Ini</label>
                <div style="margin-bottom: 10px;">
                    <img src="../uploads/<?= e($tour['image'] ?? 'placeholder.jpg') ?>" style="height: 100px; border-radius: 4px;">
                </div>
                
                <label>Ganti Foto (Opsional)</label>
                <input type="file" name="image" accept="image/*">
                
                <button class="btn" style="width:100%; margin-top:20px;">Simpan Perubahan</button>
            </form>
            
            <hr style="margin: 30px 0; border:0; border-top:1px dashed #ccc;">
            
            <form action="process_tour_delete.php" method="post" onsubmit="return confirm('Yakin ingin menghapus paket tour ini?');">
                <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
                <input type="hidden" name="id" value="<?= $tour['id'] ?>">
                <button style="background: #fee2e2; color: #b91c1c; width: 100%; border: 1px solid #fca5a5; padding: 12px; border-radius: 5px; cursor: pointer; font-weight:bold;">
                    🗑️ Hapus Paket Tour
                </button>
            </form>
        </div>
    </main>
</body>
</html>