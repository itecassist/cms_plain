<?php
/**
 * Blog/News Page
 */
require_once 'config.php';
require_once 'functions.php';

$current_page = basename(__FILE__);

include 'includes/header.php';
?>

<!-- =============== S-BLOG =============== -->
<section class="s-blog" style="padding: 80px 0;">
	<div class="container">
		<h2 class="title-decor" data-editable="blog-title"><?php echo get_content($current_page, 'blog-title', 'Latest <span>News</span>'); ?></h2>
		<p class="slogan" data-editable="blog-description"><?php echo get_content($current_page, 'blog-description', 'Stay updated with our latest fitness tips and news.'); ?></p>
		
		<div data-editable="blog-content">
			<?php echo get_content($current_page, 'blog-content', '<div class="row" style="margin-top: 40px;"><div class="col-md-12"><p>Blog posts will be displayed here. Add your news and updates.</p></div></div>'); ?>
		</div>
	</div>
</section>
<!-- =============== S-BLOG END =============== -->

<?php
include 'includes/footer.php';
?>