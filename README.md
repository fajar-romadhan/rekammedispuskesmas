# 🏥 Sistem Informasi Rekam Medis - Puskesmas Mendis
**Kecamatan Bayung Lencir, Kabupaten Musi Banyuasin, Sumatera Selatan**  
**Status Website:** 🟢 **PRODUCTION LIVE**  
**Domain Resmi:** [https://rekammedispuskesmas.my.id](https://rekammedispuskesmas.my.id)

---

## 📌 Ringkasan Status Proyek
* **Hosting:** ArenHost Indonesia (Server Tarsius - IP `195.88.211.130`).
* **Keamanan:** SSL Let's Encrypt Active (HTTPS Valid) & Apache `.htaccess` Protection.
* **Database:** MySQL `rekamme1_puskesmas` (10 Tabel Terhubung).
* **CI/CD:** GitHub Actions Auto-Deploy ke cPanel `/public_html/`.

---

## 🔑 Akun Login Bawaan Sistem
*URL Login:* [https://rekammedispuskesmas.my.id/otentikasi/index.php](https://rekammedispuskesmas.my.id/otentikasi/index.php)

| No | Username | Password | Role / Jabatan | Nama Pengguna |
| :-: | :--- | :---: | :--- | :--- |
| 1 | `suyanto` | **`1234`** | 👑 **Administrator** | Suyanto (Akses Penuh Sistem) |
| 2 | `nurtasahratia23` | **`1234`** | 👑 **Administrator** | Nurtasah Ratia (Admin Cadangan) |
| 3 | `sellyulandari23` | **`1234`** | 🧑‍💼 **Petugas Loket** | Selly Ulandari (Pendaftaran & Kasir) |
| 4 | `desirosmawati23` | **`1234`** | 🩺 **Dokter** | Desi Rosma Wati (Poli & Rekam Medis) |
| 5 | `meylanekaputri23` | **`1234`** | 🤰 **Bidan** | Meylan Eka Putri (Kebidanan & KB) |
| 6 | `fitriapratiwi23` | **`1234`** | 🏛️ **Kepala Puskesmas** | Fitria Pratiwi (Laporan & Rekapitulasi) |

---

## 🚀 Panduan Update Website (Untuk Developer)

### Cara 1: Auto-Deploy via Push GitHub
```bash
git add .
git commit -m "Deskripsi perubahan"
git push origin main
```

### Cara 2: Update Instan via Terminal cPanel
```bash
cd /home/rekamme1/public_html && git pull origin main
```
