# Blog System Documentation

## Overview
The blog system is a complete solution with SQLite database, admin management, comments system, and template-based design matching the Fitmax theme.

## Features
- ✅ SQLite database with posts, categories, tags, and comments
- ✅ Full CRUD admin interface for blog posts
- ✅ Comments system with approval workflow
- ✅ Admin comments management
- ✅ Previous/Next post navigation
- ✅ Author box with Gravatar integration
- ✅ Search functionality across blog posts
- ✅ Category filtering
- ✅ Nested comment replies
- ✅ Template-based design for easy customization

## File Structure

### Frontend Pages
- **blog.php** - Main blog listing page (matches template/blog.htm)
- **blog-single.php** - Single post page (matches template/single-blog.html.htm)
- **assets/css/blog-custom.css** - Custom styling for blog features

### Backend
- **admin/blog.php** - Blog post management (create, edit, delete)
- **admin/comments.php** - Comment moderation (approve, reject, delete)
- **blog-functions.php** - All database operations

### Database
- **database/blog.db** - SQLite database
- Tables:
  - `posts` - Blog posts with title, slug, content, featured image, etc.
  - `categories` - Post categories
  - `tags` - Post tags
  - `post_categories` - Many-to-many relationship
  - `post_tags` - Many-to-many relationship
  - `comments` - Comments with parent_id for nesting and status for moderation

## Usage

### Creating a Blog Post
1. Go to Admin → Blog
2. Click "Create New Post"
3. Fill in:
   - Title (slug auto-generates)
   - Content (using TinyMCE editor)
   - Excerpt (optional, auto-generates if empty)
   - Featured Image URL
   - Categories (comma-separated)
   - Tags (comma-separated)
   - Status (draft/published)
4. Click "Create Post"

### Managing Comments
1. Go to Admin → Comments
2. View comments by status:
   - **Pending** - New comments awaiting approval
   - **Approved** - Published comments visible on site
   - **Rejected** - Comments that have been rejected
3. Actions:
   - **Approve** - Publish the comment
   - **Reject** - Hide the comment
   - **Delete** - Permanently remove the comment
4. Bulk actions available for multiple comments

### Comment Flow
1. User submits comment on blog post
2. Comment saved with "pending" status
3. Admin reviews in Comments page
4. Admin approves or rejects
5. Approved comments appear on the blog post
6. Users can reply to comments (nested structure)

## Template-Based Design

The blog pages follow the template structure from `template/blog.htm` and `template/single-blog.html.htm`, making it easy for developers to:

1. **Copy HTML from template** - Open template files to see original structure
2. **Add PHP loops** - Replace static content with dynamic data
3. **Use {{}} placeholders** - Components can use the existing syntax
4. **Maintain CSS classes** - All original classes preserved for consistent styling

### Example: Adding Dynamic Content
```php
<!-- Static (from template) -->
<h2 class="title">Blog Post Title</h2>

<!-- Dynamic (in blog pages) -->
<h2 class="title"><?php echo htmlspecialchars($post['title']); ?></h2>
```

## URLs

### Frontend
- `/blog` - Blog listing page
- `/blog/post-slug` - Single post page
- `/blog?s=search+term` - Search results
- `/blog?category=category-slug` - Category filtered posts
- `/blog?page=2` - Pagination

### Admin
- `/admin/blog.php` - Manage posts
- `/admin/comments.php` - Manage comments

## Customization

### Modifying Blog Listing
1. Open `blog.php`
2. Find the post loop: `<?php foreach ($posts as $post): ?>`
3. Modify the HTML structure inside the loop
4. All CSS classes match template for consistent styling

### Modifying Single Post
1. Open `blog-single.php`
2. Find the post content section
3. Modify layout while keeping template structure
4. Comments section can be moved or styled

### Custom Styling
1. Edit `assets/css/blog-custom.css`
2. Target classes:
   - `.author-box` - Author information
   - `.navigation` - Prev/Next links
   - `.reviews` - Comments section
   - `.review-item` - Individual comment
   - `.review-item.child` - Nested replies

### Adding Sidebar Widgets
The sidebar already includes:
- Search form
- Categories list
- Recent posts

To add new widgets:
1. Open `blog.php` or `blog-single.php`
2. Find the sidebar section: `<div class="col-12 col-lg-3 sidebar">`
3. Add new `<li class="widget widget-name">` inside `<ul class="widgets">`
4. Follow template structure from `template/blog.htm`

## Database Functions

All blog functions are in `blog-functions.php`:

### Post Functions
- `create_post($data)` - Create new post
- `update_post($id, $data)` - Update post
- `delete_post($id)` - Delete post
- `get_post($id)` - Get post by ID
- `get_post_by_slug($slug)` - Get post by slug
- `get_posts($options)` - Get posts with filters
- `search_posts($query, $limit)` - Search posts

### Category Functions
- `create_category($name, $slug)` - Create category
- `get_all_categories()` - Get all categories
- `get_posts_by_category($slug, $limit, $offset)` - Get posts in category

### Tag Functions
- `create_tag($name, $slug)` - Create tag
- `get_all_tags()` - Get all tags
- `get_posts_by_tag($slug, $limit, $offset)` - Get posts with tag

### Comment Functions
- `add_comment($data)` - Add new comment (status: pending)
- `get_post_comments($post_id)` - Get approved comments with nested replies
- `get_comment_count($post_id)` - Count approved comments
- `get_all_comments()` - Get all comments (for admin)
- `update_comment_status($id, $status)` - Approve/reject comment
- `delete_comment($id)` - Delete comment

### Navigation Functions
- `get_previous_post($current_id)` - Get previous post
- `get_next_post($current_id)` - Get next post

## Security Features

- **SQL Injection Protection** - PDO prepared statements
- **XSS Prevention** - `htmlspecialchars()` on all output
- **Admin Authentication** - Session-based login required
- **Comment Moderation** - All comments pending by default
- **Email Validation** - Validates comment author emails
- **Input Sanitization** - Trim and validate all inputs

## Tips

### Performance
- The database auto-creates on first access
- Use pagination to limit posts per page (default: 6)
- Search is optimized with SQLite FTS (full-text search)

### SEO
- Each post has unique meta title, description, keywords
- Slugs are URL-friendly
- Breadcrumbs included
- Semantic HTML structure

### Comments
- Gravatars automatically loaded for avatars
- Nested replies supported (parent_id field)
- Status field: pending/approved/rejected
- Timestamps for sorting

## Troubleshooting

### Database Not Creating
- Check write permissions on `/database/` folder
- Verify `config.php` has correct `DATABASE_DIR` constant
- Check error logs for SQLite errors

### Comments Not Appearing
- Check comment status (must be "approved")
- Verify `get_post_comments()` called with correct post ID
- Check that JavaScript for reply form is working

### Styling Issues
- Clear browser cache
- Check that `blog-custom.css` is loaded (view source)
- Verify template CSS classes not overridden

### Routing Not Working
- Check `.htaccess` or web server config
- Verify `router.php` redirects to `index.php`
- Check `index.php` has blog route detection (lines 24-40)

## Future Enhancements

Possible additions:
- Featured posts
- Post scheduling
- Image upload for featured images
- Comment email notifications
- RSS feed
- Related posts
- Social sharing buttons
- Post views counter
- Author profiles
