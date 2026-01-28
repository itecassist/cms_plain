<?php
/**
 * Simple CMS Configuration File
 */

// Admin credentials (CHANGE THESE!)
define('ADMIN_USERNAME', 'admin');
define('ADMIN_PASSWORD', password_hash('changeme123', PASSWORD_DEFAULT)); // Change this password!

// Paths
define('CONTENT_DIR', __DIR__ . '/content');
define('COMPONENTS_DIR', __DIR__ . '/components');
define('UPLOADS_DIR', __DIR__ . '/assets/img');
define('TEMPLATE_DIR', __DIR__ . '/template');
define('JSON_DIR', __DIR__ . '/json');

// Site settings
define('SITE_NAME', 'Simple CMS');
define('SESSION_TIMEOUT', 3600); // 1 hour

// Base URL - Update this to match your site URL
// Automatically detect the base URL
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
define('BASE_URL', $protocol . $host);

// Create required directories if they don't exist
if (!file_exists(CONTENT_DIR)) {
    mkdir(CONTENT_DIR, 0755, true);
}
if (!file_exists(COMPONENTS_DIR)) {
    mkdir(COMPONENTS_DIR, 0755, true);
}
if (!file_exists(UPLOADS_DIR)) {
    mkdir(UPLOADS_DIR, 0755, true);
}
if (!file_exists(JSON_DIR)) {
    mkdir(JSON_DIR, 0755, true);
}

// Start session
session_start();
