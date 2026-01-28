# New CMS Editor System - Quick Guide

## Overview
The CMS has been updated to use a simpler, more powerful editor system with SEO support.

## Key Changes

### 1. **Single Content Editor**
- No more multiple editable zones (`data-editable` attributes)
- One rich text editor per page with your site's CSS loaded
- Edit the entire page content at once with a WYSIWYG editor

### 2. **SEO Section**
- Each page now has dedicated SEO fields:
  - Page Title (for search engines)
  - Meta Description
  - Meta Keywords
  - Open Graph Title (social media)
  - Open Graph Description
  - Open Graph Image
- SEO data is stored in `json/{page}.json`

### 3. **File Structure**

```
json/
  index.php.json    - SEO data for index.php
  about.php.json    - SEO data for about.php
  services.php.json - SEO data for services.php
  contacts.php.json - SEO data for contacts.php

content/
  index.php.json    - Page content
  about.php.json    - Page content
  services.php.json - Page content
  contacts.php.json - Page content
```

### 4. **Page Structure**
Each page now follows this pattern:

```php
<?php
require_once 'config.php';
require_once 'functions.php';

$current_page = basename(__FILE__);

// Load SEO data from json/{page}.json
$seo_data = get_seo_data($current_page);

// Load page content from content/{page}.json
$content_file = CONTENT_DIR . '/' . sanitize_filename($current_page) . '.json';
$page_content = '';
if (file_exists($content_file)) {
    $content_data = json_decode(file_get_contents($content_file), true) ?? [];
    $page_content = $content_data['content'] ?? '';
}

include 'includes/header.php';
?>

<?php if (!empty($page_content)): ?>
    <!-- Render saved content -->
    <?php echo $page_content; ?>
<?php else: ?>
    <!-- Default/fallback content -->
    <section>
        <h1>Default Content</h1>
    </section>
<?php endif; ?>

<?php
include 'includes/footer.php';
?>
```

## How to Use

### Editing Content
1. Go to `admin/edit.php?page=index.php` (or any page)
2. Fill in SEO fields (title, description, keywords, etc.)
3. Edit page content in the rich text editor
4. Click "Save Changes"

### The Editor
- Uses TinyMCE with your site's CSS loaded
- See exactly how content will look on the live site
- Upload images directly
- Full formatting controls
- HTML code view available

### SEO Benefits
- Proper meta tags for search engines
- Open Graph tags for social media sharing
- Separate from content for better organization
- Easy to update without touching code

## New Functions Added

### `get_seo_data($page)`
Loads SEO data from `json/{page}.json`

### `get_page_body_content($page)`
Extracts default content from between header and footer includes (for initial setup)

## Migration Notes

- Old `data-editable` zones are no longer used
- Content is now stored as complete HTML blocks
- Default content shows if no saved content exists
- SEO data is separate from page content

## Benefits

1. **Easier Editing**: One editor instead of multiple zones
2. **Better Preview**: See content styled exactly as it will appear
3. **SEO Ready**: Built-in SEO fields for every page
4. **Flexible**: Edit complete sections, not just text snippets
5. **Professional**: Rich editor with image upload, formatting, etc.

## File Locations

- **Editor**: `admin/edit.php`
- **Functions**: `functions.php` (get_seo_data, get_page_body_content)
- **SEO JSON**: `json/` directory
- **Content JSON**: `content/` directory
- **Header**: `includes/header.php` (uses $seo_data)
