<?php
namespace MAXSMTP;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Max_SMTP_Settings_Page {
	static $instance;

	public function __construct(){
		add_action( 'admin_menu', [ __CLASS__, 'maxsmtp_plugin_sub_menu' ] );
		add_action( 'admin_init', [ __CLASS__, 'maxsmtp_setup_sections' ] );
		add_action( 'admin_init', [ __CLASS__, 'maxsmtp_setup_fields' ] );
	}

	public static function maxsmtp_plugin_sub_menu(){
		add_submenu_page(
			'max-smtp',
			'Max SMTP - ' . __( 'Settings', 'max-smtp' ),
			__( 'Settings', 'max-smtp' ),
			'manage_options',
			'max-smtp-settings',
			[ __CLASS__, 'maxsmtp_plugin_settings_page' ]
		);
	}

	public static function maxsmtp_setup_sections() {
		add_settings_section( 'sender_section', __( 'Email Sender', 'max-smtp' ), [ __CLASS__, 'maxsmtp_section_callback' ], 'max_smtp_fields' );
		add_settings_section( 'cron_section', __( 'Cron Actions', 'max-smtp' ), [ __CLASS__, 'maxsmtp_section_callback' ], 'max_smtp_fields' );
		add_settings_section( 'filter_section', __( 'Send Filter', 'max-smtp' ), [ __CLASS__, 'maxsmtp_section_callback' ], 'max_smtp_fields' );
		add_settings_section( 'uninstall_section', __( 'Uninstall Options', 'max-smtp' ), [ __CLASS__, 'maxsmtp_section_callback' ], 'max_smtp_fields' );
	}

	public static function maxsmtp_do_settings_sections( $page ) {
		global $wp_settings_sections, $wp_settings_fields;
		if ( ! isset( $wp_settings_sections[ $page ] ) ) {
			return;
		}

		$default_tab	= 'sender_section';
		$tab			= isset( $_GET['tab'] ) && in_array( $_GET['tab'], array( 'sender_section', 'cron_section', 'filter_section', 'uninstall_section' ), true ) ? sanitize_key( $_GET['tab'] ) : $default_tab;

		echo '<nav class="nav-tab-wrapper">';
		foreach ( (array) $wp_settings_sections[ $page ] as $section_tab ) {
			$tab_active = $tab === $section_tab['id'] ? 'nav-tab-active' : null;
			echo '<a href="' . esc_url_raw( add_query_arg( [ 'tab' => $section_tab['id'] ] ) ) . '" class="nav-tab ' . $section_tab['id'] . '_tab ' . $tab_active . '">' . $section_tab['title'] . '</a>';
		}
		echo '</nav>';

		echo '<div class="max-smtp-tab-container tab-content">';
		foreach ( (array) $wp_settings_sections[ $page ] as $section ) {
			if( $tab === $section['id'] ){
				echo "<section class=\"max-smtp-tab {$section['id']} \">\n";
				if ( $section['title'] ) {
					echo "<h2>{$section['title']}</h2>\n";
				}
				if ( $section['callback'] ) {
					call_user_func( $section['callback'], $section );
				}
				if ( ! isset( $wp_settings_fields ) || ! isset( $wp_settings_fields[ $page ] ) || ! isset( $wp_settings_fields[ $page ][ $section['id'] ] ) ) {
					continue;
				}
				echo '<table class="form-table" role="presentation">';
				do_settings_fields( $page, $section['id'] );
				echo '</table>';
				echo "</section>\n";
			}
		}
		echo '</div>';
	}

	public static function maxsmtp_section_callback( $arguments ){
		switch( $arguments['id'] ){
			case 'sender_section':
				echo '<p class="description">' . __( 'Override the default email from name and address.', 'max-smtp' ) . '</p>';
				break;
			case 'cron_section':
				echo '<p class="description">' . __( 'Automated action intervals for qued emails and daily limit reset times.', 'max-smtp' ) . '</p>';
				break;
			case 'filter_section':
				echo '<p class="description">' . __( 'Stop emails with specific subjects or email addresses from being sent out.', 'max-smtp' ) . '</p>';
				break;
			case 'uninstall_section':
				echo '<p class="description">' . __( 'Selected options will remove Max SMTP saved data from your WordPress installation when the plugin is uninstalled.', 'max-smtp' ) . '</p>';
				break;
		}
	}

	public static function maxsmtp_setup_fields() {
		$fields	= [
					[
						'uid'			=> 'max_smtp_sender_field_from',
						'label'		=> __( 'Sender Name', 'max-smtp' ),
						'section'		=> 'sender_section',
						'type' 		=> 'text',
						'options'		=> false,
						'placeholder'	=> get_bloginfo( 'name' ),
						'helper'		=> '',
						'supplemental'	=> '',
						'default'		=> get_bloginfo( 'name' )
					],
					[
						'uid'			=> 'max_smtp_sender_field_email',
						'label'		=> __( 'Email Address', 'max-smtp' ),
						'section'		=> 'sender_section',
						'type' 		=> 'email',
						'options'		=> false,
						'placeholder'	=> get_bloginfo( 'admin_email' ),
						'helper'		=> '',
						'supplemental'	=> '',
						'default'		=> get_bloginfo( 'admin_email' )
					],
					[
						'uid'			=> 'max_smtp_cron_field_interval',
						'label'		=> __( 'Email Queue Interval', 'max-smtp' ),
						'section'		=> 'cron_section',
						'type' 		=> 'select',
						'options'		=> false,
						'placeholder'	=> '',
						'helper'		=> '',
						'supplemental'	=> __( 'Email queue send intervals.', 'max-smtp' ),
						'default'		=> 'maxsmtp_60'
					],
					[
						'uid'			=> 'max_smtp_queue_limit',
						'label'		=> __( 'Queue Send Limit', 'max-smtp' ),
						'section'		=> 'cron_section',
						'type' 		=> 'display',
						'options'		=> false,
						'placeholder'	=> '',
						'helper'		=> __( 'Emails Per Send Queue', 'max-smtp' ),
						'supplemental'	=> __( 'Total per second limit of your SMTP Accounts.', 'max-smtp' ),
						'default'		=> 0
					],
					[
						'uid'			=> 'max_smtp_cron_field_reset_time',
						'label'		=> __( 'Daily Reset Time', 'max-smtp' ),
						'section'		=> 'cron_section',
						'type' 		=> 'time',
						'options'		=> false,
						'placeholder'	=> '',
						'helper'		=> '',
						'supplemental'	=> defined( 'DISABLE_WP_CRON' ) ? '<span class="warning"><b>' . __( 'WP CRON is disabled', 'max-smtp' ) . '</b>' . __( ', automated schedules might run late or not at all because of this.', 'max-smtp' ) . '</span>' : '',
						'default'		=> '08:00'
					],
					[
						'uid'			=> 'max_smtp_cron_last_resend',
						'label'		=> __( 'Previous Queue Resend', 'max-smtp' ),
						'section'		=> 'cron_section',
						'type' 		=> 'display',
						'options'		=> false,
						'placeholder'	=> '',
						'helper'		=> '',
						'supplemental'	=> '',
						'default'		=> 'Pending'
					],
					[
						'uid'			=> 'max_smtp_cron_last_reset',
						'label'		=> __( 'Previous Limit Reset', 'max-smtp' ),
						'section'		=> 'cron_section',
						'type' 		=> 'display',
						'options'		=> false,
						'placeholder'	=> '',
						'helper'		=> '',
						'supplemental'	=> '',
						'default'		=> 'Pending'
					],
					[
						'uid'			=> 'max_smtp_cron_clear_sent',
						'label'		=> __( 'Previous Sent Queue Delete', 'max-smtp' ),
						'section'		=> 'cron_section',
						'type' 		=> 'display',
						'options'		=> false,
						'placeholder'	=> '',
						'helper'		=> '',
						'supplemental'	=> '',
						'default'		=> 'Pending'
					],
					[
						'uid'			=> 'max_smtp_pause_status',
						'label'		=> '',
						'section'		=> '',
						'type' 		=> '',
						'options'		=> false,
						'placeholder'	=> '',
						'helper'		=> '',
						'supplemental'	=> '',
						'default'		=> false
					],
					[
						'uid'			=> 'max_smtp_pause_message',
						'label'		=> '',
						'section'		=> '',
						'type' 		=> '',
						'options'		=> false,
						'placeholder'	=> '',
						'helper'		=> '',
						'supplemental'	=> '',
						'default'		=> ''
					],
					[
						'uid'			=> 'max_smtp_subject_filter',
						'label'		=> __( 'Email Subject Filter', 'max-smtp' ),
						'section'		=> 'filter_section',
						'type' 		=> 'textarea',
						'options'		=> false,
						'placeholder'	=> '',
						'helper'		=> '',
						'supplemental'	=> __( 'Add each Email Subject in its own line, E.g <b>WP Mail - Test</b> then next line.', 'max-smtp' ),
						'default'		=> false
					],
					[
						'uid'			=> 'max_smtp_address_filter',
						'label'		=> __( 'Email Address Filter', 'max-smtp' ),
						'section'		=> 'filter_section',
						'type' 		=> 'textarea',
						'options'		=> false,
						'placeholder'	=> '',
						'helper'		=> '',
						'supplemental'	=> __( 'Add each Email Address in its own line, E.g <b>noreply@yourdomain.com</b> then next line.<br><br>You can use <b>%%%</b> for wildcard searches, here are a few examples:<br> - <b>%%%<i>needle</i>%%%</b> to match <b>Haystack <i>needle</i> pile</b><br> - <b><i>needle</i>%%%</b> to match <b><i>needle</i> pile</b> but will not match <b>Haystack <i>needle</i> pile</b><br> - <b>%%%<i>needle</i></b> to match <b>Haystack <i>needle</i></b> but will not match <b>Haystack <i>needle</i> pile</b><br> - <b><i>Haystack </i>%%%<i> pile</i></b> to match <b>Haystack <i>needle</i> pile</b>', 'max-smtp' ),
						'default'		=> false
					],
					[
						'uid'			=> 'max_smtp_send_filter_save',
						'label'		=> __( 'Save Filtered Emails', 'max-smtp' ),
						'section'		=> 'filter_section',
						'type' 		=> 'checkbox',
						'options'		=> false,
						'placeholder'	=> '',
						'helper'		=> __( 'Save filtered emails into the Email Queue to send them manually.', 'max-smtp' ),
						'supplemental'	=> '',
						'default'		=> false
					],
					[
						'uid'			=> 'max_smtp_delete_options',
						'label'		=> __( 'Saved Settings', 'max-smtp' ),
						'section'		=> 'uninstall_section',
						'type' 		=> 'checkbox',
						'options'		=> false,
						'placeholder'	=> '',
						'helper'		=> __( 'Delete saved settings when plugin is uninstalled.', 'max-smtp' ),
						'supplemental'	=> '',
						'default'		=> false
					],
					[
						'uid'			=> 'max_smtp_delete_smtp_table',
						'label'		=> __( 'SMTP Accounts', 'max-smtp' ),
						'section'		=> 'uninstall_section',
						'type' 		=> 'checkbox',
						'options'		=> false,
						'placeholder'	=> '',
						'helper'		=> __( 'Delete "SMTP Accounts" when plugin is uninstalled.', 'max-smtp' ),
						'supplemental'	=> '',
						'default'		=> false
					],
					[
						'uid'			=> 'max_smtp_delete_queue_table',
						'label'		=> __( 'Email Queue', 'max-smtp' ),
						'section'		=> 'uninstall_section',
						'type' 		=> 'checkbox',
						'options'		=> false,
						'placeholder'	=> '',
						'helper'		=> __( 'Delete "Email Queue" when plugin is uninstalled.', 'max-smtp' ),
						'supplemental'	=> '',
						'default'		=> false
					]
				];
		foreach( $fields as $field ){
			add_settings_field( $field['uid'], $field['label'], [ __CLASS__, 'maxsmtp_field_callback' ], 'max_smtp_fields', $field['section'], $field );
			register_setting( 'max_smtp_fields', $field['uid'] );
			if( empty( get_option( $field['uid'] ) ) && !empty( $field['default'] ) ){
				update_option( $field['uid'], $field['default'] );
			}
		}
	}

	public static function maxsmtp_field_callback( $arguments ) {
		$value	= get_option( $arguments['uid'] );
		if( ! $value ) {
			$value	= $arguments['default'];
		}

		switch( $arguments['type'] ){
			case 'text':
			case 'email':
			case 'time':
				printf( '<input name="%1$s" id="%1$s" type="%2$s" placeholder="%3$s" value="%4$s" />', $arguments['uid'], $arguments['type'], $arguments['placeholder'], esc_attr( $value ) );
				break;
			case 'textarea':
				if( is_array( $value ) ){
					$value = @preg_filter( ['/\A\/\\\A\(/', '/\)\\\Z\/\Z/', '/\.\+/', '/\\\\\\\\/'], ['', '', '%%%', '\\'], $value );
					$value = @stripslashes( @implode( "\n", $value ) );
				}
				printf( '<textarea name="%1$s" id="%1$s" rows="4" cols="50">%2$s</textarea>', $arguments['uid'], esc_textarea( $value ) );
				break;
			case 'checkbox':
				$checked = $value === "true" ? 'checked' : '';
				printf( '<input name="%1$s" id="%1$s" type="%2$s" value="true" %3$s />', $arguments['uid'], $arguments['type'], $checked );
				break;
			case 'select':
				$schedules	= Max_SMTP_Plugin::maxsmtp_cron_intervals( [] );
				printf( '<select name="%1$s" id="%1$s">', $arguments['uid'] );
				foreach( $schedules as $schedule => $array ):
					$selected	= $value == $schedule ? 'selected' : '';
					printf( '<option value="%1$s" %2$s>%3$s</option>', $schedule, $selected, $array['display'] );
				endforeach;
				print( '</select>' );
				break;
			case 'display':
				printf( '<span class="%1$s">%2$s</span>', $arguments['uid'], esc_html( $value ) );
				break;
		}

		if( $helper = $arguments['helper'] ){
			printf( '<label class="helper" for="%1$s"> %2$s</label>', $arguments['uid'], $helper );
		}

		if( $supplimental = $arguments['supplemental'] ){
			printf( '<p class="description">%s</p>', $supplimental );
		}
	}

	public static function maxsmtp_handle_form() {
		if( ! isset( $_POST['max_smtp_update_form'] ) || ! wp_verify_nonce( $_POST['max_smtp_update_form'], 'max_smtp_update' ) ){
			Max_SMTP_Plugin::maxsmtp_admin_notification( 'Somthing terrible happend... Your settings was not saved, please try again.', 'error' );
			wp_redirect( esc_url_raw( add_query_arg( [] ) ) );
		} else {
			if( isset( $_POST['updated'] ) && !empty( $_POST['updated'] ) ){
				foreach( [ 'max_smtp_sender_field_from', 'max_smtp_sender_field_email', 'max_smtp_cron_field_interval', 'max_smtp_cron_field_reset_time', 'max_smtp_subject_filter', 'max_smtp_address_filter', 'max_smtp_send_filter_save', 'max_smtp_delete_options', 'max_smtp_delete_smtp_table', 'max_smtp_delete_queue_table' ] as $opt_key ){
					$opt_val	= '';
					switch( $opt_key ){
						case 'max_smtp_sender_field_from':
							$opt_val	= isset( $_POST[ $opt_key ] ) ? sanitize_text_field( $_POST[ $opt_key ] ) : null;
							break;
						case 'max_smtp_subject_filter':
						case 'max_smtp_address_filter':
							$rgx_arr = [];
							$opt_val	= isset( $_POST[ $opt_key ] ) ? sanitize_textarea_field( $_POST[ $opt_key ] ) : null;
							if( !empty( $opt_val = preg_split( '/\R/', $opt_val, 0, PREG_SPLIT_NO_EMPTY ) ) ){
								if( is_array( $opt_val ) && !empty( $opt_val ) ){
									foreach( $opt_val as $opt_rgx ){
										$rgx_arr[] = '/\A(' . preg_replace( '/%%%/', '.+', preg_quote( $opt_rgx, '/' ) ) . ')\Z/';
									}
								}
							}
							$opt_val = $rgx_arr;
							unset( $rgx_arr );
							break;
						case 'max_smtp_sender_field_email':
							$opt_val	= isset( $_POST[ $opt_key ] ) ? sanitize_email( $_POST[ $opt_key ] ) : null;
							break;
						case 'max_smtp_cron_field_interval':
							$opt_val	= isset( $_POST[ $opt_key ] ) && in_array( sanitize_text_field( $_POST[ $opt_key ] ), [ 'maxsmtp_10', 'maxsmtp_20', 'maxsmtp_30', 'maxsmtp_40', 'maxsmtp_50', 'maxsmtp_60', ] ) ? sanitize_text_field( $_POST[ $opt_key ] ) : 'maxsmtp_60';
							break;
						case 'max_smtp_cron_field_reset_time':
							$opt_val	= isset( $_POST[ $opt_key ] ) && preg_match( '/^(?:[01][0-9]|2[0-3]):[0-5][0-9]$/', $opt_val ) ? sanitize_text_field( $_POST[ $opt_key ] ) : null;;
							break;
						case 'max_smtp_send_filter_save':
						case 'max_smtp_delete_options':
						case 'max_smtp_delete_smtp_table':
						case 'max_smtp_delete_queue_table':
							$opt_val	= isset( $_POST[ $opt_key ] ) && sanitize_text_field( $_POST[ $opt_key ] ) === "true" ? "true" : "false";
							break;
					}
					update_option( $opt_key, $opt_val );
				}
			}
			Max_SMTP_Plugin::maxsmtp_clear_cron();
			Max_SMTP_Plugin::maxsmtp_update_queue_send_limits();
			Max_SMTP_Mail_Functions::maxsmtp_set_smtp_option();
			Max_SMTP_Plugin::maxsmtp_admin_notification( __( 'Settings saved.', 'max-smtp' ), 'success' );
			wp_redirect( esc_url_raw( add_query_arg( [] ) ) );
		}
	}

	public static function maxsmtp_plugin_settings_page() {
		if( isset( $_POST['updated'] ) && $_POST['updated'] === 'true'  ){
			Max_SMTP_Settings_Page::maxsmtp_handle_form();
		}
		$status	= get_option( 'max_smtp_pause_status' );
		?>
			<div class="wrap max-smtp max-smtp-settings">
				<h1><img class="max-smtp-logo" src="<?php echo esc_url( MAXSMTP_URL . '/assets/images/logo.png' ); ?>" alt="Max SMTP"> <?php _e( 'Settings', 'max-smtp' ); ?></h1>
				<?php if( $status ): ?>
					<div id="message" class="error notice notice-warning is-dismissible">
						<p><?php _e( 'Max SMTP: Sending emails via SMTP is currently paused.', 'max-smtp' ); ?> <?php esc_html_e( get_option( 'max_smtp_pause_message' ) ); ?></p>
					</div>
				<?php endif; ?>
				<div class="max-smtp-page-wrapper">
					<div class="max-smtp-page-content">
						<form method="post">
							<input type="hidden" name="updated" value="true" />
							<?php wp_nonce_field( 'max_smtp_update', 'max_smtp_update_form' ); ?>
							<?php settings_fields( 'max_smtp_fields' ); ?>
							<?php self::maxsmtp_do_settings_sections( 'max_smtp_fields' ); ?>
							<?php submit_button(); ?>
						</form>
					</div>
					<div class="max-smtp-page-sidebar">
						<div class="max-smtp-page-sidebar-item effin-studios">
							<img src="<?php echo esc_url( MAXSMTP_URL . '/assets/images/effinstudios.png' ); ?>" width="200" height="200" alt="Effin Studios">
						</div>
						<div class="max-smtp-page-sidebar-item support-us">
							<h3>Love what we are doing?</h3>
							<p>Help us keep developing more great stuff by buying us a drink or three, we truly appreciate every bit of your support!</p>
							<a class="button" href="https://ko-fi.com/effinstudios" target="_blank" rel="nofollow">Buy us coffee</a>
						</div>
					</div>
				</div>
			</div>
		<?php
	}

	public static function maxsmtp_get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}
}

add_action( 'plugins_loaded', function(){ Max_SMTP_Settings_Page::maxsmtp_get_instance(); });
?>