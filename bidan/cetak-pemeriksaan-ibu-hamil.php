<?php

session_start();

require "../template/rbac.php";

// Hanya Bidan
cekAkses([ROLE_BIDAN]);

require "../config.php";


/*
|--------------------------------------------------------------------------
| CEK ID
|--------------------------------------------------------------------------
*/

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("ID ibu hamil tidak valid.");
}

$id = (int) $_GET['id'];


/*
|--------------------------------------------------------------------------
| AMBIL DATA IBU HAMIL
|--------------------------------------------------------------------------
*/

$queryIbu = mysqli_query(
    $koneksi,
    "SELECT *
     FROM tbl_ibu_hamil
     WHERE id = '$id'"
);

if (!$queryIbu) {
    die(
        "Query data ibu hamil gagal : " .
        mysqli_error($koneksi)
    );
}

if (mysqli_num_rows($queryIbu) == 0) {
    die("Data ibu hamil tidak ditemukan.");
}

$ibu = mysqli_fetch_assoc($queryIbu);


/*
|--------------------------------------------------------------------------
| NOMOR REGISTRASI
|--------------------------------------------------------------------------
*/

if (
    isset($ibu['no_registrasi']) &&
    !empty($ibu['no_registrasi'])
) {

    $no_registrasi = $ibu['no_registrasi'];

} else {

    $no_registrasi =
        "IBH-" .
        str_pad(
            $id,
            4,
            "0",
            STR_PAD_LEFT
        );

}


/*
|--------------------------------------------------------------------------
| DATA PEMERIKSAAN
|--------------------------------------------------------------------------
*/

$queryPemeriksaan = mysqli_query(
    $koneksi,
    "SELECT *
     FROM tbl_pemeriksaan_ibu_hamil
     WHERE ibu_hamil_id = '$id'
     ORDER BY tanggal_pemeriksaan ASC, id ASC"
);

if (!$queryPemeriksaan) {
    die(
        "Query pemeriksaan gagal : " .
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
| FUNGSI TANGGAL
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

?>

<!DOCTYPE html>

<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>
        Rekam Medis Pemeriksaan Ibu Hamil
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
           JUDUL
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
           IDENTITAS
        ===================================================== */

        .identitas {

            width: 100%;

            border-collapse: collapse;

            margin-bottom: 16px;

            table-layout: fixed;

        }


        .identitas td {

            padding: 4px 3px;

            vertical-align: top;

            line-height: 1.35;

            font-size: 13px;

        }


        .identitas .label {

            width: 15%;

            font-weight: bold;

            white-space: nowrap;

        }


        .identitas .titik {

            width: 2%;

            text-align: center;

            font-weight: bold;

        }


        .identitas .data {

            width: 33%;

            padding-right: 15px;

            word-wrap: break-word;

        }


        .identitas .label-kanan {

            width: 15%;

            font-weight: bold;

            white-space: nowrap;

        }


        .identitas .data-kanan {

            width: 33%;

            word-wrap: break-word;

        }


        /* =====================================================
           TABEL PEMERIKSAAN
        ===================================================== */

        .pemeriksaan {

            width: 100%;

            border-collapse: collapse;

            table-layout: fixed;

        }


        .pemeriksaan th,
        .pemeriksaan td {

            border: 1px solid #000;

            padding: 6px 4px;

            text-align: center;

            vertical-align: middle;

            font-size: 12px;

        }


        .pemeriksaan th {

            background: #eee;

            font-weight: bold;

        }


        .pemeriksaan th:nth-child(1) {

            width: 4%;

        }


        .pemeriksaan th:nth-child(2) {

            width: 11%;

        }


        .pemeriksaan th:nth-child(3) {

            width: 8%;

        }


        .pemeriksaan th:nth-child(4) {

            width: 8%;

        }


        .pemeriksaan th:nth-child(5) {

            width: 10%;

        }


        .pemeriksaan th:nth-child(6) {

            width: 8%;

        }


        .pemeriksaan th:nth-child(7) {

            width: 9%;

        }


        .pemeriksaan th:nth-child(8) {

            width: 21%;

        }


        .pemeriksaan th:nth-child(9) {

            width: 21%;

        }


        .pemeriksaan td.hasil,
        .pemeriksaan td.keterangan {

            text-align: left;

        }


        /* =====================================================
           FOOTER / TANDA TANGAN
        ===================================================== */

        .footer {

            margin-top: 30px;

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
     TOMBOL KEMBALI DAN CETAK
========================================================= -->

<div class="no-print">

    <!-- KEMBALI -->

    <a
        href="pemeriksaan-ibu-hamil.php?id=<?= $id; ?>"
        class="btn-back">

        ← Kembali

    </a>


    <!-- CETAK -->

    <button
        type="button"
        class="btn-print"
        onclick="window.print()">

        Cetak / Simpan PDF

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
        REKAM MEDIS PEMERIKSAAN IBU HAMIL
    </h3>

    <div class="subjudul">
        Riwayat Pemeriksaan Kehamilan
    </div>

</div>


<!-- =========================================================
     IDENTITAS PASIEN
========================================================= -->

<table class="identitas">

    <!-- BARIS 1 -->

    <tr>

        <td class="label">
            Nama Ibu
        </td>

        <td class="titik">
            :
        </td>

        <td class="data">

            <?= tampil(
                $ibu['nama_ibu'] ?? null
            ); ?>

        </td>


        <td class="label-kanan">
            Nama Suami
        </td>

        <td class="titik">
            :
        </td>

        <td class="data-kanan">

            <?= tampil(
                $ibu['nama_suami'] ?? null
            ); ?>

        </td>

    </tr>


    <!-- BARIS 2 -->

    <tr>

        <td class="label">
            TTL Ibu / NIK
        </td>

        <td class="titik">
            :
        </td>

        <td class="data">

            <?= tampil(
                $ibu['tempat_lahir'] ?? null
            ); ?>

            /

            <?= tanggalIndonesia(
                $ibu['tgl_lahir'] ?? null
            ); ?>

            /

            <?= tampil(
                $ibu['nik'] ?? null
            ); ?>

        </td>


        <td class="label-kanan">
            TTL Suami / NIK
        </td>

        <td class="titik">
            :
        </td>

        <td class="data-kanan">

            <?= !empty(
                $ibu['tempat_lahir_suami'] ?? null
            )
                ? tampil(
                    $ibu['tempat_lahir_suami']
                )
                : '-';
            ?>

            /

            <?= tanggalIndonesia(
                $ibu['tgl_lahir_suami'] ?? null
            ); ?>

            /

            <?= tampil(
                $ibu['nik_suami'] ?? null
            ); ?>

        </td>

    </tr>


    <!-- BARIS 3 -->

    <tr>

        <td class="label">
            No. KK
        </td>

        <td class="titik">
            :
        </td>

        <td class="data">

            <?= tampil(
                $ibu['no_kk'] ?? null
            ); ?>

        </td>


        <td class="label-kanan">
            Reg
        </td>

        <td class="titik">
            :
        </td>

        <td class="data-kanan">

            <?= tampil(
                $no_registrasi
            ); ?>

        </td>

    </tr>


    <!-- BARIS 4 -->

    <tr>

        <td class="label">
            TB / BB Sebelum Hamil
        </td>

        <td class="titik">
            :
        </td>

        <td class="data">

            <?php

            $tb = !empty(
                $ibu['tb'] ?? null
            )
                ? tampil(
                    $ibu['tb']
                ) . ' cm'
                : '-';

            $bbSebelumHamil = !empty(
                $ibu['bb_sebelum_hamil'] ?? null
            )
                ? tampil(
                    $ibu['bb_sebelum_hamil']
                ) . ' Kg'
                : '-';

            ?>

            <?= $tb; ?>

            /

            <?= $bbSebelumHamil; ?>

        </td>


        <td class="label-kanan">
            HB
        </td>

        <td class="titik">
            :
        </td>

        <td class="data-kanan">

            <?= !empty(
                $ibu['hb'] ?? null
            )
                ? tampil(
                    $ibu['hb']
                ) . ' g/dL'
                : '-';
            ?>

        </td>

    </tr>


    <!-- BARIS 5 -->

    <tr>

        <td class="label">
            HPHT / TP
        </td>

        <td class="titik">
            :
        </td>

        <td class="data">

            <?= tanggalIndonesia(
                $ibu['hpht'] ?? null
            ); ?>

            /

            <?= tanggalIndonesia(
                $ibu['hpl'] ?? null
            ); ?>

        </td>


        <td class="label-kanan">
            No. HP
        </td>

        <td class="titik">
            :
        </td>

        <td class="data-kanan">

            <?= tampil(
                $ibu['no_hp'] ?? null
            ); ?>

        </td>

    </tr>


    <!-- BARIS 6 -->

    <tr>

        <td class="label">
            Alamat
        </td>

        <td class="titik">
            :
        </td>

        <td class="data">

            <?= tampil(
                $ibu['alamat'] ?? null
            ); ?>

        </td>


        <td class="label-kanan">
            BPJS / KIS
        </td>

        <td class="titik">
            :
        </td>

        <td class="data-kanan">

            <?= tampil(
                $ibu['bpjs'] ?? null
            ); ?>

        </td>

    </tr>


    <!-- BARIS 7 -->

    <tr>

        <td class="label">
            Pendidikan Ibu / Suami
        </td>

        <td class="titik">
            :
        </td>

        <td class="data">

            <?= tampil(
                $ibu['pendidikan_ibu'] ?? null
            ); ?>

            /

            <?= tampil(
                $ibu['pendidikan_suami'] ?? null
            ); ?>

        </td>


        <td class="label-kanan">
            G / P / A
        </td>

        <td class="titik">
            :
        </td>

        <td class="data-kanan">

            <?= tampil(
                $ibu['gravida'] ?? '0'
            ); ?>

            /

            <?= tampil(
                $ibu['para'] ?? '0'
            ); ?>

            /

            <?= tampil(
                $ibu['abortus'] ?? '0'
            ); ?>

        </td>

    </tr>


    <!-- BARIS 8 -->

    <tr>

        <td class="label">
            HBsAg / HIV / Sifilis
        </td>

        <td class="titik">
            :
        </td>

        <td class="data">

            <?= tampil(
                $ibu['hbsag'] ?? null
            ); ?>

            /

            <?= tampil(
                $ibu['hiv'] ?? null
            ); ?>

            /

            <?= tampil(
                $ibu['sifilis'] ?? null
            ); ?>

        </td>


        <td class="label-kanan">
        </td>

        <td class="titik">
        </td>

        <td class="data-kanan">
        </td>

    </tr>


</table>


<!-- =========================================================
     TABEL RIWAYAT PEMERIKSAAN
========================================================= -->

<table class="pemeriksaan">

    <thead>

        <tr>

            <th>
                No
            </th>

            <th>
                Tanggal
            </th>

            <th>
                UK
            </th>

            <th>
                BB
            </th>

            <th>
                TD
            </th>

            <th>
                TFU
            </th>

            <th>
                DJJ
            </th>

            <th>
                Hasil
            </th>

            <th>
                Keterangan
            </th>

        </tr>

    </thead>


    <tbody>

    <?php

    $no = 1;

    if (
        mysqli_num_rows(
            $queryPemeriksaan
        ) > 0
    ) :

        while (
            $p = mysqli_fetch_assoc(
                $queryPemeriksaan
            )
        ) :

    ?>

        <tr>

            <!-- NO -->

            <td>

                <?= $no++; ?>

            </td>


            <!-- TANGGAL -->

            <td>

                <?= tanggalIndonesia(
                    $p['tanggal_pemeriksaan']
                    ?? null
                ); ?>

            </td>


            <!-- UK -->

            <td>

                <?= tampil(
                    $p['usia_kehamilan']
                    ?? null
                ); ?>

                minggu

            </td>


            <!-- BB -->

            <td>

                <?= tampil(
                    $p['berat_badan']
                    ?? null
                ); ?>

                Kg

            </td>


            <!-- TD -->

            <td>

                <?= tampil(
                    $p['tekanan_darah']
                    ?? null
                ); ?>

            </td>


            <!-- TFU -->

            <td>

                <?= tampil(
                    $p['tfu']
                    ?? null
                ); ?>

                cm

            </td>


            <!-- DJJ -->

            <td>

                <?= tampil(
                    $p['djj']
                    ?? null
                ); ?>

                x/menit

            </td>


            <!-- HASIL -->

            <td class="hasil">

                <?= tampil(
                    $p['hasil']
                    ?? null
                ); ?>

            </td>


            <!-- KETERANGAN -->

            <td class="keterangan">

                <?= tampil(
                    $p['keterangan']
                    ?? null
                ); ?>

            </td>

        </tr>


    <?php

        endwhile;

    else :

    ?>

        <tr>

            <td colspan="9">

                Belum ada data pemeriksaan.

            </td>

        </tr>

    <?php

    endif;

    ?>

    </tbody>

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