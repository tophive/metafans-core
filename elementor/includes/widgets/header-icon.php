<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly

class TH_Header_Custom_Icon_Widget extends \Elementor\Widget_Base {
    public function get_name() {
        return 'th_header_custom_icon_widget';
    }

    public function get_title() {
        return __('Icon Widget', 'plugin-name');
    }

    public function get_icon() {
        return 'eicon-star-o';
    }

    public function get_categories() {
        return ['th-header'];
    }

    protected function register_controls() {
        $this->start_controls_section(
            'content_section',
            [
                'label' => __('Content', 'plugin-name'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'icon',
            [
                'label' => __('Choose Icon', 'plugin-name'),
                'type' => \Elementor\Controls_Manager::ICONS,
                'default' => [
                    'value' => 'fas fa-star',
                    'library' => 'solid',
                ],
            ]
        );

        $this->add_control(
            'text',
            [
                'label' => __('Text', 'plugin-name'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => __('Sample Text', 'plugin-name'),
            ]
        );

        $this->add_control(
            'position',
            [
                'label' => __('Text Position', 'plugin-name'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'options' => [
                    'left' => __('Left', 'plugin-name'),
                    'right' => __('Right', 'plugin-name'),
                    'top' => __('Top', 'plugin-name'),
                    'bottom' => __('Bottom', 'plugin-name'),
                ],
                'default' => 'right',
            ]
        );

        $this->add_control(
            'link',
            [
                'label' => __('Link', 'plugin-name'),
                'type' => \Elementor\Controls_Manager::URL,
                'placeholder' => __('https://your-link.com', 'plugin-name'),
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'count_style_section',
            [
                'label' => __('Icon', 'your-text-domain'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );
        $this->add_control('th_custom_icon_size', [
            'label' => __('Icon Size', 'your-text-domain'),
            'type' => \Elementor\Controls_Manager::SLIDER,
            'size_units' => ['px'],
            'range' => ['px' => ['min' => 10, 'max' => 100, 'step' => 1]],
            'default' => ['size' => 24],
            'selectors' => [
                '{{WRAPPER}} .th-header-icon-widget .icon i' => 'font-size: {{SIZE}}{{UNIT}};',
                '{{WRAPPER}} .th-header-icon-widget .icon svg' => 'width: {{SIZE}}{{UNIT}};'
            ],
        ]);

        $this->add_control('th_custom_icon_color', [
            'label' => __('Icon Color', 'your-text-domain'),
            'type' => \Elementor\Controls_Manager::COLOR,
            'default' => '#000000',
            'selectors' => [
                '{{WRAPPER}} .th-header-icon-widget .icon i' => 'color: {{VALUE}};',
                '{{WRAPPER}} .th-header-icon-widget .icon svg' => 'fill: {{VALUE}};'
            ],
        ]);
        $this->add_control('th_custom_icon_bg', [
            'label' => __('Icon Background', 'your-text-domain'),
            'type' => \Elementor\Controls_Manager::COLOR,
            'default' => '#FFFFFF00',
            'selectors' => [
                '{{WRAPPER}} .th-header-icon-widget .icon' => 'background-color: {{VALUE}};'
            ],
        ]);

        $this->add_responsive_control(
            'th_custom_icon_padding',
            [
                'label' => __('Spacing', 'your-text-domain'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors' => ['{{WRAPPER}} .th-header-icon-widget .icon' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'],
            ]
        );
        $this->add_responsive_control(
            'th_custom_icon_margin',
            [
                'label' => __('Margin', 'your-text-domain'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'default' => [
					'top' => 0,
					'right' => 10,
					'bottom' => 0,
					'left' => 0,
					'unit' => 'px',
					'isLinked' => false,
				],
                'selectors' => ['{{WRAPPER}} .th-header-icon-widget .icon' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'],
            ]
        );

        $this->add_control('th_custom_icon_br', [
            'label' => __('Border Radius', 'your-text-domain'),
            'type' => \Elementor\Controls_Manager::SLIDER,
            'size_units' => ['px'],
            'range' => ['px' => ['min' => 0, 'max' => 50, 'step' => 1]],
            'default' => ['size' => 24],
            'selectors' => ['{{WRAPPER}} .th-header-icon-widget .icon' => 'border-radius: {{SIZE}}{{UNIT}};'],
        ]);
        $this->end_controls_section();

        $this->start_controls_section(
            'icon_text_style_section',
            [
                'label' => __('Text', 'your-text-domain'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'icon_text_typography',
                'label' => __('Text Typography', 'your-text-domain'),
                'selector' => '{{WRAPPER}} .th-header-icon-widget .icon-text',
            ]
        );
        
        $this->add_control(
            'icon_text_color',
            [
                'label' => __('Text Color', 'your-text-domain'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => ['{{WRAPPER}} .th-header-icon-widget .icon-text' => 'color: {{VALUE}}'],
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        // $icon_html = \Elementor\Icons_Manager::render_icon($settings['icon'], ['aria-hidden' => 'true']);
        $text = $settings['text'];
        $position = $settings['position'];
        $link = $settings['link']['url'] ? esc_url($settings['link']['url']) : '';
        // if ($link) echo '<a href="' . $link . '" target="_blank">';
        ?>
            <div class="th-header-icon-widget text-<?php esc_attr_e($position, 'plugin-name'); ?>">
        <span class="icon">
            <?php \Elementor\Icons_Manager::render_icon( $settings['icon'], [ 'aria-hidden' => 'true' ] ); ?>
        </span>
        <span class="icon-text"><?php esc_html_e($text, 'plugin-name'); ?></span>            
        <?php
        echo '</div>';
        // if ($link) echo '</a>';
    }

    public function get_style_depends() {
        return ['custom-icon-widget-style'];
    }
}

\Elementor\Plugin::instance()->widgets_manager->register_widget_type(new TH_Header_Custom_Icon_Widget());
