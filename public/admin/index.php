<?php
// public/admin/index.php
require_once __DIR__ . '/../../app/helpers.php';
require_once __DIR__ . '/../../app/db.php';

require_login();

// Cek apakah user benar-benar Admin
if (($_SESSION['user_role'] ?? '') !== 'admin') {
    header('Location: ../index.php');
    exit;
}

// (Opsional) Hitung statistik sederhana untuk dashboard
$total_users = $pdo->query("SELECT COUNT(*) FROM users WHERE role='user'")->fetchColumn();
$total_tours = $pdo->query("SELECT COUNT(*) FROM tours")->fetchColumn();
$total_bookings = $pdo->query("SELECT COUNT(*) FROM bookings")->fetchColumn();
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Admin Dashboard - Pariwisata</title>
    <link rel="stylesheet" href="../assets/css/style.css?v=7">
    <style>
        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        .stat-card {
            background: white;
            padding: 25px;
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            text-align: center;
            border: 1px solid #eee;
            transition: transform 0.2s;
        }
        .stat-card:hover { transform: translateY(-5px); border-color: var(--primary); }
        .stat-number { font-size: 2.5rem; font-weight: bold; color: var(--primary); margin: 10px 0; }
        .stat-label { color: var(--text-muted); font-weight: bold; }
        
        .action-list { list-style: none; padding: 0; margin-top: 15px; text-align: left; }
        .action-list li { margin-bottom: 10px; border-bottom: 1px dashed #eee; padding-bottom: 5px; }
        .action-list a { text-decoration: none; display: block; font-weight: bold; }
        .action-list a:hover { color: var(--accent); }
    </style>
</head>
<body>
    <nav class="nav">
        <a class="brand" href="../index.php">Admin Panel</a>
        <div class="nav-right">
            <a href="../index.php" style="text-decoration:none; color:var(--text-muted);">Lihat Website</a>
            <div class="nav-separator"></div>
            <a href="../logout.php" class="btn-logout">Logout</a>
        </div>
    </nav>

    <main class="container">
        <h1 style="margin-bottom: 10px;">👋 Halo, Admin!</h1>
        <p class="muted">Selamat datang di pusat kontrol. Apa yang ingin Anda kerjakan hari ini?</p>

        <div class="dashboard-grid">
            
            <div class="stat-card">
                <div style="font-size: 3rem;">🏝️</div>
                <div class="stat-label">Destinasi & Tour</div>
                <ul class="action-list">
                    <li><a href="destination_add.php">+ Tambah Destinasi</a></li>
                    <li><a href="tour_add.php">+ Tambah Paket Tour</a></li>
                    <li><a href="../destinations.php" target="_blank">Lihat Semua Destinasi &rarr;</a></li>
                    <li><a href="../tours.php" target="_blank">Lihat Semua Tour &rarr;</a></li>
                </ul>
            </div>

            <div class="stat-card">
                <div style="font-size: 3rem;">💸</div>
                <div class="stat-label">Keuangan & Saldo</div>
                <ul class="action-list">
                    <li><a href="topup.php" style="color:var(--primary);">+ Kirim Saldo (Top Up)</a></li>
                    <li><a href="transactions.php">📊 Laporan Keuangan</a></li>
                    <li><small class="muted">Cek riwayat transaksi masuk & keluar.</small></li>
                </ul>
            </div>

            <div class="stat-card" style="background: var(--bg-body);">
                <div class="stat-label">Total User</div>
                <div class="stat-number"><?= $total_users ?></div>
                <div class="stat-label">Total Booking</div>
                <div class="stat-number" style="color:var(--accent);"><?= $total_bookings ?></div>
            </div>

        </div>
    </main>
</body>
</html>