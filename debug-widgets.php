<?php
/**
 * Debug Widget Registration
 *
 * Add this to your wp-config.php to enable debugging:
 * define('WP_DEBUG', true);
 * define('WP_DEBUG_LOG', true);
 *
 * Then check wp-content/debug.log for output
 */

add_action('elementor/widgets/register', function($widgets_manager) {
    error_log('=== Philosofeet Widget Registration Debug ===');
    error_log('Widgets Manager Class: ' . get_class($widgets_manager));

    // Check if files exist
    $base_widget_path = PHILOSOFEET_CORE_PATH . 'includes/widgets/base-widget.php';
    $circular_widget_path = PHILOSOFEET_CORE_PATH . 'includes/widgets/circular-wheel-widget.php';

    error_log('Base widget file exists: ' . (file_exists($base_widget_path) ? 'YES' : 'NO'));
    error_log('Base widget path: ' . $base_widget_path);
    error_log('Circular widget file exists: ' . (file_exists($circular_widget_path) ? 'YES' : 'NO'));
    error_log('Circular widget path: ' . $circular_widget_path);

    // Check if base widget class exists
    if (class_exists('Philosofeet\Widgets\Base_Widget')) {
        error_log('✓ Base_Widget class exists');
    } else {
        error_log('✗ Base_Widget class does not exist');
    }

    // Check if our widget class exists
    if (class_exists('Philosofeet\Widgets\Circular_Wheel_Widget')) {
        error_log('✓ Circular_Wheel_Widget class exists');

        try {
            $widget = new \Philosofeet\Widgets\Circular_Wheel_Widget();
            error_log('✓ Widget instantiated successfully');
            error_log('Widget Name: ' . $widget->get_name());
            error_log('Widget Title: ' . $widget->get_title());
            error_log('Widget Categories: ' . print_r($widget->get_categories(), true));
        } catch (Exception $e) {
            error_log('✗ Error instantiating widget: ' . $e->getMessage());
            error_log('Stack trace: ' . $e->getTraceAsString());
        }
    } else {
        error_log('✗ Circular_Wheel_Widget class does not exist');
        error_log('Declared classes in Philosofeet\Widgets namespace: ' . print_r(array_filter(get_declared_classes(), function($class) {
            return strpos($class, 'Philosofeet\Widgets') === 0;
        }), true));
    }

    // Check if category is registered
    $categories = $widgets_manager->get_widget_types();
    error_log('Total registered widget types: ' . count($categories));

}, 100);

add_action('elementor/elements/categories_registered', function($elements_manager) {
    error_log('=== Philosofeet Category Registration Debug ===');
    $categories = $elements_manager->get_categories();
    error_log('Registered categories: ' . print_r(array_keys($categories), true));
}, 100);
