<?php
// app/controllers/tour_update_handler.php
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../helpers.php';
require_once __DIR__ . '/../config.php';

if (empty($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'admin') {
    header('Location: ../login.php'); exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id'] ?? 0);
    $redirect_url = 'tour_edit.php?id=' . $id;

    if (!csrf_check($_POST['csrf'] ?? '')) {
        flash_set('error', 'CSRF Invalid'); session_write_close();
        header('Location: ' . $redirect_url); exit;
    }

    $title = trim($_POST['title']);
    $price = $_POST['price'];
    $contact = $_POST['contact'];
    $description = trim($_POST['description']);
    $itinerary = trim($_POST['itinerary']);
    $dest_id = !empty($_POST['destination_id']) ? $_POST['destination_id'] : null;

    // Ambil Gambar Lama
    $stmt = $pdo->prepare('SELECT image FROM tours WHERE id = ?');
    $stmt->execute([$id]);
    $old_tour = $stmt->fetch();
    $image_filename = $old_tour['image'];

    // Handle Upload Gambar Baru
    if (!empty($_FILES['image']['tmp_name'])) {
        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $new_filename = 'tour_' . time() . '.' . $ext;
        if (move_uploaded_file($_FILES['image']['tmp_name'], $UPLOAD_DIR . '/' . $new_filename)) {
            $image_filename = $new_filename;
            if (!empty($old_tour['image']) && file_exists($UPLOAD_DIR . '/' . $old_tour['image'])) {
                unlink($UPLOAD_DIR . '/' . $old_tour['image']);
            }
        }
    }

    // Update Database
    $map_embed = $_POST['map_embed'] ?? ''; // Ambil data map
    $sql = "UPDATE tours SET title=?, price=?, contact=?, description=?, itinerary=?, destination_id=?, image=?, map_embed=? WHERE id=?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$title, $price, $contact, $description, $itinerary, $dest_id, $image_filename, $map_embed, $id]);

    flash_set('success', 'Paket tour berhasil diupdate.');
    session_write_close();
    header('Location: ../tour_detail.php?id=' . $id);
    exit;
}