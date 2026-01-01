<?php
namespace TophiveElementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

/**
 * Elementor header image widget.
 *
 * Widget that displays a logo with various customization options.
 *
 * @since 1.0.0
 */
class TH_Header_Image extends Widget_Base {

    /**
     * Get widget name.
     *
     * Retrieve header image widget name.
     *
     * @since 1.0.0
     * @return string Widget name.
     */
    public function get_name() {
        return TH_ELEMENTOR_SLUG . '_header_image';
    }

    /**
     * Get widget title.
     *
     * Retrieve header image widget title.
     *
     * @since 1.0.0
     * @return string Widget title.
     */
    public function get_title() {
        return esc_html__('Logo', TH_ELEMENTOR_SLUG);
    }

    /**
     * Get widget icon.
     *
     * Retrieve widget icon.
     *
     * @since 1.0.0
     * @return string Widget icon.
     */
    public function get_icon() {
        return 'eicon-site-logo';
    }

    /**
     * Get widget categories.
     *
     * Retrieve the list of categories the widget belongs to.
     *
     * @since 1.0.0
     * @return array Widget categories.
     */
    public function get_categories() {
        return [ 'th-header' ];
    }

    /**
     * Get widget keywords.
     *
     * Retrieve the list of keywords the widget belongs to.
     *
     * @since 1.0.0
     * @return array Widget keywords.
     */
    public function get_keywords() {
        return ['header', 'logo', 'image'];
    }

    /**
     * Register header image widget controls.
     *
     * Adds input fields to allow users to customize the widget.
     *
     * @since 1.0.0
     */
    protected function register_controls() {
        $this->start_controls_section(
            'general_section',
            [
                'label' => esc_html__('Logo', TH_ELEMENTOR_SLUG),
            ]
        );

        $this->add_control(
            'logo_redirect_info',
            [
                'type' => Controls_Manager::RAW_HTML,
                'raw' => sprintf(__('Go to the <strong><u>Elementor Site Settings > Site Identity</u></strong> to add your logo.', TH_ELEMENTOR_SLUG)),
                'separator' => 'after',
                'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
            ]
        );

        $this->add_control(
            'uselogo',
            [
                'label' => esc_html__('Use logo from site settings?', TH_ELEMENTOR_SLUG),
                'description' => esc_html__('Use the logo set in the Elementor Site Settings panel', TH_ELEMENTOR_SLUG),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__('On', TH_ELEMENTOR_SLUG),
                'label_off' => esc_html__('Off', TH_ELEMENTOR_SLUG),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'image',
            [
                'label' => esc_html__('Choose Image', TH_ELEMENTOR_SLUG),
                'description' => esc_html__('Add image from gallery or upload new', TH_ELEMENTOR_SLUG),
                'type' => Controls_Manager::MEDIA,
                'condition' => [
                    'uselogo' => ''
                ]
            ]
        );

        $this->add_control(
            'linkhome',
            [
                'label' => esc_html__('Link to Homepage?', TH_ELEMENTOR_SLUG),
                'description' => esc_html__('Link the logo to the homepage', TH_ELEMENTOR_SLUG),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__('On', TH_ELEMENTOR_SLUG),
                'label_off' => esc_html__('Off', TH_ELEMENTOR_SLUG),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'link',
            [
                'label' => esc_html__('Link', TH_ELEMENTOR_SLUG),
                'type' => Controls_Manager::URL,
                'placeholder' => esc_html__('https://your-link.com', TH_ELEMENTOR_SLUG),
                'show_external' => true,
                'default' => [
                    'url' => '',
                    'is_external' => true,
                    'nofollow' => true,
                ],
                'condition' => [
                    'linkhome' => ''
                ]
            ]
        );

        $this->end_controls_section();
    }

    /**
     * Render widget output on the frontend.
     *
     * @since 1.0.0
     */
    protected function render() {
        $settings = $this->get_settings_for_display();

        if ($settings['uselogo'] === 'yes') {
            $logo_url = get_site_icon_url();
        } else {
            $logo_url = $settings['image']['url'];
        }

        $link_url = ($settings['linkhome'] === 'yes') ? home_url() : $settings['link']['url'];

        echo '<a href="' . esc_url($link_url) . '">';
        echo '<img src="' . esc_url($logo_url) . '" alt="' . esc_html__('Site Logo', TH_ELEMENTOR_SLUG) . '">';
        echo '</a>';
    }
}
\Elementor\Plugin::instance()->widgets_manager->register( new TH_Header_Image() );
