<?php
// public/profile_handler.php (BARU: Handler untuk update profil)
require_once __DIR__ . '/../app/db.php';
require_once __DIR__ . '/../app/helpers.php';

require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: /profile.php'); exit; }
if (!csrf_check($_POST['csrf'] ?? '')) {
    flash_set('error', 'Permintaan tidak valid (CSRF).');
    header('Location: /profile.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$name = trim($_POST['name'] ?? '');
$new_password = $_POST['new_password'] ?? '';

if (empty($name)) {
    flash_set('error', 'Nama tidak boleh kosong.');
    header('Location: /profile.php');
    exit;
}

$update_fields = ['name = ?'];
$update_values = [$name];

// Cek dan hash password baru jika diisi
if (!empty($new_password)) {
    if (strlen($new_password) < 6) {
        flash_set('error', 'Password baru minimal 6 karakter.');
        header('Location: /profile.php');
        exit;
    }
    $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);
    $update_fields[] = 'password = ?';
    $update_values[] = $hashed_password;
}

$update_values[] = $user_id; // Parameter terakhir untuk WHERE clause

$sql = 'UPDATE users SET ' . implode(', ', $update_fields) . ' WHERE id = ?';

try {
    $pdo->prepare($sql)->execute($update_values);
    $_SESSION['user_name'] = $name; // Update session
    flash_set('success', 'Profil berhasil diupdate.');
} catch (PDOException $e) {
    error_log("Profile update failed: " . $e->getMessage());
    flash_set('error', 'Terjadi kesalahan sistem saat update profil.');
}

header('Location: /profile.php');
exit;