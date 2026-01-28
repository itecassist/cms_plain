<?php
/**
 * Contacts Page
 */
require_once 'config.php';
require_once 'functions.php';

$current_page = basename(__FILE__);

// Load SEO data
$seo_data = get_seo_data($current_page);

// Load page content
$content_file = CONTENT_DIR . '/' . sanitize_filename($current_page) . '.json';
$page_content = '';
if (file_exists($content_file)) {
    $content_data = json_decode(file_get_contents($content_file), true) ?? [];
    $page_content = $content_data['content'] ?? '';
}

include 'includes/header.php';
?>

<?php if (!empty($page_content)): ?>
	<?php echo process_components($page_content); ?>
<?php else: ?>
	<!-- =============== S-CONTACTS =============== -->
	<section class="s-contacts">
		<div class="container">
			<h2 class="title-decor">Get In <span>Touch</span></h2>
			<p class="slogan">Have questions? We would love to hear from you.</p>
			
			<div class="row" style="margin-top: 40px;">
				<div class="col-md-6">
					<h3>Contact Information</h3>
					<p><strong>Address:</strong><br>123 Fitness Street, Gym City, ST 12345</p>
					<p><strong>Phone:</strong><br>1-800-488-6040</p>
					<p><strong>Email:</strong><br>info@fitmax.com</p>
					<p><strong>Hours:</strong><br>Mon - Fri: 8:00AM - 7:00PM<br>Sat - Sun: Closed</p>
				</div>
				<div class="col-md-6">
					<h3>Send Us A Message</h3>
					<p>Note: Contact form functionality requires additional setup. This is a placeholder for your contact form.</p>
				</div>
			</div>
		</div>
	</section>
	<!-- =============== S-CONTACTS END =============== -->
<?php endif; ?>

<?php
include 'includes/footer.php';
?>