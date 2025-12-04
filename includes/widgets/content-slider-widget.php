<?php
/**
 * Content Slider Widget
 *
 * @package Philosofeet\Widgets
 */

namespace Philosofeet\Widgets;

use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Plugin;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Content Slider Widget Class
 *
 * A slider container where each slide can contain any Elementor content
 */
class Content_Slider_Widget extends Base_Widget {

    /**
     * Get widget name
     */
    public function get_name() {
        return 'content-slider';
    }

    /**
     * Get widget title
     */
    public function get_title() {
        return __('Content Slider', 'philosofeet-core');
    }

    /**
     * Get widget icon
     */
    public function get_icon() {
        return 'eicon-slides';
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

        $this->add_control(
            'slides_help',
            [
                'type' => Controls_Manager::RAW_HTML,
                'raw' => __('<strong>How to use:</strong><br>1. Create your slide designs as <a href="/wp-admin/edit.php?post_type=elementor_library&tabs_group=library&elementor_library_type=section" target="_blank">Elementor Section Templates</a><br>2. Use <strong>"Section"</strong> template type (NOT Page or Container)<br>3. Select section templates from the dropdown for each slide<br>4. Each slide can use a different template with completely custom layouts', 'philosofeet-core'),
                'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
            ]
        );

        $repeater = new Repeater();

        $repeater->add_control(
            'slide_title',
            [
                'label' => __('Slide Title (Admin Label)', 'philosofeet-core'),
                'type' => Controls_Manager::TEXT,
                'default' => __('Slide #1', 'philosofeet-core'),
                'label_block' => true,
            ]
        );

        $repeater->add_control(
            'slide_template',
            [
                'label' => __('Choose Template', 'philosofeet-core'),
                'type' => Controls_Manager::SELECT,
                'default' => '',
                'options' => $this->get_saved_templates(),
            ]
        );

        $this->add_control(
            'slides',
            [
                'label' => __('Slides', 'philosofeet-core'),
                'type' => Controls_Manager::REPEATER,
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
     * Get saved Elementor templates
     */
    private function get_saved_templates() {
        $templates = Plugin::instance()->templates_manager->get_source('local')->get_items();
        $options = ['' => __('Select Template', 'philosofeet-core')];

        foreach ($templates as $template) {
            // Only show Section templates
            if ($template['type'] === 'section') {
                $options[$template['template_id']] = $template['title'];
            }
        }

        return $options;
    }

    /**
     * Prepare widget data for React
     */
    protected function prepare_widget_data($settings) {
        $slides = [];

        foreach ($settings['slides'] as $index => $slide) {
            $slide_data = [
                'id' => $index,
                'title' => $slide['slide_title'] ?? '',
            ];

            // Render template content
            $content = '';
            if (!empty($slide['slide_template'])) {
                $template_id = $slide['slide_template'];

                // Check if template exists
                $template_document = Plugin::instance()->documents->get($template_id);

                if ($template_document) {
                    $template_content = Plugin::instance()->frontend->get_builder_content_for_display($template_id);

                    if (!empty($template_content)) {
                        $content = $template_content;
                    } else {
                        $content = '<div style="padding: 40px; text-align: center; color: #999;"><p>Template content is empty.</p><p>Template ID: ' . esc_html($template_id) . '</p></div>';
                    }
                } else {
                    $content = '<div style="padding: 40px; text-align: center; color: #999;"><p>Template not found.</p><p>Template ID: ' . esc_html($template_id) . '</p></div>';
                }
            } else {
                $content = '<div style="padding: 40px; text-align: center; color: #999;">No template selected. Please select a section template.</div>';
            }

            // Base64 encode the content to prevent JSON parsing issues with special characters
            $slide_data['content'] = base64_encode($content);
            $slide_data['encoded'] = true;

            $slides[] = $slide_data;
        }

        return [
            'slides' => $slides,
            'autoplay' => $settings['autoplay'] === 'yes',
            'autoplaySpeed' => (int) $settings['autoplay_speed'],
            'transitionSpeed' => (int) $settings['transition_speed'],
            'infiniteLoop' => $settings['infinite_loop'] === 'yes',
            'showNavigation' => $settings['show_navigation'] === 'yes',
            'showPagination' => $settings['show_pagination'] === 'yes',
            'slideEffect' => $settings['slide_effect'],
            'sliderHeight' => $settings['slider_height'],
            'sliderBackgroundColor' => $settings['slider_background_color'],
            'navArrowColor' => $settings['nav_arrow_color'] ?? '#333333',
            'navArrowBgColor' => $settings['nav_arrow_bg_color'] ?? '#ffffff',
            'navArrowSize' => $settings['nav_arrow_size'] ?? ['size' => 40, 'unit' => 'px'],
            'paginationDotColor' => $settings['pagination_dot_color'] ?? '#cccccc',
            'paginationDotActiveColor' => $settings['pagination_dot_active_color'] ?? '#333333',
            'paginationDotSize' => $settings['pagination_dot_size'] ?? ['size' => 10, 'unit' => 'px'],
        ];
    }
}
