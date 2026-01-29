<?php
require_once '../config.php';
require_once '../functions.php';
require_login();

// Handle post creation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_post'])) {
    $post_data = [
        'title' => $_POST['title'] ?? '',
        'slug' => $_POST['slug'] ?? '',
        'content' => $_POST['content'] ?? '',
        'excerpt' => $_POST['excerpt'] ?? '',
        'author' => $_POST['author'] ?? 'Admin',
        'status' => $_POST['status'] ?? 'draft',
        'featured_image' => $_POST['featured_image'] ?? '',
        'categories' => $_POST['categories'] ?? [],
        'tags' => !empty($_POST['tags']) ? array_map('trim', explode(',', $_POST['tags'])) : []
    ];
    
    $post_id = create_post($post_data);
    
    if ($post_id) {
        header('Location: blog.php?edit=' . $post_id . '&success=created');
        exit;
    } else {
        $error = 'Failed to create post.';
    }
}

// Handle post update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_post'])) {
    $post_id = (int)$_POST['post_id'];
    $post_data = [
        'title' => $_POST['title'] ?? '',
        'slug' => $_POST['slug'] ?? '',
        'content' => $_POST['content'] ?? '',
        'excerpt' => $_POST['excerpt'] ?? '',
        'author' => $_POST['author'] ?? 'Admin',
        'status' => $_POST['status'] ?? 'draft',
        'featured_image' => $_POST['featured_image'] ?? '',
        'categories' => $_POST['categories'] ?? [],
        'tags' => !empty($_POST['tags']) ? array_map('trim', explode(',', $_POST['tags'])) : []
    ];
    
    if (update_post($post_id, $post_data)) {
        header('Location: blog.php?edit=' . $post_id . '&success=updated');
        exit;
    } else {
        $error = 'Failed to update post.';
    }
}

// Handle post deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_post'])) {
    $post_id = (int)$_POST['post_id'];
    if (delete_post($post_id)) {
        header('Location: blog.php?success=deleted');
        exit;
    } else {
        $error = 'Failed to delete post.';
    }
}

// Get editing post if specified
$editing_post = null;
if (isset($_GET['edit'])) {
    $editing_post = get_post_by_id((int)$_GET['edit']);
}

// Get all posts for listing
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 20;
$offset = ($page - 1) * $per_page;

$posts = get_posts([
    'limit' => $per_page,
    'offset' => $offset,
    'order_by' => 'updated_at',
    'order_dir' => 'DESC'
]);

$total_posts = get_posts_count();
$total_pages = ceil($total_posts / $per_page);

// Get all categories
$all_categories = get_all_categories();

$page_title = 'Blog Manager';
include 'includes/admin-header.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog Manager - <?php echo SITE_NAME; ?></title>
    
    <!-- TinyMCE -->
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
        .post-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .post-item {
            padding: 15px;
            border: 2px solid #e5e7eb;
            border-radius: 5px;
            margin-bottom: 10px;
            transition: all 0.3s;
            cursor: pointer;
        }
        .post-item:hover {
            border-color: #667eea;
            background: #f9fafb;
        }
        .post-item.active {
            border-color: #667eea;
            background: #eef2ff;
        }
        .post-title {
            font-weight: 600;
            color: #333;
            margin-bottom: 5px;
        }
        .post-meta {
            font-size: 12px;
            color: #6b7280;
            margin-top: 5px;
        }
        .status-badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            margin-left: 8px;
        }
        .badge-published {
            background: #d1fae5;
            color: #065f46;
        }
        .badge-draft {
            background: #fed7aa;
            color: #92400e;
        }
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #9ca3af;
        }
        .editor-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .pagination {
            display: flex;
            gap: 10px;
            justify-content: center;
            margin-top: 20px;
        }
        .pagination a, .pagination span {
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            text-decoration: none;
            color: #333;
        }
        .pagination a:hover {
            background: #f3f4f6;
        }
        .pagination .active {
            background: #667eea;
            color: white;
            border-color: #667eea;
        }
        .stats {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            margin-bottom: 20px;
        }
        .stat-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
        }
        .stat-number {
            font-size: 32px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .stat-label {
            font-size: 14px;
            opacity: 0.9;
        }
        .tag-input {
            display: flex;
            flex-wrap: wrap;
            gap: 5px;
            padding: 8px;
            border: 2px solid #e0e0e0;
            border-radius: 5px;
            min-height: 42px;
        }
        .tag-item {
            background: #667eea;
            color: white;
            padding: 4px 10px;
            border-radius: 3px;
            font-size: 13px;
            display: flex;
            align-items: center;
            gap: 5px;
        }
    </style>
</head>
<body>
    
    <div class="container">
        <div class="page-header">
            <h2>📝 Blog Manager</h2>
            <div>
                <?php if (!$editing_post): ?>
                    <button type="button" onclick="showNewPostForm()" class="btn btn-primary">➕ New Post</button>
                <?php else: ?>
                    <a href="blog.php" class="btn btn-secondary">← Back to List</a>
                <?php endif; ?>
            </div>
        </div>
        
        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success">
                <?php
                switch ($_GET['success']) {
                    case 'created':
                        echo 'Post created successfully!';
                        break;
                    case 'updated':
                        echo 'Post updated successfully!';
                        break;
                    case 'deleted':
                        echo 'Post deleted successfully!';
                        break;
                }
                ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <?php if (!$editing_post): ?>
            <!-- Statistics -->
            <div class="stats">
                <div class="stat-card">
                    <div class="stat-number"><?php echo get_posts_count(); ?></div>
                    <div class="stat-label">Total Posts</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?php echo get_posts_count('published'); ?></div>
                    <div class="stat-label">Published</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?php echo get_posts_count('draft'); ?></div>
                    <div class="stat-label">Drafts</div>
                </div>
            </div>
            
            <!-- Posts List -->
            <div class="section">
                <h3 class="section-title">All Posts</h3>
                
                <?php if (empty($posts)): ?>
                    <div class="empty-state">
                        <div style="font-size: 48px; margin-bottom: 15px;">📄</div>
                        <p>No blog posts yet.</p>
                        <p style="font-size: 14px; margin-top: 10px;">Create your first blog post to get started!</p>
                        <button type="button" onclick="showNewPostForm()" class="btn btn-primary" style="margin-top: 20px;">Create First Post</button>
                    </div>
                <?php else: ?>
                    <ul class="post-list">
                        <?php foreach ($posts as $post): ?>
                            <li class="post-item" onclick="window.location.href='blog.php?edit=<?php echo $post['id']; ?>'">
                                <div class="post-title">
                                    <?php echo htmlspecialchars($post['title'] ?: '(Untitled)'); ?>
                                    <span class="status-badge badge-<?php echo $post['status']; ?>">
                                        <?php echo strtoupper($post['status']); ?>
                                    </span>
                                </div>
                                <div class="post-meta">
                                    <?php if ($post['published_at']): ?>
                                        Published: <?php echo date('M j, Y g:i A', strtotime($post['published_at'])); ?>
                                    <?php else: ?>
                                        Updated: <?php echo date('M j, Y g:i A', strtotime($post['updated_at'])); ?>
                                    <?php endif; ?>
                                    • Author: <?php echo htmlspecialchars($post['author']); ?>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                    
                    <?php if ($total_pages > 1): ?>
                        <div class="pagination">
                            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                <?php if ($i === $page): ?>
                                    <span class="active"><?php echo $i; ?></span>
                                <?php else: ?>
                                    <a href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                                <?php endif; ?>
                            <?php endfor; ?>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <!-- Edit Post Form -->
            <form method="POST" id="post-form">
                <input type="hidden" name="post_id" value="<?php echo $editing_post['id']; ?>">
                
                <div class="content-grid">
                    <!-- Main Content -->
                    <div>
                        <div class="section" style="margin-bottom: 20px;">
                            <div class="form-group">
                                <label class="form-label">Title</label>
                                <input type="text" name="title" class="form-control" 
                                       value="<?php echo htmlspecialchars($editing_post['title']); ?>" 
                                       placeholder="Enter post title" required>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">Slug (URL)</label>
                                <input type="text" name="slug" class="form-control" 
                                       value="<?php echo htmlspecialchars($editing_post['slug']); ?>" 
                                       placeholder="post-url-slug">
                                <div class="form-hint">Leave empty to auto-generate from title</div>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">Excerpt</label>
                                <textarea name="excerpt" class="form-control" rows="3"
                                          placeholder="Brief summary of the post"><?php echo htmlspecialchars($editing_post['excerpt']); ?></textarea>
                                <div class="form-hint">Short description for previews and SEO</div>
                            </div>
                        </div>
                        
                        
                    </div>
                    
                    <!-- Sidebar -->
                    <div>
                        <div class="section" style="margin-bottom: 20px;">
                            <h3 class="section-title">Publish</h3>
                            
                            <div class="form-group">
                                <label class="form-label">Status</label>
                                <select name="status" class="form-control">
                                    <option value="draft" <?php echo $editing_post['status'] === 'draft' ? 'selected' : ''; ?>>Draft</option>
                                    <option value="published" <?php echo $editing_post['status'] === 'published' ? 'selected' : ''; ?>>Published</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">Author</label>
                                <input type="text" name="author" class="form-control" 
                                       value="<?php echo htmlspecialchars($editing_post['author']); ?>">
                            </div>
                            
                            <div style="display: flex; gap: 10px;">
                                <button type="submit" name="update_post" class="btn btn-success" style="flex: 1;">💾 Update Post</button>
                                <button type="button" onclick="confirmDelete()" class="btn btn-danger">🗑️</button>
                            </div>
                        </div>
                        
                        <div class="section" style="margin-bottom: 20px;">
                            <h3 class="section-title">Featured Image</h3>
                            <div class="form-group">
                                <input type="text" name="featured_image" id="featured-image" class="form-control" 
                                       value="<?php echo htmlspecialchars($editing_post['featured_image']); ?>" 
                                       placeholder="/assets/img/post-image.jpg">
                                <div class="form-hint">Enter image path or URL</div>
                            </div>
                            <?php if ($editing_post['featured_image']): ?>
                                <img src="<?php echo htmlspecialchars($editing_post['featured_image']); ?>" 
                                     style="width: 100%; border-radius: 5px; margin-top: 10px;" 
                                     onerror="this.style.display='none'">
                            <?php endif; ?>
                        </div>
                        
                        <div class="section" style="margin-bottom: 20px;">
                            <h3 class="section-title">Categories</h3>
                            <?php if (empty($all_categories)): ?>
                                <p style="color: #6b7280; font-size: 14px;">No categories yet. Create one below:</p>
                                <div class="form-group" style="margin-top: 10px;">
                                    <input type="text" id="new-category" class="form-control" placeholder="New category name">
                                    <button type="button" onclick="addCategory()" class="btn btn-primary btn-small" style="margin-top: 8px;">Add Category</button>
                                </div>
                            <?php else: ?>
                                <?php
                                $post_category_ids = array_column($editing_post['categories'], 'id');
                                foreach ($all_categories as $category):
                                ?>
                                    <label style="display: block; margin-bottom: 8px; cursor: pointer;">
                                        <input type="checkbox" name="categories[]" value="<?php echo $category['id']; ?>"
                                               <?php echo in_array($category['id'], $post_category_ids) ? 'checked' : ''; ?>>
                                        <?php echo htmlspecialchars($category['name']); ?>
                                    </label>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                        
                        <div class="section">
                            <h3 class="section-title">Tags</h3>
                            <div class="form-group">
                                <input type="text" name="tags" class="form-control" 
                                       value="<?php echo htmlspecialchars(implode(', ', array_column($editing_post['tags'], 'name'))); ?>" 
                                       placeholder="fitness, health, workout">
                                <div class="form-hint">Comma-separated tags</div>
                            </div>
                        </div>
                        <div class="section">
                            <h3 class="section-title">Content</h3>
                            <textarea name="content" id="post-content-editor"><?php echo htmlspecialchars($editing_post['content']); ?></textarea>
                        </div>
                    </div>
                </div>
            </form>
            
            <!-- Delete Form (hidden) -->
            <form id="delete-form" method="POST" style="display: none;">
                <input type="hidden" name="post_id" value="<?php echo $editing_post['id']; ?>">
                <input type="hidden" name="delete_post" value="1">
            </form>
        <?php endif; ?>
    </div>
    
    <script>
        function showNewPostForm() {
            // Create a new post with default values
            const form = document.createElement('form');
            form.method = 'POST';
            form.innerHTML = `
                <input type="hidden" name="create_post" value="1">
                <input type="hidden" name="title" value="New Post">
                <input type="hidden" name="status" value="draft">
            `;
            document.body.appendChild(form);
            form.submit();
        }
        
        <?php if ($editing_post): ?>
        function confirmDelete() {
            if (confirm('Are you sure you want to delete this post? This action cannot be undone.')) {
                document.getElementById('delete-form').submit();
            }
        }
        
        // Initialize TinyMCE
        const baseUrl = window.location.protocol + '//' + window.location.host;
        
        tinymce.init({
            selector: '#post-content-editor',
            height: 600,
            menubar: true,
            plugins: [
                'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
                'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
                'insertdatetime', 'media', 'table', 'help', 'wordcount'
            ],
            toolbar: 'undo redo | blocks | bold italic forecolor backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image media | removeformat code | preview fullscreen',
            
            content_css: [
                baseUrl + '/assets/css/bootstrap-grid.css',
                baseUrl + '/assets/css/style.css'
            ],
            
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
            
            relative_urls: true,
            remove_script_host: true,
            document_base_url: '../'
        });
        <?php endif; ?>
    </script>
</body>
</html>
<?php include 'includes/admin-footer.php'; ?>
