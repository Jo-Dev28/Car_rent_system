<?php
// contact.php
include 'includes/config.php';
include 'includes/header.php';

$success = '';
$error = '';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = sanitize($_POST['name']);
    $email = sanitize($_POST['email']);
    $subject = sanitize($_POST['subject']);
    $message = sanitize($_POST['message']);
    
    $stmt = $pdo->prepare("INSERT INTO contacts (name, email, subject, message) VALUES (?, ?, ?, ?)");
    if($stmt->execute([$name, $email, $subject, $message])) {
        $success = 'Message sent successfully! We\'ll get back to you soon.';
    } else {
        $error = 'Failed to send message. Please try again.';
    }
}
?>

<section class="contact-section py-5">
    <div class="container">
        <div class="section-header text-center mb-5" data-aos="fade-up">
            <span class="badge bg-danger px-3 py-2">Contact Us</span>
            <h2 class="display-4 fw-bold mt-3">Get In <span class="text-danger">Touch</span></h2>
            <p class="text-muted">We're here to help and answer any questions you might have</p>
        </div>
        
        <div class="row">
            <div class="col-lg-4" data-aos="fade-right">
                <div class="contact-info">
                    <div class="contact-card">
                        <i class="fas fa-map-marker-alt fa-2x text-danger"></i>
                        <h4>Visit Us</h4>
                        <p>123 Luxury Avenue,<br>Nairobi, Kenya</p>
                    </div>
                    <div class="contact-card">
                        <i class="fas fa-phone fa-2x text-danger"></i>
                        <h4>Call Us</h4>
                        <p>+254 700 123 456<br>+254 721 987 654</p>
                    </div>
                    <div class="contact-card">
                        <i class="fas fa-envelope fa-2x text-danger"></i>
                        <h4>Email Us</h4>
                        <p>info@velocityrentals.com<br>support@velocityrentals.com</p>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-8" data-aos="fade-left">
                <div class="contact-form-card">
                    <?php if($success): ?>
                        <div class="alert alert-success"><?php echo $success; ?></div>
                    <?php endif; ?>
                    <?php if($error): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>
                    
                    <form method="POST" action="">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <input type="text" name="name" class="form-control" placeholder="Your Name" required>
                            </div>
                            <div class="col-md-6">
                                <input type="email" name="email" class="form-control" placeholder="Your Email" required>
                            </div>
                            <div class="col-12">
                                <input type="text" name="subject" class="form-control" placeholder="Subject" required>
                            </div>
                            <div class="col-12">
                                <textarea name="message" class="form-control" rows="6" placeholder="Your Message" required></textarea>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-danger btn-lg px-5">Send Message <i class="fas fa-paper-plane"></i></button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>