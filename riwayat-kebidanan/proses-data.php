<?php

session_start();

require "../template/rbac.php";

// Hanya Bidan
cekAkses([ROLE_BIDAN]);

require "../config.php";


// ======================================================
// HAPUS DATA REKAM MEDIS KEBIDANAN
// ======================================================

if (isset($_GET['hapus']) && !empty($_GET['hapus'])) {

    $id = mysqli_real_escape_string(
        $koneksi,
        $_GET['hapus']
    );


    // Cek apakah data memang ada

    $cek = mysqli_query(
        $koneksi,
        "SELECT id
         FROM tbl_rekammedis_bidan
         WHERE id = '$id'"
    );


    if (!$cek) {

        echo "<script>
            alert('Terjadi kesalahan saat mengecek data: "
            . addslashes(mysqli_error($koneksi)) .
            "');
            window.location='index.php';
        </script>";

        exit();
    }


    if (mysqli_num_rows($cek) == 0) {

        echo "<script>

            alert('Data rekam medis tidak ditemukan.');

            window.location='index.php';

        </script>";

        exit();
    }


    // ==================================================
    // HAPUS
    // ==================================================

    $hapus = mysqli_query(
        $koneksi,
        "DELETE FROM tbl_rekammedis_bidan
         WHERE id = '$id'"
    );


    if ($hapus) {

        echo "<script>

            alert('Rekam medis kebidanan berhasil dihapus.');

            window.location='index.php';

        </script>";

        exit();

    } else {

        echo "<script>

            alert('Data gagal dihapus: "
            . addslashes(mysqli_error($koneksi)) .
            "');

            window.location='index.php';

        </script>";

        exit();
    }
}



// ======================================================
// SIMPAN DATA
// ======================================================

if (isset($_POST['simpan'])) {

    $id_pasien = mysqli_real_escape_string(
        $koneksi,
        $_POST['id_pasien']
    );

    $tgl_rm = mysqli_real_escape_string(
        $koneksi,
        $_POST['tgl_rm']
    );

    $keluhan = mysqli_real_escape_string(
        $koneksi,
        trim($_POST['keluhan'])
    );

    $hasil_pemeriksaan = mysqli_real_escape_string(
        $koneksi,
        trim($_POST['hasil_pemeriksaan'])
    );

    $diagnosa = mysqli_real_escape_string(
        $koneksi,
        trim($_POST['diagnosa'])
    );

    $tindakan = mysqli_real_escape_string(
        $koneksi,
        trim($_POST['tindakan'])
    );

    $keterangan = mysqli_real_escape_string(
        $koneksi,
        trim($_POST['keterangan'])
    );


    // Ambil nomor RM pasien

    $cekPasien = mysqli_query(
        $koneksi,
        "SELECT no_rm
         FROM tbl_pasien
         WHERE id = '$id_pasien'"
    );


    if (!$cekPasien) {

        die(
            "Query pasien gagal: "
            . mysqli_error($koneksi)
        );
    }


    $dataPasien = mysqli_fetch_assoc($cekPasien);


    if (!$dataPasien) {

        die("Data pasien tidak ditemukan.");
    }


    $no_rm = $dataPasien['no_rm'];


    // ==================================================
    // INSERT
    // ==================================================

    $simpan = mysqli_query(
        $koneksi,
        "INSERT INTO tbl_rekammedis_bidan
        (
            id_pasien,
            no_rm,
            tgl_rm,
            keluhan,
            hasil_pemeriksaan,
            diagnosa,
            tindakan,
            keterangan
        )
        VALUES
        (
            '$id_pasien',
            '$no_rm',
            '$tgl_rm',
            '$keluhan',
            '$hasil_pemeriksaan',
            '$diagnosa',
            '$tindakan',
            '$keterangan'
        )"
    );


    if ($simpan) {

        echo "<script>

            alert('Rekam medis kebidanan berhasil disimpan!');

            window.location='index.php';

        </script>";

        exit();

    } else {

        echo "<script>

            alert('Data gagal disimpan: "
            . addslashes(mysqli_error($koneksi)) .
            "');

            window.location='index.php';

        </script>";

        exit();
    }
}



// ======================================================
// UPDATE DATA
// ======================================================

if (isset($_POST['update-kebidanan'])) {

    $id = mysqli_real_escape_string(
        $koneksi,
        $_POST['id']
    );

    $tgl_rm = mysqli_real_escape_string(
        $koneksi,
        $_POST['tgl_rm']
    );

    $keluhan = mysqli_real_escape_string(
        $koneksi,
        trim($_POST['keluhan'])
    );

    $hasil_pemeriksaan = mysqli_real_escape_string(
        $koneksi,
        trim($_POST['hasil_pemeriksaan'])
    );

    $diagnosa = mysqli_real_escape_string(
        $koneksi,
        trim($_POST['diagnosa'])
    );

    $tindakan = mysqli_real_escape_string(
        $koneksi,
        trim($_POST['tindakan'])
    );

    $keterangan = mysqli_real_escape_string(
        $koneksi,
        trim($_POST['keterangan'])
    );


    // ==================================================
    // UPDATE
    // ==================================================

    $update = mysqli_query(
        $koneksi,
        "UPDATE tbl_rekammedis_bidan SET

            tgl_rm = '$tgl_rm',
            keluhan = '$keluhan',
            hasil_pemeriksaan = '$hasil_pemeriksaan',
            diagnosa = '$diagnosa',
            tindakan = '$tindakan',
            keterangan = '$keterangan'

         WHERE id = '$id'"
    );


    if ($update) {

        echo "<script>

            alert('Rekam medis kebidanan berhasil diperbarui!');

            window.location='index.php';

        </script>";

        exit();

    } else {

        echo "<script>

            alert('Data gagal diperbarui: "
            . addslashes(mysqli_error($koneksi)) .
            "');

            window.location='index.php';

        </script>";

        exit();
    }
}

?>