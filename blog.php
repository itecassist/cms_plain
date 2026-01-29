<?php
/**
 * Blog Listing Page - Template Style
 * Based on template/blog.htm - Easy to customize by copying template structure
 */
require_once 'config.php';
require_once 'functions.php';

// Handle search
$search_term = isset($_GET['s']) ? trim($_GET['s']) : '';
$category_filter = isset($_GET['category']) ? trim($_GET['category']) : '';

// Pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 6;
$offset = ($page - 1) * $per_page;

// Get posts
if ($search_term) {
    $posts = search_posts($search_term, 100);
    $total_posts = count($posts);
    $posts = array_slice($posts, $offset, $per_page);
} elseif ($category_filter) {
    $posts = get_posts_by_category($category_filter, $per_page, $offset);
    $total_posts = count(get_posts_by_category($category_filter, 10000, 0));
} else {
    $posts = get_posts([
        'status' => 'published',
        'limit' => $per_page,
        'offset' => $offset,
        'order_by' => 'published_at',
        'order_dir' => 'DESC'
    ]);
    $total_posts = get_posts_count('published');
}

$total_pages = ceil($total_posts / $per_page);

// Get all categories for sidebar
$all_categories = get_all_categories();

// Get recent posts for sidebar
$recent_posts = get_posts([
    'status' => 'published',
    'limit' => 5,
    'order_by' => 'published_at',
    'order_dir' => 'DESC'
]);

// SEO data
$seo_data = [
    'title' => 'News - ' . SITE_NAME,
    'description' => 'Read our latest blog posts and articles',
    'keywords' => 'blog, articles, news'
];

// Add custom blog CSS
$custom_css = ['assets/css/blog-custom.css'];

$current_page = 'blog.php';
include 'includes/header.php';
?>

<!-- =============== HEADER-TITLE =============== -->
<section class="s-header-title" style="background-image: url(/assets/img/bg-1-min.png);">
	<div class="container">
		<h1 class="title">Blog</h1>
		<ul class="breadcrambs">
			<li><a href="/">Home</a></li>
			<li>Blog</li>
		</ul>
	</div>
</section>
<!-- ============= HEADER-TITLE END ============= -->

<!--===================== S-NEWS =====================-->
<section class="s-news">
	<div class="container">
		<div class="row">
			<div class="col-12 col-lg-9 blog-cover">
				<?php if (empty($posts)): ?>
					<div class="post-item-cover">
						<div class="post-content">
							<h2 class="title">No posts found</h2>
							<div class="text">
								<p><?php echo $search_term ? 'No results found for your search.' : 'There are no blog posts yet. Please check back soon!'; ?></p>
							</div>
						</div>
						<?php if ($search_term): ?>
							<div class="post-footer">
								<a href="/blog" class="btn"><span>View All Posts</span></a>
							</div>
						<?php endif; ?>
					</div>
				<?php else: ?>
					<?php foreach ($posts as $post): ?>
						<!-- POST ITEM -->
						<div class="post-item-cover">
							<?php if ($post['featured_image']): ?>
								<div class="post-header">
									<div class="post-thumbnail">
										<a href="/blog/<?php echo htmlspecialchars($post['slug']); ?>">
											<img src="<?php echo htmlspecialchars($post['featured_image']); ?>" alt="<?php echo htmlspecialchars($post['title']); ?>">
										</a>
									</div>
								</div>
							<?php endif; ?>
							<div class="post-content">
								<div class="meta">
									<span class="post-by"><i class="fa fa-user" aria-hidden="true"></i><a href="#">By <?php echo htmlspecialchars($post['author']); ?></a></span>
									<span class="post-date"><i class="fa fa-calendar" aria-hidden="true"></i><?php echo date('F j, Y', strtotime($post['published_at'])); ?></span>
									<?php if (!empty($post['categories'])): ?>
										<span class="post-category"><i class="fa fa-tag" aria-hidden="true"></i><a href="/blog?category=<?php echo urlencode($post['categories'][0]['slug']); ?>"><?php echo htmlspecialchars($post['categories'][0]['name']); ?></a></span>
									<?php endif; ?>
								</div>
								<h2 class="title"><a href="/blog/<?php echo htmlspecialchars($post['slug']); ?>"><?php echo htmlspecialchars($post['title']); ?></a></h2>
								<div class="text">
									<p><?php echo htmlspecialchars($post['excerpt'] ?: substr(strip_tags($post['content']), 0, 200) . '...'); ?></p>
								</div>
							</div>
							<div class="post-footer">
								<div class="meta">
									<span class="post-comment"><i class="fa fa-comment" aria-hidden="true"></i><a href="/blog/<?php echo htmlspecialchars($post['slug']); ?>#comments"><?php echo get_comment_count($post['id']); ?> Comment(s)</a></span>
									<?php if (!empty($post['tags'])): ?>
										<span class="post-tags"><i class="fa fa-hashtag" aria-hidden="true"></i>
											<?php foreach (array_slice($post['tags'], 0, 2) as $tag): ?>
												<a href="/blog?s=<?php echo urlencode($tag['name']); ?>"><?php echo htmlspecialchars($tag['name']); ?></a>
											<?php endforeach; ?>
										</span>
									<?php endif; ?>
								</div>
								<a href="/blog/<?php echo htmlspecialchars($post['slug']); ?>" class="btn"><span>read more</span></a>
							</div>
						</div>
					<?php endforeach; ?>
					
					<!-- PAGINATION -->
					<?php if ($total_pages > 1): ?>
						<div class="pagination-cover">
							<ul class="pagination">
								<?php if ($page > 1): ?>
									<li class="pagination-item item-prev">
										<a href="?page=<?php echo $page - 1; ?><?php echo $search_term ? '&s=' . urlencode($search_term) : ''; ?><?php echo $category_filter ? '&category=' . urlencode($category_filter) : ''; ?>">
											<i class="fa fa-angle-left" aria-hidden="true"></i>
										</a>
									</li>
								<?php endif; ?>
								
								<?php for ($i = 1; $i <= min($total_pages, 10); $i++): ?>
									<li class="pagination-item <?php echo $i === $page ? 'active' : ''; ?>">
										<a href="?page=<?php echo $i; ?><?php echo $search_term ? '&s=' . urlencode($search_term) : ''; ?><?php echo $category_filter ? '&category=' . urlencode($category_filter) : ''; ?>"><?php echo $i; ?></a>
									</li>
								<?php endfor; ?>
								
								<?php if ($page < $total_pages): ?>
									<li class="pagination-item item-next">
										<a href="?page=<?php echo $page + 1; ?><?php echo $search_term ? '&s=' . urlencode($search_term) : ''; ?><?php echo $category_filter ? '&category=' . urlencode($category_filter) : ''; ?>">
											<i class="fa fa-angle-right" aria-hidden="true"></i>
										</a>
									</li>
								<?php endif; ?>
							</ul>
						</div>
					<?php endif; ?>
				<?php endif; ?>
			</div>
			
			<!--================= SIDEBAR =================-->
			<div class="col-12 col-lg-3 sidebar">
				<a href="#" class="btn btn-sidebar"><span>Widgets</span></a>
				<ul class="widgets">
					<!--=========== WIDGET-SEARCH ===========-->
					<li class="widget widget-search">
						<h3 class="title">Search</h3>
						<form class="search-form" method="GET" action="/blog">
							<input class="inp-form" type="text" name="s" placeholder="Search" value="<?php echo htmlspecialchars($search_term); ?>">
							<button type="submit" class="btn-form"><i class="fa fa-search" aria-hidden="true"></i></button>
						</form>
					</li>
					<!--========= WIDGET-SEARCH END =========-->
					
					<!--========= WIDGET-CATEGORIES =========-->
					<?php if (!empty($all_categories)): ?>
						<li class="widget widget-categories">
							<h3 class="title">Categories</h3>
							<ul>
								<?php foreach ($all_categories as $category): ?>
									<li><a href="/blog?category=<?php echo urlencode($category['slug']); ?>"><?php echo htmlspecialchars($category['name']); ?></a></li>
								<?php endforeach; ?>
							</ul>
						</li>
					<?php endif; ?>
					<!--======= WIDGET-CATEGORIES END =======-->
					
					<!--======== WIDGET-RECENT-POSTS ========-->
					<?php if (!empty($recent_posts)): ?>
						<li class="widget widget-recent-posts">
							<h3 class="title">Recent Blog Posts</h3>
							<ul>
								<?php foreach ($recent_posts as $recent): ?>
									<li>
										<a href="/blog/<?php echo htmlspecialchars($recent['slug']); ?>"><?php echo htmlspecialchars($recent['title']); ?></a>
										<div class="date"><i class="fa fa-calendar" aria-hidden="true"></i><?php echo date('M j, Y \a\t g:i a', strtotime($recent['published_at'])); ?></div>
									</li>
								<?php endforeach; ?>
							</ul>
						</li>
					<?php endif; ?>
					<!--====== WIDGET-RECENT-POSTS END ======-->
				</ul>
			</div>
			<!--=============== SIDEBAR END ===============-->
		</div>
	</div>
</section>
<!--=================== S-NEWS END ===================-->

<?php include 'includes/footer.php'; ?>
