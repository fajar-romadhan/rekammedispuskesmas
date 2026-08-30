<?php
session_start();
require "../config.php";

if(isset($_POST['login'])){

    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $password = $_POST['password'];

    $query = mysqli_query(
        $koneksi,
        "SELECT * FROM tbl_user WHERE username='$username'"
    );

    if(mysqli_num_rows($query) > 0){

        $user = mysqli_fetch_assoc($query);

        if(password_verify($password, $user['password'])){
            // Ambil semua role yang dimiliki user (dukungan multi-role).
            // Fallback ke jabatan tunggal kalau belum ada baris di
            // tbl_user_role (mis. data lama yang belum sempat dimigrasi).
            $roles = [];

            $queryRole = mysqli_query(
                $koneksi,
                "SELECT role_id FROM tbl_user_role WHERE userid = '{$user['userid']}'"
            );

            while ($row = mysqli_fetch_assoc($queryRole)) {
                $roles[] = (int) $row['role_id'];
            }

            if (empty($roles)) {
                $roles[] = (int) $user['jabatan'];
            }

            // set session
            $_SESSION['ssLoginRM'] = true;
            $_SESSION['ssUserRM'] = $username;

            // Akun multi-role langsung aktif dengan semua role-nya sekaligus
            // (sidebar menggabungkan semua menu, tidak perlu pilih dulu).
            // ssRolesTersedia tetap disimpan supaya fitur "Ganti Role" di
            // navbar masih bisa dipakai kalau user mau fokus ke satu role.
            $_SESSION['role'] = $roles;
            $_SESSION['ssRolesTersedia'] = $roles;

            header("Location: ../index.php");
            exit();

        } else {

            echo "<script>
                    alert('Password salah!');
                    window.location='index.php';
                  </script>";
        }

    } else {

        echo "<script>
                alert('Username tidak ditemukan!');
                window.location='index.php';
              </script>";
    }

} else {

    header("Location: index.php");
    exit();
}
?>