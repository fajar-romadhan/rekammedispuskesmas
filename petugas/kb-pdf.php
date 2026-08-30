<?php

session_start();

require "../template/rbac.php";

// Hanya Petugas
cekAkses([ROLE_PETUGAS]);

require "../config.php";


/*
|--------------------------------------------------------------------------
| CEK ID
|--------------------------------------------------------------------------
*/

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("ID peserta KB tidak valid.");
}

$id = (int) $_GET['id'];


/*
|--------------------------------------------------------------------------
| AMBIL DATA PESERTA KB
|--------------------------------------------------------------------------
*/

$queryKB = mysqli_query(
    $koneksi,
    "SELECT *
     FROM tbl_kb
     WHERE id_kb = '$id'"
);

if (!$queryKB) {
    die(
        "Query data peserta KB gagal : " .
        mysqli_error($koneksi)
    );
}

if (mysqli_num_rows($queryKB) == 0) {
    die("Data peserta KB tidak ditemukan.");
}

$kb = mysqli_fetch_assoc($queryKB);


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
| DATA
|--------------------------------------------------------------------------
*/

$tanggal = tanggalIndonesia(
    $kb['tanggal'] ?? null
);

$tanggal_lahir = tanggalIndonesia(
    $kb['tanggal_lahir'] ?? null
);

?>

<!DOCTYPE html>

<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>
        Register Peserta KB
    </title>


    <style>

        /* =====================================================
           HALAMAN
        ===================================================== */

        @page {

            size: A4 portrait;

            margin: 15mm;

        }


        * {

            box-sizing: border-box;

        }


        body {

            font-family: Arial, Helvetica, sans-serif;

            font-size: 13px;

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


        .btn-print {

            display: inline-block;

            padding: 9px 18px;

            border: 1px solid #000;

            background: #000;

            color: #fff;

            cursor: pointer;

            font-size: 13px;

            border-radius: 3px;

        }


        .btn-print:hover {

            background: #333;

        }


        .btn-back {

            display: inline-block;

            padding: 9px 18px;

            border: 1px solid #000;

            background: #fff;

            color: #000;

            text-decoration: none;

            cursor: pointer;

            font-size: 13px;

            border-radius: 3px;

        }


        .btn-back:hover {

            background: #eee;

            color: #000;

        }


        /* =====================================================
           JUDUL / KOP
        ===================================================== */

        .judul {

            text-align: center;

            margin-bottom: 20px;

            border-bottom: 2px solid #000;

            padding-bottom: 12px;

        }


        .judul h2 {

            margin: 0;

            font-size: 21px;

            font-weight: bold;

        }


        .judul h3 {

            margin: 5px 0;

            font-size: 17px;

            font-weight: bold;

        }


        .judul .subjudul {

            margin-top: 5px;

            font-size: 13px;

        }


        /* =====================================================
           IDENTITAS
        ===================================================== */

        .identitas {

            width: 100%;

            border-collapse: collapse;

            margin-bottom: 18px;

            table-layout: fixed;

        }


        .identitas td {

            padding: 5px 3px;

            vertical-align: top;

            line-height: 1.4;

            font-size: 13px;

        }


        .identitas .label {

            width: 18%;

            font-weight: bold;

            white-space: nowrap;

        }


        .identitas .titik {

            width: 2%;

            text-align: center;

            font-weight: bold;

        }


        .identitas .data {

            width: 30%;

            padding-right: 15px;

            word-wrap: break-word;

        }


        .identitas .label-kanan {

            width: 18%;

            font-weight: bold;

            white-space: nowrap;

        }


        .identitas .data-kanan {

            width: 32%;

            word-wrap: break-word;

        }


        /* =====================================================
           TABEL DATA PELAYANAN
        ===================================================== */

        .pelayanan {

            width: 100%;

            border-collapse: collapse;

            table-layout: fixed;

        }


        .pelayanan th,
        .pelayanan td {

            border: 1px solid #000;

            padding: 7px 5px;

            vertical-align: middle;

            font-size: 12px;

        }


        .pelayanan th {

            background: #eee;

            font-weight: bold;

            text-align: center;

        }


        .pelayanan td {

            text-align: center;

        }


        .pelayanan td.hasil,
        .pelayanan td.keterangan {

            text-align: left;

        }


        .pelayanan th:nth-child(1) {

            width: 6%;

        }


        .pelayanan th:nth-child(2) {

            width: 13%;

        }


        .pelayanan th:nth-child(3) {

            width: 14%;

        }


        .pelayanan th:nth-child(4) {

            width: 13%;

        }


        .pelayanan th:nth-child(5) {

            width: 14%;

        }


        .pelayanan th:nth-child(6) {

            width: 10%;

        }


        .pelayanan th:nth-child(7) {

            width: 30%;

        }


        /* =====================================================
           KETERANGAN
        ===================================================== */

        .keterangan-box {

            margin-top: 18px;

            width: 100%;

            border-collapse: collapse;

        }


        .keterangan-box td {

            border: 1px solid #000;

            padding: 8px;

            vertical-align: top;

        }


        .keterangan-box .judul-keterangan {

            width: 20%;

            font-weight: bold;

            background: #eee;

        }


        /* =====================================================
           FOOTER / TANDA TANGAN
        ===================================================== */

        .footer {

            margin-top: 35px;

            width: 100%;

        }


        .ttd {

            width: 250px;

            float: right;

            text-align: center;

            font-size: 13px;

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

                font-size: 13px;

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
        class="btn-back">

        ← Kembali

    </a>


    <button
        type="button"
        class="btn-print"
        onclick="window.print()">

        Cetak / Simpan PDF

    </button>

</div>


<!-- =========================================================
     KOP / JUDUL
========================================================= -->

<div class="judul">

    <h2>
        PUSKESMAS DESA MENDIS
    </h2>

    <h3>
        REGISTER PESERTA KB
    </h3>

    <div class="subjudul">
        Data Pelayanan Keluarga Berencana
    </div>

</div>


<!-- =========================================================
     IDENTITAS PESERTA
========================================================= -->

<table class="identitas">

    <!-- BARIS 1 -->

    <tr>

        <td class="label">
            Tanggal
        </td>

        <td class="titik">
            :
        </td>

        <td class="data">

            <?= $tanggal; ?>

        </td>


        <td class="label-kanan">
            No. Peserta KB
        </td>

        <td class="titik">
            :
        </td>

        <td class="data-kanan">

            <?= tampil(
                $kb['no_peserta_kb'] ?? null
            ); ?>

        </td>

    </tr>


    <!-- BARIS 2 -->

    <tr>

        <td class="label">
            No. KK
        </td>

        <td class="titik">
            :
        </td>

        <td class="data">

            <?= tampil(
                $kb['no_kk'] ?? null
            ); ?>

        </td>


        <td class="label-kanan">
            Tanggal Lahir
        </td>

        <td class="titik">
            :
        </td>

        <td class="data-kanan">

            <?= $tanggal_lahir; ?>

        </td>

    </tr>


    <!-- BARIS 3 -->

    <tr>

        <td class="label">
            Nama Suami
        </td>

        <td class="titik">
            :
        </td>

        <td class="data">

            <?= tampil(
                $kb['nama_suami'] ?? null
            ); ?>

        </td>


        <td class="label-kanan">
            Jumlah Anak
        </td>

        <td class="titik">
            :
        </td>

        <td class="data-kanan">

            <?= tampil(
                $kb['jumlah_anak'] ?? null
            ); ?>

            Orang

        </td>

    </tr>


    <!-- BARIS 4 -->

    <tr>

        <td class="label">
            Alamat
        </td>

        <td class="titik">
            :
        </td>

        <td class="data" colspan="4">

            <?= tampil(
                $kb['alamat'] ?? null
            ); ?>

        </td>

    </tr>


</table>


<!-- =========================================================
     DATA PELAYANAN KB
========================================================= -->

<table class="pelayanan">

    <thead>

        <tr>

            <th>
                No
            </th>

            <th>
                Jenis KB
            </th>

            <th>
                Kunjungan
            </th>

            <th>
                Tensi Darah
            </th>

            <th>
                BB
            </th>

            <th>
                Tanggal
            </th>

            <th>
                Keterangan
            </th>

        </tr>

    </thead>


    <tbody>

        <tr>

            <td>
                1
            </td>

            <td>
                <?= tampil(
                    $kb['jenis_kb'] ?? null
                ); ?>
            </td>

            <td>
                <?= tampil(
                    $kb['kunjungan'] ?? null
                ); ?>
            </td>

            <td>
                <?= tampil(
                    $kb['tekanan_darah'] ?? null
                ); ?>
            </td>

            <td>
                <?php

                if (
                    !empty(
                        $kb['bb'] ?? null
                    )
                ) {

                    echo tampil(
                        $kb['bb']
                    ) . " Kg";

                } else {

                    echo "-";

                }

                ?>
            </td>

            <td>
                <?= $tanggal; ?>
            </td>

            <td class="keterangan">

                <?= tampil(
                    $kb['keterangan'] ?? null
                ); ?>

            </td>

        </tr>

    </tbody>

</table>


<!-- =========================================================
     HASIL PEMERIKSAAN
========================================================= -->

<table class="keterangan-box">

    <tr>

        <td class="judul-keterangan">
            Hasil Pemeriksaan
        </td>

        <td>

            Tensi Darah :
            <?= tampil(
                $kb['tekanan_darah'] ?? null
            ); ?>

            &nbsp;&nbsp;&nbsp;

            BB :
            <?php

            if (
                !empty(
                    $kb['bb'] ?? null
                )
            ) {

                echo tampil(
                    $kb['bb']
                ) . " Kg";

            } else {

                echo "-";

            }

            ?>

        </td>

    </tr>


    <tr>

        <td class="judul-keterangan">
            Keterangan
        </td>

        <td>

            <?= tampil(
                $kb['keterangan'] ?? null
            ); ?>

        </td>

    </tr>

</table>


<!-- =========================================================
     TANDA TANGAN
========================================================= -->

<div class="footer">

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