<?php
require_once 'auth_check.php';
require_once 'config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

if (!isset($_POST['id'])) {
    echo json_encode(['success' => false, 'message' => 'ID rental tidak ditemukan']);
    exit;
}

$id = intval($_POST['id']);

// Ambil data rental
$query = "SELECT r.*, r.status FROM rentals r WHERE r.id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$rental = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$rental) {
    echo json_encode(['success' => false, 'message' => 'Rental tidak ditemukan']);
    exit;
}

// Hanya bisa hapus jika status pending atau dibatalkan
if (!in_array($rental['status'], ['pending', 'dibatalkan'])) {
    echo json_encode(['success' => false, 'message' => 'Hanya rental dengan status pending atau dibatalkan yang bisa dihapus']);
    exit;
}

// Update status mobil menjadi tersedia jika rental pending
if ($rental['status'] == 'pending') {
    $update_car = "UPDATE cars SET status = 'tersedia' WHERE id = ?";
    $stmt = mysqli_prepare($conn, $update_car);
    mysqli_stmt_bind_param($stmt, "i", $rental['car_id']);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}

// Hapus rental
$query_delete = "DELETE FROM rentals WHERE id = ?";
$stmt = mysqli_prepare($conn, $query_delete);
mysqli_stmt_bind_param($stmt, "i", $id);

if (mysqli_stmt_execute($stmt)) {
    echo json_encode(['success' => true, 'message' => 'Rental berhasil dihapus']);
} else {
    echo json_encode(['success' => false, 'message' => 'Gagal menghapus rental: ' . mysqli_error($conn)]);
}

mysqli_stmt_close($stmt);
mysqli_close($conn);
