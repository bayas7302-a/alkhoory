"""Awards page (Figma frame 1:1454 'Awards') -> WP page 12."""
import sys, os
sys.path.insert(0, os.path.join(os.path.dirname(__file__), '..', 'tools'))
from parts import *

POST = 12
SLUG = 'awards'

YT = 'https://en.yutong.com'
HONOR_URL = YT + '/about/honor/'
NEWS = YT + '/pressmedia/news/%s.shtml'

HERO_TEXT = ('Yutong always insists on creating greater value for customers. Its outstanding market performance has earned '
             'over 500 honors and international awards, from Busworld Europe to the Red Dot design award.')

# Real Yutong honors -- sources: en.yutong.com/about/honor/, /about/history/ and the linked press releases
AWARDS = [
    ('2025', 'Grand Award Bus: Yutong U15', 'At Busworld Europe 2025 the U15 city bus won the Grand Award Bus and a Label of Excellence, one of seven major awards Yutong secured at the show.', HONOR_URL),
    ('2025', 'Grand Award Coach: Yutong T14E', 'The T14E electric coach was named Grand Award Coach at Busworld Europe 2025, where Yutong also launched its EV Long-Life Tech.', HONOR_URL),
    ('2025', 'Label of Excellence: Yutong IC12E', 'The IC12E electric intercity coach received a Busworld Awards 2025 Label of Excellence.', HONOR_URL),
    ('ESG', 'EcoVadis Gold Medal', 'Yutong holds an EcoVadis Gold Medal, an independent rating of its sustainability performance across environment, labour and human rights, ethics and procurement.', 'https://recognition.ecovadis.com/TCDtFTAb7E66kMCUNm19Nw'),
    ('2024', 'National Science &amp; Technology Progress Award', 'Yutong&rsquo;s traction-battery R&amp;D and manufacturing project won a Second Prize, Yutong&rsquo;s fourth win of China&rsquo;s National Science and Technology Progress Award.', NEWS % '1806263274468151296'),
    ('2023', 'Busworld Design &amp; Ecology Labels', 'The 15m T15E luxury electric coach won the Design and Ecology labels at Busworld Europe 2023, the only coach to take the award.', NEWS % '1711306916094939136'),
    ('2023', 'UK Environmental Certification', 'The Yutong E12 electric bus was jointly certified by the UK&rsquo;s Zemo Partnership and UTAC for environmental performance in line with European standards.', NEWS % '2023FNQShJxhiJ'),
    ('2021', 'Red Dot Award: Product Design', 'The Xiaoyu 2.0 autonomous bus made Yutong the world&rsquo;s first autonomous bus maker recognised by the Red Dot industrial design award.', NEWS % '2021GAOlISgDTO'),
]


def award_card(i, year, name, text, href, dark=False):
    n = 'Award %d' % i
    return flex(n, 'padding: 28px; flex: 1 1 0; flex-direction: column; gap: 16px; align-self: stretch; border-radius: 24px; %s @media(--tablet) { flex: 1 1 40%%; } @media(--mobile) { flex: 0 0 auto; }' % (
        'background-color: #072b52;' if dark else 'background-color: #f2f6fb; border: 1px solid #e3e9f1;'), [
        flex(n + ' Top', 'padding: 0; justify-content: space-between; align-items: center;', [
            flex(n + ' Icon', 'width: 52px; min-width: 52px; height: 52px; padding: 0; border-radius: 26px; justify-content: center; align-items: center; background-color: %s;' % ('#0160b4' if dark else '#ffffff'),
                 [img(n + ' Glyph', 'ic-award-white' if dark else 'ic-award-blue', 'width: 23px;')]),
            p(n + ' Year', year, FB + 'padding: 5px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; color: #ffffff; margin: 0; background-color: %s;' % ('rgba(255,255,255,0.15)' if dark else '#0160b4')),
        ]),
        h(n + ' Title', name, 'h3', FD + 'font-size: 19px; font-weight: 600; line-height: 26px; color: %s; margin: 0;' % ('#ffffff' if dark else '#00050b')),
        p(n + ' Text', text, FB + 'font-size: 14px; line-height: 23px; color: %s; margin: 0;' % ('rgba(255,255,255,0.8)' if dark else '#747474')),
        Node('e-flexbox', n + ' More', {'link': link(href, True)}, 'width: auto; padding: 0; gap: 6px; align-items: center; margin-top: auto;', [
            p(n + ' More Text', 'Read more', FB + 'font-size: 14px; font-weight: 500; color: %s; margin: 0;' % ('#2c84cb' if dark else '#0160b4')),
            img(n + ' More Arrow', 'ic-arrow-sky-16' if dark else 'ic-arrow-blue-16b', 'width: 16px;'),
        ]),
    ]).c('yt-lift')


def grid_section():
    rows = [flex('Awards Row %d' % r, 'padding: 0; gap: 24px; align-items: stretch; @media(--tablet) { flex-wrap: wrap; } @media(--mobile) { flex-direction: column; }',
                 [award_card(r * 4 + c + 1, *AWARDS[r * 4 + c], dark=(r == 0 and c == 0)) for c in range(4)]) for r in range(2)]
    return flex('Awards Grid', 'padding: 110px 120px; flex-direction: column; align-items: center; gap: 48px; background-color: #ffffff; @media(--tablet) { padding: 80px 40px; } @media(--mobile) { padding: 64px 20px; gap: 32px; }', [
        flex('Awards Head', 'padding: 0; flex-direction: column; align-items: center; gap: 14px;', [
            eyebrow('Awards Eyebrow', 'Honors &amp; Awards', center=True),
            title('Awards Title', 'Recognised ' + hl('Worldwide'), 'font-size: 42px; font-weight: 700; line-height: 52px; color: #00050b; text-align: center;'),
        ]),
        flex('Awards Rows', 'padding: 0; flex-direction: column; gap: 24px;', rows),
    ], tag='section')


SECTIONS = [
    ('awards-1', lambda: page_hero('about', [('Home', 'home'), ('About', 'about'), ('Awards', None)], 'Honors &amp; ' + hl('Awards'), HERO_TEXT, 'ytawards-hero', 600)),
    ('awards-2', lambda: [grid_section()]),
    ('awards-3', lambda: [cta_section('Award-winning Yutong coaches and ' + hl('e&#8209;buses') + ', supplied and supported in the UAE by Al Khoory Automobiles.', 'cta-bg-awards'), glance_section()]),
    ('awards-4', lambda: [quote_section('0 120px 110px'), footer()]),
]
