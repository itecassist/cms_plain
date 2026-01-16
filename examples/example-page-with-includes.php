<?php
/**
 * Example page using header and footer includes
 * Save this as a template (e.g., template/example-page.php)
 */

// Include CMS files
require_once 'config.php';
require_once 'functions.php';

// Get current page name for content
$current_page = basename(__FILE__);

// Include header
include 'includes/header.php';
?>

<!-- Your page content here -->
<section class="s-crossfit-slider">
	<div class="crossfit-slider">
		<div class="crossfit-slide">
			<div class="crossfit-slide-bg" style="background-image: url(assets/img/slide-1.jpg);"></div>
			<div class="container">
				<div class="crossfit-slide-cover">
					<h2 class="title" data-editable="hero-title"><?php echo get_content($current_page, 'hero-title', 'push <span>yourself</span>'); ?></h2>
					<p data-editable="hero-description"><?php echo get_content($current_page, 'hero-description', 'Maecenas consequat ex id lobortis venenatis. Mauris id erat enim.'); ?></p>
				</div>
			</div>
		</div>
	</div>
</section>

<section class="s-crossfit">
	<div class="container">
		<h2 class="title-decor" data-editable="welcome-title"><?php echo get_content($current_page, 'welcome-title', 'Welcome To <span>Crossfit</span>'); ?></h2>
		<p class="slogan" data-editable="welcome-slogan"><?php echo get_content($current_page, 'welcome-slogan', 'Maecenas consequat ex id lobortis venenatis.'); ?></p>
		
		<div class="row">
			<div class="col-md-4 crossfit-col">
				<div class="crossfit-item">
					<img class="rx-lazy" src="assets/img/placeholder-all.png" data-src="assets/img/serv-1.svg" alt="img">
					<h3 data-editable="service1-title"><?php echo get_content($current_page, 'service1-title', 'body building'); ?></h3>
					<p data-editable="service1-description"><?php echo get_content($current_page, 'service1-description', 'Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium.'); ?></p>
					<a class="btn" href="program.html.htm">view Schedule</a>
				</div>
			</div>
			<div class="col-md-4 crossfit-col">
				<div class="crossfit-item">
					<img class="rx-lazy" src="assets/img/placeholder-all.png" data-src="assets/img/serv-2.svg" alt="img">
					<h3 data-editable="service2-title"><?php echo get_content($current_page, 'service2-title', 'group workouts'); ?></h3>
					<p data-editable="service2-description"><?php echo get_content($current_page, 'service2-description', 'Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium.'); ?></p>
					<a class="btn" href="program.html.htm">view Schedule</a>
				</div>
			</div>
			<div class="col-md-4 crossfit-col">
				<div class="crossfit-item">
					<img class="rx-lazy" src="assets/img/placeholder-all.png" data-src="assets/img/serv-3.svg" alt="img">
					<h3 data-editable="service3-title"><?php echo get_content($current_page, 'service3-title', 'boxing'); ?></h3>
					<p data-editable="service3-description"><?php echo get_content($current_page, 'service3-description', 'Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium.'); ?></p>
					<a class="btn" href="program.html.htm">view Schedule</a>
				</div>
			</div>
		</div>
	</div>
</section>

<?php
// Include footer
include 'includes/footer.php';
?>
