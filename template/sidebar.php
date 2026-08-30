<?php
$uri_path     = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri_segments = explode('/', $uri_path);
$menu         = $uri_segments[2] ?? '';

$userLogin = $_SESSION['ssUserRM'];
$cekUser   = mysqli_query($koneksi, "SELECT * FROM tbl_user WHERE username = '$userLogin'");
$dataUser  = mysqli_fetch_assoc($cekUser);

function menuAktif($menu, $target)
{
    return $menu === $target ? 'mm-active' : '';
}
?>

<!--**********************************
    Sidebar start
***********************************-->
<div class="nk-sidebar">
    <div class="nk-nav-scroll">
        <ul class="metismenu" id="menu">

            <li class="nav-label">Menu Utama</li>

            <li class="<?= menuAktif($menu, 'index.php'); ?>">
                <a href="<?= $main_url ?>">
                    <i class="bi bi-speedometer2 menu-icon"></i>
                    <span class="nav-text">Dashboard</span>
                </a>
            </li>

<?php if (userHasAnyRole([ROLE_PETUGAS, ROLE_DOKTER, ROLE_BIDAN])) { ?>

            <li class="<?= menuAktif($menu, 'rujukan'); ?>">
                <a href="<?= $main_url ?>rujukan">
                    <i class="bi bi-arrow-left-right menu-icon"></i>
                    <span class="nav-text">Surat Rujukan</span>
                </a>
            </li>

<?php } ?>


<?php if (userHasRole(ROLE_PETUGAS)) { ?>

            <li class="<?= menuAktif($menu, 'pendaftaran'); ?>">
                <a href="<?= $main_url ?>pendaftaran">
                    <i class="bi bi-person-check menu-icon"></i>
                    <span class="nav-text">Daftarkan Pasien Umum</span>
                </a>
            </li>

            <!-- Semua halaman kebidanan Petugas ada di folder petugas/,
                 jadi $menu === 'petugas' cukup buat state aktif di sini. -->
            <li class="<?= menuAktif($menu, 'petugas'); ?>">
                <a class="has-arrow" href="javascript:void()" aria-expanded="false">
                    <i class="bi bi-heart-pulse menu-icon"></i>
                    <span class="nav-text">Daftarkan Pasien Bidan</span>
                </a>
                <ul aria-expanded="false">
                    <li>
                        <a href="<?= $main_url ?>petugas/pendaftaran-kebidanan.php">
                            Jadwalkan Kunjungan
                        </a>
                    </li>
                    <li>
                        <a href="<?= $main_url ?>petugas/rekam-medis-kebidanan.php">
                            Registrasi Ibu Hamil
                        </a>
                    </li>
                    <li>
                        <a href="<?= $main_url ?>petugas/register-kb.php">
                            Registrasi KB
                        </a>
                    </li>
                </ul>
            </li>

<?php } ?>


<?php if (userHasRole(ROLE_ADMIN)) { ?>

            <li class="nav-label">Manajemen Sistem</li>

            <li class="<?= menuAktif($menu, 'user'); ?>">
                <a href="<?= $main_url ?>user">
                    <i class="bi bi-person-gear menu-icon"></i>
                    <span class="nav-text">User</span>
                </a>
            </li>

            <li>
                <a href="<?= $main_url ?>user/hak-akses.php">
                    <i class="bi bi-shield-lock menu-icon"></i>
                    <span class="nav-text">Hak Akses</span>
                </a>
            </li>

<?php } ?>


<?php if (userHasRole(ROLE_PETUGAS)) { ?>

            <li class="nav-label">Pelayanan</li>

            <li class="<?= menuAktif($menu, 'pasien'); ?>">
                <a href="<?= $main_url ?>pasien">
                    <i class="bi bi-people menu-icon"></i>
                    <span class="nav-text">Data Pasien</span>
                </a>
            </li>

            <?php $poliAntrianSaatIni = $_GET['poli'] ?? ''; ?>

            <li class="<?= ($menu === 'antrian' && $poliAntrianSaatIni !== 'kebidanan') ? 'mm-active' : ''; ?>">
                <a href="<?= $main_url ?>antrian?poli=umum">
                    <i class="bi bi-clipboard2-pulse menu-icon"></i>
                    <span class="nav-text">Antrian Pasien Umum</span>
                </a>
            </li>

            <?php if (!userHasRole(ROLE_BIDAN)) { ?>


            <li class="<?= ($menu === 'antrian' && $poliAntrianSaatIni === 'kebidanan') ? 'mm-active' : ''; ?>">
                <a href="<?= $main_url ?>antrian?poli=kebidanan">
                    <i class="bi bi-heart-pulse menu-icon"></i>
                    <span class="nav-text">Antrian Kebidanan</span>
                </a>
            </li>

            <?php } ?>

            <li class="<?= menuAktif($menu, 'obat'); ?>">
                <a href="<?= $main_url ?>obat">
                    <i class="bi bi-capsule-pill menu-icon"></i>
                    <span class="nav-text">Obat</span>
                </a>
            </li>

            <li class="<?= strpos($_SERVER['REQUEST_URI'], 'pembayaran.php') !== false ? 'mm-active' : ''; ?>">
                <a href="<?= $main_url ?>petugas/pembayaran.php">
                    <i class="bi bi-cash-stack menu-icon"></i>
                    <span class="nav-text">Pembayaran</span>
                </a>
            </li>

            <li class="<?= menuAktif($menu, 'rekammedis'); ?>">
                <a href="<?= $main_url ?>rekammedis">
                    <i class="bi bi-file-earmark-medical menu-icon"></i>
                    <span class="nav-text">Rekam Medis</span>
                </a>
            </li>

<?php } ?>


<?php if (userHasRole(ROLE_DOKTER)) { ?>

            <li class="nav-label">Pelayanan</li>

            <li class="<?= menuAktif($menu, 'antrian'); ?>">
                <a href="<?= $main_url ?>antrian">
                    <i class="bi bi-list-ol menu-icon"></i>
                    <span class="nav-text">Antrian Pasien</span>
                </a>
            </li>

            <li class="<?= menuAktif($menu, 'rekammedis'); ?>">
                <a href="<?= $main_url ?>rekammedis">
                    <i class="bi bi-file-earmark-medical menu-icon"></i>
                    <span class="nav-text">Rekam Medis</span>
                </a>
            </li>

            <li class="nav-label">Laporan</li>

            <li class="<?= menuAktif($menu, 'riwayat-perekaman'); ?>">
                <a href="<?= $main_url ?>riwayat-perekaman">
                    <i class="bi bi-file-earmark-text menu-icon"></i>
                    <span class="nav-text">Laporan Rekam Medis</span>
                </a>
            </li>

<?php } ?>


<?php if (userHasRole(ROLE_BIDAN)) { ?>


            <li class="nav-label">Kebidanan</li>

            <li>
                <a href="<?= $main_url ?>bidan/jadwal-kebidanan.php">
                    <i class="bi bi-heart-pulse menu-icon"></i>
                    <span class="nav-text">Jadwal Hari Ini</span>
                </a>
            </li>

            <li>
                <a href="<?= $main_url ?>bidan/rekam-medis-kebidanan.php">
                    <i class="bi bi-file-earmark-medical menu-icon"></i>
                    <span class="nav-text">Data Ibu Hamil</span>
                </a>
            </li>

            <li>
                <a href="<?= $main_url ?>bidan/register-kb.php">
                    <i class="bi bi-person-vcard menu-icon"></i>
                    <span class="nav-text">Data Peserta KB</span>
                </a>
            </li>

            <li class="nav-label">Laporan</li>

            <li>
                <a href="<?= $main_url ?>laporan/laporan-kehamilan.php">
                    <i class="bi bi-file-earmark-text menu-icon"></i>
                    <span class="nav-text">Laporan Kehamilan</span>
                </a>
            </li>

            <li>
                <a href="<?= $main_url ?>laporan/laporan-kb.php">
                    <i class="bi bi-file-earmark-text menu-icon"></i>
                    <span class="nav-text">Laporan KB</span>
                </a>
            </li>

            <li>

<?php } ?>


<?php if (userHasRole(ROLE_KEPALA)) { ?>

            <li class="nav-label">Laporan</li>

            <li>
                <a href="<?= $main_url ?>laporan/bpjs.php">
                    <i class="bi bi-hospital menu-icon"></i>
                    <span class="nav-text">Laporan BPJS</span>
                </a>
            </li>

            <li>
                <a href="<?= $main_url ?>laporan/asuransi.php">
                    <i class="bi bi-shield-check menu-icon"></i>
                    <span class="nav-text">Laporan Asuransi</span>
                </a>
            </li>

            <li>
                <a href="<?= $main_url ?>laporan/umum.php">
                    <i class="bi bi-people menu-icon"></i>
                    <span class="nav-text">Laporan Umum</span>
                </a>
            </li>

            <li>
                <a href="<?= $main_url ?>laporan/seluruh.php">
                    <i class="bi bi-bar-chart-line menu-icon"></i>
                    <span class="nav-text">Laporan Seluruh</span>
                </a>
            </li>

                    <li>
            <a href="<?= $main_url ?>laporan/kb.php">
                <i class="bi bi-person-vcard"></i>
                Laporan KB
            </a>
        </li>

        <li>
            <a href="<?= $main_url ?>laporan/ibu-hamil.php">
                <i class="bi bi-person-heart"></i>
                Laporan Ibu Hamil
            </a>
        </li>

<?php } ?>

        </ul>
    </div>
</div>
<!--**********************************
    Sidebar end
***********************************-->

<!--**********************************
    Content body start
***********************************-->
<div class="content-body">

    <div class="container-fluid">
