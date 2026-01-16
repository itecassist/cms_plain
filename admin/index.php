<?php
require_once '../config.php';
require_once '../functions.php';
require_login();

// Handle page status changes
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['toggle_status'])) {
    $page = $_POST['page'];
    $current_status = get_page_status($page);
    $new_status = $current_status === 'published' ? 'unpublished' : 'published';
    set_page_status($page, $new_status);
    header('Location: index.php');
    exit;
}

$pages = get_pages();
$page_title = 'Dashboard';
include 'includes/admin-header.php';
?>
    <style>
        .header {
            background: white;
            padding: 20px 40px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .header h1 {
            font-size: 24px;
            color: #333;
        }
        .header-actions {
            display: flex;
            gap: 15px;
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
        }
        .btn-primary {
            background: #667eea;
            color: white;
        }
        .btn-primary:hover {
            background: #5568d3;
        }
        .btn-secondary {
            background: #e0e0e0;
            color: #333;
        }
        .btn-secondary:hover {
            background: #d0d0d0;
        }
        .container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 20px;
        }
        .page-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 30px;
        }
        .page-card {
            background: white;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .page-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        .page-card h3 {
            margin-bottom: 15px;
            color: #333;
            font-size: 18px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .page-card .actions {
            display: flex;
            gap: 10px;
        }
        .page-card .btn {
            flex: 1;
            text-align: center;
        }
        .status-badge {
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
        }
        .status-published {
            background: #d4edda;
            color: #155724;
        }
        .status-unpublished {
            background: #f8d7da;
            color: #721c24;
        }
        .btn-toggle {
            padding: 6px 12px;
            font-size: 12px;
            margin-top: 10px;
        }
        .page-card.unpublished {
            opacity: 0.7;
            border: 2px dashed #e74c3c;
        }
        .welcome {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .welcome h2 {
            color: #333;
            margin-bottom: 15px;
        }
        .welcome p {
            color: #666;
            line-height: 1.6;
        }
        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .stat-card .number {
            font-size: 36px;
            font-weight: bold;
            color: #667eea;
            margin-bottom: 5px;
        }
        .stat-card .label {
            color: #666;
            font-size: 14px;
        }
    </style>
</head>
<body>
    
    <div class="container">
        <div class="welcome">
            <h2>Welcome to Your CMS</h2>
            <p>Select a page below to edit its content. You can modify text, upload images, and make changes that will appear immediately on your website. Start by editing <strong>Global Settings</strong> to update your header, footer, and contact information across all pages.</p>
        </div>
        
        <div class="stats">
            <div class="stat-card">
                <div class="number"><?php echo count($pages) - 1; ?></div>
                <div class="label">Total Pages</div>
            </div>
            <div class="stat-card">
                <div class="number"><?php echo count(glob(CONTENT_DIR . '/*.json')); ?></div>
                <div class="label">Edited Pages</div>
            </div>
            <div class="stat-card">
                <div class="number"><?php echo count(glob(UPLOADS_DIR . '/*.*')); ?></div>
                <div class="label">Uploaded Images</div>
            </div>
        </div>
        
        <!-- Global Settings Card -->
        <?php if (in_array('_global', $pages)): ?>
        <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 30px; border-radius: 8px; margin-bottom: 30px; color: white; box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);">
            <h2 style="color: white; margin-bottom: 15px;">⚙️ Global Settings</h2>
            <p style="margin-bottom: 20px; opacity: 0.95;">Update your site-wide settings including header phone, business hours, social media links, footer information, and more. These changes appear on every page.</p>
            <a href="edit.php?page=_global" class="btn btn-success" style="background: white; color: #667eea; display: inline-block; text-decoration: none; padding: 12px 30px; border-radius: 5px; font-weight: 600;">Edit Global Settings</a>
        </div>
        <?php endif; ?>
        
        <h2 style="margin-bottom: 15px; color: #333;">Your Pages</h2>
        <div class="page-grid">
            <?php foreach ($pages as $page): ?>
                <?php if ($page === '_global') continue; // Skip global, shown above ?>
                <?php 
                $status = get_page_status($page);
                $is_unpublished = $status === 'unpublished';
                ?>
                <div class="page-card <?php echo $is_unpublished ? 'unpublished' : ''; ?>">
                    <h3>
                        <span><?php echo htmlspecialchars(str_replace(['.php', '-'], [' ', ' '], $page)); ?></span>
                        <span class="status-badge status-<?php echo $status; ?>"><?php echo $status; ?></span>
                    </h3>
                    <div class="actions">
                        <a href="edit.php?page=<?php echo urlencode($page); ?>" class="btn btn-primary">Edit Content</a>
                        <a href="../<?php echo htmlspecialchars(str_replace('.php', '', $page)); ?>" class="btn btn-secondary" target="_blank">View</a>
                    </div>
                    <form method="POST" style="margin-top: 10px;">
                        <input type="hidden" name="toggle_status" value="1">
                        <input type="hidden" name="page" value="<?php echo htmlspecialchars($page); ?>">
                        <button type="submit" class="btn btn-toggle <?php echo $is_unpublished ? 'btn-primary' : 'btn-secondary'; ?>" style="width: 100%;">
                            <?php echo $is_unpublished ? '✓ Publish' : '✕ Unpublish'; ?>
                        </button>
                    </form>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>
