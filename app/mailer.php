<?php
// app/mailer.php
require_once __DIR__ . '/config.php';

function send_activation_email($to, $token) {
    global $BASE_URL;
    
    // Buat Link Aktivasi
    $link = $BASE_URL . '/activate.php?token=' . urlencode($token);
    
    // --- TEKNIK SIMULASI ---
    // Karena di localhost tidak bisa kirim email asli, 
    // kita simpan isi pesannya ke file "inbox_palsu.txt" di folder public.
    
    $isi_pesan = "=== EMAIL BARU ===\n";
    $isi_pesan .= "Kepada: $to\n";
    $isi_pesan .= "Pesan: Silakan klik link ini untuk aktivasi:\n";
    $isi_pesan .= "$link\n";
    $isi_pesan .= "==================\n\n";

    // Simpan ke file public/email_log.txt
    file_put_contents(__DIR__ . '/../public/email_log.txt', $isi_pesan, FILE_APPEND);

    // Kembalikan TRUE (Pura-pura sukses terkirim)
    return true; 
}