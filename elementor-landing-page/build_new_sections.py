# Clone the Process section's design into two new compact card sections and insert them.
import json, copy, random, re, sys
d = json.load(open('cur.json'))
ids = {e['id'] for e in d}
if any(e.get('editor_settings', {}).get('title') in ('types-section', 'features-section') for e in d):
    sys.exit('already built')
def rid(): return '%08x' % random.getrandbits(32)
def find(es, i):
    for e in es:
        if e['id'] == i: return e
        r = find(e.get('elements', []), i)
        if r: return r
def reid(e, prefix):
    old = e['id']; e['id'] = rid()
    t = e.get('editor_settings', {}).get('title')
    if t: e['editor_settings']['title'] = t.replace('process', prefix)
    st = e.get('styles') or {}
    new = {}
    for k, v in st.items():
        nk = 'e-%s-%04x' % (rid(), random.getrandbits(16))
        v['id'] = nk; new[nk] = v
        cl = e['settings'].get('classes', {}).get('value', [])
        e['settings']['classes']['value'] = [nk if c == k else c for c in cl]
    if st: e['styles'] = new
    for c in e.get('elements', []): reid(c, prefix)
def setp(e, k, v): e['settings'][k] = {"$$type": "escaped-html", "value": v}
def props(e, bp='desktop', state=None):
    for v in e['styles'].values():
        for var in v['variants']:
            if var['meta']['breakpoint'] == bp and var['meta']['state'] == state: return var['props']
    v = list(e['styles'].values())[0]
    var = {"meta": {"breakpoint": bp, "state": state}, "props": {}, "custom_css": None}
    v['variants'].append(var); return var['props']
def size(n, u='px'): return {"$$type": "size", "value": {"size": n, "unit": u}}
def color(c): return {"$$type": "background", "value": {"color": {"$$type": "color", "value": c}}}

proc = find(d, '41668bd9')
def build(prefix, anchor, eyebrow, title, intro, items, cols, bg, card_bg, num_bg, on_dark):
    s = copy.deepcopy(proc); reid(s, prefix)
    anc = s['elements'][0]
    anc['settings']['html'] = anc['settings']['html'].replace('id="process"', 'id="%s"' % anchor).replace('41668bd9', s['id'])
    s['editor_settings'] = {"title": prefix + '-section'}
    props(s)['background'] = color(bg)
    inner = s['elements'][1]
    hdr, grid = inner['elements'][0], inner['elements'][1]
    eb = hdr['elements'][0]
    setp(eb['elements'][1], 'paragraph', eyebrow)
    hdr['elements'][1]['settings']['title'] = {"$$type": "escaped-html", "value": title}
    setp(hdr['elements'][2], 'paragraph', intro)
    tmpl = grid['elements'][0]
    cards = []
    for i, (h, p) in enumerate(items):
        c = copy.deepcopy(tmpl); reid(c, prefix)
        c['editor_settings']['title'] = '%s-card-%d' % (prefix, i + 1)
        cp = props(c); cp['background'] = color(card_bg)
        cp['padding'] = {"$$type": "dimensions", "value": {k: size(v) for k, v in zip(['block-start', 'inline-end', 'block-end', 'inline-start'], [22, 22, 24, 22])}}
        num = c['elements'][0]; props(num)['background'] = color(num_bg)
        props(num)['width'] = size(44); props(num)['height'] = size(44)
        setp(num['elements'][0], 'paragraph', '%02d' % (i + 1))
        props(num['elements'][0])['font-size'] = size(17)
        c['elements'][1]['settings']['title'] = {"$$type": "escaped-html", "value": h}
        props(c['elements'][1])['font-size'] = size(22)
        props(c['elements'][1], 'mobile')['font-size'] = size(21)
        setp(c['elements'][2], 'paragraph', p)
        cards.append(c)
    grid['elements'] = cards
    g = props(grid)
    g['grid-template-columns'] = {"$$type": "string", "value": "repeat(%d, 1fr)" % cols}
    g['grid-template-rows'] = {"$$type": "string", "value": "auto"}
    g['gap'] = size(20)
    props(grid, 'tablet')['grid-template-columns'] = {"$$type": "string", "value": "repeat(2, 1fr)"}
    props(grid, 'tablet')['grid-template-rows'] = {"$$type": "string", "value": "auto"}
    props(grid, 'mobile')['grid-template-rows'] = {"$$type": "string", "value": "auto"}
    if on_dark:
        for el in (hdr['elements'][1], hdr['elements'][2]):
            props(el)['color'] = {"$$type": "color", "value": "#fff6ee"}
    return s

types = build('types', 'website-types', 'WHAT WE BUILD', 'TYPES OF WEBSITES <span>WE DEVELOP</span>',
    'From a single landing page to a full web portal, every website is custom designed and developed in Dubai for UAE businesses.',
    [('ONE-PAGE &amp; LANDING PAGE WEBSITES', 'A single, focused page with contact form, WhatsApp and Google Maps — ideal for launches and ad campaigns.'),
     ('MULTI-PAGE CORPORATE WEBSITES', 'Custom-designed company websites with service and portfolio pages, a blog and an easy CMS to update content yourself.'),
     ('MARKETING-READY WEBSITES', 'Built for growth: campaign landing pages, on-page SEO, analytics and conversion tracking, and lead-capture forms.'),
     ('SHOPIFY &amp; WOOCOMMERCE E-COMMERCE WEBSITES', 'Online stores with secure checkout, payment gateways, inventory, coupons and SEO-optimised product pages.'),
     ('ENTERPRISE WEB PORTALS', 'Custom portals with user roles, dashboards, multi-language support and ERP, CRM or API integrations.')],
    5, '#ffffff', '#fff6ee', '#065348', False)
feats = build('features', 'website-features', 'FEATURES &amp; CAPABILITIES', 'WEBSITE FEATURES <span>AND CAPABILITIES</span>',
    'Every Hee&amp;Lee website is designed from scratch and developed to perform for your business.',
    [('UNIQUE CUSTOM WEBSITE DESIGN', 'Designed from scratch around your brand and audience — no templates, no look-alike sites.'),
     ('DYNAMIC &amp; FUNCTIONAL WEBSITES', 'Forms, bookings, filters, member areas and content you can manage easily from the CMS.'),
     ('THIRD-PARTY INTEGRATIONS', 'WhatsApp, Google Maps, CRMs, payment gateways, booking tools, analytics and marketing platforms.'),
     ('RESPONSIVE DESIGN', 'Pixel-perfect on mobile, tablet and desktop, with fast loading on every screen size.'),
     ('SEO-FRIENDLY STRUCTURE', 'Clean code, proper headings, meta data, schema-ready markup and fast page speed from day one.'),
     ('WEBSITE OWNERSHIP TRANSFER', 'At handover you receive full ownership of the website, domain access, admin logins and source files.')],
    3, '#065348', '#ffffff', '#e15338', True)

def insert_after(root_id, sec):
    i = [e['id'] for e in d].index(root_id); d.insert(i + 1, sec)
insert_after('495e4057', feats)
insert_after('73d3928a', types)
json.dump(d, open('cur.json', 'w'))
json.dump({"types": types['id'], "features": feats['id']}, open('new_sections.json', 'w'))
print([ (e['id'], e.get('editor_settings', {}).get('title')) for e in d])
