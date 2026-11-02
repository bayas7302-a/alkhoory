# Old UAE landing page: pricing cards into one carousel row, portfolio grid -> laptop carousel.
import json, random, sys
F, PRICING, PORTFOLIO = sys.argv[1], sys.argv[2], sys.argv[3]
d = json.load(open(F))
def find(es, i):
    for e in es:
        if e['id'] == i: return e
        r = find(e.get('elements', []), i)
        if r: return r
def rid(): return '%07x' % random.getrandbits(28)
def html(title, code): return {"id": rid(), "elType": "widget", "widgetType": "html", "elements": [], "settings": {"_title": title, "html": code}}
sec, row1 = find(d, 'bf6a4e0'), find(d, 'd634fa7')
row2 = find(d, '37541db')
if row2:
    row1['elements'] += row2['elements']
    sec['elements'] = [e for e in sec['elements'] if e['id'] != '37541db']
sec['elements'] = [e for e in sec['elements'] if e.get('settings', {}).get('_title') != 'Pricing carousel']
sec['elements'].append(html('Pricing carousel', open(PRICING).read()))
port = find(d, '061b13d')
out = []
for e in port['elements']:
    if e['id'] == '72af16a' or e.get('settings', {}).get('_title') in ('Portfolio data', 'Portfolio carousel'):
        if e['id'] == '72af16a' or e['settings']['_title'] == 'Portfolio carousel':
            out.append({"id": rid(), "elType": "widget", "widgetType": "shortcode", "elements": [], "settings": {"_title": "Portfolio data", "shortcode": "[portfolio_carousel]"}})
            out.append(html('Portfolio carousel', open(PORTFOLIO).read()))
        continue
    out.append(e)
port['elements'] = out
json.dump(d, open(F, 'w'))
print([c['id'] for c in row1['elements']], [(e['id'], e.get('widgetType')) for e in port['elements']])
