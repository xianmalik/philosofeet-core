<?php
/**
 * Polling Widget
 *
 * @package Philosofeet\Widgets
 */

namespace Philosofeet\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Polling Widget Class
 */
class Polling_Widget extends Base_Widget {

    /**
     * Get widget name
     */
    public function get_name() {
        return 'polling';
    }

    /**
     * Get widget title
     */
    public function get_title() {
        return __('Polling', 'philosofeet-core');
    }

    /**
     * Get widget icon
     */
    public function get_icon() {
        return 'eicon-radio';
    }

    /**
     * Get poll options
     */
    protected function get_poll_options() {
        $polls = get_posts([
            'post_type' => 'philosofeet_poll',
            'posts_per_page' => -1,
            'post_status' => 'publish'
        ]);
        $options = ['' => __('Select Poll', 'philosofeet-core')];
        if (!empty($polls)) {
            foreach ($polls as $poll) {
                $options[$poll->ID] = $poll->post_title;
            }
        }
        return $options;
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
            'poll_id',
            [
                'label' => __('Select Poll', 'philosofeet-core'),
                'type' => Controls_Manager::SELECT,
                'options' => $this->get_poll_options(),
                'default' => '',
            ]
        );

        $this->end_controls_section();

        // Style Section - Container
        $this->start_controls_section(
            'container_style_section',
            [
                'label' => __('Container', 'philosofeet-core'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'container_padding',
            [
                'label' => __('Padding', 'philosofeet-core'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .philosofeet-poll-container' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        
        $this->add_control(
            'container_background',
            [
                'label' => __('Background Color', 'philosofeet-core'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .philosofeet-poll-container' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'container_border',
                'label' => __('Border', 'philosofeet-core'),
                'selector' => '{{WRAPPER}} .philosofeet-poll-container',
            ]
        );

        $this->end_controls_section();

        // Style Section - Title
        $this->start_controls_section(
            'title_style_section',
            [
                'label' => __('Title', 'philosofeet-core'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'title_typography',
                'label' => __('Typography', 'philosofeet-core'),
                'selector' => '{{WRAPPER}} .philosofeet-poll-title',
            ]
        );

        $this->add_control(
            'title_color',
            [
                'label' => __('Color', 'philosofeet-core'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .philosofeet-poll-title' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'title_margin',
            [
                'label' => __('Margin', 'philosofeet-core'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .philosofeet-poll-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        // Style Section - Buttons
        $this->start_controls_section(
            'buttons_style_section',
            [
                'label' => __('Buttons', 'philosofeet-core'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'button_gap',
            [
                'label' => __('Gap', 'philosofeet-core'),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .philosofeet-poll-options' => 'gap: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'button_layout',
            [
                'label' => __('Layout', 'philosofeet-core'),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'column' => [
                        'title' => __('Vertical', 'philosofeet-core'),
                        'icon' => 'eicon-arrow-down',
                    ],
                    'row' => [
                        'title' => __('Horizontal', 'philosofeet-core'),
                        'icon' => 'eicon-arrow-right',
                    ],
                ],
                'default' => 'column',
                'toggle' => false,
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'button_typography',
                'label' => __('Typography', 'philosofeet-core'),
                'selector' => '{{WRAPPER}} .philosofeet-poll-button',
            ]
        );

        $this->add_responsive_control(
            'button_padding',
            [
                'label' => __('Padding', 'philosofeet-core'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .philosofeet-poll-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'button_color',
            [
                'label' => __('Text Color', 'philosofeet-core'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .philosofeet-poll-button' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'button_background',
            [
                'label' => __('Background Color', 'philosofeet-core'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .philosofeet-poll-button' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'button_hover_color',
            [
                'label' => __('Hover Text Color', 'philosofeet-core'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .philosofeet-poll-button:hover' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'button_hover_background',
            [
                'label' => __('Hover Background Color', 'philosofeet-core'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .philosofeet-poll-button:hover' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'button_border',
                'label' => __('Border', 'philosofeet-core'),
                'selector' => '{{WRAPPER}} .philosofeet-poll-button',
            ]
        );

        $this->add_responsive_control(
            'button_border_radius',
            [
                'label' => __('Border Radius', 'philosofeet-core'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .philosofeet-poll-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
        
        $widget_data = [
            'pollId' => $settings['poll_id'],
            'layout' => $settings['button_layout'],
            // Styles are handled by Elementor selectors, but we pass layout for structural logic if needed
        ];
        
        ?>
        <div 
            class="philosofeet-widget" 
            data-widget-type="polling" 
            data-widget-id="<?php echo esc_attr($this->get_id()); ?>"
            data-widget-settings="<?php echo esc_attr(json_encode($widget_data)); ?>"
            data-initialized="false"
        ></div>
        <?php
    }

    /**
     * Render widget output in the editor
     */
    protected function content_template() {
        ?>
        <#
        var widgetData = {
            pollId: settings.poll_id,
            layout: settings.button_layout
        };
        #>
        <div 
            class="philosofeet-widget" 
            data-widget-type="polling" 
            data-widget-id="{{ view.getIDInt() }}"
            data-widget-settings="{{ JSON.stringify(widgetData) }}"
            data-initialized="false"
        ></div>
        <?php
    }
}
