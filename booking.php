<?php
// booking.php
ob_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

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
    $_SESSION['booking_redirect'] = true;
    redirect('login.php');
}

$vehicle_id = $_POST['vehicle_id'] ?? $_GET['vehicle_id'] ?? 0;
$pickup_date = $_POST['pickup_date'] ?? $_GET['pickup'] ?? '';
$return_date = $_POST['return_date'] ?? $_GET['return'] ?? '';

// Get vehicle details
$stmt = $pdo->prepare("SELECT * FROM vehicles WHERE id = ?");
$stmt->execute([$vehicle_id]);
$vehicle = $stmt->fetch();

if(!$vehicle) {
    redirect('cars.php');
}

// Calculate total price
$days = 1;
if($pickup_date && $return_date) {
    $pickup = new DateTime($pickup_date);
    $return = new DateTime($return_date);
    $days = $pickup->diff($return)->days;
    $days = $days > 0 ? $days : 1;
}
$total_price = $vehicle['rental_price'] * $days;

// Process booking
$error = '';
$success = '';

if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['confirm_booking'])) {
    $booking_no = generateBookingNo();
    $customer_name = sanitize($_POST['customer_name']);
    $customer_email = sanitize($_POST['customer_email']);
    $customer_phone = sanitize($_POST['customer_phone']);
    
    // Debug: Check if all values are present
    error_log("Booking No: " . $booking_no);
    error_log("User ID: " . $_SESSION['user_id']);
    error_log("Vehicle ID: " . $vehicle_id);
    error_log("Pickup Date: " . $pickup_date);
    error_log("Return Date: " . $return_date);
    error_log("Days: " . $days);
    error_log("Total Price: " . $total_price);
    
    try {
        $sql = "INSERT INTO bookings (booking_no, user_id, vehicle_id, pickup_date, return_date, total_days, total_price, customer_name, customer_email, customer_phone, status) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'confirmed')";
        
        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute([
            $booking_no, 
            $_SESSION['user_id'], 
            $vehicle_id, 
            $pickup_date, 
            $return_date, 
            $days, 
            $total_price, 
            $customer_name, 
            $customer_email, 
            $customer_phone
        ]);
        
        if($result) {
            $_SESSION['booking_success'] = $booking_no;
            redirect('booking-confirmation.php?booking=' . $booking_no);
        } else {
            $error = "Failed to create booking. Please try again.";
            error_log("Booking insert failed: " . print_r($stmt->errorInfo(), true));
        }
    } catch(PDOException $e) {
        $error = "Database error: " . $e->getMessage();
        error_log("PDO Exception: " . $e->getMessage());
    }
}

// Get user data for prefill
$user_stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$user_stmt->execute([$_SESSION['user_id']]);
$user = $user_stmt->fetch();
?>

<style>
    .booking-section {
        background: #f8f9fa;
        min-height: calc(100vh - 200px);
        padding: 60px 0;
    }
    
    .booking-form-card {
        background: white;
        border-radius: 20px;
        padding: 35px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.08);
    }
    
    .booking-form-card h2 {
        font-weight: 700;
        color: #1a1a1a;
        position: relative;
        padding-bottom: 15px;
        margin-bottom: 25px;
    }
    
    .booking-form-card h2:after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 60px;
        height: 3px;
        background: #dc3545;
    }
    
    .booking-summary {
        background: white;
        border-radius: 20px;
        padding: 35px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.08);
        position: sticky;
        top: 100px;
    }
    
    .booking-summary h3 {
        font-weight: 700;
        margin-bottom: 20px;
        padding-bottom: 15px;
        border-bottom: 2px solid #f0f0f0;
    }
    
    .summary-car {
        text-align: center;
        margin-bottom: 25px;
    }
    
    .summary-car img {
        width: 100%;
        height: 180px;
        object-fit: cover;
        border-radius: 15px;
        margin-bottom: 15px;
    }
    
    .summary-car h4 {
        font-weight: 700;
        margin: 0;
    }
    
    .summary-details {
        border-top: 1px solid #f0f0f0;
        padding-top: 20px;
    }
    
    .summary-item {
        display: flex;
        justify-content: space-between;
        padding: 12px 0;
        border-bottom: 1px solid #f0f0f0;
    }
    
    .summary-item.total {
        border-bottom: none;
        padding-top: 15px;
        font-size: 1.2rem;
    }
    
    .form-label {
        font-weight: 600;
        margin-bottom: 8px;
        color: #333;
    }
    
    .form-control {
        border-radius: 12px;
        padding: 12px 15px;
        border: 1px solid #e9ecef;
    }
    
    .form-control:focus {
        border-color: #dc3545;
        box-shadow: 0 0 0 3px rgba(220,53,69,0.1);
    }
    
    .form-control[disabled] {
        background: #f8f9fa;
        color: #333;
        font-weight: 500;
    }
    
    .btn-confirm {
        background: linear-gradient(135deg, #dc3545, #c82333);
        border: none;
        border-radius: 12px;
        padding: 14px;
        font-weight: 600;
        font-size: 1.1rem;
        transition: all 0.3s;
    }
    
    .btn-confirm:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 20px rgba(220,53,69,0.4);
    }
    
    .form-check-input:checked {
        background-color: #dc3545;
        border-color: #dc3545;
    }
    
    .alert {
        border-radius: 12px;
        margin-bottom: 20px;
    }
    
    @media (max-width: 768px) {
        .booking-form-card {
            padding: 25px;
            margin-bottom: 20px;
        }
        
        .booking-summary {
            position: relative;
            top: 0;
        }
    }
</style>

<section class="booking-section">
    <div class="container">
        <div class="row">
            <div class="col-lg-7" data-aos="fade-right">
                <div class="booking-form-card">
                    <h2>Complete Your Booking</h2>
                    
                    <?php if($error): ?>
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-circle me-2"></i> <?php echo $error; ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if($success): ?>
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle me-2"></i> <?php echo $success; ?>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" action="">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label"><i class="fas fa-user text-danger me-2"></i> Full Name *</label>
                                <input type="text" name="customer_name" class="form-control" value="<?php echo htmlspecialchars($user['full_name'] ?? ''); ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label"><i class="fas fa-envelope text-danger me-2"></i> Email *</label>
                                <input type="email" name="customer_email" class="form-control" value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label"><i class="fas fa-phone text-danger me-2"></i> Phone Number *</label>
                                <input type="tel" name="customer_phone" class="form-control" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label"><i class="fas fa-calendar-alt text-danger me-2"></i> Pickup Date</label>
                                <input type="text" class="form-control" value="<?php echo $pickup_date ? date('F d, Y', strtotime($pickup_date)) : 'Not selected'; ?>" disabled>
                                <input type="hidden" name="pickup_date" value="<?php echo $pickup_date; ?>">
                                <input type="hidden" name="vehicle_id" value="<?php echo $vehicle_id; ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label"><i class="fas fa-calendar-check text-danger me-2"></i> Return Date</label>
                                <input type="text" class="form-control" value="<?php echo $return_date ? date('F d, Y', strtotime($return_date)) : 'Not selected'; ?>" disabled>
                                <input type="hidden" name="return_date" value="<?php echo $return_date; ?>">
                            </div>
                            <div class="col-12">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="termsCheckbox" required>
                                    <label class="form-check-label" for="termsCheckbox">
                                        I agree to the <a href="#" class="text-danger">terms and conditions</a>
                                    </label>
                                </div>
                            </div>
                            <div class="col-12">
                                <button type="submit" name="confirm_booking" class="btn btn-danger btn-confirm w-100">
                                    Confirm Booking <i class="fas fa-check-circle ms-2"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="col-lg-5" data-aos="fade-left">
                <div class="booking-summary">
                    <h3><i class="fas fa-receipt text-danger me-2"></i> Booking Summary</h3>
                    <div class="summary-car">
                        <img src="<?php echo getUserImagePath($vehicle['image']); ?>" alt="<?php echo htmlspecialchars($vehicle['model']); ?>" class="img-fluid rounded">
                        <h4 class="mt-2"><?php echo htmlspecialchars($vehicle['brand'] . ' ' . $vehicle['model']); ?></h4>
                    </div>
                    <div class="summary-details">
                        <div class="summary-item">
                            <span><i class="fas fa-tag text-danger me-2"></i> Daily Rate:</span>
                            <span class="fw-bold">$<?php echo number_format($vehicle['rental_price'], 2); ?></span>
                        </div>
                        <div class="summary-item">
                            <span><i class="fas fa-calendar-week text-danger me-2"></i> Number of Days:</span>
                            <span class="fw-bold"><?php echo $days; ?> day(s)</span>
                        </div>
                        <div class="summary-item">
                            <span><i class="fas fa-gas-pump text-danger me-2"></i> Fuel Policy:</span>
                            <span>Full to Full</span>
                        </div>
                        <div class="summary-item">
                            <span><i class="fas fa-shield-alt text-danger me-2"></i> Insurance:</span>
                            <span>Included</span>
                        </div>
                        <div class="summary-item total">
                            <span><strong>Total Amount:</strong></span>
                            <span class="text-danger fw-bold fs-3">$<?php echo number_format($total_price, 2); ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
// Terms checkbox validation
document.querySelector('form').addEventListener('submit', function(e) {
    const checkbox = document.getElementById('termsCheckbox');
    if(!checkbox.checked) {
        e.preventDefault();
        alert('Please agree to the terms and conditions to continue.');
    }
});
</script>

<?php include 'includes/footer.php'; ?>