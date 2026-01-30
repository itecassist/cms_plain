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
                    'order' => count($menu['items']) + 1
                ];
                $menu['items'][] = $new_item;
                if (save_menu($menu)) {
                    $message = '<div class="alert alert-success">Menu item added!</div>';
                }
                break;
                
            case 'edit_item':
                $menu = get_menu();
                $index = (int)$_POST['index'];
                if (isset($menu['items'][$index])) {
                    $menu['items'][$index]['label'] = $_POST['label'];
                    $menu['items'][$index]['url'] = $_POST['url'];
                    $menu['items'][$index]['parent'] = $_POST['parent'] ?: null;
                    if (save_menu($menu)) {
                        $message = '<div class="alert alert-success">Menu item updated!</div>';
                    }
                }
                break;
                
            case 'delete_item':
                $menu = get_menu();
                $index = (int)$_POST['index'];
                array_splice($menu['items'], $index, 1);
                // Reorder remaining items
                foreach ($menu['items'] as $i => $item) {
                    $menu['items'][$i]['order'] = $i + 1;
                }
                if (save_menu($menu)) {
                    $message = '<div class="alert alert-success">Menu item deleted!</div>';
                }
                break;
                
            case 'reorder':
                $menu = get_menu();
                $order = json_decode($_POST['order'], true);
                $reordered = [];
                foreach ($order as $idx => $original_index) {
                    if (isset($menu['items'][$original_index])) {
                        $menu['items'][$original_index]['order'] = $idx + 1;
                        $reordered[] = $menu['items'][$original_index];
                    }
                }
                $menu['items'] = $reordered;
                if (save_menu($menu)) {
                    echo json_encode(['success' => true]);
                } else {
                    echo json_encode(['success' => false]);
                }
                exit;
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
        .btn-warning { background: #f39c12; color: white; }
        .btn:hover { opacity: 0.9; }
        
        .menu-items { list-style: none; padding: 0; margin: 0; }
        .menu-item { background: #f8f9fa; padding: 15px; margin-bottom: 10px; border-radius: 5px; border-left: 4px solid #3498db; display: flex; justify-content: space-between; align-items: center; transition: all 0.3s; }
        .menu-item:hover { background: #e9ecef; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        .menu-item.dragging { opacity: 0.5; }
        .menu-item.submenu { margin-left: 30px; border-left-color: #95a5a6; }
        .menu-item-info { flex: 1; }
        .menu-item-label { font-weight: 600; color: #2c3e50; margin-bottom: 5px; }
        .menu-item-url { color: #7f8c8d; font-size: 13px; }
        .menu-item-actions { display: flex; gap: 10px; }
        
        .grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        
        #sortable-menu { min-height: 50px; }
        .drag-handle { cursor: move; color: #95a5a6; margin-right: 15px; font-size: 20px; user-select: none; }
        
        /* Modal */
        .modal { display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.4); }
        .modal-content { background-color: white; margin: 10% auto; padding: 30px; border-radius: 8px; width: 90%; max-width: 500px; box-shadow: 0 4px 20px rgba(0,0,0,0.3); }
        .modal-header { font-size: 20px; font-weight: 600; margin-bottom: 20px; color: #333; }
        .close { float: right; font-size: 28px; font-weight: bold; color: #999; cursor: pointer; line-height: 1; }
        .close:hover { color: #333; }
        .modal-footer { margin-top: 25px; display: flex; justify-content: flex-end; gap: 10px; }
        .btn-cancel { background: #ccc; color: #333; }
        .btn-cancel:hover { background: #bbb; }
    </style>
    
    <!-- SortableJS from CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
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
                    
                    <button type="submit" class="btn btn-success">Add Menu Item</button>
                </form>
            </div>

            <!-- Current Menu Structure -->
            <div class="card">
                <h2>Current Menu <span style="font-size: 14px; color: #7f8c8d; font-weight: normal;">(Drag to reorder)</span></h2>
                <?php if (empty($menu['items'])): ?>
                    <p style="color: #7f8c8d;">No menu items yet. Add your first item!</p>
                <?php else: ?>
                    <ul class="menu-items" id="sortable-menu">
                        <?php foreach ($menu['items'] as $index => $item): ?>
                        <li class="menu-item<?php echo !empty($item['parent']) ? ' submenu' : ''; ?>" data-index="<?php echo $index; ?>">
                            <span class="drag-handle" title="Drag to reorder">☰</span>
                            <div class="menu-item-info">
                                <div class="menu-item-label"><?php echo htmlspecialchars($item['label']); ?><?php if (!empty($item['parent'])) echo ' <span style="color: #95a5a6; font-size: 12px;">(child of ' . htmlspecialchars($item['parent']) . ')</span>'; ?></div>
                                <div class="menu-item-url"><?php echo htmlspecialchars($item['url']); ?></div>
                            </div>
                            <div class="menu-item-actions">
                                <button type="button" class="btn btn-warning btn-edit" 
                                        data-index="<?php echo $index; ?>"
                                        data-label="<?php echo htmlspecialchars($item['label']); ?>"
                                        data-url="<?php echo htmlspecialchars($item['url']); ?>"
                                        data-parent="<?php echo htmlspecialchars($item['parent'] ?? ''); ?>">
                                    ✏️ Edit
                                </button>
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="action" value="delete_item">
                                    <input type="hidden" name="index" value="<?php echo $index; ?>">
                                    <button type="submit" class="btn btn-danger" onclick="return confirm('Delete this menu item?')">🗑️ Delete</button>
                                </form>
                            </div>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Edit Modal -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeEditModal()">&times;</span>
            <div class="modal-header">✏️ Edit Menu Item</div>
            <form method="POST" id="editForm">
                <input type="hidden" name="action" value="edit_item">
                <input type="hidden" name="index" id="edit-index">
                
                <div class="form-group">
                    <label>Menu Label</label>
                    <input type="text" name="label" id="edit-label" required>
                </div>
                
                <div class="form-group">
                    <label>URL (without .php)</label>
                    <input type="text" name="url" id="edit-url" required>
                </div>
                
                <div class="form-group">
                    <label>Parent Menu (for submenu)</label>
                    <select name="parent" id="edit-parent">
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
                
                <div class="modal-footer">
                    <button type="button" onclick="closeEditModal()" class="btn btn-cancel">Cancel</button>
                    <button type="submit" class="btn btn-success">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
    
    <script>
        // Initialize Sortable for drag and drop
        const sortableList = document.getElementById('sortable-menu');
        if (sortableList) {
            const sortable = new Sortable(sortableList, {
                handle: '.drag-handle',
                animation: 150,
                ghostClass: 'dragging',
                onEnd: function(evt) {
                    // Get new order
                    const items = sortableList.querySelectorAll('.menu-item');
                    const order = [];
                    items.forEach(item => {
                        order.push(parseInt(item.getAttribute('data-index')));
                    });
                    
                    // Send to server
                    fetch(window.location.href, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: 'action=reorder&order=' + encodeURIComponent(JSON.stringify(order))
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Reload page to show new order
                            window.location.reload();
                        }
                    });
                }
            });
        }
        
        // Edit functionality
        document.querySelectorAll('.btn-edit').forEach(btn => {
            btn.addEventListener('click', function() {
                const index = this.getAttribute('data-index');
                const label = this.getAttribute('data-label');
                const url = this.getAttribute('data-url');
                const parent = this.getAttribute('data-parent');
                
                document.getElementById('edit-index').value = index;
                document.getElementById('edit-label').value = label;
                document.getElementById('edit-url').value = url;
                document.getElementById('edit-parent').value = parent;
                
                document.getElementById('editModal').style.display = 'block';
            });
        });
        
        function closeEditModal() {
            document.getElementById('editModal').style.display = 'none';
        }
        
        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('editModal');
            if (event.target === modal) {
                modal.style.display = 'none';
            }
        }
    </script>
</body>
</html>
