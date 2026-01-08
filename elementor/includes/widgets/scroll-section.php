<?php
/**
 * Plugin Name: Elementor Scroll Sections OSmo
 * Description: Smooth inertia-based scroll between Elementor repeater sections with OSmo-like feel.
 * Version: 1.0.0
 * Author: Your Name
 */

namespace My_Core_Plugin\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Repeater;

if (!defined('ABSPATH')) exit;

class Scroll_Sections extends Widget_Base {

    public function get_name() { return 'scroll_sections_osmo'; }
    public function get_title() { return __('Scroll Sections OSmo', 'plugin-name'); }
    public function get_icon() { return 'eicon-scroll'; }
    public function get_categories() { return ['th-general']; }
    public function get_keywords() { return ['scroll', 'section', 'osmo', 'smooth', 'gsap']; }

    protected function register_controls() {
        $repeater = new Repeater();
        $repeater->add_control(
            'template_id',
            [
                'label' => __('Select Template', 'plugin-name'),
                'type' => Controls_Manager::SELECT2,
                'options' => $this->get_elementor_templates(),
                'label_block' => true,
            ]
        );

        $this->start_controls_section(
            'sections_list',
            ['label' => __('Sections', 'plugin-name'), 'tab' => Controls_Manager::TAB_CONTENT]
        );

        $this->add_control(
            'sections',
            [
                'label' => __('Scroll Sections', 'plugin-name'),
                'type' => Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
                'default' => [],
                'title_field' => '{{{ template_id }}}',
            ]
        );
        $this->end_controls_section();

        $this->start_controls_section(
            'scroll_settings',
            ['label' => __('Scroll Settings', 'plugin-name'), 'tab' => Controls_Manager::TAB_CONTENT]
        );
        $this->add_control(
            'scroll_speed',
            [
                'label' => __('Scroll Speed', 'plugin-name'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [],
                'range' => ['px' => ['min' => 0.3, 'max' => 2, 'step' => 0.1]],
                'default' => ['size' => 1],
            ]
        );
        $this->end_controls_section();
    }

    private function get_elementor_templates() {
        $options = [];
        $templates = get_posts(['post_type' => 'elementor_library', 'numberposts' => -1]);
        foreach ($templates as $t) $options[$t->ID] = $t->post_title;
        return $options;
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        if (empty($settings['sections'])) {
            echo '<p>' . esc_html__('No sections added yet.', 'plugin-name') . '</p>';
            return;
        }

        $scroll_speed = !empty($settings['scroll_speed']['size']) ? floatval($settings['scroll_speed']['size']) : 1;

        echo '<div class="osmo-scroll-wrapper"><div class="osmo-scroll-content">';
        foreach ($settings['sections'] as $item) {
            echo '<section class="osmo-scroll-section">';
            if (!empty($item['template_id'])) {
                echo \Elementor\Plugin::instance()->frontend->get_builder_content_for_display($item['template_id']);
            }
            echo '</section>';
        }
        echo '</div></div>';
        ?>

        <!-- Inline JS -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js"></script>
        <script>
        document.addEventListener("DOMContentLoaded", () => {
            const wrapper = document.querySelector(".osmo-scroll-wrapper");
            const content = document.querySelector(".osmo-scroll-content");
            const sections = gsap.utils.toArray(".osmo-scroll-section");
            const scrollSpeed = <?php echo esc_js($scroll_speed); ?>;
            if (!wrapper || !content || sections.length === 0) return;

            let scrollTarget = 0, scrollCurrent = 0;
            const sectionOffsets = [];
            const updateOffsets = () => {
                let offset = 0;
                sectionOffsets.length = 0;
                sections.forEach(sec => {
                    sectionOffsets.push(offset);
                    offset += sec.offsetHeight;
                });
                content.style.height = offset + "px";
            };
            updateOffsets();
            window.addEventListener("resize", updateOffsets);

            // Smooth inertia scroll
            gsap.ticker.add(() => {
                scrollCurrent += (scrollTarget - scrollCurrent) * 0.08;
                content.style.transform = `translateY(${-scrollCurrent}px)`;
            });

            // Wheel event
            window.addEventListener("wheel", e => {
                e.preventDefault();
                scrollTarget += e.deltaY * scrollSpeed;
                scrollTarget = Math.max(0, Math.min(scrollTarget, content.scrollHeight - window.innerHeight));
            }, { passive: false });

            // Touch support
            let startY = 0;
            wrapper.addEventListener("touchstart", e => startY = e.touches[0].clientY);
            wrapper.addEventListener("touchmove", e => {
                const delta = startY - e.touches[0].clientY;
                scrollTarget += delta * scrollSpeed;
                scrollTarget = Math.max(0, Math.min(scrollTarget, content.scrollHeight - window.innerHeight));
                startY = e.touches[0].clientY;
            });

            // Snap after stop
            let scrollTimeout;
            const snapToClosest = () => {
                const distances = sectionOffsets.map(o => Math.abs(o - scrollCurrent));
                const closest = distances.indexOf(Math.min(...distances));
                gsap.to(content, { y: -sectionOffsets[closest], duration: 0.8, ease: "power3.out",
                    onUpdate: () => scrollCurrent = -gsap.getProperty(content, "y")
                });
            };

            gsap.ticker.add(() => {
                clearTimeout(scrollTimeout);
                scrollTimeout = setTimeout(snapToClosest, 120);
            });
        });
        </script>

        <!-- Inline CSS -->
        <style>
        .osmo-scroll-wrapper {
            position: relative;
            width: 100%;
            height: 100vh;
            overflow: hidden;
        }
        .osmo-scroll-content {
            position: absolute;
            width: 100%;
            top: 0;
            left: 0;
            will-change: transform;
        }
        .osmo-scroll-section {
            width: 100%;
            height: 100vh;
            overflow: hidden;
            position: relative;
        }
        </style>
        <?php
    }
}

\Elementor\Plugin::instance()->widgets_manager->register(new \My_Core_Plugin\Widgets\Scroll_Sections());
