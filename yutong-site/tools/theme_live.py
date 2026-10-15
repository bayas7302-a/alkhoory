#!/usr/bin/env python3
"""Switch the site to the Yutong UAE theme's common header/footer.

Run after theme/yutong-uae.zip has been uploaded and activated (Appearance > Themes > Add New > Upload):
  1. checks the active theme is yutong-uae,
  2. assigns the menus to the theme locations (menus.py),
  3. sets every page (main pages + news/award articles) to the "Elementor Full Width" template,
  4. rebuilds every page without its own header/footer, then runs the site checks.
"""
import json, os, subprocess, sys
HERE = os.path.dirname(os.path.abspath(__file__))
API = 'https://soharon.co.uk/yutong/wp-json/wp/v2'
AUTH = json.load(open(os.path.expanduser('~/.claude.json')))['mcpServers']['my-blog-elementor']['headers']['Authorization']
PAGES = ['home', 'models', 'about', 'contact', 'awards', 'news', 'library', 'ownership', 'articles']


def rest(method, path, body=None):
    cmd = ['curl', '-sS', '-m', '120', '-X', method, '-H', 'Authorization: ' + AUTH, '-H', 'Content-Type: application/json', API + path]
    if body is not None:
        cmd += ['--data-binary', '@-']
    return json.loads(subprocess.run(cmd, input=json.dumps(body) if body is not None else None, capture_output=True, text=True).stdout)


active = [t['stylesheet'] for t in rest('GET', '/themes') if t['status'] == 'active']
if active != ['yutong-uae']:
    sys.exit('Active theme is %s -- upload and activate theme/yutong-uae.zip first.' % active)

subprocess.run([sys.executable, os.path.join(HERE, 'menus.py')], check=True)

pages = rest('GET', '/pages?per_page=100&status=publish&context=edit&_fields=id,slug,template')
for pg in pages:
    if pg['template'] in ('elementor_canvas', '') and pg['slug'] not in ('sample-page',):
        rest('POST', '/pages/%d' % pg['id'], {'template': 'elementor_header_footer'})
        print('template ->', pg['slug'])

for name in PAGES:
    r = subprocess.run([sys.executable, os.path.join(HERE, 'run.py'), name, '--no-shot'], capture_output=True, text=True)
    bad = [l for l in r.stdout.splitlines() if 'not supported' in l or 'stopped' in l or 'ERROR' in l]
    print('rebuilt', name, 'issues:', len(bad))
subprocess.run([sys.executable, os.path.join(HERE, 'check.py')])
subprocess.run([sys.executable, os.path.join(HERE, 'rhythm.py')])
