<?php
/**
 * Admin Help & Documentation
 */
require_once '../config.php';
require_once '../functions.php';
require_login();

$page_title = 'Help & Documentation';
include 'includes/admin-header.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Help - <?php echo SITE_NAME; ?></title>
    
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
            max-width: 1200px;
            margin: 30px auto;
            padding: 0 20px;
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
        
        .help-nav {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .help-nav-title {
            font-size: 16px;
            font-weight: 600;
            color: #333;
            margin-bottom: 15px;
        }
        .help-nav-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 10px;
        }
        .help-nav-item {
            padding: 12px 15px;
            background: #f8f9fa;
            border-radius: 5px;
            text-decoration: none;
            color: #667eea;
            font-weight: 500;
            transition: all 0.3s;
            border: 2px solid transparent;
        }
        .help-nav-item:hover {
            background: #eef2ff;
            border-color: #667eea;
        }
        
        .help-section {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        .help-section h2 {
            color: #333;
            font-size: 24px;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 3px solid #667eea;
        }
        .help-section h3 {
            color: #444;
            font-size: 18px;
            margin: 25px 0 15px 0;
        }
        .help-section h4 {
            color: #555;
            font-size: 16px;
            margin: 20px 0 10px 0;
        }
        .help-section p {
            line-height: 1.6;
            color: #666;
            margin-bottom: 15px;
        }
        .help-section ul, .help-section ol {
            margin: 15px 0;
            padding-left: 30px;
        }
        .help-section li {
            line-height: 1.8;
            color: #666;
            margin-bottom: 8px;
        }
        
        .info-box {
            background: #e3f2fd;
            border-left: 4px solid #2196f3;
            padding: 15px 20px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .info-box.warning {
            background: #fff3cd;
            border-left-color: #ffc107;
        }
        .info-box.success {
            background: #d4edda;
            border-left-color: #28a745;
        }
        .info-box.danger {
            background: #f8d7da;
            border-left-color: #dc3545;
        }
        .info-box strong {
            display: block;
            margin-bottom: 8px;
            color: #333;
        }
        
        code {
            background: #f5f5f5;
            padding: 2px 6px;
            border-radius: 3px;
            font-family: 'Courier New', monospace;
            font-size: 13px;
            color: #e83e8c;
        }
        pre {
            background: #282a36;
            color: #f8f8f2;
            padding: 15px;
            border-radius: 5px;
            overflow-x: auto;
            margin: 15px 0;
        }
        pre code {
            background: none;
            color: #f8f8f2;
            padding: 0;
        }
        
        .feature-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin: 20px 0;
        }
        .feature-card {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            border-left: 4px solid #667eea;
        }
        .feature-card h4 {
            margin: 0 0 10px 0;
            color: #333;
        }
        .feature-card p {
            margin: 0;
            font-size: 14px;
            color: #666;
        }
        
        .keyboard-shortcut {
            display: inline-block;
            background: #333;
            color: white;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 12px;
            font-family: monospace;
            margin: 0 3px;
        }
    </style>
</head>
<body>
    
    <div class="container">
        <div class="page-header">
            <h2>📚 Help & Documentation</h2>
        </div>
        
        <!-- Quick Navigation -->
        <div class="help-nav">
            <div class="help-nav-title">Quick Links</div>
            <div class="help-nav-grid">
                <a href="#getting-started" class="help-nav-item">🚀 Getting Started</a>
                <a href="#content-editing" class="help-nav-item">✏️ Editing Content</a>
                <a href="#blog-system" class="help-nav-item">📝 Blog Management</a>
                <a href="#components" class="help-nav-item">🧩 Components</a>
                <a href="#menu-system" class="help-nav-item">🔗 Menu System</a>
                <a href="#images" class="help-nav-item">🖼️ Images</a>
                <a href="#settings" class="help-nav-item">⚙️ Settings</a>
                <a href="#troubleshooting" class="help-nav-item">🔧 Troubleshooting</a>
            </div>
        </div>
        
        <!-- Getting Started -->
        <div class="help-section" id="getting-started">
            <h2>🚀 Getting Started</h2>
            
            <div class="info-box danger">
                <strong>⚠️ IMPORTANT - Change Your Password!</strong>
                If you're still using the default password, go to <a href="settings.php">Settings</a> and change it immediately for security.
            </div>
            
            <h3>Your Dashboard</h3>
            <p>The dashboard shows an overview of your website:</p>
            <ul>
                <li><strong>Quick Stats</strong> - Number of pages, blog posts, comments, and images</li>
                <li><strong>Recent Blog Posts</strong> - Latest posts with quick edit access</li>
                <li><strong>Recent Comments</strong> - Latest comments requiring moderation</li>
                <li><strong>Quick Actions</strong> - Fast access to common tasks</li>
            </ul>
            
            <h3>Navigation Menu</h3>
            <div class="feature-grid">
                <div class="feature-card">
                    <h4>Dashboard</h4>
                    <p>Overview of your website statistics and activity</p>
                </div>
                <div class="feature-card">
                    <h4>Content</h4>
                    <p>Edit page content, create new pages, and manage SEO</p>
                </div>
                <div class="feature-card">
                    <h4>Blog</h4>
                    <p>Manage blog posts, categories, and publication</p>
                </div>
                <div class="feature-card">
                    <h4>Comments</h4>
                    <p>Moderate, approve, and manage blog comments</p>
                </div>
                <div class="feature-card">
                    <h4>Components</h4>
                    <p>Create reusable HTML/PHP components</p>
                </div>
                <div class="feature-card">
                    <h4>Menu</h4>
                    <p>Manage site navigation and menu items</p>
                </div>
                <div class="feature-card">
                    <h4>Uploads</h4>
                    <p>Upload and manage images and files</p>
                </div>
                <div class="feature-card">
                    <h4>Settings</h4>
                    <p>Change password and account settings</p>
                </div>
            </div>
        </div>
        
        <!-- Content Editing -->
        <div class="help-section" id="content-editing">
            <h2>✏️ Editing Content</h2>
            
            <h3>How to Edit a Page</h3>
            <ol>
                <li>Click <strong>Content</strong> in the navigation menu</li>
                <li>Select a page from the dropdown or click on a recent page</li>
                <li>Edit the content in the visual editor (TinyMCE)</li>
                <li>Update SEO settings (title, description, keywords)</li>
                <li>Click <strong>💾 Save Changes</strong></li>
                <li>Use <strong>👁️ Preview</strong> to see your changes live</li>
            </ol>
            
            <h3>Creating a New Page</h3>
            <ol>
                <li>Go to <strong>Content</strong></li>
                <li>Click <strong>➕ Create Page</strong></li>
                <li>Enter a page name (e.g., "gallery", "team", "pricing")</li>
                <li>Click <strong>Create Page</strong></li>
                <li>The new page will open for editing</li>
            </ol>
            
            <div class="info-box">
                <strong>💡 Page Naming Rules:</strong>
                Use only letters, numbers, hyphens, and underscores. No spaces or special characters.
            </div>
            
            <h3>Rich Text Editor Features</h3>
            <ul>
                <li><strong>Text Formatting</strong> - Bold, italic, underline, colors</li>
                <li><strong>Headings</strong> - H1, H2, H3, etc. for structure</li>
                <li><strong>Lists</strong> - Bullet points and numbered lists</li>
                <li><strong>Links</strong> - Add hyperlinks to text or buttons</li>
                <li><strong>Images</strong> - Insert and resize images</li>
                <li><strong>Media</strong> - Embed videos and audio</li>
                <li><strong>Tables</strong> - Create data tables</li>
                <li><strong>Code View</strong> - Edit HTML directly</li>
            </ul>
            
            <h3>SEO Settings</h3>
            <h4>Basic SEO</h4>
            <ul>
                <li><strong>Page Title</strong> - Appears in browser tab and search results (50-60 chars)</li>
                <li><strong>Meta Description</strong> - Summary for search results (150-160 chars)</li>
                <li><strong>Meta Keywords</strong> - Comma-separated keywords</li>
            </ul>
            
            <h4>Open Graph (Social Media)</h4>
            <ul>
                <li><strong>OG Title</strong> - Title when shared on social media</li>
                <li><strong>OG Description</strong> - Description for social shares</li>
                <li><strong>OG Image</strong> - Image displayed in social media posts (1200x630px recommended)</li>
            </ul>
            
            <h3>Deleting a Page</h3>
            <ol>
                <li>Go to <strong>Content</strong> and select the page</li>
                <li>Click <strong>🗑️ Delete Page</strong></li>
                <li>Confirm deletion - this removes:
                    <ul>
                        <li>Content file (./content/page.php.json)</li>
                        <li>SEO data (./json/page.php.json)</li>
                        <li>Custom files (./custom/page.php and page.js)</li>
                    </ul>
                </li>
            </ol>
            
            <div class="info-box warning">
                <strong>⚠️ Warning:</strong>
                Page deletion cannot be undone. Make sure you have backups if needed.
            </div>
            
            <h3>Editor Keyboard Shortcuts</h3>
            <p>Speed up your editing with these shortcuts:</p>
            <ul>
                <li><span class="keyboard-shortcut">Ctrl+S</span> / <span class="keyboard-shortcut">Cmd+S</span> - Save changes</li>
                <li><span class="keyboard-shortcut">Ctrl+B</span> - Bold text</li>
                <li><span class="keyboard-shortcut">Ctrl+I</span> - Italic text</li>
                <li><span class="keyboard-shortcut">Ctrl+U</span> - Underline text</li>
                <li><span class="keyboard-shortcut">Ctrl+K</span> - Insert link</li>
                <li><span class="keyboard-shortcut">Ctrl+Z</span> - Undo</li>
                <li><span class="keyboard-shortcut">Ctrl+Y</span> - Redo</li>
            </ul>
        </div>
        
        <!-- Blog System -->
        <div class="help-section" id="blog-system">
            <h2>📝 Blog Management</h2>
            
            <h3>Creating a Blog Post</h3>
            <ol>
                <li>Go to <strong>Blog</strong></li>
                <li>Click <strong>➕ Create New Post</strong></li>
                <li>Enter a title (the slug/URL is generated automatically)</li>
                <li>Write your content in the editor</li>
                <li>Add an excerpt (optional short summary)</li>
                <li>Select categories</li>
                <li>Upload a featured image</li>
                <li>Choose status: <strong>Draft</strong> or <strong>Published</strong></li>
                <li>Click <strong>Save Post</strong></li>
            </ol>
            
            <h3>Post Status</h3>
            <ul>
                <li><strong>Draft</strong> - Not visible to public, can be edited and previewed</li>
                <li><strong>Published</strong> - Live on your website, visible to all visitors</li>
            </ul>
            
            <h3>Managing Categories</h3>
            <ol>
                <li>In the Blog page, scroll to <strong>Categories</strong> section</li>
                <li>Enter a new category name</li>
                <li>Click <strong>Add Category</strong></li>
                <li>Categories can be assigned to multiple posts</li>
            </ol>
            
            <h3>Featured Images</h3>
            <p>Each post can have a featured image that appears:</p>
            <ul>
                <li>In blog listings</li>
                <li>At the top of the post</li>
                <li>In social media shares</li>
            </ul>
            <p><strong>Recommended size:</strong> 1200x630px for best results</p>
            
            <h3>Comments Management</h3>
            <p>Go to <strong>Comments</strong> to:</p>
            <ul>
                <li>View all comments with author info and timestamps</li>
                <li>Approve pending comments</li>
                <li>Delete spam or inappropriate comments</li>
                <li>See which post each comment belongs to</li>
            </ul>
        </div>
        
        <!-- Components -->
        <div class="help-section" id="components">
            <h2>🧩 Components System</h2>
            
            <h3>What Are Components?</h3>
            <p>Components are reusable code snippets that you can include anywhere in your content. Think of them as building blocks you can place in multiple pages.</p>
            
            <h3>Creating a Component</h3>
            <ol>
                <li>Go to <strong>Components</strong></li>
                <li>Click <strong>➕ Create Component</strong></li>
                <li>Enter a component name (e.g., "contact-form", "social-links")</li>
                <li>Choose type:
                    <ul>
                        <li><strong>HTML</strong> - Static content</li>
                        <li><strong>PHP</strong> - Dynamic content with logic</li>
                    </ul>
                </li>
                <li>Click <strong>Create Component</strong></li>
            </ol>
            
            <h3>Using Components in Content</h3>
            <p>Include a component using double curly braces:</p>
            <pre><code>{{contact-form}}
{{social-links}}
{{newsletter-signup}}</code></pre>
            
            <h3>Example: Social Links Component</h3>
            <p><strong>File:</strong> contact-form.html</p>
            <pre><code>&lt;ul class="social-list"&gt;
    &lt;li&gt;&lt;a href="https://facebook.com"&gt;&lt;i class="fa fa-facebook"&gt;&lt;/i&gt;&lt;/a&gt;&lt;/li&gt;
    &lt;li&gt;&lt;a href="https://twitter.com"&gt;&lt;i class="fa fa-twitter"&gt;&lt;/i&gt;&lt;/a&gt;&lt;/li&gt;
    &lt;li&gt;&lt;a href="https://instagram.com"&gt;&lt;i class="fa fa-instagram"&gt;&lt;/i&gt;&lt;/a&gt;&lt;/li&gt;
&lt;/ul&gt;</code></pre>
            
            <h3>PHP Components</h3>
            <p>PHP components can contain logic, process forms, and access databases:</p>
            <pre><code>&lt;?php
// Process contact form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    // Handle form submission
}
?&gt;
&lt;form method="POST"&gt;
    &lt;input type="text" name="name" required&gt;
    &lt;input type="email" name="email" required&gt;
    &lt;button type="submit"&gt;Submit&lt;/button&gt;
&lt;/form&gt;</code></pre>
            
            <h3>Component Benefits</h3>
            <ul>
                <li>✅ Write once, use everywhere</li>
                <li>✅ Update one file, changes apply to all pages</li>
                <li>✅ Keep content clean and organized</li>
                <li>✅ Supports JavaScript and CSS</li>
                <li>✅ PHP components can be dynamic</li>
            </ul>
            
            <h3>Editing Components</h3>
            <p>The component editor includes:</p>
            <ul>
                <li><strong>Syntax Highlighting</strong> - Color-coded for easy reading</li>
                <li><strong>Line Numbers</strong> - Navigate code easily</li>
                <li><strong>Auto-completion</strong> - Tag and bracket closing</li>
                <li><strong>Keyboard Shortcuts</strong> - <span class="keyboard-shortcut">Ctrl+S</span> to save</li>
            </ul>
        </div>
        
        <!-- Menu System -->
        <div class="help-section" id="menu-system">
            <h2>🔗 Menu Management</h2>
            
            <h3>Adding Menu Items</h3>
            <ol>
                <li>Go to <strong>Menu</strong></li>
                <li>Fill in the form:
                    <ul>
                        <li><strong>Menu Label</strong> - Text displayed in navigation</li>
                        <li><strong>URL</strong> - Page name without .php (e.g., "about", "contact")</li>
                        <li><strong>Parent Menu</strong> - Leave blank for top-level, or select parent for submenu</li>
                    </ul>
                </li>
                <li>Click <strong>Add Menu Item</strong></li>
            </ol>
            
            <h3>Creating Submenus</h3>
            <p>To create a dropdown submenu:</p>
            <ol>
                <li>First, create the parent menu item (e.g., "Services")</li>
                <li>Then create child items and select the parent from the dropdown</li>
                <li>Child items will be indented and show as "(child of Parent)"</li>
            </ol>
            
            <div class="info-box success">
                <strong>✨ Visual Hierarchy:</strong>
                Submenu items are automatically indented and have a different colored border to show the hierarchy clearly.
            </div>
            
            <h3>Reordering Menu Items</h3>
            <ol>
                <li>Click and hold the <strong>☰</strong> drag handle</li>
                <li>Drag the item to its new position</li>
                <li>Release to save the new order</li>
            </ol>
            
            <h3>Editing Menu Items</h3>
            <ol>
                <li>Click the <strong>✏️ Edit</strong> button</li>
                <li>Update the label, URL, or parent</li>
                <li>Click <strong>Save Changes</strong></li>
            </ol>
            
            <h3>Deleting Menu Items</h3>
            <ol>
                <li>Click <strong>🗑️ Delete</strong></li>
                <li>Confirm deletion</li>
            </ol>
        </div>
        
        <!-- Images -->
        <div class="help-section" id="images">
            <h2>🖼️ Image Management</h2>
            
            <h3>Uploading Images</h3>
            <ol>
                <li>Go to <strong>Uploads</strong></li>
                <li>Either:
                    <ul>
                        <li>Click the upload area and select files</li>
                        <li>Drag and drop images onto the page</li>
                    </ul>
                </li>
                <li>Wait for "Upload successful" confirmation</li>
                <li>Your images are now available to use</li>
            </ol>
            
            <h3>Using Uploaded Images</h3>
            <h4>In Content Editor:</h4>
            <ol>
                <li>Upload your image first</li>
                <li>In the editor, click where you want the image</li>
                <li>Click the image icon in the toolbar</li>
                <li>The upload dialog will appear</li>
                <li>Upload directly or enter the image path</li>
            </ol>
            
            <h4>For Blog Posts:</h4>
            <ol>
                <li>When creating/editing a post</li>
                <li>In the Featured Image section, click <strong>Choose File</strong></li>
                <li>Select your image</li>
                <li>The image will be uploaded and set as featured</li>
            </ol>
            
            <h3>Image Best Practices</h3>
            <ul>
                <li><strong>File Size:</strong> Keep under 5MB for faster loading</li>
                <li><strong>Dimensions:</strong>
                    <ul>
                        <li>Hero images: 1920x1080px</li>
                        <li>Blog featured: 1200x630px</li>
                        <li>Thumbnails: 400x300px</li>
                    </ul>
                </li>
                <li><strong>Format:</strong>
                    <ul>
                        <li>JPG for photos</li>
                        <li>PNG for logos/graphics with transparency</li>
                        <li>WebP for modern browsers (best compression)</li>
                    </ul>
                </li>
                <li><strong>Naming:</strong> Use descriptive names (e.g., "team-photo-2024.jpg")</li>
            </ul>
            
            <h3>Viewing Images</h3>
            <p>The uploads page shows:</p>
            <ul>
                <li>Thumbnail preview</li>
                <li>File name and size</li>
                <li>Upload date</li>
                <li>Image dimensions</li>
                <li>Copy path button for easy insertion</li>
            </ul>
        </div>
        
        <!-- Settings -->
        <div class="help-section" id="settings">
            <h2>⚙️ Account Settings</h2>
            
            <h3>Changing Your Password</h3>
            <ol>
                <li>Go to <strong>Settings</strong></li>
                <li>Scroll to "Change Password" section</li>
                <li>Enter your current password</li>
                <li>Enter your new password (minimum 6 characters)</li>
                <li>Confirm your new password</li>
                <li>Click <strong>🔐 Change Password</strong></li>
            </ol>
            
            <div class="info-box">
                <strong>🛡️ Password Security Tips:</strong>
                <ul style="margin: 10px 0 0 20px;">
                    <li>Use at least 8-12 characters</li>
                    <li>Include letters, numbers, and symbols</li>
                    <li>Don't reuse passwords from other accounts</li>
                    <li>Change your password periodically</li>
                </ul>
            </div>
            
            <h3>Updating Profile</h3>
            <p>You can update:</p>
            <ul>
                <li><strong>Username</strong> - Your login username</li>
                <li><strong>Email</strong> - For password recovery (optional)</li>
            </ul>
            
            <h3>Account Information</h3>
            <p>View your:</p>
            <ul>
                <li>User ID</li>
                <li>Account creation date</li>
                <li>Current username and email</li>
            </ul>
        </div>
        
        <!-- Troubleshooting -->
        <div class="help-section" id="troubleshooting">
            <h2>🔧 Troubleshooting</h2>
            
            <h3>Common Issues</h3>
            
            <h4>Can't Log In</h4>
            <ul>
                <li>Check username spelling (case-sensitive)</li>
                <li>Verify password is correct</li>
                <li>Clear browser cache and cookies</li>
                <li>Try a different browser</li>
            </ul>
            
            <h4>Forgot Password</h4>
            <div class="info-box warning">
                <strong>Password Reset:</strong>
                This system requires server access to reset passwords. Contact your system administrator or:
                <ol style="margin: 10px 0 0 20px;">
                    <li>Access your server via FTP/SSH</li>
                    <li>Navigate to <code>/database/</code></li>
                    <li>Run: <code>php init-users.php</code></li>
                    <li>This creates a new admin account with default credentials</li>
                </ol>
            </div>
            
            <h4>Changes Not Saving</h4>
            <ul>
                <li>Check file permissions on <code>/content/</code> and <code>/database/</code> folders</li>
                <li>Ensure sufficient disk space</li>
                <li>Check PHP error logs</li>
                <li>Verify database is writable</li>
            </ul>
            
            <h4>Images Won't Upload</h4>
            <ul>
                <li>Check file size (maximum 5MB by default)</li>
                <li>Verify <code>/assets/img/</code> folder permissions</li>
                <li>Ensure file format is supported (JPG, PNG, GIF, WebP)</li>
                <li>Check PHP upload settings in php.ini</li>
            </ul>
            
            <h4>Editor Not Loading</h4>
            <ul>
                <li>Check internet connection (editor loads from CDN)</li>
                <li>Disable browser extensions</li>
                <li>Clear browser cache</li>
                <li>Try incognito/private mode</li>
            </ul>
            
            <h4>Component Not Showing</h4>
            <ul>
                <li>Verify component file exists in <code>/components/</code></li>
                <li>Check component name matches exactly (case-sensitive)</li>
                <li>Ensure syntax is correct: <code>{{component-name}}</code></li>
                <li>For PHP components, check for syntax errors</li>
            </ul>
            
            <h3>Getting Help</h3>
            <p>If you continue experiencing issues:</p>
            <ol>
                <li>Check the <code>/docs/</code> folder for detailed documentation</li>
                <li>Review PHP error logs for specific error messages</li>
                <li>Ensure your server meets requirements (PHP 7.4+, SQLite)</li>
                <li>Contact your system administrator or developer</li>
            </ol>
            
            <h3>File Structure Reference</h3>
            <pre><code>/admin/              Admin panel files
/content/            Page content (JSON)
/json/               SEO metadata (JSON)
/components/         Reusable components
/custom/             Custom PHP/JS files
/database/           SQLite database
/assets/             CSS, JS, images
/template/           Original HTML templates
/docs/               Documentation files
config.php           Configuration
functions.php        Helper functions
blog-functions.php   Blog system functions</code></pre>
        </div>
        
        <!-- Quick Reference -->
        <div class="help-section">
            <h2>📋 Quick Reference</h2>
            
            <div class="feature-grid">
                <div class="feature-card">
                    <h4>📄 Edit Page</h4>
                    <p>Content → Select page → Edit → Save</p>
                </div>
                <div class="feature-card">
                    <h4>➕ Create Page</h4>
                    <p>Content → Create Page → Enter name</p>
                </div>
                <div class="feature-card">
                    <h4>📝 New Blog Post</h4>
                    <p>Blog → Create Post → Write → Publish</p>
                </div>
                <div class="feature-card">
                    <h4>💬 Moderate Comment</h4>
                    <p>Comments → Review → Approve/Delete</p>
                </div>
                <div class="feature-card">
                    <h4>🧩 Add Component</h4>
                    <p>Components → Create → Edit code</p>
                </div>
                <div class="feature-card">
                    <h4>🔗 Update Menu</h4>
                    <p>Menu → Add item → Drag to reorder</p>
                </div>
                <div class="feature-card">
                    <h4>🖼️ Upload Image</h4>
                    <p>Uploads → Drop file → Copy path</p>
                </div>
                <div class="feature-card">
                    <h4>🔐 Change Password</h4>
                    <p>Settings → Change Password → Save</p>
                </div>
            </div>
        </div>
        
    </div>
    
</body>
</html>
<?php include 'includes/admin-footer.php'; ?>
