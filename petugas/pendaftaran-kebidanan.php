<?php

session_start();

require "../template/rbac.php";

// Hanya Petugas
cekAkses([ROLE_PETUGAS]);

require "../config.php";

$title = "Pendaftaran Kebidanan - rekammedispuskesmas";

require "../template/header.php";
require "../template/navbar.php";
require "../template/sidebar.php";

?>

<style>

/* =================================
   HALAMAN
================================= */

.kebidanan-page {
    padding-top: 15px;
    padding-bottom: 35px;
}


/* =================================
   HEADER
================================= */

.kebidanan-header {
    background: #ffffff;

    border: 1px solid #e8e7f0;

    border-radius: 14px;

    padding: 16px 20px;

    margin-bottom: 12px;

    box-shadow: 0 3px 12px rgba(0, 0, 0, .04);
}

.kebidanan-header h1 {
    margin: 0;

    font-size: 24px;

    font-weight: 600;

    color: #343540;
}

.kebidanan-header h1 i {
    color: #d63384;

    margin-right: 8px;
}

.kebidanan-header p {
    margin: 5px 0 0 34px;

    font-size: 13px;

    color: #6c757d;
}


/* =================================
   SEARCH CARD
================================= */

.search-card {
    background: #ffffff;

    border: 1px solid #e8e7f0;

    border-radius: 14px;

    padding: 15px 18px;

    margin-bottom: 12px;

    box-shadow: 0 3px 12px rgba(0, 0, 0, .04);
}

.search-title {
    font-size: 14px;

    font-weight: 600;

    color: #343540;

    margin-bottom: 10px;
}

.search-title i {
    color: #7571f9;

    margin-right: 6px;
}

.search-form {
    display: flex;

    gap: 8px;
}

.search-input-wrapper {
    position: relative;

    flex: 1;
}

.search-input-wrapper i {
    position: absolute;

    left: 12px;

    top: 50%;

    transform: translateY(-50%);

    color: #9d9caf;
}

.search-input {
    width: 100%;

    height: 40px;

    border: 1px solid #dfdfe8;

    border-radius: 8px;

    padding: 7px 12px 7px 36px;

    font-size: 13px;

    outline: none;

    transition: .2s;
}

.search-input:focus {
    border-color: #7571f9;

    box-shadow: 0 0 0 3px rgba(117, 113, 249, .08);
}

.btn-search {
    height: 40px;

    padding: 0 17px;

    border: none;

    border-radius: 8px;

    background: #7571f9;

    color: white;

    font-size: 13px;

    font-weight: 500;

    transition: .2s;
}

.btn-search:hover {
    background: #5f5ae0;

    transform: translateY(-1px);
}


/* =================================
   TABLE CARD
================================= */

.patient-card {
    background: #ffffff;

    border: 1px solid #e8e7f0;

    border-radius: 14px;

    overflow: hidden;

    box-shadow: 0 4px 16px rgba(0, 0, 0, .05);
}

.patient-card-header {
    padding: 14px 18px;

    border-bottom: 1px solid #eeeeee;

    font-size: 15px;

    font-weight: 600;

    color: #343540;
}

.patient-card-header i {
    color: #d63384;
}


/* =================================
   TABLE
================================= */

.patient-table {
    margin: 0;

    width: 100%;
}

.patient-table thead th {
    background: #f8f8fa;

    padding: 13px 15px;

    border-bottom: 1px solid #dedfe6;

    color: #494a57;

    font-size: 12px;

    font-weight: 600;

    white-space: nowrap;
}

.patient-table tbody td {
    padding: 13px 15px;

    border-bottom: 1px solid #f0f0f0;

    color: #494a57;

    font-size: 13px;

    vertical-align: middle;
}

.patient-table tbody tr {
    transition: .2s;
}

.patient-table tbody tr:hover {
    background: #fff8fb;
}


/* =================================
   NOMOR
================================= */

.nomor {
    width: 50px;

    text-align: center;

    color: #6c757d;

    font-weight: 600;
}


/* =================================
   NAMA PASIEN
================================= */

.patient-name {
    display: flex;

    align-items: center;

    gap: 9px;
}

.patient-icon {
    width: 34px;

    height: 34px;

    display: inline-flex;

    align-items: center;

    justify-content: center;

    border-radius: 8px;

    background: #fff0f6;

    color: #d63384;

    flex-shrink: 0;
}

.patient-name-text {
    font-weight: 600;

    color: #343540;
}


/* =================================
   RM
================================= */

.rm-badge {
    display: inline-block;

    padding: 5px 9px;

    border-radius: 6px;

    background: #efeeff;

    color: #7571f9;

    font-size: 11px;

    font-weight: 600;
}


/* =================================
   GENDER
================================= */

.gender-text {
    color: #6c757d;
}


/* =================================
   TOMBOL DAFTAR
================================= */

.btn-daftar {
    border: 1px solid #d63384;

    background: #fff5f9;

    color: #d63384;

    border-radius: 7px;

    padding: 6px 11px;

    font-size: 12px;

    font-weight: 500;

    text-decoration: none;

    display: inline-flex;

    align-items: center;

    gap: 5px;

    transition: .2s;
}

.btn-daftar:hover {
    background: #d63384;

    color: white;

    transform: translateY(-1px);

    box-shadow: 0 3px 8px rgba(214, 51, 132, .18);
}


/* =================================
   EMPTY
================================= */

.empty-data {
    text-align: center;

    padding: 40px 20px !important;

    color: #9d9caf !important;
}

.empty-data i {
    display: block;

    font-size: 34px;

    margin-bottom: 8px;

    color: #cecfda;
}


/* =================================
   RESPONSIVE
================================= */

@media (max-width: 768px) {

    .kebidanan-page {
        padding-top: 10px;
    }

    .kebidanan-header {
        padding: 14px 16px;
    }

    .kebidanan-header h1 {
        font-size: 21px;
    }

    .kebidanan-header p {
        margin-left: 0;

        font-size: 12px;
    }

    .search-form {
        flex-direction: column;
    }

    .btn-search {
        width: 100%;
    }

    .patient-table thead th,
    .patient-table tbody td {
        padding: 10px 11px;

        font-size: 12px;
    }

}

</style>


<div class="page-content-wrap">

    <div class="kebidanan-page">


        <!-- =================================
             HEADER
        ================================= -->

        <div class="kebidanan-header">

            <h1>

                <i class="bi bi-person-hearts"></i>

                Jadwalkan Kunjungan Kebidanan

            </h1>

            <p>

                Cari ibu hamil / peserta KB yang sudah terdaftar untuk
                dijadwalkan pelayanan hari ini. Untuk pasien baru, gunakan
                menu Registrasi Ibu Hamil / Registrasi KB terlebih dahulu.

            </p>

        </div>


        <!-- =================================
             SEARCH
        ================================= -->

        <div class="search-card">

            <div class="search-title">

                <i class="bi bi-search"></i>

                Cari Data Pasien

            </div>


            <form method="GET">

                <div class="search-form">

                    <div class="search-input-wrapper">

                        <i class="bi bi-search"></i>

                        <input
                            type="text"
                            name="keyword"
                            class="search-input"
                            placeholder="Cari nama, NIK, atau No. RM..."
                            value="<?= isset($_GET['keyword']) ? htmlspecialchars($_GET['keyword']) : ''; ?>"
                            autocomplete="off"
                        >

                    </div>


                    <button
                        type="submit"
                        class="btn-search"
                    >

                        <i class="bi bi-search me-1"></i>

                        Cari

                    </button>

                </div>

            </form>

        </div>


        <!-- =================================
             DATA PASIEN
        ================================= -->

        <div class="patient-card">


            <div class="patient-card-header">

                <i class="bi bi-people me-2"></i>

                Ibu Hamil &amp; Peserta KB Terdaftar

            </div>


            <div class="table-responsive">

                <table class="table patient-table">

                    <thead>

                        <tr>

                            <th class="text-center">
                                No
                            </th>

                            <th>
                                Jenis
                            </th>

                            <th>
                                Identitas
                            </th>

                            <th>
                                Tanggal Lahir
                            </th>

                            <th class="text-center">
                                Aksi
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                    <?php

                    $no = 1;

                    $keyword = isset($_GET['keyword'])
                        ? trim($_GET['keyword'])
                        : '';

                    $keywordSafe = mysqli_real_escape_string(
                        $koneksi,
                        $keyword
                    );


                    /*
                    |--------------------------------------------------------------------------
                    | QUERY IBU HAMIL & PESERTA KB TERDAFTAR
                    |--------------------------------------------------------------------------
                    | Digabung di PHP (bukan UNION) karena kolomnya berbeda jauh.
                    */

                    $dataTerdaftar = [];

                    if ($keyword != '') {

                        $queryIbuHamil = mysqli_query(
                            $koneksi,
                            "SELECT id, nama_ibu, nik, tgl_lahir
                             FROM tbl_ibu_hamil
                             WHERE nama_ibu LIKE '%$keywordSafe%'
                                OR nik LIKE '%$keywordSafe%'
                             ORDER BY nama_ibu ASC"
                        );

                        $queryKb = mysqli_query(
                            $koneksi,
                            "SELECT id_kb, no_peserta_kb, no_kk, nama_suami, tanggal_lahir
                             FROM tbl_kb
                             WHERE no_peserta_kb LIKE '%$keywordSafe%'
                                OR no_kk LIKE '%$keywordSafe%'
                                OR nama_suami LIKE '%$keywordSafe%'
                             ORDER BY no_peserta_kb ASC"
                        );

                    } else {

                        $queryIbuHamil = mysqli_query(
                            $koneksi,
                            "SELECT id, nama_ibu, nik, tgl_lahir
                             FROM tbl_ibu_hamil
                             ORDER BY nama_ibu ASC
                             LIMIT 20"
                        );

                        $queryKb = mysqli_query(
                            $koneksi,
                            "SELECT id_kb, no_peserta_kb, no_kk, nama_suami, tanggal_lahir
                             FROM tbl_kb
                             ORDER BY no_peserta_kb ASC
                             LIMIT 20"
                        );

                    }

                    if ($queryIbuHamil) {
                        while ($ih = mysqli_fetch_assoc($queryIbuHamil)) {
                            $dataTerdaftar[] = [
                                'jenis'    => 'Ibu Hamil',
                                'ref_id'   => $ih['id'],
                                'identitas'=> $ih['nama_ibu'] . ' (NIK: ' . ($ih['nik'] ?: '-') . ')',
                                'tgl_lahir'=> $ih['tgl_lahir'],
                            ];
                        }
                    }

                    if ($queryKb) {
                        while ($kb = mysqli_fetch_assoc($queryKb)) {
                            $dataTerdaftar[] = [
                                'jenis'    => 'KB',
                                'ref_id'   => $kb['id_kb'],
                                'identitas'=> 'No. Peserta ' . $kb['no_peserta_kb'] . ' - ' . $kb['nama_suami'],
                                'tgl_lahir'=> $kb['tanggal_lahir'],
                            ];
                        }
                    }


                    if (count($dataTerdaftar) > 0) {

                        foreach ($dataTerdaftar as $data) {

                    ?>


                        <tr>


                            <!-- NO -->

                            <td class="nomor">

                                <?= $no++; ?>

                            </td>


                            <!-- JENIS -->

                            <td>

                                <span class="rm-badge">

                                    <?= htmlspecialchars($data['jenis']); ?>

                                </span>

                            </td>


                            <!-- IDENTITAS -->

                            <td>

                                <div class="patient-name">

                                    <div class="patient-icon">

                                        <i class="bi bi-person"></i>

                                    </div>


                                    <span class="patient-name-text">

                                        <?= htmlspecialchars($data['identitas']); ?>

                                    </span>

                                </div>

                            </td>


                            <!-- TANGGAL LAHIR -->

                            <td>

                                <?= !empty($data['tgl_lahir'])
                                    ? date('d-m-Y', strtotime($data['tgl_lahir']))
                                    : '-';
                                ?>

                            </td>


                            <!-- AKSI -->

                            <td class="text-center">

                                <a
                                    href="proses-jadwal-kebidanan.php?jenis=<?= urlencode($data['jenis']); ?>&ref_id=<?= $data['ref_id']; ?>&balik=pendaftaran-kebidanan.php"
                                    class="btn-daftar"
                                    onclick="return confirm('Jadwalkan pasien ini untuk pelayanan kebidanan hari ini?');"
                                >
                                    <i class="bi bi-calendar-plus"></i>
                                    Jadwalkan Hari Ini
                                </a>

                            </td>


                        </tr>


                    <?php

                        }

                    } else {

                    ?>


                        <tr>

                            <td
                                colspan="5"
                                class="empty-data"
                            >

                                <i class="bi bi-person-x"></i>

                                <?php if ($keyword != '') { ?>

                                    Data dengan kata
                                    "<strong><?= htmlspecialchars($keyword); ?></strong>"
                                    tidak ditemukan.

                                <?php } else { ?>

                                    Belum ada ibu hamil / peserta KB yang terdaftar.

                                <?php } ?>

                            </td>

                        </tr>


                    <?php } ?>


                    </tbody>

                </table>

            </div>

        </div>

    </div>

</div>


<?php

require "../template/footer.php";

?>