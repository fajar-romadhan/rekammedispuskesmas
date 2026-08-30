<?php

session_start();

require "../template/rbac.php";

// Hanya Petugas
cekAkses([ROLE_PETUGAS]);

require "../config.php";

$title = "Edit Identitas Ibu Hamil - Rekam Medis Puskesmas";


/* =========================================================
   CEK ID
========================================================= */

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("ID ibu hamil tidak valid.");
}

$id = (int) $_GET['id'];


/* =========================================================
   AMBIL DATA IBU HAMIL
========================================================= */

$queryData = mysqli_query(
    $koneksi,
    "SELECT * FROM tbl_ibu_hamil WHERE id = '$id'"
);

if (!$queryData) {
    die("Query gagal: " . mysqli_error($koneksi));
}

$data = mysqli_fetch_assoc($queryData);

if (!$data) {
    die("Data ibu hamil tidak ditemukan.");
}


/* =========================================================
   PROSES UPDATE IDENTITAS
========================================================= */

if (isset($_POST['update'])) {

    // Nama Ibu
    $nama_ibu = mysqli_real_escape_string(
        $koneksi,
        formatNama($_POST['nama_ibu'])
    );

    // Nama Suami
    $nama_suami = mysqli_real_escape_string(
        $koneksi,
        formatNama($_POST['nama_suami'])
    );

    // Tempat Lahir Ibu
    $tempat_lahir = mysqli_real_escape_string(
        $koneksi,
        $_POST['tempat_lahir']
    );

    // Tanggal Lahir Ibu
    $tgl_lahir = mysqli_real_escape_string(
        $koneksi,
        $_POST['tgl_lahir']
    );

    // NIK Ibu
    $nik = mysqli_real_escape_string(
        $koneksi,
        $_POST['nik']
    );

    // Tempat Lahir Suami
    $tempat_lahir_suami = mysqli_real_escape_string(
        $koneksi,
        $_POST['tempat_lahir_suami']
    );

    // Tanggal Lahir Suami
    $tgl_lahir_suami = mysqli_real_escape_string(
        $koneksi,
        $_POST['tgl_lahir_suami']
    );

    // NIK Suami
    $nik_suami = mysqli_real_escape_string(
        $koneksi,
        $_POST['nik_suami']
    );

    // No KK
    $no_kk = mysqli_real_escape_string(
        $koneksi,
        $_POST['no_kk']
    );

    // No HP
    $no_hp = mysqli_real_escape_string(
        $koneksi,
        $_POST['no_hp']
    );

    // Alamat
    $alamat = mysqli_real_escape_string(
        $koneksi,
        $_POST['alamat']
    );

    // Pendidikan Ibu
    $pendidikan_ibu = mysqli_real_escape_string(
        $koneksi,
        $_POST['pendidikan_ibu']
    );

    // Pendidikan Suami
    $pendidikan_suami = mysqli_real_escape_string(
        $koneksi,
        $_POST['pendidikan_suami']
    );

    // BPJS / KIS
    $bpjs = mysqli_real_escape_string(
        $koneksi,
        $_POST['bpjs']
    );

    // Golongan Darah
    $gol_darah = mysqli_real_escape_string(
        $koneksi,
        $_POST['gol_darah']
    );


    /* =====================================================
       VALIDASI
    ===================================================== */

    if (
        empty($nama_ibu) ||
        empty($nik) ||
        empty($no_kk) ||
        empty($tempat_lahir) ||
        empty($tgl_lahir)
    ) {

        echo "
        <script>
            alert('Data yang bertanda * wajib diisi!');
            window.history.back();
        </script>
        ";

        exit();
    }


    /* =====================================================
       CEK NIK
       AGAR TIDAK DUPLIKAT
    ===================================================== */

    $cekNik = mysqli_query(
        $koneksi,
        "SELECT id
         FROM tbl_ibu_hamil
         WHERE nik = '$nik'
         AND id != '$id'
         LIMIT 1"
    );

    if (mysqli_num_rows($cekNik) > 0) {

        echo "
        <script>
            alert('NIK ibu sudah digunakan oleh data lain!');
            window.history.back();
        </script>
        ";

        exit();
    }


    /* =====================================================
       UPDATE DATA
    ===================================================== */

    $queryUpdate = mysqli_query(
        $koneksi,
        "UPDATE tbl_ibu_hamil SET

            nama_ibu = '$nama_ibu',
            nama_suami = '$nama_suami',

            tempat_lahir = '$tempat_lahir',
            tgl_lahir = '$tgl_lahir',

            nik = '$nik',

            tempat_lahir_suami = '$tempat_lahir_suami',
            tgl_lahir_suami = '$tgl_lahir_suami',
            nik_suami = '$nik_suami',

            no_kk = '$no_kk',
            no_hp = '$no_hp',

            alamat = '$alamat',

            pendidikan_ibu = '$pendidikan_ibu',
            pendidikan_suami = '$pendidikan_suami',

            bpjs = '$bpjs',
            gol_darah = '$gol_darah'

        WHERE id = '$id'"
    );


    /* =====================================================
       HASIL UPDATE
    ===================================================== */

    if ($queryUpdate) {

        echo "
        <script>
            alert('Identitas ibu hamil berhasil diperbarui!');
            window.location='rekam-medis-kebidanan.php?status=update';
        </script>
        ";

        exit();

    } else {

        echo "
        <script>
            alert('Data gagal diperbarui! ".mysqli_error($koneksi)."');
            window.history.back();
        </script>
        ";

        exit();
    }
}


require "../template/header.php";
require "../template/navbar.php";
require "../template/sidebar.php";

?>


<style>

/* =========================================================
   EDIT IDENTITAS IBU HAMIL
========================================================= */

.edit-ibu-page {
    padding-top: 18px;
    padding-bottom: 45px;
}


/* =========================================================
   HEADER
========================================================= */

.edit-header {
    background: linear-gradient(
        135deg,
        #ffffff 0%,
        #f8f8ff 100%
    );

    border: 1px solid #e5e5f0;

    border-radius: 20px;

    padding: 24px 28px;

    margin-bottom: 24px;

    box-shadow:
        0 5px 18px rgba(15, 23, 42, 0.05);

    display: flex;

    align-items: center;

    justify-content: space-between;

    gap: 20px;
}

.edit-title-area {
    display: flex;

    align-items: center;

    gap: 17px;
}

.edit-title-icon {
    width: 58px;

    height: 58px;

    min-width: 58px;

    border-radius: 16px;

    background: #eeeeff;

    color: #7571f9;

    display: flex;

    align-items: center;

    justify-content: center;

    font-size: 27px;
}

.edit-header h1 {
    margin: 0;

    font-size: 26px;

    font-weight: 700;

    color: #1a174d;
}

.edit-header p {
    margin: 5px 0 0;

    color: #727196;

    font-size: 14px;
}


/* =========================================================
   CARD
========================================================= */

.edit-card {
    border: 1px solid #e5e5f0;

    border-radius: 20px;

    background: #ffffff;

    box-shadow:
        0 5px 20px rgba(15, 23, 42, 0.04);

    margin-bottom: 20px;
}

.edit-card-body {
    padding: 25px;
}


/* =========================================================
   JUDUL CARD
========================================================= */

.edit-card-title {
    font-size: 17px;

    font-weight: 700;

    color: #1a174d;

    border-bottom: 1px solid #e9e9f2;

    padding-bottom: 12px;

    margin-bottom: 22px;
}


/* =========================================================
   LABEL
========================================================= */

.form-label {
    font-size: 14px;

    font-weight: 600;

    color: #363454;

    margin-bottom: 7px;
}

.form-control,
.form-select {
    border: 1px solid #dcdde8;

    border-radius: 10px;

    min-height: 44px;

    font-size: 14px;
}

.form-control:focus,
.form-select:focus {
    border-color: #aaa6f8;

    box-shadow:
        0 0 0 3px rgba(117, 113, 249, .10);
}


/* =========================================================
   BUTTON
========================================================= */

.btn-simpan {
    background: #212229;

    color: #ffffff;

    border: none;

    border-radius: 10px;

    padding: 11px 20px;

    font-weight: 600;

    font-size: 14px;
}

.btn-simpan:hover {
    background: #000000;

    color: #ffffff;
}

.btn-kembali {
    background: #f1f1f5;

    color: #494a57;

    border: 1px solid #dedfe6;

    border-radius: 10px;

    padding: 11px 20px;

    font-weight: 600;

    font-size: 14px;
}

.btn-kembali:hover {
    background: #e9e9ef;

    color: #212229;
}


/* =========================================================
   RESPONSIVE
========================================================= */

@media (max-width: 768px) {

    .edit-header {
        padding: 20px;

        flex-direction: column;

        align-items: flex-start;
    }

    .edit-card-body {
        padding: 18px;
    }

    .edit-header h1 {
        font-size: 22px;
    }

}

</style>


<div class="page-content-wrap">

<div class="edit-ibu-page">


    <!-- =====================================================
         HEADER
    ====================================================== -->

    <div class="edit-header">

        <div class="edit-title-area">

            <div class="edit-title-icon">

                <i class="bi bi-person-vcard"></i>

            </div>

            <div>

                <h1>
                    Edit Identitas Ibu Hamil
                </h1>

                <p>
                    Perbarui data identitas ibu hamil
                </p>

            </div>

        </div>


        <a
            href="rekam-medis-kebidanan.php"
            class="btn-kembali">

            <i class="bi bi-arrow-left me-1"></i>

            Kembali

        </a>

    </div>


    <!-- =====================================================
         FORM
    ====================================================== -->

    <form method="POST">


        <!-- =================================================
             IDENTITAS IBU HAMIL
        ================================================== -->

        <div class="edit-card">

            <div class="edit-card-body">

                <div class="edit-card-title">

                    <i class="bi bi-person me-2"></i>

                    Identitas Ibu Hamil

                </div>


                <div class="row g-3">


                    <!-- NAMA IBU -->

                    <div class="col-md-6">

                        <label class="form-label">

                            Nama Ibu
                            <span class="text-danger">*</span>

                        </label>

                        <input
                            type="text"
                            name="nama_ibu"
                            class="form-control"
                            value="<?= htmlspecialchars($data['nama_ibu']); ?>"
                            required>

                    </div>


                    <!-- NAMA SUAMI -->

                    <div class="col-md-6">

                        <label class="form-label">

                            Nama Suami

                        </label>

                        <input
                            type="text"
                            name="nama_suami"
                            class="form-control"
                            value="<?= htmlspecialchars($data['nama_suami']); ?>">

                    </div>


                    <!-- TEMPAT LAHIR IBU -->

                    <div class="col-md-6">

                        <label class="form-label">

                            Tempat Lahir Ibu

                        </label>

                        <input
                            type="text"
                            name="tempat_lahir"
                            class="form-control"
                            value="<?= htmlspecialchars($data['tempat_lahir']); ?>">

                    </div>


                    <!-- TANGGAL LAHIR IBU -->

                    <div class="col-md-6">

                        <label class="form-label">

                            Tanggal Lahir Ibu

                        </label>

                        <input
                            type="date"
                            name="tgl_lahir"
                            class="form-control"
                            value="<?= htmlspecialchars($data['tgl_lahir']); ?>">

                    </div>


                    <!-- NIK IBU -->

                    <div class="col-md-6">

                        <label class="form-label">

                            NIK Ibu
                            <span class="text-danger">*</span>

                        </label>

                        <input
                            type="text"
                            name="nik"
                            class="form-control"
                            value="<?= htmlspecialchars($data['nik']); ?>"
                            maxlength="50"
                            required>

                    </div>


                    <!-- TEMPAT LAHIR SUAMI -->

                    <div class="col-md-6">

                        <label class="form-label">

                            Tempat Lahir Suami

                        </label>

                        <input
                            type="text"
                            name="tempat_lahir_suami"
                            class="form-control"
                            value="<?= htmlspecialchars($data['tempat_lahir_suami']); ?>">

                    </div>


                    <!-- TANGGAL LAHIR SUAMI -->

                    <div class="col-md-6">

                        <label class="form-label">

                            Tanggal Lahir Suami

                        </label>

                        <input
                            type="date"
                            name="tgl_lahir_suami"
                            class="form-control"
                            value="<?= htmlspecialchars($data['tgl_lahir_suami']); ?>">

                    </div>


                    <!-- NIK SUAMI -->

                    <div class="col-md-6">

                        <label class="form-label">

                            NIK Suami

                        </label>

                        <input
                            type="text"
                            name="nik_suami"
                            class="form-control"
                            value="<?= htmlspecialchars($data['nik_suami']); ?>"
                            maxlength="50">

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
                            value="<?= htmlspecialchars($data['no_kk']); ?>"
                            maxlength="50"
                            required>

                    </div>


                    <!-- NO HP -->

                    <div class="col-md-6">

                        <label class="form-label">

                            No. HP

                        </label>

                        <input
                            type="text"
                            name="no_hp"
                            class="form-control"
                            value="<?= htmlspecialchars($data['no_hp']); ?>"
                            maxlength="50">

                    </div>


                    <!-- ALAMAT -->

                    <div class="col-12">

                        <label class="form-label">

                            Alamat

                        </label>

                        <textarea
                            name="alamat"
                            class="form-control"
                            rows="3"><?= htmlspecialchars($data['alamat']); ?></textarea>

                    </div>

                </div>

            </div>

        </div>


        <!-- =================================================
             PENDIDIKAN
        ================================================== -->

        <div class="edit-card">

            <div class="edit-card-body">

                <div class="edit-card-title">

                    <i class="bi bi-mortarboard me-2"></i>

                    Pendidikan

                </div>


                <div class="row g-3">


                    <!-- PENDIDIKAN IBU -->

                    <div class="col-md-6">

                        <label class="form-label">

                            Pendidikan Ibu

                        </label>

                        <select
                            name="pendidikan_ibu"
                            class="form-select">

                            <option value="">
                                Pilih Pendidikan
                            </option>

                            <option value="SD"
                                <?= ($data['pendidikan_ibu'] == 'SD') ? 'selected' : ''; ?>>
                                SD
                            </option>

                            <option value="SMP"
                                <?= ($data['pendidikan_ibu'] == 'SMP') ? 'selected' : ''; ?>>
                                SMP
                            </option>

                            <option value="SMA"
                                <?= ($data['pendidikan_ibu'] == 'SMA') ? 'selected' : ''; ?>>
                                SMA
                            </option>

                            <option value="D3"
                                <?= ($data['pendidikan_ibu'] == 'D3') ? 'selected' : ''; ?>>
                                D3
                            </option>

                            <option value="S1"
                                <?= ($data['pendidikan_ibu'] == 'S1') ? 'selected' : ''; ?>>
                                S1
                            </option>

                            <option value="S2"
                                <?= ($data['pendidikan_ibu'] == 'S2') ? 'selected' : ''; ?>>
                                S2
                            </option>

                            <option value="S3"
                                <?= ($data['pendidikan_ibu'] == 'S3') ? 'selected' : ''; ?>>
                                S3
                            </option>

                        </select>

                    </div>


                    <!-- PENDIDIKAN SUAMI -->

                    <div class="col-md-6">

                        <label class="form-label">

                            Pendidikan Suami

                        </label>

                        <select
                            name="pendidikan_suami"
                            class="form-select">

                            <option value="">
                                Pilih Pendidikan
                            </option>

                            <option value="SD"
                                <?= ($data['pendidikan_suami'] == 'SD') ? 'selected' : ''; ?>>
                                SD
                            </option>

                            <option value="SMP"
                                <?= ($data['pendidikan_suami'] == 'SMP') ? 'selected' : ''; ?>>
                                SMP
                            </option>

                            <option value="SMA"
                                <?= ($data['pendidikan_suami'] == 'SMA') ? 'selected' : ''; ?>>
                                SMA
                            </option>

                            <option value="D3"
                                <?= ($data['pendidikan_suami'] == 'D3') ? 'selected' : ''; ?>>
                                D3
                            </option>

                            <option value="S1"
                                <?= ($data['pendidikan_suami'] == 'S1') ? 'selected' : ''; ?>>
                                S1
                            </option>

                            <option value="S2"
                                <?= ($data['pendidikan_suami'] == 'S2') ? 'selected' : ''; ?>>
                                S2
                            </option>

                            <option value="S3"
                                <?= ($data['pendidikan_suami'] == 'S3') ? 'selected' : ''; ?>>
                                S3
                            </option>

                        </select>

                    </div>

                </div>

            </div>

        </div>


        <!-- =================================================
             DATA TAMBAHAN
        ================================================== -->

        <div class="edit-card">

            <div class="edit-card-body">

                <div class="edit-card-title">

                    <i class="bi bi-card-checklist me-2"></i>

                    Data Tambahan

                </div>


                <div class="row g-3">


                    <!-- BPJS -->

                    <div class="col-md-6">

                        <label class="form-label">

                            BPJS / KIS

                        </label>

                        <input
                            type="text"
                            name="bpjs"
                            class="form-control"
                            value="<?= htmlspecialchars($data['bpjs']); ?>"
                            maxlength="50"
                            placeholder="Masukkan nomor BPJS / KIS">

                    </div>


                    <!-- GOL DARAH -->

                    <div class="col-md-6">

                        <label class="form-label">

                            Golongan Darah

                        </label>

                        <select
                            name="gol_darah"
                            class="form-select">

                            <option value="">
                                Pilih Golongan Darah
                            </option>

                            <option value="A"
                                <?= ($data['gol_darah'] == 'A') ? 'selected' : ''; ?>>
                                A
                            </option>

                            <option value="B"
                                <?= ($data['gol_darah'] == 'B') ? 'selected' : ''; ?>>
                                B
                            </option>

                            <option value="AB"
                                <?= ($data['gol_darah'] == 'AB') ? 'selected' : ''; ?>>
                                AB
                            </option>

                            <option value="O"
                                <?= ($data['gol_darah'] == 'O') ? 'selected' : ''; ?>>
                                O
                            </option>

                              <option value="Tidak Diketahui"
                                <?= ($data['gol_darah'] == 'O') ? 'selected' : ''; ?>>
                                Tidak Ketahui
                            </option>

                        </select>

                    </div>

                </div>

            </div>

        </div>


        <!-- =================================================
             BUTTON
        ================================================== -->

        <div class="d-flex justify-content-end gap-2 mb-4">

            <a
                href="rekam-medis-kebidanan.php"
                class="btn-kembali">

                <i class="bi bi-x-lg me-1"></i>

                Batal

            </a>


            <button
                type="submit"
                name="update"
                value="1"
                class="btn-simpan">

                <i class="bi bi-save me-1"></i>

                Simpan Perubahan

            </button>

        </div>


    </form>

</div>

</div>


<?php

require "../template/footer.php";

?>