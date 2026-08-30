<?php

session_start();

require "../template/rbac.php";

// Hanya Bidan
cekAkses([ROLE_BIDAN]);

require "../config.php";


/*
|--------------------------------------------------------------------------
| QUERY DATA LAPORAN KEBIDANAN
|--------------------------------------------------------------------------
| Data pelayanan KB diambil dari tbl_pelayanan_kb
| kemudian dihubungkan dengan tbl_kb
|--------------------------------------------------------------------------
*/

$query = mysqli_query(
    $koneksi,
    "SELECT
        p.id_pelayanan_kb,
        p.id_kb,
        p.tanggal_pelayanan,
        p.metode_kb,
        p.keluhan,
        p.berat_badan,
        p.tinggi_badan,
        p.tekanan_darah,
        p.hasil_pemeriksaan,
        p.efek_samping,
        p.keterangan,

        k.no_peserta_kb,
        k.no_kk,
        k.tanggal_lahir,
        k.nama_suami,
        k.jumlah_anak,
        k.alamat,
        k.jenis_kb,
        k.kunjungan

    FROM tbl_pelayanan_kb p

    INNER JOIN tbl_kb k
        ON p.id_kb = k.id_kb

    ORDER BY
        p.tanggal_pelayanan DESC,
        p.id_pelayanan_kb DESC"
);

if (!$query) {
    die(
        "Query laporan kebidanan gagal: " .
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
        Laporan KB - Puskesmas Desa Mendis
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

        .col-peserta {
            width: 9%;
        }

        .col-kk {
            width: 10%;
        }

        .col-suami {
            width: 11%;
        }

        .col-metode {
            width: 9%;
        }

        .col-tensi {
            width: 8%;
        }

        .col-bb {
            width: 6%;
        }

        .col-tb {
            width: 6%;
        }

        .col-hasil {
            width: 11%;
        }

        .col-keterangan {
            width: 12%;
        }

        .col-efek {
            width: 8%;
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
        href="../bidan/register-kb.php"
        class="btn btn-back"
        onclick="history.back(); return false;">

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
        LAPORAN PELAYANAN KB
    </h3>

    <p>
        Data Pelayanan Keluarga Berencana (KB)
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

            <th class="col-peserta">
                No. Peserta KB
            </th>

            <th class="col-kk">
                No. KK
            </th>

            <th class="col-suami">
                Nama Suami
            </th>

            <th class="col-metode">
                Metode KB
            </th>

            <th class="col-tensi">
                Tensi Darah
            </th>

            <th class="col-bb">
                BB
            </th>

            <th class="col-tb">
                TB
            </th>

            <th class="col-hasil">
                Hasil Pemeriksaan
            </th>

            <th class="col-efek">
                Efek Samping
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
                    $data['tanggal_pelayanan']
                ); ?>

            </td>


            <!-- NO PESERTA -->

            <td>

                <?= tampil(
                    $data['no_peserta_kb']
                ); ?>

            </td>


            <!-- NO KK -->

            <td>

                <?= tampil(
                    $data['no_kk']
                ); ?>

            </td>


            <!-- NAMA SUAMI -->

            <td class="text-left">

                <?= tampil(
                    $data['nama_suami']
                ); ?>

            </td>


            <!-- METODE KB -->

            <td>

                <?= tampil(
                    $data['metode_kb']
                ); ?>

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

                    echo ' mmHg';

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


            <!-- TINGGI BADAN -->

            <td>

                <?php

                if (
                    $data['tinggi_badan'] !== null &&
                    $data['tinggi_badan'] !== ''
                ) {

                    echo tampil(
                        $data['tinggi_badan']
                    );

                    echo ' cm';

                } else {

                    echo '-';

                }

                ?>

            </td>


            <!-- HASIL -->

            <td class="text-left">

                <?= tampil(
                    $data['hasil_pemeriksaan']
                ); ?>

            </td>


            <!-- EFEK SAMPING -->

            <td class="text-left">

                <?= tampil(
                    $data['efek_samping']
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

                Belum ada data pelayanan kebidanan.

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

        Jumlah Data Pelayanan :

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