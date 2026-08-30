<?php 

session_start();

require "../template/rbac.php";

cekAkses([ROLE_ADMIN, ROLE_PETUGAS, ROLE_DOKTER, ROLE_BIDAN, ROLE_KEPALA], 'index.php');

require "../config.php";

$title = "Password - rekammedispuskesmas";


require "../template/header.php";
require "../template/navbar.php";
require "../template/sidebar.php";

?>


        <div class="page-content-wrap">
          <div class="d-flex justify-content-between flex-wrap flex-md-nowrap 
            align-items-center pt-3 pb-2 mb-3 border-bottom">
            <h1 class="h2">Ganti Password</h1>
          </div>

        <form action="../user/proses-user.php" method="post" class="row">
            <div class="form-group mb-3 col-md-6">
                <label for="oldPass" class="form-label">Password lama</label>
                <input type="password" name="oldPass"
                class="form-control" id="oldPass" placeholder="Password lama"
                autocomplete="off" required>
            </div>
            <div class="form-group mb-3 col-md-6">
                <label for="newPass" class="form-label">Password baru</label>
                <input type="password" name="newPass"
                class="form-control" id="newPass" placeholder="Password baru user"
                autocomplete="off" required>
            </div>
            <div class="form-group mb-3 col-md-6">
                <label for="confPass" class="form-label">Konfirmasi password</label>
                <input type="password" name="confPass"
                class="form-control" id="confPass" placeholder="Masukkan kembali password baru anda"
                autocomplete="off" required>
            </div>

            <div class="col-12">
                <button type="reset" class="btn btn-outline-danger btn-sm me-2">
                    <i class="bi bi-x-lg me-1"></i>Reset
                </button>
                <button type="submit" name="ganti-password" class="btn btn-outline-primary btn-sm">
                    <i class="bi bi-save me-1"></i>Simpan
                </button>
            </div>
            </form>


        </div>



<?php

require "../template/footer.php";

?>   