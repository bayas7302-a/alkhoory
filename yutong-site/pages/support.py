"""Pages from the client copy doc: Request a Quote, Ownership & Support overview, Service Centres,
Spare Parts, Brochures, Privacy Policy, Terms of Use.

Build all: python3 tools/run.py support     (creates missing WP pages, then builds each one)
"""
import sys, os, json, subprocess
sys.path.insert(0, os.path.join(os.path.dirname(__file__), '..', 'tools'))
from parts import *
import models_data as M

HERE = os.path.dirname(os.path.abspath(__file__))
FORMS = json.load(open(os.path.join(HERE, '..', 'tools', 'forms.json')))
AUTH = json.load(open(os.path.expanduser('~/.claude.json')))['mcpServers']['my-blog-elementor']['headers']['Authorization']
API = 'https://soharon.co.uk/yutong/wp-json/wp/v2/pages'
OWNERSHIP = 15


def rest(method, url, body=None):
    cmd = ['curl', '-sS', '-m', '120', '-X', method, '-H', 'Authorization: ' + AUTH, '-H', 'Content-Type: application/json', url]
    if body is not None:
        cmd += ['--data-binary', '@-']
    return json.loads(subprocess.run(cmd, input=json.dumps(body) if body is not None else None, capture_output=True, text=True).stdout)


def ensure_page(parent, slug, title):
    found = rest('GET', '%s?slug=%s&parent=%d&status=publish,draft&context=edit' % (API, slug, parent))
    if found:
        pid = found[0]['id']
    else:
        r = subprocess.run([os.path.join(WORK, 'call.sh'), 'elementor-create-page', json.dumps({'title': title})], capture_output=True, text=True).stdout
        pid = json.loads(r)['id']
    rest('POST', '%s/%d' % (API, pid), {'parent': parent, 'slug': slug, 'title': title, 'status': 'publish',
                                         'template': 'elementor_header_footer'})
    return pid


# ---------- building blocks ----------

SECTION = 'padding: 110px 120px; flex-direction: column; gap: 44px; @media(--tablet) { padding: 80px 40px; } @media(--mobile) { padding: 64px 20px; gap: 32px; }'


def head(cid, eyebrow_text, heading, intro=None, center=False, light=False):
    kids = [eyebrow(cid + ' Eyebrow', eyebrow_text, '#ffffff' if light else '#0160b4', center=center),
            title(cid + ' Title', heading, 'font-weight: 700; color: %s;%s' % ('#ffffff' if light else '#00050b', ' text-align: center;' if center else ''))]
    for i, t in enumerate(intro or []):
        kids.append(body('%s Intro %d' % (cid, i + 1), t, 'max-width: 760px; line-height: 27px;%s' % (' text-align: center;' if center else ''),
                         'rgba(255,255,255,0.82)' if light else '#6b7280', 16))
    return flex(cid + ' Head', 'max-width: 820px; padding: 0; flex-direction: column; gap: 14px;%s' % (' align-items: center; align-self: center;' if center else ''), kids)


def form_section(cid, form_key, eyebrow_text, heading, intro, side=None, anchor_id=None, bg='#f2f6fb'):
    """Two columns: copy on the left, white form card on the right (stacks on tablet)."""
    left = [eyebrow(cid + ' Eyebrow', eyebrow_text), title(cid + ' Title', heading, 'font-weight: 700; color: #00050b;')]
    left += [body('%s Text %d' % (cid, i + 1), t, 'line-height: 27px;', '#6b7280', 16) for i, t in enumerate(intro)]
    left += side or []
    kids = [anchor(anchor_id)] if anchor_id else []
    kids.append(flex(cid + ' Grid', 'padding: 0; gap: 56px; align-items: flex-start; @media(--tablet) { flex-direction: column; gap: 36px; }', [
        flex(cid + ' Copy', 'padding: 0; flex: 1 1 0; flex-direction: column; gap: 18px; @media(--tablet) { flex: 0 0 auto; }', left),
        flex(cid + ' Card', 'width: 600px; max-width: 100%; flex: 0 0 auto; padding: 34px; flex-direction: column; gap: 16px; background-color: #ffffff; border-radius: 24px; box-shadow: 0 20px 40px rgba(0,26,64,0.08); @media(--tablet) { width: 100%; } @media(--mobile) { padding: 24px 18px; }', [
            slot('Form Slot %d' % FORMS[form_key]),
        ]),
    ]))
    return flex(cid, SECTION + ' position: relative; background-color: %s;' % bg, kids, tag='section')


def feature_cards(cid, items, cols=4, icon='ic-check'):
    cards = []
    for i, (t, d) in enumerate(items):
        n = '%s Card %d' % (cid, i + 1)
        cards.append(flex(n, 'padding: 30px; flex: 1 1 0; flex-direction: column; gap: 14px; border-radius: 22px; background-color: #ffffff; border: 1px solid #e3e9f1; @media(--tablet) { flex: 1 1 40%; } @media(--mobile) { flex: 0 0 auto; }', [
            flex(n + ' Badge', 'width: 44px; min-width: 44px; height: 44px; padding: 0; border-radius: 22px; background-color: #e3effa; justify-content: center; align-items: center;', [img(n + ' Icon', icon, 'width: 14px;')]),
            h(n + ' Title', t, 'h3', FD + 'font-size: 19px; font-weight: 600; line-height: 26px; color: #00050b; margin: 0;'),
            body(n + ' Text', d, 'line-height: 24px;', '#6b7280', 14),
        ]).c('yt-lift'))
    return flex(cid + ' Cards', 'padding: 0; gap: 24px; align-items: stretch; @media(--tablet) { flex-wrap: wrap; } @media(--mobile) { flex-direction: column; }', cards)


def link_card(cid, heading, text, label, href, photo):
    return flex(cid, 'padding: 0; flex: 1 1 0; flex-direction: column; border-radius: 24px; overflow: hidden; background-color: #ffffff; border: 1px solid #e3e9f1; @media(--mobile) { flex: 0 0 auto; }', [
        div(cid + ' Media', 'padding: 0; height: 220px;', [img(cid + ' Photo', photo, 'width: 100%; height: 220px; object-fit: cover;')], href=href).c('yt-zoom'),
        flex(cid + ' Body', 'padding: 28px; flex-direction: column; gap: 14px; flex-grow: 1;', [
            h(cid + ' Title', heading, 'h3', FD + 'font-size: 22px; font-weight: 600; line-height: 30px; color: #00050b; margin: 0;', href=href),
            body(cid + ' Text', text, 'line-height: 25px;', '#6b7280', 15),
            flex(cid + ' Btn Wrap', 'padding: 6px 0 0; margin-top: auto;', [btn(cid + ' Btn', label, href, kind='outline-blue')]),
        ]),
    ]).c('yt-lift')


def contact_strip(cid, heading, text):
    return flex(cid, 'padding: 0 120px 110px; background-color: #f2f6fb; @media(--tablet) { padding: 0 40px 80px; } @media(--mobile) { padding: 0 20px 64px; }', [
        flex(cid + ' Card', 'padding: 40px 48px; gap: 32px; justify-content: space-between; align-items: center; border-radius: 28px; background-color: #072b52; background-image: linear-gradient(90deg, #072b52 0%, #0160b4 100%); @media(--tablet) { flex-direction: column; align-items: flex-start; } @media(--mobile) { padding: 30px 24px; }', [
            flex(cid + ' Text', 'padding: 0; flex: 1 1 0; flex-direction: column; gap: 10px;', [
                h(cid + ' Title', heading, 'h2', FD + 'font-size: 28px; font-weight: 600; line-height: 36px; color: #ffffff; margin: 0;'),
                body(cid + ' Body', text, 'line-height: 26px;', 'rgba(255,255,255,0.85)', 16),
            ]),
            btn(cid + ' Btn', 'Contact Us', URL['contact'], kind='white'),
        ]),
    ], tag='section')


# ---------- Request a Quote ----------

def quote_page():
    return [
        ('quote-1', lambda: page_hero('quote', [('Home', 'home'), ('Request a Quote', None)], 'Request a Quote',
                                       'Tell us about your passenger transport requirements and the Yutong UAE team will help you find the right vehicle.',
                                       'supplier-bg', 520)),
        ('quote-2', lambda: [form_section('Quote Form', 'quote', 'Request a Quote', 'Find the Right Yutong for ' + hl('Your Operation'), [
            'Whether you need a coach for tourism, a bus for daily transportation, or an electric solution for future-ready mobility, our team can help identify the Yutong model suited to your operational requirements.',
            'Share your requirements below, and a member of the Yutong UAE team will get in touch with you.'], anchor_id='request-a-quote'),
            contact_strip('Quote Direct', 'Prefer to speak with us directly?',
                          'Get in touch with the Yutong UAE sales team for assistance with model selection, fleet requirements, and sales enquiries.')]),
    ]


# ---------- Ownership & Support overview ----------

def ownership_page():
    return [
        ('own2-1', lambda: page_hero('ownership', [('Home', 'home'), ('Ownership & Support', None)], 'Ownership &amp; ' + hl('Support'),
                                      'Reliable transport depends on more than the vehicle itself. Yutong customers in the UAE have access to dedicated aftersales facilities, experienced technical teams, and genuine spare-parts support through Al Khoory Automobiles.',
                                      'own-hero', 560)),
        ('own2-2', lambda: [flex('Support Links', SECTION + ' background-color: #ffffff;', [
            head('Support Links', 'Ownership &amp; Support', 'Support That Keeps Your ' + hl('Fleet Moving.'), center=True),
            flex('Support Links Grid', 'padding: 0; gap: 24px; align-items: stretch; @media(--tablet) { flex-wrap: wrap; } @media(--mobile) { flex-direction: column; }', [
                link_card('Support Service', 'Service Centres', 'Dedicated Yutong service support in Dubai and Abu Dhabi for scheduled maintenance, repairs, and technical requirements.', 'Find a Service Centre', URL['service'], 'own-support'),
                link_card('Support Parts', 'Genuine Spare Parts', 'Access genuine Yutong parts designed to support vehicle quality, safety and long-term performance.', 'Explore Spare Parts', URL['parts'], 'own-warranty'),
                link_card('Support Brochures', 'Brochures', 'Access brochures, specifications and information across the Yutong UAE model range.', 'View Brochures', URL['brochures'], 'model-t12e'),
            ]),
        ], tag='section')]),
    ]


# ---------- Service Centres ----------

LOCATIONS = [
    ('Dubai', 'Al Manama Street, Ras Al Khor Industrial Area 1, Dubai, UAE', '+971 4 4039900', 'yklservice@alkhoory.com'),
    ('Abu Dhabi', 'Al Khoory Automobiles, 18 - 90 4th Street, Musaffah, ICAD I, Abu Dhabi, UAE', '+971 2 5568144', 'sm.auh@alkhooryauto.com'),
]


def location_card(i, city, address, tel, email):
    n = 'Location %d' % i
    line = lambda k, ic, t, href=None: flex('%s %s' % (n, k), 'padding: 0; gap: 12px; align-items: flex-start;', [
        flex('%s %s Icon' % (n, k), 'width: 36px; min-width: 36px; height: 36px; padding: 0; border-radius: 18px; background-color: #e3effa; justify-content: center; align-items: center;', [img('%s %s Glyph' % (n, k), ic, 'width: 16px;')]),
        p('%s %s Text' % (n, k), t, FB + 'flex: 1 1 0; font-size: 15px; line-height: 24px; color: #323232; margin: 0; padding-top: 6px; &:hover { color: #0160b4; }', href=href)])
    return flex(n, 'padding: 34px; flex: 1 1 0; flex-direction: column; gap: 18px; border-radius: 24px; background-color: #ffffff; border: 1px solid #e3e9f1; box-shadow: 0 10px 28px rgba(0,26,64,0.06); @media(--mobile) { flex: 0 0 auto; padding: 26px; }', [
        h(n + ' City', city, 'h3', FD + 'font-size: 26px; font-weight: 700; color: #0160b4; margin: 0;'),
        line('Address', 'ft-pin', address),
        line('Tel', 'ft-phone', 'Tel: ' + tel, 'tel:' + tel.replace(' ', '')),
        line('Email', 'ft-mail', 'Email: ' + email, 'mailto:' + email),
    ])


def service_page():
    return [
        ('svc-1', lambda: page_hero('ownership', [('Home', 'home'), ('Ownership & Support', 'ownership'), ('Service Centres', None)], 'Service ' + hl('Centres'),
                                     'Dedicated Yutong service support in Dubai and Abu Dhabi for scheduled maintenance, repairs, and technical requirements.',
                                     'own-support', 520)),
        ('svc-2', lambda: [flex('Service Intro', SECTION + ' background-color: #ffffff;', [
            head('Service Intro', 'Service Centres', 'Support When Your Fleet ' + hl('Needs It'), [
                'Keeping your vehicles operational is essential for uninterrupted passenger transportation.',
                'Through Al Khoory Automobiles, Yutong UAE provides dedicated service support through trained technical teams and authorised facilities, helping customers maintain vehicle performance, reliability, and safety throughout the ownership journey.',
                'Whether you require scheduled maintenance, technical assistance, or vehicle support, our service teams are ready to assist.']),
        ], tag='section'),
            flex('Service Locations', SECTION + ' position: relative; background-color: #f2f6fb;', [
                anchor('find-a-service-centre'),
                head('Service Locations', 'Find a Service Centre', 'Locations Across the ' + hl('UAE'),
                     ['Visit one of our dedicated service facilities for professional maintenance and technical support for your Yutong vehicle.']),
                flex('Service Locations Grid', 'padding: 0; gap: 24px; align-items: stretch; @media(--mobile) { flex-direction: column; }',
                     [location_card(i + 1, *loc) for i, loc in enumerate(LOCATIONS)]),
            ], tag='section')]),
        ('svc-3', lambda: [form_section('Service Booking', 'service', 'Service Booking', 'Schedule Your ' + hl('Service'), [
            'Request a service appointment with the Yutong UAE team.',
            'Complete the form below and our team will contact you to confirm your service requirements and appointment details.'], anchor_id='service-booking', bg='#ffffff')]),
    ]


# ---------- Spare Parts ----------

PARTS_FEATURES = [
    ('Genuine Components', 'Parts developed and supplied to maintain the quality and performance standards of Yutong vehicles.'),
    ('Vehicle Compatibility', 'Components designed specifically for Yutong buses, coaches, and vans.'),
    ('Technical Assistance', 'Support from experienced teams to help identify the correct parts for your requirements.'),
    ('Long-Term Reliability', 'Helping operators maintain vehicle performance and minimise operational interruptions.'),
]


def parts_page():
    return [
        ('parts-1', lambda: page_hero('ownership', [('Home', 'home'), ('Ownership & Support', 'ownership'), ('Spare Parts', None)], 'Genuine Parts. ' + hl('Reliable Performance.'),
                                       'Keeping a transport fleet running efficiently requires quality components and dependable parts support.',
                                       'own-warranty', 520)),
        ('parts-2', lambda: [flex('Parts Intro', SECTION + ' background-color: #ffffff;', [
            head('Parts Intro', 'Spare Parts', 'Genuine Parts. ' + hl('Reliable Performance.'), [
                'Yutong UAE provides access to genuine spare parts through Al Khoory Automobiles, helping customers maintain vehicle performance, safety, and long-term reliability.',
                'Designed specifically for Yutong vehicles, genuine parts help ensure compatibility, quality, and confidence throughout the ownership journey.']),
        ], tag='section'),
            flex('Parts Support', SECTION + ' background-color: #f2f6fb;', [
                head('Parts Support', 'Supporting Your Fleet Needs', 'Parts Support You Can ' + hl('Rely On'), [
                    'From routine replacements to maintenance requirements, Yutong UAE supports operators with access to genuine components for the complete vehicle range.',
                    'Our team can assist with identifying the right parts for your vehicle and support your requirements throughout the vehicle lifecycle.']),
                feature_cards('Parts Features', PARTS_FEATURES),
            ], tag='section')]),
        ('parts-3', lambda: [form_section('Parts Enquiry', 'parts', 'Request Spare Parts', 'Need a Specific ' + hl('Part?'), [
            'Share your requirements with the Yutong UAE team, and our specialists will assist you with your spare parts enquiry.'], anchor_id='parts-enquiry', bg='#ffffff')]),
    ]


# ---------- Brochures ----------

def brochure_card(i, m):
    n = 'Brochure %s %d' % (m['slug'], i)
    return flex(n, 'padding: 0; flex: 1 1 0; min-width: 0; flex-direction: column; border-radius: 22px; overflow: hidden; background-color: #ffffff; border: 1px solid #e3e9f1; @media(--tablet) { flex: 1 1 40%; } @media(--mobile) { flex: 0 0 auto; }', [
        flex(n + ' Media', 'height: 190px; padding: 18px; justify-content: center; align-items: center; background-color: #f2f6fb;', [
            img(n + ' Photo', m['image'], 'width: 100%; height: 150px; object-fit: contain;')]),
        flex(n + ' Body', 'padding: 24px 26px 28px; flex-direction: column; gap: 10px; flex-grow: 1;', [
            p(n + ' Category', M.label(m['category']).upper(), FB + 'font-size: 11px; font-weight: 600; letter-spacing: 1.3px; color: #0160b4; margin: 0;'),
            h(n + ' Name', m['name'], 'h3', FD + 'font-size: 24px; font-weight: 700; color: #003376; margin: 0;'),
            p(n + ' Tagline', '<em>%s</em>' % m['tagline'], FB + 'font-size: 14px; line-height: 22px; color: #6b7280; margin: 0;'),
            flex(n + ' Btn Wrap', 'padding: 8px 0 0; margin-top: auto;', [btn(n + ' Btn', 'Download Brochure', '#', kind='outline-blue')]),
        ]),
    ]).c('yt-lift')


def brochure_grid(cid, items):
    rows = []
    for r in range(0, len(items), 3):
        chunk = items[r:r + 3]
        cards = [brochure_card(r + i + 1, m) for i, m in enumerate(chunk)]
        cards += [div('%s Spacer %d %d' % (cid, r, k), 'padding: 0; flex: 1 1 0; min-width: 0; @media(--tablet) { display: none; }') for k in range(3 - len(chunk))]
        rows.append(flex('%s Row %d' % (cid, r // 3 + 1), 'padding: 0; gap: 24px; align-items: stretch; @media(--tablet) { flex-wrap: wrap; } @media(--mobile) { flex-direction: column; }', cards))
    return flex(cid, 'padding: 0; flex-direction: column; gap: 24px;', rows)


def brochures_page():
    tabs = [('All', None)] + [(c[1], c[0]) for c in M.CATEGORIES]

    def tabs_widget():
        menu = [Node('e-tab', 'Brochure Tab ' + lbl, {}, FB + 'width: auto; padding: 11px 22px; border-width: 0; border-radius: 34px; background-color: transparent; font-size: 14px; color: #323232; cursor: pointer; @media(--mobile) { padding: 9px 14px; }',
                     [p('Brochure Tab %s Label' % lbl, lbl, FB + 'font-size: 14px; margin: 0;')]).c('yt-tab') for lbl, _ in tabs]
        panels = [Node('e-tab-content', 'Brochure Panel ' + lbl, {}, 'padding: 0;', [brochure_grid('Brochure Grid ' + lbl, M.models(key))]) for lbl, key in tabs]
        return Node('e-tabs', 'Brochure Tabs', {}, 'padding: 0; gap: 40px; align-items: center; @media(--mobile) { gap: 28px; }', [
            Node('e-tabs-menu', 'Brochure Tabs Menu', {}, 'width: auto; max-width: 100%; padding: 6px; gap: 4px; display: flex; flex-wrap: wrap; justify-content: center; background-color: #f2f6fb; border-radius: 40px; @media(--mobile) { border-radius: 24px; }', menu),
            Node('e-tabs-content-area', 'Brochure Tabs Content', {}, 'width: 100%; padding: 0;', panels),
        ])
    return [
        ('bro-1', lambda: page_hero('ownership', [('Home', 'home'), ('Ownership & Support', 'ownership'), ('Brochures', None)], 'Explore the Yutong ' + hl('Range'),
                                     'Access brochures and detailed product information for Yutong buses, coaches, electric vehicles, and passenger vans available in the UAE.',
                                     'library-hero', 520)),
        ('bro-2', lambda: [flex('Brochures', SECTION + ' background-color: #ffffff;', [
            head('Brochures', 'Brochures', 'Explore the Yutong ' + hl('Range'), [
                'Explore vehicle specifications, features, and configurations to find the right solution for your transport requirements.'], center=True),
            tabs_widget(),
        ], tag='section')]),
    ]


# ---------- Legal (standard content, to be reviewed by the client) ----------

PRIVACY = [
    ('Introduction', ['Al Khoory Automobiles LLC ("we", "us", "our") operates this website for Yutong UAE. This Privacy Policy explains how we collect, use and protect personal information when you visit the website or contact us through it.']),
    ('Information We Collect', ['Information you provide: when you submit a form (for example a quote request, service booking, spare parts enquiry or contact message) we collect the details you enter, such as your name, company, job title, email address, phone number, vehicle details and message.',
                                'Information collected automatically: like most websites, our servers record technical information such as your IP address, browser type, pages visited and the date and time of your visit. Cookies may be used to keep the website working properly and to understand how it is used.']),
    ('How We Use Your Information', ['We use personal information to respond to your enquiries, prepare quotations, arrange service appointments and spare parts, provide customer support, improve our website and services, and meet our legal and regulatory obligations.',
                                     'We do not sell your personal information.']),
    ('Sharing Your Information', ['We may share information with companies within Al Khoory Group, with Yutong where needed to fulfil your request, and with trusted service providers who help us operate the website and our business. These parties may only use the information for the purposes described here. We may also disclose information where required by law.']),
    ('Data Retention and Security', ['We keep personal information only for as long as necessary for the purposes described in this policy or as required by law, and we use reasonable technical and organisational measures to protect it against loss, misuse and unauthorised access.']),
    ('Your Rights', ['Subject to applicable law, you may request access to, correction of, or deletion of your personal information, or object to certain processing. To make a request, contact us using the details below.']),
    ('Cookies', ['You can set your browser to refuse cookies or to alert you when cookies are being sent. Some parts of the website may not work properly if cookies are disabled.']),
    ('Changes to This Policy', ['We may update this Privacy Policy from time to time. The latest version will always be available on this page.']),
    ('Contact Us', ['If you have any questions about this Privacy Policy, please contact us at info@subaru-uae.com or visit our Contact page.']),
]

TERMS = [
    ('Acceptance of Terms', ['By accessing or using this website, operated by Al Khoory Automobiles LLC for Yutong UAE, you agree to these Terms of Use. If you do not agree, please do not use the website.']),
    ('Use of the Website', ['You may use this website for lawful purposes only. You must not use it in any way that could damage the website, interfere with its operation, or infringe the rights of others.']),
    ('Product Information', ['Vehicle specifications, features, images and other product information on this website are provided for general information only and may vary by market, configuration and model year. Images may show optional equipment. Please contact our team to confirm specifications, availability and pricing before making a decision. Nothing on this website constitutes a binding offer.']),
    ('Intellectual Property', ['The content of this website, including text, images, logos and trademarks, is owned by or licensed to Al Khoory Automobiles LLC, Yutong or their respective owners, and may not be copied, reproduced or distributed without prior written permission.']),
    ('Enquiries and Forms', ['Information you submit through the website must be accurate and complete. Submitting a quote request, service booking or enquiry does not create a contract; our team will contact you to confirm details.']),
    ('Third-Party Links', ['The website may contain links to third-party websites or services that we do not control. We are not responsible for their content or practices.']),
    ('Limitation of Liability', ['To the extent permitted by law, we are not liable for any loss or damage arising from the use of, or inability to use, this website or from reliance on its content. The website is provided on an "as is" and "as available" basis.']),
    ('Changes to These Terms', ['We may update these Terms of Use from time to time. Continued use of the website after changes are posted means you accept the updated terms.']),
    ('Governing Law', ['These Terms of Use are governed by the laws of the United Arab Emirates and the Emirate of Dubai, and the courts of Dubai shall have jurisdiction over any dispute.']),
    ('Contact Us', ['For questions about these Terms of Use, please contact us at info@subaru-uae.com.']),
]


def legal_page(key, heading, intro, blocks):
    def content():
        kids = [p('Legal Updated', 'Last updated: October 2026', FB + 'font-size: 13px; color: #747474; margin: 0;')]
        for i, (h2, paras) in enumerate(blocks):
            kids.append(h('Legal H %d' % (i + 1), h2, 'h2', FD + 'font-size: 24px; font-weight: 600; line-height: 32px; color: #00050b; margin: 12px 0 0;'))
            kids += [body('Legal P %d %d' % (i + 1, k + 1), t, 'line-height: 28px;', '#4b5563', 16) for k, t in enumerate(paras)]
        return [flex('Legal', SECTION + ' align-items: center; background-color: #ffffff;', [
            flex('Legal Column', 'max-width: 820px; padding: 0; flex-direction: column; gap: 16px;', kids)], tag='section')]
    return [
        ('%s-1' % key, lambda: page_hero('about', [('Home', 'home'), (heading, None)], heading, intro, 'contact-hero', 440)),
        ('%s-2' % key, content),
    ]


def PAGES():
    out = []
    spec = [
        (0, 'request-a-quote', 'Request a Quote', quote_page),
        (0, 'ownership-support', 'Ownership & Support', ownership_page),
        (OWNERSHIP, 'service-centres', 'Service Centres', service_page),
        (OWNERSHIP, 'spare-parts', 'Spare Parts', parts_page),
        (OWNERSHIP, 'brochures', 'Brochures', brochures_page),
        (0, 'privacy-policy', 'Privacy Policy', lambda: legal_page('privacy', 'Privacy Policy', 'How Yutong UAE collects, uses and protects your personal information.', PRIVACY)),
        (0, 'terms-of-use', 'Terms of Use', lambda: legal_page('terms', 'Terms of Use', 'The terms that apply when you use the Yutong UAE website.', TERMS)),
    ]
    only = [a for a in sys.argv if a.startswith('--only=')]
    for parent, slug, title_, fn in spec:
        if only and slug not in only[0][7:].split(','):
            continue
        pid = ensure_page(parent, slug, title_)
        path = slug if not parent else 'ownership-support/' + slug
        out.append((pid, path, fn()))
    return out
