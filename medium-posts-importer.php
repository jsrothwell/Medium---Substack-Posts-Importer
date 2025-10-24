<?php
/**
 * Plugin Name: Medium & Substack Posts Importer
 * Plugin URI: https://github.com/jsrothwell/medium-substack-importer
 * Description: Import and display Medium and Substack posts with proper featured image support
 * Version: 1.1.0
 * Author: Jamieson Rothwell
 * Author URI: https://lymegrove.com
 * License: GPL v2 or later
 * Text Domain: medium-posts-importer
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

class Medium_Substack_Posts_Importer {
    
    private $option_name = 'mpi_settings';
    
    public function __construct() {
        // Admin menu
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_init', array($this, 'register_settings'));
        
        // Shortcode
        add_shortcode('medium_posts', array($this, 'display_medium_posts'));
        
        // Enqueue styles
        add_action('wp_enqueue_scripts', array($this, 'enqueue_styles'));
    }
    
    /**
     * Add admin menu
     */
    public function add_admin_menu() {
        add_options_page(
            'Medium & Substack Posts Importer',
            'Medium & Substack',
            'manage_options',
            'medium-posts-importer',
            array($this, 'settings_page')
        );
    }
    
    /**
     * Register settings
     */
    public function register_settings() {
        register_setting('mpi_settings_group', $this->option_name);
        
        add_settings_section(
            'mpi_main_section',
            'Feed Settings',
            array($this, 'settings_section_callback'),
            'medium-posts-importer'
        );
        
        add_settings_field(
            'platform',
            'Platform',
            array($this, 'platform_callback'),
            'medium-posts-importer',
            'mpi_main_section'
        );
        
        add_settings_field(
            'medium_handle',
            'Medium Handle',
            array($this, 'medium_handle_callback'),
            'medium-posts-importer',
            'mpi_main_section'
        );
        
        add_settings_field(
            'substack_url',
            'Substack URL',
            array($this, 'substack_url_callback'),
            'medium-posts-importer',
            'mpi_main_section'
        );
        
        add_settings_field(
            'posts_count',
            'Number of Posts',
            array($this, 'posts_count_callback'),
            'medium-posts-importer',
            'mpi_main_section'
        );
        
        add_settings_field(
            'cache_duration',
            'Cache Duration (hours)',
            array($this, 'cache_duration_callback'),
            'medium-posts-importer',
            'mpi_main_section'
        );
        
        add_settings_field(
            'show_excerpt',
            'Show Excerpt',
            array($this, 'show_excerpt_callback'),
            'medium-posts-importer',
            'mpi_main_section'
        );
        
        add_settings_field(
            'excerpt_length',
            'Excerpt Length (words)',
            array($this, 'excerpt_length_callback'),
            'medium-posts-importer',
            'mpi_main_section'
        );
    }
    
    public function settings_section_callback() {
        echo '<p>Configure your Medium or Substack RSS feed settings below.</p>';
    }
    
    public function platform_callback() {
        $options = get_option($this->option_name);
        $platform = isset($options['platform']) ? $options['platform'] : 'medium';
        ?>
        <select name="<?php echo $this->option_name; ?>[platform]" id="mpi_platform">
            <option value="medium" <?php selected($platform, 'medium'); ?>>Medium</option>
            <option value="substack" <?php selected($platform, 'substack'); ?>>Substack</option>
        </select>
        <p class="description">Choose your publishing platform</p>
        <script>
        jQuery(document).ready(function($) {
            function togglePlatformFields() {
                var platform = $('#mpi_platform').val();
                if (platform === 'medium') {
                    $('#mpi_platform').closest('tr').nextAll('tr').eq(0).show(); // Medium handle
                    $('#mpi_platform').closest('tr').nextAll('tr').eq(1).hide(); // Substack URL
                } else {
                    $('#mpi_platform').closest('tr').nextAll('tr').eq(0).hide(); // Medium handle
                    $('#mpi_platform').closest('tr').nextAll('tr').eq(1).show(); // Substack URL
                }
            }
            
            togglePlatformFields();
            $('#mpi_platform').change(togglePlatformFields);
        });
        </script>
        <?php
    }
    
    public function medium_handle_callback() {
        $options = get_option($this->option_name);
        $handle = isset($options['medium_handle']) ? $options['medium_handle'] : '';
        echo '<input type="text" name="' . $this->option_name . '[medium_handle]" value="' . esc_attr($handle) . '" class="regular-text" placeholder="yourusername" />';
        echo '<p class="description">Your Medium username (e.g., johndoe or @johndoe)</p>';
    }
    
    public function substack_url_callback() {
        $options = get_option($this->option_name);
        $url = isset($options['substack_url']) ? $options['substack_url'] : '';
        echo '<input type="text" name="' . $this->option_name . '[substack_url]" value="' . esc_attr($url) . '" class="regular-text" placeholder="yourname.substack.com" />';
        echo '<p class="description">Your Substack subdomain (e.g., yourname.substack.com or custom domain)</p>';
    }
    
    public function posts_count_callback() {
        $options = get_option($this->option_name);
        $count = isset($options['posts_count']) ? $options['posts_count'] : 10;
        echo '<input type="number" name="' . $this->option_name . '[posts_count]" value="' . esc_attr($count) . '" min="1" max="50" />';
    }
    
    public function cache_duration_callback() {
        $options = get_option($this->option_name);
        $duration = isset($options['cache_duration']) ? $options['cache_duration'] : 12;
        echo '<input type="number" name="' . $this->option_name . '[cache_duration]" value="' . esc_attr($duration) . '" min="1" max="168" />';
        echo '<p class="description">How long to cache Medium feed results (1-168 hours)</p>';
    }
    
    public function show_excerpt_callback() {
        $options = get_option($this->option_name);
        $show_excerpt = isset($options['show_excerpt']) ? $options['show_excerpt'] : 1;
        echo '<input type="checkbox" name="' . $this->option_name . '[show_excerpt]" value="1" ' . checked(1, $show_excerpt, false) . ' />';
        echo ' <label>Display post excerpts</label>';
    }
    
    public function excerpt_length_callback() {
        $options = get_option($this->option_name);
        $length = isset($options['excerpt_length']) ? $options['excerpt_length'] : 50;
        echo '<input type="number" name="' . $this->option_name . '[excerpt_length]" value="' . esc_attr($length) . '" min="10" max="200" />';
    }
    
    /**
     * Settings page HTML
     */
    public function settings_page() {
        if (!current_user_can('manage_options')) {
            return;
        }
        
        if (isset($_GET['settings-updated'])) {
            // Clear the cache when settings are updated
            delete_transient('mpi_medium_feed');
            delete_transient('mpi_substack_feed');
            // Clear all mpi_feed transients
            global $wpdb;
            $wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_mpi_feed_%'");
            $wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_timeout_mpi_feed_%'");
            add_settings_error('mpi_messages', 'mpi_message', 'Settings Saved & Cache Cleared', 'updated');
        }
        
        settings_errors('mpi_messages');
        ?>
        <div class="wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            <form action="options.php" method="post">
                <?php
                settings_fields('mpi_settings_group');
                do_settings_sections('medium-posts-importer');
                submit_button('Save Settings');
                ?>
            </form>
            
            <hr>
            
            <h2>How to Use</h2>
            <p>Use this shortcode to display your Medium or Substack posts anywhere on your site:</p>
            <code>[medium_posts]</code>
            
            <p>Optional parameters:</p>
            <ul>
                <li><code>[medium_posts count="5"]</code> - Override number of posts</li>
                <li><code>[medium_posts columns="3"]</code> - Set grid columns (1-4)</li>
            </ul>
            
            <p><strong>Note:</strong> The shortcode name is <code>[medium_posts]</code> but it works for both Medium and Substack based on your platform selection above.</p>
            
            <hr>
            
            <h2>Clear Cache</h2>
            <p>If your posts aren't updating, you can manually clear the cache:</p>
            <form method="post">
                <?php wp_nonce_field('mpi_clear_cache', 'mpi_clear_cache_nonce'); ?>
                <input type="submit" name="clear_cache" class="button" value="Clear Cache Now">
            </form>
            
            <?php
            if (isset($_POST['clear_cache']) && check_admin_referer('mpi_clear_cache', 'mpi_clear_cache_nonce')) {
                delete_transient('mpi_medium_feed');
                delete_transient('mpi_substack_feed');
                // Clear all mpi_feed transients
                global $wpdb;
                $wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_mpi_feed_%'");
                $wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_timeout_mpi_feed_%'");
                echo '<div class="notice notice-success"><p>Cache cleared successfully!</p></div>';
            }
            ?>
        </div>
        <?php
    }
    
    /**
     * Fetch feed from Medium or Substack
     */
    private function fetch_feed($platform, $identifier, $count) {
        // Check cache first
        $cache_key = 'mpi_feed_' . md5($platform . $identifier . $count);
        $cached_data = get_transient($cache_key);
        
        if ($cached_data !== false) {
            return $cached_data;
        }
        
        // Construct feed URL based on platform
        if ($platform === 'medium') {
            // Clean handle
            $identifier = str_replace('@', '', $identifier);
            $feed_url = "https://medium.com/feed/@{$identifier}";
        } else {
            // Substack
            // Clean URL
            $identifier = str_replace(array('https://', 'http://'), '', $identifier);
            $identifier = rtrim($identifier, '/');
            $feed_url = "https://{$identifier}/feed";
        }
        
        // Fetch RSS feed
        $response = wp_remote_get($feed_url, array(
            'timeout' => 15,
            'sslverify' => false
        ));
        
        if (is_wp_error($response)) {
            return array('error' => 'Could not fetch feed: ' . $response->get_error_message());
        }
        
        $body = wp_remote_retrieve_body($response);
        
        if (empty($body)) {
            return array('error' => 'Empty response from ' . ucfirst($platform));
        }
        
        // Parse XML
        libxml_use_internal_errors(true);
        $xml = simplexml_load_string($body);
        
        if ($xml === false) {
            return array('error' => 'Could not parse feed XML');
        }
        
        $posts = array();
        $items = $xml->channel->item;
        
        foreach ($items as $index => $item) {
            if ($index >= $count) {
                break;
            }
            
            // Get content - Substack uses description, Medium uses content:encoded
            $content = '';
            if (isset($item->children('content', true)->encoded)) {
                $content = (string) $item->children('content', true)->encoded;
            } else {
                $content = (string) $item->description;
            }
            
            $description = (string) $item->description;
            
            // Extract first image from content
            $image_url = $this->extract_first_image($content, $platform);
            
            // Get categories
            $categories = array();
            if (isset($item->category)) {
                foreach ($item->category as $cat) {
                    $categories[] = (string) $cat;
                }
            }
            
            // Get creator - different namespaces for different platforms
            $creator = '';
            if (isset($item->children('dc', true)->creator)) {
                $creator = (string) $item->children('dc', true)->creator;
            } elseif (isset($item->author)) {
                $creator = (string) $item->author;
            }
            
            $posts[] = array(
                'title' => (string) $item->title,
                'link' => (string) $item->link,
                'pubDate' => (string) $item->pubDate,
                'creator' => $creator,
                'content' => $content,
                'description' => $description,
                'image' => $image_url,
                'categories' => $categories
            );
        }
        
        $data = array('posts' => $posts);
        
        // Cache the results
        $options = get_option($this->option_name);
        $cache_duration = isset($options['cache_duration']) ? intval($options['cache_duration']) : 12;
        set_transient($cache_key, $data, $cache_duration * HOUR_IN_SECONDS);
        
        return $data;
    }
    
    /**
     * Extract first image from HTML content
     */
    private function extract_first_image($content, $platform = 'medium') {
        // For Substack, look for specific image patterns
        if ($platform === 'substack') {
            // Substack often uses CDN URLs
            if (preg_match('/<img[^>]+src=["\']([^"\']*substackcdn\.com[^"\']+)["\'][^>]*>/i', $content, $matches)) {
                return $matches[1];
            }
        }
        
        // Try to find img tag
        if (preg_match('/<img[^>]+src=["\']([^"\']+)["\'][^>]*>/i', $content, $matches)) {
            return $matches[1];
        }
        
        // Try to find figure with img
        if (preg_match('/<figure[^>]*>.*?<img[^>]+src=["\']([^"\']+)["\'][^>]*>.*?<\/figure>/is', $content, $matches)) {
            return $matches[1];
        }
        
        // Try to find picture element
        if (preg_match('/<picture[^>]*>.*?<img[^>]+src=["\']([^"\']+)["\'][^>]*>.*?<\/picture>/is', $content, $matches)) {
            return $matches[1];
        }
        
        return '';
    }
    
    /**
     * Create excerpt from HTML content
     */
    private function create_excerpt($content, $word_count = 50) {
        // Strip HTML tags
        $text = strip_tags($content);
        
        // Remove extra whitespace
        $text = preg_replace('/\s+/', ' ', $text);
        $text = trim($text);
        
        // Get words
        $words = explode(' ', $text);
        
        if (count($words) > $word_count) {
            $words = array_slice($words, 0, $word_count);
            $text = implode(' ', $words) . '...';
        }
        
        return $text;
    }
    
    /**
     * Shortcode to display Medium or Substack posts
     */
    public function display_medium_posts($atts) {
        $options = get_option($this->option_name);
        
        $atts = shortcode_atts(array(
            'count' => isset($options['posts_count']) ? $options['posts_count'] : 10,
            'columns' => 3
        ), $atts);
        
        $platform = isset($options['platform']) ? $options['platform'] : 'medium';
        
        if ($platform === 'medium') {
            $identifier = isset($options['medium_handle']) ? $options['medium_handle'] : '';
            $error_message = 'Please configure your Medium handle in Settings → Medium Posts';
        } else {
            $identifier = isset($options['substack_url']) ? $options['substack_url'] : '';
            $error_message = 'Please configure your Substack URL in Settings → Medium Posts';
        }
        
        if (empty($identifier)) {
            return '<p class="mpi-error">' . esc_html($error_message) . '</p>';
        }
        
        $data = $this->fetch_feed($platform, $identifier, $atts['count']);
        
        if (isset($data['error'])) {
            return '<p class="mpi-error">' . esc_html($data['error']) . '</p>';
        }
        
        if (empty($data['posts'])) {
            return '<p class="mpi-error">No posts found</p>';
        }
        
        $show_excerpt = isset($options['show_excerpt']) ? $options['show_excerpt'] : 1;
        $excerpt_length = isset($options['excerpt_length']) ? intval($options['excerpt_length']) : 50;
        
        $columns = intval($atts['columns']);
        $columns = max(1, min(4, $columns)); // Limit between 1-4
        
        $platform_label = ucfirst($platform);
        
        ob_start();
        ?>
        <div class="mpi-posts-grid mpi-columns-<?php echo $columns; ?>">
            <?php foreach ($data['posts'] as $post): ?>
                <article class="mpi-post">
                    <?php if (!empty($post['image'])): ?>
                        <div class="mpi-post-image">
                            <a href="<?php echo esc_url($post['link']); ?>" target="_blank" rel="noopener">
                                <img src="<?php echo esc_url($post['image']); ?>" alt="<?php echo esc_attr($post['title']); ?>" loading="lazy">
                            </a>
                        </div>
                    <?php endif; ?>
                    
                    <div class="mpi-post-content">
                        <h3 class="mpi-post-title">
                            <a href="<?php echo esc_url($post['link']); ?>" target="_blank" rel="noopener">
                                <?php echo esc_html($post['title']); ?>
                            </a>
                        </h3>
                        
                        <div class="mpi-post-meta">
                            <span class="mpi-post-date"><?php echo date('F j, Y', strtotime($post['pubDate'])); ?></span>
                            <?php if (!empty($post['categories'])): ?>
                                <span class="mpi-post-categories">
                                    <?php echo esc_html(implode(', ', array_slice($post['categories'], 0, 3))); ?>
                                </span>
                            <?php endif; ?>
                        </div>
                        
                        <?php if ($show_excerpt): ?>
                            <div class="mpi-post-excerpt">
                                <?php echo esc_html($this->create_excerpt($post['content'], $excerpt_length)); ?>
                            </div>
                        <?php endif; ?>
                        
                        <a href="<?php echo esc_url($post['link']); ?>" class="mpi-read-more" target="_blank" rel="noopener">
                            Read on <?php echo $platform_label; ?> →
                        </a>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
        <?php
        return ob_get_clean();
    }
    
    /**
     * Enqueue styles
     */
    public function enqueue_styles() {
        wp_enqueue_style('mpi-styles', plugin_dir_url(__FILE__) . 'assets/mpi-styles.css', array(), '1.0.0');
        
        // If CSS file doesn't exist, add inline styles
        if (!file_exists(plugin_dir_path(__FILE__) . 'assets/mpi-styles.css')) {
            $css = "
                .mpi-posts-grid {
                    display: grid;
                    gap: 2rem;
                    margin: 2rem 0;
                }
                
                .mpi-columns-1 { grid-template-columns: 1fr; }
                .mpi-columns-2 { grid-template-columns: repeat(2, 1fr); }
                .mpi-columns-3 { grid-template-columns: repeat(3, 1fr); }
                .mpi-columns-4 { grid-template-columns: repeat(4, 1fr); }
                
                @media (max-width: 768px) {
                    .mpi-posts-grid { grid-template-columns: 1fr !important; }
                }
                
                .mpi-post {
                    background: #fff;
                    border-radius: 8px;
                    overflow: hidden;
                    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
                    transition: transform 0.2s, box-shadow 0.2s;
                }
                
                .mpi-post:hover {
                    transform: translateY(-4px);
                    box-shadow: 0 4px 16px rgba(0,0,0,0.15);
                }
                
                .mpi-post-image {
                    position: relative;
                    padding-bottom: 56.25%;
                    overflow: hidden;
                    background: #f5f5f5;
                }
                
                .mpi-post-image img {
                    position: absolute;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    object-fit: cover;
                    transition: transform 0.3s;
                }
                
                .mpi-post:hover .mpi-post-image img {
                    transform: scale(1.05);
                }
                
                .mpi-post-content {
                    padding: 1.5rem;
                }
                
                .mpi-post-title {
                    margin: 0 0 0.75rem 0;
                    font-size: 1.25rem;
                    line-height: 1.4;
                }
                
                .mpi-post-title a {
                    color: #333;
                    text-decoration: none;
                }
                
                .mpi-post-title a:hover {
                    color: #000;
                }
                
                .mpi-post-meta {
                    display: flex;
                    gap: 1rem;
                    margin-bottom: 1rem;
                    font-size: 0.875rem;
                    color: #666;
                }
                
                .mpi-post-categories {
                    font-style: italic;
                }
                
                .mpi-post-excerpt {
                    margin-bottom: 1rem;
                    color: #555;
                    line-height: 1.6;
                }
                
                .mpi-read-more {
                    display: inline-block;
                    color: #0066cc;
                    text-decoration: none;
                    font-weight: 500;
                }
                
                .mpi-read-more:hover {
                    text-decoration: underline;
                }
                
                .mpi-error {
                    padding: 1rem;
                    background: #fff3cd;
                    border: 1px solid #ffc107;
                    border-radius: 4px;
                    color: #856404;
                }
            ";
            
            wp_add_inline_style('mpi-styles', $css);
        }
    }
}

// Initialize the plugin
new Medium_Substack_Posts_Importer();
