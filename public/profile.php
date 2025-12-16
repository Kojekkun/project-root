<?php
// public/profile.php
require_once __DIR__ . '/../app/db.php';
require_once __DIR__ . '/../app/helpers.php';

// Pastikan login aktif kembali
require_login(); 

$user_id = $_SESSION['user_id'];

// 1. Ambil Data User
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

// 2. Ambil Riwayat
$sql_history = "
    SELECT 'booking' as type, b.id, t.title as item_name, b.amount as amount, b.status, b.created_at 
    FROM bookings b 
    JOIN tours t ON b.tour_id = t.id 
    WHERE b.user_id = ?
    
    UNION ALL
    
    SELECT 'topup' as type, id, 'Top Up Saldo' as item_name, amount, 'success' as status, created_at 
    FROM transactions 
    WHERE user_id = ? AND type = 'credit'
    
    ORDER BY created_at DESC LIMIT 10
";

try {
    $stmt_hist = $pdo->prepare($sql_history);
    $stmt_hist->execute([$user_id, $user_id]);
    $history = $stmt_hist->fetchAll();
} catch (Exception $e) {
    $history = [];
}
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Profil Saya - Pariwisata</title>
    <link rel="stylesheet" href="assets/css/style.css?v=22">
    <style>
        .profile-grid { display: grid; grid-template-columns: 350px 1fr; gap: 30px; align-items: start; }
        @media (max-width: 900px) { .profile-grid { grid-template-columns: 1fr; } }
        /* PERBAIKAN CSS DISINI: Menambahkan margin auto agar gambar ke tengah */
        .avatar-lg { 
            width: 120px; height: 120px; border-radius: 50%; object-fit: cover; 
            border: 4px solid white; box-shadow: 0 10px 25px rgba(0,0,0,0.1); 
            margin: 0 auto 20px auto; /* KUNCI PERBAIKAN: margin auto */
            display: block;
        }
        .balance-card { background: linear-gradient(135deg, var(--primary), #1e293b); color: white; padding: 25px; border-radius: 20px; margin-top: 20px; position: relative; overflow: hidden; }
        .balance-card::after { content: ''; position: absolute; top: -50px; right: -50px; width: 150px; height: 150px; background: rgba(255,255,255,0.1); border-radius: 50%; }
        .table-container { overflow-x: auto; }
        .table { width: 100%; border-collapse: collapse; min-width: 500px; }
        .table th { text-align: left; padding: 15px; color: var(--secondary); font-size: 0.85rem; font-weight: 700; border-bottom: 1px solid #f1f5f9; }
        .table td { padding: 15px; border-bottom: 1px solid #f8fafc; font-size: 0.95rem; }
        .status-badge { padding: 5px 12px; border-radius: 50px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; }
        .status-success { background: #dcfce7; color: #166534; }
        .status-pending { background: #fef9c3; color: #854d0e; }
        .status-failed { background: #fee2e2; color: #991b1b; }
        .type-badge { font-weight: 600; font-size: 0.8rem; padding: 4px 10px; border-radius: 8px; }
        .type-booking { background: #e0f2fe; color: #075985; }
        .type-topup { background: #f3e8ff; color: #6b21a8; }
    </style>
</head>
<body>
    
    <nav class="nav">
        <a class="brand" href="index.php">Pariwisata.</a>
        <div class="nav-right">
            <a href="index.php" class="nav-link">Beranda</a>
            <a href="tours.php" class="nav-link">Paket Tour</a>
            <a href="profile.php" class="nav-link active">Profil</a>
            <a href="logout.php" class="nav-link" style="color:#ef4444;">Logout</a>
        </div>
    </nav>

    <div class="page-header">
        <h1 class="page-title">Dashboard Saya</h1>
        <p class="page-desc">Kelola profil, saldo, dan lihat riwayat perjalanan Anda di sini.</p>
    </div>

    <main class="container">
        <?php if($m=flash_get('success')): ?><div class="alert alert-success">✅ <?= e($m) ?></div><?php endif; ?>
        <?php if($m=flash_get('error')): ?><div class="alert alert-danger">⚠️ <?= e($m) ?></div><?php endif; ?>

        <div class="profile-grid">
            
            <div>
                <div class="card" style="text-align:center; padding: 40px 30px;">
                    <?php 
                        $avatar_url = !empty($user['avatar']) ? get_image_url('avatars/' . $user['avatar']) : 'assets/images/placeholder.jpg';
                        // Fallback jika file tidak ada
                        if (strpos($avatar_url, 'http') === false && !file_exists($avatar_url)) $avatar_url = 'assets/images/placeholder.jpg';
                    ?>
                    <img src="<?= $avatar_url ?>" class="avatar-lg" alt="Avatar">
                    
                    <h2 style="margin:0; font-size: 1.5rem;"><?= e($user['name'] ?? 'User') ?></h2>
                    <p class="muted" style="margin-top:5px; font-size:0.9rem;"><?= e($user['email'] ?? '-') ?></p>
                    <p style="margin-top:5px;"><span class="card-tag" style="background:#f1f5f9; color:var(--text-main);">Member</span></p>

                    <div class="balance-card" style="text-align:left;">
                        <small style="opacity:0.8; font-size:0.85rem;">Saldo E-Wallet</small>
                        <h3 style="color:white; font-size:1.8rem; margin:5px 0 15px;">Rp <?= number_format($user['balance'] ?? 0, 0, ',', '.') ?></h3>
                        <a href="https://wa.me/6281234567890?text=Halo%20Admin,%20saya%20ingin%20Top%20Up" target="_blank" class="btn" style="background:white; color:var(--primary); width:100%; font-size:0.9rem;">+ Top Up Saldo</a>
                    </div>
                </div>

                <div class="card" style="margin-top:30px;">
                    <div class="card-body">
                        <h3 style="font-size:1.1rem; margin-bottom:20px; border-bottom:1px solid #f1f5f9; padding-bottom:10px;">⚙️ Edit Profil</h3>
                        <form action="profile_handler.php" method="post" enctype="multipart/form-data">
                            <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
                            <label>Ganti Nama</label>
                            <input type="text" name="name" value="<?= e($user['name'] ?? '') ?>" required>
                            <label>Ganti Password</label>
                            <input type="password" name="password" placeholder="Kosongkan jika tidak diganti">
                            <label>Ganti Foto</label>
                            <input type="file" name="avatar" accept="image/*" style="padding:10px; background:white;">
                            <button class="btn" style="width:100%; margin-top:10px;">Simpan Perubahan</button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
                        <h3 style="margin:0;">📜 Riwayat Aktivitas</h3>
                        <a href="history.php" style="font-size:0.9rem; font-weight:600; color:var(--accent);">Lihat Semua &rarr;</a>
                    </div>
                    <?php if(count($history) == 0): ?>
                        <div style="text-align:center; padding:40px; border:2px dashed #f1f5f9; border-radius:12px;">
                            <p class="muted">Belum ada transaksi.</p>
                            <a href="tours.php" class="btn" style="padding:8px 20px; font-size:0.9rem; margin-top:10px;">Mulai Petualangan</a>
                        </div>
                    <?php else: ?>
                        <div class="table-container">
                            <table class="table">
                                <thead><tr><th>AKTIVITAS</th><th>TANGGAL</th><th>NOMINAL</th><th>STATUS</th></tr></thead>
                                <tbody>
                                    <?php foreach($history as $h): ?>
                                    <tr>
                                        <td>
                                            <div style="display:flex; flex-direction:column; gap:4px;">
                                                <span style="font-weight:700; color:var(--primary);"><?= e($h['item_name']) ?></span>
                                                <span class="type-badge type-<?= $h['type'] ?>"><?= $h['type'] === 'booking' ? '🎫 Booking' : '💰 Top Up' ?></span>
                                            </div>
                                        </td>
                                        <td style="color:var(--secondary); font-size:0.9rem;"><?= date('d M Y', strtotime($h['created_at'])) ?><br><small><?= date('H:i', strtotime($h['created_at'])) ?></small></td>
                                        <td style="font-weight:600;">Rp <?= number_format($h['amount'], 0, ',', '.') ?></td>
                                        <td><span class="status-badge status-<?= ($h['status'] == 'success' || $h['status'] == 'paid' || $h['status'] == 'confirmed') ? 'success' : (($h['status'] == 'failed') ? 'failed' : 'pending') ?>"><?= e($h['status']) ?></span></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>
    <footer><h3 class="brand">Pariwisata.</h3><p class="muted">&copy; <?= date('Y') ?> Pariwisata Inc.</p></footer>
</body>
</html>