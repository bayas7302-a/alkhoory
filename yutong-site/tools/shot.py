#!/usr/bin/env python3
"""Screenshot a page's current Elementor draft via a fresh preview link.

usage: shot.py <post_id> <out.png> [width=1440] [--split N]
"""
import sys, json, subprocess, os
from playwright.sync_api import sync_playwright
W = os.environ.get('YT_WORK', '/tmp/claude-0/-home-user-alkhoory/69817a7b-90b7-525f-a45d-003b87e8786d/scratchpad')
pid, out = sys.argv[1], sys.argv[2]
width = int(sys.argv[3]) if len(sys.argv) > 3 and sys.argv[3].isdigit() else 1440
split = int(sys.argv[sys.argv.index('--split') + 1]) if '--split' in sys.argv else 0
if pid.startswith('http'):
    url = pid
else:
    r = subprocess.run([W + '/call.sh', 'elementor-create-preview-link', json.dumps({'post_id': int(pid)})], capture_output=True, text=True).stdout
    url = json.loads(r)['url']
with sync_playwright() as pw:
    b = pw.chromium.launch(executable_path='/opt/pw-browsers/chromium-1194/chrome-linux/chrome', args=['--no-sandbox'])
    pg = b.new_page(viewport={'width': width, 'height': 900}, ignore_https_errors=True)
    pg.goto(url, wait_until='networkidle', timeout=90000)
    pg.evaluate('''async () => { for (let y = 0; y < document.body.scrollHeight; y += 400) { window.scrollTo(0, y); await new Promise(r => setTimeout(r, 250)); } window.scrollTo(0, 0); }''')
    pg.wait_for_timeout(3000)
    pg.screenshot(path=out, full_page=True)
    b.close()
from PIL import Image
im = Image.open(out)
print(out, im.size)
if split:
    w, h = im.size
    step = -(-h // split)
    for i in range(split):
        part = im.crop((0, i * step, w, min(h, (i + 1) * step)))
        if w > 1000:
            part = part.resize((w // 2, part.size[1] // 2))
        fn = out.replace('.png', '_%d.png' % i)
        part.save(fn)
        print(fn)
