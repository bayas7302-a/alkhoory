<?php
/**
 * Plugin Name:       FIERRO Multi Currency
 * Description:       Shows WooCommerce prices in GCC currencies and USD. Exchange rates update automatically from a free API, and any rate can be overridden manually. Shortcodes: [fierro_currency_switcher], [fierro_currency_rates], [fierro_convert].
 * Version:           1.0.2
 * Author:            FIERRO
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * WC requires at least: 7.0
 * Text Domain:       fierro-mc
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class Fierro_Multi_Currency {

	const OPT_SETTINGS = 'fierro_mc_settings';
	const OPT_RATES    = 'fierro_mc_rates';
	const COOKIE       = 'fierro_currency';
	const QUERY_ARG    = 'fierro_currency';
	const CRON_HOOK    = 'fierro_mc_update_rates';

	/** Rate sources, tried in order. Both are free and need no API key. */
	const SOURCE_PRIMARY  = 'https://open.er-api.com/v6/latest/%s';
	const SOURCE_FALLBACK = 'https://cdn.jsdelivr.net/npm/@fawazahmed0/currency-api@latest/v1/currencies/%s.json';

	/** Country => currency for "auto-select by visitor country". */
	const COUNTRY_MAP = array(
		'AE' => 'AED',
		'SA' => 'SAR',
		'KW' => 'KWD',
		'QA' => 'QAR',
		'BH' => 'BHD',
		'OM' => 'OMR',
		'US' => 'USD',
	);

	private static $instance = null;

	/** Currency picked for this request (null until resolved). */
	private $current = null;

	/** Guard so our own currency lookups never recurse through our filters. */
	private $in_filter = false;

	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		register_activation_hook( __FILE__, array( __CLASS__, 'activate' ) );
		register_deactivation_hook( __FILE__, array( __CLASS__, 'deactivate' ) );

		add_filter( 'cron_schedules', array( $this, 'cron_schedules' ) );
		add_action( self::CRON_HOOK, array( $this, 'update_rates' ) );
		add_action( 'plugins_loaded', array( $this, 'init' ) );
	}

	/* ------------------------------------------------------------------
	 * Setup
	 * ---------------------------------------------------------------- */

	public static function activate() {
		if ( ! get_option( self::OPT_SETTINGS ) ) {
			update_option( self::OPT_SETTINGS, self::default_settings() );
		}
		self::schedule( self::get_settings()['frequency'] );
		self::instance()->update_rates();
	}

	public static function deactivate() {
		wp_clear_scheduled_hook( self::CRON_HOOK );
	}

	private static function schedule( $frequency ) {
		wp_clear_scheduled_hook( self::CRON_HOOK );
		wp_schedule_event( time() + 60, $frequency, self::CRON_HOOK );
	}

	public function cron_schedules( $schedules ) {
		$schedules['fierro_6h'] = array(
			'interval' => 6 * HOUR_IN_SECONDS,
			'display'  => 'Every 6 hours',
		);
		return $schedules;
	}

	public static function default_settings() {
		$row = function ( $symbol, $decimals ) {
			return array(
				'enabled'  => 1,
				'symbol'   => $symbol,
				'decimals' => $decimals,
				'rounding' => 'none',
				'markup'   => 0,
				'override' => 0,
				'manual'   => '',
			);
		};
		return array(
			'default_currency' => '',      // '' = store currency (AED)
			'auto_detect'      => 0,
			'other_countries'  => '',      // '' = store currency
			'frequency'        => 'daily',
			'currencies'       => array(
				'USD' => $row( '$', 2 ),
				'SAR' => $row( 'SAR', 2 ),
				'KWD' => $row( 'KWD', 3 ),
				'QAR' => $row( 'QAR', 2 ),
				'BHD' => $row( 'BHD', 3 ),
				'OMR' => $row( 'OMR', 3 ),
			),
		);
	}

	public static function get_settings() {
		$s = get_option( self::OPT_SETTINGS );
		if ( ! is_array( $s ) ) {
			$s = self::default_settings();
		}
		return wp_parse_args( $s, self::default_settings() );
	}

	public function init() {
		if ( ! class_exists( 'WooCommerce' ) ) {
			add_action( 'admin_notices', function () {
				echo '<div class="notice notice-error"><p><strong>FIERRO Multi Currency</strong> needs WooCommerce to be active.</p></div>';
			} );
			return;
		}

		// Admin.
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		add_action( 'admin_post_fierro_mc_save', array( $this, 'admin_save' ) );
		add_action( 'admin_post_fierro_mc_update', array( $this, 'admin_update_now' ) );

		// Picking the visitor's currency.
		add_action( 'wp_loaded', array( $this, 'handle_switch' ), 20 );

		// Currency + prices.
		add_filter( 'woocommerce_currency', array( $this, 'filter_currency' ), 999 );
		add_filter( 'woocommerce_currency_symbol', array( $this, 'filter_symbol' ), 9999, 2 );
		add_filter( 'wc_get_price_decimals', array( $this, 'filter_decimals' ), 999 );

		foreach ( array( 'price', 'regular_price', 'sale_price' ) as $type ) {
			add_filter( "woocommerce_product_get_{$type}", array( $this, 'convert_product_price' ), 999, 2 );
			add_filter( "woocommerce_product_variation_get_{$type}", array( $this, 'convert_product_price' ), 999, 2 );
			add_filter( "woocommerce_variation_prices_{$type}", array( $this, 'convert_product_price' ), 999, 2 );
		}
		add_filter( 'woocommerce_get_variation_prices_hash', array( $this, 'variation_hash' ), 999 );

		// Shipping, free-shipping threshold, coupons.
		add_filter( 'woocommerce_package_rates', array( $this, 'convert_shipping_rates' ), 999 );
		add_filter( 'woocommerce_shipping_free_shipping_instance_option', array( $this, 'convert_free_shipping_min' ), 999, 3 );
		add_filter( 'woocommerce_coupon_get_amount', array( $this, 'convert_coupon_amount' ), 999, 2 );
		add_filter( 'woocommerce_coupon_get_minimum_amount', array( $this, 'convert_amount_filter' ), 999 );
		add_filter( 'woocommerce_coupon_get_maximum_amount', array( $this, 'convert_amount_filter' ), 999 );

		// Keep page caches (WP-Optimize) separate per currency.
		add_filter( 'wpo_cache_cookies', array( $this, 'cache_cookies' ) );

		// Shortcodes + front-end helper.
		add_shortcode( 'fierro_currency_switcher', array( $this, 'sc_switcher' ) );
		add_shortcode( 'fierro_currency_rates', array( $this, 'sc_rates' ) );
		add_shortcode( 'fierro_convert', array( $this, 'sc_convert' ) );
		add_action( 'wp_footer', array( $this, 'footer_script' ) );
	}

	/* ------------------------------------------------------------------
	 * Rates
	 * ---------------------------------------------------------------- */

	/** Store currency as configured in WooCommerce (AED), never filtered. */
	public function base_currency() {
		return strtoupper( get_option( 'woocommerce_currency', 'AED' ) );
	}

	/**
	 * Fetch rates from the free API and save them.
	 * Rates are stored as "1 base = X currency" for every currency the API returns,
	 * so currencies added later have a live rate straight away.
	 */
	public function update_rates() {
		$base  = $this->base_currency();
		$rates = $this->fetch_primary( $base );
		$src   = 'open.er-api.com';
		if ( empty( $rates ) ) {
			$rates = $this->fetch_fallback( $base );
			$src   = 'fawazahmed0 currency-api';
		}

		$stored = get_option( self::OPT_RATES, array() );
		if ( empty( $rates ) ) {
			$stored['last_error'] = gmdate( 'Y-m-d H:i' ) . ' UTC: both rate sources failed, keeping the previous rates.';
			update_option( self::OPT_RATES, $stored, false );
			return false;
		}

		update_option(
			self::OPT_RATES,
			array(
				'base'       => $base,
				'rates'      => $rates,
				'source'     => $src,
				'updated'    => time(),
				'last_error' => '',
			),
			false
		);
		$this->flush_price_caches();
		return true;
	}

	private function fetch_primary( $base ) {
		$res = wp_remote_get( sprintf( self::SOURCE_PRIMARY, rawurlencode( $base ) ), array( 'timeout' => 15 ) );
		if ( is_wp_error( $res ) || 200 !== wp_remote_retrieve_response_code( $res ) ) {
			return array();
		}
		$data = json_decode( wp_remote_retrieve_body( $res ), true );
		if ( empty( $data['rates'] ) || ( isset( $data['result'] ) && 'success' !== $data['result'] ) ) {
			return array();
		}
		return $this->clean_rates( $data['rates'] );
	}

	private function fetch_fallback( $base ) {
		$key = strtolower( $base );
		$res = wp_remote_get( sprintf( self::SOURCE_FALLBACK, rawurlencode( $key ) ), array( 'timeout' => 15 ) );
		if ( is_wp_error( $res ) || 200 !== wp_remote_retrieve_response_code( $res ) ) {
			return array();
		}
		$data = json_decode( wp_remote_retrieve_body( $res ), true );
		if ( empty( $data[ $key ] ) || ! is_array( $data[ $key ] ) ) {
			return array();
		}
		return $this->clean_rates( array_change_key_case( $data[ $key ], CASE_UPPER ) );
	}

	private function clean_rates( $raw ) {
		$out = array();
		foreach ( $raw as $code => $rate ) {
			$code = strtoupper( (string) $code );
			if ( preg_match( '/^[A-Z]{3}$/', $code ) && is_numeric( $rate ) && $rate > 0 ) {
				$out[ $code ] = (float) $rate;
			}
		}
		return $out;
	}

	/** Live API rate for a currency (1 base = X), or 0 if unknown. */
	public function live_rate( $code ) {
		$r = get_option( self::OPT_RATES, array() );
		return isset( $r['rates'][ $code ] ) ? (float) $r['rates'][ $code ] : 0.0;
	}

	/** Rate actually used: manual override if switched on, else live; markup applied. */
	public function effective_rate( $code ) {
		if ( $code === $this->base_currency() ) {
			return 1.0;
		}
		$s   = self::get_settings();
		$cfg = isset( $s['currencies'][ $code ] ) ? $s['currencies'][ $code ] : null;
		if ( ! $cfg ) {
			return 0.0;
		}
		$rate = ( ! empty( $cfg['override'] ) && (float) $cfg['manual'] > 0 ) ? (float) $cfg['manual'] : $this->live_rate( $code );
		if ( $rate <= 0 ) {
			return 0.0;
		}
		return $rate * ( 1 + (float) $cfg['markup'] / 100 );
	}

	/* ------------------------------------------------------------------
	 * Which currency is this visitor using?
	 * ---------------------------------------------------------------- */

	/** Currencies a visitor may pick: base + enabled ones that have a rate. */
	public function available_currencies() {
		$list = array( $this->base_currency() );
		foreach ( self::get_settings()['currencies'] as $code => $cfg ) {
			if ( ! empty( $cfg['enabled'] ) && $this->effective_rate( $code ) > 0 ) {
				$list[] = $code;
			}
		}
		return array_values( array_unique( $list ) );
	}

	private function is_valid_choice( $code ) {
		return in_array( $code, $this->available_currencies(), true );
	}

	/**
	 * Only convert for shoppers: never in wp-admin screens, cron, or the
	 * admin/WooCommerce management REST API (orders, products, reports stay in AED).
	 */
	private function is_shop_request() {
		if ( wp_doing_cron() ) {
			return false;
		}
		if ( is_admin() && ! wp_doing_ajax() ) {
			return false;
		}
		if ( wp_doing_ajax() ) {
			$ref = isset( $_SERVER['HTTP_REFERER'] ) ? (string) $_SERVER['HTTP_REFERER'] : '';
			if ( false !== strpos( $ref, '/wp-admin/' ) ) {
				return false;
			}
		}
		$uri = isset( $_SERVER['REQUEST_URI'] ) ? (string) $_SERVER['REQUEST_URI'] : '';
		if ( ( defined( 'REST_REQUEST' ) && REST_REQUEST ) || false !== strpos( $uri, '/wp-json/' ) ) {
			return false !== strpos( $uri, '/wc/store' ); // Store API (cart/checkout blocks) only.
		}
		return true;
	}

	public function current_currency() {
		if ( null !== $this->current ) {
			return $this->current;
		}
		$base = $this->base_currency();
		if ( ! $this->is_shop_request() ) {
			return $base; // not cached: admin context can't change mid-request in practice
		}

		$this->in_filter = true;
		$code            = '';
		if ( ! empty( $_COOKIE[ self::COOKIE ] ) ) {
			$code = strtoupper( sanitize_text_field( wp_unslash( $_COOKIE[ self::COOKIE ] ) ) );
		}
		if ( ! $code || ! $this->is_valid_choice( $code ) ) {
			$code = $this->initial_currency();
		}
		$this->in_filter = false;

		$this->current = $code;
		return $code;
	}

	/** First visit: by country (if switched on), else the default currency. */
	private function initial_currency() {
		$s    = self::get_settings();
		$base = $this->base_currency();

		if ( ! empty( $s['auto_detect'] ) && class_exists( 'WC_Geolocation' ) ) {
			$geo     = WC_Geolocation::geolocate_ip( '', true, true );
			$country = isset( $geo['country'] ) ? strtoupper( $geo['country'] ) : '';
			if ( $country ) {
				$code = isset( self::COUNTRY_MAP[ $country ] ) ? self::COUNTRY_MAP[ $country ] : ( $s['other_countries'] ? $s['other_countries'] : $base );
				if ( $this->is_valid_choice( $code ) ) {
					return $code;
				}
			}
		}
		if ( $s['default_currency'] && $this->is_valid_choice( $s['default_currency'] ) ) {
			return $s['default_currency'];
		}
		return $base;
	}

	/** ?fierro_currency=USD switches currency, then redirects to a clean URL. */
	public function handle_switch() {
		if ( is_admin() || ! isset( $_GET[ self::QUERY_ARG ] ) ) {
			// Remember the first automatic choice so caches and later pages agree.
			if ( empty( $_COOKIE[ self::COOKIE ] ) && ! wp_doing_ajax() && ! headers_sent() && $this->is_shop_request() && ! ( defined( 'REST_REQUEST' ) && REST_REQUEST ) ) {
				$code = $this->current_currency();
				$this->set_cookie( $code );
			}
			return;
		}
		$code = strtoupper( sanitize_text_field( wp_unslash( $_GET[ self::QUERY_ARG ] ) ) );
		if ( $this->is_valid_choice( $code ) ) {
			$this->set_cookie( $code );
			$this->current = $code;
			if ( function_exists( 'WC' ) && WC()->cart ) {
				WC()->cart->calculate_shipping();
				WC()->cart->calculate_totals();
			}
		}
		wp_safe_redirect( remove_query_arg( self::QUERY_ARG ) );
		exit;
	}

	private function set_cookie( $code ) {
		$_COOKIE[ self::COOKIE ] = $code;
		if ( ! headers_sent() ) {
			setcookie( self::COOKIE, $code, time() + 30 * DAY_IN_SECONDS, COOKIEPATH ? COOKIEPATH : '/', COOKIE_DOMAIN, is_ssl(), false );
		}
	}

	public function cache_cookies( $cookies ) {
		$cookies   = (array) $cookies;
		$cookies[] = self::COOKIE;
		return array_unique( $cookies );
	}

	/* ------------------------------------------------------------------
	 * WooCommerce filters
	 * ---------------------------------------------------------------- */

	public function filter_currency( $currency ) {
		if ( $this->in_filter ) {
			return $currency;
		}
		return $this->current_currency();
	}

	public function filter_symbol( $symbol, $currency ) {
		if ( $currency === $this->base_currency() ) {
			return $symbol; // keep the theme's Dirham symbol
		}
		$s = self::get_settings();
		if ( ! empty( $s['currencies'][ $currency ]['symbol'] ) ) {
			$sym = $s['currencies'][ $currency ]['symbol'];
			// Letter symbols ("SAR") read better with a space: "SAR 100.00"; "$100.00" doesn't need one.
			return preg_match( '/[A-Za-z]$/', $sym ) ? $sym . '&nbsp;' : $sym;
		}
		return $symbol;
	}

	public function filter_decimals( $decimals ) {
		$code = $this->current_currency();
		$s    = self::get_settings();
		if ( $code !== $this->base_currency() && isset( $s['currencies'][ $code ]['decimals'] ) ) {
			return (int) $s['currencies'][ $code ]['decimals'];
		}
		return $decimals;
	}

	/** True when prices should be converted for this request. */
	private function converting() {
		return ! $this->in_filter && $this->current_currency() !== $this->base_currency();
	}

	/** Convert an amount from the store currency, without rounding rules (shipping, coupons). */
	public function convert_raw( $amount, $code = null ) {
		$code = $code ? $code : $this->current_currency();
		$rate = $this->effective_rate( $code );
		if ( '' === $amount || null === $amount || ! is_numeric( $amount ) || $rate <= 0 ) {
			return $amount;
		}
		return (float) $amount * $rate;
	}

	/** Convert a product price, applying the currency's rounding rule. */
	public function convert_price( $amount, $code = null ) {
		$code      = $code ? $code : $this->current_currency();
		$converted = $this->convert_raw( $amount, $code );
		if ( ! is_float( $converted ) ) {
			return $converted;
		}
		$s    = self::get_settings();
		$mode = isset( $s['currencies'][ $code ]['rounding'] ) ? $s['currencies'][ $code ]['rounding'] : 'none';
		switch ( $mode ) {
			case 'nearest_05':
				return round( $converted * 2 ) / 2;
			case 'nearest_1':
				return round( $converted );
			case 'up_1':
				return ceil( $converted );
			case 'end_99':
				return max( 0, ceil( $converted ) - 0.01 );
			case 'nearest_5':
				return round( $converted / 5 ) * 5;
			default:
				$dec = isset( $s['currencies'][ $code ]['decimals'] ) ? (int) $s['currencies'][ $code ]['decimals'] : 2;
				return round( $converted, $dec );
		}
	}

	public function convert_product_price( $price, $product = null ) {
		if ( ! $this->converting() || '' === $price || null === $price ) {
			return $price;
		}
		return $this->convert_price( $price );
	}

	public function variation_hash( $hash ) {
		$code   = $this->current_currency();
		$hash[] = $code . ':' . $this->effective_rate( $code );
		return $hash;
	}

	public function convert_shipping_rates( $rates ) {
		if ( ! $this->converting() ) {
			return $rates;
		}
		foreach ( $rates as $rate ) {
			if ( ! is_object( $rate ) || ! method_exists( $rate, 'set_cost' ) ) {
				continue;
			}
			$rate->set_cost( $this->convert_raw( $rate->get_cost() ) );
			$taxes = $rate->get_taxes();
			if ( is_array( $taxes ) ) {
				foreach ( $taxes as $k => $t ) {
					$taxes[ $k ] = $this->convert_raw( $t );
				}
				$rate->set_taxes( $taxes );
			}
		}
		return $rates;
	}

	public function convert_free_shipping_min( $value, $key, $method = null ) {
		if ( 'min_amount' !== $key || ! $this->converting() ) {
			return $value;
		}
		return $this->convert_raw( $value );
	}

	public function convert_coupon_amount( $amount, $coupon ) {
		if ( ! $this->converting() || ! is_object( $coupon ) || $coupon->is_type( array( 'percent' ) ) ) {
			return $amount;
		}
		return $this->convert_raw( $amount );
	}

	public function convert_amount_filter( $amount ) {
		if ( ! $this->converting() ) {
			return $amount;
		}
		return $this->convert_raw( $amount );
	}

	/* ------------------------------------------------------------------
	 * Shortcodes
	 * ---------------------------------------------------------------- */

	/**
	 * [fierro_currency_switcher style="dropdown|buttons" label="Currency"]
	 */
	public function sc_switcher( $atts ) {
		$atts    = shortcode_atts( array( 'style' => 'dropdown', 'label' => '' ), $atts, 'fierro_currency_switcher' );
		$current = $this->current_currency();
		$codes   = $this->available_currencies();
		$url     = function ( $code ) {
			return esc_url( add_query_arg( self::QUERY_ARG, $code ) );
		};

		ob_start();
		echo '<div class="fierro-mc-switcher fierro-mc-' . esc_attr( $atts['style'] ) . '">';
		if ( $atts['label'] ) {
			echo '<span class="fierro-mc-label">' . esc_html( $atts['label'] ) . '</span> ';
		}
		if ( 'buttons' === $atts['style'] ) {
			foreach ( $codes as $code ) {
				printf(
					'<a href="%s" class="fierro-mc-btn%s" rel="nofollow">%s</a>',
					$url( $code ), // already escaped
					$code === $current ? ' is-active' : '',
					esc_html( $code )
				);
			}
		} else {
			echo '<select class="fierro-mc-select" aria-label="Currency" onchange="if(this.value){window.location=this.value;}">';
			foreach ( $codes as $code ) {
				printf( '<option value="%s"%s>%s</option>', $url( $code ), selected( $code, $current, false ), esc_html( $code ) );
			}
			echo '</select>';
		}
		echo '</div>';
		echo '<style>
			/* Closed: no background, no border, sharp edges. White text on desktop, black on mobile. */
			.fierro-mc-switcher{display:inline-flex;align-items:center;gap:6px;font-family:"Jost",sans-serif;font-weight:300;color:#ffffff}
			.fierro-mc-switcher .fierro-mc-select{-webkit-appearance:none;-moz-appearance:none;appearance:none;font:inherit;font-size:14px;line-height:1.2;color:#ffffff;background-color:transparent;background-image:url("data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%2210%22 height=%226%22 viewBox=%220 0 10 6%22%3E%3Cpath d=%22M1 1l4 4 4-4%22 fill=%22none%22 stroke=%22%23ffffff%22 stroke-width=%221.2%22/%3E%3C/svg%3E");background-repeat:no-repeat;background-position:right 2px center;background-size:10px 6px;border:0;border-radius:0;box-shadow:none;outline:none;padding:2px 16px 2px 0;margin:0;height:auto;min-height:0;width:auto;cursor:pointer}
			/* Open list: solid background */
			.fierro-mc-switcher .fierro-mc-select option{background:#ffffff;color:#222222}
			.fierro-mc-btn{padding:3px 8px;border:0.5px solid currentColor;border-radius:0;color:inherit;background:transparent;text-decoration:none;font-size:13px}
			.fierro-mc-btn.is-active{background:#222;color:#fff;border-color:#222}
			@media (max-width:767px){
				.fierro-mc-switcher{color:#000000}
				.fierro-mc-switcher .fierro-mc-select{color:#000000;background-image:url("data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%2210%22 height=%226%22 viewBox=%220 0 10 6%22%3E%3Cpath d=%22M1 1l4 4 4-4%22 fill=%22none%22 stroke=%22%23000000%22 stroke-width=%221.2%22/%3E%3C/svg%3E")}
			}
		</style>';
		return ob_get_clean();
	}

	/**
	 * [fierro_currency_rates amount="1" currencies="USD,SAR" show_updated="yes"]
	 * Table of today's exchange rates from the store currency.
	 */
	public function sc_rates( $atts ) {
		$atts  = shortcode_atts( array( 'amount' => 1, 'currencies' => '', 'show_updated' => 'yes' ), $atts, 'fierro_currency_rates' );
		$base  = $this->base_currency();
		$codes = $atts['currencies'] ? array_map( 'trim', explode( ',', strtoupper( $atts['currencies'] ) ) ) : $this->available_currencies();
		$codes = array_values( array_diff( $codes, array( $base ) ) );
		$amt   = (float) $atts['amount'] > 0 ? (float) $atts['amount'] : 1;
		$names = function_exists( 'get_woocommerce_currencies' ) ? get_woocommerce_currencies() : array();
		$s     = self::get_settings();

		ob_start();
		echo '<table class="fierro-mc-rates"><thead><tr><th>Currency</th><th>' . esc_html( $this->format_number( $amt, 2 ) . ' ' . $base ) . ' =</th></tr></thead><tbody>';
		foreach ( $codes as $code ) {
			$rate = $this->effective_rate( $code );
			if ( $rate <= 0 ) {
				continue;
			}
			$dec = isset( $s['currencies'][ $code ]['decimals'] ) ? max( 2, (int) $s['currencies'][ $code ]['decimals'] ) : 2;
			printf(
				'<tr><td>%s</td><td>%s %s</td></tr>',
				esc_html( isset( $names[ $code ] ) ? $names[ $code ] . ' (' . $code . ')' : $code ),
				esc_html( $this->format_number( $amt * $rate, $amt * $rate < 1 ? $dec + 2 : $dec ) ),
				esc_html( $code )
			);
		}
		echo '</tbody></table>';
		$r = get_option( self::OPT_RATES, array() );
		if ( 'yes' === $atts['show_updated'] && ! empty( $r['updated'] ) ) {
			echo '<p class="fierro-mc-updated">Rates updated ' . esc_html( wp_date( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), (int) $r['updated'] ) ) . '</p>';
		}
		echo '<style>
			.fierro-mc-rates{width:100%;max-width:420px;border-collapse:collapse;font-family:"Jost",sans-serif;font-weight:300}
			.fierro-mc-rates th,.fierro-mc-rates td{padding:8px 10px;border-bottom:0.5px solid #e2e2e2;text-align:left;color:#222}
			.fierro-mc-rates th{font-weight:400;color:#6d6e71;text-transform:uppercase;font-size:13px}
			.fierro-mc-rates td:last-child,.fierro-mc-rates th:last-child{text-align:right}
			.fierro-mc-updated{font-family:"Jost",sans-serif;font-weight:300;font-size:12px;color:#6d6e71}
		</style>';
		return ob_get_clean();
	}

	/**
	 * [fierro_convert amount="150"] -> the amount in the visitor's currency, e.g. "$40.84".
	 * Handy for text such as "Free delivery above [fierro_convert amount=150]".
	 */
	public function sc_convert( $atts ) {
		$atts = shortcode_atts( array( 'amount' => 0, 'color' => '' ), $atts, 'fierro_convert' );
		$amt  = (float) $atts['amount'];
		$code = $this->current_currency();
		$base = $this->base_currency();
		$val  = $code === $base ? $amt : $this->convert_raw( $amt, $code );

		// Whole amounts without decimals ("150"), otherwise the currency's decimals ("40.84").
		$s   = self::get_settings();
		$dec = isset( $s['currencies'][ $code ]['decimals'] ) ? (int) $s['currencies'][ $code ]['decimals'] : 2;
		$num = abs( $val - round( $val ) ) < 0.005 ? $this->format_number( round( $val ), 0 ) : $this->format_number( $val, $dec );

		if ( 'AED' === $code ) {
			// Same Dirham image the theme uses; color="white" inverts it for dark bars.
			$style = 'height:.75em;width:auto;vertical-align:-0.05em;display:inline-block;margin:0 1px';
			if ( 'white' === strtolower( $atts['color'] ) ) {
				$style .= ';filter:invert(1)';
			}
			$symbol = '<img decoding="async" src="https://upload.wikimedia.org/wikipedia/commons/e/ee/UAE_Dirham_Symbol.svg" alt="AED" class="aed-symbol" style="' . esc_attr( $style ) . '">';
			return '<span class="fierro-convert">' . $symbol . ' ' . esc_html( $num ) . '</span>';
		}

		$symbol = ! empty( $s['currencies'][ $code ]['symbol'] ) ? $s['currencies'][ $code ]['symbol'] : $code;
		$sep    = preg_match( '/[A-Za-z]$/', $symbol ) ? ' ' : '';
		return '<span class="fierro-convert">' . esc_html( $symbol . $sep . $num ) . '</span>';
	}

	private function format_number( $n, $dec ) {
		return number_format( (float) $n, (int) $dec, '.', ',' );
	}

	/**
	 * After a currency change, drop WooCommerce's cached mini-cart so it
	 * re-renders in the new currency.
	 */
	public function footer_script() {
		if ( is_admin() ) {
			return;
		}
		?>
		<script>
		(function(){
			try {
				var m = document.cookie.match(/(?:^|;\s*)<?php echo esc_js( self::COOKIE ); ?>=([A-Z]{3})/);
				var cur = m ? m[1] : '';
				var last = localStorage.getItem('fierro_mc_last');
				if (cur && last && cur !== last) {
					for (var i = sessionStorage.length - 1; i >= 0; i--) {
						var k = sessionStorage.key(i);
						if (k && (k.indexOf('wc_fragments') === 0 || k.indexOf('wc_cart_hash') === 0)) { sessionStorage.removeItem(k); }
					}
					if (window.jQuery) { jQuery(function($){ $(document.body).trigger('wc_fragment_refresh'); }); }
				}
				if (cur) { localStorage.setItem('fierro_mc_last', cur); }
			} catch (e) {}
		})();
		</script>
		<?php
	}

	/* ------------------------------------------------------------------
	 * Admin page: WooCommerce -> FIERRO Currency
	 * ---------------------------------------------------------------- */

	public function admin_menu() {
		add_submenu_page( 'woocommerce', 'FIERRO Currency', 'FIERRO Currency', 'manage_woocommerce', 'fierro-currency', array( $this, 'admin_page' ) );
	}

	public function admin_page() {
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			return;
		}
		$s     = self::get_settings();
		$r     = get_option( self::OPT_RATES, array() );
		$base  = $this->base_currency();
		$names = get_woocommerce_currencies();
		$next  = wp_next_scheduled( self::CRON_HOOK );
		$round = array(
			'none'       => 'No rounding',
			'nearest_05' => 'Nearest 0.50',
			'nearest_1'  => 'Nearest 1',
			'up_1'       => 'Up to next 1',
			'end_99'     => 'End in .99',
			'nearest_5'  => 'Nearest 5',
		);
		?>
		<div class="wrap">
			<h1>FIERRO Multi Currency</h1>

			<?php if ( isset( $_GET['fierro_msg'] ) ) : ?>
				<div class="notice notice-success is-dismissible"><p><?php echo esc_html( sanitize_text_field( wp_unslash( $_GET['fierro_msg'] ) ) ); ?></p></div>
			<?php endif; ?>
			<?php if ( ! empty( $r['last_error'] ) ) : ?>
				<div class="notice notice-warning"><p><?php echo esc_html( $r['last_error'] ); ?></p></div>
			<?php endif; ?>

			<p>
				Store (base) currency: <strong><?php echo esc_html( $base ); ?></strong>. Product prices stay saved in <?php echo esc_html( $base ); ?> and are converted for shoppers.<br>
				Live rates: <strong><?php echo ! empty( $r['updated'] ) ? esc_html( wp_date( 'Y-m-d H:i', (int) $r['updated'] ) . ' from ' . $r['source'] ) : 'not fetched yet'; ?></strong>
				<?php if ( $next ) : ?> · next automatic update: <?php echo esc_html( wp_date( 'Y-m-d H:i', $next ) ); ?><?php endif; ?>
			</p>
			<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" style="margin-bottom:20px">
				<?php wp_nonce_field( 'fierro_mc_update' ); ?>
				<input type="hidden" name="action" value="fierro_mc_update">
				<button class="button">Update rates now</button>
			</form>

			<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
				<?php wp_nonce_field( 'fierro_mc_save' ); ?>
				<input type="hidden" name="action" value="fierro_mc_save">

				<h2>Currencies</h2>
				<p>Turn on <em>Override</em> to use your own rate instead of the automatic one. The automatic rate keeps updating in the background, so you can switch back at any time.</p>
				<table class="widefat striped" style="max-width:1200px">
					<thead><tr>
						<th>Enabled</th><th>Currency</th><th>Symbol</th><th>Decimals</th><th>Price rounding</th><th>Markup %</th>
						<th>Live rate<br><small>1 <?php echo esc_html( $base ); ?> =</small></th><th>Override</th><th>Manual rate<br><small>1 <?php echo esc_html( $base ); ?> =</small></th><th>Rate in use</th><th>Remove</th>
					</tr></thead>
					<tbody>
					<?php foreach ( $s['currencies'] as $code => $c ) : $f = 'currencies[' . esc_attr( $code ) . ']'; ?>
						<tr>
							<td><input type="checkbox" name="<?php echo $f; ?>[enabled]" value="1" <?php checked( ! empty( $c['enabled'] ) ); ?>></td>
							<td><strong><?php echo esc_html( $code ); ?></strong><br><small><?php echo esc_html( isset( $names[ $code ] ) ? $names[ $code ] : '' ); ?></small></td>
							<td><input type="text" name="<?php echo $f; ?>[symbol]" value="<?php echo esc_attr( $c['symbol'] ); ?>" size="5"></td>
							<td><input type="number" name="<?php echo $f; ?>[decimals]" value="<?php echo esc_attr( $c['decimals'] ); ?>" min="0" max="4" style="width:60px"></td>
							<td><select name="<?php echo $f; ?>[rounding]"><?php foreach ( $round as $k => $label ) : ?><option value="<?php echo esc_attr( $k ); ?>" <?php selected( $c['rounding'], $k ); ?>><?php echo esc_html( $label ); ?></option><?php endforeach; ?></select></td>
							<td><input type="number" step="0.01" name="<?php echo $f; ?>[markup]" value="<?php echo esc_attr( $c['markup'] ); ?>" style="width:70px"></td>
							<td><?php $lr = $this->live_rate( $code ); echo $lr ? esc_html( rtrim( rtrim( number_format( $lr, 6, '.', '' ), '0' ), '.' ) ) : '<em>n/a</em>'; ?></td>
							<td><input type="checkbox" name="<?php echo $f; ?>[override]" value="1" <?php checked( ! empty( $c['override'] ) ); ?>></td>
							<td><input type="number" step="0.000001" min="0" name="<?php echo $f; ?>[manual]" value="<?php echo esc_attr( $c['manual'] ); ?>" style="width:110px"></td>
							<td><?php $er = $this->effective_rate( $code ); echo $er ? esc_html( rtrim( rtrim( number_format( $er, 6, '.', '' ), '0' ), '.' ) ) : '<em>none</em>'; ?></td>
							<td><input type="checkbox" name="remove[]" value="<?php echo esc_attr( $code ); ?>"></td>
						</tr>
					<?php endforeach; ?>
						<tr>
							<td colspan="11">
								<strong>Add a currency:</strong>
								<input type="text" name="new_code" placeholder="e.g. EUR" maxlength="3" size="6" style="text-transform:uppercase">
								<span class="description">3-letter ISO code. It gets a live rate automatically; set a manual rate if the API doesn't list it.</span>
							</td>
						</tr>
					</tbody>
				</table>

				<h2>Visitors</h2>
				<table class="form-table">
					<tr><th>Default currency</th><td>
						<select name="default_currency">
							<option value="">Store currency (<?php echo esc_html( $base ); ?>)</option>
							<?php foreach ( array_keys( $s['currencies'] ) as $code ) : ?><option value="<?php echo esc_attr( $code ); ?>" <?php selected( $s['default_currency'], $code ); ?>><?php echo esc_html( $code ); ?></option><?php endforeach; ?>
						</select>
						<p class="description">Used for first-time visitors when auto-select is off or the country isn't recognised.</p>
					</td></tr>
					<tr><th>Auto-select by country</th><td>
						<label><input type="checkbox" name="auto_detect" value="1" <?php checked( ! empty( $s['auto_detect'] ) ); ?>> Pick the currency from the visitor's country on their first visit (UAE→AED, Saudi→SAR, Kuwait→KWD, Qatar→QAR, Bahrain→BHD, Oman→OMR, USA→USD)</label>
						<p class="description">Uses WooCommerce geolocation (WooCommerce → Settings → General → Default customer location = "Geolocate"). Visitors can always change it with the switcher.</p>
					</td></tr>
					<tr><th>Other countries</th><td>
						<select name="other_countries">
							<option value="">Store currency (<?php echo esc_html( $base ); ?>)</option>
							<?php foreach ( array_keys( $s['currencies'] ) as $code ) : ?><option value="<?php echo esc_attr( $code ); ?>" <?php selected( $s['other_countries'], $code ); ?>><?php echo esc_html( $code ); ?></option><?php endforeach; ?>
						</select>
					</td></tr>
					<tr><th>Automatic update</th><td>
						<select name="frequency">
							<?php foreach ( array( 'hourly' => 'Every hour', 'fierro_6h' => 'Every 6 hours', 'twicedaily' => 'Twice a day', 'daily' => 'Once a day' ) as $k => $label ) : ?>
								<option value="<?php echo esc_attr( $k ); ?>" <?php selected( $s['frequency'], $k ); ?>><?php echo esc_html( $label ); ?></option>
							<?php endforeach; ?>
						</select>
						<p class="description">The free API refreshes about once a day, so "Once a day" is enough.</p>
					</td></tr>
				</table>

				<?php submit_button( 'Save settings' ); ?>
			</form>

			<h2>Shortcodes</h2>
			<ul style="list-style:disc;margin-left:20px">
				<li><code>[fierro_currency_switcher]</code> – currency dropdown. <code>[fierro_currency_switcher style="buttons"]</code> for buttons.</li>
				<li><code>[fierro_currency_rates]</code> – table of today's exchange rates. Options: <code>amount="100"</code>, <code>currencies="USD,SAR"</code>, <code>show_updated="no"</code>.</li>
				<li><code>[fierro_convert amount="150"]</code> – an <?php echo esc_html( $base ); ?> amount shown in the visitor's currency.</li>
			</ul>
		</div>
		<?php
	}

	public function admin_save() {
		if ( ! current_user_can( 'manage_woocommerce' ) || ! check_admin_referer( 'fierro_mc_save' ) ) {
			wp_die( 'Not allowed' );
		}
		$old  = self::get_settings();
		$base = $this->base_currency();
		$in   = isset( $_POST['currencies'] ) && is_array( $_POST['currencies'] ) ? wp_unslash( $_POST['currencies'] ) : array();
		$rm   = isset( $_POST['remove'] ) ? array_map( 'sanitize_text_field', (array) wp_unslash( $_POST['remove'] ) ) : array();
		$ok_r = array( 'none', 'nearest_05', 'nearest_1', 'up_1', 'end_99', 'nearest_5' );

		$cur = array();
		foreach ( $old['currencies'] as $code => $c ) {
			if ( in_array( $code, $rm, true ) ) {
				continue;
			}
			$p            = isset( $in[ $code ] ) ? $in[ $code ] : array();
			$cur[ $code ] = array(
				'enabled'  => empty( $p['enabled'] ) ? 0 : 1,
				'symbol'   => isset( $p['symbol'] ) ? sanitize_text_field( $p['symbol'] ) : $c['symbol'],
				'decimals' => isset( $p['decimals'] ) ? max( 0, min( 4, (int) $p['decimals'] ) ) : $c['decimals'],
				'rounding' => isset( $p['rounding'] ) && in_array( $p['rounding'], $ok_r, true ) ? $p['rounding'] : 'none',
				'markup'   => isset( $p['markup'] ) ? (float) $p['markup'] : 0,
				'override' => empty( $p['override'] ) ? 0 : 1,
				'manual'   => isset( $p['manual'] ) && is_numeric( $p['manual'] ) && $p['manual'] > 0 ? (string) (float) $p['manual'] : '',
			);
		}

		$new = isset( $_POST['new_code'] ) ? strtoupper( sanitize_text_field( wp_unslash( $_POST['new_code'] ) ) ) : '';
		if ( preg_match( '/^[A-Z]{3}$/', $new ) && $new !== $base && ! isset( $cur[ $new ] ) ) {
			$cur[ $new ] = array(
				'enabled'  => 1,
				'symbol'   => $new,
				'decimals' => 2,
				'rounding' => 'none',
				'markup'   => 0,
				'override' => 0,
				'manual'   => '',
			);
		}

		$valid_choice = function ( $v ) use ( $cur ) {
			$v = strtoupper( sanitize_text_field( $v ) );
			return isset( $cur[ $v ] ) ? $v : '';
		};
		$freq = isset( $_POST['frequency'] ) ? sanitize_text_field( wp_unslash( $_POST['frequency'] ) ) : 'daily';
		if ( ! in_array( $freq, array( 'hourly', 'fierro_6h', 'twicedaily', 'daily' ), true ) ) {
			$freq = 'daily';
		}

		$settings = array(
			'default_currency' => $valid_choice( isset( $_POST['default_currency'] ) ? wp_unslash( $_POST['default_currency'] ) : '' ),
			'auto_detect'      => empty( $_POST['auto_detect'] ) ? 0 : 1,
			'other_countries'  => $valid_choice( isset( $_POST['other_countries'] ) ? wp_unslash( $_POST['other_countries'] ) : '' ),
			'frequency'        => $freq,
			'currencies'       => $cur,
		);
		update_option( self::OPT_SETTINGS, $settings );

		if ( $freq !== $old['frequency'] || ! wp_next_scheduled( self::CRON_HOOK ) ) {
			self::schedule( $freq );
		}
		$this->flush_price_caches();

		wp_safe_redirect( add_query_arg( array( 'page' => 'fierro-currency', 'fierro_msg' => rawurlencode( 'Settings saved.' ) ), admin_url( 'admin.php' ) ) );
		exit;
	}

	public function admin_update_now() {
		if ( ! current_user_can( 'manage_woocommerce' ) || ! check_admin_referer( 'fierro_mc_update' ) ) {
			wp_die( 'Not allowed' );
		}
		$ok = $this->update_rates();
		$this->flush_price_caches();
		$msg = $ok ? 'Exchange rates updated.' : 'Could not reach the rate services; previous rates kept.';
		wp_safe_redirect( add_query_arg( array( 'page' => 'fierro-currency', 'fierro_msg' => rawurlencode( $msg ) ), admin_url( 'admin.php' ) ) );
		exit;
	}

	/** Clear WooCommerce's cached variation price ranges after rates/settings change. */
	private function flush_price_caches() {
		if ( class_exists( 'WC_Cache_Helper' ) ) {
			WC_Cache_Helper::get_transient_version( 'product', true );
		}
	}
}

Fierro_Multi_Currency::instance();
