<?php
session_start();
require_once 'include/conn.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$message = '';
$error = '';

// Get user data
$user_query = "SELECT * FROM users WHERE id = ?";
$stmt = mysqli_prepare($conn, $user_query);
mysqli_stmt_bind_param($stmt, 'i', $user_id);
mysqli_stmt_execute($stmt);
$user_result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($user_result);

// Handle form submission for profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    
    // Validation
    if (empty($full_name)) {
        $error = 'Full name is required';
    } elseif (empty($email)) {
        $error = 'Email is required';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address';
    } else {
        // Check if email already exists for other users
        $email_check_query = "SELECT id FROM users WHERE email = ? AND id != ?";
        $stmt = mysqli_prepare($conn, $email_check_query);
        mysqli_stmt_bind_param($stmt, 'si', $email, $user_id);
        mysqli_stmt_execute($stmt);
        $email_check_result = mysqli_stmt_get_result($stmt);
        
        if (mysqli_num_rows($email_check_result) > 0) {
            $error = 'Email address is already in use by another user';
        } else {
            // Update profile
            $update_query = "UPDATE users SET full_name = ?, email = ?, phone = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?";
            $stmt = mysqli_prepare($conn, $update_query);
            mysqli_stmt_bind_param($stmt, 'sssi', $full_name, $email, $phone, $user_id);
            
            if (mysqli_stmt_execute($stmt)) {
                $message = 'Profile updated successfully!';
                // Refresh user data
                $user['full_name'] = $full_name;
                $user['email'] = $email;
                $user['phone'] = $phone;
            } else {
                $error = 'Failed to update profile. Please try again.';
            }
        }
    }
}

// Handle password change
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Validation
    if (empty($current_password)) {
        $error = 'Current password is required';
    } elseif (empty($new_password)) {
        $error = 'New password is required';
    } elseif (strlen($new_password) < 6) {
        $error = 'New password must be at least 6 characters long';
    } elseif ($new_password !== $confirm_password) {
        $error = 'New passwords do not match';
    } else {
        // Verify current password
        if (password_verify($current_password, $user['password'])) {
            // Update password
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $password_update_query = "UPDATE users SET password = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?";
            $stmt = mysqli_prepare($conn, $password_update_query);
            mysqli_stmt_bind_param($stmt, 'si', $hashed_password, $user_id);
            
            if (mysqli_stmt_execute($stmt)) {
                $message = 'Password changed successfully!';
            } else {
                $error = 'Failed to change password. Please try again.';
            }
        } else {
            $error = 'Current password is incorrect';
        }
    }
}

// Get user activity statistics
$activity_query = "SELECT 
    COUNT(*) as total_sales,
    SUM(total_amount) as total_revenue,
    MAX(created_at) as last_sale_date
FROM sales 
WHERE user_id = ?";

$stmt = mysqli_prepare($conn, $activity_query);
mysqli_stmt_bind_param($stmt, 'i', $user_id);
mysqli_stmt_execute($stmt);
$activity_result = mysqli_stmt_get_result($stmt);
$activity_data = mysqli_fetch_assoc($activity_result);

// Get recent sales
$recent_sales_query = "SELECT 
    s.id,
    s.invoice_number,
    s.total_amount,
    s.payment_method,
    s.created_at,
    COUNT(si.id) as items_count
FROM sales s
LEFT JOIN sale_items si ON s.id = si.sale_id
WHERE s.user_id = ?
GROUP BY s.id
ORDER BY s.created_at DESC
LIMIT 5";

$stmt = mysqli_prepare($conn, $recent_sales_query);
mysqli_stmt_bind_param($stmt, 'i', $user_id);
mysqli_stmt_execute($stmt);
$recent_sales_result = mysqli_stmt_get_result($stmt);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - POS System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
</head>
<body class="bg-gray-100">
    <?php include 'include/header.php'; ?>
    <?php include 'include/sidebar.php'; ?>

    <div class="main-content lg:ml-64 pt-16">
        <div class="p-6">
            <!-- Page Header -->
            <div class="mb-6">
                <h1 class="text-2xl font-bold text-gray-900">Profile</h1>
                <p class="text-gray-600">Manage your account settings and view your activity</p>
            </div>

            <!-- Alert Messages -->
            <?php if ($message): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                    <i class="fas fa-check-circle mr-2"></i>
                    <?= htmlspecialchars($message) ?>
                </div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Profile Information -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-lg shadow-sm">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h2 class="text-lg font-semibold text-gray-900">Profile Information</h2>
                        </div>
                        <div class="p-6">
                            <form method="POST" class="space-y-6">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Username</label>
                                                                                <input type="text" value="<?= htmlspecialchars($user['username'] ?? '') ?>"
                                               class="w-full border border-gray-300 rounded-lg px-3 py-2 bg-gray-50" 
                                               readonly>
                                        <p class="text-xs text-gray-500 mt-1">Username cannot be changed</p>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Role</label>
                                                                                <input type="text" value="<?= ucfirst(htmlspecialchars($user['role'] ?? '')) ?>"
                                               class="w-full border border-gray-300 rounded-lg px-3 py-2 bg-gray-50" 
                                               readonly>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Full Name *</label>
                                        <input type="text" name="full_name" value="<?= htmlspecialchars($user['full_name'] ?? '') ?>" 
                                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                                               required>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Email *</label>
                                        <input type="email" name="email" value="<?= htmlspecialchars($user['email'] ?? '') ?>" 
                                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                                               required>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Phone</label>
                                        <input type="tel" name="phone" value="<?= htmlspecialchars($user['phone'] ?? '') ?>" 
                                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Member Since</label>
                                        <input type="text" value="<?= date('F j, Y', strtotime($user['created_at'])) ?>" 
                                               class="w-full border border-gray-300 rounded-lg px-3 py-2 bg-gray-50" 
                                               readonly>
                                    </div>
                                </div>
                                
                                <div class="flex justify-end">
                                    <button type="submit" name="update_profile" 
                                            class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition-colors">
                                        <i class="fas fa-save mr-2"></i>Update Profile
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Change Password -->
                    <div class="bg-white rounded-lg shadow-sm mt-6">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h2 class="text-lg font-semibold text-gray-900">Change Password</h2>
                        </div>
                        <div class="p-6">
                            <form method="POST" class="space-y-6">
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Current Password *</label>
                                        <input type="password" name="current_password" 
                                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                                               required>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">New Password *</label>
                                        <input type="password" name="new_password" 
                                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                                               required>
                                        <p class="text-xs text-gray-500 mt-1">Minimum 6 characters</p>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Confirm New Password *</label>
                                        <input type="password" name="confirm_password" 
                                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                                               required>
                                    </div>
                                </div>
                                
                                <div class="flex justify-end">
                                    <button type="submit" name="change_password" 
                                            class="bg-yellow-600 hover:bg-yellow-700 text-white px-6 py-2 rounded-lg transition-colors">
                                        <i class="fas fa-key mr-2"></i>Change Password
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Profile Card -->
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <div class="text-center">
                            <div class="w-24 h-24 mx-auto mb-4 rounded-full bg-blue-100 flex items-center justify-center">
                                <i class="fas fa-user text-3xl text-blue-600"></i>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900"><?= htmlspecialchars($user['full_name'] ?? '') ?></h3>
                            <p class="text-sm text-gray-600"><?= ucfirst(htmlspecialchars($user['role'] ?? '')) ?></p>
                            <p class="text-xs text-gray-500 mt-1"><?= htmlspecialchars($user['username'] ?? '') ?></p>
                        </div>
                        
                        <div class="mt-6 space-y-3">
                            <div class="flex items-center text-sm">
                                <i class="fas fa-envelope text-gray-400 w-4 mr-3"></i>
                                <span class="text-gray-600"><?= htmlspecialchars($user['email'] ?? '') ?></span>
                            </div>
                            <?php if ($user['phone']): ?>
                                <div class="flex items-center text-sm">
                                    <i class="fas fa-phone text-gray-400 w-4 mr-3"></i>
                                    <span class="text-gray-600"><?= htmlspecialchars($user['phone'] ?? '') ?></span>
                                </div>
                            <?php endif; ?>
                            <div class="flex items-center text-sm">
                                <i class="fas fa-calendar text-gray-400 w-4 mr-3"></i>
                                <span class="text-gray-600">Member since <?= date('M Y', strtotime($user['created_at'])) ?></span>
                            </div>
                        </div>
                    </div>

                    <!-- Activity Statistics -->
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Activity Statistics</h3>
                        <div class="space-y-4">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center mr-3">
                                        <i class="fas fa-shopping-cart text-green-600"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">Total Sales</p>
                                        <p class="text-xs text-gray-500">Transactions</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="text-lg font-bold text-gray-900"><?= number_format($activity_data['total_sales']) ?></p>
                                </div>
                            </div>
                            
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center mr-3">
                                        <i class="fas fa-dollar-sign text-blue-600"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">Total Revenue</p>
                                        <p class="text-xs text-gray-500">Generated</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="text-lg font-bold text-gray-900">RM <?= number_format($activity_data['total_revenue'], 2) ?></p>
                                </div>
                            </div>
                            
                            <?php if ($activity_data['last_sale_date']): ?>
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 rounded-full bg-purple-100 flex items-center justify-center mr-3">
                                            <i class="fas fa-clock text-purple-600"></i>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">Last Sale</p>
                                            <p class="text-xs text-gray-500">Recent activity</p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-sm font-medium text-gray-900"><?= date('M j', strtotime($activity_data['last_sale_date'])) ?></p>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Recent Sales -->
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Recent Sales</h3>
                        <?php if ($recent_sales_result && mysqli_num_rows($recent_sales_result) > 0): ?>
                            <div class="space-y-3">
                                <?php while ($sale = mysqli_fetch_assoc($recent_sales_result)): ?>
                                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                        <div>
                                            <p class="text-sm font-medium text-gray-900"><?= htmlspecialchars($sale['invoice_number']) ?></p>
                                            <p class="text-xs text-gray-500"><?= $sale['items_count'] ?> items</p>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-sm font-bold text-gray-900">RM <?= number_format($sale['total_amount'], 2) ?></p>
                                            <p class="text-xs text-gray-500"><?= date('M j', strtotime($sale['created_at'])) ?></p>
                                        </div>
                                    </div>
                                <?php endwhile; ?>
                            </div>
                        <?php else: ?>
                            <p class="text-gray-500 text-center py-4">No recent sales</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            // Auto-hide alert messages after 5 seconds
            setTimeout(function() {
                $('.bg-green-100, .bg-red-100').fadeOut();
            }, 5000);

            // Password strength indicator
            $('input[name="new_password"]').on('input', function() {
                const password = $(this).val();
                const strength = getPasswordStrength(password);
                updatePasswordStrengthIndicator(strength);
            });

            function getPasswordStrength(password) {
                let strength = 0;
                if (password.length >= 6) strength++;
                if (password.match(/[a-z]/)) strength++;
                if (password.match(/[A-Z]/)) strength++;
                if (password.match(/[0-9]/)) strength++;
                if (password.match(/[^a-zA-Z0-9]/)) strength++;
                return strength;
            }

            function updatePasswordStrengthIndicator(strength) {
                const strengthText = ['Very Weak', 'Weak', 'Fair', 'Good', 'Strong'];
                const strengthColor = ['text-red-600', 'text-orange-600', 'text-yellow-600', 'text-blue-600', 'text-green-600'];
                
                if (strength > 0) {
                    const indicator = `<p class="text-xs mt-1 ${strengthColor[strength-1]}">Password strength: ${strengthText[strength-1]}</p>`;
                    $('input[name="new_password"]').next('.text-xs').remove();
                    $('input[name="new_password"]').after(indicator);
                }
            }
        });
    </script>
</body>
</html>
