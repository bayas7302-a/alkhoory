<?php
/**
 * Plugin Name: AROSA – Floral Wrapping (Gift) Option
 * Description: Adds an "Add floral wrapping service" option to the WooCommerce checkout (AED 75), with "To myself / To someone else" and a gift message. Shown only when the cart contains a physical product.
 * Version:     1.0.0
 * Author:      AROSA
 * Text Domain: arosa-gift-wrap
 *
 * Install: copy this file to wp-content/mu-plugins/ (auto-active), or zip it and upload as a normal plugin.
 * Works with the classic checkout (Elementor Pro "Checkout" widget / [woocommerce_checkout] shortcode).
 */

defined( 'ABSPATH' ) || exit;

final class Arosa_Gift_Wrap {

	/** Price of the floral wrapping service (in the store currency, AED). */
	const PRICE = 75;

	/** Set to true if the wrapping fee should have VAT added on top of it. */
	const TAXABLE = false;

	/** Max characters for the gift message. */
	const MAX_CHARS = 300;

	const SESSION_KEY = 'arosa_gift_wrap';

	public static function init() {
		add_action( 'woocommerce_checkout_after_customer_details', array( __CLASS__, 'render_field' ) );
		add_action( 'woocommerce_checkout_update_order_review', array( __CLASS__, 'store_from_ajax' ) );
		add_action( 'woocommerce_checkout_process', array( __CLASS__, 'store_from_post' ), 1 );
		add_action( 'woocommerce_checkout_process', array( __CLASS__, 'validate' ) );
		add_action( 'woocommerce_cart_calculate_fees', array( __CLASS__, 'add_fee' ) );
		add_action( 'woocommerce_checkout_create_order', array( __CLASS__, 'save_to_order' ), 10, 2 );
		add_action( 'woocommerce_checkout_order_processed', array( __CLASS__, 'clear_session' ) );

		// Show the details to the shop team and the customer.
		add_action( 'woocommerce_admin_order_data_after_shipping_address', array( __CLASS__, 'admin_order_details' ) );
		add_action( 'woocommerce_order_details_after_order_table', array( __CLASS__, 'customer_order_details' ) );
		add_action( 'woocommerce_email_after_order_table', array( __CLASS__, 'email_order_details' ), 10, 4 );

		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'assets' ), 20 );
	}

	/* ---------------------------------------------------------------------
	 * Helpers
	 * ------------------------------------------------------------------- */

	/** True when the product is a physical item (not virtual and not downloadable). */
	private static function is_physical( $product ) {
		return $product && ! $product->is_virtual() && ! $product->is_downloadable();
	}

	/** True when at least one physical product is in the cart. Digital-only carts never see the option. */
	public static function cart_has_physical() {
		if ( ! function_exists( 'WC' ) || ! WC()->cart ) {
			return false;
		}
		foreach ( WC()->cart->get_cart() as $item ) {
			if ( self::is_physical( isset( $item['data'] ) ? $item['data'] : null ) ) {
				return true;
			}
		}
		return false;
	}

	private static function sanitize( array $src ) {
		$enabled   = ! empty( $src['arosa_gift_wrap'] );
		$recipient = ( isset( $src['arosa_gift_recipient'] ) && 'someone_else' === $src['arosa_gift_recipient'] ) ? 'someone_else' : 'myself';
		$message   = isset( $src['arosa_gift_message'] ) ? sanitize_textarea_field( wp_unslash( $src['arosa_gift_message'] ) ) : '';

		return array(
			'enabled'   => $enabled,
			'recipient' => $recipient,
			'message'   => $enabled ? $message : '',
		);
	}

	private static function get_state() {
		$defaults = array( 'enabled' => false, 'recipient' => 'myself', 'message' => '' );
		$state    = ( function_exists( 'WC' ) && WC()->session ) ? WC()->session->get( self::SESSION_KEY ) : null;
		return is_array( $state ) ? wp_parse_args( $state, $defaults ) : $defaults;
	}

	private static function set_state( array $state ) {
		if ( WC()->session ) {
			WC()->session->set( self::SESSION_KEY, $state );
		}
	}

	private static function recipient_label( $value ) {
		return 'someone_else' === $value ? __( 'To someone else', 'arosa-gift-wrap' ) : __( 'To myself', 'arosa-gift-wrap' );
	}

	/* ---------------------------------------------------------------------
	 * Checkout field
	 * ------------------------------------------------------------------- */

	public static function render_field() {
		if ( ! self::cart_has_physical() ) {
			return;
		}
		$s = self::get_state();
		?>
		<div class="arosa-gift" id="arosa-gift">
			<label class="arosa-gift__check">
				<input type="checkbox" name="arosa_gift_wrap" id="arosa_gift_wrap" value="1" <?php checked( $s['enabled'] ); ?> />
				<span class="arosa-gift__box" aria-hidden="true"></span>
				<span class="arosa-gift__title">
					<?php esc_html_e( 'Add floral wrapping service', 'arosa-gift-wrap' ); ?>
					<span class="arosa-gift__price">+<?php echo wp_kses_post( wc_price( self::PRICE ) ); ?></span>
				</span>
			</label>
			<p class="arosa-gift__desc"><?php esc_html_e( 'Your order will be beautifully wrapped with fresh florals and includes a printed card with your personal message.', 'arosa-gift-wrap' ); ?></p>

			<div class="arosa-gift__panel" <?php echo $s['enabled'] ? '' : 'hidden'; ?>>
				<p class="arosa-gift__label"><?php esc_html_e( 'Who is this gift delivered to?', 'arosa-gift-wrap' ); ?></p>
				<div class="arosa-gift__toggle" role="radiogroup">
					<label>
						<input type="radio" name="arosa_gift_recipient" value="myself" <?php checked( 'myself', $s['recipient'] ); ?> />
						<span><?php esc_html_e( 'To myself', 'arosa-gift-wrap' ); ?></span>
					</label>
					<label>
						<input type="radio" name="arosa_gift_recipient" value="someone_else" <?php checked( 'someone_else', $s['recipient'] ); ?> />
						<span><?php esc_html_e( 'To someone else', 'arosa-gift-wrap' ); ?></span>
					</label>
				</div>
				<p class="arosa-gift__hint" data-for="someone_else" <?php echo 'someone_else' === $s['recipient'] ? '' : 'hidden'; ?>>
					<?php esc_html_e( 'Please enter the recipient’s details under “Ship to a different address”.', 'arosa-gift-wrap' ); ?>
				</p>

				<label class="arosa-gift__label" for="arosa_gift_message">
					<?php
					/* translators: %d: max number of characters */
					printf( esc_html__( 'Your message (%d)', 'arosa-gift-wrap' ), (int) self::MAX_CHARS );
					?>
				</label>
				<textarea name="arosa_gift_message" id="arosa_gift_message" class="input-text" rows="4"
					maxlength="<?php echo (int) self::MAX_CHARS; ?>"
					placeholder="<?php esc_attr_e( 'Write your message here (optional)', 'arosa-gift-wrap' ); ?>"><?php echo esc_textarea( $s['message'] ); ?></textarea>
				<p class="arosa-gift__count"><span data-count><?php echo (int) ( self::MAX_CHARS - mb_strlen( $s['message'] ) ); ?></span> <?php esc_html_e( 'characters remaining', 'arosa-gift-wrap' ); ?></p>
			</div>
		</div>
		<?php
	}

	/** Checkout AJAX refresh (update_order_review) sends the whole form as a query string. */
	public static function store_from_ajax( $post_data ) {
		parse_str( (string) $post_data, $data );
		self::set_state( self::sanitize( is_array( $data ) ? $data : array() ) );
	}

	/** Final "Place order" submit. */
	public static function store_from_post() {
		// phpcs:ignore WordPress.Security.NonceVerification.Missing -- WooCommerce verifies the checkout nonce.
		self::set_state( self::sanitize( $_POST ) );
	}

	public static function validate() {
		$s = self::get_state();
		if ( $s['enabled'] && mb_strlen( $s['message'] ) > self::MAX_CHARS ) {
			/* translators: %d: max number of characters */
			wc_add_notice( sprintf( __( 'Your gift message can be up to %d characters.', 'arosa-gift-wrap' ), self::MAX_CHARS ), 'error' );
		}
	}

	public static function add_fee( $cart ) {
		if ( is_admin() && ! wp_doing_ajax() ) {
			return;
		}
		$s = self::get_state();
		if ( $s['enabled'] && self::cart_has_physical() ) {
			$cart->add_fee( __( 'Floral wrapping service', 'arosa-gift-wrap' ), self::PRICE, self::TAXABLE );
		}
	}

	public static function save_to_order( $order, $data ) {
		$s = self::get_state();
		if ( ! $s['enabled'] || ! self::cart_has_physical() ) {
			return;
		}
		$order->update_meta_data( '_arosa_gift_wrap', 'yes' );
		$order->update_meta_data( '_arosa_gift_recipient', $s['recipient'] );
		$order->update_meta_data( '_arosa_gift_message', $s['message'] );
	}

	public static function clear_session() {
		if ( WC()->session ) {
			WC()->session->__unset( self::SESSION_KEY );
		}
	}

	/* ---------------------------------------------------------------------
	 * Order display
	 * ------------------------------------------------------------------- */

	private static function order_rows( $order ) {
		if ( ! $order || 'yes' !== $order->get_meta( '_arosa_gift_wrap' ) ) {
			return array();
		}
		$rows = array(
			__( 'Floral wrapping', 'arosa-gift-wrap' ) => __( 'Yes', 'arosa-gift-wrap' ),
			__( 'Delivered to', 'arosa-gift-wrap' )    => self::recipient_label( $order->get_meta( '_arosa_gift_recipient' ) ),
		);
		$msg = $order->get_meta( '_arosa_gift_message' );
		if ( '' !== $msg ) {
			$rows[ __( 'Gift message', 'arosa-gift-wrap' ) ] = $msg;
		}
		return $rows;
	}

	public static function admin_order_details( $order ) {
		$rows = self::order_rows( $order );
		if ( ! $rows ) {
			return;
		}
		echo '<div class="arosa-gift-admin" style="clear:both;padding-top:12px"><h3>' . esc_html__( 'Gift / Floral wrapping', 'arosa-gift-wrap' ) . '</h3>';
		foreach ( $rows as $k => $v ) {
			echo '<p><strong>' . esc_html( $k ) . ':</strong><br>' . nl2br( esc_html( $v ) ) . '</p>';
		}
		echo '</div>';
	}

	public static function customer_order_details( $order ) {
		$rows = self::order_rows( $order );
		if ( ! $rows ) {
			return;
		}
		echo '<section class="arosa-gift-summary"><h2 class="woocommerce-column__title">' . esc_html__( 'Gift details', 'arosa-gift-wrap' ) . '</h2><table class="woocommerce-table shop_table"><tbody>';
		foreach ( $rows as $k => $v ) {
			echo '<tr><th>' . esc_html( $k ) . '</th><td>' . nl2br( esc_html( $v ) ) . '</td></tr>';
		}
		echo '</tbody></table></section>';
	}

	public static function email_order_details( $order, $sent_to_admin, $plain_text, $email = null ) {
		$rows = self::order_rows( $order );
		if ( ! $rows ) {
			return;
		}
		if ( $plain_text ) {
			echo "\n" . esc_html( strtoupper( __( 'Gift details', 'arosa-gift-wrap' ) ) ) . "\n";
			foreach ( $rows as $k => $v ) {
				echo esc_html( $k . ': ' . $v ) . "\n";
			}
			return;
		}
		echo '<h2>' . esc_html__( 'Gift details', 'arosa-gift-wrap' ) . '</h2><table cellspacing="0" cellpadding="6" border="1" style="width:100%;border-collapse:collapse;margin-bottom:30px"><tbody>';
		foreach ( $rows as $k => $v ) {
			echo '<tr><th style="text-align:start;width:35%">' . esc_html( $k ) . '</th><td>' . nl2br( esc_html( $v ) ) . '</td></tr>';
		}
		echo '</tbody></table>';
	}

	/* ---------------------------------------------------------------------
	 * CSS + JS (inline, only on the checkout page)
	 * ------------------------------------------------------------------- */

	public static function assets() {
		if ( ! function_exists( 'is_checkout' ) || ! is_checkout() || is_order_received_page() ) {
			return;
		}
		wp_register_style( 'arosa-gift-wrap', false, array(), '1.0.0' );
		wp_enqueue_style( 'arosa-gift-wrap' );
		wp_add_inline_style( 'arosa-gift-wrap', self::css() );

		wp_add_inline_script( 'wc-checkout', self::js() );
	}

	private static function css() {
		return <<<'CSS'
.arosa-gift{--ag-brown:var(--e-global-color-5e21ddf,#494037);--ag-line:var(--e-global-color-f56226b,#E1DDD1);--ag-soft:var(--e-global-color-413c213,#F6E8DF);
  font-family:"coconat",Sans-serif;margin:30px 0;padding:24px 26px;border:1px solid var(--ag-line);background:#fff}
.arosa-gift,.arosa-gift *{box-sizing:border-box}
.arosa-gift *{font-family:"coconat",Sans-serif}
.arosa-gift__check{display:flex!important;align-items:center;gap:12px;cursor:pointer;margin:0!important;position:relative}
.arosa-gift__check input{position:absolute;opacity:0;width:1px;height:1px}
.arosa-gift__box{flex:0 0 20px;width:20px;height:20px;border:1px solid var(--ag-brown);background:#fff;position:relative;transition:background .2s}
.arosa-gift__check input:checked+.arosa-gift__box{background:var(--ag-brown)}
.arosa-gift__check input:checked+.arosa-gift__box:after{content:"";position:absolute;inset-inline-start:6px;top:2px;width:6px;height:11px;border:solid #fff;border-width:0 2px 2px 0;transform:rotate(45deg)}
.arosa-gift__check input:focus-visible+.arosa-gift__box{outline:2px solid var(--ag-brown);outline-offset:2px}
.woocommerce-checkout .arosa-gift .arosa-gift__title{font-weight:700!important}
.woocommerce-checkout .arosa-gift .arosa-gift__price,.woocommerce-checkout .arosa-gift .arosa-gift__price *{font-weight:400!important;white-space:nowrap}
.arosa-gift__price{margin-inline-start:6px}
.arosa-gift__desc{margin:10px 0 0!important;padding-inline-start:32px;line-height:1.5}
.arosa-gift__panel{margin-top:22px;padding-top:22px;border-top:1px solid var(--ag-line)}
.arosa-gift__panel[hidden],.arosa-gift__hint[hidden]{display:none!important}
.arosa-gift__label{display:block;margin:0 0 10px!important}
.arosa-gift__toggle{display:inline-flex;max-width:100%;gap:4px;padding:4px;background:#fff;border:1px solid var(--ag-line);border-radius:0;margin-bottom:22px}
.arosa-gift__toggle label{flex:0 0 auto;margin:0!important;cursor:pointer;position:relative}
.arosa-gift__toggle input{position:absolute;opacity:0;width:1px;height:1px}
.arosa-gift__toggle span{display:block;text-align:center;white-space:nowrap;padding:9px 26px;border-radius:0;transition:background .2s,color .2s}
.arosa-gift__toggle label:hover span{background:var(--ag-soft)}
.woocommerce-checkout .arosa-gift .arosa-gift__toggle input:checked+span{background:var(--ag-brown)!important;color:#fff!important}
.arosa-gift__toggle input:focus-visible+span{outline:2px solid var(--ag-brown);outline-offset:1px}
.arosa-gift__hint{margin:-12px 0 22px!important;opacity:.8}
.woocommerce form.woocommerce-checkout .arosa-gift textarea{width:100%;min-height:120px;padding:14px 16px;border:1px solid var(--ag-line);resize:vertical;background:#fff}
.woocommerce-checkout .arosa-gift .arosa-gift__count,.woocommerce-checkout .arosa-gift .arosa-gift__count *{font-size:13px!important;opacity:.75}
.arosa-gift__count{margin:6px 0 0!important}
@media (max-width:767px){.arosa-gift{padding:20px 16px}.arosa-gift__desc{padding-inline-start:0}.woocommerce-checkout .arosa-gift .arosa-gift__toggle span{font-size:15px!important;padding:9px 18px}}
CSS;
	}

	private static function js() {
		return <<<'JS'
jQuery(function($){
  var $wrap = $('#arosa-gift');
  if (!$wrap.length) return;
  var $panel = $wrap.find('.arosa-gift__panel');
  var $msg = $('#arosa_gift_message');
  var max = parseInt($msg.attr('maxlength'), 10) || 300;
  var $ship = $('#ship-to-different-address-checkbox');

  $('#arosa_gift_wrap').on('change', function(){
    $panel.prop('hidden', !this.checked);
    $(document.body).trigger('update_checkout');
  });

  $wrap.on('change', 'input[name="arosa_gift_recipient"]', function(){
    var other = this.value === 'someone_else';
    $wrap.find('.arosa-gift__hint').prop('hidden', !other);
    if ($ship.length && $ship.prop('checked') !== other) {
      $ship.prop('checked', other).trigger('change');
    }
    if (other && $ship.length) {
      $('html, body').animate({scrollTop: $('.woocommerce-shipping-fields').offset().top - 120}, 400);
    }
  });

  $msg.on('input', function(){
    $wrap.find('[data-count]').text(Math.max(0, max - this.value.length));
  });
});
JS;
	}
}

add_action( 'plugins_loaded', array( 'Arosa_Gift_Wrap', 'init' ) );
