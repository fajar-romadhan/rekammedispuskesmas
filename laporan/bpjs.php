<?php

session_start();

require "../template/rbac.php";

// Hanya Kepala Puskesmas
cekAkses([ROLE_KEPALA]);

require "../config.php";

$title = "Laporan BPJS - rekammedispuskesmas";

require "../template/header.php";
require "../template/navbar.php";
require "../template/sidebar.php";


// ======================================================
// AMBIL DATA PASIEN BPJS
// ======================================================

$query = mysqli_query($koneksi, "
    SELECT *
    FROM tbl_pasien
    WHERE no_asuransi LIKE 'BPJS%'
    ORDER BY nama ASC
");


// ======================================================
// HITUNG JUMLAH PASIEN BPJS
// ======================================================

$queryJumlah = mysqli_query($koneksi, "
    SELECT COUNT(*) AS total
    FROM tbl_pasien
    WHERE no_asuransi LIKE 'BPJS%'
");

$dataJumlah = mysqli_fetch_assoc($queryJumlah);
$totalBPJS = $dataJumlah['total'];

?>

<style>

/* =====================================================
   LAPORAN BPJS
===================================================== */

.bpjs-container {
    padding-bottom: 40px;
}


/* =====================================================
   HEADER HALAMAN
===================================================== */

.bpjs-header {
    background: #ffffff;
    border: 1px solid #e8e8f3;
    border-radius: 16px;
    padding: 22px 25px;
    margin-top: 20px;
    margin-bottom: 20px;
    box-shadow: 0 5px 18px rgba(0, 0, 0, 0.04);
}

.bpjs-title {
    display: flex;
    align-items: center;
    gap: 13px;
}

.bpjs-title-icon {
    width: 45px;
    height: 45px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 12px;
    background: #eae9ff;
    color: #7571f9;
    font-size: 22px;
}

.bpjs-title h1 {
    font-size: 24px;
    font-weight: 700;
    color: #262a38;
    margin: 0;
}

.bpjs-title p {
    font-size: 13px;
    color: #7b7c94;
    margin: 4px 0 0;
}


/* =====================================================
   CARD JUMLAH PASIEN
===================================================== */

.total-card {
    background: #ffffff;
    border: 1px solid #e8e8f3;
    border-radius: 15px;
    padding: 17px 20px;
    box-shadow: 0 5px 18px rgba(0, 0, 0, 0.04);
    height: 100%;
}

.total-icon {
    width: 42px;
    height: 42px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #eaf7ef;
    color: #198754;
    font-size: 20px;
}

.total-label {
    font-size: 12px;
    color: #7b7c94;
    margin-bottom: 2px;
}

.total-number {
    font-size: 23px;
    font-weight: 700;
    color: #262a38;
}


/* =====================================================
   SEARCH CARD
===================================================== */

.search-card {
    background: #ffffff;
    border: 1px solid #e8e8f3;
    border-radius: 15px;
    padding: 18px 20px;
    box-shadow: 0 5px 18px rgba(0, 0, 0, 0.04);
}

.search-label {
    font-size: 13px;
    font-weight: 600;
    color: #45465e;
    margin-bottom: 8px;
}

.search-wrapper {
    position: relative;
}

.search-wrapper .search-icon {
    position: absolute;
    left: 15px;
    top: 50%;
    transform: translateY(-50%);
    color: #7d7f95;
    font-size: 17px;
    z-index: 2;
}

#searchBPJS {
    height: 44px;
    border-radius: 10px;
    padding-left: 43px;
    padding-right: 40px;
    border: 1px solid #dcddea;
    font-size: 14px;
    transition: all 0.2s ease;
}

#searchBPJS:focus {
    border-color: #7571f9;
    box-shadow: 0 0 0 3px rgba(117, 113, 249, 0.10);
}


/* Tombol hapus search */

.clear-search {
    position: absolute;
    right: 12px;
    top: 50%;
    transform: translateY(-50%);
    border: none;
    background: transparent;
    color: #9b9cb1;
    display: none;
    cursor: pointer;
    z-index: 3;
}

.clear-search:hover {
    color: #dc3545;
}


/* =====================================================
   BUTTON PDF
===================================================== */

.pdf-card {
    background: #ffffff;
    border: 1px solid #e8e8f3;
    border-radius: 15px;
    padding: 18px 20px;
    box-shadow: 0 5px 18px rgba(0, 0, 0, 0.04);
}

.pdf-title {
    font-size: 13px;
    font-weight: 600;
    color: #45465e;
    margin-bottom: 12px;
}

.btn-pdf {
    border-radius: 9px;
    padding: 9px 15px;
    font-size: 13px;
    font-weight: 600;
    transition: all 0.2s ease;
}

.btn-pdf:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 12px rgba(220, 53, 69, 0.18);
}


/* =====================================================
   TABLE CARD
===================================================== */

.table-card {
    background: #ffffff;
    border: 1px solid #e8e8f3;
    border-radius: 16px;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
    overflow: hidden;
    margin-top: 20px;
}

.table-card-header {
    padding: 17px 20px;
    border-bottom: 1px solid #ededf4;
}

.table-card-title {
    margin: 0;
    font-size: 15px;
    font-weight: 700;
    color: #262a38;
}

.table-card-subtitle {
    margin: 4px 0 0;
    font-size: 12px;
    color: #8a8ba0;
}


/* =====================================================
   TABLE
===================================================== */

.table-wrapper {
    width: 100%;
    overflow-x: auto;
}

#tableBPJS {
    margin: 0;
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
}


/* Header */

#tableBPJS thead th {
    background: #f4f4fb;
    color: #45465e;
    font-size: 12px;
    font-weight: 700;
    padding: 15px 14px;
    border-bottom: 1px solid #e2e2ed;
    white-space: nowrap;
    vertical-align: middle;
}


/* Body */

#tableBPJS tbody td {
    padding: 14px;
    font-size: 13px;
    color: #53556d;
    border-bottom: 1px solid #ededf3;
    vertical-align: middle;
}

#tableBPJS tbody tr {
    transition: all 0.2s ease;
}

#tableBPJS tbody tr:hover {
    background: #f8f8ff;
}


/* =====================================================
   ID RM
===================================================== */

.rm-badge {
    display: inline-block;
    padding: 5px 9px;
    border-radius: 7px;
    background: #f1f2f9;
    border: 1px solid #dcddec;
    color: #45465e;
    font-size: 11px;
    font-weight: 600;
    white-space: nowrap;
}


/* =====================================================
   NAMA PASIEN
===================================================== */

.nama-pasien {
    font-weight: 600;
    color: #262a38 !important;
}


/* =====================================================
   BPJS
===================================================== */

.bpjs-badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 6px 9px;
    border-radius: 7px;
    background: #eaf7ef;
    color: #198754;
    font-size: 11px;
    font-weight: 600;
    white-space: nowrap;
}


/* =====================================================
   GENDER
===================================================== */

.gender-badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 5px 9px;
    border-radius: 7px;
    background: #f4f4fb;
    color: #595a74;
    font-size: 11px;
    white-space: nowrap;
}


/* =====================================================
   ALAMAT
===================================================== */

.alamat-pasien {
    min-width: 180px;
    max-width: 280px;
    line-height: 1.5;
}


/* =====================================================
   NIK
===================================================== */

.nik-badge {
    font-size: 11px;
    color: #5d5e74;
    white-space: nowrap;
}


/* =====================================================
   HASIL PENCARIAN
===================================================== */

.search-result-info {
    font-size: 12px;
    color: #7b7c94;
    margin-top: 8px;
}

#jumlahHasil {
    font-weight: 700;
    color: #7571f9;
}


/* =====================================================
   DATA TIDAK DITEMUKAN
===================================================== */

#dataKosong {
    display: none;
}

.empty-data {
    text-align: center;
    padding: 45px 20px !important;
    color: #7b7c94;
}

.empty-data i {
    display: block;
    font-size: 40px;
    color: #b7b8cc;
    margin-bottom: 10px;
}

.empty-data strong {
    display: block;
    color: #56576d;
    margin-bottom: 4px;
}


/* =====================================================
   RESPONSIVE
===================================================== */

@media (max-width: 768px) {

    .bpjs-header {
        padding: 18px;
    }

    .bpjs-title h1 {
        font-size: 20px;
    }

    .bpjs-title-icon {
        width: 40px;
        height: 40px;
        font-size: 19px;
    }

    .total-card {
        margin-top: 5px;
    }

    .pdf-card {
        margin-top: 5px;
    }

    #tableBPJS thead th,
    #tableBPJS tbody td {
        padding: 11px 10px;
        font-size: 12px;
    }

}


/* =====================================================
   ANIMASI
===================================================== */

.bpjs-header,
.total-card,
.search-card,
.pdf-card,
.table-card {
    animation: fadeInBPJS 0.35s ease;
}

@keyframes fadeInBPJS {

    from {
        opacity: 0;
        transform: translateY(6px);
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }

}

</style>


<div class="page-content-wrap">

<div class="bpjs-container">


    <!-- ==================================================
         HEADER
    =================================================== -->

    <div class="bpjs-header">

        <div class="bpjs-title">

            <div class="bpjs-title-icon">

                <i class="bi bi-card-checklist"></i>

            </div>

            <div>

                <h1>
                    Laporan BPJS
                </h1>

                <p>
                    Data pasien yang terdaftar sebagai peserta BPJS.
                </p>

            </div>

        </div>

    </div>


    <!-- ==================================================
         INFORMASI & SEARCH
    =================================================== -->

    <div class="row g-3 mb-3">


        <!-- TOTAL PASIEN -->

        <div class="col-lg-4 col-md-5">

            <div class="total-card">

                <div class="d-flex align-items-center gap-3">

                    <div class="total-icon">

                        <i class="bi bi-people-fill"></i>

                    </div>

                    <div>

                        <div class="total-label">
                            Total Pasien BPJS
                        </div>

                        <div class="total-number">

                            <?= $totalBPJS; ?>

                            <small
                                style="font-size:12px;
                                       font-weight:500;
                                       color:#7b7c94;">
                                Pasien
                            </small>

                        </div>

                    </div>

                </div>

            </div>

        </div>


        <!-- SEARCH -->

        <div class="col-lg-8 col-md-7">

            <div class="search-card">

                <div class="search-label">

                    <i class="bi bi-search me-1"></i>

                    Cari Data Pasien BPJS

                </div>

                <div class="search-wrapper">

                    <i class="bi bi-search search-icon"></i>

                    <input
                        type="text"
                        id="searchBPJS"
                        class="form-control"
                        placeholder="Cari nama, No RM, No BPJS, NIK, atau alamat..."
                        autocomplete="off"
                    >

                    <button
                        type="button"
                        id="clearSearch"
                        class="clear-search"
                        title="Hapus pencarian">

                        <i class="bi bi-x-circle-fill"></i>

                    </button>

                </div>

                <div class="search-result-info">

                    Menampilkan
                    <span id="jumlahHasil">
                        <?= $totalBPJS; ?>
                    </span>
                    data pasien

                </div>

            </div>

        </div>

    </div>


    <!-- ==================================================
         TOMBOL PDF
    =================================================== -->

    <div class="pdf-card">

        <div class="pdf-title">

            <i class="bi bi-file-earmark-pdf me-1"></i>

            Cetak Laporan BPJS

        </div>


        <div class="d-flex flex-wrap gap-2">

            <!-- PDF MINGGUAN -->

            <a
                href="<?= $main_url ?>laporan/bpjs-pdf.php?periode=mingguan"
                class="btn btn-danger btn-pdf"
                target="_blank"
            >

                <i class="bi bi-file-earmark-pdf me-1"></i>

                PDF Mingguan

            </a>


            <!-- PDF BULANAN -->

            <a
                href="<?= $main_url ?>laporan/bpjs-pdf.php?periode=bulanan"
                class="btn btn-danger btn-pdf"
                target="_blank"
            >

                <i class="bi bi-file-earmark-pdf me-1"></i>

                PDF Bulanan

            </a>

        </div>

    </div>


    <!-- ==================================================
         TABLE
    =================================================== -->

    <div class="table-card">


        <!-- TABLE HEADER -->

        <div class="table-card-header">

            <h6 class="table-card-title">

                <i class="bi bi-table me-1"></i>

                Data Pasien BPJS

            </h6>

            <p class="table-card-subtitle">

                Daftar pasien berdasarkan data kepesertaan BPJS.

            </p>

        </div>


        <!-- TABLE -->

        <div class="table-wrapper">

            <table
                id="tableBPJS"
                class="table align-middle"
            >

                <thead>

                    <tr>

                        <th>
                            No
                        </th>

                        <th>
                            No RM
                        </th>

                        <th>
                            Nama Pasien
                        </th>

                        <th>
                            Tanggal Lahir
                        </th>

                        <th>
                            Jenis Kelamin
                        </th>

                        <th>
                            No. BPJS
                        </th>

                        <th>
                            Alamat
                        </th>

                        <th>
                            NIK
                        </th>

                    </tr>

                </thead>


                <tbody>

                    <?php

                    $no = 1;

                    while ($data = mysqli_fetch_assoc($query)) {

                    ?>

                    <tr class="data-bpjs">


                        <!-- NO -->

                        <td>
                            <?= $no++; ?>
                        </td>


                        <!-- NO RM -->

                        <td>

                            <span class="rm-badge">

                                <?= htmlspecialchars(
                                    $data['no_rm']
                                ); ?>

                            </span>

                        </td>


                        <!-- NAMA -->

                        <td class="nama-pasien">

                            <i class="bi bi-person-circle text-primary me-1"></i>

                            <?= htmlspecialchars(
                                $data['nama']
                            ); ?>

                        </td>


                        <!-- TANGGAL LAHIR -->

                        <td>

                            <i class="bi bi-calendar3 me-1 text-muted"></i>

                            <?= date(
                                'd-m-Y',
                                strtotime($data['tgl_lahir'])
                            ); ?>

                        </td>


                        <!-- JENIS KELAMIN -->

                        <td>

                            <span class="gender-badge">

                                <?php

                                if ($data['gender'] == 'P') {

                                    echo '<i class="bi bi-gender-male"></i> Pria';

                                } else {

                                    echo '<i class="bi bi-gender-female"></i> Wanita';

                                }

                                ?>

                            </span>

                        </td>


                        <!-- BPJS -->

                        <td>

                            <span class="bpjs-badge">

                                <i class="bi bi-shield-check"></i>

                                <?= htmlspecialchars(
                                    $data['no_asuransi']
                                ); ?>

                            </span>

                        </td>


                        <!-- ALAMAT -->

                        <td class="alamat-pasien">

                            <i class="bi bi-geo-alt me-1 text-muted"></i>

                            <?= htmlspecialchars(
                                $data['alamat']
                            ); ?>

                        </td>


                        <!-- NIK -->

                        <td>

                            <span class="nik-badge">

                                <?= htmlspecialchars(
                                    $data['nik']
                                ); ?>

                            </span>

                        </td>


                    </tr>

                    <?php

                    }

                    ?>


                    <!-- DATA TIDAK DITEMUKAN -->

                    <tr id="dataKosong">

                        <td
                            colspan="8"
                            class="empty-data"
                        >

                            <i class="bi bi-search"></i>

                            <strong>
                                Data pasien tidak ditemukan
                            </strong>

                            <span>
                                Silakan coba dengan nama,
                                No RM, No BPJS, NIK,
                                atau alamat yang berbeda.
                            </span>

                        </td>

                    </tr>


                </tbody>

            </table>

        </div>

    </div>

</div>

</div>


<!-- ======================================================
     JAVASCRIPT SEARCH
======================================================= -->

<script>

document.addEventListener("DOMContentLoaded", function () {

    const searchInput =
        document.getElementById("searchBPJS");

    const clearButton =
        document.getElementById("clearSearch");

    const rows =
        document.querySelectorAll(".data-bpjs");

    const jumlahHasil =
        document.getElementById("jumlahHasil");

    const dataKosong =
        document.getElementById("dataKosong");


    // ==============================================
    // PENCARIAN
    // ==============================================

    function cariBPJS() {

        const keyword =
            searchInput.value
                .toLowerCase()
                .trim();

        let jumlah = 0;


        rows.forEach(function (row) {

            const text =
                row.innerText
                    .toLowerCase();

            if (text.includes(keyword)) {

                row.style.display = "";

                jumlah++;

            } else {

                row.style.display = "none";

            }

        });


        // ==========================================
        // JUMLAH HASIL
        // ==========================================

        jumlahHasil.textContent =
            jumlah;


        // ==========================================
        // DATA TIDAK DITEMUKAN
        // ==========================================

        if (jumlah === 0) {

            dataKosong.style.display =
                "table-row";

        } else {

            dataKosong.style.display =
                "none";

        }


        // ==========================================
        // TOMBOL CLEAR
        // ==========================================

        if (keyword !== "") {

            clearButton.style.display =
                "block";

        } else {

            clearButton.style.display =
                "none";

        }

    }


    // ==============================================
    // EVENT SEARCH
    // ==============================================

    searchInput.addEventListener(
        "input",
        cariBPJS
    );


    searchInput.addEventListener(
        "keyup",
        cariBPJS
    );


    // ==============================================
    // CLEAR SEARCH
    // ==============================================

    clearButton.addEventListener(
        "click",
        function () {

            searchInput.value = "";

            cariBPJS();

            searchInput.focus();

        }
    );

});

</script>


<?php

require "../template/footer.php";

?>