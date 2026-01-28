# Simple Flat-File CMS

A lightweight, database-free content management system perfect for small static websites. Designed for non-technical clients who need to update content without touching code.

## ✨ Features

- **No Database Required** - All content stored in JSON files
- **Simple Login System** - Single admin authentication
- **WYSIWYG Editor** - User-friendly TinyMCE editor
- **Image Upload** - Drag & drop image management
- **Editable Zones** - Mark any HTML element as editable
- **cPanel Compatible** - Works on standard PHP hosting
- **Secure** - Protected admin area with session management

## 📋 Requirements

- PHP 7.4 or higher
- Apache web server with mod_rewrite
- File write permissions for content and uploads folders

## 🚀 Quick Start

### 1. Installation

1. Upload all files to your web server
2. Ensure `content/` and `uploads/` folders are writable (755 or 775)
3. Create `.htaccess` file (see below)
4. **IMPORTANT:** Change admin password in `config.php`

### 2. Configure Admin Access

Edit `config.php` and change the default credentials:

```php
define('ADMIN_USERNAME', 'your-username');
define('ADMIN_PASSWORD', password_hash('your-secure-password', PASSWORD_DEFAULT));
```

To generate a new password hash, run this PHP command:
```php
php -r "echo password_hash('YourNewPassword', PASSWORD_DEFAULT);"
```

### 3. Access Admin Panel

- **Admin URL:** `yoursite.com/admin/`
- **Default Username:** admin
- **Default Password:** changeme123 (CHANGE THIS!)

## 🎨 Making Content Editable

To make content editable by clients, add the `data-editable` attribute to HTML elements:

### Example 1: Simple Text
```html
<h1 data-editable="page-title">Welcome to Our Gym</h1>
<p data-editable="intro-text">We offer the best fitness programs in town.</p>
```

### Example 2: Phone Number
```html
<div data-editable="contact-phone">
    <i class="fa fa-phone"></i>
    <a href="tel:18004886040">1-800-488-6040</a>
</div>
```

### Example 3: Business Hours
```html
<p data-editable="business-hours">
    Mon - Fri: 8:00AM - 7:00PM | Sat - Sun: Closed
</p>
```

### Example 4: Service Card
```html
<div class="service-card">
    <h3 data-editable="service1-title">Personal Training</h3>
    <p data-editable="service1-description">One-on-one training sessions tailored to your goals.</p>
</div>
```

## 📁 File Structure

```
/
├── admin/              # Admin panel files
│   ├── index.php       # Dashboard
│   ├── login.php       # Login page
│   ├── edit.php        # Content editor
│   ├── uploads.php     # Image manager
│   └── logout.php      # Logout handler
├── template/           # Original HTML templates
├── content/            # JSON content files (auto-created)
├── uploads/            # Uploaded images (auto-created)
├── config.php          # Configuration
├── functions.php       # Helper functions
├── index.php           # Main router
└── .htaccess           # Apache configuration
```

## 🔧 .htaccess Configuration

Create a `.htaccess` file in your root directory:

```apache
# Enable rewrite engine
RewriteEngine On
RewriteBase /

# Protect content and config files
<FilesMatch "(config|functions)\.php$">
    Order allow,deny
    Deny from all
</FilesMatch>

# Prevent directory browsing
Options -Indexes

# Route all requests through index.php (except existing files)
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteCond %{REQUEST_URI} !^/admin/
RewriteRule ^(.*)$ index.php [QSA,L]

# Security headers
<IfModule mod_headers.c>
    Header set X-Content-Type-Options "nosniff"
    Header set X-Frame-Options "SAMEORIGIN"
    Header set X-XSS-Protection "1; mode=block"
</IfModule>
```

## 🖼️ Image Management

### Uploading Images

1. Go to Admin → Manage Images
2. Click or drag images to upload
3. Copy the image path (e.g., `uploads/filename.jpg`)
4. Use in your HTML or TinyMCE editor

### Using Images in HTML

```html
<img src="uploads/your-image.jpg" alt="Description">
```

### Using Images in CSS (Background)

```html
<div style="background-image: url(uploads/your-image.jpg);"></div>
```

## 👥 For Clients - Simple Guide

### How to Edit Content

1. **Login:** Go to `yoursite.com/admin/` and enter your credentials
2. **Select Page:** Click "Edit Content" on the page you want to modify
3. **Make Changes:** Edit text in the editor - bold, add links, change colors
4. **Add Images:** Click the image icon in the editor toolbar
5. **Save:** Click "Save Changes" at the top
6. **Preview:** Click "Preview" to see your changes live

### Common Tasks

**Change Phone Number:**
1. Find the "Header Phone" section
2. Update the number
3. Save

**Update Business Hours:**
1. Find the "Header Hours" or "Business Hours" section
2. Edit the text
3. Save

**Change Main Heading:**
1. Find the "Hero Title" or page title section
2. Edit the text (keep any HTML tags like `<span>`)
3. Save

**Add/Change Images:**
1. Go to "Manage Images" from dashboard
2. Upload new image
3. Copy the path
4. In content editor, click image icon
5. Paste the path
6. Save

## 🛠️ Advanced Setup

### Setting Up on cPanel

1. **Upload Files:** Use File Manager or FTP
2. **Set Permissions:**
   - Right-click `content/` → Change Permissions → 755
   - Right-click `uploads/` → Change Permissions → 755
3. **Create .htaccess:** Use File Manager to create the file
4. **Change Password:** Edit `config.php` via File Manager or FTP

### Using Custom Domain

If your site is in a subdirectory (e.g., `yoursite.com/gym/`), update `.htaccess`:

```apache
RewriteBase /gym/
```

### Backup

**Important files to backup regularly:**
- `/content/*.json` (all your edited content)
- `/uploads/*` (all uploaded images)
- `/config.php` (your settings)

## 🔒 Security Best Practices

1. **Change default password immediately**
2. **Use strong passwords** (12+ characters, mixed case, numbers, symbols)
3. **Keep files outside web root** if possible
4. **Regular backups** of content/ and uploads/
5. **Update PHP** to latest stable version
6. **Limit file upload sizes** (currently 5MB)
7. **Use HTTPS** (SSL certificate from cPanel/Let's Encrypt)

## 🐛 Troubleshooting

### "Permission Denied" Errors
```bash
chmod 755 content/
chmod 755 uploads/
```

### Pages Not Loading
- Check `.htaccess` file exists
- Verify `mod_rewrite` is enabled in Apache
- Check PHP error logs in cPanel

### Can't Login
- Clear browser cookies
- Check `config.php` credentials
- Verify session support in PHP

### Images Not Uploading
- Check `uploads/` folder permissions (755 or 775)
- Verify PHP `upload_max_filesize` setting
- Check PHP `post_max_size` setting

### Content Not Saving
- Check `content/` folder permissions (755 or 775)
- Verify PHP has write permissions
- Check PHP error logs

## 💡 Tips for Developers

### Add More Editable Zones

Simply add `data-editable="unique-name"` to any element:

```html
<div data-editable="footer-copyright">© 2026 Your Company</div>
```

### Style the Editor

Customize TinyMCE in `admin/edit.php`:

```javascript
tinymce.init({
    // ... existing config
    content_css: '/path/to/your/custom.css',
    body_class: 'your-custom-class'
});
```

### Add Custom Plugins

Include additional TinyMCE plugins:

```javascript
plugins: [...existing, 'wordcount', 'emoticons'],
toolbar: '...existing... | emoticons'
```

## 📝 Workflow

### For Initial Setup (Developer)

1. Get template from client or purchase one
2. Install CMS files
3. Add `data-editable` attributes to key content areas
4. Test editing functionality
5. Create client account with secure password
6. Provide login details and brief tutorial

### For Clients (Day-to-Day)

1. Login to admin panel
2. Edit content as needed
3. Upload new images when necessary
4. Preview changes
5. Save and view live site

## 🆘 Support

For issues:
1. Check Troubleshooting section above
2. Review PHP error logs in cPanel
3. Check file permissions
4. Verify `.htaccess` configuration

## 📄 License

This is a simple CMS created for client projects. Feel free to modify and use for your projects.

---

**Version:** 1.0  
**Last Updated:** January 2026
