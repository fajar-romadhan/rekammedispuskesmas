<?php

session_start();

require "../template/rbac.php";

// Hanya Bidan
cekAkses([ROLE_BIDAN]);

require "../config.php";

$title = "Edit Rekam Medis Kebidanan - rekammedispuskesmas";

/* =========================
   CEK ID
========================= */

if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "<script>
            alert('ID rekam medis tidak ditemukan!');
            window.location='index.php';
          </script>";
    exit();
}

$id = mysqli_real_escape_string($koneksi, $_GET['id']);

/* =========================
   AMBIL DATA REKAM MEDIS BIDAN
========================= */

$query = mysqli_query($koneksi, "
    SELECT 
        r.id,
        r.id_pasien,
        r.no_rm,
        r.tgl_rm,
        r.keluhan,
        r.hasil_pemeriksaan,
        r.diagnosa,
        r.tindakan,
        r.keterangan,
        p.nama
    FROM tbl_rekammedis_bidan r
    INNER JOIN tbl_pasien p 
        ON r.id_pasien = p.id
    WHERE r.id = '$id'
");

if (!$query) {
    die("Query gagal: " . mysqli_error($koneksi));
}

$data = mysqli_fetch_assoc($query);

if (!$data) {
    echo "<script>
            alert('Data rekam medis kebidanan tidak ditemukan!');
            window.location='index.php';
          </script>";
    exit();
}

/* =========================
   TEMPLATE
========================= */

require "../template/header.php";
require "../template/navbar.php";
require "../template/sidebar.php";

?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap
            align-items-center pt-3 pb-2 mb-3 border-bottom">

    <h1 class="h2">Edit Rekam Medis Kebidanan</h1>

</div>


<div class="card">

    <div class="card-header">
        <i class="bi bi-pencil-square"></i>
        Edit Data Rekam Medis Kebidanan
    </div>


    <div class="card-body">

        <form action="proses-data.php" method="POST">

            <!-- ID REKAM MEDIS -->
            <input type="hidden"
                   name="id"
                   value="<?= htmlspecialchars($data['id']); ?>">


            <div class="row mb-3">

                <div class="col-md-6">

                    <label class="form-label">
                        No RM
                    </label>

                    <input type="text"
                           class="form-control"
                           value="<?= htmlspecialchars($data['no_rm']); ?>"
                           readonly>

                </div>


                <div class="col-md-6">

                    <label class="form-label">
                        Nama Pasien
                    </label>

                    <input type="text"
                           class="form-control"
                           value="<?= htmlspecialchars($data['nama']); ?>"
                           readonly>

                </div>

            </div>


            <div class="row mb-3">

                <div class="col-md-6">

                    <label class="form-label">
                        Tanggal Pemeriksaan
                    </label>

                    <input type="date"
                           name="tgl_rm"
                           class="form-control"
                           value="<?= htmlspecialchars($data['tgl_rm']); ?>"
                           required>

                </div>


                <div class="col-md-6">

                    <label class="form-label">
                        Keluhan
                    </label>

                    <input type="text"
                           name="keluhan"
                           class="form-control"
                           value="<?= htmlspecialchars($data['keluhan']); ?>"
                           required>

                </div>

            </div>


            <!-- HASIL PEMERIKSAAN -->
            <div class="mb-3">

                <label class="form-label">
                    Hasil Pemeriksaan
                </label>

                <textarea name="hasil_pemeriksaan"
                          class="form-control"
                          rows="3"><?= htmlspecialchars($data['hasil_pemeriksaan']); ?></textarea>

            </div>


            <!-- DIAGNOSA -->
            <div class="mb-3">

                <label class="form-label">
                    Diagnosa
                </label>

                <textarea name="diagnosa"
                          class="form-control"
                          rows="3"
                          required><?= htmlspecialchars($data['diagnosa']); ?></textarea>

            </div>


            <!-- TINDAKAN -->
            <div class="mb-3">

                <label class="form-label">
                    Tindakan
                </label>

                <textarea name="tindakan"
                          class="form-control"
                          rows="3"><?= htmlspecialchars($data['tindakan']); ?></textarea>

            </div>


            <!-- KETERANGAN -->
            <div class="mb-3">

                <label class="form-label">
                    Keterangan
                </label>

                <textarea name="keterangan"
                          class="form-control"
                          rows="3"><?= htmlspecialchars($data['keterangan']); ?></textarea>

            </div>


            <div class="mt-4">

                <button type="submit"
                        name="update-kebidanan"
                        class="btn btn-primary">

                    <i class="bi bi-save"></i>
                    Simpan Perubahan

                </button>


                <a href="index.php"
                   class="btn btn-secondary">

                    <i class="bi bi-arrow-left"></i>
                    Kembali

                </a>

            </div>

        </form>

    </div>

</div>