<?php
use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Widget_Base;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

add_action('elementor/element/section/section_advanced/before_section_end', function($element, $args){

    // Add new section for Matrix Grid
    $element->start_controls_section(
        'matrix_grid_section',
        [
            'label' => __('TH Matrix Grid', 'tophive-core'),
            'tab' => Controls_Manager::TAB_ADVANCED,
        ]
    );

    $element->add_control(
        'matrix_grid_enable',
        [
            'label' => __('Enable Grid', 'tophive-core'),
            'type' => Controls_Manager::SWITCHER,
            'label_on' => __('Yes', 'tophive-core'),
            'label_off' => __('No', 'tophive-core'),
            'return_value' => 'yes',
            'default' => '',
        ]
    );

    $element->add_control(
        'matrix_grid_line_color',
        [
            'label' => __('Line Color', 'tophive-core'),
            'type' => Controls_Manager::COLOR,
            'default' => '#cccccc',
            'selectors' => [
                '{{WRAPPER}}.th-matrix-grid-enabled:before,
                 {{WRAPPER}}.th-matrix-grid-enabled:after,
                 {{WRAPPER}}.th-matrix-grid-enabled hr' => 'background-color: {{VALUE}};',
            ],
        ]
    );

    $element->add_control(
        'matrix_grid_dot_color',
        [
            'label' => __('Dot Color', 'tophive-core'),
            'type' => Controls_Manager::COLOR,
            'default' => '#000000',
            'selectors' => [
                '{{WRAPPER}}.th-matrix-grid-enabled .dot' => 'background-color: {{VALUE}};',
            ],
        ]
    );

    $element->end_controls_section();

}, 10, 2);
<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Scheme_Color;

// ===============================
// Matrix Grid Section
// ===============================
add_action('elementor/element/section/section_advanced/before_section_end', function($element, $args) {

    // Add a new control under Advanced tab
    $element->add_control(
        'matrix_grid_enable',
        [
            'label' => __('Enable Matrix Grid', 'tophive-core'),
            'type' => Controls_Manager::SWITCHER,
            'label_on' => __('Yes', 'tophive-core'),
            'label_off' => __('No', 'tophive-core'),
            'return_value' => 'yes',
            'default' => '',
            'description' => __('Enable dot grid background for this section', 'tophive-core'),
        ]
    );

    // Optional: Add color control for dots
    $element->add_control(
        'matrix_grid_dot_color',
        [
            'label' => __('Dot Color', 'tophive-core'),
            'type' => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .th-matrix-grid-enabled .dot' => 'background-color: {{VALUE}};',
            ],
            'condition' => [
                'matrix_grid_enable' => 'yes',
            ],
        ]
    );

    // Optional: Add line color control
    $element->add_control(
        'matrix_grid_line_color',
        [
            'label' => __('Line Color', 'tophive-core'),
            'type' => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .th-matrix-grid-enabled hr,
                 {{WRAPPER}} .th-matrix-grid-enabled:before,
                 {{WRAPPER}} .th-matrix-grid-enabled:after' => 'background-color: {{VALUE}};',
            ],
            'condition' => [
                'matrix_grid_enable' => 'yes',
            ],
        ]
    );

}, 10, 2);

// ===============================
// Render grid HTML before section
// ===============================
add_action('elementor/frontend/section/before_render', function($section) {
    $settings = $section->get_settings_for_display();

    if (!empty($settings['matrix_grid_enable']) && $settings['matrix_grid_enable'] === 'yes') {
        $section->add_render_attribute('_wrapper', 'class', 'th-matrix-grid-enabled');
        echo '<div class="dot"></div><div class="dot"></div><div class="dot"></div><div class="dot"></div>';
        echo '<hr />';
    }
});
