#!/usr/bin/env python3
"""Inject classic Elementor widgets into atomic placeholder elements (by navigator title).

The MCP composer only speaks atomic (V4) widgets, so things like the Contact Form 7
shortcode and page-level <style>/<script> go into an empty e-div-block 'slot' that is
filled here by editing _elementor_data through the WP REST API.

usage: inject.py <post_id> <slot title> <widget json file>   (replaces the slot's children)
widget json: [{"widgetType": "shortcode", "settings": {"shortcode": "..."}}, ...]
"""
import sys, json, subprocess, os, uuid
AUTH = json.load(open(os.path.expanduser('~/.claude.json')))['mcpServers']['my-blog-elementor']['headers']['Authorization']
API = 'https://soharon.co.uk/yutong/wp-json/wp/v2/pages/%s'


def rest(method, url, body=None):
    cmd = ['curl', '-sS', '-m', '120', '-X', method, '-H', 'Authorization: ' + AUTH, '-H', 'Content-Type: application/json', url]
    if body is not None:
        cmd += ['--data-binary', '@-']
    return json.loads(subprocess.run(cmd, input=json.dumps(body) if body is not None else None, capture_output=True, text=True, check=True).stdout)


def main(pid, slot, wfile):
    page = rest('GET', API % pid + '?context=edit')
    data = json.loads(page['meta']['_elementor_data'])
    widgets = json.load(open(wfile))
    hits = []

    def walk(els):
        for e in els:
            if e.get('editor_settings', {}).get('title') == slot:
                e['elements'] = [{'id': uuid.uuid4().hex[:7], 'elType': 'widget', 'widgetType': w['widgetType'],
                                  'settings': w.get('settings', {}), 'elements': []} for w in widgets]
                hits.append(e['id'])
            walk(e.get('elements', []))

    walk(data)
    if not hits:
        sys.exit('slot not found: ' + slot)
    r = rest('POST', API % pid, {'meta': {'_elementor_data': json.dumps(data)}})
    print('injected into', hits, 'page', r.get('id'))


main(*sys.argv[1:4])
