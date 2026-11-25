# Kursus Kahwin Malaysia: Dark & Gold theme

Applied site-wide on kursuskahwinmalaysia.com through the Elementor kit (post 391).

## What was changed
- **Global colours** (Site Settings → Global Colors): Primary `#D4AF37`, Heading gold `#F5D98B`, Text cream `rgba(239,230,210,.76)`, Accent `#F0C75E`, Deep gold `#8C6B1F`, Dark surface `#1C1811`, Ivory `#FFF8EA`, Dark section `#16130E`.
- **Global fonts**: Cinzel (display/headings, matches the logo), Jost (body), Cormorant Garamond (italic accents).
- **Site Settings → Custom CSS**: contents of `dark-gold-theme.css` (dark sections, gold gradient headings and buttons, gold-framed images, dark cards, pricing, form, FAQ, footer).
- **Logo**: `site_logo` → media 1099 (`kkm-logo-monogram-sm.png`), `site_icon` → media 1101 (`kkm-icon-512.png`). Previous values: logo 405, icon 105.

## Undo
- Kit: Elementor revision **1102** (`elementor/manage-revisions` → restore, post 391), or in WP Admin → Elementor → Site Settings → History.
- Logo: set `site_logo` back to 405 and `site_icon` to 105 (Appearance → Customize → Site Identity).
