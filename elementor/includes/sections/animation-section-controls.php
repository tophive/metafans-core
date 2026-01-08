<?php
use Elementor\Element_Base;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Typography;
use Elementor\Schemes\Color;
use Elementor\Schemes\Typography;
use Elementor\Utils;
use Elementor\Control_Media;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Background;
use Elementor\Repeater;

class Tophive_Elementor_Animation_Section_Controls{
    public function __construct()
    {
        add_action( 'elementor/element/after_section_end', [$this, 'tophive_custom_animation'], 10, 2 );
        add_action( 'elementor/frontend/before_render', [ $this, 'render_section_attributes' ], 1 );
    }

    public function tophive_custom_animation( $element, $section_id ) {

        if (
            ( $element->get_name() === 'container' && 'section_layout' === $section_id) ||
            'section_advanced' === $section_id ||
            '_section_style' === $section_id
        ) {

            $element->start_controls_section(
                'tophive_custom_animations',
                [
                    'label' => __( 'Animations', TH_ELEMENTOR_SLUG ) . TH_ELEMENTOR_SECTION_BADGE,
                    'tab' => Controls_Manager::TAB_ADVANCED,
                ]
            );

            // th_el_parallax( $element ); // call parallax options
            // th_el_content_animation( $element ); // call content animation options
            self::tophive_animation_controllers($element);
            $element->end_controls_section();
        }
    }

    public static function tophive_animation_controllers($element){

            $section_title = __( 'Animate contents', 'hub-elementor-addons' );
        
            if ( $element->get_name() === 'section'){
                $section_title = __( 'Animate columns', 'hub-elementor-addons' );
            }
        
            $element->add_control(
                'th_custom_animation',
                [
                    'label' => $section_title,
                    'type' => Controls_Manager::SWITCHER,
                    'label_on' => __( 'On', 'hub-elementor-addons' ),
                    'label_off' => __( 'Off', 'hub-elementor-addons' ),
                    'return_value' => 'yes',
                    'default' => '',
                    'separator' => 'before',
                    'render_type' => 'none',
                ]
            );
        
            if ( $element->get_name() === 'container'){
                $element->add_control(
                    'th_ca_targets',
                    [
                        'label' => __( 'Animation Targets', 'hub-elementor-addons' ),
                        'type' => Controls_Manager::SELECT,
                        'default' => 'contents',
                        'options' => [
                            'contents'  => __( 'All contents', 'hub-elementor-addons' ),
                            'container'  => __( 'Only direct inner containers', 'hub-elementor-addons' ),
                        ],
                        'condition' => [
                            'th_custom_animation' => 'yes',
                        ],
                        'render_type' => 'none',
                    ]
                );
                $element->add_control(
                    'th_ca_include_inner_content',
                    [
                        'label' => __( 'Include inner containers content?', 'hub-elementor-addons' ),
                        'type' => Controls_Manager::SWITCHER,
                        'label_on' => __( 'On', 'hub-elementor-addons' ),
                        'label_off' => __( 'Off', 'hub-elementor-addons' ),
                        'return_value' => 'yes',
                        'default' => 'yes',
                        'condition' => [
                            'th_custom_animation' => 'yes',
                            'th_ca_targets' => 'contents'
                        ],
                        'render_type' => 'none',
                    ]
                );
            }
        
            $element->add_control(
                'th_ca_control_apply',
                [
                    'label' => __( 'Play animations', 'hub-elementor-addons' ),
                    'type' => \Elementor\Controls_Manager::BUTTON,
                    'button_type' => 'success',
                    'text' => __( 'Play', 'hub-elementor-addons' ),
                    'condition' => [
                        'th_custom_animation' => 'yes',
                    ],
                    'event' => 'th_ca_apply',
                    'render_type' => 'none',
                ]
            );
            
            Tophive_GSAP_Animation_Helper::custom_settings( $element, 'th_ca_' ,['th_custom_animation' => 'yes'] );
            Tophive_GSAP_Animation_Helper::animation_range_controls( $element, 'th_ca_' , [
                    'th_custom_animation' => 'yes',
                    'th_ca_preset' => 'custom',
            ] );
        
            
    }

    public static function render_section_attributes( Element_Base $element ) {

        $container_selector = version_compare( ELEMENTOR_VERSION, '3.8', '>=' ) ? 'e-con' : 'e-container';
        $container_inner_selector = version_compare( ELEMENTOR_VERSION, '3.8', '>=' ) && $element->get_settings('content_width') === 'boxed' ? '.e-con-inner' : '';

        // Section
		// if ( $element->get_settings( 'liquid_luminosity_data_attr' ) && 'default-auto' !== $element->get_settings( 'liquid_luminosity_data_attr' ) ) {
        //         $element->add_render_attribute( '_wrapper', [
        //         'data-section-luminosity' => $element->get_settings( 'liquid_luminosity_data_attr' ),
        //     ] );
        // }

        // if ( $element->get_settings( 'custom_cursor_on_hover' ) ) {
        //         $element->add_render_attribute( '_wrapper', [
        //         'data-lqd-custom-cursor' => 'true',
        //     ] );
        // }

        // if ( $element->get_settings( 'th_section_scroll' ) ) {
        //         $element->add_render_attribute( '_wrapper', [
        //         'data-lqd-section-scroll' => 'true',
        //     ] );
        // }

        // if ( $element->get_settings( 'hide_on_sticky' ) ) {
        //         $element->add_render_attribute( '_wrapper', [
        //         'class' => $element->get_settings( 'hide_on_sticky' ),
        //     ] );
        // }

        // if ( $element->get_settings( 'show_on_sticky' ) ) {
        //         $element->add_render_attribute( '_wrapper', [
        //         'class' => $element->get_settings( 'show_on_sticky' ),
        //     ] );
        // }

        // if ( $element->get_settings( 'sticky_bar' ) ) {
        //     $placement = $element->get_settings( 'stickybar_placement' );
        //     if ( empty( $placement ) ) {
        //         $placement = 'lqd-stickybar-left';
        //     }
        //     $element->add_render_attribute( '_wrapper', [
        //         'class' => 'lqd-stickybar-wrap '. $placement,
        //     ] );
        // }

        // if ( $element->get_settings( 'th_enable_bottom_shape_animation' ) ) {
        //     $element->add_render_attribute( '_wrapper', [
        //         'class' => $element->get_settings( 'th_enable_bottom_shape_animation' ),
        //     ] );
        // }

        // if ( $element->get_settings( 'th_enable_top_shape_animation' ) ) {
        //     $element->add_render_attribute( '_wrapper', [
        //         'class' => $element->get_settings( 'th_enable_top_shape_animation' ),
        //     ] );
        // }

        // if ( $element->get_settings( 'enable_sticky_column' ) ) {
        //     $element->add_render_attribute( '_wrapper', [
        //         'class' => 'lqd-css-sticky-column',
        //         'style' => '--lqd-sticky-offset:'.$element->get_settings( 'sticky_column_offset' ) ,
        //     ] );
        // }

        // if ( $element->get_settings( 'section_data_tooltip' ) ) {
        //     $element->add_render_attribute( '_wrapper', [
        //         'data-tooltip' => $element->get_settings( 'section_data_tooltip' ),
        //     ] );
        // }

        // if ( $element->get_settings( 'th_sticky_row' ) ) {
        //     $element->add_render_attribute( '_wrapper', [
        //         'class' => $element->get_settings( 'th_sticky_row' ),
        //         'style' => 'top: auto; ' . $element->get_settings( 'th_sticky_row_anchor' ) . ': ' . $element->get_settings( 'th_sticky_row_offset' ) . ';',
        //     ] );
        // }

        // // Scale BG
        // if ( $element->get_settings( 'row_scaleBg_onhover' ) ) {

        //         $image_uri = $element->get_settings( 'background_image' );

        //         $element->add_render_attribute( '_wrapper', [
        //         'class' => 'lqd-scale-bg-onhover',
        //         'data-row-bg' => $image_uri['url'],
        //     ] );
        // }

        // // Parallax
        // if ( $element->get_settings( 'th_parallax' ) ) {

        //     $perspective = $element->get_settings( 'th_parallax_settings_perspective' );

        //     $from_x = $element->get_settings( 'th_parallax_from_x' );
        //     $from_y = $element->get_settings( 'th_parallax_from_y' );
        //     $from_z = $element->get_settings( 'th_parallax_from_z' );

        //     $from_scaleX = $element->get_settings( 'th_parallax_from_scaleX' );
        //     $from_scaleY = $element->get_settings( 'th_parallax_from_scaleY' );

        //     $from_rotationX = $element->get_settings( 'th_parallax_from_rotationX' );
        //     $from_rotationY = $element->get_settings( 'th_parallax_from_rotationY' );
        //     $from_rotationZ = $element->get_settings( 'th_parallax_from_rotationZ' );

        //     $from_opacity = $element->get_settings( 'th_parallax_from_opacity' );

        //     $from_transformOriginX = $element->get_settings( 'th_parallax_from_transformOriginX' );
        //     $from_transformOriginY = $element->get_settings( 'th_parallax_from_transformOriginY' );
        //     $from_transformOriginZ = $element->get_settings( 'th_parallax_from_transformOriginZ' );

        //     $to_x = $element->get_settings( 'th_parallax_to_x' );
        //     $to_y = $element->get_settings( 'th_parallax_to_y' );
        //     $to_z = $element->get_settings( 'th_parallax_to_z' );

        //     $to_scaleX = $element->get_settings( 'th_parallax_to_scaleX' );
        //     $to_scaleY = $element->get_settings( 'th_parallax_to_scaleY' );

        //     $to_rotationX = $element->get_settings( 'th_parallax_to_rotationX' );
        //     $to_rotationY = $element->get_settings( 'th_parallax_to_rotationY' );
        //     $to_rotationZ = $element->get_settings( 'th_parallax_to_rotationZ' );

        //     $to_opacity = $element->get_settings( 'th_parallax_to_opacity' );

        //     $to_transformOriginX = $element->get_settings( 'th_parallax_to_transformOriginX' );
        //     $to_transformOriginY = $element->get_settings( 'th_parallax_to_transformOriginY' );
        //     $to_transformOriginZ = $element->get_settings( 'th_parallax_to_transformOriginZ' );

        //     $parallax_ease = $element->get_settings( 'th_parallax_settings_ease' );
        //     $parallax_duration = $element->get_settings( 'th_parallax_settings_duration' );
        //     $parallax_trigger = $element->get_settings( 'th_parallax_settings_trigger' );
        //     $parallax_trigger_start = $element->get_settings( 'th_parallax_settings_trigger_start' );
        //     $parallax_trigger_end = $element->get_settings( 'th_parallax_settings_trigger_end' );

        //     $wrapper_attributes = $parallax_data = $parallax_data_from = $parallax_data_to = $parallax_opts = array();

        //     if ( !empty( $perspective ) && !empty( $perspective['size'] ) ) { $parallax_data_from['transformPerspective'] = $perspective['size'].$perspective['unit']; }

        //     if ( !empty( $from_x ) && !empty( $to_x ) && $from_x != $to_x ) {
        //         $parallax_data_from['x'] = $from_x['size'].$from_x['unit'];
        //         $parallax_data_to['x'] = $to_x['size'].$to_x['unit'];
        //     }
        //     if ( !empty( $from_y ) && !empty( $to_y ) && $from_y != $to_y ) {
        //         $parallax_data_from['y'] = $from_y['size'].$from_y['unit'];
        //         $parallax_data_to['y'] = $to_y['size'].$to_y['unit'];
        //     }
        //     if ( !empty( $from_z ) && !empty( $to_z ) && $from_z != $to_z ) {
        //         $parallax_data_from['z'] = $from_z['size'].$from_z['unit'];
        //         $parallax_data_to['z'] = $to_z['size'].$to_z['unit'];
        //     }

        //     if ( !empty( $from_scaleX ) && !empty( $to_scaleX ) && $from_scaleX != $to_scaleX ) {
        //         $parallax_data_from['scaleX'] = (float) $from_scaleX['size'];
        //         $parallax_data_to['scaleX'] = (float) $to_scaleX['size'];
        //     }
        //     if ( !empty( $from_scaleY ) && !empty( $to_scaleY ) && $from_scaleY != $to_scaleY ) {
        //         $parallax_data_from['scaleY'] = (float) $from_scaleY['size'];
        //         $parallax_data_to['scaleY'] = (float) $to_scaleY['size'];
        //     }

        //     if ( !empty( $from_rotationX ) && !empty( $to_rotationX ) && $from_rotationX != $to_rotationX ) {
        //         $parallax_data_from['rotationX'] = (int) $from_rotationX['size'];
        //         $parallax_data_to['rotationX'] = (int) $to_rotationX['size'];
        //     }
        //     if ( !empty( $from_rotationY ) && !empty( $to_rotationY ) && $from_rotationY != $to_rotationY ) {
        //         $parallax_data_from['rotationY'] = (int) $from_rotationY['size'];
        //         $parallax_data_to['rotationY'] = (int) $to_rotationY['size'];
        //     }
        //     if ( !empty( $from_rotationZ ) && !empty( $to_rotationZ ) && $from_rotationZ != $to_rotationZ ) {
        //         $parallax_data_from['rotationZ'] = (int) $from_rotationZ['size'];
        //         $parallax_data_to['rotationZ'] = (int) $to_rotationZ['size'];
        //     }

        //     if ( !empty( $from_opacity ) && !empty( $to_opacity ) && $from_opacity != $to_opacity ) {
        //         $parallax_data_from['opacity'] = (float) $from_opacity['size'];
        //         $parallax_data_to['opacity'] = (float) $to_opacity['size'];
        //     }

        //     $from_toriginX = isset( $from_transformOriginX ) && ! empty( $from_transformOriginX ) ? $from_transformOriginX['size'].$from_transformOriginX['unit'] : '';
        //     $from_toriginY = isset( $from_transformOriginY ) && ! empty( $from_transformOriginY ) ? $from_transformOriginY['size'].$from_transformOriginY['unit'] : '';
        //     $from_toriginZ = isset( $from_transformOriginZ ) && ! empty( $from_transformOriginZ ) ? $from_transformOriginZ['size'].$from_transformOriginZ['unit'] : '';

        //     $to_toriginX = isset( $to_transformOriginX ) && ! empty( $to_transformOriginX ) ? $to_transformOriginX['size'].$to_transformOriginX['unit'] : '';
        //     $to_toriginY = isset( $to_transformOriginY ) && ! empty( $to_transformOriginY ) ? $to_transformOriginY['size'].$to_transformOriginY['unit'] : '';
        //     $to_toriginZ = isset( $to_transformOriginZ ) && ! empty( $to_transformOriginZ ) ? $to_transformOriginZ['size'].$to_transformOriginZ['unit'] : '';

        //     if (
        //         ! empty( $from_toriginX ) && ! empty( $from_toriginY ) && ! empty( $from_toriginZ ) &&
        //         ! empty( $to_toriginX ) && ! empty( $to_toriginY ) && ! empty( $to_toriginZ )
        //     ) {
        //         $parallax_data_from['transformOrigin'] = $from_toriginX . ' ' . $from_toriginY . ' ' . $from_toriginZ;
        //         $parallax_data_to['transformOrigin'] = $to_toriginX . ' ' . $to_toriginY . ' ' . $to_toriginZ;
        //     }

        //     if ( $parallax_data_from['transformOrigin'] == $parallax_data_to['transformOrigin'] ) {
        //         unset($parallax_data_from['transformOrigin']);
        //         unset($parallax_data_to['transformOrigin']);
        //     }

        //     //Parallax general options
        //     $parallax_data['from'] = $parallax_data_from;
        //     $parallax_data['to'] = $parallax_data_to;

        //     if( is_array( $parallax_data['from'] ) && ! empty( $parallax_data['from'] ) ) {
        //         $wrapper_attributes[] = 'data-parallax-from=\'' . wp_json_encode( $parallax_data['from'] ) . '\'';
        //     }
        //     if( is_array( $parallax_data['to'] ) && ! empty( $parallax_data['to'] ) ) {
        //         $wrapper_attributes[] = 'data-parallax-to=\'' . wp_json_encode( $parallax_data['to'] ) . '\'';
        //     }

        //     if ( isset( $parallax_ease ) ) { $parallax_opts['ease'] = $parallax_ease; }
        //     if( 'custom' !== $parallax_trigger ){
        //         $parallax_opts['start'] = esc_attr( $parallax_trigger );
        //         if ( isset($parallax_duration) && ! empty($parallax_duration) ) {
        //             $parallax_duration_size = (float) $parallax_duration['size'];
        //             $dur = $parallax_duration_size >= 0 ? '+='.abs($parallax_duration_size).$parallax_duration['unit'].'' : '-='.abs($parallax_duration_size).$parallax_duration['unit'].'';
        //             $parallax_opts['end'] = esc_attr( 'bottom'  . $dur . ' top' );
        //         }
        //     } else {
        //         if ( ! empty( $parallax_trigger_start ) ) {
        //             $parallax_opts['start'] = esc_attr( $parallax_trigger_start );
        //         }
        //         if ( ! empty( $parallax_trigger_end ) ) {
        //             $parallax_opts['end'] = esc_attr( $parallax_trigger_end );
        //         }
        //     }
        //     if( ! empty( $parallax_opts ) ) {
        //         $wrapper_attributes[] = 'data-parallax-options=\'' . wp_json_encode( $parallax_opts ) .'\'';
        //     }

        //     $element->add_render_attribute( '_wrapper', [
        //         'data-parallax' => 'true',
        //         'data-parallax-options' => wp_json_encode( $parallax_opts ),
        //         'data-parallax-from' => wp_json_encode( $parallax_data['from'] ),
        //         'data-parallax-to' => wp_json_encode( $parallax_data['to'] ),
        //     ] );

        // }

        // Animation
        if ( $element->get_settings( 'th_custom_animation' ) ) {
            $prefix = 'th_ca_';

            $animation_options = $animation_from = $animation_to = $animation_presets = $animation_targets = array();

            $animation_preset = $element->get_settings( $prefix . 'preset' );

            $animation_options['addChildTimelines'] = false;
            // $animation_options['addPerspective'] = false;

            switch ( $element->get_name() ){
                case 'container':
                    if ( $element->get_settings('th_ca_targets') === 'contents' ) {
                        array_push($animation_targets, ':scope ' . $container_inner_selector . ' .elementor-element .elementor-widget-container');
                        array_push($animation_targets, ':scope ' . $container_inner_selector . ' .elementor-widget-tophive-advanced-heading.split-by-lines .lines');
                        array_push($animation_targets, ':scope ' . $container_inner_selector . ' .elementor-widget-tophive-advanced-heading.split-by-words .words');
                        array_push($animation_targets, ':scope ' . $container_inner_selector . ' .elementor-widget-tophive-advanced-heading.split-by-chars .chars');
                        // array_push($animation_targets, ':scope ' . $container_inner_selector . ' > .elementor-widget-tophive-advanced-heading .lqd-adv-txt-fig');
                        // array_push($animation_targets, ':scope ' . $container_inner_selector . ' > .elementor-widget-th_custom_menu .lqd-fancy-menu > ul > li');
                        if ( $element->get_settings('th_ca_include_inner_content') === 'yes' ) {
                            // array_push($animation_targets, ':scope .' . $container_selector . ':not([data-parallax]) .elementor-element > .elementor-widget-container');
                            array_push($animation_targets, ':scope .' . $container_selector . ':not([data-parallax]) .elementor-widget-tophive-advanced-heading.split-by-lines .lines');
                            array_push($animation_targets, ':scope .' . $container_selector . ':not([data-parallax]) .elementor-widget-tophive-advanced-heading.split-by-words .words');
                            array_push($animation_targets, ':scope .' . $container_selector . ':not([data-parallax]) .elementor-widget-tophive-advanced-heading.split-by-chars .chars');
                            array_push($animation_targets, ':scope .' . $container_selector . ':not([data-parallax]) .elementor-widget-th_custom_menu .lqd-fancy-menu > ul > li');
                        }
                    } else {
                        array_push($animation_targets, ':scope ' . $container_inner_selector . ' > .' . $container_selector . '');
                    }
                break;
                case 'section':
                    array_push($animation_targets, ':scope > .elementor-container > .elementor-column');
                break;
                case 'column':
                    // $animation_options['addChildTimelines'] = true;
                    array_push($animation_targets, ':scope > .elementor-widget-wrap > .elementor-element > .elementor-widget-container');
                    array_push($animation_targets, ':scope > .elementor-widget-wrap > .elementor-section > .elementor-container > .elementor-column > .elementor-widget-wrap > .elementor-element:not(.lqd-el-has-inner-anim) > .elementor-widget-container');
                    array_push($animation_targets, ':scope > .elementor-widget-wrap > .elementor-widget-tophive-advanced-heading.split-by-lines .line');
                    array_push($animation_targets, ':scope > .elementor-widget-wrap > .elementor-widget-tophive-advanced-heading.split-by-words .word');
                    array_push($animation_targets, ':scope > .elementor-widget-wrap > .elementor-widget-tophive-advanced-heading.split-by-chars .char');
                    array_push($animation_targets, ':scope > .elementor-widget-wrap > .elementor-widget-th_custom_menu .lqd-fancy-menu > ul > li');
                break;
                case 'th_carousel':
                case 'th_testimonial_carousel':
                    array_push($animation_targets, '[data-lqd-flickity] > .flickity-viewport > .flickity-slider > .carousel-item > .carousel-item-inner');
                break;
                case 'tophive-posts-card':
                    array_push($animation_targets, '.tophive-card-element .tophive-content-card');
                break;
                case 'th_woo_products_list':
                    array_push($animation_targets, '.lqd-prod-item');
                break;
                case 'th_portfolio':
                    array_push($animation_targets, '.lqd-pf-item');
                break;
                default:
                    if( $element->get_name() === 'hub_fancy_heading' && $element->get_settings( 'enable_split' ) ){

                        $split_type = $element->get_settings( 'split_type' );

                        if ( $split_type === 'lines' ){
                            array_push($animation_targets, '.lqd-split-lines .lqd-lines .split-inner');
                        } else if ( $split_type === 'words' ){
                            array_push($animation_targets, '.lqd-split-words .lqd-words .split-inner');
                        } else if ( $split_type === 'chars, words' ){
                            array_push($animation_targets, '.lqd-split-chars .lqd-chars .split-inner');
                        }
                        array_push($animation_targets, '.lqd-adv-txt-fig');
                    } else if ( $element->get_name() === 'th_custom_menu' ) {
                        array_push($animation_targets, ':scope .lqd-fancy-menu > ul > li');
                    } else {
                        array_push($animation_targets, ':scope > .elementor-widget-container');
                    }

                break;
            }

            $animation_options['animationTarget'] = implode(', ', $animation_targets);

            $settings = Tophive_GSAP_Animation_Helper::extract_animation_settings( $element, 'th_ca_');

            $animation_options = array_merge($animation_options, $settings);

            if( 'custom' !== $animation_preset ) {
                $animation_presets = Tophive_GSAP_Animation_Helper::extract_preset_values($element, $prefix);
                $animation_from = $animation_presets['from'];
                $animation_to = $animation_presets['to'];
            }
            else {

                $custom_values = Tophive_GSAP_Animation_Helper::extract_custom_values( $element, 'th_ca_');
                $animation_from = $custom_values['from'];
                $animation_to = $custom_values['to'];
            }

            $animation_options['initValues'] = !empty( $animation_from ) ? $animation_from : array();
            $animation_options['animations'] = !empty( $animation_to ) ? $animation_to : array();

            $element->add_render_attribute( '_wrapper', [
                'data-tophive-custom-animations' => 'true',
                'data-animations-options' => stripslashes( wp_json_encode( $animation_options ) ),
            ] );

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

new Tophive_Elementor_Animation_Section_Controls();