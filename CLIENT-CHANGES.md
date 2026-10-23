# FIERRO – Client change list

Source: `Desktop  Changes/` and `Mobile Changes/` screenshots. Site: soharon.co.uk/dev-101 (classic Elementor + WooCommerce).
Status as of 2026-10-05. ✅ done & verified · ⏳ waiting on something · ⚠️ blocked / needs decision · ⏭️ skipped by client

Backups of every page/template touched: `site-backups/2026-10-05-before-changes/`.

## Header
- ✅ Wishlist and cart badges are the same size (desktop 18px, mobile 14px) and use the same font, so both show "0".
- ✅ Mobile: badges moved up and right, away from the icons.
- ✅ Dirham sign aligned with the text, in the top bar and site-wide on prices.
- ✅ Mobile phone number is now +971 52 779 4759 (WhatsApp link updated too).
- ✅ Mobile menu shows "SEASONAL SALE" and scrolls to the Seasonal Sale section (`/#sale`). Desktop bar still says "SALE" because the longer label wrapped the desktop menu.
- ✅ Mini-cart heading "Your Cart (N)" uses the real cart count. It used to add up the desktop + mobile carts and show 2 for 1 item.

- ✅ Removed the white strip under the welcome bar (caused by the header fix block taking space).

## Home page
- ⏳ Desktop hero banner: new design is in the client's Google Drive. Need the file.
- ✅ Mobile hero video no longer cropped (shown full width at its 720×470 ratio).
- ✅ "Timeless Bands / Jewelry Sets" banner hover zoom works (same effect as the other banners, desktop and mobile).
- ✅ Best Sellers hover photos load the full-size image instead of 300×300 (no more blur).
- ✅ Mobile features block (Gift Wrap / Payments / Quality / Chat) centered.
- ✅ Mobile Instagram images fill their slides, so the gaps match the outer gap.

## Footer
- ✅ "Seasonal Sale" links (desktop + mobile) go to the Seasonal Sale section. Also fixed "Seasonal Sales" typo and the missing My Account link.
- ✅ Mobile newsletter message sits 10px under SUBSCRIBE (the hidden CF7 spinner was adding ~30px) and is centered.
- ✅ Mobile: removed the double divider and extra gap above "Payment Methods".

## Category / listing pages
- ⏳ Bags, Hats & Watches banner: category pages use the **category thumbnail** (works for Necklaces, Earrings, Bracelets, Gift Sets, Jewelry Sets). Bags, Hats & Watches **has no thumbnail**, so it falls back to the generic Collection banner. Upload the Drive banner to Products → Categories → Bags, Hats & Watches → Thumbnail.
- ✅ Collection page tab rows use the same gap as the home page.

## Product page
- ✅ Clicking/tapping any gallery image (including the first) opens the image popup styled like fierrojewelry.com: white rounded card on a dimmed page, X top-right, ‹ › arrows, swipe, Esc to close.
- ✅ Accordion renamed "Returns & Exchange" and filled with the text from `Returns & Exchanges.docx`.

## Cart page
- ✅ "You are AED X away from Free Shipping" centered, and the amount now uses the AED 150 threshold. The theme's cart code still has 200 hard-coded; a script corrects the number shown.
- ✅ "You'll love these" now uses the same carousel and tabs as the home page.

## Wishlist
- ⏳ New banner from Google Drive. Need the file.
- ✅ More space between the subtitle and the table header (mobile).
- ✅ Dirham sign aligned with the price (site-wide fix).

## My Account (mobile)
- ✅ Fields, buttons and "Forgot Password" centered at every phone width.
- ✅ SIGN UP button keeps the same font size after pressing it (was 14px, now 18px like LOGIN).

## Contact Us (mobile)
- ✅ Gap between SUBMIT and the success message equals the gap above SUBMIT (17px).
- ✅ Tighter line height in the success message.

## Shipping & Payments (mobile)
- ✅ "Contact Customer Support…" paragraph is the same size as the others (12px).

## Checkout (desktop + mobile)
- ✅ Coupon `FTS10` = 10% off.
- ✅ VAT 5% added at checkout (UAE addresses), shown as a "VAT 5%" row. Prices are entered excluding VAT.
- ✅ UAE shipping: AED 25 flat rate (no VAT on shipping), free when the subtotal before VAT is AED 150 or more (flat rate hidden then).
  Verified: 100 → 100 + 25 + 5 VAT = 130; 200 → free shipping + 10 VAT = 210; 200 with FTS10 → 189.
- ✅ State / County: empty row hidden for countries without states (e.g. South Korea).
- ⚠️ "Remove Cash on Delivery, only Tabby": **Tabby is not installed as a payment method.** COD is the only active one, so turning it off would leave checkout with no way to pay. Install and configure the Tabby WooCommerce plugin (needs Tabby merchant keys), then disable COD.
- ⏭️ FedEx real-time rates: skipped for now. Note: the "rest of world" shipping zone has **no methods**, so overseas customers currently can't check out.
- ⏭️ Place Order via WhatsApp + Mamo: skipped for now.

## Not in the client list, but noticed
- The desktop menu wraps onto two lines below ~1900px wide (1280–1600 laptops). This was already the case before these changes.
- The Dirham symbol image is hot-linked from upload.wikimedia.org. If Wikimedia is slow or blocked, prices show "AED" alt text. Better to host it in the Media Library (theme code change).
- The cart's free-shipping threshold (200) should also be changed in the child theme PHP so the script workaround can be removed.

## Client answers (Oct 2026)
1. Banners are in Google Drive folder "2026 FIERRO SITE". Category banners go on the category thumbnail.
2. Returns & Exchange text: `Returns & Exchanges.docx` → `content/returns-exchange.html`.
3. VAT is added at checkout.
4. FedEx: skipped for now.
5. WhatsApp order flow: skipped for now.
6. WhatsApp API: skipped.
7. Free shipping threshold AED 150 is measured before VAT.

## Round 3 – client remarks of 2026-10-23 (zip: desktop + mobile)
Backups taken before these changes: `site-backups/2026-10-23-before-changes/` (Elementor data of header, footer, product, archive, loop, home, cart, checkout, account, contact).

### Done & verified
- ✅ Desktop top bar: currency selector now 18px, same as "Free Delivery".
- ✅ Mobile header: currency selector centred between the menu icon and the logo (±3px at 360/390/430px).
- ✅ Mobile header: wishlist/cart count circles overlap the icons' top-right corner like the client's example.
- ✅ Images & videos: right-click / long-press save and dragging blocked site-wide; videos have download, picture-in-picture and casting turned off. (Determined users can still screenshot or use developer tools – no website can fully prevent that.)
- ✅ "SAVE 30%" corner badge text crisper (thin rotated text was rendering soft).
- ✅ Footer: "Stockist" removed from Support (desktop + mobile); it linked to a non-existent page.
- ✅ Home (desktop): the 3 banners line up with the Best Sellers cards (a hidden script widget was taking a slot + 30px gap).
- ✅ Product page: main images load full size (were 600px shown at 683px+); thumbnails use 300px.
- ✅ Product page (mobile): tap the image to zoom in place, drag to look around, tap again to zoom out – no fullscreen popup. Desktop popup unchanged.
- ✅ Category pages: product model (hover) photos load full size instead of 300×300.
- ✅ Collection page (desktop + mobile): category links filter the products in place (no page change), like the home page; Prev/Next page buttons work the same way.
- ✅ VAT removed: prices are VAT-inclusive (WooCommerce tax calculation turned off). No VAT line in cart or checkout.
- ✅ Checkout: "Shipment" → "Shipping"; "(Price inclusive of all taxes)" under the total (desktop + mobile).
- ✅ Checkout (desktop): Your Order box starts level with the first billing field.
- ✅ Cart (desktop): 80px between the slider dots and "Lets Socialize" (same as the product page).
- ✅ Cart (mobile): QUANTITY heading removed; discounted items show old (crossed-out) and new price.
- ✅ Floating cart: "Shipping will be calculated at checkout" (word "Taxes" removed).
- ✅ Contact form (mobile): typed text starts close to the field edge like the example.

### Needs files / answers
- ⏳ Home banners look soft: the mobile banner files are only 680×160 px and the desktop ones 528×360 px. Phones need ~3× and laptops ~2× the displayed size. Please supply: mobile banners at least 1360×320 px (ideally 2040×480), desktop banners at least 1056×720 px.
- ℹ️ Apple Pay / Google Pay / Link buttons on checkout: normal – this is Stripe's "express checkout". They can be turned off in WooCommerce → Settings → Payments → Stripe → Express checkouts, or limited to product/cart pages.
- ℹ️ Laptop widths (below ~1720px): the Best Sellers cards run to the screen edge while banner rows keep a 40px margin. Not changed (not in the remarks); can align if wanted.
