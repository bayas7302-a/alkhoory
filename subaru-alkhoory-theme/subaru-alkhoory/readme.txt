=== Subaru Al Khoory ===
Template: hello-elementor
Requires at least: 6.3
Requires PHP: 7.4
Version: 1.0.0
License: GPLv2 or later
Text Domain: subaru-alkhoory

Child theme of Hello Elementor for the Subaru UAE / Al Khoory Automobiles website.

== Installation ==

1. Make sure the parent theme "Hello Elementor" is installed (Appearance → Themes). It does not need to be active.
2. Appearance → Themes → Add New → Upload Theme → choose subaru-alkhoory-theme.zip → Install Now.
3. Click "Activate".
   On activation the theme assigns existing menus to its locations by slug:
   primary-menu → Primary, top-bar-menu → Top bar, footer-our-range / footer-discover /
   footer-owners / footer-resources → footer columns, legal-menu → Legal.
   (If a location is still empty the theme falls back to a menu with that slug, then to built-in links.)
4. Subaru Settings (admin menu): set enquiry recipients per form, optional WhatsApp number
   (floating button is hidden while empty), social profile URLs (empty = hidden), showroom
   list (JSON), cookie bar and intro animation.
5. Appearance → Customize → "Subaru Header & Footer": optional logo / badge overrides,
   top bar text, header button.
6. Settings → Permalinks → Save (once) so the Newsroom archive URLs are refreshed.

== What the theme provides ==

* Header (top bar, sticky main navigation with "Our Range" mega menu, dropdowns, search
  overlay, full-screen mobile menu) and footer (newsletter, link columns, logos, legal links,
  back-to-top, cookie notice, optional WhatsApp button).
* Behaviours for Elementor V4 atomic layouts keyed off global class labels:
  .sub-hero-carousel (autoplay, progress tabs, swipe, keyboard, Ken Burns, text reveal),
  .sub-pill-tab / .sub-line-tab / .sub-swatch selected states and panel transitions,
  .sub-subnav (sticky + scrollspy), .sub-id-<name> → id="<name>", .sub-counter, .sub-parallax,
  .sub-zoom, .sub-gallery (lightbox), .sub-faq* (accordion styling + smooth open),
  .sub-link arrow, .sub-btn micro-interactions, image fade-in, scroll progress bar.
* Shortcodes:
  [subaru_form type="test-drive|contact|service|newsletter|brochure|offer" model=""]
  [subaru_posts count="3" category="" layout="grid|list|featured"]
  [subaru_showrooms type=""]
  [subaru_anchor id=""]
  Forms read ?model= and ?enquiry= from the URL. Submissions are stored under
  "Enquiries" (filter by type, CSV export) and emailed (wp_mail) with a customer auto-reply.
* Newsroom templates: home.php (posts page), archive.php, search.php, single.php, page.php,
  404.php, comments.php.
* Elementor widgets (category "Subaru"): Colour Configurator (with optional 360° frames),
  Hero Carousel, Spec Strip, Model Card, Feature Hotspots.
* SEO fallbacks (meta description + Open Graph when no SEO plugin is active), AutoDealer
  JSON-LD, SVG uploads for administrators (sanitised).

== Notes ==

* Emails use wp_mail(); configure an SMTP plugin for reliable delivery.
* Mega menu thumbnails use the featured image of each model page when set.
