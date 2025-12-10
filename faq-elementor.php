<?php
/**
 * Plugin Name: FAQ Elementor
 * Plugin URI: https://github.com/pereira-lui/faq-elementor
 * Description: Plugin de FAQ personalizado com widget para Elementor. Permite cadastrar perguntas frequentes com tags e exibir no editor visual.
 * Version: 1.0.1
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
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Define plugin constants
define('FAQ_ELEMENTOR_VERSION', '1.0.1');
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
        add_action('plugins_loaded', [$this, 'init']);
    }

    /**
     * Initialize the plugin
     */
    public function init() {
        // Load text domain
        load_plugin_textdomain('faq-elementor', false, dirname(plugin_basename(__FILE__)) . '/languages');

        // Include required files
        $this->includes();

        // Register Custom Post Type
        add_action('init', [$this, 'register_faq_post_type']);
        add_action('init', [$this, 'register_faq_taxonomy']);

        // Register Elementor Widget
        add_action('elementor/widgets/register', [$this, 'register_widgets']);

        // Enqueue scripts and styles
        add_action('wp_enqueue_scripts', [$this, 'enqueue_frontend_assets']);
        add_action('elementor/editor/after_enqueue_scripts', [$this, 'enqueue_editor_assets']);

        // Add admin menu icon
        add_action('admin_head', [$this, 'admin_menu_icon']);

        // Add meta boxes
        add_action('add_meta_boxes', [$this, 'add_faq_meta_boxes']);
        add_action('save_post', [$this, 'save_faq_meta']);
    }

    /**
     * Include required files
     */
    public function includes() {
        require_once FAQ_ELEMENTOR_PLUGIN_DIR . 'includes/class-faq-widget.php';
        require_once FAQ_ELEMENTOR_PLUGIN_DIR . 'includes/class-github-updater.php';
        
        // Initialize GitHub Updater
        new FAQ_Elementor_GitHub_Updater(__FILE__);
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
     * Register Elementor Widgets
     */
    public function register_widgets($widgets_manager) {
        $widgets_manager->register(new \FAQ_Elementor_Widget());
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
     * Add meta boxes for additional FAQ fields
     */
    public function add_faq_meta_boxes() {
        add_meta_box(
            'faq_order',
            __('Ordem de Exibição', 'faq-elementor'),
            [$this, 'render_order_meta_box'],
            'faq_item',
            'side',
            'high'
        );
    }

    /**
     * Render order meta box
     */
    public function render_order_meta_box($post) {
        wp_nonce_field('faq_order_nonce', 'faq_order_nonce_field');
        $order = get_post_meta($post->ID, '_faq_order', true);
        ?>
        <label for="faq_order"><?php _e('Ordem:', 'faq-elementor'); ?></label>
        <input type="number" id="faq_order" name="faq_order" value="<?php echo esc_attr($order ?: 0); ?>" min="0" style="width: 100%;">
        <p class="description"><?php _e('Números menores aparecem primeiro.', 'faq-elementor'); ?></p>
        <?php
    }

    /**
     * Save FAQ meta
     */
    public function save_faq_meta($post_id) {
        // Check nonce
        if (!isset($_POST['faq_order_nonce_field']) || !wp_verify_nonce($_POST['faq_order_nonce_field'], 'faq_order_nonce')) {
            return;
        }

        // Check autosave
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        // Check permissions
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        // Save order
        if (isset($_POST['faq_order'])) {
            update_post_meta($post_id, '_faq_order', intval($_POST['faq_order']));
        }
    }
}

// Initialize the plugin
FAQ_Elementor::instance();
