<?php 
require_once __DIR__.'/../app/db.php'; 
require_once __DIR__.'/../app/helpers.php';

$stmt=$pdo->query('SELECT d.*, c.name as category FROM destinations d LEFT JOIN categories c ON d.category_id=c.id ORDER BY d.title'); 
$list=$stmt->fetchAll();
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Semua Destinasi</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

    <nav class="nav">
        <a class="brand" href="index.php">Pariwisata</a>
        <div class="nav-right">
            <?php if(is_logged()): ?>
        
            <?php 
                $nav_foto = 'assets/images/placeholder.jpg'; 
                if (!empty($_SESSION['user_avatar']) && file_exists(__DIR__ . '/uploads/avatars/' . $_SESSION['user_avatar'])) {
                    $nav_foto = 'uploads/avatars/' . $_SESSION['user_avatar'];
                }
            ?>

            <a href="profile.php" class="nav-profile">
                <img src="<?= $nav_foto ?>" class="nav-avatar" alt="Profil">
                <span class="nav-name"><?= e($_SESSION['user_name']) ?></span>
            </a>
            
            <span style="color:#ccc; margin: 0 5px;">|</span>
            
            <a href="logout.php" style="color:#ef4444; text-decoration:none; font-size:0.9rem;">Logout</a>

            <?php else: ?>
                <a href="login.php">Login</a> | 
                <a href="register.php">Daftar</a>
            <?php endif; ?>
            
        </div>
    </nav>

    <main class="container">
        <h1>Semua Destinasi</h1>
        
        <?php if(!empty($_SESSION['user_role']) && $_SESSION['user_role']==='admin'): ?>
            <a class="btn" href="admin/destination_add.php" style="margin-bottom: 20px; display:inline-block;">+ Tambah Destinasi</a>
        <?php endif; ?>

        <div class="grid">
            <?php foreach($list as $d): ?>
            <article class="card">
                <img src="uploads/<?= e($d['image'] ?? 'placeholder.jpg') ?>" style="width:100%; height:200px; object-fit:cover;">
                
                <h3><?= e($d['title']) ?></h3>
                <p class="muted"><?= e($d['category']) ?></p>
                
                <a class="btn" href="destination_detail.php?id=<?= $d['id'] ?>">Detail</a>
            </article>
            <?php endforeach; ?>
        </div>
    </main>
</body>
</html>