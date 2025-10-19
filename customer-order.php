<?php
include 'include/conn.php';

// Get table ID from URL parameter (default to 1 if not provided)
$table_id = isset($_GET['table']) ? (int)$_GET['table'] : 1;

// Validate table ID (must be positive number)
if ($table_id <= 0) {
    $table_id = 1;
}

// Also support 't' parameter as alternative
if (isset($_GET['t']) && (int)$_GET['t'] > 0) {
    $table_id = (int)$_GET['t'];
}

// Get categories and products
$categories_query = mysqli_query($conn, "SELECT * FROM categories WHERE is_active = 1 ORDER BY name");
$categories = [];
while ($category = mysqli_fetch_array($categories_query)) {
    $category_id = $category['id'];
    $products_query = mysqli_query($conn, "SELECT id, name, sku, selling_price, stock_quantity, img FROM products WHERE category_id = $category_id AND is_active = 1 ORDER BY name");
    $products = [];
    while ($product = mysqli_fetch_array($products_query)) {
        $products[] = $product;
    }
    $category['products'] = $products;
    $categories[] = $category;
}

// Get company settings
$company_query = mysqli_query($conn, "SELECT * FROM company_settings WHERE id = 1");
$company = mysqli_fetch_array($company_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Order - Table <?= $table_id ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        .category-card {
            transition: all 0.3s ease;
        }
        .category-card:hover {
            transform: translateY(-2px);
        }
        .product-card {
            transition: all 0.3s ease;
        }
        .product-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
        .cart-item {
            animation: slideIn 0.3s ease;
        }
        @keyframes slideIn {
            from { opacity: 0; transform: translateX(-20px); }
            to { opacity: 1; transform: translateX(0); }
        }
        .category-products {
            display: none;
        }
        .category-products.active {
            display: block;
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Header -->
    <header class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-lg flex items-center justify-center">
                        <i class="fas fa-utensils text-white text-xl"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900"><?= htmlspecialchars($company['company_name']) ?></h1>
                        <p class="text-sm text-gray-500">Table <?= $table_id ?></p>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm font-medium">
                        <i class="fas fa-table mr-1"></i>
                        Table <?= $table_id ?>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Product Categories and Items -->
            <div class="lg:col-span-2">
                <!-- Categories -->
                <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">
                        <i class="fas fa-tags mr-2 text-blue-600"></i>
                        Categories
                    </h2>
                    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-3">
                        <?php foreach ($categories as $category): ?>
                        <button class="category-card flex flex-col items-center justify-center p-4 bg-gray-50 border border-gray-200 rounded-lg hover:bg-blue-50 hover:border-blue-300 hover:shadow-md" 
                                onclick="showCategoryProducts('category-<?= $category['id'] ?>')"
                                data-category-id="<?= $category['id'] ?>">
                            <i class="fas fa-tag text-2xl text-blue-600 mb-2"></i>
                            <span class="text-sm font-medium text-gray-900 text-center"><?= htmlspecialchars($category['name']) ?></span>
                            <span class="text-xs text-gray-500"><?= count($category['products']) ?> items</span>
                        </button>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Products -->
                <?php foreach ($categories as $category): ?>
                <div id="category-<?= $category['id'] ?>" class="category-products bg-white rounded-lg shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">
                        <?= htmlspecialchars($category['name']) ?> 
                        <span class="text-sm font-normal text-gray-500">(<?= count($category['products']) ?> items)</span>
                    </h3>
                    
                    <?php if (empty($category['products'])): ?>
                    <div class="text-center py-8">
                        <i class="fas fa-box-open text-4xl text-gray-300 mb-3"></i>
                        <p class="text-gray-500">No products available in this category</p>
                    </div>
                    <?php else: ?>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                        <?php foreach ($category['products'] as $product): ?>
                        <div class="product-card bg-white border border-gray-200 rounded-lg p-4 hover:shadow-lg">
                            <div class="flex items-start space-x-3">
                                <!-- Product Image -->
                                <div class="flex-shrink-0">
                                    <?php if ($product['img'] && $product['img'] !== '-'): ?>
                                        <img src="uploads/products/<?= htmlspecialchars($product['img']) ?>" 
                                             alt="<?= htmlspecialchars($product['name']) ?>" 
                                             class="w-16 h-16 rounded-lg object-cover border border-gray-200">
                                    <?php else: ?>
                                        <div class="w-16 h-16 bg-gradient-to-r from-green-500 to-emerald-600 rounded-lg flex items-center justify-center">
                                            <i class="fas fa-box text-white text-lg"></i>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                
                                <!-- Product Info -->
                                <div class="flex-1 min-w-0">
                                    <h4 class="font-medium text-gray-900 text-sm mb-1"><?= htmlspecialchars($product['name']) ?></h4>
                                    <p class="text-xs text-gray-500 mb-2"><?= htmlspecialchars($product['sku']) ?></p>
                                    
                                    <div class="flex items-center justify-between">
                                        <div class="text-lg font-bold text-green-600">
                                            RM <?= number_format($product['selling_price'], 2) ?>
                                        </div>
                                        <div class="text-xs <?= $product['stock_quantity'] > 0 ? 'text-green-600' : 'text-red-600' ?>">
                                            <?= $product['stock_quantity'] > 0 ? 'In Stock' : 'Out of Stock' ?>
                                        </div>
                                    </div>
                                    
                                    <div class="mt-3 flex items-center justify-between">
                                        <span class="text-xs text-gray-400">Stock: <?= $product['stock_quantity'] ?></span>
                                        <?php if ($product['stock_quantity'] > 0): ?>
                                        <button onclick="addToCustomerCart(<?= $product['id'] ?>, '<?= htmlspecialchars($product['name']) ?>', <?= $product['selling_price'] ?>)"
                                                class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded-md text-sm font-medium transition-colors">
                                            <i class="fas fa-plus mr-1"></i>
                                            Add
                                        </button>
                                        <?php else: ?>
                                        <button disabled class="bg-gray-300 text-gray-500 px-3 py-1 rounded-md text-sm font-medium cursor-not-allowed">
                                            Out of Stock
                                        </button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- Customer Cart -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-sm p-6 sticky top-6">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-semibold text-gray-900">
                            <i class="fas fa-shopping-cart mr-2 text-blue-600"></i>
                            Your Order
                        </h2>
                        <span id="cart-count" class="bg-blue-100 text-blue-800 px-2 py-1 rounded-full text-xs font-medium">0</span>
                    </div>

                    <!-- Cart Items -->
                    <div id="cart-items" class="space-y-3 mb-6">
                        <div class="text-center py-8 text-gray-500">
                            <i class="fas fa-shopping-cart text-3xl mb-2"></i>
                            <p>Your cart is empty</p>
                            <p class="text-sm">Add some items to get started</p>
                        </div>
                    </div>

                    <!-- Cart Summary -->
                    <div id="cart-summary" class="border-t pt-4 hidden">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm font-medium text-gray-600">Subtotal:</span>
                            <span id="cart-subtotal" class="text-sm font-medium text-gray-900">RM 0.00</span>
                        </div>
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm font-medium text-gray-600">Tax (6%):</span>
                            <span id="cart-tax" class="text-sm font-medium text-gray-900">RM 0.00</span>
                        </div>
                        <div class="flex justify-between items-center mb-4 border-t pt-2">
                            <span class="text-base font-semibold text-gray-900">Total:</span>
                            <span id="cart-total" class="text-lg font-bold text-green-600">RM 0.00</span>
                        </div>
                        
                        <!-- Action Buttons -->
                        <div class="space-y-2">
                            <button onclick="clearCustomerCart()" 
                                    class="w-full bg-gray-500 hover:bg-gray-600 text-white py-2 px-4 rounded-lg font-medium transition-colors">
                                <i class="fas fa-trash mr-2"></i>
                                Clear Cart
                            </button>
                            <button onclick="submitCustomerOrder()" 
                                    class="w-full bg-green-600 hover:bg-green-700 text-white py-3 px-4 rounded-lg font-semibold transition-colors">
                                <i class="fas fa-check mr-2"></i>
                                Place Order
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Success Modal with Receipt -->
    <div id="success-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-lg max-w-lg w-full mx-4 max-h-[90vh] overflow-y-auto">
            <div class="text-center p-6">
                <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-check text-green-600 text-2xl"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Order Placed Successfully!</h3>
                <p class="text-gray-600 mb-4">Your order has been sent to the kitchen. We'll prepare it for you shortly.</p>
                
                <!-- Receipt Section -->
                <div id="receipt-section" class="hidden bg-gray-50 rounded-lg p-4 mb-4 text-left">
                    <h4 class="font-semibold text-gray-900 mb-3 text-center">Order Receipt</h4>
                    
                    <!-- Receipt Header -->
                    <div class="text-center mb-4 pb-3 border-b border-gray-300">
                        <h5 class="font-bold text-lg"><?= htmlspecialchars($company['company_name']) ?></h5>
                        <p class="text-sm text-gray-600">Table <span id="receipt-table-id">1</span></p>
                        <p class="text-xs text-gray-500">Order #<span id="receipt-order-id">-</span></p>
                        <p class="text-xs text-gray-500" id="receipt-date">-</p>
                    </div>
                    
                    <!-- Order Items -->
                    <div id="receipt-items" class="mb-4">
                        <!-- Items will be populated here -->
                    </div>
                    
                    <!-- Receipt Totals -->
                    <div class="border-t border-gray-300 pt-3 space-y-1">
                        <div class="flex justify-between text-sm">
                            <span>Subtotal:</span>
                            <span>RM <span id="receipt-subtotal">0.00</span></span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span>Tax (6%):</span>
                            <span>RM <span id="receipt-tax">0.00</span></span>
                        </div>
                        <div class="flex justify-between font-semibold border-t border-gray-300 pt-2">
                            <span>Total:</span>
                            <span>RM <span id="receipt-total">0.00</span></span>
                        </div>
                    </div>
                    
                    <!-- QR Code -->
                    <div class="text-center mt-4 pt-3 border-t border-gray-300">
                        <p class="text-xs text-gray-600 mb-2">Scan this QR code at POS for order details</p>
                        <div id="qr-code-container" class="flex justify-center">
                            <!-- QR code will be inserted here -->
                        </div>
                    </div>
                </div>
                
                <!-- Action Buttons -->
                <div class="space-y-2">
                    <button onclick="printReceipt()" id="print-receipt-btn" class="hidden w-full bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium transition-colors">
                        <i class="fas fa-print mr-2"></i>
                        Print Receipt
                    </button>
                    <button onclick="closeSuccessModal()" 
                            class="w-full bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg font-medium transition-colors">
                        Continue Shopping
                    </button>
                </div>
            </div>
        </div>
    </div>


    <script>
        let customerCart = [];
        let tableId = <?= $table_id ?>;
        let currentCategory = null;

        // Show category products
        function showCategoryProducts(categoryId) {
            // Hide all category products
            document.querySelectorAll('.category-products').forEach(category => {
                category.classList.remove('active');
            });
            
            // Show selected category
            document.getElementById(categoryId).classList.add('active');
            currentCategory = categoryId;
            
            // Update category button states
            document.querySelectorAll('.category-card').forEach(card => {
                card.classList.remove('bg-blue-100', 'border-blue-300');
                card.classList.add('bg-gray-50', 'border-gray-200');
            });
            
            // Highlight selected category
            event.target.closest('.category-card').classList.remove('bg-gray-50', 'border-gray-200');
            event.target.closest('.category-card').classList.add('bg-blue-100', 'border-blue-300');
        }

        // Add product to customer cart
        function addToCustomerCart(productId, productName, price) {
            // Check if product already exists in cart
            const existingItem = customerCart.find(item => item.product_id === productId);
            
            if (existingItem) {
                existingItem.quantity += 1;
                existingItem.subtotal = existingItem.quantity * existingItem.price;
            } else {
                customerCart.push({
                    product_id: productId,
                    name: productName,
                    price: price,
                    quantity: 1,
                    subtotal: price
                });
            }
            
            updateCustomerCartDisplay();
            
            // Show success message
            showToast(`${productName} added to cart!`, 'success');
        }

        // Remove product from customer cart
        function removeFromCustomerCart(productId) {
            customerCart = customerCart.filter(item => item.product_id !== productId);
            updateCustomerCartDisplay();
        }

        // Update product quantity in customer cart
        function updateCustomerCartQuantity(productId, quantity) {
            if (quantity <= 0) {
                removeFromCustomerCart(productId);
                return;
            }
            
            const item = customerCart.find(item => item.product_id === productId);
            if (item) {
                item.quantity = quantity;
                item.subtotal = item.quantity * item.price;
                updateCustomerCartDisplay();
            }
        }

        // Update customer cart display
        function updateCustomerCartDisplay() {
            const cartItemsContainer = document.getElementById('cart-items');
            const cartCount = document.getElementById('cart-count');
            const cartSummary = document.getElementById('cart-summary');
            const cartSubtotal = document.getElementById('cart-subtotal');
            const cartTax = document.getElementById('cart-tax');
            const cartTotal = document.getElementById('cart-total');
            
            cartCount.textContent = customerCart.length;
            
            if (customerCart.length === 0) {
                cartItemsContainer.innerHTML = `
                    <div class="text-center py-8 text-gray-500">
                        <i class="fas fa-shopping-cart text-3xl mb-2"></i>
                        <p>Your cart is empty</p>
                        <p class="text-sm">Add some items to get started</p>
                    </div>
                `;
                cartSummary.classList.add('hidden');
            } else {
                let cartHTML = '';
                let subtotal = 0;
                
                customerCart.forEach(item => {
                    subtotal += item.subtotal;
                    cartHTML += `
                        <div class="cart-item bg-gray-50 rounded-lg p-3">
                            <div class="flex items-center justify-between mb-2">
                                <h4 class="font-medium text-gray-900 text-sm">${item.name}</h4>
                                <button onclick="removeFromCustomerCart(${item.product_id})" 
                                        class="text-red-500 hover:text-red-700 text-sm">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-2">
                                    <button onclick="updateCustomerCartQuantity(${item.product_id}, ${item.quantity - 1})" 
                                            class="w-6 h-6 bg-gray-200 hover:bg-gray-300 rounded-full flex items-center justify-center text-xs">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                    <span class="text-sm font-medium">${item.quantity}</span>
                                    <button onclick="updateCustomerCartQuantity(${item.product_id}, ${item.quantity + 1})" 
                                            class="w-6 h-6 bg-gray-200 hover:bg-gray-300 rounded-full flex items-center justify-center text-xs">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </div>
                                <div class="text-sm font-medium text-gray-900">
                                    RM ${item.subtotal.toFixed(2)}
                                </div>
                            </div>
                        </div>
                    `;
                });
                
                cartItemsContainer.innerHTML = cartHTML;
                
                const tax = subtotal * 0.06; // 6% tax
                const total = subtotal + tax;
                
                cartSubtotal.textContent = `RM ${subtotal.toFixed(2)}`;
                cartTax.textContent = `RM ${tax.toFixed(2)}`;
                cartTotal.textContent = `RM ${total.toFixed(2)}`;
                
                cartSummary.classList.remove('hidden');
            }
        }

        // Clear customer cart
        function clearCustomerCart() {
            if (customerCart.length === 0) return;
            
            if (confirm('Are you sure you want to clear your cart?')) {
                customerCart = [];
                updateCustomerCartDisplay();
                showToast('Cart cleared!', 'info');
            }
        }

        // Submit customer order
        function submitCustomerOrder() {
            if (customerCart.length === 0) {
                showToast('Your cart is empty!', 'error');
                return;
            }
            
            if (confirm('Place this order and proceed to payment?')) {
                // Send order to server
                $.ajax({
                    url: 'ajax/submit-customer-order.php',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        table_id: tableId,
                        items: JSON.stringify(customerCart)
                    },
                    success: function(data) {
                        if (data.success) {
                            customerCart = [];
                            updateCustomerCartDisplay();
                            showToast('Order placed successfully! Redirecting to payment...', 'success');
                            
                            // Redirect to payment gateway with order number
                            setTimeout(function() {
                                window.location.href = 'paymentgateway.php?order_number=' + encodeURIComponent(data.receipt_data.order_id);
                            }, 1000);
                        } else {
                            console.error('Order submission failed:', data);
                            showToast(data.message || 'Failed to place order', 'error');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX Error:', {xhr: xhr, status: status, error: error});
                        console.error('Response:', xhr.responseText);
                        showToast('Error connecting to server: ' + error, 'error');
                    }
                });
            }
        }

        // Show success modal with receipt data
        function showSuccessModal(receiptData) {
            if (receiptData) {
                // Populate receipt data
                document.getElementById('receipt-table-id').textContent = receiptData.table_id;
                document.getElementById('receipt-order-id').textContent = receiptData.order_id;
                document.getElementById('receipt-date').textContent = receiptData.date;
                document.getElementById('receipt-subtotal').textContent = receiptData.subtotal;
                document.getElementById('receipt-tax').textContent = receiptData.tax;
                document.getElementById('receipt-total').textContent = receiptData.total;
                
                // Populate receipt items
                let itemsHtml = '';
                receiptData.items.forEach(item => {
                    const itemTotal = (item.price * item.quantity).toFixed(2);
                    itemsHtml += `
                        <div class="flex justify-between items-center py-1 text-sm">
                            <div class="flex-1">
                                <div class="font-medium">${item.name}</div>
                                <div class="text-xs text-gray-500">${item.quantity} x RM ${item.price.toFixed(2)}</div>
                            </div>
                            <div class="font-medium">RM ${itemTotal}</div>
                        </div>
                    `;
                });
                document.getElementById('receipt-items').innerHTML = itemsHtml;
                
                // Add QR code with error handling
                document.getElementById('qr-code-container').innerHTML = `
                    <img src="${receiptData.qr_code_url}" 
                         alt="QR Code" 
                         class="w-32 h-32 border border-gray-300 rounded"
                         onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                    <div style="display:none;" class="text-center text-gray-500 text-sm">
                        <i class="fas fa-qrcode text-2xl mb-2"></i>
                        <p>QR Code unavailable</p>
                        <p class="text-xs">Order ID: ${receiptData.order_id}</p>
                    </div>
                `;
                
                // Show receipt section and print button
                document.getElementById('receipt-section').classList.remove('hidden');
                document.getElementById('print-receipt-btn').classList.remove('hidden');
            }
            
            document.getElementById('success-modal').classList.remove('hidden');
            document.getElementById('success-modal').classList.add('flex');
        }

        // Close success modal
        function closeSuccessModal() {
            document.getElementById('success-modal').classList.add('hidden');
            document.getElementById('success-modal').classList.remove('flex');
            
            // Hide receipt section and print button
            document.getElementById('receipt-section').classList.add('hidden');
            document.getElementById('print-receipt-btn').classList.add('hidden');
        }

        // Print receipt
        function printReceipt() {
            const printWindow = window.open('', '_blank');
            const receiptSection = document.getElementById('receipt-section');
            
            printWindow.document.write(`
                <!DOCTYPE html>
                <html>
                <head>
                    <title>Order Receipt</title>
                    <style>
                        body { font-family: Arial, sans-serif; margin: 20px; }
                        .receipt { max-width: 300px; margin: 0 auto; }
                        .header { text-align: center; margin-bottom: 20px; border-bottom: 1px solid #ccc; padding-bottom: 10px; }
                        .item { display: flex; justify-content: space-between; margin-bottom: 5px; }
                        .totals { border-top: 1px solid #ccc; padding-top: 10px; margin-top: 15px; }
                        .qr-code { text-align: center; margin-top: 20px; }
                        .qr-code img { width: 150px; height: 150px; }
                        @media print { body { margin: 0; } }
                    </style>
                </head>
                <body>
                    <div class="receipt">
                        ${receiptSection.outerHTML}
                    </div>
                </body>
                </html>
            `);
            
            printWindow.document.close();
            printWindow.print();
        }

        // Show toast notification
        function showToast(message, type = 'info') {
            const toast = document.createElement('div');
            const bgColor = type === 'success' ? 'bg-green-500' : type === 'error' ? 'bg-red-500' : 'bg-blue-500';
            
            toast.className = `fixed top-4 right-4 ${bgColor} text-white px-6 py-3 rounded-lg shadow-lg z-50 transform translate-x-full transition-transform duration-300`;
            toast.innerHTML = `
                <div class="flex items-center space-x-2">
                    <i class="fas fa-${type === 'success' ? 'check' : type === 'error' ? 'times' : 'info'}"></i>
                    <span>${message}</span>
                </div>
            `;
            
            document.body.appendChild(toast);
            
            setTimeout(() => {
                toast.classList.remove('translate-x-full');
            }, 100);
            
            setTimeout(() => {
                toast.classList.add('translate-x-full');
                setTimeout(() => {
                    document.body.removeChild(toast);
                }, 300);
            }, 3000);
        }


        // Initialize page
        $(document).ready(function() {
            // Show first category by default
            if (document.querySelector('.category-products')) {
                document.querySelector('.category-products').classList.add('active');
                document.querySelector('.category-card').classList.add('bg-blue-100', 'border-blue-300');
            }
        });
    </script>
</body>
</html>
