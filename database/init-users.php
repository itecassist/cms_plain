<?php
/**
 * Initialize users table in database
 * Run this once to set up the users table
 */

// Define database directory path
$db_dir = __DIR__;
$db_path = $db_dir . '/blog.db';

try {
    // Connect to SQLite database
    $db = new PDO('sqlite:' . $db_path);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
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
    
    // Check if admin user already exists
    $stmt = $db->prepare("SELECT COUNT(*) FROM users WHERE username = ?");
    $stmt->execute(['admin']);
    $count = $stmt->fetchColumn();
    
    if ($count == 0) {
        // Insert default admin user (migrate from config)
        $stmt = $db->prepare("INSERT INTO users (username, password, email) VALUES (?, ?, ?)");
        $stmt->execute([
            'admin',
            password_hash('changeme123', PASSWORD_DEFAULT),
            'admin@example.com'
        ]);
        echo "✓ Users table created and default admin user added.\n";
        echo "  Username: admin\n";
        echo "  Password: changeme123\n";
        echo "  Please change this password after logging in!\n";
    } else {
        echo "✓ Users table exists. Admin user already present.\n";
    }
    
} catch (PDOException $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
}
