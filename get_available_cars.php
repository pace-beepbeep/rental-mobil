<?php
require_once 'auth_check.php';
require_once 'config.php';

header('Content-Type: application/json');

// Ambil mobil yang tersedia
$query = "SELECT id, nama, merk, harga_per_hari FROM cars WHERE status = 'tersedia' ORDER BY nama";
$result = mysqli_query($conn, $query);

$cars = [];
while ($row = mysqli_fetch_assoc($result)) {
    $cars[] = $row;
}

echo json_encode(['success' => true, 'data' => $cars]);
mysqli_close($conn);
