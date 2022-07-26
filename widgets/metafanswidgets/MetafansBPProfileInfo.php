<?php

class MetafansBPProfileInfo extends WP_Widget {
 
    public function __construct() {
        $widget_options = array(
            'classname' => 'tophive-mf-profile-info-widget',
            'description' => esc_html__( 'BuddyPress - Metafans profile descriptions', 'WP_MF_CORE_SLUG' )
        );
        parent::__construct('buddypress_profile_desc', 'Buddypress profile info [metafans]', $widget_options);
    }
    public function widget( $args, $instance ) {
    	// if ( bp_is_profile_component() ) {
	        $html ='';
            $html .= $args['before_widget'];
	        $html .= '<div>';
	        $html .= '<h4 class="widget-title">'. $instance['title'] .'</h4>';
	        
            $args = array(
                'field'   => 'User Bio',
                'user_id' => bp_displayed_user_id()
            );

            $bio = bp_get_profile_field_data( $args );

			$html .= '<p class="ec-mb-0">' . get_the_author_meta('description', bp_displayed_user_id() ) . '</p>';	
	        // $html .= '</p>';
	        $html .= '</div>';
            $html .= '</section>';
	        $html .= $args['after_widget'];
		// }
        echo $html;
    }
 
    public function form( $instance ) {
        $title = ! empty( $instance['title'] ) ? $instance['title'] : '';
        ?>
            <div class="mchimp-subs_form">
                <p>
                    <label 
                        for="<?php echo $this->get_field_id( 'title' ); ?>">Title:</label>
                    <input 
                        id="<?php echo $this->get_field_id( 'title' ); ?>" 
                        type="text" 
                        name="<?php echo $this->get_field_name( 'title' ); ?>" 
                        value="<?php echo esc_attr( $title ); ?>" 
                    />
                </p>
            </div>
        <?php
    }
 
    public function update( $new_instance, $old_instance ) {
        $instance = $old_instance;
        $instance[ 'title' ] = strip_tags( $new_instance[ 'title' ] );
        return $instance;
    }
 
}