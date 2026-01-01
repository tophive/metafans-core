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

trait Tophive_Card_UI_Controls_Trait {
    
    protected function add_heading_ui_control( $dynamic = true ) {
        $this->start_controls_section(
			'heading_section',
			array(
				'label' => esc_html__( 'Heading', TH_ELEMENTOR_SLUG ),
			)
		);

        if(!$dynamic){
            $this->add_control(
                'tophive_title_control',
                [
                    'label' => esc_html__( 'Title', TH_ELEMENTOR_SLUG ),
                    'type' => \Elementor\Controls_Manager::TEXTAREA,
                    'default' => esc_html__( 'Quick brown fox jumps', TH_ELEMENTOR_SLUG ),
                    'placeholder' => esc_html__( 'Type something...', TH_ELEMENTOR_SLUG ),
                ]
            );
        }
        $this->add_control(
			'title_tag',
			[
				'label' => esc_html__( 'Title Tag', TH_ELEMENTOR_SLUG ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options' => [
					'h1' => 'H1',
					'h2' => 'H2',
					'h3' => 'H3',
					'h4' => 'H4',
					'h5' => 'H5',
					'h6' => 'H6',
					'div' => 'div',
					'span' => 'span',
					'p' => 'p',
				],
				'default' => 'h3',
				'separator' => 'before',
			]
		);

        \Tophive_Elementor_UI_Helper::title_ui_controls($this, '');

		// Use ID as 2nd params to get the class back when using in code
        \Tophive_Elementor_UI_Helper::text_hover_effects($this, 'heading');

        $this->end_controls_section();
    }

    protected function add_content_ui_control( $dynamic = true ){
        $this->start_controls_section(
			'content_section',
			array(
				'label' => esc_html__( 'Content', TH_ELEMENTOR_SLUG ),
			)
		);
        $this->add_control(
			'show_content',
			[
				'label' => esc_html__( 'Show content?', TH_ELEMENTOR_SLUG ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', TH_ELEMENTOR_SLUG ),
				'label_off' => esc_html__( 'Off', TH_ELEMENTOR_SLUG ),
				'return_value' => 'yes',
				'default' => 'yes',
			]
		);

        $this->add_control(
            'words_count',
            [
                'label' => esc_html__( 'Words Count', 'plugin-name' ),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => [ 'words' ],
                'range' => [
                    'words' => [
                        'min' => 3,
                        'max' => 100,
                        'step' => 1,
                    ],
                ],
                'default' => [
                    'size' => 10,
                ]
            ]
        );
        
        $this->add_control(
			'show_content_on_hover',
			[
				'label' => esc_html__( 'Show content on hover?', TH_ELEMENTOR_SLUG ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', TH_ELEMENTOR_SLUG ),
				'label_off' => esc_html__( 'Off', TH_ELEMENTOR_SLUG ),
				'return_value' => 'yes',
				'default' => '',
				'prefix_class' => 'icon-box-content-show-onhover-'
			]
		);

		$this->add_control(
			'content_height_on_hover',
			[
				'label' => esc_html__( 'Content height', TH_ELEMENTOR_SLUG ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 300,
						'step' => 2,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 20,
				],
				'selectors' => [
					'{{WRAPPER}}.icon-box-content-show-onhover-yes .tophive-content-card:hover .tophive-card-description' => 'height: {{SIZE}}{{UNIT}};',
				],
				'condition' => array(
					'show_content_on_hover' => 'yes',
				),
			]
		);

		$this->end_controls_section();
    }

    protected function add_read_more_link( $dynamic = true ){
        $this->start_controls_section(
			'read_more_section',
			array(
				'label' => esc_html__( 'Read more', TH_ELEMENTOR_SLUG ),
			)
		);

		$this->add_control(
			'show_read_more_link',
			[
				'label' => esc_html__( 'Show read more link?', TH_ELEMENTOR_SLUG ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', TH_ELEMENTOR_SLUG ),
				'label_off' => esc_html__( 'Off', TH_ELEMENTOR_SLUG ),
				'return_value' => 'yes',
				'default' => '',
			]
		);

        // IF NON DYNAMIC
        if(!$dynamic){
            $this->add_control(
                'read_more_link',
                [
                    'label' => esc_html__( 'Read more link', TH_ELEMENTOR_SLUG ),
                    'type' => Controls_Manager::TEXT,
                    'default' => esc_html__( '#', TH_ELEMENTOR_SLUG ),
                    'placeholder' => esc_html__( 'Your read more button text.', TH_ELEMENTOR_SLUG ),
                ]
            );
        }

		$this->add_control(
			'read_more_text',
			[
				'label' => esc_html__( 'Read more link text', TH_ELEMENTOR_SLUG ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'Read more', TH_ELEMENTOR_SLUG ),
				'placeholder' => esc_html__( 'Read more...', TH_ELEMENTOR_SLUG ),
			]
		);
		$this->add_control(
			'show_read_more_link_on_hover',
			[
				'label' => esc_html__( 'Show on hover?', TH_ELEMENTOR_SLUG ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', TH_ELEMENTOR_SLUG ),
				'label_off' => esc_html__( 'Off', TH_ELEMENTOR_SLUG ),
				'return_value' => 'yes',
				'prefix_class' => 'read-more-link-hover-',
				'default' => '',
			]
		);

		$this->add_control(
			'add_more_link_icon',
			[
				'label' => esc_html__( 'Add readmore link icon?', TH_ELEMENTOR_SLUG ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', TH_ELEMENTOR_SLUG ),
				'label_off' => esc_html__( 'Off', TH_ELEMENTOR_SLUG ),
				'return_value' => 'yes',
				'default' => 'no',
				'separator' => 'before',
			]
		);

		$this->add_control(
			'more_link_icon',
			[
				'label' => esc_html__( 'Icon', TH_ELEMENTOR_SLUG ),
				'type' => Controls_Manager::ICONS,
				'default' => [
					'value' => 'fas fa-arrow-right',
					'library' => 'solid',
				],
				'condition' => array(
					'add_more_link_icon' => 'yes',
				),
			]
		);
		$this->add_responsive_control(
			'read_more_link_icon_position',
			[
				'label' => esc_html__( 'Icon Position', TH_ELEMENTOR_SLUG ),
				'type' => Controls_Manager::CHOOSE,
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
				'prefix_class' => 'read-more-icon-',
				'condition' => array(
					'add_more_link_icon' => 'yes',
				),
			]
		);


		$this->add_control(
			'read_more_icon_onhover',
			[
				'label' => esc_html__( 'On Hover Icon', TH_ELEMENTOR_SLUG ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', TH_ELEMENTOR_SLUG ),
				'label_off' => esc_html__( 'Off', TH_ELEMENTOR_SLUG ),
				'return_value' => 'yes',
				'default' => 'no',
				'separator' => 'before',
				'prefix_class' => 'read-more-icon-onhover-',
				'condition' => array(
					'add_more_link_icon' => 'yes',
				),
			]
		);

		$this->end_controls_section();

    }
}
