<?php
// public/api_check_email.php
require_once __DIR__ . '/../app/db.php';

// Set Header agar browser tahu ini data JSON (bukan HTML)
header('Content-Type: application/json');

$email = trim($_GET['email'] ?? '');

if (empty($email)) {
    echo json_encode(['status' => 'error', 'message' => 'Email kosong']);
    exit;
}

try {
    // Cek apakah email ada di database
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user) {
        // Jika ketemu, berarti SUDAH DIPAKAI
        echo json_encode(['status' => 'taken']);
    } else {
        // Jika tidak ketemu, berarti TERSEDIA
        echo json_encode(['status' => 'available']);
    }
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Database error']);
}