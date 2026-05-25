<?php
// includes/footer.php
?>
<!-- Footer -->
<footer class="footer-section">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-4">
                <div class="footer-brand">
                    <i class="fas fa-car-side fa-2x text-danger"></i>
                    <h3 class="d-inline-block ms-2">AUTO<span class="text-danger">MOBILE</span></h3>
                </div>
                <p class="mt-3">Experience the thrill of driving premium luxury vehicles. Your satisfaction is our priority.</p>
                <div class="social-links">
                    <a href="#"><i class="fab fa-facebook-f"></i></a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                    <a href="#"><i class="fab fa-twitter"></i></a>
                    <a href="#"><i class="fab fa-linkedin-in"></i></a>
                </div>
            </div>
            <div class="col-lg-2">
                <h5>Quick Links</h5>
                <ul class="footer-links">
                    <li><a href="index.php">Home</a></li>
                    <li><a href="cars.php">Our Fleet</a></li>
                    <li><a href="#how-it-works">How It Works</a></li>
                    <li><a href="contact.php">Contact Us</a></li>
                </ul>
            </div>
            <div class="col-lg-3">
                <h5>Contact Info</h5>
                <ul class="footer-links">
                    <li><i class="fas fa-map-marker-alt"></i> 123 Luxury Avenue, NY 10001</li>
                    <li><i class="fas fa-phone"></i> +1 (555) 123-4567</li>
                    <li><i class="fas fa-envelope"></i> info@velocityrentals.com</li>
                </ul>
            </div>
            <div class="col-lg-3">
                <h5>Newsletter</h5>
                <p>Subscribe for exclusive offers</p>
                <form action="subscribe.php" method="POST" class="newsletter-form">
                    <div class="input-group">
                        <input type="email" name="email" class="form-control" placeholder="Your email" required>
                        <button class="btn btn-danger" type="submit"><i class="fas fa-paper-plane"></i></button>
                    </div>
                </form>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2024 Velocity Rentals. All rights reserved.</p>
        </div>
    </div>
</footer>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script src="assets/js/main.js"></script>
<script>
    AOS.init({
        duration: 800,
        once: true,
        offset: 100
    });
</script>
</body>
</html>