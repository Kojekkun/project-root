<?php
// app/controllers/tour_delete_handler.php
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../helpers.php';
require_once __DIR__ . '/../config.php';

if (empty($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'admin') {
    header('Location: ../login.php'); exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id'] ?? 0);
    
    // Ambil gambar untuk dihapus
    $stmt = $pdo->prepare('SELECT image FROM tours WHERE id = ?');
    $stmt->execute([$id]);
    $tour = $stmt->fetch();

    if ($tour) {
        if (!empty($tour['image']) && file_exists($UPLOAD_DIR . '/' . $tour['image'])) {
            unlink($UPLOAD_DIR . '/' . $tour['image']);
        }
        
        $pdo->prepare('DELETE FROM tours WHERE id = ?')->execute([$id]);
        flash_set('success', 'Paket tour dihapus.');
    }
    
    session_write_close();
    header('Location: ../tours.php');
    exit;
}