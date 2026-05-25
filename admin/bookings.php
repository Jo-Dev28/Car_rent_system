<?php
// admin/bookings.php
require_once '../includes/config.php';

if(!isset($_SESSION['user_role']) || $_SESSION['user_role'] != 'admin') {
    redirect('../login.php');
}

// Update booking status
if(isset($_POST['update_status'])) {
    $stmt = $pdo->prepare("UPDATE bookings SET status = ? WHERE id = ?");
    $stmt->execute([$_POST['status'], $_POST['booking_id']]);
    $_SESSION['success'] = "Booking status updated";
    redirect('bookings.php');
}

// Delete booking
if(isset($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM bookings WHERE id = ?");
    $stmt->execute([$_GET['delete']]);
    $_SESSION['success'] = "Booking deleted";
    redirect('bookings.php');
}

$status_filter = $_GET['status'] ?? 'all';
$query = "SELECT b.*, v.brand, v.model, v.image FROM bookings b JOIN vehicles v ON b.vehicle_id = v.id";
if($status_filter != 'all') {
    $query .= " WHERE b.status = '$status_filter'";
}
$query .= " ORDER BY b.created_at DESC";
$bookings = $pdo->query($query)->fetchAll();

include 'includes/sidebar.php';
?>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<!-- Page Content -->
<?php if(isset($_SESSION['success'])): ?>
    <div class="alert alert-success alert-dismissible fade show">
        <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="dashboard-card">
    <div class="dashboard-card-header">
        <h5><i class="fas fa-filter me-2"></i> Filter Bookings</h5>
        <div class="d-flex gap-2">
            <a href="?status=all" class="btn btn-sm <?php echo $status_filter == 'all' ? 'btn-danger' : 'btn-outline-secondary'; ?>">All</a>
            <a href="?status=pending" class="btn btn-sm <?php echo $status_filter == 'pending' ? 'btn-warning' : 'btn-outline-secondary'; ?>">Pending</a>
            <a href="?status=confirmed" class="btn btn-sm <?php echo $status_filter == 'confirmed' ? 'btn-success' : 'btn-outline-secondary'; ?>">Confirmed</a>
            <a href="?status=completed" class="btn btn-sm <?php echo $status_filter == 'completed' ? 'btn-info' : 'btn-outline-secondary'; ?>">Completed</a>
            <a href="?status=cancelled" class="btn btn-sm <?php echo $status_filter == 'cancelled' ? 'btn-danger' : 'btn-outline-secondary'; ?>">Cancelled</a>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>Booking No</th>
                    <th>Vehicle</th>
                    <th>Customer</th>
                    <th>Pickup Date</th>
                    <th>Return Date</th>
                    <th>Days</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($bookings as $booking): ?>
                <tr>
                    <td><strong><?php echo $booking['booking_no']; ?></strong></td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <img src="<?php echo $booking['image']; ?>" style="width: 50px; height: 50px; object-fit: cover; border-radius: 10px;" alt="">
                            <span><?php echo $booking['brand'] . ' ' . $booking['model']; ?></span>
                        </div>
                    </td>
                    <td>
                        <?php echo htmlspecialchars($booking['customer_name']); ?><br>
                        <small class="text-muted"><?php echo $booking['customer_email']; ?></small>
                    </td>
                    <td><?php echo date('M d, Y', strtotime($booking['pickup_date'])); ?></td>
                    <td><?php echo date('M d, Y', strtotime($booking['return_date'])); ?></td>
                    <td><?php echo $booking['total_days']; ?> days</td>
                    <td class="text-danger fw-bold">$<?php echo number_format($booking['total_price'], 2); ?></td>
                    <td>
                        <span class="badge bg-<?php 
                            echo $booking['status'] == 'confirmed' ? 'success' : 
                                ($booking['status'] == 'pending' ? 'warning' : 
                                ($booking['status'] == 'completed' ? 'info' : 'danger')); 
                        ?> rounded-pill">
                            <?php echo ucfirst($booking['status']); ?>
                        </span>
                    </td>
                    <td>
                        <form method="POST" class="d-inline">
                            <input type="hidden" name="booking_id" value="<?php echo $booking['id']; ?>">
                            <select name="status" class="form-select form-select-sm d-inline-block" style="width: auto;" onchange="this.form.submit()">
                                <option value="pending" <?php echo $booking['status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                                <option value="confirmed" <?php echo $booking['status'] == 'confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                                <option value="completed" <?php echo $booking['status'] == 'completed' ? 'selected' : ''; ?>>Completed</option>
                                <option value="cancelled" <?php echo $booking['status'] == 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                            </select>
                            <input type="hidden" name="update_status" value="1">
                        </form>
                        <a href="?delete=<?php echo $booking['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this booking?')">
                            <i class="fas fa-trash"></i>
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if(count($bookings) == 0): ?>
                <tr>
                    <td colspan="9" class="text-center py-5">
                        <i class="fas fa-calendar-times fa-3x text-muted mb-3 d-block"></i>
                        No bookings found
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
echo '</div></div>';
?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>