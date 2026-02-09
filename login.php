<?php
session_start();

// Redirect jika sudah login
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: admin.php');
    exit;
}

require_once 'config.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = clean_input($_POST['username']);
    $password = $_POST['password']; // Jangan escape password sebelum verify

    // Query admin
    $query = "SELECT * FROM admins WHERE username = '$username'";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $admin = mysqli_fetch_assoc($result);

        // Verifikasi password (PLAIN TEXT)
        if ($password === $admin['password']) {
            // Set session
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_username'] = $admin['username'];
            $_SESSION['admin_nama'] = $admin['nama'];

            // Redirect ke admin panel
            header('Location: admin.php');
            exit;
        } else {
            $error = 'Username atau password salah!';
        }
    } else {
        $error = 'Username atau password salah!';
    }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - Rental Mobil</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen flex items-center justify-center">
    <div class="container mx-auto px-6">
        <div class="max-w-md mx-auto">
            <!-- Logo & Title -->
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-20 h-20 bg-indigo-600 rounded-full mb-4">
                    <i class="fas fa-user-shield text-white text-3xl"></i>
                </div>
                <h1 class="text-3xl font-bold text-gray-800 mb-2">Admin Login</h1>
                <p class="text-gray-600">Masuk ke panel administrator</p>
            </div>

            <!-- Login Form -->
            <div class="bg-white rounded-xl shadow-2xl p-8">
                <?php if ($error): ?>
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6 flex items-center">
                        <i class="fas fa-exclamation-circle mr-2"></i>
                        <span><?php echo $error; ?></span>
                    </div>
                <?php endif; ?>

                <form method="POST" action="">
                    <div class="mb-6">
                        <label class="block text-gray-700 font-semibold mb-2">
                            <i class="fas fa-user mr-2 text-indigo-600"></i>Username
                        </label>
                        <input type="text" name="username" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:border-transparent transition"
                            placeholder="Masukkan username"
                            autofocus>
                    </div>

                    <div class="mb-6">
                        <label class="block text-gray-700 font-semibold mb-2">
                            <i class="fas fa-lock mr-2 text-indigo-600"></i>Password
                        </label>
                        <input type="password" name="password" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:border-transparent transition"
                            placeholder="Masukkan password">
                    </div>

                    <button type="submit"
                        class="w-full bg-indigo-600 text-white py-3 rounded-lg font-bold text-lg hover:bg-indigo-700 transition shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                        <i class="fas fa-sign-in-alt mr-2"></i>Login
                    </button>
                </form>

                <!-- Home Link Removed -->

                <!-- Info Default Credentials -->
                <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <p class="text-sm text-blue-800 text-center">
                        <i class="fas fa-info-circle mr-1"></i>
                        <strong>Default:</strong> admin / admin123
                    </p>
                </div>
            </div>

            <!-- Footer -->
            <div class="text-center mt-6">
                <p class="text-gray-600 text-sm">&copy; 2026 RentalMobil. Semua hak dilindungi.</p>
            </div>
        </div>
    </div>
</body>

</html>