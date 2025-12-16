<?php
// app/controllers/topup_handler.php (VERSI FIX REDIRECT)
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../helpers.php';

// Pastikan Admin
if (empty($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'admin') {
    header('Location: ../login.php'); exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Jika CSRF gagal, kembalikan ke halaman form
    if (!csrf_check($_POST['csrf'] ?? '')) {
        flash_set('error', 'CSRF Error'); 
        header('Location: topup.php'); // FIX: Cukup panggil nama filenya
        exit;
    }

    $admin_id = $_SESSION['user_id'];
    $user_id = intval($_POST['user_id']);
    $amount = floatval($_POST['amount']);
    $desc = trim($_POST['description']);

    if ($user_id <= 0 || $amount <= 0) {
        flash_set('error', 'Data tidak valid.'); 
        header('Location: topup.php'); // FIX
        exit;
    }

    try {
        $pdo->beginTransaction();

        // 1. Cek Saldo Admin
        $stmt = $pdo->prepare("SELECT balance FROM users WHERE id = ? FOR UPDATE");
        $stmt->execute([$admin_id]);
        $admin = $stmt->fetch();

        if ($admin['balance'] < $amount) {
            throw new Exception("Saldo Admin tidak cukup.");
        }

        // 2. Potong Saldo Admin
        $stmt = $pdo->prepare("UPDATE users SET balance = balance - ? WHERE id = ?");
        $stmt->execute([$amount, $admin_id]);

        // 3. Tambah Saldo User
        $stmt = $pdo->prepare("UPDATE users SET balance = balance + ? WHERE id = ?");
        $stmt->execute([$amount, $user_id]);

        // 4. Catat Transaksi
        $stmt = $pdo->prepare("INSERT INTO transactions (user_id, type, amount, description, created_at) VALUES (?, 'credit', ?, ?, NOW())");
        $stmt->execute([$user_id, $amount, $desc]);

        $pdo->commit();

        // SUKSES: Set pesan sukses
        flash_set('success', '✅ BERHASIL! Mengirim saldo Rp ' . number_format($amount, 0, ',', '.') . ' ke user.');
        
        // --- PERBAIKAN UTAMA DI SINI ---
        // Jangan pakai '../public/admin/topup.php' karena akan jadi double public.
        // Cukup 'topup.php' karena file ini dipanggil oleh process_topup.php yang satu folder.
        header('Location: topup.php'); 
        exit;

    } catch (Exception $e) {
        $pdo->rollBack();
        flash_set('error', '❌ Gagal: ' . $e->getMessage());
        header('Location: topup.php'); // FIX
        exit;
    }
}