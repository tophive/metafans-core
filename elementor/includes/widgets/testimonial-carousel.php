<?php
/**
 * Plugin Name: Testimonial Carousel (Elementor Widget)
 * Description: GSAP-powered testimonial carousel for Elementor with timeline animations, full control over card, text, author image, autoplay, navigation, dots and nav button styles.
 * Author: Tophive
 * version: 1.0
 * Text Domain: testimonial-carousel
 */

namespace My_Core_Plugin\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Background;
use Elementor\Utils;

if (!defined('ABSPATH')) exit;

class Testimonial_Carousel extends Widget_Base {

    public function get_name() { return 'testimonial_carousel'; }
    public function get_title() { return __('Testimonial Carousel', 'my-core-plugin'); }
    public function get_icon() { return 'eicon-review'; }
    public function get_categories() { return ['th-general']; }
    
    public function get_style_depends() {
        return [ 'tophive-elements-css' ];
    }
    
    public function get_script_depends() {
        return [ 'tophive-elementor-bundle' ];
    }
    
    // Register Controls
    protected function register_controls() {
        $this->register_settings_controls();
        $this->register_content_controls();
        $this->register_quote_icon_controls();
        $this->register_card_style_controls();
        $this->register_text_style_controls();
        $this->register_image_style_controls();
        $this->register_nav_style_controls();
    }

    /**
     * Register Content Controls
     */
    private function register_content_controls() {
        $this->start_controls_section('testimonial_section', [
            'label' => __('Testimonials', 'my-core-plugin'),
            'tab' => Controls_Manager::TAB_CONTENT,
        ]);

        // Repeater for testimonials
        $repeater = new \Elementor\Repeater();
        $repeater->add_control('testimonial_text', [
            'label' => __('Testimonial Text', 'my-core-plugin'),
            'type' => Controls_Manager::TEXTAREA,
            'default' => 'This is a testimonial text.'
        ]);
        $repeater->add_control('author_name', [
            'label' => __('Author Name', 'my-core-plugin'),
            'type' => Controls_Manager::TEXT,
            'default' => 'Author Name'
        ]);
        $repeater->add_control('author_role', [
            'label' => __('Author Role', 'my-core-plugin'),
            'type' => Controls_Manager::TEXT,
            'default' => 'Designer'
        ]);
        $repeater->add_control('author_image', [
            'label' => __('Author Image', 'my-core-plugin'),
            'type' => Controls_Manager::MEDIA,
            'default' => ['url' => Utils::get_placeholder_image_src()]
        ]);

        $this->add_control('testimonials', [
            'label' => __('Testimonial Items', 'my-core-plugin'),
            'type' => Controls_Manager::REPEATER,
            'fields' => $repeater->get_controls(),
            'default' => [
                ['testimonial_text' => 'This is an amazing product!', 'author_name' => 'John Doe', 'author_role' => 'Designer'],
                ['testimonial_text' => 'I love how this has helped my workflow.', 'author_name' => 'Jane Smith', 'author_role' => 'Developer'],
            ],
            'title_field' => '{{{ author_name }}} - {{{ author_role }}}',
        ]);
        $this->end_controls_section();
    }

    /**
     * Register Settings Controls
     */
    private function register_settings_controls() {
        $this->start_controls_section('settings_section', [
            'label' => __('Settings', 'my-core-plugin'),
            'tab' => Controls_Manager::TAB_CONTENT,
        ]);
        $this->add_responsive_control('slides_per_view', [
            'label' => __('Slides per View', 'my-core-plugin'),
            'type' => Controls_Manager::NUMBER,
            'default' => 4,
            'min' => 1,
            'max' => 10,
            'tablet_default' => 2,
            'mobile_default' => 1,
            'selectors' => [
                '{{WRAPPER}} .testimonial-item' => 'flex: 0 0 calc((100% / {{SIZE}}) - (var(--gap, 20px) * (1 - (1 / {{SIZE}}))));',
            ],
            'render_type' => 'template',
        ]);
        $this->add_responsive_control('item_gap', [
            'label' => __('Item Gap', 'my-core-plugin'),
            'type' => Controls_Manager::SLIDER,
            'size_units' => ['px'],
            'range' => [
                'px' => [
                    'min' => 0,
                    'max' => 100,
                ],
            ],
            'default' => [
                'unit' => 'px',
                'size' => 20,
            ],
            'selectors' => [
                '{{WRAPPER}} .testimonial-carousel' => 'gap: {{SIZE}}{{UNIT}};',
                '{{WRAPPER}} .testimonial-item' => '--gap: {{SIZE}}{{UNIT}};'
            ],
        ]);
        $this->add_control('autoplay', [
            'label' => __('Autoplay', 'my-core-plugin'),
            'type' => Controls_Manager::SWITCHER,
            'default' => 'yes'
        ]);
        $this->add_control('autoplay_speed', [
            'label' => __('Autoplay Speed (ms)', 'my-core-plugin'),
            'type' => Controls_Manager::NUMBER,
            'default' => 5000,
            'condition' => ['autoplay' => 'yes']
        ]);
        $this->add_control('pause_on_hover', [
            'label' => __('Pause on Hover', 'my-core-plugin'),
            'type' => Controls_Manager::SWITCHER,
            'default' => 'yes',
            'condition' => ['autoplay' => 'yes']
        ]);
        $this->add_control('pause_on_hover', [
            'label' => __('Pause on Hover', 'my-core-plugin'),
            'type' => Controls_Manager::SWITCHER,
            'default' => 'yes',
            'condition' => ['autoplay' => 'yes']
        ]);
        $this->add_control('loop', [
            'label' => __('Loop', 'my-core-plugin'),
            'type' => Controls_Manager::SWITCHER,
            'default' => 'yes'
        ]);
        $this->add_control('navigation', [
            'label' => __('Navigation', 'my-core-plugin'),
            'type' => Controls_Manager::SELECT,
            'options' => ['arrows' => __('Arrows', 'my-core-plugin'), 'dots' => __('Dots', 'my-core-plugin'), 'none' => __('None', 'my-core-plugin')],
            'default' => 'arrows',
        ]);
        $this->add_control('start_position', [
            'label' => __('Start Position', 'my-core-plugin'),
            'type' => Controls_Manager::CHOOSE,
            'options' => [
                'start' => [
                    'title' => __('Start', 'my-core-plugin'),
                    'icon' => 'eicon-h-align-left',
                ],
                'center' => [
                    'title' => __('Center', 'my-core-plugin'),
                    'icon' => 'eicon-h-align-center',
                ],
            ],
            'default' => 'center',
        ]);
        $this->end_controls_section();
    }

    /**
     * Register Card Style Controls
     */
    private function register_card_style_controls() {
        $this->start_controls_section('style_section', [
            'label' => __('Card', 'my-core-plugin'),
            'tab' => Controls_Manager::TAB_STYLE,
        ]);
        $this->add_control('card_background_type', [
            'label' => __('Background Type', 'my-core-plugin'),
            'type' => Controls_Manager::SELECT,
            'options' => ['solid'=>'Solid','gradient'=>'Gradient','glass'=>'Glass'],
            'default' => 'solid'
        ]);
        $this->add_control('card_solid_color', [
            'label' => __('Solid Color', 'my-core-plugin'),
            'type' => Controls_Manager::COLOR,
            'default' => '#292929',
            'selectors' => ['{{WRAPPER}} .testimonial-item' => 'background-color: {{VALUE}};'],
            'condition' => ['card_background_type' => 'solid']
        ]);
        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'card_gradient',
                'label' => __('Gradient', 'my-core-plugin'),
                'types' => ['gradient'],
                'condition' => ['card_background_type' => 'gradient'],
                'selector' => '{{WRAPPER}} .testimonial-item'
            ]
        );
        $this->add_responsive_control('card_glass_blur', [
            'label' => __('Glass Blur', 'my-core-plugin'),
            'type' => Controls_Manager::SLIDER,
            'range' => ['px'=>['min'=>1,'max'=>50]],
            'default' => ['unit'=>'px','size'=>10],
            'condition' => ['card_background_type'=>'glass']
        ]);
        $this->add_responsive_control('card_padding', [
            'label' => __('Padding', 'my-core-plugin'),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => ['px','em','%'],
            'selectors' => ['{{WRAPPER}} .testimonial-item'=>'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'],
        ]);
        $this->add_group_control(Group_Control_Border::get_type(), [
            'name'=>'card_border',
            'label'=>__('Border','my-core-plugin'),
            'selector'=>'{{WRAPPER}} .testimonial-item'
        ]);
        $this->add_responsive_control('card_radius', [
            'label'=>__('Border Radius','my-core-plugin'),
            'type'=> Controls_Manager::DIMENSIONS,
            'size_units'=>['px','%','em'],
            'selectors'=>['{{WRAPPER}} .testimonial-item'=>'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'],
        ]);
        $this->add_control('show_pseudo_element', [
            'label' => __('Active Border Effect', 'my-core-plugin'),
            'type' => Controls_Manager::SWITCHER,
            'label_on' => __('Show', 'my-core-plugin'),
            'label_off' => __('Hide', 'my-core-plugin'),
            'return_value' => 'yes',
            'default' => 'yes',
            'separator' => 'before',
            'selectors' => [
                '{{WRAPPER}} .testimonial-item.active[data-slider="slide"]::after' => 'opacity: 1;',
            ],
        ]);
        $this->add_control('card_pseudo_color', [
            'label' => __('Pseudo Color', 'my-core-plugin'),
            'type' => Controls_Manager::COLOR,
            'default' => '#ff9b79',
            'selectors' => [
                '{{WRAPPER}} .testimonial-item[data-slider="slide"]::after' => '--pseudo-color: {{VALUE}};',
            ],
            'condition' => [
                'show_pseudo_element' => 'yes',
            ],
        ]);
        $this->end_controls_section();
    }

    /**
     * Register Text Style Controls
     */
    private function register_text_style_controls() {
        $this->start_controls_section('text_style_section', ['label' => __('Text Style', 'my-core-plugin'), 'tab' => Controls_Manager::TAB_STYLE]);

        $this->add_typography_controls('text', 'Testimonial Text', '.testimonial-text', '#ffffff', 'left');
        $this->add_typography_controls('author_name', 'Author Name', '.author-name', '#ffffff', 'left', true);
        $this->add_typography_controls('author_role', 'Author Role', '.author-role', '#b6b6b6', 'left', true);

        $this->end_controls_section();
    }

    /**
     * Helper method to add typography and alignment controls.
     */
    private function add_typography_controls($prefix, $label, $selector, $default_color, $default_align, $is_block = false) {
        $this->add_group_control(Group_Control_Typography::get_type(), [
            'name' => 'typography_' . $prefix,
            'label' => __($label . ' Typography', 'my-core-plugin'),
            'selector' => '{{WRAPPER}} ' . $selector,
            'separator' => 'before',
        ]);

        $this->add_control($prefix . '_color', [
            'label' => __($label . ' Color', 'my-core-plugin'),
            'type' => Controls_Manager::COLOR,
            'default' => $default_color,
            'selectors' => ['{{WRAPPER}} ' . $selector => 'color: {{VALUE}};'],
        ]);

        $this->add_alignment_controls($prefix, $label, $selector, $default_align, $is_block);
    }

    /**
     * Helper method to add alignment controls.
     */
    private function add_alignment_controls($prefix, $label, $selector, $default_align, $is_block = false) {
        $css_selector = '{{WRAPPER}} ' . $selector;
        $css_rules = 'text-align: {{VALUE}};';
        if ($is_block) {
            $css_rules .= ' display: block;';
        }

        $this->add_responsive_control($prefix . '_alignment', [
            'label' => __($label . ' Alignment', 'my-core-plugin'),
            'type' => Controls_Manager::CHOOSE,
            'options' => [
                'left' => ['title' => 'Left', 'icon' => 'eicon-text-align-left'],
                'center' => ['title' => 'Center', 'icon' => 'eicon-text-align-center'],
                'right' => ['title' => 'Right', 'icon' => 'eicon-text-align-right'],
            ],
            'default' => $default_align,
            'selectors' => [$css_selector => $css_rules],
        ]);
    }

    /**
     * Register Author Image Style Controls
     */
    private function register_image_style_controls() {
        $this->start_controls_section('author_image_style', [
            'label' => __('Author Image', 'my-core-plugin'),
            'tab' => Controls_Manager::TAB_STYLE,
        ]);
        $this->add_responsive_control('author_image_size', [
            'label' => __('Image Size', 'my-core-plugin'),
            'type' => Controls_Manager::SLIDER,
            'default' => [
                'unit' => 'px',
                'size' => 50,
            ],
            'range' => ['px'=>['min'=>20,'max'=>200]],
            'selectors'=>['{{WRAPPER}} .author-image'=>'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};']
        ]);
        $this->add_responsive_control('author_image_radius', [
            'label'=>'Border Radius',
            'type'=>Controls_Manager::SLIDER,
            'default' => ['unit' => '%', 'size' => 50],
            'range'=>['px'=>['min'=>0,'max'=>100],'%'=>['min'=>0,'max'=>50]],
            'selectors'=>['{{WRAPPER}} .author-image'=>'border-radius: {{SIZE}}{{UNIT}};']
        ]);
        $this->add_group_control(Group_Control_Border::get_type(), [
            'name'=>'author_image_border',
            'label'=>'Border',
            'selector'=>'{{WRAPPER}} .author-image'
        ]);
        $this->add_control('author_image_alignment', [
            'label'=>'Alignment',
            'type'=>Controls_Manager::CHOOSE,
            'options'=>[
                'flex-start'=>['title'=>'Left','icon'=>'eicon-h-align-left'],
                'center'=>['title'=>'Center','icon'=>'eicon-h-align-center'],
                'flex-end'=>['title'=>'Right','icon'=>'eicon-h-align-right'],
            ],
            'default'=>'flex-start',
            'selectors'=>['{{WRAPPER}} .testimonial-author'=>'justify-content: {{VALUE}};']
        ]);
        $this->end_controls_section();
    }

    /**
     * Register Navigation Style Controls
     */
    private function register_nav_style_controls() {
        $this->start_controls_section('dot_style_section', [
            'label' => __('Dots', 'my-core-plugin'),
            'tab' => Controls_Manager::TAB_STYLE,
            'condition' => ['navigation' => 'dots'],
        ]);
        $this->add_responsive_control('dot_size', [
            'label' => __('Dot Size', 'my-core-plugin'),
            'type' => Controls_Manager::SLIDER,
            'selectors' => ['{{WRAPPER}} .carousel-dots .dot' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};'],
            'range' => ['px'=>['min'=>4,'max'=>30]],
            'default' => ['unit'=>'px','size'=>12],
        ]);
        $this->add_control('dot_color', [
            'label' => __('Dot Color', 'my-core-plugin'),
            'type' => Controls_Manager::COLOR,
            'default' => 'rgba(255,255,255,0.2)'
        ]);
        $this->add_control('dot_active_color', [
            'label' => __('Active Dot Color', 'my-core-plugin'),
            'type' => Controls_Manager::COLOR,
            'default' => '#9d79ff'
        ]);
        $this->end_controls_section();

        $this->start_controls_section('nav_style_section', [
            'label' => __('Navigation Buttons', 'my-core-plugin'),
            'tab' => Controls_Manager::TAB_STYLE,
            'condition' => ['navigation' => 'arrows'],
        ]);
        $this->add_responsive_control('nav_button_size', [
            'label'=>'Button Size',
            'type'=>Controls_Manager::SLIDER,
            'range'=>['px'=>['min'=>20,'max'=>100]],
            'default'=>['unit'=>'px','size'=>45],
            'selectors'=>['{{WRAPPER}} .carousel-nav button'=>'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}; font-size: calc({{SIZE}}{{UNIT}}/1.5);']
        ]);
        $this->add_control('nav_button_color', [
            'label'=>'Button Color',
            'type'=>Controls_Manager::COLOR,
            'default'=>'rgba(88, 88, 88, 0.15)',
            'selectors'=>['{{WRAPPER}} .carousel-nav button'=>'background: {{VALUE}}; color:#fff;']
        ]);
        $this->add_control('nav_button_hover_color', [
            'label'=>'Hover Background Color',
            'type'=>Controls_Manager::COLOR,
            'default'=>'#9d79ff',
            'selectors'=>['{{WRAPPER}} .carousel-nav button:hover'=>'background: {{VALUE}};']
        ]);
        $this->add_responsive_control('nav_button_radius', [
            'label'=>'Border Radius',
            'type'=>Controls_Manager::SLIDER,
            'range'=>['px'=>['min'=>0,'max'=>100],'%'=>['min'=>0,'max'=>50]],
            'selectors'=>['{{WRAPPER}} .carousel-nav button'=>'border-radius: {{SIZE}}{{UNIT}};']
        ]);
        $this->end_controls_section();
    }

    /**
     * Register Quote Icon Style Controls
     */
    private function register_quote_icon_controls() {
        $this->start_controls_section('quote_icon_content_section', [
            'label' => __('Quote Icon', 'my-core-plugin'),
            'tab' => Controls_Manager::TAB_CONTENT,
        ]);

        $this->add_control('show_quote_icon', [
            'label' => __('Show Quote Icon', 'my-core-plugin'),
            'type' => Controls_Manager::SWITCHER,
            'label_on' => __('Show', 'my-core-plugin'),
            'label_off' => __('Hide', 'my-core-plugin'),
            'return_value' => 'yes',
            'default' => 'no',
            'separator' => 'before',
        ]);

        $this->add_control('quote_icon_position', [
            'label' => __('Position', 'my-core-plugin'),
            'type' => Controls_Manager::CHOOSE,
            'options' => [
                'up' => ['title' => __('Up', 'my-core-plugin'), 'icon' => 'eicon-arrow-up'],
                'down' => ['title' => __('Down', 'my-core-plugin'), 'icon' => 'eicon-arrow-down'],
            ],
            'default' => 'up',
            'condition' => ['show_quote_icon' => 'yes'],
        ]);

        $this->add_control('quote_icon', [
            'label' => __('Icon', 'my-core-plugin'),
            'type' => Controls_Manager::ICONS,
            'default' => [
                'value' => 'fas fa-quote-right',
                'library' => 'fa-solid',
            ],
            'condition' => ['show_quote_icon' => 'yes'],
        ]);

        $this->add_responsive_control('quote_icon_align', [
            'label' => __('Icon Alignment', 'my-core-plugin'),
            'type' => Controls_Manager::CHOOSE,
            'options' => [
                'left' => ['title' => __('Left', 'my-core-plugin'), 'icon' => 'eicon-text-align-left'],
                'center' => ['title' => __('Center', 'my-core-plugin'), 'icon' => 'eicon-text-align-center'],
                'right' => ['title' => __('Right', 'my-core-plugin'), 'icon' => 'eicon-text-align-right'],
            ],
            'default' => 'right',
            'selectors' => ['{{WRAPPER}} .testimonial-quote-icon' => 'text-align: {{VALUE}};'],
        ]);

        $this->add_responsive_control('quote_icon_spacing', [
            'label' => __('Spacing', 'my-core-plugin'),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => ['px', 'em', '%'],
            'selectors' => ['{{WRAPPER}} .testimonial-quote-icon' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'],
        ]);

        $this->add_control('quote_icon_color', [
            'label' => __('Icon Color', 'my-core-plugin'),
            'type' => Controls_Manager::COLOR,
            'selectors' => ['{{WRAPPER}} .testimonial-quote-icon' => 'color: {{VALUE}};'],
        ]);

        $this->add_responsive_control('quote_icon_size', [
            'label' => __('Icon Size', 'my-core-plugin'),
            'type' => Controls_Manager::SLIDER,
            'default' => [
                'unit' => 'px',
                'size' => 40,
            ],
            'range' => ['px' => ['min' => 10, 'max' => 200]],
            'selectors' => ['{{WRAPPER}} .testimonial-quote-icon' => 'font-size: {{SIZE}}{{UNIT}};'],
        ]);

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        $unique_id = 'testimonial-carousel-' . $this->get_id();
        $prev_id = $unique_id . '-prev';
        $next_id = $unique_id . '-next';

        // Pass settings to the script.
        $this->add_render_attribute( 'wrapper', 'data-settings', wp_json_encode( $settings ) );
        ?>
        <div class="testimonial-carousel-wrapper" <?php echo $this->get_render_attribute_string( 'wrapper' ); ?>>
            <div class="testimonial-carousel" id="<?php echo esc_attr($unique_id); ?>">
                <?php foreach($settings['testimonials'] as $i => $testimonial): ?>
                <div class="testimonial-item" data-slider="slide">
                    <?php 
                    $show_icon = ! empty( $settings['show_quote_icon'] ) && 'yes' === $settings['show_quote_icon'] && ! empty( $settings['quote_icon']['value'] );
                    $icon_position = $settings['quote_icon_position'] ?? 'up';
                    
                    if ( $show_icon && 'up' === $icon_position ) : ?>
                        <div class="testimonial-quote-icon"><?php \Elementor\Icons_Manager::render_icon( $settings['quote_icon'], [ 'aria-hidden' => 'true' ] ); ?></div>
                    <?php endif; ?>
                    <p class="testimonial-text"><?php echo esc_html($testimonial['testimonial_text']); ?></p>
                    <?php if ( $show_icon && 'down' === $icon_position ) : ?>
                        <div class="testimonial-quote-icon"><?php \Elementor\Icons_Manager::render_icon( $settings['quote_icon'], [ 'aria-hidden' => 'true' ] ); ?></div>
                    <?php endif; ?>
                    <div class="testimonial-author">
                        <?php $author_image = !empty($testimonial['author_image']['url']) ? esc_url($testimonial['author_image']['url']) : Utils::get_placeholder_image_src(); ?>
                        <img src="<?php echo esc_url($author_image); ?>" alt="<?php echo esc_attr($testimonial['author_name']); ?>" class="author-image">
                        <div>
                            <p class="author-name"><?php echo esc_html($testimonial['author_name']); ?></p>
                            <p class="author-role"><?php echo esc_html($testimonial['author_role']); ?></p>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <?php if (isset($settings['navigation']) && $settings['navigation'] === 'arrows'): ?>
            <div class="carousel-nav">
                <button id="<?php echo esc_attr($prev_id); ?>" class="prev" aria-label="Previous slide">‹</button>
                <button id="<?php echo esc_attr($next_id); ?>" class="next" aria-label="Next slide">›</button>
            </div>
            <?php endif; ?>
        </div>

        <style>
            <?php
                // This block is for styles that cannot be handled by selectors, like dot colors.
            ?>
            {{WRAPPER}} .carousel-dots .dot { background-color: <?php echo esc_js($settings['dot_color'] ?? 'rgba(255,255,255,0.2)'); ?>; }
            {{WRAPPER}} .carousel-dots .dot.active { background-color: <?php echo esc_js($settings['dot_active_color'] ?? '#9d79ff'); ?>; }
        </style>
        <?php
    }
}
