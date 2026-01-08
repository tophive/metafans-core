<?php
/**
 * Plugin Name: Lottie Animations (Elementor Widget)
 * Description: Lottie animation widget for Elementor page builder.
 * Version: 1.0
 * Author: Tophive
 */

/**
 * Lottie Animations Widget
 *
 * @package My_Core_Plugin
 */
namespace My_Core_Plugin\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

if (!defined('ABSPATH')) {
    exit();
}

class Lottie_Animations extends Widget_Base
{
    public function get_name()
    {
        return 'lottie_animations';
    }

    public function get_title()
    {
        return __('Lottie Animations', 'my-core-plugin');
    }

    public function get_icon()
    {
        return 'eicon-animation';
    }

    public function get_categories()
    {
        return ['th-general'];
    }

    public function get_script_depends()
    {
        return ['tophive-elementor-bundle'];
    }
    public function get_style_depends()
    {
        return ['tophive-elements-css'];
    }

    protected function register_controls()
    {
        // ===== Content Controls =====
        $this->start_controls_section('content_section', [
            'label' => __('Content', 'my-core-plugin'),
            'tab' => Controls_Manager::TAB_CONTENT,
        ]);

        $this->add_control('lottie_file', [
            'label' => __('Upload Lottie JSON', 'my-core-plugin'),
            'type' => Controls_Manager::MEDIA,
            'media_types' => ['application/json'],
            'description' => __('Upload a .json (Bodymovin) or use URL below.', 'my-core-plugin'),
        ]);

        $this->add_control('lottie_url', [
            'label' => __('Or Lottie JSON URL', 'my-core-plugin'),
            'type' => Controls_Manager::TEXT,
            'placeholder' => 'https://assets.lottiefiles.com/.../animation.json',
            'description' => __('Used if no file is uploaded.', 'my-core-plugin'),
        ]);

        $this->add_control('renderer', [
            'label' => __('Renderer', 'my-core-plugin'),
            'type' => Controls_Manager::SELECT,
            'options' => [
                'svg' => __('SVG (crisp)', 'my-core-plugin'),
                'canvas' => __('Canvas (complex)', 'my-core-plugin'),
            ],
            'default' => 'svg',
        ]);

        $this->add_control('loop', [
            'label' => __('Loop', 'my-core-plugin'),
            'type' => Controls_Manager::SWITCHER,
            'label_on' => 'Yes',
            'label_off' => 'No',
            'return_value' => 'true',
            'default' => 'true',
        ]);

        $this->add_control('autoplay', [
            'label' => __('Autoplay', 'my-core-plugin'),
            'type' => Controls_Manager::SWITCHER,
            'label_on' => 'Yes',
            'label_off' => 'No',
            'return_value' => 'true',
            'default' => 'true',
        ]);

        $this->add_control('speed', [
            'label' => __('Speed', 'my-core-plugin'),
            'type' => Controls_Manager::NUMBER,
            'default' => 1,
            'min' => 0.1,
            'max' => 3,
            'step' => 0.1,
        ]);

        $this->add_control('trigger', [
            'label' => __('Trigger', 'my-core-plugin'),
            'type' => Controls_Manager::SELECT,
            'options' => [
                'none' => __('None', 'my-core-plugin'),
                'hover' => __('On Hover', 'my-core-plugin'),
                'click' => __('On Click (toggle)', 'my-core-plugin'),
            ],
            'default' => 'none',
        ]);

        $this->end_controls_section();

        // ===== Style Controls =====
        $this->start_controls_section('section_style', [
            'label' => __('Style', 'my-core-plugin'),
            'tab' => Controls_Manager::TAB_STYLE,
        ]);

        $this->add_control('width', [
            'label' => __('Width', 'my-core-plugin'),
            'type' => Controls_Manager::SLIDER,
            'size_units' => ['px', '%'],
            'range' => [
                'px' => ['min' => 50, 'max' => 2000],
                '%' => ['min' => 10, 'max' => 100],
            ],
            'default' => ['unit' => 'px', 'size' => 300],
        ]);

        $this->add_control('height', [
            'label' => __('Height', 'my-core-plugin'),
            'type' => Controls_Manager::SLIDER,
            'size_units' => ['px', '%'],
            'range' => [
                'px' => ['min' => 50, 'max' => 2000],
                '%' => ['min' => 10, 'max' => 100],
            ],
            'default' => ['unit' => 'px', 'size' => 300],
        ]);

        $this->add_control('alignment', [
            'label' => __('Alignment', 'my-core-plugin'),
            'type' => Controls_Manager::CHOOSE,
            'options' => [
                'left' => ['title' => __('Left', 'my-core-plugin'), 'icon' => 'eicon-text-align-left'],
                'center' => ['title' => __('Center', 'my-core-plugin'), 'icon' => 'eicon-text-align-center'],
                'right' => ['title' => __('Right', 'my-core-plugin'), 'icon' => 'eicon-text-align-right'],
            ],
            'default' => 'center',
            'toggle' => true,
        ]);

        $this->add_control('wrapper_bg_color', [
            'label' => __('Wrapper Background', 'my-core-plugin'),
            'type' => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .my-lottie-wrapper' => 'background-color: {{VALUE}};',
            ],
        ]);

        $this->add_control('svg_path_color', [
            'label' => __('SVG Path Color', 'my-core-plugin'),
            'type' => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .my-lottie svg path' => 'fill: {{VALUE}};',
            ],
            'description' => __('Tint the paths of SVG-based Lottie animations.', 'my-core-plugin'),
        ]);

        $this->end_controls_section();
    }

    protected function render()
    {
        $s = $this->get_settings_for_display();

        $lottie_url = '';
        if (!empty($s['lottie_file']['url'])) {
            $lottie_url = esc_url($s['lottie_file']['url']);
        } elseif (!empty($s['lottie_url'])) {
            $lottie_url = esc_url($s['lottie_url']);
        }

        $uid = 'th-lottie-' . uniqid();
        $align = !empty($s['alignment']) ? $s['alignment'] : 'center';

        $w_unit = isset($s['width']['unit']) ? $s['width']['unit'] : 'px';
        $w_size = isset($s['width']['size']) ? $s['width']['size'] : 300;
        $h_unit = isset($s['height']['unit']) ? $s['height']['unit'] : 'px';
        $h_size = isset($s['height']['size']) ? $s['height']['size'] : 300;

        $style = sprintf('width:%s%s;height:%s%s;', esc_attr($w_size), esc_attr($w_unit), esc_attr($h_size), esc_attr($h_unit));

        $svg_color = !empty($s['svg_path_color']) ? esc_attr($s['svg_path_color']) : '';

        echo '<div class="my-lottie-wrapper" style="text-align:' . esc_attr($align) . ';">';

        if ($lottie_url) {

            echo '<div id="' .
                esc_attr($uid) .
                '"' .
                ' class="my-lottie"' .
                ' data-url="' .
                esc_url($lottie_url) .
                '"' .
                ' data-loop="' .
                (isset($s['loop']) && $s['loop'] === 'true' ? 'true' : 'false') .
                '"' .
                ' data-autoplay="' .
                (isset($s['autoplay']) && $s['autoplay'] === 'true' ? 'true' : 'false') .
                '"' .
                ' data-speed="' .
                esc_attr(isset($s['speed']) ? (string) $s['speed'] : '1') .
                '"' .
                ' data-renderer="' .
                (isset($s['renderer']) && $s['renderer'] === 'canvas' ? 'canvas' : 'svg') .
                '"' .
                ' data-trigger="' .
                esc_attr(isset($s['trigger']) ? $s['trigger'] : 'none') .
                '"' .
                ' data-svg-path-color="' .
                $svg_color .
                '"' .
                ' role="img" aria-label="' .
                esc_attr__('Lottie animation', 'my-core-plugin') .
                '"' .
                ' style="' .
                esc_attr($style) .
                '"></div>';
        } else {
            echo '<p style="color:#888;font-size:14px;margin:0;">' . esc_html__('Please upload a Lottie JSON file or enter a URL.', 'my-core-plugin') . '</p>';
        }

        echo '</div>';
    }
}