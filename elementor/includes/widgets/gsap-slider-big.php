<?php
namespace My_Core_Plugin\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Repeater;

if (!defined('ABSPATH')) exit;

class Ultimate_Osmo_Fullscreen_Slider extends Widget_Base {

    public function get_name() { return 'ultimate_osmo_fullscreen_slider'; }
    public function get_title() { return __('Ultimate Fullscreen GSAP Slider', 'my-core-plugin'); }
    public function get_icon() { return 'eicon-slider-push'; }
    public function get_categories() { return ['th-general']; }

    protected function register_controls() {

        // Slides repeater
        $repeater = new Repeater();
        $repeater->add_control('slide_image',['label'=>'Slide Image','type'=>Controls_Manager::MEDIA,'default'=>['url'=>'']]);
        $repeater->add_control('slide_webgl',['label'=>'WebGL Background','type'=>Controls_Manager::MEDIA,'default'=>['url'=>'']]);
        $repeater->add_control('slide_title',['label'=>'Slide Title','type'=>Controls_Manager::TEXT,'default'=>'Slide Title']);
        $repeater->add_control('slide_desc',['label'=>'Slide Description','type'=>Controls_Manager::TEXTAREA,'default'=>'Slide Description']);
        $repeater->add_control('slide_button_text',['label'=>'Button Text','type'=>Controls_Manager::TEXT,'default'=>'Learn More']);
        $repeater->add_control('slide_button_link',['label'=>'Button Link','type'=>Controls_Manager::URL]);

        $this->start_controls_section('slides_section',['label'=>'Slides','tab'=>Controls_Manager::TAB_CONTENT]);
        $this->add_control('slides',[
            'label'=>'Slides',
            'type'=>Controls_Manager::REPEATER,
            'fields'=>$repeater->get_controls(),
            'default'=>[
                [
                    'slide_title'=>'Creative Design',
                    'slide_desc'=>'Interactive WebGL & GSAP slider.',
                    'slide_image'=>['url'=>'https://yourdomain.com/images/slide1.jpg'],
                    'slide_webgl'=>['url'=>'https://yourdomain.com/webgl/texture1.png'],
                    'slide_button_text'=>'Explore',
                    'slide_button_link'=>['url'=>'#slide1']
                ],
                [
                    'slide_title'=>'Modern Agency',
                    'slide_desc'=>'Responsive, touch-friendly slider.',
                    'slide_image'=>['url'=>'https://yourdomain.com/images/slide2.jpg'],
                    'slide_webgl'=>['url'=>'https://yourdomain.com/webgl/texture2.png'],
                    'slide_button_text'=>'View Projects',
                    'slide_button_link'=>['url'=>'#slide2']
                ],
                [
                    'slide_title'=>'3D Interactions',
                    'slide_desc'=>'Hover tilt & GSAP animations.',
                    'slide_image'=>['url'=>'https://yourdomain.com/images/slide3.jpg'],
                    'slide_webgl'=>['url'=>'https://yourdomain.com/webgl/texture3.png'],
                    'slide_button_text'=>'Get Started',
                    'slide_button_link'=>['url'=>'#slide3']
                ]
            ],
            'title_field'=>'{{{ slide_title }}}'
        ]);
        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display(); ?>
        <div class="ultimate-fullscreen-slider">
            <?php foreach($settings['slides'] as $slide): ?>
                <section class="slide">
                    <?php if(!empty($slide['slide_webgl']['url'])): ?>
                        <div class="slide-webgl lazy-webgl" data-webgl="<?php echo esc_url($slide['slide_webgl']['url']); ?>"></div>
                    <?php endif; ?>
                    <?php if(!empty($slide['slide_image']['url'])): ?>
                        <img class="lazy-img" data-src="<?php echo esc_url($slide['slide_image']['url']); ?>" alt="<?php echo esc_attr($slide['slide_title']); ?>">
                    <?php endif; ?>
                    <div class="slide-content">
                        <h2><?php echo esc_html($slide['slide_title']); ?></h2>
                        <p><?php echo esc_html($slide['slide_desc']); ?></p>
                        <?php if(!empty($slide['slide_button_text'])): ?>
                            <a href="<?php echo esc_url($slide['slide_button_link']['url']); ?>" class="slide-btn"><?php echo esc_html($slide['slide_button_text']); ?></a>
                        <?php endif; ?>
                    </div>
                </section>
            <?php endforeach; ?>
        </div>

        <style>
        html, body{margin:0;padding:0;overflow-x:hidden;}
        .ultimate-fullscreen-slider{display:flex;width:100vw;height:100vh;overflow:hidden;scroll-snap-type:x mandatory}
        .slide{min-width:100vw;height:100vh;position:relative;display:flex;align-items:center;justify-content:center;flex-shrink:0;scroll-snap-align:center}
        .slide img{width:100%;height:100%;object-fit:cover;position:absolute;top:0;left:0;z-index:-1;opacity:0;transition:opacity 0.8s ease}
        .slide-webgl{position:absolute;top:0;left:0;width:100%;height:100%;z-index:-2;border-radius:0}
        .slide-content{position:relative;color:#fff;text-align:center;z-index:2}
        .slide-content h2{font-size:3rem;margin-bottom:1rem}
        .slide-content p{font-size:1.25rem;margin-bottom:1.5rem}
        .slide-btn{padding:.75rem 1.5rem;border-radius:8px;background:#1a1a1a;color:#fff;text-decoration:none;transition:transform .3s}
        .slide-btn:hover{transform:scale(1.05)}
        @media(max-width:768px){.slide-content h2{font-size:2rem}.slide-content p{font-size:1rem}}
        </style>

        <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/ScrollTrigger.min.js"></script>
        <script>
        (function(){
            const slides=document.querySelectorAll('.slide');
            document.querySelectorAll('.lazy-img').forEach(img=>img.src=img.dataset.src);

            // WebGL fallback
            function isWebGL(){try{const c=document.createElement('canvas');return !!window.WebGLRenderingContext&&(!!(c.getContext('webgl')||c.getContext('experimental-webgl')))}catch(e){return false}}
            document.querySelectorAll('.lazy-webgl').forEach(el=>{if(!isWebGL()) el.style.display='none'});

            gsap.registerPlugin(ScrollTrigger);
            const container=document.querySelector('.ultimate-fullscreen-slider');

            // Horizontal scroll with snap
            gsap.to(container,{
                x: () => -(container.scrollWidth - window.innerWidth),
                ease: "none",
                scrollTrigger: {
                    trigger: container,
                    start: "top top",
                    end: () => "+=" + container.scrollWidth,
                    scrub: 1,
                    pin: true,
                    snap: 1 / (slides.length - 1),
                    anticipatePin: 1
                }
            });

            // Fade in images per slide
            slides.forEach(slide=>{
                gsap.fromTo(slide.querySelector('img'),{opacity:0},{opacity:1,duration:1,scrollTrigger:{
                    trigger: slide,
                    start: "left center",
                    end: "right center",
                    scrub: true
                }})
            });
        })();
        </script>
        <?php
    }
}

// Register widget
\Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Ultimate_Osmo_Fullscreen_Slider());
?>
