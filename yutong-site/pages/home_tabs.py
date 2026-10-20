"""Home page: replace ONLY the "Models" section's children (WP page 8, element 6952b990) with the client's
category tabs -- Coach | E-Coach | City Bus | E-Bus | Van -- listing every model from tools/models_data.py.
Each tab is a one-row carousel (3 cards desktop / 2 tablet / 1 mobile; centred when they all fit);
the carousel CSS/JS is tools/html/home-carousel.html, injected by finalize.py into 'Html Slot home-carousel'.

The rest of Home was edited by hand in Elementor and must not be rebuilt; this script is the only
code-driven change to that page. The section heading/intro are kept as the user wrote them.

usage: python3 pages/home_tabs.py
"""
import sys, os, json, subprocess
sys.path.insert(0, os.path.join(os.path.dirname(__file__), '..', 'tools'))
from parts import *
import models_data as M
from lib import payload, save

POST = 8
SECTION_ID = '6952b990'   # "Models" section on the live Home page
HEAD_TITLE = 'Explore The YUTONG Range'
HEAD_INTRO = 'From coaches and city buses to vans and electric mobility, discover passenger transport solutions designed for different routes, capacities, and operations.'


def card(m):
    n = 'Home Model %s' % m['name']
    specs = [flex('%s Spec %d' % (n, k + 1), 'padding: 12px 14px; flex: 1 1 40%; min-width: 0; flex-direction: column; gap: 2px; background-color: #ffffff; border-radius: 12px;', [
        p('%s Spec %d Label' % (n, k + 1), lab, FB + 'font-size: 11px; color: #747474; margin: 0;'),
        p('%s Spec %d Value' % (n, k + 1), val, FD + 'font-size: 16px; font-weight: 600; line-height: 22px; color: #003376; margin: 0;'),
    ]) for k, (lab, val) in enumerate(m['specs'])]
    anchor_id = next(c[3] for c in M.CATEGORIES if c[0] == m['category'])
    return flex(n, 'padding: 0; flex: 0 0 auto; min-width: 0; flex-direction: column; border-radius: 24px; overflow: hidden; background-color: #f2f6fb;', [
        flex(n + ' Media', 'height: 210px; padding: 24px; justify-content: center; align-items: center;', [
            img(n + ' Photo', m['image'], 'width: 100%; height: 170px; object-fit: contain;')]).c('yt-zoom'),
        flex(n + ' Body', 'padding: 0 26px 28px; flex-direction: column; gap: 12px; flex-grow: 1;', [
            h(n + ' Name', m['name'], 'h3', FD + 'font-size: 30px; font-weight: 700; line-height: 36px; color: #0160b4; margin: 0;'),
            p(n + ' Tagline', m['tagline'], FB + 'font-size: 14px; line-height: 22px; color: #6b7280; margin: 0;'),
            flex(n + ' Specs', 'padding: 4px 0 0; gap: 8px; flex-wrap: wrap;', specs),
            flex(n + ' Btn Wrap', 'padding: 8px 0 0; margin-top: auto;', [btn(n + ' Explore', 'Explore Model', URL['models'] + '#' + anchor_id)]),
        ]),
    ]).c('yt-lift')


def carousel(cid, items):
    return flex(cid, 'padding: 0; gap: 24px; align-items: stretch;', [card(m) for m in items]).c('yt-carousel')


def nodes():
    tabs = [Node('e-tab', 'Range Tab ' + c[2], {}, FB + 'width: auto; padding: 11px 26px; border-width: 0; border-radius: 34px; background-color: transparent; font-size: 14px; color: #323232; cursor: pointer; @media(--mobile) { padding: 9px 16px; }',
                 [p('Range Tab %s Label' % c[2], c[2], FB + 'font-size: 14px; margin: 0;')]).c('yt-tab') for c in M.CATEGORIES]
    panels = [Node('e-tab-content', 'Range Panel ' + c[2], {}, 'padding: 0;', [carousel('Range Carousel ' + c[2], M.models(c[0]))]) for c in M.CATEGORIES]
    return [
        flex('Models Head', 'padding: 0; flex-direction: column; align-items: center; gap: 14px;', [
            eyebrow('Models Eyebrow', 'Our Range', center=True),
            title('Models Title', HEAD_TITLE, 'font-weight: 700; color: #00050b; text-align: center;'),
            body('Models Intro', HEAD_INTRO, 'max-width: 700px; text-align: center; line-height: 27px;', '#747474', 16),
        ]),
        Node('e-tabs', 'Range Tabs', {}, 'padding: 0; gap: 44px; align-items: center; @media(--mobile) { gap: 28px; }', [
            Node('e-tabs-menu', 'Range Tabs Menu', {}, 'width: auto; max-width: 100%; padding: 6px; gap: 4px; display: flex; flex-wrap: wrap; justify-content: center; background-color: #f2f6fb; border-radius: 40px; @media(--mobile) { border-radius: 24px; }', tabs),
            Node('e-tabs-content-area', 'Range Tabs Content', {}, 'width: 100%; padding: 0;', panels),
        ]),
        flex('Models Footer', 'padding: 0; justify-content: center;', [btn('Models View All', 'Explore Our Models', URL['models'])]),
        slot('Html Slot home-carousel'),
    ]


if __name__ == '__main__':
    path = save(os.path.join(WORK, 'payloads', 'home-tabs.json'), payload(POST, nodes(), parent=SECTION_ID, mode='replace_children'))
    tools = os.path.join(os.path.dirname(__file__), '..', 'tools')
    r = subprocess.run([sys.executable, os.path.join(tools, 'send.py'), path], capture_output=True, text=True)
    print(r.stdout[-1500:])
    if r.returncode:
        sys.exit(1)
    subprocess.run([os.path.join(WORK, 'call.sh'), 'elementor-publish-document', '{"post_id": %d}' % POST], capture_output=True)
    subprocess.run([sys.executable, os.path.join(tools, 'finalize.py'), str(POST)])
