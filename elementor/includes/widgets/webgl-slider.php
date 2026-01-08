<?php
/**
 * Plugin Name: Elementor WebGL Slider
 * Description: WebGL Slider (Three.js + GSAP) with autoplay, overlay text animations, and shader effects.
 * Author: Tophive
 */
/** 
 *
 * @package My_Core_Plugin\Widgets
 */
namespace My_Core_Plugin\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;

if (!defined('ABSPATH')) exit;
/**
 * Class WebGL_Slider
 * @since 1.0.0
 */
class WebGL_Slider extends Widget_Base {

    public function get_name() { return 'webgl_slider'; }
    public function get_title() { return __('WebGL Slider', 'my-core-plugin'); }
    public function get_icon() { return 'eicon-slider-push'; }
    public function get_categories() { return ['th-general']; }

    public function __construct($data = [], $args = null) {
        parent::__construct($data, $args);
        
        wp_register_script('gsap', 'https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js', [], '3.12.2', true);
        // The main bundle is already registered in elements.php, so we just depend on it.
        wp_register_style('webgl-slider-css', TH_ELEMENTOR_URL . 'assets/css/elements.css', [], '1.0');
    }

    public function get_script_depends() { 
        return ['tophive-elementor-bundle']; 
    }
    public function get_style_depends() { return ['webgl-slider-css']; }


    protected function register_controls() {
        // ---------------- Content Section ----------------
        $this->start_controls_section('content_section', [
            'label' => __('Slides', 'my-core-plugin'),
            'tab' => Controls_Manager::TAB_CONTENT,
        ]);

        $repeater = new \Elementor\Repeater();

        $repeater->add_control('slide_title', [
            'label' => __('Title', 'my-core-plugin'),
            'type' => Controls_Manager::TEXTAREA,
            'default' => 'Slide Title',
            'label_block' => true,
        ]);

        $repeater->add_control('slide_description', [
            'label' => __('Description', 'my-core-plugin'),
            'type' => Controls_Manager::TEXTAREA,
            'default' => 'Slide description goes here.',
            'label_block' => true,
        ]);

        $repeater->add_control('slide_image', [
            'label' => __('Image', 'my-core-plugin'),
            'type' => Controls_Manager::MEDIA,
            'default' => [
                'url' => \Elementor\Utils::get_placeholder_image_src(),
            ],
        ]);

        $repeater->add_control('text_effect', [
            'label' => __('Text Effect', 'my-core-plugin'),
            'type' => Controls_Manager::SELECT,
            'default' => 'fade',
            'options' => [
                'none'  => __('None', 'my-core-plugin'),
                'fade'  => __('Fade', 'my-core-plugin'),
                'slide' => __('Slide', 'my-core-plugin'),
                'zoom'  => __('Zoom', 'my-core-plugin'),
                'glitch'=> __('Glitch', 'my-core-plugin'),
            ],
        ]);

        $repeater->add_control('text_duration', [
            'label' => __('Animation Duration (s)', 'my-core-plugin'),
            'type' => Controls_Manager::NUMBER,
            'default' => 0.8,
            'min' => 0.1,
            'max' => 5,
            'step' => 0.1,
        ]);

        $repeater->add_control('text_delay', [
            'label' => __('Animation Delay (s)', 'my-core-plugin'),
            'type' => Controls_Manager::NUMBER,
            'default' => 0,
            'min' => 0,
            'max' => 5,
            'step' => 0.1,
        ]);

        $repeater->add_control('text_ease', [
            'label' => __('Animation Ease', 'my-core-plugin'),
            'type' => Controls_Manager::SELECT,
            'default' => 'power2.inOut',
            'options' => [
                'power1.inOut' => 'Power1 InOut',
                'power2.inOut' => 'Power2 InOut',
                'power3.inOut' => 'Power3 InOut',
                'expo.inOut'   => 'Expo InOut',
                'elastic.out'  => 'Elastic Out',
            ],
        ]);

        $this->add_control(
            'slides', [
                'label'       => __('Slides List', 'my-core-plugin'),
                'type'        => Controls_Manager::REPEATER,
                'fields'      => $repeater->get_controls(),
                'default'     => [
                    [
                        'slide_title'       => __('First Slide', 'my-core-plugin'),
                        'slide_description' => __('This is the first slide description.', 'my-core-plugin'),
                        'slide_image'       => [ 'url' => \Elementor\Utils::get_placeholder_image_src() ],
                        'text_effect'       => 'fade',
                        'text_duration'     => 0.8,
                        'text_delay'        => 0,
                        'text_ease'         => 'power2.inOut',
                    ],
                    [
                        'slide_title'       => __('Second Slide', 'my-core-plugin'),
                        'slide_description' => __('This is the second slide description.', 'my-core-plugin'),
                        'slide_image'       => [ 'url' => \Elementor\Utils::get_placeholder_image_src() ],
                        'text_effect'       => 'slide',
                        'text_duration'     => 0.8,
                        'text_delay'        => 0,
                        'text_ease'         => 'power2.inOut',
                    ],
                ],
                'title_field' => '{{{ slide_title }}}',
            ]
        );

        $this->end_controls_section();

        // ---------------- Effect Section ----------------
        $this->start_controls_section('effect_section', [
            'label' => __('Effect', 'my-core-plugin'),
            'tab' => Controls_Manager::TAB_CONTENT,
        ]);

        $this->add_control('webgl_effect', [
            'label' => __('WebGL Effect', 'my-core-plugin'),
            'type' => Controls_Manager::SELECT,
            'default' => 'displacement',
            'options' => [
                'displacement' => __('Displacement', 'my-core-plugin'),
                'rgb_split' => __('RGB Split / Glitch', 'my-core-plugin'),
                'ripple' => __('Ripple / Wave', 'my-core-plugin'),
                'zoom_blur' => __('Zoom Blur', 'my-core-plugin'),
                'pixelate' => __('Pixelate', 'my-core-plugin'),
                'invert' => __('Invert / Negative', 'my-core-plugin'),
                'grayscale' => __('Grayscale / Sepia', 'my-core-plugin'),
                'noise' => __('Noise / TV Static', 'my-core-plugin'),
                'vignette' => __('Vignette / Edge Fade', 'my-core-plugin'),
                'kaleidoscope' => __('Kaleidoscope / Mirror', 'my-core-plugin'),
            ],
            'description' => __('Global WebGL shader effect applied to all slides.', 'my-core-plugin'),
        ]);

        $this->end_controls_section();

        // ---------------- Auto-play Section ----------------
        $this->start_controls_section('autoplay_section', [
            'label' => __('Auto-play Settings', 'my-core-plugin'),
            'tab' => Controls_Manager::TAB_CONTENT,
        ]);
        $this->add_control('autoplay_enable', [
            'label' => __('Enable Auto-play', 'my-core-plugin'),
            'type' => Controls_Manager::SWITCHER,
            'label_on'=>'Yes','label_off'=>'No',
            'return_value'=>'yes','default'=>'yes',
        ]);
        $this->add_control('autoplay_speed', [
            'label' => __('Auto-play Speed (ms)', 'my-core-plugin'),
            'type' => Controls_Manager::NUMBER,
            'default' => 5000,
            'condition'=>['autoplay_enable'=>'yes'],
        ]);
        $this->add_control('pause_on_hover', [
            'label' => __('Pause on Hover', 'my-core-plugin'),
            'type' => Controls_Manager::SWITCHER,
            'label_on'=>'Yes','label_off'=>'No',
            'return_value'=>'yes','default'=>'yes',
            'condition'=>['autoplay_enable'=>'yes'],
        ]);
        $this->end_controls_section();

        // ---------------- Style Section ----------------
        $this->start_controls_section('style_section', [
            'label' => __('Slider Style', 'my-core-plugin'),
            'tab' => Controls_Manager::TAB_STYLE,
        ]);
        $this->add_responsive_control('slider_height', [
            'label' => __('Slider Height', 'my-core-plugin'),
            'type' => Controls_Manager::SLIDER,
            'size_units' => ['px', '%', 'vh'],
            'range' => [
                'px' => ['min' => 100, 'max' => 2000],
                '%' => ['min' => 10, 'max' => 100],
                'vh' => ['min' => 10, 'max' => 100],
            ],
            'default' => ['unit'=>'vh','size'=>100],
            'selectors'=>['{{WRAPPER}} .webgl-slider-container'=>'height: {{SIZE}}{{UNIT}};'],
        ]);
        $this->add_group_control(Group_Control_Border::get_type(), [
            'name' => 'slider_border',
            'label' => __('Slider Border', 'my-core-plugin'),
            'selector' => '{{WRAPPER}} .webgl-slider-container',
        ]);
        $this->add_control('slider_border_radius', [
            'label' => __('Slider Border Radius', 'my-core-plugin'),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => ['px','%','em'],
            'default' => ['top'=>0,'right'=>0,'bottom'=>0,'left'=>0,'unit'=>'px'],
            'selectors'=>['{{WRAPPER}} .webgl-slider-container'=>'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'],
        ]);
        $this->end_controls_section();

        // ---------------- Text Style Section ----------------
        $this->start_controls_section('text_style_section', [
            'label' => __('Text Style', 'my-core-plugin'),
            'tab'   => Controls_Manager::TAB_STYLE,
        ]);
        $this->add_control('title_color', [
            'label'=>__('Slide Title Color','my-core-plugin'),
            'type'=>Controls_Manager::COLOR,
            'default'=>'#ffffff',
            'selectors'=>['{{WRAPPER}} .webgl-slider-title'=>'color: {{VALUE}};'],
        ]);
        $this->add_control('description_color', [
            'label'=>__('Slide Description Color','my-core-plugin'),
            'type'=>Controls_Manager::COLOR,
            'default'=>'#dddddd',
            'selectors'=>['{{WRAPPER}} .webgl-slider-description'=>'color: {{VALUE}};'],
        ]);
        $this->add_group_control(Group_Control_Typography::get_type(), [
            'name'=>'title_typography',
            'label'=>'Slide Title Typography',
            'selector'=>'{{WRAPPER}} .webgl-slider-title',
        ]);
        $this->add_group_control(Group_Control_Typography::get_type(), [
            'name'=>'description_typography',
            'label'=>'Slide Description Typography',
            'selector'=>'{{WRAPPER}} .webgl-slider-description',
        ]);
        $this->add_responsive_control('text_align', [
            'label'     => __('Text Alignment', 'my-core-plugin'),
            'type'      => Controls_Manager::CHOOSE,
            'options'   => [
                'left'=>['title'=>'Left','icon'=>'eicon-text-align-left'],
                'center'=>['title'=>'Center','icon'=>'eicon-text-align-center'],
                'right'=>['title'=>'Right','icon'=>'eicon-text-align-right'],
            ],
            'default'=>'center','toggle'=>true,
            'selectors'=>[
                '{{WRAPPER}} .webgl-slider-title'=>'text-align: {{VALUE}};',
                '{{WRAPPER}} .webgl-slider-description'=>'text-align: {{VALUE}};',
            ],
        ]);
        $this->end_controls_section();

        // ---------------- Progress Section ----------------
        $this->start_controls_section('nav_progress_section', [
            'label' => __('Progress Slider', 'my-core-plugin'),
            'tab' => Controls_Manager::TAB_STYLE,
        ]);
        $this->add_control('show_progress',['label'=>__('Show Progress Bar','my-core-plugin'),'type'=>Controls_Manager::SWITCHER,'label_on'=>'Yes','label_off'=>'No','return_value'=>'yes','default'=>'yes']);
        $this->add_control('progress_color', [
            'label'=>__('Progress Color','my-core-plugin'),
            'type'=>Controls_Manager::COLOR,
            'default'=>'#d5d5d5ff',
            'selectors'=>['{{WRAPPER}} .slider-progress'=>'background-color: {{VALUE}};'],
        ]);
        $this->add_control('progress_line_color', [
            'label'=>__('Progress Line Color','my-core-plugin'),
            'type'=>Controls_Manager::COLOR,
            'default'=>'#9d79ff',
            'selectors'=>['{{WRAPPER}} .slider-progress-line'=>'background-color: {{VALUE}};'],
        ]);
        $this->add_control('progress_width', [
            'label'=>__('Progress Width (%)','my-core-plugin'),
            'type'=>Controls_Manager::SLIDER,
            'default'=>['size'=>80],
            'range'=>['px'=>['min'=>0,'max'=>100]],
            'selectors'=>['{{WRAPPER}} .slider-progress'=>'width: {{SIZE}}%;'],
        ]);
        $this->add_control('progress_position', [
            'label' => __('Progress Position', 'my-core-plugin'),
            'type' => Controls_Manager::SELECT,
            'default' => 'bottom',
            'options' => ['top'=>'Top','bottom'=>'Bottom'],
        ]);
        $this->add_control('progress_align', [
            'label' => __('Progress Alignment', 'my-core-plugin'),
            'type' => Controls_Manager::SELECT,
            'default' => 'center',
            'options' => ['left'=>'Left','center'=>'Center','right'=>'Right'],
        ]);
        $this->end_controls_section();

        // ---------------- Navigation Style Section ----------------
        $this->start_controls_section('navigation_style_section', [
            'label' => __('Navigation', 'my-core-plugin'),
            'tab'   => Controls_Manager::TAB_STYLE,
        ]);
        $this->add_control('show_navigation',['label'=>__('Show Navigation','my-core-plugin'),'type'=>Controls_Manager::SWITCHER,'label_on'=>'Yes','label_off'=>'No','return_value'=>'yes','default'=>'yes']);
        $this->add_control('nav_button_color', [
            'label' => __('Icon Color', 'my-core-plugin'),
            'type'  => Controls_Manager::COLOR,
            'default' => '#ffffff',
            'selectors' => ['{{WRAPPER}} .slider-nav button' => 'color: {{VALUE}};'],
        ]);
        $this->add_control('nav_button_bg', [
            'label' => __('Background Color', 'my-core-plugin'),
            'type'  => Controls_Manager::COLOR,
            'default' => 'rgba(0,0,0,0.4)',
            'selectors' => ['{{WRAPPER}} .slider-nav button' => 'background-color: {{VALUE}};'],
        ]);
        $this->add_group_control(Group_Control_Border::get_type(), [
            'name' => 'nav_button_border',
            'selector' => '{{WRAPPER}} .slider-nav button',
        ]);
        $this->add_control('nav_button_radius', [
            'label' => __('Border Radius', 'my-core-plugin'),
            'type' => Controls_Manager::SLIDER,
            'size_units' => ['px','%'],
            'range' => ['px'=>['min'=>0,'max'=>100]],
            'selectors'=>['{{WRAPPER}} .slider-nav button'=>'border-radius: {{SIZE}}{{UNIT}};'],
        ]);
        $this->add_control('nav_button_size', [
            'label' => __('Button Size', 'my-core-plugin'),
            'type' => Controls_Manager::SLIDER,
            'size_units'=>['px'],
            'range'=>['px'=>['min'=>20,'max'=>200]],
            'default'=>['size'=>50],
            'selectors'=>['{{WRAPPER}} .slider-nav button'=>'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}; font-size: calc({{SIZE}}{{UNIT}}/2);'],
        ]);
        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        $slider_id = 'webgl-slider-' . $this->get_id();
        $show_nav = ($settings['show_navigation'] ?? 'yes') === 'yes';
        $show_progress = ($settings['show_progress'] ?? 'yes') === 'yes';

        $data_settings = [
            'autoplay_enable' => $settings['autoplay_enable'] ?? 'yes',
            'autoplay_speed' => $settings['autoplay_speed'] ?? 5000,
            'pause_on_hover' => $settings['pause_on_hover'] ?? 'yes',
            'webgl_effect' => $settings['webgl_effect'] ?? 'displacement',
            'show_navigation' => $show_nav ? 'yes' : 'no',
            'show_progress' => $show_progress ? 'yes' : 'no',
        ];
        ?>
        <div id="<?php echo esc_attr($slider_id); ?>" class="webgl-slider-container" data-settings='<?php echo wp_json_encode($data_settings); ?>'>
            <div class="slider-inner">
                <div class="slider-content">
                    <h2 class="webgl-slider-title"><?php echo esc_html($settings['slides'][0]['slide_title']); ?></h2>
                    <p class="webgl-slider-description"><?php echo esc_html($settings['slides'][0]['slide_description']); ?></p>
                </div>
            </div>

            <?php foreach($settings['slides'] as $slide): ?>
                <img src="<?php echo esc_url($slide['slide_image']['url']); ?>"
                    alt="<?php echo esc_attr($slide['slide_title']); ?>"
                    data-title="<?php echo esc_attr($slide['slide_title']); ?>"
                    data-description="<?php echo esc_attr($slide['slide_description']); ?>"
                    data-text-effect="<?php echo esc_attr($slide['text_effect']); ?>"
                    data-text-duration="<?php echo esc_attr($slide['text_duration']); ?>"
                    data-text-delay="<?php echo esc_attr($slide['text_delay']); ?>"
                    data-text-ease="<?php echo esc_attr($slide['text_ease']); ?>">
            <?php endforeach; ?>

            <?php if($show_progress): ?>
            <div class="slider-progress"
                style="bottom:<?php echo $settings['progress_position']=='bottom'?'20px':'auto'; ?>;
                        top:<?php echo $settings['progress_position']=='top'?'20px':'auto'; ?>;
                        display:flex;
                        justify-content:<?php 
                            echo $settings['progress_align'] === 'left' ? 'flex-start' :
                                ($settings['progress_align'] === 'right' ? 'flex-end' : 'center'); ?>;">
                <div class="slider-progress-line"></div>
            </div>
            <?php endif; ?>

            <?php if($show_nav): ?>
            <div class="slider-nav">
                <button class="prev-slide">&#10094;</button>
                <button class="next-slide">&#10095;</button>
            </div>
            <?php endif; ?>
        </div>        
        <?php
    }
}