<?php
session_start();

require "../template/rbac.php";

// Hanya Bidan
cekAkses([ROLE_BIDAN]);

require "../config.php";

$title = "Dashboard Bidan - Rekam Medis Puskesmas";

require "../template/header.php";
require "../template/navbar.php";
require "../template/sidebar.php";
?>

<div class="page-content-wrap">

    <!-- HEADER -->
    <div class="pt-3 pb-3 mb-4 border-bottom">

        <h1 class="h2">Dashboard Bidan</h1>

        <p class="text-muted mb-0">
            Selamat datang di Sistem Informasi Rekam Medis Puskesmas
        </p>

    </div>


    <!-- INFORMASI -->
    <div class="row g-3 mb-4">

        <!-- IBU HAMIL -->
        <div class="col-md-6 col-xl-3">

            <div class="card border-0 shadow-sm h-100">

                <div class="card-body">

                    <div class="d-flex justify-content-between
                                align-items-center">

                        <div>

                            <p class="text-muted mb-1">
                                Ibu Hamil
                            </p>

                            <h3 class="mb-0">
                                XX
                            </h3>

                        </div>

                        <div class="fs-1">
                            <i class="bi bi-person-heart"></i>
                        </div>

                    </div>

                    <a href="jadwal-kebidanan.php"
                       class="small text-dark">

                        Lihat Jadwal Hari Ini →

                    </a>

                </div>

            </div>

        </div>


        <!-- PEMERIKSAAN -->
        <div class="col-md-6 col-xl-3">

            <div class="card border-0 shadow-sm h-100">

                <div class="card-body">

                    <div class="d-flex justify-content-between
                                align-items-center">

                        <div>

                            <p class="text-muted mb-1">
                                Pemeriksaan
                            </p>

                            <h3 class="mb-0">
                                XX
                            </h3>

                        </div>

                        <div class="fs-1">
                            <i class="bi bi-clipboard2-pulse"></i>
                        </div>

                    </div>

                    <a href="jadwal-kebidanan.php"
                       class="small text-dark">

                        Lihat Jadwal Hari Ini →

                    </a>

                </div>

            </div>

        </div>


        <!-- PESERTA KB -->
        <div class="col-md-6 col-xl-3">

            <div class="card border-0 shadow-sm h-100">

                <div class="card-body">

                    <div class="d-flex justify-content-between
                                align-items-center">

                        <div>

                            <p class="text-muted mb-1">
                                Peserta KB
                            </p>

                            <h3 class="mb-0">
                                XX
                            </h3>

                        </div>

                        <div class="fs-1">
                            <i class="bi bi-people"></i>
                        </div>

                    </div>

                    <a href="jadwal-kebidanan.php"
                       class="small text-dark">

                        Lihat Jadwal Hari Ini →

                    </a>

                </div>

            </div>

        </div>


        <!-- PELAYANAN KB -->
        <div class="col-md-6 col-xl-3">

            <div class="card border-0 shadow-sm h-100">

                <div class="card-body">

                    <div class="d-flex justify-content-between
                                align-items-center">

                        <div>

                            <p class="text-muted mb-1">
                                Pelayanan KB
                            </p>

                            <h3 class="mb-0">
                                XX
                            </h3>

                        </div>

                        <div class="fs-1">
                            <i class="bi bi-heart-pulse"></i>
                        </div>

                    </div>

                    <a href="pelayanan-kb.php"
                       class="small text-dark">

                        Lihat Pelayanan →

                    </a>

                </div>

            </div>

        </div>

    </div>


    <!-- MENU CEPAT -->

    <div class="card border-0 shadow-sm">

        <div class="card-body">

            <h5 class="mb-4">
                Menu Kebidanan
            </h5>

            <div class="row g-3">


                <!-- JADWAL HARI INI -->

                <div class="col-md-6">

                    <a href="jadwal-kebidanan.php"
                       class="text-decoration-none text-dark">

                        <div class="border rounded p-3
                                    h-100">

                            <div class="d-flex align-items-center">

                                <div class="fs-2 me-3">

                                    <i class="bi bi-calendar2-check"></i>

                                </div>

                                <div>

                                    <h6 class="mb-1">
                                        Jadwal Hari Ini
                                    </h6>

                                    <small class="text-muted">
                                        Ibu hamil &amp; peserta KB yang
                                        didaftarkan petugas untuk hari ini
                                    </small>

                                </div>

                            </div>

                        </div>

                    </a>

                </div>


                <!-- PEMERIKSAAN IBU HAMIL -->

                <div class="col-md-6">

                    <a href="jadwal-kebidanan.php"
                       class="text-decoration-none text-dark">

                        <div class="border rounded p-3
                                    h-100">

                            <div class="d-flex align-items-center">

                                <div class="fs-2 me-3">

                                    <i class="bi bi-clipboard2-pulse"></i>

                                </div>

                                <div>

                                    <h6 class="mb-1">
                                        Pemeriksaan Ibu Hamil
                                    </h6>

                                    <small class="text-muted">
                                        Mencatat hasil pemeriksaan kehamilan
                                    </small>

                                </div>

                            </div>

                        </div>

                    </a>

                </div>


                <!-- PELAYANAN KB -->

                <div class="col-md-6">

                    <a href="pelayanan-kb.php"
                       class="text-decoration-none text-dark">

                        <div class="border rounded p-3
                                    h-100">

                            <div class="d-flex align-items-center">

                                <div class="fs-2 me-3">

                                    <i class="bi bi-heart-pulse"></i>

                                </div>

                                <div>

                                    <h6 class="mb-1">
                                        Pelayanan KB
                                    </h6>

                                    <small class="text-muted">
                                        Mencatat pelayanan peserta KB
                                    </small>

                                </div>

                            </div>

                        </div>

                    </a>

                </div>


                <!-- LAPORAN KEHAMILAN -->

                <div class="col-md-6">

                    <a href="../laporan/laporan-kehamilan.php"
                       class="text-decoration-none text-dark">

                        <div class="border rounded p-3
                                    h-100">

                            <div class="d-flex align-items-center">

                                <div class="fs-2 me-3">

                                    <i class="bi bi-file-earmark-text"></i>

                                </div>

                                <div>

                                    <h6 class="mb-1">
                                        Laporan Kehamilan
                                    </h6>

                                    <small class="text-muted">
                                        Melihat dan mencetak laporan kehamilan
                                    </small>

                                </div>

                            </div>

                        </div>

                    </a>

                </div>


                <!-- LAPORAN KB -->

                <div class="col-md-6">

                    <a href="../laporan/laporan-kb.php"
                       class="text-decoration-none text-dark">

                        <div class="border rounded p-3
                                    h-100">

                            <div class="d-flex align-items-center">

                                <div class="fs-2 me-3">

                                    <i class="bi bi-file-earmark-text"></i>

                                </div>

                                <div>

                                    <h6 class="mb-1">
                                        Laporan KB
                                    </h6>

                                    <small class="text-muted">
                                        Melihat dan mencetak laporan KB
                                    </small>

                                </div>

                            </div>

                        </div>

                    </a>

                </div>

            </div>

        </div>

    </div>

</div>


<?php
require "../template/footer.php";
?>