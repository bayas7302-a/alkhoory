#!/usr/bin/env python3
"""Print the text content of a page's live Elementor data, in order (titles/paragraphs/button text)."""
import json, os, subprocess, sys
AUTH = json.load(open(os.path.expanduser('~/.claude.json')))['mcpServers']['my-blog-elementor']['headers']['Authorization']
pid = sys.argv[1]
d = json.loads(subprocess.run(['curl', '-sS', '-H', 'Authorization: ' + AUTH, 'https://soharon.co.uk/yutong/wp-json/wp/v2/pages/%s?context=edit' % pid], capture_output=True, text=True).stdout)
data = json.loads(d['meta']['_elementor_data'])
def val(v):
    if isinstance(v, dict):
        v = v.get('value', v)
        if isinstance(v, dict):
            return v.get('content') or v.get('value') or ''
    return v if isinstance(v, str) else ''
def walk(els, depth=0):
    for e in els:
        s = e.get('settings', {})
        title = e.get('editor_settings', {}).get('title', '')
        for k in ('title', 'paragraph', 'text', 'editor', 'html', 'shortcode'):
            if k in s:
                t = val(s[k])
                if t:
                    print('%s[%s] %s' % ('  ' * min(depth, 3), title or e.get('widgetType'), str(t)[:300].replace('\n', ' ')))
        if e.get('elType') == 'widget' and e.get('widgetType', '').startswith(('text-editor', 'heading', 'button', 'image')) and not title:
            print('  CLASSIC', e.get('widgetType'))
        walk(e.get('elements', []), depth + 1)
walk(data)
