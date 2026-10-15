"""Ownership & Support page (Figma frame 1:1906 'Ownership & Support') -> WP page 15."""
import sys, os
sys.path.insert(0, os.path.join(os.path.dirname(__file__), '..', 'tools'))
from parts import *

POST = 15
SLUG = 'ownership-support'

# Copy is verbatim from the Figma file (placeholder text pending client content)
LOREM_INTRO = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer mi metus, lobortis at varius eget, elementum non mi. Fusce egestas risus nunc, eget porttitor nunc vulputate vel. Praesent in lectus ut urna egestas porta.'

# 42/52 section titles (as on Home), with a mobile line-height to match the smaller size
T42 = 'font-size: 42px; font-weight: 700; line-height: 52px; color: #00050b; @media(--tablet) { line-height: 46px; } @media(--mobile) { line-height: 38px; }'


def hero():
    stats = [('own-st-clock', '24/7', 'Customer Service'), ('own-st-globe', '100+', 'Countries & Regions'),
             ('own-st-build', '415+', 'Authorized Service Outlets'), ('own-st-wrench', '320+', 'Contracted Service Companies')]
    cells = []
    for i, (ic, n, t) in enumerate(stats):
        if i:
            cells.append(div('Hero Stat Divider %d' % i, 'width: 1px; min-width: 1px; height: 50px; padding: 0; background-color: rgba(255,255,255,0.25); @media(--tablet) { display: none; }'))
        cells.append(flex('Hero Stat %d' % (i + 1), 'width: auto; padding: 0; gap: 14px; align-items: center; @media(--tablet) { width: 45%; } @media(--mobile) { width: 100%; }', [
            flex('Hero Stat %d Icon' % (i + 1), 'width: 48px; min-width: 48px; height: 48px; padding: 0; border-radius: 24px; background-color: rgba(255,255,255,0.14); justify-content: center; align-items: center;', [img('Hero Stat %d Glyph' % (i + 1), ic, 'width: 21px;')]),
            flex('Hero Stat %d Text' % (i + 1), 'width: auto; padding: 0; flex-direction: column; gap: 0;', [
                p('Hero Stat %d Number' % (i + 1), n, FD + 'font-size: 30px; font-weight: 600; color: #ffffff; margin: 0; line-height: 1.25; @media(--mobile) { font-size: 24px; }'),
                p('Hero Stat %d Label' % (i + 1), t, FB + 'font-size: 13px; color: rgba(255,255,255,0.78); margin: 0;'),
            ]),
        ]))
    crumbs = flex('Breadcrumb', 'width: auto; padding: 7px 16px; gap: 8px; align-items: center; border-radius: 30px; background-color: rgba(255,255,255,0.12); border: 1px solid rgba(255,255,255,0.25);', [
        p('Crumb 1', 'Home', FB + 'font-size: 13px; font-weight: 400; color: rgba(255,255,255,0.75); margin: 0;', href=URL['home']),
        img('Crumb Chevron 1', 'ic-chevron-crumb', 'width: 12px;'),
        p('Crumb 2', 'Ownership &amp; Support', FB + 'font-size: 13px; font-weight: 600; color: #ffffff; margin: 0;'),
    ])
    return flex('Page Hero', '''min-height: 720px; padding: 190px 120px 54px; flex-direction: column; justify-content: space-between; gap: 32px;
        background-color: #072b52;
        background-image: linear-gradient(180deg, rgba(0,5,11,0) 50%, rgba(0,5,11,0.7) 100%), linear-gradient(90deg, rgba(7,43,82,0.95) 0%, rgba(7,43,82,0.7) 50%, rgba(7,43,82,0.15) 90%), ''' + bg('own-hero') + ''';
        background-size: cover; background-position: center center;
        @media(--tablet) { padding: 150px 40px 50px; min-height: 0; gap: 48px; } @media(--mobile) { padding: 120px 20px 40px; gap: 36px; }''', [
        flex('Page Hero Content', 'max-width: 700px; padding: 0; flex-direction: column; align-items: flex-start; gap: 22px;', [
            crumbs,
            h('Page Title', 'Ownership &amp; ' + hl('Support'), 'h1', FD + 'font-size: 64px; font-weight: 700; line-height: 74px; color: #ffffff; margin: 0; @media(--tablet) { font-size: 50px; line-height: 58px; } @media(--mobile) { font-size: 38px; line-height: 46px; }').c('yt-hl-sky'),
            p('Page Intro', LOREM_INTRO, FB + 'max-width: 600px; font-size: 18px; font-weight: 300; line-height: 30px; color: rgba(255,255,255,0.88); margin: 0; @media(--mobile) { font-size: 16px; line-height: 26px; }'),
        ]),
        flex('Hero Stats', 'max-width: 1200px; padding: 26px 40px; justify-content: space-between; align-items: center; border-radius: 20px; background-color: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); backdrop-filter: blur(12px); @media(--tablet) { flex-wrap: wrap; gap: 24px; } @media(--mobile) { padding: 22px; gap: 18px; }', cells),
    ], tag='section')


# ---------- Ownership Journey ----------

JOURNEY = [
    ('own-ic-truck', '01', 'Delivery', 'Vehicle handover, commissioning and operational preparation.'),
    ('own-ic-box', '02', 'Parts', 'Genuine Yutong parts with verified quality and compatibility.'),
    ('own-ic-grad', '03', 'Training', 'Driver, maintenance and operation-management training.'),
    ('own-ic-headset', '04', 'Technical Support', 'Professional service teams and technical assistance when you need it.'),
    ('own-ic-cal', '05', 'Maintenance', 'Preventive maintenance and technical inspections designed around vehicle usage.'),
    ('own-ic-loop', '06', 'Lifecycle Care', 'Continuous support to help maintain vehicle performance and operational efficiency.'),
]


def journey_card(i, ic, num, name, text):
    blue = i == 0
    n = 'Journey Card %d' % (i + 1)
    return flex(n, 'padding: 24px; flex: 1 1 0; flex-direction: column; gap: 14px; align-self: stretch; border-radius: 22px; background-color: %s; @media(--mobile) { flex: 0 0 auto; }' % ('#0160b4' if blue else '#f2f6fb'), [
        flex(n + ' Top', 'padding: 0; justify-content: space-between; align-items: center;', [
            flex(n + ' Icon', 'width: 48px; min-width: 48px; height: 48px; padding: 0; border-radius: 24px; background-color: #ffffff; justify-content: center; align-items: center;', [img(n + ' Glyph', ic, 'width: 21px;')]),
            p(n + ' Number', num, FD + 'font-size: 24px; font-weight: 700; color: %s; margin: 0;' % ('rgba(255,255,255,0.4)' if blue else 'rgba(1,96,180,0.2)')),
        ]),
        h(n + ' Title', name, 'h3', FD + 'font-size: 18px; font-weight: 600; color: %s; margin: 0;' % ('#ffffff' if blue else '#00050b')),
        p(n + ' Text', text, FB + 'font-size: 14px; line-height: 22px; color: %s; margin: 0;' % ('rgba(255,255,255,0.85)' if blue else '#747474')),
    ]).c('yt-lift')


def journey():
    rows = [flex('Journey Row %d' % (r + 1), 'padding: 0; gap: 16px; align-items: stretch; @media(--mobile) { flex-direction: column; }',
                 [journey_card(r * 2 + c, *JOURNEY[r * 2 + c]) for c in range(2)]) for r in range(3)]
    return flex('Ownership Journey', 'padding: 110px 120px; gap: 56px; align-items: center; background-color: #ffffff; @media(--tablet) { flex-direction: column; align-items: stretch; padding: 80px 40px; gap: 40px; } @media(--mobile) { padding: 64px 20px; }', [
        flex('Journey Text', 'width: 440px; max-width: 100%; flex: 0 0 auto; padding: 0; flex-direction: column; gap: 18px; @media(--tablet) { width: 100%; }', [
            flex('Journey Head', 'padding: 0; flex-direction: column; gap: 14px;', [
                eyebrow('Journey Eyebrow', 'Ownership Journey'),
                title('Journey Title', 'Support at ' + hl('Every Stage'), T42),
            ]),
            body('Journey Intro', 'From the moment your Yutong vehicle arrives, our support continues throughout its operational life.', 'line-height: 26px;', '#747474'),
            img('Journey Photo', 'own-support', 'width: 100%; height: 240px; object-fit: cover; border-radius: 24px; @media(--tablet) { height: 320px; } @media(--mobile) { height: 200px; }'),
        ]),
        flex('Journey Cards', 'padding: 0; flex: 1 1 0; flex-direction: column; gap: 16px; @media(--tablet) { flex: 0 0 auto; }', rows),
    ], tag='section')


# ---------- Service Network ----------

def pill(cid, icon, label, on):
    return flex(cid, 'width: auto; padding: 11px 24px; gap: 8px; align-items: center; border-radius: 34px; background-color: %s; %s @media(--mobile) { padding: 10px 16px; }' % ('#0160b4' if on else 'transparent', EASE + ('' if on else ' &:hover { background-color: #f2f6fb; }')), [
        img(cid + ' Icon', icon, 'width: 16px;'),
        p(cid + ' Label', label, FB + 'font-size: 14px; font-weight: %d; color: %s; margin: 0;' % (500 if on else 400, '#ffffff' if on else '#323232')).c('yt-nowrap'),
    ], href=None if on else '#spare-parts')


def chip(cid, icon, label):
    return flex(cid, 'width: auto; padding: 6px 12px; gap: 6px; align-items: center; border-radius: 30px; background-color: #f2f6fb;', [
        img(cid + ' Icon', icon, 'width: 13px;'),
        p(cid + ' Label', label, FB + 'font-size: 12px; font-weight: 500; color: #003376; margin: 0;').c('yt-nowrap'),
    ])


def network():
    card = flex('Location Card', 'width: 360px; max-width: 100%; padding: 26px; flex-direction: column; gap: 14px; background-color: #ffffff; border-radius: 22px; box-shadow: 0 20px 24px rgba(0,26,64,0.16); @media(--mobile) { width: 100%; padding: 20px; }', [
        flex('Location Head', 'padding: 0; gap: 12px; align-items: center;', [
            flex('Location Pin', 'width: 44px; min-width: 44px; height: 44px; padding: 0; border-radius: 22px; background-color: #0160b4; justify-content: center; align-items: center;', [img('Location Pin Icon', 'own-pin', 'width: 19px;')]),
            h('Location City', 'DUABI', 'h3', FD + 'font-size: 20px; font-weight: 700; color: #003376; margin: 0;'),
        ]),
        p('Location Address', 'Direct Automart Services Pte Ltd 160 Sin Ming Jabal Ali, UAE', FB + 'font-size: 14px; line-height: 22px; color: #747474; margin: 0;'),
        flex('Location Chips', 'padding: 0; gap: 8px; flex-wrap: wrap;', [
            chip('Location Chip Service', 'own-chip-wrench', 'Service Centers'),
            chip('Location Chip Parts', 'own-chip-box', 'Spare Parts'),
        ]),
    ])
    return flex('Service Network', 'padding: 110px 120px; flex-direction: column; align-items: center; gap: 36px; position: relative; background-color: #f2f6fb; @media(--tablet) { padding: 80px 40px; } @media(--mobile) { padding: 64px 20px; gap: 28px; }', [
        anchor('service-network'),
        flex('Network Head', 'padding: 0; flex-direction: column; align-items: center; gap: 14px;', [
            eyebrow('Network Eyebrow', 'Global Network', center=True),
            title('Network Title', 'Global Service &amp; ' + hl('Parts Network'), 'max-width: 820px; text-align: center; ' + T42),
            body('Network Intro', 'From expert service centers to genuine spare parts, Yutong keeps your fleet supported throughout its journey.', 'max-width: 760px; text-align: center; line-height: 27px;', '#747474', 16),
        ]),
        flex('Network Toggle', 'width: auto; padding: 6px; gap: 4px; background-color: #ffffff; border: 1px solid #e3e9f1; border-radius: 40px;', [
            pill('Network Tab Service', 'own-tab-wrench', 'Service Centers', True),
            pill('Network Tab Parts', 'own-tab-box', 'Spare Parts', False),
        ]),
        flex('Network Map', '''max-width: 1200px; min-height: 620px; padding: 40px; flex-direction: column; align-items: flex-start; border-radius: 28px; overflow: hidden;
            background-color: #dfe7f0; background-image: %s; background-size: cover; background-position: center center;
            @media(--tablet) { min-height: 480px; } @media(--mobile) { min-height: 0; padding: 0; background-image: none; background-color: transparent; border-radius: 0; gap: 16px; }''' % bg('own-map'), [
            card,
            img('Network Map Mobile', 'own-map', 'display: none; width: 100%; height: 220px; object-fit: cover; border-radius: 22px; @media(--mobile) { display: block; }'),
        ]),
    ], tag='section')


# ---------- Keep Your Fleet Moving (accordion) ----------

MAINT = [
    ('01', 'Preventive Maintenance', 'Scheduled inspections and maintenance to help keep vehicles in optimal condition.'),
    ('02', 'Managed Maintenance', None),
    ('03', 'Repair &amp; Technical Support', None),
    ('04', 'Configuration Updates', None),
]


def acc_item(i, num, name, text):
    n = 'Maint %d' % (i + 1)
    circle = 'width: 32px; min-width: 32px; height: 32px; padding: 0; border-radius: 16px; justify-content: center; align-items: center;'
    row = flex(n + ' Row', 'padding: 0; gap: 14px; align-items: center;', [
        p(n + ' Number', num, FD + 'font-size: 14px; font-weight: 600; color: #0160b4; margin: 0;'),
        h(n + ' Title', name, 'h3', FD + 'flex: 1 1 0; font-size: 18px; font-weight: 600; line-height: 1.3; color: #00050b; margin: 0; @media(--mobile) { font-size: 16px; }'),
        flex(n + ' Open Icon', circle + ' background-color: #0160b4;', [img(n + ' Minus', 'own-minus', 'width: 14px;')]).c('yt-own-open'),
        flex(n + ' Closed Icon', circle + ' background-color: #ffffff; border: 1px solid #e3e9f1;', [img(n + ' Chevron', 'own-chev', 'width: 14px;')]).c('yt-own-closed'),
    ])
    # placeholder copy for collapsed items isn't in Figma; reuse the item's own wording
    copy = text or 'Contact our service team to learn more about %s for your Yutong fleet.' % name.replace('&amp;', '&').lower()
    return Node('e-accordion-item', n, {}, 'padding: 20px 24px; background-color: #ffffff; border: 1px solid #e3e9f1; border-radius: 18px; @media(--mobile) { padding: 16px 18px; }', [
        Node('e-accordion-item-header', n + ' Header', {'show_icon': False}, 'padding: 0;', [
            Node('e-accordion-item-title', n + ' Head Title', {}, 'padding: 0; width: 100%;', [row]),
        ]),
        Node('e-accordion-item-content', n + ' Content', {}, 'padding: 12px 0 0 34px; flex-direction: column; gap: 12px; @media(--mobile) { padding-left: 0; }', [
            p(n + ' Text', copy, FB + 'font-size: 14px; line-height: 23px; color: #747474; margin: 0;'),
            flex(n + ' More', 'width: auto; padding: 0; gap: 6px; align-items: center; align-self: flex-start;', [
                p(n + ' More Text', 'Learn More', FB + 'font-size: 14px; font-weight: 500; color: #0160b4; margin: 0;'),
                img(n + ' More Arrow', 'ic-arrow-blue-16b', 'width: 16px;'),
            ], href=URL['contact']),
        ]),
    ]).c('yt-own-acc')


def fleet():
    return flex('Keep Your Fleet Moving', 'padding: 110px 120px; gap: 64px; align-items: flex-start; background-color: #ffffff; @media(--tablet) { flex-direction: column; align-items: stretch; padding: 80px 40px; gap: 40px; } @media(--mobile) { padding: 64px 20px; }', [
        flex('Fleet Text', 'width: 440px; max-width: 100%; flex: 0 0 auto; padding: 0; flex-direction: column; gap: 18px; @media(--tablet) { width: 100%; }', [
            flex('Fleet Head', 'padding: 0; flex-direction: column; gap: 14px;', [
                eyebrow('Fleet Eyebrow', 'Maintenance'),
                title('Fleet Title', 'Keep Your ' + hl('Fleet Moving'), T42),
            ]),
            body('Fleet Intro', 'Yutong offers maintenance and service solutions designed around vehicle configuration, operating conditions and technical requirements.', 'line-height: 26px;', '#747474'),
            btn('Fleet Contact', 'Contact Us', URL['contact']),
        ]),
        Node('e-accordion', 'Fleet Accordion', {'show_icon': False, 'default_state': 'first_expanded', 'max_expanded': 'one'},
             'padding: 0; flex: 1 1 0; gap: 12px; @media(--tablet) { flex: 0 0 auto; }',
             [acc_item(i, *m) for i, m in enumerate(MAINT)]),
    ], tag='section')


# ---------- Genuine Spare Parts ----------

PARTS = [
    ('own-puzzle', 'Perfect Compatibility', 'Parts designed and selected for Yutong vehicles.'),
    ('own-shield-b', 'Verified Quality', 'Suppliers undergo qualification and quality verification.'),
    ('own-gauge', 'Reliable Performance', 'Components are developed to support safety, durability and stable operation.'),
    ('own-globe-b', 'Global Availability', 'A global parts network helps customers access genuine replacement parts.'),
]


def parts():
    cards = []
    for i, (ic, t, d) in enumerate(PARTS):
        white = i == 0
        n = 'Parts Card %d' % (i + 1)
        cards.append(flex(n, 'padding: 28px; flex: 1 1 0; flex-direction: column; gap: 16px; align-self: stretch; border-radius: 24px; %s @media(--tablet) { flex: 1 1 40%%; } @media(--mobile) { flex: 0 0 auto; }' % (
            'background-color: #ffffff; border: 1px solid rgba(255,255,255,0);' if white else 'background-color: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.12);'), [
            flex(n + ' Icon', 'width: 52px; min-width: 52px; height: 52px; padding: 0; border-radius: 26px; justify-content: center; align-items: center; background-color: %s;' % ('#0160b4' if white else '#ffffff'), [img(n + ' Glyph', ic, 'width: 23px;')]),
            h(n + ' Title', t, 'h3', FD + 'font-size: 18px; font-weight: 600; color: %s; margin: 0;' % ('#00050b' if white else '#ffffff')),
            p(n + ' Text', d, FB + 'font-size: 14px; line-height: 22px; color: %s; margin: 0;' % ('#747474' if white else 'rgba(255,255,255,0.78)')),
        ]).c('yt-lift'))
    return flex('Genuine Spare Parts', 'padding: 110px 120px; flex-direction: column; gap: 44px; position: relative; background-color: #072b52; @media(--tablet) { padding: 80px 40px; } @media(--mobile) { padding: 64px 20px; gap: 32px; }', [
        anchor('spare-parts'),
        flex('Parts Head', 'padding: 0; gap: 64px; align-items: flex-end; @media(--tablet) { flex-direction: column; align-items: flex-start; gap: 20px; }', [
            flex('Parts Head Text', 'width: 560px; max-width: 100%; flex: 0 0 auto; padding: 0; flex-direction: column; gap: 14px;', [
                eyebrow('Parts Eyebrow', 'Genuine Spare Parts', '#ffffff'),
                title('Parts Title', 'Genuine Parts. Made to Fit. ' + hl('Built to Last.'), T42.replace('#00050b', '#ffffff'), accent='#2c84cb'),
            ]),
            body('Parts Intro', 'Yutong develops and customizes parts with qualified suppliers, with parts entering its supply system only after supplier testing and Yutong quality inspection.', 'flex: 1 1 0; line-height: 26px;', 'rgba(255,255,255,0.8)'),
        ]),
        flex('Parts Cards', 'padding: 0; gap: 20px; align-items: stretch; @media(--tablet) { flex-wrap: wrap; } @media(--mobile) { flex-direction: column; }', cards),
    ], tag='section')


# ---------- Warranty ----------

def warranty():
    return flex('Warranty', 'padding: 110px 120px; background-color: #ffffff; @media(--tablet) { padding: 80px 40px; } @media(--mobile) { padding: 64px 16px; }', [
        flex('Warranty Card', 'padding: 0; align-items: center; background-color: #f2f6fb; border-radius: 28px; overflow: hidden; @media(--tablet) { flex-direction: column-reverse; align-items: stretch; } @media(--mobile) { border-radius: 24px; }', [
            flex('Warranty Text', 'padding: 56px; flex: 1 1 0; flex-direction: column; align-items: flex-start; gap: 18px; @media(--tablet) { flex: 0 0 auto; padding: 44px 32px; } @media(--mobile) { padding: 28px 20px 32px; }', [
                flex('Warranty Icon', 'width: 56px; min-width: 56px; height: 56px; padding: 0; border-radius: 28px; background-color: #0160b4; justify-content: center; align-items: center;', [img('Warranty Glyph', 'own-shield-w', 'width: 25px;')]),
                h('Warranty Title', 'Protection Designed for Long-Term Ownership', 'h2', FD + 'font-size: 34px; font-weight: 700; line-height: 42px; color: #00050b; margin: 0; @media(--mobile) { font-size: 26px; line-height: 34px; }'),
                body('Warranty Body', 'Experience premium electric touring with long-range performance, intelligent technology and Yutong provides warranty support according to applicable local laws and regulations, customer.', 'line-height: 26px;', '#747474'),
                btn('Warranty CTA', 'View Warranty Details', URL['contact']),
            ]),
            img('Warranty Photo', 'own-warranty', 'width: 580px; flex: 0 0 auto; height: 420px; object-fit: cover; @media(--tablet) { width: 100%; height: 320px; } @media(--mobile) { height: 220px; }'),
        ]),
    ], tag='section')


SECTIONS = [
    ('ownership-1', lambda: [header('ownership'), hero(), journey()]),
    ('ownership-2', lambda: [network(), fleet()]),
    ('ownership-3', lambda: [parts(), warranty()]),
    ('ownership-4', lambda: [news_section(), quote_section(), footer()]),
]
