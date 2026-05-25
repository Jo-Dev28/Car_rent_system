<?php
// admin/index.php
require_once '../includes/config.php';

if(!isset($_SESSION['user_role']) || $_SESSION['user_role'] != 'admin') {
    redirect('../login.php');
}

// Get admin info
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$admin = $stmt->fetch();

// Get statistics
$totalVehicles = $pdo->query("SELECT COUNT(*) FROM vehicles")->fetchColumn();
$availableVehicles = $pdo->query("SELECT COUNT(*) FROM vehicles WHERE availability = 1")->fetchColumn();
$totalBookings = $pdo->query("SELECT COUNT(*) FROM bookings")->fetchColumn();
$pendingBookings = $pdo->query("SELECT COUNT(*) FROM bookings WHERE status = 'pending'")->fetchColumn();
$confirmedBookings = $pdo->query("SELECT COUNT(*) FROM bookings WHERE status = 'confirmed'")->fetchColumn();
$completedBookings = $pdo->query("SELECT COUNT(*) FROM bookings WHERE status = 'completed'")->fetchColumn();
$cancelledBookings = $pdo->query("SELECT COUNT(*) FROM bookings WHERE status = 'cancelled'")->fetchColumn();
$totalUsers = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'user'")->fetchColumn();
$newUsersThisMonth = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'user' AND MONTH(created_at) = MONTH(CURRENT_DATE()) AND YEAR(created_at) = YEAR(CURRENT_DATE())")->fetchColumn();
$totalRevenue = $pdo->query("SELECT SUM(total_price) FROM bookings WHERE status IN ('confirmed', 'completed')")->fetchColumn();
$monthlyRevenue = $pdo->query("SELECT SUM(total_price) FROM bookings WHERE MONTH(created_at) = MONTH(CURRENT_DATE()) AND YEAR(created_at) = YEAR(CURRENT_DATE()) AND status IN ('confirmed', 'completed')")->fetchColumn();
$todayRevenue = $pdo->query("SELECT SUM(total_price) FROM bookings WHERE DATE(created_at) = CURDATE() AND status IN ('confirmed', 'completed')")->fetchColumn();
$weeklyRevenue = $pdo->query("SELECT SUM(total_price) FROM bookings WHERE WEEK(created_at) = WEEK(CURDATE()) AND YEAR(created_at) = YEAR(CURDATE()) AND status IN ('confirmed', 'completed')")->fetchColumn();

// Get recent bookings
$recentBookings = $pdo->query("SELECT b.*, v.brand, v.model, v.image FROM bookings b JOIN vehicles v ON b.vehicle_id = v.id ORDER BY b.created_at DESC LIMIT 6")->fetchAll();

// Get top vehicles
$topVehicles = $pdo->query("
    SELECT v.brand, v.model, v.image, COUNT(b.id) as total_rentals, SUM(b.total_price) as revenue
    FROM vehicles v
    LEFT JOIN bookings b ON v.id = b.vehicle_id
    GROUP BY v.id
    ORDER BY total_rentals DESC
    LIMIT 4
")->fetchAll();

// Get monthly data for chart
$monthlyData = $pdo->query("
    SELECT 
        DATE_FORMAT(created_at, '%M') as month,
        MONTH(created_at) as month_num,
        SUM(total_price) as revenue,
        COUNT(*) as bookings
    FROM bookings 
    WHERE YEAR(created_at) = YEAR(CURRENT_DATE())
    AND status IN ('confirmed', 'completed')
    GROUP BY MONTH(created_at)
    ORDER BY month_num ASC
")->fetchAll();

// Prepare chart data
$months = [];
$revenues = [];
$bookingsCount = [];
for($i = 1; $i <= 12; $i++) {
    $found = false;
    foreach($monthlyData as $data) {
        if($data['month_num'] == $i) {
            $months[] = $data['month'];
            $revenues[] = $data['revenue'];
            $bookingsCount[] = $data['bookings'];
            $found = true;
            break;
        }
    }
    if(!$found) {
        $months[] = date('F', mktime(0, 0, 0, $i, 1));
        $revenues[] = 0;
        $bookingsCount[] = 0;
    }
}

include 'includes/sidebar.php';
?>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<!-- Page Content -->
<style>
    .welcome-card {
        background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
        border-radius: 20px;
        padding: 30px;
        margin-bottom: 25px;
        color: white;
        position: relative;
        overflow: hidden;
    }
    
    .welcome-card::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -20%;
        width: 300px;
        height: 300px;
        background: rgba(220,53,69,0.15);
        border-radius: 50%;
    }
    
    .welcome-card::after {
        content: '';
        position: absolute;
        bottom: -50%;
        left: -20%;
        width: 250px;
        height: 250px;
        background: rgba(220,53,69,0.1);
        border-radius: 50%;
    }
    
    .welcome-content {
        position: relative;
        z-index: 1;
    }
    
    .greeting-icon {
        font-size: 3rem;
        margin-bottom: 15px;
    }
    
    .stat-icon-circle {
        width: 50px;
        height: 50px;
        background: rgba(220,53,69,0.15);
        border-radius: 15px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 15px;
    }
    
    .stat-icon-circle i {
        font-size: 24px;
        color: #dc3545;
    }
    
    .trend-up {
        color: #28a745;
        font-size: 12px;
    }
    
    .trend-down {
        color: #dc3545;
        font-size: 12px;
    }
    
    .quick-action-btn {
        background: white;
        border: none;
        padding: 15px;
        border-radius: 15px;
        text-align: center;
        transition: all 0.3s;
        text-decoration: none;
        display: block;
    }
    
    .quick-action-btn:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.1);
    }
    
    .quick-action-btn i {
        font-size: 28px;
        color: #dc3545;
        margin-bottom: 10px;
    }
    
    .quick-action-btn span {
        display: block;
        color: #333;
        font-weight: 500;
    }
</style>

<!-- Welcome Section -->
<div class="welcome-card">
    <div class="welcome-content">
        <div class="row align-items-center">
            <div class="col-md-7">
                <div class="greeting-icon">
                    <i class="fas fa-hand-wave"></i>
                </div>
                <h2 class="mb-2">Welcome back, <?php echo htmlspecialchars($admin['full_name'] ?: $admin['username']); ?>!</h2>
                <p class="mb-0 opacity-75">Here's what's happening with your car rental business today. You have <?php echo $pendingBookings; ?> pending bookings to review.</p>
            </div>
            <div class="col-md-5 text-center">
                <div class="row g-2">
                    <div class="col-6">
                        <div class="bg-white bg-opacity-10 rounded-3 p-2">
                            <small>Today's Revenue</small>
                            <h4 class="mb-0 text-white">$<?php echo number_format($todayRevenue ?? 0, 2); ?></h4>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="bg-white bg-opacity-10 rounded-3 p-2">
                            <small>This Week</small>
                            <h4 class="mb-0 text-white">$<?php echo number_format($weeklyRevenue ?? 0, 2); ?></h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Statistics Cards Row -->
<div class="row g-4 mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="dashboard-card">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-icon-circle">
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                    <h3 class="mb-0">$<?php echo number_format($monthlyRevenue ?? 0, 2); ?></h3>
                    <small class="text-muted">This Month Revenue</small>
                </div>
                <div>
                    <span class="trend-up"><i class="fas fa-arrow-up"></i> +12.5%</span>
                </div>
            </div>
            <div class="mt-3 pt-2 border-top">
                <small class="text-muted">Total Revenue: $<?php echo number_format($totalRevenue ?? 0, 2); ?></small>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="dashboard-card">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-icon-circle">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <h3 class="mb-0"><?php echo $totalBookings; ?></h3>
                    <small class="text-muted">Total Bookings</small>
                </div>
                <div>
                    <span class="trend-up"><i class="fas fa-arrow-up"></i> +8.2%</span>
                </div>
            </div>
            <div class="mt-3 pt-2 border-top">
                <small class="text-muted">Pending: <?php echo $pendingBookings; ?> | Confirmed: <?php echo $confirmedBookings; ?></small>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="dashboard-card">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-icon-circle">
                        <i class="fas fa-car"></i>
                    </div>
                    <h3 class="mb-0"><?php echo $availableVehicles; ?>/<?php echo $totalVehicles; ?></h3>
                    <small class="text-muted">Available Vehicles</small>
                </div>
                <div>
                    <span class="trend-up"><i class="fas fa-arrow-up"></i> +3 new</span>
                </div>
            </div>
            <div class="mt-3 pt-2 border-top">
                <small class="text-muted"><?php echo $totalVehicles - $availableVehicles; ?> vehicles currently rented</small>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="dashboard-card">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-icon-circle">
                        <i class="fas fa-users"></i>
                    </div>
                    <h3 class="mb-0"><?php echo $totalUsers; ?></h3>
                    <small class="text-muted">Total Customers</small>
                </div>
                <div>
                    <span class="trend-up"><i class="fas fa-arrow-up"></i> +<?php echo $newUsersThisMonth; ?> this month</span>
                </div>
            </div>
            <div class="mt-3 pt-2 border-top">
                <small class="text-muted">Active customers: <?php echo $pdo->query("SELECT COUNT(*) FROM users WHERE is_active = 1 AND role = 'user'")->fetchColumn(); ?></small>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row g-4 mb-4">
    <div class="col-12">
        <h5 class="mb-3"><i class="fas fa-bolt text-danger me-2"></i> Quick Actions</h5>
        <div class="row g-3">
            <div class="col-md-3 col-6">
                <a href="vehicles.php" class="quick-action-btn">
                    <i class="fas fa-plus-circle"></i>
                    <span>Add Vehicle</span>
                </a>
            </div>
            <div class="col-md-3 col-6">
                <a href="bookings.php" class="quick-action-btn">
                    <i class="fas fa-eye"></i>
                    <span>View Bookings</span>
                </a>
            </div>
            <div class="col-md-3 col-6">
                <a href="reports.php" class="quick-action-btn">
                    <i class="fas fa-chart-line"></i>
                    <span>Generate Report</span>
                </a>
            </div>
            <div class="col-md-3 col-6">
                <a href="users.php" class="quick-action-btn">
                    <i class="fas fa-user-plus"></i>
                    <span>Manage Users</span>
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="row g-4 mb-4">
    <div class="col-lg-8">
        <div class="dashboard-card">
            <div class="dashboard-card-header">
                <h5><i class="fas fa-chart-line text-danger me-2"></i> Revenue Overview <?php echo date('Y'); ?></h5>
                <div class="btn-group btn-group-sm">
                    <button class="btn btn-outline-danger active" onclick="showRevenue()">Revenue</button>
                    <button class="btn btn-outline-danger" onclick="showBookings()">Bookings</button>
                </div>
            </div>
            <canvas id="revenueChart" height="280"></canvas>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="dashboard-card">
            <div class="dashboard-card-header">
                <h5><i class="fas fa-chart-pie text-danger me-2"></i> Booking Distribution</h5>
            </div>
            <canvas id="statusChart" height="200"></canvas>
            <div class="row text-center mt-3">
                <div class="col-3">
                    <div class="bg-success bg-opacity-10 rounded p-2">
                        <small class="text-success">Confirmed</small>
                        <h6 class="mb-0"><?php echo $confirmedBookings; ?></h6>
                    </div>
                </div>
                <div class="col-3">
                    <div class="bg-warning bg-opacity-10 rounded p-2">
                        <small class="text-warning">Pending</small>
                        <h6 class="mb-0"><?php echo $pendingBookings; ?></h6>
                    </div>
                </div>
                <div class="col-3">
                    <div class="bg-info bg-opacity-10 rounded p-2">
                        <small class="text-info">Completed</small>
                        <h6 class="mb-0"><?php echo $completedBookings; ?></h6>
                    </div>
                </div>
                <div class="col-3">
                    <div class="bg-danger bg-opacity-10 rounded p-2">
                        <small class="text-danger">Cancelled</small>
                        <h6 class="mb-0"><?php echo $cancelledBookings; ?></h6>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Bookings & Top Vehicles -->
<div class="row g-4">
    <div class="col-lg-7">
        <div class="dashboard-card">
            <div class="dashboard-card-header">
                <h5><i class="fas fa-clock text-danger me-2"></i> Recent Bookings</h5>
                <a href="bookings.php" class="btn btn-sm btn-outline-danger">View All</a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Booking ID</th>
                            <th>Vehicle</th>
                            <th>Customer</th>
                            <th>Date</th>
                            <th>Amount</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($recentBookings as $booking): ?>
                        <tr>
                            <td><small class="text-muted"><?php echo $booking['booking_no']; ?></small></td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <img src="<?php echo $booking['image']; ?>" style="width: 35px; height: 35px; object-fit: cover; border-radius: 8px;">
                                    <?php echo $booking['brand'] . ' ' . $booking['model']; ?>
                                </div>
                            </td>
                            <td><?php echo $booking['customer_name']; ?></small></td>
                            <td><?php echo date('M d', strtotime($booking['pickup_date'])); ?></td>
                            <td class="text-danger fw-bold">$<?php echo number_format($booking['total_price'], 2); ?></td>
                            <td>
                                <span class="badge bg-<?php 
                                    echo $booking['status'] == 'confirmed' ? 'success' : 
                                        ($booking['status'] == 'pending' ? 'warning' : 
                                        ($booking['status'] == 'completed' ? 'info' : 'danger')); 
                                ?> rounded-pill">
                                    <?php echo ucfirst($booking['status']); ?>
                                </span>
                            </small></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="dashboard-card">
            <div class="dashboard-card-header">
                <h5><i class="fas fa-trophy text-danger me-2"></i> Top Performing Vehicles</h5>
                <a href="vehicles.php" class="btn btn-sm btn-outline-danger">Manage</a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr><th>Vehicle</th><th>Rentals</th><th>Revenue</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach($topVehicles as $vehicle): ?>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <img src="<?php echo $vehicle['image']; ?>" style="width: 40px; height: 40px; object-fit: cover; border-radius: 8px;">
                                    <?php echo $vehicle['brand'] . ' ' . $vehicle['model']; ?>
                                </div>
                            </small></td>
                            <td><?php echo $vehicle['total_rentals'] ?? 0; ?> bookings</small></td>
                            <td class="text-danger">$<?php echo number_format($vehicle['revenue'] ?? 0, 2); ?></small></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Revenue Chart
let revenueChart = new Chart(document.getElementById('revenueChart'), {
    type: 'line',
    data: {
        labels: <?php echo json_encode($months); ?>,
        datasets: [{
            label: 'Revenue ($)',
            data: <?php echo json_encode($revenues); ?>,
            borderColor: '#dc3545',
            backgroundColor: 'rgba(220, 53, 69, 0.05)',
            tension: 0.4,
            fill: true,
            pointBackgroundColor: '#dc3545',
            pointBorderColor: 'white',
            pointRadius: 5,
            pointHoverRadius: 7
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: { position: 'bottom' }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) { return '$' + value; }
                }
            }
        }
    }
});

// Status Chart
new Chart(document.getElementById('statusChart'), {
    type: 'doughnut',
    data: {
        labels: ['Confirmed', 'Pending', 'Completed', 'Cancelled'],
        datasets: [{
            data: [<?php echo $confirmedBookings; ?>, <?php echo $pendingBookings; ?>, <?php echo $completedBookings; ?>, <?php echo $cancelledBookings; ?>],
            backgroundColor: ['#28a745', '#ffc107', '#17a2b8', '#dc3545'],
            borderWidth: 0,
            hoverOffset: 10
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        cutout: '60%',
        plugins: {
            legend: { position: 'bottom' }
        }
    }
});

function showRevenue() {
    revenueChart.data.datasets[0].data = <?php echo json_encode($revenues); ?>;
    revenueChart.data.datasets[0].label = 'Revenue ($)';
    revenueChart.update();
}

function showBookings() {
    revenueChart.data.datasets[0].data = <?php echo json_encode($bookingsCount); ?>;
    revenueChart.data.datasets[0].label = 'Number of Bookings';
    revenueChart.update();
}
</script>

<?php
echo '</div></div>';
?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>