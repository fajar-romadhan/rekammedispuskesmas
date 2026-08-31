<?php

session_start();

require "../template/rbac.php";

// Hanya Bidan
cekAkses([ROLE_BIDAN]);

require "../config.php";

$title = "Jadwal Kebidanan Hari Ini - Rekam Medis Puskesmas";

require "../template/header.php";
require "../template/navbar.php";
require "../template/sidebar.php";


/*
|--------------------------------------------------------------------------
| JADWAL KEBIDANAN HARI INI
|--------------------------------------------------------------------------
| Diisi oleh petugas lewat menu Pendaftaran / Registrasi Kebidanan.
| Bidan tinggal memilih siapa yang mau diperiksa / dilayani hari ini.
*/

$tanggalHariIni = date('Y-m-d');

$queryJadwal = mysqli_query($koneksi, "

    SELECT 
        j.id_pendaftaran,
        j.jenis_layanan,
        j.ref_id,
        j.no_antrian,
        j.status,
        j.tanggal_daftar

    FROM tbl_pendaftaran_kebidanan j

    WHERE j.tanggal = '$tanggalHariIni'
      AND j.status IN ('Menunggu', 'Dipanggil')

    ORDER BY j.no_antrian ASC

") or die("Query gagal: " . mysqli_error($koneksi));

?>

<div class="page-content-wrap">

    <!-- ==========================================
         JUDUL
    =========================================== -->

    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap
                align-items-center pt-3 pb-2 mb-3 border-bottom">

        <h1 class="h2">
            <i class="bi bi-heart-pulse me-3"></i>
            Jadwal Kebidanan Hari Ini
        </h1>

    </div>


    <!-- ==========================================
         INFORMASI TANGGAL
    =========================================== -->

    <div class="alert alert-primary">

        <i class="bi bi-calendar3"></i>

        Jadwal tanggal

        <strong>
            <?= date('d-m-Y'); ?>
        </strong>

        &mdash; pasien baru maupun kunjungan ulang didaftarkan oleh petugas
        melalui menu Pendaftaran Kebidanan.

    </div>


    <!-- ==========================================
         TABEL JADWAL
    =========================================== -->

    <div class="card">

        <div class="card-header">

            <i class="bi bi-list-check"></i>

            Menunggu Diperiksa / Dilayani

        </div>


        <div class="card-body">

            <div class="table-responsive">

                <table class="table table-hover align-middle">

                    <thead>

                        <tr>

                            <th>No. Antrian</th>

                            <th>Jenis Layanan</th>

                            <th>Identitas</th>

                            <th>Waktu Daftar</th>

                            <th>Status</th>

                            <th>Aksi</th>

                        </tr>

                    </thead>


                    <tbody>

                    <?php

                    if (mysqli_num_rows($queryJadwal) > 0) {

                        while ($j = mysqli_fetch_assoc($queryJadwal)) {

                            /*
                            |--------------------------------------------------------------------------
                            | DEFAULT
                            |--------------------------------------------------------------------------
                            */

                            $identitas = '-';
                            $linkAksi  = '#';
                            $labelAksi = 'Layani';
                            $iconAksi  = 'bi-clipboard2-pulse';


                            /*
                            |--------------------------------------------------------------------------
                            | JENIS LAYANAN IBU HAMIL
                            |--------------------------------------------------------------------------
                            */

                            if ($j['jenis_layanan'] == 'Ibu Hamil') {

                                $qIh = mysqli_query(
                                    $koneksi,
                                    "
                                    SELECT nama_ibu, nik
                                    FROM tbl_ibu_hamil
                                    WHERE id = '{$j['ref_id']}'
                                    "
                                );

                                $ih = $qIh ? mysqli_fetch_assoc($qIh) : null;

                                if ($ih) {

                                    $identitas =
                                        htmlspecialchars(
                                            $ih['nama_ibu']
                                        )
                                        . ' (NIK: '
                                        . htmlspecialchars(
                                            $ih['nik'] ?: '-'
                                        )
                                        . ')';
                                }

                                $linkAksi =
                                    "pemeriksaan-ibu-hamil.php?id={$j['ref_id']}";

                                $labelAksi = 'Periksa';

                                $iconAksi = 'bi-clipboard2-pulse';


                            /*
                            |--------------------------------------------------------------------------
                            | JENIS LAYANAN KB
                            |--------------------------------------------------------------------------
                            | NAMA ISTRI + NAMA SUAMI SAMA-SAMA DITAMPILKAN
                            |--------------------------------------------------------------------------
                            */

                            } else {

                                $qKb = mysqli_query(
                                    $koneksi,
                                    "
                                    SELECT
                                        no_peserta_kb,
                                        nama_istri,
                                        nama_suami
                                    FROM tbl_kb
                                    WHERE id_kb = '{$j['ref_id']}'
                                    "
                                );

                                $kb = $qKb ? mysqli_fetch_assoc($qKb) : null;

                                if ($kb) {

                                    $noPeserta = htmlspecialchars(
                                        $kb['no_peserta_kb'] ?: '-'
                                    );

                                    $namaIstri = htmlspecialchars(
                                        $kb['nama_istri'] ?: '-'
                                    );

                                    $namaSuami = htmlspecialchars(
                                        $kb['nama_suami'] ?: '-'
                                    );


                                    /*
                                    |--------------------------------------------------------------------------
                                    | TAMPILKAN NAMA ISTRI DAN SUAMI
                                    |--------------------------------------------------------------------------
                                    */

                                    $identitas = '
                                        <strong>Nama Istri:</strong>
                                        ' . $namaIstri . '
                                        <br>

                                        <strong>Nama Suami:</strong>
                                        ' . $namaSuami . '
                                        <br>

                                        <small class="text-muted">
                                            No. Peserta KB: ' . $noPeserta . '
                                        </small>
                                    ';
                                }

                                $linkAksi =
                                    "pelayanan-kb.php?id_kb={$j['ref_id']}";

                                $labelAksi = 'Layani';

                                $iconAksi = 'bi-heart-pulse';
                            }

                    ?>

                        <tr>

                            <!-- NO ANTRIAN -->

                            <td>

                                <span class="no-antrian-chip">

                                    <?= htmlspecialchars(
                                        $j['no_antrian']
                                    ); ?>

                                </span>

                            </td>


                            <!-- JENIS LAYANAN -->

                            <td>

                                <?php if ($j['jenis_layanan'] == 'Ibu Hamil') { ?>

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
                                <?= $identitas; ?>
                            </td>


                            <!-- WAKTU DAFTAR -->

                            <td>

                                <?= date(
                                    'H:i',
                                    strtotime($j['tanggal_daftar'])
                                ); ?>

                            </td>


                            <!-- STATUS -->

                            <td>

                                <?php if ($j['status'] == 'Menunggu') { ?>

                                    <span class="status-chip status-chip-menunggu">

                                        <i class="bi bi-clock"></i>

                                        Menunggu

                                    </span>

                                <?php } else { ?>

                                    <span class="status-chip status-chip-dipanggil">

                                        <i class="bi bi-megaphone"></i>

                                        Dipanggil

                                    </span>

                                <?php } ?>

                            </td>


                            <!-- AKSI -->

                            <td>

                                <a
                                    href="<?= $linkAksi; ?>"
                                    class="btn btn-sm btn-primary"
                                >

                                    <i class="bi <?= $iconAksi; ?>"></i>

                                    <?= $labelAksi; ?>

                                </a>


                                <a
                                    href="proses-jadwal-kebidanan.php?id=<?= $j['id_pendaftaran']; ?>&aksi=batal"
                                    class="btn btn-sm btn-outline-danger"
                                    title="Batalkan jadwal"
                                    onclick="return confirm('Batalkan jadwal ini?');"
                                >

                                    <i class="bi bi-x-lg"></i>

                                </a>

                            </td>

                        </tr>


                    <?php

                        }

                    } else {

                    ?>

                        <tr>

                            <td
                                colspan="6"
                                class="text-center text-muted py-4"
                            >

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

</div>


<?php

require "../template/footer.php";

?>
```
 