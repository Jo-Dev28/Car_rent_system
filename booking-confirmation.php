<?php
// booking-confirmation.php
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

$booking_no = $_GET['booking'] ?? '';
if(!$booking_no) {
    redirect('dashboard.php');
}

$stmt = $pdo->prepare("SELECT b.*, v.brand, v.model, v.image FROM bookings b JOIN vehicles v ON b.vehicle_id = v.id WHERE b.booking_no = ?");
$stmt->execute([$booking_no]);
$booking = $stmt->fetch();

if(!$booking) {
    redirect('dashboard.php');
}
?>

<style>
    .confirmation-section {
        background: #f8f9fa;
        min-height: calc(100vh - 200px);
    }
    
    .confirmation-card {
        background: white;
        border-radius: 30px;
        padding: 50px;
        box-shadow: 0 20px 50px rgba(0,0,0,0.1);
        max-width: 700px;
        margin: 0 auto;
    }
    
    .success-icon {
        animation: scaleIn 0.5s ease;
    }
    
    @keyframes scaleIn {
        0% { transform: scale(0); opacity: 0; }
        100% { transform: scale(1); opacity: 1; }
    }
    
    .detail-box {
        background: #f8f9fa;
        border-radius: 20px;
        padding: 25px;
        text-align: left;
        margin-top: 20px;
    }
    
    .detail-box h4 {
        margin-bottom: 20px;
        padding-bottom: 15px;
        border-bottom: 2px solid #e9ecef;
    }
    
    @media (max-width: 768px) {
        .confirmation-card {
            padding: 30px 20px;
        }
    }
</style>

<section class="confirmation-section py-5">
    <div class="container">
        <div class="confirmation-card text-center" data-aos="zoom-in">
            <div class="success-icon">
                <i class="fas fa-check-circle fa-5x text-success"></i>
            </div>
            <h1 class="display-5 fw-bold mt-3">Booking Confirmed!</h1>
            <p class="lead">Your booking has been successfully confirmed. A confirmation email has been sent to your email address.</p>
            
            <div class="booking-details mt-4">
                <div class="row justify-content-center">
                    <div class="col-md-12">
                        <div class="detail-box">
                            <h4><i class="fas fa-ticket-alt text-danger me-2"></i> Booking Number: <span class="text-danger"><?php echo $booking['booking_no']; ?></span></h4>
                            <div class="row mt-4">
                                <div class="col-6">
                                    <p><strong><i class="fas fa-car text-danger me-2"></i> Vehicle:</strong></p>
                                    <p><?php echo $booking['brand'] . ' ' . $booking['model']; ?></p>
                                </div>
                                <div class="col-6">
                                    <p><strong><i class="fas fa-user text-danger me-2"></i> Customer:</strong></p>
                                    <p><?php echo $booking['customer_name']; ?></p>
                                </div>
                                <div class="col-6">
                                    <p><strong><i class="fas fa-envelope text-danger me-2"></i> Email:</strong></p>
                                    <p><?php echo $booking['customer_email']; ?></p>
                                </div>
                                <div class="col-6">
                                    <p><strong><i class="fas fa-phone text-danger me-2"></i> Phone:</strong></p>
                                    <p><?php echo $booking['customer_phone']; ?></p>
                                </div>
                                <div class="col-6">
                                    <p><strong><i class="fas fa-calendar-alt text-danger me-2"></i> Pickup Date:</strong></p>
                                    <p><?php echo date('F d, Y', strtotime($booking['pickup_date'])); ?></p>
                                </div>
                                <div class="col-6">
                                    <p><strong><i class="fas fa-calendar-check text-danger me-2"></i> Return Date:</strong></p>
                                    <p><?php echo date('F d, Y', strtotime($booking['return_date'])); ?></p>
                                </div>
                                <div class="col-12">
                                    <hr>
                                    <p class="text-end"><strong>Total Amount:</strong> <span class="text-danger fs-4">$<?php echo number_format($booking['total_price'], 2); ?></span></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="mt-4 d-flex gap-3 justify-content-center flex-wrap">
                <a href="dashboard.php" class="btn btn-danger px-4 py-2 rounded-pill">
                    <i class="fas fa-tachometer-alt me-2"></i> Go to Dashboard
                </a>
                <a href="cars.php" class="btn btn-outline-danger px-4 py-2 rounded-pill">
                    <i class="fas fa-car me-2"></i> Browse More Cars
                </a>
                <button onclick="window.print()" class="btn btn-outline-secondary px-4 py-2 rounded-pill">
                    <i class="fas fa-print me-2"></i> Print Details
                </button>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>