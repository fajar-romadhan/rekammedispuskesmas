<?php  

session_start(); 

require "../template/rbac.php";

cekAkses([ROLE_ADMIN, ROLE_PETUGAS, ROLE_BIDAN]);

require "../config.php"; 

$title = "Pasien - rekammedispuskesmas"; 

require "../template/header.php"; 
require "../template/navbar.php"; 
require "../template/sidebar.php"; 


if ($dataUser['jabatan'] == 3) { 
    echo "<script>  
        alert('Halaman tidak ditemukan..');    
        window.location = '../index.php'; 
    </script>"; 
    exit(); 
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
        margin-right: 10px;
        color: #7571f9;
    }


    /* =========================================
       TOMBOL PASIEN BARU
    ========================================= */

    .btn-pasien-baru {
        border-radius: 8px;
        padding: 8px 15px;
        font-weight: 500;
        transition: all .2s ease;
    }

    .btn-pasien-baru i {
        margin-right: 5px;
    }

    .btn-pasien-baru:hover {
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
       CARD TABEL
    ========================================= */

    .table-container {
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 18px rgba(0,0,0,.07);
        overflow-x: auto;
        border: 1px solid #f0f0f0;
    }


    /* =========================================
       TABEL
    ========================================= */

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
        transition: all .15s ease;
    }

    .patient-table tbody tr:hover {
        background: #f8f8ff;
    }

    .patient-table tbody tr:last-child td {
        border-bottom: none;
    }


    /* =========================================
       ID PASIEN
    ========================================= */

    .patient-id {
        display: inline-block;
        background: #eeeeff;
        color: #7571f9;
        padding: 5px 9px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 600;
    }


    /* =========================================
       NAMA PASIEN
    ========================================= */

    .patient-name {
        font-weight: 600;
        color: #343540;
    }


    /* =========================================
       JENIS KELAMIN
    ========================================= */

    .gender-badge {
        display: inline-block;
        padding: 5px 10px;
        border-radius: 20px;
        background: #f1f1f5;
        color: #494a57;
        font-size: 12px;
        font-weight: 500;
    }


    /* =========================================
       TELEPON
    ========================================= */

    .phone-number {
        white-space: nowrap;
        color: #494a57;
    }

    .phone-number i {
        margin-right: 5px;
        color: #6c757d;
    }


    /* =========================================
       ALAMAT
    ========================================= */

    .patient-address {
        max-width: 220px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }


    /* =========================================
       TOMBOL AKSI
    ========================================= */

    .action-buttons {
        display: flex;
        gap: 5px;
        white-space: nowrap;
    }

    .action-buttons .btn {
        width: 34px;
        height: 34px;
        padding: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 7px;
        transition: all .2s ease;
    }

    .action-buttons .btn:hover {
        transform: translateY(-1px);
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

    <div class="page-header d-flex justify-content-between 
                align-items-center">

        <h1 class="page-title">
            <i class="bi bi-people-fill"></i>
            Data Pasien
        </h1>

    </div>


    <!-- =====================================
         TOMBOL TAMBAH PASIEN
    ====================================== -->

    <?php if(userHasAnyRole([1,2,4])){ ?>

        <a href="<?= $main_url ?>pasien/tambah-pasien.php"
           class="btn btn-primary btn-sm btn-pasien-baru mb-3"
           title="Tambah pasien baru">

            <i class="bi bi-person-plus-fill"></i>
            Pasien Baru

        </a>

    <?php } ?>


    <!-- =====================================
         SEARCH PASIEN
    ====================================== -->

    <div class="search-wrapper">

        <div class="input-group">

            <span class="input-group-text">
                <i class="bi bi-search"></i>
            </span>

            <input type="text" 
                   id="searchPasien" 
                   class="form-control" 
                   placeholder="Cari nama pasien...">

        </div>

    </div>


    <!-- =====================================
         TABEL PASIEN
    ====================================== -->

    <div class="table-container">

        <table class="table patient-table" id="myTable">

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
                    "SELECT * FROM tbl_pasien ORDER BY id DESC"
                );

                while ($pasien = mysqli_fetch_assoc($queryPasien)) {

                ?>

                <tr>

                    <!-- NO -->
                    <td>
                        <?= $no++; ?>
                    </td>


                    <!-- ID PASIEN -->
                    <td>

                        <span class="patient-id">
                            <?= htmlspecialchars($pasien['id']) ?>
                        </span>

                    </td>


                    <!-- NAMA -->
                    <td>

                        <span class="patient-name">
                            <?= htmlspecialchars($pasien['nama']) ?>
                        </span>

                    </td>


                    <!-- TANGGAL LAHIR -->
                    <td>

                        <?= in_date($pasien['tgl_lahir']) ?>

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

                            <?= htmlspecialchars($pasien['telpon']) ?>

                        </span>

                    </td>


                    <!-- ALAMAT -->
                    <td>

                        <div class="patient-address"
                             title="<?= htmlspecialchars($pasien['alamat']) ?>">

                            <?= htmlspecialchars($pasien['alamat']) ?>

                        </div>

                    </td>


                    <!-- AKSI -->
                    <td>

                        <div class="action-buttons justify-content-center">

                            <?php if(userHasAnyRole([1,2,4])){ ?>

                                <!-- EDIT -->
                                <a href="edit-pasien.php?id=<?= $pasien['id'] ?>"
                                   class="btn btn-sm btn-outline-warning"
                                   title="Edit pasien">

                                    <i class="bi bi-pencil"></i>

                                </a>

                            <?php } ?>


                            <?php if(userHasAnyRole([1,2])){ ?>

                                <!-- HAPUS -->
                                <a href="proses-pasien.php?id=<?= $pasien['id'] ?>&aksi=hapus-pasien"
                                   onclick="return confirm('Anda yakin mau menghapus pasien ini?')"
                                   class="btn btn-sm btn-outline-danger"
                                   title="Hapus pasien">

                                    <i class="bi bi-trash"></i>

                                </a>

                            <?php } ?>

                        </div>

                    </td>

                </tr>

                <?php } ?>

            </tbody>

        </table>

    </div>

</div>


<script>

$(document).ready(function () {

    $('#searchPasien').on('keyup', function () {

        var keyword = $(this).val().toLowerCase();

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