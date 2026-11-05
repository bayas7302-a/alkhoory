# Kress Kleen – site polish

One snippet for the child theme's `functions.php` that fixes layout, typography and interaction across the site.

## Files
- `kk-polish-snippet.php`: **paste this at the end of `functions.php`.** It is generated from the two files below.
- `kk-polish.css`, `kk-polish.js`: the source files, kept so the CSS and JS are easy to read and edit.

## What it changes

| Area | Change |
|---|---|
| Mobile header (≤767px) | A single 64px row: menu + language on the left, logo in the centre, "Where to buy" pill on the right. **Sticky on phones only.** The "Kleening since 2003" bar scrolls away and the main bar stays at the top with a solid green, blurred background and a soft shadow. Tablet and desktop headers are unchanged and not sticky. |
| Home hero | Smaller card and text on desktop and mobile. |
| Page banners | "Who We Are", "Where to buy" and "Catalogue" titles go from 74px to a 40–60px range on desktop, 40px on tablet and 32px on mobile. Banner padding is tighter. |
| Let's find your cleaning solution | The EXPLORE hover no longer shows a floating shadow box. It now draws an underline and slides the arrow, and the card lifts with a soft shadow while the photo zooms. Mobile shows 2 cards per row (the last one full width) with smaller text. Tablet shows 3 + 2. |
| One home. Many cleaning challenges | The 4 buttons are the same width with text centred vertically. The border is on the button itself, so the hover matches it: the button fills orange and an arrow appears. Tablet puts the text on top and the map full width. |
| Home typography and spacing | Consistent section padding (64px top/bottom, 20px sides on mobile). Headings are 24px on mobile and 28px on tablet, body text 15px. The SMART / FUNCTIONAL / RELIABLE and featured product sections are scaled down. |
| Footer (tablet/mobile) | Everything centred: logo, Quick Links row, full-width buttons of equal height, and the copyright under a thin divider. |
| Who We Are | Rebuilt for tablet and mobile. Headings scaled (30, 22, 15px), the broom image fits the screen, the 4 feature cards form a 2 × 2 grid, the dark statement boxes are tighter, "No over-engineering" fits the screen, "Guaranteed…" is 54px. The page no longer scrolls sideways (it was 679px wide on phones). |
| Where to buy | Banner, "Find Stores Near You" and the brand text scaled for mobile. |
| Catalogue filters | **Desktop:** each group (Solutions / Categories / Areas) is an accordion with an item count. The group with the current filter opens automatically. **Tablet/mobile:** a sticky "Filter" button and a "Showing: Brooms" chip above the products. The button opens a slide-up drawer with the same accordions plus "Show all" and "Done". The drawer closes on Esc or a tap outside, and focus returns to the button. Products show 2 per row on mobile. |
| Whole site | No sideways scrolling on any page (home, Who We Are and Catalogue used to scroll). Image widgets clip their zoom effects. |

## Tested
Chromium, against the live pages with the CSS/JS injected: iPhone 13 (390px), tablet (820px) and desktop (1440px), on home, Who We Are, Where to buy, Catalogue, category, product and shop. Results:
- Page width equals screen width on every page.
- No script errors.
- The header sticks on phones only.
- The scroll-video hero still works.

## How to apply
1. Appearance → Theme File Editor → child theme → `functions.php`.
2. Paste the whole `kk-polish-snippet.php` at the very end. Don't add another `<?php` at the top.
3. Save, clear the LiteSpeed cache (and Elementor → Tools → Regenerate CSS if anything looks stale).

To remove it, delete the block.

## Notes
- The CSS targets Elementor element IDs (`.elementor-element-xxxxxxx`). If a section is deleted and rebuilt in Elementor, its ID changes and that section's rule needs updating.
- Spelling seen on the site, worth fixing in Elementor:
  - footer "Download **Catelouge**" → "Catalogue"
  - menu "**Catelog**" → "Catalogue"
  - home featured product "**Eortless**" → "Effortless"
  - Where to buy "We **oer**" → "We offer"
  - the Where to buy brand block is labelled "FEATURED PRODUCT"
