<?php
namespace My_Core_Plugin\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

if (!defined('ABSPATH')) exit;

class Video_Player extends Widget_Base {

    public function get_name() {
        return 'video_player';
    }

    public function get_title() {
        return __('Video Player', 'my-core-plugin');
    }

    public function get_icon() {
        return 'eicon-play';
    }

    public function get_categories() {
        return ['th-general'];
    }

    protected function register_controls() {

        // Content Section
        $this->start_controls_section(
            'content_section',
            [
                'label' => __('Content', 'my-core-plugin'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'video_url',
            [
                'label' => __('Video URL', 'my-core-plugin'),
                'type' => Controls_Manager::TEXT,
                'placeholder' => 'Enter self-hosted video URL',
            ]
        );

        $this->add_control(
            'poster_url',
            [
                'label' => __('Poster Image URL', 'my-core-plugin'),
                'type' => Controls_Manager::MEDIA,
                'default' => ['url' => 'https://cdn.plyr.io/static/demo/View_From_A_Blue_Moon_Trailer-HD.jpg'],
            ]
        );

        $this->add_control(
            'autoplay',
            [
                'label' => __('Autoplay', 'my-core-plugin'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => 'Yes',
                'label_off' => 'No',
                'return_value' => 'true',
                'default' => '',
            ]
        );

        $this->add_control(
            'loop',
            [
                'label' => __('Loop', 'my-core-plugin'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => 'Yes',
                'label_off' => 'No',
                'return_value' => 'true',
                'default' => '',
            ]
        );

        $this->add_control(
            'mute',
            [
                'label' => __('Start Muted', 'my-core-plugin'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => 'Yes',
                'label_off' => 'No',
                'return_value' => 'true',
                'default' => '',
            ]
        );

        $this->end_controls_section();

        // Style Section
        $this->start_controls_section(
            'style_section',
            [
                'label' => __('Style', 'my-core-plugin'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'video_width',
            [
                'label' => __('Width', 'my-core-plugin'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px','%','vw'],
                'default' => ['unit' => '%','size' => 100],
                'selectors' => ['{{WRAPPER}} .my-video-wrapper' => 'width: {{SIZE}}{{UNIT}};'],
            ]
        );

        $this->add_responsive_control(
            'video_height',
            [
                'label' => __('Height', 'my-core-plugin'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px','%','vh'],
                'default' => ['unit' => 'vh','size' => 50],
                'selectors' => ['{{WRAPPER}} .my-video-wrapper' => 'height: {{SIZE}}{{UNIT}};'],
            ]
        );

        $this->add_responsive_control(
            'video_border_radius',
            [
                'label' => __('Border Radius', 'my-core-plugin'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px','%','em'],
                'selectors' => [
                    '{{WRAPPER}} .my-video-wrapper, {{WRAPPER}} .video-js' => 
                    'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow:hidden;'
                ],
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        $unique_id = 'my-video-' . $this->get_id(); // Unique per instance

        $video_url = esc_url($settings['video_url']);
        $poster_url = esc_url($settings['poster_url']['url'] ?? '');
        $autoplay = $settings['autoplay'] === 'true' ? 1 : 0;
        $loop = $settings['loop'] === 'true' ? 1 : 0;
        $mute = $settings['mute'] === 'true' ? 1 : 0;

        $is_editor = \Elementor\Plugin::$instance->editor->is_edit_mode();
        ?>

        <div class="my-video-wrapper" id="<?php echo esc_attr($unique_id); ?>-wrap" style="position: relative; width: 100%; height: 0; padding-bottom: 56.25%;">

            <?php if($is_editor): ?>
                <!-- Editor Placeholder -->
                <div style="position:absolute;top:0;left:0;width:100%;height:100%;overflow:hidden;border-radius:<?php echo esc_attr($settings['video_border_radius']['top'] ?? 0); ?><?php echo esc_attr($settings['video_border_radius']['unit'] ?? 'px'); ?> <?php echo esc_attr($settings['video_border_radius']['right'] ?? 0); ?><?php echo esc_attr($settings['video_border_radius']['unit'] ?? 'px'); ?> <?php echo esc_attr($settings['video_border_radius']['bottom'] ?? 0); ?><?php echo esc_attr($settings['video_border_radius']['unit'] ?? 'px'); ?> <?php echo esc_attr($settings['video_border_radius']['left'] ?? 0); ?><?php echo esc_attr($settings['video_border_radius']['unit'] ?? 'px'); ?>;">
                    <img src="<?php echo esc_url($poster_url); ?>" alt="Video Preview" style="width:100%;height:100%;object-fit:cover;">
                    <div style="position:absolute;top:50%;left:50%;transform:translate(-50%, -50%);width:100px;height:100px;background:rgba(0,0,0,0.15);border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:60px;color:#fff;backdrop-filter:blur(10px) saturate(1.2);">
                        ▶
                    </div>
                </div>
            <?php else: ?>
                <!-- Frontend Player -->
                <video id="<?php echo esc_attr($unique_id); ?>" class="video-js vjs-fluid" controls preload="auto" poster="<?php echo esc_url($poster_url); ?>" style="position:absolute;top:0;left:0;width:100%;height:100%;">
                    <source src="<?php echo esc_url($video_url); ?>" type="video/mp4">
                    Your browser does not support the video tag.
                </video>

                <link href="https://vjs.zencdn.net/7.20.3/video-js.css" rel="stylesheet" />
                <script src="https://vjs.zencdn.net/7.20.3/video.min.js"></script>

                <style>
                    .video-js { 
                        width: 100%; 
                        height: 100%; 
                        background: #000; 
                        position:absolute; 
                        top:0; 
                        left:0; 
                        border-radius: <?php echo esc_attr($settings['video_border_radius']['top'] ?? 0); ?><?php echo esc_attr($settings['video_border_radius']['unit'] ?? 'px'); ?> <?php echo esc_attr($settings['video_border_radius']['right'] ?? 0); ?><?php echo esc_attr($settings['video_border_radius']['unit'] ?? 'px'); ?> <?php echo esc_attr($settings['video_border_radius']['bottom'] ?? 0); ?><?php echo esc_attr($settings['video_border_radius']['unit'] ?? 'px'); ?> <?php echo esc_attr($settings['video_border_radius']['left'] ?? 0); ?><?php echo esc_attr($settings['video_border_radius']['unit'] ?? 'px'); ?>;
                        overflow:hidden;
                    }
                    .video-js .vjs-big-play-button {
                        font-size: 600%;
                        line-height: 100px;
                        height: 100px;
                        width: 100px;
                        position: absolute;
                        top: 50%;
                        left: 50%;
                        transform: translate(-50%, -50%);
                        background: rgba(0,0,0,0.15);
                        border-radius: 50%;
                        border: 0;
                        color: #fff;
                        cursor: pointer;
                        backdrop-filter: blur(10px) saturate(1.2);
                    }
                    .video-js .vjs-big-play-button:hover {
                        background: rgba(255,255,255,0.2);
                    }
                    .video-js .vjs-control-bar {
                        display: flex !important;
                        justify-content: center !important;
                        position: absolute;
                        bottom: 20px;
                        left: 50%;
                        transform: translateX(-50%);
                        width: auto !important;
                        min-width: 300px;
                        max-width: 700px;
                        border-radius: 5px;
                        background: rgba(0,0,0,0.6);
                        backdrop-filter: blur(20px) saturate(1.5) brightness(1.2);
                    }
                    .vjs-has-started.vjs-user-inactive.vjs-playing .vjs-control-bar {
                        visibility: hidden;
                        opacity: 0;
                        bottom: 5px;
                        transition: all 0.15s cubic-bezier(.44,.14,.34,.97);
                    }
                </style>

                <script>
                    document.addEventListener("DOMContentLoaded", function(){
                        var el = document.getElementById('<?php echo esc_js($unique_id); ?>');
                        if (el && typeof videojs !== 'undefined') {
                            videojs('<?php echo esc_js($unique_id); ?>', {
                                controls: true,
                                autoplay: <?php echo $autoplay ? 'true' : 'false'; ?>,
                                loop: <?php echo $loop ? 'true' : 'false'; ?>,
                                muted: <?php echo $mute ? 'true' : 'false'; ?>
                            });
                        }
                    });
                </script>
            <?php endif; ?>

        </div>
        <?php
    }
}

// Register the widget
\Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Video_Player());
