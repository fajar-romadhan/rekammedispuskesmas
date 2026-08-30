<?php
    // rbac.php aman di-require berkali-kali, dipakai untuk namaJabatan().
    require_once __DIR__ . '/rbac.php';

    $navUserLogin = $_SESSION['ssUserRM'];
    $navCekUser   = mysqli_query($koneksi,
        "SELECT * FROM tbl_user WHERE username = '$navUserLogin'");
    $navDataUser  = mysqli_fetch_assoc($navCekUser);

    $navNama = $navDataUser['fullname'] ?? $navUserLogin;

    // Label jabatan ikut role yang SEDANG aktif di session (bukan kolom
    // tbl_user.jabatan mentah), supaya benar untuk akun multi-role.
    $navRoleAktif = (int) (($_SESSION['role'][0] ?? null) ?? ($navDataUser['jabatan'] ?? null));
    $navJabatan   = namaJabatan($navRoleAktif);

    $navFoto    = !empty($navDataUser['gambar'])
        ? $main_url . 'asset/gambar/' . $navDataUser['gambar']
        : $main_url . 'asset/quixlab/images/user/1.png';
?>
<!--**********************************
    Main wrapper start
***********************************-->
<div id="main-wrapper">

    <!--**********************************
        Nav header (logo) start
    ***********************************-->
    <div class="nav-header">
        <div class="brand-logo">
            <a href="<?= $main_url ?>">
                <b class="logo-abbr">
                    <img src="<?= $main_url ?>asset/gambar/icon.png" alt="Logo Puskesmas Mendis">
                </b>
                <span class="brand-title">PUSKESMAS MENDIS</span>
            </a>
        </div>
    </div>
    <!--**********************************
        Nav header end
    ***********************************-->

    <!--**********************************
        Header start
    ***********************************-->
    <div class="header">
        <div class="header-content clearfix">

            <div class="nav-control">
                <div class="hamburger">
                    <span class="toggle-icon"><i class="icon-menu"></i></span>
                </div>
            </div>

            <div class="header-left"></div>

            <div class="header-right">
                <ul class="clearfix">

                    <li class="icons dropdown">
                        <div class="user-img c-pointer position-relative d-flex align-items-center gap-2"
                             data-toggle="dropdown">
                            <span class="activity active"></span>
                            <img src="<?= htmlspecialchars($navFoto); ?>"
                                 height="40" width="40"
                                 style="object-fit:cover; border-radius:50%;"
                                 onerror="this.src='<?= $main_url ?>asset/quixlab/images/user/1.png'"
                                 alt="Foto profil">
                            <span class="d-none d-md-flex flex-column lh-sm">
                                <span class="user-info-name"><?= htmlspecialchars($navNama); ?></span>
                                <span class="user-info-role"><?= htmlspecialchars($navJabatan); ?></span>
                            </span>
                        </div>
                        <div class="drop-down dropdown-profile animated fadeIn dropdown-menu dropdown-menu-right">
                            <div class="dropdown-content-body">
                                <ul>
                                    <li>
                                        <a href="<?= $main_url ?>otentikasi/password.php">
                                            <i class="icon-lock"></i> <span>Ganti Password</span>
                                        </a>
                                    </li>
                                    <hr class="my-2">
                                    <li>
                                        <a href="<?= $main_url ?>otentikasi/logout.php">
                                            <i class="icon-key"></i> <span>Keluar</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </li>

                </ul>
            </div>

        </div>
    </div>
    <!--**********************************
        Header end
    ***********************************-->
