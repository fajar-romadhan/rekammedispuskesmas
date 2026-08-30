<?php

session_start();

require "../template/rbac.php";

// Hanya Bidan
cekAkses([ROLE_BIDAN]);

require "../config.php";

$title = "Laporan Kebidanan - rekammedispuskesmas";

require "../template/header.php";
require "../template/navbar.php";
require "../template/sidebar.php";

?>

<div class="page-content-wrap">

    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap
                align-items-center pt-3 pb-2 mb-3 border-bottom">

        <h1 class="h2">
            <i class="bi bi-file-earmark-medical"></i>
            Laporan Kebidanan
        </h1>

    </div>

    <div class="alert alert-info">
        <i class="bi bi-info-circle me-1"></i>
        Menu laporan kebidanan sedang disiapkan.
    </div>

</div>

<?php

require "../template/footer.php";

?>