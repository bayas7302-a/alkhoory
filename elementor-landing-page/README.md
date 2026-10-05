# Hee&Lee landing page → Elementor (atomic / V4)

Built from Figma `YRL5n3TdN3Vy26zjBZMBSH` (frame 263:2) on soharon.com, draft page 24951, Elementor Canvas template.

- `sections/*.json` — one `elementor-build-composition` payload per Figma section (xml_structure, element_config, style), in page order:
  nav, hero, packages, ecommerce, stats, trusted, **work**, about, services, process, faq, contact, footer.
- **work** (portfolio carousel) is a classic container with a `[portfolio_carousel]` shortcode widget followed by an HTML widget containing
  `../portfolio-carousel/elementor-portfolio-widget.html`, which rebuilds the Website Portfolio posts in the Figma design.
- **contact** form: `[contact-form-7 id="6134e6d" title="Website Landing Page New"]` injected into the `contact-form-slot` div, styled by `sections/contact-form.css`.
- `page-fixes.css` — atomic elements also carry the legacy `.e-con` class (width:100%); this restores natural sizing. Lives in the anchor HTML widget at the top of the nav.
- Fonts come from the Elementor global font variables `--font-display` (Anton) and `--font-body` (Hanken Grotesk).
