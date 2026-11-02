# AROSA – Floral wrapping (gift) option at checkout

Client request (Shama Alfalasi): a tick box at checkout, **"Add floral wrapping service"**, **AED 75**.
When ticked it shows **"Who is this gift delivered to? To myself / To someone else"** and a **"Your message (300)"** box.

## What `arosa-gift-wrap.php` does
- Shows the option **only when the cart has at least one physical product** (not virtual and not downloadable). Carts with only digital products never see it.
- Ticking it adds a **"Floral wrapping service – AED 75"** fee line to the order totals (live update).
- "To someone else" also ticks WooCommerce's **Ship to a different address** so the customer enters the recipient's address.
- Message: optional, max 300 characters, with a live counter.
- Saved on the order (HPOS-safe meta: `_arosa_gift_wrap`, `_arosa_gift_recipient`, `_arosa_gift_message`) and shown in **WP Admin → order**, the **thank-you / My Account order page** and **order emails**.
- Styling uses the site's checkout tokens: `coconat` font, `--e-global-color-5e21ddf` (#494037) text, `#E1DDD1` lines, `#F6E8DF` toggle, matching the existing checkout custom CSS.
- Texts use the `arosa-gift-wrap` text domain, so they can be translated for `/ar/` (WPML/Loco). The layout is RTL-safe.

## Install
1. Upload `arosa-gift-wrap.php` to `wp-content/mu-plugins/` (create the folder if needed). Must-use plugins are active right away.
   Or zip it and install it from Plugins → Add New → Upload.
2. Add a physical product to the cart → Checkout → test tick / untick, both toggle options, and place a test order.

## Settings (constants at the top of the class)
- `PRICE = 75`: fee in AED.
- `TAXABLE = false`: set to `true` if VAT should be added on top of the fee.
- `MAX_CHARS = 300`

Note: built for the classic checkout (Elementor Pro Checkout widget / `[woocommerce_checkout]`), which is what the site's checkout CSS targets. If the checkout page is switched to the block checkout, this needs a block version.

Preview: `preview-desktop.png`, `preview-mobile.png` (the real site will show the `coconat` font).
