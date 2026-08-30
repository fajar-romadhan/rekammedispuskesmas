<?php

session_start();

require "../template/rbac.php";

cekAkses([ROLE_PETUGAS, ROLE_DOKTER, ROLE_BIDAN]);

require "../config.php";
require "../asset/fpdf.php";

// ==========================================
// CEK ID PEMERIKSAAN
// ==========================================

if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Data pemeriksaan ibu hamil tidak ditemukan.");
}

$id = mysqli_real_escape_string($koneksi, $_GET['id']);


// ==========================================
// AMBIL DATA PEMERIKSAAN + IBU HAMIL
// ==========================================

$query = mysqli_query($koneksi, "
    SELECT
        pi.*,
        h.nama_ibu,
        h.nik,
        h.tgl_lahir,
        h.alamat,
        h.nama_suami
    FROM tbl_pemeriksaan_ibu_hamil pi
    INNER JOIN tbl_ibu_hamil h
        ON pi.ibu_hamil_id = h.id
    WHERE pi.id = '$id'
");

if (!$query || mysqli_num_rows($query) == 0) {
    die("Data pemeriksaan ibu hamil tidak ditemukan.");
}

$data = mysqli_fetch_assoc($query);


// ==========================================
// PETUGAS/BIDAN YANG SEDANG MEMBUAT SURAT
// (tabel pemeriksaan ibu hamil tidak menyimpan
// siapa bidan yang memeriksa, jadi dipakai
// yang sedang login membuat surat ini)
// ==========================================

$userLogin = $_SESSION['ssUserRM'];

$queryUser = mysqli_query($koneksi, "
    SELECT fullname FROM tbl_user WHERE username = '" .
    mysqli_real_escape_string($koneksi, $userLogin) . "'
");

$namaPembuat = ($u = mysqli_fetch_assoc($queryUser)) ? $u['fullname'] : $userLogin;


// ==========================================
// TANGGAL LAHIR / UMUR
// ==========================================

$umur = '';

if (!empty($data['tgl_lahir'])) {

    $lahir = new DateTime($data['tgl_lahir']);
    $sekarang = new DateTime();

    $umur = $lahir->diff($sekarang)->y . " tahun";
}


// ==========================================
// TANGGAL SURAT
// ==========================================

$tanggalRujukan = date(
    'd-m-Y',
    strtotime($data['tanggal_pemeriksaan'])
);


// ==========================================
// PDF
// ==========================================

$pdf = new FPDF('P', 'mm', 'A4');

$pdf->AddPage();


// ==========================================
// KOP SURAT
// ==========================================

$pdf->SetFont('Arial', 'B', 14);

$pdf->Cell(0, 7, 'PUSKESMAS MENDIS', 0, 1, 'C');

$pdf->SetFont('Arial', '', 10);

$pdf->Cell(0, 5, 'PELAYANAN KESEHATAN MASYARAKAT', 0, 1, 'C');

$pdf->Cell(0, 5, 'SURAT RUJUKAN - PEMERIKSAAN IBU HAMIL', 0, 1, 'C');

$pdf->Ln(3);

$pdf->Line(15, $pdf->GetY(), 195, $pdf->GetY());

$pdf->Ln(7);


// ==========================================
// JUDUL
// ==========================================

$pdf->SetFont('Arial', 'B', 12);

$pdf->Cell(0, 7, 'SURAT RUJUKAN IBU HAMIL', 0, 1, 'C');

$pdf->Ln(5);


// ==========================================
// DATA IBU HAMIL
// ==========================================

$pdf->SetFont('Arial', '', 10);

$pdf->Cell(40, 7, 'Nama Ibu', 0, 0);
$pdf->Cell(5, 7, ':', 0, 0);
$pdf->Cell(135, 7, $data['nama_ibu'], 0, 1);

$pdf->Cell(40, 7, 'NIK', 0, 0);
$pdf->Cell(5, 7, ':', 0, 0);
$pdf->Cell(135, 7, $data['nik'] ?: '-', 0, 1);

$pdf->Cell(40, 7, 'Tanggal Lahir', 0, 0);
$pdf->Cell(5, 7, ':', 0, 0);
$pdf->Cell(
    135,
    7,
    !empty($data['tgl_lahir'])
        ? date('d-m-Y', strtotime($data['tgl_lahir'])) . ' (' . $umur . ')'
        : '-',
    0,
    1
);

$pdf->Cell(40, 7, 'Nama Suami', 0, 0);
$pdf->Cell(5, 7, ':', 0, 0);
$pdf->Cell(135, 7, $data['nama_suami'] ?: '-', 0, 1);

$pdf->Cell(40, 7, 'Alamat', 0, 0);
$pdf->Cell(5, 7, ':', 0, 0);
$pdf->MultiCell(135, 7, $data['alamat'], 0, 'L');

$pdf->Ln(5);


// ==========================================
// INFORMASI RUJUKAN
// ==========================================

$pdf->SetFont('Arial', 'B', 10);

$pdf->Cell(0, 7, 'INFORMASI RUJUKAN', 0, 1);

$pdf->SetFont('Arial', '', 10);

$pdf->Cell(40, 7, 'Tanggal Rujukan', 0, 0);
$pdf->Cell(5, 7, ':', 0, 0);
$pdf->Cell(135, 7, $tanggalRujukan, 0, 1);

$pdf->Ln(3);


// ==========================================
// DATA PEMERIKSAAN KEHAMILAN
// ==========================================

$pdf->SetFont('Arial', 'B', 10);

$pdf->Cell(0, 7, 'Data Pemeriksaan Kehamilan', 0, 1);

$pdf->SetFont('Arial', '', 10);

$pdf->Cell(40, 7, 'Usia Kehamilan', 0, 0);
$pdf->Cell(5, 7, ':', 0, 0);
$pdf->Cell(135, 7, $data['usia_kehamilan'] . ' minggu', 0, 1);

$pdf->Cell(40, 7, 'Berat Badan', 0, 0);
$pdf->Cell(5, 7, ':', 0, 0);
$pdf->Cell(135, 7, $data['berat_badan'] . ' kg', 0, 1);

$pdf->Cell(40, 7, 'Tekanan Darah', 0, 0);
$pdf->Cell(5, 7, ':', 0, 0);
$pdf->Cell(135, 7, $data['tekanan_darah'] ?: '-', 0, 1);

$pdf->Cell(40, 7, 'TFU', 0, 0);
$pdf->Cell(5, 7, ':', 0, 0);
$pdf->Cell(135, 7, $data['tfu'] . ' cm', 0, 1);

$pdf->Cell(40, 7, 'DJJ', 0, 0);
$pdf->Cell(5, 7, ':', 0, 0);
$pdf->Cell(135, 7, $data['djj'] . ' x/menit', 0, 1);

$pdf->Ln(3);


// ==========================================
// HASIL PEMERIKSAAN
// ==========================================

$pdf->SetFont('Arial', 'B', 10);

$pdf->Cell(0, 7, 'Hasil Pemeriksaan', 0, 1);

$pdf->SetFont('Arial', '', 10);

$pdf->MultiCell(
    0,
    7,
    !empty($data['hasil']) ? $data['hasil'] : '-',
    1,
    'L'
);

$pdf->Ln(3);


// ==========================================
// KETERANGAN
// ==========================================

$pdf->SetFont('Arial', 'B', 10);

$pdf->Cell(0, 7, 'Keterangan', 0, 1);

$pdf->SetFont('Arial', '', 10);

$pdf->MultiCell(
    0,
    7,
    !empty($data['keterangan']) ? $data['keterangan'] : '-',
    1,
    'L'
);

$pdf->Ln(8);


// ==========================================
// PENUTUP
// ==========================================

$pdf->MultiCell(
    0,
    7,
    'Dengan ini pasien tersebut dirujuk untuk mendapatkan pemeriksaan dan pelayanan kesehatan lebih lanjut.',
    0,
    'L'
);

$pdf->Ln(15);


// ==========================================
// TANDA TANGAN
// ==========================================

$pdf->Cell(110, 7, '', 0, 0);
$pdf->Cell(70, 7, 'Petugas Pemeriksa,', 0, 1, 'C');

$pdf->Ln(18);

$pdf->Cell(110, 7, '', 0, 0);
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(70, 7, $namaPembuat, 0, 1, 'C');

$pdf->SetFont('Arial', '', 10);
$pdf->Cell(110, 7, '', 0, 0);
$pdf->Cell(70, 7, 'Bidan', 0, 1, 'C');


// ==========================================
// OUTPUT
// ==========================================

$pdf->Output(
    'I',
    'Surat-Rujukan-Ibu-Hamil-' . preg_replace('/[^A-Za-z0-9\-]/', '-', $data['nama_ibu']) . '.pdf'
);

?>
