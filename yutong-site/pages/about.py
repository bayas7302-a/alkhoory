"""About page (Figma frame 1:955 'About') -> WP page 10."""
import sys, os
sys.path.insert(0, os.path.join(os.path.dirname(__file__), '..', 'tools'))
from parts import *

POST = 10
SLUG = 'about'

# Copy sources: en.yutong.com /about/about-yutong/, /about/history/, /about/honor/, /service/,
# /technology/ and /technology/reliability-and-durability/
LINEUP = "Yutong's comprehensive lineup spans across various sectors, including city buses, long distance coaches, tourist coaches, commuters, school buses and special-purpose vehicles."
FEAT_SAFETY = ("A super-strength closed-ring body keeps rollover deformation minimal at angles of up to 45&deg;, and Yutong's "
               "new-generation batteries add a &ldquo;sandwich&rdquo; fire-resistance structure and nitrogen protection system.")
FEAT_COMFORT = ("From an intelligent connected driver cockpit and efficient heat-pump climate control to wheelchair lifts on "
                "coaches such as the T15E, every Yutong is designed around the people on board.")
FEAT_GLOBAL = ("With 20+ overseas branches, 10+ overseas KD plants and 415+ authorized service outlets, Yutong supports operators "
               "worldwide, and in the UAE through Al Khoory Automobiles, its official distributor.")


def hero():
    buttons = flex('Hero Buttons', 'width: auto; padding: 0; gap: 14px; flex-wrap: wrap;', [
        btn('Hero History', 'Our History', '#our-history'),
        btn('Hero Contact', 'Contact Us', URL['contact'], kind='outline', arrow=False),
    ])
    return page_hero('about', [('Home', 'home'), ('About', None)], 'Better Bus, ' + hl('Better Life'),
                     'As a leading global electric bus manufacturer, Yutong caters to a wide array of segments, including city buses, long distance coaches, tourist coaches, commuters, school buses and special-purpose vehicles.',
                     'about-hero', 700, extra=[buttons])


# ---------- Why Choose Us (5:52) ----------

def why():
    feats = [('Safety First', 'about-ic-safety', True, FEAT_SAFETY), ('Comfortable Experience', 'about-ic-comfort', False, FEAT_COMFORT), ('Global Operations', 'about-ic-global', False, FEAT_GLOBAL)]
    cards = []
    for i, (t, ic, blue, txt) in enumerate(feats):
        n = 'Feature %d' % (i + 1)
        cards.append(flex(n, 'padding: 30px; flex: 1 1 0; flex-direction: column; gap: 16px; align-self: stretch; border-radius: 24px; background-color: %s; @media(--tablet) { flex: 1 1 40%%; } @media(--mobile) { flex: 0 0 auto; padding: 26px; }' % ('#0160b4' if blue else '#f2f6fb'), [
            flex(n + ' Icon', 'width: 56px; min-width: 56px; height: 56px; padding: 0; border-radius: 16px; background-color: #ffffff; justify-content: center; align-items: center;', [img(n + ' Glyph', ic, 'width: 26px; height: 26px; object-fit: contain;')]),
            h(n + ' Title', t, 'h3', FD + 'font-size: 20px; font-weight: 600; line-height: 1.25; color: %s; margin: 0;' % ('#ffffff' if blue else '#00050b')),
            p(n + ' Text', txt, FB + 'font-size: 14px; line-height: 23px; color: %s; margin: 0;' % ('rgba(255,255,255,0.85)' if blue else '#747474')),
        ]).c('yt-lift'))
    return flex('Why Choose Us', 'padding: 110px 120px; flex-direction: column; gap: 48px; background-color: #ffffff; @media(--tablet) { padding: 80px 40px; } @media(--mobile) { padding: 64px 20px; gap: 36px; }', [
        flex('Why Row', 'padding: 0; gap: 64px; align-items: center; @media(--tablet) { flex-direction: column; align-items: stretch; gap: 48px; }', [
            div('Why Collage', 'width: 540px; max-width: 100%; flex: 0 0 auto; height: 520px; padding: 0; position: relative; @media(--tablet) { align-self: center; } @media(--mobile) { height: 380px; }', [
                img('Why Photo A', 'ytabout2-arches', 'position: absolute; left: 0; top: 0; width: 440px; max-width: 82%; height: 380px; object-fit: cover; border-radius: 26px; @media(--mobile) { height: 270px; }'),
                img('Why Photo B', 'ytabout2-sydney', 'position: absolute; left: 220px; top: 280px; width: 320px; max-width: 60%; height: 240px; object-fit: cover; border-radius: 22px; border: 8px solid #ffffff; @media(--mobile) { left: auto; right: 0; top: 200px; height: 180px; }'),
                flex('Why Badge', 'position: absolute; left: 24px; top: 330px; width: auto; padding: 18px 22px; flex-direction: column; background-color: #0160b4; border-radius: 20px; box-shadow: 0 20px 24px rgba(0,26,64,0.16); @media(--mobile) { left: 12px; top: 236px; padding: 14px 18px; }', [
                    p('Why Badge Number', '130,000', FD + 'font-size: 30px; font-weight: 700; color: #ffffff; margin: 0; line-height: 1.25; @media(--mobile) { font-size: 24px; }'),
                    p('Why Badge Label', 'buses exported', FB + 'font-size: 12px; color: rgba(255,255,255,0.85); margin: 0;'),
                ]),
            ]),
            flex('Why Text', 'padding: 0; flex: 1 1 0; flex-direction: column; gap: 18px;', [
                eyebrow('Why Eyebrow', 'Why Choose Us'),
                title('Why Title', 'Better Bus, ' + hl('Better Life'), 'font-size: 42px; font-weight: 700; line-height: 52px; color: #00050b; @media(--tablet) { font-size: 36px; line-height: 44px; } @media(--mobile) { font-size: 30px; line-height: 38px; }'),
                body('Why Text 1', LINEUP, 'line-height: 26px;', '#747474'),
                body('Why Text 2', 'Yutong has exported nearly 130,000 buses to more than 100 countries and regions, capturing over 10% of the global market share. With over 210,000 new energy buses sold, Yutong leads the industry in sales. This performance solidifies its position as a top global electric bus manufacturer and a major player in the bus industry.', 'line-height: 26px;', '#747474'),
            ]),
        ]),
        flex('Feature Cards', 'padding: 0; gap: 24px; align-items: stretch; @media(--tablet) { flex-wrap: wrap; } @media(--mobile) { flex-direction: column; }', cards),
    ], tag='section')


# ---------- Global Expansion (5:84) ----------

def expansion():
    stats = [('320+', 'Contracted Service Companies', 'about-ic-build'), ('415+', 'Authorized service outlets', 'about-ic-wrench'),
             ('20+', 'Overseas Branches', 'about-ic-pin'), ('10+', 'Overseas KD Plants', 'about-ic-factory')]
    cards = []
    for i, (num, t, ic) in enumerate(stats):
        blue = i == 0
        n = 'Expansion Stat %d' % (i + 1)
        cards.append(flex(n, 'padding: 26px; flex: 1 1 0; gap: 16px; align-items: center; border-radius: 22px; background-color: %s; box-shadow: 0 10px 14px rgba(0,26,64,0.08); @media(--tablet) { flex: 1 1 40%%; } @media(--mobile) { flex: 0 0 auto; padding: 22px; }' % ('#0160b4' if blue else '#ffffff'), [
            flex(n + ' Icon', 'width: 52px; min-width: 52px; height: 52px; padding: 0; border-radius: 26px; justify-content: center; align-items: center; background-color: %s;' % ('rgba(255,255,255,0.16)' if blue else '#f2f6fb'), [img(n + ' Glyph', ic, 'width: 23px;')]),
            flex(n + ' Text', 'padding: 0; flex: 1 1 0; flex-direction: column; gap: 2px;', [
                p(n + ' Number', num, FD + 'font-size: 32px; font-weight: 700; line-height: 1.25; color: %s; margin: 0;' % ('#ffffff' if blue else '#0160b4')),
                p(n + ' Label', t, FB + 'font-size: 13px; line-height: 20px; color: %s; margin: 0;' % ('rgba(255,255,255,0.85)' if blue else '#747474')),
            ]),
        ]).c('yt-lift'))
    return flex('Global Expansion', 'padding: 110px 120px; flex-direction: column; align-items: center; gap: 40px; background-color: #f2f6fb; @media(--tablet) { padding: 80px 40px; } @media(--mobile) { padding: 64px 20px; gap: 32px; }', [
        flex('Expansion Head', 'max-width: 820px; padding: 0; flex-direction: column; align-items: center; gap: 14px;', [
            eyebrow('Expansion Eyebrow', 'Worldwide', center=True),
            title('Expansion Title', 'Yutong&rsquo;s ' + hl('Global Expansion'), 'font-size: 42px; font-weight: 700; line-height: 52px; color: #00050b; text-align: center; @media(--tablet) { font-size: 36px; line-height: 44px; } @media(--mobile) { font-size: 30px; line-height: 38px; }'),
            body('Expansion Text 1', 'Yutong Bus is a globally renowned leader in the bus industry. Its product range caters to diverse market segments, including long distance coaches, tourist coaches, city buses, commuters, school buses, sightseeing vehicles, apron buses, autonomous driving micro-circulation buses and special-purpose vehicles.', 'max-width: 760px; text-align: center; line-height: 27px;', '#747474', 16),
            body('Expansion Text 2', 'In 2025, Yutong Bus achieved sales of 49,518 units. Yutong buses now operate in fleets across Europe, Latin America, Africa and Asia-Pacific, supported by a global network of branches, KD plants and authorized service outlets.', 'max-width: 760px; text-align: center; line-height: 27px; margin-top: 13px;', '#747474', 16),
        ]),
        flex('Expansion Map', 'padding: 0; height: 520px; justify-content: center; align-items: center; background-color: #ffffff; border-radius: 28px; overflow: hidden; @media(--tablet) { height: auto; } @media(--mobile) { border-radius: 20px; }', [
            img('Expansion Map Image', 'ytabout2-fleet', 'width: 100%; height: 520px; object-fit: cover; border-radius: 24px; @media(--tablet) { height: 400px; } @media(--mobile) { height: 240px; }'),
        ]),
        flex('Expansion Stats', 'padding: 0; gap: 20px; align-items: flex-start; @media(--tablet) { flex-wrap: wrap; align-items: stretch; } @media(--mobile) { flex-direction: column; }', cards),
    ], tag='section')


# ---------- Empower (5:125) ----------

def empower():
    return flex('Empower', '''min-height: 560px; padding: 120px 120px 38px; flex-direction: column; justify-content: space-between; align-items: center; gap: 40px;
        background-color: #072b52; background-image: linear-gradient(180deg, rgba(7,43,82,0.3) 0%%, rgba(7,43,82,0.9) 100%%), %s;
        background-size: cover; background-position: center center;
        @media(--tablet) { padding: 100px 40px 36px; min-height: 480px; } @media(--mobile) { padding: 72px 20px 28px; min-height: 0; }''' % bg('ytabout2-bridge'), [
        flex('Empower Content', 'max-width: 900px; padding: 60px 0 0; flex-direction: column; align-items: center; gap: 14px; @media(--mobile) { padding: 0; }', [
            eyebrow('Empower Eyebrow', 'Our Commitment', '#ffffff', center=True),
            h('Empower Title', "Empower customers' growth and success", 'h2', FD + 'font-size: 52px; font-weight: 700; line-height: 62px; color: #ffffff; text-align: center; margin: 0; @media(--tablet) { font-size: 42px; line-height: 52px; } @media(--mobile) { font-size: 30px; line-height: 38px; }'),
            p('Empower Text', 'Build trusted, lasting partnerships &middot; Deliver premium products and thoughtful service', FB + 'max-width: 640px; font-size: 17px; font-weight: 300; line-height: 28px; color: rgba(255,255,255,0.88); text-align: center; margin: 0; @media(--mobile) { font-size: 15px; line-height: 24px; }'),
        ]),
        flex('Empower Thumbs', 'padding: 0; justify-content: space-between; align-items: flex-end;', [
            img('Empower Thumb 1', 'ytabout2-meeting', 'width: 150px; height: 82px; object-fit: cover; border-radius: 14px; border: 3px solid #ffffff; @media(--mobile) { width: 120px; height: 66px; }'),
            img('Empower Thumb 2', 'ytabout2-event', 'width: 150px; height: 82px; object-fit: cover; border-radius: 14px; border: 3px solid #ffffff; @media(--mobile) { width: 120px; height: 66px; }'),
        ]),
    ], tag='section')


# ---------- Why Choose Us — Services (5:135) ----------

SERVICES = [
    ('Yutong Global Supply Chain Deployment', 'KD products from Yutong bus factories have been sold globally in over 15 countries. With efficient, professional, and comprehensive services, Yutong has earned the trust of customers across Asia, Latin America, Africa, and the Middle East.'),
    ('R&amp;D Investment Over 5%', 'Yutong invests over 5% of its annual revenue in R&amp;D, reflecting an unwavering commitment to innovation. This substantial investment keeps Yutong at the forefront of bus manufacturing and technological advancement.'),
    ('Three-Dimensional Product Mix Scheme', "Yutong bus segments feature a three-dimensional mix structure. This diversity meets customers' personalized and differentiated needs while furthering transportation and sustainability development."),
    ('Package Service Solution', 'Yutong offers tailored service packages and direct service to customers, and partners with third-party service providers and authorized outlets to extend coverage, giving customers greater choice and flexibility.'),
    ('IoV Solutions', "Yutong's Internet of Vehicles (IoV) solutions combine standard products with multiple solutions to support all-around intelligent vehicle management, enhancing operational efficiency and safety."),
    ('Fast and Efficient Network of Parts', "An advanced parts management platform ensures rapid part availability. Yutong's four-tier stocking system, backed by outstanding logistics partners, guarantees a sound supply of genuine parts to customers worldwide."),
]


def acc_item(i, t, text):
    n = 'Service %d' % (i + 1)
    circle = 'width: 32px; min-width: 32px; height: 32px; padding: 0; border-radius: 16px; justify-content: center; align-items: center;'
    row = flex(n + ' Row', 'padding: 0; gap: 14px; align-items: center;', [
        p(n + ' Num', '%02d' % (i + 1), FD + 'font-size: 14px; font-weight: 600; color: #0160b4; margin: 0;'),
        p(n + ' Title', t, FD + 'flex: 1 1 0; font-size: 17px; font-weight: 600; line-height: 1.3; color: #00050b; margin: 0; @media(--mobile) { font-size: 15px; }'),
        flex(n + ' Minus', circle + 'background-color: #0160b4;', [img(n + ' Minus Icon', 'about-ic-minus', 'width: 14px;')]).c('yt-about-acc-open'),
        flex(n + ' Chev', circle + 'background-color: #ffffff; border: 1px solid #e3e9f1;', [img(n + ' Chev Icon', 'about-ic-chev', 'width: 14px;')]).c('yt-about-acc-closed'),
    ])
    body_text = text
    return Node('e-accordion-item', n, {}, 'padding: 20px 24px; gap: 12px; background-color: #ffffff; border: 1px solid #e3e9f1; border-radius: 18px; @media(--mobile) { padding: 16px 18px; }', [
        Node('e-accordion-item-header', n + ' Header', {}, 'padding: 0; width: 100%;', [
            Node('e-accordion-item-title', n + ' Head', {}, 'padding: 0; width: 100%;', [row]),
        ]),
        Node('e-accordion-item-content', n + ' Panel', {}, 'padding: 12px 0 0 34px; @media(--mobile) { padding: 10px 0 0; }', [
            p(n + ' Text', body_text, FB + 'font-size: 14px; line-height: 23px; color: #747474; margin: 0;'),
        ]),
    ]).c('yt-about-acc')


def services():
    return flex('Our Strengths', 'padding: 110px 120px; gap: 64px; align-items: flex-start; background-color: #ffffff; @media(--tablet) { flex-direction: column; padding: 80px 40px; gap: 40px; } @media(--mobile) { padding: 64px 20px; }', [
        flex('Strengths Text', 'width: 420px; max-width: 100%; flex: 0 0 auto; padding: 0; flex-direction: column; gap: 14px; @media(--tablet) { width: 100%; }', [
            eyebrow('Strengths Eyebrow', 'Our Strengths'),
            title('Strengths Title', 'Why ' + hl('Choose Us'), 'font-size: 42px; font-weight: 700; line-height: 52px; color: #00050b; @media(--tablet) { font-size: 36px; line-height: 44px; } @media(--mobile) { font-size: 30px; line-height: 38px; }'),
            body('Strengths Intro', 'Behind every Yutong bus is more than 5% of annual revenue reinvested in R&amp;D, a global parts network and a 760-person direct service team. Here is what that means for operators in the UAE.', 'line-height: 27px; margin-bottom: 6px;', '#747474', 16),
            btn('Strengths Contact', 'Contact Us', URL['contact']),
        ]),
        Node('e-accordion', 'Strengths Accordion', {'show_icon': False, 'default_state': 'first_expanded', 'max_expanded': 'one'}, 'flex: 1 1 0; padding: 0; gap: 12px; @media(--tablet) { width: 100%; flex: 0 0 auto; }',
             [acc_item(i, t, d) for i, (t, d) in enumerate(SERVICES)]),
    ], tag='section')


# ---------- Our History (5:193) ----------

HISTORY_URL = 'https://en.yutong.com/about/history/'


def new_tab(node):
    """Open a linked node (e.g. a btn) in a new tab -- external en.yutong.com links."""
    node.cfg['link'] = link(node.cfg['link']['destination'], True)
    return node

MILESTONES = [  # en.yutong.com/about/history/
    ('1963', 'Zhengzhou Bus Repair Factory founded'),
    ('1997', "China's first listed bus company (Shanghai Stock Exchange)"),
    ('2005', 'Global market strategy; first Chinese bus builder to export CKD parts'),
    ('2025', 'Seven major awards at Busworld Europe 2025'),
]

def history():
    return flex('Our History', 'padding: 110px 120px; background-color: #f2f6fb; position: relative; @media(--tablet) { padding: 80px 40px; } @media(--mobile) { padding: 64px 20px; }', [
        anchor('our-history'),
        flex('History Card', 'padding: 0; align-items: center; background-color: #ffffff; border-radius: 28px; overflow: hidden; box-shadow: 0 10px 28px rgba(0,26,64,0.08); @media(--tablet) { flex-direction: column; align-items: stretch; } @media(--mobile) { border-radius: 22px; }', [
            img('History Photo', 'ytabout2-archive-crop', 'width: 560px; flex: 0 0 auto; height: auto; min-height: 420px; align-self: stretch; object-fit: cover; object-position: 50% 40%; @media(--tablet) { width: 100%; height: 360px; min-height: 0; } @media(--mobile) { height: 240px; }'),
            flex('History Text', 'padding: 52px; flex: 1 1 0; flex-direction: column; align-items: flex-start; gap: 18px; @media(--tablet) { padding: 40px; } @media(--mobile) { padding: 28px 22px; }', [
                flex('History Icon', 'width: 56px; min-width: 56px; height: 56px; padding: 0; border-radius: 16px; background-color: #0160b4; justify-content: center; align-items: center;', [img('History Glyph', 'about-ic-history', 'width: 26px;')]),
                h('History Title', 'Our History', 'h2', FD + 'font-size: 36px; font-weight: 700; line-height: 1.25; color: #00050b; margin: 0; @media(--mobile) { font-size: 30px; }'),
                body('History Body', 'Yutong Bus traces its origins to the Zhengzhou Bus Repair Factory founded in 1963. After decades of challenges, Yutong buses are now sold worldwide with over 10% market share.', 'line-height: 26px;', '#747474'),
                flex('History Years', 'padding: 0; flex-wrap: wrap; gap: 16px; @media(--mobile) { flex-direction: column; gap: 12px; }', [
                    flex('History Year %d' % (i + 1), 'padding: 0 0 0 14px; flex: 1 1 40%; flex-direction: column; gap: 2px; border-width: 0 0 0 2px; border-style: solid; border-color: #d6e4f3; @media(--mobile) { flex: 0 0 auto; }', [
                        p('History Year %d Number' % (i + 1), y, FD + 'font-size: 24px; font-weight: 700; line-height: 1.25; color: #0160b4; margin: 0;'),
                        p('History Year %d Label' % (i + 1), t, FB + 'font-size: 13px; line-height: 20px; color: #747474; margin: 0;'),
                    ]) for i, (y, t) in enumerate(MILESTONES)]),
                new_tab(btn('History CTA', 'View Our History', HISTORY_URL, kind='outline-blue')),
            ]),
        ]),
    ], tag='section')


# ---------- Honors & Awards (5:210) ----------

HONORS = ['100 Most Valuable Automobile Brands in the World', 'Busworld Awards (Design Labels and Ecology Label)', 'Australian ADR certification',
          'BAAV Coach Builder of the Year, Innovative Award-Coach, Safety Award-Coach, Environmental Award-Coach, BRT Award and Coach Award', 'Fortune 500']


def honors():
    logos = [flex('Honor Logo %d' % (i + 1), 'padding: 12px; flex: 1 1 0; height: 76px; justify-content: center; align-items: center; background-color: #ffffff; border-radius: 14px; @media(--mobile) { height: 64px; padding: 8px; }', [
        img('Honor Logo %d Image' % (i + 1), 'about-logo-%d' % (i + 1), 'width: 80px; height: 50px; object-fit: contain;')]) for i in range(4)]
    items = [flex('Honor %d' % (i + 1), 'padding: 12px 16px; gap: 12px; align-items: center; background-color: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.1); border-radius: 14px;', [
        flex('Honor %d Icon' % (i + 1), 'width: 28px; min-width: 28px; height: 28px; padding: 0; border-radius: 14px; background-color: #0160b4; justify-content: center; align-items: center;', [img('Honor %d Glyph' % (i + 1), 'about-ic-award', 'width: 12px;')]),
        p('Honor %d Text' % (i + 1), t, FB + 'flex: 1 1 0; font-size: 14px; line-height: 21px; color: #ffffff; margin: 0;'),
    ]) for i, t in enumerate(HONORS)]
    return flex('Honors and Awards', 'padding: 110px 120px; gap: 64px; align-items: center; background-color: #072b52; @media(--tablet) { flex-direction: column; align-items: stretch; padding: 80px 40px; gap: 48px; } @media(--mobile) { padding: 64px 20px; }', [
        flex('Honors Visual', 'width: 480px; max-width: 100%; flex: 0 0 auto; padding: 0; flex-direction: column; gap: 16px; @media(--tablet) { align-self: center; }', [
            img('Honors Photo', 'ytabout-honors-trophies', 'width: 100%; height: 420px; object-fit: cover; border-radius: 28px; @media(--mobile) { height: 300px; border-radius: 22px; }'),
            flex('Honor Logos', 'padding: 0; gap: 12px; @media(--mobile) { gap: 8px; }', logos),
        ]),
        flex('Honors Text', 'padding: 0; flex: 1 1 0; flex-direction: column; align-items: flex-start; gap: 20px;', [
            eyebrow('Honors Eyebrow', 'Recognition', '#ffffff'),
            h('Honors Title', 'Over ' + hl('500 Honors') + ' and International Awards', 'h2', FD + 'max-width: 600px; font-size: 42px; font-weight: 700; line-height: 52px; color: #ffffff; margin: 0; @media(--tablet) { font-size: 36px; line-height: 44px; } @media(--mobile) { font-size: 30px; line-height: 38px; }').c('yt-hl-sky'),
            body('Honors Intro', 'Yutong always insists on creating greater value for customers. With the outstanding market performance, Yutong has been granted a number of honors and awards.', 'line-height: 26px;', 'rgba(255,255,255,0.8)'),
            flex('Honors List', 'padding: 0; flex-direction: column; gap: 10px;', items),
            btn('Honors All', 'View All Honors', URL['awards'], kind='white'),
        ]),
    ], tag='section')


SECTIONS = [
    ('about-1', lambda: hero() + [why()]),
    ('about-2', lambda: [expansion(), empower()]),
    ('about-3', lambda: [services(), history()]),
    ('about-4', lambda: [honors(), cta_section('Find the right Yutong ' + hl('coach, bus or e&#8209;bus') + ' for your fleet with the official Yutong distributor in the UAE.', 'about-cta', pad='110px 120px')]),
    ('about-5', lambda: [news_section(), quote_section(), footer()]),
]
