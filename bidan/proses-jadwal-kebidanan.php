<?php

session_start();

require "../template/rbac.php";

// Hanya Bidan
cekAkses([ROLE_BIDAN]);

require "../config.php";


/*
|--------------------------------------------------------------------------
| Batalkan Jadwal Kebidanan Hari Ini
|--------------------------------------------------------------------------
*/

$id   = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$aksi = $_GET['aksi'] ?? '';

if ($id <= 0 || $aksi !== 'batal') {

    echo "<script>
            alert('Data tidak valid.');
            window.location='jadwal-kebidanan.php';
          </script>";

    exit();
}

$update = mysqli_query(
    $koneksi,
    "UPDATE tbl_pendaftaran_kebidanan
     SET status = 'Batal'
     WHERE id_pendaftaran = '$id'
       AND status IN ('Menunggu', 'Dipanggil')"
);

if ($update) {

    echo "<script>
            alert('Jadwal berhasil dibatalkan.');
            window.location='jadwal-kebidanan.php';
          </script>";

} else {

    echo "<script>
            alert('Gagal membatalkan jadwal: " .
            addslashes(mysqli_error($koneksi)) . "');
            window.location='jadwal-kebidanan.php';
          </script>";
}

exit();
