<?php
/**
 * Plugin Name: GSAP Menu Pro Widget
 * Description: Fullscreen GSAP Menu for Elementor — links/socials left, images right, hover effect, CTA button, hamburger-to-cross animation, full style controls, logo upload, animated menu images.
 * Version: 1.0
 * Author: Tophive
 */

namespace My_Core_Plugin\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Text_Stroke;
use Elementor\Utils;

if (!defined('ABSPATH')) exit;

class Gsap_Menu_Widget extends Widget_Base {

    public function get_name() { return 'cGsap_Menu_Widget'; }
    public function get_title() { return 'GSAP Menu Pro'; }
    public function get_icon() { return 'eicon-kit-parts'; }
    public function get_categories() { return ['th-general']; }

    protected function register_controls() {

        // ================= Logo =================
        $this->start_controls_section('logo_section', [
            'label' => 'Logo',
            'tab' => Controls_Manager::TAB_CONTENT,
        ]);
        $this->add_control('logo_image', [
            'label' => 'Upload Logo',
            'type' => Controls_Manager::MEDIA,
            'default' => ['url' => Utils::get_placeholder_image_src()],
        ]);
        $this->add_responsive_control('logo_size', [
            'label' => 'Logo Size',
            'type' => Controls_Manager::SLIDER,
            'size_units' => ['px','em','%'],
            'range' => [
                'px'=>['min'=>20,'max'=>300],
                'em'=>['min'=>1,'max'=>20],
                '%'=>['min'=>10,'max'=>100]
            ],
            'default' => ['size'=>40,'unit'=>'px'],
            'selectors'=>['{{WRAPPER}} .logo-wrapper img'=>'height: {{SIZE}}{{UNIT}};'],
        ]);
        $this->end_controls_section();

        // ================= Menu Items =================
        $this->start_controls_section('menu_section', [
            'label' => 'Menu Items',
            'tab' => Controls_Manager::TAB_CONTENT,
        ]);
        $repeater = new Repeater();
        $repeater->add_control('menu_label', ['label'=>'Label','type'=>Controls_Manager::TEXT,'default'=>'Home']);
        $repeater->add_control('menu_link', ['label'=>'Link','type'=>Controls_Manager::URL,'default'=>['url'=>'#']]);
        $repeater->add_control('menu_image', ['label'=>'Background Image','type'=>Controls_Manager::MEDIA]);
        $this->add_control('menu_items', [
            'label'=>'Menu Items List',
            'type'=>Controls_Manager::REPEATER,
            'fields'=>$repeater->get_controls(),
            'default'=>[
                ['menu_label'=>'Home','menu_link'=>['url'=>'#']],
                ['menu_label'=>'About','menu_link'=>['url'=>'#']],
                ['menu_label'=>'Explore','menu_link'=>['url'=>'#']]
            ],
            'title_field'=>'{{{ menu_label }}}',
        ]);
        $this->end_controls_section();

        // ================= CTA =================
        $this->start_controls_section('cta_section', ['label'=>'CTA Button','tab'=>Controls_Manager::TAB_CONTENT]);
        $this->add_control('cta_label',['label'=>'Button Text','type'=>Controls_Manager::TEXT,'default'=>'EXPLORE TRIPS']);
        $this->add_control('cta_link',['label'=>'Button Link','type'=>Controls_Manager::URL,'default'=>['url'=>'#']]);
        $this->add_control('cta_icon',['label'=>'Button Icon','type'=>Controls_Manager::ICONS]);
        $this->add_control('cta_icon_position',['label'=>'Icon Position','type'=>Controls_Manager::SELECT,'default'=>'left','options'=>['left'=>'Left','right'=>'Right']]);
        $this->add_responsive_control('cta_icon_gap',['label'=>'Icon Gap','type'=>Controls_Manager::SLIDER,'size_units'=>['px','em','%'],'range'=>['px'=>['min'=>0,'max'=>50]],'default'=>['size'=>10],'selectors'=>['{{WRAPPER}} .menu-overlay__cta .elementor-button-icon'=>'margin-right: {{SIZE}}{{UNIT}};']]);
        $this->end_controls_section();

        // ================= Social Links =================
        $this->start_controls_section('social_section',['label'=>'Social Links','tab'=>Controls_Manager::TAB_CONTENT]);
        $repeater_social = new Repeater();
        $repeater_social->add_control('social_label',['label'=>'Label','type'=>Controls_Manager::TEXT,'default'=>'Instagram']);
        $repeater_social->add_control('social_link',['label'=>'Link','type'=>Controls_Manager::URL,'default'=>['url'=>'#']]);
        $this->add_control('social_items',['label'=>'Social Links List','type'=>Controls_Manager::REPEATER,'fields'=>$repeater_social->get_controls(),'title_field'=>'{{{ social_label }}}']);
        $this->end_controls_section();

        // ================= Header Style =================
        $this->start_controls_section('header_style',['label'=>'Header','tab'=>Controls_Manager::TAB_STYLE]);
        $this->add_responsive_control('header_padding',['label'=>'Padding','type'=>Controls_Manager::DIMENSIONS,'size_units'=>['px','em','%'],'selectors'=>['{{WRAPPER}} .navbar'=>'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        $this->add_group_control(Group_Control_Background::get_type(),['name'=>'header_bg','label'=>'Background','types'=>['classic','gradient'],'selector'=>'{{WRAPPER}} .navbar']);
        $this->add_group_control(Group_Control_Border::get_type(),['name'=>'header_border','label'=>'Border','selector'=>'{{WRAPPER}} .navbar']);
        $this->add_responsive_control('header_radius',['label'=>'Border Radius','type'=>Controls_Manager::DIMENSIONS,'size_units'=>['px','em','%'],'selectors'=>['{{WRAPPER}} .navbar'=>'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        $this->end_controls_section();

        // ================= Menu Items Style =================
        $this->start_controls_section('menu_item_style',['label'=>'Menu Items','tab'=>Controls_Manager::TAB_STYLE]);
        $this->add_group_control(Group_Control_Typography::get_type(),['name'=>'menu_typography','label'=>'Typography','selector'=>'{{WRAPPER}} .menu-overlay__main a']);
        $this->add_group_control(Group_Control_Text_Stroke::get_type(),['name'=>'menu_text_stroke','label'=>'Text Stroke','selector'=>'{{WRAPPER}} .menu-overlay__main a']);
        $this->add_control('menu_color',['label'=>'Text Color','type'=>Controls_Manager::COLOR,'selectors'=>['{{WRAPPER}} .menu-overlay__main a'=>'color: {{VALUE}};']]);
        $this->add_control('menu_hover_color',['label'=>'Hover Color','type'=>Controls_Manager::COLOR,'selectors'=>['{{WRAPPER}} .menu-overlay__main a:hover'=>'color: {{VALUE}};']]);
        $this->add_responsive_control('menu_text_align',['label'=>'Text Align','type'=>Controls_Manager::CHOOSE,'options'=>['left'=>['title'=>'Left','icon'=>'eicon-text-align-left'],'center'=>['title'=>'Center','icon'=>'eicon-text-align-center'],'right'=>['title'=>'Right','icon'=>'eicon-text-align-right']],'default'=>'center','toggle'=>true,'selectors'=>['{{WRAPPER}} .menu-overlay__main'=>'text-align: {{VALUE}};']]);
        $this->add_responsive_control('menu_item_gap',['label'=>'Item Gap','type'=>Controls_Manager::SLIDER,'range'=>['px'=>['min'=>0,'max'=>100]],'default'=>['size'=>10],'selectors'=>['{{WRAPPER}} .menu-overlay__main li'=>'margin-bottom: {{SIZE}}{{UNIT}};']]);
        $this->add_responsive_control('menu_item_padding',['label'=>'Padding','type'=>Controls_Manager::DIMENSIONS,'size_units'=>['px','em','%'],'selectors'=>['{{WRAPPER}} .menu-overlay__main li a'=>'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        $this->add_group_control(Group_Control_Background::get_type(),['name'=>'menu_item_bg','label'=>'Background','types'=>['classic','gradient'],'selector'=>'{{WRAPPER}} .menu-overlay__main li a']);
        $this->add_group_control(Group_Control_Background::get_type(),['name'=>'menu_item_bg_hover','label'=>'Background Hover','types'=>['classic','gradient'],'selector'=>'{{WRAPPER}} .menu-overlay__main li a:hover']);
        $this->add_group_control(Group_Control_Border::get_type(),['name'=>'menu_item_border','label'=>'Border','selector'=>'{{WRAPPER}} .menu-overlay__main li a']);
        $this->add_responsive_control('menu_item_radius',['label'=>'Border Radius','type'=>Controls_Manager::DIMENSIONS,'size_units'=>['px','em','%'],'selectors'=>['{{WRAPPER}} .menu-overlay__main li a'=>'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        $this->end_controls_section();

        // ================= CTA Style =================
        $this->start_controls_section('cta_style',['label'=>'CTA','tab'=>Controls_Manager::TAB_STYLE]);
        $this->add_group_control(Group_Control_Typography::get_type(),['name'=>'cta_typography','label'=>'Typography','selector'=>'{{WRAPPER}} .menu-overlay__cta']);
        $this->add_responsive_control('cta_padding',['label'=>'Padding','type'=>Controls_Manager::DIMENSIONS,'size_units'=>['px','em','%'],'selectors'=>['{{WRAPPER}} .menu-overlay__cta'=>'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);

        $this->start_controls_tabs('cta_bg_tabs');
        $this->start_controls_tab('cta_bg_normal',['label'=>'Normal']);
        $this->add_group_control(Group_Control_Background::get_type(),['name'=>'cta_bg','label'=>'Background','types'=>['classic','gradient'],'selector'=>'{{WRAPPER}} .menu-overlay__cta']);
        $this->add_control('cta_text_color',['label'=>'Text Color','type'=>Controls_Manager::COLOR,'selectors'=>['{{WRAPPER}} .menu-overlay__cta'=>'color: {{VALUE}};']]);
        $this->end_controls_tab();
        $this->start_controls_tab('cta_bg_hover',['label'=>'Hover']);
        $this->add_group_control(Group_Control_Background::get_type(),['name'=>'cta_bg_hover','label'=>'Background','types'=>['classic','gradient'],'selector'=>'{{WRAPPER}} .menu-overlay__cta:hover']);
        $this->add_control('cta_text_hover_color',['label'=>'Text Hover Color','type'=>Controls_Manager::COLOR,'selectors'=>['{{WRAPPER}} .menu-overlay__cta:hover'=>'color: {{VALUE}};']]);
        $this->end_controls_tab();
        $this->end_controls_tabs();
        $this->add_group_control(Group_Control_Border::get_type(),['name'=>'cta_border','label'=>'Border','selector'=>'{{WRAPPER}} .menu-overlay__cta']);
        $this->add_responsive_control('cta_radius',['label'=>'Border Radius','type'=>Controls_Manager::DIMENSIONS,'size_units'=>['px','em','%'],'selectors'=>['{{WRAPPER}} .menu-overlay__cta'=>'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        $this->end_controls_section();

        // ================= Menu Toggle Style =================
        $this->start_controls_section('menu_toggle_style',['label'=>'Menu Toggle','tab'=>Controls_Manager::TAB_STYLE]);
        $this->add_responsive_control('toggle_padding',['label'=>'Padding','type'=>Controls_Manager::DIMENSIONS,'size_units'=>['px','em','%'],'selectors'=>['{{WRAPPER}} .menu-toggle-wrapper'=>'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        $this->start_controls_tabs('toggle_bg_tabs');
        $this->start_controls_tab('toggle_bg_normal',['label'=>'Normal']);
        $this->add_group_control(Group_Control_Background::get_type(),['name'=>'toggle_bg_normal','label'=>'Background','types'=>['classic','gradient'],'selector'=>'{{WRAPPER}} .menu-toggle-wrapper']);
        $this->add_control('toggle_line_color',['label'=>'Line Color','type'=>Controls_Manager::COLOR,'selectors'=>['{{WRAPPER}} .menu-toggle-wrapper span'=>'background-color: {{VALUE}};']]);
        $this->end_controls_tab();
        $this->start_controls_tab('toggle_bg_hover',['label'=>'Hover']);
        $this->add_group_control(Group_Control_Background::get_type(),['name'=>'toggle_bg_hover','label'=>'Background','types'=>['classic','gradient'],'selector'=>'{{WRAPPER}} .menu-toggle-wrapper:hover']);
        $this->add_control('toggle_line_hover_color',['label'=>'Line Hover Color','type'=>Controls_Manager::COLOR,'selectors'=>['{{WRAPPER}} .menu-toggle-wrapper:hover span'=>'background-color: {{VALUE}};']]);
        $this->end_controls_tab();
        $this->end_controls_tabs();
        $this->add_group_control(Group_Control_Border::get_type(),['name'=>'toggle_border','label'=>'Border','selector'=>'{{WRAPPER}} .menu-toggle-wrapper']);
        $this->add_responsive_control('toggle_border_radius',['label'=>'Border Radius','type'=>Controls_Manager::DIMENSIONS,'size_units'=>['px','em','%'],'selectors'=>['{{WRAPPER}} .menu-toggle-wrapper'=>'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        $this->end_controls_section();

        // ================= Social Style =================
        $this->start_controls_section('social_style',['label'=>'Social Links','tab'=>Controls_Manager::TAB_STYLE]);
        $this->add_group_control(Group_Control_Typography::get_type(),['name'=>'social_typography','label'=>'Typography','selector'=>'{{WRAPPER}} .menu-overlay__socials a']);
        $this->add_control('social_color',['label'=>'Text Color','type'=>Controls_Manager::COLOR,'selectors'=>['{{WRAPPER}} .menu-overlay__socials a'=>'color: {{VALUE}};']]);
        $this->add_control('social_hover_color',['label'=>'Hover Color','type'=>Controls_Manager::COLOR,'selectors'=>['{{WRAPPER}} .menu-overlay__socials a:hover'=>'color: {{VALUE}};']]);
        $this->add_responsive_control('social_text_align',['label'=>'Text Align','type'=>Controls_Manager::CHOOSE,'options'=>['left'=>['title'=>'Left','icon'=>'eicon-text-align-left'],'center'=>['title'=>'Center','icon'=>'eicon-text-align-center'],'right'=>['title'=>'Right','icon'=>'eicon-text-align-right']],'default'=>'center','toggle'=>true,'selectors'=>['{{WRAPPER}} .menu-overlay__socials'=>'text-align: {{VALUE}};']]);
        $this->end_controls_section();

        // ================= Overlay Style =================
        $this->start_controls_section('overlay_style',['label'=>'Overlay','tab'=>Controls_Manager::TAB_STYLE]);
        $this->add_group_control(Group_Control_Background::get_type(),['name'=>'overlay_bg','label'=>'Background','types'=>['classic','gradient'],'selector'=>'{{WRAPPER}} .menu-overlay__left']);
        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        $widget_id = $this->get_id(); // Unique per widget
        ?>
        <div class="gsap-menu-widget" data-widget-id="<?php echo esc_attr($widget_id); ?>">

            <!-- Navbar -->
            <header class="navbar">
                <div class="logo-wrapper">
                    <a href="<?php echo esc_url(home_url('/')); ?>">
                        <img src="<?php echo esc_url($settings['logo_image']['url']); ?>" alt="Logo">
                    </a>
                </div>
                <div class="menu-toggle-wrapper" data-toggle-top="toggle-line-top" data-toggle-bottom="toggle-line-bottom">
                    <span class="toggle-line-top"></span>
                    <span class="toggle-line-bottom"></span>
                </div>
                <a href="<?php echo esc_url($settings['cta_link']['url']); ?>" class="menu-overlay__cta btn">
                    <?php 
                        if (!empty($settings['cta_icon']['value']) && $settings['cta_icon_position'] === 'left') {
                            \Elementor\Icons_Manager::render_icon($settings['cta_icon'], ['aria-hidden'=>'true']);
                        }
                        echo esc_html($settings['cta_label']);
                        if (!empty($settings['cta_icon']['value']) && $settings['cta_icon_position'] === 'right') {
                            \Elementor\Icons_Manager::render_icon($settings['cta_icon'], ['aria-hidden'=>'true']);
                        }
                    ?>
                </a>
            </header>

            <!-- Menu Overlay -->
            <div class="menu-overlay">
                <!-- Left: Links + Socials -->
                <div class="menu-overlay__left">
                    <ul class="menu-overlay__main">
                        <?php foreach ($settings['menu_items'] as $item): ?>
                            <li><a href="<?php echo esc_url($item['menu_link']['url']); ?>" data-text-anim><?php echo esc_html($item['menu_label']); ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                    <div class="menu-overlay__socials">
                        <ul>
                            <?php foreach ($settings['social_items'] as $social): ?>
                                <li><a href="<?php echo esc_url($social['social_link']['url']); ?>"><?php echo esc_html($social['social_label']); ?></a></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>

                <!-- Right: Images -->
                <div class="menu-overlay__right">
                    <?php foreach ($settings['menu_items'] as $item): ?>
                        <div class="menu-overlay__bg-img">
                            <img src="<?php echo esc_url($item['menu_image']['url']); ?>" alt="" />
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Inline CSS + JS scoped via data-widget-id -->
            <style>
                [data-widget-id="<?php echo esc_attr($widget_id); ?>"] * { margin:0;padding:0;box-sizing:border-box;}
                [data-widget-id="<?php echo esc_attr($widget_id); ?>"] a { text-decoration:none;color:inherit; }
                /* Navbar */
                [data-widget-id="<?php echo esc_attr($widget_id); ?>"] .navbar { display:flex; justify-content:space-between; align-items:center; position:relative; z-index:9999; }
                [data-widget-id="<?php echo esc_attr($widget_id); ?>"] .logo-wrapper img { height:40px; }
                /* Toggle */
                [data-widget-id="<?php echo esc_attr($widget_id); ?>"] .menu-toggle-wrapper { width:44px;height:44px; display:flex; flex-direction:column; gap:7px; align-items:center; justify-content:center; cursor:pointer; }
                [data-widget-id="<?php echo esc_attr($widget_id); ?>"] .menu-toggle-wrapper span { width:29px;height:2px; background:#2f2411; display:block; }
                /* Overlay */
                [data-widget-id="<?php echo esc_attr($widget_id); ?>"] .menu-overlay { position:fixed; inset:0; width:100vw; height:100vh; clip-path:polygon(0 0,100% 0,100% 0,0 0); z-index:9998; display:flex; }
                [data-widget-id="<?php echo esc_attr($widget_id); ?>"] .menu-overlay__left { width:50%; display:flex; flex-direction:column; justify-content:center; align-items:center; text-align:center; padding:4rem 2rem; gap:2rem; backdrop-filter:blur(67px);}
                [data-widget-id="<?php echo esc_attr($widget_id); ?>"] .menu-overlay__right { width:50%; position:relative; overflow:hidden; }
                [data-widget-id="<?php echo esc_attr($widget_id); ?>"] .menu-overlay__bg-img { position:absolute; inset:0; width:100%; height:100%; }
                [data-widget-id="<?php echo esc_attr($widget_id); ?>"] .menu-overlay__bg-img img { width:100%; height:100%; object-fit:cover; opacity:0; transform:scale(1.2); transition:opacity 0.5s, transform 0.5s; }
                [data-widget-id="<?php echo esc_attr($widget_id); ?>"] .menu-overlay__main li { margin-bottom:10px; width:fit-content; margin-inline:auto; transition:opacity 0.3s, transform 0.5s; }
                [data-widget-id="<?php echo esc_attr($widget_id); ?>"] .menu-overlay__main:has(li:hover) li:not(:hover) { opacity:0.5; }
                [data-widget-id="<?php echo esc_attr($widget_id); ?>"] .menu-overlay__socials ul { display:flex; gap:14px; color:#fff7e8; font-size:clamp(1rem,0.55vw+0.88rem,1.375rem); font-weight:600; list-style:none; }
                [data-widget-id="<?php echo esc_attr($widget_id); ?>"] .menu-overlay__cta.btn { display:flex; gap:10px; padding:14px; border-radius:10px; background:#fff0dc; font-family:Anton,sans-serif; align-items:center; justify-content:center; color:#2f2411; transition:all 0.3s;}
            </style>

            <script>
                document.addEventListener("DOMContentLoaded", () => {
                    const widget = document.querySelector('[data-widget-id="<?php echo esc_attr($widget_id); ?>"]');
                    const toggleButton = widget.querySelector(".menu-toggle-wrapper");
                    const menuOverlay = widget.querySelector(".menu-overlay");
                    const menuLinks = widget.querySelectorAll(".menu-overlay__main li a");
                    const bgImgs = widget.querySelectorAll(".menu-overlay__right .menu-overlay__bg-img img");
                    const topLine = toggleButton.querySelector(".toggle-line-top");
                    const bottomLine = toggleButton.querySelector(".toggle-line-bottom");

                    // Initialize images
                    bgImgs.forEach((img,i) => gsap.set(img, {opacity: i===0 ? 1 : 0, scale:1.2}));

                    // Hover animation
                    menuLinks.forEach((link, index) => {
                        link.addEventListener("mouseenter", () => {
                            bgImgs.forEach((img,i) => gsap.to(img, {opacity:i===index?1:0, scale:i===index?1.3:1.2, duration:0.6, ease:"power3.inOut"}));
                        });
                        link.addEventListener("mouseleave", () => {
                            bgImgs.forEach((img,i) => gsap.to(img, {opacity:i===0?1:0, scale:1.2, duration:0.6, ease:"power3.inOut"}));
                        });
                    });

                    // Menu overlay timeline
                    const menuTimeline = gsap.timeline({paused:true});
                    menuTimeline.to(menuOverlay, {clipPath:"polygon(0 0,100% 0,100% 100%,0% 100%)", duration:0.8, ease:"power3.inOut"});
                    menuTimeline.fromTo(bgImgs, {scale:1.5, opacity:0}, {scale:1.2, opacity:1, duration:0.8, stagger:0.1, ease:"power3.out"}, 0);

                    // Animate menu text
                    menuTimeline.add(()=>{
                        const linkTexts = widget.querySelectorAll("[data-text-anim]");
                        linkTexts.forEach(el=>{
                            gsap.set(el,{visibility:"visible"});
                            const split = SplitText.create(el,{type:"chars",smartWrap:true,mask:"chars"});
                            gsap.fromTo(split.chars,{yPercent:-200,opacity:0},{yPercent:0,opacity:1,duration:0.5,ease:"power2.out",stagger:0.03});
                            el.splitInstance = split;
                        });
                    },0);
                    
                    // Toggle hamburger animation
                    const toggleTimeline = gsap.timeline({paused:true});
                    toggleTimeline.to(topLine, {y:6, rotation:45, duration:0.4, ease:"back.out(1.5)"}, 0)
                                  .to(bottomLine, {y:-6, rotation:-45, duration:0.4, ease:"back.out(1.5)"}, 0);

                    toggleButton.addEventListener("click", () => {
                        menuOverlay.classList.toggle("active");
                        if(menuTimeline.reversed()){
                            menuTimeline.play();
                            toggleTimeline.play();
                        } else {
                            menuTimeline.reverse();
                            toggleTimeline.reverse();
                        }
                    });
                });
            </script>
        </div>
        <?php
    }
}

// Register Widget
\Elementor\Plugin::instance()->widgets_manager->register(new \My_Core_Plugin\Widgets\Gsap_Menu_Widget());
