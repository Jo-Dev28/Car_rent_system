<?php
// admin/includes/sidebar.php
// Check if user is admin
if(!isset($_SESSION['user_role']) || $_SESSION['user_role'] != 'admin') {
    redirect('../login.php');
}

// Get counts for badges
$vehicleCount = $pdo->query("SELECT COUNT(*) FROM vehicles WHERE availability = 1")->fetchColumn();
$pendingBookings = $pdo->query("SELECT COUNT(*) FROM bookings WHERE status = 'pending'")->fetchColumn();
$userCount = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'user'")->fetchColumn();
$pendingTestimonials = $pdo->query("SELECT COUNT(*) FROM testimonials WHERE is_approved = 0")->fetchColumn();
$unreadMessages = $pdo->query("SELECT COUNT(*) FROM contacts WHERE is_read = 0")->fetchColumn();

// Get current page title
$page_title = basename($_SERVER['PHP_SELF'], '.php');
$page_titles = [
    'index' => 'Dashboard',
    'vehicles' => 'Manage Vehicles',
    'bookings' => 'Manage Bookings',
    'users' => 'Manage Users',
    'testimonials' => 'Testimonials',
    'contacts' => 'Contact Messages',
    'reports' => 'Reports',
    'settings' => 'Settings'
];
$current_title = $page_titles[$page_title] ?? 'Admin Panel';
?>

<style>
    /* Sidebar Styles - Red/Black Theme */
    .admin-sidebar {
        background: linear-gradient(180deg, #0a0a0a 0%, #1a1a1a 100%);
        min-height: 100vh;
        color: white;
        position: fixed;
        left: 0;
        top: 0;
        width: 280px;
        z-index: 1000;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 2px 0 20px rgba(0,0,0,0.3);
    }
    
    .admin-sidebar.collapsed {
        width: 80px;
    }
    
    .admin-sidebar.collapsed .sidebar-text,
    .admin-sidebar.collapsed .user-name,
    .admin-sidebar.collapsed .user-role,
    .admin-sidebar.collapsed .sidebar-badge {
        display: none;
    }
    
    .admin-sidebar.collapsed .sidebar-link i {
        margin-right: 0;
        font-size: 22px;
    }
    
    .admin-sidebar.collapsed .sidebar-logo h4 {
        font-size: 0;
    }
    
    .admin-sidebar.collapsed .sidebar-logo h4 i {
        font-size: 28px;
    }
    
    .admin-sidebar.collapsed .user-avatar {
        width: 45px;
        height: 45px;
    }
    
    .admin-sidebar.collapsed .user-avatar i {
        font-size: 22px;
    }
    
    /* View Site Button */
    .view-site-btn {
        margin: 15px 20px;
        padding: 12px;
        background: linear-gradient(135deg, #28a745, #20c997);
        border: none;
        border-radius: 12px;
        color: white;
        font-weight: 600;
        text-align: center;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        transition: all 0.3s ease;
        text-decoration: none;
    }
    
    .view-site-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(40,167,69,0.3);
        color: white;
    }
    
    .admin-sidebar.collapsed .view-site-btn span {
        display: none;
    }
    
    .admin-sidebar.collapsed .view-site-btn {
        padding: 12px;
        border-radius: 50%;
        width: 45px;
        margin: 15px auto;
    }
    
    .sidebar-logo {
        padding: 25px 20px;
        border-bottom: 1px solid rgba(220,53,69,0.3);
        margin-bottom: 20px;
    }
    
    .sidebar-logo h4 {
        font-family: 'Orbitron', monospace;
        font-weight: 700;
        margin: 0;
        font-size: 1.3rem;
    }
    
    .sidebar-logo h4 i {
        color: #dc3545;
        margin-right: 8px;
    }
    
    .user-info {
        padding: 20px;
        text-align: center;
        border-bottom: 1px solid rgba(220,53,69,0.3);
        margin-bottom: 20px;
    }
    
    .user-avatar {
        width: 70px;
        height: 70px;
        background: linear-gradient(135deg, #dc3545, #ff4757);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 12px;
        box-shadow: 0 5px 15px rgba(220,53,69,0.3);
        transition: all 0.3s;
    }
    
    .user-avatar i {
        font-size: 32px;
        color: white;
    }
    
    .user-name {
        font-weight: 700;
        margin-bottom: 5px;
        font-size: 1rem;
    }
    
    .user-role {
        font-size: 11px;
        opacity: 0.8;
    }
    
    .sidebar-menu {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    
    .sidebar-menu li {
        margin-bottom: 5px;
    }
    
    .sidebar-link {
        display: flex;
        align-items: center;
        padding: 12px 20px;
        color: rgba(255,255,255,0.85);
        text-decoration: none;
        transition: all 0.3s ease;
        border-left: 3px solid transparent;
        font-size: 14px;
        font-weight: 500;
    }
    
    .sidebar-link:hover {
        background: rgba(220,53,69,0.15);
        color: #dc3545;
        border-left-color: #dc3545;
        padding-left: 25px;
    }
    
    .sidebar-link.active {
        background: linear-gradient(90deg, rgba(220,53,69,0.2), transparent);
        color: #dc3545;
        border-left-color: #dc3545;
    }
    
    .sidebar-link i {
        width: 28px;
        margin-right: 12px;
        font-size: 18px;
        text-align: center;
    }
    
    .sidebar-badge {
        background: #dc3545;
        color: white;
        border-radius: 20px;
        padding: 2px 8px;
        font-size: 10px;
        font-weight: 600;
        margin-left: auto;
        box-shadow: 0 2px 5px rgba(220,53,69,0.3);
    }
    
    .toggle-sidebar {
        position: absolute;
        right: -12px;
        top: 80px;
        background: #dc3545;
        border: none;
        color: white;
        width: 24px;
        height: 24px;
        border-radius: 50%;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
        z-index: 1001;
        box-shadow: 0 2px 5px rgba(0,0,0,0.2);
    }
    
    .toggle-sidebar:hover {
        transform: scale(1.1);
        background: #ff4757;
    }
    
    /* Main content adjustment */
    .admin-main-content {
        margin-left: 280px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        min-height: 100vh;
        background: #f5f5f5;
    }
    
    .admin-main-content.expanded {
        margin-left: 80px;
    }
    
    /* Top Navbar - Red/Black Theme */
    .admin-topbar {
        background: white;
        padding: 15px 30px;
        box-shadow: 0 2px 15px rgba(0,0,0,0.08);
        display: flex;
        justify-content: space-between;
        align-items: center;
        position: sticky;
        top: 0;
        z-index: 999;
    }
    
    .page-title {
        font-size: 1.5rem;
        font-weight: 700;
        margin: 0;
        background: linear-gradient(135deg, #dc3545, #ff4757);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
    
    .topbar-right {
        display: flex;
        align-items: center;
        gap: 25px;
    }
    
    .notification-icon {
        position: relative;
        cursor: pointer;
        color: #333;
        transition: color 0.3s;
    }
    
    .notification-icon:hover {
        color: #dc3545;
    }
    
    .notification-badge {
        position: absolute;
        top: -8px;
        right: -8px;
        background: #dc3545;
        color: white;
        border-radius: 50%;
        width: 18px;
        height: 18px;
        font-size: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
    }
    
    .admin-dropdown {
        position: relative;
        cursor: pointer;
    }
    
    .admin-dropdown > div {
        padding: 8px 12px;
        border-radius: 40px;
        transition: all 0.3s;
        background: #f8f9fa;
    }
    
    .admin-dropdown > div:hover {
        background: #dc3545;
        color: white;
    }
    
    .admin-dropdown > div:hover i {
        color: white;
    }
    
    .admin-dropdown-menu {
        position: absolute;
        top: 100%;
        right: 0;
        background: white;
        border-radius: 12px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        min-width: 200px;
        display: none;
        z-index: 1000;
        margin-top: 10px;
        overflow: hidden;
    }
    
    .admin-dropdown:hover .admin-dropdown-menu {
        display: block;
        animation: fadeIn 0.3s ease;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    .admin-dropdown-menu a {
        display: block;
        padding: 12px 20px;
        color: #333;
        text-decoration: none;
        transition: all 0.3s;
        font-size: 14px;
    }
    
    .admin-dropdown-menu a:hover {
        background: #f8f9fa;
        color: #dc3545;
        padding-left: 25px;
    }
    
    .admin-dropdown-menu hr {
        margin: 5px 0;
    }
    
    /* Dashboard Cards */
    .dashboard-card {
        background: white;
        border-radius: 20px;
        padding: 25px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.05);
        margin-bottom: 25px;
        border: none;
    }
    
    .dashboard-card-header {
        border-bottom: 2px solid #f0f0f0;
        padding-bottom: 15px;
        margin-bottom: 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .dashboard-card-header h5 {
        margin: 0;
        font-weight: 700;
        color: #1a1a1a;
    }
    
    .dashboard-card-header h5 i {
        color: #dc3545;
    }
    
    .btn-danger-custom {
        background: linear-gradient(135deg, #dc3545, #c82333);
        border: none;
        border-radius: 10px;
        padding: 10px 20px;
        color: white;
        font-weight: 500;
        transition: all 0.3s;
    }
    
    .btn-danger-custom:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(220,53,69,0.4);
    }
    
    /* Mobile Responsive */
    @media (max-width: 768px) {
        .admin-sidebar {
            transform: translateX(-100%);
        }
        
        .admin-sidebar.mobile-open {
            transform: translateX(0);
        }
        
        .admin-main-content {
            margin-left: 0;
        }
    }
</style>

<script>
function toggleSidebar() {
    const sidebar = document.querySelector('.admin-sidebar');
    const mainContent = document.querySelector('.admin-main-content');
    sidebar.classList.toggle('collapsed');
    mainContent.classList.toggle('expanded');
}

// Function to open user side in new tab
function goToUserSide() {
    window.open('../index.php', '_blank');
}
</script>

<!-- Sidebar HTML -->
<div class="admin-sidebar">
    <button class="toggle-sidebar" onclick="toggleSidebar()">
        <i class="fas fa-chevron-left"></i>
    </button>
    
    <div class="sidebar-logo">
        <h4><i class="fas fa-car-side"></i> <span class="sidebar-text">AUTOMOBILE</span></h4>
    </div>
    
    <div class="user-info">
        <div class="user-avatar">
            <i class="fas fa-user-shield"></i>
        </div>
        <div class="user-name sidebar-text"><?php echo htmlspecialchars($_SESSION['user_name']); ?></div>
        <div class="user-role sidebar-text"><span class="badge bg-danger">Administrator</span></div>
    </div>
    
    
    <ul class="sidebar-menu">
        <li>
            <a href="index.php" class="sidebar-link <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>">
                <i class="fas fa-tachometer-alt"></i>
                <span class="sidebar-text">Dashboard</span>
            </a>
        </li>
        <li>
            <a href="vehicles.php" class="sidebar-link <?php echo basename($_SERVER['PHP_SELF']) == 'vehicles.php' ? 'active' : ''; ?>">
                <i class="fas fa-car"></i>
                <span class="sidebar-text">Manage Vehicles</span>
                <span class="sidebar-badge"><?php echo $vehicleCount; ?></span>
            </a>
        </li>
        <li>
            <a href="bookings.php" class="sidebar-link <?php echo basename($_SERVER['PHP_SELF']) == 'bookings.php' ? 'active' : ''; ?>">
                <i class="fas fa-calendar-alt"></i>
                <span class="sidebar-text">Manage Bookings</span>
                <?php if($pendingBookings > 0): ?>
                <span class="sidebar-badge"><?php echo $pendingBookings; ?></span>
                <?php endif; ?>
            </a>
        </li>
        <li>
            <a href="users.php" class="sidebar-link <?php echo basename($_SERVER['PHP_SELF']) == 'users.php' ? 'active' : ''; ?>">
                <i class="fas fa-users"></i>
                <span class="sidebar-text">Manage Users</span>
                <span class="sidebar-badge"><?php echo $userCount; ?></span>
            </a>
        </li>
        <li>
            <a href="testimonials.php" class="sidebar-link <?php echo basename($_SERVER['PHP_SELF']) == 'testimonials.php' ? 'active' : ''; ?>">
                <i class="fas fa-star"></i>
                <span class="sidebar-text">Testimonials</span>
                <?php if($pendingTestimonials > 0): ?>
                <span class="sidebar-badge"><?php echo $pendingTestimonials; ?></span>
                <?php endif; ?>
            </a>
        </li>
        <li>
            <a href="contacts.php" class="sidebar-link <?php echo basename($_SERVER['PHP_SELF']) == 'contacts.php' ? 'active' : ''; ?>">
                <i class="fas fa-envelope"></i>
                <span class="sidebar-text">Contact Messages</span>
                <?php if($unreadMessages > 0): ?>
                <span class="sidebar-badge"><?php echo $unreadMessages; ?></span>
                <?php endif; ?>
            </a>
        </li>
        <li>
            <a href="reports.php" class="sidebar-link <?php echo basename($_SERVER['PHP_SELF']) == 'reports.php' ? 'active' : ''; ?>">
                <i class="fas fa-chart-bar"></i>
                <span class="sidebar-text">Reports</span>
            </a>
        </li>
        <li>
            <a href="settings.php" class="sidebar-link <?php echo basename($_SERVER['PHP_SELF']) == 'settings.php' ? 'active' : ''; ?>">
                <i class="fas fa-cog"></i>
                <span class="sidebar-text">Settings</span>
            </a>
        </li>
        <!-- View Site Button - Opens User Side -->
    <button onclick="goToUserSide()" class="view-site-btn">
        <i class="fas fa-external-link-alt"></i>
        <span class="sidebar-text">View Website</span>
    </button>
        <li>
            <a href="../logout.php" class="sidebar-link">
                <i class="fas fa-sign-out-alt"></i>
                <span class="sidebar-text">Logout</span>
            </a>
        </li>
    </ul>
</div>

<!-- Main Content Area with Topbar -->
<div class="admin-main-content">
    <!-- Topbar -->
    <div class="admin-topbar">
        <h3 class="page-title">
            <i class="fas <?php 
                echo $page_title == 'index' ? 'fa-tachometer-alt' : 
                    ($page_title == 'vehicles' ? 'fa-car' : 
                    ($page_title == 'bookings' ? 'fa-calendar-alt' : 
                    ($page_title == 'users' ? 'fa-users' : 
                    ($page_title == 'testimonials' ? 'fa-star' : 
                    ($page_title == 'contacts' ? 'fa-envelope' : 
                    ($page_title == 'reports' ? 'fa-chart-bar' : 'fa-cog')))))); 
            ?> text-danger me-2"></i> <?php echo $current_title; ?>
        </h3>
        <div class="topbar-right">
            <div class="notification-icon">
                <i class="fas fa-bell fa-lg"></i>
                <?php if($pendingBookings > 0): ?>
                <span class="notification-badge"><?php echo $pendingBookings; ?></span>
                <?php endif; ?>
            </div>
            <div class="admin-dropdown">
                <div class="d-flex align-items-center gap-2">
                    <i class="fas fa-user-circle fa-2x text-danger"></i>
                    <span class="fw-semibold"><?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
                    <i class="fas fa-chevron-down fa-sm"></i>
                </div>
                <div class="admin-dropdown-menu">
                    <a href="../logout.php"><i class="fas fa-sign-out-alt me-2 text-danger"></i> Logout</a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Content wrapper - individual pages will put their content here -->
    <div class="content-wrapper" style="padding: 20px 30px;">