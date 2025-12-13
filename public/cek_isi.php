<?php
// public/cek_isi.php
echo "<h1>Bedah Kode Server</h1>";

// Sesuaikan nama file dengan yang ada di screenshot tadi (pakai 's')
$file_target = 'destinations.php'; 

if (file_exists($file_target)) {
    echo "<h3>Isi file: $file_target</h3>";
    $content = file_get_contents($file_target);
    
    // Tampilkan kodenya di layar
    echo "<textarea style='width:100%; height:400px; font-family:monospace;'>" . htmlspecialchars($content) . "</textarea>";
} else {
    echo "❌ File $file_target tidak ditemukan.";
}
?>