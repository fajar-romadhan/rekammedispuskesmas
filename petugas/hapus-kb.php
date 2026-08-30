<?php

session_start();

require "../template/rbac.php";

// Hanya Petugas
cekAkses([ROLE_PETUGAS]);

require "../config.php";


/*
|--------------------------------------------------------------------------
| CEK ID
|--------------------------------------------------------------------------
*/

if (!isset($_GET['id']) || empty($_GET['id'])) {

    echo "
    <script>
        alert('ID peserta KB tidak ditemukan.');
        window.location='register-kb.php';
    </script>
    ";

    exit();
}


/*
|--------------------------------------------------------------------------
| AMBIL ID
|--------------------------------------------------------------------------
*/

$id_kb = mysqli_real_escape_string(
    $koneksi,
    $_GET['id']
);


/*
|--------------------------------------------------------------------------
| CEK DATA KB
|--------------------------------------------------------------------------
*/

$queryCek = mysqli_query(
    $koneksi,
    "SELECT id_kb
     FROM tbl_kb
     WHERE id_kb = '$id_kb'
     LIMIT 1"
);


if (!$queryCek) {

    echo "
    <script>
        alert('Terjadi kesalahan saat mengecek data peserta KB.');
        window.location='register-kb.php';
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
        alert('Data peserta KB tidak ditemukan.');
        window.location='register-kb.php';
    </script>
    ";

    exit();
}


/*
|--------------------------------------------------------------------------
| HAPUS DATA TERKAIT (JADWAL & PELAYANAN) SUPAYA TIDAK ADA DATA YATIM
|--------------------------------------------------------------------------
*/

mysqli_query(
    $koneksi,
    "DELETE FROM tbl_pendaftaran_kebidanan
     WHERE jenis_layanan = 'KB' AND ref_id = '$id_kb'"
);

mysqli_query(
    $koneksi,
    "DELETE FROM tbl_pelayanan_kb
     WHERE id_kb = '$id_kb'"
);


/*
|--------------------------------------------------------------------------
| HAPUS DATA KB
|--------------------------------------------------------------------------
*/

$queryHapus = mysqli_query(
    $koneksi,
    "DELETE FROM tbl_kb
     WHERE id_kb = '$id_kb'"
);


/*
|--------------------------------------------------------------------------
| HASIL HAPUS
|--------------------------------------------------------------------------
*/

if ($queryHapus) {

    echo "
    <script>
        alert('Data peserta KB berhasil dihapus.');
        window.location='register-kb.php';
    </script>
    ";

    exit();

} else {

    echo "
    <script>
        alert('Data peserta KB gagal dihapus: " .
        addslashes(mysqli_error($koneksi)) .
        "');
        window.location='register-kb.php';
    </script>
    ";

    exit();
}
