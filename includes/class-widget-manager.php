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
        $rss_feed_widget = PHILOSOFEET_CORE_PATH . 'includes/widgets/rss-feed-carousel-widget.php';
        $hamburger_menu_widget = PHILOSOFEET_CORE_PATH . 'includes/widgets/hamburger-menu-widget.php';

        if (file_exists($base_widget)) {
            require_once $base_widget;
        }

        if (file_exists($circular_widget)) {
            require_once $circular_widget;
        }

        if (file_exists($rss_feed_widget)) {
            require_once $rss_feed_widget;
        }

        if (file_exists($hamburger_menu_widget)) {
            require_once $hamburger_menu_widget;
        }
    }

    /**
     * Register widget instances
     */
    private function register_widget_instances($widgets_manager) {
        if (class_exists('Philosofeet\Widgets\Circular_Wheel_Widget')) {
            $widget = new Widgets\Circular_Wheel_Widget();
            $widgets_manager->register($widget);
        }

        if (class_exists('Philosofeet\Widgets\RSS_Feed_Carousel_Widget')) {
            $widget = new Widgets\RSS_Feed_Carousel_Widget();
            $widgets_manager->register($widget);
        }

        if (class_exists('Philosofeet\Widgets\Hamburger_Menu_Widget')) {
            $widget = new Widgets\Hamburger_Menu_Widget();
            $widgets_manager->register($widget);
        }
    }
}
