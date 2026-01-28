<?php
/**
 * Contact Form Component
 */

$form_submitted = false;
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['contact_submit'])) {
    $name = $_POST['contact_name'] ?? '';
    $email = $_POST['contact_email'] ?? '';
    $message = $_POST['contact_message'] ?? '';
    
    // Basic validation
    if (empty($name) || empty($email) || empty($message)) {
        $error_message = 'Please fill in all fields.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = 'Please enter a valid email address.';
    } else {
        // Here you would typically send an email or save to database
        // For now, just show success
        $form_submitted = true;
    }
}
?>
<div>Contact Us</div>
<div class="contact-form-component">
    <?php if ($form_submitted): ?>
        <div class="alert alert-success">
            <strong>Thank you!</strong> Your message has been sent successfully.
        </div>
    <?php else: ?>
        <?php if ($error_message): ?>
            <div class="alert alert-error">
                <?php echo htmlspecialchars($error_message); ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" class="contact-form">
            <div class="form-group">
                <label for="contact_name">Name</label>
                <input type="text" id="contact_name" name="contact_name" class="form-control" required>
            </div>
            
            <div class="form-group">
                <label for="contact_email">Email</label>
                <input type="email" id="contact_email" name="contact_email" class="form-control" required>
            </div>
            
            <div class="form-group">
                <label for="contact_message">Message</label>
                <textarea id="contact_message" name="contact_message" class="form-control" rows="5" required></textarea>
            </div>
            
            <button type="submit" name="contact_submit" class="btn btn-primary">Send Message</button>
        </form>
    <?php endif; ?>
</div>

<script>
// Optional: Add form validation or AJAX submission
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('.contact-form');
    if (form) {
        form.addEventListener('submit', function(e) {
            // You can add custom validation or AJAX here
            console.log('Form submitted');
        });
    }
});
</script>
