"""Home page (Figma frame 1:2213 'YUTONG — Redesign v1') -> WP page 8."""
import sys, os
sys.path.insert(0, os.path.join(os.path.dirname(__file__), '..', 'tools'))
from parts import *

POST = 8
SLUG = 'home'


def hero():
    stats = [('90+', 'Countries with Yutong fleets'), ('65,000', 'Units annual production capacity'),
             ('10+%', 'Global bus market share'), ('7 Days', 'Showroom open every week')]
    cells = []
    for i, (n, t) in enumerate(stats):
        if i:
            cells.append(div('Hero Stat Divider %d' % i, 'width: 1px; min-width: 1px; height: 44px; padding: 0; background-color: rgba(255,255,255,0.25); @media(--mobile) { display: none; }'))
        cells.append(flex('Hero Stat %d' % (i + 1), 'width: auto; padding: 0; flex-direction: column; gap: 2px; @media(--mobile) { width: 45%; }', [
            p('Hero Stat %d Number' % (i + 1), n, FD + 'font-size: 30px; font-weight: 600; color: #ffffff; margin: 0; line-height: 1.25; @media(--mobile) { font-size: 24px; }'),
            p('Hero Stat %d Label' % (i + 1), t, FB + 'font-size: 13px; color: rgba(255,255,255,0.75); margin: 0;'),
        ]))
    return flex('Hero', '''min-height: 880px; padding: 220px 120px 60px; flex-direction: column; justify-content: space-between; gap: 60px;
        background-color: #072b52;
        background-image: linear-gradient(180deg, rgba(0,5,11,0) 55%, rgba(0,5,11,0.75) 100%), linear-gradient(90deg, rgba(7,43,82,0.95) 0%, rgba(7,43,82,0.7) 42%, rgba(7,43,82,0) 72%), ''' + bg('hero-home-bg') + ''';
        background-size: cover; background-position: center center;
        @media(--tablet) { padding: 170px 40px 50px; min-height: 760px; }
        @media(--mobile) { padding: 130px 20px 40px; min-height: 0; background-position: 70% 50%; }''', [
        flex('Hero Content', 'max-width: 720px; padding: 0; flex-direction: column; align-items: flex-start; gap: 26px;', [
            flex('Hero Badge', 'width: auto; padding: 8px 16px; gap: 8px; align-items: center; border-radius: 30px; background-color: rgba(255,255,255,0.12); border: 1px solid rgba(255,255,255,0.3);', [
                div('Hero Badge Dot', 'width: 8px; min-width: 8px; height: 8px; padding: 0; border-radius: 50%; background-color: #2c84cb;'),
                p('Hero Badge Text', 'Official Yutong Distributor in the UAE', FB + 'font-size: 13px; font-weight: 500; color: #ffffff; margin: 0;'),
            ]),
            h('Hero Title', 'Driving the Future of ' + hl('Mobility', '#2c84cb') + ' in the UAE', 'h1',
              FD + 'font-size: 62px; font-weight: 700; line-height: 72px; letter-spacing: -0.62px; color: #ffffff; margin: 0; @media(--tablet) { font-size: 50px; line-height: 60px; } @media(--mobile) { font-size: 36px; line-height: 44px; }').c('yt-hl-sky'),
            p('Hero Text', 'Electric city buses, premium intercity coaches and complete fleet solutions — sold, serviced and supported locally by Al Khoory Automobiles.',
              FB + 'max-width: 560px; font-size: 18px; font-weight: 300; line-height: 30px; color: rgba(255,255,255,0.88); margin: 0; @media(--mobile) { font-size: 16px; line-height: 26px; }'),
            flex('Hero Buttons', 'width: auto; padding: 0; gap: 14px; flex-wrap: wrap;', [
                btn('Hero Explore', 'Explore Models', URL['models']),
                btn('Hero Quote', 'Request a Quote', URL['quote'], kind='outline', arrow=False),
            ]),
        ]),
        flex('Hero Stats', 'max-width: 1200px; padding: 26px 40px; justify-content: space-between; align-items: center; border-radius: 20px; background-color: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); backdrop-filter: blur(12px); @media(--mobile) { flex-wrap: wrap; gap: 20px; padding: 22px; }', cells),
    ])


MODEL_TABS = ['E-bus', 'E-Coach', 'City Bus', 'Coach', 'Airport bus']


def spec(cid, label, value, unit=''):
    row = [p(cid + ' Value', value, FD + 'font-size: 30px; font-weight: 600; color: #003376; margin: 0; line-height: 1.2;')]
    if unit:
        row.append(p(cid + ' Unit', unit, FB + 'font-size: 13px; font-weight: 500; color: #0160b4; margin: 0;'))
    return flex(cid, 'padding: 16px 20px; flex: 1 1 0; flex-direction: column; gap: 4px; background-color: #ffffff; border-radius: 16px;', [
        p(cid + ' Label', label, FB + 'font-size: 12px; color: #747474; margin: 0;'),
        flex(cid + ' Row', 'width: auto; padding: 0; gap: 6px; align-items: flex-end;', row),
    ])


def featured_ic12e():
    return flex('Featured IC12E', 'padding: 48px 56px; gap: 40px; align-items: center; background-color: #f2f6fb; border-radius: 28px; overflow: hidden; @media(--tablet) { flex-direction: column; padding: 40px 32px; } @media(--mobile) { padding: 28px 20px; gap: 24px; }', [
        div('IC12E Visual', 'width: 620px; max-width: 100%; flex: 0 0 auto; height: 340px; padding: 0; position: relative; @media(--mobile) { height: 200px; }', [
            p('IC12E Ghost', 'IC12E', FD + 'position: absolute; left: 10px; top: 20px; font-size: 190px; font-weight: 700; line-height: 1.2; color: rgba(1,96,180,0.07); margin: 0; @media(--mobile) { font-size: 100px; }'),
            img('IC12E Shadow', 'ground-shadow', 'position: absolute; left: 36px; top: 268px; width: 548px; max-width: 90%; @media(--mobile) { top: 150px; }'),
            img('IC12E Photo', 'ic12e', 'position: absolute; left: 0; top: 92px; width: 620px; height: 202px; object-fit: contain; @media(--mobile) { top: 50px; height: 120px; }'),
        ]),
        flex('IC12E Info', 'padding: 0; flex: 1 1 0; flex-direction: column; gap: 18px;', [
            img('IC12E Wordmark', 'wordmark', 'width: 94px; height: 14px; object-fit: contain;'),
            h('IC12E Name', 'IC12E', 'h3', FD + 'font-size: 56px; font-weight: 700; line-height: 60px; color: #0160b4; margin: 0;'),
            body('IC12E Text', 'A premium battery-electric intercity coach built for long-range comfort. The IC12E pairs a high-capacity battery with a spacious 47-seat cabin.', 'line-height: 25px;', '#747474'),
            flex('IC12E Specs', 'padding: 0; flex-direction: column; gap: 12px;', [
                flex('IC12E Specs Row 1', 'padding: 0; gap: 12px;', [spec('Spec Length', 'Length', '12', 'M'), spec('Spec Battery', 'Battery capacity', '465', 'kWh')]),
                flex('IC12E Specs Row 2', 'padding: 0; gap: 12px;', [spec('Spec Seats', 'Seats', '47'), spec('Spec Range', 'Range', '610', 'KM')]),
            ]),
            flex('IC12E Buttons', 'width: auto; padding: 6px 0 0; gap: 12px; flex-wrap: wrap;', [
                btn('IC12E Explore', 'Explore Model', URL['models']),
                btn('IC12E Quote', 'Request a Quote', URL['quote'], kind='outline-blue', arrow=False),
            ]),
        ]),
    ])


def coming_soon(cat):
    return flex(cat + ' Panel', 'padding: 64px 56px; flex-direction: column; align-items: center; gap: 18px; background-color: #f2f6fb; border-radius: 28px; @media(--mobile) { padding: 40px 20px; }', [
        img(cat + ' Wordmark', 'wordmark', 'width: 94px; height: 14px; object-fit: contain;'),
        h(cat + ' Title', cat, 'h3', FD + 'font-size: 44px; font-weight: 700; color: #0160b4; margin: 0; text-align: center;'),
        body(cat + ' Text', 'Our full %s line-up is being added to the site. Contact our team for availability, specifications and fleet pricing in the UAE.' % cat.lower(), 'max-width: 560px; text-align: center;'),
        flex(cat + ' Buttons', 'width: auto; padding: 6px 0 0; gap: 12px; flex-wrap: wrap; justify-content: center;', [
            btn(cat + ' View', 'View all models', URL['models']),
            btn(cat + ' Quote', 'Request a Quote', URL['quote'], kind='outline-blue', arrow=False),
        ]),
    ])


def models():
    tabs = [Node('e-tab', 'Tab ' + t, {}, FB + 'width: auto; padding: 11px 26px; border-width: 0; border-radius: 34px; background-color: transparent; font-size: 14px; color: #323232; cursor: pointer; @media(--mobile) { padding: 9px 16px; }',
                 [p('Tab %s Label' % t, t, FB + 'font-size: 14px; margin: 0;')]).c('yt-tab') for t in MODEL_TABS]
    panels = [Node('e-tab-content', 'Panel ' + t, {}, 'padding: 0;', [featured_ic12e() if i == 0 else coming_soon(t)]) for i, t in enumerate(MODEL_TABS)]
    return flex('Models', 'padding: 110px 120px; flex-direction: column; align-items: center; gap: 44px; background-color: #ffffff; @media(--tablet) { padding: 80px 40px; } @media(--mobile) { padding: 64px 20px; gap: 32px; }', [
        flex('Models Head', 'padding: 0; flex-direction: column; align-items: center; gap: 14px;', [
            eyebrow('Models Eyebrow', 'Our Range', center=True),
            title('Models Title', 'Explore All ' + hl('YUTONG') + ' Models', 'font-weight: 700; color: #00050b; text-align: center;'),
            body('Models Intro', 'From electric city buses to premium intercity coaches, discover the full Yutong range — engineered for performance, safety, and sustainability across every route.', 'max-width: 700px; text-align: center; line-height: 27px;', '#747474', 16),
        ]),
        Node('e-tabs', 'Model Tabs', {}, 'padding: 0; gap: 44px; align-items: center; @media(--mobile) { gap: 28px; }', [
            Node('e-tabs-menu', 'Model Tabs Menu', {}, 'width: auto; max-width: 100%; padding: 6px; gap: 4px; display: flex; flex-wrap: wrap; justify-content: center; background-color: #f2f6fb; border-radius: 40px; @media(--mobile) { border-radius: 24px; }', tabs),
            Node('e-tabs-content-area', 'Model Tabs Content', {}, 'width: 100%; padding: 0;', panels),
        ]),
        flex('Models Footer', 'padding: 0; justify-content: flex-end;', [
            flex('Models View All', 'width: auto; padding: 0; gap: 8px; align-items: center;', [
                p('Models View All Text', 'View all models', FB + 'font-size: 15px; font-weight: 500; color: #0160b4; margin: 0;'),
                img('Models View All Arrow', 'arrow-b18', 'width: 18px;'),
            ], href=URL['models']),
        ]),
    ], tag='section')


def why():
    checks = ['Local after-sales support & service network', 'Genuine spare parts availability', 'Fleet solutions for transit, tourism, corporate & schools']
    return flex('Why Choose Us', 'padding: 40px 120px 110px; gap: 72px; align-items: center; background-color: #ffffff; @media(--tablet) { flex-direction: column; padding: 40px 40px 80px; gap: 48px; } @media(--mobile) { padding: 24px 20px 64px; }', [
        div('Why Visual', 'width: 560px; max-width: 100%; flex: 0 0 auto; height: 640px; padding: 0; position: relative; @media(--mobile) { height: 520px; }', [
            img('Why Bus Photo', 'why-bus', 'position: absolute; left: 0; top: 0; width: 440px; max-width: 80%; height: 640px; object-fit: cover; border-radius: 28px; @media(--mobile) { height: 520px; }'),
            flex('Why Badge', 'position: absolute; left: 340px; top: 48px; width: auto; padding: 18px 24px; flex-direction: column; background-color: #0160b4; border-radius: 20px; box-shadow: 0 20px 24px rgba(0,26,64,0.16); @media(--mobile) { left: auto; right: 0; }', [
                p('Why Badge Number', '90+', FD + 'font-size: 34px; font-weight: 700; color: #ffffff; margin: 0; line-height: 1.25;'),
                p('Why Badge Label', 'Countries worldwide', FB + 'font-size: 12px; color: rgba(255,255,255,0.85); margin: 0;'),
            ]),
            flex('Global Presence Card', 'position: absolute; left: 260px; top: 330px; width: 300px; padding: 16px; flex-direction: column; gap: 12px; background-color: #ffffff; border-radius: 22px; box-shadow: 0 20px 24px rgba(0,26,64,0.16); @media(--mobile) { left: auto; right: 0; top: 250px; width: 260px; }', [
                img('Global Presence Photo', 'why-global', 'width: 100%; height: 120px; object-fit: cover; border-radius: 14px;'),
                flex('Global Presence Head', 'padding: 4px 4px 0; gap: 8px; align-items: center;', [
                    img('Global Presence Icon', 'ic-globe', 'width: 18px;'),
                    p('Global Presence Title', 'Global Presence', FD + 'font-size: 17px; font-weight: 600; color: #00050b; margin: 0;'),
                ]),
                flex('Global Presence Foot', 'padding: 0 4px 4px; gap: 12px; align-items: flex-end;', [
                    p('Global Presence Text', 'From the streets of Europe to the roads of the Middle East — a proven track record you can rely on.', FB + 'flex: 1 1 0; font-size: 12px; line-height: 19px; color: #747474; margin: 0;'),
                    flex('Global Presence Go', 'width: 36px; min-width: 36px; height: 36px; padding: 0; border-radius: 18px; background-color: #0160b4; justify-content: center; align-items: center;', [img('Global Presence Arrow', 'arrow-w15', 'width: 15px;')], href=URL['about']),
                ]),
            ]),
        ]),
        flex('Why Text', 'padding: 0; flex: 1 1 0; flex-direction: column; gap: 20px;', [
            eyebrow('Why Eyebrow', 'Why Choose Us'),
            title('Why Title', 'Trusted Worldwide. Engineered for ' + hl('Excellence.'), 'font-size: 42px; font-weight: 700; line-height: 52px; color: #00050b;'),
            body('Why Text 1', "As the world's largest bus manufacturer, Yutong combines decades of engineering expertise with a commitment to safety, comfort, and sustainable mobility. Every vehicle is built on proven technology and rigorous quality standards — trusted by operators across more than 90 countries.", 'line-height: 26px;', '#747474'),
            body('Why Text 2', 'In the UAE, Yutong delivers tailored fleet solutions for public transit, tourism, corporate, and school transport — backed by local after-sales support, genuine parts availability, and a service network built around long-term reliability.', 'line-height: 26px;', '#747474'),
            flex('Why Checks', 'padding: 4px 0 8px; flex-direction: column; gap: 12px;', [
                flex('Why Check %d' % (i + 1), 'padding: 0; gap: 12px; align-items: center;', [
                    flex('Why Check %d Badge' % (i + 1), 'width: 26px; min-width: 26px; height: 26px; padding: 0; border-radius: 13px; background-color: #e3effa; justify-content: center; align-items: center;', [img('Why Check %d Icon' % (i + 1), 'ic-check', 'width: 11px;')]),
                    p('Why Check %d Text' % (i + 1), t, FB + 'font-size: 15px; font-weight: 500; color: #00050b; margin: 0;'),
                ]) for i, t in enumerate(checks)]),
            btn('Why Discover', 'Discover More', URL['about']),
        ]),
    ], tag='section')


def new_energy():
    return flex('Integrated Solutions', 'padding: 0 120px 110px; background-color: #ffffff; @media(--tablet) { padding: 0 40px 80px; } @media(--mobile) { padding: 0 16px 64px; }', [
        flex('New Energy Card', '''padding: 56px 56px 56px 64px; gap: 56px; align-items: center; border-radius: 32px; overflow: hidden; position: relative;
            background-image: radial-gradient(circle at 5% 0%, rgba(255,255,255,0.12) 0%, rgba(255,255,255,0) 30%), radial-gradient(circle at 46% 100%, rgba(255,255,255,0.08) 0%, rgba(255,255,255,0) 22%), linear-gradient(90deg, #0160b4 0%, #003376 100%);
            @media(--tablet) { flex-direction: column; align-items: stretch; padding: 44px 32px; gap: 36px; } @media(--mobile) { padding: 32px 20px; border-radius: 24px; }''', [
            flex('New Energy Text', 'padding: 0; flex: 1 1 0; flex-direction: column; gap: 20px; align-items: flex-start;', [
                eyebrow('New Energy Eyebrow', 'New Energy', '#ffffff'),
                h('New Energy Title', 'Integrated New Energy Vehicle Solutions', 'h2', FD + 'font-size: 40px; font-weight: 700; line-height: 50px; color: #ffffff; margin: 0; @media(--mobile) { font-size: 30px; line-height: 38px; }'),
                body('New Energy Body', 'Based on long-term exploration of the bus and coach manufacturing field, Yutong has been continuously innovating products and providing customers with diversified public transportation solutions, and service package solutions in different segments to meet their personalized and differentiated needs.', 'line-height: 26px;', 'rgba(255,255,255,0.82)'),
                btn('New Energy CTA', 'View Our Solution', URL['technologies'], kind='white'),
            ]),
            img('New Energy Photo', 'ne-bus', 'width: 540px; max-width: 100%; height: 360px; object-fit: cover; border-radius: 22px; @media(--tablet) { width: 100%; } @media(--mobile) { height: 240px; }'),
        ]),
    ], tag='section')


def global_leader():
    cards = [('210,000+', 'New Energy Buses Sold', 'Over two decades of investment in electric mobility.', True),
             ('18,356+', 'Vehicles Delivered Worldwide', 'A growing global fleet trusted by public transit operators.', False),
             ('10+%', 'Global Market Share', 'Leading the international bus and coach industry.', False)]
    kids = []
    for i, (n, t, d, blue) in enumerate(cards):
        kids.append(flex('Leader Card %d' % (i + 1), 'padding: 36px; flex: 1 1 0; @media(--mobile) { flex: 0 0 auto; } flex-direction: column; gap: 10px; border-radius: 24px; background-color: %s; box-shadow: %s; @media(--mobile) { padding: 28px; }' % ('#0160b4' if blue else '#ffffff', '0 20px 24px rgba(0,26,64,0.16)' if blue else '0 10px 14px rgba(0,26,64,0.08)'), [
            p('Leader Card %d Number' % (i + 1), n, FD + 'font-size: 50px; font-weight: 700; line-height: 58px; color: %s; margin: 0; @media(--mobile) { font-size: 40px; line-height: 48px; }' % ('#ffffff' if blue else '#0160b4')),
            div('Leader Card %d Rule' % (i + 1), 'width: 40px; min-width: 40px; height: 3px; padding: 0; border-radius: 2px; background-color: %s;' % ('rgba(255,255,255,0.6)' if blue else '#0160b4')),
            p('Leader Card %d Title' % (i + 1), t, FD + 'font-size: 18px; font-weight: 600; color: %s; margin: 0;' % ('#ffffff' if blue else '#00050b')),
            p('Leader Card %d Text' % (i + 1), d, FB + 'font-size: 14px; line-height: 22px; color: %s; margin: 0;' % ('rgba(255,255,255,0.8)' if blue else '#747474')),
        ]).c('yt-lift'))
    return flex('Global Leader', 'padding: 110px 120px; flex-direction: column; gap: 56px; background-color: #f2f6fb; @media(--tablet) { padding: 80px 40px; } @media(--mobile) { padding: 64px 20px; gap: 36px; }', [
        flex('Leader Head', 'padding: 0; gap: 80px; align-items: flex-end; @media(--tablet) { flex-direction: column; align-items: flex-start; gap: 20px; }', [
            flex('Leader Head Text', 'width: 560px; max-width: 100%; flex: 0 0 auto; padding: 0; flex-direction: column; gap: 16px;', [
                eyebrow('Leader Eyebrow', 'Discover More About Yutong'),
                title('Leader Title', 'A ' + hl('Global Leader') + ' in Sustainable Bus Manufacturing', 'font-size: 42px; font-weight: 700; line-height: 52px; color: #00050b;'),
            ]),
            body('Leader Intro', "With decades of engineering excellence, Yutong has become one of the world's leading bus and coach manufacturers — delivering safe, reliable, and sustainable transport solutions to customers across more than 100 countries.", 'flex: 1 1 0; line-height: 28px;', '#747474', 16),
        ]),
        flex('Leader Cards', 'padding: 0; gap: 24px; @media(--mobile) { flex-direction: column; }', kids),
    ], tag='section')


def supplier():
    stats = [('4', 'Vehicle manufacturing bases'), ('5', 'Core component bases'), ('65K', 'Units annual capacity')]
    photos = [('team-1', '0'), ('team-2', '48px'), ('team-3', '16px')]
    return flex('Major Supplier', '''padding: 110px 120px; gap: 64px; align-items: center;
        background-color: #072b52; background-image: linear-gradient(90deg, rgba(7,43,82,0.96) 0%, rgba(7,43,82,0.85) 55%, rgba(0,51,118,0.6) 100%), ''' + bg('supplier-bg') + ''';
        background-size: cover; background-position: center center;
        @media(--tablet) { flex-direction: column; padding: 80px 40px; align-items: flex-start; } @media(--mobile) { padding: 64px 20px; gap: 40px; }''', [
        flex('Supplier Text', 'padding: 0; flex: 1 1 0; flex-direction: column; gap: 22px;', [
            eyebrow('Supplier Eyebrow', 'Yutong Group', '#ffffff'),
            h('Supplier Title', "Yutong, World's Major Bus and " + hl('Coach Supplier'), 'h2', FD + 'font-size: 40px; font-weight: 700; line-height: 50px; color: #ffffff; margin: 0; @media(--mobile) { font-size: 30px; line-height: 38px; }').c('yt-hl-sky'),
            body('Supplier Body', 'Yutong Group is a world-leading, highly vertically integrated transportation industry ecosystem. The company has four modern vehicle manufacturing bases and five core component production bases, with an annual capacity of 65,000 units. With its independently controllable full-chain manufacturing strength, Yutong continues to lead the development of the global commercial vehicle industry.', 'line-height: 26px;', 'rgba(255,255,255,0.82)'),
            flex('Supplier Stats', 'width: auto; padding: 6px 0 0; gap: 14px; @media(--mobile) { flex-wrap: wrap; }', [
                flex('Supplier Stat %d' % (i + 1), 'width: auto; padding: 16px 20px; flex-direction: column; gap: 2px; border-radius: 16px; background-color: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.18); backdrop-filter: blur(8px);', [
                    p('Supplier Stat %d Number' % (i + 1), n, FD + 'font-size: 28px; font-weight: 600; color: #ffffff; margin: 0; line-height: 1.25;'),
                    p('Supplier Stat %d Label' % (i + 1), t, FB + 'font-size: 12px; color: rgba(255,255,255,0.75); margin: 0;').c('yt-nowrap'),
                ]) for i, (n, t) in enumerate(stats)]),
        ]),
        flex('Supplier Photos', 'width: auto; flex: 0 0 auto; padding: 0; gap: 16px; align-items: flex-start; @media(--mobile) { gap: 10px; width: 100%; }', [
            img('Supplier Photo %d' % (i + 1), ph, 'width: 170px; height: 250px; object-fit: cover; border-radius: 20px; border: 3px solid rgba(255,255,255,0.25); margin-top: %s; @media(--mobile) { width: 31%%; height: 170px; }' % off) for i, (ph, off) in enumerate(photos)]),
    ], tag='section')


def provide():
    def text_card(n, t, d):
        return flex('Provide Card ' + n, 'width: 470px; max-width: 100%; flex: 0 0 auto; padding: 44px; flex-direction: column; gap: 16px; align-items: flex-start; background-color: #f2f6fb; border-radius: 28px; @media(--tablet) { width: 100%; } @media(--mobile) { padding: 28px; }', [
            p('Provide Card %s Number' % n, n, FD + 'font-size: 48px; font-weight: 700; line-height: 52px; color: rgba(1,96,180,0.2); margin: 0;'),
            h('Provide Card %s Title' % n, t, 'h3', FD + 'font-size: 28px; font-weight: 600; line-height: 36px; color: #00050b; margin: 0;'),
            body('Provide Card %s Text' % n, d, 'line-height: 26px;', '#747474'),
            btn('Provide Card %s CTA' % n, 'Explore Model', URL['models']),
        ])

    def photos(n, a, b):
        return flex('Provide Photos ' + n, 'padding: 0; flex: 1 1 0; gap: 16px; @media(--tablet) { width: 100%; }', [
            img('Provide Photo %s A' % n, a, 'flex: 1 1 0; width: 100%; height: 360px; object-fit: cover; border-radius: 24px; @media(--mobile) { height: 220px; }').c('yt-zoom'),
            img('Provide Photo %s B' % n, b, 'width: 230px; flex: 0 0 auto; height: 360px; object-fit: cover; border-radius: 24px; @media(--mobile) { width: 40%; height: 220px; }'),
        ])
    return flex('What We Provide', 'padding: 110px 120px; flex-direction: column; align-items: center; gap: 48px; background-color: #ffffff; @media(--tablet) { padding: 80px 40px; } @media(--mobile) { padding: 64px 20px; gap: 32px; }', [
        flex('Provide Head', 'padding: 0; flex-direction: column; align-items: center; gap: 14px;', [
            eyebrow('Provide Eyebrow', 'Our Commitment', center=True),
            title('Provide Title', 'What We Provide the ' + hl('UAE'), 'font-weight: 700; color: #00050b; text-align: center;'),
            body('Provide Intro', 'Yutong has always been committed to winning customer trust with excellent product performance and reliable operation in UAE, and making positive contributions to local transportation development by providing efficient and economical solutions.', 'max-width: 760px; text-align: center; line-height: 27px;', '#747474', 16),
        ]),
        flex('Provide Row 1', 'padding: 0; gap: 24px; align-items: center; @media(--tablet) { flex-direction: column; }', [
            text_card('01', 'Reliability and Durability', 'Built to perform in every condition, Yutong buses combine rugged engineering with rigorous testing standards — delivering dependable service across long distances, demanding climates, and high-mileage operation year after year.'),
            photos('01', 'rel-1', 'rel-2'),
        ]),
        flex('Provide Row 2', 'padding: 0; gap: 24px; align-items: center; @media(--tablet) { flex-direction: column-reverse; }', [
            photos('02', 'qual-1', 'qual-2'),
            text_card('02', 'Exceptional Quality', 'From premium interior finishes to precision-engineered components, every Yutong vehicle reflects a commitment to craftsmanship — creating a comfortable, refined experience for both drivers and passengers.'),
        ]),
    ], tag='section')


SECTIONS = [
    ('home-1', lambda: [header('home'), hero()]),
    ('home-2', lambda: [models()]),
    ('home-3', lambda: [why(), new_energy()]),
    ('home-4', lambda: [global_leader(), supplier()]),
    ('home-5', lambda: [provide()]),
    ('home-6', lambda: [news_section(), quote_section(), footer()]),
]

if __name__ == '__main__':
    out = os.path.join(WORK, 'payloads')
    os.makedirs(out, exist_ok=True)
    for i, (name, fn) in enumerate(SECTIONS):
        save(os.path.join(out, name + '.json'), payload(POST, fn(), parent='document', mode='replace_children' if i == 0 else None))
    print(' '.join(n for n, _ in SECTIONS))
