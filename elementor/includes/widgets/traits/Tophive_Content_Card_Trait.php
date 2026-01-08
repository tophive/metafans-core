<?php

use Elementor\Group_Control_Image_Size;
use Elementor\Element_Base;

trait Tophive_Content_Card_Trait {

    protected function render_content_card( $args = [] ) {
        $defaults = [
            'title'              => '',
            'description'        => '',
            'image_url'          => '',
            'rating'             => 0,
            'read_more_text'     => '',
            'read_more_link'     => '',
            'permalink'          => '',
            'user_name'          => '',
            'user_avatar'        => '',
            'video_url'          => '',
            'hover_description'  => '',
            'hover_cta_text'     => '',
            'hover_cta_link'     => '',
            'badge_text'         => '',
            'status'             => '',
            'cta_text'           => '',
            'cta_link'           => '',
            'price'              => '',
            'meta_author'        => '',
            'meta_date'          => '',
            'tags'               => [],
            'overlay_order'      => [],
            'show_image'         => true,
            'show_rating'        => false,
            'show_social'        => false,
            'title_tag'          => 'h3',
            'style'              => 'style-1',

            // New: layout_order
            'layout_order'       => [
                'image', 'badge', 'price', 'title', 'meta', 'tags',
                'description', 'rating', 'avatar', 'video', 'hover',
                'read_more', 'cta', 'social', 'status'
            ],
        ];

        $args = wp_parse_args( $args, $defaults );
        extract( $args );

        echo '<div class="tophive-content-card tophive-content-style-' . esc_attr( $style ) . '">';
        if ( $permalink && empty( $read_more_link ) ) {
            echo '<a href="' . esc_url( $permalink ) . '" class="tophive-card-link-wrapper">';
        }

        $layout_order = array_filter( $layout_order, function( $section ) {
            return $section !== 'image';
        } );
        echo '<div class="tophive-card-left">';

            //OVERLAY CONTENT
        
            if (!empty($settings['show_overlay_content']) && $settings['show_overlay_content'] === 'yes' && !empty($overlay_order)) {
                echo '<div class="tophive-card-overlay-content '. $settings['overlay_styles'] .'">';
                    echo '<div class="tophive-card-overlay-content-inner">';
                    foreach ($overlay_order as $section) {
                        echo '<div class="tophive-card-section tophive-section-' . esc_attr($section) . '">';
                        $this->render_card_section($section, $args, $settings, true);
                        echo '</div>';
                    }
                    echo '</div>';
                echo '</div>';
            }
            if ($show_image && $image_url) {
                echo '<div class="tophive-card-section tophive-section-image image-position-' . esc_attr($settings['icon_position'] ?? 'left') . '">
                        <div class="tophive-image d-block pos-rel">
                            <div class="tophive-image-container d-inline-flex pos-rel justify-content-center">
                                <figure class="w-100 pos-rel">
                                    <img src="' . esc_url($image_url) . '" alt="' . esc_attr($title) . '" />';
                                    echo $this->render_overlay_wrap();
                                echo '</figure>
                            </div>
                        </div>
                    </div>';
            }
        echo '</div>';

        echo '<div class="tophive-card-right">';

        foreach ( $layout_order as $section ) {
            echo '<div class="tophive-card-section tophive-section-' . esc_attr( $section ) . '">';
            $this->render_card_section($section, $args, $settings, false);
            echo '</div>';
        }
        

        if ( $permalink ) {
            echo '</a>';
        }

        echo '</div>';
        echo '</div>';
    }

    private function render_card_section($section, $args, $settings, $is_overlay = false) {
        extract($args);

        switch ( $section ) {

            case 'badge':
                if ( $badge_text ) {
                    echo '<div class="tophive-card-badge">' . esc_html( $badge_text ) . '</div>';
                }
                break;

            case 'price':
                if ( $price ) {
                    echo '<div class="tophive-card-price">' . esc_html( $price ) . '</div>';
                }
                break;

            case 'title':
                if ( $title ) {
                    $tag = esc_html($settings['title_tag']);
                    $title = esc_html($title); // Ensure it's escaped properly

                    $icon_html = '';
                    if ('yes' === $settings['show_title_icon']) {
                        ob_start();
                        \Elementor\Icons_Manager::render_icon($settings['tophive_title_icon'], ['aria-hidden' => 'true']);
                        $icon_html = ob_get_clean();
                    }

                    printf(
                        '<%1$s class="icon-box__title icon-title__heading"><span class="%4$s">%2$s</span>%3$s</%1$s>',
                        $tag,
                        $title,
                        $icon_html,
                        $this->get_text_hover_effect_class($settings, 'heading')
                    );
                }
                break;

            case 'meta':
                if ( $meta_author || $meta_date ) {
                    echo '<div class="tophive-card-meta">';
                    if ( $meta_author ) echo '<span class="tophive-card-author">' . esc_html( $meta_author ) . '</span>';
                    if ( $meta_date ) echo '<span class="tophive-card-date">' . esc_html( $meta_date ) . '</span>';
                    echo '</div>';
                }
                break;

            case 'tags':
                if ( $tags && is_array( $tags ) ) {
                    echo '<div class="tophive-card-tags">';
                    foreach ( $tags as $tag ) {
                        echo '<span class="tophive-tag">' . esc_html( $tag ) . '</span>';
                    }
                    echo '</div>';
                }
                break;

                case 'description':
                    if ( isset( $settings['show_content'] ) && 'yes' === $settings['show_content'] && ! empty( $description ) ) {
                        
                        $words_count = isset( $settings['words_count']['size'] ) ? intval( $settings['words_count']['size'] ) : 0;
                
                        $final_description = ( $words_count > 0 )
                            ? wp_trim_words( $description, $words_count, '...' )
                            : $description;
                
                        printf(
                            '<div class="tophive-card-description">%s</div>',
                            esc_html( $final_description )
                        );
                    }
                    break;
    
            case 'rating':
                if ( $show_rating && $rating ) {
                    echo '<div class="tophive-card-rating">';
                    for ( $i = 1; $i <= 5; $i++ ) {
                        echo '<span class="star' . ( $i <= $rating ? ' filled' : '' ) . '">&#9733;</span>';
                    }
                    echo '</div>';
                }
                break;

            case 'avatar':
                if ( $user_avatar || $user_name ) {
                    echo '<div class="tophive-card-avatar">';
                    if ( $user_avatar ) echo '<img src="' . esc_url( $user_avatar ) . '" alt="' . esc_attr( $user_name ) . '">';
                    if ( $user_name ) echo '<span class="tophive-card-username">' . esc_html( $user_name ) . '</span>';
                    echo '</div>';
                }
                break;

            case 'video':
                if ( $video_url ) {
                    echo '<div class="tophive-card-video"><a href="' . esc_url( $video_url ) . '" class="tophive-video-popup">Watch Video</a></div>';
                }
                break;

            case 'hover':
                if ( $hover_description || $hover_cta_text ) {
                    echo '<div class="tophive-card-hover-content">';
                    if ( $hover_description ) echo '<p>' . esc_html( $hover_description ) . '</p>';
                    if ( $hover_cta_link && $hover_cta_text ) {
                        echo '<a href="' . esc_url( $hover_cta_link ) . '">' . esc_html( $hover_cta_text ) . '</a>';
                    }
                    echo '</div>';
                }
                break;

            case 'read_more':
                if ( isset( $settings['show_read_more_link'] ) && 'yes' === $settings['show_read_more_link'] && ! empty( $read_more_link ) ) {
                    echo '<div class="tophive-card-readmore">';
                        echo '<a href="' . esc_url( $read_more_link ) . '" class="tophive-link">';
                            echo esc_html( $settings['read_more_text'] ?? __( 'Read More', 'plugin-name' ) );
                            if ( isset( $settings['add_more_link_icon'] ) && 'yes' === $settings['add_more_link_icon'] && ! empty( $settings['more_link_icon'] ) ) {
                                \Elementor\Icons_Manager::render_icon( $settings['more_link_icon'], [ 'aria-hidden' => 'true' ] );
                            }
                        echo '</a>';
                    echo '</div>';
                }
                break;
                
            case 'cta':
                if ( $cta_text && $cta_link ) {
                    echo '<div class="tophive-card-cta"><a href="' . esc_url( $cta_link ) . '" class="tophive-btn">' . esc_html( $cta_text ) . '</a></div>';
                }
                break;

            case 'social':
                if ( $show_social && $permalink ) {
                    echo '<div class="tophive-card-share">';
                    echo '<a href="https://facebook.com/sharer.php?u=' . urlencode( $permalink ) . '" target="_blank">Facebook</a>';
                    echo '<a href="https://twitter.com/share?url=' . urlencode( $permalink ) . '" target="_blank">Twitter</a>';
                    echo '</div>';
                }
                break;

            case 'status':
                if ( $status ) {
                    echo '<span class="tophive-card-status status-' . esc_attr( $status ) . '">' . ucfirst( esc_html( $status ) ) . '</span>';
                }
                break;
        }
    }
    

    protected function render_overlay_wrap() {
        $settings = $this->get_settings_for_display();
    
        if ( empty( $settings['tophive_overlay_enable_overlay_bg_global'] ) ) {
            return;
        }
    
        // Base classes
        $classes = [ 'tophive-overlay-bg' ];
    
        // Add on-hover class if enabled
        if ( $settings['tophive_overlay_overlay_show_on_hover_global'] == 'yes' ) {
            $classes[] = 'overlay-show-onhover';
        }
    
        $href  = '';
        $group = '';

        $html = '';
        // Wrap in anchor if href is available
        if ( $href ) {
            $this->add_render_attribute( 'overlay_link', 'href', $href );
            $this->add_render_attribute( 'overlay_link', 'class', 'tophive-overlay' );
    
            if ( $group ) {
                $this->add_render_attribute( 'overlay_link', 'data-fancybox', $group );
            }
    
            $html .= '<a ' . $this->get_render_attribute_string( 'overlay_link' ) . '>';
        }
    
        // Output the span
        $html .= '<span class="' . esc_attr( implode( ' ', $classes ) ) . '"></span>';
    
        if ( $href ) {
            $html .= '</a>';
        }

        return $html;
    }

    protected function get_wrapper_classes(): array {
        $settings = $this->get_settings_for_display();
        $classes = [ 'tophive-card-element' ];
    
        // if ( isset( $settings['hover_enabled'] ) && $settings['hover_enabled'] ) {
        //     $classes[] = 'hover-active';
        // }
    
        return $classes;
    }
    protected function init_wrapper_classes_hook(): void {
        add_action( 'elementor/frontend/before_render', [ $this, 'inject_wrapper_classes' ], 10, 1);
    }
    
    public function inject_wrapper_classes( Element_Base $element ) {
        $card_elements = ['tophive-posts-card'];
        if(in_array($element->get_name(), $card_elements)){
            $element->add_render_attribute( '_wrapper', 'class', $this->get_wrapper_classes() );
        }
    }

    public function get_text_hover_effect_class($settings, $id){
        $fid = $id . '_text_hover';

        if( $settings[$fid] == 'default' ) return '';

        return 'tophive-text-hover-effects ' . $settings[$fid];
    }
    protected function add_overlay_content_ui_control($repeater){
        $this->start_controls_section('overlay_content_section', [
            'label' => esc_html__('Overlay content', 'plugin-name'),
            'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
        ]);

        $this->add_control('show_overlay_content', [
            'label'        => esc_html__('Show Overlay Content', 'plugin-name'),
            'type'         => \Elementor\Controls_Manager::SWITCHER,
            'label_on'     => esc_html__('Yes', 'plugin-name'),
            'label_off'    => esc_html__('No', 'plugin-name'),
            'return_value' => 'yes',
            'default'      => '',
            'prefix_class' => 'show-overlay-content-',
        ]);
        
        $this->add_control('overlay_order', [
            'label' => esc_html__('Overlay Content Order', 'plugin-name'),
            'type' => \Elementor\Controls_Manager::REPEATER,
            'fields' => $repeater->get_controls(),
            'default' => [],
            'title_field' => '{{{ section_key }}}',
            'condition' => [
                'show_overlay_content' => 'yes',
            ],
        ]);
        $this->add_control('overlay_styles', [
            'label' => esc_html__('Overlay style', 'plugin-name'),
            'type' => \Elementor\Controls_Manager::SELECT,
            'default' => 'default',
            'options' => [
                'default' => 'Default',
                'overlay-style-1' => 'Style 1',
            ],
        ]);
        
        $this->end_controls_section();

    }
}
