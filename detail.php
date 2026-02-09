<?php
require_once 'auth_check.php';
require_once 'config.php';

// Ambil ID mobil dari URL
$car_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Ambil data mobil
$query = "SELECT * FROM cars WHERE id = $car_id";
$result = mysqli_query($conn, $query);
$car = mysqli_fetch_assoc($result);

// Jika mobil tidak ditemukan
if (!$car) {
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $car['nama']; ?> - Rental Mobil</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen">
    <!-- Header -->
    <header class="bg-white shadow-lg sticky top-0 z-50">
        <nav class="container mx-auto px-6 py-4">
            <div class="flex justify-between items-center">
                <div class="flex items-center space-x-2">
                    <i class="fas fa-car text-indigo-600 text-3xl"></i>
                    <h1 class="text-2xl font-bold text-gray-800">Rental<span class="text-indigo-600">Mobil</span></h1>
                </div>
                <div class="hidden md:flex space-x-6">
                    <a href="admin.php" class="text-indigo-600 font-semibold hover:text-indigo-800 transition">Beranda</a>
                    <a href="admin.php#mobil" class="text-gray-600 hover:text-indigo-600 transition">Daftar Mobil</a>
                </div>
            </div>
        </nav>
    </header>

    <!-- Detail Mobil -->
    <section class="container mx-auto px-6 py-12">
        <a href="index.php" class="inline-flex items-center text-indigo-600 hover:text-indigo-800 mb-6">
            <i class="fas fa-arrow-left mr-2"></i> Kembali ke Daftar Mobil
        </a>

        <div class="grid md:grid-cols-2 gap-8">
            <!-- Gambar Mobil -->
            <div class="bg-white rounded-xl shadow-lg p-8">
                <div class="bg-gradient-to-br from-indigo-400 to-purple-500 rounded-lg h-96 flex items-center justify-center mb-6">
                    <i class="fas fa-car text-white text-9xl opacity-50"></i>
                </div>

                <div class="grid grid-cols-3 gap-4">
                    <div class="bg-gradient-to-br from-blue-400 to-indigo-500 rounded-lg h-24 flex items-center justify-center">
                        <i class="fas fa-car-side text-white text-3xl opacity-50"></i>
                    </div>
                    <div class="bg-gradient-to-br from-purple-400 to-pink-500 rounded-lg h-24 flex items-center justify-center">
                        <i class="fas fa-car text-white text-3xl opacity-50"></i>
                    </div>
                    <div class="bg-gradient-to-br from-indigo-400 to-blue-500 rounded-lg h-24 flex items-center justify-center">
                        <i class="fas fa-car-side text-white text-3xl opacity-50"></i>
                    </div>
                </div>
            </div>

            <!-- Info Mobil -->
            <div class="bg-white rounded-xl shadow-lg p-8">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-3xl font-bold text-gray-800"><?php echo $car['nama']; ?></h2>
                    <span class="px-4 py-2 rounded-full text-sm font-semibold <?php echo $car['status'] == 'tersedia' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'; ?>">
                        <?php echo ucfirst($car['status']); ?>
                    </span>
                </div>

                <p class="text-gray-600 mb-6"><?php echo $car['deskripsi']; ?></p>

                <div class="grid grid-cols-2 gap-4 mb-6">
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <i class="fas fa-tag text-indigo-600 mb-2"></i>
                        <p class="text-sm text-gray-600">Merk</p>
                        <p class="font-semibold text-gray-800"><?php echo $car['merk']; ?></p>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <i class="fas fa-calendar text-indigo-600 mb-2"></i>
                        <p class="text-sm text-gray-600">Tahun</p>
                        <p class="font-semibold text-gray-800"><?php echo $car['tahun']; ?></p>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <i class="fas fa-palette text-indigo-600 mb-2"></i>
                        <p class="text-sm text-gray-600">Warna</p>
                        <p class="font-semibold text-gray-800"><?php echo $car['warna']; ?></p>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <i class="fas fa-cog text-indigo-600 mb-2"></i>
                        <p class="text-sm text-gray-600">Transmisi</p>
                        <p class="font-semibold text-gray-800"><?php echo $car['transmisi']; ?></p>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <i class="fas fa-users text-indigo-600 mb-2"></i>
                        <p class="text-sm text-gray-600">Kapasitas</p>
                        <p class="font-semibold text-gray-800"><?php echo $car['kapasitas']; ?> Orang</p>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <i class="fas fa-money-bill-wave text-indigo-600 mb-2"></i>
                        <p class="text-sm text-gray-600">Harga/Hari</p>
                        <p class="font-semibold text-indigo-600"><?php echo format_rupiah($car['harga_per_hari']); ?></p>
                    </div>
                </div>

                <?php if ($car['status'] == 'tersedia'): ?>
                    <button onclick="document.getElementById('formBooking').scrollIntoView({behavior: 'smooth'})" class="w-full bg-indigo-600 text-white py-3 rounded-lg font-semibold hover:bg-indigo-700 transition shadow-lg">
                        <i class="fas fa-calendar-check mr-2"></i> Booking Sekarang
                    </button>
                <?php else: ?>
                    <button disabled class="w-full bg-gray-400 text-white py-3 rounded-lg font-semibold cursor-not-allowed">
                        <i class="fas fa-times-circle mr-2"></i> Mobil Sedang Disewa
                    </button>
                <?php endif; ?>
            </div>
        </div>

        <!-- Form Booking -->
        <!-- Form Booking Removed for Admin View -->
    </section>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white mt-16">
        <div class="container mx-auto px-6 py-8">
            <div class="text-center">
                <p class="text-gray-400">&copy; 2026 RentalMobil. Semua hak dilindungi.</p>
            </div>
        </div>
    </footer>

    <script>
        const hargaPerHari = <?php echo $car['harga_per_hari']; ?>;

        function calculateTotal() {
            const tanggalMulai = document.querySelector('input[name="tanggal_mulai"]').value;
            const tanggalSelesai = document.querySelector('input[name="tanggal_selesai"]').value;

            if (tanggalMulai && tanggalSelesai) {
                const start = new Date(tanggalMulai);
                const end = new Date(tanggalSelesai);
                const diffTime = Math.abs(end - start);
                const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

                if (diffDays > 0) {
                    const totalHarga = diffDays * hargaPerHari;
                    document.getElementById('totalHari').textContent = diffDays + ' hari';
                    document.getElementById('totalHarga').textContent = formatRupiah(totalHarga);
                } else {
                    document.getElementById('totalHari').textContent = '0 hari';
                    document.getElementById('totalHarga').textContent = 'Rp 0';
                }
            }
        }

        function formatRupiah(angka) {
            return 'Rp ' + angka.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        }
    </script>
</body>

</html>