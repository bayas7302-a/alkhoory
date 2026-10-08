import sys
from dsl import *

HOME = 7
SEC = 'padding: 112px 80px; @media(--tablet) { padding: 88px 40px; } @media(--mobile) { padding: 64px 20px; }'
DARK_BG = 'background: #0e0b12;'
HEAD_ROW = 'flex-direction: row; justify-content: space-between; align-items: flex-end; gap: 40px; @media(--mobile) { flex-direction: column; align-items: flex-start; gap: 24px; }'


def hero():
    stat = lambda n, num, text: box('hero stat %s' % n, 'flex-direction: row; align-items: center; gap: 16px; flex: 1; padding: 0 32px; border-left: 1px solid rgba(255, 255, 255, 0.14); @media(--mobile) { padding: 0 0 0 16px; }' if n > 1 else 'flex-direction: row; align-items: center; gap: 16px; flex: 1; padding: 0 32px 0 0; @media(--mobile) { padding: 0; }', [
        p('hero stat %s num' % n, num, 'font-family: var(--ab-font-display); font-weight: 900; font-size: 52px; line-height: 1; color: #ffffff; @media(--mobile) { font-size: 38px; }'),
        box('hero stat %s txt' % n, 'flex-direction: column; gap: 4px;', [
            box('hero stat %s dash' % n, 'width: 18px; height: 2px; background: #e8388a;'),
            p('hero stat %s label' % n, text, 'font-family: var(--ab-font-body); font-size: 13px; line-height: 1.4; color: rgba(255, 255, 255, 0.7); @media(--mobile) { font-size: 12px; }'),
        ]),
    ])
    card = box('Hero card', 'flex-direction: column; justify-content: flex-start; position: relative; overflow: hidden; min-height: 860px; border-radius: 32px; '
               'background-image: url(%s); background-size: cover; background-position: center; background-color: #0e0b12; '
               '@media(--tablet) { min-height: 720px; } @media(--mobile) { min-height: 0; border-radius: 22px; }' % MEDIA['hero-event-stage'][1], [
        box('Hero tint', 'position: absolute; top: 0; left: 0; width: 100%%; height: 100%%; background: %s; mix-blend-mode: color; opacity: 0.55;' % G),
        box('Hero shade', 'position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: linear-gradient(90deg, rgba(14, 11, 18, 0.97) 0%, rgba(14, 11, 18, 0.82) 48%, rgba(14, 11, 18, 0.15) 100%);'),
        box('Hero shade bottom', 'position: absolute; left: 0; bottom: 0; width: 100%; height: 300px; background: linear-gradient(180deg, rgba(14, 11, 18, 0) 0%, rgba(14, 11, 18, 0.9) 100%);'),
        box('Hero slash', 'position: absolute; top: -20%; left: 64%; width: 150px; height: 140%; transform: rotate(22deg); background: linear-gradient(180deg, rgba(232, 56, 138, 0.9) 0%, rgba(192, 19, 138, 0.55) 100%); opacity: 0.85; @media(--mobile) { left: 78%; width: 80px; opacity: 0.5; }'),
        box('Hero content', 'flex-direction: column; align-items: flex-start; gap: 0; position: relative; padding: 210px 72px 220px; max-width: 760px; @media(--tablet) { padding: 150px 48px 200px; } @media(--mobile) { padding: 96px 22px 32px; }', [
            box('Hero eyebrow', 'flex-direction: row; align-items: center; gap: 14px; width: auto; margin-bottom: 22px;', [
                box('Hero eyebrow bar', 'width: 36px; height: 2px; background: #e8388a;'),
                p('Hero eyebrow text', 'INTERIOR &nbsp;/&nbsp; EVENTS &nbsp;/&nbsp; PRODUCTION', 'font-family: var(--ab-font-body); font-weight: 500; font-size: 13px; letter-spacing: 2.86px; color: rgba(255, 255, 255, 0.85); @media(--mobile) { font-size: 11px; letter-spacing: 2px; }'),
            ]),
            h('Hero title', 'SPACES. EVENTS.<br><s>PRODUCTION.</s>', 'font-size: 94px; letter-spacing: 0.47px; color: #ffffff; @media(--tablet) { font-size: 80px; } @media(--mobile) { font-size: 54px; }', tag='h1', dark=True),
            p('Hero intro', 'Solutions that make an impact — interiors, events, AV, printing, furniture, cleaning and landscape, delivered by one production partner across the UAE.',
              'font-family: var(--ab-font-body); font-weight: 300; font-size: 19px; line-height: 1.6; color: rgba(255, 255, 255, 0.82); max-width: 600px; margin-top: 24px; @media(--mobile) { font-size: 16px; }'),
            box('Hero ctas', 'flex-direction: row; flex-wrap: wrap; gap: 14px; margin-top: 36px; width: auto;', [
                btn('Hero cta primary', 'Start Your Project <span>→</span>', '/contact/', 'abp-btn-grad', 'padding: 18px 30px; font-size: 16px;'),
                btn('Hero cta ghost', 'Explore Our Services', '/services/', 'abp-btn-ghost', 'padding: 18px 30px; font-size: 16px;'),
            ]),
        ], ['abp-hero-in']),
        box('Hero stats', 'flex-direction: row; align-items: center; position: absolute; left: 0; bottom: 0; width: 100%; min-height: 118px; padding: 24px 72px; background: rgba(14, 11, 18, 0.72); backdrop-filter: blur(10px); border-top: 1px solid rgba(255, 255, 255, 0.12); '
            '@media(--tablet) { padding: 22px 32px; } @media(--mobile) { position: relative; display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 18px; padding: 24px 22px; }', [
            stat(1, '07', 'Core capabilities'), stat(2, '08', 'Industries served'), stat(3, '02', 'Locations — Dubai &amp; Sharjah'), stat(4, '01', 'Partner, start to finish'),
        ]),
    ])
    return box('Hero', 'flex-direction: column; padding: 0 24px; @media(--mobile) { padding: 0 12px; }', [card], tag='section')


def marquee():
    return section('Clients marquee', 'padding: 34px 0 0; overflow: hidden;', [
        sc('Clients marquee logos', '[ab_clients_marquee]', 'padding-bottom: 34px;'),
    ], 'max-width: 100%; border-bottom: 1px solid #ece4ee;')


def about():
    pillar = lambda n, num, title, first: box('About pillar %s' % n, 'flex-direction: column; gap: 12px; width: 180px; @media(--mobile) { width: auto; flex: 1; }', [
        box('About pillar %s rule' % n, 'width: 100%%; height: 2px; background: %s;' % ('#c0138a' if first else '#e4dce6'), cls=['abp-pillar__rule']),
        p('About pillar %s num' % n, num, 'font-family: var(--ab-font-body); font-weight: 600; font-size: 12px; letter-spacing: 1.2px; color: #c0138a;'),
        p('About pillar %s title' % n, title, 'font-family: var(--ab-font-display); font-weight: 800; font-size: 24px; line-height: 1; color: #140f18; text-transform: uppercase; @media(--mobile) { font-size: 19px; }', ['abp-pillar__title']),
    ], ['abp-pillar'])
    left = box('About copy', 'flex-direction: column; align-items: flex-start; gap: 0; flex: 1; max-width: 640px;', [
        label('About', '01', 'About ABProduction'),
        h('About title', 'A <em>MULTIDISCIPLINARY</em> PRODUCTION COMPANY <u>BRINGING IDEAS TO LIFE, SEAMLESSLY.</u>', 'font-size: 64px; line-height: 0.96; font-weight: 800; letter-spacing: 0.32px; margin-top: 28px; @media(--tablet) { font-size: 54px; } @media(--mobile) { font-size: 44px; }'),
        p('About text', 'From interiors and large-scale events to bespoke furniture, printing, AV, cleaning and landscape — we bring every capability under one roof, combining creative thinking with reliable execution.', 'max-width: 600px; margin-top: 28px;', ['abp-body']),
        box('About pillars', 'flex-direction: row; gap: 28px; margin-top: 40px; @media(--mobile) { gap: 14px; }', [
            pillar(1, '01', 'One Partner', True), pillar(2, '02', 'Multiple Capabilities', False), pillar(3, '03', 'Complete Solutions', False)]),
        box('About link', 'flex-direction: row; align-items: center; gap: 10px; width: auto; margin-top: 40px;', [
            p('About link text', 'Discover our story', 'font-family: var(--ab-font-body); font-weight: 600; font-size: 15px; color: #140f18;'),
            box('About link icon', 'width: 38px; height: 28px; border-radius: 20px; align-items: center; justify-content: center; background: %s;' % G, [
                p('About link arrow', '→', 'font-family: var(--ab-font-body); font-weight: 600; font-size: 14px; color: #ffffff; line-height: 1;')], ['abp-arrow-link__icon']),
        ], ['abp-arrow-link'], link='/about/'),
    ])
    right = box('About media', 'flex-direction: column; position: relative; width: 560px; flex-shrink: 0; @media(--tablet) { width: 44%; } @media(--mobile) { width: 100%; }', [
        img('About photo', 'about-conference-hall', 'width: 100%; height: 591px; object-fit: cover; border-radius: 24px; @media(--mobile) { height: 380px; }'),
        box('About chip', 'flex-direction: row; align-items: center; gap: 10px; position: absolute; left: 24px; bottom: 24px; width: auto; padding: 10px 18px 10px 16px; border-radius: 12px; background: #ffffff;', [
            p('About chip num', '07', 'font-family: var(--ab-font-display); font-weight: 900; font-size: 26px; line-height: 1; color: #c0138a;'),
            p('About chip text', 'Capabilities under one roof', 'font-family: var(--ab-font-body); font-weight: 500; font-size: 13px; color: #140f18;'),
        ]),
    ])
    return section('About', SEC + ' background: #ffffff;', [
        box('About row', 'flex-direction: row; align-items: center; justify-content: space-between; gap: 80px; @media(--tablet) { gap: 40px; } @media(--mobile) { flex-direction: column; gap: 48px; }', [left, right])
    ])


SERVICES = [
    ('events-production', '03', 'EVENTS<br>PRODUCTION', 'Stage Production  ·  Exhibition Stands  ·  Brand Activations<br>Conferences  ·  Gala &amp; Award Events  ·  Temporary Structures', 'service-events-production', 'grid-column: span 2;'),
    ('interior-fitouts', '01', 'INTERIOR<br>&amp; FITOUTS', 'Commercial &amp; Residential Fit-Out<br>Space Planning  ·  Joinery', 'service-interior-fitouts', ''),
    ('rental-services', '02', 'RENTAL<br>SERVICES', 'LED Screens  ·  Sound Systems<br>Lighting  ·  Stage Equipment', 'service-rental', ''),
    ('bespoke-printing', '04', 'BESPOKE<br>PRINTING', 'Large Format  ·  Event Graphics<br>Packaging &amp; Signage', 'service-printing', ''),
    ('bespoke-furniture', '05', 'BESPOKE<br>FURNITURE', 'Reception Counters<br>Exhibition &amp; Custom Furniture', None, ''),
    ('deep-cleaning', '06', 'DEEP<br>CLEANING', 'Post-Event  ·  Commercial &amp; Office<br>Kitchen Deep Cleaning', 'service-cleaning', ''),
    ('landscape-designing', '07', 'LANDSCAPE<br>DESIGNING', 'Soft &amp; Hard Scape  ·  Irrigation<br>Landscape Maintenance', 'service-landscape', ''),
]


def services():
    cards = []
    for i, (slug, num, title, items, image, span) in enumerate(SERVICES):
        n = 'Service %s' % num
        big = i == 0
        kids = []
        if image:
            kids.append(img(n + ' image', image, 'position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover;', ['abp-svc-card__img']))
            kids.append(box(n + ' shade', 'position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: linear-gradient(180deg, rgba(14, 11, 18, 0.05) 0%, rgba(14, 11, 18, 0.25) 45%, rgba(14, 11, 18, 0.95) 100%);', cls=['abp-svc-card__shade']))
        kids += [
            box(n + ' index', 'flex-direction: row; align-items: center; gap: 10px; position: absolute; top: 30px; left: 28px; width: auto;', [
                p(n + ' index num', num, 'font-family: var(--ab-font-display); font-weight: 700; font-size: 18px; letter-spacing: 1.08px; color: #ffffff; line-height: 1;'),
                box(n + ' index line', 'width: 22px; height: 1.5px; background: rgba(255, 255, 255, 0.5);')]),
            box(n + ' arrow', 'position: absolute; top: 20px; right: 28px; width: 44px; height: 44px; border-radius: 50%; border: 1px solid rgba(255, 255, 255, 0.45); align-items: center; justify-content: center;', [
                p(n + ' arrow icon', '↗', 'font-family: var(--ab-font-body); font-size: 18px; line-height: 1; color: #ffffff;')], ['abp-svc-card__arrow']),
            box(n + ' body', 'flex-direction: column; gap: 14px; position: relative;', [
                h(n + ' title', title, 'font-size: %s; color: #ffffff; @media(--mobile) { font-size: 46px; }' % ('64px' if big else '46px'), tag='h3', dark=True),
                box(n + ' accent', 'width: 32px; height: 2px; background: %s;' % ('#ffffff' if not image else '#e8388a'), cls=['abp-svc-card__accent']),
                p(n + ' list', items, 'font-family: var(--ab-font-body); font-size: 13px; line-height: 1.65; color: rgba(255, 255, 255, 0.72);', ['abp-svc-card__list']),
            ]),
        ]
        bg = 'background: linear-gradient(135deg, #e0287f 0%, #6e0080 72%);' if not image else 'background-color: #1c1622;'
        cards.append(box(n, 'flex-direction: column; justify-content: flex-end; position: relative; overflow: hidden; padding: 28px; border-radius: 24px; min-height: 100%%; %s %s @media(--tablet) { grid-column: span 1; } @media(--mobile) { min-height: 380px; }' % (bg, span),
                         kids, ['abp-svc-card'], tag='a', link='/services/#' + slug))
    head = box('Services head', HEAD_ROW, [
        box('Services head left', 'flex-direction: column; gap: 28px; width: auto;', [
            label('Services', '02', 'Our Expertise', dark=True),
            h('Services title', '<strong>SEVEN</strong> CAPABILITIES.<br>ONE PRODUCTION PARTNER.', 'font-size: 84px; @media(--tablet) { font-size: 64px; } @media(--mobile) { font-size: 46px; }', dark=True),
        ]),
        box('Services head right', 'flex-direction: column; align-items: flex-start; gap: 28px; width: 360px; @media(--mobile) { width: 100%; }', [
            p('Services intro', 'Our core services are designed to cover the physical, functional and experiential needs of businesses, brands, events and spaces.', 'color: rgba(255, 255, 255, 0.7);', ['abp-body']),
            btn('Services all', 'View all services <span>→</span>', '/services/', 'abp-btn-ghost'),
        ]),
    ])
    g = grid('Services grid', 'grid-template-columns: repeat(4, minmax(0, 1fr)); grid-template-rows: 470px 410px; gap: 20px; margin-top: 72px; '
             '@media(--tablet) { grid-template-columns: repeat(2, minmax(0, 1fr)); grid-template-rows: repeat(4, 400px); } @media(--mobile) { grid-template-columns: repeat(1, minmax(0, 1fr)); grid-template-rows: repeat(7, auto); gap: 14px; margin-top: 48px; }', cards)
    return section('Services', SEC + ' ' + DARK_BG + ' overflow: hidden;', [head, g], cls=['abp-dark-fx'])


REASONS = [('01', 'In-House Facilities', 'Greater control over quality and timelines.'),
           ('02', 'One Point of Contact', 'Every requirement coordinated through one partner.'),
           ('03', 'Multiple Capabilities', 'Interiors, furniture, events, print, cleaning and landscape.'),
           ('04', 'Bespoke Solutions', 'Our approach is shaped around your requirement.'),
           ('05', 'Attention to Detail', 'From first specification to final installation.')]


def why():
    rows = []
    for i, (num, title, text) in enumerate(REASONS):
        n = 'Reason %s' % num
        rows.append(box(n, 'flex-direction: row; align-items: center; gap: 28px; padding: 24px 0; border-bottom: 1px solid #e6dee8; %s @media(--mobile) { flex-wrap: wrap; gap: 8px; }' % ('border-top: 1px solid #e6dee8;' if i == 0 else ''), [
            p(n + ' num', num, 'font-family: var(--ab-font-body); font-weight: 600; font-size: 13px; letter-spacing: 1.3px; color: #c0138a;'),
            p(n + ' title', title, 'font-family: var(--ab-font-display); font-weight: 800; font-size: 28px; line-height: 1; color: #140f18; text-transform: uppercase; width: 250px; flex-shrink: 0; @media(--mobile) { width: auto; font-size: 24px; }', ['abp-reason__title']),
            p(n + ' text', text, 'font-family: var(--ab-font-body); font-size: 14px; line-height: 1.6; color: #6a6270; flex: 1; @media(--mobile) { flex-basis: 100%; padding-left: 32px; }'),
        ], ['abp-reason']))
    return section('Why', 'padding: 100px 80px; background: #ffffff; @media(--tablet) { padding: 80px 40px; } @media(--mobile) { padding: 64px 20px; }', [
        box('Why row', 'flex-direction: row; align-items: center; gap: 80px; @media(--tablet) { gap: 40px; } @media(--mobile) { flex-direction: column; gap: 40px; }', [
            box('Why media', 'flex-direction: column; position: relative; overflow: hidden; width: 560px; flex-shrink: 0; border-radius: 28px; @media(--tablet) { width: 42%; } @media(--mobile) { width: 100%; }', [
                img('Why photo', 'why-camera-bokeh', 'width: 100%; height: 682px; object-fit: cover; @media(--tablet) { height: 600px; } @media(--mobile) { height: 360px; }'),
                box('Why grade', 'position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: linear-gradient(180deg, rgba(14, 11, 18, 0) 50%, rgba(14, 11, 18, 0.55) 100%);'),
            ]),
            box('Why copy', 'flex-direction: column; flex: 1; gap: 0;', [
                label('Why', '03', 'Why ABProduction'),
                h('Why title', 'WHY CHOOSE<br><em>AB</em>PRODUCTION', 'font-size: 72px; margin-top: 28px; margin-bottom: 44px; @media(--mobile) { font-size: 50px; margin-bottom: 28px; }'),
                box('Why reasons', 'flex-direction: column; gap: 0;', rows),
            ]),
        ])
    ])


INDUSTRIES = [('01', 'Corporate', 'Offices · Conferences · Corporate Events · Branding'),
              ('02', 'Retail', 'Stores · Displays · Fit-Outs · Brand Experiences'),
              ('03', 'Hospitality', 'Hotels · Restaurants · Cafés · Guest Experiences'),
              ('04', 'Events', 'Exhibitions · Activations · Launches · Celebrations'),
              ('05', 'Real Estate', 'Showrooms · Sales Centres · Fit-Outs · Landscaping'),
              ('06', 'Government', 'Facilities · Events · Public Spaces · Infrastructure Support'),
              ('07', 'Schools', 'Facilities · Events · Public Spaces · Infrastructure Support'),
              ('08', 'Agencies', 'Production Support · Campaign Execution · Fabrication · Event Solutions')]


def industries(num='04'):
    cards = []
    for num, title, text in INDUSTRIES:
        n = 'Industry %s' % num
        cards.append(box(n, 'flex-direction: column; justify-content: space-between; gap: 18px; position: relative; overflow: hidden; min-height: 224px; padding: 29px 27px 30px; border-radius: 20px; border: 1px solid #ece4ee; background: #ffffff; @media(--mobile) { min-height: 190px; }', [
            box(n + ' index', 'flex-direction: row; align-items: center; gap: 10px; width: auto;', [
                p(n + ' num', num, 'font-family: var(--ab-font-body); font-weight: 600; font-size: 12px; letter-spacing: 1.44px; color: #c0138a;', ['abp-ind-card__num']),
                box(n + ' line', 'width: 24px; height: 1.5px; background: rgba(192, 19, 138, 0.5);', cls=['abp-ind-card__line'])]),
            box(n + ' arrow', 'position: absolute; top: 22px; right: 22px; width: 36px; height: 36px; border-radius: 50%; background: #ffffff; align-items: center; justify-content: center;', [
                p(n + ' arrow icon', '↗', 'font-family: var(--ab-font-body); font-weight: 600; font-size: 16px; line-height: 1; color: #a4007c;')], ['abp-ind-card__arrow']),
            box(n + ' body', 'flex-direction: column; gap: 10px;', [
                p(n + ' title', title, 'font-family: var(--ab-font-display); font-weight: 800; font-size: 38px; line-height: 1; color: #140f18; text-transform: uppercase;', ['abp-ind-card__title']),
                p(n + ' text', text, 'font-family: var(--ab-font-body); font-size: 13px; line-height: 1.6; color: #6a6270;', ['abp-ind-card__text']),
            ]),
        ], ['abp-ind-card']))
    head = box('Industries head', HEAD_ROW, [
        box('Industries head left', 'flex-direction: column; gap: 28px; width: auto;', [
            label('Industries', num, 'Industries We Serve'),
            h('Industries title', 'BUILT FOR DIFFERENT INDUSTRIES.<br><strong>READY</strong> FOR DIFFERENT CHALLENGES.', 'font-size: 64px; line-height: 0.94; @media(--tablet) { font-size: 52px; } @media(--mobile) { font-size: 40px; }'),
        ]),
        p('Industries intro', 'Our multidisciplinary capabilities allow us to work across a wide range of sectors.', 'width: 360px; @media(--mobile) { width: 100%; }', ['abp-body']),
    ])
    g = grid('Industries grid', 'grid-template-columns: repeat(4, minmax(0, 1fr)); grid-template-rows: repeat(2, auto); gap: 20px; margin-top: 64px; '
             '@media(--tablet) { grid-template-columns: repeat(2, minmax(0, 1fr)); grid-template-rows: repeat(4, auto); } @media(--mobile) { grid-template-columns: repeat(1, minmax(0, 1fr)); grid-template-rows: repeat(8, auto); gap: 14px; margin-top: 40px; }', cards)
    return section('Industries', SEC + ' background: #f7f3f8;', [head, g], anchor='industries')


def projects():
    head = box('Projects head', HEAD_ROW, [
        box('Projects head left', 'flex-direction: column; gap: 28px; width: auto;', [
            label('Projects', '05', 'Our Projects', dark=True),
            h('Projects title', 'WORK THAT<br>TURNS ON <strong>COLORS.</strong>', 'font-size: 84px; @media(--tablet) { font-size: 64px; } @media(--mobile) { font-size: 50px; }', dark=True),
        ]),
        sc('Projects filters', '[ab_project_filters target="home-projects" style="pills"]', 'width: auto; max-width: 100%;'),
    ])
    return section('Projects', SEC + ' ' + DARK_BG + ' overflow: hidden;', [
        head,
        sc('Projects mosaic', '[ab_projects layout="featured" id="home-projects" button="View all projects"]', 'margin-top: 56px;'),
    ], anchor='projects', cls=['abp-dark-fx', 'abp-dark-fx--right'])


def cta(prefix='CTA', page_bg='#ffffff'):
    return section(prefix, 'padding: 70px 80px 50px; background: %s; @media(--tablet) { padding: 56px 40px 40px; } @media(--mobile) { padding: 40px 20px 32px; }' % page_bg, [
        box(prefix + ' panel', 'flex-direction: row; align-items: center; justify-content: space-between; gap: 40px; position: relative; overflow: hidden; min-height: 320px; padding: 64px 72px; border-radius: 32px; '
            'background: linear-gradient(90deg, #6e0080 0%, #a4007c 55%, #e0287f 100%); @media(--tablet) { padding: 56px 48px; } @media(--mobile) { flex-direction: column; align-items: flex-start; padding: 44px 26px; border-radius: 24px; }', [
            box(prefix + ' slashes', 'position: absolute; top: 0; right: 0; width: 46%; height: 100%;', cls=['abp-cta-slashes']),
            box(prefix + ' orb', 'position: absolute; top: -200px; left: -120px; width: 420px; height: 420px; border-radius: 50%; background: rgba(255, 255, 255, 0.08);'),
            box(prefix + ' copy', 'flex-direction: column; gap: 14px; position: relative; width: auto; max-width: 720px;', [
                p(prefix + ' eyebrow', 'HAVE A PROJECT IN MIND?', 'font-family: var(--ab-font-body); font-weight: 600; font-size: 13px; letter-spacing: 2.86px; color: rgba(255, 255, 255, 0.8);'),
                h(prefix + ' title', 'LET’S TURN ON THE COLORS.', 'font-size: 68px; color: #ffffff; @media(--mobile) { font-size: 46px; }', dark=True),
                p(prefix + ' text', 'Interiors, an event, a stand — or all of it. Tell us what you’re planning and our team will take it from concept to installation.', 'font-family: var(--ab-font-body); font-size: 16px; line-height: 1.65; color: rgba(255, 255, 255, 0.85); max-width: 560px;'),
            ]),
            btn(prefix + ' button', 'Get a Free Consultation <span>→</span>', '/contact/', 'abp-btn-white', 'position: relative; min-width: 330px; @media(--mobile) { min-width: 0; width: 100%; }'),
        ])
    ])


def contact(prefix='Contact', label_num='06', label_text='Let’s Connect', title='LET’S WORK ON YOUR<br>NEXT PROJECT <strong>TOGETHER.</strong>'):
    return section(prefix, SEC + ' background: #ffffff;', [
        box(prefix + ' row', 'flex-direction: row; align-items: flex-start; justify-content: space-between; gap: 80px; @media(--tablet) { flex-direction: column; gap: 48px; }', [
            box(prefix + ' left', 'flex-direction: column; gap: 0; width: 560px; flex-shrink: 0; @media(--tablet) { width: 100%; }', [
                label(prefix, label_num, label_text),
                h(prefix + ' title', title, 'font-size: 58px; line-height: 0.94; margin-top: 28px; @media(--mobile) { font-size: 44px; }'),
                p(prefix + ' text', 'Whether it’s a commercial fit-out, a large-scale event, an exhibition stand or bespoke furniture, our team is ready to turn your ideas into reality.', 'max-width: 470px; margin-top: 24px; margin-bottom: 40px;', ['abp-body']),
                sc(prefix + ' card', '[ab_contact_card]'),
            ]),
            box(prefix + ' form card', 'flex-direction: column; gap: 16px; width: 640px; padding: 44px; border-radius: 28px; border: 1px solid #efe7f1; background: #ffffff; box-shadow: 0 30px 60px rgba(89, 0, 77, 0.12); @media(--tablet) { width: 100%; } @media(--mobile) { padding: 26px 20px; border-radius: 22px; }', [
                p(prefix + ' form title', 'Request a free consultation', 'font-family: var(--ab-font-display); font-weight: 800; font-size: 36px; line-height: 1; color: #140f18; @media(--mobile) { font-size: 30px; }'),
                p(prefix + ' form intro', 'Share a few details and our team will get back to you shortly.', 'font-family: var(--ab-font-body); font-size: 14px; line-height: 1.5; color: #6a6270; margin-bottom: 8px;'),
                sc(prefix + ' form', '[contact-form-7 title="AB Productions Enquiry"]'),
            ]),
        ])
    ], anchor='contact')


if __name__ == '__main__':
    which = sys.argv[1:] or ['hero', 'marquee', 'about', 'services', 'why', 'industries', 'projects', 'cta', 'contact']
    fns = {'hero': hero, 'marquee': marquee, 'about': about, 'services': services, 'why': why, 'industries': industries, 'projects': projects, 'cta': cta, 'contact': contact}
    first = True
    for w in which:
        build(HOME, [fns[w]()], mode='replace_children' if (first and w == 'hero') else 'append')
        first = False
