<?php
// public/tes_path.php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<h1>🕵️ Tes Diagnosa Jalur File</h1>";

// 1. Cek Lokasi File Fisik
$folder_sekarang = __DIR__;
$target_profile = $folder_sekarang . '/profile.php';

echo "<h3>1. Pengecekan Fisik File</h3>";
echo "Folder saat ini: <code>" . $folder_sekarang . "</code><br>";
echo "Mencari file profile: <code>" . $target_profile . "</code><br><br>";

if (file_exists($target_profile)) {
    echo "✅ <b>STATUS: ADA.</b> File profile.php ditemukan di folder ini.<br>";
} else {
    echo "❌ <b>STATUS: HILANG.</b> Server TIDAK BISA menemukan file profile.php.<br>";
    echo "Saran: Cek apakah nama filenya 'Profile.php' (huruf besar)? Ubah jadi kecil semua.<br>";
}

// 2. Cek URL Browser
echo "<h3>2. Pengecekan URL Browser</h3>";
$script_name = $_SERVER['SCRIPT_NAME']; // Contoh: /public/tes_path.php
$dir_url = dirname($script_name);       // Contoh: /public

echo "URL Script saat ini: <code>" . $script_name . "</code><br>";
echo "Folder URL: <code>" . $dir_url . "</code><br>";

// 3. Link Percobaan
echo "<h3>3. Coba Klik Link Ini Satu per Satu:</h3>";
echo "Jika salah satu link ini berhasil, berarti itulah format yang harus kita pakai.<br><br>";

// Link A: Relatif Murni
echo "🅰️ <a href='profile.php'>Link Relatif (profile.php)</a> <br><small>Hanya memanggil nama file.</small><br><br>";

// Link B: Relatif dengan Dot
echo "🅱️ <a href='./profile.php'>Link Relatif Dot (./profile.php)</a> <br><small>Memaksa browser cari di folder yang sama.</small><br><br>";

// Link C: Full Path Otomatis
$full_link = rtrim($dir_url, '/\\') . '/profile.php';
echo "©️ <a href='$full_link'>Link Full Path ($full_link)</a> <br><small>Menggunakan path lengkap dari server.</small><br><br>";

?>