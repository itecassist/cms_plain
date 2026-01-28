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

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save'])) {
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
        'content' => $_POST['page_content'] ?? ''
    ];
    
    $content_saved = file_put_contents($content_file, json_encode($content_data, JSON_PRETTY_PRINT));
    
    if ($seo_saved !== false && $content_saved !== false) {
        $success = 'Content and SEO data saved successfully!';
        $page_content = $content_data['content'];
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
    </style>
</head>
<body>
    
    <div class="container">
        <div class="page-header">
            <h2>Editing: <?php echo htmlspecialchars($page_filename); ?></h2>
            <div>
                <button type="submit" form="edit-form" class="btn btn-success">💾 Save Changes</button>
                <a href="../<?php echo htmlspecialchars(str_replace('.php', '', $page_filename)); ?>" class="btn btn-secondary" target="_blank">👁️ Preview</a>
            </div>
        </div>
        
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
                <textarea name="page_content" id="page-content-editor"><?php echo htmlspecialchars($page_content); ?></textarea>
            </div>
            
            <input type="hidden" name="save" value="1">
        </form>
    </div>
    
    <script>
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
