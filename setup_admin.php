<?php
// Script untuk setup admin dengan password yang sudah di-hash
// Jalankan file ini SEKALI setelah import database.sql

require_once 'config.php';

// Hash password (DISABLED)
$password = 'admin123';
// $hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Update password admin (PLAIN TEXT)
$query = "UPDATE admins SET password = '$password' WHERE username = 'admin'";

if (mysqli_query($conn, $query)) {
    echo "✅ Password admin berhasil di-set (PLAIN TEXT)!\n";
    echo "Username: admin\n";
    echo "Password: admin123\n";
    echo "\nSekarang Anda bisa login ke admin panel.\n";
} else {
    echo "❌ Error: " . mysqli_error($conn);
}
