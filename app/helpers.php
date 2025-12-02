<?php
// app/helpers.php
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

// Simple CSRF
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
    if(!is_logged()){ header('Location: /login.php'); exit; } 
}