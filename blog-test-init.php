<?php
/**
 * Blog System Test & Initialization
 * Run this once to create sample data
 */
require_once 'config.php';
require_once 'blog-functions.php';

echo "<h1>Blog System Initialization</h1>";
echo "<style>body { font-family: Arial; padding: 20px; } .success { color: green; } .error { color: red; } .info { color: blue; }</style>";

// Get database connection
$db = get_db();

// Initialize database tables
echo "<h2>1. Creating Database Tables...</h2>";
try {
    init_blog_tables($db);
    echo "<p class='success'>✓ Database tables created successfully</p>";
} catch (Exception $e) {
    echo "<p class='error'>✗ Error: " . htmlspecialchars($e->getMessage()) . "</p>";
}

// Create sample categories
echo "<h2>2. Creating Sample Categories...</h2>";
$categories = [
    ['name' => 'Fitness', 'slug' => 'fitness'],
    ['name' => 'Nutrition', 'slug' => 'nutrition'],
    ['name' => 'Training', 'slug' => 'training'],
    ['name' => 'Health', 'slug' => 'health']
];

foreach ($categories as $cat) {
    try {
        get_or_create_category($cat['name']);
        echo "<p class='success'>✓ Created category: {$cat['name']}</p>";
    } catch (Exception $e) {
        echo "<p class='info'>→ Category '{$cat['name']}' may already exist</p>";
    }
}

// Create sample tags
echo "<h2>3. Creating Sample Tags...</h2>";
$tags = [
    ['name' => 'workout', 'slug' => 'workout'],
    ['name' => 'diet', 'slug' => 'diet'],
    ['name' => 'strength', 'slug' => 'strength'],
    ['name' => 'cardio', 'slug' => 'cardio'],
    ['name' => 'wellness', 'slug' => 'wellness']
];

foreach ($tags as $tag) {
    try {
        get_or_create_tag($tag['name']);
        echo "<p class='success'>✓ Created tag: {$tag['name']}</p>";
    } catch (Exception $e) {
        echo "<p class='info'>→ Tag '{$tag['name']}' may already exist</p>";
    }
}

// Create sample blog posts
echo "<h2>4. Creating Sample Blog Posts...</h2>";
$sample_posts = [
    [
        'title' => 'Welcome to Our Fitness Blog',
        'slug' => 'welcome-to-our-fitness-blog',
        'content' => '<p>Welcome to our fitness blog! We\'re excited to share expert tips, workout routines, and nutrition advice to help you achieve your fitness goals.</p><p>Whether you\'re just starting your fitness journey or you\'re a seasoned athlete, we have something for everyone. Stay tuned for regular updates!</p><h3>What You\'ll Find Here</h3><ul><li>Expert workout programs</li><li>Nutrition and diet tips</li><li>Healthy lifestyle advice</li><li>Success stories from our community</li></ul>',
        'excerpt' => 'Welcome to our fitness blog! We\'re excited to share expert tips, workout routines, and nutrition advice.',
        'author' => 'Admin',
        'status' => 'published',
        'featured_image' => 'assets/img/bg-1-min.png',
        'categories' => 'Fitness,Health',
        'tags' => 'wellness,workout'
    ],
    [
        'title' => '10 Essential Tips for Building Muscle',
        'slug' => '10-essential-tips-for-building-muscle',
        'content' => '<p>Building muscle requires dedication, proper nutrition, and the right training approach. Here are 10 essential tips to help you maximize your muscle gains.</p><h3>1. Progressive Overload</h3><p>Continuously challenge your muscles by gradually increasing weight, reps, or intensity.</p><h3>2. Protein Intake</h3><p>Consume adequate protein (1.6-2.2g per kg of body weight) to support muscle growth.</p><h3>3. Compound Exercises</h3><p>Focus on exercises like squats, deadlifts, and bench press that work multiple muscle groups.</p><h3>4. Recovery Time</h3><p>Give your muscles 48-72 hours to recover between training sessions.</p><h3>5. Sleep Quality</h3><p>Get 7-9 hours of quality sleep for optimal muscle recovery and growth hormone production.</p>',
        'excerpt' => 'Discover the 10 most important tips for building muscle effectively and safely.',
        'author' => 'John Trainer',
        'status' => 'published',
        'featured_image' => 'assets/img/bg-1-min.png',
        'categories' => 'Training,Fitness',
        'tags' => 'strength,workout'
    ],
    [
        'title' => 'The Complete Guide to Nutrition for Athletes',
        'slug' => 'complete-guide-to-nutrition-for-athletes',
        'content' => '<p>Proper nutrition is the foundation of athletic performance. This comprehensive guide covers everything you need to know about fueling your body for success.</p><h3>Macronutrients</h3><p>Understanding the role of carbohydrates, proteins, and fats in athletic performance.</p><h3>Meal Timing</h3><p>When to eat for optimal energy and recovery throughout your training schedule.</p><h3>Hydration</h3><p>The importance of staying properly hydrated before, during, and after workouts.</p><h3>Supplements</h3><p>Which supplements are worth taking and which ones to avoid.</p>',
        'excerpt' => 'A comprehensive guide to nutrition strategies for optimal athletic performance.',
        'author' => 'Sarah Nutritionist',
        'status' => 'published',
        'featured_image' => 'assets/img/bg-1-min.png',
        'categories' => 'Nutrition,Health',
        'tags' => 'diet,wellness'
    ],
    [
        'title' => 'High-Intensity Interval Training: Benefits and Best Practices',
        'slug' => 'hiit-benefits-and-best-practices',
        'content' => '<p>High-Intensity Interval Training (HIIT) has become one of the most popular and effective workout methods. Learn why it works and how to do it right.</p><h3>What is HIIT?</h3><p>HIIT involves short bursts of intense exercise followed by recovery periods.</p><h3>Benefits</h3><ul><li>Burns more calories in less time</li><li>Increases metabolic rate</li><li>Improves cardiovascular health</li><li>No equipment necessary</li></ul><h3>Sample HIIT Workout</h3><p>30 seconds sprint, 30 seconds rest - repeat 10 times. Always warm up first!</p>',
        'excerpt' => 'Learn about the benefits of HIIT and how to incorporate it into your training routine.',
        'author' => 'Mike Coach',
        'status' => 'published',
        'featured_image' => 'assets/img/bg-1-min.png',
        'categories' => 'Training,Fitness',
        'tags' => 'cardio,workout'
    ]
];

foreach ($sample_posts as $post) {
    try {
        // Check if post already exists
        $existing = get_post_by_slug($post['slug']);
        if ($existing) {
            echo "<p class='info'>→ Post already exists: {$post['title']}</p>";
            continue;
        }
        
        $post_id = create_post($post);
        echo "<p class='success'>✓ Created post: {$post['title']} (ID: $post_id)</p>";
        
        // Add a sample comment to the first post
        if ($post_id == 2) {
            $comment_data = [
                'post_id' => $post_id,
                'parent_id' => 0,
                'author_name' => 'John Doe',
                'author_email' => 'john@example.com',
                'content' => 'Great post! Looking forward to more content like this.'
            ];
            add_comment($comment_data);
            
            // Approve the comment automatically for demo
            $db = get_blog_db();
            $stmt = $db->prepare("UPDATE comments SET status = 'approved' WHERE post_id = ? LIMIT 1");
            $stmt->execute([$post_id]);
            
            echo "<p class='success'>  ↳ Added sample comment</p>";
        }
    } catch (Exception $e) {
        echo "<p class='error'>✗ Error creating post: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
}

// Get statistics
echo "<h2>5. Blog System Statistics</h2>";
$db = get_db();
$total_posts = $db->query("SELECT COUNT(*) FROM posts")->fetchColumn();
$published_posts = $db->query("SELECT COUNT(*) FROM posts WHERE status = 'published'")->fetchColumn();
$total_categories = $db->query("SELECT COUNT(*) FROM categories")->fetchColumn();
$total_tags = $db->query("SELECT COUNT(*) FROM tags")->fetchColumn();
$total_comments = $db->query("SELECT COUNT(*) FROM comments")->fetchColumn();
$pending_comments = $db->query("SELECT COUNT(*) FROM comments WHERE status = 'pending'")->fetchColumn();

echo "<ul>";
echo "<li><strong>Total Posts:</strong> $total_posts ($published_posts published)</li>";
echo "<li><strong>Categories:</strong> $total_categories</li>";
echo "<li><strong>Tags:</strong> $total_tags</li>";
echo "<li><strong>Comments:</strong> $total_comments ($pending_comments pending approval)</li>";
echo "</ul>";

echo "<h2>6. Next Steps</h2>";
echo "<ul>";
echo "<li><a href='/blog'>View Blog</a> - See your blog listing page</li>";
echo "<li><a href='/admin/blog.php'>Manage Posts</a> - Create, edit, or delete posts</li>";
echo "<li><a href='/admin/comments.php'>Manage Comments</a> - Approve or moderate comments</li>";
echo "</ul>";

echo "<h2>✓ Blog System Initialized Successfully!</h2>";
echo "<p>Your blog is ready to use. You can delete this file (blog-test-init.php) after initialization.</p>";
?>
