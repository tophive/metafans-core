<?php

class MetafansBPProfileMedia extends WP_Widget {
 
    public function __construct() {
        $widget_options = array(
            'classname' => 'tophive-mf-profile-media-widget',
            'description' => esc_html__( 'BuddyPress - Metafans Profile Media', 'WP_TH_CORE_SLUG' )
        );
        parent::__construct('buddypress_profile_media', 'Buddypress profile media [metafans]', $widget_options);
    }
    public function widget( $args, $instance ) {
    	// if ( bp_is_profile_component() ) {
	        $html = $args['before_widget'];
	        $html .= '<div>';
	        $html .= '<h4 class="widget-title">'. $instance['title'] .'</h4>';
	        
            global $wpdb;
            $user_id = bp_displayed_user_id();
            $all_images = [];
            $media_html = '';
            $activities = $wpdb->get_results("SELECT id from {$wpdb->base_prefix}bp_activity WHERE user_id={$user_id} and type='activity_update'", ARRAY_N);

            if( !empty($activities) ){
                foreach ($activities as $key => $value) {
                    $images = bp_activity_get_meta( $value[0], 'activity_media', false );
                    $newImages = $images[0];
                    $newImages[0]['activity_id'] = $value[0];

                    if( !empty($images) )
                        array_push($all_images, ...$newImages);
                }
                array_filter($all_images);
                $media_html .= '<div class="ec-row bp-image-previewer">';
                $i = 1;
                foreach ($all_images as $url) {
                    if( !empty($url['thumb']) ){
                        $media_html .= '<div class="ec-col-4 bp-image-single" id="'. $i .'">';
                            $media_html .= '<div class="post-media-single">';
                                $media_html .= '<a class="media-popup-thumbnail" href="'. $url['thumb'][0] .'" data-id="'. $url['id'] .'" data-activity="'. $url['activity_id'] .'"><img src="'. $url['full'] .'" alt="gm"></a>';
                            $media_html .= '</div>';
                        $media_html .= '</div>';
                    }
                    $i++;
                }
                if( empty($all_images) ){
                    $media_html .= '<span class="no-photos">' . esc_html__( 'No photos uploaded', 'metafans' ) . '</span>';
                }
                $media_html .= '</div>';
            }
	        $html .= $media_html;
            $html .= '</div>';
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