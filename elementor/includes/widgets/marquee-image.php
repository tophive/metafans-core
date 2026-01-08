<?php
use Elementor\Controls_Manager;
use Elementor\Repeater;
// Plugin: marquee-text-widget.php
class Tophive_Marquee_Image extends \Elementor\Widget_Base {

    public function __construct($data = [], $args = null) {
		parent::__construct($data, $args);
	}
    public function get_name() {
        return 'tophive-marquee-image';
    }
    public function get_script_depends() {
		return [ 'tophive-elementor-bundle' ];
	}

    public function get_title() {
        return TH_ELEMENTOR_DISPLAY_NAME_SC . esc_html__( 'Image Marquee', TH_ELEMENTOR_SLUG );
    }

    public function get_icon() {
        return 'eicon-media-carousel';
    }

    public function get_categories() {
        return ['th-general'];
    }

    protected function register_controls() {
        $this->start_controls_section('content_section', [
            'label' => __('Content', 'plugin-name'),
        ]);
    
        $repeater = new \Elementor\Repeater();
    
        $repeater->add_control('image', [
            'label' => __('Image', 'plugin-name'),
            'type' => \Elementor\Controls_Manager::MEDIA,
            'default' => ['url' => \Elementor\Utils::get_placeholder_image_src()],
        ]);

        $repeater->add_control('label_text', [
            'label' => __('Overlay Text (optional)', 'plugin-name'),
            'type' => \Elementor\Controls_Manager::TEXT,
            'default' => '',
        ]);          
    
        $repeater->add_control('link', [
            'label' => __('Link (optional)', 'plugin-name'),
            'type' => \Elementor\Controls_Manager::URL,
            'placeholder' => __('https://your-link.com', 'plugin-name'),
            'show_external' => true,
        ]);
    
        $this->add_control('marquee_images', [
            'label' => __('Marquee Images', 'plugin-name'),
            'type' => \Elementor\Controls_Manager::REPEATER,
            'fields' => $repeater->get_controls(),
            'default' => [],
            'title_field' => 'Image',
        ]);
    
        $this->end_controls_section();
        /*
            SETTINGS
        */


        $this->start_controls_section('settings_section', [
            'label' => __('Settings', 'plugin-name'),
        ]);
        
        Tophive_Elementor_UI_Helper::marquee_controls($this);

        $this->end_controls_section();
        
        /*
            ADD GREY SCALE
        */

        $this->start_controls_section('image_grey_scale', [
            'label' => __('Greyscale', 'plugin-name'),
            'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
        ]);
        $this->add_control('add_grey_scale', [
            'label' => __('Add grey scale effect', 'plugin-name'),
            'type' => \Elementor\Controls_Manager::SWITCHER,
            'default' => 'no',
        ]);
    
        $this->end_controls_section();
    
        /**
         * Style: Image
         */
        $this->start_controls_section('style_image_section', [
            'label' => __('Image', 'plugin-name'),
            'tab' => \Elementor\Controls_Manager::TAB_STYLE,
        ]);
    
        $this->add_responsive_control('image_width', [
            'label' => __('Image Width', 'plugin-name'),
            'type' => \Elementor\Controls_Manager::SLIDER,
            'range' => ['px' => ['min' => 10, 'max' => 600]],
            'selectors' => [
                '{{WRAPPER}} .marquee-item' => 'width: {{SIZE}}{{UNIT}};',
            ],
        ]);
        $this->add_responsive_control('image_height', [
            'label' => __('Image Height', 'plugin-name'),
            'type' => \Elementor\Controls_Manager::SLIDER,
            'range' => ['px' => ['min' => 10, 'max' => 600]],
            'selectors' => [
                '{{WRAPPER}} .marquee-item' => 'height: {{SIZE}}{{UNIT}};',
            ],
        ]);
    
        $this->add_responsive_control('image_spacing', [
            'label' => __('Spacing Between Images', 'plugin-name'),
            'type' => \Elementor\Controls_Manager::SLIDER,
            'range' => ['px' => ['min' => 0, 'max' => 200]],
            'selectors' => [
                '{{WRAPPER}} .marquee-item' => 'margin-right: {{SIZE}}{{UNIT}};',
                '{{WRAPPER}}.direction-top .marquee-item, {{WRAPPER}}.direction-bottom .marquee-item' => 'margin-bottom: {{SIZE}}{{UNIT}};',
            ],
            'default' => [
                'size' => 20,
                'unit' => 'px',
            ],
        ]);
    
        $this->add_control('image_border_radius', [
            'label' => __('Image Border Radius', 'plugin-name'),
            'type' => \Elementor\Controls_Manager::DIMENSIONS,
            'selectors' => [
                '{{WRAPPER}} .marquee-item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]);
    
        $this->end_controls_section();
    
        /**
         * Style: Wrapper
         */
        $this->start_controls_section('style_wrapper_section', [
            'label' => __('Wrapper', 'plugin-name'),
            'tab' => \Elementor\Controls_Manager::TAB_STYLE,
        ]);
    
        Tophive_Elementor_UI_Helper::marquee_wrapper_control($this);
    
        $this->end_controls_section();

		Tophive_Elementor_Style_Helper::add_ui_style_controls($this, 'transform', '{{WRAPPER}} .tophive-marquee-wrapper', false, true, 'Transform');

    }
    
    protected function render() {
        $settings = $this->get_settings_for_display();
        $images = $settings['marquee_images'];
    
        if (empty($images)) {
            return;
        }

        $classes = ['tophive-marquee-wrapper', 'has-image'];
        if($settings['add_grey_scale'] == 'yes'){
            $classes[] = 'grey-scale-enabled';
        }
        if($settings['enable_mask'] == 'yes'){
            $classes[] = 'mask-enabled';
        }
        ?>
        <div class="tophive-marquee-scroll"></div>
            <div class="<?php echo implode(' ', $classes); ?>"
            <?= Tophive_Elementor_UI_Helper::tophive_data_attrs_from_settings($settings); ?>>
                <div class="tophive-marquee-track">
                    <div class="marquee-text">
                        <?php foreach ($images as $item): ?>
                            <?php
                            $bg = esc_url($item['image']['url']);
                            $hasLink = !empty($item['link']['url']);
                            ?>
                            <span class="marquee-item" style="background-image: url('<?= $bg ?>');">
                                <?php if ($hasLink): ?>
                                    <a href="<?= esc_url($item['link']['url']); ?>"
                                    <?php if (!empty($item['link']['is_external'])) echo 'target="_blank" rel="noopener noreferrer"'; ?>>
                                    </a>
                                <?php endif; ?>

                                <?php if (!empty($item['label_text'])): ?>
                                    <span class="marquee-label"><?= esc_html($item['label_text']) ?></span>
                                <?php endif; ?>
                            </span>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        <div class="tophive-marquee-scroll"></div>
        <?php
    }
    
  

}
\Elementor\Plugin::instance()->widgets_manager->register( new Tophive_Marquee_Image() );
