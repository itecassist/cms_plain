<?php
/**
 * Programs Page
 */
require_once 'config.php';
require_once 'functions.php';

$current_page = basename(__FILE__);

include 'includes/header.php';

// Page sections
include 'sections/programs-grid.php';
include 'sections/cta-section.php';

include 'includes/footer.php';
?>