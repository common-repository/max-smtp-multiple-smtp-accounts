<?php
	namespace MAXSMTP;

	if ( !defined( 'ABSPATH' ) ) {
		exit;
	}

	if( !isset( $_GET['id'] ) ){
		exit;
	}

	if( !current_user_can( 'manage_options' ) ){
		exit;
	}

	class Max_SMTP_Email_View {
		public $content;
		public function __construct() {
			if ( wp_verify_nonce( $_REQUEST['_wpnonce'], 'max_smtp_nounce' ) ) {
				global $wpdb;
				$queue_id	= absint( esc_attr( $_GET['id'] ) );
				$email_content	= $wpdb->get_var( 'SELECT mail_body FROM ' . $wpdb->base_prefix . 'maxsmtp_queue WHERE id = "' . $queue_id . '"' );

				if( !empty( $email_content ) ){
					$found = 0;
					$email_content	= preg_replace( '/\<title[ |\>][.|\s|\S]+\<\/title\>/', '<title>Email View - Max SMTP</title>', $email_content, -1, $found );
					if( !$found ){
						$email_content	= preg_replace( '/\<\/head\>/', '<title>Email View - Max SMTP</title>', $email_content );
					}
					$this->content = $email_content;
				}
			}
		}
		public function __destruct() {
			echo $this->content;
		}
	}
	new Max_SMTP_Email_View();
?>