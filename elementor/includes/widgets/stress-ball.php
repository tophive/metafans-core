<?php
/**
 * Plugin Name: Elementor Stress Ball Widget
 * Description: Draggable Stress/Tennis Ball Widget for Elementor — 20 GSAP BG effects, scroll-down icon, full controls, slider height.
 * Version: 1.0
 * Author: Tophive
 */

namespace My_Core_Plugin\Widgets;

if (!defined('ABSPATH')) exit;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Text_Stroke;
use Elementor\Widget_Base;

class Stress_Ball_Slideshow extends Widget_Base {

    public function get_name() { return 'stress_ball_slideshow'; }
    public function get_title() { return __('Stress Ball Slideshow', 'my-core-plugin'); }
    public function get_icon() { return 'eicon-layout-settings'; }
    public function get_categories() { return ['th-general']; }

    protected function register_controls() {

        // ========================
        // Content Section
        // ========================
        $this->start_controls_section('content_section', [
            'label' => __('Content', 'my-core-plugin'),
            'tab' => Controls_Manager::TAB_CONTENT,
        ]);

        $this->add_control('ball_type', [
            'label' => __('Ball Type', 'my-core-plugin'),
            'type' => Controls_Manager::SELECT,
            'options' => ['image' => 'Image', 'icon' => 'Icon'],
            'default' => 'image',
        ]);

        $this->add_control('ball_image', [
            'label' => __('Ball Image', 'my-core-plugin'),
            'type' => Controls_Manager::MEDIA,
            'condition' => ['ball_type' => 'image'],
        ]);

        $this->add_control('ball_icon', [
            'label' => __('Ball Icon', 'my-core-plugin'),
            'type' => Controls_Manager::MEDIA,
            'condition' => ['ball_type' => 'icon'],
        ]);

        $this->add_control('heading_text', [
            'label' => __('Heading Text', 'my-core-plugin'),
            'type' => Controls_Manager::TEXTAREA,
            'default' => 'STRESS BALL',
        ]);

        $this->add_control('description_text', [
            'label' => __('Description Text', 'my-core-plugin'),
            'type' => Controls_Manager::TEXTAREA,
            'default' => 'Feel all your stress fade away',
        ]);

        $this->add_control('bounce_friction', [
            'label' => __('Bounce Friction', 'my-core-plugin'),
            'type' => Controls_Manager::SLIDER,
            'size_units' => ['', '%'],
            'range' => ['' => ['min' => 0, 'max' => 1, 'step' => 0.01]],
            'default' => ['unit' => '', 'size' => 0.7],
        ]);

        // ========================
        // Responsive Ball Size
        // ========================
        $this->add_responsive_control('ball_size', [
            'label' => __('Ball Size', 'my-core-plugin'),
            'type' => Controls_Manager::SLIDER,
            'size_units' => ['px', '%', 'em'],
            'range' => [
                'px' => ['min' => 50, 'max' => 500],
                '%' => ['min' => 5, 'max' => 50],
                'em' => ['min' => 3, 'max' => 30],
            ],
            'default' => ['unit' => 'px', 'size' => 200],
            'selectors' => [
                '{{WRAPPER}} .ball' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
            ],
        ]);

        $this->end_controls_section();

        // ========================
        // Main Background Section
        // ========================
        $this->start_controls_section('main_bg_section', [
            'label' => __('Main Background', 'my-core-plugin'),
            'tab' => Controls_Manager::TAB_STYLE,
        ]);

        $this->add_control('main_bg', [
            'label' => __('Background Image', 'my-core-plugin'),
            'type' => Controls_Manager::MEDIA,
        ]);

        // Slider Height Control
        $this->add_responsive_control('slider_height', [
            'label' => __('Slider Height', 'my-core-plugin'),
            'type' => Controls_Manager::SLIDER,
            'size_units' => ['px','%','vh'],
            'range' => [
                'px'=>['min'=>100,'max'=>2000],
                '%'=>['min'=>10,'max'=>100],
                'vh'=>['min'=>10,'max'=>100],
            ],
            'default'=>['unit'=>'vh','size'=>100],
            'selectors'=>['{{WRAPPER}}'=>'height: {{SIZE}}{{UNIT}};'],
        ]);

        $this->add_control('bg_gsap_effect', [
            'label' => __('Background Effect', 'my-core-plugin'),
            'type' => Controls_Manager::SELECT,
            'options' => [
                'none'=>'None','float'=>'Float/Bob','swing'=>'Swing','pulse'=>'Pulse','rotate'=>'Continuous Rotate',
                'fade-in'=>'Fade In','slide-left'=>'Slide Left','slide-right'=>'Slide Right','clip-reveal'=>'Clip Reveal',
                'shake'=>'Shake','parallax'=>'Parallax Scroll','color-shift'=>'Hue Shift','blur'=>'Blur Animate',
                'scale-scroll'=>'Scale on Scroll','wobble'=>'Wobble','drift'=>'Drift Slowly','rotate-hover'=>'Rotate Hover',
                'zoom-pulse'=>'Zoom Pulse','tilt-hover'=>'Tilt Hover','scroll-fade'=>'Scroll Fade'
            ],
            'default'=>'none',
        ]);

        $this->add_control('main_border', [
            'label' => __('Border', 'my-core-plugin'),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => ['px'],
            'selectors' => ['{{WRAPPER}}' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'],
        ]);

        $this->add_control('main_border_color', [
            'label' => __('Border Color', 'my-core-plugin'),
            'type' => Controls_Manager::COLOR,
            'default' => '#000000',
            'selectors' => ['{{WRAPPER}}' => 'border-color: {{VALUE}};'],
        ]);

        $this->add_control('main_border_radius', [
            'label' => __('Border Radius', 'my-core-plugin'),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => ['px','%'],
            'selectors' => ['{{WRAPPER}}' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow:hidden;'],
        ]);

        $this->end_controls_section();

        // ========================
        // Ball Style Section
        // ========================
        $this->start_controls_section('ball_style', [
            'label' => __('Ball Style', 'my-core-plugin'),
            'tab' => Controls_Manager::TAB_STYLE,
        ]);

        $this->add_control('initial_position', [
            'label' => __('Ball Position', 'my-core-plugin'),
            'type' => Controls_Manager::CHOOSE,
            'options' => [
                'center' => ['title' => __('Center', 'my-core-plugin'), 'icon' => 'eicon-h-align-center'],
                'top-left' => ['title' => __('Top Left', 'my-core-plugin'), 'icon' => 'eicon-v-align-top'],
                'top-right' => ['title' => __('Top Right', 'my-core-plugin'), 'icon' => 'eicon-v-align-top'],
                'bottom-left' => ['title' => __('Bottom Left', 'my-core-plugin'), 'icon' => 'eicon-v-align-bottom'],
                'bottom-right' => ['title' => __('Bottom Right', 'my-core-plugin'), 'icon' => 'eicon-v-align-bottom'],
            ],
            'default' => 'center',
        ]);

        $this->add_control('ball_border_radius', [
            'label' => __('Ball Border Radius', 'my-core-plugin'),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => ['px','%'],
            'selectors' => [
                '{{WRAPPER}} .ball' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                '{{WRAPPER}} .ball-img, {{WRAPPER}} .ball-icon' => 'border-radius: inherit;',
            ],
        ]);

        $this->end_controls_section();

        // ========================
        // Text Styles Section
        // ========================
        $this->start_controls_section('heading_style', [
            'label' => __('Text Style', 'my-core-plugin'),
            'tab' => Controls_Manager::TAB_STYLE,
        ]);

        $this->add_group_control(Group_Control_Typography::get_type(), [
            'label' => __('Heading Font','my-core-plugin'),
            'name' => 'heading_typography',
            'selector' => '{{WRAPPER}} .presentation h1',
        ]);

        $this->add_control('heading_color', [
            'label' => __('Heading Color','my-core-plugin'),
            'type' => Controls_Manager::COLOR,
            'default' => '#854ade',
            'selectors' => ['{{WRAPPER}} .presentation h1' => 'color: {{VALUE}};'],
        ]);

        $this->add_group_control(Group_Control_Text_Stroke::get_type(), [
            'name' => 'heading_stroke',
            'selector' => '{{WRAPPER}} .presentation h1',
        ]);

        $this->add_group_control(Group_Control_Typography::get_type(), [
            'label' => __('Description Font','my-core-plugin'),
            'name' => 'desc_typography',
            'selector' => '{{WRAPPER}} .presentation p',
        ]);

        $this->add_control('desc_color', [
            'label' => __('Description Color','my-core-plugin'),
            'type' => Controls_Manager::COLOR,
            'default' => '#62596f',
            'selectors' => ['{{WRAPPER}} .presentation p'=>'color: {{VALUE}};'],
        ]);

        $this->add_control('text_align', [
            'label' => __('Alignment','my-core-plugin'),
            'type' => Controls_Manager::CHOOSE,
            'options' => [
                'left' => ['title'=>__('Left','my-core-plugin'),'icon' => 'eicon-text-align-left'],
                'center' => ['title'=>__('Center','my-core-plugin'),'icon' => 'eicon-text-align-center'],
                'right' => ['title'=>__('Right','my-core-plugin'),'icon' => 'eicon-text-align-right'],
            ],
            'default' => 'center',
            'selectors' => ['{{WRAPPER}} .presentation' => 'text-align: {{VALUE}};'],
        ]);

        $this->add_control('title_desc_gap', [
            'label'=>__('Title / Description Gap','my-core-plugin'),
            'type'=>Controls_Manager::SLIDER,
            'size_units'=>['px','em','%'],
            'range'=>['px'=>['min'=>0,'max'=>100],'em'=>['min'=>0,'max'=>10],'%'=>['min'=>0,'max'=>50]],
            'default'=>['unit'=>'px','size'=>10],
            'selectors'=>['{{WRAPPER}} .presentation h1'=>'margin-bottom: {{SIZE}}{{UNIT}};'],
        ]);

        $this->end_controls_section();

        // ========================
        // Overlay Section
        // ========================
        $this->start_controls_section('overlay_section', [
            'label'=>__('Overlay','my-core-plugin'),
            'tab'=>Controls_Manager::TAB_STYLE,
        ]);

        $this->add_group_control(Group_Control_Background::get_type(), [
            'name'=>'overlay_bg',
            'types'=>['classic','gradient','video'],
            'selector'=>'{{WRAPPER}} .overlay',
        ]);

        $this->add_control('overlay_opacity', [
            'label'=>__('Overlay Opacity','my-core-plugin'),
            'type'=>Controls_Manager::SLIDER,
            'size_units'=>['%'],
            'range'=>['%'=>['min'=>0,'max'=>100]],
            'default'=>['unit'=>'%','size'=>30],
            'selectors'=>['{{WRAPPER}} .overlay'=>'opacity: {{SIZE}}{{UNIT}};'],
        ]);

        $this->end_controls_section();

        // ========================
        // Scroll-Down Icon Section
        // ========================
        $this->start_controls_section('scroll_icon_section', [
            'label' => __('Scroll Down Icon', 'my-core-plugin'),
            'tab' => Controls_Manager::TAB_CONTENT,
        ]);

        $this->add_control('show_scroll_icon', [
            'label' => __('Show Scroll Icon', 'my-core-plugin'),
            'type' => Controls_Manager::SWITCHER,
            'label_on' => __('Yes', 'my-core-plugin'),
            'label_off' => __('No', 'my-core-plugin'),
            'return_value' => 'yes',
            'default' => 'yes',
        ]);

        $this->add_control('scroll_icon', [
            'label' => __('Icon', 'my-core-plugin'),
            'type' => Controls_Manager::ICONS,
            'default' => ['value' => 'fas fa-chevron-down', 'library' => 'fa-solid'],
            'condition' => ['show_scroll_icon' => 'yes'],
        ]);

        $this->add_responsive_control('scroll_icon_size', [
            'label' => __('Size', 'my-core-plugin'),
            'type' => Controls_Manager::SLIDER,
            'size_units' => ['px','em','%'],
            'range' => ['px'=>['min'=>10,'max'=>100],'em'=>['min'=>0.5,'max'=>10]],
            'default'=>['unit'=>'px','size'=>40],
            'selectors'=>['{{WRAPPER}} .scroll-down-icon i'=>'font-size: {{SIZE}}{{UNIT}};'],
            'condition' => ['show_scroll_icon'=>'yes'],
        ]);

        $this->add_control('scroll_icon_color', [
            'label'=>__('Color','my-core-plugin'),
            'type'=>Controls_Manager::COLOR,
            'default'=>'#ffffff',
            'selectors'=>['{{WRAPPER}} .scroll-down-icon i'=>'color: {{VALUE}};'],
            'condition' => ['show_scroll_icon'=>'yes'],
        ]);

        $this->add_control('scroll_icon_hover_color', [
            'label'=>__('Hover Color','my-core-plugin'),
            'type' => Controls_Manager::COLOR,
            'default'=>'#854ade',
            'selectors'=>['{{WRAPPER}} .scroll-down-icon:hover i'=>'color: {{VALUE}};'],
            'condition' => ['show_scroll_icon'=>'yes'],
        ]);

        $this->add_control('scroll_icon_animation', [
            'label'=>__('Animation','my-core-plugin'),
            'type' => Controls_Manager::SELECT,
            'options'=>['none'=>'None','bounce'=>'Bounce','pulse'=>'Pulse','fade'=>'Fade In-Out'],
            'default'=>'bounce',
            'condition' => ['show_scroll_icon'=>'yes'],
        ]);

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        $uid = 'stress_ball_'.uniqid();
        $bg_url = !empty($settings['main_bg']['url'])?$settings['main_bg']['url']:'';
        $ball_size_val = $settings['ball_size']['size'] ?? 200;
        $ball_size_unit = $settings['ball_size']['unit'] ?? 'px';
        $effect = $settings['bg_gsap_effect'] ?? 'none';
        $bounceFriction = ($settings['bounce_friction']['size'] ?? 0.7) * -1;
        ?>
        <div id="<?php echo esc_attr($uid); ?>" class="stress-ball-widget">
            <div class="overlay"></div>
            <div class="background background-inner" style="background-image:url('<?php echo esc_url($bg_url); ?>');position:absolute;width:100%;height:100%;top:0;left:0;background-size:cover;background-position:center;overflow:hidden;"></div>
            <div class="presentation">
                <h1><?php echo esc_html($settings['heading_text']); ?></h1>
                <p><?php echo esc_html($settings['description_text']); ?></p>
            </div>
            <div class="ball" role="button" aria-label="Draggable stress ball" tabindex="0">
                <?php if($settings['ball_type']==='image'):
                    $img_url = !empty($settings['ball_image']['url'])?$settings['ball_image']['url']:'https://emils.graphics/shared/stress-ball-1.png'; ?>
                    <div class="ball-img" style="background-image:url('<?php echo esc_url($img_url); ?>');"></div>
                <?php else:
                    $icon_url = !empty($settings['ball_icon']['url'])?$settings['ball_icon']['url']:'https://emils.graphics/shared/stress-ball-1.png'; ?>
                    <div class="ball-icon" style="background-image:url('<?php echo esc_url($icon_url); ?>');"></div>
                <?php endif; ?>
            </div>

            <?php if($settings['show_scroll_icon']==='yes'): ?>
            <div class="scroll-down-icon">
                <?php \Elementor\Icons_Manager::render_icon( $settings['scroll_icon'], ['aria-hidden'=>'true'] ); ?>
            </div>
            <?php endif; ?>
        </div>

        <style>
        #<?php echo esc_attr($uid); ?> {position:relative;width:100%;height:100%;overflow:hidden;}
        #<?php echo esc_attr($uid); ?> .presentation {position:absolute;top:40%;left:50%;width:100%;transform:translate(-50%,-50%);text-align:center;z-index:2;pointer-events:none;user-select:none;}
        #<?php echo esc_attr($uid); ?> .ball {width:<?php echo esc_attr($ball_size_val.$ball_size_unit); ?>;height:<?php echo esc_attr($ball_size_val.$ball_size_unit); ?>;position:absolute;cursor:grab;z-index:2;}
        #<?php echo esc_attr($uid); ?> .ball-img,#<?php echo esc_attr($uid); ?> .ball-icon {width:100%;height:100%;background-size:cover;background-repeat:no-repeat;}
        #<?php echo esc_attr($uid); ?> .scroll-down-icon {position:absolute;bottom:20px;left:50%;transform:translateX(-50%);z-index:3;cursor:pointer;display:flex;justify-content:center;align-items:center;}
        <?php if($settings['scroll_icon_animation']==='bounce'): ?>
        #<?php echo esc_attr($uid); ?> .scroll-down-icon {animation:bounceScroll 2s infinite;}
        @keyframes bounceScroll {0%,20%,50%,80%,100%{transform:translateX(-50%) translateY(0);}40%{transform:translateX(-50%) translateY(-10px);}60%{transform:translateX(-50%) translateY(-5px);}}
        <?php elseif($settings['scroll_icon_animation']==='pulse'): ?>
        #<?php echo esc_attr($uid); ?> .scroll-down-icon {animation:pulseScroll 1.5s infinite;}
        @keyframes pulseScroll {0%,100%{transform:translateX(-50%) scale(1);}50%{transform:translateX(-50%) scale(1.2);}}
        <?php elseif($settings['scroll_icon_animation']==='fade'): ?>
        #<?php echo esc_attr($uid); ?> .scroll-down-icon {animation:fadeScroll 1.5s infinite;}
        @keyframes fadeScroll {0%,100%{opacity:1;}50%{opacity:0.3;}}
        <?php endif; ?>
        </style>

        <script>
        (function(){
            const container = document.getElementById('<?php echo esc_js($uid); ?>');
            if(!container) return;
            const ball = container.querySelector('.ball');
            const bg = container.querySelector('.background-inner');
            const bounceFriction = <?php echo esc_js($bounceFriction); ?>;
            const friction=0.9,minVelocity=0.7;
            let isDragging=false,last={x:0,y:0},velocity={x:0,y:0};

            function getBounds() {
                return { maxX: container.offsetWidth - ball.offsetWidth, maxY: container.offsetHeight - ball.offsetHeight };
            }

            function getPoint(e){ return e.touches ? {x:e.touches[0].clientX, y:e.touches[0].clientY} : {x:e.clientX, y:e.clientY}; }
            function startDrag(e){ e.preventDefault(); isDragging=true; velocity={x:0,y:0}; last=getPoint(e); ball.style.cursor='grabbing'; }
            function dragMove(e){ if(!isDragging) return; const pt=getPoint(e); const dx=pt.x-last.x, dy=pt.y-last.y; let newX=gsap.getProperty(ball,'x')+dx; let newY=gsap.getProperty(ball,'y')+dy; const bounds=getBounds(); if(newX<0)newX=0; if(newX>bounds.maxX)newX=bounds.maxX; if(newY<0)newY=0; if(newY>bounds.maxY)newY=bounds.maxY; gsap.set(ball,{x:newX,y:newY}); velocity.x=dx; velocity.y=dy; last=pt; }
            function endDrag(){ if(!isDragging)return; isDragging=false; ball.style.cursor='grab'; requestAnimationFrame(animate); }
            function animate(){
                let x = gsap.getProperty(ball,'x'), y = gsap.getProperty(ball,'y');
                x += velocity.x; y += velocity.y;
                const bounds=getBounds();
                if(x<0){x=0; velocity.x*=bounceFriction;} else if(x>bounds.maxX){x=bounds.maxX; velocity.x*=bounceFriction;}
                if(y<0){y=0; velocity.y*=bounceFriction;} else if(y>bounds.maxY){y=bounds.maxY; velocity.y*=bounceFriction;}
                velocity.x *= friction; velocity.y *= friction;
                gsap.set(ball,{x:x,y:y,rotation:gsap.getProperty(ball,'rotation')+velocity.x*0.3});
                if(Math.abs(velocity.x)>minVelocity || Math.abs(velocity.y)>minVelocity) requestAnimationFrame(animate);
            }

            ball.addEventListener('mousedown',startDrag);
            window.addEventListener('mousemove',dragMove);
            window.addEventListener('mouseup',endDrag);
            ball.addEventListener('touchstart',startDrag,{passive:false});
            window.addEventListener('touchmove',dragMove,{passive:false});
            window.addEventListener('touchend',endDrag);

            // ✅ Initial Position Fix
            const position = '<?php echo esc_js($settings['initial_position']); ?>';
            const bounds = getBounds();
            let startX = 0, startY = 0;

            switch(position){
                case 'top-left':
                    startX = 0; startY = 0;
                    break;
                case 'top-right':
                    startX = bounds.maxX; startY = 0;
                    break;
                case 'bottom-left':
                    startX = 0; startY = bounds.maxY;
                    break;
                case 'bottom-right':
                    startX = bounds.maxX; startY = bounds.maxY;
                    break;
                case 'center':
                default:
                    startX = bounds.maxX / 2;
                    startY = bounds.maxY / 2;
                    break;
            }

            gsap.set(ball, { x: startX, y: startY }); // ✅ now positions correctly

            window.addEventListener('resize', ()=>{
                // recalculate position on resize (keep same alignment)
                const newBounds = getBounds();
                switch(position){
                    case 'top-left': startX = 0; startY = 0; break;
                    case 'top-right': startX = newBounds.maxX; startY = 0; break;
                    case 'bottom-left': startX = 0; startY = newBounds.maxY; break;
                    case 'bottom-right': startX = newBounds.maxX; startY = newBounds.maxY; break;
                    case 'center': default:
                        startX = newBounds.maxX / 2;
                        startY = newBounds.maxY / 2;
                        break;
                }
                gsap.set(ball, { x: startX, y: startY });
            });

            // =======================
            // GSAP BG Effects
            // =======================
            const effect = '<?php echo esc_js($effect); ?>';
            switch(effect){
                case 'float': gsap.to(bg,{y:'+=20',repeat:-1,yoyo:true,duration:2,ease:'sine.inOut'}); break;
                case 'swing': gsap.to(bg,{rotation:5,repeat:-1,yoyo:true,duration:2,ease:'sine.inOut'}); break;
                case 'pulse': gsap.to(bg,{scale:1.05,repeat:-1,yoyo:true,duration:1,ease:'power1.inOut'}); break;
                case 'rotate': gsap.to(bg,{rotation:360,repeat:-1,duration:20,ease:'linear'}); break;
                case 'fade-in': gsap.from(bg,{opacity:0,duration:1}); break;
                case 'slide-left': gsap.from(bg,{x:-200,opacity:0,duration:1,ease:'power2.out'}); break;
                case 'slide-right': gsap.from(bg,{x:200,opacity:0,duration:1,ease:'power2.out'}); break;
                case 'clip-reveal': gsap.from(bg,{clipPath:'inset(100% 0 0 0)',duration:1,ease:'power2.out'}); break;
                case 'shake': gsap.to(bg,{x:'+=10',repeat:-1,yoyo:true,duration:0.1}); break;
                case 'parallax': window.addEventListener('scroll',()=>{ gsap.to(bg,{y:window.scrollY*0.3,duration:0.5,ease:'none'}); }); break;
                case 'color-shift': gsap.to(bg,{filter:'hue-rotate(360deg)',repeat:-1,duration:10,ease:'linear'}); break;
                case 'blur': gsap.to(bg,{filter:'blur(5px)',repeat:-1,yoyo:true,duration:2,ease:'sine.inOut'}); break;
                case 'scale-scroll': window.addEventListener('scroll',()=>{ gsap.to(bg,{scale:1+window.scrollY/500,duration:0.3,ease:'power1.out'}); }); break;
                case 'wobble': gsap.to(bg,{rotation:3,repeat:-1,yoyo:true,duration:0.2,ease:'sine.inOut'}); break;
                case 'drift': gsap.to(bg,{x:'+=20',y:'+=10',repeat:-1,yoyo:true,duration:5,ease:'sine.inOut'}); break;
                case 'rotate-hover': container.addEventListener('mouseenter',()=>gsap.to(bg,{rotation:15,duration:1})); container.addEventListener('mouseleave',()=>gsap.to(bg,{rotation:0,duration:0.5})); break;
                case 'zoom-pulse': gsap.to(bg,{scale:1.05,repeat:-1,yoyo:true,duration:2,ease:'sine.inOut'}); break;
                case 'tilt-hover': container.addEventListener('mousemove',e=>{ const rect=container.getBoundingClientRect(); const x=(e.clientX-rect.left)/rect.width*20-10; const y=(e.clientY-rect.top)/rect.height*20-10; gsap.to(bg,{rotationX:y,rotationY:x,duration:0.3}); }); break;
                case 'scroll-fade': window.addEventListener('scroll',()=>{ gsap.to(bg,{opacity:1-window.scrollY/500,duration:0.3,ease:'power1.out'}); }); break;
            }

            // Scroll-down icon click
            const scrollIcon = container.querySelector('.scroll-down-icon');
            if(scrollIcon){scrollIcon.addEventListener('click',()=>{ const nextSection = container.nextElementSibling || document.documentElement; window.scrollTo({top:nextSection.offsetTop,behavior:'smooth'}); });}

        })();
        </script>
        <?php
    }
}

// Register widget
\Elementor\Plugin::instance()->widgets_manager->register(new \My_Core_Plugin\Widgets\Stress_Ball_Slideshow());
