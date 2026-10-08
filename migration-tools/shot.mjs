import { chromium } from '/opt/node22/lib/node_modules/playwright/index.mjs';
const [url, out, w] = process.argv.slice(2);
const b = await chromium.launch({executablePath:'/opt/pw-browsers/chromium'});
const p = await b.newPage({viewport:{width:+w||1440,height:900}});
await p.goto(url,{waitUntil:'networkidle',timeout:120000}).catch(()=>{});
await p.evaluate(async()=>{for(let y=0;y<document.body.scrollHeight;y+=500){window.scrollTo(0,y);await new Promise(r=>setTimeout(r,80));} window.scrollTo(0,0);});
await p.evaluate(async()=>{for(const i of document.images){i.loading='eager';} await Promise.all([...document.images].map(i=>i.complete?0:new Promise(r=>{i.onload=i.onerror=r; setTimeout(r,8000);})));}); await p.waitForTimeout(2500);
await p.screenshot({path:out, fullPage:true});
await b.close();
