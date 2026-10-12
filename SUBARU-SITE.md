# Subaru UAE (Al Khoory Automobiles) — WordPress build

Live site: https://soharon.co.uk/subaru/ (WordPress + Elementor 4.3.4 free, V4 atomic elements)

## What is on the site (built via the Elementor MCP)
- Design system on the Elementor kit: 15 global variables (`sub-*` colours, `font-display` Michroma, `font-body` Inter) and ~90 global classes (`sub-section`, `sub-h1`…, `sub-btn` + modifiers, cards, grids, tab/swatch/sub-nav behaviour classes, `sub-id-*` anchor classes).
- 23 published pages: Home, Our Range (+ Forester, Crosstrek, WRX, BRZ), About Us, Subaru Technology (+ BOXER, Symmetrical AWD, SI-DRIVE, Lineartronic, VDC, Suspension), Subaru Safety, Current Offers, Book a Test Drive, After-Sales Services, FAQs, Newsroom (posts page), Awards, Media Gallery, Contact Us.
- Interactive parts are native Elementor tabs/accordions: hero model carousel, model filters, colour pickers, grade selectors, FAQ accordions, gallery filters.
- Newsroom blog: 7 posts with featured images, 7 categories, tags, comments open.
- Menus: Primary, Top Bar, 4 footer columns, Legal (auto-assigned on theme activation).
- Front page = Home, posts page = Newsroom.

## Theme: `subaru-alkhoory` (child of Hello Elementor)
Source: `subaru-alkhoory-theme/subaru-alkhoory/` — installable zip: `subaru-alkhoory-theme.zip`.
Provides header/mega menu/mobile menu, footer, blog templates (newsroom, archive, single, search, 404),
the animation/behaviour engine for the `sub-*` classes, shortcodes (`[subaru_form]`, `[subaru_posts]`,
`[subaru_showrooms]`, `[subaru_anchor]`), an "Enquiries" lead inbox with CSV export, "Subaru Settings",
and 5 custom Elementor widgets (Colour Configurator with 360° drag, Hero Carousel, Spec Strip, Model Card, Feature Hotspots).

### Install
1. WP Admin → Appearance → Themes → Add New → Upload Theme → `subaru-alkhoory-theme.zip` → Activate.
2. Settings → Permalinks → Save once.
3. Subaru Settings: form recipients, showroom details, social URLs, WhatsApp number (optional).
4. Install an SMTP plugin so enquiry emails are delivered.

Until the theme is active, form/news shortcodes show as plain text and the header/footer are Hello Elementor defaults.

## Content the client must supply / review
- Awards page: placeholder award text from Figma.
- FAQ answers (written from site copy), Current Offers copy (generic, no prices).
- Contact: assistance line number, showroom addresses/phones (Subaru Settings).
- Blog posts "Crosstrek Owners Weekend" and "Summer Care Tips" — review; post "Subaru Expands Its Presence in the UAE" featured image carries a stock-photo watermark (replace with a licensed image).
- Real per-colour car photos for colour pickers (current ones are recoloured approximations) and brochure PDFs.
- Privacy Policy page (still a WordPress draft).

## v1.1 revisions
- Theme: simpler motion (no intro loader, parallax, Ken Burns, magnetic buttons, cursor glow, image fade); buttons change colour only on hover; phone fields accept any format (no UAE-only hint/validation); footer copyright + "A member of" on one line; all page sections' content is 1250px wide; mobile overflow guards; test-drive step 1 is a working model picker that fills the form and advances the step bar; links into accordion items open them.
- Site data (live): animations reduced to an H1 fade on load plus a single fade-in for card grids/galleries; Forester and Crosstrek grade sections converted into a grade accordion; inline 1240px max-widths set to 1250px; two-column layout overflow fixes on Home, Our Range and About Us.
