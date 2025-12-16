<?php
// app/controllers/tour_create_handler.php
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../helpers.php';
// require_once __DIR__ . '/../config.php'; // Opsional jika sudah ada di helpers

// 1. Validasi Keamanan (Admin & Login)
// Karena file ini di-include oleh public/admin/process_tour_add.php, session sudah start
if (empty($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'admin') {
    // Redirect ke login jika akses langsung
    header('Location: ../../public/login.php'); 
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 2. Cek Token CSRF
    $csrf = $_POST['csrf'] ?? '';
    if (!csrf_check($csrf)) { // Pastikan fungsi csrf_check ada di helpers.php Anda
        flash_set('error', 'Sesi tidak valid (CSRF Error). Silakan coba lagi.');
        header('Location: ../../public/admin/tour_add.php');
        exit;
    }

    // 3. Ambil Data Form
    $title       = trim($_POST['title']);
    $price       = $_POST['price'];
    $contact     = $_POST['contact'];
    $description = trim($_POST['description']);
    $itinerary   = trim($_POST['itinerary']);
    $dest_id     = !empty($_POST['destination_id']) ? $_POST['destination_id'] : null;
    
    // Ambil Tipe Sumber Gambar (Upload atau Link)
    $source_type = $_POST['image_source_type'] ?? 'upload';

    // Validasi Data Wajib
    if (empty($title) || empty($price) || empty($contact)) {
        flash_set('error', 'Judul, Harga, dan Kontak wajib diisi.');
        header('Location: ../../public/admin/tour_add.php');
        exit;
    }

    // 4. Logika Pemrosesan Gambar (Dual Mode)
    $final_image_name = null;

    if ($source_type === 'link') {
        // --- MODE A: LINK URL ---
        $url = trim($_POST['image_url'] ?? '');
        if (empty($url)) {
            flash_set('error', 'Link URL gambar tidak boleh kosong.');
            header('Location: ../../public/admin/tour_add.php');
            exit;
        }
        $final_image_name = $url; // Simpan URL mentah

    } else {
        // --- MODE B: UPLOAD FILE ---
        
        // Cek apakah user benar-benar mengupload file
        if (empty($_FILES['image']['name'])) {
            flash_set('error', 'Anda memilih mode Upload, tetapi belum memilih file gambar.');
            header('Location: ../../public/admin/tour_add.php');
            exit;
        }

        $file = $_FILES['image'];
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $max_size = 5 * 1024 * 1024; // 5MB

        // Validasi Tipe
        if (!in_array($file['type'], $allowed_types)) {
            flash_set('error', 'Format file tidak didukung. Gunakan JPG/PNG/WEBP.');
            header('Location: ../../public/admin/tour_add.php');
            exit;
        }

        // Validasi Ukuran
        if ($file['size'] > $max_size) {
            flash_set('error', 'Ukuran file terlalu besar (Maks 5MB).');
            header('Location: ../../public/admin/tour_add.php');
            exit;
        }

        // Tentukan Lokasi Simpan
        // Karena script berjalan di public/admin/, kita harus mundur ke uploads
        $upload_dir = __DIR__ . '/../../public/uploads/';
        
        // Generate Nama Unik
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $new_filename = 'tour_' . time() . '_' . uniqid() . '.' . $ext;
        $destination = $upload_dir . $new_filename;

        // Pindahkan File
        if (!move_uploaded_file($file['tmp_name'], $destination)) {
            flash_set('error', 'Gagal memindahkan file upload ke folder uploads.');
            header('Location: ../../public/admin/tour_add.php');
            exit;
        }

        $final_image_name = $new_filename;
    }

    // 5. Simpan ke Database
    try {
        // Hapus 'map_embed' dari query jika kolom tersebut tidak ada di tabel database Anda
        $sql = "INSERT INTO tours (title, destination_id, description, itinerary, price, contact, image, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $title, 
            $dest_id, 
            $description, 
            $itinerary, 
            $price, 
            $contact, 
            $final_image_name
        ]);

        flash_set('success', '✅ Paket tour berhasil ditambahkan!');
        
        // Redirect kembali ke Dashboard Admin
        header('Location: ../../public/admin/index.php');
        exit;

    } catch (PDOException $e) {
        error_log($e->getMessage());
        flash_set('error', 'Gagal menyimpan ke database: ' . $e->getMessage());
        header('Location: ../../public/admin/tour_add.php');
        exit;
    }
} else {
    // Jika diakses via GET
    header('Location: ../../public/admin/tour_add.php');
    exit;
}