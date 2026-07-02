<?php include 'includes/header.php'; ?>
<?php include 'config.php'; ?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-0 pt-4">
                    <h3 class="text-center text-dark">Create Account</h3>
                    <p class="text-center text-muted">Join us for the best coffee experience</p>
                </div>
                <div class="card-body p-4">
                    <?php if(isset($_GET['error'])): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?php echo htmlspecialchars($_GET['error']); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    
                    <?php if(isset($_GET['success'])): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <?php echo htmlspecialchars($_GET['success']); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    
                    <form action="register_process.php" method="POST" id="registerForm" novalidate>
                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="fas fa-user text-muted"></i>
                                </span>
                                <input type="text" class="form-control border-start-0" id="username" name="username" 
                                       placeholder="Enter username" required>
                            </div>
                            <div class="invalid-feedback">Username is required</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="fas fa-envelope text-muted"></i>
                                </span>
                                <input type="email" class="form-control border-start-0" id="email" name="email" 
                                       placeholder="Enter email" required>
                            </div>
                            <div class="invalid-feedback">Please enter a valid email address</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="mobile" class="form-label">Mobile Number</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="fas fa-phone text-muted"></i>
                                </span>
                                <input type="tel" class="form-control border-start-0" id="mobile" name="mobile" 
                                       placeholder="Enter 10-digit mobile number" maxlength="10" 
                                       pattern="[0-9]{10}" inputmode="numeric" required>
                            </div>
                            <div class="invalid-feedback" id="mobileError">Please enter a valid 10-digit mobile number</div>
                            <small class="text-muted">Only 10 digits allowed (e.g., 9876543210)</small>
                        </div>
                        
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="fas fa-lock text-muted"></i>
                                </span>
                                <input type="password" class="form-control border-start-0" id="password" name="password" 
                                       placeholder="Create password" required>
                                <button class="btn btn-outline-secondary border-start-0" type="button" onclick="togglePassword('password')">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            <div class="invalid-feedback">Password must be at least 6 characters</div>
                            <small class="text-muted">Password must be at least 6 characters long</small>
                        </div>
                        
                        <div class="mb-4">
                            <label for="confirm_password" class="form-label">Confirm Password</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="fas fa-lock text-muted"></i>
                                </span>
                                <input type="password" class="form-control border-start-0" id="confirm_password" name="confirm_password" 
                                       placeholder="Confirm password" required>
                                <button class="btn btn-outline-secondary border-start-0" type="button" onclick="togglePassword('confirm_password')">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            <div class="invalid-feedback" id="confirmError">Passwords do not match</div>
                        </div>
                        
                        <div class="form-check mb-4">
                            <input class="form-check-input" type="checkbox" id="terms" required>
                            <label class="form-check-label" for="terms">
                                I agree to the <a href="#" class="text-decoration-none">Terms of Service</a> and 
                                <a href="#" class="text-decoration-none">Privacy Policy</a>
                            </label>
                            <div class="invalid-feedback">You must agree to the terms</div>
                        </div>
                        
                        <button type="submit" class="btn btn-primary w-100 py-2">
                            <i class="fas fa-user-plus me-2"></i>Register
                        </button>
                    </form>
                    
                    <div class="text-center mt-4">
                        <p class="mb-0">Already have an account? <a href="login.php" class="text-decoration-none">Login here</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .card {
        border-radius: 15px;
        overflow: hidden;
    }
    
    .input-group-text {
        border-radius: 8px 0 0 8px;
    }
    
    .form-control {
        border-radius: 0 8px 8px 0;
    }
    
    .form-control:focus, .input-group-text:focus {
        box-shadow: none;
        border-color: #dee2e6;
    }
    
    .form-control:focus {
        border-color: #c7a17b;
        box-shadow: 0 0 0 0.2rem rgba(199, 161, 123, 0.25);
    }
    
    .btn-primary {
        background: #5a3e2b;
        border: none;
        border-radius: 8px;
        transition: all 0.3s;
    }
    
    .btn-primary:hover {
        background: #c7a17b;
        transform: translateY(-2px);
    }
    
    .invalid-feedback {
        font-size: 0.875rem;
    }
    
    .form-control.is-invalid {
        border-color: #dc3545;
    }
</style>

<script>
// Toggle password visibility
function togglePassword(fieldId) {
    const field = document.getElementById(fieldId);
    const button = field.nextElementSibling;
    const icon = button.querySelector('i');
    
    if (field.type === 'password') {
        field.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        field.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}

// Mobile number validation
document.getElementById('mobile').addEventListener('input', function(e) {
    let value = this.value;
    
    // Remove any non-digit characters
    value = value.replace(/\D/g, '');
    
    // Limit to 10 digits
    if (value.length > 10) {
        value = value.slice(0, 10);
    }
    
    this.value = value;
    
    // Validate length
    if (value.length === 0) {
        this.classList.remove('is-invalid');
        this.classList.add('is-valid');
    } else if (value.length === 10) {
        this.classList.remove('is-invalid');
        this.classList.add('is-valid');
        document.getElementById('mobileError').style.display = 'none';
    } else {
        this.classList.add('is-invalid');
        this.classList.remove('is-valid');
        document.getElementById('mobileError').innerHTML = 'Please enter a valid 10-digit mobile number';
        document.getElementById('mobileError').style.display = 'block';
    }
});

// Form validation
document.getElementById('registerForm').addEventListener('submit', function(e) {
    let isValid = true;
    
    // Validate username
    const username = document.getElementById('username');
    if (!username.value.trim()) {
        username.classList.add('is-invalid');
        isValid = false;
    } else {
        username.classList.remove('is-invalid');
        username.classList.add('is-valid');
    }
    
    // Validate email
    const email = document.getElementById('email');
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!email.value.trim() || !emailRegex.test(email.value)) {
        email.classList.add('is-invalid');
        isValid = false;
    } else {
        email.classList.remove('is-invalid');
        email.classList.add('is-valid');
    }
    
    // Validate mobile
    const mobile = document.getElementById('mobile');
    if (mobile.value.length !== 10) {
        mobile.classList.add('is-invalid');
        isValid = false;
    } else {
        mobile.classList.remove('is-invalid');
        mobile.classList.add('is-valid');
    }
    
    // Validate password
    const password = document.getElementById('password');
    if (!password.value || password.value.length < 6) {
        password.classList.add('is-invalid');
        isValid = false;
    } else {
        password.classList.remove('is-invalid');
        password.classList.add('is-valid');
    }
    
    // Validate confirm password
    const confirmPassword = document.getElementById('confirm_password');
    if (confirmPassword.value !== password.value) {
        confirmPassword.classList.add('is-invalid');
        document.getElementById('confirmError').style.display = 'block';
        isValid = false;
    } else if (confirmPassword.value) {
        confirmPassword.classList.remove('is-invalid');
        confirmPassword.classList.add('is-valid');
        document.getElementById('confirmError').style.display = 'none';
    }
    
    // Validate terms
    const terms = document.getElementById('terms');
    if (!terms.checked) {
        terms.classList.add('is-invalid');
        isValid = false;
    } else {
        terms.classList.remove('is-invalid');
    }
    
    if (!isValid) {
        e.preventDefault();
    }
});

// Real-time confirm password validation
document.getElementById('confirm_password').addEventListener('input', function() {
    const password = document.getElementById('password').value;
    const confirmError = document.getElementById('confirmError');
    
    if (this.value !== password) {
        this.classList.add('is-invalid');
        confirmError.style.display = 'block';
    } else {
        this.classList.remove('is-invalid');
        this.classList.add('is-valid');
        confirmError.style.display = 'none';
    }
});

// Password validation
document.getElementById('password').addEventListener('input', function() {
    if (this.value.length >= 6) {
        this.classList.remove('is-invalid');
        this.classList.add('is-valid');
    } else if (this.value.length > 0) {
        this.classList.add('is-invalid');
        this.classList.remove('is-valid');
    }
});

// Prevent paste of invalid characters in mobile field
document.getElementById('mobile').addEventListener('paste', function(e) {
    e.preventDefault();
    const pastedText = (e.clipboardData || window.clipboardData).getData('text');
    const digits = pastedText.replace(/\D/g, '').slice(0, 10);
    this.value = digits;
    this.dispatchEvent(new Event('input'));
});
</script>

<?php include 'includes/footer.php'; ?>