<?php
/**
 * Image Hover Swap Widget
 *
 * @package Philosofeet\Widgets
 */

namespace Philosofeet\Widgets;

use Elementor\Controls_Manager;
use Elementor\Repeater;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Image Hover Swap Widget Class
 *
 * Displays a stack of images where hovering an underlying image brings it to the top.
 */
class Image_Hover_Swap_Widget extends Base_Widget {

    /**
     * Get widget name
     */
    public function get_name() {
        return 'image-hover-swap';
    }

    /**
     * Get widget title
     */
    public function get_title() {
        return __('Image Hover Swap', 'philosofeet-core');
    }

    /**
     * Get widget icon
     */
    public function get_icon() {
        return 'eicon-image-rollover';
    }

    /**
     * Register widget controls
     */
    protected function register_controls() {
        $this->start_controls_section(
            'content_section',
            [
                'label' => __('Content', 'philosofeet-core'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $repeater = new Repeater();

        $repeater->add_control(
            'stack_image',
            [
                'label' => __('Image', 'philosofeet-core'),
                'type' => Controls_Manager::MEDIA,
                'default' => [
                    'url' => '',
                ],
            ]
        );

        $repeater->add_control(
            'x_offset',
            [
                'label' => __('X Offset (px)', 'philosofeet-core'),
                'type' => Controls_Manager::NUMBER,
                'default' => 0,
                'description' => __('Horizontal offset from the center/origin.', 'philosofeet-core'),
            ]
        );

        $repeater->add_control(
            'y_offset',
            [
                'label' => __('Y Offset (px)', 'philosofeet-core'),
                'type' => Controls_Manager::NUMBER,
                'default' => 0,
                'description' => __('Vertical offset from the center/origin.', 'philosofeet-core'),
            ]
        );
        
        $repeater->add_control(
            'rotation',
            [
                'label' => __('Rotation (deg)', 'philosofeet-core'),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'deg' => [
                        'min' => -360,
                        'max' => 360,
                    ],
                ],
                'default' => [
                    'size' => 0,
                    'unit' => 'deg',
                ],
            ]
        );
        
        $repeater->add_control(
            'custom_width',
             [
                'label' => __('Custom Width (px)', 'philosofeet-core'),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 50,
                        'max' => 500,
                    ],
                ],
                'default' => [
                    'size' => 200,
                    'unit' => 'px',
                ],
             ]
        );

        $this->add_control(
            'images',
            [
                'label' => __('Images', 'philosofeet-core'),
                'type' => Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
                'default' => [
                    [
                        'x_offset' => 0,
                        'y_offset' => 0,
                    ],
                    [
                        'x_offset' => 50,
                        'y_offset' => 50,
                    ],
                    [
                        'x_offset' => 100,
                        'y_offset' => 100,
                    ],
                ],
                'title_field' => 'Image',
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'style_section',
            [
                'label' => __('Settings', 'philosofeet-core'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'transition_duration',
            [
                'label' => __('Transition Duration (ms)', 'philosofeet-core'),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 100,
                        'max' => 2000,
                        'step' => 100,
                    ],
                ],
                'default' => [
                    'size' => 300,
                ],
            ]
        );

        $this->end_controls_section();
    }

    /**
     * Prepare widget data for React
     */
    protected function prepare_widget_data($settings) {
        $images = [];
        if (!empty($settings['images'])) {
            foreach ($settings['images'] as $item) {
                if (empty($item['stack_image']['url'])) {
                    continue;
                }
                
                $images[] = [
                    'id' => $item['_id'],
                    'url' => $item['stack_image']['url'],
                    'x' => isset($item['x_offset']) ? (float)$item['x_offset'] : 0,
                    'y' => isset($item['y_offset']) ? (float)$item['y_offset'] : 0,
                    'rotation' => isset($item['rotation']['size']) ? (float)$item['rotation']['size'] : 0,
                    'width' => isset($item['custom_width']['size']) ? (float)$item['custom_width']['size'] : 200,
                ];
            }
        }

        return [
            'images' => $images,
            'transitionDuration' => isset($settings['transition_duration']['size']) ? (int)$settings['transition_duration']['size'] : 300,
        ];
    }
}
