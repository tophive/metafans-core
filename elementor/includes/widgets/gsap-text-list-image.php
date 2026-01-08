<?php
namespace My_Core_Plugin\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Group_Control_Typography;

if (!defined('ABSPATH')) exit;

class GSAP_Text_List_Image extends Widget_Base {

    public function get_name() {
        return 'gsap_text_list_image';
    }

    public function get_title() {
        return esc_html__('GSAP Text List Image', 'my-core-plugin');
    }

    public function get_icon() {
        return 'eicon-columns';
    }

    public function get_categories() {
        return ['th-general'];
    }

    public function get_script_depends() {
        return ['gsap'];
    }

    protected function register_controls() {

        // -----------------------------
        // Content Section
        // -----------------------------
        $this->start_controls_section('content_section', ['label' => __('Projects', 'my-core-plugin')]);

        $repeater = new Repeater();

        $repeater->add_control('project_title', [
            'label' => __('Project Title', 'my-core-plugin'),
            'type' => Controls_Manager::TEXT,
            'default' => 'Project Title',
        ]);

        $repeater->add_control('project_details_left', [
            'label' => __('Left Details', 'my-core-plugin'),
            'type' => Controls_Manager::TEXTAREA,
            'default' => "Detail 1\nDetail 2\nDetail 3",
        ]);

        $repeater->add_control('project_details_right', [
            'label' => __('Right Details', 'my-core-plugin'),
            'type' => Controls_Manager::TEXTAREA,
            'default' => "Detail A\nDetail B\nDetail C",
        ]);

        $repeater->add_control('project_image', [
            'label' => __('Project Image', 'my-core-plugin'),
            'type' => Controls_Manager::MEDIA,
        ]);

        $repeater->add_control('project_year', [
            'label' => __('Project Year', 'my-core-plugin'),
            'type' => Controls_Manager::TEXT,
            'default' => '2025',
        ]);

        $this->add_control('projects_list', [
            'label' => __('Projects List', 'my-core-plugin'),
            'type' => Controls_Manager::REPEATER,
            'fields' => $repeater->get_controls(),
            'default' => [
                [
                    'project_title' => 'COSMIC DEPTHS',
                    'project_details_left' => "SPATIAL AWARENESS\nEXPANSIVE VISION\nETERNAL MOMENT",
                    'project_details_right' => "SILENT OBSERVATION\nTIMELESS CAPTURE\nBEYOND PERCEPTION",
                    'project_image' => ['url' => 'https://cdn.cosmos.so/2519c3a3-40c4-49ff-95ed-928b3cf69740?format=jpeg'],
                    'project_year' => '2025',
                ],
            ],
            'title_field' => '{{{ project_title }}}',
        ]);

        $this->end_controls_section();

        // -----------------------------
        // Typography Section
        // -----------------------------
        $this->start_controls_section('style_section', [
            'label' => __('Title Typography', 'my-core-plugin'),
            'tab' => Controls_Manager::TAB_STYLE,
        ]);

        $this->add_group_control(Group_Control_Typography::get_type(), [
            'name' => 'title_typography',
            'label' => __('Typography', 'my-core-plugin'),
            'selector' => '{{WRAPPER}} .project-title',
        ]);

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        if (empty($settings['projects_list'])) return;
        ?>
        <div class="gsap-list-component">
            <?php foreach ($settings['projects_list'] as $index => $project):
                $left_details = !empty($project['project_details_left']) ? explode("\n",$project['project_details_left']) : [];
                $right_details = !empty($project['project_details_right']) ? explode("\n",$project['project_details_right']) : [];
                $image_url = !empty($project['project_image']['url']) ? $project['project_image']['url'] : '';
            ?>
                <div class="project-item" data-index="<?php echo esc_attr($index); ?>">
                    <div class="project-title-container">
                        <div class="hover-indicator left"></div>
                        <h2 class="project-title"><?php echo esc_html($project['project_title']); ?></h2>
                        <div class="hover-indicator right"></div>
                    </div>
                    <div class="project-content">
                        <div class="project-details left">
                            <?php foreach($left_details as $detail): ?>
                                <p class="detail-label"><?php echo esc_html($detail); ?></p>
                            <?php endforeach; ?>
                        </div>
                        <div class="project-image">
                            <?php if($image_url): ?>
                                <img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($project['project_title']); ?>" />
                            <?php endif; ?>
                        </div>
                        <div class="project-details right">
                            <?php foreach($right_details as $detail): ?>
                                <p class="detail-label"><?php echo esc_html($detail); ?></p>
                            <?php endforeach; ?>
                            <p class="detail-year"><?php echo esc_html('/'.$project['project_year']); ?></p>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <style>
        .gsap-list-component { display:flex; flex-direction:column; gap:3rem; scroll-snap-type: y mandatory; }
        .project-item { position:relative; cursor:pointer; overflow:hidden; transition: transform 0.3s; scroll-snap-align: start; }
        .project-item.inactive { filter: blur(2px) brightness(0.8); opacity:0.5; }
        .project-title-container { display:flex; justify-content:space-between; align-items:center; margin-bottom:1rem; }
        .hover-indicator { width:0; height:2px; background:#000; transition:0.4s; }
        .project-item:hover .hover-indicator { width:50px; }
        .project-title { font-weight:700; }
        .project-content { display:flex; gap:2rem; }
        .project-details { flex:1; opacity:0; transform:translateY(20px); }
        .project-image img { width:100%; height:auto; object-fit:cover; opacity:0; transform:scale(1.05); }
        </style>

        <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.13.0/gsap.min.js"></script>
        <script>
        (function(){
            const items = gsap.utils.toArray(".gsap-list-component .project-item");
            let activeIndex = -1;

            function setInactive(idx){
                items.forEach((el,i)=>{
                    if(i!==idx) el.classList.add('inactive'); 
                    else el.classList.remove('inactive');
                });
            }

            items.forEach((item, index) => {
                const img = item.querySelector("img"),
                      leftDetails = item.querySelectorAll(".project-details.left .detail-label"),
                      rightDetails = item.querySelectorAll(".project-details.right .detail-label");

                item.addEventListener("mouseenter", () => {
                    if(img) gsap.to(img, {opacity:1, scale:1.05, duration:0.5});
                    gsap.to(item.querySelectorAll(".hover-indicator"), {width:50, duration:0.4});
                });

                item.addEventListener("mouseleave", () => {
                    if(index!==activeIndex){
                        if(img) gsap.to(img, {opacity:0, scale:1, duration:0.5});
                        gsap.to(item.querySelectorAll(".hover-indicator"), {width:0, duration:0.4});
                    }
                });

                item.addEventListener("click", () => {
                    activeIndex = index;
                    setInactive(activeIndex);

                    if(img) gsap.to(img, {opacity:1, scale:1.1, duration:0.5});
                    gsap.to(leftDetails, {opacity:1, y:0, stagger:0.1, duration:0.5});
                    gsap.to(rightDetails, {opacity:1, y:0, stagger:0.1, duration:0.5});

                    // scroll to center active
                    item.scrollIntoView({behavior:"smooth", block:"center"});
                });
            });
        })();
        </script>
        <?php
    }
}

\Elementor\Plugin::instance()->widgets_manager->register_widget_type(new GSAP_Text_List_Image());
