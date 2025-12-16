<?php
// public/admin/topup.php
require_once __DIR__ . '/../../app/helpers.php';
require_once __DIR__ . '/../../app/db.php';

require_login();

// Cek Admin
if (($_SESSION['user_role'] ?? '') !== 'admin') {
    header('Location: ../index.php'); exit;
}

// 1. AMBIL SALDO ADMIN (Logika Baru)
$stmt_admin = $pdo->prepare("SELECT balance FROM users WHERE id = ?");
$stmt_admin->execute([$_SESSION['user_id']]);
$admin_data = $stmt_admin->fetch();
$admin_balance = $admin_data['balance'] ?? 0;

// 2. Ambil user lain
$stmt = $pdo->query("SELECT id, name, email, balance FROM users WHERE role = 'user' ORDER BY name ASC");
$users = $stmt->fetchAll();
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Kirim Saldo ke User</title>
    <link rel="stylesheet" href="../assets/css/style.css?v=5">
</head>
<body>
    <nav class="nav">
        <a class="brand" href="../index.php">Admin Panel</a>
        <div class="nav-right">
            <a href="../index.php" style="text-decoration:none; color:#555;">&larr; Kembali</a>
        </div>
    </nav>

    <main class="container">
        <div class="card" style="max-width: 600px; margin: 0 auto;">
            <h2 style="color:#2563eb;">💸 Kirim Saldo (Top Up)</h2>
            <p class="muted">Saldo akan dipotong dari akun Anda dan dikirim ke user.</p>
            
            <div style="background: #eff6ff; border: 1px solid #bfdbfe; padding: 15px; border-radius: 8px; margin-bottom: 20px; display:flex; justify-content:space-between; align-items:center;">
                <span style="color:#1e40af; font-weight:bold;">Dompet Admin:</span>
                <span style="color:#2563eb; font-weight:bold; font-size: 1.2rem;">Rp <?= number_format($admin_balance, 0, ',', '.') ?></span>
            </div>

            <?php if($m=flash_get('success')): ?>
                <div class="alert alert-success"><?= e($m) ?></div>
            <?php endif; ?>
            <?php if($m=flash_get('error')): ?>
                <div class="alert alert-danger"><?= e($m) ?></div>
            <?php endif; ?>

            <form action="process_topup.php" method="post">
                <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
                
                <label>Pilih User Penerima</label>
                <select name="user_id" required style="padding:12px;">
                    <option value="">-- Pilih User --</option>
                    <?php foreach($users as $u): ?>
                        <option value="<?= $u['id'] ?>">
                            <?= e($u['name']) ?> (Saldo: Rp <?= number_format($u['balance'], 0, ',', '.') ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
                
                <label>Jumlah Saldo (Rp)</label>
                <input type="number" name="amount" min="1000" max="<?= $admin_balance ?>" required placeholder="Contoh: 100000">
                <p class="muted" style="font-size:0.8rem; margin-top:-5px;">Maksimal: Rp <?= number_format($admin_balance, 0, ',', '.') ?></p>
                
                <label>Catatan Transaksi</label>
                <input name="description" value="Top Up dari Admin" required>
                
                <button class="btn" style="width:100%; margin-top:10px; background:#22c55e;">Kirim Saldo Sekarang</button>
            </form>
        </div>
    </main>
</body>
</html>