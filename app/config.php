<?php
// app/config.php

// 1. Database (Azure) - MENGGUNAKAN KONSTANTA
define('DB_HOST', 'pariwisata-db-server.mysql.database.azure.com');
define('DB_NAME', 'pariwisata_db');
define('DB_USER', 'pariwisata');
define('DB_PASS', '(Kocaklol33)');

// 2. Base URL (Opsional, tapi kita simpan sebagai konstanta)
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
$host = $_SERVER['HTTP_HOST'];
define('BASE_URL', $protocol . $host . '/'); 

// 3. Upload Config
define('UPLOAD_DIR', __DIR__ . '/../public/uploads');
define('MAX_UPLOAD_SIZE', 2 * 1024 * 1024);

// 4. Email Config
$SMTP = [
    'host' => 'smtp.gmail.com',
    'port' => 587,
    'username' => 'rolandjohanes05@gmail.com',
    'password' => 'mzue wveu vyam azmr',
    'secure' => 'tls',
    'from_email' => 'rolandjohanes05@gmail.com',
    'from_name' => 'Admin Pariwisata'
];
?>