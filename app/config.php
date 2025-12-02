<?php
// app/config.php (DIKOREKSI)
// Sesuaikan dengan environment kamu
$DB_HOST = 'localhost';
$DB_NAME = 'pariwisata_db';
$DB_USER = 'dbuser';
$DB_PASS = 'dbpass';

// Base URL aplikasi (tanpa trailing slash)
$BASE_URL = 'https://yourdomain.com';

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