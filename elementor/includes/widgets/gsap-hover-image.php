<?php
namespace My_Core_Plugin\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Group_Control_Typography;

if ( ! defined( 'ABSPATH' ) ) exit;

class Gsap_Hover_Image extends Widget_Base {

    public function get_name() { return 'gsap_hover_image'; }
    public function get_title() { return esc_html__('GSAP Text Image', 'my-core-plugin'); }
    public function get_icon() { return 'eicon-image-rollover'; }
    public function get_categories() { return ['th-general']; }
    public function get_script_depends() { return ['gsap']; }

    protected function register_controls() {

        // -----------------------------
        // Content Section
        // -----------------------------
        $repeater = new Repeater();

        $repeater->add_control('image', [
            'label' => __('Image', 'my-core-plugin'),
            'type' => Controls_Manager::MEDIA,
            'default' => [
                'url' => \Elementor\Utils::get_placeholder_image_src(),
            ],
        ]);

        $repeater->add_control('text', [
            'label' => __('Text', 'my-core-plugin'),
            'type' => Controls_Manager::TEXT,
            'default' => 'Hover Text',
            'dynamic' => ['active' => true],
        ]);

        $repeater->add_control('text_tag', [
            'label' => __('HTML Tag', 'my-core-plugin'),
            'type' => Controls_Manager::SELECT,
            'default' => 'h3',
            'options' => [
                'h1' => 'H1',
                'h2' => 'H2',
                'h3' => 'H3',
                'h4' => 'H4',
                'h5' => 'H5',
                'h6' => 'H6',
                'div' => 'DIV',
                'span' => 'SPAN',
                'p' => 'P',
            ],
        ]);

        $this->start_controls_section('content_section', [
            'label' => __('Items', 'my-core-plugin'),
        ]);

        $this->add_control('items', [
            'label' => __('Hover Items', 'my-core-plugin'),
            'type' => Controls_Manager::REPEATER,
            'fields' => $repeater->get_controls(),
            'default' => [
                ['text' => 'Hover Text 1'],
                ['text' => 'Hover Text 2'],
            ],
            'title_field' => '{{{ text }}}',
        ]);

        $this->end_controls_section();

        // -----------------------------
        // Style Tab: Image
        // -----------------------------
        $this->start_controls_section('style_image', [
            'label' => __('Image', 'my-core-plugin'),
            'tab' => Controls_Manager::TAB_STYLE,
        ]);

        $this->add_control('image_size', [
            'label' => __('Image Size', 'my-core-plugin'),
            'type' => Controls_Manager::SLIDER,
            'size_units' => ['px','%','em'],
            'range' => [
                'px' => ['min' => 50, 'max' => 500],
                '%' => ['min' => 10, 'max' => 100],
                'em' => ['min' => 3, 'max' => 30],
            ],
            'selectors' => [
                '{{WRAPPER}} .gsap-container img.swipeimage' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
            ],
        ]);

        $this->add_control('image_border', [
            'label' => __('Border Width', 'my-core-plugin'),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => ['px','em','%'],
            'selectors' => [
                '{{WRAPPER}} .gsap-container img.swipeimage' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; border-style: solid;',
            ],
        ]);

        $this->add_control('image_border_color', [
            'label' => __('Border Color', 'my-core-plugin'),
            'type' => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .gsap-container img.swipeimage' => 'border-color: {{VALUE}};',
            ],
        ]);

        $this->add_control('image_border_radius', [
            'label' => __('Border Radius', 'my-core-plugin'),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => ['px','%','em'],
            'selectors' => [
                '{{WRAPPER}} .gsap-container img.swipeimage' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]);

        $this->add_group_control(\Elementor\Group_Control_Box_Shadow::get_type(), [
            'name' => 'image_box_shadow',
            'label' => __('Box Shadow', 'my-core-plugin'),
            'selector' => '{{WRAPPER}} .gsap-container img.swipeimage',
        ]);

        $this->end_controls_section();

        // -----------------------------
        // Style Tab: Layout
        // -----------------------------
        $this->start_controls_section('style_layout', [
            'label' => __('Layout', 'my-core-plugin'),
            'tab' => Controls_Manager::TAB_STYLE,
        ]);

        $this->add_responsive_control('widget_padding', [
            'label' => __('Padding', 'my-core-plugin'),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => ['px','%','em'],
            'selectors' => [
                '{{WRAPPER}} .gsap-hover-list' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]);

        $this->add_responsive_control('widget_margin', [
            'label' => __('Margin', 'my-core-plugin'),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => ['px','%','em'],
            'selectors' => [
                '{{WRAPPER}} .gsap-hover-list' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]);

        $this->add_control('widget_gap', [
            'label' => __('Gap Between Items', 'my-core-plugin'),
            'type' => Controls_Manager::SLIDER,
            'default' => ['size'=>20],
            'range' => ['px'=>['min'=>0,'max'=>200]],
            'selectors' => [
                '{{WRAPPER}} .gsap-hover-list .gsap-container' => 'margin-bottom: {{SIZE}}{{UNIT}};',
            ],
        ]);

        $this->add_control('widget_alignment', [
            'label' => __('Text Alignment', 'my-core-plugin'),
            'type' => Controls_Manager::CHOOSE,
            'options' => [
                'left' => ['title'=>__('Left','my-core-plugin'),'icon'=>'eicon-text-align-left'],
                'center' => ['title'=>__('Center','my-core-plugin'),'icon'=>'eicon-text-align-center'],
                'right' => ['title'=>__('Right','my-core-plugin'),'icon'=>'eicon-text-align-right'],
            ],
            'default' => 'left',
            'selectors' => [
                '{{WRAPPER}} .gsap-container .text h1, {{WRAPPER}} .gsap-container .text h2, {{WRAPPER}} .gsap-container .text h3, {{WRAPPER}} .gsap-container .text h4, {{WRAPPER}} .gsap-container .text h5, {{WRAPPER}} .gsap-container .text h6, {{WRAPPER}} .gsap-container .text div, {{WRAPPER}} .gsap-container .text span, {{WRAPPER}} .gsap-container .text p' => 'text-align: {{VALUE}}; margin:0;',
            ],
        ]);

        // -----------------------------
        // Item Border Controls
        // -----------------------------
        $this->add_control('item_border', [
            'label' => __('Border Width', 'my-core-plugin'),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => ['px', 'em', '%'],
            'selectors' => [
                '{{WRAPPER}} .gsap-container' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; border-style: solid;',
            ],
        ]);

        $this->add_control('item_border_color', [
            'label' => __('Border Color', 'my-core-plugin'),
            'type' => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .gsap-container' => 'border-color: {{VALUE}};',
            ],
        ]);

        $this->add_control('item_border_radius', [
            'label' => __('Border Radius', 'my-core-plugin'),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => ['px', '%', 'em'],
            'selectors' => [
                '{{WRAPPER}} .gsap-container' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]);

        $this->end_controls_section();

        // -----------------------------
        // Style Tab: Text
        // -----------------------------
        $this->start_controls_section('style_text', [
            'label' => __('Text', 'my-core-plugin'),
            'tab' => Controls_Manager::TAB_STYLE,
        ]);

        $this->add_control('text_color', [
            'label' => __('Text Color', 'my-core-plugin'),
            'type' => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .gsap-container .text h1, {{WRAPPER}} .gsap-container .text h2, {{WRAPPER}} .gsap-container .text h3, {{WRAPPER}} .gsap-container .text h4, {{WRAPPER}} .gsap-container .text h5, {{WRAPPER}} .gsap-container .text h6, {{WRAPPER}} .gsap-container .text div, {{WRAPPER}} .gsap-container .text span, {{WRAPPER}} .gsap-container .text p' => 'color: {{VALUE}}; margin:0;',
            ],
        ]);

        $this->add_group_control(Group_Control_Typography::get_type(), [
            'name' => 'text_typography',
            'label' => __('Typography', 'my-core-plugin'),
            'selector' => '{{WRAPPER}} .gsap-container .text h1, {{WRAPPER}} .gsap-container .text h2, {{WRAPPER}} .gsap-container .text h3, {{WRAPPER}} .gsap-container .text h4, {{WRAPPER}} .gsap-container .text h5, {{WRAPPER}} .gsap-container .text h6, {{WRAPPER}} .gsap-container .text div, {{WRAPPER}} .gsap-container .text span, {{WRAPPER}} .gsap-container .text p',
        ]);

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        ?>
        <ul class="gsap-hover-list">
            <?php foreach ($settings['items'] as $item): ?>
            <li class="gsap-container">
                <img class="swipeimage" src="<?php echo esc_url($item['image']['url']); ?>" />
                <div class="text">
                    <?php 
                        $tag = !empty($item['text_tag']) ? $item['text_tag'] : 'h3';
                        echo '<' . esc_html($tag) . '>' . esc_html($item['text']) . '</' . esc_html($tag) . '>';
                    ?>
                </div>
            </li>
            <?php endforeach; ?>
        </ul>

        <style>
        .gsap-hover-list { list-style: none; padding: 0; margin: 0; }
        .gsap-container { position: relative; padding: 2rem; border-bottom: 2px solid #ccc; }
        .gsap-container img.swipeimage {
            position: fixed;
            top: 0; left: 0;
            object-fit: cover;
            transform: translateX(-50%) translateY(-50%);
            z-index: 9;
            opacity: 0;
            visibility: hidden;
            pointer-events: none;
        }
        .gsap-container .text h1,
        .gsap-container .text h2,
        .gsap-container .text h3,
        .gsap-container .text h4,
        .gsap-container .text h5,
        .gsap-container .text h6,
        .gsap-container .text div,
        .gsap-container .text span,
        .gsap-container .text p {
            margin: 0;
        }
        </style>

        <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.13.0/gsap.min.js"></script>
        <script>
        (function(){
            gsap.utils.toArray(".gsap-container img.swipeimage").forEach(img => gsap.set(img, { yPercent: -50, xPercent: -50 }));
            let firstEnter;
            gsap.utils.toArray(".gsap-container").forEach((el) => {
                const image = el.querySelector("img.swipeimage"),
                      setX = gsap.quickTo(image, "x", { duration: 0.4, ease: "power3" }),
                      setY = gsap.quickTo(image, "y", { duration: 0.4, ease: "power3" }),
                      align = (e) => {
                        if(firstEnter){ setX(e.clientX, e.clientX); setY(e.clientY, e.clientY); firstEnter=false; }
                        else{ setX(e.clientX); setY(e.clientY); }
                      },
                      startFollow = () => document.addEventListener("mousemove", align),
                      stopFollow = () => document.removeEventListener("mousemove", align),
                      fade = gsap.to(image, { autoAlpha:1, ease:"none", paused:true, duration:0.1, onReverseComplete: stopFollow });

                el.addEventListener("mouseenter", (e) => { firstEnter=true; fade.play(); startFollow(); align(e); });
                el.addEventListener("mouseleave", () => fade.reverse());
            });
        })();
        </script>
        <?php
    }
}

\Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Gsap_Hover_Image());
