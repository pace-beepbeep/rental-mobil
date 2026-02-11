<?php
require_once 'auth_check.php';
require_once 'config.php';

header('Content-Type: application/json');

if (!isset($_GET['id'])) {
    echo json_encode(['success' => false, 'message' => 'ID mobil tidak ditemukan']);
    exit;
}

$id = intval($_GET['id']);

$query = "SELECT * FROM cars WHERE id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($row = mysqli_fetch_assoc($result)) {
    echo json_encode(['success' => true, 'data' => $row]);
} else {
    echo json_encode(['success' => false, 'message' => 'Mobil tidak ditemukan']);
}

mysqli_stmt_close($stmt);
mysqli_close($conn);
