<?php
// login.php
include 'includes/config.php';

if(isLoggedIn()) {
    if(isAdmin()) {
        redirect('admin/index.php');
    } else {
        redirect('dashboard.php');
    }
}

$error = '';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = sanitize($_POST['email']);
    $password = $_POST['password'];
    
    // Check in users table
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? AND is_active = 1");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    
    if($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['username'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_fullname'] = $user['full_name'];
        $_SESSION['user_role'] = $user['role'];
        
        if($user['role'] == 'admin') {
            redirect('admin/index.php');
        } else {
            redirect('dashboard.php');
        }
    } else {
        $error = 'Invalid email or password';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - <?php echo SITE_NAME; ?></title>
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
            overflow-x: hidden;
        }
        
        .floating-cars {
            position: absolute;
            width: 100%;
            height: 100%;
            overflow: hidden;
            z-index: 0;
        }
        
        .floating-car {
            position: absolute;
            opacity: 0.08;
            animation: float 20s infinite linear;
        }
        
        @keyframes float {
            0% { transform: translateX(-100px) translateY(0); }
            50% { transform: translateX(calc(100vw + 100px)) translateY(-50px); }
            100% { transform: translateX(calc(100vw + 100px)) translateY(0); }
        }
        
        .car1 { top: 20%; animation-duration: 25s; }
        .car2 { top: 50%; animation-duration: 35s; animation-delay: -5s; }
        .car3 { top: 70%; animation-duration: 30s; animation-delay: -10s; }
        
        .auth-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            position: relative;
            z-index: 1;
        }
        
        .auth-card {
            background: rgba(255, 255, 255, 0.98);
            border-radius: 30px;
            padding: 50px 40px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            width: 100%;
            max-width: 450px;
            backdrop-filter: blur(10px);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .auth-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 30px 60px -12px rgba(0, 0, 0, 0.3);
        }
        
        .auth-header {
            text-align: center;
            margin-bottom: 35px;
        }
        
        .logo-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #dc3545, #ff6b6b);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }
        
        .logo-icon i {
            font-size: 40px;
            color: white;
        }
        
        .auth-header h2 {
            font-family: 'Orbitron', monospace;
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 8px;
            color: #1a1a1a;
        }
        
        .auth-header p {
            color: #6c757d;
            font-size: 14px;
        }
        
        .form-group {
            margin-bottom: 20px;
            position: relative;
        }
        
        .form-group label {
            font-weight: 500;
            margin-bottom: 8px;
            display: block;
            color: #333;
        }
        
        .input-group-custom {
            position: relative;
        }
        
        .input-group-custom i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #dc3545;
            z-index: 10;
        }
        
        .form-control-custom {
            width: 100%;
            padding: 12px 15px 12px 45px;
            border: 2px solid #e9ecef;
            border-radius: 12px;
            font-size: 14px;
            transition: all 0.3s ease;
            background: white;
        }
        
        .form-control-custom:focus {
            outline: none;
            border-color: #dc3545;
            box-shadow: 0 0 0 3px rgba(220, 53, 69, 0.1);
        }
        
        .btn-login {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #dc3545, #bb2d3b);
            border: none;
            border-radius: 12px;
            color: white;
            font-weight: 600;
            font-size: 16px;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 10px;
        }
        
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(220, 53, 69, 0.4);
        }
        
        .auth-footer {
            text-align: center;
            margin-top: 25px;
            padding-top: 20px;
            border-top: 1px solid #e9ecef;
        }
        
        .auth-footer p {
            margin-bottom: 10px;
            color: #6c757d;
        }
        
        .auth-footer a {
            color: #dc3545;
            text-decoration: none;
            font-weight: 500;
        }
        
        .auth-footer a:hover {
            text-decoration: underline;
        }
        
        .alert-custom {
            padding: 12px 20px;
            border-radius: 12px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .alert-danger-custom {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .admin-hint {
            text-align: center;
            margin-top: 15px;
            font-size: 12px;
        }
        
        .admin-hint span {
            background: #f8f9fa;
            padding: 5px 10px;
            border-radius: 20px;
            color: #6c757d;
        }
        
        @media (max-width: 768px) {
            .auth-card {
                padding: 35px 25px;
            }
            
            .logo-icon {
                width: 60px;
                height: 60px;
            }
            
            .logo-icon i {
                font-size: 30px;
            }
            
            .auth-header h2 {
                font-size: 24px;
            }
        }
    </style>
</head>
<body>
    <div class="floating-cars">
        <i class="fas fa-car-side floating-car car1" style="font-size: 80px;"></i>
        <i class="fas fa-car floating-car car2" style="font-size: 60px;"></i>
        <i class="fas fa-truck floating-car car3" style="font-size: 70px;"></i>
    </div>
    
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <div class="logo-icon">
                    <i class="fas fa-car-side"></i>
                </div>
                <h2>Welcome Back</h2>
                <p>Login to your account</p>
            </div>
            
            <?php if($error): ?>
                <div class="alert-custom alert-danger-custom">
                    <i class="fas fa-exclamation-circle"></i>
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="form-group">
                    <label>Email Address</label>
                    <div class="input-group-custom">
                        <i class="fas fa-envelope"></i>
                        <input type="email" name="email" class="form-control-custom" placeholder="Enter your email" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Password</label>
                    <div class="input-group-custom">
                        <i class="fas fa-lock"></i>
                        <input type="password" name="password" class="form-control-custom" placeholder="Enter your password" required>
                    </div>
                </div>
                
                <button type="submit" class="btn-login">
                    <i class="fas fa-arrow-right-to-bracket"></i> Sign In
                </button>
            </form>
            
            <div class="admin-hint">
                <span><i class="fas fa-info-circle"></i> Demo Admin: admin@velocityrentals.com / admin123</span>
            </div>
            
            <div class="auth-footer">
                <p>Don't have an account? <a href="register.php">Create Account</a></p>
                <a href="index.php"><i class="fas fa-arrow-left"></i> Back to Home</a>
            </div>
        </div>
    </div>
</body>
</html>