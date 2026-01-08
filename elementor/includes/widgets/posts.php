<?php
require_once plugin_dir_path(__FILE__) . 'traits/Tophive_Content_Card_Trait.php';
require_once plugin_dir_path(__FILE__) . 'traits/Tophive_Card_Style_Controls_Trait.php';
require_once plugin_dir_path(__FILE__) . 'traits/Tophive_Card_UI_Controls_Trait.php';
require_once plugin_dir_path(__FILE__) . 'traits/Tophive_Carousel_Trait.php';

class Posts_Card_Widget extends \Elementor\Widget_Base {
    use Tophive_Content_Card_Trait;
    use Tophive_Card_Style_Controls_Trait;
    use Tophive_Carousel_Trait;
    use Tophive_Card_UI_Controls_Trait;


    public function get_name() {
        return 'tophive-posts-card';
    }

    public function get_title() {
        return 'Blog Card';
    }

    public function get_icon() {
        return 'eicon-post-list';
    }

    public function get_categories() {
        return [ 'th-general' ];
    }

    public function get_script_depends() {
        return ['tophive-elementor-bundle'];
    }

    protected function register_controls(): void {

        $this->start_controls_section('base_section', [
            'label' => esc_html__('Base', 'plugin-name'),
            'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
        ]);


        $this->add_control('post_type', [
            'label' => esc_html__('Post Type', 'plugin-name'),
            'type' => \Elementor\Controls_Manager::SELECT,
            'default' => 'post',
            'options' => [
                'post' => 'Post',
                'page' => 'Page',
                'product' => 'Product',
                'portfolio' => 'Portfolio',
            ],
        ]);

        $this->add_responsive_control('grid_columns', [
            'label' => esc_html__('Columns', 'plugin-name'),
            'type' => \Elementor\Controls_Manager::SLIDER,
            'size_units' => ['px'],
            'range' => [
                'px' => [
                    'min' => 1,
                    'max' => 6,
                    'step' => 1,
                ],
            ],
            'default' => [
                'size' => 3,
            ],
            'selectors' => [
                '{{WRAPPER}} .tophive-card-grid' => 'grid-template-columns: repeat({{SIZE}}, minmax(0, 1fr));',
            ],
        ]);
        $this->add_responsive_control('grid_columns_gap', [
            'label' => esc_html__('Gap', 'plugin-name'),
            'type' => \Elementor\Controls_Manager::SLIDER,
            'size_units' => ['px'],
            'range' => [
                'px' => [
                    'min' => 1,
                    'max' => 100,
                    'step' => 1,
                ],
            ],
            'default' => [
                'size' => 10,
            ],
            'selectors' => [
                '{{WRAPPER}} .tophive-card-grid' => 'gap: {{SIZE}}{{UNIT}};',
            ],
        ]);
        $this->add_control(
			'hidden_class',[
            'type' => \Elementor\Controls_Manager::HIDDEN,
            'default' => 'z',
            'prefix_class' => 'tophive-card-element ',
        ]);

        $repeater = new \Elementor\Repeater();
        $repeater->add_control('section_key', [
            'label' => esc_html__('Section', 'plugin-name'),
            'type' => \Elementor\Controls_Manager::SELECT,
            'default' => 'title',
            'options' => [
                'image'        => __('Image', 'plugin-name'),
                'badge'        => __('Badge', 'plugin-name'),
                'price'        => __('Price', 'plugin-name'),
                'title'        => __('Title', 'plugin-name'),
                'meta'         => __('Meta (Author/Date)', 'plugin-name'),
                'tags'         => __('Tags', 'plugin-name'),
                'description'  => __('Description', 'plugin-name'),
                'rating'       => __('Rating', 'plugin-name'),
                'avatar'       => __('Avatar', 'plugin-name'),
                'video'        => __('Video Popup', 'plugin-name'),
                'hover'        => __('Hover Content', 'plugin-name'),
                'read_more'    => __('Read More Button', 'plugin-name'),
                'cta'          => __('CTA Button', 'plugin-name'),
                'social'       => __('Social Share', 'plugin-name'),
                'status'       => __('Status Badge', 'plugin-name'),
            ],
        ]);

        $this->add_control('layout_order', [
            'label' => esc_html__('Card Element Order', 'plugin-name'),
            'type' => \Elementor\Controls_Manager::REPEATER,
            'fields' => $repeater->get_controls(),
            'default' => [
                ['section_key' => 'image'],
                ['section_key' => 'title'],
                ['section_key' => 'description'],
                ['section_key' => 'read_more'],
            ],
            'title_field' => '{{{ section_key }}}',
        ]);

        $this->end_controls_section();

        
        $this->add_heading_ui_control();
        
        $this->add_content_ui_control();
        
        $this->add_overlay_content_ui_control($repeater);

        $this->add_read_more_link();

        $this->add_carousel_settings();
        
        // STYLING SECTION STARTS
        $this->add_box_style_controls();

        $this->add_media_style_controls();

        $this->maybe_add_dynamic_style_controls('blog');

        $this->add_carousel_style_controls();

        $this->init_wrapper_classes_hook();
    }
    

    public function render() {
        $settings = $this->get_settings_for_display();

        // Build layout order array safely
        $layout_order = [];
        if ( isset($settings['layout_order']) && is_array($settings['layout_order']) ) {
            foreach ( $settings['layout_order'] as $item ) {
                if ( isset( $item['section_key'] ) ) {
                    $layout_order[] = $item['section_key'];
                }
            }
        }

        $overlay_order = [];
        if ( isset($settings['overlay_order']) && is_array($settings['overlay_order']) ) {
            foreach ( $settings['overlay_order'] as $item ) {
                if ( isset( $item['section_key'] ) ) {
                    $overlay_order[] = $item['section_key'];
                }
            }
        }

        $query = new WP_Query([
            'post_type' => $settings['post_type'] ?? 'post',
            'posts_per_page' => 3,
        ]);

        
        if ( $query->have_posts() ) {
            
            $this->start_carousel_wrapper($settings);
        
            while ( $query->have_posts() ) {
                $query->the_post();
        
                if (!empty($settings['enable_carousel']) && $settings['enable_carousel'] === 'yes') {
                    echo '<div class="swiper-slide">';
                }
        
                $this->render_content_card([
                    'title'         => get_the_title(),
                    'description'   => get_the_excerpt(),
                    'image_url'     => get_the_post_thumbnail_url(),
                    'read_more_link'=> get_permalink(),
                    'permalink'     => get_permalink(),
                    'meta_author'   => get_the_author(),
                    'meta_date'     => get_the_date(),
                    'tags'          => wp_get_post_tags(get_the_ID(), ['fields' => 'names']),
                    'layout_order'  => $layout_order,
                    'overlay_order' => $overlay_order,
                    'show_image'    => true,
                    'title_tag'     => 'h3',
                    'settings'      => $settings
                ]);
        
                if (!empty($settings['enable_carousel']) && $settings['enable_carousel'] === 'yes') {
                    echo '</div>';
                }
            }
            
            $this->end_carousel_wrapper($settings);
        
            wp_reset_postdata();
        }

        
    }
}

\Elementor\Plugin::instance()->widgets_manager->register( new Posts_Card_Widget() );
