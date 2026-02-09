-- Database untuk Rental Mobil
CREATE DATABASE IF NOT EXISTS rental_mobil;
USE rental_mobil;

-- Tabel Mobil
CREATE TABLE IF NOT EXISTS cars (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    merk VARCHAR(50) NOT NULL,
    tahun INT NOT NULL,
    warna VARCHAR(30) NOT NULL,
    harga_per_hari DECIMAL(10,2) NOT NULL,
    status ENUM('tersedia', 'disewa') DEFAULT 'tersedia',
    transmisi VARCHAR(20) NOT NULL,
    kapasitas INT NOT NULL,
    gambar VARCHAR(255) DEFAULT 'default-car.jpg',
    deskripsi TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabel Pelanggan
CREATE TABLE IF NOT EXISTS customers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    telepon VARCHAR(20) NOT NULL,
    alamat TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabel Rental
CREATE TABLE IF NOT EXISTS rentals (
    id INT AUTO_INCREMENT PRIMARY KEY,
    car_id INT NOT NULL,
    customer_id INT NOT NULL,
    tanggal_mulai DATE NOT NULL,
    tanggal_selesai DATE NOT NULL,
    total_hari INT NOT NULL,
    total_harga DECIMAL(10,2) NOT NULL,
    status ENUM('pending', 'aktif', 'selesai', 'dibatalkan') DEFAULT 'pending',
    catatan TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (car_id) REFERENCES cars(id),
    FOREIGN KEY (customer_id) REFERENCES customers(id)
);

-- Data Sample Mobil
INSERT INTO cars (nama, merk, tahun, warna, harga_per_hari, status, transmisi, kapasitas, gambar, deskripsi) VALUES
('Avanza 1.3 G', 'Toyota', 2022, 'Putih', 350000.00, 'tersedia', 'Manual', 7, 'avanza.jpg', 'Mobil keluarga yang nyaman dan irit bahan bakar. Cocok untuk perjalanan keluarga atau rombongan kecil.'),
('Xenia 1.3 R', 'Daihatsu', 2021, 'Silver', 300000.00, 'tersedia', 'Manual', 7, 'xenia.jpg', 'MPV dengan kabin luas dan harga terjangkau. Ideal untuk liburan keluarga.'),
('Innova Reborn 2.4 G', 'Toyota', 2023, 'Hitam', 500000.00, 'tersedia', 'Automatic', 7, 'innova.jpg', 'Mobil premium dengan kenyamanan maksimal. Dilengkapi fitur modern dan mesin bertenaga.'),
('Brio Satya E', 'Honda', 2022, 'Merah', 250000.00, 'tersedia', 'Manual', 5, 'brio.jpg', 'City car yang lincah dan ekonomis. Sempurna untuk perjalanan dalam kota.'),
('Jazz RS', 'Honda', 2021, 'Putih', 400000.00, 'disewa', 'Automatic', 5, 'jazz.jpg', 'Hatchback sporty dengan desain modern dan performa tangguh.'),
('Ertiga GX', 'Suzuki', 2022, 'Abu-abu', 350000.00, 'tersedia', 'Manual', 7, 'ertiga.jpg', 'MPV compact dengan efisiensi bahan bakar terbaik di kelasnya.'),
('Fortuner 2.4 VRZ', 'Toyota', 2023, 'Hitam', 800000.00, 'tersedia', 'Automatic', 7, 'fortuner.jpg', 'SUV mewah dengan performa off-road yang tangguh. Cocok untuk petualangan dan perjalanan jarak jauh.'),
('Pajero Sport Dakar', 'Mitsubishi', 2022, 'Putih', 750000.00, 'tersedia', 'Automatic', 7, 'pajero.jpg', 'SUV premium dengan teknologi 4WD dan interior mewah.');

-- Data Sample Pelanggan
INSERT INTO customers (nama, email, telepon, alamat) VALUES
('Budi Santoso', 'budi@email.com', '081234567890', 'Jl. Merdeka No. 123, Jakarta'),
('Siti Nurhaliza', 'siti@email.com', '082345678901', 'Jl. Sudirman No. 45, Bandung'),
('Ahmad Rizki', 'ahmad@email.com', '083456789012', 'Jl. Gatot Subroto No. 67, Surabaya');

-- Data Sample Rental
INSERT INTO rentals (car_id, customer_id, tanggal_mulai, tanggal_selesai, total_hari, total_harga, status, catatan) VALUES
(5, 1, '2026-02-07', '2026-02-10', 3, 1200000.00, 'aktif', 'Rental untuk liburan keluarga'),
(2, 2, '2026-02-05', '2026-02-06', 1, 300000.00, 'selesai', 'Perjalanan bisnis'),
(3, 3, '2026-02-08', '2026-02-12', 4, 2000000.00, 'pending', 'Perjalanan ke luar kota');

-- Tabel Admin
CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    nama VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Data Admin Default (username: admin, password: admin123)
-- Password akan di-hash oleh script setup_admin.php
INSERT INTO admins (username, password, nama) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator');
