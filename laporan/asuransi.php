<?php

session_start();

require "../template/rbac.php";

// Hanya Kepala Puskesmas
cekAkses([ROLE_KEPALA]);

require "../config.php";

$title = "Laporan Asuransi - rekammedispuskesmas";

require "../template/header.php";
require "../template/navbar.php";
require "../template/sidebar.php";


// ======================================================
// DATA PASIEN ASURANSI
// ======================================================

$query = mysqli_query($koneksi, "
    SELECT *
    FROM tbl_pasien
    WHERE no_asuransi IS NOT NULL
      AND no_asuransi != ''
      AND no_asuransi NOT LIKE 'BPJS%'
    ORDER BY nama ASC
");


// ======================================================
// TOTAL PASIEN ASURANSI
// ======================================================

$queryTotal = mysqli_query($koneksi, "
    SELECT COUNT(*) AS total
    FROM tbl_pasien
    WHERE no_asuransi IS NOT NULL
      AND no_asuransi != ''
      AND no_asuransi NOT LIKE 'BPJS%'
");

$dataTotal = mysqli_fetch_assoc($queryTotal);
$totalAsuransi = $dataTotal['total'];

?>

<style>

/* ======================================================
   WRAPPER
   ====================================================== */

.laporan-wrapper {
    padding-top: 10px;
    padding-bottom: 25px;
}


/* ======================================================
   HEADER
   ====================================================== */

.laporan-header {
    background: #fff;
    border: 1px solid #e2e2f0;
    border-radius: 14px;
    padding: 16px 20px;
    margin-bottom: 16px;
    box-shadow: 0 2px 8px rgba(0,0,0,.03);
}

.laporan-header-content {
    display: flex;
    align-items: center;
    gap: 12px;
}

.laporan-icon {
    width: 42px;
    height: 42px;
    border-radius: 10px;
    background: #ebeaff;

    display: flex;
    align-items: center;
    justify-content: center;

    flex-shrink: 0;
}

.laporan-icon i {
    font-size: 20px;
    color: #7571f9;
}

.laporan-header h1 {
    margin: 0;
    font-size: 21px;
    font-weight: 600;
    color: #1a174d;
}

.laporan-header p {
    margin: 3px 0 0;
    font-size: 12px;
    color: #727196;
}


/* ======================================================
   GRID
   ====================================================== */

.info-grid {
    display: grid;
    grid-template-columns: 1fr 2fr;
    gap: 14px;
    margin-bottom: 14px;
}


/* ======================================================
   CARD
   ====================================================== */

.report-card {
    background: #fff;
    border: 1px solid #e2e2f0;
    border-radius: 14px;
    box-shadow: 0 2px 8px rgba(0,0,0,.03);
}


/* ======================================================
   TOTAL
   ====================================================== */

.total-card {
    min-height: 120px;
    padding: 18px;

    display: flex;
    align-items: center;
}

.total-content {
    display: flex;
    align-items: center;
    gap: 13px;
}

.total-icon {
    width: 43px;
    height: 43px;
    border-radius: 10px;
    background: #e9f8f0;

    display: flex;
    align-items: center;
    justify-content: center;

    flex-shrink: 0;
}

.total-icon i {
    font-size: 20px;
    color: #198754;
}

.total-label {
    color: #727196;
    font-size: 12px;
    margin-bottom: 2px;
}

.total-number {
    color: #1a174d;
    font-size: 21px;
    font-weight: 700;
}

.total-number span {
    font-size: 12px;
    font-weight: 500;
    margin-left: 2px;
}


/* ======================================================
   SEARCH
   ====================================================== */

.search-card {
    padding: 17px 20px;
    min-height: 120px;
}

.search-title {
    display: flex;
    align-items: center;
    gap: 7px;

    color: #17185d;
    font-size: 13px;
    font-weight: 600;

    margin-bottom: 9px;
}

.search-title i {
    font-size: 17px;
    color: #7571f9;
}

.search-box {
    position: relative;
}

.search-box i {
    position: absolute;

    left: 13px;
    top: 50%;

    transform: translateY(-50%);

    font-size: 17px;
    color: #727196;

    z-index: 2;
}

.search-box input {
    width: 100%;
    height: 40px;

    border: 1px solid #d8d8eb;
    border-radius: 9px;

    padding: 0 12px 0 39px;

    font-size: 12px;
    color: #343355;

    outline: none;

    transition: .2s;
}

.search-box input:focus {
    border-color: #b3aefc;
    box-shadow: 0 0 0 3px rgba(117, 113, 249,.07);
}

.search-info {
    margin-top: 6px;
    font-size: 11px;
    color: #727196;
}

.search-info strong {
    color: #7571f9;
}


/* ======================================================
   PDF
   ====================================================== */

.pdf-card {
    padding: 15px 20px;
    margin-bottom: 14px;
}

.pdf-content {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 15px;
}

.pdf-title {
    display: flex;
    align-items: center;
    gap: 8px;

    color: #17185d;
    font-size: 13px;
    font-weight: 600;
}

.pdf-title i {
    color: #7571f9;
    font-size: 17px;
}

.pdf-buttons {
    display: flex;
    gap: 7px;
}

.btn-pdf {
    border: none;
    border-radius: 7px;

    padding: 7px 12px;

    font-size: 11px;
    font-weight: 600;

    color: #fff;
    background: #dc3545;

    text-decoration: none;

    display: inline-flex;
    align-items: center;
    gap: 5px;

    transition: .2s;
}

.btn-pdf:hover {
    background: #bb2d3b;
    color: #fff;
}

.btn-pdf i {
    font-size: 13px;
}


/* ======================================================
   DATA CARD
   ====================================================== */

.data-card {
    background: #fff;

    border: 1px solid #e2e2f0;
    border-radius: 14px;

    padding: 17px 20px 20px;

    box-shadow: 0 2px 8px rgba(0,0,0,.03);
}

.data-title {
    display: flex;
    align-items: center;
    gap: 7px;

    color: #1a174d;

    font-size: 15px;
    font-weight: 600;

    margin-bottom: 2px;
}

.data-title i {
    font-size: 17px;
    color: #17185d;
}

.data-subtitle {
    color: #727196;
    font-size: 11px;
    margin-bottom: 12px;
}


/* ======================================================
   TABLE
   ====================================================== */

.table-container {
    border: 1px solid #e2e2f0;
    border-radius: 9px;
    overflow: hidden;
}

#tableAsuransi {
    margin-bottom: 0;
}

#tableAsuransi thead th {
    background: #f1f2f9;

    color: #484769;

    font-weight: 600;

    font-size: 11px;

    padding: 9px 9px;

    border-bottom: 1px solid #dcdcec;

    white-space: nowrap;
}

#tableAsuransi tbody td {
    padding: 9px 9px;

    color: #343355;

    font-size: 11px;

    vertical-align: middle;

    border-bottom: 1px solid #edeef5;
}

#tableAsuransi tbody tr:last-child td {
    border-bottom: none;
}

#tableAsuransi tbody tr:hover {
    background: #f8f8fc;
}


/* ======================================================
   BADGE NO RM
   ====================================================== */

.rm-badge {
    display: inline-block;

    background: #f1f2f9;

    border: 1px solid #dbdcec;

    border-radius: 5px;

    padding: 3px 6px;

    font-weight: 600;

    font-size: 10px;

    color: #484769;
}


/* ======================================================
   BADGE ASURANSI
   ====================================================== */

.asuransi-badge {
    display: inline-block;

    background: #eeeeff;

    border: 1px solid #d9d7ff;

    border-radius: 5px;

    padding: 3px 6px;

    font-weight: 600;

    font-size: 10px;

    color: #7571f9;
}


/* ======================================================
   NAMA
   ====================================================== */

.nama-pasien {
    font-weight: 600;
    color: #1a174d;
}


/* ======================================================
   GENDER
   ====================================================== */

.gender-badge {
    display: inline-block;

    padding: 3px 6px;

    border-radius: 5px;

    background: #f8f8fa;

    font-size: 10px;

    font-weight: 500;
}


/* ======================================================
   DATA KOSONG
   ====================================================== */

.empty-data {
    text-align: center;

    padding: 30px 15px !important;

    color: #9594b8 !important;
}

.empty-data i {
    font-size: 28px;

    display: block;

    margin-bottom: 7px;
}


/* ======================================================
   RESPONSIVE
   ====================================================== */

@media (max-width: 992px) {

    .info-grid {
        grid-template-columns: 1fr;
    }

    .pdf-content {
        align-items: flex-start;
        flex-direction: column;
    }

}


@media (max-width: 576px) {

    .laporan-header {
        padding: 13px 15px;
    }

    .laporan-header h1 {
        font-size: 18px;
    }

    .laporan-header p {
        font-size: 11px;
    }

    .laporan-icon {
        width: 38px;
        height: 38px;
    }

    .laporan-icon i {
        font-size: 18px;
    }

    .total-card,
    .search-card,
    .pdf-card,
    .data-card {
        padding: 14px;
    }

    .pdf-buttons {
        width: 100%;
    }

    .btn-pdf {
        flex: 1;
        justify-content: center;
    }

}

</style>


<div class="page-content-wrap">

<div class="laporan-wrapper">


    <!-- ==================================================
         HEADER
         ================================================== -->

    <div class="laporan-header">

        <div class="laporan-header-content">

            <div class="laporan-icon">
                <i class="bi bi-card-checklist"></i>
            </div>

            <div>

                <h1>
                    Laporan Asuransi
                </h1>

                <p>
                    Data pasien yang terdaftar sebagai peserta asuransi.
                </p>

            </div>

        </div>

    </div>


    <!-- ==================================================
         TOTAL DAN SEARCH
         ================================================== -->

    <div class="info-grid">


        <!-- TOTAL -->

        <div class="report-card total-card">

            <div class="total-content">

                <div class="total-icon">
                    <i class="bi bi-people-fill"></i>
                </div>

                <div>

                    <div class="total-label">
                        Total Pasien Asuransi
                    </div>

                    <div class="total-number">

                        <span id="totalPasien">
                            <?= $totalAsuransi; ?>
                        </span>

                        <span>
                            Pasien
                        </span>

                    </div>

                </div>

            </div>

        </div>


        <!-- SEARCH -->

        <div class="report-card search-card">

            <div class="search-title">

                <i class="bi bi-search"></i>

                <span>
                    Cari Data Pasien Asuransi
                </span>

            </div>


            <div class="search-box">

                <i class="bi bi-search"></i>

                <input type="text"
                       id="searchAsuransi"
                       placeholder="Cari nama, No RM, No Asuransi, NIK, atau alamat..."
                       autocomplete="off">

            </div>


            <div class="search-info">

                Menampilkan

                <strong id="jumlahData">
                    <?= $totalAsuransi; ?>
                </strong>

                data pasien

            </div>

        </div>

    </div>


    <!-- ==================================================
         CETAK PDF
         ================================================== -->

    <div class="report-card pdf-card">

        <div class="pdf-content">

            <div class="pdf-title">

                <i class="bi bi-file-earmark-pdf"></i>

                <span>
                    Cetak Laporan Asuransi
                </span>

            </div>


            <div class="pdf-buttons">

                <a href="<?= $main_url ?>laporan/asuransi-pdf.php?periode=mingguan"
                   class="btn-pdf"
                   target="_blank">

                    <i class="bi bi-file-earmark-pdf"></i>

                    PDF Mingguan

                </a>


                <a href="<?= $main_url ?>laporan/asuransi-pdf.php?periode=bulanan"
                   class="btn-pdf"
                   target="_blank">

                    <i class="bi bi-file-earmark-pdf"></i>

                    PDF Bulanan

                </a>

            </div>

        </div>

    </div>


    <!-- ==================================================
         DATA PASIEN
         ================================================== -->

    <div class="data-card">

        <div class="data-title">

            <i class="bi bi-table"></i>

            <span>
                Data Pasien Asuransi
            </span>

        </div>


        <div class="data-subtitle">

            Daftar pasien berdasarkan data kepesertaan asuransi.

        </div>


        <div class="table-responsive table-container">

            <table class="table table-hover align-middle"
                   id="tableAsuransi">

                <thead>

                    <tr>

                        <th>No</th>

                        <th>No RM</th>

                        <th>Nama Pasien</th>

                        <th>Tanggal Lahir</th>

                        <th>Jenis Kelamin</th>

                        <th>No. Asuransi</th>

                        <th>Alamat</th>

                        <th>NIK</th>

                    </tr>

                </thead>


                <tbody>

                <?php

                $no = 1;

                if ($totalAsuransi > 0) {

                    while ($data = mysqli_fetch_assoc($query)) {

                ?>

                    <tr>

                        <td>
                            <?= $no++; ?>
                        </td>


                        <td>

                            <span class="rm-badge">

                                <?= htmlspecialchars(
                                    $data['no_rm']
                                ); ?>

                            </span>

                        </td>


                        <td>

                            <span class="nama-pasien">

                                <?= htmlspecialchars(
                                    $data['nama']
                                ); ?>

                            </span>

                        </td>


                        <td>

                            <?= date(
                                'd-m-Y',
                                strtotime($data['tgl_lahir'])
                            ); ?>

                        </td>


                        <td>

                            <span class="gender-badge">

                                <?= $data['gender'] == 'P'
                                    ? 'Pria'
                                    : 'Wanita'; ?>

                            </span>

                        </td>


                        <td>

                            <span class="asuransi-badge">

                                <?= htmlspecialchars(
                                    $data['no_asuransi']
                                ); ?>

                            </span>

                        </td>


                        <td>

                            <?= htmlspecialchars(
                                $data['alamat']
                            ); ?>

                        </td>


                        <td>

                            <?= htmlspecialchars(
                                $data['nik']
                            ); ?>

                        </td>

                    </tr>

                <?php

                    }

                } else {

                ?>

                    <tr>

                        <td colspan="8"
                            class="empty-data">

                            <i class="bi bi-inbox"></i>

                            Belum ada data pasien asuransi.

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

</div>


<!-- ======================================================
     SEARCH
     ====================================================== -->

<script>

document.addEventListener("DOMContentLoaded", function () {

    const searchInput =
        document.getElementById("searchAsuransi");

    const table =
        document.getElementById("tableAsuransi");

    const jumlahData =
        document.getElementById("jumlahData");

    const rows =
        table.querySelectorAll("tbody tr");


    searchInput.addEventListener("keyup", function () {

        const keyword =
            this.value.toLowerCase().trim();

        let jumlah = 0;


        rows.forEach(function (row) {

            if (row.querySelector(".empty-data")) {
                return;
            }


            const text =
                row.innerText.toLowerCase();


            if (text.includes(keyword)) {

                row.style.display = "";

                jumlah++;

            } else {

                row.style.display = "none";

            }

        });


        jumlahData.textContent = jumlah;

    });

});

</script>


<?php

require "../template/footer.php";

?>