<?php

class Tophive_GSAP_Animation_Helper{
    public static function custom_settings( $widget, $prefix, $deps = [] ){
        $widget->add_control(
            $prefix . 'settings_popover',
            [
                'label' => __( 'Settings', 'hub-elementor-addons' ),
                'type' => \Elementor\Controls_Manager::POPOVER_TOGGLE,
                'label_off' => __( 'Default', 'hub-elementor-addons' ),
                'label_on' => __( 'Custom', 'hub-elementor-addons' ),
                'return_value' => 'yes',
                'default' => 'yes',
                'condition' => $deps,
                'render_type' => 'none',
            ]
        );
    
        // Animation Settings
        $widget->start_popover();
            $widget->add_control(
                $prefix . 'preset',
                [
                    'label' => __( 'Animation Defaults', 'hub-elementor-addons' ),
                    'type' => \Elementor\Controls_Manager::SELECT,
                    'default' => 'custom',
                    'options' => [
                        'custom'  => __( 'Custom', 'hub-elementor-addons' ),
                        'Fade In'  => __( 'Fade In', 'hub-elementor-addons' ),
                        'Fade In Down'  => __( 'Fade In Down', 'hub-elementor-addons' ),
                        'Fade In Up'  => __( 'Fade In Up', 'hub-elementor-addons' ),
                        'Fade In Left'  => __( 'Fade In Left', 'hub-elementor-addons' ),
                        'Fade In Right'  => __( 'Fade In Right', 'hub-elementor-addons' ),
                        'Flip In Y'  => __( 'Flip In Y', 'hub-elementor-addons' ),
                        'Flip In X'  => __( 'Flip In X', 'hub-elementor-addons' ),
                        'Scale Up'  => __( 'Scale Up', 'hub-elementor-addons' ),
                        'Scale Down'  => __( 'Scale Down', 'hub-elementor-addons' ),
                    ],
                    'condition' => [
                        $prefix . 'settings_popover' => 'yes'
                    ],
                    'render_type' => 'none',
                ]
            );
    
            $widget->add_control(
                $prefix . 'settings_ease',
                [
                    'label' => __( 'Easing', 'hub-elementor-addons' ),
                    'type' => \Elementor\Controls_Manager::SELECT,
                    'default' => [ 'power4.out' ],
                    'options' => [
                        'linear' => 'linear',
                        'sine.in' => 'sine.in',
                        'expo.in' => 'expo.in',
                        'circ.in' => 'circ.in',
                        'back.in' => 'back.in',
                        'sine.out' => 'sine.out',
                        'expo.out' => 'expo.out',
                        'circ.out' => 'circ.out',
                        'back.out' => 'back.out',
                        'bounce.in' => 'bounce.in',
                        'power1.in' => 'power1.in',
                        'power2.in' => 'power2.in',
                        'power3.in' => 'power3.in',
                        'power4.in' => 'power4.in',
                        'power1.out' => 'power1.out',
                        'power2.out' => 'power2.out',
                        'power3.out' => 'power3.out',
                        'power4.out' => 'power4.out',
                        'power1.inOut' => 'power1.inOut',
                        'power2.inOut' => 'power2.inOut',
                        'power3.inOut' => 'power3.inOut',
                        'power4.inOut' => 'power4.inOut',
                        'sine.inOut' => 'sine.inOut',
                        'expo.inOut' => 'expo.inOut',
                        'circ.inOut' => 'circ.inOut',
                        'back.inOut' => 'back.inOut',
                        'bounce.out' => 'bounce.out',
                        'bounce.inOut' => 'bounce.inOut',
                        'elastic.in(1,0.2)' => 'elastic.in(1,0.2)',
                        'elastic.out(1,0.2)' => 'elastic.out(1,0.2)',
                        'elastic.inOut(1,0.2)' => 'elastic.inOut(1,0.2)',
                    ],
                    'condition' => [
                        $prefix . 'settings_popover' => 'yes'
                    ],
                    'render_type' => 'none',
                ]
            );
    
            $widget->add_control(
                $prefix . 'settings_direction',
                [
                    'label' => __( 'Direction', 'hub-elementor-addons' ),
                    'type' => \Elementor\Controls_Manager::SELECT,
                    'default' => 'forward',
                    'options' => [
                        'forward' => __( 'From Start', 'hub-elementor-addons' ),
                        'backward' => __( 'From End', 'hub-elementor-addons' ),
                        'center' => __( 'From Center', 'hub-elementor-addons' ),
                        'edges' => __( 'From Edges', 'hub-elementor-addons' ),
                        'random' => __( 'Random', 'hub-elementor-addons' ),
                    ],
                    'condition' => [
                        $prefix . 'settings_popover' => 'yes'
                    ],
                    'render_type' => 'none',
                ]
            );
    
            $widget->add_control(
                $prefix . 'settings_duration',
                [
                    'label' => __( 'Duration', 'hub-elementor-addons' ),
                    'type' => \Elementor\Controls_Manager::SLIDER,
                    'size_units' => [ 'px' ],
                    'range' => [
                        'px' => [
                            'min' => 0,
                            'max' => 10,
                            'step' => 0.1,
                        ],
                    ],
                    'default' => [
                        'size' => 1.6,
                    ],
                    'condition' => [
                        $prefix . 'settings_popover' => 'yes'
                    ],
                    'render_type' => 'none',
                ]
            );
    
            $widget->add_control(
                $prefix . 'settings_stagger',
                [
                    'label' => __( 'Stagger', 'hub-elementor-addons' ),
                    'description' => __( 'Delay between animated elements.', 'hub-elementor-addons' ),
                    'type' => \Elementor\Controls_Manager::SLIDER,
                    'size_units' => [ 'px' ],
                    'range' => [
                        'px' => [
                            'min' => 0,
                            'max' => 10,
                            'step' => 0.1,
                        ],
                    ],
                    'default' => [
                        'size' => .16,
                    ],
                    'condition' => [
                        $prefix . 'settings_popover' => 'yes'
                    ],
                    'render_type' => 'none',
                ]
            );
    
            $widget->add_control(
                $prefix . 'settings_start_delay',
                [
                    'label' => __( 'Start Delay', 'hub-elementor-addons' ),
                    'description' => __( 'Start delay of the animation.', 'hub-elementor-addons' ),
                    'type' => \Elementor\Controls_Manager::SLIDER,
                    'size_units' => [ 'px' ],
                    'range' => [
                        'px' => [
                            'min' => -0,
                            'max' => 10,
                            'step' => 0.1,
                        ],
                    ],
                    'default' => [
                        'size' => 0,
                    ],
                    'condition' => [
                        $prefix . 'settings_popover' => 'yes'
                    ],
                    'render_type' => 'none',
                ]
            );
        $widget->end_popover();
    }
    public static function animation_range_controls( $widget, $prefix, $deps = [] ){
        // From Options
        $widget->add_control(
            $prefix . 'from_popover',
            [
                'label' => __( 'Animate from', 'hub-elementor-addons' ),
                'type' => \Elementor\Controls_Manager::POPOVER_TOGGLE,
                'label_off' => __( 'Default', 'hub-elementor-addons' ),
                'label_on' => __( 'Custom', 'hub-elementor-addons' ),
                'return_value' => 'yes',
                'default' => 'yes',
                'condition' => $deps,
                'render_type' => 'none',
            ]
        );
    
        $widget->start_popover();
            $widget->add_control(
                $prefix . 'from_x',
                [
                    'label' => __( 'Translate X', 'hub-elementor-addons' ),
                    'type' => \Elementor\Controls_Manager::SLIDER,
                    'size_units' => [ 'px', '%', 'vw', 'vh' ],
                    'range' => [
                        'px' => [
                                'min' => -500,
                                'max' => 500,
                                'step' => 1,
                            ],
                            '%' => [
                                'min' => -100,
                                'max' => 100,
                                'step' => 0.1,
                            ],
                            'vw' => [
                                'min' => -100,
                                'max' => 100,
                                'step' => 0.1,
                            ],
                            'vh' => [
                                'min' => -100,
                                'max' => 100,
                                'step' => 0.1,
                            ],
                    ],
                    'default' => [
                        'size' => 0,
                    ],
                    'condition' => [
                        $prefix . 'from_popover' => 'yes',
                        $prefix . 'preset' => 'custom',
                    ],
                    'render_type' => 'none',
                ]
            );
    
            $widget->add_control(
                $prefix . 'from_y',
                [
                    'label' => __( 'Translate Y', 'hub-elementor-addons' ),
                    'type' => \Elementor\Controls_Manager::SLIDER,
                    'size_units' => [ 'px', '%', 'vw', 'vh' ],
                    'range' => [
                        'px' => [
                                'min' => -500,
                                'max' => 500,
                                'step' => 1,
                            ],
                            '%' => [
                                'min' => -100,
                                'max' => 100,
                                'step' => 0.1,
                            ],
                            'vw' => [
                                'min' => -100,
                                'max' => 100,
                                'step' => 0.1,
                            ],
                            'vh' => [
                                'min' => -100,
                                'max' => 100,
                                'step' => 0.1,
                            ],
                    ],
                    'default' => [
                        'size' => 0,
                    ],
                    'condition' => [
                        $prefix . 'from_popover' => 'yes',
                        $prefix . 'preset' => 'custom',
                    ],
                    'render_type' => 'none',
                ]
            );
    
            $widget->add_control(
                $prefix . 'from_z',
                [
                    'label' => __( 'Translate Z', 'hub-elementor-addons' ),
                    'type' => \Elementor\Controls_Manager::SLIDER,
                    'size_units' => [ 'px' ],
                    'range' => [
                        'px' => [
                                'min' => -500,
                                'max' => 500,
                                'step' => 1
                        ]
                    ],
                    'default' => [
                        'size' => 0,
                    ],
                    'separator' => 'after',
                    'condition' => [
                        $prefix . 'from_popover' => 'yes',
                        $prefix . 'preset' => 'custom',
                    ],
                    'render_type' => 'none',
                ]
            );
    
            $widget->add_control(
                $prefix . 'from_scaleX',
                [
                    'label' => __( 'Scale X', 'hub-elementor-addons' ),
                    'type' => \Elementor\Controls_Manager::SLIDER,
                    'size_units' => [ 'px' ],
                    'range' => [
                        'px' => [
                            'min' => 0,
                            'max' => 5,
                            'step' => 0.1,
                        ],
                    ],
                    'default' => [
                        'size' => 1,
                    ],
                    'condition' => [
                        $prefix . 'from_popover' => 'yes',
                        $prefix . 'preset' => 'custom',
                    ],
                    'render_type' => 'none',
                ]
            );
    
            $widget->add_control(
                $prefix . 'from_scaleY',
                [
                    'label' => __( 'Scale Y', 'hub-elementor-addons' ),
                    'type' => \Elementor\Controls_Manager::SLIDER,
                    'size_units' => [ 'px' ],
                    'range' => [
                        'px' => [
                            'min' => 0,
                            'max' => 5,
                            'step' => 0.1,
                        ],
                    ],
                    'default' => [
                        'size' => 1,
                    ],
                    'condition' => [
                        $prefix . 'from_popover' => 'yes',
                        $prefix . 'preset' => 'custom',
                    ],
                    'separator' => 'after',
                    'render_type' => 'none',
                ]
            );
    
            $widget->add_control(
                $prefix . 'from_rotationX',
                [
                    'label' => __( 'Rotate X', 'hub-elementor-addons' ),
                    'type' => \Elementor\Controls_Manager::SLIDER,
                    'size_units' => [ 'px' ],
                    'range' => [
                        'px' => [
                            'min' => -360,
                            'max' => 360,
                            'step' => 1,
                        ],
                    ],
                    'default' => [
                        'size' => 0,
                    ],
                    'condition' => [
                        $prefix . 'from_popover' => 'yes',
                        $prefix . 'preset' => 'custom',
                    ],
                    'render_type' => 'none',
                ]
            );
    
            $widget->add_control(
                $prefix . 'from_rotationY',
                [
                    'label' => __( 'Rotate Y', 'hub-elementor-addons' ),
                    'type' => \Elementor\Controls_Manager::SLIDER,
                    'size_units' => [ 'px' ],
                    'range' => [
                        'px' => [
                            'min' => -360,
                            'max' => 360,
                            'step' => 1,
                        ],
                    ],
                    'default' => [
                        'size' => 0,
                    ],
                    'condition' => [
                        $prefix . 'from_popover' => 'yes',
                        $prefix . 'preset' => 'custom',
                    ],
                    'render_type' => 'none',
                ]
            );
    
            $widget->add_control(
                $prefix . 'from_rotationZ',
                [
                    'label' => __( 'Rotate Z', 'hub-elementor-addons' ),
                    'type' => \Elementor\Controls_Manager::SLIDER,
                    'size_units' => [ 'px' ],
                    'range' => [
                        'px' => [
                            'min' => -360,
                            'max' => 360,
                            'step' => 1,
                        ],
                    ],
                    'default' => [
                        'size' => 0,
                    ],
                    'separator' => 'after',
                    'condition' => [
                        $prefix . 'from_popover' => 'yes',
                        $prefix . 'preset' => 'custom',
                    ],
                    'render_type' => 'none',
                ]
            );
    
    
            $widget->add_control(
                $prefix . 'from_opacity',
                [
                    'label' => __( 'Opacity', 'hub-elementor-addons' ),
                    'type' => \Elementor\Controls_Manager::SLIDER,
                    'size_units' => [ 'px' ],
                    'range' => [
                        'px' => [
                            'min' => 0,
                            'max' => 1,
                            'step' => 0.1,
                        ],
                    ],
                    'default' => [
                        'size' => 1,
                    ],
                    'condition' => [
                        $prefix . 'from_popover' => 'yes',
                        $prefix . 'preset' => 'custom',
                    ],
                    'render_type' => 'none',
                ]
            );
    
            $widget->add_control(
                $prefix . 'from_transformOriginX',
                [
                    'label' => __( 'Transform origin X', 'hub-elementor-addons' ),
                    'type' => \Elementor\Controls_Manager::SLIDER,
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
                                'step' => 0.1,
                            ],
                    ],
                    'default' => [
                        'size' => 50,
                        'unit' => '%',
                    ],
                    'separator' => 'before',
                    'condition' => [
                        $prefix . 'from_popover' => 'yes',
                        $prefix . 'preset' => 'custom',
                    ],
                    'render_type' => 'none',
                ]
            );
    
            $widget->add_control(
                $prefix . 'from_transformOriginY',
                [
                    'label' => __( 'Transform origin Y', 'hub-elementor-addons' ),
                    'type' => \Elementor\Controls_Manager::SLIDER,
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
                                'step' => 0.1,
                            ],
                    ],
                    'default' => [
                        'size' => 50,
                        'unit' => '%',
                    ],
                    'condition' => [
                        $prefix . 'from_popover' => 'yes',
                        $prefix . 'preset' => 'custom',
                    ],
                    'render_type' => 'none',
                ]
            );
    
            $widget->add_control(
                $prefix . 'from_transformOriginZ',
                [
                    'label' => __( 'Transform origin Z', 'hub-elementor-addons' ),
                    'type' => \Elementor\Controls_Manager::SLIDER,
                    'size_units' => [ 'px' ],
                    'range' => [
                        'px' => [
                                'min' => -500,
                                'max' => 500,
                                'step' => 1,
                            ],
                    ],
                    'default' => [
                        'size' => 0,
                    ],
                    'separator' => 'after',
                    'condition' => [
                        $prefix . 'from_popover' => 'yes',
                        $prefix . 'preset' => 'custom',
                    ],
                    'render_type' => 'none',
                ]
            );
        $widget->end_popover();
    
        // To Options
        $widget->add_control(
            $prefix . 'to_popover',
            [
                'label' => __( 'Animate to', 'hub-elementor-addons' ),
                'type' => \Elementor\Controls_Manager::POPOVER_TOGGLE,
                'label_off' => __( 'Default', 'hub-elementor-addons' ),
                'label_on' => __( 'Custom', 'hub-elementor-addons' ),
                'return_value' => 'yes',
                'default' => 'yes',
                'condition' => $deps,
                'render_type' => 'none',
            ]
        );
    
        $widget->start_popover();
            $widget->add_control(
                $prefix . 'to_x',
                [
                    'label' => __( 'Translate X', 'hub-elementor-addons' ),
                    'type' => \Elementor\Controls_Manager::SLIDER,
                    'size_units' => [ 'px', '%', 'vw', 'vh' ],
                    'range' => [
                        'px' => [
                                'min' => -500,
                                'max' => 500,
                                'step' => 1,
                            ],
                            '%' => [
                                'min' => -100,
                                'max' => 100,
                                'step' => 0.1,
                            ],
                            'vw' => [
                                'min' => -100,
                                'max' => 100,
                                'step' => 0.1,
                            ],
                            'vh' => [
                                'min' => -100,
                                'max' => 100,
                                'step' => 0.1,
                            ],
                    ],
                    'default' => [
                        'size' => 0,
                    ],
                    'condition' => [
                        $prefix . 'to_popover' => 'yes',
                        $prefix . 'preset' => 'custom',
                    ],
                    'render_type' => 'none',
                ]
            );
    
            $widget->add_control(
                $prefix . 'to_y',
                [
                    'label' => __( 'Translate Y', 'hub-elementor-addons' ),
                    'type' => \Elementor\Controls_Manager::SLIDER,
                    'size_units' => [ 'px', '%', 'vw', 'vh' ],
                    'range' => [
                        'px' => [
                                'min' => -500,
                                'max' => 500,
                                'step' => 1,
                            ],
                            '%' => [
                                'min' => -100,
                                'max' => 100,
                                'step' => 0.1,
                            ],
                            'vw' => [
                                'min' => -100,
                                'max' => 100,
                                'step' => 0.1,
                            ],
                            'vh' => [
                                'min' => -100,
                                'max' => 100,
                                'step' => 0.1,
                            ],
                    ],
                    'default' => [
                        'size' => 0,
                    ],
                    'condition' => [
                        $prefix . 'to_popover' => 'yes',
                        $prefix . 'preset' => 'custom',
                    ],
                    'render_type' => 'none',
                ]
            );
    
            $widget->add_control(
                $prefix . 'to_z',
                [
                    'label' => __( 'Translate Z', 'hub-elementor-addons' ),
                    'type' => \Elementor\Controls_Manager::SLIDER,
                    'size_units' => [ 'px' ],
                    'range' => [
                        'px' => [
                                'min' => -500,
                                'max' => 500,
                                'step' => 1
                        ]
                    ],
                    'default' => [
                        'size' => 0,
                    ],
                    'separator' => 'after',
                    'condition' => [
                        $prefix . 'to_popover' => 'yes',
                        $prefix . 'preset' => 'custom',
                    ],
                    'render_type' => 'none',
                ]
            );
    
            $widget->add_control(
                $prefix . 'to_scaleX',
                [
                    'label' => __( 'Scale X', 'hub-elementor-addons' ),
                    'type' => \Elementor\Controls_Manager::SLIDER,
                    'size_units' => [ 'px' ],
                    'range' => [
                        'px' => [
                            'min' => 0,
                            'max' => 5,
                            'step' => 0.1,
                        ],
                    ],
                    'default' => [
                        'size' => 1,
                    ],
                    'condition' => [
                        $prefix . 'to_popover' => 'yes',
                        $prefix . 'preset' => 'custom',
                    ],
                    'render_type' => 'none',
                ]
            );
    
            $widget->add_control(
                $prefix . 'to_scaleY',
                [
                    'label' => __( 'Scale Y', 'hub-elementor-addons' ),
                    'type' => \Elementor\Controls_Manager::SLIDER,
                    'size_units' => [ 'px' ],
                    'range' => [
                        'px' => [
                            'min' => 0,
                            'max' => 5,
                            'step' => 0.1,
                        ],
                    ],
                    'default' => [
                        'size' => 1,
                    ],
                    'condition' => [
                        $prefix . 'to_popover' => 'yes',
                        $prefix . 'preset' => 'custom',
                    ],
                    'separator' => 'after',
                    'render_type' => 'none',
                ]
            );
    
            $widget->add_control(
                $prefix . 'to_rotationX',
                [
                    'label' => __( 'Rotate X', 'hub-elementor-addons' ),
                    'type' => \Elementor\Controls_Manager::SLIDER,
                    'size_units' => [ 'px' ],
                    'range' => [
                        'px' => [
                            'min' => -360,
                            'max' => 360,
                            'step' => 1,
                        ],
                    ],
                    'default' => [
                        'size' => 0,
                    ],
                    'condition' => [
                        $prefix . 'to_popover' => 'yes',
                        $prefix . 'preset' => 'custom',
                    ],
                    'render_type' => 'none',
                ]
            );
    
            $widget->add_control(
                $prefix . 'to_rotationY',
                [
                    'label' => __( 'Rotate Y', 'hub-elementor-addons' ),
                    'type' => \Elementor\Controls_Manager::SLIDER,
                    'size_units' => [ 'px' ],
                    'range' => [
                        'px' => [
                            'min' => -360,
                            'max' => 360,
                            'step' => 1,
                        ],
                    ],
                    'default' => [
                        'size' => 0,
                    ],
                    'condition' => [
                        $prefix . 'to_popover' => 'yes',
                        $prefix . 'preset' => 'custom',
                    ],
                    'render_type' => 'none',
                ]
            );
    
            $widget->add_control(
                $prefix . 'to_rotationZ',
                [
                    'label' => __( 'Rotate Z', 'hub-elementor-addons' ),
                    'type' => \Elementor\Controls_Manager::SLIDER,
                    'size_units' => [ 'px' ],
                    'range' => [
                        'px' => [
                            'min' => -360,
                            'max' => 360,
                            'step' => 1,
                        ],
                    ],
                    'default' => [
                        'size' => 0,
                    ],
                    'separator' => 'after',
                    'condition' => [
                        $prefix . 'from_popover' => 'yes',
                        $prefix . 'preset' => 'custom',
                    ],
                    'render_type' => 'none',
                ]
            );
    
    
            $widget->add_control(
                $prefix . 'to_opacity',
                [
                    'label' => __( 'Opacity', 'hub-elementor-addons' ),
                    'type' => \Elementor\Controls_Manager::SLIDER,
                    'size_units' => [ 'px' ],
                    'range' => [
                        'px' => [
                            'min' => 0,
                            'max' => 1,
                            'step' => 0.1,
                        ],
                    ],
                    'default' => [
                        'size' => 1,
                    ],
                    'condition' => [
                        $prefix . 'to_popover' => 'yes',
                        $prefix . 'preset' => 'custom',
                    ],
                    'render_type' => 'none',
                ]
            );
    
            $widget->add_control(
                $prefix . 'to_transformOriginX',
                [
                    'label' => __( 'Transform origin X', 'hub-elementor-addons' ),
                    'type' => \Elementor\Controls_Manager::SLIDER,
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
                                'step' => 0.1,
                            ],
                    ],
                    'default' => [
                        'size' => 50,
                        'unit' => '%',
                    ],
                    'separator' => 'before',
                    'condition' => [
                        $prefix . 'to_popover' => 'yes',
                        $prefix . 'preset' => 'custom',
                    ],
                    'render_type' => 'none',
                ]
            );
    
            $widget->add_control(
                $prefix . 'to_transformOriginY',
                [
                    'label' => __( 'Transform origin Y', 'hub-elementor-addons' ),
                    'type' => \Elementor\Controls_Manager::SLIDER,
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
                                'step' => 0.1,
                            ],
                    ],
                    'default' => [
                        'size' => 50,
                        'unit' => '%',
                    ],
                    'condition' => [
                        $prefix . 'to_popover' => 'yes',
                        $prefix . 'preset' => 'custom',
                    ],
                    'render_type' => 'none',
                ]
            );
    
            $widget->add_control(
                $prefix . 'to_transformOriginZ',
                [
                    'label' => __( 'Transform origin Z', 'hub-elementor-addons' ),
                    'type' => \Elementor\Controls_Manager::SLIDER,
                    'size_units' => [ 'px' ],
                    'range' => [
                        'px' => [
                                'min' => -500,
                                'max' => 500,
                                'step' => 1,
                            ],
                    ],
                    'default' => [
                        'size' => 0,
                    ],
                    'separator' => 'after',
                    'condition' => [
                        $prefix . 'to_popover' => 'yes',
                        $prefix . 'preset' => 'custom',
                    ],
                    'render_type' => 'none',
                ]
            );
        $widget->end_popover();
    }
    
    public static function extract_custom_values( $element, $prefix ){
        $final_from = $final_to = [];

        $from_x = $element->get_settings( $prefix . 'from_x' );
        $from_y = $element->get_settings( $prefix . 'from_y' );
        $from_z = $element->get_settings( $prefix . 'from_z' );

        $from_scaleX = $element->get_settings( $prefix . 'from_scaleX' );
        $from_scaleY = $element->get_settings( $prefix . 'from_scaleY' );

        $from_rotationX = $element->get_settings( $prefix . 'from_rotationX' );
        $from_rotationY = $element->get_settings( $prefix . 'from_rotationY' );
        $from_rotationZ = $element->get_settings( $prefix . 'from_rotationZ' );

        $from_transformOriginX = $element->get_settings( $prefix . 'from_transformOriginX' );
        $from_transformOriginY = $element->get_settings( $prefix . 'from_transformOriginY' );
        $from_transformOriginZ = $element->get_settings( $prefix . 'from_transformOriginZ' );

        $from_opacity = $element->get_settings( $prefix . 'from_opacity' );

        $from_toriginX = isset( $from_transformOriginX ) && ! empty( $from_transformOriginX ) ? $from_transformOriginX['size'].$from_transformOriginX['unit'] : '';
        $from_toriginY = isset( $from_transformOriginY ) && ! empty( $from_transformOriginY ) ? $from_transformOriginY['size'].$from_transformOriginY['unit'] : '';
        $from_toriginZ = isset( $from_transformOriginZ ) && ! empty( $from_transformOriginZ ) ? $from_transformOriginZ['size'].$from_transformOriginZ['unit'] : '';



        $to_x = $element->get_settings( $prefix . 'to_x' );
        $to_y = $element->get_settings( $prefix . 'to_y' );
        $to_z = $element->get_settings( $prefix . 'to_z' );

        $to_scaleX = $element->get_settings( $prefix . 'to_scaleX' );
        $to_scaleY = $element->get_settings( $prefix . 'to_scaleY' );

        $to_rotationX = $element->get_settings( $prefix . 'to_rotationX' );
        $to_rotationY = $element->get_settings( $prefix . 'to_rotationY' );
        $to_rotationZ = $element->get_settings( $prefix . 'to_rotationZ' );

        $to_transformOriginX = $element->get_settings( $prefix . 'to_transformOriginX' );
        $to_transformOriginY = $element->get_settings( $prefix . 'to_transformOriginY' );
        $to_transformOriginZ = $element->get_settings( $prefix . 'to_transformOriginZ' );

        $to_opacity = $element->get_settings( $prefix . 'to_opacity' );

        $to_toriginX = isset( $to_transformOriginX ) && ! empty( $to_transformOriginX ) ? $to_transformOriginX['size'].$to_transformOriginX['unit'] : '';
        $to_toriginY = isset( $to_transformOriginY ) && ! empty( $to_transformOriginY ) ? $to_transformOriginY['size'].$to_transformOriginY['unit'] : '';
        $to_toriginZ = isset( $to_transformOriginZ ) && ! empty( $to_transformOriginZ ) ? $to_transformOriginZ['size'].$to_transformOriginZ['unit'] : '';


        if ( !empty( $from_x ) && !empty( $to_x ) && $from_x != $to_x ) {
            $final_from['x'] = $from_x['size'].$from_x['unit'];
            $final_to['x'] = $to_x['size'].$to_x['unit'];
        }
        if ( !empty( $from_y ) && !empty( $to_y ) && $from_y != $to_y ) {
            $final_from['y'] = $from_y['size'].$from_y['unit'];
            $final_to['y'] = $to_y['size'].$to_y['unit'];
        }
        if ( !empty( $from_z ) && !empty( $to_z ) && $from_z != $to_z ) {
            $final_from['z'] = $from_z['size'].$from_z['unit'];
            $final_to['z'] = $to_z['size'].$to_z['unit'];
        }

        if ( !empty( $from_scaleX ) && !empty( $to_scaleX ) && $from_scaleX != $to_scaleX ) {
            $final_from['scaleX'] = (float) $from_scaleX['size'];
            $final_to['scaleX'] = (float) $to_scaleX['size'];
        }
        if ( !empty( $from_scaleY ) && !empty( $to_scaleY ) && $from_scaleY != $to_scaleY ) {
            $final_from['scaleY'] = (float) $from_scaleY['size'];
            $final_to['scaleY'] = (float) $to_scaleY['size'];
        }

        if ( !empty( $from_rotationX ) && !empty( $to_rotationX ) && $from_rotationX != $to_rotationX ) {
            $final_from['rotationX'] = (int) $from_rotationX['size'];
            $final_to['rotationX'] = (int) $to_rotationX['size'];
        }
        if ( !empty( $from_rotationY ) && !empty( $to_rotationY ) && $from_rotationY != $to_rotationY ) {
            $final_from['rotationY'] = (int) $from_rotationY['size'];
            $final_to['rotationY'] = (int) $to_rotationY['size'];
        }
        if ( !empty( $from_rotationZ ) && !empty( $to_rotationZ ) && $from_rotationZ != $to_rotationZ ) {
            $final_from['rotationZ'] = (int) $from_rotationZ['size'];
            $final_to['rotationZ'] = (int) $to_rotationZ['size'];
        }

        if ( !empty( $from_opacity ) && !empty( $to_opacity ) && $from_opacity != $to_opacity ) {
            $final_from['opacity'] = (float) $from_opacity['size'];
            $final_to['opacity'] = (float) $to_opacity['size'];
        }

        if (
            ! empty( $from_toriginX ) && ! empty( $from_toriginY ) && ! empty( $from_toriginZ ) &&
            ! empty( $to_toriginX ) && ! empty( $to_toriginY ) && ! empty( $to_toriginZ )
        ) {

            $final_from['transformOrigin'] = $from_toriginX . ' ' . $from_toriginY . ' ' . $from_toriginZ;
            $final_to['transformOrigin'] = $to_toriginX . ' ' . $to_toriginY . ' ' . $to_toriginZ;

            if ( $final_from['transformOrigin'] == $final_to['transformOrigin'] ) {
                unset($final_from['transformOrigin']);
                unset($final_to['transformOrigin']);
            }

        }

        return [
            'from' => $final_from, 
            'to' =>$final_to
        ];
    }
    public static function extract_animation_settings( $element, $prefix ){
        $options = [];
        $animation_ease = $element->get_settings( $prefix . 'settings_ease' );
        $animation_direction = $element->get_settings( $prefix . 'settings_direction' );
        $animation_duration = $element->get_settings( $prefix . 'settings_duration' )['size'];
        $animation_stagger = $element->get_settings( $prefix . 'settings_stagger' )['size'];
        $animation_start_delay = $element->get_settings( $prefix . 'settings_start_delay' )['size'];

        if ( !empty( $animation_duration ) && $animation_duration !== 1.6 ) {
            $options['duration'] = (float) ($animation_duration);
        }
        if( !empty( $animation_start_delay ) && $animation_start_delay !== 0 ) {
            $options['startDelay'] = (float) ($animation_start_delay);
        }
        if ( !empty( $animation_stagger ) && $animation_stagger !== 0.16 ) {
            $options['delay'] = (float) ($animation_stagger);
        }
        if ( $animation_ease !== 'power4.out' ) {
            $options['ease'] = $animation_ease;
        }
        if ( $animation_direction !== 'forward' ) {
            $options['direction'] = $animation_direction;
        }
        return $options;
    }
    public static function extract_preset_values($element, $prefix){
        $preset = $element->get_settings( $prefix . 'preset' );

        $defaults = array(

            'Fade In' => array(
                'from' => array( 'opacity' => 0 ),
                'to'   => array( 'opacity' => 1 ),
            ),
            'Fade In Down' => array(
                'from' => array( 'opacity' => 0, 'y' => -150 ),
                'to'   => array( 'opacity' => 1, 'y' => 0 ),
            ),
            'Fade In Up' => array(
                'from' => array( 'opacity' => 0, 'y' => 150 ),
                'to'   => array( 'opacity' => 1, 'y' => 0 ),
            ),
            'Fade In Left' => array(
                'from' => array( 'opacity' => 0, 'x' => -150 ),
                'to'   => array( 'opacity' => 1, 'x' => 0 ),
            ),
            'Fade In Right' => array(
                'from' => array( 'opacity' => 0, 'x' => 150 ),
                'to'   => array( 'opacity' => 1, 'x' => 0 ),
            ),
            'Flip In Y' => array(
                'from' => array( 'opacity' => 0, 'x' => 150, 'rotationY' => 30 ),
                'to'   => array( 'opacity' => 1, 'x' => 0, 'rotationY' => 0 ),
            ),
            'Flip In X' => array(
                'from' => array( 'opacity' => 0, 'y' => 150, 'rotationX' => -30 ),
                'to'   => array( 'opacity' => 1, 'y' => 0, 'rotationX' => 0 ),
            ),
            'Scale Up' => array(
                'from' => array( 'opacity' => 0, 'scale' => 0.75 ),
                'to'   => array( 'opacity' => 1, 'scale' => 1 ),
            ),
            'Scale Down' => array(
                'from' => array( 'opacity' => 0, 'scale' => 1.25 ),
                'to'   => array( 'opacity' => 1, 'scale' => 1 ),
            ),

        );
        return $defaults[ $preset ];
    }
}