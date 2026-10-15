"""On-site detail pages for every News card and every Award card (dummy body copy).

Each card on /news/ and /awards/ links to its own child page here (/news/<slug>/, /awards/<slug>/),
so nothing links out to the manufacturer's site. The lead paragraph is the card summary; the rest of
the article is placeholder text for the client to replace.

Build all: python3 tools/run.py articles   (creates missing WP pages, then builds each one)
"""
import sys, os, json, subprocess
sys.path.insert(0, os.path.join(os.path.dirname(__file__), '..', 'tools'))
from parts import *
import importlib.util

HERE = os.path.dirname(os.path.abspath(__file__))
AUTH = json.load(open(os.path.expanduser('~/.claude.json')))['mcpServers']['my-blog-elementor']['headers']['Authorization']
API = 'https://soharon.co.uk/yutong/wp-json/wp/v2/pages'
NEWS_POST, AWARDS_POST = 13, 12


def _load(name):
    spec = importlib.util.spec_from_file_location(name, os.path.join(HERE, name + '.py'))
    m = importlib.util.module_from_spec(spec)
    spec.loader.exec_module(m)
    return m


def news_items():
    n = _load('news')
    return [dict(kind='news', photo=c[0], date=c[1], title=c[2], lead=c[3]) for c in [n.FEATURED] + n.CARDS]


def award_items():
    a = _load('awards')
    return [dict(kind='award', year=x[0], title=x[1], lead=x[2]) for x in a.AWARDS]


LOREM = [
    'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer mi metus, lobortis at varius eget, elementum non mi. '
    'Fusce egestas risus nunc, eget porttitor nunc vulputate vel. Praesent in lectus ut urna egestas porta, sed dictum '
    'nisl. Curabitur efficitur, lectus sit amet faucibus pharetra, orci purus sodales nibh, vitae tincidunt sem nulla at erat.',
    'Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia curae. Suspendisse potenti. Nullam '
    'vehicula, ipsum non condimentum pretium, mauris arcu tristique libero, eu fermentum nisl magna id neque. Aliquam erat '
    'volutpat. Morbi at turpis nec augue posuere dignissim in ac justo.',
    'Donec sed odio dui. Maecenas faucibus mollis interdum. Cras mattis consectetur purus sit amet fermentum. Etiam porta '
    'sem malesuada magna mollis euismod. Sed posuere consectetur est at lobortis, aenean eu leo quam pellentesque ornare sem.',
]
HIGHLIGHTS = ['Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
              'Integer mi metus, lobortis at varius eget, elementum non mi.',
              'Fusce egestas risus nunc, eget porttitor nunc vulputate vel.']
QUOTE = 'Praesent in lectus ut urna egestas porta. Curabitur efficitur, lectus sit amet faucibus pharetra, orci purus sodales nibh.'


# ---------- WordPress page bookkeeping ----------

def rest(method, url, body=None):
    cmd = ['curl', '-sS', '-m', '120', '-X', method, '-H', 'Authorization: ' + AUTH, '-H', 'Content-Type: application/json', url]
    if body is not None:
        cmd += ['--data-binary', '@-']
    return json.loads(subprocess.run(cmd, input=json.dumps(body) if body is not None else None, capture_output=True, text=True, check=True).stdout)


def ensure_page(parent, slug, title):
    """Find the child page by slug, or create it as an Elementor canvas page. Returns the post id."""
    found = rest('GET', '%s?slug=%s&parent=%d&status=publish,draft&context=edit' % (API, slug, parent))
    if found:
        pid = found[0]['id']
    else:
        r = subprocess.run([os.path.join(WORK, 'call.sh'), 'elementor-create-page', json.dumps({'title': title})], capture_output=True, text=True).stdout
        pid = json.loads(r)['id']
    rest('POST', '%s/%d' % (API, pid), {'parent': parent, 'slug': slug, 'title': title, 'template': 'elementor_canvas'})
    return pid


# ---------- layout ----------

def meta_row(it):
    if it['kind'] == 'news':
        bits = [flex('Article Date', 'width: auto; padding: 7px 14px; gap: 8px; align-items: center; border-radius: 30px; background-color: #f2f6fb;', [
                    img('Article Date Icon', 'ic-cal', 'width: 14px;'),
                    p('Article Date Text', it['date'], FB + 'font-size: 13px; font-weight: 500; color: #00050b; margin: 0;')]),
                p('Article Tag', 'NEWS', FB + 'padding: 7px 14px; border-radius: 30px; background-color: #0160b4; font-size: 11px; font-weight: 600; letter-spacing: 1.32px; color: #ffffff; margin: 0;')]
    else:
        bits = [flex('Article Year', 'width: auto; padding: 7px 14px; gap: 8px; align-items: center; border-radius: 30px; background-color: #f2f6fb;', [
                    img('Article Year Icon', 'ic-award-blue', 'width: 16px;'),
                    p('Article Year Text', it['year'], FB + 'font-size: 13px; font-weight: 600; color: #00050b; margin: 0;')]),
                p('Article Tag', 'HONORS &amp; AWARDS', FB + 'padding: 7px 14px; border-radius: 30px; background-color: #0160b4; font-size: 11px; font-weight: 600; letter-spacing: 1.32px; color: #ffffff; margin: 0;')]
    return flex('Article Meta', 'width: auto; padding: 0; gap: 10px; align-items: center; flex-wrap: wrap;', bits)


def body_section(it):
    photo = it.get('photo') or 'ytabout-honors-trophies'
    back = ('Back to News', URL['news']) if it['kind'] == 'news' else ('Back to Awards', URL['awards'])
    para = lambda i, t: body('Article Para %d' % i, t, 'line-height: 30px;', '#4b5563', 17)
    return flex('Article', 'padding: 110px 120px; flex-direction: column; align-items: center; background-color: #ffffff; @media(--tablet) { padding: 80px 40px; } @media(--mobile) { padding: 64px 20px; }', [
        flex('Article Column', 'max-width: 820px; padding: 0; flex-direction: column; gap: 26px;', [
            meta_row(it),
            p('Article Lead', it['lead'], FD + 'font-size: 22px; font-weight: 500; line-height: 34px; color: #00050b; margin: 0; @media(--mobile) { font-size: 19px; line-height: 30px; }'),
            img('Article Photo', photo, 'width: 100%; height: 460px; object-fit: cover; border-radius: 24px; @media(--mobile) { height: 240px; border-radius: 18px; }'),
            para(1, LOREM[0]),
            para(2, LOREM[1]),
            h('Article Subhead', 'Key highlights', 'h2', FD + 'font-size: 28px; font-weight: 600; line-height: 36px; color: #00050b; margin: 8px 0 0;'),
            flex('Article Highlights', 'padding: 0; flex-direction: column; gap: 12px;', [
                flex('Highlight %d' % (i + 1), 'padding: 0; gap: 12px; align-items: center;', [
                    flex('Highlight %d Badge' % (i + 1), 'width: 26px; min-width: 26px; height: 26px; padding: 0; border-radius: 13px; background-color: #e3effa; justify-content: center; align-items: center;', [img('Highlight %d Icon' % (i + 1), 'ic-check', 'width: 11px;')]),
                    p('Highlight %d Text' % (i + 1), t, FB + 'font-size: 16px; font-weight: 500; color: #00050b; margin: 0;'),
                ]) for i, t in enumerate(HIGHLIGHTS)]),
            flex('Article Quote', 'padding: 28px 32px; flex-direction: column; gap: 10px; background-color: #f2f6fb; border-radius: 20px; border-width: 0 0 0 4px; border-style: solid; border-color: #0160b4;', [
                p('Article Quote Text', '&ldquo;' + QUOTE + '&rdquo;', FD + 'font-size: 20px; font-weight: 500; line-height: 32px; color: #003376; margin: 0;'),
                p('Article Quote By', 'Lorem Ipsum, Al Khoory Automobiles', FB + 'font-size: 14px; color: #747474; margin: 0;'),
            ]),
            para(3, LOREM[2]),
            flex('Article Actions', 'padding: 10px 0 0; gap: 12px; flex-wrap: wrap;', [
                btn('Article Back', back[0], back[1], kind='outline-blue', arrow=False),
            ]),
        ]),
    ], tag='article')


def related_card(i, it):
    href = article_url(it['kind'], it['title'])
    n = 'Related %d' % i
    if it['kind'] == 'news':
        top = div(n + ' Media', 'padding: 0; height: 200px;', [img(n + ' Photo', it['photo'], 'width: 100%; height: 200px; object-fit: cover;')], href=href).c('yt-zoom')
        meta = it['date']
    else:
        top = flex(n + ' Media', 'padding: 28px 26px 0; justify-content: space-between; align-items: center;', [
            flex(n + ' Icon', 'width: 52px; min-width: 52px; height: 52px; padding: 0; border-radius: 26px; justify-content: center; align-items: center; background-color: #0160b4;', [img(n + ' Glyph', 'ic-award-white', 'width: 23px;')]),
            p(n + ' Year', it['year'], FB + 'padding: 5px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; color: #ffffff; margin: 0; background-color: #0160b4;'),
        ])
        meta = 'Honors &amp; Awards'
    return flex(n, 'padding: 0; flex: 1 1 0; flex-direction: column; background-color: #ffffff; border-radius: 22px; overflow: hidden; border: 1px solid #e3e9f1; @media(--mobile) { flex: 0 0 auto; }', [
        top,
        flex(n + ' Body', 'padding: 24px 26px 28px; flex-direction: column; gap: 12px; flex-grow: 1;', [
            p(n + ' Meta', meta, FB + 'font-size: 12px; font-weight: 500; color: #747474; margin: 0;'),
            h(n + ' Title', it['title'], 'h3', FD + 'font-size: 17px; font-weight: 600; line-height: 25px; color: #00050b; margin: 0;', href=href),
            flex(n + ' More', 'width: auto; padding: 0; gap: 8px; align-items: center; margin-top: auto;', [
                p(n + ' More Text', 'Read More', FB + 'font-size: 14px; font-weight: 500; color: #0160b4; margin: 0;'),
                img(n + ' More Arrow', 'ic-arrow-blue-16b', 'width: 16px;'),
            ], href=href),
        ]),
    ]).c('yt-lift')


def related_section(it, items):
    others = [x for x in items if x['title'] != it['title']]
    k = items.index(it)
    pick = (others[k:] + others[:k])[:3]
    head = 'More News' if it['kind'] == 'news' else 'More Honors &amp; Awards'
    return flex('Related', 'padding: 110px 120px; flex-direction: column; gap: 40px; background-color: #f2f6fb; @media(--tablet) { padding: 80px 40px; } @media(--mobile) { padding: 64px 20px; gap: 28px; }', [
        flex('Related Head', 'padding: 0; justify-content: space-between; align-items: flex-end; gap: 20px; @media(--mobile) { flex-direction: column; align-items: flex-start; }', [
            flex('Related Head Text', 'width: auto; padding: 0; flex-direction: column; gap: 14px;', [
                eyebrow('Related Eyebrow', 'Keep Reading'),
                title('Related Title', head, 'font-weight: 700; color: #00050b;'),
            ]),
            btn('Related All', 'View All', URL['news'] if it['kind'] == 'news' else URL['awards']),
        ]),
        flex('Related Cards', 'padding: 0; gap: 24px; align-items: stretch; @media(--mobile) { flex-direction: column; }',
             [related_card(i + 1, x) for i, x in enumerate(pick)]),
    ], tag='section')


def smaller_title(nodes):
    """Article headlines are long: drop the hero H1 from 64px to 44px (36/30 on tablet/mobile)."""
    def walk(n):
        if n.cid == 'Page Title':
            n.css = n.css.replace('font-size: 64px; font-weight: 700; line-height: 74px;', 'font-size: 44px; font-weight: 700; line-height: 54px;')
            n.css = n.css.replace('@media(--tablet) { font-size: 50px; line-height: 58px; } @media(--mobile) { font-size: 38px; line-height: 46px; }',
                                  '@media(--tablet) { font-size: 36px; line-height: 44px; } @media(--mobile) { font-size: 28px; line-height: 36px; }')
        for k in n.kids:
            walk(k)
    for n in nodes:
        if n is not None:  # header() is None when the theme provides it
            walk(n)
    return nodes


def sections_for(it, items):
    key = slugify(it['title'])[:24]
    if it['kind'] == 'news':
        crumbs, active, photo = [('Home', 'home'), ('News', 'news'), ('Article', None)], 'news', it['photo']
        byline = 'Published %s &middot; Yutong News' % it['date']
    else:
        crumbs, active, photo = [('Home', 'home'), ('Awards', 'awards'), ('Award', None)], 'about', 'ytawards-hero'
        byline = 'Honors &amp; Awards &middot; %s' % it['year']
    return [
        ('art-%s-1' % key, lambda: smaller_title(page_hero(active, crumbs, it['title'], byline, photo, 520))),
        ('art-%s-2' % key, lambda: [body_section(it)]),
        ('art-%s-3' % key, lambda: [related_section(it, items), quote_section(), footer()]),
    ]


def PAGES():
    out = []
    for items, parent, base in ((news_items(), NEWS_POST, 'news'), (award_items(), AWARDS_POST, 'awards')):
        for it in items:
            slug = slugify(it['title'])
            pid = ensure_page(parent, slug, it['title'].replace('&amp;', '&'))
            out.append((pid, '%s/%s' % (base, slug), sections_for(it, items)))
    return out
