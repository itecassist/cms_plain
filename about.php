<?php
/**
 * About Page
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
	<?php
	include 'sections/about.php';
	?>
<?php endif; ?>

<?php
include 'includes/footer.php';
?>