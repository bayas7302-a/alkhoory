# FIERRO – Client change list (from `Desktop  Changes/` and `Mobile Changes/`)

Site is built with classic Elementor (not Atomic). Legend:
**[E]** Elementor edit (widget settings / Custom CSS) · **[W]** WooCommerce or plugin setting · **[D]** custom development · **[?]** needs info from client

## Header (desktop + mobile)
- [ ] [E] Wishlist and cart count badges are different sizes. Make both the same size (`Header.jpg`, mobile remarks).
- [ ] [E] Mobile: move the badges up and right so they don't cover the icons.
- [ ] [E] Mobile: wishlist shows the letter "O" while cart shows "0". Both should show "0" (probably different fonts in the two widgets).
- [ ] [E] Mobile top bar: the Dirham sign has moved. Align it with the text baseline.
- [ ] [E] Mobile: phone number still shows `+971 52 665 4801`. Change it to `+971 52 779 4759` to match desktop.
- [ ] [E] Mobile menu: rename "SALE" to "SEASONAL SALE" and link it to the Seasonal Sale section (anchor, e.g. `/#seasonal-sale`).
- [ ] [W] Mini-cart header says "Your Cart (2)" with only 1 item. The count isn't refreshed (cart fragments or cache issue).

## Home page
- [ ] [?] Desktop hero banner: "still pending". Need the new banner file.
- [ ] [E] Mobile hero video is cropped. Fix the background video size/position for mobile.
- [ ] [E] "Timeless Bands / Jewelry Sets" banner: hover zoom doesn't work. Match the "Gift Sets" banner's hover settings (desktop + mobile).
- [ ] [E] Best Sellers product images look blurry. Use a larger image size (`woocommerce_single`/`full`) and regenerate thumbnails.
- [ ] [E] Mobile features block (Gift Wrap / Secured Payments / Quality / 24/7 Chat): center-align it.
- [ ] [E] Mobile Instagram feed: gap between the images is too big. Match the outer (green) gap.

## Footer
- [ ] [E] "Seasonal Sale" link (desktop + mobile) should scroll to the Seasonal Sale section. Give that section CSS ID `seasonal-sale` and link to `/#seasonal-sale`.
- [ ] [E] Mobile newsletter: reduce the gap between SUBSCRIBE and the success message, and center the message text.
- [ ] [E] Mobile: reduce the gap above "Payment Methods".

## Category / listing pages
- [ ] [?] Bags, Hats & Watches banner: replace it with the design "in the Google Drive". Need the file.
- [ ] [E] Collection page: gap between the category tab rows is bigger than on the home page. Make it the same.

## Product page
- [ ] [W/D] First gallery image doesn't open the fullscreen lightbox (the other images do).
- [ ] [E] Accordion: rename "Delivery & Returns" to "Returns & Exchange".
- [ ] [E] Fill it with the Returns & Exchange text. Ready to paste: `content/returns-exchange.html` (from `Returns & Exchanges.docx`).

## Cart page (mobile)
- [ ] [E] Center "You are AED 100 away from Free Shipping".
- [ ] [E] "You'll love these" category tabs look different from the home page tabs. Copy the home page tab styling.

## Wishlist (mobile)
- [ ] [?] Use the new banner "in the Google Drive". Need the file.
- [ ] [E] Increase the gap between the subtitle and the table header.
- [ ] [E] Align the Dirham sign with the price value.

## My Account (mobile)
- [ ] [E] Center-align the login/register fields and text (Apple + Samsung).
- [ ] [E] SIGN UP button font size changes when pressed. Fix the active/focus state styles.

## Contact Us (mobile)
- [ ] [E] Make the gap between SUBMIT and the success message the same as the gap above SUBMIT.
- [ ] [E] Reduce the line height of the success message.

## Shipping & Payments (mobile)
- [ ] [E] "Contact Customer Support… Whatsapp" paragraph font is bigger than the others. Match the other paragraphs.

## Checkout (desktop + mobile)
- [ ] [W] Coupon `FTS10`: 10% discount (WooCommerce → Marketing → Coupons).
- [ ] [W] Remove Cash on Delivery. Keep Tabby only (WhatsApp/Mamo flow skipped for now).
- [ ] [W] Add 5% VAT **at checkout** (prices entered excl. VAT): WooCommerce → Settings → Tax, standard rate 5% for AE, show "VAT 5%" row under the price.
- [ ] [W] UAE shipping: AED 25 flat rate, free when the subtotal **before VAT** is over AED 150 (Shipping zones → Free shipping, min. amount 150).
- ~~[W/D] Overseas shipping: FedEx real-time rates~~ **Skipped for now.**
- [ ] [W] State / County field shows only a label with no input for some countries (e.g. South Korea). Probably a theme CSS or checkout-field plugin issue.
- ~~[D] "Place Order via WhatsApp" + Mamo payment-link flow~~ **Skipped for now.**

## Client answers (Oct 2026)
1. Banners are in Google Drive folder "2026 FIERRO SITE". Category banners go on the **category thumbnail** (Products → Categories → Thumbnail), so check that the category pages use the thumbnail correctly.
2. Returns & Exchange text: `Returns & Exchanges.docx` → `content/returns-exchange.html`.
3. VAT is added at checkout.
4. FedEx: skipped for now.
5. WhatsApp order flow: skipped for now.
6. WhatsApp API: skipped.
7. Free shipping threshold AED 150 is measured **before VAT**.
