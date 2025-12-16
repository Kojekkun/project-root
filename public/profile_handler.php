<?php
// public/profile_handler.php
require_once __DIR__ . '/../app/db.php';
require_once __DIR__ . '/../app/helpers.php';

require_login();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_check($_POST['csrf'] ?? '')) {
        flash_set('error', 'Token keamanan tidak valid.');
        header('Location: profile.php'); // Relative Path
        exit;
    }

    $user_id = $_SESSION['user_id'];
    $name = trim($_POST['name'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($name)) {
        flash_set('error', 'Nama tidak boleh kosong.');
        header('Location: profile.php'); // Relative Path
        exit;
    }

    try {
        // Update Nama
        $stmt = $pdo->prepare("UPDATE users SET name = ? WHERE id = ?");
        $stmt->execute([$name, $user_id]);
        $_SESSION['user_name'] = $name;

        // Update Password
        if (!empty($password)) {
            if (strlen($password) < 6) {
                flash_set('error', 'Password minimal 6 karakter.');
                header('Location: profile.php');
                exit;
            }
            $hash = password_hash($password, PASSWORD_BCRYPT);
            $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
            $stmt->execute([$hash, $user_id]);
        }

        // Update Avatar
        if (!empty($_FILES['avatar']['tmp_name'])) {
            $allowed = ['image/jpeg', 'image/png', 'image/webp'];
            if (!in_array($_FILES['avatar']['type'], $allowed) || $_FILES['avatar']['size'] > 2*1024*1024) {
                flash_set('error', 'Format gambar salah/terlalu besar (Max 2MB).');
                header('Location: profile.php');
                exit;
            }

            $ext = pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION);
            $filename = 'avatar_' . $user_id . '_' . time() . '.' . $ext;
            
            // Gunakan path absolut __DIR__ agar pasti ketemu foldernya
            $target_dir = __DIR__ . '/uploads/avatars';
            
            if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);

            if (move_uploaded_file($_FILES['avatar']['tmp_name'], $target_dir . '/' . $filename)) {
                // Hapus avatar lama
                $old = $pdo->query("SELECT avatar FROM users WHERE id=$user_id")->fetchColumn();
                if ($old && file_exists($target_dir . '/' . $old)) unlink($target_dir . '/' . $old);

                $pdo->prepare("UPDATE users SET avatar = ? WHERE id = ?")->execute([$filename, $user_id]);
                $_SESSION['user_avatar'] = $filename;
            }
        }

        flash_set('success', 'Profil berhasil diperbarui!');
        header('Location: profile.php'); // Relative Path
        exit;

    } catch (Exception $e) {
        flash_set('error', 'Error: ' . $e->getMessage());
        header('Location: profile.php'); // Relative Path
        exit;
    }
} else {
    header('Location: profile.php'); // Relative Path
    exit;
}