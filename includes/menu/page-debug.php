<?php

namespace MAXSMTP;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Max_SMTP_Debug_Page {
	static $instance;

	public function __construct(){
		add_action( 'admin_menu', [ $this, 'maxsmtp_plugin_sub_menu' ] );
	}

	public function maxsmtp_plugin_sub_menu(){
		add_submenu_page(
			'max-smtp',
			'Max SMTP - Debug',
			'Debug',
			'manage_options',
			'max-smtp-debug',
			[ $this, 'maxsmtp_plugin_settings_page' ]
		);
	}

	public function maxsmtp_plugin_settings_page() {
		echo '<div class="wrap">';
		echo '<h1>Debug</h1>';
		echo '<code style="white-space:pre-wrap;">';
		/** Test Area Start **/

			$smtp_setting	= get_option( 'max_smtp_subject_filter' );
			$found		= 0;
			//@preg_replace( $smtp_setting, '', 'x.sangalang@gmail.com', -1, $found );
			//if( $found ){
				echo $found . print_r( $smtp_setting, true );
			//} else {
			//	echo "nothing";
			//}


		/** Test Area End **/
		echo '</code>';
		echo '</div>';
	}

	public static function maxsmtp_get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}
}
add_action( 'plugins_loaded', function(){ Max_SMTP_Debug_Page::maxsmtp_get_instance(); });
?>