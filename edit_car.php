<?php
require_once 'auth_check.php';
require_once 'config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Validasi input
$required_fields = ['id', 'nama', 'merk', 'tahun', 'warna', 'harga_per_hari', 'transmisi', 'kapasitas', 'status'];
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

// Ambil data dari form
$id = intval($_POST['id']);
$nama = clean_input($_POST['nama']);
$merk = clean_input($_POST['merk']);
$tahun = intval($_POST['tahun']);
$warna = clean_input($_POST['warna']);
$harga_per_hari = floatval($_POST['harga_per_hari']);
$transmisi = clean_input($_POST['transmisi']);
$kapasitas = intval($_POST['kapasitas']);
$status = clean_input($_POST['status']);
$deskripsi = isset($_POST['deskripsi']) ? clean_input($_POST['deskripsi']) : '';

// Validasi tahun
if ($tahun < 1900 || $tahun > date('Y') + 1) {
    echo json_encode(['success' => false, 'message' => 'Tahun tidak valid']);
    exit;
}

// Validasi harga
if ($harga_per_hari <= 0) {
    echo json_encode(['success' => false, 'message' => 'Harga harus lebih dari 0']);
    exit;
}

// Validasi kapasitas
if ($kapasitas <= 0 || $kapasitas > 20) {
    echo json_encode(['success' => false, 'message' => 'Kapasitas tidak valid']);
    exit;
}

// Validasi status
$allowed_status = ['tersedia', 'disewa'];
if (!in_array($status, $allowed_status)) {
    echo json_encode(['success' => false, 'message' => 'Status tidak valid']);
    exit;
}

// Ambil data mobil lama
$query = "SELECT gambar FROM cars WHERE id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$old_car = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$old_car) {
    echo json_encode(['success' => false, 'message' => 'Mobil tidak ditemukan']);
    exit;
}

$gambar = $old_car['gambar'];

// Handle upload gambar baru
if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
    $file = $_FILES['gambar'];
    $allowed_types = ['image/jpeg', 'image/jpg', 'image/png'];
    $max_size = 2 * 1024 * 1024; // 2MB

    // Validasi tipe file
    if (!in_array($file['type'], $allowed_types)) {
        echo json_encode(['success' => false, 'message' => 'Tipe file harus JPG, JPEG, atau PNG']);
        exit;
    }

    // Validasi ukuran file
    if ($file['size'] > $max_size) {
        echo json_encode(['success' => false, 'message' => 'Ukuran file maksimal 2MB']);
        exit;
    }

    // Generate nama file unik
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $new_gambar = 'car_' . time() . '_' . uniqid() . '.' . $extension;
    $upload_path = 'uploads/' . $new_gambar;

    // Upload file
    if (move_uploaded_file($file['tmp_name'], $upload_path)) {
        // Hapus gambar lama jika bukan default
        if ($gambar !== 'default-car.jpg' && file_exists('uploads/' . $gambar)) {
            unlink('uploads/' . $gambar);
        }
        $gambar = $new_gambar;
    } else {
        echo json_encode(['success' => false, 'message' => 'Gagal mengupload gambar']);
        exit;
    }
}

// Update database
$query = "UPDATE cars SET nama=?, merk=?, tahun=?, warna=?, harga_per_hari=?, status=?, transmisi=?, kapasitas=?, gambar=?, deskripsi=? WHERE id=?";

$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "ssisssisisi", $nama, $merk, $tahun, $warna, $harga_per_hari, $status, $transmisi, $kapasitas, $gambar, $deskripsi, $id);

if (mysqli_stmt_execute($stmt)) {
    echo json_encode(['success' => true, 'message' => 'Mobil berhasil diupdate']);
} else {
    echo json_encode(['success' => false, 'message' => 'Gagal mengupdate mobil: ' . mysqli_error($conn)]);
}

mysqli_stmt_close($stmt);
mysqli_close($conn);
