// usage: node tools/shot.js POST_ID OUT_PREFIX [width=1440]
// Renders an Elementor preview snapshot of the post and saves a full-page PNG.
const { chromium } = require('playwright');
const { execFileSync } = require('child_process');
const path = require('path');
(async () => {
  const [pid, out, w] = process.argv.slice(2);
  // Elementor keeps stale per-post CSS after MCP saves; clear its CSS cache first.
  execFileSync('python3', ['-c', "import urllib.request,sys;sys.path.insert(0,'"+__dirname+"');from mcp import auth_header;urllib.request.urlopen(urllib.request.Request('https://soharon.co.uk/onora/wp-json/elementor/v1/cache',method='DELETE',headers={'Authorization':auth_header()}))"]);
  const r = JSON.parse(execFileSync(path.join(__dirname, 'mcp.py'), ['elementor-create-preview-link', JSON.stringify({ post_id: +pid })]).toString());
  const url = r.preview_url || r.url || r.link;
  if (!url) { console.error(JSON.stringify(r)); process.exit(1); }
  const browser = await chromium.launch();
  const page = await browser.newPage({ viewport: { width: +(w || 1440), height: 900 } });
  await page.goto(url, { waitUntil: 'networkidle', timeout: 120000 });
  await page.evaluate(async () => { for (let y = 0; y < document.body.scrollHeight; y += 400) { window.scrollTo(0, y); await new Promise(r => setTimeout(r, 250)); } window.scrollTo(0, 0); });
  await page.waitForTimeout(2500);
  await page.screenshot({ path: `${out}-${w || 1440}.png`, fullPage: true });
  console.log(`${out}-${w || 1440}.png`, url.split('?')[0]);
  await browser.close();
})();
