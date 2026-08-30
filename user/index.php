<?php

session_start();

require "../template/rbac.php";

// Hanya Admin Sistem
cekAkses([ROLE_ADMIN]);

require "../config.php";

$title = "User - rekammedispuskesmas";

require "../template/header.php";
require "../template/navbar.php";
require "../template/sidebar.php";

// Hak akses hanya Administrator
if ($dataUser['jabatan'] != 1) {
    echo "<script>
        alert('Halaman tidak ditemukan..');
        window.location = '../index.php';
    </script>";
    exit();
}

?>

<style>

/* ================================
   HEADER HALAMAN
================================ */
.page-header-user {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 15px;

    padding: 18px 0;
    margin-bottom: 20px;

    border-bottom: 1px solid #e6e5eb;
}

.page-title-user {
    margin: 0;

    font-size: 27px;
    font-weight: 600;

    color: #1f1f37;

    display: flex;
    align-items: center;

    gap: 12px;
}

.page-title-user i {
    width: 42px;
    height: 42px;

    display: inline-flex;
    align-items: center;
    justify-content: center;

    border-radius: 12px;

    background: #efeeff;
    color: #7571f9;

    font-size: 21px;
}


/* ================================
   TOMBOL USER BARU
================================ */
.btn-user-baru {
    border: 1px solid #7571f9;

    color: #7571f9;

    background: #ffffff;

    padding: 8px 16px;

    border-radius: 9px;

    font-size: 14px;

    font-weight: 500;

    transition: all .2s ease;
}

.btn-user-baru:hover {
    background: #7571f9;

    color: white;

    transform: translateY(-1px);

    box-shadow: 0 4px 10px rgba(117, 113, 249, .20);
}

.btn-user-baru i {
    margin-right: 6px;
}


/* ================================
   SEARCH USER
================================ */
.user-search-wrapper {
    display: flex;

    justify-content: flex-end;

    margin-bottom: 15px;
}

.user-search-box {
    position: relative;

    width: 320px;
}

.user-search-box i {
    position: absolute;

    left: 13px;
    top: 50%;

    transform: translateY(-50%);

    color: #9d9caf;

    font-size: 16px;

    pointer-events: none;
}

.user-search-input {
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

.user-search-input:focus {
    border-color: #7571f9;

    box-shadow: 0 0 0 3px rgba(117, 113, 249, .10);
}

.user-search-input::placeholder {
    color: #9d9caf;
}


/* ================================
   CARD TABEL
================================ */
.user-table-card {
    background: #ffffff;

    border: 1px solid #e8e7f0;

    border-radius: 14px;

    overflow: hidden;

    box-shadow: 0 4px 18px rgba(0, 0, 0, .04);
}


/* ================================
   TABLE
================================ */
.user-table {
    margin-bottom: 0;

    vertical-align: middle;
}

.user-table thead th {
    background: #f7f7fc;

    color: #4b4b63;

    font-size: 13px;

    font-weight: 600;

    text-transform: uppercase;

    letter-spacing: .3px;

    padding: 15px 14px;

    border-bottom: 1px solid #e6e5eb;

    white-space: nowrap;
}

.user-table tbody td {
    padding: 14px;

    color: #383751;

    font-size: 14px;

    border-bottom: 1px solid #f0f0f5;

    vertical-align: middle;
}

.user-table tbody tr {
    transition: all .2s ease;
}

.user-table tbody tr:hover {
    background: #f8f8ff;
}

.user-table tbody tr:last-child td {
    border-bottom: none;
}


/* ================================
   NOMOR
================================ */
.nomor-user {
    width: 50px;

    text-align: center;

    color: #6d6b80;

    font-weight: 500;
}


/* ================================
   FOTO USER
================================ */
.user-photo {
    width: 45px;

    height: 45px;

    object-fit: cover;

    border-radius: 50% !important;

    padding: 2px;

    border: 2px solid #e8e5ff;

    background: #fff;

    transition: all .2s ease;
}

.user-photo:hover {
    transform: scale(1.08);

    border-color: #7571f9;
}


/* ================================
   USERNAME
================================ */
.username-user {
    font-weight: 600;

    color: #1f1f37;

    background: #f8f8fc;

    padding: 5px 9px;

    border-radius: 6px;

    display: inline-block;
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

    font-size: 15px;

    flex-shrink: 0;
}

.nama-user-info {
    display: flex;

    flex-direction: column;

    gap: 2px;
}

.nama-user {
    color: #1f1f37;

    font-weight: 600;

    font-size: 14px;
}

.nama-user-label {
    font-size: 11px;

    color: #9d9caf;
}


/* ================================
   BADGE JABATAN
================================ */
/* .badge-jabatan / .badge-admin/petugas/dokter/bidan/kepala sekarang
   didefinisikan secara global di template/header.php (dipakai juga di
   user/edit-user.php & user/hak-akses.php) supaya konsisten. */


/* ================================
   ALAMAT
================================ */
.alamat-user {
    max-width: 230px;

    color: #6d6b80;

    line-height: 1.5;
}


/* ================================
   TOMBOL AKSI
================================ */
.aksi-user {
    display: flex;

    align-items: center;

    gap: 6px;
}

.btn-aksi-user {
    width: 34px;

    height: 34px;

    display: inline-flex;

    align-items: center;

    justify-content: center;

    border-radius: 8px;

    transition: all .2s ease;
}


/* EDIT */
.btn-edit-user {
    border: 1px solid #ffc107;

    color: #d39e00;

    background: #fffdf5;
}

.btn-edit-user:hover {
    background: #ffc107;

    color: #fff;

    transform: translateY(-2px);

    box-shadow: 0 4px 8px rgba(255, 193, 7, .20);
}


/* HAPUS */
.btn-hapus-user {
    border: 1px solid #dc3545;

    color: #dc3545;

    background: #fff7f7;
}

.btn-hapus-user:hover {
    background: #dc3545;

    color: #fff;

    transform: translateY(-2px);

    box-shadow: 0 4px 8px rgba(220, 53, 69, .20);
}


/* ================================
   HASIL SEARCH KOSONG
================================ */
.no-search-result {
    text-align: center;

    padding: 35px !important;

    color: #9d9caf !important;

    font-size: 14px;
}

.no-search-result i {
    color: #cbcbe1;
}


/* ================================
   RESPONSIVE
================================ */
@media (max-width: 768px) {

    .page-title-user {
        font-size: 23px;
    }

    .page-header-user {
        align-items: flex-start;
    }

    .user-search-wrapper {
        justify-content: stretch;
    }

    .user-search-box {
        width: 100%;
    }

    .user-table-card {
        border-radius: 10px;
    }

    .user-table thead th,
    .user-table tbody td {
        font-size: 12px;

        padding: 10px;
    }

    .user-photo {
        width: 38px;

        height: 38px;
    }

    .alamat-user {
        max-width: 160px;
    }

    .nama-user-icon {
        width: 32px;

        height: 32px;
    }

}

</style>


<div class="page-content-wrap">


    <!-- =========================
         HEADER
    ========================== -->
    <div class="page-header-user">

        <h1 class="page-title-user">

            <i class="bi bi-people"></i>

            Data User

        </h1>


        <?php if (userHasRole(1)) { ?>

            <a
                href="<?= $main_url ?>user/tambah-user.php"
                class="btn-user-baru text-decoration-none"
            >

                <i class="bi bi-plus-lg"></i>

                User Baru

            </a>

        <?php } ?>

    </div>


    <!-- =========================
         SEARCH
    ========================== -->
    <div class="user-search-wrapper">

        <div class="user-search-box">

            <i class="bi bi-search"></i>

            <input
                type="text"
                id="searchUser"
                class="user-search-input"
                placeholder="Cari nama, username, jabatan..."
                autocomplete="off"
            >

        </div>

    </div>


    <!-- =========================
         TABLE
    ========================== -->
    <div class="user-table-card">

        <div class="table-responsive">

            <table class="table user-table">

                <thead>

                    <tr>

                        <th class="text-center">
                            No
                        </th>

                        <th>
                            Gambar
                        </th>

                        <th>
                            Username
                        </th>

                        <th>
                            Nama Lengkap
                        </th>

                        <th>
                            Jabatan
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

                    $queryUser = mysqli_query(
                        $koneksi,
                        "SELECT * FROM tbl_user ORDER BY userid DESC"
                    );

                    if (mysqli_num_rows($queryUser) > 0) {

                        while ($user = mysqli_fetch_assoc($queryUser)) {

                            $jabatan = $user['jabatan'];

                    ?>

                    <tr>


                        <!-- NOMOR -->
                        <td class="nomor-user">

                            <?= $no++; ?>

                        </td>


                        <!-- FOTO -->
                        <td>

                            <img
                                src="../asset/gambar/<?= htmlspecialchars($user['gambar']) ?>"
                                alt="User"
                                class="user-photo"
                                onerror="this.src='../asset/gambar/user.png'"
                            >

                        </td>


                        <!-- USERNAME -->
                        <td>

                            <span class="username-user">

                                <i class="bi bi-person-badge me-1"></i>

                                <?= htmlspecialchars($user['username']) ?>

                            </span>

                        </td>


                        <!-- NAMA LENGKAP -->
                        <td>

                            <div class="nama-user-wrapper">

                                <div class="nama-user-icon">

                                    <i class="bi bi-person"></i>

                                </div>


                                <div class="nama-user-info">

                                    <span class="nama-user">

                                        <?= htmlspecialchars($user['fullname']) ?>

                                    </span>

                                    <span class="nama-user-label">

                                        Pengguna Sistem

                                    </span>

                                </div>

                            </div>

                        </td>


                        <!-- JABATAN -->
                        <td>

                            <?php

                            switch ($jabatan) {

                                case 1:

                                    echo '
                                    <span class="badge-jabatan badge-admin">

                                        <i class="bi bi-shield-check me-1"></i>

                                        Administrator

                                    </span>';

                                    break;


                                case 2:

                                    echo '
                                    <span class="badge-jabatan badge-petugas">

                                        <i class="bi bi-person-workspace me-1"></i>

                                        Petugas

                                    </span>';

                                    break;


                                case 3:

                                    echo '
                                    <span class="badge-jabatan badge-dokter">

                                        <i class="bi bi-heart-pulse me-1"></i>

                                        Dokter

                                    </span>';

                                    break;


                                case 4:

                                    echo '
                                    <span class="badge-jabatan badge-bidan">

                                        <i class="bi bi-person-hearts me-1"></i>

                                        Bidan

                                    </span>';

                                    break;


                                case 5:

                                    echo '
                                    <span class="badge-jabatan badge-kepala">

                                        <i class="bi bi-building-check me-1"></i>

                                        Kepala Puskesmas

                                    </span>';

                                    break;


                                default:

                                    echo '
                                    <span class="badge-jabatan">

                                        -

                                    </span>';

                            }

                            ?>

                        </td>


                        <!-- ALAMAT -->
                        <td>

                            <div class="alamat-user">

                                <?= htmlspecialchars($user['alamat']) ?>

                            </div>

                        </td>


                        <!-- AKSI -->
                        <td>

                            <div class="aksi-user justify-content-center">

                                <?php if (userHasRole(1)) { ?>


                                    <!-- EDIT -->
                                    <a
                                        href="edit-user.php?id=<?= $user['userid'] ?>&gambar=<?= urlencode($user['gambar']) ?>"
                                        class="btn-aksi-user btn-edit-user"
                                        title="Edit User"
                                    >

                                        <i class="bi bi-pencil"></i>

                                    </a>


                                    <!-- HAK AKSES -->
                                    <a
                                        href="edit-hak-akses.php?id=<?= $user['userid'] ?>"
                                        class="btn-aksi-user btn-edit-user"
                                        title="Atur Hak Akses"
                                    >

                                        <i class="bi bi-shield-lock"></i>

                                    </a>


                                    <!-- HAPUS -->
                                    <a
                                        href="proses-user.php?id=<?= $user['userid'] ?>&gambar=<?= urlencode($user['gambar']) ?>&aksi=hapus-user"
                                        onclick="return confirm('Anda yakin ingin menghapus user ini?')"
                                        class="btn-aksi-user btn-hapus-user"
                                        title="Hapus User"
                                    >

                                        <i class="bi bi-trash"></i>

                                    </a>


                                <?php } ?>

                            </div>

                        </td>

                    </tr>

                    <?php

                        }

                    } else {

                    ?>

                    <tr>

                        <td colspan="7" class="no-search-result">

                            <i class="bi bi-people fs-4 d-block mb-2"></i>

                            Belum ada data user.

                        </td>

                    </tr>

                    <?php } ?>

                </tbody>

            </table>

        </div>

    </div>

</div>


<script>

document.addEventListener("DOMContentLoaded", function () {

    const searchInput = document.getElementById("searchUser");

    const table = document.querySelector(".user-table");

    const tbody = table.querySelector("tbody");


    searchInput.addEventListener("keyup", function () {

        const keyword = this.value.toLowerCase().trim();

        const rows = tbody.querySelectorAll("tr");

        let jumlahHasil = 0;


        rows.forEach(function (row) {

            // Abaikan baris pesan pencarian
            if (row.classList.contains("no-search-row")) {
                return;
            }


            const text = row.innerText.toLowerCase();


            if (text.includes(keyword)) {

                row.style.display = "";

                jumlahHasil++;

            } else {

                row.style.display = "none";

            }

        });


        // Hapus pesan sebelumnya
        const oldMessage = tbody.querySelector(".no-search-row");

        if (oldMessage) {

            oldMessage.remove();

        }


        // Jika tidak ada hasil pencarian
        if (jumlahHasil === 0 && keyword !== "") {

            const tr = document.createElement("tr");

            tr.classList.add("no-search-row");


            tr.innerHTML = `
                <td colspan="7" class="no-search-result">

                    <i class="bi bi-search fs-4 d-block mb-2"></i>

                    Data user "<strong>${escapeHtml(keyword)}</strong>" tidak ditemukan.

                </td>
            `;


            tbody.appendChild(tr);

        }

    });


    // Mencegah HTML masuk dari input search
    function escapeHtml(text) {

        const div = document.createElement("div");

        div.textContent = text;

        return div.innerHTML;

    }

});

</script>


<?php

require "../template/footer.php";

?>