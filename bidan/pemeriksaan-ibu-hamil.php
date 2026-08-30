<?php
session_start();

require "../template/rbac.php";

// Hanya Bidan
cekAkses([ROLE_BIDAN]);

require "../config.php";

$title = "Pemeriksaan Ibu Hamil - Rekam Medis Puskesmas";

/*
|--------------------------------------------------------------------------
| CEK ID IBU HAMIL
|--------------------------------------------------------------------------
*/

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("location: jadwal-kebidanan.php");
    exit();
}

$id = (int) $_GET['id'];

/*
|--------------------------------------------------------------------------
| AMBIL DATA IBU HAMIL
|--------------------------------------------------------------------------
*/

$queryIbu = mysqli_query(
    $koneksi,
    "SELECT * FROM tbl_ibu_hamil WHERE id = '$id'"
);

if (!$queryIbu) {
    die("Query gagal : " . mysqli_error($koneksi));
}

if (mysqli_num_rows($queryIbu) == 0) {
    die("Data ibu hamil tidak ditemukan.");
}

$ibu = mysqli_fetch_assoc($queryIbu);

/*
|--------------------------------------------------------------------------
| NOMOR REGISTRASI OTOMATIS
|--------------------------------------------------------------------------
*/

$no_registrasi = "IBH-" . str_pad($ibu['id'], 4, "0", STR_PAD_LEFT);


/*
|--------------------------------------------------------------------------
| DATA OBAT & TINDAKAN UNTUK TOKENFIELD
| (dipakai untuk menghitung tagihan pembayaran pasien otomatis)
|--------------------------------------------------------------------------
*/

$nmTindakan = [];

$queryTindakan = mysqli_query(
    $koneksi,
    "SELECT nama FROM tbl_obat WHERE kategori = 'Tindakan' ORDER BY nama ASC"
);

while ($dataTindakan = mysqli_fetch_assoc($queryTindakan)) {
    $nmTindakan[] = $dataTindakan['nama'];
}

$nmObat = [];

$queryObatBidan = mysqli_query(
    $koneksi,
    "SELECT nama FROM tbl_obat WHERE kategori = 'Obat' AND stok > 0 ORDER BY nama ASC"
);

while ($dataObatBidan = mysqli_fetch_assoc($queryObatBidan)) {
    $nmObat[] = $dataObatBidan['nama'];
}


/*
|--------------------------------------------------------------------------
| SIMPAN PEMERIKSAAN
|--------------------------------------------------------------------------
*/

if (isset($_POST['simpan_pemeriksaan'])) {

    $tanggal_pemeriksaan = mysqli_real_escape_string(
        $koneksi,
        $_POST['tanggal_pemeriksaan']
    );

    $usia_kehamilan = mysqli_real_escape_string(
        $koneksi,
        $_POST['usia_kehamilan']
    );

    $bb = mysqli_real_escape_string(
        $koneksi,
        $_POST['bb']
    );

    $td = mysqli_real_escape_string(
        $koneksi,
        $_POST['td']
    );

    $tfu = mysqli_real_escape_string(
        $koneksi,
        $_POST['tfu']
    );

    $djj = mysqli_real_escape_string(
        $koneksi,
        $_POST['djj']
    );

    $hasil = mysqli_real_escape_string(
        $koneksi,
        $_POST['hasil']
    );

    $keterangan = mysqli_real_escape_string(
        $koneksi,
        $_POST['keterangan']
    );

    $tindakan_billing = mysqli_real_escape_string(
        $koneksi,
        trim($_POST['tindakan_billing'] ?? '')
    );

    $obat_billing = mysqli_real_escape_string(
        $koneksi,
        trim($_POST['obat_billing'] ?? '')
    );

    $total_biaya = hitungTotalBiaya($koneksi, $tindakan_billing)
        + hitungTotalBiaya($koneksi, $obat_billing);


    // Kurangi stok obat (kalau ada dipilih) SEBELUM data pemeriksaan
    // disimpan, supaya kalau stok tidak cukup pemeriksaan juga tidak
    // ikut tersimpan setengah-setengah.
    try {

        kurangiStokObat($koneksi, $obat_billing);

    } catch (Exception $e) {

        // Redirect eksplisit (bukan window.history.back(), yang
        // bergantung pada isi stack riwayat browser dan bisa
        // melenceng ke halaman lain) supaya bidan pasti kembali ke
        // form pemeriksaan pasien yang sama, bukan hilang konteks.
        echo "
        <script>
            alert(" . json_encode($e->getMessage()) . ");
            window.location=" . json_encode("pemeriksaan-ibu-hamil.php?id=$id") . ";
        </script>
        ";

        exit();

    }


    $simpan = mysqli_query(
        $koneksi,
        "INSERT INTO tbl_pemeriksaan_ibu_hamil
        (
            ibu_hamil_id,
            no_registrasi,
            tanggal_pemeriksaan,
            usia_kehamilan,
            berat_badan,
            tekanan_darah,
            tfu,
            djj,
            hasil,
            keterangan,
            tindakan_billing,
            obat_billing,
            total_biaya
        )
        VALUES
        (
            '$id',
            '$no_registrasi',
            '$tanggal_pemeriksaan',
            '$usia_kehamilan',
            '$bb',
            '$td',
            '$tfu',
            '$djj',
            '$hasil',
            '$keterangan',
            '$tindakan_billing',
            '$obat_billing',
            '$total_biaya'
        )"
    );


    if ($simpan) {

        // mysqli_insert_id() harus diambil SEBELUM query lain
        // dijalankan di koneksi yang sama, kalau tidak nilainya
        // ter-reset ke 0 (dites: UPDATE ke tabel lain sudah cukup
        // membuatnya reset walau tabel itu tidak diubah).
        $idPemeriksaanBaru = mysqli_insert_id($koneksi);

        // Tandai jadwal kebidanan hari ini (kalau ada) sebagai selesai.
        mysqli_query(
            $koneksi,
            "UPDATE tbl_pendaftaran_kebidanan
             SET status = 'Selesai'
             WHERE jenis_layanan = 'Ibu Hamil'
               AND ref_id = '$id'
               AND tanggal = CURDATE()
               AND status IN ('Menunggu', 'Dipanggil')"
        );

        // Jenis pembayaran: tbl_ibu_hamil tidak punya kolom kategori
        // pembayaran terpisah, cuma kolom bpjs (nomor BPJS/KIS bebas
        // teks) -- kalau terisi berarti pakai BPJS, kalau kosong Umum.
        $jenisPembayaranTagihan = !empty($ibu['bpjs']) ? 'BPJS' : 'Umum';

        // Buat tagihan pembayaran untuk Petugas.
        upsertPembayaran(
            $koneksi,
            'ibu_hamil',
            $idPemeriksaanBaru,
            $ibu['nama_ibu'],
            'Kebidanan',
            $tanggal_pemeriksaan,
            $total_biaya,
            $jenisPembayaranTagihan
        );

        header(
            "location: pemeriksaan-ibu-hamil.php?id=$id&status=sukses"
        );

        exit();

    } else {

        $error = "Data pemeriksaan gagal disimpan : "
               . mysqli_error($koneksi);
    }
}


/*
|--------------------------------------------------------------------------
| AMBIL RIWAYAT PEMERIKSAAN
|--------------------------------------------------------------------------
*/

$queryPemeriksaan = mysqli_query(
    $koneksi,
    "SELECT *
     FROM tbl_pemeriksaan_ibu_hamil
     WHERE ibu_hamil_id = '$id'
     ORDER BY tanggal_pemeriksaan DESC, id DESC"
);


require "../template/header.php";
require "../template/navbar.php";
require "../template/sidebar.php";
?>


<style>

/* =========================================================
   HALAMAN PEMERIKSAAN IBU HAMIL
========================================================= */

.pemeriksaan-page {
    background: #f6f6fb;
    min-height: 100vh;
    padding-bottom: 40px;
}


/* =========================================================
   HEADER
========================================================= */

.page-header {
    background: #ffffff;
    border-radius: 18px;
    padding: 24px 28px;
    margin-top: 22px;
    margin-bottom: 24px;
    box-shadow: 0 4px 18px rgba(0, 0, 0, 0.05);
    border: 1px solid #eeeef5;
}

.page-title {
    font-size: 25px;
    font-weight: 700;
    color: #212229;
    margin-bottom: 5px;
}

.page-subtitle {
    color: #7a7a91;
    font-size: 14px;
    margin: 0;
}


/* =========================================================
   BUTTON
========================================================= */

.btn-custom {
    border-radius: 10px;
    padding: 9px 16px;
    font-size: 14px;
    font-weight: 600;
    transition: all 0.2s ease;
}

.btn-custom:hover {
    transform: translateY(-1px);
}

.btn-pdf {
    background: #dc3545;
    color: white;
    border: none;
}

.btn-pdf:hover {
    background: #bb2d3b;
    color: white;
}

.btn-back {
    background: #6c757d;
    color: white;
    border: none;
}

.btn-back:hover {
    background: #5c636a;
    color: white;
}

.btn-save {
    background: #198754;
    color: white;
    border: none;
    border-radius: 10px;
    padding: 11px 20px;
    font-weight: 600;
}

.btn-save:hover {
    background: #157347;
    color: white;
}


/* =========================================================
   CARD
========================================================= */

.medical-card {
    background: #ffffff;
    border: 1px solid #ededf4;
    border-radius: 18px;
    box-shadow: 0 4px 18px rgba(0, 0, 0, 0.045);
    margin-bottom: 36px;
    overflow: hidden;
}

.medical-card-body {
    padding: 26px;
}


/* =========================================================
   CARD HEADER
========================================================= */

.card-section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 1px solid #ededf4;
    padding-bottom: 16px;
    margin-bottom: 22px;
}

.card-section-title {
    font-size: 17px;
    font-weight: 700;
    color: #212229;
    margin: 0;
}

.card-section-subtitle {
    color: #8b8aa3;
    font-size: 13px;
    margin-top: 4px;
}


/* =========================================================
   REGISTRASI
========================================================= */

.registration-badge {
    background: #212229;
    color: white;
    border-radius: 10px;
    padding: 8px 14px;
    font-size: 13px;
    font-weight: 600;
    letter-spacing: 0.4px;
}


/* =========================================================
   IDENTITAS PASIEN
========================================================= */

.patient-info {
    background: #f8f8fc;
    border: 1px solid #ededf4;
    border-radius: 12px;
    padding: 18px 20px;
    height: 100%;
    transition: all 0.2s ease;
}

.patient-info:hover {
    background: #f4f4fa;
    border-color: #dfe0eb;
}

.patient-label {
    display: block;
    font-size: 12px;
    color: #8989a0;
    margin-bottom: 10px;
}

.patient-value {
    font-size: 14px;
    font-weight: 600;
    color: #252533;
    word-break: break-word;
}

/* Sub-kelompok di dalam "Identitas Ibu Hamil" (Data Ibu / Data Suami /
   Administrasi / Riwayat Kehamilan) -- dulu 20 field numpuk jadi satu
   grid rata tanpa pengelompokan sama sekali, jadi berantakan & susah
   dipindai matanya. Label kecil + ikon + jarak antar kelompok yang
   lebih lega di bawah ini yang memisahkannya. */
.identity-subgroup + .identity-subgroup {
    margin-top: 36px;
}

.identity-subgroup-title {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 12px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .6px;
    color: #7571f9;
    padding-bottom: 12px;
    margin-bottom: 18px;
    border-bottom: 1px dashed #ededf4;
}

.identity-subgroup-title i {
    font-size: 14px;
}

/* Tema Quixlab ini Bootstrap 4 -- class ".row g-4" di atas itu gutter
   Bootstrap 5 dan Bootstrap 4 TIDAK mengenalinya sama sekali, jadi
   jaraknya cuma kebentuk ke samping (dari padding bawaan .col-md-*),
   sedangkan baris yang wrap ke bawah (mis. "Nama Ibu" ke "Tanggal
   Lahir") nempel tanpa jarak vertikal. Shim kecil ini nambahin jarak
   vertikalnya saja, khusus di grid identitas ini. */
.identity-subgroup .row.g-4 {
    margin-top: -1.5rem;
}

.identity-subgroup .row.g-4 > * {
    margin-top: 1.5rem;
}


/* =========================================================
   FORM
========================================================= */

.form-label-custom {
    font-size: 13px;
    font-weight: 600;
    color: #454655;
    margin-bottom: 7px;
}

.form-control,
.form-select {
    min-height: 43px;
    border-radius: 10px;
    border: 1px solid #dfdfea;
    font-size: 14px;
    padding: 9px 12px;
    transition: all 0.2s ease;
}

.form-control:focus,
.form-select:focus {
    border-color: #b3aefc;
    box-shadow: 0 0 0 0.18rem rgba(117, 113, 249, 0.10);
}

.input-group .form-control {
    border-radius: 10px 0 0 10px;
}

.input-group-text {
    background: #f3f3f7;
    border-color: #dfdfea;
    color: #6c757d;
    font-size: 13px;
    font-weight: 600;
}


/* =========================================================
   FORM PEMERIKSAAN
========================================================= */

.examination-card {
    border-top: 4px solid #198754;
}

.examination-title-icon {
    width: 38px;
    height: 38px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 10px;
    background: #e9f7ef;
    color: #198754;
    margin-right: 10px;
}


/* =========================================================
   ALERT
========================================================= */

.alert {
    border-radius: 12px;
    border: none;
    padding: 14px 18px;
    font-size: 14px;
}


/* =========================================================
   TABLE
========================================================= */

.table-card {
    overflow: hidden;
}

.table-wrapper {
    overflow-x: auto;
}

.medical-table {
    margin-bottom: 0;
    min-width: 1050px;
}

.medical-table thead th {
    background: #212229;
    color: white;
    font-size: 12px;
    font-weight: 600;
    padding: 13px 12px;
    border: none;
    white-space: nowrap;
}

.medical-table tbody td {
    font-size: 13px;
    color: #454655;
    padding: 13px 12px;
    vertical-align: middle;
    border-color: #ededf4;
}

.medical-table tbody tr {
    transition: all 0.15s ease;
}

.medical-table tbody tr:hover {
    background: #f8f8fc;
}

.table-number {
    width: 45px;
    text-align: center;
    font-weight: 600;
}

.table-registration {
    font-weight: 600;
    color: #212229;
    white-space: nowrap;
}

.result-text {
    max-width: 220px;
    min-width: 180px;
}

.keterangan-text {
    max-width: 220px;
    min-width: 180px;
}


/* =========================================================
   STATUS KOSONG
========================================================= */

.empty-state {
    padding: 45px 20px !important;
    text-align: center;
    color: #8989a0;
}

.empty-state i {
    font-size: 38px;
    display: block;
    margin-bottom: 10px;
    color: #c8c8d5;
}


/* =========================================================
   RESPONSIVE
========================================================= */

@media (max-width: 768px) {

    .page-header {
        padding: 20px;
    }

    .page-title {
        font-size: 21px;
    }

    .page-header .d-flex {
        flex-direction: column;
        align-items: flex-start !important;
        gap: 15px;
    }

    .page-header .d-flex > div:last-child {
        width: 100%;
    }

    .page-header .btn {
        width: 100%;
    }

    .medical-card-body {
        padding: 20px 16px;
    }

    .card-section-header {
        align-items: flex-start;
        gap: 12px;
    }

    .registration-badge {
        font-size: 12px;
    }

}

</style>


<div class="pemeriksaan-page page-content-wrap">


    <!-- =========================================================
         HEADER
    ========================================================== -->

    <div class="page-header">

        <div class="d-flex justify-content-between align-items-center">

            <div>

                <h1 class="page-title">
                    <i class="bi bi-heart-pulse me-2 text-success"></i>
                    Pemeriksaan Ibu Hamil
                </h1>

                <p class="page-subtitle">
                    Kelola data dan riwayat pemeriksaan kehamilan pasien
                </p>

            </div>


            <div class="d-flex gap-2">

                <!-- CETAK PDF -->

                <a href="cetak-pemeriksaan-ibu-hamil.php?id=<?= $id; ?>"
                   target="_blank"
                   class="btn btn-custom btn-pdf">

                    <i class="bi bi-file-earmark-pdf me-1"></i>
                    Cetak PDF

                </a>


                <!-- KEMBALI -->

                <a href="jadwal-kebidanan.php"
                   class="btn btn-custom btn-back">

                    <i class="bi bi-arrow-left me-1"></i>
                    Kembali

                </a>

            </div>

        </div>

    </div>


    <!-- =========================================================
         PESAN SUKSES
    ========================================================== -->

    <?php if (
        isset($_GET['status']) &&
        $_GET['status'] == 'sukses'
    ) : ?>

        <div class="alert alert-success alert-dismissible fade show mb-4"
             role="alert">

            <i class="bi bi-check-circle-fill me-2"></i>

            <strong>Berhasil!</strong>
            Data pemeriksaan berhasil disimpan.

            <button type="button"
                    class="btn-close"
                    data-dismiss="alert">
            </button>

        </div>

    <?php endif; ?>


    <!-- =========================================================
         PESAN ERROR
    ========================================================== -->

    <?php if (isset($error)) : ?>

        <div class="alert alert-danger mb-4">

            <i class="bi bi-exclamation-triangle-fill me-2"></i>

            <?= htmlspecialchars($error); ?>

        </div>

    <?php endif; ?>


    <!-- =========================================================
         IDENTITAS IBU HAMIL
    ========================================================== -->

    <div class="medical-card">

        <div class="medical-card-body">


            <div class="card-section-header">

                <div>

                    <h5 class="card-section-title">

                        <i class="bi bi-person-vcard me-2 text-primary"></i>

                        Identitas Ibu Hamil

                    </h5>

                    <div class="card-section-subtitle">
                        Informasi lengkap identitas pasien
                    </div>

                </div>


                <span class="registration-badge">

                    <i class="bi bi-person-badge me-1"></i>

                    <?= htmlspecialchars($no_registrasi); ?>

                </span>

            </div>


            <!-- =====================================================
                 DATA IBU
            ====================================================== -->

            <div class="identity-subgroup">

                <div class="identity-subgroup-title">
                    <i class="bi bi-person"></i>
                    Data Ibu
                </div>

                <div class="row g-4">

                    <div class="col-md-4">
                        <div class="patient-info">
                            <span class="patient-label">Nama Ibu</span>
                            <div class="patient-value">
                                <?= htmlspecialchars($ibu['nama_ibu']); ?>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="patient-info">
                            <span class="patient-label">NIK</span>
                            <div class="patient-value">
                                <?= htmlspecialchars($ibu['nik']); ?>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="patient-info">
                            <span class="patient-label">Tempat Lahir</span>
                            <div class="patient-value">
                                <?= !empty($ibu['tempat_lahir'])
                                    ? htmlspecialchars($ibu['tempat_lahir'])
                                    : '-';
                                ?>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="patient-info">
                            <span class="patient-label">Tanggal Lahir</span>
                            <div class="patient-value">
                                <?= !empty($ibu['tgl_lahir'])
                                    ? date('d-m-Y', strtotime($ibu['tgl_lahir']))
                                    : '-';
                                ?>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="patient-info">
                            <span class="patient-label">Golongan Darah</span>
                            <div class="patient-value">
                                <?= !empty($ibu['gol_darah'])
                                    ? htmlspecialchars($ibu['gol_darah'])
                                    : '-';
                                ?>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="patient-info">
                            <span class="patient-label">Pendidikan Ibu</span>
                            <div class="patient-value">
                                <?= !empty($ibu['pendidikan_ibu'])
                                    ? htmlspecialchars($ibu['pendidikan_ibu'])
                                    : '-';
                                ?>
                            </div>
                        </div>
                    </div>

                </div>

            </div>


            <!-- =====================================================
                 DATA SUAMI
            ====================================================== -->

            <div class="identity-subgroup">

                <div class="identity-subgroup-title">
                    <i class="bi bi-person-badge"></i>
                    Data Suami
                </div>

                <div class="row g-4">

                    <div class="col-md-3">
                        <div class="patient-info">
                            <span class="patient-label">Nama Suami</span>
                            <div class="patient-value">
                                <?= !empty($ibu['nama_suami'])
                                    ? htmlspecialchars($ibu['nama_suami'])
                                    : '-';
                                ?>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="patient-info">
                            <span class="patient-label">NIK Suami</span>
                            <div class="patient-value">
                                <?= !empty($ibu['nik_suami'])
                                    ? htmlspecialchars($ibu['nik_suami'])
                                    : '-';
                                ?>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="patient-info">
                            <span class="patient-label">Tanggal Lahir Suami</span>
                            <div class="patient-value">
                                <?= !empty($ibu['tgl_lahir_suami'])
                                    ? date('d-m-Y', strtotime($ibu['tgl_lahir_suami']))
                                    : '-';
                                ?>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="patient-info">
                            <span class="patient-label">Pendidikan Suami</span>
                            <div class="patient-value">
                                <?= !empty($ibu['pendidikan_suami'])
                                    ? htmlspecialchars($ibu['pendidikan_suami'])
                                    : '-';
                                ?>
                            </div>
                        </div>
                    </div>

                </div>

            </div>


            <!-- =====================================================
                 ADMINISTRASI
            ====================================================== -->

            <div class="identity-subgroup">

                <div class="identity-subgroup-title">
                    <i class="bi bi-file-earmark-text"></i>
                    Administrasi
                </div>

                <div class="row g-4">

                    <div class="col-md-3">
                        <div class="patient-info">
                            <span class="patient-label">No. Registrasi</span>
                            <div class="patient-value">
                                <?= htmlspecialchars($no_registrasi); ?>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="patient-info">
                            <span class="patient-label">No. KK</span>
                            <div class="patient-value">
                                <?= !empty($ibu['no_kk'])
                                    ? htmlspecialchars($ibu['no_kk'])
                                    : '-';
                                ?>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="patient-info">
                            <span class="patient-label">No. BPJS</span>
                            <div class="patient-value">
                                <?= !empty($ibu['bpjs'])
                                    ? htmlspecialchars($ibu['bpjs'])
                                    : '-';
                                ?>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="patient-info">
                            <span class="patient-label">No. HP</span>
                            <div class="patient-value">
                                <?= !empty($ibu['no_hp'])
                                    ? htmlspecialchars($ibu['no_hp'])
                                    : '-';
                                ?>
                            </div>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="patient-info">
                            <span class="patient-label">Alamat</span>
                            <div class="patient-value">
                                <?= !empty($ibu['alamat'])
                                    ? htmlspecialchars($ibu['alamat'])
                                    : '-';
                                ?>
                            </div>
                        </div>
                    </div>

                </div>

            </div>


            <!-- =====================================================
                 RIWAYAT KEHAMILAN (OBSTETRI)
            ====================================================== -->

            <div class="identity-subgroup">

                <div class="identity-subgroup-title">
                    <i class="bi bi-calendar-heart"></i>
                    Riwayat Kehamilan
                </div>

                <div class="row g-4">

                    <div class="col-md-4">
                        <div class="patient-info">
                            <span class="patient-label">HPHT</span>
                            <div class="patient-value">
                                <?= !empty($ibu['hpht'])
                                    ? date('d-m-Y', strtotime($ibu['hpht']))
                                    : '-';
                                ?>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="patient-info">
                            <span class="patient-label">HPL</span>
                            <div class="patient-value">
                                <?= !empty($ibu['hpl'])
                                    ? date('d-m-Y', strtotime($ibu['hpl']))
                                    : '-';
                                ?>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="patient-info">
                            <span class="patient-label">Gravida (G) / Para (P) / Abortus (A)</span>
                            <div class="patient-value">
                                G<?= htmlspecialchars($ibu['gravida']); ?>
                                P<?= htmlspecialchars($ibu['para']); ?>
                                A<?= htmlspecialchars($ibu['abortus']); ?>
                            </div>
                        </div>
                    </div>

                </div>

            </div>

        </div>

    </div>


    <!-- =========================================================
         FORM PEMERIKSAAN
    ========================================================== -->

    <div class="medical-card examination-card">

        <div class="medical-card-body">


            <div class="card-section-header">

                <div>

                    <h5 class="card-section-title">

                        <span class="examination-title-icon">

                            <i class="bi bi-heart-pulse"></i>

                        </span>

                        Pemeriksaan Kehamilan

                    </h5>

                    <div class="card-section-subtitle">
                        Masukkan hasil pemeriksaan kehamilan pasien
                    </div>

                </div>

            </div>


            <form method="post">

                <div class="row g-4">


                    <!-- TANGGAL -->

                    <div class="col-md-4">

                        <label class="form-label-custom">
                            Tanggal Pemeriksaan
                            <span class="text-danger">*</span>
                        </label>

                        <input
                            type="date"
                            name="tanggal_pemeriksaan"
                            class="form-control"
                            value="<?= date('Y-m-d'); ?>"
                            required
                        >

                    </div>


                    <!-- USIA KEHAMILAN -->

                    <div class="col-md-4">

                        <label class="form-label-custom">
                            Usia Kehamilan
                            <span class="text-danger">*</span>
                        </label>

                        <div class="input-group">

                            <input
                                type="number"
                                name="usia_kehamilan"
                                class="form-control"
                                min="1"
                                max="45"
                                placeholder="Contoh: 20"
                                required
                            >

                            <span class="input-group-text">
                                Minggu
                            </span>

                        </div>

                    </div>


                    <!-- BERAT BADAN -->

                    <div class="col-md-4">

                        <label class="form-label-custom">
                            Berat Badan
                            <span class="text-danger">*</span>
                        </label>

                        <div class="input-group">

                            <input
                                type="number"
                                name="bb"
                                class="form-control"
                                step="0.01"
                                min="0"
                                placeholder="Contoh: 55.5"
                                required
                            >

                            <span class="input-group-text">
                                Kg
                            </span>

                        </div>

                    </div>


                    <!-- TEKANAN DARAH -->

                    <div class="col-md-4">

                        <label class="form-label-custom">
                            Tekanan Darah
                            <span class="text-danger">*</span>
                        </label>

                        <input
                            type="text"
                            name="td"
                            class="form-control"
                            placeholder="Contoh: 120/80"
                            required
                        >

                    </div>


                    <!-- TFU -->

                    <div class="col-md-4">

                        <label class="form-label-custom">
                            Tinggi Fundus Uteri (TFU)
                            <span class="text-danger">*</span>
                        </label>

                        <div class="input-group">

                            <input
                                type="number"
                                name="tfu"
                                class="form-control"
                                step="0.01"
                                min="0"
                                placeholder="Contoh: 20"
                                required
                            >

                            <span class="input-group-text">
                                cm
                            </span>

                        </div>

                    </div>


                    <!-- DJJ -->

                    <div class="col-md-4">

                        <label class="form-label-custom">
                            Denyut Jantung Janin (DJJ)
                            <span class="text-danger">*</span>
                        </label>

                        <div class="input-group">

                            <input
                                type="number"
                                name="djj"
                                class="form-control"
                                min="0"
                                placeholder="Contoh: 140"
                                required
                            >

                            <span class="input-group-text">
                                x/menit
                            </span>

                        </div>

                    </div>


                    <!-- HASIL -->

                    <div class="col-md-6">

                        <label class="form-label-custom">
                            Hasil Pemeriksaan
                            <span class="text-danger">*</span>
                        </label>

                        <textarea
                            name="hasil"
                            class="form-control"
                            rows="3"
                            placeholder="Contoh: Keadaan ibu dan janin baik"
                            required
                        ></textarea>

                    </div>


                    <!-- KETERANGAN -->

                    <div class="col-md-6">

                        <label class="form-label-custom">
                            Keterangan
                        </label>

                        <textarea
                            name="keterangan"
                            class="form-control"
                            rows="3"
                            placeholder="Masukkan keterangan tambahan jika ada"
                        ></textarea>

                    </div>


                    <!-- TINDAKAN / LAYANAN UNTUK TAGIHAN -->

                    <div class="col-md-6">

                        <label class="form-label-custom">
                            Tindakan / Layanan (untuk tagihan)
                        </label>

                        <input
                            type="text"
                            name="tindakan_billing"
                            id="tokenfieldTindakan"
                            class="form-control"
                            placeholder="Pilih tindakan/layanan berbayar..."
                        >

                        <small class="text-muted">
                            Pilih dari daftar tindakan yang sudah ada harganya
                            di menu Obat (Petugas) agar tagihan pasien
                            terhitung otomatis.
                        </small>

                    </div>


                    <!-- OBAT -->

                    <div class="col-md-6">

                        <label class="form-label-custom">
                            Obat (opsional)
                        </label>

                        <input
                            type="text"
                            name="obat_billing"
                            id="tokenfieldObat"
                            class="form-control"
                            placeholder="Ketik nama obat lalu tekan Enter..."
                        >

                        <small class="text-muted">
                            Vitamin/tablet Fe dsb yang diberikan saat
                            pemeriksaan ini (jika ada).
                        </small>

                    </div>


                    <!-- BUTTON -->

                    <div class="col-12">

                        <div class="d-flex justify-content-end">

                            <button
                                type="submit"
                                name="simpan_pemeriksaan"
                                class="btn btn-save"
                            >

                                <i class="bi bi-save2 me-2"></i>

                                Simpan Pemeriksaan

                            </button>

                        </div>

                    </div>

                </div>

            </form>

        </div>

    </div>


    <!-- =========================================================
         RIWAYAT PEMERIKSAAN
    ========================================================== -->

    <div class="medical-card table-card">

        <div class="medical-card-body">


            <div class="card-section-header">

                <div>

                    <h5 class="card-section-title">

                        <i class="bi bi-clock-history me-2 text-primary"></i>

                        Riwayat Pemeriksaan

                    </h5>

                    <div class="card-section-subtitle">
                        Daftar seluruh riwayat pemeriksaan kehamilan pasien
                    </div>

                </div>


                <a
                    href="cetak-pemeriksaan-ibu-hamil.php?id=<?= $id; ?>"
                    target="_blank"
                    class="btn btn-outline-danger btn-sm rounded-3"
                >

                    <i class="bi bi-file-earmark-pdf me-1"></i>

                    Cetak Riwayat

                </a>

            </div>


            <div class="table-wrapper">

                <table class="table medical-table align-middle">

                    <thead>

                        <tr>

                            <th class="table-number">
                                No
                            </th>

                            <th>
                                No. Registrasi
                            </th>

                            <th>
                                Tanggal
                            </th>

                            <th>
                                UK
                            </th>

                            <th>
                                BB
                            </th>

                            <th>
                                TD
                            </th>

                            <th>
                                TFU
                            </th>

                            <th>
                                DJJ
                            </th>

                            <th>
                                Hasil
                            </th>

                            <th>
                                Keterangan
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                    <?php

                    $no = 1;

                    if (
                        $queryPemeriksaan &&
                        mysqli_num_rows($queryPemeriksaan) > 0
                    ) :

                        while (
                            $p = mysqli_fetch_assoc(
                                $queryPemeriksaan
                            )
                        ) :

                    ?>

                        <tr>

                            <td class="table-number">

                                <?= $no++; ?>

                            </td>


                            <td>

                                <span class="table-registration">

                                    <?= htmlspecialchars(
                                        $p['no_registrasi']
                                    ); ?>

                                </span>

                            </td>


                            <td>

                                <?= date(
                                    'd-m-Y',
                                    strtotime(
                                        $p['tanggal_pemeriksaan']
                                    )
                                ); ?>

                            </td>


                            <td>

                                <strong>
                                    <?= htmlspecialchars(
                                        $p['usia_kehamilan']
                                    ); ?>
                                </strong>

                                minggu

                            </td>


                            <td>

                                <strong>
                                    <?= htmlspecialchars(
                                        $p['berat_badan']
                                    ); ?>
                                </strong>

                                Kg

                            </td>


                            <td>

                                <span class="app-chip">

                                    <?= htmlspecialchars(
                                        $p['tekanan_darah']
                                    ); ?>

                                </span>

                            </td>


                            <td>

                                <?= htmlspecialchars(
                                    $p['tfu']
                                ); ?>

                                cm

                            </td>


                            <td>

                                <?= htmlspecialchars(
                                    $p['djj']
                                ); ?>

                                x/menit

                            </td>


                            <td class="result-text">

                                <?= htmlspecialchars(
                                    $p['hasil']
                                ); ?>

                            </td>


                            <td class="keterangan-text">

                                <?= !empty($p['keterangan'])
                                    ? htmlspecialchars($p['keterangan'])
                                    : '-';
                                ?>

                            </td>

                        </tr>


                    <?php

                        endwhile;

                    else :

                    ?>

                        <tr>

                            <td
                                colspan="10"
                                class="empty-state"
                            >

                                <i class="bi bi-clipboard-x"></i>

                                <strong class="d-block mb-1">
                                    Belum Ada Data Pemeriksaan
                                </strong>

                                <span>
                                    Riwayat pemeriksaan pasien belum tersedia.
                                </span>

                            </td>

                        </tr>

                    <?php endif; ?>

                    </tbody>

                </table>

            </div>

        </div>

    </div>


</div>


<!-- ==========================================================
     TOKENFIELD (TINDAKAN & OBAT UNTUK TAGIHAN)
=========================================================== -->

<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-tokenfield/0.12.0/bootstrap-tokenfield.js"></script>

<script>

$(document).ready(function () {

    $('#tokenfieldTindakan').tokenfield({

        autocomplete: {
            source: <?= json_encode($nmTindakan); ?>,
            delay: 100
        },

        showAutocompleteOnFocus: true

    });

    $('#tokenfieldObat').tokenfield({

        autocomplete: {
            source: <?= json_encode($nmObat); ?>,
            delay: 100
        },

        showAutocompleteOnFocus: true

    });

});

</script>


<?php
require "../template/footer.php";
?>