<?php
// public/fix_login.php
require_once __DIR__ . '/../app/db.php';

// 1. HAPUS SESI LAMA (FORCE LOGOUT)
session_start();
session_unset();
session_destroy();

echo "<div style='font-family:sans-serif; padding:20px;'>";
echo "<h1>🛠️ Perbaikan Data Login & Sesi</h1>";
echo "<p>✅ Sesi login lama berhasil dibersihkan.</p>";

// 2. CEK DATA DATABASE
echo "<h3>📊 Daftar User di Database:</h3>";
echo "<table border='1' cellpadding='5' style='border-collapse:collapse;'>";
echo "<tr style='background:#eee;'><th>ID</th><th>Nama</th><th>Email</th><th>Role</th><th>Status</th></tr>";

try {
    $stmt = $pdo->query("SELECT * FROM users");
    while ($row = $stmt->fetch()) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['id']) . "</td>";
        echo "<td>" . htmlspecialchars($row['name']) . "</td>";
        echo "<td>" . htmlspecialchars($row['email']) . "</td>";
        echo "<td>" . htmlspecialchars($row['role']) . "</td>";
        echo "<td>" . htmlspecialchars($row['status']) . "</td>";
        echo "</tr>";
    }
} catch (Exception $e) {
    echo "<tr><td colspan='5'>❌ Error Database: " . $e->getMessage() . "</td></tr>";
}
echo "</table>";

echo "<br><hr>";
echo "<h3>👉 Langkah Selanjutnya:</h3>";
echo "<ol>";
echo "<li>Pastikan ID User Anda (Admin) terlihat di tabel di atas.</li>";
echo "<li>Klik tombol di bawah untuk Login Ulang dengan data baru.</li>";
echo "</ol>";

echo "<a href='login.php' style='background:blue; color:white; padding:10px 20px; text-decoration:none; border-radius:5px;'>🔑 LOGIN SEKARANG</a>";
echo "</div>";
?>