<?php
session_start();

// Jika sudah login, redirect ke admin.php
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: admin.php');
    exit;
}

// Jika belum login, redirect ke login.php
header('Location: login.php');
exit;
