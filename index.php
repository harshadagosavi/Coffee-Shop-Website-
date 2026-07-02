<?php include 'includes/header.php'; ?>
<?php include 'config.php'; ?>

<!-- Add Google Fonts -->
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700;800&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">

<style>
    /* ====== EXACT DESIGN FROM REFERENCE IMAGE ====== */
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }
    
    .coffee-hero {
        position: relative;
        min-height: 80vh;
        background: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), 
                    url('https://images.unsplash.com/photo-1453614512568-c4024d13c247?fm=jpg&q=60&w=3000&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8Mnx8Y29mZmVlJTIwc2hvcHxlbnwwfHwwfHx8MA%3D%3D');
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        padding: 0 20px;
        margin-top: -10px;
    }
    
    .coffee-content {
        max-width: 800px;
        color: white;
    }
    
    .coffee-label {
        font-family: 'Poppins', sans-serif;
        font-size: 1.2rem;
        font-weight: 400;
        letter-spacing: 8px;
        text-transform: uppercase;
        margin-bottom: 20px;
        color: rgba(255, 255, 255, 0.9);
    }
    
    .coffee-title {
        font-family: 'Playfair Display', serif;
        font-size: 5rem;
        font-weight: 700;
        margin-bottom: 25px;
        line-height: 1.2;
        text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
    }
    
    .coffee-subtitle {
        font-family: 'Poppins', sans-serif;
        font-size: 1.2rem;
        font-weight: 300;
        margin-bottom: 40px;
        line-height: 1.6;
        max-width: 600px;
        margin-left: auto;
        margin-right: auto;
        color: rgba(255, 255, 255, 0.8);
    }
    
    .coffee-btn {
        display: inline-block;
        background: transparent;
        color: white;
        border: 2px solid white;
        padding: 16px 48px;
        font-family: 'Poppins', sans-serif;
        font-size: 1.1rem;
        font-weight: 500;
        letter-spacing: 2px;
        text-transform: uppercase;
        text-decoration: none;
        transition: all 0.3s ease;
        margin-bottom: 40px;
    }
    
    .coffee-btn:hover {
        background: white;
        color: #2C1810;
    }
    
    .coffee-footer-text {
        font-family: 'Poppins', sans-serif;
        font-size: 0.9rem;
        font-weight: 300;
        color: rgba(255, 255, 255, 0.6);
        max-width: 400px;
        margin: 0 auto;
        line-height: 1.5;
    }
    
    /* Rest of your existing sections will follow */
    .featured-section {
        padding: 80px 0;
        background: #FAF3E0;
    }
    
    .section-title {
        font-family: 'Playfair Display', serif;
        font-size: 2.5rem;
        font-weight: 700;
        color: #2C1810;
        text-align: center;
        margin-bottom: 50px;
        position: relative;
    }
    
    .section-title:after {
        content: '';
        position: absolute;
        bottom: -15px;
        left: 50%;
        transform: translateX(-50%);
        width: 60px;
        height: 2px;
        background: #C7A17B;
    }
    
    /* Responsive */
    @media (max-width: 768px) {
        .coffee-title {
            font-size: 3.5rem;
        }
        
        .coffee-label {
            font-size: 1rem;
            letter-spacing: 5px;
        }
        
        .coffee-subtitle {
            font-size: 1rem;
            padding: 0 15px;
        }
        
        .coffee-btn {
            padding: 14px 38px;
            font-size: 1rem;
        }
    }
    
    @media (max-width: 576px) {
        .coffee-title {
            font-size: 2.5rem;
        }
        
        .coffee-label {
            font-size: 0.9rem;
            letter-spacing: 4px;
        }
        
        .coffee-footer-text {
            font-size: 0.8rem;
        }
    }
    
    /* Card styling to match aesthetic */
    .product-card {
        background: white;
        border: none;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        transition: transform 0.3s ease;
    }
    
    .product-card:hover {
        transform: translateY(-5px);
    }
    
    .product-image-container {
        height: 200px;
        overflow: hidden;
    }
    
    .product-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .product-body {
        padding: 20px;
        text-align: center;
    }
    
    .product-title {
        font-family: 'Playfair Display', serif;
        font-size: 1.3rem;
        font-weight: 600;
        color: #2C1810;
        margin-bottom: 10px;
    }
    
    .product-price {
        font-family: 'Poppins', sans-serif;
        font-size: 1.1rem;
        color: #C7A17B;
        font-weight: 500;
        margin-bottom: 15px;
    }
    
    .product-btn {
        background: #2C1810;
        color: white;
        border: none;
        padding: 8px 25px;
        font-family: 'Poppins', sans-serif;
        font-size: 0.9rem;
        font-weight: 400;
        letter-spacing: 1px;
        text-transform: uppercase;
        text-decoration: none;
        display: inline-block;
        transition: all 0.3s ease;
    }
    
    .product-btn:hover {
        background: #C7A17B;
        color: #2C1810;
    }
</style>

<!-- Hero Section - Exactly like reference image -->
<div class="coffee-hero">
    <div class="coffee-content">
        <div class="coffee-label">BEST COFFEE</div>
        <h1 class="coffee-title">Make your day great<br>with our coffee!</h1>
        <p class="coffee-subtitle">
            Rise and grind, it's coffee time!
            Just brewed happiness in a cup!
But first, coffee.
        </p>
        <a href="menu.php" class="coffee-btn">ORDER NOW</a>
       
    </div>
</div>

<!-- Featured Products Section -->
<div class="featured-section">
    <div class="container">
        <h2 class="section-title">Our Special Coffee</h2>
        
        <div class="row">
            <?php
            $sql = "SELECT * FROM products WHERE category = 'coffee' LIMIT 3";
            $result = $conn->query($sql);
            if($result->num_rows > 0) {
                while($row = $result->fetch_assoc()):
            ?>
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="product-card">
                    <div class="product-image-container">
                        <img src="<?php echo $row['image']; ?>" class="product-image" alt="<?php echo $row['name']; ?>">
                    </div>
                    <div class="product-body">
                        <h3 class="product-title"><?php echo $row['name']; ?></h3>
                        <p class="product-price">₹<?php echo $row['price']; ?></p>
                       
                    </div>
                </div>
            </div>
            <?php 
                endwhile;
            } else {
                // Fallback if no products in database
            ?>
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="product-card">
                    <div class="product-image-container">
                        <img src="https://images.unsplash.com/photo-1511920170033-f8396924c348?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" class="product-image" alt="Espresso">
                    </div>
                    <div class="product-body">
                        <h3 class="product-title">Espresso</h3>
                        <p class="product-price">₹150</p>
                        <a href="menu.php" class="product-btn">View Details</a>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="product-card">
                    <div class="product-image-container">
                        <img src="https://images.unsplash.com/photo-1572442388796-11668a67e53d?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" class="product-image" alt="Cappuccino">
                    </div>
                    <div class="product-body">
                        <h3 class="product-title">Cappuccino</h3>
                        <p class="product-price">₹200</p>
                        <a href="menu.php" class="product-btn">View Details</a>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="product-card">
                    <div class="product-image-container">
                        <img src="https://images.unsplash.com/photo-1461023058943-07fcbe16d735?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" class="product-image" alt="Latte">
                    </div>
                    <div class="product-body">
                        <h3 class="product-title">Latte</h3>
                        <p class="product-price">₹180</p>
                        <a href="menu.php" class="product-btn">View Details</a>
                    </div>
                </div>
            </div>
            <?php
            }
            ?>
        </div>
    </div>
</div>

<!-- About Section (Minimal) -->
<div class="container my-5">
    <div class="row align-items-center">
        <div class="col-md-6">
            <h2 style="font-family: 'Playfair Display', serif; font-size: 2rem; color: #2C1810; margin-bottom: 20px;">Our Coffee Story</h2>
            <p style="font-family: 'Poppins', sans-serif; font-size: 1rem; color: #666; line-height: 1.8;">
                Established in 2020, Coffee Shop has been serving the finest coffee and desserts to our community.
                 We are passionate about coffee and committed to providing exceptional service.
            </p>
            <a href="about.php" style="font-family: 'Poppins', sans-serif; color: #C7A17B; text-decoration: none; font-weight: 500;">Read More →</a>
        </div>
        <div class="col-md-6">
            <img src="https://images.unsplash.com/photo-1442512595331-e89e73853f31?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" 
                 alt="Coffee Shop" class="img-fluid rounded" style="box-shadow: 0 10px 30px rgba(0,0,0,0.1);">
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>