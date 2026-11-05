

/**
 * Kress Kleen – site polish
 * Mobile sticky header, hover fixes, typography + spacing on every page,
 * mobile/tablet footer, Who We Are + Where to buy responsive layout,
 * catalogue filters (accordion on desktop, slide-up drawer on tablet/mobile).
 * Paste at the END of functions.php. To undo, delete this block.
 * Source files: kress-kleen-polish/kk-polish.css + kk-polish.js
 */
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
.elementor-widget-image { overflow: hidden; }
.elementor-widget-image img { max-width: 100% !important; }
.wpcf7 form.wpcf7-form { position: relative; }
.wpcf7-spinner { position: absolute !important; right: 14px; bottom: 14px; left: auto; margin: 0 !important; }
.elementor-heading-title { overflow-wrap: break-word; }

/* =====================================================================
   1. HOME HERO (scroll video) – smaller banner text
   ===================================================================== */
.kk-sh .kk-sh__card {
  width: min(540px, 100%);
  padding: 28px 30px 30px;
}
.kk-sh .kk-sh__card--center { margin-left: calc(min(540px, 100%) / -2); }
.kk-sh .kk-sh__eyebrow { font-size: 16px; letter-spacing: 0.08em; }
.kk-sh h2.kk-sh__title { margin: 6px 0 10px; font-size: clamp(24px, 2.1vw, 32px); line-height: 1.1; }
.kk-sh .kk-sh__text { margin-bottom: 20px; font-size: 16px; }
.kk-sh a.kk-sh__btn { padding: 11px 24px; font-size: 13px; letter-spacing: 0.06em; }

@media (max-width: 767px) {
  .kk-sh .kk-sh__card { padding: 18px 18px 20px; border-radius: 14px; bottom: 4vh !important; }
  .kk-sh .kk-sh__eyebrow { font-size: 12px; }
  .kk-sh h2.kk-sh__title { margin: 4px 0 6px; font-size: 19px; }
  .kk-sh .kk-sh__text { margin-bottom: 14px; font-size: 14px; }
  .kk-sh a.kk-sh__btn { padding: 9px 18px; font-size: 12px; }
}

/* =====================================================================
   2. PAGE BANNERS (Who We Are, Where to buy, Catalogue) – smaller titles
   ===================================================================== */
.elementor-153 .elementor-element.elementor-element-901a2db .elementor-heading-title,
.elementor-132 .elementor-element.elementor-element-4cd68f2 .elementor-heading-title,
.kk-hero .kk-hero__title {
  font-size: clamp(40px, 4.2vw, 60px) !important;
  line-height: 1.05;
}
@media (max-width: 1024px) {
  .elementor-153 .elementor-element.elementor-element-901a2db .elementor-heading-title,
  .elementor-132 .elementor-element.elementor-element-4cd68f2 .elementor-heading-title,
.elementor-132 .elementor-element.elementor-element-4cd68f2 .elementor-heading-title,
  .kk-hero .kk-hero__title { font-size: 40px !important; }
}
@media (max-width: 767px) {
  .elementor-153 .elementor-element.elementor-element-901a2db .elementor-heading-title,
  .elementor-132 .elementor-element.elementor-element-4cd68f2 .elementor-heading-title,
.elementor-132 .elementor-element.elementor-element-4cd68f2 .elementor-heading-title,
  .kk-hero .kk-hero__title { font-size: 32px !important; }
  .elementor-153 .elementor-element.elementor-element-cc1a00a { --min-height: 0px; min-height: 0; --padding-top: 72px; --padding-bottom: 40px; --padding-left: var(--kp-gutter); --padding-right: var(--kp-gutter); }
  .elementor-153 .elementor-element.elementor-element-cc1a00a > .e-con-inner { padding: 0 !important; }
  .elementor-153 .elementor-element.elementor-element-90a5b6c { margin-bottom: 8px; }
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
  #masthead .elementor-element.elementor-element-56435bc { --min-height: 28px; min-height: 28px; --padding-top: 6px; --padding-bottom: 6px; }
  #masthead .elementor-element.elementor-element-8750345 { font-size: 10px; }

  /* main bar: menu | logo | where to buy */
  #masthead .elementor-element.elementor-element-4a693fd {
    --min-height: 64px; min-height: 64px;
    --padding-top: 8px; --padding-bottom: 8px;
    --padding-left: 16px; --padding-right: 16px;
    --flex-wrap: nowrap; flex-wrap: nowrap;
    background-color: transparent;
  }
  #masthead .elementor-element.elementor-element-4a693fd > .e-con-inner {
    display: grid !important;
    grid-template-columns: 1fr auto 1fr;
    align-items: center;
    gap: 8px;
    padding: 0 !important;
  }
  /* menu + language (left) */
  #masthead .elementor-element.elementor-element-f760a15 {
    grid-column: 1; grid-row: 1;
    --width: auto; width: auto !important; max-width: none;
    --flex-wrap: nowrap; flex-wrap: nowrap;
    --gap: 10px; gap: 10px;
    --justify-content: flex-start; justify-content: flex-start;
    padding: 0 !important; margin: 0 !important;
    order: 0 !important;
  }
  #masthead .elementor-element.elementor-element-98f606d .elementor-icon { font-size: 24px; }
  #masthead .elementor-element.elementor-element-98f606d .elementor-icon svg { height: 22px; width: auto; }
  #masthead .elementor-element.elementor-element-98f606d .elementor-icon-wrapper { line-height: 0; }
  #masthead .elementor-element.elementor-element-e8b8918 { line-height: 1; }
  #masthead .elementor-element.elementor-element-e8b8918 .gtranslate_wrapper { gap: 10px !important; }
  #masthead .elementor-element.elementor-element-e8b8918 a.glink::after,
  #masthead .elementor-element.elementor-element-e8b8918 a[data-gt-lang]::after { font-size: 12px !important; }
  /* logo (centre) */
  #masthead .elementor-element.elementor-element-ae1ae2e {
    grid-column: 2; grid-row: 1;
    width: auto !important; max-width: none; --container-widget-width: auto;
    margin: 0 !important;
  }
  #masthead .elementor-element.elementor-element-ae1ae2e img { width: auto !important; height: 46px; max-width: 120px; object-fit: contain; }
  /* where to buy (right) */
  #masthead .elementor-element.elementor-element-0a2ca92 {
    grid-column: 3; grid-row: 1;
    justify-self: end;
    width: auto !important; max-width: none; --container-widget-width: auto;
    margin: 0 !important;
  }
  #masthead .elementor-element.elementor-element-0a2ca92 .elementor-button {
    padding: 9px 12px;
    font-size: 11px;
    line-height: 1;
    letter-spacing: 0.04em;
    border-radius: 999px;
    white-space: nowrap;
  }

  /* stuck state: solid brand bar with a soft shadow */
  #masthead.kp-stuck { background-color: rgba(0, 97, 61, 0.96); box-shadow: 0 6px 20px rgba(0, 0, 0, 0.18); }
  @supports (backdrop-filter: blur(10px)) {
    #masthead.kp-stuck { background-color: rgba(0, 97, 61, 0.88); -webkit-backdrop-filter: blur(12px) saturate(140%); backdrop-filter: blur(12px) saturate(140%); }
  }

  /* green strips that sit behind the (shorter) header */
  :root { --kk-hdr-h: 92px; }

  /* the side menu must open above the sticky header */
  .kk-side-overlay { z-index: 1090; }
  .kk-side { z-index: 1100; }
}

/* =====================================================================
   4. HOME – "Let's find your cleaning solution" cards
   ===================================================================== */
/* EXPLORE is a text link: no shine box, no lift, no shadow – arrow slides and underline draws */
.elementor-43 .kkc-card .elementor-widget-button .elementor-button {
  overflow: visible;
  box-shadow: none !important;
  transform: none !important;
  background: transparent !important;
}
.elementor-43 .kkc-card .elementor-widget-button .elementor-button::after {
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
.elementor-43 .kkc-card { transition: box-shadow 0.4s ease, transform 0.4s var(--kp-ease), border-color 0.3s ease; }
.elementor-43 .kkc-card .elementor-widget-image img { transition: transform 0.9s var(--kp-ease); }
@media (hover: hover) {
  .elementor-43 .kkc-card:hover { box-shadow: 0 18px 40px rgba(0, 0, 0, 0.10); }
  .elementor-43 .kkc-card:hover .elementor-widget-image img { transform: scale(1.05); }
  .elementor-43 .kkc-card .elementor-widget-button .elementor-button:hover::after,
  .elementor-43 .kkc-card:hover .elementor-widget-button .elementor-button::after { transform: scaleX(1); }
  .elementor-43 .kkc-card:hover .elementor-widget-button .elementor-button-icon { transform: translateX(5px); }
}

/* =====================================================================
   5. HOME – "One home. Many cleaning challenges" area buttons
   ===================================================================== */
.elementor-43 :is(.elementor-element.elementor-element-a682427, .elementor-element.elementor-element-3a87742, .elementor-element.elementor-element-e648c86, .elementor-element.elementor-element-0f74986) {
  border: 0 !important;
  border-radius: 999px !important;
  width: 100% !important;
  max-width: 300px !important;
  margin: 0 !important;
}
.elementor-43 :is(.elementor-element.elementor-element-a682427, .elementor-element.elementor-element-3a87742, .elementor-element.elementor-element-e648c86, .elementor-element.elementor-element-0f74986) .elementor-button {
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
.elementor-43 :is(.elementor-element.elementor-element-a682427, .elementor-element.elementor-element-3a87742, .elementor-element.elementor-element-e648c86, .elementor-element.elementor-element-0f74986) .elementor-button::after {
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
.elementor-43 :is(.elementor-element.elementor-element-a682427, .elementor-element.elementor-element-3a87742, .elementor-element.elementor-element-e648c86, .elementor-element.elementor-element-0f74986) .elementor-button-content-wrapper { line-height: 1; }
@media (hover: hover) {
  .elementor-43 :is(.elementor-element.elementor-element-a682427, .elementor-element.elementor-element-3a87742, .elementor-element.elementor-element-e648c86, .elementor-element.elementor-element-0f74986) .elementor-button:hover {
    background: var(--kp-orange) !important;
    border-color: var(--kp-orange) !important;
    color: #ffffff !important;
  }
  .elementor-43 :is(.elementor-element.elementor-element-a682427, .elementor-element.elementor-element-3a87742, .elementor-element.elementor-element-e648c86, .elementor-element.elementor-element-0f74986) .elementor-button:hover::after { transform: none; opacity: 1; }
}
.elementor-43 :is(.elementor-element.elementor-element-a682427, .elementor-element.elementor-element-3a87742, .elementor-element.elementor-element-e648c86, .elementor-element.elementor-element-0f74986) .elementor-button:focus-visible {
  outline: 3px solid var(--kp-gold);
  outline-offset: 3px;
}
.elementor-43 .elementor-element.elementor-element-743fae7 { --gap: 12px; gap: 12px; }
.elementor-43 .elementor-element.elementor-element-08f48e7 { margin-bottom: 14px !important; }

/* =====================================================================
   6. HOME – typography + spacing on tablet / mobile
   ===================================================================== */
@media (max-width: 1024px) {
  .elementor-43 .kkc-card .elementor-widget-heading:first-of-type .elementor-heading-title { font-size: 24px; }
}
@media (max-width: 767px) {
  /* section rhythm */
  .elementor-43 :is(.elementor-element.elementor-element-b12f160, .elementor-element.elementor-element-975d254, .elementor-element.elementor-element-7149c5d, .elementor-element.elementor-element-1c24237) {
    --padding-top: var(--kp-sec); --padding-bottom: var(--kp-sec);
    --padding-left: var(--kp-gutter); --padding-right: var(--kp-gutter);
  }
  .elementor-43 :is(.elementor-element.elementor-element-ad39a46, .elementor-element.elementor-element-755069b, .elementor-element.elementor-element-743fae7, .elementor-element.elementor-element-5448b30) {
    --padding-left: 0px; --padding-right: 0px;
  }

  /* section headings */
  .elementor-43 :is(.elementor-element.elementor-element-9a43ca3, .elementor-element.elementor-element-cb4ed5d, .elementor-element.elementor-element-884f790, .elementor-element.elementor-element-2799513) .elementor-heading-title {
    font-size: 24px !important;
    line-height: 1.2 !important;
  }
  .elementor-43 :is(.elementor-element.elementor-element-1bde407, .elementor-element.elementor-element-08f48e7, .elementor-element.elementor-element-9d92933) .elementor-heading-title {
    font-size: 15px !important;
    line-height: 1.55 !important;
  }
  .elementor-43 .elementor-element.elementor-element-cb4ed5d { margin-bottom: 8px; }

  /* SMART / FUNCTIONAL / RELIABLE */
  .elementor-43 :is(.elementor-element.elementor-element-85d3640, .elementor-element.elementor-element-63b71db, .elementor-element.elementor-element-704d045) .elementor-icon-box-title { font-size: 20px !important; margin-bottom: 6px; }
  .elementor-43 :is(.elementor-element.elementor-element-85d3640, .elementor-element.elementor-element-63b71db, .elementor-element.elementor-element-704d045) .elementor-icon-box-description { font-size: 15px !important; }
  .elementor-43 :is(.elementor-element.elementor-element-85d3640, .elementor-element.elementor-element-63b71db, .elementor-element.elementor-element-704d045) .elementor-icon { font-size: 40px; }
  .elementor-43 .elementor-element.elementor-element-755069b { --gap: 28px; gap: 28px; margin-top: 12px; }

  /* solution cards: 2 per row */
  .elementor-43 .elementor-element.elementor-element-79b14eb {
    --flex-direction: row; flex-direction: row;
    --flex-wrap: wrap; flex-wrap: wrap;
    --gap: 12px; gap: 12px;
    --padding-left: 0px; --padding-right: 0px;
  }
  .elementor-43 .elementor-element.elementor-element-79b14eb > .kkc-card {
    --width: calc(50% - 6px); width: calc(50% - 6px) !important;
    --padding-bottom: 16px;
    border-radius: 10px; overflow: hidden;
  }
  .elementor-43 .elementor-element.elementor-element-79b14eb > .kkc-card:last-child:nth-child(odd) { --width: 100%; width: 100% !important; }
  .elementor-43 .kkc-card .elementor-widget-image img { height: 130px !important; width: 100% !important; object-fit: cover; }
  .elementor-43 .elementor-element.elementor-element-79b14eb > .kkc-card:last-child:nth-child(odd) .elementor-widget-image img { height: 170px !important; }
  .elementor-43 .kkc-card .elementor-widget-icon { margin: -22px 0 0 14px !important; }
  .elementor-43 .kkc-card .elementor-widget-icon .elementor-icon { font-size: 34px; }
  .elementor-43 .kkc-card .elementor-widget-icon svg { width: 34px; height: 34px; }
  .elementor-43 .kkc-card .elementor-widget-heading { margin: 0 14px !important; }
  .elementor-43 .kkc-card .elementor-widget-heading:nth-of-type(1) { margin-top: 10px !important; }
  .elementor-43 .kkc-card :is(.elementor-element.elementor-element-b21629b, .elementor-element.elementor-element-665f377, .elementor-element.elementor-element-252f948, .elementor-element.elementor-element-f8975af, .elementor-element.elementor-element-bcefc1b) .elementor-heading-title { font-size: 20px !important; line-height: 1.1; }
  .elementor-43 .kkc-card :is(.elementor-element.elementor-element-ed3bfa7, .elementor-element.elementor-element-21ba642, .elementor-element.elementor-element-07011f8, .elementor-element.elementor-element-ccba669, .elementor-element.elementor-element-ce02014) { margin-top: 6px !important; }
  .elementor-43 .kkc-card :is(.elementor-element.elementor-element-ed3bfa7, .elementor-element.elementor-element-21ba642, .elementor-element.elementor-element-07011f8, .elementor-element.elementor-element-ccba669, .elementor-element.elementor-element-ce02014) .elementor-heading-title { font-size: 13px !important; line-height: 1.45; }
  .elementor-43 .kkc-card .elementor-widget-button { padding: 0 14px !important; margin-top: 10px !important; }
  .elementor-43 .kkc-card .elementor-widget-button .elementor-button { font-size: 12px !important; }

  /* one home */
  .elementor-43 :is(.elementor-element.elementor-element-a682427, .elementor-element.elementor-element-3a87742, .elementor-element.elementor-element-e648c86, .elementor-element.elementor-element-0f74986) { max-width: 100% !important; }
  .elementor-43 :is(.elementor-element.elementor-element-a682427, .elementor-element.elementor-element-3a87742, .elementor-element.elementor-element-e648c86, .elementor-element.elementor-element-0f74986) .elementor-button { min-height: 46px; font-size: 14px !important; }
  .elementor-43 .elementor-element.elementor-element-38068a6 { margin-top: 24px; }

  /* featured product */
  .elementor-43 .elementor-element.elementor-element-91b1f1f { --padding-top: 40px; --padding-bottom: 32px; --padding-left: var(--kp-gutter); --padding-right: var(--kp-gutter); }
  .elementor-43 .elementor-element.elementor-element-36490c5 .elementor-heading-title { font-size: 24px !important; line-height: 1.15 !important; }
  .elementor-43 .elementor-element.elementor-element-0818b88 .elementor-heading-title { font-size: 16px !important; }
  .elementor-43 .elementor-element.elementor-element-51cf488 .elementor-icon-list-text { font-size: 14px !important; }
  .elementor-43 .elementor-element.elementor-element-d688fd8 .elementor-heading-title { font-size: 12px !important; letter-spacing: 0.1em; }
  .elementor-43 .elementor-element.elementor-element-d7da6a9 img { height: 260px !important; object-fit: cover; }
  .elementor-43 .elementor-element.elementor-element-9b21423 img { max-height: 340px; width: auto !important; margin: 0 auto; display: block; }

  /* buttons in content */
  .elementor-43 :is(.elementor-element.elementor-element-c03da2d, .elementor-element.elementor-element-041e5ca) .elementor-button { font-size: 13px !important; padding: 12px 22px !important; }
  .elementor-43 .elementor-element.elementor-element-7fb41c0 img { max-height: 320px; object-fit: contain; }
}

/* ---------- HOME – tablet (768–1024px) ---------- */
@media (min-width: 768px) and (max-width: 1024px) {
  .elementor-43 :is(.elementor-element.elementor-element-b12f160, .elementor-element.elementor-element-975d254, .elementor-element.elementor-element-7149c5d, .elementor-element.elementor-element-1c24237) {
    --padding-top: 64px; --padding-bottom: 64px; --padding-left: 32px; --padding-right: 32px;
  }
  /* designing a better way: text on top, the three values in one row below */
  .elementor-43 .elementor-element.elementor-element-b12f160 { --flex-direction: column; flex-direction: column !important; }
  .elementor-43 :is(.elementor-element.elementor-element-ad39a46, .elementor-element.elementor-element-755069b) { --width: 100%; width: 100% !important; --padding-left: 0px; --padding-right: 0px; }
  .elementor-43 .elementor-element.elementor-element-755069b { --flex-direction: row; flex-direction: row !important; --flex-wrap: nowrap; --gap: 20px; margin-top: 24px; }
  .elementor-43 .elementor-element.elementor-element-755069b > .elementor-widget { flex: 1 1 0; width: auto !important; max-width: none !important; }
  .elementor-43 :is(.elementor-element.elementor-element-85d3640, .elementor-element.elementor-element-63b71db, .elementor-element.elementor-element-704d045) .elementor-icon-box-title { font-size: 20px !important; }
  .elementor-43 :is(.elementor-element.elementor-element-85d3640, .elementor-element.elementor-element-63b71db, .elementor-element.elementor-element-704d045) .elementor-icon-box-description { font-size: 15px !important; }
  .elementor-43 .elementor-element.elementor-element-9a43ca3 .elementor-heading-title,
  .elementor-43 .elementor-element.elementor-element-cb4ed5d .elementor-heading-title,
  .elementor-43 .elementor-element.elementor-element-884f790 .elementor-heading-title,
  .elementor-43 .elementor-element.elementor-element-2799513 .elementor-heading-title { font-size: 28px !important; line-height: 1.2 !important; }

  /* solution cards: 3 + 2 */
  .elementor-43 .elementor-element.elementor-element-79b14eb { --flex-wrap: wrap; flex-wrap: wrap !important; --justify-content: center; justify-content: center !important; --gap: 14px; gap: 14px !important; --padding-left: 0px; --padding-right: 0px; }
  .elementor-43 .elementor-element.elementor-element-79b14eb > .kkc-card { --width: calc(33.333% - 10px); width: calc(33.333% - 10px) !important; }
  .elementor-43 .kkc-card .elementor-widget-image img { height: 170px !important; width: 100% !important; object-fit: cover; }

  /* one home: text on top, the map full width */
  .elementor-43 .elementor-element.elementor-element-7149c5d { --flex-direction: column; flex-direction: column !important; }
  .elementor-43 .elementor-element.elementor-element-743fae7 { --width: 100%; width: 100% !important; --padding-left: 0px; --padding-right: 0px; }
  .elementor-43 .elementor-element.elementor-element-38068a6 { width: 100% !important; max-width: 100% !important; margin-top: 24px; }

  /* featured product: photo on top */
  .elementor-43 .elementor-element.elementor-element-8e91e85 { --flex-direction: column; flex-direction: column !important; }
  .elementor-43 .elementor-element.elementor-element-d7da6a9 { width: 100% !important; max-width: 100% !important; }
  .elementor-43 .elementor-element.elementor-element-d7da6a9 img { width: 100% !important; height: 340px !important; object-fit: cover; }
  .elementor-43 .elementor-element.elementor-element-1676003 { --width: 100%; width: 100% !important; }
  .elementor-43 .elementor-element.elementor-element-36490c5 .elementor-heading-title { font-size: 30px !important; }
}

/* =====================================================================
   7. FOOTER – tidy on tablet / mobile
   ===================================================================== */
@media (max-width: 1024px) {
  .elementor-30 .elementor-element.elementor-element-ab6f316 {
    --padding-top: 48px; --padding-bottom: 28px;
    --padding-left: var(--kp-gutter); --padding-right: var(--kp-gutter);
  }
  .elementor-30 .elementor-element.elementor-element-ab6f316,
  .elementor-30 .elementor-element.elementor-element-ab6f316 > .e-con-inner {
    --flex-direction: column; flex-direction: column !important;
    --align-items: center; align-items: center !important;
    --gap: 26px; gap: 26px !important;
    text-align: center;
  }
  .elementor-30 .elementor-element.elementor-element-9f80d62 { width: auto !important; max-width: none !important; margin: 0 !important; }
  .elementor-30 .elementor-element.elementor-element-9f80d62 img { width: 150px !important; height: auto; }
  .elementor-30 .elementor-element.elementor-element-ce94520 {
    --width: 100%; width: 100% !important;
    --align-items: center; align-items: center !important;
    --gap: 12px; gap: 12px !important;
    padding: 0 !important;
  }
  .elementor-30 .elementor-element.elementor-element-0302daf .elementor-heading-title { font-size: 12px !important; letter-spacing: 0.14em; }
  .elementor-30 .elementor-element.elementor-element-501b3e5 .elementor-icon-list-items { justify-content: center !important; row-gap: 8px; }
  .elementor-30 .elementor-element.elementor-element-501b3e5 .elementor-icon-list-text { font-size: 15px !important; }
  .elementor-30 .elementor-element.elementor-element-6e495df {
    --width: 100%; width: 100% !important; max-width: 420px;
    display: grid !important;
    grid-template-columns: 1fr;
    gap: 10px !important;
    padding: 0 !important;
  }
  .elementor-30 .elementor-element.elementor-element-6e495df > .elementor-widget { width: 100% !important; max-width: none !important; margin: 0 !important; }
  .elementor-30 .elementor-element.elementor-element-6e495df .elementor-button {
    display: flex; width: 100%;
    justify-content: center;
    min-height: 46px;
    padding: 0 14px !important;
    font-size: 14px !important;
    border-radius: 10px !important;
    align-items: center;
  }
  .elementor-30 .elementor-element.elementor-element-4ba5bf5 {
    grid-column: 1 / -1;
    margin-top: 14px !important;
    padding-top: 18px;
    border-top: 1px solid rgba(255, 255, 255, 0.22);
  }
  .elementor-30 .elementor-element.elementor-element-4ba5bf5 .elementor-heading-title { font-size: 12px !important; line-height: 1.6; opacity: 0.9; text-align: center; }
}

/* =====================================================================
   8. WHO WE ARE (page 153) – desktop scale + full mobile layout
   ===================================================================== */
.elementor-153 .elementor-element.elementor-element-fb87731 .elementor-heading-title { font-size: clamp(56px, 6.4vw, 92px) !important; line-height: 1.05; }
.elementor-153 .elementor-element.elementor-element-092cb7a .elementor-heading-title { font-size: clamp(72px, 9vw, 140px) !important; line-height: 1.1; }

@media (max-width: 1024px) {
  .elementor-153 :is(.elementor-element.elementor-element-5d0b5e3, .elementor-element.elementor-element-b86e4b4) .elementor-heading-title { font-size: 40px !important; }
  .elementor-153 :is(.elementor-element.elementor-element-89c5073, .elementor-element.elementor-element-162ec16) .elementor-heading-title { font-size: 28px !important; }
  .elementor-153 .elementor-element.elementor-element-bbc3710 { --flex-wrap: wrap; flex-wrap: wrap; }
  .elementor-153 .elementor-element.elementor-element-bbc3710 > .e-con-inner { display: flex !important; flex-wrap: wrap !important; gap: 24px 16px !important; }
  .elementor-153 .elementor-element.elementor-element-bbc3710 > .e-con,
  .elementor-153 .elementor-element.elementor-element-bbc3710 > .e-con-inner > .e-con { --width: calc(50% - 8px); width: calc(50% - 8px) !important; }
}

@media (max-width: 767px) {
  /* intro */
  .elementor-153 .elementor-element.elementor-element-79a8b7a { --padding-top: 48px; --padding-bottom: 24px; --padding-left: var(--kp-gutter); --padding-right: var(--kp-gutter); --gap: 28px; }
  .elementor-153 .elementor-element.elementor-element-1a21684 { --padding-left: 0px; --padding-right: 0px; --padding-top: 0px; --gap: 10px; }
  .elementor-153 :is(.elementor-element.elementor-element-5d0b5e3, .elementor-element.elementor-element-b86e4b4) .elementor-heading-title { font-size: 30px !important; line-height: 1.15 !important; }
  .elementor-153 .elementor-element.elementor-element-71918f6 { margin: 10px 0 !important; }
  .elementor-153 .elementor-element.elementor-element-71918f6 img { height: 56px !important; width: auto !important; }
  .elementor-153 :is(.elementor-element.elementor-element-89c5073, .elementor-element.elementor-element-162ec16) .elementor-heading-title { font-size: 22px !important; line-height: 1.25 !important; }
  .elementor-153 .elementor-element.elementor-element-7ed0220 { margin-top: 8px !important; }
  .elementor-153 .elementor-element.elementor-element-7ed0220 .elementor-heading-title { font-size: 15px !important; line-height: 1.6 !important; }
  .elementor-153 .elementor-element.elementor-element-4c01e73 { width: 100% !important; max-width: 100% !important; --container-widget-width: 100%; text-align: center; margin: 0 !important; }
  .elementor-153 .elementor-element.elementor-element-4c01e73 img { width: auto !important; max-width: 78% !important; height: auto !important; max-height: 380px; margin: 0 auto; display: block; object-fit: contain; }

  /* material / durability / ergonomics / workmanship: 2 x 2 grid */
  .elementor-153 .elementor-element.elementor-element-bbc3710 {
    --flex-direction: row; flex-direction: row;
    --flex-wrap: wrap; flex-wrap: wrap;
    --gap: 16px 12px; gap: 22px 12px;
    --padding-top: 24px; --padding-bottom: 56px;
    --padding-left: var(--kp-gutter); --padding-right: var(--kp-gutter);
  }
  .elementor-153 .elementor-element.elementor-element-bbc3710 > .e-con-inner {
    display: flex !important;
    flex-direction: row !important;
    flex-wrap: wrap !important;
    gap: 22px 12px !important;
    padding: 0 !important;
  }
  .elementor-153 .elementor-element.elementor-element-bbc3710 > .e-con,
  .elementor-153 .elementor-element.elementor-element-bbc3710 > .e-con-inner > .e-con {
    --width: calc(50% - 6px); width: calc(50% - 6px) !important;
    --padding-top: 0px; --padding-left: 0px; --padding-right: 0px; --padding-bottom: 0px;
    --gap: 10px;
  }
  .elementor-153 .elementor-element.elementor-element-bbc3710 .elementor-widget-image img { height: 140px !important; width: 100% !important; object-fit: cover; border-radius: 12px; }
  .elementor-153 .elementor-element.elementor-element-bbc3710 .elementor-widget-heading .elementor-heading-title { font-size: 16px !important; }
  .elementor-153 .elementor-element.elementor-element-bbc3710 .elementor-widget-divider { --divider-width: 36px; margin: 0 !important; }

  /* dark statement boxes */
  .elementor-153 :is(.elementor-element.elementor-element-1753931, .elementor-element.elementor-element-915d10f) {
    --padding-top: 48px; --padding-bottom: 48px;
    --padding-left: var(--kp-gutter); --padding-right: var(--kp-gutter);
    --gap: 22px; --min-height: 0px;
  }
  .elementor-153 :is(.elementor-element.elementor-element-1b9f740, .elementor-element.elementor-element-3686e69, .elementor-element.elementor-element-f5a2f5e, .elementor-element.elementor-element-e6c8adb) img { height: 18px !important; width: auto !important; }
  .elementor-153 :is(.elementor-element.elementor-element-f17afcf, .elementor-element.elementor-element-e5f7c30) { margin: 0 !important; }
  .elementor-153 .elementor-element.elementor-element-f17afcf .elementor-heading-title { font-size: 18px !important; line-height: 1.6 !important; }
  .elementor-153 .elementor-element.elementor-element-e5f7c30 .elementor-heading-title { font-size: 22px !important; line-height: 1.45 !important; }

  /* no hype */
  .elementor-153 .elementor-element.elementor-element-5419111 { --padding-top: 56px; --padding-bottom: 56px; --padding-left: var(--kp-gutter); --padding-right: var(--kp-gutter); --gap: 16px; --min-height: 0px; }
  .elementor-153 .elementor-element.elementor-element-83cce31 .elementor-heading-title { font-size: 14px !important; line-height: 1.6 !important; }
  .elementor-153 .elementor-element.elementor-element-fb87731 .elementor-heading-title { font-size: 31px !important; line-height: 1.15 !important; }
  .elementor-153 .elementor-element.elementor-element-f6db65f { margin: 0 !important; }
  .elementor-153 .elementor-element.elementor-element-f6db65f .elementor-heading-title { font-size: 16px !important; line-height: 1.6 !important; }

  /* guaranteed to shine */
  .elementor-153 .elementor-element.elementor-element-bcabc46 { --padding-top: 48px; --padding-bottom: 48px; --padding-left: var(--kp-gutter); --padding-right: var(--kp-gutter); --min-height: 0px; }
  .elementor-153 .elementor-element.elementor-element-092cb7a .elementor-heading-title { font-size: 54px !important; line-height: 1.2 !important; }
}

/* =====================================================================
   8b. WHERE TO BUY (page 132)
   ===================================================================== */
@media (max-width: 767px) {
  .elementor-132 .elementor-element.elementor-element-39346a4 { --min-height: 0px; min-height: 0; --padding-top: 72px; --padding-bottom: 40px; --padding-left: var(--kp-gutter); --padding-right: var(--kp-gutter); }
  .elementor-132 .elementor-element.elementor-element-39346a4 > .e-con-inner { padding: 0 !important; }
  .elementor-132 .elementor-element.elementor-element-609ea42 { --padding-top: 48px; --padding-bottom: 48px; --padding-left: var(--kp-gutter); --padding-right: var(--kp-gutter); }
  .elementor-132 .elementor-element.elementor-element-506a975 .elementor-heading-title { font-size: 24px !important; line-height: 1.2 !important; }
  .elementor-132 .elementor-element.elementor-element-9039a56 { --padding-top: 56px; --padding-bottom: 56px; --padding-left: var(--kp-gutter); --padding-right: var(--kp-gutter); }
  .elementor-132 .elementor-element.elementor-element-142ce73 .elementor-heading-title { font-size: 12px !important; letter-spacing: 0.1em; }
  .elementor-132 .elementor-element.elementor-element-846c896 { margin-left: 0 !important; margin-right: 0 !important; }
  .elementor-132 .elementor-element.elementor-element-846c896 .elementor-heading-title { font-size: 21px !important; line-height: 1.3 !important; }
  .elementor-132 .elementor-element.elementor-element-e1a9a94 .elementor-heading-title { font-size: 15px !important; line-height: 1.65 !important; }
}

/* =====================================================================
   9. CATALOGUE – banner, filters (accordion + mobile drawer), grid
   ===================================================================== */
.kk-cat__group { margin-bottom: 0 !important; border-bottom: 1px solid rgba(0, 97, 61, 0.14); }
.kk-cat__group:last-child { border-bottom: 0; }
.kk-cat__side .kp-acc__btn {
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
.kk-cat__side .kp-acc__btn:hover,
.kk-cat__side .kp-acc__btn:focus { background: none; color: var(--kp-orange); }
.kk-cat__side .kp-acc__btn:focus-visible { outline: 2px solid var(--kp-gold); outline-offset: 4px; }
.kk-cat__side .kp-acc__count {
  margin-left: 8px;
  padding: 2px 8px;
  border-radius: 99px;
  background: rgba(0, 97, 61, 0.1);
  color: var(--kp-green-dark);
  font-size: 11px;
  letter-spacing: 0;
}
.kk-cat__side .kp-acc__icon {
  position: relative;
  flex: 0 0 auto;
  width: 12px; height: 12px;
  margin-left: auto;
}
.kk-cat__side .kp-acc__icon::before,
.kk-cat__side .kp-acc__icon::after {
  content: "";
  position: absolute;
  left: 0; top: 50%;
  width: 12px; height: 1.6px;
  margin-top: -0.8px;
  background: var(--kp-green-dark);
  transition: transform 0.35s var(--kp-ease);
}
.kk-cat__side .kp-acc__icon::after { transform: rotate(90deg); }
.kk-cat__side .kp-acc.is-open .kp-acc__icon::after { transform: rotate(0deg); }
.kk-cat__side .kp-acc__panel {
  display: grid;
  grid-template-rows: 0fr;
  transition: grid-template-rows 0.4s var(--kp-ease);
}
.kk-cat__side .kp-acc.is-open .kp-acc__panel { grid-template-rows: 1fr; }
.kk-cat__side .kp-acc__panel > .kk-cat__list { min-height: 0; overflow: hidden; margin: 0 !important; }
.kk-cat__side .kp-acc.is-open .kp-acc__panel > .kk-cat__list { padding-bottom: 16px; }
.kk-cat__side .kk-cat__link { transition: color 0.2s ease, padding 0.3s var(--kp-ease); }
.kk-cat__side .kk-cat__link.is-active { color: var(--kp-orange) !important; }
@media (hover: hover) {
  .kk-cat__side .kk-cat__link:hover { padding-left: 4px; }
}

/* filter bar + drawer (tablet / mobile) */
.kp-filterbar { display: none; }
.kp-drawer__head, .kp-drawer__foot { display: none; }

@media (max-width: 1024px) {
  .kk-hero { padding-top: 56px !important; padding-bottom: 40px !important; }
  .kk-hero .kk-hero__crumbs { font-size: 11px; margin-bottom: 8px; }

  .kp-filterbar {
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
  .kp-filterbar__btn {
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
  .kp-filterbar__btn svg { width: 16px; height: 16px; }
  .kp-filterbar__btn:hover, .kp-filterbar__btn:focus { background: var(--kp-green-dark); color: #ffffff; }
  .kp-filterbar__chip {
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
  .kp-filterbar__chip b { font-weight: 600; }

  /* the sidebar becomes a slide-up drawer (moved to <body> by the script) */
  .kk-cat { display: block !important; }
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
  .kp-drawer .kk-cat__list { list-style: none; padding: 0 !important; }
  .kp-drawer .kk-cat__link { color: var(--kp-green-dark); text-decoration: none; font-weight: 500; }
  .kp-drawer__body { flex: 1 1 auto; overflow-y: auto; padding: 0 22px; overscroll-behavior: contain; }
  .kp-drawer__head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 18px 22px 14px;
    border-bottom: 1px solid #eeeeee;
  }
  .kp-drawer__head::before {
    content: "";
    position: absolute;
    left: 50%; top: 7px;
    width: 40px; height: 4px;
    margin-left: -20px;
    border-radius: 4px;
    background: #d9d9d9;
  }
  .kp-drawer__title { margin: 0; font-size: 18px; font-weight: 700; color: var(--kp-ink); }
  .kp-drawer__close {
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
  .kp-drawer__foot {
    display: flex;
    gap: 10px;
    padding: 14px 22px calc(14px + env(safe-area-inset-bottom));
    border-top: 1px solid #eeeeee;
  }
  .kp-drawer__foot a, .kp-drawer__foot button {
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
  .kp-drawer-overlay {
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

  .kk-cat .kk-cat__main { width: 100% !important; padding: 8px var(--kp-gutter) 56px !important; }
  .kp-drawer .kk-cat__link { font-size: 16px; padding: 9px 0; display: block; }
  .kp-drawer .kk-cat__list li { margin: 0 !important; }
  .kp-drawer__reset { border: 1px solid #cccccc !important; background: #ffffff !important; color: var(--kp-ink) !important; }
  .kp-drawer__done { border: 0 !important; background: var(--kp-green-dark) !important; color: #ffffff !important; }
}
@media (max-width: 767px) {
  .kk-cat ul.products { display: grid !important; grid-template-columns: 1fr 1fr; gap: 12px; }
  .kk-cat ul.products::before, .kk-cat ul.products::after { display: none !important; }
  .kk-cat ul.products li.product { width: 100% !important; margin: 0 !important; float: none !important; padding: 12px !important; border-radius: 14px !important; }
  .kk-cat ul.products li.product img { height: 130px !important; object-fit: contain; margin-bottom: 10px !important; }
  .kk-cat ul.products li.product .woocommerce-loop-product__title { font-size: 13px !important; line-height: 1.35; padding: 0 !important; }
  .kk-cat .kk-loop-code { font-size: 10px; }
  .kk-cat .kk-loop-more { font-size: 11px; }
}

/* =====================================================================
   10. SINGLE PRODUCT – mobile text sizes
   ===================================================================== */
@media (max-width: 767px) {
  .kk-sp .kk-sp__title { font-size: 26px !important; line-height: 1.15; }
  .kk-sp .kk-features li { font-size: 15px; }
  .kk-sp .kk-pinfo__title { font-size: 18px; }
  .kk-sp .kk-pinfo__row { font-size: 14px; }
}

/* =====================================================================
   11. Reduced motion
   ===================================================================== */
@media (prefers-reduced-motion: reduce) {
  .kk-cat__side, .kp-drawer-overlay, .kk-cat__side .kp-acc__panel, #masthead { transition: none !important; }
}
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
    var mqDrawer = window.matchMedia('(max-width: 1024px)');

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

    /* ---------- 2. catalogue filters: accordion (desktop) + drawer (tablet / mobile) ---------- */
    var side = document.querySelector('.kk-cat__side');
    if (!side) { return; }

    var groups = [].slice.call(side.querySelectorAll('.kk-cat__group'));
    var uid = 0;

    groups.forEach(function (g) {
        var label = g.querySelector('.kk-cat__label');
        var list  = g.querySelector('.kk-cat__list');
        if (!label || !list) { return; }

        var id  = 'kp-acc-' + (++uid);
        var act = list.querySelector('.kk-cat__link.is-active');
        var n   = list.querySelectorAll('li').length;

        var btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'kp-acc__btn';
        btn.setAttribute('aria-controls', id);
        btn.innerHTML = '<span>' + label.textContent + '<span class="kp-acc__count">' + n + '</span></span><span class="kp-acc__icon" aria-hidden="true"></span>';

        var panel = document.createElement('div');
        panel.className = 'kp-acc__panel';
        panel.id = id;
        list.parentNode.insertBefore(panel, list);
        panel.appendChild(list);
        label.parentNode.replaceChild(btn, label);

        g.classList.add('kp-acc');
        g.__kpActive = act;

        btn.addEventListener('click', function () { setOpen(g, !g.classList.contains('is-open')); });
    });

    function setOpen(g, open) {
        g.classList.toggle('is-open', open);
        var b = g.querySelector('.kp-acc__btn');
        if (b) { b.setAttribute('aria-expanded', open ? 'true' : 'false'); }
    }

    // open the group that holds the current filter, otherwise the first one
    var activeGroup = groups.filter(function (g) { return g.__kpActive; })[0];
    groups.forEach(function (g) { setOpen(g, g === (activeGroup || groups[0])); });

    /* drawer pieces */
    var body = document.createElement('div');
    body.className = 'kp-drawer__body';
    while (side.firstChild) { body.appendChild(side.firstChild); }

    var headEl = document.createElement('div');
    headEl.className = 'kp-drawer__head';
    headEl.innerHTML = '<p class="kp-drawer__title" id="kp-drawer-title">Filters</p><button type="button" class="kp-drawer__close" aria-label="Close filters">&times;</button>';

    var shopUrl = (document.querySelector('.kk-hero__crumbs a:nth-of-type(2)') || {}).href || '';
    var foot = document.createElement('div');
    foot.className = 'kp-drawer__foot';
    foot.innerHTML = (shopUrl ? '<a class="kp-drawer__reset" href="' + shopUrl + '">Show all</a>' : '') +
                     '<button type="button" class="kp-drawer__done">Done</button>';

    side.appendChild(headEl);
    side.appendChild(body);
    side.appendChild(foot);
    side.id = side.id || 'kp-filters';
    side.setAttribute('aria-labelledby', 'kp-drawer-title');

    var overlay = document.createElement('div');
    overlay.className = 'kp-drawer-overlay';
    document.body.appendChild(overlay);

    /* filter bar above the products */
    var main = document.querySelector('.kk-cat__main');
    var current = document.querySelector('.kk-cat__side .kk-cat__link.is-active');
    var bar = document.createElement('div');
    bar.className = 'kp-filterbar';
    bar.innerHTML =
        '<button type="button" class="kp-filterbar__btn" aria-controls="' + side.id + '" aria-expanded="false">' +
            '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M4 6h16M7 12h10M10 18h4"/></svg>Filter' +
        '</button>' +
        (current ? '<span class="kp-filterbar__chip">Showing: <b>' + current.textContent + '</b></span>' : '');
    if (main) { main.insertBefore(bar, main.firstChild); }
    var openBtn = bar.querySelector('.kp-filterbar__btn');

    // in drawer mode the panel lives at the end of <body>, so it sits above the header and the overlay
    var home = document.createComment('kp-filters');
    side.parentNode.insertBefore(home, side);
    function place() {
        if (mqDrawer.matches) {
            if (side.parentNode !== document.body) { document.body.appendChild(side); }
            side.classList.add('kp-drawer');
        } else {
            if (home.parentNode && side.previousSibling !== home) { home.parentNode.insertBefore(side, home.nextSibling); }
            side.classList.remove('kp-drawer');
        }
    }

    var lastFocus = null;
    function syncAria() {
        place();
        var isDrawer = mqDrawer.matches;
        if (isDrawer) {
            side.setAttribute('role', 'dialog');
            side.setAttribute('aria-modal', 'true');
            side.setAttribute('aria-hidden', html.classList.contains('kp-filters-open') ? 'false' : 'true');
        } else {
            side.removeAttribute('role');
            side.removeAttribute('aria-modal');
            side.removeAttribute('aria-hidden');
            html.classList.remove('kp-filters-open');
        }
    }
    function openDrawer() {
        lastFocus = document.activeElement;
        html.classList.add('kp-filters-open');
        openBtn.setAttribute('aria-expanded', 'true');
        syncAria();
        setTimeout(function () { var c = side.querySelector('.kp-drawer__close'); if (c) { c.focus(); } }, 60);
    }
    function closeDrawer() {
        html.classList.remove('kp-filters-open');
        openBtn.setAttribute('aria-expanded', 'false');
        syncAria();
        if (lastFocus && lastFocus.focus) { lastFocus.focus(); }
    }

    openBtn.addEventListener('click', openDrawer);
    overlay.addEventListener('click', closeDrawer);
    side.querySelector('.kp-drawer__close').addEventListener('click', closeDrawer);
    side.querySelector('.kp-drawer__done').addEventListener('click', closeDrawer);
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && html.classList.contains('kp-filters-open')) { closeDrawer(); }
    });
    if (mqDrawer.addEventListener) { mqDrawer.addEventListener('change', syncAria); }
    syncAria();
})();
</script>
    <?php
}
