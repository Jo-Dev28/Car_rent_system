<?php
// car-details.php
include 'includes/config.php';
include 'includes/header.php';

// Function to get correct image path for user side
function getUserImagePath($image) {
    if(empty($image)) {
        return 'assets/images/default-car.jpg';
    }
    // Remove ../ prefix if exists (for admin uploaded images)
    $image = str_replace('../', '', $image);
    
    // If image is just a filename, assume it's in uploads folder
    if(strpos($image, '/') === false) {
        return 'uploads/' . $image;
    }
    
    return $image;
}

$car_id = $_GET['id'] ?? 0;
$stmt = $pdo->prepare("SELECT * FROM vehicles WHERE id = ?");
$stmt->execute([$car_id]);
$car = $stmt->fetch();

if(!$car) {
    redirect('cars.php');
}
?>

<style>
    .car-details-section {
        background: #f8f9fa;
        min-height: calc(100vh - 200px);
    }
    
    .car-gallery {
        background: white;
        border-radius: 20px;
        padding: 20px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.08);
    }
    
    .car-gallery img {
        width: 100%;
        height: 400px;
        object-fit: cover;
        border-radius: 15px;
    }
    
    .car-info {
        background: white;
        border-radius: 20px;
        padding: 30px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.08);
    }
    
    .car-rating {
        margin: 15px 0;
        color: #ffc107;
    }
    
    .car-rating span {
        color: #6c757d;
        margin-left: 10px;
    }
    
    .car-price-detail {
        margin: 20px 0;
        padding: 15px 0;
        border-top: 1px solid #e9ecef;
        border-bottom: 1px solid #e9ecef;
    }
    
    .car-price-detail h2 {
        font-size: 2rem;
        font-weight: 700;
    }
    
    .car-price-detail small {
        font-size: 1rem;
        font-weight: normal;
    }
    
    .car-specs-detail {
        margin: 20px 0;
    }
    
    .spec-item {
        background: #f8f9fa;
        padding: 12px 15px;
        border-radius: 12px;
        font-weight: 500;
    }
    
    .spec-item i {
        color: #dc3545;
        width: 25px;
        margin-right: 10px;
    }
    
    .car-description {
        margin: 20px 0;
        padding: 15px 0;
        border-top: 1px solid #e9ecef;
    }
    
    .car-description h4 {
        font-weight: 700;
        margin-bottom: 15px;
    }
    
    .car-description p {
        color: #6c757d;
        line-height: 1.6;
    }
    
    .booking-form {
        margin-top: 20px;
        padding-top: 20px;
        border-top: 1px solid #e9ecef;
    }
    
    .booking-form label {
        font-weight: 600;
        margin-bottom: 8px;
        color: #333;
    }
    
    .booking-form .form-control {
        border-radius: 12px;
        padding: 12px;
        border: 1px solid #e9ecef;
    }
    
    .booking-form .form-control:focus {
        border-color: #dc3545;
        box-shadow: 0 0 0 3px rgba(220,53,69,0.1);
    }
    
    .btn-book {
        background: linear-gradient(135deg, #dc3545, #c82333);
        border: none;
        border-radius: 12px;
        padding: 14px;
        font-weight: 600;
        font-size: 1.1rem;
        transition: all 0.3s;
    }
    
    .btn-book:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 20px rgba(220,53,69,0.4);
    }
    
    @media (max-width: 768px) {
        .car-gallery img {
            height: 250px;
        }
        
        .car-info {
            margin-top: 20px;
            padding: 20px;
        }
        
        .car-price-detail h2 {
            font-size: 1.5rem;
        }
    }
</style>

<section class="car-details-section py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-6" data-aos="fade-right">
                <div class="car-gallery">
                    <img src="<?php echo getUserImagePath($car['image']); ?>" alt="<?php echo htmlspecialchars($car['brand'] . ' ' . $car['model']); ?>" class="img-fluid rounded-4">
                </div>
            </div>
            <div class="col-lg-6" data-aos="fade-left">
                <div class="car-info">
                    <h1 class="display-4 fw-bold"><?php echo htmlspecialchars($car['brand'] . ' ' . $car['model']); ?></h1>
                    <div class="car-rating">
                        <i class="fas fa-star text-warning"></i>
                        <i class="fas fa-star text-warning"></i>
                        <i class="fas fa-star text-warning"></i>
                        <i class="fas fa-star text-warning"></i>
                        <i class="fas fa-star-half-alt text-warning"></i>
                        <span>(245 reviews)</span>
                    </div>
                    <div class="car-price-detail">
                        <h2 class="text-danger">$<?php echo number_format($car['rental_price'], 2); ?> <small class="text-muted">/ day</small></h2>
                    </div>
                    <div class="car-specs-detail">
                        <div class="row g-3">
                            <div class="col-6">
                                <div class="spec-item"><i class="fas fa-tachometer-alt"></i> Transmission: <?php echo htmlspecialchars($car['transmission']); ?></div>
                            </div>
                            <div class="col-6">
                                <div class="spec-item"><i class="fas fa-gas-pump"></i> Fuel: <?php echo htmlspecialchars($car['fuel_type']); ?></div>
                            </div>
                            <div class="col-6">
                                <div class="spec-item"><i class="fas fa-users"></i> Seats: <?php echo $car['seats']; ?> persons</div>
                            </div>
                            <div class="col-6">
                                <div class="spec-item"><i class="fas fa-calendar"></i> Year: <?php echo $car['year']; ?></div>
                            </div>
                        </div>
                    </div>
                    <div class="car-description">
                        <h4>Description</h4>
                        <p><?php echo nl2br(htmlspecialchars($car['description'])); ?></p>
                    </div>
                    
                    <?php if($car['availability']): ?>
                    <form action="booking.php" method="POST" class="booking-form">
                        <input type="hidden" name="vehicle_id" value="<?php echo $car['id']; ?>">
                        <div class="row g-3">
                            <div class="col-6">
                                <label><i class="fas fa-calendar-alt text-danger me-2"></i> Pickup Date</label>
                                <input type="date" name="pickup_date" class="form-control" required min="<?php echo date('Y-m-d'); ?>">
                            </div>
                            <div class="col-6">
                                <label><i class="fas fa-calendar-check text-danger me-2"></i> Return Date</label>
                                <input type="date" name="return_date" class="form-control" required min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>">
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-danger btn-book w-100">
                                    Proceed to Book <i class="fas fa-arrow-right ms-2"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                    <?php else: ?>
                    <div class="alert alert-warning mt-3">
                        <i class="fas fa-exclamation-triangle me-2"></i> 
                        This vehicle is currently unavailable for booking.
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
// Set minimum dates for pickup and return
document.addEventListener('DOMContentLoaded', function() {
    const pickupDate = document.querySelector('input[name="pickup_date"]');
    const returnDate = document.querySelector('input[name="return_date"]');
    
    if(pickupDate) {
        pickupDate.addEventListener('change', function() {
            if(returnDate.value && returnDate.value <= this.value) {
                returnDate.value = '';
            }
            returnDate.min = this.value;
            if(this.value) {
                const nextDay = new Date(this.value);
                nextDay.setDate(nextDay.getDate() + 1);
                returnDate.min = nextDay.toISOString().split('T')[0];
            }
        });
    }
});
</script>

<?php include 'includes/footer.php'; ?>