<!-- Header -->
<header class="bg-white shadow-sm border-b border-gray-200 fixed top-0 left-0 right-0 z-50">
    <div class="flex items-center justify-between px-4 py-3">
        <!-- Logo and Menu Toggle -->
        <div class="flex items-center space-x-4">
            <button onclick="toggleMobileSidebar()" class="text-gray-600 hover:text-gray-900 lg:hidden p-2 rounded-lg hover:bg-gray-100 transition-colors">
                <i class="fas fa-bars text-xl"></i>
            </button>
            <div class="hidden lg:block">
                <h1 class="text-xl font-bold text-gray-900">POS System</h1>
            </div>
        </div>

        <!-- Search Bar -->
        <div class="hidden md:flex flex-1 max-w-md mx-8">
            <div class="relative w-full">
                <input type="text" placeholder="Search products..." 
                       class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-search text-gray-400"></i>
                </div>
            </div>
        </div>

        <!-- Right Side Menu -->
        <div class="flex items-center space-x-4">
            <!-- Notifications -->
            <div class="relative">
                <button class="text-gray-600 hover:text-gray-900 relative">
                    <i class="fas fa-bell text-xl"></i>
                    <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">3</span>
                </button>
            </div>

            <!-- User Menu -->
            <div class="relative">
                <button id="user-menu-button" class="flex items-center space-x-2 text-gray-700 hover:text-gray-900">
                    <img class="h-8 w-8 rounded-full" src="https://ui-avatars.com/api/?name=<?= $_SESSION['username'] ?>&background=random" alt="User">
                    <span class="hidden md:block text-sm font-medium"><?= $_SESSION['username'] ?></span>
                    <i class="fas fa-chevron-down text-xs"></i>
                </button>

                <!-- Dropdown Menu -->
                <div id="user-dropdown" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50">
                    <a href="profile.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                        <i class="fas fa-user mr-2"></i>Profile
                    </a>
                    <a href="change-password.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                        <i class="fas fa-key mr-2"></i>Change Password
                    </a>
                    <hr class="my-1">
                    <a href="logout.php" class="block px-4 py-2 text-sm text-red-600 hover:bg-gray-100">
                        <i class="fas fa-sign-out-alt mr-2"></i>Logout
                    </a>
                </div>
            </div>
        </div>
    </div>
</header>

<script>
$(document).ready(function() {
    // User menu toggle
    $('#user-menu-button').click(function(e) {
        e.stopPropagation();
        $('#user-dropdown').toggleClass('hidden');
    });

    // Close dropdown when clicking outside
    $(document).click(function() {
        $('#user-dropdown').addClass('hidden');
    });
});

// Mobile sidebar toggle function
function toggleMobileSidebar() {
    const sidebar = document.querySelector('.sidebar');
    const overlay = document.getElementById('sidebar-overlay');
    
    if (!sidebar) {
        console.error('Sidebar not found');
        return;
    }
    
    const isOpen = sidebar.style.transform === 'translateX(0px)' || sidebar.style.transform === '';
    
    if (isOpen) {
        // Close sidebar
        sidebar.style.transform = 'translateX(-100%)';
        if (overlay) overlay.classList.add('hidden');
    } else {
        // Open sidebar
        sidebar.style.transform = 'translateX(0)';
        if (overlay) overlay.classList.remove('hidden');
    }
}
</script>
