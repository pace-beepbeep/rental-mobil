# Website Rental Mobil

Website rental mobil sederhana yang dibangun dengan PHP, Tailwind CSS, dan MySQL.

## Fitur

- 🚗 Daftar mobil dengan informasi lengkap
- 📝 Form booking rental mobil
- 💰 Kalkulasi harga otomatis berdasarkan durasi rental
- 👨‍💼 Admin panel untuk mengelola mobil dan rental
- 📊 Dashboard statistik
- 📱 Responsive design

## Teknologi

- **Backend**: PHP 7.4+
- **Database**: MySQL
- **Frontend**: HTML, Tailwind CSS, JavaScript
- **Icons**: Font Awesome

## Instalasi

### 1. Persiapan Database

1. Buka phpMyAdmin atau MySQL client
2. Import file `database.sql` untuk membuat database dan tabel
3. Database `rental_mobil` akan otomatis dibuat dengan data sample

### 2. Konfigurasi

Edit file `config.php` jika diperlukan untuk menyesuaikan kredensial database:

```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'rental_mobil');
```

### 3. Menjalankan Website

1. Pastikan Laragon/XAMPP/WAMP sudah berjalan
2. Akses website melalui browser: `http://localhost/rental-mobil/`

## Struktur File

```
rental-mobil/
├── config.php              # Konfigurasi database
├── index.php               # Halaman utama (daftar mobil)
├── detail.php              # Halaman detail mobil & form booking
├── process_rental.php      # Proses booking rental
├── admin.php               # Admin panel
├── database.sql            # Schema database & data sample
└── README.md               # Dokumentasi
```

## Cara Penggunaan

### Untuk Pelanggan

1. Buka halaman utama untuk melihat daftar mobil
2. Klik "Detail" pada mobil yang diinginkan
3. Isi form booking dengan data lengkap
4. Pilih tanggal mulai dan selesai rental
5. Sistem akan otomatis menghitung total harga
6. Klik "Konfirmasi Booking" untuk menyelesaikan

### Untuk Admin

1. Akses halaman admin: `http://localhost/rental-mobil/admin.php`
2. Lihat dashboard statistik
3. Kelola data mobil (view, edit, delete)
4. Lihat daftar rental yang masuk

## Database Schema

### Tabel `cars`

- Menyimpan informasi mobil (nama, merk, tahun, harga, status, dll)

### Tabel `customers`

- Menyimpan data pelanggan yang melakukan booking

### Tabel `rentals`

- Menyimpan data transaksi rental

## Catatan

- Website ini adalah versi sederhana untuk pembelajaran
- Belum ada sistem autentikasi untuk admin
- Fitur edit dan delete mobil belum diimplementasi sepenuhnya
- Untuk production, tambahkan validasi dan keamanan lebih lanjut

## Screenshot

Website menggunakan desain modern dengan:

- Gradient background
- Card-based layout
- Smooth transitions
- Responsive grid
- Icon-based UI

## Lisensi

Free to use untuk pembelajaran dan pengembangan.
