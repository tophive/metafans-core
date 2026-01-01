<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}


class Tophive_Elementor_UI_Helper {

    /**
     * Add common controls for different UI elements.
     *
     * @param \Elementor\Widget_Base $widget
     * @param string $element_type Type of element (e.g., button, input, dropdown)
     * @param string $selector CSS selector for styles
     */
    public static function add_ui_controls($widget, $element_type, $selector, $add_section = true, $section_label = '') {

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
                    'label' => $section_label ? $section_label : '',
                    'type' => \Elementor\Controls_Manager::HEADING,
                    'separator' => 'before',
                ]
            );
        }

        switch ($element_type) {
            case 'button':
                self::add_button_styles($widget, $selector);
                break;
            case 'input':
                self::add_input_styles($widget, $selector);
                break;
            case 'dropdown':
                self::add_dropdown_styles($widget, $selector);
                break;
            case 'title':
                self::title_ui_controls($widget, $selector);
                break;
        }

        if ($add_section) {
            $widget->end_controls_section();
        }
    }

    public static function button_type_controls($widget){
        // Button Styles
        $widget->add_control(
            'tophive_button_type',
            [
                'label'   => __( 'Button Effects/Styles', TH_ELEMENTOR_SLUG ),
                'type'    => \Elementor\Controls_Manager::SELECT,
                'options' => [
                    'default'  => __( 'Default', TH_ELEMENTOR_SLUG ),
                    'reveal-bottom'  => esc_html__( 'Reveal bottom', TH_ELEMENTOR_SLUG ),
                    'moveup-end'  => esc_html__( 'Move Up End', TH_ELEMENTOR_SLUG ),
                    'wiper'  => esc_html__( 'Wiper', TH_ELEMENTOR_SLUG ),
                    'winona'  => esc_html__( 'Winona', TH_ELEMENTOR_SLUG ),
                    'calypso'  => esc_html__( 'Calypso', TH_ELEMENTOR_SLUG ),
                ],
                'default'   => 'default',
            ]
        );
    }

    public static function text_hover_effects($widget, $id){
        $widget->add_control(
            $id . '_text_hover',
            [
                'label'   => __( 'Hover effects', TH_ELEMENTOR_SLUG ),
                'type'    => \Elementor\Controls_Manager::SELECT,
                'options' => [
                    'default'  => __( 'No effect', TH_ELEMENTOR_SLUG ),
                    'tophive-text-hover-underline'  => esc_html__( 'Underline', TH_ELEMENTOR_SLUG ),
                ],
                'default'   => 'default',
            ]
        );
    }

    public static function title_ui_controls($widget, $selector){
		$widget->add_control(
			'show_title_icon',
			[
				'label' => esc_html__( 'Add icon?', TH_ELEMENTOR_SLUG ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', TH_ELEMENTOR_SLUG ),
				'label_off' => esc_html__( 'Off', TH_ELEMENTOR_SLUG ),
				'return_value' => 'yes',
				'default' => '',
				'separator' => 'before',
			]
		);

		$widget->add_control(
			'tophive_title_icon',
			[
				'label' => esc_html__( 'Icon', TH_ELEMENTOR_SLUG ),
				'type' => \Elementor\Controls_Manager::ICONS,
				'default' => [
					'value' => 'fas fa-arrow-right',
					'library' => 'solid',
				],
				'condition' => array(
					'show_title_icon' => 'yes',
				),
			]
		);
		$widget->add_responsive_control(
			'title_icon_position',
			[
				'label' => esc_html__( 'Icon Position', TH_ELEMENTOR_SLUG ),
				'type' => \Elementor\Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => esc_html__( 'Left', TH_ELEMENTOR_SLUG ),
						'icon' => 'eicon-h-align-left',
					],
					'right' => [
						'title' => esc_html__( 'Right', TH_ELEMENTOR_SLUG ),
						'icon' => 'eicon-h-align-right',
					],
				],
				'default' => 'right',
				'toggle' => false,
				'prefix_class' => 'heading-icon-',
				'condition' => array(
					'show_title_icon' => 'yes',
					'show_icon_inline!' => 'yes',
				),
			]
		);


		$widget->add_control(
			'show_icon_inline',
			[
				'label' => esc_html__( 'Inline icon', TH_ELEMENTOR_SLUG ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', TH_ELEMENTOR_SLUG ),
				'label_off' => esc_html__( 'Off', TH_ELEMENTOR_SLUG ),
				'return_value' => 'yes',
				'default' => '',
				'separator' => 'before',
				'prefix_class' => 'heading-icon-inline-',
				'condition' => array(
					'show_title_icon' => 'yes',
				),
			]
		);
		$widget->add_control(
			'show_title_icon_onhover',
			[
				'label' => esc_html__( 'On Hover Icon', TH_ELEMENTOR_SLUG ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', TH_ELEMENTOR_SLUG ),
				'label_off' => esc_html__( 'Off', TH_ELEMENTOR_SLUG ),
				'return_value' => 'yes',
				'default' => '',
				'separator' => 'before',
				'prefix_class' => 'show-icon-onhover-',
				'condition' => array(
					'show_title_icon' => 'yes',
				),
			]
		);
    }

    public static function marquee_controls($widget){
        $widget->add_control('speed', [
            'label' => __('Speed (higher = faster)', 'plugin-name'),
            'type' => \Elementor\Controls_Manager::SLIDER,
            'range' => ['min' => 1, 'max' => 60],
            'default' => ['size' => 2],
        ]);
    
        $widget->add_control('direction', [
            'label' => __('Direction', 'plugin-name'),
            'type' => \Elementor\Controls_Manager::SELECT,
            'options' => [
                'left' => 'Left',
                'right' => 'Right',
                'top' => 'Top',
                'bottom' => 'Bottom',
            ],
            'default' => 'left',
            'prefix_class' => 'direction-'
        ]);
    
        $widget->add_control('pause_on_hover', [
            'label' => __('Pause on Hover', 'plugin-name'),
            'type' => \Elementor\Controls_Manager::SWITCHER,
            'default' => 'yes',
        ]);
    
        $widget->add_control('enable_mask', [
            'label' => __('Enable Mask', 'plugin-name'),
            'type' => \Elementor\Controls_Manager::SWITCHER,
            'default' => 'yes',
        ]);
        $widget->add_control('scrub_mode', [
            'label' => __('Enable Scrub Mode (Scroll-Based)', 'plugin-name'),
            'type' => \Elementor\Controls_Manager::SWITCHER,
            'default' => '',
            'description' => 'When enabled, marquee moves only on scroll instead of auto-looping.',
        ]);
    }

    public static function marquee_wrapper_control( $widget ){
        $widget->add_group_control(\Elementor\Group_Control_Background::get_type(), [
            'name' => 'background',
            'label' => __('Background', 'plugin-name'),
            'types' => ['classic', 'gradient'],
            'selector' => '{{WRAPPER}} .tophive-marquee-wrapper',
        ]);
    
        $widget->add_responsive_control('wrapper_height', [
            'label' => __('Height', 'plugin-name'),
            'type' => \Elementor\Controls_Manager::SLIDER,
            'range' => ['px' => ['min' => 10, 'max' => 500]],
            'selectors' => [
                '{{WRAPPER}} .tophive-marquee-wrapper' => 'height: {{SIZE}}{{UNIT}};',
            ],
        ]);
    
        $widget->add_group_control(\Elementor\Group_Control_Border::get_type(), [
            'name' => 'wrapper_border',
            'label' => __('Border', 'plugin-name'),
            'selector' => '{{WRAPPER}} .tophive-marquee-wrapper',
        ]);
    }

    public static function lightbox($widget){
        // Lightbox
		$widget->start_controls_section(
			'lightbox_section',
			[
				'label' => __( 'Lightbox', 'hub-elementor-addons' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT
			]
		);

		$widget->add_control(
			'enable_lightbox',
			[
				'label' => __( 'Enable lightbox', 'hub-elementor-addons' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => __( 'On', 'hub-elementor-addons' ),
				'label_off' => __( 'Off', 'hub-elementor-addons' ),
				'return_value' => 'yes',
				'default' => '',
			]
		);

		$widget->add_control(
			'lightbox_group_id',
			[
				'label' => __( 'Lightbox groupd id', 'hub-elementor-addons' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'placeholder' => __( 'Enter a lightbox group id', 'hub-elementor-addons' ),
				'condition' => [
					'enable_lightbox' => 'yes'
				]
			]
		);

		$widget->end_controls_section();
    }

    public static function tophive_data_attrs_from_settings(array $settings): string {
        $allowed_keys = ['speed', 'direction', 'pause_on_hover', 'scrub_mode'];
    
        $output = [];
    
        foreach ($allowed_keys as $key) {
            if (isset($settings[$key]) && $settings[$key] !== '') {
                $value = is_array($settings[$key]) && isset($settings[$key]['size'])
                    ? $settings[$key]['size']
                    : $settings[$key];
    
                // Map control names to proper data-* names if needed
                $data_key = match ($key) {
                    'pause_on_hover' => 'pause',
                    'scrub_mode'     => 'scrub',
                    default          => $key,
                };
    
                $output[] = 'data-' . esc_attr($data_key) . '="' . esc_attr($value) . '"';
            }
        }
    
        return implode(' ', $output);
    }

    public static function tophive_image_hover_effects( $widget ){
        $widget->add_control(
            'tophive_hover_effect',
            [
                'label' => __( 'Image Hover Effect', 'hub-elementor-addons' ),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => '',
                'options' => [
                    '' => __( 'None', 'hub-elementor-addons' ),
                    'zoom-in' => __( 'Zoom In', 'hub-elementor-addons' ),
                    'zoom-in-x' => __( 'Zoom In X', 'hub-elementor-addons' ),
                    'zoom-in-y' => __( 'Zoom In Y', 'hub-elementor-addons' ),
                    'zoom-out' => __( 'Zoom Out', 'hub-elementor-addons' ),
                    'zoom-rotate' => __( 'Zoom Rotate', 'hub-elementor-addons' ),
                    'pan-up' => __( 'Pan Up', 'hub-elementor-addons' ),
                    'pan-down' => __( 'Pan Down', 'hub-elementor-addons' ),
                    'pan-left' => __( 'Pan Left', 'hub-elementor-addons' ),
                    'pan-right' => __( 'Pan Right', 'hub-elementor-addons' ),
                    'blur-in' => __( 'Blur In', 'hub-elementor-addons' ),
                ],
                'prefix_class' => 'tophive-img-hover-',
                'selectors' => [
                    '{{WRAPPER}} .tophive-image figure' => '--tophive-hover-effect: {{VALUE}};',
                ],
            ]
        );
        
    }

    public static function add_header_controls( $widget, $dynamic = false ){

    }

    public static function get_carousel_settings($settings){
        return [
            'slidesPerView' => $settings['slides_per_view']['size'] ?? 3,
            'spaceBetween'  => $settings['space_between']['size'] ?? 20,
            'loop'          => $settings['loop'] === 'yes',
            'autoplay'      => $settings['autoplay'] === 'yes' ? [
                'delay' => intval($settings['autoplay_delay'] ?? 5000),
                'disableOnInteraction' => false,
            ] : false,
            'pagination' => [
                'el' => '.swiper-pagination',
                'clickable' => true,
            ],
            'navigation' => [
                'nextEl' => '.swiper-button-next',
                'prevEl' => '.swiper-button-prev',
            ],
        ];
    }
    
}
