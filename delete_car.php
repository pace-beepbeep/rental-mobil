<?php
require_once 'auth_check.php';
require_once 'config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

if (!isset($_POST['id'])) {
    echo json_encode(['success' => false, 'message' => 'ID mobil tidak ditemukan']);
    exit;
}

$id = intval($_POST['id']);

// Cek apakah mobil sedang/pernah dirental
$query_rental = "SELECT COUNT(*) as count FROM rentals WHERE car_id = ? AND status IN ('pending', 'aktif')";
$stmt = mysqli_prepare($conn, $query_rental);
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$rental_count = mysqli_fetch_assoc($result)['count'];
mysqli_stmt_close($stmt);

if ($rental_count > 0) {
    echo json_encode(['success' => false, 'message' => 'Mobil tidak dapat dihapus karena sedang dalam proses rental']);
    exit;
}

// Ambil data mobil untuk mendapatkan nama gambar
$query = "SELECT gambar FROM cars WHERE id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$car = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$car) {
    echo json_encode(['success' => false, 'message' => 'Mobil tidak ditemukan']);
    exit;
}

// Hapus mobil dari database
$query_delete = "DELETE FROM cars WHERE id = ?";
$stmt = mysqli_prepare($conn, $query_delete);
mysqli_stmt_bind_param($stmt, "i", $id);

if (mysqli_stmt_execute($stmt)) {
    // Hapus gambar jika bukan default
    if ($car['gambar'] !== 'default-car.jpg' && file_exists('uploads/' . $car['gambar'])) {
        unlink('uploads/' . $car['gambar']);
    }

    echo json_encode(['success' => true, 'message' => 'Mobil berhasil dihapus']);
} else {
    echo json_encode(['success' => false, 'message' => 'Gagal menghapus mobil: ' . mysqli_error($conn)]);
}

mysqli_stmt_close($stmt);
mysqli_close($conn);
