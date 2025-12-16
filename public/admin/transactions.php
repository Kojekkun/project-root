<?php
// public/admin/transactions.php
require_once __DIR__ . '/../../app/db.php';
require_once __DIR__ . '/../../app/helpers.php';

require_login();

// Cek Admin
if (($_SESSION['user_role'] ?? '') !== 'admin') {
    header('Location: ../index.php'); exit;
}

// Ambil SEMUA transaksi
$sql = "SELECT t.*, u.name as user_name 
        FROM transactions t 
        JOIN users u ON t.user_id = u.id 
        ORDER BY t.created_at DESC";
$stmt = $pdo->query($sql);
$transactions = $stmt->fetchAll();
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Laporan Keuangan Admin</title>
    <link rel="stylesheet" href="../assets/css/style.css?v=5">
</head>
<body>
    <nav class="nav">
        <a class="brand" href="../index.php">Admin Panel</a>
        <div class="nav-right">
            <a href="../index.php" style="text-decoration:none; color:#555;">&larr; Kembali</a>
        </div>
    </nav>

    <main class="container">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h1 style="margin: 0;">📊 Semua Transaksi User</h1>
            
            <a href="../export_pdf.php" target="_blank" class="btn" style="background-color: #ef4444;">
                📄 Export Laporan PDF
            </a>
        </div>

        <div class="card" style="padding:0; overflow:hidden;">
            <table style="width:100%; border-collapse:collapse;">
                <thead style="background:#f3f4f6; border-bottom:1px solid #ddd;">
                    <tr>
                        <th style="padding:15px; text-align:left;">Tanggal</th>
                        <th style="padding:15px; text-align:left;">User</th>
                        <th style="padding:15px; text-align:left;">Keterangan</th>
                        <th style="padding:15px; text-align:right;">Nominal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($transactions as $t): ?>
                    <tr style="border-bottom:1px solid #eee;">
                        <td style="padding:15px; color:#666;">
                            <?= date('d M Y H:i', strtotime($t['created_at'])) ?>
                        </td>
                        <td style="padding:15px; font-weight:bold; color:#2563eb;">
                            <?= e($t['user_name']) ?>
                        </td>
                        <td style="padding:15px;">
                            <?= e($t['description']) ?>
                            <br>
                            <?php if($t['type'] == 'credit'): ?>
                                <span style="font-size:0.8rem; background:#dcfce7; color:#166534; padding:2px 6px; border-radius:4px;">Uang Masuk</span>
                            <?php else: ?>
                                <span style="font-size:0.8rem; background:#fee2e2; color:#991b1b; padding:2px 6px; border-radius:4px;">Keluar</span>
                            <?php endif; ?>
                        </td>
                        <td style="padding:15px; text-align:right;">
                            Rp <?= number_format($t['amount'], 0, ',', '.') ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>