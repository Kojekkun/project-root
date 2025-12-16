<?php
// public/admin/tour_add.php
require_once __DIR__ . '/../../app/helpers.php';
require_once __DIR__ . '/../../app/db.php';

require_login();

// Cek Admin
if (($_SESSION['user_role'] ?? '') !== 'admin') {
    header('Location: ../index.php');
    exit;
}

// Ambil data destinasi untuk dropdown
$stmt = $pdo->query("SELECT id, title FROM destinations ORDER BY title ASC");
$destinations = $stmt->fetchAll();
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Tambah Tour Baru</title>
    <link rel="stylesheet" href="../assets/css/style.css?v=6">
    <style>
        .image-option-btn {
            background: #f1f5f9; border: 1px solid #cbd5e1; padding: 8px 15px; 
            border-radius: 6px; cursor: pointer; font-size: 0.9rem; font-weight: 600;
        }
        .image-option-btn.active {
            background: #2563eb; color: white; border-color: #2563eb;
        }
    </style>
</head>
<body>
    <nav class="nav">
        <a class="brand" href="index.php">Admin Panel</a>
        <div class="nav-right">
            <a href="index.php" style="text-decoration:none; color:#555;">← Batal & Kembali</a>
        </div>
    </nav>

    <main class="container">
        <div class="card" style="max-width: 700px; margin: 0 auto;">
            <h2 style="border-bottom: 1px solid #f1f5f9; padding-bottom: 15px; margin-bottom: 20px;">+ Tambah Paket Tour Baru</h2>
            
            <?php if($m=flash_get('error')): ?>
                <div class="alert alert-danger"><?= e($m) ?></div>
            <?php endif; ?>

            <form action="process_tour_add.php" method="post" enctype="multipart/form-data">
                <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
                
                <label>Nama Paket Tour</label>
                <input name="title" required placeholder="Contoh: Open Trip Bromo Midnight">
                
                <label>Hubungkan dengan Destinasi (Opsional)</label>
                <select name="destination_id">
                    <option value="">-- Tidak Terhubung --</option>
                    <?php foreach($destinations as $d): ?>
                        <option value="<?= $d['id'] ?>"><?= e($d['title']) ?></option>
                    <?php endforeach; ?>
                </select>
                <p class="muted" style="font-size:0.8rem; margin-top:-10px; margin-bottom: 20px;">Jika dipilih, paket ini akan muncul di halaman detail destinasi tersebut.</p>
                
                <div style="display:flex; gap:15px;">
                    <div style="flex:1;">
                        <label>Harga (Rp)</label>
                        <input type="number" name="price" required placeholder="350000">
                    </div>
                    <div style="flex:1;">
                        <label>Nomor Kontak (WhatsApp)</label>
                        <input type="number" name="contact" required placeholder="62812345678">
                    </div>
                </div>

                <label>Deskripsi Singkat</label>
                <textarea name="description" rows="3" required placeholder="Penjelasan singkat tentang paket ini..."></textarea>

                <label>Jadwal Perjalanan (Itinerary)</label>
                <textarea name="itinerary" rows="5" placeholder="Hari 1: Penjemputan... Hari 2: Wisata..."></textarea>
                
                <label style="margin-bottom: 10px;">Foto Utama</label>
                <div style="margin-bottom: 15px; display:flex; gap: 10px;">
                    <button type="button" class="image-option-btn active" onclick="toggleImageSource('upload')">📂 Upload File</button>
                    <button type="button" class="image-option-btn" onclick="toggleImageSource('link')">🔗 Gunakan Link URL</button>
                </div>

                <div id="input-upload">
                    <input type="file" name="image" id="fileInput" accept="image/*">
                    <p class="muted" style="font-size:0.8rem; margin-top:-10px;">Format: JPG/PNG. Maks 2MB.</p>
                </div>

                <div id="input-link" style="display: none;">
                    <input type="text" name="image_url" id="urlInput" placeholder="https://example.com/gambar-wisata.jpg">
                    <p class="muted" style="font-size:0.8rem; margin-top:-10px;">Pastikan link gambar dapat diakses publik.</p>
                </div>

                <input type="hidden" name="image_source_type" id="sourceType" value="upload">
                
                <button class="btn" style="width:100%; margin-top:20px; background-color: #f59e0b;">Simpan Paket Tour</button>
            </form>
        </div>
    </main>

    <script>
        function toggleImageSource(type) {
            const uploadDiv = document.getElementById('input-upload');
            const linkDiv = document.getElementById('input-link');
            const fileInput = document.getElementById('fileInput');
            const urlInput = document.getElementById('urlInput');
            const hiddenType = document.getElementById('sourceType');
            const btns = document.querySelectorAll('.image-option-btn');

            if (type === 'upload') {
                uploadDiv.style.display = 'block';
                linkDiv.style.display = 'none';
                hiddenType.value = 'upload';
                fileInput.required = true;
                urlInput.required = false;
                urlInput.value = ''; // Reset nilai URL
                
                btns[0].classList.add('active');
                btns[1].classList.remove('active');
            } else {
                uploadDiv.style.display = 'none';
                linkDiv.style.display = 'block';
                hiddenType.value = 'link';
                fileInput.required = false;
                fileInput.value = ''; // Reset input file
                urlInput.required = true;

                btns[0].classList.remove('active');
                btns[1].classList.add('active');
            }
        }
    </script>
</body>
</html>