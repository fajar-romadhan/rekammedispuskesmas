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
// DATA PASIEN UMUM
// ======================================================
//
// Kriteria disamakan dengan halaman laporan/umum.php (yang tampil di
// layar): pasien yang TIDAK punya no_asuransi, BUKAN jenis_pembayaran.
// Sebelumnya PDF ini pakai jenis_pembayaran = 'Umum', padahal banyak
// pasien lama kolom jenis_pembayaran-nya kosong (belum pernah diisi)
// -> PDF jadi kosong padahal di layar datanya ada.
// ======================================================

$query = mysqli_query($koneksi, "
    SELECT
        id,
        no_rm,
        nama,
        tgl_lahir,
        gender,
        alamat,
        nik,
        jenis_pembayaran
    FROM tbl_pasien
    WHERE no_asuransi IS NULL
       OR no_asuransi = ''
    ORDER BY nama ASC
") or die("Query gagal: " . mysqli_error($koneksi));


if (!$query) {
    die('Query gagal: ' . mysqli_error($koneksi));
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
$pdf->Cell(0, 7, 'PUSKESMAS MENDIS', 0, 1, 'C');

$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 7, 'LAPORAN PASIEN UMUM', 0, 1, 'C');

$pdf->SetFont('Arial', '', 10);
$pdf->Cell(0, 7, $judulPeriode, 0, 1, 'C');

$pdf->Ln(5);


// ======================================================
// HEADER TABEL
// ======================================================

$pdf->SetFont('Arial', 'B', 9);

$pdf->Cell(10, 8, 'No', 1, 0, 'C');
$pdf->Cell(30, 8, 'No RM', 1, 0, 'C');
$pdf->Cell(45, 8, 'Nama Pasien', 1, 0, 'C');
$pdf->Cell(30, 8, 'Tgl Lahir', 1, 0, 'C');
$pdf->Cell(30, 8, 'Jenis Kelamin', 1, 0, 'C');
$pdf->Cell(50, 8, 'Alamat', 1, 0, 'C');
$pdf->Cell(37, 8, 'NIK', 1, 1, 'C');


// ======================================================
// ISI TABEL
// ======================================================

$pdf->SetFont('Arial', '', 8);

$no = 1;

while ($data = mysqli_fetch_assoc($query)) {

   $jenisKelamin = ($data['gender'] == 'P')
    ? 'Pria'
    : 'Wanita';

    $tglLahir = date(
        'd-m-Y',
        strtotime($data['tgl_lahir'])
    );

    $pdf->Cell(10, 7, $no++, 1, 0, 'C');

    $pdf->Cell(
        30,
        7,
        $data['no_rm'],
        1,
        0,
        'C'
    );

    $pdf->Cell(
        45,
        7,
        $data['nama'],
        1,
        0,
        'L'
    );

    $pdf->Cell(
        30,
        7,
        $tglLahir,
        1,
        0,
        'C'
    );

    $pdf->Cell(
        30,
        7,
        $jenisKelamin,
        1,
        0,
        'C'
    );

    $pdf->Cell(
        50,
        7,
        $data['alamat'],
        1,
        0,
        'L'
    );

    $pdf->Cell(
        37,
        7,
        $data['nik'],
        1,
        1,
        'C'
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

$pdf->Output('I', 'Laporan-Umum-' . ucfirst($periode) . '.pdf');