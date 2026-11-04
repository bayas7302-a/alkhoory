# Kress Kleen – home hero (scroll video) smooth fix

Site: https://soharon.co.uk/off-29/ (home page, ID 43). The hero is the `kk-sh` scroll-video section, printed by a
shortcode in the child theme (Elementor Shortcode widget `c5fcd7b` in container `189373e`).

## What was wrong

| Problem | Cause |
|---|---|
| Product cards ("tabs") didn't match the video | `data-times` was 0.5–1.9 s behind the real scene changes (e.g. Sponges card at 14.9 s, sponge visible from 13 s). The card was also picked from the *requested* scroll time, not the frame on screen, so it ran ahead of the video while seeking. |
| Video felt choppy | The video was moved by a `currentTime` seek on every animation frame, and the smoothing was frame-rate dependent. |
| Cards felt slow | 0.2 s entrance delay plus a 0.30–0.54 s text stagger. |

The MP4 itself is fine (848×480, keyframe every 3 frames, faststart), so it doesn't need re-encoding.

## What `kk-sh-smooth.html` does

- New card start times, measured from the video's scene cuts:
  `[0, 1.9, 4.1, 9.1, 11.0, 13.0, 16.6, 19.7, 22.6, 25.7, 28.8]`
  (Broom, Dust Pans, Mops, Spin Buckets, Cleaning Accessories, Sponges, Cloths, Wipers, Cleaning Brushes, Scourers, Car Accessories).
- Cards, the dots and the poster fade all follow the frame that is actually on screen. Hysteresis stops a card from flickering at a boundary.
- **Slower video on scroll:** one second of video now takes 24% of the screen height to scroll (was 14%), about 40% slower. Change `PERVH` in the script to tune it (higher = slower).
- **GSAP ScrollTrigger** (free, loaded from cdnjs) smooths the scroll with `scrub: 1.2`, so the video glides to where you stopped instead of jumping. If GSAP fails to load, the script uses its own smoothing.
- Scrolling forward *plays* the video, with its speed following the scroll but never above 2× (`MAXRATE`), instead of seeking every frame. Scrolling backward or jumping far still seeks. If the browser blocks `play()` (for example, iOS Low Power Mode), it falls back to seeking.
- Faster card hand-over (0.1 s delay, 0.14–0.32 s stagger).
- Clicking a dot lands in the middle of that product's scene.

Local test (Chromium, the same scroll script for both versions):

| | before | after (GSAP) |
|---|---|---|
| video seconds per 100 px of scroll | 0.48 | 0.28 |
| time the wrong card is shown | 41 % | 0 % |
| unevenness of the video speed (lower is better) | 1.99 | 0.29 |
| jumps of more than 0.25 s between frames | 18 | 1 |
| worst gap between frames (p99) | 117 ms | 67 ms |

## How to apply

1. WP Admin → Appearance → Theme File Editor → child theme. Find the hero shortcode code. Search for `kk-sh-js`; it is usually in `functions.php`.
2. Replace the whole `<script id="kk-sh-js"> … </script>` block with the contents of `kk-sh-smooth.html` (it includes the two GSAP `<script src>` tags and a small `<style>` block).
3. Optional: also change the shortcode's `data-times` to the list above. The script already carries the new times and its own speed (`PERVH`).
4. Clear the LiteSpeed cache and check the home page.

The Elementor MCP can't edit theme PHP or V3 Shortcode/HTML widgets, and V4 has no HTML widget, so this can't be applied over MCP.
