<?php

session_start();

require "../template/rbac.php";

// Hanya Bidan
cekAkses([ROLE_BIDAN]);

require "../config.php";


/*
|--------------------------------------------------------------------------
| CEK ID
|--------------------------------------------------------------------------
*/

if (!isset($_GET['id']) || empty($_GET['id'])) {

    echo "
    <script>
        alert('ID pelayanan KB tidak ditemukan.');
        window.location='pelayanan-kb.php';
    </script>
    ";

    exit();
}


/*
|--------------------------------------------------------------------------
| AMBIL ID
|--------------------------------------------------------------------------
*/

$id_pelayanan_kb = mysqli_real_escape_string(
    $koneksi,
    $_GET['id']
);


/*
|--------------------------------------------------------------------------
| CEK DATA PELAYANAN
|--------------------------------------------------------------------------
*/

$queryCek = mysqli_query(
    $koneksi,
    "SELECT id_pelayanan_kb
     FROM tbl_pelayanan_kb
     WHERE id_pelayanan_kb = '$id_pelayanan_kb'
     LIMIT 1"
);


if (!$queryCek) {

    echo "
    <script>
        alert('Terjadi kesalahan saat mengecek data pelayanan KB.');
        window.location='pelayanan-kb.php';
    </script>
    ";

    exit();
}


/*
|--------------------------------------------------------------------------
| DATA TIDAK DITEMUKAN
|--------------------------------------------------------------------------
*/

if (mysqli_num_rows($queryCek) == 0) {

    echo "
    <script>
        alert('Data pelayanan KB tidak ditemukan.');
        window.location='pelayanan-kb.php';
    </script>
    ";

    exit();
}


/*
|--------------------------------------------------------------------------
| HAPUS DATA
|--------------------------------------------------------------------------
*/

$queryHapus = mysqli_query(
    $koneksi,
    "DELETE FROM tbl_pelayanan_kb
     WHERE id_pelayanan_kb = '$id_pelayanan_kb'"
);


/*
|--------------------------------------------------------------------------
| HASIL HAPUS
|--------------------------------------------------------------------------
*/

if ($queryHapus) {

    echo "
    <script>
        alert('Data pelayanan KB berhasil dihapus.');
        window.location='pelayanan-kb.php';
    </script>
    ";

    exit();

} else {

    echo "
    <script>
        alert('Data pelayanan KB gagal dihapus: " .
        addslashes(mysqli_error($koneksi)) .
        "');
        window.location='pelayanan-kb.php';
    </script>
    ";

    exit();
}