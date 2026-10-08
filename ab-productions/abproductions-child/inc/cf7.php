<?php
/**
 * Contact Form 7: enquiry form markup + small tweaks.
 *
 * On activation (and once from wp-admin) the theme creates the
 * "AB Productions Enquiry" form if Contact Form 7 is active and the form does
 * not exist yet. Place it with [contact-form-7 title="AB Productions Enquiry"].
 * The form styles live in assets/css/main.css (.abp-form).
 *
 * @package abp
 */

defined( 'ABSPATH' ) || exit;

const ABP_CF7_TITLE = 'AB Productions Enquiry';

function abp_cf7_form_markup() {
	return <<<'CF7'
<div class="abp-form">
<div class="abp-form__row">
<label class="abp-field"><span>First name</span>[text* first-name autocomplete:given-name placeholder "John"]</label>
<label class="abp-field"><span>Last name</span>[text* last-name autocomplete:family-name placeholder "Smith"]</label>
</div>
<div class="abp-form__row">
<label class="abp-field"><span>Email</span>[email* your-email autocomplete:email placeholder "you@company.com"]</label>
<label class="abp-field"><span>Phone</span>[tel* your-phone autocomplete:tel placeholder "+971"]</label>
</div>
<div class="abp-field"><span>Enquiring as</span>[radio enquiring-as class:abp-segment default:1 "Company" "Individual"]</div>
<label class="abp-field"><span>What can we help you with?</span>[select* service class:abp-select first_as_label "Select a service — Interiors, Events, Rentals, Printing…" "Interior & Fitouts" "Rental Services" "Events Production" "Bespoke Printing" "Bespoke Furniture" "Deep Cleaning" "Landscape Designing" "Something else"]</label>
<label class="abp-field"><span>Project details</span>[textarea project-details x4 placeholder "Tell us about your space, event date, location and budget…"]</label>
[submit class:abp-submit "Send Enquiry →"]
</div>
CF7;
}

function abp_cf7_mail() {
	$to = abp_opt( 'email' );
	return array(
		'active'             => true,
		'subject'            => '[_site_title] New enquiry from [first-name] [last-name]',
		'sender'             => '[_site_title] <wordpress@' . wp_parse_url( home_url(), PHP_URL_HOST ) . '>',
		'recipient'          => $to ? $to : '[_site_admin_email]',
		'body'               => "Name: [first-name] [last-name]\nEmail: [your-email]\nPhone: [your-phone]\nEnquiring as: [enquiring-as]\nService: [service]\n\nProject details:\n[project-details]\n\n-- \nSent from [_url]",
		'additional_headers' => 'Reply-To: [your-email]',
		'attachments'        => '',
		'use_html'           => false,
		'exclude_blank'      => false,
	);
}

function abp_cf7_find_form() {
	$found = get_posts( array( 'post_type' => 'wpcf7_contact_form', 'title' => ABP_CF7_TITLE, 'numberposts' => 1, 'post_status' => 'any' ) );
	return $found ? $found[0] : null;
}

function abp_cf7_install_form() {
	if ( ! class_exists( 'WPCF7_ContactForm' ) || abp_cf7_find_form() ) {
		return;
	}
	$form = WPCF7_ContactForm::get_template( array( 'title' => ABP_CF7_TITLE ) );
	$form->set_properties(
		array(
			'form'     => abp_cf7_form_markup(),
			'mail'     => abp_cf7_mail(),
			'messages' => array_merge(
				$form->prop( 'messages' ),
				array( 'mail_sent_ok' => 'Thank you! Our team will get back to you shortly.' )
			),
		)
	);
	$form->save();
}

add_action( 'after_switch_theme', 'abp_cf7_install_form', 20 );
add_action( 'admin_init', function () {
	if ( current_user_can( 'manage_options' ) && ! get_option( 'abp_cf7_installed' ) && class_exists( 'WPCF7_ContactForm' ) ) {
		abp_cf7_install_form();
		update_option( 'abp_cf7_installed', 1 );
	}
} );

/** CF7 wraps fields in <p>/<br>; the form layout above doesn't need them. */
add_filter( 'wpcf7_autop_or_not', '__return_false' );
