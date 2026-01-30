<?php
/**
 * User Settings / Account Management
 */
require_once '../config.php';
require_once '../functions.php';
require_login();

// Get current user info
$user = get_user($_SESSION['user_id']);

$success = '';
$error = '';

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    
    if (empty($username)) {
        $error = 'Username is required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL) && !empty($email)) {
        $error = 'Please enter a valid email address.';
    } else {
        if (update_user($_SESSION['user_id'], $username, $email)) {
            $_SESSION['username'] = $username;
            $success = 'Profile updated successfully!';
            $user = get_user($_SESSION['user_id']);
        } else {
            $error = 'Failed to update profile. Username might already exist.';
        }
    }
}

// Handle password change
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    // Verify current password
    $db = get_db();
    $stmt = $db->prepare("SELECT password FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $current_hash = $stmt->fetchColumn();
    
    if (!password_verify($current_password, $current_hash)) {
        $error = 'Current password is incorrect.';
    } elseif (strlen($new_password) < 6) {
        $error = 'New password must be at least 6 characters long.';
    } elseif ($new_password !== $confirm_password) {
        $error = 'New passwords do not match.';
    } else {
        if (update_user_password($_SESSION['user_id'], $new_password)) {
            $success = 'Password changed successfully!';
        } else {
            $error = 'Failed to change password.';
        }
    }
}

$page_title = 'Account Settings';
include 'includes/admin-header.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings - <?php echo SITE_NAME; ?></title>
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: #f5f5f5;
        }
        .container {
            max-width: 900px;
            margin: 30px auto;
            padding: 0 20px;
        }
        .alert {
            padding: 15px 20px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .alert-success {
            background: #d1fae5;
            color: #065f46;
            border: 1px solid #10b981;
        }
        .alert-error {
            background: #fee;
            color: #c33;
            border: 1px solid #f66;
        }
        .page-header {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .page-header h2 {
            margin: 0;
            color: #333;
        }
        .settings-section {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .section-title {
            font-size: 18px;
            font-weight: 600;
            color: #333;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #667eea;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-label {
            display: block;
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
            font-size: 14px;
        }
        .form-control {
            width: 100%;
            padding: 10px 12px;
            border: 2px solid #e0e0e0;
            border-radius: 5px;
            font-size: 14px;
            font-family: inherit;
            transition: border-color 0.3s;
        }
        .form-control:focus {
            outline: none;
            border-color: #667eea;
        }
        .form-hint {
            font-size: 12px;
            color: #666;
            margin-top: 5px;
        }
        .btn {
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s;
            border: none;
            cursor: pointer;
            font-size: 14px;
            display: inline-block;
        }
        .btn-success {
            background: #10b981;
            color: white;
        }
        .btn-success:hover {
            background: #059669;
        }
        .info-box {
            background: #f0f9ff;
            border: 1px solid #bae6fd;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 20px;
            font-size: 14px;
            color: #0c4a6e;
        }
        .user-info {
            background: #f8fafc;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .user-info-item {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #e2e8f0;
        }
        .user-info-item:last-child {
            border-bottom: none;
        }
        .user-info-label {
            font-weight: 600;
            color: #475569;
        }
        .user-info-value {
            color: #64748b;
        }
    </style>
</head>
<body>
    
    <div class="container">
        <div class="page-header">
            <h2>⚙️ Account Settings</h2>
        </div>
        
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <!-- Profile Information -->
        <div class="settings-section">
            <h3 class="section-title">👤 Profile Information</h3>
            
            <div class="user-info">
                <div class="user-info-item">
                    <span class="user-info-label">User ID:</span>
                    <span class="user-info-value">#<?php echo htmlspecialchars($user['id']); ?></span>
                </div>
                <div class="user-info-item">
                    <span class="user-info-label">Account Created:</span>
                    <span class="user-info-value"><?php echo date('M j, Y g:i A', strtotime($user['created_at'])); ?></span>
                </div>
            </div>
            
            <form method="POST">
                <div class="form-group">
                    <label class="form-label">Username</label>
                    <input type="text" name="username" class="form-control" 
                           value="<?php echo htmlspecialchars($user['username']); ?>" 
                           required>
                    <div class="form-hint">This is your login username</div>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Email Address</label>
                    <input type="email" name="email" class="form-control" 
                           value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" 
                           placeholder="your@email.com">
                    <div class="form-hint">Used for password recovery (optional)</div>
                </div>
                
                <button type="submit" name="update_profile" class="btn btn-success">💾 Update Profile</button>
            </form>
        </div>
        
        <!-- Change Password -->
        <div class="settings-section">
            <h3 class="section-title">🔒 Change Password</h3>
            
            <div class="info-box">
                <strong>🛡️ Password Security Tips:</strong><br>
                • Use at least 6 characters (longer is better)<br>
                • Include a mix of letters, numbers, and symbols<br>
                • Don't reuse passwords from other accounts
            </div>
            
            <form method="POST">
                <div class="form-group">
                    <label class="form-label">Current Password</label>
                    <input type="password" name="current_password" class="form-control" 
                           required autocomplete="current-password">
                </div>
                
                <div class="form-group">
                    <label class="form-label">New Password</label>
                    <input type="password" name="new_password" class="form-control" 
                           required autocomplete="new-password" minlength="6">
                    <div class="form-hint">Minimum 6 characters</div>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Confirm New Password</label>
                    <input type="password" name="confirm_password" class="form-control" 
                           required autocomplete="new-password" minlength="6">
                </div>
                
                <button type="submit" name="change_password" class="btn btn-success">🔐 Change Password</button>
            </form>
        </div>
    </div>
    
</body>
</html>
<?php include 'includes/admin-footer.php'; ?>
