<?php 

session_start();

require "template/rbac.php";

cekAkses([ROLE_ADMIN, ROLE_PETUGAS, ROLE_DOKTER, ROLE_BIDAN, ROLE_KEPALA], 'otentikasi/index.php', 'index.php');

require "config.php";

$title = "dashboard - rekammedispuskesmas";


require "template/header.php";
require "template/navbar.php";
require "template/sidebar.php";

?>


        <div class="page-content-wrap">

<?php
// User bisa punya lebih dari satu role sekarang. Dashboard yang
// ditampilkan mengikuti urutan prioritas role (Admin paling tinggi).
?>

<?php if(userHasRole(1)){ ?>

<h1 class="h2 mb-4">Dashboard Administrator</h1>

<?php
$jmlUser = mysqli_fetch_assoc(mysqli_query($koneksi,"SELECT COUNT(*) total FROM tbl_user"));
$jmlPasien = mysqli_fetch_assoc(mysqli_query($koneksi,"SELECT COUNT(*) total FROM tbl_pasien"));
$jmlObat = mysqli_fetch_assoc(mysqli_query($koneksi,"SELECT COUNT(*) total FROM tbl_obat"));
$jmlRM = mysqli_fetch_assoc(mysqli_query($koneksi,"SELECT COUNT(*) total FROM tbl_rekammedis"));

// Data grafik: jumlah pendaftaran pasien per bulan (tahun berjalan),
// dipakai oleh chart di template/footer.php.
$bln_ini = (int) date('n');
$tahunIni = date('Y');
$list_data = [];

for ($b = 1; $b <= $bln_ini; $b++) {
    $jmlBulan = mysqli_fetch_assoc(mysqli_query($koneksi, "
        SELECT COUNT(*) total
        FROM tbl_antrian
        WHERE MONTH(tanggal_daftar) = $b
          AND YEAR(tanggal_daftar) = $tahunIni
    "));
    $list_data[] = (int) $jmlBulan['total'];
}
?>

<div class="row">

<div class="col-md-3 mb-3">
<div class="card bg-primary text-white">
<div class="card-body text-center">
<h6>Total User</h6>
<h2><?= $jmlUser['total']; ?></h2>
</div>
</div>
</div>

<div class="col-md-3 mb-3">
<div class="card bg-success text-white">
<div class="card-body text-center">
<h6>Total Pasien</h6>
<h2><?= $jmlPasien['total']; ?></h2>
</div>
</div>
</div>

<div class="col-md-3 mb-3">
<div class="card bg-warning text-dark">
<div class="card-body text-center">
<h6>Total Obat</h6>
<h2><?= $jmlObat['total']; ?></h2>
</div>
</div>
</div>

<div class="col-md-3 mb-3">
<div class="card bg-danger text-white">
<div class="card-body text-center">
<h6>Total Rekam Medis</h6>
<h2><?= $jmlRM['total']; ?></h2>
</div>
</div>
</div>

</div>

<canvas id="myChart" class="my-4 w-100"></canvas>

<?php } elseif(userHasRole(2)){ ?>

<h1 class="h2 mb-4">Dashboard Petugas</h1>

<?php

$totalPasien = mysqli_fetch_assoc(mysqli_query($koneksi,
"SELECT COUNT(*) total FROM tbl_pasien"));

$hariIni = mysqli_fetch_assoc(mysqli_query($koneksi,
"SELECT COUNT(*) total FROM tbl_pasien
WHERE DATE(id)=CURDATE()"));

?>

<div class="row">

<div class="col-md-6">
    <div class="card bg-success text-white">
        <div class="card-body text-center">
            <h5>Total Pasien</h5>
            <h2><?= $totalPasien['total']; ?></h2>
        </div>
    </div>
</div>

<div class="col-md-6">
    <div class="card bg-info text-white">
        <div class="card-body text-center">
            <h5>Pasien Hari Ini</h5>
            <h2><?= $hariIni['total']; ?></h2>
        </div>
    </div>
</div>

</div>

<?php } elseif(userHasRole(3)){ ?>

<h1 class="h2 mb-4">Dashboard Dokter</h1>

<?php
$d = mysqli_fetch_assoc(mysqli_query($koneksi,"SELECT COUNT(*) total FROM tbl_rekammedis WHERE tgl_rm=CURDATE()"));
?>

<div class="card">
<div class="card-body">
<h5>Pemeriksaan Hari Ini</h5>
<h2><?= $d['total']; ?></h2>
</div>
</div>


<?php } elseif(userHasRole(4)){ ?>
<h1 class="h2 mb-4">Dashboard Bidan</h1>

<?php

// Domain Bidan itu Kebidanan (Ibu Hamil/KB), bukan Poli Umum -- jadi
// statistiknya dari tbl_ibu_hamil/tbl_kb/tbl_pendaftaran_kebidanan,
// bukan tbl_pasien/tbl_rekammedis (itu Poli Umum, sama seperti
// rujukan/ dan antrian/ yang sudah di-scope serupa).

$totalIbuHamil = mysqli_fetch_assoc(mysqli_query($koneksi,
"SELECT COUNT(*) total FROM tbl_ibu_hamil"));

$totalKb = mysqli_fetch_assoc(mysqli_query($koneksi,
"SELECT COUNT(*) total FROM tbl_kb"));

$jadwalHariIni = mysqli_fetch_assoc(mysqli_query($koneksi,
"SELECT COUNT(*) total FROM tbl_pendaftaran_kebidanan
WHERE tanggal = CURDATE() AND status IN ('Menunggu', 'Dipanggil')"));

?>

<div class="row">

<div class="col-md-4">
    <div class="card bg-primary text-white">
        <div class="card-body text-center">
            <h5>Ibu Hamil Terdaftar</h5>
            <h2><?= $totalIbuHamil['total']; ?></h2>
        </div>
    </div>
</div>

<div class="col-md-4">
    <div class="card bg-warning text-dark">
        <div class="card-body text-center">
            <h5>Peserta KB Terdaftar</h5>
            <h2><?= $totalKb['total']; ?></h2>
        </div>
    </div>
</div>

<div class="col-md-4">
    <div class="card bg-info text-white">
        <div class="card-body text-center">
            <h5>Jadwal Hari Ini</h5>
            <h2><?= $jadwalHariIni['total']; ?></h2>
        </div>
    </div>
</div>

</div>

<?php } elseif(userHasRole(5)){ ?>

<h1 class="h2 mb-4">Dashboard Kepala Puskesmas</h1>

<?php

$user = mysqli_fetch_assoc(mysqli_query($koneksi,
"SELECT COUNT(*) total FROM tbl_user"));

$pasien = mysqli_fetch_assoc(mysqli_query($koneksi,
"SELECT COUNT(*) total FROM tbl_pasien"));

$obat = mysqli_fetch_assoc(mysqli_query($koneksi,
"SELECT COUNT(*) total FROM tbl_obat"));

$rm = mysqli_fetch_assoc(mysqli_query($koneksi,
"SELECT COUNT(*) total FROM tbl_rekammedis"));

?>

<div class="row">

<div class="col-md-3">
<div class="card bg-primary text-white">
<div class="card-body text-center">
<h6>User</h6>
<h2><?= $user['total']; ?></h2>
</div>
</div>
</div>

<div class="col-md-3">
<div class="card bg-success text-white">
<div class="card-body text-center">
<h6>Pasien</h6>
<h2><?= $pasien['total']; ?></h2>
</div>
</div>
</div>

<div class="col-md-3">
<div class="card bg-warning text-dark">
<div class="card-body text-center">
<h6>Obat</h6>
<h2><?= $obat['total']; ?></h2>
</div>
</div>
</div>

<div class="col-md-3">
<div class="card bg-danger text-white">
<div class="card-body text-center">
<h6>Rekam Medis</h6>
<h2><?= $rm['total']; ?></h2>
</div>
</div>
</div>

</div>

<?php } ?>

</div>


<?php

require "template/footer.php";

?>