#!/usr/bin/env python3
"""Flag uneven vertical spacing between top-level sections (uses spacing.py's measurement).
Same background neighbours: bottom(A) + top(B) must equal the standard gap.
Different backgrounds: a section's own top and bottom space must match."""
import sys, importlib.util, os
from playwright.sync_api import sync_playwright
spec = importlib.util.spec_from_file_location('sp', os.path.join(os.path.dirname(__file__), 'spacing.py'))
src = open(spec.origin).read()
JS = src[src.index("JS = r'''") + 9: src.index("'''", src.index("JS = r'''") + 9)]
STD = {1440: 110, 820: 80, 390: 64}
TOL = 6
bad = 0
with sync_playwright() as pw:
    b = pw.chromium.launch(executable_path='/opt/pw-browsers/chromium-1194/chrome-linux/chrome', args=['--no-sandbox'])
    for p in ['', 'models/', 'about/', 'contact/', 'awards/', 'news/', 'library/', 'ownership-support/', 'request-a-quote/', 'ownership-support/service-centres/', 'ownership-support/spare-parts/', 'ownership-support/brochures/', 'privacy-policy/', 'terms-of-use/']:
        for w in STD:
            pg = b.new_page(viewport={'width': w, 'height': 900})
            pg.goto('https://soharon.co.uk/yutong/' + p + '?rh=1', wait_until='networkidle')
            rows = [r for r in pg.evaluate(JS) if r[4] > 100][1:]  # skip header bar + hero
            for i, (name, bgc, top, bot, h) in enumerate(rows):
                if name.startswith('Official Yutong distributor') or bgc == 'img':
                    continue  # footer has its own spacing; full-bleed photo bands are composed by design
                prev = rows[i - 1] if i else None
                nxt = rows[i + 1] if i + 1 < len(rows) else None
                if prev and prev[1] == bgc:
                    gap = prev[3] + top
                    if abs(gap - STD[w]) > TOL:
                        print('GAP  %-18s %4d  %-28s -> %-28s %d (want %d)' % (p or 'home', w, prev[0], name, gap, STD[w])); bad += 1
                elif top < STD[w] - TOL or top > STD[w] + 80:
                    print('TOP  %-18s %4d  %-28s top %d' % (p or 'home', w, name, top)); bad += 1
                if not (nxt and nxt[1] == bgc) and not (nxt and nxt[0].startswith('Official')) or (nxt and nxt[0].startswith('Official') and nxt[1] != bgc):
                    if nxt and nxt[1] != bgc and abs(bot - STD[w]) > TOL and abs(bot - top) > TOL:
                        print('BOT  %-18s %4d  %-28s bottom %d top %d' % (p or 'home', w, name, bot, top)); bad += 1
            pg.close()
    b.close()
print('issues:', bad)
