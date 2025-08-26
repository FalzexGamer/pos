<?php
session_start();
include 'include/conn.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    $query = mysqli_query($conn, "SELECT * FROM users WHERE username = '$username' AND password = '$password' AND is_active = 1");
    
    if (mysqli_num_rows($query) > 0) {
        $user = mysqli_fetch_array($query);
        
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['full_name'] = $user['full_name'];
        $_SESSION['role'] = $user['role'];
        
        // Update last login
        mysqli_query($conn, "UPDATE users SET last_login = NOW() WHERE id = " . $user['id']);
        
        header("location: dashboard.php");
        exit();
    } else {
        $error = "Invalid username or password";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - POS System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="max-w-md w-full space-y-8">
        <div class="bg-white rounded-lg shadow-lg p-8">
            <!-- Logo/Header -->
            <div class="text-center mb-8">
                <i class="fas fa-cash-register text-4xl text-blue-600 mb-4"></i>
                <h2 class="text-3xl font-bold text-gray-900">POS System</h2>
                <p class="text-gray-600 mt-2">Sign in to your account</p>
            </div>

            <!-- Error Message -->
            <?php if (isset($error)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <i class="fas fa-exclamation-circle mr-2"></i>
                <?= $error ?>
            </div>
            <?php endif; ?>

            <!-- Login Form -->
            <form method="POST" class="space-y-6">
                <div>
                    <label for="username" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-user mr-2"></i>Username
                    </label>
                    <input type="text" id="username" name="username" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="Enter your username">
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-lock mr-2"></i>Password
                    </label>
                    <input type="password" id="password" name="password" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="Enter your password">
                </div>

                <div>
                    <button type="submit" 
                            class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
                        <i class="fas fa-sign-in-alt mr-2"></i>Sign In
                    </button>
                </div>
            </form>

            <!-- Demo Credentials -->
            <div class="mt-6 p-4 bg-gray-50 rounded-md">
                <h3 class="text-sm font-medium text-gray-700 mb-2">Demo Credentials:</h3>
                <div class="text-xs text-gray-600 space-y-1">
                    <p><strong>Admin:</strong> admin / admin123</p>
                    <p><strong>Cashier:</strong> cashier / cashier123</p>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="text-center text-gray-500 text-sm">
            <p>&copy; <?= date('Y') ?> POS System. All rights reserved.</p>
        </div>
    </div>

    <script>
        // Auto-focus on username field
        document.getElementById('username').focus();
        
        // Enter key to submit form
        document.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                document.querySelector('form').submit();
            }
        });
    </script>
</body>
</html>
