<?php
namespace My_Core_Plugin\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Group_Control_Typography;

if ( ! defined( 'ABSPATH' ) ) exit;

class Gsap_Card_Gallery extends Widget_Base {

    public function get_name() { return 'gsap_card_gallery'; }
    public function get_title() { return esc_html__( 'GSAP Card Gallery', 'my-core-plugin' ); }
    public function get_icon() { return 'eicon-gallery-grid'; }
    public function get_categories() { return [ 'th-general' ]; }

    protected function register_controls() {

        // ================= Content: Gallery Repeater =================
        $this->start_controls_section(
            'section_gallery',
            [ 'label' => esc_html__( 'Gallery', 'my-core-plugin' ) ]
        );

        $repeater = new Repeater();

        $repeater->add_control(
            'image',
            [
                'label' => esc_html__( 'Image', 'my-core-plugin' ),
                'type' => Controls_Manager::MEDIA,
                'default' => ['url' => ''],
            ]
        );

        $repeater->add_control(
            'title',
            [
                'label' => esc_html__( 'Title', 'my-core-plugin' ),
                'type' => Controls_Manager::TEXT,
                'default' => esc_html__( 'Slide Title', 'my-core-plugin' ),
            ]
        );

        $repeater->add_control(
            'paragraph',
            [
                'label' => esc_html__( 'Paragraph', 'my-core-plugin' ),
                'type' => Controls_Manager::TEXTAREA,
                'default' => esc_html__( 'Slide paragraph content.', 'my-core-plugin' ),
            ]
        );

        $this->add_control(
            'gallery_items',
            [
                'label' => esc_html__( 'Gallery Items', 'my-core-plugin' ),
                'type' => Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
                'default' => [
                    ['title' => esc_html__('Slide 1','my-core-plugin')],
                    ['title' => esc_html__('Slide 2','my-core-plugin')],
                ],
                'title_field' => '{{{ title }}}',
            ]
        );

        $this->end_controls_section();

        // ================= Style: Grid =================
        $this->start_controls_section(
            'section_style_grid',
            [
                'label' => esc_html__( 'Grid', 'my-core-plugin' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'grid_gap',
            [
                'label' => esc_html__( 'Grid Gap', 'my-core-plugin' ),
                'type' => Controls_Manager::SLIDER,
                'default' => ['size' => 20],
                'range' => ['px' => ['min' => 0, 'max' => 100]],
                'selectors' => [
                    '{{WRAPPER}} .gsap-grid-container' => 'gap: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'grid_item_height',
            [
                'label' => esc_html__( 'Grid Item Height', 'my-core-plugin' ),
                'type' => Controls_Manager::SLIDER,
                'default' => ['size' => 300],
                'range' => ['px' => ['min' => 80, 'max' => 900]],
                'selectors' => [
                    '{{WRAPPER}} .gsap-grid-item' => 'height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        // ================= Style: Thumbs =================
        $this->start_controls_section(
            'section_style_thumbs',
            [
                'label' => esc_html__( 'Thumbs', 'my-core-plugin' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'thumb_size',
            [
                'label' => esc_html__( 'Thumb Size', 'my-core-plugin' ),
                'type' => Controls_Manager::SLIDER,
                'default' => ['size' => 60],
                'range' => ['px' => ['min' => 30, 'max' => 200]],
                'selectors' => [
                    '{{WRAPPER}} .gsap-thumb' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'thumb_radius',
            [
                'label' => esc_html__( 'Thumb Border Radius', 'my-core-plugin' ),
                'type' => Controls_Manager::SLIDER,
                'default' => ['size' => 6],
                'range' => ['px' => ['min' => 0, 'max' => 50]],
                'selectors' => [
                    '{{WRAPPER}} .gsap-thumb' => 'border-radius: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'thumb_border',
            [
                'label' => esc_html__( 'Thumb Border Width', 'my-core-plugin' ),
                'type' => Controls_Manager::SLIDER,
                'default' => ['size' => 2],
                'range' => ['px' => ['min' => 0, 'max' => 10]],
                'selectors' => [
                    '{{WRAPPER}} .gsap-thumb' => 'border-width: {{SIZE}}{{UNIT}}; border-style: solid;',
                ],
            ]
        );

        $this->add_control(
            'thumb_border_color',
            [
                'label' => esc_html__( 'Thumb Border Color', 'my-core-plugin' ),
                'type' => Controls_Manager::COLOR,
                'default' => '#fff',
                'selectors' => [
                    '{{WRAPPER}} .gsap-thumb' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_section();

        // ================= Style: Content (Fullscreen) =================
        $this->start_controls_section(
            'section_style_content',
            [
                'label' => esc_html__( 'Content (Fullscreen)', 'my-core-plugin' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'title_color',
            [
                'label' => esc_html__( 'Title Color', 'my-core-plugin' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .gsap-content-title' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'title_typography',
                'label' => esc_html__( 'Title Typography', 'my-core-plugin' ),
                'selector' => '{{WRAPPER}} .gsap-content-title',
            ]
        );

        $this->add_control(
            'paragraph_color',
            [
                'label' => esc_html__( 'Paragraph Color', 'my-core-plugin' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .gsap-content-paragraph' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'paragraph_typography',
                'label' => esc_html__( 'Paragraph Typography', 'my-core-plugin' ),
                'selector' => '{{WRAPPER}} .gsap-content-paragraph',
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        $uid = 'gsap_gallery_' . uniqid();
        $placeholder = \Elementor\Utils::get_placeholder_image_src();
        ?>
        <div id="<?php echo esc_attr($uid); ?>" class="gsap-gallery-widget">

            <!-- GRID -->
            <div class="gsap-grid-container">
                <?php foreach ( $settings['gallery_items'] as $index => $item ) :
                    $img = ! empty( $item['image']['url'] ) ? $item['image']['url'] : $placeholder;
                    $title = ! empty( $item['title'] ) ? $item['title'] : '';
                    $paragraph = ! empty( $item['paragraph'] ) ? $item['paragraph'] : '';
                ?>
                    <div class="gsap-grid-item" data-index="<?php echo esc_attr($index); ?>">
                        <div class="gsap-card"
                             data-title="<?php echo esc_attr($title); ?>"
                             data-paragraph="<?php echo esc_attr($paragraph); ?>"
                             style="background-image: url('<?php echo esc_url($img); ?>');">
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- FULLSCREEN SLIDER -->
            <div class="gsap-slider-fullscreen" style="display:none;">
                <div class="gsap-slider-image"></div>
                <div class="gsap-content-overlay">
                    <h1 class="gsap-content-title"><span></span></h1>
                    <p class="gsap-content-paragraph"></p>
                </div>
                <div class="gsap-thumbs-container">
                    <?php foreach ( $settings['gallery_items'] as $index => $item ):
                        $img = ! empty( $item['image']['url'] ) ? $item['image']['url'] : $placeholder;
                    ?>
                        <div class="gsap-thumb" data-index="<?php echo esc_attr($index); ?>" style="background-image:url('<?php echo esc_url($img); ?>');"></div>
                    <?php endforeach; ?>
                </div>
                <div class="gsap-close">&#10005;</div>
            </div>
        </div>

        <style>
        /* Grid */
        #<?php echo esc_attr($uid); ?> .gsap-grid-container { display:grid; grid-template-columns:repeat(auto-fill,minmax(250px,1fr)); margin:auto; }
        #<?php echo esc_attr($uid); ?> .gsap-grid-item { cursor:pointer; overflow:hidden; position:relative; }
        #<?php echo esc_attr($uid); ?> .gsap-card { width:100%; height:100%; background-size:cover; background-position:center; transition: transform .45s; }
        #<?php echo esc_attr($uid); ?> .gsap-card:hover { transform: scale(1.03); }

        /* Fullscreen */
        #<?php echo esc_attr($uid); ?> .gsap-slider-fullscreen { position:fixed; top:0; left:0; width:100vw; height:100vh; background:#000; z-index:999999; display:flex; align-items:center; justify-content:center; overflow:hidden; }
        #<?php echo esc_attr($uid); ?> .gsap-slider-image { position:fixed; top:0; left:0; width:100%; height:100%; background-size:cover; background-position:center center; background-repeat:no-repeat; will-change:left,top,width,height,background-position,opacity; }
        #<?php echo esc_attr($uid); ?> .gsap-content-overlay { position:fixed; left:48px; bottom:128px; z-index:1000000; pointer-events:none; opacity:0; transform:translateY(18px); }
        #<?php echo esc_attr($uid); ?> .gsap-thumbs-container { position:fixed; bottom:16px; left:50%; transform:translateX(-50%); display:flex; padding:8px 0; gap:8px; max-width:90vw; z-index:1000001; scrollbar-width:none; -ms-overflow-style:none; }
        #<?php echo esc_attr($uid); ?> .gsap-thumbs-container::-webkit-scrollbar { display:none; }
        #<?php echo esc_attr($uid); ?> .gsap-thumb { background-size:cover; background-position:center; cursor:pointer; flex-shrink:0; transition: transform 0.3s, border-color 0.3s, box-shadow 0.3s; }
        #<?php echo esc_attr($uid); ?> .gsap-thumb.active { box-shadow:0 6px 18px rgba(255,255,255,0.6); transform:translateY(-5px) scale(1.08); }

        /* Close */
        #<?php echo esc_attr($uid); ?> .gsap-close { position:fixed; top:18px; right:24px; font-size:48px; color:#fff; cursor:pointer; z-index:1000002; }
        </style>

        <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.14.1/gsap.min.js"></script>
        <script>
        (function(){
            const root = document.getElementById('<?php echo esc_js($uid); ?>');
            if(!root) return;

            const gridItems = root.querySelectorAll('.gsap-grid-item');
            const slider = root.querySelector('.gsap-slider-fullscreen');
            const sliderImage = root.querySelector('.gsap-slider-image');
            const contentOverlay = root.querySelector('.gsap-content-overlay');
            const contentTitle = root.querySelector('.gsap-content-title span');
            const contentParagraph = root.querySelector('.gsap-content-paragraph');
            const btnClose = root.querySelector('.gsap-close');
            const thumbs = Array.from(root.querySelectorAll('.gsap-thumb'));

            const slides = <?php echo wp_json_encode($settings['gallery_items']); ?>;
            let activeIndex = 0, isAnimating=false;

            function getCard(index){
                const gridItem = root.querySelector('.gsap-grid-item[data-index="'+index+'"]');
                if(!gridItem) return null;
                const card = gridItem.querySelector('.gsap-card');
                return {gridItem, card};
            }

            function setActiveThumb(index){
                thumbs.forEach((thumb,i)=>thumb.classList.toggle('active', i===index));
                const activeThumb = thumbs[index];
                if(activeThumb) activeThumb.scrollIntoView({behavior:'smooth', inline:'center'});
            }

            function openSlider(index){
                if(isAnimating) return;
                const found = getCard(index);
                if(!found) return;
                isAnimating=true;
                activeIndex=index;
                const {card} = found;
                const bg = card ? window.getComputedStyle(card).backgroundImage : '';
                const rect = card.getBoundingClientRect();
                sliderImage.style.backgroundImage=bg;
                slider.style.display='flex'; void slider.offsetHeight;
                gsap.set(sliderImage,{position:'fixed',left:rect.left,top:rect.top,width:rect.width,height:rect.height,opacity:1});
                gsap.to(sliderImage,{duration:0.8,ease:'power3.inOut',left:0,top:0,width:window.innerWidth,height:window.innerHeight,onComplete:()=>{
                    const slide=slides[activeIndex]||{};
                    contentTitle.textContent=slide.title||'';
                    contentParagraph.textContent=slide.paragraph||'';
                    gsap.fromTo(contentOverlay,{opacity:0,y:20},{opacity:1,y:0,duration:0.6,ease:'power3.out'});
                    setActiveThumb(activeIndex);
                    isAnimating=false;
                }});
            }

            function closeSlider(){
                if(isAnimating) return;
                const found=getCard(activeIndex);
                if(!found){ slider.style.display='none'; return; }
                isAnimating=true;
                const {card}=found;
                const rect=card.getBoundingClientRect();
                gsap.to(contentOverlay,{opacity:0,y:10,duration:0.25,ease:'power2.in'});
                gsap.to(sliderImage,{duration:0.7,ease:'power3.inOut',left:rect.left,top:rect.top,width:rect.width,height:rect.height,onComplete:()=>{
                    slider.style.display='none';
                    sliderImage.style.cssText='';
                    isAnimating=false;
                }});
            }

            function updateSlide(toIndex){
                if(isAnimating) return;
                activeIndex=(toIndex+slides.length)%slides.length;
                isAnimating=true;
                gsap.to(contentOverlay,{opacity:0,y:10,duration:0.25,ease:'power2.in'});
                gsap.to(sliderImage,{opacity:0,duration:0.3,ease:'power2.in',onComplete:()=>{
                    const slide=slides[activeIndex]||{};
                    const imgUrl=slide.image?.url||'';
                    sliderImage.style.backgroundImage=imgUrl?'url('+imgUrl+')':'';
                    contentTitle.textContent=slide.title||'';
                    contentParagraph.textContent=slide.paragraph||'';
                    gsap.to(sliderImage,{opacity:1,duration:0.45,ease:'power2.out'});
                    gsap.fromTo(contentOverlay,{opacity:0,y:20},{opacity:1,y:0,duration:0.6,delay:0.15,ease:'power3.out'});
                    setActiveThumb(activeIndex);
                    isAnimating=false;
                }});
            }

            gridItems.forEach(item=>{ const idx=parseInt(item.getAttribute('data-index'),10); item.addEventListener('click',()=>openSlider(idx)); });
            thumbs.forEach((thumb,i)=>thumb.addEventListener('click',()=>updateSlide(i)));

            document.addEventListener('keydown',e=>{
                if(slider.style.display!=='flex') return;
                if(e.key==='ArrowRight') updateSlide(activeIndex+1);
                if(e.key==='ArrowLeft') updateSlide(activeIndex-1);
                if(e.key==='Escape') closeSlider();
            });

            sliderImage.addEventListener('mousemove',e=>{
                const rect=sliderImage.getBoundingClientRect();
                const x=(e.clientX-rect.left)/rect.width;
                const y=(e.clientY-rect.top)/rect.height;
                gsap.to(sliderImage,{backgroundPosition:(x*100)+'% '+(y*100)+'%',duration:0.6,ease:'power3.out'});
            });

            sliderImage.addEventListener('mouseleave',()=>gsap.to(sliderImage,{backgroundPosition:'center center',duration:0.6,ease:'power3.out'}));

            window.addEventListener('resize',()=>{ if(slider.style.display==='flex'){ gsap.set(sliderImage,{left:0,top:0,width:window.innerWidth,height:window.innerHeight}); } });

            // ===== Modern GSAP Close Icon Effect =====
            if(btnClose){
                btnClose.addEventListener('mouseenter', () => {
                    gsap.to(btnClose, {scale: 1.2, rotation: 90, duration: 0.4, ease: "elastic.out(1, 0.5)"});
                });
                btnClose.addEventListener('mouseleave', () => {
                    gsap.to(btnClose, {scale: 1, rotation: 0, duration: 0.4, ease: "elastic.out(1, 0.5)"});
                });

                btnClose.addEventListener('click', () => {
                    gsap.to(btnClose, {scale: 0, rotation: 360, duration: 0.6, ease: "power3.in", onComplete: () => {
                        closeSlider();
                        gsap.set(btnClose, {scale: 1, rotation: 0});
                    }});
                });
            }

        })();
        </script>
        <?php
    }
}

// Register widget
\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Gsap_Card_Gallery() );
