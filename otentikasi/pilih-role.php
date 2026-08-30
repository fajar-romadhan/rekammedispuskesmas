<?php

session_start();
require "../config.php";
require "../template/rbac.php";


/*
|--------------------------------------------------------------------------
| GUARD
|--------------------------------------------------------------------------
*/

// Belum login sama sekali -> ke halaman login.
if (!isset($_SESSION['ssLoginRM'])) {
    header("Location: index.php");
    exit();
}

// Role sudah dipilih sebelumnya (kunjungan biasa, bukan lewat login) ->
// tetap perbolehkan buka halaman ini kalau memang user punya lebih dari
// satu role, supaya bisa dipakai sebagai "Ganti Role" dari navbar.
$rolesTersedia = $_SESSION['ssRolesTersedia'] ?? $_SESSION['role'] ?? [];

if (count($rolesTersedia) <= 1) {
    // Cuma py satu role -> tidak ada yang perlu dipilih.
    header("Location: ../index.php");
    exit();
}

// Sedang fokus ke satu role (lewat "Ganti Role") atau masih tampilan
// gabungan semua role (default)?
$sedangFokusSatuRole = count((array) ($_SESSION['role'] ?? [])) === 1;

?>
<!DOCTYPE html>
<html class="h-100" lang="id">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Pilih Role - Sistem Informasi Rekam Medis Puskesmas</title>

    <link rel="icon" type="image/x-icon" href="<?= $main_url ?>asset/gambar/puskesmas.png">
    <link href="<?= $main_url ?>asset/quixlab/css/style.css" rel="stylesheet">

    <style>

        .login-form-bg {
            background:
                linear-gradient(rgba(13, 41, 33, .78), rgba(13, 41, 33, .78)),
                url('<?= $main_url ?>asset/gambar/bg-puskesmas.jpg') center center / cover no-repeat;
        }

        .login-brand-icon {
            width: 76px;
            height: 76px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #146c43;
        }

        .login-instansi {
            font-size: 13px;
            font-weight: 700;
            letter-spacing: .08em;
            color: #146c43;
            text-transform: uppercase;
        }

        .login-title {
            font-size: 20px;
            font-weight: 700;
            color: #1f1f37;
        }

        .login-subtitle {
            font-size: 13px;
            color: #6d6b80;
        }

        .role-choice {
            display: flex;
            align-items: center;
            gap: 14px;
            width: 100%;
            padding: 14px 16px;
            margin-bottom: 12px;
            border: 1px solid #dedfe6;
            border-radius: 12px;
            background: #fff;
            color: #1f1f37;
            text-decoration: none;
            transition: all .2s ease;
        }

        .role-choice:last-child {
            margin-bottom: 0;
        }

        .role-choice:hover {
            border-color: #146c43;
            background: #f3faf6;
            color: #146c43;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(20, 108, 67, .12);
        }

        .role-choice-icon {
            width: 42px;
            height: 42px;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
            background: #eef8f2;
            color: #146c43;
            font-size: 20px;
        }

        .role-choice-text {
            font-weight: 600;
            font-size: 15px;
        }

        .role-choice-arrow {
            margin-left: auto;
            color: #adaebd;
        }

        .login-form__footer {
            font-size: 13px;
        }

    </style>
</head>

<body class="h-100">

    <div class="login-form-bg h-100">
        <div class="container h-100">
            <div class="row justify-content-center align-items-center h-100">
                <div class="col-xl-5 col-lg-6 col-md-8">
                    <div class="form-input-content">
                        <div class="card login-form mb-0">
                            <div class="card-body pt-5">

                                <div class="text-center mb-4">
                                    <img class="login-brand-icon mb-3"
                                         src="<?= $main_url ?>asset/gambar/icon.png"
                                         alt="Logo Puskesmas">
                                    <div class="login-instansi">Puskesmas Mendis</div>
                                    <div class="login-title">Ganti Role</div>
                                    <div class="login-subtitle">
                                        Akun ini punya lebih dari satu hak akses.
                                        Sidebar menampilkan semua menu sekaligus secara
                                        default &mdash; pilih di bawah kalau mau fokus
                                        ke satu role saja.
                                    </div>
                                </div>

                                <div class="mt-4 mb-3">

                                    <?php if ($sedangFokusSatuRole) { ?>

                                        <a href="proses-pilih-role.php?role=semua"
                                           class="role-choice">

                                            <span class="role-choice-icon">
                                                <i class="bi bi-grid-1x2"></i>
                                            </span>

                                            <span class="role-choice-text">
                                                Tampilkan Semua Role
                                            </span>

                                            <span class="role-choice-arrow">
                                                <i class="bi bi-chevron-right"></i>
                                            </span>

                                        </a>

                                    <?php } ?>

                                    <?php

                                    $ikonRole = [
                                        ROLE_ADMIN   => 'bi-person-gear',
                                        ROLE_PETUGAS => 'bi-person-badge',
                                        ROLE_DOKTER  => 'bi-clipboard2-pulse',
                                        ROLE_BIDAN   => 'bi-heart-pulse',
                                        ROLE_KEPALA  => 'bi-building',
                                    ];

                                    foreach ($rolesTersedia as $roleId) {

                                        $roleId = (int) $roleId;

                                    ?>

                                        <a href="proses-pilih-role.php?role=<?= $roleId; ?>"
                                           class="role-choice">

                                            <span class="role-choice-icon">
                                                <i class="bi <?= $ikonRole[$roleId] ?? 'bi-person'; ?>"></i>
                                            </span>

                                            <span class="role-choice-text">
                                                <?= htmlspecialchars(namaJabatan($roleId)); ?>
                                            </span>

                                            <span class="role-choice-arrow">
                                                <i class="bi bi-chevron-right"></i>
                                            </span>

                                        </a>

                                    <?php } ?>

                                </div>

                                <p class="mb-0 login-form__footer text-center text-muted">
                                    Bukan Anda?
                                    <a href="logout.php">Keluar</a>
                                </p>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!--**********************************
        Scripts
    ***********************************-->
    <script src="<?= $main_url ?>asset/quixlab/plugins/common/common.min.js"></script>
    <script src="<?= $main_url ?>asset/quixlab/js/custom.min.js"></script>
    <script src="<?= $main_url ?>asset/quixlab/js/settings.js"></script>
    <script src="<?= $main_url ?>asset/quixlab/js/gleek.js"></script>
    <script src="<?= $main_url ?>asset/quixlab/js/styleSwitcher.js"></script>
</body>
</html>
