<?php
// app/controllers/destination_update_handler.php (VERSI FIX PATH)
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../helpers.php';
require_once __DIR__ . '/../config.php';

// Otorisasi Admin
if (empty($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'admin') {
    flash_set('error', 'Akses ditolak.');
    session_write_close();
    header('Location: ../login.php'); // Mundur ke public/login.php
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id'] ?? 0);
    
    // PERBAIKAN 1: URL Redirect jika error (Tetap di folder admin)
    $redirect_url = 'destination_edit.php?id=' . $id;

    if (!csrf_check($_POST['csrf'] ?? '')) {
        flash_set('error', 'Permintaan tidak valid (CSRF).');
        session_write_close();
        header('Location: ' . $redirect_url);
        exit;
    }
    
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $location = trim($_POST['location'] ?? '');
    $category_id = intval($_POST['category_id'] ?? 0);
    
    if ($id <= 0 || empty($title) || empty($description) || empty($location) || $category_id <= 0) {
        flash_set('error', 'Data tidak lengkap atau ID tidak valid.');
        session_write_close();
        header('Location: ' . $redirect_url);
        exit;
    }

    // 1. Ambil data destinasi lama
    $stmt = $pdo->prepare('SELECT image FROM destinations WHERE id = ?');
    $stmt->execute([$id]);
    $old_destination = $stmt->fetch();
    
    if (!$old_destination) {
        flash_set('error', 'Destinasi tidak ditemukan.');
        session_write_close();
        header('Location: ../destinations.php'); // Mundur ke public/destinations.php
        exit;
    }

    $update_fields = [
        'title' => $title,
        'description' => $description,
        'location' => $location,
        'category_id' => $category_id
    ];
    $image_filename = $old_destination['image'];

    // 2. Handle Image Upload Baru
    if (!empty($_FILES['image']['tmp_name'])) {
        $allowed = ['image/jpeg','image/png', 'image/gif'];
        // Validasi
        if (!in_array($_FILES['image']['type'], $allowed) || $_FILES['image']['size'] > $MAX_UPLOAD_SIZE) {
            flash_set('error', 'Format gambar salah atau terlalu besar.');
            session_write_close();
            header('Location: ' . $redirect_url);
            exit;
        }

        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $new_filename = generate_token(16) . '.' . $ext;
        $target_path = $UPLOAD_DIR . '/' . $new_filename;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $target_path)) {
            $image_filename = $new_filename;
            // Hapus gambar lama
            if (!empty($old_destination['image']) && file_exists($UPLOAD_DIR . '/' . $old_destination['image'])) {
                unlink($UPLOAD_DIR . '/' . $old_destination['image']);
            }
        } else {
            flash_set('error', 'Gagal mengupload gambar baru.');
            session_write_close();
            header('Location: ' . $redirect_url);
            exit;
        }
    }
    
    $update_fields['image'] = $image_filename;
    $set_clauses = array_map(fn($key) => "{$key} = ?", array_keys($update_fields));
    $update_values = array_values($update_fields);
    $update_values[] = $id;

    // 3. Jalankan Query UPDATE
    $sql = 'UPDATE destinations SET ' . implode(', ', $set_clauses) . ', updated_at=NOW() WHERE id=?';

    try {
        $pdo->prepare($sql)->execute($update_values);
        
        flash_set('success', 'Destinasi berhasil diupdate.');
        session_write_close();
        
        // PERBAIKAN 2: Redirect Sukses (Mundur satu folder ke public/)
        header('Location: ../destination_detail.php?id=' . $id);
        exit;
    } catch (PDOException $e) {
        error_log("Update failed: " . $e->getMessage());
        flash_set('error', 'Terjadi kesalahan database.');
        session_write_close();
        header('Location: ' . $redirect_url);
        exit;
    }
} else {
    header('Location: ../destinations.php');
    exit;
}