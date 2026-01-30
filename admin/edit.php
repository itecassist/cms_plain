<?php
require_once '../config.php';
require_once '../functions.php';
require_login();

// Handle page deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_page'])) {
    $page_to_delete = $_POST['page_to_delete'] ?? '';
    
    if (!empty($page_to_delete)) {
        $page_filename = basename($page_to_delete);
        $deleted_files = [];
        $failed_files = [];
        
        // Delete content file
        $content_file = CONTENT_DIR . '/' . sanitize_filename($page_filename) . '.json';
        if (file_exists($content_file)) {
            if (unlink($content_file)) {
                $deleted_files[] = 'content/' . sanitize_filename($page_filename) . '.json';
            } else {
                $failed_files[] = 'content/' . sanitize_filename($page_filename) . '.json';
            }
        }
        
        // Delete SEO file
        $seo_file = JSON_DIR . '/' . sanitize_filename($page_filename) . '.json';
        if (file_exists($seo_file)) {
            if (unlink($seo_file)) {
                $deleted_files[] = 'json/' . sanitize_filename($page_filename) . '.json';
            } else {
                $failed_files[] = 'json/' . sanitize_filename($page_filename) . '.json';
            }
        }
        
        // Delete custom PHP file if exists
        $custom_php = '../custom/' . str_replace('.php', '', $page_filename) . '.php';
        if (file_exists($custom_php)) {
            if (unlink($custom_php)) {
                $deleted_files[] = 'custom/' . str_replace('.php', '', $page_filename) . '.php';
            } else {
                $failed_files[] = 'custom/' . str_replace('.php', '', $page_filename) . '.php';
            }
        }
        
        // Delete custom JS file if exists
        $custom_js = '../custom/' . str_replace('.php', '', $page_filename) . '.js';
        if (file_exists($custom_js)) {
            if (unlink($custom_js)) {
                $deleted_files[] = 'custom/' . str_replace('.php', '', $page_filename) . '.js';
            } else {
                $failed_files[] = 'custom/' . str_replace('.php', '', $page_filename) . '.js';
            }
        }
        
        if (count($deleted_files) > 0) {
            $delete_success = 'Page deleted successfully! Removed: ' . implode(', ', $deleted_files);
        }
        
        if (count($failed_files) > 0) {
            $delete_error = 'Failed to delete some files: ' . implode(', ', $failed_files);
        }
        
        // Redirect to index after successful deletion
        if (empty($failed_files) && !empty($deleted_files)) {
            header('Location: index.php?deleted=' . urlencode($page_filename));
            exit;
        }
    }
}

// Handle page creation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_page'])) {
    $new_page_name = trim($_POST['new_page_name'] ?? '');
    
    // Validate page name
    if (empty($new_page_name)) {
        $create_error = 'Page name is required.';
    } elseif (!preg_match('/^[a-zA-Z0-9_-]+$/', $new_page_name)) {
        $create_error = 'Page name can only contain letters, numbers, hyphens, and underscores.';
    } else {
        $page_name = $new_page_name . '.php';
        $content_file = CONTENT_DIR . '/' . sanitize_filename($page_name) . '.json';
        $seo_file = JSON_DIR . '/' . sanitize_filename($page_name) . '.json';
        
        // Check if page already exists
        if (file_exists($content_file)) {
            $create_error = 'Page already exists.';
        } else {
            // Create content file
            $content_data = ['content' => '<p>Page content goes here...</p>'];
            $content_saved = file_put_contents($content_file, json_encode($content_data, JSON_PRETTY_PRINT));
            
            // Create SEO file
            $seo_data = [
                'title' => ucfirst(str_replace('-', ' ', $new_page_name)),
                'description' => '',
                'keywords' => '',
                'og_title' => '',
                'og_description' => '',
                'og_image' => ''
            ];
            $seo_saved = file_put_contents($seo_file, json_encode($seo_data, JSON_PRETTY_PRINT));
            
            if ($content_saved && $seo_saved) {
                $create_success = 'Page "' . htmlspecialchars($new_page_name) . '" created successfully!';
                // Redirect to edit the new page
                header('Location: edit.php?page=' . urlencode($page_name));
                exit;
            } else {
                $create_error = 'Failed to create page.';
            }
        }
    }
}

$page = $_GET['page'] ?? '';
$pages = get_pages();

if (!in_array($page, $pages)) {
    header('Location: index.php');
    exit;
}

// Extract just the filename without path for JSON files
$page_filename = basename($page);

// Load SEO data
$seo_file = JSON_DIR . '/' . sanitize_filename($page_filename) . '.json';
$seo_data = [];
if (file_exists($seo_file)) {
    $seo_data = json_decode(file_get_contents($seo_file), true) ?? [];
}

// Load page content
$content_file = CONTENT_DIR . '/' . sanitize_filename($page_filename) . '.json';
$page_content = '';
if (file_exists($content_file)) {
    $content_data = json_decode(file_get_contents($content_file), true) ?? [];
    $page_content = $content_data['content'] ?? '';
}

// If no saved content, try to get initial content from the page file
if (empty($page_content) && file_exists('../' . $page_filename)) {
    $page_content = get_page_body_content($page_filename);
}

// Convert relative asset paths to absolute URLs for editor display
$page_content_for_editor = fix_asset_paths($page_content);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save'])) {
    // Convert absolute URLs back to relative paths before saving
    $content_to_save = convert_to_relative_paths($_POST['page_content'] ?? '');
    
    // Save SEO data
    $seo_data = [
        'title' => $_POST['seo_title'] ?? '',
        'description' => $_POST['seo_description'] ?? '',
        'keywords' => $_POST['seo_keywords'] ?? '',
        'og_title' => $_POST['seo_og_title'] ?? '',
        'og_description' => $_POST['seo_og_description'] ?? '',
        'og_image' => $_POST['seo_og_image'] ?? ''
    ];
    
    $seo_saved = file_put_contents($seo_file, json_encode($seo_data, JSON_PRETTY_PRINT));
    
    // Save page content
    $content_data = [
        'content' => $content_to_save
    ];
    
    $content_saved = file_put_contents($content_file, json_encode($content_data, JSON_PRETTY_PRINT));
    
    if ($seo_saved !== false && $content_saved !== false) {
        $success = 'Content and SEO data saved successfully!';
        $page_content = $content_to_save;
        $page_content_for_editor = fix_asset_paths($page_content);
    } else {
        $error = 'Failed to save content.';
    }
}
$page_title = 'Edit Content';
include 'includes/admin-header.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit: <?php echo htmlspecialchars($page_filename); ?> - <?php echo SITE_NAME; ?></title>
    
    <!-- TinyMCE from jsDelivr (no API key required) -->
    <script src="https://cdn.jsdelivr.net/npm/tinymce@6/tinymce.min.js"></script>
    
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
        .btn-secondary {
            background: #6c757d;
            color: white;
            margin-left: 10px;
        }
        .btn-secondary:hover {
            background: #5a6268;
        }
        .editor-section {
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
        textarea.form-control {
            resize: vertical;
            min-height: 80px;
        }
        .form-hint {
            font-size: 12px;
            color: #666;
            margin-top: 5px;
        }
        .seo-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        @media (max-width: 768px) {
            .seo-grid {
                grid-template-columns: 1fr;
            }
        }
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
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
            max-width: 400px;
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
        .modal-footer {
            margin-top: 25px;
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }
        .btn-primary {
            background: #667eea;
            color: white;
        }
        .btn-primary:hover {
            background: #5568d3;
        }
        .btn-cancel {
            background: #ccc;
            color: #333;
        }
        .btn-cancel:hover {
            background: #bbb;
        }
    </style>
</head>
<body>
    
    <div class="container">
        <div class="page-header">
            <h2>Editing: <?php echo htmlspecialchars($page_filename); ?></h2>
            <div>
                <button type="button" onclick="openCreateModal()" class="btn btn-primary" style="background: #667eea; margin-right: 10px;">➕ Create Page</button>
                <button type="submit" form="edit-form" class="btn btn-success">💾 Save Changes</button>
                <a href="../<?php echo htmlspecialchars(str_replace('.php', '', $page_filename)); ?>" class="btn btn-secondary" target="_blank">👁️ Preview</a>
                <button type="button" onclick="openDeleteModal()" class="btn" style="background: #e74c3c; color: white; margin-left: 10px;">🗑️ Delete Page</button>
            </div>
        </div>
        
        <?php if (isset($delete_success)): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($delete_success); ?></div>
        <?php endif; ?>
        
        <?php if (isset($delete_error)): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($delete_error); ?></div>
        <?php endif; ?>
        
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
        
        <form id="edit-form" method="POST">
            <!-- SEO Section -->
            <div class="editor-section">
                <h3 class="section-title">🔍 SEO Settings</h3>
                <div class="seo-grid">
                    <div class="form-group">
                        <label class="form-label">Page Title</label>
                        <input type="text" name="seo_title" class="form-control" 
                               value="<?php echo htmlspecialchars($seo_data['title'] ?? ''); ?>" 
                               placeholder="Enter page title for search engines">
                        <div class="form-hint">Recommended: 50-60 characters</div>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Meta Keywords</label>
                        <input type="text" name="seo_keywords" class="form-control" 
                               value="<?php echo htmlspecialchars($seo_data['keywords'] ?? ''); ?>" 
                               placeholder="fitness, gym, training">
                        <div class="form-hint">Comma-separated keywords</div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Meta Description</label>
                    <textarea name="seo_description" class="form-control" rows="3"
                              placeholder="Brief description of the page content"><?php echo htmlspecialchars($seo_data['description'] ?? ''); ?></textarea>
                    <div class="form-hint">Recommended: 150-160 characters</div>
                </div>
                
                <h4 style="margin: 25px 0 15px 0; color: #555; font-size: 16px;">📱 Open Graph (Social Media)</h4>
                
                <div class="seo-grid">
                    <div class="form-group">
                        <label class="form-label">OG Title</label>
                        <input type="text" name="seo_og_title" class="form-control" 
                               value="<?php echo htmlspecialchars($seo_data['og_title'] ?? ''); ?>" 
                               placeholder="Title when shared on social media">
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">OG Image URL</label>
                        <input type="text" name="seo_og_image" class="form-control" 
                               value="<?php echo htmlspecialchars($seo_data['og_image'] ?? ''); ?>" 
                               placeholder="/assets/img/og-image.jpg">
                        <div class="form-hint">Recommended: 1200x630px</div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label">OG Description</label>
                    <textarea name="seo_og_description" class="form-control" rows="2"
                              placeholder="Description when shared on social media"><?php echo htmlspecialchars($seo_data['og_description'] ?? ''); ?></textarea>
                </div>
            </div>
            
            <!-- Content Section -->
            <div class="editor-section">
                <h3 class="section-title">✏️ Page Content</h3>
                <textarea name="page_content" id="page-content-editor"><?php echo htmlspecialchars($page_content_for_editor); ?></textarea>
            </div>
            
            <input type="hidden" name="save" value="1">
        </form>
    </div>
    
    <!-- Create Page Modal -->
    <div id="createModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeCreateModal()">&times;</span>
            <div class="modal-header">➕ Create New Page</div>
            <form method="POST">
                <div class="form-group">
                    <label class="form-label">Page Name</label>
                    <input type="text" name="new_page_name" id="newPageName" class="form-control" 
                           placeholder="e.g. gallery, testimonials, team" 
                           pattern="[a-zA-Z0-9_-]+" 
                           required>
                    <div class="form-hint">Letters, numbers, hyphens, and underscores only. No spaces.</div>
                </div>
                <div class="modal-footer">
                    <button type="button" onclick="closeCreateModal()" class="btn btn-cancel">Cancel</button>
                    <button type="submit" name="create_page" value="1" class="btn btn-primary">Create Page</button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Delete Page Modal -->
    <div id="deleteModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeDeleteModal()">&times;</span>
            <div class="modal-header" style="color: #e74c3c;">🗑️ Delete Page</div>
            <form method="POST">
                <p style="margin-bottom: 20px; color: #666;">Are you sure you want to delete <strong><?php echo htmlspecialchars($page_filename); ?></strong>?</p>
                <p style="margin-bottom: 20px; color: #e74c3c; font-size: 14px;">⚠️ This will delete:</p>
                <ul style="margin-bottom: 20px; color: #666; padding-left: 20px;">
                    <li>Content file (./content/<?php echo htmlspecialchars($page_filename); ?>.json)</li>
                    <li>SEO data (./json/<?php echo htmlspecialchars($page_filename); ?>.json)</li>
                    <li>Custom PHP file (./custom/<?php echo str_replace('.php', '', htmlspecialchars($page_filename)); ?>.php) - if exists</li>
                    <li>Custom JS file (./custom/<?php echo str_replace('.php', '', htmlspecialchars($page_filename)); ?>.js) - if exists</li>
                </ul>
                <p style="color: #e74c3c; font-weight: 600; font-size: 14px;">This action cannot be undone!</p>
                <input type="hidden" name="delete_page" value="1">
                <input type="hidden" name="page_to_delete" value="<?php echo htmlspecialchars($page_filename); ?>">
                <div class="modal-footer">
                    <button type="button" onclick="closeDeleteModal()" class="btn btn-cancel">Cancel</button>
                    <button type="submit" class="btn" style="background: #e74c3c; color: white;">Delete Page</button>
                </div>
            </form>
        </div>
    </div>
    
    <script>
        function openCreateModal() {
            document.getElementById('createModal').style.display = 'block';
            document.getElementById('newPageName').focus();
        }
        
        function closeCreateModal() {
            document.getElementById('createModal').style.display = 'none';
        }
        
        function openDeleteModal() {
            document.getElementById('deleteModal').style.display = 'block';
        }
        
        function closeDeleteModal() {
            document.getElementById('deleteModal').style.display = 'none';
        }
        
        window.onclick = function(event) {
            const createModal = document.getElementById('createModal');
            const deleteModal = document.getElementById('deleteModal');
            if (event.target === createModal) {
                createModal.style.display = 'none';
            }
            if (event.target === deleteModal) {
                deleteModal.style.display = 'none';
            }
        }
        
        // Get the base URL for loading CSS
        const baseUrl = window.location.protocol + '//' + window.location.host;
        
        // Initialize TinyMCE with site CSS
        tinymce.init({
            selector: '#page-content-editor',
            height: 600,
            menubar: true,
            plugins: [
                'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
                'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
                'insertdatetime', 'media', 'table', 'help', 'wordcount'
            ],
            toolbar: 'undo redo | blocks | bold italic forecolor backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image media | removeformat code | preview fullscreen',
            
            // Import site's CSS into the editor
            content_css: [
                baseUrl + '/assets/css/bootstrap-grid.css',
                baseUrl + '/assets/css/style.css'
            ],
            
            // Match body styling
            body_class: 'page-content',
            
            automatic_uploads: true,
            file_picker_types: 'image',
            file_picker_callback: function (cb, value, meta) {
                if (meta.filetype === 'image') {
                    var input = document.createElement('input');
                    input.setAttribute('type', 'file');
                    input.setAttribute('accept', 'image/*');
                    
                    input.onchange = function () {
                        var file = this.files[0];
                        var formData = new FormData();
                        formData.append('file', file);
                        
                        fetch('uploads.php', {
                            method: 'POST',
                            body: formData
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                cb('../' + data.path, { title: file.name });
                            } else {
                                alert('Upload failed: ' + (data.error || 'Unknown error'));
                            }
                        })
                        .catch(error => {
                            alert('Upload error: ' + error);
                        });
                    };
                    
                    input.click();
                }
            },
            
            // Enable relative URLs
            relative_urls: true,
            remove_script_host: true,
            document_base_url: '../'
        });
    </script>
</body>
</html>
