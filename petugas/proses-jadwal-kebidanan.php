<?php

session_start();

require "../template/rbac.php";

// Hanya Petugas
cekAkses([ROLE_PETUGAS]);

require "../config.php";


/*
|--------------------------------------------------------------------------
| Halaman kembali (whitelist, supaya tidak dipakai untuk open-redirect)
|--------------------------------------------------------------------------
*/

$halamanKembaliValid = [
    'pendaftaran-kebidanan.php',
    'rekam-medis-kebidanan.php',
    'register-kb.php',
];

$balik = $_GET['balik'] ?? 'pendaftaran-kebidanan.php';

if (!in_array($balik, $halamanKembaliValid)) {
    $balik = 'pendaftaran-kebidanan.php';
}


/*
|--------------------------------------------------------------------------
| Validasi Input
|--------------------------------------------------------------------------
*/

$jenis  = $_GET['jenis'] ?? '';
$ref_id = isset($_GET['ref_id']) ? (int) $_GET['ref_id'] : 0;

if (!in_array($jenis, ['Ibu Hamil', 'KB']) || $ref_id <= 0) {

    echo "<script>
            alert('Data tidak valid.');
            window.location='$balik';
          </script>";

    exit();
}


/*
|--------------------------------------------------------------------------
| Pastikan Data Referensi Ada
|--------------------------------------------------------------------------
*/

$tabelRef = ($jenis === 'Ibu Hamil') ? 'tbl_ibu_hamil' : 'tbl_kb';
$kolomId  = ($jenis === 'Ibu Hamil') ? 'id' : 'id_kb';

$cekRef = mysqli_query(
    $koneksi,
    "SELECT $kolomId FROM $tabelRef WHERE $kolomId = '$ref_id'"
);

if (!$cekRef || mysqli_num_rows($cekRef) == 0) {

    echo "<script>
            alert('Data tidak ditemukan.');
            window.location='$balik';
          </script>";

    exit();
}


/*
|--------------------------------------------------------------------------
| Cek Sudah Dijadwalkan Hari Ini
|--------------------------------------------------------------------------
*/

$tanggalHariIni = date('Y-m-d');

/*
| Ada batasan UNIQUE (jenis_layanan, ref_id, tanggal) di tabel ini,
| jadi orang yang sama tidak bisa punya dua baris jadwal di hari yang
| sama walau statusnya sudah 'Selesai'/'Batal'. Kalau barisnya sudah
| ada, buka lagi (jangan insert baris baru) supaya tidak bentrok
| dengan constraint tersebut.
*/

$cekJadwal = mysqli_query(
    $koneksi,
    "SELECT id_pendaftaran, status
     FROM tbl_pendaftaran_kebidanan
     WHERE jenis_layanan = '$jenis'
       AND ref_id = '$ref_id'
       AND tanggal = '$tanggalHariIni'
     LIMIT 1"
);

$jadwalHariIni = $cekJadwal ? mysqli_fetch_assoc($cekJadwal) : null;

if ($jadwalHariIni && $jadwalHariIni['status'] === 'Menunggu') {

    echo "<script>
            alert('Pasien ini sudah masuk jadwal kebidanan hari ini.');
            window.location='$balik';
          </script>";

    exit();
}


/*
|--------------------------------------------------------------------------
| Simpan Jadwal
|--------------------------------------------------------------------------
| Nomor antrian dihitung sama seperti Poli Umum: urut per tanggal, satu
| urutan bersama untuk Ibu Hamil & KB (lihat nextNoAntrianKebidanan() di
| config.php), supaya "Antrian Kebidanan" punya nomor antrian juga.
*/

$noAntrian = nextNoAntrianKebidanan($koneksi, $tanggalHariIni);

if ($jadwalHariIni) {

    // Sudah pernah dijadwalkan hari ini (Selesai/Batal) -> buka lagi,
    // dengan nomor antrian baru karena dia baru masuk antrian lagi sekarang.
    $idJadwal = (int) $jadwalHariIni['id_pendaftaran'];

    $simpan = mysqli_query(
        $koneksi,
        "UPDATE tbl_pendaftaran_kebidanan
         SET status = 'Menunggu', no_antrian = '$noAntrian'
         WHERE id_pendaftaran = '$idJadwal'"
    );

} else {

    $simpan = mysqli_query(
        $koneksi,
        "INSERT INTO tbl_pendaftaran_kebidanan
            (jenis_layanan, ref_id, tanggal, no_antrian, status)
         VALUES
            ('$jenis', '$ref_id', '$tanggalHariIni', '$noAntrian', 'Menunggu')"
    );

}

if ($simpan) {

    echo "<script>
            alert('Pasien berhasil dijadwalkan untuk pelayanan kebidanan hari ini. Nomor antrian: $noAntrian');
            window.location='$balik';
          </script>";

} else {

    echo "<script>
            alert('Gagal menjadwalkan: " .
            addslashes(mysqli_error($koneksi)) . "');
            window.location='$balik';
          </script>";
}

exit();
