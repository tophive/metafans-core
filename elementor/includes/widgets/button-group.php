<?php
/**
 * Elementor Widget: Theme Button Group (with Repeater)
 * Version: 3.0
 * Namespace: My_Core_Plugin\Widgets
 * Category: th-general
 */

namespace My_Core_Plugin\Widgets;

if ( ! defined( 'ABSPATH' ) ) { exit; }

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Icons_Manager;

class Button_Group extends Widget_Base {

    public function get_name() { return 'button_group'; }
    public function get_title() { return __( 'Button Group', 'my-core-plugin' ); }
    public function get_icon() { return 'eicon-button'; }
    public function get_categories() { return [ 'th-general' ]; }

    private function is_valid_icon( $icon ): bool {
        if ( is_array( $icon ) && isset( $icon['value'] ) ) {
            $val = $icon['value'];
            if ( is_string( $val ) && $val !== '' ) return true;
            if ( is_array( $val ) && ! empty( $val ) ) return true;
        } elseif ( is_string( $icon ) && $icon !== '' ) {
            return true;
        }
        return false;
    }

    private function render_icon_safe( $icon ): string {
        if ( ! $this->is_valid_icon( $icon ) ) { return ''; }
        if ( is_string( $icon ) ) {
            return '<i class="' . esc_attr( $icon ) . '" aria-hidden="true"></i>';
        }
        ob_start();
        try { Icons_Manager::render_icon( $icon, [ 'aria-hidden' => 'true' ] ); } catch ( \Throwable $e ) {}
        $html = trim( ob_get_clean() );
        if ( $html === '' || $html === '1' ) {
            $val = $icon['value'] ?? '';
            if ( is_string( $val ) && $val !== '' ) {
                return '<i class="' . esc_attr( $val ) . '" aria-hidden="true"></i>';
            }
        }
        return $html;
    }

    protected function register_controls() {
        // === Repeater Content ===
        $this->start_controls_section( 'section_buttons', [ 'label' => __( 'Buttons', 'my-core-plugin' ) ] );
        $rep = new Repeater();
        $rep->add_control( 'text', [ 'label' => __( 'Text','my-core-plugin' ), 'type' => Controls_Manager::TEXT, 'default' => __( 'Click Me','my-core-plugin' ) ] );
        $rep->add_control( 'link', [ 'label' => __( 'Link','my-core-plugin' ), 'type' => Controls_Manager::URL, 'default' => [ 'url'=>'#' ] ] );
        $rep->add_control( 'style_variant', [ 'label'=>__('Style','my-core-plugin'),'type'=>Controls_Manager::SELECT,'options'=>['solid'=>'Solid','outline'=>'Outline','glass'=>'Glass'],'default'=>'solid' ] );
        $rep->add_control( 'icon', [ 'label'=>__('Icon','my-core-plugin'),'type'=>Controls_Manager::ICONS,'default'=>['value'=>'','library'=>'fa-solid'] ] );
        $rep->add_control( 'icon_position', [ 'label'=>__('Icon Position','my-core-plugin'),'type'=>Controls_Manager::SELECT,'options'=>['before'=>'Before','after'=>'After'],'default'=>'before','condition'=>['icon[value]!' => ''] ] );

        // Add Text Visibility control
        $rep->add_control( 'show_text', [
            'label' => __( 'Show Text', 'my-core-plugin' ),
            'type' => Controls_Manager::SWITCHER,
            'label_on' => __( 'Show', 'my-core-plugin' ),
            'label_off' => __( 'Hide', 'my-core-plugin' ),
            'return_value' => 'yes',
            'default' => 'yes',
        ]);

        // Add Icon Visibility control
        $rep->add_control( 'show_icon', [
            'label' => __( 'Show Icon', 'my-core-plugin' ),
            'type' => Controls_Manager::SWITCHER,
            'label_on' => __( 'Show', 'my-core-plugin' ),
            'label_off' => __( 'Hide', 'my-core-plugin' ),
            'return_value' => 'yes',
            'default' => 'yes',
        ]);

        $this->add_control( 'buttons', [ 'label'=>__('Button Items','my-core-plugin'),'type'=>Controls_Manager::REPEATER,'fields'=>$rep->get_controls(),'default'=>[['text'=>'Button 1'],['text'=>'Button 2']],'title_field'=>'{{{ text }}}' ] );
        $this->end_controls_section();

        // === Group Layout ===
        $this->start_controls_section( 'section_group_style', [ 'label'=>__('Group Layout','my-core-plugin'),'tab'=>Controls_Manager::TAB_STYLE ] );
        $this->add_responsive_control( 'direction', [ 'label'=>__('Direction','my-core-plugin'),'type'=>Controls_Manager::CHOOSE,'options'=>['row'=>['title'=>'Row','icon'=>'eicon-h-align-left'],'column'=>['title'=>'Column','icon'=>'eicon-v-align-top']], 'default'=>'row','selectors'=>['{{WRAPPER}} .th-btn-wrap'=>'flex-direction: {{VALUE}};'] ] );
        $this->add_responsive_control( 'align', [ 'label'=>__('Alignment','my-core-plugin'),'type'=>Controls_Manager::CHOOSE,'options'=>['flex-start'=>['title'=>'Left','icon'=>'eicon-h-align-left'],'center'=>['title'=>'Center','icon'=>'eicon-h-align-center'],'flex-end'=>['title'=>'Right','icon'=>'eicon-h-align-right'],'space-between'=>['title'=>'Between','icon'=>'eicon-justify-space-between-h']], 'default'=>'flex-start','selectors'=>['{{WRAPPER}} .th-btn-wrap'=>'justify-content: {{VALUE}};'] ] );
        $this->add_control( 'wrap', [ 'label'=>__('Wrap','my-core-plugin'),'type'=>Controls_Manager::SWITCHER,'default'=>'yes','selectors'=>['{{WRAPPER}} .th-btn-wrap'=>'flex-wrap: {{VALUE:yes|wrap}}{{VALUE:no|nowrap}};'] ] );
        $this->add_responsive_control( 'gap', [ 'label'=>__('Gap','my-core-plugin'),'type'=>Controls_Manager::SLIDER,'range'=>['px'=>['min'=>0,'max'=>100]],'default'=>['size'=>12,'unit'=>'px'],'selectors'=>['{{WRAPPER}} .th-btn-wrap'=>'gap: {{SIZE}}{{UNIT}};'] ] );
        
        // === Background Type Control ===
        $this->add_control(
            'group_background_type',
            [
                'label' => __( 'Background Type', 'my-core-plugin' ),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'solid' => __( 'Solid', 'my-core-plugin' ),
                    'gradient' => __( 'Gradient', 'my-core-plugin' ),
                    'glass' => __( 'Glass Effect', 'my-core-plugin' ),
                ],
                'default' => 'solid',
                'selectors' => [
                    '{{WRAPPER}} .th-btn-wrap' => 'background: {{VALUE}};',
                ],
            ]
        );

        // Solid Background Color Control
        $this->add_control(
            'group_background_color',
            [
                'label' => __( 'Background Color', 'my-core-plugin' ),
                'type' => Controls_Manager::COLOR,
                'condition' => [
                    'group_background_type' => 'solid',
                ],
                'selectors' => [
                    '{{WRAPPER}} .th-btn-wrap' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        // Gradient Background Control
        $this->add_control(
            'group_gradient_start',
            [
                'label' => __( 'Start Color', 'my-core-plugin' ),
                'type' => Controls_Manager::COLOR,
                'condition' => [
                    'group_background_type' => 'gradient',
                ],
                'selectors' => [
                    '{{WRAPPER}} .th-btn-wrap' => 'background: linear-gradient(to right, {{VALUE}} {{group_gradient_end}});',
                ],
            ]
        );

        $this->add_control(
            'group_gradient_end',
            [
                'label' => __( 'End Color', 'my-core-plugin' ),
                'type' => Controls_Manager::COLOR,
                'condition' => [
                    'group_background_type' => 'gradient',
                ],
                'selectors' => [
                    '{{WRAPPER}} .th-btn-wrap' => 'background: linear-gradient(to right, {{group_gradient_start}} {{VALUE}});',
                ],
            ]
        );

        // Glass Effect Background Control
        $this->add_control(
            'group_glass_background',
            [
                'label' => __( 'Glass Effect', 'my-core-plugin' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __( 'Enable', 'my-core-plugin' ),
                'label_off' => __( 'Disable', 'my-core-plugin' ),
                'default' => 'yes',
                'condition' => [
                    'group_background_type' => 'glass',
                ],
                'selectors' => [
                    '{{WRAPPER}} .th-btn-wrap' => 'background: rgba(255, 255, 255, 0.2); backdrop-filter: blur(8px);',
                ],
            ]
        );

        // Border Control Group
        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'group_border',
                'label' => __( 'Border', 'my-core-plugin' ),
                'selector' => '{{WRAPPER}} .th-btn-wrap',
            ]
        );

        // Border Radius Control
        $this->add_responsive_control(
            'group_border_radius',
            [
                'label' => __( 'Border Radius', 'my-core-plugin' ),
                'type' => Controls_Manager::DIMENSIONS,
                'selectors' => [
                    '{{WRAPPER}} .th-btn-wrap' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        // Padding Control
        $this->add_responsive_control(
            'group_padding',
            [
                'label' => __( 'Padding', 'my-core-plugin' ),
                'type' => Controls_Manager::DIMENSIONS,
                'selectors' => [
                    '{{WRAPPER}} .th-btn-wrap' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        // Box Shadow Control
        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'group_box_shadow',
                'label' => __( 'Box Shadow', 'my-core-plugin' ),
                'selector' => '{{WRAPPER}} .th-btn-wrap',
            ]
        );

        $this->end_controls_section();

        // === Button Style ===
        $this->start_controls_section( 'section_btn_style', [ 'label'=>__('Button Style','my-core-plugin'),'tab'=>Controls_Manager::TAB_STYLE ] );
        $this->add_group_control( Group_Control_Typography::get_type(), [ 'name'=>'typography','selector'=>'{{WRAPPER}} .elementor-button' ] );
        $this->add_responsive_control( 'padding', [ 'label'=>__('Padding','my-core-plugin'),'type'=>Controls_Manager::DIMENSIONS,'selectors'=>['{{WRAPPER}} .elementor-button'=>'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'] ] );
        $this->add_group_control( Group_Control_Border::get_type(), [ 'name'=>'border','selector'=>'{{WRAPPER}} .elementor-button' ] );
        $this->add_responsive_control( 'border_radius', [ 'label'=>__('Border Radius','my-core-plugin'),'type'=>Controls_Manager::DIMENSIONS,'selectors'=>['{{WRAPPER}} .elementor-button'=>'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'],'default'=>['top'=>8,'right'=>8,'bottom'=>8,'left'=>8,'unit'=>'px','isLinked'=>true] ] );
        $this->add_group_control( Group_Control_Box_Shadow::get_type(), [ 'name'=>'shadow','selector'=>'{{WRAPPER}} .elementor-button' ] );
        $this->add_responsive_control( 'icon_size', [ 'label'=>__('Icon Size','my-core-plugin'),'type'=>Controls_Manager::SLIDER,'range'=>['px'=>['min'=>8,'max'=>96]],'default'=>['size'=>18],'selectors'=>['{{WRAPPER}} .elementor-button-icon i'=>'font-size: {{SIZE}}{{UNIT}};','{{WRAPPER}} .elementor-button-icon svg'=>'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};'] ] );
        $this->add_responsive_control( 'icon_spacing', [ 'label'=>__('Icon Spacing','my-core-plugin'),'type'=>Controls_Manager::SLIDER,'range'=>['px'=>['min'=>0,'max'=>40]],'default'=>['size'=>8],'selectors'=>['{{WRAPPER}} .elementor-align-icon-left'=>'margin-right: {{SIZE}}{{UNIT}};','{{WRAPPER}} .elementor-align-icon-right'=>'margin-left: {{SIZE}}{{UNIT}};'] ] );
        // Colors (Normal / Hover)
        $this->start_controls_tabs( 'tabs_colors' );
        $this->start_controls_tab( 'tab_normal', [ 'label'=>'Normal' ] );
        $this->add_control( 'text_color', [ 'label'=>'Text','type'=>Controls_Manager::COLOR,'selectors'=>['{{WRAPPER}} .elementor-button'=>'color: {{VALUE}};'],'default'=>'#fff' ] );
        $this->add_control( 'bg_color', [ 'label'=>'Background','type'=>Controls_Manager::COLOR,'selectors'=>['{{WRAPPER}} .elementor-button.solid'=>'background-color: {{VALUE}};'],'default'=>'#9d79ff' ] );
        $this->end_controls_tab();
        $this->start_controls_tab( 'tab_hover', [ 'label'=>'Hover' ] );
        $this->add_control( 'h_text_color', [ 'label'=>'Text','type'=>Controls_Manager::COLOR,'selectors'=>['{{WRAPPER}} .elementor-button:hover'=>'color: {{VALUE}};'] ] );
        $this->add_control( 'h_bg_color', [ 'label'=>'Background','type'=>Controls_Manager::COLOR,'selectors'=>['{{WRAPPER}} .elementor-button.solid:hover'=>'background-color: {{VALUE}};'] ] );
        $this->end_controls_tab();
        $this->end_controls_tabs();
        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        $items = $settings['buttons'] ?? [];
        if ( ! is_array( $items ) || empty( $items ) ) { return; }

        ?>
        <div class="th-btn-wrap">
            <?php foreach ( $items as $i => $btn ) :
                if ( ! is_array( $btn ) ) continue;
                $text      = esc_html( $btn['text'] ?? '' );
                $variant   = in_array( $btn['style_variant'] ?? '', ['solid','outline','glass'], true ) ? $btn['style_variant'] : 'solid';
                $icon_html = $this->render_icon_safe( $btn['icon'] ?? null );
                $pos       = ($btn['icon_position'] ?? 'after') === 'after' ? 'after' : 'before';
                $pos_class = ($pos === 'after') ? 'elementor-align-icon-right' : 'elementor-align-icon-left';
                $key = 'btn_link_' . $i;
                $link = $btn['link'] ?? [];
                $show_text = isset( $btn['show_text'] ) && $btn['show_text'] === 'yes';
                $show_icon = isset( $btn['show_icon'] ) && $btn['show_icon'] === 'yes';
                $icon_spacing = isset( $settings['icon_spacing']['size'] ) ? $settings['icon_spacing']['size'] : 8;

                // If text is hidden, set icon spacing to 0
                if ( ! $show_text ) {
                    $icon_spacing = 0;  // Set icon spacing to 0 when text is hidden
                }

                if ( is_array( $link ) && isset( $link['url'] ) ) {
                    $this->add_link_attributes( $key, $link );
                } else {
                    $this->add_render_attribute( $key, 'href', '#' );
                }
                ?>
                <a <?php echo $this->get_render_attribute_string( $key ); ?>
                    class="elementor-button elementor-button-link <?php echo esc_attr( $variant ); ?>"
                    aria-label="<?php echo esc_attr( $text ); ?>">
                    <span class="elementor-button-content-wrapper">
                        <?php if ( $show_icon && $icon_html && $pos === 'before' ) : ?>
                            <span class="elementor-button-icon <?php echo esc_attr( $pos_class ); ?>" style="margin-right: <?php echo $show_text ? esc_attr($icon_spacing) . 'px' : '0'; ?>;"><?php echo $icon_html; ?></span>
                        <?php endif; ?>
                        <?php if ( $show_text ) : ?>
                            <span class="elementor-button-text"><?php echo $text; ?></span>
                        <?php endif; ?>
                        <?php if ( $show_icon && $icon_html && $pos === 'after' ) : ?>
                            <span class="elementor-button-icon <?php echo esc_attr( $pos_class ); ?>" style="margin-left: <?php echo $show_text ? esc_attr($icon_spacing) . 'px' : '0'; ?>;"><?php echo $icon_html; ?></span>
                        <?php endif; ?>
                    </span>
                    <?php if ( $variant === 'glass' ) : ?>
                        <span class="btn-glass-sheen" aria-hidden="true"></span>
                    <?php endif; ?>
                </a>
            <?php endforeach; ?>
        </div>

        <style>
            :root { --th-primary:#9d79ff; }
            .th-btn-wrap { display:flex; width:100%; align-items:center; flex-wrap:wrap; gap:12px; }
            .elementor-button {
                display:inline-flex; align-items:center; justify-content:center;
                text-decoration:none; font-weight:600; border-radius:8px;
                position:relative; overflow:hidden;
            }
            .elementor-button .elementor-button-content-wrapper { display:inline-flex; gap:0; align-items:center; }
            .elementor-button .elementor-button-icon i { line-height:1; }
            .elementor-button .elementor-align-icon-left  { margin-right:8px; }
            .elementor-button .elementor-align-icon-right { margin-left:8px; }
            /* Variants */
            .elementor-button.solid  { background-color: var(--th-primary); color:#fff; }
            .elementor-button.outline{ background:transparent; border:2px solid var(--th-primary); color:var(--th-primary); }
            .elementor-button.glass  {
                color:#fff; border:1px solid rgba(255,255,255,.18);
                background-color: rgba(255,255,255,.08);
                backdrop-filter: blur(8px);
            }
            .elementor-button.glass .btn-glass-sheen {
                position:absolute; inset:0; pointer-events:none; border-radius:inherit;
                background:linear-gradient(120deg, rgba(255,255,255,.25), rgba(255,255,255,0));
                transform:translateX(-100%); transition:transform .6s ease;
            }
            .elementor-button.glass:hover .btn-glass-sheen { transform:translateX(100%); }
        </style>
        <?php
    }
}

\Elementor\Plugin::instance()->widgets_manager->register( new \My_Core_Plugin\Widgets\Button_Group() );
