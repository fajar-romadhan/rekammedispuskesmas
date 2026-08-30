<?php

session_start();

require "../template/rbac.php";

// Hanya Bidan
cekAkses([ROLE_BIDAN]);

require "../config.php";


/*
|--------------------------------------------------------------------------
| QUERY DATA LAPORAN KEHAMILAN
|--------------------------------------------------------------------------
| Data pemeriksaan ibu hamil diambil dari tbl_pemeriksaan_ibu_hamil
| kemudian dihubungkan dengan tbl_ibu_hamil
|--------------------------------------------------------------------------
*/

$query = mysqli_query(
    $koneksi,
    "SELECT
        p.id,
        p.ibu_hamil_id,
        p.no_registrasi,
        p.tanggal_pemeriksaan,
        p.usia_kehamilan,
        p.berat_badan,
        p.tekanan_darah,
        p.tfu,
        p.djj,
        p.hasil,
        p.keterangan,

        i.nama_ibu,
        i.nik,
        i.nama_suami,
        i.no_hp,
        i.hpl,
        i.gravida,
        i.para,
        i.abortus

    FROM tbl_pemeriksaan_ibu_hamil p

    INNER JOIN tbl_ibu_hamil i
        ON p.ibu_hamil_id = i.id

    ORDER BY
        p.tanggal_pemeriksaan DESC,
        p.id DESC"
);

if (!$query) {
    die(
        "Query laporan kehamilan gagal: " .
        mysqli_error($koneksi)
    );
}


/*
|--------------------------------------------------------------------------
| FUNGSI TAMPIL
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
| FUNGSI TANGGAL INDONESIA
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
$jumlahData = mysqli_num_rows($query);

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>
        Laporan Kehamilan - Puskesmas Desa Mendis
    </title>


    <style>

        /*
        |--------------------------------------------------------------------------
        | HALAMAN
        |--------------------------------------------------------------------------
        */

        @page {
            size: A4 landscape;
            margin: 10mm;
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


        /*
        |--------------------------------------------------------------------------
        | TOMBOL
        |--------------------------------------------------------------------------
        */

        .no-print {
            margin-bottom: 15px;
            display: flex;
            gap: 8px;
        }


        .btn {
            display: inline-block;
            padding: 9px 16px;
            border-radius: 4px;
            text-decoration: none;
            cursor: pointer;
            font-size: 13px;
        }


        .btn-back {
            background: #fff;
            color: #000;
            border: 1px solid #000;
        }


        .btn-print {
            background: #000;
            color: #fff;
            border: 1px solid #000;
        }


        .btn:hover {
            opacity: 0.8;
        }


        /*
        |--------------------------------------------------------------------------
        | JUDUL
        |--------------------------------------------------------------------------
        */

        .judul {
            text-align: center;
            margin-bottom: 15px;
        }


        .judul h2 {
            margin: 0;
            font-size: 20px;
        }


        .judul h3 {
            margin: 4px 0;
            font-size: 16px;
        }


        .judul p {
            margin: 5px 0 0 0;
            font-size: 12px;
        }


        /*
        |--------------------------------------------------------------------------
        | TABEL
        |--------------------------------------------------------------------------
        */

        .tabel {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }


        .tabel th,
        .tabel td {
            border: 1px solid #000;
            padding: 4px 3px;
            vertical-align: middle;
            word-wrap: break-word;
        }


        .tabel th {
            background: #eaeaea;
            text-align: center;
            font-weight: bold;
        }


        .tabel td {
            text-align: center;
        }


        .text-left {
            text-align: left !important;
        }


        /*
        |--------------------------------------------------------------------------
        | LEBAR KOLOM
        |--------------------------------------------------------------------------
        */

        .col-no {
            width: 3%;
        }

        .col-tanggal {
            width: 7%;
        }

        .col-nama {
            width: 12%;
        }

        .col-nik {
            width: 10%;
        }

        .col-suami {
            width: 11%;
        }

        .col-uk {
            width: 6%;
        }

        .col-bb {
            width: 6%;
        }

        .col-td {
            width: 8%;
        }

        .col-tfu {
            width: 6%;
        }

        .col-djj {
            width: 7%;
        }

        .col-hasil {
            width: 12%;
        }

        .col-keterangan {
            width: 12%;
        }


        /*
        |--------------------------------------------------------------------------
        | FOOTER
        |--------------------------------------------------------------------------
        */

        .footer {
            margin-top: 20px;
            width: 100%;
        }


        .jumlah {
            float: left;
            font-size: 11px;
        }


        .ttd {
            float: right;
            width: 220px;
            text-align: center;
            font-size: 11px;
        }


        .clear {
            clear: both;
        }


        /*
        |--------------------------------------------------------------------------
        | PRINT
        |--------------------------------------------------------------------------
        */

        @media print {

            .no-print {
                display: none !important;
            }

            body {
                font-size: 9px;
            }

            .tabel th,
            .tabel td {
                padding: 3px 2px;
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
        href="../bidan/rekam-medis-kebidanan.php"
        class="btn btn-back">

        ← Kembali

    </a>


    <button
        type="button"
        class="btn btn-print"
        onclick="window.print();">

        🖨 Cetak / Simpan PDF

    </button>

</div>


<!-- =========================================================
     JUDUL
========================================================= -->

<div class="judul">

    <h2>
        PUSKESMAS DESA MENDIS
    </h2>

    <h3>
        LAPORAN PEMERIKSAAN KEHAMILAN
    </h3>

    <p>
        Data Pemeriksaan Ibu Hamil
    </p>

</div>


<!-- =========================================================
     TABEL
========================================================= -->

<table class="tabel">

    <thead>

        <tr>

            <th class="col-no">
                No
            </th>

            <th class="col-tanggal">
                Tanggal
            </th>

            <th class="col-nama">
                Nama Ibu
            </th>

            <th class="col-nik">
                NIK
            </th>

            <th class="col-suami">
                Nama Suami
            </th>

            <th class="col-uk">
                Usia Kehamilan
            </th>

            <th class="col-bb">
                BB
            </th>

            <th class="col-td">
                Tensi Darah
            </th>

            <th class="col-tfu">
                TFU
            </th>

            <th class="col-djj">
                DJJ
            </th>

            <th class="col-hasil">
                Hasil
            </th>

            <th class="col-keterangan">
                Keterangan
            </th>

        </tr>

    </thead>


    <tbody>

    <?php

    $no = 1;

    if ($jumlahData > 0):

        while (
            $data = mysqli_fetch_assoc($query)
        ):

    ?>

        <tr>

            <!-- NO -->

            <td>
                <?= $no++; ?>
            </td>


            <!-- TANGGAL -->

            <td>

                <?= tanggalIndonesia(
                    $data['tanggal_pemeriksaan']
                ); ?>

            </td>


            <!-- NAMA IBU -->

            <td class="text-left">

                <?= tampil(
                    $data['nama_ibu']
                ); ?>

            </td>


            <!-- NIK -->

            <td>

                <?= tampil(
                    $data['nik']
                ); ?>

            </td>


            <!-- NAMA SUAMI -->

            <td class="text-left">

                <?= tampil(
                    $data['nama_suami']
                ); ?>

            </td>


            <!-- USIA KEHAMILAN -->

            <td>

                <?php

                if (
                    !empty(
                        $data['usia_kehamilan']
                    )
                ) {

                    echo tampil(
                        $data['usia_kehamilan']
                    );

                    echo ' mgg';

                } else {

                    echo '-';

                }

                ?>

            </td>


            <!-- BERAT BADAN -->

            <td>

                <?php

                if (
                    $data['berat_badan'] !== null &&
                    $data['berat_badan'] !== ''
                ) {

                    echo tampil(
                        $data['berat_badan']
                    );

                    echo ' Kg';

                } else {

                    echo '-';

                }

                ?>

            </td>


            <!-- TENSI -->

            <td>

                <?php

                if (
                    !empty(
                        $data['tekanan_darah']
                    )
                ) {

                    echo tampil(
                        $data['tekanan_darah']
                    );

                } else {

                    echo '-';

                }

                ?>

            </td>


            <!-- TFU -->

            <td>

                <?php

                if (
                    !empty(
                        $data['tfu']
                    )
                ) {

                    echo tampil(
                        $data['tfu']
                    );

                    echo ' cm';

                } else {

                    echo '-';

                }

                ?>

            </td>


            <!-- DJJ -->

            <td>

                <?php

                if (
                    !empty(
                        $data['djj']
                    )
                ) {

                    echo tampil(
                        $data['djj']
                    );

                    echo ' x/mnt';

                } else {

                    echo '-';

                }

                ?>

            </td>


            <!-- HASIL -->

            <td class="text-left">

                <?= tampil(
                    $data['hasil']
                ); ?>

            </td>


            <!-- KETERANGAN -->

            <td class="text-left">

                <?= tampil(
                    $data['keterangan']
                ); ?>

            </td>

        </tr>


    <?php

        endwhile;

    else:

    ?>

        <tr>

            <td
                colspan="12"
                style="text-align:center; padding:20px;">

                Belum ada data pemeriksaan kehamilan.

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

    <div class="jumlah">

        Jumlah Data Pemeriksaan :

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
