<?php
require_once 'auth_check.php';
require_once 'config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Validasi input
$required_fields = ['nama', 'merk', 'tahun', 'warna', 'harga_per_hari', 'transmisi', 'kapasitas'];
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
$nama = clean_input($_POST['nama']);
$merk = clean_input($_POST['merk']);
$tahun = intval($_POST['tahun']);
$warna = clean_input($_POST['warna']);
$harga_per_hari = floatval($_POST['harga_per_hari']);
$transmisi = clean_input($_POST['transmisi']);
$kapasitas = intval($_POST['kapasitas']);
$deskripsi = isset($_POST['deskripsi']) ? clean_input($_POST['deskripsi']) : '';
$status = 'tersedia';

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

// Handle upload gambar
$gambar = 'default-car.jpg';

if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
    $file = $_FILES['gambar'];
    $allowed_types = ['image/jpeg', 'image/jpg', 'image/png'];
    $max_size = 5 * 1024 * 1024; // 5MB

    // Validasi tipe file
    if (!in_array($file['type'], $allowed_types)) {
        echo json_encode(['success' => false, 'message' => 'Tipe file harus JPG, JPEG, atau PNG']);
        exit;
    }

    // Validasi ukuran file
    if ($file['size'] > $max_size) {
        echo json_encode(['success' => false, 'message' => 'Ukuran file maksimal 5MB']);
        exit;
    }

    // Generate nama file unik
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $gambar = 'car_' . time() . '_' . uniqid() . '.' . $extension;
    $upload_path = 'uploads/' . $gambar;

    // Upload file
    if (!move_uploaded_file($file['tmp_name'], $upload_path)) {
        echo json_encode(['success' => false, 'message' => 'Gagal mengupload gambar']);
        exit;
    }
}

// Insert ke database
$query = "INSERT INTO cars (nama, merk, tahun, warna, harga_per_hari, status, transmisi, kapasitas, gambar, deskripsi) 
          VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = mysqli_prepare($conn, $query);

// FIX: Menggunakan 'ssisssisss' 
// Transmisi (ke-7) dan Gambar (ke-9) sekarang 's' (string)
mysqli_stmt_bind_param(
    $stmt,
    "ssisssisss",
    $nama,
    $merk,
    $tahun,
    $warna,
    $harga_per_hari,
    $status,
    $transmisi,
    $kapasitas,
    $gambar,
    $deskripsi
);

if (mysqli_stmt_execute($stmt)) {
    echo json_encode(['success' => true, 'message' => 'Mobil berhasil ditambahkan']);
} else {
    // Hapus gambar jika insert gagal
    if ($gambar !== 'default-car.jpg' && file_exists('uploads/' . $gambar)) {
        unlink('uploads/' . $gambar);
    }
    echo json_encode(['success' => false, 'message' => 'Gagal menambahkan mobil: ' . mysqli_error($conn)]);
}

mysqli_stmt_close($stmt);
mysqli_close($conn);
