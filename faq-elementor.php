<?php
/**
 * Plugin Name: FAQ PDA Elementor
 * Plugin URI: https://github.com/pereira-lui/faq-elementor
 * Description: Plugin de FAQ personalizado com widget para Elementor. Permite cadastrar perguntas frequentes com tags e exibir no editor visual.
 * Version: 1.2.0
 * Author: Lui
 * Author URI: https://github.com/pereira-lui
 * Text Domain: faq-elementor
 * Domain Path: /languages
 * Requires at least: 5.0
 * Requires PHP: 7.4
 * Requires Plugins: elementor
 * Elementor tested up to: 3.18
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * GitHub Plugin URI: https://github.com/pereira-lui/faq-elementor
 * GitHub Branch: main
 * Update URI: https://github.com/pereira-lui/faq-elementor
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Define plugin constants
define('FAQ_ELEMENTOR_VERSION', '1.2.0');
define('FAQ_ELEMENTOR_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('FAQ_ELEMENTOR_PLUGIN_URL', plugin_dir_url(__FILE__));

/**
 * Main FAQ Elementor Class
 */
final class FAQ_Elementor {

    /**
     * Instance
     */
    private static $_instance = null;

    /**
     * Singleton Instance
     */
    public static function instance() {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * Constructor
     */
    public function __construct() {
        // Always register CPT and taxonomy
        add_action('init', [$this, 'register_faq_post_type']);
        add_action('init', [$this, 'register_faq_taxonomy']);
        
        // Admin menu icon
        add_action('admin_head', [$this, 'admin_menu_icon']);
        
        // Initialize Elementor integration
        add_action('plugins_loaded', [$this, 'init']);
        
        // Check if Elementor is active
        add_action('admin_notices', [$this, 'admin_notice_missing_elementor']);
        
        // Hide Rank Math SEO from FAQ post type
        add_filter('rank_math/metabox/post/screen', [$this, 'hide_rank_math_for_faq']);
        add_action('add_meta_boxes', [$this, 'remove_rank_math_metabox'], 99);
        add_filter('rank_math/frontend/disable', [$this, 'disable_rank_math_for_faq']);
        add_filter('rank_math/admin/disable_post_type', [$this, 'disable_rank_math_post_type']);
        
        // Admin scripts for tag search
        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_tag_search']);
        add_action('admin_enqueue_scripts', [$this, 'hide_rank_math_css']);
        add_filter('rank_math/sitemap/exclude_post_type', [$this, 'exclude_faq_from_rank_math_sitemap'], 10, 2);
        
        // Include GitHub updater early
        $this->includes();
    }

    /**
     * Hide Rank Math SEO metabox from FAQ post type
     */
    public function hide_rank_math_for_faq($screen) {
        global $post_type;
        
        if ($post_type === 'faq_item') {
            return false;
        }
        
        return $screen;
    }

    /**
     * Disable Rank Math for FAQ post type
     */
    public function disable_rank_math_for_faq($disable) {
        if (get_post_type() === 'faq_item') {
            return true;
        }
        return $disable;
    }

    /**
     * Disable Rank Math admin for FAQ post type
     */
    public function disable_rank_math_post_type($post_types) {
        $post_types[] = 'faq_item';
        return $post_types;
    }

    /**
     * Hide Rank Math elements via CSS
     */
    public function hide_rank_math_css($hook) {
        global $post_type;
        
        if (($hook === 'post.php' || $hook === 'post-new.php') && $post_type === 'faq_item') {
            wp_add_inline_style('wp-admin', '
                #rank_math_metabox,
                .rank-math-status,
                .rank-math-score,
                #rank-math-post-analysis,
                .rank-math-tooltip,
                .rank-math-notice-top,
                #setting-error-rank_math,
                .rank-math-link,
                #wp-admin-bar-rank-math,
                .misc-pub-section.rank-math-score,
                .edit-post-meta-boxes-area .rank-math-editor-general,
                #rank-math-editor-general,
                .components-panel__body.rank-math-editor-general,
                [class*="rank-math"] {
                    display: none !important;
                }
            ');
        }
    }

    /**
     * Remove Rank Math metabox from FAQ post type
     */
    public function remove_rank_math_metabox() {
        remove_meta_box('rank_math_metabox', 'faq_item', 'normal');
        remove_meta_box('rank_math_metabox', 'faq_item', 'side');
        remove_meta_box('rank_math_metabox', 'faq_item', 'advanced');
    }

    /**
     * Exclude FAQ from Rank Math sitemap (optional)
     */
    public function exclude_faq_from_rank_math_sitemap($exclude, $post_type) {
        if ($post_type === 'faq_item') {
            return true;
        }
        return $exclude;
    }

    /**
     * Enqueue admin scripts for tag search
     */
    public function enqueue_admin_tag_search($hook) {
        global $post_type;
        
        // Only on FAQ post edit screen
        if (($hook === 'post.php' || $hook === 'post-new.php') && $post_type === 'faq_item') {
            // Get all tags
            $tags = get_terms([
                'taxonomy' => 'faq_tag',
                'hide_empty' => false,
                'orderby' => 'name',
                'order' => 'ASC',
            ]);
            
            $tag_list = [];
            if (!is_wp_error($tags) && !empty($tags)) {
                foreach ($tags as $tag) {
                    $tag_list[] = [
                        'id' => $tag->term_id,
                        'name' => $tag->name,
                        'slug' => $tag->slug,
                    ];
                }
            }
            
            // Inline script and styles
            wp_add_inline_script('jquery', $this->get_admin_tag_search_script($tag_list));
            wp_add_inline_style('wp-admin', $this->get_admin_tag_search_styles());
        }
    }

    /**
     * Get admin tag search script
     */
    private function get_admin_tag_search_script($tags) {
        $tags_json = json_encode($tags);
        return "
        jQuery(document).ready(function($) {
            var allTags = {$tags_json};
            var tagBox = $('#tagsdiv-faq_tag .tagsdiv');
            
            if (tagBox.length && allTags.length > 0) {
                // Replace 'most used' link with search
                var mostUsed = tagBox.find('.tagcloud-link');
                mostUsed.text('Buscar todas as tags');
                
                // Create search container
                var searchContainer = $('<div class=\"faq-tag-search-container\" style=\"display:none;\"></div>');
                var searchInput = $('<input type=\"text\" class=\"faq-tag-search-input\" placeholder=\"Digite para buscar tags...\" style=\"width:100%; margin-bottom:10px; padding:8px;\">');
                var tagsList = $('<div class=\"faq-tag-search-results\"></div>');
                
                searchContainer.append(searchInput).append(tagsList);
                tagBox.find('.ajaxtag').after(searchContainer);
                
                // Toggle search on click
                mostUsed.on('click', function(e) {
                    e.preventDefault();
                    searchContainer.slideToggle();
                    if (searchContainer.is(':visible')) {
                        searchInput.focus();
                        renderTags('');
                    }
                });
                
                // Render tags function
                function renderTags(filter) {
                    tagsList.empty();
                    var filtered = allTags.filter(function(tag) {
                        return tag.name.toLowerCase().indexOf(filter.toLowerCase()) !== -1;
                    });
                    
                    if (filtered.length === 0) {
                        tagsList.append('<p style=\"color:#666; font-style:italic;\">Nenhuma tag encontrada.</p>');
                        return;
                    }
                    
                    filtered.forEach(function(tag) {
                        var tagBtn = $('<button type=\"button\" class=\"faq-tag-search-btn\">' + tag.name + '</button>');
                        tagBtn.on('click', function() {
                            // Add tag to input
                            var input = tagBox.find('.newtag');
                            var currentVal = input.val();
                            if (currentVal) {
                                input.val(currentVal + ', ' + tag.name);
                            } else {
                                input.val(tag.name);
                            }
                            // Trigger add
                            tagBox.find('.tagadd').click();
                        });
                        tagsList.append(tagBtn);
                    });
                }
                
                // Search on input
                searchInput.on('input', function() {
                    renderTags($(this).val());
                });
            }
        });
        ";
    }

    /**
     * Get admin tag search styles
     */
    private function get_admin_tag_search_styles() {
        return "
        .faq-tag-search-container {
            margin-top: 15px;
            padding: 15px;
            background: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .faq-tag-search-results {
            max-height: 200px;
            overflow-y: auto;
            display: flex;
            flex-wrap: wrap;
            gap: 5px;
        }
        .faq-tag-search-btn {
            background: #0073aa;
            color: #fff;
            border: none;
            padding: 5px 12px;
            border-radius: 3px;
            cursor: pointer;
            font-size: 12px;
            transition: background 0.2s;
        }
        .faq-tag-search-btn:hover {
            background: #005a87;
        }
        ";
    }

    /**
     * Admin notice if Elementor is not active
     */
    public function admin_notice_missing_elementor() {
        if (!did_action('elementor/loaded')) {
            echo '<div class="notice notice-warning is-dismissible"><p>';
            echo '<strong>FAQ PDA Elementor</strong> requer o plugin <strong>Elementor</strong> para o widget funcionar. O sistema de perguntas frequentes está funcionando, mas o widget visual não estará disponível.';
            echo '</p></div>';
        }
    }

    /**
     * Initialize Elementor integration
     */
    public function init() {
        // Check if Elementor is loaded
        if (!did_action('elementor/loaded')) {
            return;
        }

        // Load text domain
        load_plugin_textdomain('faq-elementor', false, dirname(plugin_basename(__FILE__)) . '/languages');

        // Register Elementor Widget
        add_action('elementor/widgets/register', [$this, 'register_widgets']);
        
        // Register custom widget category
        add_action('elementor/elements/categories_registered', [$this, 'register_widget_category']);

        // Enqueue scripts and styles
        add_action('wp_enqueue_scripts', [$this, 'enqueue_frontend_assets']);
        add_action('elementor/editor/after_enqueue_scripts', [$this, 'enqueue_editor_assets']);
        
        // Enqueue scripts in Elementor preview
        add_action('elementor/preview/enqueue_scripts', [$this, 'enqueue_frontend_assets']);

        // AJAX handlers for search and tag tracking
        add_action('wp_ajax_faq_search', [$this, 'ajax_faq_search']);
        add_action('wp_ajax_nopriv_faq_search', [$this, 'ajax_faq_search']);
        add_action('wp_ajax_faq_track_tag', [$this, 'ajax_track_tag']);
        add_action('wp_ajax_nopriv_faq_track_tag', [$this, 'ajax_track_tag']);
        add_action('wp_ajax_faq_get_popular_tags', [$this, 'ajax_get_popular_tags']);
        add_action('wp_ajax_nopriv_faq_get_popular_tags', [$this, 'ajax_get_popular_tags']);
    }

    /**
     * Include required files
     */
    public function includes() {
        require_once FAQ_ELEMENTOR_PLUGIN_DIR . 'includes/class-github-updater.php';
        
        // Initialize GitHub Updater
        new FAQ_Elementor_GitHub_Updater(__FILE__);
    }

    /**
     * Register custom widget category
     */
    public function register_widget_category($elements_manager) {
        $elements_manager->add_category(
            'faq-pda',
            [
                'title' => __('FAQ PDA', 'faq-elementor'),
                'icon' => 'eicon-help-o',
            ]
        );
    }

    /**
     * Register Elementor Widgets
     */
    public function register_widgets($widgets_manager) {
        // Only load widget class when Elementor is ready
        require_once FAQ_ELEMENTOR_PLUGIN_DIR . 'includes/class-faq-widget.php';
        $widgets_manager->register(new \FAQ_Elementor_Widget());
    }

    /**
     * Register FAQ Custom Post Type
     */
    public function register_faq_post_type() {
        $labels = [
            'name'                  => __('Perguntas Frequentes', 'faq-elementor'),
            'singular_name'         => __('Pergunta', 'faq-elementor'),
            'menu_name'             => __('FAQ', 'faq-elementor'),
            'name_admin_bar'        => __('Pergunta FAQ', 'faq-elementor'),
            'add_new'               => __('Adicionar Nova', 'faq-elementor'),
            'add_new_item'          => __('Adicionar Nova Pergunta', 'faq-elementor'),
            'new_item'              => __('Nova Pergunta', 'faq-elementor'),
            'edit_item'             => __('Editar Pergunta', 'faq-elementor'),
            'view_item'             => __('Ver Pergunta', 'faq-elementor'),
            'all_items'             => __('Todas as Perguntas', 'faq-elementor'),
            'search_items'          => __('Buscar Perguntas', 'faq-elementor'),
            'parent_item_colon'     => __('Pergunta Pai:', 'faq-elementor'),
            'not_found'             => __('Nenhuma pergunta encontrada.', 'faq-elementor'),
            'not_found_in_trash'    => __('Nenhuma pergunta na lixeira.', 'faq-elementor'),
            'featured_image'        => __('Imagem da Pergunta', 'faq-elementor'),
            'set_featured_image'    => __('Definir imagem', 'faq-elementor'),
            'remove_featured_image' => __('Remover imagem', 'faq-elementor'),
            'use_featured_image'    => __('Usar como imagem', 'faq-elementor'),
            'archives'              => __('Arquivo de Perguntas', 'faq-elementor'),
            'insert_into_item'      => __('Inserir na pergunta', 'faq-elementor'),
            'uploaded_to_this_item' => __('Enviado para esta pergunta', 'faq-elementor'),
            'filter_items_list'     => __('Filtrar lista de perguntas', 'faq-elementor'),
            'items_list_navigation' => __('Navegação da lista de perguntas', 'faq-elementor'),
            'items_list'            => __('Lista de perguntas', 'faq-elementor'),
        ];

        $args = [
            'labels'             => $labels,
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'query_var'          => true,
            'rewrite'            => ['slug' => 'faq'],
            'capability_type'    => 'post',
            'has_archive'        => true,
            'hierarchical'       => false,
            'menu_position'      => 25,
            'menu_icon'          => 'dashicons-editor-help',
            'supports'           => ['title', 'editor', 'author', 'revisions'],
            'show_in_rest'       => true,
        ];

        register_post_type('faq_item', $args);
    }

    /**
     * Register FAQ Taxonomy (Tags)
     */
    public function register_faq_taxonomy() {
        $labels = [
            'name'                       => __('Tags FAQ', 'faq-elementor'),
            'singular_name'              => __('Tag FAQ', 'faq-elementor'),
            'search_items'               => __('Buscar Tags', 'faq-elementor'),
            'popular_items'              => __('Tags Populares', 'faq-elementor'),
            'all_items'                  => __('Todas as Tags', 'faq-elementor'),
            'parent_item'                => null,
            'parent_item_colon'          => null,
            'edit_item'                  => __('Editar Tag', 'faq-elementor'),
            'update_item'                => __('Atualizar Tag', 'faq-elementor'),
            'add_new_item'               => __('Adicionar Nova Tag', 'faq-elementor'),
            'new_item_name'              => __('Nome da Nova Tag', 'faq-elementor'),
            'separate_items_with_commas' => __('Separe as tags com vírgulas', 'faq-elementor'),
            'add_or_remove_items'        => __('Adicionar ou remover tags', 'faq-elementor'),
            'choose_from_most_used'      => __('Escolher das mais usadas', 'faq-elementor'),
            'not_found'                  => __('Nenhuma tag encontrada.', 'faq-elementor'),
            'menu_name'                  => __('Tags FAQ', 'faq-elementor'),
        ];

        $args = [
            'hierarchical'          => false,
            'labels'                => $labels,
            'show_ui'               => true,
            'show_admin_column'     => true,
            'update_count_callback' => '_update_post_term_count',
            'query_var'             => true,
            'rewrite'               => ['slug' => 'faq-tag'],
            'show_in_rest'          => true,
        ];

        register_taxonomy('faq_tag', 'faq_item', $args);
    }

    /**
     * Enqueue frontend assets
     */
    public function enqueue_frontend_assets() {
        wp_enqueue_style(
            'faq-elementor-style',
            FAQ_ELEMENTOR_PLUGIN_URL . 'assets/css/faq-style.css',
            [],
            FAQ_ELEMENTOR_VERSION
        );

        wp_enqueue_script(
            'faq-elementor-script',
            FAQ_ELEMENTOR_PLUGIN_URL . 'assets/js/faq-script.js',
            ['jquery'],
            FAQ_ELEMENTOR_VERSION,
            true
        );

        // Pass AJAX URL and nonce to JavaScript
        wp_localize_script('faq-elementor-script', 'faqElementor', [
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('faq_elementor_nonce'),
        ]);
    }

    /**
     * Enqueue editor assets
     */
    public function enqueue_editor_assets() {
        wp_enqueue_style(
            'faq-elementor-editor',
            FAQ_ELEMENTOR_PLUGIN_URL . 'assets/css/faq-editor.css',
            [],
            FAQ_ELEMENTOR_VERSION
        );
    }

    /**
     * Admin menu icon styling
     */
    public function admin_menu_icon() {
        echo '<style>
            #adminmenu .menu-icon-faq_item div.wp-menu-image:before {
                content: "\f223";
            }
        </style>';
    }

    /**
     * AJAX handler for FAQ search
     */
    public function ajax_faq_search() {
        check_ajax_referer('faq_elementor_nonce', 'nonce');

        $search = isset($_POST['search']) ? sanitize_text_field($_POST['search']) : '';
        $tag = isset($_POST['tag']) ? sanitize_text_field($_POST['tag']) : '';

        $args = [
            'post_type' => 'faq_item',
            'posts_per_page' => -1,
            'post_status' => 'publish',
        ];

        // Search query
        if (!empty($search)) {
            $args['s'] = $search;
        }

        // Filter by tag
        if (!empty($tag)) {
            $args['tax_query'] = [
                [
                    'taxonomy' => 'faq_tag',
                    'field' => 'slug',
                    'terms' => $tag,
                ],
            ];
        }

        $query = new WP_Query($args);
        $results = [];

        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                
                $post_tags = get_the_terms(get_the_ID(), 'faq_tag');
                $tag_slugs = [];
                if ($post_tags && !is_wp_error($post_tags)) {
                    foreach ($post_tags as $t) {
                        $tag_slugs[] = $t->slug;
                    }
                }

                $results[] = [
                    'id' => get_the_ID(),
                    'title' => get_the_title(),
                    'content' => apply_filters('the_content', get_the_content()),
                    'tags' => $tag_slugs,
                ];
            }
        }
        wp_reset_postdata();

        wp_send_json_success($results);
    }

    /**
     * AJAX handler for tracking tag clicks
     */
    public function ajax_track_tag() {
        check_ajax_referer('faq_elementor_nonce', 'nonce');

        $tag_slug = isset($_POST['tag']) ? sanitize_text_field($_POST['tag']) : '';

        if (empty($tag_slug)) {
            wp_send_json_error('Tag not provided');
        }

        // Get current tag stats
        $tag_stats = get_option('faq_tag_stats', []);

        // Increment count
        if (isset($tag_stats[$tag_slug])) {
            $tag_stats[$tag_slug]++;
        } else {
            $tag_stats[$tag_slug] = 1;
        }

        // Save stats
        update_option('faq_tag_stats', $tag_stats);

        wp_send_json_success(['count' => $tag_stats[$tag_slug]]);
    }

    /**
     * AJAX handler for getting popular tags
     */
    public function ajax_get_popular_tags() {
        check_ajax_referer('faq_elementor_nonce', 'nonce');

        // Get all FAQ tags
        $all_tags = get_terms([
            'taxonomy' => 'faq_tag',
            'hide_empty' => true,
        ]);

        if (is_wp_error($all_tags)) {
            wp_send_json_error('Error fetching tags');
        }

        // Get tag stats
        $tag_stats = get_option('faq_tag_stats', []);

        // Build tags array with counts
        $tags = [];
        foreach ($all_tags as $tag) {
            $count = isset($tag_stats[$tag->slug]) ? $tag_stats[$tag->slug] : 0;
            $tags[] = [
                'name' => $tag->name,
                'slug' => $tag->slug,
                'count' => $count,
            ];
        }

        // Sort by count (most popular first)
        usort($tags, function($a, $b) {
            return $b['count'] - $a['count'];
        });

        wp_send_json_success($tags);
    }

    /**
     * Get popular tags (for widget render)
     * Only returns tags that have been clicked at least once
     */
    public static function get_popular_tags_list() {
        $tag_stats = get_option('faq_tag_stats', []);
        
        // If no tags have been clicked yet, return empty array
        if (empty($tag_stats)) {
            return [];
        }

        $all_tags = get_terms([
            'taxonomy' => 'faq_tag',
            'hide_empty' => true,
        ]);

        if (is_wp_error($all_tags) || empty($all_tags)) {
            return [];
        }

        $tags = [];
        foreach ($all_tags as $tag) {
            // Only include tags that have been clicked at least once
            if (isset($tag_stats[$tag->slug]) && $tag_stats[$tag->slug] > 0) {
                $tags[] = [
                    'name' => $tag->name,
                    'slug' => $tag->slug,
                    'count' => $tag_stats[$tag->slug],
                ];
            }
        }

        // Sort by count (most popular first)
        usort($tags, function($a, $b) {
            return $b['count'] - $a['count'];
        });

        return $tags;
    }
}

// Initialize the plugin
FAQ_Elementor::instance();
