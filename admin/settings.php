<?php
// admin/settings.php
require_once '../includes/config.php';

if(!isset($_SESSION['user_role']) || $_SESSION['user_role'] != 'admin') {
    redirect('../login.php');
}

$success = '';
$error = '';

// Update admin profile
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_profile'])) {
    $full_name = sanitize($_POST['full_name']);
    $phone = sanitize($_POST['phone']);
    
    $stmt = $pdo->prepare("UPDATE users SET full_name = ?, phone = ? WHERE id = ?");
    if($stmt->execute([$full_name, $phone, $_SESSION['user_id']])) {
        $success = "Profile updated successfully!";
        $_SESSION['user_name'] = $full_name;
    } else {
        $error = "Failed to update profile";
    }
}

// Change password
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Verify current password
    $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();
    
    if(!password_verify($current_password, $user['password'])) {
        $error = "Current password is incorrect";
    } elseif($new_password != $confirm_password) {
        $error = "New passwords do not match";
    } elseif(strlen($new_password) < 6) {
        $error = "Password must be at least 6 characters";
    } else {
        $hashed = password_hash($new_password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
        if($stmt->execute([$hashed, $_SESSION['user_id']])) {
            $success = "Password changed successfully!";
        } else {
            $error = "Failed to change password";
        }
    }
}

// Update site settings (save to a settings table or file)
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_site'])) {
    $site_name = sanitize($_POST['site_name']);
    $site_email = sanitize($_POST['site_email']);
    $site_phone = sanitize($_POST['site_phone']);
    $site_address = sanitize($_POST['site_address']);
    
    // You can save these to a settings table
    $_SESSION['site_settings'] = [
        'name' => $site_name,
        'email' => $site_email,
        'phone' => $site_phone,
        'address' => $site_address
    ];
    $success = "Site settings updated successfully!";
}

// Get admin data
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$admin = $stmt->fetch();

include 'includes/sidebar.php';
?>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<!-- Page Content -->
<?php if($success): ?>
    <div class="alert alert-success alert-dismissible fade show">
        <i class="fas fa-check-circle me-2"></i> <?php echo $success; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if($error): ?>
    <div class="alert alert-danger alert-dismissible fade show">
        <i class="fas fa-exclamation-circle me-2"></i> <?php echo $error; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="row g-4">
    <!-- Profile Settings -->
    <div class="col-md-6">
        <div class="dashboard-card">
            <div class="dashboard-card-header">
                <h5><i class="fas fa-user-circle text-danger me-2"></i> Profile Settings</h5>
            </div>
            <form method="POST" action="">
                <div class="mb-3">
                    <label class="form-label fw-bold">Username</label>
                    <input type="text" class="form-control bg-light" value="<?php echo htmlspecialchars($admin['username']); ?>" disabled>
                    <small class="text-muted">Username cannot be changed</small>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Email Address</label>
                    <input type="email" class="form-control bg-light" value="<?php echo htmlspecialchars($admin['email']); ?>" disabled>
                    <small class="text-muted">Email cannot be changed</small>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Full Name</label>
                    <input type="text" name="full_name" class="form-control" value="<?php echo htmlspecialchars($admin['full_name'] ?? ''); ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Phone Number</label>
                    <input type="tel" name="phone" class="form-control" value="<?php echo htmlspecialchars($admin['phone'] ?? ''); ?>">
                </div>
                <button type="submit" name="update_profile" class="btn btn-danger w-100">Update Profile</button>
            </form>
        </div>
    </div>
    
    <!-- Change Password -->
    <div class="col-md-6">
        <div class="dashboard-card">
            <div class="dashboard-card-header">
                <h5><i class="fas fa-key text-danger me-2"></i> Change Password</h5>
            </div>
            <form method="POST" action="">
                <div class="mb-3">
                    <label class="form-label fw-bold">Current Password</label>
                    <input type="password" name="current_password" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">New Password</label>
                    <input type="password" name="new_password" class="form-control" required>
                    <small class="text-muted">Minimum 6 characters</small>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Confirm New Password</label>
                    <input type="password" name="confirm_password" class="form-control" required>
                </div>
                <button type="submit" name="change_password" class="btn btn-outline-danger w-100">Change Password</button>
            </form>
        </div>
    </div>
    
    <!-- Site Settings -->
    <div class="col-md-12">
        <div class="dashboard-card">
            <div class="dashboard-card-header">
                <h5><i class="fas fa-globe text-danger me-2"></i> Site Settings</h5>
            </div>
            <form method="POST" action="">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Site Name</label>
                        <input type="text" name="site_name" class="form-control" value="<?php echo SITE_NAME; ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Site Email</label>
                        <input type="email" name="site_email" class="form-control" value="info@velocityrentals.com">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Contact Phone</label>
                        <input type="text" name="site_phone" class="form-control" value="+1 (555) 123-4567">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Contact Address</label>
                        <input type="text" name="site_address" class="form-control" value="123 Luxury Avenue, NY 10001">
                    </div>
                </div>
                <button type="submit" name="update_site" class="btn btn-danger">Save Site Settings</button>
            </form>
        </div>
    </div>
    
    <!-- System Info -->
    <div class="col-md-12">
        <div class="dashboard-card">
            <div class="dashboard-card-header">
                <h5><i class="fas fa-info-circle text-danger me-2"></i> System Information</h5>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="p-3 bg-light rounded mb-2">
                        <small class="text-muted">PHP Version</small>
                        <h6 class="mb-0"><?php echo phpversion(); ?></h6>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="p-3 bg-light rounded mb-2">
                        <small class="text-muted">MySQL Version</small>
                        <h6 class="mb-0"><?php echo $pdo->getAttribute(PDO::ATTR_SERVER_VERSION); ?></h6>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="p-3 bg-light rounded mb-2">
                        <small class="text-muted">Server Time</small>
                        <h6 class="mb-0"><?php echo date('Y-m-d H:i:s'); ?></h6>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
echo '</div></div>';
?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>