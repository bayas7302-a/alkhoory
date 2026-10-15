#!/usr/bin/env python3
"""Create/refresh the theme's WordPress menus via REST and (when the Yutong UAE theme is active)
assign them to its menu locations. Page-object items get WP's current/ancestor classes for free.

usage: menus.py
"""
import html, json, os, subprocess
API = 'https://soharon.co.uk/yutong/wp-json/wp/v2'
AUTH = json.load(open(os.path.expanduser('~/.claude.json')))['mcpServers']['my-blog-elementor']['headers']['Authorization']
SITE = 'https://soharon.co.uk/yutong'

# (menu name, location, [(title, page_id or None, url for custom links)])
MENUS = [
    ('Header menu', 'yutong-primary', [('Home', 8, None), ('Models', 9, None), ('Technologies', None, SITE + '/models/#technology'),
                                       ('About', 10, None), ('News', 13, None), ('Contact', 11, None)]),
    ('Quick Links', 'yutong-footer-1', [('About Us', 10, None), ('Contact Us', 11, None), ('Media Library', 14, None)]),
    ('Models', 'yutong-footer-2', [('All Models', 9, None), ('Coaches', None, SITE + '/models/'), ('Vans', None, SITE + '/models/'), ('E-Buses', None, SITE + '/models/')]),
    ('Ownership & Support', 'yutong-footer-3', [('Service Centres', None, SITE + '/ownership-support/#service-network'),
                                                ('Spare Parts', None, SITE + '/ownership-support/#spare-parts')]),
    ('News', 'yutong-footer-4', [('Press Releases', 13, None), ('Awards', 12, None), ('Media Library', 14, None)]),
    ('Footer legal', 'yutong-legal', [('Your Privacy Choices', None, '#'), ('Cookie and Ad Preferences', None, '#'),
                                      ('Conditions of Use', None, '#'), ('Privacy Policy', None, '#')]),
]


def rest(method, path, body=None):
    cmd = ['curl', '-sS', '-m', '120', '-X', method, '-H', 'Authorization: ' + AUTH, '-H', 'Content-Type: application/json', API + path]
    if body is not None:
        cmd += ['--data-binary', '@-']
    return json.loads(subprocess.run(cmd, input=json.dumps(body) if body is not None else None, capture_output=True, text=True).stdout or 'null')


locations = rest('GET', '/menu-locations') or {}
existing = {html.unescape(m['name']): m['id'] for m in rest('GET', '/menus?per_page=100&context=edit')}
for name, loc, items in MENUS:
    body = {'name': name}
    if loc in locations:
        body['locations'] = [loc]
    mid = existing.get(name)
    mid = rest('POST', '/menus/%d' % mid if mid else '/menus', body)['id']
    for old in rest('GET', '/menu-items?menus=%d&per_page=100&context=edit' % mid):
        rest('DELETE', '/menu-items/%d?force=true' % old['id'])
    for order, (title, page, url) in enumerate(items, 1):
        item = {'title': title, 'menus': mid, 'menu_order': order, 'status': 'publish'}
        if page:
            item.update({'type': 'post_type', 'object': 'page', 'object_id': page})
        else:
            item.update({'type': 'custom', 'url': url})
        rest('POST', '/menu-items', item)
    print('%-22s id %-4s items %d  location %s' % (name, mid, len(items), loc if loc in locations else '(theme not active yet)'))
