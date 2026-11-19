

/**
 * Kress Kleen – site polish
 * Mobile sticky header, hover fixes, typography + spacing on every page,
 * mobile/tablet footer, Who We Are + Where to buy responsive layout,
 * catalogue filters on phones (accordion inside a slide-up drawer),
 * design v3 Phase 1 (1646px content width, Satoshi Black headings, desktop text sizes),
 * design v3 Phase 2 (header: marquee, menu icon, language switch, logo, button, inner-page header),
 * design v3 Phase 3 (footer: layout, fonts, buttons, long arrows, logo, Contact / Let's Connect links),
 * design v3 Phase 4 (home page) and Phases 5-8 (Where to Buy, Catalogue, product page, Who We Are),
 * Phase 0 text fixes, and a 301 redirect /catelog/ -> /catalogue/ (K-01).
 * Paste at the END of functions.php. To undo, delete this block.
 * Source files: kress-kleen-polish/kk-polish.css + kk-polish.js
 */
/* K-01: once the Catalogue page slug is "catalogue", the old /catelog/ address redirects to it (301).
   Only runs on a 404, so it does nothing while the old slug is still in use. */
add_action( 'template_redirect', 'kk_polish_catalogue_redirect' );
function kk_polish_catalogue_redirect() {
    if ( ! is_404() ) {
        return;
    }
    $path = trim( (string) wp_parse_url( $_SERVER['REQUEST_URI'], PHP_URL_PATH ), '/' );
    if ( ! preg_match( '#(^|/)catelog$#', $path ) ) {
        return;
    }
    $page = get_page_by_path( 'catalogue' );
    if ( $page ) {
        wp_safe_redirect( get_permalink( $page ), 301 );
        exit;
    }
}

add_action( 'wp_head', 'kk_polish_css', 999 );
function kk_polish_css() {
    if ( is_admin() ) {
        return;
    }
    ?>
<style id="kk-polish-css">
/* =====================================================================
   Kress Kleen – site polish (responsive, typography, spacing, header,
   footer, hovers, Who We Are, catalogue filters)
   Breakpoints follow Elementor: tablet <= 1024px, mobile <= 767px.
   ===================================================================== */

:root {
  --kp-green: #00784c;
  --kp-green-dark: #00613d;
  --kp-orange: #f05a38;
  --kp-gold: #f0b232;
  --kp-ink: #111111;
  --kp-mint: #e4f3ec;
  --kp-ease: cubic-bezier(0.16, 1, 0.3, 1);
  --kp-gutter: 20px;            /* side padding on mobile */
  --kp-sec: 64px;               /* vertical section padding on mobile */
}

/* ---------- 0. no sideways scrolling anywhere ---------- */
body { overflow-x: clip; }
body .elementor-widget-image { overflow: hidden; }
body .elementor-widget-image img { max-width: 100% !important; }
body .wpcf7 form.wpcf7-form { position: relative; }
body .wpcf7-spinner { position: absolute !important; right: 14px; bottom: 14px; left: auto; margin: 0 !important; }
body .elementor-heading-title { overflow-wrap: break-word; }

/* =====================================================================
   1. HOME HERO (scroll video) – smaller banner text
   ===================================================================== */
body .kk-sh .kk-sh__card {
  width: min(540px, 100%);
  padding: 28px 30px 30px;
}
body .kk-sh .kk-sh__card--center { margin-left: calc(min(540px, 100%) / -2); }
body .kk-sh .kk-sh__eyebrow { font-size: 16px; letter-spacing: 0.08em; }
body .kk-sh h2.kk-sh__title { margin: 6px 0 10px; font-size: clamp(24px, 2.1vw, 32px); line-height: 1.1; }
body .kk-sh .kk-sh__text { margin-bottom: 20px; font-size: 16px; }
body .kk-sh a.kk-sh__btn { padding: 11px 24px; font-size: 13px; letter-spacing: 0.06em; }

@media (max-width: 767px) {
  body .kk-sh .kk-sh__card { padding: 18px 18px 20px; border-radius: 14px; bottom: 4vh !important; }
  body .kk-sh .kk-sh__eyebrow { font-size: 12px; }
  body .kk-sh h2.kk-sh__title { margin: 4px 0 6px; font-size: 19px; }
  body .kk-sh .kk-sh__text { margin-bottom: 14px; font-size: 14px; }
  body .kk-sh a.kk-sh__btn { padding: 9px 18px; font-size: 12px; }
}

/* =====================================================================
   2. PAGE BANNERS (Who We Are, Where to buy, Catalogue) – smaller titles
   ===================================================================== */
body .elementor-153 .elementor-element.elementor-element-901a2db .elementor-heading-title,
body .elementor-132 .elementor-element.elementor-element-4cd68f2 .elementor-heading-title,
body .kk-hero .kk-hero__title {
  font-size: clamp(40px, 4.2vw, 60px) !important;
  line-height: 1.05;
}
@media (max-width: 1024px) {
  body .elementor-153 .elementor-element.elementor-element-901a2db .elementor-heading-title,
  body .elementor-132 .elementor-element.elementor-element-4cd68f2 .elementor-heading-title,
body .elementor-132 .elementor-element.elementor-element-4cd68f2 .elementor-heading-title,
  body .kk-hero .kk-hero__title { font-size: 40px !important; }
}
@media (max-width: 767px) {
  body .elementor-153 .elementor-element.elementor-element-901a2db .elementor-heading-title,
  body .elementor-132 .elementor-element.elementor-element-4cd68f2 .elementor-heading-title,
body .elementor-132 .elementor-element.elementor-element-4cd68f2 .elementor-heading-title,
  body .kk-hero .kk-hero__title { font-size: 32px !important; }
  body .elementor-153 .elementor-element.elementor-element-cc1a00a { --min-height: 0px; min-height: 0; --padding-top: 72px; padding-top: 72px !important; --padding-bottom: 40px; padding-bottom: 40px !important; --padding-left: var(--kp-gutter); padding-left: var(--kp-gutter) !important; --padding-right: var(--kp-gutter); padding-right: var(--kp-gutter) !important; }
  body .elementor-153 .elementor-element.elementor-element-cc1a00a > .e-con-inner { padding: 0 !important; }
  body .elementor-153 .elementor-element.elementor-element-90a5b6c { margin-bottom: 8px; }
  body :is(.elementor-153 .elementor-element-90a5b6c, .elementor-132 .elementor-element-b0e94d1) :is(ul, ol, .hfe-breadcrumbs) { padding-left: 0 !important; margin-left: 0 !important; }
}

/* =====================================================================
   3. MOBILE HEADER – one clean row, sticky on phones only
   ===================================================================== */
@media (max-width: 767px) {
  body #masthead,
  body .ehf-header #masthead {
    position: -webkit-sticky !important;
    position: sticky !important;
    top: -28px;                    /* the "Kleening since 2003" bar scrolls away, the main bar stays */
    z-index: 1000 !important;
    transition: background-color 0.3s ease, box-shadow 0.3s ease;
  }
  /* slim marquee */
  body #masthead .elementor-element.elementor-element-56435bc { --min-height: 28px; min-height: 28px; --padding-top: 6px; padding-top: 6px !important; --padding-bottom: 6px; padding-bottom: 6px !important; }
  body #masthead .elementor-element.elementor-element-8750345 { font-size: 10px; }

  /* main bar: menu | logo | where to buy */
  body #masthead .elementor-element.elementor-element-4a693fd {
    --min-height: 64px; min-height: 64px;
    --padding-top: 8px; padding-top: 8px !important; --padding-bottom: 8px; padding-bottom: 8px !important;
    --padding-left: 16px; padding-left: 16px !important; --padding-right: 16px; padding-right: 16px !important;
    --flex-wrap: nowrap; flex-wrap: nowrap;
    background-color: transparent;
  }
  body #masthead .elementor-element.elementor-element-4a693fd > .e-con-inner {
    display: grid !important;
    grid-template-columns: 1fr auto 1fr;
    align-items: center;
    gap: 8px;
    padding: 0 !important;
  }
  /* menu + language (left) */
  body #masthead .elementor-element.elementor-element-f760a15 {
    grid-column: 1; grid-row: 1;
    --width: auto; width: auto !important; max-width: none;
    --flex-wrap: nowrap; flex-wrap: nowrap;
    --gap: 10px; gap: 10px;
    --justify-content: flex-start; justify-content: flex-start;
    padding: 0 !important; margin: 0 !important;
    order: 0 !important;
  }
  body #masthead .elementor-element.elementor-element-98f606d .elementor-icon { font-size: 24px; }
  body #masthead .elementor-element.elementor-element-98f606d .elementor-icon svg { height: 22px; width: auto; }
  body #masthead .elementor-element.elementor-element-98f606d .elementor-icon-wrapper { line-height: 0; }
  body #masthead .elementor-element.elementor-element-e8b8918 { line-height: 1; }
  body #masthead .elementor-element.elementor-element-e8b8918 .gtranslate_wrapper { gap: 10px !important; }
  body #masthead .elementor-element.elementor-element-e8b8918 a.glink::after,
  body #masthead .elementor-element.elementor-element-e8b8918 a[data-gt-lang]::after { font-size: 12px !important; }
  /* logo (centre) */
  body #masthead .elementor-element.elementor-element-ae1ae2e {
    grid-column: 2; grid-row: 1;
    width: auto !important; max-width: none; --container-widget-width: auto;
    margin: 0 !important;
  }
  body #masthead .elementor-element.elementor-element-ae1ae2e img { width: auto !important; height: 46px; max-width: 120px; object-fit: contain; }
  /* where to buy (right) */
  body #masthead .elementor-element.elementor-element-0a2ca92 {
    grid-column: 3; grid-row: 1;
    justify-self: end;
    width: auto !important; max-width: none; --container-widget-width: auto;
    margin: 0 !important;
  }
  body #masthead .elementor-element.elementor-element-0a2ca92 .elementor-button {
    padding: 9px 12px;
    font-size: 11px;
    line-height: 1;
    letter-spacing: 0.04em;
    border-radius: 999px;
    white-space: nowrap;
  }

  /* stuck state: solid brand bar with a soft shadow */
  body #masthead.kp-stuck { background-color: rgba(0, 97, 61, 0.96); box-shadow: 0 6px 20px rgba(0, 0, 0, 0.18); }
  @supports (backdrop-filter: blur(10px)) {
    body #masthead.kp-stuck { background-color: rgba(0, 97, 61, 0.88); -webkit-backdrop-filter: blur(12px) saturate(140%); backdrop-filter: blur(12px) saturate(140%); }
  }

  /* green strips that sit behind the (shorter) header */
  :root { --kk-hdr-h: 92px; }

  /* the side menu must open above the sticky header */
  body .kk-side-overlay { z-index: 1090; }
  body .kk-side { z-index: 1100; }
}

/* =====================================================================
   4. HOME – "Let's find your cleaning solution" cards
   ===================================================================== */
/* EXPLORE is a text link: no shine box, no lift, no shadow – arrow slides and underline draws */
body .elementor-43 .kkc-card .elementor-widget-button .elementor-button {
  overflow: visible;
  box-shadow: none !important;
  transform: none !important;
  background: transparent !important;
}
body .elementor-43 .kkc-card .elementor-widget-button .elementor-button::after {
  content: "";
  left: 0 !important;
  top: auto;
  bottom: -4px;
  width: 100%;
  height: 1.5px;
  background: currentColor;
  transform: scaleX(0);
  transform-origin: left;
  transition: transform 0.45s var(--kp-ease) !important;
}
body .elementor-43 .kkc-card { transition: box-shadow 0.4s ease, transform 0.4s var(--kp-ease), border-color 0.3s ease; }
body .elementor-43 .kkc-card .elementor-widget-image img { transition: transform 0.9s var(--kp-ease); }
@media (hover: hover) {
  body .elementor-43 .kkc-card:hover { box-shadow: 0 18px 40px rgba(0, 0, 0, 0.10); }
  body .elementor-43 .kkc-card:hover .elementor-widget-image img { transform: scale(1.05); }
  body .elementor-43 .kkc-card .elementor-widget-button .elementor-button:hover::after,
  body .elementor-43 .kkc-card:hover .elementor-widget-button .elementor-button::after { transform: scaleX(1); }
  body .elementor-43 .kkc-card:hover .elementor-widget-button .elementor-button-icon { transform: translateX(5px); }
}

/* =====================================================================
   5. HOME – "One home. Many cleaning challenges" area buttons
   ===================================================================== */
body .elementor-43 :is(.elementor-element.elementor-element-a682427, .elementor-element.elementor-element-3a87742, .elementor-element.elementor-element-e648c86, .elementor-element.elementor-element-0f74986) {
  border: 0 !important;
  border-radius: 999px !important;
  width: 100% !important;
  max-width: 300px !important;
  margin: 0 !important;
}
body .elementor-43 :is(.elementor-element.elementor-element-a682427, .elementor-element.elementor-element-3a87742, .elementor-element.elementor-element-e648c86, .elementor-element.elementor-element-0f74986) .elementor-button {
  display: flex;
  align-items: center;
  justify-content: space-between;
  width: 100%;
  min-height: 48px;
  padding: 0 20px 0 24px;
  border: 1px solid #1a1a1a !important;
  border-radius: 999px !important;
  background: transparent !important;
  color: var(--kp-orange) !important;
  font-size: 15px !important;
  line-height: 1;
  letter-spacing: 0.06em;
  box-shadow: none !important;
  transform: none !important;
  overflow: hidden;
  transition: background-color 0.35s ease, color 0.35s ease, border-color 0.35s ease, padding 0.4s var(--kp-ease) !important;
}
body .elementor-43 :is(.elementor-element.elementor-element-a682427, .elementor-element.elementor-element-3a87742, .elementor-element.elementor-element-e648c86, .elementor-element.elementor-element-0f74986) .elementor-button::after {
  /* small arrow on the right, replaces the shine */
  content: "\2192";
  position: static;
  width: auto; height: auto;
  background: none;
  transform: translateX(-6px);
  opacity: 0;
  font-size: 16px;
  transition: transform 0.4s var(--kp-ease), opacity 0.3s ease !important;
}
body .elementor-43 :is(.elementor-element.elementor-element-a682427, .elementor-element.elementor-element-3a87742, .elementor-element.elementor-element-e648c86, .elementor-element.elementor-element-0f74986) .elementor-button-content-wrapper { line-height: 1; }
@media (hover: hover) {
  body .elementor-43 :is(.elementor-element.elementor-element-a682427, .elementor-element.elementor-element-3a87742, .elementor-element.elementor-element-e648c86, .elementor-element.elementor-element-0f74986) .elementor-button:hover {
    background: var(--kp-orange) !important;
    border-color: var(--kp-orange) !important;
    color: #ffffff !important;
  }
  body .elementor-43 :is(.elementor-element.elementor-element-a682427, .elementor-element.elementor-element-3a87742, .elementor-element.elementor-element-e648c86, .elementor-element.elementor-element-0f74986) .elementor-button:hover::after { transform: none; opacity: 1; }
}
body .elementor-43 :is(.elementor-element.elementor-element-a682427, .elementor-element.elementor-element-3a87742, .elementor-element.elementor-element-e648c86, .elementor-element.elementor-element-0f74986) .elementor-button:focus-visible {
  outline: 3px solid var(--kp-gold);
  outline-offset: 3px;
}
body .elementor-43 .elementor-element.elementor-element-743fae7 { --gap: 12px; gap: 12px; }
body .elementor-43 .elementor-element.elementor-element-08f48e7 { margin-bottom: 14px !important; }

/* =====================================================================
   6. HOME – typography + spacing on tablet / mobile
   ===================================================================== */
@media (max-width: 1024px) {
  body .elementor-43 .kkc-card .elementor-widget-heading:first-of-type .elementor-heading-title { font-size: 24px; }
}
@media (max-width: 767px) {
  /* section rhythm */
  body .elementor-43 :is(.elementor-element.elementor-element-b12f160, .elementor-element.elementor-element-975d254, .elementor-element.elementor-element-7149c5d, .elementor-element.elementor-element-1c24237) {
    --padding-top: var(--kp-sec); padding-top: var(--kp-sec) !important; --padding-bottom: var(--kp-sec); padding-bottom: var(--kp-sec) !important;
    --padding-left: var(--kp-gutter); padding-left: var(--kp-gutter) !important; --padding-right: var(--kp-gutter); padding-right: var(--kp-gutter) !important;
  }
  body .elementor-43 :is(.elementor-element.elementor-element-ad39a46, .elementor-element.elementor-element-755069b, .elementor-element.elementor-element-743fae7, .elementor-element.elementor-element-5448b30) {
    --padding-left: 0px; padding-left: 0px !important; --padding-right: 0px; padding-right: 0px !important;
  }

  /* section headings */
  body .elementor-43 :is(.elementor-element.elementor-element-9a43ca3, .elementor-element.elementor-element-cb4ed5d, .elementor-element.elementor-element-884f790, .elementor-element.elementor-element-2799513) .elementor-heading-title {
    font-size: 24px !important;
    line-height: 1.2 !important;
  }
  body .elementor-43 :is(.elementor-element.elementor-element-1bde407, .elementor-element.elementor-element-08f48e7, .elementor-element.elementor-element-9d92933) .elementor-heading-title {
    font-size: 15px !important;
    line-height: 1.55 !important;
  }
  body .elementor-43 .elementor-element.elementor-element-cb4ed5d { margin-bottom: 8px; }

  /* SMART / FUNCTIONAL / RELIABLE */
  body .elementor-43 :is(.elementor-element.elementor-element-85d3640, .elementor-element.elementor-element-63b71db, .elementor-element.elementor-element-704d045) .elementor-icon-box-title { font-size: 20px !important; margin-bottom: 6px; }
  body .elementor-43 :is(.elementor-element.elementor-element-85d3640, .elementor-element.elementor-element-63b71db, .elementor-element.elementor-element-704d045) .elementor-icon-box-description { font-size: 15px !important; }
  body .elementor-43 :is(.elementor-element.elementor-element-85d3640, .elementor-element.elementor-element-63b71db, .elementor-element.elementor-element-704d045) .elementor-icon { font-size: 40px; }
  body .elementor-43 .elementor-element.elementor-element-755069b { --gap: 28px; gap: 28px; margin-top: 12px; }

  /* solution cards: 2 per row */
  body .elementor-43 .elementor-element.elementor-element-79b14eb {
    --flex-direction: row; flex-direction: row;
    --flex-wrap: wrap; flex-wrap: wrap;
    --gap: 12px; gap: 12px;
    --padding-left: 0px; padding-left: 0px !important; --padding-right: 0px; padding-right: 0px !important;
  }
  body .elementor-43 .elementor-element.elementor-element-79b14eb > .kkc-card {
    --width: calc(50% - 6px); width: calc(50% - 6px) !important;
    --padding-bottom: 16px; padding-bottom: 16px !important;
    border-radius: 10px; overflow: hidden;
  }
  body .elementor-43 .elementor-element.elementor-element-79b14eb > .kkc-card:last-child:nth-child(odd) { --width: 100%; width: 100% !important; }
  body .elementor-43 .kkc-card .elementor-widget-image img { height: 130px !important; width: 100% !important; object-fit: cover; }
  body .elementor-43 .elementor-element.elementor-element-79b14eb > .kkc-card:last-child:nth-child(odd) .elementor-widget-image img { height: 170px !important; }
  body .elementor-43 .kkc-card .elementor-widget-icon { margin: -22px 0 0 14px !important; }
  body .elementor-43 .kkc-card .elementor-widget-icon .elementor-icon { font-size: 34px; }
  body .elementor-43 .kkc-card .elementor-widget-icon svg { width: 34px; height: 34px; }
  body .elementor-43 .kkc-card .elementor-widget-heading { margin: 0 14px !important; }
  body .elementor-43 .kkc-card .elementor-widget-heading:nth-of-type(1) { margin-top: 10px !important; }
  body .elementor-43 .kkc-card :is(.elementor-element.elementor-element-b21629b, .elementor-element.elementor-element-665f377, .elementor-element.elementor-element-252f948, .elementor-element.elementor-element-f8975af, .elementor-element.elementor-element-bcefc1b) .elementor-heading-title { font-size: 20px !important; line-height: 1.1; }
  body .elementor-43 .kkc-card :is(.elementor-element.elementor-element-ed3bfa7, .elementor-element.elementor-element-21ba642, .elementor-element.elementor-element-07011f8, .elementor-element.elementor-element-ccba669, .elementor-element.elementor-element-ce02014) { margin-top: 6px !important; }
  body .elementor-43 .kkc-card :is(.elementor-element.elementor-element-ed3bfa7, .elementor-element.elementor-element-21ba642, .elementor-element.elementor-element-07011f8, .elementor-element.elementor-element-ccba669, .elementor-element.elementor-element-ce02014) .elementor-heading-title { font-size: 13px !important; line-height: 1.45; }
  body .elementor-43 .kkc-card .elementor-widget-button { padding: 0 14px !important; margin-top: 10px !important; }
  body .elementor-43 .kkc-card .elementor-widget-button .elementor-button { font-size: 12px !important; }

  /* one home */
  body .elementor-43 :is(.elementor-element.elementor-element-a682427, .elementor-element.elementor-element-3a87742, .elementor-element.elementor-element-e648c86, .elementor-element.elementor-element-0f74986) { max-width: 100% !important; }
  body .elementor-43 :is(.elementor-element.elementor-element-a682427, .elementor-element.elementor-element-3a87742, .elementor-element.elementor-element-e648c86, .elementor-element.elementor-element-0f74986) .elementor-button { min-height: 46px; font-size: 14px !important; }
  body .elementor-43 .elementor-element.elementor-element-38068a6 { margin-top: 24px; }

  /* featured product */
  body .elementor-43 .elementor-element.elementor-element-91b1f1f { --padding-top: 40px; padding-top: 40px !important; --padding-bottom: 32px; padding-bottom: 32px !important; --padding-left: var(--kp-gutter); padding-left: var(--kp-gutter) !important; --padding-right: var(--kp-gutter); padding-right: var(--kp-gutter) !important; }
  body .elementor-43 .elementor-element.elementor-element-36490c5 .elementor-heading-title { font-size: 24px !important; line-height: 1.15 !important; }
  body .elementor-43 .elementor-element.elementor-element-0818b88 .elementor-heading-title { font-size: 16px !important; }
  body .elementor-43 .elementor-element.elementor-element-51cf488 .elementor-icon-list-text { font-size: 14px !important; }
  body .elementor-43 .elementor-element.elementor-element-d688fd8 .elementor-heading-title { font-size: 12px !important; letter-spacing: 0.1em; }
  body .elementor-43 .elementor-element.elementor-element-d7da6a9 img { height: 260px !important; object-fit: cover; }
  body .elementor-43 .elementor-element.elementor-element-9b21423 img { max-height: 340px; width: auto !important; margin: 0 auto; display: block; }

  /* buttons in content */
  body .elementor-43 :is(.elementor-element.elementor-element-c03da2d, .elementor-element.elementor-element-041e5ca) .elementor-button { font-size: 13px !important; padding: 12px 22px !important; }
  body .elementor-43 .elementor-element.elementor-element-7fb41c0 img { max-height: 320px; object-fit: contain; }
}

/* ---------- HOME – tablet (768–1024px) ---------- */
@media (min-width: 768px) and (max-width: 1024px) {
  body .elementor-43 :is(.elementor-element.elementor-element-b12f160, .elementor-element.elementor-element-975d254, .elementor-element.elementor-element-7149c5d, .elementor-element.elementor-element-1c24237) {
    --padding-top: 64px; padding-top: 64px !important; --padding-bottom: 64px; padding-bottom: 64px !important; --padding-left: 32px; padding-left: 32px !important; --padding-right: 32px; padding-right: 32px !important;
  }
  /* designing a better way: text on top, the three values in one row below */
  body .elementor-43 .elementor-element.elementor-element-b12f160 { --flex-direction: column; flex-direction: column !important; }
  body .elementor-43 :is(.elementor-element.elementor-element-ad39a46, .elementor-element.elementor-element-755069b) { --width: 100%; width: 100% !important; --padding-left: 0px; padding-left: 0px !important; --padding-right: 0px; padding-right: 0px !important; }
  body .elementor-43 .elementor-element.elementor-element-755069b { --flex-direction: row; flex-direction: row !important; --flex-wrap: nowrap; --gap: 20px; margin-top: 24px; }
  body .elementor-43 .elementor-element.elementor-element-755069b > .elementor-widget { flex: 1 1 0; width: auto !important; max-width: none !important; }
  body .elementor-43 :is(.elementor-element.elementor-element-85d3640, .elementor-element.elementor-element-63b71db, .elementor-element.elementor-element-704d045) .elementor-icon-box-title { font-size: 20px !important; }
  body .elementor-43 :is(.elementor-element.elementor-element-85d3640, .elementor-element.elementor-element-63b71db, .elementor-element.elementor-element-704d045) .elementor-icon-box-description { font-size: 15px !important; }
  body .elementor-43 .elementor-element.elementor-element-9a43ca3 .elementor-heading-title,
  body .elementor-43 .elementor-element.elementor-element-cb4ed5d .elementor-heading-title,
  body .elementor-43 .elementor-element.elementor-element-884f790 .elementor-heading-title,
  body .elementor-43 .elementor-element.elementor-element-2799513 .elementor-heading-title { font-size: 28px !important; line-height: 1.2 !important; }

  /* solution cards: 3 + 2 */
  body .elementor-43 .elementor-element.elementor-element-79b14eb { --flex-wrap: wrap; flex-wrap: wrap !important; --justify-content: center; justify-content: center !important; --gap: 14px; gap: 14px !important; --padding-left: 0px; padding-left: 0px !important; --padding-right: 0px; padding-right: 0px !important; }
  body .elementor-43 .elementor-element.elementor-element-79b14eb > .kkc-card { --width: calc(33.333% - 10px); width: calc(33.333% - 10px) !important; }
  body .elementor-43 .kkc-card .elementor-widget-image img { height: 170px !important; width: 100% !important; object-fit: cover; }

  /* one home: text on top, the map full width */
  body .elementor-43 .elementor-element.elementor-element-7149c5d { --flex-direction: column; flex-direction: column !important; }
  body .elementor-43 .elementor-element.elementor-element-743fae7 { --width: 100%; width: 100% !important; --padding-left: 0px; padding-left: 0px !important; --padding-right: 0px; padding-right: 0px !important; }
  body .elementor-43 .elementor-element.elementor-element-38068a6 { width: 100% !important; max-width: 100% !important; margin-top: 24px; }

  /* featured product: photo on top */
  body .elementor-43 .elementor-element.elementor-element-8e91e85 { --flex-direction: column; flex-direction: column !important; }
  body .elementor-43 .elementor-element.elementor-element-d7da6a9 { width: 100% !important; max-width: 100% !important; }
  body .elementor-43 .elementor-element.elementor-element-d7da6a9 img { width: 100% !important; height: 340px !important; object-fit: cover; }
  body .elementor-43 .elementor-element.elementor-element-1676003 { --width: 100%; width: 100% !important; }
  body .elementor-43 .elementor-element.elementor-element-36490c5 .elementor-heading-title { font-size: 30px !important; }
}

/* =====================================================================
   7. FOOTER – tidy on tablet / mobile
   ===================================================================== */
@media (max-width: 1024px) {
  body .elementor-30 .elementor-element.elementor-element-ab6f316 {
    --padding-top: 48px; padding-top: 48px !important; --padding-bottom: 28px; padding-bottom: 28px !important;
    --padding-left: var(--kp-gutter); padding-left: var(--kp-gutter) !important; --padding-right: var(--kp-gutter); padding-right: var(--kp-gutter) !important;
  }
  body .elementor-30 .elementor-element.elementor-element-ab6f316,
  body .elementor-30 .elementor-element.elementor-element-ab6f316 > .e-con-inner {
    --flex-direction: column; flex-direction: column !important;
    --align-items: center; align-items: center !important;
    --gap: 26px; gap: 26px !important;
    text-align: center;
  }
  body .elementor-30 .elementor-element.elementor-element-9f80d62 { width: auto !important; max-width: none !important; margin: 0 !important; }
  body .elementor-30 .elementor-element.elementor-element-9f80d62 img { width: 150px !important; height: auto; }
  body .elementor-30 .elementor-element.elementor-element-ce94520 {
    --width: 100%; width: 100% !important;
    --align-items: center; align-items: center !important;
    --gap: 12px; gap: 12px !important;
    padding: 0 !important;
  }
  body .elementor-30 .elementor-element.elementor-element-0302daf .elementor-heading-title { font-size: 12px !important; letter-spacing: 0.14em; }
  body .elementor-30 .elementor-element.elementor-element-501b3e5 .elementor-icon-list-items { justify-content: center !important; row-gap: 8px; }
  body .elementor-30 .elementor-element.elementor-element-501b3e5 .elementor-icon-list-text { font-size: 15px !important; }
  body .elementor-30 .elementor-element.elementor-element-6e495df {
    --width: 100%; width: 100% !important; max-width: 420px;
    display: grid !important;
    grid-template-columns: 1fr;
    gap: 10px !important;
    padding: 0 !important;
  }
  body .elementor-30 .elementor-element.elementor-element-6e495df > .elementor-widget { width: 100% !important; max-width: none !important; margin: 0 !important; }
  body .elementor-30 .elementor-element.elementor-element-6e495df .elementor-button {
    display: flex; width: 100%;
    justify-content: center;
    min-height: 46px;
    padding: 0 14px !important;
    font-size: 14px !important;
    border-radius: 10px !important;
    align-items: center;
  }
  body .elementor-30 .elementor-element.elementor-element-4ba5bf5 {
    grid-column: 1 / -1;
    margin-top: 14px !important;
    padding-top: 18px;
    border-top: 1px solid rgba(255, 255, 255, 0.22);
  }
  body .elementor-30 .elementor-element.elementor-element-4ba5bf5 .elementor-heading-title { font-size: 12px !important; line-height: 1.6; opacity: 0.9; text-align: center; }
}

/* =====================================================================
   8. WHO WE ARE (page 153) – desktop scale + full mobile layout
   ===================================================================== */
body .elementor-153 .elementor-element.elementor-element-fb87731 .elementor-heading-title { font-size: clamp(56px, 6.4vw, 92px) !important; line-height: 1.05; }
body .elementor-153 .elementor-element.elementor-element-092cb7a .elementor-heading-title { font-size: clamp(72px, 9vw, 140px) !important; line-height: 1.1; }

@media (max-width: 1024px) {
  body .elementor-153 :is(.elementor-element.elementor-element-5d0b5e3, .elementor-element.elementor-element-b86e4b4) .elementor-heading-title { font-size: 40px !important; }
  body .elementor-153 :is(.elementor-element.elementor-element-89c5073, .elementor-element.elementor-element-162ec16) .elementor-heading-title { font-size: 28px !important; }
  body .elementor-153 .elementor-element.elementor-element-bbc3710 { --flex-wrap: wrap; flex-wrap: wrap; }
  body .elementor-153 .elementor-element.elementor-element-bbc3710 > .e-con-inner { display: flex !important; flex-wrap: wrap !important; gap: 24px 16px !important; }
  body .elementor-153 .elementor-element.elementor-element-bbc3710 > .e-con,
  body .elementor-153 .elementor-element.elementor-element-bbc3710 > .e-con-inner > .e-con { --width: calc(50% - 8px); width: calc(50% - 8px) !important; }
}

@media (max-width: 767px) {
  /* intro */
  body .elementor-153 .elementor-element.elementor-element-79a8b7a { --padding-top: 48px; padding-top: 48px !important; --padding-bottom: 24px; padding-bottom: 24px !important; --padding-left: var(--kp-gutter); padding-left: var(--kp-gutter) !important; --padding-right: var(--kp-gutter); padding-right: var(--kp-gutter) !important; --gap: 28px; }
  body .elementor-153 .elementor-element.elementor-element-1a21684 { --padding-left: 0px; padding-left: 0px !important; --padding-right: 0px; padding-right: 0px !important; --padding-top: 0px; padding-top: 0px !important; --gap: 10px; }
  body .elementor-153 :is(.elementor-element.elementor-element-5d0b5e3, .elementor-element.elementor-element-b86e4b4) .elementor-heading-title { font-size: 30px !important; line-height: 1.15 !important; }
  body .elementor-153 .elementor-element.elementor-element-71918f6 { margin: 10px 0 !important; }
  body .elementor-153 .elementor-element.elementor-element-71918f6 img { height: 56px !important; width: auto !important; }
  body .elementor-153 :is(.elementor-element.elementor-element-89c5073, .elementor-element.elementor-element-162ec16) .elementor-heading-title { font-size: 22px !important; line-height: 1.25 !important; }
  body .elementor-153 .elementor-element.elementor-element-7ed0220 { margin-top: 8px !important; }
  body .elementor-153 .elementor-element.elementor-element-7ed0220 .elementor-heading-title { font-size: 15px !important; line-height: 1.6 !important; }
  body .elementor-153 .elementor-element.elementor-element-4c01e73 { width: 100% !important; max-width: 100% !important; --container-widget-width: 100%; text-align: center; margin: 0 !important; }
  body .elementor-153 .elementor-element.elementor-element-4c01e73 img { width: auto !important; max-width: 78% !important; height: auto !important; max-height: 380px; margin: 0 auto; display: block; object-fit: contain; }

  /* material / durability / ergonomics / workmanship: 2 x 2 grid */
  body .elementor-153 .elementor-element.elementor-element-bbc3710 {
    --flex-direction: row; flex-direction: row;
    --flex-wrap: wrap; flex-wrap: wrap;
    --gap: 16px 12px; gap: 22px 12px;
    --padding-top: 24px; padding-top: 24px !important; --padding-bottom: 56px; padding-bottom: 56px !important;
    --padding-left: var(--kp-gutter); padding-left: var(--kp-gutter) !important; --padding-right: var(--kp-gutter); padding-right: var(--kp-gutter) !important;
  }
  body .elementor-153 .elementor-element.elementor-element-bbc3710 > .e-con-inner {
    display: flex !important;
    flex-direction: row !important;
    flex-wrap: wrap !important;
    gap: 22px 12px !important;
    padding: 0 !important;
  }
  body .elementor-153 .elementor-element.elementor-element-bbc3710 > .e-con,
  body .elementor-153 .elementor-element.elementor-element-bbc3710 > .e-con-inner > .e-con {
    --width: calc(50% - 6px); width: calc(50% - 6px) !important;
    --padding-top: 0px; padding-top: 0px !important; --padding-left: 0px; padding-left: 0px !important; --padding-right: 0px; padding-right: 0px !important; --padding-bottom: 0px; padding-bottom: 0px !important;
    --gap: 10px;
  }
  body .elementor-153 .elementor-element.elementor-element-bbc3710 .elementor-widget-image img { height: 140px !important; width: 100% !important; object-fit: cover; border-radius: 12px; }
  body .elementor-153 .elementor-element.elementor-element-bbc3710 .elementor-widget-heading .elementor-heading-title { font-size: 16px !important; }
  body .elementor-153 .elementor-element.elementor-element-bbc3710 .elementor-widget-divider { --divider-width: 36px; margin: 0 !important; }

  /* dark statement boxes */
  body .elementor-153 :is(.elementor-element.elementor-element-1753931, .elementor-element.elementor-element-915d10f) {
    --padding-top: 48px; padding-top: 48px !important; --padding-bottom: 48px; padding-bottom: 48px !important;
    --padding-left: var(--kp-gutter); padding-left: var(--kp-gutter) !important; --padding-right: var(--kp-gutter); padding-right: var(--kp-gutter) !important;
    --gap: 22px; --min-height: 0px;
  }
  body .elementor-153 :is(.elementor-element.elementor-element-1b9f740, .elementor-element.elementor-element-3686e69, .elementor-element.elementor-element-f5a2f5e, .elementor-element.elementor-element-e6c8adb) img { height: 18px !important; width: auto !important; }
  body .elementor-153 :is(.elementor-element.elementor-element-f17afcf, .elementor-element.elementor-element-e5f7c30) { margin: 0 !important; }
  body .elementor-153 .elementor-element.elementor-element-f17afcf .elementor-heading-title { font-size: 18px !important; line-height: 1.6 !important; }
  body .elementor-153 .elementor-element.elementor-element-e5f7c30 .elementor-heading-title { font-size: 22px !important; line-height: 1.45 !important; }

  /* no hype */
  body .elementor-153 .elementor-element.elementor-element-5419111 { --padding-top: 56px; padding-top: 56px !important; --padding-bottom: 56px; padding-bottom: 56px !important; --padding-left: var(--kp-gutter); padding-left: var(--kp-gutter) !important; --padding-right: var(--kp-gutter); padding-right: var(--kp-gutter) !important; --gap: 16px; --min-height: 0px; }
  body .elementor-153 .elementor-element.elementor-element-83cce31 .elementor-heading-title { font-size: 14px !important; line-height: 1.6 !important; }
  body .elementor-153 .elementor-element.elementor-element-fb87731 .elementor-heading-title { font-size: 31px !important; line-height: 1.15 !important; }
  body .elementor-153 .elementor-element.elementor-element-f6db65f { margin: 0 !important; }
  body .elementor-153 .elementor-element.elementor-element-f6db65f .elementor-heading-title { font-size: 16px !important; line-height: 1.6 !important; }

  /* guaranteed to shine */
  body .elementor-153 .elementor-element.elementor-element-bcabc46 { --padding-top: 48px; padding-top: 48px !important; --padding-bottom: 48px; padding-bottom: 48px !important; --padding-left: var(--kp-gutter); padding-left: var(--kp-gutter) !important; --padding-right: var(--kp-gutter); padding-right: var(--kp-gutter) !important; --min-height: 0px; }
  body .elementor-153 .elementor-element.elementor-element-092cb7a .elementor-heading-title { font-size: 54px !important; line-height: 1.2 !important; }
}

/* =====================================================================
   8b. WHERE TO BUY (page 132)
   ===================================================================== */
@media (max-width: 767px) {
  body .elementor-132 .elementor-element.elementor-element-39346a4 { --min-height: 0px; min-height: 0; --padding-top: 72px; padding-top: 72px !important; --padding-bottom: 40px; padding-bottom: 40px !important; --padding-left: var(--kp-gutter); padding-left: var(--kp-gutter) !important; --padding-right: var(--kp-gutter); padding-right: var(--kp-gutter) !important; }
  body .elementor-132 .elementor-element.elementor-element-39346a4 > .e-con-inner { padding: 0 !important; }
  body .elementor-132 .elementor-element.elementor-element-609ea42 { --padding-top: 48px; padding-top: 48px !important; --padding-bottom: 48px; padding-bottom: 48px !important; --padding-left: var(--kp-gutter); padding-left: var(--kp-gutter) !important; --padding-right: var(--kp-gutter); padding-right: var(--kp-gutter) !important; }
  body .elementor-132 .elementor-element.elementor-element-506a975 .elementor-heading-title { font-size: 24px !important; line-height: 1.2 !important; }
  body .elementor-132 .elementor-element.elementor-element-9039a56 { --padding-top: 56px; padding-top: 56px !important; --padding-bottom: 56px; padding-bottom: 56px !important; --padding-left: var(--kp-gutter); padding-left: var(--kp-gutter) !important; --padding-right: var(--kp-gutter); padding-right: var(--kp-gutter) !important; }
  body .elementor-132 .elementor-element.elementor-element-142ce73 .elementor-heading-title { font-size: 12px !important; letter-spacing: 0.1em; }
  body .elementor-132 .elementor-element.elementor-element-846c896 { margin-left: 0 !important; margin-right: 0 !important; }
  body .elementor-132 .elementor-element.elementor-element-846c896 .elementor-heading-title { font-size: 21px !important; line-height: 1.3 !important; }
  body .elementor-132 .elementor-element.elementor-element-e1a9a94 .elementor-heading-title { font-size: 15px !important; line-height: 1.65 !important; }
}

/* =====================================================================
   9. CATALOGUE – banner, filters (phones: accordion in a slide-up drawer), grid
   ===================================================================== */
/* accordion: phones only – tablet / desktop keep the original sidebar */
@media (max-width: 767px) {
body .kk-cat__group { margin-bottom: 0 !important; border-bottom: 1px solid rgba(0, 97, 61, 0.14); }
body .kk-cat__group:last-child { border-bottom: 0; }
body .kk-cat__side .kp-acc__btn {
  display: flex;
  align-items: center;
  justify-content: space-between;
  width: 100%;
  margin: 0;
  padding: 18px 0;
  border: 0;
  background: none;
  color: var(--kp-orange);
  font: inherit;
  font-size: 12px;
  font-weight: 600;
  letter-spacing: 0.12em;
  text-transform: uppercase;
  text-align: left;
  cursor: pointer;
  box-shadow: none;
}
body .kk-cat__side .kp-acc__btn:hover,
body .kk-cat__side .kp-acc__btn:focus { background: none; color: var(--kp-orange); }
body .kk-cat__side .kp-acc__btn:focus-visible { outline: 2px solid var(--kp-gold); outline-offset: 4px; }
body .kk-cat__side .kp-acc__count {
  margin-left: 8px;
  padding: 2px 8px;
  border-radius: 99px;
  background: rgba(0, 97, 61, 0.1);
  color: var(--kp-green-dark);
  font-size: 11px;
  letter-spacing: 0;
}
body .kk-cat__side .kp-acc__icon {
  position: relative;
  flex: 0 0 auto;
  width: 12px; height: 12px;
  margin-left: auto;
}
body .kk-cat__side .kp-acc__icon::before,
body .kk-cat__side .kp-acc__icon::after {
  content: "";
  position: absolute;
  left: 0; top: 50%;
  width: 12px; height: 1.6px;
  margin-top: -0.8px;
  background: var(--kp-green-dark);
  transition: transform 0.35s var(--kp-ease);
}
body .kk-cat__side .kp-acc__icon::after { transform: rotate(90deg); }
body .kk-cat__side .kp-acc.is-open .kp-acc__icon::after { transform: rotate(0deg); }
body .kk-cat__side .kp-acc__panel {
  display: grid;
  grid-template-rows: 0fr;
  transition: grid-template-rows 0.4s var(--kp-ease);
}
body .kk-cat__side .kp-acc.is-open .kp-acc__panel { grid-template-rows: 1fr; }
body .kk-cat__side .kp-acc__panel > .kk-cat__list { min-height: 0; overflow: hidden; margin: 0 !important; }
body .kk-cat__side .kp-acc.is-open .kp-acc__panel > .kk-cat__list { padding-bottom: 16px; }
body .kk-cat__side .kk-cat__link { transition: color 0.2s ease, padding 0.3s var(--kp-ease); }
body .kk-cat__side .kk-cat__link.is-active { color: var(--kp-orange) !important; }
@media (hover: hover) {
  body .kk-cat__side .kk-cat__link:hover { padding-left: 4px; }
}
}

/* filter bar + drawer (phones only) */
body .kp-filterbar { display: none; }
body .kp-drawer__head, body .kp-drawer__foot { display: none; }

@media (max-width: 1024px) {
  body .kk-hero { padding-top: 56px !important; padding-bottom: 40px !important; }
  body .kk-hero .kk-hero__crumbs { font-size: 11px; margin-bottom: 8px; }
}

@media (max-width: 767px) {

  body .kp-filterbar {
    position: sticky;
    top: 68px;                     /* below the sticky mobile header */
    z-index: 30;
    display: flex;
    align-items: center;
    gap: 10px;
    margin: 0 0 20px;
    padding: 12px 0;
    background: #ffffff;
  }
  body .kp-filterbar__btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 11px 18px;
    border: 1px solid var(--kp-green-dark);
    border-radius: 999px;
    background: #ffffff;
    color: var(--kp-green-dark);
    font: inherit;
    font-size: 14px;
    font-weight: 600;
    line-height: 1;
    cursor: pointer;
  }
  body .kp-filterbar__btn svg { width: 16px; height: 16px; }
  body .kp-filterbar__btn:hover, body .kp-filterbar__btn:focus { background: var(--kp-green-dark); color: #ffffff; }
  body .kp-filterbar__chip {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    min-width: 0;
    padding: 8px 12px;
    border-radius: 999px;
    background: var(--kp-mint);
    color: var(--kp-green-dark);
    font-size: 13px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    text-decoration: none;
  }
  body .kp-filterbar__chip b { font-weight: 600; }

  /* the sidebar becomes a slide-up drawer (moved to <body> by the script) */
  body .kk-cat { display: block !important; }
  body .kk-cat__side.kp-drawer {
    position: fixed !important;
    left: 0; right: 0; bottom: 0; top: auto;
    z-index: 1200;
    width: 100% !important;
    max-width: none !important;
    max-height: 86vh;
    max-height: 86dvh;
    margin: 0 !important;
    padding: 0 !important;
    display: flex !important;
    flex-direction: column;
    border-radius: 20px 20px 0 0;
    background: #ffffff !important;
    box-shadow: 0 -10px 40px rgba(0, 0, 0, 0.2);
    font-family: "Satoshi", "Helvetica Neue", Arial, sans-serif;
    transform: translateY(105%);
    visibility: hidden;
    transition: transform 0.45s var(--kp-ease), visibility 0s linear 0.45s;
  }
  body .kk-cat__side.kp-drawer::before { display: none !important; }
  html.kp-filters-open body .kk-cat__side.kp-drawer { transform: none; visibility: visible; transition: transform 0.45s var(--kp-ease), visibility 0s; }
  body .kp-drawer .kk-cat__list { list-style: none; padding: 0 !important; }
  body .kp-drawer .kk-cat__link { color: var(--kp-green-dark); text-decoration: none; font-weight: 500; }
  body .kp-drawer__body { flex: 1 1 auto; overflow-y: auto; padding: 0 22px; overscroll-behavior: contain; }
  body .kp-drawer__head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 18px 22px 14px;
    border-bottom: 1px solid #eeeeee;
  }
  body .kp-drawer__head::before {
    content: "";
    position: absolute;
    left: 50%; top: 7px;
    width: 40px; height: 4px;
    margin-left: -20px;
    border-radius: 4px;
    background: #d9d9d9;
  }
  body .kp-drawer__title { margin: 0; font-size: 18px; font-weight: 700; color: var(--kp-ink); }
  body .kp-drawer__close {
    width: 36px; height: 36px;
    padding: 0 !important;
    border: 0 !important;
    border-radius: 50% !important;
    background: #f2f2f2 !important;
    color: var(--kp-ink) !important;
    font-size: 22px;
    line-height: 36px;
    cursor: pointer;
  }
  body .kp-drawer__foot {
    display: flex;
    gap: 10px;
    padding: 14px 22px calc(14px + env(safe-area-inset-bottom));
    border-top: 1px solid #eeeeee;
  }
  body .kp-drawer__foot a, body .kp-drawer__foot button {
    flex: 1;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-height: 46px;
    border-radius: 999px;
    font: inherit;
    font-size: 14px;
    font-weight: 600;
    text-decoration: none;
    cursor: pointer;
  }
  body .kp-drawer-overlay {
    position: fixed;
    inset: 0;
    z-index: 1150;
    background: rgba(0, 0, 0, 0.45);
    opacity: 0;
    visibility: hidden;
    transition: opacity 0.35s ease, visibility 0s linear 0.35s;
  }
  html.kp-filters-open .kp-drawer-overlay { opacity: 1; visibility: visible; transition: opacity 0.35s ease, visibility 0s; }
  html.kp-filters-open, html.kp-filters-open body { overflow: hidden; }

  body .kk-cat .kk-cat__main { width: 100% !important; padding: 8px var(--kp-gutter) 56px !important; }
  body .kp-drawer .kk-cat__link { font-size: 16px; padding: 9px 0; display: block; }
  body .kp-drawer .kk-cat__list li { margin: 0 !important; }
  body .kp-drawer__reset { border: 1px solid #cccccc !important; background: #ffffff !important; color: var(--kp-ink) !important; }
  body .kp-drawer__done { border: 0 !important; background: var(--kp-green-dark) !important; color: #ffffff !important; }
}
@media (max-width: 767px) {
  body .kk-cat ul.products { display: grid !important; grid-template-columns: 1fr 1fr; gap: 12px; }
  body .kk-cat ul.products::before, body .kk-cat ul.products::after { display: none !important; }
  body .kk-cat ul.products li.product { width: 100% !important; margin: 0 !important; float: none !important; padding: 12px !important; border-radius: 14px !important; }
  body .kk-cat ul.products li.product img { height: 130px !important; object-fit: contain; margin-bottom: 10px !important; }
  body .kk-cat ul.products li.product .woocommerce-loop-product__title { font-size: 13px !important; line-height: 1.35; padding: 0 !important; }
  body .kk-cat .kk-loop-code { font-size: 10px; }
  body .kk-cat .kk-loop-more { font-size: 11px; }
}

/* =====================================================================
   10. SINGLE PRODUCT – mobile text sizes
   ===================================================================== */
@media (max-width: 767px) {
  body .kk-sp .kk-sp__wood { padding-top: 44px !important; padding-bottom: 28px !important; }
  body .kk-sp .kk-sp__wood img.kk-sp__img { max-height: 300px; width: auto !important; margin: 0 auto; object-fit: contain; }
  body .kk-sp .kk-sp__title { font-size: 26px !important; line-height: 1.15; }
  body .kk-sp .kk-features li { font-size: 15px; }
  body .kk-sp .kk-pinfo__title { font-size: 18px; }
  body .kk-sp .kk-pinfo__row { font-size: 14px; }
}

/* =====================================================================
   11. Reduced motion
   ===================================================================== */
@media (prefers-reduced-motion: reduce) {
  body .kk-cat__side, body .kp-drawer-overlay, body .kk-cat__side .kp-acc__panel, body #masthead { transition: none !important; }
}


/* =====================================================================
   12. DESIGN v3 – PHASE 1 (global layout and fonts)
   X-01 width, X-02 heading font, X-03 desktop sizes, X-04 heading green.
   Widths/sizes apply on desktop only (>= 1025px); tablet/mobile keep
   their current values. At 1440px and below the page width is unchanged.
   ===================================================================== */

/* X-02 headings: Satoshi Black (were Albula Pro SemiBold / Aeonik Pro Bold) */
body .elementor-43 .elementor-element.elementor-element-9a43ca3 .elementor-heading-title,
body .elementor-43 .elementor-element.elementor-element-cb4ed5d .elementor-heading-title,
body .elementor-43 .elementor-element.elementor-element-b21629b .elementor-heading-title,
body .elementor-43 .elementor-element.elementor-element-665f377 .elementor-heading-title,
body .elementor-43 .elementor-element.elementor-element-252f948 .elementor-heading-title,
body .elementor-43 .elementor-element.elementor-element-f8975af .elementor-heading-title,
body .elementor-43 .elementor-element.elementor-element-bcefc1b .elementor-heading-title,
body .elementor-43 .elementor-element.elementor-element-884f790 .elementor-heading-title,
body .elementor-43 .elementor-element.elementor-element-36490c5 .elementor-heading-title,
body .elementor-153 .elementor-element.elementor-element-5d0b5e3 .elementor-heading-title,
body .elementor-153 .elementor-element.elementor-element-b86e4b4 .elementor-heading-title,
body .elementor-153 .elementor-element.elementor-element-89c5073 .elementor-heading-title,
body .elementor-153 .elementor-element.elementor-element-162ec16 .elementor-heading-title,
body .elementor-153 .elementor-element.elementor-element-6d7d498 .elementor-heading-title,
body .elementor-153 .elementor-element.elementor-element-2dd9171 .elementor-heading-title,
body .elementor-153 .elementor-element.elementor-element-ceb558a .elementor-heading-title,
body .elementor-153 .elementor-element.elementor-element-29ee88f .elementor-heading-title,
body .elementor-153 .elementor-element.elementor-element-fb87731 .elementor-heading-title,
body .elementor-132 .elementor-element.elementor-element-846c896 .elementor-heading-title,
body .elementor-43 .elementor-element.elementor-element-85d3640 .elementor-icon-box-title,
body .elementor-43 .elementor-element.elementor-element-63b71db .elementor-icon-box-title,
body .elementor-43 .elementor-element.elementor-element-704d045 .elementor-icon-box-title {
  font-family: "Satoshi", "Helvetica Neue", Arial, sans-serif !important;
  font-weight: 900 !important;
}
/* X-02 buttons and footer links: Satoshi (were Aeonik Pro); form fields keep Aeonik Pro */
body .elementor-widget-button .elementor-button,
body .elementor-30 .elementor-element.elementor-element-501b3e5 .elementor-icon-list-text {
  font-family: "Satoshi", "Helvetica Neue", Arial, sans-serif !important;
}

/* X-04 dark green in headings: #00653f -> #00623d */
body .elementor-43 .elementor-element.elementor-element-cb4ed5d .elementor-heading-title,
body .elementor-43 .elementor-element.elementor-element-884f790 .elementor-heading-title,
body .elementor-132 .elementor-element.elementor-element-506a975 .elementor-heading-title,
body .elementor-153 .elementor-element.elementor-element-5d0b5e3 .elementor-heading-title,
body .elementor-153 .elementor-element.elementor-element-89c5073 .elementor-heading-title {
  color: #00623d !important;
}

@media (min-width: 1025px) {
  /* X-01 page content width: 1646px at 1920px (137px side margins), 1300px at 1440px and below */
  body .e-con.e-con-boxed {
    --content-width: max(min(1646px, calc(100vw - 274px)), min(1300px, 100vw)) !important;
  }
  :root {
    --kk-page-w: max(min(1646px, calc(100vw - 274px)), min(1300px, 100vw));
  }

  /* X-03 section headings ~34px */
  body .elementor-43 .elementor-element.elementor-element-9a43ca3 .elementor-heading-title,
  body .elementor-43 .elementor-element.elementor-element-cb4ed5d .elementor-heading-title,
  body .elementor-43 .elementor-element.elementor-element-884f790 .elementor-heading-title,
  body .elementor-43 .elementor-element.elementor-element-36490c5 .elementor-heading-title,
  body .elementor-132 .elementor-element.elementor-element-506a975 .elementor-heading-title,
  body .elementor-132 .elementor-element.elementor-element-846c896 .elementor-heading-title {
    font-size: 33.7px !important;
    line-height: 1.2 !important;
  }
  /* X-03 body text 22px */
  body .elementor-43 .elementor-element.elementor-element-1bde407 .elementor-heading-title,
  body .elementor-43 .elementor-element.elementor-element-08f48e7 .elementor-heading-title,
  body .elementor-43 .elementor-element.elementor-element-85d3640 .elementor-icon-box-description,
  body .elementor-43 .elementor-element.elementor-element-63b71db .elementor-icon-box-description,
  body .elementor-43 .elementor-element.elementor-element-704d045 .elementor-icon-box-description,
  body .elementor-43 .elementor-element.elementor-element-51cf488 .elementor-icon-list-text,
  body .elementor-132 .elementor-element.elementor-element-e1a9a94 .elementor-heading-title,
  body .elementor-153 .elementor-element.elementor-element-7ed0220 .elementor-heading-title {
    font-size: 22.2px !important;
    line-height: 1.45 !important;
  }
}


/* =====================================================================
   13. DESIGN v3 – PHASE 2 (header, all pages)
   X-05 marquee text, X-07 menu icon, X-08..X-10 language switch,
   X-11/X-12 edges, X-13 logo, X-14 WHERE TO BUY, X-15 inner-page header
   height, X-16 inner-page header colour, W-02/K-04/P-01/A-03 white line.
   Sizes are desktop (>= 1025px); tablet/mobile keep their current sizes.
   ===================================================================== */

/* X-05 "KLEENING SINCE 2003": Albula Pro Bold, light grey */
body #masthead .sk-marquee__group span {
  font-family: "Albula Pro", "Satoshi", Arial, sans-serif !important;
  font-weight: 700 !important;
  color: #b5b5b5 !important;
}

/* X-07 menu icon: three thin, equal lines (drawn in CSS, the old icon is hidden) */
body #masthead .elementor-element.elementor-element-98f606d .elementor-icon svg { display: none !important; }
body #masthead .elementor-element.elementor-element-98f606d .elementor-icon {
  display: block;
  width: 36px;
  height: 19px;
  font-size: 0;
  background:
    linear-gradient(#ffffff, #ffffff) 0 0 / 100% 2px no-repeat,
    linear-gradient(#ffffff, #ffffff) 0 50% / 100% 2px no-repeat,
    linear-gradient(#ffffff, #ffffff) 0 100% / 100% 2px no-repeat;
  cursor: pointer;
}
@media (max-width: 1024px) {
  body #masthead .elementor-element.elementor-element-98f606d .elementor-icon { width: 28px; height: 15px; }
}
@media (max-width: 767px) {
  body #masthead .elementor-element.elementor-element-98f606d .elementor-icon { width: 26px; height: 14px; }
}

/* X-08 language order: ENGLISH first, then Arabic */
body #masthead .gtranslate_wrapper { display: flex !important; flex-direction: row !important; align-items: center; justify-content: flex-start !important; gap: 12px !important; }
@media (max-width: 767px) {
  body #masthead .gtranslate_wrapper { flex-direction: column !important; align-items: flex-start; gap: 3px !important; }
}
body #masthead .gtranslate_wrapper a[data-gt-lang="en"] { order: 1 !important; }
body #masthead .gtranslate_wrapper a[data-gt-lang="ar"] { order: 2 !important; }
/* X-09 ENGLISH: Albula Pro Bold */
body #masthead a[data-gt-lang="en"]::after {
  font-family: "Albula Pro", "Satoshi", Arial, sans-serif !important;
  font-weight: 700 !important;
}
/* X-10 Arabic: Janna LT Bold, full white */
body #masthead a[data-gt-lang="ar"]::after {
  font-family: "Janna LT", "Noto Sans Arabic", Tahoma, sans-serif !important;
  font-weight: 700 !important;
  opacity: 1 !important;
}

/* X-16 inner pages: the header carries its own dark-green tiled background
   (the old green strips behind it are hidden; they left the top of the header white) */
body:not(.home) #masthead {
  background-color: #003d23;
  background-image:
    linear-gradient(180deg, rgba(0, 56, 32, 0.9) 0%, rgba(0, 67, 38, 0.9) 100%),
    url("https://soharon.co.uk/off-29/wp-content/uploads/2026/10/Emerald-Mosaic-Tech-Background.png");
  background-size: cover;
  background-position: center;
}
body.page-id-153 .elementor-element.elementor-element-3e090a4,
body.page-id-132 .elementor-element.elementor-element-54f2649,
body:not(.home) .kk-sp-bg { display: none !important; }
/* the stuck mobile header keeps the same dark green */
body:not(.home) #masthead.kp-stuck { background-color: #003d23; }

@media (min-width: 1025px) {
  /* X-05 size */
  body #masthead .sk-marquee__group span { font-size: 16.8px !important; }

  /* X-11 menu icon starts on the page edge (135px at 1920) */
  body #masthead .elementor-element.elementor-element-f760a15 { padding-left: 0 !important; margin-left: -2px; gap: 47px !important; --gap: 47px; }

  /* X-09 / X-10 sizes */
  body #masthead .gtranslate_wrapper { gap: 16px !important; }
  body #masthead a[data-gt-lang="en"]::after { font-size: 16px !important; }
  body #masthead a[data-gt-lang="ar"]::after { font-size: 17px !important; }

  /* X-13 logo ~364px wide */
  body #masthead .elementor-element.elementor-element-ae1ae2e img { width: 364px !important; max-width: none; height: auto; }

  /* X-14 WHERE TO BUY ~179 x 52, Satoshi Bold 18.9px */
  body #masthead .elementor-element.elementor-element-0a2ca92 .elementor-button {
    padding: 16px 20px !important;
    font-family: "Satoshi", "Helvetica Neue", Arial, sans-serif !important;
    font-size: 18.9px !important;
    font-weight: 700 !important;
    line-height: 20px !important;
  }

  /* X-15 inner-page header 310px (incl. the marquee) + 9px white line before the banner (W-02, K-04, P-01, A-03) */
  body:not(.home) #masthead { border-bottom: 9px solid #ffffff; }
  body:not(.home) #masthead .elementor-element.elementor-element-4a693fd {
    --min-height: calc(310px - var(--kp-marquee-h, 47px));
    min-height: calc(310px - var(--kp-marquee-h, 47px)) !important;
  }
}


/* =====================================================================
   14. DESIGN v3 – PHASE 3 (footer, all pages)
   X-22 quick links, X-23 buttons, X-24 long arrow, X-26 logo,
   X-27 copyright, X-28 height. (X-21 / X-25 links: see kk-polish.js)
   Sizes and layout are desktop (>= 1025px); tablet/mobile keep theirs.
   ===================================================================== */

/* X-24 long thin arrow on the footer buttons (replaces the short solid arrow) */
body .elementor-30 .elementor-element.elementor-element-6e495df .elementor-button-icon {
  position: relative;
  display: inline-block;
  width: 40px;
  height: 10px;
  flex: 0 0 auto;
}
body .elementor-30 .elementor-element.elementor-element-6e495df .elementor-button-icon svg,
body .elementor-30 .elementor-element.elementor-element-6e495df .elementor-button-icon i { display: none !important; }
body .elementor-30 .elementor-element.elementor-element-6e495df .elementor-button-icon::before {
  content: "";
  position: absolute;
  left: 0; right: 1px; top: 50%;
  height: 1.5px;
  margin-top: -0.75px;
  background: currentColor;
}
body .elementor-30 .elementor-element.elementor-element-6e495df .elementor-button-icon::after {
  content: "";
  position: absolute;
  right: 1px; top: 50%;
  width: 7px; height: 7px;
  border-top: 1.5px solid currentColor;
  border-right: 1.5px solid currentColor;
  transform: translateY(-50%) rotate(45deg);
}
@media (max-width: 1024px) {
  body .elementor-30 .elementor-element.elementor-element-6e495df .elementor-button-icon { width: 30px; }
}

/* contact form section: room for the sticky header when jumped to */
#contact { scroll-margin-top: 90px; }

@media (min-width: 1025px) {
  /* X-28 footer 222px; content aligned like the design */
  body .elementor-30 .elementor-element.elementor-element-ab6f316 {
    --min-height: 222px; min-height: 222px !important;
    padding-top: 0 !important; padding-bottom: 0 !important;
  }
  body .elementor-30 .elementor-element.elementor-element-ab6f316 > .e-con-inner {
    display: flex !important;
    flex-direction: row !important;
    flex-wrap: nowrap !important;
    align-items: flex-start !important;
    justify-content: flex-start !important;
    gap: 0 !important;
    padding: 0 !important;
  }

  /* X-26 logo ~285px wide, starting 160px from the left at 1920 */
  body .elementor-30 .elementor-element.elementor-element-9f80d62 {
    width: 285px !important; max-width: none !important; flex: 0 0 285px;
    margin: 21px 0 0 23px !important;
  }
  body .elementor-30 .elementor-element.elementor-element-9f80d62 img { width: 285px !important; max-width: none; height: auto; }

  /* X-22 quick links block (starts at ~604px at 1920) */
  body .elementor-30 .elementor-element.elementor-element-ce94520 {
    width: auto !important; flex: 0 0 auto !important;
    margin: 0 0 0 min(159px, 8.28vw) !important;
    padding: 58px 0 0 !important;
    --gap: 8px; gap: 8px !important;
  }
  body .elementor-30 .elementor-element.elementor-element-0302daf .elementor-heading-title {
    font-family: "Satoshi", "Helvetica Neue", Arial, sans-serif !important;
    font-weight: 700 !important;
    font-size: 18px !important;
    line-height: 1.2 !important;
  }
  body .elementor-30 .elementor-element.elementor-element-501b3e5 .elementor-icon-list-text,
  body .elementor-30 .elementor-element.elementor-element-501b3e5 .elementor-icon-list-text a {
    font-family: "Satoshi", "Helvetica Neue", Arial, sans-serif !important;
    font-weight: 400 !important;
    font-size: 19px !important;
    line-height: 1.4 !important;
  }

  /* quick links: wider space around the "|" separators, as in the design */
  body .elementor-30 .elementor-element.elementor-element-501b3e5 .elementor-icon-list-items.elementor-inline-items { margin-inline: -13px !important; }
  body .elementor-30 .elementor-element.elementor-element-501b3e5 .elementor-icon-list-items.elementor-inline-items .elementor-icon-list-item { margin-inline: 13px !important; }
  body .elementor-30 .elementor-element.elementor-element-501b3e5 .elementor-icon-list-items.elementor-inline-items .elementor-icon-list-item::after { inset-inline-end: -13px !important; height: 62% !important; }

  /* X-23 buttons: 52px tall, ~211 / 272 / 211px, Satoshi Bold 19px, 14px apart, ending on the page edge */
  body .elementor-30 .elementor-element.elementor-element-6e495df {
    width: auto !important; flex: 1 1 auto !important;
    margin: 0 0 0 auto !important;
    padding: 59px 0 0 !important;
    display: flex !important;
    flex-direction: row !important;
    flex-wrap: wrap !important;
    justify-content: flex-end !important;
    align-content: flex-start !important;
    column-gap: 14px !important;
    row-gap: 38px !important;
  }
  body .elementor-30 .elementor-element.elementor-element-6e495df > .elementor-widget-button { margin: 0 !important; flex: 0 0 auto; }
  body .elementor-30 .elementor-element.elementor-element-65405a2,
  body .elementor-30 .elementor-element.elementor-element-65405a2 .elementor-button { width: 211px !important; }
  body .elementor-30 .elementor-element.elementor-element-9649bf7,
  body .elementor-30 .elementor-element.elementor-element-9649bf7 .elementor-button { width: 272px !important; }
  body .elementor-30 .elementor-element.elementor-element-cec317d,
  body .elementor-30 .elementor-element.elementor-element-cec317d .elementor-button { width: 211px !important; }
  body .elementor-30 .elementor-element.elementor-element-6e495df .elementor-button {
    display: flex !important;
    align-items: center;
    justify-content: center;
    height: 52px;
    padding: 0 16px !important;
    box-sizing: border-box;
    font-family: "Satoshi", "Helvetica Neue", Arial, sans-serif !important;
    font-weight: 700 !important;
    font-size: 19px !important;
    line-height: 1 !important;
    white-space: nowrap;
  }
  body .elementor-30 .elementor-element.elementor-element-6e495df .elementor-button-content-wrapper { gap: 14px !important; align-items: center; }

  /* X-27 copyright: Satoshi Regular 19px, centred under the buttons */
  body .elementor-30 .elementor-element.elementor-element-4ba5bf5 {
    flex: 0 0 100% !important; width: 100% !important;
    margin: 0 !important;
    padding-left: calc(100% - 211px - 272px - 211px - 28px) !important;
    box-sizing: border-box;
  }
  body .elementor-30 .elementor-element.elementor-element-4ba5bf5 .elementor-heading-title {
    font-family: "Satoshi", "Helvetica Neue", Arial, sans-serif !important;
    font-weight: 400 !important;
    font-size: 19px !important;
    line-height: 1.2 !important;
    text-align: center !important;
  }
}



/* =====================================================================
   15. DESIGN v3 – PHASE 4 (home page)
   Hero H-03..H-09, logos L-01/L-03..L-05, green strip G-01..G-12,
   cards S-01..S-16, One Home O-01..O-07/O-10/O-13/O-14,
   featured product F-01..F-15, contact C-01..C-09.
   Line breaks, card links and the hotspot pop-up side are set in kk-polish.js.
   Colours apply at every width; sizes and layout on desktop (>= 1025px) only.
   ===================================================================== */

/* forced design line breaks only on desktop */
.kp-br { display: none; }
@media (min-width: 1025px) { .kp-br { display: inline; } }

/* ---------- colours (all widths) ---------- */
/* L-01 logo strip white */
body .elementor-43 .elementor-element.elementor-element-287ec07,
body .elementor-43 .elementor-element.elementor-element-287ec07 > .elementor-motion-effects-container > .elementor-motion-effects-layer { background-color: #ffffff !important; }
/* G-02 green strip: no blue flash while the image loads */
body .elementor-43 .elementor-element.elementor-element-b12f160 { background-color: #0c4c31 !important; }
/* O-01 One Home background */
body .elementor-43 .elementor-element.elementor-element-7149c5d,
body .elementor-43 .elementor-element.elementor-element-7149c5d > .elementor-motion-effects-container > .elementor-motion-effects-layer { background-color: #f0f8f5 !important; }
/* S-12..S-16 EXPLORE colours */
body .elementor-43 .elementor-element.elementor-element-48efe92 .elementor-button { color: #f05a38 !important; }
body .elementor-43 .elementor-element.elementor-element-fe08656 .elementor-button { color: #00915d !important; }
body .elementor-43 .elementor-element.elementor-element-98d794c .elementor-button { color: #f1af1d !important; }
body .elementor-43 .elementor-element.elementor-element-3cf0f5b .elementor-button { color: #45a3d7 !important; }
body .elementor-43 .elementor-element.elementor-element-33cb841 .elementor-button { color: #707070 !important; }
/* F-05 dark panel, F-12 button colour */
body .elementor-43 .elementor-element.elementor-element-1676003 { background-color: #1f2324 !important; }
body .elementor-43 .elementor-element.elementor-element-041e5ca .elementor-button { background-color: #f05a38 !important; }
/* O-13 hotspots: flat green circle, thin white ring, no glow */
body .sk-hs .sk-hs__dot { animation: none !important; box-shadow: none !important; border-width: 2px !important; }
/* C-06 field borders pale yellow, C-07 placeholder grey */
body .cf7-custom-form input[type="text"],
body .cf7-custom-form input[type="email"],
body .cf7-custom-form input[type="tel"],
body .cf7-custom-form textarea { border-color: #fae3af !important; }
body .cf7-custom-form input::placeholder,
body .cf7-custom-form textarea::placeholder { color: #808080 !important; }

@media (min-width: 1025px) {

  /* ================= HERO (section.kk-sh) ================= */
  /* H-03 natural brightness, dark fade only at the very top */
  body .kk-sh { --kk-sh-overlay: linear-gradient(180deg, rgba(0, 0, 0, 0.42) 0%, rgba(0, 0, 0, 0.12) 12%, rgba(0, 0, 0, 0) 24%); }
  body .kk-sh .kk-sh__overlay::after { display: none; }
  /* H-04 card on the page grid (138px at 1920), bottom ~249px above the hero bottom */
  body .kk-sh .kk-sh__inner { width: max(min(1646px, calc(100vw - 274px)), min(1300px, calc(100vw - 48px))); }
  body .kk-sh .kk-sh__card--left,
  body .kk-sh .kk-sh__card--right,
  body .kk-sh .kk-sh__card--center { bottom: 23vh; }
  /* H-05 card ~555 x 290, radius ~30, thick soft frosted edge */
  body .kk-sh .kk-sh__card {
    width: min(555px, 100%);
    min-height: 290px;
    padding: 36px 47px 40px;
    border-radius: 30px;
    background: rgba(255, 255, 255, 0.55);
    -webkit-backdrop-filter: blur(20px) saturate(140%);
    backdrop-filter: blur(20px) saturate(140%);
    border: 1.5px solid rgba(255, 255, 255, 0.9);
    box-shadow:
      inset 0 0 0 6px rgba(255, 255, 255, 0.28),
      inset 0 0 22px rgba(255, 255, 255, 0.45),
      0 18px 50px rgba(0, 0, 0, 0.22);
  }
  body .kk-sh .kk-sh__card--center { margin-left: calc(min(555px, 100%) / -2); }
  /* H-06 code line */
  body .kk-sh .kk-sh__eyebrow { font-size: 22px; font-weight: 400; letter-spacing: 0; line-height: 1.2; text-transform: uppercase; }
  /* H-07 title */
  body .kk-sh h2.kk-sh__title { margin: 6px 0 14px; font-size: 33.7px; font-weight: 900; line-height: 1.2; letter-spacing: 0.02em; }
  /* H-08 sub-line */
  body .kk-sh .kk-sh__text { margin: 0 0 24px; font-size: 22px; line-height: 1.3; }
  /* H-09 LET'S DISCOVER pill ~200 x 38 */
  body .kk-sh a.kk-sh__btn {
    justify-content: center;
    width: 200px; height: 38px;
    padding: 0;
    box-sizing: border-box;
    font-size: 16px; font-weight: 400;
    letter-spacing: 0.12em;
    line-height: 1;
  }

  /* ================= LOGO STRIP (287ec07) ================= */
  /* L-03 full width (60px margins), L-05 170px tall, L-04 larger logos */
  body .elementor-43 .elementor-element.elementor-element-287ec07 {
    --content-width: calc(100vw - 120px) !important;
    --min-height: 170px; min-height: 170px !important;
    padding-top: 0 !important; padding-bottom: 0 !important;
    justify-content: center;
  }
  body .elementor-43 .elementor-element.elementor-element-287ec07 > .e-con-inner { justify-content: center; padding: 0 !important; }
  body .elementor-43 .elementor-element.elementor-element-631eaef { width: 100%; }
  body .elementor-43 .elementor-element.elementor-element-631eaef .swiper-slide { display: flex; justify-content: center; align-items: center; }
  body .elementor-43 .elementor-element.elementor-element-631eaef .swiper-slide img,
  body .elementor-43 .elementor-element.elementor-element-631eaef .swiper-slide-image { width: 100% !important; max-width: 250px !important; height: auto !important; }

  /* ================= GREEN STRIP (b12f160) ================= */
  /* G-01 471px */
  body .elementor-43 .elementor-element.elementor-element-b12f160 {
    --min-height: 471px; min-height: 471px !important;
    padding-top: 88px !important; padding-bottom: 40px !important;
  }
  body .elementor-43 .elementor-element.elementor-element-b12f160 > .e-con-inner { align-items: flex-start !important; padding: 0 !important; }
  /* G-07 left block on the page edge */
  body .elementor-43 .elementor-element.elementor-element-ad39a46 { padding: 0 !important; --gap: 0px; gap: 0 !important; }
  body .elementor-43 .elementor-element.elementor-element-9a43ca3,
  body .elementor-43 .elementor-element.elementor-element-1bde407,
  body .elementor-43 .elementor-element.elementor-element-c03da2d { margin: 0 !important; }
  body .elementor-43 .elementor-element.elementor-element-9a43ca3 .elementor-heading-title { line-height: 1.24 !important; }
  body .elementor-43 .elementor-element.elementor-element-1bde407 { margin-top: 24px !important; }
  body .elementor-43 .elementor-element.elementor-element-1bde407 .elementor-heading-title { line-height: 33px !important; }
  body .elementor-43 .elementor-element.elementor-element-c03da2d { margin-top: 44px !important; }
  /* G-06 READ MORE ~200 x 38, Satoshi Regular 16, wide spacing */
  body .elementor-43 .elementor-element.elementor-element-c03da2d .elementor-button {
    display: inline-flex; align-items: center; justify-content: center;
    width: 200px; height: 38px; padding: 0 !important; box-sizing: border-box;
    font-family: "Satoshi", "Helvetica Neue", Arial, sans-serif !important;
    font-size: 16px !important; font-weight: 400 !important;
    letter-spacing: 0.12em !important; line-height: 1 !important;
  }
  /* G-11 columns centred at ~1017 / 1341 / 1647 (at 1920): 308 / 340 / 272 wide, ending on the page edge */
  body .elementor-43 .elementor-element.elementor-element-755069b {
    --width: auto; width: 920px !important; max-width: 920px; flex: 0 0 920px;
    margin-left: auto !important;
    padding: 9px 0 0 !important;
    --gap: 0px; gap: 0 !important;
    flex-wrap: nowrap !important;
  }
  body .elementor-43 .elementor-element.elementor-element-85d3640 { width: 308px !important; max-width: 308px !important; flex: 0 0 308px; }
  body .elementor-43 .elementor-element.elementor-element-63b71db { width: 340px !important; max-width: 340px !important; flex: 0 0 340px; }
  body .elementor-43 .elementor-element.elementor-element-704d045 { width: 272px !important; max-width: 272px !important; flex: 0 0 272px; }
  /* G-12 1px dividers */
  body .elementor-43 .elementor-element.elementor-element-85d3640,
  body .elementor-43 .elementor-element.elementor-element-63b71db { border-right-width: 1px !important; }
  /* G-08 / G-09 column text */
  body .elementor-43 :is(.elementor-element.elementor-element-85d3640, .elementor-element.elementor-element-63b71db, .elementor-element.elementor-element-704d045) .elementor-icon-box-title { font-size: 29px !important; letter-spacing: 0.03em; margin-bottom: 10px !important; }
  body .elementor-43 :is(.elementor-element.elementor-element-85d3640, .elementor-element.elementor-element-63b71db, .elementor-element.elementor-element-704d045) .elementor-icon-box-description { line-height: 27px !important; }

  /* ================= SOLUTION CARDS (975d254) ================= */
  /* S-02 772px */
  body .elementor-43 .elementor-element.elementor-element-975d254 {
    --min-height: 772px; min-height: 772px !important;
    padding-top: 122px !important; padding-bottom: 96px !important;
  }
  body .elementor-43 .elementor-element.elementor-element-975d254 > .e-con-inner { padding: 0 !important; --gap: 34px; gap: 34px !important; }
  body .elementor-43 .elementor-element.elementor-element-cb4ed5d { margin: 0 !important; }
  /* S-03 / S-04 five cards ~321 x 477, photo ~266 */
  body .elementor-43 .elementor-element.elementor-element-79b14eb { padding: 0 !important; --gap: 10px; gap: 10px !important; flex-wrap: nowrap !important; }
  body .elementor-43 .elementor-element.elementor-element-79b14eb > .kkc-card,
  body .elementor-43 .elementor-element.elementor-element-79b14eb > .e-con {
    --width: auto; width: calc((100% - 40px) / 5) !important; flex: 1 1 0 !important;
    min-height: 477px;
    padding: 0 0 19px !important;
    --gap: 0px; gap: 0 !important;
  }
  body .elementor-43 .kkc-card .elementor-widget-image { margin: 0 !important; }
  body .elementor-43 .kkc-card .elementor-widget-image img { height: 266px !important; width: 100% !important; object-fit: cover; }
  /* S-05 66px badge with a white ring, centred on the photo edge */
  body .elementor-43 .kkc-card .elementor-widget-icon { margin: -33px 0 0 14px !important; position: relative; z-index: 2; }
  body .elementor-43 .kkc-card .elementor-widget-icon .elementor-icon { font-size: 66px; line-height: 0; }
  body .elementor-43 .kkc-card .elementor-widget-icon svg { width: 66px !important; height: 66px !important; border-radius: 50%; box-shadow: 0 0 0 4px #ffffff; }
  /* S-07 / S-08 / S-09 text, 79px from the card edge */
  body .elementor-43 .kkc-card .elementor-widget-heading { margin: 0 16px 0 78px !important; }
  body .elementor-43 :is(.elementor-element.elementor-element-b21629b, .elementor-element.elementor-element-665f377, .elementor-element.elementor-element-252f948, .elementor-element.elementor-element-f8975af, .elementor-element.elementor-element-bcefc1b) { margin-top: -10px !important; }
  body .elementor-43 :is(.elementor-element.elementor-element-b21629b, .elementor-element.elementor-element-665f377, .elementor-element.elementor-element-252f948, .elementor-element.elementor-element-f8975af, .elementor-element.elementor-element-bcefc1b) .elementor-heading-title { font-size: 32px !important; line-height: 1.15 !important; letter-spacing: 0.03em; }
  body .elementor-43 :is(.elementor-element.elementor-element-ed3bfa7, .elementor-element.elementor-element-21ba642, .elementor-element.elementor-element-07011f8, .elementor-element.elementor-element-ccba669, .elementor-element.elementor-element-ce02014) { margin-top: 12px !important; }
  body .elementor-43 :is(.elementor-element.elementor-element-ed3bfa7, .elementor-element.elementor-element-21ba642, .elementor-element.elementor-element-07011f8, .elementor-element.elementor-element-ccba669, .elementor-element.elementor-element-ce02014) .elementor-heading-title { font-size: 20px !important; line-height: 27px !important; }
  /* S-11 EXPLORE: Satoshi Black 17.7px, letter-spaced, followed by ">" */
  body .elementor-43 .kkc-card .elementor-widget-button { margin: auto 0 0 !important; padding: 34px 0 0 78px !important; }
  body .elementor-43 .kkc-card .elementor-widget-button .elementor-button {
    font-family: "Satoshi", "Helvetica Neue", Arial, sans-serif !important;
    font-size: 17.7px !important; font-weight: 900 !important; letter-spacing: 0.1em !important;
    padding: 0 !important; line-height: 1.2 !important;
  }
  body .elementor-43 .kkc-card .elementor-widget-button .elementor-button-content-wrapper { gap: 10px !important; }
  body .elementor-43 .kkc-card .elementor-widget-button .elementor-button-icon svg { display: none !important; }
  body .elementor-43 .kkc-card .elementor-widget-button .elementor-button-icon::before { content: ">"; font: inherit; letter-spacing: 0; }

  /* ================= ONE HOME (7149c5d) ================= */
  /* O-02 793px + O-14 61px white gap below */
  body .elementor-43 .elementor-element.elementor-element-7149c5d {
    --min-height: 793px; min-height: 793px !important;
    padding: 0 !important;
    margin-bottom: 61px !important;
    overflow: hidden;
  }
  body .elementor-43 .elementor-element.elementor-element-7149c5d > .e-con-inner { align-items: flex-start !important; padding: 0 !important; gap: 0 !important; flex-wrap: nowrap !important; }
  body .elementor-43 .elementor-element.elementor-element-743fae7 { --width: auto; width: 448px !important; flex: 0 0 448px; padding: 115px 0 0 !important; --gap: 0px; gap: 0 !important; }
  body .elementor-43 .elementor-element.elementor-element-884f790,
  body .elementor-43 .elementor-element.elementor-element-08f48e7 { margin: 0 !important; }
  body .elementor-43 .elementor-element.elementor-element-884f790 .elementor-heading-title { line-height: 40px !important; letter-spacing: 0.03em; }
  body .elementor-43 .elementor-element.elementor-element-08f48e7 { margin-top: 30px !important; }
  body .elementor-43 .elementor-element.elementor-element-08f48e7 .elementor-heading-title { line-height: 33px !important; }
  /* O-06 area buttons ~250 x 38, light grey border, Satoshi Medium 21px, 10px gap */
  body .elementor-43 :is(.elementor-element.elementor-element-a682427, .elementor-element.elementor-element-3a87742, .elementor-element.elementor-element-e648c86, .elementor-element.elementor-element-0f74986) { max-width: 250px !important; width: 250px !important; margin: 10px 0 0 !important; }
  body .elementor-43 .elementor-element.elementor-element-a682427 { margin-top: 34px !important; }
  body .elementor-43 :is(.elementor-element.elementor-element-a682427, .elementor-element.elementor-element-3a87742, .elementor-element.elementor-element-e648c86, .elementor-element.elementor-element-0f74986) .elementor-button {
    min-height: 38px !important; height: 38px;
    padding: 0 16px 0 21px !important;
    border-color: #adadad !important;
    font-family: "Satoshi", "Helvetica Neue", Arial, sans-serif !important;
    font-size: 21px !important; font-weight: 500 !important; letter-spacing: 0.04em !important;
  }
  /* O-07 house illustration from ~585px to ~1810px (at 1920), vertically centred */
  body .elementor-43 .elementor-element.elementor-element-38068a6 {
    width: auto !important; max-width: 1225px !important; flex: 1 1 0 !important;
    margin: 40px calc(110px - (100vw - var(--kk-w)) / 2) 0 0 !important;
  }

  /* ================= FEATURED PRODUCT (8e91e85) ================= */
  /* F-01 full page width, F-02 600px */
  body .elementor-43 .elementor-element.elementor-element-8e91e85 {
    --content-width: 100vw !important;
    --min-height: 600px; min-height: 600px !important;
    padding: 0 !important;
  }
  body .elementor-43 .elementor-element.elementor-element-8e91e85 > .e-con-inner { max-width: none !important; padding: 0 !important; gap: 0 !important; align-items: stretch !important; flex-wrap: nowrap !important; }
  /* F-03 left photo ~722px wide */
  body .elementor-43 .elementor-element.elementor-element-d7da6a9 { width: 37.6vw !important; max-width: none !important; flex: 0 0 37.6vw; margin: 0 !important; }
  body .elementor-43 .elementor-element.elementor-element-d7da6a9 img { width: 100% !important; height: max(600px, 31.25vw) !important; object-fit: cover; }
  body .elementor-43 .elementor-element.elementor-element-1676003 { --width: auto; width: auto !important; flex: 1 1 auto !important; padding: 0 0 0 2.66vw !important; align-items: stretch !important; gap: 0 !important; }
  body .elementor-43 .elementor-element.elementor-element-91b1f1f { --width: auto; width: 30.73vw !important; flex: 0 1 30.73vw; min-width: 420px; padding: 85px 0 0 !important; --gap: 0px; gap: 0 !important; }
  body .elementor-43 :is(.elementor-element.elementor-element-d688fd8, .elementor-element.elementor-element-36490c5, .elementor-element.elementor-element-0818b88, .elementor-element.elementor-element-51cf488, .elementor-element.elementor-element-041e5ca) { margin: 0 !important; }
  /* F-06 label */
  body .elementor-43 .elementor-element.elementor-element-d688fd8 .elementor-heading-title { font-size: 16px !important; letter-spacing: 0.12em !important; line-height: 1.2 !important; }
  /* F-07 / F-08 title */
  body .elementor-43 .elementor-element.elementor-element-36490c5 { margin-top: 20px !important; }
  body .elementor-43 .elementor-element.elementor-element-36490c5 .elementor-heading-title { line-height: 40px !important; letter-spacing: 0.03em; }
  /* F-09 tagline */
  body .elementor-43 .elementor-element.elementor-element-0818b88 { margin-top: 22px !important; }
  body .elementor-43 .elementor-element.elementor-element-0818b88 .elementor-heading-title {
    font-family: "Satoshi", "Helvetica Neue", Arial, sans-serif !important;
    font-size: 23px !important; font-weight: 700 !important; line-height: 1.3 !important;
  }
  /* F-11 bullets: plain "•", 22.2px */
  body .elementor-43 .elementor-element.elementor-element-51cf488 { margin-top: 30px !important; }
  body .elementor-43 .elementor-element.elementor-element-51cf488 .elementor-icon-list-icon { display: none !important; }
  body .elementor-43 .elementor-element.elementor-element-51cf488 .elementor-icon-list-item { padding: 0 !important; margin: 0 !important; }
  body .elementor-43 .elementor-element.elementor-element-51cf488 .elementor-icon-list-text { line-height: 33px !important; padding: 0 !important; }
  body .elementor-43 .elementor-element.elementor-element-51cf488 .elementor-icon-list-text::before { content: "\2022\00a0"; }
  /* F-13 button ~275 x 39, Satoshi Regular 16, letter-spaced */
  body .elementor-43 .elementor-element.elementor-element-041e5ca { margin-top: 44px !important; }
  body .elementor-43 .elementor-element.elementor-element-041e5ca .elementor-button {
    display: inline-flex; align-items: center; justify-content: center;
    width: 275px; height: 39px; padding: 0 !important; box-sizing: border-box;
    border-radius: 999px !important;
    font-family: "Satoshi", "Helvetica Neue", Arial, sans-serif !important;
    font-size: 16px !important; font-weight: 400 !important;
    letter-spacing: 0.12em !important; line-height: 1 !important;
  }
  /* F-15 right bucket image ~450 x 550 */
  body .elementor-43 .elementor-element.elementor-element-9b21423 { align-self: flex-start; width: 23.44vw !important; max-width: none !important; flex: 0 0 23.44vw; margin: 25px 6.77vw 0 auto !important; }
  body .elementor-43 .elementor-element.elementor-element-9b21423 img { width: 100% !important; height: 28.65vw !important; max-height: none !important; object-fit: contain; }

  /* ================= CONTACT FORM (1c24237) ================= */
  /* C-01 761px; C-08 / C-09 photo ~850 x 735, reaching ~1840px, from above the heading down to the footer */
  body .elementor-43 .elementor-element.elementor-element-1c24237 {
    position: relative;
    --min-height: 761px; min-height: 761px !important;
    padding: 0 !important;
    overflow: hidden;
  }
  body .elementor-43 .elementor-element.elementor-element-1c24237 > .e-con-inner { padding: 0 !important; align-items: flex-start !important; }
  body .elementor-43 .elementor-element.elementor-element-5448b30 { --width: auto; width: 860px !important; max-width: 52vw; flex: 0 1 860px; padding: 182px 0 0 !important; --gap: 0px; gap: 0 !important; position: relative; z-index: 1; }
  body .elementor-43 :is(.elementor-element.elementor-element-2799513, .elementor-element.elementor-element-9d92933, .elementor-element.elementor-element-22940b5) { margin: 0 !important; }
  body .elementor-43 .elementor-element.elementor-element-7fb41c0 {
    position: absolute !important;
    right: 4.17vw; bottom: 0;
    width: 44.27vw !important; max-width: none !important; height: 38.28vw;
    margin: 0 !important;
  }
  body .elementor-43 .elementor-element.elementor-element-7fb41c0 img { width: 100% !important; height: 100% !important; max-height: none !important; object-fit: contain; object-position: center bottom; }
  /* C-02 heading Satoshi Bold Italic 40px */
  body .elementor-43 .elementor-element.elementor-element-2799513 .elementor-heading-title {
    font-family: "Satoshi", "Helvetica Neue", Arial, sans-serif !important;
    font-size: 40px !important; font-weight: 700 !important; font-style: italic !important; line-height: 1.2 !important;
  }
  /* C-03 / C-04 intro ~20px, three lines */
  body .elementor-43 .elementor-element.elementor-element-9d92933 { margin-top: 18px !important; max-width: 660px; }
  body .elementor-43 .elementor-element.elementor-element-9d92933 .elementor-heading-title { font-size: 20px !important; line-height: 27px !important; }
  /* C-05 two columns ~410px; C-07 Aeonik Pro Regular 18px for fields and Submit */
  body .elementor-43 .elementor-element.elementor-element-22940b5 { margin-top: 50px !important; width: 843px !important; max-width: none !important; }
  body .elementor-43 .cf7-custom-form { grid-template-columns: 410px 410px !important; column-gap: 23px !important; row-gap: 12px; }
  body .cf7-custom-form input[type="text"],
  body .cf7-custom-form input[type="email"],
  body .cf7-custom-form input[type="tel"],
  body .cf7-custom-form textarea,
  body .cf7-custom-form input[type="submit"],
  body .cf7-custom-form .wpcf7-submit {
    font-family: "Aeonik Pro", "Satoshi", Arial, sans-serif !important;
    font-weight: 400 !important;
    font-size: 18px !important;
  }
  body .cf7-custom-form input[type="text"],
  body .cf7-custom-form input[type="email"],
  body .cf7-custom-form input[type="tel"],
  body .cf7-custom-form input[type="submit"],
  body .cf7-custom-form .wpcf7-submit { height: 45px !important; line-height: 45px !important; }
}



/* =====================================================================
   16. DESIGN v3 – PHASE 5 (Where to Buy, page 132)
   W-01, W-03..W-08, W-10..W-13, W-17, W-19, W-20, W-22..W-25, W-27, W-28
   ===================================================================== */

/* ---------- inner-page breadcrumb (W-03 colour, W-05 chevron, W-06 no underline) – all widths ---------- */
body .elementor-132 .elementor-element.elementor-element-b0e94d1 .hfe-breadcrumbs-text,
body .elementor-132 .elementor-element.elementor-element-b0e94d1 .hfe-breadcrumbs a { color: #75c4a8 !important; text-decoration: none !important; }
body .elementor-132 .elementor-element.elementor-element-b0e94d1 .hfe-breadcrumbs-separator-text { display: none !important; }
body .elementor-132 .elementor-element.elementor-element-b0e94d1 .hfe-breadcrumbs-separator::before {
  content: "";
  display: inline-block;
  width: 8px; height: 8px;
  border-top: 1.5px solid #75c4a8;
  border-right: 1.5px solid #75c4a8;
  transform: rotate(45deg);
  margin: 0 4px 2px 0;
}
body .elementor-132 .elementor-element.elementor-element-b0e94d1 .hfe-breadcrumbs { display: flex; align-items: center; gap: 14px; padding-left: 0 !important; margin-left: 0 !important; }
body .elementor-132 .elementor-element.elementor-element-b0e94d1 .hfe-breadcrumbs li { margin: 0 !important; padding: 0 !important; }
/* W-22 brand heading green, W-12 dropdown border pale yellow */
body .elementor-132 .elementor-element.elementor-element-846c896 .elementor-heading-title { color: #00623d !important; }
body .elementor-132 .sk-fs .sk-fs__select select { border-color: #fae3af !important; }

@media (min-width: 1025px) {
  /* W-01 banner 325px; W-08 breadcrumb and title on the same edge */
  body .elementor-132 .elementor-element.elementor-element-39346a4 {
    --min-height: 325px; min-height: 325px !important;
    padding-top: 94px !important; padding-bottom: 0 !important;
    justify-content: flex-start !important;
  }
  body .elementor-132 .elementor-element.elementor-element-39346a4 > .e-con-inner { justify-content: flex-start !important; padding: 0 !important; gap: 0 !important; }
  body .elementor-132 .elementor-element.elementor-element-b0e94d1,
  body .elementor-132 .elementor-element.elementor-element-4cd68f2 { margin: 0 !important; padding: 0 !important; }
  /* W-04 breadcrumb Satoshi Regular 20.5px with wide gaps */
  body .elementor-132 .elementor-element.elementor-element-b0e94d1 .hfe-breadcrumbs { gap: 20px; }
  body .elementor-132 .elementor-element.elementor-element-b0e94d1 .hfe-breadcrumbs-text {
    font-family: "Satoshi", "Helvetica Neue", Arial, sans-serif !important;
    font-size: 20.5px !important; font-weight: 400 !important; line-height: 25px !important; text-transform: uppercase;
  }
  /* W-07 title Satoshi Bold 95px, letter-spaced */
  body .elementor-132 .elementor-element.elementor-element-4cd68f2 { margin-top: 26px !important; }
  body .elementor-132 .elementor-element.elementor-element-4cd68f2 .elementor-heading-title {
    font-family: "Satoshi", "Helvetica Neue", Arial, sans-serif !important;
    font-size: min(95px, 4.95vw) !important; font-weight: 700 !important; letter-spacing: 0.04em !important; line-height: 1.1 !important;
  }

  /* W-10 Find Stores Near You: Satoshi Black 33.7px */
  body .elementor-132 .elementor-element.elementor-element-609ea42 { padding-top: 108px !important; padding-bottom: 142px !important; }
  body .elementor-132 .elementor-element.elementor-element-609ea42 > .e-con-inner { padding: 0 !important; }
  body .elementor-132 .elementor-element.elementor-element-506a975 { margin: 0 !important; }
  body .elementor-132 .elementor-element.elementor-element-506a975 .elementor-heading-title { font-weight: 900 !important; }
  /* W-11 45px from the heading to the dropdowns */
  body .elementor-132 .elementor-element.elementor-element-4cc55bc { margin-top: 45px !important; }
  body .elementor-132 .sk-fs { padding-top: 0 !important; padding-bottom: 0 !important; }
  /* W-13 dropdowns and Apply: Aeonik Pro Regular 18px */
  body .elementor-132 .sk-fs .sk-fs__select select,
  body .elementor-132 .sk-fs button.sk-fs__apply {
    font-family: "Aeonik Pro", "Satoshi", Arial, sans-serif !important;
    font-weight: 400 !important;
    font-size: 18px !important;
  }
  body .elementor-132 .sk-fs .sk-fs__select { width: 264px; }
  /* W-17 map ~1507 x 830 */
  body .elementor-132 .sk-fs .sk-fs__map { max-width: 1507px !important; margin-top: 65px !important; }
  body .elementor-132 .sk-fs .sk-fs__map iframe { height: 830px !important; }

  /* W-19 / W-20 logo strip: 270px tall, six large logos across the full width (grey background stays) */
  body .elementor-132 .elementor-element.elementor-element-e3418af {
    --content-width: calc(100vw - 120px) !important;
    --min-height: 270px; min-height: 270px !important;
    padding-top: 0 !important; padding-bottom: 0 !important;
    justify-content: center;
  }
  body .elementor-132 .elementor-element.elementor-element-e3418af > .e-con-inner { justify-content: center; padding: 0 !important; }
  body .elementor-132 .elementor-element.elementor-element-648e3e1 { width: 100%; }
  body .elementor-132 .elementor-element.elementor-element-648e3e1 .swiper-slide { display: flex; justify-content: center; align-items: center; }
  body .elementor-132 .elementor-element.elementor-element-648e3e1 .swiper-slide img,
  body .elementor-132 .elementor-element.elementor-element-648e3e1 .swiper-slide-image { width: 100% !important; max-width: 250px !important; height: auto !important; }

  /* W-28 116px from the logo strip to the label; brand block centred */
  body .elementor-132 .elementor-element.elementor-element-9039a56 { padding-top: 116px !important; padding-bottom: 220px !important; }
  body .elementor-132 .elementor-element.elementor-element-9039a56 > .e-con-inner { padding: 0 !important; gap: 0 !important; align-items: center !important; }
  body .elementor-132 :is(.elementor-element.elementor-element-142ce73, .elementor-element.elementor-element-846c896, .elementor-element.elementor-element-e1a9a94) { margin: 0 !important; }
  body .elementor-132 .elementor-element.elementor-element-142ce73 .elementor-heading-title { font-size: 16px !important; letter-spacing: 0.12em !important; line-height: 1.2 !important; }
  /* W-22..W-24 brand heading, ~40px line height, line 1 ends "brand" */
  body .elementor-132 .elementor-element.elementor-element-846c896 { margin-top: 24px !important; width: auto !important; max-width: 1300px !important; }
  body .elementor-132 .elementor-element.elementor-element-846c896 .elementor-heading-title { line-height: 40px !important; text-align: center; }
  /* W-25 / W-27 paragraph 22.2px, three-line second paragraph */
  body .elementor-132 .elementor-element.elementor-element-e1a9a94 { margin-top: 44px !important; width: 100% !important; max-width: 1507px !important; }
  body .elementor-132 .elementor-element.elementor-element-e1a9a94 .elementor-heading-title { line-height: 33px !important; text-align: center; }
}


/* =====================================================================
   17. DESIGN v3 – PHASE 6 (Catalogue + category pages, .kk-hero / .kk-cat)
   K-03, K-05..K-16, K-18..K-23 (desktop >= 1025px; the phone filter drawer is untouched)
   ===================================================================== */
/* K-05 breadcrumb colour (all widths) */
body .kk-hero .kk-hero__crumbs,
body .kk-hero .kk-hero__crumbs a { color: #75c4a8 !important; text-decoration: none !important; }

@media (min-width: 1025px) {
  /* K-03 banner 325px; K-07 breadcrumb and title on the page edge (135px at 1920) */
  body .kk-hero {
    box-sizing: border-box;
    min-height: 325px;
    padding: 94px 0 0 !important;
  }
  body .kk-hero .kk-hero__inner { padding-left: 0 !important; }
  /* K-05 breadcrumb Satoshi Regular 20.5px with a thin chevron */
  body .kk-hero .kk-hero__crumbs {
    display: flex; align-items: center; gap: 20px;
    margin: 0 !important;
    font-family: "Satoshi", "Helvetica Neue", Arial, sans-serif !important;
    font-size: 20.5px !important; font-weight: 400 !important; line-height: 25px !important; letter-spacing: 0 !important;
    text-transform: uppercase;
  }
  body .kk-hero .kk-hero__sep { font-size: 0 !important; display: inline-block; }
  body .kk-hero .kk-hero__sep::before {
    content: ""; display: inline-block;
    width: 8px; height: 8px;
    border-top: 1.5px solid #75c4a8; border-right: 1.5px solid #75c4a8;
    transform: rotate(45deg); margin: 0 4px 2px 0;
  }
  /* K-06 title Satoshi Bold 95px */
  body .kk-hero .kk-hero__title {
    margin: 26px 0 0 !important;
    font-family: "Satoshi", "Helvetica Neue", Arial, sans-serif !important;
    font-size: min(95px, 4.95vw) !important; font-weight: 700 !important; line-height: 1.1 !important; letter-spacing: 0.04em !important;
  }

  /* K-08 sidebar 604px (31%), K-09 text from 161px; mint panel on the sidebar itself */
  body .kk-cat { display: flex !important; padding: 0 !important; align-items: stretch; }
  body .kk-cat .kk-cat__side {
    flex: 0 0 31.46vw !important;
    width: 31.46vw !important; max-width: none !important;
    box-sizing: border-box;
    padding: 90px 24px 120px 8.44vw !important;
    background: #e4f3ec !important;
  }
  body .kk-cat .kk-cat__side::before { display: none !important; }
  /* K-13 no dividers between groups */
  body .kk-cat .kk-cat__group { border: 0 !important; margin: 0 0 76px !important; padding: 0 !important; }
  body .kk-cat .kk-cat__group:last-child { margin-bottom: 0 !important; }
  /* K-14 group labels Satoshi Regular 16px, letter-spaced, orange */
  body .kk-cat .kk-cat__label {
    margin: 0 0 18px !important;
    font-family: "Satoshi", "Helvetica Neue", Arial, sans-serif !important;
    font-size: 16px !important; font-weight: 400 !important; letter-spacing: 0.12em !important; line-height: 1.2 !important;
    color: #f05a38 !important; text-transform: uppercase;
  }
  /* K-15 filter links Satoshi Black 33.7px, #00623d, 46px pitch */
  body .kk-cat .kk-cat__list { margin: 0 !important; padding: 0 !important; list-style: none; }
  body .kk-cat .kk-cat__list li { display: block; height: 46px; margin: 0 !important; padding: 0 !important; line-height: 40px; }
  body .kk-cat .kk-cat__link {
    display: inline-block; vertical-align: top;
    padding: 0 !important;
    font-family: "Satoshi", "Helvetica Neue", Arial, sans-serif !important;
    font-size: min(33.7px, 1.76vw) !important; font-weight: 900 !important; line-height: 1.19 !important;
    color: #00623d !important; text-decoration: none !important;
    border: 0 !important;
  }
  /* K-16 selected filter: yellow underline */
  body .kk-cat .kk-cat__link.is-active { box-shadow: inset 0 -2px 0 #f0b232; color: #00623d !important; }

  /* products area: starts ~107px right of the sidebar, ~87px under the banner */
  body .kk-cat .kk-cat__main { flex: 1 1 auto !important; width: auto !important; padding: 87px 0 120px 5.57vw !important; box-sizing: border-box; }
  body .kk-cat .kk-cat__main .site-main { margin: 0 !important; padding: 0 !important; max-width: none !important; width: auto !important; }
  /* K-18 / K-19 cards ~488 x 570, 95px between the columns */
  body .kk-cat ul.products {
    display: grid !important;
    grid-template-columns: repeat(2, 25.42vw) !important;
    column-gap: 4.95vw !important; row-gap: 85px !important;
    justify-content: start !important;
    margin: 0 !important; width: auto !important;
  }
  body .kk-cat ul.products::before, body .kk-cat ul.products::after { display: none !important; }
  body .kk-cat ul.products li.product {
    width: auto !important; float: none !important; margin: 0 !important;
    min-height: 570px;
    padding: 0 41px 40px !important;
    box-sizing: border-box;
    overflow: hidden;
  }
  /* K-20 photo fills the card width, broom cropped large */
  body .kk-cat ul.products li.product img {
    display: block;
    width: calc(100% + 82px) !important; max-width: none !important;
    height: 358px !important;
    margin: 0 -41px 22px !important;
    object-fit: cover; object-position: center 30%;
  }
  /* K-21 item code, K-22 name (two lines), K-23 DISCOVER */
  body .kk-cat .kk-loop-code { font-size: 17px !important; font-weight: 700 !important; color: #00915d !important; letter-spacing: 0.01em !important; line-height: 1.3 !important; margin-bottom: 6px !important; }
  body .kk-cat ul.products li.product .woocommerce-loop-product__title {
    font-family: "Satoshi", "Helvetica Neue", Arial, sans-serif !important;
    font-size: 23px !important; font-weight: 700 !important; line-height: 30px !important;
    padding: 0 !important; margin: 0 !important;
  }
  body .kk-cat .kk-loop-more { display: inline-block; margin-top: 34px !important; font-size: 17px !important; font-weight: 700 !important; letter-spacing: 0.02em !important; }
}


/* =====================================================================
   18. DESIGN v3 – PHASE 7 (product page, .kk-sp) – desktop (>= 1025px)
   P-03..P-18 (P-02 wood photo needs the asset)
   ===================================================================== */
@media (min-width: 1025px) {
  /* P-03 left floor photo 604px (31.46vw) */
  body .kk-sp::before { width: 31.46vw !important; }
  /* P-04 product image ~826px wide, starting ~172px from the left */
  body .kk-sp .kk-sp__wood img.kk-sp__img {
    width: 43vw !important;
    margin-left: calc(8.96vw - (100vw - var(--kk-w)) / 2) !important;
  }
  /* text column from ~1060px (at 1920), 61px under the header (P-05) */
  body .kk-sp .kk-sp__panel { padding-top: 61px !important; padding-left: 35.9% !important; padding-right: 0 !important; }
  body .kk-sp .kk-sp__content { width: 37.76vw !important; max-width: 725px !important; }
  /* P-06 item code */
  body .kk-sp .kk-itemcode { font-size: 17px !important; font-weight: 700 !important; margin: 0 0 4px !important; letter-spacing: 0.01em; }
  /* P-07 title */
  body .kk-sp h1.kk-sp__title { font-family: "Satoshi", "Helvetica Neue", Arial, sans-serif !important; font-size: 23px !important; font-weight: 700 !important; line-height: 1.3 !important; margin: 0 0 32px !important; }
  /* P-08 bullets 19px, 30px pitch */
  body .kk-sp .kk-features { margin: 0 0 68px !important; padding-left: 18px !important; }
  body .kk-sp .kk-features li { font-size: 19px !important; line-height: 30px !important; color: #111111; }
  /* P-09 label */
  body .kk-sp .kk-pinfo { position: relative; }
  body .kk-sp .kk-pinfo__title { font-size: 17px !important; font-weight: 700 !important; margin: 0 0 16px !important; }
  /* P-10..P-14 table: 33px pills, 41.6px pitch, equal widths, white values */
  body .kk-sp .kk-pinfo__row { display: flex !important; gap: 7px !important; margin: 0 0 8.6px !important; flex-wrap: nowrap !important; }
  body .kk-sp .kk-pinfo__label,
  body .kk-sp .kk-pinfo__value {
    box-sizing: border-box;
    height: 33px !important; line-height: 33px !important;
    font-size: 16.5px !important;
    border-radius: 999px;
    white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
  }
  body .kk-sp .kk-pinfo__label { flex: 0 0 149px !important; width: 149px !important; padding: 0 12px !important; font-weight: 500 !important; }
  body .kk-sp .kk-pinfo__value { flex: 0 0 222px !important; width: 222px !important; padding: 0 16px !important; font-weight: 400 !important; background: #ffffff !important; }
  /* P-15 scroll-down box: white square, ~96px, down chevron, level with the table, on the right */
  body .kk-sp .kp-sp-down {
    position: absolute; right: 0; top: 41px;
    display: flex; align-items: center; justify-content: center;
    width: 96px; height: 96px;
    padding: 0; border: 0; border-radius: 0;
    background: #ffffff;
    cursor: pointer;
    box-shadow: none;
    transition: transform 0.3s ease;
  }
  body .kk-sp .kp-sp-down::before {
    content: ""; width: 44px; height: 44px; margin-top: -24px;
    border-right: 1.5px solid #1a1a1a; border-bottom: 1.5px solid #1a1a1a;
    transform: rotate(45deg);
  }
  body .kk-sp .kp-sp-down:hover { transform: translateY(3px); }
  body .kk-sp .kp-sp-down:focus-visible { outline: 3px solid #f0b232; outline-offset: 3px; }
  /* P-16..P-18 feature images: text-column width (~725px), left-aligned with the text, soft shadow */
  body .kk-sp .kk-highlights { margin-top: 52px !important; margin-left: 0 !important; width: 100% !important; }
  body .kk-sp .kk-highlight img { width: 100% !important; box-shadow: 10px 12px 22px rgba(0, 0, 0, 0.16); }
}
@media (max-width: 1024px) { body .kk-sp .kp-sp-down { display: none; } }



/* =====================================================================
   19. DESIGN v3 – PHASE 8 (Who We Are, page 153) + banner photos
   A-01..A-14, A-16..A-18, A-20..A-22, A-24..A-28; K-02 catalogue banner photo
   ===================================================================== */

/* K-02 catalogue banner: green floor wiper on tiles */
:root { --kk-hero-img: url("https://soharon.co.uk/off-29/wp-content/uploads/2026/10/WhatsApp-Image-2026-09-03-at-11.32.14-4.jpeg"); }
body .kk-hero { background-position: center 62% !important; }
/* A-01 Who We Are banner: white string mop */
body .elementor-153 .elementor-element.elementor-element-cc1a00a { background-image: url("https://soharon.co.uk/off-29/wp-content/uploads/2026/10/WhatsApp-Image-2026-09-03-at-11.32.14-2.jpeg") !important; background-size: cover !important; background-position: center 72% !important; }

/* A-04 breadcrumb (same as Where to Buy): mint, chevron, no underline – all widths */
body .elementor-153 .elementor-element.elementor-element-90a5b6c .hfe-breadcrumbs-text,
body .elementor-153 .elementor-element.elementor-element-90a5b6c .hfe-breadcrumbs a { color: #75c4a8 !important; text-decoration: none !important; }
body .elementor-153 .elementor-element.elementor-element-90a5b6c .hfe-breadcrumbs-separator-text { display: none !important; }
body .elementor-153 .elementor-element.elementor-element-90a5b6c .hfe-breadcrumbs-separator::before {
  content: ""; display: inline-block; width: 8px; height: 8px;
  border-top: 1.5px solid #75c4a8; border-right: 1.5px solid #75c4a8;
  transform: rotate(45deg); margin: 0 4px 2px 0;
}
body .elementor-153 .elementor-element.elementor-element-90a5b6c .hfe-breadcrumbs { display: flex; align-items: center; gap: 14px; padding-left: 0 !important; margin-left: 0 !important; }
body .elementor-153 .elementor-element.elementor-element-90a5b6c .hfe-breadcrumbs li { margin: 0 !important; padding: 0 !important; }

/* A-06 / A-14 bright brand green */
body .elementor-153 :is(.elementor-element.elementor-element-5d0b5e3, .elementor-element.elementor-element-89c5073) .elementor-heading-title { color: #00915d !important; }
body .elementor-153 :is(.elementor-element.elementor-element-12b049b, .elementor-element.elementor-element-87a16f6) { --divider-color: #00915d !important; }
body .elementor-153 :is(.elementor-element.elementor-element-12b049b, .elementor-element.elementor-element-87a16f6) .elementor-divider-separator { border-color: #00915d !important; }
/* A-27 Guaranteed… grey */
body .elementor-153 .elementor-element.elementor-element-092cb7a .elementor-heading-title { color: #4f4f4f !important; }

/* A-07 clean down arrow (thin line + arrowhead) replaces the screenshot image */
body .elementor-153 .elementor-element.elementor-element-71918f6 img { display: none !important; }
body .elementor-153 .elementor-element.elementor-element-71918f6 .elementor-widget-container,
body .elementor-153 .elementor-element.elementor-element-71918f6 {
  min-height: 60px;
  background: url("data:image/svg+xml,%3Csvg%20xmlns%3D%27http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%27%20viewBox%3D%270%200%2014%2096%27%20fill%3D%27none%27%20stroke%3D%27%23111111%27%20stroke-width%3D%271.4%27%3E%3Cpath%20d%3D%27M7%200v94%27%2F%3E%3Cpath%20d%3D%27M2%2088l5%207%205-7%27%20fill%3D%27%23111111%27%20stroke%3D%27none%27%2F%3E%3C%2Fsvg%3E") left top / auto 100% no-repeat;
}
/* A-18 clean four-point sparkle stars replace the screenshot images */
body .elementor-153 :is(.elementor-element.elementor-element-1b9f740, .elementor-element.elementor-element-3686e69, .elementor-element.elementor-element-f5a2f5e, .elementor-element.elementor-element-e6c8adb) img { display: none !important; }
body .elementor-153 :is(.elementor-element.elementor-element-1b9f740, .elementor-element.elementor-element-3686e69, .elementor-element.elementor-element-f5a2f5e, .elementor-element.elementor-element-e6c8adb) {
  min-height: 27px;
  width: 100% !important;
  background: url("data:image/svg+xml,%3Csvg%20xmlns%3D%27http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%27%20viewBox%3D%270%200%2024%2024%27%3E%3Cpath%20fill%3D%27%23ffffff%27%20d%3D%27M12%200c.7%206.6%205.4%2011.3%2012%2012-6.6.7-11.3%205.4-12%2012-.7-6.6-5.4-11.3-12-12C6.6%2011.3%2011.3%206.6%2012%200z%27%2F%3E%3C%2Fsvg%3E") center / 27px 27px no-repeat !important;
}

@media (min-width: 1025px) {
  /* A-02 banner 325px; A-04 breadcrumb 20.5px + title Bold 95px on the same edge */
  body .elementor-153 .elementor-element.elementor-element-cc1a00a { --min-height: 325px; min-height: 325px !important; padding-top: 94px !important; padding-bottom: 0 !important; justify-content: flex-start !important; }
  body .elementor-153 .elementor-element.elementor-element-cc1a00a > .e-con-inner { justify-content: flex-start !important; padding: 0 !important; gap: 0 !important; }
  body .elementor-153 .elementor-element.elementor-element-90a5b6c, body .elementor-153 .elementor-element.elementor-element-901a2db { margin: 0 !important; padding: 0 !important; }
  body .elementor-153 .elementor-element.elementor-element-90a5b6c .hfe-breadcrumbs { gap: 20px; }
  body .elementor-153 .elementor-element.elementor-element-90a5b6c .hfe-breadcrumbs-text { font-family: "Satoshi", "Helvetica Neue", Arial, sans-serif !important; font-size: 20.5px !important; font-weight: 400 !important; line-height: 25px !important; text-transform: uppercase; }
  body .elementor-153 .elementor-element.elementor-element-901a2db { margin-top: 26px !important; }
  body .elementor-153 .elementor-element.elementor-element-901a2db .elementor-heading-title { font-family: "Satoshi", "Helvetica Neue", Arial, sans-serif !important; font-size: min(95px, 4.95vw) !important; font-weight: 700 !important; letter-spacing: 0.04em !important; line-height: 1.1 !important; }

  /* intro: text column 137 → 1275, exploded broom ~600 x 800 from 1275 to ~1875 (A-10) */
  body .elementor-153 .elementor-element.elementor-element-79a8b7a { padding: 0 0 70px !important; }
  body .elementor-153 .elementor-element.elementor-element-79a8b7a > .e-con-inner { padding: 0 !important; gap: 0 !important; align-items: flex-start !important; flex-wrap: nowrap !important; }
  body .elementor-153 .elementor-element.elementor-element-1a21684 { --width: auto; width: auto !important; flex: 1 1 auto !important; padding: 122px 0 0 !important; --gap: 0px; gap: 0 !important; }
  body .elementor-153 :is(.elementor-element.elementor-element-5d0b5e3, .elementor-element.elementor-element-b86e4b4, .elementor-element.elementor-element-71918f6, .elementor-element.elementor-element-89c5073, .elementor-element.elementor-element-162ec16, .elementor-element.elementor-element-7ed0220) { margin: 0 !important; padding: 0 !important; }
  /* A-05 92.5px, A-08 62.5px Satoshi Black */
  body .elementor-153 :is(.elementor-element.elementor-element-5d0b5e3, .elementor-element.elementor-element-b86e4b4) .elementor-heading-title { font-size: min(92.5px, 4.82vw) !important; font-weight: 900 !important; line-height: 1.12 !important; }
  body .elementor-153 .elementor-element.elementor-element-71918f6 { margin-top: 40px !important; height: 95px; min-height: 95px; width: 14px !important; }
  body .elementor-153 .elementor-element.elementor-element-89c5073 { margin-top: 64px !important; }
  body .elementor-153 :is(.elementor-element.elementor-element-89c5073, .elementor-element.elementor-element-162ec16) .elementor-heading-title { font-size: min(62.5px, 3.26vw) !important; font-weight: 900 !important; line-height: 1.12 !important; }
  /* A-09 intro paragraph 22.2px */
  body .elementor-153 .elementor-element.elementor-element-7ed0220 { margin-top: 36px !important; max-width: 860px; }
  body .elementor-153 .elementor-element.elementor-element-7ed0220 .elementor-heading-title { font-size: 22.2px !important; line-height: 33px !important; }
  body .elementor-153 .elementor-element.elementor-element-4c01e73 {
    width: 31.25vw !important; max-width: none !important; flex: 0 0 31.25vw;
    margin: 0 calc(45px - (100vw - var(--kk-w)) / 2) 0 0 !important;
  }
  body .elementor-153 .elementor-element.elementor-element-4c01e73 img { width: 31.25vw !important; height: 41.67vw !important; max-height: none !important; object-fit: contain; object-position: center top; }

  /* A-11..A-13 four feature photos 375 x 307 (row 171 → 1727), Satoshi Black 32px titles, 99 x 5 underlines */
  body .elementor-153 .elementor-element.elementor-element-bbc3710 { padding: 30px 0 76px !important; }
  body .elementor-153 .elementor-element.elementor-element-bbc3710 > .e-con-inner { justify-content: center !important; gap: 0.99vw !important; padding: 0 !important; flex-wrap: nowrap !important; }
  body .elementor-153 .elementor-element.elementor-element-bbc3710 > .e-con,
  body .elementor-153 .elementor-element.elementor-element-bbc3710 > .e-con-inner > .e-con { --width: auto; width: 19.53vw !important; flex: 0 0 19.53vw !important; padding: 0 !important; --gap: 0px; gap: 0 !important; }
  body .elementor-153 .elementor-element.elementor-element-bbc3710 .elementor-widget-image { margin: 0 !important; }
  body .elementor-153 .elementor-element.elementor-element-bbc3710 .elementor-widget-image img { width: 100% !important; height: 15.99vw !important; object-fit: cover; border-radius: 14px; }
  body .elementor-153 .elementor-element.elementor-element-bbc3710 .elementor-widget-heading { margin: 30px 0 0 !important; }
  body .elementor-153 .elementor-element.elementor-element-bbc3710 .elementor-widget-heading .elementor-heading-title { font-size: 32px !important; font-weight: 900 !important; line-height: 1.2 !important; }
  body .elementor-153 .elementor-element.elementor-element-bbc3710 .elementor-widget-divider { margin: 18px 0 0 !important; --divider-width: 99px; --divider-border-width: 5px; }
  body .elementor-153 .elementor-element.elementor-element-bbc3710 .elementor-divider-separator { width: 99px !important; border-top-width: 5px !important; margin: 0 auto !important; }

  /* A-16 / A-17 dark statement 515px, Satoshi Bold 40px, four lines */
  body .elementor-153 .elementor-element.elementor-element-1753931 { --min-height: 515px; min-height: 515px !important; padding: 64px 0 !important; justify-content: center !important; }
  body .elementor-153 .elementor-element.elementor-element-1753931 > .e-con-inner { padding: 0 !important; gap: 40px !important; justify-content: center !important; }
  body .elementor-153 :is(.elementor-element.elementor-element-1b9f740, .elementor-element.elementor-element-3686e69, .elementor-element.elementor-element-f17afcf, .elementor-element.elementor-element-f5a2f5e, .elementor-element.elementor-element-e5f7c30, .elementor-element.elementor-element-e6c8adb) { margin: 0 auto !important; }
  body .elementor-153 .elementor-element.elementor-element-f17afcf { width: 1250px !important; max-width: 100% !important; }
  body .elementor-153 .elementor-element.elementor-element-f17afcf .elementor-heading-title { font-size: min(40px, 2.08vw) !important; font-weight: 700 !important; line-height: 1.28 !important; }

  /* A-20..A-22 "No…" section 681px, large line Satoshi Bold 154px, text Bold 40px (line 1 ends "affordable doesn't") */
  body .elementor-153 .elementor-element.elementor-element-5419111 { --min-height: 681px; min-height: 681px !important; padding: 64px 0 !important; justify-content: center !important; }
  body .elementor-153 .elementor-element.elementor-element-5419111 > .e-con-inner { padding: 0 !important; justify-content: center !important; gap: 0 !important; }
  body .elementor-153 .elementor-element.elementor-element-fb87731 { margin: 0 !important; }
  body .elementor-153 .elementor-element.elementor-element-fb87731 .elementor-heading-title { font-size: min(154px, 8.02vw) !important; font-weight: 700 !important; line-height: 1.15 !important; }
  body .elementor-153 .elementor-element.elementor-element-f6db65f { width: 1410px !important; max-width: 100% !important; margin: 52px auto 0 !important; }
  body .elementor-153 .elementor-element.elementor-element-f6db65f .elementor-heading-title { font-size: min(40px, 2.08vw) !important; font-weight: 700 !important; line-height: 1.3 !important; }

  /* A-24 / A-25 "Born in the UAE" 587px, Satoshi Black 53px */
  body .elementor-153 .elementor-element.elementor-element-915d10f { --min-height: 587px; min-height: 587px !important; padding: 64px 0 !important; justify-content: center !important; }
  body .elementor-153 .elementor-element.elementor-element-915d10f > .e-con-inner { padding: 0 !important; gap: 40px !important; justify-content: center !important; }
  body .elementor-153 .elementor-element.elementor-element-e5f7c30 .elementor-heading-title { font-size: min(53px, 2.76vw) !important; font-weight: 900 !important; line-height: 1.43 !important; }

  /* A-26 / A-28 "Guaranteed…" 223px, section 414px */
  body .elementor-153 .elementor-element.elementor-element-bcabc46 { --min-height: 414px; min-height: 414px !important; padding: 40px 0 !important; justify-content: center !important; }
  body .elementor-153 .elementor-element.elementor-element-bcabc46 > .e-con-inner { padding: 0 !important; justify-content: center !important; }
  body .elementor-153 .elementor-element.elementor-element-092cb7a { margin: 0 !important; }
  body .elementor-153 .elementor-element.elementor-element-092cb7a .elementor-heading-title { font-size: min(223px, 11.6vw) !important; line-height: 1.1 !important; }
}

/* W-21 label colour (text set in kk-polish.js) */
body .elementor-132 .elementor-element.elementor-element-142ce73 .elementor-heading-title { color: #f05a38 !important; letter-spacing: 0.12em !important; }
</style>
    <?php
}

add_action( 'wp_footer', 'kk_polish_js', 100 );
function kk_polish_js() {
    if ( is_admin() || isset( $_GET['elementor-preview'] ) ) {
        return;
    }
    ?>
<script id="kk-polish-js">
(function () {
    var html = document.documentElement;
    var mqMobile = window.matchMedia('(max-width: 767px)');

    /* ---------- 1. mobile header: add a solid background once it sticks ---------- */
    var head = document.getElementById('masthead');
    if (head) {
        var ticking = false;
        var update = function () {
            ticking = false;
            head.classList.toggle('kp-stuck', mqMobile.matches && window.pageYOffset > 24);
        };
        window.addEventListener('scroll', function () {
            if (!ticking) { ticking = true; requestAnimationFrame(update); }
        }, { passive: true });
        if (mqMobile.addEventListener) { mqMobile.addEventListener('change', update); }
        update();
    }

    /* ---------- 1a. text fixes (design v3 Phase 0) ----------
       Corrects the exact typos only; once fixed in Elementor these do nothing. */
    (function () {
        var fixes = [
            [/\bCatelouge\b/g, 'Catalogue'],             // X-18
            [/\bCatelog\b/g, 'Catalogue'],               // X-17, N-01
            [/\bLets Connect\b/g, 'Let’s Connect'], // X-19
            [/\bWe oer\b/g, 'We offer'],                 // W-26
            [/\bEortless\b/g, 'Effortless']              // F-10
        ];
        var scope = document.querySelectorAll('.elementor-30, .elementor-43, .elementor-132, .kk-side, .kk-hero');
        scope.forEach(function (root) {
            var w = document.createTreeWalker(root, NodeFilter.SHOW_TEXT, null), n;
            while ((n = w.nextNode())) {
                var v = n.nodeValue, o = v;
                fixes.forEach(function (f) { v = v.replace(f[0], f[1]); });
                if (v !== o) { n.nodeValue = v; }
            }
        });
        // W-21: label above the Where to Buy brand heading
        var lbl = document.querySelector('.elementor-132 .elementor-element-142ce73 .elementor-heading-title');
        if (lbl && /^\s*FEATURED PRODUCT\s*$/i.test(lbl.textContent)) { lbl.textContent = 'WHO WE ARE'; lbl.classList.add('kp-w21'); }
    })();

    /* ---------- 1b. footer links (X-21, X-25) ----------
       "Contact" and "Let's Connect" go to the contact form on the home page.
       Only fills a link that is missing (no link / "#"), so links set in Elementor win. */
    (function () {
        var footer = document.querySelector('.elementor-30');
        if (!footer) { return; }
        var homeLink = footer.querySelector('.elementor-element-501b3e5 a');
        var home = homeLink ? homeLink.href.split('#')[0] : '/';
        var target = home + '#contact';

        // give the home page contact section an anchor
        var form = document.querySelector('.elementor-43 .elementor-element-1c24237');
        if (form && !document.getElementById('contact')) { form.id = 'contact'; }

        footer.querySelectorAll('.elementor-element-501b3e5 .elementor-icon-list-item').forEach(function (li) {
            if (li.querySelector('a')) { return; }
            if (!/^\s*contact\s*$/i.test(li.textContent)) { return; }
            var a = document.createElement('a');
            a.href = target;
            while (li.firstChild) { a.appendChild(li.firstChild); }
            li.appendChild(a);
        });
        var lc = footer.querySelector('.elementor-element-cec317d a.elementor-button');
        if (lc) {
            var h = lc.getAttribute('href');
            if (!h || h === '#') { lc.setAttribute('href', target); }
        }
    })();

    // forced line breaks from the design (shown on desktop only, .kp-br)
    function kpAddBreak(el, phrase) {
        if (!el || el.__kpBr && el.__kpBr[phrase]) { return; }
        var walker = document.createTreeWalker(el, NodeFilter.SHOW_TEXT, null), nodes = [], full = '', n;
        while ((n = walker.nextNode())) { nodes.push({ node: n, start: full.length }); full += n.nodeValue; }
        var re = new RegExp(phrase.trim().split(/\s+/).map(function (w) { return w.replace(/[.*+?^${}()|[\]\\]/g, '\\$&'); }).join('\\s+'));
        var m = re.exec(full);
        if (!m) { return; }
        var hit = null;
        for (var i = nodes.length - 1; i >= 0; i--) { if (nodes[i].start <= m.index) { hit = nodes[i]; break; } }
        if (!hit) { return; }
        var br = document.createElement('br');
        br.className = 'kp-br';
        var word = hit.node.parentElement && hit.node.parentElement.closest('.kk-w');
        if (word && el.contains(word)) {
            word.parentNode.insertBefore(br, word);
        } else {
            var after = hit.node.splitText(m.index - hit.start);
            after.parentNode.insertBefore(br, after);
        }
        (el.__kpBr = el.__kpBr || {})[phrase] = true;
    }

    /* ---------- 1c. home page (design v3 Phase 4) ---------- */
    (function () {
        var page = document.querySelector('.elementor-43');
        if (!page) { return; }

        var T = function (id, inner) { return page.querySelector('.elementor-element-' + id + ' ' + (inner || '.elementor-heading-title')); };
        [
            ['9a43ca3', null, ['TO KLEEN']],                                    // G-04
            ['1bde407', null, ['design, practical', 'make everyday']],          // G-05
            ['63b71db', '.elementor-icon-box-description', ['cleaning easier']],// G-10
            ['704d045', '.elementor-icon-box-description', ['Built to last']],  // G-10
            ['ed3bfa7', null, ['dust and dirt']],                               // S-10 (design)
            ['21ba642', null, ['fresh and spotless']],                          // S-10
            ['07011f8', null, ['clean and shine']],                             // S-10 (design)
            ['ccba669', null, ['with ease']],                                   // S-10
            ['ce02014', null, ['keep it dust']],                                // S-10
            ['884f790', null, ['MANY CLEANING', 'CHALLENGES']],                 // O-04
            ['36490c5', null, ['EASY-WRING', 'MOP BUCKET']],                    // F-08
            ['9d92933', null, ['get in touch']]                                 // C-04
        ].forEach(function (r) {
            var el = T(r[0], r[1]);
            r[2].forEach(function (ph) { kpAddBreak(el, ph); });
        });

        // N-07: Sweep and Mop cards go to their solution pages, like Wipe / Scrub / Dust
        [['48efe92', /\/product-category\/brooms\/?$/, 'solution/sweep/'], ['fe08656', /\/product-category\/mops\/?$/, 'solution/mop/']].forEach(function (r) {
            var a = page.querySelector('.elementor-element-' + r[0] + ' a.elementor-button');
            if (a && r[1].test(a.getAttribute('href') || '')) { a.setAttribute('href', a.href.replace(r[1], '/' + r[2])); }
        });

        // O-10: on desktop the area pop-up opens to the LEFT of its hotspot, joined by a thin line
        var hs = document.getElementById('sk-hs');
        if (hs) {
            var line = document.createElement('span');
            line.className = 'kp-hs-line';
            line.setAttribute('aria-hidden', 'true');
            line.style.cssText = 'position:absolute;z-index:2;height:1px;background:#ffffff;box-shadow:0 0 2px rgba(0,0,0,.35);pointer-events:none;display:none;';
            hs.appendChild(line);
            var desk = window.matchMedia('(min-width: 1025px)');
            var placeLeft = function (card) {
                line.style.display = 'none';
                if (!desk.matches || !card.classList.contains('is-open')) { return; }
                var dot = hs.querySelector('.sk-hs__dot[data-target="' + card.id + '"]');
                if (!dot) { return; }
                var px = dot.offsetLeft + dot.offsetWidth / 2, py = dot.offsetTop + dot.offsetHeight / 2;
                var r = dot.offsetWidth / 2, gap = 64, cw = card.offsetWidth, ch = card.offsetHeight;
                var x = px - r - gap - cw;
                if (x < 8) { return; }   // no room on the left: keep the original placement
                var y = Math.max(8, Math.min(py - 52, hs.clientHeight - ch - 8));
                card.style.left = x + 'px';
                card.style.top = y + 'px';
                line.style.left = (x + cw) + 'px';
                line.style.top = py + 'px';
                line.style.width = (gap) + 'px';
                line.style.display = 'block';
            };
            hs.querySelectorAll('.sk-hs__card').forEach(function (card) {
                new MutationObserver(function () { placeLeft(card); }).observe(card, { attributes: true, attributeFilter: ['class'] });
            });
            window.addEventListener('resize', function () { var open = hs.querySelector('.sk-hs__card.is-open'); if (open) { placeLeft(open); } else { line.style.display = 'none'; } });
        }
    })();

    /* ---------- 1d. Where to Buy (design v3 Phase 5) ---------- */
    (function () {
        var page = document.querySelector('.elementor-132');
        if (!page) { return; }
        var T = function (id) { return page.querySelector('.elementor-element-' + id + ' .elementor-heading-title'); };
        kpAddBreak(T('846c896'), 'specializing in household');   // W-23: line 1 ends "brand"
        kpAddBreak(T('e1a9a94'), 'We o');                         // W-27: "We offer…" starts line 2
        kpAddBreak(T('e1a9a94'), 'performing square');            // W-27: "performing…" starts line 3
    })();

    /* ---------- 1e. Catalogue cards (design v3 Phase 6, K-22): name on two lines, after the closing quote ---------- */
    document.querySelectorAll('.kk-cat ul.products .woocommerce-loop-product__title').forEach(function (t) {
        if (t.querySelector('.kp-br')) { return; }
        var m = /^(.*”)\s+(.+)$/.exec(t.textContent.trim());
        if (!m || t.children.length) { return; }
        t.textContent = '';
        t.appendChild(document.createTextNode(m[1] + ' '));
        var br = document.createElement('br'); br.className = 'kp-br'; t.appendChild(br);
        t.appendChild(document.createTextNode(m[2]));
    });

    /* ---------- 1f. product page scroll-down box (design v3 Phase 7, P-15) ---------- */
    (function () {
        var info = document.querySelector('.kk-sp .kk-pinfo');
        var target = document.querySelector('.kk-sp .kk-highlights, .kk-sp #kk-highlight');
        if (!info || !target || info.querySelector('.kp-sp-down')) { return; }
        var b = document.createElement('button');
        b.type = 'button';
        b.className = 'kp-sp-down';
        b.setAttribute('aria-label', 'Scroll to the product highlights');
        b.addEventListener('click', function () {
            var y = target.getBoundingClientRect().top + window.pageYOffset - 24;
            window.scrollTo({ top: y, behavior: 'smooth' });
        });
        info.appendChild(b);
    })();

    /* ---------- 1g. Who We Are (design v3 Phase 8): A-17 four lines, A-22 line 1 ends "affordable doesn't" ---------- */
    (function () {
        var page = document.querySelector('.elementor-153');
        if (!page) { return; }
        var T = function (id) { return page.querySelector('.elementor-element-' + id + ' .elementor-heading-title'); };
        kpAddBreak(T('f6db65f'), 'have to mean ordinary');
    })();

    /* ---------- 2. product highlight images: every image gets the curtain reveal ----------
       The page animation starts the reveal when a picture scrolls into view, but these
       pictures are lazy-loaded, so only the first was ready in time. Here each one is
       downloaded early and revealed only once it is decoded. */
    var hl = [].slice.call(document.querySelectorAll('.kk-highlight'));
    if (hl.length && html.classList.contains('kk-anim') && 'IntersectionObserver' in window) {
        var ready = function (img) {
            return new Promise(function (resolve) {
                if (!img) { resolve(); return; }
                var done = false, ok = function () { if (!done) { done = true; resolve(); } };
                var dec = function () { if (img.decode) { img.decode().then(ok, ok); } else { ok(); } };
                if (img.complete && img.naturalWidth > 0) { dec(); } else {
                    img.addEventListener('load', dec, { once: true });
                    img.addEventListener('error', ok, { once: true });
                }
                setTimeout(ok, 5000);
            });
        };
        var hio = new IntersectionObserver(function (entries) {
            entries.forEach(function (e) {
                if (!e.isIntersecting) { return; }
                var el = e.target;
                hio.unobserve(el);
                ready(el.querySelector('img')).then(function () {
                    requestAnimationFrame(function () {
                        el.classList.add('kk-in');
                        setTimeout(function () { el.classList.add('kk-done'); el.classList.remove('kk-in'); }, 2100);
                    });
                });
            });
        }, { rootMargin: '0px 0px -8% 0px', threshold: 0.12 });
        hl.forEach(function (el) {
            if (el.classList.contains('kk-done')) { return; }
            el.__kkRevealed = true;   // the general page animation leaves these to us
            el.__kkDone = true;
            var img = el.querySelector('img');
            if (img) { img.loading = 'eager'; img.decoding = 'async'; }
            hio.observe(el);
        });
    }

    /* ---------- 3. catalogue filters: phones only (<= 767px) ----------
       Accordion groups inside a slide-up drawer, opened from a "Filter" button.
       On tablet / desktop the original sidebar is left exactly as it was. */
    var side = document.querySelector('.kk-cat__side');
    if (!side) { return; }

    var home = document.createComment('kp-filters');
    side.parentNode.insertBefore(home, side);
    var main = document.querySelector('.kk-cat__main');
    var built = null, lastFocus = null;

    function setOpen(g, open) {
        g.classList.toggle('is-open', open);
        var b = g.querySelector('.kp-acc__btn');
        if (b) { b.setAttribute('aria-expanded', open ? 'true' : 'false'); }
    }

    function openDrawer() {
        if (!built) { return; }
        lastFocus = document.activeElement;
        html.classList.add('kp-filters-open');
        built.openBtn.setAttribute('aria-expanded', 'true');
        side.setAttribute('aria-hidden', 'false');
        setTimeout(function () { var c = side.querySelector('.kp-drawer__close'); if (c) { c.focus(); } }, 60);
    }
    function closeDrawer() {
        html.classList.remove('kp-filters-open');
        if (!built) { return; }
        built.openBtn.setAttribute('aria-expanded', 'false');
        side.setAttribute('aria-hidden', 'true');
        if (lastFocus && lastFocus.focus) { lastFocus.focus(); }
    }
    function onKey(e) { if (e.key === 'Escape' && html.classList.contains('kp-filters-open')) { closeDrawer(); } }

    function build() {
        if (built) { return; }
        var b = { groups: [] };
        var uid = 0, activeGroup = null;

        [].slice.call(side.querySelectorAll('.kk-cat__group')).forEach(function (g) {
            var label = g.querySelector('.kk-cat__label');
            var list  = g.querySelector('.kk-cat__list');
            if (!label || !list) { return; }
            var id  = 'kp-acc-' + (++uid);
            var btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'kp-acc__btn';
            btn.setAttribute('aria-controls', id);
            btn.innerHTML = '<span>' + label.textContent + '<span class="kp-acc__count">' + list.querySelectorAll('li').length + '</span></span><span class="kp-acc__icon" aria-hidden="true"></span>';
            var panel = document.createElement('div');
            panel.className = 'kp-acc__panel';
            panel.id = id;
            list.parentNode.insertBefore(panel, list);
            panel.appendChild(list);
            label.parentNode.insertBefore(btn, label);
            label.hidden = true;
            g.classList.add('kp-acc');
            btn.addEventListener('click', function () { setOpen(g, !g.classList.contains('is-open')); });
            if (!activeGroup && list.querySelector('.kk-cat__link.is-active')) { activeGroup = g; }
            b.groups.push({ g: g, label: label, list: list, btn: btn, panel: panel });
        });
        b.groups.forEach(function (x, k) { setOpen(x.g, activeGroup ? x.g === activeGroup : k === 0); });

        var body = document.createElement('div');
        body.className = 'kp-drawer__body';
        while (side.firstChild) { body.appendChild(side.firstChild); }

        var head = document.createElement('div');
        head.className = 'kp-drawer__head';
        head.innerHTML = '<p class="kp-drawer__title" id="kp-drawer-title">Filters</p><button type="button" class="kp-drawer__close" aria-label="Close filters">&times;</button>';

        var shopUrl = (document.querySelector('.kk-hero__crumbs a:nth-of-type(2)') || {}).href || '';
        var foot = document.createElement('div');
        foot.className = 'kp-drawer__foot';
        foot.innerHTML = (shopUrl ? '<a class="kp-drawer__reset" href="' + shopUrl + '">Show all</a>' : '') +
                         '<button type="button" class="kp-drawer__done">Done</button>';

        side.appendChild(head);
        side.appendChild(body);
        side.appendChild(foot);
        if (!side.id) { side.id = 'kp-filters'; b.setId = true; }
        side.setAttribute('aria-labelledby', 'kp-drawer-title');
        side.setAttribute('role', 'dialog');
        side.setAttribute('aria-modal', 'true');
        side.setAttribute('aria-hidden', 'true');
        side.classList.add('kp-drawer');
        document.body.appendChild(side);   // above the header and the overlay

        var overlay = document.createElement('div');
        overlay.className = 'kp-drawer-overlay';
        document.body.appendChild(overlay);

        var current = side.querySelector('.kk-cat__link.is-active');
        var bar = document.createElement('div');
        bar.className = 'kp-filterbar';
        bar.innerHTML =
            '<button type="button" class="kp-filterbar__btn" aria-controls="' + side.id + '" aria-expanded="false">' +
                '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M4 6h16M7 12h10M10 18h4"/></svg>Filter' +
            '</button>' +
            (current ? '<span class="kp-filterbar__chip">Showing: <b>' + current.textContent + '</b></span>' : '');
        if (main) { main.insertBefore(bar, main.firstChild); }

        b.body = body; b.head = head; b.foot = foot; b.overlay = overlay; b.bar = bar;
        b.openBtn = bar.querySelector('.kp-filterbar__btn');
        b.openBtn.addEventListener('click', openDrawer);
        overlay.addEventListener('click', closeDrawer);
        head.querySelector('.kp-drawer__close').addEventListener('click', closeDrawer);
        foot.querySelector('.kp-drawer__done').addEventListener('click', closeDrawer);
        document.addEventListener('keydown', onKey);
        built = b;
    }

    function destroy() {
        if (!built) { return; }
        var b = built;
        html.classList.remove('kp-filters-open');
        document.removeEventListener('keydown', onKey);
        b.groups.forEach(function (x) {
            x.label.hidden = false;
            x.panel.parentNode.insertBefore(x.list, x.panel);
            x.panel.parentNode.removeChild(x.panel);
            x.btn.parentNode.removeChild(x.btn);
            x.g.classList.remove('kp-acc', 'is-open');
        });
        while (b.body.firstChild) { side.insertBefore(b.body.firstChild, b.head); }
        [b.head, b.body, b.foot, b.overlay, b.bar].forEach(function (n) { if (n.parentNode) { n.parentNode.removeChild(n); } });
        ['role', 'aria-modal', 'aria-hidden', 'aria-labelledby'].forEach(function (a) { side.removeAttribute(a); });
        if (b.setId) { side.removeAttribute('id'); }
        side.classList.remove('kp-drawer');
        home.parentNode.insertBefore(side, home.nextSibling);
        built = null;
    }

    function sync() { if (mqMobile.matches) { build(); } else { destroy(); } }
    if (mqMobile.addEventListener) { mqMobile.addEventListener('change', sync); }
    else if (mqMobile.addListener) { mqMobile.addListener(sync); }
    sync();
})();
</script>
    <?php
}
