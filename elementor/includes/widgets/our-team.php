<?php
//version - 1.0
/**
 * Elements Name: GSAP Team Widget
 * Description: Elementor widget for a GSAP-powered team member marquee with hover-to-slow and full style controls.
 */

namespace My_Core_Plugin\Widgets;

if (!defined('ABSPATH')) {
    exit();
}

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;

class Our_Team extends Widget_Base {

    public function get_name() {
        return 'gsap_team_marquee';
    }

    public function get_title() {
        return __('Our Team', 'my-core-plugin');
    }

    public function get_icon() {
        return 'eicon-nested-carousel';
    }

    public function get_categories() {
        return ['th-general'];
    }

    public function get_style_depends() {
        return ['tophive-elements-css'];
    }

    public function get_script_depends() {
        return ['tophive-elementor-bundle'];
    }

    protected function _register_controls()
    {
        // ---------------- Settings Section ----------------
        $this->start_controls_section('section_settings', [
            'label' => __('Settings', 'my-core-plugin'),
            'tab' => Controls_Manager::TAB_CONTENT,
        ]);

        $this->add_control('scroll_speed', [
            'label' => __('Scroll Speed (seconds)', 'my-core-plugin'),
            'type' => Controls_Manager::NUMBER,
            'min' => 5,
            'max' => 60,
            'step' => 1,
            'default' => 20,
        ]);

        $this->add_control('hover_color_effect', [
            'label' => __('Image Color Effect', 'my-core-plugin'),
            'type' => Controls_Manager::SELECT,
            'default' => 'grayscale',
            'options' => [
                'none' => 'None',
                'grayscale' => 'Grayscale',
                'sepia' => 'Sepia',
                'brightness' => 'Brightness',
                'saturate' => 'Saturate',
                'invert' => 'Invert',
            ],
        ]);

        $this->add_control('show_arrows', [
            'label' => __('Show Arrows', 'my-core-plugin'),
            'type' => Controls_Manager::SWITCHER,
            'label_on' => __('Show', 'my-core-plugin'),
            'label_off' => __('Hide', 'my-core-plugin'),
            'return_value' => 'yes',
            'default' => 'yes',
        ]);

        $this->add_control('enable_scroll', [
            'label' => __('Enable Auto Scroll', 'my-core-plugin'),
            'type' => Controls_Manager::SWITCHER,
            'label_on' => __('Yes', 'my-core-plugin'),
            'label_off' => __('No', 'my-core-plugin'),
            'return_value' => 'yes',
            'default' => 'yes',
        ]);

        $this->end_controls_section();

        // ---------------- Content Tab ----------------
        $this->start_controls_section('section_team_members', [
            'label' => __('Team Members', 'my-core-plugin'),
            'tab' => Controls_Manager::TAB_CONTENT,
        ]);

        $repeater = new Repeater();

        $repeater->add_control('team_image', [
            'label' => __('Team Image', 'my-core-plugin'),
            'type' => Controls_Manager::MEDIA,
            'default' => [
                'url' => \Elementor\Utils::get_placeholder_image_src(),
            ],
        ]);

        $repeater->add_control('team_name', [
            'label' => __('Name', 'my-core-plugin'),
            'type' => Controls_Manager::TEXT,
            'default' => 'John Doe',
        ]);

        $repeater->add_control('team_designation', [
            'label' => __('Designation', 'my-core-plugin'),
            'type' => Controls_Manager::TEXT,
            'default' => 'CEO',
        ]);

        $repeater->add_control('team_link', [
            'label' => __('Link', 'my-core-plugin'),
            'type' => Controls_Manager::URL,
            'placeholder' => __('https://your-link.com', 'my-core-plugin'),
            'show_external' => true,
            'default' => ['url' => ''], // Set default to empty to avoid invalid links
        ]);

        $this->add_control('team_members', [
            'label' => __('Team Members', 'my-core-plugin'),
            'type' => Controls_Manager::REPEATER,
            'fields' => $repeater->get_controls(),
            'title_field' => '{{{ team_name }}}',
            'default' => [
                [
                    'team_name' => 'John Doe',
                    'team_designation' => 'CEO',
                ],
            ],
        ]);

        $this->end_controls_section();

        // ---------------- Card Style Section ----------------
        $this->start_controls_section('section_card_style', [
            'label' => __('Card Style', 'my-core-plugin'),
            'tab' => Controls_Manager::TAB_STYLE,
        ]);

        $this->add_control('card_gap', [
            'label' => __('Card Gap', 'my-core-plugin'),
            'type' => Controls_Manager::SLIDER,
            'size_units' => ['px', '%'],
            'range' => [
                'px' => ['min' => 0, 'max' => 100],
                '%' => ['min' => 0, 'max' => 50],
            ],
            'selectors' => [
                '{{WRAPPER}} .marquee' => 'gap: {{SIZE}}{{UNIT}};',
            ],
            'default' => [
                'unit' => 'px',
                'size' => 20,
            ],
        ]);

        $this->add_group_control(Group_Control_Border::get_type(), [
            'name' => 'card_border',
            'label' => __('Card Border', 'my-core-plugin'),
            'selector' => '{{WRAPPER}} .member',
        ]);

        $this->add_control('card_radius', [
            'label' => __('Card Border Radius', 'my-core-plugin'),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => ['px', '%', 'em'],
            'selectors' => [
                '{{WRAPPER}} .member, {{WRAPPER}} .member-img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]);

        $this->add_group_control(Group_Control_Box_Shadow::get_type(), [
            'name' => 'card_shadow',
            'label' => __('Card Box Shadow', 'my-core-plugin'),
            'selector' => '{{WRAPPER}} .member',
        ]);

        $this->add_control('card_background', [
            'label' => __('Card Background', 'my-core-plugin'),
            'type' => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .member' => 'background-color: {{VALUE}};',
            ],
        ]);

        $this->end_controls_section();

        // ---------------- Text Style Section ----------------
        $this->start_controls_section('section_text_style', [
            'label' => __('Text Style', 'my-core-plugin'),
            'tab' => Controls_Manager::TAB_STYLE,
        ]);

        $this->add_group_control(Group_Control_Typography::get_type(), [
            'name' => 'name_typography',
            'label' => __('Name Typography', 'my-core-plugin'),
            'selector' => '{{WRAPPER}} .member-title',
        ]);

        $this->add_control('name_color', [
            'label' => __('Name Color', 'my-core-plugin'),
            'type' => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .member-title' => 'color: {{VALUE}};',
            ],
        ]);

        $this->add_group_control(Group_Control_Typography::get_type(), [
            'name' => 'designation_typography',
            'label' => __('Designation Typography', 'my-core-plugin'),
            'selector' => '{{WRAPPER}} .member-subtitle',
        ]);

        $this->add_control('designation_color', [
            'label' => __('Designation Color', 'my-core-plugin'),
            'type' => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .member-subtitle' => 'color: {{VALUE}};',
            ],
        ]);

        $this->add_control('card_style', [
            'label' => __('Card Style', 'my-core-plugin'),
            'type' => Controls_Manager::SELECT,
            'default' => 'hover',
            'options' => [
                'hover' => 'Content on Hover',
                'always-show' => 'Content Always Shown',
            ],
        ]);

        $this->add_control('content_background', [
            'label' => __('Content Background', 'my-core-plugin'),
            'type' => Controls_Manager::SELECT,
            'default' => 'solid',
            'options' => [
                'solid' => 'Solid',
                'gradient' => 'Gradient',
                'glass' => 'Glass',
            ],
        ]);

        $this->end_controls_section();

        // ---------------- Arrow Style Section ----------------
        $this->start_controls_section('section_arrow_style', [
            'label' => __('Arrow Style', 'my-core-plugin'),
            'tab' => Controls_Manager::TAB_STYLE,
        ]);

        $this->add_responsive_control('arrow_size', [
            'label' => __('Arrow Size', 'my-core-plugin'),
            'type' => Controls_Manager::SLIDER,
            'size_units' => ['px'],
            'range' => ['px' => ['min' => 20, 'max' => 120]],
            'selectors' => [
                '{{WRAPPER}} .marquee-arrow' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
            ],
        ]);

        $this->add_responsive_control('arrow_padding', [
            'label' => __('Arrow Padding', 'my-core-plugin'),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => ['px', '%', 'em'],
            'selectors' => [
                '{{WRAPPER}} .marquee-arrow' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]);

        $this->add_responsive_control('arrow_border_radius', [
            'label' => __('Border Radius', 'my-core-plugin'),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => ['px', '%', 'em'],
            'selectors' => [
                '{{WRAPPER}} .marquee-arrow' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]);

        $this->add_control('arrow_background', [
            'label' => __('Arrow Background', 'my-core-plugin'),
            'type' => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .marquee-arrow' => 'background-color: {{VALUE}};',
            ],
        ]);

        $this->add_control('arrow_background_hover', [
            'label' => __('Arrow Hover Background', 'my-core-plugin'),
            'type' => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .marquee-arrow:hover' => 'background-color: {{VALUE}};',
            ],
        ]);

        $this->add_control('arrow_icon_color', [
            'label' => __('Icon Color', 'my-core-plugin'),
            'type' => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .marquee-arrow svg path' => 'fill: {{VALUE}};',
            ],
        ]);

        $this->add_control('arrow_icon_hover_color', [
            'label' => __('Hover Icon Color', 'my-core-plugin'),
            'type' => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .marquee-arrow:hover svg path' => 'fill: {{VALUE}};',
            ],
        ]);

        $this->add_group_control(Group_Control_Box_Shadow::get_type(), [
            'name' => 'arrow_box_shadow',
            'selector' => '{{WRAPPER}} .marquee-arrow',
        ]);

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        $speed          = !empty($settings['scroll_speed']) ? $settings['scroll_speed'] : 20;
        $hover_effect   = !empty($settings['hover_color_effect']) ? $settings['hover_color_effect'] : 'grayscale';
        $show_arrows    = !empty($settings['show_arrows']) && $settings['show_arrows'] === 'yes';
        $enable_scroll  = !empty($settings['enable_scroll']) && $settings['enable_scroll'] === 'yes';
        $card_style     = !empty($settings['card_style']) ? $settings['card_style'] : 'hover';
        $content_bg     = !empty($settings['content_background']) ? $settings['content_background'] : 'solid';

        $this->add_render_attribute('wrapper', [
            'class' => 'gsap-team-marquee-wrapper',
            'data-settings' => wp_json_encode([
                'enable_scroll' => $enable_scroll,
                'speed' => $speed,
                'show_arrows' => $show_arrows,
            ]),
        ]);
        ?>
        <div <?php echo $this->get_render_attribute_string('wrapper'); ?>>
            <div class="marquee-wrapper">
                <div class="marquee">
                    <?php foreach ($settings['team_members'] as $index => $member):
                        $link_key = 'link_' . $index;
                        $tag = 'div';
                        if (!empty($member['team_link']['url']) && '#' !== $member['team_link']['url']) {
                            $tag = 'a';
                            $this->add_link_attributes($link_key, $member['team_link']);
                        }
                        ?>
                        <div class="marquee-item elementor-repeater-item-<?php echo esc_attr($member['_id']); ?>">
                            <article class="member">
                                <<?php echo $tag; ?> <?php echo $this->get_render_attribute_string($link_key); ?> class="member-link">
                                    <figure class="member-img-wrapper">
                                        <img class="member-img <?php echo esc_attr($hover_effect); ?>"
                                             src="<?php echo esc_url($member['team_image']['url']); ?>"
                                             alt="<?php echo esc_attr($member['team_name']); ?>">
                                    </figure>
                                    <figcaption class="member-details <?php echo esc_attr($card_style); ?> member-details-bg-<?php echo esc_attr($content_bg); ?>">
                                        <h3 class="member-title"><?php echo esc_html($member['team_name']); ?></h3><p class="member-subtitle"><?php echo esc_html($member['team_designation']); ?></p>
                                    </figcaption>
                                </<?php echo $tag; ?>>
                            </article>
                        </div>
                    <?php endforeach; ?>
                </div>

                <?php if ($show_arrows): ?>
                    <button type="button" class="marquee-arrow arrow-prev" aria-label="Previous"><svg width="24" height="24" viewBox="0 0 24 24"><path d="M20 .755l-14.374 11.245 14.374 11.219-.619.781-15.381-12 15.391-12 .609.755z"/></svg></button>
                    <button type="button" class="marquee-arrow arrow-next" aria-label="Next"><svg width="24" height="24" viewBox="0 0 24 24"><path d="M4 .755l14.374 11.245-14.374 11.219.619.781 15.381-12-15.391-12-.609.755z"/></svg></button>
                <?php endif; ?>
            </div>
        </div>
        <?php
    }
}
