<?php

session_start();

require "../template/rbac.php";

// Hanya Admin Sistem
cekAkses([ROLE_ADMIN]);

require "../config.php";

$title = "Tambah User - rekammedispuskesmas";


require "../template/header.php";
require "../template/navbar.php";
require "../template/sidebar.php";

if ($dataUser['jabatan'] != 1) {
    echo "<script>
        alert('Halaman tidak ditemukan..');
        window.location = '../index.php';
    </script>";
    exit();
}

?>

<style>

/* =====================================
   CONTAINER HALAMAN
===================================== */

.user-form-page {
    padding-top: 8px;
    padding-bottom: 40px;
}


/* =====================================
   HEADER
===================================== */

.user-page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;

    flex-wrap: wrap;

    gap: 15px;

    padding: 18px 0;

    margin-bottom: 25px;

    border-bottom: 1px solid #e6e5eb;
}

.user-page-title {
    margin: 0;

    display: flex;
    align-items: center;

    gap: 12px;

    font-size: 27px;

    font-weight: 600;

    color: #1f1f37;
}

.user-page-title i {
    width: 43px;
    height: 43px;

    display: inline-flex;

    align-items: center;
    justify-content: center;

    border-radius: 12px;

    background: #efeeff;

    color: #7571f9;

    font-size: 21px;
}


/* =====================================
   BUTTON KEMBALI
===================================== */

.btn-kembali-user {
    display: inline-flex;

    align-items: center;

    gap: 8px;

    padding: 8px 15px;

    border: 1px solid #dedfe6;

    border-radius: 9px;

    background: #fff;

    color: #494a57;

    text-decoration: none;

    font-size: 14px;

    transition: all .2s ease;
}

.btn-kembali-user:hover {
    background: #f8f8fa;

    color: #7571f9;

    border-color: #beb9ff;

    transform: translateX(-2px);
}


/* =====================================
   CARD FORM
===================================== */

.user-form-card {
    background: #fff;

    border: 1px solid #e8e7f0;

    border-radius: 15px;

    padding: 28px;

    box-shadow: 0 5px 20px rgba(0, 0, 0, .04);
}


/* =====================================
   CARD FOTO
===================================== */

.user-photo-card {
    height: 100%;

    background: #f8f8fc;

    border: 1px solid #e8e8f3;

    border-radius: 14px;

    padding: 25px 20px;

    text-align: center;
}

.user-photo-title {
    font-size: 16px;

    font-weight: 600;

    color: #383751;

    margin-bottom: 18px;
}

.user-photo-title i {
    color: #7571f9;

    margin-right: 7px;
}


/* =====================================
   FOTO PREVIEW
===================================== */

.preview-wrapper {
    width: 140px;
    height: 140px;

    margin: 0 auto 18px;

    padding: 5px;

    background: #fff;

    border: 2px solid #e4e4f0;

    border-radius: 50%;

    box-shadow: 0 4px 12px rgba(0, 0, 0, .06);

    overflow: hidden;

    transition: all .25s ease;
}

.preview-wrapper:hover {
    border-color: #7571f9;

    transform: translateY(-3px);

    box-shadow: 0 7px 18px rgba(117, 113, 249, .15);
}

.preview-wrapper img {
    width: 100%;
    height: 100%;

    object-fit: cover;

    border-radius: 50%;
}


/* =====================================
   FILE INPUT
===================================== */

.user-file-input {
    font-size: 13px;

    border-radius: 8px;

    padding: 8px;

    background: #fff;
}

.user-file-input:focus {
    border-color: #b3aefc;

    box-shadow: 0 0 0 .15rem rgba(117, 113, 249, .12);
}


/* =====================================
   INFO FOTO
===================================== */

.photo-info {
    display: block;

    margin-top: 10px;

    color: #6d6b80;

    font-size: 12px;

    line-height: 1.7;
}


/* =====================================
   FORM LABEL
===================================== */

.user-form-card .form-label {
    color: #383751;

    font-size: 14px;

    font-weight: 600;

    margin-bottom: 7px;
}

.user-form-card .form-label i {
    color: #7571f9;

    margin-right: 5px;
}


/* =====================================
   INPUT
===================================== */

.user-form-card .form-control,
.user-form-card .form-select {
    min-height: 43px;

    border: 1px solid #dfdfe8;

    border-radius: 9px;

    font-size: 14px;

    color: #383751;

    background-color: #fff;

    transition: all .2s ease;
}

.user-form-card textarea.form-control {
    min-height: 95px;

    resize: vertical;
}

.user-form-card .form-control::placeholder {
    color: #9d9caf;
}

.user-form-card .form-control:focus,
.user-form-card .form-select:focus {
    border-color: #b3aefc;

    box-shadow: 0 0 0 .18rem rgba(117, 113, 249, .10);

    outline: none;
}


/* =====================================
   FORM GROUP
===================================== */

.form-group-user {
    margin-bottom: 18px;
}


/* =====================================
   PASSWORD INPUT
===================================== */

.password-wrapper {
    position: relative;
}

.password-wrapper .form-control {
    padding-right: 45px;
}

.password-icon {
    position: absolute;

    right: 14px;

    top: 50%;

    transform: translateY(-50%);

    color: #9d9caf;

    cursor: pointer;

    transition: color .2s ease;
}

.password-icon:hover {
    color: #7571f9;
}


/* =====================================
   BUTTON FORM
===================================== */

.form-action-user {
    display: flex;

    justify-content: flex-end;

    gap: 9px;

    margin-top: 25px;

    padding-top: 20px;

    border-top: 1px solid #ededf3;
}

.btn-reset-user,
.btn-simpan-user {

    padding: 8px 17px;

    border-radius: 8px;

    font-size: 13px;

    font-weight: 500;

    transition: all .2s ease;
}

.btn-reset-user {
    border: 1px solid #dc3545;

    color: #dc3545;

    background: #fff;
}

.btn-reset-user:hover {
    background: #dc3545;

    color: #fff;

    transform: translateY(-1px);
}

.btn-simpan-user {
    border: 1px solid #7571f9;

    color: #fff;

    background: #7571f9;
}

.btn-simpan-user:hover {
    background: #5f5ae0;

    border-color: #5f5ae0;

    transform: translateY(-1px);

    box-shadow: 0 4px 10px rgba(117, 113, 249, .20);
}


/* =====================================
   RESPONSIVE
===================================== */

@media (max-width: 991px) {

    .user-form-card {
        padding: 20px;
    }

    .user-photo-card {
        margin-bottom: 20px;
    }

}

@media (max-width: 576px) {

    .user-page-title {
        font-size: 22px;
    }

    .user-page-title i {
        width: 38px;
        height: 38px;

        font-size: 18px;
    }

    .user-page-header {
        align-items: flex-start;
    }

    .btn-kembali-user {
        width: 100%;

        justify-content: center;
    }

    .user-form-card {
        padding: 15px;
    }

    .form-action-user {
        justify-content: stretch;
    }

    .btn-reset-user,
    .btn-simpan-user {
        flex: 1;
    }

}

</style>


<div class="user-form-page page-content-wrap">


    <!-- =====================================
         HEADER HALAMAN
    ====================================== -->

    <div class="user-page-header">

        <h1 class="user-page-title">

            <i class="bi bi-person-plus"></i>

            User Baru

        </h1>


        <a href="<?= $main_url ?>user"
           class="btn-kembali-user">

            <i class="bi bi-arrow-left"></i>

            Kembali

        </a>

    </div>


    <!-- =====================================
         FORM USER
    ====================================== -->

    <form action="proses-user.php"
          method="post"
          enctype="multipart/form-data">

        <div class="user-form-card">

            <div class="row g-4">


                <!-- =================================
                     FOTO USER
                ================================== -->

                <div class="col-lg-4">

                    <div class="user-photo-card">

                        <div class="user-photo-title">

                            <i class="bi bi-camera"></i>

                            Foto User

                        </div>


                        <div class="preview-wrapper">

                            <img
                                src="<?= $main_url ?>asset/gambar/user.png"
                                alt="user"
                                class="tampil"
                            >

                        </div>


                        <input
                            type="file"
                            class="form-control user-file-input"
                            name="gambar"
                            id="gambar"
                            accept=".jpg,.jpeg,.png,.gif"
                            onchange="imgView()"
                        >


                        <span class="photo-info">

                            <i class="bi bi-info-circle me-1"></i>

                            Type file: JPG | PNG | GIF
                            <br>

                            Width = Height

                        </span>


                        <!-- BUTTON -->
                        <div class="form-action-user justify-content-center">

                            <button
                                type="reset"
                                class="btn-reset-user"
                            >

                                <i class="bi bi-x-lg me-1"></i>

                                Reset

                            </button>


                            <button
                                type="submit"
                                name="simpan"
                                class="btn-simpan-user"
                            >

                                <i class="bi bi-save me-1"></i>

                                Simpan

                            </button>

                        </div>

                    </div>

                </div>



                <!-- =================================
                     DATA USER
                ================================== -->

                <div class="col-lg-8">


                    <!-- USERNAME -->

                    <div class="form-group-user">

                        <label for="username"
                               class="form-label">

                            <i class="bi bi-person"></i>

                            Username

                        </label>

                        <input
                            type="text"
                            name="username"
                            class="form-control"
                            id="username"
                            placeholder="Masukkan username"
                            autocomplete="off"
                            autofocus
                            required
                        >

                    </div>



                    <!-- FULLNAME -->

                    <div class="form-group-user">

                        <label for="fullname"
                               class="form-label">

                            <i class="bi bi-person-vcard"></i>

                            Fullname

                        </label>

                        <input
                            type="text"
                            name="fullname"
                            class="form-control"
                            id="fullname"
                            placeholder="Masukkan nama lengkap user"
                            required
                        >

                    </div>



                    <!-- PASSWORD -->

                    <div class="form-group-user">

                        <label for="password"
                               class="form-label">

                            <i class="bi bi-lock"></i>

                            Password

                        </label>

                        <div class="password-wrapper">

                            <input
                                type="password"
                                name="password"
                                class="form-control"
                                id="password"
                                placeholder="Masukkan password user"
                                required
                            >

                            <i
                                class="bi bi-eye password-icon"
                                onclick="togglePassword('password', this)"
                            ></i>

                        </div>

                    </div>



                    <!-- KONFIRMASI PASSWORD -->

                    <div class="form-group-user">

                        <label for="password2"
                               class="form-label">

                            <i class="bi bi-shield-lock"></i>

                            Konfirmasi Password

                        </label>

                        <div class="password-wrapper">

                            <input
                                type="password"
                                name="password2"
                                class="form-control"
                                id="password2"
                                placeholder="Masukkan kembali password user"
                                required
                            >

                            <i
                                class="bi bi-eye password-icon"
                                onclick="togglePassword('password2', this)"
                            ></i>

                        </div>

                    </div>



                    <!-- JABATAN -->

                    <div class="form-group-user">

                        <label for="jabatan"
                               class="form-label">

                            <i class="bi bi-briefcase"></i>

                            Jabatan

                        </label>

                        <select
                            name="jabatan"
                            id="jabatan"
                            class="form-select"
                            required
                        >

                            <option value="">
                                -- Pilih Jabatan --
                            </option>

                            <option value="1">
                                Administrator
                            </option>

                            <option value="2">
                                Petugas
                            </option>

                            <option value="3">
                                Dokter
                            </option>

                            <option value="4">
                                Bidan
                            </option>

                            <option value="5">
                                Kepala Puskesmas
                            </option>

                        </select>

                    </div>



                    <!-- ALAMAT -->

                    <div class="form-group-user">

                        <label for="alamat"
                               class="form-label">

                            <i class="bi bi-geo-alt"></i>

                            Alamat

                        </label>

                        <textarea
                            name="alamat"
                            id="alamat"
                            rows="3"
                            class="form-control"
                            placeholder="Masukkan alamat user"
                        ></textarea>

                    </div>


                </div>

            </div>

        </div>

    </form>

</div>



<script>

/* =====================================
   PREVIEW GAMBAR
===================================== */

function imgView() {

    let gambar = document.getElementById('gambar');

    let tampil = document.querySelector('.tampil');

    if (gambar.files && gambar.files[0]) {

        let fileReader = new FileReader();

        fileReader.readAsDataURL(gambar.files[0]);

        fileReader.addEventListener('load', function(e) {

            tampil.src = e.target.result;

        });

    }

}


/* =====================================
   SHOW / HIDE PASSWORD
===================================== */

function togglePassword(id, icon) {

    let input = document.getElementById(id);

    if (input.type === "password") {

        input.type = "text";

        icon.classList.remove("bi-eye");

        icon.classList.add("bi-eye-slash");

    } else {

        input.type = "password";

        icon.classList.remove("bi-eye-slash");

        icon.classList.add("bi-eye");

    }

}

</script>


<?php

require "../template/footer.php";

?>