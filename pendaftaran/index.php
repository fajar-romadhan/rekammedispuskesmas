<?php

session_start();

require "../template/rbac.php";

// =========================================================
// ROLE YANG BOLEH MENDAFTARKAN PASIEN
// =========================================================

cekAkses([ROLE_ADMIN, ROLE_PETUGAS]);

require "../config.php";


// =========================================================
// REDIRECT PENDAFTARAN BIDAN
// =========================================================

if (($_GET['jenis'] ?? '') === 'bidan') {

    header("location: ../petugas/pendaftaran-kebidanan.php");
    exit();

}


$title = "Daftarkan Pasien Umum - rekammedispuskesmas";

require "../template/header.php";
require "../template/navbar.php";
require "../template/sidebar.php";


// =========================================================
// TANGGAL HARI INI
// =========================================================

$tanggalHariIni = date('Y-m-d');


// =========================================================
// FUNGSI CEK KOLOM
// =========================================================

function kolomAda($koneksi, $tabel, $kolom)
{
    $tabel = mysqli_real_escape_string($koneksi, $tabel);
    $kolom = mysqli_real_escape_string($koneksi, $kolom);

    $query = mysqli_query(
        $koneksi,
        "SHOW COLUMNS FROM `$tabel` LIKE '$kolom'"
    );

    return ($query && mysqli_num_rows($query) > 0);
}


// =========================================================
// CARI KOLOM ID PASIEN DI TBL_ANTRIAN
// =========================================================

$kolomIdPasien = null;

$calonIdPasien = [
    'id_pasien',
    'id',
    'pasien_id'
];

foreach ($calonIdPasien as $kolom) {

    if (kolomAda($koneksi, 'tbl_antrian', $kolom)) {

        $kolomIdPasien = $kolom;
        break;

    }

}


// =========================================================
// CARI KOLOM TANGGAL PENDAFTARAN / ANTRIAN
// =========================================================

$kolomTanggal = null;

$calonTanggal = [
    'tgl_antrian',
    'tanggal_antrian',
    'tgl_daftar',
    'tanggal_daftar',
    'tgl_pendaftaran',
    'tanggal_pendaftaran',
    'tgl',
    'tanggal',
    'tgl_periksa',
    'tanggal_periksa',
    'created_at'
];

foreach ($calonTanggal as $kolom) {

    if (kolomAda($koneksi, 'tbl_antrian', $kolom)) {

        $kolomTanggal = $kolom;
        break;

    }

}


// =========================================================
// CARI KOLOM ID ANTRIAN UNTUK URUTAN
// =========================================================

$kolomIdAntrian = null;

$calonIdAntrian = [
    'id_antrian',
    'id'
];

foreach ($calonIdAntrian as $kolom) {

    if (kolomAda($koneksi, 'tbl_antrian', $kolom)) {

        $kolomIdAntrian = $kolom;
        break;

    }

}


// =========================================================
// QUERY PASIEN HARI INI
// =========================================================

$queryHariIni = false;

$jumlahHariIni = 0;


// ---------------------------------------------------------
// HANYA JALANKAN QUERY JIKA KOLOM YANG DIBUTUHKAN ADA
// ---------------------------------------------------------

if ($kolomIdPasien !== null && $kolomTanggal !== null) {


    /*
     * Kita hanya mengambil data dari tbl_pasien.
     *
     * DISTINCT digunakan supaya kalau seorang pasien
     * mempunyai data antrian lebih dari satu pada hari yang
     * sama, pasien tidak muncul berkali-kali.
     */

    $orderBy = "p.id DESC";

    if ($kolomIdAntrian !== null) {

        $orderBy = "a.`$kolomIdAntrian` DESC";

    }


    $sqlHariIni = "
        SELECT DISTINCT
            p.id,
            p.nama,
            p.tgl_lahir,
            p.gender,
            p.telpon,
            p.alamat

        FROM tbl_antrian a

        INNER JOIN tbl_pasien p
            ON p.id = a.`$kolomIdPasien`

        WHERE DATE(a.`$kolomTanggal`) = '$tanggalHariIni'

        ORDER BY $orderBy
    ";


    $queryHariIni = mysqli_query(
        $koneksi,
        $sqlHariIni
    );


    if ($queryHariIni) {

        $jumlahHariIni = mysqli_num_rows($queryHariIni);

    }

}

?>


<style>

/* =========================================================
   HEADER HALAMAN
========================================================= */

.page-header {

    padding-top: 20px;

    padding-bottom: 15px;

    margin-bottom: 20px;

    border-bottom: 1px solid #e9e9ef;

}


.page-title {

    font-size: 25px;

    font-weight: 600;

    color: #343540;

    margin: 0;

}


.page-title i {

    margin-right: 10px;

    color: #7571f9;

}


/* =========================================================
   INFO
========================================================= */

.info-alert {

    background: #f0f0ff;

    border: 1px solid #dedcff;

    color: #494a57;

    border-radius: 8px;

    padding: 12px 15px;

    margin-bottom: 25px;

}


/* =========================================================
   CARD
========================================================= */

.section-card {

    background: #ffffff;

    border-radius: 12px;

    box-shadow: 0 4px 18px rgba(0,0,0,.07);

    border: 1px solid #f0f0f0;

    margin-bottom: 25px;

    overflow: hidden;

}


/* =========================================================
   HEADER CARD
========================================================= */

.section-header {

    padding: 17px 20px;

    border-bottom: 1px solid #eeeeF2;

    display: flex;

    align-items: center;

    justify-content: space-between;

}


.section-title {

    margin: 0;

    font-size: 17px;

    font-weight: 600;

    color: #343540;

}


.section-title i {

    color: #7571f9;

    margin-right: 7px;

}


.total-badge {

    background: #eeeeff;

    color: #7571f9;

    padding: 6px 12px;

    border-radius: 20px;

    font-size: 12px;

    font-weight: 600;

}


/* =========================================================
   CARD BODY
========================================================= */

.section-body {

    padding: 20px;

}


/* =========================================================
   SEARCH
========================================================= */

.search-wrapper {

    max-width: 500px;

    margin-bottom: 20px;

}


.search-wrapper .input-group {

    background: #ffffff;

    border-radius: 10px;

    overflow: hidden;

    box-shadow: 0 3px 12px rgba(0,0,0,.06);

}


.search-wrapper .input-group-text {

    background: #ffffff;

    border: none;

    padding-left: 15px;

    color: #6c757d;

}


.search-wrapper .form-control {

    border: none;

    padding: 11px 12px;

    box-shadow: none;

}


.search-wrapper .form-control:focus {

    box-shadow: none;

}


/* =========================================================
   TABLE CONTAINER
========================================================= */

.table-container {

    background: #ffffff;

    overflow-x: auto;

}


/* =========================================================
   TABLE
========================================================= */

.patient-table {

    margin-bottom: 0;

    vertical-align: middle;

    min-width: 900px;

}


.patient-table thead th {

    background: #f8f8fa;

    color: #494a57;

    font-size: 13px;

    font-weight: 600;

    text-transform: uppercase;

    letter-spacing: .2px;

    padding: 14px 12px;

    border-bottom: 1px solid #dedfe6;

    white-space: nowrap;

}


.patient-table tbody td {

    padding: 13px 12px;

    font-size: 14px;

    color: #494a57;

    border-bottom: 1px solid #f0f0f0;

}


.patient-table tbody tr {

    transition: .15s ease;

}


.patient-table tbody tr:hover {

    background: #f8f8ff;

}


.patient-table tbody tr:last-child td {

    border-bottom: none;

}


/* =========================================================
   ID PASIEN
========================================================= */

.patient-id {

    display: inline-block;

    background: #eeeeff;

    color: #7571f9;

    padding: 5px 9px;

    border-radius: 6px;

    font-size: 12px;

    font-weight: 600;

}


/* =========================================================
   NAMA
========================================================= */

.patient-name {

    font-weight: 600;

    color: #343540;

}


/* =========================================================
   JENIS KELAMIN
========================================================= */

.gender-badge {

    display: inline-block;

    padding: 5px 10px;

    border-radius: 20px;

    background: #f1f1f5;

    color: #494a57;

    font-size: 12px;

    font-weight: 500;

}


/* =========================================================
   TELEPON
========================================================= */

.phone-number {

    white-space: nowrap;

    color: #494a57;

}


.phone-number i {

    margin-right: 5px;

    color: #6c757d;

}


/* =========================================================
   ALAMAT
========================================================= */

.patient-address {

    max-width: 220px;

    white-space: nowrap;

    overflow: hidden;

    text-overflow: ellipsis;

}


/* =========================================================
   STATUS
========================================================= */

.status-today {

    display: inline-block;

    padding: 6px 10px;

    border-radius: 20px;

    background: #e8f7ee;

    color: #198754;

    font-size: 12px;

    font-weight: 600;

    white-space: nowrap;

}


/* =========================================================
   ACTION
========================================================= */

.action-buttons {

    display: flex;

    gap: 5px;

    justify-content: center;

    white-space: nowrap;

}


.action-buttons .btn {

    height: 34px;

    padding: 0 12px;

    display: flex;

    align-items: center;

    justify-content: center;

    border-radius: 7px;

    transition: all .2s ease;

    font-size: 13px;

}


.action-buttons .btn i {

    margin-right: 5px;

}


.action-buttons .btn:hover {

    transform: translateY(-1px);

}


/* =========================================================
   EMPTY STATE
========================================================= */

.empty-state {

    text-align: center;

    padding: 35px 20px;

    color: #777985;

}


.empty-state i {

    display: block;

    font-size: 36px;

    color: #bcbcc9;

    margin-bottom: 10px;

}


.empty-state strong {

    display: block;

    color: #555664;

    margin-bottom: 5px;

}


/* =========================================================
   ERROR / INFO DATABASE
========================================================= */

.database-info {

    padding: 14px 18px;

    margin: 0 20px 20px 20px;

    background: #fff8e8;

    border: 1px solid #f5dfaa;

    color: #725b22;

    border-radius: 8px;

    font-size: 13px;

}


/* =========================================================
   RESPONSIVE
========================================================= */

@media (max-width: 768px) {

    .page-title {

        font-size: 21px;

    }


    .section-header {

        align-items: flex-start;

        gap: 10px;

    }


    .search-wrapper {

        max-width: 100%;

    }


    .table-container {

        overflow-x: auto;

    }

}

</style>


<div class="page-content-wrap">


    <!-- =====================================================
         HEADER
    ====================================================== -->

    <div class="page-header">

        <h1 class="page-title">

            <i class="bi bi-person-check-fill"></i>

            Daftarkan Pasien Umum

        </h1>

    </div>


    <!-- =====================================================
         INFO
    ====================================================== -->

    <div class="info-alert">

        <i class="bi bi-info-circle me-1"></i>

        Pasien yang didaftarkan di sini akan masuk ke antrian
        <strong>Poli Umum (Dokter)</strong> hari ini.

    </div>



    <!-- =====================================================
         =====================================================
         KOTAK 1
         PASIEN BEROBAT HARI INI
         =====================================================
    ====================================================== -->

    <div class="section-card">


        <div class="section-header">


            <h2 class="section-title">

                <i class="bi bi-calendar-check-fill"></i>

                Pasien Berobat Hari Ini

            </h2>


            <span class="total-badge">

                <?= $jumlahHariIni; ?> Pasien

            </span>


        </div>



        <?php

        /*
         * Jika struktur tbl_antrian belum mempunyai kolom yang
         * diperlukan, jangan tampilkan error PHP.
         */

        if ($kolomIdPasien === null || $kolomTanggal === null) {

        ?>

            <div class="database-info">

                <i class="bi bi-info-circle me-1"></i>

                Data pasien hari ini belum dapat ditampilkan
                karena struktur tabel antrian belum mempunyai
                kolom yang sesuai untuk membaca tanggal pendaftaran.

            </div>

        <?php

        }

        ?>



        <div class="table-container">


            <table class="table patient-table">


                <thead>

                    <tr>

                        <th>No</th>

                        <th>ID Pasien</th>

                        <th>Nama</th>

                        <th>Tgl Lahir</th>

                        <th>Jenis Kelamin</th>

                        <th>Telpon</th>

                        <th>Alamat</th>

                        <th>Status</th>

                    </tr>

                </thead>



                <tbody>


                <?php

                if (

                    $queryHariIni &&

                    mysqli_num_rows($queryHariIni) > 0

                ) {


                    $noHariIni = 1;


                    while (

                        $pasienHariIni = mysqli_fetch_assoc(
                            $queryHariIni
                        )

                    ) {

                ?>


                    <tr>


                        <!-- NO -->

                        <td>

                            <?= $noHariIni++; ?>

                        </td>


                        <!-- ID PASIEN -->

                        <td>

                            <span class="patient-id">

                                <?= htmlspecialchars(
                                    $pasienHariIni['id']
                                ); ?>

                            </span>

                        </td>


                        <!-- NAMA -->

                        <td>

                            <span class="patient-name">

                                <?= htmlspecialchars(
                                    $pasienHariIni['nama']
                                ); ?>

                            </span>

                        </td>


                        <!-- TGL LAHIR -->

                        <td>

                            <?= in_date(
                                $pasienHariIni['tgl_lahir']
                            ); ?>

                        </td>


                        <!-- JENIS KELAMIN -->

                        <td>

                            <span class="gender-badge">

                                <?php

                                if (
                                    $pasienHariIni['gender'] == 'P'
                                ) {

                                    echo 'Pria';

                                } else {

                                    echo 'Wanita';

                                }

                                ?>

                            </span>

                        </td>


                        <!-- TELEPON -->

                        <td>

                            <span class="phone-number">

                                <i class="bi bi-telephone"></i>

                                <?= htmlspecialchars(
                                    $pasienHariIni['telpon']
                                ); ?>

                            </span>

                        </td>


                        <!-- ALAMAT -->

                        <td>

                            <div
                                class="patient-address"
                                title="<?= htmlspecialchars(
                                    $pasienHariIni['alamat']
                                ); ?>"
                            >

                                <?= htmlspecialchars(
                                    $pasienHariIni['alamat']
                                ); ?>

                            </div>

                        </td>


                        <!-- STATUS -->

                        <td>

                            <span class="status-today">

                                <i class="bi bi-check-circle-fill me-1"></i>

                                Terdaftar Hari Ini

                            </span>

                        </td>


                    </tr>


                <?php

                    }


                } else {

                ?>


                    <tr>

                        <td colspan="8">


                            <div class="empty-state">


                                <i class="bi bi-calendar-x"></i>


                                <strong>
                                    Belum Ada Pasien Berobat Hari Ini
                                </strong>


                                Pasien yang sudah didaftarkan untuk
                                berobat hari ini akan muncul di sini.


                            </div>


                        </td>

                    </tr>


                <?php

                }

                ?>


                </tbody>


            </table>


        </div>

    </div>



    <!-- =====================================================
         =====================================================
         KOTAK 2
         SEMUA DATA PASIEN
         =====================================================
    ====================================================== -->

    <div class="section-card">


        <!-- HEADER -->

        <div class="section-header">


            <h2 class="section-title">

                <i class="bi bi-people-fill"></i>

                Semua Data Pasien

            </h2>


        </div>



        <!-- BODY -->

        <div class="section-body">


            <!-- SEARCH -->

            <div class="search-wrapper">


                <div class="input-group">


                    <span class="input-group-text">

                        <i class="bi bi-search"></i>

                    </span>


                    <input
                        type="text"
                        id="searchPasien"
                        class="form-control"
                        placeholder="Cari nama pasien..."
                        autocomplete="off"
                    >


                </div>


            </div>


        </div>



        <!-- TABLE -->

        <div class="table-container">


            <table
                class="table patient-table"
                id="myTable"
            >


                <thead>

                    <tr>

                        <th>No</th>

                        <th>ID Pasien</th>

                        <th>Nama</th>

                        <th>Tgl Lahir</th>

                        <th>Jenis Kelamin</th>

                        <th>Telpon</th>

                        <th>Alamat</th>

                        <th class="text-center">

                            Aksi

                        </th>

                    </tr>

                </thead>



                <tbody>


                <?php

                $no = 1;


                $queryPasien = mysqli_query(

                    $koneksi,

                    "
                    SELECT *
                    FROM tbl_pasien
                    ORDER BY id DESC
                    "

                );


                if (

                    $queryPasien &&

                    mysqli_num_rows($queryPasien) > 0

                ) {


                    while (

                        $pasien = mysqli_fetch_assoc(
                            $queryPasien
                        )

                    ) {

                ?>


                    <tr>


                        <!-- NO -->

                        <td>

                            <?= $no++; ?>

                        </td>


                        <!-- ID PASIEN -->

                        <td>

                            <span class="patient-id">

                                <?= htmlspecialchars(
                                    $pasien['id']
                                ); ?>

                            </span>

                        </td>


                        <!-- NAMA -->

                        <td>

                            <span class="patient-name">

                                <?= htmlspecialchars(
                                    $pasien['nama']
                                ); ?>

                            </span>

                        </td>


                        <!-- TGL LAHIR -->

                        <td>

                            <?= in_date(
                                $pasien['tgl_lahir']
                            ); ?>

                        </td>


                        <!-- JENIS KELAMIN -->

                        <td>

                            <span class="gender-badge">

                                <?php

                                if ($pasien['gender'] == 'P') {

                                    echo 'Pria';

                                } else {

                                    echo 'Wanita';

                                }

                                ?>

                            </span>

                        </td>


                        <!-- TELEPON -->

                        <td>

                            <span class="phone-number">

                                <i class="bi bi-telephone"></i>

                                <?= htmlspecialchars(
                                    $pasien['telpon']
                                ); ?>

                            </span>

                        </td>


                        <!-- ALAMAT -->

                        <td>

                            <div
                                class="patient-address"
                                title="<?= htmlspecialchars(
                                    $pasien['alamat']
                                ); ?>"
                            >

                                <?= htmlspecialchars(
                                    $pasien['alamat']
                                ); ?>

                            </div>

                        </td>


                        <!-- AKSI -->

                        <td>


                            <div class="action-buttons">


                                <a
                                    href="pasien-lama.php?id=<?= $pasien['id']; ?>"
                                    class="btn btn-sm btn-primary"
                                    title="Daftarkan pasien"
                                >

                                    <i class="bi bi-person-check"></i>

                                    Daftarkan

                                </a>


                            </div>


                        </td>


                    </tr>


                <?php

                    }


                } else {

                ?>


                    <tr>

                        <td colspan="8">


                            <div class="empty-state">


                                <i class="bi bi-people"></i>


                                <strong>
                                    Belum Ada Data Pasien
                                </strong>


                                Belum terdapat data pasien.


                            </div>


                        </td>

                    </tr>


                <?php

                }


                ?>


                </tbody>


            </table>


        </div>


    </div>


</div>



<script>

$(document).ready(function () {


    // =====================================================
    // SEARCH NAMA PASIEN
    // =====================================================

    $('#searchPasien').on('keyup', function () {


        var keyword = $(this)
            .val()
            .toLowerCase()
            .trim();


        $('#myTable tbody tr').each(function () {


            var nama = $(this)
                .find('td:eq(2)')
                .text()
                .toLowerCase();


            if (nama.indexOf(keyword) !== -1) {

                $(this).show();

            } else {

                $(this).hide();

            }


        });


    });


});

</script>



<?php

require "../template/footer.php";

?>