<?php
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../helpers.php';
require_once __DIR__ . '/../config.php';

if (empty($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'admin') {
    flash_set('error', 'Akses ditolak.');
    session_write_close();
    header('Location: ../login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id'] ?? 0);
    $redirect_url = 'destination_edit.php?id=' . $id;

    if (!csrf_check($_POST['csrf'] ?? '')) {
        flash_set('error', 'CSRF Error.'); session_write_close();
        header('Location: ' . $redirect_url); exit;
    }
    
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $location = trim($_POST['location'] ?? '');
    $category_id = intval($_POST['category_id'] ?? 0);
    $map_embed = $_POST['map_embed'] ?? '';
    
    if ($id <= 0 || empty($title) || empty($description) || empty($location) || $category_id <= 0) {
        flash_set('error', 'Data wajib tidak boleh kosong.'); session_write_close();
        header('Location: ' . $redirect_url); exit;
    }

    // 1. Ambil data lama
    $stmt = $pdo->prepare('SELECT image FROM destinations WHERE id = ?');
    $stmt->execute([$id]);
    $old_destination = $stmt->fetch();
    
    if (!$old_destination) {
        flash_set('error', 'Destinasi hilang.'); session_write_close();
        header('Location: ../destinations.php'); exit;
    }

    $image_final = $old_destination['image']; // Default pakai lama
    $upload_dir = defined('UPLOAD_DIR') ? UPLOAD_DIR : __DIR__ . '/../../public/uploads';
    $max_size = defined('MAX_UPLOAD_SIZE') ? MAX_UPLOAD_SIZE : 2097152;

    // 2. LOGIKA GANTI GAMBAR
    // Opsi A: User Upload File Baru
    if (!empty($_FILES['image']['tmp_name'])) {
        $allowed = ['image/jpeg','image/png', 'image/gif', 'image/webp'];
        if (!in_array($_FILES['image']['type'], $allowed) || $_FILES['image']['size'] > $max_size) {
            flash_set('error', 'Format gambar salah/terlalu besar.'); session_write_close();
            header('Location: ' . $redirect_url); exit;
        }

        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $new_filename = generate_token(16) . '.' . $ext;
        
        if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_dir . '/' . $new_filename)) {
            $image_final = $new_filename;
            
            // Hapus file lama jika lokal
            if (!empty($old_destination['image']) && strpos($old_destination['image'], 'http') !== 0 && file_exists($upload_dir . '/' . $old_destination['image'])) {
                unlink($upload_dir . '/' . $old_destination['image']);
            }
        }
    } 
    // Opsi B: User Masukkan Link URL Baru
    elseif (!empty($_POST['image_url'])) {
        if ($_POST['image_url'] !== $old_destination['image']) {
            $image_final = trim($_POST['image_url']);
            
            // Hapus file lama jika lokal (karena diganti link)
            if (!empty($old_destination['image']) && strpos($old_destination['image'], 'http') !== 0 && file_exists($upload_dir . '/' . $old_destination['image'])) {
                unlink($upload_dir . '/' . $old_destination['image']);
            }
        }
    }

    // 3. Update Database
    $sql = 'UPDATE destinations SET title=?, description=?, location=?, category_id=?, map_embed=?, image=?, updated_at=NOW() WHERE id=?';

    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$title, $description, $location, $category_id, $map_embed, $image_final, $id]);

        flash_set('success', 'Destinasi berhasil diupdate.');
        session_write_close();
        header('Location: ../destination_detail.php?id=' . $id);
        exit;
    } catch (PDOException $e) {
        error_log("Update failed: " . $e->getMessage());
        flash_set('error', 'Error Database.'); session_write_close();
        header('Location: ' . $redirect_url); exit;
    }
} else {
    header('Location: ../destinations.php'); exit;
}