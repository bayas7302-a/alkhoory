#!/usr/bin/env python3
"""Audit vertical rhythm: for every top-level section, report its background and the real gap between
the section edge and its first/last visible content (text, image, coloured box)."""
import sys
from playwright.sync_api import sync_playwright
SITE = 'https://soharon.co.uk/yutong/'
PAGES = sys.argv[1:] or ['', 'models/', 'about/', 'contact/', 'awards/', 'news/', 'library/', 'ownership-support/']
JS = r'''() => {
  const root = [...document.querySelectorAll('.yt-wrap')][0].parentElement;
  const secs = [...root.children].filter(e => e.getBoundingClientRect().height > 2);
  const bgOf = e => { const c = getComputedStyle(e); return c.backgroundImage !== 'none' ? 'img' : c.backgroundColor; };
  const visible = e => { const c = getComputedStyle(e); if (c.display === 'none' || c.visibility === 'hidden') return false;
    const d = e.closest('details'); if (d && d !== e && !d.open && !e.closest('summary')) return false;
    const r = e.getBoundingClientRect(); if (r.height < 2 || r.width < 2) return false;
    if (e.tagName === 'IMG' || e.tagName === 'VIDEO') return true;
    if ([...e.childNodes].some(n => n.nodeType === 3 && n.textContent.trim())) return true;
    if (c.backgroundColor !== 'rgba(0, 0, 0, 0)' || c.backgroundImage !== 'none' || parseFloat(c.borderTopWidth) > 0 || c.boxShadow !== 'none') return true;
    return false; };
  return secs.map(s => { const r = s.getBoundingClientRect(); let top = Infinity, bot = -Infinity;
    s.querySelectorAll('*').forEach(e => { if (!visible(e)) return; let p = e, hidden = false;
      while (p && p !== s) { const c = getComputedStyle(p); if (c.display === 'none') { hidden = true; break; } p = p.parentElement; }
      if (hidden) return; const q = e.getBoundingClientRect(); top = Math.min(top, q.top); bot = Math.max(bot, q.bottom); });
    const t = (s.querySelector('h1,h2,h3,p') || {}).textContent || '';
    return [t.trim().slice(0, 28), bgOf(s), Math.round(top - r.top), Math.round(r.bottom - bot), Math.round(r.height)]; });
}'''
with sync_playwright() as pw:
    b = pw.chromium.launch(executable_path='/opt/pw-browsers/chromium-1194/chrome-linux/chrome', args=['--no-sandbox'])
    for w in (1440, 390):
        for p in PAGES:
            pg = b.new_page(viewport={'width': w, 'height': 900})
            pg.goto(SITE + p + '?sp=1', wait_until='networkidle')
            print('==', p or 'home', w)
            for row in pg.evaluate(JS):
                print('   %-30s %-22s top %4s  bottom %4s  h %5s' % tuple(row))
            pg.close()
    b.close()
