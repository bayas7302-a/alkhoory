#!/usr/bin/env python3
"""Smoke-check every live page: HTTP OK, no horizontal overflow, images loaded, form present, internal links resolve."""
import sys, subprocess
from playwright.sync_api import sync_playwright
SITE = 'https://soharon.co.uk/yutong'
PAGES = {'home': '/', 'models': '/models/', 'about': '/about/', 'contact': '/contact/', 'awards': '/awards/',
         'news': '/news/', 'library': '/library/', 'ownership': '/ownership-support/'}
links = set()
with sync_playwright() as pw:
    b = pw.chromium.launch(executable_path='/opt/pw-browsers/chromium-1194/chrome-linux/chrome', args=['--no-sandbox'])
    for name, path in PAGES.items():
        for w in (1440, 820, 390):
            pg = b.new_page(viewport={'width': w, 'height': 900})
            r = pg.goto(SITE + path + '?chk=1', wait_until='networkidle', timeout=90000)
            pg.evaluate('''async () => { for (let y = 0; y < document.body.scrollHeight; y += 500) { window.scrollTo(0, y); await new Promise(r => setTimeout(r, 120)); } }''')
            pg.wait_for_timeout(1500)
            d = pg.evaluate('''() => ({
                sw: document.documentElement.scrollWidth, h: document.body.scrollHeight,
                broken: [...document.images].filter(i => !i.complete || !i.naturalWidth).map(i => i.src.split('/').pop()),
                form: document.querySelectorAll('form.wpcf7-form').length,
                wide: [...document.querySelectorAll('body *')].filter(e => e.getBoundingClientRect().right > window.innerWidth + 1 && getComputedStyle(e).position !== 'fixed').slice(0, 3).map(e => (e.className || e.tagName).toString().slice(0, 40)),
                links: [...document.querySelectorAll('a[href]')].map(a => a.href) })''')
            links.update(l.split('#')[0] for l in d['links'] if l.startswith(SITE))
            flag = 'OK ' if r.status == 200 and d['sw'] <= w and not d['broken'] and d['form'] else 'BAD'
            print(flag, name, w, 'status', r.status, 'h', d['h'], 'overflow', d['sw'] - w, 'broken', d['broken'][:3], 'forms', d['form'], d['wide'] if d['sw'] > w else '')
            pg.close()
    b.close()
for l in sorted(links):
    code = subprocess.run(['curl', '-s', '-o', '/dev/null', '-w', '%{http_code}', '-L', l], capture_output=True, text=True).stdout
    if code != '200':
        print('LINK', code, l)
print('checked', len(links), 'internal links')
