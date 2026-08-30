<?php

session_start();

require "../template/rbac.php";

// Hanya Admin Sistem
cekAkses([ROLE_ADMIN]);

require "../config.php";

$title = "Hak Akses Sistem - rekammedispuskesmas";

require "../template/header.php";
require "../template/navbar.php";
require "../template/sidebar.php";

?>

<style>

/* ================================
   HALAMAN HAK AKSES
================================ */
.hak-akses-page {
    padding-top: 25px;
    padding-bottom: 40px;
}


/* ================================
   HEADER
================================ */
.page-header {
    background: #ffffff;

    border-radius: 16px;

    padding: 20px 24px;

    margin-bottom: 20px;

    border: 1px solid #e9e9ef;

    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
}

.page-header h1 {
    margin: 0;

    font-size: 25px;

    font-weight: 600;

    color: #343540;
}

.page-header h1 i {
    color: #7571f9;

    margin-right: 8px;
}

.page-header p {
    margin: 5px 0 0 34px;

    color: #6c757d;

    font-size: 14px;
}


/* ================================
   SEARCH
================================ */
.search-wrapper {
    display: flex;

    justify-content: flex-end;

    margin-bottom: 15px;
}

.search-box {
    position: relative;

    width: 320px;
}

.search-box i {
    position: absolute;

    left: 13px;

    top: 50%;

    transform: translateY(-50%);

    color: #9d9caf;

    font-size: 16px;

    pointer-events: none;
}

.search-input {
    width: 100%;

    height: 42px;

    padding: 8px 14px 8px 40px;

    border: 1px solid #dfdfe8;

    border-radius: 9px;

    outline: none;

    font-size: 14px;

    color: #383751;

    background: #ffffff;

    transition: all .2s ease;
}

.search-input:focus {
    border-color: #7571f9;

    box-shadow: 0 0 0 3px rgba(117, 113, 249, .10);
}

.search-input::placeholder {
    color: #9d9caf;
}


/* ================================
   CARD
================================ */
.hak-akses-card {
    border: none;

    border-radius: 16px;

    overflow: hidden;

    background: #ffffff;

    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.07);
}


/* ================================
   CARD HEADER
================================ */
.hak-akses-card .card-header {
    background: #ffffff;

    padding: 18px 22px;

    border-bottom: 1px solid #eeeeee;

    font-size: 16px;

    font-weight: 600;

    color: #343540;
}

.hak-akses-card .card-header i {
    color: #7571f9;
}


/* ================================
   CARD BODY
================================ */
.hak-akses-card .card-body {
    padding: 0;
}


/* ================================
   TABLE
================================ */
.hak-akses-table {
    margin: 0;

    width: 100%;
}

.hak-akses-table thead {
    background: #f8f8fa;
}

.hak-akses-table thead th {
    padding: 15px 18px;

    font-size: 13px;

    font-weight: 600;

    color: #494a57;

    border-bottom: 1px solid #dedfe6;

    white-space: nowrap;
}

.hak-akses-table tbody td {
    padding: 15px 18px;

    font-size: 14px;

    color: #494a57;

    border-bottom: 1px solid #f0f0f0;

    vertical-align: middle;
}


/* ================================
   HOVER BARIS
================================ */
.hak-akses-table tbody tr {
    transition: all 0.2s ease;
}

.hak-akses-table tbody tr:hover {
    background: #f8f8ff;
}


/* ================================
   NOMOR
================================ */
.nomor {
    width: 55px;

    text-align: center;

    font-weight: 600;

    color: #6c757d;
}


/* ================================
   NAMA USER
================================ */
.nama-user-wrapper {
    display: flex;

    align-items: center;

    gap: 10px;
}

.nama-user-icon {
    width: 36px;

    height: 36px;

    display: inline-flex;

    align-items: center;

    justify-content: center;

    border-radius: 9px;

    background: #efeeff;

    color: #7571f9;

    font-size: 17px;

    flex-shrink: 0;
}

.nama-user-info {
    display: flex;

    flex-direction: column;

    gap: 2px;
}

.nama-user {
    font-weight: 600;

    color: #343540;

    font-size: 14px;
}

.nama-user-label {
    font-size: 11px;

    color: #9d9caf;
}


/* ================================
   USERNAME
================================ */
.username {
    color: #6c757d;
}

.username i {
    color: #7571f9;
}


/* .badge-jabatan / .badge-admin/petugas/dokter/bidan/kepala sekarang
   didefinisikan secara global di template/header.php supaya warnanya
   konsisten dengan user/index.php & user/edit-user.php. */


/* ================================
   TOMBOL HAK AKSES
================================ */
.btn-hak-akses {
    border-radius: 8px;

    padding: 7px 13px;

    font-size: 13px;

    font-weight: 500;

    transition: all 0.2s ease;
}

.btn-hak-akses:hover {
    transform: translateY(-1px);

    box-shadow: 0 4px 10px rgba(117, 113, 249, 0.18);
}


/* ================================
   EMPTY / TIDAK DITEMUKAN
================================ */
.empty-data {
    padding: 45px 20px !important;

    text-align: center;

    color: #6c757d;
}

.empty-data i {
    display: block;

    font-size: 40px;

    margin-bottom: 10px;

    color: #adaebd;
}


/* ================================
   SEARCH TIDAK DITEMUKAN
================================ */
.no-search-result {
    padding: 40px 20px !important;

    text-align: center;

    color: #9d9caf !important;

    font-size: 14px;
}

.no-search-result i {
    display: block;

    font-size: 35px;

    margin-bottom: 10px;

    color: #cbcbe1;
}


/* ================================
   RESPONSIVE
================================ */
@media (max-width: 768px) {

    .hak-akses-page {
        padding-top: 15px;
    }

    .page-header {
        padding: 17px;
    }

    .page-header h1 {
        font-size: 21px;
    }

    .page-header p {
        margin-left: 0;

        margin-top: 8px;
    }

    .search-wrapper {
        justify-content: stretch;
    }

    .search-box {
        width: 100%;
    }

    .hak-akses-card .card-header {
        padding: 15px;
    }

    .hak-akses-table thead th,
    .hak-akses-table tbody td {
        padding: 12px;

        font-size: 13px;
    }

    .btn-hak-akses {
        padding: 6px 10px;
    }

    .nama-user-icon {
        width: 32px;

        height: 32px;
    }

}

</style>


<div class="page-content-wrap">

    <div class="hak-akses-page">


        <!-- ================================
             HEADER HALAMAN
        ================================= -->

        <div class="page-header">

            <h1>

                <i class="bi bi-shield-lock"></i>

                Hak Akses Sistem

            </h1>

            <p>

                Mengatur hak akses pengguna berdasarkan jabatan dalam sistem.

            </p>

        </div>


        <!-- ================================
             SEARCH
        ================================= -->

        <div class="search-wrapper">

            <div class="search-box">

                <i class="bi bi-search"></i>

                <input
                    type="text"
                    id="searchHakAkses"
                    class="search-input"
                    placeholder="Cari nama, username, jabatan..."
                    autocomplete="off"
                >

            </div>

        </div>


        <!-- ================================
             CARD DATA USER
        ================================= -->

        <div class="card hak-akses-card">


            <!-- CARD HEADER -->

            <div class="card-header">

                <i class="bi bi-person-gear me-2"></i>

                Pengaturan Hak Akses Pengguna

            </div>


            <!-- CARD BODY -->

            <div class="card-body">

                <div class="table-responsive">

                    <table class="table hak-akses-table align-middle">

                        <thead>

                            <tr>

                                <th class="nomor">
                                    No
                                </th>

                                <th>
                                    Nama Pengguna
                                </th>

                                <th>
                                    Username
                                </th>

                                <th>
                                    Jabatan
                                </th>

                                <th>
                                    Hak Akses
                                </th>

                            </tr>

                        </thead>


                        <tbody>


                        <?php

                        $no = 1;

                        $queryUser = mysqli_query(
                            $koneksi,
                            "
                            SELECT *
                            FROM tbl_user
                            ORDER BY fullname ASC
                            "
                        );


                        if (mysqli_num_rows($queryUser) > 0) {


                            $namaRole = [
                                1 => 'Admin',
                                2 => 'Petugas',
                                3 => 'Dokter',
                                4 => 'Bidan',
                                5 => 'Kepala Puskesmas',
                            ];

                            while ($user = mysqli_fetch_assoc($queryUser)) {


                                // Semua role yang dimiliki user ini (multi-role).
                                $roleUser = [];

                                $queryRoleUser = mysqli_query($koneksi, "
                                    SELECT role_id
                                    FROM tbl_user_role
                                    WHERE userid = '{$user['userid']}'
                                    ORDER BY role_id ASC
                                ");

                                while ($rr = mysqli_fetch_assoc($queryRoleUser)) {
                                    $roleUser[] = (int) $rr['role_id'];
                                }

                                if (empty($roleUser)) {
                                    $roleUser[] = (int) $user['jabatan'];
                                }

                        ?>


                            <tr>


                                <!-- NO -->

                                <td class="nomor">

                                    <?= $no++; ?>

                                </td>


                                <!-- NAMA -->

                                <td>

                                    <div class="nama-user-wrapper">

                                        <div class="nama-user-icon">

                                            <i class="bi bi-person"></i>

                                        </div>


                                        <div class="nama-user-info">

                                            <span class="nama-user">

                                                <?= htmlspecialchars($user['fullname']); ?>

                                            </span>

                                            <span class="nama-user-label">

                                                Pengguna Sistem

                                            </span>

                                        </div>

                                    </div>

                                </td>


                                <!-- USERNAME -->

                                <td>

                                    <span class="username">

                                        <i class="bi bi-person me-1"></i>

                                        <?= htmlspecialchars($user['username']); ?>

                                    </span>

                                </td>


                                <!-- JABATAN -->

                                <td>

                                    <?php

                                    // Warna badge disamakan dengan user/index.php (badge-admin/
                                    // petugas/dokter/bidan/kepala), supaya tampilan jabatan yang
                                    // sama terlihat konsisten di semua halaman.
                                    $kelasRoleHakAkses = [
                                        1 => 'badge-admin',
                                        2 => 'badge-petugas',
                                        3 => 'badge-dokter',
                                        4 => 'badge-bidan',
                                        5 => 'badge-kepala',
                                    ];

                                    foreach ($roleUser as $roleId) {
                                        $kelas = $kelasRoleHakAkses[$roleId] ?? '';
                                    ?>

                                        <span class="badge-jabatan <?= $kelas; ?> me-1">

                                            <i class="bi bi-briefcase me-1"></i>

                                            <?= htmlspecialchars($namaRole[$roleId] ?? 'Tidak diketahui'); ?>

                                        </span>

                                    <?php } ?>

                                </td>


                                <!-- HAK AKSES -->

                                <td>

                                    <a
                                        href="edit-hak-akses.php?id=<?= $user['userid']; ?>"
                                        class="btn btn-sm btn-outline-primary btn-hak-akses"
                                    >

                                        <i class="bi bi-pencil-square me-1"></i>

                                        Atur Hak Akses

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

                                    <i class="bi bi-people"></i>

                                    Belum ada data pengguna.

                                </td>

                            </tr>


                        <?php } ?>


                        </tbody>

                    </table>

                </div>

            </div>

        </div>

    </div>

</div>


<!-- ================================
     JAVASCRIPT SEARCH
================================ -->

<script>

document.addEventListener("DOMContentLoaded", function () {

    const searchInput =
        document.getElementById("searchHakAkses");

    const table =
        document.querySelector(".hak-akses-table");

    const tbody =
        table.querySelector("tbody");


    searchInput.addEventListener("keyup", function () {

        const keyword =
            this.value.toLowerCase().trim();


        const rows =
            tbody.querySelectorAll("tr");


        let jumlahHasil = 0;


        rows.forEach(function (row) {


            // Abaikan baris hasil pencarian

            if (
                row.classList.contains("no-search-row")
            ) {

                return;

            }


            const text =
                row.innerText.toLowerCase();


            if (text.includes(keyword)) {

                row.style.display = "";

                jumlahHasil++;

            } else {

                row.style.display = "none";

            }

        });


        // Hapus pesan pencarian lama

        const oldMessage =
            tbody.querySelector(".no-search-row");


        if (oldMessage) {

            oldMessage.remove();

        }


        // Jika data tidak ditemukan

        if (
            jumlahHasil === 0 &&
            keyword !== ""
        ) {


            const tr =
                document.createElement("tr");


            tr.classList.add(
                "no-search-row"
            );


            tr.innerHTML = `

                <td
                    colspan="5"
                    class="no-search-result"
                >

                    <i class="bi bi-search"></i>

                    Data pengguna
                    "<strong>${escapeHtml(keyword)}</strong>"
                    tidak ditemukan.

                </td>

            `;


            tbody.appendChild(tr);

        }

    });


    // ================================
    // MENCEGAH HTML DARI INPUT SEARCH
    // ================================

    function escapeHtml(text) {

        const div =
            document.createElement("div");

        div.textContent = text;

        return div.innerHTML;

    }

});

</script>


<?php

require "../template/footer.php";

?>