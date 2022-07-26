<?php
namespace METAFANSCORE\widgets\metafanswidgets;
class WidgetHelper{
    private static $instance = null;

    public static function TH_ajax_subscribe(){
        $email = $_POST['email'];
        $list_id = tophive_metafans()->get_setting('tophive_mailchimp_list_id');
        $api_key = tophive_metafans()->get_setting('tophive_mailchimp_api_key');

        if( empty($list_id) || empty($api_key) ){
            $html = '<p class="ec-text-danger ec-mt-2 small"><b>'. esc_html__( 'Form is not configured yet!', 'tophive' ) .'</b></p>';
        }else{
            $MailChimp = new \MailChimp($api_key);
            $result = $MailChimp->post("lists/$list_id/members", [
                            'email_address' => $email,
                            'status'        => 'subscribed',
                        ]);
            if( $result['status'] == 400 ){
                $html = '<p class="ec-text-danger ec-mt-2 small"><b>'. esc_html__( 'Invalid mail or already subscribed', 'tophive' ) .'</b></p>';
            } elseif( $result['status'] == 'subscribed' ){
                $html = '<p class="ec-text-success ec-mt-2 small"><b>'. esc_html__('Thank you. You have subscribed successfully', 'tophive') . '</b></p>';
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