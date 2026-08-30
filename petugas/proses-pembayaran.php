<?php

session_start();

require "../template/rbac.php";

// Hanya Petugas
cekAkses([ROLE_PETUGAS]);

require "../config.php";


// ======================================================
// TANDAI LUNAS
// ======================================================

if (isset($_POST['lunas'])) {

    $id = (int) $_POST['id'];

    // Jenis pembayaran (Umum/BPJS/Asuransi) sudah ditentukan sejak
    // pendaftaran dan tersimpan di tbl_pembayaran -- jangan percaya
    // metode_bayar kiriman form begitu saja (bisa dimanipulasi lewat
    // request manual). Untuk BPJS/Asuransi, metode SELALU dipaksa
    // ikut jenisnya sendiri; hanya untuk Umum petugas boleh memilih
    // Tunai/Transfer.
    $queryJenis = mysqli_query($koneksi, "
        SELECT jenis_pembayaran FROM tbl_pembayaran WHERE id = $id LIMIT 1
    ");

    $jenisRow = $queryJenis ? mysqli_fetch_assoc($queryJenis) : null;
    $jenisPembayaran = $jenisRow ? $jenisRow['jenis_pembayaran'] : 'Umum';

    if (in_array($jenisPembayaran, ['BPJS', 'Asuransi'], true)) {

        $metode_bayar = $jenisPembayaran;

    } else {

        $metodeKiriman = trim($_POST['metode_bayar'] ?? 'Tunai');
        $metode_bayar  = in_array($metodeKiriman, ['Tunai', 'Transfer'], true)
            ? $metodeKiriman
            : 'Tunai';

    }

    $metode_bayar = mysqli_real_escape_string($koneksi, $metode_bayar);

    // $dataUser tidak tersedia di sini karena template/sidebar.php
    // (yang membuatnya) tidak di-require pada halaman proses ini.
    $usernameLogin = mysqli_real_escape_string($koneksi, $_SESSION['ssUserRM'] ?? '');

    $queryPetugas = mysqli_query($koneksi, "
        SELECT userid FROM tbl_user WHERE username = '$usernameLogin' LIMIT 1
    ");

    $petugasRow = $queryPetugas ? mysqli_fetch_assoc($queryPetugas) : null;
    $id_petugas = $petugasRow ? (int) $petugasRow['userid'] : 0;

    mysqli_query($koneksi, "
        UPDATE tbl_pembayaran
        SET
            status = 'Lunas',
            metode_bayar = '$metode_bayar',
            tanggal_bayar = NOW(),
            id_petugas = $id_petugas
        WHERE id = $id
          AND status = 'Belum Bayar'
    ");

    header('location: pembayaran.php?msg=lunas');
    exit();

}

header('location: pembayaran.php');
exit();
