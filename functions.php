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
        header('Location: /admin/login.php');
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
    
    // Get all JSON files from ./content/ directory
    // These represent the actual editable pages
    $content_files = glob(CONTENT_DIR . '/*.php.json');
    
    foreach ($content_files as $file) {
        // Extract just the filename (e.g., "about.php.json" -> "about.php")
        $filename = basename($file, '.json');
        $pages[] = $filename;
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
 * Get page body content (HTML between header and footer includes)
 */
function get_page_body_content($page) {
    $file = '../' . $page;
    
    if (!file_exists($file)) {
        return '';
    }
    
    $content = file_get_contents($file);
    
    // Extract content between header and footer includes
    // Pattern: after include 'includes/header.php' and before include 'includes/footer.php'
    $pattern = '/include\s+[\'"]includes\/header\.php[\'"];?\s*\?>(.*?)<\?php\s+include\s+[\'"]includes\/footer\.php[\'"]/s';
    
    if (preg_match($pattern, $content, $matches)) {
        $body_content = trim($matches[1]);
        
        // Check if body content has PHP includes for sections
        if (strpos($body_content, 'include') !== false && strpos($body_content, 'sections/') !== false) {
            // Extract section file paths and render them
            preg_match_all('/include\s+[\'"]sections\/([^\'"]+)[\'"];?/i', $body_content, $section_matches);
            
            if (!empty($section_matches[1])) {
                $rendered_content = '';
                foreach ($section_matches[1] as $section_file) {
                    $section_path = '../sections/' . $section_file;
                    if (file_exists($section_path)) {
                        ob_start();
                        include $section_path;
                        $rendered_content .= ob_get_clean();
                    }
                }
                
                // Convert relative asset paths to absolute paths
                $rendered_content = fix_asset_paths($rendered_content);
                
                return $rendered_content;
            }
        }
        
        return $body_content;
    }
    
    return '';
}

/**
 * Fix relative asset paths to absolute URLs for editor
 */
function fix_asset_paths($content) {
    $base_url = BASE_URL;
    
    // Convert src="assets/ to src="http://localhost:8000/assets/
    $content = preg_replace('/src="assets\//', 'src="' . $base_url . '/assets/', $content);
    
    // Convert src='assets/ to src='http://localhost:8000/assets/
    $content = preg_replace('/src=\'assets\//', 'src=\'' . $base_url . '/assets/', $content);
    
    // Convert href="assets/ to href="http://localhost:8000/assets/
    $content = preg_replace('/href="assets\//', 'href="' . $base_url . '/assets/', $content);
    
    // Convert href='assets/ to href='http://localhost:8000/assets/
    $content = preg_replace('/href=\'assets\//', 'href=\'' . $base_url . '/assets/', $content);
    
    // Convert url(assets/ to url(http://localhost:8000/assets/
    $content = preg_replace('/url\(assets\//', 'url(' . $base_url . '/assets/', $content);
    
    // Convert style="background-image: url(assets/ patterns
    $content = preg_replace('/url\(\s*["\']?assets\//', 'url(' . $base_url . '/assets/', $content);
    
    return $content;
}

/**
 * Convert absolute URLs back to relative paths for storage
 */
function convert_to_relative_paths($content) {
    $base_url = BASE_URL;
    $escaped_base_url = preg_quote($base_url, '/');
    
    // Convert http://localhost:8000/assets/ to assets/
    $content = preg_replace('/' . $escaped_base_url . '\/assets\//', 'assets/', $content);
    
    // Also handle with quotes
    $content = preg_replace('/["\']' . $escaped_base_url . '\/assets\//', '"assets/', $content);
    $content = preg_replace('/[\']' . $escaped_base_url . '\/assets\//', '\'assets/', $content);
    
    return $content;
}

/**
 * Process component includes in content
 * Replaces {{component-name}} with content from components/component-name.html or .php
 * PHP components are executed, HTML components are included as-is
 */
function process_components($content) {
    // Find all {{component-name}} patterns
    preg_match_all('/\{\{([a-zA-Z0-9_-]+)\}\}/', $content, $matches);
    
    if (!empty($matches[0])) {
        foreach ($matches[0] as $index => $placeholder) {
            $component_name = $matches[1][$index];
            
            // Check for .php first, then .html
            $component_file = null;
            if (file_exists(COMPONENTS_DIR . '/' . $component_name . '.php')) {
                $component_file = COMPONENTS_DIR . '/' . $component_name . '.php';
                $is_php = true;
            } elseif (file_exists(COMPONENTS_DIR . '/' . $component_name . '.html')) {
                $component_file = COMPONENTS_DIR . '/' . $component_name . '.html';
                $is_php = false;
            }
            
            if ($component_file) {
                if ($is_php) {
                    // Execute PHP component and capture output
                    ob_start();
                    include $component_file;
                    $component_content = ob_get_clean();
                } else {
                    // Just read HTML component
                    $component_content = file_get_contents($component_file);
                }
                $content = str_replace($placeholder, $component_content, $content);
            }
        }
    }
    
    return $content;
}

/**
 * Get SEO data for a page
 */
function get_seo_data($page) {
    $seo_file = JSON_DIR . '/' . sanitize_filename($page) . '.json';
    
    if (file_exists($seo_file)) {
        return json_decode(file_get_contents($seo_file), true) ?? [];
    }
    
    // Return defaults
    return [
        'title' => SITE_NAME,
        'description' => '',
        'keywords' => '',
        'og_title' => '',
        'og_description' => '',
        'og_image' => ''
    ];
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
