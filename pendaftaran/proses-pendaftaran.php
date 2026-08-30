<?php

session_start();

require "../template/rbac.php";

cekAkses([ROLE_ADMIN, ROLE_PETUGAS]);

require "../config.php";


if (isset($_POST['daftar'])) {

    $id_pasien = mysqli_real_escape_string(
        $koneksi,
        $_POST['id_pasien']
    );

    // Poli tujuan: menentukan pasien masuk ke antrian Poli Umum (Dokter)
    // atau Poli Kebidanan (Bidan). Divalidasi terhadap daftar yang memang
    // ada di enum tbl_antrian.jenis_layanan supaya tidak bisa dipalsukan
    // lewat request manual.
    $poliValid = ['Umum', 'Kebidanan'];
    $poli      = $_POST['poli'] ?? '';

    if (!in_array($poli, $poliValid, true)) {

        echo "<script>
            alert('Poli tujuan tidak valid, silakan pilih ulang!');
            window.location='pasien-lama.php?id=$id_pasien';
        </script>";

        exit();
    }

    $jenis_pembayaran = mysqli_real_escape_string(
        $koneksi,
        $_POST['jenis_pembayaran']
    );

    $no_asuransi = mysqli_real_escape_string(
        $koneksi,
        trim($_POST['no_asuransi'])
    );

    $tekanan_darah = mysqli_real_escape_string(
        $koneksi,
        trim($_POST['tekanan_darah'] ?? '')
    );

    $berat_badan = trim($_POST['berat_badan'] ?? '');
    $berat_badan = ($berat_badan === '') ? 'NULL' : "'" . mysqli_real_escape_string($koneksi, $berat_badan) . "'";

    // Tinggi badan hanya dikirim untuk pasien anak (lihat pasien-lama.php)
    $tinggi_badan = trim($_POST['tinggi_badan'] ?? '');
    $tinggi_badan = ($tinggi_badan === '') ? 'NULL' : "'" . mysqli_real_escape_string($koneksi, $tinggi_badan) . "'";


    // Ambil data pasien
    $queryPasien = mysqli_query($koneksi, "
        SELECT *
        FROM tbl_pasien
        WHERE id = '$id_pasien'
    ");

    $pasien = mysqli_fetch_assoc($queryPasien);


    if (!$pasien) {

        echo "<script>
            alert('Data pasien tidak ditemukan!');
            window.location='pasien-lama.php?id=$id_pasien';
        </script>";

        exit();
    }


    /*
     * Nomor antrian berdasarkan tanggal hari ini, dihitung TERPISAH per
     * poli supaya Poli Umum (Dokter) dan Poli Kebidanan (Bidan) masing-
     * masing punya urutan nomor antriannya sendiri (mulai dari 1 lagi).
     */

    $tanggalHariIni = date('Y-m-d');


    $cekAntrian = mysqli_query($koneksi, "
        SELECT MAX(no_antrian) AS max_antrian
        FROM tbl_antrian
        WHERE tanggal = '$tanggalHariIni'
          AND jenis_layanan = '$poli'
    ");


    $dataAntrian = mysqli_fetch_assoc($cekAntrian);


    if (!$dataAntrian || $dataAntrian['max_antrian'] == null) {

        $no_antrian = 1;

    } else {

        $no_antrian = $dataAntrian['max_antrian'] + 1;

    }


    /*
     * Simpan pendaftaran
     *
     * mysqli sejak PHP 8.1 default-nya melempar exception (bukan
     * mengembalikan false) kalau query gagal -> dibungkus try/catch supaya
     * pelanggaran uq_antrian_pasien_layanan_harian (pasien sudah terdaftar
     * di poli yang sama hari ini) bisa ditangkap dan ditampilkan sebagai
     * pesan yang ramah, bukan fatal error mentah.
     */

    try {

        mysqli_query($koneksi, "

            INSERT INTO tbl_antrian
            (
                id_pasien,
                no_rm,
                tanggal,
                no_antrian,
                jenis_pembayaran,
                jenis_layanan,
                berat_badan,
                tinggi_badan,
                tekanan_darah,
                status
            )

            VALUES
            (
                '$id_pasien',
                '{$pasien['no_rm']}',
                '$tanggalHariIni',
                '$no_antrian',
                '$jenis_pembayaran',
                '$poli',
                $berat_badan,
                $tinggi_badan,
                '$tekanan_darah',
                'Menunggu'
            )

        ");

        $namaPoli = $poli === 'Kebidanan' ? 'Poli Kebidanan' : 'Poli Umum';

        echo "<script>

            alert(
                'Pasien berhasil didaftarkan ke $namaPoli! Nomor antrian: $no_antrian'
            );

            window.location='../pasien/index.php';

        </script>";

        exit();

    } catch (mysqli_sql_exception $e) {

        if ($e->getCode() == 1062) {

            // Melanggar uq_antrian_pasien_layanan_harian -> pasien ini
            // sudah terdaftar di poli yang sama untuk hari ini.
            echo "<script>
                alert('Pasien ini sudah terdaftar di poli tersebut untuk hari ini!');
                window.location='pasien-lama.php?id=$id_pasien';
            </script>";

            exit();
        }

        die("Pendaftaran gagal: " . htmlspecialchars($e->getMessage()));
    }

}

?>