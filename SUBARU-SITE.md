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

## v1.1.2 revisions (client "Updates on Website" doc)
- Contact, Book a Test Drive and After-Sales forms rebuilt to the Figma field sets and style (labels above fields, compact inputs, placeholders, Emirate/Grade selects; test-drive model comes from the step-1 cards and Grade is filtered by the chosen model).
- Newsroom/theme CTA band: grille image restored (bundled fallback) and "Find a Showroom" is an outline button again (CSS specificity fix).
- Section spacing reduced site-wide: 80px desktop / 64px tablet / 48px mobile (was up to 120px).

## v1.1.3
- Header logo replaced with the client-supplied white Subaru logo (tagline "Confidence in Motion" cropped out); matching dark version generated from the same artwork for the scrolled header.

## Client images (Oct 2026)
- Header logo: client "Logo (6)" cropped to remove "Confidence in Motion" (theme v1.1.3).
- Showroom image: non-Subaru signage (Alkah Establishment, M. Tayyeb Khoory & Sons, The Elite Cars, Urban) blanked to plain fascia panels (`client-assets/edit_showroom.py`); used on About Us, "Subaru's Trusted Partner" section (media 779).
- Subaru range image: upscaled 960px original to 1920px; used as the Our Range hero background (media 780). A higher-resolution original from the client would look sharper.
- v1.1.4: CTA band image fills the full card height (was cut short by a generic img rule).

## v1.2.0 — "Subaru UAE Website Feedback" (Oct 2026)
Reverted: Our Range hero back to the original image; showroom edit redone hiding only Elite Cars + URBAN (About Us); range image moved to the Book a Test Drive hero.

Theme (needs upload): top-bar text "Official Subaru Distributor in Dubai & the Northern Emirates"; 50 Years badge moved left of the Subaru logo; Al Khoory Automobiles logo slot on the right (Customizer → Header right logo, or bundle assets/img/al-khoory-automobiles.png); "A member of Subaru" removed; Al Khoory Group logo centred below the footer; all forms email info@subaru-uae.com by default; built-in maths CAPTCHA on contact/offer/service/test-drive; all fields mandatory except comments/message; enquiry types reordered + Title Case (On-road and Hybrid removed); service models + Outback / WRX STI / Legacy, evening 4–6pm; test drive morning 9:30am–12pm, location Dubai showroom only, grade hidden for Forester/Crosstrek and WRX/BRZ Automatic/Manual only; nav "Courtesy Drop-Off Service".

Live page data: Home (Crosstrek/WRX carousel images, "Go Further. Explore More.", Subaru Way Forester image + 3 new points, range card outdoor images + specs 13.2 km/L, 13 km/L, 275 PS, 237 PS, WRX Symmetrical AWD, engineering image, Newsroom hidden); About (hero, intro text); Technology (hero, BOXER fit, card 2/4 images, Hybrid hidden); BOXER + AWD image fixes; Safety image fixes; Offers hero; After-Sales Courtesy Drop-Off section/card/anchor, warranty image, button removed; Contact (hero, Tel/Email/Get Directions per block, Parts Centre – Dubai, bold second lines, sales execs); Test Drive (PNG cards with shadows, What to expect); model pages (real per-colour images for Forester/Crosstrek, 360° wording removed, grade PNGs).
Tools used for image prep: client-assets/tools/.
