<?php

class Tophive_Core_Footer extends Tophive_Core_Builder{
    public function __construct()
    {   
        add_filter( 'tophive/filters/footer/content', [ $this, 'tophive_elementor_footer_init' ], 10);
    }
    public function tophive_elementor_footer_init( $content ){
        // if( defined( 'ELEMENTOR_VERSION' ) ){
        //     $elementor_preview_active = Elementor\Plugin::$instance->preview->is_preview_mode();;
        //     if( $elementor_preview_active ){
        //         return '';
        //     }
        //     else{
        //         return $content;
        //     }
        // }
        if( 'tophive-header' == get_post_type() ){
            return '';
        }

        $id = $this->get_component_id('tophive-footer');
        if( $id ){
            return $this->render_elementor_component( $id );
        }else{
            return $content;
        }
    }
}

new Tophive_Core_Footer();