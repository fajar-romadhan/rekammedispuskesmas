<?php  

session_start(); 

require "../template/rbac.php";

cekAkses([ROLE_ADMIN, ROLE_PETUGAS, ROLE_BIDAN]);

require "../config.php"; 

$title = "Edit Pasien - rekammedispuskesmas"; 

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
   AMBIL ID PASIEN
========================================= */

if (!isset($_GET['id']) || empty($_GET['id'])) {

    echo "<script>
        alert('ID pasien tidak ditemukan.');
        window.location = 'index.php';
    </script>";

    exit();

}

$id = mysqli_real_escape_string(
    $koneksi,
    $_GET['id']
);


/* =========================================
   DATA PASIEN
========================================= */

$queryPasien = mysqli_query(
    $koneksi,
    "SELECT * FROM tbl_pasien WHERE id = '$id'"
);

$pasien = mysqli_fetch_assoc($queryPasien);


/* =========================================
   CEK DATA
========================================= */

if (!$pasien) {

    echo "<script>
        alert('Data pasien tidak ditemukan.');
        window.location = 'index.php';
    </script>";

    exit();

}

?>


<style>

    /* =========================================
       HALAMAN EDIT PASIEN
    ========================================= */

    .edit-page-header {
        padding-top: 20px;
        padding-bottom: 15px;
        margin-bottom: 22px;
        border-bottom: 1px solid #e9e9ef;
    }

    .edit-page-title {
        font-size: 25px;
        font-weight: 600;
        color: #343540;
        margin: 0;
    }

    .edit-page-title i {
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

    .edit-card {
        border: none;
        border-radius: 15px;
        overflow: hidden;
        background: white;
        box-shadow: 0 4px 20px rgba(0,0,0,.07);
    }


    /* =========================================
       CARD HEADER
    ========================================= */

    .edit-card-header {
        background: linear-gradient(
            135deg,
            #7571f9,
            #5f5ae0
        );

        color: white;
        padding: 20px 25px;
    }

    .edit-card-header h5 {
        margin: 0;
        font-size: 17px;
        font-weight: 600;
    }

    .edit-card-header small {
        opacity: .85;
    }

    .patient-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        background: rgba(255,255,255,.15);

        display: flex;
        align-items: center;
        justify-content: center;

        font-size: 23px;
    }


    /* =========================================
       SECTION
    ========================================= */

    .form-section {
        background: #f8f8fa;
        border-radius: 12px;
        padding: 20px;
        height: 100%;
    }

    .section-title {
        font-size: 16px;
        font-weight: 600;
        color: #494a57;

        padding-bottom: 12px;
        margin-bottom: 18px;

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

    .form-control,
    .form-select {

        border-radius: 9px;

        padding: 10px 12px;

        border: 1px solid #cecfda;

        transition: all .2s ease;

    }

    .form-control:focus,
    .form-select:focus {

        border-color: #7571f9;

        box-shadow:
            0 0 0 .2rem
            rgba(117, 113, 249,.12);

    }

    textarea.form-control {

        min-height: 105px;

        resize: vertical;

    }


    /* =========================================
       ID PASIEN
    ========================================= */

    .patient-id-box {

        background: #eeeeff;

        border: 1px solid #d9d7ff;

        border-radius: 9px;

        padding: 10px 12px;

        color: #7571f9;

        font-weight: 600;

    }

    .patient-id-box i {

        margin-right: 7px;

    }


    /* =========================================
       GENDER
    ========================================= */

    .gender-box {

        display: flex;

        gap: 10px;

    }

    .gender-option {

        flex: 1;

    }

    .gender-option input {

        display: none;

    }

    .gender-option label {

        display: block;

        text-align: center;

        padding: 10px;

        border: 1px solid #cecfda;

        border-radius: 9px;

        background: white;

        cursor: pointer;

        transition: .2s;

    }

    .gender-option label:hover {

        border-color: #7571f9;

    }

    .gender-option input:checked + label {

        background: #7571f9;

        color: white;

        border-color: #7571f9;

    }


    /* =========================================
       PEMBAYARAN
    ========================================= */

    .payment-box {

        display: flex;

        gap: 8px;

        flex-wrap: wrap;

    }

    .payment-option {

        flex: 1;

        min-width: 90px;

    }

    .payment-option input {

        display: none;

    }

    .payment-option label {

        display: block;

        text-align: center;

        padding: 10px 5px;

        background: white;

        border: 1px solid #cecfda;

        border-radius: 9px;

        cursor: pointer;

        font-size: 13px;

        transition: .2s;

    }

    .payment-option label:hover {

        border-color: #7571f9;

    }

    .payment-option input:checked + label {

        background: #7571f9;

        color: white;

        border-color: #7571f9;

    }


    /* =========================================
       FOOTER BUTTON
    ========================================= */

    .form-footer {

        border-top: 1px solid #eee;

        padding-top: 20px;

        margin-top: 25px;

        display: flex;

        justify-content: flex-end;

        gap: 8px;

    }

    .form-footer .btn {

        border-radius: 8px;

        padding: 9px 18px;

    }


    /* =========================================
       INFO
    ========================================= */

    .info-box {

        border-radius: 10px;

        padding: 12px 14px;

        font-size: 13px;

        margin-top: 15px;

    }


    /* =========================================
       RESPONSIVE
    ========================================= */

    @media (max-width: 768px) {

        .edit-page-title {

            font-size: 21px;

        }

        .gender-box {

            flex-direction: column;

        }

        .payment-box {

            flex-direction: column;

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

    <div class="edit-page-header d-flex 
                justify-content-between 
                align-items-center">

        <h1 class="edit-page-title">

            <i class="bi bi-person-vcard-fill"></i>

            Edit Pasien

        </h1>


        <a href="<?= $main_url ?>pasien"
           class="back-button">

            <i class="bi bi-arrow-left"></i>

            Kembali

        </a>

    </div>



    <!-- =====================================
         CARD
    ====================================== -->

    <div class="edit-card mb-5">


        <!-- CARD HEADER -->

        <div class="edit-card-header">

            <div class="d-flex align-items-center">

                <div class="patient-icon me-3">

                    <i class="bi bi-person-fill"></i>

                </div>

                <div>

                    <h5>
                        Edit Data Pasien
                    </h5>

                    <small>
                        Perbarui informasi pasien yang diperlukan.
                    </small>

                </div>

            </div>

        </div>



        <!-- CARD BODY -->

        <div class="card-body p-4">


            <form action="proses-pasien.php"
                  method="post">


                <!-- ID PASIEN -->

                <input type="hidden"
                       name="id"
                       value="<?= htmlspecialchars($pasien['id']) ?>">



                <div class="row g-4">


                    <!-- =================================
                         DATA PRIBADI
                    ================================== -->

                    <div class="col-lg-6">

                        <div class="form-section">


                            <div class="section-title">

                                <i class="bi bi-person"></i>

                                Data Pribadi

                            </div>



                            <!-- ID -->

                            <div class="mb-3">

                                <label class="form-label">

                                    ID Pasien

                                </label>

                                <div class="patient-id-box">

                                    <i class="bi bi-person-badge"></i>

                                    <?= htmlspecialchars($pasien['id']) ?>

                                </div>

                            </div>



                            <!-- NAMA -->

                            <div class="mb-3">

                                <label for="nama"
                                       class="form-label">

                                    Nama Lengkap

                                </label>

                                <input type="text"
                                       name="nama"
                                       id="nama"
                                       class="form-control"
                                       placeholder="Masukkan nama lengkap pasien"
                                       value="<?= htmlspecialchars($pasien['nama']) ?>"
                                       required>

                            </div>



                            <!-- TANGGAL LAHIR -->

                            <div class="mb-3">

                                <label for="tgl_lahir"
                                       class="form-label">

                                    Tanggal Lahir

                                </label>

                                <input type="date"
                                       name="tgl_lahir"
                                       id="tgl_lahir"
                                       class="form-control"
                                       value="<?= htmlspecialchars($pasien['tgl_lahir']) ?>"
                                       required>

                            </div>



                            <!-- JENIS KELAMIN -->

                            <div class="mb-3">

                                <label class="form-label">

                                    Jenis Kelamin

                                </label>


                                <div class="gender-box">


                                    <div class="gender-option">

                                        <input type="radio"
                                               name="gender"
                                               id="pria"
                                               value="P"
                                               required
                                               <?= ($pasien['gender'] == 'P') ? 'checked' : ''; ?>>

                                        <label for="pria">

                                            <i class="bi bi-gender-male"></i>

                                            Pria

                                        </label>

                                    </div>



                                    <div class="gender-option">

                                        <input type="radio"
                                               name="gender"
                                               id="wanita"
                                               value="W"
                                               <?= ($pasien['gender'] == 'W') ? 'checked' : ''; ?>>

                                        <label for="wanita">

                                            <i class="bi bi-gender-female"></i>

                                            Wanita

                                        </label>

                                    </div>


                                </div>

                            </div>



                            <!-- TELEPON -->

                            <div class="mb-3">

                                <label for="telpon"
                                       class="form-label">

                                    Telpon / Handphone

                                </label>

                                <input type="tel"
                                       name="telpon"
                                       id="telpon"
                                       class="form-control"
                                       placeholder="Contoh: 081234567890"
                                       pattern="[0-9]{8,}"
                                       title="Nomor telepon minimal 8 angka"
                                       value="<?= htmlspecialchars($pasien['telpon']) ?>"
                                       required>

                            </div>



                            <!-- ALAMAT -->

                            <div class="mb-3">

                                <label for="alamat"
                                       class="form-label">

                                    Alamat

                                </label>

                                <textarea name="alamat"
                                          id="alamat"
                                          class="form-control"
                                          placeholder="Masukkan alamat pasien"
                                          required><?= htmlspecialchars($pasien['alamat']) ?></textarea>

                            </div>


                        </div>

                    </div>



                    <!-- =================================
                         DATA ADMINISTRASI
                    ================================== -->

                    <div class="col-lg-6">

                        <div class="form-section">


                            <div class="section-title">

                                <i class="bi bi-card-text"></i>

                                Data Administrasi

                            </div>



                            <!-- NIK -->

                            <div class="mb-3">

                                <label for="nik"
                                       class="form-label">

                                    NIK

                                </label>

                                <input type="text"
                                       name="nik"
                                       id="nik"
                                       class="form-control"
                                       placeholder="Masukkan 16 digit NIK"
                                       maxlength="16"
                                       minlength="16"
                                       pattern="[0-9]{16}"
                                       title="NIK harus terdiri dari 16 angka"
                                       value="<?= htmlspecialchars($pasien['nik'] ?? '') ?>"
                                       required>

                            </div>



                            <!-- GOLONGAN DARAH -->

                            <div class="mb-3">

                                <label for="gol_darah"
                                       class="form-label">

                                    Golongan Darah

                                </label>

                                <select name="gol_darah"
                                        id="gol_darah"
                                        class="form-select"
                                        required>

                                    <option value="">
                                        -- Pilih Golongan Darah --
                                    </option>

                                    <option value="A"
                                        <?= (($pasien['gol_darah'] ?? '') == 'A') ? 'selected' : ''; ?>>
                                        A
                                    </option>

                                    <option value="B"
                                        <?= (($pasien['gol_darah'] ?? '') == 'B') ? 'selected' : ''; ?>>
                                        B
                                    </option>

                                    <option value="AB"
                                        <?= (($pasien['gol_darah'] ?? '') == 'AB') ? 'selected' : ''; ?>>
                                        AB
                                    </option>

                                    <option value="O"
                                        <?= (($pasien['gol_darah'] ?? '') == 'O') ? 'selected' : ''; ?>>
                                        O
                                    </option>

                                    <option value="Tidak Diketahui"
                                        <?= (($pasien['gol_darah'] ?? '') == 'O') ? 'selected' : ''; ?>>
                                        Tidak Diketahui
                                    </option>

                                </select>

                            </div>



                            <!-- JENIS PEMBAYARAN -->

                            <div class="mb-3">

                                <label class="form-label">

                                    Jenis Pembayaran

                                </label>


                                <div class="payment-box">


                                    <div class="payment-option">

                                        <input type="radio"
                                               name="jenis_pembayaran"
                                               id="bpjs"
                                               value="BPJS"
                                               <?= (($pasien['jenis_pembayaran'] ?? '') == 'BPJS') ? 'checked' : ''; ?>>

                                        <label for="bpjs">

                                            <i class="bi bi-credit-card"></i>

                                            BPJS

                                        </label>

                                    </div>



                                    <div class="payment-option">

                                        <input type="radio"
                                               name="jenis_pembayaran"
                                               id="asuransi"
                                               value="ASURANSI"
                                               <?= (($pasien['jenis_pembayaran'] ?? '') == 'ASURANSI') ? 'checked' : ''; ?>>

                                        <label for="asuransi">

                                            <i class="bi bi-shield-check"></i>

                                            Asuransi

                                        </label>

                                    </div>



                                    <div class="payment-option">

                                        <input type="radio"
                                               name="jenis_pembayaran"
                                               id="umum"
                                               value="UMUM"
                                               <?= (($pasien['jenis_pembayaran'] ?? '') == 'UMUM') ? 'checked' : ''; ?>>

                                        <label for="umum">

                                            <i class="bi bi-cash"></i>

                                            Umum

                                        </label>

                                    </div>


                                </div>

                            </div>



                            <!-- NOMOR ASURANSI -->

                            <div class="mb-3"
                                 id="insuranceField">

                                <label for="no_asuransi"
                                       class="form-label">

                                    No. BPJS / Asuransi

                                </label>

                                <input type="text"
                                       name="no_asuransi"
                                       id="no_asuransi"
                                       class="form-control"
                                       placeholder="Masukkan nomor BPJS / Asuransi"
                                       value="<?= htmlspecialchars($pasien['no_asuransi'] ?? '') ?>">

                                <small class="text-muted">

                                    Isi jika menggunakan BPJS atau Asuransi.

                                </small>

                            </div>



                            <!-- INFO -->

                            <div class="alert alert-primary info-box mb-0">

                                <i class="bi bi-info-circle-fill me-1"></i>

                                Pastikan perubahan data sudah benar
                                sebelum menekan tombol Update.

                            </div>


                        </div>

                    </div>


                </div>



                <!-- =================================
                     BUTTON
                ================================== -->

                <div class="form-footer">


                    <a href="<?= $main_url ?>pasien"
                       class="btn btn-outline-secondary">

                        <i class="bi bi-x-lg me-1"></i>

                        Batal

                    </a>



                    <button type="submit"
                            name="update"
                            class="btn btn-primary">

                        <i class="bi bi-save me-1"></i>

                        Update Data

                    </button>


                </div>


            </form>

        </div>

    </div>

</div>



<script>

/* =========================================
   BPJS / ASURANSI
========================================= */

const paymentRadios = document.querySelectorAll(
    'input[name="jenis_pembayaran"]'
);

const insuranceField = document.getElementById(
    'insuranceField'
);

const insuranceInput = document.getElementById(
    'no_asuransi'
);


function checkPayment() {

    const selected = document.querySelector(
        'input[name="jenis_pembayaran"]:checked'
    );


    if (!selected) {

        insuranceField.style.display = 'none';

        insuranceInput.required = false;

        return;

    }


    if (
        selected.value === 'BPJS' ||
        selected.value === 'ASURANSI'
    ) {

        insuranceField.style.display = 'block';

        insuranceInput.required = true;

    } else {

        insuranceField.style.display = 'none';

        insuranceInput.required = false;

    }

}


paymentRadios.forEach(function(radio) {

    radio.addEventListener(
        'change',
        checkPayment
    );

});


/* Jalankan saat halaman pertama kali dibuka */
checkPayment();

</script>



<?php 

require "../template/footer.php"; 

?>