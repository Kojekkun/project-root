<?php
// public/cek_history.php
$target = __DIR__ . '/history.php';

echo "<h1>🕵️‍♂️ Cek File History</h1>";
echo "Mencari file di: <code>$target</code><br><br>";

if (file_exists($target)) {
    echo "✅ <b>FILE ADA!</b> Server melihat file ini.<br>";
    echo "👉 <a href='history.php'>Klik untuk Buka History</a>";
} else {
    echo "❌ <b>FILE TIDAK DITEMUKAN!</b><br>";
    echo "Kemungkinan nama file salah (Cek huruf besar 'H') atau file belum ter-upload.";
}
?>