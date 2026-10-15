#!/usr/bin/env python3
"""Rebuild a page end to end: generate payloads, send each section, fill slots, screenshot.

usage: run.py <page module name> [--no-shot] [--only home-3]
"""
import sys, os, subprocess, importlib.util
HERE = os.path.dirname(os.path.abspath(__file__))
WORK = os.environ.get('YT_WORK', '/tmp/claude-0/-home-user-alkhoory/69817a7b-90b7-525f-a45d-003b87e8786d/scratchpad')
name = sys.argv[1]
spec = importlib.util.spec_from_file_location(name, os.path.join(HERE, '..', 'pages', name + '.py'))
mod = importlib.util.module_from_spec(spec)
sys.path.insert(0, HERE)
spec.loader.exec_module(mod)
from lib import payload, save
out = os.path.join(WORK, 'payloads')
os.makedirs(out, exist_ok=True)
for i, (sec, fn) in enumerate(mod.SECTIONS):
    path = save(os.path.join(out, sec + '.json'), payload(mod.POST, fn(), parent='document', mode='replace_children' if i == 0 else None))
    r = subprocess.run([sys.executable, os.path.join(HERE, 'send.py'), path], capture_output=True, text=True)
    print('==', sec, r.stdout.strip()[:1500])
    if r.returncode:
        sys.exit('stopped at ' + sec)
# publish re-saves from Elementor's own copy, so it must come BEFORE the REST slot injection
subprocess.run([os.path.join(WORK, 'call.sh'), 'elementor-publish-document', '{"post_id": %d}' % mod.POST], capture_output=True)
subprocess.run([sys.executable, os.path.join(HERE, 'finalize.py'), str(mod.POST)])
if '--no-shot' not in sys.argv:
    live = 'https://soharon.co.uk/yutong/%s/?v=%d' % (mod.SLUG, os.getpid())
    for w in (1440, 390):
        subprocess.run([sys.executable, os.path.join(HERE, 'shot.py'), live, os.path.join(WORK, 'shots', '%s-%s.png' % (name, w)), str(w), '--split', '6' if w > 1000 else '3'])
