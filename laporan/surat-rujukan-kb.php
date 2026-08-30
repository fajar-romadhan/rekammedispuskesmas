<?php

session_start();

require "../template/rbac.php";

cekAkses([ROLE_PETUGAS, ROLE_DOKTER, ROLE_BIDAN]);

require "../config.php";
require "../asset/fpdf.php";

// ==========================================
// CEK ID PELAYANAN KB
// ==========================================

if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Data pelayanan KB tidak ditemukan.");
}

$id = mysqli_real_escape_string($koneksi, $_GET['id']);


// ==========================================
// AMBIL DATA PELAYANAN + PESERTA KB
// ==========================================

$query = mysqli_query($koneksi, "
    SELECT
        pk.*,
        k.no_kk,
        k.no_peserta_kb,
        k.tanggal_lahir,
        k.nama_suami,
        k.alamat AS alamat_kb,
        k.jenis_kb
    FROM tbl_pelayanan_kb pk
    INNER JOIN tbl_kb k
        ON pk.id_kb = k.id_kb
    WHERE pk.id_pelayanan_kb = '$id'
");

if (!$query || mysqli_num_rows($query) == 0) {
    die("Data pelayanan KB tidak ditemukan.");
}

$data = mysqli_fetch_assoc($query);


// ==========================================
// PETUGAS/BIDAN YANG SEDANG MEMBUAT SURAT
// (tabel pelayanan KB tidak menyimpan siapa
// bidan yang melayani, jadi dipakai yang
// sedang login membuat surat ini)
// ==========================================

$userLogin = $_SESSION['ssUserRM'];

$queryUser = mysqli_query($koneksi, "
    SELECT fullname FROM tbl_user WHERE username = '" .
    mysqli_real_escape_string($koneksi, $userLogin) . "'
");

$namaPembuat = ($u = mysqli_fetch_assoc($queryUser)) ? $u['fullname'] : $userLogin;


// ==========================================
// TANGGAL SURAT
// ==========================================

$tanggalRujukan = date(
    'd-m-Y',
    strtotime($data['tanggal_pelayanan'])
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

$pdf->Cell(0, 5, 'SURAT RUJUKAN - PELAYANAN KB', 0, 1, 'C');

$pdf->Ln(3);

$pdf->Line(15, $pdf->GetY(), 195, $pdf->GetY());

$pdf->Ln(7);


// ==========================================
// JUDUL
// ==========================================

$pdf->SetFont('Arial', 'B', 12);

$pdf->Cell(0, 7, 'SURAT RUJUKAN PESERTA KB', 0, 1, 'C');

$pdf->Ln(5);


// ==========================================
// DATA PESERTA KB
// ==========================================

$pdf->SetFont('Arial', '', 10);

$pdf->Cell(40, 7, 'No. Peserta KB', 0, 0);
$pdf->Cell(5, 7, ':', 0, 0);
$pdf->Cell(135, 7, $data['no_peserta_kb'], 0, 1);

$pdf->Cell(40, 7, 'No. KK', 0, 0);
$pdf->Cell(5, 7, ':', 0, 0);
$pdf->Cell(135, 7, $data['no_kk'] ?: '-', 0, 1);

$pdf->Cell(40, 7, 'Tanggal Lahir', 0, 0);
$pdf->Cell(5, 7, ':', 0, 0);
$pdf->Cell(
    135,
    7,
    !empty($data['tanggal_lahir'])
        ? date('d-m-Y', strtotime($data['tanggal_lahir']))
        : '-',
    0,
    1
);

$pdf->Cell(40, 7, 'Nama Suami', 0, 0);
$pdf->Cell(5, 7, ':', 0, 0);
$pdf->Cell(135, 7, $data['nama_suami'] ?: '-', 0, 1);

$pdf->Cell(40, 7, 'Jenis KB', 0, 0);
$pdf->Cell(5, 7, ':', 0, 0);
$pdf->Cell(135, 7, $data['jenis_kb'] ?: '-', 0, 1);

$pdf->Cell(40, 7, 'Alamat', 0, 0);
$pdf->Cell(5, 7, ':', 0, 0);
$pdf->MultiCell(135, 7, $data['alamat_kb'], 0, 'L');

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
// DATA PELAYANAN KB
// ==========================================

$pdf->SetFont('Arial', 'B', 10);

$pdf->Cell(0, 7, 'Data Pelayanan KB', 0, 1);

$pdf->SetFont('Arial', '', 10);

$pdf->Cell(40, 7, 'Metode KB', 0, 0);
$pdf->Cell(5, 7, ':', 0, 0);
$pdf->Cell(135, 7, $data['metode_kb'] ?: '-', 0, 1);

$pdf->Cell(40, 7, 'Tekanan Darah', 0, 0);
$pdf->Cell(5, 7, ':', 0, 0);
$pdf->Cell(135, 7, $data['tekanan_darah'] ?: '-', 0, 1);

$pdf->Cell(40, 7, 'Berat Badan', 0, 0);
$pdf->Cell(5, 7, ':', 0, 0);
$pdf->Cell(135, 7, !empty($data['berat_badan']) ? $data['berat_badan'] . ' kg' : '-', 0, 1);

$pdf->Ln(3);


// ==========================================
// KELUHAN
// ==========================================

$pdf->SetFont('Arial', 'B', 10);

$pdf->Cell(0, 7, 'Keluhan', 0, 1);

$pdf->SetFont('Arial', '', 10);

$pdf->MultiCell(
    0,
    7,
    !empty($data['keluhan']) ? $data['keluhan'] : '-',
    1,
    'L'
);

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
    !empty($data['hasil_pemeriksaan']) ? $data['hasil_pemeriksaan'] : '-',
    1,
    'L'
);

$pdf->Ln(3);


// ==========================================
// EFEK SAMPING
// ==========================================

$pdf->SetFont('Arial', 'B', 10);

$pdf->Cell(0, 7, 'Efek Samping', 0, 1);

$pdf->SetFont('Arial', '', 10);

$pdf->MultiCell(
    0,
    7,
    !empty($data['efek_samping']) ? $data['efek_samping'] : '-',
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
    'Dengan ini peserta tersebut dirujuk untuk mendapatkan pemeriksaan dan pelayanan kesehatan lebih lanjut.',
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
    'Surat-Rujukan-KB-' . preg_replace('/[^A-Za-z0-9\-]/', '-', $data['no_peserta_kb']) . '.pdf'
);

?>
