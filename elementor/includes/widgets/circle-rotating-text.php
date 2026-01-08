<?php
namespace My_Core_Plugin\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Icons_Manager;

if (!defined('ABSPATH')) exit;

class Circle_Rotating_Text extends Widget_Base {

    public function get_name() { return 'circle_rotating_text'; }
    public function get_title() { return 'Circle Rotating Text'; }
    public function get_icon() { return 'eicon-integration'; }
    public function get_categories() { return ['th-general']; }

    protected function register_controls() {

        // ===== CONTENT TAB =====
        $this->start_controls_section('content_section', [
            'label' => 'Content',
            'tab' => Controls_Manager::TAB_CONTENT,
        ]);

        $this->add_control('content_type', [
            'label' => __('Content Type', 'my-core-plugin'),
            'type' => Controls_Manager::SELECT,
            'options' => ['image'=>'Image','icon'=>'Icon'],
            'default' => 'image',
        ]);

        $this->add_control('character_image', [
            'label' => __('Center Image', 'my-core-plugin'),
            'type' => Controls_Manager::MEDIA,
            'condition' => ['content_type' => 'image'],
        ]);

        $this->add_control('character_icon', [
            'label' => __('Center Icon', 'my-core-plugin'),
            'type' => Controls_Manager::ICONS,
            'condition' => ['content_type' => 'icon'],
        ]);

        $this->add_control('center_size', [
            'label' => __('Center Size', 'my-core-plugin'),
            'type' => Controls_Manager::SLIDER,
            'default' => ['size'=>180,'unit'=>'px'],
            'range' => ['px'=>['min'=>50,'max'=>500]],
        ]);

        $this->add_control('rotating_text', [
            'label'=>'Rotating Text',
            'type'=>Controls_Manager::TEXTAREA,
            'default'=>'ROTATING TEXT AROUND IMAGE',
        ]);

        $this->add_control('direction', [
            'label'=>'Direction',
            'type'=>Controls_Manager::SELECT,
            'options'=>['normal'=>'Clockwise','reverse'=>'Counter Clockwise'],
            'default'=>'normal',
        ]);

        $this->add_control('animation_speed', [
            'label'=>'Animation Speed (sec)',
            'type'=>Controls_Manager::NUMBER,
            'default'=>10,
        ]);

        $this->add_control('hover_effect', [
            'label'=>'Hover Effect',
            'type'=>Controls_Manager::SELECT,
            'default'=>'Scale',
            'options'=>['none'=>'None','scale'=>'Scale','rotate'=>'Rotate Faster'],
        ]);

        $this->add_control('hover_speed', [
            'label'=>'Hover Speed (sec)',
            'type'=>Controls_Manager::NUMBER,
            'default'=>2,
        ]);

        $this->end_controls_section();

        // ===== STYLE TAB =====
        // Background
        $this->start_controls_section('background_style_section', [
            'label' => 'Background Style',
            'tab' => Controls_Manager::TAB_STYLE,
        ]);

        $this->add_control('bg_color', [
            'label' => __('Background Color','my-core-plugin'),
            'type'=>Controls_Manager::COLOR,
            'selectors'=>['{{WRAPPER}} .crt-wrapper'=>'background-color: {{VALUE}};'],
        ]);

        $this->add_group_control(Group_Control_Border::get_type(), [
            'name'=>'bg_border',
            'label'=>__('Border','my-core-plugin'),
            'selector'=>'{{WRAPPER}} .crt-wrapper',
        ]);

        $this->add_control('bg_radius', [
            'label'=>__('Border Radius','my-core-plugin'),
            'type'=>Controls_Manager::SLIDER,
            'range'=>['px'=>['min'=>0,'max'=>100]],
            'selectors'=>['{{WRAPPER}} .crt-wrapper'=>'border-radius: {{SIZE}}{{UNIT}};'],
        ]);

        $this->add_responsive_control('bg_padding', [
            'label'=>__('Padding','my-core-plugin'),
            'type'=>Controls_Manager::DIMENSIONS,
            'selectors'=>['{{WRAPPER}} .crt-wrapper'=>'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'],
        ]);

        $this->add_group_control(Group_Control_Box_Shadow::get_type(), [
            'name'=>'bg_shadow',
            'selector'=>'{{WRAPPER}} .crt-wrapper',
        ]);

        $this->end_controls_section();

        // Image/Icon style
        $this->start_controls_section('center_style_section', [
            'label'=>'Image / Icon Style',
            'tab'=>Controls_Manager::TAB_STYLE,
        ]);

        $this->add_group_control(Group_Control_Border::get_type(), [
            'name'=>'center_border',
            'selector'=>'{{WRAPPER}} .crt-center-image, {{WRAPPER}} .crt-center-icon',
        ]);

        $this->add_control('center_radius', [
            'label'=>'Border Radius',
            'type'=>Controls_Manager::SLIDER,
            'range'=>['px'=>['min'=>0,'max'=>100]],
            'selectors'=>['{{WRAPPER}} .crt-center-image, {{WRAPPER}} .crt-center-icon'=>'border-radius: {{SIZE}}{{UNIT}};'],
        ]);

        $this->add_group_control(Group_Control_Box_Shadow::get_type(), [
            'name'=>'center_shadow',
            'selector'=>'{{WRAPPER}} .crt-center-image, {{WRAPPER}} .crt-center-icon',
        ]);

        // Icon specific color
        $this->add_control('icon_color', [
            'label'=>'Icon Color',
            'type'=>Controls_Manager::COLOR,
            'default'=>'#9d79ff',
            'condition'=>['content_type'=>'icon'],
            'selectors'=>['{{WRAPPER}} .crt-center-icon'=>'color: {{VALUE}};'],
        ]);

        $this->end_controls_section();

        // Text style
        $this->start_controls_section('text_style_section', [
            'label'=>'Text Style',
            'tab'=>Controls_Manager::TAB_STYLE,
        ]);

        $this->add_control('circle_radius',[
            'label'=>'Circle Radius',
            'type'=>Controls_Manager::SLIDER,
            'default'=>['size'=>100,'unit'=>'px'],
            'range'=>['px'=>['min'=>50,'max'=>500]],
        ]);

        $this->add_control('text_image_gap',[
            'label'=>'Text Gap from Center',
            'type'=>Controls_Manager::NUMBER,
            'default'=>20,
            'min'=>0,'max'=>200,
        ]);

        $this->add_group_control(Group_Control_Typography::get_type(), [
            'name'=>'text_typography',
            'selector'=>'{{WRAPPER}} svg text',
        ]);

        $this->add_control('text_color',[
            'label'=>'Text Color',
            'type'=>Controls_Manager::COLOR,
            'selectors'=>['{{WRAPPER}} svg text'=>'fill: {{VALUE}};'],
        ]);

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        $is_editor = \Elementor\Plugin::$instance->editor->is_edit_mode();

        $content_type = $settings['content_type'] ?? 'image';
        $image_url = $settings['character_image']['url'] ?? '';
        $icon_data = $settings['character_icon'] ?? '';
        $center_size = $settings['center_size']['size'] ?? 180;
        $icon_color = $settings['icon_color'] ?? '#9d79ff';
        $text = $settings['rotating_text'] ?? '';
        $radius = $settings['circle_radius']['size'] ?? 100;
        $gap = $settings['text_image_gap'] ?? 20;
        $effective_radius = $radius + $gap;
        $direction = $settings['direction'] ?? 'normal';
        $speed = $settings['animation_speed'] ?? 10;
        $hover_effect = $settings['hover_effect'] ?? 'none';
        $hover_speed = $settings['hover_speed'] ?? 2;
        $text_color = $settings['text_color'] ?? '#9d79ff';

        $viewBoxSize = $effective_radius*2 + 50;
        ?>
        <div class="crt-wrapper" style="position: relative; width: <?php echo $viewBoxSize; ?>px; height: <?php echo $viewBoxSize; ?>px; overflow:hidden;">
            <?php if($content_type==='image' && $image_url): ?>
                <img src="<?php echo esc_url($image_url); ?>" class="crt-center-image"
                     style="width:<?php echo esc_attr($center_size); ?>px; height:<?php echo esc_attr($center_size); ?>px; position:absolute; top:50%; left:50%; transform:translate(-50%,-50%); z-index:1;">
            <?php elseif($content_type==='icon' && !empty($icon_data)): ?>
                <?php Icons_Manager::render_icon($icon_data, [
                    'aria-hidden'=>'true',
                    'class'=>'crt-center-icon',
                    'style'=>'position:absolute; top:50%; left:50%; transform:translate(-50%,-50%);
                             font-size:'.esc_attr($center_size).'px; color:'.esc_attr($icon_color).'; z-index:1; transition: transform 0.4s ease;'
                ]); ?>
            <?php endif; ?>

            <svg width="<?php echo $viewBoxSize; ?>" height="<?php echo $viewBoxSize; ?>" viewBox="0 0 <?php echo $viewBoxSize; ?> <?php echo $viewBoxSize; ?>" style="position:absolute; top:0; left:0; z-index:2;">
                <defs>
                    <path id="circlePath" d="M<?php echo $viewBoxSize/2; ?>,<?php echo $viewBoxSize/2; ?> 
                        m-<?php echo $effective_radius; ?>,0 
                        a<?php echo $effective_radius; ?>,<?php echo $effective_radius; ?> 0 1,1 <?php echo $effective_radius*2; ?>,0 
                        a<?php echo $effective_radius; ?>,<?php echo $effective_radius; ?> 0 1,1 -<?php echo $effective_radius*2; ?>,0"/>
                </defs>
                <text fill="<?php echo esc_attr($text_color); ?>" text-anchor="middle">
                    <textPath xlink:href="#circlePath" startOffset="50%"><?php echo esc_html($text); ?></textPath>
                </text>
            </svg>
        </div>

        <script>
        (function(){
            const wrapper = document.currentScript.parentElement;
            const svg = wrapper.querySelector('svg');
            const contentEl = wrapper.querySelector('.crt-center-image, .crt-center-icon');
            const direction = '<?php echo $direction==='reverse' ? -1 : 1; ?>';
            const speed = <?php echo $speed; ?>;
            const hoverEffect = '<?php echo $hover_effect; ?>';
            const hoverSpeed = <?php echo $hover_speed; ?>;
            const isEditor = <?php echo $is_editor?'true':'false'; ?>;

            if(typeof gsap === 'undefined'){
                const gsapScript = document.createElement('script');
                gsapScript.src = 'https://cdnjs.cloudflare.com/ajax/libs/gsap/3.14.1/gsap.min.js';
                gsapScript.onload = initRotation;
                document.head.appendChild(gsapScript);
            } else initRotation();

            function initRotation(){
                let rotationDuration = isEditor ? 5 : speed;
                let rotation = gsap.to(svg, {
                    rotation: 360 * direction,
                    transformOrigin: '50% 50%',
                    duration: rotationDuration,
                    repeat: -1,
                    ease: 'linear'
                });

                // Editor guide circle
                if(isEditor){
                    const guideCircle = document.createElement('div');
                    guideCircle.style.width = svg.clientWidth + 'px';
                    guideCircle.style.height = svg.clientHeight + 'px';
                    guideCircle.style.border = '1px dashed #999';
                    guideCircle.style.borderRadius = '50%';
                    guideCircle.style.position = 'absolute';
                    guideCircle.style.top = '0';
                    guideCircle.style.left = '0';
                    wrapper.appendChild(guideCircle);
                }

                wrapper.addEventListener('mouseenter', ()=>{
                    if(hoverEffect==='scale') gsap.to([contentEl,svg], {scale:1.05, duration:0.4, ease:'power1.out'});
                    else if(hoverEffect==='rotate') rotation.timeScale(speed/hoverSpeed);
                });
                wrapper.addEventListener('mouseleave', ()=>{
                    if(hoverEffect==='scale') gsap.to([contentEl,svg], {scale:1, duration:0.4, ease:'power1.inOut'});
                    else if(hoverEffect==='rotate') rotation.timeScale(1);
                });
            }
        })();
        </script>

        <style>
        .crt-wrapper svg text { font-family: Arial, sans-serif; font-size:16px; }
        </style>
        <?php
    }
}
// Register Widget
\Elementor\Plugin::instance()->widgets_manager->register(new \My_Core_Plugin\Widgets\Circle_Rotating_Text());
