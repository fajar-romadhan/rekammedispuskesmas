<?php

session_start();

require "../template/rbac.php";

// Hanya Bidan
cekAkses([ROLE_BIDAN]);

require "../config.php";


/*
|--------------------------------------------------------------------------
| AMBIL SEMUA DATA IBU HAMIL
|--------------------------------------------------------------------------
*/

$queryIbu = mysqli_query(
    $koneksi,
    "SELECT *
     FROM tbl_ibu_hamil
     ORDER BY id DESC"
);

if (!$queryIbu) {
    die(
        "Query data ibu hamil gagal : " .
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

$jumlahData = mysqli_num_rows($queryIbu);

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
        Registrasi Ibu Hamil - Puskesmas Desa Mendis
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
           TABEL REGISTRASI IBU HAMIL
        ===================================================== */

        .tabel-ibu {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        .tabel-ibu th,
        .tabel-ibu td {
            border: 1px solid #000;
            padding: 5px 3px;
            vertical-align: middle;
            font-size: 9px;
        }

        .tabel-ibu th {
            background: #eee;
            text-align: center;
            font-weight: bold;
        }

        .tabel-ibu td {
            text-align: center;
        }


        /* =====================================================
           LEBAR KOLOM
        ===================================================== */

        .no {
            width: 3%;
        }

        .nama {
            width: 15%;
        }

        .nik {
            width: 13%;
        }

        .suami {
            width: 14%;
        }

        .hp {
            width: 10%;
        }

        .hpht {
            width: 9%;
        }

        .hpl {
            width: 9%;
        }

        .gpa {
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

            .tabel-ibu th,
            .tabel-ibu td {
                font-size: 9px;
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
        href="rekam-medis-kebidanan.php"
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
        REGISTRASI IBU HAMIL
    </h3>

    <div class="subjudul">
        Data Registrasi dan Rekam Medis Kebidanan
    </div>

</div>


<!-- =========================================================
     TABEL REGISTRASI IBU HAMIL
========================================================= -->

<table class="tabel-ibu">

    <thead>

        <tr>

            <th class="no">
                No
            </th>

            <th class="nama">
                Nama Ibu
            </th>

            <th class="nik">
                NIK
            </th>

            <th class="suami">
                Nama Suami
            </th>

            <th class="hp">
                No. HP
            </th>

            <th class="hpht">
                HPHT
            </th>

            <th class="hpl">
                HPL
            </th>

            <th class="gpa">
                G / P / A
            </th>

        </tr>

    </thead>


    <tbody>

    <?php

    $no = 1;

    if ($jumlahData > 0):

        while ($data = mysqli_fetch_assoc($queryIbu)):

    ?>

        <tr>

            <!-- NO -->

            <td>
                <?= $no++; ?>
            </td>


            <!-- NAMA IBU -->

            <td>
                <?= tampil(
                    $data['nama_ibu'] ?? null
                ); ?>
            </td>


            <!-- NIK -->

            <td>
                <?= tampil(
                    $data['nik'] ?? null
                ); ?>
            </td>


            <!-- NAMA SUAMI -->

            <td>
                <?= tampil(
                    $data['nama_suami'] ?? null
                ); ?>
            </td>


            <!-- NO HP -->

            <td>
                <?= tampil(
                    $data['no_hp'] ?? null
                ); ?>
            </td>


            <!-- HPHT -->

            <td>
                <?= tanggalIndonesia(
                    $data['hpht'] ?? null
                ); ?>
            </td>


            <!-- HPL -->

            <td>
                <?= tanggalIndonesia(
                    $data['hpl'] ?? null
                ); ?>
            </td>


            <!-- G / P / A -->

            <td>
                <?= tampil($data['gravida'] ?? '0'); ?>
                /
                <?= tampil($data['para'] ?? '0'); ?>
                /
                <?= tampil($data['abortus'] ?? '0'); ?>
            </td>

        </tr>


    <?php

        endwhile;

    else:

    ?>

        <tr>

            <td colspan="8">
                Belum ada data ibu hamil.
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

        Jumlah Data Ibu Hamil :
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
