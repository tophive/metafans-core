<?php
/**
 * Plugin Name: Modern GSAP Slider (Elementor Widget)
 * Description: Smooth GSAP-powered slider with global image and text effects, text position, alignment, gap control, nav, image fit & position, dots, and full style options.
 * Version: 1.0.0
 * Author: Tophive
 */

namespace My_Core_Plugin\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;

if (!defined('ABSPATH')) exit;

class Modern_Slider extends Widget_Base {

    public function get_name() { return 'modern_slider'; }
    public function get_title() { return __('Modern Slider', 'my-core-plugin'); }
    public function get_icon() { return 'eicon-slider-full-screen'; }
    public function get_categories() { return ['th-general']; }

    public function get_script_depends() {
        return ['gsap', 'jquery'];
    }

    protected function register_controls() {

        // ---------------- Slides ----------------
        $this->start_controls_section('slides_section', ['label' => __('Slides', 'my-core-plugin'), 'tab' => Controls_Manager::TAB_CONTENT]);
        $repeater = new \Elementor\Repeater();

        $repeater->add_control('title', ['label' => __('Title','my-core-plugin'),'type'=>Controls_Manager::TEXT,'default'=>'Slide Title']);
        $repeater->add_control('description', ['label'=>__('Description','my-core-plugin'),'type'=>Controls_Manager::TEXTAREA,'default'=>'Slide description goes here']);
        $repeater->add_control('button_text',['label'=>__('Button Text','my-core-plugin'),'type'=>Controls_Manager::TEXT,'default'=>'Learn More']);
        $repeater->add_control('button_url',['label'=>__('Button URL','my-core-plugin'),'type'=>Controls_Manager::URL,'default'=>['url'=>'#']]);
        $repeater->add_control('image',['label'=>__('Background Image','my-core-plugin'),'type'=>Controls_Manager::MEDIA,'default'=>['url'=>\Elementor\Utils::get_placeholder_image_src()]]);

        $this->add_control('slides',['label'=>__('Slides','my-core-plugin'),'type'=>Controls_Manager::REPEATER,'fields'=>$repeater->get_controls(),'title_field'=>'{{{ title }}}']);
        $this->end_controls_section();

        // ---------------- Settings ----------------
        $this->start_controls_section('settings_section',['label'=>__('Settings','my-core-plugin'),'tab'=>Controls_Manager::TAB_CONTENT]);
        $this->add_control('autoplay',['label'=>__('Autoplay','my-core-plugin'),'type'=>Controls_Manager::SWITCHER,'default'=>'yes']);
        $this->add_control('autoplay_speed',['label'=>__('Autoplay Speed (ms)','my-core-plugin'),'type'=>Controls_Manager::NUMBER,'default'=>5000,'condition'=>['autoplay'=>'yes']]);
        $this->add_control('pause_on_hover',['label'=>__('Pause on Hover','my-core-plugin'),'type'=>Controls_Manager::SWITCHER,'default'=>'yes']);
        $this->add_control('nav_show',['label'=>__('Show Navigation','my-core-plugin'),'type'=>Controls_Manager::SWITCHER,'default'=>'yes']);
        $this->add_control('dots_show',['label'=>__('Show Dots','my-core-plugin'),'type'=>Controls_Manager::SWITCHER,'default'=>'yes']);
        $this->add_control('text_vertical_position',['label'=>__('Text Vertical Position','my-core-plugin'),'type'=>Controls_Manager::SELECT,'default'=>'bottom','options'=>['top'=>'Top','middle'=>'Middle','bottom'=>'Bottom']]);
        $this->add_control('text_horizontal_position',['label'=>__('Text Horizontal Position','my-core-plugin'),'type'=>Controls_Manager::CHOOSE,'options'=>[
            'left'=>['title'=>'Left','icon'=>'eicon-text-align-left'],
            'center'=>['title'=>'Center','icon'=>'eicon-text-align-center'],
            'right'=>['title'=>'Right','icon'=>'eicon-text-align-right']
        ],'default'=>'left','toggle'=>true]);
        $this->add_control('text_gap', [
            'label' => __('Text Gap','my-core-plugin'),
            'type' => Controls_Manager::SLIDER,
            'size_units' => ['px','em','%'],
            'range' => [
                'px'=>['min'=>0,'max'=>100],
                'em'=>['min'=>0,'max'=>10],
                '%' => ['min'=>0,'max'=>50]
            ],
            'default'=>['unit'=>'px','size'=>15],
            'selectors'=>[
                '{{WRAPPER}} .modern-slide-content h2' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                '{{WRAPPER}} .modern-slide-content p' => 'margin-bottom: {{SIZE}}{{UNIT}};'
            ]
        ]);
        $this->add_control('image_fit', [
            'label' => __('Image Fit','my-core-plugin'),
            'type' => Controls_Manager::SELECT,
            'default' => 'cover',
            'options' => [
                'cover' => 'Cover',
                'contain' => 'Contain',
                'fill' => 'Fill',
                'none' => 'None',
                'scale-down' => 'Scale Down'
            ]
        ]);
        $this->add_control('image_position', [
            'label' => __('Image Position','my-core-plugin'),
            'type' => Controls_Manager::SELECT,
            'default' => 'center center',
            'options' => [
                'top left'=>'Top Left',
                'top center'=>'Top Center',
                'top right'=>'Top Right',
                'center left'=>'Center Left',
                'center center'=>'Center Center',
                'center right'=>'Center Right',
                'bottom left'=>'Bottom Left',
                'bottom center'=>'Bottom Center',
                'bottom right'=>'Bottom Right'
            ]
        ]);
        $this->end_controls_section();

        // ---------------- Effect Control Panel ----------------
        $this->start_controls_section('effect_control_panel', [
            'label' => __('Effect Control Panel', 'my-core-plugin'),
            'tab' => Controls_Manager::TAB_CONTENT,
        ]);
        $this->add_control('slider_effect', [
            'label' => __('Image Effect','my-core-plugin'),
            'type' => Controls_Manager::SELECT,
            'default' => 'fade',
            'options' => [
                'fade' => 'Fade',
                'slide' => 'Slide Left/Right',
                'zoom' => 'Zoom In',
                'flip' => 'Flip',
                'scale' => 'Scale & Fade'
            ],
            'frontend_available' => true,
        ]);
        $this->add_control('text_effect', [
            'label' => __('Text Effect','my-core-plugin'),
            'type' => Controls_Manager::SELECT,
            'default' => 'fade_up',
            'options' => [
                'fade_up' => 'Fade Up',
                'slide_left' => 'Slide Left',
                'zoom_in' => 'Zoom In'
            ],
            'frontend_available' => true,
        ]);
        $this->end_controls_section();

        // ---------------- Style Controls ----------------
        $this->start_controls_section('slider_style',['label'=>__('Slider','my-core-plugin'),'tab'=>Controls_Manager::TAB_STYLE]);
        $this->add_responsive_control('slider_height',[
            'label' => __('Slider Height','my-core-plugin'),
            'type' => Controls_Manager::SLIDER,
            'size_units'=>['px','vh','%'],
            'range'=>['px'=>['min'=>100,'max'=>2000],'vh'=>['min'=>10,'max'=>100],'%'=>['min'=>10,'max'=>100]],
            'default'=>['unit'=>'vh','size'=>80],
            'selectors'=>['{{WRAPPER}} .modern-slider-wrapper'=>'height: {{SIZE}}{{UNIT}};']
        ]);
        $this->add_group_control(Group_Control_Border::get_type(), ['name'=>'slider_border','selector'=>'{{WRAPPER}} .modern-slider-wrapper']);
        $this->add_control('slider_radius',['label'=>'Border Radius','type'=>Controls_Manager::SLIDER,'size_units'=>['px','%'],'range'=>['px'=>['min'=>0,'max'=>200],'%'=>['min'=>0,'max'=>50]],'selectors'=>['{{WRAPPER}} .modern-slider-wrapper'=>'border-radius: {{SIZE}}{{UNIT}}; overflow:hidden;']]);
        $this->end_controls_section();

        // ---------------- Text Style ----------------
        $this->start_controls_section('text_style',['label'=>__('Text Style','my-core-plugin'),'tab'=>Controls_Manager::TAB_STYLE]);

        // Title
        $this->add_control('title_color',['label'=>'Title Color','type'=>Controls_Manager::COLOR,'selectors'=>['{{WRAPPER}} .modern-slide-content h2'=>'color:{{VALUE}};']]);
        $this->add_group_control(Group_Control_Typography::get_type(), ['name'=>'title_typography','selector'=>'{{WRAPPER}} .modern-slide-content h2']);
        $this->add_group_control(\Elementor\Group_Control_Text_Shadow::get_type(), ['name'=>'title_text_shadow','label'=>__('Title Text Shadow','my-core-plugin'),'selector'=>'{{WRAPPER}} .modern-slide-content h2']);
        
        // Description
        $this->add_control('desc_color',['label'=>'Description Color','type'=>Controls_Manager::COLOR,'selectors'=>['{{WRAPPER}} .modern-slide-content p'=>'color:{{VALUE}};']]);
        $this->add_group_control(Group_Control_Typography::get_type(), ['name'=>'desc_typography','selector'=>'{{WRAPPER}} .modern-slide-content p']);
        $this->add_group_control(\Elementor\Group_Control_Text_Shadow::get_type(), ['name'=>'desc_text_shadow','label'=>__('Description Text Shadow','my-core-plugin'),'selector'=>'{{WRAPPER}} .modern-slide-content p']);
        
        // Gap between title & description
        $this->add_control('title_desc_gap', [
            'label' => __('Title & Description Gap','my-core-plugin'),
            'type' => Controls_Manager::SLIDER,
            'size_units' => ['px','em','%'],
            'range' => [
                'px' => ['min'=>0,'max'=>100],
                'em' => ['min'=>0,'max'=>10],
                '%'  => ['min'=>0,'max'=>50]
            ],
            'default' => ['unit'=>'px','size'=>10],
            'selectors' => [
                '{{WRAPPER}} .modern-slide-content h2' => 'margin-bottom: {{SIZE}}{{UNIT}};',
            ],
        ]);
        $this->end_controls_section();

        // ---------------- Button Style ----------------
        $this->start_controls_section('button_style',['label'=>__('Button','my-core-plugin'),'tab'=>Controls_Manager::TAB_STYLE]);
        $this->add_control('button_color',['label'=>'Text Color','type'=>Controls_Manager::COLOR,'selectors'=>['{{WRAPPER}} .modern-btn'=>'color: {{VALUE}};']]);
        $this->add_control('button_bg',['label'=>'Background Color','type'=>Controls_Manager::COLOR,'selectors'=>['{{WRAPPER}} .modern-btn'=>'background-color: {{VALUE}};']]);
        $this->add_control('button_hover_color',['label'=>'Hover Text Color','type'=>Controls_Manager::COLOR,'selectors'=>['{{WRAPPER}} .modern-btn:hover'=>'color: {{VALUE}};']]);
        $this->add_control('button_hover_bg',['label'=>'Hover Background','type'=>Controls_Manager::COLOR,'selectors'=>['{{WRAPPER}} .modern-btn:hover'=>'background-color: {{VALUE}};']]);
        $this->add_group_control(Group_Control_Typography::get_type(),['name'=>'button_typography','selector'=>'{{WRAPPER}} .modern-btn']);
        $this->add_responsive_control('button_padding',['label'=>'Padding','type'=>Controls_Manager::DIMENSIONS,'size_units'=>['px','em','%'],'selectors'=>['{{WRAPPER}} .modern-btn'=>'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        $this->add_responsive_control('button_radius',['label'=>'Border Radius','type'=>Controls_Manager::SLIDER,'size_units'=>['px','%'],'selectors'=>['{{WRAPPER}} .modern-btn'=>'border-radius: {{SIZE}}{{UNIT}};']]);
        $this->end_controls_section();

        // ---------------- Nav & Dot Style ----------------
        $this->start_controls_section('nav_dot_style',['label'=>__('Nav & Dot Style','my-core-plugin'),'tab'=>Controls_Manager::TAB_STYLE]);

        // Nav color
        $this->add_control('nav_color',['label'=>'Nav Color','type'=>Controls_Manager::COLOR,'default'=>'#fff','selectors'=>['{{WRAPPER}} .nav-btn'=>'color:{{VALUE}};']]);

        // Dot color & active
        $this->add_control('dot_color',['label'=>'Dot Color','type'=>Controls_Manager::COLOR,'default'=>'#fff','selectors'=>['{{WRAPPER}} .modern-dots .dot'=>'background:{{VALUE}};']]);
        $this->add_control('dot_active_color',['label'=>'Active Dot Color','type'=>Controls_Manager::COLOR,'default'=>'#9d79ff','selectors'=>['{{WRAPPER}} .modern-dots .dot.active'=>'background:{{VALUE}};']]);

        // Dot size
        $this->add_control('dot_size', [
            'label' => __('Dot Size','my-core-plugin'),
            'type' => Controls_Manager::SLIDER,
            'size_units' => ['px','em'],
            'range' => [
                'px'=>['min'=>5,'max'=>50],
                'em'=>['min'=>0.3,'max'=>5]
            ],
            'default' => ['unit'=>'px','size'=>12],
            'selectors' => ['{{WRAPPER}} .modern-dots .dot'=>'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};']
        ]);

        // Dot spacing
        $this->add_control('dot_spacing', [
            'label' => __('Dot Spacing','my-core-plugin'),
            'type' => Controls_Manager::SLIDER,
            'size_units'=>['px','em'],
            'range'=>[
                'px'=>['min'=>0,'max'=>50],
                'em'=>['min'=>0,'max'=>5]
            ],
            'default'=>['unit'=>'px','size'=>10],
            'selectors'=>['{{WRAPPER}} .modern-dots'=>'gap: {{SIZE}}{{UNIT}};']
        ]);

        // Dot hover opacity
        $this->add_control('dot_hover_opacity', [
            'label' => __('Dot Hover Opacity','my-core-plugin'),
            'type' => Controls_Manager::SLIDER,
            'size_units'=>['%'],
            'range'=>['%'=>['min'=>0,'max'=>100]],
            'default'=>['unit'=>'%','size'=>100],
            'selectors'=>['{{WRAPPER}} .modern-dots .dot:hover'=>'opacity: {{SIZE}}%;']
        ]);

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        if(empty($settings['slides'])) return;

        $slider_id = 'modern-slider-'.$this->get_id();
        ?>
        <div id="<?php echo esc_attr($slider_id); ?>" class="modern-slider-wrapper" 
             data-autoplay="<?php echo esc_attr($settings['autoplay']); ?>" 
             data-speed="<?php echo esc_attr($settings['autoplay_speed']); ?>" 
             data-effect="<?php echo esc_attr($settings['slider_effect']); ?>" 
             data-text-effect="<?php echo esc_attr($settings['text_effect']); ?>"
             data-pause-hover="<?php echo esc_attr($settings['pause_on_hover']); ?>"
             data-nav="<?php echo esc_attr($settings['nav_show']); ?>"
             data-dots="<?php echo esc_attr($settings['dots_show']); ?>"
             data-image-fit="<?php echo esc_attr($settings['image_fit']); ?>"
             data-image-position="<?php echo esc_attr($settings['image_position']); ?>">
            <div class="modern-slider">
                <?php foreach($settings['slides'] as $slide): ?>
                    <div class="modern-slide">
                        <img src="<?php echo esc_url($slide['image']['url']); ?>" 
                             alt="<?php echo esc_attr($slide['title']); ?>" 
                             style="object-fit: <?php echo esc_attr($settings['image_fit']); ?>; 
                                    object-position: <?php echo esc_attr($settings['image_position']); ?>;">
                        <div class="modern-slide-content"
                             style="justify-content:<?php echo $settings['text_vertical_position']=='top'?'flex-start':($settings['text_vertical_position']=='middle'?'center':'flex-end'); ?>;
                                    align-items:<?php echo $settings['text_horizontal_position']=='left'?'flex-start':($settings['text_horizontal_position']=='center'?'center':'flex-end'); ?>;">
                            <h2><?php echo esc_html($slide['title']); ?></h2>
                            <p><?php echo esc_html($slide['description']); ?></p>
                            <?php if(!empty($slide['button_text'])): ?>
                                <a href="<?php echo esc_url($slide['button_url']['url']); ?>" class="modern-btn"><?php echo esc_html($slide['button_text']); ?></a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <?php if($settings['nav_show']=='yes'): ?>
                <div class="nav-btn prev">‹</div>
                <div class="nav-btn next">›</div>
            <?php endif; ?>
            <?php if($settings['dots_show']=='yes'): ?>
                <div class="modern-dots">
                    <?php foreach($settings['slides'] as $i => $slide): ?>
                        <div class="dot" data-index="<?php echo $i; ?>"></div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <style>
        .modern-slider-wrapper{position:relative;overflow:hidden;width:100%;height:80vh;display:flex;flex-direction:column;align-items:center;}
        .modern-slider{position:relative;width:100%;height:100%;}
        .modern-slide{position:absolute;inset:0;opacity:0;transition:opacity 0.8s, transform 0.8s;transform:scale(1.05);z-index:2;}
        .modern-slide.active{opacity:1;transform:scale(1);}
        .modern-slide img{width:100%;height:100%;object-fit:cover;}
        .modern-slide-content{position:absolute;top:0;left:0;width:100%;height:100%;display:flex;flex-direction:column;padding:2rem;z-index:3;pointer-events:none;}
        .modern-slide-content h2,.modern-slide-content p,.modern-slide-content .modern-btn{pointer-events:auto;}
        .modern-slide-content h2{font-size:3rem;margin:0;color:#fff;text-shadow:0 3px 8px rgba(0,0,0,0.4);}
        .modern-slide-content p{font-size:1.2rem;margin:0.5rem 0 0 0;}
        .modern-slide-content .modern-btn{display:inline-block;padding:0.8em 1.5em;text-decoration:none;border-radius:4px;margin-top:1rem;}
        .nav-btn{position:absolute;top:50%;transform:translateY(-50%);color:#fff;font-size:2.5rem;cursor:pointer;z-index:4;opacity:0.7;user-select:none;}
        .nav-btn:hover{opacity:1;}
        .nav-btn.prev{left:20px;}
        .nav-btn.next{right:20px;}
        .modern-dots{display:flex;justify-content:center;align-items:center;position:absolute;bottom:15px;gap:10px;z-index:5;}
        .modern-dots .dot{width:12px;height:12px;border-radius:50%;background:#fff;opacity:0.7;cursor:pointer;transition:opacity 0.3s;}
        .modern-dots .dot.active{background:#9d79ff;opacity:1;}
        .modern-dots .dot:hover{opacity:1;}
        @media(max-width:768px){.modern-slide-content h2{font-size:2rem;}.modern-slide-content p{font-size:1rem;}.modern-slide-content .modern-btn{padding:0.6em 1.2em;}}
        </style>

        <script>
        jQuery(function($){
            const wrapper = $('#<?php echo esc_js($slider_id); ?>');
            const slides = wrapper.find('.modern-slide');
            const dots = wrapper.find('.modern-dots .dot');
            let index = 0, timer = null;
            const autoplay = <?php echo ($settings['autoplay']=='yes')?'true':'false'; ?>;
            const speed = <?php echo esc_js($settings['autoplay_speed']); ?>;
            const pauseHover = wrapper.data('pause-hover') === 'yes';
            const navShow = wrapper.data('nav') === 'yes';
            const dotsShow = wrapper.data('dots') === 'yes';
            let effect = wrapper.data('effect') || 'fade';
            let textEffect = wrapper.data('text-effect') || 'fade_up';

            function goTo(i){
                index=i;
                slides.removeClass('active');
                slides.eq(i).addClass('active');
                if(dotsShow){ dots.removeClass('active'); dots.eq(i).addClass('active'); }

                const slide = slides.eq(i);
                const img = slide.find('img');
                const content = slide.find('.modern-slide-content h2, .modern-slide-content p, .modern-slide-content .modern-btn');

                switch(effect){
                    case 'fade': gsap.fromTo(img,{opacity:0},{opacity:1,duration:0.8}); break;
                    case 'slide': gsap.fromTo(img,{x:100,opacity:0},{x:0,opacity:1,duration:0.8}); break;
                    case 'zoom': gsap.fromTo(img,{scale:1.2,opacity:0},{scale:1,opacity:1,duration:0.8}); break;
                    case 'flip': gsap.fromTo(img,{rotationY:180,opacity:0},{rotationY:0,opacity:1,duration:0.8}); break;
                    case 'scale': gsap.fromTo(img,{scale:0.8,opacity:0},{scale:1,opacity:1,duration:0.8}); break;
                }

                switch(textEffect){
                    case 'fade_up': gsap.fromTo(content,{autoAlpha:0,y:20},{autoAlpha:1,y:0,duration:0.6,stagger:0.15}); break;
                    case 'slide_left': gsap.fromTo(content,{autoAlpha:0,x:50},{autoAlpha:1,x:0,duration:0.6,stagger:0.15}); break;
                    case 'zoom_in': gsap.fromTo(content,{autoAlpha:0,scale:1.1},{autoAlpha:1,scale:1,duration:0.6,stagger:0.15}); break;
                }
            }

            function nextSlide(){ goTo((index+1)%slides.length); restartAutoplay(); }
            function prevSlide(){ goTo((index-1+slides.length)%slides.length); restartAutoplay(); }

            if(navShow){
                wrapper.find('.next').on('click',nextSlide);
                wrapper.find('.prev').on('click',prevSlide);
            }

            if(dotsShow){
                dots.on('click',function(){ goTo($(this).data('index')); });
            }

            function autoplayStart(){ if(autoplay) timer=setInterval(nextSlide,speed); }
            function restartAutoplay(){ if(timer){ clearInterval(timer); autoplayStart(); } }

            let startX=0, deltaX=0;
            wrapper.on('touchstart',e=>{startX=e.originalEvent.touches[0].clientX;});
            wrapper.on('touchmove',e=>{deltaX=e.originalEvent.touches[0].clientX-startX;});
            wrapper.on('touchend',()=>{if(deltaX>50) prevSlide(); else if(deltaX<-50) nextSlide(); restartAutoplay(); deltaX=0;});

            if(pauseHover){
                wrapper.on('mouseenter',()=>{ if(timer) clearInterval(timer); });
                wrapper.on('mouseleave',()=>{ if(autoplay) autoplayStart(); });
            }

            if(typeof elementor !== 'undefined' && elementor.channels && elementor.channels.editor){
                elementor.channels.editor.on('change:slider_effect', function(model){ effect = model.get('slider_effect'); goTo(index); });
                elementor.channels.editor.on('change:text_effect', function(model){ textEffect = model.get('text_effect'); goTo(index); });
            }

            goTo(index);
            autoplayStart();
        });
        </script>
        <?php
    }
}

\Elementor\Plugin::instance()->widgets_manager->register(new \My_Core_Plugin\Widgets\Modern_Slider());
