<?php
// namespace LiquidElementor\Widgets;

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
use Elementor\Group_Control_Css_Filter;
use Elementor\Repeater;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor heading widget.
 *
 * Elementor widget that displays an eye-catching headlines.
 *
 * @since 1.0.0
 */
class Tophive_Image_Widget extends Widget_Base {

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
		return 'tophive-image';
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
        return TH_ELEMENTOR_DISPLAY_NAME_SC . esc_html__( 'Image', TH_ELEMENTOR_SLUG );
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
		return 'eicon-image';
	}

	/**
	 * Get widget categories.
	 *
	 * Retrieve the list of categories the heading widget belongs to.
	 *
	 * Used to determine where to display the widget in the editor.
	 *
	 * @since 2.0.0
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
	 * @since 2.1.0
	 * @access public
	 *
	 * @return array Widget keywords.
	 */
	public function get_keywords() {
		return [ 'image', 'gallery'  ];
	}

	/**
	 * Retrieve the list of scripts the counter widget depended on.
	 *
	 * Used to set scripts dependencies required to run the widget.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return array Widget scripts dependencies.
	 */
	public function get_script_depends() {
		return [ 'fancybox-js', 'tophive-elementor-bundle' ];
	}

	/**
	 * Retrieve the list of scripts the counter widget depended on.
	 *
	 * Used to set scripts dependencies required to run the widget.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return array Widget scripts dependencies.
	 */
	public function get_style_depends() {
        return [ 'fancybox-css' ];
	}

	/**
	 * Register heading widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function register_controls() {

		// General Section
		$this->start_controls_section(
			'general_section',
			[
				'label' => __( 'General', 'hub-elementor-addons' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'image',
			[
				'label' => __( 'Image', 'hub-elementor-addons' ),
				'type' => Controls_Manager::MEDIA,
				'default' => [
					'url' => Utils::get_placeholder_image_src(),
				],
			]
		);

        $this->add_control(
            'dimensions_heading',
            [
                'label' => esc_html__( 'Dimensions', 'hub-elementor-addons' ),
                'type' => \Elementor\Controls_Manager::POPOVER_TOGGLE,
                'label_off' => esc_html__( 'None', 'hub-elementor-addons' ),
                'label_on' => esc_html__( 'Custom', 'hub-elementor-addons' ),
                'return_value' => 'yes',
            ]
        );
        
        $this->start_popover();
        
        $this->add_responsive_control(
            'width',
            [
                'label' => esc_html__( 'Width', 'hub-elementor-addons' ),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'unit' => '%',
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
                    '{{WRAPPER}} img' => 'width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );
        
        $this->add_responsive_control(
            'space',
            [
                'label' => esc_html__( 'Max Width', 'hub-elementor-addons' ),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'unit' => '%',
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
                    '{{WRAPPER}} img' => 'max-width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );
        
        $this->add_responsive_control(
            'height',
            [
                'label' => esc_html__( 'Height', 'hub-elementor-addons' ),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'unit' => 'px',
                ],
                'tablet_default' => [
                    'unit' => 'px',
                ],
                'mobile_default' => [
                    'unit' => 'px',
                ],
                'size_units' => [ 'px', 'vh' ],
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
                    '{{WRAPPER}} img, {{WRAPPER}} figure' => 'height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );
        
        $this->end_popover();
        

		$this->add_responsive_control(
			'object-fit',
			[
				'label' => esc_html__( 'Object Fit', 'hub-elementor-addons' ),
				'type' => Controls_Manager::SELECT,
				'condition' => [
					'height[size]!' => '',
				],
				'options' => [
					'' => esc_html__( 'Default', 'hub-elementor-addons' ),
					'fill' => esc_html__( 'Fill', 'hub-elementor-addons' ),
					'cover' => esc_html__( 'Cover', 'hub-elementor-addons' ),
					'contain' => esc_html__( 'Contain', 'hub-elementor-addons' ),
				],
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} img' => 'object-fit: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			[
				'name' => 'css_filters',
				'selector' => '{{WRAPPER}} img',
			]
		);

		$this->add_responsive_control(
            'custom_image_size',
            [
                'label' => __( 'Image container size', 'hub-elementor-addons' ),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'description' => __( 'Choose the image size based on its container.', 'hub-elementor-addons' ),
                'size_units' => [ '%' ],
                'default' => [
                    'unit' => '%',
                    'size' => 50,
                ],
                'range' => [
                    '%' => [
                        'min' => 1,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}}' => 'width: auto;',
                    '{{WRAPPER}} .tophive-image-container' => 'width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );
        

		$this->add_responsive_control(
			'image_align',
			[
				'label' => __( 'Alignment', 'hub-elementor-addons' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => __( 'Left', 'hub-elementor-addons' ),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'hub-elementor-addons' ),
						'icon' => 'eicon-text-align-center',
					],
					'right' => [
						'title' => __( 'Right', 'hub-elementor-addons' ),
						'icon' => 'eicon-text-align-right',
					],
				],
				'selectors' => [
					'{{WRAPPER}}' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'img_link',
			[
				'label' => __( 'Link', 'hub-elementor-addons' ),
				'type' => Controls_Manager::URL,
				'placeholder' => __( 'https://your-link.com', 'hub-elementor-addons' ),
				'show_external' => true,
				'default' => [
					'url' => '',
					'is_external' => false,
					'nofollow' => false,
				],
			]
		);

		$this->end_controls_section();
		
        // Overlay Background
		$this->start_controls_section(
			'bg_overlay_section',
			[
				'label' => __( 'Overlay Background', 'hub-elementor-addons' ),
				'tab' => Controls_Manager::TAB_CONTENT
			]
		);

		Tophive_Elementor_Style_Helper::add_ui_style_controls($this, 'overlay', '{{WRAPPER}} .tophive-overlay-bg', false, '');
		
        $this->end_controls_section();
		

        Tophive_Elementor_UI_Helper::lightbox($this);

		// Reveal effect
		$this->start_controls_section(
			'reveal_effect_section',
			[
				'label' => __( 'Reveal effect', 'hub-elementor-addons' ),
				'tab' => Controls_Manager::TAB_CONTENT
			]
		);

		$this->add_control(
			'enable_reveal',
			[
				'label' => __( 'Reveal on scroll', 'hub-elementor-addons' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'On', 'hub-elementor-addons' ),
				'label_off' => __( 'Off', 'hub-elementor-addons' ),
				'return_value' => 'yes',
				'default' => '',
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'reveal_color',
				'label' => __( 'Background', 'hub-elementor-addons' ),
				'types' => [ 'classic', 'gradient' ],
				'exclude' => [ 'image' ],
				'selector' => '{{WRAPPER}} .tophive-reveal-effect::after, {{WRAPPER}} .tophive-reveal-effect::before',
				'fields_options' => [
					'background' => [
						'default' => 'classic',
					],
				],
				'condition' => [
					'enable_reveal' => 'yes'
				]
			]
		);

		$this->add_control(
			'reveal_direction',
			[
				'label' => __( 'Direction', 'hub-elementor-addons' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'lr',
				'options' => [
					'right' => __( 'Left -> Right', 'hub-elementor-addons' ),
					'bottom' => __( 'Top -> Bottom', 'hub-elementor-addons' ),
					'left' => __( 'Right -> Left', 'hub-elementor-addons' ),
					'top' => __( 'Bottom -> Top', 'hub-elementor-addons' ),
				],
				'condition' => [
					'enable_reveal' => 'yes'
				]
			]
		);

		$this->end_controls_section();
		
		
		// Hover 3D
		$this->start_controls_section(
			'hover3d_section',
			[
				'label' => __( 'Hover Tilt', 'hub-elementor-addons' ),
				'tab' => Controls_Manager::TAB_CONTENT
			]
		);

        
		$this->add_control(
            'enable_hover_tilt',
            [
            'label' => __( 'Enable Hover Tilt', 'hub-elementor-addons' ),
            'type' => \Elementor\Controls_Manager::SWITCHER,
            'label_on' => __( 'Yes', 'hub-elementor-addons' ),
            'label_off' => __( 'No', 'hub-elementor-addons' ),
            'return_value' => 'yes',
            'default' => '',
            ]
        );
        $this->add_control(
            'tilt_effect_note',
            [
                'type' => \Elementor\Controls_Manager::RAW_HTML,
                'raw' => '<strong>Note:</strong> Tilt effect might conflict with transform or animation effects. If you notice unexpected behavior, try disabling either Tilt or conflicting transform-based animations.',
                'content_classes' => 'elementor-panel-alert elementor-panel-alert-warning',
                'separator' => 'before',
                'condition' => [
                    'enable_hover_tilt' => 'yes',
                ],
            ]
        );
            
        $this->add_control(
            'hover_tilt_max',
            [
                'label' => __( 'Max Rotation', 'hub-elementor-addons' ),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'condition' => [
                    'enable_hover_tilt' => 'yes',
                ],
                'default' => [
                    'size' => 20,
                ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 90,
                    ],
                ],
            ]
        );
                
        $this->add_control(
            'hover_tilt_scale',
            [
            'label' => __( 'Scale on Hover', 'hub-elementor-addons' ),
            'type' => \Elementor\Controls_Manager::SLIDER,
            'condition' => [
                'enable_hover_tilt' => 'yes',
            ],
            'default' => [
                'size' => 1.1,
            ],
            'range' => [
                'px' => [
                    'min' => 1,
                    'max' => 2,
                    'step' => 0.01,
                ],
            ],
            ]
        );
                
        $this->add_control(
            'hover_tilt_perspective',
        [
            'label' => __( 'Perspective', 'hub-elementor-addons' ),
            'type' => \Elementor\Controls_Manager::SLIDER,
            'condition' => [
                'enable_hover_tilt' => 'yes',
            ],
            'default' => [
                'size' => 600,
            ],
            'range' => [
                'px' => [
                    'min' => 100,
                    'max' => 2000,
                ],
            ],
            ]
        );
                    
        $this->end_controls_section();
                        
        $this->start_controls_section(
            'blob_effects',
            [
                'label' => __( 'Blob Effect', 'hub-elementor-addons' ),
                'tab' => Controls_Manager::TAB_CONTENT
            ]
        );
        $this->add_control(
            'enable_blob_effect',
            [
                'label' => __( 'Enable Blob Effect', 'hub-elementor-addons' ),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __( 'Yes', 'hub-elementor-addons' ),
                'label_off' => __( 'No', 'hub-elementor-addons' ),
                'return_value' => 'yes',
                'default' => '',
            ]
        );
        
        $this->add_control(
            'blob_scale',
            [
                'label' => __( 'Blob Size', 'hub-elementor-addons' ),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'default' => [
                    'size' => 1,
                ],
                'range' => [
                    'px' => [
                        'min' => 0.1,
                        'max' => 2,
                        'step' => 0.01,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}}' => '--tophive-blob-scale: {{SIZE}};',
                ],
            ]
        );
        
        
        $this->add_control(
            'blob_translate_x',
            [
                'label' => __( 'Blob Translate X (px)', 'hub-elementor-addons' ),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'default' => [
                    'size' => 0,
                ],
                'range' => [
                    'px' => [
                        'min' => -100,
                        'max' => 100,
                        'step' => 1,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}}' => '--tophive-blob-x: {{SIZE}}px;',
                ],
            ]
        );

        $this->add_control(
            'blob_translate_y',
            [
                'label' => __( 'Blob Translate Y (px)', 'hub-elementor-addons' ),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'default' => [
                    'size' => 0,
                ],
                'range' => [
                    'px' => [
                        'min' => -100,
                        'max' => 100,
                        'step' => 1,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}}' => '--tophive-blob-y: {{SIZE}}px;',
                ],
            ]
        );
        $this->end_controls_section();

        $this->start_controls_section(
            'image_hover_effects',
            [
                'label' => __( 'Hover Effect', 'hub-elementor-addons' ),
                'tab' => Controls_Manager::TAB_CONTENT
            ]
        );

        Tophive_Elementor_UI_Helper::tophive_image_hover_effects($this);
        
		$this->end_controls_section();

		Tophive_Elementor_Style_Helper::add_ui_style_controls($this, 'image', '{{WRAPPER}} figure', '', true, 'Image Styles');

        // Transform effects
		Tophive_Elementor_Style_Helper::add_ui_style_controls($this, 'transform', '{{WRAPPER}} figure', '', true, 'Transform');
        
        // Animation effect
		Tophive_Elementor_Style_Helper::add_ui_style_controls($this, 'animate', '{{WRAPPER}} figure', '', true, 'Animation');
	}

	protected function get_data_options() {

		$settings = $this->get_settings_for_display();

        if ( ! empty( $settings['enable_hover_tilt'] ) ) {
            $this->add_render_attribute( 'figure', 'class', 'tophive-hover-tilt' );

            $tilt_settings = [
                'max'         => ! empty( $settings['hover_tilt_max']['size'] ) ? floatval( $settings['hover_tilt_max']['size'] ) : 20,
                'scale'       => ! empty( $settings['hover_tilt_scale']['size'] ) ? floatval( $settings['hover_tilt_scale']['size'] ) : 1.1,
                'perspective' => ! empty( $settings['hover_tilt_perspective']['size'] ) ? intval( $settings['hover_tilt_perspective']['size'] ) : 500,
            ];

            $this->add_render_attribute( 'figure', 'data-tilt-settings', wp_json_encode( $tilt_settings ) );
        }
	}
    protected function get_reveal_data() {
		$reveal = $this->get_settings_for_display('enable_reveal');
		if( $reveal ) {
            $classes = ['tophive-reveal-effect'];
            $classes[] = $this->get_settings_for_display('reveal_direction');
			$this->add_render_attribute( 'figure', 'class', $classes );
		}

	}

    protected function get_blob_effect(){
        $settings = $this->get_settings_for_display();

        if ( $settings['enable_blob_effect'] === 'yes' ) {
            $this->add_render_attribute( 'figure', 'class', 'tophive-blob-mask' );
        }        
    }
    protected function render_blobs(){
        $settings = $this->get_settings_for_display();

        if ( $settings['enable_blob_effect'] === 'yes' ) {
            ?>
                <div class="tophive-blob">
                    <svg class="clippy" viewBox="0 0 480 480">
                        <defs>
                            <clipPath id="tophive-clip1">
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M402.5,294.5Q370,349,321.5,389.5Q273,430,199,434.5Q125,439,85.5,374.5Q46,310,50,241Q54,172,87.5,102.5Q121,33,199.5,30Q278,27,351,56.5Q424,86,429.5,163Q435,240,402.5,294.5Z" fill="black" />
                            </clipPath>
                        </defs>
                    </svg>
                </div>
            <?php
        }        
    }

	protected function render_overlay_wrap() {
        $settings = $this->get_settings_for_display();
    
        if ( empty( $settings['tophive_overlay_enable_overlay_bg_global'] ) ) {
            return;
        }
    
        // Base classes
        $classes = [ 'tophive-overlay-bg' ];
    
        // Add on-hover class if enabled
        if ( $settings['tophive_overlay_overlay_show_on_hover_global'] == 'yes' ) {
            $classes[] = 'overlay-show-onhover';
        }
    
        $href  = '';
        $group = '';
    
        // Lightbox or regular link logic
        if ( ! empty( $settings['enable_lightbox'] ) ) {
            $group = ! empty( $settings['lightbox_group_id'] ) ? esc_attr( $settings['lightbox_group_id'] ) : 'gallery';
    
            if ( ! empty( $settings['image']['id'] ) ) {
                $href = esc_url( wp_get_attachment_image_url( $settings['image']['id'], 'full', false ) );
            }
        } elseif ( ! empty( $settings['img_link']['url'] ) ) {
            $href = esc_url( $settings['img_link']['url'] );
        }
    
        // Wrap in anchor if href is available
        if ( $href ) {
            $this->add_render_attribute( 'overlay_link', 'href', $href );
            $this->add_render_attribute( 'overlay_link', 'class', 'tophive-overlay' );
    
            if ( $group ) {
                $this->add_render_attribute( 'overlay_link', 'data-fancybox', $group );
            }
    
            echo '<a ' . $this->get_render_attribute_string( 'overlay_link' ) . '>';
        }
    
        // Output the span
        echo '<span class="' . esc_attr( implode( ' ', $classes ) ) . '"></span>';
    
        if ( $href ) {
            echo '</a>';
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

		// wrapper
		$this->add_render_attribute(
			'wrapper', [
				'class' => [ 
					'tophive-image',
					'tophive-content-card',
					'd-block',
					'pos-rel',
					$settings['enable_hover_tilt'] === 'yes' ? 'perspective' : '',
				 ],
			]
		);
		$this->get_data_options();
		$this->get_reveal_data();
		$this->get_blob_effect();

		// inner_wrapper
		$this->add_render_attribute(
			'inner_wrapper', [
				'class' => [ 
					'tophive-image-container',
					'd-inline-flex',
					'pos-rel',
					'justify-content-center',
					$settings['enable_hover_tilt'] === 'yes' ? 'transform-style-3d' : '',
					$settings['tophive_animation_position_animation_switcher_global'] === 'yes' ? 'tophive-object-animate-position' : '',
				 ],
			]
		);
	
		// figure
		$this->add_render_attribute(
			'figure', [
				'class' => [ 'w-100', 'pos-rel' ],
			]
		);

		?>

		<div <?php $this->print_render_attribute_string( 'wrapper' ); ?>>
			<div <?php $this->print_render_attribute_string( 'inner_wrapper' ); ?>>
				<figure <?php $this->print_render_attribute_string( 'figure' ); ?>>
					<?php $this->render_blobs(); ?>
					<?php echo Group_Control_Image_Size::get_attachment_image_html( $settings, 'thumbnail', 'image' ); ?>
					<?php $this->render_overlay_wrap(); ?>
				</figure>
			</div>
		</div>

    	<?php

	}

    protected function content_template() {
        ?>
        <#
        var figureClasses = [ 'w-100', 'pos-rel' ];
    
        if ( settings.enable_reveal ) {
            figureClasses.push( 'tophive-reveal-effect' );
            if ( settings.reveal_direction ) {
                figureClasses.push( settings.reveal_direction );
            }
        }
    
        if ( settings.enable_hover_tilt ) {
            figureClasses.push( 'tophive-hover-tilt' );
    
            var tiltSettings = {
                max: parseFloat( settings.hover_tilt_max?.size || 20 ),
                scale: parseFloat( settings.hover_tilt_scale?.size || 1.1 ),
                perspective: parseInt( settings.hover_tilt_perspective?.size || 500 )
            };
    
            view.addRenderAttribute( 'figure', 'data-tilt-settings', JSON.stringify( tiltSettings ) );
        }
    
        if ( settings.enable_blob_effect === 'yes' ) {
            figureClasses.push( 'tophive-blob-mask' );
        }
    
        view.addRenderAttribute( 'wrapper', {
            class: [
                'tophive-image',
                'd-block',
                'pos-rel'
            ]
        });
    
        view.addRenderAttribute( 'inner_wrapper', {
            class: [
                'tophive-image-container',
                'd-inline-flex',
                'pos-rel',
                'justify-content-center',
                settings.tophive_animation_position_animation_switcher_global == 'yes' ? 'tophive-object-animate-position': ''
            ]
        });
    
        view.addRenderAttribute( 'figure', {
            class: figureClasses
        });
        #>
    
        <div {{{ view.getRenderAttributeString( 'wrapper' ) }}}>
            <div {{{ view.getRenderAttributeString( 'inner_wrapper' ) }}}>
                <figure {{{ view.getRenderAttributeString( 'figure' ) }}}>
    
                    <# if ( settings.enable_blob_effect === 'yes' ) { #>
                        <div class="tophive-blob">
                            <svg class="clippy" viewBox="0 0 480 480">
                                <defs>
                                    <clipPath id="tophive-clip1">
                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                            d="M402.5,294.5Q370,349,321.5,389.5Q273,430,199,434.5Q125,439,85.5,374.5Q46,310,50,241Q54,172,87.5,102.5Q121,33,199.5,30Q278,27,351,56.5Q424,86,429.5,163Q435,240,402.5,294.5Z" />
                                    </clipPath>
                                </defs>
                            </svg>
                        </div>
                    <# } #>
    
                    <# if ( settings.image && settings.image.url ) {
                        var imageUrl = elementor.imagesManager.getImageUrl( settings.image, settings.thumbnail_size ) || settings.image.url;
                        var altText = settings.image.alt || '';
                    #>
                        <img src="{{ imageUrl }}" alt="{{ altText }}" />
                    <# } #>
    
                    <# if ( settings.tophive_overlay_enable_overlay_bg_global ) {
                        var overlayClasses = [ 'tophive-overlay-bg' ];
                        if ( settings.tophive_overlay_overlay_show_on_hover_global ) {
                            overlayClasses.push( 'overlay-show-onhover' );
                        }
    
                        var href = '';
                        var group = '';
                        var imgId = settings.image?.id;
    
                        if ( settings.enable_lightbox ) {
                            group = settings.lightbox_group_id || 'gallery';
    
                            if ( !settings.img_link?.url && imgId ) {
                                href = elementor.imagesManager.getImageUrl( settings.image, 'full' );
                            } else {
                                href = settings.img_link?.url;
                            }
                        } else if ( settings.img_link?.url ) {
                            href = settings.img_link.url;
                        }
    
                        if ( href ) {
                            #>
                            <a href="{{ href }}" class="tophive-overlay" <# if ( group ) { #> data-fancybox="{{ group }}" <# } #>>
                                <span class="{{ overlayClasses.join(' ') }}"></span>
                            </a>
                            <#
                        } else {
                            #>
                            <span class="{{ overlayClasses.join(' ') }}"></span>
                            <#
                        }
                    } #>
    
                </figure>
            </div>
        </div>
        <?php
    }
    
}
\Elementor\Plugin::instance()->widgets_manager->register( new Tophive_Image_Widget() );