<?php
// public/verify.php
require_once __DIR__ . '/../app/db.php';
require_once __DIR__ . '/../app/helpers.php';

// Jika ada POST, berarti user mengirim OTP
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $otp = trim($_POST['otp'] ?? '');

    // Cari user dengan Email & OTP yang cocok
    $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ? AND activation_token = ? AND status = "pending"');
    $stmt->execute([$email, $otp]);
    $user = $stmt->fetch();

    if ($user) {
        // SUKSES: Aktifkan akun & Hapus OTP
        $pdo->prepare('UPDATE users SET status="active", activation_token=NULL WHERE id=?')->execute([$user['id']]);
        
        // Hapus sesi email pending
        unset($_SESSION['pending_email']);
        
        header('Location: verification_success.php');
        exit;
    } else {
        flash_set('error', 'Kode OTP salah atau email tidak ditemukan.');
    }
}

// Ambil email dari sesi (jika ada) biar user gak capek ngetik
$pending_email = $_SESSION['pending_email'] ?? '';
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Verifikasi OTP</title>
    <link rel="stylesheet" href="assets/css/style.css?v=5">
</head>
<body>
    <main class="container">
        <div class="card" style="max-width: 400px; margin: 50px auto; text-align: center;">
            <h2>🔐 Verifikasi Akun</h2>
            <p class="muted">Masukkan 6 digit kode OTP yang telah dikirim ke email Anda.</p>
            
            <?php if($m=flash_get('error')): ?>
                <div class="alert alert-danger"><?= e($m) ?></div>
            <?php endif; ?>
            
            <?php if($m=flash_get('success')): ?>
                <div class="alert alert-success"><?= e($m) ?></div>
            <?php endif; ?>

            <form method="post">
                <div style="text-align: left;">
                    <label>Email Anda</label>
                    <input name="email" value="<?= e($pending_email) ?>" required placeholder="email@contoh.com">
                </div>

                <div style="text-align: left;">
                    <label>Kode OTP (6 Digit)</label>
                    <input name="otp" type="number" required placeholder="Contoh: 123456" style="font-size: 1.5rem; letter-spacing: 5px; text-align: center;">
                </div>

                <button class="btn" style="width: 100%; margin-top: 10px;">Verifikasi Sekarang</button>
            </form>
            
            <br>
            <small>Tidak menerima kode? <a href="register.php">Daftar Ulang</a></small>
        </div>
    </main>
</body>
</html>