"""Library page (Figma frame 1:1769 'Library') -> WP page 14."""
import sys, os
sys.path.insert(0, os.path.join(os.path.dirname(__file__), '..', 'tools'))
from parts import *

POST = 14
SLUG = 'library'

# Copy is verbatim from the Figma file (placeholder text pending client content).
# No media files exist yet: tiles are placeholders and their view/download buttons go to Contact.
HERO_TEXT = 'We never stop exploring and providing you with the latest information about Yutong around the world'
TALL, SHORT = 369, 240
# masonry columns as designed (tile heights, top to bottom)
COLUMNS = [[TALL, SHORT, TALL, SHORT], [SHORT, TALL, SHORT, TALL], [TALL, SHORT, TALL, SHORT]]


def tile(i, height):
    n = 'Media Tile %d' % i
    mob = 260 if height == TALL else 190
    return flex(n, 'height: %dpx; padding: 0; flex: 0 0 auto; position: relative; justify-content: center; align-items: center; background-color: #f2f6fb; border-radius: 22px; overflow: hidden; @media(--tablet) { height: %dpx; } @media(--mobile) { flex: 0 0 auto; height: %dpx; }' % (height, int(height * 0.8), mob), [
        img(n + ' Placeholder', 'library-ic-placeholder', 'width: 48px;'),
        flex(n + ' Overlay', 'position: absolute; top: 0; left: 0; width: 100%; height: 100%; padding: 0 24px 28px; align-items: flex-end; background-image: linear-gradient(180deg, rgba(7,43,82,0.1) 30%, rgba(7,43,82,0.85) 100%); @media(--mobile) { padding: 0 18px 20px; }', [
            flex(n + ' Bar', 'padding: 0; gap: 12px; justify-content: space-between; align-items: center;', [
                p(n + ' Caption', 'Lorem ipsum dolor', FD + 'font-size: 16px; font-weight: 600; color: #ffffff; margin: 0;'),
                flex(n + ' Actions', 'width: auto; padding: 0; gap: 8px;', [
                    flex(n + ' View', 'width: 40px; min-width: 40px; height: 40px; padding: 0; border-radius: 20px; background-color: #ffffff; justify-content: center; align-items: center;', [img(n + ' View Icon', 'library-ic-zoom', 'width: 18px;')], href=URL['contact']),
                    flex(n + ' Download', 'width: 40px; min-width: 40px; height: 40px; padding: 0; border-radius: 20px; background-color: #0160b4; justify-content: center; align-items: center;', [img(n + ' Download Icon', 'library-ic-download', 'width: 18px;')], href=URL['contact']),
                ]),
            ]),
        ]).c('yt-library-overlay'),
    ]).c('yt-library-tile')


def pill(label, icon, on):
    return flex('Filter ' + label, 'width: auto; padding: 11px 22px; gap: 8px; align-items: center; border-radius: 34px; background-color: %s; %s' % (
        '#0160b4' if on else 'transparent', 'box-shadow: 0 6px 8px rgba(1,96,180,0.3);' if on else ''), [
        img('Filter %s Icon' % label, icon, 'width: 16px;'),
        p('Filter %s Label' % label, label, FB + 'font-size: 14px; font-weight: %d; color: %s; margin: 0;' % (500 if on else 400, '#ffffff' if on else '#323232')),
    ])


def page_btn(label, on=False, icon=None, blue=False):
    fill = 'background-color: #0160b4; border: 1px solid #0160b4;' if (on or blue) else 'background-color: #ffffff; border: 1px solid #e3e9f1;'
    kid = img('Page %s Icon' % label, icon, 'width: 19px;') if icon else p('Page %s Label' % label, label, FB + 'font-size: 14px; font-weight: 500; color: %s; margin: 0;' % ('#ffffff' if on else '#323232'))
    return flex('Page ' + label, 'width: 44px; min-width: 44px; height: 44px; padding: 0; border-radius: 22px; justify-content: center; align-items: center; %s %s @media(--mobile) { width: 32px; min-width: 32px; height: 32px; }' % (fill, EASE), [kid])


def library():
    k = 0
    cols = []
    for ci, hs in enumerate(COLUMNS):
        tiles = []
        for hgt in hs:
            k += 1
            tiles.append(tile(k, hgt))
        cols.append(flex('Masonry Col %d' % (ci + 1), 'padding: 0; flex: 1 1 0; flex-direction: column; gap: 24px; @media(--tablet) { gap: 16px; } @media(--mobile) { flex: 0 0 auto; }', tiles))
    pages = [page_btn('Prev', icon='library-ic-arrowl')] + [page_btn(str(n), on=n == 1) for n in range(1, 8)] + [page_btn('Next', icon='library-ic-arrow', blue=True)]
    return flex('Library', 'padding: 110px 120px; flex-direction: column; gap: 40px; background-color: #ffffff; @media(--tablet) { padding: 80px 40px; } @media(--mobile) { padding: 64px 20px; gap: 28px; }', [
        flex('Library Head', 'padding: 0; justify-content: space-between; align-items: flex-end; gap: 24px; @media(--mobile) { flex-direction: column; align-items: flex-start; }', [
            flex('Library Head Text', 'width: 560px; max-width: 100%; padding: 0; flex-direction: column; gap: 14px;', [
                eyebrow('Library Eyebrow', 'Lorem'),
                title('Library Title', 'Lorem ipsum dolor', 'font-size: 42px; font-weight: 700; line-height: 52px; color: #00050b;'),
            ]),
            flex('Library Filters', 'width: auto; padding: 6px; gap: 4px; background-color: #f2f6fb; border-radius: 40px;', [
                pill('Images', 'library-ic-image', True),
                pill('Videos', 'library-ic-play', False),
            ]),
        ]),
        flex('Masonry', 'padding: 0; gap: 24px; align-items: flex-start; @media(--tablet) { gap: 16px; } @media(--mobile) { flex-direction: column; align-items: stretch; }', cols),
        flex('Pagination', 'padding: 0; justify-content: center;', [
            flex('Pagination Items', 'width: auto; padding: 0; gap: 8px; align-items: center; flex-wrap: wrap; justify-content: center; @media(--mobile) { gap: 4px; }', pages),
        ]),
    ], tag='section')


SECTIONS = [
    ('library-1', lambda: page_hero('news', [('Home', 'home'), ('News', 'news'), ('Media Library', None)], 'Yutong Library', HERO_TEXT, 'library-hero', 560)),
    ('library-2', lambda: [library()]),
    ('library-3', lambda: [cta_section('Experience premium electric touring with long-range performance, intelligent technology and passenger-focused comfort.', 'library-cta'), glance_section()]),
    ('library-4', lambda: [quote_section('0 120px 110px'), footer()]),
]
