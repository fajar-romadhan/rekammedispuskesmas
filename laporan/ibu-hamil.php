<?php

session_start();

require "../template/rbac.php";

// Hanya Kepala Puskesmas
cekAkses([ROLE_KEPALA]);

require "../config.php";

$title = "Laporan Ibu Hamil - rekammedispuskesmas";

require "../template/header.php";
require "../template/navbar.php";
require "../template/sidebar.php";


/*
|--------------------------------------------------------------------------
| AMBIL DATA LAPORAN IBU HAMIL
|--------------------------------------------------------------------------
|
| Relasi:
|
| tbl_pemeriksaan_ibu_hamil.ibu_hamil_id
|          ↓
| tbl_ibu_hamil.id
|
|--------------------------------------------------------------------------
*/

$query = mysqli_query($koneksi, "
    SELECT

        p.no_registrasi,
        p.tanggal_pemeriksaan,
        p.usia_kehamilan,
        p.berat_badan,
        p.tekanan_darah,
        p.tfu,
        p.djj,
        p.hasil,
        p.keterangan,

        i.nik,
        i.no_kk,
        i.nama_ibu,
        i.nama_suami,
        i.no_hp,
        i.hpl,
        i.gravida,
        i.para,
        i.abortus

    FROM tbl_pemeriksaan_ibu_hamil AS p

    INNER JOIN tbl_ibu_hamil AS i
        ON p.ibu_hamil_id = i.id

    ORDER BY
        p.tanggal_pemeriksaan DESC,
        p.id DESC
");

if (!$query) {
    die("Query laporan ibu hamil gagal: " . mysqli_error($koneksi));
}


// ======================================================
// HITUNG JUMLAH DATA
// ======================================================

$queryJumlah = mysqli_query($koneksi, "
    SELECT COUNT(*) AS total FROM tbl_pemeriksaan_ibu_hamil
");

$dataJumlah = mysqli_fetch_assoc($queryJumlah);
$totalIbuHamil = $dataJumlah['total'];

?>

<style>

/* =====================================================
   LAPORAN IBU HAMIL
===================================================== */

.ibuhamil-container {
    padding-bottom: 40px;
}


/* =====================================================
   HEADER HALAMAN
===================================================== */

.ibuhamil-header {
    background: #ffffff;
    border: 1px solid #e8e8f3;
    border-radius: 16px;
    padding: 22px 25px;
    margin-top: 20px;
    margin-bottom: 20px;
    box-shadow: 0 5px 18px rgba(0, 0, 0, 0.04);
}

.ibuhamil-title {
    display: flex;
    align-items: center;
    gap: 13px;
}

.ibuhamil-title-icon {
    width: 45px;
    height: 45px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 12px;
    background: #eae9ff;
    color: #7571f9;
    font-size: 22px;
}

.ibuhamil-title h1 {
    font-size: 24px;
    font-weight: 700;
    color: #262a38;
    margin: 0;
}

.ibuhamil-title p {
    font-size: 13px;
    color: #7b7c94;
    margin: 4px 0 0;
}


/* =====================================================
   CARD JUMLAH DATA
===================================================== */

.total-card {
    background: #ffffff;
    border: 1px solid #e8e8f3;
    border-radius: 15px;
    padding: 17px 20px;
    box-shadow: 0 5px 18px rgba(0, 0, 0, 0.04);
    height: 100%;
}

.total-icon {
    width: 42px;
    height: 42px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #eaf7ef;
    color: #198754;
    font-size: 20px;
}

.total-label {
    font-size: 12px;
    color: #7b7c94;
    margin-bottom: 2px;
}

.total-number {
    font-size: 23px;
    font-weight: 700;
    color: #262a38;
}


/* =====================================================
   SEARCH CARD
===================================================== */

.search-card {
    background: #ffffff;
    border: 1px solid #e8e8f3;
    border-radius: 15px;
    padding: 18px 20px;
    box-shadow: 0 5px 18px rgba(0, 0, 0, 0.04);
}

.search-label {
    font-size: 13px;
    font-weight: 600;
    color: #45465e;
    margin-bottom: 8px;
}

.search-wrapper {
    position: relative;
}

.search-wrapper .search-icon {
    position: absolute;
    left: 15px;
    top: 50%;
    transform: translateY(-50%);
    color: #7d7f95;
    font-size: 17px;
    z-index: 2;
}

#searchIbuHamil {
    height: 44px;
    border-radius: 10px;
    padding-left: 43px;
    padding-right: 40px;
    border: 1px solid #dcddea;
    font-size: 14px;
    transition: all 0.2s ease;
}

#searchIbuHamil:focus {
    border-color: #7571f9;
    box-shadow: 0 0 0 3px rgba(117, 113, 249, 0.10);
}


/* Tombol hapus search */

.clear-search {
    position: absolute;
    right: 12px;
    top: 50%;
    transform: translateY(-50%);
    border: none;
    background: transparent;
    color: #9b9cb1;
    display: none;
    cursor: pointer;
    z-index: 3;
}

.clear-search:hover {
    color: #dc3545;
}


/* =====================================================
   BUTTON PDF
===================================================== */

.pdf-card {
    background: #ffffff;
    border: 1px solid #e8e8f3;
    border-radius: 15px;
    padding: 18px 20px;
    box-shadow: 0 5px 18px rgba(0, 0, 0, 0.04);
}

.pdf-title {
    font-size: 13px;
    font-weight: 600;
    color: #45465e;
    margin-bottom: 12px;
}

.btn-pdf {
    border-radius: 9px;
    padding: 9px 15px;
    font-size: 13px;
    font-weight: 600;
    transition: all 0.2s ease;
}

.btn-pdf:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 12px rgba(220, 53, 69, 0.18);
}


/* =====================================================
   TABLE CARD
===================================================== */

.table-card {
    background: #ffffff;
    border: 1px solid #e8e8f3;
    border-radius: 16px;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
    overflow: hidden;
    margin-top: 20px;
}

.table-card-header {
    padding: 17px 20px;
    border-bottom: 1px solid #ededf4;
}

.table-card-title {
    margin: 0;
    font-size: 15px;
    font-weight: 700;
    color: #262a38;
}

.table-card-subtitle {
    margin: 4px 0 0;
    font-size: 12px;
    color: #8a8ba0;
}


/* =====================================================
   TABLE
===================================================== */

.table-wrapper {
    width: 100%;
    overflow-x: auto;
}

#tableIbuHamil {
    margin: 0;
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
}

#tableIbuHamil thead th {
    background: #f4f4fb;
    color: #45465e;
    font-size: 12px;
    font-weight: 700;
    padding: 15px 14px;
    border-bottom: 1px solid #e2e2ed;
    white-space: nowrap;
    vertical-align: middle;
}

#tableIbuHamil tbody td {
    padding: 14px;
    font-size: 13px;
    color: #53556d;
    border-bottom: 1px solid #ededf3;
    vertical-align: middle;
    white-space: nowrap;
}

#tableIbuHamil tbody tr {
    transition: all 0.2s ease;
}

#tableIbuHamil tbody tr:hover {
    background: #f8f8ff;
}


/* =====================================================
   BADGE
===================================================== */

.rm-badge {
    display: inline-block;
    padding: 5px 9px;
    border-radius: 7px;
    background: #f1f2f9;
    border: 1px solid #dcddec;
    color: #45465e;
    font-size: 11px;
    font-weight: 600;
    white-space: nowrap;
}

.nama-pasien {
    font-weight: 600;
    color: #262a38 !important;
}

.gpa-badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 6px 9px;
    border-radius: 7px;
    background: #eae9ff;
    color: #7571f9;
    font-size: 11px;
    font-weight: 600;
    white-space: nowrap;
}

.hasil-badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 6px 9px;
    border-radius: 7px;
    background: #eaf7ef;
    color: #198754;
    font-size: 11px;
    font-weight: 600;
    white-space: nowrap;
}

.teks-panjang {
    min-width: 160px;
    max-width: 260px;
    white-space: normal !important;
    line-height: 1.5;
}


/* =====================================================
   HASIL PENCARIAN
===================================================== */

.search-result-info {
    font-size: 12px;
    color: #7b7c94;
    margin-top: 8px;
}

#jumlahHasil {
    font-weight: 700;
    color: #7571f9;
}


/* =====================================================
   DATA TIDAK DITEMUKAN
===================================================== */

#dataKosong {
    display: none;
}

.empty-data {
    text-align: center;
    padding: 45px 20px !important;
    color: #7b7c94;
}

.empty-data i {
    display: block;
    font-size: 40px;
    color: #b7b8cc;
    margin-bottom: 10px;
}

.empty-data strong {
    display: block;
    color: #56576d;
    margin-bottom: 4px;
}


/* =====================================================
   RESPONSIVE
===================================================== */

@media (max-width: 768px) {

    .ibuhamil-header {
        padding: 18px;
    }

    .ibuhamil-title h1 {
        font-size: 20px;
    }

    .ibuhamil-title-icon {
        width: 40px;
        height: 40px;
        font-size: 19px;
    }

    .total-card {
        margin-top: 5px;
    }

    .pdf-card {
        margin-top: 5px;
    }

    #tableIbuHamil thead th,
    #tableIbuHamil tbody td {
        padding: 11px 10px;
        font-size: 12px;
    }

}


/* =====================================================
   ANIMASI
===================================================== */

.ibuhamil-header,
.total-card,
.search-card,
.pdf-card,
.table-card {
    animation: fadeInIbuHamil 0.35s ease;
}

@keyframes fadeInIbuHamil {

    from {
        opacity: 0;
        transform: translateY(6px);
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }

}

</style>


<div class="page-content-wrap">

<div class="ibuhamil-container">


    <!-- ==================================================
         HEADER
    =================================================== -->

    <div class="ibuhamil-header">

        <div class="ibuhamil-title">

            <div class="ibuhamil-title-icon">

                <i class="bi bi-person-heart"></i>

            </div>

            <div>

                <h1>
                    Laporan Pemeriksaan Ibu Hamil
                </h1>

                <p>
                    Data pemeriksaan kehamilan Puskesmas Desa Mendis.
                </p>

            </div>

        </div>

    </div>


    <!-- ==================================================
         INFORMASI & SEARCH
    =================================================== -->

    <div class="row g-3 mb-3">


        <!-- TOTAL DATA -->

        <div class="col-lg-4 col-md-5">

            <div class="total-card">

                <div class="d-flex align-items-center gap-3">

                    <div class="total-icon">

                        <i class="bi bi-clipboard2-pulse"></i>

                    </div>

                    <div>

                        <div class="total-label">
                            Total Pemeriksaan Ibu Hamil
                        </div>

                        <div class="total-number">

                            <?= $totalIbuHamil; ?>

                            <small
                                style="font-size:12px;
                                       font-weight:500;
                                       color:#7b7c94;">
                                Data
                            </small>

                        </div>

                    </div>

                </div>

            </div>

        </div>


        <!-- SEARCH -->

        <div class="col-lg-8 col-md-7">

            <div class="search-card">

                <div class="search-label">

                    <i class="bi bi-search me-1"></i>

                    Cari Data Pemeriksaan Ibu Hamil

                </div>

                <div class="search-wrapper">

                    <i class="bi bi-search search-icon"></i>

                    <input
                        type="text"
                        id="searchIbuHamil"
                        class="form-control"
                        placeholder="Cari nama ibu, nama suami, NIK, atau no. registrasi..."
                        autocomplete="off"
                    >

                    <button
                        type="button"
                        id="clearSearch"
                        class="clear-search"
                        title="Hapus pencarian">

                        <i class="bi bi-x-circle-fill"></i>

                    </button>

                </div>

                <div class="search-result-info">

                    Menampilkan
                    <span id="jumlahHasil">
                        <?= $totalIbuHamil; ?>
                    </span>
                    data pemeriksaan

                </div>

            </div>

        </div>

    </div>


    <!-- ==================================================
         TOMBOL PDF
    =================================================== -->

    <div class="pdf-card">

        <div class="pdf-title">

            <i class="bi bi-file-earmark-pdf me-1"></i>

            Cetak Laporan Ibu Hamil

        </div>


        <div class="d-flex flex-wrap gap-2">

            <!-- PDF MINGGUAN -->

            <a
                href="<?= $main_url ?>laporan/ibu-hamil-pdf.php?periode=mingguan"
                class="btn btn-danger btn-pdf"
                target="_blank"
            >

                <i class="bi bi-file-earmark-pdf me-1"></i>

                PDF Mingguan

            </a>


            <!-- PDF BULANAN -->

            <a
                href="<?= $main_url ?>laporan/ibu-hamil-pdf.php?periode=bulanan"
                class="btn btn-danger btn-pdf"
                target="_blank"
            >

                <i class="bi bi-file-earmark-pdf me-1"></i>

                PDF Bulanan

            </a>

        </div>

    </div>


    <!-- ==================================================
         TABLE
    =================================================== -->

    <div class="table-card">


        <!-- TABLE HEADER -->

        <div class="table-card-header">

            <h6 class="table-card-title">

                <i class="bi bi-table me-1"></i>

                Data Pemeriksaan Ibu Hamil

            </h6>

            <p class="table-card-subtitle">

                Daftar pemeriksaan kehamilan yang telah dicatat.

            </p>

        </div>


        <!-- TABLE -->

        <div class="table-wrapper">

            <table
                id="tableIbuHamil"
                class="table align-middle"
            >

                <thead>

                    <tr>

                        <th>No</th>
                        <th>Tanggal</th>
                        <th>No. Registrasi</th>
                        <th>NIK</th>
                        <th>Nama Ibu</th>
                        <th>Nama Suami</th>
                        <th>No. HP</th>
                        <th>G/P/A</th>
                        <th>HPL</th>
                        <th>Usia Kehamilan</th>
                        <th>BB</th>
                        <th>Tekanan Darah</th>
                        <th>TFU</th>
                        <th>DJJ</th>
                        <th>Hasil</th>
                        <th>Keterangan</th>

                    </tr>

                </thead>


                <tbody>

                    <?php

                    $no = 1;
                    $totalData = mysqli_num_rows($query);

                    if ($totalData > 0) :

                        while ($data = mysqli_fetch_assoc($query)) :

                    ?>

                    <tr class="data-ibuhamil">


                        <!-- NO -->

                        <td>
                            <?= $no++; ?>
                        </td>


                        <!-- TANGGAL -->

                        <td>

                            <i class="bi bi-calendar3 me-1 text-muted"></i>

                            <?= !empty($data['tanggal_pemeriksaan'])
                                ? date('d-m-Y', strtotime($data['tanggal_pemeriksaan']))
                                : '-'; ?>

                        </td>


                        <!-- NO REGISTRASI -->

                        <td>

                            <span class="rm-badge">
                                <?= htmlspecialchars($data['no_registrasi'] ?? '-'); ?>
                            </span>

                        </td>


                        <!-- NIK -->

                        <td>
                            <?= htmlspecialchars($data['nik'] ?? '-'); ?>
                        </td>


                        <!-- NAMA IBU -->

                        <td class="nama-pasien">

                            <i class="bi bi-person-circle text-primary me-1"></i>

                            <?= !empty($data['nama_ibu'])
                                ? htmlspecialchars($data['nama_ibu'])
                                : '-'; ?>

                        </td>


                        <!-- NAMA SUAMI -->

                        <td>

                            <i class="bi bi-person me-1 text-muted"></i>

                            <?= !empty($data['nama_suami'])
                                ? htmlspecialchars($data['nama_suami'])
                                : '-'; ?>

                        </td>


                        <!-- NO HP -->

                        <td>
                            <?= !empty($data['no_hp'])
                                ? htmlspecialchars($data['no_hp'])
                                : '-'; ?>
                        </td>


                        <!-- G/P/A -->

                        <td>

                            <span class="gpa-badge">
                                <?= (int) $data['gravida']; ?>/<?= (int) $data['para']; ?>/<?= (int) $data['abortus']; ?>
                            </span>

                        </td>


                        <!-- HPL -->

                        <td>
                            <?= !empty($data['hpl'])
                                ? date('d-m-Y', strtotime($data['hpl']))
                                : '-'; ?>
                        </td>


                        <!-- USIA KEHAMILAN -->

                        <td>
                            <?= $data['usia_kehamilan'] !== null && $data['usia_kehamilan'] !== ''
                                ? htmlspecialchars($data['usia_kehamilan']) . ' minggu'
                                : '-'; ?>
                        </td>


                        <!-- BB -->

                        <td>
                            <?= $data['berat_badan'] !== null && $data['berat_badan'] !== ''
                                ? htmlspecialchars($data['berat_badan']) . ' kg'
                                : '-'; ?>
                        </td>


                        <!-- TEKANAN DARAH -->

                        <td>
                            <?= !empty($data['tekanan_darah'])
                                ? htmlspecialchars($data['tekanan_darah'])
                                : '-'; ?>
                        </td>


                        <!-- TFU -->

                        <td>
                            <?= $data['tfu'] !== null && $data['tfu'] !== ''
                                ? htmlspecialchars($data['tfu']) . ' cm'
                                : '-'; ?>
                        </td>


                        <!-- DJJ -->

                        <td>
                            <?= $data['djj'] !== null && $data['djj'] !== ''
                                ? htmlspecialchars($data['djj']) . ' x/mnt'
                                : '-'; ?>
                        </td>


                        <!-- HASIL -->

                        <td>

                            <span class="hasil-badge">
                                <?= !empty($data['hasil'])
                                    ? htmlspecialchars($data['hasil'])
                                    : '-'; ?>
                            </span>

                        </td>


                        <!-- KETERANGAN -->

                        <td class="teks-panjang">
                            <?= !empty($data['keterangan'])
                                ? htmlspecialchars($data['keterangan'])
                                : '-'; ?>
                        </td>


                    </tr>

                    <?php

                        endwhile;

                    endif;

                    ?>


                    <!-- DATA TIDAK DITEMUKAN -->

                    <tr id="dataKosong" <?= $totalData == 0 ? 'style="display: table-row;"' : ''; ?>>

                        <td
                            colspan="16"
                            class="empty-data"
                        >

                            <i class="bi bi-search"></i>

                            <strong>
                                <?= $totalData == 0
                                    ? 'Belum ada data pemeriksaan ibu hamil'
                                    : 'Data pemeriksaan tidak ditemukan'; ?>
                            </strong>

                            <span>
                                Silakan coba dengan nama,
                                NIK, atau no. registrasi
                                yang berbeda.
                            </span>

                        </td>

                    </tr>


                </tbody>

            </table>

        </div>

    </div>

</div>

</div>


<!-- ======================================================
     JAVASCRIPT SEARCH
======================================================= -->

<script>

document.addEventListener("DOMContentLoaded", function () {

    const searchInput = document.getElementById("searchIbuHamil");
    const clearButton = document.getElementById("clearSearch");
    const rows = document.querySelectorAll(".data-ibuhamil");
    const jumlahHasil = document.getElementById("jumlahHasil");
    const dataKosong = document.getElementById("dataKosong");

    if (!searchInput) {
        return;
    }


    // ==============================================
    // PENCARIAN
    // ==============================================

    function cariIbuHamil() {

        const keyword = searchInput.value.toLowerCase().trim();

        let jumlah = 0;

        rows.forEach(function (row) {

            const text = row.innerText.toLowerCase();

            if (text.includes(keyword)) {

                row.style.display = "";
                jumlah++;

            } else {

                row.style.display = "none";

            }

        });

        jumlahHasil.textContent = jumlah;

        if (jumlah === 0) {
            dataKosong.style.display = "table-row";
        } else {
            dataKosong.style.display = "none";
        }

        if (keyword !== "") {
            clearButton.style.display = "block";
        } else {
            clearButton.style.display = "none";
        }

    }


    searchInput.addEventListener("input", cariIbuHamil);
    searchInput.addEventListener("keyup", cariIbuHamil);


    clearButton.addEventListener("click", function () {

        searchInput.value = "";
        cariIbuHamil();
        searchInput.focus();

    });

});

</script>


<?php

require "../template/footer.php";

?>
