<?php
/**
 * Menu Manager
 */
require_once '../config.php';
require_once '../functions.php';

// Check login
if (!is_logged_in()) {
    header('Location: login.php');
    exit;
}

// Handle menu updates
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'save_menu':
                $menu_data = json_decode($_POST['menu_data'], true);
                if ($menu_data && save_menu($menu_data)) {
                    $message = '<div class="alert alert-success">Menu saved successfully!</div>';
                } else {
                    $message = '<div class="alert alert-error">Failed to save menu.</div>';
                }
                break;
                
            case 'add_item':
                $menu = get_menu();
                $new_item = [
                    'label' => $_POST['label'],
                    'url' => $_POST['url'],
                    'parent' => $_POST['parent'] ?: null,
                    'order' => (int)$_POST['order']
                ];
                $menu['items'][] = $new_item;
                if (save_menu($menu)) {
                    $message = '<div class="alert alert-success">Menu item added!</div>';
                }
                break;
                
            case 'delete_item':
                $menu = get_menu();
                $index = (int)$_POST['index'];
                array_splice($menu['items'], $index, 1);
                if (save_menu($menu)) {
                    $message = '<div class="alert alert-success">Menu item deleted!</div>';
                }
                break;
        }
    }
}

$menu = get_menu();
$pages = get_pages();
$page_title = 'Menu Manager';
include 'includes/admin-header.php';
?>
    <style>
        
        .container { max-width: 1200px; margin: 0 auto; padding: 20px; }
        .header { background: white; padding: 20px; margin-bottom: 30px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .header h1 { font-size: 28px; color: #2c3e50; margin-bottom: 10px; }
        .nav { display: flex; gap: 20px; margin-top: 15px; }
        .nav a { color: #7f8c8d; text-decoration: none; padding: 8px 15px; border-radius: 5px; }
        .nav a:hover, .nav a.active { background: #ecf0f1; color: #2c3e50; }
        
        .card { background: white; padding: 25px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-bottom: 20px; }
        .card h2 { font-size: 20px; margin-bottom: 20px; color: #2c3e50; }
        
        .alert { padding: 15px; border-radius: 5px; margin-bottom: 20px; }
        .alert-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .alert-error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: 500; color: #2c3e50; }
        .form-group input, .form-group select { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; }
        
        .btn { padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; font-size: 14px; text-decoration: none; display: inline-block; }
        .btn-primary { background: #3498db; color: white; }
        .btn-success { background: #27ae60; color: white; }
        .btn-danger { background: #e74c3c; color: white; }
        .btn:hover { opacity: 0.9; }
        
        .menu-items { list-style: none; }
        .menu-item { background: #f8f9fa; padding: 15px; margin-bottom: 10px; border-radius: 5px; border-left: 4px solid #3498db; display: flex; justify-content: space-between; align-items: center; }
        .menu-item.submenu { margin-left: 30px; border-left-color: #95a5a6; }
        .menu-item-info { flex: 1; }
        .menu-item-label { font-weight: 600; color: #2c3e50; margin-bottom: 5px; }
        .menu-item-url { color: #7f8c8d; font-size: 13px; }
        .menu-item-actions { display: flex; gap: 10px; }
        
        .grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        
        #sortable-menu { min-height: 50px; }
        .drag-handle { cursor: move; color: #95a5a6; margin-right: 10px; }
        .dragging { opacity: 0.5; }
    </style>
</head>
<body>

    <div class="container">
        <?php echo $message; ?>

        <div class="grid">
            <!-- Add New Menu Item -->
            <div class="card">
                <h2>Add Menu Item</h2>
                <form method="POST">
                    <input type="hidden" name="action" value="add_item">
                    
                    <div class="form-group">
                        <label>Menu Label</label>
                        <input type="text" name="label" required placeholder="e.g., About Us">
                    </div>
                    
                    <div class="form-group">
                        <label>URL (without .php)</label>
                        <input type="text" name="url" required placeholder="e.g., about">
                    </div>
                    
                    <div class="form-group">
                        <label>Parent Menu (for submenu)</label>
                        <select name="parent">
                            <option value="">None (Top Level)</option>
                            <?php
                            $unique_labels = [];
                            foreach ($menu['items'] as $item) {
                                if (!in_array($item['label'], $unique_labels)) {
                                    echo '<option value="' . htmlspecialchars($item['label']) . '">' . htmlspecialchars($item['label']) . '</option>';
                                    $unique_labels[] = $item['label'];
                                }
                            }
                            ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Order</label>
                        <input type="number" name="order" value="<?php echo count($menu['items']) + 1; ?>">
                    </div>
                    
                    <button type="submit" class="btn btn-success">Add Menu Item</button>
                </form>
            </div>

            <!-- Current Menu Structure -->
            <div class="card">
                <h2>Current Menu</h2>
                <?php if (empty($menu['items'])): ?>
                    <p style="color: #7f8c8d;">No menu items yet. Add your first item!</p>
                <?php else: ?>
                    <ul class="menu-items" id="sortable-menu">
                        <?php 
                        $tree = build_menu_tree($menu['items']);
                        function render_menu_item($item, $index, $is_submenu = false) {
                            $class = $is_submenu ? 'menu-item submenu' : 'menu-item';
                            echo '<li class="' . $class . '" data-index="' . $index . '">';
                            echo '<span class="drag-handle">☰</span>';
                            echo '<div class="menu-item-info">';
                            echo '<div class="menu-item-label">' . htmlspecialchars($item['label']) . '</div>';
                            echo '<div class="menu-item-url">' . htmlspecialchars($item['url']) . '</div>';
                            echo '</div>';
                            echo '<div class="menu-item-actions">';
                            echo '<form method="POST" style="display: inline;">';
                            echo '<input type="hidden" name="action" value="delete_item">';
                            echo '<input type="hidden" name="index" value="' . $index . '">';
                            echo '<button type="submit" class="btn btn-danger" onclick="return confirm(\'Delete this menu item?\')">Delete</button>';
                            echo '</form>';
                            echo '</div>';
                            echo '</li>';
                            
                            if (isset($item['children'])) {
                                foreach ($item['children'] as $child_index => $child) {
                                    render_menu_item($child, $index + $child_index + 1, true);
                                }
                            }
                        }
                        
                        foreach ($tree as $index => $item) {
                            render_menu_item($item, $index);
                        }
                        ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>

        <!-- Quick Links -->
        <div class="card">
            <h2>Available Pages</h2>
            <p style="color: #7f8c8d; margin-bottom: 15px;">Copy these URLs to add them to your menu:</p>
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 10px;">
                <?php foreach ($pages as $page): ?>
                    <div style="background: #ecf0f1; padding: 10px; border-radius: 5px;">
                        <strong><?php echo htmlspecialchars($page); ?></strong><br>
                        <code style="font-size: 12px; color: #7f8c8d;"><?php echo str_replace('.php', '', $page); ?></code>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</body>
</html>
