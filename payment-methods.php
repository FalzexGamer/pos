<?php
include 'include/conn.php';
include 'include/session.php';
include 'include/head.php';
include 'include/header.php';
include 'include/sidebar.php';
?>

<!-- Main Content -->
<div class="main-content ml-0 lg:ml-64 pt-16">
    <!-- Background Gradient -->
    <div class="absolute inset-0 bg-gradient-to-br from-blue-50 via-white to-indigo-50 -z-10"></div>
    
    <div class="p-4 sm:p-6 lg:p-8">
        <!-- Page Header with Glassmorphism -->
        <div class="backdrop-blur-sm bg-white/70 rounded-2xl shadow-xl border border-white/20 p-6 mb-8">
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center space-y-4 sm:space-y-0">
                <div>
                    <div class="flex items-center space-x-3 mb-2">
                        <div class="p-2 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-xl">
                            <i class="fas fa-credit-card text-white text-xl"></i>
                        </div>
                        <h1 class="text-3xl sm:text-4xl font-bold bg-gradient-to-r from-gray-900 to-gray-700 bg-clip-text text-transparent">
                            Payment Methods
                        </h1>
                    </div>
                    <p class="text-gray-600 text-lg">Manage your payment methods and options</p>
                </div>
                <button onclick="openAddModal()" class="group relative inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl hover:from-blue-700 hover:to-indigo-700 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                    <div class="absolute inset-0 bg-white/20 rounded-xl opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    <i class="fas fa-plus mr-2 relative z-10"></i>
                    <span class="relative z-10 font-medium">Add Payment Method</span>
                </button>
            </div>
        </div>





        <!-- Data Table with Glassmorphism -->
        <div class="backdrop-blur-sm bg-white/70 rounded-xl lg:rounded-2xl shadow-xl border border-white/20 overflow-hidden">
            <div class="p-4 lg:p-6">
                <div class="flex items-center justify-between mb-4 lg:mb-6">
                    <h3 class="text-lg lg:text-xl font-semibold text-gray-900">Payment Methods List</h3>
                    <div class="text-sm text-gray-500">
                        <span id="showing-info">Showing 0 of 0 entries</span>
                    </div>
                </div>
                
                <div class="overflow-x-auto">
                    <table id="payment-methods-table" class="w-full text-sm text-left text-gray-500">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                            <tr>
                                <th scope="col" class="px-4 py-3">ID</th>
                                <th scope="col" class="px-4 py-3">Name</th>
                                <th scope="col" class="px-4 py-3">Description</th>
                                <th scope="col" class="px-4 py-3">Status</th>
                                <th scope="col" class="px-4 py-3">Created</th>
                                <th scope="col" class="px-4 py-3">Updated</th>
                                <th scope="col" class="px-4 py-3">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="payment-methods-tbody">
                            <!-- Data will be loaded here -->
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <div class="flex items-center justify-between mt-4 lg:mt-6">
                    <div class="text-sm text-gray-700">
                        <span id="pagination-info">Page 1 of 1</span>
                    </div>
                    <div class="flex space-x-2" id="pagination-controls">
                        <!-- Pagination controls will be generated here -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add/Edit Modal -->
<div id="payment-method-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-auto">
            <div class="p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-gray-900" id="modal-title">Add Payment Method</h3>
                    <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                
                <form id="payment-method-form" class="space-y-4">
                    <input type="hidden" id="method-id" name="id">
                    
                    <div class="space-y-2">
                        <label for="method-name" class="block text-sm font-medium text-gray-700">Name *</label>
                        <input type="text" id="method-name" name="name" required maxlength="100"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                               placeholder="Enter payment method name">
                    </div>
                    
                    <div class="space-y-2">
                        <label for="method-description" class="block text-sm font-medium text-gray-700">Description</label>
                        <textarea id="method-description" name="description" rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                                  placeholder="Enter description (optional)"></textarea>
                    </div>
                    
                    <div class="space-y-2">
                        <label for="method-status" class="block text-sm font-medium text-gray-700">Status</label>
                        <select id="method-status" name="is_active" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>
                    
                    <div class="flex space-x-3 pt-4">
                        <button type="submit" class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200">
                            <i class="fas fa-save mr-2"></i>Save
                        </button>
                        <button type="button" onclick="closeModal()" class="flex-1 px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors duration-200">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="delete-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-auto">
            <div class="p-6 text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                    <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Delete Payment Method</h3>
                <p class="text-sm text-gray-500 mb-6">Are you sure you want to delete this payment method? This action cannot be undone.</p>
                
                <div class="flex space-x-3">
                    <button onclick="confirmDelete()" class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors duration-200">
                        <i class="fas fa-trash mr-2"></i>Delete
                    </button>
                    <button onclick="closeDeleteModal()" class="flex-1 px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors duration-200">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'include/footer.php'; ?>

<script>
let currentPage = 1;
let totalPages = 1;
let itemsPerPage = 10;
let deleteId = null;

// Load payment methods on page load
document.addEventListener('DOMContentLoaded', function() {
    loadPaymentMethods();
});

// Load payment methods
function loadPaymentMethods() {
    fetch(`ajax/get-payment-methods-list.php?page=${currentPage}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayPaymentMethods(data.payment_methods);
                updatePagination(data.total_pages, data.current_page, data.total_records);
            } else {
                showAlert('Error', data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('Error', 'Failed to load payment methods', 'error');
        });
}

// Display payment methods in table
function displayPaymentMethods(methods) {
    const tbody = document.getElementById('payment-methods-tbody');
    tbody.innerHTML = '';
    
    if (methods.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                    <i class="fas fa-inbox text-4xl mb-4 block"></i>
                    <p class="text-lg">No payment methods found</p>
                    <p class="text-sm">Try adjusting your search or filters</p>
                </td>
            </tr>
        `;
        return;
    }
    
    methods.forEach(method => {
        const row = document.createElement('tr');
        row.className = 'bg-white border-b hover:bg-gray-50 transition-colors duration-200';
        
        row.innerHTML = `
            <td class="px-4 py-3 font-medium text-gray-900">${method.id}</td>
            <td class="px-4 py-3">
                <div class="flex items-center space-x-2">
                    <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-credit-card text-blue-600 text-sm"></i>
                    </div>
                    <span class="font-medium text-gray-900">${method.name}</span>
                </div>
            </td>
            <td class="px-4 py-3 text-gray-600">
                ${method.description || '<span class="text-gray-400 italic">No description</span>'}
            </td>
            <td class="px-4 py-3">
                <span class="px-2 py-1 text-xs font-medium rounded-full ${method.is_active == 1 ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'}">
                    ${method.is_active == 1 ? 'Active' : 'Inactive'}
                </span>
            </td>
            <td class="px-4 py-3 text-gray-600">
                ${formatDate(method.created_at)}
            </td>
            <td class="px-4 py-3 text-gray-600">
                ${formatDate(method.updated_at)}
            </td>
            <td class="px-4 py-3">
                <div class="flex items-center space-x-2">
                    <button onclick="editPaymentMethod(${method.id})" class="text-blue-600 hover:text-blue-800 transition-colors" title="Edit">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button onclick="deletePaymentMethod(${method.id})" class="text-red-600 hover:text-red-800 transition-colors" title="Delete">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </td>
        `;
        
        tbody.appendChild(row);
    });
}

// Update pagination
function updatePagination(totalPages, currentPage, totalRecords) {
    this.totalPages = totalPages;
    this.currentPage = currentPage;
    
    document.getElementById('pagination-info').textContent = `Page ${currentPage} of ${totalPages}`;
    document.getElementById('showing-info').textContent = `Showing ${((currentPage - 1) * itemsPerPage) + 1} to ${Math.min(currentPage * itemsPerPage, totalRecords)} of ${totalRecords} entries`;
    
    const controls = document.getElementById('pagination-controls');
    controls.innerHTML = '';
    
    // Previous button
    const prevBtn = document.createElement('button');
    prevBtn.className = `px-3 py-2 text-sm font-medium rounded-lg ${currentPage > 1 ? 'bg-blue-600 text-white hover:bg-blue-700' : 'bg-gray-200 text-gray-400 cursor-not-allowed'}`;
    prevBtn.textContent = 'Previous';
    prevBtn.disabled = currentPage <= 1;
    prevBtn.onclick = () => {
        if (currentPage > 1) {
            this.currentPage = currentPage - 1;
            loadPaymentMethods();
        }
    };
    controls.appendChild(prevBtn);
    
    // Page numbers
    for (let i = 1; i <= totalPages; i++) {
        if (i === 1 || i === totalPages || (i >= currentPage - 2 && i <= currentPage + 2)) {
            const pageBtn = document.createElement('button');
            pageBtn.className = `px-3 py-2 text-sm font-medium rounded-lg mx-1 ${i === currentPage ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300'}`;
            pageBtn.textContent = i;
            pageBtn.onclick = () => {
                this.currentPage = i;
                loadPaymentMethods();
            };
            controls.appendChild(pageBtn);
        } else if (i === currentPage - 3 || i === currentPage + 3) {
            const ellipsis = document.createElement('span');
            ellipsis.className = 'px-3 py-2 text-gray-500';
            ellipsis.textContent = '...';
            controls.appendChild(ellipsis);
        }
    }
    
    // Next button
    const nextBtn = document.createElement('button');
    nextBtn.className = `px-3 py-2 text-sm font-medium rounded-lg ${currentPage < totalPages ? 'bg-blue-600 text-white hover:bg-blue-700' : 'bg-gray-200 text-gray-400 cursor-not-allowed'}`;
    nextBtn.textContent = 'Next';
    nextBtn.disabled = currentPage >= totalPages;
    nextBtn.onclick = () => {
        if (currentPage < totalPages) {
            this.currentPage = currentPage + 1;
            loadPaymentMethods();
        }
    };
    controls.appendChild(nextBtn);
}



// Open add modal
function openAddModal() {
    document.getElementById('modal-title').textContent = 'Add Payment Method';
    document.getElementById('payment-method-form').reset();
    document.getElementById('method-id').value = '';
    document.getElementById('payment-method-modal').classList.remove('hidden');
}

// Open edit modal
function editPaymentMethod(id) {
    fetch(`ajax/get-payment-method.php?id=${id}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const method = data.payment_method;
                document.getElementById('modal-title').textContent = 'Edit Payment Method';
                document.getElementById('method-id').value = method.id;
                document.getElementById('method-name').value = method.name;
                document.getElementById('method-description').value = method.description || '';
                document.getElementById('method-status').value = method.is_active;
                document.getElementById('payment-method-modal').classList.remove('hidden');
            } else {
                showAlert('Error', data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('Error', 'Failed to load payment method details', 'error');
        });
}

// Close modal
function closeModal() {
    document.getElementById('payment-method-modal').classList.add('hidden');
}

// Handle form submission
document.getElementById('payment-method-form').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const id = formData.get('id');
    const url = id ? 'ajax/edit-payment-method.php' : 'ajax/save-payment-method.php';
    
    fetch(url, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('Success', data.message, 'success');
            closeModal();
            loadPaymentMethods();
        } else {
            showAlert('Error', data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('Error', 'Failed to save payment method', 'error');
    });
});

// Delete payment method
function deletePaymentMethod(id) {
    deleteId = id;
    document.getElementById('delete-modal').classList.remove('hidden');
}

// Close delete modal
function closeDeleteModal() {
    document.getElementById('delete-modal').classList.add('hidden');
    deleteId = null;
}

// Confirm delete
function confirmDelete() {
    if (!deleteId) return;
    
    fetch('ajax/delete-payment-method.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `id=${deleteId}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('Success', data.message, 'success');
            closeDeleteModal();
            loadPaymentMethods();
        } else {
            showAlert('Error', data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('Error', 'Failed to delete payment method', 'error');
    });
}



// Utility functions
function formatDate(dateString) {
    if (!dateString) return '-';
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
}



function showAlert(title, message, type) {
    Swal.fire({
        title: title,
        text: message,
        icon: type,
        confirmButtonColor: '#3B82F6',
        confirmButtonText: 'OK'
    });
}
</script>
