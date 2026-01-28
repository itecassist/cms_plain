<?php
/**
 * Router for PHP Built-in Server
 * This file handles URL rewriting when using php -S
 */

// Get the requested URI
$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

// Remove query string
$uri = strtok($uri, '?');

// If it's a real file or directory, serve it directly
if ($uri !== '/' && file_exists(__DIR__ . $uri)) {
    return false;
}

// Don't route admin, assets, uploads, or content directories - serve them directly
if (preg_match('#^/(admin|assets|uploads|content)(/|$)#', $uri)) {
    return false;
}

// Handle admin subdirectory index (e.g., /admin/ or /admin/index.php)
if ($uri === 'admin') {
    return false;
}

// Protect sensitive files
if (preg_match('#/(config|functions)\.php$#', $uri)) {
    http_response_code(403);
    die('Access denied');
}

// Remove leading slash
$uri = ltrim($uri, '/');

// Remove any trailing slash
$uri = rtrim($uri, '/');

// If empty, serve index.php (homepage)
if (empty($uri) || $uri === 'index') {
    require __DIR__ . '/index.php';
    exit;
}

// For all other pages, pass to index.php with page parameter
// This allows index.php to determine which page to load
$_GET['page'] = $uri;
require __DIR__ . '/index.php';
exit;

// If no match found, return 404
http_response_code(404);
echo '<!DOCTYPE html>
<html>
<head>
    <title>404 - Page Not Found</title>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; padding: 50px; }
        h1 { color: #e74c3c; }
    </style>
</head>
<body>
    <h1>404 - Page Not Found</h1>
    <p>The page "' . htmlspecialchars($uri) . '" could not be found.</p>
    <a href="/">Go to Homepage</a>
</body>
</html>';
