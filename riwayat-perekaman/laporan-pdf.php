<?php

require "../config.php";
require "../asset/fpdf.php";


/*
|--------------------------------------------------------------------------
| PDF DENGAN TABEL YANG BISA MELIPAT (WORD-WRAP)
|--------------------------------------------------------------------------
| FPDF bawaan cuma punya Cell() (teks 1 baris, kalau kepanjangan malah
| tumpang tindih ke kolom sebelah) dan MultiCell() (melipat ke bawah,
| tapi tiap panggilan langsung pindah baris sendiri, tidak bisa dipakai
| berdampingan untuk 1 baris tabel). Class ini nambah TableRow() yang
| menghitung dulu berapa baris yang dibutuhkan tiap kolom (NbLines(),
| resep umum dari dokumentasi FPDF), lalu menggambar semua kolom di
| baris yang sama dengan tinggi baris menyesuaikan isi yang terpanjang.
|--------------------------------------------------------------------------
*/

class PDFTabelLipat extends FPDF
{
    function NbLines($w, $txt)
    {
        $cw = &$this->CurrentFont['cw'];

        if ($w == 0) {
            $w = $this->w - $this->rMargin - $this->x;
        }

        $wmax = ($w - 2 * $this->cMargin) * 1000 / $this->FontSize;

        $s  = str_replace("\r", '', (string) $txt);
        $nb = strlen($s);

        if ($nb > 0 && $s[$nb - 1] == "\n") {
            $nb--;
        }

        $sep = -1;
        $i   = 0;
        $j   = 0;
        $l   = 0;
        $nl  = 1;

        while ($i < $nb) {

            $c = $s[$i];

            if ($c == "\n") {
                $i++;
                $sep = -1;
                $j   = $i;
                $l   = 0;
                $nl++;
                continue;
            }

            if ($c == ' ') {
                $sep = $i;
            }

            $l += $cw[$c] ?? 0;

            if ($l > $wmax) {

                if ($sep == -1) {
                    if ($i == $j) {
                        $i++;
                    }
                } else {
                    $i = $sep + 1;
                }

                $sep = -1;
                $j   = $i;
                $l   = 0;
                $nl++;

            } else {
                $i++;
            }
        }

        return $nl;
    }

    function TableRow($widths, $data, $lineHeight = 3.6, $aligns = [])
    {
        $nbLines = 1;

        foreach ($data as $i => $txt) {
            $nbLines = max($nbLines, $this->NbLines($widths[$i], $txt));
        }

        $height = $nbLines * $lineHeight;

        // Kalau baris ini tidak muat lagi di halaman sekarang, pindah
        // halaman dulu (header tabel tidak diulang, sama seperti
        // perilaku lama).
        if ($this->GetY() + $height > $this->PageBreakTrigger) {
            $this->AddPage($this->CurOrientation);
        }

        $x = $this->GetX();
        $y = $this->GetY();

        foreach (array_values($data) as $i => $txt) {

            $align = $aligns[$i] ?? 'L';

            $this->Rect($x, $y, $widths[$i], $height);

            $this->SetXY($x, $y);
            $this->MultiCell($widths[$i], $lineHeight, (string) $txt, 0, $align);

            $x += $widths[$i];
        }

        $this->SetXY($this->lMargin, $y + $height);
    }
}

// Cek parameter id
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("ID Pasien tidak ditemukan.");
}

$id = mysqli_real_escape_string($koneksi, $_GET['id']);

// ========================
// DATA PASIEN
// ========================
$queryPasien = mysqli_query($koneksi, "
    SELECT *
    FROM tbl_pasien
    WHERE id = '$id'
");


if (!$queryPasien) {
    die("Query pasien gagal : " . mysqli_error($koneksi));
}

if (mysqli_num_rows($queryPasien) == 0) {
    die("Data pasien tidak ditemukan.");
}

$pasien = mysqli_fetch_assoc($queryPasien);

// Hitung umur otomatis
$tgl_lahir = new DateTime($pasien['tgl_lahir']);
$hari_ini  = new DateTime();
$umur = $hari_ini->diff($tgl_lahir)->y;

// ========================
// RIWAYAT REKAM MEDIS
// ========================
$queryRM = mysqli_query($koneksi, "
    SELECT rm.*, p.no_rm
    FROM tbl_rekammedis rm
    JOIN tbl_pasien p ON rm.id_pasien = p.id
    WHERE rm.id_pasien = '$id'
    ORDER BY rm.tgl_rm DESC
");

if (!$queryRM) {
    die("Query rekam medis gagal : " . mysqli_error($koneksi));
}

// ========================
// PDF
// ========================
$pdf = new PDFTabelLipat('p','mm','A4');
$pdf->AddPage();

$pdf->SetFont('Arial','B',12);

$pdf->Cell(190,6,'PEMERINTAH KABUPATEN MUSI BANYUASIN',0,1,'C');
$pdf->Cell(190,6,'DINAS KESEHATAN',0,1,'C');
$pdf->Cell(190,6,'POSKESDES MENDIS',0,1,'C');
$pdf->Cell(190,6,'Kec. Bayung Lencir, Sumatera Selatan',0,1,'C');

$pdf->Line(10,35,200,35);
$pdf->Ln(5);

$pdf->Image('../asset/logo-muba.png',10,8,20);
$pdf->Image('../asset/logo-poskesdes.png',180,8,20);

$pdf->Ln(3); // memberi jarak


$pdf->Ln(5);

$pdf->SetFont('Arial','',11);


// Catatan: sebelumnya semua Cell() identitas ini ditumpuk di satu baris
// yang sama (ln=0 terus) sampai lebarnya total ~295mm, padahal lebar
// halaman cuma ~190mm -> field yang belakangan (termasuk Nama Pasien)
// jadi tercetak di luar halaman alias tidak pernah kelihatan. Ditata
// ulang jadi 2 kolom per baris (masing-masing max 90mm) supaya semua
// field, termasuk Nama Pasien, benar-benar muat & terlihat di kertas.

$pdf->Cell(40,6,'Nama Pasien',0,0);
$pdf->Cell(5,6,':',0,0);
$pdf->Cell(45,6,$pasien['nama'],0,0);

$pdf->Cell(40,6,'No RM',0,0);
$pdf->Cell(5,6,':',0,0);
$pdf->Cell(45,6,$pasien['no_rm'],0,1);


$pdf->Cell(40,6,'NIK',0,0);
$pdf->Cell(5,6,':',0,0);
$pdf->Cell(45,6,$pasien['nik'],0,0);

$pdf->Cell(40,6,'No Asuransi',0,0);
$pdf->Cell(5,6,':',0,0);
$pdf->Cell(45,6,$pasien['no_asuransi'] ?: '-',0,1);


$pdf->Cell(40,6,'Tanggal Lahir',0,0);
$pdf->Cell(5,6,':',0,0);
$pdf->Cell(45,6,$pasien['tgl_lahir'],0,0);

$pdf->Cell(40,6,'Umur',0,0);
$pdf->Cell(5,6,':',0,0);
$pdf->Cell(45,6,$umur.' Tahun',0,1);


$pdf->Cell(40,6,'Jenis Kelamin',0,0);
$pdf->Cell(5,6,':',0,0);
$pdf->Cell(45,6,($pasien['gender']=='P')?'Pria':'Wanita',0,0);

$pdf->Cell(40,6,'Golongan Darah',0,0);
$pdf->Cell(5,6,':',0,0);
$pdf->Cell(45,6,$pasien['gol_darah'] ?: '-',0,1);


$pdf->Cell(40,6,'No Handphone',0,0);
$pdf->Cell(5,6,':',0,0);
$pdf->Cell(140,6,$pasien['telpon'],0,1);

$pdf->Cell(40,6,'Alamat',0,0);
$pdf->Cell(5,6,':',0,0);
$pdf->MultiCell(145,6,$pasien['alamat'],0,'L');

$pdf->Ln(5);

// ========================
// HEADER TABEL
// ========================
$pdf->SetFont('Arial','B',6);

$pdf->Cell(6,10,'No',1,0,'C');
$pdf->Cell(14,10,'Tgl',1,0,'C');
$pdf->Cell(16,10,'Anam',1,0,'C');
$pdf->Cell(14,10,'Fisik',1,0,'C');
$pdf->Cell(12,10,'Lab',1,0,'C');
$pdf->Cell(16,10,'Diagnosa',1,0,'C');
$pdf->Cell(16,10,'Keluhan',1,0,'C');
$pdf->Cell(16,10,'Tindakan',1,0,'C');
$pdf->Cell(16,10,'Resep',1,0,'C');
$pdf->Cell(12,10,'Poli',1,0,'C');
$pdf->Cell(12,10,'R.Int',1,0,'C');
$pdf->Cell(12,10,'R.Ext',1,0,'C');
$pdf->Cell(8,10,'TTD',1,1,'C');


// ========================
// DATA REKAM MEDIS
// ========================
$pdf->SetFont('Arial','',6);

$no = 1;

$lebarKolom = [6, 14, 16, 14, 12, 16, 16, 16, 16, 12, 12, 12, 8];
$rataKolom  = ['C', 'C', 'L', 'L', 'L', 'L', 'L', 'L', 'L', 'C', 'C', 'C', 'C'];

if(mysqli_num_rows($queryRM) > 0){

    while($row=mysqli_fetch_assoc($queryRM)){

        $pdf->TableRow($lebarKolom, [
            $no++,
            $row['tgl_rm'],
            $row['anamnesa'],
            $row['pemeriksaan_fisik'],
            $row['pemeriksaan_lab'],
            $row['diagnosa'],
            $row['keluhan'],
            $row['tindakan'],
            $row['resep_obat'],
            $row['poli'],
            $row['rujuk_internal'],
            $row['rujuk_eksternal'],
            '',
        ], 3.6, $rataKolom);

    }

}else{

    $pdf->Cell(170,10,'Belum ada riwayat rekam medis.',1,1,'C');

}

$pdf->Output('I','Riwayat_Rekam_Medis.pdf');