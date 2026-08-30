<?php

session_start();

require "../template/rbac.php";

// Hanya Petugas
cekAkses([ROLE_PETUGAS]);

require "../config.php";

$title = "Pembayaran - rekammedispuskesmas";

require "../template/header.php";
require "../template/navbar.php";
require "../template/sidebar.php";


/* ==========================================
   TAB AKTIF
========================================== */

$tab = (isset($_GET['tab']) && $_GET['tab'] === 'riwayat') ? 'riwayat' : 'belum-bayar';

$statusFilter = ($tab === 'riwayat') ? 'Lunas' : 'Belum Bayar';


/* ==========================================
   PESAN
========================================== */

$msg = isset($_GET['msg']) ? $_GET['msg'] : '';

$alert = "";

if ($msg == 'lunas') {

    $alert = '
    <div class="alert alert-success alert-dismissible fade show custom-alert" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i>
        <strong>Pembayaran berhasil dicatat.</strong>
        <button type="button" class="btn-close" data-dismiss="alert" aria-label="Close"></button>
    </div>';

}


/* ==========================================
   DATA PEMBAYARAN
========================================== */

$queryPembayaran = mysqli_query($koneksi, "
    SELECT
        p.*,
        u.fullname AS nama_petugas
    FROM tbl_pembayaran p
    LEFT JOIN tbl_user u ON p.id_petugas = u.userid
    WHERE p.status = '$statusFilter'
    ORDER BY " . ($tab === 'riwayat' ? "p.tanggal_bayar DESC" : "p.tanggal ASC, p.id ASC") . "
");

if (!$queryPembayaran) {
    die("Query pembayaran gagal: " . mysqli_error($koneksi));
}

$totalBaris = mysqli_num_rows($queryPembayaran);


/* ==========================================
   BADGE JUMLAH BELUM BAYAR (UNTUK TAB)
========================================== */

$queryJumlahBelumBayar = mysqli_query($koneksi, "
    SELECT COUNT(*) AS total FROM tbl_pembayaran WHERE status = 'Belum Bayar'
");
$jumlahBelumBayar = mysqli_fetch_assoc($queryJumlahBelumBayar)['total'];

?>


<style>

/* =====================================================
   PEMBAYARAN
===================================================== */

.pembayaran-container {
    padding-bottom: 40px;
}


/* =====================================================
   HEADER
===================================================== */

.pembayaran-header {
    background: #ffffff;
    border: 1px solid #e8e8f3;
    border-radius: 16px;
    padding: 22px 25px;
    margin-top: 20px;
    margin-bottom: 20px;
    box-shadow: 0 5px 18px rgba(0, 0, 0, 0.04);
}

.pembayaran-title {
    display: flex;
    align-items: center;
    gap: 13px;
}

.pembayaran-title-icon {
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

.pembayaran-title h1 {
    font-size: 24px;
    font-weight: 700;
    color: #262a38;
    margin: 0;
}

.pembayaran-title p {
    font-size: 13px;
    color: #7b7c94;
    margin: 4px 0 0;
}


/* =====================================================
   TAB
===================================================== */

.pembayaran-tabs {
    display: flex;
    gap: 8px;
    margin-bottom: 20px;
}

.pembayaran-tab {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    padding: 10px 18px;
    border-radius: 10px;
    background: #ffffff;
    border: 1px solid #e8e8f3;
    color: #56576d;
    font-size: 14px;
    font-weight: 600;
    text-decoration: none;
    transition: .2s;
}

.pembayaran-tab:hover {
    color: #7571f9;
    text-decoration: none;
}

.pembayaran-tab.active {
    background: #7571f9;
    border-color: #7571f9;
    color: #ffffff;
}

.pembayaran-tab .count-badge {
    background: rgba(255,255,255,.25);
    padding: 2px 8px;
    border-radius: 20px;
    font-size: 12px;
}

.pembayaran-tab:not(.active) .count-badge {
    background: #f1f2f9;
    color: #7571f9;
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
}

.table-card-header {
    padding: 17px 20px;
    border-bottom: 1px solid #ededf4;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 10px;
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

#searchPembayaran {
    height: 42px;
    border-radius: 10px;
    padding-left: 15px;
    border: 1px solid #dcddea;
    font-size: 14px;
    min-width: 260px;
}

#searchPembayaran:focus {
    border-color: #7571f9;
    box-shadow: 0 0 0 3px rgba(117, 113, 249, 0.10);
}


/* =====================================================
   TABLE
===================================================== */

.table-wrapper {
    width: 100%;
    overflow-x: auto;
}

#tablePembayaran {
    margin: 0;
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
}

#tablePembayaran thead th {
    background: #f4f4fb;
    color: #45465e;
    font-size: 12px;
    font-weight: 700;
    padding: 15px 14px;
    border-bottom: 1px solid #e2e2ed;
    white-space: nowrap;
    vertical-align: middle;
}

#tablePembayaran tbody td {
    padding: 14px;
    font-size: 13px;
    color: #53556d;
    border-bottom: 1px solid #ededf3;
    vertical-align: middle;
}

#tablePembayaran tbody tr:hover {
    background: #f8f8ff;
}


/* =====================================================
   BADGE
===================================================== */

.poli-badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 5px 9px;
    border-radius: 7px;
    font-size: 11px;
    font-weight: 600;
    white-space: nowrap;
}

.poli-umum {
    background: #eae9ff;
    color: #7571f9;
}

.poli-kebidanan {
    background: #ffeef4;
    color: #d6336c;
}

.status-badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 5px 9px;
    border-radius: 7px;
    font-size: 11px;
    font-weight: 600;
    white-space: nowrap;
}

.status-belum {
    background: #fff3ea;
    color: #cc6a1d;
}

.status-lunas {
    background: #eaf7ef;
    color: #198754;
}

.total-tagihan {
    font-weight: 700;
    color: #262a38;
}

.jenis-badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 5px 9px;
    border-radius: 7px;
    font-size: 11px;
    font-weight: 600;
    white-space: nowrap;
}

.jenis-umum {
    background: #f1f2f9;
    color: #56576d;
}

.jenis-bpjs {
    background: #eaf3ff;
    color: #1a73c7;
}

.jenis-asuransi {
    background: #eae9ff;
    color: #7571f9;
}

.nama-pasien {
    font-weight: 600;
    color: #262a38 !important;
}


/* =====================================================
   TOMBOL BAYAR
===================================================== */

.btn-bayar {
    border-radius: 8px;
    padding: 7px 14px;
    font-size: 12.5px;
    font-weight: 600;
}


/* =====================================================
   EMPTY
===================================================== */

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


/* =====================================================
   MODAL BAYAR
===================================================== */

#modalBayar .modal-content {
    border: none;
    border-radius: 16px;
    overflow: hidden;
}

#modalBayar .modal-header {
    background: linear-gradient(135deg, #7571f9, #5f5ae0);
    color: #fff;
}

#modalBayar .modal-header .btn-close {
    filter: invert(1);
}

.ringkasan-bayar {
    background: #f8f8fa;
    border: 1px solid #e5e5e9;
    border-radius: 10px;
    padding: 15px;
    margin-bottom: 15px;
}

.ringkasan-bayar .baris {
    display: flex;
    justify-content: space-between;
    font-size: 13.5px;
    color: #494a57;
    padding: 4px 0;
}

.ringkasan-bayar .baris strong {
    color: #262a38;
}

.metode-terkunci-info {
    display: flex;
    align-items: flex-start;
    gap: 6px;
    background: #eaf3ff;
    border: 1px solid #cfe4ff;
    color: #1a4d80;
    border-radius: 10px;
    padding: 12px 14px;
    font-size: 13px;
    line-height: 1.5;
}

.metode-terkunci-info i {
    margin-top: 1px;
}


/* =====================================================
   RESPONSIVE
===================================================== */

@media (max-width: 768px) {

    .pembayaran-header {
        padding: 18px;
    }

    .pembayaran-title h1 {
        font-size: 20px;
    }

    #searchPembayaran {
        min-width: 100%;
    }

}

</style>


<div class="page-content-wrap">

<div class="pembayaran-container">


    <!-- ==================================================
         HEADER
    =================================================== -->

    <div class="pembayaran-header">

        <div class="pembayaran-title">

            <div class="pembayaran-title-icon">
                <i class="bi bi-cash-stack"></i>
            </div>

            <div>
                <h1>Pembayaran</h1>
                <p>
                    Tagihan pasien setelah diperiksa dokter/bidan (Poli Umum &amp; Kebidanan).
                </p>
            </div>

        </div>

    </div>


    <!-- ALERT -->

    <?php if ($msg !== '') { echo $alert; } ?>


    <!-- ==================================================
         TAB
    =================================================== -->

    <div class="pembayaran-tabs">

        <a href="pembayaran.php?tab=belum-bayar"
           class="pembayaran-tab <?= $tab === 'belum-bayar' ? 'active' : ''; ?>">

            <i class="bi bi-hourglass-split"></i>
            Belum Bayar
            <span class="count-badge"><?= $jumlahBelumBayar; ?></span>

        </a>

        <a href="pembayaran.php?tab=riwayat"
           class="pembayaran-tab <?= $tab === 'riwayat' ? 'active' : ''; ?>">

            <i class="bi bi-clock-history"></i>
            Riwayat Pembayaran

        </a>

    </div>


    <!-- ==================================================
         TABLE
    =================================================== -->

    <div class="table-card">

        <div class="table-card-header">

            <div>
                <h6 class="table-card-title">
                    <i class="bi bi-table me-1"></i>
                    <?= $tab === 'riwayat' ? 'Riwayat Pembayaran (Lunas)' : 'Tagihan Belum Dibayar'; ?>
                </h6>
                <p class="table-card-subtitle">
                    <?= $totalBaris; ?> data
                </p>
            </div>

            <input
                type="text"
                id="searchPembayaran"
                class="form-control"
                placeholder="Cari nama pasien...">

        </div>


        <div class="table-wrapper">

            <table id="tablePembayaran" class="table align-middle">

                <thead>

                    <tr>

                        <th>No</th>
                        <th>Tanggal</th>
                        <th>Poli</th>
                        <th>Nama Pasien</th>
                        <th>Jenis Bayar</th>
                        <th>Total Tagihan</th>

                        <?php if ($tab === 'riwayat') { ?>
                            <th>Dibayar</th>
                            <th>Metode</th>
                            <th>Diproses Oleh</th>
                        <?php } else { ?>
                            <th class="text-center">Aksi</th>
                        <?php } ?>

                    </tr>

                </thead>


                <tbody>

                    <?php

                    $no = 1;

                    if ($totalBaris > 0) :

                        while ($data = mysqli_fetch_assoc($queryPembayaran)) :

                    ?>

                    <tr class="data-pembayaran">

                        <td><?= $no++; ?></td>

                        <td>
                            <?= date('d-m-Y', strtotime($data['tanggal'])); ?>
                        </td>

                        <td>
                            <?php if ($data['poli'] === 'Kebidanan') { ?>
                                <span class="poli-badge poli-kebidanan">
                                    <i class="bi bi-heart-pulse"></i>
                                    Kebidanan
                                </span>
                            <?php } else { ?>
                                <span class="poli-badge poli-umum">
                                    <i class="bi bi-hospital"></i>
                                    Umum
                                </span>
                            <?php } ?>
                        </td>

                        <td class="nama-pasien">
                            <i class="bi bi-person-circle text-primary me-1"></i>
                            <?= htmlspecialchars($data['nama_pasien']); ?>
                        </td>

                        <td>
                            <?php if ($data['jenis_pembayaran'] === 'BPJS') { ?>
                                <span class="jenis-badge jenis-bpjs">
                                    <i class="bi bi-shield-check"></i>
                                    BPJS
                                </span>
                            <?php } elseif ($data['jenis_pembayaran'] === 'Asuransi') { ?>
                                <span class="jenis-badge jenis-asuransi">
                                    <i class="bi bi-shield-plus"></i>
                                    Asuransi
                                </span>
                            <?php } else { ?>
                                <span class="jenis-badge jenis-umum">
                                    <i class="bi bi-wallet2"></i>
                                    Umum
                                </span>
                            <?php } ?>
                        </td>

                        <td class="total-tagihan">
                            Rp <?= number_format((float) $data['total_tagihan'], 0, ',', '.'); ?>
                        </td>

                        <?php if ($tab === 'riwayat') { ?>

                            <td>
                                <?= !empty($data['tanggal_bayar'])
                                    ? date('d-m-Y H:i', strtotime($data['tanggal_bayar']))
                                    : '-'; ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($data['metode_bayar'] ?: '-'); ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($data['nama_petugas'] ?: '-'); ?>
                            </td>

                        <?php } else { ?>

                            <td class="text-center">

                                <button
                                    type="button"
                                    class="btn btn-primary btn-bayar btn-buka-bayar"
                                    data-id="<?= $data['id']; ?>"
                                    data-nama="<?= htmlspecialchars($data['nama_pasien']); ?>"
                                    data-poli="<?= htmlspecialchars($data['poli']); ?>"
                                    data-jenis="<?= htmlspecialchars($data['jenis_pembayaran']); ?>"
                                    data-tanggal="<?= date('d-m-Y', strtotime($data['tanggal'])); ?>"
                                    data-total="Rp <?= number_format((float) $data['total_tagihan'], 0, ',', '.'); ?>"
                                    data-toggle="modal"
                                    data-target="#modalBayar">

                                    <i class="bi bi-cash-coin me-1"></i>
                                    Bayar

                                </button>

                            </td>

                        <?php } ?>

                    </tr>

                    <?php

                        endwhile;

                    else:

                    ?>

                        <tr>

                            <td colspan="<?= $tab === 'riwayat' ? 9 : 7; ?>" class="empty-data">

                                <i class="bi bi-database-x"></i>

                                <?= $tab === 'riwayat'
                                    ? 'Belum ada riwayat pembayaran.'
                                    : 'Tidak ada tagihan yang belum dibayar.'; ?>

                            </td>

                        </tr>

                    <?php endif; ?>

                </tbody>

            </table>

        </div>

    </div>

</div>

</div>


<!-- ======================================================
     MODAL BAYAR
======================================================= -->

<div class="modal fade" id="modalBayar" tabindex="-1" aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered">

        <div class="modal-content">

            <form action="proses-pembayaran.php" method="post">

                <div class="modal-header">

                    <h5 class="modal-title">
                        <i class="bi bi-cash-coin me-2"></i>
                        Proses Pembayaran
                    </h5>

                    <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>

                </div>

                <div class="modal-body p-4">

                    <input type="hidden" name="id" id="bayarId">

                    <div class="ringkasan-bayar">

                        <div class="baris">
                            <span>Pasien</span>
                            <strong id="bayarNama">-</strong>
                        </div>

                        <div class="baris">
                            <span>Poli</span>
                            <strong id="bayarPoli">-</strong>
                        </div>

                        <div class="baris">
                            <span>Tanggal</span>
                            <strong id="bayarTanggal">-</strong>
                        </div>

                        <div class="baris">
                            <span>Total Tagihan</span>
                            <strong id="bayarTotal">-</strong>
                        </div>

                        <div class="baris">
                            <span>Jenis Bayar</span>
                            <strong id="bayarJenis">-</strong>
                        </div>

                    </div>

                    <!-- BPJS/ASURANSI: metode sudah ditentukan sejak
                         pendaftaran, tidak perlu (tidak boleh) dipilih
                         ulang di sini. -->
                    <div id="metodeTerkunci" class="d-none">

                        <div class="metode-terkunci-info">
                            <i class="bi bi-lock-fill me-2"></i>
                            <span id="metodeTerkunciTeks">
                                Pasien ini terdaftar pakai <strong>BPJS</strong> sejak
                                pendaftaran, metode pembayaran otomatis mengikuti itu.
                            </span>
                        </div>

                        <input type="hidden" name="metode_bayar" id="metodeTerkunciValue">

                    </div>

                    <!-- UMUM: petugas pilih Tunai atau Transfer. -->
                    <div id="metodeUmum" class="d-none">

                        <label class="form-label fw-semibold">Metode Pembayaran</label>

                        <select name="metode_bayar" id="metodeUmumSelect" class="form-select" required>
                            <option value="Tunai">Tunai</option>
                            <option value="Transfer">Transfer</option>
                        </select>

                    </div>

                </div>

                <div class="modal-footer bg-light">

                    <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">
                        Batal
                    </button>

                    <button type="submit" name="lunas" class="btn btn-primary">
                        <i class="bi bi-check-circle me-1"></i>
                        Tandai Lunas
                    </button>

                </div>

            </form>

        </div>

    </div>

</div>


<!-- ======================================================
     JAVASCRIPT
======================================================= -->

<script>

document.addEventListener("DOMContentLoaded", function () {


    /* PENCARIAN */

    var searchInput = document.getElementById("searchPembayaran");
    var rows = document.querySelectorAll(".data-pembayaran");

    if (searchInput) {

        searchInput.addEventListener("input", function () {

            var keyword = this.value.toLowerCase().trim();

            rows.forEach(function (row) {
                row.style.display = row.innerText.toLowerCase().includes(keyword) ? "" : "none";
            });

        });

    }


    /* MODAL BAYAR */

    document.querySelectorAll(".btn-buka-bayar").forEach(function (btn) {

        btn.addEventListener("click", function () {

            document.getElementById("bayarId").value = this.dataset.id;
            document.getElementById("bayarNama").textContent = this.dataset.nama;
            document.getElementById("bayarPoli").textContent = this.dataset.poli;
            document.getElementById("bayarTanggal").textContent = this.dataset.tanggal;
            document.getElementById("bayarTotal").textContent = this.dataset.total;
            document.getElementById("bayarJenis").textContent = this.dataset.jenis;

            var jenis = this.dataset.jenis;

            var boxTerkunci = document.getElementById("metodeTerkunci");
            var boxUmum = document.getElementById("metodeUmum");
            var inputTerkunci = document.getElementById("metodeTerkunciValue");
            var selectUmum = document.getElementById("metodeUmumSelect");

            if (jenis === "BPJS" || jenis === "Asuransi") {

                // Metode sudah ditentukan sejak pendaftaran -- kunci ke
                // jenis itu. select Umum di-nonaktifkan supaya TIDAK ikut
                // terkirim (kalau cuma disembunyikan pakai CSS, field
                // dengan name yang sama tetap ikut ter-submit dan bisa
                // menimpa nilai yang terkunci).
                boxTerkunci.classList.remove("d-none");
                boxUmum.classList.add("d-none");

                inputTerkunci.disabled = false;
                inputTerkunci.value = jenis;

                selectUmum.disabled = true;

                document.getElementById("metodeTerkunciTeks").innerHTML =
                    "Pasien ini terdaftar pakai <strong>" + jenis + "</strong> sejak " +
                    "pendaftaran, metode pembayaran otomatis mengikuti itu.";

            } else {

                // Umum -- petugas pilih sendiri Tunai/Transfer.
                boxTerkunci.classList.add("d-none");
                boxUmum.classList.remove("d-none");

                inputTerkunci.disabled = true;
                selectUmum.disabled = false;

            }

        });

    });

});


window.setTimeout(function () {
    $('.custom-alert').fadeOut();
}, 5000);

</script>


<?php

require "../template/footer.php";

?>
