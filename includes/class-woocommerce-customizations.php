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

        // Variation swatches: render after add-to-cart (priority 30)
        add_action('woocommerce_single_product_summary', [$this, 'add_variation_swatches'], 31);

        // Move price below the swatches
        remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_price', 10);
        add_action('woocommerce_single_product_summary', 'woocommerce_template_single_price', 32);

        // Enqueue swatch JS on single product pages
        add_action('wp_enqueue_scripts', [$this, 'enqueue_swatch_scripts']);
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
     * Render rectangular swatches grouped by attribute (one row per attribute).
     */
    public function add_variation_swatches() {
        global $product;

        if (!$product || !$product->is_type('variable')) {
            return;
        }

        // All possible values per attribute: ['flavors' => ['Blueberry Nibs', ...], 'size' => [...]]
        $product_attributes = $product->get_variation_attributes();
        if (empty($product_attributes)) {
            return;
        }

        // Build image map: attr_key → value → thumbnail URL
        // Uses the same get_available_variations() + get_image_id() pattern that is known to work.
        $parent_image_id = $product->get_image_id();
        $attr_image_map  = [];

        foreach ($product->get_available_variations() as $variation) {
            $variation_obj = wc_get_product($variation['variation_id']);
            if (!$variation_obj) {
                continue;
            }

            $image_id  = $variation_obj->get_image_id() ?: $parent_image_id;
            $image_url = $image_id ? wp_get_attachment_image_url($image_id, 'thumbnail') : '';

            if (!$image_url) {
                continue;
            }

            foreach ($variation['attributes'] as $attr_key => $attr_val) {
                if ($attr_val !== '' && !isset($attr_image_map[$attr_key][$attr_val])) {
                    $attr_image_map[$attr_key][$attr_val] = $image_url;
                }
            }
        }

        ?>
        <div class="philosofeet-variation-swatches">
        <?php foreach ($product_attributes as $attr_name => $values) :
            $attr_key   = 'attribute_' . sanitize_title($attr_name);
            $attr_label = wc_attribute_label($attr_name);
            ?>
            <div class="philosofeet-attr-row" data-attr-key="<?php echo esc_attr($attr_key); ?>">
                <p class="philosofeet-swatch-header">
                    <?php echo esc_html($attr_label); ?>: <span class="philosofeet-swatch-selected">—</span>
                </p>
                <div class="philosofeet-swatch-options">
                    <?php foreach ($values as $value) :
                        if ($value === '') continue; // skip the "Any" placeholder
                        $image_url = $attr_image_map[$attr_key][$value] ?? '';

                        // For taxonomy attributes get the term display name
                        $display = $value;
                        if (taxonomy_exists('pa_' . $attr_name)) {
                            $term = get_term_by('slug', $value, 'pa_' . $attr_name);
                            if ($term) {
                                $display = $term->name;
                            }
                        }
                    ?>
                        <button
                            type="button"
                            class="philosofeet-swatch"
                            data-attr-key="<?php echo esc_attr($attr_key); ?>"
                            data-attr-value="<?php echo esc_attr($value); ?>"
                            data-label="<?php echo esc_attr($display); ?>"
                        >
                            <?php if ($image_url) : ?>
                                <img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($display); ?>" loading="lazy" />
                            <?php endif; ?>
                            <span><?php echo esc_html($display); ?></span>
                        </button>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>
        </div>
        <?php
    }

    /**
     * Enqueue swatch JS on single product pages.
     */
    public function enqueue_swatch_scripts() {
        if (!is_product()) {
            return;
        }

        wp_enqueue_script(
            'philosofeet-swatches',
            PHILOSOFEET_CORE_ASSETS_URL . 'js/swatches.js',
            ['jquery', 'wc-add-to-cart-variation'],
            PHILOSOFEET_CORE_VERSION,
            true
        );
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
