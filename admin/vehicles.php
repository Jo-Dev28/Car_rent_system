<?php
// admin/vehicles.php
require_once '../includes/config.php';

if(!isset($_SESSION['user_role']) || $_SESSION['user_role'] != 'admin') {
    redirect('../login.php');
}

// Handle delete
if(isset($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM vehicles WHERE id = ?");
    $stmt->execute([$_GET['delete']]);
    $_SESSION['success'] = "Vehicle deleted successfully";
    redirect('vehicles.php');
}

// Handle add/edit
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $brand = sanitize($_POST['brand']);
    $model = sanitize($_POST['model']);
    $year = $_POST['year'];
    $transmission = $_POST['transmission'];
    $fuel_type = $_POST['fuel_type'];
    $seats = $_POST['seats'];
    $rental_price = $_POST['rental_price'];
    $description = sanitize($_POST['description']);
    $availability = isset($_POST['availability']) ? 1 : 0;
    
    $image = '../assets/images/default-car.jpg';
    if(isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $upload_dir = '../uploads/';
        if(!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
        $image = '../uploads/' . time() . '_' . basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], $image);
    }
    
    if(isset($_POST['edit_id']) && $_POST['edit_id']) {
        $stmt = $pdo->prepare("UPDATE vehicles SET brand=?, model=?, year=?, transmission=?, fuel_type=?, seats=?, rental_price=?, description=?, availability=?, image=COALESCE(?, image) WHERE id=?");
        $stmt->execute([$brand, $model, $year, $transmission, $fuel_type, $seats, $rental_price, $description, $availability, $image, $_POST['edit_id']]);
        $_SESSION['success'] = "Vehicle updated successfully";
    } else {
        $stmt = $pdo->prepare("INSERT INTO vehicles (brand, model, year, transmission, fuel_type, seats, rental_price, description, availability, image) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$brand, $model, $year, $transmission, $fuel_type, $seats, $rental_price, $description, $availability, $image]);
        $_SESSION['success'] = "Vehicle added successfully";
    }
    redirect('vehicles.php');
}

// Toggle availability
if(isset($_GET['toggle'])) {
    $stmt = $pdo->prepare("UPDATE vehicles SET availability = NOT availability WHERE id = ?");
    $stmt->execute([$_GET['toggle']]);
    redirect('vehicles.php');
}

$vehicles = $pdo->query("SELECT * FROM vehicles ORDER BY id DESC")->fetchAll();

include 'includes/sidebar.php';
?>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<!-- Page Content -->
<?php if(isset($_SESSION['success'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i> <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="dashboard-card">
    <div class="dashboard-card-header">
        <h5><i class="fas fa-list me-2"></i> All Vehicles</h5>
        <button class="btn-danger-custom" data-bs-toggle="modal" data-bs-target="#vehicleModal">
            <i class="fas fa-plus me-2"></i> Add New Vehicle
        </button>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Image</th>
                    <th>Brand/Model</th>
                    <th>Year</th>
                    <th>Transmission</th>
                    <th>Fuel</th>
                    <th>Seats</th>
                    <th>Price/Day</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($vehicles as $vehicle): ?>
                <tr>
                    <td><?php echo $vehicle['id']; ?></td>
                    <td><img src="<?php echo $vehicle['image']; ?>" style="width: 60px; height: 60px; object-fit: cover; border-radius: 10px;" alt=""></td>
                    <td><strong><?php echo $vehicle['brand'] . ' ' . $vehicle['model']; ?></strong></td>
                    <td><?php echo $vehicle['year']; ?></td>
                    <td><?php echo $vehicle['transmission']; ?></td>
                    <td><?php echo $vehicle['fuel_type']; ?></td>
                    <td><?php echo $vehicle['seats']; ?></td>
                    <td class="text-danger fw-bold">$<?php echo number_format($vehicle['rental_price'], 2); ?></td>
                    <td>
                        <span class="badge bg-<?php echo $vehicle['availability'] ? 'success' : 'danger'; ?> rounded-pill">
                            <?php echo $vehicle['availability'] ? 'Available' : 'Booked'; ?>
                        </span>
                    </td>
                    <td>
                        <button class="btn btn-sm btn-outline-warning edit-vehicle" 
                            data-id="<?php echo $vehicle['id']; ?>"
                            data-brand="<?php echo $vehicle['brand']; ?>"
                            data-model="<?php echo $vehicle['model']; ?>"
                            data-year="<?php echo $vehicle['year']; ?>"
                            data-transmission="<?php echo $vehicle['transmission']; ?>"
                            data-fuel="<?php echo $vehicle['fuel_type']; ?>"
                            data-seats="<?php echo $vehicle['seats']; ?>"
                            data-price="<?php echo $vehicle['rental_price']; ?>"
                            data-description="<?php echo htmlspecialchars($vehicle['description']); ?>"
                            data-availability="<?php echo $vehicle['availability']; ?>">
                            <i class="fas fa-edit"></i>
                        </button>
                        <a href="?toggle=<?php echo $vehicle['id']; ?>" class="btn btn-sm btn-outline-info">
                            <i class="fas fa-exchange-alt"></i>
                        </a>
                        <a href="?delete=<?php echo $vehicle['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure?')">
                            <i class="fas fa-trash"></i>
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Vehicle Modal -->
<div class="modal fade" id="vehicleModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(135deg, #dc3545, #c82333); color: white;">
                <h5 class="modal-title"><i class="fas fa-car me-2"></i> Add/Edit Vehicle</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" name="edit_id" id="edit_id">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Brand *</label>
                            <input type="text" name="brand" id="brand" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Model *</label>
                            <input type="text" name="model" id="model" class="form-control" required>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label fw-bold">Year</label>
                            <input type="number" name="year" id="year" class="form-control" value="2024">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label fw-bold">Transmission</label>
                            <select name="transmission" id="transmission" class="form-select">
                                <option>Automatic</option>
                                <option>Manual</option>
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label fw-bold">Fuel Type</label>
                            <select name="fuel_type" id="fuel_type" class="form-select">
                                <option>Petrol</option>
                                <option>Diesel</option>
                                <option>Electric</option>
                                <option>Hybrid</option>
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label fw-bold">Seats</label>
                            <input type="number" name="seats" id="seats" class="form-control" value="5">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Rental Price ($/day) *</label>
                            <input type="number" step="0.01" name="rental_price" id="rental_price" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Availability</label>
                            <div class="form-check mt-2">
                                <input type="checkbox" name="availability" id="availability" class="form-check-input" value="1">
                                <label class="form-check-label">Available for booking</label>
                            </div>
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label fw-bold">Description</label>
                            <textarea name="description" id="description" class="form-control" rows="3"></textarea>
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label fw-bold">Vehicle Image</label>
                            <input type="file" name="image" class="form-control" accept="image/*">
                            <small class="text-muted">Leave empty to keep current image</small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Save Vehicle</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.querySelectorAll('.edit-vehicle').forEach(btn => {
    btn.addEventListener('click', function() {
        document.getElementById('edit_id').value = this.dataset.id;
        document.getElementById('brand').value = this.dataset.brand;
        document.getElementById('model').value = this.dataset.model;
        document.getElementById('year').value = this.dataset.year;
        document.getElementById('transmission').value = this.dataset.transmission;
        document.getElementById('fuel_type').value = this.dataset.fuel;
        document.getElementById('seats').value = this.dataset.seats;
        document.getElementById('rental_price').value = this.dataset.price;
        document.getElementById('description').value = this.dataset.description;
        document.getElementById('availability').checked = this.dataset.availability == '1';
        new bootstrap.Modal(document.getElementById('vehicleModal')).show();
    });
});
</script>

<?php
// Close the content-wrapper divs
echo '</div></div>';
?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>