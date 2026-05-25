<?php
// index.php
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

// Get featured vehicles
$featuredVehicles = $pdo->query("SELECT * FROM vehicles WHERE availability = 1 ORDER BY id DESC LIMIT 4");
$vehicles = $featuredVehicles->fetchAll();
?>
<style>
    .bouncing-car-wrapper {
        position: relative;
        display: inline-block;
    }
    
    /* Bouncing animation for the car */
    .bouncing-car {
        animation: bounce 2s ease-in-out infinite;
        filter: drop-shadow(0 20px 30px rgba(0,0,0,0.3));
        max-height: 400px;
        width: 100%;
        object-fit: contain;
        position: relative;
        z-index: 2;
    }
    
    /* Shadow that moves with the car */
    .car-shadow {
        position: absolute;
        bottom: 0;
        left: 50%;
        transform: translateX(-50%);
        width: 80%;
        height: 80px;
        background: rgba(0,0,0,0.2);
        border-radius: 50%;
        animation: shadowScale 2s ease-in-out infinite;
        z-index: 1;
    }
    
    @keyframes bounce {
        0%, 100% {
            transform: translateY(0);
        }
        50% {
            transform: translateY(-20px);
        }
    }
    
    @keyframes shadowScale {
        0%, 100% {
            transform: translateX(-50%) scale(1);
            opacity: 0.2;
        }
        50% {
            transform: translateX(-50%) scale(0.8);
            opacity: 0.1;
        }
    }
    
    /* Optional: Add rotation for more dynamic effect */
    @keyframes bounceRotate {
        0%, 100% {
            transform: translateY(0) rotate(0deg);
        }
        25% {
            transform: translateY(-10px) rotate(-2deg);
        }
        75% {
            transform: translateY(-20px) rotate(2deg);
        }
    }
    
    /* Alternative: Bounce with rotation */
    .bouncing-car-rotate {
        animation: bounceRotate 2s ease-in-out infinite;
        filter: drop-shadow(0 20px 30px rgba(0,0,0,0.3));
        max-height: 400px;
        width: 100%;
        object-fit: contain;
    }
    
    /* Smooth bounce with different easing */
    @keyframes smoothBounce {
        0%, 100% {
            transform: translateY(0);
            animation-timing-function: cubic-bezier(0.8, 0, 1, 1);
        }
        50% {
            transform: translateY(-25px);
            animation-timing-function: cubic-bezier(0, 0, 0.2, 1);
        }
    }
    
    .bouncing-car-smooth {
        animation: smoothBounce 2.5s infinite;
        filter: drop-shadow(0 20px 30px rgba(0,0,0,0.3));
        max-height: 400px;
        width: 100%;
        object-fit: contain;
    }
</style>
<!-- Hero Section -->
<section class="hero-section" style="background: linear-gradient(135deg, #0a0a0a 0%, #1a1a1a 100%); min-height: 90vh; display: flex; align-items: center; position: relative; overflow: hidden; width: 100%;">
    <div class="container" style="position: relative; z-index: 2;">
        <div class="row align-items-center">
            <div class="col-lg-6 text-white" data-aos="fade-right">
                <span class="badge bg-danger mb-3 px-3 py-2" style="border-radius: 30px;">Premium Car Rental</span>
                <h1 class="display-3 fw-bold mb-3">Drive Your <span class="text-danger">Dream Car</span> Today</h1>
                <p class="lead mb-4">Experience luxury, performance, and style with our exclusive fleet of premium vehicles.</p>
                <div class="hero-buttons">
                    <a href="cars.php" class="btn btn-danger btn-lg me-3 px-4" style="border-radius: 50px;">Browse Cars <i class="fas fa-arrow-right ms-2"></i></a>
                    <a href="#booking-form" class="btn btn-outline-light btn-lg px-4" style="border-radius: 50px;">Book Now</a>
                </div>
            </div>
            <div class="col-lg-6 text-center" data-aos="fade-left" data-aos-delay="200">
                <div class="bouncing-car-wrapper">
                    <img src="1.png" alt="Luxury Sports Car" class="img-fluid bouncing-car">
                    <div class="car-shadow"></div>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- Booking Form Section -->
<section id="booking-form" class="booking-section" style="margin-top: -50px; position: relative; z-index: 10;">
    <div class="container">
        <div class="card border-0 shadow-lg rounded-4" style="background: white; border-radius: 20px; overflow: hidden;">
            <div class="card-body p-4">
                <div class="row align-items-center">
                    <div class="col-md-5">
                        <h3 class="mb-2 fw-bold">Book Your <span class="text-danger">Dream Car</span></h3>
                        <p class="text-muted mb-0">Fill in your details and we'll find the perfect car for you</p>
                    </div>
                    <div class="col-md-7">
                        <form action="cars.php" method="GET" class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold"><i class="fas fa-map-marker-alt text-danger"></i> Pickup Location</label>
                                <select name="location" class="form-select" required>
                                    <option value="">Select Location</option>
                                    <option>Jomo Kenyatta Airport</option>
                                    <option>City Center Branch</option>
                                    <option>Westlands Branch</option>
                                    <option>Upper Hill Branch</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold"><i class="fas fa-calendar-alt text-danger"></i> Pickup Date</label>
                                <input type="date" name="pickup_date" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold"><i class="fas fa-calendar-check text-danger"></i> Return Date</label>
                                <input type="date" name="return_date" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">&nbsp;</label>
                                <button type="submit" class="btn btn-danger w-100" style="border-radius: 50px;">Find Available Cars <i class="fas fa-search ms-2"></i></button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Featured Vehicles -->
<section class="vehicles-section py-5">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <span class="badge bg-danger px-3 py-2" style="border-radius: 30px;">Our Fleet</span>
            <h2 class="display-4 fw-bold mt-3">Premium <span class="text-danger">Vehicles</span></h2>
            <p class="text-muted">Choose from our exclusive collection of luxury and sports cars</p>
        </div>
        
        <?php if(count($vehicles) > 0): ?>
        <div class="row g-4">
            <?php foreach($vehicles as $car): ?>
            <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="<?php echo $car['id'] * 100; ?>">
                <div class="card border-0 shadow-lg rounded-4 h-100" style="transition: transform 0.3s; overflow: hidden;">
                    <div class="position-relative" style="height: 220px; overflow: hidden;">
                        <img src="<?php echo getUserImagePath($car['image']); ?>" alt="<?php echo htmlspecialchars($car['brand'] . ' ' . $car['model']); ?>" style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.5s;">
                        <div class="position-absolute top-0 end-0 m-3">
                            <span class="badge bg-danger px-3 py-2 rounded-pill">$<?php echo number_format($car['rental_price'], 2); ?><small class="fw-normal">/day</small></span>
                        </div>
                    </div>
                    <div class="card-body p-3">
                        <h5 class="card-title fw-bold mb-2"><?php echo htmlspecialchars($car['brand'] . ' ' . $car['model']); ?></h5>
                        <div class="d-flex justify-content-between mb-3">
                            <small class="text-muted"><i class="fas fa-cogs text-danger"></i> <?php echo htmlspecialchars($car['transmission']); ?></small>
                            <small class="text-muted"><i class="fas fa-gas-pump text-danger"></i> <?php echo htmlspecialchars($car['fuel_type']); ?></small>
                            <small class="text-muted"><i class="fas fa-users text-danger"></i> <?php echo $car['seats']; ?> Seats</small>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="car-details.php?id=<?php echo $car['id']; ?>" class="btn btn-outline-danger flex-grow-1" style="border-radius: 50px;">View</a>
                            <button class="btn btn-danger quick-book" data-id="<?php echo $car['id']; ?>" style="border-radius: 50px;">Book</button>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="text-center py-5">
            <i class="fas fa-car-side fa-4x text-muted mb-3"></i>
            <h4>No vehicles available</h4>
            <p>Please check back later for our premium fleet.</p>
        </div>
        <?php endif; ?>
        
        <div class="text-center mt-5">
            <a href="cars.php" class="btn btn-danger btn-lg px-5" style="border-radius: 50px;">View All Vehicles <i class="fas fa-arrow-right ms-2"></i></a>
        </div>
    </div>
</section>

<!-- How It Works -->
<section id="how-it-works" class="py-5" style="background: linear-gradient(135deg, #0a0a0a 0%, #1a1a1a 100%); color: white;">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <span class="badge bg-danger px-3 py-2" style="border-radius: 30px;">Simple Process</span>
            <h2 class="display-4 fw-bold mt-3">Quick & <span class="text-danger">Easy</span> Rental Steps</h2>
            <p class="text-white-50">Get your dream car in just 3 simple steps</p>
        </div>
        <div class="row g-4">
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
                <div class="text-center p-4 rounded-4" style="background: rgba(255,255,255,0.05); transition: transform 0.3s;">
                    <div class="bg-danger rounded-circle d-inline-flex p-3 mb-3" style="width: 70px; height: 70px;">
                        <i class="fas fa-search fa-2x m-auto"></i>
                    </div>
                    <h4 class="mb-3">Choose Vehicle</h4>
                    <p class="text-white-50">Browse our extensive collection of premium vehicles</p>
                </div>
            </div>
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
                <div class="text-center p-4 rounded-4" style="background: rgba(255,255,255,0.05); transition: transform 0.3s;">
                    <div class="bg-danger rounded-circle d-inline-flex p-3 mb-3" style="width: 70px; height: 70px;">
                        <i class="fas fa-calendar-check fa-2x m-auto"></i>
                    </div>
                    <h4 class="mb-3">Book & Confirm</h4>
                    <p class="text-white-50">Select dates and confirm your booking instantly</p>
                </div>
            </div>
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="300">
                <div class="text-center p-4 rounded-4" style="background: rgba(255,255,255,0.05); transition: transform 0.3s;">
                    <div class="bg-danger rounded-circle d-inline-flex p-3 mb-3" style="width: 70px; height: 70px;">
                        <i class="fas fa-key fa-2x m-auto"></i>
                    </div>
                    <h4 class="mb-3">Pickup & Drive</h4>
                    <p class="text-white-50">Pick up your car and enjoy your drive</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Statistics Counters -->
<section class="py-5" style="background: #f8f9fa;">
    <div class="container">
        <div class="row g-4 text-center">
            <div class="col-md-3" data-aos="zoom-in">
                <div class="card border-0 shadow-lg rounded-4 p-4">
                    <i class="fas fa-smile fa-3x text-danger mb-3"></i>
                    <h3 class="fw-bold display-5">2500+</h3>
                    <p class="text-muted mb-0">Happy Customers</p>
                </div>
            </div>
            <div class="col-md-3" data-aos="zoom-in" data-aos-delay="100">
                <div class="card border-0 shadow-lg rounded-4 p-4">
                    <i class="fas fa-car fa-3x text-danger mb-3"></i>
                    <h3 class="fw-bold display-5">45+</h3>
                    <p class="text-muted mb-0">Luxury Cars</p>
                </div>
            </div>
            <div class="col-md-3" data-aos="zoom-in" data-aos-delay="200">
                <div class="card border-0 shadow-lg rounded-4 p-4">
                    <i class="fas fa-road fa-3x text-danger mb-3"></i>
                    <h3 class="fw-bold display-5">15000+</h3>
                    <p class="text-muted mb-0">Trips Completed</p>
                </div>
            </div>
            <div class="col-md-3" data-aos="zoom-in" data-aos-delay="300">
                <div class="card border-0 shadow-lg rounded-4 p-4">
                    <i class="fas fa-headset fa-3x text-danger mb-3"></i>
                    <h3 class="fw-bold display-5">24/7</h3>
                    <p class="text-muted mb-0">Support</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Testimonials -->
<section id="testimonials" class="py-5">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <span class="badge bg-danger px-3 py-2" style="border-radius: 30px;">Testimonials</span>
            <h2 class="display-4 fw-bold mt-3">What Our <span class="text-danger">Clients Say</span></h2>
            <p class="text-muted">Read reviews from our satisfied customers</p>
        </div>
        <div class="row g-4">
            <?php
            $testimonials = $pdo->query("SELECT * FROM testimonials WHERE is_approved = 1 ORDER BY id DESC LIMIT 3");
            $testimonialList = $testimonials->fetchAll();
            if(count($testimonialList) > 0):
                foreach($testimonialList as $t):
            ?>
            <div class="col-md-4" data-aos="fade-up">
                <div class="card border-0 shadow-lg rounded-4 p-4 h-100">
                    <i class="fas fa-quote-left fa-2x text-danger opacity-50 mb-3"></i>
                    <p class="mb-3">"<?php echo htmlspecialchars($t['comment']); ?>"</p>
                    <div class="d-flex align-items-center mt-auto">
                        <img src="<?php echo $t['user_image'] ?? 'https://randomuser.me/api/portraits/men/1.jpg'; ?>" class="rounded-circle me-3" style="width: 50px; height: 50px; object-fit: cover;">
                        <div>
                            <h6 class="mb-0"><?php echo htmlspecialchars($t['user_name']); ?></h6>
                            <small class="text-muted">Verified Customer</small>
                        </div>
                    </div>
                </div>
            </div>
            <?php 
                endforeach;
            else:
            ?>
            <div class="col-12 text-center py-5">
                <p class="text-muted">No testimonials yet. Be the first to leave a review!</p>
            </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- FAQ Section -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <span class="badge bg-danger px-3 py-2" style="border-radius: 30px;">FAQ</span>
            <h2 class="display-4 fw-bold mt-3">Frequently Asked <span class="text-danger">Questions</span></h2>
        </div>
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="accordion" id="faqAccordion">
                    <div class="accordion-item border-0 shadow-sm mb-3 rounded-3 overflow-hidden">
                        <h2 class="accordion-header">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                                What documents do I need to rent a car?
                            </button>
                        </h2>
                        <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">You need a valid driver's license, credit card, and passport or national ID for identification.</div>
                        </div>
                    </div>
                    <div class="accordion-item border-0 shadow-sm mb-3 rounded-3 overflow-hidden">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                                Is there a mileage limit?
                            </button>
                        </h2>
                        <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">No, all our rentals come with unlimited mileage. Drive as much as you want!</div>
                        </div>
                    </div>
                    <div class="accordion-item border-0 shadow-sm mb-3 rounded-3 overflow-hidden">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                                Can I cancel my booking?
                            </button>
                        </h2>
                        <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">Yes, free cancellation up to 24 hours before pickup. After that, cancellation fees may apply.</div>
                        </div>
                    </div>
                    <div class="accordion-item border-0 shadow-sm rounded-3 overflow-hidden">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq4">
                                Is insurance included?
                            </button>
                        </h2>
                        <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">Basic insurance is included. Additional coverage options are available at checkout.</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Mobile App Promotion -->
<section class="py-5" style="background: linear-gradient(135deg, #0a0a0a 0%, #1a1a1a 100%); color: white;">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6" data-aos="fade-right">
                <span class="badge bg-danger mb-3 px-3 py-2" style="border-radius: 30px;">Mobile App</span>
                <h2 class="display-4 fw-bold">Download Our <span class="text-danger">Mobile App</span></h2>
                <p class="lead mb-4">Book cars, track rentals, and get exclusive deals right from your phone</p>
                <div class="d-flex gap-3">
                    <a href="#" class="btn btn-dark px-4 py-3 rounded-pill"><i class="fab fa-apple fa-lg me-2"></i> App Store</a>
                    <a href="#" class="btn btn-dark px-4 py-3 rounded-pill"><i class="fab fa-google-play fa-lg me-2"></i> Google Play</a>
                </div>
            </div>
            <div class="col-lg-6 text-center" data-aos="fade-left">
                <img src="2.png" alt="Mobile App" class="img-fluid" style="max-width: 280px;">
            </div>
        </div>
    </div>
</section>

<script>
// Quick Book Buttons
document.querySelectorAll('.quick-book').forEach(btn => {
    btn.addEventListener('click', () => {
        const carId = btn.dataset.id;
        window.location.href = `booking.php?vehicle_id=${carId}`;
    });
});
</script>

<?php include 'includes/footer.php'; ?>