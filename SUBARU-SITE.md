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

## v1.3.5 — "Subaru Website Changes 26/10/2026" (Oct 2026)

Theme:
- Optional Google reCAPTCHA v2 for all forms (Subaru Settings → Spam protection); maths question stays as fallback while keys are empty.
- Model pages: grade-detail feature images share one 16:10 frame (uniform cards).
- /wp-json/subaru/v1/assets also lists form recipients and whether reCAPTCHA is on.

Live content (Elementor):
- Home hero slides → media 832 Forester, 829 BRZ, 831 WRX, 830 Crosstrek.
- About: "SUBARU History" → subaru.co.jp history page (new tab); "Safety History" → Safety page #passive-safety; building photo shown uncropped.
- BOXER page overview image → 276 (engine cutaway; old 474 was cropped at source).
- After-Sales: hero → 212, Service Maintenance → 151, Courtesy Drop-Off → 211. Test drive hero → pack Crosstrek_lifestyle_driver_interior.jpg.
- Model pages: 37 feature crops made from the image pack (media 833–870, titles "Subaru <model> …"); BRZ MT shifter → 729; BRZ Manual feature list added; Forester "Easy-to-Clean Interior" → "One-touch Folding Switches".
- Still needing client photos: Crosstrek aluminium pedals, Forester upper cargo hooks, a workshop photo for the After-Sales hero.

## v1.3.4 — Configurator images no longer zoom/overflow (Elementor img height:auto overridden); no pink Hello Elementor button states; interior photos shown whole; grade accordions scroll the opened grade into view; Media Gallery adds the model-page photos (gallery_media, Forester filter back); home range cards use matching 360 frames + smaller logos; Subaru Way logos smaller; calmer motion (no image hover zoom outside galleries, no count-up, no pulse, one short heading fade per page).

## v1.3.3 — 360 view: colour photo hidden once a frame shows (no double image), frames scaled to match the photos, soft image edges. Legacy hidden sections removed from the model and Media Gallery pages.

## v1.3.2 — 360 folders the web server cannot read are skipped (reported at /wp-json/subaru/v1/assets as "unreadable") instead of breaking the configurator; duplicate img attributes removed.

## v1.3.1 — Fix: v1.3.0 redeclared subaru_models() (fatal error); the pack data accessor is now subaru_asset_data().

## v1.3.0 — Image pack (wp-content/uploads/subaru-assets/) (Oct 2026)

The client asset pack (7 folders) lives on the server in `wp-content/uploads/subaru-assets/`, uploaded via cPanel.
It is not in the Media Library, the theme or this repo.

**Theme files**
- `inc/subaru-assets.php`: `subaru_asset_url()`, `subaru_asset_img()` (width/height, alt, lazy/eager, srcset), 360 frame discovery (`subaru_spin_index()`), Elementor output filter (adds width/height/loading/decoding/srcset to pack images; `.sub-lcp` = hero, eager + fetchpriority), admin health check `GET /wp-json/subaru/v1/assets` (`?flush=1` clears the 360 cache).
- `inc/subaru-models.php`: single data source (colours + swatch hex, logos, grades, interiors, galleries, hero/home images).
- `inc/subaru-asset-dims.php`: generated pixel sizes of the pack files.
- `inc/subaru-components.php`: `[subaru_configurator model="…"]`, `[subaru_gallery model="…" layout="feature|masonry" filter="yes"]`.
- `template-parts/configurator.php`, `template-parts/gallery.php`, `assets/js/models.js`, `assets/css/models.css` (loaded only where used).
- Resized copies are cached on demand in `wp-content/uploads/subaru-assets-cache/{640|1024|1600}/…` (≈2.5 s budget per request). Disable with `add_filter( 'subaru_assets_resize', '__return_false' )`.

**Switch-over:** component wrappers carry the global class `.sub-await-theme`, which Elementor hides until 1.3.0 is active. The sections they replace carry `.sub-legacy`, which 1.3.0 hides. Once 1.3.0 is live, the legacy sections can be deleted in Elementor.

**Nearest-match mappings (confirm with the client):**
- Crosstrek 2.0i-L uses the 2.5i-L EyeSight cut-out and interior.
- WRX HIGH / HIGH EyeSight interior uses the pack's GT ultrasuede photo.

**Placeholders (Elementor navigator title "TODO: client image"):**
- After-Sales: hero, Service & Maintenance, Courtesy Drop-Off.
- Forester: hero and gallery photos (none in the pack, so the current images are kept).

## v1.2.2 — Footer line + Home tech icons (Oct 2026)

- Removed the divider line above the Al Khoory Group logo in the footer.
- Live data: Home "Subaru Way" card icons swapped to redrawn transparent line icons (media 809 AWD, 810 BOXER, 811 EyeSight); the old dark-filled 315/320 are untouched elsewhere.

## v1.2.1 — Al Khoory Automobiles logo (Oct 2026)

- Client's AKA logo bundled as `assets/img/al-khoory-automobiles.png` (original colours) and `al-khoory-automobiles-white.png` (white text, emblem colours kept).
- Wide desktop (>1140px): logo sits at the right end of the top bar (white version). The main nav row has no free width for a 10:1 wordmark.
- Tablet (768–1140px, burger layout): logo moves into the main bar after the CTA, swapping white/original with the transparent/solid header state.
- Hidden on mobile. Customizer › "Header right logo" still overrides the original-colour version.

## v1.2.0 — "Subaru UAE Website Feedback" (Oct 2026)
Reverted: Our Range hero back to the original image; showroom edit redone hiding only Elite Cars + URBAN (About Us); range image moved to the Book a Test Drive hero.

Theme (needs upload): top-bar text "Official Subaru Distributor in Dubai & the Northern Emirates"; 50 Years badge moved left of the Subaru logo; Al Khoory Automobiles logo slot on the right (Customizer → Header right logo, or bundle assets/img/al-khoory-automobiles.png); "A member of Subaru" removed; Al Khoory Group logo centred below the footer; all forms email info@subaru-uae.com by default; built-in maths CAPTCHA on contact/offer/service/test-drive; all fields mandatory except comments/message; enquiry types reordered + Title Case (On-road and Hybrid removed); service models + Outback / WRX STI / Legacy, evening 4–6pm; test drive morning 9:30am–12pm, location Dubai showroom only, grade hidden for Forester/Crosstrek and WRX/BRZ Automatic/Manual only; nav "Courtesy Drop-Off Service".

Live page data: Home (Crosstrek/WRX carousel images, "Go Further. Explore More.", Subaru Way Forester image + 3 new points, range card outdoor images + specs 13.2 km/L, 13 km/L, 275 PS, 237 PS, WRX Symmetrical AWD, engineering image, Newsroom hidden); About (hero, intro text); Technology (hero, BOXER fit, card 2/4 images, Hybrid hidden); BOXER + AWD image fixes; Safety image fixes; Offers hero; After-Sales Courtesy Drop-Off section/card/anchor, warranty image, button removed; Contact (hero, Tel/Email/Get Directions per block, Parts Centre – Dubai, bold second lines, sales execs); Test Drive (PNG cards with shadows, What to expect); model pages (real per-colour images for Forester/Crosstrek, 360° wording removed, grade PNGs).
Tools used for image prep: client-assets/tools/.
