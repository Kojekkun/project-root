<?php
// public/login_handler.php (BARU: Menangani login & verifikasi aktivasi)
require_once __DIR__ . '/../app/db.php';
require_once __DIR__ . '/../app/helpers.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /login.php');
    exit;
}

if (!csrf_check($_POST['csrf'] ?? '')) {
    flash_set('error', 'Permintaan tidak valid (CSRF).');
    header('Location: /login.php');
    exit;
}

$email = trim(strtolower($_POST['email'] ?? ''));
$password = $_POST['password'] ?? '';

function login_fail() {
    flash_set('error', 'Email atau password salah.');
    header('Location: /login.php');
    exit;
}

$stmt = $pdo->prepare('SELECT id, name, password, status, role FROM users WHERE email = ?');
$stmt->execute([$email]);
$user = $stmt->fetch();

if (!$user) {
    login_fail(); // Email tidak ditemukan
}

// Verifikasi Password
if (!password_verify($password, $user['password'])) {
    login_fail(); // Password salah
}

// Cek Status Aktivasi (Ketentuan 5)
if (($user['status'] ?? '') !== 'active') {
    flash_set('error', 'Akun Anda belum diaktivasi. Silakan cek email Anda.');
    header('Location: /login.php');
    exit;
}

// Berhasil Login: Buat Sesi
session_regenerate_id(true); 
$_SESSION['user_id'] = $user['id'];
$_SESSION['user_name'] = $user['name'];
$_SESSION['user_role'] = $user['role']; 

header('Location: /');
exit;