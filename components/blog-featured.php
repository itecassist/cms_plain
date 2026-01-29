<?php
/**
 * Blog Featured Component
 * Displays featured/recent blog posts in a compact format
 */

$limit = isset($_GET['featured_limit']) ? (int)$_GET['featured_limit'] : 3;

// Get recent published posts
$posts = get_posts([
    'status' => 'published',
    'limit' => $limit,
    'order_by' => 'published_at',
    'order_dir' => 'DESC'
]);
?>

<div class="blog-featured-component">
    <h2 class="blog-featured-title">Latest from Our Blog</h2>
    
    <?php if (empty($posts)): ?>
        <p class="blog-featured-empty">No posts available yet.</p>
    <?php else: ?>
        <div class="blog-featured-grid">
            <?php foreach ($posts as $post): ?>
                <div class="blog-featured-card">
                    <?php if ($post['featured_image']): ?>
                        <a href="/blog/<?php echo htmlspecialchars($post['slug']); ?>" class="blog-featured-image">
                            <img src="<?php echo htmlspecialchars($post['featured_image']); ?>" 
                                 alt="<?php echo htmlspecialchars($post['title']); ?>">
                        </a>
                    <?php endif; ?>
                    
                    <div class="blog-featured-content">
                        <div class="blog-featured-date">
                            <?php echo date('M j, Y', strtotime($post['published_at'])); ?>
                        </div>
                        
                        <h3 class="blog-featured-post-title">
                            <a href="/blog/<?php echo htmlspecialchars($post['slug']); ?>">
                                <?php echo htmlspecialchars($post['title']); ?>
                            </a>
                        </h3>
                        
                        <?php if ($post['excerpt']): ?>
                            <p class="blog-featured-excerpt">
                                <?php echo htmlspecialchars(substr($post['excerpt'], 0, 120)); ?>
                                <?php echo strlen($post['excerpt']) > 120 ? '...' : ''; ?>
                            </p>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <div class="blog-featured-footer">
            <a href="/blog" class="blog-featured-view-all">View All Posts →</a>
        </div>
    <?php endif; ?>
</div>

<style>
.blog-featured-component {
    background: #f9fafb;
    padding: 60px 20px;
}

.blog-featured-title {
    text-align: center;
    font-size: 36px;
    font-weight: 700;
    margin-bottom: 40px;
    color: #1f2937;
}

.blog-featured-grid {
    max-width: 1200px;
    margin: 0 auto;
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 30px;
}

.blog-featured-card {
    background: white;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    transition: all 0.3s;
}

.blog-featured-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.12);
}

.blog-featured-image {
    display: block;
    position: relative;
    padding-bottom: 60%;
    overflow: hidden;
    background: #e5e7eb;
}

.blog-featured-image img {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s;
}

.blog-featured-card:hover .blog-featured-image img {
    transform: scale(1.05);
}

.blog-featured-content {
    padding: 20px;
}

.blog-featured-date {
    font-size: 13px;
    color: #667eea;
    font-weight: 600;
    margin-bottom: 10px;
}

.blog-featured-post-title {
    font-size: 20px;
    font-weight: 700;
    margin-bottom: 12px;
    line-height: 1.3;
}

.blog-featured-post-title a {
    color: #1f2937;
    text-decoration: none;
    transition: color 0.3s;
}

.blog-featured-post-title a:hover {
    color: #667eea;
}

.blog-featured-excerpt {
    font-size: 14px;
    color: #6b7280;
    line-height: 1.5;
}

.blog-featured-footer {
    text-align: center;
    margin-top: 40px;
}

.blog-featured-view-all {
    display: inline-block;
    padding: 12px 30px;
    background: #667eea;
    color: white;
    text-decoration: none;
    border-radius: 5px;
    font-weight: 600;
    transition: background 0.3s;
}

.blog-featured-view-all:hover {
    background: #5568d3;
}

.blog-featured-empty {
    text-align: center;
    color: #9ca3af;
    padding: 40px 20px;
}

@media (max-width: 768px) {
    .blog-featured-title {
        font-size: 28px;
    }
    
    .blog-featured-grid {
        grid-template-columns: 1fr;
    }
}
</style>
