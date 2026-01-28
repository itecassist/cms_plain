/**
 * Custom JavaScript for contacts.php
 * 
 * This file is automatically loaded if it exists
 * Use for page-specific functionality like form validation, animations, etc.
 */

document.addEventListener('DOMContentLoaded', function() {
    // Example: Form validation
    const contactForm = document.querySelector('form[name="contact"]');
    
    if (contactForm) {
        contactForm.addEventListener('submit', function(e) {
            // Add custom validation here if needed
            console.log('Contact form submitted');
        });
    }
    
    // Add your custom JavaScript here
    // This code runs only on the contacts page
});
