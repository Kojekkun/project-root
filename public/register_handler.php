<?php
// public/register_handler.php (HANDLER REGISTRASI YANG HILANG)
require_once __DIR__ . '/../app/db.php';
require_once __DIR__ . '/../app/helpers.php';
require_once __DIR__ . '/../app/mailer.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /register.php');
    exit;
}

// 1. Validasi CSRF
if (!csrf_check($_POST['csrf'] ?? '')) {
    flash_set('error', 'Permintaan tidak valid (CSRF).');
    header('Location: /register.php');
    exit;
}

$name = trim($_POST['name'] ?? '');
$email = trim(strtolower($_POST['email'] ?? ''));
$password = $_POST['password'] ?? '';

// Validasi Input
if (empty($name) || empty($email) || empty($password) || strlen($password) < 6) {
    flash_set('error', 'Semua kolom wajib diisi dan password minimal 6 karakter.');
    header('Location: /register.php');
    exit;
}

// Cek apakah email sudah terdaftar
$stmt = $pdo->prepare('SELECT id FROM users WHERE email = ?');
$stmt->execute([$email]);
if ($stmt->fetch()) {
    flash_set('error', 'Email sudah terdaftar. Silakan login atau gunakan email lain.');
    header('Location: /register.php');
    exit;
}

// ENKRIPSI PASSWORD (Ketentuan 4)
$hashed_password = password_hash($password, PASSWORD_BCRYPT);

// Generate Token Aktivasi (Ketentuan 3)
$activation_token = generate_token(50); 

try {
    // Status awal adalah 'pending'
    $stmt = $pdo->prepare('INSERT INTO users (name, email, password, activation_token, status, role, created_at) VALUES (?, ?, ?, ?, "pending", "user", NOW())');
    $stmt->execute([$name, $email, $hashed_password, $activation_token]);
    
    // Kirim Email Aktivasi (Ketentuan 3)
    if (send_activation_email($email, $activation_token)) {
        flash_set('success', 'Pendaftaran berhasil! Silakan cek email Anda untuk tautan aktivasi.');
    } else {
        // Log error email, tapi tetap berhasilkan registrasi
        flash_set('error', 'Pendaftaran berhasil, tetapi gagal mengirim email aktivasi. Silakan hubungi dukungan.');
    }
} catch (PDOException $e) {
    error_log("Registration failed: " . $e->getMessage());
    flash_set('error', 'Terjadi kesalahan sistem saat mendaftar.');
}

header('Location: /login.php');
exit;