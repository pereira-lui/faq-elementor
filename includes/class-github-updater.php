<?php
/**
 * GitHub Plugin Updater
 * 
 * Permite atualização automática do plugin via GitHub
 *
 * @package FAQ_Elementor
 */

if (!defined('ABSPATH')) {
    exit;
}

class FAQ_Elementor_GitHub_Updater {

    private $slug;
    private $plugin_data;
    private $username;
    private $repo;
    private $plugin_file;
    private $github_response;
    private $plugin_activated;

    /**
     * Constructor
     */
    public function __construct($plugin_file) {
        $this->plugin_file = $plugin_file;
        $this->username = 'pereira-lui';
        $this->repo = 'faq-elementor';
        $this->slug = plugin_basename($plugin_file);

        add_filter('pre_set_site_transient_update_plugins', [$this, 'check_update']);
        add_filter('plugins_api', [$this, 'plugin_info'], 20, 3);
        add_filter('upgrader_post_install', [$this, 'after_install'], 10, 3);
        
        // Add settings link
        add_filter('plugin_action_links_' . $this->slug, [$this, 'plugin_settings_link']);
    }

    /**
     * Get plugin data
     */
    private function get_plugin_data() {
        if (!function_exists('get_plugin_data')) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }
        $this->plugin_data = get_plugin_data($this->plugin_file);
    }

    /**
     * Get repository info from GitHub
     */
    private function get_repository_info() {
        if (!empty($this->github_response)) {
            return;
        }

        $request_uri = sprintf('https://api.github.com/repos/%s/%s/releases/latest', $this->username, $this->repo);
        
        $response = wp_remote_get($request_uri, [
            'headers' => [
                'Accept' => 'application/vnd.github.v3+json',
                'User-Agent' => 'WordPress/' . get_bloginfo('version')
            ]
        ]);

        if (is_wp_error($response)) {
            return;
        }

        $response_code = wp_remote_retrieve_response_code($response);
        
        if ($response_code !== 200) {
            return;
        }

        $response_body = wp_remote_retrieve_body($response);
        $this->github_response = json_decode($response_body);
    }

    /**
     * Check for plugin updates
     */
    public function check_update($transient) {
        if (empty($transient->checked)) {
            return $transient;
        }

        $this->get_plugin_data();
        $this->get_repository_info();

        if (empty($this->github_response)) {
            return $transient;
        }

        $github_version = ltrim($this->github_response->tag_name, 'v');
        $current_version = $this->plugin_data['Version'];

        if (version_compare($github_version, $current_version, '>')) {
            $package = $this->github_response->zipball_url;

            // Check if there's a specific asset (zip file)
            if (!empty($this->github_response->assets)) {
                foreach ($this->github_response->assets as $asset) {
                    if (strpos($asset->name, '.zip') !== false) {
                        $package = $asset->browser_download_url;
                        break;
                    }
                }
            }

            $transient->response[$this->slug] = (object) [
                'slug' => dirname($this->slug),
                'new_version' => $github_version,
                'url' => $this->plugin_data['PluginURI'],
                'package' => $package,
                'icons' => [],
                'banners' => [],
                'banners_rtl' => [],
                'tested' => '',
                'requires_php' => $this->plugin_data['RequiresPHP'],
                'compatibility' => new stdClass(),
            ];
        }

        return $transient;
    }

    /**
     * Get plugin info for the WordPress plugin information popup
     */
    public function plugin_info($result, $action, $args) {
        if ($action !== 'plugin_information') {
            return $result;
        }

        if (dirname($this->slug) !== $args->slug) {
            return $result;
        }

        $this->get_plugin_data();
        $this->get_repository_info();

        if (empty($this->github_response)) {
            return $result;
        }

        $plugin_info = new stdClass();
        $plugin_info->name = $this->plugin_data['Name'];
        $plugin_info->slug = dirname($this->slug);
        $plugin_info->version = ltrim($this->github_response->tag_name, 'v');
        $plugin_info->author = $this->plugin_data['AuthorName'];
        $plugin_info->homepage = $this->plugin_data['PluginURI'];
        $plugin_info->requires = $this->plugin_data['RequiresWP'];
        $plugin_info->requires_php = $this->plugin_data['RequiresPHP'];
        $plugin_info->downloaded = 0;
        $plugin_info->last_updated = $this->github_response->published_at;
        $plugin_info->sections = [
            'description' => $this->plugin_data['Description'],
            'changelog' => $this->get_changelog(),
        ];
        
        $plugin_info->download_link = $this->github_response->zipball_url;

        // Check for specific zip asset
        if (!empty($this->github_response->assets)) {
            foreach ($this->github_response->assets as $asset) {
                if (strpos($asset->name, '.zip') !== false) {
                    $plugin_info->download_link = $asset->browser_download_url;
                    break;
                }
            }
        }

        return $plugin_info;
    }

    /**
     * Get changelog from release notes
     */
    private function get_changelog() {
        if (empty($this->github_response->body)) {
            return '<p>Veja as notas de atualização no GitHub.</p>';
        }

        return '<pre>' . esc_html($this->github_response->body) . '</pre>';
    }

    /**
     * After install - rename folder to correct name
     */
    public function after_install($response, $hook_extra, $result) {
        global $wp_filesystem;

        // Check if this is our plugin
        if (!isset($hook_extra['plugin']) || $hook_extra['plugin'] !== $this->slug) {
            return $result;
        }

        // Check if plugin was active
        $this->plugin_activated = is_plugin_active($this->slug);

        // Rename the folder
        $plugin_folder = WP_PLUGIN_DIR . '/' . dirname($this->slug);
        $wp_filesystem->move($result['destination'], $plugin_folder);
        $result['destination'] = $plugin_folder;

        // Reactivate if it was active
        if ($this->plugin_activated) {
            activate_plugin($this->slug);
        }

        return $result;
    }

    /**
     * Add settings link to plugins page
     */
    public function plugin_settings_link($links) {
        $github_link = '<a href="https://github.com/' . $this->username . '/' . $this->repo . '" target="_blank">GitHub</a>';
        array_unshift($links, $github_link);
        return $links;
    }
}
