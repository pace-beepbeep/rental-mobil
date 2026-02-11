<?php
require_once 'auth_check.php';
require_once 'config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Validasi input rental
$required_fields = ['car_id', 'tanggal_mulai', 'tanggal_selesai'];
$errors = [];

foreach ($required_fields as $field) {
    if (empty($_POST[$field])) {
        $errors[] = "Field $field harus diisi";
    }
}

// Validasi customer
$customer_required = ['customer_name', 'customer_email', 'customer_phone'];
foreach ($customer_required as $field) {
    if (empty($_POST[$field])) {
        $errors[] = "Field $field harus diisi";
    }
}

if (!empty($errors)) {
    echo json_encode(['success' => false, 'message' => implode(', ', $errors)]);
    exit;
}

$car_id = intval($_POST['car_id']);
$tanggal_mulai = clean_input($_POST['tanggal_mulai']);
$tanggal_selesai = clean_input($_POST['tanggal_selesai']);
$catatan = isset($_POST['catatan']) ? clean_input($_POST['catatan']) : '';

// Data customer
$customer_name = clean_input($_POST['customer_name']);
$customer_email = clean_input($_POST['customer_email']);
$customer_phone = clean_input($_POST['customer_phone']);
$customer_address = isset($_POST['customer_address']) ? clean_input($_POST['customer_address']) : '';

// Val idasi tanggal
$date_mulai = new DateTime($tanggal_mulai);
$date_selesai = new DateTime($tanggal_selesai);

if ($date_selesai < $date_mulai) {
    echo json_encode(['success' => false, 'message' => 'Tanggal selesai harus setelah tanggal mulai']);
    exit;
}

// Hitung total hari
$interval = $date_mulai->diff($date_selesai);
$total_hari = $interval->days > 0 ? $interval->days : 1;

// Cek mobil exists dan ambil harga
$query_car = "SELECT harga_per_hari, status FROM cars WHERE id = ?";
$stmt = mysqli_prepare($conn, $query_car);
mysqli_stmt_bind_param($stmt, "i", $car_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$car = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$car) {
    echo json_encode(['success' => false, 'message' => 'Mobil tidak ditemukan']);
    exit;
}

// Hitung total harga
$total_harga = $car['harga_per_hari'] * $total_hari;

// Cek apakah customer sudah ada berdasarkan email
$query_check_customer = "SELECT id FROM customers WHERE email = ?";
$stmt = mysqli_prepare($conn, $query_check_customer);
mysqli_stmt_bind_param($stmt, "s", $customer_email);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$existing_customer = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if ($existing_customer) {
    $customer_id = $existing_customer['id'];

    // Update data customer jika ada perubahan
    $query_update_customer = "UPDATE customers SET nama=?, telepon=?, alamat=? WHERE id=?";
    $stmt = mysqli_prepare($conn, $query_update_customer);
    mysqli_stmt_bind_param($stmt, "sssi", $customer_name, $customer_phone, $customer_address, $customer_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
} else {
    // Insert customer baru
    $query_insert_customer = "INSERT INTO customers (nama, email, telepon, alamat) VALUES (?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $query_insert_customer);
    mysqli_stmt_bind_param($stmt, "ssss", $customer_name, $customer_email, $customer_phone, $customer_address);

    if (!mysqli_stmt_execute($stmt)) {
        echo json_encode(['success' => false, 'message' => 'Gagal menambahkan customer: ' . mysqli_error($conn)]);
        mysqli_stmt_close($stmt);
        exit;
    }

    $customer_id = mysqli_insert_id($conn);
    mysqli_stmt_close($stmt);
}

// Insert rental
$status = 'pending';
$query_rental = "INSERT INTO rentals (car_id, customer_id, tanggal_mulai, tanggal_selesai, total_hari, total_harga, status, catatan) 
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
$stmt = mysqli_prepare($conn, $query_rental);
mysqli_stmt_bind_param($stmt, "iissidss", $car_id, $customer_id, $tanggal_mulai, $tanggal_selesai, $total_hari, $total_harga, $status, $catatan);

if (mysqli_stmt_execute($stmt)) {
    // Update status mobil  jadi disewa
    $query_update_car = "UPDATE cars SET status = 'disewa' WHERE id = ?";
    $stmt_car = mysqli_prepare($conn, $query_update_car);
    mysqli_stmt_bind_param($stmt_car, "i", $car_id);
    mysqli_stmt_execute($stmt_car);
    mysqli_stmt_close($stmt_car);

    echo json_encode(['success' => true, 'message' => 'Rental berhasil ditambahkan']);
} else {
    echo json_encode(['success' => false, 'message' => 'Gagal menambahkan rental: ' . mysqli_error($conn)]);
}

mysqli_stmt_close($stmt);
mysqli_close($conn);
