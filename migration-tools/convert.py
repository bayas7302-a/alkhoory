"""Convert a rendered Elementor V3/Liquid page (saved HTML + computed styles)
into Elementor V4 build-composition payloads (free widgets only)."""
import json, re, sys, os, hashlib, html as htmllib
from bs4 import BeautifulSoup, NavigableString, Tag
sys.path.insert(0, os.path.dirname(__file__))
from css import parse_rules

DEV = ('desktop', 'tablet', 'mobile')
ZERO = ('0px', '0', '', None)


def px(v):
    try:
        return float(str(v).replace('px', ''))
    except Exception:
        return None


def fmt(n):
    n = round(n, 2)
    return str(int(n)) if n == int(n) else str(n)


def box4(c, prefix, suffix=''):
    vals = [c.get(f'{prefix}-{s}{suffix}') for s in ('top', 'right', 'bottom', 'left')]
    if any(v is None for v in vals):
        return None
    if vals.count(vals[0]) == 4:
        return vals[0]
    if vals[0] == vals[2] and vals[1] == vals[3]:
        return f'{vals[0]} {vals[1]}'
    return ' '.join(vals)


def radius(c):
    vals = [c.get(k) for k in ('border-top-left-radius', 'border-top-right-radius', 'border-bottom-right-radius', 'border-bottom-left-radius')]
    if any(v is None for v in vals):
        return None
    vals = [v.split(' ')[0] for v in vals]
    return vals[0] if vals.count(vals[0]) == 4 else ' '.join(vals)


def transparent(col):
    return col in (None, '', 'transparent', 'rgba(0, 0, 0, 0)')


def font_family(ff):
    if not ff:
        return None
    f = ff.split(',')[0].strip().strip('"\'')
    return f


def typo(c, with_color=True, with_align=False):
    """Typography props from a computed style dict."""
    if not c:
        return {}
    o = {}
    o['font-family'] = font_family(c.get('font-family'))
    o['font-size'] = c.get('font-size')
    o['font-weight'] = c.get('font-weight')
    fs, lh = px(c.get('font-size')), px(c.get('line-height'))
    if lh and fs:
        o['line-height'] = fmt(lh / fs)
    ls = c.get('letter-spacing')
    if ls and ls != 'normal' and px(ls) != 0:
        o['letter-spacing'] = ls
    if c.get('text-transform') not in (None, 'none'):
        o['text-transform'] = c['text-transform']
    if c.get('font-style') == 'italic':
        o['font-style'] = 'italic'
    if 'underline' in (c.get('text-decoration-line') or ''):
        o['text-decoration'] = 'underline'
    if with_color:
        o['color'] = c.get('color')
    if with_align and c.get('text-align') not in (None, 'start'):
        o['text-align'] = c['text-align'].replace('start', 'left').replace('end', 'right')
    return o


def boxstyle(c, margin=True, padding=True, bg=True):
    """Box model + decoration from computed style."""
    if not c:
        return {}
    o = {}
    if padding:
        o['padding'] = box4(c, 'padding')
    if margin:
        m = box4(c, 'margin')
        if m and m not in ('0px',):
            o['margin'] = m
    if bg and not transparent(c.get('background-color')):
        o['background-color'] = c['background-color']
    r = radius(c)
    if r and r != '0px':
        o['border-radius'] = r
    bw = box4(c, 'border', '-width')
    if bw and bw != '0px' and c.get('border-top-style') not in (None, 'none'):
        o['border-width'] = bw
        o['border-style'] = c['border-top-style']
        o['border-color'] = c['border-top-color']
    if c.get('box-shadow') not in (None, 'none'):
        o['box-shadow'] = c['box-shadow']
    return o


def add_margins(a, b):
    """Sum two 'margin' shorthands made of px values (wrapper + inner)."""
    def parts(m):
        if not m:
            return [0, 0, 0, 0]
        p = [px(x) or 0 for x in m.split()]
        if len(p) == 1:
            p = p * 4
        elif len(p) == 2:
            p = [p[0], p[1], p[0], p[1]]
        elif len(p) == 3:
            p = [p[0], p[1], p[2], p[1]]
        return p
    s = [x + y for x, y in zip(parts(a), parts(b))]
    if not any(s):
        return None
    return ' '.join(fmt(v) + 'px' for v in s)


class Ctx:
    def __init__(self, page, html, comp, rules, assets):
        self.page = page
        self.html = html
        self.comp = comp
        self.rules = rules
        self.assets = assets      # svg hash -> {"id":..,"url":..}
        self.n = 0
        self.config = {}
        self.style = {}
        self.missing_svgs = []
        self.align_stack = ['normal']
        self.containers = set(re.findall(r'data-id="([0-9a-f]+)" data-element_type="container"', html))

    def cid(self, label):
        self.n += 1
        return f'{label} {self.n}'

    def c(self, eid, dev='desktop', key='self'):
        d = self.comp.get(dev, {}).get(eid)
        return d.get(key) if d else None

    def rule(self, eid, suffix='', media=''):
        out = {}
        for m, s, d in self.rules.get(eid, []):
            if m == media and s == suffix:
                out.update(d)
        return out


# ---------------------------------------------------------------- styles

def css_from_devs(devs, defaults=None):
    """devs: {'desktop':{prop:val}, 'tablet':{...}, 'mobile':{...}} -> css string with cascading media."""
    d = {k: v for k, v in (devs.get('desktop') or {}).items() if v not in (None, '')}
    if defaults:
        for k, v in defaults.items():
            if d.get(k) == v:
                d.pop(k)
    parts = [f'{k}: {v};' for k, v in d.items()]
    eff = dict(devs.get('desktop') or {})
    for dev, mq in (('tablet', '--tablet'), ('mobile', '--mobile')):
        cur = devs.get(dev) or {}
        diff = {}
        for k in set(cur) | set(eff):
            v = cur.get(k)
            if v in (None, ''):
                continue
            if eff.get(k) != v:
                diff[k] = v
        if diff:
            parts.append(f'@media({mq}) {{ ' + ' '.join(f'{k}: {v};' for k, v in diff.items()) + ' }')
        eff.update({k: v for k, v in cur.items() if v not in (None, '')})
    return ' '.join(parts)


def visibility(ctx, eid, shown_display):
    out = {}
    for dev in DEV:
        s = ctx.c(eid, dev)
        if s is None:
            continue
        out[dev] = {'display': 'none' if s.get('display') == 'none' else shown_display}
    return out


def merge_devs(*ds):
    out = {dev: {} for dev in DEV}
    for d in ds:
        for dev in DEV:
            out[dev].update({k: v for k, v in (d.get(dev) or {}).items() if v is not None})
    return out


def wrapper_layout(ctx, eid, parent_dir):
    """Positioning of a V3 element (widget wrapper or container) inside its flex parent."""
    out = {}
    for dev in DEV:
        s = ctx.c(eid, dev)
        if not s:
            continue
        o = {}
        if s.get('position') in ('absolute', 'fixed', 'relative'):
            if s['position'] != 'relative':
                o['position'] = s['position']
                for k in ('top', 'left', 'right', 'bottom'):
                    if s.get(k) not in (None, 'auto'):
                        o[k] = s[k]
            if s.get('z-index') not in (None, 'auto'):
                o['z-index'] = s['z-index']
        if s.get('align-self') not in (None, 'auto', 'normal'):
            o['align-self'] = s['align-self']
        if s.get('flex-grow') not in (None, '0'):
            o['flex-grow'] = s['flex-grow']
        if s.get('flex-shrink') not in (None, '1'):
            o['flex-shrink'] = s['flex-shrink']
        if s.get('order') not in (None, '0'):
            o['order'] = s['order']
        out[dev] = o
    # explicit widths from the original CSS rules
    w = {'desktop': None, 'tablet': None, 'mobile': None}
    for media, dev_list in (('', DEV), ('@media(min-width:768px)', ('desktop', 'tablet')),
                            ('@media(max-width:1024px)', ('tablet', 'mobile')), ('@media(max-width:767px)', ('mobile',))):
        r = ctx.rule(eid, '', media)
        val = r.get('--width') or r.get('width')
        if val:
            m = re.match(r'var\(\s*--container-widget-width,\s*([^)]+)\)', val)
            if m:
                val = m.group(1).strip()
            if '--container-widget-width' in r:
                val = r['--container-widget-width']
            for dev in dev_list:
                w[dev] = val
        mw = r.get('max-width')
        if mw and mw != 'none':
            for dev in dev_list:
                out.setdefault(dev, {})['max-width'] = mw
    is_container = ctx.c(eid, 'desktop', 'inner') is not None or (eid in ctx.containers)
    any_w = any(w[d] and w[d] not in ('initial', 'auto') for d in DEV)
    if not is_container and not any_w and parent_dir.startswith('row'):
        for dev in DEV:
            out.setdefault(dev, {})['width'] = 'auto'
    for dev in DEV:
        if w[dev] and w[dev] not in ('initial', 'auto'):
            out.setdefault(dev, {})['width'] = w[dev]
            out[dev].setdefault('max-width', w[dev] if w[dev].endswith('%') else '100%')
        elif any_w:
            # width only defined for other breakpoints -> V3 default for this one
            out.setdefault(dev, {})['width'] = '100%' if is_container else 'auto'
            out[dev].setdefault('max-width', '100%')
    # V3 widgets in a ROW parent size to content; V3 containers default to their --width
    return out


def background(ctx, eid):
    """Background layers from V3 rules (overlay + background), per device."""
    out = {}
    for media, devs in (('', DEV), ('@media(max-width:1024px)', ('tablet', 'mobile')), ('@media(max-width:767px)', ('mobile',))):
        base = ctx.rule(eid, ':not(.elementor-motion-effects-element-type-background)', media)
        ov = ctx.rule(eid, '::before', media)
        opac = ctx.rule(eid, '', media).get('--overlay-opacity')
        if not base and not ov and not opac:
            continue
        for dev in devs:
            o = out.setdefault(dev, {'_layers': [], '_base': {}, '_ov': {}, '_op': None})
            o['_base'].update(base)
            o['_ov'].update(ov)
            if opac:
                o['_op'] = opac
    res = {}
    for dev, o in out.items():
        b, ov = o['_base'], o['_ov']
        css = {}
        layers, sizes, poss, reps = [], [], [], []
        op = float(o['_op']) if o['_op'] else (0.5 if ov else 1)
        # overlay layer
        if ov:
            if ov.get('background-image', '').startswith('linear-gradient') or ov.get('background-image', '').startswith('radial-gradient'):
                layers.append(alpha_gradient(ov['background-image'], op))
                sizes.append('cover'); poss.append('center'); reps.append('no-repeat')
            elif ov.get('background-image', '').startswith('url'):
                layers.append(ov['background-image'])
                sizes.append(ov.get('background-size', 'auto')); poss.append(ov.get('background-position', 'center')); reps.append(ov.get('background-repeat', 'no-repeat'))
            elif not transparent(ov.get('background-color')) and ov.get('background-color'):
                col = rgba(ov['background-color'], op)
                layers.append(f'linear-gradient({col}, {col})')
                sizes.append('cover'); poss.append('center'); reps.append('no-repeat')
        bg = b.get('background') or ''
        bimg = b.get('background-image') or ''
        m = re.search(r'url\("?([^")]+)"?\)\s*([^;]*)', bg)
        if m:
            layers.append(f'url("{m.group(1)}")')
            poss.append(m.group(2).strip() or b.get('background-position', 'center'))
            sizes.append(b.get('background-size', 'auto')); reps.append(b.get('background-repeat', 'no-repeat'))
        elif bimg.startswith('url'):
            layers.append(bimg); poss.append(b.get('background-position', 'center'))
            sizes.append(b.get('background-size', 'auto')); reps.append(b.get('background-repeat', 'no-repeat'))
        elif 'gradient' in bimg:
            layers.append(bimg); poss.append('center'); sizes.append('cover'); reps.append('no-repeat')
        elif 'gradient' in bg:
            layers.append(bg); poss.append('center'); sizes.append('cover'); reps.append('no-repeat')
        col = b.get('background-color')
        if col and not transparent(col):
            css['background-color'] = col
        elif bg.startswith('#') or bg.startswith('rgb'):
            css['background-color'] = bg
        css['_layers'] = [{'background-image': l, 'background-size': sz, 'background-position': ps, 'background-repeat': rp}
                          for l, sz, ps, rp in zip(layers, sizes, poss, reps)]
        res[dev] = css
    return res


def hex2rgb(h):
    h = h.lstrip('#')
    if len(h) in (3, 4):
        h = ''.join(c * 2 for c in h)
    a = int(h[6:8], 16) / 255 if len(h) == 8 else 1
    return int(h[0:2], 16), int(h[2:4], 16), int(h[4:6], 16), a


def rgba(col, op):
    col = col.strip()
    if col.startswith('#'):
        r, g, b, a = hex2rgb(col)
    else:
        m = re.findall(r'[\d.]+', col)
        r, g, b = map(lambda x: int(float(x)), m[:3])
        a = float(m[3]) if len(m) > 3 else 1
    return f'rgba({r},{g},{b},{fmt(a * op)})'


def alpha_gradient(g, op):
    return re.sub(r'#[0-9a-fA-F]{3,8}\b|rgba?\([^)]*\)', lambda m: rgba(m.group(0), op), g)


# ---------------------------------------------------------------- content helpers

ALLOWED_INLINE = {'b', 'strong', 'sup', 'sub', 's', 'em', 'i', 'u', 'a', 'del', 'span', 'br'}


def clean_inline(node, allow=ALLOWED_INLINE):
    """Return inner HTML keeping only allowed inline tags (no attributes except href)."""
    out = []
    for ch in node.children:
        if isinstance(ch, NavigableString):
            out.append(htmllib.escape(str(ch), quote=False))
        elif isinstance(ch, Tag):
            if ch.name in ('figure', 'img', 'svg', 'style', 'script'):
                continue
            inner = clean_inline(ch, allow)
            if ch.name == 'br':
                out.append('<br>')
            elif ch.name in allow and ch.name != 'span':
                href = f' href="{htmllib.escape(ch.get("href",""))}"' if ch.name == 'a' and ch.get('href') else ''
                out.append(f'<{ch.name}{href}>{inner}</{ch.name}>')
            elif ch.name in ('ul', 'ol', 'li') and ch.name in allow:
                out.append(f'<{ch.name}>{inner}</{ch.name}>')
            else:
                out.append(inner)
    return ''.join(out)


def norm_ws(s):
    s = re.sub(r'[ \t\r\n]+', ' ', s)
    s = re.sub(r'\s*<br>\s*', '<br>', s)
    return s.strip()


# ---------------------------------------------------------------- converters

def elem(ctx, tag, label, config=None, devs=None, extra_css=''):
    cid = ctx.cid(label)
    if config:
        ctx.config[cid] = config
    css = css_from_devs(devs) if devs else ''
    if extra_css:
        css = (css + ' ' + extra_css).strip()
    if css:
        ctx.style[cid] = css
    return cid


def xml(tag, cid, children=()):
    inner = ''.join(children)
    return f'<{tag} configuration-id="{htmllib.escape(cid, quote=True)}">{inner}</{tag}>'


def children_of(node):
    """Direct Elementor element children of a container node."""
    inner = node.find('div', class_='e-con-inner', recursive=False)
    host = inner if inner is not None else node
    return [c for c in host.find_all(recursive=False) if isinstance(c, Tag) and c.has_attr('data-element_type')]


def convert_container(ctx, node, parent_dir='column', top=False):
    eid = node['data-id']
    boxed = 'e-con-boxed' in node.get('class', [])
    lay_src = 'inner' if boxed else 'self'
    devs_outer, devs_inner = {}, {}
    for dev in DEV:
        s = ctx.c(eid, dev)
        if not s:
            continue
        L = ctx.c(eid, dev, lay_src) or s
        lay = {
            'flex-direction': L.get('flex-direction'),
            'flex-wrap': L.get('flex-wrap') if L.get('flex-wrap') != 'nowrap' else 'nowrap',
            'justify-content': L.get('justify-content') if L.get('justify-content') not in ('normal',) else 'flex-start',
            'align-items': L.get('align-items') if L.get('align-items') not in ('normal',) else 'stretch',
        }
        rg, cg = L.get('row-gap'), L.get('column-gap')
        lay['gap'] = rg if rg == cg else (rg if (L.get('flex-direction') or '').startswith('column') else cg)
        if rg in ('normal',):
            lay['gap'] = '0px'
        if L.get('display') == 'none' or s.get('display') == 'none':
            lay['display'] = 'none'
        else:
            lay['display'] = 'flex'
        box = boxstyle(s, bg=False)
        box['padding'] = box4(s, 'padding') or '0px'
        if s.get('overflow-x') == 'hidden' or s.get('overflow-y') == 'hidden':
            box['overflow'] = 'hidden'
        mh = ctx.rule(eid, '', '').get('--min-height')
        if dev != 'desktop':
            mq = '@media(max-width:1024px)' if dev == 'tablet' else '@media(max-width:767px)'
            mh = ctx.rule(eid, '', mq).get('--min-height') or mh
            if dev == 'mobile':
                mh = ctx.rule(eid, '', mq).get('--min-height') or ctx.rule(eid, '', '@media(max-width:1024px)').get('--min-height') or ctx.rule(eid, '', '').get('--min-height')
        if mh:
            box['min-height'] = mh
        if boxed:
            devs_outer[dev] = {**box, 'display': lay['display'], 'flex-direction': 'column', 'align-items': 'center', 'justify-content': lay['justify-content'] if lay['flex-direction'].startswith('column') else 'flex-start', 'gap': '0px'}
            if lay['display'] == 'none':
                devs_outer[dev]['display'] = 'none'
            devs_inner[dev] = {**lay, 'padding': box4(L, 'padding') or '0px', 'width': '100%'}
            cw = None
            for media in ('@media(min-width:768px)', ''):
                cw = cw or ctx.rule(eid, '', media).get('--content-width')
            if cw and dev != 'mobile':
                devs_inner[dev]['max-width'] = cw
            elif dev == 'mobile':
                devs_inner[dev]['max-width'] = '100%'
            if mh:
                devs_inner[dev]['flex-grow'] = '1'
        else:
            devs_outer[dev] = {**box, **lay}
    bgd = background(ctx, eid)
    layered = any(len((bgd.get(d) or {}).get('_layers', [])) > 1 for d in DEV)
    bg_img, bg_ov = {}, {}
    for dev in DEV:
        b = dict(bgd.get(dev) or {})
        layers = b.pop('_layers', [])
        if layered:
            bg_img[dev] = dict(b)
            if layers:
                bg_img[dev].update(layers[-1])
            if len(layers) > 1:
                bg_ov[dev] = {'background-image': layers[0]['background-image']}
        else:
            bg_img[dev] = b
            if layers:
                bg_img[dev].update(layers[0])
    wl = wrapper_layout(ctx, eid, parent_dir)
    outer = merge_devs(devs_outer, bg_img, wl)
    my_dir = (ctx.c(eid, 'desktop', lay_src) or {}).get('flex-direction', 'column')
    ctx.align_stack.append((ctx.c(eid, 'desktop', lay_src) or {}).get('align-items', 'normal'))
    kids = [convert_node(ctx, ch, my_dir) for ch in children_of(node)]
    ctx.align_stack.pop()
    kids = [k for k in kids if k]
    tag = 'e-flexbox'
    if layered:
        # Overlay layer as a separate full-size flexbox (multi-layer backgrounds are not supported natively)
        ov = {}
        for dev in DEV:
            o = outer.get(dev) or {}
            keep = {k: o[k] for k in ('padding', 'min-height', 'flex-direction', 'flex-wrap', 'justify-content', 'align-items', 'gap', 'border-radius') if k in o}
            ov[dev] = {**keep, **(bg_ov.get(dev) or {}), 'width': '100%', 'flex-grow': '1', 'display': 'flex'}
            if 'padding' in o:
                o['padding'] = '0px'
            o['flex-direction'] = 'column'
            o['align-items'] = 'stretch'
            o['gap'] = '0px'
        if boxed:
            icid = elem(ctx, tag, 'Inner', devs=devs_inner)
            kids = [xml(tag, icid, kids)]
        vcid = elem(ctx, tag, 'Overlay', devs=ov)
        ocid = elem(ctx, tag, 'Section' if top else 'Box', config={'tag': 'section'} if top else None, devs=outer)
        return xml(tag, ocid, [xml(tag, vcid, kids)])
    if boxed:
        icid = elem(ctx, tag, 'Inner', devs=devs_inner)
        inner_xml = xml(tag, icid, kids)
        ocid = elem(ctx, tag, 'Section' if top else 'Box', config={'tag': 'section'} if top else None, devs=outer)
        return xml(tag, ocid, [inner_xml])
    ocid = elem(ctx, tag, 'Section' if top else 'Container', config={'tag': 'section'} if top else None, devs=outer)
    return xml(tag, ocid, kids)


def widget_box(ctx, eid, parent_dir, inner_key=None, shown='block'):
    """Per-device wrapper styles (margin from widget-container, layout, visibility)."""
    devs = {}
    for dev in DEV:
        wc = ctx.c(eid, dev, 'wc') or {}
        s = ctx.c(eid, dev) or {}
        o = boxstyle(wc, margin=False)
        if o.get('padding') in ('0px',):
            o.pop('padding')
        m = box4(wc, 'margin')
        sm = box4(s, 'margin')
        mm = add_margins(m if m != '0px' else None, sm if sm != '0px' else None)
        if inner_key:
            ic = ctx.c(eid, dev, inner_key) or {}
            im = box4(ic, 'margin')
            mm = add_margins(mm, im if im != '0px' else None)
        o['margin'] = mm or '0px'
        o['display'] = 'none' if s.get('display') == 'none' else shown
        devs[dev] = o
    return merge_devs(devs, wrapper_layout(ctx, eid, parent_dir))


def text_devs(ctx, eid, key, align_key=None, extra=None):
    devs = {}
    for dev in DEV:
        c = ctx.c(eid, dev, key)
        if not c:
            continue
        o = typo(c)
        o.update({k: v for k, v in boxstyle(c, margin=False).items() if k != 'padding' or v != '0px'})
        a = ctx.c(eid, dev, align_key) if align_key else c
        if a and a.get('text-align'):
            ta = a['text-align'].replace('start', 'left').replace('end', 'right')
            o['text-align'] = ta
        devs[dev] = o
    return devs


def convert_heading(ctx, node, parent_dir):
    eid = node['data-id']
    el = node.select_one('.ld-fh-element')
    if el is None:
        return ''
    tag = el.name if el.name in ('h1', 'h2', 'h3', 'h4', 'h5', 'h6') else 'p'
    spans = node.select('.lqd-adv-txt-item')
    base_color = (ctx.c(eid, 'desktop', 't') or {}).get('color')
    comp_spans = (ctx.comp['desktop'].get(eid) or {}).get('spans') or []
    span_colors, flag = [], None
    heading_color = None
    if spans:
        raw = []
        for i, sp in enumerate(spans):
            img = sp.find('img')
            if img is not None:
                flag = (img, comp_spans[i]['img'] if i < len(comp_spans) else None)
                continue
            raw.append((clean_inline(sp), comp_spans[i]['color'] if i < len(comp_spans) else base_color))
        first = raw[0][1] if raw else base_color
        if first != base_color:
            heading_color = first
        parts = []
        for txt, col in raw:
            if col and col != first:
                span_colors.append(col)
                parts.append(f'<span>{txt}</span>')
            else:
                parts.append(txt)
        content = norm_ws(''.join(parts))
    else:
        content = norm_ws(clean_inline(el))
    devs = merge_devs(widget_box(ctx, eid, parent_dir, inner_key='t'), text_devs(ctx, eid, 't', align_key='wrap'))
    extra = ''
    if heading_color:
        for dev in DEV:
            if dev in devs:
                devs[dev]['color'] = heading_color
    if parent_dir.startswith('column'):
        for dev in DEV:
            if dev in devs and 'width' not in devs[dev]:
                devs[dev]['width'] = '100%'
    if tag == 'p':
        cfg = {'paragraph': content, 'tag': 'p'}
        wt = 'e-paragraph'
    else:
        cfg = {'tag': tag, 'title': content}
        wt = 'e-heading'
    if flag:
        img, ic = flag
        # heading with an inline flag image -> row of image + heading
        row_devs = {dev: {'display': (devs.get(dev) or {}).get('display', 'flex').replace('block', 'flex'), 'flex-direction': 'row', 'align-items': 'center', 'gap': '12px', 'padding': '0px', 'margin': (devs.get(dev) or {}).get('margin', '0px')} for dev in DEV}
        for dev in DEV:
            (devs.get(dev) or {}).pop('margin', None)
            for k in ('width', 'max-width', 'align-self'):
                if k in (devs.get(dev) or {}):
                    row_devs[dev][k] = devs[dev].pop(k)
            ta = (devs.get(dev) or {}).get('text-align')
            if ta == 'center':
                row_devs[dev]['justify-content'] = 'center'
        w = ic['_w'] if ic else 40
        icid = elem(ctx, 'e-image', 'Flag', config={'image': {'src': {'url': img['src'], 'alt': img.get('alt', '')}, 'size': 'full'}},
                    devs={'desktop': {'width': f'{w}px', 'height': 'auto', 'flex-shrink': '0'}})
        hcid = elem(ctx, wt, 'Heading', config=cfg, devs=devs, extra_css=extra)
        rcid = elem(ctx, 'e-flexbox', 'Heading Row', devs=row_devs)
        return xml('e-flexbox', rcid, [xml('e-image', icid), xml(wt, hcid)])
    cid = elem(ctx, wt, 'Heading' if wt == 'e-heading' else 'Text', config=cfg, devs=devs, extra_css=extra)
    return xml(wt, cid)


def svg_asset(ctx, svg_node_html_idx_wid, wid):
    """Return uploaded PNG for the n-th svg of widget wid."""
    key = svg_node_html_idx_wid
    a = ctx.assets.get(key)
    if not a:
        ctx.missing_svgs.append(key)
        return None
    return a


def norm_svg(html):
    m = re.match(r'<svg[^>]*>', html)
    root = re.sub(r'\s(width|height)="[^"]*"', '', m.group(0))
    return root + html[m.end():]


def svg_hash(html):
    return hashlib.md5(norm_svg(html).encode()).hexdigest()[:12]


SVG_SIZES = {}


def svg_key(ctx, wid, k):
    svgs = [s for s in ctx.comp.get('svgs', []) if s['wid'] == wid]
    if k >= len(svgs):
        return None, None
    s = dict(svgs[k])
    h = svg_hash(s['html'])
    if not s['w']:
        w, hh = SVG_SIZES.get(h, (0, 0))
        if not w:
            m = re.search(r'viewBox="[\d.]+ [\d.]+ ([\d.]+) ([\d.]+)"', s['html'], re.I)
            ratio = float(m.group(2)) / float(m.group(1)) if m else 1
            w, hh = 18, 18 * ratio
        s['w'], s['h'] = w, hh
    return h, s


def icon_image(ctx, wid, k, label='Icon'):
    h, s = svg_key(ctx, wid, k)
    if not h:
        return None
    a = ctx.assets.get(h)
    if not a:
        ctx.missing_svgs.append(h)
        return None
    w, hh = round(s['w']), round(s['h'])
    if not w or not hh:
        return None
    cid = elem(ctx, 'e-image', label, config={'image': {'src': {'id': a['id']}, 'size': 'full'}},
               devs={'desktop': {'width': f'{w}px', 'height': f'{hh}px', 'flex-shrink': '0'}})
    return xml('e-image', cid)


def convert_iconbox(ctx, node, parent_dir):
    eid = node['data-id']
    ib = node.select_one('.iconbox')
    if ib is None:
        return ''
    cls = ib.get('class', [])
    layout = 'column' if 'iconbox-default' in cls else 'row'
    kids = []
    # label (e.g. "Step 1")
    lab = ib.select_one('.iconbox-label')
    if lab is not None:
        ld = {}
        for dev in DEV:
            c = ctx.c(eid, dev, 'label')
            if c:
                o = typo(c)
                o.update(boxstyle(c))
                o.update({'position': 'absolute', 'top': c.get('top')})
                lr = divider_rules(ctx, eid, '.iconbox-label', dev)
                start, end = lr.get('inset-inline-start'), lr.get('inset-inline-end')
                if end not in (None, 'auto', '') and start in (None, 'auto', ''):
                    o['right'] = end
                else:
                    o['left'] = start if start not in (None, '') else c.get('left')
                ld[dev] = o
        cid = elem(ctx, 'e-paragraph', 'Label', config={'paragraph': norm_ws(clean_inline(lab)), 'tag': 'span'}, devs=ld)
        kids.append(xml('e-paragraph', cid))
    # icon
    icon_xml = None
    img = ib.select_one('.iconbox-icon-container > img')
    svg = ib.select_one('.iconbox-icon-container svg')
    if img is not None:
        idev = {}
        for dev in DEV:
            c = ctx.c(eid, dev, 'img')
            if c:
                idev[dev] = {'width': f"{c['_w']}px", 'height': 'auto', 'flex-shrink': '0'}
        cid = elem(ctx, 'e-image', 'Icon', config={'image': {'src': {'url': img['src'], 'alt': img.get('alt', '').replace('<br>', ' ')}, 'size': 'full'}}, devs=idev)
        icon_xml = xml('e-image', cid)
    elif svg is not None:
        all_svgs = node.select('svg')
        icon_xml = icon_image(ctx, eid, all_svgs.index(svg))
    if icon_xml:
        ic = ctx.c(eid, 'desktop', 'icon') or {}
        iw = ctx.c(eid, 'desktop', 'iconwrap') or {}
        wrap_needed = not transparent(ic.get('background-color')) or (ic.get('border-top-style') not in (None, 'none') and px(ic.get('border-top-width')))
        mdev = {}
        for dev in DEV:
            icd = ctx.c(eid, dev, 'icon') or {}
            iwd = ctx.c(eid, dev, 'iconwrap') or {}
            mm = add_margins(box4(iwd, 'margin'), box4(icd, 'margin'))
            mdev[dev] = {'margin': mm or '0px', 'flex-shrink': '0'}
        if wrap_needed:
            wdev = {}
            for dev in DEV:
                icd = ctx.c(eid, dev, 'icon') or {}
                o = boxstyle(icd, margin=False)
                o.update({'width': f"{icd.get('_w')}px", 'height': f"{icd.get('_h')}px", 'justify-content': 'center', 'align-items': 'center', 'display': 'flex'})
                o.update(mdev[dev])
                wdev[dev] = o
            wcid = elem(ctx, 'e-flexbox', 'Icon Wrap', devs=wdev)
            icon_xml = xml('e-flexbox', wcid, [icon_xml])
        else:
            # apply margins to the image itself via a light wrapper
            wcid = elem(ctx, 'e-flexbox', 'Icon Wrap', devs={dev: {**mdev[dev], 'padding': '0px', 'width': 'auto', 'display': 'flex'} for dev in DEV})
            icon_xml = xml('e-flexbox', wcid, [icon_xml])
        kids.append(icon_xml)
    # text
    txt = []
    h = ib.select_one('.lqd-iconbox-heading')
    if h is not None and h.get_text(strip=True):
        hd = text_devs(ctx, eid, 'h')
        for dev in DEV:
            c = ctx.c(eid, dev, 'h')
            if c and dev in hd:
                hd[dev]['margin'] = box4(c, 'margin')
        htag = h.name if h.name in ('h1', 'h2', 'h3', 'h4', 'h5', 'h6') else 'h3'
        cid = elem(ctx, 'e-heading', 'Title', config={'tag': htag, 'title': norm_ws(clean_inline(h))}, devs=hd)
        txt.append(xml('e-heading', cid))
    ps = [p for p in ib.select('p') if p.get_text(strip=True) and 'iconbox-label' not in (p.get('class') or [])]
    for p in ps:
        pd = text_devs(ctx, eid, 'p')
        for dev in DEV:
            c = ctx.c(eid, dev, 'p')
            if c and dev in pd:
                pd[dev]['margin'] = box4(c, 'margin')
        cid = elem(ctx, 'e-paragraph', 'Description', config={'paragraph': norm_ws(clean_inline(p)), 'tag': 'p'}, devs=pd)
        txt.append(xml('e-paragraph', cid))
    if layout == 'row' and len(txt) > 1:
        tcid = elem(ctx, 'e-flexbox', 'Content', devs={dev: {'flex-direction': 'column', 'padding': '0px', 'gap': '0px', 'width': 'auto', 'flex-grow': '1', 'min-width': '0'} for dev in DEV})
        kids.append(xml('e-flexbox', tcid, txt))
    else:
        kids.extend(txt)
    # card
    devs = {}
    for dev in DEV:
        wc = ctx.c(eid, dev, 'wc') or {}
        s = ctx.c(eid, dev) or {}
        box = ctx.c(eid, dev, 'box') or {}
        o = boxstyle(wc, margin=False)
        o['padding'] = box4(wc, 'padding') or '0px'
        o['margin'] = add_margins(box4(wc, 'margin'), box4(s, 'margin')) or '0px'
        o['display'] = 'none' if s.get('display') == 'none' else 'flex'
        o['flex-direction'] = layout
        o['gap'] = '0px'
        ta = (box.get('text-align') or 'left').replace('start', 'left').replace('end', 'right')
        o['text-align'] = ta
        if layout == 'column':
            o['align-items'] = {'center': 'center', 'right': 'flex-end'}.get(ta, 'flex-start')
            o['justify-content'] = (wc.get('justify-content') if wc.get('justify-content') not in (None, 'normal') else 'flex-start')
        else:
            o['align-items'] = box.get('align-items') if box.get('align-items') not in (None, 'normal') else 'flex-start'
            if 'iconbox-inline' in cls:
                o['flex-wrap'] = 'wrap'
        if lab is not None:
            o['position'] = 'relative'
        devs[dev] = o
    devs = merge_devs(devs, wrapper_layout(ctx, eid, parent_dir))
    hv = ctx.comp.get('hover', {}).get(eid)
    base = ctx.c(eid, 'desktop', 'wc') or {}
    hover = []
    if hv:
        if hv.get('bg') and hv['bg'] != base.get('background-color'):
            hover.append(f"background-color: {hv['bg']};")
        if hv.get('shadow') and hv['shadow'] != base.get('box-shadow') and hv['shadow'] != 'none':
            hover.append(f"box-shadow: {hv['shadow']};")
        m = re.match(r'matrix\(([\d.]+),', hv.get('wtransform') or '')
        if m and abs(float(m.group(1)) - 1) > 0.005:
            hover.append(f'transform: scale({fmt(float(m.group(1)))});')
    if 'lqd-iconbox-scale' in node.get('class', []) and not any('transform' in x for x in hover):
        hover.append('transform: scale(1.05);')
    extra = ''
    if hover:
        extra = 'transition: all 0.3s; &:hover { ' + ' '.join(hover) + ' }'
    link = ib.select_one('a[href]')
    cfg = None
    if link is not None and link.get('href') and not link.get_text(strip=True):
        cfg = {'tag': 'a', 'link': {'destination': link['href'], 'tag': 'a'}}
    cid = elem(ctx, 'e-flexbox', 'Icon Box', config=cfg, devs=devs, extra_css=extra)
    return xml('e-flexbox', cid, kids)


def convert_button(ctx, node, parent_dir):
    eid = node['data-id']
    a = node.select_one('a.btn')
    if a is None:
        return ''
    text = norm_ws(clean_inline(a.select_one('.btn-txt') or a))
    href = a.get('href', '#')
    devs = {}
    for dev in DEV:
        c = ctx.c(eid, dev, 'a')
        s = ctx.c(eid, dev) or {}
        if not c:
            continue
        o = typo(c)
        o.update(boxstyle(c, margin=False))
        o['padding'] = box4(c, 'padding')
        if transparent(c.get('background-color')):
            o['background-color'] = 'transparent'
        if 'border-width' not in o:
            o['border-width'] = '0px'
        if not o.get('border-radius'):
            o['border-radius'] = '0px'
        o['text-align'] = 'center'
        wc = ctx.c(eid, dev, 'wc') or {}
        o['margin'] = add_margins(box4(wc, 'margin'), box4(s, 'margin')) or '0px'
        o['display'] = 'none' if s.get('display') == 'none' else 'inline-flex'
        devs[dev] = o
    cls = node.get('class', [])
    al = None
    for k, v in (('elementor-align-right', 'flex-end'), ('elementor-align-center', 'center'), ('elementor-align-justify', 'stretch'), ('elementor-align-left', 'flex-start')):
        if k in cls:
            al = v
    wl = wrapper_layout(ctx, eid, parent_dir)
    if parent_dir.startswith('column') and 'btn-block' not in a.get('class', []):
        # V3 widget wrappers are full width in column containers; the button sits by the wrapper's text-align
        for dev in DEV:
            ta = ((ctx.c(eid, dev) or {}).get('text-align') or 'start')
            fallback = {'center': 'center', 'right': 'flex-end', 'end': 'flex-end'}.get(ta, 'flex-start')
            wl.setdefault(dev, {}).setdefault('align-self', al or fallback)
    if 'btn-block' in a.get('class', []):
        for dev in DEV:
            devs.setdefault(dev, {})['width'] = '100%'
            devs[dev]['justify-content'] = 'center'
    devs = merge_devs(devs, wl)
    hv = ctx.comp.get('hover', {}).get(eid)
    base = ctx.c(eid, 'desktop', 'a') or {}
    hover = []
    if hv:
        if hv.get('bg') and hv['bg'] != base.get('background-color'):
            hover.append(f"background-color: {hv['bg']};")
        if hv.get('color') and hv['color'] != base.get('color'):
            hover.append(f"color: {hv['color']};")
        if hv.get('border') and hv['border'] != base.get('border-top-color') and base.get('border-top-style') not in (None, 'none'):
            hover.append(f"border-color: {hv['border']};")
    if not hover:
        hover.append('opacity: 0.88;')
    extra = 'transition: all 0.3s; &:hover { ' + ' '.join(hover) + ' }'
    svg = a.select_one('.btn-icon svg')
    if svg is not None:
        icon = icon_image(ctx, eid, node.select('svg').index(svg), 'Button Icon')
        ic = ctx.c(eid, 'desktop', 'icon') or {}
        for dev in DEV:
            devs.setdefault(dev, {}).update({'align-items': 'center', 'gap': '10px', 'flex-direction': 'row-reverse' if 'btn-icon-left' in a.get('class', []) else 'row', 'width': devs.get(dev, {}).get('width', 'auto')})
            devs[dev].setdefault('justify-content', 'center')
            if devs[dev].get('display') != 'none':
                devs[dev]['display'] = 'flex'
        tcfg = {'paragraph': text, 'tag': 'span'}
        tcid = elem(ctx, 'e-paragraph', 'Button Text', config=tcfg, devs={'desktop': {'color': 'inherit'}})
        kids = [xml('e-paragraph', tcid)]
        if icon:
            kids.append(icon)
        cid = elem(ctx, 'e-flexbox', 'Button', config={'tag': 'a', 'link': {'destination': href, 'tag': 'a'}}, devs=devs, extra_css=extra)
        return xml('e-flexbox', cid, kids)
    cid = elem(ctx, 'e-button', 'Button', config={'text': text, 'link': {'destination': href, 'tag': 'a'}}, devs=devs, extra_css=extra)
    return xml('e-button', cid)


def image_cfg(img):
    src = img.get('data-src') or img.get('src')
    m = re.search(r'wp-image-(\d+)', ' '.join(img.get('class', [])))
    if m:
        return {'image': {'src': {'id': int(m.group(1))}, 'size': 'full'}}
    return {'image': {'src': {'url': src, 'alt': img.get('alt', '')}, 'size': 'full'}}


def convert_image(ctx, node, parent_dir):
    eid = node['data-id']
    img = node.find('img')
    if img is None:
        return ''
    devs = {}
    for dev, media in (('desktop', ''), ('tablet', '@media(max-width:1024px)'), ('mobile', '@media(max-width:767px)')):
        s = ctx.c(eid, dev) or {}
        ci = ctx.c(eid, dev, 'img') or {}
        wc = ctx.c(eid, dev, 'wc') or {}
        o = {}
        r = ctx.rule(eid, 'img', '')
        if dev != 'desktop':
            r = {**r, **ctx.rule(eid, 'img', '@media(max-width:1024px)')}
        if dev == 'mobile':
            r = {**r, **ctx.rule(eid, 'img', '@media(max-width:767px)')}
        o['width'] = r.get('width') or ('100%' if ci.get('_w') and wc.get('_w') and abs(ci['_w'] - wc['_w']) < 3 else (f"{ci.get('_w')}px" if ci.get('_w') else None))
        o['max-width'] = r.get('max-width') or '100%'
        o['height'] = r.get('height') or 'auto'
        if r.get('object-fit'):
            o['object-fit'] = r['object-fit']
        fig = ctx.c(eid, dev, 'fig') or {}
        rr = radius(fig) if fig else None
        rr = rr if rr and rr != '0px' else radius(ci)
        if rr and rr != '0px':
            o['border-radius'] = rr
        if fig.get('box-shadow') not in (None, 'none'):
            o['box-shadow'] = fig['box-shadow']
        o['margin'] = add_margins(box4(wc, 'margin'), box4(s, 'margin')) or '0px'
        o['display'] = 'none' if s.get('display') == 'none' else 'block'
        ta = (s.get('text-align') or wc.get('text-align') or '')
        if parent_dir.startswith('column') and o['width'] != '100%':
            if ta == 'center':
                o['align-self'] = 'center'
            elif ta in ('right', 'end'):
                o['align-self'] = 'flex-end'
        devs[dev] = o
    devs = merge_devs(devs, wrapper_layout(ctx, eid, parent_dir))
    cfg = image_cfg(img)
    a = node.find('a', href=True)
    if a is not None:
        cfg['link'] = {'destination': a['href'], 'tag': 'a'}
    cid = elem(ctx, 'e-image', 'Image', config=cfg, devs=devs)
    return xml('e-image', cid)


def convert_counter(ctx, node, parent_dir):
    eid = node['data-id']
    num = node.select_one('.lqd-counter-nums-wrap') or node.select_one('.lqd-counter-element')
    lab = node.select_one('.lqd-counter-text')
    kids = []
    if num is not None:
        cid = elem(ctx, 'e-paragraph', 'Number', config={'paragraph': norm_ws(num.get_text(' ')), 'tag': 'p'}, devs=text_devs(ctx, eid, 'num'))
        kids.append(xml('e-paragraph', cid))
    if lab is not None:
        cid = elem(ctx, 'e-paragraph', 'Label', config={'paragraph': norm_ws(lab.get_text(' ')), 'tag': 'p'}, devs=text_devs(ctx, eid, 'text'))
        kids.append(xml('e-paragraph', cid))
    devs = widget_box(ctx, eid, parent_dir, shown='flex')
    for dev in DEV:
        devs[dev].update({'flex-direction': 'column', 'gap': '0px', 'padding': devs[dev].get('padding', '0px')})
        b = ctx.c(eid, dev, 'box') or {}
        ta = b.get('text-align', 'left')
        devs[dev]['align-items'] = {'center': 'center', 'right': 'flex-end', 'end': 'flex-end'}.get(ta, 'flex-start')
    cid = elem(ctx, 'e-flexbox', 'Counter', devs=devs)
    return xml('e-flexbox', cid, kids)


def resolve_var(ctx, v):
    """Resolve var(--e-global-color-xxx) using the kit's CSS."""
    if not v:
        return v
    m = re.search(r'var\(\s*(--e-global-[a-z0-9-]+)\s*\)', v)
    if not m:
        return v
    if not hasattr(ctx, '_kit'):
        ctx._kit = dict(re.findall(r'(--e-global-color-[a-z0-9]+):\s*([^;}\s]+)', ctx.html))
    return ctx._kit.get(m.group(1), v)


def divider_rules(ctx, eid, suffix, dev):
    out = {}
    for media, devs in (('', DEV), ('@media(max-width:1024px)', ('tablet', 'mobile')), ('@media(max-width:767px)', ('mobile',))):
        if dev in devs:
            out.update(ctx.rule(eid, suffix, media))
    return out


def convert_divider(ctx, node, parent_dir):
    eid = node['data-id']
    cls = node.get('class', [])
    textel = node.select_one('.elementor-divider__text')
    label = norm_ws(textel.get_text(' ')) if textel is not None else ''
    align = 'center'
    for a in ('left', 'right', 'center'):
        if f'elementor-widget-divider--element-align-{a}' in cls:
            align = a
    devs, line_devs, text_devs_ = {}, {}, {}
    for dev in DEV:
        s = ctx.c(eid, dev) or {}
        wc = ctx.c(eid, dev, 'wc') or {}
        base = divider_rules(ctx, eid, '', dev)
        sep = divider_rules(ctx, eid, '.elementor-divider-separator', dev)
        dv = divider_rules(ctx, eid, '.elementor-divider', dev)
        col = resolve_var(ctx, base.get('--divider-color')) or '#000000'
        bw = base.get('--divider-border-width', '1px')
        bs = base.get('--divider-border-style', 'solid')
        if bs not in ('solid', 'dashed', 'dotted', 'double'):
            bs = 'solid'
        width = sep.get('width', '100%')
        pad_t = dv.get('padding-block-start', '15px')
        pad_b = dv.get('padding-block-end', '15px')
        ta = dv.get('text-align') or ('left' if sep.get('margin-left') == '0' else 'right' if sep.get('margin-right') == '0' else 'center')
        o = {'display': 'none' if s.get('display') == 'none' else ('flex' if label else 'block'),
             'width': width, 'max-width': '100%', 'min-width': '0px', 'padding': '0px',
             'margin': add_margins(add_margins(box4(wc, 'margin'), box4(s, 'margin')), f'{pad_t} 0px {pad_b} 0px') or '0px'}
        if ta == 'center':
            o['margin-left'] = 'auto'; o['margin-right'] = 'auto'
        elif ta == 'right':
            o['margin-left'] = 'auto'; o['margin-right'] = '0px'
        if label:
            o.update({'flex-direction': 'row', 'align-items': 'center', 'gap': '10px'})
            line_devs[dev] = {'flex-grow': '1', 'height': bw, 'padding': '0px', 'min-width': '10px', 'background-color': col}
            t = divider_rules(ctx, eid, '.elementor-divider__text', dev)
            td = {k: resolve_var(ctx, t.get(k)) for k in ('color', 'font-size', 'font-weight', 'text-transform', 'letter-spacing', 'line-height') if t.get(k)}
            if t.get('font-family'):
                td['font-family'] = font_family(t['font-family'])
            td['flex-shrink'] = '0'
            text_devs_[dev] = td
        else:
            o.update({'height': bw, 'background-color': col})
        devs[dev] = o
    devs = merge_devs(devs, wrapper_layout(ctx, eid, parent_dir))
    if not label:
        cid = elem(ctx, 'e-div-block', 'Divider', devs=devs)
        return xml('e-div-block', cid)
    parts = []
    def line():
        c = elem(ctx, 'e-div-block', 'Divider Line', devs=line_devs)
        return xml('e-div-block', c)
    tcid = elem(ctx, 'e-paragraph', 'Divider Text', config={'paragraph': label, 'tag': 'span'}, devs=text_devs_)
    txt = xml('e-paragraph', tcid)
    if align == 'left':
        parts = [txt, line()]
    elif align == 'right':
        parts = [line(), txt]
    else:
        parts = [line(), txt, line()]
    cid = elem(ctx, 'e-flexbox', 'Divider', devs=devs)
    return xml('e-flexbox', cid, parts)


def convert_icon_list(ctx, node, parent_dir):
    eid = node['data-id']
    items = node.select('.elementor-icon-list-item')
    comp_items = (ctx.comp['desktop'].get(eid) or {}).get('items') or []
    all_svgs = node.select('svg')
    ul = ctx.c(eid, 'desktop', 'ul') or {}
    inline = 'elementor-inline-items' in ' '.join(node.select_one('ul').get('class', [])) if node.select_one('ul') else False
    kids = []
    for i, li in enumerate(items):
        parts = []
        svg = li.select_one('.elementor-icon-list-icon svg')
        img = li.select_one('.elementor-icon-list-icon img')
        if svg is not None:
            ic = icon_image(ctx, eid, all_svgs.index(svg), 'List Icon')
            if ic:
                parts.append(ic)
        elif img is not None:
            cid = elem(ctx, 'e-image', 'List Icon', config={'image': {'src': {'url': img['src'], 'alt': ''}, 'size': 'full'}}, devs={'desktop': {'width': f"{(comp_items[i]['icon'] or {}).get('_w', 20)}px"}})
            parts.append(xml('e-image', cid))
        t = li.select_one('.elementor-icon-list-text')
        tdevs = {}
        for dev in DEV:
            ci = ((ctx.comp[dev].get(eid) or {}).get('items') or [])
            if i < len(ci) and ci[i]['text']:
                o = typo(ci[i]['text'])
                o['padding'] = box4(ci[i]['text'], 'padding')
                tdevs[dev] = o
        cid = elem(ctx, 'e-paragraph', 'List Text', config={'paragraph': norm_ws(clean_inline(t)) if t else '', 'tag': 'span'}, devs=tdevs)
        parts.append(xml('e-paragraph', cid))
        a = li.find('a', href=True)
        rdevs = {}
        for dev in DEV:
            ci = ((ctx.comp[dev].get(eid) or {}).get('items') or [])
            lic = ci[i]['li'] if i < len(ci) else {}
            icc = ci[i]['icon'] if i < len(ci) else {}
            gap = icc.get('padding-right') if icc and px(icc.get('padding-right')) else (icc.get('margin-right') if icc else '8px')
            li_align = (lic or {}).get('align-items')
            rdevs[dev] = {'flex-direction': 'row', 'align-items': li_align if li_align not in (None, 'normal') else 'center', 'gap': gap or '8px', 'padding': box4(lic, 'padding') if lic else '0px', 'width': 'auto', 'margin': box4(lic, 'margin') if lic and box4(lic, 'margin') != '0px' else '0px'}
        cfg = {'tag': 'a', 'link': {'destination': a['href'], 'tag': 'a'}} if a is not None else None
        rcid = elem(ctx, 'e-flexbox', 'List Item', config=cfg, devs=rdevs)
        kids.append(xml('e-flexbox', rcid, parts))
    devs = widget_box(ctx, eid, parent_dir, shown='flex')
    for dev in DEV:
        u = ctx.c(eid, dev, 'ul') or {}
        devs[dev].update({'flex-direction': 'row' if inline else 'column', 'flex-wrap': 'wrap', 'padding': '0px',
                          'gap': u.get('row-gap') if u.get('row-gap') not in (None, 'normal') else '0px'})
        jc = u.get('justify-content')
        if jc and jc != 'normal':
            devs[dev]['justify-content' if inline else 'align-items'] = jc
    cid = elem(ctx, 'e-flexbox', 'Icon List', devs=devs)
    return xml('e-flexbox', cid, kids)


def convert_icon(ctx, node, parent_dir):
    eid = node['data-id']
    ic = icon_image(ctx, eid, 0)
    if not ic:
        return ''
    devs = widget_box(ctx, eid, parent_dir, shown='flex')
    for dev in DEV:
        s = ctx.c(eid, dev, 'wc') or {}
        devs[dev].update({'padding': '0px', 'justify-content': {'center': 'center', 'right': 'flex-end', 'end': 'flex-end'}.get(s.get('text-align'), 'flex-start')})
    a = node.find('a', href=True)
    cfg = {'tag': 'a', 'link': {'destination': a['href'], 'tag': 'a'}} if a is not None else None
    cid = elem(ctx, 'e-flexbox', 'Icon', config=cfg, devs=devs)
    return xml('e-flexbox', cid, [ic])


def convert_social(ctx, node, parent_dir):
    eid = node['data-id']
    kids = []
    all_svgs = node.select('svg')
    for a in node.select('a.elementor-social-icon'):
        svg = a.find('svg')
        ic = icon_image(ctx, eid, all_svgs.index(svg), 'Social Icon') if svg is not None else None
        d = {}
        for dev in DEV:
            c = ctx.c(eid, dev, 'a') or {}
            o = boxstyle(c, margin=False)
            o.update({'width': f"{c.get('_w')}px", 'height': f"{c.get('_h')}px", 'justify-content': 'center', 'align-items': 'center', 'padding': '0px'})
            d[dev] = o
        cid = elem(ctx, 'e-flexbox', 'Social Link', config={'tag': 'a', 'link': {'destination': a.get('href', '#'), 'tag': 'a', 'isTargetBlank': True}}, devs=d, extra_css='transition: all 0.3s; &:hover { opacity: 0.85; }')
        kids.append(xml('e-flexbox', cid, [ic] if ic else []))
    devs = widget_box(ctx, eid, parent_dir, shown='flex')
    for dev in DEV:
        w = ctx.c(eid, dev, 'wrap') or {}
        devs[dev].update({'flex-direction': 'row', 'flex-wrap': 'wrap', 'padding': '0px', 'gap': w.get('column-gap') if w.get('column-gap') not in (None, 'normal') else '10px'})
        ta = (ctx.c(eid, dev, 'wc') or {}).get('text-align')
        devs[dev]['justify-content'] = {'center': 'center', 'right': 'flex-end', 'end': 'flex-end'}.get(ta, 'flex-start')
    cid = elem(ctx, 'e-flexbox', 'Social Icons', devs=devs)
    return xml('e-flexbox', cid, kids)


def convert_cf7(ctx, node, parent_dir):
    eid = node['data-id']
    f = node.select_one('[data-wpcf7-id]')
    fid = f['data-wpcf7-id'] if f is not None else '13'
    devs = widget_box(ctx, eid, parent_dir, shown='block')
    cid = elem(ctx, 'e-paragraph', 'Contact Form', config={'paragraph': f'[ais_cf7 id="{fid}"]', 'tag': 'p'}, devs=devs)
    return xml('e-paragraph', cid)


def convert_carousel(ctx, node, parent_dir):
    eid = node['data-id']
    items = node.select('.carousel-item')
    kids = []
    iw = {}
    for dev, media in (('desktop', ''), ('tablet', '@media(max-width:1024px)'), ('mobile', '@media(max-width:767px)')):
        r = ctx.rule(eid, '.carousel-item', media)
        if r.get('width'):
            iw[dev] = r['width']
    for it in items:
        doc = it.select_one('[data-elementor-id]')
        if doc is None:
            continue
        inner = [convert_node(ctx, c, 'column') for c in doc.find_all(recursive=False) if isinstance(c, Tag) and c.has_attr('data-element_type')]
        d = {}
        for dev in DEV:
            w = iw.get(dev) or iw.get('tablet' if dev == 'mobile' else 'desktop') or '33.33%'
            d[dev] = {'flex': {'desktop': '0 0 calc(33.333% - 14px)', 'tablet': '0 0 calc(50% - 10px)', 'mobile': '0 0 100%'}[dev], 'padding': '0px', 'display': 'flex'}
        cid = elem(ctx, 'e-flexbox', 'Slide', devs=d)
        kids.append(xml('e-flexbox', cid, inner))
    devs = widget_box(ctx, eid, parent_dir, shown='flex')
    for dev in DEV:
        devs[dev].update({'flex-direction': 'row', 'flex-wrap': 'wrap', 'gap': '20px', 'padding': '0px', 'align-items': 'stretch'})
    cid = elem(ctx, 'e-flexbox', 'Carousel', devs=devs)
    return xml('e-flexbox', cid, kids)


def convert_navmenu(ctx, node, parent_dir):
    """Elementor Pro nav-menu (used in the footer) -> simple list of links."""
    eid = node['data-id']
    nav = node.select_one('nav.elementor-nav-menu--main') or node.select_one('nav')
    if nav is None:
        return ''
    links = nav.select('a')
    kids = []
    for a in links:
        depth = len(a.find_parents('ul')) - 1
        d = {}
        for dev in DEV:
            c = ctx.c(eid, dev, 'link') or {}
            o = typo(c) if c else {'font-family': 'DM Sans', 'font-size': '15px', 'font-weight': '400', 'line-height': '1.7', 'color': '#606060'}
            o['padding'] = f"4px 0px 4px {12 * depth}px"
            d[dev] = o
        txt = norm_ws(a.get_text(' '))
        if a.get('href'):
            cfg = {'paragraph': txt, 'tag': 'p', 'link': {'destination': a['href'], 'tag': 'a'}}
        else:
            cfg = {'paragraph': txt, 'tag': 'p'}
        cid = elem(ctx, 'e-paragraph', 'Menu Link', config=cfg, devs=d, extra_css='&:hover { color: #02AAFE; }')
        kids.append(xml('e-paragraph', cid))
    devs = widget_box(ctx, eid, parent_dir, shown='flex')
    for dev in DEV:
        devs[dev].update({'flex-direction': 'column', 'gap': '0px', 'padding': '0px'})
    cid = elem(ctx, 'e-flexbox', 'Footer Menu', devs=devs)
    return xml('e-flexbox', cid, kids)


CONV = {
    'hub_fancy_heading': convert_heading,
    'ld_icon_box': convert_iconbox,
    'ld_button': convert_button,
    'ld_fancy_image': convert_image,
    'image': convert_image,
    'ld_counter': convert_counter,
    'divider': convert_divider,
    'icon-list': convert_icon_list,
    'icon': convert_icon,
    'social-icons': convert_social,
    'ld_cf722': convert_cf7,
    'ld_carousel': convert_carousel,
    'nav-menu': convert_navmenu,
}


def convert_node(ctx, node, parent_dir, top=False):
    t = node.get('data-element_type')
    if t == 'container':
        return convert_container(ctx, node, parent_dir, top=top)
    wt = (node.get('data-widget_type') or '').split('.')[0]
    fn = CONV.get(wt)
    if fn is None:
        print('UNHANDLED', wt, node.get('data-id'), file=sys.stderr)
        return ''
    return fn(ctx, node, parent_dir)


def convert_page(name, page_id, base):
    html = open(f'{base}/site/pages/{name}.html', encoding='utf-8').read()
    comp = json.load(open(f'{base}/computed/{name}.json'))
    assets = json.load(open(f'{base}/assets.json')) if os.path.exists(f'{base}/assets.json') else {}
    soup = BeautifulSoup(html, 'lxml')
    root = soup.select_one(f'[data-elementor-id="{page_id}"]')
    ctx = Ctx(name, html, comp, parse_rules(html), assets)
    for sv in comp.get('svgs', []):
        if sv['w']:
            SVG_SIZES.setdefault(svg_hash(sv['html']), (sv['w'], sv['h']))
    sections = []
    for top in [c for c in root.find_all(recursive=False) if isinstance(c, Tag) and c.has_attr('data-element_type')]:
        ctx.config, ctx.style = {}, {}
        x = convert_node(ctx, top, 'column', top=True)
        sections.append({'xml_structure': x, 'element_config': ctx.config, 'style': ctx.style})
    return sections, ctx


if __name__ == '__main__':
    base = sys.argv[1]
    name, pid = sys.argv[2], sys.argv[3]
    secs, ctx = convert_page(name, pid, base)
    json.dump(secs, open(f'{base}/out/{name}.json', 'w'), indent=1)
    print(name, 'sections', len(secs), 'elements', ctx.n, 'missing svgs', len(set(ctx.missing_svgs)))
