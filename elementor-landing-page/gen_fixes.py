import json, re
M = json.load(open('idmap.json'))
NAV_ROOT = json.load(open('roots.json'))['nav'][0]
P = {p['title']: p['img'] for p in json.load(open('projects.json'))}
slab = json.load(open('typoslab_ids.json'))['ids'] if __import__('os').path.exists('typoslab_ids.json') else []


def s1(cid):
    i = M[cid]
    return '.elementor [data-interaction-id="%s"], .elementor .elementor-element.elementor-element-%s' % (i, i)


css = []
css.append("""@font-face{font-family:"TypoSlab Irregular Demo";src:url("/TypoSlab%20Irregular%20Demo.otf") format("opentype");font-display:swap;}
/* atomic elements also carry legacy .e-con (width:100%) */
:where(.e-con).e-atomic-element{--width:auto;width:auto;--container-max-width:none;min-width:0;}
html{scroll-behavior:smooth;}
/* smoother buttons */
.elementor-24951 .e-button-base, .elementor-24951 a.e-con, .elementor-24951 .wpcf7-submit, .sio-card__cta, .sio-portfolio__btn{transition:transform .45s cubic-bezier(.22,1,.36,1), box-shadow .45s cubic-bezier(.22,1,.36,1), background-color .35s ease, color .35s ease, border-color .35s ease, background-size .35s ease !important;}""")

# TypoSlab text
if slab:
    css.append(','.join(s1(c) for c in slab if c in M) +
               '{font-family:"TypoSlab Irregular Demo", var(--font-body), serif !important;font-weight:400 !important;}')

# nav: underline follows scroll (scrollspy adds .is-active)
navs = ['home', 'packages', 'work', 'services', 'process', 'faq', 'contact']
nl = ['.elementor-24951 [data-interaction-id="%s"]' % M['nav-link-' + n] for n in navs]
css.append(','.join(nl) + '{background-size:0 3px !important;font-weight:500 !important;color:var(--hl-ink) !important;}')
css.append(','.join(x + ':hover' for x in nl) + '{background-size:24px 3px !important;color:var(--hl-green) !important;}')
css.append(','.join(x + '.is-active' for x in nl) + '{background-size:24px 3px !important;font-weight:700 !important;color:var(--hl-green) !important;}')

# stats dividers: thin 1px lines
for i in (1, 2, 3):
    css.append(s1('stats-divider-%d' % i) + '{width:1px !important;min-width:1px !important;padding:0 !important;}')

# WhatsApp icons on package CTAs
wa = json.load(open('sections/packages.json'))['style']['packages-c1-cta-icon']
bg = re.search(r'background-image:(url\("data:[^)]*"\))', wa).group(1)
ids = [('packages', i) for i in range(1, 5)] + [('ecommerce', i) for i in range(1, 4)]
css.append(','.join(s1('%s-c%d-cta-icon' % x) for x in ids) +
           '{background-image:%s !important;background-size:contain !important;background-repeat:no-repeat !important;'
           'background-position:center !important;width:20px !important;height:20px !important;min-width:20px !important;padding:0 !important;}' % bg)

# services: rows are plain; hover = green card + image pops in (zoom + rotate), leaves with a tilt
imgs = [P['ZOI Creatives'], P['We NSPYR'], P['Amore Travels'], P['Hydrovits']]
E = 'cubic-bezier(.22,1,.36,1)'
for i in range(1, 5):
    rid = M['services-row-%d' % i]
    R = '.elementor .elementor-element.elementor-element-%s' % rid
    img = 'services-row-1-image' if i == 1 else 'services-row-%d-image-slot' % i
    q = lambda c: '[data-interaction-id="%s"]' % M[c]
    # Row = transparent strip. ::after = divider line, ::before = the green card, which unfolds
    # from the row's centre line (clip-path) so the radius never morphs; touching dividers fade out.
    css.append(f"""{s1('services-row-%d' % i)}{{position:relative !important;isolation:isolate;background-color:transparent !important;border:0 !important;border-radius:0 !important;box-shadow:none !important;margin-bottom:0 !important;padding:30px 36px !important;transform:none !important;}}
{R}::after{{content:"";position:absolute;left:0;right:0;bottom:0;height:1.5px;background:var(--hl-ink);transition:opacity .35s ease, transform .5s {E};pointer-events:none;}}
{R}::before{{content:"";position:absolute;inset:0;z-index:-1;background:var(--hl-green);border:2px solid var(--hl-ink);border-radius:26px;box-shadow:8px 8px 0 0 var(--hl-ink);clip-path:inset(50% -14px 50% 0 round 26px);opacity:0;transition:clip-path .6s {E}, opacity .25s ease .05s;pointer-events:none;}}
@media (hover:hover){{
{R}:hover::before{{clip-path:inset(0 -14px -14px 0 round 26px);opacity:1;transition:clip-path .6s {E}, opacity .15s ease;}}
{R}:hover::after, {R}:has(+ :hover)::after{{opacity:0;transform:scaleX(.6);}}
}}
{s1('services-row-%d-index' % i)}{{color:var(--hl-coral) !important;transition:color .35s ease !important;}}
{s1('services-row-%d-title' % i)}{{color:var(--hl-green) !important;transition:color .35s ease !important;}}
{s1('services-row-%d-desc' % i)}{{color:#56665f !important;transition:color .35s ease !important;}}
{s1('services-row-%d-arrow' % i)}{{background-color:transparent !important;border:1.5px solid var(--hl-green) !important;color:var(--hl-green) !important;box-shadow:none !important;}}
{R}:hover {q('services-row-%d-index' % i)}{{color:var(--hl-amber) !important;}}
{R}:hover {q('services-row-%d-title' % i)}{{color:var(--hl-cream) !important;}}
{R}:hover {q('services-row-%d-desc' % i)}{{color:var(--hl-muted) !important;}}
{R}:hover {q('services-row-%d-arrow' % i)}{{background-color:var(--hl-amber) !important;border-color:var(--hl-ink) !important;color:var(--hl-ink) !important;box-shadow:3px 3px 0 0 var(--hl-ink) !important;transform:rotate(-45deg);}}
{s1(img)}{{display:block !important;width:190px !important;height:128px !important;min-width:190px !important;flex-shrink:0 !important;padding:0 !important;margin:0 4px !important;background:#f2f2f2 url("{imgs[i - 1]}") center top/cover no-repeat !important;border:2px solid var(--hl-ink) !important;border-radius:14px !important;box-shadow:5px 5px 0 0 var(--hl-ink) !important;opacity:0;transform:scale(.55) rotate(-14deg) !important;transition:opacity .3s ease, transform .55s cubic-bezier(.34,1.56,.64,1) !important;pointer-events:none;}}
{R}:hover .elementor-element-{M[img]}{{opacity:1;transform:scale(1) rotate(4deg) !important;}}
@media (max-width:1024px){{{s1(img)}{{display:none !important;}}}}""")

# Mobile header: hamburger + flyout panel (built by JS from the existing nav links)
css.append("""
.hl-burger{display:none;position:relative;width:46px;height:46px;border-radius:50%;border:1.5px solid var(--hl-ink);background:var(--hl-amber);box-shadow:3px 3px 0 0 var(--hl-ink);cursor:pointer;padding:0;flex-shrink:0;transition:transform .45s cubic-bezier(.22,1,.36,1), box-shadow .45s cubic-bezier(.22,1,.36,1);}
.hl-burger span{position:absolute;left:13px;right:13px;height:2px;border-radius:2px;background:var(--hl-ink);transition:transform .45s cubic-bezier(.22,1,.36,1), opacity .2s ease, top .45s cubic-bezier(.22,1,.36,1);}
.hl-burger span:nth-child(1){top:16px}.hl-burger span:nth-child(2){top:22px}.hl-burger span:nth-child(3){top:28px}
.hl-menu-open .hl-burger span:nth-child(1){top:22px;transform:rotate(45deg)}
.hl-menu-open .hl-burger span:nth-child(2){opacity:0}
.hl-menu-open .hl-burger span:nth-child(3){top:22px;transform:rotate(-45deg)}
.hl-flyout-overlay{position:fixed;inset:0;background:rgba(14,42,37,.45);opacity:0;visibility:hidden;transition:opacity .4s ease, visibility .4s;z-index:9998;}
.hl-flyout{position:fixed;top:0;right:0;bottom:0;width:min(86vw,360px);background:var(--hl-cream);border-left:2px solid var(--hl-ink);box-shadow:-8px 0 0 0 var(--hl-green);transform:translateX(110%);transition:transform .55s cubic-bezier(.22,1,.36,1);z-index:9999;display:flex;flex-direction:column;padding:96px 28px 32px;overflow-y:auto;}
.hl-flyout a.hl-fl-link{display:flex;align-items:center;justify-content:space-between;font-family:var(--font-display),Impact,sans-serif;font-size:34px;line-height:1.1;color:var(--hl-green);text-decoration:none;text-transform:uppercase;padding:14px 0;border-bottom:1.5px solid rgba(14,42,37,.15);opacity:0;transform:translateX(24px);transition:opacity .4s ease, transform .5s cubic-bezier(.22,1,.36,1), color .3s ease;}
.hl-flyout a.hl-fl-link::after{content:"\\2192";font-family:var(--font-body),sans-serif;font-size:20px;color:var(--hl-coral);transition:transform .35s cubic-bezier(.22,1,.36,1);}
.hl-flyout a.hl-fl-link:hover, .hl-flyout a.hl-fl-link.is-active{color:var(--hl-coral);}
.hl-flyout a.hl-fl-link:hover::after{transform:translateX(4px) rotate(-45deg);}
.hl-flyout a.hl-fl-cta{margin-top:28px;display:flex;align-items:center;justify-content:center;gap:10px;padding:16px 24px;border-radius:100px;background:var(--hl-amber);color:var(--hl-ink);border:1.5px solid var(--hl-ink);box-shadow:3px 3px 0 0 var(--hl-ink);font-family:var(--font-body),sans-serif;font-weight:700;font-size:16px;text-decoration:none;opacity:0;transform:translateY(12px);transition:opacity .4s ease, transform .5s cubic-bezier(.22,1,.36,1);}
.hl-menu-open .hl-flyout{transform:translateX(0);}
.hl-menu-open .hl-flyout-overlay{opacity:1;visibility:visible;}
.hl-menu-open .hl-flyout a.hl-fl-link, .hl-menu-open .hl-flyout a.hl-fl-cta{opacity:1;transform:none;}
html.hl-menu-open{overflow:hidden;}
@media (max-width:767px){
  .hl-burger{display:block;}
  .elementor-24951 [data-id="__NAV__"] [data-interaction-id="__CTA__"]{display:none !important;}
  .elementor-24951 [data-id="__NAV__"]{z-index:10000 !important;}
}
@media (prefers-reduced-motion:reduce){.hl-flyout,.hl-flyout *,.hl-burger,.hl-burger span{transition:none !important;}}
""".replace('__NAV__', NAV_ROOT).replace('__CTA__', M['nav-cta']))

js_menu = """<script>(function(){
  function menu(){
    var nav=document.querySelector('[data-id="%s"]'); if(!nav||nav.querySelector('.hl-burger')) return;
    var inner=nav.querySelector('.elementor-element-%s')||nav;
    var links=[].slice.call(nav.querySelectorAll('.elementor-element-%s a[href^="#"], [data-interaction-id] a[href^="#"]')).filter(function(a){return a.textContent.trim();});
    if(!links.length) links=[].slice.call(nav.querySelectorAll('a[href^="#"]')).filter(function(a){var t=a.textContent.trim();return t&&!/connect/i.test(t)&&t.length<20;});
    var cta=nav.querySelector('[data-interaction-id="%s"]');
    var btn=document.createElement('button'); btn.type='button'; btn.className='hl-burger'; btn.setAttribute('aria-label','Open menu'); btn.setAttribute('aria-expanded','false'); btn.setAttribute('aria-controls','hl-flyout');
    btn.innerHTML='<span></span><span></span><span></span>';
    inner.appendChild(btn);
    var ov=document.createElement('div'); ov.className='hl-flyout-overlay';
    var fl=document.createElement('nav'); fl.className='hl-flyout'; fl.id='hl-flyout'; fl.setAttribute('aria-label','Mobile menu');
    var seen={};
    links.forEach(function(a,i){ var h=a.getAttribute('href'); if(seen[h]) return; seen[h]=1;
      var l=document.createElement('a'); l.className='hl-fl-link'; l.href=h; l.textContent=a.textContent.trim(); l.style.transitionDelay=(0.08+i*0.05)+'s'; fl.appendChild(l); });
    if(cta){ var c=document.createElement('a'); c.className='hl-fl-cta'; c.href=cta.getAttribute('href')||'#contact'; c.textContent=cta.textContent.trim(); c.style.transitionDelay=(0.12+links.length*0.05)+'s'; fl.appendChild(c); }
    document.body.appendChild(ov); document.body.appendChild(fl);
    var root=document.documentElement;
    function set(open){ root.classList.toggle('hl-menu-open',open); btn.setAttribute('aria-expanded',open?'true':'false'); btn.setAttribute('aria-label',open?'Close menu':'Open menu'); }
    btn.addEventListener('click',function(){ set(!root.classList.contains('hl-menu-open')); });
    ov.addEventListener('click',function(){ set(false); });
    fl.addEventListener('click',function(e){ if(e.target.closest('a')) set(false); });
    document.addEventListener('keydown',function(e){ if(e.key==='Escape') set(false); });
    window.addEventListener('resize',function(){ if(window.innerWidth>767) set(false); });
    // mirror scrollspy state into the flyout
    var obs=new MutationObserver(function(){ links.forEach(function(a){ var m=fl.querySelector('a.hl-fl-link[href="'+a.getAttribute('href')+'"]'); if(m) m.classList.toggle('is-active',a.classList.contains('is-active')); }); });
    links.forEach(function(a){ obs.observe(a,{attributes:true,attributeFilter:['class']}); });
  }
  if(document.readyState==='loading')document.addEventListener('DOMContentLoaded',menu);else menu();
})();</script>""" % (NAV_ROOT, M['nav-inner'], M['nav-menu'], M['nav-cta'])

js = """<script>(function(){
  function spy(){
    var nav=document.querySelector('[data-id="%s"]'); if(!nav) return;
    var map=[].slice.call(nav.querySelectorAll('a[href^="#"]')).map(function(a){return {a:a,t:document.getElementById(a.getAttribute('href').slice(1))};}).filter(function(x){return x.t;});
    if(!map.length) return;
    function upd(){
      var y=window.scrollY+160, cur=map[0];
      map.forEach(function(x){ if(x.t.getBoundingClientRect().top+window.scrollY<=y) cur=x; });
      if(window.innerHeight+window.scrollY>=document.documentElement.scrollHeight-4) cur=map[map.length-1];
      map.forEach(function(x){ if(x.a.textContent.trim()) x.a.classList.toggle('is-active',x===cur); });
    }
    var r; window.addEventListener('scroll',function(){cancelAnimationFrame(r);r=requestAnimationFrame(upd);},{passive:true}); upd();
  }
  if(document.readyState==='loading')document.addEventListener('DOMContentLoaded',spy);else spy();
})();</script>""" % NAV_ROOT

open('page_fixes.html', 'w').write('<style id="hl-page-fixes">' + '\n'.join(css) + '</style>' + js + js_menu)
print('slab ids:', len(slab), 'bytes:', len(open('page_fixes.html').read()))
