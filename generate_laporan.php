<?php
require_once 'auth_check.php';
require_once 'config.php';

header('Content-Type: application/json');

// Get filter parameters
$tanggal_mulai = isset($_GET['tanggal_mulai']) ? clean_input($_GET['tanggal_mulai']) : '';
$tanggal_akhir = isset($_GET['tanggal_akhir']) ? clean_input($_GET['tanggal_akhir']) : '';
$status = 'selesai'; // Always filter for completed rentals only
$car_id = isset($_GET['car_id']) ? intval($_GET['car_id']) : 0;

// Build query
$query = "SELECT r.*, c.nama as car_name, c.merk, cu.nama as customer_name 
          FROM rentals r 
          JOIN cars c ON r.car_id = c.id 
          JOIN customers cu ON r.customer_id = cu.id 
          WHERE 1=1";

$params = [];
$types = '';

if ($tanggal_mulai) {
    $query .= " AND r.tanggal_mulai >= ?";
    $params[] = $tanggal_mulai;
    $types .= 's';
}

if ($tanggal_akhir) {
    $query .= " AND r.tanggal_selesai <= ?";
    $params[] = $tanggal_akhir;
    $types .= 's';
}

if ($status) {
    $query .= " AND r.status = ?";
    $params[] = $status;
    $types .= 's';
}

if ($car_id > 0) {
    $query .= " AND r.car_id = ?";
    $params[] = $car_id;
    $types .= 'i';
}

$query .= " ORDER BY r.created_at DESC";

// Execute query
if (!empty($params)) {
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, $types, ...$params);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
} else {
    $result = mysqli_query($conn, $query);
}

// Fetch all rentals
$rentals = [];
$total_rental = 0;
$total_pendapatan = 0;

while ($row = mysqli_fetch_assoc($result)) {
    $rentals[] = $row;
    $total_rental++;
    $total_pendapatan += $row['total_harga'];
}

// Prepare response
$response = [
    'success' => true,
    'data' => $rentals,
    'statistics' => [
        'total_rental' => $total_rental,
        'total_pendapatan' => $total_pendapatan
    ]
];

echo json_encode($response);

if (!empty($params)) {
    mysqli_stmt_close($stmt);
}
mysqli_close($conn);
