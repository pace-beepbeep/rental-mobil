<?php
require_once 'auth_check.php';
require_once 'config.php';

// Set default date range (last 30 days)
$default_end = date('Y-m-d');
$default_start = date('Y-m-d', strtotime('-30 days'));
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Rental Selesai - Admin Panel</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @media print {
            .no-print {
                display: none !important;
            }

            body {
                background: white;
            }
        }
    </style>
</head>

<body class="bg-gray-50">
    <header class="bg-white shadow-md sticky top-0 z-40 no-print">
        <div class="container mx-auto px-6 py-4">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Laporan <span class="text-indigo-600">Rental Selesai</span></h1>
                </div>
                <div class="flex items-center space-x-6">
                    <span class="text-gray-600"><i class="fas fa-user mr-2"></i><?php echo $_SESSION['admin_nama']; ?></span>
                    <a href="admin.php" class="text-gray-600 hover:text-indigo-600 transition">
                        <i class="fas fa-arrow-left mr-2"></i>Kembali ke Admin
                    </a>
                    <a href="logout.php" class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600 transition">
                        <i class="fas fa-sign-out-alt mr-2"></i>Logout
                    </a>
                </div>
            </div>
        </div>
    </header>

    <section class="container mx-auto px-6 py-8">
        <div class="bg-white rounded-xl shadow-lg p-6 mb-6 no-print">
            <h4 class="font-bold text-gray-800 mb-4"><i class="fas fa-filter mr-2 text-indigo-600"></i>Filter Laporan</h4>
            <form id="filterLaporanForm" class="grid md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-gray-700 font-semibold mb-2">Tanggal Mulai</label>
                    <input type="date" id="filter_tanggal_mulai" value="<?php echo $default_start; ?>" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-600">
                </div>
                <div>
                    <label class="block text-gray-700 font-semibold mb-2">Tanggal Akhir</label>
                    <input type="date" id="filter_tanggal_akhir" value="<?php echo $default_end; ?>" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-600">
                </div>
                <div>
                    <label class="block text-gray-700 font-semibold mb-2">Mobil</label>
                    <select id="filter_car" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-600">
                        <option value="">Semua Mobil</option>
                        <?php
                        $cars_query = mysqli_query($conn, "SELECT id, nama, merk FROM cars ORDER BY nama");
                        while ($car = mysqli_fetch_assoc($cars_query)) {
                            echo "<option value='{$car['id']}'>{$car['nama']} ({$car['merk']})</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="flex items-end gap-2">
                    <button type="button" onclick="generateLaporan()" class="bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700 transition flex-1">
                        <i class="fas fa-search mr-2"></i>Tampilkan
                    </button>
                    <button type="button" onclick="window.print()" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition">
                        <i class="fas fa-print"></i>
                    </button>
                </div>
            </form>
        </div>

        <div id="statistikLaporan" class="grid md:grid-cols-3 gap-6 mb-6">
            <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-lg p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-green-100 text-sm">Total Rental Selesai</p>
                        <p id="stat_total_rental" class="text-3xl font-bold mt-2">-</p>
                    </div>
                    <div class="bg-white bg-opacity-20 rounded-full p-4">
                        <i class="fas fa-check-circle text-2xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-blue-100 text-sm">Total Pendapatan</p>
                        <p id="stat_total_pendapatan" class="text-2xl font-bold mt-2">-</p>
                    </div>
                    <div class="bg-white bg-opacity-20 rounded-full p-4">
                        <i class="fas fa-money-bill-wave text-2xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl shadow-lg p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-purple-100 text-sm">Total Hari Sewa</p>
                        <p id="stat_total_hari" class="text-3xl font-bold mt-2">-</p>
                    </div>
                    <div class="bg-white bg-opacity-20 rounded-full p-4">
                        <i class="fas fa-calendar-alt text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="p-6 border-b">
                <h4 class="font-bold text-gray-800"><i class="fas fa-table mr-2 text-indigo-600"></i>Detail Rental Selesai</h4>
                <p class="text-sm text-gray-600 mt-1">Hanya menampilkan rental yang telah diselesaikan.</p>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal Rental</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Mobil</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pelanggan</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Durasi</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total Harga</th>
                        </tr>
                    </thead>
                    <tbody id="laporanTableBody" class="bg-white divide-y divide-gray-200">
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                                <i class="fas fa-spinner fa-spin text-3xl mb-2"></i>
                                <p>Memuat data...</p>
                            </td>
                        </tr>
                    </tbody>
                    <tfoot id="laporanTableFoot" class="bg-gray-100 font-bold hidden">
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-right">TOTAL:</td>
                            <td id="footer_total_hari" class="px-6 py-4">-</td>
                            <td id="footer_total_pendapatan" class="px-6 py-4">-</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </section>

    <script>
        function generateLaporan() {
            // Ambil elemen berdasarkan ID
            const tanggalMulai = document.getElementById('filter_tanggal_mulai').value;
            const tanggalAkhir = document.getElementById('filter_tanggal_akhir').value;
            const carId = document.getElementById('filter_car').value;

            // PERBAIKAN: Hapus baris yang mencari 'filter_status' karena elemennya sudah dihapus
            // const status = document.getElementById('filter_status').value; <-- INI PENYEBAB ERROR

            // Build Query Params
            let queryParams = [];
            if (tanggalMulai) queryParams.push('tanggal_mulai=' + tanggalMulai);
            if (tanggalAkhir) queryParams.push('tanggal_akhir=' + tanggalAkhir);
            if (carId) queryParams.push('car_id=' + carId);

            fetch('generate_laporan.php?' + queryParams.join('&'))
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.text().then(text => {
                        try {
                            return JSON.parse(text);
                        } catch (e) {
                            throw new Error('Format Data Salah. Cek Console.');
                        }
                    });
                })
                .then(data => {
                    if (data.success) {
                        // Update statistics
                        document.getElementById('stat_total_rental').textContent = data.statistics.total_rental;
                        document.getElementById('stat_total_pendapatan').textContent = 'Rp ' + parseInt(data.statistics.total_pendapatan).toLocaleString('id-ID');

                        // Calculate total days
                        const totalHari = data.data.reduce((sum, rental) => sum + parseInt(rental.total_hari), 0);
                        document.getElementById('stat_total_hari').textContent = totalHari;

                        // Update table
                        const tbody = document.getElementById('laporanTableBody');
                        const tfoot = document.getElementById('laporanTableFoot');
                        tbody.innerHTML = '';

                        if (data.data.length === 0) {
                            tbody.innerHTML = '<tr><td colspan="7" class="px-6 py-8 text-center text-gray-500"><i class="fas fa-inbox text-3xl mb-2"></i><p>Tidak ada data rental selesai untuk periode ini</p></td></tr>';
                            tfoot.classList.add('hidden');
                        } else {
                            data.data.forEach((rental, index) => {
                                const row = document.createElement('tr');
                                row.className = 'hover:bg-gray-50';
                                row.innerHTML = `
                                    <td class="px-6 py-4 text-sm text-gray-900">${index + 1}</td>
                                    <td class="px-6 py-4 text-sm text-gray-500">${new Date(rental.tanggal_mulai).toLocaleDateString('id-ID')} - ${new Date(rental.tanggal_selesai).toLocaleDateString('id-ID')}</td>
                                    <td class="px-6 py-4 text-sm font-medium text-gray-900">${rental.car_name} (${rental.merk})</td>
                                    <td class="px-6 py-4 text-sm text-gray-900">${rental.customer_name}</td>
                                    <td class="px-6 py-4">
                                        <span class="px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                            SELESAI
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-900">${rental.total_hari} hari</td>
                                    <td class="px-6 py-4 text-sm font-semibold text-green-600">Rp ${parseInt(rental.total_harga).toLocaleString('id-ID')}</td>
                                `;
                                tbody.appendChild(row);
                            });

                            // Show and update footer
                            document.getElementById('footer_total_hari').textContent = totalHari + ' hari';
                            document.getElementById('footer_total_pendapatan').textContent = 'Rp ' + parseInt(data.statistics.total_pendapatan).toLocaleString('id-ID');
                            tfoot.classList.remove('hidden');
                        }
                    } else {
                        alert('Gagal memuat data: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error(error);
                    document.getElementById('laporanTableBody').innerHTML = `<tr><td colspan="7" class="text-center py-4 text-red-500">Error: ${error.message}</td></tr>`;
                });
        }

        // Auto-load on page load
        window.addEventListener('DOMContentLoaded', function() {
            generateLaporan();
        });
    </script>
</body>

</html>