<?php
// app/db.php
require_once __DIR__ . '/config.php';

try {
    $dsn = "mysql:host={$DB_HOST};dbname={$DB_NAME};charset=utf8mb4";
    
    // Opsi tambahan khusus Azure SSL
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
        // Baris di bawah ini PENTING untuk Azure Flexible Server
        //PDO::MYSQL_ATTR_SSL_CA => __DIR__ . '/../DigiCertGlobalRootG2.crt.pem', // Jika pakai sertifikat
        // ATAU gunakan baris ini jika ingin bypass verifikasi SSL (Tidak disarankan untuk Production, tapi oke untuk Dev):
        PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => false, 
    ];

    $pdo = new PDO($dsn, $DB_USER, $DB_PASS, $options);
} catch (PDOException $e) {
    // Tampilkan error lengkap agar kita tahu jika salah password/firewall
    die('Koneksi Database Gagal: ' . $e->getMessage());
}