<?php
require_once 'auth_check.php';
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Ambil data dari form
    $car_id = clean_input($_POST['car_id']);
    $nama = clean_input($_POST['nama']);
    $email = clean_input($_POST['email']);
    $telepon = clean_input($_POST['telepon']);
    $alamat = clean_input($_POST['alamat']);
    $tanggal_mulai = clean_input($_POST['tanggal_mulai']);
    $tanggal_selesai = clean_input($_POST['tanggal_selesai']);
    $catatan = clean_input($_POST['catatan']);

    // Validasi tanggal
    $start = new DateTime($tanggal_mulai);
    $end = new DateTime($tanggal_selesai);
    $interval = $start->diff($end);
    $total_hari = $interval->days;

    if ($total_hari <= 0) {
        echo "<script>alert('Tanggal tidak valid!'); window.history.back();</script>";
        exit;
    }

    // Ambil harga mobil
    $query_car = "SELECT harga_per_hari, status FROM cars WHERE id = $car_id";
    $result_car = mysqli_query($conn, $query_car);
    $car = mysqli_fetch_assoc($result_car);

    if (!$car || $car['status'] != 'tersedia') {
        echo "<script>alert('Mobil tidak tersedia!'); window.location.href='index.php';</script>";
        exit;
    }

    $total_harga = $total_hari * $car['harga_per_hari'];

    // Mulai transaksi
    mysqli_begin_transaction($conn);

    try {
        // Insert customer
        $query_customer = "INSERT INTO customers (nama, email, telepon, alamat) VALUES ('$nama', '$email', '$telepon', '$alamat')";
        mysqli_query($conn, $query_customer);
        $customer_id = mysqli_insert_id($conn);

        // Insert rental
        $query_rental = "INSERT INTO rentals (car_id, customer_id, tanggal_mulai, tanggal_selesai, total_hari, total_harga, status, catatan) 
                        VALUES ($car_id, $customer_id, '$tanggal_mulai', '$tanggal_selesai', $total_hari, $total_harga, 'pending', '$catatan')";
        mysqli_query($conn, $query_rental);

        // Update status mobil
        $query_update = "UPDATE cars SET status = 'disewa' WHERE id = $car_id";
        mysqli_query($conn, $query_update);

        // Commit transaksi
        mysqli_commit($conn);

        // Redirect dengan pesan sukses
        echo "<!DOCTYPE html>
        <html lang='id'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>Booking Berhasil</title>
            <script src='https://cdn.tailwindcss.com'></script>
            <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css'>
        </head>
        <body class='bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen flex items-center justify-center'>
            <div class='bg-white rounded-xl shadow-2xl p-8 max-w-md text-center'>
                <div class='bg-green-100 rounded-full w-20 h-20 flex items-center justify-center mx-auto mb-6'>
                    <i class='fas fa-check-circle text-green-500 text-5xl'></i>
                </div>
                <h2 class='text-3xl font-bold text-gray-800 mb-4'>Booking Berhasil!</h2>
                <p class='text-gray-600 mb-6'>Terima kasih, <strong>$nama</strong>. Booking Anda telah berhasil diproses.</p>
                <div class='bg-gray-50 p-4 rounded-lg mb-6 text-left'>
                    <p class='text-sm text-gray-600 mb-2'><strong>Total Hari:</strong> $total_hari hari</p>
                    <p class='text-sm text-gray-600 mb-2'><strong>Total Harga:</strong> " . format_rupiah($total_harga) . "</p>
                    <p class='text-sm text-gray-600'><strong>Status:</strong> <span class='text-yellow-600 font-semibold'>Pending</span></p>
                </div>
                <p class='text-sm text-gray-500 mb-6'>Kami akan menghubungi Anda melalui email atau telepon untuk konfirmasi lebih lanjut.</p>
                <a href='index.php' class='inline-block bg-indigo-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-indigo-700 transition'>
                    <i class='fas fa-home mr-2'></i> Kembali ke Beranda
                </a>
            </div>
        </body>
        </html>";
    } catch (Exception $e) {
        // Rollback jika terjadi error
        mysqli_rollback($conn);
        echo "<script>alert('Terjadi kesalahan: " . $e->getMessage() . "'); window.history.back();</script>";
    }
} else {
    header('Location: index.php');
    exit;
}
