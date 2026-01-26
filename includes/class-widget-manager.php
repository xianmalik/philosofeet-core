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
        $drawer_nav_menu_widget = PHILOSOFEET_CORE_PATH . 'includes/widgets/drawer-nav-menu-widget.php';
        $image_swap_widget = PHILOSOFEET_CORE_PATH . 'includes/widgets/image-swap-widget.php';
        $sticky_image_widget = PHILOSOFEET_CORE_PATH . 'includes/widgets/sticky-image-widget.php';
        $content_slider_widget = PHILOSOFEET_CORE_PATH . 'includes/widgets/content-slider-widget.php';
        $content_slider_widget = PHILOSOFEET_CORE_PATH . 'includes/widgets/content-slider-widget.php';
        $nested_slider_widget = PHILOSOFEET_CORE_PATH . 'includes/widgets/nested-slider-widget.php';
        $image_hover_swap_widget = PHILOSOFEET_CORE_PATH . 'includes/widgets/image-hover-swap-widget.php';

        if (file_exists($base_widget)) {
            require_once $base_widget;
        }

        if (file_exists($circular_widget)) {
            require_once $circular_widget;
        }

        if (file_exists($rss_feed_widget)) {
            require_once $rss_feed_widget;
        }

        if (file_exists($drawer_nav_menu_widget)) {
            require_once $drawer_nav_menu_widget;
        }

        if (file_exists($image_swap_widget)) {
            require_once $image_swap_widget;
        }

        if (file_exists($sticky_image_widget)) {
            require_once $sticky_image_widget;
        }

        if (file_exists($content_slider_widget)) {
            require_once $content_slider_widget;
        }

        if (file_exists($nested_slider_widget)) {
            require_once $nested_slider_widget;
        }

        if (file_exists($image_hover_swap_widget)) {
            require_once $image_hover_swap_widget;
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

        if (class_exists('Philosofeet\Widgets\Drawer_Nav_Menu_Widget')) {
            $widget = new Widgets\Drawer_Nav_Menu_Widget();
            $widgets_manager->register($widget);
        }

        if (class_exists('Philosofeet\Widgets\Image_Swap_Widget')) {
            $widget = new Widgets\Image_Swap_Widget();
            $widgets_manager->register($widget);
        }

        if (class_exists('Philosofeet\Widgets\Sticky_Image_Widget')) {
            $widget = new Widgets\Sticky_Image_Widget();
            $widgets_manager->register($widget);
        }

        if (class_exists('Philosofeet\Widgets\Content_Slider_Widget')) {
            $widget = new Widgets\Content_Slider_Widget();
            $widgets_manager->register($widget);
        }

        if (class_exists('Philosofeet\Widgets\Nested_Slider_Widget')) {
            $widget = new Widgets\Nested_Slider_Widget();
            $widgets_manager->register($widget);
        }

        if (class_exists('Philosofeet\Widgets\Image_Hover_Swap_Widget')) {
            $widget = new Widgets\Image_Hover_Swap_Widget();
            $widgets_manager->register($widget);
        }
    }
}
