<?php
// change-password.php
include 'includes/config.php';
include 'includes/header.php';

if(!isLoggedIn()) {
    redirect('login.php');
}

$error = '';
$success = '';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $current = $_POST['current_password'];
    $new = $_POST['new_password'];
    $confirm = $_POST['confirm_password'];
    
    $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();
    
    if(!password_verify($current, $user['password'])) {
        $error = 'Current password is incorrect';
    } elseif($new != $confirm) {
        $error = 'New passwords do not match';
    } elseif(strlen($new) < 6) {
        $error = 'Password must be at least 6 characters';
    } else {
        $hashed = password_hash($new, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
        if($stmt->execute([$hashed, $_SESSION['user_id']])) {
            $success = 'Password changed successfully!';
        }
    }
}
?>

<section class="profile-section py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6" data-aos="fade-up">
                <div class="profile-card">
                    <div class="profile-header">
                        <i class="fas fa-lock fa-4x text-danger"></i>
                        <h2>Change Password</h2>
                    </div>
                    
                    <?php if($success): ?>
                        <div class="alert alert-success"><?php echo $success; ?></div>
                    <?php endif; ?>
                    <?php if($error): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>
                    
                    <form method="POST" action="">
                        <div class="mb-3">
                            <label class="form-label">Current Password</label>
                            <input type="password" name="current_password" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">New Password</label>
                            <input type="password" name="new_password" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Confirm New Password</label>
                            <input type="password" name="confirm_password" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-danger w-100">Change Password</button>
                        <a href="profile.php" class="btn btn-outline-secondary w-100 mt-2">Back to Profile</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>