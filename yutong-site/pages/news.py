"""News page (Figma frame 1:1622 'News') -> WP page 13."""
import sys, os
sys.path.insert(0, os.path.join(os.path.dirname(__file__), '..', 'tools'))
from parts import *

POST = 13
SLUG = 'news'

# Copy is verbatim from the Figma file (placeholder text pending client content)
INTRO = 'We never stop exploring and providing you with the latest information about Yutong around the world'
DATE = 'September 11, 2026'
H_COP = '110 Yutong Buses Serve UNCCD COP17, Advancing Sustainable Development Through Green Mobility'
H_VOICES = 'Customer Voices | Reliable Range, Superior Ride Comfort: Yutong T Series &amp; IC Series Win Acclaim Among Dut.....'
H_EUROPE = 'Yutong Opens Its First Comprehensive Service Center in Europe'
CARDS = [('newspage-photo-1', H_COP), ('newspage-photo-2', H_VOICES), ('newspage-featured', H_EUROPE),
         ('newspage-photo-1', H_COP), ('newspage-photo-2', H_VOICES)]
FILTERS = ['All', 'Press Releases', 'Awards', 'Events']


def filters():
    """Static pills (Figma has a single grid, no per-tab content) -- first one active."""
    pills = [p('Filter %d' % (i + 1), t, FB + 'padding: 10px 20px; border-radius: 34px; font-size: 14px; font-weight: %s; color: %s; margin: 0; background-color: %s; @media(--mobile) { padding: 8px 12px; font-size: 13px; }' % (
        '500' if i == 0 else '400', '#ffffff' if i == 0 else '#323232', '#0160b4' if i == 0 else 'transparent')).c('yt-nowrap') for i, t in enumerate(FILTERS)]
    return flex('News Filters', 'width: auto; flex: 0 0 auto; padding: 6px; gap: 4px; border-radius: 40px; background-color: #f2f6fb; @media(--mobile) { gap: 2px; padding: 5px; }', pills)


def featured():
    return flex('Featured', 'padding: 0; align-items: stretch; background-color: #072b52; border-radius: 28px; overflow: hidden; box-shadow: 0 20px 48px rgba(0,26,64,0.16); @media(--tablet) { flex-direction: column; }', [
        div('Featured Media', 'width: 640px; flex: 0 0 auto; min-height: 380px; padding: 0; position: relative; @media(--tablet) { width: 100%; min-height: 0; height: 320px; } @media(--mobile) { height: 220px; }', [
            img('Featured Photo', 'newspage-featured', 'position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover;'),
        ]).c('yt-zoom'),
        flex('Featured Body', 'padding: 48px; flex: 1 1 0; flex-direction: column; justify-content: center; align-items: flex-start; gap: 18px; @media(--mobile) { padding: 28px 22px; }', [
            p('Featured Badge', 'FEATURED', FB + 'padding: 6px 14px; border-radius: 30px; background-color: rgba(255,255,255,0.12); font-size: 11px; font-weight: 600; letter-spacing: 1.32px; color: #ffffff; margin: 0;'),
            flex('Featured Date', 'width: auto; padding: 0; gap: 8px; align-items: center;', [
                img('Featured Date Icon', 'newspage-cal-white', 'width: 15px;'),
                p('Featured Date Text', DATE, FB + 'font-size: 13px; color: rgba(255,255,255,0.8); margin: 0;'),
            ]),
            h('Featured Title', H_EUROPE, 'h3', FD + 'font-size: 30px; font-weight: 700; line-height: 40px; color: #ffffff; margin: 0; @media(--mobile) { font-size: 24px; line-height: 32px; }', href=URL['news']),
            btn('Featured Read', 'Read Article', URL['news'], kind='white'),
        ]),
    ]).c('yt-lift')


def card(i, photo, headline):
    n = 'News Item %d' % i
    return flex(n, 'padding: 0; flex: 1 1 0; flex-direction: column; align-self: stretch; background-color: #ffffff; border: 1px solid #e3e9f1; border-radius: 22px; overflow: hidden; box-shadow: 0 10px 28px rgba(0,26,64,0.08); @media(--tablet) { flex: 1 1 40%; } @media(--mobile) { flex: 0 0 auto; }', [
        div(n + ' Media', 'padding: 0; height: 230px;', [
            img(n + ' Photo', photo, 'width: 100%; height: 230px; object-fit: cover;'),
        ]).c('yt-zoom'),
        flex(n + ' Body', 'padding: 24px; flex-direction: column; gap: 12px;', [
            flex(n + ' Date', 'width: auto; padding: 0; gap: 8px; align-items: center;', [
                img(n + ' Cal', 'ic-cal', 'width: 14px;'),
                p(n + ' Date Text', DATE, FB + 'font-size: 12px; color: #747474; margin: 0;'),
            ]),
            h(n + ' Title', headline, 'h3', FD + 'font-size: 17px; font-weight: 600; line-height: 25px; color: #00050b; margin: 0;', href=URL['news']),
            flex(n + ' More', 'width: auto; padding: 0; gap: 6px; align-items: center;', [
                p(n + ' More Text', 'Read More', FB + 'font-size: 14px; font-weight: 500; color: #0160b4; margin: 0;'),
                img(n + ' More Arrow', 'ic-arrow-blue-16b', 'width: 16px;'),
            ], href=URL['news']),
        ]),
    ]).c('yt-lift')


def pager():
    base = 'width: 44px; min-width: 44px; height: 44px; padding: 0; border-radius: 22px; justify-content: center; align-items: center; '
    off = base + 'background-color: #ffffff; border: 1px solid #e3e9f1;'
    on = base + 'background-color: #0160b4;'
    items = [flex('Page Prev', off, [img('Page Prev Icon', 'newspage-arrow-left', 'width: 19px;')])]
    for k in range(1, 6):
        items.append(flex('Page %d' % k, on if k == 1 else off, [
            p('Page %d Num' % k, str(k), FB + 'font-size: 14px; font-weight: 500; color: %s; margin: 0;' % ('#ffffff' if k == 1 else '#323232'))]))
    items.append(flex('Page Next', on, [img('Page Next Icon', 'newspage-arrow-right', 'width: 19px;')]))
    return flex('Pagination', 'padding: 0; justify-content: center;', [
        flex('Pagination Items', 'width: auto; padding: 0; gap: 8px; align-items: center; @media(--mobile) { gap: 6px; }', items)])


def list_section():
    rows = []
    for r in range(2):
        kids = [card(r * 3 + c + 1, *CARDS[r * 3 + c]) for c in range(3) if r * 3 + c < len(CARDS)]
        if len(kids) < 3:
            kids.append(div('News Row Spacer', 'padding: 0; flex: 1 1 0; @media(--tablet) { display: none; }'))
        rows.append(flex('News Row %d' % (r + 1), 'padding: 0; gap: 24px; align-items: stretch; @media(--tablet) { flex-wrap: wrap; } @media(--mobile) { flex-direction: column; }', kids))
    return flex('News List', 'padding: 110px 120px; flex-direction: column; gap: 40px; background-color: #ffffff; @media(--tablet) { padding: 80px 40px; } @media(--mobile) { padding: 64px 20px; gap: 32px; }', [
        flex('News List Head', 'padding: 0; justify-content: space-between; align-items: flex-end; gap: 24px; @media(--tablet) { flex-direction: column; align-items: flex-start; }', [
            flex('News List Head Text', 'max-width: 560px; padding: 0; flex-direction: column; gap: 14px;', [
                eyebrow('News List Eyebrow', 'Lorem'),
                title('News List Title', 'Lorem ipsum  dolor', 'font-size: 42px; font-weight: 700; line-height: 52px; color: #00050b; @media(--mobile) { font-size: 30px; line-height: 38px; }'),
            ]),
            filters(),
        ]),
        featured(),
        flex('News Rows', 'padding: 0; flex-direction: column; gap: 24px;', rows),
        pager(),
    ], tag='section')


SECTIONS = [
    ('news-1', lambda: page_hero('news', [('Home', 'home'), ('News', None)], 'Yutong News', INTRO, 'newspage-hero', 560)),
    ('news-2', lambda: [list_section()]),
    ('news-3', lambda: [cta_section('Experience premium electric touring with long-range performance, intelligent technology and passenger-focused comfort.', 'newspage-cta'), glance_section()]),
    ('news-4', lambda: [quote_section('0 120px 110px'), footer()]),
]
