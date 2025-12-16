<?php 
// public/index.php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__.'/../app/db.php'; 
require_once __DIR__.'/../app/helpers.php';

// Logika Pencarian
$keyword = trim($_GET['q'] ?? '');
$category_filter = $_GET['cat'] ?? '';

$dest = [];
$tours = [];
$categories = [];
$hero_images = [];
$error_db = "";

try {
    $categories = $pdo->query("SELECT * FROM categories ORDER BY name ASC")->fetchAll();

    // 1. Ambil Destinasi
    $sql_dest = "SELECT d.*, c.name as category FROM destinations d 
                 LEFT JOIN categories c ON d.category_id = c.id WHERE 1=1";
    $params_dest = [];

    if (!empty($keyword)) {
        $sql_dest .= " AND (d.title LIKE ? OR d.location LIKE ?)";
        $params_dest[] = "%$keyword%";
        $params_dest[] = "%$keyword%";
    }
    if (!empty($category_filter)) {
        $sql_dest .= " AND d.category_id = ?";
        $params_dest[] = $category_filter;
    }
    $sql_dest .= " ORDER BY d.id DESC LIMIT 6"; 
    $stmt = $pdo->prepare($sql_dest);
    $stmt->execute($params_dest);
    $dest = $stmt->fetchAll();

    // 2. Ambil Tour
    $sql_tour = "SELECT t.*, d.image as dest_image, d.title as dest_title 
                 FROM tours t 
                 LEFT JOIN destinations d ON t.destination_id = d.id 
                 WHERE 1=1";
    $params_tour = [];

    if (!empty($keyword)) {
        $sql_tour .= " AND (t.title LIKE ? OR t.description LIKE ?)";
        $params_tour[] = "%$keyword%";
        $params_tour[] = "%$keyword%";
    }
    $sql_tour .= " ORDER BY t.id DESC LIMIT 6"; 
    $stmt_tour = $pdo->prepare($sql_tour);
    $stmt_tour->execute($params_tour);
    $tours = $stmt_tour->fetchAll();

    // 3. Ambil Gambar Slideshow
    $stmt_hero = $pdo->query("SELECT image FROM destinations WHERE image IS NOT NULL AND image != '' ORDER BY RAND() LIMIT 5");
    $raw_images = $stmt_hero->fetchAll(PDO::FETCH_COLUMN);
    
    if (!empty($raw_images)) {
        $hero_images = array_map('get_image_url', $raw_images);
    }
    
    // Fallback HD
    if (count($hero_images) < 3) {
        $hd_stock = [
            'https://images.unsplash.com/photo-1506744038136-46273834b3fb?ixlib=rb-1.2.1&auto=format&fit=crop&w=1920&q=95',
            'https://images.unsplash.com/photo-1511576661531-b34d7da5d0bb?ixlib=rb-1.2.1&auto=format&fit=crop&w=1920&q=95',
            'https://images.unsplash.com/photo-1476514525535-07fb3b4ae5f1?ixlib=rb-1.2.1&auto=format&fit=crop&w=1920&q=95',
            'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?ixlib=rb-1.2.1&auto=format&fit=crop&w=1920&q=95'
        ];
        $hero_images = array_slice(array_merge($hero_images, $hd_stock), 0, 5);
    }

} catch (Exception $e) {
    $error_db = $e->getMessage();
}
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Pariwisata</title>
    <link rel="stylesheet" href="assets/css/style.css?v=16"> 
</head>
<body>
    <nav class="nav">
        <a class="brand" href="index.php">Pariwisata.</a>
        <div class="nav-right">
        <a href="index.php" class="nav-link">Beranda</a>
        <a href="tours.php" class="nav-link">Paket Tour</a>
        
        <?php if(is_logged()): ?>
            <div class="nav-separator"></div>
            <?php if($_SESSION['user_role'] === 'admin'): ?>
                <a href="admin/index.php" class="btn" style="padding: 8px 15px; font-size: 0.8rem;">Admin</a>
            <?php endif; ?>
            
            <a href="profile.php" class="nav-profile">
                <?php 
                    $avatar_url = !empty($_SESSION['user_avatar']) ? 'uploads/avatars/' . $_SESSION['user_avatar'] : 'assets/images/placeholder.jpg';
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

    <div class="hero-section">
        <div class="hero-slideshow" id="slideshowContainer">
            <?php foreach($hero_images as $index => $img): ?>
                <div class="hero-slide <?= $index === 0 ? 'active' : '' ?>" style="background-image: url('<?= $img ?>');"></div>
            <?php endforeach; ?>
        </div>
        <div class="hero-overlay"></div>
        
        <div class="hero-content">
            <h1 class="hero-title">Explore Indonesia<br>Beyond Limits</h1>
            <p class="hero-subtitle">Temukan pengalaman liburan tak terlupakan bersama kami.</p>
            
            <form action="index.php" method="get" class="hero-search">
    
                <div class="search-group">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="search-icon">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    </svg>
                    
                    <input type="text" name="q" class="search-input" placeholder="Mau kemana hari ini?" value="<?= e($keyword) ?>">
                </div>

                <div class="search-divider"></div>

                <div class="search-group">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="search-icon">
                        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                        <polyline points="9 22 9 12 15 12 15 22"></polyline>
                    </svg>
                    
                    <select name="cat" class="search-select">
                        <option value="">Semua Kategori</option>
                        <?php foreach($categories as $c): ?>
                            <option value="<?= $c['id'] ?>" <?= $c['id'] == $category_filter ? 'selected' : '' ?>><?= e($c['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <button type="submit" class="search-btn">Cari</button>
            </form>

        </div>
    </div>

    <main class="container">
        <?php if ($error_db): ?>
            <div class="alert alert-danger"><h3>⚠️ Error:</h3><p><?= e($error_db) ?></p></div>
        <?php endif; ?>

        <div class="section-header">
            <div>
                <h2 class="section-title">Destinasi Trending</h2>
                <p class="section-desc">Spot liburan paling diminati bulan ini.</p>
            </div>
        </div>

        <?php if (count($dest) == 0): ?><div style="text-align:center; padding:60px;"><p class="muted">Belum ada data destinasi.</p></div><?php endif; ?>

        <div class="grid">
            <?php foreach($dest as $d): ?>
                <article class="card">
                    <div style="position: relative;">
                        <img src="<?= get_image_url($d['image']) ?>" alt="<?= e($d['title']) ?>" class="card-img" style="width: 100%; height: 240px; object-fit: cover;">
                        <span style="position: absolute; top: 15px; left: 15px;" class="card-tag"><?= e($d['category'] ?? 'Umum') ?></span>
                    </div>
                    <div class="card-body">
                        <h3 style="margin: 0 0 5px; font-size: 1.25rem; font-weight:700;"><?= e($d['title']) ?></h3>
                        <p style="color:#94a3b8; font-size: 0.9rem; margin-bottom: 20px;">📍 <?= e($d['location']) ?></p>
                        <a href="destination_detail.php?id=<?= $d['id'] ?>" style="color:var(--accent); font-weight:700; text-decoration:none; font-size:0.95rem;">Lihat Detail &rarr;</a>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>

        <div class="section-header">
            <div>
                <h2 class="section-title">Paket Terbaru</h2>
                <p class="section-desc">Pilihan paket perjalanan lengkap untuk Anda.</p>
            </div>
            <a href="tours.php" style="color:var(--text-main); font-weight:600; text-decoration:none;">Lihat Semua &rarr;</a>
        </div>

        <?php if (count($tours) == 0): ?><div style="text-align:center; padding:60px;"><p class="muted">Belum ada paket tour.</p></div><?php endif; ?>

        <div class="grid">
            <?php foreach($tours as $t): ?>
                <?php 
                    $final_image_tour = !empty($t['dest_image']) ? $t['dest_image'] : $t['image']; 
                ?>
                <article class="card">
                    <div style="position:relative;">
                        <img src="<?= get_image_url($final_image_tour) ?>" class="card-img" style="width:100%; height:220px; object-fit:cover;">
                        <span style="position:absolute; bottom:15px; right:15px; background:rgba(255,255,255,0.95); color:var(--accent); padding:6px 14px; border-radius:50px; font-size:0.85rem; font-weight:800; box-shadow:0 4px 10px rgba(0,0,0,0.1);">
                            IDR <?= number_format($t['price'] / 1000, 0) ?>K
                        </span>
                    </div>
                    <div class="card-body">
                        <small style="text-transform:uppercase; font-size:0.75rem; font-weight:700; color:var(--secondary); letter-spacing:0.5px;">
                            <?= e($t['dest_title'] ?? 'OPEN TRIP') ?>
                        </small>
                        
                        <h3 style="margin:5px 0 10px; font-size: 1.2rem; font-weight:700; line-height:1.4;">
                            <?= e($t['title']) ?>
                        </h3>
                        
                        <p style="color:#64748b; font-size:0.9rem; line-height:1.6; margin-bottom:20px;">
                            <?= substr(e($t['description']), 0, 80) ?>...
                        </p>
                        
                        <a href="tour_detail.php?id=<?= $t['id'] ?>" class="btn" style="width:100%; text-align:center; border-radius:10px;">
                            Pesan Sekarang
                        </a>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
        <br><br>
    </main>
    
    <footer>
        <h3 class="brand" style="font-size:1.5rem; margin-bottom:10px;">Pariwisata.</h3>
        <p style="color:#94a3b8;">&copy; <?= date('Y') ?> Pariwisata Inc. All rights reserved.</p>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const slides = document.querySelectorAll('.hero-slide');
            if (slides.length === 0) return;
            let currentIndex = 0;
            setInterval(() => {
                slides[currentIndex].classList.remove('active');
                currentIndex = (currentIndex + 1) % slides.length;
                slides[currentIndex].classList.add('active');
            }, 5000);
        });
    </script>
</body>
</html>