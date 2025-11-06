<?php
/**
 * Main Plugin Class
 *
 * @package Philosofeet
 */

namespace Philosofeet;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * PhilosofeetCORE Main Class
 */
final class PhilosofeetCORE {

    /**
     * Plugin instance
     *
     * @var PhilosofeetCORE
     */
    private static $_instance = null;

    /**
     * Minimum Elementor Version
     *
     * @var string
     */
    const MINIMUM_ELEMENTOR_VERSION = '3.0.0';

    /**
     * Minimum PHP Version
     *
     * @var string
     */
    const MINIMUM_PHP_VERSION = '7.4';

    /**
     * Instance
     *
     * @return PhilosofeetCORE
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
    private function __construct() {
        add_action('init', [$this, 'i18n']);
        // Call init directly since we're already in plugins_loaded
        $this->init();
    }

    /**
     * Load translations
     */
    public function i18n() {
        load_plugin_textdomain('philosofeet-core', false, dirname(plugin_basename(PHILOSOFEET_CORE_FILE)) . '/languages');
    }

    /**
     * Initialize the plugin
     */
    public function init() {
        // Check if Elementor is installed and activated
        if (!did_action('elementor/loaded')) {
            add_action('admin_notices', [$this, 'admin_notice_missing_elementor']);
            return;
        }

        // Check for required Elementor version
        if (!version_compare(ELEMENTOR_VERSION, self::MINIMUM_ELEMENTOR_VERSION, '>=')) {
            add_action('admin_notices', [$this, 'admin_notice_minimum_elementor_version']);
            return;
        }

        // Check for required PHP version
        if (version_compare(PHP_VERSION, self::MINIMUM_PHP_VERSION, '<')) {
            add_action('admin_notices', [$this, 'admin_notice_minimum_php_version']);
            return;
        }

        // Load plugin files
        $this->includes();

        // Register Elementor category
        add_action('elementor/elements/categories_registered', [$this, 'register_widget_categories'], 10);

        // Register widgets
        add_action('elementor/widgets/register', [$this, 'register_widgets'], 10);

        // Register scripts and styles
        add_action('wp_enqueue_scripts', [$this, 'enqueue_frontend_scripts']);
        add_action('elementor/frontend/after_enqueue_styles', [$this, 'enqueue_frontend_styles']);

        // Load debug file if debug mode is enabled
        if (defined('PHILOSOFEET_DEBUG') && PHILOSOFEET_DEBUG) {
            $debug_file = PHILOSOFEET_CORE_PATH . 'debug-widgets.php';
            if (file_exists($debug_file)) {
                require_once $debug_file;
            }
        }
    }

    /**
     * Include required files
     */
    private function includes() {
        require_once PHILOSOFEET_CORE_PATH . 'includes/class-widget-manager.php';
    }

    /**
     * Register widget categories
     */
    public function register_widget_categories($elements_manager) {
        $elements_manager->add_category(
            'philosofeet',
            [
                'title' => __('Philosofeet', 'philosofeet-core'),
                'icon' => 'fa fa-plug',
            ]
        );
    }

    /**
     * Register widgets
     */
    public function register_widgets($widgets_manager) {
        $widget_manager = new Widget_Manager();
        $widget_manager->register_widgets($widgets_manager);
    }

    /**
     * Enqueue frontend scripts
     */
    public function enqueue_frontend_scripts() {
        $is_dev = defined('WP_DEBUG') && WP_DEBUG && file_exists(PHILOSOFEET_CORE_PATH . '.vite-dev');

        if ($is_dev) {
            // Development mode: Load from Vite dev server
            wp_enqueue_script(
                'philosofeet-core-vite-client',
                'http://localhost:5173/@vite/client',
                [],
                null,
                true
            );

            wp_enqueue_script(
                'philosofeet-core-react',
                'http://localhost:5173/assets/js/src/index.js',
                [],
                null,
                true
            );

            // Add type="module" attribute
            add_filter('script_loader_tag', function($tag, $handle) {
                if (in_array($handle, ['philosofeet-core-vite-client', 'philosofeet-core-react'])) {
                    return str_replace('<script ', '<script type="module" ', $tag);
                }
                return $tag;
            }, 10, 2);
        } else {
            // Production mode: Load built files
            wp_enqueue_script(
                'philosofeet-core-react',
                PHILOSOFEET_CORE_ASSETS_URL . 'js/dist/main.js',
                [],
                PHILOSOFEET_CORE_VERSION,
                true
            );

            // Add type="module" attribute for production build
            add_filter('script_loader_tag', function($tag, $handle) {
                if ($handle === 'philosofeet-core-react') {
                    return str_replace('<script ', '<script type="module" ', $tag);
                }
                return $tag;
            }, 10, 2);
        }

        // Pass global config to React
        wp_localize_script('philosofeet-core-react', 'philosofeetCore', [
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('philosofeet_core_nonce'),
            'assetsUrl' => PHILOSOFEET_CORE_ASSETS_URL,
            'isDev' => $is_dev,
        ]);
    }

    /**
     * Enqueue frontend styles
     */
    public function enqueue_frontend_styles() {
        wp_enqueue_style(
            'philosofeet-core-global',
            PHILOSOFEET_CORE_ASSETS_URL . 'css/global.css',
            [],
            PHILOSOFEET_CORE_VERSION
        );
    }

    /**
     * Admin notice for missing Elementor
     */
    public function admin_notice_missing_elementor() {
        $message = sprintf(
            esc_html__('"%1$s" requires "%2$s" to be installed and activated.', 'philosofeet-core'),
            '<strong>' . esc_html__('Philosofeet Core', 'philosofeet-core') . '</strong>',
            '<strong>' . esc_html__('Elementor', 'philosofeet-core') . '</strong>'
        );

        printf('<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message);
    }

    /**
     * Admin notice for minimum Elementor version
     */
    public function admin_notice_minimum_elementor_version() {
        $message = sprintf(
            esc_html__('"%1$s" requires "%2$s" version %3$s or greater.', 'philosofeet-core'),
            '<strong>' . esc_html__('Philosofeet Core', 'philosofeet-core') . '</strong>',
            '<strong>' . esc_html__('Elementor', 'philosofeet-core') . '</strong>',
            self::MINIMUM_ELEMENTOR_VERSION
        );

        printf('<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message);
    }

    /**
     * Admin notice for minimum PHP version
     */
    public function admin_notice_minimum_php_version() {
        $message = sprintf(
            esc_html__('"%1$s" requires "%2$s" version %3$s or greater.', 'philosofeet-core'),
            '<strong>' . esc_html__('Philosofeet Core', 'philosofeet-core') . '</strong>',
            '<strong>' . esc_html__('PHP', 'philosofeet-core') . '</strong>',
            self::MINIMUM_PHP_VERSION
        );

        printf('<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message);
    }
}
