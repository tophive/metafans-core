<?php

namespace METAFANSCORE\widgets\elementor;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class MetafansElementorTestimonialCarousel extends \Elementor\Widget_Base {
	private $slide_prints_count = 0;


  	public function get_script_depends() {
     	return [ 'th-fe-script-handle' ];
  	}
	public function get_name() {
		return 'th-testimonial-carousel';
	}

	public function get_title() {
		return esc_html__( 'Testimonial Carousel', WP_MF_CORE_SLUG );
	}

	public function get_icon() {
		return 'eicon-testimonial';
	}
	public function get_categories(){
        return [ WP_MF_CORE_SLUG ];
    }
	public function get_keywords() {
		return [ 'testimonial', 'carousel', 'image' ];
	}

	protected function register_controls() {
		$this->start_controls_section(
			'section_slides',
			[
				'label' => esc_html__( 'Slides', WP_MF_CORE_SLUG ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$repeater = new \Elementor\Repeater();

		$this->add_repeater_controls( $repeater );

		$this->add_control(
			'slides',
			[
				'label' => esc_html__( 'Slides', WP_MF_CORE_SLUG ),
				'type' => \Elementor\Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'default' => $this->get_repeater_defaults(),
				'separator' => 'after',
			]
		);

		$this->add_control(
			'effect',
			[
				'type' => \Elementor\Controls_Manager::SELECT,
				'label' => esc_html__( 'Effect', WP_MF_CORE_SLUG ),
				'default' => 'slide',
				'options' => [
					'slide' => esc_html__( 'Slide', WP_MF_CORE_SLUG ),
					'fade' => esc_html__( 'Fade', WP_MF_CORE_SLUG ),
					'cube' => esc_html__( 'Cube', WP_MF_CORE_SLUG ),
				],
				'frontend_available' => true,
			]
		);

		// $slides_per_view = range( 1, 10 );
		// $slides_per_view = array_combine( $slides_per_view, $slides_per_view );

		// $this->add_responsive_control(
		// 	'slides_per_view',
		// 	[
		// 		'type' => \Elementor\Controls_Manager::SELECT,
		// 		'label' => esc_html__( 'Slides Per View', WP_MF_CORE_SLUG ),
		// 		'options' => [ '' => esc_html__( 'Default', WP_MF_CORE_SLUG ) ] + $slides_per_view,
		// 		'condition' => [
		// 			'effect' => 'slide',
		// 		],
		// 		'default' => 1,
		// 		'frontend_available' => true,
		// 	]
		// );

		// $this->add_responsive_control(
		// 	'slides_to_scroll',
		// 	[
		// 		'type' => \Elementor\Controls_Manager::SELECT,
		// 		'label' => esc_html__( 'Slides to Scroll', WP_MF_CORE_SLUG ),
		// 		'description' => esc_html__( 'Set how many slides are scrolled per swipe.', WP_MF_CORE_SLUG ),
		// 		'options' => [ '' => esc_html__( 'Default', WP_MF_CORE_SLUG ) ] + $slides_per_view,
		// 		'condition' => [
		// 			'effect' => 'slide',
		// 		],
		// 		'default' => 3,
		// 		'frontend_available' => true,
		// 	]
		// );

		// $this->add_responsive_control(
		// 	'height',
		// 	[
		// 		'type' => \Elementor\Controls_Manager::SLIDER,
		// 		'label' => esc_html__( 'Height', WP_MF_CORE_SLUG ),
		// 		'size_units' => [ 'px', 'vh' ],
		// 		'range' => [
		// 			'px' => [
		// 				'min' => 100,
		// 				'max' => 1000,
		// 			],
		// 			'vh' => [
		// 				'min' => 20,
		// 			],
		// 		],
		// 		'selectors' => [
		// 			'{{WRAPPER}} .tophive-main-swiper' => 'height: {{SIZE}}{{UNIT}};',
		// 		],
		// 	]
		// );

		// $this->add_responsive_control(
		// 	'width',
		// 	[
		// 		'type' => \Elementor\Controls_Manager::SLIDER,
		// 		'label' => esc_html__( 'Width', WP_MF_CORE_SLUG ),
		// 		'range' => [
		// 			'px' => [
		// 				'min' => 100,
		// 				'max' => 1140,
		// 			],
		// 			'%' => [
		// 				'min' => 50,
		// 			],
		// 		],
		// 		'size_units' => [ '%', 'px' ],
		// 		'default' => [
		// 			'unit' => '%',
		// 		],
		// 		'selectors' => [
		// 			'{{WRAPPER}} .tophive-main-swiper' => 'width: calc({{SIZE}}{{UNIT}} + 30px)',
		// 		],
		// 	]
		// );

		$this->end_controls_section();

		$this->start_controls_section(
			'section_additional_options',
			[
				'label' => esc_html__( 'Additional Options', WP_MF_CORE_SLUG ),
			]
		);

		$this->add_control(
			'show_arrows',
			[
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label' => esc_html__( 'Arrows', WP_MF_CORE_SLUG ),
				'default' => 'yes',
				'label_off' => esc_html__( 'Hide', WP_MF_CORE_SLUG ),
				'label_on' => esc_html__( 'Show', WP_MF_CORE_SLUG ),
				'return_value' => 'yes',
				'render_type' => 'template',
				'frontend_available' => true,
			]
		);
		$this->add_control(
			'speed',
			[
				'label' => esc_html__( 'Transition Duration', WP_MF_CORE_SLUG ),
				'type' => \Elementor\Controls_Manager::NUMBER,
				'default' => 500,
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'autoplay',
			[
				'label' => esc_html__( 'Autoplay', WP_MF_CORE_SLUG ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'default' => 'yes',
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'autoplay_speed',
			[
				'label' => esc_html__( 'Autoplay Speed', WP_MF_CORE_SLUG ),
				'type' => \Elementor\Controls_Manager::NUMBER,
				'default' => 5000,
				'condition' => [
					'autoplay' => 'yes',
				],
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'loop',
			[
				'label' => esc_html__( 'Infinite Loop', WP_MF_CORE_SLUG ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'default' => 'yes',
				'frontend_available' => true,
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Image_Size::get_type(),
			[
				'name' => 'image_size',
				'default' => 'full',
				'separator' => 'before',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_slides_style',
			[
				'label' => esc_html__( 'Slides', WP_MF_CORE_SLUG ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'content_height',
			[
				'label' => esc_html__( 'Slider Height', WP_MF_CORE_SLUG ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'default' => array(
					'unit' => 'px',
					'size' => 410
				),
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 800,
						'step' => 1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .tophive-testimonial' => 'height: {{SIZE}}{{UNIT}}',
				],
			]
		);
		$this->add_responsive_control(
			'content_spacing',
			[
				'label' => esc_html__( 'Slider Spacing', WP_MF_CORE_SLUG ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'default' => array(
					'unit' => 'px',
					'size' => 90
				),
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 800,
						'step' => 1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .tophive-testimonial' => 'padding-top: {{SIZE}}{{UNIT}}',
				],
			]
		);

		
		$this->add_responsive_control(
			'slide_padding',
			[
				'label' => esc_html__( 'Padding', WP_MF_CORE_SLUG ),
				'type' => \Elementor\Controls_Manager::DIMENSIONS,
				'selectors' => [
					'{{WRAPPER}} .tophive-main-swiper .swiper-slide' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				],
				'separator' => 'before',
			]
		);
		$this->add_responsive_control(
			'slide_margin',
			[
				'label' => esc_html__( 'Margin', WP_MF_CORE_SLUG ),
				'type' => \Elementor\Controls_Manager::DIMENSIONS,
				'selectors' => [
					'{{WRAPPER}} .tophive-testimonial' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				],
				'separator' => 'before',
			]
		);

		$this->end_controls_section();

		/*****************************************************************/
		/***************** Navigation Section ******************************/
		/*****************************************************************/
		$this->start_controls_section(
			'section_navigation',
			[
				'label' => esc_html__( 'Navigation', WP_MF_CORE_SLUG ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

		$this->start_controls_tabs('navigation_controls_tabs_testimonial');
		$this->start_controls_tab(
			'navigation_controls_tabs_testimonial_section', 
			[ 
				'label' => esc_html__( 'Arrow', WP_MF_CORE_SLUG ) 
			] 
		);

		$this->add_control(
			'arrows_size',
			[
				'label' => esc_html__( 'Size', WP_MF_CORE_SLUG ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'default' => [
					'size' => 20,
				],
				'range' => [
					'px' => [
						'min' => 10,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .th-slider-prev-next span i' => 'font-size: {{SIZE}}{{UNIT}}',
				],
			]
		);
		$this->add_control(
			'arrow_sapcing',
			[
				'label' => esc_html__( 'Spacing', WP_MF_CORE_SLUG ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 50,
						'step' => 1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .th-slider-prev-next span i' => 'padding: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_control(
			'arrow_width',
			[
				'label' => esc_html__( 'Width', WP_MF_CORE_SLUG ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 50,
						'step' => 1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .th-slider-prev-next span i' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_control(
			'arrow_height',
			[
				'label' => esc_html__( 'Height', WP_MF_CORE_SLUG ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 50,
						'step' => 1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .th-slider-prev-next span i' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_control(
			'arrow_border_radius',
			[
				'label' => esc_html__( 'Border Radius', WP_MF_CORE_SLUG ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 50,
						'step' => 1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .th-slider-prev-next span i' => 'border-radius: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'arrows_color',
			[
				'label' => esc_html__( 'Color', 'plugin-domain' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'scheme' => [
					'type' => \Elementor\Core\Schemes\Color::get_type(),
					'value' => \Elementor\Core\Schemes\Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .th-slider-prev-next span i' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_control(
			'arrow_background',
			[
				'label' => esc_html__( 'Background Color', 'plugin-domain' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'scheme' => [
					'type' => \Elementor\Core\Schemes\Color::get_type(),
					'value' => \Elementor\Core\Schemes\Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .th-slider-prev-next span i' => 'background-color: {{VALUE}}',
				],
			]
		);
		$this->add_control(
			'arrows_color_hover',
			[
				'label' => esc_html__( 'Hover Color', 'plugin-domain' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'scheme' => [
					'type' => \Elementor\Core\Schemes\Color::get_type(),
					'value' => \Elementor\Core\Schemes\Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .th-slider-prev-next span i:hover' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_control(
			'arrow_background_hover',
			[
				'label' => esc_html__( 'Hover Background Color', 'plugin-domain' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'scheme' => [
					'type' => \Elementor\Core\Schemes\Color::get_type(),
					'value' => \Elementor\Core\Schemes\Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .th-slider-prev-next span i:hover' => 'background-color: {{VALUE}}',
				],
			]
		);
		
		$this->end_controls_tab();
		$this->start_controls_tab(
			'navigation_controls_tabs_testimonial_section_pagi', 
			[ 
				'label' => esc_html__( 'Pagination', WP_MF_CORE_SLUG ) 
			] 
		);
		$this->add_control(
			'heading_pagination',
			[
				'label' => esc_html__( 'Pagination', WP_MF_CORE_SLUG ),
				'type' => \Elementor\Controls_Manager::HEADING,
			]
		);

		$this->add_control(
			'pagination_size',
			[
				'label' => esc_html__( 'Size', WP_MF_CORE_SLUG ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max' => 20,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .th-slide-dot-nav span' => 'width: {{SIZE}}{{UNIT}};'
				]
			]
		);

		$this->add_control(
			'pagination_color',
			[
				'label' => esc_html__( 'Color', WP_MF_CORE_SLUG ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .th-slide-dot-nav span' => 'background-color: {{VALUE}}'
				]
			]
		);
		$this->add_control(
			'pagination_color_active',
			[
				'label' => esc_html__( 'Active Color', WP_MF_CORE_SLUG ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .th-slide-dot-nav span.active' => 'background-color: {{VALUE}}'
				]
			]
		);

		$this->add_control(
			'pagination_top_margin',
			[
				'label' => esc_html__( 'Pagination Margin Top', WP_MF_CORE_SLUG ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range' => [
					'px' => [
						'min' => -200,
						'max' => 200,
						'step' => 1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .th-slide-dot-nav' => 'margin-top: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();

		$this->start_injection( [
			'of' => 'slides',
		] );

		// $this->add_control(
		// 	'layout',
		// 	[
		// 		'label' => esc_html__( 'Layout', WP_MF_CORE_SLUG ),
		// 		'type' => \Elementor\Controls_Manager::SELECT,
		// 		'default' => 'image_inline',
		// 		'options' => [
		// 			'image_inline' => esc_html__( 'Image Inline', WP_MF_CORE_SLUG ),
		// 			'image_stacked' => esc_html__( 'Image Stacked', WP_MF_CORE_SLUG ),
		// 			'image_above' => esc_html__( 'Image Above', WP_MF_CORE_SLUG ),
		// 			'image_left' => esc_html__( 'Image Left', WP_MF_CORE_SLUG ),
		// 			'image_right' => esc_html__( 'Image Right', WP_MF_CORE_SLUG ),
		// 		],
		// 		'prefix_class' => 'tophive-testimonial--layout-',
		// 		'render_type' => 'template',
		// 	]
		// );

		$this->add_control(
			'alignment',
			[
				'label' => esc_html__( 'Alignment', WP_MF_CORE_SLUG ),
				'type' => \Elementor\Controls_Manager::CHOOSE,
				'label_block' => false,
				'default' => 'center',
				'options' => [
					'left' => [
						'title' => esc_html__( 'Left', WP_MF_CORE_SLUG ),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', WP_MF_CORE_SLUG ),
						'icon' => 'eicon-text-align-center',
					],
					'right' => [
						'title' => esc_html__( 'Right', WP_MF_CORE_SLUG ),
						'icon' => 'eicon-text-align-right',
					],
				],
				'prefix_class' => 'tophive-testimonial--align-',
			]
		);

		$this->end_injection();

		$this->start_controls_section(
			'section_skin_style',
			[
				'label' => esc_html__( 'Bubble', WP_MF_CORE_SLUG ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
				'condition' => [
					'skin' => 'bubble',
				],
			]
		);

		$this->add_control(
			'background_color',
			[
				'label' => esc_html__( 'Background Color', WP_MF_CORE_SLUG ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'alpha' => false,
				'selectors' => [
					'{{WRAPPER}} .tophive-testimonial__content, {{WRAPPER}} .tophive-testimonial__content:after' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			'text_padding',
			[
				'label' => esc_html__( 'Padding', WP_MF_CORE_SLUG ),
				'type' => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'default' => [
					'top' => '20',
					'bottom' => '20',
					'left' => '20',
					'right' => '20',
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .tophive-testimonial__content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
					'{{WRAPPER}}.tophive-testimonial--layout-image_left .tophive-testimonial__footer,
					{{WRAPPER}}.tophive-testimonial--layout-image_right .tophive-testimonial__footer' => 'padding-top: {{TOP}}{{UNIT}}',
					'{{WRAPPER}}.tophive-testimonial--layout-image_above .tophive-testimonial__footer,
					{{WRAPPER}}.tophive-testimonial--layout-image_inline .tophive-testimonial__footer,
					{{WRAPPER}}.tophive-testimonial--layout-image_stacked .tophive-testimonial__footer' => 'padding: 0 {{RIGHT}}{{UNIT}} 0 {{LEFT}}{{UNIT}}',
				],
			]
		);

		$this->add_responsive_control(
			'border_radius',
			[
				'label' => esc_html__( 'Border Radius', WP_MF_CORE_SLUG ),
				'type' => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .tophive-testimonial__content' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'border',
			[
				'label' => esc_html__( 'Border', WP_MF_CORE_SLUG ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'selectors' => [
					'{{WRAPPER}} .tophive-testimonial__content, {{WRAPPER}} .tophive-testimonial__content:after' => 'border-style: solid',
				],
			]
		);

		$this->add_control(
			'border_color',
			[
				'label' => esc_html__( 'Border Color', WP_MF_CORE_SLUG ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'default' => '#000',
				'selectors' => [
					'{{WRAPPER}} .tophive-testimonial__content' => 'border-color: {{VALUE}}',
					'{{WRAPPER}} .tophive-testimonial__content:after' => 'border-color: transparent {{VALUE}} {{VALUE}} transparent',
				],
				'condition' => [
					'border' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'border_width',
			[
				'label' => esc_html__( 'Border Width', WP_MF_CORE_SLUG ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 20,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .tophive-testimonial__content, {{WRAPPER}} .tophive-testimonial__content:after' => 'border-width: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}}.tophive-testimonial--layout-image_stacked .tophive-testimonial__content:after,
					{{WRAPPER}}.tophive-testimonial--layout-image_inline .tophive-testimonial__content:after' => 'margin-top: -{{SIZE}}{{UNIT}}',
					'{{WRAPPER}}.tophive-testimonial--layout-image_above .tophive-testimonial__content:after' => 'margin-bottom: -{{SIZE}}{{UNIT}}',
				],
				'condition' => [
					'border' => 'yes',
				],
			]
		);

		$this->end_controls_section();

		$this->start_injection( [
			'at' => 'before',
			'of' => 'section_navigation',
		] );

		$this->start_controls_section(
			'section_content_style',
			[
				'label' => esc_html__( 'Content', WP_MF_CORE_SLUG ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_group_control(
			\Elementor\Group_Control_Background::get_type(),
			[
				'name' => 'testimonial_bg_color',
				'label' => esc_html__( 'Background', WP_MF_CORE_SLUG ),
				'types' => [ 'classic'],
				'selector' => '{{WRAPPER}} .tophive-testimonial__content',
			]
		);
		$this->add_responsive_control(
			'test_content_padding',
			[
				'label' => esc_html__( 'Padding', WP_MF_CORE_SLUG ),
				'type' => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .tophive-testimonial__content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_control(
			'testimonial_content_br',
			[
				'label' => esc_html__( 'Border Radius', WP_MF_CORE_SLUG ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 50,
						'step' => 1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .tophive-testimonial__content' => 'border-radius: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'testimonial_content_spacing',
			[
				'label' => esc_html__( 'Left-Right Position', WP_MF_CORE_SLUG ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => -500,
						'max' => 500,
						'step' => 1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .tophive-testimonial__content' => 'margin-left: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'content_color',
			[
				'label' => esc_html__( 'Text Color', WP_MF_CORE_SLUG ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tophive-testimonial__text' => 'color: {{VALUE}}',
				],
				'scheme' => [
					'type' => \Elementor\Core\Schemes\Color::get_type(),
					'value' => \Elementor\Core\Schemes\Color::COLOR_3,
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name' => 'content_typography',
				'selector' => '{{WRAPPER}} .tophive-testimonial__text',
				'scheme' => \Elementor\Core\Schemes\Typography::TYPOGRAPHY_3,
			]
		);
		$this->add_control(
			'content_mb',
			[
				'label' => esc_html__( 'Spacing', WP_MF_CORE_SLUG ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 50,
						'step' => 1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .tophive-testimonial__text' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'name_title_style',
			[
				'label' => esc_html__( 'Name', WP_MF_CORE_SLUG ),
				'type' => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'name_color',
			[
				'label' => esc_html__( 'Text Color', WP_MF_CORE_SLUG ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tophive-testimonial__name' => 'color: {{VALUE}}',
				],
				'scheme' => [
					'type' => \Elementor\Core\Schemes\Color::get_type(),
					'value' => \Elementor\Core\Schemes\Color::COLOR_3,
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name' => 'name_typography',
				'selector' => '{{WRAPPER}} .tophive-testimonial__name',
				'scheme' => \Elementor\Core\Schemes\Typography::TYPOGRAPHY_1,
			]
		);

		$this->add_control(
			'heading_title_style',
			[
				'label' => esc_html__( 'Title', WP_MF_CORE_SLUG ),
				'type' => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'title_color',
			[
				'label' => esc_html__( 'Text Color', WP_MF_CORE_SLUG ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tophive-testimonial__title' => 'color: {{VALUE}}',
				],
				'scheme' => [
					'type' => \Elementor\Core\Schemes\Color::get_type(),
					'value' => \Elementor\Core\Schemes\Color::COLOR_1,
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name' => 'title_typography',
				'selector' => '{{WRAPPER}} .tophive-testimonial__title',
				'scheme' => \Elementor\Core\Schemes\Typography::TYPOGRAPHY_2,
			]
		);
		$this->add_control(
			'heading_rating_style',
			[
				'label' => esc_html__( 'Rating', WP_MF_CORE_SLUG ),
				'type' => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_control(
			'rating_color',
			[
				'label' => esc_html__( 'Color', WP_MF_CORE_SLUG ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'scheme' => [
					'type' => \Elementor\Core\Schemes\Color::get_type(),
					'value' => \Elementor\Core\Schemes\Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .review-stars-rated .review-stars.empty,{{WRAPPER}}  .review-stars-rated .review-stars.filled' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_control(
			'rating_spacing',
			[
				'label' => esc_html__( 'Rating Spacing', WP_MF_CORE_SLUG ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 50,
						'step' => 1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .tophive-testimonial__content .review-stars-rated' => 'margin-top: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->end_controls_section();

		$this->start_controls_section(
			'section_image_style',
			[
				'label' => esc_html__( 'Image', WP_MF_CORE_SLUG ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'image_size_width',
			[
				'label' => esc_html__( 'Width', WP_MF_CORE_SLUG ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 500,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 100,
				],
				'selectors' => [
					'{{WRAPPER}} .tophive-testimonial__image' => 'width: {{SIZE}}{{UNIT}};'
					
				],
			]
		);
		$this->add_responsive_control(
			'image_size_height',
			[
				'label' => esc_html__( 'height', WP_MF_CORE_SLUG ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 500,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 100,
				],
				'selectors' => [
					'{{WRAPPER}} .tophive-testimonial__image' => 'height: {{SIZE}}{{UNIT}};'
					
				],
			]
		);
		$this->add_responsive_control(
			'image_position_top',
			[
				'label' => esc_html__( 'Position(Top To Bottom)', WP_MF_CORE_SLUG ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range' => [
					'px' => [
						'min' => -500,
						'max' => 500,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => -130,
				],
				'selectors' => [
					'{{WRAPPER}} .tophive-testimonial__image' => 'top: {{SIZE}}{{UNIT}};'
					
				],
			]
		);
		$this->add_responsive_control(
			'image_position_bottom',
			[
				'label' => esc_html__( 'Position(Side To Side)', WP_MF_CORE_SLUG ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range' => [
					'px' => [
						'min' => -500,
						'max' => 500,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => -50,
				],
				'selectors' => [
					'{{WRAPPER}} .tophive-testimonial__image' => 'right: {{SIZE}}{{UNIT}};'
				],
			]
		);

		$this->add_responsive_control(
			'image_border_width',
			[
				'label' => esc_html__( 'Border Width', WP_MF_CORE_SLUG ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 20,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .tophive-testimonial__image img' => 'border-width: {{SIZE}}{{UNIT}}',
				],
				'condition' => [
					'image_border' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'image_border_radius',
			[
				'label' => esc_html__( 'Border Radius', WP_MF_CORE_SLUG ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .tophive-testimonial__image' => 'border-radius: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->end_controls_section();

		$this->end_injection();

		$this->update_responsive_control(
			'width',
			[
				'selectors' => [
					'{{WRAPPER}} .tophive-main-swiper' => 'width: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->update_responsive_control(
			'slides_per_view',
			[
				'condition' => null,
			]
		);

		$this->update_control(
			'slides_to_scroll',
			[
				'condition' => null,
			]
		);

		$this->remove_control( 'effect' );
		$this->remove_responsive_control( 'height' );
		$this->remove_control( 'pagination_position' );
	}

	protected function add_repeater_controls( $repeater ) {
		$repeater->add_control(
			'content',
			[
				'label' => esc_html__( 'Content', WP_MF_CORE_SLUG ),
				'type' => \Elementor\Controls_Manager::TEXTAREA,
			]
		);

		$repeater->add_control(
			'image',
			[
				'label' => esc_html__( 'Image', WP_MF_CORE_SLUG ),
				'type' => \Elementor\Controls_Manager::MEDIA,
			]
		);

		$repeater->add_control(
			'name',
			[
				'label' => esc_html__( 'Name', WP_MF_CORE_SLUG ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => esc_html__( 'John Doe', WP_MF_CORE_SLUG ),
			]
		);

		$repeater->add_control(
			'title',
			[
				'label' => esc_html__( 'Title', WP_MF_CORE_SLUG ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => esc_html__( 'CEO', WP_MF_CORE_SLUG ),
			]
		);
		$repeater->add_control(
			'percent',
			[
				'label' => esc_html__( 'Rating', WP_MF_CORE_SLUG ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ '%' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
						'step' => 1,
					],
				],
			]
		);
	}

	protected function get_repeater_defaults() {
		$placeholder_image_src = \Elementor\Utils::get_placeholder_image_src();

		return [
			[
				'content' => esc_html__( 'I am slide content. Click edit button to change this text. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.', WP_MF_CORE_SLUG ),
				'name' => esc_html__( 'John Doe', WP_MF_CORE_SLUG ),
				'title' => esc_html__( 'CEO', WP_MF_CORE_SLUG ),
				'percent' => 0,
				'image' => [
					'url' => $placeholder_image_src,
				],
			],
			[
				'content' => esc_html__( 'I am slide content. Click edit button to change this text. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.', WP_MF_CORE_SLUG ),
				'name' => esc_html__( 'John Doe', WP_MF_CORE_SLUG ),
				'title' => esc_html__( 'CEO', WP_MF_CORE_SLUG ),
				'percent' => 0,
				'image' => [
					'url' => $placeholder_image_src,
				],
			],
			[
				'content' => esc_html__( 'I am slide content. Click edit button to change this text. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.', WP_MF_CORE_SLUG ),
				'name' => esc_html__( 'John Doe', WP_MF_CORE_SLUG ),
				'title' => esc_html__( 'CEO', WP_MF_CORE_SLUG ),
				'percent' => 0,
				'image' => [
					'url' => $placeholder_image_src,
				],
			],
		];
	}

	protected function print_slide( array $slide, array $settings, $element_key ) {
		$this->add_render_attribute( $element_key . '-testimonial', [
			'class' => 'tophive-testimonial',
		] );

		if ( ! empty( $slide['image']['url'] ) ) {
			$this->add_render_attribute( $element_key . '-image', [
				'src' => $this->get_slide_image_url( $slide, $settings ),
				'alt' => ! empty( $slide['name'] ) ? $slide['name'] : '',
			] );
		}
		$percent = !isset($slide['percent']) && empty($slide['percent']) ? 0 : $slide['percent']['size'];
		?>
		<div <?php echo $this->get_render_attribute_string( $element_key . '-testimonial' ); ?>>
			<?php if ( $slide['content'] ) : ?>
				<div class="tophive-testimonial__content ec-col-md-6 ec-offset-md-3">
					<div class="tophive-testimonial__text">
						<?php echo $slide['content']; ?>
					</div>
					<cite class="tophive-testimonial__cite">
					<?php if ( ! empty( $slide['name'] ) ): ?>
						<span class="tophive-testimonial__name"><?php echo $slide['name']; ?></span>
					<?php endif; ?>
					<?php if ( ! empty( $slide['title'] ) ):?>
						<span class="tophive-testimonial__title"><?php echo $slide['title'];?></span>
					<?php endif; ?>
					</cite>
					<br/>
					<div class="review-stars-rated testimonial">
						<div class="review-stars empty"></div>
						<div class="review-stars filled" style="width:<?php echo $percent; ?>%"></div>
						<!-- <div class="review-count">('. $item .')</div> -->
					</div>
				</div>
			<?php endif; ?>
			<?php if ( $slide['image']['url'] ) : ?>
				<div class="tophive-testimonial__footer">
					<div class="tophive-testimonial__image">
						<img <?php echo $this->get_render_attribute_string( $element_key . '-image' ); ?>>
					</div>
				</div>
			<?php endif; ?>
		</div>
		<?php
	}
	protected function print_slider( array $settings = null ) {
		$id = rand();
		if ( null === $settings ) {
			$settings = $this->get_active_settings();
		}

		$default_settings = [
			'container_class' => 'tophive-main-swiper',
			'video_play_icon' => true,
		];

		$settings = array_merge( $default_settings, $settings );
		?>
		
		<div class="th-slider-container th-slider-<?php echo $id; ?>">
			<?php foreach ( $settings['slides'] as $index => $slide ) :?>
				<?php $this->print_slide( $slide, $settings, 'slide-' . $index . '-' . $this->slide_prints_count ); ?>
			<?php endforeach; ?>
		</div>
		<?php if( 'yes' === $settings['show_arrows'] ): ?>
			<div class="th-slider-prev-next">
				<span class="th-prev"><i>
					<svg width="15px" height="15px" viewBox="0 0 16 16" class="bi bi-chevron-left" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
					  <path fill-rule="evenodd" d="M11.354 1.646a.5.5 0 0 1 0 .708L5.707 8l5.647 5.646a.5.5 0 0 1-.708.708l-6-6a.5.5 0 0 1 0-.708l6-6a.5.5 0 0 1 .708 0z"/>
					</svg>
				</i></span>
				<span class="th-next"><i>
					<svg width="15px" height="15px" viewBox="0 0 16 16" class="bi bi-chevron-right" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
					  <path fill-rule="evenodd" d="M4.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L10.293 8 4.646 2.354a.5.5 0 0 1 0-.708z"/>
					</svg>
				</i></span>
			</div>
		<?php endif; ?>
		<div class="th-slide-dot-nav">
			<?php foreach ( $settings['slides'] as $index => $slide ) :?>
				<span class="<?php echo $index;?> <?php echo $index == 0 ? 'active' : ''; ?>" data-slide="<?php echo $index; ?>"></span>
			<?php endforeach; ?>
		</div>

		<script>
			jQuery(document).ready(function(){
				var slide_<?php echo $id; ?> = new MetafansSlider({
					selector : '.th-slider-<?php echo $id; ?>',
					duration: <?php echo $settings['speed']; ?>,
					loop: true,
					onChange : addClassActive,
					perPage : 1,
				});
				function addClassActive(){
					jQuery('.th-slide-dot-nav span').removeClass('active');
					jQuery('.th-slide-dot-nav span.' + this.currentSlide ).addClass('active');
				}
				jQuery('.th-slider-prev-next .th-prev').on('click', function(){
					slide_<?php echo $id; ?>.prev();
				});
				jQuery('.th-slider-prev-next .th-next').on('click', function(){
					slide_<?php echo $id; ?>.next();
				});
				jQuery('.th-slide-dot-nav span').on('click', function(){
					jQuery('.th-slide-dot-nav span').removeClass('active');
					var _e = jQuery(this);
					var _i = _e.data('slide');
					slide_<?php echo $id; ?>.goTo(_i);
					_e.addClass('active');
				});
				<?php if('yes' === $settings['autoplay']): ?>
					setInterval( function(){slide_<?php echo $id; ?>.next()}, <?php echo $settings['autoplay_speed']; ?>);
				<?php endif; ?>

			});
		</script>
		<?php
	}

	protected function get_slide_image_url( $slide, array $settings ) {
		$image_url = \Elementor\Group_Control_Image_Size::get_attachment_image_src( $slide['image']['id'], 'image_size', $settings );

		if ( ! $image_url ) {
			$image_url = $slide['image']['url'];
		}

		return $image_url;
	}

	protected function render() {
		$this->print_slider();
	}
}
