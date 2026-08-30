<?php

date_default_timezone_set('Asia/Jakarta');

// ==========================================================================
// PENGATURAN KONEKSI DATABASE (CPANEL ARENHOST)
// ==========================================================================
$host   = 'localhost';
$user   = 'rekamme1_id_rsa';
$pass   = 'J&.k_#,DmW+0G%f&';
$dbname = 'rekamme1_puskesmas';

$koneksi = @mysqli_connect($host, $user, $pass, $dbname);

if (!$koneksi) {
    die("<div style='font-family:sans-serif; padding:20px; max-width:600px; margin:50px auto; border:1px solid #e74c3c; border-radius:8px; background:#fdf2f2;'>
        <h3 style='color:#c0392b; margin-top:0;'>Koneksi Database Gagal!</h3>
        <p>Aplikasi tidak dapat terhubung ke database MySQL.</p>
        <p><strong>Penyebab:</strong> " . mysqli_connect_error() . "</p>
        <p><strong>Solusi:</strong> Buka file <code>config.php</code> dan sesuaikan <code>\$host</code>, <code>\$user</code>, <code>\$pass</code>, serta <code>\$dbname</code> dengan data dari cPanel Hosting.</p>
    </div>");
}

// ==========================================================================
// PENGATURAN BASE URL (OTOMATIS MENYESUAIKAN DOMAIN / SUBFOLDER HOSTING)
// ==========================================================================
// Anda juga bisa mengisi manual, contoh: $main_url = "https://rekammedis.namadomain.com/";
$isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443);
$protocol = $isHttps ? "https://" : "http://";
$hostName = $_SERVER['HTTP_HOST'] ?? 'localhost';
$docRoot  = str_replace('\\', '/', realpath($_SERVER['DOCUMENT_ROOT'] ?? ''));
$baseDir  = str_replace('\\', '/', __DIR__);
$subDir   = trim(str_replace($docRoot, '', $baseDir), '/');

$main_url = $protocol . $hostName . ($subDir ? '/' . $subDir . '/' : '/');

/*
 * Samakan berbagai variasi penulisan jenis pembayaran (tbl_pasien
 * pakai HURUF BESAR SEMUA "BPJS"/"ASURANSI"/"UMUM", tbl_antrian pakai
 * "BPJS"/"Asuransi"/"Umum") ke satu bentuk baku yang dipakai
 * tbl_pembayaran: 'Umum', 'BPJS', atau 'Asuransi'. JANGAN pakai
 * ucfirst(strtolower(...)) untuk ini -- itu mengubah "BPJS" jadi
 * "Bpjs" (salah, harusnya singkatan tetap huruf besar semua).
 */
function normalisasiJenisPembayaran($value){

    $value = strtoupper(trim((string) $value));

    if ($value === 'BPJS') {
        return 'BPJS';
    }

    if ($value === 'ASURANSI') {
        return 'Asuransi';
    }

    return 'Umum';
}

function uploadGbr($url){

    if($_FILES['gambar']['error'] == 4){
        return "user.png";
    }

    $namafile = $_FILES['gambar']['name'];
    $ukuran   = $_FILES['gambar']['size'];
    $tmp      = $_FILES['gambar']['tmp_name'];

    $ekstensiValid = ['jpg','jpeg','png','gif'];
    $ekstensiFile  = strtolower(pathinfo($namafile, PATHINFO_EXTENSION));

    if(!in_array($ekstensiFile, $ekstensiValid)){
        echo "<script>
                alert('File yang diupload bukan gambar!');
                window.location.href='$url';
              </script>";
        exit;
    }

    if($ukuran > 1000000){
        echo "<script>
                alert('Ukuran gambar maksimal 1 MB!');
                window.location.href='$url';
              </script>";
        exit;
    }

    $namafileBaru = time() . '-' . $namafile;

    if(!move_uploaded_file($tmp, '../asset/gambar/' . $namafileBaru)){
        die('Upload gambar gagal!');
    }

    return $namafileBaru;
}

function in_date($tgl){
    $dd    = substr($tgl, 8, 2);
    $mm    = substr($tgl, 5, 2);
    $yy    = substr($tgl, 0, 4);

    return $dd . "-" . $mm . "-" . $yy;
}

function formatNama($nama){
    $nama = trim($nama);

    if ($nama === '') {
        return $nama;
    }

    // Kecilkan dulu semua hurufnya, supaya "SITI" atau "siti" hasil
    // akhirnya tetap sama: "Siti". Lalu kapitalkan huruf pertama tiap kata
    // (termasuk setelah spasi / tanda hubung, mis. "siti-aminah" -> "Siti-Aminah").
    $nama = mb_strtolower($nama, 'UTF-8');

    return mb_convert_case($nama, MB_CASE_TITLE, 'UTF-8');
}

/*
 * Nomor antrian Poli Kebidanan berikutnya untuk tanggal tertentu.
 * Dihitung sama seperti antrian Poli Umum (lihat
 * pendaftaran/proses-pendaftaran.php): MAX(no_antrian) hari itu + 1,
 * satu urutan bersama untuk Ibu Hamil & KB (satu poli, satu antrian),
 * dipakai di semua tempat yang mendaftarkan pasien ke jadwal kebidanan
 * hari ini (petugas/proses-jadwal-kebidanan.php,
 * petugas/register-ibu-hamil.php, petugas/tambah_kb.php).
 */
function nextNoAntrianKebidanan($koneksi, $tanggal){
    $tanggalSafe = mysqli_real_escape_string($koneksi, $tanggal);

    $cek = mysqli_query($koneksi, "
        SELECT MAX(no_antrian) AS max_antrian
        FROM tbl_pendaftaran_kebidanan
        WHERE tanggal = '$tanggalSafe'
    ");

    $data = mysqli_fetch_assoc($cek);

    if (!$data || $data['max_antrian'] == null) {
        return 1;
    }

    return $data['max_antrian'] + 1;
}

function htgumur($tgl_lahir){
    $tgllahir = new DateTime($tgl_lahir);
    $hariini  = new DateTime("today");

    $umur = $hariini->diff($tgllahir)->y;

    return $umur . " tahun";
}

/*
 * Hitung total harga dari daftar nama obat/tindakan yang dipisah koma
 * (format tokenfield yang sudah dipakai di form rekam medis/pelayanan
 * KB/pemeriksaan ibu hamil). Dicocokkan berdasarkan nama persis ke
 * tbl_obat (menu Obat milik Petugas, kolom kategori 'Obat' atau
 * 'Tindakan' sama-sama dicek di sini karena pemanggil sudah memisah
 * mana yang obat vs tindakan). Item yang tidak ketemu di tbl_obat
 * (misal salah ketik / bukan dari daftar) tidak ikut dihitung.
 *
 * Dipakai untuk mengisi total_biaya di tbl_rekammedis,
 * tbl_pemeriksaan_ibu_hamil, dan tbl_pelayanan_kb, yang kemudian jadi
 * total_tagihan di tbl_pembayaran (modul Pembayaran milik Petugas).
 */
function hitungTotalBiaya($koneksi, $daftarNamaCsv){

    $total = 0;

    if (empty($daftarNamaCsv)) {
        return $total;
    }

    $namaArray = explode(',', $daftarNamaCsv);

    foreach ($namaArray as $nama) {

        $nama = trim($nama);

        if ($nama === '') {
            continue;
        }

        $namaSafe = mysqli_real_escape_string($koneksi, $nama);

        $cek = mysqli_query($koneksi, "
            SELECT harga FROM tbl_obat WHERE nama = '$namaSafe' LIMIT 1
        ");

        if ($cek && ($data = mysqli_fetch_assoc($cek))) {
            $total += (float) $data['harga'];
        }

    }

    return $total;
}

/*
 * Buat atau perbarui baris tagihan di tbl_pembayaran untuk satu
 * kunjungan (rekam medis Poli Umum / pemeriksaan ibu hamil /
 * pelayanan KB). Dipanggil sesaat setelah dokter/bidan menyimpan
 * pemeriksaan, supaya otomatis muncul di menu Pembayaran milik
 * Petugas dengan status 'Belum Bayar'.
 *
 * $jenisPembayaran ikut disimpan (Umum/BPJS/Asuransi, sesuai yang
 * dipilih pasien saat pendaftaran) -- dipakai Petugas di halaman
 * Pembayaran untuk MENENTUKAN metode bayarnya, bukan lagi dipilih
 * bebas: BPJS/Asuransi otomatis terkunci ke jenisnya sendiri, Umum
 * baru boleh pilih Tunai/Transfer.
 *
 * Kalau baris untuk kunjungan itu sudah ada DAN sudah 'Lunas',
 * total/nama/tanggal/jenis tetap disegarkan (jaga-jaga ada koreksi
 * data), tapi status Lunas-nya tidak diutak-atik supaya riwayat
 * pembayaran yang sudah selesai tidak berubah jadi "Belum Bayar" lagi.
 *
 * Tagihan Rp 0 (tidak ada obat/tindakan berbayar dipilih) otomatis
 * langsung 'Lunas' (metode "Gratis") -- pasien tidak perlu diproses
 * manual oleh Petugas kalau memang tidak ada yang perlu dibayar.
 */
function upsertPembayaran($koneksi, $sumber, $refId, $namaPasien, $poli, $tanggal, $totalTagihan, $jenisPembayaran = 'Umum'){

    $sumberSafe = mysqli_real_escape_string($koneksi, $sumber);
    $refIdSafe  = (int) $refId;
    $namaSafe   = mysqli_real_escape_string($koneksi, $namaPasien);
    $poliSafe   = mysqli_real_escape_string($koneksi, $poli);
    $tglSafe    = mysqli_real_escape_string($koneksi, $tanggal);
    $totalSafe  = (float) $totalTagihan;

    $jenisPembayaran = in_array($jenisPembayaran, ['Umum', 'BPJS', 'Asuransi'], true)
        ? $jenisPembayaran
        : 'Umum';

    mysqli_query($koneksi, "
        INSERT INTO tbl_pembayaran
            (sumber, ref_id, nama_pasien, poli, jenis_pembayaran, tanggal, total_tagihan, status, metode_bayar, tanggal_bayar)
        VALUES
            (
                '$sumberSafe', $refIdSafe, '$namaSafe', '$poliSafe', '$jenisPembayaran', '$tglSafe', $totalSafe,
                IF($totalSafe <= 0, 'Lunas', 'Belum Bayar'),
                IF($totalSafe <= 0, 'Gratis', NULL),
                IF($totalSafe <= 0, NOW(), NULL)
            )
        ON DUPLICATE KEY UPDATE
            nama_pasien = '$namaSafe',
            poli = '$poliSafe',
            jenis_pembayaran = '$jenisPembayaran',
            tanggal = '$tglSafe',
            total_tagihan = $totalSafe,
            status = IF(status = 'Belum Bayar' AND $totalSafe <= 0, 'Lunas', status),
            metode_bayar = IF(status = 'Belum Bayar' AND $totalSafe <= 0, 'Gratis', metode_bayar),
            tanggal_bayar = IF(status = 'Belum Bayar' AND $totalSafe <= 0, NOW(), tanggal_bayar)
    ");
}

/*
 * Kurangi stok tbl_obat berdasarkan daftar nama obat (format
 * tokenfield, dipisah koma) yang diberikan bidan/dokter. Item yang
 * ketemu lebih dari sekali dihitung jumlahnya (mis. "paracetamol,
 * paracetamol" = 2). Melempar Exception (dengan pesan siap tampil ke
 * user) kalau ada obat yang tidak ditemukan atau stoknya tidak cukup,
 * SEBELUM stok manapun dikurangi -- supaya tidak ada pengurangan
 * separuh jalan.
 *
 * Item yang tidak ditemukan di tbl_obat (misal dari kategori
 * Tindakan, yang memang tidak punya stok) otomatis dilewati, bukan
 * dianggap error -- fungsi ini hanya untuk field obat sungguhan.
 */
function kurangiStokObat($koneksi, $daftarNamaCsv){

    if (empty($daftarNamaCsv)) {
        return;
    }

    $daftarObat = [];

    foreach (explode(',', $daftarNamaCsv) as $nama) {

        $nama = trim($nama);

        if ($nama === '') {
            continue;
        }

        $daftarObat[$nama] = ($daftarObat[$nama] ?? 0) + 1;

    }

    foreach ($daftarObat as $namaObat => $jumlah) {

        $namaObatDB = mysqli_real_escape_string($koneksi, $namaObat);

        $queryStok = mysqli_query($koneksi, "
            SELECT id, stok FROM tbl_obat
            WHERE nama = '$namaObatDB' AND kategori = 'Obat'
        ");

        if (!$queryStok || mysqli_num_rows($queryStok) == 0) {
            // Bukan obat sungguhan (misal tindakan) -- lewati.
            continue;
        }

        $dataObat = mysqli_fetch_assoc($queryStok);

        if ((int) $dataObat['stok'] < $jumlah) {
            throw new Exception(
                "Stok obat \"$namaObat\" tidak mencukupi. Stok tersedia: " . $dataObat['stok']
            );
        }

    }

    foreach ($daftarObat as $namaObat => $jumlah) {

        $namaObatDB = mysqli_real_escape_string($koneksi, $namaObat);

        mysqli_query($koneksi, "
            UPDATE tbl_obat SET stok = stok - $jumlah
            WHERE nama = '$namaObatDB' AND kategori = 'Obat'
        ");

    }

}

?>