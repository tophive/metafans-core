<?php

namespace TophiveElementor\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Widget_Base;

class TH_Video_Player extends Widget_Base
{
    /**
     * Get widget name.
     *
     * Retrieve heading widget name.
     *
     * @since 1.0.0
     *
     * @return string widget name
     */
    public function get_name()
    {
        return 'th_video_player';
    }

    /**
     * Get widget title.
     *
     * Retrieve heading widget title.
     *
     * @since 1.0.0
     *
     * @return string widget title
     */
    public function get_title()
    {
        return TH_ELEMENTOR_DISPLAY_NAME_SC.esc_html__(' Video', TH_ELEMENTOR_SLUG);
    }

    /**
     * Get widget icon.
     *
     * Retrieve heading widget icon.
     *
     * @since 1.0.0
     *
     * @return string widget icon
     */
    public function get_icon()
    {
        return 'eicon-video-playlist';
    }

    /**
     * Get widget categories.
     *
     * Used to show widget under a category in the editor.
     *
     * @since 1.0.0
     *
     * @return array widget categories
     */
    public function get_categories()
    {
        return ['th-general'];
    }

    /**
     * Get widget keywords.
     *
     * Retrieve the list of keywords the widget belongs to.
     *
     * @since 1.0.0
     *
     * @return array widget keywords
     */
    public function get_keywords()
    {
        return ['video'];
    }

    /**
     * Register widget controls.
     *
     * Add input fields to allow the user to customize the widget settings.
     *
     * @since 1.0.0
     */
    protected function register_controls()
    {
        // Icon Section
        $this->start_controls_section(
            'th_el_video_section',
            [
                'label' => __('Video', TH_ELEMENTOR_SLUG),
            ]
        );

        $this->add_control(
            'video_source',
            [
                'label' => __('Video Source', TH_ELEMENTOR_SLUG),
                'type' => Controls_Manager::SELECT,
                'default' => 'url',
                'options' => [
                    'url' => __('URL', TH_ELEMENTOR_SLUG),
                    'local' => __('Local', TH_ELEMENTOR_SLUG),
                ],
            ]
        );

        $this->add_control(
            'video_url',
            [
                'label' => __('Video URL', TH_ELEMENTOR_SLUG),
                'type' => Controls_Manager::TEXT,
                'input_type' => 'url',
                'placeholder' => __('Enter your video URL', TH_ELEMENTOR_SLUG),
                'condition' => [
                    'video_source' => 'url',
                ],
            ]
        );

        $this->add_control(
            'video_file',
            [
                'label' => __('Video File', TH_ELEMENTOR_SLUG),
                'type' => Controls_Manager::MEDIA,
                'media_type' => 'video',
                'condition' => [
                    'video_source' => 'local',
                ],
            ]
        );

        $this->add_control(
            'video_start_time',
            [
                'label' => __('Start Time (seconds)', TH_ELEMENTOR_SLUG),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'default' => 0,
            ]
        );
        $this->add_control(
            'video_end_time',
            [
                'label' => __('End Time (seconds)', TH_ELEMENTOR_SLUG),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'default' => 0,
            ]
        );

        $this->add_control(
            'autoplay',
            [
                'label' => __('Autoplay', TH_ELEMENTOR_SLUG),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Yes', TH_ELEMENTOR_SLUG),
                'label_off' => __('No', TH_ELEMENTOR_SLUG),
                'return_value' => 'yes',
                'default' => 'no',
            ]
        );

        $this->add_control(
            'controls',
            [
                'label' => __('Show Controls', TH_ELEMENTOR_SLUG),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Yes', TH_ELEMENTOR_SLUG),
                'label_off' => __('No', TH_ELEMENTOR_SLUG),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );
        $this->add_control(
            'mute',
            [
                'label' => __('Mute', TH_ELEMENTOR_SLUG),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __('Yes', TH_ELEMENTOR_SLUG),
                'label_off' => __('No', TH_ELEMENTOR_SLUG),
                'return_value' => 'yes',
                'default' => 'no',
            ]
        );
        $this->add_control(
            'loop',
            [
                'label' => __('Loop', TH_ELEMENTOR_SLUG),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __('Yes', TH_ELEMENTOR_SLUG),
                'label_off' => __('No', TH_ELEMENTOR_SLUG),
                'return_value' => 'yes',
                'default' => 'no',
            ]
        );
        $this->add_control(
            'video_filter',
            [
                'label' => __('Video Filter', TH_ELEMENTOR_SLUG),
                'type' => \Elementor\Controls_Manager::SELECT,
                'options' => [
                    'none' => __('None', TH_ELEMENTOR_SLUG),
                    'grayscale' => __('Grayscale', TH_ELEMENTOR_SLUG),
                    'sepia' => __('Sepia', TH_ELEMENTOR_SLUG),
                    'blur' => __('Blur', TH_ELEMENTOR_SLUG),
                    'brightness' => __('Brightness', TH_ELEMENTOR_SLUG),
                    'contrast' => __('Contrast', TH_ELEMENTOR_SLUG),
                ],
                'default' => 'none',
            ]
        );
        $this->add_control(
            'video_aspect_ratio',
            [
                'label' => __('Video Aspect Ratio', 'plugin-name'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'options' => [
                    '16-9' => '16:9',
                    '4-3' => '4:3',
                    '1-1' => '1:1',
                ],
                'default' => '16-9',
            ]
        );

        $this->add_control(
            'lazy_load',
            [
                'label' => __('Lazy Load', TH_ELEMENTOR_SLUG),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __('Yes', TH_ELEMENTOR_SLUG),
                'label_off' => __('No', TH_ELEMENTOR_SLUG),
                'return_value' => 'yes',
                'default' => 'no',
            ]
        );
        $this->add_control(
            'overlay_image',
            [
                'label' => __('Overlay Image', TH_ELEMENTOR_SLUG),
                'type' => \Elementor\Controls_Manager::MEDIA,
                'default' => [
                    'url' => \Elementor\Utils::get_placeholder_image_src(),
                ],
            ]
        );

        $this->add_control(
            'overlay_opacity',
            [
                'label' => __('Overlay Opacity', TH_ELEMENTOR_SLUG),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['%'],
                'range' => [
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'default' => [
                    'unit' => '%',
                    'size' => 50,
                ],
            ]
        );

        $this->end_controls_section();

        // Background Section
        $this->start_controls_section(
            'section_background',
            [
                'label' => __('Background', TH_ELEMENTOR_SLUG),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'background',
                'label' => __('Background', TH_ELEMENTOR_SLUG),
                'types' => ['classic', 'gradient', 'video'],
                'selector' => '{{WRAPPER}} .th-video-player',
            ]
        );

        $this->end_controls_section();
    }

    /**
     * Render widget output on the frontend.
     *
     * Written in PHP and used to generate the final HTML.
     *
     * @since 1.0.0
     */
    protected function render()
    {
        $settings = $this->get_settings_for_display();

        $video_url = !empty($settings['video_url']) ? esc_url($settings['video_url']) : '';
        $video_file = !empty($settings['video_file']['url']) ? esc_url($settings['video_file']['url']) : '';
        $autoplay = $settings['autoplay'] === 'yes' ? 'autoplay' : '';
        $controls = $settings['controls'] === 'yes' ? 'controls' : '';

        echo '<div class="th-video-player" style="position:relative; padding-bottom:56.25%; height:0; overflow:hidden;">';
        echo '<video width="100%" height="100%" style="position:absolute; top:0; left:0;" '.$autoplay.' '.$controls.'>';

        if ('yes' === $settings['lazy_load']) {
            echo '<img src="your-image.jpg" alt="Your Image" loading="lazy">';
        } else {
            echo '<img src="your-image.jpg" alt="Your Image">';
        }

        if ($settings['video_source'] === 'url' && $video_url) {
            echo '<source src="'.$video_url.'" type="video/mp4">';
        } elseif ($settings['video_source'] === 'local' && $video_file) {
            echo '<source src="'.$video_file.'" type="video/mp4">';
        }
        echo __('Your browser does not support the video tag.', TH_ELEMENTOR_SLUG);
        echo '</video>';
        echo '</div>';
    }

    /**
     * Render widget output in the editor.
     *
     * Written in Backbone.js and used to generate the live preview.
     *
     * @since 1.0.0
     */
    protected function content_template()
    {
        ?>
        <div class="th-video-player" style="position:relative; padding-bottom:56.25%; height:0; overflow:hidden;">
            <video width="100%" height="100%" style="position:absolute; top:0; left:0;" 
                   <# if ( settings.autoplay === 'yes' ) { #> autoplay <# } #>
                   <# if ( settings.controls === 'yes' ) { #> controls <# } #>>
                <# var video_url = settings.video_source === 'url' ? settings.video_url : settings.video_file.url; #>
                <# if ( video_url ) { #>
                    <source src="{{ video_url }}" type="video/mp4">
                <# } #>
                <?php echo __('Your browser does not support the video tag.', TH_ELEMENTOR_SLUG); ?>
            </video>
        </div>
        <?php
    }
}

\Elementor\Plugin::instance()->widgets_manager->register(new TH_Video_Player());
