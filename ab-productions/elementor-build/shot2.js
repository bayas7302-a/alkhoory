// Preview with the theme simulated: header/footer + shortcodes rendered by the PHP harness.
const { chromium } = require('playwright');
const fs = require('fs'); const cp = require('child_process'); const path = require('path');
const T = path.resolve(__dirname, '../abproductions-child');
function dataUrl(p) { const ext = path.extname(p).slice(1).replace('jpg', 'jpeg'); return 'data:image/' + ext + ';base64,' + fs.readFileSync(p).toString('base64'); }
function fix(html) {
  return html.replace(/file:\/\/[^"')\s]+/g, m => fs.existsSync(m.slice(7)) ? dataUrl(m.slice(7)) : m)
             .replace(/IMGBASE\/([^"]+)/g, (m, f) => dataUrl(T + '/assets/img/projects/' + f));
}
(async () => {
  const [url, out, width, actions] = process.argv.slice(2);
  const b = await chromium.launch();
  const pg = await b.newPage({ viewport: { width: parseInt(width || '1440'), height: 900 } });
  await pg.goto(url, { waitUntil: 'networkidle', timeout: 90000 });
  await pg.evaluate(() => document.querySelectorAll('p').forEach(e => { if (/Contact form not found/.test(e.textContent)) e.textContent = '[contact-form-7]'; }));
  const codes = await pg.evaluate(() => Array.from(document.querySelectorAll('p,span,h1,h2,h3')).map(e => e.textContent.trim()).filter(t => /^\[(ab_|contact-form-7)/.test(t)));
  const r = JSON.parse(cp.execFileSync('php', [__dirname + '/harness/render.php', JSON.stringify(codes)]).toString());
  for (const k of Object.keys(r)) r[k] = fix(r[k]);
  await pg.evaluate((r) => {
    document.querySelectorAll('p,span,h1,h2,h3').forEach(e => {
      const t = e.textContent.trim();
      if (r[t] !== undefined) { const d = document.createElement('div'); d.className = 'abp-sc ' + e.className.split(' ').filter(c => !/^e-(paragraph|heading)-base$|^e-default-/.test(c)).join(' '); d.innerHTML = r[t]; e.replaceWith(d); }
    });
    const oh = document.querySelector('#site-header, header.site-header'); if (oh) oh.remove();
    const of = document.querySelector('#site-footer, footer.site-footer'); if (of) of.remove();
    document.body.insertAdjacentHTML('afterbegin', r.__header);
    document.body.insertAdjacentHTML('beforeend', r.__footer + r.__lightbox);
    document.body.classList.add('abp-site');
  }, r);
  const f = fs.readFileSync(T + '/assets/fonts/BigShouldersDisplay-latin.woff2').toString('base64');
  await pg.addStyleTag({ url: 'https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap' });
  await pg.addStyleTag({ content: '@font-face{font-family:"Dharma Gothic E";src:url(data:font/woff2;base64,' + f + ') format("woff2");font-weight:100 900;}' });
  await pg.addStyleTag({ content: fs.readFileSync(T + '/assets/css/main.css', 'utf8') });
  await pg.addScriptTag({ content: fs.readFileSync(T + '/assets/js/main.js', 'utf8') });
  for (let y = 0; y < 14000; y += 600) { await pg.evaluate(v => window.scrollTo(0, v), y); await pg.waitForTimeout(100); }
  await pg.evaluate(() => window.scrollTo(0, 0));
  await pg.waitForTimeout(800);
  if (actions) { await eval('(async () => {' + actions + '})()'); }
  else await pg.screenshot({ path: out, fullPage: true });
  await b.close();
})();
