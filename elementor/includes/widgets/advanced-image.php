<?php
/**
 * Plugin Name: Advanced Image Pro Widget
 * Description: Advanced image hover widget with 2D/3D layered stacked hover effect, auto-responsive scaling, border-radius support, click-to-stack animation (GSAP), and full Elementor controls.
 * Version: 1.0
 * Author: Tophive
 */
/**
 * Advanced Image Widget for Elementor
 *
 * @package Tophive
 */
namespace My_Core_Plugin\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Image_Size;
use Elementor\Utils;

if (!defined('ABSPATH')) exit;

/**
 * Advanced Image Widget Class
 * @since 1.0.0
 */
class Advanced_Image_Widget extends Widget_Base {

    public function get_name() {
        return 'Advanced_Image_Widget';
    }

    public function get_title() {
        return __('Advanced Image', 'th-core');
    }

    public function get_icon() {
        return 'eicon-photo-library';
    }

    public function get_categories() {
        return ['th-general'];
    }

    public function get_script_depends() {
        return ['tophive-elementor-bundle'];
    }

    public function get_style_depends() {
        return ['tophive-elements-css'];
    }

    protected function register_controls() {

        $this->start_controls_section(
            'section_content',
            ['label' => __('Content', 'th-core')]
        );

        $this->add_control(
            'image',
            [
                'label' => __('Select Image', 'th-core'),
                'type' => Controls_Manager::MEDIA,
                'default' => ['url' => Utils::get_placeholder_image_src()],
            ]
        );

        $this->add_control(
            'num_layers',
            [
                'label' => __('Number of Layers', 'th-core'),
                'type' => Controls_Manager::SLIDER,
                'range' => ['px' => ['min' => 3, 'max' => 20, 'step' => 1]],
                'default' => ['size' => 10],
            ]
        );

        $this->add_control(
            'shape_style',
            [
                'label' => __('Shape', 'th-core'),
                'type' => Controls_Manager::SELECT,
                'default' => 'rectangle',
                'options' => [
                    'rectangle' => __('Rectangle', 'th-core'),
                    'circle' => __('Circle', 'th-core'),
                    'diamond' => __('Diamond', 'th-core'),
                    'hexagon' => __('Hexagon', 'th-core'),
                ],
            ]
        );

        $this->add_control(
            'enable_3d',
            [
                'label' => __('Enable 3D Mode', 'th-core'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'th-core'),
                'label_off' => __('No', 'th-core'),
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'hover_depth',
            [
                'label' => __('Hover Depth Intensity', 'th-core'),
                'type' => Controls_Manager::SLIDER,
                'range' => ['px' => ['min' => 0.5, 'max' => 3, 'step' => 0.1]],
                'default' => ['size' => 1.2],
            ]
        );

        $this->add_control(
            'anim_duration',
            [
                'label' => __('Animation Duration (ms)', 'th-core'),
                'type' => Controls_Manager::NUMBER,
                'default' => 400,
                'min' => 100,
                'max' => 2000,
                'step' => 50,
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_style',
            [
                'label' => __('Style', 'th-core'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            Group_Control_Image_Size::get_type(),
            ['name' => 'image_size', 'default' => 'large']
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            ['name' => 'border', 'selector' => '{{WRAPPER}} .ad-image-layer']
        );

        $this->add_responsive_control(
            'image_width',
            [
                'label' => __('Image Width', 'th-core'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px', '%'],
                'range' => [
                    'px' => ['min' => 100, 'max' => 1000],
                    '%'  => ['min' => 10, 'max' => 100],
                ],
                'default' => ['size' => 100, 'unit' => '%'],
                'selectors' => [
                    '{{WRAPPER}} .image-pro-widget' => 'display: inline-block; width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control('border_radius', [
            'label' => __('Border Radius', 'th-core'),
            'type' => Controls_Manager::SLIDER,
            'range' => ['px' => ['min' => 0, 'max' => 100, 'step' => 1]],
            'default' => ['size' => 0],
            'selectors' => [
                '{{WRAPPER}} .ad-image-container, {{WRAPPER}} .ad-image-layer' => 'border-radius: {{SIZE}}px;',
            ],
        ]);

        $this->add_responsive_control(
            'aspect_ratio',
            [
                'label' => __('Aspect Ratio', 'th-core'),
                'type' => Controls_Manager::TEXT,
                'default' => '1/1',
                'description' => __('Enter a custom aspect ratio. E.g., 16/9, 4/3, 1/1.', 'th-core'),
                'selectors' => [
                    '{{WRAPPER}} .ad-image-container' => 'aspect-ratio: {{VALUE}};',
                ],
                'separator' => 'before',
            ]
        );

        $this->add_responsive_control(
            'background_size',
            [
                'label' => __('Image Size', 'th-core'),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'cover' => __('Cover', 'th-core'),
                    'contain' => __('Contain', 'th-core'),
                    'auto' => __('Auto', 'th-core'),
                ],
                'default' => 'cover',
                'selectors' => [
                    '{{WRAPPER}} .ad-image-layer' => 'background-size: {{VALUE}};',
                ],
                'description' => __('"Cover" may crop the image. "Contain" ensures the full image is visible.', 'th-core'),
                'separator' => 'after',
            ]
        );

        $this->add_responsive_control(
            'background_position',
            [
                'label' => __('Image Position', 'th-core'),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'center' => __('Center', 'th-core'),
                    'top' => __('Top', 'th-core'),
                    'bottom' => __('Bottom', 'th-core'),
                    'left' => __('Left', 'th-core'),
                    'right' => __('Right', 'th-core'),
                ],
                'default' => 'center',
                'selectors' => [
                    '{{WRAPPER}} .ad-image-layer' => 'background-position: {{VALUE}};',
                ],
            ]
        );


        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();

        if ( empty( $settings['image']['url'] ) ) {
            return;
        }

        if ( ! empty( $settings['image']['id'] ) ) {
            $image_url = Group_Control_Image_Size::get_attachment_image_src($settings['image']['id'], 'image_size', $settings);
        } else {
            $image_url = $settings['image']['url'];
        }

        $layers = absint($settings['num_layers']['size'] ?? 10);
        $shape = esc_attr($settings['shape_style']);
        $is_3d = $settings['enable_3d'] === 'yes' ? 'is-3d' : '';
        $depth = floatval($settings['hover_depth']['size'] ?? 1.2);
        $duration = absint($settings['anim_duration'] ?? 400);
        $widget_id = esc_attr($this->get_id());
        ?>

        <div class="image-pro-widget" id="image-pro-<?php echo $widget_id; ?>">
            <div class="ad-image-container <?php echo $is_3d; ?>"
                data-depth="<?php echo esc_attr($depth); ?>"
                data-duration="<?php echo esc_attr($duration); ?>">
                <?php for ($i = 0; $i < $layers; $i++): ?>
                    <div class="ad-image-layer <?php echo esc_attr($shape); ?>" style="background-image:url('<?php echo $image_url; ?>');"></div>
                <?php endfor; ?>
            </div>
        </div>
        <?php
    }
}