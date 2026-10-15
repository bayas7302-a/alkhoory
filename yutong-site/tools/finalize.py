#!/usr/bin/env python3
"""Fill the classic-widget slots on a built page: enquiry form, anchors, page CSS.

usage: finalize.py <post_id>
Fills 'Quote Form Slot' (and 'Contact Form Slot' if present) with the CF7 form + its CSS,
and every 'Anchor <name>' div with <span id="<name>">.
"""
import sys, json, subprocess, os, uuid
HERE = os.path.dirname(os.path.abspath(__file__))
AUTH = json.load(open(os.path.expanduser('~/.claude.json')))['mcpServers']['my-blog-elementor']['headers']['Authorization']
API = 'https://soharon.co.uk/yutong/wp-json/wp/v2/pages/%s'
CF7 = '[contact-form-7 id="44" title="Yutong Enquiry"]'
FORM_CSS = open(os.path.join(HERE, 'form.css')).read()
# Contact page uses the fuller Figma form (phone, model, seats, consent) -- CF7 form 243
CF7_CONTACT = '[contact-form-7 id="243" title="Yutong Contact"]'
CONTACT_CSS = open(os.path.join(HERE, 'form-contact.css')).read()


def rest(method, url, body=None):
    cmd = ['curl', '-sS', '-m', '120', '-X', method, '-H', 'Authorization: ' + AUTH, '-H', 'Content-Type: application/json', url]
    if body is not None:
        cmd += ['--data-binary', '@-']
    return json.loads(subprocess.run(cmd, input=json.dumps(body) if body is not None else None, capture_output=True, text=True, check=True).stdout)


def widget(kind, **settings):
    return {'id': uuid.uuid4().hex[:7], 'elType': 'widget', 'widgetType': kind, 'settings': settings, 'elements': []}


def main(pid):
    page = rest('GET', API % pid + '?context=edit')
    data = json.loads(page['meta']['_elementor_data'])
    done = []

    def walk(els):
        for e in els:
            t = e.get('editor_settings', {}).get('title', '')
            if t == 'Quote Form Slot':
                e['elements'] = [widget('shortcode', shortcode='<style>%s</style>%s' % (FORM_CSS, CF7))]
                done.append(t)
            elif t == 'Contact Form Slot':
                e['elements'] = [widget('shortcode', shortcode='<style>%s%s</style><div class="yt-contact-form">%s</div>' % (FORM_CSS, CONTACT_CSS, CF7_CONTACT))]
                done.append(t)
            elif t.startswith('Anchor '):
                e['elements'] = [widget('html', html='<span id="%s"></span>' % t[7:])]
                done.append(t)
            walk(e.get('elements', []))

    walk(data)
    rest('POST', API % pid, {'meta': {'_elementor_data': json.dumps(data)}})
    print(pid, 'filled:', ', '.join(done) or 'nothing')


for pid in sys.argv[1:]:
    main(pid)
# REST meta writes don't invalidate Elementor's generated atomic CSS; clear it so it rebuilds
subprocess.run(['curl', '-sS', '-o', '/dev/null', '-X', 'DELETE', '-H', 'Authorization: ' + AUTH, 'https://soharon.co.uk/yutong/wp-json/elementor/v1/cache'])
