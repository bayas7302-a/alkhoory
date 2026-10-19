#!/usr/bin/env python3
"""Create/refresh the theme's WordPress menus via REST and (when the Yutong UAE theme is active)
assign them to its menu locations. Page-object items get WP's current/ancestor classes for free.

The header menu keeps a "Request a Quote" item; theme 1.4+ hides it there because the header shows it as a button.
Re-run after a theme upload that adds menu locations, so the new locations get their menus.

usage: menus.py
"""
import html, json, os, subprocess
API = 'https://soharon.co.uk/yutong/wp-json/wp/v2'
AUTH = json.load(open(os.path.expanduser('~/.claude.json')))['mcpServers']['my-blog-elementor']['headers']['Authorization']
SITE = 'https://soharon.co.uk/yutong'

# (menu name, location, items); item = (title, page_id or None, url or None, [children])
M_ = SITE + '/models/#'
MODELS_SUB = [('Coaches', None, M_ + 'coaches', []), ('E-Coaches', None, M_ + 'e-coaches', []), ('City Buses', None, M_ + 'city-buses', []),
              ('E-Buses', None, M_ + 'e-buses', []), ('Vans', None, M_ + 'vans', [])]
SUPPORT_SUB = [('Service Centres', 645, None, []), ('Spare Parts', 647, None, []), ('Brochures', 649, None, [])]
NEWS_SUB = [('Press Releases', 13, None, []), ('Awards', 12, None, []), ('Media Library', 14, None, [])]
MENUS = [
    ('Header menu', 'yutong-primary', [('Home', 8, None, []), ('Models', 9, None, MODELS_SUB), ('Request a Quote', 641, None, []),
                                       ('About Us', 10, None, []), ('Ownership & Support', 15, None, SUPPORT_SUB),
                                       ('News', 13, None, NEWS_SUB), ('Contact Us', 11, None, [])]),
    ('Quick Links', 'yutong-footer-quick', [('About Us', 10, None, []), ('Contact Us', 11, None, []), ('Request a Quote', 641, None, [])]),
    ('Models', 'yutong-footer-1', [('All Models', 9, None, [])] + MODELS_SUB),
    ('Ownership & Support', 'yutong-footer-2', SUPPORT_SUB),
    ('News', 'yutong-footer-news', NEWS_SUB),
    ('Footer legal', 'yutong-legal', [('Privacy Policy', 670, None, []), ('Terms of Use', 652, None, [])]),
]


def rest(method, path, body=None):
    cmd = ['curl', '-sS', '-m', '120', '-X', method, '-H', 'Authorization: ' + AUTH, '-H', 'Content-Type: application/json', API + path]
    if body is not None:
        cmd += ['--data-binary', '@-']
    return json.loads(subprocess.run(cmd, input=json.dumps(body) if body is not None else None, capture_output=True, text=True).stdout or 'null')


def add_items(mid, items, parent=0):
    for order, (title, page, url, kids) in enumerate(items, 1):
        item = {'title': title, 'menus': mid, 'menu_order': order, 'status': 'publish', 'parent': parent}
        if page:
            item.update({'type': 'post_type', 'object': 'page', 'object_id': page})
        else:
            item.update({'type': 'custom', 'url': url})
        new = rest('POST', '/menu-items', item)
        if kids:
            add_items(mid, kids, new['id'])


locations = rest('GET', '/menu-locations') or {}
existing = {html.unescape(m['name']): m['id'] for m in rest('GET', '/menus?per_page=100&context=edit')}
used = {loc for _, loc, _ in MENUS}
for name, mid in existing.items():  # retire menus no longer in MENUS (e.g. old footer columns)
    if name not in [m[0] for m in MENUS]:
        rest('DELETE', '/menus/%d?force=true' % mid)
        print('removed menu', name)
for name, loc, items in MENUS:
    body = {'name': name, 'locations': [loc] if loc in locations else []}
    mid = existing.get(name)
    mid = rest('POST', '/menus/%d' % mid if mid else '/menus', body)['id']
    for old in rest('GET', '/menu-items?menus=%d&per_page=100&context=edit' % mid):
        rest('DELETE', '/menu-items/%d?force=true' % old['id'])
    add_items(mid, items)
    print('%-22s id %-4s items %d  location %s' % (name, mid, len(items), loc if loc in locations else '(not registered by active theme)'))
