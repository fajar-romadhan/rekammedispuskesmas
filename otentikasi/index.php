<?php

session_start();
require "../config.php";

// Jika SUDAH login, masuk ke dashboard
if (isset($_SESSION['ssLoginRM'])) {
    header("Location: ../index.php");
    exit();
}

?>
<!DOCTYPE html>
<html class="h-100" lang="id">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Masuk - Sistem Informasi Rekam Medis Puskesmas</title>

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

        .login-form__btn {
            background-color: #146c43;
            border-color: #146c43;
        }

        .login-form__btn:hover {
            background-color: #0f5132;
            border-color: #0f5132;
        }

        .login-form__footer {
            font-size: 13px;
        }

    </style>
</head>

<body class="h-100">

    <!-- Preloader -->
    <div id="preloader">
        <div class="loader">
            <svg class="circular" viewBox="25 25 50 50">
                <circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="3" stroke-miterlimit="10" />
            </svg>
        </div>
    </div>

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
                                    <div class="login-title">Sistem Informasi Rekam Medis</div>
                                    <div class="login-subtitle">Silakan masuk menggunakan akun resmi Anda</div>
                                </div>

                                <form class="mt-4 mb-4 login-input" action="proses-login.php" method="POST">

                                    <div class="form-group">
                                        <label for="username">Username</label>
                                        <input type="text"
                                               class="form-control"
                                               id="username"
                                               name="username"
                                               placeholder="Masukkan username"
                                               autocomplete="username"
                                               required>
                                    </div>

                                    <div class="form-group">
                                        <label for="password">Password</label>
                                        <input type="password"
                                               class="form-control"
                                               id="password"
                                               name="password"
                                               placeholder="Masukkan password"
                                               autocomplete="current-password"
                                               required>
                                    </div>

                                    <button class="btn login-form__btn submit w-100 text-white fw-semibold mt-2"
                                            type="submit"
                                            name="login">
                                        Masuk
                                    </button>

                                </form>

                                <p class="mb-0 login-form__footer text-center text-muted">
                                    &copy; <?= date('Y'); ?> Puskesmas Mendis
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
