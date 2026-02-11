<?php
require_once 'auth_check.php';
require_once 'config.php';

// Ambil data mobil
$query_cars = "SELECT * FROM cars ORDER BY created_at DESC";
$result_cars = mysqli_query($conn, $query_cars);

// Ambil data rental dengan join
$query_rentals = "SELECT r.*, c.nama as car_name, cu.nama as customer_name, cu.telepon 
                  FROM rentals r 
                  JOIN cars c ON r.car_id = c.id 
                  JOIN customers cu ON r.customer_id = cu.id 
                  ORDER BY r.created_at DESC";
$result_rentals = mysqli_query($conn, $query_rentals);
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Rental Mobil</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen">
    <!-- Header -->
    <header class="bg-white shadow-lg sticky top-0 z-50">
        <nav class="container mx-auto px-6 py-4">
            <div class="flex justify-between items-center">
                <div class="flex items-center space-x-2">
                    <i class="fas fa-user-shield text-indigo-600 text-3xl"></i>
                    <h1 class="text-2xl font-bold text-gray-800">Admin <span class="text-indigo-600">Panel</span></h1>
                </div>
                <div class="flex items-center space-x-6">
                    <span class="text-gray-600"><i class="fas fa-user mr-2"></i><?php echo $_SESSION['admin_nama']; ?></span>
                    <a href="index.php" class="text-gray-600 hover:text-indigo-600 transition">Beranda</a>
                    <a href="#mobil" class="text-gray-600 hover:text-indigo-600 transition">Kelola Mobil</a>
                    <a href="#rental" class="text-gray-600 hover:text-indigo-600 transition">Daftar Rental</a>
                    <a href="laporan.php" class="text-gray-600 hover:text-indigo-600 transition">Laporan</a>
                    <a href="logout.php" class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600 transition">
                        <i class="fas fa-sign-out-alt mr-2"></i>Logout
                    </a>
                </div>
            </div>
        </nav>
    </header>

    <!-- Dashboard Stats -->
    <section class="container mx-auto px-6 py-12">
        <h2 class="text-3xl font-bold text-gray-800 mb-8">Dashboard</h2>

        <div class="grid md:grid-cols-4 gap-6 mb-12">
            <?php
            $total_cars = mysqli_num_rows($result_cars);
            $total_rentals = mysqli_num_rows($result_rentals);

            $query_available = "SELECT COUNT(*) as count FROM cars WHERE status = 'tersedia'";
            $result_available = mysqli_query($conn, $query_available);
            $available = mysqli_fetch_assoc($result_available)['count'];

            $query_rented = "SELECT COUNT(*) as count FROM cars WHERE status = 'disewa'";
            $result_rented = mysqli_query($conn, $query_rented);
            $rented = mysqli_fetch_assoc($result_rented)['count'];
            ?>

            <div class="bg-white rounded-xl shadow-lg p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm">Total Mobil</p>
                        <p class="text-3xl font-bold text-gray-800"><?php echo $total_cars; ?></p>
                    </div>
                    <div class="bg-indigo-100 rounded-full p-4">
                        <i class="fas fa-car text-indigo-600 text-2xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-lg p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm">Tersedia</p>
                        <p class="text-3xl font-bold text-green-600"><?php echo $available; ?></p>
                    </div>
                    <div class="bg-green-100 rounded-full p-4">
                        <i class="fas fa-check-circle text-green-600 text-2xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-lg p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm">Disewa</p>
                        <p class="text-3xl font-bold text-red-600"><?php echo $rented; ?></p>
                    </div>
                    <div class="bg-red-100 rounded-full p-4">
                        <i class="fas fa-times-circle text-red-600 text-2xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-lg p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm">Total Rental</p>
                        <p class="text-3xl font-bold text-purple-600"><?php echo $total_rentals; ?></p>
                    </div>
                    <div class="bg-purple-100 rounded-full p-4">
                        <i class="fas fa-clipboard-list text-purple-600 text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Kelola Mobil -->
        <div id="mobil" class="mb-12">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-2xl font-bold text-gray-800">Kelola Mobil</h3>
                <button onclick="openAddCarModal()" class="bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700 transition">
                    <i class="fas fa-plus mr-2"></i> Tambah Mobil
                </button>
            </div>

            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Gambar</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Mobil</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Merk</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tahun</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Harga/Hari</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php
                            mysqli_data_seek($result_cars, 0);
                            while ($car = mysqli_fetch_assoc($result_cars)):
                            ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo $car['id']; ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <?php if (!empty($car['gambar']) && $car['gambar'] !== 'default-car.jpg' && file_exists('uploads/' . $car['gambar'])): ?>
                                            <img src="uploads/<?php echo $car['gambar']; ?>" alt="<?php echo $car['nama']; ?>" class="w-12 h-12 object-cover rounded-lg border border-gray-200">
                                        <?php else: ?>
                                            <div class="w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center border border-gray-200">
                                                <i class="fas fa-car text-gray-400"></i>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?php echo $car['nama']; ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo $car['merk']; ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo $car['tahun']; ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo format_rupiah($car['harga_per_hari']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo $car['status'] == 'tersedia' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
                                            <?php echo ucfirst($car['status']); ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <a href="detail.php?id=<?php echo $car['id']; ?>" class="text-indigo-600 hover:text-indigo-900 mr-3">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="#" onclick="openEditCarModal(<?php echo $car['id']; ?>); return false;" class="text-yellow-600 hover:text-yellow-900 mr-3">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="#" onclick="deleteCar(<?php echo $car['id']; ?>); return false;" class="text-red-600 hover:text-red-900">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Daftar Rental -->
        <div id="rental">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-2xl font-bold text-gray-800">Daftar Rental</h3>
                <button onclick="openAddRentalModal()" class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700 transition">
                    <i class="fas fa-plus mr-2"></i> Tambah Rental
                </button>
            </div>

            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mobil</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pelanggan</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Telepon</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Hari</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Harga</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php while ($rental = mysqli_fetch_assoc($result_rentals)): ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo $rental['id']; ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?php echo $rental['car_name']; ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo $rental['customer_name']; ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo $rental['telepon']; ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?php echo date('d/m/Y', strtotime($rental['tanggal_mulai'])); ?> -
                                        <?php echo date('d/m/Y', strtotime($rental['tanggal_selesai'])); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo $rental['total_hari']; ?> hari</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo format_rupiah($rental['total_harga']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <?php
                                        $status_colors = [
                                            'pending' => 'bg-yellow-100 text-yellow-800',
                                            'aktif' => 'bg-blue-100 text-blue-800',
                                            'selesai' => 'bg-green-100 text-green-800',
                                            'dibatalkan' => 'bg-red-100 text-red-800'
                                        ];
                                        ?>
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo $status_colors[$rental['status']]; ?>">
                                            <?php echo ucfirst($rental['status']); ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <div class="flex items-center gap-3">
                                            <a href="#" onclick="viewRentalDetail(<?php echo $rental['id']; ?>); return false;" class="text-indigo-600 hover:text-indigo-900" title="Lihat Detail">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="#" onclick="openEditRentalModal(<?php echo $rental['id']; ?>); return false;" class="text-yellow-600 hover:text-yellow-900" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="#" onclick="deleteRental(<?php echo $rental['id']; ?>); return false;" class="text-red-600 hover:text-red-900" title="Hapus">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                            <select onchange="updateStatus(<?php echo $rental['id']; ?>, this.value)" class="border border-gray-300 rounded px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-600">
                                                <option value="">Ubah Status</option>
                                                <option value="pending" <?php echo $rental['status'] == 'pending' ? 'disabled' : ''; ?>>Pending</option>
                                                <option value="aktif" <?php echo $rental['status'] == 'aktif' ? 'disabled' : ''; ?>>Aktif</option>
                                                <option value="selesai" <?php echo $rental['status'] == 'selesai' ? 'disabled' : ''; ?>>Selesai</option>
                                                <option value="dibatalkan" <?php echo $rental['status'] == 'dibatalkan' ? 'disabled' : ''; ?>>Dibatalkan</option>
                                            </select>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
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

    <!-- Add Rental Modal -->
    <div id="addRentalModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
            <div class="bg-green-600 text-white px-6 py-4 rounded-t-xl flex justify-between items-center">
                <h3 class="text-xl font-bold"><i class="fas fa-plus-circle mr-2"></i>Tambah Rental Baru</h3>
                <button onclick="closeAddRentalModal()" class="text-white hover:text-gray-200">
                    <i class="fas fa-times text-2xl"></i>
                </button>
            </div>
            <form id="addRentalForm" class="p-6">
                <div class="mb-4">
                    <h4 class="font-bold text-gray-800 mb-3"><i class="fas fa-user mr-2 text-green-600"></i>Data Pelanggan</h4>
                    <div class="grid md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">Nama Lengkap *</label>
                            <input type="text" name="customer_name" required class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-green-600">
                        </div>
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">Email *</label>
                            <input type="email" name="customer_email" required class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-green-600">
                        </div>
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">Telepon *</label>
                            <input type="text" name="customer_phone" required class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-green-600">
                        </div>
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">Alamat</label>
                            <input type="text" name="customer_address" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-green-600">
                        </div>
                    </div>
                </div>

                <div class="border-t pt-4 mb-4">
                    <h4 class="font-bold text-gray-800 mb-3"><i class="fas fa-car mr-2 text-green-600"></i>Pilih Mobil & Periode</h4>
                    <div class="grid md:grid-cols-2 gap-4">
                        <div class="md:col-span-2">
                            <label class="block text-gray-700 font-semibold mb-2">Mobil *</label>
                            <select name="car_id" id="add_rental_car_id" required onchange="updateAddRentalPrice()" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-green-600">
                                <option value="">Pilih Mobil</option>
                            </select>
                            <p id="add_rental_car_price" class="text-sm text-gray-600 mt-1"></p>
                        </div>
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">Tanggal Mulai *</label>
                            <input type="date" name="tanggal_mulai" id="add_rental_tanggal_mulai" required onchange="calculateAddRentalTotal()" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-green-600">
                        </div>
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">Tanggal Selesai *</label>
                            <input type="date" name="tanggal_selesai" id="add_rental_tanggal_selesai" required onchange="calculateAddRentalTotal()" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-green-600">
                        </div>
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">Total Hari</label>
                            <input type="text" id="add_rental_total_hari" readonly class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-100">
                        </div>
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">Total Harga</label>
                            <input type="text" id="add_rental_total_harga" readonly class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-100 font-bold text-green-600">
                        </div>
                    </div>
                </div>

                <div class="border-t pt-4">
                    <label class="block text-gray-700 font-semibold mb-2">Catatan</label>
                    <textarea name="catatan" rows="2" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-green-600"></textarea>
                </div>

                <div class="flex justify-end gap-3 mt-6">
                    <button type="button" onclick="closeAddRentalModal()" class="bg-gray-300 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-400 transition">Batal</button>
                    <button type="submit" class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700 transition">
                        <i class="fas fa-save mr-2"></i>Tambah Rental
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- View Rental Detail Modal -->
    <div id="viewRentalModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-2xl max-w-3xl w-full max-h-[90vh] overflow-y-auto">
            <div class="bg-indigo-600 text-white px-6 py-4 rounded-t-xl flex justify-between items-center">
                <h3 class="text-xl font-bold"><i class="fas fa-file-alt mr-2"></i>Detail Rental</h3>
                <button onclick="closeViewRentalModal()" class="text-white hover:text-gray-200">
                    <i class="fas fa-times text-2xl"></i>
                </button>
            </div>
            <div id="rentalDetailContent" class="p-6">
                <!-- Content will be loaded dynamically -->
            </div>
        </div>
    </div>

    <!-- Edit Rental Modal -->
    <div id="editRentalModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
            <div class="bg-yellow-600 text-white px-6 py-4 rounded-t-xl flex justify-between items-center">
                <h3 class="text-xl font-bold"><i class="fas fa-edit mr-2"></i>Edit Rental</h3>
                <button onclick="closeEditRentalModal()" class="text-white hover:text-gray-200">
                    <i class="fas fa-times text-2xl"></i>
                </button>
            </div>
            <form id="editRentalForm" class="p-6">
                <input type="hidden" name="id" id="rental_edit_id">

                <div class="mb-4 bg-gray-100 p-4 rounded-lg">
                    <h4 class="font-bold text-gray-800 mb-2">Informasi Mobil & Pelanggan</h4>
                    <div class="grid md:grid-cols-2 gap-3 text-sm">
                        <div>
                            <span class="text-gray-600">Mobil:</span>
                            <span id="rental_car_info" class="font-semibold text-gray-900"></span>
                        </div>
                        <div>
                            <span class="text-gray-600">Harga/Hari:</span>
                            <span id="rental_price_info" class="font-semibold text-gray-900"></span>
                        </div>
                        <div>
                            <span class="text-gray-600">Pelanggan:</span>
                            <span id="rental_customer_info" class="font-semibold text-gray-900"></span>
                        </div>
                        <div>
                            <span class="text-gray-600">Telepon:</span>
                            <span id="rental_phone_info" class="font-semibold text-gray-900"></span>
                        </div>
                    </div>
                </div>

                <div class="grid md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-gray-700 font-semibold mb-2">Tanggal Mulai *</label>
                        <input type="date" name="tanggal_mulai" id="rental_tanggal_mulai" required onchange="calculateRentalTotal()" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-yellow-600">
                    </div>
                    <div>
                        <label class="block text-gray-700 font-semibold mb-2">Tanggal Selesai *</label>
                        <input type="date" name="tanggal_selesai" id="rental_tanggal_selesai" required onchange="calculateRentalTotal()" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-yellow-600">
                    </div>
                </div>

                <div class="grid md:grid-cols-2 gap-4 mt-4">
                    <div>
                        <label class="block text-gray-700 font-semibold mb-2">Total Hari</label>
                        <input type="text" id="rental_total_hari_display" readonly class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-100">
                    </div>
                    <div>
                        <label class="block text-gray-700 font-semibold mb-2">Total Harga</label>
                        <input type="text" id="rental_total_harga_display" readonly class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-100">
                    </div>
                </div>

                <div class="mt-4">
                    <label class="block text-gray-700 font-semibold mb-2">Status *</label>
                    <select name="status" id="rental_status" required class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-yellow-600">
                        <option value="pending">Pending</option>
                        <option value="aktif">Aktif</option>
                        <option value="selesai">Selesai</option>
                        <option value="dibatalkan">Dibatalkan</option>
                    </select>
                </div>

                <div class="mt-4">
                    <label class="block text-gray-700 font-semibold mb-2">Catatan</label>
                    <textarea name="catatan" id="rental_catatan" rows="3" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-yellow-600"></textarea>
                </div>

                <div class="flex justify-end gap-3 mt-6">
                    <button type="button" onclick="closeEditRentalModal()" class="bg-gray-300 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-400 transition">Batal</button>
                    <button type="submit" class="bg-yellow-600 text-white px-6 py-2 rounded-lg hover:bg-yellow-700 transition">
                        <i class="fas fa-save mr-2"></i>Update
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Add Car Modal -->
    <div id="addCarModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
            <div class="bg-indigo-600 text-white px-6 py-4 rounded-t-xl flex justify-between items-center">
                <h3 class="text-xl font-bold"><i class="fas fa-plus-circle mr-2"></i>Tambah Mobil Baru</h3>
                <button onclick="closeAddCarModal()" class="text-white hover:text-gray-200">
                    <i class="fas fa-times text-2xl"></i>
                </button>
            </div>
            <form id="addCarForm" class="p-6" enctype="multipart/form-data">
                <div class="grid md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-gray-700 font-semibold mb-2">Nama Mobil *</label>
                        <input type="text" name="nama" required class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-600">
                    </div>
                    <div>
                        <label class="block text-gray-700 font-semibold mb-2">Merk *</label>
                        <input type="text" name="merk" required class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-600">
                    </div>
                    <div>
                        <label class="block text-gray-700 font-semibold mb-2">Tahun *</label>
                        <input type="number" name="tahun" required min="1900" max="<?php echo date('Y') + 1; ?>" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-600">
                    </div>
                    <div>
                        <label class="block text-gray-700 font-semibold mb-2">Warna *</label>
                        <input type="text" name="warna" required class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-600">
                    </div>
                    <div>
                        <label class="block text-gray-700 font-semibold mb-2">Harga per Hari (Rp) *</label>
                        <input type="number" name="harga_per_hari" required min="1" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-600">
                    </div>
                    <div>
                        <label class="block text-gray-700 font-semibold mb-2">Transmisi *</label>
                        <select name="transmisi" required class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-600">
                            <option value="">Pilih Transmisi</option>
                            <option value="Manual">Manual</option>
                            <option value="Automatic">Automatic</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-gray-700 font-semibold mb-2">Kapasitas *</label>
                        <input type="number" name="kapasitas" required min="1" max="20" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-600">
                    </div>
                    <div>
                        <label class="block text-gray-700 font-semibold mb-2">Gambar Mobil</label>
                        <input type="file" name="gambar" accept="image/jpeg,image/jpg,image/png" onchange="previewImage(this, 'addPreview')" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-600">
                        <div id="addPreview" class="mt-2"></div>
                    </div>
                </div>
                <div class="mt-4">
                    <label class="block text-gray-700 font-semibold mb-2">Deskripsi</label>
                    <textarea name="deskripsi" rows="3" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-600"></textarea>
                </div>
                <div class="flex justify-end gap-3 mt-6">
                    <button type="button" onclick="closeAddCarModal()" class="bg-gray-300 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-400 transition">Batal</button>
                    <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700 transition">
                        <i class="fas fa-save mr-2"></i>Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Car Modal -->
    <div id="editCarModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
            <div class="bg-yellow-600 text-white px-6 py-4 rounded-t-xl flex justify-between items-center">
                <h3 class="text-xl font-bold"><i class="fas fa-edit mr-2"></i>Edit Mobil</h3>
                <button onclick="closeEditCarModal()" class="text-white hover:text-gray-200">
                    <i class="fas fa-times text-2xl"></i>
                </button>
            </div>
            <form id="editCarForm" class="p-6" enctype="multipart/form-data">
                <input type="hidden" name="id" id="edit_id">
                <div class="grid md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-gray-700 font-semibold mb-2">Nama Mobil *</label>
                        <input type="text" name="nama" id="edit_nama" required class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-yellow-600">
                    </div>
                    <div>
                        <label class="block text-gray-700 font-semibold mb-2">Merk *</label>
                        <input type="text" name="merk" id="edit_merk" required class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-yellow-600">
                    </div>
                    <div>
                        <label class="block text-gray-700 font-semibold mb-2">Tahun *</label>
                        <input type="number" name="tahun" id="edit_tahun" required min="1900" max="<?php echo date('Y') + 1; ?>" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-yellow-600">
                    </div>
                    <div>
                        <label class="block text-gray-700 font-semibold mb-2">Warna *</label>
                        <input type="text" name="warna" id="edit_warna" required class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-yellow-600">
                    </div>
                    <div>
                        <label class="block text-gray-700 font-semibold mb-2">Harga per Hari (Rp) *</label>
                        <input type="number" name="harga_per_hari" id="edit_harga_per_hari" required min="1" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-yellow-600">
                    </div>
                    <div>
                        <label class="block text-gray-700 font-semibold mb-2">Transmisi *</label>
                        <select name="transmisi" id="edit_transmisi" required class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-yellow-600">
                            <option value="">Pilih Transmisi</option>
                            <option value="Manual">Manual</option>
                            <option value="Automatic">Automatic</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-gray-700 font-semibold mb-2">Kapasitas *</label>
                        <input type="number" name="kapasitas" id="edit_kapasitas" required min="1" max="20" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-yellow-600">
                    </div>
                    <div>
                        <label class="block text-gray-700 font-semibold mb-2">Status *</label>
                        <select name="status" id="edit_status" required class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-yellow-600">
                            <option value="tersedia">Tersedia</option>
                            <option value="disewa">Disewa</option>
                        </select>
                    </div>
                </div>
                <div class="mt-4">
                    <label class="block text-gray-700 font-semibold mb-2">Gambar Mobil (Opsional - kosongkan jika tidak ingin mengubah)</label>
                    <input type="file" name="gambar" accept="image/jpeg,image/jpg,image/png" onchange="previewImage(this, 'editPreview')" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-yellow-600">
                    <div id="editPreview" class="mt-2"></div>
                </div>
                <div class="mt-4">
                    <label class="block text-gray-700 font-semibold mb-2">Deskripsi</label>
                    <textarea name="deskripsi" id="edit_deskripsi" rows="3" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-yellow-600"></textarea>
                </div>
                <div class="flex justify-end gap-3 mt-6">
                    <button type="button" onclick="closeEditCarModal()" class="bg-gray-300 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-400 transition">Batal</button>
                    <button type="submit" class="bg-yellow-600 text-white px-6 py-2 rounded-lg hover:bg-yellow-700 transition">
                        <i class="fas fa-save mr-2"></i>Update
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Add Car Modal Functions
        function openAddCarModal() {
            document.getElementById('addCarModal').classList.remove('hidden');
            document.getElementById('addCarForm').reset();
            document.getElementById('addPreview').innerHTML = '';
        }

        function closeAddCarModal() {
            document.getElementById('addCarModal').classList.add('hidden');
        }

        // Edit Car Modal Functions
        function openEditCarModal(carId) {
            fetch('get_car.php?id=' + carId)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const car = data.data;
                        document.getElementById('edit_id').value = car.id;
                        document.getElementById('edit_nama').value = car.nama;
                        document.getElementById('edit_merk').value = car.merk;
                        document.getElementById('edit_tahun').value = car.tahun;
                        document.getElementById('edit_warna').value = car.warna;
                        document.getElementById('edit_harga_per_hari').value = car.harga_per_hari;
                        document.getElementById('edit_transmisi').value = car.transmisi;
                        document.getElementById('edit_kapasitas').value = car.kapasitas;
                        document.getElementById('edit_status').value = car.status;
                        document.getElementById('edit_deskripsi').value = car.deskripsi || '';

                        // Show current image preview
                        const preview = document.getElementById('editPreview');
                        if (car.gambar && car.gambar !== 'default-car.jpg') {
                            preview.innerHTML = `<div class="mb-2 text-xs text-gray-500">Gambar saat ini:</div><img src="uploads/${car.gambar}" class="w-32 h-32 object-cover rounded-lg border-2 border-gray-300">`;
                        } else {
                            preview.innerHTML = '';
                        }
                        document.getElementById('editCarModal').classList.remove('hidden');
                    } else {
                        alert('Gagal memuat data mobil: ' + data.message);
                    }
                })
                .catch(error => {
                    alert('Terjadi kesalahan: ' + error);
                });
        }

        function closeEditCarModal() {
            document.getElementById('editCarModal').classList.add('hidden');
        }

        // Delete Car Function
        function deleteCar(carId) {
            if (confirm('Yakin ingin menghapus mobil ini? Data akan dihapus permanen!')) {
                const formData = new FormData();
                formData.append('id', carId);

                fetch('delete_car.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert('Mobil berhasil dihapus!');
                            location.reload();
                        } else {
                            alert('Gagal menghapus mobil: ' + data.message);
                        }
                    })
                    .catch(error => {
                        alert('Terjadi kesalahan: ' + error);
                    });
            }
        }

        // Preview Image Function
        function previewImage(input, previewId) {
            const preview = document.getElementById(previewId);
            preview.innerHTML = '';

            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.innerHTML = '<img src="' + e.target.result + '" class="w-32 h-32 object-cover rounded-lg border-2 border-gray-300">';
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        // Add Car Form Submit
        document.getElementById('addCarForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);

            fetch('add_car.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Mobil berhasil ditambahkan!');
                        location.reload();
                    } else {
                        alert('Gagal menambahkan mobil: ' + data.message);
                    }
                })
                .catch(error => {
                    alert('Terjadi kesalahan: ' + error);
                });
        });

        // Edit Car Form Submit
        document.getElementById('editCarForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);

            fetch('edit_car.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Mobil berhasil diupdate!');
                        location.reload();
                    } else {
                        alert('Gagal mengupdate mobil: ' + data.message);
                    }
                })
                .catch(error => {
                    alert('Terjadi kesalahan: ' + error);
                });
        });

        // Add Rental Functions
        let availableCars = [];
        let addRentalPricePerDay = 0;

        function openAddRentalModal() {
            // Load available cars
            fetch('get_available_cars.php')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        availableCars = data.data;
                        const carSelect = document.getElementById('add_rental_car_id');
                        carSelect.innerHTML = '<option value="">Pilih Mobil</option>';

                        data.data.forEach(car => {
                            const option = document.createElement('option');
                            option.value = car.id;
                            option.textContent = `${car.nama} (${car.merk})`;
                            option.dataset.price = car.harga_per_hari;
                            carSelect.appendChild(option);
                        });

                        document.getElementById('addRentalForm').reset();
                        document.getElementById('add_rental_car_price').textContent = '';
                        document.getElementById('add_rental_total_hari').value = '';
                        document.getElementById('add_rental_total_harga').value = '';
                        document.getElementById('addRentalModal').classList.remove('hidden');
                    } else {
                        alert('Gagal memuat data mobil: ' + data.message);
                    }
                })
                .catch(error => {
                    alert('Terjadi kesalahan: ' + error);
                });
        }

        function closeAddRentalModal() {
            document.getElementById('addRentalModal').classList.add('hidden');
        }

        function updateAddRentalPrice() {
            const carSelect = document.getElementById('add_rental_car_id');
            const selectedOption = carSelect.options[carSelect.selectedIndex];

            if (selectedOption.value) {
                addRentalPricePerDay = parseFloat(selectedOption.dataset.price);
                document.getElementById('add_rental_car_price').textContent = 'Harga: Rp ' + parseInt(addRentalPricePerDay).toLocaleString('id-ID') + ' / hari';
                calculateAddRentalTotal();
            } else {
                addRentalPricePerDay = 0;
                document.getElementById('add_rental_car_price').textContent = '';
                document.getElementById('add_rental_total_hari').value = '';
                document.getElementById('add_rental_total_harga').value = '';
            }
        }

        function calculateAddRentalTotal() {
            const startDate = new Date(document.getElementById('add_rental_tanggal_mulai').value);
            const endDate = new Date(document.getElementById('add_rental_tanggal_selesai').value);

            if (startDate && endDate && endDate >= startDate && addRentalPricePerDay > 0) {
                const timeDiff = endDate - startDate;
                const daysDiff = Math.ceil(timeDiff / (1000 * 60 * 60 * 24));
                const totalDays = daysDiff > 0 ? daysDiff : 1;
                const totalPrice = addRentalPricePerDay * totalDays;

                document.getElementById('add_rental_total_hari').value = totalDays + ' hari';
                document.getElementById('add_rental_total_harga').value = 'Rp ' + parseInt(totalPrice).toLocaleString('id-ID');
            }
        }

        // Add Rental Form Submit
        document.getElementById('addRentalForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);

            fetch('add_rental.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Rental berhasil ditambahkan!');
                        location.reload();
                    } else {
                        alert('Gagal menambahkan rental: ' + data.message);
                    }
                })
                .catch(error => {
                    alert('Terjadi kesalahan: ' + error);
                });
        });

        // Rental Management Functions
        let rentalPricePerDay = 0;

        // View Rental Detail
        function viewRentalDetail(rentalId) {
            fetch('get_rental.php?id=' + rentalId)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const rental = data.data;
                        const statusColors = {
                            'pending': 'bg-yellow-100 text-yellow-800',
                            'aktif': 'bg-blue-100 text-blue-800',
                            'selesai': 'bg-green-100 text-green-800',
                            'dibatalkan': 'bg-red-100 text-red-800'
                        };

                        const content = `
                            <div class="grid md:grid-cols-2 gap-6">
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <h4 class="font-bold text-gray-800 mb-3 flex items-center">
                                        <i class="fas fa-car text-indigo-600 mr-2"></i>Informasi Mobil
                                    </h4>
                                    <div class="space-y-2 text-sm">
                                        <div><span class="text-gray-600">Nama:</span> <span class="font-semibold">${rental.car_name}</span></div>
                                        <div><span class="text-gray-600">Merk:</span> <span class="font-semibold">${rental.merk}</span></div>
                                        <div><span class="text-gray-600">Harga/Hari:</span> <span class="font-semibold">Rp ${parseInt(rental.harga_per_hari).toLocaleString('id-ID')}</span></div>
                                    </div>
                                </div>
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <h4 class="font-bold text-gray-800 mb-3 flex items-center">
                                        <i class="fas fa-user text-indigo-600 mr-2"></i>Informasi Pelanggan
                                    </h4>
                                    <div class="space-y-2 text-sm">
                                        <div><span class="text-gray-600">Nama:</span> <span class="font-semibold">${rental.customer_name}</span></div>
                                        <div><span class="text-gray-600">Email:</span> <span class="font-semibold">${rental.email}</span></div>
                                        <div><span class="text-gray-600">Telepon:</span> <span class="font-semibold">${rental.telepon}</span></div>
                                        <div><span class="text-gray-600">Alamat:</span> <span class="font-semibold">${rental.alamat || '-'}</span></div>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-6 bg-indigo-50 p-4 rounded-lg">
                                <h4 class="font-bold text-gray-800 mb-3 flex items-center">
                                    <i class="fas fa-calendar-alt text-indigo-600 mr-2"></i>Detail Rental
                                </h4>
                                <div class="grid md:grid-cols-2 gap-4 text-sm">
                                    <div><span class="text-gray-600">Tanggal Mulai:</span> <span class="font-semibold">${new Date(rental.tanggal_mulai).toLocaleDateString('id-ID')}</span></div>
                                    <div><span class="text-gray-600">Tanggal Selesai:</span> <span class="font-semibold">${new Date(rental.tanggal_selesai).toLocaleDateString('id-ID')}</span></div>
                                    <div><span class="text-gray-600">Total Hari:</span> <span class="font-semibold">${rental.total_hari} hari</span></div>
                                    <div><span class="text-gray-600">Total Harga:</span> <span class="font-semibold text-green-600 text-lg">Rp ${parseInt(rental.total_harga).toLocaleString('id-ID')}</span></div>
                                    <div><span class="text-gray-600">Status:</span> <span class="px-2 py-1 rounded-full text-xs font-semibold ${statusColors[rental.status]}">${rental.status.toUpperCase()}</span></div>
                                    <div><span class="text-gray-600">Dibuat:</span> <span class="font-semibold">${new Date(rental.created_at).toLocaleString('id-ID')}</span></div>
                                </div>
                                ${rental.catatan ? `<div class="mt-3"><span class="text-gray-600">Catatan:</span> <p class="font-semibold mt-1">${rental.catatan}</p></div>` : ''}
                            </div>
                        `;

                        document.getElementById('rentalDetailContent').innerHTML = content;
                        document.getElementById('viewRentalModal').classList.remove('hidden');
                    } else {
                        alert('Gagal memuat data rental: ' + data.message);
                    }
                })
                .catch(error => {
                    alert('Terjadi kesalahan: ' + error);
                });
        }

        function closeViewRentalModal() {
            document.getElementById('viewRentalModal').classList.add('hidden');
        }

        // Edit Rental Modal
        function openEditRentalModal(rentalId) {
            fetch('get_rental.php?id=' + rentalId)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const rental = data.data;
                        rentalPricePerDay = parseFloat(rental.harga_per_hari);

                        document.getElementById('rental_edit_id').value = rental.id;
                        document.getElementById('rental_tanggal_mulai').value = rental.tanggal_mulai;
                        document.getElementById('rental_tanggal_selesai').value = rental.tanggal_selesai;
                        document.getElementById('rental_status').value = rental.status;
                        document.getElementById('rental_catatan').value = rental.catatan || '';

                        document.getElementById('rental_car_info').textContent = rental.car_name + ' (' + rental.merk + ')';
                        document.getElementById('rental_price_info').textContent = 'Rp ' + parseInt(rental.harga_per_hari).toLocaleString('id-ID');
                        document.getElementById('rental_customer_info').textContent = rental.customer_name;
                        document.getElementById('rental_phone_info').textContent = rental.telepon;

                        calculateRentalTotal();
                        document.getElementById('editRentalModal').classList.remove('hidden');
                    } else {
                        alert('Gagal memuat data rental: ' + data.message);
                    }
                })
                .catch(error => {
                    alert('Terjadi kesalahan: ' + error);
                });
        }

        function closeEditRentalModal() {
            document.getElementById('editRentalModal').classList.add('hidden');
        }

        function calculateRentalTotal() {
            const startDate = new Date(document.getElementById('rental_tanggal_mulai').value);
            const endDate = new Date(document.getElementById('rental_tanggal_selesai').value);

            if (startDate && endDate && endDate >= startDate) {
                const timeDiff = endDate - startDate;
                const daysDiff = Math.ceil(timeDiff / (1000 * 60 * 60 * 24));
                const totalDays = daysDiff > 0 ? daysDiff : 1;
                const totalPrice = rentalPricePerDay * totalDays;

                document.getElementById('rental_total_hari_display').value = totalDays + ' hari';
                document.getElementById('rental_total_harga_display').value = 'Rp ' + parseInt(totalPrice).toLocaleString('id-ID');
            }
        }

        // Edit Rental Form Submit
        document.getElementById('editRentalForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);

            fetch('edit_rental.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Rental berhasil diupdate!');
                        location.reload();
                    } else {
                        alert('Gagal mengupdate rental: ' + data.message);
                    }
                })
                .catch(error => {
                    alert('Terjadi kesalahan: ' + error);
                });
        });

        // Delete Rental
        function deleteRental(rentalId) {
            if (confirm('Yakin ingin menghapus rental ini? Data akan dihapus permanen!\n\nCatatan: Hanya rental dengan status pending atau dibatalkan yang bisa dihapus.')) {
                const formData = new FormData();
                formData.append('id', rentalId);

                fetch('delete_rental.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert('Rental berhasil dihapus!');
                            location.reload();
                        } else {
                            alert('Gagal menghapus rental: ' + data.message);
                        }
                    })
                    .catch(error => {
                        alert('Terjadi kesalahan: ' + error);
                    });
            }
        }

        // Update Rental Status Function
        function updateStatus(rentalId, newStatus) {
            if (!newStatus) return;

            if (confirm('Yakin ingin mengubah status rental ini?')) {
                fetch('update_rental_status.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: 'rental_id=' + rentalId + '&status=' + newStatus
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert('Status berhasil diupdate!');
                            location.reload();
                        } else {
                            alert('Gagal update status: ' + data.message);
                        }
                    })
                    .catch(error => {
                        alert('Terjadi kesalahan: ' + error);
                    });
            }
        }

        // Laporan Functions
        function generateLaporan() {
            const tanggalMulai = document.getElementById('filter_tanggal_mulai').value;
            const tanggalAkhir = document.getElementById('filter_tanggal_akhir').value;
            const status = document.getElementById('filter_status').value;

            let queryParams = [];
            if (tanggalMulai) queryParams.push('tanggal_mulai=' + tanggalMulai);
            if (tanggalAkhir) queryParams.push('tanggal_akhir=' + tanggalAkhir);
            if (status) queryParams.push('status=' + status);

            fetch('generate_laporan.php' + (queryParams.length > 0 ? '?' + queryParams.join('&') : ''))
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('stat_total_rental').textContent = data.statistics.total_rental;
                        document.getElementById('stat_total_pendapatan').textContent = 'Rp ' + parseInt(data.statistics.total_pendapatan).toLocaleString('id-ID');
                        document.getElementById('stat_rental_aktif').textContent = data.statistics.rental_aktif;
                        document.getElementById('stat_rental_selesai').textContent = data.statistics.rental_selesai;

                        const tbody = document.getElementById('laporanTableBody');
                        tbody.innerHTML = '';

                        if (data.data.length === 0) {
                            tbody.innerHTML = '<tr><td colspan="7" class="px-6 py-8 text-center text-gray-500">Tidak ada data</td></tr>';
                        } else {
                            const statusColors = {
                                'pending': 'bg-yellow-100 text-yellow-800',
                                'aktif': 'bg-blue-100 text-blue-800',
                                'selesai': 'bg-green-100 text-green-800',
                                'dibatalkan': 'bg-red-100 text-red-800'
                            };
                            data.data.forEach((rental, index) => {
                                const row = document.createElement('tr');
                                row.innerHTML = `<td class="px-6 py-4 text-sm">${index + 1}</td><td class="px-6 py-4 text-sm">${new Date(rental.tanggal_mulai).toLocaleDateString('id-ID')} - ${new Date(rental.tanggal_selesai).toLocaleDateString('id-ID')}</td><td class="px-6 py-4 text-sm font-medium">${rental.car_name} (${rental.merk})</td><td class="px-6 py-4 text-sm">${rental.customer_name}</td><td class="px-6 py-4 text-sm">${rental.total_hari} hari</td><td class="px-6 py-4 text-sm font-semibold">Rp ${parseInt(rental.total_harga).toLocaleString('id-ID')}</td><td class="px-6 py-4"><span class="px-2 py-1 rounded-full text-xs font-semibold ${statusColors[rental.status]}">${rental.status.toUpperCase()}</span></td>`;
                                tbody.appendChild(row);
                            });
                        }
                    }
                });
        }

        // Auto-load
        window.addEventListener('DOMContentLoaded', function() {
            const today = new Date();
            const past = new Date(today);
            past.setDate(today.getDate() - 30);
            document.getElementById('filter_tanggal_mulai').value = past.toISOString().split('T')[0];
            document.getElementById('filter_tanggal_akhir').value = today.toISOString().split('T')[0];
            generateLaporan();
        });
    </script>
</body>

</html>