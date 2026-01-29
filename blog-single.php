<?php
/**
 * Single Blog Post Page - Template Style
 * Based on template/single-blog.html.htm - Easy to customize
 */
require_once 'config.php';
require_once 'functions.php';

// Get post slug from URL
$slug = isset($_GET['slug']) ? trim($_GET['slug']) : '';

if (empty($slug)) {
    header('Location: /blog');
    exit;
}

// Get the post
$post = get_post_by_slug($slug);

if (!$post || $post['status'] !== 'published') {
    header('HTTP/1.0 404 Not Found');
    echo '404 - Post not found';
    exit;
}

// Handle comment submission
$comment_error = '';
$comment_success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_comment'])) {
    $comment_data = [
        'post_id' => $post['id'],
        'parent_id' => isset($_POST['parent_id']) ? (int)$_POST['parent_id'] : 0,
        'author_name' => trim($_POST['author_name'] ?? ''),
        'author_email' => trim($_POST['author_email'] ?? ''),
        'content' => trim($_POST['content'] ?? '')
    ];
    
    // Validate
    if (empty($comment_data['author_name'])) {
        $comment_error = 'Name is required';
    } elseif (empty($comment_data['author_email'])) {
        $comment_error = 'Email is required';
    } elseif (!filter_var($comment_data['author_email'], FILTER_VALIDATE_EMAIL)) {
        $comment_error = 'Valid email is required';
    } elseif (empty($comment_data['content'])) {
        $comment_error = 'Comment is required';
    } else {
        if (add_comment($comment_data)) {
            $comment_success = 'Your comment has been submitted and is awaiting approval.';
        } else {
            $comment_error = 'Failed to submit comment. Please try again.';
        }
    }
}

// Get comments for this post
$comments = get_post_comments($post['id']);
$comment_count = get_comment_count($post['id']);

// Get previous and next posts
$prev_post = get_previous_post($post['id']);
$next_post = get_next_post($post['id']);

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
    'title' => htmlspecialchars($post['title']) . ' - ' . SITE_NAME,
    'description' => htmlspecialchars($post['excerpt'] ?: substr(strip_tags($post['content']), 0, 160)),
    'keywords' => !empty($post['tags']) ? implode(', ', array_column($post['tags'], 'name')) : ''
];

// Add custom blog CSS
$custom_css = ['assets/css/blog-custom.css'];

$current_page = 'blog.php';
include 'includes/header.php';
?>

<!-- =============== HEADER-TITLE =============== -->
<section class="s-header-title" style="background-image: url(/assets/img/bg-1-min.png);">
	<div class="container">
		<h1 class="title"><?php echo htmlspecialchars($post['title']); ?></h1>
		<ul class="breadcrambs">
			<li><a href="/">Home</a></li>
			<li><a href="/blog">Blog</a></li>
			<li><?php echo htmlspecialchars($post['title']); ?></li>
		</ul>
	</div>
</section>
<!-- ============= HEADER-TITLE END ============= -->

<!--===================== S-NEWS =====================-->
<section class="s-news">
	<div class="container">
		<div class="row">
			<div class="col-12 col-lg-9 blog-cover">
				<!-- POST ITEM -->
				<div class="post-item-cover">
					<?php if ($post['featured_image']): ?>
						<div class="post-header">
							<div class="post-thumbnail">
								<img src="<?php echo htmlspecialchars($post['featured_image']); ?>" alt="<?php echo htmlspecialchars($post['title']); ?>">
							</div>
						</div>
					<?php endif; ?>
					
					<div class="post-content">
						<div class="meta">
							<span class="post-by"><i class="fa fa-user" aria-hidden="true"></i><a href="#">By <?php echo htmlspecialchars($post['author']); ?></a></span>
							<span class="post-date"><i class="fa fa-calendar" aria-hidden="true"></i><?php echo date('F j, Y', strtotime($post['published_at'])); ?></span>
							<?php if (!empty($post['categories'])): ?>
								<span class="post-category"><i class="fa fa-tag" aria-hidden="true"></i>
									<?php foreach ($post['categories'] as $category): ?>
										<a href="/blog?category=<?php echo urlencode($category['slug']); ?>"><?php echo htmlspecialchars($category['name']); ?></a>
									<?php endforeach; ?>
								</span>
							<?php endif; ?>
						</div>
						
						<h2 class="title"><?php echo htmlspecialchars($post['title']); ?></h2>
						
						<div class="text">
							<?php echo $post['content']; ?>
						</div>
						
						<?php if (!empty($post['tags'])): ?>
							<div class="post-footer">
								<div class="meta">
									<span class="post-tags"><i class="fa fa-hashtag" aria-hidden="true"></i>
										<?php foreach ($post['tags'] as $tag): ?>
											<a href="/blog?s=<?php echo urlencode($tag['name']); ?>"><?php echo htmlspecialchars($tag['name']); ?></a>
										<?php endforeach; ?>
									</span>
								</div>
							</div>
						<?php endif; ?>
					</div>
				</div>
				
				<!--=============== AUTHOR BOX ===============-->
				<div class="author-box">
					<div class="author-avatar">
						<img src="https://www.gravatar.com/avatar/<?php echo md5(strtolower(trim($post['author']))); ?>?d=mp&s=100" alt="<?php echo htmlspecialchars($post['author']); ?>">
					</div>
					<div class="author-info">
						<h3 class="author-name"><?php echo htmlspecialchars($post['author']); ?></h3>
						<p class="author-bio">Article author</p>
					</div>
				</div>
				<!--============= AUTHOR BOX END =============-->
				
				<!--=============== NAVIGATION ===============-->
				<?php if ($prev_post || $next_post): ?>
					<div class="navigation">
						<?php if ($prev_post): ?>
							<div class="navigation-left">
								<a href="/blog/<?php echo htmlspecialchars($prev_post['slug']); ?>">
									<span class="nav-label"><i class="fa fa-angle-left" aria-hidden="true"></i> Previous</span>
									<span class="nav-title"><?php echo htmlspecialchars($prev_post['title']); ?></span>
								</a>
							</div>
						<?php endif; ?>
						
						<?php if ($next_post): ?>
							<div class="navigation-right">
								<a href="/blog/<?php echo htmlspecialchars($next_post['slug']); ?>">
									<span class="nav-label">Next <i class="fa fa-angle-right" aria-hidden="true"></i></span>
									<span class="nav-title"><?php echo htmlspecialchars($next_post['title']); ?></span>
								</a>
							</div>
						<?php endif; ?>
					</div>
				<?php endif; ?>
				<!--============= NAVIGATION END =============-->
				
				<!--=============== COMMENTS ===============-->
				<div class="reviews" id="comments">
					<h2 class="title">Comments (<?php echo $comment_count; ?>)</h2>
					
					<?php if ($comment_success): ?>
						<div class="alert alert-success">
							<?php echo htmlspecialchars($comment_success); ?>
						</div>
					<?php endif; ?>
					
					<?php if ($comment_error): ?>
						<div class="alert alert-error">
							<?php echo htmlspecialchars($comment_error); ?>
						</div>
					<?php endif; ?>
					
					<?php if (!empty($comments)): ?>
						<ul class="reviews-list">
							<?php 
							function render_comment($comment) {
								?>
								<li class="review-item<?php echo $comment['parent_id'] > 0 ? ' child' : ''; ?>">
									<div class="review-avatar">
										<img src="https://www.gravatar.com/avatar/<?php echo md5(strtolower(trim($comment['author_email']))); ?>?d=mp&s=80" alt="<?php echo htmlspecialchars($comment['author_name']); ?>">
									</div>
									<div class="review-content">
										<div class="review-header">
											<h4 class="review-author"><?php echo htmlspecialchars($comment['author_name']); ?></h4>
											<span class="review-date"><?php echo date('F j, Y \a\t g:i a', strtotime($comment['created_at'])); ?></span>
										</div>
										<div class="review-text">
											<p><?php echo nl2br(htmlspecialchars($comment['content'])); ?></p>
										</div>
										<button class="btn-reply" onclick="showReplyForm(<?php echo $comment['id']; ?>, '<?php echo htmlspecialchars(addslashes($comment['author_name'])); ?>')">
											<i class="fa fa-reply" aria-hidden="true"></i> Reply
										</button>
									</div>
									
									<?php if (!empty($comment['replies'])): ?>
										<ul class="reviews-list">
											<?php foreach ($comment['replies'] as $reply): ?>
												<?php render_comment($reply); ?>
											<?php endforeach; ?>
										</ul>
									<?php endif; ?>
								</li>
								<?php
							}
							
							foreach ($comments as $comment) {
								render_comment($comment);
							}
							?>
						</ul>
					<?php else: ?>
						<p>No comments yet. Be the first to comment!</p>
					<?php endif; ?>
					
					<!--========= COMMENT FORM =========-->
					<div class="leave-comment">
						<h3 class="title">Leave a Comment</h3>
						<form class="comment-form" method="POST" action="">
							<input type="hidden" name="parent_id" id="parent_id" value="0">
							<div id="reply-notice" style="display: none; margin-bottom: 15px; padding: 10px; background: #f0f0f0; border-radius: 4px;">
								<span id="reply-text"></span>
								<button type="button" onclick="cancelReply()" style="margin-left: 10px;">Cancel</button>
							</div>
							
							<div class="row">
								<div class="col-12 col-lg-6">
									<input class="inp-form" type="text" name="author_name" placeholder="Name*" required value="<?php echo isset($_POST['author_name']) ? htmlspecialchars($_POST['author_name']) : ''; ?>">
								</div>
								<div class="col-12 col-lg-6">
									<input class="inp-form" type="email" name="author_email" placeholder="Email*" required value="<?php echo isset($_POST['author_email']) ? htmlspecialchars($_POST['author_email']) : ''; ?>">
								</div>
								<div class="col-12">
									<textarea class="txt-form" name="content" placeholder="Your Comment*" required><?php echo isset($_POST['content']) ? htmlspecialchars($_POST['content']) : ''; ?></textarea>
								</div>
								<div class="col-12">
									<button class="btn" type="submit" name="submit_comment"><span>Submit Comment</span></button>
								</div>
							</div>
						</form>
					</div>
					<!--======= COMMENT FORM END =======-->
				</div>
				<!--============= COMMENTS END =============-->
			</div>
			
			<!--================= SIDEBAR =================-->
			<div class="col-12 col-lg-3 sidebar">
				<a href="#" class="btn btn-sidebar"><span>Widgets</span></a>
				<ul class="widgets">
					<!--=========== WIDGET-SEARCH ===========-->
					<li class="widget widget-search">
						<h3 class="title">Search</h3>
						<form class="search-form" method="GET" action="/blog">
							<input class="inp-form" type="text" name="s" placeholder="Search">
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

<script>
function showReplyForm(commentId, authorName) {
    document.getElementById('parent_id').value = commentId;
    document.getElementById('reply-notice').style.display = 'block';
    document.getElementById('reply-text').textContent = 'Replying to ' + authorName;
    document.querySelector('.comment-form textarea').focus();
}

function cancelReply() {
    document.getElementById('parent_id').value = '0';
    document.getElementById('reply-notice').style.display = 'none';
}
</script>

<?php include 'includes/footer.php'; ?>
