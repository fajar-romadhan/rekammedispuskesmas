<?php

session_start();

require "../template/rbac.php";

cekAkses([ROLE_ADMIN, ROLE_PETUGAS, ROLE_DOKTER, ROLE_BIDAN]);

require "../config.php";


// =====================================================
// TAMBAH DATA REKAM MEDIS
// =====================================================

if (isset($_POST['simpan'])) {

    $tgl                = $_POST['tgl'];
    $idpasien           = $_POST['id'];
    $id_antrian         = !empty($_POST['id_antrian']) ? mysqli_real_escape_string($koneksi, $_POST['id_antrian']) : null;
    $keluhan            = trim(htmlspecialchars($_POST['keluhan']));
    $dokter             = $_POST['dokter'];
    $diagnosa           = trim(htmlspecialchars($_POST['diagnosa']));
    $obat               = trim(htmlspecialchars($_POST['obat']));
    $anamnesa           = trim(htmlspecialchars($_POST['anamnesa']));
    $pemeriksaan_fisik  = trim(htmlspecialchars($_POST['pemeriksaan_fisik']));
    $pemeriksaan_lab    = trim(htmlspecialchars($_POST['pemeriksaan_lab']));
    $tindakan            = trim(htmlspecialchars($_POST['tindakan']));
    $tindakan_billing   = trim(htmlspecialchars($_POST['tindakan_billing'] ?? ''));
    $poli               = trim(htmlspecialchars($_POST['poli']));
    $rujuk_internal     = trim(htmlspecialchars($_POST['rujuk_internal']));
    $rujuk_eksternal    = trim(htmlspecialchars($_POST['rujuk_eksternal']));


    // =================================================
    // HITUNG TOTAL TAGIHAN (OBAT + TINDAKAN)
    // =================================================
    //
    // Dicocokkan berdasarkan nama persis dengan data harga di
    // tbl_obat (menu Obat milik Petugas). Item yang tidak
    // ditemukan/diketik manual (bukan dari daftar) tidak ikut
    // terhitung ke tagihan.
    // =================================================

    $total_biaya = hitungTotalBiaya($koneksi, $obat) + hitungTotalBiaya($koneksi, $tindakan_billing);


    // =================================================
    // MULAI TRANSAKSI
    // =================================================

    mysqli_begin_transaction($koneksi);

    try {

        // =============================================
        // CEK STOK OBAT TERLEBIH DAHULU
        // =============================================

        $daftarObat = [];

        if (!empty($obat)) {

            $obatArray = explode(',', $obat);

            foreach ($obatArray as $namaObat) {

                $namaObat = trim($namaObat);

                if ($namaObat != '') {

                    if (isset($daftarObat[$namaObat])) {
                        $daftarObat[$namaObat]++;
                    } else {
                        $daftarObat[$namaObat] = 1;
                    }

                }
            }
        }


        // =============================================
        // CEK SETIAP STOK OBAT
        // =============================================

        foreach ($daftarObat as $namaObat => $jumlah) {

            $namaObatDB = mysqli_real_escape_string(
                $koneksi,
                $namaObat
            );

            $queryStok = mysqli_query($koneksi, "
                SELECT id, nama, stok
                FROM tbl_obat
                WHERE nama = '$namaObatDB'
                FOR UPDATE
            ");

            if (!$queryStok || mysqli_num_rows($queryStok) == 0) {

                throw new Exception(
                    "Obat \"$namaObat\" tidak ditemukan di data obat."
                );

            }

            $dataObat = mysqli_fetch_assoc($queryStok);


            // =========================================
            // CEK STOK CUKUP ATAU TIDAK
            // =========================================

            if ((int)$dataObat['stok'] < $jumlah) {

                throw new Exception(
                    "Stok obat \"$namaObat\" tidak mencukupi. Stok tersedia: "
                    . $dataObat['stok']
                );

            }

        }


        // =============================================
        // SIMPAN REKAM MEDIS
        // =============================================

        $query = mysqli_query($koneksi, "

            INSERT INTO tbl_rekammedis
            (
                tgl_rm,
                id_pasien,
                id_antrian,
                keluhan,
                id_dokter,
                diagnosa,
                anamnesa,
                pemeriksaan_fisik,
                pemeriksaan_lab,
                tindakan,
                tindakan_billing,
                resep_obat,
                total_biaya,
                poli,
                rujuk_internal,
                rujuk_eksternal,
                ttd
            )

            VALUES
            (
                '$tgl',
                '$idpasien',
                " . ($id_antrian ? "'$id_antrian'" : 'NULL') . ",
                '$keluhan',
                '$dokter',
                '$diagnosa',
                '$anamnesa',
                '$pemeriksaan_fisik',
                '$pemeriksaan_lab',
                '$tindakan',
                '$tindakan_billing',
                '$obat',
                '$total_biaya',
                '$poli',
                '$rujuk_internal',
                '$rujuk_eksternal',
                ''
            )

        ");


        if (!$query) {

            throw new Exception(
                "Gagal menyimpan rekam medis: "
                . mysqli_error($koneksi)
            );

        }


        // =============================================
        // BUAT TAGIHAN PEMBAYARAN (UNTUK PETUGAS)
        // =============================================

        $id_rm_baru = mysqli_insert_id($koneksi);

        $queryNamaPasien = mysqli_query($koneksi, "
            SELECT nama FROM tbl_pasien WHERE id = '$idpasien' LIMIT 1
        ");

        $namaPasienRow = $queryNamaPasien ? mysqli_fetch_assoc($queryNamaPasien) : null;
        $namaPasienUntukTagihan = $namaPasienRow ? $namaPasienRow['nama'] : $idpasien;

        // Jenis pembayaran ikut dari pendaftaran (tbl_antrian.jenis_pembayaran
        // -- diisi pasien/petugas saat daftar ke antrian). Kalau tidak lewat
        // antrian (Admin/Petugas input bebas), jatuhkan ke jenis_pembayaran
        // default pasien di tbl_pasien.
        $jenisPembayaranTagihan = 'Umum';

        if ($id_antrian) {

            $queryJenisBayar = mysqli_query($koneksi, "
                SELECT jenis_pembayaran FROM tbl_antrian WHERE id = '$id_antrian' LIMIT 1
            ");

            $jenisBayarRow = $queryJenisBayar ? mysqli_fetch_assoc($queryJenisBayar) : null;

        } else {

            $queryJenisBayar = mysqli_query($koneksi, "
                SELECT jenis_pembayaran FROM tbl_pasien WHERE id = '$idpasien' LIMIT 1
            ");

            $jenisBayarRow = $queryJenisBayar ? mysqli_fetch_assoc($queryJenisBayar) : null;

        }

        if ($jenisBayarRow && !empty($jenisBayarRow['jenis_pembayaran'])) {

            $jenisPembayaranTagihan = normalisasiJenisPembayaran($jenisBayarRow['jenis_pembayaran']);

        }

        upsertPembayaran(
            $koneksi,
            'umum',
            $id_rm_baru,
            $namaPasienUntukTagihan,
            $poli,
            $tgl,
            $total_biaya,
            $jenisPembayaranTagihan
        );


        // =============================================
        // TANDAI ANTRIAN SELESAI (JIKA DIPERIKSA LEWAT ANTRIAN)
        // =============================================

        if ($id_antrian) {

            mysqli_query($koneksi, "
                UPDATE tbl_antrian
                SET status = 'Selesai'
                WHERE id = '$id_antrian'
            ");

        }


        // =============================================
        // KURANGI STOK OBAT
        // =============================================

        foreach ($daftarObat as $namaObat => $jumlah) {

            $namaObatDB = mysqli_real_escape_string(
                $koneksi,
                $namaObat
            );

            $updateStok = mysqli_query($koneksi, "

                UPDATE tbl_obat

                SET stok = stok - $jumlah

                WHERE nama = '$namaObatDB'

            ");


            if (!$updateStok) {

                throw new Exception(
                    "Gagal mengurangi stok obat \"$namaObat\"."
                );

            }

        }


        // =============================================
        // SEMUA BERHASIL
        // =============================================

        mysqli_commit($koneksi);

        // PENTING: redirect ke index.php (daftar rekam medis), BUKAN
        // balik ke tambah-data.php begitu saja. tambah-data.php punya
        // guard "wajib lewat antrian" khusus dokter/bidan -- kalau
        // redirect ke situ tanpa membawa antrian_id, guard itu salah
        // kira mereka membuka halaman tanpa lewat antrian, lalu
        // menampilkan alert palsu "Pasien harus didaftarkan Petugas..."
        // TEPAT SETELAH rekam medisnya berhasil tersimpan. index.php
        // tidak punya guard itu sama sekali, jadi aman.
        header("location: index.php?msg=added");
        exit();


    } catch (Exception $e) {

        // Batalkan semua perubahan
        mysqli_rollback($koneksi);

        // Kembali ke form pemeriksaan yang SAMA (pertahankan antrian_id
        // di URL). Kalau id_antrian dilepas di sini, dokter/bidan bukan
        // Admin/Petugas akan kena guard "harus didaftarkan Petugas ke
        // antrian" di tambah-data.php -- alert KEDUA yang membingungkan
        // ini menutupi pesan error aslinya (mis. stok obat tidak
        // mencukupi) padahal pasiennya memang sedang benar diperiksa
        // lewat antrian.
        $urlKembali = 'tambah-data.php'
            . ($id_antrian ? '?antrian_id=' . urlencode($id_antrian) : '');

        echo "<script>

            alert(" . json_encode($e->getMessage()) . ");

            window.location=" . json_encode($urlKembali) . ";

        </script>";

        exit();

    }

}



// =====================================================
// UPDATE DATA REKAM MEDIS
// =====================================================

if (isset($_POST['update'])) {

    $id_rm              = $_POST['id_rm'];
    $tgl                = $_POST['tgl'];
    $idpasien           = $_POST['id'];
    $keluhan            = trim(htmlspecialchars($_POST['keluhan']));
    $dokter             = $_POST['dokter'];
    $diagnosa           = trim(htmlspecialchars($_POST['diagnosa']));
    $obat               = trim(htmlspecialchars($_POST['obat']));

    $anamnesa           = trim(htmlspecialchars($_POST['anamnesa']));
    $pemeriksaan_fisik  = trim(htmlspecialchars($_POST['pemeriksaan_fisik']));
    $pemeriksaan_lab    = trim(htmlspecialchars($_POST['pemeriksaan_lab']));
    $tindakan            = trim(htmlspecialchars($_POST['tindakan']));
    $tindakan_billing   = trim(htmlspecialchars($_POST['tindakan_billing'] ?? ''));
    $poli               = trim(htmlspecialchars($_POST['poli']));
    $rujuk_internal     = trim(htmlspecialchars($_POST['rujuk_internal']));
    $rujuk_eksternal    = trim(htmlspecialchars($_POST['rujuk_eksternal']));

    $total_biaya = hitungTotalBiaya($koneksi, $obat) + hitungTotalBiaya($koneksi, $tindakan_billing);


    $query = mysqli_query($koneksi, "

        UPDATE tbl_rekammedis SET

            tgl_rm='$tgl',

            id_pasien='$idpasien',

            keluhan='$keluhan',

            id_dokter='$dokter',

            diagnosa='$diagnosa',

            anamnesa='$anamnesa',

            pemeriksaan_fisik='$pemeriksaan_fisik',

            pemeriksaan_lab='$pemeriksaan_lab',

            tindakan='$tindakan',

            tindakan_billing='$tindakan_billing',

            resep_obat='$obat',

            total_biaya='$total_biaya',

            poli='$poli',

            rujuk_internal='$rujuk_internal',

            rujuk_eksternal='$rujuk_eksternal'

        WHERE id_rm='$id_rm'

    ");


    if ($query) {

        $queryNamaPasien = mysqli_query($koneksi, "
            SELECT nama FROM tbl_pasien WHERE id = '$idpasien' LIMIT 1
        ");

        $namaPasienRow = $queryNamaPasien ? mysqli_fetch_assoc($queryNamaPasien) : null;
        $namaPasienUntukTagihan = $namaPasienRow ? $namaPasienRow['nama'] : $idpasien;

        // Jenis pembayaran ikut dari antrian aslinya (kalau rekam medis ini
        // dibuat lewat antrian), fallback ke jenis_pembayaran default pasien.
        $jenisPembayaranTagihan = 'Umum';

        $queryJenisBayar = mysqli_query($koneksi, "
            SELECT COALESCE(a.jenis_pembayaran, p.jenis_pembayaran) AS jenis_pembayaran
            FROM tbl_rekammedis r
            LEFT JOIN tbl_antrian a ON r.id_antrian = a.id
            INNER JOIN tbl_pasien p ON p.id = '$idpasien'
            WHERE r.id_rm = '$id_rm'
        ");

        $jenisBayarRow = $queryJenisBayar ? mysqli_fetch_assoc($queryJenisBayar) : null;

        if ($jenisBayarRow && !empty($jenisBayarRow['jenis_pembayaran'])) {

            $jenisPembayaranTagihan = normalisasiJenisPembayaran($jenisBayarRow['jenis_pembayaran']);

        }

        upsertPembayaran(
            $koneksi,
            'umum',
            (int) $id_rm,
            $namaPasienUntukTagihan,
            $poli,
            $tgl,
            $total_biaya,
            $jenisPembayaranTagihan
        );

        header("location: index.php?msg=update");

    } else {

        die(mysqli_error($koneksi));

    }

    exit();

}



// =====================================================
// HAPUS DATA REKAM MEDIS
// =====================================================

if (
    isset($_GET['aksi']) &&
    $_GET['aksi'] == 'hapus' &&
    !userHasRole(1)
) {

    die("Akses ditolak");

}


if (
    isset($_GET['aksi']) &&
    $_GET['aksi'] == "hapus-data"
) {

    $id_rm = mysqli_real_escape_string(
        $koneksi,
        $_GET['id']
    );


    mysqli_query(
        $koneksi,
        "DELETE FROM tbl_rekammedis WHERE id_rm='$id_rm'"
    );

    // Ikut hapus tagihan pembayarannya (kalau ada) supaya tidak
    // menyisakan baris "Belum Bayar" untuk rekam medis yang sudah
    // tidak ada lagi.
    mysqli_query(
        $koneksi,
        "DELETE FROM tbl_pembayaran WHERE sumber = 'umum' AND ref_id='$id_rm'"
    );


    header("location: index.php?msg=deleted");
    exit();

}

?>