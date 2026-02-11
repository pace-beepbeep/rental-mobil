<?php
require_once 'config.php';

// Ambil data mobil dari database
$query = "SELECT * FROM cars ORDER BY created_at DESC";
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rental Mobil - Sewa Mobil Terpercaya</title>
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
                    <a href="#beranda" class="text-indigo-600 font-semibold hover:text-indigo-800 transition">Beranda</a>
                    <a href="#mobil" class="text-gray-600 hover:text-indigo-600 transition">Daftar Mobil</a>
                    <a href="#kontak" class="text-gray-600 hover:text-indigo-600 transition">Kontak</a>
                </div>
            </div>
        </nav>
    </header>

    <!-- Hero Section -->
    <section id="beranda" class="container mx-auto px-6 py-16">
        <div class="text-center mb-12">
            <h2 class="text-4xl md:text-5xl font-bold text-gray-800 mb-4">
                Sewa Mobil <span class="text-indigo-600">Impian Anda</span>
            </h2>
            <p class="text-gray-600 text-lg">
                Pilih dari berbagai koleksi mobil berkualitas dengan harga terjangkau
            </p>
        </div>

        <!-- Stats -->
        <div class="grid md:grid-cols-3 gap-6 mb-16">
            <?php
            $total_cars = mysqli_num_rows($result);

            $query_available = "SELECT COUNT(*) as count FROM cars WHERE status = 'tersedia'";
            $result_available = mysqli_query($conn, $query_available);
            $available = mysqli_fetch_assoc($result_available)['count'];

            $query_rented = "SELECT COUNT(*) as count FROM cars WHERE status = 'disewa'";
            $result_rented = mysqli_query($conn, $query_rented);
            $rented = mysqli_fetch_assoc($result_rented)['count'];
            ?>

            <div class="bg-white rounded-xl shadow-lg p-8 text-center transform hover:scale-105 transition">
                <div class="bg-indigo-100 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-car text-indigo-600 text-2xl"></i>
                </div>
                <h3 class="text-3xl font-bold text-gray-800"><?php echo $total_cars; ?></h3>
                <p class="text-gray-600">Total Mobil</p>
            </div>

            <div class="bg-white rounded-xl shadow-lg p-8 text-center transform hover:scale-105 transition">
                <div class="bg-green-100 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-check-circle text-green-600 text-2xl"></i>
                </div>
                <h3 class="text-3xl font-bold text-green-600"><?php echo $available; ?></h3>
                <p class="text-gray-600">Mobil Tersedia</p>
            </div>

            <div class="bg-white rounded-xl shadow-lg p-8 text-center transform hover:scale-105 transition">
                <div class="bg-purple-100 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-users text-purple-600 text-2xl"></i>
                </div>
                <h3 class="text-3xl font-bold text-purple-600">1000+</h3>
                <p class="text-gray-600">Pelanggan Puas</p>
            </div>
        </div>
    </section>

    <!-- Daftar Mobil -->
    <section id="mobil" class="container mx-auto px-6 py-12">
        <h2 class="text-3xl font-bold text-gray-800 mb-8 text-center">Pilih Mobil Anda</h2>

        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php
            mysqli_data_seek($result, 0);
            while ($car = mysqli_fetch_assoc($result)):
            ?>
                <div class="bg-white rounded-xl shadow-lg overflow-hidden transform hover:scale-105 transition duration-300">
                    <!-- Gambar Mobil -->
                    <?php if (!empty($car['gambar']) && $car['gambar'] !== 'default-car.jpg' && file_exists('uploads/' . $car['gambar'])): ?>
                        <img src="uploads/<?php echo $car['gambar']; ?>" alt="<?php echo $car['nama']; ?>" class="w-full h-48 object-cover">
                    <?php else: ?>
                        <div class="bg-gradient-to-br from-indigo-400 to-purple-500 h-48 flex items-center justify-center">
                            <i class="fas fa-car text-white text-6xl opacity-50"></i>
                        </div>
                    <?php endif; ?>

                    <!-- Info Mobil -->
                    <div class="p-6">
                        <div class="flex justify-between items-start mb-3">
                            <h3 class="text-xl font-bold text-gray-800"><?php echo $car['nama']; ?></h3>
                            <span class="px-3 py-1 rounded-full text-xs font-semibold <?php echo $car['status'] == 'tersedia' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'; ?>">
                                <?php echo ucfirst($car['status']); ?>
                            </span>
                        </div>

                        <p class="text-gray-600 text-sm mb-4"><?php echo substr($car['deskripsi'], 0, 80); ?>...</p>

                        <div class="grid grid-cols-2 gap-2 mb-4 text-sm">
                            <div class="flex items-center text-gray-600">
                                <i class="fas fa-tag text-indigo-600 mr-2"></i>
                                <?php echo $car['merk']; ?>
                            </div>
                            <div class="flex items-center text-gray-600">
                                <i class="fas fa-calendar text-indigo-600 mr-2"></i>
                                <?php echo $car['tahun']; ?>
                            </div>
                            <div class="flex items-center text-gray-600">
                                <i class="fas fa-cog text-indigo-600 mr-2"></i>
                                <?php echo $car['transmisi']; ?>
                            </div>
                            <div class="flex items-center text-gray-600">
                                <i class="fas fa-users text-indigo-600 mr-2"></i>
                                <?php echo $car['kapasitas']; ?> Orang
                            </div>
                        </div>

                        <div class="flex justify-between items-center">
                            <div>
                                <p class="text-sm text-gray-500">Harga/Hari</p>
                                <p class="text-xl font-bold text-indigo-600"><?php echo format_rupiah($car['harga_per_hari']); ?></p>
                            </div>
                            <a href="car_detail.php?id=<?php echo $car['id']; ?>" class="bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700 transition">
                                Lihat Detail
                            </a>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </section>

    <!-- Kontak Section -->
    <section id="kontak" class="container mx-auto px-6 py-16">
        <div class="bg-white rounded-xl shadow-lg p-8 md:p-12">
            <div class="text-center mb-8">
                <h2 class="text-3xl font-bold text-gray-800 mb-4">Hubungi Kami</h2>
                <p class="text-gray-600">Ingin menyewa mobil? Hubungi kami melalui WhatsApp!</p>
            </div>

            <div class="flex flex-col md:flex-row justify-center items-center gap-6">
                <div class="flex items-center space-x-4">
                    <div class="bg-green-100 rounded-full p-4">
                        <i class="fab fa-whatsapp text-green-600 text-3xl"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">WhatsApp</p>
                        <p class="text-lg font-semibold text-gray-800">Chat Admin</p>
                    </div>
                </div>
                <a href="https://wa.me/628123456789?text=Halo,%20saya%20ingin%20bertanya%20tentang%20rental%20mobil"
                    target="_blank"
                    class="bg-green-500 text-white px-8 py-3 rounded-lg hover:bg-green-600 transition font-semibold flex items-center space-x-2">
                    <i class="fab fa-whatsapp text-xl"></i>
                    <span>Chat Sekarang</span>
                </a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white mt-16">
        <div class="container mx-auto px-6 py-8">
            <div class="grid md:grid-cols-3 gap-8 mb-8">
                <div>
                    <div class="flex items-center space-x-2 mb-4">
                        <i class="fas fa-car text-indigo-400 text-2xl"></i>
                        <h3 class="text-xl font-bold">RentalMobil</h3>
                    </div>
                    <p class="text-gray-400">Layanan rental mobil terpercaya dengan harga terjangkau dan pelayanan terbaik.</p>
                </div>

                <div>
                    <h4 class="font-bold mb-4">Menu</h4>
                    <ul class="space-y-2">
                        <li><a href="#beranda" class="text-gray-400 hover:text-white transition">Beranda</a></li>
                        <li><a href="#mobil" class="text-gray-400 hover:text-white transition">Daftar Mobil</a></li>
                        <li><a href="#kontak" class="text-gray-400 hover:text-white transition">Kontak</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="font-bold mb-4">Kontak</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li><i class="fab fa-whatsapp mr-2"></i> 0812-3456-789</li>
                        <li><i class="fas fa-envelope mr-2"></i> info@rentalmobil.com</li>
                        <li><i class="fas fa-map-marker-alt mr-2"></i> Jakarta, Indonesia</li>
                    </ul>
                </div>
            </div>

            <div class="border-t border-gray-700 pt-6 text-center">
                <p class="text-gray-400">&copy; 2026 RentalMobil. Semua hak dilindungi.</p>
            </div>
        </div>
    </footer>
</body>

</html>