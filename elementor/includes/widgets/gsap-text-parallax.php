<?php
namespace My_Core_Plugin\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Color;

if (!defined('ABSPATH')) exit;

class GSAP_Text_Parallax extends Widget_Base {

    public function get_name() {
        return 'gsap_text_parallax';
    }

    public function get_title() {
        return esc_html__('Text Layers Parallax', 'my-core-plugin');
    }

    public function get_icon() {
        return 'eicon-text';
    }

    public function get_categories() {
        return ['th-general'];
    }

    public function get_script_depends() {
        return ['gsap'];
    }

    protected function register_controls() {
        // Content Section
        $this->start_controls_section('content_section', [
            'label' => __('Projects', 'my-core-plugin')
        ]);

        $repeater = new Repeater();

        // Project Title Control
        $repeater->add_control('project_title', [
            'label' => __('Project Title', 'my-core-plugin'),
            'type' => Controls_Manager::TEXT,
            'default' => 'Project Title',
        ]);

        // Projects List Control (Repeater)
        $this->add_control('projects_list', [
            'label' => __('Projects List', 'my-core-plugin'),
            'type' => Controls_Manager::REPEATER,
            'fields' => $repeater->get_controls(),
            'default' => [
                ['project_title' => 'COSMIC DEPTHS'],
                ['project_title' => 'STELLAR VISION'],
            ],
            'title_field' => '{{{ project_title }}}',
        ]);

        // Show Title Control
        $this->add_control('show_title', [
            'label' => __('Show Title', 'my-core-plugin'),
            'type' => Controls_Manager::SWITCHER,
            'label_on' => __('Yes', 'my-core-plugin'),
            'label_off' => __('No', 'my-core-plugin'),
            'default' => 'yes',
            'return_value' => 'yes',
        ]);

        // Show Parallax Layers Control
        $this->add_control('show_layers', [
            'label' => __('Show Parallax Layers', 'my-core-plugin'),
            'type' => Controls_Manager::SWITCHER,
            'label_on' => __('Yes', 'my-core-plugin'),
            'label_off' => __('No', 'my-core-plugin'),
            'default' => 'yes',
            'return_value' => 'yes',
        ]);

        $this->end_controls_section();

        // Title Typography & Color Section
        $this->start_controls_section('title_style_section', [
            'label' => __('Title Typography & Color', 'my-core-plugin'),
            'tab' => Controls_Manager::TAB_STYLE
        ]);

        // Title Typography Control
        $this->add_group_control(Group_Control_Typography::get_type(), [
            'name' => 'title_typography',
            'label' => __('Title Typography', 'my-core-plugin'),
            'selector' => '{{WRAPPER}} .project-title',  // Targeting the <h2> for project title
        ]);

        // Title Color Control
        $this->add_control('title_color', [
            'label' => __('Title Color', 'my-core-plugin'),
            'type' => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .project-title' => 'color: {{VALUE}};',
            ],
        ]);

        $this->end_controls_section();

        // Layer Typography & Color Section
        $this->start_controls_section('layer_style_section', [
            'label' => __('Layer Typography & Color', 'my-core-plugin'),
            'tab' => Controls_Manager::TAB_STYLE
        ]);

        // Layer Typography Control
        $this->add_group_control(Group_Control_Typography::get_type(), [
            'name' => 'layer_typography',
            'label' => __('Layer Typography', 'my-core-plugin'),
            'selector' => '{{WRAPPER}} .parallax-layers .layer h2',  // Targeting <h2> inside each parallax layer
        ]);

        // Layer Color Control
        $this->add_control('layer_color', [
            'label' => __('Layer Text Color', 'my-core-plugin'),
            'type' => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .parallax-layers .layer h2' => 'color: {{VALUE}};',
            ],
        ]);

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        if (empty($settings['projects_list'])) return;
        ?>
        <div class="gsap-title-wrapper">
            <?php foreach ($settings['projects_list'] as $index => $project): ?>
                <div class="project-title-item" data-index="<?php echo esc_attr($index); ?>">
                    <?php if ('yes' === $settings['show_title']): ?>
                        <!-- Only Show Title if Toggle is On -->
                        <h2 class="project-title"><?php echo esc_html($project['project_title']); ?></h2>
                    <?php endif; ?>

                    <?php if ('yes' === $settings['show_layers']): ?>
                        <!-- Only Show Parallax Layers if Toggle is On -->
                        <div class="parallax-layers">
                            <?php
                            $layers = 6; // Number of layers
                            for ($i = 0; $i < $layers; $i++):
                                $opacity = 0.05 + ($i * 0.08);
                                $offset = $i * 8;
                            ?>
                                <span class="layer" style="opacity:<?php echo $opacity; ?>; transform:translateY(<?php echo $offset; ?>px);">
                                    <h2><?php echo esc_html($project['project_title']); ?></h2> <!-- Wrap text in <h2> tag -->
                                </span>
                            <?php endfor; ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>

        <style>
        .gsap-title-wrapper { position: relative; display: flex; flex-direction: column; gap: 4rem; }
        .project-title-item { position: relative; overflow: visible; }
        .project-title { font-weight: 900; position: relative; z-index: 10; margin: 0; }
        .parallax-layers { position: absolute; top: 0; left: 0; width: 100%; pointer-events: none; z-index: 1; }
        .parallax-layers .layer {
            display: block;
            font-weight: 900;
            position: absolute;
            top: 0;
            left: 0;
            white-space: nowrap;
        }
        </style>

        <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.13.0/gsap.min.js"></script>
        <script>
        (function(){
            const items = gsap.utils.toArray(".gsap-title-wrapper .project-title-item");

            items.forEach(item=>{
                const layers = item.querySelectorAll(".parallax-layers .layer");

                window.addEventListener("scroll", ()=>{
                    layers.forEach((layer, i)=>{
                        const speed = 0.05 + i*0.08;
                        gsap.to(layer, {
                            y: window.scrollY * speed,
                            duration: 0.3,
                            ease: "power1.out"
                        });
                    });
                });
            });
        })();
        </script>
        <?php
    }
}

// Register the widget with Elementor
\Elementor\Plugin::instance()->widgets_manager->register_widget_type(new GSAP_Text_Parallax());
