<?php
// profile.php
include 'includes/config.php';

if(!isLoggedIn()) {
    redirect('login.php');
}

$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

$success = '';
$error = '';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $full_name = sanitize($_POST['full_name']);
    $phone = sanitize($_POST['phone']);
    
    $stmt = $pdo->prepare("UPDATE users SET full_name = ?, phone = ? WHERE id = ?");
    if($stmt->execute([$full_name, $phone, $_SESSION['user_id']])) {
        $success = 'Profile updated successfully!';
        $_SESSION['user_name'] = $full_name;
    } else {
        $error = 'Update failed. Please try again.';
    }
}

include 'includes/header.php';
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-0 pt-4">
                    <div class="text-center">
                        <div class="bg-danger bg-opacity-10 rounded-circle d-inline-flex p-3 mb-3">
                            <i class="fas fa-user-circle fa-3x text-danger"></i>
                        </div>
                        <h3 class="mb-1">My Profile</h3>
                        <p class="text-muted">Manage your account information</p>
                    </div>
                </div>
                <div class="card-body p-4">
                    <?php if($success): ?>
                        <div class="alert alert-success"><?php echo $success; ?></div>
                    <?php endif; ?>
                    <?php if($error): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>
                    
                    <form method="POST" action="">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Username</label>
                            <input type="text" class="form-control bg-light" value="<?php echo htmlspecialchars($user['username']); ?>" disabled>
                            <small class="text-muted">Username cannot be changed</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Email Address</label>
                            <input type="email" class="form-control bg-light" value="<?php echo htmlspecialchars($user['email']); ?>" disabled>
                            <small class="text-muted">Email cannot be changed</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Full Name</label>
                            <input type="text" name="full_name" class="form-control" value="<?php echo htmlspecialchars($user['full_name'] ?? ''); ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Phone Number</label>
                            <input type="tel" name="phone" class="form-control" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Member Since</label>
                            <input type="text" class="form-control bg-light" value="<?php echo date('F j, Y', strtotime($user['created_at'])); ?>" disabled>
                        </div>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-danger px-4">Update Profile</button>
                            <a href="change-password.php" class="btn btn-outline-danger px-4">Change Password</a>
                            <a href="dashboard.php" class="btn btn-outline-secondary ms-auto">Back to Dashboard</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>