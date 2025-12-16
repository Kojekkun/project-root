<?php
// public/login_success.php
require_once __DIR__ . '/../app/helpers.php';

// Cek apakah sesi benar-benar ada
if (is_logged()) {
    // Jika login admin, ke admin panel
    if (($_SESSION['user_role'] ?? '') === 'admin') {
        header('Location: admin/index.php');
    } else {
        // Jika user biasa, ke profil
        header('Location: profile.php');
    }
} else {
    // Jika sesi hilang, kembalikan ke login dengan pesan debug
    // (Anda bisa menghapus pesan debug ini nanti)
    flash_set('error', 'Sesi login terputus. Silakan coba login lagi.');
    header('Location: login.php');
}
exit;
?>