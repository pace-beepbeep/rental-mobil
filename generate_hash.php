<?php
// Script untuk generate password hash
// Jalankan file ini sekali untuk mendapatkan hash yang benar

$password = 'admin123';
$hash = password_hash($password, PASSWORD_DEFAULT);

echo "Password: $password\n";
echo "Hash: $hash\n";
echo "\n";
echo "Copy hash di atas dan paste ke database.sql\n";
