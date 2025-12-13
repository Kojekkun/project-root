<?php
require_once __DIR__ . '/../app/helpers.php';

// Hapus semua data sesi
session_unset();
session_destroy();

// Kembalikan ke halaman login
header('Location: /login.php');
exit;