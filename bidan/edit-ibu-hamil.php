<?php
session_start();

require "../template/rbac.php";

// Hanya Bidan
cekAkses([ROLE_BIDAN]);

require "../config.php";

$title = "Edit Data Ibu Hamil - Rekam Medis Puskesmas";

/* ==============================
   CEK ID
   ============================== */

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("location: rekam-medis-kebidanan.php");
    exit();
}

$id = (int) $_GET['id'];

/* ==============================
   AMBIL DATA IBU HAMIL
   ============================== */

$query = mysqli_query(
    $koneksi,
    "SELECT * FROM tbl_ibu_hamil WHERE id = '$id'"
);

if (!$query || mysqli_num_rows($query) == 0) {
    die("Data ibu hamil tidak ditemukan.");
}

$data = mysqli_fetch_assoc($query);


/* ==============================
   UPDATE DATA
   ============================== */

if (isset($_POST['update'])) {

    $nama_ibu   = mysqli_real_escape_string($koneksi, formatNama($_POST['nama_ibu']));
    $nik        = mysqli_real_escape_string($koneksi, $_POST['nik']);
    $nama_suami = mysqli_real_escape_string($koneksi, formatNama($_POST['nama_suami']));
    $no_hp      = mysqli_real_escape_string($koneksi, $_POST['no_hp']);

    $hpht = !empty($_POST['hpht'])
        ? "'" . mysqli_real_escape_string($koneksi, $_POST['hpht']) . "'"
        : "NULL";

    $hpl = !empty($_POST['hpl'])
        ? "'" . mysqli_real_escape_string($koneksi, $_POST['hpl']) . "'"
        : "NULL";

    $gravida    = mysqli_real_escape_string($koneksi, $_POST['gravida']);
    $para       = mysqli_real_escape_string($koneksi, $_POST['para']);
    $abortus    = mysqli_real_escape_string($koneksi, $_POST['abortus']);

    $update = mysqli_query(
        $koneksi,
        "UPDATE tbl_ibu_hamil SET

            nama_ibu   = '$nama_ibu',
            nik        = '$nik',
            nama_suami = '$nama_suami',
            no_hp      = '$no_hp',
            hpht       = $hpht,
            hpl        = $hpl,
            gravida    = '$gravida',
            para       = '$para',
            abortus    = '$abortus'

        WHERE id = '$id'"
    );

    if ($update) {

        header(
            "location: rekam-medis-kebidanan.php?status=update"
        );

        exit();

    } else {

        $error = "Data ibu hamil gagal diperbarui.";

    }
}


require "../template/header.php";
require "../template/navbar.php";
require "../template/sidebar.php";
?>


<div class="page-content-wrap">

    <!-- HEADER -->

    <div class="d-flex justify-content-between align-items-center
                pt-3 pb-3 mb-4 border-bottom">

        <div>

            <h1 class="h2">
                Edit Data Ibu Hamil
            </h1>

            <p class="text-muted mb-0">
                Perbarui data ibu hamil
            </p>

        </div>

        <a href="rekam-medis-kebidanan.php"
           class="btn btn-secondary">

            <i class="bi bi-arrow-left"></i>
            Kembali

        </a>

    </div>


    <?php if (isset($error)) : ?>

        <div class="alert alert-danger">

            <?= $error; ?>

        </div>

    <?php endif; ?>


    <!-- FORM -->

    <div class="card border-0 shadow-sm">

        <div class="card-body">

            <form method="post">

                <div class="row g-3">


                    <!-- NAMA IBU -->

                    <div class="col-md-6">

                        <label class="form-label">
                            Nama Ibu
                        </label>

                        <input type="text"
                               name="nama_ibu"
                               class="form-control"
                               value="<?= htmlspecialchars($data['nama_ibu']); ?>"
                               required>

                    </div>


                    <!-- NIK -->

                    <div class="col-md-6">

                        <label class="form-label">
                            NIK
                        </label>

                        <input type="text"
                               name="nik"
                               class="form-control"
                               value="<?= htmlspecialchars($data['nik']); ?>"
                               required>

                    </div>


                    <!-- NAMA SUAMI -->

                    <div class="col-md-6">

                        <label class="form-label">
                            Nama Suami
                        </label>

                        <input type="text"
                               name="nama_suami"
                               class="form-control"
                               value="<?= htmlspecialchars($data['nama_suami']); ?>">

                    </div>


                    <!-- NO HP -->

                    <div class="col-md-6">

                        <label class="form-label">
                            No. HP
                        </label>

                        <input type="text"
                               name="no_hp"
                               class="form-control"
                               value="<?= htmlspecialchars($data['no_hp']); ?>">

                    </div>


                    <!-- HPHT -->

                    <div class="col-md-4">

                        <label class="form-label">
                            HPHT
                        </label>

                        <input type="date"
                               name="hpht"
                               class="form-control"
                               value="<?= htmlspecialchars($data['hpht']); ?>">

                    </div>


                    <!-- HPL -->

                    <div class="col-md-4">

                        <label class="form-label">
                            HPL
                        </label>

                        <input type="date"
                               name="hpl"
                               class="form-control"
                               value="<?= htmlspecialchars($data['hpl']); ?>">

                    </div>


                    <!-- GRAVIDA -->

                    <div class="col-md-4">

                        <label class="form-label">
                            Gravida
                        </label>

                        <input type="number"
                               name="gravida"
                               class="form-control"
                               value="<?= htmlspecialchars($data['gravida']); ?>">

                    </div>


                    <!-- PARA -->

                    <div class="col-md-4">

                        <label class="form-label">
                            Para
                        </label>

                        <input type="number"
                               name="para"
                               class="form-control"
                               value="<?= htmlspecialchars($data['para']); ?>">

                    </div>


                    <!-- ABORTUS -->

                    <div class="col-md-4">

                        <label class="form-label">
                            Abortus
                        </label>

                        <input type="number"
                               name="abortus"
                               class="form-control"
                               value="<?= htmlspecialchars($data['abortus']); ?>">

                    </div>


                    <!-- BUTTON -->

                    <div class="col-12 text-end mt-4">

                        <a href="rekam-medis-kebidanan.php"
                           class="btn btn-secondary">

                            Batal

                        </a>

                        <button type="submit"
                                name="update"
                                class="btn btn-dark">

                            <i class="bi bi-save"></i>
                            Simpan Perubahan

                        </button>

                    </div>

                </div>

            </form>

        </div>

    </div>

</div>


<?php
require "../template/footer.php";
?>
