<?php

session_start();

require "../template/rbac.php";

// Hanya Admin Sistem
cekAkses([ROLE_ADMIN]);

require "../config.php";

$title = "Edit User - rekammedispuskesmas";


require "../template/header.php";
require "../template/navbar.php";
require "../template/sidebar.php";


$id = $_GET['id'];

$queryUser = mysqli_query($koneksi, "SELECT * FROM tbl_user WHERE userid = $id");

$user = mysqli_fetch_assoc($queryUser);


// Jabatan/hak akses sekarang diatur khusus lewat halaman Atur Hak Akses
// (mendukung multi-role), jadi di sini cuma ditampilkan sebagai info.
$namaRoleUser = [
    1 => 'Admin Sistem',
    2 => 'Petugas',
    3 => 'Dokter',
    4 => 'Bidan',
    5 => 'Kepala Puskesmas',
];

$roleUserSaatIni = [];

$queryRoleUser = mysqli_query($koneksi, "
    SELECT role_id FROM tbl_user_role WHERE userid = '$id' ORDER BY role_id ASC
");

while ($rr = mysqli_fetch_assoc($queryRoleUser)) {
    $roleUserSaatIni[] = (int) $rr['role_id'];
}

if (empty($roleUserSaatIni)) {
    $roleUserSaatIni[] = (int) $user['jabatan'];
}

?>

<style>

/* =====================================
   HALAMAN EDIT USER
===================================== */

.edit-user-page {
    padding-top: 8px;
    padding-bottom: 40px;
}


/* =====================================
   HEADER
===================================== */

.edit-user-header {
    display: flex;
    justify-content: space-between;
    align-items: center;

    flex-wrap: wrap;
    gap: 15px;

    padding: 18px 0;

    margin-bottom: 25px;

    border-bottom: 1px solid #e6e5eb;
}

.edit-user-title {
    margin: 0;

    display: flex;
    align-items: center;

    gap: 12px;

    font-size: 27px;
    font-weight: 600;

    color: #1f1f37;
}

.edit-user-title i {
    width: 43px;
    height: 43px;

    display: inline-flex;

    align-items: center;
    justify-content: center;

    border-radius: 12px;

    background: #fff7e6;

    color: #f0ad00;

    font-size: 21px;
}


/* =====================================
   BUTTON KEMBALI
===================================== */

.btn-kembali-edit {
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

.btn-kembali-edit:hover {
    background: #f8f8fa;

    color: #7571f9;

    border-color: #beb9ff;

    transform: translateX(-2px);
}


/* =====================================
   CARD FORM
===================================== */

.edit-user-card {
    background: #ffffff;

    border: 1px solid #e8e7f0;

    border-radius: 15px;

    padding: 28px;

    box-shadow: 0 5px 20px rgba(0, 0, 0, .04);
}


/* =====================================
   CARD FOTO
===================================== */

.edit-photo-card {
    height: 100%;

    background: #f8f8fc;

    border: 1px solid #e8e8f3;

    border-radius: 14px;

    padding: 25px 20px;

    text-align: center;
}

.edit-photo-title {
    font-size: 16px;

    font-weight: 600;

    color: #383751;

    margin-bottom: 18px;
}

.edit-photo-title i {
    color: #f0ad00;

    margin-right: 7px;
}


/* =====================================
   FOTO USER
===================================== */

.edit-preview-wrapper {
    width: 140px;
    height: 140px;

    margin: 0 auto 18px;

    padding: 5px;

    background: #fff;

    border: 2px solid #e4e4f0;

    border-radius: 50%;

    overflow: hidden;

    box-shadow: 0 4px 12px rgba(0, 0, 0, .06);

    transition: all .25s ease;
}

.edit-preview-wrapper:hover {
    border-color: #f0ad00;

    transform: translateY(-3px);

    box-shadow: 0 7px 18px rgba(240, 173, 0, .15);
}

.edit-preview-wrapper img {
    width: 100%;
    height: 100%;

    object-fit: cover;

    border-radius: 50%;
}


/* =====================================
   FILE INPUT
===================================== */

.edit-file-input {
    font-size: 13px;

    border-radius: 8px;

    padding: 8px;

    background: #fff;
}

.edit-file-input:focus {
    border-color: #b3aefc;

    box-shadow: 0 0 0 .15rem rgba(117, 113, 249, .12);
}


/* =====================================
   KETERANGAN FOTO
===================================== */

.photo-info-edit {
    display: block;

    margin-top: 10px;

    color: #6d6b80;

    font-size: 12px;

    line-height: 1.7;
}


/* =====================================
   LABEL
===================================== */

.edit-user-card .form-label {
    color: #383751;

    font-size: 14px;

    font-weight: 600;

    margin-bottom: 7px;
}

.edit-user-card .form-label i {
    color: #7571f9;

    margin-right: 5px;
}


/* =====================================
   INPUT
===================================== */

.edit-user-card .form-control,
.edit-user-card .form-select {
    min-height: 43px;

    border: 1px solid #dfdfe8;

    border-radius: 9px;

    font-size: 14px;

    color: #383751;

    background-color: #fff;

    transition: all .2s ease;
}

.edit-user-card textarea.form-control {
    min-height: 100px;

    resize: vertical;
}

.edit-user-card .form-control:focus,
.edit-user-card .form-select:focus {
    border-color: #b3aefc;

    box-shadow: 0 0 0 .18rem rgba(117, 113, 249, .10);

    outline: none;
}


/* =====================================
   FORM GROUP
===================================== */

.edit-form-group {
    margin-bottom: 19px;
}


/* =====================================
   INFO USERNAME
===================================== */

.username-info {
    display: block;

    margin-top: 5px;

    font-size: 11px;

    color: #9d9caf;
}


/* =====================================
   BUTTON UPDATE
===================================== */

.edit-action {
    display: flex;

    justify-content: flex-end;

    margin-top: 25px;

    padding-top: 20px;

    border-top: 1px solid #ededf3;
}

.btn-update-user {
    display: inline-flex;

    align-items: center;

    gap: 7px;

    padding: 9px 19px;

    border-radius: 8px;

    border: 1px solid #7571f9;

    background: #7571f9;

    color: #fff;

    font-size: 13px;

    font-weight: 500;

    transition: all .2s ease;
}

.btn-update-user:hover {
    background: #5f5ae0;

    border-color: #5f5ae0;

    color: #fff;

    transform: translateY(-1px);

    box-shadow: 0 4px 10px rgba(117, 113, 249, .20);
}


/* =====================================
   RESPONSIVE
===================================== */

@media (max-width: 991px) {

    .edit-user-card {
        padding: 20px;
    }

    .edit-photo-card {
        margin-bottom: 20px;
    }

}


@media (max-width: 576px) {

    .edit-user-title {
        font-size: 22px;
    }

    .edit-user-title i {
        width: 38px;
        height: 38px;

        font-size: 18px;
    }

    .edit-user-header {
        align-items: flex-start;
    }

    .btn-kembali-edit {
        width: 100%;

        justify-content: center;
    }

    .edit-user-card {
        padding: 15px;
    }

    .edit-action {
        justify-content: stretch;
    }

    .btn-update-user {
        width: 100%;

        justify-content: center;
    }

}

</style>


<div class="edit-user-page page-content-wrap">


    <!-- =====================================
         HEADER
    ====================================== -->

    <div class="edit-user-header">

        <h1 class="edit-user-title">

            <i class="bi bi-person-gear"></i>

            Edit User

        </h1>


        <a href="<?= $main_url ?>user"
           class="btn-kembali-edit">

            <i class="bi bi-arrow-left"></i>

            Kembali

        </a>

    </div>



    <!-- =====================================
         FORM EDIT USER
    ====================================== -->

    <form action="proses-user.php"
          method="post"
          enctype="multipart/form-data">


        <div class="edit-user-card">

            <div class="row g-4">


                <!-- =================================
                     FOTO USER
                ================================== -->

                <div class="col-lg-4">

                    <div class="edit-photo-card">


                        <div class="edit-photo-title">

                            <i class="bi bi-camera"></i>

                            Foto User

                        </div>


                        <input
                            type="hidden"
                            name="gbrlama"
                            value="<?= $user['gambar'] ?>"
                        >


                        <div class="edit-preview-wrapper">

                            <img
                                src="<?= $main_url ?>asset/gambar/<?= $user['gambar'] ?>"
                                alt="user"
                                class="tampil"
                            >

                        </div>


                        <input
                            type="file"
                            class="form-control edit-file-input"
                            name="gambar"
                            id="gambar"
                            accept=".jpg,.jpeg,.png,.gif"
                            onchange="imgView()"
                        >


                        <span class="photo-info-edit">

                            <i class="bi bi-info-circle me-1"></i>

                            Type file: JPG | PNG | GIF
                            <br>

                            Width = Height

                        </span>


                    </div>

                </div>



                <!-- =================================
                     DATA USER
                ================================== -->

                <div class="col-lg-8">


                    <!-- ID USER -->

                    <input
                        type="hidden"
                        name="id"
                        value="<?= $user['userid'] ?>"
                    >


                    <!-- USERNAME LAMA -->

                    <input
                        type="hidden"
                        name="usernameLama"
                        value="<?= $user['username'] ?>"
                    >


                    <!-- USERNAME -->

                    <div class="edit-form-group">

                        <label
                            for="username"
                            class="form-label"
                        >

                            <i class="bi bi-person"></i>

                            Username

                        </label>


                        <input
                            type="text"
                            name="username"
                            class="form-control"
                            id="username"
                            placeholder="Masukkan username"
                            value="<?= $user['username'] ?>"
                            autocomplete="off"
                            autofocus
                            required
                        >


                        <span class="username-info">

                            <i class="bi bi-info-circle me-1"></i>

                            Username digunakan untuk login ke dalam sistem.

                        </span>

                    </div>



                    <!-- FULLNAME -->

                    <div class="edit-form-group">

                        <label
                            for="fullname"
                            class="form-label"
                        >

                            <i class="bi bi-person-vcard"></i>

                            Fullname

                        </label>


                        <input
                            type="text"
                            name="fullname"
                            class="form-control"
                            id="fullname"
                            value="<?= $user['fullname'] ?>"
                            placeholder="Masukkan nama lengkap user"
                            required
                        >

                    </div>



                    <!-- JABATAN (read-only, diatur lewat Atur Hak Akses) -->

                    <div class="edit-form-group">

                        <label class="form-label">

                            <i class="bi bi-briefcase"></i>

                            Jabatan

                        </label>

                        <div>

                            <?php

                            // Warna badge disamakan dengan user/index.php (badge-admin/
                            // petugas/dokter/bidan/kepala), supaya tampilan jabatan yang
                            // sama terlihat konsisten di semua halaman.
                            $kelasRole = [
                                1 => 'badge-admin',
                                2 => 'badge-petugas',
                                3 => 'badge-dokter',
                                4 => 'badge-bidan',
                                5 => 'badge-kepala',
                            ];

                            foreach ($roleUserSaatIni as $roleId) {
                                $kelas = $kelasRole[$roleId] ?? '';
                            ?>

                                <span class="badge-jabatan <?= $kelas; ?> me-1">
                                    <?= htmlspecialchars($namaRoleUser[$roleId] ?? 'Tidak diketahui'); ?>
                                </span>

                            <?php } ?>

                        </div>

                        <small class="text-muted d-block mt-1">
                            Jabatan/hak akses (bisa lebih dari satu) diatur lewat
                            <a href="edit-hak-akses.php?id=<?= $user['userid']; ?>">Atur Hak Akses</a>.
                        </small>

                    </div>



                    <!-- ALAMAT -->

                    <div class="edit-form-group">

                        <label
                            for="alamat"
                            class="form-label"
                        >

                            <i class="bi bi-geo-alt"></i>

                            Alamat

                        </label>


                        <textarea
                            name="alamat"
                            id="alamat"
                            rows="3"
                            class="form-control"
                            placeholder="Masukkan alamat user"
                        ><?= $user['alamat'] ?></textarea>

                    </div>



                    <!-- BUTTON UPDATE -->

                    <div class="edit-action">

                        <button
                            type="submit"
                            name="update"
                            class="btn-update-user"
                        >

                            <i class="bi bi-save"></i>

                            Update User

                        </button>

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

</script>



<?php

require "../template/footer.php";

?>