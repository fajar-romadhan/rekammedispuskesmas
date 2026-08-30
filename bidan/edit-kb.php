<?php

session_start();

require "../template/rbac.php";

// Hanya Bidan
cekAkses([ROLE_BIDAN]);

require "../config.php";


/* =========================================================
   CEK ID
========================================================= */

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("location: register-kb.php");
    exit();
}

$id_kb = (int) $_GET['id'];


/* =========================================================
   AMBIL DATA KB
========================================================= */

$query = mysqli_query($koneksi, "
    SELECT *
    FROM tbl_kb
    WHERE id_kb = $id_kb
");

if (!$query) {
    die("Query gagal : " . mysqli_error($koneksi));
}

$data = mysqli_fetch_assoc($query);

if (!$data) {
    echo "<script>
            alert('Data peserta KB tidak ditemukan!');
            window.location='register-kb.php';
          </script>";
    exit();
}


/* =========================================================
   PROSES UPDATE
========================================================= */

if (isset($_POST['simpan'])) {

    $tanggal        = mysqli_real_escape_string($koneksi, $_POST['tanggal']);
    $no_kk          = mysqli_real_escape_string($koneksi, $_POST['no_kk']);
    $no_peserta_kb  = mysqli_real_escape_string($koneksi, $_POST['no_peserta_kb']);
    $tanggal_lahir  = mysqli_real_escape_string($koneksi, $_POST['tanggal_lahir']);
    $nama_istri     = mysqli_real_escape_string($koneksi, $_POST['nama_istri']);
    $nama_suami     = mysqli_real_escape_string($koneksi, $_POST['nama_suami']);
    $jumlah_anak    = mysqli_real_escape_string($koneksi, $_POST['jumlah_anak']);
    $jenis_kb       = mysqli_real_escape_string($koneksi, $_POST['jenis_kb']);
    $kunjungan      = mysqli_real_escape_string($koneksi, $_POST['kunjungan']);


    $update = mysqli_query($koneksi, "
        UPDATE tbl_kb SET

            tanggal = '$tanggal',

            no_kk = '$no_kk',

            no_peserta_kb = '$no_peserta_kb',

            tanggal_lahir = '$tanggal_lahir',

            nama_istri = '$nama_istri',

            nama_suami = '$nama_suami',

            jumlah_anak = '$jumlah_anak',

            jenis_kb = '$jenis_kb',

            kunjungan = '$kunjungan'

        WHERE id_kb = $id_kb
    ");


    if ($update) {

        echo "<script>
                alert('Data peserta KB berhasil diperbarui!');
                window.location='register-kb.php';
              </script>";

        exit();

    } else {

        $error = mysqli_error($koneksi);

    }

}


$title = "Edit Data Peserta KB - Rekam Medis Puskesmas";


require "../template/header.php";
require "../template/navbar.php";
require "../template/sidebar.php";

?>


<style>

.edit-kb-page {
    padding-bottom: 50px;
}

.edit-kb-header {
    padding-top: 25px;
    padding-bottom: 22px;
    margin-bottom: 25px;
    border-bottom: 1px solid #e9e9ef;
}

.edit-kb-header h1 {
    font-size: 28px;
    font-weight: 700;
    color: #212229;
    margin-bottom: 5px;
}

.edit-kb-header p {
    font-size: 14px;
    color: #6c757d;
}

.edit-kb-card {
    border: none;
    border-radius: 15px;
    box-shadow: 0 4px 18px rgba(0,0,0,.07);
}

.edit-kb-card .card-body {
    padding: 30px;
}

.form-label {
    font-weight: 600;
    font-size: 14px;
    color: #343540;
}

.form-control,
.form-select {
    border-radius: 9px;
    border: 1px solid #dedfe6;
    padding: 10px 12px;
    font-size: 14px;
}

.form-control:focus,
.form-select:focus {
    border-color: #6c757d;
    box-shadow: 0 0 0 3px rgba(33,37,41,.08);
}

.btn {
    border-radius: 9px;
    padding: 10px 18px;
}

</style>


<div class="edit-kb-page page-content-wrap">

    <!-- HEADER -->

    <div class="edit-kb-header">

        <h1>Edit Data Peserta KB</h1>

        <p class="mb-0">
            Ubah data register peserta keluarga berencana
        </p>

    </div>


    <!-- ERROR -->

    <?php if (isset($error)) { ?>

        <div class="alert alert-danger">

            <?= htmlspecialchars($error); ?>

        </div>

    <?php } ?>


    <!-- FORM -->

    <div class="card edit-kb-card">

        <div class="card-body">

            <form method="POST">


                <div class="row g-3">


                    <!-- TANGGAL -->

                    <div class="col-md-6">

                        <label class="form-label">
                            Tanggal
                        </label>

                        <input
                            type="date"
                            name="tanggal"
                            class="form-control"
                            value="<?= htmlspecialchars($data['tanggal']); ?>"
                            required
                        >

                    </div>


                    <!-- NO KK -->

                    <div class="col-md-6">

                        <label class="form-label">
                            No. KK
                        </label>

                        <input
                            type="text"
                            name="no_kk"
                            class="form-control"
                            value="<?= htmlspecialchars($data['no_kk']); ?>"
                            required
                        >

                    </div>


                    <!-- NO PESERTA KB -->

                    <div class="col-md-6">

                        <label class="form-label">
                            No. Peserta KB
                        </label>

                        <input
                            type="text"
                            name="no_peserta_kb"
                            class="form-control"
                            value="<?= htmlspecialchars($data['no_peserta_kb']); ?>"
                            required
                        >

                    </div>


                    <!-- TANGGAL LAHIR -->

                    <div class="col-md-6">

                        <label class="form-label">
                            Tanggal Lahir
                        </label>

                        <input
                            type="date"
                            name="tanggal_lahir"
                            class="form-control"
                            value="<?= htmlspecialchars($data['tanggal_lahir']); ?>"
                            required
                        >

                    </div>


                    <!-- NAMA ISTRI -->

                    <div class="col-md-6">

                        <label class="form-label">
                            Nama Istri
                        </label>

                        <input
                            type="text"
                            name="nama_istri"
                            class="form-control"
                            value="<?= htmlspecialchars($data['nama_istri']); ?>"
                            required
                        >

                    </div>


                    <!-- NAMA SUAMI -->

                    <div class="col-md-6">

                        <label class="form-label">
                            Nama Suami
                        </label>

                        <input
                            type="text"
                            name="nama_suami"
                            class="form-control"
                            value="<?= htmlspecialchars($data['nama_suami']); ?>"
                        >

                    </div>


                    <!-- JUMLAH ANAK -->

                    <div class="col-md-6">

                        <label class="form-label">
                            Jumlah Anak
                        </label>

                        <input
                            type="number"
                            name="jumlah_anak"
                            class="form-control"
                            min="0"
                            value="<?= htmlspecialchars($data['jumlah_anak']); ?>"
                        >

                    </div>


                    <!-- JENIS KB -->

                    <div class="col-md-6">

                        <label class="form-label">
                            Jenis KB
                        </label>

                        <select
                            name="jenis_kb"
                            class="form-select"
                            required
                        >

                            <option value="">
                                -- Pilih Jenis KB --
                            </option>

                            <option value="Pil"
                                <?= $data['jenis_kb'] == 'Pil' ? 'selected' : ''; ?>>
                                Pil
                            </option>

                            <option value="Suntik"
                                <?= $data['jenis_kb'] == 'Suntik' ? 'selected' : ''; ?>>
                                Suntik
                            </option>

                            <option value="IUD"
                                <?= $data['jenis_kb'] == 'IUD' ? 'selected' : ''; ?>>
                                IUD
                            </option>

                            <option value="Implan"
                                <?= $data['jenis_kb'] == 'Implan' ? 'selected' : ''; ?>>
                                Implan
                            </option>

                            <option value="MOW"
                                <?= $data['jenis_kb'] == 'MOW' ? 'selected' : ''; ?>>
                                MOW
                            </option>

                            <option value="MOP"
                                <?= $data['jenis_kb'] == 'MOP' ? 'selected' : ''; ?>>
                                MOP
                            </option>

                        </select>

                    </div>


                    <!-- KUNJUNGAN -->

                    <div class="col-md-6">

                        <label class="form-label">
                            Kunjungan
                        </label>

                        <select
                            name="kunjungan"
                            class="form-select"
                            required
                        >

                            <option value="">
                                -- Pilih Kunjungan --
                            </option>

                            <option value="Baru"
                                <?= $data['kunjungan'] == 'Baru' ? 'selected' : ''; ?>>
                                Baru
                            </option>

                            <option value="Ganti"
                                <?= $data['kunjungan'] == 'Ganti' ? 'selected' : ''; ?>>
                                Ganti
                            </option>

                            <option value="Lama"
                                <?= $data['kunjungan'] == 'Lama' ? 'selected' : ''; ?>>
                                Lama
                            </option>

                        </select>

                    </div>


                </div>


                <!-- BUTTON -->

                <div class="mt-4 d-flex gap-2">

                    <a
                        href="register-kb.php"
                        class="btn btn-outline-secondary"
                    >

                        <i class="bi bi-arrow-left me-1"></i>

                        Kembali

                    </a>


                    <button
                        type="submit"
                        name="simpan"
                        class="btn btn-dark"
                    >

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