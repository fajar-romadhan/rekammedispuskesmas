<?php

session_start();

require "../template/rbac.php";

// Hanya Bidan
cekAkses([ROLE_BIDAN]);

require "../config.php";


/* =========================================================
   AMBIL DATA REGISTER KB
========================================================= */

$queryKB = mysqli_query($koneksi, "
    SELECT *
    FROM tbl_kb
    ORDER BY id_kb DESC
");

if (!$queryKB) {
    die("Query gagal : " . mysqli_error($koneksi));
}


$title = "Data Pasien KB - Rekam Medis Puskesmas";


require "../template/header.php";
require "../template/navbar.php";
require "../template/sidebar.php";

?>


<style>

/* =========================================================
   MAIN CONTENT
========================================================= */

.kb-page {
    padding-bottom: 50px;
}


/* =========================================================
   HEADER HALAMAN
========================================================= */

.kb-header {
    padding-top: 25px;
    padding-bottom: 22px;
    margin-bottom: 25px;
    border-bottom: 1px solid #e9e9ef;
}

.kb-header h1 {
    font-size: 28px;
    font-weight: 700;
    color: #212229;
    margin-bottom: 5px;
}

.kb-header p {
    font-size: 14px;
    color: #6c757d;
}


/* =========================================================
   BUTTON HEADER
========================================================= */

.kb-header-actions {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}

.kb-header-actions .btn {
    border-radius: 9px;
    padding: 10px 16px;
    font-size: 14px;
    font-weight: 500;
    transition: all .2s ease;
}

.kb-header-actions .btn-dark {
    box-shadow: 0 3px 8px rgba(0,0,0,.12);
}

.kb-header-actions .btn-dark:hover {
    transform: translateY(-1px);
    box-shadow: 0 5px 12px rgba(0,0,0,.16);
}

.kb-header-actions .btn-outline-dark:hover {
    transform: translateY(-1px);
}


/* =========================================================
   CARD UTAMA
========================================================= */

.kb-card {
    border: none !important;
    border-radius: 15px !important;
    box-shadow: 0 4px 18px rgba(0,0,0,.07) !important;
    overflow: hidden;
    background: #fff;
}

.kb-card .card-body {
    padding: 25px;
}


/* =========================================================
   CARD HEADER
========================================================= */

.kb-card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 22px;
}

.kb-card-title {
    display: flex;
    align-items: center;
    gap: 12px;
}

.kb-card-icon {
    width: 42px;
    height: 42px;
    border-radius: 11px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #212229;
    color: white;
    font-size: 19px;
}

.kb-card-title h5 {
    margin: 0;
    font-size: 17px;
    font-weight: 700;
    color: #212229;
}

.kb-card-title small {
    display: block;
    margin-top: 3px;
    color: #6c757d;
    font-size: 12px;
}


/* =========================================================
   SEARCH
========================================================= */

.kb-search {
    width: 320px;
}

.kb-search .input-group {
    border: 1px solid #dedfe6;
    border-radius: 10px;
    overflow: hidden;
    background: #fff;
    transition: all .2s ease;
}

.kb-search .input-group:focus-within {
    border-color: #6c757d;
    box-shadow: 0 0 0 3px rgba(33,37,41,.08);
}

.kb-search .input-group-text {
    border: none;
    background: #fff;
    color: #6c757d;
    padding-left: 13px;
}

.kb-search .form-control {
    border: none;
    box-shadow: none !important;
    font-size: 13px;
    padding: 10px 12px 10px 3px;
}

.kb-search .form-control::placeholder {
    color: #adaebd;
}


/* =========================================================
   TABLE WRAPPER
========================================================= */

.kb-table-wrapper {
    border: 1px solid #e9e9ef;
    border-radius: 12px;
    padding-bottom: 5px;
    overflow-x: auto;
    overflow-y: hidden;
}


/* =========================================================
   TABLE
========================================================= */

#tabelKB {
    margin-bottom: 0 !important;
    min-width: 1350px;
    width: 100%;
}

#tabelKB thead th {
    background: #212229 !important;
    color: #fff;
    border-color: #343540 !important;
    font-size: 12px;
    font-weight: 600;
    padding: 14px 14px;
    white-space: nowrap;
    vertical-align: middle;
}

#tabelKB tbody td {
    font-size: 13px;
    color: #343540;
    padding: 13px 14px;
    border-color: #edeef2;
    vertical-align: middle;
    white-space: nowrap;
}

#tabelKB tbody tr {
    transition: all .15s ease;
}

#tabelKB tbody tr:hover {
    background: #f8f8fa;
}


/* =========================================================
   NOMOR
========================================================= */

.kb-number {
    width: 34px;
    height: 34px;
    display: inline-flex;
    justify-content: center;
    align-items: center;
    border-radius: 8px;
    background: #f1f1f5;
    color: #494a57;
    font-size: 12px;
    font-weight: 600;
}


/* =========================================================
   KOLOM AKSI
========================================================= */

#tabelKB th:last-child {
    width: 155px !important;
    min-width: 155px !important;
    text-align: center;
}

#tabelKB td:last-child {
    width: 155px !important;
    min-width: 155px !important;
    padding-left: 18px !important;
    padding-right: 18px !important;
    text-align: center;
}


/* =========================================================
   CONTAINER TOMBOL AKSI
========================================================= */

.kb-action-wrapper {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 9px;
    width: 100%;
    min-width: 115px;
}


/* =========================================================
   TOMBOL AKSI
========================================================= */

.kb-action {
    display: inline-flex;
    justify-content: center;
    align-items: center;
    flex-shrink: 0;
    width: 35px;
    height: 35px;
    min-width: 35px;
    border-radius: 8px;
    font-size: 14px;
    text-decoration: none;
    transition: all .2s ease;
}

.kb-action:hover {
    transform: translateY(-2px);
}


/* =========================================================
   EDIT
========================================================= */

.kb-action-edit {
    background: #f1f1f5;
    color: #343540;
    border: 1px solid #dedfe6;
}

.kb-action-edit:hover {
    background: #dedfe6;
    color: #212229;
}


/* =========================================================
   HAPUS
========================================================= */

.kb-action-hapus {
    background: #fdecec;
    color: #d33636;
    border: 1px solid #f7c9c9;
}

.kb-action-hapus:hover {
    background: #f8d3d3;
    color: #a92323;
}


/* =========================================================
   PDF
========================================================= */

.kb-action-pdf {
    background: #212229;
    color: #fff;
    border: 1px solid #212229;
}

.kb-action-pdf:hover {
    background: #000;
    color: #fff;
}


/* =========================================================
   DATA KOSONG
========================================================= */

.kb-empty {
    padding: 55px 20px !important;
    text-align: center;
}

.kb-empty-icon {
    width: 65px;
    height: 65px;
    margin: auto;
    border-radius: 50%;
    background: #f1f1f5;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #adaebd;
    font-size: 27px;
}

.kb-empty p {
    margin-top: 15px;
    margin-bottom: 0;
    color: #6c757d;
    font-size: 13px;
}


/* =========================================================
   RESPONSIVE
========================================================= */

@media (max-width: 992px) {

    .kb-card .card-body {
        padding: 20px;
    }

    .kb-card-header {
        align-items: flex-start;
        flex-direction: column;
        gap: 15px;
    }

    .kb-search {
        width: 100%;
    }

}


@media (max-width: 768px) {

    .kb-header {
        align-items: flex-start !important;
        flex-direction: column;
        gap: 18px;
    }

    .kb-header-actions {
        width: 100%;
    }

    .kb-header-actions .btn {
        flex: 1;
    }

    .kb-header h1 {
        font-size: 24px;
    }

}


@media (max-width: 576px) {

    .kb-card .card-body {
        padding: 15px;
    }

    .kb-header-actions {
        flex-direction: column;
    }

    .kb-header-actions .btn {
        width: 100%;
    }

}

</style>


<!-- =========================================================
     MAIN
========================================================= -->

<div class="kb-page page-content-wrap">


    <!-- =====================================================
         HEADER
    ====================================================== -->

    <div class="kb-header">

        <div class="d-flex justify-content-between
                    align-items-center">

            <div>

                <h1>
                    Data Pasien KB
                </h1>

                <p class="mb-0">
                    Data register peserta keluarga berencana
                </p>

            </div>


            <!-- BUTTON HEADER -->

            <div class="kb-header-actions">

                <a href="kb-pdf-semua.php"
                   target="_blank"
                   class="btn btn-outline-dark">

                    <i class="bi bi-file-earmark-pdf me-1"></i>

                    Cetak Semua Data PDF

                </a>

            </div>

        </div>

    </div>


    <!-- =====================================================
         CARD DATA
    ====================================================== -->

    <div class="card kb-card mb-4">

        <div class="card-body">


            <!-- =================================================
                 CARD HEADER
            ================================================== -->

            <div class="kb-card-header">


                <!-- TITLE -->

                <div class="kb-card-title">

                    <div class="kb-card-icon">

                        <i class="bi bi-person-vcard"></i>

                    </div>

                    <div>

                        <h5>
                            Data Peserta KB
                        </h5>

                        <small>
                            Daftar register peserta keluarga berencana
                        </small>

                    </div>

                </div>


                <!-- SEARCH -->

                <div class="kb-search">

                    <div class="input-group">

                        <span class="input-group-text">

                            <i class="bi bi-search"></i>

                        </span>

                        <input type="text"
                               id="searchKB"
                               class="form-control"
                               placeholder="Cari peserta KB...">

                    </div>

                </div>

            </div>


            <!-- =================================================
                 TABLE
            ================================================== -->

            <div class="table-responsive kb-table-wrapper">

                <table class="table table-hover align-middle"
                       id="tabelKB">

                    <thead>

                        <tr>

                            <th class="text-center"
                                style="width:55px;">

                                No

                            </th>

                            <th>
                                Tanggal
                            </th>

                            <th>
                                No. KK
                            </th>

                            <th>
                                No. Peserta KB
                            </th>

                            <th>
                                Tanggal Lahir
                            </th>

                            <th>
                                Nama Istri
                            </th>

                            <th>
                                Nama Suami
                            </th>

                            <th class="text-center">
                                Anak
                            </th>

                            <th>
                                Jenis KB
                            </th>

                            <th>
                                Kunjungan
                            </th>

                            <th class="text-center"
                                style="width:155px;">

                                Aksi

                            </th>

                        </tr>

                    </thead>


                    <tbody>


                    <?php

                    $no = 1;


                    if (mysqli_num_rows($queryKB) > 0) {


                        while ($data = mysqli_fetch_assoc($queryKB)) {


                            /* ============================
                               BADGE KUNJUNGAN
                            ============================ */

                            $visitClass = "kb-visit-lama";


                            if ($data['kunjungan'] == 'Baru') {

                                $visitClass = "kb-visit-baru";

                            } elseif ($data['kunjungan'] == 'Ganti') {

                                $visitClass = "kb-visit-ganti";

                            }

                    ?>

                        <tr>


                            <!-- NO -->

                            <td class="text-center">

                                <span class="kb-number">

                                    <?= $no++; ?>

                                </span>

                            </td>


                            <!-- TANGGAL -->

                            <td>

                                <?= !empty($data['tanggal'])

                                    ? date(
                                        'd-m-Y',
                                        strtotime($data['tanggal'])
                                    )

                                    : '-';

                                ?>

                            </td>


                            <!-- NO KK -->

                            <td>

                                <?= !empty($data['no_kk'])

                                    ? htmlspecialchars(
                                        $data['no_kk'],
                                        ENT_QUOTES,
                                        'UTF-8'
                                    )

                                    : '-';

                                ?>

                            </td>


                            <!-- NO PESERTA KB -->

                            <td>

                                <span class="kb-registration">

                                    <?= !empty($data['no_peserta_kb'])

                                        ? htmlspecialchars(
                                            $data['no_peserta_kb'],
                                            ENT_QUOTES,
                                            'UTF-8'
                                        )

                                        : '-';

                                    ?>

                                </span>

                            </td>


                            <!-- TANGGAL LAHIR -->

                            <td>

                                <?= !empty($data['tanggal_lahir'])

                                    ? date(
                                        'd-m-Y',
                                        strtotime(
                                            $data['tanggal_lahir']
                                        )
                                    )

                                    : '-';

                                ?>

                            </td>


                            <!-- NAMA ISTRI -->

                            <td>

                                <?= !empty($data['nama_istri'])

                                    ? htmlspecialchars(
                                        $data['nama_istri'],
                                        ENT_QUOTES,
                                        'UTF-8'
                                    )

                                    : '-';

                                ?>

                            </td>


                            <!-- NAMA SUAMI -->

                            <td>

                                <?= !empty($data['nama_suami'])

                                    ? htmlspecialchars(
                                        $data['nama_suami'],
                                        ENT_QUOTES,
                                        'UTF-8'
                                    )

                                    : '-';

                                ?>

                            </td>


                            <!-- JUMLAH ANAK -->

                            <td class="text-center">

                                <?= isset($data['jumlah_anak'])

                                    ? htmlspecialchars(
                                        $data['jumlah_anak'],
                                        ENT_QUOTES,
                                        'UTF-8'
                                    )

                                    : '0';

                                ?>

                            </td>


                            <!-- JENIS KB -->

                            <td>

                                <span class="kb-type">

                                    <?= !empty($data['jenis_kb'])

                                        ? htmlspecialchars(
                                            $data['jenis_kb'],
                                            ENT_QUOTES,
                                            'UTF-8'
                                        )

                                        : '-';

                                    ?>

                                </span>

                            </td>


                            <!-- KUNJUNGAN -->

                            <td>

                                <span class="kb-visit <?= $visitClass; ?>">

                                    <?= !empty($data['kunjungan'])

                                        ? htmlspecialchars(
                                            $data['kunjungan'],
                                            ENT_QUOTES,
                                            'UTF-8'
                                        )

                                        : '-';

                                    ?>

                                </span>

                            </td>


                            <!-- =================================================
                                 AKSI
                            ================================================== -->

                            <td class="text-center">

                                <div class="kb-action-wrapper">


                                    <!-- EDIT -->

                                    <a href="edit-kb.php?id=<?= $data['id_kb']; ?>"
                                       class="kb-action kb-action-edit"
                                       title="Edit Data">

                                        <i class="bi bi-pencil-square"></i>

                                    </a>


                                    <!-- HAPUS -->

                                    <a href="hapus-kb.php?id=<?= $data['id_kb']; ?>"
                                       class="kb-action kb-action-hapus"
                                       title="Hapus"

                                       onclick="return confirm(
                                           'Hapus data peserta KB <?= htmlspecialchars(
                                               $data['no_peserta_kb'],
                                               ENT_QUOTES,
                                               'UTF-8'
                                           ); ?>? Data pelayanan dan jadwal terkait juga akan terhapus.'
                                       );">

                                        <i class="bi bi-trash"></i>

                                    </a>


                                    <!-- PDF -->

                                    <a href="kb-pdf.php?id=<?= $data['id_kb']; ?>"
                                       target="_blank"
                                       class="kb-action kb-action-pdf"
                                       title="Cetak PDF">

                                        <i class="bi bi-file-earmark-pdf"></i>

                                    </a>


                                </div>

                            </td>


                        </tr>

                    <?php

                        }

                    } else {

                    ?>


                        <!-- DATA KOSONG -->

                        <tr>

                            <td colspan="11"
                                class="kb-empty">

                                <div class="kb-empty-icon">

                                    <i class="bi bi-database-x"></i>

                                </div>

                                <p>
                                    Belum ada data peserta KB.
                                </p>

                            </td>

                        </tr>


                    <?php

                    }

                    ?>


                    </tbody>

                </table>

            </div>


        </div>

    </div>


</div>


<!-- =========================================================
     SEARCH JAVASCRIPT
========================================================= -->

<script>

document.addEventListener("DOMContentLoaded", function () {

    const searchInput =
        document.getElementById("searchKB");

    const table =
        document.getElementById("tabelKB");

    if (!searchInput || !table) {
        return;
    }

    searchInput.addEventListener("keyup", function () {

        const keyword =
            this.value
                .toLowerCase()
                .trim();

        const rows =
            table.querySelectorAll(
                "tbody tr"
            );

        rows.forEach(function (row) {

            const text =
                row.textContent
                    .toLowerCase();

            row.style.display =
                text.includes(keyword)
                ? ""
                : "none";

        });

    });

});

</script>


<?php

require "../template/footer.php";

?>