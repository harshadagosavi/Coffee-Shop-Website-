<?php include 'includes/header.php'; ?>
<?php include 'config.php'; ?>

<?php
if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$sql = "SELECT c.*, p.name, p.price, p.image FROM cart c JOIN products p ON c.product_id = p.id WHERE c.user_id = $user_id";
$result = $conn->query($sql);
$total = 0;
?>

<style>
    .cart-item {
        transition: all 0.3s ease;
        border: 1px solid #e0e0e0;
        border-radius: 12px;
        overflow: hidden;
    }
    
    .cart-item:hover {
        box-shadow: 0 5px 15px rgba(0,0,0,0.08);
    }
    
    .cart-item-img {
        width: 80px;
        height: 80px;
        object-fit: cover;
        border-radius: 8px;
    }
    
    .cart-item-name {
        font-weight: 600;
        color: #333;
        margin-bottom: 5px;
    }
    
    .cart-item-price {
        color: #667eea;
        font-weight: 500;
    }
    
    .quantity-form {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }
    
    .quantity-input {
        width: 70px;
        text-align: center;
        border: 1px solid #ddd;
        border-radius: 6px;
        padding: 6px;
    }
    
    .btn-update {
        background: #667eea;
        color: white;
        border: none;
        padding: 6px 12px;
        border-radius: 6px;
        font-size: 12px;
        transition: background 0.3s;
    }
    
    .btn-update:hover {
        background: #5a67d8;
    }
    
    .btn-remove {
        background: #dc3545;
        color: white;
        border: none;
        padding: 6px 12px;
        border-radius: 6px;
        font-size: 12px;
        transition: background 0.3s;
        cursor: pointer;
    }
    
    .btn-remove:hover {
        background: #c82333;
    }
    
    .subtotal {
        font-weight: 600;
        color: #333;
        font-size: 1.1rem;
    }
    
    .summary-card {
        position: sticky;
        top: 20px;
        border: none;
        border-radius: 12px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        background: white;
    }
    
    .summary-card .card-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 12px 12px 0 0;
        padding: 15px 20px;
    }
    
    .summary-card .card-header h5 {
        margin: 0;
        font-weight: 600;
    }
    
    .summary-card .card-body {
        padding: 20px;
    }
    
    .total-amount {
        font-size: 1.5rem;
        font-weight: 700;
        color: #667eea;
        margin-top: 10px;
        padding-top: 10px;
        border-top: 2px solid #f0f0f0;
    }
    
    .btn-checkout {
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        color: white;
        border: none;
        padding: 12px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 1rem;
        transition: transform 0.2s, box-shadow 0.2s;
        width: 100%;
    }
    
    .btn-checkout:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(40, 167, 69, 0.3);
    }
    
    .empty-cart {
        text-align: center;
        padding: 60px 20px;
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }
    
    .empty-cart i {
        font-size: 4rem;
        color: #ccc;
        margin-bottom: 20px;
    }
    
    .empty-cart h3 {
        color: #333;
        margin-bottom: 10px;
    }
    
    .empty-cart p {
        color: #999;
        margin-bottom: 20px;
    }
    
    .btn-browse {
        background: #667eea;
        color: white;
        border: none;
        padding: 10px 25px;
        border-radius: 25px;
        text-decoration: none;
        display: inline-block;
        transition: background 0.3s;
    }
    
    .btn-browse:hover {
        background: #5a67d8;
        color: white;
    }
    
    @media (max-width: 768px) {
        .cart-item .row {
            text-align: center;
        }
        
        .cart-item-img {
            margin-bottom: 10px;
        }
        
        .quantity-form {
            justify-content: center;
        }
        
        .btn-remove {
            margin-top: 10px;
        }
    }
</style>

<div class="container my-5">
    <h1 class="text-center mb-4" style="color: #333; font-weight: 600;">Your Cart</h1>
    
    <?php if($result->num_rows > 0): ?>
        <div class="row g-4">
            <div class="col-lg-8">
                <?php while($row = $result->fetch_assoc()): 
                    $subtotal = $row['price'] * $row['quantity'];
                    $total += $subtotal;
                ?>
                <div class="cart-item mb-3">
                    <div class="card-body p-3 p-md-4">
                        <div class="row align-items-center">
                            <!-- Product Image -->
                            <div class="col-md-2 col-12 text-center text-md-start mb-3 mb-md-0">
                                <img src="<?php echo htmlspecialchars($row['image']); ?>" alt="<?php echo htmlspecialchars($row['name']); ?>" class="cart-item-img">
                            </div>
                            
                            <!-- Product Info -->
                            <div class="col-md-3 col-12 text-center text-md-start mb-3 mb-md-0">
                                <h5 class="cart-item-name"><?php echo htmlspecialchars($row['name']); ?></h5>
                                <p class="cart-item-price mb-0">₹<?php echo number_format($row['price'], 2); ?></p>
                            </div>
                            
                            <!-- Quantity Update Form -->
                            <div class="col-md-3 col-12 text-center text-md-start mb-3 mb-md-0">
                                <form action="update_cart.php" method="POST" class="quantity-form">
                                    <input type="hidden" name="cart_id" value="<?php echo $row['id']; ?>">
                                    <input type="number" name="quantity" value="<?php echo $row['quantity']; ?>" min="1" class="quantity-input">
                                    <button type="submit" class="btn-update">
                                        <i class="fas fa-sync-alt me-1"></i>Update
                                    </button>
                                </form>
                            </div>
                            
                            <!-- Subtotal -->
                            <div class="col-md-2 col-6 text-center text-md-start mb-3 mb-md-0">
                                <p class="subtotal mb-0">₹<?php echo number_format($subtotal, 2); ?></p>
                            </div>
                            
                            <!-- Remove Button -->
                            <div class="col-md-2 col-6 text-center text-md-end">
                                <form action="remove_from_cart.php" method="POST" onsubmit="return confirm('Remove this item from cart?');">
                                    <input type="hidden" name="cart_id" value="<?php echo $row['id']; ?>">
                                    <button type="submit" class="btn-remove">
                                        <i class="fas fa-trash-alt me-1"></i>Remove
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
            
            <!-- Order Summary Sidebar -->
            <div class="col-lg-4">
                <div class="summary-card">
                    <div class="card-header">
                        <h5><i class="fas fa-shopping-cart me-2"></i>Order Summary</h5>
                    </div>
                    <div class="card-body">
                        <div class="total-amount d-flex justify-content-between">
                            <strong>Total Amount:</strong>
                            <strong>₹<?php echo number_format($total, 2); ?></strong>
                        </div>
                        <hr>
                        <a href="checkout.php" class="btn-checkout">
                            <i class="fas fa-credit-card me-2"></i>Proceed to Checkout
                        </a>
                    </div>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="empty-cart">
            <i class="fas fa-shopping-cart"></i>
            <h3>Your cart is empty</h3>
            <p>Looks like you haven't added any items to your cart yet.</p>
            <a href="menu.php" class="btn-browse">
                <i class="fas fa-coffee me-2"></i>Browse Our Menu
            </a>
        </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>