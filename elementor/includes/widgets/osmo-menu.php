<?php
namespace My_Core_Plugin\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;

if (!defined('ABSPATH')) exit;

class Osmo_Menu extends Widget_Base {

    public function get_name() { return 'osmo_menu'; }
    public function get_title() { return __('OSMO Menu', 'my-core-plugin'); }
    public function get_icon() { return 'eicon-menu-bar'; }
    public function get_categories() { return ['th-general']; }

    protected function register_controls() {
        // ---------------- Menu Items ----------------
        $this->start_controls_section('menu_items_section', [
            'label' => __('Menu Items', 'my-core-plugin'),
        ]);

        $repeater = new \Elementor\Repeater();
        $repeater->add_control('label', [
            'label' => __('Label', 'my-core-plugin'),
            'type' => Controls_Manager::TEXT,
            'default' => 'Home',
        ]);
        $repeater->add_control('link', [
            'label' => __('Link', 'my-core-plugin'),
            'type' => Controls_Manager::URL,
            'default' => ['url' => '#'],
        ]);

        $this->add_control('menu_items', [
            'type' => Controls_Manager::REPEATER,
            'fields' => $repeater->get_controls(),
            'default' => [
                ['label'=>'Home','link'=>['url'=>'#']],
                ['label'=>'About','link'=>['url'=>'#']],
                ['label'=>'Services','link'=>['url'=>'#']],
                ['label'=>'Contact','link'=>['url'=>'#']],
            ],
            'title_field' => '{{{ label }}}',
        ]);
        $this->end_controls_section();

        // ---------------- Nav Position ----------------
        $this->start_controls_section('position_section', [
            'label' => __('Nav Position', 'my-core-plugin'),
            'tab' => Controls_Manager::TAB_CONTENT,
        ]);
        $this->add_control('nav_position', [
            'label' => __('Position Type', 'my-core-plugin'),
            'type' => Controls_Manager::SELECT,
            'options' => [
                'default' => __('Default', 'my-core-plugin'),
                'fixed' => __('Fixed', 'my-core-plugin'),
            ],
            'default' => 'fixed',
        ]);
        $this->add_control('position', [
            'label' => __('Order', 'my-core-plugin'),
            'type' => Controls_Manager::SELECT,
            'options' => [
                'bottom' => __('Bottom', 'my-core-plugin'),
                'top' => __('Top', 'my-core-plugin'),
            ],
            'default' => 'bottom',
        ]);
        $this->add_control('top_margin', [
            'label' => __('Top Margin (px)', 'my-core-plugin'),
            'type' => Controls_Manager::NUMBER,
            'default' => 20,
            'condition' => ['position' => 'top'],
        ]);
        $this->end_controls_section();

        // ---------------- Style ----------------
        $this->start_controls_section('style_section', [
            'label' => __('Style', 'my-core-plugin'),
            'tab' => Controls_Manager::TAB_STYLE,
        ]);

        $this->add_control('bg_color', [
            'label' => __('Background Color', 'my-core-plugin'),
            'type' => Controls_Manager::COLOR,
            'default' => 'rgba(26,26,26,0.6)',
        ]);
        // Nav padding (responsive)
        $this->add_responsive_control('nav_padding', [
            'label' => __('Nav Padding', 'my-core-plugin'),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => ['px','em','%'],
            'selectors' => [
                '{{WRAPPER}} .osmo-floating-bottom-nav' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]);

        // Border radius & shadows
        $this->add_group_control(Group_Control_Border::get_type(), [
            'name' => 'nav_border',
            'label' => __('Nav Border', 'my-core-plugin'),
            'selector' => '{{WRAPPER}} .osmo-floating-bottom-nav',
        ]);
        $this->add_group_control(Group_Control_Box_Shadow::get_type(), [
            'name' => 'nav_shadow',
            'label' => __('Nav Shadow', 'my-core-plugin'),
            'selector' => '{{WRAPPER}} .osmo-floating-bottom-nav',
        ]);
        $this->add_group_control(Group_Control_Box_Shadow::get_type(), [
            'name' => 'nav_hover_shadow',
            'label' => __('Nav Hover Shadow', 'my-core-plugin'),
            'selector' => '{{WRAPPER}} .osmo-floating-bottom-nav:hover',
        ]);
        $this->end_controls_section();

        // ---------------- Typography ----------------
        $this->start_controls_section('typography_section', [
            'label' => __('Typography', 'my-core-plugin'),
            'tab' => Controls_Manager::TAB_STYLE,
        ]);
        $this->add_control('icon_color', [
            'label' => __('Text Color', 'my-core-plugin'),
            'type' => Controls_Manager::COLOR,
            'default' => '#fff',
        ]);
        $this->add_control('icon_hover_color', [
            'label' => __('Text Hover Color', 'my-core-plugin'),
            'type' => Controls_Manager::COLOR,
            'default' => '#9d79ff',
        ]);
        $this->add_group_control(Group_Control_Typography::get_type(), [
            'name' => 'typography',
            'label' => __('Typography', 'my-core-plugin'),
            'selector' => '{{WRAPPER}} .floating-menu-items a',
        ]);
        $this->end_controls_section();

        // ---------------- Spotlights ----------------
        $this->start_controls_section('spotlights_section', [
            'label' => __('Spotlights', 'my-core-plugin'),
            'tab' => Controls_Manager::TAB_STYLE,
        ]);
        $this->add_control('spotlight_count', [
            'label' => __('Number of Spotlights', 'my-core-plugin'),
            'type' => Controls_Manager::NUMBER,
            'default' => 3,
            'min' => 1,
            'max' => 10,
        ]);
        $this->add_control('spotlight_type', [
            'label' => __('Spotlight Type', 'my-core-plugin'),
            'type' => Controls_Manager::SELECT,
            'options' => [
                'gradient1'=>'Gradient 1','gradient2'=>'Gradient 2',
                'gradient3'=>'Gradient 3','gradient4'=>'Gradient 4',
            ],
            'default' => 'gradient1',
        ]);
        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();

        // ---------- NAV POSITION ----------
        $position_css = '';
        $spotlight_y_offset = -40; // default bottom

        if ($settings['nav_position'] === 'fixed') {
            $position_css .= 'position: fixed; left: 50%; transform: translateX(-50%);';
            if ($settings['position'] === 'top') {
                $position_css .= 'top:' . $settings['top_margin'] . 'px;';
                $spotlight_y_offset = 40;
            } else {
                $position_css .= 'bottom: 20px;';
                $spotlight_y_offset = -40;
            }
        } else {
            // Default = normal flow
            $position_css .= 'position: relative;';
            $spotlight_y_offset = 0;
        }

        // ---------- GRADIENTS ----------
        $gradients_map = [
            'gradient1' => ["rgba(255,0,150,0.5)", "rgba(0,200,255,0.5)"],
            'gradient2' => ["rgba(255,255,0,0.5)", "rgba(0,255,100,0.5)"],
            'gradient3' => ["rgba(0,0,255,0.5)", "rgba(255,0,0,0.5)"],
            'gradient4' => ["rgba(255,150,0,0.5)", "rgba(0,255,255,0.5)"]
        ];
        $gradients = $gradients_map[$settings['spotlight_type']];
        $blur = 80;
        ?>

        <div class="osmo-floating-bottom-nav" role="navigation" style="<?php echo esc_attr($position_css); ?>">
            <ul class="floating-menu-items">
                <?php foreach($settings['menu_items'] as $i=>$item): ?>
                    <li data-index="<?php echo $i; ?>">
                        <a href="<?php echo esc_url($item['link']['url']); ?>">
                            <span class="menu-label"><?php echo esc_html($item['label']); ?></span>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
            <div class="nav-spotlights">
                <?php for($i=0;$i<$settings['spotlight_count'];$i++): ?>
                    <div class="spotlight" style="--size:<?php echo 120+($i*20); ?>px;"></div>
                <?php endfor; ?>
            </div>
        </div>

        <style>
        .osmo-floating-bottom-nav {
            width: max-content;
            background: <?php echo $settings['bg_color']; ?>;
            border-radius: var(--e-border-radius,30px);
            box-shadow: var(--e-box-shadow,0 8px 25px rgba(0,0,0,0.3));
            backdrop-filter: blur(15px);
            z-index: 9999;
            display: flex;
            justify-content: center;
            overflow: hidden;
        }
        .floating-menu-items { display: flex; list-style: none; margin: 0; padding: 0; align-items: center; }
        .floating-menu-items li { margin: 0 12px; }
        .floating-menu-items a {
            display: flex; flex-direction: column; align-items: center;
            color: <?php echo $settings['icon_color']; ?>;
            text-decoration: none; position: relative; padding: 4px 8px; cursor: pointer;
            transition: color 0.3s ease;
        }
        .floating-menu-items a:hover { color: <?php echo $settings['icon_hover_color']; ?>; }
        .nav-spotlights { position: absolute; top: 0; left: 0; width: 100%; height: 100%; pointer-events: none; }
        .nav-spotlights .spotlight {
            position: absolute; width: var(--size); height: var(--size);
            border-radius: 50%; filter: blur(<?php echo $blur; ?>px); mix-blend-mode: screen;
        }
        </style>

        <script>
        document.addEventListener('DOMContentLoaded', function(){
            const nav = document.querySelector('.osmo-floating-bottom-nav');
            const items = nav.querySelectorAll('.floating-menu-items li');
            const spotlights = nav.querySelectorAll('.nav-spotlights .spotlight');
            const positionOffset = <?php echo $spotlight_y_offset; ?>;
            const gradients = <?php echo json_encode($gradients); ?>;

            gsap.from(nav,{y:100,opacity:0,duration:1,ease:"power3.out"});
            gsap.from(items,{y:20,opacity:0,duration:0.8,stagger:0.1,ease:"power2.out",delay:0.3});

            spotlights.forEach((spot,i)=>{
                const x = Math.random() * nav.offsetWidth;
                const y = Math.random() * nav.offsetHeight;
                gsap.set(spot,{x:x,y:y,background:'radial-gradient(circle,'+gradients[i % gradients.length]+',transparent 70%)'});
                gsap.to(spot,{x:'+=60',y:'+='+(Math.random()*30-15+positionOffset),duration:5+Math.random()*3,yoyo:true,repeat:-1,ease:'sine.inOut',delay:Math.random()});
            });

            items.forEach((item,i)=>{
                const spot = spotlights[i % spotlights.length];
                item.addEventListener('mouseenter', ()=>{
                    const rect = item.getBoundingClientRect();
                    const navRect = nav.getBoundingClientRect();
                    const x = rect.left - navRect.left + rect.width/2 - spot.offsetWidth/2;
                    const y = rect.top - navRect.top + rect.height/2 - spot.offsetHeight/2;
                    gsap.to(spot,{x:x,y:y,duration:0.3,ease:'power2.out'});
                });
            });

            document.addEventListener('mousemove', e=>{
                const rect = nav.getBoundingClientRect();
                const x = e.clientX - rect.left;
                const y = e.clientY - rect.top;
                gsap.to(spotlights[0],{x:x-spotlights[0].offsetWidth/2,y:y-spotlights[0].offsetHeight/2,duration:0.3});
            });
        });
        </script>
        <?php
    }
}

if(class_exists('\Elementor\Plugin')){
    \Elementor\Plugin::instance()->widgets_manager->register(new Osmo_Menu());
}
