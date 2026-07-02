<?php
session_start();
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    
    // Here you would typically save to a newsletter table
    // For now, we'll just redirect back with a success message
    
    // Optional: Save to database if you have a newsletter table
    /*
    $sql = "INSERT INTO newsletter (email) VALUES (?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    */
    
    // Redirect back to homepage with success message
    header("Location: index.php?newsletter=success");
    exit();
}
?>