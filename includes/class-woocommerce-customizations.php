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
     * Render rectangular text swatches for each product variation.
     */
    public function add_variation_swatches() {
        global $product;

        if (!$product || !$product->is_type('variable')) {
            return;
        }

        $variations = $product->get_available_variations();
        if (empty($variations)) {
            return;
        }

        $parent_image_id = $product->get_image_id();
        $swatches        = [];

        foreach ($variations as $variation) {
            $variation_id  = $variation['variation_id'];
            $variation_obj = wc_get_product($variation_id);
            if (!$variation_obj) {
                continue;
            }

            $attr_values = array_filter(array_values($variation['attributes']));
            $label       = !empty($attr_values) ? implode(' / ', $attr_values) : $variation_obj->get_name();

            $image_id  = $variation_obj->get_image_id() ?: $parent_image_id;
            $image_url = $image_id ? wp_get_attachment_image_url($image_id, 'thumbnail') : '';

            $swatches[] = [
                'id'         => $variation_id,
                'label'      => $label,
                'image_url'  => $image_url,
                'attributes' => wp_json_encode($variation['attributes']),
            ];
        }

        if (empty($swatches)) {
            return;
        }

        // Get human-readable attribute name (e.g. "Flavours")
        $first_key  = array_key_first($variations[0]['attributes']);
        $attr_slug  = str_replace('attribute_', '', $first_key);
        $attr_label = wc_attribute_label($attr_slug);

        ?>
        <div class="philosofeet-variation-swatches">
            <p class="philosofeet-swatch-header">
                <?php echo esc_html($attr_label); ?>: <span class="philosofeet-swatch-selected">—</span>
            </p>
            <div class="philosofeet-swatch-options">
                <?php foreach ($swatches as $swatch) : ?>
                    <button
                        type="button"
                        class="philosofeet-swatch"
                        data-variation-id="<?php echo esc_attr($swatch['id']); ?>"
                        data-attributes="<?php echo esc_attr($swatch['attributes']); ?>"
                        data-label="<?php echo esc_attr($swatch['label']); ?>"
                    >
                        <?php if ($swatch['image_url']) : ?>
                            <img src="<?php echo esc_url($swatch['image_url']); ?>" alt="<?php echo esc_attr($swatch['label']); ?>" loading="lazy" />
                        <?php endif; ?>
                        <span><?php echo esc_html($swatch['label']); ?></span>
                    </button>
                <?php endforeach; ?>
            </div>
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
