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
// DATA PELAYANAN KB
// ======================================================
//
// Kriteria disamakan dengan halaman laporan/kb.php (yang tampil di
// layar), difilter berdasarkan tanggal_pelayanan pada periode terpilih.
// ======================================================

$tanggalMulaiDb = mysqli_real_escape_string($koneksi, $tanggalMulai);
$tanggalAkhirDb = mysqli_real_escape_string($koneksi, $tanggalAkhir);

$query = mysqli_query($koneksi, "
    SELECT

        p.tanggal_pelayanan,
        p.metode_kb,
        p.berat_badan,
        p.tinggi_badan,
        p.tekanan_darah,
        p.hasil_pemeriksaan,

        k.no_peserta_kb,
        k.nama_suami,
        k.jenis_kb,

        ibu.nama_ibu AS nama_istri

    FROM tbl_pelayanan_kb AS p

    INNER JOIN tbl_kb AS k
        ON p.id_kb = k.id_kb

    LEFT JOIN tbl_ibu_hamil AS ibu
        ON k.no_kk = ibu.no_kk

    WHERE p.tanggal_pelayanan BETWEEN '$tanggalMulaiDb' AND '$tanggalAkhirDb'

    ORDER BY p.tanggal_pelayanan ASC, p.id_pelayanan_kb ASC
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
$pdf->Cell(0, 7, 'LAPORAN PELAYANAN KB', 0, 1, 'C');

$pdf->SetFont('Arial', '', 10);
$pdf->Cell(0, 7, $judulPeriode, 0, 1, 'C');

$pdf->Ln(5);


// ======================================================
// HEADER TABEL
// ======================================================

$pdf->SetFont('Arial', 'B', 9);

$pdf->Cell(10, 8, 'No', 1, 0, 'C');
$pdf->Cell(22, 8, 'Tanggal', 1, 0, 'C');
$pdf->Cell(28, 8, 'No. Peserta', 1, 0, 'C');
$pdf->Cell(42, 8, 'Nama Istri', 1, 0, 'C');
$pdf->Cell(42, 8, 'Nama Suami', 1, 0, 'C');
$pdf->Cell(28, 8, 'Jenis KB', 1, 0, 'C');
$pdf->Cell(28, 8, 'Metode KB', 1, 0, 'C');
$pdf->Cell(18, 8, 'BB', 1, 0, 'C');
$pdf->Cell(18, 8, 'TB', 1, 0, 'C');
$pdf->Cell(41, 8, 'Tekanan Darah', 1, 1, 'C');


// ======================================================
// ISI TABEL
// ======================================================

$pdf->SetFont('Arial', '', 8);

$no = 1;
$adaData = false;

while ($data = mysqli_fetch_assoc($query)) {

    $adaData = true;

    $tanggal = date('d-m-Y', strtotime($data['tanggal_pelayanan']));

    $pdf->Cell(10, 7, $no++, 1, 0, 'C');
    $pdf->Cell(22, 7, $tanggal, 1, 0, 'C');
    $pdf->Cell(28, 7, $data['no_peserta_kb'] ?? '-', 1, 0, 'C');
    $pdf->Cell(42, 7, $data['nama_istri'] ?? '-', 1, 0, 'L');
    $pdf->Cell(42, 7, $data['nama_suami'] ?? '-', 1, 0, 'L');
    $pdf->Cell(28, 7, $data['jenis_kb'] ?? '-', 1, 0, 'C');
    $pdf->Cell(28, 7, $data['metode_kb'] ?? '-', 1, 0, 'C');
    $pdf->Cell(18, 7, ($data['berat_badan'] !== null && $data['berat_badan'] !== '') ? $data['berat_badan'] . ' kg' : '-', 1, 0, 'C');
    $pdf->Cell(18, 7, ($data['tinggi_badan'] !== null && $data['tinggi_badan'] !== '') ? $data['tinggi_badan'] . ' cm' : '-', 1, 0, 'C');
    $pdf->Cell(41, 7, $data['tekanan_darah'] ?: '-', 1, 1, 'C');

}

if (!$adaData) {

    $pdf->SetFont('Arial', 'I', 9);
    $pdf->Cell(277, 8, 'Tidak ada data pelayanan KB pada periode ini.', 1, 1, 'C');

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

$pdf->Output('I', 'Laporan-KB-' . ucfirst($periode) . '.pdf');
