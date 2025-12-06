<?php
/**
 * Circular Wheel Widget
 *
 * @package Philosofeet\Widgets
 */

namespace Philosofeet\Widgets;

use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Background;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Circular Wheel Widget Class
 *
 * Displays a circular wheel with groups, times, and images
 */
class Circular_Wheel_Widget extends Base_Widget {

    /**
     * Get widget name
     */
    public function get_name() {
        return 'circular-wheel';
    }

    /**
     * Get widget title
     */
    public function get_title() {
        return __('Circular Wheel', 'philosofeet-core');
    }

    /**
     * Get widget icon
     */
    public function get_icon() {
        return 'eicon-spinner';
    }

    /**
     * Register widget controls
     */
    protected function register_controls() {
        // Content Section - Groups
        $this->start_controls_section(
            'groups_section',
            [
                'label' => __('Groups', 'philosofeet-core'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $groups_repeater = new Repeater();

        $groups_repeater->add_control(
            'group_title',
            [
                'label' => __('Group Title', 'philosofeet-core'),
                'type' => Controls_Manager::TEXT,
                'default' => __('Season', 'philosofeet-core'),
                'placeholder' => __('Enter group title', 'philosofeet-core'),
            ]
        );

        $groups_repeater->add_control(
            'group_type',
            [
                'label' => __('Group Type', 'philosofeet-core'),
                'type' => Controls_Manager::TEXT,
                'default' => __('season', 'philosofeet-core'),
                'placeholder' => __('Enter group type', 'philosofeet-core'),
            ]
        );

        $groups_repeater->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'group_background',
                'label' => __('Group Background', 'philosofeet-core'),
                'types' => ['classic', 'gradient'],
                'exclude' => ['image', 'video', 'attachment'],
                'selector' => '{{WRAPPER}} .group-{{ID}}',
                'fields_options' => [
                    'background' => [
                        'default' => 'classic',
                    ],
                    'color' => [
                        'default' => '#8B4513',
                    ],
                ],
            ]
        );

        // Background opacity control
        $groups_repeater->add_control(
            'group_background_opacity',
            [
                'label' => __('Background Opacity', 'philosofeet-core'),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 1,
                        'step' => 0.01,
                    ],
                ],
                'default' => [
                    'size' => 1,
                ],
            ]
        );

        $groups_repeater->add_control(
            'group_image',
            [
                'label' => __('Group Image', 'philosofeet-core'),
                'type' => Controls_Manager::MEDIA,
                'default' => [
                    'url' => '',
                ],
            ]
        );

        $groups_repeater->add_control(
            'group_link',
            [
                'label' => __('Link', 'philosofeet-core'),
                'type' => Controls_Manager::URL,
                'placeholder' => __('https://your-link.com', 'philosofeet-core'),
                'default' => [
                    'url' => '',
                    'is_external' => false,
                    'nofollow' => false,
                ],
            ]
        );

        // Times nested repeater
        $groups_repeater->add_control(
            'times',
            [
                'label' => __('Times', 'philosofeet-core'),
                'type' => Controls_Manager::TEXTAREA,
                'default' => "Morning\nAfternoon\nEvening\nNight",
                'placeholder' => __('Enter times (one per line)', 'philosofeet-core'),
                'description' => __('Enter each time on a new line', 'philosofeet-core'),
            ]
        );

        $this->add_control(
            'groups',
            [
                'label' => __('Groups', 'philosofeet-core'),
                'type' => Controls_Manager::REPEATER,
                'fields' => $groups_repeater->get_controls(),
                'default' => [
                    [
                        'group_title' => 'Spring',
                        'group_type' => 'Sandals',
                        'group_background_background' => 'classic',
                        'group_background_color' => '#2d5016',
                        'times' => "Morning\nNoon\nAfternoon",
                        'group_image' => [
                            'url' => '',
                        ],
                    ],
                    [
                        'group_title' => 'Spring',
                        'group_type' => 'Sneaker',
                        'group_background_background' => 'classic',
                        'group_background_color' => '#3d6b1f',
                        'times' => "Evening\nNight",
                        'group_image' => [
                            'url' => '',
                        ],
                    ],
                    [
                        'group_title' => 'Summer',
                        'group_type' => 'Flats',
                        'group_background_background' => 'classic',
                        'group_background_color' => '#8B4513',
                        'times' => "Morning\nAfternoon\nEvening",
                        'group_image' => [
                            'url' => '',
                        ],
                    ],
                    [
                        'group_title' => 'Summer',
                        'group_type' => 'Boots',
                        'group_background_background' => 'classic',
                        'group_background_color' => '#a05a1f',
                        'times' => "Morning\nNoon",
                        'group_image' => [
                            'url' => '',
                        ],
                    ],
                    [
                        'group_title' => 'Fall',
                        'group_type' => 'Heels',
                        'group_background_background' => 'classic',
                        'group_background_color' => '#5c1a1a',
                        'times' => "Afternoon\nEvening",
                        'group_image' => [
                            'url' => '',
                        ],
                    ],
                    [
                        'group_title' => 'Fall',
                        'group_type' => 'Boots',
                        'group_background_background' => 'classic',
                        'group_background_color' => '#7a2323',
                        'times' => "Morning\nAfternoon\nEvening",
                        'group_image' => [
                            'url' => '',
                        ],
                    ],
                    [
                        'group_title' => 'Winter',
                        'group_type' => 'Slippers',
                        'group_background_background' => 'classic',
                        'group_background_color' => '#3d2817',
                        'times' => "Night\nMorning",
                        'group_image' => [
                            'url' => '',
                        ],
                    ],
                    [
                        'group_title' => 'Winter',
                        'group_type' => 'Boots',
                        'group_background_background' => 'classic',
                        'group_background_color' => '#5a3d25',
                        'times' => "Morning\nAfternoon\nEvening\nNight",
                        'group_image' => [
                            'url' => '',
                        ],
                    ],
                ],
                'title_field' => '{{{ group_title }}}',
            ]
        );

        $this->end_controls_section();

        // Content Section - Center Content
        $this->start_controls_section(
            'center_section',
            [
                'label' => __('Center Content', 'philosofeet-core'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'center_icon',
            [
                'label' => __('Center Icon/Image', 'philosofeet-core'),
                'type' => Controls_Manager::MEDIA,
                'default' => [
                    'url' => '',
                ],
            ]
        );

        $this->add_control(
            'center_text',
            [
                'label' => __('Center Text', 'philosofeet-core'),
                'type' => Controls_Manager::TEXT,
                'default' => '',
                'placeholder' => __('Optional center text', 'philosofeet-core'),
            ]
        );

        $this->end_controls_section();

        // Style Section - Wheel
        $this->start_controls_section(
            'wheel_style_section',
            [
                'label' => __('Wheel Style', 'philosofeet-core'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'wheel_size',
            [
                'label' => __('Wheel Size', 'philosofeet-core'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px', 'vh'],
                'range' => [
                    'px' => [
                        'min' => 200,
                        'max' => 1000,
                        'step' => 10,
                    ],
                    'vh' => [
                        'min' => 20,
                        'max' => 100,
                        'step' => 1,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 720,
                ],
            ]
        );

        $this->add_control(
            'ring_width',
            [
                'label' => __('Ring Width', 'philosofeet-core'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['%'],
                'range' => [
                    '%' => [
                        'min' => 10,
                        'max' => 50,
                        'step' => 1,
                    ],
                ],
                'default' => [
                    'unit' => '%',
                    'size' => 25,
                ],
            ]
        );

        $this->add_control(
            'inner_ring_width',
            [
                'label' => __('Inner Ring Width', 'philosofeet-core'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['%'],
                'range' => [
                    '%' => [
                        'min' => 10,
                        'max' => 50,
                        'step' => 1,
                    ],
                ],
                'default' => [
                    'unit' => '%',
                    'size' => 20,
                ],
            ]
        );

        $this->add_control(
            'gap_size',
            [
                'label' => __('Gap Between Segments', 'philosofeet-core'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 20,
                        'step' => 1,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 2,
                ],
            ]
        );

        $this->add_control(
            'center_circle_size',
            [
                'label' => __('Center Circle Size', 'philosofeet-core'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['%'],
                'range' => [
                    '%' => [
                        'min' => 10,
                        'max' => 50,
                        'step' => 1,
                    ],
                ],
                'default' => [
                    'unit' => '%',
                    'size' => 30,
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'center_circle_background',
                'label' => __('Center Circle Background', 'philosofeet-core'),
                'types' => ['classic', 'gradient'],
                'selector' => '{{WRAPPER}} .wheel-center',
            ]
        );

        $this->end_controls_section();

        // Style Section - Typography
        $this->start_controls_section(
            'typography_style_section',
            [
                'label' => __('Typography', 'philosofeet-core'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'group_title_typography',
                'label' => __('Group Title Typography', 'philosofeet-core'),
                'selector' => '{{WRAPPER}} .wheel-segment-text',
            ]
        );

        $this->add_control(
            'group_title_color',
            [
                'label' => __('Group Title Color', 'philosofeet-core'),
                'type' => Controls_Manager::COLOR,
                'default' => '#ffffff',
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'time_typography',
                'label' => __('Time Typography', 'philosofeet-core'),
                'selector' => '{{WRAPPER}} .wheel-inner-segment-text',
            ]
        );

        $this->add_control(
            'time_color',
            [
                'label' => __('Time Color', 'philosofeet-core'),
                'type' => Controls_Manager::COLOR,
                'default' => '#ffffff',
            ]
        );

        $this->end_controls_section();

        // Style Section - Images
        $this->start_controls_section(
            'images_style_section',
            [
                'label' => __('Images', 'philosofeet-core'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'group_image_size',
            [
                'label' => __('Group Image Size', 'philosofeet-core'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 30,
                        'max' => 200,
                        'step' => 5,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 60,
                ],
            ]
        );

        $this->add_responsive_control(
            'center_icon_size',
            [
                'label' => __('Center Icon Size', 'philosofeet-core'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 30,
                        'max' => 200,
                        'step' => 5,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 80,
                ],
            ]
        );

        $this->end_controls_section();
    }

    /**
     * Prepare widget data for React
     */
    protected function prepare_widget_data($settings) {
        // Process groups data
        $groups = [];
        if (!empty($settings['groups'])) {
            foreach ($settings['groups'] as $group) {
                // Convert times textarea to array
                $times = !empty($group['times'])
                    ? array_filter(array_map('trim', explode("\n", $group['times'])))
                    : [];

                // Process background (solid color or gradient)
                $background = [
                    'type' => !empty($group['group_background_background']) ? $group['group_background_background'] : 'classic',
                ];

                if ($background['type'] === 'gradient') {
                    $background['gradient_type'] = !empty($group['group_background_gradient_type']) ? $group['group_background_gradient_type'] : 'linear';
                    $background['gradient_angle'] = !empty($group['group_background_gradient_angle']['size']) ? $group['group_background_gradient_angle']['size'] : 180;
                    $background['gradient_position'] = !empty($group['group_background_gradient_position']) ? $group['group_background_gradient_position'] : 'center center';
                    $background['color'] = !empty($group['group_background_color']) ? $group['group_background_color'] : '#8B4513';
                    $background['color_b'] = !empty($group['group_background_color_b']) ? $group['group_background_color_b'] : '#000000';
                    $background['color_stop'] = !empty($group['group_background_color_stop']['size']) ? $group['group_background_color_stop']['size'] : 0;
                    $background['color_b_stop'] = !empty($group['group_background_color_b_stop']['size']) ? $group['group_background_color_b_stop']['size'] : 100;

                    // Third color stop (optional)
                    if (!empty($group['group_background_color_c'])) {
                        $background['color_c'] = $group['group_background_color_c'];
                        $background['color_c_stop'] = !empty($group['group_background_color_c_stop']['size']) ? $group['group_background_color_c_stop']['size'] : 50;
                    }
                } else {
                    $background['color'] = !empty($group['group_background_color']) ? $group['group_background_color'] : '#8B4513';
                }

                // Background opacity
                $background['opacity'] = !empty($group['group_background_opacity']['size']) ? $group['group_background_opacity']['size'] : 1;

                // Process link data
                $link = [
                    'url' => !empty($group['group_link']['url']) ? $group['group_link']['url'] : '',
                    'is_external' => !empty($group['group_link']['is_external']),
                    'nofollow' => !empty($group['group_link']['nofollow']),
                ];

                $groups[] = [
                    'title' => $group['group_title'],
                    'type' => $group['group_type'],
                    'background' => $background,
                    'image' => !empty($group['group_image']['url']) ? $group['group_image']['url'] : '',
                    'link' => $link,
                    'times' => $times,
                ];
            }
        }

        return [
            'groups' => $groups,
            'centerIcon' => !empty($settings['center_icon']['url']) ? $settings['center_icon']['url'] : '',
            'centerText' => !empty($settings['center_text']) ? $settings['center_text'] : '',
            'wheelSize' => $settings['wheel_size'],
            'ringWidth' => $settings['ring_width'],
            'innerRingWidth' => $settings['inner_ring_width'],
            'gapSize' => $settings['gap_size'],
            'centerCircleSize' => $settings['center_circle_size'],
            'groupTitleColor' => $settings['group_title_color'],
            'timeColor' => $settings['time_color'],
            'groupImageSize' => $settings['group_image_size'],
            'centerIconSize' => $settings['center_icon_size'],
        ];
    }
}
