<?php

session_start();

require "../template/rbac.php";

// Hanya Bidan
cekAkses([ROLE_BIDAN]);

require "../config.php";

$title = "Edit Pelayanan KB - Rekam Medis Puskesmas";


/*
|--------------------------------------------------------------------------
| CEK ID PELAYANAN
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

$id_pelayanan_kb = mysqli_real_escape_string(
    $koneksi,
    $_GET['id']
);


/*
|--------------------------------------------------------------------------
| AMBIL DATA PELAYANAN
|--------------------------------------------------------------------------
*/

$queryData = mysqli_query(
    $koneksi,
    "SELECT
        p.*,

        k.no_peserta_kb,
        k.no_kk,
        k.tanggal_lahir,
        k.nama_suami,
        k.jumlah_anak,
        k.alamat,
        k.jenis_kb,
        k.kunjungan

     FROM tbl_pelayanan_kb p

     INNER JOIN tbl_kb k
        ON p.id_kb = k.id_kb

     WHERE p.id_pelayanan_kb = '$id_pelayanan_kb'

     LIMIT 1"
);


if (!$queryData) {

    die(
        "Query data pelayanan KB gagal: " .
        mysqli_error($koneksi)
    );

}


if (mysqli_num_rows($queryData) == 0) {

    echo "
    <script>
        alert('Data pelayanan KB tidak ditemukan.');
        window.location='pelayanan-kb.php';
    </script>
    ";

    exit();
}


$data = mysqli_fetch_assoc($queryData);


/*
|--------------------------------------------------------------------------
| PROSES UPDATE
|--------------------------------------------------------------------------
*/

if (isset($_POST['update'])) {

    $id_kb = mysqli_real_escape_string(
        $koneksi,
        $_POST['id_kb'] ?? ''
    );

    $tanggal_pelayanan = mysqli_real_escape_string(
        $koneksi,
        $_POST['tanggal_pelayanan'] ?? ''
    );

    $metode_kb = mysqli_real_escape_string(
        $koneksi,
        $_POST['metode_kb'] ?? ''
    );

    $keluhan = mysqli_real_escape_string(
        $koneksi,
        $_POST['keluhan'] ?? ''
    );

    $berat_badan = mysqli_real_escape_string(
        $koneksi,
        $_POST['berat_badan'] ?? ''
    );

    $tinggi_badan = mysqli_real_escape_string(
        $koneksi,
        $_POST['tinggi_badan'] ?? ''
    );

    $tekanan_darah = mysqli_real_escape_string(
        $koneksi,
        $_POST['tekanan_darah'] ?? ''
    );

    $hasil_pemeriksaan = mysqli_real_escape_string(
        $koneksi,
        $_POST['hasil_pemeriksaan'] ?? ''
    );

    $efek_samping = mysqli_real_escape_string(
        $koneksi,
        $_POST['efek_samping'] ?? ''
    );

    $keterangan = mysqli_real_escape_string(
        $koneksi,
        $_POST['keterangan'] ?? ''
    );


    /*
    |--------------------------------------------------------------------------
    | VALIDASI
    |--------------------------------------------------------------------------
    */

    if (
        empty($id_kb) ||
        empty($tanggal_pelayanan) ||
        empty($metode_kb)
    ) {

        echo "
        <script>
            alert('Peserta KB, tanggal pelayanan, dan metode KB wajib diisi.');
            window.history.back();
        </script>
        ";

        exit();
    }


    /*
    |--------------------------------------------------------------------------
    | CEK PESERTA KB
    |--------------------------------------------------------------------------
    */

    $cekPeserta = mysqli_query(
        $koneksi,
        "SELECT id_kb
         FROM tbl_kb
         WHERE id_kb = '$id_kb'
         LIMIT 1"
    );


    if (!$cekPeserta) {

        die(
            "Query pengecekan peserta KB gagal: " .
            mysqli_error($koneksi)
        );

    }


    if (mysqli_num_rows($cekPeserta) == 0) {

        echo "
        <script>
            alert('Peserta KB tidak ditemukan pada Register KB.');
            window.history.back();
        </script>
        ";

        exit();
    }


    /*
    |--------------------------------------------------------------------------
    | UPDATE DATA
    |--------------------------------------------------------------------------
    */

    $queryUpdate = mysqli_query(
        $koneksi,
        "UPDATE tbl_pelayanan_kb SET

            id_kb = '$id_kb',
            tanggal_pelayanan = '$tanggal_pelayanan',
            metode_kb = '$metode_kb',
            keluhan = '$keluhan',
            berat_badan = '$berat_badan',
            tinggi_badan = '$tinggi_badan',
            tekanan_darah = '$tekanan_darah',
            hasil_pemeriksaan = '$hasil_pemeriksaan',
            efek_samping = '$efek_samping',
            keterangan = '$keterangan'

         WHERE id_pelayanan_kb = '$id_pelayanan_kb'"
    );


    /*
    |--------------------------------------------------------------------------
    | HASIL UPDATE
    |--------------------------------------------------------------------------
    */

    if ($queryUpdate) {

        echo "
        <script>
            alert('Data pelayanan KB berhasil diperbarui.');
            window.location='pelayanan-kb.php';
        </script>
        ";

        exit();

    } else {

        echo "
        <script>
            alert('Data pelayanan KB gagal diperbarui: " .
            addslashes(mysqli_error($koneksi)) .
            "');
            window.history.back();
        </script>
        ";

        exit();
    }
}


/*
|--------------------------------------------------------------------------
| AMBIL SEMUA PESERTA KB
|--------------------------------------------------------------------------
*/

$queryPesertaKB = mysqli_query(
    $koneksi,
    "SELECT *
     FROM tbl_kb
     ORDER BY id_kb DESC"
);


if (!$queryPesertaKB) {

    die(
        "Query peserta KB gagal: " .
        mysqli_error($koneksi)
    );

}


require "../template/header.php";
require "../template/navbar.php";
require "../template/sidebar.php";

?>


<style>

/* =========================================================
   GLOBAL
========================================================= */

body {
    background-color: #f5f5fa;
}

main {
    padding-bottom: 40px;
}


/* =========================================================
   HEADER HALAMAN
========================================================= */

.page-header {
    background: #ffffff;
    border-radius: 16px;
    padding: 22px 25px;
    margin-top: 20px;
    margin-bottom: 25px;
    border: 1px solid #e9e9ef;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.04);
}

.page-title {
    font-weight: 700;
    color: #212229;
    margin-bottom: 5px;
}

.page-subtitle {
    color: #6c757d;
    margin-bottom: 0;
    font-size: 14px;
}

.page-icon {
    width: 50px;
    height: 50px;
    border-radius: 13px;
    background: #212229;
    color: #ffffff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 23px;
}


/* =========================================================
   CARD FORM
========================================================= */

.form-card {
    background: #ffffff;
    border: 1px solid #e9e9ef;
    border-radius: 16px;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
    overflow: hidden;
}

.form-card-body {
    padding: 30px;
}


/* =========================================================
   JUDUL SECTION
========================================================= */

.section-title {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 24px;
    padding-bottom: 13px;
    border-bottom: 1px solid #e9e9ef;
}

.section-icon {
    width: 38px;
    height: 38px;
    border-radius: 10px;
    background: #212229;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.section-title h5 {
    margin: 0;
    font-weight: 700;
    color: #212229;
}

.section-title small {
    display: block;
    margin-top: 2px;
    color: #6c757d;
}


/* =========================================================
   LABEL
========================================================= */

.form-label {
    font-size: 14px;
    font-weight: 600;
    color: #343540;
    margin-bottom: 8px;
}

.required {
    color: #dc3545;
}


/* =========================================================
   INPUT
========================================================= */

.form-control,
.form-select {
    min-height: 44px;
    border-radius: 10px;
    border: 1px solid #dedfe6;
    padding: 10px 13px;
    font-size: 14px;
    transition: all 0.2s ease;
    background-color: #ffffff;
}

.form-control:focus,
.form-select:focus {
    border-color: #494a57;
    box-shadow: 0 0 0 3px rgba(33, 37, 41, 0.08);
}

textarea.form-control {
    min-height: 110px;
    resize: vertical;
}

.input-group .form-control {
    border-radius: 10px 0 0 10px;
}

.input-group-text {
    border: 1px solid #dedfe6;
    background-color: #f8f8fa;
    color: #6c757d;
    font-size: 13px;
    font-weight: 500;
}


/* =========================================================
   INFO PESERTA
========================================================= */

.info-peserta {
    background: linear-gradient(
        135deg,
        #f8f8fa 0%,
        #ffffff 100%
    );

    border: 1px solid #dedfe6;
    border-radius: 14px;
    padding: 20px;
    margin-top: 4px;
    position: relative;
    overflow: hidden;
}

.info-peserta::before {
    content: "";
    position: absolute;
    left: 0;
    top: 0;
    bottom: 0;
    width: 4px;
    background: #212229;
}

.info-item {
    padding: 10px 14px;
    background: #ffffff;
    border: 1px solid #eeeeee;
    border-radius: 10px;
    height: 100%;
}

.info-label {
    display: block;
    font-size: 12px;
    color: #6c757d;
    margin-bottom: 5px;
}

.info-value {
    font-size: 14px;
    font-weight: 700;
    color: #212229;
    word-break: break-word;
}


/* =========================================================
   BADGE
========================================================= */

.badge-kb {
    background-color: #212229;
    color: white;
    padding: 7px 11px;
    border-radius: 7px;
    font-size: 12px;
}


/* =========================================================
   FORM DIVIDER
========================================================= */

.form-section {
    margin-top: 35px;
}


/* =========================================================
   FOOTER BUTTON
========================================================= */

.form-actions {
    display: flex;
    justify-content: flex-end;
    align-items: center;
    gap: 10px;
    margin-top: 30px;
    padding-top: 22px;
    border-top: 1px solid #e9e9ef;
}

.btn-custom {
    min-height: 43px;
    border-radius: 9px;
    padding: 9px 18px;
    font-size: 14px;
    font-weight: 600;
    transition: all 0.2s ease;
}

.btn-custom:hover {
    transform: translateY(-1px);
}

.btn-save {
    background-color: #212229;
    color: #ffffff;
    border: 1px solid #212229;
}

.btn-save:hover {
    background-color: #000000;
    color: #ffffff;
}

.btn-cancel {
    background-color: #ffffff;
    color: #494a57;
    border: 1px solid #cecfda;
}

.btn-cancel:hover {
    background-color: #f1f1f5;
    color: #212229;
}


/* =========================================================
   RESPONSIVE
========================================================= */

@media (max-width: 768px) {

    .page-header {
        padding: 18px;
        margin-top: 15px;
    }

    .form-card-body {
        padding: 20px;
    }

    .page-icon {
        width: 43px;
        height: 43px;
        font-size: 19px;
    }

    .page-title {
        font-size: 22px;
    }

    .form-actions {
        flex-direction: column-reverse;
        align-items: stretch;
    }

    .form-actions .btn {
        width: 100%;
    }

}

</style>


<div class="page-content-wrap">


    <!-- =====================================================
         HEADER
    ====================================================== -->

    <div class="page-header">

        <div class="d-flex align-items-center gap-3">

            <div class="page-icon">
                <i class="bi bi-pencil-square"></i>
            </div>

            <div>

                <h1 class="page-title h3">
                    Edit Pelayanan KB
                </h1>

                <p class="page-subtitle">
                    Ubah data pemeriksaan dan pelayanan peserta KB
                </p>

            </div>

        </div>

    </div>


    <!-- =====================================================
         CARD FORM
    ====================================================== -->

    <div class="form-card">

        <div class="form-card-body">


            <form method="POST">


                <!-- =================================================
                     DATA PELAYANAN
                ================================================== -->

                <div class="section-title">

                    <div class="section-icon">
                        <i class="bi bi-person-vcard"></i>
                    </div>

                    <div>

                        <h5>
                            Data Pelayanan
                        </h5>

                        <small>
                            Informasi dasar pelayanan peserta KB
                        </small>

                    </div>

                </div>


                <div class="row g-4">


                    <!-- TANGGAL -->

                    <div class="col-md-6">

                        <label class="form-label">
                            Tanggal Pelayanan
                            <span class="required">*</span>
                        </label>

                        <input
                            type="date"
                            name="tanggal_pelayanan"
                            class="form-control"
                            value="<?= htmlspecialchars(
                                $data['tanggal_pelayanan']
                            ); ?>"
                            required>

                    </div>


                    <!-- PESERTA -->

                    <div class="col-md-6">

                        <label class="form-label">
                            Peserta KB
                            <span class="required">*</span>
                        </label>

                        <select
                            name="id_kb"
                            id="id_kb"
                            class="form-select"
                            required>

                            <option value="">
                                Pilih Peserta KB
                            </option>


                            <?php

                            while (
                                $peserta =
                                mysqli_fetch_assoc(
                                    $queryPesertaKB
                                )
                            ) {

                                $selected =
                                    ($peserta['id_kb'] ==
                                     $data['id_kb'])
                                    ? 'selected'
                                    : '';

                            ?>

                                <option
                                    value="<?= $peserta['id_kb']; ?>"
                                    <?= $selected; ?>

                                    data-kk="<?= htmlspecialchars(
                                        $peserta['no_kk']
                                    ); ?>"

                                    data-suami="<?= htmlspecialchars(
                                        $peserta['nama_suami']
                                    ); ?>"

                                    data-anak="<?= htmlspecialchars(
                                        $peserta['jumlah_anak']
                                    ); ?>"

                                    data-jenis="<?= htmlspecialchars(
                                        $peserta['jenis_kb']
                                    ); ?>">

                                    <?= htmlspecialchars(
                                        $peserta['no_peserta_kb']
                                    ); ?>

                                    -

                                    <?= htmlspecialchars(
                                        $peserta['nama_suami'] ?: '-'
                                    ); ?>

                                    (KK:
                                    <?= htmlspecialchars(
                                        $peserta['no_kk']
                                    ); ?>)

                                </option>

                            <?php

                            }

                            ?>

                        </select>

                    </div>


                    <!-- INFO PESERTA -->

                    <div class="col-12">

                        <div
                            id="infoPesertaKB"
                            class="info-peserta">

                            <div class="row g-3">


                                <!-- NO KK -->

                                <div class="col-md-3">

                                    <div class="info-item">

                                        <span class="info-label">
                                            <i class="bi bi-card-text me-1"></i>
                                            No. KK
                                        </span>

                                        <div
                                            id="infoKK"
                                            class="info-value">

                                            -

                                        </div>

                                    </div>

                                </div>


                                <!-- SUAMI -->

                                <div class="col-md-3">

                                    <div class="info-item">

                                        <span class="info-label">
                                            <i class="bi bi-person me-1"></i>
                                            Nama Suami
                                        </span>

                                        <div
                                            id="infoSuami"
                                            class="info-value">

                                            -

                                        </div>

                                    </div>

                                </div>


                                <!-- ANAK -->

                                <div class="col-md-3">

                                    <div class="info-item">

                                        <span class="info-label">
                                            <i class="bi bi-people me-1"></i>
                                            Jumlah Anak
                                        </span>

                                        <div
                                            id="infoAnak"
                                            class="info-value">

                                            -

                                        </div>

                                    </div>

                                </div>


                                <!-- JENIS KB -->

                                <div class="col-md-3">

                                    <div class="info-item">

                                        <span class="info-label">
                                            <i class="bi bi-heart-pulse me-1"></i>
                                            Jenis KB Register
                                        </span>

                                        <div
                                            id="infoJenisKB"
                                            class="info-value">

                                            -

                                        </div>

                                    </div>

                                </div>


                            </div>

                        </div>

                    </div>


                    <!-- METODE KB -->

                    <div class="col-md-6">

                        <label class="form-label">
                            Metode / Alat Kontrasepsi
                            <span class="required">*</span>
                        </label>

                        <select
                            name="metode_kb"
                            class="form-select"
                            required>

                            <option value="">
                                Pilih Metode KB
                            </option>

                            <?php

                            $metodeList = [
                                'Suntik 1 Bulan',
                                'Suntik 3 Bulan',
                                'Tablet / Pil',
                                'Implant',
                                'IUD',
                                'MOW',
                                'MOP'
                            ];

                            foreach (
                                $metodeList
                                as $metode
                            ) {

                                $selected =
                                    ($data['metode_kb'] ==
                                     $metode)
                                    ? 'selected'
                                    : '';

                            ?>

                                <option
                                    value="<?= htmlspecialchars(
                                        $metode
                                    ); ?>"
                                    <?= $selected; ?>>

                                    <?= htmlspecialchars(
                                        $metode
                                    ); ?>

                                </option>

                            <?php

                            }

                            ?>

                        </select>

                    </div>


                    <!-- KELUHAN -->

                    <div class="col-md-6">

                        <label class="form-label">
                            Keluhan
                        </label>

                        <input
                            type="text"
                            name="keluhan"
                            class="form-control"
                            value="<?= htmlspecialchars(
                                $data['keluhan'] ?? ''
                            ); ?>"
                            placeholder="Masukkan keluhan">

                    </div>

                </div>


                <!-- =================================================
                     HASIL PEMERIKSAAN
                ================================================== -->

                <div class="form-section">

                    <div class="section-title">

                        <div class="section-icon">
                            <i class="bi bi-clipboard2-pulse"></i>
                        </div>

                        <div>

                            <h5>
                                Hasil Pemeriksaan
                            </h5>

                            <small>
                                Data hasil pemeriksaan kesehatan peserta KB
                            </small>

                        </div>

                    </div>


                    <div class="row g-4">


                        <!-- BERAT BADAN -->

                        <div class="col-md-4">

                            <label class="form-label">
                                Berat Badan
                            </label>

                            <div class="input-group">

                                <input
                                    type="number"
                                    name="berat_badan"
                                    step="0.1"
                                    min="0"
                                    class="form-control"
                                    value="<?= htmlspecialchars(
                                        $data['berat_badan'] ?? ''
                                    ); ?>"
                                    placeholder="Masukkan berat badan">

                                <span class="input-group-text">
                                    Kg
                                </span>

                            </div>

                        </div>


                        <!-- TINGGI BADAN -->

                        <div class="col-md-4">

                            <label class="form-label">
                                Tinggi Badan
                            </label>

                            <div class="input-group">

                                <input
                                    type="number"
                                    name="tinggi_badan"
                                    step="0.1"
                                    min="0"
                                    class="form-control"
                                    value="<?= htmlspecialchars(
                                        $data['tinggi_badan'] ?? ''
                                    ); ?>"
                                    placeholder="Masukkan tinggi badan">

                                <span class="input-group-text">
                                    cm
                                </span>

                            </div>

                        </div>


                        <!-- TEKANAN DARAH -->

                        <div class="col-md-4">

                            <label class="form-label">
                                Tekanan Darah
                            </label>

                            <div class="input-group">

                                <input
                                    type="text"
                                    name="tekanan_darah"
                                    class="form-control"
                                    value="<?= htmlspecialchars(
                                        $data['tekanan_darah'] ?? ''
                                    ); ?>"
                                    placeholder="120/80">

                                <span class="input-group-text">
                                    mmHg
                                </span>

                            </div>

                        </div>


                        <!-- HASIL -->

                        <div class="col-md-6">

                            <label class="form-label">
                                Hasil Pemeriksaan
                            </label>

                            <textarea
                                name="hasil_pemeriksaan"
                                class="form-control"
                                rows="4"
                                placeholder="Masukkan hasil pemeriksaan"><?= htmlspecialchars(
                                    $data['hasil_pemeriksaan'] ?? ''
                                ); ?></textarea>

                        </div>


                        <!-- EFEK SAMPING -->

                        <div class="col-md-6">

                            <label class="form-label">
                                Efek Samping
                            </label>

                            <textarea
                                name="efek_samping"
                                class="form-control"
                                rows="4"
                                placeholder="Masukkan efek samping"><?= htmlspecialchars(
                                    $data['efek_samping'] ?? ''
                                ); ?></textarea>

                        </div>


                        <!-- KETERANGAN -->

                        <div class="col-12">

                            <label class="form-label">
                                Keterangan
                            </label>

                            <textarea
                                name="keterangan"
                                class="form-control"
                                rows="4"
                                placeholder="Masukkan keterangan"><?= htmlspecialchars(
                                    $data['keterangan'] ?? ''
                                ); ?></textarea>

                        </div>

                    </div>

                </div>


                <!-- =================================================
                     TOMBOL
                ================================================== -->

                <div class="form-actions">

                    <a
                        href="pelayanan-kb.php"
                        class="btn btn-cancel btn-custom">

                        <i class="bi bi-arrow-left me-1"></i>

                        Kembali

                    </a>


                    <button
                        type="submit"
                        name="update"
                        value="1"
                        class="btn btn-save btn-custom">

                        <i class="bi bi-check2-circle me-1"></i>

                        Simpan Perubahan

                    </button>

                </div>


            </form>

        </div>

    </div>

</div>


<!-- =========================================================
     SCRIPT INFORMASI PESERTA
========================================================= -->

<script>

function tampilkanInfoPeserta() {

    const select =
        document.getElementById("id_kb");

    const option =
        select.options[
            select.selectedIndex
        ];


    if (!select.value) {

        document.getElementById(
            "infoKK"
        ).textContent = "-";

        document.getElementById(
            "infoSuami"
        ).textContent = "-";

        document.getElementById(
            "infoAnak"
        ).textContent = "-";

        document.getElementById(
            "infoJenisKB"
        ).textContent = "-";

        return;
    }


    document.getElementById(
        "infoKK"
    ).textContent =
        option.dataset.kk || "-";


    document.getElementById(
        "infoSuami"
    ).textContent =
        option.dataset.suami || "-";


    document.getElementById(
        "infoAnak"
    ).textContent =
        option.dataset.anak || "0";


    document.getElementById(
        "infoJenisKB"
    ).textContent =
        option.dataset.jenis || "-";
}


/*
|--------------------------------------------------------------------------
| SAAT PESERTA DIGANTI
|--------------------------------------------------------------------------
*/

document
    .getElementById("id_kb")
    .addEventListener(
        "change",
        tampilkanInfoPeserta
    );


/*
|--------------------------------------------------------------------------
| TAMPILKAN DATA SAAT HALAMAN DIBUKA
|--------------------------------------------------------------------------
*/

document.addEventListener(
    "DOMContentLoaded",
    function () {

        tampilkanInfoPeserta();

    }
);

</script>


<?php

require "../template/footer.php";

?>