<?php

namespace TophiveElementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Typography;
use Elementor\Scheme_Color;
use Elementor\Scheme_Typography;
use Elementor\Utils;
use Elementor\Control_Media;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Background;
use Elementor\Repeater;



class Tophive_Advanced_Heading extends Widget_Base {

	public function __construct($data = [], $args = null) {
		parent::__construct($data, $args);
	}
    
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
		return 'tophive-advanced-heading';
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
		return TH_ELEMENTOR_DISPLAY_NAME_SC . esc_html__( 'Advanced Heading', TH_ELEMENTOR_SLUG );
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
		return 'eicon-e-heading';
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
		return [ 'text', 'paragraphs' ];
	}

	public function get_script_depends() {
		return [ 'tophive-elementor-bundle' ];
	}
    
	/**
	 * Register widget controls.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function register_controls() {

		$this->start_controls_section(
			'section_section_title',
			array(
				'label' => __( 'Title', 'hub-elementor-elementor' ),
			)
		);

		$this->tophive_advanced_text( $this );
		
		$this->add_control(
			'content',
			[
				'label' => __( 'Title', 'tophive-elementor' ),
				'type' => Controls_Manager::TEXTAREA,
				'dynamic' => array(
					'active' => true,
				),
				'rows' => '6',
				'default' => 'Add Your Heading Text Here',
				'condition' => [
					'advanced_text_enable!' => 'yes', 
				]
			]
		);

		$this->add_control(
			'tag',
			[
				'label' => esc_html__( 'Element Tag', 'tophive-elementor' ),
				'type' => Controls_Manager::SELECT,
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
				'default' => 'h2',
			]
		);

		$this->add_responsive_control(
			'alignment',
			[
				'label' => __( 'Alignment', 'tophive-elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => __( 'Left', 'tophive-elementor' ),
						'icon' => 'fa fa-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'tophive-elementor' ),
						'icon' => 'fa fa-align-center',
					],
					'right' => [
						'title' => __( 'Right', 'tophive-elementor' ),
						'icon' => 'fa fa-align-right',
					],
					'justify' => [
						'title' => __( 'Justify', 'tophive-elementor' ),
						'icon' => 'fa fa-align-justify',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .ld-fancy-heading' => 'text-align: {{VALUE}}',
				],
				'toggle' => true,
			]
		);

		$this->end_controls_section();

        \Tophive_Elementor_Style_Helper::add_ui_style_controls($this, 'text', '{{WRAPPER}} .tophive-advanced-heading', '', true, 'Styles');


		// Animation Section
		$this->start_controls_section(
			'split_text_section',
			[
				'label' => __( 'Split text', 'tophive-elementor' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'enable_text_split',
			[
				'label' => __( 'Enable text split?', 'tophive-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'On', 'tophive-elementor' ),
				'label_off' => __( 'Off', 'tophive-elementor' ),
				'return_value' => 'true',
				'default' => '',
			]
		);

		$this->add_control(
			'split_type',
			[
				'label' => __( 'Splitting type', 'tophive-elementor' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'lines',
				'options' => [
					'lines'  => __( 'Lines', 'tophive-elementor' ),
					'words' => __( 'Words', 'tophive-elementor' ),
					'chars' => __( 'Characters', 'tophive-elementor' ),
				],
                'prefix_class' => 'split-by-',
				'condition' => [
					'enable_text_split' => 'true',
				],
			]
		);

		$this->add_control(
			'use_mask',
			[
				'label' => __( 'Enabled mask?', 'tophive-elementor' ),
				'description' => __('Check to enable mask on title to use it in animation', 'tophive-elementor'),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'On', 'tophive-elementor' ),
				'label_off' => __( 'Off', 'tophive-elementor' ),
				'return_value' => 'true',
				'default' => '',
                'prefix_class' => 'th-split-mask ',
				'condition' => [
					'enable_text_split' => 'true',
				],
			]
		);

        $this->end_controls_section();

		$this->start_controls_section(
			'rotator_section',
			[
				'label' => __( 'Text rotate', 'tophive-elementor' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'enable_text_rotator',
			[
				'label' => __( 'Enable text rotator?', 'tophive-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'On', 'tophive-elementor' ),
				'label_off' => __( 'Off', 'tophive-elementor' ),
				'return_value' => 'yes',
				'default' => 'no',
				'separator' => 'before'
			]
		);

		$this->add_control(
			'rotator_type',
			[
				'label' => __( 'Rotation mode', 'tophive-elementor' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'slider',
				'options' => [
					'inline'  => __( 'Inline', 'tophive-elementor' ),
					'list' => __( 'List', 'tophive-elementor' ),
					'circle' => __( 'Text Circle', 'tophive-elementor' ),
				],
				'prefix_class' => 'rotate-mode-',
				'condition' => array(
					'enable_text_rotator' => 'yes',
				),
			]
		);

        $this->add_control(
            'rotator_placeholder_note',
            [
                'type' => \Elementor\Controls_Manager::RAW_HTML,
                'raw' => '<strong>Note:</strong> Use <code>&#123;&#123;TEXT_ROTATOR&#125;&#125;</code> in your text where the rotating word should appear.',
                'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
                'condition' => [
                    'enable_text_rotator' => 'yes',
                    'rotator_type' => 'inline',
                ],
            ]
        );
		
		$this->add_responsive_control(
			'rotator_visible_words_max',
			[
				'label' => esc_html__( 'Visible words', 'elementor' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'unit' => 'px',
					'size' => '3'
				],
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 11,
					]
				],
				'condition' => array(
					'enable_text_rotator' => 'yes',
					'rotator_type' => 'list',
				),
			]
		);

		$this->add_responsive_control(
			'rotator_heights',
			[
				'label' => esc_html__( 'Container height', 'elementor' ),
				'description' => esc_html__( 'Adjust your container height', 'elementor' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'unit' => 'px',
					'size' => '120'
				],
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 50,
						'max' => 1000,
					]
				],
				'selectors' => [
					'{{WRAPPER}}' => '--text-rotator-list-height: {{SIZE}}{{UNIT}};',
				],
				'condition' => array(
					'enable_text_rotator' => 'yes',
					'rotator_type' => 'list',
				),
			]
		);

		$this->add_responsive_control(
			'rotator_text_font_size',
			[
				'label' => esc_html__( 'Font size', 'elementor' ),
				'description' => esc_html__( 'Adjust your font size', 'elementor' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'unit' => 'px',
					'size' => '30'
				],
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 50,
						'max' => 1000,
					]
				],
				'selectors' => [
					'{{WRAPPER}}' => '--text-rotator-font-size: {{SIZE}}{{UNIT}};',
				],
				'condition' => array(
					'enable_text_rotator' => 'yes',
					'rotator_type' => 'list',
				),
			]
		);
		$this->add_responsive_control(
			'rotator_highlighter_height',
			[
				'label' => esc_html__( 'Highlighter height', 'elementor' ),
				'description' => esc_html__( 'Choose a word to highlight', 'elementor' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'unit' => 'px',
					'size' => '30'
				],
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 10,
						'max' => 200,
					]
				],
				'selectors' => [
					'{{WRAPPER}}' => '--text-rotator-heighlighter-height: {{SIZE}}{{UNIT}};',
				],
				'condition' => array(
					'enable_text_rotator' => 'yes',
					'rotator_type' => 'list',
				),
			]
		);
		$this->add_responsive_control(
			'rotator_highlighter_position',
			[
				'label' => esc_html__( 'Highlighter position', 'elementor' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'unit' => 'px',
					'size' => '40'
				],
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => -100,
						'max' => 400,
					]
				],
				'selectors' => [
					'{{WRAPPER}}' => '--text-rotator-heighlighter-position: {{SIZE}}{{UNIT}};',
				],
				'condition' => array(
					'enable_text_rotator' => 'yes',
					'rotator_type' => 'list',
				),
			]
		);

		\Tophive_GSAP_Animation_Helper::custom_settings( $this, 'th_ra_' ,['enable_text_rotator' => 'yes', 'rotator_type' => 'inline'] );
		\Tophive_GSAP_Animation_Helper::animation_range_controls( $this, 'th_ra_' , [
				'enable_text_rotator' => 'yes',
				'th_ra_preset' => 'custom',
				'rotator_type' => 'inline'
		] );

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
                'default'       => 'image',
				'condition' => array(
					'enable_text_rotator' => 'yes',
					'rotator_type' => 'circle'
				),
            ]
        );

		$this->add_control(
			'icon',
			[
				'label' => esc_html__( 'Icon', TH_ELEMENTOR_SLUG ),
				'type' => Controls_Manager::ICONS,
				'default' => [
					'value' => 'far fa-check-circle',
					'library' => 'regular',
				],
				'condition' => array(
					'icon_type' => 'icon',
					'enable_text_rotator' => 'yes',
					'rotator_type' => 'circle'
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
					'enable_text_rotator' => 'yes',
					'rotator_type' => 'circle'
				),
			]
		);

		$this->add_control(
			'circle_colors',
			[
				'label' => __( 'Circle text color', 'tophive-elementor' ),
				'type' => Controls_Manager::COLOR,
				'condition' => array(
					'enable_text_rotator' => 'yes',
					'rotator_type' => 'circle'
				),
				'selectors' => [
					'{{WRAPPER}} .tophive-text-circle-container textpath' => 'fill: {{VALUE}};',
				],
			]
		);
		$this->add_control(
			'circle_text_size',
			[
				'label' => __( 'Text font size', 'tophive-elementor' ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'default' => [
					'size' => 16,
				],
				'condition' => array(
					'enable_text_rotator' => 'yes',
					'rotator_type' => 'circle'
				),
				'selectors' => [
					'{{WRAPPER}} .tophive-text-circle-container textpath' => 'font-size: {{SIZE}}{{UNIT}}',
				],
			]
		);
		$this->add_control(
			'circle_size',
			[
				'label' => __( 'Circle size', 'tophive-elementor' ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'default' => [
					'size' => 250,
				],
				'range' => [
					'px' => [
						'min' => 50,
						'max' => 500,
						'step' => 1,
					]
				],
				'condition' => array(
					'enable_text_rotator' => 'yes',
					'rotator_type' => 'circle'
				),
				'selectors' => [
					'{{WRAPPER}}  .tophive-text-circle-container' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_control(
			'circle_image_size',
			[
				'label' => __( 'Center circle size', 'tophive-elementor' ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'default' => [
					'size' => 200,
				],
				'range' => [
					'px' => [
						'min' => 50,
						'max' => 500,
						'step' => 1,
					]
				],
				'condition' => array(
					'enable_text_rotator' => 'yes',
					'rotator_type' => 'circle'
				),
				'selectors' => [
					'{{WRAPPER}} .tophive-text-circle-container .image-container' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_control(
			'circle_image_spacing',
			[
				'label' => __( 'Image spacing', 'tophive-elementor' ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'default' => [
					'size' => 0,
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
						'step' => 1,
					]
				],
				'condition' => array(
					'enable_text_rotator' => 'yes',
					'rotator_type' => 'circle'
				),
				'selectors' => [
					'{{WRAPPER}} .tophive-text-circle-container .image-container' => 'padding: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			[
				'label' => 'Circle border',
				'name' => 'centre_circle_border',
				'selector' => '{{WRAPPER}} .tophive-text-circle-container .image-container',
				'condition' => array(
					'enable_text_rotator' => 'yes',
					'rotator_type' => 'circle'
				),
			]
		);
		$this->add_responsive_control(
			'rotation_direction',
			[
				'label' => __( 'Direction', 'tophive-elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'-360deg' => [
						'title' => __( 'Anti Clockwise', 'tophive-elementor' ),
						'icon' => 'eicon-angle-left',
					],
					'360deg' => [
						'title' => __( 'Clockwise', 'tophive-elementor' ),
						'icon' => 'eicon-angle-right',
					],
				],
				'selectors' => [
					'{{WRAPPER}}' => '--tophive-text-rotation-direction: {{VALUE}}',
				],
				'prefix_class' => 'tophive-rotate-',
				'condition' => array(
					'enable_text_rotator' => 'yes',
					'rotator_type' => 'circle'
				),
			]
		);
		$this->add_responsive_control(
			'rotation_direction_list',
			[
				'label' => __( 'Rotation Direction', 'tophive-elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'to-top' => [
						'title' => __( 'Towards top', 'tophive-elementor' ),
						'icon' => 'eicon-v-align-top',
					],
					'to-bottom' => [
						'title' => __( 'Towards bottom', 'tophive-elementor' ),
						'icon' => 'eicon-v-align-bottom',
					],
				],
				'condition' => array(
					'enable_text_rotator' => 'yes',
					'rotator_type' => 'list'
				),
			]
		);

		$this->add_control(
			'rotator_highlighter',
			[
				'label' => __( 'Highlight type', 'tophive-elementor' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'text-mask',
				'options' => [
					'none'  		=> __( 'None', 'tophive-elementor' ),
					'text-mask' 	=> __( 'Text mask', 'tophive-elementor' ),
					'background' 	=> __( 'Background', 'tophive-elementor' ),
					'target' 	=> __( 'Target', 'tophive-elementor' ),
				],
				'prefix_class' => 'rotator-highlighter-mode-',
				'condition' => array(
					'enable_text_rotator' => 'yes',
					'rotator_type' => 'list',
				),
			]
		);

		$this->add_control(
			'list_word_color',
			[
				'label' => __( 'List Text Color', 'tophive-elementor' ),
				'type' => Controls_Manager::COLOR,
				'condition' => array(
					'enable_text_rotator' => 'yes',
					'rotator_type' => 'list',
				),
				'selectors' => [
					'{{WRAPPER}}' => '--list-text-color: {{VALUE}}'
				],
			]
		);

		$this->add_control(
			'list_active_word_color',
			[
				'label' => __( 'Active Text Color', 'tophive-elementor' ),
				'type' => Controls_Manager::COLOR,
				'condition' => array(
					'enable_text_rotator' => 'yes',
					'rotator_type' => 'list',
					'rotator_highlighter' => 'background',
				),
				'selectors' => [
					'{{WARAPPER}}.rotate-mode-list.rotator-highlighter-mode-background .text-rotate-keyword.active' => 'color: {{VALUE}}'
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Background::get_type(),
			[
				'label' => __( 'Active color', 'tophive-elementor' ),
				'name' => 'rotator_list_active_word_color',
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .text-rotator-shade::after, {{WARAPPER}}.rotate-mode-list.rotator-highlighter-mode-background .text-rotator-spacer',
				'condition' => array(
					'enable_text_rotator' => 'yes',
					'rotator_type' => 'list',
				),
			]
		);

		$this->add_control(
			'container_mask_color',
			[
				'label' => __( 'Container mask color', 'tophive-elementor' ),
				'type' => Controls_Manager::COLOR,
				'condition' => array(
					'enable_text_rotator' => 'yes',
					'rotator_type' => 'list',
				),
				'selectors' => [
					'{{WRAPPER}}' => '--container-mask-color: {{VALUE}}'
				],
			]
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'word', [
				'label' => __( 'Title word', 'tophive-elementor' ),
				'type' => Controls_Manager::TEXT,
				'label_block' => true,
			]
		);

		

		$this->add_control(
			'items',
			[
				'label' => __( 'Text rotator words', 'tophive-elementor' ),
				'type' => Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'title_field' => '{{{ word }}}',
				'condition' => array(
					'enable_text_rotator' => 'yes',
				),
			]
		);

		$this->end_controls_section();

		// White Space Section
		$this->start_controls_section(
			'white_space_section',
			[
				'label' => __( 'White Space', 'tophive-elementor' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'whitespace',
			[
				'label' => __( 'Whitespace', 'tophive-elementor' ),
				'type' => Controls_Manager::SELECT,
				'default' => '',
				'options' => [
					''  => __( 'Normal', 'tophive-elementor' ),
					'ws-nowrap' => __( 'Nowrap', 'tophive-elementor' ),
				]
			]
		);
		
		$this->end_controls_section();

		// Highlight Section
		$this->start_controls_section(
			'highlight_section',
			[
				'label' => __( 'Highlight', 'tophive-elementor' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'enable_highlighter',
			[
				'label' => __('Enable Highlighter', 'your-textdomain'),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => __('Yes', 'your-textdomain'),
				'label_off' => __('No', 'your-textdomain'),
				'return_value' => 'yes',
				'default' => 'no',
			]
		);		

		$this->add_control(
			'highlight_wrapper_note',
			[
				'type' => \Elementor\Controls_Manager::RAW_HTML,
				'raw' => '<strong>Note:</strong> Wrap the text you want to highlight with <code>&#123;&#123;HIGHLIGHT&#125;&#125;</code> and <code>&#123;&#123;/HIGHLIGHT&#125;&#125;</code> to apply the highlighter effect.',
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
				'condition' => [
					'enable_highlighter' => 'yes',
				],
			]
		);
		

		$this->add_control(
			'highlight_type',
			[
				'label' => __( 'Type', 'tophive-elementor' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'rounded-circ',
				'options' => [
					'straight-ul'  => __( 'Underline Straight', 'tophive-elementor' ),
					'round-ul'  => __( 'Underline Rounded', 'tophive-elementor' ),
					'rounded-circ' => __( 'Rounded', 'tophive-elementor' ),
				],
				'condition' => [
					'enable_highlighter' => 'yes',
				],
			]
		);
		
		
		$this->start_controls_tabs('highlight_tabs');
		
		// ░░░ NORMAL TAB ░░░
		$this->start_controls_tab(
			'highlight_tab_normal',
			[
				'label' => __('Normal', 'your-textdomain'),
			]
		);
		
		// 🔸 Add your existing controls in NORMAL tab:
		$this->add_control(
			'highlight_color',
			[
				'label' => __( 'Highlight Color', 'your-textdomain' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'default' => '#fcdc58',
				'selectors' => [
					'{{WRAPPER}}' => '--tophive-highlight-color: {{VALUE}};',
				],
				'condition' => [
					'enable_highlighter' => 'yes',
				],
			]
		);
		$this->add_control(
			'highlight_text_color',
			[
				'label' => __( 'Highlight text color', 'your-textdomain' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'default' => '#fcdc58',
				'selectors' => [
					'{{WRAPPER}}' => '--tophive-highlight-text-color: {{VALUE}};',
				],
				'condition' => [
					'enable_highlighter' => 'yes',
				],
			]
		);
		
		$this->add_responsive_control(
			'highlight_height',
			[
				'label' => __( 'Highlight Height (%)', 'your-textdomain' ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ '%' ],
				'range' => [ '%' => [ 'min' => 10, 'max' => 150 ] ],
				'selectors' => [
					'{{WRAPPER}}' => '--tophive-highlight-height: {{SIZE}}%;',
				],
				'condition' => [
					'enable_highlighter' => 'yes',
				],
			]
		);
		
		$this->add_responsive_control(
			'highlight_width',
			[
				'label' => __( 'Highlight Width (%)', 'your-textdomain' ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ '%' ],
				'range' => [ '%' => [ 'min' => 10, 'max' => 150 ] ],
				'selectors' => [
					'{{WRAPPER}}' => '--tophive-highlight-width: {{SIZE}}%;',
				],
				'condition' => [
					'enable_highlighter' => 'yes',
				],
			]
		);
		
		$this->add_responsive_control(
			'highlight_top',
			[
				'label' => __( 'Highlight Top', 'your-textdomain' ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [ 'px' => [ 'min' => -100, 'max' => 100 ] ],
				'selectors' => [
					'{{WRAPPER}}' => '--tophive-highlight-top: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'enable_highlighter' => 'yes',
				],
			]
		);
		
		$this->add_responsive_control(
			'highlight_left',
			[
				'label' => __( 'Highlight Left', 'your-textdomain' ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [ 'px' => [ 'min' => -100, 'max' => 100 ] ],
				'selectors' => [
					'{{WRAPPER}}' => '--tophive-highlight-left: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'enable_highlighter' => 'yes',
				],
			]
		);
		
		$this->add_responsive_control(
			'highlight_rotate',
			[
				'label' => __( 'Highlight Rotate', 'your-textdomain' ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'deg' ],
				'range' => [ 'deg' => [ 'min' => -180, 'max' => 180 ] ],
				'default' => [ 'unit' => 'deg', 'size' => 0 ],
				'selectors' => [
					'{{WRAPPER}}' => '--tophive-highlight-rotate: {{SIZE}}deg;',
				],
				'condition' => [
					'enable_highlighter' => 'yes',
				],
			]
		);
		
		$this->end_controls_tab(); // end normal tab
		
		// ░░░ HOVER TAB ░░░
		$this->start_controls_tab(
			'highlight_tab_hover',
			[
				'label' => __('Hover', 'your-textdomain'),
			]
		);
		
		// 🔸 Hover variants with `-hover` suffix
		$this->add_control(
			'highlight_color_hover',
			[
				'label' => __( 'Highlight Color (Hover)', 'your-textdomain' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}}' => '--tophive-highlight-color-hover: {{VALUE}};',
				],
				'condition' => [
					'enable_highlighter' => 'yes',
				],
			]
		);
		$this->add_control(
			'highlight_text_color_hover',
			[
				'label' => __( 'Highlight Text Color (Hover)', 'your-textdomain' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}}' => '--tophive-highlight-text-color-hover: {{VALUE}};',
				],
				'condition' => [
					'enable_highlighter' => 'yes',
				],
			]
		);
		
		$this->add_responsive_control(
			'highlight_height_hover',
			[
				'label' => __( 'Highlight Height (%)', 'your-textdomain' ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ '%' ],
				'range' => [ '%' => [ 'min' => 10, 'max' => 150 ] ],
				'selectors' => [
					'{{WRAPPER}}' => '--tophive-highlight-height-hover: {{SIZE}}%;',
				],
				'condition' => [
					'enable_highlighter' => 'yes',
				],
			]
		);
		
		$this->add_responsive_control(
			'highlight_width_hover',
			[
				'label' => __( 'Highlight Width (%)', 'your-textdomain' ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ '%' ],
				'range' => [ '%' => [ 'min' => 10, 'max' => 150 ] ],
				'selectors' => [
					'{{WRAPPER}}' => '--tophive-highlight-width-hover: {{SIZE}}%;',
				],
				'condition' => [
					'enable_highlighter' => 'yes',
				],
			]
		);
		
		$this->add_responsive_control(
			'highlight_top_hover',
			[
				'label' => __( 'Highlight Top (Hover)', 'your-textdomain' ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [ 'px' => [ 'min' => -100, 'max' => 100 ] ],
				'selectors' => [
					'{{WRAPPER}}' => '--tophive-highlight-top-hover: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'enable_highlighter' => 'yes',
				],
			]
		);
		
		$this->add_responsive_control(
			'highlight_left_hover',
			[
				'label' => __( 'Highlight Left (Hover)', 'your-textdomain' ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [ 'px' => [ 'min' => -100, 'max' => 100 ] ],
				'selectors' => [
					'{{WRAPPER}}' => '--tophive-highlight-left-hover: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'enable_highlighter' => 'yes',
				],
			]
		);
		
		$this->add_responsive_control(
			'highlight_rotate_hover',
			[
				'label' => __( 'Highlight Rotate (Hover)', 'your-textdomain' ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'deg' ],
				'range' => [ 'deg' => [ 'min' => -180, 'max' => 180 ] ],
				'default' => [ 'unit' => 'deg', 'size' => 0 ],
				'selectors' => [
					'{{WRAPPER}}' => '--tophive-highlight-rotate-hover: {{SIZE}}deg;',
				],
				'condition' => [
					'enable_highlighter' => 'yes',
				],
			]
		);
		
		$this->end_controls_tab(); // end hover tab
		$this->end_controls_tabs();
		$this->end_controls_section();
		

		// TEXT FILL SECTION
		$this->start_controls_section(
			'text_fill_section',
			[
				'label' => __( 'Text Fill Effects', 'your-textdomain' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);
		
		$this->add_control(
			'enable_text_fill',
			[
				'label'        => __( 'Enable Text Fill Effect', 'your-textdomain' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'default'      => 'no',
				'label_on'     => __( 'Yes', 'your-textdomain' ),
				'label_off'    => __( 'No', 'your-textdomain' ),
				'return_value' => 'yes',
				'prefix_class' => 'tophive-text-fill-effects '
			]
		);
		
		$this->add_control(
			'text_fill_primary_color',
			[
				'label'     => __( 'Primary Color', 'your-textdomain' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '#000000',
				'condition' => [
					'enable_text_fill' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}}' => '--tophive-text-fill-primary: {{VALUE}};',
				],
			]
		);
		
		$this->add_control(
			'text_fill_fill_color',
			[
				'label'     => __( 'Fill Color', 'your-textdomain' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '#fcdc58',
				'condition' => [
					'enable_text_fill' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}}' => '--tophive-text-fill-fill: {{VALUE}};',
				],
			]
		);
		// START
		$this->add_control(
			'scroll_anim_start',
			[
				'label' => __( 'Start Point', 'your-textdomain' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => 'top 50%',
				'placeholder' => 'e.g., top 80%',
			]
		);

		// END
		$this->add_control(
			'scroll_anim_end',
			[
				'label' => __( 'End Point', 'your-textdomain' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => 'bottom 50%',
				'placeholder' => 'e.g., bottom top',
			]
		);

		// SCRUB
		$this->add_control(
			'scroll_anim_scrub',
			[
				'label' => __( 'Scrub Smoothness', 'your-textdomain' ),
				'type' => \Elementor\Controls_Manager::NUMBER,
				'min' => 0,
				'max' => 2,
				'step' => 0.05,
				'default' => 0.75,
				'description' => '0 = no scrub, 1+ = smooth scrub animation',
			]
		);

		// PIN
		$this->add_control(
			'scroll_anim_pin',
			[
				'label' => __( 'Enable Pin', 'your-textdomain' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => __( 'Yes', 'your-textdomain' ),
				'label_off' => __( 'No', 'your-textdomain' ),
				'return_value' => 'yes',
				'default' => 'no',
			]
		);
		
		$this->end_controls_section();
		

		$this->start_controls_section(
			'typer_effect_section',
			[
				'label' => __( 'Typer Effect', 'your-textdomain' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);
		
		// ✅ Switch to enable the typer effect
		$this->add_control(
			'enable_typer',
			[
				'label' => __( 'Enable Typer Effect', 'your-textdomain' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => __( 'Yes', 'your-textdomain' ),
				'label_off' => __( 'No', 'your-textdomain' ),
				'return_value' => 'yes',
				'default' => 'no',
			]
		);
		
		// 📌 Usage note
		$this->add_control(
			'typer_effect_note',
			[
				'type' => \Elementor\Controls_Manager::RAW_HTML,
				'raw'  => '<strong>Note:</strong> Use <code>&#123;&#123;TYPER_TEXT&#125;&#125;</code> in your text where the typing effect should appear.',
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
				'condition' => [
					'enable_typer' => 'yes',
				],
			]
		);
		
		// 🎨 Typer text color
		$this->add_control(
			'typer_text_color',
			[
				'label' => __( 'Typer Text Color', 'your-textdomain' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .th-typed' => 'color: {{VALUE}};',
				],
				'condition' => [
					'enable_typer' => 'yes',
				],
			]
		);
		
		// 🔤 Typer typography
		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name' => 'typer_typography',
				'label' => __( 'Typer Font', 'your-textdomain' ),
				'selector' => '{{WRAPPER}} .th-typed',
				'condition' => [
					'enable_typer' => 'yes',
				],
			]
		);
		$repeater = new \Elementor\Repeater();

		$repeater->add_control(
			'typer_word',
			[
				'label' => __( 'Word', 'your-textdomain' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Typing Text', 'your-textdomain' ),
				'label_block' => true,
			]
		);

		$this->add_control(
			'typer_cursor_char',
			[
				'label' => __( 'Typer Cursor Character', 'your-textdomain' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => '|',
				'placeholder' => '|',
				'condition' => [
					'enable_typer' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}}' => '--th-typer-text: \'{{VALUE}}\';',
				],
			]
		);
		
		$this->add_control(
			'typer_cursor_color',
			[
				'label' => __( 'Typer Cursor Color', 'your-textdomain' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'default' => '#000000',
				'condition' => [
					'enable_typer' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}}' => '--th-typer-color: {{VALUE}};',
				],
			]
		);
		

		$this->add_control(
			'typer_words',
			[
				'label' => __( 'Typer Words', 'your-textdomain' ),
				'type' => \Elementor\Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'default' => [
					[ 'typer_word' => __( 'Creative', 'your-textdomain' ) ],
					[ 'typer_word' => __( 'Smart', 'your-textdomain' ) ],
					[ 'typer_word' => __( 'Fast', 'your-textdomain' ) ],
				],
				'title_field' => '{{{ typer_word }}}',
				'condition' => [
					'enable_typer' => 'yes',
				],
			]
		);

		
		$this->end_controls_section();
		
		// Vertical Text Section
		$this->start_controls_section(
			'vertical_txt_section',
			[
				'label' => __( 'Vertical Text', 'tophive-elementor' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'vertical_txt',
			[
				'label' => __( 'Vertical Text?', 'tophive-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'On', 'tophive-elementor' ),
				'label_off' => __( 'Off', 'tophive-elementor' ),
				'return_value' => 'true',
				'default' => 'false',
			]
		);
		$this->end_controls_section();


		// Style Tab
		$this->start_controls_section(
			'style_section',
			[
				'label' => __( 'Style', 'tophive-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'ld_inner_margin',
			[
				'label' => __( 'Heading Margin', 'tophive-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'default' => [
					'bottom' => '0.5',
					'unit' => 'em',
					'isLinked' => false,
				],
				'selectors' => [
					'{{WRAPPER}} .ld-fh-element' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'ld_inner_padding',
			[
				'label' => __( 'Heading Padding', 'tophive-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .ld-fh-element' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->end_controls_section();

		$color_sections_hide = get_post_type() === 'liquid-header' ? '' : '_hide';

		// Sticky Header
		$this->start_controls_section(
			'sticky_colors' . $color_sections_hide,
			[
				'label' => __( 'Sticky Color', 'tophive-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'sticky_color',
			array(
				'label' => __( 'Color', 'elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'.is-stuck {{WRAPPER}} .ld-fh-element, .is-stuck {{WRAPPER}} .ld-fh-element span' => 'color:{{VALUE}} !important;',
				],
				'condition' => [
					'add_gradient' => ''
				]
			)
		);

		$this->end_controls_section();

		// Colors Over Light Rows
		$this->start_controls_section(
			'sticky_light_colors' . $color_sections_hide,
			[
				'label' => __( 'Colors Over Light Rows', 'tophive-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'sticky_light_color',
			array(
				'label' => __( 'Color', 'elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}}.lqd-active-row-light .ld-fh-element, {{WRAPPER}}.lqd-active-row-light .ld-fh-element span, {{WRAPPER}}.lqd-active-row-light .ld-fh-element a' => 'color:{{VALUE}} !important;',
				],
				'condition' => [
					'add_gradient' => ''
				]
			)
		);

		$this->end_controls_section();

		// Colors Over Dark Rows
		$this->start_controls_section(
			'sticky_dark_colors' . $color_sections_hide,
			[
				'label' => __( 'Colors Over Dark Rows', 'tophive-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'sticky_dark_color',
			array(
				'label' => __( 'Color', 'elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}}.lqd-active-row-dark .ld-fh-element, {{WRAPPER}}.lqd-active-row-dark .ld-fh-element span, {{WRAPPER}}.lqd-active-row-dark .ld-fh-element a' => 'color:{{VALUE}} !important;',
				],
				'condition' => [
					'add_gradient' => ''
				]
			)
		);

		$this->end_controls_section();
		
		$this->end_controls_tab();


	}

	function tophive_advanced_text( $prefix, $condition = '' ){

		$prefix->add_control(
			'advanced_text_enable',
			[
				'label' => esc_html__( 'Enable the advanced text?', 'tophive-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'tophive-elementor' ),
				'label_off' => esc_html__( 'Off', 'tophive-elementor' ),
				'return_value' => 'yes',
				'default' => '',
			]
		);
		
		$repeater_advanced_text = new Repeater();
		
		$repeater_advanced_text->add_control(
			'text', [
				'label' => esc_html__( 'Title', 'tophive-elementor' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'Title' , 'tophive-elementor' ),
				'label_block' => true,
			]
		);
	
		$repeater_advanced_text->add_control(
			'image',
			[
				'label' => esc_html__( 'Choose Image', 'tophive-elementor' ),
				'type' => Controls_Manager::MEDIA,
			]
		);
	
		$repeater_advanced_text->add_responsive_control(
			'img_width',
			[
				'label' => esc_html__( 'Width', 'tophive-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'vw' ],
				'range' => [
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
					'{{WRAPPER}} {{CURRENT_ITEM}} img' => 'width: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'image[url]!' => '' 
				]
			]
		);
	
		$repeater_advanced_text->add_control(
			'image_align',
			[
				'label' => esc_html__( 'Image placement', 'tophive-elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => esc_html__( 'Left', 'tophive-elementor' ),
						'icon' => 'eicon-h-align-left',
					],
					'right' => [
						'title' => esc_html__( 'Right', 'tophive-elementor' ),
						'icon' => 'eicon-h-align-right',
					],
				],
				'default' => 'left',
				'toggle' => false,
				'condition' => [
					'image[url]!' => '' 
				]
			]
		);
	
		$repeater_advanced_text->add_control(
			'image_v_align',
			[
				'label' => esc_html__( 'Vertical align', 'tophive-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'baseline' => 'Baseline',
					'sub' => 'Subscript',
					'sup' => 'Superscript',
					'top' => 'Top',
					'text-top' => 'Text top',
					'middle' => 'Middle',
					'bottom' => 'Bottom',
					'text-bottom' => 'Text bottom',
				],
				'default' => 'bottom',
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}} figure' => 'vertical-align: {{VALUE}};',
				],
				'condition' => [
					'image[url]!' => '' 
				]
			]
		);
	
	
		$repeater_advanced_text->add_responsive_control(
			'border',
			[
				'label' => esc_html__( 'Border', 'tophive-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}} img' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};border-style: solid;',
				],
				'condition' => [
					'image[url]!' => '' 
				]
			]
		);
	
		$repeater_advanced_text->add_responsive_control(
			'border_radius',
			[
				'label' => esc_html__( 'Border Radius', 'tophive-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}} img, {{WRAPPER}} {{CURRENT_ITEM}} figure' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'image[url]!' => '' 
				]
			]
		);
	
		$repeater_advanced_text->add_control(
			'border_color',
			[
				'label' => esc_html__( 'Border Color', 'textdomain' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}} img' => 'border-color: {{VALUE}}',
				],
				'condition' => [
					'image[url]!' => '' 
				]
			]
		);
	
		$repeater_advanced_text->add_responsive_control(
			'margin',
			[
				'label' => esc_html__( 'Margin', 'tophive-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}} figure' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'image[url]!' => '' 
				]
			]
		);
	
	
		$repeater_advanced_text->add_responsive_control(
			'item_z_index',
			[
				'label' => esc_html__( 'Z-Index', 'elementor' ),
				'type' => Controls_Manager::NUMBER,
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}}' => 'position: relative; z-index: {{VALUE}};',
				],
				'condition' => [
					'image[url]!' => '' 
				]
			]
		);
	
		$repeater_advanced_text->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'advanced_text_typography',
				'selector' => '{{WRAPPER}} {{CURRENT_ITEM}}',
			]
		);
	
		$repeater_advanced_text->add_control(
			'advanced_text_color',
			[
				'label' => esc_html__( 'Color', 'tophive-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}}' => 'color: {{VALUE}}',
				],
			]
		);
		
		$prefix->add_control(
			'advanced_text_content',
			[
				'label' => esc_html__( 'Items', 'tophive-elementor' ),
				'type' => Controls_Manager::REPEATER,
				'fields' => $repeater_advanced_text->get_controls(),
				'default' => [
					[
						'text' => esc_html__( 'Title #1', 'tophive-elementor' ),
					],
					[
						'text' => esc_html__( 'Title #2', 'tophive-elementor' ),
					],
				],
				'title_field' => '{{{ text }}}',
				'condition' => [
					'advanced_text_enable' => 'yes'
				]
			]
		);
	
	}

	protected function add_render_attributes() {
		parent::add_render_attributes();

		$settings = $this->get_settings();

		$classnames = [];

		if ( $settings['enable_text_split'] !== '' && $settings['enable_text_fill'] !== 'yes' ) {
            if($settings['split_type'] !== ''){
                $this->add_render_attribute( '_wrapper', 'data-split-text', 'yes' );
                $this->add_render_attribute( '_wrapper', 'data-split-by', $settings['split_type'] );
            }
		}
		if ( $settings['enable_text_rotator'] !== '' ) {
				$animation_options = [];

				$prefix = 'th_ra_';

				$animation_preset = $this->get_settings( $prefix . 'preset' );

            	$anim_settings = \Tophive_GSAP_Animation_Helper::extract_animation_settings( $this, $prefix);

				$animation_options = array_merge($animation_options, $anim_settings);

				if( 'custom' !== $animation_preset ) {
					$animation_presets = \Tophive_GSAP_Animation_Helper::extract_preset_values( $this, $prefix );
					$animation_from = $animation_presets['from'];
					$animation_to = $animation_presets['to'];
				}
				else {
					$custom_values = \Tophive_GSAP_Animation_Helper::extract_custom_values( $this, $prefix );
					$animation_from = $custom_values['from'];
					$animation_to = $custom_values['to'];
				}

				$animation_options['from'] = !empty( $animation_from ) ? $animation_from : array();
            	$animation_options['to'] = !empty( $animation_to ) ? $animation_to : array();


				$this->add_render_attribute( '_wrapper', 'data-text-rotator', $settings['rotator_type'] );
                $this->add_render_attribute( '_wrapper', 'data-text-rotator-settings', stripslashes(wp_json_encode($animation_options) ));
		}
	}

	/**
	 * Render heading widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function render() {
		
		$settings = $this->get_settings_for_display();
		$this->add_inline_editing_attributes( 'content', 'basic' );


        $this->advanced_title();
	}

	protected function advanced_title() {

		$settings = $this->get_settings_for_display();
		
		$tag = $settings['tag'];
		
		$classnames = $outline_title = '';
		$classnames_arr = array(
			'tophive-advanced-heading',
			$settings['enable_text_rotator'] === 'yes' && $settings['rotator_type'] !== '' ? 'text-rotator-' . $settings['rotator_type'] : ''
		);

		if ( $tag !== 'p' && $tag !== 'div' && $tag !== 'span' ) {
			array_push($classnames_arr, 'elementor-heading-title');
		}

		if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
			$classnames_arr[] = 'elementor-inline-editing';
			$inline_edit_attr = 'data-elementor-setting-key="content" data-elementor-inline-editing-toolbar="basic"';
		} else {
			$inline_edit_attr = '';
		}
		
		if( !empty( $settings['add_gradient'] ) ) {
			$classnames_arr[] = 'ld-gradient-heading';
		}

		if( $settings['vertical_txt'] === 'true' ) {
			$classnames_arr[] = 'text-vertical';
		}

		$title = $settings['content'];

        $rotator_html = $this->get_rotator_words();

		$title = preg_replace('/^<p[^>]*>(.*)<\/p[^>]*>/', '$1', $title);

        if ( $rotator_html && strpos( $title, '{{TEXT_ROTATOR}}' ) !== false ) {
            $title = str_replace('{{TEXT_ROTATOR}}', $rotator_html, $title);
        }

		if ( $settings['enable_highlighter'] === 'yes' ) {
			$highlighter_classes = ['tophive-text-highlighter'];
			$highlighter_classes[] = $settings['highlight_type'];
		
			$title = preg_replace_callback(
				'/\{\{HIGHLIGHT\}\}(.*?)\{\{\/HIGHLIGHT\}\}/',
				function ( $matches ) use ( $highlighter_classes ) {
					return '<span class="' . implode( ' ', $highlighter_classes ) . '">' . $matches[1] . '</span>';
				},
				$title
			);
		}

		if ( $settings['enable_typer'] === 'yes' && ! empty( $settings['typer_words'] ) ) {
			$typer_words = array_column( $settings['typer_words'], 'typer_word' );
			$typer_words_json = wp_json_encode( $typer_words );
		
			// Replace {{TYPER_TEXT}} with span + data
			$title = str_replace(
				'{{TYPER_TEXT}}',
				'<span class="th-typed" 
					data-typer-strings=\'' . esc_attr( $typer_words_json ) . '\'>
				</span>',
				$title
			);
		}

		// if ( $settings['enable_text_fill'] === 'yes' ) {
		// 	$classnames_arr[] = 'tophive-text-fill-effects';
		// }
		
		if( !empty( $classnames_arr ) ) {
			$classnames = 'class="' . join( ' ', $classnames_arr ) . '"';
		}
		
		// Title
		if( $settings['enable_text_rotator'] !== 'yes' || $settings['rotator_type'] == 'inline' ) {
            $tag           = !empty($tag) ? $tag : 'h2';
            $data_opts     = $this->get_data_opts();
            $classnames    = $classnames ?? '';
            $inline_attr   = $inline_edit_attr ?? '';
            $outline       = $outline_title;
            $content       = '';

            if ( ! empty($settings['link']['url']) ) {
                $this->add_link_attributes('fancy_heading_link', $settings['link']);
                $link_attrs = $this->get_render_attribute_string('fancy_heading_link');

                // split options go inside <a> here
                $content = sprintf(
                    '<a %s %s>%s %s</a>',
                    $link_attrs,
                    $inline_attr,
                    $outline,
                    $title
                );

                printf(
                    '<%s %s %s>%s</%s>',
                    $tag,
                    $classnames,
                    $data_opts,
                    $content,
                    $tag
                );
            } else {
                // split options go inside outer tag here
                printf(
                    '<%s %s %s %s>%s %s</%s>',
                    $tag,
                    $classnames,
                    $data_opts,
                    $inline_attr,
                    $outline,
                    $title,
                    $tag
                );
            }

		}
		if( $settings['rotator_type'] == 'circle' ){
			echo $this->get_text_circle();
		}
		if( $settings['rotator_type'] == 'list' ){
			echo $this->get_rotator_words();
		}

        ?>
        <?php
		
	}
	function ld_el_get_advanced_text( $widget ){

		$items = $widget->get_settings_for_display('advanced_text_content');
		$content = '';
	
		if ( $items ){
			foreach( $items as $item ){
	
				$content .= sprintf( '<span class="lqd-adv-txt-item elementor-repeater-item-%s">', $item['_id'] );
	
				if ( !empty($item['image']['url']) ){
					$img_html = '<figure class="lqd-adv-txt-fig pos-rel d-inline-flex">' . wp_get_attachment_image( $item['image']['id'], 'full', false ) . '</figure>';
					$content = $item['image_align'] === 'left' ? $content . $img_html . $item['text'] : $content . $item['text'] . $img_html; 
				} else {
					$content .= $item['text'];
				}
	
				$content .= "</span>";
	
			}
		}
	
		return $content;
	
	}
	
	protected function get_text_fill_options() {

		$settings = $this->get_settings_for_display();
		
		if( $settings['enable_text_fill'] !== 'yes' ) {
			return;
		}
		$fill_settings = [
			'start' => $settings['scroll_anim_start'] ?? 'top 50%',
			'end'   => $settings['scroll_anim_end'] ?? 'bottom 50%',
			'scrub' => floatval($settings['scroll_anim_scrub'] ?? 0.75),
			'pin'   => $settings['scroll_anim_pin'] === 'yes' ? true : false,
			'color' => $settings['text_fill_fill_color'] ?? '#FF0000',
		];

		$attrs = array(
			'data-fill-settings' => wp_json_encode($fill_settings),
		);
		
		return $attrs;
		
	}

	protected function get_data_opts() {
		$opts = array();
		$text_fill = $this->get_text_fill_options();
	
		if ( is_array( $text_fill ) && ! empty( $text_fill ) ) {
			$opts = array_merge( $opts, $text_fill );
		}
	
		$attr_string = '';
		foreach ( $opts as $key => $value ) {
			$attr_string .= sprintf( '%s=\'%s\' ', esc_attr( $key ), esc_attr( $value ) );
		}
	
		return trim( $attr_string );
	}

	protected function get_highlight_opts() {

		$settings = $this->get_settings_for_display();
		
		// if( $settings['content'] && !has_shortcode( $settings['content'], 'ld_highlight' )  ) {
		// 	return;
		// }
		
		$opts = array(
			'data-inview' => true,
			'data-transition-delay' => true,
			'data-delay-options' => wp_json_encode(
				array(
					'elements' => '.lqd-highlight-inner',
					'delayType' => 'transition'
				)
			)
		);
		
		return $opts;
	
	}

	protected function get_text_list_rotator(){

	}

	protected function get_text_circle(){
		$settings = $this->get_settings_for_display();
		if( $settings['enable_text_rotator'] !== 'yes' ) {
			return;
		}
		if( $settings['rotator_type'] !== 'circle' ) {
			return;
		}

		if ($settings['icon_type'] === 'icon') {
			ob_start();
			\Elementor\Icons_Manager::render_icon($settings['icon'], ['aria-hidden' => 'true']);
			$imageHtml = ob_get_clean();
		} elseif ($settings['icon_type'] === 'image' && !empty($settings['image']['url'])) {
			$imageHtml = '<img src="' . esc_url($settings['image']['url']) . '" alt="' . esc_attr(get_post_meta($settings['image']['id'], '_wp_attachment_image_alt', true)) . '">';
		}

		$imageContainer = '<div class="image-container">%s</div>';
		if (!empty($imageHtml)) {
			$imageContainer = sprintf($imageContainer, $imageHtml);
		}

		$words = array_column($settings['items'], 'word');

		$out = '';

		$out = '<div class="tophive-text-circle-container">
					<svg viewBox="0 0 100 100">
						<defs>
							<path id="circle" d="
							M 50, 50
							m -37, 0
							a 37,37 0 1,1 74,0
							a 37,37 0 1,1 -74,0"/>
						</defs>
						<text font-size="4">
							<textPath xlink:href="#circle">
								'. implode(' - ', $words) .'
							</textPath>
						</text>
					</svg>
					
					'. $imageContainer .'
				</div>';

		return $out;
	}

	protected function get_rotator_words() {

		$settings = $this->get_settings_for_display();
		
		if( $settings['enable_text_rotator'] !== 'yes' ) {
			return;
		}

		if( empty( $settings['items'] ) ) {
			return;
		}

		$words = $settings['items'] ;

		if( empty( $words ) ) {
			return;
		}
		
		$out = $style_word = '';
		
		if ( $settings['rotator_type'] === 'list' ) {
			$out .= ' <span class="text-rotate-keywords" data-visible-words="'. $settings['rotator_visible_words_max']['size'] .'" data-rotation-direction="' . $settings['rotation_direction_list'] . '">';
		}else{
			$out .= ' <span class="text-rotate-keywords">';
		}
		$i = 1;
		foreach ( $words as $word ) {
			$style_word = !empty( $word['word_color'] ) ? 'style="color:' . esc_attr( $word['word_color'] ) . '"' : '';
			
			$out .= '<span class="text-rotate-keyword" ' . $style_word . '>' . esc_html( $word['word'] ) . '</span>';
			$i++;
		}
		if ( $settings['rotator_type'] === 'inline' ) {
        	$out .= '<span class="text-rotate-keyword" ' . $style_word . '>' . esc_html( $words[0]['word'] ) . '</span>';
		// 	$out .= ' </span>';
		}
		$out .= '</span>';
		$out .= '<span class="text-rotator-spacer"></span>';
		$out .= '<span class="text-rotator-shade"></span>';

        return sprintf(
            '<span class="text-rotator-wrapper">%s</span>',
            $out
        );
	}
}

\Elementor\Plugin::instance()->widgets_manager->register( new Tophive_Advanced_Heading() );
