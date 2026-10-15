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


# Shared blocks repeated on many pages: left out of the search text so results reflect each page's own content.
SHARED = {'Request a Quote', 'Yutong News', 'Yutong at a Glance', 'CTA', 'Related', 'Header', 'Footer'}


def search_text(nodes):
    """Headings + paragraphs of a page's own sections (for WP search; Elementor renders from its own data)."""
    import html, re
    out = []

    def walk(n):
        if n is None or n.cid in SHARED:
            return
        t = n.cfg.get('title') or n.cfg.get('paragraph')
        if isinstance(t, str):
            t = html.unescape(re.sub(r'<[^>]+>', ' ', t)).strip()
            if t and t not in out:
                out.append(t)
        for k in n.kids:
            walk(k)
    for n in nodes:
        walk(n)
    return out


def index_page(post, sections):
    """Store clean page text as post_content/excerpt so site search matches and snippets are meaningful."""
    import json as _j, html
    texts = []
    for _, fn in sections:
        texts += [t for t in search_text(fn()) if t not in texts]
    body = ''.join('<p>%s</p>' % html.escape(t) for t in texts)
    lead = next((t for t in texts if len(t) > 60), texts[0] if texts else '')
    auth = _j.load(open(os.path.expanduser('~/.claude.json')))['mcpServers']['my-blog-elementor']['headers']['Authorization']
    subprocess.run(['curl', '-sS', '-o', '/dev/null', '-X', 'POST', '-H', 'Authorization: ' + auth, '-H', 'Content-Type: application/json',
                    'https://soharon.co.uk/yutong/wp-json/wp/v2/pages/%d' % post, '--data-binary', '@-'],
                   input=_j.dumps({'content': body, 'excerpt': lead[:300]}), text=True)


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
    index_page(post, sections)
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
    if '--index-only' in sys.argv:
        for post, slug, sections in (mod.PAGES() if hasattr(mod, 'PAGES') else [(mod.POST, mod.SLUG, mod.SECTIONS)]):
            index_page(post, sections)
            print('indexed', slug)
        sys.exit(0)
    if hasattr(mod, 'PAGES'):
        for post, slug, sections in mod.PAGES():
            build(post, slug, sections, ('%s-%s' % (name, slug.split('/')[-1]))[:60] if '--shot' in sys.argv else None)
    else:
        build(mod.POST, mod.SLUG, mod.SECTIONS, name if shots else None)
