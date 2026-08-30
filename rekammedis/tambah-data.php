<?php

session_start();

require "../template/rbac.php";

cekAkses([ROLE_ADMIN, ROLE_PETUGAS, ROLE_DOKTER, ROLE_BIDAN]);

require "../config.php";

$title = "Tambah Data - Rekam Medis Puskesmas";

require "../template/header.php";
require "../template/navbar.php";
require "../template/sidebar.php";


/* =========================================================
   ALERT
========================================================= */

$msg = $_GET['msg'] ?? '';

$alert = "";

if ($msg == 'added') {

    $alert = '
    <div class="alert alert-success alert-dismissible fade show custom-alert" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i>
        <strong>Tambah Data Rekam Medis Berhasil.</strong>
        <button type="button" class="btn-close" data-dismiss="alert"></button>
    </div>';

}


/* =========================================================
   DATA OBAT
   HANYA OBAT DENGAN STOK TERSEDIA
========================================================= */

$nmObat = [];

$queryObat = mysqli_query(
    $koneksi,
    "SELECT *
     FROM tbl_obat
     WHERE kategori = 'Obat'
     AND stok > 0
     ORDER BY nama ASC"
);

if ($queryObat) {

    while ($dataObat = mysqli_fetch_assoc($queryObat)) {

        $nmObat[] = $dataObat['nama'];

    }

}


/* =========================================================
   DATA DARI ANTRIAN
========================================================= */

$dariAntrian = null;

if (isset($_GET['antrian_id']) && !empty($_GET['antrian_id'])) {

    $antrianId = mysqli_real_escape_string(
        $koneksi,
        $_GET['antrian_id']
    );

    $queryAntrian = mysqli_query(
        $koneksi,
        "
        SELECT
            a.*,
            p.nama,
            p.alamat
        FROM tbl_antrian a
        INNER JOIN tbl_pasien p
            ON a.id_pasien = p.id
        WHERE a.id = '$antrianId'
        "
    );

    if (
        $queryAntrian &&
        mysqli_num_rows($queryAntrian) > 0
    ) {

        $dariAntrian = mysqli_fetch_assoc(
            $queryAntrian
        );

    }

}


/* =========================================================
   DOKTER / BIDAN WAJIB MELALUI ANTRIAN
========================================================= */

if (
    !$dariAntrian &&
    !userHasAnyRole([
        ROLE_ADMIN,
        ROLE_PETUGAS
    ])
) {

    echo "
    <script>
        alert(
            'Pasien harus didaftarkan oleh Petugas ke antrian hari ini terlebih dahulu. Silakan periksa pasien melalui tombol Periksa di daftar antrian.'
        );

        window.location='" . $main_url . "rekammedis';
    </script>";

    exit();

}


/* =========================================================
   IDENTITAS DOKTER LOGIN
========================================================= */

$isDokterLogin = userHasRole(
    ROLE_DOKTER
);


/* =========================================================
   POLI OTOMATIS DARI ANTRIAN
========================================================= */

$poliOtomatis = '';

if ($dariAntrian) {

    $poliOtomatis =
        ($dariAntrian['jenis_layanan'] === 'Kebidanan')
        ? 'Poli Kebidanan'
        : 'Poli Umum';

}

?>

<style>

/* =========================================================
   HALAMAN UTAMA
========================================================= */

.main-rekammedis {
    padding-top: 25px;
    padding-bottom: 50px;
}


/* =========================================================
   HEADER
========================================================= */

.page-header-rekammedis {
    background: #ffffff;
    border-radius: 16px;
    padding: 22px 25px;
    margin-bottom: 25px;
    border: 1px solid #e8e8f3;
    box-shadow: 0 4px 15px rgba(0,0,0,.04);
}

.page-header-rekammedis h1 {
    margin: 0;
    font-size: 27px;
    font-weight: 700;
    color: #262a38;
}

.page-header-rekammedis h1 i {
    color: #7571f9;
}

.page-header-rekammedis .back-btn {
    text-decoration: none;
    font-weight: 600;
    color: #7571f9;
    padding: 9px 15px;
    border-radius: 9px;
    transition: .2s;
}

.page-header-rekammedis .back-btn:hover {
    background: #eeeeff;
}


/* =========================================================
   CARD FORM
========================================================= */

.form-card-rekammedis {
    background: #ffffff;
    border: 1px solid #e8e8f3;
    border-radius: 18px;
    box-shadow: 0 6px 25px rgba(0,0,0,.05);
    overflow: hidden;
}

.form-card-header {
    background: linear-gradient(
        135deg,
        #7571f9,
        #5f5ae0
    );

    color: white;
    padding: 18px 24px;
}

.form-card-header h5 {
    margin: 0;
    font-weight: 600;
}

.form-card-body {
    padding: 28px;
}


/* =========================================================
   LABEL
========================================================= */

.form-label {
    font-weight: 600;
    color: #373d4f;
    margin-bottom: 7px;
}


/* =========================================================
   INPUT
========================================================= */

.form-control,
.form-select {
    min-height: 44px;
    border-radius: 9px;
    border: 1px solid #dcdceb;
    transition: .2s;
}

.form-control:focus,
.form-select:focus {
    border-color: #7571f9;
    box-shadow: 0 0 0 3px rgba(
        117,
        113,
        249,
        .10
    );
}


/* =========================================================
   TEXTAREA
========================================================= */

textarea.form-control {
    min-height: 90px;
    resize: vertical;
}


/* =========================================================
   PASIEN BOX
========================================================= */

.patient-box {
    background: #f8f8ff;
    border: 1px solid #dcddff;
    border-radius: 13px;
    padding: 18px;
    margin-bottom: 20px;
}

.patient-title {
    font-weight: 700;
    color: #7571f9;
    margin-bottom: 15px;
}

.patient-search-input {
    background: white;
}

.patient-info {
    margin-top: 12px;
}


/* =========================================================
   BUTTON SEARCH
========================================================= */

.btn-search-pasien {
    min-width: 52px;
    border-radius: 0 9px 9px 0 !important;
}

.btn-search-pasien i {
    font-size: 18px;
}


/* =========================================================
   KOLOM KANAN
========================================================= */

.right-form-column {
    border-left: 1px solid #e9e9ef;
    padding-left: 28px;
}


/* =========================================================
   BUTTON FORM
========================================================= */

.form-action {
    border-top: 1px solid #ededf3;
    margin-top: 25px;
    padding-top: 20px;
}

.form-action .btn {
    border-radius: 9px;
    padding: 9px 20px;
    font-weight: 600;
}


/* =========================================================
   ALERT
========================================================= */

.custom-alert {
    border-radius: 10px;
    border: none;
}


/* =========================================================
   MODAL PASIEN
========================================================= */

#modalPasien .modal-content {
    border: none;
    border-radius: 18px;
    overflow: hidden;
    box-shadow: 0 15px 50px rgba(0,0,0,.20);
}

#modalPasien .modal-header {
    padding: 20px 25px;
    background: #ffffff;
}

#modalPasien .modal-title {
    font-size: 23px;
    font-weight: 700;
    color: #262a38;
}

#modalPasien .modal-title i {
    color: #7571f9;
}


/* =========================================================
   SEARCH MODAL
========================================================= */

.search-pasien-wrapper {
    position: relative;
}

.search-pasien-wrapper .search-icon {
    position: absolute;
    left: 16px;
    top: 50%;
    transform: translateY(-50%);
    color: #7571f9;
    font-size: 18px;
    z-index: 5;
}

#searchPasien {
    padding-left: 47px;
    padding-right: 50px;
    min-height: 50px;
    border-radius: 10px !important;
}

#resetSearchPasien {
    position: absolute;
    right: 7px;
    top: 6px;
    z-index: 5;
    border: none;
    background: transparent;
    color: #6c757d;
    width: 38px;
    height: 38px;
    border-radius: 8px;
}

#resetSearchPasien:hover {
    background: #f1f1f5;
    color: #dc3545;
}


/* =========================================================
   TABEL PASIEN
========================================================= */

#tabelPasien {
    margin-bottom: 0;
}

#tabelPasien thead th {
    background: #f1f1fc;
    color: #455a64;
    font-size: 14px;
    font-weight: 700;
    border-bottom: 1px solid #dcdceb;
    padding: 13px 12px;
    white-space: nowrap;
}

#tabelPasien tbody td {
    padding: 13px 12px;
    border-color: #ededf3;
    vertical-align: middle;
}

#tabelPasien tbody tr {
    transition: .15s;
}

#tabelPasien tbody tr:hover {
    background: #f7f8ff;
}


/* =========================================================
   BADGE RM
========================================================= */

.badge-rm {
    background: #eeeeff;
    color: #7571f9;
    border: 1px solid #d1cfff;
    font-weight: 600;
    padding: 7px 9px;
    border-radius: 7px;
}


/* =========================================================
   BUTTON PILIH
========================================================= */

.btn-pilih-pasien {
    width: 42px;
    height: 42px;
    border-radius: 9px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}

.btn-pilih-pasien i {
    font-size: 18px;
}


/* =========================================================
   EMPTY SEARCH
========================================================= */

#pasienTidakDitemukan {
    padding: 45px 20px;
}

#pasienTidakDitemukan i {
    font-size: 45px;
    color: #adaebd;
}


/* =========================================================
   TOKEN FIELD OBAT
========================================================= */

.tokenfield {
    min-height: 44px;
    border-radius: 9px;
    border: 1px solid #dcdceb;
    padding: 4px 8px;
}

.tokenfield.focus {
    border-color: #7571f9;
    box-shadow: 0 0 0 3px rgba(
        117,
        113,
        249,
        .10
    );
}

.tokenfield .token {
    background: #7571f9;
    color: white;
    border: none;
    border-radius: 6px;
    padding: 5px 9px;
    margin: 3px;
}


/* =========================================================
   RESPONSIVE
========================================================= */

@media (max-width: 991px) {

    .right-form-column {
        border-left: none;
        border-top: 1px solid #e9e9ef;
        padding-left: 12px;
        padding-top: 25px;
        margin-top: 20px;
    }

    .form-card-body {
        padding: 20px;
    }

}


@media (max-width: 576px) {

    .page-header-rekammedis h1 {
        font-size: 21px;
    }

    .page-header-rekammedis {
        padding: 18px;
    }

    .form-card-body {
        padding: 15px;
    }

}

</style>


<div class="main-rekammedis page-content-wrap">


    <!-- =====================================================
         HEADER
    ====================================================== -->

    <div class="page-header-rekammedis">

        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">

            <h1>

                <i class="bi bi-file-earmark-medical me-2"></i>

                Tambah Data Perekaman

            </h1>


            <a href="<?= $main_url ?>rekammedis"
               class="back-btn">

                <i class="bi bi-arrow-left me-1"></i>

                Kembali

            </a>

        </div>

    </div>


    <!-- =====================================================
         ALERT
    ====================================================== -->

    <?php

    if ($msg !== '') {

        echo $alert;

    }

    ?>


    <!-- =====================================================
         FORM CARD
    ====================================================== -->

    <div class="form-card-rekammedis">


        <div class="form-card-header">

            <h5>

                <i class="bi bi-clipboard2-pulse me-2"></i>

                Form Rekam Medis Pasien

            </h5>

        </div>


        <div class="form-card-body">


            <form
                action="proses-data.php"
                method="post"
                onsubmit="return validasiForm();"
            >


                <div class="row">


                    <!-- =================================================
                         KOLOM KIRI
                    ================================================== -->

                    <div class="col-lg-6">


                        <!-- TANGGAL -->

                        <div class="form-group mb-4">

                            <label
                                for="tgl"
                                class="form-label"
                            >

                                <i class="bi bi-calendar3 me-1 text-primary"></i>

                                Tanggal Pemeriksaan

                            </label>


                            <input
                                type="date"
                                name="tgl"
                                class="form-control"
                                id="tgl"
                                value="<?= date('Y-m-d'); ?>"
                                required
                            >

                        </div>


                        <!-- =================================================
                             PASIEN
                        ================================================== -->

                        <div class="patient-box">


                            <div class="patient-title">

                                <i class="bi bi-person-vcard me-1"></i>

                                Data Pasien


                                <?php if ($dariAntrian) { ?>

                                    <span class="app-chip app-chip-info ms-2">

                                        Dari Antrian No.
                                        <?= htmlspecialchars(
                                            $dariAntrian['no_antrian']
                                        ); ?>

                                    </span>

                                <?php } ?>

                            </div>


                            <label
                                for="pasien_id"
                                class="form-label"
                            >

                                Pasien

                            </label>


                            <div class="input-group mb-3">


                                <input
                                    type="text"
                                    class="form-control patient-search-input"
                                    id="pasien_id"
                                    name="id"
                                    placeholder="Pilih pasien..."
                                    value="<?= $dariAntrian
                                        ? htmlspecialchars(
                                            $dariAntrian['id_pasien']
                                        )
                                        : ''; ?>"
                                    readonly
                                    required
                                >


                                <?php if (!$dariAntrian) { ?>

                                    <button
                                        class="btn btn-primary btn-search-pasien"
                                        type="button"
                                        id="cari"
                                        data-toggle="modal"
                                        data-target="#modalPasien"
                                        title="Cari Pasien"
                                    >

                                        <i class="bi bi-search"></i>

                                    </button>

                                <?php } ?>


                            </div>


                            <?php if ($dariAntrian) { ?>

                                <input
                                    type="hidden"
                                    name="id_antrian"
                                    value="<?= htmlspecialchars(
                                        $dariAntrian['id']
                                    ); ?>"
                                >

                            <?php } ?>


                            <!-- NAMA PASIEN -->

                            <div class="patient-info">


                                <label class="form-label">

                                    <i class="bi bi-person me-1"></i>

                                    Nama Pasien

                                </label>


                                <input
                                    type="text"
                                    id="namaPasien"
                                    class="form-control"
                                    placeholder="Nama pasien"
                                    value="<?= $dariAntrian
                                        ? htmlspecialchars(
                                            $dariAntrian['nama']
                                        )
                                        : ''; ?>"
                                    readonly
                                >

                            </div>


                            <!-- ALAMAT -->

                            <div class="patient-info">


                                <label class="form-label">

                                    <i class="bi bi-geo-alt me-1"></i>

                                    Alamat

                                </label>


                                <textarea
                                    id="alamatPasien"
                                    class="form-control"
                                    placeholder="Alamat pasien"
                                    rows="2"
                                    readonly
                                ><?= $dariAntrian
                                    ? htmlspecialchars(
                                        $dariAntrian['alamat']
                                    )
                                    : ''; ?></textarea>

                            </div>


                            <!-- PEMERIKSAAN AWAL DARI PENDAFTARAN -->

                            <?php

                            if (
                                $dariAntrian &&
                                !empty(
                                    $dariAntrian['tekanan_darah']
                                )
                            ) {

                            ?>

                                <div class="patient-info">


                                    <label class="form-label">

                                        <i class="bi bi-clipboard2-pulse me-1"></i>

                                        Hasil Pemeriksaan Awal (Pendaftaran)

                                    </label>


                                    <input
                                        type="text"
                                        class="form-control"
                                        readonly
                                        value="Tensi: <?= htmlspecialchars(
                                            $dariAntrian['tekanan_darah']
                                        ); ?> | BB: <?= htmlspecialchars(
                                            $dariAntrian['berat_badan'] ?? '-'
                                        ); ?> kg<?= !empty(
                                            $dariAntrian['tinggi_badan']
                                        )
                                            ? ' | TB: ' .
                                              htmlspecialchars(
                                                  $dariAntrian['tinggi_badan']
                                              ) .
                                              ' cm'
                                            : ''; ?>"
                                    >

                                </div>

                            <?php } ?>


                        </div>


                        <!-- =================================================
                             KELUHAN
                        ================================================== -->

                        <div class="form-group mb-4">


                            <label
                                for="keluhan"
                                class="form-label"
                            >

                                <i class="bi bi-chat-left-text me-1 text-primary"></i>

                                Keluhan

                            </label>


                            <textarea
                                name="keluhan"
                                id="keluhan"
                                class="form-control"
                                rows="3"
                                placeholder="Masukkan keluhan pasien..."
                            ><?= (
                                $dariAntrian &&
                                !empty($dariAntrian['keluhan'])
                            )
                                ? htmlspecialchars(
                                    $dariAntrian['keluhan']
                                )
                                : ''; ?></textarea>


                        </div>


                        <!-- =================================================
                             ANAMNESA
                        ================================================== -->

                        <div class="form-group mb-4">


                            <label
                                for="anamnesa"
                                class="form-label"
                            >

                                <i class="bi bi-journal-text me-1 text-primary"></i>

                                Anamnesa

                            </label>


                            <textarea
                                name="anamnesa"
                                id="anamnesa"
                                class="form-control"
                                rows="3"
                                placeholder="Masukkan hasil anamnesa..."
                            ></textarea>


                        </div>


                        <!-- =================================================
                             PEMERIKSAAN FISIK
                        ================================================== -->

                        <div class="form-group mb-4">


                            <label
                                for="pemeriksaan_fisik"
                                class="form-label"
                            >

                                <i class="bi bi-heart-pulse me-1 text-primary"></i>

                                Pemeriksaan Fisik

                            </label>


                            <textarea
                                name="pemeriksaan_fisik"
                                id="pemeriksaan_fisik"
                                class="form-control"
                                rows="3"
                                placeholder="Masukkan hasil pemeriksaan fisik..."
                            ></textarea>


                        </div>


                        <!-- =================================================
                             PEMERIKSAAN LAB
                        ================================================== -->

                        <div class="form-group mb-4">


                            <label
                                for="pemeriksaan_lab"
                                class="form-label"
                            >

                                <i class="bi bi-eyedropper me-1 text-primary"></i>

                                Pemeriksaan Laboratorium

                            </label>


                            <textarea
                                name="pemeriksaan_lab"
                                id="pemeriksaan_lab"
                                class="form-control"
                                rows="3"
                                placeholder="Masukkan hasil pemeriksaan laboratorium..."
                            ></textarea>


                        </div>


                    </div>


                    <!-- =================================================
                         KOLOM KANAN
                    ================================================== -->

                    <div class="col-lg-6 right-form-column">


                        <!-- =================================================
                             TINDAKAN
                        ================================================== -->

                        <div class="form-group mb-4">


                            <label
                                for="tindakan"
                                class="form-label"
                            >

                                <i class="bi bi-bandaid me-1 text-primary"></i>

                                Tindakan

                            </label>


                            <textarea
                                name="tindakan"
                                id="tindakan"
                                class="form-control"
                                rows="3"
                                placeholder="Masukkan tindakan yang dilakukan..."
                            ></textarea>


                        </div>


                        <!-- =================================================
                             POLI
                        ================================================== -->

                        <div class="form-group mb-4">


                            <label
                                for="poli"
                                class="form-label"
                            >

                                <i class="bi bi-hospital me-1 text-primary"></i>

                                Poli / Ruangan

                            </label>


                            <?php if ($dariAntrian) { ?>


                                <input
                                    type="text"
                                    class="form-control"
                                    value="<?= htmlspecialchars(
                                        $poliOtomatis
                                    ); ?>"
                                    readonly
                                >


                                <input
                                    type="hidden"
                                    name="poli"
                                    value="<?= htmlspecialchars(
                                        $poliOtomatis
                                    ); ?>"
                                >


                            <?php } else { ?>


                                <input
                                    type="text"
                                    name="poli"
                                    id="poli"
                                    class="form-control"
                                    placeholder="Masukkan poli / ruangan"
                                >


                            <?php } ?>


                        </div>


                        <!-- =================================================
                             RUJUK INTERNAL
                        ================================================== -->

                        <div class="form-group mb-4">


                            <label
                                for="rujuk_internal"
                                class="form-label"
                            >

                                <i class="bi bi-arrow-left-right me-1 text-primary"></i>

                                Rujuk Internal

                            </label>


                            <input
                                type="text"
                                name="rujuk_internal"
                                id="rujuk_internal"
                                class="form-control"
                                placeholder="Masukkan rujukan internal"
                            >


                        </div>


                        <!-- =================================================
                             RUJUK EKSTERNAL
                        ================================================== -->

                        <div class="form-group mb-4">


                            <label
                                for="rujuk_eksternal"
                                class="form-label"
                            >

                                <i class="bi bi-box-arrow-up-right me-1 text-primary"></i>

                                Rujuk Eksternal

                            </label>


                            <input
                                type="text"
                                name="rujuk_eksternal"
                                id="rujuk_eksternal"
                                class="form-control"
                                placeholder="Masukkan rujukan eksternal"
                            >


                        </div>


                        <!-- =================================================
                             DOKTER
                        ================================================== -->

                        <div class="form-group mb-4">


                            <label
                                for="dokter"
                                class="form-label"
                            >

                                <i class="bi bi-person-badge me-1 text-primary"></i>

                                Dokter

                            </label>


                            <?php if ($isDokterLogin) { ?>


                                <input
                                    type="text"
                                    class="form-control"
                                    value="<?= htmlspecialchars(
                                        $dataUser['fullname']
                                    ); ?> (Anda)"
                                    readonly
                                >


                                <input
                                    type="hidden"
                                    name="dokter"
                                    value="<?= htmlspecialchars(
                                        $dataUser['userid']
                                    ); ?>"
                                >


                            <?php } else { ?>


                                <select
                                    name="dokter"
                                    id="dokter"
                                    class="form-select"
                                    required
                                >

                                    <option value="">
                                        -- Pilih Dokter --
                                    </option>


                                    <?php

                                    $queryDokter = mysqli_query(
                                        $koneksi,
                                        "
                                        SELECT *
                                        FROM tbl_user
                                        WHERE jabatan = 3
                                        ORDER BY fullname ASC
                                        "
                                    );


                                    if ($queryDokter) {

                                        while (
                                            $data =
                                            mysqli_fetch_assoc(
                                                $queryDokter
                                            )
                                        ) {

                                    ?>

                                        <option
                                            value="<?= htmlspecialchars(
                                                $data['userid']
                                            ); ?>"
                                        >

                                            <?= htmlspecialchars(
                                                $data['fullname']
                                            ); ?>

                                        </option>

                                    <?php

                                        }

                                    }

                                    ?>

                                </select>


                            <?php } ?>


                        </div>


                        <!-- =================================================
                             DIAGNOSA
                        ================================================== -->

                        <div class="form-group mb-4">


                            <label
                                for="diagnosa"
                                class="form-label"
                            >

                                <i class="bi bi-clipboard2-pulse me-1 text-primary"></i>

                                Diagnosa

                            </label>


                            <textarea
                                name="diagnosa"
                                id="diagnosa"
                                class="form-control"
                                rows="3"
                                placeholder="Hasil diagnosa dokter..."
                            ></textarea>


                        </div>


                        <!-- =================================================
                             OBAT
                        ================================================== -->

                        <div class="form-group mb-4">


                            <label
                                for="tokenfield"
                                class="form-label"
                            >

                                <i class="bi bi-capsule-pill me-1 text-primary"></i>

                                Obat

                            </label>


                            <input
                                type="text"
                                name="obat"
                                class="form-control"
                                id="tokenfield"
                                placeholder="Ketik nama obat lalu tekan Enter"
                            >


                            <small class="text-muted">

                                Pisahkan obat dengan koma atau tekan Enter.
                                Hanya obat dengan stok tersedia yang muncul di saran.

                            </small>


                        </div>


                        <!-- =================================================
                             BUTTON
                        ================================================== -->

                        <div class="form-action">


                            <button
                                type="reset"
                                class="btn btn-outline-danger btn-sm"
                            >

                                <i class="bi bi-x-lg me-1"></i>

                                Reset

                            </button>


                            <button
                                type="submit"
                                name="simpan"
                                class="btn btn-primary btn-sm"
                            >

                                <i class="bi bi-save me-1"></i>

                                Simpan Rekam Medis

                            </button>


                        </div>


                    </div>


                </div>


            </form>

        </div>

    </div>

</div>


<!-- =========================================================
     MODAL CARI PASIEN
========================================================= -->

<div
    class="modal fade"
    id="modalPasien"
    tabindex="-1"
    aria-labelledby="modalPasienLabel"
    aria-hidden="true"
>


    <div class="modal-dialog modal-xl modal-dialog-centered">


        <div class="modal-content">


            <!-- HEADER -->

            <div class="modal-header">


                <h4
                    class="modal-title"
                    id="modalPasienLabel"
                >

                    <i class="bi bi-search me-2"></i>

                    Cari Pasien

                </h4>


                <button
                    type="button"
                    class="btn-close"
                    data-dismiss="modal"
                    aria-label="Close"
                ></button>


            </div>


            <!-- BODY -->

            <div class="modal-body p-4">


                <!-- SEARCH -->

                <div class="mb-4">


                    <label class="form-label">

                        <i class="bi bi-search me-1"></i>

                        Cari Pasien

                    </label>


                    <div class="search-pasien-wrapper">


                        <i class="bi bi-search search-icon"></i>


                        <input
                            type="text"
                            id="searchPasien"
                            class="form-control"
                            placeholder="Cari ID pasien, No RM, nama atau alamat..."
                            autocomplete="off"
                        >


                        <button
                            type="button"
                            id="resetSearchPasien"
                            title="Reset pencarian"
                        >

                            <i class="bi bi-x-lg"></i>

                        </button>


                    </div>


                    <small class="text-muted">

                        Ketik nama pasien, nomor RM, ID pasien, atau alamat.

                    </small>


                </div>


                <!-- TABLE -->

                <div class="table-responsive">


                    <table
                        class="table table-hover align-middle"
                        id="tabelPasien"
                    >


                        <thead>


                            <tr>


                                <th width="7%">
                                    No
                                </th>


                                <th width="20%">
                                    ID Pasien
                                </th>


                                <th width="20%">
                                    No RM
                                </th>


                                <th width="23%">
                                    Nama
                                </th>


                                <th>
                                    Alamat
                                </th>


                                <th
                                    width="9%"
                                    class="text-center"
                                >
                                    Pilih
                                </th>


                            </tr>


                        </thead>


                        <tbody>


                        <?php

                        $no = 1;

                        $queryPasien = mysqli_query(
                            $koneksi,
                            "
                            SELECT *
                            FROM tbl_pasien
                            ORDER BY nama ASC
                            "
                        );


                        if ($queryPasien) {

                            while (
                                $pasien =
                                mysqli_fetch_assoc(
                                    $queryPasien
                                )
                            ) {

                        ?>


                            <tr class="data-pasien">


                                <td>

                                    <?= $no++; ?>

                                </td>


                                <td>

                                    <span class="badge-rm">

                                        <?= htmlspecialchars(
                                            $pasien['id']
                                        ); ?>

                                    </span>

                                </td>


                                <td>

                                    <span class="badge-rm">

                                        <?= htmlspecialchars(
                                            $pasien['no_rm']
                                        ); ?>

                                    </span>

                                </td>


                                <td class="fw-semibold">

                                    <?= htmlspecialchars(
                                        $pasien['nama']
                                    ); ?>

                                </td>


                                <td>

                                    <?= htmlspecialchars(
                                        $pasien['alamat']
                                    ); ?>

                                </td>


                                <td class="text-center">


                                    <button
                                        type="button"
                                        title="Pilih pasien"

                                        data-id="<?= htmlspecialchars(
                                            $pasien['id']
                                        ); ?>"

                                        data-norm="<?= htmlspecialchars(
                                            $pasien['no_rm']
                                        ); ?>"

                                        data-namapasien="<?= htmlspecialchars(
                                            $pasien['nama']
                                        ); ?>"

                                        data-address="<?= htmlspecialchars(
                                            $pasien['alamat']
                                        ); ?>"

                                        class="btn btn-primary btn-sm btn-pilih-pasien cekPasien"
                                    >

                                        <i class="bi bi-check-lg"></i>

                                    </button>


                                </td>


                            </tr>


                        <?php

                            }

                        }

                        ?>


                        </tbody>


                    </table>


                </div>


                <!-- TIDAK DITEMUKAN -->

                <div
                    id="pasienTidakDitemukan"
                    class="text-center d-none"
                >

                    <i class="bi bi-person-x"></i>


                    <h5 class="mt-3">

                        Pasien tidak ditemukan

                    </h5>


                    <p class="text-muted">

                        Silakan coba kata kunci lainnya.

                    </p>


                </div>


            </div>


            <!-- FOOTER -->

            <div class="modal-footer bg-light">


                <button
                    type="button"
                    class="btn btn-outline-secondary"
                    data-dismiss="modal"
                >

                    <i class="bi bi-x-lg me-1"></i>

                    Tutup

                </button>


            </div>


        </div>

    </div>

</div>


<!-- =========================================================
     JQUERY UI
========================================================= -->

<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>


<!-- =========================================================
     TOKENFIELD
========================================================= -->

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-tokenfield/0.12.0/bootstrap-tokenfield.js"></script>


<script>

$(document).ready(function () {


    /* =====================================================
       PILIH PASIEN
    ===================================================== */

    $(document).on(
        'click',
        '.cekPasien',
        function () {

            let pasienID =
                $(this).data('id');

            let pasienName =
                $(this).data('namapasien');

            let pasienAddress =
                $(this).data('address');


            $('#pasien_id')
                .val(pasienID);


            $('#namaPasien')
                .val(pasienName);


            $('#alamatPasien')
                .val(pasienAddress);


            $('#modalPasien')
                .modal('hide');

        }
    );


    /* =====================================================
       SEARCH PASIEN
    ===================================================== */

    $('#searchPasien').on(
        'keyup',
        function () {

            let keyword =
                $(this)
                .val()
                .toLowerCase()
                .trim();


            let jumlahTampil = 0;


            $('#tabelPasien tbody tr.data-pasien')
                .each(
                    function () {

                        let idPasien =
                            $(this)
                            .find('td:eq(1)')
                            .text()
                            .toLowerCase()
                            .trim();


                        let noRM =
                            $(this)
                            .find('.cekPasien')
                            .data('norm')
                            .toString()
                            .toLowerCase();


                        let namaPasien =
                            $(this)
                            .find('td:eq(3)')
                            .text()
                            .toLowerCase()
                            .trim();


                        let alamatPasien =
                            $(this)
                            .find('td:eq(4)')
                            .text()
                            .toLowerCase()
                            .trim();


                        if (

                            idPasien.includes(keyword) ||

                            noRM.includes(keyword) ||

                            namaPasien.includes(keyword) ||

                            alamatPasien.includes(keyword)

                        ) {

                            $(this).show();

                            jumlahTampil++;

                        } else {

                            $(this).hide();

                        }

                    }
                );


            if (
                jumlahTampil === 0 &&
                keyword !== ''
            ) {

                $('#pasienTidakDitemukan')
                    .removeClass('d-none');

            } else {

                $('#pasienTidakDitemukan')
                    .addClass('d-none');

            }

        }
    );


    /* =====================================================
       RESET SEARCH
    ===================================================== */

    $('#resetSearchPasien').on(
        'click',
        function () {

            $('#searchPasien')
                .val('');


            $('#tabelPasien tbody tr.data-pasien')
                .show();


            $('#pasienTidakDitemukan')
                .addClass('d-none');


            $('#searchPasien')
                .focus();

        }
    );


    /* =====================================================
       MODAL DIBUKA
    ===================================================== */

    $('#modalPasien').on(
        'shown.bs.modal',
        function () {

            $('#searchPasien')
                .focus();

        }
    );


    /* =====================================================
       MODAL DITUTUP
    ===================================================== */

    $('#modalPasien').on(
        'hidden.bs.modal',
        function () {

            $('#searchPasien')
                .val('');


            $('#tabelPasien tbody tr.data-pasien')
                .show();


            $('#pasienTidakDitemukan')
                .addClass('d-none');

        }
    );


    /* =====================================================
       TOKENFIELD OBAT
    ===================================================== */

    $('#tokenfield').tokenfield({

        autocomplete: {

            source: [

                <?php

                if (!empty($nmObat)) {

                    echo '"'
                        . implode(
                            '","',
                            array_map(
                                'addslashes',
                                $nmObat
                            )
                        )
                        . '"';

                }

                ?>

            ],

            delay: 100

        },

        showAutocompleteOnFocus: true

    });


});


/* =========================================================
   VALIDASI FORM
========================================================= */

function validasiForm() {

    let pasien =
        document.getElementById(
            'pasien_id'
        ).value.trim();


    if (pasien === '') {

        alert(
            'Silakan pilih pasien terlebih dahulu.'
        );

        return false;

    }


    return true;

}

</script>


<?php

require "../template/footer.php";

?>