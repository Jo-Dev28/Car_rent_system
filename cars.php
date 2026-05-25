<?php
// cars.php
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

// Get filter parameters
$search = $_GET['search'] ?? '';
$transmission = $_GET['transmission'] ?? '';
$fuel = $_GET['fuel'] ?? '';
$min_price = $_GET['min_price'] ?? 0;
$max_price = $_GET['max_price'] ?? 1000;

// Build query
$query = "SELECT * FROM vehicles WHERE availability = 1";
$params = [];

if($search) {
    $query .= " AND (brand LIKE ? OR model LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}
if($transmission) {
    $query .= " AND transmission = ?";
    $params[] = $transmission;
}
if($fuel) {
    $query .= " AND fuel_type = ?";
    $params[] = $fuel;
}
if($min_price) {
    $query .= " AND rental_price >= ?";
    $params[] = $min_price;
}
if($max_price && $max_price < 1000) {
    $query .= " AND rental_price <= ?";
    $params[] = $max_price;
}

$query .= " ORDER BY id DESC";
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$vehicles = $stmt->fetchAll();
?>

<style>
    .page-header {
        background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
        color: white;
        padding: 80px 0 60px;
        text-align: center;
        margin-top: -85px;
        position: relative;
        overflow: hidden;
    }
    
    .page-header::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320"><path fill="rgba(220,53,69,0.1)" fill-opacity="1" d="M0,96L48,112C96,128,192,160,288,160C384,160,480,128,576,122.7C672,117,768,139,864,154.7C960,171,1056,181,1152,165.3C1248,149,1344,107,1392,85.3L1440,64L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path></svg>') no-repeat bottom;
        background-size: cover;
        opacity: 0.3;
        pointer-events: none;
    }
    
    .page-header h1 {
        font-family: 'Orbitron', monospace;
        font-size: 3rem;
        font-weight: 800;
        margin-bottom: 15px;
        position: relative;
        z-index: 1;
    }
    
    .page-header .lead {
        font-size: 1.2rem;
        opacity: 0.9;
        position: relative;
        z-index: 1;
    }
    
    .filter-sidebar {
        background: white;
        border-radius: 20px;
        padding: 25px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.05);
        position: sticky;
        top: 100px;
    }
    
    .filter-sidebar h4 {
        font-weight: 700;
        margin-bottom: 20px;
        padding-bottom: 15px;
        border-bottom: 2px solid #f0f0f0;
    }
    
    .filter-sidebar .form-label {
        font-weight: 600;
        margin-bottom: 8px;
        color: #333;
    }
    
    .filter-sidebar .form-control,
    .filter-sidebar .form-select {
        border-radius: 12px;
        padding: 10px 15px;
        border: 1px solid #e9ecef;
    }
    
    .filter-sidebar .form-control:focus,
    .filter-sidebar .form-select:focus {
        border-color: #dc3545;
        box-shadow: 0 0 0 3px rgba(220,53,69,0.1);
    }
    
    .car-card {
        background: white;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(0,0,0,0.08);
        transition: all 0.3s ease;
        height: 100%;
    }
    
    .car-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 20px 40px rgba(0,0,0,0.15);
    }
    
    .car-image {
        position: relative;
        height: 200px;
        overflow: hidden;
    }
    
    .car-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s ease;
    }
    
    .car-card:hover .car-image img {
        transform: scale(1.1);
    }
    
    .car-badge {
        position: absolute;
        top: 15px;
        left: 15px;
        background: #28a745;
        color: white;
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
    }
    
    .car-details {
        padding: 20px;
    }
    
    .car-details h4 {
        font-size: 1.1rem;
        font-weight: 700;
        margin-bottom: 10px;
    }
    
    .car-specs {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        font-size: 0.75rem;
        color: #6c757d;
        margin-bottom: 15px;
    }
    
    .car-specs i {
        color: #dc3545;
        width: 14px;
        margin-right: 4px;
    }
    
    .car-price {
        font-size: 1rem;
        font-weight: 400;
        color: #fff;
        margin-bottom: 15px;
    }
    
    .car-price span {
        font-size: 0.8rem;
        font-weight: normal;
        color: #abb1b6;
    }
    
    @media (max-width: 768px) {
        .page-header {
            margin-top: -70px;
            padding: 100px 0 50px;
        }
        
        .page-header h1 {
            font-size: 2rem;
        }
        
        .filter-sidebar {
            margin-bottom: 30px;
            position: relative;
            top: 0;
        }
    }
</style>

<section class="page-header">
    <div class="container">
        <h1 class="display-4 fw-bold">Our Vehicle Fleet</h1>
        <p class="lead">Choose from our wide selection of premium cars</p>
    </div>
</section>

<section class="cars-page py-5">
    <div class="container">
        <div class="row">
            <!-- Filters Sidebar -->
            <div class="col-lg-3" data-aos="fade-right">
                <div class="filter-sidebar">
                    <h4><i class="fas fa-filter text-danger"></i> Filters</h4>
                    <form method="GET" action="">
                        <div class="mb-3">
                            <label class="form-label"><i class="fas fa-search text-danger me-1"></i> Search</label>
                            <input type="text" name="search" class="form-control" placeholder="Search by brand or model" value="<?php echo htmlspecialchars($search); ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label"><i class="fas fa-cogs text-danger me-1"></i> Transmission</label>
                            <select name="transmission" class="form-select">
                                <option value="">All</option>
                                <option value="Automatic" <?php echo $transmission == 'Automatic' ? 'selected' : ''; ?>>Automatic</option>
                                <option value="Manual" <?php echo $transmission == 'Manual' ? 'selected' : ''; ?>>Manual</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label"><i class="fas fa-gas-pump text-danger me-1"></i> Fuel Type</label>
                            <select name="fuel" class="form-select">
                                <option value="">All</option>
                                <option value="Petrol" <?php echo $fuel == 'Petrol' ? 'selected' : ''; ?>>Petrol</option>
                                <option value="Diesel" <?php echo $fuel == 'Diesel' ? 'selected' : ''; ?>>Diesel</option>
                                <option value="Electric" <?php echo $fuel == 'Electric' ? 'selected' : ''; ?>>Electric</option>
                                <option value="Hybrid" <?php echo $fuel == 'Hybrid' ? 'selected' : ''; ?>>Hybrid</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label"><i class="fas fa-dollar-sign text-danger me-1"></i> Price Range</label>
                            <div class="row g-2">
                                <div class="col-6">
                                    <input type="number" name="min_price" class="form-control" placeholder="Min" value="<?php echo $min_price; ?>">
                                </div>
                                <div class="col-6">
                                    <input type="number" name="max_price" class="form-control" placeholder="Max" value="<?php echo $max_price < 1000 ? $max_price : ''; ?>">
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-danger w-100 mb-2" style="border-radius: 50px;">Apply Filters</button>
                        <a href="cars.php" class="btn btn-outline-secondary w-100" style="border-radius: 50px;">Reset</a>
                    </form>
                </div>
            </div>
            
            <!-- Vehicles Grid -->
            <div class="col-lg-9">
                <?php if(count($vehicles) > 0): ?>
                    <div class="row g-4">
                        <?php foreach($vehicles as $car): ?>
                        <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="<?php echo $car['id'] * 100; ?>">
                            <div class="car-card">
                                <div class="car-image">
                                    <img src="<?php echo getUserImagePath($car['image']); ?>" alt="<?php echo htmlspecialchars($car['brand'] . ' ' . $car['model']); ?>">
                                    <div class="car-badge">Available</div>
                                </div>
                                <div class="car-details">
                                    <h4><?php echo htmlspecialchars($car['brand'] . ' ' . $car['model']); ?></h4>
                                    <div class="car-specs">
                                        <span><i class="fas fa-calendar"></i> <?php echo $car['year']; ?></span>
                                        <span><i class="fas fa-cogs"></i> <?php echo htmlspecialchars($car['transmission']); ?></span>
                                        <span><i class="fas fa-gas-pump"></i> <?php echo htmlspecialchars($car['fuel_type']); ?></span>
                                    </div>
                                    <div class="car-price">$<?php echo number_format($car['rental_price'], 2); ?> <span>/ day</span></div>
                                    <a href="car-details.php?id=<?php echo $car['id']; ?>" class="btn btn-danger w-100" style="border-radius: 50px;">View & Book</a>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5">
                        <i class="fas fa-car-side fa-4x text-muted mb-3"></i>
                        <h3>No vehicles found</h3>
                        <p class="text-muted">Try adjusting your filters to find your perfect car</p>
                        <a href="cars.php" class="btn btn-danger mt-3" style="border-radius: 50px;">Clear Filters</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>