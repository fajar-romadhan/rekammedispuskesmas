<?php

session_start();

require "../template/rbac.php";

// Hanya Admin Sistem
cekAkses([ROLE_ADMIN]);

require "../config.php";

/* ==========================================
   TAMBAH USER
========================================== */
if (isset($_POST['simpan'])) {

    $username  = trim(htmlspecialchars($_POST['username']));
    $nama      = formatNama(htmlspecialchars($_POST['fullname']));
    $jabatan   = $_POST['jabatan'];
    $alamat    = trim(htmlspecialchars($_POST['alamat']));
    $password  = $_POST['password'];
    $password2 = $_POST['password2'];

    if ($password != $password2) {
        echo "<script>
                alert('Konfirmasi password tidak sesuai!');
                window.history.back();
              </script>";
        exit;
    }

    $cekUsername = mysqli_query($koneksi,
        "SELECT * FROM tbl_user WHERE username='$username'");

    if (mysqli_num_rows($cekUsername) > 0) {
        echo "<script>
                alert('Username sudah digunakan!');
                window.history.back();
              </script>";
        exit;
    }

    $gambar = uploadGbr('tambah-user.php');

    $pass = password_hash($password, PASSWORD_DEFAULT);

    $query = mysqli_query($koneksi, "
        INSERT INTO tbl_user
        VALUES(
            NULL,
            '$username',
            '$nama',
            '$pass',
            '$jabatan',
            '$alamat',
            '$gambar'
        )
    ");

    if (!$query) {
        die(mysqli_error($koneksi));
    }

    // Simpan juga role awalnya ke tbl_user_role (sumber kebenaran untuk
    // multi-role). Role tambahan bisa ditambahkan lewat Atur Hak Akses.
    $userIdBaru = mysqli_insert_id($koneksi);

    mysqli_query($koneksi, "
        INSERT INTO tbl_user_role (userid, role_id)
        VALUES ('$userIdBaru', '$jabatan')
    ");

    echo "<script>
            alert('User baru berhasil diregistrasi!');
            window.location='tambah-user.php';
          </script>";
    return;
}


/* ==========================================
   HAPUS USER
========================================== */
if (@$_GET['aksi'] == 'hapus-user') {

    $id    = $_GET['id'];
    $gbr   = $_GET['gambar'];
    $force = (@$_GET['force'] == '1');

    // Cek dulu apakah user ini masih punya rekam medis (sebagai dokter).
    // tbl_rekammedis.id_dokter -> tbl_user.userid pakai ON DELETE RESTRICT,
    // jadi kalau masih ada rekam medis miliknya, DELETE akan gagal (FK
    // constraint fails). Dicek manual dulu supaya bisa kasih pilihan ke
    // admin: batal, atau hapus sekalian semua rekam medis terkait.
    $cekRm = mysqli_query($koneksi,
        "SELECT COUNT(*) AS jumlah FROM tbl_rekammedis WHERE id_dokter='$id'");
    $jumlahRm = mysqli_fetch_assoc($cekRm)['jumlah'];

    if ($jumlahRm > 0 && !$force) {

        $urlForce = 'proses-user.php?id=' . $id
            . '&gambar=' . urlencode($gbr)
            . '&aksi=hapus-user&force=1';

        echo "<script>
                if (confirm('User ini masih memiliki $jumlahRm data rekam medis. Jika dilanjutkan, SEMUA data rekam medis milik user ini akan ikut terhapus permanen dan tidak bisa dikembalikan. Yakin ingin menghapus semuanya?')) {
                    window.location='$urlForce';
                } else {
                    window.location='index.php';
                }
              </script>";
        exit;
    }

    // Admin sudah konfirmasi hapus sekalian rekam medis terkait.
    if ($jumlahRm > 0 && $force) {
        mysqli_query($koneksi,
            "DELETE FROM tbl_rekammedis WHERE id_dokter='$id'");
    }

    // Jaga-jaga kalau masih ada relasi FK lain yang belum terdeteksi di atas,
    // tangkap error-nya supaya tidak muncul fatal error mentah ke user.
    try {
        mysqli_query($koneksi,
            "DELETE FROM tbl_user WHERE userid='$id'");
    } catch (\mysqli_sql_exception $e) {
        echo "<script>
                alert('User ini tidak bisa dihapus karena masih terkait dengan data lain di sistem.');
                window.location='index.php';
              </script>";
        exit;
    }

    if ($gbr != 'user.png') {
        @unlink('../asset/gambar/' . $gbr);
    }

    echo "<script>
            alert('User berhasil dihapus!');
            window.location='index.php';
          </script>";
    return;
}


/* ==========================================
   UPDATE USER
========================================== */
if (isset($_POST['update'])) {

    $id        = $_POST['id'];
    $userLama  = trim(htmlspecialchars($_POST['usernameLama']));
    $username  = trim(htmlspecialchars($_POST['username']));
    $nama      = formatNama(htmlspecialchars($_POST['fullname']));
    // Jabatan/hak akses tidak lagi diedit dari form ini, lihat Atur Hak Akses.
    $alamat    = trim(htmlspecialchars($_POST['alamat']));
    $gbrLama   = $_POST['gbrlama'];

    // Cek username jika diubah
    if ($username != $userLama) {

        $cekUsername = mysqli_query($koneksi,
            "SELECT * FROM tbl_user WHERE username='$username'");

        if (mysqli_num_rows($cekUsername) > 0) {
            echo "<script>
                    alert('Username sudah digunakan, data user gagal di perbarui!');
                    window.history.back();
                  </script>";
            exit;
        }
    }

    // Upload gambar baru jika ada
    if ($_FILES['gambar']['name'] != "") {

        $gbrUser = uploadGbr('index.php');

        if ($gbrLama != 'user.png') {
            @unlink('../asset/gambar/' . $gbrLama);
        }

    } else {

        $gbrUser = $gbrLama;

    }

    $query = mysqli_query($koneksi, "
        UPDATE tbl_user SET
            username = '$username',
            fullname = '$nama',
            alamat   = '$alamat',
            gambar   = '$gbrUser'
        WHERE userid = '$id'
    ");

    if (!$query) {
        die(mysqli_error($koneksi));
    }

    echo "<script>
            alert('Data user berhasil diperbarui!');
            window.location='index.php';
          </script>";
    return;
}

// ganti password
if (isset($_POST['ganti-password'])) {

    $curPass  = trim($_POST['oldPass']);
    $newPass  = trim($_POST['newPass']);
    $confPass = trim($_POST['confPass']);

    $userLogin = $_SESSION['ssUserRM'];

    $queryUser = mysqli_query($koneksi, "SELECT * FROM tbl_user WHERE username='$userLogin'");
    $dataUser  = mysqli_fetch_assoc($queryUser);

    if ($newPass != $confPass) {

        echo "<script>
            alert('Konfirmasi password tidak sama!');
            window.location='../otentikasi/password.php';
        </script>";
        exit;

    }

    if (!password_verify($curPass, $dataUser['password'])) {

        echo "<script>
            alert('Password lama salah!');
            window.location='../otentikasi/password.php';
        </script>";
        exit;

    }

    $pass = password_hash($newPass, PASSWORD_DEFAULT);

    mysqli_query($koneksi, "UPDATE tbl_user
        SET password='$pass'
        WHERE username='$userLogin'");

    echo "<script>
        alert('Password berhasil diubah');
        window.location='../otentikasi/password.php';
    </script>";
    exit;
}


?>