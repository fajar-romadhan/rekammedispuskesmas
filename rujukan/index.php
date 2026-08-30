<?php

session_start();

require "../template/rbac.php";

// Surat Rujukan bisa diakses Petugas, Dokter, dan Bidan
cekAkses([ROLE_PETUGAS, ROLE_DOKTER, ROLE_BIDAN]);

require "../config.php";

$title = "Surat Rujukan - rekammedispuskesmas";

require "../template/header.php";
require "../template/navbar.php";
require "../template/sidebar.php";


/* =========================================================
   MODE HALAMAN

   - Tanpa tipe/id : cari orang dulu (pasien umum, ibu hamil,
     atau peserta KB - ketiganya tersimpan di tabel terpisah).
   - Dengan tipe+id: pilih riwayat pemeriksaan/rekam medis yang
     mau dijadikan dasar surat rujukan.
========================================================= */

/*
|--------------------------------------------------------------------------
| TIPE ORANG YANG BOLEH DILIHAT/DIRUJUK, SESUAI ROLE
|--------------------------------------------------------------------------
| Sama seperti antrian/ dan jadwal kebidanan: Dokter cuma boleh urusan
| Poli Umum (tbl_pasien), Bidan cuma Kebidanan (Ibu Hamil/KB). Petugas,
| atau akun dengan kombinasi role (termasuk Dokter+Bidan sekaligus,
| tidak ada cara membedakan tanpa fitur "Ganti Role"), tetap lihat semua.
|--------------------------------------------------------------------------
*/

$tipeDiizinkan = ['pasien', 'ibu_hamil', 'kb'];

if (userHasRole(ROLE_DOKTER) && !userHasAnyRole([ROLE_PETUGAS, ROLE_BIDAN])) {
    $tipeDiizinkan = ['pasien'];
} elseif (userHasRole(ROLE_BIDAN) && !userHasAnyRole([ROLE_PETUGAS, ROLE_DOKTER])) {
    $tipeDiizinkan = ['ibu_hamil', 'kb'];
}

$tipeDipilih = $_GET['tipe'] ?? '';
$idDipilih   = $_GET['id'] ?? '';

if (!in_array($tipeDipilih, $tipeDiizinkan, true) || $idDipilih === '') {
    $tipeDipilih = '';
}

$orangDipilih = null;

if ($tipeDipilih !== '') {

    $idAman = mysqli_real_escape_string($koneksi, $idDipilih);

    if ($tipeDipilih === 'pasien') {

        $q = mysqli_query($koneksi, "SELECT * FROM tbl_pasien WHERE id = '$idAman' LIMIT 1");

    } elseif ($tipeDipilih === 'ibu_hamil') {

        $q = mysqli_query($koneksi, "SELECT * FROM tbl_ibu_hamil WHERE id = '$idAman' LIMIT 1");

    } else { // kb

        $q = mysqli_query($koneksi, "SELECT * FROM tbl_kb WHERE id_kb = '$idAman' LIMIT 1");

    }

    if ($q && mysqli_num_rows($q) > 0) {
        $orangDipilih = mysqli_fetch_assoc($q);
    } else {
        $tipeDipilih = '';
    }
}


/* =========================================================
   MODE 1: DATA PENCARIAN
   Digabung dari 3 sumber yang memang tabelnya terpisah:
   pasien umum, ibu hamil, dan peserta KB.
========================================================= */

$daftarPencarian = [];

if (!$orangDipilih) {

    if (in_array('pasien', $tipeDiizinkan, true)) {

        $qPasien = mysqli_query(
            $koneksi,
            "SELECT id, no_rm, nama, alamat FROM tbl_pasien ORDER BY nama ASC"
        ) or die("Query gagal: " . mysqli_error($koneksi));

        while ($p = mysqli_fetch_assoc($qPasien)) {
            $daftarPencarian[] = [
                'tipe'      => 'pasien',
                'id'        => $p['id'],
                'nama'      => $p['nama'],
                'identitas' => 'No. RM: ' . $p['no_rm'],
                'alamat'    => $p['alamat'],
            ];
        }

    }

    if (in_array('ibu_hamil', $tipeDiizinkan, true)) {

        $qIbuHamil = mysqli_query(
            $koneksi,
            "SELECT id, nik, nama_ibu, alamat FROM tbl_ibu_hamil ORDER BY nama_ibu ASC"
        ) or die("Query gagal: " . mysqli_error($koneksi));

        while ($i = mysqli_fetch_assoc($qIbuHamil)) {
            $daftarPencarian[] = [
                'tipe'      => 'ibu_hamil',
                'id'        => $i['id'],
                'nama'      => $i['nama_ibu'],
                'identitas' => 'NIK: ' . ($i['nik'] ?: '-'),
                'alamat'    => $i['alamat'],
            ];
        }

    }

    if (in_array('kb', $tipeDiizinkan, true)) {

        $qKb = mysqli_query(
            $koneksi,
            "SELECT id_kb, no_kk, no_peserta_kb, nama_suami, alamat FROM tbl_kb ORDER BY no_peserta_kb ASC"
        ) or die("Query gagal: " . mysqli_error($koneksi));

        while ($k = mysqli_fetch_assoc($qKb)) {
            $daftarPencarian[] = [
                'tipe'      => 'kb',
                'id'        => $k['id_kb'],
                'nama'      => 'Peserta KB ' . $k['no_peserta_kb'] . ' (Suami: ' . $k['nama_suami'] . ')',
                'identitas' => 'No. KK: ' . ($k['no_kk'] ?: '-'),
                'alamat'    => $k['alamat'],
            ];
        }

    }

    usort($daftarPencarian, function ($a, $b) {
        return strcasecmp($a['nama'], $b['nama']);
    });

}


/* =========================================================
   MODE 2: DATA RIWAYAT SESUAI TIPE YANG DIPILIH
========================================================= */

if ($orangDipilih && $tipeDipilih === 'pasien') {

    $queryRiwayat = mysqli_query(
        $koneksi,
        "SELECT
            r.id_rm,
            r.tgl_rm,
            r.keluhan,
            r.diagnosa,
            r.poli,
            u.fullname AS nama_petugas
         FROM tbl_rekammedis r
         LEFT JOIN tbl_user u ON r.id_dokter = u.userid
         WHERE r.id_pasien = '{$orangDipilih['id']}'
         ORDER BY r.tgl_rm DESC"
    ) or die("Query gagal: " . mysqli_error($koneksi));

} elseif ($orangDipilih && $tipeDipilih === 'ibu_hamil') {

    $queryRiwayat = mysqli_query(
        $koneksi,
        "SELECT id, tanggal_pemeriksaan, usia_kehamilan, hasil, keterangan
         FROM tbl_pemeriksaan_ibu_hamil
         WHERE ibu_hamil_id = '{$orangDipilih['id']}'
         ORDER BY tanggal_pemeriksaan DESC"
    ) or die("Query gagal: " . mysqli_error($koneksi));

} elseif ($orangDipilih && $tipeDipilih === 'kb') {

    $queryRiwayat = mysqli_query(
        $koneksi,
        "SELECT id_pelayanan_kb, tanggal_pelayanan, metode_kb, hasil_pemeriksaan, keterangan
         FROM tbl_pelayanan_kb
         WHERE id_kb = '{$orangDipilih['id_kb']}'
         ORDER BY tanggal_pelayanan DESC"
    ) or die("Query gagal: " . mysqli_error($koneksi));

}

?>

<style>

/* =========================================
   SEARCH (disamakan dengan halaman lain)
========================================= */

.search-wrapper {
    max-width: 500px;
    margin-bottom: 20px;
}

.search-wrapper .input-group {
    background: white;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 3px 12px rgba(0,0,0,.06);
}

.search-wrapper .input-group-text {
    background: white;
    border: none;
    padding-left: 15px;
    color: #6c757d;
}

.search-wrapper .form-control {
    border: none;
    padding: 11px 12px;
    box-shadow: none;
}

.search-wrapper .form-control:focus {
    box-shadow: none;
}

@media (max-width: 768px) {
    .search-wrapper {
        max-width: 100%;
    }
}


/* .tipe-chip* sekarang didefinisikan secara global di
   template/header.php supaya warnanya konsisten di semua halaman. */


/* =========================================
   KARTU ORANG TERPILIH
========================================= */

.pasien-terpilih {
    background: #f8f8ff;
    border: 1px solid #dcddff;
    border-radius: 13px;
    padding: 16px 20px;
    margin-bottom: 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 15px;
    flex-wrap: wrap;
}

.pasien-terpilih .nama {
    font-weight: 700;
    color: #212229;
    font-size: 16px;
}

.pasien-terpilih .detail {
    font-size: 13px;
    color: #6c757d;
}

</style>

<div class="page-content-wrap">

    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap
                align-items-center pt-3 pb-2 mb-3 border-bottom">

        <h1 class="h2">
            <i class="bi bi-arrow-left-right me-2"></i>
            Surat Rujukan
        </h1>

    </div>


    <?php if (!$orangDipilih) { ?>


        <!-- =====================================================
             MODE 1: CARI PASIEN / IBU HAMIL / PESERTA KB
        ====================================================== -->

        <div class="search-wrapper">

            <div class="input-group">

                <span class="input-group-text">
                    <i class="bi bi-search"></i>
                </span>

                <input type="text"
                       id="searchPasienRujukan"
                       class="form-control"
                       placeholder="Cari nama, No. RM/NIK/No. KK, atau alamat...">

            </div>

        </div>


        <div class="card">

            <div class="card-header">
                <i class="bi bi-people"></i>
                Cari Orang untuk Dibuatkan Surat Rujukan
            </div>

            <div class="card-body">

                <div class="table-responsive">

                    <table class="table table-hover align-middle" id="myTable">

                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Jenis</th>
                                <th>Nama</th>
                                <th>Identitas</th>
                                <th>Alamat</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>

                        <tbody>

                            <?php

                            $no = 1;

                            if (count($daftarPencarian) > 0) {

                                foreach ($daftarPencarian as $o) {

                                    if ($o['tipe'] === 'ibu_hamil') {
                                        $tipeClass = 'tipe-chip-ibu-hamil';
                                        $tipeLabel = 'Ibu Hamil';
                                    } elseif ($o['tipe'] === 'kb') {
                                        $tipeClass = 'tipe-chip-kb';
                                        $tipeLabel = 'Peserta KB';
                                    } else {
                                        $tipeClass = 'tipe-chip-pasien';
                                        $tipeLabel = 'Pasien Umum';
                                    }

                            ?>

                                <tr>
                                    <td><?= $no++; ?></td>
                                    <td>
                                        <span class="tipe-chip <?= $tipeClass; ?>">
                                            <?= $tipeLabel; ?>
                                        </span>
                                    </td>
                                    <td><?= htmlspecialchars($o['nama']); ?></td>
                                    <td><?= htmlspecialchars($o['identitas']); ?></td>
                                    <td><?= htmlspecialchars($o['alamat']); ?></td>
                                    <td class="text-center">
                                        <a href="index.php?tipe=<?= $o['tipe']; ?>&id=<?= urlencode($o['id']); ?>"
                                           class="btn btn-sm btn-primary"
                                           title="Buat Surat Rujukan">
                                            <i class="bi bi-file-earmark-plus"></i>
                                            Buat Surat
                                        </a>
                                    </td>
                                </tr>

                            <?php
                                }

                            } else {
                            ?>

                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">
                                        <i class="bi bi-inbox fs-3"></i><br>
                                        Belum ada data.
                                    </td>
                                </tr>

                            <?php } ?>

                        </tbody>

                    </table>

                    <div id="noResultPasienRujukan" class="text-center text-muted py-4" style="display:none;">
                        <i class="bi bi-search fs-3"></i><br>
                        Tidak ada yang cocok dengan pencarian.
                    </div>

                </div>

            </div>

        </div>


    <?php } else { ?>


        <!-- =====================================================
             MODE 2: PILIH RIWAYAT SEBAGAI DASAR SURAT RUJUKAN
        ====================================================== -->

        <div class="pasien-terpilih">

            <div>

                <div class="nama">

                    <i class="bi bi-person-check me-1"></i>

                    <?php if ($tipeDipilih === 'ibu_hamil') { ?>

                        <?= htmlspecialchars($orangDipilih['nama_ibu']); ?>
                        <span class="tipe-chip tipe-chip-ibu-hamil ms-1">Ibu Hamil</span>

                    <?php } elseif ($tipeDipilih === 'kb') { ?>

                        Peserta KB <?= htmlspecialchars($orangDipilih['no_peserta_kb']); ?>
                        <span class="tipe-chip tipe-chip-kb ms-1">Peserta KB</span>

                    <?php } else { ?>

                        <?= htmlspecialchars($orangDipilih['nama']); ?>
                        <span class="tipe-chip tipe-chip-pasien ms-1">Pasien Umum</span>

                    <?php } ?>

                </div>

                <div class="detail">

                    <?php if ($tipeDipilih === 'ibu_hamil') { ?>

                        NIK: <?= htmlspecialchars($orangDipilih['nik'] ?: '-'); ?>
                        &nbsp;|&nbsp;
                        Alamat: <?= htmlspecialchars($orangDipilih['alamat']); ?>

                    <?php } elseif ($tipeDipilih === 'kb') { ?>

                        Suami: <?= htmlspecialchars($orangDipilih['nama_suami']); ?>
                        &nbsp;|&nbsp;
                        Alamat: <?= htmlspecialchars($orangDipilih['alamat']); ?>

                    <?php } else { ?>

                        No. RM: <?= htmlspecialchars($orangDipilih['no_rm']); ?>
                        &nbsp;|&nbsp;
                        Alamat: <?= htmlspecialchars($orangDipilih['alamat']); ?>

                    <?php } ?>

                </div>

            </div>

            <a href="index.php" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left me-1"></i>
                Cari Orang Lain
            </a>

        </div>


        <div class="card">

            <div class="card-header">
                <i class="bi bi-clipboard2-pulse"></i>
                <?php if ($tipeDipilih === 'ibu_hamil') { ?>
                    Pilih Riwayat Pemeriksaan Ibu Hamil sebagai Dasar Surat Rujukan
                <?php } elseif ($tipeDipilih === 'kb') { ?>
                    Pilih Riwayat Pelayanan KB sebagai Dasar Surat Rujukan
                <?php } else { ?>
                    Pilih Rekam Medis sebagai Dasar Surat Rujukan
                <?php } ?>
            </div>

            <div class="card-body">

                <div class="table-responsive">

                    <?php if ($tipeDipilih === 'ibu_hamil') { ?>

                    <table class="table table-hover align-middle">

                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Tanggal Periksa</th>
                                <th>Usia Kehamilan</th>
                                <th>Hasil</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>

                        <tbody>

                            <?php

                            $no = 1;

                            if (mysqli_num_rows($queryRiwayat) > 0) {

                                while ($rw = mysqli_fetch_assoc($queryRiwayat)) {

                            ?>

                                <tr>
                                    <td><?= $no++; ?></td>
                                    <td><?= date('d-m-Y', strtotime($rw['tanggal_pemeriksaan'])); ?></td>
                                    <td><?= htmlspecialchars($rw['usia_kehamilan']); ?> minggu</td>
                                    <td><?= htmlspecialchars($rw['hasil'] ?: '-'); ?></td>
                                    <td class="text-center">
                                        <a href="../laporan/surat-rujukan-ibu-hamil.php?id=<?= $rw['id']; ?>"
                                           target="_blank"
                                           class="btn btn-sm btn-outline-primary"
                                           title="Buat &amp; Cetak Surat Rujukan">
                                            <i class="bi bi-file-earmark-medical"></i>
                                            Buat Surat
                                        </a>
                                    </td>
                                </tr>

                            <?php
                                }

                            } else {
                            ?>

                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">
                                        <i class="bi bi-inbox fs-3"></i><br>
                                        Ibu ini belum punya riwayat pemeriksaan,
                                        belum bisa dibuatkan surat rujukan.
                                    </td>
                                </tr>

                            <?php } ?>

                        </tbody>

                    </table>

                    <?php } elseif ($tipeDipilih === 'kb') { ?>

                    <table class="table table-hover align-middle">

                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Tanggal Pelayanan</th>
                                <th>Metode KB</th>
                                <th>Hasil Pemeriksaan</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>

                        <tbody>

                            <?php

                            $no = 1;

                            if (mysqli_num_rows($queryRiwayat) > 0) {

                                while ($rw = mysqli_fetch_assoc($queryRiwayat)) {

                            ?>

                                <tr>
                                    <td><?= $no++; ?></td>
                                    <td><?= date('d-m-Y', strtotime($rw['tanggal_pelayanan'])); ?></td>
                                    <td><?= htmlspecialchars($rw['metode_kb'] ?: '-'); ?></td>
                                    <td><?= htmlspecialchars($rw['hasil_pemeriksaan'] ?: '-'); ?></td>
                                    <td class="text-center">
                                        <a href="../laporan/surat-rujukan-kb.php?id=<?= $rw['id_pelayanan_kb']; ?>"
                                           target="_blank"
                                           class="btn btn-sm btn-outline-primary"
                                           title="Buat &amp; Cetak Surat Rujukan">
                                            <i class="bi bi-file-earmark-medical"></i>
                                            Buat Surat
                                        </a>
                                    </td>
                                </tr>

                            <?php
                                }

                            } else {
                            ?>

                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">
                                        <i class="bi bi-inbox fs-3"></i><br>
                                        Peserta ini belum punya riwayat pelayanan KB,
                                        belum bisa dibuatkan surat rujukan.
                                    </td>
                                </tr>

                            <?php } ?>

                        </tbody>

                    </table>

                    <?php } else { ?>

                    <table class="table table-hover align-middle">

                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Tanggal</th>
                                <th>Poli</th>
                                <th>Keluhan</th>
                                <th>Diagnosa</th>
                                <th>Petugas</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>

                        <tbody>

                            <?php

                            $no = 1;

                            if (mysqli_num_rows($queryRiwayat) > 0) {

                                while ($rw = mysqli_fetch_assoc($queryRiwayat)) {

                            ?>

                                <tr>
                                    <td><?= $no++; ?></td>
                                    <td><?= date('d-m-Y', strtotime($rw['tgl_rm'])); ?></td>
                                    <td><?= htmlspecialchars($rw['poli'] ?: '-'); ?></td>
                                    <td><?= htmlspecialchars($rw['keluhan'] ?: '-'); ?></td>
                                    <td><?= htmlspecialchars($rw['diagnosa'] ?: '-'); ?></td>
                                    <td><?= htmlspecialchars($rw['nama_petugas'] ?? '-'); ?></td>
                                    <td class="text-center">
                                        <a href="../laporan/surat-rujukan.php?id=<?= $rw['id_rm']; ?>"
                                           target="_blank"
                                           class="btn btn-sm btn-outline-primary"
                                           title="Buat &amp; Cetak Surat Rujukan">
                                            <i class="bi bi-file-earmark-medical"></i>
                                            Buat Surat
                                        </a>
                                    </td>
                                </tr>

                            <?php
                                }

                            } else {
                            ?>

                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">
                                        <i class="bi bi-inbox fs-3"></i><br>
                                        Pasien ini belum memiliki data rekam medis,
                                        belum bisa dibuatkan surat rujukan.
                                    </td>
                                </tr>

                            <?php } ?>

                        </tbody>

                    </table>

                    <?php } ?>

                </div>

            </div>

        </div>


    <?php } ?>

</div>


<script>
$(function () {

    $('#searchPasienRujukan').on('keyup', function () {

        var keyword = $(this).val().toLowerCase();
        var jumlahTampil = 0;

        $('#myTable tbody tr').each(function () {

            var teks = $(this).text().toLowerCase();

            if (teks.indexOf(keyword) !== -1) {
                $(this).show();
                jumlahTampil++;
            } else {
                $(this).hide();
            }

        });

        $('#noResultPasienRujukan').toggle(jumlahTampil === 0);

    });

});
</script>


<?php
require "../template/footer.php";
?>
