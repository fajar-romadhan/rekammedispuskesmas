<?php

session_start();

require "../template/rbac.php";

// Hanya Dokter
cekAkses([ROLE_DOKTER]);

require "../config.php";

$title = "Riwayat Perekaman - rekammedispuskesmas";

require "../template/header.php";
require "../template/navbar.php";
require "../template/sidebar.php";

?>

<style>

/* =====================================================
   HALAMAN LAPORAN REKAM MEDIS
===================================================== */

.laporan-container {
    padding-bottom: 40px;
}

/* Header halaman */
.page-header {
    background: #ffffff;
    border-radius: 16px;
    padding: 22px 25px;
    margin-top: 20px;
    margin-bottom: 20px;
    border: 1px solid #e8e8f3;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.04);
}

.page-header h1 {
    margin: 0;
    font-size: 24px;
    font-weight: 700;
    color: #262a38;
}

.page-header p {
    margin: 6px 0 0;
    color: #7b7c94;
    font-size: 14px;
}

/* Icon judul */
.title-icon {
    width: 42px;
    height: 42px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: #eaeaff;
    color: #7571f9;
    border-radius: 12px;
    margin-right: 10px;
}

/* Card tabel */
.table-card {
    background: #ffffff;
    border-radius: 16px;
    border: 1px solid #e8e8f3;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
    overflow: hidden;
}

/* Bagian atas tabel */
.table-card-header {
    padding: 18px 20px;
    border-bottom: 1px solid #ededf4;
    background: #ffffff;
}

/* Search */
.search-wrapper {
    position: relative;
    max-width: 430px;
}

.search-wrapper i {
    position: absolute;
    left: 15px;
    top: 50%;
    transform: translateY(-50%);
    color: #7d7f99;
    font-size: 17px;
    z-index: 2;
}

#searchPasien {
    height: 44px;
    padding-left: 43px;
    padding-right: 40px;
    border-radius: 10px;
    border: 1px solid #dcddea;
    font-size: 14px;
    transition: all 0.2s ease;
}

#searchPasien:focus {
    border-color: #7571f9;
    box-shadow: 0 0 0 3px rgba(117, 113, 249, 0.10);
}

/* Tombol clear search */
.clear-search {
    position: absolute;
    right: 12px;
    top: 50%;
    transform: translateY(-50%);
    border: none;
    background: transparent;
    color: #9a9bb1;
    display: none;
    cursor: pointer;
    z-index: 3;
}

.clear-search:hover {
    color: #dc3545;
}

/* Info hasil pencarian */
.search-info {
    font-size: 13px;
    color: #7b7c94;
    margin-top: 8px;
}

/* Table wrapper */
.table-wrapper {
    width: 100%;
    overflow-x: auto;
}

/* Tabel */
#tabelPasien {
    width: 100%;
    margin: 0;
    border-collapse: separate;
    border-spacing: 0;
}

/* Header tabel */
#tabelPasien thead th {
    background: #f4f4fb;
    color: #45465e;
    font-size: 13px;
    font-weight: 700;
    padding: 15px 14px;
    border-bottom: 1px solid #e3e3ee;
    white-space: nowrap;
    vertical-align: middle;
}

/* Isi tabel */
#tabelPasien tbody td {
    padding: 14px;
    font-size: 14px;
    color: #4c4e66;
    border-bottom: 1px solid #ededf3;
    vertical-align: middle;
}

/* Hover */
#tabelPasien tbody tr {
    transition: all 0.2s ease;
}

#tabelPasien tbody tr:hover {
    background: #f8f8ff;
}

/* Nomor */
.nomor {
    width: 55px;
    text-align: center;
    color: #7d7e92 !important;
}

/* ID pasien */
.id-pasien {
    display: inline-block;
    padding: 5px 9px;
    border-radius: 7px;
    background: #f1f2f9;
    border: 1px solid #dcddec;
    color: #45465e;
    font-size: 12px;
    font-weight: 600;
    white-space: nowrap;
}

/* Nama */
.nama-pasien {
    font-weight: 600;
    color: #262a38 !important;
}

/* Gender */
.gender-badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 5px 9px;
    border-radius: 7px;
    background: #f4f4fb;
    color: #595a74;
    font-size: 12px;
    white-space: nowrap;
}

/* Umur */
.umur-badge {
    display: inline-block;
    padding: 5px 9px;
    border-radius: 7px;
    background: #eef0ff;
    color: #7571f9;
    font-size: 12px;
    font-weight: 600;
}

/* Alamat */
.alamat-pasien {
    min-width: 170px;
    max-width: 260px;
    line-height: 1.5;
}

/* Tombol PDF */
.btn-pdf {
    width: 38px;
    height: 38px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 9px;
    transition: all 0.2s ease;
}

.btn-pdf:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 10px rgba(220, 53, 69, 0.18);
}

/* Tidak ditemukan */
#tidakDitemukan {
    display: none;
}

.empty-search {
    text-align: center;
    padding: 45px 20px !important;
    color: #7b7c94;
}

.empty-search i {
    font-size: 40px;
    display: block;
    margin-bottom: 10px;
    color: #b7b8cc;
}

.empty-search strong {
    display: block;
    color: #56576d;
    margin-bottom: 4px;
}

/* Responsive */
@media (max-width: 768px) {

    .page-header {
        padding: 18px;
    }

    .page-header h1 {
        font-size: 20px;
    }

    .table-card-header {
        padding: 15px;
    }

    .search-wrapper {
        max-width: 100%;
    }

    #tabelPasien thead th,
    #tabelPasien tbody td {
        padding: 11px 10px;
        font-size: 13px;
    }

}

/* Animasi */
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(5px);
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.table-card {
    animation: fadeIn 0.35s ease;
}

</style>


<div class="page-content-wrap">

    <div class="laporan-container">

        <!-- ==========================================
             HEADER
        =========================================== -->

        <div class="page-header">

            <div class="d-flex justify-content-between
                        align-items-center
                        flex-wrap gap-3">

                <div>

                    <h1>
                        <span class="title-icon">
                            <i class="bi bi-file-earmark-medical"></i>
                        </span>

                        Laporan Rekam Medis Pasien
                    </h1>

                    <p>
                        Daftar pasien dan laporan rekam medis yang tersimpan
                        dalam sistem.
                    </p>

                </div>

            </div>

        </div>


        <!-- ==========================================
             TABLE CARD
        =========================================== -->

        <div class="table-card">

            <!-- SEARCH HEADER -->

            <div class="table-card-header">

                <div class="d-flex justify-content-between
                            align-items-center
                            flex-wrap gap-3">

                    <div>

                        <h6 class="mb-1 fw-bold">
                            <i class="bi bi-people me-1"></i>
                            Data Pasien
                        </h6>

                        <div class="search-info">
                            <span id="jumlahPasien">0</span>
                            pasien ditampilkan
                        </div>

                    </div>


                    <!-- SEARCH -->

                    <div class="search-wrapper">

                        <i class="bi bi-search"></i>

                        <input
                            type="text"
                            id="searchPasien"
                            class="form-control"
                            placeholder="Cari ID, nama, telepon, alamat..."
                            autocomplete="off"
                        >

                        <button
                            type="button"
                            class="clear-search"
                            id="clearSearch"
                            title="Hapus pencarian">

                            <i class="bi bi-x-circle-fill"></i>

                        </button>

                    </div>

                </div>

            </div>


            <!-- ======================================
                 TABLE
            ======================================= -->

            <div class="table-wrapper">

                <table
                    class="table"
                    id="tabelPasien">

                    <thead>

                        <tr>

                            <th class="nomor">
                                No
                            </th>

                            <th>
                                ID Pasien
                            </th>

                            <th>
                                Nama
                            </th>

                            <th>
                                Umur
                            </th>

                            <th>
                                Jenis Kelamin
                            </th>

                            <th>
                                Telpon
                            </th>

                            <th>
                                Alamat
                            </th>

                            <th class="text-center">
                                Aksi
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                    <?php

                    $no = 1;

                    // jumlah_rm dipakai buat sembunyikan tombol Cetak Laporan
                    // Rekam Medis kalau pasiennya belum punya riwayat rekam
                    // medis sama sekali (PDF-nya kalau dipaksa cetak isinya
                    // cuma "Belum ada riwayat rekam medis").
                    $queryPasien = mysqli_query(
                        $koneksi,
                        "SELECT p.*, COUNT(r.id_rm) AS jumlah_rm
                         FROM tbl_pasien p
                         LEFT JOIN tbl_rekammedis r ON r.id_pasien = p.id
                         GROUP BY p.id
                         ORDER BY p.nama ASC"
                    );

                    while ($pasien = mysqli_fetch_assoc($queryPasien)) {

                    ?>

                        <tr class="data-pasien">

                            <!-- NO -->

                            <td class="nomor">
                                <?= $no++; ?>
                            </td>


                            <!-- ID -->

                            <td>

                                <span class="id-pasien">

                                    <?= htmlspecialchars(
                                        $pasien['id']
                                    ); ?>

                                </span>

                            </td>


                            <!-- NAMA -->

                            <td class="nama-pasien">

                                <i class="bi bi-person-circle me-1 text-primary"></i>

                                <?= htmlspecialchars(
                                    $pasien['nama']
                                ); ?>

                            </td>


                            <!-- UMUR -->

                            <td>

                                <span class="umur-badge">

                                    <i class="bi bi-calendar3 me-1"></i>

                                    <?= htgUmur(
                                        $pasien['tgl_lahir']
                                    ); ?>

                                </span>

                            </td>


                            <!-- GENDER -->

                            <td>

                                <span class="gender-badge">

                                    <?php

                                    if ($pasien['gender'] == 'P') {

                                        echo '<i class="bi bi-gender-male"></i> Pria';

                                    } else {

                                        echo '<i class="bi bi-gender-female"></i> Wanita';

                                    }

                                    ?>

                                </span>

                            </td>


                            <!-- TELEPON -->

                            <td>

                                <i class="bi bi-telephone me-1 text-muted"></i>

                                <?= htmlspecialchars(
                                    $pasien['telpon']
                                ); ?>

                            </td>


                            <!-- ALAMAT -->

                            <td class="alamat-pasien">

                                <i class="bi bi-geo-alt me-1 text-muted"></i>

                                <?= htmlspecialchars(
                                    $pasien['alamat']
                                ); ?>

                            </td>


                            <!-- AKSI -->

                            <td class="text-center">

                                <?php if ($pasien['jumlah_rm'] > 0) { ?>

                                    <a
                                        href="../riwayat-perekaman/laporan-pdf.php?id=<?= $pasien['id']; ?>"
                                        class="btn btn-sm btn-outline-danger btn-pdf"
                                        target="_blank"
                                        title="Cetak Laporan Rekam Medis"
                                    >

                                        <i class="bi bi-file-earmark-pdf"></i>

                                    </a>

                                <?php } else { ?>

                                    <span class="text-muted small"
                                          title="Pasien ini belum punya riwayat rekam medis">
                                        &mdash;
                                    </span>

                                <?php } ?>

                            </td>

                        </tr>

                    <?php

                    }

                    ?>

                    <!-- DATA TIDAK DITEMUKAN -->

                    <tr id="tidakDitemukan">

                        <td
                            colspan="8"
                            class="empty-search">

                            <i class="bi bi-search"></i>

                            <strong>
                                Pasien tidak ditemukan
                            </strong>

                            <span>
                                Coba gunakan nama, ID pasien,
                                nomor telepon, atau alamat.
                            </span>

                        </td>

                    </tr>

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</div>


<!-- ==========================================
     JAVASCRIPT SEARCH
=========================================== -->

<script>

document.addEventListener("DOMContentLoaded", function () {

    const searchInput =
        document.getElementById("searchPasien");

    const clearButton =
        document.getElementById("clearSearch");

    const rows =
        document.querySelectorAll(".data-pasien");

    const jumlahPasien =
        document.getElementById("jumlahPasien");

    const tidakDitemukan =
        document.getElementById("tidakDitemukan");


    // ==========================================
    // HITUNG DATA AWAL
    // ==========================================

    jumlahPasien.textContent = rows.length;


    // ==========================================
    // FUNGSI PENCARIAN
    // ==========================================

    function cariPasien() {

        const keyword =
            searchInput.value
                .toLowerCase()
                .trim();

        let jumlah = 0;


        rows.forEach(function (row) {

            const text =
                row.textContent
                    .toLowerCase();

            if (text.includes(keyword)) {

                row.style.display = "";

                jumlah++;

            } else {

                row.style.display = "none";

            }

        });


        // Tampilkan pesan jika tidak ditemukan

        if (jumlah === 0) {

            tidakDitemukan.style.display =
                "table-row";

        } else {

            tidakDitemukan.style.display =
                "none";

        }


        // Update jumlah hasil

        jumlahPasien.textContent = jumlah;


        // Tampilkan tombol clear

        if (keyword !== "") {

            clearButton.style.display =
                "block";

        } else {

            clearButton.style.display =
                "none";

        }

    }


    // ==========================================
    // EVENT SEARCH
    // ==========================================

    searchInput.addEventListener(
        "keyup",
        cariPasien
    );


    searchInput.addEventListener(
        "input",
        cariPasien
    );


    // ==========================================
    // CLEAR SEARCH
    // ==========================================

    clearButton.addEventListener(
        "click",
        function () {

            searchInput.value = "";

            cariPasien();

            searchInput.focus();

        }
    );

});

</script>


<?php

require "../template/footer.php";

?>