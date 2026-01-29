<?php
/**
 * Blog List Component
 * Displays a list of recent blog posts
 */

// Get parameters from URL or use defaults
$limit = isset($_GET['blog_limit']) ? (int)$_GET['blog_limit'] : 6;
$page = isset($_GET['blog_page']) ? (int)$_GET['blog_page'] : 1;
$offset = ($page - 1) * $limit;

// Get published posts
$posts = get_posts([
    'status' => 'published',
    'limit' => $limit,
    'offset' => $offset,
    'order_by' => 'published_at',
    'order_dir' => 'DESC'
]);

$total_posts = get_posts_count('published');
$total_pages = ceil($total_posts / $limit);
?>

<div class="blog-list-component">
    <?php if (empty($posts)): ?>
        <div class="blog-empty">
            <p>No blog posts yet. Check back soon!</p>
        </div>
    <?php else: ?>
        <div class="blog-grid">
            <?php foreach ($posts as $post): ?>
                <article class="blog-card">
                    <?php if ($post['featured_image']): ?>
                        <div class="blog-card-image">
                            <a href="/blog/<?php echo htmlspecialchars($post['slug']); ?>">
                                <img src="<?php echo htmlspecialchars($post['featured_image']); ?>" 
                                     alt="<?php echo htmlspecialchars($post['title']); ?>">
                            </a>
                        </div>
                    <?php endif; ?>
                    
                    <div class="blog-card-content">
                        <div class="blog-card-meta">
                            <span class="blog-date">
                                <?php echo date('M j, Y', strtotime($post['published_at'])); ?>
                            </span>
                            <?php if (!empty($post['categories'])): ?>
                                <span class="blog-category">
                                    <?php echo htmlspecialchars($post['categories'][0]['name']); ?>
                                </span>
                            <?php endif; ?>
                        </div>
                        
                        <h3 class="blog-card-title">
                            <a href="/blog/<?php echo htmlspecialchars($post['slug']); ?>">
                                <?php echo htmlspecialchars($post['title']); ?>
                            </a>
                        </h3>
                        
                        <?php if ($post['excerpt']): ?>
                            <p class="blog-card-excerpt">
                                <?php echo htmlspecialchars($post['excerpt']); ?>
                            </p>
                        <?php endif; ?>
                        
                        <a href="/blog/<?php echo htmlspecialchars($post['slug']); ?>" class="blog-read-more">
                            Read More →
                        </a>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
        
        <?php if ($total_pages > 1): ?>
            <div class="blog-pagination">
                <?php if ($page > 1): ?>
                    <a href="?blog_page=<?php echo $page - 1; ?>" class="blog-pagination-prev">← Previous</a>
                <?php endif; ?>
                
                <div class="blog-pagination-numbers">
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <?php if ($i === $page): ?>
                            <span class="blog-pagination-current"><?php echo $i; ?></span>
                        <?php else: ?>
                            <a href="?blog_page=<?php echo $i; ?>"><?php echo $i; ?></a>
                        <?php endif; ?>
                    <?php endfor; ?>
                </div>
                
                <?php if ($page < $total_pages): ?>
                    <a href="?blog_page=<?php echo $page + 1; ?>" class="blog-pagination-next">Next →</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

<style>
.blog-list-component {
    max-width: 1200px;
    margin: 0 auto;
    padding: 40px 20px;
}

.blog-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
    gap: 30px;
    margin-bottom: 40px;
}

.blog-card {
    background: white;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    transition: transform 0.3s, box-shadow 0.3s;
}

.blog-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 4px 16px rgba(0,0,0,0.15);
}

.blog-card-image {
    position: relative;
    padding-bottom: 60%;
    overflow: hidden;
    background: #f3f4f6;
}

.blog-card-image img {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s;
}

.blog-card:hover .blog-card-image img {
    transform: scale(1.05);
}

.blog-card-content {
    padding: 25px;
}

.blog-card-meta {
    display: flex;
    align-items: center;
    gap: 15px;
    margin-bottom: 15px;
    font-size: 13px;
    color: #6b7280;
}

.blog-category {
    background: #667eea;
    color: white;
    padding: 3px 10px;
    border-radius: 12px;
    font-weight: 600;
}

.blog-card-title {
    font-size: 22px;
    font-weight: 700;
    margin-bottom: 15px;
    line-height: 1.3;
}

.blog-card-title a {
    color: #1f2937;
    text-decoration: none;
    transition: color 0.3s;
}

.blog-card-title a:hover {
    color: #667eea;
}

.blog-card-excerpt {
    color: #6b7280;
    line-height: 1.6;
    margin-bottom: 15px;
}

.blog-read-more {
    color: #667eea;
    font-weight: 600;
    text-decoration: none;
    transition: color 0.3s;
}

.blog-read-more:hover {
    color: #5568d3;
}

.blog-pagination {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 15px;
    margin-top: 40px;
}

.blog-pagination a, .blog-pagination span {
    padding: 10px 15px;
    border: 2px solid #e5e7eb;
    border-radius: 5px;
    text-decoration: none;
    color: #333;
    font-weight: 500;
    transition: all 0.3s;
}

.blog-pagination a:hover {
    border-color: #667eea;
    color: #667eea;
}

.blog-pagination-current {
    background: #667eea;
    color: white;
    border-color: #667eea;
}

.blog-pagination-numbers {
    display: flex;
    gap: 8px;
}

.blog-empty {
    text-align: center;
    padding: 60px 20px;
    color: #9ca3af;
    font-size: 18px;
}

@media (max-width: 768px) {
    .blog-grid {
        grid-template-columns: 1fr;
    }
    
    .blog-card-title {
        font-size: 20px;
    }
}
</style>
