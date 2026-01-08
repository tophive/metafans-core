<?php

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Horizontal_Text_Scroller_Widget extends Widget_Base {

    public function get_name() {
        return 'horizontal_text_scroller';
    }

    public function get_title() {
        return __( 'Horizontal Text Scroller', 'text-domain' );
    }

    public function get_icon() {
        return 'eicon-slider-push';
    }

    public function get_categories() {
        return [ 'th-general' ];
    }

    public function get_script_depends() {
        return ['tophive-elementor-bundle'];
    }

    protected function register_controls() {
        $this->start_controls_section(
            'content_section',
            [
                'label' => __( 'Content', 'text-domain' ),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        // Text Content
        $this->add_control(
            'scroll_text',
            [
                'label' => __( 'Scrolling Text', 'text-domain' ),
                'type' => Controls_Manager::TEXT,
                'default' => __( 'Your customizable scrolling text goes here.', 'text-domain' ),
                'placeholder' => __( 'Enter text', 'text-domain' ),
            ]
        );

        // Background Color
        $this->add_control(
            'background_color',
            [
                'label' => __( 'Background Color', 'text-domain' ),
                'type' => Controls_Manager::COLOR,
                'default' => '#1c1c1c',
                'selectors' => [
                    '{{WRAPPER}} .customizable-scroller' => 'background-color: {{VALUE}}',
                ],
            ]
        );

        // Text Color
        $this->add_control(
            'text_color',
            [
                'label' => __( 'Text Color', 'text-domain' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
					'{{WRAPPER}} .customizable-scroller p' => 'color: {{VALUE}}',
				],
                ]
            );
            
        // Animation Speed
        $this->add_control(
            'animation_speed',
            [
                'label' => __( 'Animation Speed (seconds)', 'plugin-name' ),
                'type' => Controls_Manager::NUMBER,
                'default' => 20,
                'description' => __( 'Set the animation speed in seconds', 'plugin-name' ),
            ]
        );
        
           
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'typography',
                'label' => __( 'Typography', 'plugin-name' ),
                'selector' => '{{WRAPPER}} .scrolling-text',
            ]
        );

        // Height Control
        $this->add_control(
            'height',
            [
                'label' => __( 'Height', 'text-domain' ),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 50,
                        'max' => 300,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .customizable-scroller' => 'height: {{SIZE}}{{UNIT}}'
                ],
                'default' => [
                    'size' => 80,
                    'unit' => 'px',
                ],
            ]
        );

        // Rotation Control
        $this->add_control(
            'rotate',
            [
                'label' => __( 'Rotation (degrees)', 'text-domain' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'deg' ],
                'range' => [
                    'deg' => [
                        'min' => -45,
                        'max' => 45,
                        'step' => 1,
                    ],
                ],
                'default' => [
                    'unit' => 'deg',
                    'size' => 0,
                ],
            ]
        );

        // Scale Control
        $this->add_control(
            'scale',
            [
                'label' => __( 'Scale', 'text-domain' ),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0.5,
                        'max' => 3,
                        'step' => 0.1,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 1,
                ],
            ]
        );

        // Position X Control
        $this->add_control(
            'position_x',
            [
                'label' => __( 'Position X', 'text-domain' ),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => -500,
                        'max' => 500,
                    ],
                ],
                'default' => [
                    'size' => -200,
                    'unit' => 'px',
                ],
            ]
        );

        // Position Y Control
        $this->add_control(
            'position_y',
            [
                'label' => __( 'Position Y', 'text-domain' ),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => -500,
                        'max' => 500,
                    ],
                ],
                'default' => [
                    'size' => 0,
                    'unit' => 'px',
                ],
            ]
        );

        // Scroll Direction Control
        $this->add_control(
            'scroll_direction',
            [
                'label' => __( 'Scroll Direction', 'text-domain' ),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'left' => __( 'Left to Right', 'text-domain' ),
                    'right' => __( 'Right to Left', 'text-domain' ),
                ],
                'default' => 'right',
            ]
        );
        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        $speed = !empty($settings['animation_speed']) ? $settings['animation_speed'] . 's' : '20s';
        $direction = $settings['scroll_direction'] === 'left' ? 'scroll-right' : 'scroll-left';
        ?>
        <div class="customizable-scroller" data-animation-speed="<?php echo esc_attr($speed); ?>"
            style="transform: rotate(<?php echo esc_attr( $settings['rotate']['size'] ); ?>deg) scale(<?php echo esc_attr( $settings['scale']['size'] ); ?>) translate(<?php echo esc_attr( $settings['position_x']['size'] ); ?>px, <?php echo esc_attr( $settings['position_y']['size'] ); ?>px);">
            <div class="scrolling-text <?php echo esc_attr($direction); ?>">
                <p><?php echo esc_html( $settings['scroll_text'] ); ?> &#x2022; <?php echo esc_html( $settings['scroll_text'] ); ?> &#x2022; <?php echo esc_html( $settings['scroll_text'] ); ?> &#x2022; <?php echo esc_html( $settings['scroll_text'] ); ?></p>
            </div>
        </div>
        <?php
    }
          

    public function _content_template() {}
}

\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new \Horizontal_Text_Scroller_Widget() );
