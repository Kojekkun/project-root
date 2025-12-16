<?php
// app/controllers/booking_handler.php (VERSI FINAL: UANG MASUK KE ADMIN)
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../helpers.php';

require_login();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_check($_POST['csrf'] ?? '')) {
        flash_set('error', 'CSRF Error'); 
        header('Location: ../tour_detail.php?id=' . $_POST['tour_id']); exit;
    }

    $user_id = $_SESSION['user_id'];
    $tour_id = intval($_POST['tour_id']);
    $date = $_POST['tour_date'];
    
    // Asumsi: Admin adalah user dengan ID 1
    $admin_id = 1; 

    try {
        $pdo->beginTransaction();

        // 1. Ambil Data Tour & Harga
        $stmt = $pdo->prepare("SELECT title, price FROM tours WHERE id = ?");
        $stmt->execute([$tour_id]);
        $tour = $stmt->fetch();
        
        if (!$tour) throw new Exception("Paket tour tidak ditemukan.");
        $amount = $tour['price'];

        // 2. Cek Saldo User (Lock Row)
        $stmt = $pdo->prepare("SELECT balance FROM users WHERE id = ? FOR UPDATE");
        $stmt->execute([$user_id]);
        $user = $stmt->fetch();

        if ($user['balance'] < $amount) {
            throw new Exception("Saldo Anda tidak mencukupi.");
        }

        // 3. POTONG SALDO USER
        $stmt = $pdo->prepare("UPDATE users SET balance = balance - ? WHERE id = ?");
        $stmt->execute([$amount, $user_id]);

        // 4. TAMBAH SALDO ADMIN (Uang Masuk ke Perusahaan)
        $stmt = $pdo->prepare("UPDATE users SET balance = balance + ? WHERE id = ?");
        $stmt->execute([$amount, $admin_id]);

        // 5. CATAT BOOKING
        $stmt = $pdo->prepare("INSERT INTO bookings (user_id, tour_id, tour_date, amount, status, created_at) VALUES (?, ?, ?, ?, 'paid', NOW())");
        $stmt->execute([$user_id, $tour_id, $date, $amount]);

        // 6. CATAT RIWAYAT (Debit User)
        $desc = "Pembayaran Tour: " . $tour['title'] . " (" . $date . ")";
        $stmt = $pdo->prepare("INSERT INTO transactions (user_id, type, amount, description, created_at) VALUES (?, 'debit', ?, ?, NOW())");
        $stmt->execute([$user_id, $amount, $desc]);

        // 7. CATAT RIWAYAT (Credit Admin - Pendapatan)
        // Opsional: Agar Admin bisa lihat laporan pemasukan di history-nya
        $desc_admin = "Pemasukan dari User ID $user_id: " . $tour['title'];
        $stmt = $pdo->prepare("INSERT INTO transactions (user_id, type, amount, description, created_at) VALUES (?, 'credit', ?, ?, NOW())");
        $stmt->execute([$admin_id, $amount, $desc_admin]);

        $pdo->commit();

        flash_set('success', 'Pembayaran berhasil! Tiket tour telah dipesan.');
        header('Location: ../../public/history.php');
        exit;

    } catch (Exception $e) {
        $pdo->rollBack();
        flash_set('error', 'Gagal Memesan: ' . $e->getMessage());
        header('Location: ../tour_detail.php?id=' . $tour_id);
        exit;
    }
}