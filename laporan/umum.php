<?php

session_start();

require "../template/rbac.php";

// Hanya Kepala Puskesmas
cekAkses([ROLE_KEPALA]);

require "../config.php";

$title = "Laporan Umum - rekammedispuskesmas";

require "../template/header.php";
require "../template/navbar.php";
require "../template/sidebar.php";


// ======================================================
// DATA PASIEN UMUM
// ======================================================

$query = mysqli_query($koneksi, "
    SELECT *
    FROM tbl_pasien
    WHERE no_asuransi IS NULL
       OR no_asuransi = ''
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
    background: #fff;
    border: 1px solid #e1e1ef;
    border-radius: 18px;
    box-shadow: 0 5px 18px rgba(30, 60, 90, 0.035);
}


/* ======================================================
   HEADER
====================================================== */

.header-card {
    padding: 22px 28px;
    margin-bottom: 20px;
}

.header-content {
    display: flex;
    align-items: center;
    gap: 16px;
}

.header-icon {
    width: 56px;
    height: 56px;
    border-radius: 14px;
    background: #eaeaff;

    display: flex;
    align-items: center;
    justify-content: center;
}

.header-icon i {
    font-size: 25px;
    color: #7571f9;
}

.header-text h1 {
    margin: 0;
    font-size: 26px;
    font-weight: 700;
    color: #242753;
}

.header-text p {
    margin: 4px 0 0;
    font-size: 14px;
    color: #727196;
}


/* ======================================================
   INFO ROW
====================================================== */

.info-row {
    display: grid;
    grid-template-columns: 31% 69%;
    gap: 20px;
    margin-bottom: 20px;
}


/* ======================================================
   TOTAL CARD
====================================================== */

.total-card {
    padding: 23px 25px;
    min-height: 150px;

    display: flex;
    align-items: center;
}

.total-content {
    display: flex;
    align-items: center;
    gap: 17px;
}

.total-icon {
    width: 54px;
    height: 54px;
    border-radius: 14px;
    background: #e9f8f0;

    display: flex;
    align-items: center;
    justify-content: center;
}

.total-icon i {
    font-size: 25px;
    color: #159957;
}

.total-label {
    font-size: 14px;
    color: #727196;
    margin-bottom: 5px;
}

.total-number {
    font-size: 25px;
    font-weight: 700;
    color: #242753;
}

.total-number span {
    font-size: 14px;
    font-weight: 500;
}


/* ======================================================
   SEARCH CARD
====================================================== */

.search-card {
    padding: 22px 27px;
    min-height: 150px;
}

.search-title {
    display: flex;
    align-items: center;
    gap: 10px;

    font-size: 16px;
    font-weight: 600;
    color: #242753;

    margin-bottom: 11px;
}

.search-title i {
    font-size: 21px;
    color: #525584;
}

.search-box {
    position: relative;
}

.search-box > i {
    position: absolute;
    left: 18px;
    top: 50%;
    transform: translateY(-50%);

    font-size: 23px;
    color: #6f73a3;
}

.search-box input {
    width: 100%;
    height: 56px;

    border: 1px solid #d8d9eb;
    border-radius: 14px;

    padding: 0 18px 0 54px;

    font-size: 16px;
    color: #333768;

    outline: none;
}

.search-box input:focus {
    border-color: #b3aefc;
    box-shadow: 0 0 0 3px rgba(117, 113, 249, .07);
}

.search-info {
    margin-top: 8px;
    font-size: 13px;
    color: #727196;
}

.search-info strong {
    color: #7571f9;
}


/* ======================================================
   CETAK
====================================================== */

.print-card {
    padding: 22px 27px;
    margin-bottom: 20px;
}

.print-title {
    display: flex;
    align-items: center;
    gap: 10px;

    font-size: 16px;
    font-weight: 600;

    color: #242753;

    margin-bottom: 15px;
}

.print-title i {
    color: #1223a0;
    font-size: 20px;
}

.print-buttons {
    display: flex;
    gap: 10px;
}

.btn-pdf {
    display: inline-flex;
    align-items: center;
    gap: 8px;

    padding: 10px 20px;

    border-radius: 11px;

    background: #e9344b;
    color: #fff;

    font-size: 14px;
    font-weight: 600;

    text-decoration: none;

    transition: .2s;
}

.btn-pdf i {
    font-size: 17px;
}

.btn-pdf:hover {
    background: #d9273e;
    color: #fff;
    transform: translateY(-1px);
}


/* ======================================================
   DATA CARD
====================================================== */

.data-card {
    padding: 23px 27px;
}

.data-header {
    margin-bottom: 17px;
}

.data-title {
    display: flex;
    align-items: center;
    gap: 10px;

    font-size: 20px;
    font-weight: 700;

    color: #242753;
}

.data-title i {
    font-size: 22px;
}

.data-subtitle {
    margin-top: 4px;
    font-size: 14px;
    color: #7b7da1;
}


/* ======================================================
   TABLE
====================================================== */

.table-container {
    overflow-x: auto;

    border: 1px solid #e3e4ef;
    border-radius: 13px;
}

#tableUmum {
    margin: 0;
    min-width: 900px;
}

#tableUmum thead th {
    background: #f1f2f9;

    color: #3c3f62;

    font-size: 13px;
    font-weight: 700;

    padding: 12px;

    border-bottom: 1px solid #dcddec;

    white-space: nowrap;
}

#tableUmum tbody td {
    padding: 12px;

    font-size: 13px;

    color: #46486c;

    border-bottom: 1px solid #edeef5;

    vertical-align: middle;
}

#tableUmum tbody tr:last-child td {
    border-bottom: none;
}

#tableUmum tbody tr:hover {
    background: #f8f8ff;
}

#tableUmum tbody td:nth-child(3) {
    font-weight: 600;
    color: #262950;
}


/* ======================================================
   BADGE
====================================================== */

.rm-badge {
    display: inline-block;

    padding: 4px 8px;

    border-radius: 7px;

    background: #f1f2f9;
    border: 1px solid #dcddec;

    color: #454770;

    font-size: 12px;
    font-weight: 600;
}

.gender-badge {
    display: inline-block;

    padding: 4px 8px;

    border-radius: 7px;

    background: #f3f3fa;

    color: #525479;

    font-size: 12px;
}


/* ======================================================
   EMPTY
====================================================== */

.empty-data {
    text-align: center;

    padding: 30px !important;

    color: #8283a5 !important;
}

.empty-data i {
    display: block;

    font-size: 30px;

    margin-bottom: 7px;
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
        padding: 20px;
    }

    .header-text h1 {
        font-size: 23px;
    }

    .header-text p {
        font-size: 13px;
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

                <i class="bi bi-clipboard2-check"></i>

            </div>

            <div class="header-text">

                <h1>
                    Laporan Umum
                </h1>

                <p>
                    Data pasien yang terdaftar sebagai pasien umum.
                </p>

            </div>

        </div>

    </div>


    <!-- ==================================================
         TOTAL + SEARCH
    ================================================== -->

    <div class="info-row">


        <!-- TOTAL -->

        <div class="laporan-card total-card">

            <div class="total-content">

                <div class="total-icon">

                    <i class="bi bi-people-fill"></i>

                </div>

                <div>

                    <div class="total-label">
                        Total Pasien Umum
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
                    Cari Data Pasien Umum
                </span>

            </div>


            <div class="search-box">

                <i class="bi bi-search"></i>

                <input
                    type="text"
                    id="searchUmum"
                    placeholder="Cari nama, No RM, NIK, atau alamat..."
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
                Cetak Laporan Umum
            </span>

        </div>


        <div class="print-buttons">

            <a
                href="<?= $main_url ?>laporan/umum-pdf.php?periode=mingguan"
                class="btn-pdf"
                target="_blank"
            >

                <i class="bi bi-file-earmark-pdf"></i>

                PDF Mingguan

            </a>


            <a
                href="<?= $main_url ?>laporan/umum-pdf.php?periode=bulanan"
                class="btn-pdf"
                target="_blank"
            >

                <i class="bi bi-file-earmark-pdf"></i>

                PDF Bulanan

            </a>

        </div>

    </div>


    <!-- ==================================================
         DATA PASIEN
    ================================================== -->

    <div class="laporan-card data-card">

        <div class="data-header">

            <div class="data-title">

                <i class="bi bi-table"></i>

                <span>
                    Data Pasien Umum
                </span>

            </div>

            <div class="data-subtitle">

                Daftar pasien berdasarkan data pasien umum.

            </div>

        </div>


        <div class="table-container">

            <table
                class="table align-middle"
                id="tableUmum"
            >

                <thead>

                    <tr>

                        <th>No</th>

                        <th>No RM</th>

                        <th>Nama Pasien</th>

                        <th>Tanggal Lahir</th>

                        <th>Jenis Kelamin</th>

                        <th>Alamat</th>

                        <th>NIK</th>

                    </tr>

                </thead>


                <tbody>

                <?php

                $no = 1;

                if ($totalPasien > 0) {

                    while ($data = mysqli_fetch_assoc($query)) {

                ?>

                    <tr>

                        <td>
                            <?= $no++; ?>
                        </td>


                        <td>

                            <span class="rm-badge">

                                <?= htmlspecialchars($data['no_rm']); ?>

                            </span>

                        </td>


                        <td>

                            <?= htmlspecialchars($data['nama']); ?>

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

                            <?= htmlspecialchars($data['alamat']); ?>

                        </td>


                        <td>

                            <?= htmlspecialchars($data['nik']); ?>

                        </td>

                    </tr>

                <?php

                    }

                } else {

                ?>

                    <tr>

                        <td
                            colspan="7"
                            class="empty-data"
                        >

                            <i class="bi bi-inbox"></i>

                            Belum terdapat data pasien umum.

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
   SEARCH
====================================================== */

document
    .getElementById('searchUmum')
    .addEventListener('keyup', function () {

        let keyword = this.value.toLowerCase().trim();

        let rows = document.querySelectorAll(
            '#tableUmum tbody tr'
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