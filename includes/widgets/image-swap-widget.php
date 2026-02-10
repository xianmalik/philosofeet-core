<?php
/**
 * Image Swap Widget
 *
 * Displays an image or video that swaps to another on hover
 * Videos autoplay on hover without controls
 *
 * @package Philosofeet\Widgets
 */

namespace Philosofeet\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Css_Filter;

if (!defined('ABSPATH')) {
  exit; // Exit if accessed directly.
}

/**
 * Image Swap Widget Class
 */
class Image_Swap_Widget extends Base_Widget {

  /**
   * Get widget name.
   *
   * @return string Widget name.
   */
  public function get_name() {
    return 'image-swap';
  }

  /**
   * Get widget title.
   *
   * @return string Widget title.
   */
  public function get_title() {
    return __('Media Swap', 'philosofeet-core');
  }

  /**
   * Get widget icon.
   *
   * @return string Widget icon.
   */
  public function get_icon() {
    return 'eicon-image-rollover';
  }

  /**
   * Get widget categories.
   *
   * @return array Widget categories.
   */
  public function get_categories() {
    return ['philosofeet'];
  }

  /**
   * Get widget keywords.
   *
   * @return array Widget keywords.
   */
  public function get_keywords() {
    return ['image', 'hover', 'swap', 'rollover', 'flip'];
  }

  /**
   * Register widget controls.
   */
  protected function register_controls() {
    // Content Section - Media
    $this->start_controls_section(
      'section_images',
      [
        'label' => __('Media', 'philosofeet-core'),
      ]
    );

    // Default Media Type
    $this->add_control(
      'default_media_type',
      [
        'label' => __('Default Media Type', 'philosofeet-core'),
        'type' => Controls_Manager::CHOOSE,
        'options' => [
          'image' => [
            'title' => __('Image', 'philosofeet-core'),
            'icon' => 'eicon-image',
          ],
          'video' => [
            'title' => __('Video', 'philosofeet-core'),
            'icon' => 'eicon-video-camera',
          ],
        ],
        'default' => 'image',
        'toggle' => false,
      ]
    );

    $this->add_control(
      'default_image',
      [
        'label' => __('Default Image', 'philosofeet-core'),
        'type' => Controls_Manager::MEDIA,
        'default' => [
          'url' => \Elementor\Utils::get_placeholder_image_src(),
        ],
        'dynamic' => [
          'active' => true,
        ],
        'media_types' => ['image'],
        'condition' => [
          'default_media_type' => 'image',
        ],
      ]
    );

    $this->add_control(
      'default_video',
      [
        'label' => __('Default Video', 'philosofeet-core'),
        'type' => Controls_Manager::MEDIA,
        'dynamic' => [
          'active' => true,
        ],
        'media_types' => ['video'],
        'condition' => [
          'default_media_type' => 'video',
        ],
      ]
    );

    $this->add_control(
      'default_divider',
      [
        'type' => Controls_Manager::DIVIDER,
      ]
    );

    // Hover Media Type
    $this->add_control(
      'hover_media_type',
      [
        'label' => __('Hover Media Type', 'philosofeet-core'),
        'type' => Controls_Manager::CHOOSE,
        'options' => [
          'image' => [
            'title' => __('Image', 'philosofeet-core'),
            'icon' => 'eicon-image',
          ],
          'video' => [
            'title' => __('Video', 'philosofeet-core'),
            'icon' => 'eicon-video-camera',
          ],
        ],
        'default' => 'image',
        'toggle' => false,
      ]
    );

    $this->add_control(
      'hover_image',
      [
        'label' => __('Hover Image', 'philosofeet-core'),
        'type' => Controls_Manager::MEDIA,
        'default' => [
          'url' => \Elementor\Utils::get_placeholder_image_src(),
        ],
        'dynamic' => [
          'active' => true,
        ],
        'media_types' => ['image'],
        'condition' => [
          'hover_media_type' => 'image',
        ],
      ]
    );

    $this->add_control(
      'hover_video',
      [
        'label' => __('Hover Video', 'philosofeet-core'),
        'type' => Controls_Manager::MEDIA,
        'dynamic' => [
          'active' => true,
        ],
        'media_types' => ['video'],
        'condition' => [
          'hover_media_type' => 'video',
        ],
        'description' => __('Video will autoplay on hover without controls', 'philosofeet-core'),
      ]
    );

    $this->add_control(
      'hover_divider',
      [
        'type' => Controls_Manager::DIVIDER,
      ]
    );

    $this->add_control(
      'alt_text',
      [
        'label' => __('Alt Text', 'philosofeet-core'),
        'type' => Controls_Manager::TEXT,
        'default' => '',
        'placeholder' => __('Enter image alt text', 'philosofeet-core'),
        'dynamic' => [
          'active' => true,
        ],
      ]
    );

    $this->add_control(
      'link',
      [
        'label' => __('Link', 'philosofeet-core'),
        'type' => Controls_Manager::URL,
        'placeholder' => __('https://your-link.com', 'philosofeet-core'),
        'dynamic' => [
          'active' => true,
        ],
      ]
    );

    $this->add_control(
      'transition_duration',
      [
        'label' => __('Transition Duration (ms)', 'philosofeet-core'),
        'type' => Controls_Manager::NUMBER,
        'default' => 300,
        'min' => 0,
        'max' => 2000,
        'step' => 50,
      ]
    );

    $this->end_controls_section();

    // Style Section - Container
    $this->start_controls_section(
      'section_style_container',
      [
        'label' => __('Container', 'philosofeet-core'),
        'tab' => Controls_Manager::TAB_STYLE,
      ]
    );

    $this->add_responsive_control(
      'width',
      [
        'label' => __('Width', 'philosofeet-core'),
        'type' => Controls_Manager::SLIDER,
        'size_units' => ['px', '%', 'vw'],
        'range' => [
          'px' => [
            'min' => 0,
            'max' => 2000,
          ],
          '%' => [
            'min' => 0,
            'max' => 100,
          ],
          'vw' => [
            'min' => 0,
            'max' => 100,
          ],
        ],
        'default' => [
          'unit' => '%',
          'size' => 100,
        ],
        'selectors' => [
          '{{WRAPPER}} .image-swap-container' => 'width: {{SIZE}}{{UNIT}};',
        ],
      ]
    );

    $this->add_responsive_control(
      'height',
      [
        'label' => __('Height', 'philosofeet-core'),
        'type' => Controls_Manager::SLIDER,
        'size_units' => ['px', 'vh'],
        'range' => [
          'px' => [
            'min' => 0,
            'max' => 2000,
          ],
          'vh' => [
            'min' => 0,
            'max' => 100,
          ],
        ],
        'selectors' => [
          '{{WRAPPER}} .image-swap-container' => 'height: {{SIZE}}{{UNIT}};',
        ],
      ]
    );

    $this->add_responsive_control(
      'min_height',
      [
        'label' => __('Min Height', 'philosofeet-core'),
        'type' => Controls_Manager::SLIDER,
        'size_units' => ['px', 'vh'],
        'range' => [
          'px' => [
            'min' => 0,
            'max' => 2000,
          ],
          'vh' => [
            'min' => 0,
            'max' => 100,
          ],
        ],
        'default' => [
          'unit' => 'px',
          'size' => 300,
        ],
        'selectors' => [
          '{{WRAPPER}} .image-swap-container' => 'min-height: {{SIZE}}{{UNIT}};',
        ],
      ]
    );

    $this->add_responsive_control(
      'max_width',
      [
        'label' => __('Max Width', 'philosofeet-core'),
        'type' => Controls_Manager::SLIDER,
        'size_units' => ['px', '%'],
        'range' => [
          'px' => [
            'min' => 0,
            'max' => 2000,
          ],
          '%' => [
            'min' => 0,
            'max' => 100,
          ],
        ],
        'selectors' => [
          '{{WRAPPER}} .image-swap-container' => 'max-width: {{SIZE}}{{UNIT}};',
        ],
      ]
    );

    $this->add_responsive_control(
      'alignment',
      [
        'label' => __('Alignment', 'philosofeet-core'),
        'type' => Controls_Manager::CHOOSE,
        'options' => [
          'left' => [
            'title' => __('Left', 'philosofeet-core'),
            'icon' => 'eicon-text-align-left',
          ],
          'center' => [
            'title' => __('Center', 'philosofeet-core'),
            'icon' => 'eicon-text-align-center',
          ],
          'right' => [
            'title' => __('Right', 'philosofeet-core'),
            'icon' => 'eicon-text-align-right',
          ],
        ],
        'selectors' => [
          '{{WRAPPER}}' => 'text-align: {{VALUE}};',
        ],
      ]
    );

    $this->add_group_control(
      Group_Control_Border::get_type(),
      [
        'name' => 'container_border',
        'selector' => '{{WRAPPER}} .image-swap-container',
      ]
    );

    $this->add_responsive_control(
      'container_border_radius',
      [
        'label' => __('Border Radius', 'philosofeet-core'),
        'type' => Controls_Manager::DIMENSIONS,
        'size_units' => ['px', '%'],
        'selectors' => [
          '{{WRAPPER}} .image-swap-container' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
        ],
      ]
    );

    $this->add_group_control(
      Group_Control_Box_Shadow::get_type(),
      [
        'name' => 'container_box_shadow',
        'selector' => '{{WRAPPER}} .image-swap-container',
      ]
    );

    $this->end_controls_section();

    // Style Section - Image
    $this->start_controls_section(
      'section_style_image',
      [
        'label' => __('Image', 'philosofeet-core'),
        'tab' => Controls_Manager::TAB_STYLE,
      ]
    );

    $this->add_responsive_control(
      'object_fit',
      [
        'label' => __('Object Fit', 'philosofeet-core'),
        'type' => Controls_Manager::SELECT,
        'options' => [
          'fill' => __('Fill', 'philosofeet-core'),
          'contain' => __('Contain', 'philosofeet-core'),
          'cover' => __('Cover', 'philosofeet-core'),
          'none' => __('None', 'philosofeet-core'),
          'scale-down' => __('Scale Down', 'philosofeet-core'),
        ],
        'default' => 'contain',
        'selectors' => [
          '{{WRAPPER}} .image-swap-container img, {{WRAPPER}} .image-swap-container video' => 'object-fit: {{VALUE}};',
        ],
      ]
    );

    $this->add_responsive_control(
      'object_position',
      [
        'label' => __('Object Position', 'philosofeet-core'),
        'type' => Controls_Manager::SELECT,
        'options' => [
          'center center' => __('Center Center', 'philosofeet-core'),
          'center left' => __('Center Left', 'philosofeet-core'),
          'center right' => __('Center Right', 'philosofeet-core'),
          'top center' => __('Top Center', 'philosofeet-core'),
          'top left' => __('Top Left', 'philosofeet-core'),
          'top right' => __('Top Right', 'philosofeet-core'),
          'bottom center' => __('Bottom Center', 'philosofeet-core'),
          'bottom left' => __('Bottom Left', 'philosofeet-core'),
          'bottom right' => __('Bottom Right', 'philosofeet-core'),
        ],
        'default' => 'center center',
        'selectors' => [
          '{{WRAPPER}} .image-swap-container img, {{WRAPPER}} .image-swap-container video' => 'object-position: {{VALUE}};',
        ],
        'condition' => [
          'object_fit!' => 'fill',
        ],
      ]
    );

    $this->start_controls_tabs('image_effects_tabs');

    // Default Image Effects
    $this->start_controls_tab(
      'tab_default_effects',
      [
        'label' => __('Default', 'philosofeet-core'),
      ]
    );

    $this->add_control(
      'default_opacity',
      [
        'label' => __('Opacity', 'philosofeet-core'),
        'type' => Controls_Manager::SLIDER,
        'range' => [
          'px' => [
            'min' => 0,
            'max' => 1,
            'step' => 0.1,
          ],
        ],
        'default' => [
          'size' => 1,
        ],
        'selectors' => [
          '{{WRAPPER}} .image-swap-default' => 'opacity: {{SIZE}};',
        ],
      ]
    );

    $this->add_group_control(
      Group_Control_Css_Filter::get_type(),
      [
        'name' => 'default_css_filters',
        'selector' => '{{WRAPPER}} .image-swap-default',
      ]
    );

    $this->end_controls_tab();

    // Hover Image Effects
    $this->start_controls_tab(
      'tab_hover_effects',
      [
        'label' => __('Hover', 'philosofeet-core'),
      ]
    );

    $this->add_control(
      'hover_opacity',
      [
        'label' => __('Opacity', 'philosofeet-core'),
        'type' => Controls_Manager::SLIDER,
        'range' => [
          'px' => [
            'min' => 0,
            'max' => 1,
            'step' => 0.1,
          ],
        ],
        'default' => [
          'size' => 1,
        ],
        'selectors' => [
          '{{WRAPPER}} .image-swap-hover' => 'opacity: {{SIZE}};',
        ],
      ]
    );

    $this->add_group_control(
      Group_Control_Css_Filter::get_type(),
      [
        'name' => 'hover_css_filters',
        'selector' => '{{WRAPPER}} .image-swap-hover',
      ]
    );

    $this->end_controls_tab();

    $this->end_controls_tabs();

    $this->end_controls_section();
  }

  /**
   * Prepare widget data for React component.
   *
   * @param array $settings Widget settings.
   * @return array Prepared data.
   */
  protected function prepare_widget_data($settings) {
    // Get default media based on type
    $default_media_type = !empty($settings['default_media_type']) ? $settings['default_media_type'] : 'image';
    $default_media_field = $default_media_type === 'video' ? 'default_video' : 'default_image';

    // Get hover media based on type
    $hover_media_type = !empty($settings['hover_media_type']) ? $settings['hover_media_type'] : 'image';
    $hover_media_field = $hover_media_type === 'video' ? 'hover_video' : 'hover_image';

    return [
      'defaultImage' => [
        'url' => !empty($settings[$default_media_field]['url']) ? $settings[$default_media_field]['url'] : '',
        'id' => !empty($settings[$default_media_field]['id']) ? $settings[$default_media_field]['id'] : '',
      ],
      'hoverImage' => [
        'url' => !empty($settings[$hover_media_field]['url']) ? $settings[$hover_media_field]['url'] : '',
        'id' => !empty($settings[$hover_media_field]['id']) ? $settings[$hover_media_field]['id'] : '',
      ],
      'altText' => !empty($settings['alt_text']) ? $settings['alt_text'] : '',
      'link' => [
        'url' => !empty($settings['link']['url']) ? $settings['link']['url'] : '',
        'isExternal' => !empty($settings['link']['is_external']) ? $settings['link']['is_external'] : false,
        'nofollow' => !empty($settings['link']['nofollow']) ? $settings['link']['nofollow'] : false,
      ],
      'transitionDuration' => !empty($settings['transition_duration']) ? (int) $settings['transition_duration'] : 300,
      'objectFit' => !empty($settings['object_fit']) ? $settings['object_fit'] : 'contain',
      'objectPosition' => !empty($settings['object_position']) ? $settings['object_position'] : 'center center',
    ];
  }
}
