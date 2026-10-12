<?php
/**
 * Enquiry forms: field definitions, rendering, AJAX submission (validation, anti-spam, storage, email).
 *
 * @package SubaruAlKhoory
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Model options.
 *
 * @return array value => label
 */
function subaru_model_options() {
	$out = array();
	foreach ( subaru_models() as $m ) {
		$out[ $m['name'] ] = 'Subaru ' . $m['name'];
	}
	return $out;
}

/**
 * Location options filtered by kind (showroom|service).
 *
 * @param string $kind Kind.
 * @return array
 */
function subaru_location_options( $kind ) {
	$out = array();
	foreach ( subaru_showrooms() as $s ) {
		$type = strtolower( $s['type'] . ' ' . $s['name'] );
		if ( 'service' === $kind && false === strpos( $type, 'service' ) ) {
			continue;
		}
		if ( 'showroom' === $kind && false === strpos( $type, 'showroom' ) ) {
			continue;
		}
		$out[ $s['name'] ] = $s['name'];
	}
	if ( ! $out ) {
		foreach ( subaru_showrooms() as $s ) {
			$out[ $s['name'] ] = $s['name'];
		}
	}
	$out['No preference'] = __( 'No preference', 'subaru-alkhoory' );
	return $out;
}

/**
 * Contact enquiry types.
 *
 * @return array
 */
function subaru_enquiry_types() {
	return array(
		'general'    => __( 'General enquiry', 'subaru-alkhoory' ),
		'sales'      => __( 'New vehicle sales', 'subaru-alkhoory' ),
		'test-drive' => __( 'Test drive', 'subaru-alkhoory' ),
		'brochure'   => __( 'Brochure request', 'subaru-alkhoory' ),
		'offer'      => __( 'Current offers', 'subaru-alkhoory' ),
		'service'    => __( 'Service & maintenance', 'subaru-alkhoory' ),
		'warranty'   => __( 'Warranty', 'subaru-alkhoory' ),
		'assistance' => __( 'On-road assistance', 'subaru-alkhoory' ),
		'hybrid'     => __( 'Hybrid technology', 'subaru-alkhoory' ),
		'parts'      => __( 'Parts & accessories', 'subaru-alkhoory' ),
		'feedback'   => __( 'Feedback', 'subaru-alkhoory' ),
	);
}

/**
 * Field definitions for every form type.
 *
 * Keys: label, type, required, half, options, autocomplete, hint, show_if.
 *
 * @param string $type Form type.
 * @return array
 */
function subaru_form_fields( $type ) {
	$first   = array( 'label' => __( 'First name', 'subaru-alkhoory' ), 'type' => 'text', 'required' => true, 'half' => true, 'autocomplete' => 'given-name' );
	$last    = array( 'label' => __( 'Last name', 'subaru-alkhoory' ), 'type' => 'text', 'required' => true, 'half' => true, 'autocomplete' => 'family-name' );
	$name    = array( 'label' => __( 'Full name', 'subaru-alkhoory' ), 'type' => 'text', 'required' => true, 'half' => true, 'autocomplete' => 'name' );
	$email   = array( 'label' => __( 'Email address', 'subaru-alkhoory' ), 'type' => 'email', 'required' => true, 'half' => true, 'autocomplete' => 'email' );
	$mobile  = array( 'label' => __( 'Mobile number', 'subaru-alkhoory' ), 'type' => 'tel', 'required' => true, 'half' => true, 'autocomplete' => 'tel', 'hint' => __( 'UAE mobile, e.g. 05X XXX XXXX or +971 5X XXX XXXX', 'subaru-alkhoory' ) );
	$model   = array( 'label' => __( 'Model', 'subaru-alkhoory' ), 'type' => 'select', 'required' => true, 'half' => true, 'options' => subaru_model_options() );
	$consent = array( 'label' => __( 'I agree to be contacted by Al Khoory Automobiles about my enquiry and accept the Privacy Policy.', 'subaru-alkhoory' ), 'type' => 'consent', 'required' => true );
	$slots   = array(
		'Morning (9am – 12pm)'   => __( 'Morning (9am – 12pm)', 'subaru-alkhoory' ),
		'Afternoon (12pm – 4pm)' => __( 'Afternoon (12pm – 4pm)', 'subaru-alkhoory' ),
		'Evening (4pm – 8pm)'    => __( 'Evening (4pm – 8pm)', 'subaru-alkhoory' ),
	);

	switch ( $type ) {
		case 'test-drive':
			$fields = array(
				'model'    => $model,
				'showroom' => array( 'label' => __( 'Preferred showroom', 'subaru-alkhoory' ), 'type' => 'select', 'required' => true, 'half' => true, 'options' => subaru_location_options( 'showroom' ) ),
				'first_name' => $first,
				'last_name'  => $last,
				'email'      => $email,
				'mobile'     => $mobile,
				'date'       => array( 'label' => __( 'Preferred date', 'subaru-alkhoory' ), 'type' => 'date', 'required' => true, 'half' => true ),
				'time'       => array( 'label' => __( 'Preferred time', 'subaru-alkhoory' ), 'type' => 'select', 'required' => true, 'half' => true, 'options' => $slots ),
				'comments'   => array( 'label' => __( 'Comments (optional)', 'subaru-alkhoory' ), 'type' => 'textarea', 'required' => false ),
				'consent'    => $consent,
			);
			break;
		case 'contact':
			$fields = array(
				'name'    => $name,
				'email'   => $email,
				'mobile'  => $mobile,
				'enquiry' => array( 'label' => __( 'Enquiry type', 'subaru-alkhoory' ), 'type' => 'select', 'required' => true, 'half' => true, 'options' => subaru_enquiry_types() ),
				'model'   => array_merge( $model, array( 'required' => false, 'label' => __( 'Model of interest', 'subaru-alkhoory' ), 'half' => false, 'show_if' => 'enquiry:sales,test-drive,brochure,offer,hybrid,warranty' ) ),
				'message' => array( 'label' => __( 'Your message', 'subaru-alkhoory' ), 'type' => 'textarea', 'required' => true ),
				'consent' => $consent,
			);
			break;
		case 'service':
			$fields = array(
				'name'    => $name,
				'mobile'  => $mobile,
				'email'   => $email,
				'model'   => $model,
				'vin'     => array( 'label' => __( 'Plate number or VIN', 'subaru-alkhoory' ), 'type' => 'text', 'required' => true, 'half' => true ),
				'mileage' => array( 'label' => __( 'Current mileage (km)', 'subaru-alkhoory' ), 'type' => 'number', 'required' => false, 'half' => true ),
				'service' => array(
					'label'    => __( 'Service type', 'subaru-alkhoory' ),
					'type'     => 'select',
					'required' => true,
					'half'     => true,
					'options'  => array(
						'Periodic maintenance' => __( 'Periodic maintenance', 'subaru-alkhoory' ),
						'Repair / diagnosis'   => __( 'Repair / diagnosis', 'subaru-alkhoory' ),
						'Warranty work'        => __( 'Warranty work', 'subaru-alkhoory' ),
						'Tyres & battery'      => __( 'Tyres & battery', 'subaru-alkhoory' ),
						'Body & paint'         => __( 'Body & paint', 'subaru-alkhoory' ),
						'Other'                => __( 'Other', 'subaru-alkhoory' ),
					),
				),
				'date'    => array( 'label' => __( 'Preferred date', 'subaru-alkhoory' ), 'type' => 'date', 'required' => true, 'half' => true ),
				'centre'  => array( 'label' => __( 'Service centre', 'subaru-alkhoory' ), 'type' => 'select', 'required' => true, 'options' => subaru_location_options( 'service' ) ),
				'notes'   => array( 'label' => __( 'Notes (optional)', 'subaru-alkhoory' ), 'type' => 'textarea', 'required' => false ),
				'consent' => $consent,
			);
			break;
		case 'brochure':
			$fields = array(
				'name'    => $name,
				'email'   => $email,
				'mobile'  => $mobile,
				'model'   => $model,
				'consent' => $consent,
			);
			break;
		case 'offer':
			$fields = array(
				'name'    => $name,
				'email'   => $email,
				'mobile'  => $mobile,
				'model'   => $model,
				'message' => array( 'label' => __( 'Message (optional)', 'subaru-alkhoory' ), 'type' => 'textarea', 'required' => false ),
				'consent' => $consent,
			);
			break;
		case 'newsletter':
			$fields = array(
				'email' => array( 'label' => __( 'Enter your email address', 'subaru-alkhoory' ), 'type' => 'email', 'required' => true, 'autocomplete' => 'email' ),
			);
			break;
		default:
			$fields = array();
	}
	return apply_filters( 'subaru_form_fields', $fields, $type );
}

/**
 * Submit button labels.
 *
 * @param string $type Type.
 * @return string
 */
function subaru_form_submit_label( $type ) {
	$labels = array(
		'test-drive' => __( 'Book My Test Drive', 'subaru-alkhoory' ),
		'contact'    => __( 'Send Enquiry', 'subaru-alkhoory' ),
		'service'    => __( 'Request Service Booking', 'subaru-alkhoory' ),
		'brochure'   => __( 'Request Brochure', 'subaru-alkhoory' ),
		'offer'      => __( 'Enquire About This Offer', 'subaru-alkhoory' ),
		'newsletter' => __( 'Subscribe', 'subaru-alkhoory' ),
	);
	return isset( $labels[ $type ] ) ? $labels[ $type ] : __( 'Submit', 'subaru-alkhoory' );
}

/**
 * Success messages.
 *
 * @param string $type Type.
 * @return array title, text
 */
function subaru_form_success( $type ) {
	switch ( $type ) {
		case 'test-drive':
			return array( __( 'Test drive requested', 'subaru-alkhoory' ), __( 'Thank you. Our team will contact you shortly to confirm your test drive.', 'subaru-alkhoory' ) );
		case 'service':
			return array( __( 'Service request received', 'subaru-alkhoory' ), __( 'Thank you. A service advisor will contact you to confirm your appointment.', 'subaru-alkhoory' ) );
		case 'brochure':
			return array( __( 'Request received', 'subaru-alkhoory' ), __( 'Thank you. We will send the brochure to your email shortly.', 'subaru-alkhoory' ) );
		case 'newsletter':
			return array( __( 'You are subscribed', 'subaru-alkhoory' ), __( 'Thank you for subscribing to Subaru UAE news.', 'subaru-alkhoory' ) );
		default:
			return array( __( 'Thank you', 'subaru-alkhoory' ), __( 'Your enquiry has been received. Our team will get back to you shortly.', 'subaru-alkhoory' ) );
	}
}

/**
 * Resolve a model value (slug or name) to its option value.
 *
 * @param string $value Value.
 * @return string
 */
function subaru_match_model( $value ) {
	$value = strtolower( trim( (string) $value ) );
	if ( '' === $value ) {
		return '';
	}
	foreach ( subaru_models() as $slug => $m ) {
		if ( $value === $slug || strtolower( $m['name'] ) === $value || strtolower( 'subaru ' . $m['name'] ) === $value ) {
			return $m['name'];
		}
	}
	return '';
}

/**
 * Signed timestamp for the time-trap.
 *
 * @return string
 */
function subaru_form_token() {
	$t = time();
	return $t . '.' . substr( wp_hash( 'subaru-form|' . $t ), 0, 16 );
}

/**
 * Render a form.
 *
 * @param string $type  Form type.
 * @param array  $atts  Attributes (model, title, class, source).
 * @return string
 */
function subaru_render_form( $type, $atts = array() ) {
	static $instance = 0;
	++$instance;
	$type   = sanitize_key( $type );
	$fields = subaru_form_fields( $type );
	if ( ! $fields ) {
		return '';
	}
	$atts = wp_parse_args(
		$atts,
		array(
			'model' => '',
			'class' => '',
		)
	);

	// Pre-selection: URL params override shortcode attribute.
	// phpcs:disable WordPress.Security.NonceVerification.Recommended
	$pre_model   = isset( $_GET['model'] ) ? subaru_match_model( sanitize_text_field( wp_unslash( $_GET['model'] ) ) ) : '';
	$pre_model   = $pre_model ? $pre_model : subaru_match_model( $atts['model'] );
	$pre_enquiry = isset( $_GET['enquiry'] ) ? sanitize_key( wp_unslash( $_GET['enquiry'] ) ) : '';
	// phpcs:enable

	$uid     = 'sa-form-' . $instance;
	$is_news = 'newsletter' === $type;
	$classes = 'sa-form sa-form--' . $type . ( $is_news ? ' sa-form--inline' : '' ) . ( $atts['class'] ? ' ' . $atts['class'] : '' );

	ob_start();
	?>
	<form class="<?php echo esc_attr( $classes ); ?>" id="<?php echo esc_attr( $uid ); ?>" method="post" action="<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>" novalidate data-sa-form="<?php echo esc_attr( $type ); ?>">
		<input type="hidden" name="action" value="subaru_form_submit">
		<input type="hidden" name="sa_type" value="<?php echo esc_attr( $type ); ?>">
		<input type="hidden" name="sa_nonce" value="<?php echo esc_attr( wp_create_nonce( 'subaru_form' ) ); ?>">
		<input type="hidden" name="sa_ts" value="<?php echo esc_attr( subaru_form_token() ); ?>">
		<input type="hidden" name="sa_page" value="">
		<div class="sa-hp" aria-hidden="true">
			<label for="<?php echo esc_attr( $uid ); ?>-website"><?php esc_html_e( 'Leave this field empty', 'subaru-alkhoory' ); ?></label>
			<input type="text" id="<?php echo esc_attr( $uid ); ?>-website" name="sa_website" value="" tabindex="-1" autocomplete="off">
		</div>
		<div class="sa-form__grid">
			<?php
			foreach ( $fields as $key => $f ) {
				$value = '';
				if ( 'model' === $key ) {
					$value = $pre_model;
				}
				if ( 'enquiry' === $key && isset( $f['options'][ $pre_enquiry ] ) ) {
					$value = $pre_enquiry;
				}
				subaru_render_field( $uid, $key, $f, $value, $is_news );
				if ( $is_news ) {
					echo '<button type="submit" class="sa-btn sa-btn--sm sa-form__submit"><span class="sa-btn__label">' . esc_html( subaru_form_submit_label( $type ) ) . '</span><span class="sa-spinner" aria-hidden="true"></span></button>';
				}
			}
			?>
		</div>
		<?php if ( ! $is_news ) : ?>
			<div class="sa-form__footer">
				<button type="submit" class="sa-btn sa-form__submit"><span class="sa-btn__label"><?php echo esc_html( subaru_form_submit_label( $type ) ); ?></span><span class="sa-spinner" aria-hidden="true"></span><?php subaru_the_icon( 'arrow', 16 ); ?></button>
				<p class="sa-form__req"><?php esc_html_e( '* Required fields', 'subaru-alkhoory' ); ?></p>
			</div>
		<?php endif; ?>
		<div class="sa-form__status" role="status" aria-live="polite"></div>
	</form>
	<?php
	return trim( ob_get_clean() );
}

/**
 * Render one field.
 *
 * @param string $uid     Form id.
 * @param string $key     Field key.
 * @param array  $f       Definition.
 * @param string $value   Pre-selected value.
 * @param bool   $is_news Inline newsletter variant.
 */
function subaru_render_field( $uid, $key, $f, $value = '', $is_news = false ) {
	$id       = $uid . '-' . $key;
	$required = ! empty( $f['required'] );
	$cls      = 'sa-field sa-field--' . $f['type'] . ( ! empty( $f['half'] ) ? ' is-half' : '' ) . ( $value ? ' is-filled' : '' );
	$err_id   = $id . '-error';
	$hint_id  = $id . '-hint';
	$describe = $err_id . ( ! empty( $f['hint'] ) ? ' ' . $hint_id : '' );
	$req_attr = $required ? ' required aria-required="true"' : '';
	$show_if  = ! empty( $f['show_if'] ) ? ' data-show-if="' . esc_attr( $f['show_if'] ) . '"' : '';
	$label    = esc_html( $f['label'] ) . ( $required && ! $is_news ? '<span class="sa-req" aria-hidden="true">*</span>' : '' );

	echo '<div class="' . esc_attr( $cls ) . '"' . $show_if . '>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped above.

	switch ( $f['type'] ) {
		case 'select':
			echo '<select id="' . esc_attr( $id ) . '" name="' . esc_attr( $key ) . '" aria-describedby="' . esc_attr( $describe ) . '"' . $req_attr . '>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo '<option value=""></option>';
			foreach ( $f['options'] as $ov => $ol ) {
				echo '<option value="' . esc_attr( $ov ) . '"' . selected( (string) $value, (string) $ov, false ) . '>' . esc_html( $ol ) . '</option>';
			}
			echo '</select><label for="' . esc_attr( $id ) . '">' . $label . '</label>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			subaru_the_icon( 'chevron', 16, 'sa-field__chev' );
			break;
		case 'textarea':
			echo '<textarea id="' . esc_attr( $id ) . '" name="' . esc_attr( $key ) . '" rows="4" placeholder=" " maxlength="3000" aria-describedby="' . esc_attr( $describe ) . '"' . $req_attr . '></textarea>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo '<label for="' . esc_attr( $id ) . '">' . $label . '</label>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			break;
		case 'consent':
			echo '<label class="sa-check" for="' . esc_attr( $id ) . '"><input type="checkbox" id="' . esc_attr( $id ) . '" name="' . esc_attr( $key ) . '" value="1" aria-describedby="' . esc_attr( $err_id ) . '"' . $req_attr . '><span class="sa-check__box" aria-hidden="true">'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			subaru_the_icon( 'check', 14 );
			echo '</span><span class="sa-check__text">';
			$privacy = get_privacy_policy_url();
			$privacy = $privacy ? $privacy : home_url( '/privacy-policy/' );
			echo wp_kses(
				str_replace(
					'Privacy Policy',
					'<a href="' . esc_url( $privacy ) . '" target="_blank" rel="noopener">' . esc_html__( 'Privacy Policy', 'subaru-alkhoory' ) . '</a>',
					esc_html( $f['label'] )
				),
				array(
					'a' => array(
						'href'   => true,
						'target' => true,
						'rel'    => true,
					),
				)
			);
			echo ( $required ? '<span class="sa-req" aria-hidden="true">*</span>' : '' ) . '</span></label>';
			break;
		default:
			$type  = $f['type'];
			$extra = '';
			if ( 'date' === $type ) {
				$extra = ' min="' . esc_attr( wp_date( 'Y-m-d', time() + DAY_IN_SECONDS ) ) . '"';
			}
			if ( 'tel' === $type ) {
				$extra = ' inputmode="tel" pattern="^(?:\+?971|00971|0)?\s?5[0-9](?:[\s-]?[0-9]){7}$"';
			}
			if ( 'number' === $type ) {
				$extra = ' inputmode="numeric" min="0" max="2000000" step="1"';
			}
			if ( ! empty( $f['autocomplete'] ) ) {
				$extra .= ' autocomplete="' . esc_attr( $f['autocomplete'] ) . '"';
			}
			echo '<input type="' . esc_attr( $type ) . '" id="' . esc_attr( $id ) . '" name="' . esc_attr( $key ) . '" value="' . esc_attr( $value ) . '" placeholder=" " aria-describedby="' . esc_attr( $describe ) . '"' . $req_attr . $extra . '>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo '<label for="' . esc_attr( $id ) . '"' . ( $is_news ? ' class="screen-reader-text"' : '' ) . '>' . $label . '</label>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			if ( 'date' === $type ) {
				subaru_the_icon( 'calendar', 16, 'sa-field__chev' );
			}
	}
	if ( ! empty( $f['hint'] ) ) {
		echo '<span class="sa-field__hint" id="' . esc_attr( $hint_id ) . '">' . esc_html( $f['hint'] ) . '</span>';
	}
	echo '<span class="sa-field__error" id="' . esc_attr( $err_id ) . '"></span>';
	echo '</div>';
}

/**
 * Validate a UAE mobile number.
 *
 * @param string $v Value.
 * @return bool
 */
function subaru_valid_uae_mobile( $v ) {
	$digits = preg_replace( '/[\s\-()]/', '', (string) $v );
	return (bool) preg_match( '/^(?:\+971|00971|971|0)?5[0-9]{8}$/', $digits );
}

/**
 * Client IP (best effort, used only for rate limiting).
 *
 * @return string
 */
function subaru_client_ip() {
	$ip = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
	return filter_var( $ip, FILTER_VALIDATE_IP ) ? $ip : '0.0.0.0';
}

/**
 * AJAX handler.
 */
function subaru_form_submit() {
	// 1. Nonce.
	if ( ! isset( $_POST['sa_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['sa_nonce'] ) ), 'subaru_form' ) ) {
		wp_send_json_error( array( 'message' => __( 'Your session has expired. Please refresh the page and try again.', 'subaru-alkhoory' ) ), 403 );
	}

	$type   = isset( $_POST['sa_type'] ) ? sanitize_key( wp_unslash( $_POST['sa_type'] ) ) : '';
	$fields = subaru_form_fields( $type );
	if ( ! $fields ) {
		wp_send_json_error( array( 'message' => __( 'Unknown form.', 'subaru-alkhoory' ) ), 400 );
	}

	// 2. Honeypot — pretend success for bots.
	if ( ! empty( $_POST['sa_website'] ) ) {
		$msg = subaru_form_success( $type );
		wp_send_json_success( array( 'title' => $msg[0], 'message' => $msg[1] ) );
	}

	// 3. Time trap (min 3 s after render, max 2 days, signed).
	$token = isset( $_POST['sa_ts'] ) ? sanitize_text_field( wp_unslash( $_POST['sa_ts'] ) ) : '';
	$parts = explode( '.', $token );
	$ts    = isset( $parts[0] ) ? (int) $parts[0] : 0;
	$sig   = isset( $parts[1] ) ? $parts[1] : '';
	if ( ! $ts || ! hash_equals( substr( wp_hash( 'subaru-form|' . $ts ), 0, 16 ), $sig ) || ( time() - $ts ) < 3 || ( time() - $ts ) > 2 * DAY_IN_SECONDS ) {
		wp_send_json_error( array( 'message' => __( 'Please wait a moment and try submitting again.', 'subaru-alkhoory' ) ), 400 );
	}

	// 4. Rate limit: 8 submissions / 10 minutes / IP.
	$rl_key = 'sa_rl_' . md5( subaru_client_ip() );
	$hits   = (int) get_transient( $rl_key );
	if ( $hits >= 8 ) {
		wp_send_json_error( array( 'message' => __( 'Too many submissions. Please try again later.', 'subaru-alkhoory' ) ), 429 );
	}
	set_transient( $rl_key, $hits + 1, 10 * MINUTE_IN_SECONDS );

	// 5. Validate.
	$errors = array();
	$clean  = array();
	$enq    = isset( $_POST['enquiry'] ) ? sanitize_key( wp_unslash( $_POST['enquiry'] ) ) : '';
	foreach ( $fields as $key => $f ) {
		$raw = isset( $_POST[ $key ] ) ? wp_unslash( $_POST[ $key ] ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- sanitised per type below.
		$raw = is_array( $raw ) ? '' : (string) $raw;

		// Conditional fields that are hidden are skipped.
		if ( ! empty( $f['show_if'] ) ) {
			list( $dep, $vals ) = array_pad( explode( ':', $f['show_if'], 2 ), 2, '' );
			if ( 'enquiry' === $dep && ! in_array( $enq, explode( ',', $vals ), true ) ) {
				continue;
			}
		}

		switch ( $f['type'] ) {
			case 'email':
				$val = sanitize_email( $raw );
				if ( '' !== trim( $raw ) && ! is_email( $val ) ) {
					$errors[ $key ] = __( 'Please enter a valid email address.', 'subaru-alkhoory' );
				}
				break;
			case 'tel':
				$val = sanitize_text_field( $raw );
				if ( '' !== $val && ! subaru_valid_uae_mobile( $val ) ) {
					$errors[ $key ] = __( 'Please enter a valid UAE mobile number, e.g. 05X XXX XXXX.', 'subaru-alkhoory' );
				}
				break;
			case 'date':
				$val = sanitize_text_field( $raw );
				if ( '' !== $val ) {
					$d = DateTime::createFromFormat( 'Y-m-d', $val, wp_timezone() );
					if ( ! $d || $d->format( 'Y-m-d' ) !== $val || $val < wp_date( 'Y-m-d', time() + DAY_IN_SECONDS ) ) {
						$errors[ $key ] = __( 'Please choose a date from tomorrow onwards.', 'subaru-alkhoory' );
					}
				}
				break;
			case 'number':
				$val = '' === trim( $raw ) ? '' : (string) absint( $raw );
				break;
			case 'select':
				$val = sanitize_text_field( $raw );
				if ( '' !== $val && ! isset( $f['options'][ $val ] ) ) {
					$errors[ $key ] = __( 'Please choose an option from the list.', 'subaru-alkhoory' );
				}
				break;
			case 'consent':
				$val = $raw ? 'Yes' : '';
				break;
			case 'textarea':
				$val = sanitize_textarea_field( $raw );
				$val = function_exists( 'mb_substr' ) ? mb_substr( $val, 0, 3000 ) : substr( $val, 0, 3000 );
				break;
			default:
				$val = sanitize_text_field( $raw );
				$val = function_exists( 'mb_substr' ) ? mb_substr( $val, 0, 200 ) : substr( $val, 0, 200 );
		}
		if ( ! empty( $f['required'] ) && '' === $val && ! isset( $errors[ $key ] ) ) {
			$errors[ $key ] = 'consent' === $f['type'] ? __( 'Please accept to continue.', 'subaru-alkhoory' ) : __( 'This field is required.', 'subaru-alkhoory' );
		}
		$clean[ $key ] = $val;
	}

	if ( $errors ) {
		wp_send_json_error(
			array(
				'message' => __( 'Please check the highlighted fields.', 'subaru-alkhoory' ),
				'errors'  => $errors,
			),
			422
		);
	}

	$page  = isset( $_POST['sa_page'] ) ? esc_url_raw( wp_unslash( $_POST['sa_page'] ) ) : '';
	$page  = ( $page && 0 === strpos( $page, home_url() ) ) ? $page : '';
	$email = isset( $clean['email'] ) ? $clean['email'] : '';
	$name  = isset( $clean['name'] ) ? $clean['name'] : trim( ( isset( $clean['first_name'] ) ? $clean['first_name'] : '' ) . ' ' . ( isset( $clean['last_name'] ) ? $clean['last_name'] : '' ) );
	$phone = isset( $clean['mobile'] ) ? $clean['mobile'] : '';
	$model = isset( $clean['model'] ) ? $clean['model'] : '';

	// 6. Newsletter dedupe.
	if ( 'newsletter' === $type ) {
		$existing = get_posts(
			array(
				'post_type'      => 'subaru_enquiry',
				'post_status'    => 'any',
				'posts_per_page' => 1,
				'fields'         => 'ids',
				'meta_query'     => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
					'relation' => 'AND',
					array(
						'key'   => '_sa_type',
						'value' => 'newsletter',
					),
					array(
						'key'   => '_sa_email',
						'value' => $email,
					),
				),
			)
		);
		if ( $existing ) {
			wp_send_json_success(
				array(
					'title'   => __( 'Already subscribed', 'subaru-alkhoory' ),
					'message' => __( 'This email is already on our list — thank you!', 'subaru-alkhoory' ),
				)
			);
		}
	}

	// 7. Store.
	$types  = subaru_form_types();
	$labels = array();
	foreach ( $clean as $key => $val ) {
		if ( '' === $val ) {
			continue;
		}
		$label = wp_strip_all_tags( $fields[ $key ]['label'] );
		if ( 'consent' === $fields[ $key ]['type'] ) {
			$label = __( 'Consent', 'subaru-alkhoory' );
		}
		if ( 'select' === $fields[ $key ]['type'] && isset( $fields[ $key ]['options'][ $val ] ) ) {
			$val = $fields[ $key ]['options'][ $val ];
		}
		$labels[ $label ] = $val;
	}
	$title   = $types[ $type ] . ' — ' . ( $name ? $name : $email ) . ( $model ? ' — ' . $model : '' );
	$post_id = wp_insert_post(
		array(
			'post_type'   => 'subaru_enquiry',
			'post_status' => 'pending',
			'post_title'  => wp_strip_all_tags( $title ),
		),
		true
	);
	if ( is_wp_error( $post_id ) ) {
		wp_send_json_error( array( 'message' => __( 'Something went wrong. Please try again or contact us directly.', 'subaru-alkhoory' ) ), 500 );
	}
	update_post_meta( $post_id, '_sa_type', $type );
	update_post_meta( $post_id, '_sa_model', $model );
	update_post_meta( $post_id, '_sa_name', $name );
	update_post_meta( $post_id, '_sa_email', $email );
	update_post_meta( $post_id, '_sa_phone', $phone );
	update_post_meta( $post_id, '_sa_page', $page );
	update_post_meta( $post_id, '_sa_fields', $labels );

	// 8. Emails.
	subaru_form_send_emails( $type, $labels, $email, $name, $page, $post_id );

	do_action( 'subaru_form_submitted', $type, $clean, $post_id );

	$msg = subaru_form_success( $type );
	wp_send_json_success(
		array(
			'title'   => $msg[0],
			'message' => $msg[1],
		)
	);
}
add_action( 'wp_ajax_subaru_form_submit', 'subaru_form_submit' );
add_action( 'wp_ajax_nopriv_subaru_form_submit', 'subaru_form_submit' );

/**
 * Notification + auto-reply emails.
 *
 * @param string $type    Type.
 * @param array  $labels  Label => value.
 * @param string $email   Customer email.
 * @param string $name    Customer name.
 * @param string $page    Source page.
 * @param int    $post_id Enquiry ID.
 */
function subaru_form_send_emails( $type, $labels, $email, $name, $page, $post_id ) {
	$types      = subaru_form_types();
	$recipients = subaru_setting( 'recipient_' . $type );
	$recipients = $recipients ? array_map( 'trim', explode( ',', $recipients ) ) : array( get_option( 'admin_email' ) );
	$from_name  = subaru_setting( 'from_name', get_bloginfo( 'name' ) );
	$site       = wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES );

	$rows = '';
	foreach ( $labels as $label => $val ) {
		$rows .= '<tr><td style="padding:10px 14px;border-bottom:1px solid #e3e7ed;color:#5a6573;width:38%;vertical-align:top">' . esc_html( $label ) . '</td><td style="padding:10px 14px;border-bottom:1px solid #e3e7ed;color:#10151c">' . nl2br( esc_html( $val ) ) . '</td></tr>';
	}
	$admin_link = admin_url( 'post.php?post=' . (int) $post_id . '&action=edit' );
	$body       = subaru_email_wrap(
		/* translators: %s: form type */
		sprintf( __( 'New %s enquiry', 'subaru-alkhoory' ), strtolower( $types[ $type ] ) ),
		'<table style="width:100%;border-collapse:collapse;font-size:15px">' . $rows . '</table>'
		. ( $page ? '<p style="color:#8a94a3;font-size:13px">' . esc_html__( 'Submitted from:', 'subaru-alkhoory' ) . ' <a href="' . esc_url( $page ) . '">' . esc_html( $page ) . '</a></p>' : '' )
		. '<p><a href="' . esc_url( $admin_link ) . '" style="display:inline-block;background:#0160b4;color:#fff;padding:12px 22px;border-radius:999px;text-decoration:none;font-weight:600">' . esc_html__( 'View in WordPress', 'subaru-alkhoory' ) . '</a></p>'
	);

	$headers = array( 'Content-Type: text/html; charset=UTF-8' );
	if ( $email ) {
		$headers[] = 'Reply-To: ' . ( $name ? str_replace( array( "\r", "\n", '"' ), '', $name ) . ' ' : '' ) . '<' . $email . '>';
	}
	/* translators: 1: site name, 2: form type, 3: customer name */
	$subject = sprintf( __( '[%1$s] %2$s enquiry from %3$s', 'subaru-alkhoory' ), $site, $types[ $type ], $name ? $name : $email );
	wp_mail( $recipients, $subject, $body, $headers );

	// Auto-reply.
	if ( $email && subaru_setting( 'autoreply' ) ) {
		$msg   = subaru_form_success( $type );
		$intro = $name
			/* translators: %s: customer name */
			? sprintf( __( 'Dear %s,', 'subaru-alkhoory' ), esc_html( $name ) )
			: __( 'Hello,', 'subaru-alkhoory' );
		$reply = subaru_email_wrap(
			$msg[0],
			'<p>' . $intro . '</p><p>' . esc_html( $msg[1] ) . '</p>'
			. ( 'newsletter' !== $type ? '<p style="color:#5a6573">' . esc_html__( 'A summary of your request:', 'subaru-alkhoory' ) . '</p><table style="width:100%;border-collapse:collapse;font-size:15px">' . $rows . '</table>' : '' )
			. '<p>' . esc_html__( 'Kind regards,', 'subaru-alkhoory' ) . '<br>' . esc_html( $from_name ) . '</p>'
		);
		$reply_headers = array( 'Content-Type: text/html; charset=UTF-8' );
		/* translators: 1: site name, 2: message title */
		wp_mail( $email, sprintf( __( '%1$s — %2$s', 'subaru-alkhoory' ), $site, $msg[0] ), $reply, $reply_headers );
	}
}

/**
 * Simple branded HTML email wrapper.
 *
 * @param string $title Title.
 * @param string $html  Inner HTML (already escaped).
 * @return string
 */
function subaru_email_wrap( $title, $html ) {
	return '<!doctype html><html><body style="margin:0;background:#f4f6f9;font-family:Arial,Helvetica,sans-serif;color:#10151c">'
		. '<div style="max-width:620px;margin:0 auto;padding:32px 16px">'
		. '<div style="background:#07142a;color:#fff;padding:22px 28px;border-radius:16px 16px 0 0;font-size:13px;letter-spacing:2px;text-transform:uppercase">Subaru UAE · Al Khoory Automobiles</div>'
		. '<div style="background:#fff;padding:28px;border-radius:0 0 16px 16px;line-height:1.6">'
		. '<h1 style="font-size:22px;margin:0 0 18px">' . esc_html( $title ) . '</h1>' . $html
		. '</div></div></body></html>';
}

/**
 * From name for theme emails.
 *
 * @param string $name Name.
 * @return string
 */
function subaru_mail_from_name( $name ) {
	if ( 'WordPress' === $name ) {
		return wp_specialchars_decode( subaru_setting( 'from_name', get_bloginfo( 'name' ) ), ENT_QUOTES );
	}
	return $name;
}
add_filter( 'wp_mail_from_name', 'subaru_mail_from_name' );
