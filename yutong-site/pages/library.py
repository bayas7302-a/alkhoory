"""Library page (Figma frame 1:1769 'Library') -> WP page 14."""
import sys, os
sys.path.insert(0, os.path.join(os.path.dirname(__file__), '..', 'tools'))
from parts import *

POST = 14
SLUG = 'library'

# Yutong photos and videos, all hosted in this site's media library (no off-site links).
# Images / Videos is a working e-tabs switch; no download options (client request). The view button
# on each photo opens the full-size image in a new tab.
HERO_TEXT = ('Photos and videos of Yutong buses and coaches at work around the world, from trade shows and '
             'major international events to customer operations and service centres.')
TALL, SHORT = 369, 240
# masonry columns as designed (tile heights, top to bottom)
COLUMNS = [[TALL, SHORT, TALL, SHORT], [SHORT, TALL, SHORT, TALL], [TALL, SHORT, TALL, SHORT]]
# (asset, caption) in tile order: column 1 top to bottom, then column 2, then column 3
PHOTOS = [
    ('ytlib-01', 'Yutong hybrid city bus in service'),
    ('ytlib-02', 'UNCCD COP17 shuttle fleet, Ulaanbaatar'),
    ('ytlib-03', 'Yutong coach at the 2026 Australasia Bus &amp; Coach Expo'),
    ('ytlib-04', 'On-site service support at UNCCD COP17'),
    ('ytlib-05', 'Yutong Service Center, Stokke, Norway'),
    ('ytlib-06', 'U18 18-metre battery-electric bus'),
    ('ytlib-07', 'C12E long-range challenge, Hong Kong'),
    ('ytlib-08', 'Yutong coach in London'),
    ('ytlib-09', 'T Series electric coach in the Netherlands'),
    ('ytlib-10', 'Workshop at the Norway Service Center'),
    ('ytlib-11', 'Yutong at the Australasia Bus &amp; Coach Expo, Sydney'),
    ('ytlib-12', 'The Yutong team in Sydney'),
]
# (title, mp4 in the WP media library -- 720p web transcodes, faststart, poster asset)
VU = SITE + '/wp-content/uploads/2026/10/yutong-video-%d.mp4'
VIDEOS = [
    ('2026 FIAA @ Spain', VU % 1, 'ytlib-vid-1'),
    ('Lean Operation in Norway, More Vision in Europe', VU % 2, 'ytlib-vid-2'),
    ("2026 World Events: Yutong's Moments at UNCCD COP17", VU % 3, 'ytlib-vid-3'),
    ('Together, We Grow', VU % 4, 'ytlib-vid-4'),
    ('Customer Voice | How Yutong T12E Achieves High-efficiency Operation in Denmark', VU % 5, 'ytlib-vid-5'),
    ('Yuntai Mountain Energy Saving Challenge', VU % 6, 'ytlib-vid-6'),
]


def blank(node, url):
    """Link a node to a page/file in a new tab (lib.link() defaults to same tab)."""
    node.cfg['link'] = link(url, True)
    return node


def tile(i, height, photo, caption):
    n = 'Media Tile %d' % i
    mob = 260 if height == TALL else 190
    return flex(n, 'height: %dpx; padding: 0; flex: 0 0 auto; position: relative; justify-content: center; align-items: center; background-color: #f2f6fb; border-radius: 22px; overflow: hidden; @media(--tablet) { height: %dpx; } @media(--mobile) { flex: 0 0 auto; height: %dpx; }' % (height, int(height * 0.8), mob), [
        img(n + ' Photo', photo, 'position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover;'),
        flex(n + ' Overlay', 'position: absolute; top: 0; left: 0; width: 100%; height: 100%; padding: 0 24px 28px; align-items: flex-end; background-image: linear-gradient(180deg, rgba(7,43,82,0.1) 30%, rgba(7,43,82,0.85) 100%); @media(--mobile) { padding: 0 18px 20px; }', [
            flex(n + ' Bar', 'padding: 0; gap: 12px; justify-content: space-between; align-items: center;', [
                p(n + ' Caption', caption, FD + 'flex: 1 1 0; font-size: 16px; font-weight: 600; line-height: 22px; color: #ffffff; margin: 0;'),
                blank(flex(n + ' View', 'width: 40px; min-width: 40px; height: 40px; padding: 0; border-radius: 20px; background-color: #ffffff; justify-content: center; align-items: center;', [img(n + ' View Icon', 'library-ic-zoom', 'width: 18px;')]), asset(photo)['url']),
            ]),
        ]).c('yt-library-overlay'),
    ]).c('yt-library-tile', 'yt-zoom')


def gallery():
    k = 0
    cols = []
    for ci, hs in enumerate(COLUMNS):
        tiles = []
        for hgt in hs:
            tiles.append(tile(k + 1, hgt, *PHOTOS[k]))
            k += 1
        cols.append(flex('Masonry Col %d' % (ci + 1), 'padding: 0; flex: 1 1 0; flex-direction: column; gap: 24px; @media(--tablet) { gap: 16px; } @media(--mobile) { flex: 0 0 auto; }', tiles))
    return flex('Masonry', 'padding: 0; gap: 24px; align-items: flex-start; @media(--tablet) { gap: 16px; } @media(--mobile) { flex-direction: column; align-items: stretch; }', cols)


def video_card(i, name, mp4, poster):
    n = 'Video %d' % i
    player = Node('e-self-hosted-video', n + ' Player', {
        'source': {'url': mp4}, 'controls': True, 'preload': 'none', 'playsinline': True, 'download': False,
        'poster_enabled': True, 'poster': {'src': {'id': asset(poster)['id']}, 'size': 'large'},
    }, 'width: 100%; aspect-ratio: 16/9; height: auto; display: block; object-fit: cover; background-color: #072b52; border-radius: 18px; overflow: hidden;')
    return flex(n, 'padding: 0; flex: 1 1 0; min-width: 0; flex-direction: column; gap: 14px; @media(--tablet) { flex: 1 1 40%; } @media(--mobile) { flex: 0 0 auto; }', [
        player,
        h(n + ' Title', name, 'h3', FD + 'font-size: 17px; font-weight: 600; line-height: 25px; color: #00050b; margin: 0; padding: 0 4px; transition: color 0.3s; &:hover { color: #0160b4; }'),
    ])


def videos():
    rows = [flex('Video Row %d' % (r + 1), 'padding: 0; gap: 24px; align-items: flex-start; @media(--tablet) { flex-wrap: wrap; } @media(--mobile) { flex-direction: column; align-items: stretch; gap: 28px; }',
                 [video_card(r * 3 + c + 1, *VIDEOS[r * 3 + c]) for c in range(3)]) for r in range(2)]
    return flex('Videos', 'padding: 0; flex-direction: column; gap: 36px; @media(--mobile) { gap: 28px; }', rows)


def tab(label, icon):
    return Node('e-tab', 'Tab ' + label, {}, FB + 'width: auto; display: flex; gap: 8px; align-items: center; padding: 11px 22px; border-width: 0; border-radius: 34px; background-color: transparent; font-size: 14px; color: #323232; cursor: pointer; @media(--mobile) { padding: 9px 16px; }', [
        img('Tab %s Icon' % label, icon, 'width: 16px;'),
        p('Tab %s Label' % label, label, FB + 'font-size: 14px; margin: 0;'),
    ]).c('yt-tab', 'yt-library-tab')


def library():
    head = flex('Library Head Text', 'width: 560px; max-width: 100%; padding: 0; flex-direction: column; gap: 14px;', [
        eyebrow('Library Eyebrow', 'Media Library'),
        title('Library Title', 'Photos &amp; ' + hl('Videos'), 'font-size: 42px; font-weight: 700; line-height: 52px; color: #00050b;'),
    ])
    tabs = Node('e-tabs', 'Library Tabs', {}, 'padding: 0; flex-direction: row; flex-wrap: wrap; justify-content: space-between; align-items: flex-end; gap: 40px; @media(--mobile) { gap: 28px; flex-direction: column; align-items: flex-start; }', [
        head,
        Node('e-tabs-menu', 'Library Tabs Menu', {}, 'width: auto; padding: 6px; gap: 4px; display: flex; background-color: #f2f6fb; border-radius: 40px;', [
            tab('Images', 'library-ic-image'), tab('Videos', 'library-ic-play')]),
        Node('e-tabs-content-area', 'Library Tabs Content', {}, 'width: 100%; flex: 0 0 100%; padding: 0;', [
            Node('e-tab-content', 'Panel Images', {}, 'padding: 0;', [gallery()]),
            Node('e-tab-content', 'Panel Videos', {}, 'padding: 0;', [videos()]),
        ]),
    ])
    return flex('Library', 'padding: 110px 120px; flex-direction: column; gap: 40px; background-color: #ffffff; @media(--tablet) { padding: 80px 40px; } @media(--mobile) { padding: 64px 20px; gap: 28px; }', [tabs], tag='section')


SECTIONS = [
    ('library-1', lambda: page_hero('news', [('Home', 'home'), ('News', 'news'), ('Media Library', None)], 'Yutong Library', HERO_TEXT, 'library-hero', 560)),
    ('library-2', lambda: [library()]),
    ('library-3', lambda: [cta_section('Experience premium electric touring with long-range performance, intelligent technology and passenger-focused comfort.', 'library-cta'), glance_section()]),
    ('library-4', lambda: [quote_section('0 120px 110px'), footer()]),
]
