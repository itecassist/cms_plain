<?php
/**
 * Page Template - Copy this file and rename to create a new page
 * Example: Copy to "my-new-page.php"
 * 
 * For custom page logic (like form handling), create a file in:
 * ./custom/my-new-page.php
 */
require_once 'config.php';
require_once 'functions.php';

$current_page = basename(__FILE__);

// Include custom page logic if it exists
// This allows each page to have custom PHP (forms, mail, etc.)
if (file_exists('custom/' . $current_page)) {
    include 'custom/' . $current_page;
}

// Load SEO data
$seo_data = get_seo_data($current_page);

// Load page content from ./content/
$content_file = CONTENT_DIR . '/' . sanitize_filename($current_page) . '.json';
$page_content = '';
if (file_exists($content_file)) {
    $content_data = json_decode(file_get_contents($content_file), true) ?? [];
    $page_content = $content_data['content'] ?? '';
}

include 'includes/header.php';
?>

<?php echo process_components($page_content); ?>

<?php
include 'includes/footer.php';
?>
