<?php
// Tahan output agar tidak ada teks error yang merusak JSON
ob_start();

require_once 'auth_check.php';
require_once 'config.php';

// Matikan display error agar tidak muncul di response
ini_set('display_errors', 0);
error_reporting(E_ALL);

header('Content-Type: application/json');

try {
    // Get filter parameters
    $tanggal_mulai = isset($_GET['tanggal_mulai']) ? clean_input($_GET['tanggal_mulai']) : '';
    $tanggal_akhir = isset($_GET['tanggal_akhir']) ? clean_input($_GET['tanggal_akhir']) : '';
    $car_id = isset($_GET['car_id']) ? intval($_GET['car_id']) : 0;

    // Build query
    // PERBAIKAN 1: Hardcode status = 'selesai'
    $query = "SELECT r.*, c.nama as car_name, c.merk, cu.nama as customer_name 
              FROM rentals r 
              JOIN cars c ON r.car_id = c.id 
              JOIN customers cu ON r.customer_id = cu.id 
              WHERE r.status = 'selesai'";

    $params = [];
    $types = '';

    // PERBAIKAN 2: Filter berdasarkan created_at (Tanggal Transaksi)
    if ($tanggal_mulai) {
        // Menggunakan fungsi DATE() untuk mengambil bagian tanggal saja dari timestamp created_at
        $query .= " AND DATE(r.created_at) >= ?";
        $params[] = $tanggal_mulai;
        $types .= 's';
    }

    if ($tanggal_akhir) {
        $query .= " AND DATE(r.created_at) <= ?";
        $params[] = $tanggal_akhir;
        $types .= 's';
    }

    if ($car_id > 0) {
        $query .= " AND r.car_id = ?";
        $params[] = $car_id;
        $types .= 'i';
    }

    // Urutkan dari yang transaksi terbaru
    $query .= " ORDER BY r.created_at DESC";

    // Execute query
    if (!empty($params)) {
        $stmt = mysqli_prepare($conn, $query);
        if (!$stmt) {
            throw new Exception("Query Error: " . mysqli_error($conn));
        }
        mysqli_stmt_bind_param($stmt, $types, ...$params);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
    } else {
        $result = mysqli_query($conn, $query);
        if (!$result) {
            throw new Exception("Query Error: " . mysqli_error($conn));
        }
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

    ob_end_clean();
    echo json_encode($response);

    if (!empty($params) && isset($stmt)) {
        mysqli_stmt_close($stmt);
    }
    mysqli_close($conn);
} catch (Exception $e) {
    ob_end_clean();
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
