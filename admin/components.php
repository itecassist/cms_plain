<?php
require_once '../config.php';
require_once '../functions.php';
require_login();

// Get all component files
function get_components() {
    $components = [];
    $files = glob(COMPONENTS_DIR . '/*.{php,html}', GLOB_BRACE);
    
    foreach ($files as $file) {
        $basename = basename($file);
        $name = pathinfo($basename, PATHINFO_FILENAME);
        $extension = pathinfo($basename, PATHINFO_EXTENSION);
        $components[] = [
            'name' => $name,
            'file' => $basename,
            'extension' => $extension,
            'path' => $file,
            'size' => filesize($file),
            'modified' => filemtime($file)
        ];
    }
    
    // Sort by name
    usort($components, function($a, $b) {
        return strcmp($a['name'], $b['name']);
    });
    
    return $components;
}

// Handle component creation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_component'])) {
    $component_name = trim($_POST['component_name'] ?? '');
    $component_type = $_POST['component_type'] ?? 'html';
    
    // Validate component name
    if (empty($component_name)) {
        $create_error = 'Component name is required.';
    } elseif (!preg_match('/^[a-zA-Z0-9_-]+$/', $component_name)) {
        $create_error = 'Component name can only contain letters, numbers, hyphens, and underscores.';
    } else {
        $component_file = COMPONENTS_DIR . '/' . $component_name . '.' . $component_type;
        
        // Check if component already exists
        if (file_exists($component_file)) {
            $create_error = 'Component already exists.';
        } else {
            // Create default content based on type
            if ($component_type === 'php') {
                $default_content = "<?php\n/**\n * " . ucfirst(str_replace('-', ' ', $component_name)) . " Component\n */\n?>\n<div class=\"component-" . $component_name . "\">\n    <!-- Add your component content here -->\n    <p>Component content goes here...</p>\n</div>";
            } else {
                $default_content = "<div class=\"component-" . $component_name . "\">\n    <!-- Add your component content here -->\n    <p>Component content goes here...</p>\n</div>";
            }
            
            if (file_put_contents($component_file, $default_content)) {
                $create_success = 'Component "' . htmlspecialchars($component_name) . '.' . $component_type . '" created successfully!';
                header('Location: components.php?edit=' . urlencode($component_name . '.' . $component_type));
                exit;
            } else {
                $create_error = 'Failed to create component.';
            }
        }
    }
}

// Handle component deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_component'])) {
    $component_file = COMPONENTS_DIR . '/' . basename($_POST['component_file']);
    
    if (file_exists($component_file)) {
        if (unlink($component_file)) {
            $success = 'Component deleted successfully!';
        } else {
            $error = 'Failed to delete component.';
        }
    }
}

// Handle component editing
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_component'])) {
    $component_file = COMPONENTS_DIR . '/' . basename($_POST['component_file']);
    $component_content = $_POST['component_content'] ?? '';
    
    if (file_exists($component_file)) {
        if (file_put_contents($component_file, $component_content)) {
            $success = 'Component saved successfully!';
        } else {
            $error = 'Failed to save component.';
        }
    } else {
        $error = 'Component file not found.';
    }
}

// Handle component renaming
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['rename_component'])) {
    $old_file = COMPONENTS_DIR . '/' . basename($_POST['old_file']);
    $new_name = trim($_POST['new_name'] ?? '');
    $extension = pathinfo($old_file, PATHINFO_EXTENSION);
    
    if (empty($new_name)) {
        $error = 'Component name is required.';
    } elseif (!preg_match('/^[a-zA-Z0-9_-]+$/', $new_name)) {
        $error = 'Component name can only contain letters, numbers, hyphens, and underscores.';
    } else {
        $new_file = COMPONENTS_DIR . '/' . $new_name . '.' . $extension;
        
        if (file_exists($new_file)) {
            $error = 'A component with that name already exists.';
        } elseif (rename($old_file, $new_file)) {
            $success = 'Component renamed successfully!';
            if (isset($_GET['edit'])) {
                header('Location: components.php?edit=' . urlencode($new_name . '.' . $extension));
                exit;
            }
        } else {
            $error = 'Failed to rename component.';
        }
    }
}

$components = get_components();

// Load component for editing
$editing_component = null;
$component_content = '';
if (isset($_GET['edit'])) {
    $edit_file = COMPONENTS_DIR . '/' . basename($_GET['edit']);
    if (file_exists($edit_file)) {
        $editing_component = [
            'name' => pathinfo($edit_file, PATHINFO_FILENAME),
            'file' => basename($edit_file),
            'extension' => pathinfo($edit_file, PATHINFO_EXTENSION),
            'path' => $edit_file
        ];
        $component_content = file_get_contents($edit_file);
    }
}

$page_title = 'Components Manager';
include 'includes/admin-header.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Components - <?php echo SITE_NAME; ?></title>
    
    <!-- CodeMirror for better code editing -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/codemirror.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/theme/dracula.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/codemirror.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/mode/xml/xml.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/mode/javascript/javascript.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/mode/css/css.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/mode/htmlmixed/htmlmixed.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/mode/php/php.min.js"></script>
    
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
            max-width: 1400px;
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
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .page-header h2 {
            margin: 0;
            color: #333;
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
        .btn-primary {
            background: #667eea;
            color: white;
        }
        .btn-primary:hover {
            background: #5568d3;
        }
        .btn-secondary {
            background: #6c757d;
            color: white;
            margin-left: 10px;
        }
        .btn-secondary:hover {
            background: #5a6268;
        }
        .btn-danger {
            background: #ef4444;
            color: white;
        }
        .btn-danger:hover {
            background: #dc2626;
        }
        .btn-small {
            padding: 5px 12px;
            font-size: 12px;
            margin-left: 5px;
        }
        .content-grid {
            display: grid;
            grid-template-columns: 350px 1fr;
            gap: 20px;
        }
        @media (max-width: 1024px) {
            .content-grid {
                grid-template-columns: 1fr;
            }
        }
        .section {
            background: white;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .section-title {
            font-size: 18px;
            font-weight: 600;
            color: #333;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #667eea;
        }
        .component-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .component-item {
            padding: 15px;
            border: 2px solid #e5e7eb;
            border-radius: 5px;
            margin-bottom: 10px;
            transition: all 0.3s;
            cursor: pointer;
        }
        .component-item:hover {
            border-color: #667eea;
            background: #f9fafb;
        }
        .component-item.active {
            border-color: #667eea;
            background: #eef2ff;
        }
        .component-name {
            font-weight: 600;
            color: #333;
            margin-bottom: 5px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .component-badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 3px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
        }
        .badge-php {
            background: #818cf8;
            color: white;
        }
        .badge-html {
            background: #f59e0b;
            color: white;
        }
        .component-meta {
            font-size: 12px;
            color: #6b7280;
            margin-top: 5px;
        }
        .component-usage {
            font-size: 12px;
            color: #667eea;
            font-family: 'Courier New', monospace;
            margin-top: 8px;
            padding: 5px;
            background: #f3f4f6;
            border-radius: 3px;
        }
        .editor-section {
            min-height: 500px;
        }
        .editor-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .editor-title {
            font-size: 16px;
            font-weight: 600;
            color: #333;
        }
        .CodeMirror {
            border: 2px solid #e5e7eb;
            border-radius: 5px;
            height: auto;
            min-height: 400px;
            font-size: 14px;
        }
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #9ca3af;
        }
        .empty-state-icon {
            font-size: 64px;
            margin-bottom: 20px;
        }
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.4);
        }
        .modal-content {
            background-color: white;
            margin: 10% auto;
            padding: 30px;
            border-radius: 8px;
            width: 90%;
            max-width: 500px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.3);
        }
        .modal-header {
            font-size: 20px;
            font-weight: 600;
            margin-bottom: 20px;
            color: #333;
        }
        .close {
            float: right;
            font-size: 28px;
            font-weight: bold;
            color: #999;
            cursor: pointer;
            line-height: 1;
        }
        .close:hover {
            color: #333;
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
        .modal-footer {
            margin-top: 25px;
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }
        .btn-cancel {
            background: #ccc;
            color: #333;
        }
        .btn-cancel:hover {
            background: #bbb;
        }
        .info-box {
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 20px;
            font-size: 14px;
            color: #1e40af;
        }
        .info-box strong {
            display: block;
            margin-bottom: 8px;
        }
        .info-box code {
            background: #dbeafe;
            padding: 2px 6px;
            border-radius: 3px;
            font-family: 'Courier New', monospace;
        }
    </style>
</head>
<body>
    
    <div class="container">
        <div class="page-header">
            <h2>🧩 Components Manager</h2>
            <div>
                <button type="button" onclick="openCreateModal()" class="btn btn-primary">➕ Create Component</button>
            </div>
        </div>
        
        <?php if (isset($create_success)): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($create_success); ?></div>
        <?php endif; ?>
        
        <?php if (isset($create_error)): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($create_error); ?></div>
        <?php endif; ?>
        
        <?php if (isset($success)): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <div class="info-box">
            <strong>💡 How to use components:</strong>
            Components are reusable code snippets that can be included in your page content. 
            Use <code>{{component-name}}</code> in your content editor to include a component. 
            PHP components can contain logic, while HTML components are static.
        </div>
        
        <div class="content-grid">
            <!-- Components List -->
            <div class="section">
                <h3 class="section-title">Your Components</h3>
                
                <?php if (empty($components)): ?>
                    <div style="text-align: center; padding: 40px 20px; color: #9ca3af;">
                        <div style="font-size: 48px; margin-bottom: 15px;">📦</div>
                        <p>No components yet.</p>
                        <p style="font-size: 14px; margin-top: 10px;">Create your first component to get started!</p>
                    </div>
                <?php else: ?>
                    <ul class="component-list">
                        <?php foreach ($components as $component): ?>
                            <li class="component-item <?php echo ($editing_component && $editing_component['file'] === $component['file']) ? 'active' : ''; ?>" 
                                onclick="window.location.href='components.php?edit=<?php echo urlencode($component['file']); ?>'">
                                <div class="component-name">
                                    <span><?php echo htmlspecialchars($component['name']); ?></span>
                                    <span class="component-badge badge-<?php echo $component['extension']; ?>">
                                        <?php echo strtoupper($component['extension']); ?>
                                    </span>
                                </div>
                                <div class="component-usage">
                                    {{<?php echo htmlspecialchars($component['name']); ?>}}
                                </div>
                                <div class="component-meta">
                                    <?php echo number_format($component['size']); ?> bytes • 
                                    Modified: <?php echo date('M j, Y g:i A', $component['modified']); ?>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
            
            <!-- Editor -->
            <div class="section editor-section">
                <?php if ($editing_component): ?>
                    <div class="editor-header">
                        <div>
                            <div class="editor-title">
                                Editing: <?php echo htmlspecialchars($editing_component['file']); ?>
                            </div>
                            <div style="font-size: 12px; color: #6b7280; margin-top: 5px;">
                                Use in content: <code style="background: #f3f4f6; padding: 2px 6px; border-radius: 3px;">{{<?php echo htmlspecialchars($editing_component['name']); ?>}}</code>
                            </div>
                        </div>
                        <div>
                            <button type="button" onclick="openRenameModal()" class="btn btn-secondary btn-small">Rename</button>
                            <button type="button" onclick="confirmDelete()" class="btn btn-danger btn-small">Delete</button>
                            <button type="submit" form="edit-form" class="btn btn-success">💾 Save</button>
                        </div>
                    </div>
                    
                    <form id="edit-form" method="POST">
                        <input type="hidden" name="component_file" value="<?php echo htmlspecialchars($editing_component['file']); ?>">
                        <textarea name="component_content" id="component-editor"><?php echo htmlspecialchars($component_content); ?></textarea>
                        <input type="hidden" name="save_component" value="1">
                    </form>
                    
                    <!-- Delete Form (hidden) -->
                    <form id="delete-form" method="POST" style="display: none;">
                        <input type="hidden" name="component_file" value="<?php echo htmlspecialchars($editing_component['file']); ?>">
                        <input type="hidden" name="delete_component" value="1">
                    </form>
                <?php else: ?>
                    <div class="empty-state">
                        <div class="empty-state-icon">✏️</div>
                        <h3 style="margin-bottom: 10px; color: #6b7280;">Select a component to edit</h3>
                        <p>Choose a component from the list or create a new one.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Create Component Modal -->
    <div id="createModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeCreateModal()">&times;</span>
            <div class="modal-header">➕ Create New Component</div>
            <form method="POST">
                <div class="form-group">
                    <label class="form-label">Component Name</label>
                    <input type="text" name="component_name" class="form-control" 
                           placeholder="e.g. contact-form, social-links" 
                           pattern="[a-zA-Z0-9_-]+" 
                           required>
                    <div class="form-hint">Letters, numbers, hyphens, and underscores only. No spaces.</div>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Component Type</label>
                    <select name="component_type" class="form-control">
                        <option value="html">HTML (Static Content)</option>
                        <option value="php">PHP (Dynamic Content)</option>
                    </select>
                    <div class="form-hint">PHP components can contain logic and variables.</div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" onclick="closeCreateModal()" class="btn btn-cancel">Cancel</button>
                    <button type="submit" name="create_component" value="1" class="btn btn-primary">Create Component</button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Rename Component Modal -->
    <?php if ($editing_component): ?>
    <div id="renameModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeRenameModal()">&times;</span>
            <div class="modal-header">✏️ Rename Component</div>
            <form method="POST">
                <input type="hidden" name="old_file" value="<?php echo htmlspecialchars($editing_component['file']); ?>">
                
                <div class="form-group">
                    <label class="form-label">New Component Name</label>
                    <input type="text" name="new_name" class="form-control" 
                           value="<?php echo htmlspecialchars($editing_component['name']); ?>"
                           pattern="[a-zA-Z0-9_-]+" 
                           required>
                    <div class="form-hint">Letters, numbers, hyphens, and underscores only. Extension will remain .<?php echo $editing_component['extension']; ?></div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" onclick="closeRenameModal()" class="btn btn-cancel">Cancel</button>
                    <button type="submit" name="rename_component" value="1" class="btn btn-primary">Rename</button>
                </div>
            </form>
        </div>
    </div>
    <?php endif; ?>
    
    <script>
        // Modal functions
        function openCreateModal() {
            document.getElementById('createModal').style.display = 'block';
        }
        
        function closeCreateModal() {
            document.getElementById('createModal').style.display = 'none';
        }
        
        <?php if ($editing_component): ?>
        function openRenameModal() {
            document.getElementById('renameModal').style.display = 'block';
        }
        
        function closeRenameModal() {
            document.getElementById('renameModal').style.display = 'none';
        }
        
        function confirmDelete() {
            if (confirm('Are you sure you want to delete this component? This action cannot be undone.')) {
                document.getElementById('delete-form').submit();
            }
        }
        <?php endif; ?>
        
        // Close modal when clicking outside
        window.onclick = function(event) {
            const createModal = document.getElementById('createModal');
            <?php if ($editing_component): ?>
            const renameModal = document.getElementById('renameModal');
            <?php endif; ?>
            
            if (event.target === createModal) {
                createModal.style.display = 'none';
            }
            <?php if ($editing_component): ?>
            if (event.target === renameModal) {
                renameModal.style.display = 'none';
            }
            <?php endif; ?>
        }
        
        <?php if ($editing_component): ?>
        // Initialize CodeMirror
        const editor = CodeMirror.fromTextArea(document.getElementById('component-editor'), {
            mode: '<?php echo $editing_component['extension'] === 'php' ? 'application/x-httpd-php' : 'htmlmixed'; ?>',
            theme: 'dracula',
            lineNumbers: true,
            indentUnit: 4,
            indentWithTabs: false,
            lineWrapping: true,
            matchBrackets: true,
            autoCloseBrackets: true,
            autoCloseTags: true,
            extraKeys: {
                "Ctrl-S": function(cm) {
                    document.getElementById('edit-form').submit();
                },
                "Cmd-S": function(cm) {
                    document.getElementById('edit-form').submit();
                }
            }
        });
        
        // Set editor height
        editor.setSize(null, 500);
        
        // Update textarea before form submission
        document.getElementById('edit-form').addEventListener('submit', function() {
            editor.save();
        });
        <?php endif; ?>
    </script>
</body>
</html>
<?php include 'includes/admin-footer.php'; ?>
