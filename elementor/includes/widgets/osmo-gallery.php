<?php
namespace My_Core_Plugin\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Group_Control_Typography;

if (!defined('ABSPATH')) exit;

class Osmo_Gallery extends Widget_Base {

    public function get_name() { return 'osmo_gallery'; }
    public function get_title() { return __('Osmo Gallery', 'my-core-plugin'); }
    public function get_icon() { return 'eicon-gallery-grid'; }
    public function get_categories() { return ['th-general']; }

    protected function register_controls() {

        // Layout & Style
        $this->start_controls_section('section_layout', ['label'=>__('Layout & Style','my-core-plugin')]);

        $this->add_control('layout', [
            'label'=>__('Layout', 'my-core-plugin'),
            'type'=>Controls_Manager::SELECT,
            'options'=>['grid'=>'Grid','justified'=>'Justified','masonry'=>'Masonry'],
            'default'=>'grid',
        ]);

        // Columns (Grid & Masonry)
        $this->add_responsive_control('columns', [
            'label' => __('Columns', 'my-core-plugin'),
            'type' => Controls_Manager::SLIDER,
            'size_units' => [''],
            'range' => ['px'=>['min'=>1,'max'=>6]],
            'default' => ['size'=>3],
            'condition' => ['layout!' => 'justified'],
        ]);

        // Grid item height
        $this->add_responsive_control('grid_item_height', [
            'label' => __('Grid Item Height (px)', 'my-core-plugin'),
            'type' => Controls_Manager::SLIDER,
            'size_units' => ['px'],
            'range' => ['px'=>['min'=>50,'max'=>1000]],
            'default' => ['size'=>250],
            'condition' => ['layout' => 'grid'],
        ]);

        // Row height for justified
        $this->add_responsive_control('justified_row_height', [
            'label'=>__('Justified Row Height', 'my-core-plugin'),
            'type'=>Controls_Manager::SLIDER,
            'size_units'=>['px'],
            'range'=>['px'=>['min'=>50,'max'=>600]],
            'default'=>['size'=>200],
            'condition' => ['layout' => 'justified'],
        ]);

        // Hover animation
        $this->add_responsive_control('hover_animation', [
            'label' => __('Hover Animation', 'my-core-plugin'),
            'type' => Controls_Manager::SELECT,
            'options' => ['none'=>'None','fade'=>'Fade','zoom'=>'Zoom','slide'=>'Slide'],
            'default' => 'fade',
        ]);

        // Link / Lightbox
        $this->add_control('link_type', [
            'label'=>__('Link Options', 'my-core-plugin'),
            'type'=>Controls_Manager::SELECT,
            'options'=>['none'=>'None','media'=>'Media File','custom'=>'Custom URL'],
            'default'=>'none',
        ]);

        $this->add_responsive_control('enable_lightbox', [
            'label' => __('Enable Lightbox', 'my-core-plugin'),
            'type' => Controls_Manager::SWITCHER,
            'return_value' => 'yes',
            'default' => 'yes',
        ]);

        // Layout-specific gaps
        $this->add_responsive_control('grid_gap', [
            'label' => __('Grid Gap (px)', 'my-core-plugin'),
            'type' => Controls_Manager::SLIDER,
            'size_units' => ['px'],
            'range' => ['px'=>['min'=>0,'max'=>50]],
            'default' => ['size'=>16],
            'condition' => ['layout' => 'grid'],
        ]);

        $this->add_responsive_control('masonry_gap', [
            'label' => __('Masonry Gap (px)', 'my-core-plugin'),
            'type' => Controls_Manager::SLIDER,
            'size_units' => ['px'],
            'range' => ['px'=>['min'=>0,'max'=>50]],
            'default' => ['size'=>16],
            'condition' => ['layout' => 'masonry'],
        ]);

        $this->add_responsive_control('justified_gap', [
            'label' => __('Justified Gap (px)', 'my-core-plugin'),
            'type' => Controls_Manager::SLIDER,
            'size_units' => ['px'],
            'range' => ['px'=>['min'=>0,'max'=>50]],
            'default' => ['size'=>6],
            'condition' => ['layout' => 'justified'],
        ]);

        $this->end_controls_section();

        // Gallery Items
        $this->start_controls_section('section_items', ['label'=>__('Content', 'my-core-plugin')]);

        $repeater = new Repeater();
        $repeater->add_control('image', [
            'label'=>__('Image', 'my-core-plugin'),
            'type'=>Controls_Manager::MEDIA,
            'default'=>['url'=>\Elementor\Utils::get_placeholder_image_src()],
        ]);
        $repeater->add_control('title', ['label'=>__('Title', 'my-core-plugin'),'type'=>Controls_Manager::TEXT]);
        $repeater->add_control('caption', ['label'=>__('Caption', 'my-core-plugin'),'type'=>Controls_Manager::TEXTAREA]);
        $repeater->add_control('link', ['label'=>__('Custom URL', 'my-core-plugin'),'type'=>Controls_Manager::URL,'placeholder'=>'https://']);

        $this->add_control('items', [
            'label'=>__('Gallery Items', 'my-core-plugin'),
            'type'=>Controls_Manager::REPEATER,
            'fields'=>$repeater->get_controls(),
            'title_field'=>'{{{ title }}}',
        ]);

        $this->end_controls_section();

        // Caption Style
        $this->start_controls_section('section_caption_style', [
            'label'=>__('Caption', 'my-core-plugin'),
            'tab' => Controls_Manager::TAB_STYLE
        ]);
        $this->add_group_control(Group_Control_Typography::get_type(), [
            'name' => 'caption_typography',
            'label' => __('Typography', 'my-core-plugin'),
            'selector' => '{{WRAPPER}} .osmo-caption',
        ]);
        $this->add_control('caption_color', [
            'label'=>__('Text Color', 'my-core-plugin'),
            'type'=>Controls_Manager::COLOR,
            'default'=>'#333333',
            'selectors'=>['{{WRAPPER}} .osmo-caption'=>'color: {{VALUE}};'],
        ]);
        $this->add_responsive_control('caption_align', [
            'label'=>__('Text Align', 'my-core-plugin'),
            'type'=>Controls_Manager::CHOOSE,
            'options'=>[
                'left'=>['title'=>__('Left','my-core-plugin'),'icon'=>'eicon-text-align-left'],
                'center'=>['title'=>__('Center','my-core-plugin'),'icon'=>'eicon-text-align-center'],
                'right'=>['title'=>__('Right','my-core-plugin'),'icon'=>'eicon-text-align-right'],
            ],
            'default'=>'center',
            'selectors'=>['{{WRAPPER}} .osmo-caption'=>'text-align: {{VALUE}};'],
        ]);
        $this->end_controls_section();

        // Title Style
        $this->start_controls_section('section_title_style', [
            'label'=>__('Title', 'my-core-plugin'),
            'tab'=>Controls_Manager::TAB_STYLE,
        ]);
        $this->add_group_control(Group_Control_Typography::get_type(), [
            'name' => 'title_typography',
            'label' => __('Typography', 'my-core-plugin'),
            'selector' => '{{WRAPPER}} .osmo-caption strong',
        ]);
        $this->add_control('title_color', [
            'label'=>__('Text Color', 'my-core-plugin'),
            'type' => Controls_Manager::COLOR,
            'default'=>'#000000',
            'selectors'=>['{{WRAPPER}} .osmo-caption strong'=>'color: {{VALUE}};'],
        ]);
        $this->add_responsive_control('title_align', [
            'label'=>__('Text Align', 'my-core-plugin'),
            'type'=>Controls_Manager::CHOOSE,
            'options'=>[
                'left'=>['title'=>__('Left','my-core-plugin'),'icon'=>'eicon-text-align-left'],
                'center'=>['title'=>__('Center','my-core-plugin'),'icon'=>'eicon-text-align-center'],
                'right'=>['title'=>__('Right','my-core-plugin'),'icon'=>'eicon-text-align-right'],
            ],
            'default'=>'center',
            'selectors'=>['{{WRAPPER}} .osmo-caption strong'=>'text-align: {{VALUE}};'],
        ]);
        $this->end_controls_section();

    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        $items = $settings['items'] ?? [];

        if(empty($items) && \Elementor\Plugin::$instance->editor->is_edit_mode()){
            $placeholder = \Elementor\Utils::get_placeholder_image_src();
            $items = [
                ['image'=>['url'=>$placeholder],'title'=>'Sample 1','caption'=>'Caption 1','link'=>['url'=>'#']],
                ['image'=>['url'=>$placeholder],'title'=>'Sample 2','caption'=>'Caption 2','link'=>['url'=>'#']],
                ['image'=>['url'=>$placeholder],'title'=>'Sample 3','caption'=>'Caption 3','link'=>['url'=>'#']],
            ];
        }
        if(empty($items)) return;

        $gallery_id = 'osmo-gallery-' . $this->get_id();
        $layout = $settings['layout'] ?? 'grid';
        $linkType = $settings['link_type'] ?? 'none';
        $hover = $settings['hover_animation'] ?? 'fade';
        $enableLightbox = ($settings['enable_lightbox'] ?? 'yes') === 'yes';

        // Responsive columns
        $columns_desktop = intval($settings['columns']['size'] ?? 3);
        $columns_tablet  = intval($settings['columns_tablet']['size'] ?? $columns_desktop);
        $columns_mobile  = intval($settings['columns_mobile']['size'] ?? $columns_tablet);

        $gridHeight = intval($settings['grid_item_height']['size'] ?? 250);

        // Justified responsive row heights
        $rowHeight_desktop = intval($settings['justified_row_height']['size'] ?? 200);
        $rowHeight_tablet  = intval($settings['justified_row_height_tablet']['size'] ?? $rowHeight_desktop);
        $rowHeight_mobile  = intval($settings['justified_row_height_mobile']['size'] ?? $rowHeight_tablet);

        // Gap per layout
        $gap = 16;
        if($layout==='grid') $gap = intval($settings['grid_gap']['size'] ?? 16);
        elseif($layout==='masonry') $gap = intval($settings['masonry_gap']['size'] ?? 16);
        elseif($layout==='justified') $gap = intval($settings['justified_gap']['size'] ?? 6);

        ?>

        <div id="<?php echo esc_attr($gallery_id); ?>" class="osmo-gallery osmo-<?php echo esc_attr($layout); ?>">
            <?php foreach($items as $item):
                $img = $item['image']['url'] ?? \Elementor\Utils::get_placeholder_image_src();
                $link = '';
                if($linkType==='media') $link = $img;
                elseif($linkType==='custom') $link = $item['link']['url'] ?? '';
            ?>
            <div class="osmo-gallery-item">
                <?php if($link): ?><a href="<?php echo esc_url($link); ?>" <?php echo $enableLightbox ? 'class="osmo-lightbox"' : ''; ?> rel="noopener"><?php endif; ?>
                    <img src="<?php echo esc_url($img); ?>" alt="<?php echo esc_attr($item['title'] ?: 'Gallery Image'); ?>"
                         class="osmo-hover-<?php echo esc_attr($hover); ?>"
                         style="width:100%; display:block; border-radius:0.5rem; object-fit:cover; transition:transform 0.3s ease, opacity 0.3s ease;
                         <?php if($layout==='grid'): ?>height:<?php echo $gridHeight; ?>px;<?php endif; ?>">
                    <?php if($item['title'] || $item['caption']): ?>
                        <div class="osmo-caption">
                            <?php if($item['title']): ?><strong><?php echo esc_html($item['title']); ?></strong><br><?php endif; ?>
                            <?php if($item['caption']): ?><span><?php echo esc_html($item['caption']); ?></span><?php endif; ?>
                        </div>
                    <?php endif; ?>
                <?php if($link): ?></a><?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>

        <style>
        #<?php echo esc_attr($gallery_id); ?> { display:flex; flex-wrap:wrap; margin:-<?php echo $gap/2; ?>px; }
        #<?php echo esc_attr($gallery_id); ?> .osmo-gallery-item { box-sizing:border-box; padding:<?php echo $gap/2; ?>px;
            width: calc(100% / <?php echo $columns_desktop; ?> - <?php echo $gap; ?>px); }

        @media(max-width:1024px){
            #<?php echo esc_attr($gallery_id); ?> .osmo-gallery-item { width: calc(100% / <?php echo $columns_tablet; ?> - <?php echo $gap; ?>px); }
        }
        @media(max-width:767px){
            #<?php echo esc_attr($gallery_id); ?> .osmo-gallery-item { width: calc(100% / <?php echo $columns_mobile; ?> - <?php echo $gap; ?>px); }
        }

        .osmo-hover-zoom:hover { transform: scale(1.05); }
        .osmo-hover-fade:hover { opacity:0.8; }
        .osmo-hover-slide:hover { transform: translateY(-5px); }
        </style>

        <script src="https://unpkg.com/masonry-layout@4/dist/masonry.pkgd.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/justifiedGallery/3.8.1/js/jquery.justifiedGallery.min.js"></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/justifiedGallery/3.8.1/css/justifiedGallery.min.css" />
        <script src="https://cdnjs.cloudflare.com/ajax/libs/simplelightbox/2.14.1/simple-lightbox.min.js"></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/simplelightbox/2.14.1/simple-lightbox.min.css" />

        <script>
        (function(){
            var gallery = document.getElementById('<?php echo esc_js($gallery_id); ?>');
            var layout = '<?php echo esc_js($layout); ?>';
            var enableLightbox = <?php echo $enableLightbox ? 'true' : 'false'; ?>;
            var gap = <?php echo $gap; ?>;

            var rowHeightDesktop = <?php echo $rowHeight_desktop; ?>;
            var rowHeightTablet = <?php echo $rowHeight_tablet; ?>;
            var rowHeightMobile = <?php echo $rowHeight_mobile; ?>;

            function getResponsiveRowHeight(){
                var w = window.innerWidth;
                if(w <= 767) return rowHeightMobile;
                else if(w <= 1024) return rowHeightTablet;
                else return rowHeightDesktop;
            }

            function initGallery(){
                if(layout==='masonry' && typeof Masonry !== 'undefined'){
                    new Masonry(gallery,{
                        itemSelector: '.osmo-gallery-item',
                        columnWidth: '.osmo-gallery-item',
                        percentPosition:true,
                        gutter: gap
                    });
                } else if(layout==='justified' && typeof jQuery !== 'undefined' && typeof jQuery.fn.justifiedGallery !== 'undefined'){
                    jQuery(gallery).justifiedGallery('destroy');
                    jQuery(gallery).justifiedGallery({
                        rowHeight: getResponsiveRowHeight(),
                        margins: gap,
                        lastRow: 'nojustify',
                        captions: false,
                        fixedHeight:true
                    });
                }

                if(enableLightbox && typeof SimpleLightbox !== 'undefined'){
                    if(gallery.simpleLightboxInstance) gallery.simpleLightboxInstance.destroy();
                    gallery.simpleLightboxInstance = new SimpleLightbox(gallery.querySelectorAll('.osmo-lightbox'), {});
                }
            }

            initGallery();
            window.addEventListener('resize', initGallery);
        })();
        </script>

    <?php
    }
}

if(class_exists('\Elementor\Plugin')){
    \Elementor\Plugin::instance()->widgets_manager->register(new Osmo_Gallery());
}
