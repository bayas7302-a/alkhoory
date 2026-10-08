# Liquid/Hub → free Elementor (V4) migration tools

Used to rebuild the 9 AIS pages without the Hub theme, Liquid widgets or Elementor Pro.

1. `collect2.mjs <name> <url>` – headless Chromium capture of computed styles (desktop / tablet 1000px / mobile 390px) + inline SVG icons.
2. `raster.mjs` – turns the needed SVG icons into transparent PNGs (uploaded to the Media Library; ids in `uploaded-icons.json`).
3. `convert.py` – reads the saved page HTML + computed styles and produces Elementor `build-composition` payloads using only free V4 elements
   (e-flexbox, e-heading, e-paragraph, e-image, e-button, e-div-block).
4. `build.py <name> <source-page-id> <target-post-id>` – pushes the payload through the site's Elementor MCP endpoint.
   Afterwards call `elementor-publish-document` for published pages and clear the Elementor CSS cache
   (`DELETE /wp-json/elementor/v1/cache`).

Credentials are read from the `WP_AUTH` environment variable (`Basic base64(user:application-password)`) and are never stored.
The previous Liquid versions of every page remain available in each page's Elementor revision history.
