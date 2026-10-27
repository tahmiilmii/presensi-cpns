# Sistem Digitalisasi Presensi CPNS dan PPPK Berbasis Web

Aplikasi berbasis web untuk mendukung pengelolaan data peserta dan proses presensi CPNS dan PPPK. Sistem ini menyediakan antarmuka administrasi untuk mengelola peserta, petugas, lokasi, sesi, serta data presensi.

Proyek ini dikembangkan menggunakan PHP dan CodeIgniter 4 dengan pendekatan aplikasi web berbasis database.

## Fitur Utama

### Administrasi
- Autentikasi dan pengelolaan sesi login admin.
- Dashboard administrasi.
- Pengelolaan data peserta.
- Pengelolaan akun petugas.
- Pengelolaan lokasi presensi.
- Pengelolaan sesi presensi.
- Pengelolaan dan pemantauan data presensi.

### Pengelolaan Data
- Tambah, lihat, ubah, dan hapus data.
- Impor data peserta.
- Ekspor data peserta ke Excel dan PDF.
- Ekspor data presensi ke Excel dan PDF.
- Penyediaan template impor peserta.
- Fitur QR code pada pengelolaan peserta.

### Portal Peserta
- Autentikasi peserta.
- Halaman profil peserta.
- Pengelolaan informasi profil.

## Teknologi

- **Bahasa pemrograman:** PHP
- **Framework:** CodeIgniter 4
- **Database:** MySQL/MariaDB
- **Frontend:** HTML, CSS, JavaScript, Bootstrap
- **Dependency management:** Composer

*Catatan: teknologi dan fitur yang dicantumkan perlu disesuaikan dengan implementasi aktual aplikasi.*

## Struktur Aplikasi

```text
presensi-cpns/
├── app/
│   ├── Config/
│   ├── Controllers/
│   │   ├── Admin/
│   │   └── Participant/
│   ├── Database/
│   │   ├── Migrations/
│   │   └── Seeds/
│   ├── Models/
│   └── Views/
├── public/
│   └── assets/
├── tests/
├── writable/
├── composer.json
└── README.md
```

## Persyaratan Sistem

- PHP yang kompatibel dengan versi CodeIgniter 4 pada proyek ini.
- Composer.
- MySQL atau MariaDB.
- Ekstensi PHP yang dibutuhkan oleh CodeIgniter dan driver database.

## Instalasi Lokal

1. Clone repository:

   ```bash
   git clone <URL-REPOSITORY>
   cd presensi-cpns
   ```

2. Instal dependency:

   ```bash
   composer install
   ```

3. Salin file konfigurasi environment:

   ```bash
   cp env .env
   ```

4. Atur `app.baseURL` serta konfigurasi database di `.env`.

5. Buat database kosong sesuai konfigurasi aplikasi.

6. Jalankan migrasi database:

   ```bash
   php spark migrate
   ```

7. Jalankan aplikasi:

   ```bash
   php spark serve
   ```

8. Buka alamat lokal yang ditampilkan oleh server development CodeIgniter.

> Langkah instalasi di atas merupakan panduan awal. Sesuaikan dengan konfigurasi, migrasi, dan kebutuhan aktual proyek sebelum digunakan.

## Catatan Keamanan

- Jangan mengunggah file `.env`, kredensial database, atau data pribadi peserta.
- Gunakan data dummy untuk demonstrasi dan pengujian.
- Kredensial akun demo, jika tersedia, hanya boleh digunakan pada lingkungan lokal dan harus diganti sebelum deployment.
- Terapkan konfigurasi keamanan yang sesuai sebelum aplikasi digunakan pada lingkungan produksi.

## Tujuan Proyek

Proyek ini menjadi bagian dari dokumentasi pengembangan aplikasi web, pengelolaan database, dan implementasi sistem informasi presensi berbasis web.

## Lisensi

Lihat file `LICENSE` untuk informasi lisensi proyek.
