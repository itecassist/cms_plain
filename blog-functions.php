<?php
/**
 * Blog Database Functions
 */

/**
 * Initialize blog database tables
 */
function init_blog_tables($db) {
    // Posts table
    $db->exec("
        CREATE TABLE IF NOT EXISTS posts (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            title TEXT NOT NULL,
            slug TEXT UNIQUE NOT NULL,
            content TEXT,
            excerpt TEXT,
            author TEXT,
            status TEXT DEFAULT 'draft',
            featured_image TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            published_at DATETIME
        )
    ");
    
    // Categories table
    $db->exec("
        CREATE TABLE IF NOT EXISTS categories (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL,
            slug TEXT UNIQUE NOT NULL,
            description TEXT
        )
    ");
    
    // Tags table
    $db->exec("
        CREATE TABLE IF NOT EXISTS tags (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL UNIQUE
        )
    ");
    
    // Post-Category relationship
    $db->exec("
        CREATE TABLE IF NOT EXISTS post_categories (
            post_id INTEGER,
            category_id INTEGER,
            FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,
            FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE,
            PRIMARY KEY (post_id, category_id)
        )
    ");
    
    // Post-Tag relationship
    $db->exec("
        CREATE TABLE IF NOT EXISTS post_tags (
            post_id INTEGER,
            tag_id INTEGER,
            FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,
            FOREIGN KEY (tag_id) REFERENCES tags(id) ON DELETE CASCADE,
            PRIMARY KEY (post_id, tag_id)
        )
    ");
    
    // Comments table
    $db->exec("
        CREATE TABLE IF NOT EXISTS comments (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            post_id INTEGER NOT NULL,
            parent_id INTEGER DEFAULT NULL,
            author_name TEXT NOT NULL,
            author_email TEXT NOT NULL,
            content TEXT NOT NULL,
            status TEXT DEFAULT 'pending',
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,
            FOREIGN KEY (parent_id) REFERENCES comments(id) ON DELETE CASCADE
        )
    ");
    
    // Create indexes
    $db->exec("CREATE INDEX IF NOT EXISTS idx_posts_slug ON posts(slug)");
    $db->exec("CREATE INDEX IF NOT EXISTS idx_posts_status ON posts(status)");
    $db->exec("CREATE INDEX IF NOT EXISTS idx_posts_published_at ON posts(published_at)");
    $db->exec("CREATE INDEX IF NOT EXISTS idx_comments_post_id ON comments(post_id)");
    $db->exec("CREATE INDEX IF NOT EXISTS idx_comments_status ON comments(status)");
}

/**
 * Generate unique slug from title
 */
function generate_slug($title, $post_id = null) {
    $slug = strtolower(trim($title));
    $slug = preg_replace('/[^a-z0-9-]/', '-', $slug);
    $slug = preg_replace('/-+/', '-', $slug);
    $slug = trim($slug, '-');
    
    // Check if slug exists
    $db = get_db();
    $query = "SELECT COUNT(*) FROM posts WHERE slug = :slug";
    $params = [':slug' => $slug];
    
    if ($post_id) {
        $query .= " AND id != :id";
        $params[':id'] = $post_id;
    }
    
    $stmt = $db->prepare($query);
    $stmt->execute($params);
    $count = $stmt->fetchColumn();
    
    if ($count > 0) {
        $i = 1;
        $original_slug = $slug;
        while ($count > 0) {
            $slug = $original_slug . '-' . $i;
            $stmt = $db->prepare($query);
            $params[':slug'] = $slug;
            $stmt->execute($params);
            $count = $stmt->fetchColumn();
            $i++;
        }
    }
    
    return $slug;
}

/**
 * Create a new blog post
 */
function create_post($data) {
    $db = get_db();
    
    $slug = !empty($data['slug']) ? $data['slug'] : generate_slug($data['title']);
    
    $query = "INSERT INTO posts (title, slug, content, excerpt, author, status, featured_image, published_at) 
              VALUES (:title, :slug, :content, :excerpt, :author, :status, :featured_image, :published_at)";
    
    $stmt = $db->prepare($query);
    $stmt->execute([
        ':title' => $data['title'],
        ':slug' => $slug,
        ':content' => $data['content'] ?? '',
        ':excerpt' => $data['excerpt'] ?? '',
        ':author' => $data['author'] ?? 'Admin',
        ':status' => $data['status'] ?? 'draft',
        ':featured_image' => $data['featured_image'] ?? '',
        ':published_at' => ($data['status'] ?? 'draft') === 'published' ? date('Y-m-d H:i:s') : null
    ]);
    
    $post_id = $db->lastInsertId();
    
    // Handle categories
    if (!empty($data['categories'])) {
        set_post_categories($post_id, $data['categories']);
    }
    
    // Handle tags
    if (!empty($data['tags'])) {
        set_post_tags($post_id, $data['tags']);
    }
    
    return $post_id;
}

/**
 * Update a blog post
 */
function update_post($id, $data) {
    $db = get_db();
    
    $slug = !empty($data['slug']) ? $data['slug'] : generate_slug($data['title'], $id);
    
    // If status is being changed to published and published_at is null, set it
    $published_at = null;
    if (($data['status'] ?? 'draft') === 'published') {
        $current_post = get_post_by_id($id);
        $published_at = $current_post['published_at'] ?? date('Y-m-d H:i:s');
    }
    
    $query = "UPDATE posts SET 
              title = :title, 
              slug = :slug, 
              content = :content, 
              excerpt = :excerpt, 
              author = :author, 
              status = :status, 
              featured_image = :featured_image,
              published_at = :published_at,
              updated_at = CURRENT_TIMESTAMP
              WHERE id = :id";
    
    $stmt = $db->prepare($query);
    $result = $stmt->execute([
        ':title' => $data['title'],
        ':slug' => $slug,
        ':content' => $data['content'] ?? '',
        ':excerpt' => $data['excerpt'] ?? '',
        ':author' => $data['author'] ?? 'Admin',
        ':status' => $data['status'] ?? 'draft',
        ':featured_image' => $data['featured_image'] ?? '',
        ':published_at' => $published_at,
        ':id' => $id
    ]);
    
    // Handle categories
    if (isset($data['categories'])) {
        set_post_categories($id, $data['categories']);
    }
    
    // Handle tags
    if (isset($data['tags'])) {
        set_post_tags($id, $data['tags']);
    }
    
    return $result;
}

/**
 * Delete a blog post
 */
function delete_post($id) {
    $db = get_db();
    $stmt = $db->prepare("DELETE FROM posts WHERE id = :id");
    return $stmt->execute([':id' => $id]);
}

/**
 * Get a post by ID
 */
function get_post_by_id($id) {
    $db = get_db();
    $stmt = $db->prepare("SELECT * FROM posts WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $post = $stmt->fetch();
    
    if ($post) {
        $post['categories'] = get_post_categories($id);
        $post['tags'] = get_post_tags($id);
    }
    
    return $post;
}

/**
 * Get a post by slug
 */
function get_post_by_slug($slug) {
    $db = get_db();
    $stmt = $db->prepare("SELECT * FROM posts WHERE slug = :slug");
    $stmt->execute([':slug' => $slug]);
    $post = $stmt->fetch();
    
    if ($post) {
        $post['categories'] = get_post_categories($post['id']);
        $post['tags'] = get_post_tags($post['id']);
    }
    
    return $post;
}

/**
 * Get all posts with pagination
 */
function get_posts($options = []) {
    $db = get_db();
    
    $status = $options['status'] ?? null;
    $limit = $options['limit'] ?? 10;
    $offset = $options['offset'] ?? 0;
    $order_by = $options['order_by'] ?? 'published_at';
    $order_dir = $options['order_dir'] ?? 'DESC';
    
    $query = "SELECT * FROM posts";
    $params = [];
    
    if ($status) {
        $query .= " WHERE status = :status";
        $params[':status'] = $status;
    }
    
    $query .= " ORDER BY $order_by $order_dir LIMIT :limit OFFSET :offset";
    
    $stmt = $db->prepare($query);
    
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
    
    $stmt->execute();
    $posts = $stmt->fetchAll();
    
    // Add categories and tags to each post
    foreach ($posts as &$post) {
        $post['categories'] = get_post_categories($post['id']);
        $post['tags'] = get_post_tags($post['id']);
    }
    
    return $posts;
}

/**
 * Get total post count
 */
function get_posts_count($status = null) {
    $db = get_db();
    
    $query = "SELECT COUNT(*) FROM posts";
    $params = [];
    
    if ($status) {
        $query .= " WHERE status = :status";
        $params[':status'] = $status;
    }
    
    $stmt = $db->prepare($query);
    $stmt->execute($params);
    
    return $stmt->fetchColumn();
}

/**
 * Search posts
 */
function search_posts($search_term, $limit = 10) {
    $db = get_db();
    
    $query = "SELECT * FROM posts 
              WHERE status = 'published' 
              AND (title LIKE :search OR content LIKE :search OR excerpt LIKE :search)
              ORDER BY published_at DESC 
              LIMIT :limit";
    
    $stmt = $db->prepare($query);
    $stmt->bindValue(':search', '%' . $search_term . '%');
    $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
    $stmt->execute();
    
    return $stmt->fetchAll();
}

/**
 * Get or create category by name
 */
function get_or_create_category($name) {
    $db = get_db();
    
    $slug = strtolower(preg_replace('/[^a-z0-9-]/', '-', $name));
    $slug = preg_replace('/-+/', '-', trim($slug, '-'));
    
    $stmt = $db->prepare("SELECT id FROM categories WHERE slug = :slug");
    $stmt->execute([':slug' => $slug]);
    $category = $stmt->fetch();
    
    if ($category) {
        return $category['id'];
    }
    
    $stmt = $db->prepare("INSERT INTO categories (name, slug) VALUES (:name, :slug)");
    $stmt->execute([':name' => $name, ':slug' => $slug]);
    
    return $db->lastInsertId();
}

/**
 * Get all categories
 */
function get_all_categories() {
    $db = get_db();
    $stmt = $db->query("SELECT * FROM categories ORDER BY name");
    return $stmt->fetchAll();
}

/**
 * Set post categories
 */
function set_post_categories($post_id, $category_ids) {
    $db = get_db();
    
    // Remove existing categories
    $stmt = $db->prepare("DELETE FROM post_categories WHERE post_id = :post_id");
    $stmt->execute([':post_id' => $post_id]);
    
    // Add new categories
    if (!empty($category_ids)) {
        $stmt = $db->prepare("INSERT INTO post_categories (post_id, category_id) VALUES (:post_id, :category_id)");
        foreach ($category_ids as $category_id) {
            $stmt->execute([':post_id' => $post_id, ':category_id' => $category_id]);
        }
    }
}

/**
 * Get post categories
 */
function get_post_categories($post_id) {
    $db = get_db();
    $stmt = $db->prepare("
        SELECT c.* FROM categories c
        INNER JOIN post_categories pc ON c.id = pc.category_id
        WHERE pc.post_id = :post_id
    ");
    $stmt->execute([':post_id' => $post_id]);
    return $stmt->fetchAll();
}

/**
 * Get or create tag by name
 */
function get_or_create_tag($name) {
    $db = get_db();
    
    $stmt = $db->prepare("SELECT id FROM tags WHERE name = :name");
    $stmt->execute([':name' => $name]);
    $tag = $stmt->fetch();
    
    if ($tag) {
        return $tag['id'];
    }
    
    $stmt = $db->prepare("INSERT INTO tags (name) VALUES (:name)");
    $stmt->execute([':name' => $name]);
    
    return $db->lastInsertId();
}

/**
 * Get all tags
 */
function get_all_tags() {
    $db = get_db();
    $stmt = $db->query("SELECT * FROM tags ORDER BY name");
    return $stmt->fetchAll();
}

/**
 * Set post tags (accepts array of tag names)
 */
function set_post_tags($post_id, $tag_names) {
    $db = get_db();
    
    // Remove existing tags
    $stmt = $db->prepare("DELETE FROM post_tags WHERE post_id = :post_id");
    $stmt->execute([':post_id' => $post_id]);
    
    // Add new tags
    if (!empty($tag_names)) {
        foreach ($tag_names as $tag_name) {
            $tag_name = trim($tag_name);
            if (!empty($tag_name)) {
                $tag_id = get_or_create_tag($tag_name);
                $stmt = $db->prepare("INSERT OR IGNORE INTO post_tags (post_id, tag_id) VALUES (:post_id, :tag_id)");
                $stmt->execute([':post_id' => $post_id, ':tag_id' => $tag_id]);
            }
        }
    }
}

/**
 * Get post tags
 */
function get_post_tags($post_id) {
    $db = get_db();
    $stmt = $db->prepare("
        SELECT t.* FROM tags t
        INNER JOIN post_tags pt ON t.id = pt.tag_id
        WHERE pt.post_id = :post_id
    ");
    $stmt->execute([':post_id' => $post_id]);
    return $stmt->fetchAll();
}

/**
 * Get posts by category
 */
function get_posts_by_category($category_slug, $limit = 10, $offset = 0) {
    $db = get_db();
    
    $stmt = $db->prepare("
        SELECT p.* FROM posts p
        INNER JOIN post_categories pc ON p.id = pc.post_id
        INNER JOIN categories c ON pc.category_id = c.id
        WHERE c.slug = :slug AND p.status = 'published'
        ORDER BY p.published_at DESC
        LIMIT :limit OFFSET :offset
    ");
    
    $stmt->bindValue(':slug', $category_slug);
    $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
    $stmt->execute();
    
    return $stmt->fetchAll();
}

/**
 * Get posts by tag
 */
function get_posts_by_tag($tag_name, $limit = 10, $offset = 0) {
    $db = get_db();
    
    $stmt = $db->prepare("
        SELECT p.* FROM posts p
        INNER JOIN post_tags pt ON p.id = pt.post_id
        INNER JOIN tags t ON pt.tag_id = t.id
        WHERE t.name = :name AND p.status = 'published'
        ORDER BY p.published_at DESC
        LIMIT :limit OFFSET :offset
    ");
    
    $stmt->bindValue(':name', $tag_name);
    $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
    $stmt->execute();
    
    return $stmt->fetchAll();
}

/**
 * Add a comment to a post
 */
function add_comment($post_id, $author_name, $author_email, $content, $parent_id = null) {
    $db = get_db();
    
    $stmt = $db->prepare("
        INSERT INTO comments (post_id, parent_id, author_name, author_email, content, status)
        VALUES (:post_id, :parent_id, :author_name, :author_email, :content, 'pending')
    ");
    
    return $stmt->execute([
        ':post_id' => $post_id,
        ':parent_id' => $parent_id,
        ':author_name' => $author_name,
        ':author_email' => $author_email,
        ':content' => $content
    ]);
}

/**
 * Get approved comments for a post
 */
function get_post_comments($post_id) {
    $db = get_db();
    
    $stmt = $db->prepare("
        SELECT * FROM comments 
        WHERE post_id = :post_id AND status = 'approved'
        ORDER BY created_at ASC
    ");
    
    $stmt->execute([':post_id' => $post_id]);
    $comments = $stmt->fetchAll();
    
    // Organize into parent/child structure
    $comment_tree = [];
    $comment_map = [];
    
    foreach ($comments as $comment) {
        $comment['replies'] = [];
        $comment_map[$comment['id']] = $comment;
    }
    
    foreach ($comment_map as $id => $comment) {
        if ($comment['parent_id']) {
            if (isset($comment_map[$comment['parent_id']])) {
                $comment_map[$comment['parent_id']]['replies'][] = &$comment_map[$id];
            }
        } else {
            $comment_tree[] = &$comment_map[$id];
        }
    }
    
    return $comment_tree;
}

/**
 * Get comment count for a post
 */
function get_comment_count($post_id) {
    $db = get_db();
    
    $stmt = $db->prepare("
        SELECT COUNT(*) FROM comments 
        WHERE post_id = :post_id AND status = 'approved'
    ");
    
    $stmt->execute([':post_id' => $post_id]);
    return $stmt->fetchColumn();
}

/**
 * Get all comments (for admin)
 */
function get_all_comments($status = null, $limit = 50, $offset = 0) {
    $db = get_db();
    
    $query = "SELECT c.*, p.title as post_title, p.slug as post_slug 
              FROM comments c 
              LEFT JOIN posts p ON c.post_id = p.id";
    
    $params = [];
    
    if ($status) {
        $query .= " WHERE c.status = :status";
        $params[':status'] = $status;
    }
    
    $query .= " ORDER BY c.created_at DESC LIMIT :limit OFFSET :offset";
    
    $stmt = $db->prepare($query);
    
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
    
    $stmt->execute();
    return $stmt->fetchAll();
}

/**
 * Update comment status
 */
function update_comment_status($comment_id, $status) {
    $db = get_db();
    
    $stmt = $db->prepare("UPDATE comments SET status = :status WHERE id = :id");
    return $stmt->execute([':status' => $status, ':id' => $comment_id]);
}

/**
 * Delete a comment
 */
function delete_comment($comment_id) {
    $db = get_db();
    
    $stmt = $db->prepare("DELETE FROM comments WHERE id = :id");
    return $stmt->execute([':id' => $comment_id]);
}

/**
 * Get comment count by status
 */
function get_comments_count($status = null) {
    $db = get_db();
    
    if ($status) {
        $stmt = $db->prepare("SELECT COUNT(*) FROM comments WHERE status = :status");
        $stmt->execute([':status' => $status]);
    } else {
        $stmt = $db->query("SELECT COUNT(*) FROM comments");
    }
    
    return $stmt->fetchColumn();
}

/**
 * Get previous post
 */
function get_previous_post($current_post_id) {
    $db = get_db();
    
    $stmt = $db->prepare("
        SELECT * FROM posts 
        WHERE status = 'published' AND id < :id 
        ORDER BY id DESC 
        LIMIT 1
    ");
    
    $stmt->execute([':id' => $current_post_id]);
    return $stmt->fetch();
}

/**
 * Get next post
 */
function get_next_post($current_post_id) {
    $db = get_db();
    
    $stmt = $db->prepare("
        SELECT * FROM posts 
        WHERE status = 'published' AND id > :id 
        ORDER BY id ASC 
        LIMIT 1
    ");
    
    $stmt->execute([':id' => $current_post_id]);
    return $stmt->fetch();
}
