<?php

session_start();

require "../template/rbac.php";

// Hanya Kepala Puskesmas
cekAkses([ROLE_KEPALA]);

require "../config.php";
require "../asset/fpdf.php";

date_default_timezone_set('Asia/Jakarta');

// ======================================================
// MENENTUKAN PERIODE
// ======================================================

$periode = isset($_GET['periode']) ? $_GET['periode'] : 'mingguan';

$tanggalSekarang = date('Y-m-d');

if ($periode == 'mingguan') {

    // 7 hari terakhir termasuk hari ini
    $tanggalMulai = date('Y-m-d', strtotime('-6 days'));
    $tanggalAkhir = $tanggalSekarang;

    $judulPeriode = 'Periode Mingguan : '
        . date('d-m-Y', strtotime($tanggalMulai))
        . ' s/d '
        . date('d-m-Y', strtotime($tanggalAkhir));

} elseif ($periode == 'bulanan') {

    // Bulan berjalan
    $tanggalMulai = date('Y-m-01');
    $tanggalAkhir = date('Y-m-t');

    $judulPeriode = 'Periode Bulanan : '
        . date('d-m-Y', strtotime($tanggalMulai))
        . ' s/d '
        . date('d-m-Y', strtotime($tanggalAkhir));

} else {

    die('Periode laporan tidak valid.');

}


// ======================================================
// DATA PEMERIKSAAN IBU HAMIL
// ======================================================
//
// Kriteria disamakan dengan halaman laporan/ibu-hamil.php (yang tampil
// di layar), difilter berdasarkan tanggal_pemeriksaan pada periode
// terpilih.
// ======================================================

$tanggalMulaiDb = mysqli_real_escape_string($koneksi, $tanggalMulai);
$tanggalAkhirDb = mysqli_real_escape_string($koneksi, $tanggalAkhir);

$query = mysqli_query($koneksi, "
    SELECT

        p.no_registrasi,
        p.tanggal_pemeriksaan,
        p.usia_kehamilan,
        p.berat_badan,
        p.tekanan_darah,
        p.hasil,

        i.nama_ibu,
        i.nama_suami,
        i.hpl

    FROM tbl_pemeriksaan_ibu_hamil AS p

    INNER JOIN tbl_ibu_hamil AS i
        ON p.ibu_hamil_id = i.id

    WHERE p.tanggal_pemeriksaan BETWEEN '$tanggalMulaiDb' AND '$tanggalAkhirDb'

    ORDER BY p.tanggal_pemeriksaan ASC, p.id ASC
") or die("Query gagal: " . mysqli_error($koneksi));


// ======================================================
// FPDF
// ======================================================

$pdf = new FPDF('L', 'mm', 'A4');

$pdf->SetMargins(10, 10, 10);
$pdf->SetAutoPageBreak(true, 10);

$pdf->AddPage();


// ======================================================
// JUDUL
// ======================================================

$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(0, 7, 'PUSKESMAS MENDIS', 0, 1, 'C');

$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 7, 'LAPORAN PEMERIKSAAN IBU HAMIL', 0, 1, 'C');

$pdf->SetFont('Arial', '', 10);
$pdf->Cell(0, 7, $judulPeriode, 0, 1, 'C');

$pdf->Ln(5);


// ======================================================
// HEADER TABEL
// ======================================================

$pdf->SetFont('Arial', 'B', 9);

$pdf->Cell(10, 8, 'No', 1, 0, 'C');
$pdf->Cell(22, 8, 'Tanggal', 1, 0, 'C');
$pdf->Cell(28, 8, 'No. Reg', 1, 0, 'C');
$pdf->Cell(45, 8, 'Nama Ibu', 1, 0, 'C');
$pdf->Cell(45, 8, 'Nama Suami', 1, 0, 'C');
$pdf->Cell(22, 8, 'HPL', 1, 0, 'C');
$pdf->Cell(30, 8, 'Usia Kehamilan', 1, 0, 'C');
$pdf->Cell(18, 8, 'BB', 1, 0, 'C');
$pdf->Cell(30, 8, 'Tekanan Darah', 1, 0, 'C');
$pdf->Cell(27, 8, 'Hasil', 1, 1, 'C');


// ======================================================
// ISI TABEL
// ======================================================

$pdf->SetFont('Arial', '', 8);

$no = 1;
$adaData = false;

while ($data = mysqli_fetch_assoc($query)) {

    $adaData = true;

    $tanggal = date('d-m-Y', strtotime($data['tanggal_pemeriksaan']));
    $hpl = !empty($data['hpl']) ? date('d-m-Y', strtotime($data['hpl'])) : '-';

    $pdf->Cell(10, 7, $no++, 1, 0, 'C');
    $pdf->Cell(22, 7, $tanggal, 1, 0, 'C');
    $pdf->Cell(28, 7, $data['no_registrasi'] ?? '-', 1, 0, 'C');
    $pdf->Cell(45, 7, $data['nama_ibu'] ?? '-', 1, 0, 'L');
    $pdf->Cell(45, 7, $data['nama_suami'] ?? '-', 1, 0, 'L');
    $pdf->Cell(22, 7, $hpl, 1, 0, 'C');
    $pdf->Cell(30, 7, ($data['usia_kehamilan'] !== null && $data['usia_kehamilan'] !== '') ? $data['usia_kehamilan'] . ' minggu' : '-', 1, 0, 'C');
    $pdf->Cell(18, 7, ($data['berat_badan'] !== null && $data['berat_badan'] !== '') ? $data['berat_badan'] . ' kg' : '-', 1, 0, 'C');
    $pdf->Cell(30, 7, $data['tekanan_darah'] ?: '-', 1, 0, 'C');
    $pdf->Cell(27, 7, $data['hasil'] ?: '-', 1, 1, 'C');

}

if (!$adaData) {

    $pdf->SetFont('Arial', 'I', 9);
    $pdf->Cell(277, 8, 'Tidak ada data pemeriksaan ibu hamil pada periode ini.', 1, 1, 'C');

}


// ======================================================
// TANGGAL CETAK
// ======================================================

$pdf->Ln(5);

$pdf->SetFont('Arial', 'I', 8);

$pdf->Cell(
    0,
    5,
    'Dicetak pada: ' . date('d-m-Y H:i'),
    0,
    1,
    'R'
);


// ======================================================
// OUTPUT PDF
// ======================================================

$pdf->Output('I', 'Laporan-Ibu-Hamil-' . ucfirst($periode) . '.pdf');
