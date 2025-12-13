<?php
// public/profile_handler.php
require_once __DIR__ . '/../app/db.php';
require_once __DIR__ . '/../app/helpers.php';
require_once __DIR__ . '/../app/config.php'; // Butuh config untuk $MAX_UPLOAD_SIZE

require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: profile.php'); exit; }
if (!csrf_check($_POST['csrf'] ?? '')) {
    flash_set('error', 'Permintaan tidak valid (CSRF).');
    session_write_close(); header('Location: profile.php'); exit;
}

$user_id = $_SESSION['user_id'];
$name = trim($_POST['name'] ?? '');
$new_password = $_POST['new_password'] ?? '';

if (empty($name)) {
    flash_set('error', 'Nama tidak boleh kosong.');
    session_write_close(); header('Location: profile.php'); exit;
}

$update_fields = ['name = ?'];
$update_values = [$name];

// --- LOGIKA UPLOAD FOTO ---
if (!empty($_FILES['avatar']['tmp_name'])) {
    $allowed = ['image/jpeg', 'image/png', 'image/gif'];
    $max_size = 2 * 1024 * 1024; // 2MB
    
    // Validasi
    if (!in_array($_FILES['avatar']['type'], $allowed)) {
        flash_set('error', 'Format foto harus JPG atau PNG.');
        session_write_close(); header('Location: profile.php'); exit;
    }
    if ($_FILES['avatar']['size'] > $max_size) {
        flash_set('error', 'Ukuran foto maksimal 2MB.');
        session_write_close(); header('Location: profile.php'); exit;
    }

    // Buat folder uploads/avatars jika belum ada
    $avatar_dir = __DIR__ . '/uploads/avatars';
    if (!is_dir($avatar_dir)) mkdir($avatar_dir, 0755, true);

    // Generate nama file unik
    $ext = pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION);
    $new_filename = 'user_' . $user_id . '_' . time() . '.' . $ext;
    $target = $avatar_dir . '/' . $new_filename;

    if (move_uploaded_file($_FILES['avatar']['tmp_name'], $target)) {
        // Masukkan ke query update
        $update_fields[] = 'avatar = ?';
        $update_values[] = $new_filename;
        
        // Update Sesi Langsung (Supaya navbar berubah seketika)
        $_SESSION['user_avatar'] = $new_filename;
    }
}
// --------------------------

// Logic Password
if (!empty($new_password)) {
    if (strlen($new_password) < 6) {
        flash_set('error', 'Password baru minimal 6 karakter.');
        session_write_close(); header('Location: profile.php'); exit;
    }
    $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);
    $update_fields[] = 'password = ?';
    $update_values[] = $hashed_password;
}

$update_values[] = $user_id; 

$sql = 'UPDATE users SET ' . implode(', ', $update_fields) . ' WHERE id = ?';

try {
    $pdo->prepare($sql)->execute($update_values);
    $_SESSION['user_name'] = $name; 
    flash_set('success', 'Profil berhasil diupdate.');
} catch (PDOException $e) {
    flash_set('error', 'Gagal update database.');
}

session_write_close();
header('Location: profile.php');
exit;