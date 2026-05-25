<?php
// dashboard.php
include 'includes/config.php';

if(!isLoggedIn()) {
    redirect('login.php');
}

// Get user bookings
$stmt = $pdo->prepare("SELECT b.*, v.brand, v.model, v.image FROM bookings b JOIN vehicles v ON b.vehicle_id = v.id WHERE b.user_id = ? ORDER BY b.created_at DESC");
$stmt->execute([$_SESSION['user_id']]);
$bookings = $stmt->fetchAll();

include 'includes/header.php';
?>

<section class="dashboard-section py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-3" data-aos="fade-right">
                <div class="dashboard-sidebar">
                    <div class="user-profile">
                        <i class="fas fa-user-circle fa-4x text-danger"></i>
                        <h4><?php echo htmlspecialchars($_SESSION['user_name']); ?></h4>
                        <p><?php echo htmlspecialchars($_SESSION['user_email']); ?></p>
                    </div>
                    <div class="dashboard-menu">
                        <a href="dashboard.php" class="active"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
                        <a href="my-bookings.php"><i class="fas fa-calendar-alt"></i> My Bookings</a>
                        <a href="profile.php"><i class="fas fa-user"></i> Profile Settings</a>
                        <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                    </div>
                </div>
            </div>
            <div class="col-lg-9" data-aos="fade-left">
                <div class="dashboard-content">
                    <h2>My Dashboard</h2>
                    
                    <!-- Stats Cards -->
                    <div class="row g-4 mb-4">
                        <div class="col-md-4">
                            <div class="stat-card">
                                <div class="stat-card-icon"><i class="fas fa-calendar-check"></i></div>
                                <div class="stat-card-info">
                                    <h3><?php echo count($bookings); ?></h3>
                                    <p>Total Bookings</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="stat-card">
                                <div class="stat-card-icon"><i class="fas fa-check-circle"></i></div>
                                <div class="stat-card-info">
                                    <h3><?php echo count(array_filter($bookings, fn($b) => $b['status'] == 'confirmed')); ?></h3>
                                    <p>Active Bookings</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="stat-card">
                                <div class="stat-card-icon"><i class="fas fa-clock"></i></div>
                                <div class="stat-card-info">
                                    <h3><?php echo count(array_filter($bookings, fn($b) => $b['status'] == 'pending')); ?></h3>
                                    <p>Pending</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Recent Bookings -->
                    <div class="recent-bookings">
                        <h3>Recent Bookings</h3>
                        <?php if(count($bookings) > 0): ?>
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr><th>Booking No</th><th>Vehicle</th><th>Dates</th><th>Total</th><th>Status</th><th>Action</th></tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach(array_slice($bookings, 0, 5) as $booking): ?>
                                        <tr>
                                            <td><?php echo $booking['booking_no']; ?></td>
                                            <td><?php echo $booking['brand'] . ' ' . $booking['model']; ?></td>
                                            <td><?php echo $booking['pickup_date']; ?> to <?php echo $booking['return_date']; ?></td>
                                            <td>$<?php echo number_format($booking['total_price'], 2); ?></td>
                                            <td><span class="badge bg-<?php echo $booking['status'] == 'confirmed' ? 'success' : ($booking['status'] == 'pending' ? 'warning' : 'secondary'); ?>"><?php echo $booking['status']; ?></span></td>
                                            <td><a href="booking-details.php?id=<?php echo $booking['id']; ?>" class="btn btn-sm btn-outline-danger">View</a></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <p>No bookings yet. <a href="cars.php">Browse our fleet</a></p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>