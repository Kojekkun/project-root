<?php
// app/helpers.php
// Pastikan tidak ada spasi sebelum <?php di baris paling atas!

// 1. Tahan Output (Penting)
ob_start();

// 2. SETTING SESI KHUSUS AZURE (SOLUSI FINAL)
// Berdasarkan diagnosa, server Anda hanya mengizinkan tulis di /tmp
if (file_exists('/tmp') && is_writable('/tmp')) {
    session_save_path('/tmp');
}

// Set durasi sesi 1 jam
ini_set('session.gc_maxlifetime', 3600);
session_set_cookie_params(3600);

// 3. Mulai Sesi
session_start();

require_once __DIR__ . '/config.php';

function e($str) {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

function generate_token($length = 32) {
    return bin2hex(random_bytes($length));
}

function flash_set($key, $msg) {
    $_SESSION['flash'][$key] = $msg;
}

function flash_get($key) {
    if (!empty($_SESSION['flash'][$key])) {
        $msg = $_SESSION['flash'][$key];
        unset($_SESSION['flash'][$key]);
        return $msg;
    }
    return null;
}

function is_logged(){
    return !empty($_SESSION['user_id']); 
}

function csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_check($token) {
    return hash_equals($_SESSION['csrf_token'] ?? '', $token ?? '');
}

function require_login(){
    if(!is_logged()){ header('Location: login.php'); exit; } 
}