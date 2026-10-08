import { chromium } from '/opt/node22/lib/node_modules/playwright/index.mjs';
import fs from 'fs';
const [name, url] = process.argv.slice(2);
const VP = {desktop:[1440,900], tablet:[1000,1300], mobile:[390,844]};
const PROPS = ['font-family','font-size','font-weight','line-height','letter-spacing','text-transform','font-style','text-decoration-line','color','background-color','background-image','margin-top','margin-right','margin-bottom','margin-left','padding-top','padding-right','padding-bottom','padding-left','border-top-left-radius','border-top-right-radius','border-bottom-right-radius','border-bottom-left-radius','border-top-width','border-right-width','border-bottom-width','border-left-width','border-top-style','border-top-color','box-shadow','text-align','display','flex-direction','flex-wrap','justify-content','align-items','align-self','row-gap','column-gap','flex-grow','flex-shrink','flex-basis','position','top','left','right','bottom','z-index','opacity','overflow-x','overflow-y','order','object-fit','max-width','min-height'];
const SEL = {
  hub_fancy_heading: {t:'.ld-fh-element', wrap:'.ld-fancy-heading'},
  ld_icon_box: {box:'.iconbox', iconwrap:'.iconbox-icon-wrap', icon:'.iconbox-icon-container', img:'.iconbox-icon-container > img', svg:'.iconbox-icon-container svg', h:'.lqd-iconbox-heading', p:'.iconbox p', label:'.iconbox-label', contents:'.contents', link:'a'},
  ld_button: {a:'a.btn', icon:'.btn-icon', svg:'.btn-icon svg', txt:'.btn-txt'},
  ld_fancy_image: {img:'img', fig:'figure'},
  ld_counter: {num:'.lqd-counter-element', text:'.lqd-counter-text', box:'.lqd-counter'},
  ld_cf722: {input:'input[type=text]', textarea:'textarea', label:'label', submit:'[type=submit]', select:'select'},
  image: {img:'img'},
  'icon-list': {ul:'.elementor-icon-list-items', li:'.elementor-icon-list-item', icon:'.elementor-icon-list-icon', svg:'.elementor-icon-list-icon svg', text:'.elementor-icon-list-text'},
  divider: {sep:'.elementor-divider-separator', div:'.elementor-divider'},
  icon: {i:'.elementor-icon', svg:'svg'},
  'social-icons': {a:'.elementor-social-icon', svg:'.elementor-social-icon svg', wrap:'.elementor-social-icons-wrapper'},
  ld_carousel: {items:'.carousel-items', item:'.carousel-item'},
};
const collect = ({PROPS, SEL}) => {
  const cs = el => { if(!el) return null; const s=getComputedStyle(el); const o={}; for(const p of PROPS) o[p]=s.getPropertyValue(p); const r=el.getBoundingClientRect(); o._w=Math.round(r.width); o._h=Math.round(r.height); return o; };
  const out = {};
  for (const el of document.querySelectorAll('[data-element_type]')) {
    const id = el.dataset.id; if (!id || out[id]) continue;
    const d = {self: cs(el)};
    if (el.dataset.element_type === 'container') { d.inner = cs(el.querySelector(':scope > .e-con-inner')); }
    else {
      const t = (el.dataset.widget_type||'').split('.')[0];
      d.wc = cs(el.querySelector(':scope > .elementor-widget-container'));
      const m = SEL[t]||{}; for (const k in m) { const q=el.querySelector(m[k]); d[k]=cs(q); }
      if (t==='hub_fancy_heading') d.spans=[...el.querySelectorAll('.lqd-adv-txt-item')].map(s=>({cls:[...s.classList].find(x=>x.startsWith('elementor-repeater-item-')), color:getComputedStyle(s).color, fw:getComputedStyle(s).fontWeight, ff:getComputedStyle(s).fontFamily, img: s.querySelector('img')?cs(s.querySelector('img')):null}));
      if (t==='icon-list') d.items=[...el.querySelectorAll('.elementor-icon-list-item')].map(li=>({icon:cs(li.querySelector('.elementor-icon-list-icon')), text:cs(li.querySelector('.elementor-icon-list-text')), li:cs(li)}));
    }
    out[id]=d;
  }
  out.__body = cs(document.body);
  return out;
};
const svgs = () => {
  // inline computed paint on every svg inside widgets, for rasterising
  const res = [];
  document.querySelectorAll('[data-widget_type] svg').forEach((svg,i)=>{
    const w = svg.closest('[data-widget_type]'); const r=svg.getBoundingClientRect();
    const clone = svg.cloneNode(true);
    const src=[svg,...svg.querySelectorAll('*')], dst=[clone,...clone.querySelectorAll('*')];
    src.forEach((s,k)=>{ const c=getComputedStyle(s); for (const p of ['fill','stroke','stroke-width','opacity','fill-opacity','stroke-opacity','color']) { const v=c.getPropertyValue(p); if(v) dst[k].style.setProperty(p,v); } });
    clone.setAttribute('width', r.width); clone.setAttribute('height', r.height);
    svg.setAttribute('data-svgidx', i);
    res.push({idx:i, wid:w.dataset.id, w:r.width, h:r.height, html: clone.outerHTML});
  });
  return res;
};
const b = await chromium.launch({executablePath:'/opt/pw-browsers/chromium'});
const res = {};
for (const [dev,[w,h]] of Object.entries(VP)) {
  const ctx = await b.newContext({viewport:{width:w,height:h}});
  const p = await ctx.newPage();
  await p.goto(url,{waitUntil:'networkidle',timeout:120000}).catch(e=>console.log('ERR',name,dev,e.message));
  await p.evaluate(async()=>{for(let y=0;y<document.body.scrollHeight;y+=500){window.scrollTo(0,y);await new Promise(r=>setTimeout(r,100));} window.scrollTo(0,0);});
  await p.waitForTimeout(3000);
  await p.waitForFunction(()=>{const e=document.querySelector('.e-con'); return e && getComputedStyle(e).display==='flex';},{timeout:60000}).catch(()=>console.log('CSS NOT READY',name,dev));
  res[dev] = await p.evaluate(collect, {PROPS, SEL});
  if (dev==='desktop') {
    res.svgs = await p.evaluate(svgs);
    const hov = {};
    for (const sel of ['.elementor-widget-ld_button a.btn','.elementor-widget-ld_icon_box']) {
      for (const el of await p.$$(sel)) {
        const id = await el.evaluate(e=>e.closest('[data-widget_type]').dataset.id);
        if (hov[id]) continue;
        const vis = await el.isVisible(); if (!vis) continue;
        try { await el.scrollIntoViewIfNeeded({timeout:3000}); await el.hover({timeout:3000}); await p.waitForTimeout(800);
          hov[id] = await el.evaluate(e=>{const t=e.matches('a')?e:e.querySelector('.elementor-widget-container'); const s=getComputedStyle(t); return {color:s.color,bg:s.backgroundColor,border:s.borderTopColor,shadow:s.boxShadow, wtransform:getComputedStyle(e).transform};});
          await p.mouse.move(1,1); await p.waitForTimeout(300);
        } catch(e) {}
      }
    }
    res.hover = hov;
  }
  await ctx.close();
}
fs.writeFileSync(`computed/${name}.json`, JSON.stringify(res));
console.log('done', name, Object.keys(res.desktop).length, res.svgs.length);
await b.close();
