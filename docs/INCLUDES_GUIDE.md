# HEADER & FOOTER INCLUDES - Usage Guide

## Overview

The CMS now includes reusable header and footer templates that make it easy to maintain consistent branding, navigation, and contact information across all pages of your site.

## 📁 Files Created

```
includes/
  ├── header.php    # Site header with navigation, logo, contact info
  └── footer.php    # Site footer with contact info, social links, copyright
```

## 🎯 Benefits

✅ **Update Once, Apply Everywhere** - Change phone number in one place, updates on all pages
✅ **Consistent Branding** - Same header/footer across entire site
✅ **Easy Maintenance** - No need to update 20+ pages individually
✅ **Global Settings** - Manage site-wide content through admin panel
✅ **Clean Code** - Separate content from structure

## 🚀 How to Use

### Basic Implementation

Create a new page file (e.g., `my-page.php`):

```php
<?php
// Include CMS files
require_once 'config.php';
require_once 'functions.php';

// Get current page name for content
$current_page = basename(__FILE__);

// Include header
include 'includes/header.php';
?>

<!-- YOUR PAGE CONTENT HERE -->
<section class="my-section">
    <div class="container">
        <h1 data-editable="page-title"><?php echo get_content($current_page, 'page-title', 'My Page'); ?></h1>
        <p data-editable="page-description"><?php echo get_content($current_page, 'page-description', 'Page description here'); ?></p>
    </div>
</section>

<?php
// Include footer
include 'includes/footer.php';
?>
```

### That's It!

Your page now has:
- Full header with navigation
- Contact information in header
- Social media links
- Complete footer
- Copyright notice
- All JavaScript includes

## 🎨 Global Settings

### What's Included in Global Settings

When you edit **Global Settings** in the admin panel, you can update:

**Header:**
- Phone number (appears in header)
- Business hours (appears in header)
- Social media links (Facebook, Twitter, YouTube, Instagram)

**Footer:**
- About text (company description)
- Twitter feed items
- Contact phone
- Contact email
- Contact address
- Copyright text

**SEO:**
- Default page title
- Meta keywords
- Meta description

### Editing Global Settings

1. Login to admin panel
2. Click **"Edit Global Settings"** (purple card at top)
3. Update any fields you want
4. Click **"Save Changes"**
5. Changes appear on ALL pages immediately!

## 📝 Editable Zones in Header/Footer

### Header Zones:
```
page-title          - Default page title
meta-keywords       - SEO keywords
meta-description    - SEO description
header-phone        - Phone number in header
header-hours        - Business hours in header
social-facebook     - Facebook URL
social-twitter      - Twitter URL
social-youtube      - YouTube URL
social-instagram    - Instagram URL
```

### Footer Zones:
```
footer-about        - Company description
footer-tweet1       - First Twitter item
footer-tweet2       - Second Twitter item
footer-phone        - Footer phone number
footer-email        - Footer email address
footer-address      - Footer physical address
footer-copyright    - Copyright text
```

## 🔧 Customization

### Changing Header/Footer

Edit the files directly:
- `includes/header.php` - Modify header structure
- `includes/footer.php` - Modify footer structure

### Adding New Global Zones

1. Edit `functions.php`
2. Find `get_editable_zones()` function
3. Add your zone to the `_global` array:

```php
'my-new-zone' => ['name' => 'my-new-zone', 'content' => 'Default value']
```

4. Use it in header/footer:

```php
<?php echo get_content('_global', 'my-new-zone', 'Default'); ?>
```

## 📋 Migration Guide

### Converting Existing Static Pages

**Before (static HTML):**
```html
<!DOCTYPE html>
<html>
<head>
    <title>My Page</title>
    ...
</head>
<body>
    <header>
        <!-- Header code -->
    </header>
    
    <!-- Your content -->
    
    <footer>
        <!-- Footer code -->
    </footer>
</body>
</html>
```

**After (using includes):**
```php
<?php
require_once 'config.php';
require_once 'functions.php';
$current_page = basename(__FILE__);
include 'includes/header.php';
?>

<!-- Your content only -->

<?php include 'includes/footer.php'; ?>
```

## 💡 Best Practices

### DO:
✅ Use header/footer includes for all new pages
✅ Update global settings instead of editing individual pages
✅ Keep page-specific content separate from global content
✅ Use descriptive zone names
✅ Test after updating global settings

### DON'T:
❌ Duplicate header/footer code on multiple pages
❌ Edit phone numbers on individual pages
❌ Hard-code social media URLs
❌ Copy/paste headers across files

## 🎯 Example Use Cases

### Use Case 1: Update Phone Number
**Old Way:** Edit 15+ files manually
**New Way:** Edit Global Settings → header-phone → Save

### Use Case 2: Change Social Media Link
**Old Way:** Find and replace in all files
**New Way:** Edit Global Settings → social-facebook → Save

### Use Case 3: Update Copyright Year
**Old Way:** Update footer on every page
**New Way:** Automatically shows current year with `<?php echo date('Y'); ?>`

### Use Case 4: Change Business Hours
**Old Way:** Search through all templates
**New Way:** Edit Global Settings → header-hours → Save

## 🔄 Workflow

### For Developers:
1. Create new page using includes
2. Add page-specific editable zones
3. Test the page
4. Hand off to client

### For Clients:
1. Login to admin
2. Edit Global Settings for site-wide changes
3. Edit individual pages for page-specific content
4. All changes appear immediately

## 🆘 Troubleshooting

### Header/Footer Not Showing
- Check file paths: `include 'includes/header.php';`
- Verify files exist in `/includes/` folder
- Check for PHP syntax errors

### Content Not Updating
- Make sure you're editing Global Settings, not a page
- Clear browser cache
- Check JSON file was saved in `/content/_global.json`

### Broken Links/Images
- Use relative paths: `assets/img/logo.svg`
- Not absolute paths: `/assets/img/logo.svg`

## 📊 File Structure

```
your-site/
│
├── includes/
│   ├── header.php           # Reusable header
│   └── footer.php           # Reusable footer
│
├── content/
│   ├── _global.json         # Global settings data
│   ├── my-page.php.json     # Page-specific data
│   └── ...
│
├── my-page.php              # Your page using includes
├── another-page.php         # Another page using includes
│
├── config.php
└── functions.php
```

## 🎓 Advanced: Dynamic Navigation

To make navigation highlight the current page:

```php
<?php $current = basename($_SERVER['PHP_SELF']); ?>
<nav>
    <ul>
        <li class="<?php echo $current === 'index.php' ? 'active' : ''; ?>">
            <a href="index.php">Home</a>
        </li>
        <li class="<?php echo $current === 'about.php' ? 'active' : ''; ?>">
            <a href="about.php">About</a>
        </li>
    </ul>
</nav>
```

## ✅ Checklist for Implementation

- [ ] Create includes/header.php
- [ ] Create includes/footer.php
- [ ] Update get_editable_zones() in functions.php
- [ ] Test Global Settings in admin
- [ ] Convert existing pages to use includes
- [ ] Update phone/email/hours in Global Settings
- [ ] Update social media links
- [ ] Test all pages display header/footer correctly
- [ ] Train client on Global Settings vs Page Settings

## 🎉 Summary

The header and footer include system gives you:
- **Centralized management** of site-wide content
- **Faster development** - write once, use everywhere
- **Easier maintenance** - update in one place
- **Better client experience** - clear separation of global vs page content
- **Consistent branding** - same look across all pages

---

**Questions?** Check out `examples/example-page-with-includes.php` for a working example!
