<?php
namespace METAFANSCORE\widgets\metafanswidgets;
class WidgetHelper{
    private static $instance = null;

    public static function TH_ajax_subscribe(){
        $email = isset($_POST['email']) ? sanitize_email(wp_unslash($_POST['email'])) : '';

		if (!is_email($email)) {
			$html = '<p class="ec-text-danger ec-mt-2 small"><b>' . esc_html__('Invalid email address provided.', 'tophive') . '</b></p>';
			wp_send_json( $html, 400 );
		}

        $list_id = tophive_metafans()->get_setting('tophive_mailchimp_list_id');
        $api_key = tophive_metafans()->get_setting('tophive_mailchimp_api_key');
		$html = '';

        if( empty($list_id) || empty($api_key) ){
            $html = '<p class="ec-text-danger ec-mt-2 small"><b>'. esc_html__( 'Form is not configured yet!', 'tophive' ) .'</b></p>';
        }else{
            $MailChimp = new \MailChimp($api_key);
            $result = $MailChimp->post("lists/$list_id/members", [
                            'email_address' => $email,
                            'status'        => 'subscribed',
                        ]);
            if( isset($result['status']) && $result['status'] == 400 ){
                $html = '<p class="ec-text-danger ec-mt-2 small"><b>'. esc_html__( 'Invalid mail or already subscribed', 'tophive' ) .'</b></p>';
            } elseif( isset($result['status']) && $result['status'] == 'subscribed' ){
                $html = '<p class="ec-text-success ec-mt-2 small"><b>'. esc_html__('Thank you. You have subscribed successfully', 'tophive') . '</b></p>';
            } else {
				$html = '<p class="ec-text-danger ec-mt-2 small"><b>' . esc_html__( 'An unknown error occurred.', 'tophive' ) . '</b></p>';
			}
        }
        wp_send_json( $html, 200 );
    }
    public static function getInstance(){
        if(!isset(self::$instance)){
            self::$instance = new self();
        }
        return self::$instance;
    }
}