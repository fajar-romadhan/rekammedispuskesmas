<?php

session_start();

require "../template/rbac.php";

// Hanya Petugas
cekAkses([ROLE_PETUGAS]);

require "../config.php";


// tambah obat / tindakan baru
if (isset($_POST['simpan'])) {

    $nama     = mysqli_real_escape_string($koneksi, trim($_POST['nama']));
    $kategori = ($_POST['kategori'] ?? 'Obat') === 'Tindakan' ? 'Tindakan' : 'Obat';
    $kegunaan = mysqli_real_escape_string($koneksi, trim($_POST['kegunaan']));
    $harga    = (float) ($_POST['harga'] ?? 0);

    mysqli_query($koneksi, "
        INSERT INTO tbl_obat (nama, kategori, kegunaan, harga, stok)
        VALUES ('$nama', '$kategori', '$kegunaan', '$harga', 0)
    ");

    header('location: tambah-obat.php?msg=added');
    exit();

}

// hapus obat
if (isset($_GET['aksi']) && $_GET['aksi'] == 'hapus-obat') {

    $id = (int) $_GET['id'];

    mysqli_query($koneksi, "DELETE FROM tbl_obat WHERE id = $id");

    header('location: index.php?msg=deleted');
    exit();

}

// update obat / tindakan
if (isset($_POST['update'])) {

    $id       = (int) $_POST['id'];
    $nama     = mysqli_real_escape_string($koneksi, trim($_POST['nama']));
    $kategori = ($_POST['kategori'] ?? 'Obat') === 'Tindakan' ? 'Tindakan' : 'Obat';
    $kegunaan = mysqli_real_escape_string($koneksi, trim($_POST['kegunaan']));
    $harga    = (float) ($_POST['harga'] ?? 0);

    mysqli_query($koneksi, "
        UPDATE tbl_obat
        SET nama = '$nama',
            kategori = '$kategori',
            kegunaan = '$kegunaan',
            harga = '$harga'
        WHERE id = $id
    ");

    header('location: index.php?msg=update');
    exit();

}
