<?php
// app/config.php (DIKOREKSI)
// Sesuaikan dengan environment kamu
$DB_HOST = 'pariwisata-db-server.mysql.database.azure.com'; // Ganti dengan Server Name dari Azure
$DB_NAME = 'pariwisata_db'; // Nama database yang akan kita buat nanti
$DB_USER = 'pariwisata'; // Username admin Azure Anda
$DB_PASS = '(Kocaklol33)';  // Password admin Azure Anda

// Base URL aplikasi (tanpa trailing slash)
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
$host = $_SERVER['HTTP_HOST']; // Akan mendeteksi 'localhost' atau 'nama-web.azurewebsites.net'
$BASE_URL = $protocol . $host;

// Email (gunakan SMTP jika hosting support)
// Jika menggunakan mail() native, pastikan server punya MTA atau gunakan layanan SMTP
$MAIL_FROM = 'no-reply@yourdomain.com';
$MAIL_REPLY = 'support@yourdomain.com';

// Jika ingin gunakan SMTP (rekomendasi), simpan konfigurasi di sini
$SMTP = [
'host' => 'smtp.example.com',
'port' => 587,
'username' => 'smtp_user',
'password' => 'smtp_pass',
'secure' => 'tls'
];

// Upload limits
// **SOLUSI: Ubah jalur agar menunjuk ke /project-root/public/uploads**
// __DIR__ adalah /project-root/app. Kita harus naik satu level (..) lalu masuk ke public/uploads
$UPLOAD_DIR = __DIR__ . '/../public/uploads'; 
$MAX_UPLOAD_SIZE = 2 * 1024 * 1024; // 2 MB