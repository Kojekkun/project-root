<?php
// public/export_pdf.php
require_once __DIR__ . '/../app/db.php';
require_once __DIR__ . '/../app/helpers.php';
require_once __DIR__ . '/../app/fpdf/fpdf.php'; // Panggil Library

require_login();

// 1. TENTUKAN DATA SIAPA YANG DIAMBIL
$user_id = $_SESSION['user_id'];
$role = $_SESSION['user_role'] ?? 'user';

if ($role === 'admin') {
    // Admin: Ambil SEMUA transaksi + Nama Usernya
    $sql = "SELECT t.*, u.name as user_name 
            FROM transactions t 
            JOIN users u ON t.user_id = u.id 
            ORDER BY t.created_at DESC";
    $stmt = $pdo->query($sql);
    $transactions = $stmt->fetchAll();
    $report_title = "LAPORAN KEUANGAN (ADMIN)";
} else {
    // User: Ambil transaksi DIA SAJA
    $sql = "SELECT t.*, u.name as user_name 
            FROM transactions t 
            JOIN users u ON t.user_id = u.id 
            WHERE t.user_id = ? 
            ORDER BY t.created_at DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$user_id]);
    $transactions = $stmt->fetchAll();
    $report_title = "RIWAYAT TRANSAKSI: " . strtoupper($_SESSION['user_name']);
}

// 2. MULAI MEMBUAT PDF
class PDF extends FPDF {
    // Header Halaman
    function Header() {
        global $report_title;
        $this->SetFont('Arial','B',14);
        $this->Cell(0,10,$report_title,0,1,'C');
        $this->SetFont('Arial','I',10);
        $this->Cell(0,10,'Dicetak pada: ' . date('d-m-Y H:i'),0,1,'C');
        $this->Ln(10);
        
        // Header Tabel
        $this->SetFont('Arial','B',10);
        $this->SetFillColor(200,220,255); // Warna Biru Muda
        
        // Lebar Kolom: Total 190 (A4 Margin)
        $this->Cell(10, 10, 'No', 1, 0, 'C', true);
        $this->Cell(35, 10, 'Tanggal', 1, 0, 'C', true);
        $this->Cell(40, 10, 'User', 1, 0, 'C', true); // Kolom Nama User
        $this->Cell(20, 10, 'Tipe', 1, 0, 'C', true);
        $this->Cell(50, 10, 'Keterangan', 1, 0, 'C', true);
        $this->Cell(35, 10, 'Nominal (Rp)', 1, 1, 'C', true); // Parameter akhir 1 = Ganti baris
    }

    // Footer Halaman
    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial','I',8);
        $this->Cell(0,10,'Halaman '.$this->PageNo().'/{nb}',0,0,'C');
    }
}

// Inisialisasi PDF
$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Arial','',9);

$no = 1;
foreach($transactions as $t) {
    // Tentukan Warna Baris (Zebra Striping)
    $fill = ($no % 2 == 0); // Selang seling warna jika mau (tapi di sini kita default putih)
    
    // Format Data
    $date = date('d/m/Y', strtotime($t['created_at']));
    $type = ($t['type'] == 'credit') ? 'Masuk' : 'Keluar';
    $amount = number_format($t['amount'], 0, ',', '.');
    $desc = substr($t['description'], 0, 25); // Potong jika kepanjangan
    $user = substr($t['user_name'], 0, 18);

    // Cetak Cell
    $pdf->Cell(10, 8, $no++, 1, 0, 'C');
    $pdf->Cell(35, 8, $date, 1, 0, 'C');
    $pdf->Cell(40, 8, $user, 1, 0, 'L');
    $pdf->Cell(20, 8, $type, 1, 0, 'C');
    $pdf->Cell(50, 8, $desc, 1, 0, 'L');
    $pdf->Cell(35, 8, $amount, 1, 1, 'R');
}

// Output File (D = Download)
$pdf->Output('D', 'Laporan_Transaksi.pdf');
?>