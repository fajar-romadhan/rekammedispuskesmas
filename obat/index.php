<?php  

session_start(); 

require "../template/rbac.php";

// Hanya Petugas
cekAkses([ROLE_PETUGAS]);

require "../config.php"; 

$title = "Obat - rekammedispuskesmas"; 

require "../template/header.php"; 
require "../template/navbar.php"; 
require "../template/sidebar.php"; 


/* =========================================
   CEK HAK AKSES
========================================= */

if ($dataUser['jabatan'] == 3) {

    echo "<script>  
        alert('Halaman tidak ditemukan..');    
        window.location = '../index.php'; 
    </script>"; 

    exit(); 
}


/* =========================================
   PESAN
========================================= */

$msg = isset($_GET['msg']) ? $_GET['msg'] : '';

$alert = "";


if ($msg == 'deleted') {

    $alert = '
    <div class="alert alert-success alert-dismissible fade show custom-alert"
         role="alert">

        <i class="bi bi-check-circle-fill me-2"></i>

        <strong>Hapus data obat berhasil.</strong>

        <button type="button"
                class="btn-close"
                data-dismiss="alert"
                aria-label="Close">
        </button>

    </div>';

}


if ($msg == 'update') {

    $alert = '
    <div class="alert alert-success alert-dismissible fade show custom-alert update"
         role="alert">

        <i class="bi bi-check-circle-fill me-2"></i>

        <strong>Edit data obat berhasil.</strong>

        <button type="button"
                class="btn-close"
                data-dismiss="alert"
                aria-label="Close">
        </button>

    </div>';

}

?>


<style>

/* =========================================
   HEADER HALAMAN
========================================= */

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

    color: #7571f9;

    margin-right: 10px;

}


/* =========================================
   ALERT
========================================= */

.custom-alert {

    border: none;

    border-radius: 10px;

    box-shadow: 0 3px 12px rgba(0,0,0,.06);

    font-size: 14px;

}


/* =========================================
   TOMBOL OBAT BARU
========================================= */

.btn-obat-baru {

    border-radius: 8px;

    padding: 8px 15px;

    font-weight: 500;

    transition: .2s;

}

.btn-obat-baru i {

    margin-right: 5px;

}

.btn-obat-baru:hover {

    transform: translateY(-1px);

    box-shadow: 0 3px 8px rgba(0,0,0,.12);

}


/* =========================================
   SEARCH
========================================= */

.search-wrapper {

    max-width: 500px;

    margin-bottom: 20px;

}

.search-wrapper .input-group {

    background: white;

    border-radius: 10px;

    overflow: hidden;

    box-shadow: 0 3px 12px rgba(0,0,0,.06);

}

.search-wrapper .input-group-text {

    background: white;

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


/* =========================================
   TABLE CONTAINER
========================================= */

.table-container {

    background: white;

    border-radius: 12px;

    border: 1px solid #f0f0f0;

    box-shadow: 0 4px 18px rgba(0,0,0,.07);

    overflow-x: auto;

}


/* =========================================
   TABLE
========================================= */

.obat-table {

    margin-bottom: 0;

    vertical-align: middle;

    min-width: 700px;

}

.obat-table thead th {

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

.obat-table tbody td {

    padding: 14px 12px;

    font-size: 14px;

    color: #494a57;

    border-bottom: 1px solid #f0f0f0;

}

.obat-table tbody tr {

    transition: .15s ease;

}

.obat-table tbody tr:hover {

    background: #f8f8ff;

}

.obat-table tbody tr:last-child td {

    border-bottom: none;

}


/* =========================================
   NAMA OBAT
========================================= */

.nama-obat {

    font-weight: 600;

    color: #343540;

}

.nama-obat i {

    color: #7571f9;

    margin-right: 7px;

}


/* =========================================
   KEGUNAAN
========================================= */

.kegunaan-obat {

    max-width: 350px;

    line-height: 1.5;

}


/* =========================================
   STOK
========================================= */

/* .stock-badge / .stock-habis/warning/aman sekarang didefinisikan
   secara global di template/header.php (dipakai juga di
   obat/edit-obat.php) supaya konsisten. */


/* =========================================
   KATEGORI
========================================= */

.kategori-badge {

    display: inline-flex;
    align-items: center;
    gap: 5px;

    padding: 5px 10px;

    border-radius: 7px;

    font-size: 12px;
    font-weight: 600;

    white-space: nowrap;

}

.kategori-obat {

    background: #eaf7ef;
    color: #198754;

}

.kategori-tindakan {

    background: #eae9ff;
    color: #7571f9;

}


/* =========================================
   FILTER KATEGORI
========================================= */

.filter-kategori-wrapper {

    display: flex;
    gap: 8px;
    flex-wrap: wrap;

}

.filter-kategori-btn {

    display: inline-flex;
    align-items: center;
    gap: 6px;

    padding: 8px 15px;

    border-radius: 9px;
    border: 1px solid #dedfe6;
    background: #ffffff;
    color: #56576d;

    font-size: 13.5px;
    font-weight: 600;

    transition: .2s;
    cursor: pointer;

}

.filter-kategori-btn:hover {

    border-color: #7571f9;
    color: #7571f9;

}

.filter-kategori-btn.active {

    background: #7571f9;
    border-color: #7571f9;
    color: #ffffff;

}

.filter-kategori-btn .filter-count {

    background: #f1f2f9;
    color: #7571f9;

    padding: 1px 8px;
    border-radius: 20px;

    font-size: 11.5px;

}

.filter-kategori-btn.active .filter-count {

    background: rgba(255,255,255,.25);
    color: #ffffff;

}


/* =========================================
   HARGA
========================================= */

.harga-obat {

    font-weight: 600;
    color: #343540;
    white-space: nowrap;

}


/* =========================================
   ACTION
========================================= */

.action-buttons {

    display: flex;

    gap: 5px;

    align-items: center;

    white-space: nowrap;

}

.action-buttons .btn {

    width: 35px;

    height: 35px;

    padding: 0;

    border-radius: 7px;

    display: flex;

    align-items: center;

    justify-content: center;

    transition: .2s;

}

.action-buttons .btn:hover {

    transform: translateY(-1px);

}


/* TOMBOL STOK */

.btn-stock {

    width: auto !important;

    padding: 0 10px !important;

}


/* =========================================
   EMPTY DATA
========================================= */

.empty-data {

    text-align: center;

    padding: 45px 20px !important;

    color: #6c757d;

}

.empty-data i {

    font-size: 40px;

    display: block;

    margin-bottom: 10px;

    color: #adaebd;

}


/* =========================================
   RESPONSIVE
========================================= */

@media (max-width: 768px) {

    .page-title {

        font-size: 21px;

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


    <!-- =====================================
         HEADER
    ====================================== -->

    <div class="page-header d-flex 
                justify-content-between 
                align-items-center">

        <h1 class="page-title">

            <i class="bi bi-capsule-pill"></i>

            Data Obat

        </h1>

    </div>



    <!-- =====================================
         ALERT
    ====================================== -->

    <?php

    if ($msg !== '') {

        echo $alert;

    }

    ?>



    <!-- =====================================
         BUTTON TAMBAH OBAT
    ====================================== -->

    <a href="<?= $main_url ?>obat/tambah-obat.php"
       class="btn btn-primary btn-sm btn-obat-baru mb-3"
       title="Tambah obat baru">

        <i class="bi bi-plus-lg me-1"></i>

        Obat Baru

    </a>



    <!-- =====================================
         FILTER KATEGORI
    ====================================== -->

    <div class="filter-kategori-wrapper mb-3">

        <button type="button"
                class="filter-kategori-btn active"
                data-filter="semua">

            Semua
            <span class="filter-count" id="countSemua">0</span>

        </button>

        <button type="button"
                class="filter-kategori-btn"
                data-filter="Obat">

            <i class="bi bi-capsule me-1"></i>
            Obat
            <span class="filter-count" id="countObat">0</span>

        </button>

        <button type="button"
                class="filter-kategori-btn"
                data-filter="Tindakan">

            <i class="bi bi-clipboard2-pulse me-1"></i>
            Tindakan
            <span class="filter-count" id="countTindakan">0</span>

        </button>

    </div>



    <!-- =====================================
         SEARCH
    ====================================== -->

    <div class="search-wrapper">

        <div class="input-group">

            <span class="input-group-text">

                <i class="bi bi-search"></i>

            </span>

            <input type="text"
                   id="searchObat"
                   class="form-control"
                   placeholder="Cari nama obat atau kegunaan...">

        </div>

    </div>



    <!-- =====================================
         TABLE
    ====================================== -->

    <div class="table-container">

        <table class="table obat-table"
               id="myTable">

            <thead>

                <tr>

                    <th>No</th>

                    <th>Nama</th>

                    <th>Kategori</th>

                    <th>Kegunaan</th>

                    <th>Harga</th>

                    <th>Stok</th>

                    <th class="text-center">
                        Aksi
                    </th>

                </tr>

            </thead>


            <tbody>


            <?php

            $no = 1;

            $queryObat = mysqli_query(
                $koneksi,
                "SELECT * FROM tbl_obat ORDER BY kategori ASC, nama ASC"
            );


            if (mysqli_num_rows($queryObat) == 0) {

            ?>

                <tr>

                    <td colspan="7"
                        class="empty-data">

                        <i class="bi bi-capsule"></i>

                        Belum ada data obat.

                    </td>

                </tr>

            <?php

            }


            while ($obat = mysqli_fetch_assoc($queryObat)) {

            ?>


                <tr class="data-obat-row" data-kategori="<?= htmlspecialchars($obat['kategori']); ?>">


                    <!-- NO -->

                    <td>

                        <?= $no++; ?>

                    </td>



                    <!-- NAMA OBAT -->

                    <td>

                        <span class="nama-obat">

                            <i class="bi bi-capsule"></i>

                            <?= htmlspecialchars(
                                $obat['nama']
                            ); ?>

                        </span>

                    </td>



                    <!-- KATEGORI -->

                    <td>

                        <?php if ($obat['kategori'] === 'Tindakan') { ?>

                            <span class="kategori-badge kategori-tindakan">
                                <i class="bi bi-clipboard2-pulse"></i>
                                Tindakan
                            </span>

                        <?php } else { ?>

                            <span class="kategori-badge kategori-obat">
                                <i class="bi bi-capsule"></i>
                                Obat
                            </span>

                        <?php } ?>

                    </td>



                    <!-- KEGUNAAN -->

                    <td>

                        <div class="kegunaan-obat">

                            <?= htmlspecialchars(
                                $obat['kegunaan']
                            ); ?>

                        </div>

                    </td>



                    <!-- HARGA -->

                    <td>

                        <span class="harga-obat">
                            Rp <?= number_format((float) $obat['harga'], 0, ',', '.'); ?>
                        </span>

                    </td>



                    <!-- STOK -->

                    <td>

                        <?php if ($obat['kategori'] === 'Tindakan') { ?>

                            <span class="text-muted">
                                &mdash;
                            </span>

                        <?php

                        } elseif ($obat['stok'] <= 0) {

                        ?>

                            <span class="stock-badge stock-habis">

                                <i class="bi bi-exclamation-circle"></i>

                                Habis

                            </span>

                        <?php

                        } elseif ($obat['stok'] <= 10) {

                        ?>

                            <span class="stock-badge stock-warning">

                                <i class="bi bi-exclamation-triangle"></i>

                                <?= $obat['stok']; ?>

                            </span>

                        <?php

                        } else {

                        ?>

                            <span class="stock-badge stock-aman">

                                <i class="bi bi-box-seam"></i>

                                <?= $obat['stok']; ?>

                            </span>

                        <?php

                        }

                        ?>

                    </td>



                    <!-- AKSI -->

                    <td>

                        <div class="action-buttons justify-content-center">


                            <!-- TAMBAH STOK (obat saja, tindakan tidak punya stok) -->

                            <?php if ($obat['kategori'] !== 'Tindakan') { ?>

                            <a href="tambah-stok.php?id=<?= $obat['id']; ?>"
                               class="btn btn-sm btn-outline-success btn-stock"
                               title="Tambah stok obat">

                                <i class="bi bi-plus-circle"></i>

                                <span class="ms-1">
                                    Stok
                                </span>

                            </a>

                            <?php } ?>



                            <!-- EDIT -->

                            <a href="edit-obat.php?id=<?= $obat['id']; ?>"
                               class="btn btn-sm btn-outline-warning"
                               title="Edit obat">

                                <i class="bi bi-pencil"></i>

                            </a>



                            <!-- HAPUS -->

                            <a href="proses-obat.php?id=<?= $obat['id']; ?>&aksi=hapus-obat"
                               onclick="return confirm('Anda yakin mau menghapus obat ini?')"
                               class="btn btn-sm btn-outline-danger"
                               title="Hapus obat">

                                <i class="bi bi-trash"></i>

                            </a>


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



<!-- =========================================
     SEARCH SCRIPT
========================================= -->

<script>

$(document).ready(function () {


    var kategoriAktif = 'semua';


    /* =========================================
       HITUNG JUMLAH PER KATEGORI (SEKALI SAAT LOAD)
    ========================================= */

    var jumlahSemua = 0;
    var jumlahObat = 0;
    var jumlahTindakan = 0;

    $('#myTable tbody tr.data-obat-row').each(function () {

        jumlahSemua++;

        if ($(this).data('kategori') === 'Tindakan') {
            jumlahTindakan++;
        } else {
            jumlahObat++;
        }

    });

    $('#countSemua').text(jumlahSemua);
    $('#countObat').text(jumlahObat);
    $('#countTindakan').text(jumlahTindakan);


    /* =========================================
       TERAPKAN FILTER (KATEGORI + PENCARIAN)
    ========================================= */

    function terapkanFilter() {

        var keyword = $('#searchObat')
            .val()
            .toLowerCase();

        $('#myTable tbody tr.data-obat-row').each(function () {

            var row = $(this);

            var cocokKategori =
                kategoriAktif === 'semua' ||
                row.data('kategori') === kategoriAktif;

            var nama = row
                .find('td:eq(1)')
                .text()
                .toLowerCase();

            var kegunaan = row
                .find('td:eq(3)')
                .text()
                .toLowerCase();

            var cocokKeyword =
                nama.indexOf(keyword) !== -1 ||
                kegunaan.indexOf(keyword) !== -1;

            row.toggle(cocokKategori && cocokKeyword);

        });

    }


    /* =========================================
       KLIK CHIP FILTER KATEGORI
    ========================================= */

    $('.filter-kategori-btn').on('click', function () {

        $('.filter-kategori-btn').removeClass('active');

        $(this).addClass('active');

        kategoriAktif = $(this).data('filter');

        terapkanFilter();

    });


    /* =========================================
       PENCARIAN
    ========================================= */

    $('#searchObat').on('keyup', terapkanFilter);


});


/* =========================================
   ALERT AUTO HILANG
========================================= */

window.setTimeout(function () {

    $('.update').fadeOut();

}, 5000);

</script>



<?php

require "../template/footer.php";

?>