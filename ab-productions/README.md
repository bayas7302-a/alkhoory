# AB Productions website (WordPress + Elementor)

Site: https://soharon.co.uk/ab-production/ · Design: Figma `CCKeyRvX5nJ6sH6ifEzV8u` (AB-PRODUCTIONS) · Content: AB Production company profile (PDF).

## What's here

| Path | What it is |
|---|---|
| `abproductions-child/` | Child theme of **Hello Elementor**: header, footer, Projects post type, shortcodes, fonts, hover effects, Contact Form 7 form + styles |
| `dist/abproductions-child.zip` | The same theme, ready to upload |
| `elementor-build/` | Scripts that built the five Elementor pages through the Elementor MCP (`home.py`, `pages.py`), plus a local preview harness |

## Install (one-time)

1. **Appearance → Themes → Add New → Upload Theme** → `dist/abproductions-child.zip` → **Activate**.
   Hello Elementor (the parent) is already installed.
2. On activation the theme:
   - creates the Project Categories (Events, Exhibitions, Brand Activations, Hospitality, Interiors & Fit-Outs) and 8 sample projects from the company profile,
   - creates the Contact Form 7 form **"AB Productions Enquiry"** (Contact Form 7 is already installed and active).
3. **Font**: the primary font is *Dharma Gothic E*. `https://soharon.co.uk/ab-production/Dharma%20Gothic%20E.otf` currently returns **404**.
   Either upload the file to that URL, or copy it into the theme as `assets/fonts/DharmaGothicE.otf`.
   Until then headings use the bundled Big Shoulders Display (the condensed font used in the Figma file).
   Poppins is loaded by Elementor (Google Fonts), via the `ab-font-body` global font variable.
4. Optional: **Appearance → Customize → AB Productions** for phone numbers, email, addresses, map searches and social links.
   **Appearance → Menus** can replace the default header/footer links (locations: Header menu, Footer: Services, Footer: Company).

## Pages (built with Elementor V4 atomic elements)

Home (front page), About, Services, Projects, Contact. All published, template *Elementor Full Width* (theme header + footer), page title hidden.

Shared styling lives in Elementor **global variables** (`ab-*` colours, `ab-font-display`, `ab-font-body`) and **global classes** (`abp-*`).

Dynamic blocks are Elementor Paragraphs whose whole text is a shortcode. The theme swaps the paragraph for the output on the front end, so in the editor they show as text:

| Shortcode | Used on |
|---|---|
| `[ab_projects layout="featured" id="home-projects" button="View all projects"]` + `[ab_project_filters target="home-projects" style="pills"]` | Home, "Our Projects" mosaic + filter pills |
| `[ab_projects per_page="8"]` | Projects page: category tabs, grid, Load More |
| `[ab_services_tabs]` | About + Services: tabs on desktop, accordion on mobile (content from the company profile) |
| `[ab_clients_marquee]` / `[ab_clients_grid]` | Home logo marquee / About client wall |
| `[ab_contact_card]` | "Reach us directly" card |
| `[contact-form-7 title="AB Productions Enquiry"]` | Enquiry form |
| `[ab_find_us]` | Contact: map with Dubai HQ / Sharjah toggle |
| `[ab_anchor id="industries"]` | Jump targets for `/#industries`, `/about/#clientele`, etc. |

## Projects backend

**Projects → Add New**: title, **Project image** (featured image), one or more **Categories**, and under *Project details* a subtitle, an optional home-page tag, and "Show on the home page".
Order with *Page Attributes → Order*.

- Category tabs on the Projects page are built from the categories that have projects, so a new category appears automatically.
- **Image crop**: upload a wide rectangle (about 3:1, e.g. 2700 × 1140). Cards show only the **centre third**; the left and right thirds are hidden.
- Clicking a card opens a pop-up with **only the full image** (arrows, keyboard and swipe move between visible projects; Esc closes).

## Hover effects

Card hovers are in `assets/css/main.css` under "Hover effects for the Elementor cards". They target the Elementor global classes (Elementor prints the class label on the element):
`abp-svc-card*` (service cards: lift, zoom, gradient shade, arrow rotates), `abp-ind-card*` (industry cards turn brand-gradient with an arrow, as drawn in Figma), `abp-reason*`, `abp-pillar*`, `abp-strength*`, `abp-loc-card*`, `abp-arrow-link*`.
Project cards, client logos, contact rows, buttons and footer links have their own hovers.

## Rebuilding pages from code

```bash
cd elementor-build
export ABP_MCP_AUTH=$(printf 'admin:APPLICATION PASSWORD' | base64)
python3 home.py            # all Home sections (replaces the page content)
python3 pages.py 63 64     # About, Services (65 Projects, 66 Contact)
```

After a rebuild, clear Elementor's CSS cache (Elementor → Tools → Regenerate CSS, or `DELETE /wp-json/elementor/v1/cache`).

Note: this overwrites edits made in the Elementor editor.
