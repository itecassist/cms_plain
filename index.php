<?php
/**
 * Homepage
 */
require_once 'config.php';
require_once 'functions.php';

// Get current page name for content
$current_page = basename(__FILE__);

// Include header
include 'includes/header.php';

// Include sections
include 'sections/hero-slider.php';
include 'sections/services-grid.php';
include 'sections/programs-grid.php';
include 'sections/about-section.php';

// Include footer
include 'includes/footer.php';
?>
