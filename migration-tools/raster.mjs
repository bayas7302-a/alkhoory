import { chromium } from '/opt/node22/lib/node_modules/playwright/index.mjs';
import fs from 'fs';
const need = JSON.parse(fs.readFileSync('svgs_needed.json'));
const b = await chromium.launch({executablePath:'/opt/pw-browsers/chromium'});
const p = await b.newPage({deviceScaleFactor:3});
for (const [h,s] of Object.entries(need)) {
  const w=Math.max(1,Math.round(s.w)), hh=Math.max(1,Math.round(s.h));
  const svg = s.html.replace(/^<svg([^>]*)>/, (m,a)=>'<svg'+a.replace(/\s(width|height)="[^"]*"/g,'')+` width="${w}" height="${hh}">`);
  await p.setContent(`<html><body style="margin:0;background:transparent"><div id="x" style="display:inline-block;line-height:0;width:${w}px;height:${hh}px">${svg}</div></body></html>`);
  await p.locator('#x').screenshot({path:`icons/ais-icon-${h}.png`, omitBackground:true});
}
await b.close(); console.log('ok');
