<?php
require_once 'auth_check.php';
require_once 'config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Validasi input
$required_fields = ['id', 'tanggal_mulai', 'tanggal_selesai', 'status'];
$errors = [];

foreach ($required_fields as $field) {
    if (empty($_POST[$field])) {
        $errors[] = "Field $field harus diisi";
    }
}

if (!empty($errors)) {
    echo json_encode(['success' => false, 'message' => implode(', ', $errors)]);
    exit;
}

$id = intval($_POST['id']);
$tanggal_mulai = clean_input($_POST['tanggal_mulai']);
$tanggal_selesai = clean_input($_POST['tanggal_selesai']);
$status = clean_input($_POST['status']);
$catatan = isset($_POST['catatan']) ? clean_input($_POST['catatan']) : '';

// Validasi tanggal
$date_mulai = new DateTime($tanggal_mulai);
$date_selesai = new DateTime($tanggal_selesai);

if ($date_selesai < $date_mulai) {
    echo json_encode(['success' => false, 'message' => 'Tanggal selesai harus setelah tanggal mulai']);
    exit;
}

// Hitung total hari
$interval = $date_mulai->diff($date_selesai);
$total_hari = $interval->days > 0 ? $interval->days : 1;

// Ambil data rental lama untuk mendapatkan harga
$query = "SELECT r.car_id, c.harga_per_hari, r.status as old_status 
          FROM rentals r 
          JOIN cars c ON r.car_id = c.id 
          WHERE r.id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$old_rental = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$old_rental) {
    echo json_encode(['success' => false, 'message' => 'Rental tidak ditemukan']);
    exit;
}

// Hitung total harga
$total_harga = $old_rental['harga_per_hari'] * $total_hari;

// Validasi status
$allowed_status = ['pending', 'aktif', 'selesai', 'dibatalkan'];
if (!in_array($status, $allowed_status)) {
    echo json_encode(['success' => false, 'message' => 'Status tidak valid']);
    exit;
}

// Update status mobil jika status rental berubah
$old_status = $old_rental['old_status'];
$car_id = $old_rental['car_id'];

// Logika update status mobil
if ($old_status != $status) {
    if (in_array($status, ['selesai', 'dibatalkan'])) {
        // Jika rental selesai/dibatalkan, mobil jadi tersedia
        $update_car = "UPDATE cars SET status = 'tersedia' WHERE id = ?";
        $stmt = mysqli_prepare($conn, $update_car);
        mysqli_stmt_bind_param($stmt, "i", $car_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    } elseif (in_array($status, ['aktif', 'pending']) && $old_status == 'selesai') {
        // Jika dari selesai ke aktif/pending, mobil jadi disewa
        $update_car = "UPDATE cars SET status = 'disewa' WHERE id = ?";
        $stmt = mysqli_prepare($conn, $update_car);
        mysqli_stmt_bind_param($stmt, "i", $car_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }
}

// Update rental
$query_update = "UPDATE rentals SET tanggal_mulai=?, tanggal_selesai=?, total_hari=?, total_harga=?, status=?, catatan=? WHERE id=?";
$stmt = mysqli_prepare($conn, $query_update);
mysqli_stmt_bind_param($stmt, "ssidssi", $tanggal_mulai, $tanggal_selesai, $total_hari, $total_harga, $status, $catatan, $id);

if (mysqli_stmt_execute($stmt)) {
    echo json_encode(['success' => true, 'message' => 'Rental berhasil diupdate']);
} else {
    echo json_encode(['success' => false, 'message' => 'Gagal mengupdate rental: ' . mysqli_error($conn)]);
}

mysqli_stmt_close($stmt);
mysqli_close($conn);
