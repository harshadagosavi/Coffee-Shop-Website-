<?php
session_start();
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $subject = mysqli_real_escape_string($conn, $_POST['subject'] ?? '');
    $message = mysqli_real_escape_string($conn, $_POST['message']);
    
    // Get user_id if logged in
    $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'NULL';
    
    // Insert into database
    $sql = "INSERT INTO messages (user_id, name, email, subject, message) VALUES (?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("issss", $user_id, $name, $email, $subject, $message);
    
    if ($stmt->execute()) {
        // Redirect with success
        header("Location: contact.php?success=Message sent successfully! We'll get back to you soon.");
    } else {
        header("Location: contact.php?error=Failed to send message. Please try again.");
    }
    exit();
}
?>