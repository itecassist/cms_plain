<?php
/**
 * CMS Helper Functions
 */

/**
 * Check if user is logged in
 */
function is_logged_in() {
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

/**
 * Require login
 */
function require_login() {
    if (!is_logged_in()) {
        header('Location: admin/login.php');
        exit;
    }
}

/**
 * Get content for a specific page and zone
 */
function get_content($page, $zone, $default = '') {
    $content_file = CONTENT_DIR . '/' . sanitize_filename($page) . '.json';
    
    if (file_exists($content_file)) {
        $content = json_decode(file_get_contents($content_file), true);
        return isset($content[$zone]) ? $content[$zone] : $default;
    }
    
    return $default;
}

/**
 * Save content for a specific page
 */
function save_content($page, $data) {
    $content_file = CONTENT_DIR . '/' . sanitize_filename($page) . '.json';
    return file_put_contents($content_file, json_encode($data, JSON_PRETTY_PRINT));
}

/**
 * Get all editable pages
 */
function get_pages() {
    $pages = [];
    
    // Add global settings as a special page
    $pages[] = '_global';
    
    // Get PHP files in root directory (actual pages)
    $files = glob('*.php');
    
    foreach ($files as $file) {
        // Skip system files
        if (!in_array($file, ['config.php', 'functions.php', 'verify-setup.php'])) {
            $pages[] = $file;
        }
    }
    
    sort($pages);
    return $pages;
}

/**
 * Sanitize filename
 */
function sanitize_filename($filename) {
    return preg_replace('/[^a-zA-Z0-9._-]/', '_', $filename);
}

/**
 * Get editable zones from a page
 */
function get_editable_zones($page) {
    // Special handling for global settings
    if ($page === '_global') {
        return [
            'page-title' => ['name' => 'page-title', 'content' => 'Fitmax'],
            'meta-keywords' => ['name' => 'meta-keywords', 'content' => 'fitness, gym, crossfit, training'],
            'meta-description' => ['name' => 'meta-description', 'content' => 'Your fitness destination'],
            'header-phone' => ['name' => 'header-phone', 'content' => '1-800-488-6040'],
            'header-hours' => ['name' => 'header-hours', 'content' => 'Mon - Fri: 8:00AM - 7:00PM | Sat - Sun: Closed'],
            'social-facebook' => ['name' => 'social-facebook', 'content' => 'https://www.facebook.com'],
            'social-twitter' => ['name' => 'social-twitter', 'content' => 'https://twitter.com'],
            'social-youtube' => ['name' => 'social-youtube', 'content' => 'https://www.youtube.com'],
            'social-instagram' => ['name' => 'social-instagram', 'content' => 'https://www.instagram.com'],
            'footer-about' => ['name' => 'footer-about', 'content' => 'Your gym description here'],
            'footer-tweet1' => ['name' => 'footer-tweet1', 'content' => 'Latest tweet 1'],
            'footer-tweet2' => ['name' => 'footer-tweet2', 'content' => 'Latest tweet 2'],
            'footer-phone' => ['name' => 'footer-phone', 'content' => '1-800-488-6040'],
            'footer-email' => ['name' => 'footer-email', 'content' => 'CrossFit@gmail.com'],
            'footer-address' => ['name' => 'footer-address', 'content' => 'London, Street 225r.21'],
            'footer-copyright' => ['name' => 'footer-copyright', 'content' => 'Fitmax. All Rights Reserved.']
        ];
    }
    
    // For PHP pages in root directory
    $file = $page;
    
    if (!file_exists($file)) {
        return [];
    }
    
    $html = file_get_contents($file);
    $zones = [];
    
    // Find all data-editable attributes
    preg_match_all('/data-editable="([^"]+)"[^>]*>(.*?)<\/[^>]+>/s', $html, $matches);
    
    if (!empty($matches[1])) {
        foreach ($matches[1] as $index => $zone_name) {
            $zones[$zone_name] = [
                'name' => $zone_name,
                'content' => trim($matches[2][$index])
            ];
        }
    }
    
    return $zones;
}

/**
 * Render a page with editable content
 */
function render_page($page) {
    $file = TEMPLATE_DIR . '/' . $page;
    
    if (!file_exists($file)) {
        return false;
    }
    
    $html = file_get_contents($file);
    $content_file = CONTENT_DIR . '/' . sanitize_filename($page) . '.json';
    
    if (file_exists($content_file)) {
        $content = json_decode(file_get_contents($content_file), true);
        
        if ($content) {
            foreach ($content as $zone => $value) {
                // Replace content in data-editable zones
                $pattern = '/(data-editable="' . preg_quote($zone, '/') . '"[^>]*>)(.*?)(<\/[^>]+>)/s';
                $html = preg_replace($pattern, '$1' . $value . '$3', $html);
            }
        }
    }
    
    return $html;
}

/**
 * Handle file upload
 */
function handle_upload($file) {
    $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
    $max_size = 5 * 1024 * 1024; // 5MB
    
    if (!in_array($file['type'], $allowed_types)) {
        return ['success' => false, 'error' => 'Invalid file type. Only JPG, PNG, GIF, and WebP are allowed.'];
    }
    
    if ($file['size'] > $max_size) {
        return ['success' => false, 'error' => 'File too large. Maximum size is 5MB.'];
    }
    
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = uniqid() . '_' . time() . '.' . $extension;
    $destination = UPLOADS_DIR . '/' . $filename;
    
    if (move_uploaded_file($file['tmp_name'], $destination)) {
        return ['success' => true, 'filename' => $filename, 'path' => 'uploads/' . $filename];
    }
    
    return ['success' => false, 'error' => 'Failed to upload file.'];
}

/**
 * Get menu structure
 */
function get_menu() {
    $menu_file = CONTENT_DIR . '/_menu.json';
    
    if (file_exists($menu_file)) {
        return json_decode(file_get_contents($menu_file), true);
    }
    
    // Default menu structure
    return [
        'items' => [
            ['label' => 'Home', 'url' => 'index', 'parent' => null, 'order' => 1],
            ['label' => 'About', 'url' => 'about', 'parent' => null, 'order' => 2],
            ['label' => 'Services', 'url' => 'services', 'parent' => null, 'order' => 3],
            ['label' => 'Contact', 'url' => 'contacts', 'parent' => null, 'order' => 4]
        ]
    ];
}

/**
 * Save menu structure
 */
function save_menu($menu) {
    $menu_file = CONTENT_DIR . '/_menu.json';
    return file_put_contents($menu_file, json_encode($menu, JSON_PRETTY_PRINT));
}

/**
 * Get page status (published/unpublished)
 */
function get_page_status($page) {
    $status_file = CONTENT_DIR . '/_page_status.json';
    
    if (file_exists($status_file)) {
        $statuses = json_decode(file_get_contents($status_file), true);
        return isset($statuses[$page]) ? $statuses[$page] : 'published';
    }
    
    return 'published';
}

/**
 * Set page status
 */
function set_page_status($page, $status) {
    $status_file = CONTENT_DIR . '/_page_status.json';
    $statuses = [];
    
    if (file_exists($status_file)) {
        $statuses = json_decode(file_get_contents($status_file), true);
    }
    
    $statuses[$page] = $status;
    return file_put_contents($status_file, json_encode($statuses, JSON_PRETTY_PRINT));
}

/**
 * Build hierarchical menu (for rendering nested menus)
 */
function build_menu_tree($items, $parent = null) {
    $branch = [];
    
    foreach ($items as $item) {
        if ($item['parent'] === $parent) {
            $children = build_menu_tree($items, $item['label']);
            if ($children) {
                $item['children'] = $children;
            }
            $branch[] = $item;
        }
    }
    
    // Sort by order
    usort($branch, function($a, $b) {
        return ($a['order'] ?? 0) <=> ($b['order'] ?? 0);
    });
    
    return $branch;
}

/**
 * Render menu HTML
 */
function render_menu($tree = null, $is_submenu = false) {
    if ($tree === null) {
        $menu = get_menu();
        $tree = build_menu_tree($menu['items']);
    }
    
    if (empty($tree)) {
        return '';
    }
    
    $html = $is_submenu ? '<ul class="sub-menu">' : '';
    
    foreach ($tree as $item) {
        // Check if page is published
        $page_status = get_page_status($item['url'] . '.php');
        if ($page_status === 'unpublished') {
            continue; // Skip unpublished pages in menu
        }
        
        $has_children = isset($item['children']) && !empty($item['children']);
        $class = $has_children ? ' class="dropdown"' : '';
        
        $html .= '<li' . $class . '>';
        $html .= '<a href="' . htmlspecialchars($item['url']) . '">' . htmlspecialchars($item['label']);
        
        if ($has_children) {
            $html .= ' <i class="fa fa-caret-down"></i>';
        }
        
        $html .= '</a>';
        
        if ($has_children) {
            $html .= '<ul>';
            $html .= render_menu($item['children'], true);
            $html .= '</ul>';
        }
        
        $html .= '</li>';
    }
    
    $html .= $is_submenu ? '</ul>' : '';
    
    return $html;
}
