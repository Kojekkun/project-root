<?php
// public/profile_test_db.php

// 1. LOAD SYSTEM PALING ATAS (Sebelum ada output apapun!)
// Ini wajib agar session bisa terbaca
require_once __DIR__ . '/../app/helpers.php';
require_once __DIR__ . '/../app/db.php';

// Aktifkan Debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Cek Login & Session</title>
    <style>body { font-family: monospace; padding: 20px; line-height: 1.6; }</style>
</head>
<body>
    <h1>🕵️‍♂️ Diagnosa Session & Login</h1>

    <h3>1. Cek Data Sesi PHP</h3>
    <?php
    if (session_status() === PHP_SESSION_NONE) {
        echo "❌ <b>Session Status:</b> Mati (PHP_SESSION_NONE)<br>";
    } else {
        echo "✅ <b>Session Status:</b> Aktif (ID: " . session_id() . ")<br>";
    }
    
    echo "<b>Isi Variable \$_SESSION:</b><br>";
    echo "<pre style='background:#eee; padding:10px; border:1px solid #999;'>";
    var_dump($_SESSION); // Melihat isi sesi "mentah"
    echo "</pre>";
    ?>

    <h3>2. Cek Fungsi Login (is_logged)</h3>
    <?php
    if (is_logged()) {
        echo "✅ <b>Status:</b> USER SUDAH LOGIN!<br>";
        echo "User ID: " . $_SESSION['user_id'] . "<br>";
        echo "Role: " . ($_SESSION['user_role'] ?? 'N/A') . "<br>";
    } else {
        echo "⚠️ <b>Status:</b> USER BELUM LOGIN (Terdeteksi sebagai Tamu)<br>";
        echo "Analisis: Jika Anda baru saja login tapi melihat pesan ini, berarti sesi hilang saat pindah halaman.<br>";
    }
    ?>

    <h3>3. Cek Lokasi File</h3>
    <?php
    echo "File ini berada di: " . __FILE__ . "<br>";
    ?>

    <br><hr>
    <a href="login.php">➡️ Coba Login Ulang</a> | 
    <a href="profile.php">➡️ Coba ke Profile</a>
</body>
</html>