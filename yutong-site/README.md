# Yutong UAE site → WordPress / Elementor (atomic V4)

Built from Figma `zxyrIfPRXJd0H1AQisGmym` (YUTONG, Page 1) on https://soharon.co.uk/yutong/ via the
`my-blog-elementor` Elementor MCP server. Elementor 4.3.5 (free, no Pro).

## Theme: `theme/yutong-uae` (child of Hello Elementor)

The common header and footer live in the **Yutong UAE** child theme; pages use the
**Elementor Full Width** template and their Elementor content is only the page body.

- `header.php` / `footer.php` — header bar (logo, menu, search, Request a Quote, Al Khoory logo, native
  `<details>` mobile menu) and footer (logos, 4 menu columns, contact info, legal links).
- Menus (Appearance › Menus): locations `yutong-primary`, `yutong-footer-1..4`, `yutong-legal`;
  `tools/menus.py` creates them via REST. Built-in fallbacks render if a location is empty.
- Contact details (address, timing, e-mail, phone, map link, quote link, copyright):
  Appearance › Customize › *Yutong UAE: header & footer*.
- `assets/css/theme.css` — header/footer styles and all Contact Form 7 styles; CF7 auto-`<p>` is off.
- Install/update: upload `theme/yutong-uae.zip` in Appearance › Themes › Add New › Upload (replace
  current), activate, then run `tools/theme_live.py` (assigns menus, switches pages to Full Width,
  rebuilds every page without its own header/footer, runs the checks). `parts.THEME_CHROME` controls
  whether page builds include header/footer.

| Page | WP id | slug | Figma frame |
|---|---|---|---|
| Home | 8 | `home` (front page) | 1:2213 YUTONG — Redesign v1 |
| Models | 9 | `models` | 1:288 Model |
| About | 10 | `about` | 1:955 About |
| Contact | 11 | `contact` | 1:1231 Contact |
| Awards | 12 | `awards` | 1:1454 Awards |
| News | 13 | `news` | 1:1622 News |
| Library | 14 | `library` | 1:1769 Library |
| Ownership & Support | 15 | `ownership-support` | 1:1906 Ownership & Support |
| ~~Model T12E~~ | — | — | 1:595 — not built yet (client will supply models later) |

## Layout

- `tools/lib.py` — tiny node builder (`flex`, `div`, `h`, `p`, `img`, `payload`); `.c('class')` attaches global classes.
- `tools/parts.py` — shared sections: `header(active)`, `page_hero(...)`, `btn`, `eyebrow`, `title`/`hl`,
  `news_section`, `quote_section`, `cta_section`, `glance_section`, `footer`.
- `pages/<page>.py` — one module per page: `POST`, `SLUG`, `SECTIONS = [(name, fn -> [nodes])]`.
- `tools/run.py <page>` — generate payloads → send each section (first one replaces the page) →
  publish → `finalize.py` → live screenshots at 1440 and 390.
- `tools/assets.py name=URL ...` — downloads Figma MCP asset URLs, rasterises SVG to 3x PNG
  (WordPress refuses SVG uploads), converts big opaque PNGs to JPEG, uploads to the media library,
  dedupes by hash into `$YT_WORK/assets/map.json`. Use the name with `img('…', name)` / `bg(name)`.
- `tools/check.py` — smoke test of every live page at 1440/820/390: HTTP 200, no horizontal overflow,
  visible images loaded (hidden ones, e.g. closed tabs, are skipped), CF7 form present, internal links.
- `tools/rhythm.py` — vertical-spacing audit at 1440/820/390: gaps between same-colour sections must equal the
  standard (110 / 80 / 64px); a section between different colours needs equal space above and below.
  `tools/spacing.py [page/]` prints the raw per-section measurements. Use `responsive_pad()` in parts.py for
  sections whose desktop padding has a 0 side.
- `pages/articles.py` — generates the news/award detail pages (creates missing WP pages via `ensure_page`).
- `tools/finalize.py <post_id>` — the MCP composer only knows atomic widgets, so classic widgets are
  injected into placeholder slots through the REST API: `Quote Form Slot` / `Contact Form Slot` get the
  Contact Form 7 shortcode (form 44 "Yutong Enquiry"; the Contact page uses form 243 "Yutong Contact" with
  phone/model/seats/consent; styles are in the theme); `Anchor <id>` divs get `<span id="<id>">`.
  It then clears Elementor's CSS cache (REST meta writes don't regenerate atomic CSS).
  **Order matters:** publishing re-saves from Elementor's own copy and drops injected widgets, so
  always publish first, finalize last (`run.py` does this).

## Conventions / gotchas

- Inline `style` is converted to native atomic styles; unsupported things (`white-space`, `& span`
  descendant rules, `[aria-selected]`) are silently dropped. Put those in **global classes**
  (raw CSS, nesting works): `yt-hl-blue`, `yt-hl-sky` (colour `<span>` words in headings — inline
  `style` on spans is stripped), `yt-nowrap`, `yt-lift` (card hover), `yt-zoom`, `yt-cover`, `yt-tab`.
- Two-value `gap`, `align-items: baseline`, `background-position: right center` are rejected — use
  single values.
- In a column flex on mobile, `flex: 1 1 0` collapses cards with `overflow: hidden` — add
  `@media(--mobile) { flex: 0 0 auto; }`.
- Buttons are linked flexboxes (label + arrow image), `align-self: flex-start`.
- Content width: 1250px site-wide. `payload()` strips the desktop `120px` side padding from every top-level section and
  attaches the global class `yt-wrap` (`padding-inline: max(20px, (100% - 1250px)/2)`); header bar max-width 1250px.
- Breakpoints: `@media(--tablet)` (≤1024) and `@media(--mobile)` (≤767).
- Design tokens (global variables): `yt-navy #072b52`, `yt-blue #0160b4`, `yt-sky #2c84cb`,
  `yt-mist #f2f6fb`, `yt-ink #323232`, `yt-white`; fonts `yt-display` (Lexend), `yt-body` (Poppins).
- Credentials are read from the `my-blog-elementor` entry in `~/.claude.json`; nothing secret is in this repo.
- `$YT_WORK` (default: the session scratchpad) holds `wpmcp.sh`/`call.sh` (raw MCP JSON-RPC over curl),
  payloads, screenshots and the asset map.

## Content notes

- All copy and most photos now come from https://en.yutong.com/ (client-approved), adapted to the
  UAE-distributor voice; UAE contact details (Showroom 12, Al Quoz, 04 3146235, info@subaru-uae.com) are used site-wide.
- **No links leave the soharon domain** (only the Google Maps "View Location" directions link).
  `tools/offsite.py` scans every published page for off-domain URLs.
- Every News card and Award card opens its own on-site detail page, built by `pages/articles.py`
  (`run.py articles`): children of News (13) and Awards (12) at `/news/<slug>/` and `/awards/<slug>/`.
  The lead paragraph is the card summary; the body, highlights and quote are dummy (lorem) copy to replace.
- Library videos are 720p web transcodes uploaded to the media library (`yutong-video-1..6.mp4`).
- Models page catalogue and Home model tabs use real models/specs from en.yutong.com product pages;
  edit `FEATURED` in `pages/home.py` to swap a featured model.
- Still static (no backend): Models quick-contact WhatsApp (links to Contact), Subscribe banner,
  footer legal links (`#`). Model T12E detail page not built (models to be supplied later).
