<?php
// public/process_booking.php
require_once __DIR__ . '/../app/db.php';
require_once __DIR__ . '/../app/helpers.php';

require_login();

// Validasi Request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: tours.php');
    exit;
}

// 1. Cek CSRF
if (!csrf_check($_POST['csrf'] ?? '')) {
    flash_set('error', 'Token keamanan tidak valid.');
    header('Location: tours.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$tour_id = filter_input(INPUT_POST, 'tour_id', FILTER_VALIDATE_INT);
$tour_date = $_POST['tour_date'] ?? '';

if (!$tour_id || empty($tour_date)) {
    flash_set('error', 'Data booking tidak lengkap.');
    header('Location: tours.php');
    exit;
}

try {
    $pdo->beginTransaction();

    // 2. Ambil Info Tour & Harga
    $stmt = $pdo->prepare("SELECT price, title FROM tours WHERE id = ? FOR UPDATE");
    $stmt->execute([$tour_id]);
    $tour = $stmt->fetch();

    if (!$tour) throw new Exception("Paket tour tidak ditemukan.");
    $price = $tour['price'];

    // 3. Cek Saldo User
    $stmt = $pdo->prepare("SELECT balance FROM users WHERE id = ? FOR UPDATE");
    $stmt->execute([$user_id]);
    $user_balance = $stmt->fetchColumn();

    if ($user_balance < $price) {
        throw new Exception("Saldo tidak cukup.");
    }

    // 4. Potong Saldo User
    $stmt = $pdo->prepare("UPDATE users SET balance = balance - ? WHERE id = ?");
    $stmt->execute([$price, $user_id]);

    // 5. Simpan Data Booking
    $stmt = $pdo->prepare("INSERT INTO bookings (user_id, tour_id, tour_date, amount, status, created_at) VALUES (?, ?, ?, ?, 'paid', NOW())");
    $stmt->execute([$user_id, $tour_id, $tour_date, $price]);

    // 6. [PENTING] Catat di Tabel Transactions (Agar muncul di Riwayat Keuangan)
    // Kita gunakan tipe 'debit' artinya uang keluar
    $stmt = $pdo->prepare("INSERT INTO transactions (user_id, type, amount, description, created_at) VALUES (?, 'debit', ?, ?, NOW())");
    $desc = "Pembayaran Tour: " . $tour['title'];
    $stmt->execute([$user_id, $price, $desc]);

    $pdo->commit();

    // 7. Sukses & Redirect
    flash_set('success', '✅ Pembayaran Berhasil! Paket tour telah dibooking.');
    header('Location: history.php'); // Langsung arahkan ke History
    exit;

} catch (Exception $e) {
    $pdo->rollBack();
    flash_set('error', 'Gagal Booking: ' . $e->getMessage());
    header('Location: tour_detail.php?id=' . $tour_id);
    exit;
}
?>