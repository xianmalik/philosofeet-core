<?php
/**
 * Example Widget
 *
 * @package Philosofeet\Widgets
 */

namespace Philosofeet\Widgets;

use Elementor\Controls_Manager;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Example Widget Class
 *
 * This is a template widget to demonstrate the structure
 * Copy and modify this file when creating new widgets
 */
class Example_Widget extends Base_Widget {

    /**
     * Get widget name
     */
    public function get_name() {
        return 'example-widget';
    }

    /**
     * Get widget title
     */
    public function get_title() {
        return __('Example Widget', 'philosofeet-core');
    }

    /**
     * Get widget icon
     */
    public function get_icon() {
        return 'eicon-code';
    }

    /**
     * Register widget controls
     */
    protected function register_controls() {
        // Content Section
        $this->start_controls_section(
            'content_section',
            [
                'label' => __('Content', 'philosofeet-core'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'title',
            [
                'label' => __('Title', 'philosofeet-core'),
                'type' => Controls_Manager::TEXT,
                'default' => __('Example Title', 'philosofeet-core'),
                'placeholder' => __('Enter your title', 'philosofeet-core'),
            ]
        );

        $this->add_control(
            'description',
            [
                'label' => __('Description', 'philosofeet-core'),
                'type' => Controls_Manager::TEXTAREA,
                'default' => __('Example description text', 'philosofeet-core'),
                'placeholder' => __('Enter your description', 'philosofeet-core'),
            ]
        );

        $this->end_controls_section();

        // Style Section
        $this->start_controls_section(
            'style_section',
            [
                'label' => __('Style', 'philosofeet-core'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'text_color',
            [
                'label' => __('Text Color', 'philosofeet-core'),
                'type' => Controls_Manager::COLOR,
                'default' => '#333333',
            ]
        );

        $this->end_controls_section();
    }

    /**
     * Prepare widget data for React
     */
    protected function prepare_widget_data($settings) {
        return [
            'title' => $settings['title'],
            'description' => $settings['description'],
            'textColor' => $settings['text_color'],
        ];
    }
}
