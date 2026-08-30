<?php

session_start();

require "../template/rbac.php";

cekAkses([ROLE_ADMIN, ROLE_PETUGAS, ROLE_BIDAN]);

require "../config.php";


// =====================================================
// TAMBAH PASIEN BARU
// =====================================================

if (isset($_POST['simpan'])) {

    // =========================
    // DATA PASIEN
    // =========================

    $nama = formatNama(trim(htmlspecialchars($_POST['nama'])));
    $tgl_lahir = $_POST['tgl_lahir'];
    $gender = $_POST['gender'];

    $telpon = trim(htmlspecialchars($_POST['telpon']));
    $alamat = trim(htmlspecialchars($_POST['alamat']));

    $nik = trim(htmlspecialchars($_POST['nik']));

    // Golongan darah
    $gol_darah = trim(htmlspecialchars($_POST['gol_darah']));

    // Nomor BPJS / Asuransi
    $no_asuransi = trim(htmlspecialchars($_POST['no_asuransi']));

    // Jenis pembayaran
    $jenis_pembayaran = isset($_POST['jenis_pembayaran'])
        ? trim(htmlspecialchars($_POST['jenis_pembayaran']))
        : '';


    // =========================
    // VALIDASI
    // =========================

    if ($nama == '') {
        die('Nama pasien belum diisi.');
    }

    if ($tgl_lahir == '') {
        die('Tanggal lahir belum diisi.');
    }

    if ($gender == '') {
        die('Jenis kelamin belum dipilih.');
    }

    if ($telpon == '') {
        die('Nomor telepon belum diisi.');
    }

    if ($alamat == '') {
        die('Alamat belum diisi.');
    }

    if ($nik == '') {
        die('NIK belum diisi.');
    }

    if ($gol_darah == '') {
        die('Golongan darah belum dipilih.');
    }

    if ($jenis_pembayaran == '') {
        die('Jenis pembayaran belum dipilih.');
    }


    // =========================
    // NOMOR BPJS / ASURANSI
    // =========================

    if (
        $jenis_pembayaran == 'BPJS' ||
        $jenis_pembayaran == 'ASURANSI'
    ) {

        if ($no_asuransi == '') {
            die('Nomor BPJS / Asuransi wajib diisi.');
        }

    } else {

        $no_asuransi = '';

    }


    // =========================
    // ID PASIEN
    // =========================

    $id = date('ymdhis');


    // =========================
    // CEK NIK
    // =========================

    $cekNik = mysqli_query(
        $koneksi,
        "SELECT id FROM tbl_pasien WHERE nik = '$nik'"
    );

    if (mysqli_num_rows($cekNik) > 0) {

        die('NIK pasien sudah terdaftar di dalam sistem.');

    }


    // =========================
    // MEMBUAT NO RM OTOMATIS
    // =========================

    $q = mysqli_query(
        $koneksi,
        "SELECT MAX(no_rm) AS max_rm FROM tbl_pasien"
    );

    $d = mysqli_fetch_assoc($q);


    if (empty($d['max_rm'])) {

        $no_rm = "RM000001";

    } else {

        $angka = (int) substr($d['max_rm'], 2);

        $angka++;

        $no_rm = "RM" . sprintf("%06d", $angka);

    }


    // =========================
    // SIMPAN DATA PASIEN
    // =========================

    $query = mysqli_query(
        $koneksi,
        "
        INSERT INTO tbl_pasien
        (
            id,
            no_rm,
            nama,
            tgl_lahir,
            gender,
            telpon,
            alamat,
            nik,
            gol_darah,
            no_asuransi,
            jenis_pembayaran
        )
        VALUES
        (
            '$id',
            '$no_rm',
            '$nama',
            '$tgl_lahir',
            '$gender',
            '$telpon',
            '$alamat',
            '$nik',
            '$gol_darah',
            '$no_asuransi',
            '$jenis_pembayaran'
        )
        "
    );


    // =========================
    // CEK HASIL SIMPAN
    // =========================

    if (!$query) {

        die(
            'Gagal menyimpan pasien: ' .
            mysqli_error($koneksi)
        );

    }


    // =========================
    // BERHASIL
    // =========================

    echo "
    <script>

        alert('Pasien baru berhasil diregistrasi!');

        window.location = 'tambah-pasien.php';

    </script>
    ";

    exit();

}



// =====================================================
// HAPUS PASIEN
// =====================================================

if (@$_GET['aksi'] == 'hapus-pasien') {

    $id = isset($_GET['id'])
        ? trim(htmlspecialchars($_GET['id']))
        : '';


    if ($id == '') {

        die('ID pasien tidak ditemukan.');

    }


    mysqli_query(
        $koneksi,
        "DELETE FROM tbl_pasien WHERE id = '$id'"
    );


    echo "
    <script>

        alert('Pasien berhasil dihapus!');

        window.location = 'index.php';

    </script>
    ";

    exit();

}



// =====================================================
// UPDATE PASIEN
// =====================================================

if (isset($_POST['update'])) {

    // =========================
    // DATA PASIEN
    // =========================

    $id = trim(
        htmlspecialchars($_POST['id'])
    );

    $nama = formatNama(
        trim(htmlspecialchars($_POST['nama']))
    );

    // PERBAIKAN DI SINI
    $tgl_lahir = $_POST['tgl_lahir'];

    $gender = $_POST['gender'];

    $telpon = trim(
        htmlspecialchars($_POST['telpon'])
    );

    $alamat = trim(
        htmlspecialchars($_POST['alamat'])
    );

    $nik = trim(
        htmlspecialchars($_POST['nik'])
    );

    // Golongan darah
    $gol_darah = trim(
        htmlspecialchars($_POST['gol_darah'])
    );

    // Nomor BPJS / Asuransi
    $no_asuransi = trim(
        htmlspecialchars($_POST['no_asuransi'])
    );

    // Jenis pembayaran
    $jenis_pembayaran = isset($_POST['jenis_pembayaran'])
        ? trim(htmlspecialchars($_POST['jenis_pembayaran']))
        : '';


    // =========================
    // VALIDASI
    // =========================

    if ($id == '') {

        die('ID pasien tidak ditemukan.');

    }

    if ($nama == '') {

        die('Nama pasien belum diisi.');

    }

    if ($tgl_lahir == '') {

        die('Tanggal lahir belum diisi.');

    }

    if ($gender == '') {

        die('Jenis kelamin belum dipilih.');

    }

    if ($telpon == '') {

        die('Nomor telepon belum diisi.');

    }

    if ($alamat == '') {

        die('Alamat belum diisi.');

    }

    if ($nik == '') {

        die('NIK belum diisi.');

    }

    if ($gol_darah == '') {

        die('Golongan darah belum dipilih.');

    }

    if ($jenis_pembayaran == '') {

        die('Jenis pembayaran belum dipilih.');

    }


    // =========================
    // NOMOR BPJS / ASURANSI
    // =========================

    if (
        $jenis_pembayaran == 'BPJS' ||
        $jenis_pembayaran == 'ASURANSI'
    ) {

        if ($no_asuransi == '') {

            die(
                'Nomor BPJS / Asuransi wajib diisi.'
            );

        }

    } else {

        $no_asuransi = '';

    }


    // =========================
    // UPDATE DATA
    // =========================

    $query = mysqli_query(
        $koneksi,
        "
        UPDATE tbl_pasien SET

            nama = '$nama',

            tgl_lahir = '$tgl_lahir',

            gender = '$gender',

            telpon = '$telpon',

            alamat = '$alamat',

            nik = '$nik',

            gol_darah = '$gol_darah',

            no_asuransi = '$no_asuransi',

            jenis_pembayaran = '$jenis_pembayaran'

        WHERE id = '$id'
        "
    );


    // =========================
    // CEK UPDATE
    // =========================

    if (!$query) {

        die(
            'Gagal memperbarui data pasien: ' .
            mysqli_error($koneksi)
        );

    }


    // =========================
    // BERHASIL
    // =========================

    echo "
    <script>

        alert('Data pasien berhasil diperbarui!');

        window.location = 'index.php';

    </script>
    ";

    exit();

}

?>