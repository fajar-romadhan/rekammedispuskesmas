<?php

/*
|--------------------------------------------------------------------------
| RBAC (Role Based Access Control)
|--------------------------------------------------------------------------
| Definisi role yang dipakai di seluruh sistem, supaya setiap halaman
| tidak menuliskan angka "ajaib" (1, 2, 3, ...) sendiri-sendiri.
|
| Cara pakai di setiap halaman (setelah session_start()):
|
|   require "../template/rbac.php";
|   cekAkses([ROLE_ADMIN, ROLE_PETUGAS]);
|
| cekAkses() akan otomatis redirect ke halaman login jika belum login,
| dan redirect ke dashboard jika role user tidak diizinkan.
|--------------------------------------------------------------------------
*/

define('ROLE_ADMIN', 1);
define('ROLE_PETUGAS', 2);
define('ROLE_DOKTER', 3);
define('ROLE_BIDAN', 4);
define('ROLE_KEPALA', 5);

/**
 * Nama jabatan untuk ditampilkan (dipakai juga oleh navbar).
 */
function namaJabatan($jabatan)
{
    switch ((int) $jabatan) {
        case ROLE_ADMIN:
            return 'Administrator';
        case ROLE_PETUGAS:
            return 'Petugas';
        case ROLE_DOKTER:
            return 'Dokter';
        case ROLE_BIDAN:
            return 'Bidan';
        case ROLE_KEPALA:
            return 'Kepala Puskesmas';
        default:
            return '-';
    }
}

/**
 * Sejak dukungan multi-role ditambahkan, $_SESSION['role'] berisi ARRAY
 * berisi semua role_id yang dimiliki user (lihat tbl_user_role & otentikasi/
 * proses-login.php), bukan lagi satu angka tunggal. Dua helper di bawah ini
 * dipakai di seluruh aplikasi supaya pengecekan role tetap aman dipakai baik
 * untuk array (kondisi normal) maupun kalau session lama masih berupa
 * scalar (jaga-jaga/fallback).
 */

/**
 * Apakah user yang sedang login memiliki role tertentu?
 *
 * @param int $roleId Salah satu ROLE_*.
 */
function userHasRole($roleId)
{
    $sessionRoles = (array) ($_SESSION['role'] ?? []);

    return in_array((int) $roleId, array_map('intval', $sessionRoles), true);
}

/**
 * Apakah user yang sedang login memiliki minimal salah satu dari role
 * yang diberikan?
 *
 * @param int[] $roleIds Daftar ROLE_* yang dicek.
 */
function userHasAnyRole(array $roleIds)
{
    $sessionRoles = array_map('intval', (array) ($_SESSION['role'] ?? []));
    $roleIds      = array_map('intval', $roleIds);

    return count(array_intersect($roleIds, $sessionRoles)) > 0;
}

/**
 * Wajibkan user sudah login DAN rolenya termasuk salah satu $allowedRoles.
 * Panggil ini di baris paling atas setiap halaman yang butuh proteksi.
 *
 * @param int[] $allowedRoles Daftar ROLE_* yang boleh mengakses halaman ini.
 * @param string $loginUrl    Lokasi redirect jika belum login.
 * @param string $homeUrl     Lokasi redirect jika role tidak diizinkan.
 */
function cekAkses(array $allowedRoles, $loginUrl = '../otentikasi/index.php', $homeUrl = '../index.php')
{
    if (!isset($_SESSION['ssLoginRM'])) {
        header("location: $loginUrl");
        exit();
    }

    // Jaga-jaga untuk sesi lama: kalau role somehow kosong padahal akun ini
    // punya daftar role (mis. sesi dari sebelum perubahan ini), arahkan ke
    // halaman pilih role dulu supaya tidak error. Pada alur normal sekarang
    // $_SESSION['role'] langsung diisi SEMUA role saat login (lihat
    // otentikasi/proses-login.php), jadi blok ini seharusnya tidak pernah
    // kepakai.
    if (empty($_SESSION['role']) && !empty($_SESSION['ssRolesTersedia'])) {
        $pilihRoleUrl = str_replace('index.php', 'pilih-role.php', $loginUrl);
        header("location: $pilihRoleUrl");
        exit();
    }

    if (!userHasAnyRole($allowedRoles)) {
        header("location: $homeUrl");
        exit();
    }
}
