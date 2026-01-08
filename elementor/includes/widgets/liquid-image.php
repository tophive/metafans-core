<?php
/**
 * Plugin Name: Elementor Liquid Image Widget
 * Description: Fully functional Elementor widget with 8 interactive premium effects, full style controls, and effect presets (clean UI: Preset only).
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
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Css_Filter;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class Liquid_Image_Widget
 * @since 1.0.0
 */
class Liquid_Image_Widget extends Widget_Base {

    public function get_name() {
        return 'liquid_image';
    }

    public function get_title() {
        return __('Liquid Image', 'my-core-plugin');
    }

    public function get_icon() {
        return 'eicon-image';
    }

    public function get_categories() {
        return ['th-general'];
    }
    
    public function get_script_depends() {
        return ['tophive-elementor-bundle'];
    }

    public function get_style_depends() {
        // The global elements.css contains styles for both.
        return ['tophive-elements-css'];
    }
    protected function register_controls() {

        // Content Tab
        $this->start_controls_section(
            'section_content',
            ['label' => __('Content', 'my-core-plugin')]
        );

        $this->add_control(
            'image',
            [
                'label' => __('Choose Image', 'my-core-plugin'),
                'type'  => Controls_Manager::MEDIA,
                'default' => [
                    'url' => \Elementor\Utils::get_placeholder_image_src(),
                ],
            ]
        );

        // Presets Effect Type
        $this->add_control(
            'effect_preset',
            [
                'label' => __('Effect Preset', 'my-core-plugin'),
                'type' => Controls_Manager::SELECT,
                'default' => 'default',
                'options' => [
                    'default' => __('Liquid', 'my-core-plugin'),
                    'liquid_hover' => __('Liquid Morph', 'my-core-plugin'),
                    'ripple_wave'  => __('Ripple Wave', 'my-core-plugin'),
                    'glitch_rgb'   => __('Glitch RGB', 'my-core-plugin'),
                    'parallax_tilt'=> __('3D Parallax', 'my-core-plugin'),
                    'hue_shift'    => __('Hue Shift', 'my-core-plugin'),
                    'blur_focus'   => __('Blur Focus', 'my-core-plugin'),
                    'scroll_disp'  => __('Scroll Displacement', 'my-core-plugin'),
                ],
            ]
        );

        $this->add_control(
            'displacement_intensity',
            [
                'label' => __('Displacement Intensity', 'my-core-plugin'),
                'type' => Controls_Manager::SLIDER,
                'range' => ['px' => ['min' => 0.0, 'max' => 0.5, 'step' => 0.01]],
                'default' => ['size' => 0.03],
                'description' => 'Controls the strength of the distortion effect. Higher values are more intense.',
                'condition' => [
                    'effect_preset' => ['default', 'liquid_hover', 'ripple_wave', 'blur_focus', 'scroll_disp'],
                ],
            ]
        );

        $this->add_control(
            'wave_speed',
            [
                'label' => __('Wave Speed', 'my-core-plugin'),
                'type' => Controls_Manager::SLIDER,
                'range' => ['px' => ['min' => 0.1, 'max' => 10, 'step' => 0.1]],
                'default' => ['size' => 5.0],
                'condition' => [
                    'effect_preset' => ['liquid_hover', 'ripple_wave', 'default'],
                ],
            ]
        );

        $this->add_control(
            'glitch_speed',
            [
                'label' => __('Glitch Speed', 'my-core-plugin'),
                'type' => Controls_Manager::SLIDER,
                'range' => ['px' => ['min' => 1, 'max' => 50, 'step' => 1]],
                'default' => ['size' => 10],
                'condition' => [
                    'effect_preset' => 'glitch_rgb',
                ],
            ]
        );

        $this->add_control(
            'glitch_intensity',
            [
                'label' => __('Glitch Intensity', 'my-core-plugin'),
                'type' => Controls_Manager::SLIDER,
                'range' => ['px' => ['min' => 0.0, 'max' => 0.1, 'step' => 0.005]],
                'default' => ['size' => 0.01],
                'condition' => [
                    'effect_preset' => 'glitch_rgb',
                ],
            ]
        );

        $this->add_control(
            'glitch_on_hover',
            [
                'label' => __('Glitch on Hover', 'my-core-plugin'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('On', 'my-core-plugin'),
                'label_off' => __('Off', 'my-core-plugin'),
                'return_value' => 'yes',
                'default' => 'no',
                'description' => 'If enabled, the glitch effect will only trigger on mouse hover.',
                'condition' => [
                    'effect_preset' => 'glitch_rgb',
                ],
            ]
        );

        $this->end_controls_section();

        // Style Tab – Controls
        $this->start_controls_section(
            'section_image_style',
            ['label' => __('Image Style', 'my-core-plugin'), 'tab' => Controls_Manager::TAB_STYLE]
        );

        // Width
        $this->add_responsive_control(
            'image_width',
            [
                'label' => __('Image Size', 'my-core-plugin'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px', '%'],
                'range' => [
                    'px' => ['min' => 50, 'max' => 2000],
                    '%'  => ['min' => 10, 'max' => 100],
                ],
                'default' => ['size' => 100, 'unit' => '%'],
                'selectors' => [
                    '{{WRAPPER}} .liquid-image-widget-wrap' => 'max-width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        // Border
        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'image_border',
                'label' => __('Border', 'my-core-plugin'),
                'selector' => '{{WRAPPER}} .liquid-image-widget-wrap',
            ]
        );

        // Border radius
        $this->add_responsive_control(
            'image_border_radius',
            [
                'label' => __('Border Radius', 'my-core-plugin'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .liquid-image-widget-wrap' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow: hidden;',
                ],
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        $image_url = esc_url($settings['image']['url']);
        $image_id = $settings['image']['id'] ?? null;
        $aspect_ratio_style = '';

        if ($image_id) {
            $image_meta = wp_get_attachment_metadata($image_id);
            if (!empty($image_meta['width']) && !empty($image_meta['height'])) {
                $aspect_ratio_style = ' --tophive-aspect-ratio: ' . $image_meta['width'] . '/' . $image_meta['height'] . ';';
            }
        }

        $preset = isset($settings['effect_preset']) ? $settings['effect_preset'] : 'default';
        $alt_text = get_post_meta( $settings['image']['id'], '_wp_attachment_image_alt', true );

        // Map presets to internal effect type and settings
        $effectType = 'mouse_displacement'; // Default effect type
        $displacementIntensity = $settings['displacement_intensity']['size'] ?? 0.03;
        $glitchSpeed = $settings['glitch_speed']['size'] ?? 10.0;        
        $glitchOnHover = $settings['glitch_on_hover'] === 'yes' ? 1.0 : 0.0;
        $glitchIntensity = $settings['glitch_intensity']['size'] ?? 0.01;
        $waveSpeed = $settings['wave_speed']['size'] ?? 5.0;
        $gridResolution = 128;

        switch ($preset) { // Presets primarily set the effect type
            case 'liquid_hover': $effectType = 'mouse_displacement'; $displacementIntensity = 0.05; $waveSpeed = 6; break;
            case 'ripple_wave': $effectType = 'ripple_hover'; $displacementIntensity = 0.03; $waveSpeed = 4; break;
            case 'glitch_rgb': $effectType = 'glitch_hover'; break;
            case 'parallax_tilt': $effectType = 'parallax_3d'; break;
            case 'hue_shift': $effectType = 'hue_shift'; break;
            case 'blur_focus': $effectType = 'blur_focus'; $displacementIntensity = 0.01; break;
            case 'scroll_disp': $effectType = 'scroll_displacement'; $displacementIntensity = 0.03; $waveSpeed = 3; break;
            default: $effectType = 'mouse_displacement'; break;
        }

        // Allow sliders to override preset values if they have been changed from the default
        if (isset($settings['displacement_intensity']['size']) && $settings['displacement_intensity']['size'] !== 0.03) { $displacementIntensity = $settings['displacement_intensity']['size']; }
        if (isset($settings['wave_speed']['size']) && $settings['wave_speed']['size'] !== 5.0) { $waveSpeed = $settings['wave_speed']['size']; }


        $data_settings = [
            'effectType' => $effectType,
            'displacementIntensity' => $displacementIntensity,
            'waveSpeed' => $waveSpeed,
            'glitchSpeed' => $glitchSpeed,
            'glitchOnHover' => $glitchOnHover,
            'glitchIntensity' => $glitchIntensity,
            'gridResolution' => $gridResolution,
        ];
        ?>
        <div class="liquid-image-widget-wrap"
             data-settings='<?php echo esc_attr(wp_json_encode($data_settings)); ?>'
             style="<?php echo esc_attr($aspect_ratio_style); ?>"
        >
            <img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr( $alt_text ); ?>">
        </div>
        <?php
    }
}
