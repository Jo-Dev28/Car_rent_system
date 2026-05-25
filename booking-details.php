<?php
// booking-details.php
include 'includes/config.php';
include 'includes/header.php';

// Function to get correct image path
function getUserImagePath($image) {
    if(empty($image)) {
        return 'assets/images/default-car.jpg';
    }
    $image = str_replace('../', '', $image);
    if(strpos($image, '/') === false) {
        return 'uploads/' . $image;
    }
    return $image;
}

if(!isLoggedIn()) {
    redirect('login.php');
}

$booking_id = $_GET['id'] ?? 0;

$stmt = $pdo->prepare("
    SELECT b.*, v.brand, v.model, v.image, v.year, v.transmission, v.fuel_type, v.seats, v.rental_price as daily_rate 
    FROM bookings b 
    JOIN vehicles v ON b.vehicle_id = v.id 
    WHERE b.id = ? AND b.user_id = ?
");
$stmt->execute([$booking_id, $_SESSION['user_id']]);
$booking = $stmt->fetch();

if(!$booking) {
    redirect('dashboard.php');
}
?>

<style>
    .booking-details-wrapper {
        background: #f8f9fa;
        min-height: calc(100vh - 200px);
        padding: 60px 0;
    }
    
    .booking-details-card {
        background: white;
        border-radius: 25px;
        overflow: hidden;
        box-shadow: 0 10px 40px rgba(0,0,0,0.1);
    }
    
    .booking-header {
        background: linear-gradient(135deg, #1a1a2e, #16213e);
        color: white;
        padding: 30px;
    }
    
    .booking-status {
        display: inline-block;
        padding: 8px 20px;
        border-radius: 30px;
        font-size: 0.85rem;
        font-weight: 600;
        margin-top: 15px;
    }
    
    .booking-body {
        padding: 30px;
    }
    
    .info-section {
        margin-bottom: 30px;
    }
    
    .info-section h4 {
        font-size: 1.2rem;
        font-weight: 700;
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 2px solid #f0f0f0;
    }
    
    .info-section h4 i {
        color: #dc3545;
        margin-right: 10px;
    }
    
    .info-row {
        display: flex;
        justify-content: space-between;
        padding: 12px 0;
        border-bottom: 1px solid #f0f0f0;
    }
    
    .info-label {
        font-weight: 500;
        color: #6c757d;
    }
    
    .info-value {
        font-weight: 500;
        color: #333;
    }
    
    .vehicle-image {
        width: 100%;
        border-radius: 15px;
        margin-bottom: 20px;
    }
    
    .spec-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 15px;
        background: #f8f9fa;
        border-radius: 12px;
        margin-right: 10px;
        margin-bottom: 10px;
        font-size: 0.85rem;
    }
    
    .price-breakdown {
        background: #f8f9fa;
        border-radius: 15px;
        padding: 20px;
        margin-top: 20px;
    }
    
    .total-amount {
        font-size: 1.5rem;
        font-weight: 700;
        color: #dc3545;
    }
    
    .action-buttons {
        display: flex;
        gap: 15px;
        margin-top: 30px;
        flex-wrap: wrap;
    }
    
    @media (max-width: 768px) {
        .booking-header {
            padding: 20px;
        }
        
        .booking-body {
            padding: 20px;
        }
        
        .info-row {
            flex-direction: column;
        }
        
        .info-value {
            margin-top: 5px;
        }
        
        .action-buttons {
            flex-direction: column;
        }
        
        .action-buttons .btn {
            width: 100%;
        }
    }
</style>

<section class="booking-details-wrapper">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="booking-details-card" data-aos="fade-up">
                    <div class="booking-header">
                        <h3><i class="fas fa-receipt me-2"></i> Booking Details</h3>
                        <p class="mb-0">Booking Reference: <strong><?php echo $booking['booking_no']; ?></strong></p>
                        <span class="booking-status bg-<?php 
                            echo $booking['status'] == 'confirmed' ? 'success' : 
                                ($booking['status'] == 'pending' ? 'warning' : 
                                ($booking['status'] == 'completed' ? 'info' : 'danger')); 
                        ?>">
                            <i class="fas fa-<?php 
                                echo $booking['status'] == 'confirmed' ? 'check-circle' : 
                                    ($booking['status'] == 'pending' ? 'clock' : 
                                    ($booking['status'] == 'completed' ? 'flag-checkered' : 'times-circle')); 
                            ?> me-2"></i>
                            <?php echo ucfirst($booking['status']); ?>
                        </span>
                    </div>
                    
                    <div class="booking-body">
                        <div class="row">
                            <div class="col-lg-5">
                                <img src="<?php echo getUserImagePath($booking['image']); ?>" alt="<?php echo $booking['model']; ?>" class="vehicle-image">
                                <div class="specs">
                                    <div class="spec-badge"><i class="fas fa-calendar text-danger"></i> <?php echo $booking['year']; ?></div>
                                    <div class="spec-badge"><i class="fas fa-cogs text-danger"></i> <?php echo $booking['transmission']; ?></div>
                                    <div class="spec-badge"><i class="fas fa-gas-pump text-danger"></i> <?php echo $booking['fuel_type']; ?></div>
                                    <div class="spec-badge"><i class="fas fa-users text-danger"></i> <?php echo $booking['seats']; ?> Seats</div>
                                </div>
                            </div>
                            
                            <div class="col-lg-7">
                                <div class="info-section">
                                    <h4><i class="fas fa-car"></i> Vehicle Information</h4>
                                    <div class="info-row">
                                        <span class="info-label">Vehicle:</span>
                                        <span class="info-value"><?php echo $booking['brand'] . ' ' . $booking['model']; ?></span>
                                    </div>
                                    <div class="info-row">
                                        <span class="info-label">Daily Rate:</span>
                                        <span class="info-value">$<?php echo number_format($booking['daily_rate'], 2); ?> / day</span>
                                    </div>
                                </div>
                                
                                <div class="info-section">
                                    <h4><i class="fas fa-calendar-alt"></i> Rental Period</h4>
                                    <div class="info-row">
                                        <span class="info-label">Pickup Date:</span>
                                        <span class="info-value"><?php echo date('F d, Y', strtotime($booking['pickup_date'])); ?></span>
                                    </div>
                                    <div class="info-row">
                                        <span class="info-label">Return Date:</span>
                                        <span class="info-value"><?php echo date('F d, Y', strtotime($booking['return_date'])); ?></span>
                                    </div>
                                    <div class="info-row">
                                        <span class="info-label">Total Days:</span>
                                        <span class="info-value"><?php echo $booking['total_days']; ?> day(s)</span>
                                    </div>
                                </div>
                                
                                <div class="info-section">
                                    <h4><i class="fas fa-user"></i> Customer Information</h4>
                                    <div class="info-row">
                                        <span class="info-label">Full Name:</span>
                                        <span class="info-value"><?php echo htmlspecialchars($booking['customer_name']); ?></span>
                                    </div>
                                    <div class="info-row">
                                        <span class="info-label">Email Address:</span>
                                        <span class="info-value"><?php echo htmlspecialchars($booking['customer_email']); ?></span>
                                    </div>
                                    <div class="info-row">
                                        <span class="info-label">Phone Number:</span>
                                        <span class="info-value"><?php echo htmlspecialchars($booking['customer_phone']); ?></span>
                                    </div>
                                </div>
                                
                                <div class="price-breakdown">
                                    <div class="info-row">
                                        <span class="info-label">Daily Rate x <?php echo $booking['total_days']; ?> days:</span>
                                        <span class="info-value">$<?php echo number_format($booking['daily_rate'] * $booking['total_days'], 2); ?></span>
                                    </div>
                                    <div class="info-row">
                                        <span class="info-label">Insurance:</span>
                                        <span class="info-value text-success">Included</span>
                                    </div>
                                    <div class="info-row">
                                        <span class="info-label">Tax (10%):</span>
                                        <span class="info-value">$<?php echo number_format($booking['total_price'] * 0.1, 2); ?></span>
                                    </div>
                                    <div class="info-row">
                                        <span class="info-label"><strong>Total Amount:</strong></span>
                                        <span class="total-amount">$<?php echo number_format($booking['total_price'], 2); ?></span>
                                    </div>
                                </div>
                                
                                <div class="action-buttons">
                                    <?php if($booking['status'] == 'pending'): ?>
                                        <button class="btn btn-danger" onclick="cancelBooking(<?php echo $booking['id']; ?>)">
                                            <i class="fas fa-times me-2"></i> Cancel Booking
                                        </button>
                                    <?php endif; ?>
                                    <a href="cars.php" class="btn btn-outline-danger">
                                        <i class="fas fa-car me-2"></i> Book Another Car
                                    </a>
                                    <button onclick="window.print()" class="btn btn-outline-secondary">
                                        <i class="fas fa-print me-2"></i> Print Details
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
function cancelBooking(bookingId) {
    if(confirm('Are you sure you want to cancel this booking? This action cannot be undone.')) {
        window.location.href = 'cancel-booking.php?id=' + bookingId;
    }
}
</script>

<?php include 'includes/footer.php'; ?>