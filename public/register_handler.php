<?php
// public/register_handler.php (VERSI OTP)
require_once __DIR__ . '/../app/db.php';
require_once __DIR__ . '/../app/helpers.php';
require_once __DIR__ . '/../app/mailer.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: register.php'); exit;
}

if (!csrf_check($_POST['csrf'] ?? '')) {
    flash_set('error', 'Permintaan tidak valid (CSRF).');
    header('Location: register.php'); exit;
}

$name = trim($_POST['name'] ?? '');
$email = trim(strtolower($_POST['email'] ?? ''));
$password = $_POST['password'] ?? '';

if (empty($name) || empty($email) || empty($password) || strlen($password) < 6) {
    flash_set('error', 'Semua kolom wajib diisi dan password minimal 6 karakter.');
    header('Location: register.php'); exit;
}

// Cek Email Duplikat
$stmt = $pdo->prepare('SELECT id FROM users WHERE email = ?');
$stmt->execute([$email]);
if ($stmt->fetch()) {
    flash_set('error', 'Email sudah terdaftar. Silakan login.');
    header('Location: register.php'); exit;
}

// Enkripsi Password
$hashed_password = password_hash($password, PASSWORD_BCRYPT);

// --- PERUBAHAN: GENERATE OTP 6 DIGIT ---
$otp_code = rand(100000, 999999); 

try {
    // Simpan OTP ke kolom 'activation_token' (kita pakai ulang kolom ini)
    $stmt = $pdo->prepare('INSERT INTO users (name, email, password, activation_token, status, role, created_at) VALUES (?, ?, ?, ?, "pending", "user", NOW())');
    $stmt->execute([$name, $email, $hashed_password, $otp_code]);
    
    // Kirim OTP via Email (Simulasi)
    send_activation_otp($email, $otp_code);

    // Simpan email di sesi agar di halaman verify tidak perlu ketik ulang
    $_SESSION['pending_email'] = $email;

    flash_set('success', 'Registrasi berhasil! Masukkan kode OTP yang telah dikirim ke email Anda.');
    
    // Arahkan ke Halaman Verifikasi
    header('Location: verify.php');
    exit;

} catch (PDOException $e) {
    error_log("Register Error: " . $e->getMessage());
    flash_set('error', 'Gagal mendaftar. Silakan coba lagi.');
    header('Location: register.php');
    exit;
}