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
        alert('ID ibu hamil tidak ditemukan.');
        window.location='rekam-medis-kebidanan.php';
    </script>
    ";

    exit();
}


/*
|--------------------------------------------------------------------------
| AMBIL ID
|--------------------------------------------------------------------------
*/

$id = mysqli_real_escape_string(
    $koneksi,
    $_GET['id']
);


/*
|--------------------------------------------------------------------------
| CEK DATA IBU HAMIL
|--------------------------------------------------------------------------
*/

$queryCek = mysqli_query(
    $koneksi,
    "SELECT id
     FROM tbl_ibu_hamil
     WHERE id = '$id'
     LIMIT 1"
);


if (!$queryCek) {

    echo "
    <script>
        alert('Terjadi kesalahan saat mengecek data ibu hamil.');
        window.location='rekam-medis-kebidanan.php';
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
        alert('Data ibu hamil tidak ditemukan.');
        window.location='rekam-medis-kebidanan.php';
    </script>
    ";

    exit();
}


/*
|--------------------------------------------------------------------------
| HAPUS DATA TERKAIT (JADWAL & PEMERIKSAAN) SUPAYA TIDAK ADA DATA YATIM
|--------------------------------------------------------------------------
*/

mysqli_query(
    $koneksi,
    "DELETE FROM tbl_pendaftaran_kebidanan
     WHERE jenis_layanan = 'Ibu Hamil' AND ref_id = '$id'"
);

mysqli_query(
    $koneksi,
    "DELETE FROM tbl_pemeriksaan_ibu_hamil
     WHERE ibu_hamil_id = '$id'"
);


/*
|--------------------------------------------------------------------------
| HAPUS DATA IBU HAMIL
|--------------------------------------------------------------------------
*/

$queryHapus = mysqli_query(
    $koneksi,
    "DELETE FROM tbl_ibu_hamil
     WHERE id = '$id'"
);


/*
|--------------------------------------------------------------------------
| HASIL HAPUS
|--------------------------------------------------------------------------
*/

if ($queryHapus) {

    echo "
    <script>
        alert('Data ibu hamil berhasil dihapus.');
        window.location='rekam-medis-kebidanan.php';
    </script>
    ";

    exit();

} else {

    echo "
    <script>
        alert('Data ibu hamil gagal dihapus: " .
        addslashes(mysqli_error($koneksi)) .
        "');
        window.location='rekam-medis-kebidanan.php';
    </script>
    ";

    exit();
}
