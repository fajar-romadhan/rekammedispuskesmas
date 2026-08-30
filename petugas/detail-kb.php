<?php

session_start();

require "../template/rbac.php";

// Hanya Petugas
cekAkses([ROLE_PETUGAS]);

require "../config.php";


/* =========================================================
   CEK ID
========================================================= */

if (!isset($_GET['id']) || empty($_GET['id'])) {

    echo "
    <script>
        alert('ID peserta KB tidak ditemukan!');
        window.location='register-kb.php';
    </script>
    ";

    exit();
}


$id = mysqli_real_escape_string(
    $koneksi,
    $_GET['id']
);


/* =========================================================
   AMBIL DATA
========================================================= */

$query = mysqli_query(
    $koneksi,
    "SELECT *
     FROM tbl_kb
     WHERE id_kb = '$id'
     LIMIT 1"
);


if (mysqli_num_rows($query) == 0) {

    echo "
    <script>
        alert('Data peserta KB tidak ditemukan!');
        window.location='register-kb.php';
    </script>
    ";

    exit();
}


$data = mysqli_fetch_assoc($query);


$title = "Detail Peserta KB - Rekam Medis Puskesmas";

require "../template/header.php";
require "../template/navbar.php";
require "../template/sidebar.php";

?>


<div class="page-content-wrap">


    <!-- HEADER -->

    <div class="d-flex justify-content-between
                align-items-center
                pt-3 pb-3 mb-4
                border-bottom">

        <div>

            <h1 class="h2 mb-1">
                Detail Peserta KB
            </h1>

            <p class="text-muted mb-0">
                Detail data register keluarga berencana
            </p>

        </div>


        <a href="register-kb.php"
           class="btn btn-secondary">

            <i class="bi bi-arrow-left me-1"></i>
            Kembali

        </a>

    </div>


    <!-- DATA PESERTA -->

    <div class="card border-0 shadow-sm mb-4">

        <div class="card-body">

            <h5 class="fw-bold border-bottom pb-2 mb-4">
                Data Peserta KB
            </h5>


            <div class="row g-3">


                <!-- TANGGAL -->

                <div class="col-md-6">

                    <label class="form-label text-muted">
                        Tanggal
                    </label>

                    <div class="form-control bg-light">

                        <?= date(
                            'd-m-Y',
                            strtotime($data['tanggal'])
                        ); ?>

                    </div>

                </div>


                <!-- NO KK -->

                <div class="col-md-6">

                    <label class="form-label text-muted">
                        No. KK
                    </label>

                    <div class="form-control bg-light">

                        <?= htmlspecialchars(
                            $data['no_kk']
                        ); ?>

                    </div>

                </div>


                <!-- NO PESERTA KB -->

                <div class="col-md-6">

                    <label class="form-label text-muted">
                        No. Peserta KB
                    </label>

                    <div class="form-control bg-light">

                        <?= htmlspecialchars(
                            $data['no_peserta_kb']
                        ); ?>

                    </div>

                </div>


                <!-- TANGGAL LAHIR -->

                <div class="col-md-6">

                    <label class="form-label text-muted">
                        Tanggal Lahir
                    </label>

                    <div class="form-control bg-light">

                        <?= date(
                            'd-m-Y',
                            strtotime(
                                $data['tanggal_lahir']
                            )
                        ); ?>

                    </div>

                </div>


                <!-- NAMA SUAMI -->

                <div class="col-md-6">

                    <label class="form-label text-muted">
                        Nama Suami
                    </label>

                    <div class="form-control bg-light">

                        <?= htmlspecialchars(
                            $data['nama_suami']
                        ); ?>

                    </div>

                </div>


                <!-- JUMLAH ANAK -->

                <div class="col-md-6">

                    <label class="form-label text-muted">
                        Jumlah Anak
                    </label>

                    <div class="form-control bg-light">

                        <?= htmlspecialchars(
                            $data['jumlah_anak']
                        ); ?>

                        Anak

                    </div>

                </div>


                <!-- ALAMAT -->

                <div class="col-12">

                    <label class="form-label text-muted">
                        Alamat
                    </label>

                    <div class="form-control bg-light"
                         style="min-height:80px;">

                        <?= nl2br(
                            htmlspecialchars(
                                $data['alamat']
                            )
                        ); ?>

                    </div>

                </div>

            </div>

        </div>

    </div>


    <!-- PELAYANAN KB -->

    <div class="card border-0 shadow-sm mb-4">

        <div class="card-body">

            <h5 class="fw-bold border-bottom pb-2 mb-4">
                Data Pelayanan KB
            </h5>


            <div class="row g-3">


                <!-- JENIS KB -->

                <div class="col-md-6">

                    <label class="form-label text-muted">
                        Jenis KB
                    </label>

                    <div class="form-control bg-light">

                        <?= htmlspecialchars(
                            $data['jenis_kb']
                        ); ?>

                    </div>

                </div>


                <!-- KUNJUNGAN -->

                <div class="col-md-6">

                    <label class="form-label text-muted">
                        Kunjungan
                    </label>

                    <div class="form-control bg-light">

                        <?= htmlspecialchars(
                            $data['kunjungan']
                        ); ?>

                    </div>

                </div>

            </div>

        </div>

    </div>


    <!-- HASIL PEMERIKSAAN -->

    <div class="card border-0 shadow-sm mb-4">

        <div class="card-body">

            <h5 class="fw-bold border-bottom pb-2 mb-4">
                Hasil Pemeriksaan
            </h5>


            <div class="row g-3">


                <!-- TENSI -->

                <div class="col-md-6">

                    <label class="form-label text-muted">
                        Tensi Darah
                    </label>

                    <div class="form-control bg-light">

                        <?= !empty(
                            $data['tensi_darah']
                        )
                            ? htmlspecialchars(
                                $data['tensi_darah']
                            ) . ' mmHg'
                            : '-';
                        ?>

                    </div>

                </div>


                <!-- BB -->

                <div class="col-md-6">

                    <label class="form-label text-muted">
                        Berat Badan
                    </label>

                    <div class="form-control bg-light">

                        <?= $data['bb'] !== null &&
                            $data['bb'] !== ''
                            ? htmlspecialchars(
                                $data['bb']
                            ) . ' Kg'
                            : '-';
                        ?>

                    </div>

                </div>


                <!-- KETERANGAN -->

                <div class="col-12">

                    <label class="form-label text-muted">
                        Keterangan
                    </label>

                    <div class="form-control bg-light"
                         style="min-height:80px;">

                        <?= !empty(
                            $data['keterangan']
                        )
                            ? nl2br(
                                htmlspecialchars(
                                    $data['keterangan']
                                )
                            )
                            : '-';
                        ?>

                    </div>

                </div>

            </div>

        </div>

    </div>


    <!-- BUTTON -->

    <div class="d-flex justify-content-end gap-2 mb-4">

        <a href="register-kb.php"
           class="btn btn-secondary">

            <i class="bi bi-arrow-left me-1"></i>
            Kembali

        </a>


        <a href="kb-pdf.php?id=<?= $data['id_kb']; ?>"
           target="_blank"
           class="btn btn-dark">

            <i class="bi bi-file-earmark-pdf me-1"></i>
            Cetak PDF

        </a>

    </div>


</div>


<?php

require "../template/footer.php";

?>