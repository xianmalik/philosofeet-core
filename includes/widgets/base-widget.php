<?php
/**
 * Base Widget Class
 *
 * @package Philosofeet\Widgets
 */

namespace Philosofeet\Widgets;

use Elementor\Widget_Base;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Base Widget
 *
 * Abstract base class for all Philosofeet widgets
 */
abstract class Base_Widget extends Widget_Base {

    /**
     * Get widget categories
     */
    public function get_categories() {
        return ['philosofeet'];
    }

    /**
     * Render widget output on the frontend
     */
    protected function render() {
        $settings = $this->get_settings_for_display();
        $widget_data = $this->prepare_widget_data($settings);

        // Output container with React mount point
        echo '<div
            class="philosofeet-widget"
            data-widget-type="' . esc_attr($this->get_name()) . '"
            data-widget-id="' . esc_attr($this->get_id()) . '"
            data-widget-settings="' . esc_attr(wp_json_encode($widget_data)) . '"
        ></div>';
    }

    /**
     * Prepare widget data for React
     *
     * Override this method in child classes to customize data preparation
     */
    protected function prepare_widget_data($settings) {
        return $settings;
    }

    /**
     * Render widget output in the editor
     */
    protected function content_template() {
        ?>
        <#
        var widgetData = settings;
        #>
        <div
            class="philosofeet-widget"
            data-widget-type="<?php echo esc_attr($this->get_name()); ?>"
            data-widget-settings="{{ JSON.stringify(widgetData) }}"
        >
            <div class="philosofeet-widget-placeholder">
                <p><?php echo esc_html($this->get_title()); ?> - Preview will render on frontend</p>
            </div>
        </div>
        <?php
    }
}
