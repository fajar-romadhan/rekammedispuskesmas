<?php

session_start();

require "../template/rbac.php";

// Hanya dokter dan bidan yang boleh edit rekam medis
cekAkses([ROLE_DOKTER, ROLE_BIDAN]);

require "../config.php";

$title = "Edit Data - rekammedispuskesmas";

require "../template/header.php";
require "../template/navbar.php";
require "../template/sidebar.php";


// ======================================================
// CEK ID REKAM MEDIS
// ======================================================

if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "<script>
            alert('ID rekam medis tidak ditemukan!');
            window.location='index.php';
          </script>";
    exit();
}

$id = mysqli_real_escape_string($koneksi, $_GET['id']);


// ======================================================
// AMBIL DATA REKAM MEDIS
// ======================================================

$sqlrm = "SELECT 
            tbl_rekammedis.*,
            tbl_pasien.no_rm AS no_rm,
            tbl_pasien.nama,
            tbl_pasien.alamat AS alamatpasien,
            tbl_user.fullname
          FROM tbl_rekammedis
          INNER JOIN tbl_pasien 
              ON tbl_rekammedis.id_pasien = tbl_pasien.id
          INNER JOIN tbl_user 
              ON tbl_rekammedis.id_dokter = tbl_user.userid
          WHERE tbl_rekammedis.id_rm = '$id'";

$queryrm = mysqli_query($koneksi, $sqlrm);

if (!$queryrm) {
    die("Query error: " . mysqli_error($koneksi));
}

if (mysqli_num_rows($queryrm) == 0) {
    echo "<script>
            alert('Data rekam medis tidak ditemukan!');
            window.location='index.php';
          </script>";
    exit();
}

$rm = mysqli_fetch_assoc($queryrm);

?>

<style>

/* ======================================================
   FORM EDIT REKAM MEDIS
   ====================================================== */

.form-card {
    background: #ffffff;
    border: none;
    border-radius: 18px;
    box-shadow: 0 5px 25px rgba(0, 0, 0, 0.07);
    overflow: hidden;
}

.form-card-header {
    background: linear-gradient(135deg, #7571f9, #5f5ae0);
    color: white;
    padding: 20px 25px;
}

.form-card-header h5 {
    margin: 0;
    font-weight: 600;
}

.form-card-body {
    padding: 30px;
}

.form-label {
    font-weight: 600;
    color: #363454;
    margin-bottom: 8px;
}

.form-control,
.form-select {
    border: 1px solid #d1d0dd;
    border-radius: 10px;
    padding: 10px 13px;
    transition: all 0.2s ease;
}

.form-control:focus,
.form-select:focus {
    border-color: #7571f9;
    box-shadow: 0 0 0 3px rgba(117, 113, 249, 0.10);
}

.readonly-field {
    background-color: #f8f8fa !important;
    color: #696685;
}


/* ======================================================
   TOMBOL
   ====================================================== */

.btn-custom {
    border-radius: 9px;
    padding: 9px 18px;
    font-weight: 500;
}

.btn-back {
    color: #7571f9;
    font-weight: 500;
    text-decoration: none;
}

.btn-back:hover {
    color: #4a44c7;
}


/* ======================================================
   MODAL CARI PASIEN
   ====================================================== */

.modal-pasien .modal-content {
    border: none;
    border-radius: 18px;
    overflow: hidden;
    box-shadow: 0 15px 50px rgba(0, 0, 0, 0.18);
}

.modal-pasien .modal-header {
    background: linear-gradient(135deg, #7571f9, #5f5ae0);
    color: white;
    padding: 18px 25px;
    border: none;
}

.modal-pasien .modal-header h5 {
    margin: 0;
    font-weight: 600;
}

.modal-pasien .modal-body {
    padding: 25px;
}


/* Search Box */

.search-pasien-box {
    position: relative;
    margin-bottom: 20px;
}

.search-pasien-box i {
    position: absolute;
    left: 15px;
    top: 50%;
    transform: translateY(-50%);
    color: #696685;
    font-size: 18px;
}

#searchPasien {
    padding-left: 45px;
    padding-right: 45px;
    height: 45px;
    border-radius: 10px;
    border: 1px solid #d1d0dd;
}

#searchPasien:focus {
    border-color: #7571f9;
    box-shadow: 0 0 0 3px rgba(117, 113, 249, 0.10);
}

.clear-search {
    position: absolute;
    right: 12px;
    top: 50%;
    transform: translateY(-50%);
    border: none;
    background: transparent;
    color: #9a98b3;
    display: none;
}

.clear-search:hover {
    color: #dc3545;
}


/* ======================================================
   TABEL PASIEN
   ====================================================== */

.table-pasien-wrapper {
    border: 1px solid #ebeaf0;
    border-radius: 12px;
    overflow: hidden;
}

.table-pasien {
    margin: 0;
}

.table-pasien thead th {
    background: #f2f2f7;
    color: #363454;
    font-size: 14px;
    font-weight: 600;
    padding: 13px 15px;
    border-bottom: 1px solid #ebeaf0;
    white-space: nowrap;
}

.table-pasien tbody td {
    padding: 13px 15px;
    vertical-align: middle;
    color: #484767;
}

.table-pasien tbody tr {
    transition: 0.15s ease;
}

.table-pasien tbody tr:hover {
    background-color: #f9f8ff;
}

.table-pasien tbody tr.hidden-row {
    display: none;
}

.badge-rm {
    background: #eff1ff;
    color: #2417d3;
    border: 1px solid #b2bcff;
    padding: 5px 9px;
    border-radius: 7px;
    font-size: 12px;
    font-weight: 600;
}


/* Tombol pilih */

.btn-pilih-pasien {
    width: 38px;
    height: 38px;
    border-radius: 9px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s ease;
}

.btn-pilih-pasien:hover {
    transform: translateY(-2px);
}


/* ======================================================
   HASIL SEARCH KOSONG
   ====================================================== */

#noResult {
    display: none;
    text-align: center;
    padding: 35px 15px;
    color: #696685;
}

#noResult i {
    font-size: 40px;
    color: #9a98b3;
    display: block;
    margin-bottom: 10px;
}


/* ======================================================
   RESPONSIVE
   ====================================================== */

@media (max-width: 768px) {

    .form-card-body {
        padding: 20px;
    }

    .border-start {
        border-left: none !important;
        border-top: 1px solid #dedfe6;
        padding-top: 25px;
        margin-top: 20px;
    }

    .modal-pasien .modal-body {
        padding: 15px;
    }

    .table-pasien {
        min-width: 700px;
    }

}

</style>


<div class="page-content-wrap">

    <!-- ==================================================
         HEADER
         ================================================== -->

    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap
                align-items-center pt-3 pb-3 mb-4 border-bottom">

        <div>
            <h1 class="h2 mb-1">
                <i class="bi bi-pencil-square text-primary me-2"></i>
                Edit Data Perekaman
            </h1>

            <small class="text-muted">
                Perbarui informasi rekam medis pasien
            </small>
        </div>

        <a href="<?= $main_url ?>rekammedis"
           class="btn-back">

            <i class="bi bi-arrow-left me-1"></i>
            Kembali

        </a>

    </div>


    <!-- ==================================================
         FORM
         ================================================== -->

    <div class="form-card mb-5">

        <div class="form-card-header">

            <h5>
                <i class="bi bi-clipboard2-pulse me-2"></i>
                Informasi Rekam Medis
            </h5>

        </div>


        <div class="form-card-body">

            <form action="proses-data.php" method="post">

                <input type="hidden"
                       name="id_rm"
                       value="<?= htmlspecialchars($rm['id_rm']); ?>">


                <div class="row g-4">

                    <!-- ==================================================
                         KOLOM KIRI
                         ================================================== -->

                    <div class="col-lg-6 pe-lg-4">

                        <!-- NO RM -->

                        <div class="mb-4">

                            <label class="form-label">
                                <i class="bi bi-card-text me-1 text-primary"></i>
                                No. Rekam Medis
                            </label>

                            <input type="text"
                                   name="no_rm"
                                   class="form-control readonly-field"
                                   value="<?= htmlspecialchars($rm['no_rm']); ?>"
                                   readonly>

                        </div>


                        <!-- TANGGAL -->

                        <div class="mb-4">

                            <label for="tgl" class="form-label">
                                <i class="bi bi-calendar3 me-1 text-primary"></i>
                                Tanggal Pemeriksaan
                            </label>

                            <input type="date"
                                   name="tgl"
                                   class="form-control"
                                   id="tgl"
                                   value="<?= htmlspecialchars($rm['tgl_rm']); ?>"
                                   required>

                        </div>


                        <!-- PASIEN -->

                        <div class="mb-4">

                            <label class="form-label">
                                <i class="bi bi-person-vcard me-1 text-primary"></i>
                                Pasien
                            </label>

                            <div class="input-group">

                                <input type="text"
                                       class="form-control readonly-field"
                                       id="pasien_id"
                                       name="id"
                                       placeholder="ID Pasien"
                                       value="<?= htmlspecialchars($rm['id_pasien']); ?>"
                                       readonly>

                                <button class="btn btn-primary"
                                        type="button"
                                        data-toggle="modal"
                                        data-target="#modalPasien">

                                    <i class="bi bi-search me-1"></i>
                                    Cari

                                </button>

                            </div>


                            <input type="text"
                                   id="namaPasien"
                                   class="form-control readonly-field mt-2"
                                   placeholder="Nama pasien"
                                   value="<?= htmlspecialchars($rm['nama']); ?>"
                                   readonly>


                            <textarea id="alamatPasien"
                                      class="form-control readonly-field mt-2"
                                      placeholder="Alamat pasien"
                                      rows="2"
                                      readonly><?= htmlspecialchars($rm['alamatpasien']); ?></textarea>

                        </div>


                        <!-- KELUHAN -->

                        <div class="mb-4">

                            <label for="keluhan" class="form-label">

                                <i class="bi bi-chat-left-text me-1 text-primary"></i>
                                Keluhan

                            </label>

                            <textarea name="keluhan"
                                      id="keluhan"
                                      class="form-control"
                                      rows="4"
                                      placeholder="Masukkan keluhan pasien..."><?= htmlspecialchars($rm['keluhan']); ?></textarea>

                        </div>

                    </div>


                    <!-- ==================================================
                         KOLOM KANAN
                         ================================================== -->

                    <div class="col-lg-6 border-start ps-lg-4">

                        <!-- DOKTER -->

                        <div class="mb-4">

                            <label for="dokter" class="form-label">

                                <i class="bi bi-person-badge me-1 text-primary"></i>
                                Dokter

                            </label>

                            <select name="dokter"
                                    id="dokter"
                                    class="form-select"
                                    required>

                                <option value="">
                                    -- Pilih Dokter --
                                </option>

                                <?php

                                $queryDokter = mysqli_query(
                                    $koneksi,
                                    "SELECT *
                                     FROM tbl_user
                                     WHERE jabatan = 3
                                     ORDER BY fullname ASC"
                                );

                                while ($data = mysqli_fetch_assoc($queryDokter)) {

                                ?>

                                    <option value="<?= $data['userid']; ?>"
                                        <?= $data['userid'] == $rm['id_dokter'] ? 'selected' : ''; ?>>

                                        <?= htmlspecialchars($data['fullname']); ?>

                                    </option>

                                <?php } ?>

                            </select>

                        </div>


                        <!-- DIAGNOSA -->

                        <div class="mb-4">

                            <label for="diagnosa" class="form-label">

                                <i class="bi bi-clipboard2-pulse me-1 text-primary"></i>
                                Diagnosa

                            </label>

                            <textarea name="diagnosa"
                                      id="diagnosa"
                                      class="form-control"
                                      rows="4"
                                      placeholder="Masukkan hasil diagnosa dokter..."><?= htmlspecialchars($rm['diagnosa']); ?></textarea>

                        </div>


                        <!-- OBAT -->

                        <div class="mb-4">

                            <label for="tokenfield" class="form-label">

                                <i class="bi bi-capsule me-1 text-primary"></i>
                                Obat

                            </label>

                            <input type="text"
                                   name="obat"
                                   id="tokenfield"
                                   class="form-control"
                                   value="<?= htmlspecialchars($rm['resep_obat']); ?>"
                                   placeholder="Ketik nama obat...">

                            <small class="text-muted">
                                Pisahkan beberapa obat dengan koma.
                            </small>

                        </div>


                        <!-- TINDAKAN / LAYANAN UNTUK TAGIHAN -->

                        <div class="mb-4">

                            <label for="tokenfieldTindakan" class="form-label">

                                <i class="bi bi-receipt me-1 text-primary"></i>
                                Tindakan / Layanan (untuk tagihan)

                            </label>

                            <input type="text"
                                   name="tindakan_billing"
                                   id="tokenfieldTindakan"
                                   class="form-control"
                                   value="<?= htmlspecialchars($rm['tindakan_billing'] ?? ''); ?>"
                                   placeholder="Pilih tindakan/layanan berbayar...">

                            <small class="text-muted">
                                Dipakai untuk menghitung ulang tagihan pembayaran pasien.
                            </small>

                        </div>


                        <!-- BUTTON -->

                        <div class="d-flex gap-2 mt-4">

                            <button type="reset"
                                    class="btn btn-outline-danger btn-custom">

                                <i class="bi bi-arrow-counterclockwise me-1"></i>
                                Reset

                            </button>


                            <button type="submit"
                                    name="update"
                                    class="btn btn-primary btn-custom">

                                <i class="bi bi-save me-1"></i>
                                Update Data

                            </button>

                        </div>

                    </div>

                </div>

            </form>

        </div>

    </div>

</div>


<!-- ======================================================
     MODAL CARI PASIEN
     ====================================================== -->

<div class="modal fade modal-pasien"
     id="modalPasien"
     tabindex="-1"
     aria-labelledby="modalPasienLabel"
     aria-hidden="true">

    <div class="modal-dialog modal-xl modal-dialog-centered">

        <div class="modal-content">


            <!-- HEADER MODAL -->

            <div class="modal-header">

                <h5 class="modal-title" id="modalPasienLabel">

                    <i class="bi bi-search me-2"></i>
                    Cari Pasien

                </h5>

                <button type="button"
                        class="btn-close btn-close-white"
                        data-dismiss="modal">
                </button>

            </div>


            <!-- BODY MODAL -->

            <div class="modal-body">


                <!-- ==================================================
                     SEARCH PASIEN
                     ================================================== -->

                <div class="search-pasien-box">

                    <i class="bi bi-search"></i>

                    <input type="text"
                           id="searchPasien"
                           class="form-control"
                           placeholder="Cari berdasarkan nama, ID pasien, No. RM, atau alamat..."
                           autocomplete="off">

                    <button type="button"
                            class="clear-search"
                            id="clearSearch"
                            title="Hapus pencarian">

                        <i class="bi bi-x-circle-fill"></i>

                    </button>

                </div>


                <!-- INFO SEARCH -->

                <div class="d-flex justify-content-between align-items-center mb-3">

                    <small class="text-muted">

                        <i class="bi bi-info-circle me-1"></i>
                        Ketik nama pasien untuk mencari data dengan cepat.

                    </small>

                    <small class="text-muted" id="jumlahPasien">

                        Menampilkan semua pasien

                    </small>

                </div>


                <!-- ==================================================
                     TABEL
                     ================================================== -->

                <div class="table-responsive table-pasien-wrapper">

                    <table class="table table-pasien align-middle"
                           id="tabelPasien">

                        <thead>

                            <tr>

                                <th width="60">No</th>

                                <th>ID Pasien</th>

                                <th>No. RM</th>

                                <th>Nama</th>

                                <th>Alamat</th>

                                <th class="text-center" width="90">
                                    Pilih
                                </th>

                            </tr>

                        </thead>


                        <tbody>

                        <?php

                        $no = 1;

                        $queryPasien = mysqli_query(
                            $koneksi,
                            "SELECT *
                             FROM tbl_pasien
                             ORDER BY nama ASC"
                        );

                        while ($pasien = mysqli_fetch_assoc($queryPasien)) {

                        ?>

                            <tr class="data-pasien">

                                <td>
                                    <?= $no++; ?>
                                </td>


                                <td>

                                    <span class="badge-rm">

                                        <?= htmlspecialchars($pasien['id']); ?>

                                    </span>

                                </td>


                                <td>

                                    <span class="badge-rm">

                                        <?= htmlspecialchars($pasien['no_rm']); ?>

                                    </span>

                                </td>


                                <td>

                                    <strong>
                                        <?= htmlspecialchars($pasien['nama']); ?>
                                    </strong>

                                </td>


                                <td>

                                    <?= htmlspecialchars($pasien['alamat']); ?>

                                </td>


                                <td class="text-center">

                                    <button type="button"
                                            title="Pilih pasien"
                                            class="btn btn-primary btn-pilih-pasien cekPasien"

                                            data-id="<?= htmlspecialchars($pasien['id']); ?>"

                                            data-norm="<?= htmlspecialchars($pasien['no_rm']); ?>"

                                            data-namapasien="<?= htmlspecialchars($pasien['nama']); ?>"

                                            data-address="<?= htmlspecialchars($pasien['alamat']); ?>">

                                        <i class="bi bi-check-lg"></i>

                                    </button>

                                </td>

                            </tr>

                        <?php } ?>

                        </tbody>

                    </table>


                    <!-- HASIL TIDAK DITEMUKAN -->

                    <div id="noResult">

                        <i class="bi bi-person-x"></i>

                        <strong>Pasien tidak ditemukan</strong>

                        <div class="small mt-1">

                            Coba gunakan nama, ID pasien, atau No. RM yang berbeda.

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>


<!-- ======================================================
     TOKENFIELD
     ====================================================== -->

<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-tokenfield/0.12.0/bootstrap-tokenfield.js"></script>


<script>

$(document).ready(function () {


    // ==================================================
    // PILIH PASIEN
    // ==================================================

    $(document).on('click', '.cekPasien', function () {

        let pasienID      = $(this).data('id');
        let pasienRM      = $(this).data('norm');
        let pasienName    = $(this).data('namapasien');
        let pasienAddress = $(this).data('address');


        $('#pasien_id').val(pasienID);

        $('input[name="no_rm"]').val(pasienRM);

        $('#namaPasien').val(pasienName);

        $('#alamatPasien').val(pasienAddress);


        // Bersihkan pencarian
        $('#searchPasien').val('');
        $('#clearSearch').hide();

        $('.data-pasien').removeClass('hidden-row');

        $('#noResult').hide();

        $('#jumlahPasien').text('Menampilkan semua pasien');


        // Tutup modal
        $('#modalPasien').modal('hide');

    });



    // ==================================================
    // SEARCH PASIEN
    // ==================================================

    $('#searchPasien').on('keyup', function () {

        let keyword = $(this).val()
                              .toLowerCase()
                              .trim();

        let jumlah = 0;


        // Tombol clear
        if (keyword !== '') {

            $('#clearSearch').show();

        } else {

            $('#clearSearch').hide();

        }


        $('.data-pasien').each(function () {

            let rowText = $(this).text()
                                 .toLowerCase();


            if (rowText.indexOf(keyword) !== -1) {

                $(this).removeClass('hidden-row');

                jumlah++;

            } else {

                $(this).addClass('hidden-row');

            }

        });


        // Tampilkan jumlah hasil
        if (keyword === '') {

            $('#jumlahPasien').text('Menampilkan semua pasien');

        } else {

            $('#jumlahPasien').text(
                jumlah + ' pasien ditemukan'
            );

        }


        // Tidak ada hasil
        if (jumlah === 0 && keyword !== '') {

            $('#noResult').show();

        } else {

            $('#noResult').hide();

        }

    });



    // ==================================================
    // CLEAR SEARCH
    // ==================================================

    $('#clearSearch').on('click', function () {

        $('#searchPasien').val('').focus();

        $('.data-pasien').removeClass('hidden-row');

        $('#clearSearch').hide();

        $('#noResult').hide();

        $('#jumlahPasien').text(
            'Menampilkan semua pasien'
        );

    });



    // ==================================================
    // FOKUS SEARCH SAAT MODAL DIBUKA
    // ==================================================

    $('#modalPasien').on('shown.bs.modal', function () {

        $('#searchPasien').focus();

    });



    // ==================================================
    // DATA OBAT
    // ==================================================

    <?php

    $nmObat = [];

    $queryObat = mysqli_query(
        $koneksi,
        "SELECT * FROM tbl_obat WHERE kategori = 'Obat' AND stok > 0 ORDER BY nama ASC"
    );

    while ($data = mysqli_fetch_assoc($queryObat)) {

        $nmObat[] = $data['nama'];

    }

    $nmTindakan = [];

    $queryTindakan = mysqli_query(
        $koneksi,
        "SELECT * FROM tbl_obat WHERE kategori = 'Tindakan' ORDER BY nama ASC"
    );

    while ($data = mysqli_fetch_assoc($queryTindakan)) {

        $nmTindakan[] = $data['nama'];

    }

    ?>


    // ==================================================
    // TOKENFIELD OBAT
    // ==================================================

    $('#tokenfield').tokenfield({

        autocomplete: {

            source: <?= json_encode($nmObat); ?>,

            delay: 100

        },

        showAutocompleteOnFocus: true

    });


    // ==================================================
    // TOKENFIELD TINDAKAN / LAYANAN (TAGIHAN)
    // ==================================================

    $('#tokenfieldTindakan').tokenfield({

        autocomplete: {

            source: <?= json_encode($nmTindakan); ?>,

            delay: 100

        },

        showAutocompleteOnFocus: true

    });


});

</script>


<?php

require "../template/footer.php";

?>