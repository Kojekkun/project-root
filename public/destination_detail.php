<?php
// public/destination_detail.php

// 1. Tampilkan Semua Error (Agar ketahuan jika ada salah ketik)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h1>Mode Debugging</h1>";

// 2. Cek Koneksi File
echo "<p>Mengambil file database...</p>";
require_once __DIR__ . '/../app/db.php';
require_once __DIR__ . '/../app/helpers.php';
echo "<p>✅ File database terhubung.</p>";

// 3. Cek ID dari URL
$id = $_GET['id'] ?? 'KOSONG';
echo "<p>ID yang diterima dari URL: <strong>" . htmlspecialchars($id) . "</strong></p>";

if ($id === 'KOSONG' || $id == 0) {
    die("<h2 style='color:red'>❌ Error: ID tidak ditemukan di URL.</h2><p>Pastikan link di halaman depan formatnya: destination_detail.php?id=1</p>");
}

// 4. Cek Data di Database
echo "<p>Mencari data ke Azure Database...</p>";
try {
    $stmt = $pdo->prepare('SELECT d.*, c.name as category FROM destinations d LEFT JOIN categories c ON d.category_id = c.id WHERE d.id = ?');
    $stmt->execute([$id]);
    $destination = $stmt->fetch();

    if (!$destination) {
        die("<h2 style='color:red'>❌ Data Kosong!</h2><p>Tidak ada wisata dengan ID $id di database.</p>");
    } else {
        echo "<h2 style='color:green'>✅ Data Ditemukan!</h2>";
        echo "Nama Wisata: " . htmlspecialchars($destination['title']);
        echo "<br>Gambar: " . htmlspecialchars($destination['image']);
        echo "<hr>Jika tulisan ini muncul, berarti logika database BENAR. Masalahnya mungkin di HTML bawahnya.";
    }
} catch (Exception $e) {
    die("<h2 style='color:red'>❌ Error Database:</h2>" . $e->getMessage());
}

// ---------------------------------------------------------
// JIKA SUKSES SAMPAI SINI, BARU TAMPILKAN HTML
// ---------------------------------------------------------
?>

<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Debug Detail</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="container" style="margin-top: 50px;">
        <article class="card">
            <img src="uploads/<?= e($destination['image'] ?? 'placeholder.jpg') ?>" 
                 alt="<?= e($destination['title']) ?>" 
                 style="width: 100%; height: 300px; object-fit: cover;">
            
            <h1><?= e($destination['title']) ?></h1>
            <p>Lokasi: <?= e($destination['location']) ?></p>
            <p><?= nl2br(e($destination['description'])) ?></p>
            
            <br>
            <a href="index.php" class="btn">Kembali</a>
        </article>
    </div>
</body>
</html>