<?php
require_once __DIR__ . '/../app/helpers.php';

session_unset();
session_destroy();

// UBAH DARI: header('Location: /login.php');
// MENJADI: (Tanpa garis miring)
header('Location: index.php');
exit;