# Kursus Kahwin Malaysia: Dark & Gold theme

Applied site-wide on kursuskahwinmalaysia.com through the Elementor kit (post 391).

## What was changed
- **Global colours** (Site Settings → Global Colors): Primary `#D4AF37`, Heading gold `#F5D98B`, Text cream `rgba(239,230,210,.76)`, Accent `#F0C75E`, Deep gold `#8C6B1F`, Dark surface `#1C1811`, Ivory `#FFF8EA`, Dark section `#16130E`.
- **Global fonts**: Cinzel (display/headings, matches the logo), Jost (body), Cormorant Garamond (italic accents).
- **Site Settings → Custom CSS**: contents of `dark-gold-theme.css` (dark sections, gold gradient headings and buttons, gold-framed images, dark cards, pricing, form, FAQ, footer).
- **Logo**: `site_logo` → media 1099 (`kkm-logo-monogram-sm.png`), `site_icon` → media 1101 (`kkm-icon-512.png`). Previous values: logo 405, icon 105.

## v2 section layouts (CSS only, content still editable in Elementor)
- **Contact form**: single "Borang Tempahan" booking card, 2×2 field grid, full-width message and button.
- **Pricing**: split card with a gold price panel on the left and an "Apa Yang Termasuk" checklist plus CTA on the right (stacks on tablet/mobile).
- **FAQ**: numbered 01–05 list with +/× toggles and a gold bar on the open question.

## v3 "Tentang Kami" section
Open editorial layout: no boxed panel, lead paragraph with a gold rule, the four points as 2×2 tiles with gold ✦ seals, an arrow on the button, the photo with an offset gold frame and dot pattern, and "A Cemerlang" as a round gold seal on the photo corner.

Note: Elementor strips backslashes from custom CSS, so use literal characters (✦ →) in `content:`, not escapes like `\2726`.

Note: `elementor/update-page-settings` on the kit **replaces** the settings rather than merging them.
Always send the full set (colours, typography, body background, custom_css) together.

## v4 services section + burgundy accents
- The "Tentang Kami" section on the landing page (822) and Home (388) now holds the services write-up ("Perkhidmatan Kami": 01 Kursus Pra Perkahwinan Islam, 02 Bantuan Borang Perkahwinan). The old bullet list and the "A Cemerlang" badge were removed.
- The photo is replaced by the "Isi Borang Kahwin RM40" poster (media 1115). The button reads "Hubungi Kami Hari Ini" and opens WhatsApp (60147184475).
- Content was written to `_elementor_data` via the WP REST API by `scripts/update_services_section.py`. Pre-change copies are in `backups/`.
- Burgundy (#7A1424 → #3D0812) added from the poster: hero price card, trust strip, pricing plaque, form header, FAQ open state, section glows, footer.

## v5 refinements
- Services: no boxes; large outlined numerals 01/02, hairline dividers, 17px text, tagline as an italic pull-quote.
- Kenapa Pilih Kami: ruled 3×2 grid with outlined numbers, thin gold icon rings, and a wine→gold underline on hover. The blob SVGs are hidden.
- 5000+ badge: single round wine seal with a slowly rotating dashed gold ring.
- Testimonials: editorial quotes (gold side rule, wine quote mark, 22px serif). The red top border is removed.
- Animated Islamic lattice (inline SVG) drifting slowly behind "Kenapa Pilih Kami", testimonials, the trust strip and the footer, plus a slow gold shimmer on section titles. Both are disabled for prefers-reduced-motion.

## v6
- Fixed the services/poster section: it is a boxed container (`.e-con-inner`), so `>` child selectors never matched. The poster now starts level with the heading and stays in view (sticky) while the text scrolls. Columns are 56/44 on desktop.
- One button style site-wide: square corners, gold, 56px tall, Cinzel 13px uppercase.

## v7 services layout
The services section (af68fa7) is restructured by `scripts/restructure_services_v7.py`:
centred header → row 01 (service text | framed "Mengapa Memilih Kami?" panel) → row 02 (poster | service 02, tagline, WhatsApp button).
Note: containers take their CSS class from `css_classes`; widgets use `_css_classes`.
The pre-v7 page data is in `backups/page-<id>-elementor_data-v4.json`.

## v8
- Header logo: horizontal lockup (KK monogram + "KURSUS KAHWIN / MALAYSIA" cropped from the original artwork), `assets/kkm-logo-horizontal*.png`, media 1126, now the WordPress `site_logo` (was 1099).
- Contact details: rows with square wine icon tiles, gold labels and cream values. This fixes values made invisible by the title shimmer.

## v9 hero spacing
Hero (7a621be): 48px top/bottom padding (32px tablet, 22/28px mobile). On desktop it fills the screen under the header with the cards centred.
Note: a boxed container's `.e-con-inner` inherits `--padding-*` vars, so set real padding on the outer container and zero it on the inner one.

## v10: fix blank sections on phones with Reduce Motion
The v5 `prefers-reduced-motion` rule disabled ALL animations. Phlox scroll-reveal widgets (`.aux-appear-watch-animation`) start at opacity 0 and only become visible through their animation, so on iPhones with Reduce Motion on, 54 widgets stayed invisible (the client's video).
Now the rule only stops our decorative motion and forces reveal widgets visible.
Testing tip: never use `?s=` as a cache-buster, because it is WordPress search and returns a 404 page.

## Undo
- Page content: POST `backups/page-<id>-elementor_data.json` back to the `_elementor_data` meta of page 822 / 388.
- Kit: revision **1133** = before v10; **1131** = before v9; **1129** / **1127** = before v8 tweaks; **1124** = before the mobile centring fix; **1122** = before v7; **1120** = before v6; **1118** = before v5; **1116** = before v4 burgundy; **1112** = before the v3 about-section fix; **1110** = before v3; **1108** = v1 theme (before the v2 layouts); revision **1102** = original pink theme (`elementor/manage-revisions` → restore, post 391), or in WP Admin → Elementor → Site Settings → History.
- Logo: set `site_logo` back to 405 and `site_icon` to 105 (Appearance → Customize → Site Identity).
