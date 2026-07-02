</div>
    <footer class="footer py-5 mt-5">
        <div class="container">
            <div class="row g-4">
                <div class="col-md-4">
                    <h5 class="mb-3 footer-heading">
                        <i class="fas fa-mug-hot me-2"></i>Coffee Shop
                    </h5>
                    <p class="footer-text">Your favorite place for coffee and desserts. Experience the perfect blend of taste and aroma.</p>
        
                </div>
                
                <div class="col-md-4">
                    <h5 class="mb-3 footer-heading">
                        <i class="fas fa-link me-2"></i>Quick Links
                    </h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="index.php" class="footer-link"><i class="fas fa-home me-2"></i>Home</a></li>
                        <li class="mb-2"><a href="menu.php" class="footer-link"><i class="fas fa-coffee me-2"></i>Menu</a></li>
                        <li class="mb-2"><a href="about.php" class="footer-link"><i class="fas fa-info-circle me-2"></i>About</a></li>
                        <li class="mb-2"><a href="contact.php" class="footer-link"><i class="fas fa-envelope me-2"></i>Contact</a></li>
                        <?php if(isset($_SESSION['user_id'])): ?>
                            <li class="mb-2"><a href="profile.php" class="footer-link"><i class="fas fa-user me-2"></i>My Profile</a></li>
                        <?php endif; ?>
                    </ul>
                </div>
                
                <div class="col-md-4">
                    <h5 class="mb-3 footer-heading">
                        <i class="fa fa-phone  me-2"></i>Contact Info
                    </h5>
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <i class="fas fa-map-marker-alt footer-icon me-2"></i>
                            <span class="footer-text">123 Coffee Street, City, State, PIN</span>
                        </li>
                        <li class="mb-2">
                            <i class="fa fa-phone footer-icon me-2"></i>
                            <a href="tel:+918849184854" class="footer-link">+91 8849184854</a>
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-envelope footer-icon me-2"></i>
                            <a href="mailto:harshadagosavi006@gmail.com" class="footer-link">harshadagosavi006@gmail.com</a>
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-clock footer-icon me-2"></i>
                            <span class="footer-text">Mon-Sun: 8:00 AM - 10:00 PM</span>
                        </li>
                    </ul>
                </div>
            </div>
            
            <hr class="my-4 footer-divider">
            
            <div class="row align-items-center">
                <div class="col-md-6 text-center text-md-start">
                    <p class="mb-0 footer-copyright">&copy; <?php echo date('Y'); ?> Coffee Shop. All rights reserved.</p>
                </div>
                <div class="col-md-6 text-center text-md-end">
                    <p class="mb-0 footer-badges">
                        <i class="fas fa-credit-card me-1"></i>Secure Payments | 
                        <i class="fas fa-truck me-1"></i>Fast Delivery
                    </p>
                </div>
            </div>
        </div>
    </footer>
    
    <style>
        .footer {
            background: #2C1810;
            color: #fff;
        }
        
        .footer-heading {
            color: #C7A17B;
            font-family: 'Playfair Display', serif;
            font-weight: 600;
            font-size: 1.3rem;
            margin-bottom: 20px;
        }
        
        .footer-text {
            color: #D4B48C;
            font-family: 'Poppins', sans-serif;
            font-size: 0.95rem;
            line-height: 1.6;
        }
        
        .footer-link {
            color: #D4B48C;
            text-decoration: none;
            font-family: 'Poppins', sans-serif;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            display: inline-block;
        }
        
        .footer-link:hover {
            color: #C7A17B;
            transform: translateX(5px);
        }
        
        .footer-icon {
            color: #C7A17B;
            font-size: 1rem;
        }
        
        .social-icon {
            color: #D4B48C;
            font-size: 1.2rem;
            transition: all 0.3s ease;
            display: inline-block;
        }
        
        .social-icon:hover {
            color: #C7A17B;
            transform: translateY(-3px);
        }
        
        .footer-divider {
            border-color: rgba(199, 161, 123, 0.3);
        }
        
        .footer-copyright {
            color: #D4B48C;
            font-family: 'Poppins', sans-serif;
            font-size: 0.9rem;
        }
        
        .footer-badges {
            color: #D4B48C;
            font-family: 'Poppins', sans-serif;
            font-size: 0.9rem;
        }
        
        @media (max-width: 768px) {
            .footer {
                text-align: center;
            }
            
            .footer-link:hover {
                transform: translateX(0);
            }
            
            .footer-heading {
                font-size: 1.2rem;
            }
        }
    </style>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://kit.fontawesome.com/your-fontawesome-kit.js" crossorigin="anonymous"></script>
    <script src="js/script.js"></script>
</body>
</html>