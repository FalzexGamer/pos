<?php
include '../include/conn.php';

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$user_id = $_SESSION['user_id'] ?? 0;
$member_id = $_GET['member_id'] ?? $_POST['member_id'] ?? '';

if (!$user_id) {
    echo json_encode([
        'success' => false,
        'message' => 'User not authenticated',
        'cart' => [],
        'html' => '',
        'subtotal' => 0,
        'discount' => 0,
        'tax' => 0,
        'total' => 0
    ]);
    exit;
}

// Get member discount percentage if member is selected
$discount_percentage = 0;
$member_name = '';
if (!empty($member_id)) {
    $member_query = mysqli_query($conn, "
        SELECT m.name, mt.discount_percentage 
        FROM members m 
        LEFT JOIN membership_tiers mt ON m.membership_tier_id = mt.id 
        WHERE m.id = '$member_id' AND m.is_active = 1
    ");
    
    if ($member_data = mysqli_fetch_array($member_query)) {
        $discount_percentage = floatval($member_data['discount_percentage']);
        $member_name = $member_data['name'];
    }
}

// Get cart items from database
$query_cart_items = mysqli_query($conn, "
    SELECT c.*, p.name, p.barcode, p.stock_quantity, p.img 
    FROM cart c 
    JOIN products p ON c.product_id = p.id 
    WHERE c.user_id = $user_id AND c.status = 'active' 
    ORDER BY c.created_at ASC
");

$cart = [];
$html = '';
$subtotal = 0;

while ($item = mysqli_fetch_array($query_cart_items)) {
    $cart[] = [
        'id' => $item['id'],
        'product_id' => $item['product_id'],
        'name' => $item['name'],
        'sku' => $item['sku'],
        'price' => $item['price'],
        'quantity' => $item['quantity'],
        'total' => $item['subtotal']
    ];
    
    $html .= '
    <div class="group backdrop-blur-sm bg-white/70 rounded-xl shadow-sm border border-white/20 p-3 lg:p-4 hover:shadow-lg transition-all duration-200">
        <div class="flex items-center space-x-2 lg:space-x-4">
            <!-- Product Icon -->
            <div class="flex-shrink-0">
                ' . ($item['img'] && $item['img'] !== '-' ? 
                    '<img src="uploads/products/' . htmlspecialchars($item['img']) . '" alt="' . htmlspecialchars($item['name']) . '" class="w-10 h-10 lg:w-12 lg:h-12 rounded-xl object-cover border border-gray-200">' :
                    '<div class="w-10 h-10 lg:w-12 lg:h-12 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center">
                        <i class="fas fa-box text-white text-xs lg:text-sm"></i>
                    </div>') . '
            </div>
            
            <!-- Product Info -->
            <div class="flex-1 min-w-0">
                <div class="flex items-center justify-between mb-1">
                    <h4 class="font-semibold text-gray-900 text-xs lg:text-sm truncate">' . htmlspecialchars($item['name']) . '</h4>
                    <button onclick="removeFromCart(' . $item['id'] . ')" class="text-red-500 hover:text-red-700 p-1 hover:bg-red-50 rounded-lg transition-colors duration-200 group-hover:opacity-100 opacity-0">
                        <i class="fas fa-trash text-xs lg:text-sm"></i>
                    </button>
                </div>
                <p class="text-xs text-gray-500 font-mono mb-1 lg:mb-2">' . htmlspecialchars($item['sku']) . '</p>
                <div class="flex items-center justify-between">
                    <div class="text-xs lg:text-sm font-bold text-blue-600">RM ' . number_format($item['price'], 2) . '</div>
                    <div class="text-xs lg:text-sm font-semibold text-gray-900">RM ' . number_format($item['subtotal'], 2) . '</div>
                </div>
            </div>
            
            <!-- Quantity Control -->
            <div class="flex-shrink-0">
                <div class="flex items-center space-x-1 lg:space-x-2">
                    <button onclick="updateQuantity(' . $item['id'] . ', ' . ($item['quantity'] - 1) . ')" class="w-7 h-7 lg:w-8 lg:h-8 bg-gray-100 hover:bg-gray-200 rounded-lg flex items-center justify-center transition-colors duration-200">
                        <i class="fas fa-minus text-xs text-gray-600"></i>
                    </button>
                    <input type="number" value="' . $item['quantity'] . '" min="1" 
                           class="w-12 lg:w-16 px-2 lg:px-3 py-1 lg:py-2 text-xs lg:text-sm text-center bg-white/80 backdrop-blur-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200" 
                           onchange="updateQuantity(' . $item['id'] . ', this.value)">
                    <button onclick="updateQuantity(' . $item['id'] . ', ' . ($item['quantity'] + 1) . ')" class="w-7 h-7 lg:w-8 lg:h-8 bg-gray-100 hover:bg-gray-200 rounded-lg flex items-center justify-center transition-colors duration-200">
                        <i class="fas fa-plus text-xs text-gray-600"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>';
    
    $subtotal += $item['subtotal'];
}

if (empty($cart)) {
    $html = '<div class="text-center py-8 lg:py-12">
        <div class="w-12 h-12 lg:w-16 lg:h-16 mx-auto bg-gradient-to-br from-gray-100 to-gray-200 rounded-full flex items-center justify-center mb-4">
            <i class="fas fa-shopping-cart text-xl lg:text-2xl text-gray-400"></i>
        </div>
        <h3 class="text-base lg:text-lg font-medium text-gray-900 mb-2">Cart is Empty</h3>
        <p class="text-xs lg:text-sm text-gray-500">Start adding products to begin a sale</p>
    </div>';
}

// Calculate totals
$discount = ($subtotal * $discount_percentage) / 100; // Apply member discount
$tax = ($subtotal - $discount) * 0.06; // 6% tax
$total = $subtotal - $discount + $tax;

echo json_encode([
    'success' => true,
    'cart' => $cart,
    'html' => $html,
    'subtotal' => $subtotal,
    'discount' => $discount,
    'tax' => $tax,
    'total' => $total,
    'member_name' => $member_name,
    'discount_percentage' => $discount_percentage
]);
?>
