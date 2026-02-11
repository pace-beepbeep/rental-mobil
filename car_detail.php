<?php
require_once 'config.php';

// Ambil ID mobil dari URL
$car_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Ambil data mobil
$query = "SELECT * FROM cars WHERE id = $car_id";
$result = mysqli_query($conn, $query);
$car = mysqli_fetch_assoc($result);

// Jika mobil tidak ditemukan
if (!$car) {
    header('Location: home.php');
    exit;
}

// Nomor WhatsApp Admin (GANTI DENGAN NOMOR YANG SEBENARNYA)
$whatsapp_number = "6285736546272"; // Format: 628123456789 (62 + nomor tanpa 0 di depan)

// Format pesan WhatsApp
$whatsapp_message = "Halo, saya tertarik dengan mobil *" . $car['nama'] . "* (" . $car['merk'] . " " . $car['tahun'] . "). Apakah masih tersedia?";
$whatsapp_url = "https://wa.me/" . $whatsapp_number . "?text=" . urlencode($whatsapp_message);
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
                    <a href="index.php" class="text-indigo-600 font-semibold hover:text-indigo-800 transition">Beranda</a>
                    <a href="index.php#mobil" class="text-gray-600 hover:text-indigo-600 transition">Daftar Mobil</a>
                    <a href="index.php#kontak" class="text-gray-600 hover:text-indigo-600 transition">Kontak</a>
                </div>
            </div>
        </nav>
    </header>

    <!-- Detail Mobil -->
    <section class="container mx-auto px-6 py-12">
        <a href="index.php#mobil" class="inline-flex items-center text-indigo-600 hover:text-indigo-800 mb-6 transition">
            <i class="fas fa-arrow-left mr-2"></i> Kembali ke Daftar Mobil
        </a>

        <div class="grid md:grid-cols-2 gap-8 mb-8">
            <!-- Gambar Mobil -->
            <div class="bg-white rounded-xl shadow-lg p-8">
                <?php if (!empty($car['gambar']) && $car['gambar'] !== 'default-car.jpg' && file_exists('uploads/' . $car['gambar'])): ?>
                    <img src="uploads/<?php echo $car['gambar']; ?>" alt="<?php echo $car['nama']; ?>" class="w-full h-96 object-cover rounded-lg mb-6">
                <?php else: ?>
                    <div class="bg-gradient-to-br from-indigo-400 to-purple-500 rounded-lg h-96 flex items-center justify-center mb-6">
                        <i class="fas fa-car text-white text-9xl opacity-50"></i>
                    </div>
                <?php endif; ?>


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

                <!-- Tombol WhatsApp -->
                <div class="border-t border-gray-200 pt-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-3">Tertarik dengan mobil ini?</h3>
                    <p class="text-gray-600 text-sm mb-4">Hubungi admin kami melalui WhatsApp untuk melakukan pemesanan dan informasi lebih lanjut.</p>

                    <a href="<?php echo $whatsapp_url; ?>"
                        target="_blank"
                        class="w-full bg-green-500 text-white py-4 rounded-lg font-semibold hover:bg-green-600 transition shadow-lg flex items-center justify-center space-x-2">
                        <i class="fab fa-whatsapp text-2xl"></i>
                        <span>Pesan via WhatsApp</span>
                    </a>

                    <div class="mt-4 bg-indigo-50 p-4 rounded-lg">
                        <p class="text-sm text-indigo-800">
                            <i class="fas fa-info-circle mr-2"></i>
                            Admin kami akan membantu Anda dengan proses pemesanan dan memberikan informasi terkait ketersediaan mobil.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Fitur Mobil -->
        <div class="bg-white rounded-xl shadow-lg p-8">
            <h3 class="text-2xl font-bold text-gray-800 mb-6">Fitur & Spesifikasi</h3>

            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="flex items-center space-x-3">
                    <div class="bg-indigo-100 rounded-full p-3">
                        <i class="fas fa-shield-alt text-indigo-600"></i>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-800">Asuransi</p>
                        <p class="text-sm text-gray-600">Fully Insured</p>
                    </div>
                </div>

                <div class="flex items-center space-x-3">
                    <div class="bg-indigo-100 rounded-full p-3">
                        <i class="fas fa-gas-pump text-indigo-600"></i>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-800">Bahan Bakar</p>
                        <p class="text-sm text-gray-600">Bensin</p>
                    </div>
                </div>

                <div class="flex items-center space-x-3">
                    <div class="bg-indigo-100 rounded-full p-3">
                        <i class="fas fa-cog text-indigo-600"></i>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-800">Transmisi</p>
                        <p class="text-sm text-gray-600"><?php echo $car['transmisi']; ?></p>
                    </div>
                </div>

                <div class="flex items-center space-x-3">
                    <div class="bg-indigo-100 rounded-full p-3">
                        <i class="fas fa-snowflake text-indigo-600"></i>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-800">AC</p>
                        <p class="text-sm text-gray-600">Full AC</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white mt-16">
        <div class="container mx-auto px-6 py-8">
            <div class="text-center">
                <p class="text-gray-400">&copy; 2026 RentalMobil. Semua hak dilindungi.</p>
            </div>
        </div>
    </footer>
</body>

</html>