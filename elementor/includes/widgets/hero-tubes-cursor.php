<?php
/**
 * Plugin Name: Tubes Cursor Widget
 * Description: Simplified Tubes Cursor for Elementor (single variant)
 * Version: 1.0
 * Author: Tophive
 */

namespace My_Core_Plugin\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Text_Stroke;
use Elementor\Repeater;

if (!defined('ABSPATH')) exit;

class Tubes_Cursor_Widget extends Widget_Base {

    public function get_name() { return 'tubes_cursor_widget'; }
    public function get_title() { return __('Tubes Cursor', 'my-core-plugin'); }
    public function get_icon() { return 'eicon-animation-text'; }
    public function get_categories() { return ['th-general']; }

    protected function register_controls() {

        /** ---------------- CONTENT TAB ---------------- */
        $this->start_controls_section('content_section',['label'=>'Content']);
        $this->add_control('title_text',['label'=>'Title','type'=>Controls_Manager::TEXT,'default'=>'Tubes','label_block'=>true]);
        $this->add_control('subtitle_text',['label'=>'Subtitle','type'=>Controls_Manager::TEXTAREA,'default'=>'Cursor']);
        $this->add_control('link_text',['label'=>'Link Text','type'=>Controls_Manager::TEXT,'default'=>'Framer Component','label_block'=>true]);
        $this->add_control('link_url',['label'=>'Link URL','type'=>Controls_Manager::URL,'default'=>['url'=>'#','is_external'=>true]]);
        $this->end_controls_section();

        /** ---------------- STYLE TAB: Slider Background ---------------- */
        $this->start_controls_section('slider_style_section',['label'=>'Slider Background','tab'=>Controls_Manager::TAB_STYLE]);
        $this->add_group_control(Group_Control_Background::get_type(),['name'=>'slider_bg','types'=>['classic','gradient','video'],'selector'=>'{{WRAPPER}} .tubes-cursor-wrapper']);
        $this->add_responsive_control('slider_height',['label'=>'Height','type'=>Controls_Manager::SLIDER,'size_units'=>['px','%','vh'],'range'=>['px'=>['min'=>200,'max'=>2000],'%'=>['min'=>10,'max'=>100],'vh'=>['min'=>10,'max'=>100]],'default'=>['size'=>100,'unit'=>'vh'],'selectors'=>['{{WRAPPER}} .tubes-cursor-wrapper'=>'height: {{SIZE}}{{UNIT}};']]);
        $this->add_group_control(Group_Control_Border::get_type(),['name'=>'slider_border','selector'=>'{{WRAPPER}} .tubes-cursor-wrapper']);
        $this->add_responsive_control('slider_radius',['label'=>'Border Radius','type'=>Controls_Manager::DIMENSIONS,'size_units'=>['px','%'],'selectors'=>['{{WRAPPER}} .tubes-cursor-wrapper'=>'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow:hidden;']]);
        $this->end_controls_section();

        /** ---------------- STYLE TAB: Font ---------------- */
        $this->start_controls_section('font_style_section',['label'=>'Font Style','tab'=>Controls_Manager::TAB_STYLE]);
        $this->add_control('heading_h1_title',['label'=>'Heading','type'=>Controls_Manager::HEADING,'separator'=>'before']);
        $this->add_group_control(Group_Control_Typography::get_type(),['name'=>'h1_typography','label'=>'Heading Typography','selector'=>'{{WRAPPER}} .tubes-cursor-widget h1']);
        $this->add_group_control(Group_Control_Text_Stroke::get_type(),['name'=>'h1_text_stroke','selector'=>'{{WRAPPER}} .tubes-cursor-widget h1']);
        $this->add_control('h1_color',['label'=>'Heading Color','type'=>Controls_Manager::COLOR,'selectors'=>['{{WRAPPER}} .tubes-cursor-widget h1'=>'color: {{VALUE}};']]);
        $this->add_control('heading_h2_title',['label'=>'Subheading','type'=>Controls_Manager::HEADING,'separator'=>'before']);
        $this->add_group_control(Group_Control_Typography::get_type(),['name'=>'h2_typography','label'=>'Subheading Typography','selector'=>'{{WRAPPER}} .tubes-cursor-widget h2']);
        $this->add_control('h2_color',['label'=>'Subheading Color','type'=>Controls_Manager::COLOR,'selectors'=>['{{WRAPPER}} .tubes-cursor-widget h2'=>'color: {{VALUE}};']]);
        $this->add_control('link_heading',['label'=>'Link Text','type'=>Controls_Manager::HEADING,'separator'=>'before']);
        $this->add_group_control(Group_Control_Typography::get_type(),['name'=>'link_typography','label'=>'Link Typography','selector'=>'{{WRAPPER}} .tubes-cursor-widget a']);
        $this->add_control('link_color',['label'=>'Link Color','type'=>Controls_Manager::COLOR,'selectors'=>['{{WRAPPER}} .tubes-cursor-widget a'=>'color: {{VALUE}};']]);
        $this->add_control('link_hover_color',['label'=>'Link Hover Color','type'=>Controls_Manager::COLOR,'selectors'=>['{{WRAPPER}} .tubes-cursor-widget a:hover'=>'color: {{VALUE}};']]);
        $this->add_responsive_control('text_gap',['label'=>'Text Gap','type'=>Controls_Manager::SLIDER,'size_units'=>['px'],'range'=>['px'=>['min'=>0,'max'=>200]],'default'=>['size'=>10],'selectors'=>['{{WRAPPER}} .tubes-cursor-widget .hero'=>'gap: {{SIZE}}{{UNIT}};']]);
        $this->end_controls_section();

        /** ---------------- STYLE TAB: Tubes Cursor ---------------- */
        $this->start_controls_section('tubes_cursor_section',['label'=>'Tubes Cursor','tab'=>Controls_Manager::TAB_STYLE]);
        $this->add_control('enable_tubes_cursor',['label'=>'Enable Tubes Cursor','type'=>Controls_Manager::SWITCHER,'return_value'=>'yes','default'=>'yes']);
        $this->add_control('cursor_size',['label'=>'Cursor Size','type'=>Controls_Manager::SLIDER,'size_units'=>['px'],'range'=>['px'=>['min'=>1,'max'=>100]],'default'=>['size'=>10]]);
        $this->add_control('cursor_trail',['label'=>'Trail / Particle Count','type'=>Controls_Manager::SLIDER,'size_units'=>['px'],'range'=>['px'=>['min'=>1,'max'=>200]],'default'=>['size'=>50]]);
        $this->add_control('cursor_speed',['label'=>'Cursor Speed','type'=>Controls_Manager::SLIDER,'size_units'=>[''],'range'=>['min'=>0.01,'max'=>1,'step'=>0.01],'default'=>['size'=>0.15]]);

        $repeater = new Repeater();
        $repeater->add_control('color',['label'=>'Color','type'=>Controls_Manager::COLOR,'default'=>'#ff0000']);
        $this->add_control('cursor_colors',['label'=>'Cursor Colors','type'=>Controls_Manager::REPEATER,'fields'=>$repeater->get_controls(),'default'=>[['color'=>'#ff0000'],['color'=>'#00ff00'],['color'=>'#0000ff']],'title_field'=>'{{color}}']);
        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        $unique_id = 'tubes_cursor_'.$this->get_id();
        $link = $settings['link_url'] ?? ['url'=>'#','is_external'=>true];
        $radius = $settings['slider_radius'] ?? ['top'=>0,'right'=>0,'bottom'=>0,'left'=>0];
        ?>

<div id="<?php echo esc_attr($unique_id); ?>" class="tubes-cursor-widget">
    <div class="tubes-cursor-wrapper" style="
        border-radius: <?php echo esc_attr($radius['top']).'px '.$radius['right'].'px '.$radius['bottom'].'px '.$radius['left'].'px'; ?>;
        overflow:hidden;
        <?php if(!empty($settings['slider_bg']['url'])): ?>
            background-image: url('<?php echo esc_url($settings['slider_bg']['url']); ?>');
            background-size: cover;
            background-position: center;
        <?php endif; ?>
    ">
        <canvas id="<?php echo esc_attr($unique_id); ?>_canvas" class="tubes-canvas"></canvas>
        <div class="hero">
            <h1><?php echo esc_html($settings['title_text']); ?></h1>
            <h2><?php echo esc_html($settings['subtitle_text']); ?></h2>
            <a href="<?php echo esc_url($link['url']); ?>" <?php echo $link['is_external'] ? 'target="_blank"' : ''; ?>>
                <?php echo esc_html($settings['link_text']); ?>
            </a>
        </div>
    </div>
</div>

<?php if($settings['enable_tubes_cursor']==='yes'): ?>
<script type="module">
import * as THREE from 'https://cdn.jsdelivr.net/npm/three@0.158.0/build/three.module.js';
const canvas = document.getElementById('<?php echo esc_js($unique_id); ?>_canvas');
canvas.width = canvas.parentElement.offsetWidth;
canvas.height = canvas.parentElement.offsetHeight;

let cursorApp;
const settingsJS = {
    size: <?php echo floatval($settings['cursor_size']['size']); ?>,
    trail: <?php echo floatval($settings['cursor_trail']['size']); ?>,
    speed: <?php echo floatval($settings['cursor_speed']['size']); ?>,
    colors: [<?php echo implode(',', array_map(fn($c)=>"'".esc_js($c['color'])."'", $settings['cursor_colors'])); ?>]
};

async function initCursor(){
    if(cursorApp && cursorApp.dispose) cursorApp.dispose();
    const {default: TubesCursor} = await import('https://cdn.jsdelivr.net/npm/threejs-components@0.0.19/build/cursors/tubes1.min.js');
    cursorApp = TubesCursor(canvas,{ tubes:{colors:settingsJS.colors, lights:{intensity:200, colors:settingsJS.colors}} });
}

initCursor();

window.addEventListener('resize',()=>{
    canvas.width = canvas.parentElement.offsetWidth;
    canvas.height = canvas.parentElement.offsetHeight;
    if(cursorApp && cursorApp.resizeCanvas) cursorApp.resizeCanvas();
});
</script>
<?php endif; ?>

<style>
.tubes-cursor-widget{position:relative;width:100%;height:100%;font-family:"Montserrat",sans-serif;}
.tubes-cursor-wrapper{position:relative;width:100%;height:100%;overflow:hidden;}
.tubes-canvas{position:absolute;inset:0;width:100%;height:100%;z-index:0;background:transparent !important;border-radius:inherit;}
.tubes-cursor-widget .hero{position:relative;z-index:1;height:100%;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:10px;color:#fff;text-shadow:0 0 20px rgba(0,0,0,1);}
.tubes-cursor-widget h1{margin:0;font-size:80px;font-weight:700;text-transform:uppercase;}
.tubes-cursor-widget h2{margin:0;font-size:60px;font-weight:500;text-transform:uppercase;}
.tubes-cursor-widget a{color:#fff;text-decoration:none;}
</style>

<?php
    }
}

// Register widget
\Elementor\Plugin::instance()->widgets_manager->register(new \My_Core_Plugin\Widgets\Tubes_Cursor_Widget());
