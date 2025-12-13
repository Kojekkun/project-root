<?php
// app/controllers/destination_delete_handler.php (VERSI FINAL & STABIL)
// File ini dipanggil oleh public/admin/process_delete.php

require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../helpers.php';
require_once __DIR__ . '/../config.php';

// Otorisasi Admin
if (empty($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'admin') {
    flash_set('error', 'Akses ditolak.');
    session_write_close();
    // Redirect mundur ke public/login.php
    header('Location: ../login.php'); 
    exit;
}

// Pastikan hanya bisa diakses lewat POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // 1. Cek CSRF (Keamanan)
    if (!csrf_check($_POST['csrf'] ?? '')) { 
        flash_set('error', 'Permintaan tidak valid (CSRF).');
        session_write_close();
        header('Location: ../destinations.php'); // Kembali ke list
        exit;
    }

    $id = intval($_POST['id'] ?? 0);
    
    // 2. Ambil data gambar sebelum dihapus
    $stmt = $pdo->prepare('SELECT image FROM destinations WHERE id = ?');
    $stmt->execute([$id]);
    $destination = $stmt->fetch();
    
    if (!$destination) {
        flash_set('error', 'Destinasi tidak ditemukan.');
        session_write_close();
        header('Location: ../destinations.php');
        exit;
    }

    // 3. Hapus fisik file gambar (jika ada & file-nya eksis)
    // $UPLOAD_DIR diambil dari app/config.php
    if (!empty($destination['image']) && file_exists($UPLOAD_DIR . '/' . $destination['image'])) {
        unlink($UPLOAD_DIR . '/' . $destination['image']);
    }

    // 4. Hapus data dari database
    try {
        $stmt = $pdo->prepare('DELETE FROM destinations WHERE id = ?');
        $stmt->execute([$id]);
        
        flash_set('success', 'Destinasi berhasil dihapus permanen.');
        
        // SUKSES: Simpan sesi dan kembali ke halaman list (public/destinations.php)
        session_write_close();
        header('Location: ../destinations.php');
        exit;

    } catch (PDOException $e) {
        error_log("Delete failed: " . $e->getMessage());
        flash_set('error', 'Gagal menghapus data dari database.');
        session_write_close();
        header('Location: ../destinations.php');
        exit;
    }
} else {
    // Jika ada yang mencoba akses langsung via GET, lempar ke list
    header('Location: ../destinations.php');
    exit;
}