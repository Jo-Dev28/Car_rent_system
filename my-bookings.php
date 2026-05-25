<?php
// my-bookings.php
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

$stmt = $pdo->prepare("SELECT b.*, v.brand, v.model, v.image FROM bookings b JOIN vehicles v ON b.vehicle_id = v.id WHERE b.user_id = ? ORDER BY b.created_at DESC");
$stmt->execute([$_SESSION['user_id']]);
$bookings = $stmt->fetchAll();
?>

<style>
    .my-bookings-section {
        background: #f8f9fa;
        min-height: calc(100vh - 200px);
    }
    
    .booking-card {
        background: white;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(0,0,0,0.08);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        height: 100%;
    }
    
    .booking-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 40px rgba(0,0,0,0.12);
    }
    
    .booking-header {
        background: linear-gradient(135deg, #1a1a2e, #16213e);
        color: white;
        padding: 15px 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .booking-no {
        font-weight: 600;
        font-size: 0.85rem;
        font-family: monospace;
    }
    
    .booking-body {
        padding: 20px;
    }
    
    .booking-car-img {
        width: 100%;
        height: 160px;
        object-fit: cover;
        border-radius: 15px;
        margin-bottom: 15px;
    }
    
    .booking-body h4 {
        font-size: 1.2rem;
        font-weight: 700;
        margin-bottom: 15px;
    }
    
    .booking-dates {
        background: #f8f9fa;
        padding: 12px;
        border-radius: 12px;
        margin: 15px 0;
    }
    
    .booking-dates div {
        margin-bottom: 8px;
        font-size: 0.85rem;
    }
    
    .booking-dates div:last-child {
        margin-bottom: 0;
    }
    
    .booking-dates i {
        color: #dc3545;
        width: 20px;
        margin-right: 8px;
    }
    
    .booking-price {
        font-size: 1.1rem;
        text-align: center;
        padding-top: 15px;
        border-top: 1px solid #e9ecef;
        margin-top: 10px;
    }
    
    .booking-price strong {
        font-size: 1.3rem;
        color: #dc3545;
    }
    
    .booking-footer {
        padding: 15px 20px 20px;
        border-top: 1px solid #e9ecef;
    }
    
    .section-header h2 {
        font-family: 'Orbitron', monospace;
    }
    
    @media (max-width: 768px) {
        .booking-card {
            margin-bottom: 20px;
        }
    }
</style>

<section class="my-bookings-section py-5">
    <div class="container">
        <div class="section-header text-center mb-5" data-aos="fade-up">
            <span class="badge bg-danger px-3 py-2 mb-3" style="border-radius: 30px;">My Rentals</span>
            <h2 class="display-4 fw-bold">My <span class="text-danger">Bookings</span></h2>
            <p class="text-muted">View and manage all your rental bookings</p>
        </div>
        
        <?php if(count($bookings) > 0): ?>
            <div class="row g-4">
                <?php foreach($bookings as $booking): ?>
                <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="<?php echo $booking['id'] * 100; ?>">
                    <div class="booking-card">
                        <div class="booking-header">
                            <span class="booking-no"><i class="fas fa-ticket-alt me-2"></i> <?php echo $booking['booking_no']; ?></span>
                            <span class="badge bg-<?php 
                                echo $booking['status'] == 'confirmed' ? 'success' : 
                                    ($booking['status'] == 'pending' ? 'warning' : 
                                    ($booking['status'] == 'completed' ? 'info' : 'danger')); 
                            ?> rounded-pill px-3 py-2">
                                <?php echo ucfirst($booking['status']); ?>
                            </span>
                        </div>
                        <div class="booking-body">
                            <img src="<?php echo getUserImagePath($booking['image']); ?>" alt="<?php echo $booking['brand'] . ' ' . $booking['model']; ?>" class="booking-car-img">
                            <h4><?php echo htmlspecialchars($booking['brand'] . ' ' . $booking['model']); ?></h4>
                            <div class="booking-dates">
                                <div><i class="fas fa-calendar-alt"></i> Pickup: <?php echo date('F d, Y', strtotime($booking['pickup_date'])); ?></div>
                                <div><i class="fas fa-calendar-check"></i> Return: <?php echo date('F d, Y', strtotime($booking['return_date'])); ?></div>
                                <div><i class="fas fa-clock"></i> Duration: <?php echo $booking['total_days']; ?> days</div>
                            </div>
                            <div class="booking-price">
                                Total: <strong>$<?php echo number_format($booking['total_price'], 2); ?></strong>
                            </div>
                        </div>
                        <div class="booking-footer">
                            <a href="booking-details.php?id=<?php echo $booking['id']; ?>" class="btn btn-outline-danger w-100 rounded-pill">
                                View Details <i class="fas fa-arrow-right ms-2"></i>
                            </a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="text-center py-5" data-aos="fade-up">
                <div class="empty-state">
                    <i class="fas fa-calendar-alt fa-5x text-muted mb-4"></i>
                    <h3 class="mb-3">No Bookings Yet</h3>
                    <p class="text-muted mb-4">Start your luxury driving experience today!<br>Choose from our premium fleet of vehicles.</p>
                    <a href="cars.php" class="btn btn-danger btn-lg rounded-pill px-5">
                        <i class="fas fa-car me-2"></i> Browse Vehicles
                    </a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php include 'includes/footer.php'; ?>