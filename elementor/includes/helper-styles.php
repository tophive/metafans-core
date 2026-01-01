<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

use Elementor\Core\Kits\Documents\Tabs\Global_Colors;

class Tophive_Elementor_Style_Helper {

    /**
     * Add common style controls for different UI elements.
     *
     * @param \Elementor\Widget_Base $widget
     * @param string $element_type Type of element (e.g., button, input, dropdown)
     * @param string $selector CSS selector for styles
     */
    public static function add_ui_style_controls($widget, $element_type, $selector, $hover_selector = '', $add_section = true, $section_label = '', $exclude_controls = []) {

        if ($add_section) {
            $widget->start_controls_section(
                "{$element_type}_style_section",
                [
                    'label' => $section_label ? $section_label : ucfirst($element_type) . esc_html__(' Styles', 'text-domain'),
                    'tab' => \Elementor\Controls_Manager::TAB_STYLE,
                ]
            );
        } else {
            // Just add a heading & separator before the styles
            $widget->add_control(
                "{$element_type}_heading",
                [
                    'label' => $section_label ? $section_label : ucfirst($element_type) . esc_html__(' Styles', 'text-domain'),
                    'type' => \Elementor\Controls_Manager::HEADING,
                    'separator' => 'before',
                ]
            );
        }

        switch ($element_type) {
            case 'button':
                self::add_button_styles($widget, $selector, $exclude_controls);
                break;
            case 'input':
                self::add_input_styles($widget, $selector, $exclude_controls);
                break;
            case 'text':
                self::add_text_styles($widget, $selector, $exclude_controls);
                break;
            case 'dropdown':
                self::add_dropdown_styles($widget, $selector, $exclude_controls);
                break;
            case 'box':
                self::add_box_styles($widget, $selector, $exclude_controls);
                break;
            case 'icon':
                self::add_icon_styles($widget, $selector, $hover_selector, $exclude_controls);
                break;
            case 'text_icon':
                self::add_text_with_icon_styles($widget, $selector, $hover_selector, $exclude_controls);
                break;
            case 'ripple':
                self::add_ripple_effect_styles($widget, $selector, $hover_selector, $exclude_controls);
                break;
            case 'transform':
                self::add_transform_controls($widget, $selector, $hover_selector, $exclude_controls);
                break;
            case 'animate':
                self::add_animation_controls($widget, $selector, $hover_selector, $exclude_controls);
                break;
            case 'overlay':
                self::add_overlay_controls($widget, $selector, $hover_selector, $exclude_controls);
                break;
            case 'image':
                self::add_image_styles($widget, $selector, $hover_selector, $exclude_controls);
                break;
        }

        if ($add_section) {
            $widget->end_controls_section();
        }
    }

    /**
     * Add button styles (Advanced Full Styling)
     */
    private static function add_button_styles($widget, $selector, $exclude_controls = []) {
        // Controls OUTSIDE Tabs (Typography, Margin, Border Radius, Height)
        $global_controls = [
            'position' => [
                'type' => 'choose',
                'label' => __('Position', 'elementor'),
                'options' => [
                    'left' => [
                        'title' => __('Left', 'elementor'),
                        'icon' => 'eicon-h-align-left',
                    ],
                    'center' => [
                        'title' => __('Center', 'elementor'),
                        'icon' => 'eicon-h-align-center',
                    ],
                    'right' => [
                        'title' => __('Right', 'elementor'),
                        'icon' => 'eicon-h-align-right',
                    ],
                    'justify' => [
                        'title' => __('Justify', 'elementor'),
                        'icon' => 'eicon-h-align-stretch',
                    ],
                ],
                'default' => 'center',
                'toggle' => true,
                'inside_tab' => false,
                'prefix' => 'tophive-button-',
                'selectors' => [
                    '{{WRAPPER}}' => 'text-align: {{VALUE}};',
                ],
            ],

            'typography' => [
                'type' => 'typography',
                'label' => __('Button Typography', 'text-domain'),
                'inside_tab' => false,
            ],
            'margin' => [
                'type' => 'dimensions',
                'label' => __('Margin', 'text-domain'),
                'css' => 'margin',
                'default' => ['top' => 0, 'right' => 0, 'bottom' => 0, 'left' => 0, 'unit' => 'px', 'isLinked' => false],
                'inside_tab' => false,
            ],
            'border_radius' => [
                'type' => 'slider',
                'label' => __('Border Radius', 'text-domain'),
                'css' => 'border-radius',
                'default' => ['size' => 5],
                'inside_tab' => false,
            ],
            'height' => [
                'type' => 'slider',
                'label' => __('Button Height', 'text-domain'),
                'css' => 'height',
                'default' => ['size' => 45],
                'inside_tab' => false,
            ],
        ];
    
        // Controls INSIDE Tabs (Normal, Hover, Focus)
        $tab_controls = [
            'border' => ['type' => 'border'],
            'box_shadow' => ['type' => 'box_shadow'],
            'background_color' => [
                'type' => 'background',
                'label' => __('Background Color', 'text-domain'),
                'css' => 'background-color',
                'selectors' => [
                    'normal' => "$selector, {$selector}.moveup-end::before, {$selector}.reveal-bottom::before",
                    'hover' => "{$selector}:hover, {$selector}::before, {$selector}::after"
                ],
            ],
            'text_color' => [
                'type' => 'color',
                'label' => __('Text Color', 'text-domain'),
                'css' => 'color',
                'default' => [
                    'normal' => '#FFF',
                    'hover' => '#FFF',
                    'focus' => '#555',
                ],
            ],
        ];
    
        // Define Required Tabs
        $tabs = [
            'normal' => '',
            'hover' => ':hover',
            'focus' => ':focus'
        ];
    
        // Apply Global Controls (Outside Tabs)
        self::add_dynamic_style_controls($widget, 'button', $selector, $global_controls, [], $exclude_controls);
    
        // Apply Tabbed Controls (Inside Normal, Hover, Focus Tabs)
        self::add_dynamic_style_controls($widget, 'button', $selector, $tab_controls, $tabs, $exclude_controls);
    }
    

    private static function add_input_styles($widget, $selector, $exclude_controls = []) {
        // ✅ Controls OUTSIDE Tabs (Typography, Input Height)
        $global_controls = [
            'typography' => [
                'type' => 'typography',
                'label' => __('Input Text Typography', 'your-text-domain'),
                'inside_tab' => false,
            ],
            'height' => [
                'type' => 'slider',
                'label' => __('Input Height', 'your-text-domain'),
                'css' => 'height',
                'default' => ['size' => 40, 'unit' => 'px'],
                'inside_tab' => false,
            ],
            'border_radius' => [
                'type' => 'slider',
                'label' => __('Border Radius', 'your-text-domain'),
                'css' => 'border-radius',
                'default' => ['size' => 5, 'unit' => 'px'],
                'inside_tab' => false,
            ],
        ];
    
        // ✅ Controls INSIDE Tabs (Normal, Hover, Focus)
        $tab_controls = [
            'border' => [
                'type' => 'border',
            ],
            'box_shadow' => [
                'type' => 'box_shadow',
            ],
            'placeholder_color' => [
                'type' => 'color',
                'label' => __('Placeholder Color', 'your-text-domain'),
                'css' => 'color',
                'default' => ['value' => '#555'], // ✅ Ensures proper color format
            ],
            'text_color' => [
                'type' => 'color',
                'label' => __('Text Color', 'your-text-domain'),
                'css' => 'color',
                'default' => ['value' => '#555'], // ✅ Ensures proper color format
            ],
        ];
    
        // ✅ Define Required Tabs
        $tabs = [
            'normal' => '',
            'hover' => ':hover',
            'focus' => ':focus'
        ];
    
        // ✅ Apply Global Controls (Outside Tabs)
        self::add_dynamic_style_controls($widget, 'input', $selector, $global_controls, [], $exclude_controls);
    
        // ✅ Apply Tabbed Controls (Inside Normal, Hover, Focus Tabs)
        self::add_dynamic_style_controls($widget, 'input', $selector, $tab_controls, $tabs, $exclude_controls);
    }

    private static function add_text_styles($widget, $selector, $exclude_controls = []) {
        $global_controls = [
            'typography' => [
                'type' => 'typography',
                'label' => __('Typography', 'elementor'),
                'inside_tab' => false,
            ],
            'text_margin' => [
                'type' => 'dimensions',
                'label' => __('Margin', 'elementor'),
                'css' => 'margin',
                'inside_tab' => false,
            ],
            'text_padding' => [
                'type' => 'dimensions',
                'label' => __('Padding', 'elementor'),
                'css' => 'padding',
                'inside_tab' => false,
            ],
            'text_clip_mask' => [
                'type' => 'switcher',
                'label' => __('Enable Text Masking', 'elementor'),
                'label_on' => __('Yes', 'elementor'),
                'label_off' => __('No', 'elementor'),
                'inside_tab' => false,
                'default' => '',
                'prefix' => 'tophive-masked-text-'
            ]
        ];
    
        $tab_controls = [
            'text_color' => [
                'type' => 'color',
                'label' => __('Text Color', 'elementor'),
                'css' => 'color',
                'condition' => [
                    'tophive_text_text_clip_mask_global!' => 'yes',
                ],
            ],
            'text_masked_bg' => [
                'type' => 'background',
                'label' => __('Masked Background (Gradient/Image)', 'elementor'),
                'types' => [ 'classic', 'gradient', 'image' ],
                'selectors' => [
                    'normal' => $selector,
                    'hover' => $selector . ':hover',
                ],
                'condition' => [
                    'tophive_text_text_clip_mask_global' => 'yes',
                ],
            ],
        ];
    
        $tabs = [
            'normal' => '',
            'hover' => ':hover',
        ];
    
        self::add_dynamic_style_controls($widget, 'text', $selector, $global_controls, [], $exclude_controls);
        self::add_dynamic_style_controls($widget, 'text', $selector, $tab_controls, $tabs, $exclude_controls);
    
        // ➕ Extra CSS when text masking is enabled
        $widget->add_control(
            'text_masking_note',
            [
                'type' => \Elementor\Controls_Manager::RAW_HTML,
                'raw' => '<div style="margin-top:10px;font-style:italic;opacity:0.8;">Note: When masking is enabled, text color becomes transparent and background is clipped to text.</div>',
                'content_classes' => 'elementor-control-field-description',
                'condition' => [
                    'tophive_text_text_clip_mask_global' => 'yes'
                ]
            ]
        );
    }
    
    

    private static function add_box_styles($widget, $selector, $exclude_controls = []) {
        // Controls OUTSIDE Tabs (Spacing, Margin, Border Radius)
        $global_controls = [
            'padding' => [
                'type' => 'dimensions',
                'label' => __('Spacing', 'your-text-domain'),
                'css' => 'padding',
                'inside_tab' => false, // Stays outside tabs
            ],
            'margin' => [
                'type' => 'dimensions',
                'label' => __('Margin', 'your-text-domain'),
                'css' => 'margin',
                'inside_tab' => false, // Stays outside tabs
            ],
            'border_radius' => [
                'type' => 'slider',
                'label' => __('Border Radius', 'your-text-domain'),
                'css' => 'border-radius',
                'default' => ['size' => 5],
                'inside_tab' => false, // Stays outside tabs
            ]
        ];
    
        // Controls INSIDE Tabs (Normal, Hover)
        $tab_controls = [
            'background' => [
                'type' => 'color',
                'label' => __('Background', 'your-text-domain'),
                'css' => 'background-color',
            ],
            'text_color' => [
                'type' => 'color',
                'label' => __('Text Color', 'your-text-domain'),
                'css' => 'color',
                'selector' => $selector, // Custom selector for text color
                'default' => '#000',
            ],
            'border' => [
                'type' => 'border',
            ],
            'box_shadow' => [
                'type' => 'box_shadow',
            ]
        ];
    
        // Define Tabs (Normal, Hover)
        $tabs = [
            'normal' => '',
            'hover' => ':hover'
        ];
    
        // Apply Global Controls (Outside Tabs)
        self::add_dynamic_style_controls($widget, 'box', $selector, $global_controls, [], $exclude_controls);
    
        // Apply Tabbed Controls (Inside Normal & Hover Tabs)
        self::add_dynamic_style_controls($widget, 'box', $selector, $tab_controls, $tabs, $exclude_controls);
    }

    private static function add_icon_styles($widget, $selector, $hover_selector = '', $exclude_controls = []) {
        // Controls OUTSIDE Tabs (Size, Padding, Rotate, Border Width, Border Radius)
        $global_controls = [
            'icon_size' => [
                'type' => 'slider',
                'label' => __('Size', 'elementor'),
                // 'css' => 'font-size',
                'default' => ['size' => 25, 'unit' => 'px'],
                'range' => [
                    'px' => ['max' => 500],
                    'em' => ['min' => 0, 'max' => 5],
                    'rem' => ['min' => 0, 'max' => 5],
                ],
                'inside_tab' => false, // Stays outside tabs
                'selectors' => [
                    "{$selector} i, {$selector} svg" => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}; font-size: {{SIZE}}{{UNIT}};',
                    "{$selector} img" => 'width: {{SIZE}}{{UNIT}};'
                ]
            ],
            'icon_padding' => [
                'type' => 'slider',
                'label' => __('Padding', 'elementor'),
                'css' => 'padding',
                'default' => ['size' => 10],
                'range' => [
                    'px' => ['max' => 50],
                    'em' => ['min' => 0, 'max' => 5],
                    'rem' => ['min' => 0, 'max' => 5],
                ],
                'inside_tab' => false, // Stays outside tabs
            ],
            'border_radius' => [
                'type' => 'dimensions',
                'label' => __('Border radius', 'your-text-domain'),
                'css' => 'border-radius',
                'inside_tab' => false, // Stays outside tabs
                'selectors' => [
                    "{$selector}, {$selector} img, {$selector}::before" => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
                ]
            ],
        ];
    
        // Controls INSIDE Tabs (Normal, Hover)
        $tab_controls = [
            'icon_color' => [
                'type' => 'color',
                'label' => __('Color', 'elementor'),
                'css' => 'background-color',
                'global' => ['default' => Global_Colors::COLOR_PRIMARY],
                'selectors' => [
                    'normal' => ["{$selector} i, {$selector} svg" => 'fill: {{VALUE}}; color: {{VALUE}};'],
                    'hover' => ["{$hover_selector} i, {$hover_selector} svg" => 'fill: {{VALUE}}; color: {{VALUE}};']
                ],
            ],
            'background_color' => [
                'type' => 'background',
                'label' => __('Background Color', 'elementor'),
                'selectors' => [
                    'normal' => $selector,
                    'hover' => $hover_selector
                ],
            ],
            'border' => [
                'type' => 'border',
            ],
            'box_shadow' => [
                'type' => 'box_shadow',
                'selectors' => [
                    'normal' => $selector,
                    'hover' => $hover_selector
                ],
            ],
            'hover_animation' => [
                'type' => 'hover_animation',
                'label' => __('Hover Animation', 'elementor'),
            ],
        ];
    
        // Define Tabs (Normal, Hover)
        $tabs = [
            'normal' => '',
            'hover' => !empty($hover_selector) ? '' : ':hover'
        ];
    
        // Apply Global Controls (Outside Tabs)
        self::add_dynamic_style_controls($widget, 'icon', $selector, $global_controls, [], $exclude_controls);
    
        // Apply Tabbed Controls (Inside Normal & Hover Tabs)
        self::add_dynamic_style_controls($widget, 'icon', $selector, $tab_controls, $tabs, $exclude_controls);
    }

    private static function add_text_with_icon_styles( $widget, $selector, $hover_selector = '', $exclude_controls = [] ){
        if(empty($hover_selector)){
            $hover_selector = $selector;
        }
        $global_controls = [
            'icon_size' => [
                'type' => 'slider',
                'label' => __('Icon Size', 'elementor'),
                'css' => 'font-size',
                'default' => ['size' => 24], // Default icon size
                'inside_tab' => false, // Stays outside tabs
                'selectors' => [
                    "{$selector} i, {$selector} svg" => 'font-size: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};'
                ]
            ],
            'icon_spacing' => [
                'type' => 'slider',
                'label' => __('Icon spacing', 'elementor'),
                'css' => 'font-size',
                'default' => ['size' => 5], // Default icon size
                'inside_tab' => false, // Stays outside tabs
                'range' => ['px' => ['min' => -100, 'max' => 100, 'step' => 1]],
                'selectors' => [
                    $selector => '--read-more-icon-spacing: {{SIZE}}{{UNIT}};'
                ]
            ],
            'hover_spacing' => [
                'type' => 'slider',
                'label' => __('Link hover spacing', 'elementor'),
                'css' => 'font-size',
                'default' => ['size' => 5], // Default icon size
                'inside_tab' => false, // Stays outside tabs
                'selectors' => [
                    $selector => '--read-more-link-hover-spacing: {{SIZE}}{{UNIT}};'
                ],
                'condition' => [
                    'read_more_link_icon_position' => ['left']
                ]
            ],
            'link_typography' => [
                'type' => 'typography',
                'label' => __('Text typography', 'elementor'),
                'css' => 'typography',
                'inside_tab' => false, // Stays outside tabs
                'selectors' => $selector
            ],
            'margin' => [
                'type' => 'dimensions',
                'label' => __('Spacing', 'text-domain'),
                'css' => 'margin',
                'default' => ['top' => 0, 'right' => 0, 'bottom' => 10, 'left' => 0, 'unit' => 'px', 'isLinked' => false],
                'inside_tab' => false,
            ],
        ];
    
        // Controls INSIDE Tabs (Normal, Hover)
        $tab_controls = [
            'icon_color' => [
                'type' => 'color',
                'label' => __('Icon Color', 'elementor'),
                'css' => 'color',
                'global' => ['default' => Global_Colors::COLOR_PRIMARY],
                'selectors' => [
                    'normal' => ["{$selector} i, {$selector} svg" => 'fill: {{VALUE}}; color: {{VALUE}};'],
                    'hover' => ["{$hover_selector} i, {$hover_selector} svg" => 'fill: {{VALUE}}; color: {{VALUE}};']
                ],
            ],
            'text_color' => [
                'type' => 'color',
                'label' => __('Color', 'elementor'),
                'css' => 'color',
                'selectors' => [
                    'normal' => [$selector => 'color: {{VALUE}};'],
                    'hover' => [$hover_selector => 'color: {{VALUE}};']
                ],
            ],
        ];
    
        // Define Tabs (Normal, Hover)
        $tabs = [
            'normal' => '',
            'hover' => !empty($hover_selector) ? '' : ':hover'
        ];
    
        // Apply Global Controls (Outside Tabs)
        self::add_dynamic_style_controls($widget, 'text_icon', $selector, $global_controls, [], $exclude_controls);
    
        // Apply Tabbed Controls (Inside Normal & Hover Tabs)
        self::add_dynamic_style_controls($widget, 'text_icon', $selector, $tab_controls, $tabs, $exclude_controls);
    }
    
    private static function add_ripple_effect_styles( $widget, $selector, $hover_selector = '', $exclude_controls = []){
        $controls = [
            "custom_switcher"  => [
                'type' => 'switcher',
                'label' => __('Enable ripple effect', 'elementor'),
                'default' => 'no',
                'inside_tab' => false, // Stays outside tabs
            ],
    
            'custom_select' => [
                'type' => 'select',
                'label' => __('Effect condition', 'elementor'),
                'options' => [
                    'normal' => __('On normal', 'elementor'),
                    'hover' => __('On Hover', 'elementor'),
                    'focus' => __('On focus', 'elementor'),
                ],
                'inside_tab' => false, // Stays outside tabs
                'default' => 'normal',
                'prefix' => 'tophive-ripple-effect tophive-ripple-effect-',
                'condition' => [ "tophive_ripple_custom_switcher_global" => 'yes' ], // ✅ FIXED!
            ],
            'custom_select_scope' => [
                'type' => 'select',
                'label' => __('Scope', 'elementor'),
                'options' => [
                    'inside' => __('Inside', 'elementor'),
                    'outside' => __('Outside', 'elementor'),
                ],
                'inside_tab' => false, // Stays outside tabs
                'default' => 'outside',
                'prefix' => 'tophive-ripple-effect-',
                'condition' => [ "tophive_ripple_custom_switcher_global" => 'yes' ], // ✅ FIXED!
            ],
            'ripple_color' => [
                'type' => 'color',
                'label' => __('Color', 'elementor'),
                'css' => 'color',
                'global' => ['default' => Global_Colors::COLOR_PRIMARY],
                'inside_tab' => false, // Stays outside tabs
                'condition' => [ "tophive_ripple_custom_switcher_global" => 'yes' ], // ✅ FIXED!
                'selectors' => [
                    "{{WRAPPER}}" => '--tophive-ripple-color: {{VALUE}} !important;',
                ],
            ],
            'transition_duration' => [
                'type' => 'slider',
                'label' => __('Transition duration(in seconds)', 'elementor'),
                'default' => ['size' => 2],
                'range' => ['px' => ['min' => 0.1, 'max' => 4, 'step' => 0.1]],
                'inside_tab' => false,
                'condition' => [ "tophive_ripple_custom_switcher_global" => 'yes' ],
                'selectors' => [
                    "{{WRAPPER}}" => '--tophive-ripple-animation-duration: {{SIZE}}s;'
                ]
            ],
            'ripple_spread' => [
                'type' => 'slider',
                'label' => __('Ripple Spread', 'elementor'),
                'default' => ['size' => 1.4],
                'range' => ['px' => ['min' => 1, 'max' => 3, 'step' => 0.1]],
                'inside_tab' => false,
                'condition' => [ "tophive_ripple_custom_switcher_global" => 'yes' ],
                'selectors' => [
                    '{{WRAPPER}}' => '--tophive-ripple-growth-field: {{SIZE}};',
                ],
            ],
        ];
    
        self::add_dynamic_style_controls($widget, 'ripple', $selector, $controls, [], $exclude_controls);
    }
    
    private static function add_transform_controls($widget, $selector, $hover_selector = '', $exclude_controls = []) {
        $controls = [
            "transform_switcher"  => [
                'type' => 'switcher',
                'label' => __('Enable transform effect', 'elementor'),
                'default' => 'no',
                'inside_tab' => false, // Stays outside tabs
            ],
    
            'transform_target' => [
                'type' => 'select',
                'label' => __('Transform target', 'elementor'),
                'options' => [
                    'icon' => __('Only icon', 'elementor'),
                    'icon-box' => __('Full box', 'elementor'),
                ],
                'inside_tab' => false, // Stays outside tabs
                'default' => 'icon-box',
                'prefix' => 'tophive-transform-effect-',
                'condition' => [ "tophive_transform_transform_switcher_global" => 'yes' ],
            ],
            'transform_action' => [
                'type' => 'select',
                'label' => __('Transform action', 'elementor'),
                'options' => [
                    'normal' => __('Normal', 'elementor'),
                    'both' => __('Normal & Hover', 'elementor'),
                ],
                'inside_tab' => false, // Stays outside tabs
                'default' => 'both',
                'prefix' => 'tophive-transform-action-',
                'condition' => [ "tophive_transform_transform_switcher_global" => 'yes' ],
            ],
    
        ];
        $tab_controls = [
            'translateX' => [
                'type' => 'slider',
                'label' => __('Translate X', 'elementor'),
                'css' => 'transform',
                'selectors' => [
                    'normal' => [
                        "{{WRAPPER}}" => '--tophive-transform-translatex: {{SIZE}}{{UNIT}};'
                    ],
                    'hover' => [
                        "{{WRAPPER}}" => '--tophive-transform-translatex-hover: {{SIZE}}{{UNIT}};'
                    ]
                ],
                'default' => ['size' => 0, 'unit' => 'px'],
                'range' => ['px' => ['min' => -200, 'max' => 200, 'step' => 1]],
                'condition' => [ "tophive_transform_transform_switcher_global" => 'yes' ],
            ],
            'translateY' => [
                'type' => 'slider',
                'label' => __('Translate Y', 'elementor'),
                'css' => 'transform',
                'selectors' => [
                    'normal' => [
                        "{{WRAPPER}}" => '--tophive-transform-translatey: {{SIZE}}{{UNIT}};'
                    ],
                    'hover' => [
                        "{{WRAPPER}}" => '--tophive-transform-translatey-hover: {{SIZE}}{{UNIT}};'
                    ]
                ],
                'default' => ['size' => 0, 'unit' => 'px'],
                'range' => ['px' => ['min' => -200, 'max' => 200, 'step' => 1]],
                'condition' => [ "tophive_transform_transform_switcher_global" => 'yes' ],
            ],
            'scale' => [
                'type' => 'slider',
                'label' => __('Scale', 'elementor'),
                'css' => 'transform',
                'selectors' => [
                    'normal' => [
                        "{{WRAPPER}}" => '--tophive-transform-scale: {{SIZE}};'
                    ],
                    'hover' => [
                        "{{WRAPPER}}" => '--tophive-transform-scale-hover: {{SIZE}};'
                    ]
                ],
                'default' => ['size' => 1],
                'range' => ['px' => ['min' => 0.5, 'max' => 3, 'step' => 0.1]],
                'condition' => [ "tophive_transform_transform_switcher_global" => 'yes' ],
            ],
            'rotate' => [
                'type' => 'slider',
                'label' => __('Rotate', 'elementor'),
                'css' => 'transform',
                'selectors' => [
                    'normal' => [
                        "{{WRAPPER}}" => '--tophive-transform-rotate: {{SIZE}}{{UNIT}};'
                    ],
                    'hover' => [
                        "{{WRAPPER}}" => '--tophive-transform-rotate-hover: {{SIZE}}{{UNIT}};'
                    ]
                ],
                'default' => ['size' => 0, 'unit' => 'deg'],
                'range' => ['deg' => ['min' => -180, 'max' => 180, 'step' => 1]],
                'condition' => [ "tophive_transform_transform_switcher_global" => 'yes' ],
            ],
            'skew' => [
                'type' => 'slider',
                'label' => __('Skew', 'elementor'),
                'css' => 'transform',
                'selectors' => [
                    'normal' => [
                        "{{WRAPPER}}" => '--tophive-transform-skew: {{SIZE}}{{UNIT}};'
                    ],
                    'hover' => [
                        "{{WRAPPER}}" => '--tophive-transform-skew-hover: {{SIZE}}{{UNIT}};'
                    ]
                ],
                'default' => ['size' => 0, 'unit' => 'deg'],
                'range' => ['deg' => ['min' => -50, 'max' => 50, 'step' => 1]],
                'condition' => [ "tophive_transform_transform_switcher_global" => 'yes' ],
            ],
        ];

        $tabs = [];
        
    
        // ✅ Define Required Tabs
        $tabs['normal'] = '';
        if( false !== $hover_selector ){
            $tabs['hover'] = ':hover';
        }

        self::add_dynamic_style_controls($widget, 'transform', $selector, $controls, [], $exclude_controls);
    
        // ✅ Apply Tabbed Controls (Inside Normal & Hover Tabs)
        self::add_dynamic_style_controls($widget, 'transform', $selector, $tab_controls, $tabs, $exclude_controls);
    }

    private static function add_animation_controls($widget, $selector, $hover_selector = '', $exclude_controls = []) {
        $controls = [
            "position_animation_switcher"  => [
                'type' => 'switcher',
                'label' => __('Enable animation', 'elementor'),
                'default' => '',
                'seperator' => 'after',
                'prefix' => 'tophive-object-animate-position ',
                'inside_tab' => false, // Stays outside tabs
            ],
        ];
    
        self::add_dynamic_style_controls($widget, 'animation', $selector, $controls, [], $exclude_controls);
    
        // Position Animate
        self::add_position_animation_controls($widget, $selector, $exclude_controls);
        

        // Shadow Animate

        $controls_shadow = [
            "shadow_animation_switcher"  => [
                'type' => 'switcher',
                'label' => __('Enable Shadow animation', 'elementor'),
                'default' => '',
                'prefix' => 'tophive-object-animate-shadow ',
                'inside_tab' => false, // Stays outside tabs
            ],
        ];
        self::add_dynamic_style_controls($widget, 'animation_shadow', $selector, $controls_shadow, [], $exclude_controls);
        self::add_shadow_animation_controls($widget, $selector, $exclude_controls);
    
        // Background Animate
        $controls_bg = [
            "background_animation_switcher"  => [
                'type' => 'switcher',
                'label' => __('Enable Gradient animation', 'elementor'),
                'default' => '',
                'prefix' => 'tophive-object-animate-background ',
                'inside_tab' => false, // Stays outside tabs
            ],
        ];
    
        self::add_dynamic_style_controls($widget, 'animation_bg', $selector, $controls_bg, [], $exclude_controls);
        self::add_background_animation_controls($widget, $selector, $exclude_controls);
    }
    
    private static function add_position_animation_controls($widget, $selector, $exclude_controls = []) {
        $position_controls = [
            'translateX' => [
                'type' => 'slider',
                'label' => __('Translate X', 'elementor'),
                'selectors' => [
                    'from' => ["{{WRAPPER}}" => '--tophive-animate-translatex-from: {{SIZE}}{{UNIT}};'],
                    'to' => ["{{WRAPPER}}" => '--tophive-animate-translatex-to: {{SIZE}}{{UNIT}};']
                ],
                'default' => ['size' => 0, 'unit' => 'px'],
                'condition' => [ "tophive_animation_position_animation_switcher_global" => 'yes' ],
            ],
            'translateY' => [
                'type' => 'slider',
                'label' => __('Translate Y', 'elementor'),
                'selectors' => [
                    'from' => ["{{WRAPPER}}" => '--tophive-animate-translatey-from: {{SIZE}}{{UNIT}};'],
                    'to' => ["{{WRAPPER}}" => '--tophive-animate-translatey-to: {{SIZE}}{{UNIT}};']
                ],
                'default' => ['size' => 0, 'unit' => 'px'],
                'condition' => [ "tophive_animation_position_animation_switcher_global" => 'yes' ],
            ],
            'scale' => [
                'type' => 'slider',
                'label' => __('Scale', 'elementor'),
                'selectors' => [
                    'from' => ["{{WRAPPER}}" => '--tophive-animate-scale-from: {{SIZE}};'],
                    'to' => ["{{WRAPPER}}" => '--tophive-animate-scale-to: {{SIZE}};']
                ],
                'default' => ['size' => 1, 'unit' => ''],
                'range' => ['px' => ['min' => 0.5, 'max' => 2, 'step' => 0.1]],
                'condition' => [ "tophive_animation_position_animation_switcher_global" => 'yes' ],
            ],
            'rotate' => [
                'type' => 'slider',
                'label' => __('Rotate', 'elementor'),
                'selectors' => [
                    'from' => ["{{WRAPPER}}" => '--tophive-animate-rotate-from: {{SIZE}}{{UNIT}};'],
                    'to' => ["{{WRAPPER}}" => '--tophive-animate-rotate-to: {{SIZE}}{{UNIT}};']
                ],
                'default' => ['size' => 0, 'unit' => 'deg'],
                'range' => ['deg' => ['min' => -180, 'max' => 180, 'step' => 1]],
                'condition' => [ "tophive_animation_position_animation_switcher_global" => 'yes' ],
            ],
            'skew' => [
                'type' => 'slider',
                'label' => __('Skew', 'elementor'),
                'selectors' => [
                    'from' => ["{{WRAPPER}}" => '--tophive-animate-skew-from: {{SIZE}}{{UNIT}};'],
                    'to' => ["{{WRAPPER}}" => '--tophive-animate-skew-to: {{SIZE}}{{UNIT}};']
                ],
                'default' => ['size' => 0, 'unit' => 'deg'],
                'range' => ['deg' => ['min' => -50, 'max' => 50, 'step' => 1]],
                'condition' => [ "tophive_animation_position_animation_switcher_global" => 'yes' ],
            ],
            'opacity' => [
                'type' => 'slider',
                'label' => __('Opacity', 'elementor'),
                'selectors' => [
                    'from' => ["{{WRAPPER}}" => '--tophive-animate-opacity-from: {{SIZE}};'],
                    'to' => ["{{WRAPPER}}" => '--tophive-animate-opacity-to: {{SIZE}};']
                ],
                'default' => ['size' => 1],
                'range' => ['px' => ['min' => 0, 'max' => 1, 'step' => 0.05]],
                'condition' => [ "tophive_animation_position_animation_switcher_global" => 'yes' ],
            ],
        ];
    
        $animation_settings = [
            'duration' => [
                'type' => 'slider',
                'label' => __('Animation Duration', 'elementor'),
                'selectors' => ["{{WRAPPER}}" => '--tophive-animate-duration: {{SIZE}}s;'],
                'default' => ['size' => 1],
                'range' => ['px' => ['min' => 0.1, 'max' => 10, 'step' => 0.1]],
                'inside_tab' => false,
                'condition' => [ "tophive_animation_position_animation_switcher_global" => 'yes' ],
            ],
            'timing_function' => [
                'type' => 'select',
                'label' => __('Timing Function', 'elementor'),
                'options' => [
                    'ease' => 'Ease',
                    'linear' => 'Linear',
                    'ease-in' => 'Ease-In',
                    'ease-out' => 'Ease-Out',
                    'ease-in-out' => 'Ease-In-Out',
                ],
                'default' => 'ease',
                'inside_tab' => false,
                'condition' => [ "tophive_animation_position_animation_switcher_global" => 'yes' ],
            ],
            'delay' => [
                'type' => 'slider',
                'label' => __('Animation Delay', 'elementor'),
                'selectors' => ["{{WRAPPER}}" => '--tophive-animate-delay: {{SIZE}}s;'],
                'default' => ['size' => 0],
                'range' => ['px' => ['min' => 0, 'max' => 5, 'step' => 0.1]],
                'inside_tab' => false, // Stays outside tabs
                'condition' => [ "tophive_animation_position_animation_switcher_global" => 'yes' ],
            ],
            'animate_iteration' => [
                'type' => 'select',
                'label' => __('Iteration Count', 'elementor'),
                'options' => [
                    '1' => __('1 Time', 'elementor'),
                    '2' => __('2 Times', 'elementor'),
                    '3' => __('3 Times', 'elementor'),
                    '5' => __('5 Times', 'elementor'),
                    'infinite' => __('Infinite', 'elementor'),
                ],
                'default' => '1',
                'selectors' => [
                    '{{WRAPPER}}' => '--tophive-animate-iteration: {{VALUE}};',
                ],
                'inside_tab' => false, // Stays outside tabs
                'condition' => [ "tophive_animation_position_animation_switcher_global" => 'yes' ],
            ],
        ];
    
        $tabs = [
            'from' => '',
            'to' => ''
        ];
    
        self::add_dynamic_style_controls($widget, 'position_animate', $selector, $position_controls, $tabs, $exclude_controls);
        self::add_dynamic_style_controls($widget, 'animation_settings', $selector, $animation_settings, [], $exclude_controls);
    }
    private static function add_shadow_animation_controls($widget, $selector, $exclude_controls = []) {
        $shadow_controls = [
            'shadow_distance' => [
                'type' => 'slider',
                'label' => __('Shadow Distance (Bottom)', 'hub-elementor-addons'),
                'selectors' => [
                    'from' => [ '{{WRAPPER}}' => '--tophive-animated-shadow-distance-from: {{SIZE}}{{UNIT}};' ],
                    'to'   => [ '{{WRAPPER}}' => '--tophive-animated-shadow-distance-to: {{SIZE}}{{UNIT}};' ],
                ],
                'default' => [ 'size' => -65, 'unit' => 'px' ],
                'range' => [ 'px' => [ 'min' => -200, 'max' => 200, 'step' => 1 ] ],
                'condition' => [ 'tophive_animation_shadow_shadow_animation_switcher_global' => 'yes' ],
            ],
            'shadow_width' => [
                'type' => 'slider',
                'label' => __('Shadow Width (%)', 'hub-elementor-addons'),
                'selectors' => [
                    'from' => [ '{{WRAPPER}}' => '--tophive-animated-shadow-width-from: {{SIZE}}%;' ],
                    'to'   => [ '{{WRAPPER}}' => '--tophive-animated-shadow-width-to: {{SIZE}}%;' ],
                ],
                'default' => [ 'size' => 70 ],
                'range' => [ 'px' => [ 'min' => 0, 'max' => 100, 'step' => 1 ] ],
                'condition' => [ 'tophive_animation_shadow_shadow_animation_switcher_global' => 'yes' ],
            ],
            'shadow_height' => [
                'type' => 'slider',
                'label' => __('Shadow Height (px)', 'hub-elementor-addons'),
                'selectors' => [
                    'from' => [ '{{WRAPPER}}' => '--tophive-animated-shadow-height-from: {{SIZE}}px;' ],
                    'to'   => [ '{{WRAPPER}}' => '--tophive-animated-shadow-height-to: {{SIZE}}px;' ],
                ],
                'default' => [ 'size' => 40 ],
                'range' => [ 'px' => [ 'min' => 0, 'max' => 200, 'step' => 1 ] ],
                'condition' => [ 'tophive_animation_shadow_shadow_animation_switcher_global' => 'yes' ],
            ],
            'shadow_blur' => [
                'type' => 'slider',
                'label' => __('Shadow Blur (px)', 'hub-elementor-addons'),
                'selectors' => [
                    'from' => [ '{{WRAPPER}}' => '--tophive-animated-shadow-blur-from: {{SIZE}}px;' ],
                    'to'   => [ '{{WRAPPER}}' => '--tophive-animated-shadow-blur-to: {{SIZE}}px;' ],
                ],
                'default' => [ 'size' => 20 ],
                'range' => [ 'px' => [ 'min' => 0, 'max' => 100, 'step' => 1 ] ],
                'condition' => [ 'tophive_animation_shadow_shadow_animation_switcher_global' => 'yes' ],
            ],
            'shadow_radius' => [
                'type' => 'slider',
                'label' => __('Shadow Border Radius (%)', 'hub-elementor-addons'),
                'selectors' => [
                    'from' => [ '{{WRAPPER}}' => '--tophive-animated-shadow-radius-from: {{SIZE}}%;' ],
                    'to'   => [ '{{WRAPPER}}' => '--tophive-animated-shadow-radius-to: {{SIZE}}%;' ],
                ],
                'default' => [ 'size' => 50 ],
                'range' => [ 'px' => [ 'min' => 0, 'max' => 100, 'step' => 1 ] ],
                'condition' => [ 'tophive_animation_shadow_shadow_animation_switcher_global' => 'yes' ],
            ],
            'color' => [
                'type' => 'color',
                'label' => __('Shadow color', 'elementor'),
                'selectors' => [
                    'from' => [ '{{WRAPPER}}' => '--tophive-animated-shadow-color-from: {{VALUE}};' ],
                    'to'   => [ '{{WRAPPER}}' => '--tophive-animated-shadow-color-to: {{VALUE}};' ],
                ],
                'condition' => ["tophive_animation_shadow_shadow_animation_switcher_global" => 'yes'],
            ],
        ];
        
    
        $tabs = [
            'from' => '',
            'to' => ''
        ];
    
        self::add_dynamic_style_controls($widget, 'box_shadow', $selector, $shadow_controls, $tabs, $exclude_controls);
    }

    private static function add_background_animation_controls($widget, $selector, $exclude_controls = []) {
        $background_controls = [
            'bg_color_1' => [
            'type' => 'color',
            'label' => __('Background Color 1', 'elementor'),
            'selectors' => ["{{WRAPPER}}" => '--tophive-bg-color-1: {{VALUE}};'],
            'inside_tab' => false,
            'default' => '#FFA63D',
            'condition' => ["tophive_animation_bg_background_animation_switcher_global" => 'yes'],
        ],
        'bg_color_2' => [
            'type' => 'color',
            'label' => __('Background Color 2', 'elementor'),
            'selectors' => ["{{WRAPPER}}" => '--tophive-bg-color-2: {{VALUE}};'],
            'inside_tab' => false,
            'default' => '#FF3D77',
            'condition' => ["tophive_animation_bg_background_animation_switcher_global" => 'yes'],
        ],
        'bg_color_3' => [
            'type' => 'color',
            'label' => __('Background Color 3', 'elementor'),
            'selectors' => ["{{WRAPPER}}" => '--tophive-bg-color-3: {{VALUE}};'],
            'inside_tab' => false,
            'default' => '#338AFF',
            'condition' => ["tophive_animation_bg_background_animation_switcher_global" => 'yes'],
        ],
        'bg_color_4' => [
            'type' => 'color',
            'label' => __('Background Color 4', 'elementor'),
            'selectors' => ["{{WRAPPER}}" => '--tophive-bg-color-4: {{VALUE}};'],
            'inside_tab' => false,
            'default' => '#3CF0C5',
            'condition' => ["tophive_animation_bg_background_animation_switcher_global" => 'yes'],
        ],
        'bg_speed' => [
            'type' => 'slider',
            'label' => __('Background Animation Speed', 'elementor'),
            'selectors' => ["{{WRAPPER}}" => '--tophive-bg-speed: {{SIZE}}s;'],
            'default' => ['size' => 16],
            'range' => ['px' => ['min' => 1, 'max' => 20, 'step' => 0.5]],
            'inside_tab' => false,
            'condition' => ["tophive_animation_bg_background_animation_switcher_global" => 'yes'],
        ],
        'gradient_angle' => [
            'type' => 'slider',
            'label' => __('Gradient angle', 'elementor'),
            'selectors' => ["{{WRAPPER}}" => '--tophive-gradient-angle: {{SIZE}}deg;'],
            'default' => ['size' => 45],
            'range' => ['deg' => ['min' => -180, 'max' => 180, 'step' => 1]],
            'inside_tab' => false,
            'condition' => ["tophive_animation_bg_background_animation_switcher_global" => 'yes'],
        ],

        ];
    
        self::add_dynamic_style_controls($widget, 'background_animate', $selector, $background_controls, [], $exclude_controls);
    }
    
    public static function add_overlay_controls($widget, $selector, $hover_selector='', $exclude_controls = []){
        $controls = [
            "enable_overlay_bg"  => [
                'type' => 'switcher',
                'label' => __('Add overlay background', 'elementor'),
                'default' => 'no',
                'inside_tab' => false, // Stays outside tabs
                'prefix' => 'overlay-bg-'
            ],
            'overlay_bg_color' => [
                'type' => 'background',
                'label' => __('Background Color', 'text-domain'),
                'css' => 'background-color',
                'inside_tab' => false, // Stays outside tabs
                'selectors' => $selector,
                'condition' => [
                    'tophive_overlay_enable_overlay_bg_global' => 'yes',
                ],
            ],
            "overlay_show_on_hover"  => [
                'type' => 'switcher',
                'label' => __('Show on hover', 'elementor'),
                'default' => 'no',
                'inside_tab' => false, // Stays outside tabs
                'prefix' => 'overlay-show-onhover ',
                'condition' => [
                    'tophive_overlay_enable_overlay_bg_global' => 'yes',
                ],
            ],
        ];
        self::add_dynamic_style_controls($widget, 'overlay', $selector, $controls, [], $exclude_controls);
    }

    private static function add_image_styles($widget, $selector, $hover_selector='', $exclude_controls = []){
        $tab_controls = [
            'img_opacity' => [
                'type' => 'slider',
                'label' => __('Opacity', 'elementor'),
                'css' => 'opacity',
                'selectors' => [
                    'normal' => [
                        "{$selector}" => 'opacity: {{SIZE}};'
                    ],
                    'hover' => [
                        "{$selector}:hover" => 'opacity: {{SIZE}};'
                    ]
                ],
                'default' => ['size' => 1, 'unit' => 'px'],
                'range' => ['px' => ['min' => 0, 'max' => 1, 'step' => 0.01]],
            ],
            'img_radius' => [
                'type' => 'slider',
                'label' => __('Border radius', 'elementor'),
                'css' => 'border-radius',
                'default' => ['size' => 1, 'unit' => 'px'],
                'range' => ['px' => ['min' => 0, 'max' => 100, 'step' => 1]],
            ],
            'box_shadow' => ['type' => 'box_shadow'],
            'img_gray_scale' => [
                'type' => 'slider',
                'label' => __('Gray scale', 'elementor'),
                'css' => 'filter',
                'selectors' => [
                    'normal' => [
                        "{$selector}" => 'filter: grayscale({{SIZE}});'
                    ],
                    'hover' => [
                        "{$selector}:hover" => 'filter: grayscale({{SIZE}});'
                    ]
                ],
                'default' => ['size' => 0, 'unit' => 'px'],
                'range' => ['px' => ['min' => 0, 'max' => 1, 'step' => 0.01]],
            ],
        ];
        $tabs = [
            'normal' => '',
            'hover' => ':hover'
        ];
        self::add_dynamic_style_controls($widget, 'image', $selector, $tab_controls, $tabs, $exclude_controls);
    }
    
    
    public static function add_dynamic_style_controls($widget, $section_id, $selector, $controls = [], $tabs = [], $exclude_controls = []) {
        if (!empty($tabs)) {
            // Start Normal, Hover, Focus Tabs
            $widget->start_controls_tabs("tophive_{$section_id}_tabs");
    
            foreach ($tabs as $state => $pseudo) {
                $widget->start_controls_tab("tophive_{$section_id}_{$state}_tab", [
                    'label' => ucfirst($state),
                ]);
    
                self::add_controls_from_array($widget, $section_id, $selector, $controls, $pseudo, $state, true, $exclude_controls);
    
                $widget->end_controls_tab();
            }
    
            $widget->end_controls_tabs();
        }
    
        // Add global controls (that are not inside tabs)
        self::add_controls_from_array($widget, $section_id, $selector, $controls, '', 'global', false, $exclude_controls);
    }
    private static function add_controls_from_array($widget, $section_id, $selector, $controls, $pseudo = '', $state = 'normal', $inside_tab = false, $exclude_controls = []) {
        foreach ($controls as $name => $control) {

            
            // Skip excluded controls
            if (in_array($name, $exclude_controls, true)) {
                continue;
            }
            
            // Skip controls inside/outside tabs based on their settings
            if ($inside_tab && !($control['inside_tab'] ?? true)) {
                continue;
            }
            if (!$inside_tab && ($control['inside_tab'] ?? true)) {
                continue;
            }
            
    
            $selectors = [];

            // ✅ Ensure selectors are processed correctly
            if (!empty($control['selectors']) && is_array($control['selectors'])) {
                // ✅ Check if this control has state-specific selectors
                if (!empty($control['selectors'][$state]) && is_array($control['selectors'][$state])) {
                    foreach ($control['selectors'][$state] as $key => $value) {
                        $selectors["{$key}{$pseudo}"] = self::format_css_value($control['type'], $value);
                    }
                } else {
                    // ✅ Use the default selectors if no state-specific selector is defined
                    foreach ($control['selectors'] as $key => $value) {
                        $selectors["{$key}{$pseudo}"] = self::format_css_value($control['type'], $value);
                    }
                }
            } elseif (!empty($control['css'])) {
                // ✅ Default CSS structure (handles sliders, dimensions, etc.)
                $selectors = ["{$selector}{$pseudo}" => self::format_css_value($control['type'], $control['css'])];
            }

            // ✅ Ensure selectors output correctly (fallback mechanism)
            if (empty($selectors)) {
                $selectors = ["{$selector}{$pseudo}" => self::format_css_value($control['type'], $control['css'] ?? '')];
            }

            // ✅ Ensure `selectors` is always an **array of strings**
            if (!is_array($selectors)) {
                $selectors = [$selectors];
            }
            foreach ($selectors as $key => $value) {
                if (is_array($value)) {
                    unset($selectors[$key]); // Remove invalid array values
                }
            }


    
            // ✅ Ensure `default` is properly structured for Elementor
            $default_value = $control['default'] ?? '';
    
            // **Fix for Color Controls (Ensure it’s a String, not an Array)**
            if ($control['type'] === 'color') {
                if (!is_array($default_value)) {
                    $default_value = ['value' => $default_value ?: '']; // Ensure `'value'` key exists
                } elseif (!isset($default_value['value'])) {
                    $default_value['value'] = ''; // Prevent Elementor warning
                }
            }
    
            // **Fix for Slider Controls (Ensure `'size'` and `'unit'` exist)**
            if ($control['type'] === 'slider') {
                if (!is_array($default_value)) {
                    $default_value = ['size' => is_numeric($default_value) ? $default_value : 5, 'unit' => 'px'];
                }
                if (!isset($default_value['size'])) {
                    $default_value['size'] = 5;
                }
                if (!isset($default_value['unit'])) {
                    $default_value['unit'] = 'px';
                }
            }
    
            // **Fix for Dimensions Controls (Ensure all keys exist)**
            if ($control['type'] === 'dimensions') {
                if (!is_array($default_value)) {
                    $default_value = ['top' => 0, 'right' => 0, 'bottom' => 0, 'left' => 0, 'unit' => 'px', 'isLinked' => false];
                } else {
                    $default_value = array_merge(
                        ['top' => 0, 'right' => 0, 'bottom' => 0, 'left' => 0, 'unit' => 'px', 'isLinked' => false],
                        $default_value
                    );
                }
            }
    
            // **Fix for Typography Controls (Ensure it's an Array)**
            if ($control['type'] === 'typography' && !is_array($default_value)) {
                $default_value = [];
            }

            $widget_id = $widget->get_id();

            // ✅ Apply Controls Properly
            switch ($control['type']) {
                case 'color':
                    $widget->add_control(
                        "tophive_{$section_id}_{$name}_{$state}",
                        [
                            'label' => $control['label'],
                            'type' => \Elementor\Controls_Manager::COLOR,
                            'selectors' => $selectors, // ✅ Always an array of strings
                            'default' => $default_value['value'], // ✅ Ensure it's a string
                            'condition' => $control['condition'] ?? [],
                        ]
                    );
                    break;
                case 'choose':
                    $widget->add_control(
                        "tophive_{$section_id}_{$name}_{$state}",
                        [
                            'label' => $control['label'],
                            'type' => \Elementor\Controls_Manager::CHOOSE,
                            'options' => $control['options'] ?? [],
                            'default' => $default_value,
                            'toggle' => $control['toggle'] ?? false,
                            'selectors' => $control['selectors'] ?? [],
                            'prefix_class' => $control['prefix'] ?? []
                        ]
                    );
                    break;
                case 'background':
                    $widget->add_group_control(
                        \Elementor\Group_Control_Background::get_type(),
                        [
                            'name' => "tophive_{$section_id}_background_{$state}",
                            'label' => $control['label'] ?? 'Background type',
                            'types' => $control['types'] ?? [ 'classic', 'gradient'],
                            'selector' => isset($control['selectors'][$state]) 
                                ? $control['selectors'][$state]
                                : $selector,
                            'condition' => $control['condition'] ?? []
                        ]
                    );
                    break;
    
                case 'border':
                    $widget->add_group_control(
                        \Elementor\Group_Control_Border::get_type(),
                        [
                            'name' => "tophive_{$section_id}_border_{$state}",
                            'selector' => key($selectors) ?: "{$selector}{$pseudo}",
                        ]
                    );
                    break;
    
                case 'box_shadow':
                    $box_shadow_selector = isset($control['selectors'][$state])
                        ? $control['selectors'][$state]
                        : "{$selector}{$pseudo}";
                
                    $widget->add_group_control(
                        \Elementor\Group_Control_Box_Shadow::get_type(),
                        [
                            'name' => "tophive_{$section_id}_box_shadow_{$state}",
                            'selector' => $box_shadow_selector,
                            'condition' => $control['condition'] ?? []
                        ]
                    );
                    break;
    
                case 'dimensions':
                    $widget->add_responsive_control(
                        "tophive_{$section_id}_{$name}_{$state}",
                        [
                            'label' => $control['label'],
                            'type' => \Elementor\Controls_Manager::DIMENSIONS,
                            'size_units' => ['px', 'em', '%'],
                            'default' => $default_value, // ✅ Ensured correct structure
                            'selectors' => $selectors,
                        ]
                    );
                    break;
    
                case 'slider':
                    $widget->add_control(
                        "tophive_{$section_id}_{$name}_{$state}",
                        [
                            'label' => $control['label'],
                            'type' => \Elementor\Controls_Manager::SLIDER,
                            'size_units' => ['px'],
                            'range' => $control['range'] ?? ['px' => ['min' => 0, 'max' => 100, 'step' => 1]],
                            'default' => $default_value,
                            'condition' => $control['condition'] ?? [],
                            'selectors' => isset($control['selectors'][$state]) 
                                ? $control['selectors'][$state] : $selectors,
                        ]
                    );
                    break;
    
                case 'typography':
                    $widget->add_group_control(
                        \Elementor\Group_Control_Typography::get_type(),
                        [
                            'name' => "tophive_{$section_id}_typography_{$state}",
                            'label' => $control['label'],
                            'selector' => key($selectors) ?: "{$selector}{$pseudo}",
                        ]
                    );
                    break;

                case 'switcher':
                    $widget->add_control(
                        "tophive_{$section_id}_{$name}_{$state}",
                        [
                            'label' => $control['label'],
                            'type' => \Elementor\Controls_Manager::SWITCHER,
                            'label_on' => __('Yes', 'elementor'),
                            'label_off' => __('No', 'elementor'),
                            'return_value' => 'yes',
                            'seperator' => $control['seperator'] ?? '',
                            'prefix_class' => $control['prefix'] ?? '',
                            'default' => $control['default'] ?? 'no',
                            'condition' => $control['condition'] ?? []
                        ]
                    );
                    break;

                case 'select':
                    $widget->add_control(
                        "tophive_{$section_id}_{$name}_{$state}",
                        [
                            'label' => $control['label'],
                            'type' => \Elementor\Controls_Manager::SELECT,
                            'options' => $control['options'] ?? [],
                            'default' => $control['default'] ?? '',
                            'prefix_class' => $control['prefix'] ?? '',
                            'condition' => $control['condition'] ?? [], // ✅ Enables dynamic visibility
                        ]
                    );
                    break;

            }
        }
    }
    private static function format_css_value($control_type, $css_rule) {
        switch ($control_type) {
            case 'color':
                return "{$css_rule}: {{VALUE}};";
            case 'background':
                return "";
            case 'typography':
                return "";
            case 'transform': 
                return "{{VALUE}}"; // No default formatting, custom usage        
            case 'slider':
                return "{$css_rule}: {{SIZE}}{{UNIT}};";
            case 'dimensions':
                return "{$css_rule}: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};"; // Needs UNIT
            default:
                return "{$css_rule}: {{VALUE}};";
        }
    }    
}
