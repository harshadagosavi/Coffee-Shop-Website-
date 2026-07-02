<?php
session_start();
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $mobile = trim($_POST['mobile']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Mobile number validation
    if (!preg_match('/^[0-9]{10}$/', $mobile)) {
        header("Location: register.php?error=Please enter a valid 10-digit mobile number");
        exit();
    }

    if (strtolower($username) === 'admin') {
        header("Location: register.php?error=Username 'admin' is reserved");
        exit();
    }

    if ($password !== $confirm_password) {
        header("Location: register.php?error=Passwords do not match");
        exit();
    }

    if (strlen($password) < 6) {
        header("Location: register.php?error=Password must be at least 6 characters");
        exit();
    }

    // Check if email already exists
    $check_email = "SELECT id FROM users WHERE email = ?";
    $stmt = $conn->prepare($check_email);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        header("Location: register.php?error=Email already registered. Please login or use another email.");
        exit();
    }
    
    // Check if mobile already exists
    $check_mobile = "SELECT id FROM users WHERE mobile = ?";
    $stmt = $conn->prepare($check_mobile);
    $stmt->bind_param("s", $mobile);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        header("Location: register.php?error=Mobile number already registered.");
        exit();
    }

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $sql = "INSERT INTO users (username, email, password, mobile) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $username, $email, $hashed_password, $mobile);

    if ($stmt->execute()) {
        $_SESSION['user_id'] = $conn->insert_id;
        $_SESSION['username'] = $username;
        header("Location: index.php?success=Registration successful! Welcome to Coffee Shop!");
        exit();
    } else {
        header("Location: register.php?error=Registration failed. Please try again.");
        exit();
    }
}
?>