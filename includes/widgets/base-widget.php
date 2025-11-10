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
        >
            <div class="philosofeet-loading" style="padding: 20px; background: #f0f0f0; border: 2px solid #ccc; text-align: center;">
                <p><strong>Loading ' . esc_html($this->get_title()) . '...</strong></p>
                <p><small>If this message persists, check the browser console for errors.</small></p>
            </div>
        </div>';
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
     *
     * This method is called by Elementor to render the widget in the editor preview.
     * We output the same markup as the frontend, allowing React to initialize properly.
     */
    protected function content_template() {
        // Render the same markup as frontend
        // Elementor will populate data-widget-settings via JavaScript
        ?>
        <div
            class="philosofeet-widget"
            data-widget-type="<?php echo esc_attr($this->get_name()); ?>"
            data-widget-id="{{ view.getIDInt() }}"
            data-widget-settings="{{ JSON.stringify(settings) }}"
            data-initialized="false"
        >
            <div class="philosofeet-loading" style="padding: 20px; background: #f0f0f0; border: 2px solid #ccc; text-align: center;">
                <p><strong>Loading <?php echo esc_html($this->get_title()); ?>...</strong></p>
                <p><small>If this message persists, check the browser console for errors.</small></p>
            </div>
        </div>
        <?php
    }
}
