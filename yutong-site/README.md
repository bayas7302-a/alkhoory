# Yutong UAE site → WordPress / Elementor (atomic V4)

Built from Figma `zxyrIfPRXJd0H1AQisGmym` (YUTONG, Page 1) on https://soharon.co.uk/yutong/ via the
`my-blog-elementor` Elementor MCP server. Hello Elementor theme, Elementor 4.3.5 (free, no Pro),
every page on the **Elementor Canvas** template (header/footer are built into each page).

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
  images loaded (images hidden at a breakpoint show as false positives), CF7 form present, internal links.
- `tools/finalize.py <post_id>` — the MCP composer only knows atomic widgets, so classic widgets are
  injected into placeholder slots through the REST API: `Quote Form Slot` / `Contact Form Slot` get the
  Contact Form 7 shortcode (form 44 "Yutong Enquiry"; the Contact page uses form 243 "Yutong Contact" with
  phone/model/seats/consent + `tools/form-contact.css`) + `tools/form.css`; `Anchor <id>` divs get `<span id="<id>">`.
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

## Content still to come from the client

- Placeholder copy kept verbatim from Figma: Awards grid ("Lorem ipsum dolor" + Subaru text), hero intros,
  News/Library headings, About accordion items 02–06, Contact enquiry panel.
- Copy written during the build where Figma had none (replace when real copy arrives): Home model tabs
  other than E-bus, Models page Battery/E-Drive tabs, Ownership "Keep Your Fleet Moving" items 02–04.
- Contact page details are Yutong HQ (Zhengzhou, +86 numbers) as designed; the footer/quote use the UAE details.
- Low-resolution source photos in Figma: Home hero (716px), Models hero (768px), Ownership hero, About history.
- Static UI with no backend yet: Models search/filters, Subscribe field, News/Library filter pills and pagination,
  Library downloads (link to Contact). Footer legal links are `#` until those pages exist.
- Model T12E page: not built (models to be supplied later).
