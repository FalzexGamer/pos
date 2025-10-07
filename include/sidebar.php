<!-- Mobile overlay -->
<div id="sidebar-overlay" class="fixed inset-0 bg-black bg-opacity-50 z-30 lg:hidden hidden" onclick="toggleMobileSidebar()"></div>

<!-- Sidebar -->
<aside class="sidebar fixed left-0 top-0 h-full w-64 bg-white shadow-lg transform transition-transform duration-300 ease-in-out z-40" style="top: 64px;">
    <div class="p-4 h-full overflow-y-auto">
        <!-- Close button for mobile -->
        <div class="flex justify-between items-center mb-4 lg:hidden">
            <h2 class="text-lg font-semibold text-gray-800">Menu</h2>
            <button onclick="toggleMobileSidebar()" class="text-gray-600 hover:text-gray-900 p-2 rounded-lg hover:bg-gray-100 transition-colors">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <nav class="space-y-2">
            <!-- Dashboard -->
            <?php if ($user_permissions['access_dashboard'] == 1): ?>
            <a href="dashboard.php" class="flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-blue-50 hover:text-blue-600 transition-colors">
                <i class="fas fa-tachometer-alt w-5 h-5 mr-3"></i>
                <span>Dashboard</span>
            </a>
            <?php endif; ?>

            <!-- POS -->
            <?php if ($user_permissions['access_pos'] == 1): ?>
            <a href="pos.php" class="flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-blue-50 hover:text-blue-600 transition-colors">
                <i class="fas fa-cash-register w-5 h-5 mr-3"></i>
                <span>Point of Sale</span>
            </a>
            <?php endif; ?>

            <!-- Customer Orders -->
            <?php if ($user_permissions['access_customer_orders'] == 1): ?>
            <a href="order-list.php" class="flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-blue-50 hover:text-blue-600 transition-colors">
                <i class="fas fa-list-alt w-5 h-5 mr-3"></i>
                <span>Customer Orders</span>
            </a>
            <?php endif; ?>

            <!-- Customer Order Entry -->
            <?php if ($user_permissions['access_customer_order'] == 1): ?>
            <a href="customer-order.php" class="flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-blue-50 hover:text-blue-600 transition-colors">
                <i class="fas fa-plus-circle w-5 h-5 mr-3"></i>
                <span>Customer Order Entry</span>
            </a>
            <?php endif; ?>

            <!-- Sales -->
            <?php if ($user_permissions['access_sales'] == 1 || $user_permissions['access_sales_report'] == 1 || $user_permissions['access_opening_closing'] == 1): ?>
            <div class="space-y-1">
                <button class="w-full flex items-center justify-between px-4 py-3 text-gray-700 rounded-lg hover:bg-blue-50 hover:text-blue-600 transition-colors" onclick="toggleSubmenu('sales-submenu')">
                    <div class="flex items-center">
                        <i class="fas fa-shopping-cart w-5 h-5 mr-3"></i>
                        <span>Sales</span>
                    </div>
                    <i class="fas fa-chevron-down text-xs transition-transform" id="sales-arrow"></i>
                </button>
                <div id="sales-submenu" class="hidden pl-8 space-y-1">
                    <?php if ($user_permissions['access_sales'] == 1): ?>
                    <a href="sales.php" class="block px-4 py-2 text-sm text-gray-600 rounded-lg hover:bg-blue-50 hover:text-blue-600 transition-colors">
                        <i class="fas fa-list mr-2"></i>Sales History
                    </a>
                    <?php endif; ?>
                    <?php if ($user_permissions['access_sales_report'] == 1): ?>
                    <a href="sales-report.php" class="block px-4 py-2 text-sm text-gray-600 rounded-lg hover:bg-blue-50 hover:text-blue-600 transition-colors">
                        <i class="fas fa-chart-bar mr-2"></i>Sales Report
                    </a>
                    <?php endif; ?>
                    <?php if ($user_permissions['access_opening_closing'] == 1): ?>
                    <a href="opening-closing.php" class="block px-4 py-2 text-sm text-gray-600 rounded-lg hover:bg-blue-50 hover:text-blue-600 transition-colors">
                        <i class="fas fa-door-open mr-2"></i>Opening & Closing
                    </a>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- Inventory -->
            <?php if ($user_permissions['access_products'] == 1 || $user_permissions['access_categories'] == 1 || $user_permissions['access_suppliers'] == 1 || $user_permissions['access_uom'] == 1 || $user_permissions['access_stock_take'] == 1 || $user_permissions['access_inventory_report'] == 1): ?>
            <div class="space-y-1">
                <button class="w-full flex items-center justify-between px-4 py-3 text-gray-700 rounded-lg hover:bg-blue-50 hover:text-blue-600 transition-colors" onclick="toggleSubmenu('inventory-submenu')">
                    <div class="flex items-center">
                        <i class="fas fa-boxes w-5 h-5 mr-3"></i>
                        <span>Inventory</span>
                    </div>
                    <i class="fas fa-chevron-down text-xs transition-transform" id="inventory-arrow"></i>
                </button>
                <div id="inventory-submenu" class="hidden pl-8 space-y-1">
                    <?php if ($user_permissions['access_products'] == 1): ?>
                    <a href="products.php" class="block px-4 py-2 text-sm text-gray-600 rounded-lg hover:bg-blue-50 hover:text-blue-600 transition-colors">
                        <i class="fas fa-box mr-2"></i>Products
                    </a>
                    <?php endif; ?>
                    <?php if ($user_permissions['access_categories'] == 1): ?>
                    <a href="categories.php" class="block px-4 py-2 text-sm text-gray-600 rounded-lg hover:bg-blue-50 hover:text-blue-600 transition-colors">
                        <i class="fas fa-tags mr-2"></i>Categories
                    </a>
                    <?php endif; ?>
                    <?php if ($user_permissions['access_suppliers'] == 1): ?>
                    <a href="suppliers.php" class="block px-4 py-2 text-sm text-gray-600 rounded-lg hover:bg-blue-50 hover:text-blue-600 transition-colors">
                        <i class="fas fa-truck mr-2"></i>Suppliers
                    </a>
                    <?php endif; ?>
                    <?php if ($user_permissions['access_uom'] == 1): ?>
                    <a href="uom.php" class="block px-4 py-2 text-sm text-gray-600 rounded-lg hover:bg-blue-50 hover:text-blue-600 transition-colors">
                        <i class="fas fa-ruler mr-2"></i>Units of Measure
                    </a>
                    <?php endif; ?>
                    <?php if ($user_permissions['access_stock_take'] == 1): ?>
                    <a href="stock-take.php" class="block px-4 py-2 text-sm text-gray-600 rounded-lg hover:bg-blue-50 hover:text-blue-600 transition-colors">
                        <i class="fas fa-clipboard-check mr-2"></i>Stock Take
                    </a>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- Members -->
            <?php if ($user_permissions['access_members'] == 1): ?>
            <div class="space-y-1">
                <button class="w-full flex items-center justify-between px-4 py-3 text-gray-700 rounded-lg hover:bg-blue-50 hover:text-blue-600 transition-colors" onclick="toggleSubmenu('members-submenu')">
                    <div class="flex items-center">
                        <i class="fas fa-users w-5 h-5 mr-3"></i>
                        <span>Members</span>
                    </div>
                    <i class="fas fa-chevron-down text-xs transition-transform" id="members-arrow"></i>
                </button>
                <div id="members-submenu" class="hidden pl-8 space-y-1">
                    <a href="members.php" class="block px-4 py-2 text-sm text-gray-600 rounded-lg hover:bg-blue-50 hover:text-blue-600 transition-colors">
                        <i class="fas fa-user mr-2"></i>All Members
                    </a>
                    <a href="membership-tiers.php" class="block px-4 py-2 text-sm text-gray-600 rounded-lg hover:bg-blue-50 hover:text-blue-600 transition-colors">
                        <i class="fas fa-crown mr-2"></i>Membership Tiers
                    </a>
                </div>
            </div>
            <?php endif; ?>

            <!-- Reports -->
            <?php if ($user_permissions['access_inventory_report'] == 1 || $user_permissions['access_member_report'] == 1 || $user_permissions['access_profit_loss'] == 1): ?>
            <div class="space-y-1">
                <button class="w-full flex items-center justify-between px-4 py-3 text-gray-700 rounded-lg hover:bg-blue-50 hover:text-blue-600 transition-colors" onclick="toggleSubmenu('reports-submenu')">
                    <div class="flex items-center">
                        <i class="fas fa-chart-line w-5 h-5 mr-3"></i>
                        <span>Reports</span>
                    </div>
                    <i class="fas fa-chevron-down text-xs transition-transform" id="reports-arrow"></i>
                </button>
                <div id="reports-submenu" class="hidden pl-8 space-y-1">
                    <?php if ($user_permissions['access_inventory_report'] == 1): ?>
                    <a href="inventory-report.php" class="block px-4 py-2 text-sm text-gray-600 rounded-lg hover:bg-blue-50 hover:text-blue-600 transition-colors">
                        <i class="fas fa-boxes mr-2"></i>Inventory Report
                    </a>
                    <?php endif; ?>
                    <?php if ($user_permissions['access_member_report'] == 1): ?>
                    <a href="member-report.php" class="block px-4 py-2 text-sm text-gray-600 rounded-lg hover:bg-blue-50 hover:text-blue-600 transition-colors">
                        <i class="fas fa-users mr-2"></i>Member Report
                    </a>
                    <?php endif; ?>
                    <?php if ($user_permissions['access_profit_loss'] == 1): ?>
                    <a href="profit-loss.php" class="block px-4 py-2 text-sm text-gray-600 rounded-lg hover:bg-blue-50 hover:text-blue-600 transition-colors">
                        <i class="fas fa-chart-pie mr-2"></i>Profit & Loss
                    </a>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- Settings -->
            <?php if ($_SESSION['role'] == 'admin'): ?>
            <div class="space-y-1">
                <button class="w-full flex items-center justify-between px-4 py-3 text-gray-700 rounded-lg hover:bg-blue-50 hover:text-blue-600 transition-colors" onclick="toggleSubmenu('settings-submenu')">
                    <div class="flex items-center">
                        <i class="fas fa-cog w-5 h-5 mr-3"></i>
                        <span>Settings</span>
                    </div>
                    <i class="fas fa-chevron-down text-xs transition-transform" id="settings-arrow"></i>
                </button>
                <div id="settings-submenu" class="hidden pl-8 space-y-1">
                    <a href="users.php" class="block px-4 py-2 text-sm text-gray-600 rounded-lg hover:bg-blue-50 hover:text-blue-600 transition-colors">
                        <i class="fas fa-user-cog mr-2"></i>Users
                    </a>
                    <a href="access-level.php" class="block px-4 py-2 text-sm text-gray-600 rounded-lg hover:bg-blue-50 hover:text-blue-600 transition-colors">
                        <i class="fas fa-shield-alt mr-2"></i>Access Level
                    </a>
                    <a href="company-settings.php" class="block px-4 py-2 text-sm text-gray-600 rounded-lg hover:bg-blue-50 hover:text-blue-600 transition-colors">
                        <i class="fas fa-building mr-2"></i>Company Settings
                    </a>
                    <a href="payment-methods.php" class="block px-4 py-2 text-sm text-gray-600 rounded-lg hover:bg-blue-50 hover:text-blue-600 transition-colors">
                        <i class="fas fa-credit-card mr-2"></i>Payment Methods
                    </a>
                    <a href="backup.php" class="block px-4 py-2 text-sm text-gray-600 rounded-lg hover:bg-blue-50 hover:text-blue-600 transition-colors">
                        <i class="fas fa-database mr-2"></i>Backup
                    </a>
                </div>
            </div>
            <?php endif; ?>
        </nav>
    </div>
</aside>

<script>
function toggleSubmenu(submenuId) {
    const submenu = document.getElementById(submenuId);
    const arrow = document.getElementById(submenuId.replace('-submenu', '-arrow'));
    
    submenu.classList.toggle('hidden');
    arrow.style.transform = submenu.classList.contains('hidden') ? 'rotate(0deg)' : 'rotate(180deg)';
}

// Initialize sidebar for mobile
document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.querySelector('.sidebar');
    const overlay = document.getElementById('sidebar-overlay');
    
    if (!sidebar) {
        console.error('Sidebar not found');
        return;
    }
    
    // Start with sidebar closed on mobile
    if (window.innerWidth < 1024) {
        sidebar.style.transform = 'translateX(-100%)';
        if (overlay) overlay.classList.add('hidden');
        console.log('Mobile: Sidebar initialized as closed');
    } else {
        // Desktop - ensure sidebar is visible
        sidebar.style.transform = 'translateX(0)';
        if (overlay) overlay.classList.add('hidden');
        console.log('Desktop: Sidebar initialized as open');
    }
});

// Handle window resize
window.addEventListener('resize', function() {
    const sidebar = document.querySelector('.sidebar');
    const overlay = document.getElementById('sidebar-overlay');
    
    if (window.innerWidth >= 1024) {
        // Desktop - show sidebar
        sidebar.style.transform = 'translateX(0)';
        if (overlay) overlay.classList.add('hidden');
    } else {
        // Mobile - hide sidebar
        sidebar.style.transform = 'translateX(-100%)';
        if (overlay) overlay.classList.add('hidden');
    }
});

// Highlight current page
$(document).ready(function() {
    const currentPage = window.location.pathname.split('/').pop();
    $('a[href="' + currentPage + '"]').addClass('bg-blue-50 text-blue-600');
    
    // Close mobile sidebar when clicking on a link
    $('a').click(function() {
        if (window.innerWidth < 1024) {
            toggleMobileSidebar();
        }
    });
});
</script>
