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
        }

        // Include additional widget files here as you create them
        // Example:
        // require_once PHILOSOFEET_CORE_PATH . 'includes/widgets/example-widget.php';

        if (file_exists($circular_widget)) {
            require_once $circular_widget;
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
            $widget = new Widgets\Circular_Wheel_Widget();
            $widgets_manager->register($widget);
        }
    }
}
