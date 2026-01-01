<?php

trait Tophive_Carousel_Trait {

    public function __construct($data = [], $args = null) {
        parent::__construct($data, $args);

        wp_register_script(
            'tophive-carousel-js',
            TH_ELEMENTOR_URL . 'assets/js/carousel.js',
            ['elementor-common-modules', 'elementor-frontend'],
            '1.0.0',
            true
        );
    }

    public function get_script_depends(): array {
        return ['tophive-carousel-js'];
    }

    public function get_style_depends(): array {
        return ['e-swiper'];
    }

    protected function add_carousel_settings(){
        $this->start_controls_section('carousel_section', [
            'label' => esc_html__('Carousel Settings', 'plugin-name'),
            'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
        ]);
        
        $this->add_control('enable_carousel', [
            'label' => esc_html__('Enable Carousel', 'plugin-name'),
            'type' => \Elementor\Controls_Manager::SWITCHER,
            'return_value' => 'yes',
            'default' => '',
        ]);
        
        $this->add_responsive_control('slides_per_view', [
            'label' => esc_html__('Slides Per View', 'plugin-name'),
            'type' => \Elementor\Controls_Manager::SLIDER,
            'size_units' => ['px'],
            'range' => ['px' => ['min' => 1, 'max' => 6]],
            'default' => ['size' => 3],
            'condition' => ['enable_carousel' => 'yes'],
        ]);
        
        $this->add_control('space_between', [
            'label' => esc_html__('Space Between', 'plugin-name'),
            'type' => \Elementor\Controls_Manager::SLIDER,
            'range' => ['px' => ['min' => 0, 'max' => 100]],
            'default' => ['size' => 20],
            'condition' => ['enable_carousel' => 'yes'],
        ]);
        
        $this->add_control('loop', [
            'label' => esc_html__('Loop', 'plugin-name'),
            'type' => \Elementor\Controls_Manager::SWITCHER,
            'default' => '',
            'condition' => ['enable_carousel' => 'yes'],
        ]);
        
        $this->add_control('autoplay', [
            'label' => esc_html__('Autoplay', 'plugin-name'),
            'type' => \Elementor\Controls_Manager::SWITCHER,
            'default' => '',
            'condition' => ['enable_carousel' => 'yes'],
        ]);
        
        $this->add_control('autoplay_delay', [
            'label' => esc_html__('Autoplay Delay (ms)', 'plugin-name'),
            'type' => \Elementor\Controls_Manager::NUMBER,
            'default' => 5000,
            'condition' => [
                'enable_carousel' => 'yes',
                'autoplay' => 'yes',
            ],
        ]);
        
        $this->end_controls_section();        
    }

    protected function start_carousel_wrapper(array $settings): void {
        if (!empty($settings['enable_carousel']) && $settings['enable_carousel'] === 'yes') {
            $carousel_settings = \Tophive_Elementor_UI_Helper::get_carousel_settings($settings);

            echo '<div class="tophive-card-carousel swiper show-arrows-' . esc_attr($settings['show_arrows']) . ' show-pagination-' . esc_attr($settings['show_pagination']) . '" data-tophive-carousel-settings=\'' . esc_attr(json_encode($carousel_settings)) . '\'>';
            echo '<div class="swiper-wrapper">';
        } else {
            echo '<div class="tophive-card-grid">';
        }
    }

    protected function end_carousel_wrapper(array $settings): void {
        if (!empty($settings['enable_carousel']) && $settings['enable_carousel'] === 'yes') {
            echo '</div>'; // .swiper-wrapper

            echo '</div>'; // .swiper
            if (!empty($settings['show_pagination']) && $settings['show_pagination'] === 'yes') {
                echo '<div class="swiper-pagination"></div>';
            }

            if (!empty($settings['show_arrows']) && $settings['show_arrows'] === 'yes') {
                echo '<div class="swiper-button-prev"></div>';
                echo '<div class="swiper-button-next"></div>';
            }
        } else {
            echo '</div>'; // .tophive-card-grid
        }
    }

    protected function add_carousel_style_controls(){
        $this->start_controls_section('swiper_nav_style', [
            'label' => esc_html__('Carousel Navigation', 'plugin-name'),
            'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            'condition' => [
                'enable_carousel' => 'yes',
            ],
        ]);
        
        /**
         * Arrows
         */
        $this->add_control('nav_heading', [
            'label' => esc_html__('Arrows', 'plugin-name'),
            'type' => \Elementor\Controls_Manager::HEADING,
            'separator' => 'before',
        ]);
        $this->add_control('show_arrows', [
            'label' => esc_html__('Show Arrows', 'plugin-name'),
            'type' => \Elementor\Controls_Manager::SWITCHER,
            'return_value' => 'yes',
            'default' => 'yes',
            'condition' => ['enable_carousel' => 'yes'],
        ]);
        
        $this->add_responsive_control('nav_arrow_size', [
            'label' => esc_html__('Arrow Size', 'plugin-name'),
            'type' => \Elementor\Controls_Manager::SLIDER,
            'range' => ['px' => ['min' => 10, 'max' => 100]],
            'selectors' => [
                '{{WRAPPER}} .swiper-button-next::after, {{WRAPPER}} .swiper-button-prev::after' => 'font-size: {{SIZE}}{{UNIT}};',
            ],
            'condition' => ['show_arrows' => 'yes'],
        ]);
        
        // Arrow Width
        $this->add_responsive_control('arrow_width', [
            'label' => esc_html__('Arrow Width', 'plugin-name'),
            'type' => \Elementor\Controls_Manager::SLIDER,
            'range' => ['px' => ['min' => 10, 'max' => 200]],
            'selectors' => [
                '{{WRAPPER}} .swiper-button-prev, {{WRAPPER}} .swiper-button-next' => 'width: {{SIZE}}{{UNIT}};',
            ],
            'condition' => ['show_arrows' => 'yes'],
        ]);

        // Arrow Height
        $this->add_responsive_control('arrow_height', [
            'label' => esc_html__('Arrow Height', 'plugin-name'),
            'type' => \Elementor\Controls_Manager::SLIDER,
            'range' => ['px' => ['min' => 10, 'max' => 200]],
            'selectors' => [
                '{{WRAPPER}} .swiper-button-prev, {{WRAPPER}} .swiper-button-next' => 'height: {{SIZE}}{{UNIT}};',
            ],
            'condition' => ['show_arrows' => 'yes'],
        ]);

        // Disabled Opacity
        $this->add_control('arrow_disabled_opacity', [
            'label' => esc_html__('Disabled Arrow Opacity', 'plugin-name'),
            'type' => \Elementor\Controls_Manager::SLIDER,
            'size_units' => ['%'],
            'range' => ['%' => ['min' => 0, 'max' => 100, 'step' => 1]],
            'default' => ['size' => 0.4],
            'selectors' => [
                '{{WRAPPER}} .swiper-button-disabled' => 'opacity: {{SIZE}}%;',
            ],
            'condition' => ['show_arrows' => 'yes'],
        ]);

        // Start Tabs: Normal / Hover
        $this->start_controls_tabs('arrow_style_tabs');

        // NORMAL
        $this->start_controls_tab('arrow_style_normal', ['label' => esc_html__('Normal', 'plugin-name')]);

        $this->add_control('arrow_color', [
            'label' => esc_html__('Icon Color', 'plugin-name'),
            'type' => \Elementor\Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .swiper-button-prev, {{WRAPPER}} .swiper-button-next' => 'color: {{VALUE}};',
            ],
        ]);

        $this->add_control('arrow_bg_color', [
            'label' => esc_html__('Background Color', 'plugin-name'),
            'type' => \Elementor\Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .swiper-button-prev, {{WRAPPER}} .swiper-button-next' => 'background-color: {{VALUE}};',
            ],
        ]);

        $this->add_group_control(
            \Elementor\Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'arrow_shadow',
                'selector' => '{{WRAPPER}} .swiper-button-prev, {{WRAPPER}} .swiper-button-next',
            ]
        );

        $this->end_controls_tab();

        // HOVER
        $this->start_controls_tab('arrow_style_hover', ['label' => esc_html__('Hover', 'plugin-name')]);

        $this->add_control('arrow_color_hover', [
            'label' => esc_html__('Icon Color (Hover)', 'plugin-name'),
            'type' => \Elementor\Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .swiper-button-prev:hover, {{WRAPPER}} .swiper-button-next:hover' => 'color: {{VALUE}};',
            ],
        ]);

        $this->add_control('arrow_bg_color_hover', [
            'label' => esc_html__('Background Color (Hover)', 'plugin-name'),
            'type' => \Elementor\Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .swiper-button-prev:hover, {{WRAPPER}} .swiper-button-next:hover' => 'background-color: {{VALUE}};',
            ],
        ]);

        $this->add_group_control(
            \Elementor\Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'arrow_shadow_hover',
                'selector' => '{{WRAPPER}} .swiper-button-prev:hover, {{WRAPPER}} .swiper-button-next:hover',
            ]
        );

        $this->end_controls_tab();
        $this->end_controls_tabs();
        
        $this->add_responsive_control('nav_arrow_radius', [
            'label' => esc_html__('Border Radius', 'plugin-name'),
            'type' => \Elementor\Controls_Manager::DIMENSIONS,
            'selectors' => [
                '{{WRAPPER}} .swiper-button-next, {{WRAPPER}} .swiper-button-prev' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
            'condition' => ['show_arrows' => 'yes'],
        ]);

        $this->add_group_control(\Elementor\Group_Control_Border::get_type(), [
            'name' => 'arrow_border',
            'label' => esc_html__('Border', 'plugin-name'),
            'selector' => '{{WRAPPER}} .swiper-button-prev, {{WRAPPER}} .swiper-button-next',
            'condition' => ['show_arrows' => 'yes'],
        ]);
        $this->add_responsive_control('arrow_padding', [
            'label' => esc_html__('Arrow Padding', 'plugin-name'),
            'type' => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => ['px', '%', 'em'],
            'selectors' => [
                '{{WRAPPER}} .swiper-button-next, {{WRAPPER}} .swiper-button-prev' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
            'condition' => ['show_arrows' => 'yes'],
        ]);        

        // ARROW POSITIONS
        $this->add_control('arrow_position_tabs_heading', [
            'label' => esc_html__('Arrow Position', 'plugin-name'),
            'type' => \Elementor\Controls_Manager::HEADING,
            'separator' => 'before',
        ]);
        
        $this->start_controls_tabs('arrow_position_tabs');
        
        /**
         * 🔹 LEFT BUTTON
         */
        $this->start_controls_tab('arrow_left_tab', [
            'label' => esc_html__('Left Button', 'plugin-name'),
        ]);
        
        $this->add_responsive_control('arrow_left_translate_x', [
            'label' => esc_html__('Translate X', 'plugin-name'),
            'type' => \Elementor\Controls_Manager::SLIDER,
            'range' => ['%' => ['min' => -500, 'max' => 500]],
            'size_units' => ['px', '%', 'em', 'rem'],
            'default' => ['size' => 0],
            'selectors' => [
                '{{WRAPPER}} .swiper-button-prev' => 'transform: translateX({{SIZE}}{{UNIT}});',
            ],
            'condition' => ['show_arrows' => 'yes'],
        ]);
        
        $this->add_responsive_control('arrow_left_translate_y', [
            'label' => esc_html__('Translate Y', 'plugin-name'),
            'type' => \Elementor\Controls_Manager::SLIDER,
            'range' => ['%' => ['min' => -500, 'max' => 500]],
            'size_units' => ['px', '%', 'em', 'rem'],
            'default' => ['size' => 0],
            'selectors' => [
                '{{WRAPPER}} .swiper-button-prev' => 'margin-top: {{SIZE}}{{UNIT}};',
            ],
            'condition' => ['show_arrows' => 'yes'],
        ]);
        
        $this->end_controls_tab();
        
        /**
         * 🔸 RIGHT BUTTON
         */
        $this->start_controls_tab('arrow_right_tab', [
            'label' => esc_html__('Right Button', 'plugin-name'),
        ]);
        
        $this->add_responsive_control('arrow_right_translate_x', [
            'label' => esc_html__('Translate X', 'plugin-name'),
            'type' => \Elementor\Controls_Manager::SLIDER,
            'range' => ['px' => ['min' => -500, 'max' => 500]],
            'default' => ['size' => 0],
            'selectors' => [
                '{{WRAPPER}} .swiper-button-next' => 'transform: translateX({{SIZE}}{{UNIT}});',
            ],
            'condition' => ['show_arrows' => 'yes'],
        ]);
        
        $this->add_responsive_control('arrow_right_translate_y', [
            'label' => esc_html__('Translate Y', 'plugin-name'),
            'type' => \Elementor\Controls_Manager::SLIDER,
            'range' => ['px' => ['min' => -500, 'max' => 500]],
            'default' => ['size' => 0],
            'selectors' => [
                '{{WRAPPER}} .swiper-button-next' => 'margin-top: {{SIZE}}{{UNIT}};',
            ],
            'condition' => ['show_arrows' => 'yes'],
        ]);
        
        $this->end_controls_tab();
        
        $this->end_controls_tabs();
        
        /**
         * Pagination
         */
        $this->add_control('pagination_heading', [
            'label' => esc_html__('Pagination Bullets', 'plugin-name'),
            'type' => \Elementor\Controls_Manager::HEADING,
            'separator' => 'before',
        ]);
        $this->add_control('show_pagination', [
            'label' => esc_html__('Show Pagination', 'plugin-name'),
            'type' => \Elementor\Controls_Manager::SWITCHER,
            'return_value' => 'yes',
            'default' => 'yes',
        ]);
        
        $this->add_responsive_control('pagination_bullet_size', [
            'label' => esc_html__('Border radius', 'plugin-name'),
            'type' => \Elementor\Controls_Manager::SLIDER,
            'range' => ['px' => ['min' => 0, 'max' => 50]],
            'selectors' => [
                '{{WRAPPER}} .swiper-pagination-bullet' => 'border-radius: {{SIZE}}{{UNIT}};',
            ],
            'condition' => ['show_pagination' => 'yes'],
        ]);
        
        $this->add_control('pagination_bullet_color', [
            'label' => esc_html__('Bullet Color', 'plugin-name'),
            'type' => \Elementor\Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .swiper-pagination-bullet' => 'background-color: {{VALUE}};',
            ],
            'condition' => ['show_pagination' => 'yes'],
        ]);
        
        $this->add_control('pagination_bullet_active_color', [
            'label' => esc_html__('Active Bullet Color', 'plugin-name'),
            'type' => \Elementor\Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .swiper-pagination-bullet-active' => 'background-color: {{VALUE}};',
            ],
            'condition' => ['show_pagination' => 'yes'],
        ]);

        $this->add_control('pagination_position_heading', [
            'label' => esc_html__('Pagination Position', 'plugin-name'),
            'type' => \Elementor\Controls_Manager::HEADING,
            'separator' => 'before',
            'condition' => ['show_pagination' => 'yes'],
        ]);
        
        // Bullet Width
        $this->add_responsive_control('pagination_bullet_width', [
            'label' => esc_html__('Bullet Width', 'plugin-name'),
            'type' => \Elementor\Controls_Manager::SLIDER,
            'range' => ['px' => ['min' => 5, 'max' => 100]],
            'selectors' => [
                '{{WRAPPER}} .swiper-pagination-bullet' => 'width: {{SIZE}}{{UNIT}};',
            ],
            'condition' => ['show_pagination' => 'yes'],
        ]);
        
        // Bullet Height
        $this->add_responsive_control('pagination_bullet_height', [
            'label' => esc_html__('Bullet Height', 'plugin-name'),
            'type' => \Elementor\Controls_Manager::SLIDER,
            'range' => ['px' => ['min' => 5, 'max' => 100]],
            'selectors' => [
                '{{WRAPPER}} .swiper-pagination-bullet' => 'height: {{SIZE}}{{UNIT}};',
            ],
            'condition' => ['show_pagination' => 'yes'],
        ]);
        
        // TranslateX
        $this->add_responsive_control('pagination_translate_x', [
            'label' => esc_html__('Translate X', 'plugin-name'),
            'type' => \Elementor\Controls_Manager::SLIDER,
            'range' => ['px' => ['min' => -500, 'max' => 500]],
            'selectors' => [
                '{{WRAPPER}} .swiper-pagination' => 'transform: translateX({{SIZE}}{{UNIT}}) translateY(0);',
            ],
            'condition' => ['show_pagination' => 'yes'],
        ]);
        
        // TranslateY
        $this->add_responsive_control('pagination_translate_y', [
            'label' => esc_html__('Translate Y', 'plugin-name'),
            'type' => \Elementor\Controls_Manager::SLIDER,
            'range' => ['px' => ['min' => -500, 'max' => 500]],
            'selectors' => [
                '{{WRAPPER}} .swiper-pagination' => 'transform: translateX(0) translateY({{SIZE}}{{UNIT}});',
            ],
            'condition' => ['show_pagination' => 'yes'],
        ]);
        
        
        $this->end_controls_section();
        
    }
}
