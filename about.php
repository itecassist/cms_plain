<?php
/**
 * About Page
 */
require_once 'config.php';
require_once 'functions.php';

$current_page = basename(__FILE__);

include 'includes/header.php';

// Page sections
include 'sections/about-section.php';
include 'sections/cta-section.php';

include 'includes/footer.php';
?>