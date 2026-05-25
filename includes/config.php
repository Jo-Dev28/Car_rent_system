<?php
// includes/config.php
session_start();

$host = 'localhost';
$dbname = 'car_rental_db';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

define('SITE_NAME', 'Velocity Rentals');
define('SITE_TAGLINE', 'Drive Your Dream');

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function isAdmin() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] == 'admin';
}

function redirect($url) {
    header("Location: $url");
    exit();
}

function sanitize($input) {
    return htmlspecialchars(strip_tags(trim($input)));
}

function generateBookingNo() {
    return 'VEL-' . strtoupper(uniqid()) . '-' . rand(1000, 9999);
}
?>