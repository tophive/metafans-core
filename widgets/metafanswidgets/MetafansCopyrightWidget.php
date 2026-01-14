<?php

class MetafansCopyrightWidget extends WP_Widget {

    public function __construct() {
        $widget_options = array(
            'classname' => 'tophive_copyright_widget',
            'description' => esc_html__( 'Display copyright text.', 'WP_MF_CORE_SLUG' )
        );
        parent::__construct('tophive_copyright_widget', 'MC Copyright', $widget_options);
    }

    public function widget( $args, $instance ) {
        $content = ! empty( $instance['content'] ) ? $instance['content'] : '';

        echo $args['before_widget'];
        ?>
        <div class="copyright-widget-content">
            <?php echo do_shortcode( wp_kses_post( $content ) ); ?>
        </div>
        <?php
        echo $args['after_widget'];
    }

    public function form( $instance ) {
        $content = ! empty( $instance['content'] ) ? $instance['content'] : '';
        ?>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'content' ) ); ?>"><?php esc_html_e( 'Content:', 'WP_MF_CORE_SLUG' ); ?></label>
            <textarea
                    class="widefat"
                    id="<?php echo esc_attr( $this->get_field_id( 'content' ) ); ?>"
                    name="<?php echo esc_attr( $this->get_field_name( 'content' ) ); ?>"
                    rows="10"
            ><?php echo esc_textarea( $content ); ?></textarea>
        </p>
        <?php
    }

    public function update( $new_instance, $old_instance ) {
        $instance = $old_instance;
        if ( current_user_can( 'unfiltered_html' ) ) {
            $instance['content'] = isset( $new_instance['content'] ) ? $new_instance['content'] : '';
        } else {
            $instance['content'] = isset( $new_instance['content'] ) ? wp_kses_post( $new_instance['content'] ) : '';
        }
        return $instance;
    }
}
