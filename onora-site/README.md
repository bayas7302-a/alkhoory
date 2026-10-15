# ONORA Hotel Supplies → Elementor (atomic / V4)

Built from Figma `nTy7KmqdkNIQY27OPamlhi` (page "ONORA Website", node 49:2) on the fresh WordPress install at
https://soharon.co.uk/onora/ (Hello Elementor theme, Elementor 4.3.5 free, no Pro). Content comes from the Figma
frames, which adapt the copy from the reference site https://soharon.co.uk/dummy/.

| Page | WP post | Slug | Figma frame |
|------|---------|------|-------------|
| Home | 25 | `home` (set as front page) | 50:2 |
| About | 26 | `about-us` | 52:2 |
| Brands | 27 | `brands` | 53:2 |
| Products | 28 | `products` | 54:2 |
| Contact | 29 | `contact-us` | 55:2 |

All pages use the **Elementor Canvas** template (no theme header/footer); the header and footer are part of every page.

## Tools (`tools/`)
- `mcp.py TOOL ARGS.json|'{json}'` calls the Elementor MCP endpoint (`/wp-json/elementor/mcp/`). It reads credentials
  from the `my-blog-elementor` entry in `~/.claude.json` (or `ONORA_AUTH=user:app-password`). Nothing secret is stored in the repo.
- `upload.py FILE…` uploads images to the media library via the WP REST API and caches `{id,url,width,height}` in `tools/media.json`.
- `chrome.py POST_ID ACTIVE header|footer [--build]` generates/builds the shared header and footer.
  `header --build` replaces the whole page, so the build order is: header → sections → footer.
- `node tools/shot.js POST_ID OUT_PREFIX [WIDTH]` renders a signed preview and saves a full-page screenshot.

## Design tokens (kit global variables, use as `var(--label)`)
Colours: `on-navy` #0B2F62 · `on-navy-deep` #071F44 · `on-gold` #C9962E · `on-gold-soft` #E2BB6C · `on-gold-light` #F6D27A ·
`on-gold-dark` #8A5A08 · `on-ink` #16213A · `on-muted` #5F6675 · `on-cream` #F7F4EE · `on-line` #E6E0D6 · `on-white`.
Fonts: `on-serif` (Cormorant Garamond), `on-sans` (Montserrat).
Gold gradient: `linear-gradient(90deg, #A5740B 0%, #DEAF57 25%, #F1C96A 50%, #BE8826 75%, #A5740B 100%)`.

## Global classes
`on-section` (120px/100px padding, responsive) · `on-inner` · `on-bare` (padding 0) · `on-eyebrow-row` · `on-rule` (32×2 gold line) ·
`on-eyebrow` · `on-h1` · `on-h2` · `on-h3` · `on-lead` · `on-body` · `on-small` · `on-btn-navy` · `on-btn-gold` ·
`on-btn-outline-light` · `on-btn-outline-navy` · `on-link-arrow`.

## Section payloads
`sections/<page>-NN-<name>.json` hold the exact `elementor-build-composition` payload for each section, in page order.
Re-running them in order (header first, footer last) rebuilds a page from scratch.

## Style converter gotchas (Elementor 4.3.5)
Not saved: `white-space`, `row-gap`/`column-gap`, per-side border shorthands (`border-top: …`), `animation*`.
Use `border-width: 1px 0 0 0; border-style: solid; border-color: …` instead of `border-top`.
Not saved either: `pointer-events`, `box-sizing`, `max-width` on e-heading, `width: max-content`, `<br>` in headings, `-webkit-` prefixes.
Section anchors: pass `"_cssid": "<slug>"` in a container's element_config (Products uses this for `#chinaware` … `#indian-tableware`).

## Publishing / stale CSS
After a build on a **published** page the live HTML/CSS keeps the old version. Order that works:
build-composition → `elementor-publish-document` → `elementor-update-page-settings {"template":"elementor_canvas"}`;
clearing the CSS cache (`DELETE /wp-json/elementor/v1/cache`, done by `shot.js`) also helps.

## Rebuild scripts
`tools/<page>_build*.sh|.py` + generators (`home_build.py`, `about_gen.py`, `brands_gen.py`, `products_gen.py`, `contact_gen.py`)
regenerate the section JSON. Some scripts were written in a scratch folder; adjust their paths before reuse.

## Known gaps
- Contact form is visual only: the atomic form field widgets aren't registered on this site, so "Send enquiry" opens a mailto.
  A real form needs Elementor's atomic form elements enabled, or a form plugin.
- Contact FAQ answers 2–5 were written to fill the design (only Q1 has copy in Figma); have the client confirm them.
- Catalogue "Download" buttons open a mailto request; no PDFs exist yet.
- Gold rings are CSS circle borders, not the gradient SVG strokes.
