<?php
/**
 * Plugin Name: Philosofeet Core
 * Plugin URI: https://paperhouse.agency
 * Description: Custom Elementor widgets with React-based frontend rendering
 * Version: 2.0.0
 * Author: PaperHouse
 * Author URI: https://paperhouse.agency
 * Text Domain: philosofeet-core
 * Domain Path: /languages
 * Requires at least: 5.8
 * Requires PHP: 7.4
 * Elementor tested up to: 3.20.0
 * Elementor Pro tested up to: 3.20.0
 */

namespace Philosofeet;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Plugin constants
define('PHILOSOFEET_CORE_VERSION', '2.0.0');
define('PHILOSOFEET_CORE_FILE', __FILE__);
define('PHILOSOFEET_CORE_PATH', plugin_dir_path(__FILE__));
define('PHILOSOFEET_CORE_URL', plugin_dir_url(__FILE__));
define('PHILOSOFEET_CORE_ASSETS_URL', PHILOSOFEET_CORE_URL . 'assets/');
define('PHILOSOFEET_CORE_ASSETS_PATH', PHILOSOFEET_CORE_PATH . 'assets/');

/**
 * Load the main plugin class
 */
require_once PHILOSOFEET_CORE_PATH . 'includes/class-philosofeet-core.php';

/**
 * Initialize the plugin
 */
function philosofeet_core_init() {
    \Philosofeet\PhilosofeetCORE::instance();
}
add_action('plugins_loaded', __NAMESPACE__ . '\philosofeet_core_init');
