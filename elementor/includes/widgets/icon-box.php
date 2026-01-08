<?php

namespace TophiveElementor\Widgets;

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


class TH_Icon_Box extends Widget_Base {

    
	/**
	 * Get widget name.
	 *
	 * Retrieve heading widget name.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'th_icon_box';
	}

    
	/**
	 * Get widget title.
	 *
	 * Retrieve heading widget title.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget title.
	 */

	public function get_title() {
		return TH_ELEMENTOR_DISPLAY_NAME_SC . esc_html__( ' Icon box', TH_ELEMENTOR_SLUG );
	}

	/**
	 * Get widget icon.
	 *
	 * Retrieve heading widget icon.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-icon-box';
	}

    /**
	 * Get widget categories.
	 *
	 * Used to show widget under a category in the editor.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return array Widget categories.
	 */
	public function get_categories() {
		return [ 'th-general' ];
	}
    
	/**
	 * Get widget keywords.
	 *
	 * Retrieve the list of keywords the widget belongs to.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return array Widget keywords.
	 */
	public function get_keywords() {
		return [ 'icon' ];
	}
    
	/**
	 * Register widget controls.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function register_controls() {

        // Icon Section
		$this->start_controls_section(
			'icon_section',
			[
				'label' => esc_html__( 'Icon', TH_ELEMENTOR_SLUG ),
			]
		);

        $this->add_control(
            'icon_type', [
                'label'       => esc_html__( 'Icon Type', TH_ELEMENTOR_SLUG ),
                'type'        => Controls_Manager::CHOOSE,
                'label_block' => false,
                'options'     => [
                    'none' => [
                        'title' => esc_html__( 'None', TH_ELEMENTOR_SLUG ),
                        'icon'  => 'fa fa-ban',
                    ],
                    'icon' => [
                        'title' => esc_html__( 'Icon', TH_ELEMENTOR_SLUG ),
                        'icon'  => 'fa fa-paint-brush',
                    ],
                    'image' => [
                        'title' => esc_html__( 'Image', TH_ELEMENTOR_SLUG ),
                        'icon'  => 'fa fa-image',
                    ],
                ],
                'default'       => 'none',
            ]
        );

		$this->add_control(
			'ib_icon',
			[
				'label' => esc_html__( 'Icon', TH_ELEMENTOR_SLUG ),
				'type' => Controls_Manager::ICONS,
				'default' => [
					'value' => 'far fa-check-circle',
					'library' => 'regular',
				],
				'condition' => array(
					'icon_type' => 'icon',
				),
			]
		);

		$this->add_control(
			'image',
			[
				'label' => esc_html__( 'Image', TH_ELEMENTOR_SLUG ),
				'type' => Controls_Manager::MEDIA,
				'condition' => array(
					'icon_type' => 'image',
				),
			]
		);

		$this->end_controls_section();

		// Heading Section
		$this->start_controls_section(
			'heading_section',
			array(
				'label' => esc_html__( 'Heading', TH_ELEMENTOR_SLUG ),
			)
		);

		$this->add_control(
			'tophive_title_control',
			[
				'label' => esc_html__( 'Title', TH_ELEMENTOR_SLUG ),
				'type' => \Elementor\Controls_Manager::TEXTAREA,
				'default' => esc_html__( 'Quick brown fox jumps', TH_ELEMENTOR_SLUG ),
				'placeholder' => esc_html__( 'Type something...', TH_ELEMENTOR_SLUG ),
			]
		);
		$this->add_control(
			'tophive_title_control_tag',
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

		\Tophive_Elementor_UI_Helper::add_ui_controls($this, 'title', '', false);


		$this->end_controls_section();

		// Content Section
		$this->start_controls_section(
			'content_section',
			array(
				'label' => esc_html__( 'Content', TH_ELEMENTOR_SLUG ),
			)
		);

		$this->add_control(
			'content',
			[
				'label' => esc_html__( 'IconBox Content', TH_ELEMENTOR_SLUG ),
				'type' => Controls_Manager::TEXTAREA,
				'default' => esc_html__( 'Simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s', TH_ELEMENTOR_SLUG ),
				'placeholder' => esc_html__( 'Type your description here', TH_ELEMENTOR_SLUG ),
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
					'{{WRAPPER}}.icon-box-content-show-onhover-yes:hover .icon-box-text' => 'height: {{SIZE}}{{UNIT}};',
				],
				'condition' => array(
					'show_content_on_hover' => 'yes',
				),
			]
		);


		$this->end_controls_section();

		$this->start_controls_section(
			'link_section',
			array(
				'label' => esc_html__( 'Link/URL', TH_ELEMENTOR_SLUG ),
			)
		);


		$this->add_control(
			'icon_box_link',
			[
				'label' => esc_html__( 'Content URL/Link', TH_ELEMENTOR_SLUG ),
				'type' => Controls_Manager::URL,
				'placeholder' => esc_html__( 'https://my-url.com', TH_ELEMENTOR_SLUG ),
				'show_external' => true,
				'default' => [
					'url' => '',
					'is_external' => true,
					'nofollow' => true,
				],
			]
		);

		$this->end_controls_section();

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

		$this->add_control(
			'read_more_link',
			[
				'label' => esc_html__( 'Read more link', TH_ELEMENTOR_SLUG ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( '#', TH_ELEMENTOR_SLUG ),
				'placeholder' => esc_html__( 'Your read more button text.', TH_ELEMENTOR_SLUG ),
			]
		);

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

		// Label Section
		$this->start_controls_section(
			'label_section',
			array(
				'label' => esc_html__( 'Watermark / Label', TH_ELEMENTOR_SLUG ),
			)
		);

		$this->add_control(
			'show_label',
			[
				'label' => esc_html__( 'Add Watermark/Label to iconbox', TH_ELEMENTOR_SLUG ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', TH_ELEMENTOR_SLUG ),
				'label_off' => esc_html__( 'Off', TH_ELEMENTOR_SLUG ),
				'return_value' => 'yes',
				'default' => '',
			]
		);

		$this->add_control(
			'label',
			[
				'label' => esc_html__( 'Content', TH_ELEMENTOR_SLUG ),
				'type' => Controls_Manager::TEXTAREA,
				'default' => esc_html__( 'Label', TH_ELEMENTOR_SLUG ),
				'placeholder' => esc_html__( 'Add label text', TH_ELEMENTOR_SLUG ),
				'condition' => array(
					'show_label' => 'yes',
				),
			]
		);


		$this->add_control(
			'label_position',
			[
				'label' => esc_html__( 'Position', TH_ELEMENTOR_SLUG ),
				'type' => Controls_Manager::SELECT,
				'default' => 'floating',
				'options' => [
					'floating' => esc_html__( 'Floating', TH_ELEMENTOR_SLUG ),
					'in_content' => esc_html__( 'Top of Content', TH_ELEMENTOR_SLUG ),
					'in_title' => esc_html__( 'Top of Title', TH_ELEMENTOR_SLUG ),
				],
				'condition' => array(
					'show_label' => 'yes',
				),
			]
		);

		$this->add_responsive_control(
			'label_offset_top',
			[
				'label' => esc_html__( 'Label top offset', TH_ELEMENTOR_SLUG ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range' => [
					'px' => [
						'min' => -500,
						'max' => 500,
						'step' => 1,
					],
					'%' => [
						'min' => -100,
						'max' => 100,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 0,
				],
				'selectors' => [
					'{{WRAPPER}} .iconbox-label' => 'top: {{SIZE}}{{UNIT}};',
				],
				'condition' => array(
					'show_label' => 'yes',
					'label_position' => 'floating'
				),
			]
		);

		$this->add_responsive_control(
			'label_offset_right',
			[
				'label' => esc_html__( 'Label right offset', TH_ELEMENTOR_SLUG ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range' => [
					'px' => [
						'min' => -500,
						'max' => 500,
						'step' => 1,
					],
					'%' => [
						'min' => -100,
						'max' => 100,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 0,
				],
				'selectors' => [
					'{{WRAPPER}} .iconbox-label' => 'inset-inline-start: auto; inset-inline-end: {{SIZE}}{{UNIT}};',
				],
				'condition' => array(
					'show_label' => 'yes',
					'label_position' => 'floating'
				),
			]
		);

		$this->end_controls_section();

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
				'label' => esc_html__( 'Icon Position', TH_ELEMENTOR_SLUG ),
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
				'label' => esc_html__( 'Icon Spacing', TH_ELEMENTOR_SLUG ),
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
					'{{WRAPPER}}' => '--icon-box-icon-margin: {{SIZE}}{{UNIT}}',
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
					'{{WRAPPER}} .icon-box__content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);


		\Tophive_Elementor_Style_Helper::add_ui_style_controls($this, 'box', '{{WRAPPER}}.tophive-icon-box .icon-box__inner', '', false, '', ['text_color']);
        

		$this->end_controls_section();

		\Tophive_Elementor_Style_Helper::add_ui_style_controls($this, 'icon', '{{WRAPPER}}.tophive-icon-box .icon-box__icon', '{{WRAPPER}}.tophive-icon-box:hover .icon-box__icon');


		\Tophive_Elementor_Style_Helper::add_ui_style_controls($this, 'text_icon', '{{WRAPPER}}.tophive-icon-box .tophive-link', '{{WRAPPER}}.tophive-icon-box .tophive-link:hover', true, 'Read more');


		// Style Tab
		$this->start_controls_section(
			'style_section',
			[
				'label' => esc_html__( 'Content', TH_ELEMENTOR_SLUG ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'icon_box_title_typography',
				'label' => esc_html__( 'Title Typography', TH_ELEMENTOR_SLUG ),
				'selector' => '{{WRAPPER}} .icon-title__heading .icon-box__title',
			]
		);
		
		$this->add_responsive_control(
			'title_spacing',
			[
				'label' => esc_html__( 'Title Spacing', TH_ELEMENTOR_SLUG ),
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
					'{{WRAPPER}}.tophive-icon-box .icon-title__heading' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'iconbox_content_typography',
				'label' => esc_html__( 'Content Typography', TH_ELEMENTOR_SLUG ),
				'selector' => '{{WRAPPER}} .icon-box-text',
			]
		);
		$this->add_responsive_control(
			'conten_text_spacing',
			[
				'label' => esc_html__( 'Text Spacing', TH_ELEMENTOR_SLUG ),
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
					'size' => 5,
				],
				'selectors' => [
					'{{WRAPPER}}.tophive-icon-box .icon-box-text' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);
		
		
		$this->add_control(
			'style_colors_heading',
			[
				'label' => esc_html__( 'Colors', TH_ELEMENTOR_SLUG ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->start_controls_tabs(
			'style_color_tabs'
		);

		// Normal State
		$this->start_controls_tab(
			'style_color_normal_tab',
			[
				'label' => esc_html__( 'Normal', TH_ELEMENTOR_SLUG ),
			]
		);
			
			$this->add_control(
				'h_color',
				[
					'label' => esc_html__( 'Heading Color', TH_ELEMENTOR_SLUG ),
					'type' => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .icon-title__heading .icon-box__title' => 'color: {{VALUE}}',
					],
					'separator' => 'before'
				]
			);

			$this->add_control(
				'content_color',
				[
					'label' => esc_html__( 'Content Color', TH_ELEMENTOR_SLUG ),
					'type' => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .icon-box-text' => 'color: {{VALUE}}',
					],
				]
			);

		$this->end_controls_tab();

		// Hover State
		$this->start_controls_tab(
			'style_color_hover_tab',
			[
				'label' => esc_html__( 'Hover', TH_ELEMENTOR_SLUG ),
			]
		);

			
			$this->add_control(
				'h_hcolor',
				[
					'label' => esc_html__( 'Heading Color', TH_ELEMENTOR_SLUG ),
					'type' => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}}:hover .lqd-iconbox-heading' => 'color: {{VALUE}}',
					],
					'separator' => 'before',
				]
			);

			$this->add_control(
				'content_color_hover',
				[
					'label' => esc_html__( 'Content Color', TH_ELEMENTOR_SLUG ),
					'type' => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}}:hover .icon-box-text' => 'color: {{VALUE}}',
					],
				]
			);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->end_controls_section();

		\Tophive_Elementor_Style_Helper::add_ui_style_controls($this, 'ripple', '{{WRAPPER}}.tophive-icon-box .icon-box__icon', '', true, 'Ripple Effects', ['custom_select_scope']);

		\Tophive_Elementor_Style_Helper::add_ui_style_controls($this, 'transform', '{{WRAPPER}}.tophive-icon-box', '', true, 'Transform');
		

		// Label Style Section
		$this->start_controls_section(
			'label_style_section',
			[
				'label' => esc_html__( 'Label', TH_ELEMENTOR_SLUG ),
				'tab'   => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_label' => 'yes',
				],
			]
		);

		// Label Text Color
		$this->add_control(
			'label_text_color',
			[
				'label' => esc_html__( 'Text Color', TH_ELEMENTOR_SLUG ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .iconbox-label' => 'color: {{VALUE}};',
				],
			]
		);

		// Label Background Color
		$this->add_control(
			'label_background_color',
			[
				'label' => esc_html__( 'Background Color', TH_ELEMENTOR_SLUG ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .iconbox-label' => 'background-color: {{VALUE}};',
				],
			]
		);

		// Label Padding
		$this->add_responsive_control(
			'label_padding',
			[
				'label' => esc_html__( 'Padding', TH_ELEMENTOR_SLUG ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .iconbox-label' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		// Label Margin
		$this->add_responsive_control(
			'label_margin',
			[
				'label' => esc_html__( 'Margin', TH_ELEMENTOR_SLUG ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .iconbox-label' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		// Label Box Shadow
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'label_box_shadow',
				'label' => esc_html__( 'Box Shadow', TH_ELEMENTOR_SLUG ),
				'selector' => '{{WRAPPER}} .iconbox-label',
			]
		);

		$this->end_controls_section();

    }
	protected function render() {
		$settings = $this->get_settings_for_display();
	
		// Handle link attributes if provided
		$has_link = !empty($settings['icon_box_link']['url']);
		$link_target = $has_link && !empty($settings['icon_box_link']['is_external']) ? ' target="_blank"' : '';
		$link_nofollow = $has_link && !empty($settings['icon_box_link']['nofollow']) ? ' rel="nofollow"' : '';
		$link_attributes = $has_link ? ' href="' . esc_url($settings['icon_box_link']['url']) . '"' . $link_target . $link_nofollow : '';
	
		// Handle Watermark / Label attributes
		$has_label = !empty($settings['show_label']) && $settings['show_label'] === 'yes' && !empty($settings['label']);
		$label_classes = 'iconbox-label iconbox-label-' . esc_attr($settings['label_position']);
	
		// Inline styles for floating label
		$label_styles = '';
		if ($has_label && $settings['label_position'] === 'floating') {
			if ($settings['label_offset_top']['size'] !== '') {
				$label_styles .= 'top: ' . esc_attr($settings['label_offset_top']['size']) . esc_attr($settings['label_offset_top']['unit']) . '; ';
			}
			if ($settings['label_offset_right']['size'] !== '') {
				$label_styles .= 'right: ' . esc_attr($settings['label_offset_right']['size']) . esc_attr($settings['label_offset_right']['unit']) . '; ';
			}
		}
	
		// Render the label
		$label_html = $has_label ? '<div class="' . esc_attr($label_classes) . '" style="' . esc_attr($label_styles) . '">' . esc_html($settings['label']) . '</div>' : '';
		?>
	
		<?php if ($has_link): ?>
			<a class="icon-box__link" <?php echo $link_attributes; ?>>
		<?php endif; ?>
	
		<div class="icon-box__inner">
	
			<?php if ($has_label && $settings['label_position'] === 'floating'): ?>
				<?php echo $label_html; // Floating label ?>
			<?php endif; ?>
	
			<?php if ($settings['icon_type'] == 'icon' || $settings['icon_type'] == 'image'): ?>
				<div class="icon-box__icon">
					<?php
					if ($settings['icon_type'] == 'icon') {
						\Elementor\Icons_Manager::render_icon($settings['ib_icon'], ['aria-hidden' => 'true']);
					} elseif ($settings['icon_type'] == 'image' && !empty($settings['image']['url'])) {
						echo '<img src="' . esc_url($settings['image']['url']) . '" alt="' . esc_attr(get_post_meta($settings['image']['id'], '_wp_attachment_image_alt', true)) . '">';
					}
					?>
				</div>
			<?php endif; ?>
	
			<div class="icon-box__content">
	
				<?php if ($has_label && $settings['label_position'] === 'in_title'): ?>
					<?php echo $label_html; // Label above title ?>
				<?php endif; ?>
	
				<div class="icon-title__heading">
					<<?php echo esc_html($settings['tophive_title_control_tag']); ?> class="icon-box__title">
						<?php echo esc_html($settings['tophive_title_control']); ?>
					</<?php echo esc_html($settings['tophive_title_control_tag']); ?>>
					<?php if ('yes' == $settings['show_title_icon']) {
						Icons_Manager::render_icon($settings['tophive_title_icon'], ['aria-hidden' => 'true']);
					} ?>
				</div>
	
				<?php if ($has_label && $settings['label_position'] === 'in_content'): ?>
					<?php echo $label_html; // Label above content ?>
				<?php endif; ?>
	
				<?php if (!empty($settings['content'])): ?>
					<p class="icon-box-text"><?php echo esc_html($settings['content']); ?></p>
				<?php endif; ?>
	
				<?php if ('yes' === $settings['show_read_more_link']): ?>
					<div class="tophive-card-readmore">
						<a class="tophive-link" href="<?php echo esc_url($settings['read_more_link']); ?>">
							<?php echo esc_html($settings['read_more_text']); ?>
							<?php if ('yes' == $settings['add_more_link_icon']) {
								Icons_Manager::render_icon($settings['more_link_icon'], ['aria-hidden' => 'true']);
							} ?>
						</a>
					</div>
				<?php endif; ?>
			</div>
	
		</div>
	
		<?php if ($has_link): ?>
			</a>
		<?php endif; ?>
		<?php
	}
	protected function content_template() {
		?>
		<#
		const hasLink = settings.icon_box_link && settings.icon_box_link.url;
		const tagLinkStart = hasLink ? `<a class="icon-box__link" href="${ settings.icon_box_link.url }"${ settings.icon_box_link.is_external ? ' target="_blank"' : '' }${ settings.icon_box_link.nofollow ? ' rel="nofollow"' : '' }>` : '';
		const tagLinkEnd = hasLink ? '</a>' : '';
	
		const hasLabel = settings.show_label === 'yes' && settings.label;
		const labelClass = hasLabel ? `iconbox-label iconbox-label-${ settings.label_position }` : '';
		let labelStyles = '';
	
		if (hasLabel && settings.label_position === 'floating') {
			if (settings.label_offset_top?.size !== '') {
				labelStyles += `top: ${ settings.label_offset_top.size }${ settings.label_offset_top.unit }; `;
			}
			if (settings.label_offset_right?.size !== '') {
				labelStyles += `right: ${ settings.label_offset_right.size }${ settings.label_offset_right.unit }; `;
			}
		}
	
		const labelHTML = hasLabel ? `<div class="${ labelClass }" style="${ labelStyles }">${ settings.label }</div>` : '';
	
		const iconHTML = (settings.icon_type === 'icon' && settings.icon?.value)
			? elementor.helpers.renderIcon( view, settings.ib_icon, { 'aria-hidden': true }, 'i', 'object' ).value
			: (settings.icon_type === 'image' && settings.image?.url)
				? `<img src="${ settings.image.url }" alt="">`
				: '';
		#>
	
		{{{ tagLinkStart }}}
			<div class="icon-box__inner">
				
				<# if (hasLabel && settings.label_position === 'floating') { #>
					{{{ labelHTML }}}
				<# } #>
	
				<# if (settings.icon_type === 'icon' || settings.icon_type === 'image') { #>
					<div class="icon-box__icon">
						{{{ iconHTML }}}
					</div>
				<# } #>
	
				<div class="icon-box__content">
	
					<# if (hasLabel && settings.label_position === 'in_title') { #>
						{{{ labelHTML }}}
					<# } #>
	
					<div class="icon-title__heading">
						<{{ settings.tophive_title_control_tag }} class="icon-box__title">
							{{{ settings.tophive_title_control }}}
						</{{ settings.tophive_title_control_tag }}>
						
						<# if (settings.show_title_icon === 'yes') {
							const titleIconHTML = elementor.helpers.renderIcon( view, settings.tophive_title_icon, { 'aria-hidden': true }, 'i', 'object' );
							if (titleIconHTML && titleIconHTML.rendered) { #>
								{{{ titleIconHTML.value }}}
							<# }
						} #>
					</div>
	
					<# if (hasLabel && settings.label_position === 'in_content') { #>
						{{{ labelHTML }}}
					<# } #>
	
					<# if (settings.content) { #>
						<p class="icon-box-text">{{{ settings.content }}}</p>
					<# } #>
	
					<# if (settings.show_read_more_link === 'yes') { #>
						<div class="tophive-card-readmore">
							<a class="tophive-link" href="{{ settings.read_more_link }}">
								{{{ settings.read_more_text }}}
								<# if (settings.add_more_link_icon === 'yes') {
									const moreIcon = elementor.helpers.renderIcon( view, settings.more_link_icon, { 'aria-hidden': true }, 'i', 'object' );
									if (moreIcon && moreIcon.rendered) { #>
										{{{ moreIcon.value }}}
									<# }
								} #>
							</a>
						</div>
					<# } #>
	
				</div>
			</div>
		{{{ tagLinkEnd }}}
		<?php
	}
	
}

\Elementor\Plugin::instance()->widgets_manager->register( new TH_Icon_Box() );
