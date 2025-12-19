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
    private $plugin_folder;

    /**
     * Constructor
     */
    public function __construct($plugin_file) {
        $this->plugin_file = $plugin_file;
        $this->username = 'pereira-lui';
        $this->repo = 'faq-elementor';
        $this->plugin_folder = 'faq-elementor';
        $this->slug = $this->plugin_folder . '/' . basename($plugin_file);

        add_filter('pre_set_site_transient_update_plugins', [$this, 'check_update']);
        add_filter('plugins_api', [$this, 'plugin_info'], 20, 3);
        add_filter('upgrader_source_selection', [$this, 'fix_source_folder'], 10, 4);
        add_filter('upgrader_post_install', [$this, 'after_install'], 10, 3);
        
        // Add settings link
        add_filter('plugin_action_links_' . $this->slug, [$this, 'plugin_settings_link']);
    }

    /**
     * Get plugin data
     */
    private function get_plugin_data() {
        if (!empty($this->plugin_data)) {
            return;
        }
        
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

        // Check cache first
        $cache_key = 'faq_elementor_github_response';
        $cached = get_transient($cache_key);
        
        if ($cached !== false) {
            $this->github_response = $cached;
            return;
        }

        // First try to get the latest release
        $request_uri = sprintf('https://api.github.com/repos/%s/%s/releases/latest', $this->username, $this->repo);
        
        $response = wp_remote_get($request_uri, [
            'headers' => [
                'Accept' => 'application/vnd.github.v3+json',
                'User-Agent' => 'WordPress/' . get_bloginfo('version') . '; ' . home_url()
            ],
            'timeout' => 10,
        ]);

        $use_tags = false;
        
        if (is_wp_error($response) || wp_remote_retrieve_response_code($response) !== 200) {
            $use_tags = true;
        } else {
            $response_body = wp_remote_retrieve_body($response);
            $release_data = json_decode($response_body);
            
            // Check if we need to use tags instead (if tag is newer than release)
            $tags_response = $this->get_latest_tag();
            if ($tags_response && !empty($tags_response->name)) {
                $release_version = isset($release_data->tag_name) ? ltrim($release_data->tag_name, 'v') : '0.0.0';
                $tag_version = ltrim($tags_response->name, 'v');
                
                if (version_compare($tag_version, $release_version, '>')) {
                    $use_tags = true;
                } else {
                    $this->github_response = $release_data;
                }
            } else {
                $this->github_response = $release_data;
            }
        }
        
        // If release not found or tag is newer, get latest tag
        if ($use_tags) {
            $tag_data = $this->get_latest_tag();
            if ($tag_data) {
                $this->github_response = (object) [
                    'tag_name' => $tag_data->name,
                    'zipball_url' => sprintf('https://api.github.com/repos/%s/%s/zipball/%s', $this->username, $this->repo, $tag_data->name),
                    'published_at' => $tag_data->commit->committer->date ?? date('c'),
                    'body' => '',
                    'assets' => [],
                ];
            }
        }
        
        if (!empty($this->github_response)) {
            // Cache for 1 hour (reduced from 6)
            set_transient($cache_key, $this->github_response, 1 * HOUR_IN_SECONDS);
        }
    }
    
    /**
     * Get latest tag from GitHub
     */
    private function get_latest_tag() {
        $request_uri = sprintf('https://api.github.com/repos/%s/%s/tags', $this->username, $this->repo);
        
        $response = wp_remote_get($request_uri, [
            'headers' => [
                'Accept' => 'application/vnd.github.v3+json',
                'User-Agent' => 'WordPress/' . get_bloginfo('version') . '; ' . home_url()
            ],
            'timeout' => 10,
        ]);

        if (is_wp_error($response) || wp_remote_retrieve_response_code($response) !== 200) {
            return null;
        }

        $response_body = wp_remote_retrieve_body($response);
        $tags = json_decode($response_body);
        
        if (empty($tags) || !is_array($tags)) {
            return null;
        }
        
        // Tags are returned in order, first one is the latest
        return $tags[0];
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

        if (empty($this->github_response) || empty($this->github_response->tag_name)) {
            return $transient;
        }

        $github_version = ltrim($this->github_response->tag_name, 'v');
        $current_version = $this->plugin_data['Version'];

        if (version_compare($github_version, $current_version, '>')) {
            $package = $this->github_response->zipball_url;

            // Check if there's a specific asset (zip file named faq-elementor.zip)
            if (!empty($this->github_response->assets)) {
                foreach ($this->github_response->assets as $asset) {
                    if ($asset->name === 'faq-elementor.zip') {
                        $package = $asset->browser_download_url;
                        break;
                    }
                }
            }

            $transient->response[$this->slug] = (object) [
                'slug' => $this->plugin_folder,
                'plugin' => $this->slug,
                'new_version' => $github_version,
                'url' => 'https://github.com/' . $this->username . '/' . $this->repo,
                'package' => $package,
                'icons' => [],
                'banners' => [],
                'banners_rtl' => [],
                'tested' => '',
                'requires_php' => '7.4',
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

        if (!isset($args->slug) || $args->slug !== $this->plugin_folder) {
            return $result;
        }

        $this->get_plugin_data();
        $this->get_repository_info();

        if (empty($this->github_response)) {
            return $result;
        }

        $plugin_info = new stdClass();
        $plugin_info->name = $this->plugin_data['Name'];
        $plugin_info->slug = $this->plugin_folder;
        $plugin_info->version = ltrim($this->github_response->tag_name, 'v');
        $plugin_info->author = '<a href="https://github.com/' . $this->username . '">Lui</a>';
        $plugin_info->homepage = 'https://github.com/' . $this->username . '/' . $this->repo;
        $plugin_info->requires = '5.0';
        $plugin_info->requires_php = '7.4';
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
                if ($asset->name === 'faq-elementor.zip') {
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
            return '<p>Veja as notas de atualização no <a href="https://github.com/' . $this->username . '/' . $this->repo . '/releases" target="_blank">GitHub</a>.</p>';
        }

        // Convert markdown to basic HTML
        $changelog = esc_html($this->github_response->body);
        $changelog = nl2br($changelog);
        
        return '<div style="white-space: pre-wrap;">' . $changelog . '</div>';
    }

    /**
     * Fix the source folder name after download
     * This is the key function that renames the extracted folder
     */
    public function fix_source_folder($source, $remote_source, $upgrader, $hook_extra = null) {
        global $wp_filesystem;

        // Check if this is our plugin being updated
        if (!isset($hook_extra['plugin'])) {
            // Check by folder name pattern for GitHub downloads
            $source_basename = basename(untrailingslashit($source));
            if (strpos($source_basename, $this->username . '-' . $this->repo) === false && 
                strpos($source_basename, $this->repo . '-') === false) {
                return $source;
            }
        } elseif ($hook_extra['plugin'] !== $this->slug) {
            return $source;
        }

        // The correct folder name
        $correct_folder = trailingslashit($remote_source) . $this->plugin_folder;
        
        // If source is already correct, return it
        if (trailingslashit($source) === trailingslashit($correct_folder)) {
            return $source;
        }
        
        if (basename(untrailingslashit($source)) === $this->plugin_folder) {
            return $source;
        }

        // Move/rename the folder
        if ($wp_filesystem->move($source, $correct_folder, true)) {
            return trailingslashit($correct_folder);
        }

        return $source;
    }

    /**
     * After install - reactivate plugin if it was active
     */
    public function after_install($response, $hook_extra, $result) {
        global $wp_filesystem;

        // Check if this is our plugin
        if (!isset($hook_extra['plugin']) || $hook_extra['plugin'] !== $this->slug) {
            return $result;
        }

        // Clear the GitHub response cache
        delete_transient('faq_elementor_github_response');

        // Check if plugin was active before update
        $was_active = is_plugin_active($this->slug);

        // If the destination folder name is wrong, fix it
        $proper_destination = WP_PLUGIN_DIR . '/' . $this->plugin_folder;
        
        if (isset($result['destination']) && $result['destination'] !== $proper_destination && is_dir($result['destination'])) {
            // Remove old folder if exists
            if (is_dir($proper_destination)) {
                $wp_filesystem->delete($proper_destination, true);
            }
            
            // Move to correct location
            $wp_filesystem->move($result['destination'], $proper_destination, true);
            $result['destination'] = $proper_destination;
            $result['destination_name'] = $this->plugin_folder;
        }

        // Reactivate if it was active
        if ($was_active) {
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
    
    /**
     * Clear update cache (useful for testing)
     */
    public static function clear_cache() {
        delete_transient('faq_elementor_github_response');
        delete_site_transient('update_plugins');
    }
}
