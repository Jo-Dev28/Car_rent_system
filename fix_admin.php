<?php
// fix_admin.php - Run this once to fix admin login
include 'includes/config.php';

echo "<h1>Admin Account Fixer</h1>";

// Check if admin exists
$stmt = $pdo->prepare("SELECT * FROM admins WHERE email = ?");
$stmt->execute(['admin@velocityrentals.com']);
$admin = $stmt->fetch();

if($admin) {
    echo "<p>Admin account found!</p>";
    echo "<p>Current password hash: " . $admin['password'] . "</p>";
    
    // Verify if password hash is correct for 'admin123'
    if(password_verify('admin123', $admin['password'])) {
        echo "<p style='color:green'>✓ Password 'admin123' is correct!</p>";
    } else {
        echo "<p style='color:red'>✗ Password hash doesn't match 'admin123'. Updating...</p>";
        $new_hash = password_hash('admin123', PASSWORD_DEFAULT);
        $update = $pdo->prepare("UPDATE admins SET password = ? WHERE email = ?");
        $update->execute([$new_hash, 'admin@velocityrentals.com']);
        echo "<p style='color:green'>✓ Password has been reset to 'admin123'</p>";
    }
} else {
    echo "<p>Admin account not found. Creating new admin account...</p>";
    $hashed_password = password_hash('admin123', PASSWORD_DEFAULT);
    $insert = $pdo->prepare("INSERT INTO admins (username, email, password, role) VALUES (?, ?, ?, ?)");
    $insert->execute(['admin', 'admin@velocityrentals.com', $hashed_password, 'super_admin']);
    echo "<p style='color:green'>✓ Admin account created! Email: admin@velocityrentals.com, Password: admin123</p>";
}

// Display all admins
echo "<h2>Current Admin Accounts:</h2>";
$admins = $pdo->query("SELECT id, username, email, role FROM admins")->fetchAll();
echo "<table border='1' cellpadding='10'>";
echo "<tr><th>ID</th><th>Username</th><th>Email</th><th>Role</th></tr>";
foreach($admins as $admin) {
    echo "<tr>";
    echo "<td>{$admin['id']}</td>";
    echo "<td>{$admin['username']}</td>";
    echo "<td>{$admin['email']}</td>";
    echo "<td>{$admin['role']}</td>";
    echo "</tr>";
}
echo "</table>";

echo "<p><a href='login.php'>Go to Login Page</a></p>";
?>