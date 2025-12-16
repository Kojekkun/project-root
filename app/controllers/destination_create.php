<?php
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../helpers.php';
require_once __DIR__ . '/../config.php';

// Cek Admin
if (empty($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'admin') {
    flash_set('error', 'Akses ditolak.');
    header('Location: ../login.php'); exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_check($_POST['csrf'] ?? '')) { 
        flash_set('error', 'CSRF Error.'); header('Location: destination_add.php'); exit;
    }

    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $location = trim($_POST['location'] ?? '');
    $category_id = intval($_POST['category_id'] ?? 0);
    $map_embed = $_POST['map_embed'] ?? '';
    
    // --- LOGIKA GAMBAR BARU ---
    $image_final = null;

    // Cek 1: Apakah Admin Mengupload File?
    if (!empty($_FILES['image']['tmp_name'])) {
        $allowed = ['image/jpeg','image/png', 'image/gif', 'image/webp'];
        // MAX_UPLOAD_SIZE diambil dari config.php, pastikan ada atau set manual angka (misal 2MB = 2097152)
        $max_size = defined('MAX_UPLOAD_SIZE') ? MAX_UPLOAD_SIZE : 2097152; 

        if (!in_array($_FILES['image']['type'], $allowed) || $_FILES['image']['size'] > $max_size) { 
            flash_set('error', 'Format gambar salah atau terlalu besar.'); 
            header('Location: destination_add.php'); exit;
        }
        
        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $image_final = generate_token(16) . '.' . $ext;
        
        // Pastikan UPLOAD_DIR terdefinisi di config.php atau gunakan path manual
        $target_dir = defined('UPLOAD_DIR') ? UPLOAD_DIR : __DIR__ . '/../../public/uploads';

        if (!move_uploaded_file($_FILES['image']['tmp_name'], $target_dir . '/' . $image_final)) {
            flash_set('error', 'Gagal upload gambar ke server.');
            header('Location: destination_add.php'); exit;
        }
    } 
    // Cek 2: Jika tidak upload, apakah Admin mengisi Link URL?
    elseif (!empty($_POST['image_url'])) {
        $image_final = trim($_POST['image_url']);
    }

    // Validasi Akhir: Harus ada gambar (salah satu)
    if (empty($image_final)) {
        flash_set('error', 'Wajib upload foto ATAU masukkan link gambar.');
        header('Location: destination_add.php'); exit;
    }
    // ---------------------------

    try {
        $sql = 'INSERT INTO destinations (title, description, location, category_id, image, map_embed, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())';
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$title, $description, $location, $category_id, $image_final, $map_embed]);
        
        flash_set('success', 'Destinasi berhasil ditambahkan!');
        header('Location: ../destinations.php'); exit;

    } catch (PDOException $e) {
        flash_set('error', 'Database Error: ' . $e->getMessage());
        header('Location: destination_add.php'); exit;
    }
}