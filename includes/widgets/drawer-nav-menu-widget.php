<?php
/**
 * Drawer Nav Menu Widget
 *
 * @package Philosofeet\Widgets
 */

namespace Philosofeet\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Drawer Nav Menu Widget Class
 */
class Drawer_Nav_Menu_Widget extends Base_Widget {

    /**
     * Get widget name
     */
    public function get_name() {
        return 'drawer-nav-menu';
    }

    /**
     * Get widget title
     */
    public function get_title() {
        return __('Drawer Nav Menu', 'philosofeet-core');
    }

    /**
     * Get widget icon
     */
    public function get_icon() {
        return 'eicon-menu-bar';
    }

    /**
     * Register widget controls
     */
    protected function register_controls() {

        // Content Section - Icon & Text
        $this->start_controls_section(
            'content_section',
            [
                'label' => __('Content', 'philosofeet-core'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'menu_text',
            [
                'label' => __('Menu Text', 'philosofeet-core'),
                'type' => Controls_Manager::TEXT,
                'default' => __('MENU', 'philosofeet-core'),
                'placeholder' => __('Enter menu text', 'philosofeet-core'),
            ]
        );

        $this->add_control(
            'selected_icon',
            [
                'label' => __('Icon', 'philosofeet-core'),
                'type' => Controls_Manager::ICONS,
                'default' => [
                    'value' => 'fas fa-bars',
                    'library' => 'fa-solid',
                ],
                'recommended' => [
                    'fa-solid' => [
                        'bars',
                        'navicon',
                        'align-justify',
                        'stream',
                    ],
                ],
            ]
        );

        $this->add_control(
            'icon_position',
            [
                'label' => __('Icon Position', 'philosofeet-core'),
                'type' => Controls_Manager::SELECT,
                'default' => 'left',
                'options' => [
                    'left' => __('Left', 'philosofeet-core'),
                    'right' => __('Right', 'philosofeet-core'),
                ],
            ]
        );

        $this->end_controls_section();

        // Menu Items Section
        $this->start_controls_section(
            'menu_items_section',
            [
                'label' => __('Menu', 'philosofeet-core'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        // Get all registered WordPress menus
        $menus = wp_get_nav_menus();
        $menu_options = ['' => __('Select Menu', 'philosofeet-core')];

        foreach ($menus as $menu) {
            $menu_options[$menu->term_id] = $menu->name;
        }

        $this->add_control(
            'menu_id',
            [
                'label' => __('Select Menu', 'philosofeet-core'),
                'type' => Controls_Manager::SELECT,
                'options' => $menu_options,
                'default' => '',
                'description' => __('Select a menu from Appearance > Menus', 'philosofeet-core'),
            ]
        );

        $this->end_controls_section();

        // Footer Items Section
        $this->start_controls_section(
            'footer_items_section',
            [
                'label' => __('Footer Menu', 'philosofeet-core'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'footer_menu_id',
            [
                'label' => __('Select Footer Menu', 'philosofeet-core'),
                'type' => Controls_Manager::SELECT,
                'options' => $menu_options,
                'default' => '',
                'description' => __('Select a menu from Appearance > Menus', 'philosofeet-core'),
            ]
        );

        $this->end_controls_section();

        // Style Section - Button
        $this->start_controls_section(
            'style_button_section',
            [
                'label' => __('Button', 'philosofeet-core'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'button_padding',
            [
                'label' => __('Padding', 'philosofeet-core'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .ph-hamburger-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'button_background',
            [
                'label' => __('Background Color', 'philosofeet-core'),
                'type' => Controls_Manager::COLOR,
                'default' => 'transparent',
                'selectors' => [
                    '{{WRAPPER}} .ph-hamburger-button' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'button_border',
                'label' => __('Border', 'philosofeet-core'),
                'selector' => '{{WRAPPER}} .ph-hamburger-button',
            ]
        );

        $this->add_responsive_control(
            'button_border_radius',
            [
                'label' => __('Border Radius', 'philosofeet-core'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .ph-hamburger-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        // Style Section - Text
        $this->start_controls_section(
            'style_text_section',
            [
                'label' => __('Menu Text', 'philosofeet-core'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'text_typography',
                'label' => __('Typography', 'philosofeet-core'),
                'selector' => '{{WRAPPER}} .ph-hamburger-text',
            ]
        );

        $this->add_control(
            'text_color',
            [
                'label' => __('Text Color', 'philosofeet-core'),
                'type' => Controls_Manager::COLOR,
                'default' => '#000000',
                'selectors' => [
                    '{{WRAPPER}} .ph-hamburger-text' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'text_spacing',
            [
                'label' => __('Spacing', 'philosofeet-core'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 50,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 8,
                ],
                'selectors' => [
                    '{{WRAPPER}} .ph-hamburger-button[data-icon-position="left"] .ph-hamburger-text' => 'margin-left: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .ph-hamburger-button[data-icon-position="right"] .ph-hamburger-text' => 'margin-right: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        // Style Section - Icon
        $this->start_controls_section(
            'style_icon_section',
            [
                'label' => __('Icon', 'philosofeet-core'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'icon_size',
            [
                'label' => __('Icon Size', 'philosofeet-core'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 10,
                        'max' => 100,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 24,
                ],
                'selectors' => [
                    '{{WRAPPER}} .ph-hamburger-icon' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}; font-size: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .ph-hamburger-icon svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .ph-hamburger-icon img' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .ph-hamburger-icon i' => 'font-size: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'icon_color',
            [
                'label' => __('Icon Color', 'philosofeet-core'),
                'type' => Controls_Manager::COLOR,
                'default' => '#000000',
                'selectors' => [
                    '{{WRAPPER}} .ph-hamburger-icon' => 'color: {{VALUE}} !important; fill: {{VALUE}} !important;',
                    '{{WRAPPER}} .ph-hamburger-icon svg' => 'fill: {{VALUE}} !important;',
                    '{{WRAPPER}} .ph-hamburger-icon svg path' => 'fill: {{VALUE}} !important;',
                    '{{WRAPPER}} .ph-hamburger-icon i' => 'color: {{VALUE}} !important;',
                    '{{WRAPPER}} .ph-hamburger-icon img' => 'filter: brightness(0) saturate(100%);',
                ],
            ]
        );

        $this->end_controls_section();

        // Style Section - Drawer
        $this->start_controls_section(
            'style_drawer_section',
            [
                'label' => __('Drawer', 'philosofeet-core'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'drawer_width',
            [
                'label' => __('Width', 'philosofeet-core'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px', '%', 'vw'],
                'range' => [
                    'px' => [
                        'min' => 200,
                        'max' => 800,
                    ],
                    '%' => [
                        'min' => 10,
                        'max' => 100,
                    ],
                    'vw' => [
                        'min' => 10,
                        'max' => 100,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 400,
                ],
                'mobile_default' => [
                    'unit' => '%',
                    'size' => 100,
                ],
                'selectors' => [
                    '{{WRAPPER}} .ph-drawer' => 'width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'drawer_background',
            [
                'label' => __('Background Color', 'philosofeet-core'),
                'type' => Controls_Manager::COLOR,
                'default' => '#ffffff',
                'selectors' => [
                    '{{WRAPPER}} .ph-drawer' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'drawer_padding',
            [
                'label' => __('Padding', 'philosofeet-core'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'default' => [
                    'top' => 40,
                    'right' => 30,
                    'bottom' => 40,
                    'left' => 30,
                    'unit' => 'px',
                ],
                'selectors' => [
                    '{{WRAPPER}} .ph-drawer-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        // Style Section - Menu Items
        $this->start_controls_section(
            'style_menu_items_section',
            [
                'label' => __('Menu Items', 'philosofeet-core'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'menu_item_typography',
                'label' => __('Typography', 'philosofeet-core'),
                'selector' => '{{WRAPPER}} .ph-drawer-menu-item',
            ]
        );

        $this->add_control(
            'menu_item_color',
            [
                'label' => __('Text Color', 'philosofeet-core'),
                'type' => Controls_Manager::COLOR,
                'default' => '#000000',
                'selectors' => [
                    '{{WRAPPER}} .ph-drawer-menu-item' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'menu_item_hover_color',
            [
                'label' => __('Hover Color', 'philosofeet-core'),
                'type' => Controls_Manager::COLOR,
                'default' => '#666666',
                'selectors' => [
                    '{{WRAPPER}} .ph-drawer-menu-item:hover' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'menu_item_spacing',
            [
                'label' => __('Spacing', 'philosofeet-core'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 50,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 20,
                ],
                'selectors' => [
                    '{{WRAPPER}} .ph-drawer-menu-item' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        // Style Section - Footer Items
        $this->start_controls_section(
            'style_footer_items_section',
            [
                'label' => __('Footer Items', 'philosofeet-core'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'footer_item_typography',
                'label' => __('Typography', 'philosofeet-core'),
                'selector' => '{{WRAPPER}} .ph-drawer-footer-item',
            ]
        );

        $this->add_control(
            'footer_item_color',
            [
                'label' => __('Text Color', 'philosofeet-core'),
                'type' => Controls_Manager::COLOR,
                'default' => '#000000',
                'selectors' => [
                    '{{WRAPPER}} .ph-drawer-footer-item' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'footer_item_spacing',
            [
                'label' => __('Spacing', 'philosofeet-core'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 50,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 15,
                ],
                'selectors' => [
                    '{{WRAPPER}} .ph-drawer-footer-item' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'footer_top_spacing',
            [
                'label' => __('Top Spacing', 'philosofeet-core'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 40,
                ],
                'selectors' => [
                    '{{WRAPPER}} .ph-drawer-footer' => 'margin-top: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        // Style Section - Close Button
        $this->start_controls_section(
            'style_close_button_section',
            [
                'label' => __('Close Button', 'philosofeet-core'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'close_button_size',
            [
                'label' => __('Size', 'philosofeet-core'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 20,
                        'max' => 60,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 32,
                ],
                'selectors' => [
                    '{{WRAPPER}} .ph-drawer-close' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'close_button_color',
            [
                'label' => __('Color', 'philosofeet-core'),
                'type' => Controls_Manager::COLOR,
                'default' => '#000000',
                'selectors' => [
                    '{{WRAPPER}} .ph-drawer-close svg' => 'fill: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_section();
    }

    /**
     * Get menu items from WordPress menu
     */
    private function get_menu_items($menu_id) {
        if (empty($menu_id)) {
            return [];
        }

        $menu_items = wp_get_nav_menu_items($menu_id);
        if (!$menu_items) {
            return [];
        }

        $items = [];
        foreach ($menu_items as $item) {
            $items[] = [
                'text' => $item->title,
                'url' => $item->url,
                'target' => $item->target,
            ];
        }

        return $items;
    }

    /**
     * Render widget output in the editor
     */
    protected function content_template() {
        ?>
        <#
        // Transform Elementor settings to React component format
        var widgetData = {
            menuText: settings.menu_text || 'MENU',
            showIcon: settings.show_icon === 'yes',
            iconPosition: settings.icon_position || 'left',
            menuId: settings.menu_id || '',
            footerMenuId: settings.footer_menu_id || '',
        };
        #>
        <div
            class="philosofeet-widget"
            data-widget-type="drawer-nav-menu"
            data-widget-id="{{ view.getIDInt() }}"
            data-widget-settings="{{ JSON.stringify(widgetData) }}"
            data-initialized="false"
        >
            <div class="philosofeet-loading" style="padding: 20px; background: #f0f0f0; border: 2px solid #ccc; text-align: center;">
                <p><strong>Loading Hamburger Menu...</strong></p>
                <p><small>If this message persists, check the browser console for errors.</small></p>
            </div>
        </div>
        <?php
    }

    /**
     * Prepare widget data for React
     */
    protected function prepare_widget_data($settings) {
        $menu_items = $this->get_menu_items($settings['menu_id']);
        $footer_items = $this->get_menu_items($settings['footer_menu_id']);

        return [
            'menuText' => $settings['menu_text'],
            'showIcon' => $settings['show_icon'] === 'yes',
            'iconPosition' => $settings['icon_position'],
            'menuItems' => $menu_items,
            'footerItems' => $footer_items,
        ];
    }
}
