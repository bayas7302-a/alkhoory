import sys
from dsl import *
import home
from home import SEC, HEAD_ROW, DARK_BG

ABOUT, SERVICES, PROJECTS, CONTACT = 63, 64, 65, 66


def page_hero(pre, crumb, title, intro, image):
    card = box(pre + ' card', 'flex-direction: column; justify-content: center; position: relative; overflow: hidden; min-height: 476px; border-radius: 32px; '
               'background-image: url(%s); background-size: cover; background-position: center; background-color: #0e0b12; @media(--mobile) { min-height: 380px; border-radius: 22px; }' % MEDIA[image][1], [
        box(pre + ' wash', 'position: absolute; top: 0; left: 0; width: 100%%; height: 100%%; background: %s; mix-blend-mode: color; opacity: 0.5;' % G),
        box(pre + ' shade', 'position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: linear-gradient(90deg, rgba(14, 11, 18, 0.96) 0%, rgba(14, 11, 18, 0.8) 55%, rgba(14, 11, 18, 0.3) 100%);'),
        box(pre + ' slash', 'position: absolute; top: -30%; left: 66%; width: 120px; height: 160%; transform: rotate(24deg); background: linear-gradient(180deg, rgba(232, 56, 138, 0.85) 0%, rgba(192, 19, 138, 0.35) 100%); opacity: 0.75; @media(--mobile) { left: 82%; width: 60px; }'),
        box(pre + ' content', 'flex-direction: column; align-items: flex-start; position: relative; padding: 60px 72px; max-width: 760px; @media(--tablet) { padding: 56px 48px; } @media(--mobile) { padding: 96px 22px 44px; }', [
            box(pre + ' crumbs', 'flex-direction: row; align-items: center; gap: 12px; width: auto; margin-bottom: 24px;', [
                p(pre + ' crumb home', 'Home', 'font-family: var(--ab-font-body); font-weight: 500; font-size: 13px; color: rgba(255, 255, 255, 0.6); &:hover { color: #ffffff; }', link='/'),
                p(pre + ' crumb sep', '/', 'font-family: var(--ab-font-body); font-size: 13px; color: rgba(255, 255, 255, 0.4);'),
                p(pre + ' crumb current', crumb, 'font-family: var(--ab-font-body); font-weight: 500; font-size: 13px; color: #ff5aa8;'),
            ]),
            h(pre + ' title', title + '<s>.</s>', 'font-size: 90px; @media(--tablet) { font-size: 76px; } @media(--mobile) { font-size: 56px; }', tag='h1', dark=True),
            p(pre + ' intro', intro, 'font-family: var(--ab-font-body); font-weight: 300; font-size: 18px; line-height: 1.6; color: rgba(255, 255, 255, 0.82); max-width: 560px; margin-top: 24px; @media(--mobile) { font-size: 16px; }'),
        ], ['abp-hero-in']),
    ])
    return box(pre, 'flex-direction: column; padding: 0 24px; @media(--mobile) { padding: 0 12px; }', [card], tag='section')


def head(pre, num, lbl, title, intro, dark=False, size='72px'):
    return box(pre + ' head', HEAD_ROW, [
        box(pre + ' head left', 'flex-direction: column; gap: 28px; width: auto;', [
            label(pre, num, lbl, dark=dark),
            h(pre + ' title', title, 'font-size: %s; @media(--tablet) { font-size: 58px; } @media(--mobile) { font-size: 44px; }' % size, dark=dark),
        ]),
        p(pre + ' intro', intro, ('color: rgba(255, 255, 255, 0.7); ' if dark else '') + 'width: 400px; @media(--mobile) { width: 100%; }', ['abp-body']),
    ])


# ---------------------------------------------------------------- About

def who():
    return section('Who', SEC + ' background: #ffffff;', [
        box('Who top', 'flex-direction: row; align-items: flex-start; gap: 60px; @media(--tablet) { flex-direction: column; gap: 28px; }', [
            label('Who', '01', 'Who We Are', style='flex-shrink: 0; margin-top: 18px; width: 189px;'),
            h('Who statement', '<em>AB</em>PRODUCTION IS A MULTIDISCIPLINARY PRODUCTION AND SOLUTIONS COMPANY <u>BUILT AROUND BRINGING IDEAS TO LIFE, SEAMLESSLY.</u>', 'font-size: 60px; font-weight: 800; line-height: 1.1; letter-spacing: -0.6px; @media(--tablet) { font-size: 48px; } @media(--mobile) { font-size: 34px; }'),
        ]),
        box('Who feature', 'flex-direction: column; justify-content: flex-end; position: relative; overflow: hidden; min-height: 520px; margin-top: 72px; border-radius: 28px; '
            'background-image: url(%s); background-size: cover; background-position: center; @media(--mobile) { min-height: 420px; margin-top: 44px; border-radius: 22px; }' % MEDIA['why-camera-bokeh'][1], [
            box('Who shade', 'position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: linear-gradient(90deg, rgba(14, 11, 18, 0.75) 0%, rgba(14, 11, 18, 0.1) 50%, rgba(14, 11, 18, 0) 100%);'),
            box('Who chip', 'flex-direction: row; align-items: center; gap: 10px; position: absolute; top: 40px; right: 40px; width: auto; padding: 12px 20px 12px 18px; border-radius: 40px; background: #ffffff; @media(--mobile) { top: 20px; right: 20px; }', [
                p('Who chip num', '07', 'font-family: var(--ab-font-display); font-weight: 900; font-size: 24px; line-height: 1; color: #c0138a;'),
                p('Who chip text', 'capabilities under one roof', 'font-family: var(--ab-font-body); font-weight: 500; font-size: 13px; color: #140f18;'),
            ]),
            h('Who manifesto', 'ONE PARTNER.<br><em>MULTIPLE CAPABILITIES.</em><br>COMPLETE SOLUTIONS.', 'position: relative; font-size: 56px; line-height: 0.96; padding: 0 56px 56px; @media(--mobile) { font-size: 38px; padding: 0 24px 28px; }', tag='h3', dark=True),
        ]),
        box('Who body', 'flex-direction: row; align-items: flex-start; gap: 40px; margin-top: 56px; @media(--tablet) { flex-wrap: wrap; } @media(--mobile) { flex-direction: column; gap: 20px; margin-top: 36px; }', [
            box('Who kicker', 'flex-direction: row; align-items: center; gap: 16px; flex: 1; min-width: 300px; margin-top: 4px;', [
                box('Who kicker bar', 'width: 40px; height: 2px; background: %s;' % G),
                p('Who kicker text', 'Interior • Events • Production', 'font-family: var(--ab-font-body); font-weight: 600; font-size: 13px; letter-spacing: 1.56px; text-transform: uppercase; color: #c0138a;'),
            ]),
            p('Who p1', 'From transforming interiors and producing large-scale events to creating bespoke furniture, printing, AV solutions, cleaning environments and landscape experiences, we bring multiple capabilities together under one roof.', 'width: 390px; line-height: 1.75; color: #4a4450; @media(--mobile) { width: 100%; }', ['abp-body']),
            p('Who p2', 'Our strength lies in combining creative thinking, technical expertise, production capability and reliable execution to deliver solutions that are practical, distinctive and built around each client’s requirements.', 'width: 390px; line-height: 1.75; color: #4a4450; @media(--mobile) { width: 100%; }', ['abp-body']),
        ]),
    ])


STRENGTHS = [('01', 'Creative Thinking', 'Ideas shaped around your brand, your space and your audience.'),
             ('02', 'Technical Expertise', 'AV, lighting, staging and fit-out know-how within one team.'),
             ('03', 'Production Capability', 'In-house facilities to fabricate, print, build and install.'),
             ('04', 'Reliable Execution', 'From the first specification to the final installation.')]


def strengths():
    cols = []
    for i, (num, title, text) in enumerate(STRENGTHS):
        n = 'Strength %s' % num
        cols.append(box(n, 'flex-direction: column; gap: 0; padding: 0 0 12px; border-radius: 4px;', [
            box(n + ' rule', 'width: 100%%; height: 2px; background: %s;' % (G if i == 0 else 'rgba(255, 255, 255, 0.16)'), cls=['abp-strength__rule']),
            p(n + ' num', num, 'font-family: var(--ab-font-display); font-weight: 900; font-size: 72px; line-height: 1; margin-top: 28px; color: %s;' % ('#e8388a' if i == 0 else 'rgba(255, 255, 255, 0.18)'), ['abp-strength__num']),
            p(n + ' title', title, 'font-family: var(--ab-font-display); font-weight: 800; font-size: 32px; line-height: 0.95; text-transform: uppercase; color: #ffffff; margin-top: 20px;'),
            p(n + ' text', text, 'font-family: var(--ab-font-body); font-size: 15px; line-height: 1.65; color: rgba(255, 255, 255, 0.68); margin-top: 16px; max-width: 264px;'),
        ], ['abp-strength']))
    return section('Strengths', SEC + ' ' + DARK_BG + ' overflow: hidden;', [
        head('Strengths', '02', 'Our Strengths', 'CREATIVE THINKING.<br><s>RELIABLE EXECUTION.</s>', 'Four strengths behind every project — so the result is practical, distinctive and built around each client’s requirements.', dark=True, size='80px'),
        grid('Strengths grid', 'grid-template-columns: repeat(4, minmax(0, 1fr)); grid-template-rows: repeat(1, auto); gap: 24px; margin-top: 72px; @media(--tablet) { grid-template-columns: repeat(2, minmax(0, 1fr)); grid-template-rows: repeat(2, auto); gap: 32px; } @media(--mobile) { grid-template-columns: repeat(1, minmax(0, 1fr)); grid-template-rows: repeat(4, auto); margin-top: 44px; }', cols),
    ], cls=['abp-dark-fx'])


def full_scope(num='03', anchor='services'):
    return section('Scope', SEC + ' background: #f7f3f8;', [
        head('Scope', num, 'What We Deliver', 'THE FULL SCOPE<s>.</s>', 'Our core services are designed to cover the physical, functional and experiential needs of businesses, brands, events and spaces.', size='80px'),
        sc('Scope tabs', '[ab_services_tabs]', 'margin-top: 72px; @media(--mobile) { margin-top: 40px; }'),
    ], anchor=anchor)


def locations():
    def card(n, image, eyebrow, title, address, query):
        return box(n, 'flex-direction: column; overflow: hidden; border-radius: 24px; border: 1px solid #ece4ee; background: #ffffff;', [
            box(n + ' media', 'flex-direction: column; overflow: hidden; height: 200px;', [
                img(n + ' image', image, 'width: 100%; height: 200px; object-fit: cover;', ['abp-loc-card__img'])], ['abp-loc-card__media']),
            box(n + ' body', 'flex-direction: column; gap: 8px; padding: 26px 32px 32px 31px; position: relative; @media(--mobile) { padding: 24px 22px 26px; }', [
                p(n + ' eyebrow', eyebrow, 'font-family: var(--ab-font-body); font-weight: 600; font-size: 11px; letter-spacing: 1.76px; text-transform: uppercase; color: #c0138a;'),
                p(n + ' title', title, 'font-family: var(--ab-font-display); font-weight: 800; font-size: 36px; line-height: 1; text-transform: uppercase; color: #140f18;'),
                box(n + ' foot', 'flex-direction: row; align-items: flex-end; justify-content: space-between; gap: 20px; @media(--mobile) { flex-direction: column; align-items: flex-start; gap: 16px; }', [
                    p(n + ' address', address, 'font-family: var(--ab-font-body); font-size: 14px; line-height: 1.65; color: #5a5360;'),
                    box(n + ' link', 'flex-direction: row; align-items: center; gap: 8px; width: auto; flex-shrink: 0;', [
                        p(n + ' link text', 'Get directions', 'font-family: var(--ab-font-body); font-weight: 600; font-size: 14px; color: #140f18;'),
                        p(n + ' link arrow', '→', 'font-family: var(--ab-font-body); font-weight: 600; font-size: 14px; color: #c0138a;', ['abp-arrow-link__icon']),
                    ], ['abp-arrow-link'], link='https://www.google.com/maps/search/?api=1&query=' + query),
                ]),
            ]),
        ], ['abp-loc-card'])
    return section('Locations', SEC + ' background: #f7f3f8;', [
        head('Locations', '03', 'Our Locations', 'TWO LOCATIONS.<br><em>ONE TEAM.</em>', 'Our Dubai headquarters and our Sharjah branch work as one team — with in-house production facilities keeping quality and timelines in our hands.'),
        grid('Locations grid', 'grid-template-columns: repeat(2, minmax(0, 1fr)); grid-template-rows: repeat(1, auto); gap: 20px; margin-top: 64px; @media(--mobile) { grid-template-columns: repeat(1, minmax(0, 1fr)); grid-template-rows: repeat(2, auto); margin-top: 40px; }', [
            card('Location HQ', 'location-av-control', 'Dubai · Headquarters', 'The Onyx Towers', 'Office 313, P3 Floor, Tower 1, The Greens,<br>P.O. Box 391186, Dubai, UAE', 'The+Onyx+Towers+The+Greens+Dubai'),
            card('Location Branch', 'service-rental', 'Sharjah · Branch &amp; Workshop', 'Al Sajaa Facility', 'Warehouse Shed 8, Plot No. 550,<br>Al Sajaa, Sharjah, UAE', 'Al+Sajaa+Industrial+Area+Sharjah'),
        ]),
    ], anchor='locations')


def clientele():
    return section('Clientele', SEC + ' background: #ffffff;', [
        head('Clientele', '04', 'Our Clientele', 'TRUSTED BY THOSE<br><em>WHO EXPECT MORE.</em>', 'Every client brings a different vision, challenge and expectation. We bring the experience and production capability to turn those requirements into reality.'),
        sc('Clientele logos', '[ab_clients_grid]', 'margin-top: 64px; @media(--mobile) { margin-top: 40px; }'),
    ], anchor='clientele')


# ---------------------------------------------------------------- Services page

def services_cards():
    s = home.services()
    s.cid = 'Capabilities'
    return s


# ---------------------------------------------------------------- Projects page

def projects_grid():
    return section('Projects grid', 'padding: 96px 80px 112px; background: #ffffff; @media(--tablet) { padding: 72px 40px 88px; } @media(--mobile) { padding: 48px 20px 64px; }', [
        sc('Projects list', '[ab_projects per_page="8"]'),
    ])


# ---------------------------------------------------------------- Contact page

def find_us():
    return section('Find us', SEC + ' background: #f7f3f8;', [
        box('Find us head', 'flex-direction: column; gap: 28px;', [
            label('Find us', '02', 'Find Us'),
            h('Find us title', 'VISIT OUR OFFICES<s>.</s>', 'font-size: 64px; @media(--mobile) { font-size: 44px; }'),
        ]),
        sc('Find us map', '[ab_find_us]', 'margin-top: 48px;'),
    ], anchor='find-us')


PAGES = {
    ABOUT: lambda: [page_hero('About hero', 'About Us', 'ABOUT US', 'One partner for interiors, events, production and everything in between — built around bringing ideas to life, seamlessly.', 'hero-exhibition-stand'),
                    who(), strengths(), locations(), clientele(), home.cta()],
    SERVICES: lambda: [page_hero('Services hero', 'Services', 'OUR SERVICES', 'Seven capabilities under one roof — interiors, rentals, events, printing, furniture, cleaning and landscape, delivered by one production partner.', 'hero-event-stage'),
                       full_scope('01', 'full-scope'), services_cards(), home.cta()],
    PROJECTS: lambda: [page_hero('Projects hero', 'Projects', 'OUR PROJECTS', 'Events, exhibitions, activations and spaces we’ve brought to life for brands across the UAE.', 'hero-concert-crowd'),
                       projects_grid(), home.cta()],
    CONTACT: lambda: [page_hero('Contact hero', 'Contact Us', 'GET IN TOUCH', 'Planning a fit-out, an event, a stand or all of it? Tell us what you need and our team will take it from there.', 'hero-event-stage-screens'),
                      home.contact('Contact', '01', 'Let’s Connect'), find_us()],
}

if __name__ == '__main__':
    for pid in [int(x) for x in sys.argv[1:]] or PAGES:
        for i, sec in enumerate(PAGES[pid]()):
            build(pid, [sec], mode='replace_children' if i == 0 else 'append')
