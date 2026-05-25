<?php
// includes/header.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo SITE_NAME; ?> - <?php echo SITE_TAGLINE; ?></title>
    
    <!-- Google Fonts - Premium Typography -->
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;500;600;700;800;900&family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- AOS Animation Library -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    
    <style>
        /* Premium Font Styling */
        :root {
            --primary-red: #dc3545;
            --primary-red-dark: #bb2d3b;
            --dark-bg: #0a0a0a;
            --light-bg: #f8f9fa;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            padding-top: 85px;
            overflow-x: hidden;
        }
        
        /* Premium Navbar */
        .premium-navbar {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(10px);
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.05);
            padding: 0.8rem 0;
            transition: all 0.4s ease;
        }
        
        .premium-navbar.scrolled {
            padding: 0.5rem 0;
            background: white;
            box-shadow: 0 5px 30px rgba(0, 0, 0, 0.1);
        }
        
        /* Logo Styling */
        .navbar-brand {
            font-family: 'Orbitron', monospace;
            font-size: 1.6rem;
            font-weight: 800;
            letter-spacing: -0.5px;
            position: relative;
            transition: all 0.3s ease;
        }
        
        .navbar-brand i {
            font-size: 1.8rem;
            color: var(--primary-red);
            margin-right: 8px;
            transition: transform 0.3s ease;
        }
        
        .navbar-brand:hover i {
            transform: rotate(-10deg) scale(1.1);
        }
        
        .brand-white {
            color: #1a1a1a;
        }
        
        .brand-red {
            color: var(--primary-red);
            position: relative;
        }
        
        .brand-red::after {
            content: '';
            position: absolute;
            bottom: -3px;
            left: 0;
            width: 100%;
            height: 2px;
            background: var(--primary-red);
            transform: scaleX(0);
            transition: transform 0.3s ease;
        }
        
        .navbar-brand:hover .brand-red::after {
            transform: scaleX(1);
        }
        
        /* Navigation Links */
        .nav-link {
            font-family: 'Poppins', sans-serif;
            font-weight: 500;
            font-size: 0.95rem;
            margin: 0 0.3rem;
            padding: 0.5rem 1rem;
            color: #2c3e50;
            position: relative;
            transition: all 0.3s ease;
        }
        
        .nav-link::before {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 0;
            height: 2px;
            background: var(--primary-red);
            transition: width 0.3s ease;
        }
        
        .nav-link:hover::before,
        .nav-link.active::before {
            width: 70%;
        }
        
        .nav-link:hover {
            color: var(--primary-red);
            transform: translateY(-2px);
        }
        
        /* CTA Button */
        .btn-nav-cta {
            background: linear-gradient(135deg, var(--primary-red), var(--primary-red-dark));
            color: white !important;
            border-radius: 50px;
            padding: 0.5rem 1.5rem !important;
            font-weight: 600;
            box-shadow: 0 4px 15px rgba(220, 53, 69, 0.3);
            transition: all 0.3s ease;
        }
        
        .btn-nav-cta:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(220, 53, 69, 0.4);
        }
        
        .btn-nav-cta::before {
            display: none;
        }
        
        /* Admin Badge in Navbar */
        .admin-badge {
            background: #dc3545;
            color: white;
            font-size: 0.7rem;
            padding: 3px 8px;
            border-radius: 20px;
            margin-left: 10px;
            font-weight: 500;
        }
        
        /* Theme Toggle */
        .theme-toggle {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #f0f0f0;
            border: none;
            transition: all 0.3s ease;
        }
        
        .theme-toggle:hover {
            background: var(--primary-red);
            color: white;
            transform: rotate(15deg);
        }
        
        /* Mobile Responsive */
        @media (max-width: 991px) {
            body {
                padding-top: 70px;
            }
            
            .navbar-brand {
                font-size: 1.3rem;
            }
            
            .navbar-brand i {
                font-size: 1.5rem;
            }
            
            .nav-link {
                text-align: center;
                padding: 0.8rem 1rem;
            }
            
            .nav-link::before {
                display: none;
            }
            
            .btn-nav-cta {
                width: fit-content;
                margin: 0 auto;
            }
            
            .navbar-collapse {
                background: white;
                border-radius: 20px;
                padding: 20px;
                margin-top: 15px;
                box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            }
            
            .admin-badge {
                display: inline-block;
                margin-left: 5px;
            }
        }
        
        /* Page Headers with Premium Font */
        .premium-page-header {
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
            color: white;
            margin-top: -85px;
            padding: 80px 0 60px;
            position: relative;
            overflow: hidden;
        }
        
        .premium-page-header::before {
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
        
        .premium-page-header h1 {
            font-family: 'Orbitron', monospace;
            font-size: 3.5rem;
            font-weight: 800;
            margin-bottom: 15px;
            position: relative;
            z-index: 1;
        }
        
        .premium-page-header .lead {
            font-family: 'Poppins', sans-serif;
            font-size: 1.2rem;
            font-weight: 400;
            opacity: 0.9;
            position: relative;
            z-index: 1;
        }
        
        @media (max-width: 768px) {
            .premium-page-header {
                margin-top: -70px;
                padding: 100px 0 50px;
            }
            
            .premium-page-header h1 {
                font-size: 2rem;
            }
            
            .premium-page-header .lead {
                font-size: 1rem;
            }
        }
        
        /* Section Headers */
        .section-header-premium {
            text-align: center;
            margin-bottom: 50px;
        }
        
        .section-badge {
            display: inline-block;
            background: rgba(220, 53, 69, 0.1);
            color: var(--primary-red);
            padding: 8px 20px;
            border-radius: 50px;
            font-size: 0.85rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 20px;
        }
        
        .section-header-premium h2 {
            font-family: 'Orbitron', monospace;
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 15px;
        }
        
        .section-header-premium .text-red {
            color: var(--primary-red);
            position: relative;
            display: inline-block;
        }
        
        .section-header-premium p {
            font-size: 1.1rem;
            color: #6c757d;
        }
        
        @media (max-width: 768px) {
            .section-header-premium h2 {
                font-size: 1.8rem;
            }
        }
        
        /* Dark Mode Styles */
        body.dark-mode {
            background: #1a1a1a;
            color: #ffffff;
        }
        
        body.dark-mode .premium-navbar {
            background: rgba(26, 26, 26, 0.98);
        }
        
        body.dark-mode .navbar-brand .brand-white {
            color: white;
        }
        
        body.dark-mode .nav-link {
            color: #e0e0e0;
        }
        
        body.dark-mode .nav-link:hover {
            color: var(--primary-red);
        }
        
        body.dark-mode .theme-toggle {
            background: #333;
            color: white;
        }
        
        /* Scroll to top button */
        .scroll-top {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: var(--primary-red);
            color: white;
            border: none;
            cursor: pointer;
            display: none;
            z-index: 1000;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(220, 53, 69, 0.3);
        }
        
        .scroll-top:hover {
            transform: translateY(-5px);
            background: var(--primary-red-dark);
        }
    </style>
</head>
<body>

<!-- Premium Navbar -->
<nav class="navbar navbar-expand-lg fixed-top premium-navbar" id="mainNav">
    <div class="container">
        <a class="navbar-brand" href="index.php">
            <i class="fas fa-car-side"></i>
            <span class="brand-white">AUTO</span><span class="brand-red">MOBILE</span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>" href="index.php">
                        <i class="fas fa-home"></i> Home
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'cars.php' ? 'active' : ''; ?>" href="cars.php">
                        <i class="fas fa-car"></i> Vehicles
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="index.php#how-it-works">
                        <i class="fas fa-question-circle"></i> How It Works
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="index.php#testimonials">
                        <i class="fas fa-star"></i> Testimonials
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'contact.php' ? 'active' : ''; ?>" href="contact.php">
                        <i class="fas fa-envelope"></i> Contact
                    </a>
                </li>
                <?php if(isLoggedIn()): ?>
                    <?php if(isAdmin()): ?>
                        <!-- Show Admin Panel link for admin users -->
                        <li class="nav-item">
                            <a class="nav-link" href="admin/index.php">
                                <i class="fas fa-shield-alt"></i> Admin
                                <span class="admin-badge"><i class="fas fa-crown"></i> Admin</span>
                            </a>
                        </li>
                    <?php else: ?>
                        <!-- Show Dashboard link for regular users -->
                        <li class="nav-item">
                            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>" href="dashboard.php">
                                <i class="fas fa-tachometer-alt"></i> Dashboard
                            </a>
                        </li>
                    <?php endif; ?>
                    <li class="nav-item">
                        <a class="nav-link btn-nav-cta" href="logout.php">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </a>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link" href="login.php">
                            <i class="fas fa-lock"></i> Login
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link btn-nav-cta" href="register.php">
                            <i class="fas fa-user-plus"></i> Register
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
            <button id="themeToggle" class="theme-toggle ms-3">
                <i class="fas fa-moon"></i>
            </button>
        </div>
    </div>
</nav>

<!-- Page Header Function (to be used on individual pages) -->
<?php if(isset($page_header) && $page_header): ?>
<div class="premium-page-header">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto text-center">
                <h1 data-aos="fade-up"><?php echo $page_title ?? 'Dashboard'; ?></h1>
                <p class="lead" data-aos="fade-up" data-aos-delay="100"><?php echo $page_subtitle ?? 'Manage your account and bookings'; ?></p>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<script>
// Navbar scroll effect
window.addEventListener('scroll', function() {
    const navbar = document.getElementById('mainNav');
    if (window.scrollY > 50) {
        navbar.classList.add('scrolled');
    } else {
        navbar.classList.remove('scrolled');
    }
});

// Theme Toggle
const themeToggle = document.getElementById('themeToggle');
if (themeToggle) {
    themeToggle.addEventListener('click', () => {
        document.body.classList.toggle('dark-mode');
        const icon = themeToggle.querySelector('i');
        if (document.body.classList.contains('dark-mode')) {
            icon.classList.remove('fa-moon');
            icon.classList.add('fa-sun');
            localStorage.setItem('theme', 'dark');
        } else {
            icon.classList.remove('fa-sun');
            icon.classList.add('fa-moon');
            localStorage.setItem('theme', 'light');
        }
    });
    
    if (localStorage.getItem('theme') === 'dark') {
        document.body.classList.add('dark-mode');
        themeToggle.querySelector('i').classList.remove('fa-moon');
        themeToggle.querySelector('i').classList.add('fa-sun');
    }
}

// Scroll to top button
const scrollBtn = document.createElement('button');
scrollBtn.innerHTML = '<i class="fas fa-arrow-up"></i>';
scrollBtn.className = 'scroll-top';
document.body.appendChild(scrollBtn);

window.addEventListener('scroll', () => {
    if (window.scrollY > 300) {
        scrollBtn.style.display = 'block';
    } else {
        scrollBtn.style.display = 'none';
    }
});

scrollBtn.addEventListener('click', () => {
    window.scrollTo({ top: 0, behavior: 'smooth' });
});
</script>