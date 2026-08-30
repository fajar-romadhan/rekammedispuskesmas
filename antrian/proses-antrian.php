<?php

session_start();

require "../template/rbac.php";

/*
|--------------------------------------------------------------------------
| ANTRIAN: PETUGAS (SEMUA POLI), DOKTER (POLI UMUM), BIDAN (POLI KEBIDANAN)
|--------------------------------------------------------------------------
*/
cekAkses([ROLE_PETUGAS, ROLE_DOKTER, ROLE_BIDAN]);

require "../config.php";

/*
|--------------------------------------------------------------------------
| CEK AKSI
|--------------------------------------------------------------------------
*/
if (isset($_GET['id']) && isset($_GET['aksi'])) {

    $id   = mysqli_real_escape_string($koneksi, $_GET['id']);
    $aksi = $_GET['aksi'];

    /*
    |--------------------------------------------------------------------------
    | SUMBER: 'umum' (default) = tbl_antrian, 'kebidanan' = tbl_pendaftaran_kebidanan
    |--------------------------------------------------------------------------
    | Kebidanan (Ibu Hamil/KB) punya antrian sendiri di tbl_pendaftaran_kebidanan
    | (tidak tersimpan di tbl_antrian, lihat antrian/index.php), jadi aksi
    | panggil/selesai perlu tahu tabel mana yang harus diubah.
    |--------------------------------------------------------------------------
    */
    $sumber   = ($_GET['sumber'] ?? 'umum') === 'kebidanan' ? 'kebidanan' : 'umum';
    $tabel    = ($sumber === 'kebidanan') ? 'tbl_pendaftaran_kebidanan' : 'tbl_antrian';
    $kolomId  = ($sumber === 'kebidanan') ? 'id_pendaftaran' : 'id';
    $balikUrl = ($sumber === 'kebidanan') ? 'index.php?poli=kebidanan' : 'index.php?poli=umum';

    /*
    |--------------------------------------------------------------------------
    | KUNCI POLI: Dokter (bukan Petugas) hanya boleh mengubah antrian Poli
    | Umum, Bidan (bukan Petugas) hanya Poli Kebidanan. Petugas boleh
    | keduanya. Cegah dokter/bidan mengubah antrian poli lain lewat URL.
    |--------------------------------------------------------------------------
    */
    if (!userHasRole(ROLE_PETUGAS)) {

        $poliDiizinkan = [];

        if (userHasRole(ROLE_DOKTER)) {
            $poliDiizinkan[] = 'Umum';
        }

        if (userHasRole(ROLE_BIDAN)) {
            $poliDiizinkan[] = 'Kebidanan';
        }

        // tbl_pendaftaran_kebidanan tidak punya kolom poli (jenis_layanan
        // di situ artinya Ibu Hamil/KB, bukan Umum/Kebidanan) -- sumbernya
        // sendiri sudah cukup menentukan poli baris ini.
        if ($sumber === 'kebidanan') {

            $poliBaris = 'Kebidanan';

        } else {

            $cekPoli = mysqli_query($koneksi, "
                SELECT jenis_layanan
                FROM tbl_antrian
                WHERE id = '$id'
            ");

            $dataPoli  = mysqli_fetch_assoc($cekPoli);
            $poliBaris = $dataPoli['jenis_layanan'] ?? null;

        }

        if (!$poliBaris || !in_array($poliBaris, $poliDiizinkan, true)) {

            echo "<script>
                    alert('Anda tidak berhak mengubah antrian poli ini.');
                    window.location='index.php';
                  </script>";

            exit();
        }
    }

    /*
    |--------------------------------------------------------------------------
    | PANGGIL PASIEN
    |--------------------------------------------------------------------------
    */
    if ($aksi == 'panggil') {

        $query = mysqli_query($koneksi, "
            UPDATE $tabel
            SET status = 'Dipanggil'
            WHERE $kolomId = '$id'
        ");

        if ($query) {

            echo "<script>
                    alert('Pasien berhasil dipanggil.');
                    window.location='$balikUrl';
                  </script>";

        } else {

            echo "<script>
                    alert('Gagal mengubah status antrian.');
                    window.location='$balikUrl';
                  </script>";
        }

        exit();
    }

    /*
    |--------------------------------------------------------------------------
    | SELESAI
    |--------------------------------------------------------------------------
    */
    if ($aksi == 'selesai') {

        $query = mysqli_query($koneksi, "
            UPDATE $tabel
            SET status = 'Selesai'
            WHERE $kolomId = '$id'
        ");

        if ($query) {

            echo "<script>
                    alert('Antrian berhasil diselesaikan.');
                    window.location='$balikUrl';
                  </script>";

        } else {

            echo "<script>
                    alert('Gagal mengubah status antrian.');
                    window.location='$balikUrl';
                  </script>";
        }

        exit();
    }
}

/*
|--------------------------------------------------------------------------
| JIKA AKSI TIDAK DITEMUKAN
|--------------------------------------------------------------------------
*/
header("location: index.php");
exit();

?>