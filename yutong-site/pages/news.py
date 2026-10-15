"""News page (Figma frame 1:1622 'News') -> WP page 13."""
import sys, os
sys.path.insert(0, os.path.join(os.path.dirname(__file__), '..', 'tools'))
from parts import *

POST = 13
SLUG = 'news'

# Real Yutong news (client-authorised content from https://en.yutong.com/pressmedia/news/).
# The UAE site has no article pages, so every card links to the original article in a new tab.
YT_NEWS = 'https://en.yutong.com/pressmedia/news/'
INTRO = ('The latest from Yutong around the world: new vehicles, customer deliveries, events and service milestones, '
         'shared by Al Khoory Automobiles, the official Yutong distributor in the UAE.')
FEATURED = ('ytnews-europe', 'September 11, 2026', 'Yutong Opens Its First Comprehensive Service Center in Europe',
            'Opened on September 8 in Stokke, Norway, the new Yutong Service Center brings vehicle delivery, after-sales service, '
            'spare parts supply and professional training together in one hub for battery-electric bus fleets.',
            YT_NEWS + '2098336357998006272.shtml')
# (photo, date, headline, excerpt, url)
CARDS = [
    ('ytnews-cop17', 'August 27, 2026', '110 Yutong Buses Serve UNCCD COP17, Advancing Sustainable Development Through Green Mobility',
     'As designated transport provider for the conference in Ulaanbaatar, Yutong buses shuttled delegates from 197 countries.',
     YT_NEWS + '2092876156117323776.shtml'),
    ('ytnews-dutch', 'August 10, 2026', 'Customer Voices | Reliable Range, Superior Ride Comfort: Yutong T Series &amp; IC Series Win Acclaim Among Dutch Clients',
     'Dutch operators share first-hand feedback on the all-electric T Series touring coach and IC Series intercity bus.',
     YT_NEWS + '2086695012816523264.shtml'),
    ('ytnews-workforce', 'July 31, 2026', 'Gather in Yutong, Connect the World, Grow Hand in Hand with Our Global Workforce',
     "Local employees from more than ten countries met at Yutong's Zhengzhou headquarters for in-person exchanges.",
     YT_NEWS + '2084848732939096064.shtml'),
    ('ytnews-hybrid', 'July 29, 2026', 'Operating in 206 Cities, Yutong Hybrid Buses Have Won Global Market Recognition Through Real-World Operation',
     'Yutong hybrid buses have been delivered to 206 cities worldwide, with cumulative sales reaching 33,000 units.',
     YT_NEWS + '2082345259316154368.shtml'),
    ('ytnews-expo-au', 'July 17, 2026', 'Yutong Debuts at 2026 Australasia Bus &amp; Coach Expo, Demonstrating Product Strength for Diverse Scenarios',
     'Five models on show in Sydney, led by the Australian debut of the 18-metre battery-electric U18.',
     YT_NEWS + '2077944103810764800.shtml'),
    ('ytnews-c12e', 'June 18, 2026', '261 km on Just 47% Battery! Yutong C12E Hong Kong Long-Range Challenge Succeeds',
     'A Hong Kong-spec C12E covered 261 km on a real cross-border route with the air conditioning running non-stop.',
     YT_NEWS + '2068983698539286528.shtml'),
]


def blank(node, url):
    """Link a node to an external page in a new tab (lib.link() defaults to same tab)."""
    node.cfg['link'] = link(url, True)
    return node


def featured():
    photo, date, headline, excerpt, url = FEATURED
    return flex('Featured', 'padding: 0; align-items: stretch; background-color: #072b52; border-radius: 28px; overflow: hidden; box-shadow: 0 20px 48px rgba(0,26,64,0.16); @media(--tablet) { flex-direction: column; }', [
        blank(div('Featured Media', 'width: 640px; flex: 0 0 auto; min-height: 380px; padding: 0; position: relative; @media(--tablet) { width: 100%; min-height: 0; height: 320px; } @media(--mobile) { height: 220px; }', [
            img('Featured Photo', photo, 'position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover;'),
        ]), url).c('yt-zoom'),
        flex('Featured Body', 'padding: 48px; flex: 1 1 0; flex-direction: column; justify-content: center; align-items: flex-start; gap: 18px; @media(--mobile) { padding: 28px 22px; }', [
            p('Featured Badge', 'FEATURED', FB + 'padding: 6px 14px; border-radius: 30px; background-color: rgba(255,255,255,0.12); font-size: 11px; font-weight: 600; letter-spacing: 1.32px; color: #ffffff; margin: 0;'),
            flex('Featured Date', 'width: auto; padding: 0; gap: 8px; align-items: center;', [
                img('Featured Date Icon', 'newspage-cal-white', 'width: 15px;'),
                p('Featured Date Text', date, FB + 'font-size: 13px; color: rgba(255,255,255,0.8); margin: 0;'),
            ]),
            blank(h('Featured Title', headline, 'h3', FD + 'font-size: 30px; font-weight: 700; line-height: 40px; color: #ffffff; margin: 0; @media(--mobile) { font-size: 24px; line-height: 32px; }'), url),
            p('Featured Excerpt', excerpt, FB + 'font-size: 15px; line-height: 26px; color: rgba(255,255,255,0.78); margin: 0;'),
            blank(btn('Featured Read', 'Read Article', url, kind='white'), url),
        ]),
    ]).c('yt-lift')


def card(i, photo, date, headline, excerpt, url):
    n = 'News Item %d' % i
    return flex(n, 'padding: 0; flex: 1 1 0; flex-direction: column; align-self: stretch; background-color: #ffffff; border: 1px solid #e3e9f1; border-radius: 22px; overflow: hidden; box-shadow: 0 10px 28px rgba(0,26,64,0.08); @media(--tablet) { flex: 1 1 40%; } @media(--mobile) { flex: 0 0 auto; }', [
        blank(div(n + ' Media', 'padding: 0; height: 230px;', [
            img(n + ' Photo', photo, 'width: 100%; height: 230px; object-fit: cover;'),
        ]), url).c('yt-zoom'),
        flex(n + ' Body', 'padding: 24px; flex-direction: column; gap: 12px; flex-grow: 1;', [
            flex(n + ' Date', 'width: auto; padding: 0; gap: 8px; align-items: center;', [
                img(n + ' Cal', 'ic-cal', 'width: 14px;'),
                p(n + ' Date Text', date, FB + 'font-size: 12px; color: #747474; margin: 0;'),
            ]),
            blank(h(n + ' Title', headline, 'h3', FD + 'font-size: 17px; font-weight: 600; line-height: 25px; color: #00050b; margin: 0;'), url),
            p(n + ' Excerpt', excerpt, FB + 'font-size: 14px; line-height: 22px; color: #747474; margin: 0;'),
            blank(flex(n + ' More', 'width: auto; padding: 0; gap: 6px; align-items: center; margin-top: auto;', [
                p(n + ' More Text', 'Read More', FB + 'font-size: 14px; font-weight: 500; color: #0160b4; margin: 0;'),
                img(n + ' More Arrow', 'ic-arrow-blue-16b', 'width: 16px;'),
            ]), url),
        ]),
    ]).c('yt-lift')


def list_section():
    rows = []
    for r in range(2):
        rows.append(flex('News Row %d' % (r + 1), 'padding: 0; gap: 24px; align-items: stretch; @media(--tablet) { flex-wrap: wrap; } @media(--mobile) { flex-direction: column; }',
                         [card(r * 3 + c + 1, *CARDS[r * 3 + c]) for c in range(3)]))
    return flex('News List', 'padding: 110px 120px; flex-direction: column; gap: 40px; background-color: #ffffff; @media(--tablet) { padding: 80px 40px; } @media(--mobile) { padding: 64px 20px; gap: 32px; }', [
        flex('News List Head', 'padding: 0; justify-content: space-between; align-items: flex-end; gap: 24px; @media(--tablet) { flex-direction: column; align-items: flex-start; }', [
            flex('News List Head Text', 'max-width: 640px; padding: 0; flex-direction: column; gap: 14px;', [
                eyebrow('News List Eyebrow', 'Press Releases'),
                title('News List Title', 'Latest from ' + hl('Yutong'), 'font-size: 42px; font-weight: 700; line-height: 52px; color: #00050b; @media(--mobile) { font-size: 30px; line-height: 38px; }'),
            ]),
            blank(btn('News List Archive', 'All Yutong News', YT_NEWS, kind='outline-blue'), YT_NEWS),
        ]),
        featured(),
        flex('News Rows', 'padding: 0; flex-direction: column; gap: 24px;', rows),
    ], tag='section')


SECTIONS = [
    ('news-1', lambda: page_hero('news', [('Home', 'home'), ('News', None)], 'Yutong News', INTRO, 'newspage-hero', 560)),
    ('news-2', lambda: [list_section()]),
    ('news-3', lambda: [cta_section('Experience premium electric touring with long-range performance, intelligent technology and passenger-focused comfort.', 'newspage-cta'), glance_section()]),
    ('news-4', lambda: [quote_section('0 120px 110px'), footer()]),
]
