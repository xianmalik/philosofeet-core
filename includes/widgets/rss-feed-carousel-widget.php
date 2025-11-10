<?php
/**
 * RSS Feed Carousel Widget
 *
 * @package Philosofeet\Widgets
 */

namespace Philosofeet\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Image_Size;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * RSS Feed Carousel Widget Class
 *
 * Displays RSS feed items in a carousel format
 */
class RSS_Feed_Carousel_Widget extends Base_Widget {

    /**
     * Get widget name
     */
    public function get_name() {
        return 'rss-feed-carousel';
    }

    /**
     * Get widget title
     */
    public function get_title() {
        return __('RSS Feed Carousel', 'philosofeet-core');
    }

    /**
     * Get widget icon
     */
    public function get_icon() {
        return 'eicon-post-slider';
    }

    /**
     * Register widget controls
     */
    protected function register_controls() {
        // Content Section - RSS Feed Settings
        $this->start_controls_section(
            'feed_section',
            [
                'label' => __('RSS Feed', 'philosofeet-core'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'feed_source',
            [
                'label' => __('Feed Source URL', 'philosofeet-core'),
                'type' => Controls_Manager::URL,
                'placeholder' => 'https://example.com/feed',
                'description' => __('Enter the RSS feed URL', 'philosofeet-core'),
                'default' => [
                    'url' => '',
                ],
            ]
        );

        $this->add_control(
            'feed_limit',
            [
                'label' => __('Number of Items', 'philosofeet-core'),
                'type' => Controls_Manager::NUMBER,
                'default' => 10,
                'min' => 1,
                'max' => 50,
            ]
        );

        $this->end_controls_section();

        // Content Section - Carousel Settings
        $this->start_controls_section(
            'carousel_section',
            [
                'label' => __('Carousel Settings', 'philosofeet-core'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_responsive_control(
            'items_per_row',
            [
                'label' => __('Items Per Row', 'philosofeet-core'),
                'type' => Controls_Manager::NUMBER,
                'default' => 3,
                'tablet_default' => 2,
                'mobile_default' => 1,
                'min' => 1,
                'max' => 6,
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
                'default' => 3000,
                'min' => 1000,
                'max' => 10000,
                'step' => 500,
                'condition' => [
                    'autoplay' => 'yes',
                ],
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

        $this->end_controls_section();

        // Content Section - Navigation
        $this->start_controls_section(
            'navigation_section',
            [
                'label' => __('Navigation', 'philosofeet-core'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'show_arrows',
            [
                'label' => __('Show Arrows', 'philosofeet-core'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'philosofeet-core'),
                'label_off' => __('No', 'philosofeet-core'),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'show_dots',
            [
                'label' => __('Show Dots', 'philosofeet-core'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'philosofeet-core'),
                'label_off' => __('No', 'philosofeet-core'),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->end_controls_section();

        // Content Section - Content Display
        $this->start_controls_section(
            'content_section',
            [
                'label' => __('Content Display', 'philosofeet-core'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'show_thumbnail',
            [
                'label' => __('Show Thumbnail', 'philosofeet-core'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'philosofeet-core'),
                'label_off' => __('No', 'philosofeet-core'),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'placeholder_image',
            [
                'label' => __('Placeholder Image', 'philosofeet-core'),
                'type' => Controls_Manager::MEDIA,
                'default' => [
                    'url' => \Elementor\Utils::get_placeholder_image_src(),
                ],
                'condition' => [
                    'show_thumbnail' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'show_title',
            [
                'label' => __('Show Title', 'philosofeet-core'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'philosofeet-core'),
                'label_off' => __('No', 'philosofeet-core'),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'show_date',
            [
                'label' => __('Show Date', 'philosofeet-core'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'philosofeet-core'),
                'label_off' => __('No', 'philosofeet-core'),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'show_excerpt',
            [
                'label' => __('Show Excerpt', 'philosofeet-core'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'philosofeet-core'),
                'label_off' => __('No', 'philosofeet-core'),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'excerpt_length',
            [
                'label' => __('Excerpt Length', 'philosofeet-core'),
                'type' => Controls_Manager::NUMBER,
                'default' => 150,
                'min' => 50,
                'max' => 500,
                'condition' => [
                    'show_excerpt' => 'yes',
                ],
            ]
        );

        $this->end_controls_section();

        // Style Section - Carousel Container
        $this->start_controls_section(
            'carousel_style_section',
            [
                'label' => __('Carousel Container', 'philosofeet-core'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'carousel_gap',
            [
                'label' => __('Gap Between Items', 'philosofeet-core'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                        'step' => 1,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 20,
                ],
            ]
        );

        $this->end_controls_section();

        // Style Section - Card Style
        $this->start_controls_section(
            'card_style_section',
            [
                'label' => __('Card Style', 'philosofeet-core'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'card_background',
            [
                'label' => __('Background Color', 'philosofeet-core'),
                'type' => Controls_Manager::COLOR,
                'default' => '#ffffff',
            ]
        );

        $this->add_responsive_control(
            'card_padding',
            [
                'label' => __('Padding', 'philosofeet-core'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'default' => [
                    'top' => '15',
                    'right' => '15',
                    'bottom' => '15',
                    'left' => '15',
                    'unit' => 'px',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'card_border',
                'label' => __('Border', 'philosofeet-core'),
                'selector' => '{{WRAPPER}} .ph-rss-feed-card',
            ]
        );

        $this->add_responsive_control(
            'card_border_radius',
            [
                'label' => __('Border Radius', 'philosofeet-core'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'default' => [
                    'top' => '8',
                    'right' => '8',
                    'bottom' => '8',
                    'left' => '8',
                    'unit' => 'px',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'card_box_shadow',
                'label' => __('Box Shadow', 'philosofeet-core'),
                'selector' => '{{WRAPPER}} .ph-rss-feed-card',
            ]
        );

        $this->end_controls_section();

        // Style Section - Thumbnail
        $this->start_controls_section(
            'thumbnail_style_section',
            [
                'label' => __('Thumbnail', 'philosofeet-core'),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'show_thumbnail' => 'yes',
                ],
            ]
        );

        $this->add_responsive_control(
            'thumbnail_height',
            [
                'label' => __('Height', 'philosofeet-core'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px', '%'],
                'range' => [
                    'px' => [
                        'min' => 100,
                        'max' => 500,
                        'step' => 10,
                    ],
                    '%' => [
                        'min' => 50,
                        'max' => 200,
                        'step' => 10,
                    ],
                ],
                'default' => [
                    'unit' => '%',
                    'size' => 100,
                ],
                'description' => __('Use 100% for square images (aspect ratio 1:1)', 'philosofeet-core'),
            ]
        );

        $this->add_responsive_control(
            'thumbnail_border_radius',
            [
                'label' => __('Border Radius', 'philosofeet-core'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'default' => [
                    'top' => '4',
                    'right' => '4',
                    'bottom' => '4',
                    'left' => '4',
                    'unit' => 'px',
                ],
            ]
        );

        $this->add_control(
            'thumbnail_object_fit',
            [
                'label' => __('Object Fit', 'philosofeet-core'),
                'type' => Controls_Manager::SELECT,
                'default' => 'cover',
                'options' => [
                    'cover' => __('Cover', 'philosofeet-core'),
                    'contain' => __('Contain', 'philosofeet-core'),
                    'fill' => __('Fill', 'philosofeet-core'),
                ],
            ]
        );

        $this->end_controls_section();

        // Style Section - Title
        $this->start_controls_section(
            'title_style_section',
            [
                'label' => __('Title', 'philosofeet-core'),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'show_title' => 'yes',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'title_typography',
                'label' => __('Typography', 'philosofeet-core'),
                'selector' => '{{WRAPPER}} .ph-rss-feed-title',
                'fields_options' => [
                    'font_size' => [
                        'default' => [
                            'unit' => 'px',
                            'size' => 18,
                        ],
                    ],
                ],
            ]
        );

        $this->add_control(
            'title_color',
            [
                'label' => __('Color', 'philosofeet-core'),
                'type' => Controls_Manager::COLOR,
                'default' => '#ffffff',
            ]
        );

        $this->add_responsive_control(
            'title_spacing',
            [
                'label' => __('Spacing', 'philosofeet-core'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 50,
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

        // Style Section - Date
        $this->start_controls_section(
            'date_style_section',
            [
                'label' => __('Date', 'philosofeet-core'),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'show_date' => 'yes',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'date_typography',
                'label' => __('Typography', 'philosofeet-core'),
                'selector' => '{{WRAPPER}} .ph-rss-feed-date',
                'fields_options' => [
                    'font_size' => [
                        'default' => [
                            'unit' => 'px',
                            'size' => 12,
                        ],
                    ],
                ],
            ]
        );

        $this->add_control(
            'date_color',
            [
                'label' => __('Color', 'philosofeet-core'),
                'type' => Controls_Manager::COLOR,
                'default' => '#ffffff',
            ]
        );

        $this->add_responsive_control(
            'date_spacing',
            [
                'label' => __('Spacing', 'philosofeet-core'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 50,
                        'step' => 1,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 8,
                ],
            ]
        );

        $this->end_controls_section();

        // Style Section - Excerpt
        $this->start_controls_section(
            'excerpt_style_section',
            [
                'label' => __('Excerpt', 'philosofeet-core'),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'show_excerpt' => 'yes',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'excerpt_typography',
                'label' => __('Typography', 'philosofeet-core'),
                'selector' => '{{WRAPPER}} .ph-rss-feed-excerpt',
                'fields_options' => [
                    'font_size' => [
                        'default' => [
                            'unit' => 'px',
                            'size' => 14,
                        ],
                    ],
                ],
            ]
        );

        $this->add_control(
            'excerpt_color',
            [
                'label' => __('Color', 'philosofeet-core'),
                'type' => Controls_Manager::COLOR,
                'default' => '#ffffff',
            ]
        );

        $this->end_controls_section();

        // Style Section - Arrows
        $this->start_controls_section(
            'arrows_style_section',
            [
                'label' => __('Arrows', 'philosofeet-core'),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'show_arrows' => 'yes',
                ],
            ]
        );

        $this->add_responsive_control(
            'arrow_size',
            [
                'label' => __('Size', 'philosofeet-core'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 20,
                        'max' => 100,
                        'step' => 5,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 40,
                ],
            ]
        );

        $this->add_control(
            'arrow_color',
            [
                'label' => __('Color', 'philosofeet-core'),
                'type' => Controls_Manager::COLOR,
                'default' => '#000000',
            ]
        );

        $this->add_control(
            'arrow_background',
            [
                'label' => __('Background Color', 'philosofeet-core'),
                'type' => Controls_Manager::COLOR,
                'default' => '#ffffff',
            ]
        );

        $this->add_control(
            'arrow_hover_color',
            [
                'label' => __('Hover Color', 'philosofeet-core'),
                'type' => Controls_Manager::COLOR,
                'default' => '#ffffff',
            ]
        );

        $this->add_control(
            'arrow_hover_background',
            [
                'label' => __('Hover Background', 'philosofeet-core'),
                'type' => Controls_Manager::COLOR,
                'default' => '#000000',
            ]
        );

        $this->end_controls_section();

        // Style Section - Dots
        $this->start_controls_section(
            'dots_style_section',
            [
                'label' => __('Dots', 'philosofeet-core'),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'show_dots' => 'yes',
                ],
            ]
        );

        $this->add_responsive_control(
            'dot_size',
            [
                'label' => __('Size', 'philosofeet-core'),
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

        $this->add_control(
            'dot_color',
            [
                'label' => __('Color', 'philosofeet-core'),
                'type' => Controls_Manager::COLOR,
                'default' => '#cccccc',
            ]
        );

        $this->add_control(
            'dot_active_color',
            [
                'label' => __('Active Color', 'philosofeet-core'),
                'type' => Controls_Manager::COLOR,
                'default' => '#000000',
            ]
        );

        $this->add_responsive_control(
            'dot_spacing',
            [
                'label' => __('Spacing', 'philosofeet-core'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 30,
                        'step' => 1,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 20,
                ],
            ]
        );

        $this->end_controls_section();
    }

    /**
     * Prepare widget data for React
     */
    protected function prepare_widget_data($settings) {
        return [
            'feedSource' => !empty($settings['feed_source']['url']) ? $settings['feed_source']['url'] : '',
            'feedLimit' => $settings['feed_limit'],
            'itemsPerRow' => [
                'desktop' => $settings['items_per_row'],
                'tablet' => $settings['items_per_row_tablet'],
                'mobile' => $settings['items_per_row_mobile'],
            ],
            'autoplay' => $settings['autoplay'] === 'yes',
            'autoplaySpeed' => $settings['autoplay_speed'],
            'infiniteLoop' => $settings['infinite_loop'] === 'yes',
            'showArrows' => $settings['show_arrows'] === 'yes',
            'showDots' => $settings['show_dots'] === 'yes',
            'showThumbnail' => $settings['show_thumbnail'] === 'yes',
            'placeholderImage' => !empty($settings['placeholder_image']['url']) ? $settings['placeholder_image']['url'] : '',
            'showTitle' => $settings['show_title'] === 'yes',
            'showDate' => $settings['show_date'] === 'yes',
            'showExcerpt' => $settings['show_excerpt'] === 'yes',
            'excerptLength' => $settings['excerpt_length'],
            'carouselGap' => $settings['carousel_gap'],
            'cardBackground' => $settings['card_background'],
            'cardPadding' => $settings['card_padding'],
            'cardBorderRadius' => $settings['card_border_radius'],
            'thumbnailHeight' => $settings['thumbnail_height'],
            'thumbnailBorderRadius' => $settings['thumbnail_border_radius'],
            'thumbnailObjectFit' => $settings['thumbnail_object_fit'],
            'titleColor' => $settings['title_color'],
            'titleSpacing' => $settings['title_spacing'],
            'dateColor' => $settings['date_color'],
            'dateSpacing' => $settings['date_spacing'],
            'excerptColor' => $settings['excerpt_color'],
            'arrowSize' => $settings['arrow_size'],
            'arrowColor' => $settings['arrow_color'],
            'arrowBackground' => $settings['arrow_background'],
            'arrowHoverColor' => $settings['arrow_hover_color'],
            'arrowHoverBackground' => $settings['arrow_hover_background'],
            'dotSize' => $settings['dot_size'],
            'dotColor' => $settings['dot_color'],
            'dotActiveColor' => $settings['dot_active_color'],
            'dotSpacing' => $settings['dot_spacing'],
        ];
    }
}
