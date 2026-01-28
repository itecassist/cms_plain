# 🚀 INSTALLATION GUIDE - Step-by-Step

## Complete Setup Instructions for Simple Flat-File CMS

---

## 📦 What You'll Need

- [ ] Web hosting with PHP 7.4+ (cPanel hosting works great)
- [ ] FTP client (FileZilla) or cPanel File Manager
- [ ] Your HTML template files
- [ ] 30 minutes of time

---

## 🎯 Installation Steps

### STEP 1: Download/Upload Files

**Option A: If files are on your computer**
1. Use FTP (FileZilla) or cPanel File Manager
2. Upload entire `itec_cms_plain` folder to your web root
   - Usually: `/public_html/` or `/www/` or `/htdocs/`
3. Wait for upload to complete

**Option B: If setting up locally first**
1. Place files in your local server directory (XAMPP/WAMP/MAMP)
2. Access via `localhost/itec_cms_plain/`

---

### STEP 2: Set Folder Permissions

**Using cPanel File Manager:**
1. Navigate to your CMS folder
2. Right-click on `content` folder → Change Permissions
3. Set to `755` (Read, Write, Execute for owner)
4. Check "Recurse into subdirectories"
5. Click "Change Permissions"
6. Repeat for `uploads` folder

**Using FTP (FileZilla):**
1. Right-click `content` folder
2. File Permissions → Set to `755`
3. Repeat for `uploads` folder

**Using SSH/Terminal:**
```bash
cd /path/to/your/cms
chmod 755 content/
chmod 755 uploads/
```

---

### STEP 3: Change Admin Password (CRITICAL!)

1. Open `config.php` in a text editor
2. Find line 8 that says:
   ```php
   define('ADMIN_PASSWORD', password_hash('changeme123', PASSWORD_DEFAULT));
   ```

3. **Generate a password hash:**

   **Method A - Using PHP command line:**
   ```bash
   php -r "echo password_hash('YourNewPassword', PASSWORD_DEFAULT);"
   ```
   
   **Method B - Using online tool:**
   - Google "bcrypt hash generator"
   - Enter your desired password
   - Copy the hash
   
   **Method C - Create temp PHP file:**
   ```php
   <?php echo password_hash('YourNewPassword', PASSWORD_DEFAULT); ?>
   ```
   Upload it, visit it in browser, copy hash, delete file

4. Replace the line with:
   ```php
   define('ADMIN_PASSWORD', '$2y$10$YOUR_HASH_HERE');
   ```

5. Save the file

---

### STEP 4: Create/Upload .htaccess File

**Option A - File already exists:**
- Verify it's uploaded to root directory
- Check content matches the template

**Option B - Create new:**
1. Create file named `.htaccess` (note the dot at start)
2. Copy content from `.htaccess` template
3. Upload to root directory (same level as index.php)

**Important for cPanel users:**
- File Manager may hide files starting with "."
- Click "Settings" → Check "Show Hidden Files"

**If in a subdirectory:**
- Edit `.htaccess` line 3
- Change `RewriteBase /` to `RewriteBase /subdirectory/`

---

### STEP 5: Add Your Templates

1. Place your HTML template files in `/template/` folder
2. Keep original file structure
3. Assets (CSS, JS, images) should stay in their original paths

**Example structure:**
```
template/
  ├── index.htm
  ├── about.html
  ├── services.html
  └── assets/
      ├── css/
      ├── js/
      └── img/
```

---

### STEP 6: Verify Installation

1. Upload `verify-setup.php` to your root directory
2. Visit: `yourwebsite.com/verify-setup.php?confirm=yes`
3. Check all items are green (✓)
4. Fix any red (✗) or yellow (⚠) items
5. **DELETE verify-setup.php** after verification

---

### STEP 7: First Login

1. Go to: `yourwebsite.com/admin/`
2. **Username:** `admin` (or what you set in config.php)
3. **Password:** Your new password (not changeme123!)
4. Click "Login"

**If you can't login:**
- Clear browser cache and cookies
- Check password in config.php
- Verify permissions on folders
- Check PHP error logs in cPanel

---

### STEP 8: Make Content Editable

Now you need to add editable zones to your templates.

**Example - Before:**
```html
<h1>Welcome to Our Gym</h1>
<p>Get fit and stay healthy</p>
```

**Example - After:**
```html
<h1 data-editable="hero-title">Welcome to Our Gym</h1>
<p data-editable="hero-description">Get fit and stay healthy</p>
```

**Tips:**
- Add to any HTML tag: `<div>`, `<p>`, `<h1>`, `<span>`, etc.
- Use descriptive names: `hero-title`, `service1-description`, `footer-phone`
- Don't add to navigation menus or complex layouts
- See CONVERSION_GUIDE.txt for more examples

---

### STEP 9: Test Everything

**Checklist:**
- [ ] Can login to admin panel
- [ ] Can see list of pages
- [ ] Can click "Edit Content" on a page
- [ ] Can see editable zones
- [ ] Can make changes and save
- [ ] Changes appear on live site
- [ ] Can upload images
- [ ] Can logout successfully

---

### STEP 10: Hand Off to Client

**Provide to client:**
1. **Login URL:** yourwebsite.com/admin/
2. **Username:** admin (or custom)
3. **Password:** (Securely share - don't email plain text!)
4. **Documentation:** CLIENT_GUIDE.md
5. **Brief training** (15-30 minutes recommended)

**Training topics to cover:**
- How to login/logout
- How to select and edit pages
- How to use the text editor
- How to upload images
- How to save changes
- What NOT to edit (navigation, scripts)

---

## 🔧 Common Setup Issues

### Issue: "Permission Denied" Error

**Solution:**
```bash
chmod 755 content/
chmod 755 uploads/
```
Or use cPanel File Manager permissions dialog.

---

### Issue: Pages Not Loading / 404 Errors

**Solution:**
1. Check `.htaccess` file exists
2. Verify mod_rewrite is enabled (contact host)
3. Check file permissions on `.htaccess`
4. If in subdirectory, update RewriteBase

---

### Issue: "Cannot write to content directory"

**Solution:**
1. Check folder permissions (755 or 775)
2. Check parent folder permissions
3. Contact hosting support if needed

---

### Issue: Admin Panel Shows "No Pages Found"

**Solution:**
1. Check `/template/` folder has .htm or .html files
2. Verify folder permissions
3. Check file names don't have special characters

---

### Issue: Images Won't Upload

**Solution:**
1. Check `/uploads/` folder permissions (755)
2. Verify PHP upload_max_filesize in php.ini
3. Check file is under 5MB
4. Verify it's a valid image format (jpg, png, gif)

---

### Issue: Changes Not Saving

**Solution:**
1. Check `/content/` folder is writable (755)
2. Check disk space not full
3. Look for JSON syntax errors
4. Check PHP error logs

---

### Issue: "White Screen" or Blank Page

**Solution:**
1. Enable PHP error display:
   ```php
   ini_set('display_errors', 1);
   error_reporting(E_ALL);
   ```
2. Check PHP error logs in cPanel
3. Verify all files uploaded correctly
4. Check PHP version (needs 7.4+)

---

## 🔒 Security Checklist (Post-Installation)

- [x] Changed default admin password
- [x] Set proper folder permissions (755)
- [x] Deleted verify-setup.php
- [ ] Enabled HTTPS/SSL (via Let's Encrypt in cPanel)
- [ ] Changed default admin username (optional)
- [ ] Set up regular backups
- [ ] Documented login credentials securely
- [ ] Added IP restrictions to admin (optional, advanced)

---

## 📋 Quick Command Reference

### Set Permissions
```bash
chmod 755 content/ uploads/
```

### Generate Password Hash
```bash
php -r "echo password_hash('MyPassword', PASSWORD_DEFAULT);"
```

### Check PHP Version
```bash
php -v
```

### View Error Logs (cPanel)
```
cPanel → Errors → Error Log
```

### Backup Content
```bash
tar -czf backup.tar.gz content/ uploads/ config.php
```

---

## 🎓 Next Steps After Installation

1. **Test thoroughly** - Try editing every page
2. **Add more editable zones** - Make more content editable
3. **Upload images** - Add to the uploads folder
4. **Customize styling** - Adjust admin panel if needed
5. **Create documentation** - Note any customizations
6. **Set up backups** - Schedule automatic backups
7. **Train client** - Walk through basic tasks
8. **Monitor** - Check error logs periodically

---

## 📞 Getting Support

**For installation issues:**
1. Check this guide first
2. Review README.md troubleshooting section
3. Check PHP error logs
4. Contact your hosting provider

**For customization:**
1. Review CONVERSION_GUIDE.txt
2. Check examples/ folder
3. Modify functions.php carefully

---

## ✅ Installation Complete!

If you've completed all steps above:

🎉 **Congratulations! Your CMS is ready to use.**

**Quick Links:**
- Admin Login: yourwebsite.com/admin/
- View Site: yourwebsite.com/
- Documentation: README.md
- Client Guide: CLIENT_GUIDE.md

**Remember:**
- Keep config.php secure
- Backup regularly (content/, uploads/)
- Update contact info in CLIENT_GUIDE.md
- Provide proper training to clients

---

*Need more help? Review README.md for complete documentation.*
