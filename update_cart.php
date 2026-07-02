<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $cart_id = $_POST['cart_id'];
    $quantity = $_POST['quantity'];

    if ($quantity > 0) {
        $sql = "UPDATE cart SET quantity = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $quantity, $cart_id);
        $stmt->execute();
    } else {
        $sql = "DELETE FROM cart WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $cart_id);
        $stmt->execute();
    }

    header("Location: cart.php");
    exit();
}
?>
