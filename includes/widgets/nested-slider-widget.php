<?php
/**
 * Nested Slider Widget
 *
 * @package Philosofeet\Widgets
 */

namespace Philosofeet\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Plugin;
use Elementor\Repeater;

if (!defined('ABSPATH')) {
    exit;
}

// Only define the widget if Elementor's nested elements are available
if (!class_exists('\Elementor\Modules\NestedElements\Base\Widget_Nested_Base')) {
    return;
}

/**
 * Nested Slider Widget Class
 *
 * A slider where each slide is a container that can hold any Elementor elements
 */
class Nested_Slider_Widget extends \Elementor\Modules\NestedElements\Base\Widget_Nested_Base {

    /**
     * Get widget name
     */
    public function get_name() {
        return 'nested-slider';
    }

    /**
     * Get widget title
     */
    public function get_title() {
        return __('Nested Slider', 'philosofeet-core');
    }

    /**
     * Get widget icon
     */
    public function get_icon() {
        return 'eicon-carousel';
    }

    /**
     * Get widget categories
     */
    public function get_categories() {
        return ['philosofeet'];
    }

    /**
     * Show widget in panel only if nested elements are enabled
     */
    public function show_in_panel(): bool {
        // Check if Elementor's nested elements feature is available
        return Plugin::$instance->experiments->is_feature_active('nested-elements', true);
    }

    /**
     * Get widget keywords
     */
    public function get_keywords() {
        return ['slider', 'carousel', 'nested', 'container', 'slides'];
    }

    /**
     * Has widget inner wrapper
     */
    public function has_widget_inner_wrapper(): bool {
        return !Plugin::$instance->experiments->is_feature_active('e_optimized_markup');
    }

    /**
     * Create a slide container
     */
    protected function slide_container(int $index) {
        return [
            'elType' => 'container',
            'settings' => [
                '_title' => sprintf(
                    /* translators: %d: Slide index. */
                    __('Slide #%d', 'philosofeet-core'),
                    $index
                ),
                'content_width' => 'full',
            ],
        ];
    }

    /**
     * Get default children elements
     */
    protected function get_default_children_elements() {
        return [
            $this->slide_container(1),
            $this->slide_container(2),
            $this->slide_container(3),
        ];
    }

    /**
     * Get default repeater title setting key
     */
    protected function get_default_repeater_title_setting_key() {
        return 'slide_title';
    }

    /**
     * Get default children title
     */
    protected function get_default_children_title() {
        /* translators: %d: Slide index. */
        return esc_html__('Slide #%d', 'philosofeet-core');
    }

    /**
     * Get default children placeholder selector
     */
    protected function get_default_children_placeholder_selector() {
        return '.philosofeet-slider-slides';
    }

    /**
     * Register widget controls
     */
    protected function register_controls() {
        // Slides Section
        $this->start_controls_section(
            'slides_section',
            [
                'label' => __('Slides', 'philosofeet-core'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $repeater = new Repeater();

        $repeater->add_control(
            'slide_title',
            [
                'label' => __('Title (For Editor Only)', 'philosofeet-core'),
                'type' => Controls_Manager::TEXT,
                'default' => __('Slide Title', 'philosofeet-core'),
                'placeholder' => __('Slide Title', 'philosofeet-core'),
                'label_block' => true,
                'dynamic' => [
                    'active' => true,
                ],
            ]
        );

        $repeater->add_control(
            'slide_background_color',
            [
                'label' => __('Background Color', 'philosofeet-core'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} {{CURRENT_ITEM}}' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'slides',
            [
                'label' => __('Slide Items', 'philosofeet-core'),
                'type' => \Elementor\Modules\NestedElements\Controls\Control_Nested_Repeater::CONTROL_TYPE,
                'fields' => $repeater->get_controls(),
                'default' => [
                    [
                        'slide_title' => __('Slide #1', 'philosofeet-core'),
                    ],
                    [
                        'slide_title' => __('Slide #2', 'philosofeet-core'),
                    ],
                    [
                        'slide_title' => __('Slide #3', 'philosofeet-core'),
                    ],
                ],
                'title_field' => '{{{ slide_title }}}',
                'button_text' => __('Add Slide', 'philosofeet-core'),
            ]
        );

        $this->end_controls_section();

        // Slider Settings Section
        $this->start_controls_section(
            'slider_settings_section',
            [
                'label' => __('Slider Settings', 'philosofeet-core'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'autoplay',
            [
                'label' => __('Autoplay', 'philosofeet-core'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'philosofeet-core'),
                'label_off' => __('No', 'philosofeet-core'),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'autoplay_speed',
            [
                'label' => __('Autoplay Speed (ms)', 'philosofeet-core'),
                'type' => Controls_Manager::NUMBER,
                'default' => 5000,
                'min' => 1000,
                'max' => 10000,
                'step' => 100,
                'condition' => [
                    'autoplay' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'transition_speed',
            [
                'label' => __('Transition Speed (ms)', 'philosofeet-core'),
                'type' => Controls_Manager::NUMBER,
                'default' => 500,
                'min' => 100,
                'max' => 2000,
                'step' => 50,
            ]
        );

        $this->add_control(
            'infinite_loop',
            [
                'label' => __('Infinite Loop', 'philosofeet-core'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'philosofeet-core'),
                'label_off' => __('No', 'philosofeet-core'),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'show_navigation',
            [
                'label' => __('Show Navigation Arrows', 'philosofeet-core'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'philosofeet-core'),
                'label_off' => __('No', 'philosofeet-core'),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'show_pagination',
            [
                'label' => __('Show Pagination Dots', 'philosofeet-core'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'philosofeet-core'),
                'label_off' => __('No', 'philosofeet-core'),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'slide_effect',
            [
                'label' => __('Slide Effect', 'philosofeet-core'),
                'type' => Controls_Manager::SELECT,
                'default' => 'slide',
                'options' => [
                    'slide' => __('Slide', 'philosofeet-core'),
                    'fade' => __('Fade', 'philosofeet-core'),
                ],
            ]
        );

        $this->end_controls_section();

        // Style Section - Slider Container
        $this->start_controls_section(
            'slider_style_section',
            [
                'label' => __('Slider Container', 'philosofeet-core'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'slider_height',
            [
                'label' => __('Height', 'philosofeet-core'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px', 'vh', '%'],
                'range' => [
                    'px' => [
                        'min' => 200,
                        'max' => 1000,
                        'step' => 10,
                    ],
                    'vh' => [
                        'min' => 10,
                        'max' => 100,
                        'step' => 1,
                    ],
                    '%' => [
                        'min' => 10,
                        'max' => 100,
                        'step' => 1,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 500,
                ],
                'selectors' => [
                    '{{WRAPPER}} .philosofeet-nested-slider' => 'height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'slider_background_color',
            [
                'label' => __('Background Color', 'philosofeet-core'),
                'type' => Controls_Manager::COLOR,
                'default' => '#f5f5f5',
                'selectors' => [
                    '{{WRAPPER}} .philosofeet-nested-slider' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'slider_border',
                'selector' => '{{WRAPPER}} .philosofeet-nested-slider',
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'slider_box_shadow',
                'selector' => '{{WRAPPER}} .philosofeet-nested-slider',
            ]
        );

        $this->end_controls_section();

        // Style Section - Navigation
        $this->start_controls_section(
            'navigation_style_section',
            [
                'label' => __('Navigation', 'philosofeet-core'),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'show_navigation' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'nav_arrow_color',
            [
                'label' => __('Arrow Color', 'philosofeet-core'),
                'type' => Controls_Manager::COLOR,
                'default' => '#333333',
            ]
        );

        $this->add_control(
            'nav_arrow_bg_color',
            [
                'label' => __('Arrow Background Color', 'philosofeet-core'),
                'type' => Controls_Manager::COLOR,
                'default' => '#ffffff',
            ]
        );

        $this->add_responsive_control(
            'nav_arrow_size',
            [
                'label' => __('Arrow Size', 'philosofeet-core'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 20,
                        'max' => 100,
                        'step' => 1,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 40,
                ],
            ]
        );

        $this->end_controls_section();

        // Style Section - Pagination
        $this->start_controls_section(
            'pagination_style_section',
            [
                'label' => __('Pagination', 'philosofeet-core'),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'show_pagination' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'pagination_dot_color',
            [
                'label' => __('Dot Color', 'philosofeet-core'),
                'type' => Controls_Manager::COLOR,
                'default' => '#cccccc',
            ]
        );

        $this->add_control(
            'pagination_dot_active_color',
            [
                'label' => __('Active Dot Color', 'philosofeet-core'),
                'type' => Controls_Manager::COLOR,
                'default' => '#333333',
            ]
        );

        $this->add_responsive_control(
            'pagination_dot_size',
            [
                'label' => __('Dot Size', 'philosofeet-core'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 5,
                        'max' => 30,
                        'step' => 1,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 10,
                ],
            ]
        );

        $this->end_controls_section();
    }

    /**
     * Render widget output on the frontend
     */
    protected function render() {
        $settings = $this->get_settings_for_display();
        $children = $this->get_children();

        ?>
        <div class="philosofeet-nested-slider" data-slider-settings='<?php echo wp_json_encode([
            'autoplay' => $settings['autoplay'] === 'yes',
            'autoplaySpeed' => (int) $settings['autoplay_speed'],
            'transitionSpeed' => (int) $settings['transition_speed'],
            'infiniteLoop' => $settings['infinite_loop'] === 'yes',
            'showNavigation' => $settings['show_navigation'] === 'yes',
            'showPagination' => $settings['show_pagination'] === 'yes',
            'slideEffect' => $settings['slide_effect'],
            'navArrowColor' => $settings['nav_arrow_color'] ?? '#333333',
            'navArrowBgColor' => $settings['nav_arrow_bg_color'] ?? '#ffffff',
            'navArrowSize' => $settings['nav_arrow_size'] ?? ['size' => 40, 'unit' => 'px'],
            'paginationDotColor' => $settings['pagination_dot_color'] ?? '#cccccc',
            'paginationDotActiveColor' => $settings['pagination_dot_active_color'] ?? '#333333',
            'paginationDotSize' => $settings['pagination_dot_size'] ?? ['size' => 10, 'unit' => 'px'],
        ]); ?>'>
            <div class="philosofeet-slider-slides">
                <?php foreach ($children as $index => $child) : ?>
                    <div class="philosofeet-slider-slide elementor-repeater-item-<?php echo esc_attr($settings['slides'][$index]['_id'] ?? $index); ?>">
                        <?php $child->print_element(); ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php
    }

    /**
     * Render widget content template (for Elementor editor)
     */
    protected function content_template() {
        ?>
        <#
        const slidesData = settings.slides || [];
        const sliderSettings = {
            autoplay: settings.autoplay === 'yes',
            autoplaySpeed: parseInt(settings.autoplay_speed) || 5000,
            transitionSpeed: parseInt(settings.transition_speed) || 500,
            infiniteLoop: settings.infinite_loop === 'yes',
            showNavigation: settings.show_navigation === 'yes',
            showPagination: settings.show_pagination === 'yes',
            slideEffect: settings.slide_effect || 'slide',
            navArrowColor: settings.nav_arrow_color || '#333333',
            navArrowBgColor: settings.nav_arrow_bg_color || '#ffffff',
            navArrowSize: settings.nav_arrow_size || { size: 40, unit: 'px' },
            paginationDotColor: settings.pagination_dot_color || '#cccccc',
            paginationDotActiveColor: settings.pagination_dot_active_color || '#333333',
            paginationDotSize: settings.pagination_dot_size || { size: 10, unit: 'px' }
        };
        #>
        <div class="philosofeet-nested-slider" data-slider-settings="{{ JSON.stringify(sliderSettings) }}">
            <div class="philosofeet-slider-slides"></div>
        </div>
        <?php
    }
}
