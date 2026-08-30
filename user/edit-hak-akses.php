<?php 
 
session_start(); 

require "../template/rbac.php";

// Hanya Admin Sistem
cekAkses([ROLE_ADMIN]);

require "../config.php"; 
 
$title = "Atur Hak Akses - rekammedispuskesmas"; 
 
require "../template/header.php"; 
require "../template/navbar.php"; 
require "../template/sidebar.php"; 
 
 
// ========================================== 
// CEK ID USER 
// ========================================== 
 
if (!isset($_GET['id']) || empty($_GET['id'])) { 
    header("location: hak-akses.php"); 
    exit(); 
} 
 
$id = mysqli_real_escape_string($koneksi, $_GET['id']); 
 
 
// ========================================== 
// AMBIL DATA USER 
// ========================================== 
 
$query = mysqli_query($koneksi, " 
    SELECT * 
    FROM tbl_user 
    WHERE userid = '$id' 
"); 
 
if (mysqli_num_rows($query) == 0) { 
    echo "<script> 
            alert('Data user tidak ditemukan!'); 
            window.location='hak-akses.php'; 
          </script>"; 
    exit(); 
} 
 
$user = mysqli_fetch_assoc($query);


// ==========================================
// AMBIL ROLE YANG SUDAH DIMILIKI USER
// ==========================================

$rolesDimiliki = [];

$queryRole = mysqli_query($koneksi, "
    SELECT role_id FROM tbl_user_role WHERE userid = '$id'
");

while ($r = mysqli_fetch_assoc($queryRole)) {
    $rolesDimiliki[] = (int) $r['role_id'];
}

if (empty($rolesDimiliki)) {
    // Jaga-jaga: user lama yang belum sempat punya baris di tbl_user_role.
    $rolesDimiliki[] = (int) $user['jabatan'];
}

?>

<style>

/* =========================================
   HALAMAN ATUR HAK AKSES
========================================= */

.hak-akses-wrapper {
    padding-top: 25px;
    padding-bottom: 40px;
}


/* =========================================
   HEADER HALAMAN
========================================= */

.hak-header {
    background: #ffffff;
    border: 1px solid #e9e9ef;
    border-radius: 16px;
    padding: 20px 24px;
    margin-bottom: 25px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);

    display: flex;
    justify-content: space-between;
    align-items: center;
}

.hak-header-left h1 {
    margin: 0;
    font-size: 25px;
    font-weight: 600;
    color: #343540;
}

.hak-header-left h1 i {
    color: #7571f9;
    margin-right: 8px;
}

.hak-header-left p {
    margin: 7px 0 0 36px;
    color: #6c757d;
    font-size: 14px;
}


/* =========================================
   TOMBOL KEMBALI
========================================= */

.btn-kembali {
    display: inline-flex;
    align-items: center;
    gap: 5px;

    padding: 8px 14px;

    border: 1px solid #dedfe6;
    border-radius: 8px;

    color: #494a57;
    background: #ffffff;

    text-decoration: none;
    font-size: 13px;
    font-weight: 500;

    transition: all 0.2s ease;
}

.btn-kembali:hover {
    color: #7571f9;
    border-color: #7571f9;
    background: #f5f5ff;
}


/* =========================================
   CARD UTAMA
========================================= */

.hak-card {
    border: none;
    border-radius: 16px;
    overflow: hidden;

    background: #ffffff;

    box-shadow: 0 5px 22px rgba(0, 0, 0, 0.07);
}


/* =========================================
   CARD HEADER
========================================= */

.hak-card-header {
    padding: 18px 24px;

    background: #ffffff;

    border-bottom: 1px solid #eeeeee;

    font-size: 16px;
    font-weight: 600;

    color: #343540;
}

.hak-card-header i {
    color: #7571f9;
}


/* =========================================
   CARD BODY
========================================= */

.hak-card-body {
    padding: 28px;
}


/* =========================================
   FORM
========================================= */

.form-group-hak {
    margin-bottom: 22px;
}

.form-group-hak .form-label {
    display: block;

    margin-bottom: 8px;

    color: #343540;

    font-size: 14px;
    font-weight: 600;
}

.form-group-hak .form-label i {
    color: #7571f9;
}


/* INPUT */

.hak-input {
    width: 100%;

    padding: 11px 14px;

    border: 1px solid #dedfe6;
    border-radius: 9px;

    background: #f8f8fa;

    color: #494a57;

    font-size: 14px;

    transition: all 0.2s ease;
}

.hak-input:focus {
    outline: none;

    border-color: #b3aefc;

    box-shadow: 0 0 0 3px rgba(117, 113, 249, 0.08);
}


/* INPUT READONLY */

.hak-input[readonly] {
    cursor: default;
}


/* SELECT */

.hak-select {
    width: 100%;

    padding: 11px 14px;

    border: 1px solid #dedfe6;
    border-radius: 9px;

    background-color: #ffffff;

    color: #494a57;

    font-size: 14px;

    cursor: pointer;

    transition: all 0.2s ease;
}

.hak-select:focus {
    border-color: #b3aefc;

    box-shadow: 0 0 0 3px rgba(117, 113, 249, 0.08);

    outline: none;
}


/* CHECKBOX MULTI ROLE */

.hak-akses-checkbox-group {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
}

.hak-akses-checkbox {
    display: inline-flex;
    align-items: center;
    gap: 8px;

    padding: 10px 14px;

    border: 1px solid #dedfe6;
    border-radius: 9px;

    background: #f8f8fa;

    color: #494a57;

    font-size: 14px;
    font-weight: 500;

    cursor: pointer;

    transition: all 0.2s ease;
}

.hak-akses-checkbox:hover {
    border-color: #b3aefc;
}

.hak-akses-checkbox:has(input:checked) {
    border-color: #7571f9;
    background: #f0efff;
    color: #343540;
}

.hak-akses-checkbox input {
    cursor: pointer;
}


/* =========================================
   INFORMASI HAK AKSES
========================================= */

.hak-info {
    margin-top: 5px;
    margin-bottom: 25px;

    padding: 18px 20px;

    border: 1px solid #dcdcf8;
    border-radius: 12px;

    background: #f8f8ff;
}

.hak-info-title {
    margin-bottom: 13px;

    color: #7571f9;

    font-size: 14px;
    font-weight: 600;
}

.hak-info-title i {
    margin-right: 5px;
}

.hak-info-item {
    display: flex;

    align-items: flex-start;

    margin-bottom: 9px;

    color: #5c636a;

    font-size: 13px;

    line-height: 1.6;
}

.hak-info-item:last-child {
    margin-bottom: 0;
}

.hak-info-item strong {
    min-width: 145px;

    color: #343540;
}


/* =========================================
   BUTTON
========================================= */

.hak-button-area {
    display: flex;
    gap: 10px;

    padding-top: 5px;
}

.btn-hak-batal,
.btn-hak-simpan {
    display: inline-flex;

    align-items: center;
    justify-content: center;

    gap: 5px;

    padding: 9px 17px;

    border-radius: 8px;

    font-size: 13px;
    font-weight: 500;

    transition: all 0.2s ease;
}


/* BATAL */

.btn-hak-batal:hover {
    transform: translateY(-1px);
}


/* SIMPAN */

.btn-hak-simpan {
    box-shadow: 0 3px 8px rgba(117, 113, 249, 0.15);
}

.btn-hak-simpan:hover {
    transform: translateY(-1px);

    box-shadow: 0 5px 12px rgba(117, 113, 249, 0.22);
}


/* =========================================
   RESPONSIVE
========================================= */

@media (max-width: 768px) {

    .hak-akses-wrapper {
        padding-top: 15px;
    }

    .hak-header {
        padding: 17px;

        flex-direction: column;
        align-items: flex-start;

        gap: 15px;
    }

    .hak-header-left h1 {
        font-size: 21px;
    }

    .hak-header-left p {
        margin-left: 0;
        margin-top: 7px;
    }

    .btn-kembali {
        width: 100%;

        justify-content: center;
    }

    .hak-card-body {
        padding: 20px;
    }

    .hak-info-item {
        display: block;
    }

    .hak-info-item strong {
        display: block;
        margin-bottom: 2px;
    }

    .hak-button-area {
        flex-direction: column;
    }

    .btn-hak-batal,
    .btn-hak-simpan {
        width: 100%;
    }
}

</style>


<div class="page-content-wrap">

    <div class="hak-akses-wrapper">


        <!-- =====================================
             HEADER
        ====================================== -->

        <div class="hak-header">

            <div class="hak-header-left">

                <h1>
                    <i class="bi bi-shield-lock"></i>
                    Atur Hak Akses
                </h1>

                <p>
                    Mengatur jabatan dan hak akses pengguna sistem.
                </p>

            </div>


            <a href="hak-akses.php" class="btn-kembali">

                <i class="bi bi-arrow-left"></i>

                Kembali

            </a>

        </div>



        <!-- =====================================
             CARD
        ====================================== -->

        <div class="hak-card">


            <!-- CARD HEADER -->

            <div class="hak-card-header">

                <i class="bi bi-person-gear me-2"></i>

                Pengaturan Hak Akses Pengguna

            </div>



            <!-- CARD BODY -->

            <div class="hak-card-body">

                <form action="proses-hak-akses.php" method="post">


                    <input type="hidden" 
                           name="userid" 
                           value="<?= $user['userid']; ?>">



                    <!-- =================================
                         NAMA
                    ================================== -->

                    <div class="form-group-hak">

                        <label class="form-label">

                            <i class="bi bi-person me-1"></i>

                            Nama Pengguna

                        </label>

                        <input type="text"
                               class="hak-input"
                               value="<?= htmlspecialchars($user['fullname']); ?>"
                               readonly>

                    </div>



                    <!-- =================================
                         USERNAME
                    ================================== -->

                    <div class="form-group-hak">

                        <label class="form-label">

                            <i class="bi bi-person-badge me-1"></i>

                            Username

                        </label>

                        <input type="text"
                               class="hak-input"
                               value="<?= htmlspecialchars($user['username']); ?>"
                               readonly>

                    </div>



                    <!-- =================================
                         HAK AKSES
                    ================================== -->

                    <div class="form-group-hak">

                        <label class="form-label">

                            <i class="bi bi-shield-check me-1"></i>

                            Jabatan / Hak Akses

                        </label>

                        <div class="hak-akses-checkbox-group">

                            <?php

                            $daftarRole = [
                                1 => 'Admin Sistem',
                                2 => 'Petugas',
                                3 => 'Dokter',
                                4 => 'Bidan',
                                5 => 'Kepala Puskesmas',
                            ];

                            foreach ($daftarRole as $roleId => $namaRole) {
                            ?>

                                <label class="hak-akses-checkbox">

                                    <input type="checkbox"
                                           name="jabatan[]"
                                           value="<?= $roleId; ?>"
                                        <?= in_array($roleId, $rolesDimiliki) ? 'checked' : ''; ?>>

                                    <?= htmlspecialchars($namaRole); ?>

                                </label>

                            <?php } ?>

                        </div>

                        <small class="text-muted d-block mt-1">
                            Bisa pilih lebih dari satu (multi role).
                        </small>

                    </div>



                    <!-- =================================
                         KETERANGAN
                    ================================== -->

                    <div class="hak-info">

                        <div class="hak-info-title">

                            <i class="bi bi-info-circle"></i>

                            Keterangan Hak Akses

                        </div>


                        <div class="hak-info-item">

                            <strong>Admin Sistem:</strong>

                            <span>
                                Mengelola user dan hak akses sistem.
                            </span>

                        </div>


                        <div class="hak-info-item">

                            <strong>Petugas:</strong>

                            <span>
                                Mengelola pasien, antrian, obat, rekam medis,
                                dan laporan rekam medis.
                            </span>

                        </div>


                        <div class="hak-info-item">

                            <strong>Dokter:</strong>

                            <span>
                                Mengelola rekam medis dan melihat laporan
                                rekam medis.
                            </span>

                        </div>


                        <div class="hak-info-item">

                            <strong>Bidan:</strong>

                            <span>
                                Mengelola rekam medis kebidanan.
                            </span>

                        </div>


                        <div class="hak-info-item">

                            <strong>Kepala Puskesmas:</strong>

                            <span>
                                Melihat laporan sistem.
                            </span>

                        </div>

                    </div>



                    <!-- =================================
                         BUTTON
                    ================================== -->

                    <div class="hak-button-area">


                        <a href="hak-akses.php"
                           class="btn btn-outline-secondary btn-hak-batal">

                            <i class="bi bi-x-lg"></i>

                            Batal

                        </a>


                        <button type="submit"
                                name="simpan"
                                class="btn btn-primary btn-hak-simpan">

                            <i class="bi bi-save"></i>

                            Simpan Hak Akses

                        </button>


                    </div>


                </form>

            </div>

        </div>

    </div>

</div>


<?php 

require "../template/footer.php"; 

?>