<?php
// public/admin/process_delete_user.php
require_once __DIR__ . '/../../app/db.php';
require_once __DIR__ . '/../../app/helpers.php';

// 1. Keamanan: Pastikan Login & Admin
require_login();
if (($_SESSION['user_role'] ?? '') !== 'admin') {
    header('Location: ../index.php');
    exit;
}

// 2. Cek Request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? 0;

    // Jangan biarkan admin menghapus dirinya sendiri
    if ($id == $_SESSION['user_id']) {
        flash_set('error', 'Anda tidak dapat menghapus akun Anda sendiri saat sedang login.');
        header('Location: index.php');
        exit;
    }

    if (!$id) {
        flash_set('error', 'ID User tidak valid.');
        header('Location: index.php');
        exit;
    }

    try {
        // Mulai Transaksi Database
        $pdo->beginTransaction();

        // A. Hapus Foto Avatar (Opsional)
        $stmt = $pdo->prepare("SELECT avatar FROM users WHERE id = ?");
        $stmt->execute([$id]);
        $user = $stmt->fetch();
        if ($user && !empty($user['avatar'])) {
            $file_path = __DIR__ . '/../uploads/avatars/' . $user['avatar'];
            if (file_exists($file_path)) {
                @unlink($file_path); 
            }
        }

        // B. Hapus Data Terkait
        // 1. Hapus Booking
        $stmt = $pdo->prepare("DELETE FROM bookings WHERE user_id = ?");
        $stmt->execute([$id]);

        // 2. Hapus Transaksi
        $stmt = $pdo->prepare("DELETE FROM transactions WHERE user_id = ?");
        $stmt->execute([$id]);

        // 3. Hapus User
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$id]);

        // Commit Transaksi
        $pdo->commit();

        // PERBAIKAN DISINI: Gunakan flash_set, bukan flash
        flash_set('success', 'User dan semua datanya berhasil dihapus permanen.');

    } catch (Exception $e) {
        $pdo->rollBack();
        // PERBAIKAN DISINI: Gunakan flash_set
        flash_set('error', 'Gagal menghapus user: ' . $e->getMessage());
    }

    header('Location: index.php');
    exit;
} else {
    // Jika akses via GET
    header('Location: index.php');
    exit;
}