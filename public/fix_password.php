<?php
// public/fix_password.php
require_once __DIR__ . '/../app/db.php';

$email = 'admin@pariwisata.com';
$password_baru = 'password123';

// 1. Buat Hash Baru (Fresh langsung dari sistem PHP laptop Anda)
$hash_baru = password_hash($password_baru, PASSWORD_BCRYPT);

// 2. Update Database Azure
try {
    $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE email = ?");
    $stmt->execute([$hash_baru, $email]);
    
    echo "<h1>✅ Password Berhasil Diperbaiki!</h1>";
    echo "<p>Password untuk admin <b>($email)</b> sudah di-reset ulang.</p>";
    echo "<p>Silakan gunakan password: <b>$password_baru</b></p>";
    echo "<hr>";
    echo "<a href='/login.php' style='font-size:20px; font-weight:bold'>Klik Disini untuk LOGIN ></a>";
} catch (PDOException $e) {
    echo "<h1>❌ Gagal Update</h1>";
    echo "Error: " . $e->getMessage();
}