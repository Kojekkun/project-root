<?php
// public/tour_detail.php
require_once __DIR__ . '/../app/db.php';
require_once __DIR__ . '/../app/helpers.php';

$id = $_GET['id'] ?? 0;

$sql = "SELECT t.*, d.image as dest_image, d.title as dest_title 
        FROM tours t 
        LEFT JOIN destinations d ON t.destination_id = d.id 
        WHERE t.id = ?";

$stmt = $pdo->prepare($sql);
$stmt->execute([$id]);
$tour = $stmt->fetch();

if (!$tour) {
    header('Location: index.php');
    exit;
}

$final_image = !empty($tour['dest_image']) ? $tour['dest_image'] : $tour['image'];
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title><?= e($tour['title']) ?> - Detail Paket</title>
    <link rel="stylesheet" href="assets/css/style.css?v=10">
</head>
<body>
    <nav class="nav">
        <a class="brand" href="index.php">Pariwisata</a>
        <div class="nav-right">
            <a href="index.php" style="text-decoration:none; color:var(--text-main);">Beranda</a>
            <a href="tours.php" style="text-decoration:none; color:var(--text-main); font-weight:bold;">Paket Tour</a>
            <?php if(is_logged()): ?>
                <div class="nav-separator"></div>
                <?php if($_SESSION['user_role'] === 'admin'): ?>
                    <a href="admin/index.php" class="btn" style="padding: 8px 15px; font-size: 0.8rem; background: var(--secondary);">⚙️ Admin Panel</a>
                <?php endif; ?>
                <a href="profile.php" class="nav-profile">
                    <?php 
                        $avatar_url = !empty($_SESSION['user_avatar']) ? get_image_url('avatars/' . $_SESSION['user_avatar']) : 'assets/images/placeholder.jpg';
                        if (strpos($avatar_url, 'http') === false && !file_exists($avatar_url)) $avatar_url = 'assets/images/placeholder.jpg';
                    ?>
                    <img src="<?= $avatar_url ?>" class="nav-avatar" alt="Profil">
                    <span class="nav-name"><?= e($_SESSION['user_name']) ?></span>
                </a>
                <div class="nav-separator"></div>
                <a href="logout.php" class="btn-logout">Logout</a>
            <?php else: ?>
                <span style="margin: 0 10px; color:#ccc;">|</span>
                <a href="login.php" style="color:var(--text-main);">Masuk</a>
                <a href="register.php" class="btn">Daftar</a>
            <?php endif; ?>
        </div>
    </nav>

    <main class="container">
        <article class="card">
            <div style="position: relative; border-radius: 16px; overflow: hidden; margin-bottom: 25px;">
                <img src="<?= get_image_url($final_image) ?>" 
                     style="width: 100%; height: 400px; object-fit: cover;">
                     
                <div style="position: absolute; bottom: 0; left: 0; right: 0; background: linear-gradient(to top, rgba(0,0,0,0.8), transparent); padding: 30px;">
                    <small style="color: #fbbf24; font-weight: bold; font-size: 0.9rem;">
                        <?= e($tour['dest_title'] ?? 'Paket Wisata') ?>
                    </small>
                    <h1 style="color: white; margin: 5px 0 0; text-shadow: 0 2px 4px rgba(0,0,0,0.5);">
                        <?= e($tour['title']) ?>
                    </h1>
                </div>
            </div>
            
            <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; margin-bottom: 30px;">
                <div>
                    <h2 style="margin:0; font-size: 1.5rem; color: var(--secondary);">Informasi Paket</h2>
                    <p class="muted" style="margin-top:5px;">Durasi dan fasilitas menyesuaikan jadwal.</p>
                </div>
                <div style="background:#f0f9ff; padding:15px 30px; border-radius:50px; text-align:right; border: 1px solid #bae6fd;">
                    <small style="color:#0369a1; font-weight:bold;">Harga per pax</small>
                    <h2 style="margin:0; color:#0284c7; font-size: 1.8rem;">Rp <?= number_format($tour['price'], 0, ',', '.') ?></h2>
                </div>
            </div>

            <hr style="border: 0; border-top: 1px dashed #eee; margin: 1.5rem 0;">

            <h3>📝 Deskripsi</h3>
            <div style="line-height: 1.8; color:#4b5563; font-size: 1.05rem;">
                <?= nl2br(e($tour['description'])) ?>
            </div>

            <br>

            <h3>🗓️ Jadwal Perjalanan (Itinerary)</h3>
            <div style="background:#f9fafb; padding:25px; border-radius:12px; border-left:4px solid #2563eb; line-height: 1.8;">
                <?= nl2br(e($tour['itinerary'] ?? 'Hubungi admin untuk detail jadwal.')) ?>
            </div>

            <br>

            <div style="background: #fff; padding: 30px; border-radius: 16px; border: 1px solid #e5e7eb; box-shadow: 0 10px 25px rgba(0,0,0,0.05);">
                <h3 style="margin-top:0;">🛒 Pesan Sekarang</h3>
                
                <?php if(is_logged()): ?>
                    <?php
                        $stmt_u = $pdo->prepare("SELECT balance FROM users WHERE id = ?");
                        $stmt_u->execute([$_SESSION['user_id']]);
                        $curr_balance = $stmt_u->fetchColumn();
                    ?>

                    <form action="process_booking.php" method="post" onsubmit="return confirm('Yakin ingin membayar paket ini dengan saldo Anda?');">
                        <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
                        <input type="hidden" name="tour_id" value="<?= $tour['id'] ?>">
                        
                        <div style="margin-bottom: 20px;">
                            <label style="display:block; font-weight:bold; margin-bottom:8px;">Pilih Tanggal Keberangkatan</label>
                            <input type="date" name="tour_date" required min="<?= date('Y-m-d') ?>" style="width:100%; padding:12px; border:1px solid #ddd; border-radius:8px;">
                        </div>

                        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; font-size:1rem; background: #f8fafc; padding: 15px; border-radius: 8px;">
                            <span>Saldo Anda Saat Ini:</span>
                            <span style="font-weight:bold; font-size: 1.1rem; <?= $curr_balance < $tour['price'] ? 'color:#ef4444;' : 'color:#16a34a;' ?>">
                                Rp <?= number_format($curr_balance, 0, ',', '.') ?>
                            </span>
                        </div>

                        <?php if($curr_balance >= $tour['price']): ?>
                            <button class="btn" style="width:100%; background:#2563eb; font-size: 1.1rem; padding: 15px;">
                                💳 Bayar & Booking (Rp <?= number_format($tour['price'], 0, ',', '.') ?>)
                            </button>
                        <?php else: ?>
                            <div class="alert alert-danger" style="margin-bottom:0; text-align:center;">
                                Saldo tidak cukup. <br>
                                <a href="https://wa.me/<?= e($tour['contact']) ?>" target="_blank" style="color:#721c24; text-decoration:underline; font-weight:bold;">Hubungi Admin untuk Top Up</a>
                            </div>
                        <?php endif; ?>
                    </form>

                <?php else: ?>
                    <div style="text-align: center; padding: 20px;">
                        <p class="muted" style="margin-bottom: 20px;">Silakan login terlebih dahulu untuk memesan paket ini.</p>
                        <a href="login.php" class="btn" style="display:inline-block; padding: 12px 30px;">Login untuk Memesan</a>
                    </div>
                <?php endif; ?>
            </div>

            <br>
            
            <div style="display:flex; gap:15px; margin-top: 20px;">
                 <a href="tours.php" class="btn" style="background:#fff; color: #555; border: 1px solid #ccc; flex:1; text-align:center;">
                    &larr; Kembali
                 </a>
                 
                 <a href="https://wa.me/<?= e($tour['contact']) ?>" target="_blank" class="btn" style="background:#25d366; flex:2; text-align:center; display:flex; align-items:center; justify-content:center; gap: 8px;">
                   <span>💬</span> Tanya Admin via WhatsApp
                </a>
            </div>

            <?php if(!empty($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
                <div style="margin-top: 30px; border-top: 1px dashed #ccc; padding-top: 20px;">
                    <a href="admin/tour_edit.php?id=<?= $tour['id'] ?>" class="btn" style="width: 100%; text-align: center; background: #f59e0b;">
                        ✏️ Edit Paket Tour Ini (Admin)
                    </a>
                </div>
            <?php endif; ?>

        </article>
    </main>
    
    <footer style="background:var(--bg-card); padding:40px 20px; text-align:center; border-top:1px solid #eee; margin-top:80px;">
        <h3 class="brand" style="font-size:1.5rem; margin-bottom:10px;">Pariwisata.</h3>
        <p class="muted">&copy; <?= date('Y') ?> Pariwisata.</p>
    </footer>
</body>
</html>