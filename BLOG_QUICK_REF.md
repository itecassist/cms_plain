# Blog System - Quick Reference

## URLs
```
Frontend:
/blog                          → Blog listing
/blog/post-slug                → Single post
/blog?s=search                 → Search
/blog?category=slug            → Category filter
/blog?page=2                   → Pagination

Admin:
/admin/blog.php                → Manage posts
/admin/comments.php            → Moderate comments
```

## First-Time Setup
```
1. Visit: /blog-test-init.php
2. Creates: Database + Sample content
3. Login: /admin/
4. Manage: Blog and Comments
```

## File Locations
```
Frontend Pages:
├── blog.php                   → Listing page
├── blog-single.php            → Post page
└── assets/css/blog-custom.css → Styling

Backend:
├── admin/blog.php             → Post CRUD
├── admin/comments.php         → Comment moderation
└── blog-functions.php         → Database layer

Database:
└── database/blog.db           → SQLite (auto-created)
```

## Key Features
```
✓ Template-based (matches Fitmax design)
✓ Comments with nesting & moderation
✓ Search & category filtering
✓ Previous/Next navigation
✓ Author box with Gravatar
✓ Responsive design
✓ SEO optimized
```

## Creating a Post
```
1. Admin → Blog → Create New Post
2. Title: Auto-generates slug
3. Content: TinyMCE editor
4. Categories: Comma-separated (e.g., "Fitness,Health")
5. Tags: Comma-separated (e.g., "workout,diet")
6. Featured Image: Full URL
7. Status: draft or published
8. Save
```

## Moderating Comments
```
1. Admin → Comments
2. Filter: All | Pending | Approved | Rejected
3. Actions: Approve | Reject | Delete
4. Bulk: Select multiple → Apply action
```

## Database Functions
```php
// Posts
create_post($data)
update_post($id, $data)
delete_post($id)
get_post_by_slug($slug)
get_posts($options)
search_posts($query, $limit)

// Comments
add_comment($data)              // Status: pending
get_post_comments($post_id)     // Approved only
update_comment_status($id, $status)
delete_comment($id)

// Navigation
get_previous_post($current_id)
get_next_post($current_id)

// Categories/Tags
create_category($name, $slug)
get_posts_by_category($slug, $limit, $offset)
create_tag($name, $slug)
```

## Customization Examples

### Change Posts Per Page
```php
// blog.php line 14
$per_page = 6; // Your number
```

### Add Sidebar Widget
```php
// blog.php or blog-single.php
<li class="widget widget-custom">
    <h3 class="title">Widget Title</h3>
    <!-- Content -->
</li>
```

### Modify Comment Styling
```css
/* assets/css/blog-custom.css */
.review-item {
    /* Your styles */
}
```

## Security
```
✓ PDO prepared statements
✓ XSS prevention (htmlspecialchars)
✓ Email validation
✓ Comment moderation
✓ Admin authentication
✓ Input sanitization
```

## Testing Checklist
```
□ Run /blog-test-init.php
□ View /blog (listing page)
□ Click post (single page)
□ Submit comment (will be pending)
□ Approve in admin
□ Test search (/blog?s=test)
□ Test category (/blog?category=fitness)
□ Test pagination
□ Test prev/next navigation
□ Create new post in admin
□ Reply to comment (nested)
```

## Common Issues

### Pages show 404
```
Check: .htaccess exists
Check: router.php configured
Check: index.php has blog routes
```

### Comments not showing
```
Status must be: approved
Check: get_post_comments() called
Check: JavaScript not blocked
```

### Styling broken
```
Clear: Browser cache
Check: blog-custom.css loaded
Check: $custom_css in header.php
```

### Database error
```
Check: /database/ writable
Check: blog-functions.php included
Check: PHP error logs
```

## Template Structure
```
Based on:
template/blog.htm              → blog.php
template/single-blog.html.htm  → blog-single.php

All CSS classes preserved for consistency!
```

## Tips
```
💡 Use admin/uploads for featured images
💡 TinyMCE for rich text formatting
💡 Write good excerpts for SEO
💡 Moderate comments regularly
💡 Backup database/blog.db
💡 SQLite scales to thousands of posts
```

## Documentation
```
Full Guide: docs/BLOG_SYSTEM.md
Summary: BLOG_IMPLEMENTATION.md
This File: BLOG_QUICK_REF.md
```

---
**Ready to blog!** Run the init script and start creating content.
