# QUICK START GUIDE

## Immediate Actions Required

### 1. Change Admin Password (CRITICAL!)
Open `config.php` and change line 8:
```php
define('ADMIN_PASSWORD', password_hash('YOUR-NEW-PASSWORD-HERE', PASSWORD_DEFAULT));
```

### 2. Set Folder Permissions
```bash
chmod 755 content/
chmod 755 uploads/
```

### 3. Login to Admin
- URL: http://yoursite.com/admin/
- Username: admin
- Password: changeme123 (or your new password)

## Making Content Editable

Add `data-editable="unique-name"` to HTML elements:

```html
<h1 data-editable="hero-title">Welcome</h1>
<p data-editable="hero-text">Your text here</p>
```

## File Structure

```
/admin/          - Admin panel (login here)
/template/       - Your original HTML templates
/content/        - Edited content (JSON files)
/uploads/        - Uploaded images
config.php       - Settings (CHANGE PASSWORD!)
.htaccess        - Server configuration
```

## Common Tasks

**Upload Image:**
1. Admin → Manage Images
2. Drag/drop or click to upload
3. Copy the path (e.g., "uploads/image.jpg")

**Edit Content:**
1. Admin → Select page
2. Edit in visual editor
3. Save changes

**Add Editable Zone:**
1. Open template HTML file
2. Add: `data-editable="zone-name"` to any tag
3. Refresh admin panel
4. Edit the new zone

## Troubleshooting

**Can't save content?**
- Check folder permissions: `chmod 755 content/ uploads/`

**Pages not loading?**
- Verify `.htaccess` exists
- Check Apache mod_rewrite is enabled

**Images won't upload?**
- Check `uploads/` permissions
- Verify PHP upload limit (5MB default)

## Full Documentation
See README.md for complete documentation
