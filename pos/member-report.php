<?php
include 'include/conn.php';
include 'include/session.php';
include 'include/head.php';
include 'include/header.php';
include 'include/sidebar.php';

// Get default date range (current month)
$today = date('Y-m-d');
$current_month = date('Y-m');
$month_start = $current_month . '-01';
$month_end = $today;

// Get member statistics
$query_total_members = mysqli_query($conn, "SELECT COUNT(*) as total FROM members");
$total_members = mysqli_fetch_array($query_total_members)['total'];

$query_active_members = mysqli_query($conn, "SELECT COUNT(*) as active FROM members WHERE is_active = 1");
$active_members = mysqli_fetch_array($query_active_members)['active'];

$query_inactive_members = mysqli_query($conn, "SELECT COUNT(*) as inactive FROM members WHERE is_active = 0");
$inactive_members = mysqli_fetch_array($query_inactive_members)['inactive'];

$query_new_members_this_month = mysqli_query($conn, "SELECT COUNT(*) as new_members FROM members WHERE DATE(created_at) BETWEEN '$month_start' AND '$month_end'");
$new_members_this_month = mysqli_fetch_array($query_new_members_this_month)['new_members'];

// Get membership tier statistics
$query_tier_stats = mysqli_query($conn, "SELECT 
    mt.name as tier_name,
    COUNT(m.id) as member_count,
    AVG(m.total_spent) as avg_spent,
    SUM(m.total_spent) as total_spent,
    AVG(m.total_points) as avg_points
FROM membership_tiers mt
LEFT JOIN members m ON mt.id = m.membership_tier_id
GROUP BY mt.id, mt.name
ORDER BY mt.id");
$tier_stats = [];
while ($row = mysqli_fetch_array($query_tier_stats)) {
    $tier_stats[] = $row;
}

// Get top spending members
$query_top_spenders = mysqli_query($conn, "SELECT 
    m.member_code,
    m.name,
    m.total_spent,
    m.total_points,
    mt.name as tier_name
FROM members m
LEFT JOIN membership_tiers mt ON m.membership_tier_id = mt.id
WHERE m.is_active = 1
ORDER BY m.total_spent DESC
LIMIT 10");
$top_spenders = [];
while ($row = mysqli_fetch_array($query_top_spenders)) {
    $top_spenders[] = $row;
}

// Get recent member registrations
$query_recent_members = mysqli_query($conn, "SELECT 
    m.member_code,
    m.name,
    m.phone,
    m.email,
    m.created_at,
    mt.name as tier_name
FROM members m
LEFT JOIN membership_tiers mt ON m.membership_tier_id = mt.id
ORDER BY m.created_at DESC
LIMIT 10");
$recent_members = [];
while ($row = mysqli_fetch_array($query_recent_members)) {
    $recent_members[] = $row;
}

// Calculate average statistics
$query_avg_stats = mysqli_query($conn, "SELECT 
    AVG(total_spent) as avg_total_spent,
    AVG(total_points) as avg_total_points,
    MAX(total_spent) as max_spent,
    MIN(total_spent) as min_spent
FROM members WHERE is_active = 1");
$avg_stats = mysqli_fetch_array($query_avg_stats);
?>

<!-- Main Content -->
<div class="main-content ml-0 lg:ml-64 pt-16">
    <!-- Background Gradient -->
    <div class="absolute inset-0 bg-gradient-to-br from-purple-50 via-white to-pink-50 -z-10"></div>
    
    <div class="p-4 sm:p-6 lg:p-8">
        <!-- Page Header with Glassmorphism -->
        <div class="backdrop-blur-sm bg-white/70 rounded-2xl shadow-xl border border-white/20 p-6 mb-8">
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center space-y-4 sm:space-y-0">
                <div>
                    <div class="flex items-center space-x-3 mb-2">
                        <div class="p-2 bg-gradient-to-r from-purple-500 to-pink-600 rounded-xl">
                            <i class="fas fa-users-cog text-white text-xl"></i>
                        </div>
                        <h1 class="text-3xl sm:text-4xl font-bold bg-gradient-to-r from-gray-900 to-gray-700 bg-clip-text text-transparent">
                            Member Report
                        </h1>
                    </div>
                    <p class="text-gray-600 text-lg">Comprehensive member analytics and insights</p>
                </div>
                <div class="flex items-center space-x-3">
                    <button onclick="exportMemberReport()" class="group relative inline-flex items-center px-4 py-2 bg-gradient-to-r from-purple-600 to-pink-600 text-white rounded-xl hover:from-purple-700 hover:to-pink-700 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                        <i class="fas fa-download mr-2"></i>
                        <span class="font-medium">Export Report</span>
                    </button>
                    <button onclick="printMemberReport()" class="group relative inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl hover:from-blue-700 hover:to-indigo-700 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                        <i class="fas fa-print mr-2"></i>
                        <span class="font-medium">Print Report</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Report Filters -->
        <div class="backdrop-blur-sm bg-white/70 rounded-xl lg:rounded-2xl shadow-xl border border-white/20 p-4 lg:p-6 mb-6 lg:mb-8">
            <div class="flex items-center space-x-3 mb-4 lg:mb-6">
                <div class="p-2 bg-gradient-to-r from-blue-500 to-indigo-500 rounded-lg">
                    <i class="fas fa-filter text-white"></i>
                </div>
                <h3 class="text-base lg:text-lg font-semibold text-gray-900">Report Parameters</h3>
            </div>
            
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-4 lg:gap-6">
                <div class="space-y-2">
                    <label class="block text-xs lg:text-sm font-semibold text-gray-700">Start Date</label>
                    <input type="date" id="start-date" value="<?php echo $month_start; ?>"
                           class="w-full px-3 lg:px-4 py-2.5 lg:py-3 bg-white/80 backdrop-blur-sm border border-gray-200 rounded-lg lg:rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200 text-sm lg:text-base">
                </div>
                
                <div class="space-y-2">
                    <label class="block text-xs lg:text-sm font-semibold text-gray-700">End Date</label>
                    <input type="date" id="end-date" value="<?php echo $month_end; ?>"
                           class="w-full px-3 lg:px-4 py-2.5 lg:py-3 bg-white/80 backdrop-blur-sm border border-gray-200 rounded-lg lg:rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200 text-sm lg:text-base">
                </div>
                
                <div class="space-y-2">
                    <label class="block text-xs lg:text-sm font-semibold text-gray-700">Membership Tier</label>
                    <select id="tier-filter" class="w-full px-3 lg:px-4 py-2.5 lg:py-3 bg-white/80 backdrop-blur-sm border border-gray-200 rounded-lg lg:rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200 text-sm lg:text-base">
                        <option value="">All Tiers</option>
                        <?php foreach ($tier_stats as $tier): ?>
                            <option value="<?php echo htmlspecialchars($tier['tier_name']); ?>"><?php echo htmlspecialchars($tier['tier_name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="space-y-2">
                    <label class="block text-xs lg:text-sm font-semibold text-gray-700">Status</label>
                    <select id="status-filter" class="w-full px-3 lg:px-4 py-2.5 lg:py-3 bg-white/80 backdrop-blur-sm border border-gray-200 rounded-lg lg:rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200 text-sm lg:text-base">
                        <option value="">All Status</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
            </div>
            
            <div class="flex items-center justify-end space-x-3 mt-4 lg:mt-6">
                <button onclick="generateMemberReport()" class="group relative inline-flex items-center px-6 py-3 bg-gradient-to-r from-purple-600 to-pink-600 text-white rounded-xl hover:from-purple-700 hover:to-pink-700 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                    <i class="fas fa-chart-bar mr-2"></i>
                    <span class="font-medium">Generate Report</span>
                </button>
            </div>
        </div>

        <!-- Member Statistics Cards -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-6 mb-6 lg:mb-8">
            <div class="stats-card backdrop-blur-sm bg-white/70 rounded-xl lg:rounded-2xl shadow-lg border border-white/20 p-4 lg:p-6 hover:shadow-xl transition-all duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs lg:text-sm font-medium text-gray-600">Total Members</p>
                        <p class="text-lg lg:text-2xl font-bold text-gray-900"><?php echo number_format($total_members); ?></p>
                    </div>
                    <div class="p-2 lg:p-3 bg-blue-100 rounded-lg lg:rounded-xl">
                        <i class="fas fa-users text-blue-600 text-sm lg:text-xl"></i>
                    </div>
                </div>
            </div>
            
            <div class="stats-card backdrop-blur-sm bg-white/70 rounded-xl lg:rounded-2xl shadow-lg border border-white/20 p-4 lg:p-6 hover:shadow-xl transition-all duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs lg:text-sm font-medium text-gray-600">Active Members</p>
                        <p class="text-lg lg:text-2xl font-bold text-green-600"><?php echo number_format($active_members); ?></p>
                        <p class="text-xs text-gray-500"><?php echo $total_members > 0 ? round(($active_members / $total_members) * 100, 1) : 0; ?>% of total</p>
                    </div>
                    <div class="p-2 lg:p-3 bg-green-100 rounded-lg lg:rounded-xl">
                        <i class="fas fa-check-circle text-green-600 text-sm lg:text-xl"></i>
                    </div>
                </div>
            </div>
            
            <div class="stats-card backdrop-blur-sm bg-white/70 rounded-xl lg:rounded-2xl shadow-lg border border-white/20 p-4 lg:p-6 hover:shadow-xl transition-all duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs lg:text-sm font-medium text-gray-600">New This Month</p>
                        <p class="text-lg lg:text-2xl font-bold text-purple-600"><?php echo number_format($new_members_this_month); ?></p>
                        <p class="text-xs text-gray-500">New registrations</p>
                    </div>
                    <div class="p-2 lg:p-3 bg-purple-100 rounded-lg lg:rounded-xl">
                        <i class="fas fa-user-plus text-purple-600 text-sm lg:text-xl"></i>
                    </div>
                </div>
            </div>
            
            <div class="stats-card backdrop-blur-sm bg-white/70 rounded-xl lg:rounded-2xl shadow-lg border border-white/20 p-4 lg:p-6 hover:shadow-xl transition-all duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs lg:text-sm font-medium text-gray-600">Avg. Spent</p>
                        <p class="text-lg lg:text-2xl font-bold text-orange-600">RM <?php echo number_format($avg_stats['avg_total_spent'], 2); ?></p>
                        <p class="text-xs text-gray-500">Per active member</p>
                    </div>
                    <div class="p-2 lg:p-3 bg-orange-100 rounded-lg lg:rounded-xl">
                        <i class="fas fa-money-bill-wave text-orange-600 text-sm lg:text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Membership Tier Analysis -->
        <div class="backdrop-blur-sm bg-white/70 rounded-xl lg:rounded-2xl shadow-xl border border-white/20 p-4 lg:p-6 mb-6 lg:mb-8">
            <div class="flex items-center space-x-3 mb-4 lg:mb-6">
                <div class="p-2 bg-gradient-to-r from-yellow-500 to-orange-500 rounded-lg">
                    <i class="fas fa-crown text-white"></i>
                </div>
                <h3 class="text-base lg:text-lg font-semibold text-gray-900">Membership Tier Analysis</h3>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-sm lg:text-base">
                    <thead>
                        <tr class="border-b border-gray-200">
                            <th class="text-left py-3 px-4 font-semibold text-gray-700">Tier</th>
                            <th class="text-center py-3 px-4 font-semibold text-gray-700">Members</th>
                            <th class="text-center py-3 px-4 font-semibold text-gray-700">Percentage</th>
                            <th class="text-center py-3 px-4 font-semibold text-gray-700">Avg. Spent</th>
                            <th class="text-center py-3 px-4 font-semibold text-gray-700">Total Spent</th>
                            <th class="text-center py-3 px-4 font-semibold text-gray-700">Avg. Points</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($tier_stats as $tier): ?>
                            <tr class="border-b border-gray-100 hover:bg-gray-50/50 transition-colors duration-200">
                                <td class="py-3 px-4">
                                    <div class="flex items-center space-x-2">
                                        <span class="w-3 h-3 rounded-full 
                                            <?php 
                                            switch($tier['tier_name']) {
                                                case 'Bronze': echo 'bg-yellow-400'; break;
                                                case 'Silver': echo 'bg-gray-400'; break;
                                                case 'Gold': echo 'bg-yellow-500'; break;
                                                case 'Platinum': echo 'bg-purple-500'; break;
                                                default: echo 'bg-gray-300';
                                            }
                                            ?>"></span>
                                        <span class="font-medium text-gray-900"><?php echo htmlspecialchars($tier['tier_name']); ?></span>
                                    </div>
                                </td>
                                <td class="py-3 px-4 text-center font-medium text-gray-900"><?php echo number_format($tier['member_count']); ?></td>
                                <td class="py-3 px-4 text-center text-gray-600"><?php echo $total_members > 0 ? round(($tier['member_count'] / $total_members) * 100, 1) : 0; ?>%</td>
                                <td class="py-3 px-4 text-center font-medium text-green-600">RM <?php echo number_format($tier['avg_spent'], 2); ?></td>
                                <td class="py-3 px-4 text-center font-medium text-blue-600">RM <?php echo number_format($tier['total_spent'], 2); ?></td>
                                <td class="py-3 px-4 text-center text-gray-600"><?php echo number_format($tier['avg_points']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Top Spenders and Recent Members -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 lg:gap-8 mb-6 lg:mb-8">
            <!-- Top Spenders -->
            <div class="backdrop-blur-sm bg-white/70 rounded-xl lg:rounded-2xl shadow-xl border border-white/20 p-4 lg:p-6">
                <div class="flex items-center space-x-3 mb-4 lg:mb-6">
                    <div class="p-2 bg-gradient-to-r from-green-500 to-emerald-500 rounded-lg">
                        <i class="fas fa-trophy text-white"></i>
                    </div>
                    <h3 class="text-base lg:text-lg font-semibold text-gray-900">Top Spenders</h3>
                </div>
                
                <div class="space-y-3">
                    <?php foreach ($top_spenders as $index => $member): ?>
                        <div class="flex items-center justify-between p-3 bg-gray-50/50 rounded-lg hover:bg-gray-100/50 transition-colors duration-200">
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 bg-gradient-to-r from-green-500 to-emerald-600 rounded-full flex items-center justify-center text-white text-sm font-bold">
                                    <?php echo $index + 1; ?>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900"><?php echo htmlspecialchars($member['name']); ?></p>
                                    <p class="text-xs text-gray-500"><?php echo htmlspecialchars($member['member_code']); ?> • <?php echo htmlspecialchars($member['tier_name']); ?></p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="font-bold text-green-600">RM <?php echo number_format($member['total_spent'], 2); ?></p>
                                <p class="text-xs text-gray-500"><?php echo number_format($member['total_points']); ?> points</p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Recent Members -->
            <div class="backdrop-blur-sm bg-white/70 rounded-xl lg:rounded-2xl shadow-xl border border-white/20 p-4 lg:p-6">
                <div class="flex items-center space-x-3 mb-4 lg:mb-6">
                    <div class="p-2 bg-gradient-to-r from-blue-500 to-indigo-500 rounded-lg">
                        <i class="fas fa-user-plus text-white"></i>
                    </div>
                    <h3 class="text-base lg:text-lg font-semibold text-gray-900">Recent Members</h3>
                </div>
                
                <div class="space-y-3">
                    <?php foreach ($recent_members as $member): ?>
                        <div class="flex items-center justify-between p-3 bg-gray-50/50 rounded-lg hover:bg-gray-100/50 transition-colors duration-200">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-full flex items-center justify-center text-white text-sm font-bold">
                                    <?php echo strtoupper(substr($member['name'], 0, 1)); ?>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900"><?php echo htmlspecialchars($member['name']); ?></p>
                                    <p class="text-xs text-gray-500"><?php echo htmlspecialchars($member['member_code']); ?> • <?php echo htmlspecialchars($member['tier_name']); ?></p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-xs text-gray-500"><?php echo date('M j, Y', strtotime($member['created_at'])); ?></p>
                                <p class="text-xs text-gray-400"><?php echo date('g:i A', strtotime($member['created_at'])); ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Member Report Table -->
        <div class="backdrop-blur-sm bg-white/70 rounded-xl lg:rounded-2xl shadow-xl border border-white/20 p-4 lg:p-6">
            <div class="flex items-center space-x-3 mb-4 lg:mb-6">
                <div class="p-2 bg-gradient-to-r from-purple-500 to-pink-500 rounded-lg">
                    <i class="fas fa-table text-white"></i>
                </div>
                <h3 class="text-base lg:text-lg font-semibold text-gray-900">Member Details Report</h3>
            </div>
            
            <div class="overflow-x-auto">
                <table id="member-report-table" class="w-full text-sm lg:text-base">
                    <thead>
                        <tr class="border-b border-gray-200">
                            <th class="text-left py-3 px-4 font-semibold text-gray-700">Member Code</th>
                            <th class="text-left py-3 px-4 font-semibold text-gray-700">Name</th>
                            <th class="text-left py-3 px-4 font-semibold text-gray-700">Phone</th>
                            <th class="text-left py-3 px-4 font-semibold text-gray-700">Email</th>
                            <th class="text-center py-3 px-4 font-semibold text-gray-700">Tier</th>
                            <th class="text-center py-3 px-4 font-semibold text-gray-700">Status</th>
                            <th class="text-center py-3 px-4 font-semibold text-gray-700">Total Spent</th>
                            <th class="text-center py-3 px-4 font-semibold text-gray-700">Points</th>
                            <th class="text-center py-3 px-4 font-semibold text-gray-700">Joined</th>
                        </tr>
                    </thead>
                    <tbody id="member-report-tbody">
                        <!-- Data will be loaded via AJAX -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
// Initialize DataTable
$(document).ready(function() {
    loadMemberReport();
    
    // Initialize DataTable
    $('#member-report-table').DataTable({
        pageLength: 25,
        order: [[8, 'desc']], // Sort by joined date descending
        responsive: true,
        language: {
            search: "Search members:",
            lengthMenu: "Show _MENU_ members per page",
            info: "Showing _START_ to _END_ of _TOTAL_ members",
            paginate: {
                first: "First",
                last: "Last",
                next: "Next",
                previous: "Previous"
            }
        }
    });
});

function loadMemberReport() {
    const startDate = $('#start-date').val();
    const endDate = $('#end-date').val();
    const tierFilter = $('#tier-filter').val();
    const statusFilter = $('#status-filter').val();
    
    $.ajax({
        url: 'ajax/get-members-report.php',
        type: 'POST',
        data: {
            start_date: startDate,
            end_date: endDate,
            tier_filter: tierFilter,
            status_filter: statusFilter
        },
        success: function(response) {
            $('#member-report-tbody').html(response);
            $('#member-report-table').DataTable().destroy();
            $('#member-report-table').DataTable({
                pageLength: 25,
                order: [[8, 'desc']],
                responsive: true,
                language: {
                    search: "Search members:",
                    lengthMenu: "Show _MENU_ members per page",
                    info: "Showing _START_ to _END_ of _TOTAL_ members",
                    paginate: {
                        first: "First",
                        last: "Last",
                        next: "Next",
                        previous: "Previous"
                    }
                }
            });
        },
        error: function() {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Failed to load member report data.'
            });
        }
    });
}

function generateMemberReport() {
    loadMemberReport();
}

function exportMemberReport() {
    const startDate = $('#start-date').val();
    const endDate = $('#end-date').val();
    const tierFilter = $('#tier-filter').val();
    const statusFilter = $('#status-filter').val();
    
    window.open(`ajax/export-members-report.php?start_date=${startDate}&end_date=${endDate}&tier_filter=${tierFilter}&status_filter=${statusFilter}`, '_blank');
}

function printMemberReport() {
    const startDate = $('#start-date').val();
    const endDate = $('#end-date').val();
    const tierFilter = $('#tier-filter').val();
    const statusFilter = $('#status-filter').val();
    
    window.open(`ajax/print-members-report.php?start_date=${startDate}&end_date=${endDate}&tier_filter=${tierFilter}&status_filter=${statusFilter}`, '_blank');
}
</script>

<?php include 'include/footer.php'; ?>

