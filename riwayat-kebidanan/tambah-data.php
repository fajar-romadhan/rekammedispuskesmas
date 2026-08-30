<?php

session_start();

require "../template/rbac.php";

// Hanya Bidan
cekAkses([ROLE_BIDAN]);

require "../config.php";

$title = "Tambah Rekam Medis Kebidanan";

require "../template/header.php";
require "../template/navbar.php";
require "../template/sidebar.php";


// DATA PASIEN
$pasien = mysqli_query($koneksi, "
    SELECT id, no_rm, nama
    FROM tbl_pasien
    ORDER BY nama ASC
");

?>

<div class="page-content-wrap">

    <div class="d-flex justify-content-between
                align-items-center
                pt-3 pb-2 mb-3 border-bottom">

        <h1 class="h2">Tambah Rekam Medis Kebidanan</h1>

    </div>


    <div class="card">

        <div class="card-body">

            <form action="proses-data.php" method="POST">

                <!-- PASIEN -->

                <div class="mb-3">

                    <label class="form-label">
                        Pasien
                    </label>

                    <select name="id_pasien"
                            class="form-select"
                            required>

                        <option value="">
                            -- Pilih Pasien --
                        </option>

                        <?php while ($p = mysqli_fetch_assoc($pasien)) { ?>

                            <option value="<?= $p['id']; ?>">

                                <?= htmlspecialchars($p['no_rm']); ?>
                                -
                                <?= htmlspecialchars($p['nama']); ?>

                            </option>

                        <?php } ?>

                    </select>

                </div>


                <!-- TANGGAL -->

                <div class="mb-3">

                    <label class="form-label">
                        Tanggal Pemeriksaan
                    </label>

                    <input type="date"
                           name="tgl_rm"
                           class="form-control"
                           value="<?= date('Y-m-d'); ?>"
                           required>

                </div>


                <!-- KELUHAN -->

                <div class="mb-3">

                    <label class="form-label">
                        Keluhan
                    </label>

                    <textarea name="keluhan"
                              class="form-control"
                              rows="3"></textarea>

                </div>


                <!-- HASIL PEMERIKSAAN -->

                <div class="mb-3">

                    <label class="form-label">
                        Hasil Pemeriksaan
                    </label>

                    <textarea name="hasil_pemeriksaan"
                              class="form-control"
                              rows="3"></textarea>

                </div>


                <!-- DIAGNOSA -->

                <div class="mb-3">

                    <label class="form-label">
                        Diagnosa
                    </label>

                    <textarea name="diagnosa"
                              class="form-control"
                              rows="3"></textarea>

                </div>


                <!-- TINDAKAN -->

                <div class="mb-3">

                    <label class="form-label">
                        Tindakan
                    </label>

                    <textarea name="tindakan"
                              class="form-control"
                              rows="3"></textarea>

                </div>


                <!-- KETERANGAN -->

                <div class="mb-3">

                    <label class="form-label">
                        Keterangan
                    </label>

                    <textarea name="keterangan"
                              class="form-control"
                              rows="3"></textarea>

                </div>


                <button type="submit"
                        name="simpan"
                        class="btn btn-primary">

                    <i class="bi bi-save"></i>
                    Simpan

                </button>

                <a href="index.php"
                   class="btn btn-secondary">

                    Kembali

                </a>

            </form>

        </div>

    </div>

</div>

<?php require "../template/footer.php"; ?>