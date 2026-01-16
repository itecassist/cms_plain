<?php
/**
 * CMS Setup Verification Script
 * Run this file once to verify your installation
 * Access: yoursite.com/verify-setup.php
 * DELETE THIS FILE after verification for security!
 */

// Prevent running if already set up
if (isset($_GET['confirm']) && $_GET['confirm'] !== 'yes') {
    die('Add ?confirm=yes to URL to run this verification');
}

$errors = [];
$warnings = [];
$success = [];

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CMS Setup Verification</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: #f5f5f5;
            padding: 40px 20px;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
            margin-bottom: 30px;
            text-align: center;
        }
        .check-item {
            padding: 15px;
            margin-bottom: 10px;
            border-radius: 5px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .success {
            background: #d1fae5;
            border-left: 4px solid #10b981;
        }
        .error {
            background: #fee;
            border-left: 4px solid #ef4444;
        }
        .warning {
            background: #fef3c7;
            border-left: 4px solid #f59e0b;
        }
        .icon {
            font-size: 24px;
        }
        .message {
            flex: 1;
        }
        .section {
            margin-top: 30px;
        }
        .section h2 {
            color: #667eea;
            margin-bottom: 15px;
            font-size: 20px;
        }
        .info {
            background: #e0e7ff;
            padding: 15px;
            border-radius: 5px;
            margin-top: 20px;
        }
        .delete-warning {
            background: #fee;
            border: 2px solid #ef4444;
            padding: 20px;
            border-radius: 5px;
            margin-top: 30px;
            text-align: center;
        }
        .delete-warning strong {
            color: #ef4444;
            font-size: 18px;
        }
        code {
            background: #f5f5f5;
            padding: 2px 6px;
            border-radius: 3px;
            font-family: monospace;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔧 CMS Setup Verification</h1>
        
        <?php
        // Check PHP version
        if (version_compare(PHP_VERSION, '7.4.0', '>=')) {
            echo '<div class="check-item success"><span class="icon">✓</span><div class="message"><strong>PHP Version:</strong> ' . PHP_VERSION . ' (OK)</div></div>';
        } else {
            $errors[] = 'PHP version ' . PHP_VERSION . ' is too old. Requires 7.4 or higher.';
            echo '<div class="check-item error"><span class="icon">✗</span><div class="message"><strong>PHP Version:</strong> ' . PHP_VERSION . ' (Too old - need 7.4+)</div></div>';
        }
        
        // Check if config.php exists
        if (file_exists('config.php')) {
            echo '<div class="check-item success"><span class="icon">✓</span><div class="message"><strong>config.php:</strong> Found</div></div>';
            include 'config.php';
        } else {
            $errors[] = 'config.php not found!';
            echo '<div class="check-item error"><span class="icon">✗</span><div class="message"><strong>config.php:</strong> NOT FOUND</div></div>';
        }
        
        // Check if functions.php exists
        if (file_exists('functions.php')) {
            echo '<div class="check-item success"><span class="icon">✓</span><div class="message"><strong>functions.php:</strong> Found</div></div>';
        } else {
            $errors[] = 'functions.php not found!';
            echo '<div class="check-item error"><span class="icon">✗</span><div class="message"><strong>functions.php:</strong> NOT FOUND</div></div>';
        }
        
        // Check if .htaccess exists
        if (file_exists('.htaccess')) {
            echo '<div class="check-item success"><span class="icon">✓</span><div class="message"><strong>.htaccess:</strong> Found</div></div>';
        } else {
            $warnings[] = '.htaccess not found. URL routing may not work.';
            echo '<div class="check-item warning"><span class="icon">⚠</span><div class="message"><strong>.htaccess:</strong> NOT FOUND (URL routing may not work)</div></div>';
        }
        
        // Check admin folder
        if (is_dir('admin')) {
            echo '<div class="check-item success"><span class="icon">✓</span><div class="message"><strong>admin/ folder:</strong> Exists</div></div>';
            
            $admin_files = ['index.php', 'login.php', 'edit.php', 'uploads.php', 'logout.php'];
            foreach ($admin_files as $file) {
                if (file_exists('admin/' . $file)) {
                    echo '<div class="check-item success"><span class="icon">✓</span><div class="message"><strong>admin/' . $file . ':</strong> Found</div></div>';
                } else {
                    $errors[] = 'admin/' . $file . ' not found!';
                    echo '<div class="check-item error"><span class="icon">✗</span><div class="message"><strong>admin/' . $file . ':</strong> NOT FOUND</div></div>';
                }
            }
        } else {
            $errors[] = 'admin/ folder not found!';
            echo '<div class="check-item error"><span class="icon">✗</span><div class="message"><strong>admin/ folder:</strong> NOT FOUND</div></div>';
        }
        
        // Check template folder
        if (is_dir('template')) {
            $template_files = glob('template/*.htm*');
            echo '<div class="check-item success"><span class="icon">✓</span><div class="message"><strong>template/ folder:</strong> Exists (' . count($template_files) . ' template files found)</div></div>';
        } else {
            $warnings[] = 'template/ folder not found.';
            echo '<div class="check-item warning"><span class="icon">⚠</span><div class="message"><strong>template/ folder:</strong> NOT FOUND</div></div>';
        }
        
        // Check content folder
        if (is_dir('content')) {
            if (is_writable('content')) {
                echo '<div class="check-item success"><span class="icon">✓</span><div class="message"><strong>content/ folder:</strong> Exists and writable</div></div>';
            } else {
                $errors[] = 'content/ folder is not writable! Run: chmod 755 content/';
                echo '<div class="check-item error"><span class="icon">✗</span><div class="message"><strong>content/ folder:</strong> NOT WRITABLE (chmod 755 needed)</div></div>';
            }
        } else {
            $errors[] = 'content/ folder not found!';
            echo '<div class="check-item error"><span class="icon">✗</span><div class="message"><strong>content/ folder:</strong> NOT FOUND</div></div>';
        }
        
        // Check uploads folder
        if (is_dir('uploads')) {
            if (is_writable('uploads')) {
                echo '<div class="check-item success"><span class="icon">✓</span><div class="message"><strong>uploads/ folder:</strong> Exists and writable</div></div>';
            } else {
                $errors[] = 'uploads/ folder is not writable! Run: chmod 755 uploads/';
                echo '<div class="check-item error"><span class="icon">✗</span><div class="message"><strong>uploads/ folder:</strong> NOT WRITABLE (chmod 755 needed)</div></div>';
            }
        } else {
            $errors[] = 'uploads/ folder not found!';
            echo '<div class="check-item error"><span class="icon">✗</span><div class="message"><strong>uploads/ folder:</strong> NOT FOUND</div></div>';
        }
        
        // Check if password has been changed
        if (defined('ADMIN_PASSWORD')) {
            if (password_verify('changeme123', ADMIN_PASSWORD)) {
                $warnings[] = 'You are still using the DEFAULT PASSWORD! Change it in config.php immediately!';
                echo '<div class="check-item warning"><span class="icon">⚠</span><div class="message"><strong>Admin Password:</strong> STILL DEFAULT - CHANGE IT!</div></div>';
            } else {
                echo '<div class="check-item success"><span class="icon">✓</span><div class="message"><strong>Admin Password:</strong> Changed from default</div></div>';
            }
        }
        
        // Check session support
        if (function_exists('session_start')) {
            echo '<div class="check-item success"><span class="icon">✓</span><div class="message"><strong>Session Support:</strong> Available</div></div>';
        } else {
            $errors[] = 'PHP sessions not supported!';
            echo '<div class="check-item error"><span class="icon">✗</span><div class="message"><strong>Session Support:</strong> NOT AVAILABLE</div></div>';
        }
        
        // Check JSON support
        if (function_exists('json_encode') && function_exists('json_decode')) {
            echo '<div class="check-item success"><span class="icon">✓</span><div class="message"><strong>JSON Support:</strong> Available</div></div>';
        } else {
            $errors[] = 'PHP JSON extension not available!';
            echo '<div class="check-item error"><span class="icon">✗</span><div class="message"><strong>JSON Support:</strong> NOT AVAILABLE</div></div>';
        }
        
        // Check mod_rewrite (if Apache)
        if (function_exists('apache_get_modules')) {
            if (in_array('mod_rewrite', apache_get_modules())) {
                echo '<div class="check-item success"><span class="icon">✓</span><div class="message"><strong>mod_rewrite:</strong> Enabled</div></div>';
            } else {
                $warnings[] = 'mod_rewrite not detected. URL routing may not work.';
                echo '<div class="check-item warning"><span class="icon">⚠</span><div class="message"><strong>mod_rewrite:</strong> NOT DETECTED</div></div>';
            }
        } else {
            echo '<div class="check-item warning"><span class="icon">⚠</span><div class="message"><strong>mod_rewrite:</strong> Cannot detect (not Apache or function unavailable)</div></div>';
        }
        
        // Summary
        echo '<div class="section">';
        echo '<h2>Summary</h2>';
        
        if (empty($errors)) {
            if (empty($warnings)) {
                echo '<div class="check-item success"><span class="icon">🎉</span><div class="message"><strong>Perfect!</strong> Your CMS is ready to use.</div></div>';
            } else {
                echo '<div class="check-item warning"><span class="icon">⚠</span><div class="message"><strong>Almost there!</strong> Fix the warnings above for best results.</div></div>';
            }
        } else {
            echo '<div class="check-item error"><span class="icon">✗</span><div class="message"><strong>Setup incomplete.</strong> Fix the errors above before using the CMS.</div></div>';
        }
        echo '</div>';
        
        // Next steps
        if (empty($errors)) {
            echo '<div class="info">';
            echo '<h3>✅ Next Steps:</h3>';
            echo '<ol style="margin-left: 20px; margin-top: 10px;">';
            echo '<li>Visit <code>' . $_SERVER['HTTP_HOST'] . '/admin/</code> to login</li>';
            echo '<li>Default username: <code>admin</code></li>';
            echo '<li>Default password: <code>changeme123</code> (CHANGE THIS!)</li>';
            echo '<li>Add <code>data-editable</code> attributes to your templates</li>';
            echo '<li>Start editing your content!</li>';
            echo '</ol>';
            echo '</div>';
        }
        ?>
        
        <div class="delete-warning">
            <strong>⚠️ SECURITY WARNING</strong>
            <p style="margin-top: 10px;">Delete this file (<code>verify-setup.php</code>) after verification!</p>
            <p>It contains sensitive system information.</p>
        </div>
    </div>
</body>
</html>
