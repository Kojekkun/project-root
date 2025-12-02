<?php
// app/controllers/destination_create.php (DISELESAIKAN)
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../helpers.php';
require_once __DIR__ . '/../config.php'; // Diperlukan untuk $UPLOAD_DIR, $MAX_UPLOAD_SIZE

// Pastikan user adalah admin
if (empty($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'admin') {
    flash_set('error', 'Akses ditolak.');
    header('Location: /login.php'); 
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_check($_POST['csrf'] ?? '')) { 
        flash_set('error', 'Permintaan tidak valid (CSRF).');
        header('Location: /admin/destination_add.php');
        exit;
    }

    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $location = trim($_POST['location'] ?? '');
    $category_id = intval($_POST['category_id'] ?? 0);
    $image_filename = null;

    if (empty($title) || empty($description) || empty($location) || $category_id <= 0) {
        flash_set('error', 'Mohon lengkapi semua data wajib.');
        header('Location: /admin/destination_add.php');
        exit;
    }

    // Handle Image Upload (simple)
    if (!empty($_FILES['image']['tmp_name'])) {
        if (!is_dir($UPLOAD_DIR) && !mkdir($UPLOAD_DIR, 0777, true)) {
            flash_set('error', 'Gagal membuat direktori upload.');
            header('Location: /admin/destination_add.php');
            exit;
        }

        $allowed = ['image/jpeg','image/png', 'image/gif'];
        if (!in_array($_FILES['image']['type'], $allowed)) { 
            flash_set('error', 'Format gambar tidak diperbolehkan (hanya JPG, PNG, GIF).'); 
            header('Location: /admin/destination_add.php');
            exit;
        }
        if ($_FILES['image']['size'] > $MAX_UPLOAD_SIZE) { 
            flash_set('error', 'File terlalu besar (Maks ' . ($MAX_UPLOAD_SIZE/1024/1024) . ' MB).'); 
            header('Location: /admin/destination_add.php');
            exit;
        }
        
        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $image_filename = generate_token(16) . '.' . $ext;
        $target_path = $UPLOAD_DIR . '/' . $image_filename;

        if (!move_uploaded_file($_FILES['image']['tmp_name'], $target_path)) {
            flash_set('error', 'Gagal memindahkan file gambar.');
            header('Location: /admin/destination_add.php');
            exit;
        }
    }

    // Insert ke database
    try {
        $stmt = $pdo->prepare('INSERT INTO destinations (title, description, location, category_id, image, created_at) VALUES (?, ?, ?, ?, ?, NOW())');
        $stmt->execute([$title, $description, $location, $category_id, $image_filename]);
        
        flash_set('success', 'Destinasi baru berhasil ditambahkan.');
        header('Location: /destinations.php');
        exit;

    } catch (PDOException $e) {
        error_log("Destination creation failed: " . $e->getMessage());
        flash_set('error', 'Terjadi kesalahan database saat menyimpan data.');
        header('Location: /admin/destination_add.php');
        exit;
    }
} else {
    header('Location: /admin/destination_add.php');
    exit;
}