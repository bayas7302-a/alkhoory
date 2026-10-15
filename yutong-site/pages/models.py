"""Models page (Figma frame 1:288 'Model') -> WP page 9."""
import sys, os
sys.path.insert(0, os.path.join(os.path.dirname(__file__), '..', 'tools'))
from parts import *

POST = 9
SLUG = 'models'

# Copy is verbatim from the Figma file (placeholder text pending client content)
HERO_TEXT = 'Yutong has launched 6-15m battery-electric coaches for new energy market segments such as long-distance e-coach, tourist e-coach, commuter, and intercity e-coach.'
TEL = 'tel:+97143146235'
MAIL = 'mailto:info@subaru-uae.com'


# ---------- hero ----------

def hero():
    quick = [('Whatsapp', 'models-ic-chat', URL['contact']), ('Hotline', 'models-ic-phone', TEL), ('E-mail', 'models-ic-mail', MAIL)]
    extra = [
        flex('Hero Buttons', 'width: auto; padding: 0; gap: 14px; flex-wrap: wrap;', [
            btn('Hero Explore', 'Explore Range', '#range'),
            btn('Hero Contact', 'Contact us', URL['contact'], kind='outline', arrow=False),
        ]),
        flex('Quick Contact', 'width: auto; padding: 18px 24px; gap: 12px; align-items: center; flex-wrap: wrap; border-radius: 20px; background-color: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); backdrop-filter: blur(12px); @media(--mobile) { padding: 14px; gap: 8px; }', [
            flex('Quick %s' % t, 'width: auto; padding: 10px 18px; gap: 10px; align-items: center; border-radius: 40px; background-color: rgba(255,255,255,0.12); %s &:hover { background-color: rgba(255,255,255,0.22); } @media(--mobile) { padding: 9px 14px; }' % EASE, [
                img('Quick %s Icon' % t, ic, 'width: 17px;'),
                p('Quick %s Label' % t, t, FB + 'font-size: 14px; font-weight: 500; color: #ffffff; margin: 0;').c('yt-nowrap'),
            ], href=href) for t, ic, href in quick]),
    ]
    return page_hero('models', [('Home', 'home'), ('Products', 'models'), ('E-Coach', None)], 'E-Coach', HERO_TEXT, 'models-hero', 640, extra)


# ---------- catalogue ----------

def chip(cid, text, on=False):
    return p(cid, text, FB + 'width: auto; padding: 8px 14px; border-radius: 30px; font-size: 13px; line-height: 20px; margin: 0; cursor: pointer; %s' % (
        'font-weight: 500; color: #ffffff; background-color: #0160b4; border: 1px solid #0160b4;' if on else
        'font-weight: 400; color: #323232; background-color: #ffffff; border: 1px solid #e3e9f1; transition: border-color 0.3s; &:hover { border-color: #0160b4; }')).c('yt-nowrap')


def filter_group(n, label, icon, values):
    return flex('Filter %s' % n, 'padding: 0; flex-direction: column; gap: 12px;', [
        flex('Filter %s Head' % n, 'width: auto; padding: 0; gap: 8px; align-items: center;', [
            img('Filter %s Icon' % n, icon, 'width: 16px;'),
            p('Filter %s Label' % n, label, FB + 'font-size: 14px; font-weight: 600; color: #00050b; margin: 0;'),
        ]),
        flex('Filter %s Chips' % n, 'padding: 0; gap: 8px; flex-wrap: wrap;',
             [chip('Filter %s Chip %d' % (n, i + 1), v, i == 0) for i, v in enumerate(values)]),
    ])


def model_card(i):
    n = 'Model Card %d' % i
    return flex(n, 'padding: 0; flex: 1 1 0; min-width: 0; flex-direction: column; background-color: #ffffff; border: 1px solid #e3e9f1; border-radius: 22px; overflow: hidden; box-shadow: 0 10px 28px rgba(0,26,64,0.08); %s @media(--mobile) { flex: 0 0 auto; }' % EASE, [
        flex(n + ' Media', 'padding: 26px 20px 10px; flex-direction: column; align-items: center; background-color: #f2f6fb;', [
            img(n + ' Photo', 'models-t12e', 'width: 100%; height: 110px; object-fit: contain;'),
        ]).c('yt-zoom'),
        flex(n + ' Body', 'padding: 22px; flex-direction: column; gap: 10px;', [
            flex(n + ' Top', 'padding: 0; justify-content: space-between; align-items: center; gap: 8px;', [
                h(n + ' Name', 'T12E', 'h3', FD + 'font-size: 22px; font-weight: 700; line-height: 28px; color: #003376; margin: 0;', href=URL['quote']),
                p(n + ' Tag', 'E-Coach', FB + 'width: auto; padding: 4px 10px; border-radius: 20px; background-color: #e3effa; font-size: 11px; font-weight: 600; color: #0160b4; margin: 0;').c('yt-nowrap'),
            ]),
            flex(n + ' Seats', 'width: auto; padding: 0; gap: 8px; align-items: center;', [
                img(n + ' Seat Icon', 'models-ic-seat-grey', 'width: 16px;'),
                p(n + ' Seats Text', 'Seats: 51/53/55', FB + 'font-size: 13px; color: #747474; margin: 0;'),
            ]),
            flex(n + ' More', 'width: auto; padding: 6px 0 0; gap: 6px; align-items: center;', [
                p(n + ' More Text', 'View details', FB + 'font-size: 14px; font-weight: 500; color: #0160b4; margin: 0;'),
                img(n + ' More Arrow', 'models-ic-arrow-b16', 'width: 16px;'),
            ], href=URL['quote']),
        ]),
    ]).c('yt-lift')


def catalogue():
    rows = [flex('Model Row %d' % (r + 1), 'padding: 0; gap: 24px; align-items: stretch; @media(--mobile) { flex-direction: column; }',
                 [model_card(r * 3 + c + 1) for c in range(3)]) for r in range(2)]
    return flex('Catalogue', 'padding: 110px 120px; flex-direction: column; gap: 40px; background-color: #ffffff; position: relative; @media(--tablet) { padding: 80px 40px; } @media(--mobile) { padding: 64px 20px; gap: 32px; }', [
        anchor('range'),
        flex('Catalogue Head', 'padding: 0; justify-content: space-between; align-items: flex-end; gap: 30px; @media(--tablet) { flex-direction: column; align-items: flex-start; gap: 24px; }', [
            flex('Catalogue Head Text', 'width: auto; padding: 0; flex-direction: column; gap: 14px;', [
                eyebrow('Catalogue Eyebrow', 'Our Range'),
                title('Catalogue Title', 'Sustainable ' + hl('Public Transport') + ' Solutions', 'font-size: 42px; font-weight: 700; line-height: 52px; color: #00050b; @media(--tablet) { font-size: 36px; line-height: 44px; } @media(--mobile) { font-size: 30px; line-height: 38px; }'),
            ]),
            flex('Catalogue Search', 'width: 340px; max-width: 100%; flex: 0 0 auto; height: 52px; padding: 0 6px 0 18px; gap: 10px; align-items: center; background-color: #f2f6fb; border: 1px solid #e3e9f1; border-radius: 40px;', [
                img('Catalogue Search Icon', 'models-ic-search', 'width: 18px;'),
                p('Catalogue Search Text', 'Search', FB + 'flex: 1 1 0; font-size: 14px; color: #747474; margin: 0;'),
                flex('Catalogue Search Go', 'width: 40px; min-width: 40px; height: 40px; padding: 0; border-radius: 20px; background-color: #0160b4; justify-content: center; align-items: center;', [
                    img('Catalogue Search Arrow', 'models-ic-arrow-w168', 'width: 17px;'),
                ]),
            ]),
        ]),
        flex('Catalogue Body', 'padding: 0; gap: 32px; align-items: flex-start; @media(--tablet) { flex-direction: column; align-items: stretch; } @media(--mobile) { gap: 24px; }', [
            flex('Filters', 'width: 290px; flex: 0 0 auto; padding: 28px; flex-direction: column; gap: 28px; background-color: #f2f6fb; border-radius: 24px; @media(--tablet) { width: 100%; flex-direction: row; flex-wrap: wrap; } @media(--mobile) { flex-direction: column; padding: 24px 20px; gap: 22px; }', [
                flex('Filters Head', 'width: auto; padding: 0; gap: 10px; align-items: center; @media(--tablet) { width: 100%; }', [
                    img('Filters Icon', 'models-ic-filter', 'width: 18px;'),
                    p('Filters Title', 'Filters', FD + 'font-size: 18px; font-weight: 600; color: #00050b; margin: 0;'),
                ]),
                filter_group('Seats', 'Seats', 'models-ic-seat', ['All', '10-30', '31-40', '41-50', '51-60', '61 or']),
                filter_group('Length', 'Length (m)', 'models-ic-filter-sm', ['All', '7-8', '8-9', '10-11', '11-12', '13 or']),
            ]),
            flex('Model Grid', 'padding: 0; flex: 1 1 0; min-width: 0; flex-direction: column; gap: 24px; @media(--tablet) { flex: 0 0 auto; }', rows),
        ]),
    ], tag='section')


# ---------- new energy technology ----------

TECH_TABS = [('Electric Control', 'models-ic-chip'), ('Battery', 'models-ic-battery'), ('E-Drive', 'models-ic-cog')]


def stat_row(cid, label, value):
    return flex(cid, 'padding: 14px 18px; justify-content: space-between; align-items: center; gap: 12px; background-color: #f2f6fb; border-radius: 14px;', [
        p(cid + ' Label', label, FB + 'font-size: 14px; color: #323232; margin: 0;'),
        p(cid + ' Value', value, FD + 'font-size: 22px; font-weight: 700; line-height: 28px; color: #0160b4; margin: 0;'),
    ])


def tech_card(cat, idx):
    n = 'Tech %d' % idx
    if idx == 0:
        info = [
            p(n + ' Eyebrow', 'Electric Control', FB + 'font-size: 13px; font-weight: 600; letter-spacing: 1.3px; color: #0160b4; margin: 0;'),
            h(n + ' Title', 'Multi-in- One SiC Power Domain Controller', 'h3', FD + 'max-width: 420px; font-size: 30px; font-weight: 700; line-height: 38px; color: #00050b; margin: 0; @media(--mobile) { font-size: 24px; line-height: 32px; }'),
            flex(n + ' Stats', 'padding: 0; flex-direction: column; gap: 12px;', [
                stat_row(n + ' Stat 1', 'Maximum Efficiency', '99.5%'),
                stat_row(n + ' Stat 2', 'System Weight Reduction', '50%'),
                stat_row(n + ' Stat 3', 'Energy Consumption Lowered by', '5%'),
            ]),
            btn(n + ' More', 'Read more', URL['about'], kind='outline-blue'),
        ]
    else:
        # only the Electric Control panel is designed in Figma; the others hold a holding message
        info = [
            p(n + ' Eyebrow', cat, FB + 'font-size: 13px; font-weight: 600; letter-spacing: 1.3px; color: #0160b4; margin: 0;'),
            h(n + ' Title', cat, 'h3', FD + 'font-size: 30px; font-weight: 700; line-height: 38px; color: #00050b; margin: 0; @media(--mobile) { font-size: 24px; line-height: 32px; }'),
            body(n + ' Text', 'Details of Yutong %s technology are being added to the site. Contact our team for specifications and availability in the UAE.' % cat, 'line-height: 26px;', '#747474'),
            btn(n + ' More', 'Contact us', URL['contact'], kind='outline-blue'),
        ]
    return flex(n + ' Card', 'padding: 40px 48px; gap: 48px; align-items: center; background-color: #ffffff; border-radius: 28px; box-shadow: 0 10px 14px rgba(0,26,64,0.08); @media(--tablet) { flex-direction: column; align-items: stretch; padding: 32px; gap: 32px; } @media(--mobile) { padding: 20px; gap: 24px; border-radius: 22px; }', [
        img(n + ' Photo', 'models-tech', 'width: 560px; max-width: 100%; flex: 0 0 auto; height: 340px; object-fit: cover; border-radius: 22px; @media(--tablet) { width: 100%; } @media(--mobile) { height: 220px; border-radius: 16px; }'),
        flex(n + ' Info', 'padding: 0; flex: 1 1 0; min-width: 0; flex-direction: column; align-items: stretch; gap: 20px; @media(--tablet) { flex: 0 0 auto; }', info),
    ])


def technology():
    tabs = [Node('e-tab', 'Tech Tab ' + t, {}, FB + 'width: auto; padding: 11px 22px; gap: 8px; display: flex; align-items: center; border-width: 0; border-radius: 34px; background-color: transparent; font-size: 14px; color: #323232; cursor: pointer; @media(--mobile) { padding: 9px 14px; }',
                 [img('Tech Tab %s Icon' % t, ic, 'width: 16px;'), p('Tech Tab %s Label' % t, t, FB + 'font-size: 14px; margin: 0;').c('yt-nowrap')]).c('yt-tab', 'yt-models-tab-ic') for t, ic in TECH_TABS]
    panels = [Node('e-tab-content', 'Tech Panel ' + t, {}, 'padding: 0;', [tech_card(t, i)]) for i, (t, _) in enumerate(TECH_TABS)]
    return flex('New Energy Technology', 'padding: 110px 120px; flex-direction: column; gap: 44px; background-color: #f2f6fb; position: relative; @media(--tablet) { padding: 80px 40px; gap: 32px; } @media(--mobile) { padding: 64px 20px; gap: 28px; }', [
        anchor('technology'),
        flex('Tech Head', 'width: auto; padding: 0; flex-direction: column; gap: 14px;', [
            eyebrow('Tech Eyebrow', 'Innovation'),
            title('Tech Title', 'New Energy ' + hl('Technology'), 'font-size: 42px; font-weight: 700; line-height: 52px; color: #00050b; @media(--tablet) { font-size: 36px; line-height: 44px; } @media(--mobile) { font-size: 30px; line-height: 38px; }'),
        ]),
        Node('e-tabs', 'Tech Tabs', {}, 'padding: 0; gap: 44px; align-items: stretch; @media(--tablet) { gap: 28px; }', [
            Node('e-tabs-menu', 'Tech Tabs Menu', {}, 'width: auto; max-width: 100%; align-self: flex-end; margin-top: -101px; padding: 6px; gap: 4px; display: flex; flex-wrap: wrap; background-color: #ffffff; border: 1px solid #e3e9f1; border-radius: 40px; @media(--tablet) { align-self: flex-start; margin-top: 0; } @media(--mobile) { border-radius: 24px; }', tabs),
            Node('e-tabs-content-area', 'Tech Tabs Content', {}, 'width: 100%; padding: 0;', panels),
        ]),
    ], tag='section')


# ---------- best service ----------

def best_service():
    return flex('Best Service', 'padding: 0 120px 110px; background-color: #f2f6fb; @media(--tablet) { padding: 0 40px 80px; } @media(--mobile) { padding: 0 16px 64px; }', [
        flex('Service Card', 'padding: 0; align-items: stretch; border-radius: 28px; overflow: hidden; background-image: linear-gradient(90deg, #0160b4 0%, #003376 100%); @media(--tablet) { flex-direction: column; } @media(--mobile) { border-radius: 24px; }', [
            flex('Service Text', 'padding: 56px; flex: 1 1 0; min-width: 0; flex-direction: column; justify-content: center; align-items: flex-start; gap: 20px; @media(--tablet) { flex: 0 0 auto; padding: 44px 32px; } @media(--mobile) { padding: 32px 20px; }', [
                eyebrow('Service Eyebrow', 'After-Sales', '#ffffff'),
                h('Service Title', 'Best Service for Your Bus', 'h2', FD + 'font-size: 38px; font-weight: 700; line-height: 46px; color: #ffffff; margin: 0; @media(--mobile) { font-size: 28px; line-height: 36px; }'),
                body('Service Body', "Yutong's integrated service ecosystem delivers comprehensive maintenance solutions. Powered by Yutong Cloud, our smart platform anticipates your needs, offering exceptional end-to-end service experiences.", 'line-height: 26px;', 'rgba(255,255,255,0.85)'),
                btn('Service More', 'Read more', URL['ownership'], kind='white'),
            ]),
            img('Service Photo', 'models-service', 'width: 560px; max-width: 100%; flex: 0 0 auto; height: 400px; object-fit: cover; @media(--tablet) { width: 100%; height: 340px; } @media(--mobile) { height: 220px; }'),
        ]),
    ], tag='section')


# ---------- explore + subscribe ----------

def explore_card(i, label, photo, href):
    n = 'Explore Card %d' % i
    return flex(n, '''padding: 24px; flex: 1 1 0; min-width: 0; height: 300px; flex-direction: column; justify-content: flex-end; border-radius: 24px; overflow: hidden;
        background-color: #072b52; background-image: linear-gradient(180deg, rgba(0,5,11,0) 35%%, rgba(7,43,82,0.9) 100%%), %s; background-size: cover; background-position: center center;
        %s @media(--mobile) { flex: 0 0 auto; height: 240px; }''' % (bg(photo), EASE), [
        flex(n + ' Foot', 'padding: 0; justify-content: space-between; align-items: center; gap: 12px;', [
            h(n + ' Title', label, 'h3', FD + 'font-size: 22px; font-weight: 600; color: #ffffff; margin: 0;'),
            flex(n + ' Go', 'width: 40px; min-width: 40px; height: 40px; padding: 0; border-radius: 20px; background-color: #ffffff; justify-content: center; align-items: center;', [
                img(n + ' Arrow', 'models-ic-arrow-b168', 'width: 17px;'),
            ]),
        ]),
    ], href=href).c('yt-lift')


def explore():
    cards = [('Events', 'models-events', URL['news']), ('Service network', 'models-service-network', URL['ownership']), ('Message', 'models-message', URL['contact'])]
    return flex('Explore', 'padding: 110px 120px; flex-direction: column; align-items: center; gap: 40px; background-color: #ffffff; @media(--tablet) { padding: 80px 40px; } @media(--mobile) { padding: 64px 20px; gap: 28px; }', [
        flex('Explore Head', 'width: auto; padding: 0; flex-direction: column; align-items: center; gap: 14px;', [
            eyebrow('Explore Eyebrow', 'Discover', center=True),
            title('Explore Title', 'Lorem Ipsum Eednmm', 'font-size: 42px; font-weight: 700; line-height: 52px; color: #00050b; text-align: center; @media(--tablet) { font-size: 36px; line-height: 44px; } @media(--mobile) { font-size: 30px; line-height: 38px; }'),
        ]),
        flex('Explore Cards', 'padding: 0; gap: 24px; @media(--mobile) { flex-direction: column; gap: 16px; }',
             [explore_card(i + 1, *c) for i, c in enumerate(cards)]),
        flex('Subscribe', '''min-height: 260px; padding: 40px 48px; justify-content: space-between; align-items: center; gap: 32px; border-radius: 28px; overflow: hidden;
            background-color: #072b52; background-image: linear-gradient(90deg, rgba(7,43,82,0.95) 0%%, rgba(7,43,82,0.7) 70%%, rgba(7,43,82,0.3) 100%%), %s;
            background-size: cover; background-position: center center;
            @media(--tablet) { flex-direction: column; align-items: flex-start; min-height: 0; padding: 40px 32px; gap: 24px; }
            @media(--mobile) { padding: 32px 20px; border-radius: 24px; }''' % bg('models-subscribe'), [
            flex('Subscribe Text', 'width: auto; padding: 0; flex-direction: column; gap: 8px;', [
                h('Subscribe Title', 'Subscribe Now', 'h2', FD + 'font-size: 34px; font-weight: 700; line-height: 1.25; color: #ffffff; margin: 0; @media(--mobile) { font-size: 28px; }'),
                p('Subscribe Sub', 'Get updates you care about.', FB + 'font-size: 16px; color: rgba(255,255,255,0.8); margin: 0;'),
            ]),
            flex('Subscribe Field', 'width: 460px; max-width: 100%; flex: 0 0 auto; padding: 6px 6px 6px 22px; gap: 10px; align-items: center; background-color: #ffffff; border-radius: 40px; @media(--tablet) { width: 100%; } @media(--mobile) { padding-left: 16px; }', [
                img('Subscribe Mail Icon', 'models-ic-mail-grey', 'width: 18px; @media(--mobile) { display: none; }'),
                p('Subscribe Placeholder', 'Please Enter your email address', FB + 'flex: 1 1 0; min-width: 0; font-size: 14px; color: #747474; margin: 0; @media(--mobile) { font-size: 13px; }'),
                btn('Subscribe Submit', 'Submit', URL['contact'], size='sm'),
            ]),
        ]),
    ], tag='section')


SECTIONS = [
    ('models-1', hero),
    ('models-2', lambda: [catalogue()]),
    ('models-3', lambda: [technology(), best_service()]),
    ('models-4', lambda: [explore(), glance_section()]),
    ('models-5', lambda: [quote_section('0 120px 110px'), footer()]),
]
