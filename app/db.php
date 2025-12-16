<?php
// app/db.php
require_once __DIR__ . '/config.php';

try {
    // MENGGUNAKAN KONSTANTA (Tanpa tanda $)
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
    
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => false, // Fix Azure SSL
    ];

    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
} catch (PDOException $e) {
    // Menangkap error agar tidak fatal
    die('Koneksi Database Gagal: ' . $e->getMessage());
}
?>