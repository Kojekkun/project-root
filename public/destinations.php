<?php
// public/destinations.php
require_once __DIR__ . '/../app/db.php';
require_once __DIR__ . '/../app/helpers.php';

$keyword = trim($_GET['q'] ?? '');
$cat_id = $_GET['cat'] ?? '';

$categories = $pdo->query("SELECT * FROM categories ORDER BY name ASC")->fetchAll();

$sql = "SELECT d.*, c.name as category_name FROM destinations d LEFT JOIN categories c ON d.category_id = c.id WHERE 1=1";
$params = [];

if (!empty($keyword)) {
    $sql .= " AND (d.title LIKE ? OR d.location LIKE ?)";
    $params[] = "%$keyword%";
    $params[] = "%$keyword%";
}
if (!empty($cat_id)) {
    $sql .= " AND d.category_id = ?";
    $params[] = $cat_id;
}
$sql .= " ORDER BY d.id DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$destinations = $stmt->fetchAll();
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Semua Destinasi - Travel Buddies</title>
    <link rel="stylesheet" href="assets/css/style.css?v=11">
</head>
<body>
    <nav class="nav">
        <a class="brand" href="index.php">Travel Buddies</a>
        <div class="nav-right">
            <a href="index.php" style="text-decoration:none; color:var(--text-main); font-weight:bold;">Beranda</a>
            <a href="tours.php" style="text-decoration:none; color:var(--text-main);">Paket Tour</a>
            <?php if(is_logged()): ?>
                <div class="nav-separator"></div>
                <?php if($_SESSION['user_role'] === 'admin'): ?>
                    <a href="admin/index.php" class="btn" style="padding: 8px 15px; font-size: 0.8rem; background: var(--secondary);">⚙️ Admin Panel</a>
                <?php endif; ?>
                <a href="profile.php" class="nav-profile">
                    <?php 
                        $avatar_url = !empty($_SESSION['user_avatar']) ? get_image_url('avatars/' . $_SESSION['user_avatar']) : 'assets/images/placeholder.jpg';
                        if (strpos($avatar_url, 'http') === false && !file_exists($avatar_url)) $avatar_url = 'assets/images/placeholder.jpg';
                    ?>
                    <img src="<?= $avatar_url ?>" class="nav-avatar" alt="Profil">
                    <span class="nav-name"><?= e($_SESSION['user_name']) ?></span>
                </a>
            <?php else: ?>
                <span style="margin: 0 10px; color:#ccc;">|</span>
                <a href="login.php" style="color:var(--text-main);">Masuk</a>
                <a href="register.php" class="btn">Daftar</a>
            <?php endif; ?>
        </div>
    </nav>

    <main class="container">
        <div style="text-align:center; margin-bottom: 40px; margin-top:20px;">
            <h1 style="font-size: 2.5rem; margin-bottom: 10px;">Jelajahi Wisata Indonesia</h1>
            <p class="muted">Temukan surga tersembunyi yang belum pernah Anda kunjungi</p>
        </div>

        <form action="destinations.php" method="get" class="search-container" style="max-width: 800px; margin: 0 auto 50px auto;">
            <input type="text" name="q" class="search-input" placeholder="Cari nama tempat atau lokasi..." value="<?= e($keyword) ?>">
            <select name="cat" class="search-select" style="max-width: 200px;">
                <option value="">Semua Kategori</option>
                <?php foreach($categories as $c): ?>
                    <option value="<?= $c['id'] ?>" <?= $c['id'] == $cat_id ? 'selected' : '' ?>><?= e($c['name']) ?></option>
                <?php endforeach; ?>
            </select>
            <button type="submit" class="search-btn">🔍 Cari</button>
            <?php if(!empty($keyword) || !empty($cat_id)): ?>
                <a href="destinations.php" class="reset-btn">Reset</a>
            <?php endif; ?>
        </form>

        <?php if(count($destinations) == 0): ?>
            <div style="text-align:center; padding:50px; background:#fff; border-radius:var(--radius); border:1px solid #eee;">
                <h3>Wah, belum ketemu nih! 🍃</h3>
                <p class="muted">Destinasi yang kamu cari belum tersedia.</p>
                <a href="destinations.php" class="btn" style="margin-top:10px;">Lihat Semua</a>
            </div>
        <?php else: ?>
            <div class="grid">
                <?php foreach($destinations as $d): ?>
                    <article class="card" style="padding: 0; overflow: hidden; display: flex; flex-direction: column;">
                        <div style="position: relative;">
                            <img src="<?= get_image_url($d['image']) ?>" alt="<?= e($d['title']) ?>" style="width: 100%; height: 220px; object-fit: cover;">
                            <span style="position: absolute; top: 15px; left: 15px; background: rgba(255,255,255,0.9); color: var(--primary); padding: 5px 12px; border-radius: 20px; font-weight: bold; font-size: 0.8rem; box-shadow: 0 2px 5px rgba(0,0,0,0.1);"><?= e($d['category_name'] ?? 'Umum') ?></span>
                        </div>
                        
                        <div style="padding: 20px; flex-grow: 1; display: flex; flex-direction: column;">
                            <h3 style="margin-top: 0; margin-bottom: 5px; font-size: 1.2rem;"><?= e($d['title']) ?></h3>
                            <p class="muted" style="font-size: 0.9rem; margin-bottom: 15px;">📍 <?= e($d['location']) ?></p>
                            
                            <div style="margin-top: auto; display:flex; gap:10px;">
                                <a class="btn" href="destination_detail.php?id=<?= $d['id'] ?>" style="flex:1; text-align: center; background:white; color:var(--primary); border:1px solid var(--primary);">
                                    Lihat Detail
                                </a>

                                <?php if(!empty($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
                                    <a href="admin/destination_edit.php?id=<?= $d['id'] ?>" class="btn" style="background:#f59e0b; padding:10px 15px;" title="Edit Destinasi">
                                        ✏️
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </main>

    <footer style="background:var(--bg-card); padding:40px 20px; text-align:center; border-top:1px solid #eee; margin-top:80px;">
        <h3 class="brand" style="font-size:1.5rem; margin-bottom:10px;">Travel Buddies</h3>
        <p class="muted">&copy; <?= date('Y') ?> Travel Buddies.</p>
    </footer>
</body>
</html>