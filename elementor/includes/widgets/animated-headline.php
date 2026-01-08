<?php
namespace My_Core_Plugin\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Text_Shadow;

if ( ! defined( 'ABSPATH' ) ) exit;

class Animated_Headline extends Widget_Base {

    public function get_name() {
        return 'animated_headline';
    }

    public function get_title() {
        return __( 'Animated Headline', 'my-core-plugin' );
    }

    public function get_icon() {
        return 'eicon-wordart';
    }

    public function get_categories() {
        return [ 'th-general' ];
    }

    protected function _register_controls() {

        // Content Section
        $this->start_controls_section(
            'content_section',
            [
                'label' => __( 'Content', 'my-core-plugin' ),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'pre_text',
            [
                'label' => __( 'Pre Text', 'my-core-plugin' ),
                'type' => Controls_Manager::TEXT,
                'default' => 'My',
            ]
        );

        $repeater = new \Elementor\Repeater();

        $repeater->add_control(
            'word',
            [
                'label' => __( 'Word', 'my-core-plugin' ),
                'type' => Controls_Manager::TEXT,
                'default' => 'Excellent',
            ]
        );

        $repeater->add_control(
            'animation_type',
            [
                'label' => __( 'Animation Type', 'my-core-plugin' ),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'type' => 'Type',
                    'slide' => 'Slide',
                    'zoom' => 'Zoom',
                    'rotate' => 'Rotate',
                ],
                'default' => 'type',
            ]
        );

        $repeater->add_control(
            'easing',
            [
                'label' => __( 'Animation Easing', 'my-core-plugin' ),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'power1.out' => 'Power1 Out',
                    'power2.out' => 'Power2 Out',
                    'power3.out' => 'Power3 Out',
                    'elastic.out(1,0.5)' => 'Elastic Out',
                ],
                'default' => 'power2.out',
            ]
        );

        $repeater->add_control(
            'word_color',
            [
                'label' => __( 'Word Color', 'my-core-plugin' ),
                'type' => Controls_Manager::COLOR,
                'default' => '#6a5acd',
            ]
        );

        $this->add_control(
            'animated_words',
            [
                'label' => __( 'Animated Words', 'my-core-plugin' ),
                'type' => Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
                'default' => [
                    ['word' => 'Excellent', 'animation_type' => 'type', 'easing'=>'power2.out', 'word_color'=>'#6a5acd'],
                    ['word' => 'Creative', 'animation_type' => 'slide', 'easing'=>'power2.out', 'word_color'=>'#ff4500'],
                    ['word' => 'Amazing', 'animation_type' => 'zoom', 'easing'=>'power2.out', 'word_color'=>'#32cd32'],
                ],
                'title_field' => '{{{ word }}}',
            ]
        );

        $this->add_control(
            'post_text',
            [
                'label' => __( 'Post Text', 'my-core-plugin' ),
                'type' => Controls_Manager::TEXT,
                'default' => 'Website',
            ]
        );

        $this->end_controls_section();

        // Animation Settings
        $this->start_controls_section(
            'animation_settings',
            [
                'label' => __( 'Animation Settings', 'my-core-plugin' ),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'letter_delay',
            [
                'label' => __( 'Letter Delay (s)', 'my-core-plugin' ),
                'type' => Controls_Manager::NUMBER,
                'min' => 0.01,
                'max' => 1,
                'step' => 0.01,
                'default' => 0.05,
            ]
        );

        $this->add_control(
            'word_interval',
            [
                'label' => __( 'Word Rotation Interval (ms)', 'my-core-plugin' ),
                'type' => Controls_Manager::NUMBER,
                'min' => 500,
                'max' => 10000,
                'step' => 100,
                'default' => 3000,
            ]
        );

        $this->add_control(
            'cursor_show',
            [
                'label' => __( 'Show Cursor', 'my-core-plugin' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __( 'Show', 'my-core-plugin' ),
                'label_off' => __( 'Hide', 'my-core-plugin' ),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'cursor_color',
            [
                'label' => __( 'Cursor Color', 'my-core-plugin' ),
                'type' => Controls_Manager::COLOR,
                'default' => '#6a5acd',
            ]
        );

        $this->add_control(
            'letter_spacing',
            [
                'label' => __( 'Letter Spacing (px)', 'my-core-plugin' ),
                'type' => Controls_Manager::NUMBER,
                'min' => 0,
                'max' => 20,
                'step' => 0.5,
                'default' => 0,
            ]
        );

        $this->end_controls_section();

        // Style Section (Typography, Color, Stroke, Alignment)
        $this->start_controls_section(
            'style_section',
            [
                'label' => __( 'Style', 'my-core-plugin' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        // Text Color
        $this->add_control(
            'text_color',
            [
                'label' => __( 'Text Color', 'my-core-plugin' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} h1.headline' => 'color: {{VALUE}};',
                ],
            ]
        );

        // Text Stroke (like Elementor)
        $this->add_group_control(
            Group_Control_Text_Shadow::get_type(),
            [
                'name' => 'text_stroke',
                'label' => __( 'Text Stroke', 'my-core-plugin' ),
                'selector' => '{{WRAPPER}} h1.headline',
            ]
        );

        // Typography
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'headline_typography',
                'label' => __( 'Typography', 'my-core-plugin' ),
                'selector' => '{{WRAPPER}} h1.headline',
            ]
        );

        // Text Alignment
        $this->add_responsive_control(
            'headline_alignment',
            [
                'label' => __( 'Alignment', 'my-core-plugin' ),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => __( 'Left', 'my-core-plugin' ),
                        'icon' => 'eicon-text-align-left',
                    ],
                    'center' => [
                        'title' => __( 'Center', 'my-core-plugin' ),
                        'icon' => 'eicon-text-align-center',
                    ],
                    'right' => [
                        'title' => __( 'Right', 'my-core-plugin' ),
                        'icon' => 'eicon-text-align-right',
                    ],
                ],
                'default' => 'center',
                'selectors' => [
                    '{{WRAPPER}} h1.headline' => 'text-align: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        $pre_text = esc_html($settings['pre_text']);
        $post_text = esc_html($settings['post_text']);
        $words = $settings['animated_words'] ?? [];
        if (is_string($words)) $words = json_decode($words,true) ?: [];
        if (!is_array($words)) $words = [];

        $letter_delay = floatval($settings['letter_delay']);
        $word_interval = intval($settings['word_interval']);
        $cursor_color = esc_attr($settings['cursor_color']);
        $letter_spacing = floatval($settings['letter_spacing']);
        $cursor_show = ($settings['cursor_show'] ?? '') === 'yes';

        $unique_id = 'animated-headline-' . $this->get_id();

        echo '<h1 class="headline" style="letter-spacing:' . $letter_spacing . 'px;">';
        echo $pre_text . ' <span class="type-animation" id="' . $unique_id . '">';
        foreach ($words as $item) {
            $word = esc_html($item['word'] ?? '');
            $animation = esc_attr($item['animation_type'] ?? 'type');
            $easing = esc_attr($item['easing'] ?? 'power2.out');
            $word_color = esc_attr($item['word_color'] ?? '#6a5acd');

            echo '<span class="word ' . $animation . '" data-easing="' . $easing . '" style="color:' . $word_color . ';">';
            foreach (str_split($word) as $letter) {
                echo '<span>' . esc_html($letter) . '</span>';
            }
            echo '</span>';
        }
        echo '</span> ' . $post_text;
        echo '</h1>';

        ?>
        <style>
        h1.headline { 
            font-size: clamp(2.5rem,5vw,4.5rem); 
            display: inline-flex; 
            gap: 10px; 
            align-items: center; 
            text-align:center; 
            margin:0; 
            font-family: Rosario,sans-serif; 
        }
        #<?= $unique_id ?> { position: relative; display: inline-block; overflow: hidden; }
        #<?= $unique_id ?> .word { position: absolute; left: 0; top: 0; display: flex; opacity: 0; white-space: nowrap; }
        #<?= $unique_id ?> .word.active { opacity: 1; position: relative; }
        #<?= $unique_id ?> .word span { display: inline-block; opacity: 0; }
        <?php if($cursor_show): ?>
        #<?= $unique_id ?>:after { 
            content: ''; 
            position: absolute; 
            right: 0; top: 0; bottom: 0; 
            width: 0.08em; 
            border-right: 2px solid <?= $cursor_color ?>; 
            animation: blink-<?= $unique_id ?> 0.7s infinite; 
        }
        @keyframes blink-<?= $unique_id ?> { 0%,100%{border-color:transparent;}50%{border-color:<?= $cursor_color ?>;} }
        <?php endif; ?>
        </style>

        <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.15.0/gsap.min.js"></script>
        <script>
        (function(){
            const container = document.getElementById('<?= $unique_id ?>');
            if(!container) return;

            const words = Array.from(container.querySelectorAll('.word'));
            if(!words.length) return;

            const letterDelay = <?= $letter_delay ?>;
            const wordInterval = <?= $word_interval ?>;
            let current = 0;

            function showWord(index){
                words.forEach(w => { 
                    w.style.opacity=0; 
                    w.classList.remove('active'); 
                    w.style.position='absolute'; 
                    w.style.width='auto'; 
                });
                const word = words[index];
                word.style.opacity = 1;
                word.classList.add('active');
                word.style.position='relative';

                const letters = Array.from(word.querySelectorAll('span'));
                const easing = word.dataset.easing || 'power2.out';
                const animType = word.classList.contains('slide') ? 'slide' :
                                 word.classList.contains('zoom') ? 'zoom' :
                                 word.classList.contains('rotate') ? 'rotate' : 'type';

                if(animType==='type'){
                    const fullWidth = letters.reduce((w,l)=>w+l.offsetWidth,0) + (letters.length*2);
                    word.style.width = '0px';
                    gsap.to(word, {width: fullWidth, duration: letters.length*letterDelay, ease:easing});
                    letters.forEach((l,i)=>{
                        gsap.to(l, {opacity:1, duration:0.1, delay:i*letterDelay});
                    });
                } else {
                    letters.forEach(l => gsap.set(l, {opacity:0, y:0, scale:1, rotationX:0}));
                    letters.forEach((l,i)=>{
                        let from={opacity:0}, to={opacity:1,duration:0.4,ease:easing,delay:i*letterDelay};
                        if(animType==='slide'){ from.y=-30; to.y=0; }
                        if(animType==='zoom'){ from.scale=2; to.scale=1; }
                        if(animType==='rotate'){ from.rotationX=90; to.rotationX=0; }
                        gsap.fromTo(l, from, to);
                    });
                }
            }

            showWord(current);

            if(words.length>1){
                setInterval(()=>{
                    current = (current+1) % words.length;
                    showWord(current);
                }, wordInterval);
            }
        })();
        </script>
        <?php
    }
}

// Register widget
\Elementor\Plugin::instance()->widgets_manager->register( new \My_Core_Plugin\Widgets\Animated_Headline() );
