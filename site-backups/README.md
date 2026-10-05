# Elementor data backups

`2026-10-05-before-changes/` holds the `_elementor_data` of every page and template as it was **before** the
Oct 2026 client fixes (fetched via `wp-json/wp/v2/{pages|elementor_library}/<id>?context=edit`).

| File | Post |
|---|---|
| tpl-425 | Header template |
| tpl-428 | Footer template |
| tpl-443 | Single product template |
| tpl-519 | Product archive (category) template |
| tpl-171 | Product loop item |
| page-34 | Home |
| page-11 / 12 / 13 | Cart / Checkout / My Account |
| page-253 / 278 / 481 | Contact Us / Shipping & Payments / Wishlist |

To roll back one post, POST `{"meta":{"_elementor_data": <meta._elementor_data from the file>}}` to the same
endpoint, then clear Elementor's CSS cache (Elementor → Tools → Regenerate CSS & Data).
