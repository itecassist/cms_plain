<?php
require_once '../config.php';
require_once '../functions.php';
require_login();

$page = $_GET['page'] ?? '';
$pages = get_pages();

if (!in_array($page, $pages)) {
    header('Location: index.php');
    exit;
}

$zones = get_editable_zones($page);
$saved_content = [];

$content_file = CONTENT_DIR . '/' . sanitize_filename($page) . '.json';
if (file_exists($content_file)) {
    $saved_content = json_decode(file_get_contents($content_file), true) ?? [];
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save'])) {
    $content = [];
    foreach ($_POST as $key => $value) {
        if (strpos($key, 'zone_') === 0) {
            $zone_name = substr($key, 5);
            $content[$zone_name] = $value;
        }
    }
    
    if (save_content($page, $content)) {
        $success = 'Content saved successfully!';
        $saved_content = $content;
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
    <title>Edit: <?php echo htmlspecialchars($page); ?> - <?php echo SITE_NAME; ?></title>
    
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
        .header {
            background: white;
            padding: 20px 40px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 100;
        }
        .header h1 {
            font-size: 20px;
            color: #333;
        }
        .header-actions {
            display: flex;
            gap: 10px;
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
        .btn-success {
            background: #10b981;
            color: white;
        }
        .btn-success:hover {
            background: #059669;
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
        .editor-section {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .zone-group {
            margin-bottom: 30px;
        }
        .zone-group:last-child {
            margin-bottom: 0;
        }
        .zone-label {
            display: block;
            font-weight: 600;
            color: #333;
            margin-bottom: 10px;
            font-size: 16px;
        }
        .zone-name {
            display: inline-block;
            background: #667eea;
            color: white;
            padding: 3px 10px;
            border-radius: 3px;
            font-size: 12px;
            margin-left: 10px;
            font-weight: normal;
        }
        textarea {
            width: 100%;
            min-height: 150px;
            padding: 12px;
            border: 2px solid #e0e0e0;
            border-radius: 5px;
            font-size: 14px;
            font-family: inherit;
            resize: vertical;
        }
        .no-zones {
            text-align: center;
            padding: 60px 20px;
            color: #666;
        }
        .no-zones h3 {
            margin-bottom: 15px;
            color: #333;
        }
        .instructions {
            background: #fff9e6;
            border: 1px solid #ffd700;
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .instructions h3 {
            color: #333;
            margin-bottom: 10px;
        }
        .instructions p {
            color: #666;
            line-height: 1.6;
            margin-bottom: 10px;
        }
        .instructions code {
            background: #f5f5f5;
            padding: 2px 6px;
            border-radius: 3px;
            font-family: monospace;
        }
    </style>
</head>
<body>
    
    <div class="container">
        <div style="background: white; padding: 20px; border-radius: 8px; margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center;">
            <h2 style="margin: 0;">Editing: <?php echo $page === '_global' ? '⚙️ Global Settings' : htmlspecialchars($page); ?></h2>
            <div>
                <button type="submit" form="edit-form" class="btn btn-success">Save Changes</button>
                <?php if ($page !== '_global'): ?>
                <a href="../<?php echo htmlspecialchars(str_replace('.php', '', $page)); ?>" class="btn btn-secondary" target="_blank">Preview</a>
                <?php endif; ?>
            </div>
        </div>
        
        <?php if (isset($success)): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <?php if ($page === '_global'): ?>
            <div class="instructions" style="background: #e0f2fe; border-color: #0ea5e9;">
                <h3>📝 About Global Settings</h3>
                <p>These settings apply to <strong>all pages</strong> that use the header and footer includes. Update your contact information, social media links, and footer content here.</p>
                <p><strong>Fields included:</strong> Phone numbers, business hours, social media URLs, footer text, copyright, and more.</p>
            </div>
        <?php endif; ?>
        
        <?php if (empty($zones)): ?>
            <div class="instructions">
                <h3>⚠️ No Editable Zones Found</h3>
                <p>This page doesn't have any editable zones yet. To make content editable, you need to add <code>data-editable="zone-name"</code> attributes to HTML elements in the template file.</p>
                <p><strong>Example:</strong></p>
                <p><code>&lt;h1 data-editable="hero-title"&gt;Welcome to Our Gym&lt;/h1&gt;</code></p>
                <p><code>&lt;p data-editable="hero-description"&gt;Get fit and stay healthy&lt;/p&gt;</code></p>
            </div>
            
            <div class="editor-section no-zones">
                <h3>Ready to add editable zones?</h3>
                <p>Edit the template file and add data-editable attributes to the elements you want clients to edit.</p>
            </div>
        <?php else: ?>
            <form id="edit-form" method="POST">
                <div class="editor-section">
                    <?php foreach ($zones as $zone_name => $zone_data): ?>
                        <div class="zone-group">
                            <label class="zone-label">
                                <?php echo ucwords(str_replace(['-', '_'], ' ', $zone_name)); ?>
                                <span class="zone-name"><?php echo htmlspecialchars($zone_name); ?></span>
                            </label>
                            <textarea 
                                name="zone_<?php echo htmlspecialchars($zone_name); ?>" 
                                class="tinymce-editor"
                            ><?php echo htmlspecialchars($saved_content[$zone_name] ?? $zone_data['content']); ?></textarea>
                        </div>
                    <?php endforeach; ?>
                </div>
                <input type="hidden" name="save" value="1">
            </form>
        <?php endif; ?>
    </div>
    
    <script>
        // Initialize TinyMCE
        tinymce.init({
            selector: '.tinymce-editor',
            height: 300,
            menubar: false,
            plugins: [
                'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
                'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
                'insertdatetime', 'media', 'table', 'help', 'wordcount'
            ],
            toolbar: 'undo redo | blocks | bold italic forecolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | removeformat | code',
            content_style: 'body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen, Ubuntu, Cantarell, sans-serif; font-size: 14px; }',
            automatic_uploads: true,
            file_picker_types: 'image',
            file_picker_callback: function (cb, value, meta) {
                if (meta.filetype === 'image') {
                    var input = document.createElement('input');
                    input.setAttribute('type', 'file');
                    input.setAttribute('accept', 'image/*');
                    
                    input.onchange = function () {
                        var file = this.files[0];
                        var reader = new FileReader();
                        
                        reader.onload = function () {
                            var formData = new FormData();
                            formData.append('file', file);
                            
                            fetch('upload.php', {
                                method: 'POST',
                                body: formData
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    cb('../' + data.path, { title: file.name });
                                } else {
                                    alert('Upload failed: ' + data.error);
                                }
                            })
                            .catch(error => {
                                alert('Upload error: ' + error);
                            });
                        };
                        
                        reader.readAsDataURL(file);
                    };
                    
                    input.click();
                }
            }
        });
    </script>
</body>
</html>
