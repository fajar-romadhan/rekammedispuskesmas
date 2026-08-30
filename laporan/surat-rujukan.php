<?php

session_start();

require "../template/rbac.php";

cekAkses([ROLE_PETUGAS, ROLE_DOKTER, ROLE_BIDAN]);

require "../config.php";
require "../asset/fpdf.php";

// ==========================================
// CEK ID REKAM MEDIS
// ==========================================

if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Data rekam medis tidak ditemukan.");
}

$id = mysqli_real_escape_string($koneksi, $_GET['id']);


// ==========================================
// AMBIL DATA REKAM MEDIS
// ==========================================

$query = mysqli_query($koneksi, "
    SELECT 
        r.*,
        p.nama,
        p.no_rm,
        p.tgl_lahir,
        p.gender,
        p.alamat,
        u.fullname AS nama_dokter
    FROM tbl_rekammedis r
    INNER JOIN tbl_pasien p 
        ON r.id_pasien = p.id
    LEFT JOIN tbl_user u
        ON r.id_dokter = u.userid
    WHERE r.id_rm = '$id'
");

if (!$query || mysqli_num_rows($query) == 0) {
    die("Data rekam medis tidak ditemukan.");
}

$data = mysqli_fetch_assoc($query);


// ==========================================
// TANGGAL LAHIR
// ==========================================

$umur = '';

if (!empty($data['tgl_lahir'])) {

    $lahir = new DateTime($data['tgl_lahir']);
    $sekarang = new DateTime();

    $umur = $lahir->diff($sekarang)->y . " tahun";
}


// ==========================================
// JENIS KELAMIN
// ==========================================

$gender = $data['gender'];

if ($gender == 'P') {
    $gender = 'Pria';
} else {
    $gender = 'Wanita';
}


// ==========================================
// TANGGAL RUJUKAN
// ==========================================

$tanggalRujukan = date(
    'd-m-Y',
    strtotime($data['tgl_rm'])
);


// ==========================================
// TUJUAN RUJUKAN
// ==========================================

$tujuanRujukan = '';

if (!empty($data['rujuk_eksternal'])) {

    $tujuanRujukan = $data['rujuk_eksternal'];

} elseif (!empty($data['rujuk_internal'])) {

    $tujuanRujukan = $data['rujuk_internal'];

} else {

    $tujuanRujukan = '-';

}


// ==========================================
// PDF
// ==========================================

$pdf = new FPDF('P', 'mm', 'A4');

$pdf->AddPage();


// ==========================================
// KOP SURAT
// ==========================================

$pdf->SetFont('Arial', 'B', 14);

$pdf->Cell(
    0,
    7,
    'PUSKESMAS MENDIS',
    0,
    1,
    'C'
);

$pdf->SetFont('Arial', '', 10);

$pdf->Cell(
    0,
    5,
    'PELAYANAN KESEHATAN MASYARAKAT',
    0,
    1,
    'C'
);

$pdf->Cell(
    0,
    5,
    'SURAT RUJUKAN',
    0,
    1,
    'C'
);

$pdf->Ln(3);

$pdf->Line(
    15,
    $pdf->GetY(),
    195,
    $pdf->GetY()
);

$pdf->Ln(7);


// ==========================================
// JUDUL
// ==========================================

$pdf->SetFont('Arial', 'B', 12);

$pdf->Cell(
    0,
    7,
    'SURAT RUJUKAN PASIEN',
    0,
    1,
    'C'
);

$pdf->Ln(5);


// ==========================================
// DATA PASIEN
// ==========================================

$pdf->SetFont('Arial', '', 10);

$pdf->Cell(40, 7, 'Nama Pasien', 0, 0);
$pdf->Cell(5, 7, ':', 0, 0);
$pdf->Cell(135, 7, $data['nama'], 0, 1);

$pdf->Cell(40, 7, 'No. Rekam Medis', 0, 0);
$pdf->Cell(5, 7, ':', 0, 0);
$pdf->Cell(135, 7, $data['no_rm'], 0, 1);

$pdf->Cell(40, 7, 'Tanggal Lahir', 0, 0);
$pdf->Cell(5, 7, ':', 0, 0);
$pdf->Cell(
    135,
    7,
    date('d-m-Y', strtotime($data['tgl_lahir'])) .
    ' (' . $umur . ')',
    0,
    1
);

$pdf->Cell(40, 7, 'Jenis Kelamin', 0, 0);
$pdf->Cell(5, 7, ':', 0, 0);
$pdf->Cell(135, 7, $gender, 0, 1);

$pdf->Cell(40, 7, 'Alamat', 0, 0);
$pdf->Cell(5, 7, ':', 0, 0);
$pdf->MultiCell(135, 7, $data['alamat'], 0, 'L');

$pdf->Ln(5);


// ==========================================
// INFORMASI RUJUKAN
// ==========================================

$pdf->SetFont('Arial', 'B', 10);

$pdf->Cell(
    0,
    7,
    'INFORMASI RUJUKAN',
    0,
    1
);

$pdf->SetFont('Arial', '', 10);

$pdf->Cell(40, 7, 'Tanggal Rujukan', 0, 0);
$pdf->Cell(5, 7, ':', 0, 0);
$pdf->Cell(135, 7, $tanggalRujukan, 0, 1);

$pdf->Cell(40, 7, 'Tujuan Rujukan', 0, 0);
$pdf->Cell(5, 7, ':', 0, 0);
$pdf->MultiCell(135, 7, $tujuanRujukan, 0, 'L');

$pdf->Ln(3);


// ==========================================
// DIAGNOSA
// ==========================================

$pdf->SetFont('Arial', 'B', 10);

$pdf->Cell(
    0,
    7,
    'Diagnosa',
    0,
    1
);

$pdf->SetFont('Arial', '', 10);

$pdf->MultiCell(
    0,
    7,
    !empty($data['diagnosa'])
        ? $data['diagnosa']
        : '-',
    1,
    'L'
);

$pdf->Ln(3);


// ==========================================
// KELUHAN
// ==========================================

$pdf->SetFont('Arial', 'B', 10);

$pdf->Cell(
    0,
    7,
    'Keluhan',
    0,
    1
);

$pdf->SetFont('Arial', '', 10);

$pdf->MultiCell(
    0,
    7,
    !empty($data['keluhan'])
        ? $data['keluhan']
        : '-',
    1,
    'L'
);

$pdf->Ln(3);


// ==========================================
// PEMERIKSAAN
// ==========================================

$pdf->SetFont('Arial', 'B', 10);

$pdf->Cell(
    0,
    7,
    'Hasil Pemeriksaan',
    0,
    1
);

$pdf->SetFont('Arial', '', 10);

$pdf->MultiCell(
    0,
    7,
    !empty($data['pemeriksaan_fisik'])
        ? $data['pemeriksaan_fisik']
        : '-',
    1,
    'L'
);

$pdf->Ln(3);


// ==========================================
// TINDAKAN
// ==========================================

$pdf->SetFont('Arial', 'B', 10);

$pdf->Cell(
    0,
    7,
    'Tindakan',
    0,
    1
);

$pdf->SetFont('Arial', '', 10);

$pdf->MultiCell(
    0,
    7,
    !empty($data['tindakan'])
        ? $data['tindakan']
        : '-',
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

$pdf->Cell(
    70,
    7,
    'Dokter Pemeriksa,',
    0,
    1,
    'C'
);

$pdf->Ln(18);

$pdf->Cell(110, 7, '', 0, 0);

$pdf->SetFont('Arial', 'B', 10);

$pdf->Cell(
    70,
    7,
    $data['nama_dokter'],
    0,
    1,
    'C'
);

$pdf->SetFont('Arial', '', 10);

$pdf->Cell(110, 7, '', 0, 0);

$pdf->Cell(
    70,
    7,
    'Dokter',
    0,
    1,
    'C'
);


// ==========================================
// OUTPUT
// ==========================================

$pdf->Output(
    'I',
    'Surat-Rujukan-' . $data['no_rm'] . '.pdf'
);

?>