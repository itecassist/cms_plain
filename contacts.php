<?php
/**
 * Contacts Page
 */
require_once 'config.php';
require_once 'functions.php';

$current_page = basename(__FILE__);

include 'includes/header.php';
?>

<!-- =============== S-CONTACTS =============== -->
<section class="s-contacts">
	<div class="container">
		<h2 class="title-decor" data-editable="contact-title"><?php echo get_content($current_page, 'contact-title', 'Get In <span>Touch</span>'); ?></h2>
		<p class="slogan" data-editable="contact-description"><?php echo get_content($current_page, 'contact-description', 'Have questions? We would love to hear from you.'); ?></p>
		
		<div class="row" style="margin-top: 40px;">
			<div class="col-md-6">
				<h3 data-editable="contact-info-title"><?php echo get_content($current_page, 'contact-info-title', 'Contact Information'); ?></h3>
				<div data-editable="contact-address">
					<?php echo get_content($current_page, 'contact-address', '<p><strong>Address:</strong><br>123 Fitness Street, Gym City, ST 12345</p>'); ?>
				</div>
				<div data-editable="contact-phone">
					<?php echo get_content($current_page, 'contact-phone', '<p><strong>Phone:</strong><br>1-800-488-6040</p>'); ?>
				</div>
				<div data-editable="contact-email">
					<?php echo get_content($current_page, 'contact-email', '<p><strong>Email:</strong><br>info@fitmax.com</p>'); ?>
				</div>
				<div data-editable="contact-hours">
					<?php echo get_content($current_page, 'contact-hours', '<p><strong>Hours:</strong><br>Mon - Fri: 8:00AM - 7:00PM<br>Sat - Sun: Closed</p>'); ?>
				</div>
			</div>
			<div class="col-md-6">
				<h3 data-editable="contact-form-title"><?php echo get_content($current_page, 'contact-form-title', 'Send Us A Message'); ?></h3>
				<div data-editable="contact-form-note">
					<?php echo get_content($current_page, 'contact-form-note', '<p>Note: Contact form functionality requires additional setup. This is a placeholder for your contact form.</p>'); ?>
				</div>
			</div>
		</div>
	</div>
</section>
<!-- =============== S-CONTACTS END =============== -->

<?php
include 'includes/footer.php';
?>