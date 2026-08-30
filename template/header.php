<?php
// $title dipakai untuk tag <title> browser (boleh ada akhiran seperti
// " - rekammedispuskesmas"). $pageTitle adalah versi bersihnya (cuma
// bagian nama halaman) untuk ditampilkan di topbar & breadcrumb.
$pageTitle = trim(explode(' - ', $title ?? '')[0]);
if ($pageTitle === '') {
    $pageTitle = 'Rekam Medis';
}
?>
<!doctype html>
<html lang="id">
<head>

    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title><?= htmlspecialchars($title ?? 'Rekam Medis'); ?></title>

    <link rel="icon" type="image/x-icon" href="<?= $main_url ?>asset/gambar/puskesmas.png">

    <!-- Tema Quixlab (bundel Bootstrap 4 + ikon + metisMenu) -->
    <link href="<?= $main_url ?>asset/quixlab/css/style.css" rel="stylesheet">

    <!-- DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/2.3.8/css/dataTables.dataTables.css">

    <!-- Tokenfield (input obat multi-tag) -->
    <link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.css">
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-tokenfield/0.12.0/css/bootstrap-tokenfield.css">

    <!-- Bootstrap Icons (dipakai di banyak halaman selain ikon bawaan Quixlab) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css" rel="stylesheet">

    <style>

        /* ================================
           IDENTITAS PUSKESMAS MENDIS
           (logo di nav-header, background gelap
           bawaan Quixlab -> teks/ikon harus putih,
           dan harus muat di tinggi nav-header 5rem)
        ================================ */

        .nav-header .brand-logo {
            height: 100%;
        }

        .nav-header .brand-logo a {
            display: flex !important;
            align-items: center;
            gap: .6rem;
            height: 100%;
            white-space: nowrap;
        }

        /* Logo bulat -- selalu tampil (termasuk saat sidebar mini),
           sama seperti logo di halaman login */
        .nav-header .brand-logo a .logo-abbr {
            display: inline-flex !important;
            align-items: center;
            justify-content: center;
        }

        .nav-header .brand-logo a .logo-abbr img {
            width: 2.375rem;
            height: 2.375rem;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid rgba(255,255,255,.6);
        }

        .nav-header .brand-logo a span.brand-title {
            color: #ffffff !important;
            font-size: 1.05rem !important;
            font-weight: 700;
            letter-spacing: .02em;
        }

        .user-info-name {
            font-weight: 600;
            font-size: 14px;
        }

        .user-info-role {
            font-size: 12px;
            color: #8a8a8a;
        }

        /* ================================
           SHIM: tombol .btn-close (Bootstrap 5)
           supaya tetap tampil di atas Bootstrap 4
        ================================ */

        .btn-close {
            box-sizing: content-box;
            width: 1em;
            height: 1em;
            padding: .25em;
            color: #000;
            background: transparent;
            border: 0;
            border-radius: .25rem;
            opacity: .5;
            font-size: 1.1rem;
            line-height: 1;
        }

        .btn-close::before {
            content: "\00d7";
        }

        .btn-close:hover {
            opacity: .75;
        }

        /* ================================
           SHIM: .form-select (Bootstrap 5)
           tidak ada bawaan di Bootstrap 4
        ================================ */

        .form-select {
            display: block;
            width: 100%;
            padding: .375rem 2.25rem .375rem .75rem;
            font-size: 1rem;
            font-weight: 400;
            line-height: 1.5;
            color: #212229;
            background-color: #fff;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23343a40' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M2 5l6 6 6-6'/%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right .75rem center;
            background-size: 16px 12px;
            border: 1px solid #cecfda;
            border-radius: .375rem;
            transition: border-color .15s ease-in-out, box-shadow .15s ease-in-out;
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
        }

        .form-select:focus {
            border-color: #b3aefc;
            outline: 0;
            box-shadow: 0 0 0 .25rem rgba(117, 113, 249,.25);
        }

        /* ================================================================
           CHIP / BADGE STATUS -- DIPAKAI DI SELURUH APLIKASI
           ================================================================
           Ditaruh global (bukan di tiap halaman) supaya benar-benar
           konsisten warna & fontnya di mana-mana, dan gampang dirawat
           dari satu tempat.

           Kenapa perlu: markup di banyak halaman lama pakai
           class="badge bg-primary/bg-secondary/bg-success/bg-danger" (atau
           "text-bg-*"). Itu utility Bootstrap 5; di tema Quixlab (Bootstrap
           4) yang "bg-*" cuma nyetel background TANPA warna teks (jadi teks
           gelap di atas warna gelap/jenuh nyaris tak kelihatan, mis. nomor
           antrian di atas ungu), sedangkan "text-bg-*" malah tidak ada
           definisinya sama sekali (badge tampil polos tanpa gaya). Chip di
           bawah ini menggantikan semuanya dengan warna latar terang +
           warna teks yang senada, kontrasnya jelas di kedua kasus.
        ================================================================ */

        /* -- Nomor antrian / angka yang perlu paling menonjol -- */
        .no-antrian-chip {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 42px;
            padding: 7px 12px;
            border-radius: 8px;
            background: #212229;
            color: #ffffff !important;
            font-weight: 700;
            font-size: 14px;
            letter-spacing: .3px;
        }

        /* -- Poli (Umum / Kebidanan) -- */
        .poli-chip {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 6px 11px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .poli-chip-umum {
            background: #f1f1f5;
            color: #494a57;
            border: 1px solid #dedfe6;
        }

        .poli-chip-kebidanan {
            background: #eeeeff;
            color: #5f5ae0;
            border: 1px solid #d1cfff;
        }

        /* -- Status antrian (Menunggu / Dipanggil / Selesai) -- */
        .status-chip {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 6px 11px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .status-chip-menunggu {
            background: #fff3cd;
            color: #8a6416;
            border: 1px solid #ffe69c;
        }

        .status-chip-dipanggil {
            background: #5f5ae0;
            color: #ffffff;
            border: 1px solid #5f5ae0;
        }

        .status-chip-selesai {
            background: #d9f5e3;
            color: #157347;
            border: 1px solid #b6ecc8;
        }

        .status-chip-lain {
            background: #f1f1f5;
            color: #494a57;
            border: 1px solid #dedfe6;
        }

        /* -- Tipe orang (Pasien Umum / Ibu Hamil / Peserta KB) -- */
        .tipe-chip {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
            white-space: nowrap;
        }

        .tipe-chip-pasien {
            background: #f1f1f5;
            color: #494a57;
            border: 1px solid #dedfe6;
        }

        .tipe-chip-ibu-hamil {
            background: #eeeeff;
            color: #5f5ae0;
            border: 1px solid #d1cfff;
        }

        .tipe-chip-kb {
            background: #ffe9f3;
            color: #c2276f;
            border: 1px solid #f7c2dd;
        }

        /* -- Nomor identitas KB (No. Peserta) -- */
        .kb-registration {
            display: inline-block;
            padding: 6px 10px;
            border-radius: 7px;
            background: #212229;
            color: #fff;
            font-size: 11px;
            font-weight: 600;
            letter-spacing: .2px;
        }

        .kb-type {
            display: inline-block;
            padding: 6px 10px;
            border-radius: 20px;
            border: 1px solid #dedfe6;
            background: #f8f8fa;
            color: #494a57;
            font-size: 11px;
            font-weight: 500;
        }

        /* -- Jenis kunjungan KB (Baru / Ganti / Lama) -- */
        .kb-visit {
            display: inline-block;
            padding: 6px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
        }

        .kb-visit-baru {
            background: #212229;
            color: #fff;
        }

        .kb-visit-ganti {
            background: #fff3cd;
            color: #856404;
            border: 1px solid #ffe69c;
        }

        .kb-visit-lama {
            background: #e9e9ef;
            color: #494a57;
            border: 1px solid #dedfe6;
        }

        /* -- Jabatan / role user (Admin, Petugas, Dokter, Bidan, Kepala) -- */
        .badge-jabatan {
            display: inline-flex;
            align-items: center;
            padding: 6px 10px;
            border-radius: 7px;
            font-size: 12px;
            font-weight: 600;
            white-space: nowrap;
            background: #f1f1f5;
            color: #494a57;
        }

        .badge-admin {
            background: #eae8ff;
            color: #5e5ac7;
        }

        .badge-petugas {
            background: #e8f8ef;
            color: #187f4f;
        }

        .badge-dokter {
            background: #e8f8fa;
            color: #087990;
        }

        .badge-bidan {
            background: #fff0f5;
            color: #c9307c;
        }

        .badge-kepala {
            background: #fff7df;
            color: #8d6b04;
        }

        /* -- Stok obat (Habis / mau habis / aman) -- */
        .stock-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            min-width: 75px;
            justify-content: center;
            padding: 6px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .stock-habis {
            background: #ffe5e5;
            color: #c6303e;
        }

        .stock-warning {
            background: #fff3cd;
            color: #856404;
        }

        .stock-aman {
            background: #e3f7eb;
            color: #187c4d;
        }

        /* -- Chip generik serba guna, buat nilai singkat yang belum
           punya kategori warna sendiri (mis. tensi darah) -- */
        .app-chip {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 6px 11px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            background: #f1f1f5;
            color: #494a57;
            border: 1px solid #dedfe6;
        }

        /* Varian "info" -- pengganti "badge bg-info text-dark" yang
           kontrasnya kurang jelas (teks abu-abu gelap di atas biru
           terang #4d7cff, cuma ~2.3:1, jauh di bawah standar 4.5:1). */
        .app-chip-info {
            background: #eeeeff;
            color: #5f5ae0;
            border: 1px solid #d1cfff;
        }

        /* ================================
           SHIM: utilitas logical spacing &
           text-align Bootstrap 5 (ms-/me-/
           text-start/text-end) tidak ada di
           Bootstrap 4 (yang punya ml-/mr-)
        ================================ */

        .ms-0 { margin-left: 0 !important; }
        .ms-1 { margin-left: .25rem !important; }
        .ms-2 { margin-left: .5rem !important; }
        .ms-3 { margin-left: 1rem !important; }
        .ms-4 { margin-left: 1.5rem !important; }
        .ms-5 { margin-left: 3rem !important; }
        .ms-auto { margin-left: auto !important; }

        .me-0 { margin-right: 0 !important; }
        .me-1 { margin-right: .25rem !important; }
        .me-2 { margin-right: .5rem !important; }
        .me-3 { margin-right: 1rem !important; }
        .me-4 { margin-right: 1.5rem !important; }
        .me-5 { margin-right: 3rem !important; }
        .me-auto { margin-right: auto !important; }

        .ps-0 { padding-left: 0 !important; }
        .ps-1 { padding-left: .25rem !important; }
        .ps-2 { padding-left: .5rem !important; }
        .ps-3 { padding-left: 1rem !important; }
        .ps-4 { padding-left: 1.5rem !important; }
        .ps-5 { padding-left: 3rem !important; }

        .pe-0 { padding-right: 0 !important; }
        .pe-1 { padding-right: .25rem !important; }
        .pe-2 { padding-right: .5rem !important; }
        .pe-3 { padding-right: 1rem !important; }
        .pe-4 { padding-right: 1.5rem !important; }
        .pe-5 { padding-right: 3rem !important; }

        .text-start { text-align: left !important; }
        .text-end { text-align: right !important; }

        /* ================================
           SHIM: gap-* (flex/grid gap) dan
           lh-sm/lh-base/lh-lg — Bootstrap 5,
           tidak ada di Bootstrap 4
        ================================ */

        .gap-1 { gap: .25rem !important; }
        .gap-2 { gap: .5rem !important; }
        .gap-3 { gap: 1rem !important; }
        .gap-4 { gap: 1.5rem !important; }
        .gap-5 { gap: 3rem !important; }

        .lh-1 { line-height: 1 !important; }
        .lh-sm { line-height: 1.25 !important; }
        .lh-base { line-height: 1.5 !important; }
        .lh-lg { line-height: 2 !important; }

    </style>

    <!-- jQuery + Bootstrap 4 JS + metisMenu + slimScroll, dimuat di awal
         supaya tersedia untuk script tiap halaman -->
    <script src="<?= $main_url ?>asset/quixlab/plugins/common/common.min.js"></script>

</head>
<body>
