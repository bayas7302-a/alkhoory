"""Tiny DSL that turns a node tree into an elementor-build-composition payload."""
import json, sys, os
sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
import mcp

G = 'linear-gradient(90deg, #7f0083 0%, #a4007c 55%, #e0287f 100%)'
MEDIA = {}
for line in open(os.path.join(os.path.dirname(os.path.abspath(__file__)), 'media.tsv')):
    k, i, u = line.strip().split('\t')
    MEDIA[k] = (int(i), u)
SITE = 'https://soharon.co.uk/ab-production'


import re


def norm(style):
    """Per-side borders are not supported natively: merge into border-width/style/color."""
    sides = {}
    color = None

    def grab(m):
        nonlocal color
        sides[m.group(1)] = m.group(2)
        color = m.group(3)
        return ''
    top = style.split('@media')[0]
    rest = style[len(top):]
    top = re.sub(r'border-(top|right|bottom|left):\s*([0-9.]+px)\s+solid\s+([^;]+);', grab, top)
    if sides:
        w = ' '.join(sides.get(k, '0') for k in ('top', 'right', 'bottom', 'left'))
        top += ' border-width: %s; border-style: solid; border-color: %s;' % (w, color)
    return top + rest


class N:
    def __init__(self, tag, cid, cfg=None, style='', cls=None, kids=None):
        self.tag, self.cid, self.cfg, self.style, self.cls, self.kids = tag, cid, cfg or {}, norm(style), cls or [], kids or []


def box(cid, style='', kids=None, cls=None, tag=None, link=None):
    cfg = {}
    if tag:
        cfg['tag'] = tag
    if link:
        cfg['link'] = {'destination': link if link.startswith('http') else SITE + link, 'tag': 'a'}
    return N('e-flexbox', cid, cfg, 'padding: 0; ' + style, cls, kids)


def grid(cid, style='', kids=None, cls=None):
    return N('e-grid', cid, {}, 'padding: 0; ' + style, cls, kids)


def p(cid, text, style='', cls=None, tag=None, link=None):
    cfg = {'paragraph': text}
    if tag:
        cfg['tag'] = tag
    if link:
        cfg['link'] = {'destination': link if link.startswith('http') else SITE + link, 'tag': 'a'}
    return N('e-paragraph', cid, cfg, 'margin: 0; ' + style, cls)


def h(cid, text, style='', cls=None, tag='h2', dark=False):
    c = (['abp-h-dark'] if dark else []) + ['abp-h'] + (cls or [])
    if dark and 'color:' not in style.split('@media')[0]:
        style = 'color: #ffffff; ' + style
    return N('e-heading', cid, {'tag': tag, 'title': text}, style, c)


def img(cid, key, style='', cls=None):
    return N('e-image', cid, {'image': {'src': {'id': MEDIA[key][0]}, 'size': 'full'}}, style, cls)


def btn(cid, text, link, cls='abp-btn-grad', style=''):
    return N('e-button', cid, {'text': text, 'link': {'destination': link if link.startswith('http') else SITE + link, 'tag': 'a'}}, style, [cls])


def sc(cid, code, style=''):
    """Shortcode placeholder paragraph (expanded by the theme)."""
    return p(cid, code, 'width: 100%; ' + style)


def label(pre, num, text, dark=False, style=''):
    return box(pre + ' label', style, [
        p(pre + ' label num', num, 'color: #ff4fa3;' if dark else '', ['abp-label-num']),
        box(pre + ' label bar', '', [], ['abp-label-bar']),
        p(pre + ' label text', text, 'color: rgba(255, 255, 255, 0.8);' if dark else '', ['abp-label-text']),
    ], ['abp-label'])


def section(cid, style, kids, inner_style='', anchor=None, cls=None):
    """Full-width section with a centred 1280px inner column."""
    k = []
    if anchor:
        k.append(sc(cid + ' anchor', '[ab_anchor id="%s"]' % anchor))
    k.append(box(cid + ' inner', 'flex-direction: column; width: 100%; max-width: 1280px; margin: 0 auto; ' + inner_style, kids))
    return box(cid, 'flex-direction: column; position: relative; ' + style, k, cls, tag='section')


def walk(n, xml, cfg, sty, cls):
    seen = set()

    def rec(n):
        assert n.cid not in seen, n.cid
        seen.add(n.cid)
        out = '<%s configuration-id="%s">' % (n.tag, n.cid.replace('"', ''))
        for k in n.kids:
            out += rec(k)
        out += '</%s>' % n.tag
        if n.cfg:
            cfg[n.cid] = n.cfg
        if n.style.strip():
            sty[n.cid] = n.style
        if n.cls:
            cls[n.cid] = n.cls
        return out
    return rec(n)


def build(post_id, nodes, mode='append', parent='document', dry=False):
    cfg, sty, cls = {}, {}, {}
    xml = ''.join(walk(n, None, cfg, sty, cls) for n in nodes)
    payload = {'post_id': post_id, 'xml_structure': xml, 'element_config': cfg, 'style': sty, 'classes': cls, 'mode': mode, 'parent_id': parent}
    if dry:
        payload['dry_run'] = True
    out = mcp.text('elementor-build-composition', payload)
    try:
        d = json.loads(out)
    except Exception:
        print(out[:3000])
        raise SystemExit(1)
    if not d.get('success'):
        print(out[:3000])
        raise SystemExit(1)
    if d.get('warnings'):
        print('WARNINGS', json.dumps(d['warnings'])[:2500])
    print('ok', post_id, d.get('root_element_ids'))
    return d
