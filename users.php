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
                            <i class="fas fa-users text-white text-xl"></i>
                        </div>
                        <h1 class="text-3xl sm:text-4xl font-bold bg-gradient-to-r from-gray-900 to-gray-700 bg-clip-text text-transparent">
                            User Management
                        </h1>
                    </div>
                    <p class="text-gray-600 text-lg">Manage system users and their roles</p>
                </div>
                <button onclick="openAddModal()" class="group relative inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl hover:from-blue-700 hover:to-indigo-700 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                    <div class="absolute inset-0 bg-white/20 rounded-xl opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    <i class="fas fa-plus mr-2 relative z-10"></i>
                    <span class="relative z-10 font-medium">Add User</span>
                </button>
            </div>
        </div>

        <!-- Data Table with Glassmorphism -->
        <div class="backdrop-blur-sm bg-white/70 rounded-xl lg:rounded-2xl shadow-xl border border-white/20 overflow-hidden">
            <div class="p-4 lg:p-6">
                <div class="flex items-center justify-between mb-4 lg:mb-6">
                    <h3 class="text-lg lg:text-xl font-semibold text-gray-900">Users List</h3>
                    <div class="text-sm text-gray-500">
                        <span id="showing-info">Showing 0 of 0 entries</span>
                    </div>
                </div>
                
                <div class="overflow-x-auto">
                    <table id="users-table" class="w-full text-sm text-left text-gray-500">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                            <tr>
                                <th scope="col" class="px-4 py-3">ID</th>
                                <th scope="col" class="px-4 py-3">Username</th>
                                <th scope="col" class="px-4 py-3">Full Name</th>
                                <th scope="col" class="px-4 py-3">Email</th>
                                <th scope="col" class="px-4 py-3">Role</th>
                                <th scope="col" class="px-4 py-3">Status</th>
                                <th scope="col" class="px-4 py-3">Last Login</th>
                                <th scope="col" class="px-4 py-3">Created</th>
                                <th scope="col" class="px-4 py-3">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="users-tbody">
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
<div id="user-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-auto">
            <div class="p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-gray-900" id="modal-title">Add User</h3>
                    <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                
                <form id="user-form" class="space-y-4">
                    <input type="hidden" id="user-id" name="id">
                    
                    <div class="space-y-2">
                        <label for="username" class="block text-sm font-medium text-gray-700">Username *</label>
                        <input type="text" id="username" name="username" required maxlength="50"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                               placeholder="Enter username">
                    </div>
                    
                    <div class="space-y-2">
                        <label for="full-name" class="block text-sm font-medium text-gray-700">Full Name *</label>
                        <input type="text" id="full-name" name="full_name" required maxlength="100"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                               placeholder="Enter full name">
                    </div>
                    
                    <div class="space-y-2">
                        <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                        <input type="email" id="email" name="email" maxlength="255"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                               placeholder="Enter email address">
                    </div>
                    
                    <div class="space-y-2">
                        <label for="phone" class="block text-sm font-medium text-gray-700">Phone</label>
                        <input type="tel" id="phone" name="phone" maxlength="50"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                               placeholder="Enter phone number">
                    </div>
                    
                    <div class="space-y-2">
                        <label for="role" class="block text-sm font-medium text-gray-700">Role *</label>
                        <select id="role" name="role" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                            <option value="">Select Role</option>
                            <option value="admin">Administrator</option>
                            <option value="manager">Manager</option>
                            <option value="cashier">Cashier</option>
                        </select>
                    </div>
                    
                    <div class="space-y-2" id="password-section">
                        <label for="password" class="block text-sm font-medium text-gray-700">Password *</label>
                        <input type="password" id="password" name="password" maxlength="255"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                               placeholder="Enter password">
                    </div>
                    
                    <div class="space-y-2" id="confirm-password-section">
                        <label for="confirm-password" class="block text-sm font-medium text-gray-700">Confirm Password *</label>
                        <input type="password" id="confirm-password" name="confirm_password" maxlength="255"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                               placeholder="Confirm password">
                    </div>
                    
                    <div class="space-y-2">
                        <label for="user-status" class="block text-sm font-medium text-gray-700">Status</label>
                        <select id="user-status" name="is_active" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
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
                <h3 class="text-lg font-medium text-gray-900 mb-2">Delete User</h3>
                <p class="text-sm text-gray-500 mb-6">Are you sure you want to delete this user? This action cannot be undone.</p>
                
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

// Load users on page load
document.addEventListener('DOMContentLoaded', function() {
    loadUsers();
});

// Load users
function loadUsers() {
    fetch(`ajax/get-users-list.php?page=${currentPage}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayUsers(data.users);
                updatePagination(data.total_pages, data.current_page, data.total_records);
            } else {
                showAlert('Error', data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('Error', 'Failed to load users', 'error');
        });
}

// Display users in table
function displayUsers(users) {
    const tbody = document.getElementById('users-tbody');
    tbody.innerHTML = '';
    
    if (users.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="9" class="px-4 py-8 text-center text-gray-500">
                    <i class="fas fa-users text-4xl mb-4 block"></i>
                    <p class="text-lg">No users found</p>
                    <p class="text-sm">Try adjusting your search or filters</p>
                </td>
            </tr>
        `;
        return;
    }
    
    users.forEach(user => {
        const row = document.createElement('tr');
        row.className = 'bg-white border-b hover:bg-gray-50 transition-colors duration-200';
        
        row.innerHTML = `
            <td class="px-4 py-3 font-medium text-gray-900">${user.id}</td>
            <td class="px-4 py-3">
                <div class="flex items-center space-x-2">
                    <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-user text-blue-600 text-sm"></i>
                    </div>
                    <span class="font-medium text-gray-900">${user.username}</span>
                </div>
            </td>
            <td class="px-4 py-3 text-gray-900">${user.full_name}</td>
            <td class="px-4 py-3 text-gray-600">${user.email || '<span class="text-gray-400 italic">No email</span>'}</td>
            <td class="px-4 py-3">
                <span class="px-2 py-1 text-xs font-medium rounded-full ${getRoleColor(user.role)}">
                    ${getRoleDisplay(user.role)}
                </span>
            </td>
            <td class="px-4 py-3">
                <span class="px-2 py-1 text-xs font-medium rounded-full ${user.is_active == 1 ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'}">
                    ${user.is_active == 1 ? 'Active' : 'Inactive'}
                </span>
            </td>
            <td class="px-4 py-3 text-gray-600">
                ${user.last_login ? formatDate(user.last_login) : '<span class="text-gray-400 italic">Never</span>'}
            </td>
            <td class="px-4 py-3 text-gray-600">
                ${formatDate(user.created_at)}
            </td>
            <td class="px-4 py-3">
                <div class="flex items-center space-x-2">
                    <button onclick="editUser(${user.id})" class="text-blue-600 hover:text-blue-800 transition-colors" title="Edit">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button onclick="deleteUser(${user.id})" class="text-red-600 hover:text-red-800 transition-colors" title="Delete">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </td>
        `;
        
        tbody.appendChild(row);
    });
}

// Get role color
function getRoleColor(role) {
    switch(role) {
        case 'admin': return 'bg-red-100 text-red-800';
        case 'manager': return 'bg-purple-100 text-purple-800';
        case 'cashier': return 'bg-blue-100 text-blue-800';
        default: return 'bg-gray-100 text-gray-800';
    }
}

// Get role display name
function getRoleDisplay(role) {
    switch(role) {
        case 'admin': return 'Administrator';
        case 'manager': return 'Manager';
        case 'cashier': return 'Cashier';
        default: return role;
    }
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
            loadUsers();
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
                loadUsers();
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
            loadUsers();
        }
    };
    controls.appendChild(nextBtn);
}

// Open add modal
function openAddModal() {
    document.getElementById('modal-title').textContent = 'Add User';
    document.getElementById('user-form').reset();
    document.getElementById('user-id').value = '';
    document.getElementById('password-section').style.display = 'block';
    document.getElementById('confirm-password-section').style.display = 'block';
    document.getElementById('password').required = true;
    document.getElementById('confirm-password').required = true;
    document.getElementById('user-modal').classList.remove('hidden');
}

// Open edit modal
function editUser(id) {
    fetch(`ajax/get-user.php?id=${id}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const user = data.user;
                document.getElementById('modal-title').textContent = 'Edit User';
                document.getElementById('user-id').value = user.id;
                document.getElementById('username').value = user.username;
                document.getElementById('full-name').value = user.full_name;
                document.getElementById('email').value = user.email || '';
                document.getElementById('phone').value = user.phone || '';
                document.getElementById('role').value = user.role;
                document.getElementById('user-status').value = user.is_active;
                
                // Hide password fields for edit
                document.getElementById('password-section').style.display = 'none';
                document.getElementById('confirm-password-section').style.display = 'none';
                document.getElementById('password').required = false;
                document.getElementById('confirm-password').required = false;
                
                document.getElementById('user-modal').classList.remove('hidden');
            } else {
                showAlert('Error', data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('Error', 'Failed to load user details', 'error');
        });
}

// Close modal
function closeModal() {
    document.getElementById('user-modal').classList.add('hidden');
}

// Handle form submission
document.getElementById('user-form').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const id = formData.get('id');
    
    // Validate password confirmation for new users
    if (!id) {
        const password = formData.get('password');
        const confirmPassword = formData.get('confirm_password');
        
        if (password !== confirmPassword) {
            showAlert('Error', 'Passwords do not match', 'error');
            return;
        }
    }
    
    const url = id ? 'ajax/edit-user.php' : 'ajax/save-user.php';
    
    fetch(url, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('Success', data.message, 'success');
            closeModal();
            loadUsers();
        } else {
            showAlert('Error', data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('Error', 'Failed to save user', 'error');
    });
});

// Delete user
function deleteUser(id) {
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
    
    fetch('ajax/delete-user.php', {
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
            loadUsers();
        } else {
            showAlert('Error', data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('Error', 'Failed to delete user', 'error');
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
