<?php
/**
 * Test Widget Loading
 *
 * This file tests if the widget files can be loaded without fatal errors
 */

// Simulate WordPress environment minimally
define('ABSPATH', 'C:/wamp64/www/');
define('PHILOSOFEET_CORE_PATH', __DIR__ . '/');

// Try to load the files
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "Testing widget file loading...\n\n";

// Test base widget
$base_widget_file = PHILOSOFEET_CORE_PATH . 'includes/widgets/base-widget.php';
echo "1. Testing base-widget.php\n";
echo "   File exists: " . (file_exists($base_widget_file) ? 'YES' : 'NO') . "\n";

if (file_exists($base_widget_file)) {
    try {
        include_once $base_widget_file;
        echo "   ✓ File loaded successfully\n";
    } catch (Exception $e) {
        echo "   ✗ Error: " . $e->getMessage() . "\n";
    }
} else {
    echo "   ✗ File not found\n";
}

echo "\n";

// Test circular wheel widget
$circular_widget_file = PHILOSOFEET_CORE_PATH . 'includes/widgets/circular-wheel-widget.php';
echo "2. Testing circular-wheel-widget.php\n";
echo "   File exists: " . (file_exists($circular_widget_file) ? 'YES' : 'NO') . "\n";

if (file_exists($circular_widget_file)) {
    try {
        include_once $circular_widget_file;
        echo "   ✓ File loaded successfully\n";
    } catch (Exception $e) {
        echo "   ✗ Error: " . $e->getMessage() . "\n";
    }
} else {
    echo "   ✗ File not found\n";
}

echo "\n";
echo "Test complete!\n";
