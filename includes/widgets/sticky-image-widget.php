<?php
namespace Philosofeet\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Image_Size;
use Elementor\Utils;

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

class Sticky_Image_Widget extends Base_Widget {

	public function get_name() {
		return 'sticky-image';
	}

	public function get_title() {
		return __('Sticky Image', 'philosofeet-core');
	}

	public function get_icon() {
		return 'eicon-image-rollover';
	}

	public function get_categories() {
		return ['philosofeet'];
	}

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
			'image',
			[
				'label' => __('Choose Image', 'philosofeet-core'),
				'type' => Controls_Manager::MEDIA,
				'default' => [
					'url' => Utils::get_placeholder_image_src(),
				],
			]
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name' => 'image',
				'default' => 'full',
			]
		);

		$this->end_controls_section();
	}

	protected function prepare_widget_data($settings) {
		$data = [
			'image' => [],
		];

		// Process image data
		if (!empty($settings['image']['id'])) {
			$image_size = $settings['image_size'] ?? 'full';
			$image_url = wp_get_attachment_image_src($settings['image']['id'], $image_size);

			$data['image'] = [
				'url' => $image_url ? $image_url[0] : $settings['image']['url'],
				'alt' => get_post_meta($settings['image']['id'], '_wp_attachment_image_alt', true) ?: '',
			];
		} elseif (!empty($settings['image']['url'])) {
			$data['image'] = [
				'url' => $settings['image']['url'],
				'alt' => '',
			];
		}

		return $data;
	}
}
