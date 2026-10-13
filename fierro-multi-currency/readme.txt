=== FIERRO Multi Currency ===
Requires: WordPress 6.0+, WooCommerce 7.0+, PHP 7.4+

Shows WooCommerce prices in GCC currencies (SAR, KWD, QAR, BHD, OMR) and USD.
Product prices stay saved in the store currency (AED) and are converted for shoppers.

== Install ==
1. Plugins > Add New > Upload Plugin > choose fierro-multi-currency.zip > Install > Activate.
2. WooCommerce > FIERRO Currency: click "Update rates now", check the rates, save.
3. Add [fierro_currency_switcher] to the header (Elementor Shortcode widget).

== Exchange rates ==
- Fetched automatically (default once a day) from open.er-api.com, with
  fawazahmed0/currency-api as a fallback. Both are free and need no API key.
- If both are unreachable the last good rates are kept and a warning is shown.
- Per currency: tick "Override" and enter a manual rate to use your own rate.
  Untick to go back to the automatic rate.
- Optional markup % per currency (e.g. 2 to cover card conversion fees).
- Price rounding per currency: none, nearest 0.50, nearest 1, up to next 1, end in .99, nearest 5.

== Adding currencies later ==
Type a 3-letter code (e.g. EUR) in "Add a currency" and save. It gets a live
rate automatically; set a manual rate if the API doesn't list it.

== What is converted ==
Product, cart and checkout prices, shipping costs, the free-shipping minimum,
fixed-amount coupons and coupon min/max spend. Percentage VAT and coupons work
as normal. Orders are placed and paid in the shopper's selected currency.
Admin screens, reports and the WooCommerce REST API stay in AED.

== Shortcodes ==
[fierro_currency_switcher]                 dropdown
[fierro_currency_switcher style="buttons"] buttons
[fierro_currency_rates]                    rate table (amount="100", currencies="USD,SAR", show_updated="no")
[fierro_convert amount="150"]              an AED amount in the visitor's currency

== Notes ==
- Page cache: WP-Optimize is told to keep a separate cache per currency. After
  rates update, purge the page cache (or set a cache lifespan of about a day).
- Auto-select by country uses WooCommerce geolocation.
- Test one paid order in KWD/BHD/OMR (3-decimal currencies) with Stripe before going live.
