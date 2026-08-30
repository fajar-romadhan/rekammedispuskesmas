<?php 

session_start(); 

require "../template/rbac.php";

// Hanya Petugas
cekAkses([ROLE_PETUGAS]);

require "../config.php"; 

$title = "Tambah Stok Obat - rekammedispuskesmas"; 

require "../template/header.php"; 
require "../template/navbar.php"; 
require "../template/sidebar.php"; 


// ==============================
// CEK ID OBAT
// ==============================

if (!isset($_GET['id']) || empty($_GET['id'])) { 
    header("location: index.php"); 
    exit(); 
} 

$id = mysqli_real_escape_string($koneksi, $_GET['id']); 


// ==============================
// AMBIL DATA OBAT
// ==============================

$query = mysqli_query($koneksi, " 
    SELECT * 
    FROM tbl_obat 
    WHERE id = '$id' 
"); 

if (mysqli_num_rows($query) == 0) { 
    header("location: index.php"); 
    exit(); 
} 

$obat = mysqli_fetch_assoc($query); 


// ==============================
// PROSES TAMBAH STOK
// ==============================

if (isset($_POST['tambah-stok'])) { 

    $jumlah = (int) $_POST['jumlah']; 

    if ($jumlah <= 0) { 

        echo "<script> 
            alert('Jumlah stok harus lebih dari 0!'); 
            window.location='tambah-stok.php?id=$id'; 
        </script>"; 
        exit(); 

    } 

    $update = mysqli_query($koneksi, " 
        UPDATE tbl_obat 
        SET stok = stok + $jumlah 
        WHERE id = '$id' 
    "); 

    if ($update) { 

        echo "<script> 
            alert('Stok obat berhasil ditambahkan!'); 
            window.location='index.php'; 
        </script>"; 
        exit(); 

    } else { 

        echo "<script> 
            alert('Stok obat gagal ditambahkan!'); 
            window.location='tambah-stok.php?id=$id'; 
        </script>"; 
        exit(); 

    } 
} 

?> 


<style>

/* =========================================
   HALAMAN TAMBAH STOK
========================================= */

.stok-page {
    padding-top: 25px;
    padding-bottom: 50px;
}

/* =========================================
   HEADER HALAMAN
========================================= */

.stok-header {
    background: #ffffff;
    border-radius: 14px;
    padding: 18px 22px;
    margin-bottom: 25px;
    box-shadow: 0 3px 15px rgba(0, 0, 0, 0.06);
    border-left: 5px solid #198754;
}

.stok-header h1 {
    margin: 0;
    font-size: 25px;
    font-weight: 600;
    color: #343540;
}

.stok-header h1 i {
    color: #198754;
    margin-right: 8px;
}

/* =========================================
   CARD
========================================= */

.stok-card {
    max-width: 760px;
    margin: 0 auto;
    background: #ffffff;
    border: none;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 5px 25px rgba(0, 0, 0, 0.08);
}

/* =========================================
   CARD HEADER
========================================= */

.stok-card .card-header {
    background: linear-gradient(135deg, #198754, #20a464);
    color: #ffffff;
    padding: 17px 22px;
    border: none;
    font-size: 17px;
    font-weight: 600;
}

.stok-card .card-header i {
    margin-right: 8px;
}

/* =========================================
   CARD BODY
========================================= */

.stok-card .card-body {
    padding: 30px;
}

/* =========================================
   FORM
========================================= */

.stok-card .form-label {
    font-weight: 600;
    color: #494a57;
    margin-bottom: 8px;
}

.stok-card .form-control {
    border: 1px solid #dedfe6;
    border-radius: 9px;
    padding: 11px 14px;
    min-height: 45px;
    transition: all 0.2s ease;
    background-color: #fff;
}

.stok-card .form-control:focus {
    border-color: #198754;
    box-shadow: 0 0 0 0.18rem rgba(25, 135, 84, 0.12);
}

/* INPUT READONLY */

.stok-card .form-control[readonly] {
    background-color: #f8f8fa;
    color: #494a57;
    cursor: default;
}

/* =========================================
   STOK SAAT INI
========================================= */

.stok-sekarang {
    position: relative;
}

.stok-sekarang .form-control {
    font-weight: 600;
    color: #198754;
    background: #f0fff7;
    border-color: #cfeadd;
}

/* =========================================
   INPUT JUMLAH
========================================= */

.input-stok {
    border: 2px solid #e9e9ef !important;
    font-size: 16px;
    font-weight: 500;
}

.input-stok:focus {
    border-color: #198754 !important;
}

/* =========================================
   BUTTON
========================================= */

.btn-stok {
    border-radius: 8px;
    padding: 10px 18px;
    font-weight: 500;
    transition: all 0.2s ease;
}

.btn-stok:hover {
    transform: translateY(-1px);
}

/* =========================================
   INFORMASI
========================================= */

.info-stok {
    background: #f8f8fa;
    border: 1px solid #e9e9ef;
    border-radius: 10px;
    padding: 13px 15px;
    margin-bottom: 25px;
    color: #6c757d;
    font-size: 13px;
}

.info-stok i {
    color: #198754;
    margin-right: 6px;
}

/* =========================================
   RESPONSIVE
========================================= */

@media (max-width: 768px) {

    .stok-page {
        padding-top: 15px;
    }

    .stok-header {
        padding: 15px 17px;
    }

    .stok-header h1 {
        font-size: 21px;
    }

    .stok-card {
        border-radius: 12px;
    }

    .stok-card .card-body {
        padding: 20px;
    }

    .btn-stok {
        width: 100%;
        margin-bottom: 5px;
    }

}

</style>


<div class="page-content-wrap">

    <div class="stok-page">

        <!-- HEADER -->
        <div class="stok-header">

            <h1>
                <i class="bi bi-box-seam"></i>
                Tambah Stok Obat
            </h1>

        </div>


        <!-- CARD -->
        <div class="stok-card">

            <div class="card-header">

                <i class="bi bi-capsule-pill"></i>
                Tambah Stok Obat

            </div>


            <div class="card-body">


                <!-- INFORMASI -->
                <div class="info-stok">

                    <i class="bi bi-info-circle"></i>

                    Silakan masukkan jumlah stok yang ingin ditambahkan
                    pada obat yang dipilih.

                </div>


                <!-- NAMA OBAT -->
                <div class="mb-3">

                    <label class="form-label">
                        Nama Obat
                    </label>

                    <input type="text"
                           class="form-control"
                           value="<?= htmlspecialchars($obat['nama']) ?>"
                           readonly>

                </div>


                <!-- KEGUNAAN -->
                <div class="mb-3">

                    <label class="form-label">
                        Kegunaan
                    </label>

                    <input type="text"
                           class="form-control"
                           value="<?= htmlspecialchars($obat['kegunaan']) ?>"
                           readonly>

                </div>


                <!-- STOK SAAT INI -->
                <div class="mb-3 stok-sekarang">

                    <label class="form-label">
                        Stok Saat Ini
                    </label>

                    <input type="text"
                           class="form-control"
                           value="<?= $obat['stok'] ?>"
                           readonly>

                </div>


                <!-- FORM TAMBAH STOK -->
                <form method="post">

                    <div class="mb-4">

                        <label class="form-label">

                            Jumlah Stok Ditambahkan

                        </label>

                        <input type="number"
                               name="jumlah"
                               class="form-control input-stok"
                               min="1"
                               placeholder="Masukkan jumlah stok"
                               required>

                    </div>


                    <!-- BUTTON -->
                    <div class="d-flex gap-2">

                        <button type="submit"
                                name="tambah-stok"
                                class="btn btn-success btn-stok">

                            <i class="bi bi-plus-circle me-1"></i>

                            Tambah Stok

                        </button>


                        <a href="index.php"
                           class="btn btn-outline-secondary btn-stok">

                            <i class="bi bi-arrow-left me-1"></i>

                            Kembali

                        </a>

                    </div>

                </form>


            </div>

        </div>

    </div>

</div>


<?php 

require "../template/footer.php"; 

?>