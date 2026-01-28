<?php
/**
 * Custom logic for contacts.php
 * 
 * This file is automatically included before the page loads
 * Use this for form handling, sending emails, validations, etc.
 */

// Handle contact form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['contact_submit'])) {
    $name = sanitize_input($_POST['name'] ?? '');
    $email = sanitize_input($_POST['email'] ?? '');
    $phone = sanitize_input($_POST['phone'] ?? '');
    $message = sanitize_input($_POST['message'] ?? '');
    
    // Validate inputs
    $errors = [];
    if (empty($name)) $errors[] = 'Name is required';
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Valid email is required';
    if (empty($message)) $errors[] = 'Message is required';
    
    // If no errors, send email
    if (empty($errors)) {
        $to = 'your-email@example.com'; // Change this to your email
        $subject = 'New Contact Form Submission from ' . $name;
        $body = "Name: $name\nEmail: $email\nPhone: $phone\n\nMessage:\n$message";
        $headers = "From: $email";
        
        if (mail($to, $subject, $body, $headers)) {
            $_SESSION['contact_success'] = 'Thank you! Your message has been sent.';
            header('Location: contacts');
            exit;
        } else {
            $_SESSION['contact_error'] = 'Error sending message. Please try again.';
        }
    } else {
        $_SESSION['contact_errors'] = $errors;
    }
}

/**
 * Helper function to sanitize user input
 */
function sanitize_input($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

// Pass any variables to the page content
$contact_success = $_SESSION['contact_success'] ?? '';
$contact_errors = $_SESSION['contact_errors'] ?? [];

// Clear session messages after displaying
if (isset($_SESSION['contact_success'])) unset($_SESSION['contact_success']);
if (isset($_SESSION['contact_errors'])) unset($_SESSION['contact_errors']);
