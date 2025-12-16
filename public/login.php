<?php 
require_once __DIR__ . '/../app/helpers.php'; 
if(is_logged()) { header('Location: index.php'); exit; }
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Masuk - Travel Buddies</title>
    <link rel="stylesheet" href="assets/css/style.css?v=15">
</head>
<body class="auth-body">
    
    <div class="auth-card">
        <h2 class="auth-title">Welcome Back!</h2>
        <p class="auth-subtitle">Masuk untuk melanjutkan petualanganmu.</p>

        <?php if($m=flash_get('error')): ?>
            <div class="alert alert-danger"><?= e($m) ?></div>
        <?php endif; ?>
        <?php if($m=flash_get('success')): ?>
            <div class="alert alert-success"><?= e($m) ?></div>
        <?php endif; ?>

        <form method="post" action="login_handler.php" style="text-align:left;">
            <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
            
            <label>Email Address</label>
            <input name="email" type="email" required placeholder="nama@email.com">
            
            <label>Password</label>
            <input name="password" type="password" required placeholder="••••••••">
            
            <button class="btn" style="width:100%; margin-top:10px; border-radius:50px; padding:14px;">Masuk Sekarang</button>
        </form>
        
        <div style="margin-top: 25px; font-size: 0.9rem; color: var(--secondary);">
            Belum punya akun? <a href="register.php" style="color:var(--accent); font-weight:700;">Daftar disini</a>
            <br><br>
            <a href="index.php" style="color: var(--text-main); text-decoration: none; font-weight:500;">&larr; Kembali ke Beranda</a>
        </div>
    </div>

</body>
</html>