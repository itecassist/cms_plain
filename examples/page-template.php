<?php
/**
 * Page Template
 * Copy this file to create new pages
 */
require_once 'config.php';
require_once 'functions.php';

$current_page = basename(__FILE__);

// Load SEO data from json/{page}.json
$seo_data = get_seo_data($current_page);

// Load page content from content/{page}.json
$content_file = CONTENT_DIR . '/' . sanitize_filename($current_page) . '.json';
$page_content = '';
if (file_exists($content_file)) {
    $content_data = json_decode(file_get_contents($content_file), true) ?? [];
    $page_content = $content_data['content'] ?? '';
}

include 'includes/header.php';
?>

<?php if (!empty($page_content)): ?>
	<!-- Render saved content -->
	<?php echo $page_content; ?>
<?php else: ?>
	<!-- Default content - Replace this with your page's default HTML -->
	<section class="page-section">
		<div class="container">
			<h1>New Page Title</h1>
			<p>This is the default content. Edit this page in the admin to customize it.</p>
		</div>
	</section>
<?php endif; ?>

<?php
include 'includes/footer.php';
?>
