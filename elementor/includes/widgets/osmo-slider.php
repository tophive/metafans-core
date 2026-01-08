<?php
// version - 1.1
namespace My_Core_Plugin\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Group_Control_Typography;

if (!defined('ABSPATH')) exit;

class OSMO_Slider extends Widget_Base {

    public function get_name() { return 'osmo_slider'; }
    public function get_title() { return __('OSMO Slider', 'my-core-plugin'); }
    public function get_icon() { return 'eicon-slider-full-screen'; }
    public function get_categories() { return ['th-general']; }
    public function get_keywords() { return ['slider', 'carousel', 'hero', 'osmo']; }

    public function __construct($data = [], $args = null) {
        parent::__construct($data, $args);
        wp_register_style('osmo-slider', TH_ELEMENTOR_URL . 'assets/css/elements.css', [], '1.1');
    }

    public function get_script_depends() {
        return ['tophive-elementor-bundle'];
    }
    public function get_style_depends() {
        return ['osmo-slider'];
    }

    protected function register_controls() {
        // Slides
        $this->start_controls_section('content_section', [
            'label' => __('Slides', 'my-core-plugin'),
            'tab'   => Controls_Manager::TAB_CONTENT,
        ]);

        $repeater = new Repeater();

        $repeater->add_control('slide_bg_type', [
            'label'   => __('Background Type', 'my-core-plugin'),
            'type'    => Controls_Manager::SELECT,
            'options' => ['image'=>'Image','video'=>'Video'],
            'default' => 'image',
        ]);
        $repeater->add_control('slide_image', [
            'label'   => __('Image', 'my-core-plugin'),
            'type'    => Controls_Manager::MEDIA,
            'default' => ['url'=>\Elementor\Utils::get_placeholder_image_src()],
            'condition'=>['slide_bg_type'=>'image'],
        ]);
        $repeater->add_control('slide_video', [
            'label'       => __('Video URL', 'my-core-plugin'),
            'type'        => Controls_Manager::URL,
            'placeholder' => 'https://your-video.mp4',
            'condition'   => ['slide_bg_type'=>'video'],
        ]);

        $repeater->add_control('slide_overlay_color', [
            'label'=>__('Overlay Color','my-core-plugin'),
            'type'=>Controls_Manager::COLOR,
            'default'=>'#000000',
        ]);
        $repeater->add_control('slide_overlay_opacity', [
            'label'=>__('Overlay Opacity','my-core-plugin'),
            'type'=>Controls_Manager::SLIDER,
            'size_units'=>[''],
            'range'=>[''=>['min'=>0,'max'=>1,'step'=>0.05]],
            'default'=>['size'=>0.4],
        ]);

        $repeater->add_control('slide_title', [
            'label'=>__('Title','my-core-plugin'),
            'type'=>Controls_Manager::TEXT,
            'default'=>'Slide Title',
            'label_block'=>true,
        ]);
        $repeater->add_control('slide_text', [
            'label'=>__('Description','my-core-plugin'),
            'type'=>Controls_Manager::TEXTAREA,
            'default'=>'Slide description goes here.',
            'rows'=>3,
        ]);

        $repeater->add_control('slide_button', [
            'label'=>__('Button Text','my-core-plugin'),
            'type'=>Controls_Manager::TEXT,
            'default'=>'Learn More',
        ]);
        $repeater->add_control('slide_link', [
            'label'=>__('Button Link','my-core-plugin'),
            'type'=>Controls_Manager::URL,
            'placeholder'=>'https://your-link.com',
        ]);

        $repeater->add_control('slide_text_position', [
            'label' => __('Text Vertical Position','my-core-plugin'),
            'type'=>Controls_Manager::SELECT,
            'options'=>['top'=>'Top','center'=>'Center','bottom'=>'Bottom'],
            'default'=>'center',
        ]);
        $repeater->add_control('slide_text_align', [
            'label' => __('Text Alignment','my-core-plugin'),
            'type'=>Controls_Manager::SELECT,
            'options'=>['left'=>'Left','center'=>'Center','right'=>'Right'],
            'default'=>'left',
        ]);

        // Button style
        $repeater->add_control('slide_button_color', [
            'label'=>__('Button Text Color','my-core-plugin'),
            'type'=>Controls_Manager::COLOR,
            'default'=>'#000000',
        ]);
        $repeater->add_control('slide_button_bg', [
            'label'=>__('Button Background Color','my-core-plugin'),
            'type'=>Controls_Manager::COLOR,
            'default'=>'#ffffff',
        ]);
        $repeater->add_control('slide_button_radius', [
            'label'=>__('Button Border Radius','my-core-plugin'),
            'type'=>Controls_Manager::SLIDER,
            'size_units'=>['px','%'],
            'range'=>['px'=>['min'=>0,'max'=>50],'%'=>['min'=>0,'max'=>50]],
            'default'=>['unit'=>'px','size'=>8],
        ]);
        $repeater->add_control('slide_button_hover_color', [
            'label'=>__('Button Hover Text Color','my-core-plugin'),
            'type'=>Controls_Manager::COLOR,
            'default'=>'#ffffff',
        ]);
        $repeater->add_control('slide_button_hover_bg', [
            'label'=>__('Button Hover Background Color','my-core-plugin'),
            'type'=>Controls_Manager::COLOR,
            'default'=>'#9d79ff',
        ]);

        // Image pan & zoom
        $repeater->add_control('slide_image_zoom', [
            'label'=>__('Image Zoom Effect','my-core-plugin'),
            'type'=>Controls_Manager::SELECT,
            'options'=>['none'=>'None','zoom_in'=>'Zoom In','zoom_out'=>'Zoom Out','pan'=>'Pan'],
            'default'=>'none',
        ]);

        $this->add_control('slides', [
            'label'=>__('Slides','my-core-plugin'),
            'type'=>Controls_Manager::REPEATER,
            'fields'=>$repeater->get_controls(),
            'title_field'=>'{{{ slide_title }}}',
        ]);
        $this->end_controls_section();

        // Settings
        $this->start_controls_section('slider_settings', [
            'label'=>__('Slider Settings','my-core-plugin'),
            'tab'=>Controls_Manager::TAB_CONTENT,
        ]);
        $this->add_control('autoplay', [
            'label'=>__('Autoplay','my-core-plugin'),
            'type'=>Controls_Manager::SWITCHER,
            'default'=>'yes',
        ]);
        $this->add_control('autoplay_speed', [
            'label'=>__('Autoplay Speed (ms)','my-core-plugin'),
            'type'=>Controls_Manager::NUMBER,
            'default'=>4000,
            'condition'=>['autoplay'=>'yes'],
        ]);
        $this->add_control('loop', [
            'label'=>__('Loop','my-core-plugin'),
            'type'=>Controls_Manager::SWITCHER,
            'default'=>'yes',
        ]);
        $this->add_control('pause_on_hover', [
            'label'=>__('Pause on Hover','my-core-plugin'),
            'type'=>Controls_Manager::SWITCHER,
            'default'=>'yes',
        ]);
        $this->add_control('keyboard_nav', [
            'label'=>__('Keyboard Navigation','my-core-plugin'),
            'type'=>Controls_Manager::SWITCHER,
            'default'=>'yes',
        ]);
        $this->add_control('transition_mode', [
            'label' => __('Transition Mode','my-core-plugin'),
            'type'  => Controls_Manager::SELECT,
            'options' => ['slide'=>__('Slide','my-core-plugin'),'fade'=>__('Fade','my-core-plugin')],
            'default' => 'slide',
        ]);
        $this->add_control('show_dots', [
            'label'=>__('Show Dot Pagination','my-core-plugin'),
            'type'=>Controls_Manager::SWITCHER,
            'default'=>'yes',
        ]);
        $this->add_control('slider_height', [
            'label'=>__('Slider Height','my-core-plugin'),
            'type'=>Controls_Manager::SLIDER,
            'size_units'=>['px','vh'],
            'range'=>['px'=>['min'=>300,'max'=>1200],'vh'=>['min'=>30,'max'=>100]],
            'default'=>['unit'=>'px','size'=>600],
        ]);
        $this->add_control('slider_border_radius', [
            'label'=>__('Slider Border Radius','my-core-plugin'),
            'type'=>Controls_Manager::SLIDER,
            'size_units'=>['px','%'],
            'range'=>['px'=>['min'=>0,'max'=>100],'%'=>['min'=>0,'max'=>50]],
            'default'=>['unit'=>'px','size'=>0],
        ]);
        $this->end_controls_section();

        // Typography
        $this->start_controls_section('typography_section', [
            'label'=>__('Typography','my-core-plugin'),
            'tab'=>Controls_Manager::TAB_STYLE,
        ]);
        $this->add_group_control(Group_Control_Typography::get_type(),[
            'name'=>'slide_title_typography',
            'label'=>__('Title Typography','my-core-plugin'),
            'selector'=>'{{WRAPPER}} .osmo-slide .slide-caption h2',
        ]);
        $this->add_control('slide_title_color',[
            'label'=>__('Title Color','my-core-plugin'),
            'type'=>Controls_Manager::COLOR,
            'default'=>'#ffffff',
            'selectors'=>['{{WRAPPER}} .osmo-slide .slide-caption h2'=>'color: {{VALUE}};'],
        ]);
        $this->add_group_control(Group_Control_Typography::get_type(),[
            'name'=>'slide_desc_typography',
            'label'=>__('Description Typography','my-core-plugin'),
            'selector'=>'{{WRAPPER}} .osmo-slide .slide-caption p',
        ]);
        $this->add_control('slide_desc_color',[
            'label'=>__('Description Color','my-core-plugin'),
            'type'=>Controls_Manager::COLOR,
            'default'=>'#ffffff',
            'selectors'=>['{{WRAPPER}} .osmo-slide .slide-caption p'=>'color: {{VALUE}};'],
        ]);
        $this->end_controls_section();
    }

    protected function render() {
        $s = $this->get_settings_for_display();
        if (empty($s['slides'])) return;

        $slider_id = 'osmo-slider-' . esc_attr($this->get_id());
        $br = isset($s['slider_border_radius']['size'], $s['slider_border_radius']['unit'])
            ? $s['slider_border_radius']['size'] . $s['slider_border_radius']['unit'] : '0px';
        $height = isset($s['slider_height']['size'], $s['slider_height']['unit'])
            ? $s['slider_height']['size'] . $s['slider_height']['unit'] : '600px';

        $autoplay       = ($s['autoplay'] === 'yes');
        $autoplay_speed = (int)($s['autoplay_speed'] ?? 4000);
        $loop           = ($s['loop'] === 'yes');
        $pause_on_hover = ($s['pause_on_hover'] === 'yes');
        $keyboard_nav   = ($s['keyboard_nav'] === 'yes');
        $mode           = $s['transition_mode'] ?? 'slide';
        $show_dots      = (!empty($s['show_dots']) && $s['show_dots']==='yes');

        $total = count($s['slides']);
        ?>

        <div class="osmo-slider" id="<?php echo $slider_id; ?>"
             data-autoplay="<?php echo $autoplay ? '1':'0'; ?>"
             data-speed="<?php echo esc_attr($autoplay_speed); ?>"
             data-loop="<?php echo $loop ? '1':'0'; ?>"
             data-pause-hover="<?php echo $pause_on_hover ? '1':'0'; ?>"
             data-keyboard="<?php echo $keyboard_nav ? '1':'0'; ?>"
             data-mode="<?php echo esc_attr($mode); ?>"
             data-dots="<?php echo $show_dots ? '1' : '0'; ?>"
             dir="<?php echo is_rtl() ? 'rtl' : 'ltr'; ?>"
             style="border-radius: <?php echo esc_attr($br); ?>; overflow:hidden;">

            <div class="osmo-slider-viewport" style="height: <?php echo esc_attr($height); ?>;">
                <div class="osmo-slider-list" data-slider="list" aria-live="polite">
                    <?php foreach($s['slides'] as $i => $slide):
                        $is_video = ($slide['slide_bg_type']==='video' && !empty($slide['slide_video']['url']));
                        $overlay_hex = !empty($slide['slide_overlay_color']) ? $slide['slide_overlay_color'] : '#000000';
                        $overlay_opacity = isset($slide['slide_overlay_opacity']['size']) ? floatval($slide['slide_overlay_opacity']['size']) : 0.4;
                        $rgba = self::hex_to_rgba($overlay_hex, $overlay_opacity);
                        $vpos = $slide['slide_text_position'] ?? 'center';
                        $talign = $slide['slide_text_align'] ?? 'left';

                        $btn_radius_val = '8px';
                        if (!empty($slide['slide_button_radius']['size']) && !empty($slide['slide_button_radius']['unit'])) {
                            $btn_radius_val = $slide['slide_button_radius']['size'].$slide['slide_button_radius']['unit'];
                        }
                    ?>
                    <div data-slider="slide" class="osmo-slide" role="group" aria-roledescription="slide" aria-label="<?php echo esc_attr(($i+1).'/'.$total); ?>">
                        <div class="slide-bg" aria-hidden="true">
                            <?php if($is_video): ?>
                                <video autoplay muted loop playsinline src="<?php echo esc_url($slide['slide_video']['url']); ?>"></video>
                            <?php else: ?>
                                <img src="<?php echo esc_url($slide['slide_image']['url'] ?? \Elementor\Utils::get_placeholder_image_src()); ?>"
                                     alt=""
                                     class="slide-image"
                                     data-zoom="<?php echo esc_attr($slide['slide_image_zoom'] ?? 'none'); ?>">
                            <?php endif; ?>
                            <div class="slide-overlay" style="background: <?php echo esc_attr($rgba); ?>"></div>
                        </div>

                        <div class="slide-inner"
                             style="
                                justify-content: <?php echo esc_attr($vpos==='top'?'flex-start':($vpos==='bottom'?'flex-end':'center')); ?>;
                                text-align: <?php echo esc_attr($talign); ?>;
                             ">
                            <div class="slide-caption">
                                <?php if(!empty($slide['slide_title'])): ?>
                                    <h2><?php echo esc_html($slide['slide_title']); ?></h2>
                                <?php endif; ?>
                                <?php if(!empty($slide['slide_text'])): ?>
                                    <p><?php echo esc_html($slide['slide_text']); ?></p>
                                <?php endif; ?>

                                <?php if(!empty($slide['slide_button'])):
                                    $link = $slide['slide_link']['url'] ?? '';
                                    $btn_color = $slide['slide_button_color'] ?? '#000000';
                                    $btn_bg = $slide['slide_button_bg'] ?? '#ffffff';
                                    $btn_hover_color = $slide['slide_button_hover_color'] ?? '#ffffff';
                                    $btn_hover_bg = $slide['slide_button_hover_bg'] ?? '#9d79ff';
                                ?>
                                    <a class="osmo-btn"
                                       href="<?php echo esc_url($link); ?>"
                                       style="color: <?php echo esc_attr($btn_color); ?>;
                                              background-color: <?php echo esc_attr($btn_bg); ?>;
                                              border-radius: <?php echo esc_attr($btn_radius_val); ?>;"
                                       data-hover-color="<?php echo esc_attr($btn_hover_color); ?>"
                                       data-hover-bg="<?php echo esc_attr($btn_hover_bg); ?>">
                                        <?php echo esc_html($slide['slide_button']); ?>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="osmo-slider-nav" aria-controls="<?php echo esc_attr($slider_id); ?>">
                <button type="button" data-slider="prev" class="prev" aria-label="<?php esc_attr_e('Previous slide','my-core-plugin'); ?>">←</button>
                <div class="counter"><span class="current">1</span>/<span class="total"><?php echo (int)$total; ?></span></div>
                <button type="button" data-slider="next" class="next" aria-label="<?php esc_attr_e('Next slide','my-core-plugin'); ?>">→</button>
            </div>

            <?php if ($show_dots) : ?>
                <div class="osmo-slider-dots" role="tablist" aria-label="<?php esc_attr_e('Slide pagination', 'my-core-plugin'); ?>"></div>
            <?php endif; ?>
        </div>
        <?php
    }

    private static function hex_to_rgba($hex, $alpha = 1){
        $hex = str_replace('#','',$hex);
        if(strlen($hex)===3){
            $r = hexdec(str_repeat(substr($hex,0,1),2));
            $g = hexdec(str_repeat(substr($hex,1,1),2));
            $b = hexdec(str_repeat(substr($hex,2,1),2));
        } else {
            $r = hexdec(substr($hex,0,2));
            $g = hexdec(substr($hex,2,2));
            $b = hexdec(substr($hex,4,2));
        }
        $alpha = max(0, min(1, floatval($alpha)));
        return "rgba($r, $g, $b, $alpha)";
    }
}

// Register widget
\Elementor\Plugin::instance()->widgets_manager->register(new OSMO_Slider());
