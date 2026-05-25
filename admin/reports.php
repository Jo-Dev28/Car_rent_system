<?php
// admin/reports.php
require_once '../includes/config.php';

if(!isset($_SESSION['user_role']) || $_SESSION['user_role'] != 'admin') {
    redirect('../login.php');
}

// Get date filters
$start_date = $_GET['start_date'] ?? date('Y-m-01');
$end_date = $_GET['end_date'] ?? date('Y-m-t');
$report_type = $_GET['report_type'] ?? 'bookings';

// Get report data
if($report_type == 'bookings') {
    $stmt = $pdo->prepare("
        SELECT b.*, v.brand, v.model 
        FROM bookings b 
        JOIN vehicles v ON b.vehicle_id = v.id 
        WHERE DATE(b.created_at) BETWEEN ? AND ?
        ORDER BY b.created_at DESC
    ");
    $stmt->execute([$start_date, $end_date]);
    $report_data = $stmt->fetchAll();
    $total_amount = $pdo->prepare("SELECT SUM(total_price) FROM bookings WHERE DATE(created_at) BETWEEN ? AND ? AND status IN ('confirmed', 'completed')");
    $total_amount->execute([$start_date, $end_date]);
    $total_revenue = $total_amount->fetchColumn();
} elseif($report_type == 'vehicles') {
    $report_data = $pdo->query("
        SELECT v.*, COUNT(b.id) as total_rentals, SUM(b.total_price) as total_revenue
        FROM vehicles v
        LEFT JOIN bookings b ON v.id = b.vehicle_id
        GROUP BY v.id
        ORDER BY total_rentals DESC
    ")->fetchAll();
    $total_revenue = $pdo->query("SELECT SUM(total_price) FROM bookings WHERE status IN ('confirmed', 'completed')")->fetchColumn();
} else {
    $report_data = $pdo->prepare("
        SELECT * FROM users WHERE role = 'user' AND DATE(created_at) BETWEEN ? AND ? ORDER BY created_at DESC
    ");
    $report_data->execute([$start_date, $end_date]);
    $report_data = $report_data->fetchAll();
    $total_revenue = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'user'")->fetchColumn();
}

include 'includes/sidebar.php';
?>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<!-- Page Content -->
<div class="dashboard-card">
    <div class="dashboard-card-header">
        <h5><i class="fas fa-filter me-2"></i> Filter Reports</h5>
    </div>
    <form method="GET" action="" class="row g-3">
        <div class="col-md-3">
            <label class="form-label">Report Type</label>
            <select name="report_type" class="form-select">
                <option value="bookings" <?php echo $report_type == 'bookings' ? 'selected' : ''; ?>>Bookings Report</option>
                <option value="vehicles" <?php echo $report_type == 'vehicles' ? 'selected' : ''; ?>>Vehicles Report</option>
                <option value="users" <?php echo $report_type == 'users' ? 'selected' : ''; ?>>Users Report</option>
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label">Start Date</label>
            <input type="date" name="start_date" class="form-control" value="<?php echo $start_date; ?>">
        </div>
        <div class="col-md-3">
            <label class="form-label">End Date</label>
            <input type="date" name="end_date" class="form-control" value="<?php echo $end_date; ?>">
        </div>
        <div class="col-md-3">
            <label class="form-label">&nbsp;</label>
            <button type="submit" class="btn btn-danger w-100">Generate Report</button>
        </div>
    </form>
</div>

<div class="dashboard-card">
    <div class="dashboard-card-header">
        <h5><i class="fas fa-chart-bar me-2"></i> Report Summary</h5>
        <div>
            <button onclick="window.print()" class="btn btn-sm btn-outline-danger me-2"><i class="fas fa-print"></i> Print</button>
            <button id="exportExcel" class="btn btn-sm btn-success"><i class="fas fa-file-excel"></i> Export Excel</button>
        </div>
    </div>
    
    <?php if($report_type == 'bookings'): ?>
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="p-3 bg-light rounded">
                    <small class="text-muted">Total Bookings</small>
                    <h4 class="mb-0"><?php echo count($report_data); ?></h4>
                </div>
            </div>
            <div class="col-md-4">
                <div class="p-3 bg-light rounded">
                    <small class="text-muted">Total Revenue</small>
                    <h4 class="mb-0 text-danger">$<?php echo number_format($total_revenue ?? 0, 2); ?></h4>
                </div>
            </div>
            <div class="col-md-4">
                <div class="p-3 bg-light rounded">
                    <small class="text-muted">Period</small>
                    <h4 class="mb-0"><?php echo date('M d, Y', strtotime($start_date)); ?> - <?php echo date('M d, Y', strtotime($end_date)); ?></h4>
                </div>
            </div>
        </div>
        
        <div class="table-responsive">
            <table class="table table-bordered" id="reportTable">
                <thead class="table-dark">
                    <tr>
                        <th>Booking No</th>
                        <th>Vehicle</th>
                        <th>Customer</th>
                        <th>Pickup Date</th>
                        <th>Return Date</th>
                        <th>Days</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Booking Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($report_data as $row): ?>
                    <tr>
                        <td><?php echo $row['booking_no']; ?></small></td>
                        <td><?php echo $row['brand'] . ' ' . $row['model']; ?></small></td>
                        <td><?php echo $row['customer_name']; ?></small></td>
                        <td><?php echo date('M d, Y', strtotime($row['pickup_date'])); ?></small></td>
                        <td><?php echo date('M d, Y', strtotime($row['return_date'])); ?></small></td>
                        <td><?php echo $row['total_days']; ?> days</small></td>
                        <td class="text-danger">$<?php echo number_format($row['total_price'], 2); ?></small></td>
                        <td>
                            <span class="badge bg-<?php echo $row['status'] == 'confirmed' ? 'success' : ($row['status'] == 'pending' ? 'warning' : 'secondary'); ?>">
                                <?php echo ucfirst($row['status']); ?>
                            </span>
                        </small></td>
                        <td><?php echo date('M d, Y', strtotime($row['created_at'])); ?></small></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
    <?php elseif($report_type == 'vehicles'): ?>
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="p-3 bg-light rounded">
                    <small class="text-muted">Total Vehicles</small>
                    <h4 class="mb-0"><?php echo count($report_data); ?></h4>
                </div>
            </div>
            <div class="col-md-4">
                <div class="p-3 bg-light rounded">
                    <small class="text-muted">Total Revenue</small>
                    <h4 class="mb-0 text-danger">$<?php echo number_format($total_revenue ?? 0, 2); ?></h4>
                </div>
            </div>
            <div class="col-md-4">
                <div class="p-3 bg-light rounded">
                    <small class="text-muted">Available Vehicles</small>
                    <h4 class="mb-0"><?php echo $pdo->query("SELECT COUNT(*) FROM vehicles WHERE availability = 1")->fetchColumn(); ?></h4>
                </div>
            </div>
        </div>
        
        <div class="table-responsive">
            <table class="table table-bordered" id="reportTable">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Vehicle</th>
                        <th>Year</th>
                        <th>Price/Day</th>
                        <th>Total Rentals</th>
                        <th>Total Revenue</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($report_data as $row): ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><strong><?php echo $row['brand'] . ' ' . $row['model']; ?></strong><br><small class="text-muted"><?php echo $row['fuel_type']; ?> | <?php echo $row['seats']; ?> seats</small></td>
                        <td><?php echo $row['year']; ?></small></td>
                        <td class="text-danger">$<?php echo number_format($row['rental_price'], 2); ?></small></td>
                        <td><?php echo $row['total_rentals'] ?? 0; ?> bookings</small></td>
                        <td class="text-danger">$<?php echo number_format($row['total_revenue'] ?? 0, 2); ?></small></td>
                        <td>
                            <span class="badge bg-<?php echo $row['availability'] ? 'success' : 'danger'; ?>">
                                <?php echo $row['availability'] ? 'Available' : 'Booked'; ?>
                            </span>
                        </small></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
    <?php else: ?>
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="p-3 bg-light rounded">
                    <small class="text-muted">Total Users</small>
                    <h4 class="mb-0"><?php echo count($report_data); ?></h4>
                </div>
            </div>
            <div class="col-md-4">
                <div class="p-3 bg-light rounded">
                    <small class="text-muted">New Users (Period)</small>
                    <h4 class="mb-0"><?php echo count($report_data); ?></h4>
                </div>
            </div>
            <div class="col-md-4">
                <div class="p-3 bg-light rounded">
                    <small class="text-muted">Active Users</small>
                    <h4 class="mb-0"><?php echo $pdo->query("SELECT COUNT(*) FROM users WHERE is_active = 1 AND role = 'user'")->fetchColumn(); ?></h4>
                </div>
            </div>
        </div>
        
        <div class="table-responsive">
            <table class="table table-bordered" id="reportTable">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Full Name</th>
                        <th>Phone</th>
                        <th>Status</th>
                        <th>Registered Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($report_data as $row): ?>
                    <tr>
                        <td><?php echo $row['id']; ?></small></td>
                        <td><strong><?php echo $row['username']; ?></strong></small></td>
                        <td><?php echo $row['email']; ?></small></td>
                        <td><?php echo $row['full_name'] ?? '-'; ?></small></td>
                        <td><?php echo $row['phone'] ?? '-'; ?></small></td>
                        <td>
                            <span class="badge bg-<?php echo $row['is_active'] ? 'success' : 'secondary'; ?>">
                                <?php echo $row['is_active'] ? 'Active' : 'Inactive'; ?>
                            </span>
                        </small></td>
                        <td><?php echo date('M d, Y', strtotime($row['created_at'])); ?></small></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<script>
document.getElementById('exportExcel').addEventListener('click', function() {
    let table = document.getElementById('reportTable');
    let html = table.outerHTML;
    let url = 'data:application/vnd.ms-excel,' + encodeURIComponent(html);
    let downloadLink = document.createElement('a');
    downloadLink.href = url;
    downloadLink.download = 'report_<?php echo $report_type; ?>_<?php echo date('Y-m-d'); ?>.xls';
    document.body.appendChild(downloadLink);
    downloadLink.click();
    document.body.removeChild(downloadLink);
});
</script>

<?php
echo '</div></div>';
?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>