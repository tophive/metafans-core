<?php

class Tophive_Core_Header extends Tophive_Core_Builder{
    public function __construct()
    {   
        add_filter( 'tophive/filters/header/content', [ $this, 'tophive_elementor_header_init' ], 10);
    }
    public function tophive_elementor_header_init( $content ){
        
        if( 'tophive-footer' == get_post_type() ){
            return '';
        }

        $id = $this->get_component_id('tophive-header');
        if( $id ){
            return $this->render_elementor_component( $id );
        }else{
            return $content;
        }
    }
}

// new Tophive_Core_Header();