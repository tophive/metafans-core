<?php
/**
 * Elementor Button Widget
 *
 * @package Tophive
 */
if (!defined('ABSPATH')) exit; // Exit if accessed directly
/**
 * Tophive Elementor Button Widget Class
 * @since 1.0.0
 */
class Tophive_Elementor_Button extends \Elementor\Widget_Base{
    public function get_name() {
        return 'tophive-button-widget';
    }

    public function get_title() {
        return TH_ELEMENTOR_DISPLAY_NAME_SC . esc_html__( 'Button', TH_ELEMENTOR_SLUG );
    }

    public function get_icon() {
        return 'eicon-button';
    }

    public function get_categories() {
        return ['th-general'];
    }

    public function get_script_depends(): array {
		return [ 'tophive-elementor-bundle' ];
	}
    protected function register_controls() {
        $this->start_controls_section('button_content', [
            'label' => __('Button', 'text-domain'),
            'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
        ]);

        $this->add_control(
			'tophive_title_control',
			[
				'label' => esc_html__( 'Button text', TH_ELEMENTOR_SLUG ),
				'type' => \Elementor\Controls_Manager::TEXTAREA,
				'default' => esc_html__( 'Button text', TH_ELEMENTOR_SLUG ),
				'placeholder' => esc_html__( 'Type something...', TH_ELEMENTOR_SLUG ),
			]
		);

        Tophive_Elementor_UI_Helper::add_ui_controls($this, 'title', '', false);

        Tophive_Elementor_UI_Helper::button_type_controls($this);

        $this->add_control(
			'button_link',
			[
				'label' => esc_html__( 'Content URL/Link', TH_ELEMENTOR_SLUG ),
				'type' => \Elementor\Controls_Manager::URL,
				'placeholder' => esc_html__( 'https://my-url.com', TH_ELEMENTOR_SLUG ),
				'show_external' => true,
				'default' => [
					'url' => '',
					'is_external' => true,
					'nofollow' => true,
				],
			]
		);

        $this->end_controls_section();

		\Tophive_Elementor_Style_Helper::add_ui_style_controls($this, 'button', '{{WRAPPER}} .tophive-custom-button');

		\Tophive_Elementor_Style_Helper::add_ui_style_controls($this, 'transform', '{{WRAPPER}}.tophive-custom-button', '', true, 'Transform');
        
		\Tophive_Elementor_Style_Helper::add_ui_style_controls($this, 'animate', '{{WRAPPER}}.tophive-custom-button', '', true, 'Animation');

    }
    protected function render() {
        $settings = $this->get_settings_for_display(); // Move this line to the top
    
        $has_link = !empty($settings['button_link']['url']);
        $link_target = $has_link && !empty($settings['button_link']['is_external']) ? ' target="_blank"' : '';
        $link_nofollow = $has_link && !empty($settings['button_link']['nofollow']) ? ' rel="nofollow"' : '';
        $link_attributes = $has_link ? ' href="' . esc_url($settings['button_link']['url']) . '"' . $link_target . $link_nofollow : '';
    
        $button_text = $settings['tophive_title_control'];
        $show_icon = $settings['show_title_icon'] === 'yes';
        
        $classes = ['tophive-custom-button'];
        $classes[] = $settings['tophive_button_type'];
    
        echo '<a class="' . implode(' ', $classes) . '" data-text="'. esc_attr($button_text) .'" '. $link_attributes .'>';
        
        if ($settings['tophive_button_type'] == 'moveup-end') {
            echo "<span>";
        }
        
        echo "<span class='button-text'>";
        echo esc_html($button_text);
        
        if ($show_icon) {
            echo "<span class='button-text-icon'>";
            \Elementor\Icons_Manager::render_icon($settings['tophive_title_icon'], ['aria-hidden' => 'true']);
            echo "</span>";
        }
    
        echo "</span>";
    
        if ($settings['tophive_button_type'] == 'moveup-end') {
            echo "</span>";
        }
        
        echo '</a>';
    }
    
}
// Register the widget
\Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Tophive_Elementor_Button());
