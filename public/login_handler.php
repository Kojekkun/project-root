<?php
// public/login_handler.php
require_once __DIR__ . '/../app/db.php';
require_once __DIR__ . '/../app/helpers.php';

// Pastikan method POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.php');
    exit;
}

// Cek CSRF
if (!csrf_check($_POST['csrf'] ?? '')) {
    flash_set('error', 'Permintaan tidak valid (CSRF).');
    header('Location: login.php');
    exit;
}

$email = trim(strtolower($_POST['email'] ?? ''));
$password = $_POST['password'] ?? '';

// Fungsi Gagal Login
function login_fail() {
    flash_set('error', 'Email atau password salah.');
    session_write_close(); // KUNCI: Simpan sesi error sebelum pindah
    header('Location: login.php');
    exit;
}

// Cek User di Database
$stmt = $pdo->prepare('SELECT id, name, password, status, role, avatar FROM users WHERE email = ?');
$stmt->execute([$email]);
$user = $stmt->fetch();

if (!$user) {
    login_fail(); 
}

if (!password_verify($password, $user['password'])) {
    login_fail(); 
}

if (($user['status'] ?? '') !== 'active') {
    flash_set('error', 'Akun Anda belum diaktivasi. Silakan cek email Anda.');
    session_write_close();
    header('Location: login.php');
    exit;
}

// --- LOGIN SUKSES ---
$_SESSION['user_id'] = $user['id'];
$_SESSION['user_name'] = $user['name'];
$_SESSION['user_role'] = $user['role']; 
$_SESSION['user_avatar'] = $user['avatar'];

// Simpan sesi sukses sebelum redirect
session_write_close();

header('Location: login_success.php');
exit;