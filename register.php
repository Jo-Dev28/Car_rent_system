<?php
// register.php
include 'includes/config.php';

if(isLoggedIn()) {
    redirect('dashboard.php');
}

$error = '';
$success = '';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = sanitize($_POST['username']);
    $email = sanitize($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $full_name = sanitize($_POST['full_name']);
    $phone = sanitize($_POST['phone']);
    
    if($password !== $confirm_password) {
        $error = 'Passwords do not match';
    } else {
        // Check if email exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if($stmt->fetch()) {
            $error = 'Email already registered';
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (username, email, password, full_name, phone) VALUES (?, ?, ?, ?, ?)");
            if($stmt->execute([$username, $email, $hashed_password, $full_name, $phone])) {
                $success = 'Registration successful! Please login.';
            } else {
                $error = 'Registration failed. Please try again.';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - <?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;500;600;700;800&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            position: relative;
        }
        
        .auth-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
        }
        
        .auth-card {
            background: rgba(255, 255, 255, 0.98);
            border-radius: 30px;
            padding: 40px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            width: 100%;
            max-width: 550px;
        }
        
        .auth-header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .logo-icon {
            width: 70px;
            height: 70px;
            background: linear-gradient(135deg, #dc3545, #ff6b6b);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
        }
        
        .logo-icon i {
            font-size: 35px;
            color: white;
        }
        
        .auth-header h2 {
            font-family: 'Orbitron', monospace;
            font-size: 28px;
            font-weight: 700;
            color: #1a1a1a;
        }
        
        .auth-header p {
            color: #6c757d;
            font-size: 14px;
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        .form-group label {
            font-weight: 500;
            margin-bottom: 5px;
            display: block;
            color: #333;
            font-size: 13px;
        }
        
        .input-group-custom {
            position: relative;
        }
        
        .input-group-custom i {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #dc3545;
        }
        
        .form-control-custom {
            width: 100%;
            padding: 10px 12px 10px 38px;
            border: 2px solid #e9ecef;
            border-radius: 10px;
            font-size: 14px;
            transition: all 0.3s ease;
        }
        
        .form-control-custom:focus {
            outline: none;
            border-color: #dc3545;
        }
        
        .btn-register {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #dc3545, #bb2d3b);
            border: none;
            border-radius: 10px;
            color: white;
            font-weight: 600;
            cursor: pointer;
            margin-top: 10px;
        }
        
        .btn-register:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(220, 53, 69, 0.4);
        }
        
        .auth-footer {
            text-align: center;
            margin-top: 20px;
            padding-top: 15px;
            border-top: 1px solid #e9ecef;
        }
        
        .auth-footer a {
            color: #dc3545;
            text-decoration: none;
        }
        
        .alert-custom {
            padding: 10px 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-size: 14px;
        }
        
        .alert-danger-custom {
            background: #f8d7da;
            color: #721c24;
        }
        
        .alert-success-custom {
            background: #d4edda;
            color: #155724;
        }
        
        @media (max-width: 768px) {
            .auth-card {
                padding: 30px 20px;
            }
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <div class="logo-icon">
                    <i class="fas fa-user-plus"></i>
                </div>
                <h2>Create Account</h2>
                <p>Join Velocity Rentals today</p>
            </div>
            
            <?php if($error): ?>
                <div class="alert-custom alert-danger-custom">
                    <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <?php if($success): ?>
                <div class="alert-custom alert-success-custom">
                    <i class="fas fa-check-circle"></i> <?php echo $success; ?>
                    <script>
                        setTimeout(() => {
                            window.location.href = 'login.php';
                        }, 2000);
                    </script>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Username *</label>
                            <div class="input-group-custom">
                                <i class="fas fa-user"></i>
                                <input type="text" name="username" class="form-control-custom" required>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Full Name</label>
                            <div class="input-group-custom">
                                <i class="fas fa-id-card"></i>
                                <input type="text" name="full_name" class="form-control-custom">
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Email Address *</label>
                    <div class="input-group-custom">
                        <i class="fas fa-envelope"></i>
                        <input type="email" name="email" class="form-control-custom" required>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Password *</label>
                            <div class="input-group-custom">
                                <i class="fas fa-lock"></i>
                                <input type="password" name="password" class="form-control-custom" required>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Confirm Password *</label>
                            <div class="input-group-custom">
                                <i class="fas fa-check-circle"></i>
                                <input type="password" name="confirm_password" class="form-control-custom" required>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Phone Number</label>
                    <div class="input-group-custom">
                        <i class="fas fa-phone"></i>
                        <input type="tel" name="phone" class="form-control-custom" placeholder="+1234567890">
                    </div>
                </div>
                
                <button type="submit" class="btn-register">
                    <i class="fas fa-user-plus"></i> Register Account
                </button>
            </form>
            
            <div class="auth-footer">
                <p>Already have an account? <a href="login.php">Login here</a></p>
                <a href="index.php"><i class="fas fa-arrow-left"></i> Back to Home</a>
            </div>
        </div>
    </div>
</body>
</html>