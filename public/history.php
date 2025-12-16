<?php
// public/history.php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$db_file = __DIR__ . '/../app/db.php';
$helper_file = __DIR__ . '/../app/helpers.php';
require_once $db_file;
require_once $helper_file;

require_login();
$user_id = $_SESSION['user_id'];

$history = [];

try {
    $sql_booking = "
        SELECT 'booking' as type, b.id, t.title as description, b.amount as amount, b.status, b.created_at 
        FROM bookings b 
        JOIN tours t ON b.tour_id = t.id 
        WHERE b.user_id = ?
    ";
    $stmt = $pdo->prepare($sql_booking);
    $stmt->execute([$user_id]);
    $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $sql_trans = "
        SELECT 'transaction' as type, id, description, amount, 'success' as status, created_at 
        FROM transactions 
        WHERE user_id = ?
    ";
    $stmt = $pdo->prepare($sql_trans);
    $stmt->execute([$user_id]);
    $transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $history = array_merge($bookings, $transactions);
    usort($history, function($a, $b) {
        return strtotime($b['created_at']) - strtotime($a['created_at']);
    });

} catch (PDOException $e) {
    die("<div style='background:red; color:white; padding:20px;'><h3>⚠️ SQL Error:</h3>" . $e->getMessage() . "</div>");
}
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Riwayat Aktivitas - Pariwisata</title>
    <link rel="stylesheet" href="assets/css/style.css?v=21">
    <style>
        .badge { padding: 4px 10px; border-radius: 50px; font-size: 0.8rem; font-weight: 600; }
        .bg-green { background: #dcfce7; color: #166534; }
        .bg-yellow { background: #fef9c3; color: #854d0e; }
        .bg-red { background: #fee2e2; color: #991b1b; }
        .table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .table th { text-align: left; padding: 12px; border-bottom: 2px solid #eee; }
        .table td { padding: 12px; border-bottom: 1px solid #eee; }
    </style>
</head>
<body>
    
    <nav class="nav">
        <a class="brand" href="index.php">Pariwisata.</a>
        <div class="nav-right">
            <a href="index.php" class="nav-link">Beranda</a>
            <a href="tours.php" class="nav-link">Paket Tour</a>
            <a href="profile.php" class="nav-link">Profil</a>
            <a href="logout.php" class="nav-link" style="color:#ef4444;">Logout</a>
        </div>
    </nav>

    <main class="container">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h1>📜 Riwayat Aktivitas</h1>
            <a href="export_pdf.php" target="_blank" class="btn" style="background-color: #ef4444; color: white; padding: 8px 15px; text-decoration: none; border-radius: 5px;">
                📄 Download PDF
            </a>
        </div>

        <div class="card" style="padding:0; overflow:hidden;">
            <?php if(empty($history)): ?>
                <div style="padding:50px; text-align:center; color:#666;">
                    <p>Belum ada aktivitas.</p>
                    <a href="tours.php" class="btn" style="padding:10px 20px; background:#2563eb; color:white; text-decoration:none; border-radius:5px;">Mulai Pesan Tiket</a>
                </div>
            <?php else: ?>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Keterangan</th>
                            <th style="text-align:right;">Nominal</th>
                            <th style="text-align:center;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($history as $h): ?>
                        <tr>
                            <td style="color:#666; font-size:0.9rem;">
                                <?= date('d M Y', strtotime($h['created_at'])) ?><br>
                                <small><?= date('H:i', strtotime($h['created_at'])) ?></small>
                            </td>
                            <td>
                                <strong><?= htmlspecialchars($h['description'] ?? '-') ?></strong><br>
                                <small style="color:#888; text-transform: uppercase;">
                                    <?= $h['type'] === 'booking' ? '🎫 Booking' : '💳 Transaksi' ?>
                                </small>
                            </td>
                            <td style="text-align:right; font-weight:bold;">
                                <?php 
                                    $is_topup = ($h['type'] === 'transaction' && stripos($h['description'] ?? '', 'top up') !== false);
                                    $color = $is_topup ? '#16a34a' : '#333';
                                    $sign = $is_topup ? '+ ' : '';
                                ?>
                                <span style="color:<?= $color ?>;">
                                    <?= $sign ?>Rp <?= number_format($h['amount'], 0, ',', '.') ?>
                                </span>
                            </td>
                            <td style="text-align:center;">
                                <?php
                                    $status = strtolower($h['status']);
                                    $bg = 'bg-yellow';
                                    if($status == 'success' || $status == 'paid' || $status == 'confirmed') $bg = 'bg-green';
                                    if($status == 'failed' || $status == 'cancelled') $bg = 'bg-red';
                                ?>
                                <span class="badge <?= $bg ?>"><?= htmlspecialchars($status) ?></span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>