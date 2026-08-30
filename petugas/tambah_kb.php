```php
<?php

session_start();

require "../template/rbac.php";

// =========================================================
// HANYA PETUGAS
// =========================================================
cekAkses([ROLE_PETUGAS]);

require "../config.php";

$title = "Tambah Peserta KB - Rekam Medis Puskesmas";


// =========================================================
// PROSES SIMPAN DATA
// =========================================================
if (isset($_POST['simpan'])) {

    // =====================================================
    // DATA IDENTITAS PESERTA
    // =====================================================
    $tanggal = mysqli_real_escape_string(
        $koneksi,
        $_POST['tanggal'] ?? ''
    );

    $no_kk = mysqli_real_escape_string(
        $koneksi,
        $_POST['no_kk'] ?? ''
    );

    $nama_istri = mysqli_real_escape_string(
        $koneksi,
        formatNama($_POST['nama_istri'] ?? '')
    );

    $tanggal_lahir = mysqli_real_escape_string(
        $koneksi,
        $_POST['tanggal_lahir'] ?? ''
    );

    $nama_suami = mysqli_real_escape_string(
        $koneksi,
        formatNama($_POST['nama_suami'] ?? '')
    );

    $jumlah_anak = mysqli_real_escape_string(
        $koneksi,
        $_POST['jumlah_anak'] ?? ''
    );

    $alamat = mysqli_real_escape_string(
        $koneksi,
        $_POST['alamat'] ?? ''
    );


    // =====================================================
    // PEMERIKSAAN AWAL
    // Diisi oleh Petugas
    // =====================================================
    $tensi_darah = mysqli_real_escape_string(
        $koneksi,
        $_POST['tensi_darah'] ?? ''
    );

    $bb = mysqli_real_escape_string(
        $koneksi,
        $_POST['bb'] ?? ''
    );


    // =====================================================
    // DATA PELAYANAN KB
    // TIDAK DIISI PETUGAS
    // Akan diisi oleh Bidan
    // =====================================================
    $jenis_kb = '';
    $kunjungan = '';
    $keterangan = '';


    // =====================================================
    // VALIDASI DATA WAJIB
    // =====================================================
    if (
        empty($tanggal) ||
        empty($no_kk) ||
        empty($nama_istri) ||
        empty($tanggal_lahir) ||
        empty($nama_suami) ||
        $jumlah_anak === '' ||
        empty($alamat) ||
        empty($tensi_darah) ||
        $bb === ''
    ) {

        echo "
        <script>
            alert('Data yang bertanda * wajib diisi!');
            window.history.back();
        </script>
        ";

        exit();
    }


    // =====================================================
    // VALIDASI TENSI DARAH
    // Contoh: 120/80
    // =====================================================
    if (!preg_match('/^[0-9]{2,3}\\/[0-9]{2,3}$/', $tensi_darah)) {

        echo "
        <script>
            alert('Format tensi darah tidak valid! Contoh: 120/80');
            window.history.back();
        </script>
        ";

        exit();
    }


    // =====================================================
    // VALIDASI BERAT BADAN
    // =====================================================
    if (!is_numeric($bb) || $bb <= 0) {

        echo "
        <script>
            alert('Berat badan harus berupa angka dan lebih dari 0 Kg!');
            window.history.back();
        </script>
        ";

        exit();
    }


    // =====================================================
    // NOMOR PESERTA KB OTOMATIS
    // Format:
    // KB000001
    // KB000002
    // dst
    // =====================================================
    $cekPeserta = mysqli_query(
        $koneksi,
        "
        SELECT MAX(no_peserta_kb) AS max_peserta
        FROM tbl_kb
        WHERE no_peserta_kb LIKE 'KB%'
        "
    );


    if (!$cekPeserta) {

        die(
            "Query nomor peserta gagal : " .
            mysqli_error($koneksi)
        );

    }


    $dataPeserta = mysqli_fetch_assoc($cekPeserta);


    if (empty($dataPeserta['max_peserta'])) {

        $no_peserta_kb = "KB000001";

    } else {

        $angka = (int) substr(
            $dataPeserta['max_peserta'],
            2
        );

        $angka++;

        $no_peserta_kb = "KB" . sprintf(
            "%06d",
            $angka
        );

    }


    // =====================================================
    // SIMPAN KE TBL_KB
    // =====================================================
    $query = mysqli_query(
        $koneksi,
        "
        INSERT INTO tbl_kb
        (
            tanggal,
            no_kk,
            no_peserta_kb,
            nama_istri,
            tanggal_lahir,
            nama_suami,
            jumlah_anak,
            alamat,
            jenis_kb,
            kunjungan,
            tensi_darah,
            bb,
            keterangan
        )
        VALUES
        (
            '$tanggal',
            '$no_kk',
            '$no_peserta_kb',
            '$nama_istri',
            '$tanggal_lahir',
            '$nama_suami',
            '$jumlah_anak',
            '$alamat',
            '$jenis_kb',
            '$kunjungan',
            '$tensi_darah',
            '$bb',
            '$keterangan'
        )
        "
    );


    // =====================================================
    // JIKA BERHASIL
    // =====================================================
    if ($query) {

        // ID KB yang baru disimpan
        $idKbBaru = mysqli_insert_id($koneksi);


        // =================================================
        // TANGGAL HARI INI
        // =================================================
        $tanggalHariIni = date('Y-m-d');


        // =================================================
        // NOMOR ANTRIAN KEBIDANAN
        // =================================================
        $noAntrian = nextNoAntrianKebidanan(
            $koneksi,
            $tanggalHariIni
        );


        // =================================================
        // MASUKKAN KE JADWAL KEBIDANAN
        // =================================================
        $queryJadwal = mysqli_query(
            $koneksi,
            "
            INSERT INTO tbl_pendaftaran_kebidanan
            (
                jenis_layanan,
                ref_id,
                tanggal,
                no_antrian,
                status
            )
            VALUES
            (
                'KB',
                '$idKbBaru',
                '$tanggalHariIni',
                '$noAntrian',
                'Menunggu'
            )
            "
        );


        // =================================================
        // PESAN BERHASIL
        // =================================================
        $namaIstriJs = htmlspecialchars(
            $nama_istri,
            ENT_QUOTES,
            'UTF-8'
        );

        echo "
        <script>

            alert(
                'Data peserta KB berhasil disimpan!\\n\\n' +
                'No. Peserta KB: $no_peserta_kb\\n' +
                'Nama Istri: $namaIstriJs\\n' +
                'Tensi Darah: $tensi_darah mmHg\\n' +
                'Berat Badan: $bb Kg\\n' +
                'Nomor Antrian: $noAntrian'
            );

            window.location='register-kb.php';

        </script>
        ";

        exit();


    } else {

        echo "
        <script>

            alert(
                'Data gagal disimpan!\\n\\n" .
                addslashes(mysqli_error($koneksi)) .
                "'
            );

            window.history.back();

        </script>
        ";

        exit();

    }

}


// =========================================================
// TEMPLATE
// =========================================================
require "../template/header.php";
require "../template/navbar.php";
require "../template/sidebar.php";

?>

<style>

/* =========================================================
   HALAMAN
========================================================= */

.page-content-wrap {
    padding-bottom: 40px;
}


/* =========================================================
   HEADER
========================================================= */

.kb-form-header {
    padding-top: 25px;
    padding-bottom: 20px;
    margin-bottom: 25px;
    border-bottom: 1px solid #e9e9ef;
}

.kb-form-header h1 {
    font-size: 28px;
    font-weight: 700;
    color: #212229;
}

.kb-form-header p {
    font-size: 14px;
    color: #6c757d;
}


/* =========================================================
   CARD
========================================================= */

.kb-form-card {
    border: none !important;
    border-radius: 15px !important;
    box-shadow: 0 4px 18px rgba(0,0,0,.07) !important;
    background: #fff;
}

.kb-form-card .card-body {
    padding: 25px;
}


/* =========================================================
   JUDUL SECTION
========================================================= */

.kb-section-title {
    display: flex;
    align-items: center;
    gap: 12px;

    padding-bottom: 15px;
    margin-bottom: 22px;

    border-bottom: 1px solid #e9e9ef;
}

.kb-section-icon {
    width: 42px;
    height: 42px;

    border-radius: 10px;

    background: #212229;
    color: #fff;

    display: flex;
    align-items: center;
    justify-content: center;

    font-size: 18px;
}

.kb-section-title h5 {
    margin: 0;

    font-size: 17px;
    font-weight: 700;

    color: #212229;
}

.kb-section-title small {
    display: block;

    margin-top: 3px;

    color: #6c757d;

    font-size: 12px;
}


/* =========================================================
   FORM
========================================================= */

.form-label {
    font-size: 13px;
    font-weight: 600;

    color: #343540;

    margin-bottom: 7px;
}

.form-control,
.form-select {

    border: 1px solid #dedfe6;

    border-radius: 9px;

    padding: 10px 12px;

    font-size: 13px;

    min-height: 43px;
}

textarea.form-control {
    min-height: 105px;

    resize: vertical;
}

.form-control:focus,
.form-select:focus {

    border-color: #6c757d;

    box-shadow:
        0 0 0 3px
        rgba(33,37,41,.08);
}


/* =========================================================
   INPUT GROUP
========================================================= */

.input-group .form-control {

    border-top-right-radius: 0;
    border-bottom-right-radius: 0;
}

.input-group-text {

    border: 1px solid #dedfe6;

    background: #f8f8fa;

    color: #555;

    font-size: 13px;

    min-width: 58px;

    justify-content: center;
}


/* =========================================================
   INFO
========================================================= */

.kb-info {

    background: #f8f8fa;

    border: 1px solid #e5e5ea;

    border-radius: 10px;

    padding: 13px 15px;

    margin-bottom: 20px;

    font-size: 12px;

    color: #5f606b;
}

.kb-info i {

    margin-right: 5px;

    color: #343540;
}


/* =========================================================
   NOMOR PESERTA
========================================================= */

.kb-auto-number {

    background: #f8f8fa;

    color: #777;

    cursor: not-allowed;
}


/* =========================================================
   BUTTON
========================================================= */

.kb-form-buttons {

    display: flex;

    justify-content: flex-end;

    gap: 10px;

    margin-top: 5px;
}

.kb-form-buttons .btn {

    border-radius: 9px;

    padding: 10px 17px;

    font-size: 13px;

    font-weight: 500;
}

.kb-form-buttons .btn-dark {

    box-shadow: 0 3px 8px rgba(0,0,0,.12);

    transition: all .2s ease;
}

.kb-form-buttons .btn-dark:hover {

    transform: translateY(-1px);

    box-shadow:
        0 5px 12px
        rgba(0,0,0,.16);
}


/* =========================================================
   RESPONSIVE
========================================================= */

@media (max-width: 768px) {

    .kb-form-card .card-body {
        padding: 20px;
    }

    .kb-form-header h1 {
        font-size: 24px;
    }

    .kb-form-header > div {

        align-items: flex-start !important;

        flex-direction: column;

        gap: 15px;
    }

    .kb-form-buttons {

        flex-direction: column;
    }

    .kb-form-buttons .btn {

        width: 100%;
    }

}


@media (max-width: 576px) {

    .kb-form-card .card-body {

        padding: 15px;
    }

}

</style>


<!-- =========================================================
     MAIN
========================================================= -->

<div class="page-content-wrap">


    <!-- =====================================================
         HEADER
    ====================================================== -->

    <div class="kb-form-header">

        <div class="d-flex justify-content-between align-items-center">

            <div>

                <h1 class="h2 mb-1">
                    Tambah Peserta KB
                </h1>

                <p class="mb-0">
                    Form pendaftaran peserta keluarga berencana
                </p>

            </div>


            <!-- KEMBALI -->

            <a
                href="register-kb.php"
                class="btn btn-secondary"
            >

                <i class="bi bi-arrow-left me-1"></i>

                Kembali

            </a>

        </div>

    </div>


    <!-- =====================================================
         FORM
    ====================================================== -->

    <form method="POST">


        <!-- =================================================
             DATA PESERTA
        ================================================== -->

        <div class="card kb-form-card mb-4">

            <div class="card-body">


                <!-- SECTION TITLE -->

                <div class="kb-section-title">

                    <div class="kb-section-icon">

                        <i class="bi bi-person-vcard"></i>

                    </div>

                    <div>

                        <h5>
                            Data Peserta KB
                        </h5>

                        <small>
                            Data identitas peserta keluarga berencana
                        </small>

                    </div>

                </div>


                <!-- FORM DATA -->

                <div class="row g-3">


                    <!-- TANGGAL -->

                    <div class="col-md-6">

                        <label class="form-label">

                            Tanggal

                            <span class="text-danger">*</span>

                        </label>

                        <input
                            type="date"
                            name="tanggal"
                            class="form-control"
                            value="<?= date('Y-m-d'); ?>"
                            required
                        >

                    </div>


                    <!-- NO KK -->

                    <div class="col-md-6">

                        <label class="form-label">

                            No. KK

                            <span class="text-danger">*</span>

                        </label>

                        <input
                            type="text"
                            name="no_kk"
                            class="form-control"
                            placeholder="Masukkan No. KK"
                            maxlength="20"
                            required
                        >

                    </div>


                    <!-- NO PESERTA -->

                    <div class="col-md-6">

                        <label class="form-label">

                            No. Peserta KB

                        </label>

                        <input
                            type="text"
                            class="form-control kb-auto-number"
                            value="Dibuat otomatis setelah disimpan"
                            readonly
                        >

                    </div>


                    <!-- NAMA ISTRI -->

                    <div class="col-md-6">

                        <label class="form-label">

                            Nama Istri

                            <span class="text-danger">*</span>

                        </label>

                        <input
                            type="text"
                            name="nama_istri"
                            class="form-control"
                            placeholder="Masukkan nama istri"
                            required
                        >

                    </div>


                    <!-- TANGGAL LAHIR -->

                    <div class="col-md-6">

                        <label class="form-label">

                            Tanggal Lahir

                            <span class="text-danger">*</span>

                        </label>

                        <input
                            type="date"
                            name="tanggal_lahir"
                            class="form-control"
                            required
                        >

                    </div>


                    <!-- NAMA SUAMI -->

                    <div class="col-md-6">

                        <label class="form-label">

                            Nama Suami

                            <span class="text-danger">*</span>

                        </label>

                        <input
                            type="text"
                            name="nama_suami"
                            class="form-control"
                            placeholder="Masukkan nama suami"
                            required
                        >

                    </div>


                    <!-- JUMLAH ANAK -->

                    <div class="col-md-6">

                        <label class="form-label">

                            Jumlah Anak

                            <span class="text-danger">*</span>

                        </label>

                        <input
                            type="number"
                            name="jumlah_anak"
                            class="form-control"
                            min="0"
                            placeholder="Jumlah anak"
                            required
                        >

                    </div>


                    <!-- ALAMAT -->

                    <div class="col-12">

                        <label class="form-label">

                            Alamat

                            <span class="text-danger">*</span>

                        </label>

                        <textarea
                            name="alamat"
                            class="form-control"
                            rows="3"
                            placeholder="Masukkan alamat lengkap"
                            required
                        ></textarea>

                    </div>


                </div>

            </div>

        </div>


        <!-- =================================================
             PEMERIKSAAN AWAL
        ================================================== -->

        <div class="card kb-form-card mb-4">

            <div class="card-body">


                <!-- SECTION TITLE -->

                <div class="kb-section-title">

                    <div class="kb-section-icon">

                        <i class="bi bi-heart-pulse"></i>

                    </div>

                    <div>

                        <h5>
                            Pemeriksaan Awal
                        </h5>

                        <small>
                            Pemeriksaan dilakukan oleh Petugas sebelum pelayanan KB
                        </small>

                    </div>

                </div>


                <!-- INFO -->

                <div class="kb-info">

                    <i class="bi bi-info-circle"></i>

                    Tensi darah dan berat badan wajib diisi oleh Petugas
                    sebelum peserta mendapatkan pelayanan KB dari Bidan.

                </div>


                <!-- FORM PEMERIKSAAN -->

                <div class="row g-3">


                    <!-- TENSI -->

                    <div class="col-md-6">

                        <label class="form-label">

                            Tensi Darah

                            <span class="text-danger">*</span>

                        </label>


                        <div class="input-group">

                            <input
                                type="text"
                                name="tensi_darah"
                                class="form-control"
                                placeholder="Contoh: 120/80"
                                maxlength="7"
                                pattern="[0-9]{2,3}/[0-9]{2,3}"
                                title="Masukkan format seperti 120/80"
                                required
                            >

                            <span class="input-group-text">
                                mmHg
                            </span>

                        </div>

                    </div>


                    <!-- BB -->

                    <div class="col-md-6">

                        <label class="form-label">

                            Berat Badan (BB)

                            <span class="text-danger">*</span>

                        </label>


                        <div class="input-group">

                            <input
                                type="number"
                                name="bb"
                                class="form-control"
                                step="0.1"
                                min="0.1"
                                placeholder="Contoh: 55"
                                required
                            >

                            <span class="input-group-text">
                                Kg
                            </span>

                        </div>

                    </div>


                </div>

            </div>

        </div>


        <!-- =================================================
             BUTTON
        ================================================== -->

        <div class="kb-form-buttons mb-4">


            <!-- BATAL -->

            <a
                href="register-kb.php"
                class="btn btn-secondary"
            >

                <i class="bi bi-x-lg me-1"></i>

                Batal

            </a>


            <!-- SIMPAN -->

            <button
                type="submit"
                name="simpan"
                value="1"
                class="btn btn-dark"
            >

                <i class="bi bi-save me-1"></i>

                Simpan Data Peserta KB

            </button>


        </div>


    </form>

</div>


<?php

require "../template/footer.php";

?>
```
