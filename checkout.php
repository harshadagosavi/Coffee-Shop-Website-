<?php include 'includes/header.php'; ?>
<?php include 'config.php'; ?>

<?php
if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$sql = "SELECT c.*, p.name, p.price FROM cart c JOIN products p ON c.product_id = p.id WHERE c.user_id = $user_id";
$result = $conn->query($sql);
$total = 0;
while($row = $result->fetch_assoc()) {
    $total += $row['price'] * $row['quantity'];
}
?>

<style>
    .checkout-container {
        max-width: 1200px;
        margin: 40px auto;
        padding: 0 20px;
    }
    
    .order-summary {
        background: white;
        border-radius: 15px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        padding: 25px;
        margin-bottom: 30px;
    }
    
    .payment-methods {
        background: white;
        border-radius: 15px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        padding: 25px;
    }
    
    .payment-option {
        border: 2px solid #e0e0e0;
        border-radius: 12px;
        padding: 15px;
        margin-bottom: 15px;
        cursor: pointer;
        transition: all 0.3s;
        position: relative;
    }
    
    .payment-option:hover {
        border-color: #c7a17b;
        background: #fefaf7;
    }
    
    .payment-option.selected {
        border-color: #5a3e2b;
        background: #fefaf7;
    }
    
    .payment-option input[type="radio"] {
        margin-right: 12px;
        transform: scale(1.2);
    }
    
    .payment-option label {
        cursor: pointer;
        font-weight: 500;
        color: #333;
        margin: 0;
    }
    
    .payment-icon {
        width: 40px;
        height: 40px;
        background: #f5f5f5;
        border-radius: 10px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin-right: 15px;
    }
    
    .payment-icon i {
        font-size: 1.3rem;
        color: #5a3e2b;
    }
    
    .card-details-form {
        margin-top: 20px;
        padding: 20px;
        background: #f8f9fa;
        border-radius: 12px;
        display: none;
        animation: slideDown 0.3s ease;
    }
    
    .card-details-form.show {
        display: block;
    }
    
    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .form-group {
        margin-bottom: 15px;
    }
    
    .form-group label {
        font-weight: 500;
        margin-bottom: 5px;
        display: block;
        color: #555;
    }
    
    .form-group input {
        width: 100%;
        padding: 10px 15px;
        border: 2px solid #e0e0e0;
        border-radius: 8px;
        transition: all 0.3s;
    }
    
    .form-group input:focus {
        border-color: #c7a17b;
        outline: none;
        box-shadow: 0 0 0 3px rgba(199, 161, 123, 0.1);
    }
    
    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 15px;
    }
    
    .pay-btn {
        background: #5a3e2b;
        color: white;
        border: none;
        padding: 14px;
        width: 100%;
        border-radius: 8px;
        font-weight: 600;
        font-size: 1rem;
        cursor: pointer;
        transition: all 0.3s;
        margin-top: 20px;
    }
    
    .pay-btn:hover {
        background: #c7a17b;
        transform: translateY(-2px);
    }
    
    .pay-btn:disabled {
        background: #ccc;
        cursor: not-allowed;
        transform: none;
    }
    
    .order-item {
        display: flex;
        justify-content: space-between;
        padding: 10px 0;
        border-bottom: 1px solid #f0f0f0;
    }
    
    .total-row {
        font-size: 1.2rem;
        font-weight: bold;
        color: #5a3e2b;
        margin-top: 15px;
        padding-top: 15px;
        border-top: 2px solid #e0e0e0;
    }
    
    .alert {
        padding: 12px 20px;
        border-radius: 8px;
        margin-bottom: 20px;
    }
    
    .alert-error {
        background: #fee;
        color: #c33;
        border: 1px solid #fcc;
    }
    
    .alert-success {
        background: #efe;
        color: #3c3;
        border: 1px solid #cfc;
    }
    
    .popup-message {
        position: fixed;
        top: 20px;
        right: 20px;
        background: #dc3545;
        color: white;
        padding: 12px 20px;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        z-index: 9999;
        animation: slideInRight 0.3s ease;
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 14px;
    }
    
    @keyframes slideInRight {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    .popup-message i {
        font-size: 18px;
    }
</style>

<div class="checkout-container">
    <h1 class="mb-4" style="font-family: 'Playfair Display', serif; color: #5a3e2b;">Checkout</h1>
    
    <div class="row">
        <div class="col-md-7">
            <div class="payment-methods">
                <h3 class="mb-4" style="color: #5a3e2b;">Select Payment Method</h3>
                
                <?php if(isset($_GET['error'])): ?>
                    <div class="alert alert-error">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        <?php echo htmlspecialchars($_GET['error']); ?>
                    </div>
                <?php endif; ?>
                
                <form action="process_payment.php" method="POST" id="paymentForm">
                    <input type="hidden" name="total" value="<?php echo $total; ?>">
                    
                    <!-- Payment Options -->
                    <div class="payment-option" onclick="selectPayment('debit')">
                        <div class="d-flex align-items-center">
                            <div class="payment-icon">
                                <i class="fas fa-credit-card"></i>
                            </div>
                            <input type="radio" name="payment_method" value="debit" id="debit" required>
                            <label for="debit" class="mb-0">Debit Card</label>
                        </div>
                    </div>
                    
                    <div class="payment-option" onclick="selectPayment('credit')">
                        <div class="d-flex align-items-center">
                            <div class="payment-icon">
                                <i class="fas fa-credit-card"></i>
                            </div>
                            <input type="radio" name="payment_method" value="credit" id="credit">
                            <label for="credit" class="mb-0">Credit Card</label>
                        </div>
                    </div>
                    
                    <div class="payment-option" onclick="selectPayment('upi')">
                        <div class="d-flex align-items-center">
                            <div class="payment-icon">
                                <i class="fas fa-mobile-alt"></i>
                            </div>
                            <input type="radio" name="payment_method" value="upi" id="upi">
                            <label for="upi" class="mb-0">UPI / Online Payment</label>
                        </div>
                    </div>
                    
                    <div class="payment-option" onclick="selectPayment('cod')">
                        <div class="d-flex align-items-center">
                            <div class="payment-icon">
                                <i class="fas fa-money-bill-wave"></i>
                            </div>
                            <input type="radio" name="payment_method" value="cod" id="cod">
                            <label for="cod" class="mb-0">Cash on Delivery</label>
                        </div>
                    </div>
                    
                    <!-- Card Details Form -->
                    <div id="cardForm" class="card-details-form">
                        <h5 class="mb-3">Card Details</h5>
                        <div class="form-group">
                            <label>Card Holder Name</label>
                            <input type="text" id="card_name" placeholder="Enter name as on card" autocomplete="off">
                        </div>
                        <div class="form-group">
                            <label>Card Number</label>
                            <input type="text" id="card_number" placeholder="1234 5678 9012 3456" maxlength="19" autocomplete="off">
                            <small class="text-muted">16-digit card number</small>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label>Expiry Date</label>
                                <input type="text" id="expiry_date" placeholder="MM/YY" maxlength="5" autocomplete="off">
                            </div>
                            <div class="form-group">
                                <label>CVV</label>
                                <input type="password" id="cvv" placeholder="123" maxlength="3" autocomplete="off">
                            </div>
                        </div>
                        <div id="cardError" class="alert alert-error" style="display: none; margin-top: 10px;"></div>
                    </div>
                    
                    <!-- UPI Form -->
                    <div id="upiForm" class="card-details-form">
                        <h5 class="mb-3">UPI Payment</h5>
                        <div class="form-group">
                            <label>UPI ID</label>
                            <input type="text" id="upi_id" placeholder="username@upi" autocomplete="off">
                        </div>
                        <small class="text-muted">Enter your UPI ID to pay via Google Pay, PhonePe, etc.</small>
                        <div id="upiError" class="alert alert-error" style="display: none; margin-top: 10px;"></div>
                    </div>
                    
                    <button type="submit" class="pay-btn" id="payBtn">
                        <i class="fas fa-rupee-sign me-2"></i>Pay ₹<?php echo number_format($total, 2); ?>
                    </button>
                </form>
            </div>
        </div>
        
        <div class="col-md-5">
            <div class="order-summary">
                <h3 class="mb-3" style="color: #5a3e2b;">Order Summary</h3>
                <?php
                $result->data_seek(0);
                while($row = $result->fetch_assoc()):
                    $subtotal = $row['price'] * $row['quantity'];
                ?>
                <div class="order-item">
                    <span><?php echo htmlspecialchars($row['name']); ?> <span class="text-muted">(x<?php echo $row['quantity']; ?>)</span></span>
                    <span>₹<?php echo number_format($subtotal, 2); ?></span>
                </div>
                <?php endwhile; ?>
                <div class="order-item total-row">
                    <strong>Total Amount</strong>
                    <strong>₹<?php echo number_format($total, 2); ?></strong>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Function to show popup message
function showPopupMessage(message) {
    // Remove any existing popup
    const existingPopup = document.querySelector('.popup-message');
    if (existingPopup) {
        existingPopup.remove();
    }
    
    // Create popup element
    const popup = document.createElement('div');
    popup.className = 'popup-message';
    popup.innerHTML = '<i class="fas fa-exclamation-circle"></i> ' + message;
    document.body.appendChild(popup);
    
    // Auto remove after 3 seconds
    setTimeout(function() {
        if (popup) {
            popup.style.animation = 'slideInRight 0.3s ease reverse';
            setTimeout(function() {
                popup.remove();
            }, 300);
        }
    }, 3000);
}

// Real-time UPI validation
const upiInput = document.getElementById('upi_id');
if (upiInput) {
    upiInput.addEventListener('input', function(e) {
        const upiId = this.value.trim();
        const upiError = document.getElementById('upiError');
        
        if (upiId.length > 0) {
            // Validate UPI format in real-time
            if (!upiId.includes('@')) {
                upiError.textContent = '❌ UPI ID must contain "@" symbol (e.g., username@upi)';
                upiError.style.display = 'block';
            } else {
                const parts = upiId.split('@');
                const username = parts[0];
                const bank = parts[1];
                
                if (parts.length !== 2) {
                    upiError.textContent = '❌ Invalid UPI format. Use: username@bankname';
                    upiError.style.display = 'block';
                } else if (username.length === 0) {
                    upiError.textContent = '❌ Please enter username before "@" (e.g., username@upi)';
                    upiError.style.display = 'block';
                } else if (bank.length === 0) {
                    upiError.textContent = '❌ Please enter bank name after "@" (e.g., username@bankname)';
                    upiError.style.display = 'block';
                } else if (username.length < 3) {
                    upiError.textContent = '❌ Username should be at least 3 characters long';
                    upiError.style.display = 'block';
                } else if (!/^[a-zA-Z0-9._-]+$/.test(username)) {
                    upiError.textContent = '❌ Username can only contain letters, numbers, dots, underscores, and hyphens';
                    upiError.style.display = 'block';
                } else if (!/^[a-zA-Z0-9.-]+$/.test(bank)) {
                    upiError.textContent = '❌ Bank name should contain only letters, numbers, dots, or hyphens';
                    upiError.style.display = 'block';
                } else {
                    upiError.textContent = '✓ Valid UPI ID format';
                    upiError.style.background = '#efe';
                    upiError.style.color = '#3c3';
                    upiError.style.border = '1px solid #cfc';
                }
            }
        } else {
            upiError.style.display = 'none';
        }
    });
    
    // Blur event for final validation
    upiInput.addEventListener('blur', function(e) {
        const upiId = this.value.trim();
        const upiError = document.getElementById('upiError');
        
        if (upiId.length > 0) {
            if (!upiId.includes('@')) {
                upiError.textContent = '❌ UPI ID must contain "@" symbol (e.g., username@upi)';
                upiError.style.display = 'block';
                showPopupMessage('Invalid UPI ID: Missing "@" symbol');
            } else {
                const parts = upiId.split('@');
                const username = parts[0];
                const bank = parts[1];
                
                if (parts.length !== 2) {
                    upiError.textContent = '❌ Invalid UPI format. Use: username@bankname';
                    upiError.style.display = 'block';
                    showPopupMessage('Invalid UPI format. Example: username@bankname');
                } else if (username.length === 0) {
                    upiError.textContent = '❌ Please enter username before "@"';
                    upiError.style.display = 'block';
                    showPopupMessage('Please enter username before "@"');
                } else if (bank.length === 0) {
                    upiError.textContent = '❌ Please enter bank name after "@"';
                    upiError.style.display = 'block';
                    showPopupMessage('Please enter bank name after "@"');
                } else if (username.length < 3) {
                    upiError.textContent = '❌ Username should be at least 3 characters long';
                    upiError.style.display = 'block';
                    showPopupMessage('Username should be at least 3 characters long');
                } else if (!/^[a-zA-Z0-9._-]+$/.test(username)) {
                    upiError.textContent = '❌ Username contains invalid characters';
                    upiError.style.display = 'block';
                    showPopupMessage('Username can only contain letters, numbers, dots, underscores, and hyphens');
                } else if (!/^[a-zA-Z0-9.-]+$/.test(bank)) {
                    upiError.textContent = '❌ Bank name contains invalid characters';
                    upiError.style.display = 'block';
                    showPopupMessage('Bank name should contain only letters, numbers, dots, or hyphens');
                }
            }
        }
    });
}

let selectedPayment = null;

function selectPayment(method) {
    selectedPayment = method;
    
    // Update radio buttons
    document.getElementById(method).checked = true;
    
    // Remove selected class from all options
    document.querySelectorAll('.payment-option').forEach(opt => {
        opt.classList.remove('selected');
    });
    
    // Add selected class to clicked option
    event.currentTarget.classList.add('selected');
    
    // Hide all forms
    document.getElementById('cardForm').classList.remove('show');
    document.getElementById('upiForm').classList.remove('show');
    
    // Show appropriate form
    if (method === 'debit' || method === 'credit') {
        document.getElementById('cardForm').classList.add('show');
        document.getElementById('upiForm').classList.remove('show');
    } else if (method === 'upi') {
        document.getElementById('upiForm').classList.add('show');
        document.getElementById('cardForm').classList.remove('show');
    } else {
        document.getElementById('cardForm').classList.remove('show');
        document.getElementById('upiForm').classList.remove('show');
    }
}

// Format card number with spaces
document.getElementById('card_number')?.addEventListener('input', function(e) {
    let value = this.value.replace(/\D/g, '');
    if (value.length > 16) value = value.slice(0, 16);
    
    // Add spaces every 4 digits
    let formatted = '';
    for (let i = 0; i < value.length; i++) {
        if (i > 0 && i % 4 === 0) formatted += ' ';
        formatted += value[i];
    }
    this.value = formatted;
});

// Format expiry date
document.getElementById('expiry_date')?.addEventListener('input', function(e) {
    let value = this.value.replace(/\D/g, '');
    if (value.length > 4) value = value.slice(0, 4);
    
    if (value.length >= 2) {
        let month = value.slice(0, 2);
        let year = value.slice(2);
        if (parseInt(month) > 12) month = '12';
        this.value = month + (year ? '/' + year : '');
    } else {
        this.value = value;
    }
});

// Limit CVV to 3 digits
document.getElementById('cvv')?.addEventListener('input', function(e) {
    this.value = this.value.replace(/\D/g, '').slice(0, 3);
});

// Form validation before submission
document.getElementById('paymentForm').addEventListener('submit', function(e) {
    const paymentMethod = document.querySelector('input[name="payment_method"]:checked');
    
    if (!paymentMethod) {
        e.preventDefault();
        showPopupMessage('Please select a payment method');
        return false;
    }
    
    const method = paymentMethod.value;
    
    if (method === 'debit' || method === 'credit') {
        const cardName = document.getElementById('card_name').value.trim();
        const cardNumber = document.getElementById('card_number').value.replace(/\s/g, '');
        const expiry = document.getElementById('expiry_date').value;
        const cvv = document.getElementById('cvv').value;
        const cardError = document.getElementById('cardError');
        
        if (!cardName) {
            e.preventDefault();
            cardError.textContent = 'Please enter card holder name';
            cardError.style.display = 'block';
            showPopupMessage('Please enter card holder name');
            return false;
        }
        
        if (cardNumber.length !== 16) {
            e.preventDefault();
            cardError.textContent = 'Please enter a valid 16-digit card number';
            cardError.style.display = 'block';
            showPopupMessage('Please enter a valid 16-digit card number');
            return false;
        }
        
        if (!expiry.match(/^(0[1-9]|1[0-2])\/\d{2}$/)) {
            e.preventDefault();
            cardError.textContent = 'Please enter valid expiry date (MM/YY)';
            cardError.style.display = 'block';
            showPopupMessage('Please enter valid expiry date (MM/YY)');
            return false;
        }
        
        // Validate expiry date is not in the past
        const [month, year] = expiry.split('/');
        const currentDate = new Date();
        const currentYear = currentDate.getFullYear() % 100;
        const currentMonth = currentDate.getMonth() + 1;
        
        if (parseInt(year) < currentYear || (parseInt(year) === currentYear && parseInt(month) < currentMonth)) {
            e.preventDefault();
            cardError.textContent = 'Card has expired. Please use a valid card.';
            cardError.style.display = 'block';
            showPopupMessage('Card has expired. Please use a valid card.');
            return false;
        }
        
        if (cvv.length !== 3) {
            e.preventDefault();
            cardError.textContent = 'Please enter a valid 3-digit CVV';
            cardError.style.display = 'block';
            showPopupMessage('Please enter a valid 3-digit CVV');
            return false;
        }
        
        cardError.style.display = 'none';
        
        // Add card details to form as hidden fields
        const cardNameInput = document.createElement('input');
        cardNameInput.type = 'hidden';
        cardNameInput.name = 'card_name';
        cardNameInput.value = cardName;
        this.appendChild(cardNameInput);
        
        const cardNumberInput = document.createElement('input');
        cardNumberInput.type = 'hidden';
        cardNumberInput.name = 'card_number';
        cardNumberInput.value = cardNumber;
        this.appendChild(cardNumberInput);
        
        const cardExpiryInput = document.createElement('input');
        cardExpiryInput.type = 'hidden';
        cardExpiryInput.name = 'card_expiry';
        cardExpiryInput.value = expiry;
        this.appendChild(cardExpiryInput);
        
        const cardCvvInput = document.createElement('input');
        cardCvvInput.type = 'hidden';
        cardCvvInput.name = 'card_cvv';
        cardCvvInput.value = cvv;
        this.appendChild(cardCvvInput);
        
    } else if (method === 'upi') {
        const upiId = document.getElementById('upi_id').value.trim();
        const upiError = document.getElementById('upiError');
        
        if (!upiId) {
            e.preventDefault();
            upiError.textContent = 'Please enter UPI ID';
            upiError.style.display = 'block';
            showPopupMessage('Please enter UPI ID');
            return false;
        }
        
        if (!upiId.includes('@')) {
            e.preventDefault();
            upiError.textContent = 'Please enter a valid UPI ID (e.g., username@upi)';
            upiError.style.display = 'block';
            showPopupMessage('Invalid UPI ID: Missing "@" symbol. Format: username@bankname');
            return false;
        }
        
        const parts = upiId.split('@');
        const username = parts[0];
        const bank = parts[1];
        
        if (parts.length !== 2) {
            e.preventDefault();
            upiError.textContent = 'Invalid UPI format. Use: username@bankname';
            upiError.style.display = 'block';
            showPopupMessage('Invalid UPI format. Example: username@bankname');
            return false;
        }
        
        if (username.length === 0) {
            e.preventDefault();
            upiError.textContent = 'Please enter username before "@"';
            upiError.style.display = 'block';
            showPopupMessage('Please enter username before "@"');
            return false;
        }
        
        if (bank.length === 0) {
            e.preventDefault();
            upiError.textContent = 'Please enter bank name after "@"';
            upiError.style.display = 'block';
            showPopupMessage('Please enter bank name after "@"');
            return false;
        }
        
        if (username.length < 3) {
            e.preventDefault();
            upiError.textContent = 'Username should be at least 3 characters long';
            upiError.style.display = 'block';
            showPopupMessage('Username should be at least 3 characters long');
            return false;
        }
        
        if (!/^[a-zA-Z0-9._-]+$/.test(username)) {
            e.preventDefault();
            upiError.textContent = 'Username can only contain letters, numbers, dots, underscores, and hyphens';
            upiError.style.display = 'block';
            showPopupMessage('Username can only contain letters, numbers, dots, underscores, and hyphens');
            return false;
        }
        
        if (!/^[a-zA-Z0-9.-]+$/.test(bank)) {
            e.preventDefault();
            upiError.textContent = 'Bank name should contain only letters, numbers, dots, or hyphens';
            upiError.style.display = 'block';
            showPopupMessage('Bank name should contain only letters, numbers, dots, or hyphens');
            return false;
        }
        
        upiError.style.display = 'none';
        
        const upiInput = document.createElement('input');
        upiInput.type = 'hidden';
        upiInput.name = 'upi_id';
        upiInput.value = upiId;
        this.appendChild(upiInput);
    }
    
    // Disable button to prevent double submission
    const submitBtn = document.querySelector('.pay-btn');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Processing...';
    
    return true;
});
</script>

<?php include 'includes/footer.php'; ?>