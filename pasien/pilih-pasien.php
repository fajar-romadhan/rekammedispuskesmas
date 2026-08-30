<?php

session_start();

require "../template/rbac.php";

cekAkses([ROLE_ADMIN, ROLE_PETUGAS, ROLE_BIDAN]);

require "../config.php";


if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "<script>
        alert('ID pasien tidak ditemukan.');
        window.location='index.php';
    </script>";
    exit();
}


$id = mysqli_real_escape_string(
    $koneksi,
    $_GET['id']
);


$query = mysqli_query($koneksi, "
    SELECT *
    FROM tbl_pasien
    WHERE id = '$id'
");


$pasien = mysqli_fetch_assoc($query);


if (!$pasien) {

    echo "<script>
        alert('Data pasien tidak ditemukan.');
        window.location='index.php';
    </script>";

    exit();
}


$title = "Pilih Pasien - rekammedispuskesmas";


require "../template/header.php";
require "../template/navbar.php";
require "../template/sidebar.php";

?>


<div class="page-content-wrap">


    <div class="d-flex justify-content-between flex-wrap
                flex-md-nowrap align-items-center
                pt-3 pb-2 mb-3 border-bottom">

        <h1 class="h2">
            Pilih Pasien
        </h1>

    </div>


    <div class="card">

        <div class="card-header">

            <i class="bi bi-person-check"></i>
            Data Pasien

        </div>


        <div class="card-body">


            <div class="row mb-3">

                <div class="col-md-6">

                    <label class="form-label">
                        ID Pasien
                    </label>

                    <input type="text"
                           class="form-control"
                           value="<?= htmlspecialchars($pasien['id']); ?>"
                           readonly>

                </div>


                <div class="col-md-6">

                    <label class="form-label">
                        Nama Pasien
                    </label>

                    <input type="text"
                           class="form-control"
                           value="<?= htmlspecialchars($pasien['nama']); ?>"
                           readonly>

                </div>

            </div>


            <div class="row mb-3">

                <div class="col-md-6">

                    <label class="form-label">
                        Tanggal Lahir
                    </label>

                    <input type="text"
                           class="form-control"
                           value="<?= in_date($pasien['tgl_lahir']); ?>"
                           readonly>

                </div>


                <div class="col-md-6">

                    <label class="form-label">
                        Jenis Kelamin
                    </label>

                    <input type="text"
                           class="form-control"
                           value="<?= ($pasien['gender'] == 'P') ? 'Pria' : 'Wanita'; ?>"
                           readonly>

                </div>

            </div>


            <div class="mt-4">

                <a href="index.php"
                   class="btn btn-secondary">

                    <i class="bi bi-arrow-left"></i>
                    Kembali

                </a>


                <?php if (userHasAnyRole([1,2])) { ?>

                    <a href="../antrian/tambah.php?id_pasien=<?= $pasien['id']; ?>"
                       class="btn btn-primary">

                        <i class="bi bi-ticket-perforated"></i>
                        Ambil Nomor Antrian

                    </a>

                <?php } ?>


                <?php if (userHasRole(4)) { ?>

                    <a href="../riwayat-kebidanan/tambah-data.php?id_pasien=<?= $pasien['id']; ?>"
                       class="btn btn-primary">

                        <i class="bi bi-file-medical"></i>
                        Rekam Medis Kebidanan

                    </a>

                <?php } ?>

            </div>


        </div>

    </div>


</div>


<?php

require "../template/footer.php";

?>