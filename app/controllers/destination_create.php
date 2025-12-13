<?php
// app/controllers/destination_create.php (VERSI FIX PATH & SESSION)
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../helpers.php';
require_once __DIR__ . '/../config.php';

// Pastikan user adalah admin
if (empty($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'admin') {
    flash_set('error', 'Akses ditolak.');
    session_write_close();
    header('Location: ../login.php'); // PERBAIKAN: ../
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_check($_POST['csrf'] ?? '')) { 
        flash_set('error', 'Permintaan tidak valid (CSRF).');
        session_write_close();
        header('Location: destination_add.php'); // Tetap di folder admin
        exit;
    }

    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $location = trim($_POST['location'] ?? '');
    $category_id = intval($_POST['category_id'] ?? 0);
    $image_filename = null;

    if (empty($title) || empty($description) || empty($location) || $category_id <= 0) {
        flash_set('error', 'Mohon lengkapi semua data wajib.');
        session_write_close();
        header('Location: destination_add.php');
        exit;
    }

    // Handle Image Upload
    if (!empty($_FILES['image']['tmp_name'])) {
        // Validasi tipe dan ukuran...
        $allowed = ['image/jpeg','image/png', 'image/gif'];
        if (!in_array($_FILES['image']['type'], $allowed) || $_FILES['image']['size'] > $MAX_UPLOAD_SIZE) { 
            flash_set('error', 'Format gambar salah atau terlalu besar.'); 
            session_write_close();
            header('Location: destination_add.php');
            exit;
        }
        
        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $image_filename = generate_token(16) . '.' . $ext;
        $target_path = $UPLOAD_DIR . '/' . $image_filename;

        if (!move_uploaded_file($_FILES['image']['tmp_name'], $target_path)) {
            flash_set('error', 'Gagal memindahkan file gambar.');
            session_write_close();
            header('Location: destination_add.php');
            exit;
        }
    }

    // Insert ke database
    try {
        $stmt = $pdo->prepare('INSERT INTO destinations (title, description, location, category_id, image, created_at) VALUES (?, ?, ?, ?, ?, NOW())');
        $stmt->execute([$title, $description, $location, $category_id, $image_filename]);
        
        flash_set('success', 'Destinasi baru berhasil ditambahkan.');
        session_write_close();
        header('Location: ../destinations.php'); // PERBAIKAN: Mundur ke public/
        exit;

    } catch (PDOException $e) {
        error_log("Creation failed: " . $e->getMessage());
        flash_set('error', 'Terjadi kesalahan database.');
        session_write_close();
        header('Location: destination_add.php');
        exit;
    }
} else {
    header('Location: destination_add.php');
    exit;
}