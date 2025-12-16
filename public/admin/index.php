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
    <link rel="stylesheet" href="../assets/css/style.css?v=8">
    <style>
        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-top: 20px;
            margin-bottom: 40px;
        }
        .stat-card {
            background: white;
            padding: 25px;
            border-radius: var(--radius-xl);
            box-shadow: var(--shadow-soft);
            text-align: center;
            border: 1px solid #f1f5f9;
            transition: transform 0.2s;
        }
        .stat-card:hover { transform: translateY(-5px); border-color: var(--accent); }
        .stat-number { font-size: 2.5rem; font-weight: 800; color: var(--primary); margin: 10px 0; }
        .stat-label { color: var(--secondary); font-weight: 700; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 0.5px; }
        
        .action-list { list-style: none; padding: 0; margin-top: 15px; text-align: left; }
        .action-list li { margin-bottom: 10px; border-bottom: 1px dashed #eee; padding-bottom: 5px; }
        .action-list a { text-decoration: none; display: block; font-weight: 600; color: var(--primary); }
        .action-list a:hover { color: var(--accent); }

        /* Style Tambahan untuk Tabel User */
        .table-responsive { overflow-x: auto; }
        .table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        .table th { text-align: left; padding: 15px; background: #f8fafc; border-bottom: 2px solid #e2e8f0; color: var(--secondary); font-size: 0.85rem; font-weight: 700; text-transform: uppercase; }
        .table td { padding: 15px; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
        .badge-role { padding: 4px 10px; border-radius: 50px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; }
    </style>
</head>
<body>
    <nav class="nav">
        <a class="brand" href="../index.php">Admin Panel</a>
        <div class="nav-right">
            <a href="../index.php" class="nav-link">Lihat Website</a>
            <div style="width:1px; height:20px; background:#ddd; margin:0 15px;"></div>
            <a href="../logout.php" class="btn" style="background: #ef4444; padding: 8px 15px; font-size: 0.85rem;">Logout</a>
        </div>
    </nav>

    <main class="container">
        <?php if($m=flash_get('success')): ?><div class="alert alert-success" style="margin-top:20px;">✅ <?= e($m) ?></div><?php endif; ?>
        <?php if($m=flash_get('error')): ?><div class="alert alert-danger" style="margin-top:20px;">⚠️ <?= e($m) ?></div><?php endif; ?>

        <div style="margin-top: 40px; margin-bottom: 20px;">
            <h1 style="margin-bottom: 5px;">👋 Halo, Admin!</h1>
            <p class="muted">Selamat datang di pusat kontrol. Apa yang ingin Anda kerjakan hari ini?</p>
        </div>

        <div class="dashboard-grid">
            
            <div class="stat-card">
                <div style="font-size: 3rem; margin-bottom: 10px;">🏝️</div>
                <div class="stat-label">Destinasi & Tour</div>
                <ul class="action-list">
                    <li><a href="destination_add.php">+ Tambah Destinasi</a></li>
                    <li><a href="tour_add.php">+ Tambah Paket Tour</a></li>
                    <li><a href="../tours.php" target="_blank">Lihat Preview Tour &rarr;</a></li>
                </ul>
            </div>

            <div class="stat-card">
                <div style="font-size: 3rem; margin-bottom: 10px;">💸</div>
                <div class="stat-label">Keuangan & Saldo</div>
                <ul class="action-list">
                    <li><a href="topup.php" style="color:var(--accent);">+ Top Up Saldo User</a></li>
                    <li><a href="transactions.php">📊 Laporan Transaksi</a></li>
                </ul>
            </div>

            <div class="stat-card" style="background: #f8fafc; border: 1px dashed #cbd5e1;">
                <div class="stat-label">Total User</div>
                <div class="stat-number"><?= $total_users ?></div>
                <div class="stat-label">Total Booking</div>
                <div class="stat-number" style="color:var(--accent); font-size: 1.8rem;"><?= $total_bookings ?></div>
            </div>

        </div>

        <div class="card">
            <div style="padding: 20px; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center;">
                <h2 style="margin:0; font-size: 1.25rem;">👥 Manajemen Pengguna</h2>
                <span class="muted" style="font-size: 0.9rem;">Hapus user bermasalah di sini</span>
            </div>
            
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>User Info</th>
                            <th>Email</th>
                            <th>Saldo</th>
                            <th>Role</th>
                            <th style="text-align: right;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Ambil semua data user KECUALI diri sendiri (admin yg sedang login)
                        $stmt_users = $pdo->prepare("SELECT * FROM users WHERE id != ? ORDER BY id DESC");
                        $stmt_users->execute([$_SESSION['user_id']]);
                        $users = $stmt_users->fetchAll();
                        ?>

                        <?php if(count($users) == 0): ?>
                            <tr><td colspan="5" class="text-center muted">Belum ada user lain yang terdaftar.</td></tr>
                        <?php endif; ?>
                        
                        <?php foreach($users as $u): ?>
                        <tr>
                            <td>
                                <div style="display:flex; align-items:center; gap:12px;">
                                    <?php 
                                        $ava = !empty($u['avatar']) ? '../uploads/avatars/'.$u['avatar'] : '../assets/images/placeholder.jpg';
                                        if (!file_exists($ava) && strpos($ava, 'http') === false) $ava = '../assets/images/placeholder.jpg';
                                    ?>
                                    <img src="<?= $ava ?>" style="width:40px; height:40px; border-radius:50%; object-fit:cover; border: 1px solid #e2e8f0;">
                                    <div>
                                        <div style="font-weight:bold; color:var(--primary);"><?= htmlspecialchars($u['name']) ?></div>
                                        <small class="muted">ID: #<?= $u['id'] ?></small>
                                    </div>
                                </div>
                            </td>
                            <td><?= htmlspecialchars($u['email']) ?></td>
                            <td style="font-weight:600; color: #16a34a;">Rp <?= number_format($u['balance'], 0, ',', '.') ?></td>
                            <td>
                                <span class="badge-role" style="background: <?= $u['role'] === 'admin' ? '#fde68a; color:#92400e' : '#e2e8f0; color:#475569' ?>;">
                                    <?= $u['role'] ?>
                                </span>
                            </td>
                            <td style="text-align: right;">
                                <form action="process_delete_user.php" method="POST" onsubmit="return confirm('⚠️ PERINGATAN KERAS:\n\nMenghapus user \'<?= htmlspecialchars($u['name']) ?>\' akan menghapus permanen:\n- Semua data booking mereka\n- Semua riwayat transaksi\n\nTindakan ini TIDAK BISA DIBATALKAN. Lanjutkan?');" style="display:inline;">
                                    <input type="hidden" name="id" value="<?= $u['id'] ?>">
                                    <button type="submit" class="btn" style="background:#fee2e2; color:#ef4444; border:1px solid #fecaca; padding:6px 14px; font-size:0.85rem; border-radius:8px;">
                                        🗑️ Hapus Akun
                                    </button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <br><br>
    </main>
</body>
</html>