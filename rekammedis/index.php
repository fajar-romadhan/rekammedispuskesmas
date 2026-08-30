<?php  

session_start(); 

require "../template/rbac.php";

cekAkses([ROLE_ADMIN, ROLE_PETUGAS, ROLE_DOKTER, ROLE_BIDAN]);

require "../config.php";

$title = "Data - rekammedispuskesmas"; 

require "../template/header.php"; 
require "../template/navbar.php"; 
require "../template/sidebar.php"; 


if (isset($_GET['msg'])) { 
    $msg = $_GET['msg']; 
} else { 
    $msg = ''; 
} 


$alert = "";

if ($msg == 'added') {

    $alert = '<div class="alert alert-success alert-dismissible fade show update" role="alert">
        <strong>
            <i class="bi bi-bag-check-fill me-2"></i>
            Tambah Data Rekam Medis Berhasil..
        </strong>
        <button type="button" class="btn-close" data-dismiss="alert" aria-label="Close"></button>
    </div>';

}


if ($msg == 'deleted') {

    $alert = '<div class="alert alert-success alert-dismissible fade show" role="alert"> 
        <strong>
            <i class="bi bi-bag-check-fill me-2"></i>
            Hapus Data Rekam Medis Berhasil..
        </strong>  
        <button type="button" class="btn-close" data-dismiss="alert" aria-label="Close"></button> 
    </div>'; 

} 


if ($msg == 'update') { 

    $alert = '<div class="alert alert-success alert-dismissible fade show update" role="alert"> 
        <strong>
            <i class="bi bi-bag-check-fill me-2"></i>
            Edit Data Rekam Medis Berhasil..
        </strong>  
    </div>'; 

}

?>

<style>

/* =========================================
   HALAMAN REKAM MEDIS
========================================= */

.rm-page {
    padding-bottom: 40px;
}


/* =========================================
   HEADER
========================================= */

.rm-page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;

    padding-top: 25px;
    padding-bottom: 18px;
    margin-bottom: 22px;

    border-bottom: 1px solid #e9e9ef;
}

.rm-page-title {
    margin: 0;

    display: flex;
    align-items: center;

    font-size: 27px;
    font-weight: 600;

    color: #343540;
}

.rm-page-title i {
    display: flex;
    align-items: center;
    justify-content: center;

    width: 42px;
    height: 42px;

    margin-right: 12px;

    border-radius: 10px;

    background: #eeeeff;
    color: #7571f9;

    font-size: 22px;
}


/* =========================================
   ALERT
========================================= */

.rm-alert {
    margin-bottom: 20px;
}

.rm-alert .alert {
    border: none;
    border-radius: 10px;

    padding: 13px 16px;

    box-shadow: 0 3px 12px rgba(0,0,0,.05);
}


/* =========================================
   TOOLBAR
========================================= */

.rm-toolbar {
    display: flex;
    justify-content: space-between;
    align-items: center;

    margin-bottom: 18px;
}


/* =========================================
   BUTTON TAMBAH
========================================= */

.rm-add-button {
    display: inline-flex;
    align-items: center;
    gap: 7px;

    padding: 8px 15px;

    border-radius: 8px;

    font-size: 13px;
    font-weight: 500;

    transition: all .2s ease;
}

.rm-add-button:hover {
    transform: translateY(-2px);

    box-shadow: 0 4px 10px rgba(0,0,0,.10);
}


/* =========================================
   CARD TABLE
========================================= */

.rm-table-card {
    background: #ffffff;

    border: 1px solid #e9e9ef;

    border-radius: 13px;

    overflow: hidden;

    box-shadow: 0 4px 16px rgba(0,0,0,.05);
}


/* =========================================
   TABLE WRAPPER
========================================= */

.rm-table-wrapper {
    width: 100%;

    overflow-x: auto;
}


/* =========================================
   TABLE
========================================= */

#myTable {
    width: 100%;

    min-width: 1340px;

    margin-bottom: 0;

    /* table-layout: fixed supaya lebar kolom (lihat <colgroup>) benar-
       benar ditegakkan, sehingga isi yang panjang melipat ke bawah
       (word-wrap) alih-alih mendorong tabel melebar ke samping. */
    table-layout: fixed;

    border-collapse: separate;
    border-spacing: 0;
}


/* =========================================
   HEADER TABLE
========================================= */

#myTable thead th {
    background: #f8f8fa;

    color: #494a57;

    font-size: 12px;
    font-weight: 600;

    text-transform: uppercase;

    letter-spacing: .2px;

    padding: 15px 13px;

    border-bottom: 1px solid #dedfe6;

    white-space: nowrap;

    vertical-align: middle;
}


/* =========================================
   BODY TABLE
========================================= */

#myTable tbody td {
    padding: 15px 13px;

    font-size: 13px;

    color: #494a57;

    border-bottom: 1px solid #f0f0f3;

    vertical-align: middle;
}


/* =========================================
   BARIS TABLE
========================================= */

#myTable tbody tr {
    transition: background .2s ease;
}

#myTable tbody tr:hover {
    background: #f8f8ff;
}

#myTable tbody tr:last-child td {
    border-bottom: none;
}


/* =========================================
   NO
========================================= */

#myTable tbody td:first-child {
    color: #6c757d;

    font-weight: 500;

    text-align: center;
}


/* =========================================
   NO RM
========================================= */

#myTable tbody td:nth-child(2) {
    color: #7571f9;

    font-weight: 600;

    white-space: nowrap;
}


/* =========================================
   TANGGAL
========================================= */

#myTable tbody td:nth-child(3) {
    white-space: nowrap;

    color: #5f6368;
}


/* =========================================
   NAMA PASIEN
========================================= */

#myTable tbody td:nth-child(4) {
    font-weight: 600;

    color: #343540;

    white-space: nowrap;
}


/* =========================================
   ALAMAT
========================================= */

#myTable tbody td:nth-child(5) {
    line-height: 1.5;

    white-space: normal;
    word-wrap: break-word;
    overflow-wrap: break-word;
    word-break: break-word;
}


/* =========================================
   KELUHAN
========================================= */

#myTable tbody td:nth-child(6) {
    line-height: 1.5;

    white-space: normal;
    word-wrap: break-word;
    overflow-wrap: break-word;
    word-break: break-word;
}


/* =========================================
   DOKTER
========================================= */

#myTable tbody td:nth-child(7) {
    white-space: nowrap;

    font-weight: 500;
}


/* =========================================
   DIAGNOSA
========================================= */

#myTable tbody td:nth-child(8) {
    line-height: 1.5;

    white-space: normal;
    word-wrap: break-word;
    overflow-wrap: break-word;
    word-break: break-word;
}


/* =========================================
   OBAT
========================================= */

#myTable tbody td:nth-child(9) {
    line-height: 1.5;

    white-space: normal;
    word-wrap: break-word;
    overflow-wrap: break-word;
    word-break: break-word;
}


/* =========================================
   KOLOM AKSI
========================================= */

#myTable tbody td:last-child {
    text-align: center;

    white-space: nowrap;
}


/* =========================================
   BUTTON AKSI
========================================= */

#myTable tbody td:last-child .btn {
    width: 35px;
    height: 35px;

    padding: 0;

    margin: 2px;

    display: inline-flex;

    align-items: center;
    justify-content: center;

    border-radius: 7px;

    transition: all .2s ease;
}


/* =========================================
   HOVER BUTTON AKSI
========================================= */

#myTable tbody td:last-child .btn:hover {
    transform: translateY(-2px);

    box-shadow: 0 4px 9px rgba(0,0,0,.12);
}


/* =========================================
   ICON BUTTON
========================================= */

#myTable tbody td:last-child .btn i {
    font-size: 15px;
}


/* =========================================
   SCROLLBAR
========================================= */

.rm-table-wrapper::-webkit-scrollbar {
    height: 7px;
}

.rm-table-wrapper::-webkit-scrollbar-track {
    background: #f1f1f5;
}

.rm-table-wrapper::-webkit-scrollbar-thumb {
    background: #cecfda;

    border-radius: 10px;
}

.rm-table-wrapper::-webkit-scrollbar-thumb:hover {
    background: #adaebd;
}


/* =========================================
   RESPONSIVE
========================================= */

@media (max-width: 992px) {

    .rm-page-title {
        font-size: 24px;
    }

    .rm-page-title i {
        width: 39px;
        height: 39px;

        font-size: 20px;
    }

}


@media (max-width: 768px) {

    .rm-page-header {
        padding-top: 20px;
    }

    .rm-page-title {
        font-size: 21px;
    }

    .rm-page-title i {
        width: 36px;
        height: 36px;

        margin-right: 9px;

        font-size: 18px;
    }

    .rm-toolbar {
        margin-bottom: 15px;
    }

}


/* =========================================
   DATATABLE
========================================= */

.dataTables_wrapper {
    padding: 15px;
}


/* SEARCH DATATABLE */

.dataTables_wrapper .dataTables_filter input {
    border: 1px solid #dedfe6;

    border-radius: 8px;

    padding: 7px 12px;

    outline: none;

    margin-left: 7px;
}

.dataTables_wrapper .dataTables_filter input:focus {
    border-color: #b3aefc;

    box-shadow: 0 0 0 .15rem rgba(117, 113, 249,.10);
}


/* LENGTH */

.dataTables_wrapper .dataTables_length select {
    border: 1px solid #dedfe6;

    border-radius: 7px;

    padding: 5px 25px 5px 8px;

    outline: none;
}


/* PAGINATION */

.dataTables_wrapper .dataTables_paginate .paginate_button {
    border-radius: 7px !important;

    border: none !important;

    margin: 2px;
}

.dataTables_wrapper .dataTables_paginate .paginate_button:hover {
    background: #7571f9 !important;

    color: white !important;

    border: none !important;
}


/* =========================================
   MOBILE TABLE
========================================= */

@media (max-width: 576px) {

    .rm-page-title {
        font-size: 19px;
    }

    .rm-page-title i {
        width: 34px;
        height: 34px;

        font-size: 17px;
    }

    .rm-add-button {
        font-size: 12px;

        padding: 7px 11px;
    }

}

</style>


<div class="rm-page page-content-wrap">


    <!-- =====================================
         HEADER
    ====================================== -->

    <div class="rm-page-header">

        <h1 class="rm-page-title">

            <i class="bi bi-file-medical"></i>

            Data Rekam Medis

        </h1>

    </div>


    <!-- =====================================
         ALERT
    ====================================== -->

    <?php if ($msg !== '') { ?>

        <div class="rm-alert">

            <?= $alert ?>

        </div>

    <?php } ?>


    <!-- =====================================
         TOOLBAR

         "Tambah Data" bebas (tanpa lewat antrian) sengaja dihilangkan
         untuk Dokter & Bidan: pasien yang boleh direkam rekam medisnya
         harus sudah didaftarkan Petugas dulu (masuk antrian hari ini),
         baru diperiksa lewat tombol "Periksa" di halaman Antrian Pasien
         (menu Antrian Pasien Umum / Antrian Kebidanan / Antrian Pasien)
         -> otomatis bawa antrian_id. Widget antrian yang dulu ada di
         sini sudah dipindah ke sana supaya tidak dobel. Lihat juga
         pengecekan senada di rekammedis/tambah-data.php.
    ====================================== -->


    <!-- =====================================
         TABLE CARD
    ====================================== -->

    <div class="rm-table-card">

        <div class="rm-table-wrapper">


            <table class="table table-hover" id="myTable">

                <!-- Lebar kolom tetap (table-layout: fixed) supaya isi
                     yang panjang (Keluhan/Diagnosa/Obat/Alamat) melipat
                     ke bawah, bukan mendorong tabel melebar ke samping. -->
                <colgroup>
                    <col style="width:50px;">
                    <col style="width:100px;">
                    <col style="width:100px;">
                    <col style="width:150px;">
                    <col style="width:180px;">
                    <col style="width:180px;">
                    <col style="width:130px;">
                    <col style="width:160px;">
                    <col style="width:160px;">
                    <col style="width:130px;">
                </colgroup>

                <thead>

                    <tr>

                        <th>No</th>

                        <th>No RM</th>

                        <th>Tanggal</th>

                        <th>Nama Pasien</th>

                        <th>Alamat</th>

                        <th>Keluhan</th>

                        <th>Dokter</th>

                        <th>Diagnosa</th>

                        <th>Obat</th>

                        <th>Aksi</th>

                    </tr>

                </thead>


                <tbody>

                    <?php 

                    $no = 1; 

                    $sqlrm = "SELECT *, tbl_pasien.alamat AS alamatpasien FROM tbl_rekammedis 
                    INNER JOIN tbl_pasien ON tbl_rekammedis.id_pasien = tbl_pasien.id INNER JOIN 
                    tbl_user ON tbl_rekammedis.id_dokter = tbl_user.userid order by tgl_rm desc"; 

                    $queryrm = mysqli_query($koneksi, $sqlrm); 

                    while ($rm = mysqli_fetch_assoc($queryrm)) { 

                    ?>  


                    <tr>


                        <!-- NO -->

                        <td>

                            <?= $no++; ?>

                        </td>


                        <!-- NO RM -->

                        <td>

                            <?= $rm['no_rm']; ?>

                        </td>


                        <!-- TANGGAL -->

                        <td>

                            <?= in_date($rm['tgl_rm']) ?>

                        </td>


                        <!-- NAMA PASIEN -->

                        <td>

                            <?= $rm['nama'] ?>

                        </td>


                        <!-- ALAMAT -->

                        <td>

                            <?= $rm['alamatpasien'] ?>

                        </td>


                        <!-- KELUHAN -->

                        <td>

                            <?= $rm['keluhan'] ?>

                        </td>


                        <!-- DOKTER -->

                        <td>

                            <?= $rm['fullname'] ?>

                        </td>


                        <!-- DIAGNOSA -->

                        <td>

                            <?= $rm['diagnosa'] ?>

                        </td>


                        <!-- OBAT -->

                        <td>

                            <?= $rm['resep_obat'] ?>

                        </td>


                        <!-- AKSI -->

                        <td>


                            <?php if(userHasRole(3)){ ?> 


                                <a href="edit-data.php?id=<?= $rm['id_rm'] ?>" 
                                   class="btn btn-sm btn-outline-warning" 
                                   title="Edit Rekam Medis">

                                    <i class="bi bi-pencil-square"></i>

                                </a>


                            <?php } ?>


                            <?php if(userHasAnyRole([1,2])){ ?>


                                <a href="proses-data.php?id=<?= $rm['id_rm']; ?>&aksi=hapus" 
                                   class="btn btn-sm btn-outline-danger" 
                                   title="Hapus Rekam Medis" 
                                   onclick="return confirm('Yakin ingin menghapus data rekam medis ini?')">

                                    <i class="bi bi-trash"></i>

                                </a>


                            <?php } ?> 


                        </td>


                    </tr>


                    <?php } ?> 


                </tbody>

            </table>


        </div>

    </div>


</div>


<script>

window.setTimeout(function(){ 

    $('.update').fadeOut(); 

}, 5000);

</script>


<?php 

require "../template/footer.php"; 

?>