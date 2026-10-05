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

## Home page
- ⏳ Desktop hero banner: new design is in the client's Google Drive. Need the file.
- ✅ Mobile hero video no longer cropped (shown full width at its 720×470 ratio).
- ✅ "Timeless Bands / Jewelry Sets" banner hover zoom works (same effect as the other banners, desktop and mobile).
- ✅ Best Sellers hover photos load the full-size image instead of 300×300 (no more blur).
- ✅ Mobile features block (Gift Wrap / Payments / Quality / Chat) centered.
- ✅ Mobile Instagram images fill their slides, so the gaps match the outer gap.

## Footer
- ✅ "Seasonal Sale" links (desktop + mobile) go to the Seasonal Sale section. Also fixed "Seasonal Sales" typo and the missing My Account link.
- ✅ Mobile newsletter "thank you" message sits closer to SUBSCRIBE and is centered.
- ✅ Mobile: removed the double divider and extra gap above "Payment Methods".

## Category / listing pages
- ⏳ Bags, Hats & Watches banner: category pages use the **category thumbnail** (works for Necklaces, Earrings, Bracelets, Gift Sets, Jewelry Sets). Bags, Hats & Watches **has no thumbnail**, so it falls back to the generic Collection banner. Upload the Drive banner to Products → Categories → Bags, Hats & Watches → Thumbnail.
- ✅ Collection page tab rows use the same gap as the home page.

## Product page
- ✅ Clicking/tapping any gallery image (including the first) opens a fullscreen preview with arrows, swipe, and Esc to close.
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
