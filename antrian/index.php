<?php

session_start();

require "../template/rbac.php";

/*
|--------------------------------------------------------------------------
| ANTRIAN: PETUGAS (SEMUA POLI), DOKTER (POLI UMUM), BIDAN (POLI KEBIDANAN)
|--------------------------------------------------------------------------
*/
cekAkses([ROLE_PETUGAS, ROLE_DOKTER, ROLE_BIDAN]);


if (userHasRole(ROLE_BIDAN) && !userHasAnyRole([ROLE_PETUGAS, ROLE_DOKTER])) {
    header("location: ../bidan/jadwal-kebidanan.php");
    exit();
}

require "../config.php";

$title = "Antrian Pasien - rekammedispuskesmas";

require "../template/header.php";
require "../template/navbar.php";
require "../template/sidebar.php";


/*
|--------------------------------------------------------------------------
| TANGGAL HARI INI
|--------------------------------------------------------------------------
*/

$tanggalHariIni = date('Y-m-d');


/*
|--------------------------------------------------------------------------
| POLI YANG BOLEH DILIHAT
|--------------------------------------------------------------------------
| Petugas punya 2 menu terpisah di sidebar ("Antrian Pasien Umum" &
| "Antrian Kebidanan", lewat ?poli=umum / ?poli=kebidanan) jadi bisa
| memilih. Dokter hanya boleh melihat antrian Poli Umum, Bidan hanya Poli
| Kebidanan, apapun ?poli= yang dikirim. Akun yang punya kedua role
| Dokter+Bidan (tapi bukan Petugas) tetap melihat keduanya karena tidak
| ada cara membedakan mau lihat yang mana tanpa fitur "Ganti Role".
|--------------------------------------------------------------------------
*/

$poliDiminta = $_GET['poli'] ?? '';

$filterPoli = null;

if (userHasRole(ROLE_PETUGAS)) {

    if ($poliDiminta === 'kebidanan') {
        $filterPoli = 'Kebidanan';
    } elseif ($poliDiminta === 'umum') {
        $filterPoli = 'Umum';
    }
    // ?poli= kosong/lainnya -> petugas lihat semua poli.

} else {

    if (userHasRole(ROLE_DOKTER) && !userHasRole(ROLE_BIDAN)) {
        $filterPoli = 'Umum';
    } elseif (userHasRole(ROLE_BIDAN) && !userHasRole(ROLE_DOKTER)) {
        $filterPoli = 'Kebidanan';
    }
}

$filterPoliSql = $filterPoli
    ? "AND a.jenis_layanan = '" . mysqli_real_escape_string($koneksi, $filterPoli) . "'"
    : '';


/*
|--------------------------------------------------------------------------
| DATA ANTRIAN HARI INI
|--------------------------------------------------------------------------
| Kebidanan (Ibu Hamil / KB) tidak tersimpan di tbl_pasien, jadi tidak
| bisa ikut query tbl_antrian di bawah (butuh INNER JOIN tbl_pasien).
| Antriannya diambil terpisah dari tbl_pendaftaran_kebidanan (diisi lewat
| petugas/pendaftaran-kebidanan.php), dengan nomor antrian yang dihitung
| sama seperti Poli Umum (lihat nextNoAntrianKebidanan() di config.php).
| Identitasnya digabung di PHP karena sumbernya dua tabel berbeda
| (tbl_ibu_hamil / tbl_kb) -- sama seperti di bidan/jadwal-kebidanan.php.
*/

$dataKebidanan = [];

if ($filterPoli === 'Kebidanan') {

    $queryKebidanan = mysqli_query($koneksi, "

        SELECT id_pendaftaran, jenis_layanan, ref_id, no_antrian, status, tanggal_daftar
        FROM tbl_pendaftaran_kebidanan
        WHERE tanggal = '$tanggalHariIni'
        ORDER BY no_antrian ASC

    ") or die("Query gagal: " . mysqli_error($koneksi));

    while ($j = mysqli_fetch_assoc($queryKebidanan)) {

        $identitas = '-';

        if ($j['jenis_layanan'] === 'Ibu Hamil') {

            $qIh = mysqli_query($koneksi, "
                SELECT nama_ibu, nik
                FROM tbl_ibu_hamil
                WHERE id = '{$j['ref_id']}'
            ");
            $ih = $qIh ? mysqli_fetch_assoc($qIh) : null;

            if ($ih) {
                $identitas = $ih['nama_ibu'] . ' (NIK: ' . ($ih['nik'] ?: '-') . ')';
            }

        } else {

            $qKb = mysqli_query($koneksi, "
                SELECT no_peserta_kb, nama_suami
                FROM tbl_kb
                WHERE id_kb = '{$j['ref_id']}'
            ");
            $kb = $qKb ? mysqli_fetch_assoc($qKb) : null;

            if ($kb) {
                $identitas = 'No. Peserta ' . $kb['no_peserta_kb'] . ' - ' . $kb['nama_suami'];
            }
        }

        $j['identitas'] = $identitas;
        $dataKebidanan[] = $j;
    }

} else {

    $query = mysqli_query($koneksi, "

        SELECT
            a.*,
            p.nama,
            p.gender,
            p.tgl_lahir

        FROM tbl_antrian a

        INNER JOIN tbl_pasien p
            ON a.id_pasien = p.id

        WHERE a.tanggal = '$tanggalHariIni'
          $filterPoliSql

        ORDER BY a.no_antrian ASC

    ") or die("Query gagal: " . mysqli_error($koneksi));

}


/*
|--------------------------------------------------------------------------
| JUDUL HALAMAN SESUAI FILTER POLI
|--------------------------------------------------------------------------
*/

$judulAntrian = 'Antrian Pasien';
$iconAntrian  = 'bi-people';

if ($filterPoli === 'Umum') {
    $judulAntrian = 'Antrian Pasien Umum';
    $iconAntrian  = 'bi-clipboard2-pulse';
} elseif ($filterPoli === 'Kebidanan') {
    $judulAntrian = 'Antrian Kebidanan';
    $iconAntrian  = 'bi-heart-pulse';
}

?>


<style>

/* .no-antrian-chip / .poli-chip* / .status-chip* sekarang didefinisikan
   secara global di template/header.php (dipakai juga di rekammedis/
   index.php dkk) supaya warnanya konsisten di semua halaman. */

/* =========================================
   HEADER INFO TANGGAL
========================================= */

.antrian-info-tanggal {
    background: #f8f8ff;
    border: 1px solid #dcddff;
    border-radius: 12px;
    padding: 14px 18px;
    margin-bottom: 20px;
    color: #363454;
}

.antrian-info-tanggal i {
    color: #7571f9;
    margin-right: 6px;
}

</style>


<div class="page-content-wrap">


    <!-- ==========================================
         JUDUL
         ========================================== -->

    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap
                align-items-center pt-3 pb-2 mb-3 border-bottom">

<h1 class="h2">
    <i class="bi <?= $iconAntrian; ?> me-3"></i>
    <?= htmlspecialchars($judulAntrian); ?>
</h1>
    </div>


    <!-- ==========================================
         INFORMASI TANGGAL
         ========================================== -->

    <div class="antrian-info-tanggal">

        <i class="bi bi-calendar3"></i>

        Antrian tanggal

        <strong>
            <?= date('d-m-Y'); ?>
        </strong>

        <?php if ($filterPoli === 'Umum') { ?>
            &mdash; menampilkan <strong>Poli Umum</strong> saja
        <?php } elseif ($filterPoli === 'Kebidanan') { ?>
            &mdash; menampilkan <strong>Poli Kebidanan</strong> saja
        <?php } ?>

    </div>


    <!-- ==========================================
         TABEL ANTRIAN
         ========================================== -->

    <?php if ($filterPoli === 'Kebidanan') { ?>


    <!-- ==========================================
         TABEL ANTRIAN KEBIDANAN (Ibu Hamil / KB)
         -- sumber data & kolomnya beda dari Poli Umum di
         bawah, lihat catatan di query $dataKebidanan di atas.
         ========================================== -->

    <div class="card">

        <div class="card-header">

            <i class="bi bi-people"></i>

            Daftar Antrian Hari Ini

        </div>


        <div class="card-body">

            <div class="table-responsive">

                <table class="table table-hover align-middle">


                    <thead>

                        <tr>

                            <th>No</th>

                            <th>No. Antrian</th>

                            <th>Jenis</th>

                            <th>Identitas</th>

                            <th>Jam Daftar</th>

                            <th>Status</th>

                            <th>Aksi</th>

                        </tr>

                    </thead>


                    <tbody>


                    <?php

                    $no = 1;

                    if (count($dataKebidanan) > 0) {

                        foreach ($dataKebidanan as $data) {

                    ?>


                        <tr>


                            <!-- NO -->
                            <td>
                                <?= $no++; ?>
                            </td>


                            <!-- NO ANTRIAN -->
                            <td>
                                <span class="no-antrian-chip">
                                    <?= htmlspecialchars($data['no_antrian']); ?>
                                </span>
                            </td>


                            <!-- JENIS -->
                            <td>

                                <?php if ($data['jenis_layanan'] === 'Ibu Hamil') { ?>

                                    <span class="tipe-chip tipe-chip-ibu-hamil">
                                        <i class="bi bi-person-heart"></i>
                                        Ibu Hamil
                                    </span>

                                <?php } else { ?>

                                    <span class="tipe-chip tipe-chip-kb">
                                        <i class="bi bi-people"></i>
                                        KB
                                    </span>

                                <?php } ?>

                            </td>


                            <!-- IDENTITAS -->
                            <td>
                                <?= htmlspecialchars($data['identitas']); ?>
                            </td>


                            <!-- JAM DAFTAR -->
                            <td>
                                <?= htmlspecialchars(
                                    date('H:i', strtotime($data['tanggal_daftar']))
                                ); ?>
                            </td>


                            <!-- STATUS -->
                            <td>

                                <?php if ($data['status'] == 'Menunggu') { ?>

                                    <span class="status-chip status-chip-menunggu">
                                        <i class="bi bi-clock"></i>
                                        Menunggu
                                    </span>

                                <?php } elseif ($data['status'] == 'Dipanggil') { ?>

                                    <span class="status-chip status-chip-dipanggil">
                                        <i class="bi bi-megaphone"></i>
                                        Dipanggil
                                    </span>

                                <?php } elseif ($data['status'] == 'Selesai') { ?>

                                    <span class="status-chip status-chip-selesai">
                                        <i class="bi bi-check-circle"></i>
                                        Selesai
                                    </span>

                                <?php } else { ?>

                                    <span class="status-chip status-chip-lain">
                                        <?= htmlspecialchars($data['status']); ?>
                                    </span>

                                <?php } ?>

                            </td>


                            <!-- AKSI -->
                            <td>

                                <?php
                                // Sama seperti Poli Umum: "Layani/Periksa" cuma buat Bidan,
                                // dan cuma dibuka untuk baris yang masih Menunggu/Dipanggil.
                                $bisaLayani = userHasRole(ROLE_BIDAN)
                                    && in_array($data['status'], ['Menunggu', 'Dipanggil'], true);

                                $linkLayani = $data['jenis_layanan'] === 'Ibu Hamil'
                                    ? "../bidan/pemeriksaan-ibu-hamil.php?id={$data['ref_id']}"
                                    : "../bidan/pelayanan-kb.php?id_kb={$data['ref_id']}";
                                ?>

                                <?php if ($data['status'] == 'Menunggu') { ?>

                                    <!-- TOMBOL PANGGIL -->
                                    <a href="proses-antrian.php?id=<?= $data['id_pendaftaran']; ?>&aksi=panggil&sumber=kebidanan"
                                       class="btn btn-sm btn-outline-primary"
                                       title="Panggil Pasien"
                                       onclick="return confirm('Panggil pasien ini?')">
                                        <i class="bi bi-check-lg"></i>
                                        Panggil
                                    </a>

                                <?php } elseif ($data['status'] == 'Dipanggil') { ?>

                                    <!-- TOMBOL SELESAI -->
                                    <a href="proses-antrian.php?id=<?= $data['id_pendaftaran']; ?>&aksi=selesai&sumber=kebidanan"
                                       class="btn btn-sm btn-outline-success"
                                       title="Selesaikan Antrian"
                                       onclick="return confirm('Apakah pasien sudah selesai dilayani?')">
                                        <i class="bi bi-check-circle"></i>
                                        Selesai
                                    </a>

                                <?php } ?>

                                <?php if ($bisaLayani) { ?>

                                    <!-- TOMBOL LAYANI/PERIKSA -->
                                    <a href="<?= $linkLayani; ?>"
                                       class="btn btn-sm btn-primary"
                                       title="Layani &amp; buat rekam medis kebidanan">
                                        <i class="bi bi-clipboard2-pulse"></i>
                                        Layani
                                    </a>

                                <?php } ?>

                                <?php if ($data['status'] != 'Menunggu' && $data['status'] != 'Dipanggil') { ?>

                                    <span class="text-success">
                                        <i class="bi bi-check-circle-fill"></i>
                                        <?= htmlspecialchars($data['status']); ?>
                                    </span>

                                <?php } ?>

                            </td>


                        </tr>


                    <?php

                        }

                    } else {

                    ?>


                        <tr>

                            <td colspan="7"
                                class="text-center text-muted py-4">

                                <i class="bi bi-inbox fs-3"></i>

                                <br>

                                Belum ada pasien yang dijadwalkan hari ini.

                            </td>

                        </tr>


                    <?php

                    }

                    ?>


                    </tbody>

                </table>

            </div>

        </div>

    </div>


    <?php } else { ?>


    <!-- ==========================================
         TABEL ANTRIAN (Poli Umum / semua poli)
         ========================================== -->

    <div class="card">

        <div class="card-header">

            <i class="bi bi-people"></i>

            Daftar Antrian Hari Ini

        </div>


        <div class="card-body">

            <div class="table-responsive">

                <table class="table table-hover align-middle">


                    <thead>

                        <tr>

                            <th>No</th>

                            <th>No. Antrian</th>

                            <th>No. RM</th>

                            <th>Nama Pasien</th>

                            <th>Jenis Kelamin</th>

                            <th>Poli</th>

                            <th>Jam Daftar</th>

                            <th>Jenis Pembayaran</th>

                            <th>Status</th>

                            <th>Aksi</th>

                        </tr>

                    </thead>


                    <tbody>


                    <?php

                    $no = 1;

                    if (mysqli_num_rows($query) > 0) {

                        while ($data = mysqli_fetch_assoc($query)) {

                    ?>


                        <tr>


                            <!-- NO -->

                            <td>

                                <?= $no++; ?>

                            </td>


                            <!-- NO ANTRIAN -->

                            <td>

                                <span class="no-antrian-chip">

                                    <?= htmlspecialchars(
                                        $data['no_antrian']
                                    ); ?>

                                </span>

                            </td>


                            <!-- NO RM -->

                            <td>

                                <?= htmlspecialchars(
                                    $data['no_rm']
                                ); ?>

                            </td>


                            <!-- NAMA -->

                            <td>

                                <?= htmlspecialchars(
                                    $data['nama']
                                ); ?>

                            </td>


                            <!-- JENIS KELAMIN -->

                            <td>

                                <?php

                                if ($data['gender'] == 'P') {

                                    echo 'Pria';

                                } else {

                                    echo 'Wanita';

                                }

                                ?>

                            </td>


                            <!-- POLI -->

                            <td>

                                <?php if ($data['jenis_layanan'] == 'Kebidanan') { ?>

                                    <span class="poli-chip poli-chip-kebidanan">
                                        <i class="bi bi-heart-pulse"></i>
                                        Poli Kebidanan
                                    </span>

                                <?php } else { ?>

                                    <span class="poli-chip poli-chip-umum">
                                        <i class="bi bi-clipboard2-pulse"></i>
                                        Poli Umum
                                    </span>

                                <?php } ?>

                            </td>


                            <!-- JAM DAFTAR -->

                            <td>

                                <?= htmlspecialchars(
                                    date('H:i', strtotime($data['tanggal_daftar']))
                                ); ?>

                            </td>


                            <!-- PEMBAYARAN -->

                            <td>

                                <?= htmlspecialchars(
                                    $data['jenis_pembayaran']
                                ); ?>

                            </td>


                            <!-- STATUS -->

                            <td>

                                <?php

                                if ($data['status'] == 'Menunggu') {

                                ?>

                                    <span class="status-chip status-chip-menunggu">

                                        <i class="bi bi-clock"></i>

                                        Menunggu

                                    </span>


                                <?php

                                } elseif ($data['status'] == 'Dipanggil') {

                                ?>

                                    <span class="status-chip status-chip-dipanggil">

                                        <i class="bi bi-megaphone"></i>

                                        Dipanggil

                                    </span>


                                <?php

                                } elseif ($data['status'] == 'Selesai') {

                                ?>

                                    <span class="status-chip status-chip-selesai">

                                        <i class="bi bi-check-circle"></i>

                                        Selesai

                                    </span>


                                <?php

                                } else {

                                ?>

                                    <span class="status-chip status-chip-lain">

                                        <?= htmlspecialchars(
                                            $data['status']
                                        ); ?>

                                    </span>

                                <?php

                                }

                                ?>

                            </td>


                            <!-- AKSI -->

                            <td>


                                <?php
                                // Tombol "Periksa" (buat rekam medis) cuma buat Dokter/Bidan --
                                // menyimpan rekam medisnya nanti otomatis menandai antrian ini
                                // "Selesai" juga (lihat rekammedis/proses-data.php), jadi tidak
                                // perlu klik "Selesai" terpisah lagi.
                                $bisaPeriksa = userHasAnyRole([ROLE_DOKTER, ROLE_BIDAN])
                                    && in_array($data['status'], ['Menunggu', 'Dipanggil'], true);
                                ?>

                                <?php if ($data['status'] == 'Menunggu') { ?>


                                    <!-- TOMBOL PANGGIL -->

                                    <a href="proses-antrian.php?id=<?= $data['id']; ?>&aksi=panggil"

                                       class="btn btn-sm btn-outline-primary"

                                       title="Panggil Pasien"

                                       onclick="return confirm('Panggil pasien <?= htmlspecialchars($data['nama']); ?>?')">

                                        <i class="bi bi-check-lg"></i>

                                        Panggil

                                    </a>


                                <?php } elseif ($data['status'] == 'Dipanggil') { ?>


                                    <!-- TOMBOL SELESAI -->

                                    <a href="proses-antrian.php?id=<?= $data['id']; ?>&aksi=selesai"

                                       class="btn btn-sm btn-outline-success"

                                       title="Selesaikan Antrian"

                                       onclick="return confirm('Apakah pasien sudah selesai dilayani?')">

                                        <i class="bi bi-check-circle"></i>

                                        Selesai

                                    </a>


                                <?php } ?>


                                <?php if ($bisaPeriksa) { ?>


                                    <!-- TOMBOL PERIKSA (BUAT REKAM MEDIS) -->

                                    <a href="../rekammedis/tambah-data.php?antrian_id=<?= $data['id']; ?>"

                                       class="btn btn-sm btn-primary"

                                       title="Periksa &amp; buat rekam medis">

                                        <i class="bi bi-clipboard2-pulse"></i>

                                        Periksa

                                    </a>


                                <?php } ?>


                                <?php if ($data['status'] != 'Menunggu' && $data['status'] != 'Dipanggil') { ?>


                                    <span class="text-success">

                                        <i class="bi bi-check-circle-fill"></i>

                                        Selesai

                                    </span>


                                <?php } ?>


                            </td>


                        </tr>


                    <?php

                        }

                    } else {

                    ?>


                        <tr>

                            <td colspan="10"
                                class="text-center text-muted py-4">

                                <i class="bi bi-inbox fs-3"></i>

                                <br>

                                Belum ada pasien yang mendaftar hari ini.

                            </td>

                        </tr>


                    <?php

                    }

                    ?>


                    </tbody>

                </table>

            </div>

        </div>

    </div>


    <?php } ?>

</div>


<?php

require "../template/footer.php";

?>