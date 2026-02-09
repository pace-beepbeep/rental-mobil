<?php
require_once 'auth_check.php';
require_once 'config.php';

// Cek apakah request adalah POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $rental_id = (int)$_POST['rental_id'];
    $new_status = clean_input($_POST['status']);

    // Validasi status
    $valid_statuses = ['pending', 'aktif', 'selesai', 'dibatalkan'];
    if (!in_array($new_status, $valid_statuses)) {
        echo json_encode(['success' => false, 'message' => 'Status tidak valid']);
        exit;
    }

    // Ambil data rental
    $query = "SELECT * FROM rentals WHERE id = $rental_id";
    $result = mysqli_query($conn, $query);
    $rental = mysqli_fetch_assoc($result);

    if (!$rental) {
        echo json_encode(['success' => false, 'message' => 'Rental tidak ditemukan']);
        exit;
    }

    // Update status rental
    $query_update = "UPDATE rentals SET status = '$new_status' WHERE id = $rental_id";

    if (mysqli_query($conn, $query_update)) {
        // Jika status selesai atau dibatalkan, update status mobil jadi tersedia
        if ($new_status == 'selesai' || $new_status == 'dibatalkan') {
            $query_car = "UPDATE cars SET status = 'tersedia' WHERE id = " . $rental['car_id'];
            mysqli_query($conn, $query_car);
        }
        // Jika status aktif, update status mobil jadi disewa
        elseif ($new_status == 'aktif') {
            $query_car = "UPDATE cars SET status = 'disewa' WHERE id = " . $rental['car_id'];
            mysqli_query($conn, $query_car);
        }

        echo json_encode(['success' => true, 'message' => 'Status berhasil diupdate']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Gagal update status']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}
