<?php
// app/mailer.php
require_once __DIR__ . '/config.php';


function send_activation_email($to, $token) {
global $BASE_URL, $MAIL_FROM;
$subject = 'Aktivasi Akun - Pariwisata';
$link = $BASE_URL . '/activate.php?token=' . urlencode($token);
$message = "Halo,\n\nSilakan klik tautan berikut untuk mengaktifkan akun Anda:\n" . $link . "\n\nJika bukan Anda, abaikan email ini.";
$headers = 'From: ' . $MAIL_FROM . "\r\n" . 'Reply-To: ' . $MAIL_FROM . "\r\n";


// mail() mengembalikan boolean
return mail($to, $subject, $message, $headers);
}