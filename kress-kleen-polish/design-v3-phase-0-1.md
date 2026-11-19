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

---

# Phase 2 – header, all pages (CSS, section 13 of `kk-polish.css`)

Done in CSS for the same reason as Phase 1: the header (template 25) is built from classic V3 widgets, which the MCP can't edit. All results below were measured at 1920px against the live pages with the new snippet swapped in.

| ID | Fix | Result at 1920px |
|---|---|---|
| X-05 | "KLEENING SINCE 2003" → Albula Pro Bold 16.8px, #b5b5b5 | Done. Phones keep their 10px size. |
| X-07 | Menu icon → three thin equal lines, ~36 × 19px | Done: **36 × 19px**, drawn in CSS. Tablet uses 28 × 15px and phones 26 × 14px. The icon still opens the side menu. |
| X-08 | ENGLISH first, then Arabic | Done (ENGLISH at 218px, Arabic at 310px). On phones they stack, ENGLISH on top. |
| X-09 | ENGLISH → Albula Pro Bold 16px | Done. |
| X-10 | Arabic → Janna LT Bold 17px, full white | Done. **The Janna LT Bold font is already loaded on the site** (Soharon Fonts in `functions.php`), so no asset is needed. |
| X-11 | Menu icon left edge → 135px | Done: **135px**. |
| X-12 | WHERE TO BUY right edge → 1784px | Done: **1784px**. |
| X-13 | Logo → ~364 × 203px | Done: **364 × 196px**. The logo file's own proportion gives 196px; the design's 203 includes the star. |
| X-14 | WHERE TO BUY → ~179 × 52px, Satoshi Bold 18.9px | Done: **183 × 52px**, Satoshi Bold 18.9px. |
| X-15 | Inner-page header → 310px | Done: **310px** including the marquee, on Where to Buy, Who We Are, Catalogue/category and product pages. |
| X-16 | Inner-page header colour → dark green #003820–#004326 | Done: dark green gradient over the existing mosaic tile image. This also fixes a bug where the top ~34px of inner-page headers was white, which made the white marquee text invisible. |
| W-02, K-04, P-01, A-03 | 9px white line between header and banner | Done on all inner pages (desktop). |

Tablet (820px) and phone (375px) were checked on every page. Header sizes there are unchanged, the header stays sticky on phones, and nothing scrolls sideways.

### Not changed (outside Phase 2 or waiting on Jameer)
- **X-06:** the marquee still shows on inner pages. The design has it on the home page only; this is in "Ask Jameer first".
- **Home header:** on the home page the marquee sits on a white band above the hero, so the grey text is hard to read. The design shows it on a dark bar over the hero. The brief has no fix line for this, so it's untouched. Say if you want it.
