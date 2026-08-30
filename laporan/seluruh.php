<?php

session_start();

require "../template/rbac.php";

// Hanya Kepala Puskesmas
cekAkses([ROLE_KEPALA]);

require "../config.php";

$title = "Laporan Seluruh Pasien - rekammedispuskesmas";

require "../template/header.php";
require "../template/navbar.php";
require "../template/sidebar.php";


// ======================================================
// DATA SELURUH PASIEN
// ======================================================

$query = mysqli_query($koneksi, "
    SELECT *
    FROM tbl_pasien
    ORDER BY nama ASC
");

$totalPasien = mysqli_num_rows($query);

?>

<style>

/* ======================================================
   CONTAINER
====================================================== */

.laporan-wrapper {
    padding: 5px 0 30px;
}


/* ======================================================
   CARD
====================================================== */

.laporan-card {
    background: #ffffff;
    border: 1px solid #e1e1ef;
    border-radius: 16px;
    box-shadow: 0 4px 15px rgba(30, 60, 90, 0.04);
}


/* ======================================================
   HEADER
====================================================== */

.header-card {
    padding: 20px 25px;
    margin-bottom: 18px;
}

.header-content {
    display: flex;
    align-items: center;
    gap: 14px;
}

.header-icon {
    width: 50px;
    height: 50px;
    border-radius: 12px;
    background: #eaeaff;

    display: flex;
    align-items: center;
    justify-content: center;
}

.header-icon i {
    font-size: 23px;
    color: #7571f9;
}

.header-text h1 {
    margin: 0;
    font-size: 23px;
    font-weight: 700;
    color: #242753;
}

.header-text p {
    margin: 3px 0 0;
    font-size: 13px;
    color: #727196;
}


/* ======================================================
   INFO
====================================================== */

.info-row {
    display: grid;
    grid-template-columns: 30% 70%;
    gap: 18px;
    margin-bottom: 18px;
}


/* ======================================================
   TOTAL
====================================================== */

.total-card {
    padding: 20px 23px;
    min-height: 135px;

    display: flex;
    align-items: center;
}

.total-content {
    display: flex;
    align-items: center;
    gap: 15px;
}

.total-icon {
    width: 50px;
    height: 50px;
    border-radius: 12px;
    background: #e9f8f0;

    display: flex;
    align-items: center;
    justify-content: center;
}

.total-icon i {
    font-size: 23px;
    color: #159957;
}

.total-label {
    font-size: 13px;
    color: #727196;
    margin-bottom: 4px;
}

.total-number {
    font-size: 23px;
    font-weight: 700;
    color: #242753;
}

.total-number span {
    font-size: 13px;
    font-weight: 500;
}


/* ======================================================
   SEARCH
====================================================== */

.search-card {
    padding: 20px 25px;
    min-height: 135px;
}

.search-title {
    display: flex;
    align-items: center;
    gap: 9px;

    font-size: 15px;
    font-weight: 600;
    color: #242753;

    margin-bottom: 10px;
}

.search-title i {
    font-size: 19px;
    color: #525584;
}

.search-box {
    position: relative;
}

.search-box > i {
    position: absolute;

    left: 16px;
    top: 50%;

    transform: translateY(-50%);

    font-size: 20px;
    color: #6f73a3;
}

.search-box input {
    width: 100%;
    height: 50px;

    border: 1px solid #d8d9eb;
    border-radius: 12px;

    padding: 0 16px 0 48px;

    font-size: 14px;
    color: #333768;

    outline: none;
}

.search-box input:focus {
    border-color: #b3aefc;

    box-shadow:
        0 0 0 3px rgba(117, 113, 249, .07);
}

.search-info {
    margin-top: 7px;

    font-size: 12px;

    color: #727196;
}

.search-info strong {
    color: #7571f9;
}


/* ======================================================
   CETAK PDF
====================================================== */

.print-card {
    padding: 20px 25px;
    margin-bottom: 18px;
}

.print-title {
    display: flex;
    align-items: center;
    gap: 9px;

    font-size: 15px;
    font-weight: 600;

    color: #242753;

    margin-bottom: 13px;
}

.print-title i {
    color: #d9344b;
    font-size: 19px;
}

.print-buttons {
    display: flex;
    gap: 9px;
}

.btn-pdf {
    display: inline-flex;
    align-items: center;
    gap: 7px;

    padding: 8px 16px;

    border-radius: 9px;

    background: #dc3545;
    color: #ffffff;

    font-size: 13px;
    font-weight: 600;

    text-decoration: none;

    transition: .2s;
}

.btn-pdf i {
    font-size: 15px;
}

.btn-pdf:hover {
    background: #c82333;
    color: #ffffff;
    transform: translateY(-1px);
}


/* ======================================================
   DATA CARD
====================================================== */

.data-card {
    padding: 20px 25px;
}

.data-header {
    margin-bottom: 15px;
}

.data-title {
    display: flex;
    align-items: center;
    gap: 9px;

    font-size: 18px;
    font-weight: 700;

    color: #242753;
}

.data-title i {
    font-size: 20px;
}

.data-subtitle {
    margin-top: 3px;

    font-size: 12px;

    color: #7b7da1;
}


/* ======================================================
   TABLE
====================================================== */

.table-container {
    overflow-x: auto;

    border: 1px solid #e3e4ef;

    border-radius: 11px;
}

#tableSeluruh {
    margin: 0;

    min-width: 950px;
}

#tableSeluruh thead th {
    background: #f1f2f9;

    color: #3c3f62;

    font-size: 12px;
    font-weight: 700;

    padding: 11px;

    border-bottom: 1px solid #dcddec;

    white-space: nowrap;
}

#tableSeluruh tbody td {
    padding: 11px;

    font-size: 12px;

    color: #46486c;

    border-bottom: 1px solid #edeef5;

    vertical-align: middle;
}

#tableSeluruh tbody tr:last-child td {
    border-bottom: none;
}

#tableSeluruh tbody tr:hover {
    background: #f8f8ff;
}

#tableSeluruh tbody td:nth-child(3) {
    font-weight: 600;
    color: #262950;
}


/* ======================================================
   BADGE NO RM
====================================================== */

.rm-badge {
    display: inline-block;

    padding: 4px 7px;

    border-radius: 6px;

    background: #f1f2f9;

    border: 1px solid #dcddec;

    color: #454770;

    font-size: 11px;

    font-weight: 600;
}


/* ======================================================
   BADGE JENIS KELAMIN
====================================================== */

.gender-badge {
    display: inline-block;

    padding: 4px 7px;

    border-radius: 6px;

    background: #f3f3fa;

    color: #525479;

    font-size: 11px;
}


/* ======================================================
   BADGE ASURANSI
====================================================== */

.insurance-badge {
    display: inline-block;

    padding: 4px 7px;

    border-radius: 6px;

    background: #f8f8fa;

    border: 1px solid #e1e2e9;

    color: #525479;

    font-size: 11px;
}


/* ======================================================
   DATA KOSONG
====================================================== */

.empty-data {
    text-align: center;

    padding: 30px !important;

    color: #8283a5 !important;
}

.empty-data i {
    display: block;

    font-size: 28px;

    margin-bottom: 6px;
}


/* ======================================================
   RESPONSIVE
====================================================== */

@media (max-width: 992px) {

    .info-row {
        grid-template-columns: 1fr;
    }

}

@media (max-width: 768px) {

    .header-card,
    .total-card,
    .search-card,
    .print-card,
    .data-card {
        padding: 18px;
    }

    .header-text h1 {
        font-size: 21px;
    }

    .print-buttons {
        flex-wrap: wrap;
    }

}

</style>


<div class="page-content-wrap">

<div class="laporan-wrapper">


    <!-- ==================================================
         HEADER
    ================================================== -->

    <div class="laporan-card header-card">

        <div class="header-content">

            <div class="header-icon">

                <i class="bi bi-people-fill"></i>

            </div>

            <div class="header-text">

                <h1>
                    Laporan Seluruh Pasien
                </h1>

                <p>
                    Data seluruh pasien yang terdaftar pada sistem.
                </p>

            </div>

        </div>

    </div>


    <!-- ==================================================
         TOTAL + SEARCH
    ================================================== -->

    <div class="info-row">


        <!-- TOTAL PASIEN -->

        <div class="laporan-card total-card">

            <div class="total-content">

                <div class="total-icon">

                    <i class="bi bi-people-fill"></i>

                </div>

                <div>

                    <div class="total-label">
                        Total Seluruh Pasien
                    </div>

                    <div class="total-number">

                        <?= $totalPasien ?>

                        <span>
                            Pasien
                        </span>

                    </div>

                </div>

            </div>

        </div>


        <!-- SEARCH -->

        <div class="laporan-card search-card">

            <div class="search-title">

                <i class="bi bi-search"></i>

                <span>
                    Cari Data Pasien
                </span>

            </div>


            <div class="search-box">

                <i class="bi bi-search"></i>

                <input
                    type="text"
                    id="searchSeluruh"
                    placeholder="Cari nama, No RM, NIK, alamat, atau asuransi..."
                    autocomplete="off"
                >

            </div>


            <div class="search-info">

                Menampilkan

                <strong id="jumlahData">
                    <?= $totalPasien ?>
                </strong>

                data pasien

            </div>

        </div>

    </div>


    <!-- ==================================================
         CETAK LAPORAN
    ================================================== -->

    <div class="laporan-card print-card">

        <div class="print-title">

            <i class="bi bi-file-earmark-pdf"></i>

            <span>
                Cetak Laporan
            </span>

        </div>


        <div class="print-buttons">

            <a
                href="<?= $main_url ?>laporan/seluruh-pdf.php?periode=mingguan"
                class="btn-pdf"
                target="_blank"
            >

                <i class="bi bi-file-earmark-pdf"></i>

                PDF Mingguan

            </a>


            <a
                href="<?= $main_url ?>laporan/seluruh-pdf.php?periode=bulanan"
                class="btn-pdf"
                target="_blank"
            >

                <i class="bi bi-file-earmark-pdf"></i>

                PDF Bulanan

            </a>

        </div>

    </div>


    <!-- ==================================================
         DATA SELURUH PASIEN
    ================================================== -->

    <div class="laporan-card data-card">

        <div class="data-header">

            <div class="data-title">

                <i class="bi bi-table"></i>

                <span>
                    Data Seluruh Pasien
                </span>

            </div>

            <div class="data-subtitle">

                Daftar seluruh pasien yang telah terdaftar.

            </div>

        </div>


        <div class="table-container">

            <table
                class="table align-middle"
                id="tableSeluruh"
            >

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

                if ($totalPasien > 0) {

                    $no = 1;

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

                            <?= htmlspecialchars(
                                $data['nama']
                            ); ?>

                        </td>


                        <td>

                            <?= !empty($data['tgl_lahir'])
                                ? date(
                                    'd-m-Y',
                                    strtotime($data['tgl_lahir'])
                                )
                                : '-';
                            ?>

                        </td>


                        <td>

                            <span class="gender-badge">

                                <?= $data['gender'] == 'P'
                                    ? 'Pria'
                                    : 'Wanita';
                                ?>

                            </span>

                        </td>


                        <td>

                            <?php if (
                                !empty($data['no_asuransi'])
                            ) { ?>

                                <span class="insurance-badge">

                                    <?= htmlspecialchars(
                                        $data['no_asuransi']
                                    ); ?>

                                </span>

                            <?php } else { ?>

                                <span class="text-muted">
                                    -
                                </span>

                            <?php } ?>

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

                        <td
                            colspan="8"
                            class="empty-data"
                        >

                            <i class="bi bi-inbox"></i>

                            Belum terdapat data pasien.

                        </td>

                    </tr>

                <?php } ?>

                </tbody>

            </table>

        </div>

    </div>


</div>

</div>


<script>

/* ======================================================
   PENCARIAN DATA
====================================================== */

document
    .getElementById('searchSeluruh')
    .addEventListener('keyup', function () {

        let keyword = this.value
            .toLowerCase()
            .trim();

        let rows = document.querySelectorAll(
            '#tableSeluruh tbody tr'
        );

        let jumlah = 0;

        rows.forEach(function (row) {

            let text = row.innerText.toLowerCase();

            if (text.includes(keyword)) {

                row.style.display = '';

                jumlah++;

            } else {

                row.style.display = 'none';

            }

        });


        document
            .getElementById('jumlahData')
            .innerText = jumlah;

    });

</script>


<?php

require "../template/footer.php";

?>