<?php

session_start();

require "../template/rbac.php";

cekAkses([ROLE_ADMIN, ROLE_PETUGAS, ROLE_BIDAN]);

require "../config.php";

$title = "Tambah Pasien - rekammedispuskesmas";

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

/* =========================
   FORM TAMBAH PASIEN
========================= */

.patient-card {
    border: none;
    border-radius: 15px;
    box-shadow: 0 4px 18px rgba(0,0,0,.08);
    overflow: hidden;
    background: #fff;
}

.patient-card-header {
    background: linear-gradient(135deg, #7571f9, #5f5ae0);
    color: white;
    padding: 20px 25px;
}

.patient-card-header h5 {
    margin: 0;
    font-weight: 600;
}

.patient-card-header small {
    opacity: .85;
}

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
    margin-bottom: 18px;
    padding-bottom: 10px;
    border-bottom: 1px solid #dedfe6;
}

.section-title i {
    color: #7571f9;
    margin-right: 7px;
}

.form-label {
    font-weight: 500;
    color: #494a57;
    margin-bottom: 7px;
}

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
    box-shadow: 0 0 0 .2rem rgba(117, 113, 249,.12);
}

textarea.form-control {
    min-height: 105px;
    resize: vertical;
}


/* =========================
   JENIS KELAMIN
========================= */

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


/* =========================
   PEMBAYARAN
========================= */

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
    font-size: 14px;
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


/* =========================
   NOMOR BPJS / ASURANSI
========================= */

.insurance-field {
    display: none;
}


/* =========================
   FOOTER FORM
========================= */

.form-footer {
    border-top: 1px solid #eee;
    padding-top: 20px;
    margin-top: 20px;
    display: flex;
    justify-content: flex-end;
    gap: 8px;
}

.btn {
    border-radius: 8px;
    padding: 9px 18px;
}


/* =========================
   RESPONSIVE
========================= */

@media (max-width: 768px) {

    .form-section {
        margin-bottom: 15px;
    }

    .gender-box,
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


    <!-- =========================
         HEADER HALAMAN
    ========================== -->

    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap
                align-items-center pt-3 pb-3 mb-4 border-bottom">

        <div>

            <h1 class="h3 mb-1">

                <i class="bi bi-person-plus-fill text-primary me-2"></i>

                Pasien Baru

            </h1>

            <small class="text-muted">

                Tambahkan data pasien baru ke dalam sistem rekam medis.

            </small>

        </div>


        <a href="<?= $main_url ?>pasien"
           class="btn btn-outline-secondary btn-sm">

            <i class="bi bi-arrow-left"></i>

            Kembali

        </a>

    </div>



    <!-- =========================
         CARD FORM
    ========================== -->

    <div class="patient-card mb-5">


        <!-- HEADER CARD -->

        <div class="patient-card-header">

            <div class="d-flex align-items-center">

                <div class="me-3">

                    <i class="bi bi-person-vcard fs-2"></i>

                </div>

                <div>

                    <h5>Form Data Pasien</h5>

                    <small>

                        Silakan lengkapi informasi pasien dengan benar.

                    </small>

                </div>

            </div>

        </div>



        <div class="card-body p-4">


            <form action="proses-pasien.php" method="post">


                <div class="row g-4">


                    <!-- =========================
                         DATA PRIBADI
                    ========================== -->

                    <div class="col-lg-6">

                        <div class="form-section">


                            <div class="section-title">

                                <i class="bi bi-person"></i>

                                Data Pribadi

                            </div>



                            <!-- NAMA -->

                            <div class="mb-3">

                                <label for="nama" class="form-label">

                                    Nama Lengkap

                                </label>

                                <input type="text"
                                       name="nama"
                                       id="nama"
                                       class="form-control"
                                       placeholder="Masukkan nama lengkap pasien"
                                       required>

                            </div>



                            <!-- TANGGAL LAHIR -->

                            <div class="mb-3">

                                <label for="tgl_lahir" class="form-label">

                                    Tanggal Lahir

                                </label>

                                <input type="date"
                                       name="tgl_lahir"
                                       id="tgl_lahir"
                                       class="form-control"
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
                                               required>

                                        <label for="pria">

                                            <i class="bi bi-gender-male"></i>

                                            Pria

                                        </label>

                                    </div>



                                    <div class="gender-option">

                                        <input type="radio"
                                               name="gender"
                                               id="wanita"
                                               value="W">

                                        <label for="wanita">

                                            <i class="bi bi-gender-female"></i>

                                            Wanita

                                        </label>

                                    </div>


                                </div>

                            </div>



                            <!-- TELEPON -->

                            <div class="mb-3">

                                <label for="telpon" class="form-label">

                                    Telpon / Handphone

                                </label>

                                <input type="tel"
                                       name="telpon"
                                       id="telpon"
                                       class="form-control"
                                       placeholder="Contoh: 081234567890"
                                       pattern="[0-9]{8,}"
                                       title="Nomor telepon minimal 8 angka"
                                       required>

                            </div>



                            <!-- ALAMAT -->

                            <div class="mb-3">

                                <label for="alamat" class="form-label">

                                    Alamat

                                </label>

                                <textarea name="alamat"
                                          id="alamat"
                                          class="form-control"
                                          placeholder="Masukkan alamat lengkap pasien"
                                          required></textarea>

                            </div>


                        </div>

                    </div>



                    <!-- =========================
                         DATA ADMINISTRASI
                    ========================== -->

                    <div class="col-lg-6">

                        <div class="form-section">


                            <div class="section-title">

                                <i class="bi bi-card-text"></i>

                                Data Administrasi

                            </div>



                            <!-- NIK -->

                            <div class="mb-3">

                                <label for="nik" class="form-label">

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
                                       required>

                            </div>



                            <!-- =========================
                                 GOLONGAN DARAH
                            ========================== -->

                            <div class="mb-3">

                                <label for="gol_darah" class="form-label">

                                    Golongan Darah

                                </label>


                                <select name="gol_darah"
                                        id="gol_darah"
                                        class="form-select"
                                        required>

                                    <option value="">
                                        -- Pilih Golongan Darah --
                                    </option>

                                    <option value="A">
                                        A
                                    </option>

                                    <option value="B">
                                        B
                                    </option>

                                    <option value="AB">
                                        AB
                                    </option>

                                    <option value="O">
                                        O
                                    </option>

                                    <!-- SUDAH DIPERBAIKI -->

                                    <option value="Tidak Diketahui">
                                        Tidak Diketahui
                                    </option>

                                </select>

                            </div>



                            <!-- =========================
                                 JENIS PEMBAYARAN
                            ========================== -->

                            <div class="mb-3">

                                <label class="form-label">

                                    Jenis Pembayaran

                                </label>


                                <div class="payment-box">


                                    <!-- BPJS -->

                                    <div class="payment-option">

                                        <input type="radio"
                                               name="jenis_pembayaran"
                                               id="bpjs"
                                               value="BPJS"
                                               required>

                                        <label for="bpjs">

                                            <i class="bi bi-credit-card"></i>

                                            BPJS

                                        </label>

                                    </div>



                                    <!-- ASURANSI -->

                                    <div class="payment-option">

                                        <input type="radio"
                                               name="jenis_pembayaran"
                                               id="asuransi"
                                               value="ASURANSI">

                                        <label for="asuransi">

                                            <i class="bi bi-shield-check"></i>

                                            Asuransi

                                        </label>

                                    </div>



                                    <!-- UMUM -->

                                    <div class="payment-option">

                                        <input type="radio"
                                               name="jenis_pembayaran"
                                               id="umum"
                                               value="UMUM">

                                        <label for="umum">

                                            <i class="bi bi-cash"></i>

                                            Umum

                                        </label>

                                    </div>


                                </div>

                            </div>



                            <!-- =========================
                                 NOMOR BPJS / ASURANSI
                            ========================== -->

                            <div class="mb-3 insurance-field"
                                 id="insuranceField">


                                <label for="no_asuransi"
                                       class="form-label">

                                    No. BPJS / Asuransi

                                </label>


                                <input type="text"
                                       name="no_asuransi"
                                       id="no_asuransi"
                                       class="form-control"
                                       placeholder="Masukkan nomor BPJS / Asuransi">


                                <small class="text-muted">

                                    Isi jika pasien menggunakan BPJS atau Asuransi.

                                </small>


                            </div>



                            <!-- INFORMASI -->

                            <div class="alert alert-primary mt-4 mb-0">

                                <i class="bi bi-info-circle-fill"></i>

                                <small>

                                    Pastikan data pasien yang dimasukkan sudah benar
                                    sebelum menyimpan.

                                </small>

                            </div>


                        </div>

                    </div>

                </div>



                <!-- =========================
                     BUTTON
                ========================== -->

                <div class="form-footer">


                    <button type="reset"
                            class="btn btn-outline-danger">

                        <i class="bi bi-arrow-counterclockwise"></i>

                        Reset

                    </button>



                    <button type="submit"
                            name="simpan"
                            class="btn btn-primary">

                        <i class="bi bi-save"></i>

                        Simpan Pasien

                    </button>


                </div>


            </form>

        </div>

    </div>

</div>



<script>

/* ================================
   NOMOR BPJS / ASURANSI
================================ */

const paymentRadios = document.querySelectorAll(
    'input[name="jenis_pembayaran"]'
);

const insuranceField = document.getElementById(
    'insuranceField'
);

const insuranceInput = document.getElementById(
    'no_asuransi'
);


paymentRadios.forEach(function(radio) {

    radio.addEventListener('change', function() {


        if (
            this.value === 'BPJS' ||
            this.value === 'ASURANSI'
        ) {

            insuranceField.style.display = 'block';

            insuranceInput.required = true;

        }

        else {

            insuranceField.style.display = 'none';

            insuranceInput.required = false;

            insuranceInput.value = '';

        }

    });

});

</script>



<?php

require "../template/footer.php";

?>