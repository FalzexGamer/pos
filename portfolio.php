<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>POS System Portfolio - Complete Point of Sale Solution</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <style>
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .feature-card {
            transition: all 0.3s ease;
        }
        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        }
        .stats-counter {
            font-size: 3rem;
            font-weight: bold;
            background: linear-gradient(45deg, #667eea, #764ba2);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .tech-badge {
            background: linear-gradient(45deg, #667eea, #764ba2);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 25px;
            font-size: 0.875rem;
            font-weight: 500;
            margin: 0.25rem;
            display: inline-block;
        }
        .demo-section {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        }
        .screenshot-container {
            position: relative;
            overflow: hidden;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        .screenshot-container img {
            width: 100%;
            height: auto;
            transition: transform 0.3s ease;
        }
        .screenshot-container:hover img {
            transform: scale(1.05);
        }
        .floating-animation {
            animation: float 6s ease-in-out infinite;
        }
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }
        .pulse-animation {
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white shadow-lg fixed w-full z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-cash-register text-2xl text-blue-600"></i>
                        <span class="ml-2 text-xl font-bold text-gray-900">POS System</span>
                    </div>
                </div>
                <div class="hidden md:flex items-center space-x-8">
                    <a href="#features" class="text-gray-700 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium">Features</a>
                    <a href="#modules" class="text-gray-700 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium">Modules</a>
                    <a href="#tech" class="text-gray-700 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium">Technology</a>
                    <a href="#demo" class="text-gray-700 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium">Demo</a>
                    <a href="#contact" class="bg-blue-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-blue-700">Get Started</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="gradient-bg pt-20 pb-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <h1 class="text-4xl md:text-6xl font-bold text-white mb-6">
                    Complete POS System
                </h1>
                <p class="text-xl md:text-2xl text-blue-100 mb-8 max-w-3xl mx-auto">
                    A comprehensive Point of Sale solution with inventory management, membership system, stock take, and advanced reporting capabilities.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="#demo" class="bg-white text-blue-600 px-8 py-3 rounded-lg font-semibold hover:bg-gray-100 transition duration-300">
                        <i class="fas fa-play mr-2"></i>View Demo
                    </a>
                    <a href="#features" class="border-2 border-white text-white px-8 py-3 rounded-lg font-semibold hover:bg-white hover:text-blue-600 transition duration-300">
                        <i class="fas fa-info-circle mr-2"></i>Learn More
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
                <div class="p-6">
                    <div class="stats-counter">50+</div>
                    <p class="text-gray-600 font-medium">Features</p>
                </div>
                <div class="p-6">
                    <div class="stats-counter">15+</div>
                    <p class="text-gray-600 font-medium">Modules</p>
                </div>
                <div class="p-6">
                    <div class="stats-counter">100%</div>
                    <p class="text-gray-600 font-medium">Responsive</p>
                </div>
                <div class="p-6">
                    <div class="stats-counter">24/7</div>
                    <p class="text-gray-600 font-medium">Support</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Overview -->
    <section id="features" class="py-16 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Key Features</h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                    Everything you need to run a successful retail business, from sales processing to inventory management.
                </p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Point of Sale -->
                <div class="feature-card bg-white p-8 rounded-xl shadow-lg">
                    <div class="text-center">
                        <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-cash-register text-2xl text-blue-600"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-4">Point of Sale</h3>
                        <ul class="text-gray-600 space-y-2 text-left">
                            <li><i class="fas fa-check text-green-500 mr-2"></i>Barcode scanning & manual search</li>
                            <li><i class="fas fa-check text-green-500 mr-2"></i>Multiple payment methods</li>
                            <li><i class="fas fa-check text-green-500 mr-2"></i>Discount & voucher support</li>
                            <li><i class="fas fa-check text-green-500 mr-2"></i>Receipt printing</li>
                            <li><i class="fas fa-check text-green-500 mr-2"></i>Real-time stock updates</li>
                        </ul>
                    </div>
                </div>

                <!-- Inventory Management -->
                <div class="feature-card bg-white p-8 rounded-xl shadow-lg">
                    <div class="text-center">
                        <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-boxes text-2xl text-green-600"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-4">Inventory Management</h3>
                        <ul class="text-gray-600 space-y-2 text-left">
                            <li><i class="fas fa-check text-green-500 mr-2"></i>Product management with SKU</li>
                            <li><i class="fas fa-check text-green-500 mr-2"></i>Category organization</li>
                            <li><i class="fas fa-check text-green-500 mr-2"></i>Supplier tracking</li>
                            <li><i class="fas fa-check text-green-500 mr-2"></i>Stock alerts</li>
                            <li><i class="fas fa-check text-green-500 mr-2"></i>Unit of measurement</li>
                        </ul>
                    </div>
                </div>

                <!-- Membership System -->
                <div class="feature-card bg-white p-8 rounded-xl shadow-lg">
                    <div class="text-center">
                        <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-users text-2xl text-purple-600"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-4">Membership System</h3>
                        <ul class="text-gray-600 space-y-2 text-left">
                            <li><i class="fas fa-check text-green-500 mr-2"></i>Member management</li>
                            <li><i class="fas fa-check text-green-500 mr-2"></i>Purchase history tracking</li>
                            <li><i class="fas fa-check text-green-500 mr-2"></i>Membership tiers</li>
                            <li><i class="fas fa-check text-green-500 mr-2"></i>Points system</li>
                            <li><i class="fas fa-check text-green-500 mr-2"></i>Discount management</li>
                        </ul>
                    </div>
                </div>

                <!-- Stock Take System -->
                <div class="feature-card bg-white p-8 rounded-xl shadow-lg">
                    <div class="text-center">
                        <div class="w-16 h-16 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-clipboard-check text-2xl text-orange-600"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-4">Stock Take System</h3>
                        <ul class="text-gray-600 space-y-2 text-left">
                            <li><i class="fas fa-check text-green-500 mr-2"></i>Stock take sessions</li>
                            <li><i class="fas fa-check text-green-500 mr-2"></i>Physical count verification</li>
                            <li><i class="fas fa-check text-green-500 mr-2"></i>Discrepancy management</li>
                            <li><i class="fas fa-check text-green-500 mr-2"></i>History logging</li>
                            <li><i class="fas fa-check text-green-500 mr-2"></i>Reporting & analysis</li>
                        </ul>
                    </div>
                </div>

                <!-- Reporting & Analytics -->
                <div class="feature-card bg-white p-8 rounded-xl shadow-lg">
                    <div class="text-center">
                        <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-chart-line text-2xl text-red-600"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-4">Reporting & Analytics</h3>
                        <ul class="text-gray-600 space-y-2 text-left">
                            <li><i class="fas fa-check text-green-500 mr-2"></i>Sales reports with charts</li>
                            <li><i class="fas fa-check text-green-500 mr-2"></i>Inventory reports</li>
                            <li><i class="fas fa-check text-green-500 mr-2"></i>Member activity analysis</li>
                            <li><i class="fas fa-check text-green-500 mr-2"></i>Profit & loss tracking</li>
                            <li><i class="fas fa-check text-green-500 mr-2"></i>Export capabilities</li>
                        </ul>
                    </div>
                </div>

                <!-- Customer Ordering -->
                <div class="feature-card bg-white p-8 rounded-xl shadow-lg">
                    <div class="text-center">
                        <div class="w-16 h-16 bg-indigo-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-shopping-cart text-2xl text-indigo-600"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-4">Customer Ordering</h3>
                        <ul class="text-gray-600 space-y-2 text-left">
                            <li><i class="fas fa-check text-green-500 mr-2"></i>Table-based ordering</li>
                            <li><i class="fas fa-check text-green-500 mr-2"></i>QR code system</li>
                            <li><i class="fas fa-check text-green-500 mr-2"></i>Order management</li>
                            <li><i class="fas fa-check text-green-500 mr-2"></i>Status tracking</li>
                            <li><i class="fas fa-check text-green-500 mr-2"></i>Receipt generation</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Detailed Modules Section -->
    <section id="modules" class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">System Modules</h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                    Comprehensive modules covering every aspect of retail business management.
                </p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
                <!-- POS Module -->
                <div class="bg-gradient-to-br from-blue-50 to-blue-100 p-8 rounded-xl">
                    <div class="flex items-center mb-6">
                        <div class="w-12 h-12 bg-blue-600 rounded-lg flex items-center justify-center mr-4">
                            <i class="fas fa-cash-register text-white text-xl"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900">Point of Sale Module</h3>
                    </div>
                    <div class="space-y-4">
                        <div class="flex items-start">
                            <i class="fas fa-barcode text-blue-600 mt-1 mr-3"></i>
                            <div>
                                <h4 class="font-semibold text-gray-900">Barcode Scanning</h4>
                                <p class="text-gray-600">Support for barcode scanning and manual product search</p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <i class="fas fa-credit-card text-blue-600 mt-1 mr-3"></i>
                            <div>
                                <h4 class="font-semibold text-gray-900">Payment Processing</h4>
                                <p class="text-gray-600">Multiple payment methods including cash, card, and e-wallet</p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <i class="fas fa-receipt text-blue-600 mt-1 mr-3"></i>
                            <div>
                                <h4 class="font-semibold text-gray-900">Receipt Printing</h4>
                                <p class="text-gray-600">Automatic receipt generation with thermal printer support</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Inventory Module -->
                <div class="bg-gradient-to-br from-green-50 to-green-100 p-8 rounded-xl">
                    <div class="flex items-center mb-6">
                        <div class="w-12 h-12 bg-green-600 rounded-lg flex items-center justify-center mr-4">
                            <i class="fas fa-boxes text-white text-xl"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900">Inventory Module</h3>
                    </div>
                    <div class="space-y-4">
                        <div class="flex items-start">
                            <i class="fas fa-tags text-green-600 mt-1 mr-3"></i>
                            <div>
                                <h4 class="font-semibold text-gray-900">Product Management</h4>
                                <p class="text-gray-600">Complete product lifecycle management with SKU tracking</p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <i class="fas fa-layer-group text-green-600 mt-1 mr-3"></i>
                            <div>
                                <h4 class="font-semibold text-gray-900">Category Organization</h4>
                                <p class="text-gray-600">Hierarchical category system for product organization</p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <i class="fas fa-truck text-green-600 mt-1 mr-3"></i>
                            <div>
                                <h4 class="font-semibold text-gray-900">Supplier Management</h4>
                                <p class="text-gray-600">Track suppliers and manage purchase relationships</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Membership Module -->
                <div class="bg-gradient-to-br from-purple-50 to-purple-100 p-8 rounded-xl">
                    <div class="flex items-center mb-6">
                        <div class="w-12 h-12 bg-purple-600 rounded-lg flex items-center justify-center mr-4">
                            <i class="fas fa-users text-white text-xl"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900">Membership Module</h3>
                    </div>
                    <div class="space-y-4">
                        <div class="flex items-start">
                            <i class="fas fa-user-plus text-purple-600 mt-1 mr-3"></i>
                            <div>
                                <h4 class="font-semibold text-gray-900">Member Registration</h4>
                                <p class="text-gray-600">Easy member registration with detailed profile management</p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <i class="fas fa-crown text-purple-600 mt-1 mr-3"></i>
                            <div>
                                <h4 class="font-semibold text-gray-900">Membership Tiers</h4>
                                <p class="text-gray-600">Regular, Gold, and Platinum tiers with different benefits</p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <i class="fas fa-history text-purple-600 mt-1 mr-3"></i>
                            <div>
                                <h4 class="font-semibold text-gray-900">Purchase History</h4>
                                <p class="text-gray-600">Complete transaction history and spending analysis</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Reporting Module -->
                <div class="bg-gradient-to-br from-red-50 to-red-100 p-8 rounded-xl">
                    <div class="flex items-center mb-6">
                        <div class="w-12 h-12 bg-red-600 rounded-lg flex items-center justify-center mr-4">
                            <i class="fas fa-chart-line text-white text-xl"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900">Reporting Module</h3>
                    </div>
                    <div class="space-y-4">
                        <div class="flex items-start">
                            <i class="fas fa-chart-bar text-red-600 mt-1 mr-3"></i>
                            <div>
                                <h4 class="font-semibold text-gray-900">Sales Analytics</h4>
                                <p class="text-gray-600">Comprehensive sales reports with interactive charts</p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <i class="fas fa-download text-red-600 mt-1 mr-3"></i>
                            <div>
                                <h4 class="font-semibold text-gray-900">Export Capabilities</h4>
                                <p class="text-gray-600">Export reports to CSV and print functionality</p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <i class="fas fa-trending-up text-red-600 mt-1 mr-3"></i>
                            <div>
                                <h4 class="font-semibold text-gray-900">Performance Tracking</h4>
                                <p class="text-gray-600">Track business performance with detailed metrics</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Technology Stack -->
    <section id="tech" class="py-16 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Technology Stack</h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                    Built with modern technologies for performance, security, and scalability.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Backend -->
                <div class="bg-white p-8 rounded-xl shadow-lg">
                    <div class="text-center mb-6">
                        <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fab fa-php text-3xl text-blue-600"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900">Backend</h3>
                    </div>
                    <div class="space-y-3">
                        <div class="tech-badge">PHP 7.4+</div>
                        <div class="tech-badge">MySQLi</div>
                        <div class="tech-badge">Procedural PHP</div>
                        <div class="tech-badge">Prepared Statements</div>
                    </div>
                </div>

                <!-- Frontend -->
                <div class="bg-white p-8 rounded-xl shadow-lg">
                    <div class="text-center mb-6">
                        <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fab fa-html5 text-3xl text-green-600"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900">Frontend</h3>
                    </div>
                    <div class="space-y-3">
                        <div class="tech-badge">Tailwind CSS</div>
                        <div class="tech-badge">jQuery</div>
                        <div class="tech-badge">DataTables</div>
                        <div class="tech-badge">ApexCharts</div>
                    </div>
                </div>

                <!-- Database -->
                <div class="bg-white p-8 rounded-xl shadow-lg">
                    <div class="text-center mb-6">
                        <div class="w-16 h-16 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-database text-3xl text-orange-600"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900">Database</h3>
                    </div>
                    <div class="space-y-3">
                        <div class="tech-badge">MySQL 5.7+</div>
                        <div class="tech-badge">Optimized Indexes</div>
                        <div class="tech-badge">ACID Compliance</div>
                        <div class="tech-badge">Backup System</div>
                    </div>
                </div>
            </div>

            <!-- Security Features -->
            <div class="mt-16 bg-white p-8 rounded-xl shadow-lg">
                <h3 class="text-2xl font-bold text-gray-900 mb-8 text-center">Security Features</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div class="text-center">
                        <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-3">
                            <i class="fas fa-shield-alt text-red-600 text-xl"></i>
                        </div>
                        <h4 class="font-semibold text-gray-900">SQL Injection Protection</h4>
                        <p class="text-gray-600 text-sm">Prepared statements and input sanitization</p>
                    </div>
                    <div class="text-center">
                        <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-3">
                            <i class="fas fa-lock text-blue-600 text-xl"></i>
                        </div>
                        <h4 class="font-semibold text-gray-900">XSS Protection</h4>
                        <p class="text-gray-600 text-sm">Output encoding and sanitization</p>
                    </div>
                    <div class="text-center">
                        <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-3">
                            <i class="fas fa-user-shield text-green-600 text-xl"></i>
                        </div>
                        <h4 class="font-semibold text-gray-900">Role-based Access</h4>
                        <p class="text-gray-600 text-sm">Admin, Manager, Cashier roles</p>
                    </div>
                    <div class="text-center">
                        <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-3">
                            <i class="fas fa-key text-purple-600 text-xl"></i>
                        </div>
                        <h4 class="font-semibold text-gray-900">Session Management</h4>
                        <p class="text-gray-600 text-sm">Secure session handling</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Interactive Sandbox Section -->
    <section id="demo" class="demo-section py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Interactive Sandbox</h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                    Experience the power of our POS system through live interactive demonstrations.
                </p>
            </div>

            <!-- Sandbox Navigation -->
            <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
                <div class="flex flex-wrap justify-center gap-4">
                    <button onclick="showSandbox('pos')" class="sandbox-tab active bg-blue-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-blue-700 transition duration-300">
                        <i class="fas fa-cash-register mr-2"></i>POS Interface
                    </button>
                    <button onclick="showSandbox('inventory')" class="sandbox-tab bg-gray-200 text-gray-700 px-6 py-3 rounded-lg font-semibold hover:bg-gray-300 transition duration-300">
                        <i class="fas fa-boxes mr-2"></i>Inventory
                    </button>
                    <button onclick="showSandbox('members')" class="sandbox-tab bg-gray-200 text-gray-700 px-6 py-3 rounded-lg font-semibold hover:bg-gray-300 transition duration-300">
                        <i class="fas fa-users mr-2"></i>Members
                    </button>
                    <button onclick="showSandbox('reports')" class="sandbox-tab bg-gray-200 text-gray-700 px-6 py-3 rounded-lg font-semibold hover:bg-gray-300 transition duration-300">
                        <i class="fas fa-chart-line mr-2"></i>Reports
                    </button>
                </div>
            </div>

            <!-- POS Sandbox -->
            <div id="pos-sandbox" class="sandbox-content">
                <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                    <div class="bg-gradient-to-r from-blue-600 to-blue-700 text-white p-4">
                        <h3 class="text-xl font-bold">POS System - Live Demo</h3>
                        <p class="text-blue-100">Interactive Point of Sale Interface</p>
                    </div>
                    
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-0">
                        <!-- Product Categories -->
                        <div class="bg-gray-50 p-6 border-r">
                            <h4 class="font-bold text-gray-900 mb-4">Product Categories</h4>
                            <div class="space-y-2">
                                <button onclick="addToCart('electronics')" class="w-full text-left p-3 bg-white rounded-lg shadow-sm hover:shadow-md transition duration-200 border-l-4 border-blue-500">
                                    <div class="flex items-center">
                                        <i class="fas fa-laptop text-blue-600 mr-3"></i>
                                        <div>
                                            <div class="font-semibold">Electronics</div>
                                            <div class="text-sm text-gray-500">4 products</div>
                                        </div>
                                    </div>
                                </button>
                                <button onclick="addToCart('clothing')" class="w-full text-left p-3 bg-white rounded-lg shadow-sm hover:shadow-md transition duration-200 border-l-4 border-green-500">
                                    <div class="flex items-center">
                                        <i class="fas fa-tshirt text-green-600 mr-3"></i>
                                        <div>
                                            <div class="font-semibold">Clothing</div>
                                            <div class="text-sm text-gray-500">3 products</div>
                                        </div>
                                    </div>
                                </button>
                                <button onclick="addToCart('food')" class="w-full text-left p-3 bg-white rounded-lg shadow-sm hover:shadow-md transition duration-200 border-l-4 border-orange-500">
                                    <div class="flex items-center">
                                        <i class="fas fa-utensils text-orange-600 mr-3"></i>
                                        <div>
                                            <div class="font-semibold">Food & Beverages</div>
                                            <div class="text-sm text-gray-500">3 products</div>
                                        </div>
                                    </div>
                                </button>
                            </div>
                        </div>

                        <!-- Shopping Cart -->
                        <div class="p-6">
                            <h4 class="font-bold text-gray-900 mb-4">Shopping Cart</h4>
                            <div class="mb-4">
                                <input type="text" id="barcode-input" placeholder="Scan barcode or enter SKU..." 
                                       class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <div class="flex gap-2 mt-2">
                                    <button onclick="scanBarcode()" class="bg-purple-600 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-purple-700">
                                        <i class="fas fa-qrcode mr-1"></i>Scan QR
                                    </button>
                                    <button onclick="clearCart()" class="bg-red-600 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-red-700">
                                        <i class="fas fa-trash mr-1"></i>Clear Cart
                                    </button>
                                </div>
                            </div>
                            
                            <div id="cart-items" class="space-y-2 mb-4">
                                <div class="text-center text-gray-500 py-8">
                                    <i class="fas fa-shopping-cart text-4xl mb-2"></i>
                                    <p>Cart is Empty</p>
                                    <p class="text-sm">Start adding products to begin a sale</p>
                                </div>
                            </div>
                        </div>

                        <!-- Payment Summary -->
                        <div class="bg-gray-50 p-6">
                            <h4 class="font-bold text-gray-900 mb-4">Payment Summary</h4>
                            
                            <div class="space-y-3 mb-6">
                                <div class="flex justify-between">
                                    <span>Subtotal:</span>
                                    <span id="subtotal">RM 0.00</span>
                                </div>
                                <div class="flex justify-between text-green-600">
                                    <span>Discount:</span>
                                    <span id="discount">RM 0.00</span>
                                </div>
                                <div class="flex justify-between">
                                    <span>Tax (6%):</span>
                                    <span id="tax">RM 0.00</span>
                                </div>
                                <hr>
                                <div class="flex justify-between text-xl font-bold text-blue-600">
                                    <span>Total:</span>
                                    <span id="total">RM 0.00</span>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Customer</label>
                                <div class="flex">
                                    <input type="text" id="customer-input" value="Walk-in Customer" 
                                           class="flex-1 p-2 border border-gray-300 rounded-l-lg focus:ring-2 focus:ring-blue-500">
                                    <button class="bg-blue-600 text-white px-3 rounded-r-lg">
                                        <i class="fas fa-users"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="space-y-2">
                                <button onclick="processPayment('cash')" class="w-full bg-green-600 text-white py-3 rounded-lg font-semibold hover:bg-green-700 transition duration-200">
                                    <i class="fas fa-money-bill-wave mr-2"></i>Cash Payment
                                </button>
                                <button onclick="processPayment('card')" class="w-full bg-blue-600 text-white py-3 rounded-lg font-semibold hover:bg-blue-700 transition duration-200">
                                    <i class="fas fa-credit-card mr-2"></i>Online Payment
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Inventory Sandbox -->
            <div id="inventory-sandbox" class="sandbox-content hidden">
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-xl font-bold text-gray-900">Inventory Management</h3>
                        <button onclick="addProduct()" class="bg-green-600 text-white px-4 py-2 rounded-lg font-semibold hover:bg-green-700">
                            <i class="fas fa-plus mr-2"></i>Add Product
                        </button>
                    </div>
                    
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <div>
                            <h4 class="font-semibold text-gray-900 mb-4">Product List</h4>
                            <div class="space-y-2">
                                <div class="p-3 bg-gray-50 rounded-lg flex justify-between items-center">
                                    <div>
                                        <div class="font-semibold">iPhone 15 Pro</div>
                                        <div class="text-sm text-gray-500">SKU: IPH15P-001 | Stock: 5</div>
                                    </div>
                                    <div class="flex gap-2">
                                        <button class="text-blue-600 hover:text-blue-800">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="text-red-600 hover:text-red-800">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="p-3 bg-gray-50 rounded-lg flex justify-between items-center">
                                    <div>
                                        <div class="font-semibold">Samsung Galaxy S24</div>
                                        <div class="text-sm text-gray-500">SKU: SGS24-001 | Stock: 3</div>
                                    </div>
                                    <div class="flex gap-2">
                                        <button class="text-blue-600 hover:text-blue-800">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="text-red-600 hover:text-red-800">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="p-3 bg-gray-50 rounded-lg flex justify-between items-center">
                                    <div>
                                        <div class="font-semibold">MacBook Air M2</div>
                                        <div class="text-sm text-gray-500">SKU: MBA-M2-001 | Stock: 2</div>
                                    </div>
                                    <div class="flex gap-2">
                                        <button class="text-blue-600 hover:text-blue-800">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="text-red-600 hover:text-red-800">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div>
                            <h4 class="font-semibold text-gray-900 mb-4">Stock Alerts</h4>
                            <div class="space-y-2">
                                <div class="p-3 bg-red-50 border-l-4 border-red-500 rounded-lg">
                                    <div class="font-semibold text-red-800">Low Stock Alert</div>
                                    <div class="text-sm text-red-600">MacBook Air M2 - Only 2 units left</div>
                                </div>
                                <div class="p-3 bg-yellow-50 border-l-4 border-yellow-500 rounded-lg">
                                    <div class="font-semibold text-yellow-800">Medium Stock Alert</div>
                                    <div class="text-sm text-yellow-600">Samsung Galaxy S24 - 3 units remaining</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Members Sandbox -->
            <div id="members-sandbox" class="sandbox-content hidden">
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-xl font-bold text-gray-900">Member Management</h3>
                        <button onclick="addMember()" class="bg-purple-600 text-white px-4 py-2 rounded-lg font-semibold hover:bg-purple-700">
                            <i class="fas fa-user-plus mr-2"></i>Add Member
                        </button>
                    </div>
                    
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        <div class="lg:col-span-2">
                            <h4 class="font-semibold text-gray-900 mb-4">Member List</h4>
                            <div class="space-y-3">
                                <div class="p-4 bg-gradient-to-r from-purple-50 to-purple-100 rounded-lg">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <div class="font-semibold text-gray-900">John Smith</div>
                                            <div class="text-sm text-gray-600">Gold Member | Total Spent: RM 2,450</div>
                                            <div class="text-sm text-gray-500">Phone: +60123456789</div>
                                        </div>
                                        <span class="bg-yellow-500 text-white px-2 py-1 rounded-full text-xs font-semibold">Gold</span>
                                    </div>
                                </div>
                                <div class="p-4 bg-gradient-to-r from-blue-50 to-blue-100 rounded-lg">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <div class="font-semibold text-gray-900">Sarah Johnson</div>
                                            <div class="text-sm text-gray-600">Platinum Member | Total Spent: RM 5,200</div>
                                            <div class="text-sm text-gray-500">Phone: +60198765432</div>
                                        </div>
                                        <span class="bg-purple-500 text-white px-2 py-1 rounded-full text-xs font-semibold">Platinum</span>
                                    </div>
                                </div>
                                <div class="p-4 bg-gradient-to-r from-green-50 to-green-100 rounded-lg">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <div class="font-semibold text-gray-900">Mike Wilson</div>
                                            <div class="text-sm text-gray-600">Regular Member | Total Spent: RM 850</div>
                                            <div class="text-sm text-gray-500">Phone: +60187654321</div>
                                        </div>
                                        <span class="bg-gray-500 text-white px-2 py-1 rounded-full text-xs font-semibold">Regular</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div>
                            <h4 class="font-semibold text-gray-900 mb-4">Membership Stats</h4>
                            <div class="space-y-4">
                                <div class="text-center p-4 bg-blue-50 rounded-lg">
                                    <div class="text-2xl font-bold text-blue-600">156</div>
                                    <div class="text-sm text-gray-600">Total Members</div>
                                </div>
                                <div class="text-center p-4 bg-green-50 rounded-lg">
                                    <div class="text-2xl font-bold text-green-600">23</div>
                                    <div class="text-sm text-gray-600">Platinum Members</div>
                                </div>
                                <div class="text-center p-4 bg-yellow-50 rounded-lg">
                                    <div class="text-2xl font-bold text-yellow-600">45</div>
                                    <div class="text-sm text-gray-600">Gold Members</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Reports Sandbox -->
            <div id="reports-sandbox" class="sandbox-content hidden">
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-xl font-bold text-gray-900">Analytics Dashboard</h3>
                        <div class="flex gap-2">
                            <button onclick="exportReport()" class="bg-green-600 text-white px-4 py-2 rounded-lg font-semibold hover:bg-green-700">
                                <i class="fas fa-download mr-2"></i>Export
                            </button>
                            <button onclick="printReport()" class="bg-blue-600 text-white px-4 py-2 rounded-lg font-semibold hover:bg-blue-700">
                                <i class="fas fa-print mr-2"></i>Print
                            </button>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <div>
                            <h4 class="font-semibold text-gray-900 mb-4">Sales Overview</h4>
                            <div class="space-y-4">
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="p-4 bg-blue-50 rounded-lg text-center">
                                        <div class="text-2xl font-bold text-blue-600">RM 12,450</div>
                                        <div class="text-sm text-gray-600">Today's Sales</div>
                                    </div>
                                    <div class="p-4 bg-green-50 rounded-lg text-center">
                                        <div class="text-2xl font-bold text-green-600">45</div>
                                        <div class="text-sm text-gray-600">Transactions</div>
                                    </div>
                                </div>
                                <div class="p-4 bg-purple-50 rounded-lg">
                                    <div class="font-semibold text-gray-900 mb-2">Top Products</div>
                                    <div class="space-y-2">
                                        <div class="flex justify-between">
                                            <span class="text-sm">iPhone 15 Pro</span>
                                            <span class="text-sm font-semibold">8 sold</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-sm">Samsung Galaxy S24</span>
                                            <span class="text-sm font-semibold">5 sold</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-sm">MacBook Air M2</span>
                                            <span class="text-sm font-semibold">3 sold</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div>
                            <h4 class="font-semibold text-gray-900 mb-4">Sales Chart</h4>
                            <div class="bg-gray-50 p-6 rounded-lg text-center">
                                <div class="text-gray-500 mb-4">
                                    <i class="fas fa-chart-line text-4xl"></i>
                                </div>
                                <p class="text-gray-600">Interactive sales chart would be displayed here</p>
                                <p class="text-sm text-gray-500 mt-2">Powered by ApexCharts</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Get Started Today</h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                    Ready to transform your retail business? Contact us to learn more about our POS system.
                </p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
                <div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-6">System Requirements</h3>
                    <div class="space-y-4">
                        <div class="flex items-start">
                            <i class="fas fa-server text-blue-600 mt-1 mr-3"></i>
                            <div>
                                <h4 class="font-semibold text-gray-900">Server Requirements</h4>
                                <p class="text-gray-600">PHP 7.4+, MySQL 5.7+, Apache/Nginx</p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <i class="fas fa-desktop text-blue-600 mt-1 mr-3"></i>
                            <div>
                                <h4 class="font-semibold text-gray-900">Browser Support</h4>
                                <p class="text-gray-600">Chrome, Firefox, Safari, Edge (latest versions)</p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <i class="fas fa-mobile-alt text-blue-600 mt-1 mr-3"></i>
                            <div>
                                <h4 class="font-semibold text-gray-900">Mobile Support</h4>
                                <p class="text-gray-600">Fully responsive design for all devices</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 p-8 rounded-xl">
                    <h3 class="text-2xl font-bold text-gray-900 mb-6">Quick Setup</h3>
                    <div class="space-y-4">
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-blue-600 text-white rounded-full flex items-center justify-center mr-4 text-sm font-bold">1</div>
                            <span class="text-gray-700">Import database structure</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-blue-600 text-white rounded-full flex items-center justify-center mr-4 text-sm font-bold">2</div>
                            <span class="text-gray-700">Configure database connection</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-blue-600 text-white rounded-full flex items-center justify-center mr-4 text-sm font-bold">3</div>
                            <span class="text-gray-700">Set file permissions</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-blue-600 text-white rounded-full flex items-center justify-center mr-4 text-sm font-bold">4</div>
                            <span class="text-gray-700">Access your POS system</span>
                        </div>
                    </div>
                    <div class="mt-8">
                        <a href="#" class="bg-blue-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-blue-700 transition duration-300 inline-block">
                            <i class="fas fa-download mr-2"></i>Download System
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div>
                    <div class="flex items-center mb-4">
                        <i class="fas fa-cash-register text-2xl text-blue-400 mr-3"></i>
                        <span class="text-xl font-bold">POS System</span>
                    </div>
                    <p class="text-gray-400">
                        A comprehensive Point of Sale solution for modern retail businesses.
                    </p>
                </div>
                <div>
                    <h3 class="text-lg font-semibold mb-4">Features</h3>
                    <ul class="space-y-2 text-gray-400">
                        <li>Point of Sale</li>
                        <li>Inventory Management</li>
                        <li>Membership System</li>
                        <li>Reporting & Analytics</li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-lg font-semibold mb-4">Technology</h3>
                    <ul class="space-y-2 text-gray-400">
                        <li>PHP & MySQL</li>
                        <li>Tailwind CSS</li>
                        <li>jQuery & ApexCharts</li>
                        <li>Responsive Design</li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-800 mt-8 pt-8 text-center text-gray-400">
                <p>&copy; 2024 POS System. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script>
        // Sandbox functionality
        let cart = [];
        let cartTotal = 0;

        // Show specific sandbox
        function showSandbox(type) {
            // Hide all sandbox content
            document.querySelectorAll('.sandbox-content').forEach(content => {
                content.classList.add('hidden');
            });
            
            // Remove active class from all tabs
            document.querySelectorAll('.sandbox-tab').forEach(tab => {
                tab.classList.remove('active', 'bg-blue-600', 'text-white');
                tab.classList.add('bg-gray-200', 'text-gray-700');
            });
            
            // Show selected sandbox
            document.getElementById(type + '-sandbox').classList.remove('hidden');
            
            // Add active class to clicked tab
            event.target.classList.remove('bg-gray-200', 'text-gray-700');
            event.target.classList.add('active', 'bg-blue-600', 'text-white');
        }

        // Add product to cart
        function addToCart(category) {
            const products = {
                'electronics': [
                    { name: 'iPhone 15 Pro', price: 4299, sku: 'IPH15P-001' },
                    { name: 'Samsung Galaxy S24', price: 3299, sku: 'SGS24-001' },
                    { name: 'MacBook Air M2', price: 5299, sku: 'MBA-M2-001' },
                    { name: 'iPad Pro', price: 2899, sku: 'IPAD-PRO-001' }
                ],
                'clothing': [
                    { name: 'Nike Air Max', price: 299, sku: 'NIKE-AM-001' },
                    { name: 'Adidas T-Shirt', price: 89, sku: 'ADIDAS-TS-001' },
                    { name: 'Levi\'s Jeans', price: 199, sku: 'LEVIS-J-001' }
                ],
                'food': [
                    { name: 'Coca Cola', price: 3.50, sku: 'COKE-001' },
                    { name: 'McDonald\'s Burger', price: 12.90, sku: 'MCD-B-001' },
                    { name: 'Starbucks Coffee', price: 15.90, sku: 'SBUX-C-001' }
                ]
            };

            const categoryProducts = products[category];
            const randomProduct = categoryProducts[Math.floor(Math.random() * categoryProducts.length)];
            
            // Check if product already exists in cart
            const existingItem = cart.find(item => item.sku === randomProduct.sku);
            
            if (existingItem) {
                existingItem.quantity += 1;
            } else {
                cart.push({
                    ...randomProduct,
                    quantity: 1
                });
            }
            
            updateCartDisplay();
            showNotification(`Added ${randomProduct.name} to cart!`);
        }

        // Update cart display
        function updateCartDisplay() {
            const cartItems = document.getElementById('cart-items');
            const subtotal = document.getElementById('subtotal');
            const tax = document.getElementById('tax');
            const total = document.getElementById('total');
            
            if (cart.length === 0) {
                cartItems.innerHTML = `
                    <div class="text-center text-gray-500 py-8">
                        <i class="fas fa-shopping-cart text-4xl mb-2"></i>
                        <p>Cart is Empty</p>
                        <p class="text-sm">Start adding products to begin a sale</p>
                    </div>
                `;
                subtotal.textContent = 'RM 0.00';
                tax.textContent = 'RM 0.00';
                total.textContent = 'RM 0.00';
                return;
            }
            
            let cartHTML = '';
            let subtotalAmount = 0;
            
            cart.forEach((item, index) => {
                const itemTotal = item.price * item.quantity;
                subtotalAmount += itemTotal;
                
                cartHTML += `
                    <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                        <div>
                            <div class="font-semibold">${item.name}</div>
                            <div class="text-sm text-gray-500">SKU: ${item.sku}</div>
                        </div>
                        <div class="flex items-center gap-2">
                            <button onclick="updateQuantity(${index}, -1)" class="w-8 h-8 bg-red-500 text-white rounded-full flex items-center justify-center text-sm">-</button>
                            <span class="w-8 text-center">${item.quantity}</span>
                            <button onclick="updateQuantity(${index}, 1)" class="w-8 h-8 bg-green-500 text-white rounded-full flex items-center justify-center text-sm">+</button>
                            <span class="font-semibold ml-4">RM ${itemTotal.toFixed(2)}</span>
                            <button onclick="removeFromCart(${index})" class="text-red-600 hover:text-red-800 ml-2">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                `;
            });
            
            cartItems.innerHTML = cartHTML;
            
            const taxAmount = subtotalAmount * 0.06;
            const totalAmount = subtotalAmount + taxAmount;
            
            subtotal.textContent = `RM ${subtotalAmount.toFixed(2)}`;
            tax.textContent = `RM ${taxAmount.toFixed(2)}`;
            total.textContent = `RM ${totalAmount.toFixed(2)}`;
            
            cartTotal = totalAmount;
        }

        // Update item quantity
        function updateQuantity(index, change) {
            cart[index].quantity += change;
            if (cart[index].quantity <= 0) {
                cart.splice(index, 1);
            }
            updateCartDisplay();
        }

        // Remove item from cart
        function removeFromCart(index) {
            cart.splice(index, 1);
            updateCartDisplay();
        }

        // Clear cart
        function clearCart() {
            cart = [];
            updateCartDisplay();
            showNotification('Cart cleared!');
        }

        // Scan barcode
        function scanBarcode() {
            const barcodeInput = document.getElementById('barcode-input');
            const barcode = barcodeInput.value.trim();
            
            if (barcode) {
                // Simulate barcode scanning
                const products = [
                    { name: 'iPhone 15 Pro', price: 4299, sku: 'IPH15P-001' },
                    { name: 'Samsung Galaxy S24', price: 3299, sku: 'SGS24-001' },
                    { name: 'MacBook Air M2', price: 5299, sku: 'MBA-M2-001' }
                ];
                
                const foundProduct = products.find(p => p.sku === barcode);
                if (foundProduct) {
                    const existingItem = cart.find(item => item.sku === foundProduct.sku);
                    if (existingItem) {
                        existingItem.quantity += 1;
                    } else {
                        cart.push({ ...foundProduct, quantity: 1 });
                    }
                    updateCartDisplay();
                    showNotification(`Scanned: ${foundProduct.name}`);
                } else {
                    showNotification('Product not found!', 'error');
                }
                barcodeInput.value = '';
            } else {
                showNotification('Please enter a barcode/SKU', 'error');
            }
        }

        // Process payment
        function processPayment(method) {
            if (cart.length === 0) {
                showNotification('Cart is empty!', 'error');
                return;
            }
            
            const methodText = method === 'cash' ? 'Cash Payment' : 'Online Payment';
            showNotification(`Processing ${methodText} for RM ${cartTotal.toFixed(2)}...`, 'success');
            
            // Simulate payment processing
            setTimeout(() => {
                showNotification(`Payment successful! Receipt printed.`, 'success');
                cart = [];
                updateCartDisplay();
            }, 2000);
        }

        // Show notification
        function showNotification(message, type = 'info') {
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50 ${
                type === 'success' ? 'bg-green-500' : 
                type === 'error' ? 'bg-red-500' : 'bg-blue-500'
            } text-white`;
            notification.textContent = message;
            
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.remove();
            }, 3000);
        }

        // Sandbox action functions
        function addProduct() {
            showNotification('Add Product dialog would open here', 'info');
        }

        function addMember() {
            showNotification('Add Member dialog would open here', 'info');
        }

        function exportReport() {
            showNotification('Exporting report...', 'success');
        }

        function printReport() {
            showNotification('Printing report...', 'success');
        }

        // Smooth scrolling for navigation links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Add scroll effect to navigation
        window.addEventListener('scroll', function() {
            const nav = document.querySelector('nav');
            if (window.scrollY > 100) {
                nav.classList.add('bg-white', 'shadow-lg');
            } else {
                nav.classList.remove('bg-white', 'shadow-lg');
            }
        });

        // Counter animation for stats
        function animateCounter(element, target, duration = 2000) {
            let start = 0;
            const increment = target / (duration / 16);
            const timer = setInterval(() => {
                start += increment;
                element.textContent = Math.floor(start);
                if (start >= target) {
                    element.textContent = target;
                    clearInterval(timer);
                }
            }, 16);
        }

        // Intersection Observer for animations
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animate-fade-in');
                }
            });
        }, observerOptions);

        // Observe all feature cards
        document.querySelectorAll('.feature-card').forEach(card => {
            observer.observe(card);
        });

        // Add CSS for fade-in animation
        const style = document.createElement('style');
        style.textContent = `
            .animate-fade-in {
                animation: fadeIn 0.6s ease-in-out;
            }
            @keyframes fadeIn {
                from { opacity: 0; transform: translateY(20px); }
                to { opacity: 1; transform: translateY(0); }
            }
        `;
        document.head.appendChild(style);
    </script>
</body>
</html>
