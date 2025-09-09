<?php
include 'include/conn.php';
include 'include/session.php';
include 'include/head.php';
include 'include/header.php';
include 'include/sidebar.php';

// Check if user is admin
if ($_SESSION['role'] != 'admin') {
    header('Location: dashboard.php');
    exit();
}

// Get current company settings
$query_settings = mysqli_query($conn, "SELECT * FROM company_settings WHERE id = 1");
$settings = mysqli_fetch_array($query_settings);

// If no settings exist, create default
if (!$settings) {
    mysqli_query($conn, "INSERT INTO company_settings (company_name, address, phone, email, website, tax_number, currency, logo) VALUES ('POS System', '', '', '', '', '', 'MYR', '')");
    $query_settings = mysqli_query($conn, "SELECT * FROM company_settings WHERE id = 1");
    $settings = mysqli_fetch_array($query_settings);
}
?>

<!-- Main Content -->
<div class="main-content ml-0 lg:ml-64 pt-16">
    <div class="p-4 md:p-6">
        <!-- Page Header -->
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Company Settings</h1>
            <p class="text-gray-600">Manage your company information and preferences</p>
        </div>

        <!-- Settings Form -->
        <div class="bg-white rounded-lg shadow">
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-900">Company Information</h2>
                <p class="text-sm text-gray-500">Update your company details and branding</p>
            </div>
            
            <form id="company-settings-form" class="p-6 space-y-6">
                <!-- Company Name -->
                <div>
                    <label for="company_name" class="block text-sm font-medium text-gray-700 mb-2">
                        Company Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           id="company_name" 
                           name="company_name" 
                           value="<?= htmlspecialchars($settings['company_name'] ?? '') ?>"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           required>
                </div>

                <!-- Address -->
                <div>
                    <label for="address" class="block text-sm font-medium text-gray-700 mb-2">
                        Address
                    </label>
                    <textarea id="address" 
                              name="address" 
                              rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                              placeholder="Enter company address"><?= htmlspecialchars($settings['address'] ?? '') ?></textarea>
                </div>

                <!-- Contact Information Row -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Phone -->
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                            Phone Number
                        </label>
                        <input type="tel" 
                               id="phone" 
                               name="phone" 
                               value="<?= htmlspecialchars($settings['phone'] ?? '') ?>"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="+60 12-345 6789">
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                            Email Address
                        </label>
                        <input type="email" 
                               id="email" 
                               name="email" 
                               value="<?= htmlspecialchars($settings['email'] ?? '') ?>"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="info@company.com">
                    </div>
                </div>

                <!-- Website and Tax Number Row -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Website -->
                    <div>
                        <label for="website" class="block text-sm font-medium text-gray-700 mb-2">
                            Website
                        </label>
                        <input type="url" 
                               id="website" 
                               name="website" 
                               value="<?= htmlspecialchars($settings['website'] ?? '') ?>"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="https://www.company.com">
                    </div>

                    <!-- Tax Number -->
                    <div>
                        <label for="tax_number" class="block text-sm font-medium text-gray-700 mb-2">
                            Tax Number
                        </label>
                        <input type="text" 
                               id="tax_number" 
                               name="tax_number" 
                               value="<?= htmlspecialchars($settings['tax_number'] ?? '') ?>"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="Tax registration number">
                    </div>
                </div>

                <!-- Currency -->
                <div>
                    <label for="currency" class="block text-sm font-medium text-gray-700 mb-2">
                        Currency
                    </label>
                    <select id="currency" 
                            name="currency" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="MYR" <?= ($settings['currency'] ?? 'MYR') == 'MYR' ? 'selected' : '' ?>>Malaysian Ringgit (MYR)</option>
                        <option value="USD" <?= ($settings['currency'] ?? '') == 'USD' ? 'selected' : '' ?>>US Dollar (USD)</option>
                        <option value="EUR" <?= ($settings['currency'] ?? '') == 'EUR' ? 'selected' : '' ?>>Euro (EUR)</option>
                        <option value="SGD" <?= ($settings['currency'] ?? '') == 'SGD' ? 'selected' : '' ?>>Singapore Dollar (SGD)</option>
                        <option value="THB" <?= ($settings['currency'] ?? '') == 'THB' ? 'selected' : '' ?>>Thai Baht (THB)</option>
                        <option value="IDR" <?= ($settings['currency'] ?? '') == 'IDR' ? 'selected' : '' ?>>Indonesian Rupiah (IDR)</option>
                    </select>
                </div>

                <!-- Logo Upload -->
                <div>
                    <label for="logo" class="block text-sm font-medium text-gray-700 mb-2">
                        Company Logo
                    </label>
                    <div class="flex items-center space-x-4">
                        <div class="flex-shrink-0">
                            <?php if (!empty($settings['logo'])): ?>
                                <img id="logo-preview" 
                                     src="<?= htmlspecialchars($settings['logo']) ?>" 
                                     alt="Company Logo" 
                                     class="h-20 w-20 object-cover rounded-lg border border-gray-300">
                            <?php else: ?>
                                <div id="logo-preview" 
                                     class="h-20 w-20 bg-gray-100 rounded-lg border border-gray-300 flex items-center justify-center">
                                    <i class="fas fa-image text-gray-400 text-2xl"></i>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="flex-1">
                            <input type="file" 
                                   id="logo" 
                                   name="logo" 
                                   accept="image/*"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <p class="text-xs text-gray-500 mt-1">Recommended size: 200x200px. Max file size: 2MB</p>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row gap-4 pt-6 border-t border-gray-200">
                    <button type="submit" 
                            class="flex-1 sm:flex-none bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
                        <i class="fas fa-save mr-2"></i>Save Settings
                    </button>
                    <button type="button" 
                            id="reset-form"
                            class="flex-1 sm:flex-none bg-gray-600 text-white px-6 py-2 rounded-lg hover:bg-gray-700 focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-colors">
                        <i class="fas fa-undo mr-2"></i>Reset
                    </button>
                </div>
            </form>
        </div>

        <!-- System Information -->
        <div class="mt-8 bg-white rounded-lg shadow">
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-900">System Information</h2>
                <p class="text-sm text-gray-500">Current system status and version</p>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h3 class="text-sm font-medium text-gray-700">System Version</h3>
                        <p class="text-lg font-semibold text-gray-900">v1.0.0</p>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h3 class="text-sm font-medium text-gray-700">Database</h3>
                        <p class="text-lg font-semibold text-gray-900">MySQL 8.0</p>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h3 class="text-sm font-medium text-gray-700">Last Updated</h3>
                        <p class="text-lg font-semibold text-gray-900"><?= date('M d, Y', strtotime($settings['updated_at'] ?? 'now')) ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Logo preview functionality
    $('#logo').change(function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                $('#logo-preview').html('<img src="' + e.target.result + '" alt="Company Logo" class="h-20 w-20 object-cover rounded-lg border border-gray-300">');
            };
            reader.readAsDataURL(file);
        }
    });

    // Form submission
    $('#company-settings-form').submit(function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        // Show loading state
        const submitBtn = $(this).find('button[type="submit"]');
        const originalText = submitBtn.html();
        submitBtn.html('<i class="fas fa-spinner fa-spin mr-2"></i>Saving...').prop('disabled', true);
        
        $.ajax({
            url: 'ajax/save-company-settings.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: 'Company settings updated successfully.',
                        timer: 2000,
                        showConfirmButton: false
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: response.message || 'Failed to update company settings.',
                    });
                }
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'An error occurred while updating company settings.',
                });
            },
            complete: function() {
                submitBtn.html(originalText).prop('disabled', false);
            }
        });
    });

    // Reset form
    $('#reset-form').click(function() {
        Swal.fire({
            title: 'Reset Form?',
            text: 'This will reset all changes to the original values.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, reset it!'
        }).then((result) => {
            if (result.isConfirmed) {
                location.reload();
            }
        });
    });
});
</script>

<?php include 'include/footer.php'; ?>
