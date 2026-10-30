import json, copy, random, re
d = json.load(open('old/d.json'))
def find(es, i, parent=None):
    for e in es:
        if e['id'] == i: return e, parent
        r = find(e.get('elements', []), i, e)
        if r: return r
def el(i): return find(d, i)[0]
def rid(): return '%07x' % random.getrandbits(28)
WA = 'https://wa.me/+971556138232'; TEL = 'tel:+971556138232'; MAIL = 'hello@heeandlee.ae'

SUP = '<sup id="rupee-sym" style="color: #54595F; font-weight: 700;">AED</sup>'
FROM = '<span style="display:block;font-size:14px;line-height:1.6;font-weight:600;color:#54595F;letter-spacing:1px;">STARTING FROM</span>'
plans = [  # (card title id, price id, features list id, title, price, tagline, features)
 ('2978cda','db067b7','a570e1e','<span id="ttext">Starter</span> Package','899','For businesses that simply need an online presence. Best for new businesses with tight budget.',
  ['One landing page','Responsive mobile design','Minimum 4 sections','WhatsApp integration','Form submission feature']),
 ('aae88d8','1b6a1c1','b5abe79','<span id="ttext">Business</span> Package','1,899','For businesses that need to look professional online. Best for new businesses, small service companies, consultants and professionals.',
  ['Up to 3 pages','Custom website design','Responsive mobile design','Basic on-page SEO','WhatsApp integration','Free website hosting server for 1 year','Professional email account (3 IDs)']),
 ('66f631b','7af2d0f','86eb44e','<span id="ttext">Growth</span> Package','2,899','A complete dynamic site with quality lead generation system. Best for growing businesses actively looking for organic leads and sales.',
  ['Up to 5 pages','Custom website design','Responsive mobile design','SEO for first 3 months','WhatsApp integration','Free website hosting server for 1 year','Professional email account (5 IDs)']),
 ('1f86cb0','b4302a5','c986228','<span style="color: #5E8E3E;">E-commerce</span> Package','3,999','A complete online store, designed and developed to sell. Best for brands selling online with a Shopify or WooCommerce store.',
  ['Up to 8 pages','Responsive mobile design','Payment integration setup','WhatsApp integration','Free website hosting server for 1 year','Professional email account (5 IDs)']),
]
for i, (tid, pid, lid, title, price, tag, feats) in enumerate(plans):
    el(tid)['settings']['title'] = title
    span = '<span style="color: #5E8E3E;">%s </span>' % price if i == 3 else '<span id="ttext">%s </span>' % price
    el(pid)['settings']['title'] = FROM + span + SUP
    L = el(lid)['settings']['icon_list']; tmpl = L[0]
    items = [dict(tmpl, text='<b>%s</b>' % tag, _id=rid())] + [dict(tmpl, text=f, _id=rid()) for f in feats]
    for it in items: it.pop('link', None)
    el(lid)['settings']['icon_list'] = items
# two rows of two: drop Plan E (WooCommerce 2499) and Plan F (Enterprise) - pricing now matches the 4 packages
row1, row2 = el('d634fa7'), el('37541db')
row2['elements'] = [c for c in row2['elements'] if c['id'] not in ('8b42444', '3d1092f')]
planC = [c for c in row1['elements'] if c['id'] == 'e2632dd'][0]
row1['elements'].remove(planC); row2['elements'].insert(0, planC)

# WhatsApp / call / email
el('5e2b91c')['settings']['link']['url'] = WA
fb = el('606db09')['settings']
h = fb['html']
h = h.replace('href="https://wa.me/+971551514803"', 'href="%s"' % WA).replace('href="tel:+971551514803"', 'href="%s"' % TEL)
h = h.replace('<a class="whatsapp-landing-page-web-uae" ', '<a id="whatsapp-landing-page-web-uae" class="whatsapp-landing-page-web-uae" data-track="whatsapp" rel="noopener" aria-label="Chat with us on WhatsApp"', 1)
h = h.replace('<a class="call-landing-page-web-uae" ', '<a id="call-landing-page-web-uae" class="call-landing-page-web-uae" data-track="call" aria-label="Call us on +971 55 613 8232" ', 1)
h = h.replace(' href="tel:+971556138232"  target="_blank"', ' href="tel:+971556138232"')
if 'floating_whatsapp_click' not in h:
    h += '''
<script nowprocket>
(function(){ var last={};
  document.addEventListener('click',function(e){
    var a=e.target.closest&&e.target.closest('a#whatsapp-landing-page-web-uae, a#call-landing-page-web-uae'); if(!a) return;
    var now=Date.now(); if(last[a.id]&&now-last[a.id]<800) return; last[a.id]=now;
    window.dataLayer=window.dataLayer||[];
    window.dataLayer.push({event:a.dataset.track==='whatsapp'?'floating_whatsapp_click':'floating_call_click',button_id:a.id,link_url:a.href,page_path:location.pathname});
  });
})();
</script>'''
fb['html'] = h
L = el('984e0a4')['settings']['icon_list']
L[0]['text'] = MAIL; L[0]['link']['url'] = 'mailto:' + MAIL
if not any('971' in x['text'] for x in L):
    L.insert(0, {"text": "+971 55 613 8232", "selected_icon": {"value": "fas fa-phone-alt", "library": "fa-solid"}, "_id": rid(),
                 "link": {"url": TEL, "is_external": "", "nofollow": "", "custom_attributes": ""}})

# contact form: same CF7 form as the new page (same fields, recipient and thank-you redirect)
el('9df76f9')['settings']['shortcode'] = '[contact-form-7 id="6134e6d" title="Website Landing Page New"]'
box = el('04e37bc')
if not any('hl-old-form' in c.get('settings', {}).get('html', '') for c in box['elements']):
    box['elements'].insert(box['elements'].index(el('9df76f9')) + 1,
        {"id": rid(), "elType": "widget", "widgetType": "html", "elements": [],
         "settings": {"_title": "Contact form setup", "html": open('old/form_setup.html').read()}})
else:
    for c in box['elements']:
        if 'hl-old-form' in c.get('settings', {}).get('html', ''): c['settings']['html'] = open('old/form_setup.html').read()
json.dump(d, open('old/d.json', 'w'))
json.dump({"meta": {"_elementor_data": json.dumps(d)}}, open('old/body.json', 'w'))
print('ok')
