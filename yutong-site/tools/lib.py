"""Tiny builder for elementor-build-composition payloads.

    from lib import *
    root = flex('Hero', 'padding: 4rem;', [
        h('Hero Title', 'Hello', 'h1', 'font-size: 3rem;'),
        btn('Hero CTA', 'Explore', '/models/'),
    ])
    payload(post_id, [root])   # -> dict ready for send.py

Every node gets a unique configuration-id; duplicates are suffixed automatically
so shared parts (header, footer) can be reused freely within one payload.
"""
import json, os

WORK = os.environ.get('YT_WORK', '/tmp/claude-0/-home-user-alkhoory/69817a7b-90b7-525f-a45d-003b87e8786d/scratchpad')
SITE = 'https://soharon.co.uk/yutong'

# page URLs (slugs set via REST); home is the static front page
URL = {
    'home': SITE + '/',
    'models': SITE + '/models/',
    'technologies': SITE + '/models/#technology',
    'about': SITE + '/about/',
    'news': SITE + '/news/',
    'contact': SITE + '/contact/',
    'awards': SITE + '/awards/',
    'library': SITE + '/library/',
    'ownership': SITE + '/ownership-support/',
    'quote': '#request-a-quote',
}

FD = 'font-family: var(--yt-display);'
FB = 'font-family: var(--yt-body);'


class Node:
    def __init__(self, tag, cid, cfg=None, css='', kids=None, cls=None):
        self.tag, self.cid, self.cfg, self.css, self.kids = tag, cid, cfg or {}, css or '', kids or []
        self.cls = list(cls or [])

    def c(self, *labels):
        """Attach global classes (raw CSS escape hatch: nesting, white-space, hover on children...)."""
        self.cls += labels
        return self


def _media(ids):
    """Uploaded asset lookup: assets/map.json keyed by name."""
    m = json.load(open(os.path.join(WORK, 'assets', 'map.json')))
    return {n: v for v in m.values() for n in v.get('names', [v['name']])}


_ASSETS = None


def asset(name):
    global _ASSETS
    if _ASSETS is None or name not in _ASSETS:
        _ASSETS = _media(None)
    return _ASSETS[name]


def link(url, blank=False):
    return {'destination': url, 'isTargetBlank': blank, 'tag': 'a'}


def flex(cid, css='', kids=None, href=None, tag=None):
    cfg = {}
    if href:
        cfg['link'] = link(href)
    if tag:
        cfg['tag'] = tag
    return Node('e-flexbox', cid, cfg, css, kids)


def div(cid, css='', kids=None, href=None, tag=None):
    cfg = {}
    if href:
        cfg['link'] = link(href)
    if tag:
        cfg['tag'] = tag
    return Node('e-div-block', cid, cfg, css, kids)


def grid(cid, css='', kids=None):
    return Node('e-grid', cid, {}, css, kids)


def h(cid, text, tag='h2', css='', href=None):
    cfg = {'title': text, 'tag': tag}
    if href:
        cfg['link'] = link(href)
    return Node('e-heading', cid, cfg, css)


def p(cid, text, css='', tag=None, href=None):
    cfg = {'paragraph': text}
    if tag:
        cfg['tag'] = tag
    if href:
        cfg['link'] = link(href)
    return Node('e-paragraph', cid, cfg, css)


def img(cid, name, css='', href=None):
    """Image from the uploaded asset map. Width defaults to the asset's natural width."""
    a = asset(name)
    cfg = {'image': {'src': {'id': a['id']}, 'size': 'full'}}
    if href:
        cfg['link'] = link(href)
    base = 'display: block; max-width: 100%; height: auto; '
    if 'width' not in css:
        base += 'width: %dpx; ' % a['w']
    return Node('e-image', cid, cfg, base + css)


def bg(name):
    return "url('%s')" % asset(name)['url']


def payload(post_id, roots, parent=None, mode=None):
    xml, cfg, sty, seen, cls = [], {}, {}, {}, {}

    def walk(n):
        cid = n.cid
        if cid in seen:
            seen[cid] += 1
            cid = '%s %d' % (n.cid, seen[n.cid])
        else:
            seen[cid] = 1
        if n.kids:
            xml.append('<%s configuration-id="%s">' % (n.tag, cid))
            for k in n.kids:
                walk(k)
            xml.append('</%s>' % n.tag)
        else:
            xml.append('<%s configuration-id="%s"></%s>' % (n.tag, cid, n.tag))
        if n.cfg:
            cfg[cid] = n.cfg
        if n.css:
            sty[cid] = ' '.join(n.css.split())
        if n.cls:
            cls[cid] = n.cls

    for r in roots:
        walk(r)
    d = {'post_id': post_id, 'xml_structure': ''.join(xml), 'element_config': cfg, 'style': sty, 'classes': cls}
    if parent:
        d['parent_id'] = parent
    if mode:
        d['mode'] = mode
    return d


def save(path, d):
    json.dump(d, open(path, 'w'), indent=1)
    return path
