<?php
// public/login_handler.php
require_once __DIR__ . '/../app/db.php';
require_once __DIR__ . '/../app/helpers.php';

// Pastikan method POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.php');
    exit;
}

// Cek CSRF
if (!csrf_check($_POST['csrf'] ?? '')) {
    flash_set('error', 'Permintaan tidak valid (CSRF).');
    header('Location: login.php');
    exit;
}

$email = trim(strtolower($_POST['email'] ?? ''));
$password = $_POST['password'] ?? '';

// Fungsi Gagal Login
function login_fail() {
    flash_set('error', 'Email atau password salah.');
    header('Location: login.php');
    exit;
}

try {
    // Cek User di Database
    $stmt = $pdo->prepare('SELECT id, name, password, status, role, avatar FROM users WHERE email = ?');
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if (!$user) {
        login_fail(); 
    }

    if (!password_verify($password, $user['password'])) {
        login_fail(); 
    }

    if (($user['status'] ?? '') !== 'active') {
        flash_set('error', 'Akun Anda belum diaktivasi. Silakan cek email Anda.');
        header('Location: login.php');
        exit;
    }

    // --- LOGIN SUKSES ---
    // Regenerasi ID Sesi untuk mencegah Session Fixation (Penting!)
    session_regenerate_id(true);

    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_name'] = $user['name'];
    $_SESSION['user_role'] = $user['role']; 
    $_SESSION['user_avatar'] = $user['avatar'];

    // Paksa simpan sesi ke disk
    session_write_close();

    // Redirect ke halaman sukses (bukan langsung profile) untuk memastikan cookie ter-set
    header('Location: login_success.php');
    exit;

} catch (Exception $e) {
    flash_set('error', 'Terjadi kesalahan sistem.');
    header('Location: login.php');
    exit;
}
?>