<?php
/**
 * "Subaru Settings" admin page (form recipients, WhatsApp, social, showrooms, cookie bar).
 *
 * @package SubaruAlKhoory
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Form types and their labels.
 *
 * @return array
 */
function subaru_form_types() {
	return array(
		'test-drive' => __( 'Test drive', 'subaru-alkhoory' ),
		'contact'    => __( 'Contact', 'subaru-alkhoory' ),
		'service'    => __( 'Service booking', 'subaru-alkhoory' ),
		'brochure'   => __( 'Brochure request', 'subaru-alkhoory' ),
		'offer'      => __( 'Offer enquiry', 'subaru-alkhoory' ),
		'newsletter' => __( 'Newsletter', 'subaru-alkhoory' ),
	);
}

/**
 * Register the menu page.
 */
function subaru_settings_menu() {
	add_menu_page(
		__( 'Subaru Settings', 'subaru-alkhoory' ),
		__( 'Subaru Settings', 'subaru-alkhoory' ),
		'manage_options',
		'subaru-settings',
		'subaru_settings_page',
		'dashicons-car',
		59
	);
}
add_action( 'admin_menu', 'subaru_settings_menu' );

/**
 * Register setting with sanitisation.
 */
function subaru_settings_register() {
	register_setting(
		'subaru_settings_group',
		SUBARU_OPTION,
		array(
			'type'              => 'array',
			'sanitize_callback' => 'subaru_settings_sanitize',
			'default'           => array(),
		)
	);
}
add_action( 'admin_init', 'subaru_settings_register' );

/**
 * Sanitize settings.
 *
 * @param array $input Raw input.
 * @return array
 */
function subaru_settings_sanitize( $input ) {
	$input = is_array( $input ) ? $input : array();
	$out   = array();

	foreach ( array_keys( subaru_form_types() ) as $type ) {
		$key    = 'recipient_' . $type;
		$emails = isset( $input[ $key ] ) ? array_filter( array_map( 'sanitize_email', array_map( 'trim', explode( ',', (string) $input[ $key ] ) ) ) ) : array();
		$out[ $key ] = implode( ', ', array_filter( $emails, 'is_email' ) );
	}

	$out['autoreply']    = empty( $input['autoreply'] ) ? 0 : 1;
	$out['cookie_notice'] = empty( $input['cookie_notice'] ) ? 0 : 1;
	$out['page_intro']   = empty( $input['page_intro'] ) ? 0 : 1;

	$out['recaptcha_site_key']   = isset( $input['recaptcha_site_key'] ) ? sanitize_text_field( $input['recaptcha_site_key'] ) : '';
	$out['recaptcha_secret_key'] = isset( $input['recaptcha_secret_key'] ) ? sanitize_text_field( $input['recaptcha_secret_key'] ) : '';
	$out['from_name']        = isset( $input['from_name'] ) ? sanitize_text_field( $input['from_name'] ) : '';
	$out['whatsapp_number']  = isset( $input['whatsapp_number'] ) ? preg_replace( '/[^0-9]/', '', (string) $input['whatsapp_number'] ) : '';
	$out['whatsapp_message'] = isset( $input['whatsapp_message'] ) ? sanitize_text_field( $input['whatsapp_message'] ) : '';
	$out['cookie_text']      = isset( $input['cookie_text'] ) ? sanitize_textarea_field( $input['cookie_text'] ) : '';
	$out['phone']            = isset( $input['phone'] ) ? sanitize_text_field( $input['phone'] ) : '';
	$out['email']            = isset( $input['email'] ) ? sanitize_email( $input['email'] ) : '';

	foreach ( array( 'instagram', 'facebook', 'linkedin', 'youtube' ) as $net ) {
		$out[ 'social_' . $net ] = isset( $input[ 'social_' . $net ] ) ? esc_url_raw( trim( $input[ 'social_' . $net ] ) ) : '';
	}

	// Showrooms: JSON array of objects. Sanitise every field.
	$out['showrooms'] = '';
	$raw              = isset( $input['showrooms'] ) ? trim( (string) $input['showrooms'] ) : '';
	if ( '' !== $raw ) {
		$data = json_decode( $raw, true );
		if ( ! is_array( $data ) ) {
			add_settings_error( SUBARU_OPTION, 'showrooms', __( 'Showrooms: invalid JSON — previous value kept.', 'subaru-alkhoory' ) );
			$out['showrooms'] = subaru_setting( 'showrooms' );
		} else {
			$clean = array();
			foreach ( $data as $row ) {
				if ( ! is_array( $row ) || empty( $row['name'] ) ) {
					continue;
				}
				$item = array();
				foreach ( array( 'name', 'type', 'area', 'address', 'phone', 'email', 'hours', 'map' ) as $f ) {
					$item[ $f ] = isset( $row[ $f ] ) ? sanitize_textarea_field( (string) $row[ $f ] ) : '';
				}
				$clean[] = $item;
			}
			$out['showrooms'] = $clean ? wp_json_encode( $clean, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) : '';
		}
	}
	return $out;
}

/**
 * Render a text input row.
 *
 * @param string $key   Key.
 * @param string $label Label.
 * @param string $help  Help text.
 * @param string $type  Input type.
 */
function subaru_settings_field( $key, $label, $help = '', $type = 'text' ) {
	$opts = (array) get_option( SUBARU_OPTION, array() );
	$defs = subaru_setting_defaults();
	$val  = isset( $opts[ $key ] ) ? $opts[ $key ] : ( isset( $defs[ $key ] ) ? $defs[ $key ] : '' );
	$name = SUBARU_OPTION . '[' . $key . ']';
	echo '<tr><th scope="row"><label for="' . esc_attr( 'sa-' . $key ) . '">' . esc_html( $label ) . '</label></th><td>';
	if ( 'checkbox' === $type ) {
		echo '<label><input type="checkbox" id="' . esc_attr( 'sa-' . $key ) . '" name="' . esc_attr( $name ) . '" value="1" ' . checked( ! empty( $val ), true, false ) . '> ' . esc_html( $help ) . '</label>';
	} elseif ( 'textarea' === $type || 'code' === $type ) {
		echo '<textarea id="' . esc_attr( 'sa-' . $key ) . '" name="' . esc_attr( $name ) . '" rows="' . ( 'code' === $type ? 14 : 3 ) . '" class="large-text' . ( 'code' === $type ? ' code' : '' ) . '">' . esc_textarea( $val ) . '</textarea>';
		if ( $help ) {
			echo '<p class="description">' . wp_kses_post( $help ) . '</p>';
		}
	} else {
		echo '<input type="' . esc_attr( $type ) . '" id="' . esc_attr( 'sa-' . $key ) . '" name="' . esc_attr( $name ) . '" value="' . esc_attr( $val ) . '" class="regular-text">';
		if ( $help ) {
			echo '<p class="description">' . wp_kses_post( $help ) . '</p>';
		}
	}
	echo '</td></tr>';
}

/**
 * Settings page.
 */
function subaru_settings_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	$example = wp_json_encode(
		array(
			array(
				'name'    => 'Al Khoory Automobiles Showroom — Dubai',
				'type'    => 'Showroom',
				'area'    => 'Dubai',
				'address' => '',
				'phone'   => '',
				'email'   => '',
				'hours'   => '',
				'map'     => 'Al Khoory Automobiles Subaru Dubai',
			),
		),
		JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
	);
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Subaru Settings', 'subaru-alkhoory' ); ?></h1>
		<?php settings_errors( SUBARU_OPTION ); ?>
		<form method="post" action="options.php">
			<?php settings_fields( 'subaru_settings_group' ); ?>

			<h2><?php esc_html_e( 'Enquiry form recipients', 'subaru-alkhoory' ); ?></h2>
			<p><?php
				/* translators: %s: admin email */
				printf( esc_html__( 'Comma-separate multiple addresses. Empty = %s.', 'subaru-alkhoory' ), esc_html( SUBARU_DEFAULT_RECIPIENT ) );
			?></p>
			<table class="form-table" role="presentation">
				<?php
				foreach ( subaru_form_types() as $type => $label ) {
					subaru_settings_field( 'recipient_' . $type, $label );
				}
				subaru_settings_field( 'autoreply', __( 'Customer auto-reply', 'subaru-alkhoory' ), __( 'Send a confirmation email to the customer', 'subaru-alkhoory' ), 'checkbox' );
				subaru_settings_field( 'from_name', __( 'Email "from" name', 'subaru-alkhoory' ) );
				?>
			</table>

			<h2><?php esc_html_e( 'Spam protection (Google reCAPTCHA)', 'subaru-alkhoory' ); ?></h2>
			<p><?php esc_html_e( 'Create reCAPTCHA v2 "I\'m not a robot" keys for this domain at google.com/recaptcha/admin and paste them here. While either key is empty, the forms use the built-in maths question.', 'subaru-alkhoory' ); ?></p>
			<table class="form-table" role="presentation">
				<?php
				subaru_settings_field( 'recaptcha_site_key', __( 'reCAPTCHA site key', 'subaru-alkhoory' ) );
				subaru_settings_field( 'recaptcha_secret_key', __( 'reCAPTCHA secret key', 'subaru-alkhoory' ) );
				?>
			</table>

			<h2><?php esc_html_e( 'Contact & WhatsApp', 'subaru-alkhoory' ); ?></h2>
			<table class="form-table" role="presentation">
				<?php
				subaru_settings_field( 'whatsapp_number', __( 'WhatsApp number', 'subaru-alkhoory' ), __( 'International format without + or spaces, e.g. 9715XXXXXXXX. Leave empty to hide the floating WhatsApp button.', 'subaru-alkhoory' ) );
				subaru_settings_field( 'whatsapp_message', __( 'WhatsApp pre-filled message', 'subaru-alkhoory' ) );
				subaru_settings_field( 'phone', __( 'Main phone number', 'subaru-alkhoory' ), __( 'Used in structured data and auto-reply emails. Optional.', 'subaru-alkhoory' ) );
				subaru_settings_field( 'email', __( 'Public email', 'subaru-alkhoory' ), '', 'email' );
				?>
			</table>

			<h2><?php esc_html_e( 'Social profiles', 'subaru-alkhoory' ); ?></h2>
			<table class="form-table" role="presentation">
				<?php
				subaru_settings_field( 'social_instagram', 'Instagram', '', 'url' );
				subaru_settings_field( 'social_facebook', 'Facebook', '', 'url' );
				subaru_settings_field( 'social_linkedin', 'LinkedIn', '', 'url' );
				subaru_settings_field( 'social_youtube', 'YouTube', __( 'Empty profiles are hidden in the footer.', 'subaru-alkhoory' ), 'url' );
				?>
			</table>

			<h2><?php esc_html_e( 'Showrooms & service centres', 'subaru-alkhoory' ); ?></h2>
			<table class="form-table" role="presentation">
				<?php
				subaru_settings_field(
					'showrooms',
					__( 'Locations (JSON)', 'subaru-alkhoory' ),
					sprintf(
						/* translators: %s: example JSON */
						__( 'Used by the [subaru_showrooms] shortcode and the test-drive / service forms. A list of objects with keys name, type, area, address, phone, email, hours, map (Google Maps search text). Empty fields are hidden. Leave the whole box empty to use placeholder locations. Example:<br><code style="white-space:pre">%s</code>', 'subaru-alkhoory' ),
						esc_html( $example )
					),
					'code'
				);
				?>
			</table>

			<h2><?php esc_html_e( 'Site experience', 'subaru-alkhoory' ); ?></h2>
			<table class="form-table" role="presentation">
				<?php
				subaru_settings_field( 'cookie_notice', __( 'Cookie notice', 'subaru-alkhoory' ), __( 'Show the dismissable cookie bar', 'subaru-alkhoory' ), 'checkbox' );
				subaru_settings_field( 'cookie_text', __( 'Cookie notice text', 'subaru-alkhoory' ), '', 'textarea' );
				subaru_settings_field( 'page_intro', __( 'Intro animation', 'subaru-alkhoory' ), __( 'Show the short logo reveal on the first page view of a session', 'subaru-alkhoory' ), 'checkbox' );
				?>
			</table>
			<?php submit_button(); ?>
		</form>
	</div>
	<?php
}
