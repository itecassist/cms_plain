<?php
/**
 * Simple CMS Configuration File
 */

// Paths
define('CONTENT_DIR', __DIR__ . '/content');
define('COMPONENTS_DIR', __DIR__ . '/components');
define('UPLOADS_DIR', __DIR__ . '/assets/img');
define('TEMPLATE_DIR', __DIR__ . '/template');
define('JSON_DIR', __DIR__ . '/json');
define('DATABASE_DIR', __DIR__ . '/database');

// Site settings
define('SITE_NAME', 'Simple CMS');
define('SESSION_TIMEOUT', 3600); // 1 hour

// Base URL - Update this to match your site URL
// Automatically detect the base URL
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443)) ? "https://" : "http://";
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
if (!file_exists(DATABASE_DIR)) {
    mkdir(DATABASE_DIR, 0755, true);
}

// Start session
session_start();

/**
 * Get database connection
 */
function get_db() {
    static $db = null;
    if ($db === null) {
        try {
            $db_path = DATABASE_DIR . '/blog.db';
            
            // Create database directory if it doesn't exist
            if (!file_exists(DATABASE_DIR)) {
                mkdir(DATABASE_DIR, 0755, true);
            }
            
            $db = new PDO('sqlite:' . $db_path);
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            
            // Auto-initialize users table if it doesn't exist
            init_users_table($db);
        } catch (PDOException $e) {
            die('Database connection failed: ' . $e->getMessage());
        }
    }
    return $db;
}

/**
 * Initialize users table if it doesn't exist
 */
function init_users_table($db) {
    try {
        // Check if users table exists
        $result = $db->query("SELECT name FROM sqlite_master WHERE type='table' AND name='users'");
        
        if ($result->fetchColumn() === false) {
            // Create users table
            $db->exec("
                CREATE TABLE IF NOT EXISTS users (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    username TEXT UNIQUE NOT NULL,
                    password TEXT NOT NULL,
                    email TEXT,
                    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
                )
            ");
            
            // Insert default admin user
            $stmt = $db->prepare("INSERT INTO users (username, password, email) VALUES (?, ?, ?)");
            $stmt->execute([
                'admin',
                password_hash('changeme123', PASSWORD_DEFAULT),
                'admin@example.com'
            ]);
        }
    } catch (PDOException $e) {
        // Silently fail - table might already exist
    }
}

// Include blog functions
require_once __DIR__ . '/blog-functions.php';
