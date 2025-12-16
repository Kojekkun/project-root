<?php
// public/activate.php (DIKOREKSI)
require_once __DIR__ . '/../app/db.php';
require_once __DIR__ . '/../app/helpers.php';

// Cek jika sudah login, alihkan
if(is_logged()){
    header('Location: /');
    exit;
}

$token = $_GET['token'] ?? '';
if (!$token) {
    die('Token tidak ditemukan.');
}

$stmt = $pdo->prepare('SELECT id, status FROM users WHERE activation_token = ?');
$stmt->execute([$token]);
$user = $stmt->fetch(); // Variabel yang benar: $user

if (!$user) { // Menggunakan $user
    flash_set('error', 'Token aktivasi tidak valid.');
    header('Location: login.php');
    exit;
}

if(($user['status'] ?? '') === 'active'){ // Menggunakan $user
    flash_set('error', 'Akun sudah aktif. Silakan masuk.');
    header('Location: login.php');
    exit; 
}

// Aktivasi berhasil
$pdo->prepare('UPDATE users SET status="active", activation_token=NULL WHERE id=?')->execute([$user['id']]); // Menggunakan $user

flash_set('success', 'Aktivasi akun berhasil! Silakan masuk.');
header('Location: login.php');
exit;