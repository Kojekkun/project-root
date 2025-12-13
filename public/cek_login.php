<?php
// public/cek_login.php
require_once __DIR__ . '/../app/db.php';

echo "<h1>Diagnosa Database & Login</h1>";

// 1. Cek Koneksi
echo "<h3>1. Status Koneksi</h3>";
if ($pdo) {
    echo "<p style='color:green'>✅ Koneksi ke Azure BERHASIL.</p>";
} else {
    die("<p style='color:red'>❌ Koneksi Gagal.</p>");
}

// 2. Cek Isi Tabel Users
echo "<h3>2. Cek Data User</h3>";
$stmt = $pdo->query("SELECT * FROM users");
$users = $stmt->fetchAll();

if (count($users) == 0) {
    echo "<p style='color:red'>❌ Tabel 'users' KOSONG! Belum ada data admin.</p>";
    echo "<p>Solusi: Jalankan query INSERT lagi di MySQL Workbench.</p>";
} else {
    echo "<p style='color:green'>✅ Ditemukan " . count($users) . " user di database.</p>";
    
    echo "<table border='1' cellpadding='5'><tr><th>ID</th><th>Nama</th><th>Email</th><th>Role</th><th>Status</th><th>Password Hash</th></tr>";
    foreach ($users as $u) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($u['id']) . "</td>";
        echo "<td>" . htmlspecialchars($u['name']) . "</td>";
        echo "<td>" . htmlspecialchars($u['email']) . "</td>";
        echo "<td>" . htmlspecialchars($u['role']) . "</td>";
        echo "<td>" . htmlspecialchars($u['status']) . "</td>";
        echo "<td>" . substr($u['password'], 0, 10) . "...</td>";
        echo "</tr>";
    }
    echo "</table>";
}

// 3. Tes Verifikasi Password
echo "<h3>3. Simulasi Login</h3>";
$email_tes = 'admin@pariwisata.com';
$pass_tes = 'password123';

echo "<p>Mencoba login dengan Email: <b>$email_tes</b> dan Password: <b>$pass_tes</b> ...</p>";

// Cari user
$stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
$stmt->execute([$email_tes]);
$user = $stmt->fetch();

if (!$user) {
    echo "<p style='color:red'>❌ User dengan email '$email_tes' TIDAK DITEMUKAN di database.</p>";
} else {
    echo "<p style='color:green'>✅ Email ditemukan.</p>";
    
    // Cek Password
    if (password_verify($pass_tes, $user['password'])) {
        echo "<p style='color:green; font-weight:bold; font-size:18px'>✅ PASSWORD COCOK! Login seharusnya berhasil.</p>";
    } else {
        echo "<p style='color:red; font-weight:bold; font-size:18px'>❌ PASSWORD SALAH!</p>";
        echo "<p>Hash di database tidak cocok dengan 'password123'.</p>";
    }
}
?>