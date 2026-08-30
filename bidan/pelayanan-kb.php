<?php

session_start();

require "../template/rbac.php";

// Hanya Bidan
cekAkses([ROLE_BIDAN]);

require "../config.php";

$title = "Pelayanan KB - Rekam Medis Puskesmas";


/*
|--------------------------------------------------------------------------
| SIMPAN PELAYANAN KB
|--------------------------------------------------------------------------
*/

if (isset($_POST['simpan'])) {

    $id_kb = mysqli_real_escape_string(
        $koneksi,
        $_POST['id_kb'] ?? ''
    );

    $tanggal_pelayanan = mysqli_real_escape_string(
        $koneksi,
        $_POST['tanggal_pelayanan'] ?? ''
    );

    $metode_kb = mysqli_real_escape_string(
        $koneksi,
        $_POST['metode_kb'] ?? ''
    );

    $keluhan = mysqli_real_escape_string(
        $koneksi,
        $_POST['keluhan'] ?? ''
    );

    $hasil_pemeriksaan = mysqli_real_escape_string(
        $koneksi,
        $_POST['hasil_pemeriksaan'] ?? ''
    );

    $efek_samping = mysqli_real_escape_string(
        $koneksi,
        $_POST['efek_samping'] ?? ''
    );

    $keterangan = mysqli_real_escape_string(
        $koneksi,
        $_POST['keterangan'] ?? ''
    );

    $obat_billing = mysqli_real_escape_string(
        $koneksi,
        trim($_POST['obat_billing'] ?? '')
    );

    // metode_kb sendiri adalah "tindakan" pelayanan KB-nya (harganya
    // dicari dari tbl_obat berdasarkan nama metode yang sama persis).
    $total_biaya = hitungTotalBiaya($koneksi, $metode_kb)
        + hitungTotalBiaya($koneksi, $obat_billing);

    // Kurangi stok obat tambahan (kalau ada dipilih) SEBELUM data
    // pelayanan disimpan, supaya kalau stok tidak cukup datanya juga
    // tidak ikut tersimpan setengah-setengah.
    try {

        kurangiStokObat($koneksi, $obat_billing);

    } catch (Exception $e) {

        // Redirect eksplisit ke pelayanan-kb.php?id_kb=... (bukan
        // window.history.back() yang bergantung pada riwayat browser)
        // -- modal Tambah Pelayanan KB otomatis kebuka lagi dengan
        // peserta yang sama terpilih, lihat blok JS di bawah.
        echo "
        <script>
            alert(" . json_encode($e->getMessage()) . ");
            window.location=" . json_encode("pelayanan-kb.php?id_kb=$id_kb") . ";
        </script>
        ";

        exit();

    }


    /*
    |--------------------------------------------------------------------------
    | VALIDASI
    |--------------------------------------------------------------------------
    */

    if (
        empty($id_kb) ||
        empty($tanggal_pelayanan) ||
        empty($metode_kb)
    ) {

        echo "
        <script>
            alert('Peserta KB, tanggal pelayanan, dan metode KB wajib diisi.');
            window.location=" . json_encode("pelayanan-kb.php?id_kb=$id_kb") . ";
        </script>
        ";

        exit();
    }


    /*
    |--------------------------------------------------------------------------
    | CEK PESERTA REGISTER KB
    |--------------------------------------------------------------------------
    */

    $cekPeserta = mysqli_query(
        $koneksi,
        "SELECT id_kb
         FROM tbl_kb
         WHERE id_kb = '$id_kb'"
    );

    if (!$cekPeserta) {

        die(
            "Query pengecekan peserta gagal: " .
            mysqli_error($koneksi)
        );
    }


    if (mysqli_num_rows($cekPeserta) == 0) {

        echo "
        <script>
            alert('Peserta KB tidak ditemukan pada Register KB.');
            window.location='pelayanan-kb.php';
        </script>
        ";

        exit();
    }


    /*
    |--------------------------------------------------------------------------
    | SIMPAN KE TBL_PELAYANAN_KB
    |--------------------------------------------------------------------------
    |
    | BB, TB dan tekanan darah TIDAK disimpan di sini
    | karena sudah dicatat pada pemeriksaan oleh Petugas.
    |
    */

    $querySimpan = mysqli_query(
        $koneksi,
        "INSERT INTO tbl_pelayanan_kb
        (
            id_kb,
            tanggal_pelayanan,
            metode_kb,
            keluhan,
            hasil_pemeriksaan,
            efek_samping,
            keterangan,
            obat_billing,
            total_biaya
        )
        VALUES
        (
            '$id_kb',
            '$tanggal_pelayanan',
            '$metode_kb',
            '$keluhan',
            '$hasil_pemeriksaan',
            '$efek_samping',
            '$keterangan',
            '$obat_billing',
            '$total_biaya'
        )"
    );


    /*
    |--------------------------------------------------------------------------
    | JIKA BERHASIL
    |--------------------------------------------------------------------------
    */

    if ($querySimpan) {

        // mysqli_insert_id() harus diambil SEBELUM query lain
        // dijalankan di koneksi yang sama, kalau tidak nilainya
        // ter-reset ke 0.
        $idPelayananBaru = mysqli_insert_id($koneksi);

        /*
        | Tandai jadwal kebidanan hari ini sebagai selesai
        */

        mysqli_query(
            $koneksi,
            "UPDATE tbl_pendaftaran_kebidanan
             SET status = 'Selesai'
             WHERE jenis_layanan = 'KB'
               AND ref_id = '$id_kb'
               AND tanggal = CURDATE()
               AND status IN ('Menunggu', 'Dipanggil')"
        );


        /*
        | Buat tagihan pembayaran untuk Petugas
        */

        $queryNamaPeserta = mysqli_query(
            $koneksi,
            "SELECT
                COALESCE(ibu.nama_ibu, CONCAT('Suami: ', k.nama_suami)) AS nama_pasien,
                ibu.bpjs
             FROM tbl_kb k
             LEFT JOIN tbl_ibu_hamil ibu ON k.no_kk = ibu.no_kk
             WHERE k.id_kb = '$id_kb'"
        );

        $namaPesertaRow = $queryNamaPeserta ? mysqli_fetch_assoc($queryNamaPeserta) : null;
        $namaPesertaUntukTagihan = $namaPesertaRow ? $namaPesertaRow['nama_pasien'] : 'Peserta KB';

        // Jenis pembayaran: sama seperti ibu hamil, diturunkan dari nomor
        // BPJS/KIS di tbl_ibu_hamil (dicocokkan lewat no_kk) kalau ada,
        // kalau tidak ketemu/kosong dianggap Umum.
        $jenisPembayaranTagihan = (!empty($namaPesertaRow['bpjs'])) ? 'BPJS' : 'Umum';

        upsertPembayaran(
            $koneksi,
            'kb',
            $idPelayananBaru,
            $namaPesertaUntukTagihan,
            'Kebidanan',
            $tanggal_pelayanan,
            $total_biaya,
            $jenisPembayaranTagihan
        );


        echo "
        <script>
            alert('Data pelayanan KB berhasil disimpan.');
            window.location='pelayanan-kb.php';
        </script>
        ";

        exit();

    } else {

        echo "
        <script>
            alert('Data pelayanan KB gagal disimpan: " .
            addslashes(mysqli_error($koneksi)) .
            "');
            window.location=" . json_encode("pelayanan-kb.php?id_kb=$id_kb") . ";
        </script>
        ";

        exit();
    }
}


/*
|--------------------------------------------------------------------------
| DATA PESERTA REGISTER KB
|--------------------------------------------------------------------------
*/

$queryPesertaKB = mysqli_query(
    $koneksi,
    "SELECT
        id_kb,
        no_peserta_kb,
        no_kk,
        tanggal,
        tanggal_lahir,
        nama_suami,
        jumlah_anak,
        alamat,
        jenis_kb,
        kunjungan
     FROM tbl_kb
     ORDER BY id_kb DESC"
);

if (!$queryPesertaKB) {

    die(
        "Query Register KB gagal: " .
        mysqli_error($koneksi)
    );
}


/*
|--------------------------------------------------------------------------
| DATA PELAYANAN KB
|--------------------------------------------------------------------------
*/

$queryPelayananKB = mysqli_query(
    $koneksi,
    "SELECT
        p.id_pelayanan_kb,
        p.id_kb,
        p.tanggal_pelayanan,
        p.metode_kb,
        p.keluhan,
        p.hasil_pemeriksaan,
        p.efek_samping,
        p.keterangan,

        k.no_peserta_kb,
        k.no_kk,
        k.tanggal_lahir,
        k.nama_suami,
        k.jumlah_anak,
        k.alamat,
        k.jenis_kb,
        k.kunjungan

    FROM tbl_pelayanan_kb p

    INNER JOIN tbl_kb k
        ON p.id_kb = k.id_kb

    ORDER BY p.id_pelayanan_kb DESC"
);

if (!$queryPelayananKB) {

    die(
        "Query pelayanan KB gagal: " .
        mysqli_error($koneksi)
    );
}


/*
|--------------------------------------------------------------------------
| DATA METODE KB (TINDAKAN) & OBAT UNTUK TAGIHAN
|--------------------------------------------------------------------------
*/

$queryMetodeKB = mysqli_query(
    $koneksi,
    "SELECT nama, harga FROM tbl_obat WHERE kategori = 'Tindakan' ORDER BY nama ASC"
);

$daftarMetodeKB = [];

while ($m = mysqli_fetch_assoc($queryMetodeKB)) {
    $daftarMetodeKB[] = $m;
}

$nmObatKB = [];

$queryObatKB = mysqli_query(
    $koneksi,
    "SELECT nama FROM tbl_obat WHERE kategori = 'Obat' AND stok > 0 ORDER BY nama ASC"
);

while ($o = mysqli_fetch_assoc($queryObatKB)) {
    $nmObatKB[] = $o['nama'];
}


require "../template/header.php";
require "../template/navbar.php";
require "../template/sidebar.php";

?>


<style>

/* =========================================================
   PELAYANAN KB
========================================================= */

:root {
    --kb-dark: #212229;
    --kb-gray: #6c757d;
    --kb-light: #f8f8fa;
    --kb-border: #e9e9ef;
}


/* =========================================================
   MAIN CONTENT
========================================================= */

.kb-page {
    padding-bottom: 40px;
}


/* =========================================================
   HEADER
========================================================= */

.kb-header {
    padding-top: 22px;
    padding-bottom: 18px;
    margin-bottom: 25px;
    border-bottom: 1px solid var(--kb-border);
}

.kb-header-title {
    font-size: 27px;
    font-weight: 700;
    color: #212229;
    margin-bottom: 5px;
    letter-spacing: -0.3px;
}

.kb-header-subtitle {
    color: #6c757d;
    font-size: 14px;
    margin-bottom: 0;
}

.kb-header-actions {
    display: flex;
    align-items: center;
    gap: 10px;
}


/* =========================================================
   BUTTON
========================================================= */

.kb-btn {
    border-radius: 8px;
    padding: 9px 15px;
    font-size: 14px;
    font-weight: 500;
    transition: all .2s ease;
}

.kb-btn:hover {
    transform: translateY(-1px);
}

.kb-btn-dark {
    background: #212229;
    border-color: #212229;
    color: white;
}

.kb-btn-dark:hover {
    background: #343540;
    border-color: #343540;
    color: white;
}

.kb-btn-outline {
    background: white;
    border: 1px solid #212229;
    color: #212229;
}

.kb-btn-outline:hover {
    background: #212229;
    color: white;
}


/* =========================================================
   CARD
========================================================= */

.kb-card {
    background: #ffffff;
    border: 1px solid #edeef2;
    border-radius: 12px;
    box-shadow: 0 4px 18px rgba(0, 0, 0, .045);
    overflow: hidden;
}

.kb-card-body {
    padding: 22px;
}


/* =========================================================
   SEARCH
========================================================= */

.kb-search-wrapper {
    margin-bottom: 20px;
}

.kb-search {
    max-width: 430px;
}

.kb-search .input-group-text {
    background: #ffffff;
    border-right: 0;
    border-color: #dedfe6;
    color: #6c757d;
}

.kb-search .form-control {
    border-left: 0;
    height: 42px;
    font-size: 14px;
}

.kb-search .form-control:focus {
    box-shadow: none;
    border-color: #212229;
}

.kb-search .input-group:focus-within .input-group-text {
    border-color: #212229;
}


/* =========================================================
   TABLE
========================================================= */

.kb-table-wrapper {
    border: 1px solid #e9e9ef;
    border-radius: 9px;
    overflow: hidden;
}

.kb-table {
    margin-bottom: 0 !important;
    font-size: 13.5px;
}

.kb-table thead th {
    background: #212229 !important;
    color: #ffffff;
    font-weight: 600;
    font-size: 13px;
    padding: 13px 12px;
    border-color: #343540;
    white-space: nowrap;
    vertical-align: middle;
}

.kb-table tbody td {
    padding: 12px;
    border-color: #edeef2;
    vertical-align: middle;
    color: #343540;
}

.kb-table tbody tr {
    transition: background-color .15s ease;
}

.kb-table tbody tr:hover {
    background-color: #f8f8fa;
}

.kb-table tbody tr:last-child td {
    border-bottom: 0;
}


/* =========================================================
   BADGE
========================================================= */

.kb-badge-peserta {
    display: inline-block;
    background: #212229;
    color: #ffffff;
    padding: 6px 10px;
    border-radius: 6px;
    font-size: 12px;
    font-weight: 600;
    letter-spacing: .2px;
}

.kb-badge-metode {
    display: inline-block;
    background: #f8f8fa;
    border: 1px solid #dedfe6;
    color: #494a57;
    padding: 5px 10px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 500;
}


/* =========================================================
   ACTION
========================================================= */

.kb-action {
    display: flex;
    justify-content: center;
    gap: 5px;
}

.kb-action .btn {
    width: 34px;
    height: 34px;
    display: inline-flex;
    justify-content: center;
    align-items: center;
    border-radius: 7px;
    transition: all .2s ease;
}

.kb-action .btn:hover {
    transform: translateY(-1px);
}

.kb-action-detail {
    background: #6c757d;
    border-color: #6c757d;
    color: white;
}

.kb-action-detail:hover {
    background: #5c636a;
    color: white;
}

.kb-action-edit {
    background: #212229;
    border-color: #212229;
    color: white;
}

.kb-action-edit:hover {
    background: #343540;
    color: white;
}

.kb-action-delete {
    background: white;
    border: 1px solid #212229;
    color: #212229;
}

.kb-action-delete:hover {
    background: #212229;
    color: white;
}


/* =========================================================
   EMPTY
========================================================= */

.kb-empty {
    padding: 55px 20px !important;
    color: #6c757d;
}

.kb-empty i {
    font-size: 45px;
    opacity: .45;
}

.kb-empty p {
    margin-top: 12px;
    margin-bottom: 0;
    font-size: 14px;
}


/* =========================================================
   MODAL
========================================================= */

.kb-modal .modal-content {
    border: 0;
    border-radius: 14px;
    overflow: hidden;
    box-shadow: 0 15px 45px rgba(0, 0, 0, .18);
}

.kb-modal .modal-header {
    background: #212229;
    color: white;
    padding: 17px 22px;
    border-bottom: 0;
}

.kb-modal .modal-title {
    font-size: 18px;
    font-weight: 600;
}

.kb-modal .btn-close {
    filter: invert(1);
    opacity: .9;
}

.kb-modal .modal-body {
    background: #ffffff;
    padding: 24px;
}

.kb-modal .modal-footer {
    padding: 15px 22px;
    border-top: 1px solid #e9e9ef;
}


/* =========================================================
   SECTION
========================================================= */

.kb-section-title {
    display: flex;
    align-items: center;
    gap: 9px;
    font-size: 15px;
    font-weight: 700;
    color: #212229;
    padding-bottom: 10px;
    margin-bottom: 18px;
    border-bottom: 1px solid #e9e9ef;
}

.kb-section-title i {
    font-size: 17px;
}


/* =========================================================
   FORM
========================================================= */

.kb-form-label {
    font-size: 13.5px;
    font-weight: 600;
    color: #343540;
    margin-bottom: 7px;
}

.kb-modal .form-control,
.kb-modal .form-select {
    border-radius: 7px;
    border-color: #dfe0e6;
    min-height: 42px;
    font-size: 13.5px;
    transition: all .2s ease;
}

.kb-modal textarea.form-control {
    min-height: 95px;
    resize: vertical;
}

.kb-modal .form-control:focus,
.kb-modal .form-select:focus {
    border-color: #212229;
    box-shadow: 0 0 0 .15rem rgba(33, 37, 41, .08);
}


/* =========================================================
   INFORMASI PESERTA
========================================================= */

.kb-info-peserta {
    background: #f8f8fa;
    border: 1px solid #e5e5e9;
    border-radius: 10px;
    padding: 17px;
    margin-top: 3px;
}

.kb-info-item {
    padding: 4px 10px;
    border-right: 1px solid #dedfe6;
}

.kb-info-item:last-child {
    border-right: 0;
}

.kb-info-label {
    display: block;
    color: #6c757d;
    font-size: 11.5px;
    margin-bottom: 3px;
    text-transform: uppercase;
    letter-spacing: .3px;
}

.kb-info-value {
    color: #212229;
    font-size: 14px;
    font-weight: 600;
    word-break: break-word;
}


/* =========================================================
   MODAL BUTTON
========================================================= */

.kb-modal .btn {
    border-radius: 7px;
    padding: 8px 15px;
    font-size: 13.5px;
    font-weight: 500;
}


/* =========================================================
   RESPONSIVE
========================================================= */

@media (max-width: 991.98px) {

    .kb-header {
        align-items: flex-start !important;
    }

    .kb-header-actions {
        margin-top: 15px;
        width: 100%;
    }

    .kb-header-actions .btn {
        flex: 1;
    }
}


@media (max-width: 767.98px) {

    .kb-header {
        flex-direction: column;
    }

    .kb-header-title {
        font-size: 23px;
    }

    .kb-header-actions {
        flex-direction: column;
    }

    .kb-header-actions .btn {
        width: 100%;
    }

    .kb-card-body {
        padding: 15px;
    }

    .kb-search {
        max-width: 100%;
    }

    .kb-table {
        font-size: 12px;
    }

    .kb-table thead th,
    .kb-table tbody td {
        padding: 9px;
    }

    .kb-modal .modal-body {
        padding: 17px;
    }

    .kb-info-item {
        border-right: 0;
        border-bottom: 1px solid #dedfe6;
        padding: 10px 5px;
    }

    .kb-info-item:last-child {
        border-bottom: 0;
    }
}

</style>


<div class="kb-page page-content-wrap">


    <!-- =====================================================
         HEADER
    ====================================================== -->

    <div class="kb-header d-flex justify-content-between
                flex-wrap flex-md-nowrap
                align-items-center">

        <div>

            <h1 class="kb-header-title">
                Pelayanan KB
            </h1>

            <p class="kb-header-subtitle">
                Data pemeriksaan dan pelayanan peserta KB
            </p>

        </div>


        <div class="kb-header-actions">

            <!-- CETAK LAPORAN -->

            <a
                href="../laporan/laporan-kb.php"
                target="_blank"
                class="btn kb-btn kb-btn-outline">

                <i class="bi bi-file-earmark-pdf me-1"></i>

                Cetak Laporan

            </a>


            <!-- TAMBAH -->

            <button
                type="button"
                class="btn kb-btn kb-btn-dark"
                data-toggle="modal"
                data-target="#modalPelayananKB">

                <i class="bi bi-plus-lg me-1"></i>

                Tambah Pelayanan KB

            </button>

        </div>

    </div>


    <!-- =====================================================
         TABEL DATA PELAYANAN
    ====================================================== -->

    <div class="kb-card mb-4">

        <div class="kb-card-body">


            <!-- SEARCH -->

            <div class="kb-search-wrapper">

                <div class="kb-search">

                    <div class="input-group">

                        <span class="input-group-text">

                            <i class="bi bi-search"></i>

                        </span>

                        <input
                            type="text"
                            id="searchPelayananKB"
                            class="form-control"
                            placeholder="Cari peserta KB, No. KK, metode...">

                    </div>

                </div>

            </div>


            <!-- TABLE -->

            <div class="table-responsive kb-table-wrapper">

                <table
                    class="table table-bordered table-hover align-middle kb-table"
                    id="tabelPelayananKB">

                    <thead>

                        <tr>

                            <th
                                class="text-center"
                                style="width:50px;">

                                No

                            </th>

                            <th>
                                Tanggal
                            </th>

                            <th>
                                No. Peserta KB
                            </th>

                            <th>
                                No. KK
                            </th>

                            <th>
                                Nama Suami
                            </th>

                            <th>
                                Metode KB
                            </th>

                            <th>
                                Hasil
                            </th>

                            <th
                                class="text-center"
                                style="width:130px;">

                                Aksi

                            </th>

                        </tr>

                    </thead>


                    <tbody>

                    <?php

                    $no = 1;

                    if (mysqli_num_rows($queryPelayananKB) > 0) {

                        while (
                            $data = mysqli_fetch_assoc(
                                $queryPelayananKB
                            )
                        ) {

                    ?>

                        <tr>

                            <!-- NO -->

                            <td class="text-center">

                                <?= $no++; ?>

                            </td>


                            <!-- TANGGAL -->

                            <td>

                                <?php

                                if (
                                    !empty(
                                        $data['tanggal_pelayanan']
                                    )
                                ) {

                                    echo date(
                                        'd-m-Y',
                                        strtotime(
                                            $data['tanggal_pelayanan']
                                        )
                                    );

                                } else {

                                    echo '-';

                                }

                                ?>

                            </td>


                            <!-- NO PESERTA -->

                            <td>

                                <span class="kb-badge-peserta">

                                    <?= htmlspecialchars(
                                        $data['no_peserta_kb']
                                    ); ?>

                                </span>

                            </td>


                            <!-- NO KK -->

                            <td>

                                <?= htmlspecialchars(
                                    $data['no_kk']
                                ); ?>

                            </td>


                            <!-- NAMA SUAMI -->

                            <td>

                                <?= htmlspecialchars(
                                    $data['nama_suami'] ?: '-'
                                ); ?>

                            </td>


                            <!-- METODE KB -->

                            <td>

                                <span class="kb-badge-metode">

                                    <?= htmlspecialchars(
                                        $data['metode_kb']
                                    ); ?>

                                </span>

                            </td>


                            <!-- HASIL -->

                            <td>

                                <?php

                                if (
                                    !empty(
                                        $data['hasil_pemeriksaan']
                                    )
                                ) {

                                    echo htmlspecialchars(
                                        $data['hasil_pemeriksaan']
                                    );

                                } else {

                                    echo '-';

                                }

                                ?>

                            </td>


                            <!-- AKSI -->

                            <td class="text-center">

                                <div class="kb-action">


                                    <!-- DETAIL -->

                                    <a
                                        href="detail-pelayanan-kb.php?id=<?= $data['id_pelayanan_kb']; ?>"
                                        class="btn kb-action-detail"
                                        title="Detail">

                                        <i class="bi bi-eye"></i>

                                    </a>


                                    <!-- EDIT -->

                                    <a
                                        href="edit-pelayanan-kb.php?id=<?= $data['id_pelayanan_kb']; ?>"
                                        class="btn kb-action-edit"
                                        title="Edit">

                                        <i class="bi bi-pencil"></i>

                                    </a>


                                    <!-- HAPUS -->

                                    <a
                                        href="hapus-pelayanan-kb.php?id=<?= $data['id_pelayanan_kb']; ?>"
                                        class="btn kb-action-delete"
                                        title="Hapus"
                                        onclick="return confirm('Yakin ingin menghapus data pelayanan KB ini?');">

                                        <i class="bi bi-trash"></i>

                                    </a>

                                </div>

                            </td>

                        </tr>

                    <?php

                        }

                    } else {

                    ?>

                        <tr>

                            <td
                                colspan="8"
                                class="kb-empty text-center">

                                <i class="bi bi-database-x"></i>

                                <p>
                                    Belum ada data pelayanan KB.
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
     MODAL TAMBAH PELAYANAN KB
========================================================= -->

<div
    class="modal fade kb-modal"
    id="modalPelayananKB"
    tabindex="-1"
    aria-labelledby="modalPelayananKBLabel"
    aria-hidden="true">


    <div
        class="modal-dialog modal-xl modal-dialog-scrollable">


        <div class="modal-content">


            <!-- HEADER MODAL -->

            <div class="modal-header">

                <h5
                    class="modal-title"
                    id="modalPelayananKBLabel">

                    <i class="bi bi-heart-pulse me-2"></i>

                    Tambah Pelayanan KB

                </h5>


                <button
                    type="button"
                    class="btn-close"
                    data-dismiss="modal"
                    aria-label="Close">

                </button>

            </div>


            <!-- FORM -->

            <form
                method="POST"
                action="pelayanan-kb.php">


                <!-- BODY -->

                <div
                    class="modal-body"
                    style="max-height:70vh; overflow-y:auto;">


                    <!-- DATA PELAYANAN -->

                    <div class="kb-section-title">

                        <i class="bi bi-person-vcard"></i>

                        Data Pelayanan

                    </div>


                    <div class="row g-3">


                        <!-- TANGGAL -->

                        <div class="col-md-6">

                            <label class="kb-form-label">

                                Tanggal Pelayanan

                            </label>

                            <input
                                type="date"
                                name="tanggal_pelayanan"
                                class="form-control"
                                value="<?= date('Y-m-d'); ?>"
                                required>

                        </div>


                        <!-- PESERTA -->

                        <div class="col-md-6">

                            <label class="kb-form-label">

                                Peserta KB

                            </label>


                            <select
                                name="id_kb"
                                id="id_kb"
                                class="form-select"
                                required>

                                <option value="">

                                    Pilih Peserta KB

                                </option>


                                <?php

                                mysqli_data_seek(
                                    $queryPesertaKB,
                                    0
                                );

                                while (
                                    $peserta =
                                    mysqli_fetch_assoc(
                                        $queryPesertaKB
                                    )
                                ) {

                                ?>

                                    <option
                                        value="<?= $peserta['id_kb']; ?>"

                                        <?= (
                                            isset($_GET['id_kb']) &&
                                            (int) $_GET['id_kb'] ===
                                            (int) $peserta['id_kb']
                                        )
                                            ? 'selected'
                                            : '';
                                        ?>

                                        data-kk="<?= htmlspecialchars(
                                            $peserta['no_kk']
                                        ); ?>"

                                        data-suami="<?= htmlspecialchars(
                                            $peserta['nama_suami']
                                        ); ?>"

                                        data-anak="<?= htmlspecialchars(
                                            $peserta['jumlah_anak']
                                        ); ?>"

                                        data-jenis="<?= htmlspecialchars(
                                            $peserta['jenis_kb']
                                        ); ?>"

                                        data-alamat="<?= htmlspecialchars(
                                            $peserta['alamat']
                                        ); ?>">

                                        <?= htmlspecialchars(
                                            $peserta['no_peserta_kb']
                                        ); ?>

                                        -

                                        <?= htmlspecialchars(
                                            $peserta['nama_suami'] ?: '-'
                                        ); ?>

                                        &nbsp;

                                        (KK:

                                        <?= htmlspecialchars(
                                            $peserta['no_kk']
                                        ); ?>)

                                    </option>

                                <?php

                                }

                                ?>

                            </select>

                        </div>


                        <!-- INFORMASI PESERTA -->

                        <div class="col-12">

                            <div
                                id="infoPesertaKB"
                                class="kb-info-peserta d-none">

                                <div class="row g-3">


                                    <div class="col-md-3 kb-info-item">

                                        <span class="kb-info-label">

                                            No. KK

                                        </span>

                                        <div
                                            id="infoKK"
                                            class="kb-info-value">

                                            -

                                        </div>

                                    </div>


                                    <div class="col-md-3 kb-info-item">

                                        <span class="kb-info-label">

                                            Nama Suami

                                        </span>

                                        <div
                                            id="infoSuami"
                                            class="kb-info-value">

                                            -

                                        </div>

                                    </div>


                                    <div class="col-md-3 kb-info-item">

                                        <span class="kb-info-label">

                                            Jumlah Anak

                                        </span>

                                        <div
                                            id="infoAnak"
                                            class="kb-info-value">

                                            -

                                        </div>

                                    </div>


                                    <div class="col-md-3 kb-info-item">

                                        <span class="kb-info-label">

                                            Jenis KB Register

                                        </span>

                                        <div
                                            id="infoJenisKB"
                                            class="kb-info-value">

                                            -

                                        </div>

                                    </div>

                                </div>

                            </div>

                        </div>


                        <!-- METODE KB -->

                        <div class="col-md-6">

                            <label class="kb-form-label">

                                Metode / Alat Kontrasepsi

                            </label>


                            <select
                                name="metode_kb"
                                id="metode_kb"
                                class="form-select"
                                required>

                                <option value="">

                                    Pilih Metode KB

                                </option>

                                <?php foreach ($daftarMetodeKB as $m) { ?>

                                    <option
                                        value="<?= htmlspecialchars($m['nama']); ?>"
                                        data-harga="<?= (float) $m['harga']; ?>">

                                        <?= htmlspecialchars($m['nama']); ?>
                                        (Rp <?= number_format((float) $m['harga'], 0, ',', '.'); ?>)

                                    </option>

                                <?php } ?>

                            </select>

                            <small class="text-muted">
                                Daftar metode & harganya diatur lewat menu
                                Obat (Petugas), kategori Tindakan.
                            </small>

                        </div>


                        <!-- OBAT TAMBAHAN -->

                        <div class="col-md-6">

                            <label class="kb-form-label">

                                Obat Tambahan (opsional)

                            </label>

                            <input
                                type="text"
                                name="obat_billing"
                                id="tokenfieldObatKB"
                                class="form-control"
                                placeholder="Ketik nama obat lalu tekan Enter...">

                        </div>


                        <!-- ESTIMASI TAGIHAN -->

                        <div class="col-12">

                            <div class="d-flex justify-content-between align-items-center"
                                 style="background:#f8f8fa;border:1px solid #e5e5e9;border-radius:10px;padding:12px 15px;">

                                <span>
                                    <i class="bi bi-cash-coin me-1"></i>
                                    Estimasi Tagihan Pasien
                                </span>

                                <strong id="estimasiTagihanKB">
                                    Rp 0
                                </strong>

                            </div>

                        </div>


                        <!-- KELUHAN -->

                        <div class="col-md-6">

                            <label class="kb-form-label">

                                Keluhan

                            </label>

                            <input
                                type="text"
                                name="keluhan"
                                class="form-control"
                                placeholder="Masukkan keluhan">

                        </div>

                    </div>


                    <!-- HASIL PEMERIKSAAN -->

                    <div class="kb-section-title mt-4">

                        <i class="bi bi-clipboard2-pulse"></i>

                        Hasil Pelayanan

                    </div>


                    <div class="row g-3">


                        <!-- HASIL -->

                        <div class="col-md-6">

                            <label class="kb-form-label">

                                Hasil Pemeriksaan

                            </label>

                            <textarea
                                name="hasil_pemeriksaan"
                                class="form-control"
                                rows="3"
                                placeholder="Masukkan hasil pemeriksaan"></textarea>

                        </div>


                        <!-- EFEK SAMPING -->

                        <div class="col-md-6">

                            <label class="kb-form-label">

                                Efek Samping

                            </label>

                            <textarea
                                name="efek_samping"
                                class="form-control"
                                rows="3"
                                placeholder="Masukkan efek samping"></textarea>

                        </div>


                        <!-- KETERANGAN -->

                        <div class="col-12">

                            <label class="kb-form-label">

                                Keterangan

                            </label>

                            <textarea
                                name="keterangan"
                                class="form-control"
                                rows="3"
                                placeholder="Masukkan keterangan"></textarea>

                        </div>

                    </div>

                </div>


                <!-- FOOTER -->

                <div class="modal-footer bg-light">

                    <button
                        type="button"
                        class="btn btn-secondary"
                        data-dismiss="modal">

                        <i class="bi bi-x-lg me-1"></i>

                        Batal

                    </button>


                    <button
                        type="submit"
                        name="simpan"
                        value="1"
                        class="btn kb-btn-dark">

                        <i class="bi bi-save me-1"></i>

                        Simpan

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>


<!-- =========================================================
     JAVASCRIPT
========================================================= -->

<script>


/*
|--------------------------------------------------------------------------
| SEARCH
|--------------------------------------------------------------------------
*/

const searchPelayananKB =
    document.getElementById("searchPelayananKB");

if (searchPelayananKB) {

    searchPelayananKB.addEventListener(
        "keyup",
        function () {

            let keyword =
                this.value.toLowerCase();

            let rows =
                document.querySelectorAll(
                    "#tabelPelayananKB tbody tr"
                );

            rows.forEach(function (row) {

                let text =
                    row.textContent.toLowerCase();

                row.style.display =
                    text.includes(keyword)
                        ? ""
                        : "none";

            });

        }
    );

}


/*
|--------------------------------------------------------------------------
| INFORMASI PESERTA KB
|--------------------------------------------------------------------------
*/

const selectPeserta =
    document.getElementById("id_kb");

if (selectPeserta) {

    selectPeserta.addEventListener(
        "change",
        function () {

            let option =
                this.options[
                    this.selectedIndex
                ];

            let info =
                document.getElementById(
                    "infoPesertaKB"
                );


            if (!this.value) {

                info.classList.add("d-none");

                document.getElementById(
                    "infoKK"
                ).textContent = "-";

                document.getElementById(
                    "infoSuami"
                ).textContent = "-";

                document.getElementById(
                    "infoAnak"
                ).textContent = "-";

                document.getElementById(
                    "infoJenisKB"
                ).textContent = "-";

                return;
            }


            document.getElementById(
                "infoKK"
            ).textContent =
                option.dataset.kk || "-";


            document.getElementById(
                "infoSuami"
            ).textContent =
                option.dataset.suami || "-";


            document.getElementById(
                "infoAnak"
            ).textContent =
                option.dataset.anak || "0";


            document.getElementById(
                "infoJenisKB"
            ).textContent =
                option.dataset.jenis || "-";


            info.classList.remove("d-none");

        }
    );

}


/*
|--------------------------------------------------------------------------
| RESET MODAL
|--------------------------------------------------------------------------
*/

const modalPelayananKB =
    document.getElementById(
        "modalPelayananKB"
    );

if (modalPelayananKB) {

    modalPelayananKB.addEventListener(
        "hidden.bs.modal",
        function () {

            let form =
                this.querySelector("form");

            if (form) {
                form.reset();
            }


            document
                .getElementById(
                    "infoPesertaKB"
                )
                .classList.add(
                    "d-none"
                );


            document
                .getElementById(
                    "infoKK"
                )
                .textContent = "-";


            document
                .getElementById(
                    "infoSuami"
                )
                .textContent = "-";


            document
                .getElementById(
                    "infoAnak"
                )
                .textContent = "-";


            document
                .getElementById(
                    "infoJenisKB"
                )
                .textContent = "-";

        }
    );

}


/*
|--------------------------------------------------------------------------
| BUKA MODAL OTOMATIS DARI JADWAL
|--------------------------------------------------------------------------
*/

<?php if (isset($_GET['id_kb'])) { ?>

$(document).ready(function () {

    $('#id_kb').trigger('change');

    $('#modalPelayananKB').modal('show');

});

<?php } ?>

</script>


<!-- ==========================================================
     TOKENFIELD OBAT TAMBAHAN + ESTIMASI TAGIHAN
=========================================================== -->

<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-tokenfield/0.12.0/bootstrap-tokenfield.js"></script>

<script>

$(document).ready(function () {

    $('#tokenfieldObatKB').tokenfield({

        autocomplete: {
            source: <?= json_encode($nmObatKB); ?>,
            delay: 100
        },

        showAutocompleteOnFocus: true

    });


    <?php
    $queryHargaObatKB = mysqli_query($koneksi, "SELECT nama, harga FROM tbl_obat WHERE kategori = 'Obat' AND stok > 0");
    $petaHargaObatKB = [];
    while ($h = mysqli_fetch_assoc($queryHargaObatKB)) {
        $petaHargaObatKB[$h['nama']] = $h['harga'];
    }
    ?>

    var hargaObatKB = <?= json_encode($petaHargaObatKB); ?>;


    function hitungEstimasiKB() {

        var total = 0;

        var opt = document.getElementById('metode_kb');

        if (opt && opt.selectedOptions.length) {
            total += parseFloat(opt.selectedOptions[0].dataset.harga || 0);
        }

        var obatItems = ($('#tokenfieldObatKB').val() || '').split(',');

        obatItems.forEach(function (nama) {
            nama = nama.trim();
            if (nama !== '' && hargaObatKB.hasOwnProperty(nama)) {
                total += parseFloat(hargaObatKB[nama]);
            }
        });

        $('#estimasiTagihanKB').text('Rp ' + total.toLocaleString('id-ID'));

    }

    $(document).on('change', '#metode_kb', hitungEstimasiKB);

    $(document).on('tokenfield:createdtoken tokenfield:removedtoken', '#tokenfieldObatKB', function () {
        setTimeout(hitungEstimasiKB, 50);
    });

});

</script>


<?php

require "../template/footer.php";

?>