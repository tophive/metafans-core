<?php
use Elementor\Element_Base;


class Tophive_Elementor_Header_Section_Controls{
    public function __construct()
    {
        add_action('elementor/element/column/section_advanced/after_section_end', [$this, 'tophive_elementor_header_sections'], 10, 1);
        add_action('elementor/element/section/section_advanced/after_section_end', [$this, 'tophive_elementor_header_sections'], 10, 1);
        add_action('elementor/element/container/section_layout/after_section_end', [$this, 'tophive_elementor_header_sections'], 10, 1);
        add_action( 'elementor/frontend/before_render', [ $this, 'add_custom_classes_to_container' ], 10, 1);
        add_filter( 'elementor/document/wrapper_attributes', [ $this, 'add_custom_class_to_wrapper'], 10, 2 );

        add_action( 'wp_enqueue_scripts', [$this, 'enqueue_dynamic_sticky_styles'] );
    }

    public function tophive_elementor_header_sections( Element_Base $element ){
        // Check if the current element is a section (or target a specific widget instead)

        $is_container = 'container' === $element->get_name();
        $is_section = 'section' === $element->get_name();

        if ( $is_container || $is_section ) {
            // Check if we're on the 'tophive-header' post type editor page
            if ( get_post_type( get_the_ID() ) !== 'tophive-header' ) {
                return;
            }
    
    
            // Add a custom control section
            $element->start_controls_section(
                'custom_section_control',
                [
                    'label' => __( 'Header Sticky', 'plugin-name' ) . TH_ELEMENTOR_SECTION_BADGE,
                    'tab' => \Elementor\Controls_Manager::TAB_ADVANCED,
                ]
            );
    
            // Add a custom control to the section
            $element->add_control(
                'tophive_transparent_header', // Control ID
                [
                    'label'        => __( 'Header Trasparent', 'text-domain' ),
                    'type'         => \Elementor\Controls_Manager::SWITCHER,
                    'label_on'     => __( 'Yes', 'text-domain' ),
                    'label_off'    => __( 'No', 'text-domain' ),
                    'return_value' => 'yes',
                    'default'      => 'no',
                ]
            );
    
            // Sticky header enable section - SWITCHER
            $element->add_control(
                'tophive_sticky_header', // Control ID
                [
                    'label'        => __( 'Enable Sticky', 'text-domain' ),
                    'type'         => \Elementor\Controls_Manager::SWITCHER,
                    'label_on'     => __( 'Yes', 'text-domain' ),
                    'label_off'    => __( 'No', 'text-domain' ),
                    'return_value' => 'yes',
                    'default'      => 'no',
                ]
            );

            // Background color when sticked
    
            $element->add_control(
                'tophive_sticky_background',
                [
                    'label'     => __( 'Sticky Background Color', 'text-domain' ),
                    'type'      => \Elementor\Controls_Manager::COLOR,
                    'default'   => '#ffffff',
                ]
            );
    
            $element->add_control(
                'tophive_sticky_border',
                [
                    'label'      => __( 'Sticky Border', 'text-domain' ),
                    'type'       => \Elementor\Controls_Manager::DIMENSIONS,
                    'size_units' => [ 'px' ],
                    'condition'  => [
                        'tophive_sticky_header' => 'yes',
                    ],
                ]
            );

            $element->add_control(
                'tophive_sticky_border_color',
                [
                    'label'     => __( 'Sticky border Color', 'text-domain' ),
                    'type'      => \Elementor\Controls_Manager::COLOR,
                    'default'   => '#ffffff',
                ]
            );

            $element->add_control(
                'box_shadow_starts',
                [
                    'label' => esc_html__( 'Box shadow', 'textdomain' ),
                    'type' => \Elementor\Controls_Manager::HEADING,
                    'separator' => 'before',
                ]
            );

            $element->add_control(
                'tophive_sticky_box_shadow',
                [
                    'label' => esc_html__( 'Box Shadow', 'textdomain' ),
                    'type' => \Elementor\Controls_Manager::BOX_SHADOW,
                    'selectors' => [
                        '{{SELECTOR}}' => 'box-shadow: {{HORIZONTAL}}px {{VERTICAL}}px {{BLUR}}px {{SPREAD}}px {{COLOR}};',
                    ],
                ]
            );
            $element->add_control(
                'box_shadow_end',
                [
                    'label' => esc_html__( '', 'textdomain' ),
                    'type' => \Elementor\Controls_Manager::HEADING,
                    'separator' => 'after',
                ]
            );
    
            $element->add_control(
                'tophive_sticky_height',
                [
                    'label'     => __( 'Sticky Height', 'text-domain' ),
                    'type'      => \Elementor\Controls_Manager::SLIDER,
                    'size_units'=> [ 'px', '%' ],
                    'range'     => [
                        'px' => [
                            'min' => 20,
                            'max' => 150,
                        ],
                    ],
                    'selectors' => [
                        '[data-header-sticky="true"]' => 'height: {{SIZE}}{{UNIT}};',
                    ],
                    'condition' => [
                        'tophive_sticky_header' => 'yes',
                    ],
                ]
            );      
    
            $element->add_control(
                'tophive_sticky_margin',
                [
                    'label'      => __( 'Sticky Margin', 'text-domain' ),
                    'type'       => \Elementor\Controls_Manager::DIMENSIONS,
                    'size_units' => [ 'px', '%' ],
                    'selectors'  => [
                        '{{WRAPPER}}[data-header-sticky="true"].only-sticky-visible' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ],
                    'condition'  => [
                        'tophive_sticky_header' => 'yes',
                    ],
                ]
            );

            $element->add_control(
                'tophive_sticky_top_spacing',
                [
                    'label'       => __( 'Sticky top spacing', 'text-domain' ),
                    'type'      => \Elementor\Controls_Manager::SLIDER,
                    'size_units'=> [ 'px' ],
                    'range'     => [
                        'px' => [
                            'min' => 0,
                            'max' => 50,
                        ],
                    ],
                    'description' => __( 'Maximum top margin in pixels for the sticky header.', 'text-domain' ),
                    'condition'   => [
                        'tophive_sticky_header' => 'yes',
                    ],
                ]
            );
            
            $element->add_control(
                'tophive_sticky_max_border_radius',
                [
                    'label'       => __( 'Max Border Radius', 'text-domain' ),
                    'type'        => \Elementor\Controls_Manager::NUMBER,
                    'default'     => 50,
                    'description' => __( 'Maximum border-radius in pixels for the sticky header.', 'text-domain' ),
                    'condition'   => [
                        'tophive_sticky_header' => 'yes',
                    ],
                ]
            );
            
            $element->add_control(
                'tophive_sticky_min_width',
                [
                    'label'       => __( 'Min Width Percentage', 'text-domain' ),
                    'type'        => \Elementor\Controls_Manager::NUMBER,
                    'default'     => 1,
                    'description' => __( 'Minimum width as a percentage of the full width.', 'text-domain' ),
                    'condition'   => [
                        'tophive_sticky_header' => 'yes',
                    ],
                ]
            );
            
            $element->add_control(
                'tophive_sticky_transition_duration',
                [
                    'label'       => __( 'Transition Duration', 'text-domain' ),
                    'type'        => \Elementor\Controls_Manager::NUMBER,
                    'default'     => 0.6,
                    'description' => __( 'Transition duration in seconds.', 'text-domain' ),
                    'condition'   => [
                        'tophive_sticky_header' => 'yes',
                    ],
                ]
            );
    
    
            // End the section
            $element->end_controls_section();
        }

    }

    function add_custom_classes_to_container( Element_Base $element ) {
    
        // Check if transparent header is enabled
        if ( 'yes' === $element->get_settings( 'tophive_transparent_header' ) ) {
            $element->add_render_attribute( '_wrapper', 'data-header-transparent', 'true' );
        }
        if ( 'yes' === $element->get_settings( 'tophive_sticky_header' ) ) {
            // $settings = $element->get_frontend_settings();
            $element->add_render_attribute( '_wrapper', [
                'data-header-sticky' => 'true',
                'data-sticky-background' => $element->get_settings('tophive_sticky_background'),
                'data-sticky-height' => $element->get_settings('tophive_sticky_height')['size'] ?? '',
                'data-sticky-max-border-radius' => $element->get_settings('tophive_sticky_max_border_radius'),
                'data-sticky-top-spacing' => $element->get_settings('tophive_sticky_top_spacing')['size'] ?? '',
                'data-sticky-border' => [
                    $element->get_settings('tophive_sticky_border')['top'] ?? '', 
                    $element->get_settings('tophive_sticky_border')['right'] ?? '', 
                    $element->get_settings('tophive_sticky_border')['bottom'] ?? '', 
                    $element->get_settings('tophive_sticky_border')['left'] ?? ''
                ],
                'data-sticky-border_color' => $element->get_settings('tophive_sticky_border_color'),
                'data-sticky-box-shadow' => $element->get_settings('tophive_sticky_box_shadow'),
                'data-sticky-text' => "#ffffff", 
                'data-sticky-blur' => "5" 
            ]);
        }
    }

    public function enqueue_dynamic_sticky_styles() {

        if( !class_exists('Tophive_Core_Header') ){
            return;
        }
        $TOPHIVE_HEADER = new Tophive_Core_Header();

        $post_id = $TOPHIVE_HEADER->get_component_id('tophive-header');

        if(!$post_id) return;

        $elementor_data = get_post_meta( $post_id, '_elementor_data', true );
    
        if ( ! $elementor_data ) {
            return;
        }
    
        $elementor_data = json_decode( $elementor_data, false ); // Decode Elementor data
        $custom_css = '';
        foreach ( $elementor_data as $element ) {
            if ( isset( $element->settings->tophive_sticky_background ) && $element->settings->tophive_sticky_background !== '' ) {
    
                // $custom_css .= "
                //     .elementor-{$post_id}.sticky-enabled .elementor-element-{$element->id}.only-sticky-visible{
                //         background-color: ". $element->settings->tophive_sticky_background ." !important;
                //     }
                // ";
            }
        }
    
        if ( $custom_css ) {
            wp_add_inline_style( 'elementor-frontend', $custom_css );
        }
    }
    public function add_custom_class_to_wrapper( $attributes, $document ) {
        // Check if the current document is a WordPress post
        if ( 'wp-post' === $document->get_name() ) {
            $post_id = $document->get_main_id();
    
            // Retrieve raw Elementor data
            $elementor_data = get_post_meta( $post_id, '_elementor_data', true );
            if ( $elementor_data ) {
                $elementor_data = json_decode( $elementor_data, false ); // Decode JSON to an object
    
                // Traverse the data to check for the "tophive_sticky_header" setting
                foreach ( $elementor_data as $element ) {
                    if ( isset( $element->settings->tophive_sticky_header ) && 
                         $element->settings->tophive_sticky_header === 'yes' ) {
                        $attributes['class'] .= ' sticky-enabled';
    
                        // Add dynamic attributes for styling
                        $attributes['data-sticky-top-spacing'] = $element->settings->tophive_sticky_top_spacing->size ?? 0;
                        $attributes['data-sticky-max-border-radius'] = $element->settings->tophive_sticky_max_border_radius ?? 50;
                        $attributes['data-sticky-min-width'] = $element->settings->tophive_sticky_min_width ?? 1;
                        $attributes['data-sticky-transition-duration'] = $element->settings->tophive_sticky_transition_duration ?? 0.6;
                    }
    
                    if ( isset( $element->settings->tophive_transparent_header ) && 
                         $element->settings->tophive_transparent_header === 'yes' ) {
                        $attributes['class'] .= ' tophive-header-transparent';
                    }
    
                    // Exit early if both conditions are satisfied
                    if ( strpos( $attributes['class'], 'sticky-enabled' ) !== false &&
                         strpos( $attributes['class'], 'tophive-header-transparent' ) !== false ) {
                        break;
                    }
                }
            }
        }
    
        $attributes['class'] .= ' tophive-header'; // Add default class
    
        return $attributes;
    }
      
    
}

new Tophive_Elementor_Header_Section_Controls();
