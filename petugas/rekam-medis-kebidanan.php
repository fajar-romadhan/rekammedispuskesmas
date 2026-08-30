<?php

session_start();

require "../template/rbac.php";

// Hanya Petugas
cekAkses([ROLE_PETUGAS]);

require "../config.php";

$title = "Rekam Medis Kebidanan - Rekam Medis Puskesmas";

require "../template/header.php";
require "../template/navbar.php";
require "../template/sidebar.php";


/* =========================================================
   AMBIL DATA IBU HAMIL
========================================================= */

$query = mysqli_query(
    $koneksi,
    "SELECT * FROM tbl_ibu_hamil ORDER BY id DESC"
);

?>

<style>

/* =========================================================
   REKAM MEDIS KEBIDANAN
========================================================= */

.kebidanan-page {
    padding-top: 18px;
    padding-bottom: 45px;
}


/* =========================================================
   HEADER
========================================================= */

.kebidanan-header {
    background: linear-gradient(
        135deg,
        #ffffff 0%,
        #f8f8ff 100%
    );

    border: 1px solid #e5e5f0;
    border-radius: 20px;

    padding: 24px 28px;
    margin-bottom: 24px;

    box-shadow:
        0 5px 18px rgba(15, 23, 42, 0.05);

    display: flex;
    align-items: center;
    justify-content: space-between;

    gap: 20px;
}


/* BAGIAN JUDUL */

.kebidanan-title-area {
    display: flex;
    align-items: center;
    gap: 17px;
}


/* ICON */

.kebidanan-title-icon {
    width: 58px;
    height: 58px;
    min-width: 58px;

    border-radius: 16px;

    background: #eeeeff;
    color: #7571f9;

    display: flex;
    align-items: center;
    justify-content: center;

    font-size: 27px;

    box-shadow:
        0 5px 15px rgba(117, 113, 249, 0.10);
}


/* JUDUL */

.kebidanan-header h1 {
    margin: 0;

    font-size: 28px;
    font-weight: 700;

    color: #1a174d;

    letter-spacing: -0.4px;
}


/* SUB JUDUL */

.kebidanan-header p {
    margin: 5px 0 0;

    color: #727196;
    font-size: 14px;
}


/* =========================================================
   BUTTON TAMBAH
========================================================= */

.btn-tambah-ibu {
    background: #212229;
    color: #ffffff;

    border: none;
    border-radius: 10px;

    padding: 11px 18px;

    font-weight: 600;
    font-size: 14px;

    display: inline-flex;
    align-items: center;

    gap: 7px;

    transition: all .2s ease;

    white-space: nowrap;
}

.btn-tambah-ibu:hover {
    background: #000000;
    color: #ffffff;

    transform: translateY(-1px);

    box-shadow:
        0 5px 12px rgba(0,0,0,.15);
}


/* =========================================================
   ALERT
========================================================= */

.kebidanan-alert {
    border: none;
    border-radius: 12px;

    padding: 14px 18px;

    box-shadow:
        0 3px 12px rgba(0,0,0,.04);

    margin-bottom: 20px;
}


/* =========================================================
   CARD UTAMA
========================================================= */

.kebidanan-card {
    border: 1px solid #e5e5f0;
    border-radius: 20px;

    background: #ffffff;

    box-shadow:
        0 5px 20px rgba(15, 23, 42, 0.04);

    overflow: hidden;
}

.kebidanan-card-body {
    padding: 25px;
}


/* =========================================================
   SEARCH
========================================================= */

.search-section {
    margin-bottom: 24px;
}

.search-label {
    font-size: 14px;

    font-weight: 600;

    color: #363454;

    margin-bottom: 8px;
}

.search-label i {
    color: #7571f9;

    margin-right: 5px;
}

.search-wrapper {
    position: relative;

    max-width: 480px;
}

.search-wrapper .search-icon {
    position: absolute;

    left: 16px;
    top: 50%;

    transform: translateY(-50%);

    color: #7b7c94;

    font-size: 18px;

    z-index: 2;
}

.search-input {
    height: 48px;

    border: 1px solid #dcdde8;

    border-radius: 11px !important;

    padding-left: 45px !important;
    padding-right: 15px;

    font-size: 14px;

    color: #363454;

    transition: all .2s ease;
}

.search-input::placeholder {
    color: #9a98b3;
}

.search-input:focus {
    border-color: #b3aefc;

    box-shadow:
        0 0 0 3px rgba(117, 113, 249,.10);

    outline: none;
}

.search-help {
    display: block;

    margin-top: 7px;

    font-size: 12px;

    color: #9a98b3;
}


/* =========================================================
   TABLE WRAPPER
========================================================= */

.kebidanan-table-wrapper {
    border: 1px solid #e4e4ec;

    border-radius: 13px;

    overflow-x: auto;
}


/* =========================================================
   TABLE
========================================================= */

.kebidanan-table {
    margin-bottom: 0 !important;

    width: 100%;
}


/* HEADER TABLE */

.kebidanan-table thead th {
    background: #212229;

    color: #ffffff;

    border-color: #343540;

    font-size: 13px;

    font-weight: 600;

    padding: 14px 13px;

    white-space: nowrap;

    vertical-align: middle;
}


/* BODY */

.kebidanan-table tbody td {
    padding: 14px 13px;

    font-size: 13px;

    color: #363454;

    border-color: #e9e9f2;

    vertical-align: middle;
}


/* HOVER */

.kebidanan-table tbody tr {
    transition: background .15s ease;
}

.kebidanan-table tbody tr:hover {
    background: #f8f8ff;
}


/* NOMOR */

.kebidanan-table .nomor {
    width: 60px;

    font-weight: 600;

    color: #696685;
}


/* NAMA */

.nama-ibu {
    font-weight: 600;

    color: #1a174d !important;

    min-width: 160px;
}


/* NIK */

.nik {
    font-family: monospace;

    font-size: 12px !important;

    color: #484767 !important;

    white-space: nowrap;
}


/* =========================================================
   TOMBOL AKSI
========================================================= */

.aksi-wrapper {
    display: flex;

    justify-content: center;
    align-items: center;

    gap: 6px;

    white-space: nowrap;
}


.btn-aksi {
    width: 36px;
    height: 36px;

    border-radius: 8px;

    display: inline-flex;

    align-items: center;
    justify-content: center;

    border: 1px solid transparent;

    transition: all .2s ease;

    text-decoration: none;
}


/* =========================================================
   PEMERIKSAAN / JADWALKAN
========================================================= */

.btn-pemeriksaan {
    background: #212229;

    color: #ffffff;
}

.btn-pemeriksaan:hover {
    background: #000000;

    color: #ffffff;

    transform: translateY(-1px);

    box-shadow:
        0 4px 8px rgba(0,0,0,.15);
}


/* =========================================================
   EDIT IDENTITAS
========================================================= */

.btn-edit {
    background: #f1f1f5;

    color: #494a57;

    border-color: #dedfe6;
}

.btn-edit:hover {
    background: #e9e9ef;

    color: #212229;

    transform: translateY(-1px);

    box-shadow:
        0 4px 8px rgba(0,0,0,.08);
}


/* =========================================================
   HAPUS
========================================================= */

.btn-hapus {
    background: #fdecec;

    color: #d33636;

    border-color: #f7c9c9;
}

.btn-hapus:hover {
    background: #f8d3d3;

    color: #a92323;

    transform: translateY(-1px);

    box-shadow:
        0 4px 8px rgba(211,54,54,.10);
}


/* =========================================================
   EMPTY DATA
========================================================= */

.empty-data {
    padding: 55px 20px !important;

    text-align: center;

    color: #9a98b3 !important;
}

.empty-data-icon {
    width: 58px;
    height: 58px;

    border-radius: 15px;

    background: #f5f5fa;

    display: flex;

    align-items: center;
    justify-content: center;

    margin: 0 auto 12px;

    font-size: 26px;

    color: #9a98b3;
}

.empty-data-title {
    font-weight: 600;

    color: #696685;

    margin-bottom: 3px;
}

.empty-data-text {
    font-size: 13px;

    color: #9a98b3;
}


/* =========================================================
   RESPONSIVE
========================================================= */

@media (max-width: 992px) {

    .kebidanan-header {
        align-items: flex-start;

        flex-direction: column;
    }

    .btn-tambah-ibu {
        align-self: flex-start;
    }
}


@media (max-width: 768px) {

    .kebidanan-header {
        padding: 20px;
    }

    .kebidanan-header h1 {
        font-size: 23px;
    }

    .kebidanan-title-icon {
        width: 50px;
        height: 50px;

        min-width: 50px;

        font-size: 23px;
    }

    .kebidanan-card-body {
        padding: 18px;
    }

    .search-wrapper {
        max-width: 100%;
    }
}


@media (max-width: 576px) {

    .kebidanan-header {
        border-radius: 15px;
    }

    .kebidanan-title-area {
        align-items: flex-start;
    }

    .kebidanan-header h1 {
        font-size: 20px;
    }

    .kebidanan-header p {
        font-size: 13px;
    }

    .btn-tambah-ibu {
        width: 100%;

        justify-content: center;
    }
}


/* =========================================================
   SCROLLBAR TABLE
========================================================= */

.kebidanan-table-wrapper::-webkit-scrollbar {
    height: 7px;
}

.kebidanan-table-wrapper::-webkit-scrollbar-track {
    background: #f1f1f5;
}

.kebidanan-table-wrapper::-webkit-scrollbar-thumb {
    background: #cbcbe1;

    border-radius: 10px;
}

.kebidanan-table-wrapper::-webkit-scrollbar-thumb:hover {
    background: #9594b8;
}

</style>


<div class="page-content-wrap">

<div class="kebidanan-page">


    <!-- =====================================================
         HEADER
    ====================================================== -->

    <div class="kebidanan-header">

        <div class="kebidanan-title-area">

            <div class="kebidanan-title-icon">

                <i class="bi bi-heart-pulse"></i>

            </div>


            <div>

                <h1>
                    Rekam Medis Kebidanan dan Pemeriksaan Ibu Hamil
                </h1>

                <p>
                    Pengelolaan data ibu hamil dan pemeriksaan kebidanan
                </p>

            </div>

        </div>


        <a
            href="register-ibu-hamil.php"
            class="btn-tambah-ibu">

            <i class="bi bi-plus-lg"></i>

            Tambah Ibu Hamil

        </a>

    </div>



    <!-- =====================================================
         PESAN SUKSES
    ====================================================== -->

    <?php if (
        isset($_GET['status']) &&
        $_GET['status'] == 'sukses'
    ) : ?>

        <div
            class="alert alert-success alert-dismissible fade show kebidanan-alert"
            role="alert">

            <i class="bi bi-check-circle-fill me-2"></i>

            Data ibu hamil berhasil disimpan.

            <button
                type="button"
                class="btn-close"
                data-dismiss="alert">
            </button>

        </div>

    <?php endif; ?>



    <?php if (
        isset($_GET['status']) &&
        $_GET['status'] == 'update'
    ) : ?>

        <div
            class="alert alert-success alert-dismissible fade show kebidanan-alert"
            role="alert">

            <i class="bi bi-check-circle-fill me-2"></i>

            Data identitas ibu hamil berhasil diperbarui.

            <button
                type="button"
                class="btn-close"
                data-dismiss="alert">
            </button>

        </div>

    <?php endif; ?>



    <!-- =====================================================
         CARD DATA
    ====================================================== -->

    <div class="kebidanan-card">

        <div class="kebidanan-card-body">


            <!-- =================================================
                 SEARCH
            ================================================== -->

            <div class="search-section">

                <div class="search-label">

                    <i class="bi bi-search"></i>

                    Cari Nama Ibu

                </div>


                <div class="search-wrapper">

                    <i
                        class="bi bi-search search-icon">
                    </i>


                    <input
                        type="text"
                        id="searchIbu"
                        class="form-control search-input"
                        placeholder="Ketik nama ibu hamil..."
                        autocomplete="off">

                </div>


                <small class="search-help">

                    <i class="bi bi-info-circle me-1"></i>

                    Cari berdasarkan nama ibu atau NIK.

                </small>

            </div>



            <!-- =================================================
                 TABLE
            ================================================== -->

            <div class="kebidanan-table-wrapper">

                <table
                    class="table table-bordered table-hover align-middle kebidanan-table"
                    id="tabelIbuHamil">

                    <thead>

                        <tr>

                            <th
                                class="text-center"
                                style="width:60px;">

                                No

                            </th>


                            <th>
                                Nama Ibu
                            </th>


                            <th>
                                NIK
                            </th>


                            <th>
                                Nama Suami
                            </th>


                            <th>
                                No. HP
                            </th>


                            <th>
                                HPL
                            </th>


                            <th
                                class="text-center"
                                style="width:160px;">

                                Aksi

                            </th>

                        </tr>

                    </thead>


                    <tbody>

                    <?php

                    $no = 1;

                    if (
                        mysqli_num_rows($query) > 0
                    ) :

                        while (
                            $data =
                            mysqli_fetch_assoc($query)
                        ) :

                    ?>

                        <tr class="data-ibu">


                            <!-- NO -->

                            <td class="text-center nomor">

                                <?= $no++; ?>

                            </td>



                            <!-- NAMA -->

                            <td class="nama-ibu">

                                <?= htmlspecialchars(
                                    $data['nama_ibu']
                                ); ?>

                            </td>



                            <!-- NIK -->

                            <td class="nik">

                                <?= htmlspecialchars(
                                    $data['nik']
                                ); ?>

                            </td>



                            <!-- SUAMI -->

                            <td>

                                <?= htmlspecialchars(
                                    $data['nama_suami']
                                ); ?>

                            </td>



                            <!-- HP -->

                            <td>

                                <?= htmlspecialchars(
                                    $data['no_hp']
                                ); ?>

                            </td>



                            <!-- HPL -->

                            <td>

                                <?php

                                if (
                                    !empty(
                                        $data['hpl']
                                    )
                                ) {

                                    echo date(
                                        'd-m-Y',
                                        strtotime(
                                            $data['hpl']
                                        )
                                    );

                                } else {

                                    echo '-';

                                }

                                ?>

                            </td>



                            <!-- =================================================
                                 AKSI
                            ================================================== -->

                            <td class="text-center">

                                <div class="aksi-wrapper">


                                    <!-- =================================================
                                         PEMERIKSAAN / JADWALKAN
                                    ================================================== -->

                                    <a
                                        href="proses-jadwal-kebidanan.php?jenis=Ibu+Hamil&ref_id=<?= $data['id']; ?>&balik=rekam-medis-kebidanan.php"
                                        class="btn-aksi btn-pemeriksaan"
                                        title="Jadwalkan pemeriksaan hari ini"
                                        data-toggle="tooltip"
                                        onclick="return confirm('Jadwalkan <?= htmlspecialchars($data['nama_ibu'], ENT_QUOTES); ?> untuk pemeriksaan kebidanan hari ini?');">

                                        <i class="bi bi-calendar-plus"></i>

                                    </a>



                                    <!-- =================================================
                                         EDIT IDENTITAS
                                    ================================================== -->

                                    <a
                                        href="edit-ibu-hamil.php?id=<?= $data['id']; ?>"
                                        class="btn-aksi btn-edit"
                                        title="Edit identitas ibu hamil"
                                        data-toggle="tooltip">

                                        <i class="bi bi-pencil"></i>

                                    </a>



                                    <!-- =================================================
                                         HAPUS
                                    ================================================== -->

                                    <a
                                        href="hapus-ibu-hamil.php?id=<?= $data['id']; ?>"
                                        class="btn-aksi btn-hapus"
                                        title="Hapus"
                                        data-toggle="tooltip"
                                        onclick="return confirm('Hapus data ibu hamil <?= htmlspecialchars($data['nama_ibu'], ENT_QUOTES); ?>? Data pemeriksaan dan jadwal terkait juga akan terhapus.');">

                                        <i class="bi bi-trash"></i>

                                    </a>


                                </div>

                            </td>


                        </tr>


                    <?php

                        endwhile;

                    else :

                    ?>

                        <tr id="dataKosong">

                            <td
                                colspan="7"
                                class="empty-data">

                                <div class="empty-data-icon">

                                    <i class="bi bi-person-x"></i>

                                </div>


                                <div class="empty-data-title">

                                    Belum ada data ibu hamil

                                </div>


                                <div class="empty-data-text">

                                    Silakan tambahkan data ibu hamil terlebih dahulu.

                                </div>

                            </td>

                        </tr>

                    <?php

                    endif;

                    ?>

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</div>

</div>



<!-- =========================================================
     SEARCH OTOMATIS
========================================================= -->

<script>

document.addEventListener(
    "DOMContentLoaded",
    function () {

        const searchInput =
            document.getElementById(
                "searchIbu"
            );


        const rows =
            document.querySelectorAll(
                "#tabelIbuHamil tbody .data-ibu"
            );


        searchInput.addEventListener(
            "input",
            function () {

                const keyword =
                    this.value
                        .toLowerCase()
                        .trim();


                rows.forEach(
                    function (row) {

                        const nama =
                            row
                                .querySelector(
                                    ".nama-ibu"
                                )
                                .textContent
                                .toLowerCase();


                        const nik =
                            row
                                .querySelector(
                                    ".nik"
                                )
                                .textContent
                                .toLowerCase();


                        /*
                         * Cari berdasarkan:
                         * - Nama ibu
                         * - NIK
                         */

                        if (
                            nama.includes(keyword) ||
                            nik.includes(keyword)
                        ) {

                            row.style.display = "";

                        } else {

                            row.style.display = "none";

                        }

                    }
                );


                /*
                 * Jika kotak search kosong,
                 * semua data ditampilkan kembali.
                 */

                if (keyword === "") {

                    rows.forEach(
                        function (row) {

                            row.style.display = "";

                        }
                    );

                }

            }
        );


        /*
         * AKTIFKAN TOOLTIP
         */

        const tooltipTriggerList =
            document.querySelectorAll(
                '[data-toggle="tooltip"]'
            );


        tooltipTriggerList.forEach(
            function (tooltipTriggerEl) {

                if (
                    typeof bootstrap !== "undefined" &&
                    bootstrap.Tooltip
                ) {

                    new bootstrap.Tooltip(
                        tooltipTriggerEl
                    );

                }

            }
        );

    }
);

</script>



<?php

require "../template/footer.php";

?>