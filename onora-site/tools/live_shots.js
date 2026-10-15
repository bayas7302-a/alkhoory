// usage: node tools/live_shots.js OUT_DIR [width...]  — full-page screenshots of the live site.
const { chromium } = require('playwright');
const PAGES = [['home', ''], ['about', 'about-us/'], ['brands', 'brands/'], ['products', 'products/'], ['contact', 'contact-us/']];
(async () => {
  const [out, ...ws] = process.argv.slice(2);
  const b = await chromium.launch();
  for (const [n, u] of PAGES) for (const w of (ws.length ? ws : [1440, 390]).map(Number)) {
    const p = await b.newPage({ viewport: { width: w, height: 900 } });
    await p.goto('https://soharon.co.uk/onora/' + u + '?nocache=' + Date.now(), { waitUntil: 'networkidle', timeout: 120000 });
    await p.evaluate(async () => { for (let y = 0; y < document.body.scrollHeight; y += 400) { scrollTo(0, y); await new Promise(r => setTimeout(r, 200)); } scrollTo(0, 0); });
    await p.waitForTimeout(1500);
    const sw = await p.evaluate(() => document.documentElement.scrollWidth);
    await p.screenshot({ path: `${out}/${n}-${w}.png`, fullPage: true });
    console.log(n, w, 'scrollWidth', sw);
    await p.close();
  }
  await b.close();
})();
