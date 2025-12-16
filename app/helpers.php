<?php
// app/helpers.php

if (session_status() === PHP_SESSION_NONE) {
    // === TAMBAHAN PENTING UNTUK AZURE ===
    // Memastikan sesi bertahan 1 hari dan tidak hilang saat redirect
    ini_set('session.cookie_lifetime', 86400);
    ini_set('session.gc_maxlifetime', 86400);
    
    $is_https = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on';
    
    session_set_cookie_params([
        'lifetime' => 86400,
        'path' => '/',
        'domain' => '', 
        'secure' => $is_https, 
        'httponly' => true,
        'samesite' => 'Lax' 
    ]);
    
    session_start();
}

require_once __DIR__ . '/config.php';

function e($str) {
    return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8');
}

function is_logged() {
    // Cek ganda: Sesi User ID ada & tidak kosong
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

// === REDIRECT RELATIF (JANGAN DIUBAH) ===
function require_login() {
    if (!is_logged()) {
        if (strpos($_SERVER['SCRIPT_NAME'], '/admin/') !== false) {
             header('Location: ../login.php');
        } else {
             header('Location: login.php');
        }
        exit;
    }
}

function flash_set($type, $message) {
    if (session_status() === PHP_SESSION_NONE) session_start();
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function flash_get($type) {
    if (session_status() === PHP_SESSION_NONE) session_start();
    if (isset($_SESSION['flash']) && $_SESSION['flash']['type'] === $type) {
        $msg = $_SESSION['flash']['message'];
        unset($_SESSION['flash']);
        return $msg;
    }
    return null;
}

function csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_check($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

function get_image_url($image_name) {
    $prefix = 'uploads/';
    if (strpos($_SERVER['SCRIPT_NAME'], '/admin/') !== false) {
        $prefix = '../uploads/';
    }

    if (empty($image_name)) return $prefix . 'placeholder.jpg'; 

    if (strpos($image_name, 'http') === 0) {
        if (strpos($image_name, 'google') !== false) {
            $image_name = preg_replace('/=(s|w|h)[0-9]+(-[a-z0-9]+)*$/i', '=w1920-h1080-no', $image_name);
        }
        return $image_name;
    }
    return $prefix . $image_name;
}
?>