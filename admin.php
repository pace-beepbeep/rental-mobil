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
                <button onclick="alert('Fitur tambah mobil akan segera hadir!')" class="bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700 transition">
                    <i class="fas fa-plus mr-2"></i> Tambah Mobil
                </button>
            </div>

            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
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
                                        <a href="#" onclick="alert('Fitur edit akan segera hadir!')" class="text-yellow-600 hover:text-yellow-900 mr-3">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="#" onclick="return confirm('Yakin ingin menghapus?')" class="text-red-600 hover:text-red-900">
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
            <h3 class="text-2xl font-bold text-gray-800 mb-6">Daftar Rental</h3>

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
                                        <select onchange="updateStatus(<?php echo $rental['id']; ?>, this.value)" class="border border-gray-300 rounded px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-600">
                                            <option value="">Ubah Status</option>
                                            <option value="pending" <?php echo $rental['status'] == 'pending' ? 'disabled' : ''; ?>>Pending</option>
                                            <option value="aktif" <?php echo $rental['status'] == 'aktif' ? 'disabled' : ''; ?>>Aktif</option>
                                            <option value="selesai" <?php echo $rental['status'] == 'selesai' ? 'disabled' : ''; ?>>Selesai</option>
                                            <option value="dibatalkan" <?php echo $rental['status'] == 'dibatalkan' ? 'disabled' : ''; ?>>Dibatalkan</option>
                                        </select>
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

    <script>
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
    </script>
</body>

</html>