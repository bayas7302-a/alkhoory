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

---

# Phase 3 – footer, all pages (section 14 of `kk-polish.css` + footer links in `kk-polish.js`)

The footer (template 30) is built from classic V3 widgets, which the MCP can't edit, so this is CSS plus a small script. Measured at 1920px against the live pages with the new snippet swapped in. Design positions were read from the PDF rendered at 2×.

| ID | Fix | Result at 1920px |
|---|---|---|
| X-21 | "Contact" plain text → link to the contact form | Done (script). It links to `home/#contact`. The home page contact section gets the anchor `contact`, and the sticky mobile header doesn't cover it. |
| X-22 | Quick links Satoshi Regular 19px; "QUICK LINK" Satoshi Bold 18px | Done. The separators now have the design's wider spacing (about 26px). The block starts at **604px**, as in the design. |
| X-23 | Buttons 52px tall, ~211 / 272 / 245px, Satoshi Bold 19px | Done: **211 / 272 / 211 × 52px**, Satoshi Bold 19px, 14px apart, ending at 1783px. Note: the design PDF measures "Let’s Connect" at **~211px**, not 245px; I followed the PDF. |
| X-24 | Long thin line arrow | Done (drawn in CSS). It's also used on tablet and phone, a bit shorter there. |
| X-25 | "Let’s Connect" → contact form | Done (script): `home/#contact`. "Download Catalogue" still links to `#` because the catalogue PDF is in "Needs assets". |
| X-26 | Logo ~285px wide | Done: **285px**, left edge at 160px, top 21px. In the PDF the star sits just inside the footer's top edge (pages 1 and 2), so I matched the PDF. |
| X-27 | Copyright Satoshi Regular 19px | Done, centred under the buttons as in the design. |
| X-28 | Footer height 209 → 222px | Done: **222px**. |

Tablet (820px) and phone (375px) keep the centred stacked footer from earlier, now with the long arrow. Nothing scrolls sideways.

The links are filled in by the script only where they are missing (no link or `#`). A link set later in the Elementor editor takes over automatically. If you prefer, set them in the editor now:
- Footer → Icon List → "Contact" → link `/#contact`.
- "Let’s Connect" button → `/#contact`.
- Home page → contact section (`1c24237`) → Advanced → CSS ID `contact`.

### Waiting on others
- **X-17, X-18, X-19 (Phase 0 text):** "Catelog", "Catelouge" and "Lets Connect" still need the editor edits.
- **X-20 / D-04 (Ask Jameer):** which quick-link set to use on inner pages.
- **X-25 (Needs assets):** the catalogue PDF for "Download Catalogue".

---

# Phase 4 – Home page (section 15 of `kk-polish.css` + "home page" block in `kk-polish.js`)

All home sections are classic V3 widgets, and the hero is the `[kk_scroll_hero]` shortcode, so this is CSS plus a small script. Measured at 1920px against the live page with the new snippet swapped in. Design positions were read from the PDF. Sizes and layout apply on desktop only; tablet (820px) and phone (375px) keep their layouts and only pick up the new colours. Live page height at 1920: **4963px** (design 4967px).

### Hero (`section.kk-sh`)
| ID | Result |
|---|---|
| H-03 | Overlay removed. The photo is at natural brightness with a dark fade only at the very top. |
| H-04 | Card left edge **137px**, bottom **248px** above the hero bottom. |
| H-05 | Card **555 × 296px**, 30px radius, thick soft frosted edge (light inner rim and soft shadow). |
| H-06 | "K-8051" Satoshi Regular 22px, no letter spacing. |
| H-07 | Title Satoshi 900, 33.7px. |
| H-08 | Sub-line 22px. |
| H-09 | LET’S DISCOVER **200 × 38px**, Satoshi Regular 16px, wide letter spacing. |

The card change applies to all 11 scroll cards. H-01/02/10/11 and N-05 are in "Ask Jameer first": the hero still scrolls with the video, "SCROLL" label and progress bar.

### Retailer logo strip (`287ec07`)
| ID | Result |
|---|---|
| L-01 | Background white. |
| L-02 | **Not changed.** The yellow line is the hero's progress bar (H-11, "Ask Jameer first"). It goes when H-11 is answered. |
| L-03 | Full width, **60px → 1860px**. |
| L-04 | Logos up to 250px wide (6 × 283px slots). |
| L-05 | **170px** tall. |

L-06 (carousel) is ask-first, so it's kept. L-07 (clean logo files) is in Needs assets; the current screenshots still show their grey tiles.

### Green strip (`b12f160`)
| ID | Result |
|---|---|
| G-01 | **471px**. |
| G-02 | Background colour **#0c4c31**, so there's no blue flash while the image loads. |
| G-03 | Satoshi Black 33.7px. |
| G-04 | Breaks before "TO KLEEN". |
| G-05 | 22.2px; lines end at "thoughtful" and "performance to". |
| G-06 | READ MORE **200 × 38px**, Satoshi Regular 16px, wide letter spacing. |
| G-07 | Left block starts at **137px**. |
| G-08 | Column titles Satoshi Black 29px. |
| G-09 | Column text 22.2px. |
| G-10 | Breaks as in the design. |
| G-11 | Columns 863–1171 / 1171–1511 / 1511–1783, so centres at **1017 / 1341 / 1647**. |
| G-12 | Dividers 1px. |

### Solution cards (`975d254`)
| ID | Result |
|---|---|
| S-01 | Satoshi Black 33.7px. |
| S-02 | **772px**. |
| S-03 | Cards **137 → 1783px**, each **321px** wide. |
| S-04 | Card **477px**, photo **266px**. |
| S-05 | **66px** badge with a 4px white ring, centred on the photo edge. |
| S-07 | Titles Satoshi Black 32px. |
| S-08 | Text indent **78px**. |
| S-09 | Description 20px. |
| S-10 | Breaks as in the design (Mop, Scrub, Dust, plus Sweep and Wipe to match the PDF). |
| S-11 | EXPLORE Satoshi Black 17.7px, letter-spaced, followed by ">". |
| S-12–S-16 | EXPLORE colours #f05a38 / #00915d / #f1af1d / #45a3d7 / #707070 (all widths). |
| N-07 | Sweep → `/solution/sweep/`, Mop → `/solution/mop/` (set by the script; both pages exist). For a permanent fix, change the two EXPLORE links in the editor. |

S-06 (icon artwork) is in Needs assets.

### One Home (`7149c5d`)
| ID | Result |
|---|---|
| O-01 | Background #f0f8f5. |
| O-02 | **793px**. |
| O-03 | Satoshi Black 33.7px. |
| O-04 | "ONE HOME." / "MANY CLEANING" / "CHALLENGES". |
| O-05 | 22.2px. |
| O-06 | Area buttons **250 × 38px**, #adadad border, Satoshi Medium 21px, 10px gap. |
| O-07 | House illustration **585 → 1810px**. |
| O-10 | On desktop the pop-up opens **to the left** of its hotspot, joined by a thin line. If there's no room on the left it falls back to the old placement. |
| O-13 | Hotspots are flat green with a thin white ring, no glow or pulse. |
| O-14 | **61px** white gap before Featured Product. |

O-08 is in Needs assets. O-09/11/12 are ask-first: the Kitchen pop-up isn't open by default, the × stays, and the active hotspot still turns red.

### Featured product (`8e91e85`)
| ID | Result |
|---|---|
| F-01 | Full page width. |
| F-02 | **600px**. |
| F-03 | Left photo **722px** wide. |
| F-05 | Panel #1f2324. |
| F-06 | Label 16px, letter-spaced. |
| F-07 | Title Satoshi Black 33.7px. |
| F-08 | "KRESS KLEEN “CYCLONE” / EASY-WRING SPIN / MOP BUCKET SET". |
| F-09 | Tagline Satoshi Bold 23px. |
| F-11 | Bullets 22.2px with plain "•". |
| F-12 | Button #f05a38. |
| F-13 | Button **275 × 39px**, Satoshi Regular 16px, letter-spaced. |
| F-14 | **Not done: there's no Cyclone product on the site** (WooCommerce only has K-8051). The button still links to `#`. Add the product, then link it in the editor. |
| F-15 | Right image **450 × 550px**. |

F-04 (extra slides and arrows) is in Needs assets. F-10 ("Eortless") is a Phase 0 editor fix.

### Contact form (`1c24237`)
| ID | Result |
|---|---|
| C-01 | **761px**. |
| C-02 | Satoshi Bold Italic 40px. |
| C-03 | Intro 20px. |
| C-04 | Line 2 ends "Kress Kleen,"; line 3 is "get in touch with our team." |
| C-05 | Two **410px** columns. |
| C-06 | Field borders #fae3af. |
| C-07 | Fields and Submit Aeonik Pro Regular 18px; placeholder #808080. |
| C-08 | Photo box **850 × 735px**, right edge at **1840px**. |
| C-09 | Photo starts above the heading and runs down to the footer. |

### About the script
- The line breaks are `<br class="kp-br">` added on page load and shown on desktop only. Phone and tablet wrap the text naturally.
- Card links and the pop-up side are set on page load and don't change anything stored in WordPress.

---

# Phases 5–8 and Phase 0 completion (sections 16–19 of `kk-polish.css` + script blocks 1a, 1d–1g)

Measured at 1920px against the live pages with the new snippet swapped in. Every page was also checked at 1440, 820 and 375px: no page scrolls sideways, and the large display sizes scale down proportionally below 1920.

## Phase 0 – now complete in the snippet
The six V3 text fixes weren't made in the editor yet, so the script now corrects the exact typos on the page. If you fix them in Elementor later, the script simply finds nothing to change.

| ID | Fix |
|---|---|
| X-17 / N-01 | "Catelog" → "Catalogue" |
| X-18 | "Download Catelouge" → "Download Catalogue" |
| X-19 | "Lets Connect" → "Let’s Connect" |
| W-26 | "We oer" → "We offer" |
| F-10 | "Eortless" → "Effortless" |
| W-21 | Label reads **WHO WE ARE** in #f05a38, letter-spaced |

**K-01 slug:** the snippet adds a 301 redirect from `/catelog/` to `/catalogue/`. It only runs after the slug is changed, so it's safe to paste first. Once the snippet is live, change the slug in Pages → Catalogue → Slug. The menu updates by itself, and the hard-coded footer link is caught by the redirect. I didn't change the slug from here because the redirect only exists once the snippet is pasted.

## Phase 5 – Where to Buy
| ID | Result at 1920 |
|---|---|
| W-01 | Banner **325px**. |
| W-03–W-06 | Breadcrumb #75c4a8, Satoshi Regular 20.5px, thin chevron with wide gaps, no underline. |
| W-07 | Title Satoshi Bold 95px, letter-spaced. |
| W-08 | Breadcrumb and title both at **137px**. |
| W-10 | "Find Stores Near You" Satoshi Black 33.7px, #00623d. |
| W-11 | 45px gap to the dropdowns. |
| W-12 | Pale-yellow dropdown borders. |
| W-13 | Dropdowns and Apply in Aeonik Pro 18px. |
| W-17 | Map **1507 × 830**. |
| W-19 / W-20 | Logo strip 270px, full width, large logos (grey background kept, as in the design). |
| W-21 | Label "WHO WE ARE". |
| W-22–W-24 | Brand heading Satoshi Black 33.7px #00623d, line 1 ends "brand", 40px line height. |
| W-25 / W-27 | Paragraph 22.2px with the design's three lines. |
| W-28 | 116px gap. |

W-14 is ask-first. W-15, W-16 and W-18 need the My Maps embed (assets). W-09 is ask-first.

## Phase 6 – Catalogue (and category pages)
| ID | Result at 1920 |
|---|---|
| K-02 | Banner photo is now the **green floor wiper on tiles** (found in the media library). |
| K-03 | Banner **325px**. |
| K-05–K-07 | Breadcrumb 20.5px, #75c4a8, chevron; title Bold 95px; both at **137px**. |
| K-08 | Sidebar **604px**. |
| K-09 | Sidebar text at **162px**. |
| K-10–K-13 | On desktop all three lists are open, with no badges, no +/− icons and no dividers. (The phone filter drawer is unchanged.) |
| K-14 | Labels Satoshi Regular 16px, letter-spaced, #f05a38. |
| K-15 | Links Satoshi Black 33.7px, #00623d, **46px** pitch. |
| K-16 | Yellow underline on the active filter. |
| K-18 / K-19 | Cards **488 × 570**, 95px between the columns. |
| K-20 | Photo fills the card width, broom cropped large. |
| K-21–K-23 | Item code 17px #00915d; name 23px on two lines; DISCOVER 17px. |

K-17 (three more products) is in Needs assets.

## Phase 7 – Product page (K-8051)
| ID | Result at 1920 |
|---|---|
| P-03 | Left floor panel **604px**. |
| P-04 | Product image **826px**, from 172px. |
| P-05 | Item code 61px under the header. |
| P-06–P-09 | 17px / 23px / bullets 19px on a 30px pitch / label 17px. |
| P-10–P-14 | Table 16.5px; pills 33px tall on a 41.6px pitch; labels all **149px**; values all **222px** and white. |
| P-15 | White 96px square with a down chevron at **x 1689**, level with the table. It scrolls to the highlight images. |
| P-16–P-18 | Feature images **725px**, left-aligned with the text at 1060px, with a soft shadow. |

P-02 (wood floor photo) is in Needs assets; the library only has a grey floor.

## Phase 8 – Who We Are
| ID | Result at 1920 |
|---|---|
| A-01 | Banner photo is now the **white string mop** (from the library). |
| A-02 | Banner 325px. |
| A-04 | Breadcrumb and title as on Where to Buy. |
| A-05 / A-06 | 92.5px Satoshi Black; green #00915d. |
| A-07 | Clean SVG down arrow, 95px. |
| A-08 | 62.5px. |
| A-09 | 22.2px. |
| A-10 | Exploded broom **600 × 800**, from **1275px**. |
| A-11–A-14 | Photos **375 × 307**; titles Satoshi Black 32px; underlines **99 × 5**; green #00915d. |
| A-16 / A-17 | Section **515px**, statement Bold 40px on four lines. |
| A-18 | Clean SVG four-point stars. |
| A-20–A-22 | Section **681px**, large line Bold 154px, text Bold 40px with line 1 ending "affordable doesn’t". |
| A-24 / A-25 | Section **587px**, Black 53px. |
| A-26–A-28 | 223px, #4f4f4f, section **414px**. |

Page height at 1920: **4439px** (design 4454px).

- **A-15:** the dark mop-strand photo isn't in the media library, so the statement section keeps its current photo.
- **A-23 (dusk skyline):** needs the asset.
- **A-19:** ask-first.

## Everything still open (all need you or Jameer)
- **Ask Jameer first:** H-01/02/10/11 (scroll hero, and with it L-02), N-05, L-06, X-06, X-20/D-04, O-09/11/12, W-14, W-09, A-19, N-04, D-02.
- **Needs assets:**
  - Catalogue PDF (X-25)
  - Retailer logos (L-07, W-20 files)
  - Card icons (S-06)
  - Transparent house (O-08)
  - Featured slides (F-04)
  - Cyclone product (F-14)
  - Three products (K-17)
  - Wood floor (P-02)
  - Mop-strand photo (A-15)
  - Skyline (A-23)
  - My Maps embed (W-15/16/18)
  - Real email/phone and socials (N-02/N-03)
  - READ MORE target (N-06)
- **Your step:** after pasting the snippet, change the Catalogue slug to `catalogue` (K-01).
