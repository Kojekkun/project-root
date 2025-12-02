<?php
// app/controllers/destination_delete_handler.php (BARU: Delete CRUD)
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../helpers.php';
require_once __DIR__ . '/../config.php';

// Otorisasi Admin
if (empty($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'admin') {
    flash_set('error', 'Akses ditolak.');
    header('Location: /login.php'); 
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_check($_POST['csrf'] ?? '')) { 
        flash_set('error', 'Permintaan tidak valid (CSRF).');
        header('Location: /destinations.php');
        exit;
    }

    $id = intval($_POST['id'] ?? 0);
    
    // Ambil data untuk menghapus gambar
    $stmt = $pdo->prepare('SELECT image FROM destinations WHERE id = ?');
    $stmt->execute([$id]);
    $destination = $stmt->fetch();
    
    if (!$destination) {
        flash_set('error', 'Destinasi tidak ditemukan.');
        header('Location: /destinations.php');
        exit;
    }

    // Hapus gambar terkait (jika ada)
    if (!empty($destination['image']) && file_exists($UPLOAD_DIR . '/' . $destination['image'])) {
        unlink($UPLOAD_DIR . '/' . $destination['image']);
    }

    // Hapus data dari database
    try {
        $stmt = $pdo->prepare('DELETE FROM destinations WHERE id = ?');
        $stmt->execute([$id]);
        
        flash_set('success', 'Destinasi berhasil dihapus.');
        header('Location: /destinations.php');
        exit;

    } catch (PDOException $e) {
        error_log("Destination deletion failed: " . $e->getMessage());
        flash_set('error', 'Terjadi kesalahan database saat menghapus data.');
        header('Location: /destinations.php');
        exit;
    }
} else {
    header('Location: /destinations.php');
    exit;
}