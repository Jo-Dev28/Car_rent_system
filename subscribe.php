<?php
// subscribe.php
include 'includes/config.php';

$email = sanitize($_POST['email']);

$stmt = $pdo->prepare("INSERT INTO subscribers (email) VALUES (?) ON DUPLICATE KEY UPDATE subscribed_at = NOW()");
$stmt->execute([$email]);

$_SESSION['subscribe_msg'] = 'Thank you for subscribing!';
header("Location: " . $_SERVER['HTTP_REFERER']);
exit();
?>