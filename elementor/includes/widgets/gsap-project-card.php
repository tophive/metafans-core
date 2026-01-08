<?php
/**
 * Widget Name: GSAP Project Card
 * Description: Provides a grid of project cards with interactive GSAP marbling effect.
 * Version: 1.0
 * Author: Tophive
 *
 * @package My_Core_Plugin\Widgets
 */

namespace My_Core_Plugin\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Repeater;
use Elementor\Utils;

if(!defined('ABSPATH')) exit;

/**
 * Class Gsap_Project_Card
 * @since 1.0.0
 */
class Gsap_Project_Card extends Widget_Base {

    public function get_name(){ return 'gsap_project_card'; }
    public function get_title(){ return __('GSAP Project Card','my-core-plugin'); }
    public function get_icon(){ return 'eicon-ehp-zigzag'; }
    public function get_categories(){ return ['th-general']; }
    public function get_script_depends(){ return ['tophive-elementor-bundle']; } // Correctly depends on registered scripts
    public function get_style_depends(){ return ['tophive-elements-css']; } // Depends on the new stylesheet

    // -------------------- Controls --------------------
    protected function register_controls(){

        // ---------------- GRID ----------------
        $this->start_controls_section('grid_section',['label'=>__('Grid','my-core-plugin'),'tab'=>Controls_Manager::TAB_CONTENT]);
        $this->add_control('show_grid_overlay',['label'=>__('Grid Lines','my-core-plugin'),'type'=>Controls_Manager::SWITCHER,'return_value'=>'yes','default'=>'']);
        $this->add_control('grid_overlay_animate',['label'=>__('Animate Overlay Lines','my-core-plugin'),'type'=>Controls_Manager::SWITCHER,'return_value'=>'yes','default'=>'yes','condition'=>['show_grid_overlay'=>'yes']]);
        $this->add_control('grid_overlay_duration',['label'=>__('Animation Duration (s)','my-core-plugin'),'type'=>Controls_Manager::NUMBER,'default'=>1.2,'min'=>0.1,'step'=>0.05,'condition'=>['show_grid_overlay'=>'yes','grid_overlay_animate'=>'yes']]);
        $this->add_control('grid_overlay_stagger',['label'=>__('Stagger (s)','my-core-plugin'),'type'=>Controls_Manager::NUMBER,'default'=>0.08,'min'=>0,'step'=>0.01,'condition'=>['show_grid_overlay'=>'yes','grid_overlay_animate'=>'yes']]);
        $this->add_control('grid_overlay_ease',['label'=>__('Ease','my-core-plugin'),'type'=>Controls_Manager::SELECT,'default'=>'power2.out','options'=>['power1.out'=>'power1.out','power2.out'=>'power2.out','power3.out'=>'power3.out','expo.out'=>'expo.out','none'=>'none'],'condition'=>['show_grid_overlay'=>'yes','grid_overlay_animate'=>'yes']]);
        $this->add_control('columns',['label'=>__('Columns','my-core-plugin'),'type'=>Controls_Manager::SLIDER,'range'=>['custom'=>['min'=>1,'max'=>12,'step'=>1]],'default'=>['size'=>12],'condition'=>['show_grid_overlay'=>'yes']]);
        $this->add_control('gap',['label'=>__('Gap (px)','my-core-plugin'),'type'=>Controls_Manager::NUMBER,'default'=>12,'min'=>0,'step'=>1,'condition'=>['show_grid_overlay'=>'yes']]);
        $this->end_controls_section();

        // ---------------- ITEMS ----------------
        $this->start_controls_section('items_section',['label'=>__('Projects','my-core-plugin'),'tab'=>Controls_Manager::TAB_CONTENT]);
        $repeater = new Repeater();
        $repeater->add_control('image',['label'=>'Image','type'=>Controls_Manager::MEDIA,'default'=>['url'=>Utils::get_placeholder_image_src()]]);
        $repeater->add_control('alt',['label'=>'Alt Text','type'=>Controls_Manager::TEXT,'default'=>'Project image']);
        $repeater->add_control('date',['label'=>'Date / Kicker','type'=>Controls_Manager::TEXT,'default'=>'Branding']);
        $repeater->add_control('title',['label'=>'Title','type'=>Controls_Manager::TEXT,'default'=>'The Blue Silence']);
        $repeater->add_control('subtitle',['label'=>'Subtitle','type'=>Controls_Manager::TEXT,'default'=>'Exploring the depths']);
        $repeater->add_control('height',['label'=>'Tile Height (px)','type'=>Controls_Manager::NUMBER,'default'=>350]);
        $repeater->add_control('col_start',['label'=>'Column Start','type'=>Controls_Manager::NUMBER,'default'=>1,'min'=>1,'max'=>12]);
        $repeater->add_control('col_span',['label'=>'Column Span','type'=>Controls_Manager::NUMBER,'default'=>3,'min'=>1,'max'=>12]);
        $this->add_control('items',['label'=>'Grid Items','type'=>Controls_Manager::REPEATER,'fields'=>$repeater->get_controls(),'title_field'=>'{{{ title }}}']);
        $this->end_controls_section();

        // ---------------- EFFECT ----------------
        $this->start_controls_section('section_settings',['label'=>__('Effect Settings','my-core-plugin'),'tab'=>Controls_Manager::TAB_CONTENT]);
        $this->add_control('enable_effect',['label'=>__('Marbling Effect','my-core-plugin'),'type'=>Controls_Manager::SWITCHER,'return_value'=>'yes','default'=>'yes']);
        $this->add_control('effect_mode',['label'=>__('Effect Mode','my-core-plugin'),'type'=>Controls_Manager::SELECT,'options'=>['invert'=>'Invert Inside','reveal'=>'Reveal Original'],'default'=>'invert','condition'=>['enable_effect'=>'yes']]);
        $this->add_control('brush_radius',['label'=>__('Brush Radius','my-core-plugin'),'type'=>Controls_Manager::SLIDER,'size_units'=>['px'],'range'=>['px'=>['min'=>0,'max'=>0.6,'step'=>0.01]],'default'=>['size'=>0.35],'condition'=>['enable_effect'=>'yes']]);
        $this->add_control('swirl_strength',['label'=>__('Swirl Strength','my-core-plugin'),'type'=>Controls_Manager::SLIDER,'size_units'=>['px'],'range'=>['px'=>['min'=>0.5,'max'=>5,'step'=>0.01]],'default'=>['size'=>2.6],'condition'=>['enable_effect'=>'yes']]);
        $this->add_control('invert_strength',['label'=>__('Effect Intensity','my-core-plugin'),'type'=>Controls_Manager::SLIDER,'size_units'=>['px'],'range'=>['px'=>['min'=>0,'max'=>1,'step'=>0.01]],'default'=>['size'=>1.0],'condition'=>['enable_effect'=>'yes']]);
        $this->add_control('marble_speed',['label'=>__('Marble Speed','my-core-plugin'),'type'=>Controls_Manager::SLIDER,'size_units'=>['px'],'range'=>['px'=>['min'=>0,'max'=>3,'step'=>0.01]],'default'=>['size'=>1.0],'condition'=>['enable_effect'=>'yes']]);
        $this->add_control('edge_softness',['label'=>__('Edge Softness','my-core-plugin'),'type'=>Controls_Manager::SLIDER,'size_units'=>['px'],'range'=>['px'=>['min'=>0,'max'=>0.5,'step'=>0.01]],'default'=>['size'=>0],'condition'=>['enable_effect'=>'yes']]);
        $this->add_control('tint_a',['label'=>__('Tint A','my-core-plugin'),'type'=>Controls_Manager::COLOR,'default'=>'#0a0910','condition'=>['enable_effect'=>'yes']]);
        $this->add_control('tint_b',['label'=>__('Tint B','my-core-plugin'),'type'=>Controls_Manager::COLOR,'default'=>'#9d79ff','condition'=>['enable_effect'=>'yes']]);
        $this->add_control('wp_media_only',['label'=>__('WP Media Only (CORS Safe)','my-core-plugin'),'type'=>Controls_Manager::SWITCHER,'return_value'=>'yes','default'=>'','condition'=>['enable_effect'=>'yes']]);
        $this->end_controls_section();

        // ---------------- TYPOGRAPHY ----------------
        $this->start_controls_section('typo_section',['label'=>'Captions Style','tab'=>Controls_Manager::TAB_STYLE]);
        $this->add_group_control(Group_Control_Typography::get_type(),['name'=>'date_typo','label'=>'Date Typography','selector'=>'{{WRAPPER}} .mi-date']);
        $this->add_control('date_color',['label'=>'Date Color','type'=>Controls_Manager::COLOR,'selectors'=>['{{WRAPPER}} .mi-date'=>'color: {{VALUE}};']]);
        $this->add_group_control(Group_Control_Typography::get_type(),['name'=>'title_typo','label'=>'Title Typography','selector'=>'{{WRAPPER}} .mi-title']);
        $this->add_control('title_color',['label'=>'Title Color','type'=>Controls_Manager::COLOR,'selectors'=>['{{WRAPPER}} .mi-title'=>'color: {{VALUE}};']]);
        $this->add_group_control(Group_Control_Typography::get_type(),['name'=>'subtitle_typo','label'=>'Subtitle Typography','selector'=>'{{WRAPPER}} .mi-subtitle']);
        $this->add_control('subtitle_color',['label'=>'Subtitle Color','type'=>Controls_Manager::COLOR,'selectors'=>['{{WRAPPER}} .mi-subtitle'=>'color: {{VALUE}};']]);
        $this->end_controls_section();
    }

    // ---------------- HELPER ----------------
    protected static function hex2rgb_normalized($hex){
        $hex=str_replace('#','',$hex);
        if(strlen($hex)===3) $hex=$hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
        return [hexdec(substr($hex,0,2))/255,hexdec(substr($hex,2,2))/255,hexdec(substr($hex,4,2))/255];
    }

    // ---------------- RENDER ----------------
    protected function render(){
        $s=$this->get_settings_for_display();
        $uid='gsap-project-'.$this->get_id();

        $data_settings=[
            'show_grid_overlay'=>($s['show_grid_overlay']??'')==='yes',
            'grid_overlay_animate'=>($s['grid_overlay_animate']??'')==='yes',
            'grid_overlay_duration'=>(float)($s['grid_overlay_duration']??1.2),
            'grid_overlay_stagger'=>(float)($s['grid_overlay_stagger']??0.08),
            'grid_overlay_ease'=>$s['grid_overlay_ease']??'power2.out',
            'enable_effect'=>($s['enable_effect']??'')==='yes',
            'effect_mode'=>$s['effect_mode']??'invert',
            'brush_radius'=>(float)($s['brush_radius']['size']??0.35),
            'swirl_strength'=>(float)($s['swirl_strength']['size']??2.6),
            'invert_strength'=>(float)($s['invert_strength']['size']??1.0),
            'marble_speed'=>(float)($s['marble_speed']['size']??1.0),
            'edge_softness'=>(float)($s['edge_softness']['size']??0),
            'tintA'=>self::hex2rgb_normalized($s['tint_a']??'#0a0910'),
            'tintB'=>self::hex2rgb_normalized($s['tint_b']??'#9d79ff'),
            'wp_media_only'=>($s['wp_media_only']??'')==='yes',
            'columns'=>(int)($s['columns']['size']??12),
            'gap'=>(int)($s['gap']??12),
            'items'=>$s['items']??[]
        ];

        // ---------------- INLINE RENDER ----------------
        $cols = max(1,min(12,$data_settings['columns']));
        $gap = (int)$data_settings['gap'];
        $placeholder = \Elementor\Utils::get_placeholder_image_src();
        $site_host = parse_url(home_url(), PHP_URL_HOST);
        ?>

        <div id="<?php echo esc_attr($uid); ?>" class="mi-grid-widget" data-settings='<?php echo wp_json_encode($data_settings); ?>'>
            <?php if($data_settings['show_grid_overlay']): ?>
            <div class="mi-grid-overlay" aria-hidden="true">
                <div class="mi-grid-overlay-inner" style="display:grid;grid-template-columns:repeat(<?php echo $cols; ?>,1fr);gap:<?php echo $gap; ?>px;height:100%;">
                    <?php for($i=0;$i<$cols;$i++): ?><div class="mi-grid-col"></div><?php endfor; ?>
                </div>
            </div>
            <?php endif; ?>

            <div class="mi-grid-inner" style="display:grid;grid-template-columns:repeat(<?php echo $cols; ?>,1fr);gap:<?php echo $gap; ?>px;">
                <?php foreach($data_settings['items'] as $idx=>$item):
                    $img = $item['image']['url'] ?? $placeholder;
                    $alt = $item['alt'] ?? '';
                    $date = $item['date'] ?? '';
                    $title = $item['title'] ?? '';
                    $subtitle = $item['subtitle'] ?? '';
                    $height = isset($item['height']) ? (int)$item['height'] : 350;
                    $col_start = max(1, min($cols, (int)($item['col_start'] ?? 1)));
                    $col_span = max(1, min($cols, (int)($item['col_span'] ?? 3)));
                    $tile_id = $uid.'-tile-'.$idx;
                ?>
                <article class="mi-item" role="listitem" style="grid-column: <?php echo $col_start; ?> / span <?php echo $col_span; ?>;">
                    <figure class="mi-figure">
                        <div id="<?php echo esc_attr($tile_id); ?>" class="mi-media" data-mi-lens data-img-src="<?php echo esc_url($img); ?>" style="height:<?php echo $height; ?>px;">
                            <img class="mi-img" src="<?php echo esc_url($img); ?>" alt="<?php echo esc_attr($alt); ?>">
                        </div>
                        <figcaption class="mi-info">
                            <?php if($date): ?><div class="mi-date"><?php echo esc_html($date); ?></div><?php endif; ?>
                            <?php if($title): ?><h3 class="mi-title"><?php echo esc_html($title); ?></h3><?php endif; ?>
                            <?php if($subtitle): ?><p class="mi-subtitle"><?php echo esc_html($subtitle); ?></p><?php endif; ?>
                        </figcaption>
                    </figure>
                </article>
                <?php endforeach; ?>
            </div>
        </div>

        <?php
    }
}
