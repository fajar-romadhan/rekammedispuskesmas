```php
<?php

session_start();

require "../template/rbac.php";

// Hanya Petugas
cekAkses([ROLE_PETUGAS]);

require "../config.php";

mysqli_report(MYSQLI_REPORT_OFF);


/*
|--------------------------------------------------------------------------
| SIMPAN DATA IBU HAMIL
|--------------------------------------------------------------------------
*/

if (isset($_POST['simpan'])) {

    /*
    |--------------------------------------------------------------------------
    | IDENTITAS IBU
    |--------------------------------------------------------------------------
    */

    $nik = mysqli_real_escape_string(
        $koneksi,
        trim($_POST['nik'] ?? '')
    );

    $no_kk = mysqli_real_escape_string(
        $koneksi,
        trim($_POST['no_kk'] ?? '')
    );

    $nama_ibu = mysqli_real_escape_string(
        $koneksi,
        formatNama(trim($_POST['nama_ibu'] ?? ''))
    );

    $tempat_lahir = mysqli_real_escape_string(
        $koneksi,
        trim($_POST['tempat_lahir'] ?? '')
    );

    $tgl_lahir = !empty($_POST['tgl_lahir'])
        ? mysqli_real_escape_string($koneksi, $_POST['tgl_lahir'])
        : null;


    /*
    |--------------------------------------------------------------------------
    | IDENTITAS SUAMI
    |--------------------------------------------------------------------------
    */

    $nama_suami = mysqli_real_escape_string(
        $koneksi,
        formatNama(trim($_POST['nama_suami'] ?? ''))
    );

    $tempat_lahir_suami = mysqli_real_escape_string(
        $koneksi,
        trim($_POST['tempat_lahir_suami'] ?? '')
    );

    $tgl_lahir_suami = !empty($_POST['tgl_lahir_suami'])
        ? mysqli_real_escape_string($koneksi, $_POST['tgl_lahir_suami'])
        : null;

    $nik_suami = mysqli_real_escape_string(
        $koneksi,
        trim($_POST['nik_suami'] ?? '')
    );


    /*
    |--------------------------------------------------------------------------
    | DATA LAIN
    |--------------------------------------------------------------------------
    */

    $no_hp = mysqli_real_escape_string(
        $koneksi,
        trim($_POST['no_hp'] ?? '')
    );

    $alamat = mysqli_real_escape_string(
        $koneksi,
        trim($_POST['alamat'] ?? '')
    );

    $bpjs = mysqli_real_escape_string(
        $koneksi,
        trim($_POST['bpjs'] ?? '')
    );

    $gol_darah = mysqli_real_escape_string(
        $koneksi,
        trim($_POST['gol_darah'] ?? '')
    );

    $pendidikan_ibu = mysqli_real_escape_string(
        $koneksi,
        trim($_POST['pendidikan_ibu'] ?? '')
    );

    $pendidikan_suami = mysqli_real_escape_string(
        $koneksi,
        trim($_POST['pendidikan_suami'] ?? '')
    );


    /*
    |--------------------------------------------------------------------------
    | VALIDASI
    |--------------------------------------------------------------------------
    */

    if ($nik == '' || $nama_ibu == '') {

        echo "<script>
                alert('NIK dan Nama Ibu wajib diisi.');
                window.history.back();
              </script>";

        exit();
    }


    /*
    |--------------------------------------------------------------------------
    | CEK NIK
    |--------------------------------------------------------------------------
    */

    $cek = mysqli_query(
        $koneksi,
        "SELECT id
         FROM tbl_ibu_hamil
         WHERE nik = '$nik'
         LIMIT 1"
    );

    if (!$cek) {

        echo "<script>
                alert('Gagal mengecek NIK:\\n\\n" .
                addslashes(mysqli_error($koneksi)) . "');
                window.history.back();
              </script>";

        exit();
    }


    if (mysqli_num_rows($cek) > 0) {

        echo "<script>
                alert('NIK sudah terdaftar sebagai ibu hamil.');
                window.history.back();
              </script>";

        exit();
    }


    /*
    |--------------------------------------------------------------------------
    | INSERT DATA IBU HAMIL
    |--------------------------------------------------------------------------
    */

    $query = "INSERT INTO tbl_ibu_hamil
    (
        nik,
        no_kk,
        nama_ibu,
        nama_suami,
        tempat_lahir_suami,
        tgl_lahir_suami,
        nik_suami,
        tempat_lahir,
        tgl_lahir,
        no_hp,
        alamat,
        bpjs,
        gol_darah,
        pendidikan_ibu,
        pendidikan_suami
    )
    VALUES
    (
        '$nik',
        '$no_kk',
        '$nama_ibu',
        '$nama_suami',
        '$tempat_lahir_suami',
        " . ($tgl_lahir_suami !== null
            ? "'$tgl_lahir_suami'"
            : "NULL") . ",
        '$nik_suami',
        '$tempat_lahir',
        " . ($tgl_lahir !== null
            ? "'$tgl_lahir'"
            : "NULL") . ",
        '$no_hp',
        '$alamat',
        '$bpjs',
        '$gol_darah',
        '$pendidikan_ibu',
        '$pendidikan_suami'
    )";


    /*
    |--------------------------------------------------------------------------
    | EKSEKUSI
    |--------------------------------------------------------------------------
    */

    $simpanIbu = mysqli_query(
        $koneksi,
        $query
    );


    /*
    |--------------------------------------------------------------------------
    | JIKA GAGAL
    |--------------------------------------------------------------------------
    */

    if (!$simpanIbu) {

        $error = mysqli_error($koneksi);

        echo "<script>
                alert(
                    'Data ibu hamil gagal disimpan!\\n\\n' +
                    'Error database:\\n$error'
                );

                window.history.back();
              </script>";

        exit();
    }


    /*
    |--------------------------------------------------------------------------
    | ID IBU BARU
    |--------------------------------------------------------------------------
    */

    $idIbuBaru = mysqli_insert_id($koneksi);

    $tanggalHariIni = date('Y-m-d');


    /*
    |--------------------------------------------------------------------------
    | NOMOR ANTRIAN
    |--------------------------------------------------------------------------
    */

    if (function_exists('nextNoAntrianKebidanan')) {

        $noAntrian = nextNoAntrianKebidanan(
            $koneksi,
            $tanggalHariIni
        );

    } else {

        $cekAntrian = mysqli_query(
            $koneksi,
            "SELECT MAX(no_antrian) AS max_antrian
             FROM tbl_pendaftaran_kebidanan
             WHERE tanggal = '$tanggalHariIni'"
        );

        $dataAntrian = mysqli_fetch_assoc($cekAntrian);

        $noAntrian =
            ((int)($dataAntrian['max_antrian'] ?? 0)) + 1;
    }


    /*
    |--------------------------------------------------------------------------
    | MASUKKAN KE JADWAL KEBIDANAN
    |--------------------------------------------------------------------------
    */

    $queryJadwal = "INSERT INTO tbl_pendaftaran_kebidanan
    (
        jenis_layanan,
        ref_id,
        tanggal,
        no_antrian,
        status
    )
    VALUES
    (
        'Ibu Hamil',
        '$idIbuBaru',
        '$tanggalHariIni',
        '$noAntrian',
        'Menunggu'
    )";


    $simpanJadwal = mysqli_query(
        $koneksi,
        $queryJadwal
    );


    /*
    |--------------------------------------------------------------------------
    | JIKA JADWAL GAGAL
    |--------------------------------------------------------------------------
    */

    if (!$simpanJadwal) {

        $error = mysqli_error($koneksi);

        echo "<script>
                alert(
                    'Data ibu hamil berhasil disimpan, tetapi gagal masuk jadwal kebidanan.\\n\\n' +
                    'Error database:\\n$error'
                );

                window.location='rekam-medis-kebidanan.php';
              </script>";

        exit();
    }


    /*
    |--------------------------------------------------------------------------
    | BERHASIL
    |--------------------------------------------------------------------------
    */

    echo "<script>

            alert(
                'Data ibu hamil berhasil disimpan.\\n\\n' +
                'Nomor antrian: $noAntrian'
            );

            window.location='rekam-medis-kebidanan.php';

          </script>";

    exit();
}


$title = "Register Ibu Hamil - Rekam Medis Puskesmas";

require "../template/header.php";
require "../template/navbar.php";
require "../template/sidebar.php";
?>


<style>

/* =========================================================
   REGISTER IBU HAMIL
========================================================= */

.register-page {
    padding-bottom: 40px;
}


/* HEADER */

.page-header {
    background: linear-gradient(135deg, #212229, #343540);
    color: #fff;
    border-radius: 16px;
    padding: 24px 28px;
    margin-top: 20px;
    margin-bottom: 25px;
    box-shadow: 0 8px 25px rgba(0,0,0,.08);
}

.page-header h1 {
    font-size: 24px;
    font-weight: 700;
    margin-bottom: 6px;
}

.page-header p {
    color: rgba(255,255,255,.75);
    margin-bottom: 0;
}

.page-header .btn {
    border-radius: 10px;
    padding: 9px 17px;
    font-weight: 500;
}


/* CARD */

.form-card {
    border: none !important;
    border-radius: 15px !important;
    overflow: hidden;
    box-shadow: 0 5px 20px rgba(0,0,0,.07) !important;
}


/* JUDUL */

.section-title {
    display: flex;
    align-items: center;
    gap: 10px;
    font-weight: 700;
    font-size: 17px;
    color: #212229;
    border-bottom: 1px solid #e9e9ef;
    padding-bottom: 14px;
    margin-bottom: 22px;
}

.section-title .icon {
    width: 35px;
    height: 35px;
    border-radius: 10px;
    background: #212229;
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
}


/* LABEL */

.form-label {
    font-weight: 600;
    font-size: 14px;
    color: #343540;
    margin-bottom: 7px;
}


/* INPUT */

.form-control,
.form-select {
    min-height: 44px;
    border-radius: 9px;
    border: 1px solid #dedfe6;
    font-size: 14px;
    transition: .2s;
}

.form-control:focus,
.form-select:focus {
    border-color: #6c757d;
    box-shadow: 0 0 0 .2rem rgba(33,37,41,.08);
}


/* TEXTAREA */

textarea.form-control {
    min-height: 85px;
    resize: vertical;
}


/* REQUIRED */

.text-danger {
    font-weight: 700;
}


/* BUTTON */

.btn {
    border-radius: 9px;
    font-size: 14px;
    font-weight: 500;
}

.btn-dark {
    box-shadow: 0 3px 8px rgba(0,0,0,.12);
}

.btn-dark:hover {
    transform: translateY(-1px);
}


/* ACTION CARD */

.action-card {
    background: #fff;
    border: none !important;
    border-radius: 15px !important;
    box-shadow: 0 5px 20px rgba(0,0,0,.07) !important;
}


/* MOBILE */

@media (max-width: 768px) {

    .page-header {
        padding: 20px;
        flex-direction: column;
        align-items: flex-start !important;
        gap: 15px;
    }

    .page-header h1 {
        font-size: 20px;
    }

    .page-header .btn {
        width: 100%;
    }

    .form-card .card-body {
        padding: 18px;
    }

}

</style>


<div class="register-page page-content-wrap">


    <!-- =====================================================
         HEADER
    ====================================================== -->

    <div class="page-header d-flex justify-content-between align-items-center">

        <div>

            <h1>
                <i class="bi bi-person-heart me-2"></i>
                Register Ibu Hamil
            </h1>

            <p>
                Pendaftaran data ibu hamil
            </p>

        </div>


        <a href="rekam-medis-kebidanan.php"
           class="btn btn-light">

            <i class="bi bi-arrow-left me-1"></i>

            Kembali

        </a>

    </div>


    <form method="POST" action="">


        <!-- =====================================================
             IDENTITAS
        ====================================================== -->

        <div class="card form-card mb-4">

            <div class="card-body p-4">

                <h5 class="section-title">

                    <span class="icon">
                        <i class="bi bi-person"></i>
                    </span>

                    Identitas Ibu Hamil

                </h5>


                <div class="row g-4">


                    <!-- NAMA IBU -->

                    <div class="col-md-6">

                        <label class="form-label">

                            Nama Ibu

                            <span class="text-danger">*</span>

                        </label>

                        <input type="text"
                               name="nama_ibu"
                               class="form-control"
                               placeholder="Masukkan nama lengkap ibu"
                               required>

                    </div>


                    <!-- NAMA SUAMI -->

                    <div class="col-md-6">

                        <label class="form-label">
                            Nama Suami
                        </label>

                        <input type="text"
                               name="nama_suami"
                               class="form-control"
                               placeholder="Masukkan nama suami">

                    </div>


                    <!-- TEMPAT LAHIR IBU -->

                    <div class="col-md-4">

                        <label class="form-label">
                            Tempat Lahir Ibu
                        </label>

                        <input type="text"
                               name="tempat_lahir"
                               class="form-control"
                               placeholder="Contoh: Palembang">

                    </div>


                    <!-- TANGGAL LAHIR IBU -->

                    <div class="col-md-4">

                        <label class="form-label">
                            Tanggal Lahir Ibu
                        </label>

                        <input type="date"
                               name="tgl_lahir"
                               class="form-control">

                    </div>


                    <!-- NIK IBU -->

                    <div class="col-md-4">

                        <label class="form-label">

                            NIK Ibu

                            <span class="text-danger">*</span>

                        </label>

                        <input type="text"
                               name="nik"
                               class="form-control"
                               maxlength="50"
                               placeholder="Masukkan NIK"
                               required>

                    </div>


                    <!-- TEMPAT LAHIR SUAMI -->

                    <div class="col-md-4">

                        <label class="form-label">
                            Tempat Lahir Suami
                        </label>

                        <input type="text"
                               name="tempat_lahir_suami"
                               class="form-control"
                               placeholder="Tempat lahir suami">

                    </div>


                    <!-- TANGGAL LAHIR SUAMI -->

                    <div class="col-md-4">

                        <label class="form-label">
                            Tanggal Lahir Suami
                        </label>

                        <input type="date"
                               name="tgl_lahir_suami"
                               class="form-control">

                    </div>


                    <!-- NIK SUAMI -->

                    <div class="col-md-4">

                        <label class="form-label">
                            NIK Suami
                        </label>

                        <input type="text"
                               name="nik_suami"
                               class="form-control"
                               maxlength="50"
                               placeholder="Masukkan NIK suami">

                    </div>


                    <!-- NO KK -->

                    <div class="col-md-6">

                        <label class="form-label">
                            No. KK
                        </label>

                        <input type="text"
                               name="no_kk"
                               class="form-control"
                               maxlength="50"
                               placeholder="Masukkan nomor KK">

                    </div>


                    <!-- NO HP -->

                    <div class="col-md-6">

                        <label class="form-label">
                            No. HP
                        </label>

                        <input type="text"
                               name="no_hp"
                               class="form-control"
                               placeholder="08xxxxxxxxxx">

                    </div>


                    <!-- ALAMAT -->

                    <div class="col-12">

                        <label class="form-label">
                            Alamat
                        </label>

                        <textarea name="alamat"
                                  class="form-control"
                                  rows="2"
                                  placeholder="Masukkan alamat lengkap"></textarea>

                    </div>


                    <!-- PENDIDIKAN IBU -->

                    <div class="col-md-6">

                        <label class="form-label">
                            Pendidikan Ibu
                        </label>

                        <select name="pendidikan_ibu"
                                class="form-select">

                            <option value="">
                                Pilih Pendidikan
                            </option>

                            <option value="SD">SD</option>
                            <option value="SMP">SMP</option>
                            <option value="SMA">SMA</option>
                            <option value="D3">D3</option>
                            <option value="S1">S1</option>
                            <option value="S2">S2</option>
                            <option value="S3">S3</option>

                        </select>

                    </div>


                    <!-- PENDIDIKAN SUAMI -->

                    <div class="col-md-6">

                        <label class="form-label">
                            Pendidikan Suami
                        </label>

                        <select name="pendidikan_suami"
                                class="form-select">

                            <option value="">
                                Pilih Pendidikan
                            </option>

                            <option value="SD">SD</option>
                            <option value="SMP">SMP</option>
                            <option value="SMA">SMA</option>
                            <option value="D3">D3</option>
                            <option value="S1">S1</option>
                            <option value="S2">S2</option>
                            <option value="S3">S3</option>

                        </select>

                    </div>


                    <!-- BPJS -->

                    <div class="col-md-6">

                        <label class="form-label">
                            BPJS / KIS
                        </label>

                        <input type="text"
                               name="bpjs"
                               class="form-control"
                               placeholder="Nomor BPJS / KIS">

                    </div>


                    <!-- GOL DARAH -->

                    <div class="col-md-6">

                        <label class="form-label">
                            Golongan Darah
                        </label>

                        <select name="gol_darah"
                                class="form-select">

                            <option value="">
                                Pilih Golongan Darah
                            </option>

                            <option value="A">A</option>
                            <option value="B">B</option>
                            <option value="AB">AB</option>
                            <option value="O">O</option>

                            <option value="Tidak Diketahui">
                                Tidak Diketahui
                            </option>

                        </select>

                    </div>

                </div>

            </div>

        </div>


        <!-- =====================================================
             BUTTON
        ====================================================== -->

        <div class="action-card card mb-4">

            <div class="card-body p-4">

                <div class="d-flex justify-content-between align-items-center">

                    <div class="text-muted small">

                        <i class="bi bi-info-circle me-1"></i>

                        Pastikan data yang dimasukkan sudah benar.

                    </div>


                    <div class="d-flex justify-content-end gap-2">

                        <a href="rekam-medis-kebidanan.php"
                           class="btn btn-secondary">

                            <i class="bi bi-x-lg me-1"></i>

                            Batal

                        </a>


                        <button type="submit"
                                name="simpan"
                                value="1"
                                class="btn btn-dark">

                            <i class="bi bi-save me-1"></i>

                            Simpan Data Ibu Hamil

                        </button>

                    </div>

                </div>

            </div>

        </div>

    </form>

</div>


<?php
require "../template/footer.php";
?>
```
