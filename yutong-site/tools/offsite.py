#!/usr/bin/env python3
"""List every link / media URL on the live site that leaves soharon.co.uk (main pages + article pages)."""
import json, subprocess, re, os, collections
AUTH = json.load(open(os.path.expanduser('~/.claude.json')))['mcpServers']['my-blog-elementor']['headers']['Authorization']
pages = json.loads(subprocess.run(['curl', '-sS', '-H', 'Authorization: ' + AUTH, 'https://soharon.co.uk/yutong/wp-json/wp/v2/pages?per_page=100&status=publish&_fields=link'], capture_output=True, text=True).stdout)
hits = collections.defaultdict(set)
broken = []
for pg in pages:
    url = pg['link']
    html = subprocess.run(['curl', '-sS', '-L', url + '?off=1'], capture_output=True, text=True).stdout
    body = html[html.find('<body'):]
    for attr, u in re.findall(r'(href|src|poster|data-settings)="([^"]+)"', body):
        for v in re.findall(r'https?:[^"\'\s&]+', u.replace('\\/', '/')):
            if 'soharon.co.uk' not in v and 'gmpg.org' not in v and 'w.org' not in v:
                hits[v].add(url.replace('https://soharon.co.uk/yutong', ''))
    for v in set(re.findall(r'href="(https://soharon\.co\.uk/yutong/[^"#?]*)', body)):
        pass
print(len(pages), 'pages scanned')
for v, where in sorted(hits.items()):
    print(v, '<-', ', '.join(sorted(where))[:120])
