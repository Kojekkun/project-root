<?php
// public/cek_file.php
echo "<h1>Isi Folder Server Azure</h1>";
echo "<p>Folder saat ini: <strong>" . __DIR__ . "</strong></p>";
echo "<hr>";

// Ambil semua file di folder ini
$files = scandir(__DIR__);

echo "<ul>";
foreach ($files as $file) {
    if ($file == '.' || $file == '..') continue;
    
    // Cek apakah itu file destination_detail.php
    if (strtolower($file) == 'destination_detail.php') {
        echo "<li style='color:green; font-weight:bold'>✅ " . $file . " (ADA!)</li>";
    } else {
        echo "<li>" . $file . "</li>";
    }
}
echo "</ul>";
?>