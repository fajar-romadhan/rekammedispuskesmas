<?php

session_start();

require "../template/rbac.php";

// Hanya Petugas
cekAkses([ROLE_PETUGAS]);

require "../config.php";


/*
|--------------------------------------------------------------------------
| AMBIL SEMUA DATA KB
|--------------------------------------------------------------------------
*/

$queryKB = mysqli_query(
    $koneksi,
    "SELECT *
     FROM tbl_kb
     ORDER BY tanggal ASC, id_kb ASC"
);

if (!$queryKB) {
    die(
        "Query data KB gagal : " .
        mysqli_error($koneksi)
    );
}


/*
|--------------------------------------------------------------------------
| FUNGSI TAMPIL DATA
|--------------------------------------------------------------------------
*/

function tampil($data)
{
    if (
        isset($data) &&
        $data !== null &&
        $data !== ''
    ) {
        return htmlspecialchars(
            $data,
            ENT_QUOTES,
            'UTF-8'
        );
    }

    return '-';
}


/*
|--------------------------------------------------------------------------
| FUNGSI FORMAT TANGGAL
|--------------------------------------------------------------------------
*/

function tanggalIndonesia($tanggal)
{
    if (
        empty($tanggal) ||
        $tanggal == '0000-00-00'
    ) {
        return '-';
    }

    return date(
        'd-m-Y',
        strtotime($tanggal)
    );
}


/*
|--------------------------------------------------------------------------
| JUMLAH DATA
|--------------------------------------------------------------------------
*/

$jumlahData = mysqli_num_rows($queryKB);

?>

<!DOCTYPE html>

<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        Register KB - Puskesmas Desa Mendis
    </title>


    <style>

        /* =====================================================
           HALAMAN
        ===================================================== */

        @page {
            size: A4 landscape;
            margin: 12mm;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 10px;
            color: #000;
            margin: 0;
            padding: 0;
        }


        /* =====================================================
           TOMBOL
        ===================================================== */

        .no-print {
            margin-bottom: 15px;
            display: flex;
            gap: 8px;
            align-items: center;
        }

        .btn-print,
        .btn-back {
            display: inline-block;
            padding: 9px 18px;
            font-size: 13px;
            border-radius: 3px;
            cursor: pointer;
            text-decoration: none;
        }

        .btn-print {
            border: 1px solid #000;
            background: #000;
            color: #fff;
        }

        .btn-print:hover {
            background: #333;
        }

        .btn-back {
            border: 1px solid #000;
            background: #fff;
            color: #000;
        }

        .btn-back:hover {
            background: #eee;
        }


        /* =====================================================
           JUDUL / KOP
        ===================================================== */

        .judul {
            text-align: center;
            margin-bottom: 15px;
        }

        .judul h2 {
            margin: 0;
            font-size: 21px;
            font-weight: bold;
        }

        .judul h3 {
            margin: 4px 0;
            font-size: 17px;
            font-weight: bold;
        }

        .judul .subjudul {
            margin-top: 5px;
            font-size: 13px;
        }


        /* =====================================================
           TABEL REGISTER KB
        ===================================================== */

        .tabel-kb {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        .tabel-kb th,
        .tabel-kb td {
            border: 1px solid #000;
            padding: 5px 3px;
            vertical-align: middle;
            font-size: 8.5px;
        }

        .tabel-kb th {
            background: #eee;
            text-align: center;
            font-weight: bold;
        }

        .tabel-kb td {
            text-align: center;
        }

        .tabel-kb td.alamat,
        .tabel-kb td.keterangan {
            text-align: left;
            word-wrap: break-word;
        }


        /* =====================================================
           LEBAR KOLOM
        ===================================================== */

        .no {
            width: 3%;
        }

        .tanggal {
            width: 8%;
        }

        .no-kk {
            width: 11%;
        }

        .no-peserta {
            width: 11%;
        }

        .tgl-lahir {
            width: 8%;
        }

        .suami {
            width: 11%;
        }

        .anak {
            width: 5%;
        }

        .alamat {
            width: 14%;
        }

        .jenis-kb {
            width: 9%;
        }

        .kunjungan {
            width: 7%;
        }

        .tensi {
            width: 8%;
        }

        .bb {
            width: 6%;
        }

        .keterangan {
            width: 12%;
        }


        /* =====================================================
           FOOTER
        ===================================================== */

        .footer {
            margin-top: 25px;
            width: 100%;
        }

        .jumlah-data {
            float: left;
            font-size: 12px;
        }

        .ttd {
            width: 250px;
            float: right;
            text-align: center;
            font-size: 12px;
        }

        .clear {
            clear: both;
        }


        /* =====================================================
           PRINT
        ===================================================== */

        @media print {

            .no-print {
                display: none !important;
            }

            body {
                font-size: 10px;
            }

            .tabel-kb th,
            .tabel-kb td {
                font-size: 8.5px;
            }

        }

    </style>

</head>


<body>


<!-- =========================================================
     TOMBOL
========================================================= -->

<div class="no-print">

    <a
        href="register-kb.php"
        class="btn-back"
    >
        ← Kembali
    </a>

    <button
        type="button"
        class="btn-print"
        onclick="window.print()"
    >
        Cetak / Simpan PDF
    </button>

</div>


<!-- =========================================================
     JUDUL / KOP
========================================================= -->

<div class="judul">

    <h2>
        PUSKESMAS DESA MENDIS
    </h2>

    <h3>
        REGISTER KELUARGA BERENCANA (KB)
    </h3>

    <div class="subjudul">
        Data Register Peserta Keluarga Berencana
    </div>

</div>


<!-- =========================================================
     TABEL REGISTER KB
========================================================= -->

<table class="tabel-kb">

    <thead>

        <tr>

            <th class="no">
                No
            </th>

            <th class="tanggal">
                Tanggal
            </th>

            <th class="no-kk">
                No. KK
            </th>

            <th class="no-peserta">
                No. Peserta KB
            </th>

            <th class="tgl-lahir">
                Tanggal Lahir
            </th>

            <th class="suami">
                Nama Suami
            </th>

            <th class="anak">
                Jumlah Anak
            </th>

            <th class="alamat">
                Alamat
            </th>

            <th class="jenis-kb">
                Jenis KB
            </th>

            <th class="kunjungan">
                Kunjungan
            </th>

            <th class="tensi">
                Tensi Darah
            </th>

            <th class="bb">
                BB
            </th>

            <th class="keterangan">
                Keterangan
            </th>

        </tr>

    </thead>


    <tbody>

    <?php

    $no = 1;

    if ($jumlahData > 0):

        while ($data = mysqli_fetch_assoc($queryKB)):

    ?>

        <tr>

            <!-- NO -->

            <td>
                <?= $no++; ?>
            </td>


            <!-- TANGGAL -->

            <td>
                <?= tanggalIndonesia(
                    $data['tanggal'] ?? null
                ); ?>
            </td>


            <!-- NO KK -->

            <td>
                <?= tampil(
                    $data['no_kk'] ?? null
                ); ?>
            </td>


            <!-- NO PESERTA KB -->

            <td>
                <?= tampil(
                    $data['no_peserta_kb'] ?? null
                ); ?>
            </td>


            <!-- TANGGAL LAHIR -->

            <td>
                <?= tanggalIndonesia(
                    $data['tanggal_lahir'] ?? null
                ); ?>
            </td>


            <!-- NAMA SUAMI -->

            <td>
                <?= tampil(
                    $data['nama_suami'] ?? null
                ); ?>
            </td>


            <!-- JUMLAH ANAK -->

            <td>
                <?= tampil(
                    $data['jumlah_anak'] ?? null
                ); ?>
            </td>


            <!-- ALAMAT -->

            <td class="alamat">
                <?= tampil(
                    $data['alamat'] ?? null
                ); ?>
            </td>


            <!-- JENIS KB -->

            <td>
                <?= tampil(
                    $data['jenis_kb'] ?? null
                ); ?>
            </td>


            <!-- KUNJUNGAN -->

            <td>
                <?= tampil(
                    $data['kunjungan'] ?? null
                ); ?>
            </td>


            <!-- TENSI DARAH -->

            <td>
                <?= tampil(
                    $data['tensi_darah'] ?? null
                ); ?>
            </td>


            <!-- BB -->

            <td>

                <?php if (
                    isset($data['bb']) &&
                    $data['bb'] !== '' &&
                    $data['bb'] !== null
                ): ?>

                    <?= tampil($data['bb']); ?> Kg

                <?php else: ?>

                    -

                <?php endif; ?>

            </td>


            <!-- KETERANGAN -->

            <td class="keterangan">
                <?= tampil(
                    $data['keterangan'] ?? null
                ); ?>
            </td>

        </tr>


    <?php

        endwhile;

    else:

    ?>

        <tr>

            <td colspan="13">
                Belum ada data peserta KB.
            </td>

        </tr>

    <?php

    endif;

    ?>

    </tbody>

</table>


<!-- =========================================================
     FOOTER
========================================================= -->

<div class="footer">

    <div class="jumlah-data">

        Jumlah Data Peserta KB :
        <strong>
            <?= $jumlahData; ?>
        </strong>

    </div>


    <div class="ttd">

        Mendis,
        <?= date('d-m-Y'); ?>

        <br><br>

        Bidan Pemeriksa

        <br><br><br><br>

        __________________________

        <br>

        Nama dan Tanda Tangan

    </div>


    <div class="clear"></div>

</div>


</body>

</html>