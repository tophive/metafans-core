<?php


use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Image_Size;
use Elementor\Utils;
use Elementor\Repeater;
use Elementor\Icons_Manager;
use Elementor\Group_Control_Border;
use Elementor\Control_Media;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Schemes\Color;
use Elementor\Schemes\Typography;

trait Tophive_Card_Style_Controls_Trait {
    protected array $blog_style_control_map = [
        'image'       => 'add_image_style_controls',
        'title'       => 'add_title_style_controls',
        'description' => 'add_description_style_controls',
        'read_more'   => 'add_read_more_style_controls',
        'overlay_order'=> 'add_overlay_content_controls',
        'tags'        => 'add_tag_style_controls',
    ];
    
    protected function maybe_add_dynamic_style_controls( string $name ): void {
        $map_var = "{$name}_style_control_map";

        if ( ! property_exists( $this, $map_var ) || ! is_array( $this->$map_var ) ) {
            return;
        }
    
        foreach ( $this->$map_var as $method ) {
            if ( method_exists( $this, $method ) ) {
                $this->$method();
            }
        }
    }

    protected function add_box_style_controls(){
        // Start Box Style Section
		$this->start_controls_section(
			'box_style_section',
			[
				'label' => esc_html__( 'Box', TH_ELEMENTOR_SLUG ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		// Icon Position Control
		$this->add_responsive_control(
			'icon_position',
			[
				'label' => esc_html__( 'Image Position', TH_ELEMENTOR_SLUG ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => esc_html__( 'Left', TH_ELEMENTOR_SLUG ),
						'icon' => 'eicon-h-align-left',
					],
					'top' => [
						'title' => esc_html__( 'Top', TH_ELEMENTOR_SLUG ),
						'icon' => 'eicon-v-align-top',
					],
					'right' => [
						'title' => esc_html__( 'Right', TH_ELEMENTOR_SLUG ),
						'icon' => 'eicon-h-align-right',
					],
				],
				'default' => 'top',
				'toggle' => false,
				'prefix_class' => 'tophive-icon-box icon-'
			]
		);

		// Vertical Alignment Control
		$this->add_responsive_control(
			'vertical_alignment',
			[
				'label' => esc_html__( 'Vertical Alignment', TH_ELEMENTOR_SLUG ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'top' => [
						'title' => esc_html__( 'Top', TH_ELEMENTOR_SLUG ),
						'icon' => 'eicon-v-align-top',
					],
					'center' => [
						'title' => esc_html__( 'Middle', TH_ELEMENTOR_SLUG ),
						'icon' => 'eicon-v-align-middle',
					],
					'end' => [
						'title' => esc_html__( 'Bottom', TH_ELEMENTOR_SLUG ),
						'icon' => 'eicon-v-align-bottom',
					],
				],
				'default' => 'center',
				'prefix_class' => 'tophive-icon-box-vertical-align-',
				'condition' => [
					'icon_position!' => 'top',
				],
			]
		);

		// Alignment Control
		$this->add_responsive_control(
			'alignment',
			[
				'label' => esc_html__( 'Alignment', TH_ELEMENTOR_SLUG ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => esc_html__( 'Left', TH_ELEMENTOR_SLUG ),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', TH_ELEMENTOR_SLUG ),
						'icon' => 'eicon-text-align-center',
					],
					'right' => [
						'title' => esc_html__( 'Right', TH_ELEMENTOR_SLUG ),
						'icon' => 'eicon-text-align-right',
					],
				],
				'default' => 'left',
				'prefix_class' => 'icon-box-align-',
				'selectors' => [
					'{{WRAPPER}} .icon-box__content' => 'text-align: {{VALUE}};',
					'{{WRAPPER}} .icon-title__heading' => 'justify-content: {{VALUE}};'
				],
			]
		);

		// Icon Spacing Control
		$this->add_responsive_control(
			'icon_spacing',
			[
				'label' => esc_html__( 'Image Spacing', TH_ELEMENTOR_SLUG ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
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
				'selectors' => [
					'{{WRAPPER}}' => '--tophive-card-image-margin: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_responsive_control(
			'box_content_padding',
			[
				'label' => esc_html__( 'Content Padding', TH_ELEMENTOR_SLUG ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .icon-box__content, {{WRAPPER}} .tophive-card-right' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);


		\Tophive_Elementor_Style_Helper::add_ui_style_controls($this, 'box', '{{WRAPPER}} .tophive-content-card', '', false, '', ['text_color']);
        

		$this->end_controls_section();

    }

    protected function add_media_style_controls(){
        $this->start_controls_section('section_media_style', [
            'label' => __('Image', 'plugin-name'),
            'tab' => \Elementor\Controls_Manager::TAB_STYLE,
        ]);

        $this->add_responsive_control(
            'width',
            [
                'label' => esc_html__( 'Width', 'hub-elementor-addons' ),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'unit' => '%',
                    'size' => 100
                ],
                'tablet_default' => [
                    'unit' => '%',
                ],
                'mobile_default' => [
                    'unit' => '%',
                ],
                'size_units' => [ '%', 'px', 'vw' ],
                'range' => [
                    '%' => [
                        'min' => 1,
                        'max' => 100,
                    ],
                    'px' => [
                        'min' => 1,
                        'max' => 1000,
                    ],
                    'vw' => [
                        'min' => 1,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .tophive-content-card .tophive-section-image' => 'width: {{SIZE}}{{UNIT}};'
                ],
            ]
        );
        
        $this->add_responsive_control(
            'height',
            [
                'label' => esc_html__( 'Height', 'hub-elementor-addons' ),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'unit' => '%',
                    'size' => 100
                ],
                'tablet_default' => [
                    'unit' => '%',
                ],
                'mobile_default' => [
                    'unit' => '%',
                ],
                'size_units' => [ '%', 'px', 'vh' ],
                'range' => [
                    'px' => [
                        'min' => 1,
                        'max' => 500,
                    ],
                    'vh' => [
                        'min' => 1,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .tophive-content-card .tophive-section-image' => 'height: {{SIZE}}{{UNIT}};'
                ],
            ]
        );

		$this->add_responsive_control(
			'border_radius',
			[
				'label' => esc_html__( 'Border Radius', TH_ELEMENTOR_SLUG ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .tophive-content-card .tophive-section-image figure' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
				],
			]
		);

        Tophive_Elementor_Style_Helper::add_ui_style_controls($this, 'overlay', '{{WRAPPER}} .tophive-overlay-bg', false, '');

        Tophive_Elementor_UI_Helper::tophive_image_hover_effects($this);

        $this->end_controls_section();
    }
    
    
    protected function add_title_style_controls() {
        $this->start_controls_section('section_title_style', [
            'label' => __('Title', 'plugin-name'),
            'tab' => \Elementor\Controls_Manager::TAB_STYLE,
        ]);

        $this->add_control('title_color', [
            'label' => __('Color', 'plugin-name'),
            'type' => \Elementor\Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .tophive-card-title, {{WRAPPER}} .icon-box__title' => 'color: {{VALUE}};',
            ],
        ]);

        $this->add_group_control(\Elementor\Group_Control_Typography::get_type(), [
            'name' => 'title_typography',
            'selector' => '{{WRAPPER}} .tophive-card-title, {{WRAPPER}} .icon-box__title',
        ]);

        $this->add_responsive_control(
			'box_content_margin',
			[
				'label' => esc_html__( 'Margin', TH_ELEMENTOR_SLUG ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .tophive-card-title, {{WRAPPER}} .icon-box__title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

        $this->end_controls_section();
    }

    protected function add_price_style_controls() {
        $this->start_controls_section('section_price_style', [
            'label' => __('Price', 'plugin-name'),
            'tab' => \Elementor\Controls_Manager::TAB_STYLE,
        ]);

        $this->add_control('price_color', [
            'label' => __('Color', 'plugin-name'),
            'type' => \Elementor\Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .tophive-card-price' => 'color: {{VALUE}};',
            ],
        ]);

        $this->add_group_control(\Elementor\Group_Control_Typography::get_type(), [
            'name' => 'price_typography',
            'selector' => '{{WRAPPER}} .tophive-card-price',
        ]);

        $this->end_controls_section();
    }

    protected function add_description_style_controls() {
        $this->start_controls_section('section_description_style', [
            'label' => __('Description', 'plugin-name'),
            'tab' => \Elementor\Controls_Manager::TAB_STYLE,
        ]);

        $this->add_control('description_color', [
            'label' => __('Color', 'plugin-name'),
            'type' => \Elementor\Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .tophive-card-description' => 'color: {{VALUE}};',
            ],
        ]);

        $this->add_responsive_control(
			'box_desc_margin',
			[
				'label' => esc_html__( 'Margin', TH_ELEMENTOR_SLUG ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .tophive-card-description' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

        $this->add_group_control(\Elementor\Group_Control_Typography::get_type(), [
            'name' => 'description_typography',
            'selector' => '{{WRAPPER}} .tophive-card-description',
        ]);

        $this->end_controls_section();
    }

    protected function add_rating_style_controls() {
        $this->start_controls_section('section_rating_style', [
            'label' => __('Rating', 'plugin-name'),
            'tab' => \Elementor\Controls_Manager::TAB_STYLE,
        ]);

        $this->add_control('rating_color', [
            'label' => __('Star Color', 'plugin-name'),
            'type' => \Elementor\Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .tophive-card-rating .star' => 'color: {{VALUE}};',
            ],
        ]);

        $this->end_controls_section();
    }

    protected function add_read_more_style_controls() {
        \Tophive_Elementor_Style_Helper::add_ui_style_controls($this, 'text_icon', '{{WRAPPER}}.tophive-card-element .tophive-link', '{{WRAPPER}}.tophive-card-element .tophive-link:hover', true, 'Read more');
    }

    protected function add_cta_button_style_controls() {
        $this->start_controls_section('section_cta_style', [
            'label' => __('CTA Button', 'plugin-name'),
            'tab' => \Elementor\Controls_Manager::TAB_STYLE,
        ]);

        $this->add_control('cta_text_color', [
            'label' => __('Text Color', 'plugin-name'),
            'type' => \Elementor\Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .tophive-btn' => 'color: {{VALUE}};',
            ],
        ]);

        $this->add_control('cta_bg_color', [
            'label' => __('Background Color', 'plugin-name'),
            'type' => \Elementor\Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .tophive-btn' => 'background-color: {{VALUE}};',
            ],
        ]);

        $this->end_controls_section();
    }

    protected function add_badge_style_controls() {
        $this->start_controls_section('section_badge_style', [
            'label' => __('Badge', 'plugin-name'),
            'tab' => \Elementor\Controls_Manager::TAB_STYLE,
        ]);

        $this->add_control('badge_color', [
            'label' => __('Color', 'plugin-name'),
            'type' => \Elementor\Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .tophive-card-badge' => 'color: {{VALUE}};',
            ],
        ]);

        $this->add_control('badge_bg_color', [
            'label' => __('Background Color', 'plugin-name'),
            'type' => \Elementor\Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .tophive-card-badge' => 'background-color: {{VALUE}};',
            ],
        ]);

        $this->end_controls_section();
    }

    protected function add_avatar_style_controls() {
        $this->start_controls_section('section_avatar_style', [
            'label' => __('Avatar', 'plugin-name'),
            'tab' => \Elementor\Controls_Manager::TAB_STYLE,
        ]);

        $this->add_control('avatar_size', [
            'label' => __('Size (px)', 'plugin-name'),
            'type' => \Elementor\Controls_Manager::SLIDER,
            'range' => [
                'px' => [ 'min' => 20, 'max' => 150 ],
            ],
            'selectors' => [
                '{{WRAPPER}} .tophive-card-avatar img' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
            ],
        ]);

        $this->add_control('avatar_radius', [
            'label' => __('Border Radius', 'plugin-name'),
            'type' => \Elementor\Controls_Manager::SLIDER,
            'selectors' => [
                '{{WRAPPER}} .tophive-card-avatar img' => 'border-radius: {{SIZE}}{{UNIT}};',
            ],
        ]);

        $this->end_controls_section();
    }

    protected function add_status_style_controls() {
        $this->start_controls_section('section_status_style', [
            'label' => __('Status Label', 'plugin-name'),
            'tab' => \Elementor\Controls_Manager::TAB_STYLE,
        ]);

        $this->add_control('status_color', [
            'label' => __('Text Color', 'plugin-name'),
            'type' => \Elementor\Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .tophive-card-status' => 'color: {{VALUE}};',
            ],
        ]);

        $this->add_control('status_bg_color', [
            'label' => __('Background Color', 'plugin-name'),
            'type' => \Elementor\Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .tophive-card-status' => 'background-color: {{VALUE}};',
            ],
        ]);

        $this->end_controls_section();
    }

    protected function add_overlay_content_controls(){
        $this->start_controls_section('overlay_content_style', [
            'label' => esc_html__('Overlay Content', 'plugin-name'),
            'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
            'condition' => [
                'show_overlay_content' => 'yes',
            ],
        ]);
        
        $this->start_controls_tabs('overlay_content_tabs');
        
        // ----------------------
        // 🔹 NORMAL TAB
        // ----------------------
        $this->start_controls_tab('overlay_content_tab_normal', [
            'label' => esc_html__('Normal', 'plugin-name'),
        ]);
        
        // Height
        $this->add_responsive_control('overlay_height', [
            'label' => esc_html__('Height', 'plugin-name'),
            'type' => \Elementor\Controls_Manager::SLIDER,
            'range' => [
                'px' => ['min' => 0, 'max' => 1000],
            ],
            'selectors' => [
                '{{WRAPPER}}.show-overlay-content-yes .tophive-card-left .tophive-card-overlay-content' => 'height: {{SIZE}}{{UNIT}};',
            ],
        ]);
        
        // Position Y
        $this->add_responsive_control('overlay_translate_y', [
            'label' => esc_html__('Translate Y (Position)', 'plugin-name'),
            'type' => \Elementor\Controls_Manager::SLIDER,
            'range' => [
                'px' => ['min' => -100, 'max' => 500],
            ],
            'selectors' => [
                '{{WRAPPER}}.show-overlay-content-yes .tophive-card-left .tophive-card-overlay-content' => 'transform: translateY({{SIZE}}{{UNIT}});',
            ],
        ]);
        
        // Background
        $this->add_group_control(
            \Elementor\Group_Control_Background::get_type(),
            [
                'name' => 'overlay_bg',
                'label' => esc_html__('Background', 'plugin-name'),
                'types' => ['classic', 'gradient'],
                'selector' => '{{WRAPPER}}.show-overlay-content-yes .tophive-card-left .tophive-card-overlay-content',
            ]
        );
        
        // Padding
        $this->add_responsive_control('overlay_padding', [
            'label' => esc_html__('Padding', 'plugin-name'),
            'type' => \Elementor\Controls_Manager::DIMENSIONS,
            'selectors' => [
                '{{WRAPPER}}.show-overlay-content-yes .tophive-card-left .tophive-card-overlay-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]);
        
        // Margin
        $this->add_responsive_control('overlay_margin', [
            'label' => esc_html__('Margin', 'plugin-name'),
            'type' => \Elementor\Controls_Manager::DIMENSIONS,
            'selectors' => [
                '{{WRAPPER}}.show-overlay-content-yes .tophive-card-left .tophive-card-overlay-content' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]);
        
        // Border Radius
        $this->add_responsive_control('overlay_border_radius', [
            'label' => esc_html__('Border Radius', 'plugin-name'),
            'type' => \Elementor\Controls_Manager::DIMENSIONS,
            'selectors' => [
                '{{WRAPPER}}.show-overlay-content-yes .tophive-card-left .tophive-card-overlay-content' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]);
        
        $this->end_controls_tab();
        
        // ----------------------
        // 🔸 HOVER TAB
        // ----------------------
        $this->start_controls_tab('overlay_content_tab_hover', [
            'label' => esc_html__('Hover', 'plugin-name'),
        ]);
        
        // Hover Height
        $this->add_responsive_control('overlay_height_hover', [
            'label' => esc_html__('Height (Hover)', 'plugin-name'),
            'type' => \Elementor\Controls_Manager::SLIDER,
            'range' => [
                'px' => ['min' => 0, 'max' => 1000],
            ],
            'selectors' => [
                '{{WRAPPER}}.show-overlay-content-yes .tophive-card-left:hover .tophive-card-overlay-content' => 'height: {{SIZE}}{{UNIT}};',
            ],
        ]);
        
        // Hover Background
        $this->add_group_control(
            \Elementor\Group_Control_Background::get_type(),
            [
                'name' => 'overlay_bg_hover',
                'label' => esc_html__('Background (Hover)', 'plugin-name'),
                'types' => ['classic', 'gradient'],
                'selector' => '{{WRAPPER}}.show-overlay-content-yes .tophive-card-left:hover .tophive-card-overlay-content',
            ]
        );
        
        $this->end_controls_tab();
        
        $this->end_controls_tabs();
        $this->end_controls_section();        
    }
}
