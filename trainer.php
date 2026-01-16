<?php
/**
 * Trainers Page
 */
require_once 'config.php';
require_once 'functions.php';

$current_page = basename(__FILE__);

include 'includes/header.php';
?>

<!-- =============== S-TRAINERS =============== -->
<section class="s-trainers" style="padding: 80px 0;">
	<div class="container">
		<h2 class="title-decor" data-editable="trainers-title"><?php echo get_content($current_page, 'trainers-title', 'Our <span>Trainers</span>'); ?></h2>
		<p class="slogan" data-editable="trainers-description"><?php echo get_content($current_page, 'trainers-description', 'Meet our professional team of certified trainers.'); ?></p>
		
		<div data-editable="trainers-content">
			<?php echo get_content($current_page, 'trainers-content', '<div class="row" style="margin-top: 40px;"><div class="col-md-12"><p>Our trainers content goes here. Add your trainer profiles and information.</p></div></div>'); ?>
		</div>
	</div>
</section>
<!-- =============== S-TRAINERS END =============== -->

<?php
include 'includes/footer.php';
?>