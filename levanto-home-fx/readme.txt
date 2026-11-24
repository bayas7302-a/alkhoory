=== Levanto Home FX ===
Version: 1.2.3

Motion layer for the Levanto Elementor home page (Elementor 4 / V4 atomic widgets).

The page itself is built natively in Elementor and stays fully editable. Elementor V4
drops CSS animations, transitions and repeating patterns from widget styles, so this
plugin adds them, keyed off the lv-* global classes in the Elementor kit:

* Animated background patterns: rippling rings, drifting dot grids, floating glows,
  flowing lines with travelling sparks (Industries), drifting arc grid (Why Levanto)
* Infinite category marquee (pauses on hover)
* Product accordion: hover/click/keyboard to expand a category
* Services list: active row follows the pointer
* Hover transitions on buttons (sheen + arrow turn), industry cards (3D tilt + zoom),
  brand/client logos (greyscale to colour), footer links
* Count-up numbers, Ken Burns hero, sticky nav pill, scroll progress bar,
  current-section underline, one-page anchors, mobile burger menu
* Respects prefers-reduced-motion

Install: WP Admin > Plugins > Add New > Upload Plugin > levanto-home-fx.zip > Activate.
No settings. Deactivating it leaves the page static but intact.

== Changelog ==
1.2.3 - Mobile header: no quote button, neutral burger button, solid menu panel.
1.2.2 - Footer wordmark: clean SVG-filter outline (no inner glyph lines) with a travelling burnt-orange sheen.
1.2.1 - Softer burnt-orange runners; Why Levanto pattern no longer drifts.
1.2.0 - Why Levanto and Industries patterns redrawn as live vectors with orange
        runners travelling random routes; fixed the inner Industries line joint.
1.1.0 - Patterns now animate the exact Figma artwork (set on the page) instead of
        CSS approximations; arrows keep the design's diagonal and hop on hover;
        every Services row gets the dark highlight + image on hover; icons centred.
1.0.0 - First release.
