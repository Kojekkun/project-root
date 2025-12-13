<?php
// public/tour_detail.php
require_once __DIR__ . '/../app/db.php';
require_once __DIR__ . '/../app/helpers.php';

$id = $_GET['id'] ?? 0;

// Ambil data Tour
$stmt = $pdo->prepare('SELECT * FROM tours WHERE id = ?');
$stmt->execute([$id]);
$tour = $stmt->fetch();

if (!$tour) {
    header('Location: index.php');
    exit;
}
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title><?= e($tour['title']) ?> - Detail Paket</title>
    <link rel="stylesheet" href="assets/css/style.css?v=5">
</head>
<body>
    <nav class="nav">
        <a class="brand" href="index.php">Pariwisata</a>
        <div class="nav-right">
            <a href="index.php" style="text-decoration:none; color:#555;">Home</a>
            <a href="tours.php" style="text-decoration:none; color:#555; margin-left:15px;">Paket Tour</a>
        </div>
    </nav>

    <main class="container">
        <article class="card">
            <img src="uploads/<?= e($tour['image'] ?? 'placeholder.jpg') ?>" 
                 style="width: 100%; height: 400px; object-fit: cover; border-radius: 5px; margin-bottom: 20px;">
            
            <div style="display:flex; justify-content:space-between; align-items:flex-start; flex-wrap:wrap;">
                <div>
                    <h1 style="margin-bottom: 5px;"><?= e($tour['title']) ?></h1>
                    <p class="muted" style="margin-top:0;">Paket Perjalanan Seru</p>
                </div>
                <div style="background:#f0f9ff; padding:15px 25px; border-radius:10px; text-align:right;">
                    <small style="color:#555;">Harga per pax</small>
                    <h2 style="margin:0; color:#2563eb;">Rp <?= number_format($tour['price'], 0, ',', '.') ?></h2>
                </div>
            </div>

            <hr style="border: 0; border-top: 1px solid #eee; margin: 1.5rem 0;">

            <h3>📝 Deskripsi</h3>
            <div style="line-height: 1.8; color:#444;">
                <?= nl2br(e($tour['description'])) ?>
            </div>

            <br>

            <h3>🗓️ Jadwal Perjalanan (Itinerary)</h3>
            <div style="background:#f9fafb; padding:20px; border-radius:8px; border-left:4px solid #2563eb;">
                <?= nl2br(e($tour['itinerary'] ?? 'Jadwal akan diinfokan oleh admin.')) ?>
            </div>

            <br><br>

            <div style="display:flex; gap:10px;">
                <a href="https://wa.me/<?= e($tour['contact']) ?>?text=Halo admin, saya mau booking paket: <?= e($tour['title']) ?>" 
                   target="_blank" class="btn" style="background:#25d366; flex:1; text-align:center;">
                   📱 Booking via WhatsApp
                </a>
                <a href="index.php" class="btn" style="background:#6b7280;">Kembali</a>

                <?php if(!empty($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
                    <div style="margin-top: 10px; border-top: 1px dashed #ccc; padding-top: 10px;">
                        <a href="admin/tour_edit.php?id=<?= $tour['id'] ?>" class="btn" style="width: 100%; text-align: center; background: #f59e0b;">
                            ✏️ Edit Paket Tour Ini
                        </a>
                    </div>
                <?php endif; ?>

            </div>
        </article>
    </main>
</body>
</html>