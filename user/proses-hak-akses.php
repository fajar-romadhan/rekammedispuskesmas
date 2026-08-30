<?php

session_start();

require "../template/rbac.php";

// Hanya Admin Sistem
cekAkses([ROLE_ADMIN]);

require "../config.php";


// ==========================================
// PROSES SIMPAN HAK AKSES
// ==========================================

if (isset($_POST['simpan'])) {

    $userid = mysqli_real_escape_string(
        $koneksi,
        $_POST['userid']
    );

    // Sekarang bisa pilih lebih dari satu role sekaligus (checkbox),
    // jadi $_POST['jabatan'] berupa array, mis. ['1','4'].
    $jabatanList = $_POST['jabatan'] ?? [];


    // ======================================
    // VALIDASI ROLE
    // ======================================

    $roleValid = ['1', '2', '3', '4', '5'];

    $jabatanList = array_values(array_intersect($jabatanList, $roleValid));

    if (empty($jabatanList)) {

        echo "<script>

            alert('Pilih minimal 1 hak akses!');

            window.location='edit-hak-akses.php?id=$userid';

        </script>";

        exit();

    }


    // ======================================
    // SIMPAN SEMUA ROLE KE tbl_user_role
    // ======================================

    mysqli_query($koneksi, "
        DELETE FROM tbl_user_role WHERE userid = '$userid'
    ");

    foreach ($jabatanList as $roleId) {

        $roleId = mysqli_real_escape_string($koneksi, $roleId);

        mysqli_query($koneksi, "
            INSERT INTO tbl_user_role (userid, role_id)
            VALUES ('$userid', '$roleId')
        ");

    }


    // ======================================
    // SINKRONKAN tbl_user.jabatan (role utama)
    // ======================================
    // Kolom jabatan lama tetap dipakai sebagai "role utama" oleh beberapa
    // halaman yang belum ikut dimigrasi ke multi-role. Role dengan angka
    // terkecil (paling tinggi wewenangnya) dipakai sebagai role utama.

    $jabatanUtama = min(array_map('intval', $jabatanList));

    $query = mysqli_query($koneksi, "
        UPDATE tbl_user
        SET jabatan = '$jabatanUtama'
        WHERE userid = '$userid'
    ");


    // Kalau admin mengubah hak aksesnya sendiri, refresh session supaya
    // langsung berlaku tanpa perlu logout-login ulang.
    $queryEdited = mysqli_query($koneksi, "
        SELECT username FROM tbl_user WHERE userid = '$userid'
    ");
    $dataEdited  = mysqli_fetch_assoc($queryEdited);

    if ($dataEdited && $dataEdited['username'] === ($_SESSION['ssUserRM'] ?? null)) {
        $_SESSION['role'] = array_map('intval', $jabatanList);
    }


    if ($query) {

        echo "<script>

            alert('Hak akses berhasil diperbarui!');

            window.location='hak-akses.php';

        </script>";

        exit();

    } else {

        echo "<script>

            alert('Hak akses gagal diperbarui!');

            window.location='hak-akses.php';

        </script>";

        exit();

    }

}


// ==========================================
// JIKA AKSES LANGSUNG
// ==========================================

header("location: hak-akses.php");
exit();

?>