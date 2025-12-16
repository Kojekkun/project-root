<?php
// app/mailer.php (VERSI LIVE SMTP)
require_once __DIR__ . '/config.php';

// Panggil Library PHPMailer
require_once __DIR__ . '/PHPMailer/Exception.php';
require_once __DIR__ . '/PHPMailer/PHPMailer.php';
require_once __DIR__ . '/PHPMailer/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function send_activation_otp($to, $otp) {
    global $SMTP; // Ambil data dari config.php

    $mail = new PHPMailer(true);

    try {
        // 1. Konfigurasi Server
        $mail->isSMTP();
        $mail->Host       = $SMTP['host'];
        $mail->SMTPAuth   = true;
        $mail->Username   = $SMTP['username'];
        $mail->Password   = $SMTP['password'];
        $mail->SMTPSecure = $SMTP['secure'];
        $mail->Port       = $SMTP['port'];

        // 2. Penerima & Pengirim
        $mail->setFrom($SMTP['from_email'], $SMTP['from_name']);
        $mail->addAddress($to); // Kirim ke email pendaftar

        // 3. Konten Email
        $mail->isHTML(true);
        $mail->Subject = 'Kode OTP Aktivasi Akun';
        
        // Desain Body Email (HTML)
        $bodyContent = "
        <div style='font-family: Arial, sans-serif; max-width: 500px; margin: auto; border: 1px solid #ddd; padding: 20px; border-radius: 10px;'>
            <h2 style='color: #2563eb; text-align: center;'>🔐 Kode OTP Anda</h2>
            <p>Halo,</p>
            <p>Terima kasih telah mendaftar. Gunakan kode berikut untuk memverifikasi akun Anda:</p>
            <div style='background: #f3f4f6; padding: 15px; text-align: center; font-size: 24px; font-weight: bold; letter-spacing: 5px; color: #333; margin: 20px 0; border-radius: 5px;'>
                {$otp}
            </div>
            <p style='color: #666; font-size: 14px;'>Kode ini bersifat rahasia. Jangan berikan kepada siapa pun.</p>
            <hr style='border: 0; border-top: 1px solid #eee; margin: 20px 0;'>
            <small style='color: #999;'>Dikirim otomatis oleh Sistem Pariwisata.</small>
        </div>
        ";
        
        $mail->Body    = $bodyContent;
        $mail->AltBody = "Kode OTP Anda adalah: {$otp}"; // Untuk email client non-HTML

        $mail->send();
        return true;

    } catch (Exception $e) {
        // Jika gagal, catat errornya di server log (bukan di layar user)
        error_log("Gagal kirim email ke $to. Error: {$mail->ErrorInfo}");
        return false;
    }
}