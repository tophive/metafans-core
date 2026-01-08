<?php
/**
 * Plugin Name: Liquid Morphology Slideshow widget
 * Description: Liquid Morphology slideshow (Three.js + GSAP) with autoplay, overlay text animation, slider style controls, and shader effects.
 * Version: 1.0
 * Author: Tophive
 */

/** 
 *
 * @package My_Core_Plugin\Widgets
 */
namespace My_Core_Plugin\Widgets;

use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Widget_Base;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
/**
 * Class Liquid_Morphology_Slideshow
 * @since 1.0.0
 */

class Liquid_Morphology_Slideshow extends Widget_Base {

    public function get_name() {
        return 'liquid_morphology_slideshow';
    }

    public function get_title() {
        return __( 'Liquid Slideshow', 'my-core-plugin' );
    }

    public function get_icon() {
        return 'eicon-slider-push';
    }

    public function get_script_depends() {
        return ['tophive-elementor-bundle'];
    }

    public function get_style_depends() {
        return ['tophive-elements-css'];
    }

    public function get_categories() {
        return [ 'th-general' ];
    }

    /**
     * Register widget controls.
     *
     * @return void
     */
    protected function register_controls() {

        // Slides section
        $this->start_controls_section(
            'slides_section',
            [
                'label' => __( 'Slides', 'my-core-plugin' ),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $repeater = new Repeater();
        $repeater->add_control(
            'slide_title',
            [
                'label' => __( 'Title', 'my-core-plugin' ),
                'type' => Controls_Manager::TEXT,
                'default' => __( 'Slide Title', 'my-core-plugin' ),
            ]
        );
        $repeater->add_control(
            'slide_subtitle',
            [
                'label' => __( 'Subtitle', 'my-core-plugin' ),
                'type' => Controls_Manager::TEXT,
                'default' => __( 'Subtitle text', 'my-core-plugin' ),
            ]
        );
        $repeater->add_control(
            'slide_image',
            [
                'label' => __( 'Image', 'my-core-plugin' ),
                'type' => Controls_Manager::MEDIA,
                'default' => [ 'url' => \Elementor\Utils::get_placeholder_image_src() ],
            ]
        );

        $this->add_control(
            'slides',
            [
                'label' => __( 'Slides', 'my-core-plugin' ),
                'type' => Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
                'default' => [
                    [ 'slide_title' => 'Ethereal Glow', 'slide_subtitle' => 'Subtle portrait' ],
                    [ 'slide_title' => 'Rose Mirage', 'slide_subtitle' => 'Soft lighting' ],
                ],
                'title_field' => '{{{ slide_title }}}',
            ]
        );

        // Effect settings
        $this->add_control(
            'effect_type',
            [
                'label' => __( 'Image Effect', 'my-core-plugin' ),
                'type' => Controls_Manager::SELECT,
                'default' => 'liquid',
                'separator' => 'before',
                'options' => [
                    'liquid' => __( 'Liquid', 'my-core-plugin' ),
                    'fade'   => __( 'Fade', 'my-core-plugin' ),
                    'slide'  => __( 'Slide', 'my-core-plugin' ),
                    'morph'  => __( 'Morph', 'my-core-plugin' ),
                    'ripple' => __( 'Water Ripple', 'my-core-plugin' ),
                    'circle' => __( 'Circle Reveal', 'my-core-plugin' ),
                    'cinematic' => __( 'Cinematic Zoom', 'my-core-plugin' ),
                    'aurora' => __( 'Aurora Flow', 'my-core-plugin' ),
                    'timewarp' => __( 'Timewarp Zoom', 'my-core-plugin' ),
                    'particle' => __( 'Particle Swirl', 'my-core-plugin' ),
                    'ocean' => __( 'Ocean Caustics', 'my-core-plugin' ),
                    'chrome' => __( 'Chrome Morph', 'my-core-plugin' ),
                    'pixel' => __( 'Pixelation', 'my-core-plugin' ),
                    'glitch_chroma'  => __( 'Glitch Chromatic', 'my-core-plugin' ),
                    'glitch_warp_rgb' => __( 'RGB Split', 'my-core-plugin' ),
                ],
            ]
        );
        // Effect intensity control
        $this->add_control(
            'effect_intensity',
            [
                'label' => __( 'Effect Intensity', 'my-core-plugin' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ '' ],
                'range' => [ '' => [ 'min' => 0, 'max' => 2 ] ],
                'step' => 0.05,
                'default' => [ 'unit' => '', 'size' => 0.6 ],
                'description' => __( 'Lower to reduce GPU work; higher for stronger effect.', 'my-core-plugin' ),
            ]
        );
        // Title animation effect control
        $this->add_control(
            'title_animation_effect',
            [
                'label' => __( 'Text Effect', 'my-core-plugin' ),
                'type' => Controls_Manager::SELECT,
                'default' => 'fade_slide',
                'options' => [
                    'fade_slide'        => __( 'Fade + Slide', 'my-core-plugin' ),
                    'elastic_pop'       => __( 'Elastic Pop', 'my-core-plugin' ),
                    'wave'              => __( 'Wave Motion', 'my-core-plugin' ),
                    'flip'              => __( '3D Flip', 'my-core-plugin' ),
                    'scramble'          => __( 'Scramble / Random', 'my-core-plugin' ),
                    'trail'             => __( 'Trail / Afterimage', 'my-core-plugin' ),
                    'typing_scramble'   => __( 'Typing / Scramble', 'my-core-plugin' ),
                    'popout_zoom'       => __( 'Pop-Out / Zoom In', 'my-core-plugin' ),
                    'wave_3d'           => __( 'Wave + 3D Rotation', 'my-core-plugin' ),
                    'morph_liquid'      => __( 'Morphing / Liquid Text', 'my-core-plugin' ),
                    'cascade'           => __( 'Cascade / Staggered Timeline', 'my-core-plugin' ),
                    'glitch'            => __( 'Shake / Glitch Animation', 'my-core-plugin' ),
                    'sweep'             => __( 'Diagonal / Sweep Reveal', 'my-core-plugin' ),
                ],
                'separator' => 'before',
                'description' => __( 'Select the animation effect for slide title and subtitle.', 'my-core-plugin' ),
            ]
        );

        $this->end_controls_section();

        // Autoplay section
        $this->start_controls_section(
            'autoplay_section',
            [
                'label' => __( 'Autoplay', 'my-core-plugin' ),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'autoplay',
            [
                'label' => __( 'Enable Autoplay', 'my-core-plugin' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __( 'Yes', 'my-core-plugin' ),
                'label_off' => __( 'No', 'my-core-plugin' ),
                'return_value' => 'yes',
                'default' => 'no',
            ]
        );

        $this->add_control(
            'autoplay_delay',
            [
                'label' => __( 'Autoplay Delay (seconds)', 'my-core-plugin' ),
                'type' => Controls_Manager::NUMBER,
                'min' => 1,
                'max' => 60,
                'step' => 1,
                'default' => 5,
                'condition' => [ 'autoplay' => 'yes' ],
            ]
        );

        $this->add_control(
            'autoplay_pause_hover',
            [
                'label' => __( 'Pause on Hover', 'my-core-plugin' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => 'Yes',
                'label_off' => 'No',
                'return_value' => 'yes',
                'default' => 'yes',
                'condition' => [ 'autoplay' => 'yes' ],
            ]
        );

        $this->add_control(
            'autoplay_pause_interaction',
            [
                'label' => __( 'Pause on Interaction', 'my-core-plugin' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => 'Yes',
                'label_off' => 'No',
                'return_value' => 'yes',
                'default' => 'yes',
                'condition' => [ 'autoplay' => 'yes' ],
            ]
        );

        $this->add_control(
            'autoplay_loop',
            [
                'label' => __( 'Loop Slides', 'my-core-plugin' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => 'Yes',
                'label_off' => 'No',
                'return_value' => 'yes',
                'default' => 'yes',
                'condition' => [ 'autoplay' => 'yes' ],
            ]
        );

        $this->end_controls_section();

        // Slider style & overlay sections
        $this->start_controls_section(
            'slider_style_section',
            [
                'label' => __( 'Slider Style', 'my-core-plugin' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'slider_height',
            [
                'label' => __( 'Slider Height', 'my-core-plugin' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', 'vh', '%' ],
                'range' => [
                    'px' => [ 'min' => 100, 'max' => 2000 ],
                    'vh' => [ 'min' => 10, 'max' => 100 ],
                    '%'  => [ 'min' => 10, 'max' => 100 ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .lm-slider-wrapper' => 'height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name' => 'slider_border',
                'label' => __( 'Slider Border', 'my-core-plugin' ),
                'selector' => '{{WRAPPER}} .lm-slider-wrapper',
            ]
        );

        $this->add_responsive_control(
            'slider_border_radius',
            [
                'label' => __( 'Border Radius', 'my-core-plugin' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', '%' ],
                'range' => [
                    'px' => [ 'min' => 0, 'max' => 500 ],
                    '%'  => [ 'min' => 0, 'max' => 100 ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .lm-slider-wrapper' => 'border-radius: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        // Overlay text style
        $this->start_controls_section(
            'overlay_text_style',
            [
                'label' => __( 'Overlay Text', 'my-core-plugin' ),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'title_typography',
                'label' => __( 'Title Typography', 'my-core-plugin' ),
                'selector' => '{{WRAPPER}} .lm-overlay .lm-title',
                'fields_options' => [
                    'font_size' => [ 'responsive' => true, 'units' => [ 'px', 'em', 'rem' ] ],
                    'line_height' => [ 'responsive' => true, 'units' => [ 'px', 'em', 'rem' ] ],
                    'letter_spacing' => [ 'responsive' => true, 'units' => [ 'px', 'em', 'rem' ] ],
                ],
            ]
        );
        $this->add_group_control(
            \Elementor\Group_Control_Text_Stroke::get_type(),
            [
                'name' => 'title_text_stroke',
                'label' => __( 'Title Text Stroke', 'my-core-plugin' ),
                'selector' => '{{WRAPPER}} .lm-overlay .lm-title',
            ]
        );
        $this->add_control(
            'title_color',
            [
                'label' => __( 'Title Color', 'my-core-plugin' ),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .lm-overlay .lm-title' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'title_subtitle_gap',
            [
                'label' => __( 'Gap between Title & Subtitle', 'my-core-plugin' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', 'em', 'rem' ],
                'range' => [
                    'px' => [ 'min' => 0, 'max' => 200 ],
                    'em' => [ 'min' => 0, 'max' => 20 ],
                    'rem' => [ 'min' => 0, 'max' => 20 ],
                ],
                'default' => [ 'unit' => 'px', 'size' => 20 ],
                'selectors' => [
                    '{{WRAPPER}} .lm-overlay .lm-subtitle' => 'margin-top: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'subtitle_typography',
                'label' => __( 'Subtitle Typography', 'my-core-plugin' ),
                'selector' => '{{WRAPPER}} .lm-overlay .lm-subtitle',
                'fields_options' => [
                    'font_size' => [ 'responsive' => true, 'units' => [ 'px', 'em', 'rem' ] ],
                    'line_height' => [ 'responsive' => true, 'units' => [ 'px', 'em', 'rem' ] ],
                    'letter_spacing' => [ 'responsive' => true, 'units' => [ 'px', 'em', 'rem' ] ],
                ],
            ]
        );

        $this->add_control(
            'subtitle_color',
            [
                'label' => __( 'Subtitle Color', 'my-core-plugin' ),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .lm-overlay .lm-subtitle' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_section();

        // Slide number & nav text style + show/hide controls
        $this->start_controls_section(
            'slider_nav_style_section',
            [
                'label' => __( 'Numbers & Nav', 'my-core-plugin' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'slide_number_typography',
                'label' => __( 'Slide Number Typography', 'my-core-plugin' ),
                'selector' => '{{WRAPPER}} .lm-slide-number',
                'condition' => [ 'show_numbers' => 'yes' ],
            ]
        );

        $this->add_control(
            'slide_number_color',
            [
                'label' => __( 'Slide Number Color', 'my-core-plugin' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [ '{{WRAPPER}} .lm-slide-number' => 'color: {{VALUE}};' ],
                'condition' => [ 'show_numbers' => 'yes' ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'nav_title_typography',
                'label' => __( 'Navigation Title Typography', 'my-core-plugin' ),
                'selector' => '{{WRAPPER}} .lm-slide-nav-title',
                'condition' => [ 'show_nav' => 'yes' ],
            ]
        );

        $this->add_control(
            'nav_title_color',
            [
                'label' => __( 'Navigation Title Color', 'my-core-plugin' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [ '{{WRAPPER}} .lm-slide-nav-title' => 'color: {{VALUE}};' ],
                'condition' => [ 'show_nav' => 'yes' ],
            ]
        );

        $this->add_control(
            'show_numbers',
            [
                'label' => __( 'Show Numbers', 'my-core-plugin' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __( 'Yes', 'my-core-plugin' ),
                'label_off' => __( 'No', 'my-core-plugin' ),
                'return_value' => 'yes',
                'default' => 'yes',
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'show_nav',
            [
                'label' => __( 'Show Navigation', 'my-core-plugin' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __( 'Yes', 'my-core-plugin' ),
                'label_off' => __( 'No', 'my-core-plugin' ),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->end_controls_section();
    }

    /**
     * Render widget front-end.
     *
     * @return void
     */
    protected function render() {
        $settings = $this->get_settings_for_display();
        $slides = isset( $settings['slides'] ) && is_array( $settings['slides'] ) ? $settings['slides'] : [];
		$upload_dir = wp_get_upload_dir();
        $local_file_path = trailingslashit( $upload_dir['basedir'] ) . 'displacement-map.png';
        $local_disp = file_exists( $local_file_path ) ? trailingslashit( $upload_dir['baseurl'] ) . 'displacement-map.png' : '';
        $safe_disp  = 'https://threejs.org/examples/textures/waterdudv.jpg';

        $slides_json = wp_json_encode( $slides, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT ); // This line is no longer used but is kept for context.

        $data_settings = [
            'slides' => isset( $settings['slides'] ) && is_array( $settings['slides'] ) ? $settings['slides'] : [],
            'localDisp' => $local_disp,
            'safeDisp' => $safe_disp,
            'effectType' => $settings['effect_type'] ?? 'liquid',
            'effectIntensity' => $settings['effect_intensity']['size'] ?? 0.6,
            'titleAnimationEffect' => $settings['title_animation_effect'] ?? 'fade_slide',
            'autoplayEnabled' => ($settings['autoplay'] ?? 'no') === 'yes' ? 'true' : 'false',
            'autoplayDelay' => (isset($settings['autoplay_delay']) && is_numeric($settings['autoplay_delay'])) ? (int) $settings['autoplay_delay'] * 1000 : 5000,
            'pauseOnHover' => ($settings['autoplay_pause_hover'] ?? 'yes') === 'yes' ? 'true' : 'false',
            'pauseOnInteraction' => ($settings['autoplay_pause_interaction'] ?? 'yes') === 'yes' ? 'true' : 'false',
            'loopSlides' => ($settings['autoplay_loop'] ?? 'yes') === 'yes' ? 'true' : 'false',
        ];

        $uid = 'lm_slider_' . $this->get_id();
        $show_numbers = ($settings['show_numbers'] ?? 'yes') === 'yes';
        $show_nav = ($settings['show_nav'] ?? 'yes') === 'yes';
        ?>
        <main id="<?php echo esc_attr( $uid ); ?>"
              data-uid="<?php echo esc_attr( $uid ); ?>"
              data-settings='<?php echo wp_json_encode($data_settings); ?>'
              class="lm-slider-wrapper<?php echo $show_nav ? '' : ' lm-hide-nav'; ?><?php echo $show_numbers ? '' : ' lm-hide-numbers'; ?>"
              role="region"
              aria-roledescription="carousel"
              aria-label="<?php echo esc_attr__( 'Image slideshow', 'my-core-plugin' ); ?>">
            <canvas class="lm-webgl-canvas" aria-hidden="true"></canvas>
            <div class="lm-overlay">
                <div class="lm-text">
                    <h2 class="lm-title" id="<?php echo esc_attr( $uid ); ?>-title" aria-live="polite"></h2>
                    <div class="lm-subtitle" id="<?php echo esc_attr( $uid ); ?>-subtitle"></div>
                </div>
            </div>
            <span class="lm-slide-number" id="<?php echo esc_attr( $uid ); ?>-number" aria-hidden="true">01</span>
            <span class="lm-slide-total" id="<?php echo esc_attr( $uid ); ?>-total" aria-hidden="true"><?php echo str_pad( count( $slides ), 2, '0', STR_PAD_LEFT ); ?></span>
            <nav class="lm-slides-navigation" id="<?php echo esc_attr( $uid ); ?>-nav" aria-label="<?php echo esc_attr__( 'Slide navigation', 'my-core-plugin' ); ?>"></nav>
        </main>
        
        <?php
    }
}
