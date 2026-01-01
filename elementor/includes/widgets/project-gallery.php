<?php
/**
 * Plugin Name: Elementor Project Gallery Widget
 * Description: Project Gallery with fullscreen hover image, grid/list toggle, GSAP animations, click popup, modern fly-to-popup animation, and full style controls.
 * Version: 1.0
 * Author: Tophive
 */

namespace My_Core_Plugin\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;

if (!defined('ABSPATH')) exit;

class Project_Gallery_Widget extends Widget_Base {

    public function get_name() { return 'Project_Gallery_Widget'; }
    public function get_title() { return __('Project Gallery', 'my-core-plugin'); }
    public function get_icon() { return 'eicon-shape'; }
    public function get_categories() { return ['th-general']; }

    protected function register_controls() {

        /** CONTENT TAB **/

        // Header
        $this->start_controls_section('section_header', ['label'=>'Header']);
        $this->add_control('header_title', ['label'=>'Header Title','type'=>Controls_Manager::TEXT,'default'=>'SELECTED WORKS']);
        $this->add_responsive_control('header_gap', [
            'label' => 'Gap Below Header',
            'type' => Controls_Manager::SLIDER,
            'size_units' => ['px','em','rem'],
            'range' => ['px'=>['min'=>0,'max'=>200],'em'=>['min'=>0,'max'=>20],'rem'=>['min'=>0,'max'=>20]],
            'default' => ['size'=>20,'unit'=>'px'],
        ]);
        $this->add_control('popup_view', ['label'=>'Popup View','type'=>Controls_Manager::SELECT,'options'=>['hover'=>'Hover Overlay','click'=>'Click Popup'],'default'=>'hover']);
        $this->end_controls_section();

        // Projects Repeater
        $repeater = new Repeater();
        $repeater->add_control('project_title', ['label'=>'Title','type'=>Controls_Manager::TEXT,'default'=>'Project Title']);
        $repeater->add_control('project_year', ['label'=>'Year','type'=>Controls_Manager::TEXT,'default'=>'2025']);
        $repeater->add_control('project_image', ['label'=>'Image','type'=>Controls_Manager::MEDIA]);
        $repeater->add_control('project_description', ['label'=>'Description','type'=>Controls_Manager::TEXTAREA,'default'=>'']);
        $repeater->add_control('project_link', ['label'=>'Project Link','type'=>Controls_Manager::URL,'placeholder'=>'https://example.com']);

        $this->start_controls_section('projects_section', ['label'=>'Projects Section']);
        $this->add_control('projects', ['label'=>'Projects','type'=>Controls_Manager::REPEATER,'fields'=>$repeater->get_controls(),'title_field'=>'{{{ project_title }}}']);
        $this->end_controls_section();

        /** STYLE TAB **/

        // Header Style
        $this->start_controls_section('style_header', ['label'=>'Header Title','tab'=>Controls_Manager::TAB_STYLE]);
        $this->add_control('header_color',['label'=>'Color','type'=>Controls_Manager::COLOR,'selectors'=>['{{WRAPPER}} .header-title h1'=>'color: {{VALUE}};']]);
        $this->add_group_control(Group_Control_Typography::get_type(), ['name'=>'header_typography','selector'=>'{{WRAPPER}} .header-title h1']);
        $this->end_controls_section();

        // Toggle Buttons Style
        $this->start_controls_section('style_toggle', ['label'=>'Toggle Buttons','tab'=>Controls_Manager::TAB_STYLE]);
        $this->add_control('toggle_show', [
            'label' => 'Show Toggle',
            'type' => Controls_Manager::SWITCHER,
            'label_on' => 'Show',
            'label_off' => 'Hide',
            'return_value' => 'yes',
            'default' => 'yes',
        ]);

        // Dependent Controls
        $this->add_control('toggle_bg',['label'=>'Background Color','type'=>Controls_Manager::COLOR,'selectors'=>['{{WRAPPER}} .toggle-btn'=>'background-color: {{VALUE}};'],'condition'=>['toggle_show'=>'yes']]);
        $this->add_control('toggle_color',['label'=>'Text Color','type'=>Controls_Manager::COLOR,'selectors'=>['{{WRAPPER}} .toggle-btn'=>'color: {{VALUE}};'],'condition'=>['toggle_show'=>'yes']]);
        $this->add_group_control(Group_Control_Typography::get_type(), ['name'=>'toggle_typography','selector'=>'{{WRAPPER}} .toggle-btn','condition'=>['toggle_show'=>'yes']]);
        $this->add_group_control(Group_Control_Border::get_type(), ['name'=>'toggle_border','selector'=>'{{WRAPPER}} .toggle-btn','condition'=>['toggle_show'=>'yes']]);
        $this->add_responsive_control('toggle_radius',['label'=>'Border Radius','type'=>Controls_Manager::DIMENSIONS,'size_units'=>['px','%'],'selectors'=>['{{WRAPPER}} .toggle-btn'=>'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'],'condition'=>['toggle_show'=>'yes']]);
        $this->end_controls_section();

        // Project Items Style
        $this->start_controls_section('style_project_item', ['label'=>'Project Items','tab'=>Controls_Manager::TAB_STYLE]);
        $this->start_controls_tabs('project_item_tabs');

        // Card Tab
        $this->start_controls_tab('project_card', ['label'=>'Card']);
        $this->add_group_control(Group_Control_Border::get_type(), ['name'=>'project_border','selector'=>'{{WRAPPER}} .project-item']);
        $this->add_responsive_control('project_radius',['label'=>'Border Radius','type'=>Controls_Manager::DIMENSIONS,'size_units'=>['px','%'],'selectors'=>['{{WRAPPER}} .project-item'=>'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        $this->add_control('project_bg',['label'=>'Background Color','type'=>Controls_Manager::COLOR,'selectors'=>['{{WRAPPER}} .project-item'=>'background-color: {{VALUE}};']]);
        $this->add_control('project_hover_bg',['label'=>'Hover Background','type'=>Controls_Manager::COLOR,'selectors'=>['{{WRAPPER}} .project-item:hover'=>'background-color: {{VALUE}};']]);
        $this->add_responsive_control('project_padding',['label'=>'Padding','type'=>Controls_Manager::DIMENSIONS,'size_units'=>['px','em','%'],'selectors'=>['{{WRAPPER}} .project-item'=>'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        $this->end_controls_tab();

        // Text Style Tab
        $this->start_controls_tab('project_text', ['label'=>'Text Style']);
        $this->add_control('project_title_color',['label'=>'Title Color','type'=>Controls_Manager::COLOR,'selectors'=>['{{WRAPPER}} .project-item .project-cell.title'=>'color: {{VALUE}};']]);
        $this->add_control('project_year_color',['label'=>'Year Color','type'=>Controls_Manager::COLOR,'selectors'=>['{{WRAPPER}} .project-item .project-cell.year'=>'color: {{VALUE}};']]);
        $this->add_control('project_desc_color',['label'=>'Description Color','type'=>Controls_Manager::COLOR,'selectors'=>['{{WRAPPER}} .project-item .overlay-content p'=>'color: {{VALUE}};']]);
        $this->add_group_control(Group_Control_Typography::get_type(), ['name'=>'project_title_typo','label'=>'Title','selector'=>'{{WRAPPER}} .project-item .project-cell.title']);
        $this->add_group_control(Group_Control_Typography::get_type(), ['name'=>'project_year_typo','label'=>'Year','selector'=>'{{WRAPPER}} .project-item .project-cell.year']);
        $this->add_group_control(Group_Control_Typography::get_type(), ['name'=>'project_desc_typo','label'=>'Description','selector'=>'{{WRAPPER}} .project-item .overlay-content p']);
        $this->end_controls_tab();

        $this->end_controls_tabs();
        $this->end_controls_section();

        // Hover Image Style
        $this->start_controls_section('style_hover_image', ['label'=>'Hover Image','tab'=>Controls_Manager::TAB_STYLE]);
        $this->add_responsive_control('hover_image_width',['label'=>'Width','type'=>Controls_Manager::SLIDER,'size_units'=>['px','%'],'range'=>['px'=>['min'=>50,'max'=>2000],'%'=>['min'=>10,'max'=>100]],'selectors'=>['{{WRAPPER}} .project-hover-image'=>'width: {{SIZE}}{{UNIT}}; height:auto;']]);
        $this->add_group_control(Group_Control_Border::get_type(), ['name'=>'hover_image_border','selector'=>'{{WRAPPER}} .project-hover-image']);
        $this->add_responsive_control('hover_image_radius',['label'=>'Border Radius','type'=>Controls_Manager::DIMENSIONS,'size_units'=>['px','%'],'selectors'=>['{{WRAPPER}} .project-hover-image'=>'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        $this->add_group_control(Group_Control_Box_Shadow::get_type(), ['name'=>'hover_image_shadow','selector'=>'{{WRAPPER}} .project-hover-image']);
        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        $unique = $this->get_id();
        $header_gap = isset($settings['header_gap']['size']) ? $settings['header_gap']['size'].$settings['header_gap']['unit'] : '20px';
        ?>
        <div class="project-section-wrapper" id="project-section-<?php echo esc_attr($unique); ?>">

            <!-- Header -->
            <div class="header" style="margin-bottom: <?php echo esc_attr($header_gap); ?>;">
                <div class="header-title">
                    <h1 style="margin:0;"><?php echo esc_html($settings['header_title']); ?><sup><?php echo count($settings['projects']); ?></sup></h1>
                </div>
                <?php if(!empty($settings['toggle_show']) && $settings['toggle_show']==='yes'): ?>
                <div class="view-toggle">
                    <button class="toggle-btn active" data-view="list">List</button>
                    <button class="toggle-btn" data-view="grid">Grid</button>
                </div>
                <?php endif; ?>
            </div>

            <!-- Projects Container -->
            <div id="projects-container-<?php echo esc_attr($unique); ?>" class="projects-container list-view">
                <?php foreach ($settings['projects'] as $proj): ?>
                    <div class="project-item">
                        <div class="project-table">
                            <div class="project-cell title"><?php echo esc_html($proj['project_title']); ?></div>
                            <div class="project-cell year"><?php echo esc_html($proj['project_year']); ?></div>
                        </div>

                        <!-- Hover Image -->
                        <img class="project-hover-image" src="<?php echo esc_url($proj['project_image']['url']); ?>" alt="<?php echo esc_attr($proj['project_title']); ?>">

                        <?php if($settings['popup_view']==='hover'): ?>
                        <div class="project-hover-overlay">
                            <div class="overlay-content">
                                <p><?php echo esc_html($proj['project_description']); ?></p>
                                <?php if(!empty($proj['project_link']['url'])): ?>
                                    <a href="<?php echo esc_url($proj['project_link']['url']); ?>" target="<?php echo esc_attr($proj['project_link']['is_external'] ? '_blank':'_self'); ?>">View Project</a>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Click Popup -->
            <?php if($settings['popup_view']==='click'): ?>
            <div id="popup-overlay">
                <div class="popup-content">
                    <img class="popup-image" src="" alt="Popup Image">
                    <div class="popup-description"></div>
                </div>
            </div>
            <?php endif; ?>

            <!-- INLINE CSS & JS -->
            <style>
                *{box-sizing:border-box;margin:0;padding:0}
                .project-section-wrapper{position:relative;}
                .header{display:flex;justify-content:space-between;align-items:center}
                .toggle-btn{padding:5px 15px;margin-left:5px;cursor:pointer;border:1px solid #ccc;color:#272727;background:#fff}
                .toggle-btn.active{background:#000;color:#fff}
                #projects-container-<?php echo esc_attr($unique); ?>.list-view .project-item{display:flex;flex-direction:column;position:relative}
                #projects-container-<?php echo esc_attr($unique); ?>.grid-view{display:grid;grid-template-columns:repeat(3,1fr);gap:20px}
                #projects-container-<?php echo esc_attr($unique); ?>.grid-view .project-item{border:none;display:flex;flex-direction:column;align-items:center;padding:10px;position:relative;text-align:center}
                .project-table{display:flex;justify-content:space-between;align-items:center;width:100%;}
                .project-hover-image{position:fixed;top:50%;left:50%;transform:translate(-50%,-50%) scale(0.8);opacity:0;transition:all 0.3s ease;z-index:999;pointer-events:none;}
                .project-item:hover .project-hover-image{opacity:1;transform:translate(-50%,-50%) scale(1)}
                .project-hover-overlay{position:absolute;top:0;left:0;width:100%;height:100%;opacity:0;transition:opacity 0.3s;overflow:hidden;display:flex;align-items:center;justify-content:center;color:#fff;padding:15px}
                .project-item:hover .project-hover-overlay{opacity:1}
                .overlay-content{position:relative;z-index:2;text-align:center}
                #popup-overlay{position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.8);display:none;align-items:center;justify-content:center;z-index:9999}
                .popup-content img{display:block;margin-bottom:20px;max-width:90%;height:auto}
                .popup-description{color:#fff;text-align:center;font-size:16px}
                @media(max-width:768px){#projects-container-<?php echo esc_attr($unique); ?>.grid-view{grid-template-columns:repeat(2,1fr)}}
                @media(max-width:480px){#projects-container-<?php echo esc_attr($unique); ?>.grid-view{grid-template-columns:1fr}}
            </style>

            <script>
            document.addEventListener('DOMContentLoaded', function(){
                const container = document.getElementById('projects-container-<?php echo esc_attr($unique); ?>');
                const projects = container.querySelectorAll('.project-item');

                if(typeof gsap !== 'undefined'){
                    gsap.from(projects, {opacity:0, y:30, stagger:0.1, duration:0.5, ease:"power3.out"});
                }

                const gridBtn = container.parentElement.querySelector('.toggle-btn[data-view="grid"]');
                const listBtn = container.parentElement.querySelector('.toggle-btn[data-view="list"]');

                if(gridBtn && listBtn){
                    gridBtn.addEventListener('click', ()=>{
                        container.classList.add('grid-view'); container.classList.remove('list-view');
                        projects.forEach(p=>{p.style.flexDirection='column';p.style.borderBottom='none';p.style.textAlign='center';});
                        if(typeof gsap!=='undefined'){gsap.fromTo(projects,{opacity:0,scale:0.8},{opacity:1,scale:1,stagger:0.1,duration:0.5,ease:"power3.out"});}
                        gridBtn.classList.add('active'); listBtn.classList.remove('active');
                    });
                    listBtn.addEventListener('click', ()=>{
                        container.classList.add('list-view'); container.classList.remove('grid-view');
                        projects.forEach(p=>{p.style.flexDirection='column';p.style.borderBottom='1px solid #ccc';p.style.textAlign='left';});
                        if(typeof gsap!=='undefined'){gsap.fromTo(projects,{opacity:0,y:30},{opacity:1,y:0,stagger:0.1,duration:0.5,ease:"power3.out"});}
                        listBtn.classList.add('active'); gridBtn.classList.remove('active');
                    });
                }

                <?php if($settings['popup_view']==='click'): ?>
                const popup = document.getElementById('popup-overlay');
                const popupImg = popup.querySelector('.popup-image');
                const popupDesc = popup.querySelector('.popup-description');

                projects.forEach(p=>{
                    p.addEventListener('click', ()=>{
                        const hoverImg = p.querySelector('.project-hover-image');
                        const desc = p.querySelector('.overlay-content p');
                        const rect = hoverImg.getBoundingClientRect();

                        popupImg.src = hoverImg.src;
                        popupDesc.textContent = desc ? desc.textContent : '';
                        popup.style.display = 'flex';

                        gsap.fromTo(popupImg,{
                            x:rect.left + rect.width/2 - window.innerWidth/2,
                            y:rect.top + rect.height/2 - window.innerHeight/2,
                            scale:0.5,
                            opacity:0
                        },{
                            x:0,
                            y:0,
                            scale:1,
                            opacity:1,
                            duration:0.6,
                            ease:"power3.out"
                        });
                    });
                });

                popup.addEventListener('click', ()=>{
                    gsap.to(popupImg,{scale:0.5,opacity:0,duration:0.4,ease:"power2.in",onComplete:()=>{popup.style.display='none';}});
                });
                <?php endif; ?>
            });
            </script>

        </div>
        <?php
    }
}
// Register widget
\Elementor\Plugin::instance()->widgets_manager->register(new \My_Core_Plugin\Widgets\Project_Gallery_Widget());
