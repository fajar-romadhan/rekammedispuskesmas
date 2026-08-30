<?php  

session_start(); 

require "../template/rbac.php";

// Hanya Petugas
cekAkses([ROLE_PETUGAS]);

require "../config.php"; 

$title = "Edit Obat - rekammedispuskesmas"; 

require "../template/header.php"; 
require "../template/navbar.php"; 
require "../template/sidebar.php"; 


/* =========================================
   AMBIL ID OBAT
========================================= */

if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "<script>
        alert('ID obat tidak ditemukan.');
        window.location = '../obat';
    </script>";
    exit();
}

$id = mysqli_real_escape_string($koneksi, $_GET['id']);


/* =========================================
   AMBIL DATA OBAT
========================================= */

$queryObat = mysqli_query(
    $koneksi,
    "SELECT * FROM tbl_obat WHERE id = '$id'"
);

if (mysqli_num_rows($queryObat) == 0) {

    echo "<script>
        alert('Data obat tidak ditemukan.');
        window.location = '../obat';
    </script>";

    exit();
}

$obat = mysqli_fetch_assoc($queryObat);

?>

<style>

/* =========================================
   HEADER
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


/* =========================================
   BUTTON KEMBALI
========================================= */

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

    margin-right: 6px;

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
   LABEL
========================================= */

.form-label {

    font-size: 14px;

    font-weight: 500;

    color: #494a57;

    margin-bottom: 7px;

}


/* =========================================
   INPUT
========================================= */

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

    min-height: 135px;

    resize: vertical;

}


/* =========================================
   INPUT ICON
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
   DATA OBAT
========================================= */

.data-obat {

    background: white;

    border-radius: 10px;

    border: 1px solid #e6e5eb;

    padding: 15px;

    margin-bottom: 15px;

}

.data-obat-label {

    display: block;

    font-size: 12px;

    color: #6c757d;

    margin-bottom: 4px;

}

.data-obat-value {

    font-weight: 600;

    color: #343540;

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

            Edit Obat

        </h1>


        <a href="<?= $main_url ?>obat"
           class="back-button">

            <i class="bi bi-arrow-left"></i>

            Kembali

        </a>

    </div>



    <!-- =====================================
         CARD
    ====================================== -->

    <div class="obat-card mb-5">


        <!-- CARD HEADER -->

        <div class="obat-card-header">

            <div class="d-flex align-items-center">


                <div class="obat-icon me-3">

                    <i class="bi bi-pencil-square"></i>

                </div>


                <div>

                    <h5>

                        Edit Data Obat

                    </h5>

                    <small>

                        Perbarui informasi obat yang
                        tersimpan di dalam sistem.

                    </small>

                </div>


            </div>

        </div>



        <!-- CARD BODY -->

        <div class="card-body p-4">


            <form action="proses-obat.php"
                  method="post">


                <input type="hidden"
                       name="id"
                       value="<?= htmlspecialchars($obat['id']); ?>">



                <div class="row g-4">


                    <!-- =================================
                         FORM DATA
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
                                           value="<?= htmlspecialchars($obat['nama']); ?>"
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

                                    <option value="Obat"
                                        <?= $obat['kategori'] === 'Obat' ? 'selected' : ''; ?>>
                                        Obat (dispensing, punya stok)
                                    </option>

                                    <option value="Tindakan"
                                        <?= $obat['kategori'] === 'Tindakan' ? 'selected' : ''; ?>>
                                        Tindakan / Jasa (biaya periksa, konsultasi, pelayanan KB, dll)
                                    </option>

                                </select>

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
                                          required><?= htmlspecialchars($obat['kegunaan']); ?></textarea>


                                <small class="text-muted">

                                    Pastikan informasi kegunaan
                                    sudah sesuai.

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
                                           value="<?= htmlspecialchars($obat['harga']); ?>"
                                           required>

                                </div>

                            </div>


                        </div>

                    </div>



                    <!-- =================================
                         INFORMASI SAMPING
                    ================================== -->

                    <div class="col-lg-4">

                        <div class="form-section">


                            <div class="section-title">

                                <i class="bi bi-info-circle"></i>

                                Informasi

                            </div>



                            <!-- NAMA -->

                            <div class="data-obat">

                                <span class="data-obat-label">

                                    Nama Obat Saat Ini

                                </span>

                                <span class="data-obat-value">

                                    <i class="bi bi-capsule me-1"></i>

                                    <?= htmlspecialchars($obat['nama']); ?>

                                </span>

                            </div>



                            <!-- STOK -->

                            <div class="data-obat">

                                <span class="data-obat-label">

                                    Stok Saat Ini

                                </span>

                                <span class="data-obat-value">

                                    <?php if ($obat['stok'] <= 0) { ?>

                                        <span class="stock-badge stock-habis">

                                            Habis

                                        </span>

                                    <?php } elseif ($obat['stok'] <= 10) { ?>

                                        <span class="stock-badge stock-warning">

                                            <?= $obat['stok']; ?>

                                        </span>

                                    <?php } else { ?>

                                        <span class="stock-badge stock-aman">

                                            <?= $obat['stok']; ?>

                                        </span>

                                    <?php } ?>

                                </span>

                            </div>



                            <div class="info-box">

                                <i class="bi bi-info-circle-fill"></i>

                                Perubahan yang dilakukan akan
                                mengganti data obat yang tersimpan
                                sebelumnya.

                            </div>


                        </div>

                    </div>


                </div>



                <!-- =================================
                     BUTTON
                ================================== -->

                <div class="form-footer">


                    <a href="<?= $main_url ?>obat"
                       class="btn btn-outline-secondary">

                        <i class="bi bi-x-lg me-1"></i>

                        Batal

                    </a>



                    <button type="submit"
                            name="update"
                            class="btn btn-primary">

                        <i class="bi bi-save me-1"></i>

                        Simpan Perubahan

                    </button>


                </div>


            </form>

        </div>

    </div>


</div>



<?php

require "../template/footer.php";

?>