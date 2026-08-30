<?php

session_start();

require "../template/rbac.php";

// Hanya Bidan
cekAkses([ROLE_BIDAN]);

require "../config.php";

$title = "Detail Pelayanan KB - Rekam Medis Puskesmas";


/*
|--------------------------------------------------------------------------
| CEK ID
|--------------------------------------------------------------------------
*/

if (!isset($_GET['id']) || empty($_GET['id'])) {

    echo "
    <script>
        alert('ID pelayanan KB tidak ditemukan.');
        window.location='pelayanan-kb.php';
    </script>
    ";

    exit();
}


$id = mysqli_real_escape_string(
    $koneksi,
    $_GET['id']
);


/*
|--------------------------------------------------------------------------
| AMBIL DATA PELAYANAN KB
|--------------------------------------------------------------------------
|
| Data pelayanan diambil dari:
| tbl_pelayanan_kb
|
| Data peserta diambil dari:
| tbl_kb
|
| Relasi:
| tbl_pelayanan_kb.id_kb = tbl_kb.id_kb
|--------------------------------------------------------------------------
*/

$query = mysqli_query(
    $koneksi,
    "SELECT
        p.*,

        k.no_peserta_kb,
        k.no_kk,
        k.tanggal,
        k.tanggal_lahir,
        k.nama_suami,
        k.jumlah_anak,
        k.alamat,
        k.jenis_kb,
        k.kunjungan

     FROM tbl_pelayanan_kb p

     INNER JOIN tbl_kb k
        ON p.id_kb = k.id_kb

     WHERE p.id_pelayanan_kb = '$id'

     LIMIT 1"
);


if (!$query) {

    die(
        "Query detail pelayanan KB gagal: " .
        mysqli_error($koneksi)
    );

}


if (mysqli_num_rows($query) == 0) {

    echo "
    <script>
        alert('Data pelayanan KB tidak ditemukan.');
        window.location='pelayanan-kb.php';
    </script>
    ";

    exit();
}


$data = mysqli_fetch_assoc($query);


/*
|--------------------------------------------------------------------------
| FUNGSI TAMPIL DATA
|--------------------------------------------------------------------------
*/

function tampil($value)
{
    if (
        isset($value) &&
        $value !== null &&
        $value !== ''
    ) {

        return htmlspecialchars(
            $value,
            ENT_QUOTES,
            'UTF-8'
        );

    }

    return '-';
}


/*
|--------------------------------------------------------------------------
| FUNGSI TANGGAL
|--------------------------------------------------------------------------
*/

function tanggalIndonesia($tanggal)
{
    if (
        empty($tanggal) ||
        $tanggal == '0000-00-00'
    ) {

        return '-';

    }

    return date(
        'd-m-Y',
        strtotime($tanggal)
    );
}


require "../template/header.php";
require "../template/navbar.php";
require "../template/sidebar.php";

?>


<div class="page-content-wrap">


    <!-- =====================================================
         HEADER
    ====================================================== -->

    <div class="d-flex justify-content-between
                flex-wrap flex-md-nowrap
                align-items-center
                pt-3 pb-3 mb-4
                border-bottom">

        <div>

            <h1 class="h2 mb-1">
                Detail Pelayanan KB
            </h1>

            <p class="text-muted mb-0">
                Informasi lengkap pemeriksaan dan pelayanan peserta KB
            </p>

        </div>


        <div class="d-flex gap-2">

            <a
                href="pelayanan-kb.php"
                class="btn btn-secondary">

                <i class="bi bi-arrow-left me-1"></i>

                Kembali

            </a>


            <a
                href="edit-pelayanan-kb.php?id=<?= $data['id_pelayanan_kb']; ?>"
                class="btn btn-dark">

                <i class="bi bi-pencil me-1"></i>

                Edit

            </a>

        </div>

    </div>


    <!-- =====================================================
         INFORMASI PESERTA
    ====================================================== -->

    <div class="card border-0 shadow-sm mb-4">

        <div class="card-body">


            <h5 class="fw-bold mb-1">
                Data Peserta KB
            </h5>

            <p class="text-muted mb-4">
                Informasi peserta berdasarkan Register KB
            </p>


            <div class="row g-4">


                <!-- NO PESERTA -->

                <div class="col-md-4">

                    <div class="text-muted small mb-1">
                        No. Peserta KB
                    </div>

                    <div class="fw-bold">

                        <span class="kb-registration">

                            <?= tampil(
                                $data['no_peserta_kb']
                            ); ?>

                        </span>

                    </div>

                </div>


                <!-- NO KK -->

                <div class="col-md-4">

                    <div class="text-muted small mb-1">
                        No. KK
                    </div>

                    <div class="fw-bold">

                        <?= tampil(
                            $data['no_kk']
                        ); ?>

                    </div>

                </div>


                <!-- TANGGAL LAHIR -->

                <div class="col-md-4">

                    <div class="text-muted small mb-1">
                        Tanggal Lahir
                    </div>

                    <div class="fw-bold">

                        <?= tanggalIndonesia(
                            $data['tanggal_lahir'] ?? null
                        ); ?>

                    </div>

                </div>


                <!-- NAMA SUAMI -->

                <div class="col-md-4">

                    <div class="text-muted small mb-1">
                        Nama Suami
                    </div>

                    <div class="fw-bold">

                        <?= tampil(
                            $data['nama_suami']
                        ); ?>

                    </div>

                </div>


                <!-- JUMLAH ANAK -->

                <div class="col-md-4">

                    <div class="text-muted small mb-1">
                        Jumlah Anak
                    </div>

                    <div class="fw-bold">

                        <?= tampil(
                            $data['jumlah_anak']
                        ); ?>

                    </div>

                </div>


                <!-- JENIS KB REGISTER -->

                <div class="col-md-4">

                    <div class="text-muted small mb-1">
                        Jenis KB Register
                    </div>

                    <div class="fw-bold">

                        <span class="app-chip">

                            <?= tampil(
                                $data['jenis_kb']
                            ); ?>

                        </span>

                    </div>

                </div>


                <!-- ALAMAT -->

                <div class="col-12">

                    <div class="text-muted small mb-1">
                        Alamat
                    </div>

                    <div class="fw-bold">

                        <?= tampil(
                            $data['alamat']
                        ); ?>

                    </div>

                </div>


                <!-- KUNJUNGAN REGISTER -->

                <div class="col-md-4">

                    <div class="text-muted small mb-1">
                        Kunjungan Register
                    </div>

                    <?php

                    $badgeKunjungan =
                        "kb-visit-lama";

                    if (
                        ($data['kunjungan'] ?? '') ==
                        'Baru'
                    ) {

                        $badgeKunjungan =
                            "kb-visit-baru";

                    } elseif (
                        ($data['kunjungan'] ?? '') ==
                        'Ganti'
                    ) {

                        $badgeKunjungan =
                            "kb-visit-ganti";

                    }

                    ?>

                    <span class="kb-visit <?= $badgeKunjungan; ?>">

                        <?= tampil(
                            $data['kunjungan']
                        ); ?>

                    </span>

                </div>

            </div>

        </div>

    </div>


    <!-- =====================================================
         DATA PELAYANAN
    ====================================================== -->

    <div class="card border-0 shadow-sm mb-4">

        <div class="card-body">


            <h5 class="fw-bold mb-1">
                Data Pelayanan
            </h5>

            <p class="text-muted mb-4">
                Data kunjungan dan metode kontrasepsi
            </p>


            <div class="row g-4">


                <!-- TANGGAL PELAYANAN -->

                <div class="col-md-4">

                    <div class="text-muted small mb-1">
                        Tanggal Pelayanan
                    </div>

                    <div class="fw-bold">

                        <?= tanggalIndonesia(
                            $data['tanggal_pelayanan'] ?? null
                        ); ?>

                    </div>

                </div>


                <!-- METODE KB -->

                <div class="col-md-4">

                    <div class="text-muted small mb-1">
                        Metode / Alat Kontrasepsi
                    </div>

                    <div class="fw-bold">

                        <span class="app-chip">

                            <?= tampil(
                                $data['metode_kb']
                            ); ?>

                        </span>

                    </div>

                </div>


                <!-- KELUHAN -->

                <div class="col-md-4">

                    <div class="text-muted small mb-1">
                        Keluhan
                    </div>

                    <div class="fw-bold">

                        <?= tampil(
                            $data['keluhan']
                        ); ?>

                    </div>

                </div>

            </div>

        </div>

    </div>


    <!-- =====================================================
         HASIL PEMERIKSAAN
    ====================================================== -->

    <div class="card border-0 shadow-sm mb-4">

        <div class="card-body">


            <h5 class="fw-bold mb-1">
                Hasil Pemeriksaan
            </h5>

            <p class="text-muted mb-4">
                Hasil pemeriksaan peserta pada saat pelayanan
            </p>


            <div class="row g-4">


                <!-- BERAT BADAN -->

                <div class="col-md-4">

                    <div class="text-muted small mb-1">
                        Berat Badan
                    </div>

                    <div class="fw-bold">

                        <?php

                        if (
                            isset($data['berat_badan']) &&
                            $data['berat_badan'] !== '' &&
                            $data['berat_badan'] !== null
                        ) {

                            echo tampil(
                                $data['berat_badan']
                            );

                            echo " Kg";

                        } else {

                            echo "-";

                        }

                        ?>

                    </div>

                </div>


                <!-- TINGGI BADAN -->

                <div class="col-md-4">

                    <div class="text-muted small mb-1">
                        Tinggi Badan
                    </div>

                    <div class="fw-bold">

                        <?php

                        if (
                            isset($data['tinggi_badan']) &&
                            $data['tinggi_badan'] !== '' &&
                            $data['tinggi_badan'] !== null
                        ) {

                            echo tampil(
                                $data['tinggi_badan']
                            );

                            echo " cm";

                        } else {

                            echo "-";

                        }

                        ?>

                    </div>

                </div>


                <!-- TEKANAN DARAH -->

                <div class="col-md-4">

                    <div class="text-muted small mb-1">
                        Tekanan Darah
                    </div>

                    <div class="fw-bold">

                        <?php

                        if (
                            !empty(
                                $data['tekanan_darah']
                            )
                        ) {

                            echo tampil(
                                $data['tekanan_darah']
                            );

                            echo " mmHg";

                        } else {

                            echo "-";

                        }

                        ?>

                    </div>

                </div>


                <!-- HASIL PEMERIKSAAN -->

                <div class="col-md-6">

                    <div class="text-muted small mb-2">
                        Hasil Pemeriksaan
                    </div>

                    <div class="border rounded p-3 bg-light">

                        <?= nl2br(
                            tampil(
                                $data['hasil_pemeriksaan']
                            )
                        ); ?>

                    </div>

                </div>


                <!-- EFEK SAMPING -->

                <div class="col-md-6">

                    <div class="text-muted small mb-2">
                        Efek Samping
                    </div>

                    <div class="border rounded p-3 bg-light">

                        <?= nl2br(
                            tampil(
                                $data['efek_samping']
                            )
                        ); ?>

                    </div>

                </div>


                <!-- KETERANGAN -->

                <div class="col-12">

                    <div class="text-muted small mb-2">
                        Keterangan
                    </div>

                    <div class="border rounded p-3 bg-light">

                        <?= nl2br(
                            tampil(
                                $data['keterangan']
                            )
                        ); ?>

                    </div>

                </div>

            </div>

        </div>

    </div>


    <!-- =====================================================
         TOMBOL BAWAH
    ====================================================== -->

    <div class="d-flex justify-content-end gap-2 mb-5">

        <a
            href="pelayanan-kb.php"
            class="btn btn-secondary">

            <i class="bi bi-arrow-left me-1"></i>

            Kembali

        </a>


        <a
            href="edit-pelayanan-kb.php?id=<?= $data['id_pelayanan_kb']; ?>"
            class="btn btn-dark">

            <i class="bi bi-pencil me-1"></i>

            Edit Data

        </a>

    </div>


</div>


<?php

require "../template/footer.php";

?>