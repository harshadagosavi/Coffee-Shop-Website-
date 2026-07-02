<?php include 'includes/header.php'; ?>
<?php include 'config.php'; ?>

<?php
if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$order_id = $_GET['order_id'] ?? 0;
$user_id = $_SESSION['user_id'];

$sql = "SELECT * FROM orders WHERE id = ? AND user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $order_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    header("Location: index.php");
    exit();
}

$order = $result->fetch_assoc();
?>

<style>
    .success-container {
        max-width: 600px;
        margin: 60px auto;
        text-align: center;
        padding: 0 20px;
    }
    
    .success-card {
        background: white;
        border-radius: 20px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        padding: 40px;
        animation: fadeInUp 0.5s ease;
    }
    
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .success-icon {
        width: 80px;
        height: 80px;
        background: #4caf50;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 20px;
        animation: scaleIn 0.5s ease 0.2s both;
    }
    
    @keyframes scaleIn {
        from {
            transform: scale(0);
        }
        to {
            transform: scale(1);
        }
    }
    
    .success-icon i {
        font-size: 3rem;
        color: white;
    }
    
    .order-details {
        background: #f8f9fa;
        border-radius: 12px;
        padding: 20px;
        margin: 20px 0;
        text-align: left;
    }
    
    .order-details p {
        margin: 10px 0;
        display: flex;
        justify-content: space-between;
    }
    
    .action-buttons {
        display: flex;
        gap: 15px;
        justify-content: center;
        margin-top: 25px;
    }
    
    .btn {
        padding: 12px 25px;
        border-radius: 8px;
        text-decoration: none;
        transition: all 0.3s;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    
    .btn-primary {
        background: #5a3e2b;
        color: white;
    }
    
    .btn-primary:hover {
        background: #c7a17b;
        transform: translateY(-2px);
    }
    
    .btn-secondary {
        background: #6c757d;
        color: white;
    }
    
    .btn-secondary:hover {
        background: #5a6268;
        transform: translateY(-2px);
    }
    
    .btn-outline {
        border: 2px solid #5a3e2b;
        color: #5a3e2b;
        background: transparent;
    }
    
    .btn-outline:hover {
        background: #5a3e2b;
        color: white;
    }
</style>

<div class="success-container">
    <div class="success-card">
        <div class="success-icon">
            <i class="fas fa-check"></i>
        </div>
        
        <h1 style="color: #4caf50; margin-bottom: 10px;">Payment Successful!</h1>
        <p class="text-muted">Thank you for your order. Your delicious coffee is being prepared!</p>
        
        <div class="order-details">
            <p><strong>Order ID:</strong> <span>#<?php echo $order['id']; ?></span></p>
            <p><strong>Order Date:</strong> <span><?php echo date('d M Y, h:i A', strtotime($order['order_date'])); ?></span></p>
            <p><strong>Payment Method:</strong> <span><?php echo ucfirst($order['payment_method']); ?></span></p>
            <p><strong>Total Amount:</strong> <span style="color: #5a3e2b; font-size: 1.2rem;">₹<?php echo number_format($order['total'], 2); ?></span></p>
        </div>
        
        <p>We'll send you a confirmation email shortly with your order details.</p>
        
        <div class="action-buttons">
            <a href="profile.php" class="btn btn-primary">
                <i class="fas fa-shopping-bag"></i> View Orders
            </a>
            <a href="download_invoice.php?order_id=<?php echo $order['id']; ?>" class="btn btn-outline">
                <i class="fas fa-download"></i> Download Invoice
            </a>
            <a href="menu.php" class="btn btn-secondary">
                <i class="fas fa-coffee"></i> Order More
            </a>
        </div>
    </div>
</div>

<script>
    // Optional: Play success sound or animation
    setTimeout(function() {
        // You can add additional tracking or analytics here
        console.log('Order completed: #<?php echo $order['id']; ?>');
    }, 1000);
</script>

<?php include 'includes/footer.php'; ?>