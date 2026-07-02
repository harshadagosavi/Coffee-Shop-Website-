<?php include 'includes/header.php'; ?>

<style>
/* ===== CONTACT PAGE - COMPACT & COLORFUL ===== */
.contact-page {
    background: linear-gradient(135deg, #f5efe9 0%, #e8d9cc 100%);
    padding: 40px 0;
    min-height: 100vh;
    font-family: 'Poppins', sans-serif;
}

.contact-container {
    max-width: 1100px;
    margin: 0 auto;
    padding: 0 20px;
}

/* Header */
.contact-header {
    text-align: center;
    margin-bottom: 30px;
}

.contact-header h1 {
    font-family: 'Playfair Display', serif;
    font-size: 2.5rem;
    color: #5a3e2b;
    margin-bottom: 5px;
}

.contact-header p {
    color: #7b5e47;
    font-size: 1rem;
}

/* Main Grid - 2 Columns */
.contact-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 25px;
}

/* ===== LEFT SIDE - FORM ===== */
.form-card {
    background: #ffffffd9;
    backdrop-filter: blur(10px);
    padding: 30px;
    border-radius: 20px;
    box-shadow: 0 15px 30px rgba(90, 62, 43, 0.15);
    border: 1px solid #d4b59e;
}

.form-card h2 {
    font-family: 'Playfair Display', serif;
    font-size: 1.8rem;
    color: #5a3e2b;
    margin-bottom: 5px;
}

.form-sub {
    color: #8b6f55;
    font-size: 0.9rem;
    margin-bottom: 20px;
}

.input-group {
    margin-bottom: 15px;
}

.input-group input,
.input-group textarea {
    width: 100%;
    padding: 12px 15px;
    border: 2px solid #e8d9cc;
    border-radius: 12px;
    font-size: 0.95rem;
    transition: all 0.3s;
    background: #fefaf7;
}

.input-group input:focus,
.input-group textarea:focus {
    border-color: #c7a17b;
    outline: none;
    box-shadow: 0 5px 15px rgba(199, 161, 123, 0.2);
}

.input-group textarea {
    height: 100px;
    resize: none;
}

.send-btn {
    background: #5a3e2b;
    color: white;
    border: none;
    padding: 14px;
    width: 100%;
    border-radius: 12px;
    font-weight: 600;
    font-size: 1rem;
    cursor: pointer;
    transition: all 0.3s;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
}

.send-btn:hover {
    background: #c7a17b;
    transform: translateY(-2px);
}

/* ===== RIGHT SIDE - INFO ===== */
.info-card {
    background: #5a3e2b;
    padding: 30px;
    border-radius: 20px;
    color: white;
    height: 100%;
    display: flex;
    flex-direction: column;
    box-shadow: 0 15px 30px rgba(90, 62, 43, 0.3);
}

.info-card h3 {
    font-family: 'Playfair Display', serif;
    font-size: 1.8rem;
    margin-bottom: 5px;
    color: #ffead2;
}

.info-sub {
    color: #d4b59e;
    font-size: 0.9rem;
    margin-bottom: 25px;
}

/* Info Items with Icons */
.info-item {
    display: flex;
    align-items: center;
    gap: 15px;
    margin-bottom: 20px;
    background: rgba(255, 255, 255, 0.1);
    padding: 12px 15px;
    border-radius: 12px;
    backdrop-filter: blur(5px);
}

.info-icon {
    width: 40px;
    height: 40px;
    background: #c7a17b;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
    color: #5a3e2b;
}

.info-text {
    flex: 1;
}

.info-label {
    font-size: 0.7rem;
    text-transform: uppercase;
    letter-spacing: 1px;
    color: #d4b59e;
}

.info-value {
    font-weight: 600;
    font-size: 1rem;
    color: white;
}

.info-value a {
    color: white;
    text-decoration: none;
}

.info-value a:hover {
    color: #c7a17b;
}

/* Hours */
.hours-box {
    background: rgba(199, 161, 123, 0.2);
    border-radius: 12px;
    padding: 15px;
    margin: 15px 0;
}

.hours-row {
    display: flex;
    justify-content: space-between;
    padding: 8px 0;
    border-bottom: 1px dashed #c7a17b;
}

.hours-row:last-child {
    border-bottom: none;
}

.hours-day {
    font-weight: 500;
}

.hours-day i {
    margin-right: 8px;
    color: #c7a17b;
}

.hours-time {
    color: #ffead2;
    font-weight: 600;
}

/* Mini Map */
.mini-map {
    height: 120px;
    background: #c7a17b;
    border-radius: 12px;
    overflow: hidden;
    margin-top: auto;
    border: 3px solid #ffead2;
}

.mini-map iframe {
    width: 100%;
    height: 100%;
    border: none;
}

/* Responsive */
@media (max-width: 768px) {
    .contact-grid {
        grid-template-columns: 1fr;
    }
    
    .contact-header h1 {
        font-size: 2rem;
    }
    
    .form-card, .info-card {
        padding: 25px;
    }
}
</style>

<!-- Add Font Awesome if not already in header -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<div class="contact-page">
    <div class="contact-container">
        
        <!-- Header -->
        <div class="contact-header">
            <h1>☕ Contact Us</h1>
            <p>We'd love to hear from you!</p>
        </div>
        
        <!-- 2 Column Grid -->
        <div class="contact-grid">
            
            <!-- LEFT: Message Form -->
            <div class="form-card">
                <h2>Send Message</h2>
                <div class="form-sub">Fill the form, we'll reply within 24h</div>
                
                <form action="send_message.php" method="POST">
                    <div class="input-group">
                        <input type="text" name="name" placeholder="Your Name *" required>
                    </div>
                    <div class="input-group">
                        <input type="email" name="email" placeholder="Email Address *" required>
                    </div>
                    <div class="input-group">
                        <input type="text" name="subject" placeholder="Subject">
                    </div>
                    <div class="input-group">
                        <textarea name="message" placeholder="Your Message *" required></textarea>
                    </div>
                    <button type="submit" class="send-btn">
                        <i class="fas fa-paper-plane"></i> Send Message
                    </button>
                </form>
            </div>
            
            <!-- RIGHT: Contact Info with Icons -->
            <div class="info-card">
                <h3>Visit Us</h3>
                <div class="info-sub">We're always happy to serve you</div>
                
                <!-- Address with Icon -->
                <div class="info-item">
                    <div class="info-icon">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <div class="info-text">
                        <div class="info-label">ADDRESS</div>
                        <div class="info-value">TCC Coffee, Vesu, Surat, India</div>
                    </div>
                </div>
                
                <!-- Phone with Icon -->
                <div class="info-item">
                    <div class="info-icon">
                        <i class="fa fa-phone alt"></i>
                    </div>
                    <div class="info-text">
                        <div class="info-label">CALL US</div>
                        <div class="info-value">
                            <a href="tel:+91 12345 67890">+91 12345 67890</a>
                        </div>
                    </div>
                </div>
                
                <!-- Email with Icon -->
                <div class="info-item">
                    <div class="info-icon">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <div class="info-text">
                        <div class="info-label">EMAIL</div>
                        <div class="info-value">
                            <a href="mailto:coffeeShop123@gmail.com">coffeeShop123@gmail.com</a>
                        </div>
                    </div>
                </div>
                
                <!-- Hours Box with Icons -->
                <div class="hours-box">
                    <div class="hours-row">
                        <span class="hours-day">
                            <i class="far fa-clock"></i> Mon - Sun
                        </span>
                        <span class="hours-time">8:00 AM - 10:00 PM</span>
                    </div>
                    <div class="hours-row">
                        <span class="hours-day">
                            <i class="far fa-calendar-alt"></i> Holidays
                        </span>
                        <span class="hours-time">8:00 AM - 6:00 PM</span>
                    </div>
                </div>
                
                <!-- Mini Map -->
                <div class="mini-map">
                    <iframe 
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3720.874196952583!2d72.78542431470316!3d21.169248985916!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3be04de6d6b4b6b7%3A0x5b5e5b5e5b5e5b5e!2sVesu%2C%20Surat%2C%20Gujarat!5e0!3m2!1sen!2sin!4v1620000000000!5m2!1sen!2sin"
                        allowfullscreen="" 
                        loading="lazy">
                    </iframe>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>