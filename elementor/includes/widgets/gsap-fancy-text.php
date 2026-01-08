<?php
namespace My_Core_Plugin\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;

if (!defined('ABSPATH')) exit;

class GSAP_Letter_Animation extends Widget_Base {

    public function get_name() {
        return 'gsap_letter_animation';
    }

    public function get_title() {
        return __('Fancy Text', 'my-core-plugin');
    }

    public function get_icon() {
        return 'eicon-animation';
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
            'text',
            [
                'label' => __('Text', 'my-core-plugin'),
                'type' => Controls_Manager::TEXT,
                'default' => 'VOXELO',
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

        // Typography (font size default 12rem)
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'typography',
                'label' => __('Typography', 'my-core-plugin'),
                'selector' => '{{WRAPPER}} .letter',
                'fields_options' => [
                    'font_size' => [
                        'default' => [
                            'size' => 192, // 12rem
                            'unit' => 'px',
                        ],
                    ],
                ],
            ]
        );

        // Stroke Width Control
        $this->add_control(
            'stroke_width',
            [
                'label' => __('Stroke Width', 'my-core-plugin'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => ['min' => 1, 'max' => 15, 'step' => 1],
                ],
                'default' => ['size' => 2, 'unit' => 'px'],
            ]
        );

        $this->add_control(
            'base_color',
            [
                'label' => __('Base Stroke Color', 'my-core-plugin'),
                'type' => Controls_Manager::COLOR,
                'default' => '#ffffff',
            ]
        );

        $this->add_control(
            'top_color',
            [
                'label' => __('Top Layer Color', 'my-core-plugin'),
                'type' => Controls_Manager::COLOR,
                'default' => '#00ffff',
            ]
        );

        $this->add_control(
            'bottom_color',
            [
                'label' => __('Bottom Layer Color', 'my-core-plugin'),
                'type' => Controls_Manager::COLOR,
                'default' => '#ff00ff',
            ]
        );

        $this->add_control(
            'hover_color',
            [
                'label' => __('Hover Color', 'my-core-plugin'),
                'type' => Controls_Manager::COLOR,
                'default' => '#ffffff',
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        $text = str_split($settings['text']);
        $uid = 'gsap-letters-' . uniqid();
        $stroke_width = isset($settings['stroke_width']['size']) ? $settings['stroke_width']['size'] . 'px' : '2px';
        ?>

        <div class="text-animation-container" id="<?php echo esc_attr($uid); ?>">
            <div class="letters-container">
                <?php foreach ($text as $letter): ?>
                    <span class="letter"><?php echo esc_html($letter); ?></span>
                <?php endforeach; ?>
            </div>
        </div>

        <style>
        #<?php echo esc_attr($uid); ?> {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 150px;
            background: transparent;
        }
        #<?php echo esc_attr($uid); ?> .letters-container {
            display: flex;
            gap: 1rem;
        }
        #<?php echo esc_attr($uid); ?> .letter {
            display: inline-block;
            position: relative;
            font-family: 'Arial Black', sans-serif;
            font-weight: 900;
            font-size: <?php echo esc_attr($settings['typography']['font_size']['size']); ?><?php echo esc_attr($settings['typography']['font_size']['unit']); ?>;
            color: transparent;
            -webkit-text-stroke: <?php echo esc_attr($stroke_width); ?> <?php echo esc_attr($settings['base_color']); ?>;
        }
        </style>

        <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
        <script>
        (function(){
            function initGSAP(uid){
                const container = document.getElementById(uid);
                if(!container) return;
                const letters = container.querySelectorAll('.letter');
                letters.forEach(l => l.setAttribute('data-text', l.textContent));

                const tl = gsap.timeline({ repeat:-1, repeatDelay:0.5 });
                gsap.set(letters, { opacity:0, scale:0, rotationX:-90 });

                tl.to(letters, {
                    opacity:1, scale:1, rotationX:0,
                    duration:0.8,
                    stagger:{ each:0.1, ease:"back.out(1.7)" }
                })
                .to(letters, { duration:0.15, skewX:20, ease:"power4.inOut", stagger:{ each:0.05, from:"random" }})
                .to(letters, { duration:0.15, skewX:0, ease:"power4.inOut" })
                .to(letters, {
                    duration:0.25,
                    css:{ WebkitTextStroke: "<?php echo esc_js($stroke_width); ?> <?php echo esc_js($settings['top_color']); ?>" },
                    stagger:{ each:0.05, from:"start" }
                })
                .to(letters, {
                    duration:0.25,
                    css:{ WebkitTextStroke: "<?php echo esc_js($stroke_width); ?> <?php echo esc_js($settings['bottom_color']); ?>" },
                    stagger:{ each:0.05, from:"end" }
                })
                .to(letters, {
                    duration:0.25,
                    css:{ WebkitTextStroke: "<?php echo esc_js($stroke_width); ?> <?php echo esc_js($settings['base_color']); ?>" }
                })
                .to(letters, {
                    duration:0.5, scaleY:1.4, ease:"power2.in",
                    stagger:{ each:0.05, from:"center" }
                })
                .to(letters, {
                    duration:0.7, scaleY:1, ease:"elastic.out(1,0.3)",
                    stagger:{ each:0.05, from:"center" }
                })
                .to(letters, {
                    duration:0.7, y:-50, ease:"power2.inOut",
                    stagger:{ each:0.05, repeat:1, yoyo:true }
                })
                .to(letters, {
                    duration:1, opacity:0, scale:0, rotationX:90,
                    stagger:{ each:0.1, from:"end", ease:"back.in(1.7)" }
                });

                letters.forEach(letter => {
                    letter.addEventListener('mouseenter', () => gsap.to(letter, {
                        scale:1.2,
                        color:'<?php echo esc_js($settings['hover_color']); ?>',
                        webkitTextStroke:'0px',
                        duration:0.3,
                        ease:"power2.out"
                    }));
                    letter.addEventListener('mouseleave', () => gsap.to(letter, {
                        scale:1,
                        color:'transparent',
                        webkitTextStroke:'<?php echo esc_js($stroke_width); ?> <?php echo esc_js($settings['base_color']); ?>',
                        duration:0.3,
                        ease:"power2.in"
                    }));
                });
            }

            document.addEventListener('DOMContentLoaded', function(){ initGSAP('<?php echo esc_js($uid); ?>'); });

            if(window.elementorFrontend){
                window.elementorFrontend.hooks.addAction(
                    'frontend/element_ready/<?php echo esc_js($this->get_name()); ?>.default',
                    function(){ initGSAP('<?php echo esc_js($uid); ?>'); }
                );
            }
        })();
        </script>

        <?php
    }
}

\Elementor\Plugin::instance()->widgets_manager->register_widget_type(new GSAP_Letter_Animation());
