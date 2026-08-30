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
// DATA SELURUH PASIEN
// ======================================================

$query = mysqli_query($koneksi, "

    SELECT
        p.no_rm,
        p.nama,
        p.tgl_lahir,
        p.gender,
        p.no_asuransi,
        p.jenis_pembayaran,
        p.alamat,
        p.nik

    FROM tbl_pasien p

    ORDER BY p.no_rm ASC

") or die("Query gagal: " . mysqli_error($koneksi));

// ======================================================
// CEK DATA
// ======================================================

if (mysqli_num_rows($query) == 0) {

    die(
        "Tidak ada data pasien pada periode "
        . date('d-m-Y', strtotime($tanggalMulai))
        . " s/d "
        . date('d-m-Y', strtotime($tanggalAkhir))
    );

}


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

$pdf->Cell(
    0,
    7,
    'PUSKESMAS MENDIS',
    0,
    1,
    'C'
);

$pdf->SetFont('Arial', 'B', 12);

$pdf->Cell(
    0,
    7,
    'LAPORAN SELURUH PASIEN',
    0,
    1,
    'C'
);

$pdf->SetFont('Arial', '', 10);

$pdf->Cell(
    0,
    7,
    $judulPeriode,
    0,
    1,
    'C'
);

$pdf->Ln(5);


// ======================================================
// HEADER TABEL
// ======================================================

$pdf->SetFont('Arial', 'B', 8);

$pdf->Cell(8, 8, 'No', 1, 0, 'C');
$pdf->Cell(25, 8, 'No RM', 1, 0, 'C');
$pdf->Cell(40, 8, 'Nama Pasien', 1, 0, 'C');
$pdf->Cell(28, 8, 'Tgl Lahir', 1, 0, 'C');
$pdf->Cell(27, 8, 'Jenis Kelamin', 1, 0, 'C');
$pdf->Cell(38, 8, 'Jenis Pembayaran', 1, 0, 'C');
$pdf->Cell(40, 8, 'No. Asuransi', 1, 0, 'C');
$pdf->Cell(45, 8, 'Alamat', 1, 0, 'C');
$pdf->Cell(35, 8, 'NIK', 1, 1, 'C');


// ======================================================
// ISI TABEL
// ======================================================

$pdf->SetFont('Arial', '', 7);

$no = 1;

while ($data = mysqli_fetch_assoc($query)) {

$jenisKelamin = ($data['gender'] == 'P')
    ? 'Pria'
    : 'Wanita';
    
    $jenisPembayaran = !empty($data['jenis_pembayaran'])
        ? $data['jenis_pembayaran']
        : '-';

    $noAsuransi = !empty($data['no_asuransi'])
        ? $data['no_asuransi']
        : '-';

    $tglLahir = !empty($data['tgl_lahir'])
        ? date('d-m-Y', strtotime($data['tgl_lahir']))
        : '-';


    $pdf->Cell(8, 7, $no++, 1, 0, 'C');

    $pdf->Cell(
        25, 7,
        $data['no_rm'],
        1, 0, 'C'
    );

    $pdf->Cell(
        40, 7,
        $data['nama'],
        1, 0, 'L'
    );

    $pdf->Cell(
        28, 7,
        $tglLahir,
        1, 0, 'C'
    );

    $pdf->Cell(
        27, 7,
        $jenisKelamin,
        1, 0, 'C'
    );

    $pdf->Cell(
        38, 7,
        $jenisPembayaran,
        1, 0, 'C'
    );

    $pdf->Cell(
        40, 7,
        $noAsuransi,
        1, 0, 'C'
    );

    $pdf->Cell(
        45, 7,
        $data['alamat'],
        1, 0, 'L'
    );

    $pdf->Cell(
        35, 7,
        $data['nik'],
        1, 1, 'C'
    );
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

$pdf->Output(
    'I',
    'Laporan-Seluruh-' . ucfirst($periode) . '.pdf'
);