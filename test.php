<?php
require_once 'config.php';
require_once 'functions.php';
$current_page = basename(__FILE__);

include 'includes/header.php';

// Include only the sections you want
include 'sections/hero-slider.php';
include 'sections/services-grid.php';

include 'includes/footer.php';
?>