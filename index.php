<?php
/**
 * Universal Page Router
 * Single entry point for all pages
 * 
 * All requests are routed here by router.php or .htaccess
 * This file handles all page loading dynamically
 */
require_once 'config.php';
require_once 'functions.php';

// Determine which page is being requested
// From router.php: $_GET['page'] or $_SERVER['REQUEST_URI']
$requested_page = '';
$full_path = '';

if (isset($_GET['page'])) {
    $full_path = $_GET['page'];
    $requested_page = basename($_GET['page']);
} else {
    // Get from REQUEST_URI and extract page name
    $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $uri = trim($uri, '/');
    $full_path = $uri;
    $requested_page = !empty($uri) ? basename($uri) : 'index';
}

// Check if this is a blog route
$is_blog_listing = ($requested_page === 'blog' && strpos($full_path, '/') === false);
$is_blog_post = false;

// Check for blog post route (blog/slug)
if (preg_match('#^blog/(.+)$#', $full_path, $matches)) {
    $is_blog_post = true;
    $_GET['slug'] = $matches[1];
}

// Handle blog routes
if ($is_blog_listing || $is_blog_post) {
    if ($is_blog_post) {
        require 'blog-single.php';
    } else {
        require 'blog.php';
    }
    exit;
}

// Validate page name (prevent directory traversal)
$requested_page = preg_replace('/[^a-zA-Z0-9_-]/', '', $requested_page);
$requested_page = $requested_page ?: 'index';

// Current page variable for dynamic content loading
$current_page = $requested_page . '.php';

// Include custom page logic if it exists
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

// Load custom JavaScript for this page if it exists
if (file_exists('custom/' . str_replace('.php', '.js', $current_page))) {
?>
<script src="custom/<?php echo str_replace('.php', '.js', $current_page); ?>"></script>
<?php
}
?>
