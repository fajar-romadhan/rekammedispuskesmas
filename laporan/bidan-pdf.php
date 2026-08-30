<?php

session_start();

require "../template/rbac.php";

// Hanya Bidan
cekAkses([ROLE_BIDAN]);

require "../config.php";
require "../fpdf184/fpdf.php";


// ======================================================
// AMBIL DATA REKAM MEDIS
// ======================================================

$query = mysqli_query($koneksi, "
    SELECT 
        tbl_rekammedis.*,
        tbl_pasien.nama,
        tbl_pasien.alamat,
        tbl_pasien.tgl_lahir,
        tbl_pasien.gender
    FROM tbl_rekammedis
    INNER JOIN tbl_pasien
        ON tbl_rekammedis.id_pasien = tbl_pasien.id_pasien
    ORDER BY tbl_rekammedis.tgl_rm DESC
");


// ======================================================
// PDF
// ======================================================

$pdf = new FPDF('L', 'mm', 'A4');

$pdf->AddPage();


// HEADER
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(0, 8, 'PUSKESMAS MENDIS', 0, 1, 'C');

$pdf->SetFont('Arial', '', 12);
$pdf->Cell(0, 7, 'PELAYANAN KESEHATAN MASYARAKAT', 0, 1, 'C');

$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(0, 8, 'LAPORAN REKAM MEDIS KEBIDANAN', 0, 1, 'C');

$pdf->Ln(5);


// GARIS
$pdf->SetDrawColor(0, 0, 0);
$pdf->Line(10, 40, 287, 40);

$pdf->Ln(5);


// ======================================================
// TABEL
// ======================================================

$pdf->SetFont('Arial', 'B', 9);

$pdf->Cell(10, 8, 'No', 1, 0, 'C');
$pdf->Cell(30, 8, 'No RM', 1, 0, 'C');
$pdf->Cell(45, 8, 'Nama Pasien', 1, 0, 'C');
$pdf->Cell(28, 8, 'Tanggal', 1, 0, 'C');
$pdf->Cell(60, 8, 'Keluhan', 1, 0, 'C');
$pdf->Cell(50, 8, 'Diagnosa', 1, 0, 'C');
$pdf->Cell(50, 8, 'Obat', 1, 1, 'C');


$pdf->SetFont('Arial', '', 8);

$no = 1;

while ($data = mysqli_fetch_assoc($query)) {

    $pdf->Cell(
        10,
        8,
        $no++,
        1,
        0,
        'C'
    );

    $pdf->Cell(
        30,
        8,
        $data['no_rm'],
        1,
        0,
        'C'
    );

    $pdf->Cell(
        45,
        8,
        $data['nama'],
        1,
        0,
        'L'
    );

    $pdf->Cell(
        28,
        8,
        date('d-m-Y', strtotime($data['tgl_rm'])),
        1,
        0,
        'C'
    );

    $pdf->Cell(
        60,
        8,
        $data['keluhan'],
        1,
        0,
        'L'
    );

    $pdf->Cell(
        50,
        8,
        $data['diagnosa'],
        1,
        0,
        'L'
    );

    $pdf->Cell(
        50,
        8,
        $data['obat'],
        1,
        1,
        'L'
    );
}


// ======================================================
// TANDA TANGAN
// ======================================================

$pdf->Ln(10);

$pdf->SetFont('Arial', '', 10);

$pdf->Cell(
    0,
    6,
    'Mendis, ' . date('d-m-Y'),
    0,
    1,
    'R'
);

$pdf->Ln(15);

$pdf->SetFont('Arial', 'B', 10);

$pdf->Cell(
    0,
    6,
    'Bidan',
    0,
    1,
    'R'
);


// OUTPUT

$pdf->Output(
    'I',
    'laporan-kebidanan.pdf'
);

?>