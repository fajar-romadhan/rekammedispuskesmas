<?php

session_start();
require "../template/rbac.php";


/*
|--------------------------------------------------------------------------
| GUARD
|--------------------------------------------------------------------------
*/

if (!isset($_SESSION['ssLoginRM'])) {
    header("Location: index.php");
    exit();
}

$rolesTersedia = $_SESSION['ssRolesTersedia'] ?? $_SESSION['role'] ?? [];
$rolesTersedia = array_map('intval', $rolesTersedia);

// role=semua -> kembali ke tampilan gabungan semua role (default sejak login).
if (($_GET['role'] ?? '') === 'semua') {

    $_SESSION['role'] = $rolesTersedia;
    $_SESSION['ssRolesTersedia'] = $rolesTersedia;

    header("Location: ../index.php");
    exit();
}

$roleDipilih = isset($_GET['role']) ? (int) $_GET['role'] : 0;


/*
|--------------------------------------------------------------------------
| VALIDASI: hanya boleh pilih role yang memang dimiliki user ini
|--------------------------------------------------------------------------
*/

if (!in_array($roleDipilih, $rolesTersedia, true)) {

    header("Location: pilih-role.php");
    exit();
}


/*
|--------------------------------------------------------------------------
| AKTIFKAN ROLE TERPILIH (fokus ke satu role saja)
|--------------------------------------------------------------------------
*/

$_SESSION['role'] = [$roleDipilih];

// Tetap simpan daftar semua role yang dimiliki supaya bisa dipakai lagi
// untuk fitur "Ganti Role" tanpa perlu logout-login ulang.
$_SESSION['ssRolesTersedia'] = $rolesTersedia;

header("Location: ../index.php");
exit();
