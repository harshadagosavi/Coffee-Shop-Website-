<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'coffee_shop');

// Admin credentials (hard-coded)
define('ADMIN_EMAIL', 'admin@coffeeShop.com');
define('ADMIN_PASSWORD', 'Admin@182');

// Create database connection using MySQLi
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
