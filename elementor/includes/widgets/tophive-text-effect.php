<?php
use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Group_Control_Typography;

if ( ! defined( 'ABSPATH' ) ) exit;

class Tophive_Text_Effect extends \Elementor\Widget_Base {

    public function get_name() {
        return 'tophive-text-effect';
    }

    public function get_title() {
        return TH_ELEMENTOR_DISPLAY_NAME_SC . esc_html__('GSAP Text Mask', TH_ELEMENTOR_SLUG);
    }

    public function get_icon() {
        return 'eicon-text-area';
    }

    public function get_categories() {
        return ['th-general'];
    }

    public function get_script_depends() {
        return ['tophive-text-effect-js'];
    }

    protected function register_controls() {

        // -----------------------------
        // Content Section
        // -----------------------------
        $repeater = new Repeater();
        $repeater->add_control('main_text', [
            'label' => __('Main Text', 'plugin-name'),
            'type' => Controls_Manager::TEXT,
            'default' => 'TEXT EFFECT',
            'label_block' => true,
        ]);
        $repeater->add_control('sub_text', [
            'label' => __('Sub Text', 'plugin-name'),
            'type' => Controls_Manager::TEXT,
            'default' => 'WOAH',
            'label_block' => true,
        ]);
        $repeater->add_control('sub_text_link', [
            'label' => __('Sub Text Link', 'plugin-name'),
            'type' => Controls_Manager::URL,
            'placeholder' => 'https://your-link.com',
            'show_external' => true,
            'default' => ['url' => '', 'is_external' => false, 'nofollow' => false],
        ]);

        $this->start_controls_section('content_section', [
            'label' => __('Content', 'plugin-name'),
        ]);

        $this->add_control('lines', [
            'label' => __('Text Lines', 'plugin-name'),
            'type' => Controls_Manager::REPEATER,
            'fields' => $repeater->get_controls(),
            'default' => [
                ['main_text' => 'TEXT EFFECT', 'sub_text' => 'WOAH'],
                ['main_text' => 'GSAP', 'sub_text' => 'AND CLIPPING'],
                ['main_text' => 'CRAZYYY', 'sub_text' => 'CRAZYYY'],
            ],
            'title_field' => '{{{ main_text }}}',
        ]);

        $this->end_controls_section();

        // -----------------------------
        // Layout / Style
        // -----------------------------
        $this->start_controls_section('style_layout', [
            'label' => __('Layout', 'plugin-name'),
            'tab' => Controls_Manager::TAB_STYLE,
        ]);

        $this->add_responsive_control('widget_padding', [
            'label' => __('Padding', 'plugin-name'),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => ['px','%','em'],
            'default' => ['top'=>0,'right'=>0,'bottom'=>0,'left'=>0],
            'selectors' => [
                '{{WRAPPER}} .tophive-text-effect-wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]);

        $this->add_responsive_control('widget_margin', [
            'label' => __('Margin', 'plugin-name'),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => ['px','%','em'],
            'default' => ['top'=>0,'right'=>0,'bottom'=>0,'left'=>0],
            'selectors' => [
                '{{WRAPPER}} .tophive-text-effect-wrapper' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]);

        $this->add_control('widget_gap', [
            'label' => __('Gap Between Lines', 'plugin-name'),
            'type' => Controls_Manager::SLIDER,
            'default' => ['size'=>20],
            'range' => ['px'=>['min'=>0,'max'=>200]],
            'selectors' => [
                '{{WRAPPER}} .tophive-text-effect-wrapper' => 'gap: {{SIZE}}{{UNIT}};',
            ],
        ]);

        $this->add_control('widget_alignment', [
            'label' => __('Text Alignment', 'plugin-name'),
            'type' => Controls_Manager::CHOOSE,
            'options' => [
                'left'   => ['title'=>__('Left','plugin-name'),'icon'=>'eicon-text-align-left'],
                'center' => ['title'=>__('Center','plugin-name'),'icon'=>'eicon-text-align-center'],
                'right'  => ['title'=>__('Right','plugin-name'),'icon'=>'eicon-text-align-right'],
            ],
            'default' => 'left',
            'selectors' => [
                '{{WRAPPER}} .tophive-text-effect-wrapper' => 'text-align: {{VALUE}};',
            ],
        ]);

        $this->end_controls_section();

        // -----------------------------
        // Main Text Style
        // -----------------------------
        $this->start_controls_section('style_main_text', [
            'label' => __('Main Text', 'plugin-name'),
            'tab' => Controls_Manager::TAB_STYLE,
        ]);

        $this->add_control('main_text_color', [
            'label' => __('Text Color', 'plugin-name'),
            'type' => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .tophive-text-effect' => 'color: {{VALUE}};',
            ],
        ]);

        $this->add_group_control(Group_Control_Typography::get_type(), [
            'name' => 'main_text_typography',
            'label' => __('Typography', 'plugin-name'),
            'selector' => '{{WRAPPER}} .tophive-text-effect',
        ]);

        $this->add_control('main_text_border', [
            'label' => __('Main Text Border', 'plugin-name'),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => ['px','em','%'],
            'selectors' => [
                '{{WRAPPER}} .tophive-text-effect' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; border-style: solid;',
            ],
        ]);

        $this->add_control('main_text_border_color', [
            'label' => __('Main Text Border Color', 'plugin-name'),
            'type' => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .tophive-text-effect' => 'border-color: {{VALUE}};',
            ],
        ]);

        $this->end_controls_section();

        // -----------------------------
        // Sub Text Style
        // -----------------------------
        $this->start_controls_section('style_sub_text', [
            'label' => __('Sub Text', 'plugin-name'),
            'tab' => Controls_Manager::TAB_STYLE,
        ]);

        $this->add_control('sub_text_color', [
            'label' => __('Text Color', 'plugin-name'),
            'type' => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .tophive-text-effect span' => 'color: {{VALUE}};',
            ],
        ]);

        $this->add_control('sub_text_bg', [
            'label' => __('Background Color', 'plugin-name'),
            'type' => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .tophive-text-effect span' => 'background-color: {{VALUE}};',
            ],
        ]);

        $this->add_control('sub_text_border', [
            'label' => __('Border', 'plugin-name'),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => ['px','em','%'],
            'selectors' => [
                '{{WRAPPER}} .tophive-text-effect span' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; border-style: solid;',
            ],
        ]);

        $this->add_control('sub_text_border_color', [
            'label' => __('Border Color', 'plugin-name'),
            'type' => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .tophive-text-effect span' => 'border-color: {{VALUE}};',
            ],
        ]);

        $this->add_control('sub_text_border_hover_color', [
            'label' => __('Hover Border Color', 'plugin-name'),
            'type' => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .tophive-text-effect:hover span' => 'border-color: {{VALUE}};',
            ],
        ]);

        $this->add_control('sub_text_alignment', [
            'label' => __('Sub Text Alignment', 'plugin-name'),
            'type' => Controls_Manager::CHOOSE,
            'options' => [
                'flex-start' => ['title' => __('Left','plugin-name'),'icon' => 'eicon-h-align-left'],
                'center'     => ['title' => __('Center','plugin-name'),'icon' => 'eicon-h-align-center'],
                'flex-end'   => ['title' => __('Right','plugin-name'),'icon' => 'eicon-h-align-right'],
            ],
            'default' => 'center',
            'selectors' => [
                '{{WRAPPER}} .tophive-text-effect span' => 'justify-content: {{VALUE}};',
            ],
        ]);

        $this->add_group_control(Group_Control_Typography::get_type(), [
            'name' => 'sub_text_typography',
            'label' => __('Typography', 'plugin-name'),
            'selector' => '{{WRAPPER}} .tophive-text-effect span, {{WRAPPER}} .tophive-text-effect span a',
        ]);

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        ?>
        <div class="tophive-text-effect-wrapper">
            <?php foreach($settings['lines'] as $line): 
                $link = $line['sub_text_link']['url'] ?? '';
                $target = $line['sub_text_link']['is_external'] ? ' target="_blank"' : '';
                $nofollow = $line['sub_text_link']['nofollow'] ? ' rel="nofollow"' : '';
            ?>
                <h1 class="tophive-text-effect">
                    <?php echo esc_html($line['main_text']); ?>
                    <span>
                        <?php if($link): ?>
                            <a href="<?php echo esc_url($link); ?>"<?php echo $target.$nofollow; ?>>
                                <?php echo esc_html($line['sub_text']); ?>
                            </a>
                        <?php else: ?>
                            <?php echo esc_html($line['sub_text']); ?>
                        <?php endif; ?>
                    </span>
                </h1>
            <?php endforeach; ?>
        </div>

        <style>
        .tophive-text-effect-wrapper {
            display: flex;
            flex-direction: column;
            justify-content: center;
            gap: 2vw;
        }
        .tophive-text-effect {
            font-family: 'Poppins', sans-serif;
            font-size: 10vw;
            letter-spacing: -.01em;
            line-height: 100%;
            margin: 0;
            width: 100%;
            color: rgba(182,182,182,0.2);
            background: linear-gradient(to right, #b6b6b6, #b6b6b6) no-repeat;
            -webkit-background-clip: text;
            background-clip: text;
            background-size: 0%;
            transition: background-size cubic-bezier(.1,.5,.5,1) 0.5s;
            border-bottom: 1px solid #2F2B28;
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .tophive-text-effect span {
            position: absolute;
            width: 100%;
            height: 100%;
            display: flex; 
            justify-content: center;
            align-items: center;
            background-color: #4246ce;
            color: #0D0D0D;
            clip-path: polygon(0 50%, 100% 50%, 100% 50%, 0 50%);
            transform-origin: center;
            transition: all cubic-bezier(.1,.5,.5,1) 0.4s;
        }
        .tophive-text-effect span a {
            color: inherit;
            text-decoration: none;
            display: flex;
            justify-content: inherit;
            align-items: inherit;
            font-family: inherit;
            font-size: inherit;
            font-weight: inherit;
            line-height: inherit;
        }
        .tophive-text-effect:hover > span {
            clip-path: polygon(0 0, 100% 0, 100% 100%, 0 100%);
        }
        </style>

        <script>
        (function($, elementorModules) {
            const TextEffectHandler = elementorModules.frontend.handlers.Base.extend({
                onInit: function() {
                    this.runTextEffect();
                },
                runTextEffect: function() {
                    const $widget = this.$element;
                    const $textElements = $widget.find('.tophive-text-effect');

                    if (typeof gsap === 'undefined' || typeof ScrollTrigger === 'undefined') return;

                    gsap.registerPlugin(ScrollTrigger);

                    $textElements.each(function() {
                        gsap.to(this, {
                            backgroundSize: '100%',
                            ease: 'none',
                            scrollTrigger: {
                                trigger: this,
                                start: 'center 80%',
                                end: 'center 20%',
                                scrub: true,
                            }
                        });
                    });
                }
            });

            $(window).on('elementor/frontend/init', function(){
                elementorFrontend.elementsHandler.addHandler(TextEffectHandler, {
                    selector: '.elementor-widget-tophive-text-effect'
                });
            });
        })(jQuery, elementorModules);
        </script>
        <?php
    }
}

\Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Tophive_Text_Effect());
