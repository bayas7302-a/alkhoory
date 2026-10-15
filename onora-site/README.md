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

**Theme:** the custom `onora` theme (`theme/onora/`) supplies the header (utility bar, sticky nav, mobile burger menu),
the footer, the 1350px layout and the Products tab filtering. Pages use the theme's default template and contain only
their content sections. Menus can be managed in Appearance → Menus (locations "Main menu" and "Footer collections");
until assigned, the theme falls back to the built-in links. Contact details live in `onora_contact()` in `functions.php`.

**Width:** content is 1350px wide. Every section root uses side padding `max(40px, calc((100% - 1350px) / 2))`
(40px on tablet, 20px on mobile); the theme uses `.onora-container` with the same maths.

## Tools (`tools/`)
- `mcp.py TOOL ARGS.json|'{json}'` calls the Elementor MCP endpoint (`/wp-json/elementor/mcp/`). It reads credentials
  from the `my-blog-elementor` entry in `~/.claude.json` (or `ONORA_AUTH=user:app-password`). Nothing secret is stored in the repo.
- `upload.py FILE…` uploads images to the media library via the WP REST API and caches `{id,url,width,height}` in `tools/media.json`.
- `rebuild.py PAGE…` replays `sections/<page>-NN-*.json` (first one replaces the page), publishes and sets the template.
- `deploy_theme.py` pushes `theme/onora` to the server and activates it. WordPress has no theme-upload API, so it
  needs the **Code Snippets** plugin active (install it via `POST /wp/v2/plugins {"slug":"code-snippets","status":"active"}`);
  the script adds a temporary snippet, triggers it once and deletes it. Remove the plugin afterwards.
  Alternatively zip `theme/onora` and upload it in Appearance → Themes → Add New → Upload.
- `revise.py` applies the content/width revisions (idempotent); `products_tabs.py` generates the Products tab bar.
- `node tools/live_shots.js OUT_DIR [widths…]` screenshots the five live pages.
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
`tools/rebuild.py <page>` rebuilds a page from them.

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

## Contact form (Contact Form 7)
The quotation form is the CF7 form **"ONORA quotation"** (id 291). Its markup and email body live in the theme
(`theme/onora/cf7/quotation-form.txt`, `quotation-mail.txt`); `functions.php` saves them into CF7 once per theme
version with `wpcf7_save_contact_form()` (CF7's REST API returns success without saving), and injects the form into the
Elementor element with ID `onora-quote-form` on the Contact page. Mail goes to `onora_contact('email')` with
Reply-To set to the enquirer. Styles: "Contact Form 7" block in `assets/css/theme.css`.
Edits made to the form in WP Admin → Contact are overwritten on the next theme version bump; change the .txt files instead.
Delivery depends on the server's PHP mail; install an SMTP plugin if test enquiries don't arrive.

## Brands filter
The chips (`#brand-filters`, links `#tableware`, `#cutlery`, …) filter the cards in `#brand-grid` (card ids `brand-<slug>`).
Which brand belongs to which category is the `BRAND_CATEGORIES` map in `theme/onora/assets/js/theme.js`; the
"Looking for another brand?" card always stays. Links like `/brands/#lighting` open pre-filtered.

## Products tabs
`sections/products-02-tabs.json` is a nav with ids `product-tabs` / `product-tabs-track`: All + 12 collections in one line
(scrolls sideways on phones). `theme/onora/assets/js/theme.js` makes it sticky under the header and filters the collection
sections (ids `#chinaware` … `#indian-tableware`); links like `/products/#cutlery` open that collection only.

## Known gaps
- Contact FAQ answers 2–5 were written to fill the design (only Q1 has copy in Figma); have the client confirm them.
- Gold rings are CSS circle borders, not the gradient SVG strokes.
