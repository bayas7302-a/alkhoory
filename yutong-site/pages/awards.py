"""Awards page (Figma frame 1:1454 'Awards') -> WP page 12."""
import sys, os
sys.path.insert(0, os.path.join(os.path.dirname(__file__), '..', 'tools'))
from parts import *

POST = 12
SLUG = 'awards'

# Copy is verbatim from the Figma file (placeholder text pending client content)
LOREM_INTRO = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer mi metus, lobortis at varius eget, elementum non mi. Fusce egestas risus nunc, eget porttitor nunc vulputate vel. Praesent in lectus ut urna egestas porta.'
CARD_TEXT = "Discover how Subaru's Symmetrical All-Wheel Drive and X-MODE help you explore challenging terrains with confidence"


def award_card(i, dark=False):
    n = 'Award %d' % i
    return flex(n, 'padding: 28px; flex: 1 1 0; flex-direction: column; gap: 16px; align-self: stretch; border-radius: 24px; %s @media(--tablet) { flex: 1 1 40%%; } @media(--mobile) { flex: 0 0 auto; }' % (
        'background-color: #072b52;' if dark else 'background-color: #f2f6fb; border: 1px solid #e3e9f1;'), [
        flex(n + ' Top', 'padding: 0; justify-content: space-between; align-items: center;', [
            flex(n + ' Icon', 'width: 52px; min-width: 52px; height: 52px; padding: 0; border-radius: 26px; justify-content: center; align-items: center; background-color: %s;' % ('#0160b4' if dark else '#ffffff'),
                 [img(n + ' Glyph', 'ic-award-white' if dark else 'ic-award-blue', 'width: 23px;')]),
            p(n + ' Year', '2026', FB + 'padding: 5px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; color: #ffffff; margin: 0; background-color: %s;' % ('rgba(255,255,255,0.15)' if dark else '#0160b4')),
        ]),
        h(n + ' Title', 'Lorem ipsum dolor', 'h3', FD + 'font-size: 19px; font-weight: 600; line-height: 26px; color: %s; margin: 0;' % ('#ffffff' if dark else '#00050b')),
        p(n + ' Text', CARD_TEXT, FB + 'font-size: 14px; line-height: 23px; color: %s; margin: 0;' % ('rgba(255,255,255,0.8)' if dark else '#747474')),
        flex(n + ' More', 'width: auto; padding: 0; gap: 6px; align-items: center; margin-top: auto;', [
            p(n + ' More Text', 'Read more', FB + 'font-size: 14px; font-weight: 500; color: %s; margin: 0;' % ('#2c84cb' if dark else '#0160b4')),
            img(n + ' More Arrow', 'ic-arrow-sky-16' if dark else 'ic-arrow-blue-16b', 'width: 16px;'),
        ], href=URL['news']),
    ]).c('yt-lift')


def grid_section():
    rows = [flex('Awards Row %d' % r, 'padding: 0; gap: 24px; align-items: stretch; @media(--tablet) { flex-wrap: wrap; } @media(--mobile) { flex-direction: column; }',
                 [award_card(r * 4 + c + 1, dark=(r == 0 and c == 0)) for c in range(4)]) for r in range(2)]
    return flex('Awards Grid', 'padding: 110px 120px; flex-direction: column; align-items: center; gap: 48px; background-color: #ffffff; @media(--tablet) { padding: 80px 40px; } @media(--mobile) { padding: 64px 20px; gap: 32px; }', [
        flex('Awards Head', 'padding: 0; flex-direction: column; align-items: center; gap: 14px;', [
            eyebrow('Awards Eyebrow', 'Lorem', center=True),
            title('Awards Title', 'Lorem ipsum dolor', 'font-size: 42px; font-weight: 700; line-height: 52px; color: #00050b; text-align: center;'),
        ]),
        flex('Awards Rows', 'padding: 0; flex-direction: column; gap: 24px;', rows),
    ], tag='section')


SECTIONS = [
    ('awards-1', lambda: page_hero('about', [('Home', 'home'), ('About', 'about'), ('Awards', None)], 'Awards', LOREM_INTRO, 'hero-awards', 600)),
    ('awards-2', lambda: [grid_section()]),
    ('awards-3', lambda: [cta_section('Experience premium electric touring with long-range performance, intelligent technology and passenger-focused comfort.', 'cta-bg-awards'), glance_section()]),
    ('awards-4', lambda: [quote_section('0 120px 110px'), footer()]),
]
