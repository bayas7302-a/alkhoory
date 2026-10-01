# Al Khoory Automobiles — WordPress + Elementor

Home page built from the Figma design (`Home` frame, file `TykzoXBLBXI7y7NX26bYFB`, node `77:1375`) for
https://soharon.co.uk/alkhoory/ using **Elementor (free) 4.x** atomic widgets.

## What's here

| Path | Purpose |
| --- | --- |
| `alkhoory-theme/` | Custom classic theme: header + footer from the design, design tokens, mobile menu. |
| `dist/alkhoory-theme.zip` | Ready-to-upload build of the theme. |
| `tools/elementor-home/` | Script that (re)builds the Home page through the Elementor MCP endpoint. |

## Installing the theme

1. WordPress admin → **Appearance → Themes → Add New → Upload Theme**.
2. Upload `dist/alkhoory-theme.zip`, then **Activate**.

Free Elementor has no Theme Builder, so the header and footer come from the theme. Everything between
them on the Home page is Elementor content.

### Theme settings

- **Appearance → Customize → Al Khoory Theme Options**: header button label/URL, group logo,
  footer logo, footer text, phone, email, address, copyright (`{year}` is replaced automatically).
- **Appearance → Customize → Site Identity**: header logo (defaults to the bundled Al Khoory logo).
- **Appearance → Menus**: assign menus to *Primary (Header)*, *Footer: Quick Links*, *Footer: Our Brands*,
  *Footer: Services* and *Footer: Legal*. Until a menu is assigned, each location shows the links from
  the design.

## Elementor setup on the site

- Page **Home** (ID 42) is published, uses the **Elementor Full Width** template and is set as the
  static front page.
- Global variables (Elementor → Site Settings / Variables): `ak-blue`, `ak-navy`, `ak-deep`, `ak-ink`,
  `ak-muted`, `ak-line`, `ak-soft`, `ak-sky`, `ak-icon-bg`, `ak-card-grey`, `ak-white`,
  `ak-font-heading` (Poppins), `ak-font-body` (Archivo).
- Global classes: `ak-section`, `ak-inner`, `ak-heading-group`, `ak-eyebrow`, `ak-h2`, `ak-lead`,
  `ak-body`, `ak-card`, `ak-card-title`, `ak-btn` (+ `ak-btn-primary`, `-light`, `-outline-light`,
  `-outline-dark`, `-arrow-dark`, `-arrow-white`), `ak-link-arrow`, `ak-check-item`,
  `ak-icon-wheel-blue`, `ak-icon-wheel-white`. Reuse these on new pages to stay on-design.
- All images (photos, brand logos, icons) are in the Media Library at their original Figma resolution.

## Rebuilding the Home page

```bash
cd tools/elementor-home
export WP_USER=admin WP_APP_PASSWORD='xxxx xxxx xxxx xxxx xxxx xxxx'
python3 build.py hero stats about brands why mission services cta insights          # dry run
python3 build.py hero stats about brands why mission services cta insights --live   # replaces page content
```

After a live build, publish the page in Elementor (or via the `elementor-publish-document` tool) and
clear Elementor's CSS cache (**Elementor → Tools → Regenerate CSS & Data**), otherwise stale
per-breakpoint CSS can be served.

## Placeholder links

The design has no inner pages yet, so buttons point at in-page/temporary targets
(`#brands`, `#services`, `#insights`, `#contact`, `#about`). "Visit site" uses
subaru.ae / yutong.ae / kinglong.ae — confirm the correct brand URLs before launch.
