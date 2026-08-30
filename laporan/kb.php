<?php

session_start();

require "../template/rbac.php";

// Hanya Kepala Puskesmas
cekAkses([ROLE_KEPALA]);

require "../config.php";

$title = "Laporan KB - rekammedispuskesmas";

require "../template/header.php";
require "../template/navbar.php";
require "../template/sidebar.php";


/*
|--------------------------------------------------------------------------
| AMBIL DATA LAPORAN KB
|--------------------------------------------------------------------------
|
| Relasi:
|
| tbl_pelayanan_kb.id_kb
|          ↓
| tbl_kb.id_kb
|
| tbl_kb.no_kk
|          ↓
| tbl_ibu_hamil.no_kk  (nama istri)
|
|--------------------------------------------------------------------------
*/

$query = mysqli_query($koneksi, "
    SELECT

        p.id_pelayanan_kb,
        p.tanggal_pelayanan,
        p.metode_kb,
        p.keluhan,
        p.berat_badan,
        p.tinggi_badan,
        p.tekanan_darah,
        p.hasil_pemeriksaan,
        p.efek_samping,
        p.keterangan,

        k.no_peserta_kb,
        k.no_kk,
        k.nama_suami,
        k.jenis_kb,

        ibu.nama_ibu AS nama_istri

    FROM tbl_pelayanan_kb AS p

    INNER JOIN tbl_kb AS k
        ON p.id_kb = k.id_kb

    LEFT JOIN tbl_ibu_hamil AS ibu
        ON k.no_kk = ibu.no_kk

    ORDER BY
        p.tanggal_pelayanan DESC,
        p.id_pelayanan_kb DESC
");

if (!$query) {
    die("Query laporan KB gagal: " . mysqli_error($koneksi));
}


// ======================================================
// HITUNG JUMLAH DATA
// ======================================================

$queryJumlah = mysqli_query($koneksi, "
    SELECT COUNT(*) AS total FROM tbl_pelayanan_kb
");

$dataJumlah = mysqli_fetch_assoc($queryJumlah);
$totalKB = $dataJumlah['total'];

?>

<style>

/* =====================================================
   LAPORAN KB
===================================================== */

.kb-container {
    padding-bottom: 40px;
}


/* =====================================================
   HEADER HALAMAN
===================================================== */

.kb-header {
    background: #ffffff;
    border: 1px solid #e8e8f3;
    border-radius: 16px;
    padding: 22px 25px;
    margin-top: 20px;
    margin-bottom: 20px;
    box-shadow: 0 5px 18px rgba(0, 0, 0, 0.04);
}

.kb-title {
    display: flex;
    align-items: center;
    gap: 13px;
}

.kb-title-icon {
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

.kb-title h1 {
    font-size: 24px;
    font-weight: 700;
    color: #262a38;
    margin: 0;
}

.kb-title p {
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

#searchKB {
    height: 44px;
    border-radius: 10px;
    padding-left: 43px;
    padding-right: 40px;
    border: 1px solid #dcddea;
    font-size: 14px;
    transition: all 0.2s ease;
}

#searchKB:focus {
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

#tableKB {
    margin: 0;
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
}

#tableKB thead th {
    background: #f4f4fb;
    color: #45465e;
    font-size: 12px;
    font-weight: 700;
    padding: 15px 14px;
    border-bottom: 1px solid #e2e2ed;
    white-space: nowrap;
    vertical-align: middle;
}

#tableKB tbody td {
    padding: 14px;
    font-size: 13px;
    color: #53556d;
    border-bottom: 1px solid #ededf3;
    vertical-align: middle;
    white-space: nowrap;
}

#tableKB tbody tr {
    transition: all 0.2s ease;
}

#tableKB tbody tr:hover {
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

.jenis-badge {
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

.metode-badge {
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

    .kb-header {
        padding: 18px;
    }

    .kb-title h1 {
        font-size: 20px;
    }

    .kb-title-icon {
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

    #tableKB thead th,
    #tableKB tbody td {
        padding: 11px 10px;
        font-size: 12px;
    }

}


/* =====================================================
   ANIMASI
===================================================== */

.kb-header,
.total-card,
.search-card,
.pdf-card,
.table-card {
    animation: fadeInKB 0.35s ease;
}

@keyframes fadeInKB {

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

<div class="kb-container">


    <!-- ==================================================
         HEADER
    =================================================== -->

    <div class="kb-header">

        <div class="kb-title">

            <div class="kb-title-icon">

                <i class="bi bi-person-vcard"></i>

            </div>

            <div>

                <h1>
                    Laporan Pelayanan KB
                </h1>

                <p>
                    Data pelayanan peserta Keluarga Berencana Puskesmas Desa Mendis.
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
                            Total Pelayanan KB
                        </div>

                        <div class="total-number">

                            <?= $totalKB; ?>

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

                    Cari Data Pelayanan KB

                </div>

                <div class="search-wrapper">

                    <i class="bi bi-search search-icon"></i>

                    <input
                        type="text"
                        id="searchKB"
                        class="form-control"
                        placeholder="Cari nama istri, nama suami, no peserta KB, atau jenis KB..."
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
                        <?= $totalKB; ?>
                    </span>
                    data pelayanan

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

            Cetak Laporan KB

        </div>


        <div class="d-flex flex-wrap gap-2">

            <!-- PDF MINGGUAN -->

            <a
                href="<?= $main_url ?>laporan/kb-pdf.php?periode=mingguan"
                class="btn btn-danger btn-pdf"
                target="_blank"
            >

                <i class="bi bi-file-earmark-pdf me-1"></i>

                PDF Mingguan

            </a>


            <!-- PDF BULANAN -->

            <a
                href="<?= $main_url ?>laporan/kb-pdf.php?periode=bulanan"
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

                Data Pelayanan KB

            </h6>

            <p class="table-card-subtitle">

                Daftar pelayanan peserta Keluarga Berencana.

            </p>

        </div>


        <!-- TABLE -->

        <div class="table-wrapper">

            <table
                id="tableKB"
                class="table align-middle"
            >

                <thead>

                    <tr>

                        <th>No</th>
                        <th>Tanggal</th>
                        <th>No. Peserta KB</th>
                        <th>No. KK</th>
                        <th>Nama Istri</th>
                        <th>Nama Suami</th>
                        <th>Jenis KB</th>
                        <th>Metode KB</th>
                        <th>Keluhan</th>
                        <th>BB</th>
                        <th>TB</th>
                        <th>Tekanan Darah</th>
                        <th>Hasil Pemeriksaan</th>
                        <th>Efek Samping</th>
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

                    <tr class="data-kb">


                        <!-- NO -->

                        <td>
                            <?= $no++; ?>
                        </td>


                        <!-- TANGGAL -->

                        <td>

                            <i class="bi bi-calendar3 me-1 text-muted"></i>

                            <?= !empty($data['tanggal_pelayanan'])
                                ? date('d-m-Y', strtotime($data['tanggal_pelayanan']))
                                : '-'; ?>

                        </td>


                        <!-- NO PESERTA -->

                        <td>

                            <span class="rm-badge">
                                <?= htmlspecialchars($data['no_peserta_kb'] ?? '-'); ?>
                            </span>

                        </td>


                        <!-- NO KK -->

                        <td>
                            <?= htmlspecialchars($data['no_kk'] ?? '-'); ?>
                        </td>


                        <!-- NAMA ISTRI -->

                        <td class="nama-pasien">

                            <i class="bi bi-person-circle text-primary me-1"></i>

                            <?= !empty($data['nama_istri'])
                                ? htmlspecialchars($data['nama_istri'])
                                : '-'; ?>

                        </td>


                        <!-- NAMA SUAMI -->

                        <td>

                            <i class="bi bi-person me-1 text-muted"></i>

                            <?= !empty($data['nama_suami'])
                                ? htmlspecialchars($data['nama_suami'])
                                : '-'; ?>

                        </td>


                        <!-- JENIS KB -->

                        <td>

                            <span class="jenis-badge">
                                <?= !empty($data['jenis_kb'])
                                    ? htmlspecialchars($data['jenis_kb'])
                                    : '-'; ?>
                            </span>

                        </td>


                        <!-- METODE KB -->

                        <td>

                            <span class="metode-badge">
                                <?= !empty($data['metode_kb'])
                                    ? htmlspecialchars($data['metode_kb'])
                                    : '-'; ?>
                            </span>

                        </td>


                        <!-- KELUHAN -->

                        <td class="teks-panjang">
                            <?= !empty($data['keluhan'])
                                ? htmlspecialchars($data['keluhan'])
                                : '-'; ?>
                        </td>


                        <!-- BB -->

                        <td>
                            <?= $data['berat_badan'] !== null && $data['berat_badan'] !== ''
                                ? htmlspecialchars($data['berat_badan']) . ' kg'
                                : '-'; ?>
                        </td>


                        <!-- TB -->

                        <td>
                            <?= $data['tinggi_badan'] !== null && $data['tinggi_badan'] !== ''
                                ? htmlspecialchars($data['tinggi_badan']) . ' cm'
                                : '-'; ?>
                        </td>


                        <!-- TEKANAN DARAH -->

                        <td>
                            <?= !empty($data['tekanan_darah'])
                                ? htmlspecialchars($data['tekanan_darah'])
                                : '-'; ?>
                        </td>


                        <!-- HASIL PEMERIKSAAN -->

                        <td class="teks-panjang">
                            <?= !empty($data['hasil_pemeriksaan'])
                                ? htmlspecialchars($data['hasil_pemeriksaan'])
                                : '-'; ?>
                        </td>


                        <!-- EFEK SAMPING -->

                        <td class="teks-panjang">
                            <?= !empty($data['efek_samping'])
                                ? htmlspecialchars($data['efek_samping'])
                                : '-'; ?>
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
                            colspan="15"
                            class="empty-data"
                        >

                            <i class="bi bi-search"></i>

                            <strong>
                                <?= $totalData == 0
                                    ? 'Belum ada data pelayanan KB'
                                    : 'Data pelayanan tidak ditemukan'; ?>
                            </strong>

                            <span>
                                Silakan coba dengan nama,
                                No Peserta KB, atau jenis KB
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

    const searchInput = document.getElementById("searchKB");
    const clearButton = document.getElementById("clearSearch");
    const rows = document.querySelectorAll(".data-kb");
    const jumlahHasil = document.getElementById("jumlahHasil");
    const dataKosong = document.getElementById("dataKosong");

    if (!searchInput) {
        return;
    }


    // ==============================================
    // PENCARIAN
    // ==============================================

    function cariKB() {

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


    searchInput.addEventListener("input", cariKB);
    searchInput.addEventListener("keyup", cariKB);


    clearButton.addEventListener("click", function () {

        searchInput.value = "";
        cariKB();
        searchInput.focus();

    });

});

</script>


<?php

require "../template/footer.php";

?>
