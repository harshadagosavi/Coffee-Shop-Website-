<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id'];
    $payment_method = $_POST['payment_method'];
    $total = $_POST['total'];

    // Validate payment method
    $valid_methods = ['debit', 'credit', 'upi', 'cod'];
    if (!in_array($payment_method, $valid_methods)) {
        header("Location: checkout.php?error=Invalid payment method selected");
        exit();
    }

    // Validate card details if applicable
    if (in_array($payment_method, ['debit', 'credit'])) {
        $card_name = $_POST['card_name'] ?? '';
        $card_number = $_POST['card_number'] ?? '';
        $card_expiry = $_POST['card_expiry'] ?? '';
        $card_cvv = $_POST['card_cvv'] ?? '';
        
        // Basic validation (in production, you'd use a payment gateway)
        if (empty($card_name) || strlen($card_number) != 16 || strlen($card_cvv) != 3) {
            header("Location: checkout.php?error=Invalid card details");
            exit();
        }
        
        // Validate expiry date
        if (!preg_match('/^(0[1-9]|1[0-2])\/\d{2}$/', $card_expiry)) {
            header("Location: checkout.php?error=Invalid expiry date format");
            exit();
        }
        
        // Check if card is expired
        list($month, $year) = explode('/', $card_expiry);
        $current_year = date('y');
        $current_month = date('m');
        
        if ($year < $current_year || ($year == $current_year && $month < $current_month)) {
            header("Location: checkout.php?error=Card has expired");
            exit();
        }
        
        // In production, you would integrate with a payment gateway here
        // For demo purposes, we'll simulate successful payment
    }
    
    if ($payment_method == 'upi') {
        $upi_id = $_POST['upi_id'] ?? '';
        if (empty($upi_id) || !strpos($upi_id, '@')) {
            header("Location: checkout.php?error=Invalid UPI ID");
            exit();
        }
        // Simulate UPI payment processing
    }
    
    if ($payment_method == 'cod') {
        // No validation needed for COD
    }

    // Calculate total from cart to verify
    $sql = "SELECT SUM(p.price * c.quantity) as total FROM cart c 
            JOIN products p ON c.product_id = p.id 
            WHERE c.user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $cart_total = $row['total'];
    
    if ($cart_total != $total) {
        header("Location: checkout.php?error=Cart total mismatch. Please try again.");
        exit();
    }

    // Start transaction
    $conn->begin_transaction();
    
    try {
        // Insert order
        $sql = "INSERT INTO orders (user_id, total, payment_method, status) VALUES (?, ?, ?, 'pending')";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ids", $user_id, $total, $payment_method);
        $stmt->execute();
        $order_id = $conn->insert_id;

        // Insert order items
        $sql = "SELECT c.product_id, c.quantity, p.price FROM cart c 
                JOIN products p ON c.product_id = p.id 
                WHERE c.user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $sql = "INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)";
            $stmt2 = $conn->prepare($sql);
            $stmt2->bind_param("iiid", $order_id, $row['product_id'], $row['quantity'], $row['price']);
            $stmt2->execute();
        }

        // Clear cart
        $sql = "DELETE FROM cart WHERE user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();

        // Commit transaction
        $conn->commit();
        
        // Store order ID in session for success page
        $_SESSION['last_order_id'] = $order_id;
        
        // Redirect to success page with order ID
        header("Location: order_success.php?order_id=" . $order_id);
        exit();
        
    } catch (Exception $e) {
        $conn->rollback();
        header("Location: checkout.php?error=Payment failed. Please try again.");
        exit();
    }
}
?>