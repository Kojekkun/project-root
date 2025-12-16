<?php
// public/cek_debug.php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<div style='font-family: monospace; padding: 20px; background:#f0f0f0; border:1px solid #ccc;'>";
echo "<h1>🕵️‍♂️ Diagnosis Server</h1>";

// 1. Cek Lokasi File
echo "<h3>1. Pengecekan File</h3>";
$dir = __DIR__;
echo "📂 Folder Saat Ini: <b>" . $dir . "</b><br>";

$files_to_check = ['index.php', 'profile.php', 'login.php', '../app/config.php', '../app/helpers.php'];
foreach ($files_to_check as $file) {
    $path = $dir . '/' . $file;
    if (file_exists($path)) {
        echo "✅ File ditemukan: $file <br>";
    } else {
        echo "❌ <b>FILE HILANG:</b> $file (Cek huruf besar/kecil!)<br>";
    }
}

// 2. Cek Konfigurasi Database (Variabel vs Konstanta)
echo "<h3>2. Cek Koneksi Database</h3>";
$config_path = __DIR__ . '/../app/config.php';
if (file_exists($config_path)) {
    require_once $config_path;
    
    // Deteksi apakah pakai Define atau Variabel
    if (defined('DB_HOST')) {
        echo "ℹ️ Config menggunakan: <b>KONSTANTA (define)</b><br>";
        $host = DB_HOST; $user = DB_USER; $pass = DB_PASS; $name = DB_NAME;
    } elseif (isset($DB_HOST)) {
        echo "ℹ️ Config menggunakan: <b>VARIABEL ($)</b><br>";
        $host = $DB_HOST; $user = $DB_USER; $pass = $DB_PASS; $name = $DB_NAME;
    } else {
        echo "❌ <b>ERROR:</b> Tidak ada konfigurasi database di config.php!<br>";
    }

    // Tes Koneksi Manual
    if (!empty($host)) {
        try {
            $dsn = "mysql:host=$host;dbname=$name;charset=utf8mb4";
            $pdo = new PDO($dsn, $user, $pass);
            echo "✅ <b>KONEKSI SUKSES!</b> Database terhubung.<br>";
        } catch (PDOException $e) {
            echo "❌ <b>KONEKSI GAGAL:</b> " . $e->getMessage() . "<br>";
            echo "<i>Saran: Pastikan db.php menggunakan jenis variabel yang sama dengan config.php (Konstanta vs Variabel).</i><br>";
        }
    }
}

// 3. Cek Sesi User
echo "<h3>3. Status Login</h3>";
if (isset($_SESSION['user_id'])) {
    echo "✅ User ID: " . $_SESSION['user_id'] . " (Sedang Login)<br>";
    echo "👤 Nama: " . ($_SESSION['user_name'] ?? 'Tanpa Nama') . "<br>";
    echo "👉 <a href='profile.php'>Klik Disini untuk ke Profile.php</a>";
} else {
    echo "⚠️ User <b>BELUM LOGIN</b>. (Akses ke profile.php akan dilempar ke login)<br>";
}

echo "</div>";
?>