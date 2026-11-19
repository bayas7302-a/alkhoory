# Design v3 – Phase 0 and Phase 1 report (19 Nov 2026)

Brief: `kress-kleen-fix-brief.md`. Design: `KK_Website_v3_copy.pdf`.

## Why some items could not go through the Elementor MCP

The whole site is built with **classic (V3) Elementor widgets**. The `kress-kleen-elementor` MCP only edits V4 (atomic) elements. Every V3 edit returns `elementor_v3_not_supported` (tested on the footer button `cec317d`). The kit's global settings are also still Elementor's defaults (1140px, Roboto). Every section sets its own width (1300px) and fonts, so a kit change would not show anywhere.

What was used instead:
- **WordPress / WooCommerce REST API** (same admin application password) for the menu, the page title and the product data.
- **CSS in `kk-polish-snippet.php`** for Phase 1. It needs pasting into `functions.php` like before.

## Phase 0 – text fixes

| ID | Fix | Status |
|---|---|---|
| N-01 | Slide-out menu "Catelog" → "Catalogue" | **Done** (REST, menu item 241). Live. |
| D-01 | Product bullet "fifine" → "fine" | **Done** (REST, product 216 `_kk_features`). Live. |
| K-01 | Catalogue page title "Catelog" → "Catalogue" | **Title done** (REST, page 231). |
| K-01 | Slug `/catelog/` → `/catalogue/` + redirect | **Not done.** Page 231 is the WooCommerce shop page, and the footer "Catelog" link is a V3 widget the MCP can't edit. WordPress doesn't redirect old page slugs, and no redirect plugin is installed. Rule 8 says not to change the permalink until every link is updated and a redirect exists. Do the editor fixes below first; then I can change the slug and add a redirect. |
| F-10 | Home bullet "Eortless" → "Effortless" | **Blocked (V3)**: page 43, icon-list `51cf488`, item 2 |
| W-26 | Where to Buy "We oer" → "We offer" | **Blocked (V3)**: page 132, heading `e1a9a94` |
| W-21 | Where to Buy label "FEATURED PRODUCT" (#f1af1d) → "WHO WE ARE" (#f05a38, letter-spaced) | **Blocked (V3)**: page 132, heading `142ce73` |
| X-17 | Footer quick link "Catelog" → "Catalogue" | **Blocked (V3)**: footer template 30, icon-list `501b3e5`, item 3 |
| X-18 | Footer "Download Catelouge" → "Download Catalogue" | **Blocked (V3)**: footer 30, button `9649bf7` |
| X-19 | Footer "Lets Connect" → "Let’s Connect" | **Blocked (V3)**: footer 30, button `cec317d` |

### The six blocked fixes, in the Elementor editor (about 5 minutes)
1. **Templates → Saved Templates → footer (post 30) → Edit with Elementor.**
   - Icon List (Quick Link): item "Catelog" → `Catalogue`.
   - Green button: `Download Catalogue`.
   - Outline button: `Let’s Connect` (curly apostrophe ’).
2. **Pages → Home → Edit with Elementor.** In the Featured Product icon list, retype item 2 as `Effortless ‘Easy-Wring’ Drying Action`. Type the "ff" by hand; the ligature was lost.
3. **Pages → Where to buy → Edit with Elementor.**
   - Paragraph under the brand heading: "We oer" → `We offer`.
   - Label above the brand heading: text `WHO WE ARE`, colour `#F05A38`, letter spacing about 2px.
4. Update each page, then clear the LiteSpeed cache.

## Phase 1 – global layout and fonts (CSS, section 12 of `kk-polish.css`)

| ID | Fix | Result at 1920px (tested against live HTML with the snippet swapped in) |
|---|---|---|
| X-01 | Content width 1300 → ~1646px | All boxed sections, header, footer, catalogue and product templates now run **137px → 1783px**. The width grows smoothly from 1300px at 1440px; **1440px and narrower are unchanged** (70→1370 at 1440). |
| X-02 | Headings → Satoshi 900 | Every heading that used Albula Pro SemiBold or Aeonik Pro Bold is now **Satoshi 900**: Home section, card and value titles; Who We Are headings; the Where to buy brand heading. Buttons and footer links moved from Aeonik Pro to Satoshi. Form fields are untouched (they get Aeonik Pro in C-07). |
| X-03 | Body 22px, section headings ~34px (desktop) | Section headings **33.7px**: Home "Designing…", "Let’s find…", "One home…", featured title; Where to buy "Find Stores…" and the brand heading. Main paragraphs **22.2px**: Home intro and value texts, One Home intro and featured bullets; Where to buy paragraph; Who We Are intro. |
| X-04 | Heading green #00653f → #00623d | Done on all five headings that used it (Home ×2, Where to buy ×1, Who We Are ×2). |

Not changed in Phase 1 because a later phase gives them their own target:
- **Hero card:** Phase 4, H-04 to H-09.
- **Card descriptions:** S-09, 20px.
- **Contact heading and intro:** C-02 and C-03, 40px and 20px.
- **Who We Are display headings:** A-05 and A-08.
- **Buttons:** sizes and weights per phase.
- **Footer text sizes:** X-22.

Tablet (768px) and phone (375px) keep their current sizes. The font family change applies at every width, as in the design. No page scrolls sideways at any width.

## To apply
Replace the "Kress Kleen – site polish" block in `functions.php` with the new `kk-polish-snippet.php`, then clear the LiteSpeed cache.
