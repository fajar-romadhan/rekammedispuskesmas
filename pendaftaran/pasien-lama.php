<?php

session_start();

require "../template/rbac.php";

// Role yang boleh mendaftarkan pasien
cekAkses([ROLE_ADMIN, ROLE_PETUGAS]);

require "../config.php";

$title = "Pendaftaran Pasien Lama";

require "../template/header.php";
require "../template/navbar.php";
require "../template/sidebar.php";


if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "<script>
        alert('Data pasien tidak ditemukan!');
        window.location='index.php';
    </script>";
    exit();
}

$id = mysqli_real_escape_string($koneksi, $_GET['id']);

// Halaman ini khusus pendaftaran ke Poli Umum (lihat catatan di
// pendaftaran/index.php) -- Kebidanan didaftarkan lewat
// petugas/pendaftaran-kebidanan.php.
$poliTujuan = 'Umum';
$poliLabel  = 'Poli Umum (Dokter)';

$query = mysqli_query($koneksi, "
    SELECT *
    FROM tbl_pasien
    WHERE id = '$id'
");

$data = mysqli_fetch_assoc($query);

if (!$data) {
    echo "<script>
        alert('Data pasien tidak ditemukan!');
        window.location='index.php';
    </script>";
    exit();
}

// Umur pasien, dipakai untuk menentukan apakah field Tinggi Badan
// perlu ditampilkan (khusus pasien anak-anak).
$umurPasien = (new DateTime($data['tgl_lahir']))
    ->diff(new DateTime())
    ->y;

$isAnak = $umurPasien < 18;

?>

<div class="page-content-wrap">

    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap
                align-items-center pt-3 pb-2 mb-3 border-bottom">

        <h1 class="h2">
            Pendaftaran Pasien Lama
            &mdash; <?= htmlspecialchars($poliLabel); ?>
        </h1>

    </div>


    <div class="card">

        <div class="card-header">
            <i class="bi bi-person-check"></i>
            Pendaftaran Pasien Lama
        </div>

        <div class="card-body">

            <form action="proses-pendaftaran.php" method="POST">

                <input type="hidden"
                       name="id_pasien"
                       value="<?= htmlspecialchars($data['id']); ?>">


                <div class="row mb-3">

                    <div class="col-md-6">

                        <label class="form-label">
                            No RM
                        </label>

                        <input type="text"
                               class="form-control"
                               value="<?= htmlspecialchars($data['no_rm']); ?>"
                               readonly>

                    </div>


                    <div class="col-md-6">

                        <label class="form-label">
                            Nama Pasien
                        </label>

                        <input type="text"
                               class="form-control"
                               value="<?= htmlspecialchars($data['nama']); ?>"
                               readonly>

                    </div>

                </div>


                <div class="row mb-3">

                    <div class="col-md-6">

                        <label class="form-label">
                            Tanggal Lahir
                        </label>

                        <input type="text"
                               class="form-control"
                               value="<?= date('d-m-Y', strtotime($data['tgl_lahir'])); ?>"
                               readonly>

                    </div>


                    <div class="col-md-6">

                        <label class="form-label">
                            Jenis Kelamin
                        </label>

                        <input type="text"
                               class="form-control"
                               value="<?= $data['gender'] == 'P' ? 'Pria' : 'Wanita'; ?>"
                               readonly>

                    </div>

                </div>


                <div class="row mb-3">

                    <div class="col-md-6">

                        <label class="form-label">
                            Poli Tujuan
                        </label>

                        <!-- Poli tujuan sudah ditentukan otomatis dari menu
                             yang dipakai (Daftarkan Pasien Umum / Bidan),
                             tidak perlu/tidak bisa dipilih ulang manual. -->

                        <input type="text"
                               class="form-control"
                               value="<?= htmlspecialchars($poliLabel); ?>"
                               readonly>

                        <input type="hidden"
                               name="poli"
                               value="<?= htmlspecialchars($poliTujuan); ?>">

                    </div>

                </div>


                <div class="row mb-3">

                    <div class="col-md-6">

                        <label class="form-label">
                            Jenis Pembayaran
                        </label>

                        <select name="jenis_pembayaran"
                                class="form-select"
                                required>

                            <option value="">-- Pilih Pembayaran --</option>

                            <option value="Umum"
                                <?= ($data['jenis_pembayaran'] ?? '') == 'Umum' ? 'selected' : ''; ?>>
                                Umum
                            </option>

                            <option value="BPJS"
                                <?= ($data['jenis_pembayaran'] ?? '') == 'BPJS' ? 'selected' : ''; ?>>
                                BPJS
                            </option>

                            <option value="Asuransi"
                                <?= ($data['jenis_pembayaran'] ?? '') == 'Asuransi' ? 'selected' : ''; ?>>
                                Asuransi
                            </option>

                        </select>

                    </div>


                    <div class="col-md-6">

                        <label class="form-label">
                            No. Asuransi / BPJS
                        </label>

                        <input type="text"
                               name="no_asuransi"
                               class="form-control"
                               value="<?= htmlspecialchars($data['no_asuransi'] ?? ''); ?>">

                    </div>

                </div>


                <hr>

                <h6 class="mb-3">
                    <i class="bi bi-clipboard2-pulse"></i>
                    Pemeriksaan Awal
                    <?php if ($isAnak) { ?>
                        <span class="app-chip app-chip-info">
                            Pasien Anak (<?= $umurPasien; ?> tahun)
                        </span>
                    <?php } ?>
                </h6>

                <div class="row mb-3">

                    <div class="col-md-4">

                        <label class="form-label">
                            Tekanan Darah (Tensi)
                        </label>

                        <input type="text"
                               name="tekanan_darah"
                               class="form-control"
                               placeholder="Contoh: 120/80">

                    </div>


                    <div class="col-md-4">

                        <label class="form-label">
                            Berat Badan (kg)
                        </label>

                        <input type="number"
                               step="0.1"
                               min="0"
                               name="berat_badan"
                               class="form-control"
                               placeholder="Contoh: 55.5">

                    </div>


                    <?php if ($isAnak) { ?>

                    <div class="col-md-4">

                        <label class="form-label">
                            Tinggi Badan (cm)
                        </label>

                        <input type="number"
                               step="0.1"
                               min="0"
                               name="tinggi_badan"
                               class="form-control"
                               placeholder="Contoh: 110">

                    </div>

                    <?php } ?>

                </div>


                <div class="mt-4">

                    <button type="submit"
                            name="daftar"
                            class="btn btn-primary">

                        <i class="bi bi-person-check"></i>
                        Daftarkan Pasien

                    </button>


                    <a href="index.php"
                       class="btn btn-secondary">

                        <i class="bi bi-arrow-left"></i>
                        Kembali

                    </a>

                </div>

            </form>

        </div>

    </div>

</div>


<?php
require "../template/footer.php";
?>