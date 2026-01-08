<?php
/**
 * Plugin Name: marquee text widget for Elementor
 * Description: An Elementor widget that displays a scrolling marquee of text items.
 * Version: 1.0
 * Author: Tophive
 */

/** 
 *
 * @package My_Core_Plugin\Widgets
 */
namespace My_Core_Plugin\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Repeater;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class Tophive_Marquee_Text
 * @since 1.0.0
 */
class Tophive_Marquee_Text extends \Elementor\Widget_Base {

    public function __construct($data = [], $args = null) {
		parent::__construct($data, $args);
	}
    public function get_name() {
        return 'tophive-marquee-text';
    }
    public function get_script_depends() {
		return [ 'tophive-elementor-bundle' ];
	}

    public function get_title() {
        return TH_ELEMENTOR_DISPLAY_NAME_SC . esc_html__( 'Text Marquee', TH_ELEMENTOR_SLUG );
    }

    public function get_icon() {
        return 'eicon-animation-text';
    }

    public function get_categories() {
        return ['th-general'];
    }

    protected function register_controls() {
        $this->start_controls_section('content_section', [
            'label' => __('Content', 'plugin-name'),
        ]);
    
        $repeater = new Repeater();
    
        $repeater->add_control('text_item', [
            'label' => __('Text Item', 'plugin-name'),
            'type' => Controls_Manager::TEXT,
            'default' => 'Sample marquee item',
            'label_block' => true,
        ]);
    
        $this->add_control('marquee_items', [
            'label' => __('Marquee Text Items', 'plugin-name'),
            'type' => Controls_Manager::REPEATER,
            'fields' => $repeater->get_controls(),
            'default' => [
                ['text_item' => 'First marquee text'],
                ['text_item' => 'Second marquee text'],
                ['text_item' => 'Third marquee text'],
            ],
            'title_field' => '{{{ text_item }}}',
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

        /**
         * Style: Text
         */
        $this->start_controls_section('style_text_section', [
            'label' => __('Text', 'plugin-name'),
            'tab' => \Elementor\Controls_Manager::TAB_STYLE,
        ]);
        
        $this->add_control('text_color', [
            'label' => __('Text Color', 'plugin-name'),
            'type' => \Elementor\Controls_Manager::COLOR,
            'selectors' => [
            '{{WRAPPER}} .marquee-item' => 'color: {{VALUE}};',
            ],
        ]);
        
        $this->add_group_control(\Elementor\Group_Control_Typography::get_type(), [
            'name' => 'typography',
            'label' => __('Typography', 'plugin-name'),
            'selector' => '{{WRAPPER}} .marquee-item',
        ]);
        
        $this->add_responsive_control('item_spacing', [
            'label' => __('Spacing Between Items', 'plugin-name'),
            'type' => \Elementor\Controls_Manager::SLIDER,
            'range' => [
            'px' => ['min' => 0, 'max' => 200],
            ],
            'selectors' => [
            '{{WRAPPER}} .marquee-item' => 'margin-right: {{SIZE}}{{UNIT}};',
            ],
            'default' => [
            'size' => 20,
            'unit' => 'px',
            ],
        ]);

        $this->add_control('separator', [
            'label' => __('Separator', 'plugin-name'),
            'type' => \Elementor\Controls_Manager::TEXT,
            'default' => ' ✦ ',
            'placeholder' => 'e.g. | or ✦ or ✨',
        ]);

        $this->add_control('separator_color', [
            'label' => __('Separator Color', 'plugin-name'),
            'type' => \Elementor\Controls_Manager::COLOR,
            'selectors' => [
              '{{WRAPPER}} .marquee-separator' => 'color: {{VALUE}};',
            ],
        ]);
        $this->add_responsive_control('seperator_spacing', [
            'label' => __('Seperator spacing', 'plugin-name'),
            'type' => \Elementor\Controls_Manager::SLIDER,
            'range' => ['px' => ['min' => 0, 'max' => 50]],
            'selectors' => [
                '{{WRAPPER}} .marquee-separator' => 'margin-left: {{SIZE}}{{UNIT}};',
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
        
        
        $this->add_group_control(\Elementor\Group_Control_Background::get_type(), [
            'name' => 'background',
            'label' => __('Background', 'plugin-name'),
            'types' => ['classic', 'gradient'],
            'selector' => '{{WRAPPER}} .tophive-marquee-wrapper',
        ]);
        
        $this->add_responsive_control('wrapper_height', [
            'label' => __('Height', 'plugin-name'),
            'type' => \Elementor\Controls_Manager::SLIDER,
            'range' => ['px' => ['min' => 10, 'max' => 500]],
            'selectors' => [
            '{{WRAPPER}} .tophive-marquee-wrapper' => 'height: {{SIZE}}{{UNIT}};',
            ],
        ]);
        
        $this->add_group_control(\Elementor\Group_Control_Border::get_type(), [
            'name' => 'wrapper_border',
            'label' => __('Border', 'plugin-name'),
            'selector' => '{{WRAPPER}} .tophive-marquee-wrapper',
        ]);
        
        $this->end_controls_section();
          
    }
    

    protected function render() {
        $settings = $this->get_settings_for_display();
        $texts = $settings['marquee_items'];
        $classes = ['tophive-marquee-wrapper'];
        
        if($settings['enable_mask'] == 'yes'){
            $classes[] = 'mask-enabled';
        }
        ?>

        <div class="tophive-marquee-scroll"></div>
            <div class="<?php echo implode(' ', $classes); ?>"
                <?= Tophive_Elementor_UI_Helper::tophive_data_attrs_from_settings($settings); ?>>
                <div class="tophive-marquee-track">
                    <div class="marquee-text">
                        <?php
                            $total = count($texts);
                            foreach ($texts as $index => $item):
                        ?>
                            <span class="marquee-item"><?= esc_html($item['text_item']) ?>
                                <?php if ($index < $total - 1): ?>
                                    <span class="marquee-separator"><?= esc_html($settings['separator']) ?></span>
                                <?php endif; ?>
                            </span>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        <div class="scroll"></div>
        <?php
    }
}
