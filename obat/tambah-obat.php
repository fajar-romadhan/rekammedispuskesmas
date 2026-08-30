<?php  

session_start(); 

require "../template/rbac.php";

// Hanya Petugas
cekAkses([ROLE_PETUGAS]);

require "../config.php"; 

$title = "Tambah Obat - rekammedispuskesmas"; 

require "../template/header.php"; 
require "../template/navbar.php"; 
require "../template/sidebar.php"; 


/* =========================================
   CEK HAK AKSES
========================================= */

if ($dataUser['jabatan'] == 1) {

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


if ($msg == 'added') {

    $alert = '
    <div class="alert alert-success alert-dismissible fade show custom-alert"
         role="alert">

        <i class="bi bi-check-circle-fill me-2"></i>

        <strong>Tambah obat baru berhasil.</strong>

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

    margin-bottom: 22px;

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

.back-button {

    text-decoration: none;

    color: #6c757d;

    font-size: 14px;

    transition: .2s;

}

.back-button:hover {

    color: #7571f9;

}

.back-button i {

    margin-right: 5px;

}


/* =========================================
   CARD
========================================= */

.obat-card {

    background: white;

    border: none;

    border-radius: 15px;

    overflow: hidden;

    box-shadow: 0 4px 20px rgba(0,0,0,.07);

}


/* =========================================
   CARD HEADER
========================================= */

.obat-card-header {

    background: linear-gradient(
        135deg,
        #7571f9,
        #5f5ae0
    );

    color: white;

    padding: 20px 25px;

}

.obat-icon {

    width: 48px;

    height: 48px;

    border-radius: 12px;

    background: rgba(255,255,255,.15);

    display: flex;

    align-items: center;

    justify-content: center;

    font-size: 23px;

}

.obat-card-header h5 {

    margin: 0;

    font-size: 17px;

    font-weight: 600;

}

.obat-card-header small {

    opacity: .85;

}


/* =========================================
   FORM SECTION
========================================= */

.form-section {

    background: #f8f8fa;

    border-radius: 12px;

    padding: 22px;

}

.section-title {

    font-size: 16px;

    font-weight: 600;

    color: #494a57;

    padding-bottom: 12px;

    margin-bottom: 20px;

    border-bottom: 1px solid #dedfe6;

}

.section-title i {

    color: #7571f9;

    margin-right: 7px;

}


/* =========================================
   FORM
========================================= */

.form-label {

    font-size: 14px;

    font-weight: 500;

    color: #494a57;

    margin-bottom: 7px;

}

.form-control {

    border-radius: 9px;

    padding: 10px 12px;

    border: 1px solid #cecfda;

    transition: all .2s ease;

}

.form-control:focus {

    border-color: #7571f9;

    box-shadow:
        0 0 0 .2rem
        rgba(117, 113, 249,.12);

}

textarea.form-control {

    min-height: 130px;

    resize: vertical;

}


/* =========================================
   ICON INPUT
========================================= */

.input-icon {

    position: relative;

}

.input-icon i {

    position: absolute;

    left: 13px;

    top: 50%;

    transform: translateY(-50%);

    color: #6c757d;

    z-index: 2;

}

.input-icon .form-control {

    padding-left: 38px;

}


/* =========================================
   INFO BOX
========================================= */

.info-box {

    background: #eeeeff;

    border: 1px solid #d9d7ff;

    color: #494a57;

    border-radius: 10px;

    padding: 14px;

    font-size: 13px;

    line-height: 1.6;

}

.info-box i {

    color: #7571f9;

    margin-right: 6px;

}


/* =========================================
   FORM FOOTER
========================================= */

.form-footer {

    border-top: 1px solid #eee;

    padding-top: 20px;

    margin-top: 22px;

    display: flex;

    justify-content: flex-end;

    gap: 8px;

}

.form-footer .btn {

    border-radius: 8px;

    padding: 9px 18px;

    transition: .2s;

}

.form-footer .btn:hover {

    transform: translateY(-1px);

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
   RESPONSIVE
========================================= */

@media (max-width: 768px) {

    .page-title {

        font-size: 21px;

    }

    .form-footer {

        justify-content: stretch;

    }

    .form-footer .btn {

        flex: 1;

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

            Obat Baru

        </h1>


        <a href="<?= $main_url ?>obat"
           class="back-button">

            <i class="bi bi-arrow-left"></i>

            Kembali

        </a>

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
         CARD
    ====================================== -->

    <div class="obat-card mb-5">


        <!-- CARD HEADER -->

        <div class="obat-card-header">

            <div class="d-flex align-items-center">


                <div class="obat-icon me-3">

                    <i class="bi bi-capsule"></i>

                </div>


                <div>

                    <h5>

                        Tambah Data Obat

                    </h5>

                    <small>

                        Masukkan informasi obat yang akan
                        digunakan dalam sistem.

                    </small>

                </div>


            </div>

        </div>



        <!-- CARD BODY -->

        <div class="card-body p-4">


            <form action="proses-obat.php"
                  method="post">


                <div class="row g-4">


                    <!-- =================================
                         FORM DATA OBAT
                    ================================== -->

                    <div class="col-lg-8">

                        <div class="form-section">


                            <div class="section-title">

                                <i class="bi bi-capsule-pill"></i>

                                Informasi Obat

                            </div>



                            <!-- NAMA OBAT -->

                            <div class="mb-3">

                                <label for="nama"
                                       class="form-label">

                                    Nama

                                </label>


                                <div class="input-icon">

                                    <i class="bi bi-capsule"></i>

                                    <input type="text"
                                           name="nama"
                                           id="nama"
                                           class="form-control"
                                           placeholder="Contoh: Paracetamol 500 mg"
                                           required>

                                </div>

                            </div>



                            <!-- KATEGORI -->

                            <div class="mb-3">

                                <label for="kategori"
                                       class="form-label">

                                    Kategori

                                </label>

                                <select name="kategori"
                                        id="kategori"
                                        class="form-control"
                                        required>

                                    <option value="Obat">
                                        Obat (dispensing, punya stok)
                                    </option>

                                    <option value="Tindakan">
                                        Tindakan / Jasa (biaya periksa, konsultasi, pelayanan KB, dll)
                                    </option>

                                </select>

                                <small class="text-muted">

                                    Dipakai dokter/bidan saat memilih
                                    obat atau tindakan pada rekam medis
                                    untuk menghitung tagihan pasien.

                                </small>

                            </div>



                            <!-- KEGUNAAN -->

                            <div class="mb-3">

                                <label for="kegunaan"
                                       class="form-label">

                                    Kegunaan

                                </label>


                                <textarea name="kegunaan"
                                          id="kegunaan"
                                          class="form-control"
                                          placeholder="Jelaskan kegunaan atau fungsi obat/tindakan..."
                                          required></textarea>


                                <small class="text-muted">

                                    Tuliskan kegunaan secara singkat
                                    dan jelas.

                                </small>

                            </div>



                            <!-- HARGA -->

                            <div class="mb-3">

                                <label for="harga"
                                       class="form-label">

                                    Harga

                                </label>

                                <div class="input-icon">

                                    <i class="bi bi-cash-coin"></i>

                                    <input type="number"
                                           name="harga"
                                           id="harga"
                                           class="form-control"
                                           min="0"
                                           step="0.01"
                                           placeholder="Contoh: 5000"
                                           required>

                                </div>

                                <small class="text-muted">

                                    Harga satuan (Rp) untuk menghitung
                                    tagihan pembayaran pasien secara
                                    otomatis.

                                </small>

                            </div>


                        </div>

                    </div>



                    <!-- =================================
                         INFORMASI
                    ================================== -->

                    <div class="col-lg-4">

                        <div class="form-section">


                            <div class="section-title">

                                <i class="bi bi-info-circle"></i>

                                Informasi

                            </div>


                            <div class="info-box mb-3">

                                <i class="bi bi-check-circle-fill"></i>

                                Pastikan nama obat ditulis dengan
                                benar agar mudah ditemukan saat
                                digunakan dalam proses rekam medis.

                            </div>


                            <div class="info-box">

                                <i class="bi bi-box-seam "></i>

                                Setelah obat ditambahkan, stok obat
                                dapat diatur melalui menu
                                <strong>Tambah Stok</strong>.

                            </div>


                        </div>

                    </div>


                </div>



                <!-- =================================
                     BUTTON
                ================================== -->

                <div class="form-footer">


                    <button type="reset"
                            class="btn btn-outline-danger">

                        <i class="bi bi-arrow-counterclockwise me-1"></i>

                        Reset

                    </button>



                    <button type="submit"
                            name="simpan"
                            class="btn btn-primary">

                        <i class="bi bi-save me-1"></i>

                        Simpan Obat

                    </button>


                </div>


            </form>

        </div>

    </div>


</div>



<script>

/* =========================================
   ALERT AUTO HILANG
========================================= */

window.setTimeout(function () {

    $('.custom-alert').fadeOut();

}, 5000);

</script>



<?php

require "../template/footer.php";

?>