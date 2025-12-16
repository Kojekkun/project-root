<?php
// app/controllers/tour_create_handler.php
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../helpers.php';
require_once __DIR__ . '/../config.php';

// Pastikan Admin
if (empty($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'admin') {
    header('Location: ../login.php'); exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_check($_POST['csrf'] ?? '')) {
        flash_set('error', 'CSRF Error'); session_write_close();
        header('Location: tour_add.php'); exit;
    }

    $title = trim($_POST['title']);
    $price = $_POST['price'];
    $contact = $_POST['contact'];
    $description = trim($_POST['description']);
    $itinerary = trim($_POST['itinerary']);
    $dest_id = !empty($_POST['destination_id']) ? $_POST['destination_id'] : null;
    $image_filename = null;

    // Validasi Sederhana
    if (empty($title) || empty($price) || empty($contact)) {
        flash_set('error', 'Data wajib harus diisi.'); session_write_close();
        header('Location: tour_add.php'); exit;
    }

    // Handle Upload Gambar
    if (!empty($_FILES['image']['tmp_name'])) {
        $allowed = ['image/jpeg','image/png', 'image/gif'];
        if (!in_array($_FILES['image']['type'], $allowed) || $_FILES['image']['size'] > $MAX_UPLOAD_SIZE) {
            flash_set('error', 'Format gambar salah atau terlalu besar.'); session_write_close();
            header('Location: tour_add.php'); exit;
        }
        
        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $image_filename = 'tour_' . time() . '.' . $ext;
        if (!move_uploaded_file($_FILES['image']['tmp_name'], $UPLOAD_DIR . '/' . $image_filename)) {
            flash_set('error', 'Gagal upload gambar.'); session_write_close();
            header('Location: tour_add.php'); exit;
        }
    }

    // Insert ke Database
    
    try {
        $map_embed = $_POST['map_embed'] ?? ''; // Ambil data map

        $sql = "INSERT INTO tours (title, destination_id, description, itinerary, price, contact, image, map_embed, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$title, $dest_id, $description, $itinerary, $price, $contact, $image_filename, $map_embed]);

        flash_set('success', 'Paket tour baru berhasil ditambahkan.');
        session_write_close();
        // Kembali ke halaman utama (bagian bawah)
        header('Location: ../index.php'); 
        exit;
    } catch (PDOException $e) {
        error_log($e->getMessage());
        flash_set('error', 'Gagal menyimpan ke database.');
        session_write_close();
        header('Location: tour_add.php');
        exit;
    }
}