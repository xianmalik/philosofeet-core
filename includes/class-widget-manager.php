<?php
/**
 * Widget Manager
 *
 * @package Philosofeet
 */

namespace Philosofeet;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Widget Manager Class
 */
class Widget_Manager {

    /**
     * Register widgets
     */
    public function register_widgets($widgets_manager) {
        $this->include_widget_files();
        $this->register_widget_instances($widgets_manager);
    }

    /**
     * Include widget files
     */
    private function include_widget_files() {
        $base_widget = PHILOSOFEET_CORE_PATH . 'includes/widgets/base-widget.php';
        $circular_widget = PHILOSOFEET_CORE_PATH . 'includes/widgets/circular-wheel-widget.php';

        if (file_exists($base_widget)) {
            require_once $base_widget;
            if (defined('WP_DEBUG') && WP_DEBUG) {
                error_log('Philosofeet: Base widget loaded from ' . $base_widget);
            }
        } else {
            error_log('Philosofeet ERROR: Base widget file not found at ' . $base_widget);
        }

        // Include additional widget files here as you create them
        // Example:
        // require_once PHILOSOFEET_CORE_PATH . 'includes/widgets/example-widget.php';

        if (file_exists($circular_widget)) {
            require_once $circular_widget;
            if (defined('WP_DEBUG') && WP_DEBUG) {
                error_log('Philosofeet: Circular wheel widget loaded from ' . $circular_widget);
            }
        } else {
            error_log('Philosofeet ERROR: Circular wheel widget file not found at ' . $circular_widget);
        }
    }

    /**
     * Register widget instances
     */
    private function register_widget_instances($widgets_manager) {
        // Register your widgets here as you create them
        // Example:
        // $widgets_manager->register(new Widgets\Example_Widget());

        if (class_exists('Philosofeet\Widgets\Circular_Wheel_Widget')) {
            try {
                $widget = new Widgets\Circular_Wheel_Widget();
                $widgets_manager->register($widget);
                if (defined('WP_DEBUG') && WP_DEBUG) {
                    error_log('Philosofeet: Circular Wheel Widget registered successfully');
                }
            } catch (\Exception $e) {
                error_log('Philosofeet ERROR: Failed to register Circular Wheel Widget: ' . $e->getMessage());
                error_log('Stack trace: ' . $e->getTraceAsString());
            }
        } else {
            error_log('Philosofeet ERROR: Circular_Wheel_Widget class does not exist when trying to register');
        }
    }
}
