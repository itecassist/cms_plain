<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title ?? 'Admin'; ?> - CMS Admin</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background: #f5f7fa; }
        
        .admin-header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 0; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        .admin-header-top { padding: 20px 40px; display: flex; justify-content: space-between; align-items: center; }
        .admin-header h1 { font-size: 24px; font-weight: 600; }
        .admin-header h1 a { color: white; text-decoration: none; }
        .admin-nav { background: rgba(0,0,0,0.1); }
        .admin-nav ul { list-style: none; display: flex; padding: 0 40px; }
        .admin-nav li { margin: 0; }
        .admin-nav a { display: block; padding: 15px 20px; color: rgba(255,255,255,0.9); text-decoration: none; transition: all 0.3s; border-bottom: 3px solid transparent; }
        .admin-nav a:hover { background: rgba(255,255,255,0.1); color: white; }
        .admin-nav a.active { background: rgba(255,255,255,0.15); color: white; border-bottom-color: white; font-weight: 600; }
        
        .btn { padding: 10px 20px; border-radius: 5px; text-decoration: none; font-weight: 500; transition: all 0.3s; border: none; cursor: pointer; font-size: 14px; display: inline-block; }
        .btn-primary { background: #667eea; color: white; }
        .btn-primary:hover { background: #5568d3; }
        .btn-secondary { background: #e0e0e0; color: #333; }
        .btn-secondary:hover { background: #d0d0d0; }
        .btn-success { background: #27ae60; color: white; }
        .btn-success:hover { background: #229954; }
        .btn-danger { background: #e74c3c; color: white; }
        .btn-danger:hover { background: #c0392b; }
        .btn-light { background: rgba(255,255,255,0.2); color: white; border: 1px solid rgba(255,255,255,0.3); }
        .btn-light:hover { background: rgba(255,255,255,0.3); }
        
        .container { max-width: 1200px; margin: 40px auto; padding: 0 20px; }
        .card { background: white; padding: 25px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-bottom: 20px; }
        .card h2 { font-size: 20px; margin-bottom: 20px; color: #2c3e50; }
        
        .alert { padding: 15px; border-radius: 5px; margin-bottom: 20px; }
        .alert-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .alert-error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .alert-info { background: #d1ecf1; color: #0c5460; border: 1px solid #bee5eb; }
        
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: 500; color: #2c3e50; }
        .form-group input, .form-group select, .form-group textarea { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; }
        .form-group textarea { min-height: 100px; font-family: inherit; }
        
        .admin-table { width: 100%; border-collapse: collapse; background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .admin-table thead { background: #667eea; color: white; }
        .admin-table th { padding: 15px; text-align: left; font-weight: 600; }
        .admin-table td { padding: 15px; border-bottom: 1px solid #f0f0f0; }
        .admin-table tbody tr:hover { background: #f9f9f9; }
        .admin-table .actions { white-space: nowrap; }
        
        .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        .page-header h1 { font-size: 28px; color: #2c3e50; }
        
        .admin-content { max-width: 1400px; margin: 40px auto; padding: 0 20px; }
    </style>
</head>
<body>
    <div class="admin-header">
        <div class="admin-header-top">
            <h1><a href="index.php">⚡ CMS Admin</a></h1>
            <div>
                <a href="../" class="btn btn-light" target="_blank">View Site</a>
                <a href="logout.php" class="btn btn-light">Logout</a>
            </div>
        </div>
        <nav class="admin-nav">
            <ul>
                <li><a href="index.php" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'index.php') ? 'active' : ''; ?>">Dashboard</a></li>
                <li><a href="edit.php" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'edit.php') ? 'active' : ''; ?>">Content</a></li>
                <li><a href="blog.php" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'blog.php') ? 'active' : ''; ?>">Blog</a></li>
                <li><a href="comments.php" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'comments.php') ? 'active' : ''; ?>">Comments</a></li>
                <li><a href="components.php" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'components.php') ? 'active' : ''; ?>">Components</a></li>
                <li><a href="menu.php" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'menu.php') ? 'active' : ''; ?>">Menu</a></li>
                <li><a href="uploads.php" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'uploads.php') ? 'active' : ''; ?>">Uploads</a></li>
            </ul>
        </nav>
    </div>
