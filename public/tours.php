<?php
// public/tours.php
require_once __DIR__ . '/../app/db.php';
require_once __DIR__ . '/../app/helpers.php';

// Logika Pencarian & Filter
$keyword = trim($_GET['q'] ?? '');
$category_filter = $_GET['cat'] ?? '';

$categories = $pdo->query("SELECT * FROM categories ORDER BY name ASC")->fetchAll();

$sql = "SELECT t.*, d.image as dest_image, d.title as dest_title, d.category_id 
        FROM tours t 
        LEFT JOIN destinations d ON t.destination_id = d.id 
        WHERE 1=1";

$params = [];

if (!empty($keyword)) {
    $sql .= " AND (t.title LIKE ? OR t.description LIKE ?)";
    $params[] = "%$keyword%";
    $params[] = "%$keyword%";
}

if (!empty($category_filter)) {
    $sql .= " AND d.category_id = ?";
    $params[] = $category_filter;
}

$sql .= " ORDER BY t.id DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$tours = $stmt->fetchAll();
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Semua Paket Tour - Pariwisata</title>
    <link rel="stylesheet" href="assets/css/style.css?v=17">
</head>
<body>
    
    <nav class="nav">
        <a class="brand" href="index.php">Pariwisata.</a>
        <div class="nav-right">
            <a href="index.php" class="nav-link">Beranda</a>
            <a href="tours.php" class="nav-link active">Paket Tour</a>
            <?php if(is_logged()): ?>
                <div class="nav-separator"></div>
                <?php if($_SESSION['user_role'] === 'admin'): ?>
                    <a href="admin/index.php" class="btn" style="padding: 8px 15px; font-size: 0.8rem;">Admin</a>
                <?php endif; ?>
                <a href="profile.php" class="nav-profile">
                    <?php 
                        $avatar_url = !empty($_SESSION['user_avatar']) ? get_image_url('avatars/' . $_SESSION['user_avatar']) : 'assets/images/placeholder.jpg';
                        if (strpos($avatar_url, 'http') === false && !file_exists($avatar_url)) $avatar_url = 'assets/images/placeholder.jpg';
                    ?>
                    <img src="<?= $avatar_url ?>" class="nav-avatar" alt="Profil">
                </a>
            <?php else: ?>
                <span style="margin: 0 10px; color:#ccc;">|</span>
                <a href="login.php" class="nav-link">Masuk</a>
                <a href="register.php" class="btn">Daftar</a>
            <?php endif; ?>
        </div>
    </nav>

    <div class="page-header">
        <h1 class="page-title">Paket Perjalanan</h1>
        <p class="page-desc">Temukan berbagai pilihan paket wisata menarik yang telah kami kurasi khusus untuk pengalaman liburan terbaik Anda.</p>
    </div>

    <main class="container">
        
        <form action="tours.php" method="get" class="search-container">
    
            <div class="search-group">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="search-icon">
                    <circle cx="11" cy="11" r="8"></circle>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                </svg>
                <input type="text" name="q" class="search-input" placeholder="Cari paket (misal: Bali)..." value="<?= e($keyword) ?>">
            </div>

            <div class="search-divider"></div>

            <div class="search-group">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="search-icon">
                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                    <polyline points="9 22 9 12 15 12 15 22"></polyline>
                </svg>
                <select name="cat" class="search-select">
                    <option value="">Semua Kategori</option>
                    <?php foreach($categories as $c): ?>
                        <option value="<?= $c['id'] ?>" <?= $c['id'] == $category_filter ? 'selected' : '' ?>>
                            <?= e($c['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <button type="submit" class="search-btn">Cari</button>
        </form>

        <?php if(!empty($keyword) || !empty($category_filter)): ?>
            <div class="search-reset-container">
                <a href="tours.php" class="reset-btn">
                    <span>↺</span> Reset Filter Pencarian
                </a>
            </div>
        <?php endif; ?>

        <?php if(count($tours) == 0): ?>
            <div class="text-center" style="padding:60px; background:white; border-radius:24px; box-shadow:0 4px 6px rgba(0,0,0,0.05);">
                <h3>Yah, paket tidak ditemukan 🍃</h3>
                <p class="muted">Coba ganti kata kunci atau pilih kategori lain.</p>
                <a href="tours.php" class="btn" style="margin-top:15px; background:var(--secondary);">Lihat Semua Paket</a>
            </div>
        <?php else: ?>
            <div class="grid">
                <?php foreach($tours as $t): ?>
                    <?php 
                        $img = !empty($t['dest_image']) ? $t['dest_image'] : $t['image']; 
                    ?>
                    <article class="card">
                        <div style="position:relative;">
                            <img src="<?= get_image_url($img) ?>" class="card-img" style="height:220px;">
                            
                            <span style="position:absolute; bottom:15px; right:15px; background:rgba(255,255,255,0.95); color:var(--accent); padding:6px 14px; border-radius:50px; font-size:0.85rem; font-weight:800; box-shadow:0 4px 10px rgba(0,0,0,0.1);">
                                IDR <?= number_format($t['price']/1000, 0) ?>K
                            </span>
                        </div>
                        
                        <div class="card-body">
                            <small style="text-transform:uppercase; font-size:0.75rem; font-weight:700; color:var(--secondary); letter-spacing:0.5px;">
                                <?= e($t['dest_title'] ?? 'TRIP') ?>
                            </small>

                            <h3 style="font-size:1.2rem; margin:5px 0 10px; font-weight:700; line-height:1.4;">
                                <?= e($t['title']) ?>
                            </h3>
                            
                            <p class="muted" style="font-size:0.9rem; margin-bottom:20px; line-height:1.6;">
                                <?= substr(e($t['description']), 0, 90) ?>...
                            </p>
                            
                            <a href="tour_detail.php?id=<?= $t['id'] ?>" class="btn" style="width:100%; border-radius:10px;">Lihat Detail</a>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </main>

    <footer>
        <h3 class="brand" style="font-size:1.5rem; margin-bottom:10px;">Pariwisata.</h3>
        <p class="muted">&copy; <?= date('Y') ?> Pariwisata Inc. All rights reserved.</p>
    </footer>
</body>
</html>