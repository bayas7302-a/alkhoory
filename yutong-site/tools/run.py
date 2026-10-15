#!/usr/bin/env python3
"""Rebuild a page end to end: generate payloads, send each section, publish, fill slots, screenshot.

usage: run.py <page module name> [--no-shot]

A page module defines either POST / SLUG / SECTIONS (one page) or PAGES() returning
[(post_id, slug, sections), ...] (many generated pages, e.g. the news/award articles).
"""
import sys, os, subprocess, importlib.util
HERE = os.path.dirname(os.path.abspath(__file__))
WORK = os.environ.get('YT_WORK', '/tmp/claude-0/-home-user-alkhoory/69817a7b-90b7-525f-a45d-003b87e8786d/scratchpad')
sys.path.insert(0, HERE)
from lib import payload, save


def build(post, slug, sections, shot_name=None):
    out = os.path.join(WORK, 'payloads')
    os.makedirs(out, exist_ok=True)
    for i, (sec, fn) in enumerate(sections):
        path = save(os.path.join(out, sec + '.json'), payload(post, fn(), parent='document', mode='replace_children' if i == 0 else None))
        r = subprocess.run([sys.executable, os.path.join(HERE, 'send.py'), path], capture_output=True, text=True)
        print('==', sec, r.stdout.strip()[:1500])
        if r.returncode:
            sys.exit('stopped at ' + sec)
    # publish re-saves from Elementor's own copy, so it must come BEFORE the REST slot injection
    subprocess.run([os.path.join(WORK, 'call.sh'), 'elementor-publish-document', '{"post_id": %d}' % post], capture_output=True)
    subprocess.run([sys.executable, os.path.join(HERE, 'finalize.py'), str(post)])
    if shot_name:
        live = 'https://soharon.co.uk/yutong/%s/?v=%d' % (slug, os.getpid())
        for w in (1440, 390):
            subprocess.run([sys.executable, os.path.join(HERE, 'shot.py'), live, os.path.join(WORK, 'shots', '%s-%s.png' % (shot_name, w)), str(w), '--split', '6' if w > 1000 else '3'])


if __name__ == '__main__':
    name = sys.argv[1]
    spec = importlib.util.spec_from_file_location(name, os.path.join(HERE, '..', 'pages', name + '.py'))
    mod = importlib.util.module_from_spec(spec)
    spec.loader.exec_module(mod)
    shots = '--no-shot' not in sys.argv
    if hasattr(mod, 'PAGES'):
        for post, slug, sections in mod.PAGES():
            build(post, slug, sections, ('%s-%s' % (name, slug.split('/')[-1]))[:60] if '--shot' in sys.argv else None)
    else:
        build(mod.POST, mod.SLUG, mod.SECTIONS, name if shots else None)
