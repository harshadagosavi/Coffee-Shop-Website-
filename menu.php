<?php include 'includes/header.php'; ?>
<?php include 'config.php'; ?>
<link rel="stylesheet" href="css/menu.css">
<!-- Add custom CSS for menu images -->
<style>
    .menu-card {
        height: 100%;
        transition: transform 0.3s ease;
    }
    
    .menu-card:hover {
        transform: translateY(-5px);
    }
    
    .menu-image-container {
        height: 200px; /* Fixed height for image container */
        width: 100%;
        overflow: hidden;
        position: relative;
    }
    
    .menu-image {
        width: 100%;
        height: 100%;
        object-fit: cover; /* This ensures images cover the container without distortion */
        object-position: center;
        transition: transform 0.3s ease;
    }
    
    .menu-card:hover .menu-image {
        transform: scale(1.05);
    }
    
    .card-body {
        display: flex;
        flex-direction: column;
    }
    
    .card-text {
        flex-grow: 1;
    }
    
    /* Search and filter improvements */
    #search {
        border-radius: 25px;
        padding: 12px 20px;
        border: 2px solid #e2e8f0;
    }
    
    #category {
        border-radius: 25px;
        padding: 12px 20px;
        border: 2px solid #e2e8f0;
    }
    
    /* Responsive adjustments */
    @media (max-width: 768px) {
        .menu-image-container {
            height: 180px;
        }
    }
    
    @media (max-width: 576px) {
        .menu-image-container {
            height: 160px;
        }
        
        .menu-card {
            margin-bottom: 20px;
        }
    }
    
    /* Price styling */
    .price-tag {
        color: #667eea;
        font-weight: 600;
        font-size: 1.2rem;
    }
    
    /* Badge for categories */
    .category-badge {
        position: absolute;
        top: 10px;
        right: 10px;
        background: rgba(102, 126, 234, 0.9);
        color: white;
        padding: 5px 10px;
        border-radius: 20px;
        font-size: 0.8rem;
        z-index: 2;
    }
    
    /* Toast notification styles */
    .toast-notification {
        position: fixed;
        top: 20px;
        right: 20px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 12px 20px;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        z-index: 9999;
        display: flex;
        align-items: center;
        gap: 10px;
        animation: slideInRight 0.3s ease;
        font-size: 14px;
        font-weight: 500;
        min-width: 250px;
        max-width: 350px;
    }
    
    .toast-notification i {
        font-size: 18px;
    }
    
    .toast-notification.success {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    }
    
    .toast-notification.error {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
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
    
    @keyframes slideOutRight {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }
    
    .toast-notification.fade-out {
        animation: slideOutRight 0.3s ease forwards;
    }
    
    /* Button animation */
    .btn-success {
        transition: all 0.3s ease;
    }
    
    .btn-success:active {
        transform: scale(0.95);
    }
</style>

<div class="container my-5">
    <h1 class="text-center mb-4">Our Menu</h1>
    
    <!-- Search and Filter -->
    <div class="row mb-4">
        <div class="col-md-6 mb-3">
            <div class="input-group">
                
                <input type="text" id="search" class="form-control border-start-0" placeholder="Search products...">
            </div>
        </div>
        <div class="col-md-6">
            <select id="category" class="form-select">
                <option value="">All Categories</option>
                <option value="coffee">Coffee</option>
                <option value="dessert">Dessert</option>
                <option value="cake">Cake</option>
            </select>
        </div>
    </div>
    
    <div class="row" id="products">
        <?php
        $sql = "SELECT * FROM products";
        $result = $conn->query($sql);
        while($row = $result->fetch_assoc()):
        ?>
        <div class="col-lg-4 col-md-6 mb-4 product-item" data-category="<?php echo $row['category']; ?>">
            <div class="card menu-card h-100 border-0 shadow-sm">
                <div class="menu-image-container">
                    <img src="<?php echo $row['image']; ?>" class="menu-image" alt="<?php echo $row['name']; ?>">
                    <span class="category-badge"><?php echo ucfirst($row['category']); ?></span>
                </div>
                <div class="card-body">
                    <h5 class="card-title"><?php echo $row['name']; ?></h5>
                    <p class="card-text text-muted"><?php echo $row['description']; ?></p>
                    <div class="d-flex justify-content-between align-items-center mt-auto">
                        <p class="price-tag mb-0">₹<?php echo $row['price']; ?></p>
                        <?php if(isset($_SESSION['user_id'])): ?>
                            <button class="btn btn-success add-to-cart-btn" 
                                    data-product-id="<?php echo $row['id']; ?>" 
                                    data-product-name="<?php echo $row['name']; ?>">
                                <i class="fas fa-cart-plus me-1"></i> Add to Cart
                            </button>
                        <?php else: ?>
                            <a href="login.php" class="btn btn-outline-success">
                                <i class="fas fa-sign-in-alt me-1"></i>Order Now 
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php endwhile; ?>
    </div>
    
    <!-- Empty state for no results -->
    <div id="no-results" class="text-center d-none">
        <i class="fas fa-search fa-3x text-muted mb-3"></i>
        <h4>No products found</h4>
        <p class="text-muted">Try adjusting your search or filter to find what you're looking for.</p>
    </div>
</div>

<script>
// Enhanced search and filter functionality
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('search');
    const categorySelect = document.getElementById('category');
    const products = document.querySelectorAll('.product-item');
    const noResults = document.getElementById('no-results');
    
    // Show toast notification function
    function showToast(message, type = 'success') {
        // Remove any existing toast
        const existingToast = document.querySelector('.toast-notification');
        if (existingToast) {
            existingToast.remove();
        }
        
        // Create toast element
        const toast = document.createElement('div');
        toast.className = `toast-notification ${type}`;
        
        // Add icon based on type
        const icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';
        toast.innerHTML = `
            <i class="fas ${icon}"></i>
            <span>${message}</span>
        `;
        
        // Add to body
        document.body.appendChild(toast);
        
        // Auto remove after 3 seconds
        setTimeout(() => {
            toast.classList.add('fade-out');
            setTimeout(() => {
                if (toast.parentNode) {
                    toast.remove();
                }
            }, 300);
        }, 3000);
    }
    
    // Handle add to cart functionality
    const addToCartBtns = document.querySelectorAll('.add-to-cart-btn');
    
    addToCartBtns.forEach(btn => {
        btn.addEventListener('click', async function(e) {
            e.preventDefault();
            
            const productId = this.getAttribute('data-product-id');
            const productName = this.getAttribute('data-product-name');
            const originalBtnText = this.innerHTML;
            
            // Show loading state
            this.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Adding...';
            this.disabled = true;
            
            try {
                // Create form data
                const formData = new FormData();
                formData.append('product_id', productId);
                
                // Send AJAX request to add to cart
                const response = await fetch('add_to_cart.php', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.text();
                
                if (response.ok) {
                    // Show success popup message
                    showToast('✓ Product is added to cart successfully!', 'success');
                    
                    // Optional: Update cart count in header if exists
                    const cartCountElement = document.querySelector('.cart-count');
                    if (cartCountElement) {
                        let currentCount = parseInt(cartCountElement.textContent) || 0;
                        cartCountElement.textContent = currentCount + 1;
                    }
                    
                    // Add a subtle animation to the button
                    this.style.transform = 'scale(0.95)';
                    setTimeout(() => {
                        this.style.transform = 'scale(1)';
                    }, 200);
                    
                } else {
                    showToast('Failed to add product. Please try again.', 'error');
                }
            } catch (error) {
                console.error('Error:', error);
                showToast('An error occurred. Please try again.', 'error');
            } finally {
                // Reset button state
                this.innerHTML = originalBtnText;
                this.disabled = false;
            }
        });
    });
    
    function filterProducts() {
        const searchTerm = searchInput.value.toLowerCase();
        const selectedCategory = categorySelect.value;
        
        let visibleCount = 0;
        
        products.forEach(product => {
            const name = product.querySelector('.card-title').textContent.toLowerCase();
            const description = product.querySelector('.card-text').textContent.toLowerCase();
            const category = product.getAttribute('data-category');
            
            const matchesSearch = name.includes(searchTerm) || description.includes(searchTerm);
            const matchesCategory = selectedCategory === '' || category === selectedCategory;
            
            if (matchesSearch && matchesCategory) {
                product.style.display = 'block';
                visibleCount++;
                // Add fade-in animation
                product.style.animation = 'fadeIn 0.5s ease';
            } else {
                product.style.display = 'none';
            }
        });
        
        // Show/hide no results message
        if (visibleCount === 0) {
            noResults.classList.remove('d-none');
        } else {
            noResults.classList.add('d-none');
        }
    }
    
    // Add CSS animation
    const style = document.createElement('style');
    style.textContent = `
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    `;
    document.head.appendChild(style);
    
    // Event listeners
    searchInput.addEventListener('input', filterProducts);
    categorySelect.addEventListener('change', filterProducts);
    
    // Add smooth scrolling for filtered results
    const searchForm = searchInput.closest('.input-group');
    if (searchForm) {
        searchForm.addEventListener('submit', function(e) {
            e.preventDefault();
            filterProducts();
            
            // Scroll to first visible product
            const firstVisible = Array.from(products).find(p => p.style.display !== 'none');
            if (firstVisible) {
                firstVisible.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        });
    }
    
    // Initialize filter on page load
    filterProducts();
});
</script>

<?php include 'includes/footer.php'; ?>