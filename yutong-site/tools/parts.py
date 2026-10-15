"""Shared Yutong sections and atoms, reused by every page builder."""
from lib import *

NAV = [('Home', 'home'), ('Models', 'models'), ('Technologies', 'technologies'),
       ('About', 'about'), ('News', 'news'), ('Contact', 'contact')]

EASE = 'transition: all 0.3s;'


# ---------- atoms ----------

def btn(cid, text, href, kind='primary', size='md', arrow=True):
    """Pill button: linked flexbox with label + arrow icon.
    kind: primary (blue), outline (white border, on dark), outline-blue, white (white bg, blue text)."""
    pad = {'sm': '10px 20px', 'md': '15px 28px'}[size]
    fs = {'sm': '13px', 'md': '15px'}[size]
    look = {
        'primary': 'background-color: var(--yt-blue); border: 1.5px solid #0160b4; &:hover { background-color: #014c8f; }',
        'outline': 'background-color: transparent; border: 1.5px solid #ffffff; &:hover { background-color: rgba(255,255,255,0.12); }',
        'outline-blue': 'background-color: transparent; border: 1.5px solid #0160b4; &:hover { background-color: #eaf2fb; }',
        'white': 'background-color: #ffffff; border: 1.5px solid #ffffff; &:hover { background-color: #eaf2fb; }',
    }[kind]
    color = '#0160b4' if kind in ('outline-blue', 'white') else '#ffffff'
    kids = [p(cid + ' Label', text, FB + 'font-size: %s; font-weight: 500; color: %s; line-height: 1.2; margin: 0;' % (fs, color)).c('yt-nowrap')]
    if arrow:
        icon = 'ic-arrow-white' if color == '#ffffff' else 'ic-arrow-blue'
        kids.append(img(cid + ' Arrow', icon, 'width: %s;' % ('15px' if size == 'sm' else '17px')))
    return flex(cid, 'width: auto; flex: 0 0 auto; align-self: flex-start; padding: %s; gap: 10px; align-items: center; justify-content: center; border-radius: 40px; %s %s' % (pad, look, EASE), kids, href=href)


def eyebrow(cid, text, color='#0160b4', center=False, line_color=None):
    """Small uppercase label with a short rule before (and after, when centred)."""
    lc = line_color or color
    rule = lambda n: div(cid + ' Rule' + n, 'width: 24px; min-width: 24px; height: 2px; padding: 0; background-color: %s;' % lc)
    kids = [rule('')]
    kids.append(p(cid + ' Text', text, FB + 'font-size: 13px; font-weight: 600; letter-spacing: 1.56px; text-transform: uppercase; color: %s; margin: 0;' % color))
    if center:
        kids.append(rule(' 2'))
    return flex(cid, 'width: auto; padding: 0; gap: 10px; align-items: center; %s' % ('align-self: center;' if center else ''), kids)


def title(cid, html, css='', tag='h2', color='#0b0b0b', accent='#0160b4'):
    """Section title in Lexend; wrap highlighted words in <span style=color> via hl()."""
    return h(cid, html, tag, FD + 'font-size: 44px; font-weight: 600; line-height: 1.2; letter-spacing: -0.44px; color: %s; margin: 0; @media(--tablet) { font-size: 36px; line-height: 1.2; } @media(--mobile) { font-size: 30px; line-height: 1.25; } %s' % (color, css)).c(HL_CLASS[accent])


def hl(text, color=None):
    """Highlighted words; the colour comes from the heading's `& span` rule (inline styles are stripped)."""
    return '<span>%s</span>' % text


HL_CLASS = {'#0160b4': 'yt-hl-blue', '#2c84cb': 'yt-hl-sky'}


def body(cid, text, css='', color='#6b7280', size=15):
    return p(cid, text, FB + 'font-size: %dpx; font-weight: 400; line-height: 1.75; color: %s; margin: 0; %s' % (size, color, css))


def section(cid, css, kids, anchor=None):
    """Full-width section with a centred 1200px inner column."""
    return flex(cid, 'padding: 0 20px; justify-content: center; ' + css, [
        flex(cid + ' Inner', 'max-width: 1200px; padding: 0; flex-direction: column; gap: 0;', kids)
    ], tag='section')


# ---------- header ----------

def header(active):
    links = []
    for label, key in NAV:
        on = key == active
        links.append(flex('Nav ' + label, 'width: auto; padding: 0; flex-direction: column; gap: 4px; align-items: center;', [
            p('Nav %s Link' % label, label, FB + 'font-size: 15px; font-weight: %d; color: %s; margin: 0; %s &:hover { color: #0160b4; }' % (600 if on else 400, '#0160b4' if on else '#323232', EASE), href=URL[key]),
            div('Nav %s Dot' % label, 'width: 5px; min-width: 5px; height: 5px; padding: 0; border-radius: 50%%; background-color: %s;' % ('#0160b4' if on else 'transparent')),
        ]))
    menu_links = [p('Menu ' + label, label, FB + 'font-size: 16px; font-weight: %d; color: %s; margin: 0; padding: 10px 0; border-width: 0 0 1px 0; border-style: solid; border-color: #eef2f7;' % (600 if key == active else 400, '#0160b4' if key == active else '#323232'), href=URL[key]) for label, key in NAV]
    mobile_menu = Node('e-accordion', 'Mobile Menu', {'show_icon': False, 'default_state': 'all_collapsed'}, 'display: none; width: auto; padding: 0; @media(--tablet) { display: block; }', [
        Node('e-accordion-item', 'Mobile Menu Item', {}, 'padding: 0; border: none;', [
            Node('e-accordion-item-header', 'Mobile Menu Toggle', {'show_icon': False}, 'padding: 0; width: 44px; height: 44px; border-radius: 22px; background-color: #f2f6fb; justify-content: center; align-items: center;', [
                Node('e-accordion-item-title', 'Mobile Menu Title', {}, 'padding: 0;', [
                    p('Mobile Menu Icon', '&#9776;', 'font-size: 18px; line-height: 1; color: #0160b4; margin: 0;'),
                ]),
            ]),
            Node('e-accordion-item-content', 'Mobile Menu Panel', {}, 'position: absolute; top: 64px; right: 0; width: 260px; padding: 12px 24px; background-color: #ffffff; border-radius: 18px; box-shadow: 0 20px 40px rgba(0,26,64,0.15); z-index: 50;', menu_links + [
                flex('Menu CTA Wrap', 'padding: 16px 0 6px;', [btn('Menu Quote', 'Request a Quote', URL['quote'], size='sm')]),
            ]),
        ]),
    ])
    bar = flex('Header Bar', 'max-width: 1250px; padding: 14px 28px; justify-content: space-between; align-items: center; background-color: #ffffff; border-radius: 18px; box-shadow: 0 10px 14px rgba(0,26,64,0.08); @media(--mobile) { padding: 10px 16px; border-radius: 14px; }', [
        img('Header Logo', 'logo-yutong', 'width: 148px; @media(--mobile) { width: 120px; }', href=URL['home']),
        flex('Header Nav', 'width: auto; padding: 0; gap: 36px; align-items: center; @media(--tablet) { display: none; }', links),
        flex('Header Actions', 'width: auto; padding: 0; gap: 16px; align-items: center; @media(--mobile) { gap: 10px; }', [
            flex('Header Search', 'width: 44px; min-width: 44px; height: 44px; padding: 0; border-radius: 22px; background-color: #f2f6fb; justify-content: center; align-items: center; @media(--mobile) { display: none; }', [img('Header Search Icon', 'ic-search', 'width: 18px;')], href=URL['news']),
            flex('Header Quote Wrap', 'width: auto; padding: 0; @media(--mobile) { display: none; }', [btn('Header Quote', 'Request a Quote', URL['quote'], size='sm')]),
            img('Header AKG Logo', 'logo-akg', 'width: 28px;'),
            mobile_menu,
        ]),
    ])
    return flex('Header', 'position: absolute; top: 28px; left: 0; z-index: 30; padding: 0 60px; justify-content: center; @media(--tablet) { padding: 0 24px; } @media(--mobile) { top: 14px; padding: 0 14px; }', [bar])


# ---------- slots (filled by inject.py / finalize) ----------

def slot(name):
    """Empty placeholder that finalize() fills with a classic widget (anchor id, form, CSS)."""
    return div(name, 'padding: 0; min-width: 0; width: 100%;')


def anchor(name):
    return div('Anchor ' + name, 'padding: 0; min-width: 0; width: 0; height: 0; position: absolute; top: -40px; left: 0;')


# ---------- Yutong News (shared) ----------

NEWS = [
    ('news-1', 'ALPS The Bus Inc. Strengthens Its Fleet with Additional Yutong C12 Coaches'),
    ('news-2', 'Yutong Delivers Special Bus Maintenance Training in Johannesburg to Empower'),
    ('news-3', 'Yutong Bus Showcases Sustainable Mobility Solutions at FIA 2026 in Algeria'),
]


def news_card(i, photo, headline, date='July 28, 2026', tag='NEWS', href=None):
    href = href or URL['news']
    return flex('News Card %d' % i, 'padding: 0; flex: 1 1 0; @media(--mobile) { flex: 0 0 auto; } flex-direction: column; align-self: stretch; background-color: #ffffff; border-radius: 22px; overflow: hidden; box-shadow: 0 10px 28px rgba(0,26,64,0.08);', [
        div('News Card %d Media' % i, 'padding: 0; height: 230px; position: relative;', [
            img('News Card %d Photo' % i, photo, 'width: 100%; height: 230px; object-fit: cover;'),
            flex('News Card %d Date' % i, 'position: absolute; top: 16px; left: 16px; width: auto; padding: 7px 12px; gap: 6px; align-items: center; background-color: #ffffff; border-radius: 20px;', [
                img('News Card %d Cal' % i, 'ic-cal', 'width: 14px;'),
                p('News Card %d Date Text' % i, date, FB + 'font-size: 12px; font-weight: 500; color: #00050b; margin: 0;'),
            ]),
        ]).c('yt-zoom'),
        flex('News Card %d Body' % i, 'padding: 26px; flex-direction: column; gap: 14px; flex-grow: 1;', [
            p('News Card %d Tag' % i, tag, FB + 'font-size: 11px; font-weight: 600; letter-spacing: 1.32px; color: #0160b4; margin: 0;'),
            h('News Card %d Title' % i, headline, 'h3', FD + 'font-size: 18px; font-weight: 600; line-height: 26px; color: #00050b; margin: 0;', href=href),
            flex('News Card %d More' % i, 'width: auto; padding: 0; gap: 8px; align-items: center; margin-top: auto;', [
                p('News Card %d More Text' % i, 'Read More', FB + 'font-size: 14px; font-weight: 500; color: #0160b4; margin: 0;'),
                img('News Card %d More Arrow' % i, 'ic-arrow-blue-16', 'width: 16px;'),
            ], href=href),
        ]),
    ]).c('yt-lift')


def news_section():
    return flex('Yutong News', 'padding: 110px 120px; flex-direction: column; gap: 44px; background-color: #f2f6fb; @media(--tablet) { padding: 80px 40px; } @media(--mobile) { padding: 64px 20px; gap: 32px; }', [
        flex('News Head', 'padding: 0; justify-content: space-between; align-items: flex-end; gap: 24px; @media(--mobile) { flex-direction: column; align-items: flex-start; }', [
            flex('News Head Text', 'max-width: 620px; padding: 0; flex-direction: column; gap: 14px;', [
                eyebrow('News Eyebrow', 'Latest Updates'),
                title('News Title', 'Yutong News', 'font-weight: 700; color: #00050b;'),
                body('News Intro', 'As a globally leading manufacturer of buses and coaches, we bring decades of expertise and cutting-edge technology to the bus and coach industry.', 'max-width: 600px; line-height: 27px;', '#747474', 16),
            ]),
            btn('News All', 'View All Blogs', URL['news']),
        ]),
        flex('News Cards', 'padding: 0; gap: 24px; align-items: stretch; @media(--tablet) { flex-wrap: wrap; } @media(--mobile) { flex-direction: column; }',
             [news_card(i + 1, ph, t) for i, (ph, t) in enumerate(NEWS)]),
    ], tag='section')


# ---------- Request a Quote (shared) ----------

def quote_section(pad='110px 120px'):
    contacts = [('q-phone', 'Tel: 04 3146235'), ('q-mail', 'info@subaru-uae.com'), ('q-clock', '09:30 AM to 8:00 PM — Open 7 days a week')]
    rows = []
    for i, (ic, t) in enumerate(contacts):
        rows.append(flex('Quote Contact %d' % (i + 1), 'width: auto; padding: 0; gap: 12px; align-items: center;', [
            flex('Quote Contact %d Icon' % (i + 1), 'width: 40px; min-width: 40px; height: 40px; padding: 0; border-radius: 20px; background-color: rgba(255,255,255,0.1); justify-content: center; align-items: center;', [img('Quote Contact %d Glyph' % (i + 1), ic, 'width: 18px;')]),
            p('Quote Contact %d Text' % (i + 1), t, FB + 'font-size: 14px; color: #ffffff; margin: 0;'),
        ]))
    return flex('Request a Quote', 'padding: %s; background-color: #ffffff; position: relative; @media(--tablet) { padding: 80px 40px; } @media(--mobile) { padding: 56px 16px; }' % pad, [
        anchor('request-a-quote'),
        flex('Quote Card', '''padding: 56px; gap: 56px; align-items: center; border-radius: 32px; overflow: hidden; position: relative;
            background-color: #072b52; background-image: radial-gradient(circle at 8% 0%, rgba(44,132,203,0.45) 0%, rgba(44,132,203,0) 45%);
            @media(--tablet) { flex-direction: column; align-items: stretch; padding: 44px 32px; gap: 40px; }
            @media(--mobile) { padding: 32px 20px; border-radius: 24px; }''', [
            flex('Quote Info', 'padding: 0; flex: 1 1 0; flex-direction: column; gap: 20px;', [
                eyebrow('Quote Eyebrow', 'Request a Quote', '#ffffff'),
                h('Quote Title', 'Coaches, vans and ' + hl('e&#8209;buses') + ' built for the UAE', 'h2', FD + 'font-size: 40px; font-weight: 700; line-height: 50px; color: #ffffff; margin: 0; @media(--mobile) { font-size: 30px; line-height: 38px; }').c('yt-hl-sky'),
                body('Quote Text', 'Official Yutong distributor in the UAE, with sales, servicing and genuine spare parts for fleet operators, schools and transport companies.', 'line-height: 26px;', 'rgba(255,255,255,0.8)'),
                flex('Quote Contacts', 'padding: 8px 0 0; flex-direction: column; gap: 14px;', rows),
            ]),
            flex('Quote Form Card', 'width: 500px; max-width: 100%; flex: 0 0 auto; padding: 34px; flex-direction: column; gap: 16px; background-color: #ffffff; border-radius: 24px; box-shadow: 0 20px 24px rgba(0,26,64,0.16); @media(--tablet) { width: 100%; } @media(--mobile) { padding: 24px 18px; }', [
                h('Quote Form Title', 'Send us your enquiry', 'h3', FD + 'font-size: 22px; font-weight: 600; color: #00050b; margin: 0;'),
                slot('Quote Form Slot'),
            ]),
        ]),
    ], tag='section')


# ---------- Footer (shared) ----------

FOOT_COLS = [
    ('Quick Links', [('About Us', 'about'), ('Contact Us', 'contact'), ('Request a Quote', 'quote')]),
    ('Models', [('All Models', 'models'), ('Coaches', 'models'), ('Vans', 'models'), ('E-Buses', 'models')]),
    ('Ownership & Support', [('Service Centres', 'ownership'), ('Spare Parts', 'ownership')]),
    ('News', [('Press Releases', 'news'), ('Awards', 'awards'), ('Media Library', 'library')]),
]


def footer():
    cols = []
    for ci, (head, links) in enumerate(FOOT_COLS):
        cols.append(flex('Footer Col %d' % (ci + 1), 'padding: 0; flex: 1 1 0; flex-direction: column; gap: 14px; @media(--mobile) { flex: 1 1 40%; }', [
            p('Footer Col %d Head' % (ci + 1), head, FD + 'font-size: 15px; font-weight: 600; color: #ffffff; margin: 0;')] + [
            p('Footer Link %d %d' % (ci + 1, li + 1), t, FB + 'font-size: 13px; color: rgba(255,255,255,0.65); margin: 0; transition: color 0.3s; &:hover { color: #ffffff; }', href=URL[k]) for li, (t, k) in enumerate(links)]))
    info = [('ft-pin', 'Showroom 12, Sheikh Zayed Road, Al Quoz Industrial Area 3, Dubai, UAE'),
            ('ft-clock', 'Showroom Timing: 09:30 AM to 8:00 PM — Open 7 days a week'),
            ('ft-mail', 'info@subaru-uae.com'), ('ft-phone', 'Spare Parts — Tel: 04 3146235')]
    contact = [p('Footer Contact Head', 'Contact Information', FD + 'font-size: 15px; font-weight: 600; color: #ffffff; margin: 0;')]
    for i, (ic, t) in enumerate(info):
        contact.append(flex('Footer Info %d' % (i + 1), 'padding: 0; gap: 10px; align-items: flex-start;', [
            img('Footer Info %d Icon' % (i + 1), ic, 'width: 16px; min-width: 16px; margin-top: 2px;'),
            p('Footer Info %d Text' % (i + 1), t, FB + 'flex: 1 1 0; font-size: 13px; line-height: 20px; color: rgba(255,255,255,0.75); margin: 0;'),
        ]))
    loc = flex('Footer Location', 'width: auto; flex: 0 0 auto; padding: 10px 20px; gap: 10px; align-items: center; border-radius: 40px; border: 1.5px solid #ffffff; transition: background-color 0.3s; &:hover { background-color: rgba(255,255,255,0.12); }', [
        p('Footer Location Label', 'View Location', FB + 'font-size: 13px; font-weight: 500; color: #ffffff; margin: 0;').c('yt-nowrap'),
        img('Footer Location Icon', 'ft-pin-white', 'width: 15px;'),
    ], href='https://maps.google.com/?q=Al+Khoory+Automobiles+Al+Quoz+Industrial+Area+3+Dubai')
    contact.append(flex('Footer Location Wrap', 'padding: 0;', [loc]))
    legal = ['Your Privacy Choices', 'Cookie and Ad Preferences', 'Conditions of Use', 'Privacy Policy']
    return flex('Footer', 'padding: 72px 120px 28px; flex-direction: column; gap: 44px; background-color: #00050b; @media(--tablet) { padding: 64px 40px 28px; } @media(--mobile) { padding: 56px 20px 24px; gap: 32px; }', [
        flex('Footer Top', 'padding: 0; gap: 32px; align-items: flex-start; @media(--tablet) { flex-wrap: wrap; } @media(--mobile) { gap: 24px; }', [
            flex('Footer Brand', 'width: 260px; flex: 0 0 auto; padding: 0; flex-direction: column; gap: 18px; @media(--tablet) { width: 100%; }', [
                flex('Footer Logos', 'width: auto; padding: 12px 16px; gap: 14px; align-items: center; background-color: #ffffff; border-radius: 14px; align-self: flex-start;', [
                    img('Footer Yutong Logo', 'logo-yutong', 'width: 120px;', href=URL['home']),
                    div('Footer Logo Divider', 'width: 1px; min-width: 1px; height: 30px; padding: 0; background-color: #e3e9f1;'),
                    img('Footer AKG Logo', 'logo-akg', 'width: 22px;'),
                ]),
                p('Footer About', 'Official Yutong distributor in the UAE, with sales, servicing and genuine spare parts for fleet operators, schools and transport companies.', FB + 'font-size: 13px; line-height: 22px; color: rgba(255,255,255,0.65); margin: 0;'),
            ]),
        ] + cols + [
            flex('Footer Contact', 'width: 260px; flex: 0 0 auto; padding: 0; flex-direction: column; gap: 16px; @media(--tablet) { width: 100%; }', contact),
        ]),
        div('Footer Rule', 'height: 1px; padding: 0; background-color: rgba(255,255,255,0.1);'),
        flex('Footer Bottom', 'padding: 0; justify-content: space-between; align-items: center; gap: 16px; @media(--mobile) { flex-direction: column; align-items: flex-start; }', [
            p('Footer Copy', '2026 © Al Khoory Automobiles LLC. All rights reserved.', FB + 'font-size: 12px; color: rgba(255,255,255,0.55); margin: 0;'),
            flex('Footer Legal', 'width: auto; padding: 0; gap: 22px; flex-wrap: wrap; @media(--mobile) { gap: 14px; }',
                 [p('Footer Legal %d' % (i + 1), t, FB + 'font-size: 12px; color: rgba(255,255,255,0.55); margin: 0; &:hover { color: #ffffff; }', href='#') for i, t in enumerate(legal)]),
        ]),
    ], tag='footer')


# ---------- inner-page hero (shared pattern) ----------

def page_hero(active, crumbs, heading, text, photo, height=600, extra=None):
    """Inner page hero: photo with navy gradients, breadcrumb pill, H1, intro.
    crumbs: [('Home', 'home'), ('About', 'about'), ('Awards', None)] -- last one is current.
    extra: optional list of nodes appended under the intro (buttons, stats...)."""
    trail = []
    for i, (label, key) in enumerate(crumbs):
        last = i == len(crumbs) - 1
        if i:
            trail.append(img('Crumb Chevron %d' % i, 'ic-chevron-crumb', 'width: 12px;'))
        trail.append(p('Crumb %d' % (i + 1), label, FB + 'font-size: 13px; font-weight: %d; color: %s; margin: 0;' % (600 if last else 400, '#ffffff' if last else 'rgba(255,255,255,0.75)'), href=None if last or not key else URL[key]))
    kids = [
        flex('Breadcrumb', 'width: auto; padding: 7px 16px; gap: 8px; align-items: center; border-radius: 30px; background-color: rgba(255,255,255,0.12); border: 1px solid rgba(255,255,255,0.25);', trail),
        h('Page Title', heading, 'h1', FD + 'font-size: 64px; font-weight: 700; line-height: 74px; color: #ffffff; margin: 0; @media(--tablet) { font-size: 50px; line-height: 58px; } @media(--mobile) { font-size: 38px; line-height: 46px; }').c('yt-hl-sky'),
        p('Page Intro', text, FB + 'max-width: 600px; font-size: 18px; font-weight: 300; line-height: 30px; color: rgba(255,255,255,0.88); margin: 0; @media(--mobile) { font-size: 16px; line-height: 26px; }'),
    ] + (extra or [])
    return [header(active), flex('Page Hero', '''min-height: %dpx; padding: 160px 120px 60px; flex-direction: column; justify-content: center;
        background-color: #072b52;
        background-image: linear-gradient(180deg, rgba(0,5,11,0) 60%%, rgba(0,5,11,0.6) 100%%), linear-gradient(90deg, rgba(7,43,82,0.95) 0%%, rgba(7,43,82,0.7) 50%%, rgba(7,43,82,0.15) 90%%), %s;
        background-size: cover; background-position: center center;
        @media(--tablet) { padding: 150px 40px 60px; min-height: 520px; } @media(--mobile) { padding: 120px 20px 48px; min-height: 0; }''' % (height, bg(photo)), [
        flex('Page Hero Content', 'max-width: 700px; padding: 0; flex-direction: column; align-items: flex-start; gap: 22px;', kids),
    ], tag='section')]


# ---------- CTA band (shared pattern) ----------

def cta_section(text, photo, eyebrow_text='Ready to Move Forward', button='Contact Us', href=None, pad='0 120px 110px'):
    return flex('CTA', 'padding: %s; background-color: #ffffff; @media(--tablet) { padding: 0 40px 80px; } @media(--mobile) { padding: 0 16px 64px; }' % pad, [
        flex('CTA Panel', '''min-height: 360px; padding: 56px; flex-direction: column; justify-content: center; border-radius: 32px; overflow: hidden;
            background-color: #072b52; background-image: linear-gradient(90deg, rgba(7,43,82,0.96) 0%%, rgba(7,43,82,0.75) 60%%, rgba(7,43,82,0.3) 100%%), %s;
            background-size: cover; background-position: center center;
            @media(--mobile) { min-height: 0; padding: 36px 22px; border-radius: 24px; }''' % bg(photo), [
            flex('CTA Content', 'max-width: 640px; padding: 0; flex-direction: column; align-items: flex-start; gap: 18px;', [
                eyebrow('CTA Eyebrow', eyebrow_text, '#ffffff'),
                h('CTA Text', text, 'h2', FD + 'font-size: 28px; font-weight: 600; line-height: 38px; color: #ffffff; margin: 0; @media(--mobile) { font-size: 22px; line-height: 30px; }').c('yt-hl-sky'),
                btn('CTA Button', button, href or URL['contact'], kind='white'),
            ]),
        ]),
    ], tag='section')


# ---------- Yutong at a Glance (shared) ----------

def glance_section(pad='0 120px 100px'):
    stats = [('glance-globe', '90+', 'Countries with Yutong fleets'), ('glance-factory', '65,000', 'Units annual production capacity'),
             ('glance-chart', '10+%', 'Global bus market share'), ('glance-cal', '7 Days', 'Showroom open every week')]
    cards = []
    for i, (ic, n, t) in enumerate(stats):
        blue = i == 0
        cards.append(flex('Glance Stat %d' % (i + 1), 'padding: 36px 24px; flex: 1 1 0; flex-direction: column; align-items: center; gap: 14px; border-radius: 24px; %s @media(--tablet) { flex: 1 1 40%%; } @media(--mobile) { flex: 0 0 auto; padding: 28px 20px; }' % (
            'background-color: #0160b4; box-shadow: 0 14px 36px rgba(0,26,64,0.18);' if blue else 'background-color: #ffffff; border: 1px solid #e3e9f1; box-shadow: 0 14px 36px rgba(0,26,64,0.05);'), [
            flex('Glance Stat %d Icon' % (i + 1), 'width: 56px; min-width: 56px; height: 56px; padding: 0; border-radius: 28px; justify-content: center; align-items: center; background-color: %s;' % ('rgba(255,255,255,0.15)' if blue else '#f2f6fb'), [img('Glance Stat %d Glyph' % (i + 1), ic, 'width: 24px;')]),
            p('Glance Stat %d Number' % (i + 1), n, FD + 'font-size: 38px; font-weight: 700; color: %s; margin: 0; text-align: center; line-height: 1.25;' % ('#ffffff' if blue else '#003376')),
            p('Glance Stat %d Label' % (i + 1), t, FB + 'font-size: 14px; color: %s; margin: 0; text-align: center;' % ('rgba(255,255,255,0.85)' if blue else '#747474')),
        ]).c('yt-lift'))
    return flex('Yutong at a Glance', 'padding: %s; flex-direction: column; align-items: center; gap: 32px; background-color: #ffffff; @media(--tablet) { padding: 0 40px 80px; } @media(--mobile) { padding: 0 20px 64px; }' % pad, [
        flex('Glance Head', 'padding: 0; flex-direction: column; align-items: center; gap: 10px;', [
            eyebrow('Glance Eyebrow', 'Yutong at a Glance', center=True),
            title('Glance Title', 'Trusted worldwide', 'font-size: 32px; font-weight: 700; color: #00050b; text-align: center;'),
        ]),
        flex('Glance Cards', 'padding: 0; gap: 24px; @media(--tablet) { flex-wrap: wrap; } @media(--mobile) { flex-direction: column; }', cards),
    ], tag='section')
