<?php
/**
 * WooCommerce Customizations
 *
 * @package Philosofeet
 */

namespace Philosofeet;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * WooCommerce_Customizations Class
 */
class WooCommerce_Customizations {

    /**
     * Constructor
     */
    public function __construct() {
        // Only run if WooCommerce is active
        if (!class_exists('WooCommerce')) {
            return;
        }

        // Move description after price
        add_action('woocommerce_single_product_summary', [$this, 'add_description_after_price'], 25);

        // Remove all product tabs
        add_filter('woocommerce_product_tabs', [$this, 'remove_all_tabs'], 98);

        // Add standalone reviews section (priority 5 to appear before related products which is at 20)
        add_action('woocommerce_after_single_product_summary', [$this, 'add_standalone_reviews'], 5);
    }

    /**
     * Add product description after price
     */
    public function add_description_after_price() {
        global $post;

        $description = get_the_content();

        if (!empty($description)) {
            echo '<div class="philosofeet-product-description">';
            echo apply_filters('the_content', $description);
            echo '</div>';
        }
    }

    /**
     * Remove all product tabs
     *
     * @param array $tabs Product tabs.
     * @return array Empty array.
     */
    public function remove_all_tabs($tabs) {
        return [];
    }

    /**
     * Add standalone reviews section
     */
    public function add_standalone_reviews() {
        global $product;

        if (!comments_open()) {
            return;
        }

        echo '<div class="philosofeet-reviews-section">';
        comments_template();
        echo '</div>';
    }
}
