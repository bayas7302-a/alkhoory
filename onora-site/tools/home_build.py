#!/usr/bin/env python3
"""Generate ONORA Home page section payloads -> sections/home-NN-*.json"""
import json, os, sys

PID = 25
BASE = "https://soharon.co.uk/onora/"
OUT = "/home/user/alkhoory/onora-site/sections"
UP = "https://soharon.co.uk/onora/wp-content/uploads/2026/10/"
GOLD = "linear-gradient(90deg, #A5740B 0%, #DEAF57 25%, #F1C96A 50%, #BE8826 75%, #A5740B 100%)"
MEDIA = json.load(open("/home/user/alkhoory/onora-site/tools/media.json"))


def mid(name):
    return MEDIA[name]["id"]


def link(url, blank=False):
    return {"destination": url, "isTargetBlank": blank, "tag": "a"}


class C:
    def __init__(self):
        self.x, self.cfg, self.st, self.cl = [], {}, {}, {}

    def open(self, tag, cid, style=None, classes=None, cfg=None):
        self.x.append(f'<{tag} configuration-id="{cid}">')
        self._set(cid, style, classes, cfg)

    def close(self, tag):
        self.x.append(f'</{tag}>')

    def leaf(self, tag, cid, style=None, classes=None, cfg=None):
        self.x.append(f'<{tag} configuration-id="{cid}"></{tag}>')
        self._set(cid, style, classes, cfg)

    def _set(self, cid, style, classes, cfg):
        if style:
            self.st[cid] = style
        if classes:
            self.cl[cid] = classes
        if cfg:
            self.cfg[cid] = cfg

    def p(self, cid, text, style=None, classes=None, url=None):
        self.leaf("e-paragraph", cid, style, classes, {"paragraph": text, **({"link": link(url)} if url else {})})

    def h(self, cid, text, tag="h2", style=None, classes=None):
        self.leaf("e-heading", cid, style, classes, {"tag": tag, "title": text})

    def img(self, cid, file, style=None, url=None):
        self.leaf("e-image", cid, style, None, {"image": {"src": {"id": mid(file)}, "size": "full"}, **({"link": link(url)} if url else {})})

    def btn(self, cid, text, url, cls, style=None):
        self.leaf("e-button", cid, style, [cls], {"text": text + "&nbsp;&nbsp;→", "link": link(url)})

    def eyebrow(self, cid, text, color=None, center=False):
        self.open("e-flexbox", cid, None, ["on-eyebrow-row"])
        self.leaf("e-div-block", cid + " rule", None, ["on-rule"])
        self.p(cid + " text", text, f"color: {color};" if color else None, ["on-eyebrow"])
        if center:
            self.leaf("e-div-block", cid + " rule 2", None, ["on-rule"])
        self.close("e-flexbox")

    def payload(self):
        return {"post_id": PID, "xml_structure": "".join(self.x), "element_config": self.cfg, "style": self.st, "classes": self.cl}


def ring(c, cid, style):
    c.leaf("e-div-block", cid, "position: absolute; padding: 0px; border-radius: 50%; border-width: 1.4px; border-style: solid; "
           "border-color: rgba(201,150,46,0.5); background: transparent; " + style)


def head_split(c, pre, eyebrow, title, body, eye_color=None, title_color=None, body_color=None, button=None, left_w=700):
    """Left eyebrow + H2, right paragraph (+ optional button)."""
    c.open("e-flexbox", f"{pre} head", "padding: 0px; justify-content: space-between; align-items: flex-end; gap: 40px;"
           " @media(--tablet){ flex-direction: column; align-items: flex-start; gap: 24px; } @media(--mobile){ flex-direction: column; align-items: flex-start; gap: 20px; }")
    c.open("e-flexbox", f"{pre} head left", f"flex-direction: column; gap: 22px; padding: 0px; width: {left_w}px; max-width: 100%;")
    c.eyebrow(f"{pre} eyebrow", eyebrow, eye_color)
    c.h(f"{pre} title", title, "h2", f"color: {title_color};" if title_color else None, ["on-h2"])
    c.close("e-flexbox")
    c.open("e-flexbox", f"{pre} head right", "flex-direction: column; gap: 24px; padding: 0px; width: 420px; max-width: 100%; align-items: flex-start;")
    c.p(f"{pre} intro", body, f"color: {body_color};" if body_color else None, ["on-body"])
    if button:
        c.btn(f"{pre} button", *button)
    c.close("e-flexbox")
    c.close("e-flexbox")


# ---------------------------------------------------------------- 01 Hero
def hero():
    c = C()
    c.open("e-flexbox", "Hero",
           "flex-direction: column; justify-content: space-between; align-items: flex-start; min-height: 900px; padding: 150px 100px 0px 100px; gap: 64px; overflow: hidden;"
           f" background-image: linear-gradient(90deg, rgba(7,31,68,0.97) 0%, rgba(11,47,98,0.82) 50%, rgba(11,47,98,0.2) 100%), url({UP}home-hero-bg.jpg);"
           " background-size: cover; background-position: center center; background-repeat: no-repeat; background-color: #071F44;"
           " @media(--tablet){ min-height: 0px; padding: 120px 40px 0px 40px; } @media(--mobile){ min-height: 0px; padding: 72px 20px 0px 20px; gap: 48px; }")
    ring(c, "Hero ring large", "left: 860px; top: 40px; width: 720px; height: 720px; min-width: 720px; @media(--tablet){ display: none; } @media(--mobile){ display: none; }")
    ring(c, "Hero ring small", "left: 940px; top: 120px; width: 560px; height: 560px; min-width: 560px; @media(--tablet){ display: none; } @media(--mobile){ display: none; }")
    c.open("e-flexbox", "Hero content", "flex-direction: column; gap: 28px; padding: 0px; width: 760px; max-width: 100%; align-items: flex-start; z-index: 2;")
    c.eyebrow("Hero eyebrow", "Hotel · Restaurant · Café supplies", "var(--on-gold-soft)")
    c.h("Hero title", "The art of hospitality, served beautifully.", "h1",
        "font-size: 104px; line-height: 100px; letter-spacing: -0.3px; @media(--tablet){ font-size: 76px; line-height: 76px; } @media(--mobile){ font-size: 50px; line-height: 52px; }",
        ["on-h1"])
    c.p("Hero lead", "From refined chinaware and precision-crafted cutlery to buffet, bar and service equipment — ONORA brings world-class hospitality products to every table you set.",
        "color: rgba(255,255,255,0.82); font-weight: 400; max-width: 560px; @media(--mobile){ font-size: 16px; line-height: 27px; }", ["on-lead"])
    c.open("e-flexbox", "Hero buttons", "padding: 0px; gap: 16px; width: auto; flex-wrap: wrap; @media(--mobile){ width: 100%; }")
    c.btn("Hero explore", "Explore products", BASE + "products/", "on-btn-gold")
    c.btn("Hero brands", "Our brands", BASE + "brands/", "on-btn-outline-light", "border-color: rgba(255,255,255,0.5);")
    c.close("e-flexbox")
    c.close("e-flexbox")
    # floating collection card
    c.open("e-flexbox", "Hero card",
           "position: absolute; right: 100px; top: 300px; width: auto; padding: 16px; gap: 18px; align-items: center; z-index: 2;"
           " background-color: rgba(255,255,255,0.1); border-width: 1px; border-style: solid; border-color: rgba(255,255,255,0.22); backdrop-filter: blur(10px);"
           " @media(--tablet){ display: none; } @media(--mobile){ display: none; }",
           cfg={"link": link(BASE + "products/")})
    c.img("Hero card image", "home-hero-card.jpg", "width: 96px; height: 96px; min-width: 96px; object-fit: cover;")
    c.open("e-flexbox", "Hero card text", "flex-direction: column; gap: 6px; padding: 0px; width: 220px; align-items: flex-start;")
    c.p("Hero card label", "New season", "font-family: var(--on-sans); font-weight: 600; font-size: 11px; letter-spacing: 2.4px; text-transform: uppercase; color: var(--on-gold-soft);")
    c.p("Hero card title", "Contemporary porcelain collection", "font-family: var(--on-serif); font-weight: 600; font-size: 24px; line-height: 26px; letter-spacing: -0.3px; color: #FFFFFF;")
    c.p("Hero card link", "View collection&nbsp;&nbsp;→", "font-family: var(--on-sans); font-weight: 600; font-size: 12px; letter-spacing: 0.6px; color: rgba(255,255,255,0.8);")
    c.close("e-flexbox")
    c.close("e-flexbox")
    # stats bar
    c.open("e-flexbox", "Stats bar",
           "background-color: #FFFFFF; padding: 34px 48px; justify-content: space-between; align-items: flex-start; gap: 20px; z-index: 2; max-width: 1240px;"
           " @media(--tablet){ padding: 28px 32px; } @media(--mobile){ flex-wrap: wrap; padding: 24px 20px; gap: 24px; }")
    stats = [("12", "Product collections"), ("19+", "Global brand partners"), ("HORECA", "Hotels · Restaurants · Cafés"), ("Dubai", "Serving the UAE")]
    for i, (n, l) in enumerate(stats):
        k = f"Stat {i+1}"
        c.open("e-flexbox", k, "padding: 0px; width: auto; gap: 20px; align-items: center; @media(--mobile){ width: calc(50% - 12px); }")
        if i:
            c.leaf("e-div-block", f"{k} divider", "width: 1px; min-width: 1px; height: 56px; padding: 0px; background-color: #E6E0D6; @media(--mobile){ display: none; }")
        c.open("e-flexbox", f"{k} text", "flex-direction: column; gap: 4px; padding: 0px; width: auto;")
        c.p(f"{k} number", n, "font-family: var(--on-serif); font-weight: 600; font-size: 44px; line-height: 44px; letter-spacing: -0.3px; color: var(--on-navy);"
            " @media(--tablet){ font-size: 36px; line-height: 38px; } @media(--mobile){ font-size: 30px; line-height: 32px; }")
        c.p(f"{k} label", l, "font-family: var(--on-sans); font-weight: 500; font-size: 12px; letter-spacing: 1.2px; text-transform: uppercase; color: var(--on-muted);"
            " @media(--tablet){ font-size: 10px; letter-spacing: 0.8px; } @media(--mobile){ font-size: 10px; letter-spacing: 0.6px; line-height: 15px; }")
        c.close("e-flexbox")
        c.close("e-flexbox")
    c.close("e-flexbox")
    c.close("e-flexbox")
    return c


# ---------------------------------------------------------------- 02 Intro
def intro():
    c = C()
    c.open("e-flexbox", "Intro", "background-color: #FFFFFF; overflow: hidden;", ["on-section"])
    c.open("e-flexbox", "Intro row", "padding: 0px; justify-content: space-between; align-items: center; gap: 40px;"
           " @media(--tablet){ flex-direction: column; align-items: flex-start; gap: 64px; } @media(--mobile){ flex-direction: column; gap: 48px; }")
    c.open("e-flexbox", "Intro text", "flex-direction: column; gap: 28px; padding: 0px; width: 560px; max-width: 100%; align-items: flex-start;")
    c.eyebrow("Intro eyebrow", "Everyday luxury, delivered")
    c.h("Intro title", "Where elegant design meets uncompromising performance.", "h2", None, ["on-h2"])
    c.p("Intro lead", "ONORA Hotel Supplies partners with hotels, restaurants and cafés to source tableware and service equipment that looks exceptional and stands up to the demands of daily hospitality.",
        "max-width: 540px;", ["on-lead"])
    c.p("Intro body", "We curate collections from trusted global manufacturers — so every plate, glass and trolley reflects your standards, your style and your guests’ expectations.",
        "max-width: 540px;", ["on-body"])
    c.open("e-flexbox", "Intro actions", "padding: 0px; width: auto; gap: 28px; align-items: center; flex-wrap: wrap; @media(--mobile){ gap: 20px; }")
    c.btn("Intro about", "About ONORA", BASE + "about-us/", "on-btn-navy")
    c.open("e-flexbox", "Intro signature", "flex-direction: column; gap: 4px; padding: 0px; width: auto;")
    c.p("Intro sig name", "Jason Dsouza", "font-family: var(--on-serif); font-weight: 600; font-size: 24px; line-height: 26px; letter-spacing: -0.3px; color: var(--on-navy);")
    c.p("Intro sig role", "CEO, ONORA Hotel Supplies", "font-family: var(--on-sans); font-weight: 500; font-size: 12px; letter-spacing: 0.6px; color: var(--on-muted);")
    c.close("e-flexbox")
    c.close("e-flexbox")
    c.close("e-flexbox")
    # image composition
    c.open("e-div-block", "Intro composition", "position: relative; width: 620px; min-width: 620px; height: 680px; padding: 0px;"
           " @media(--tablet){ align-self: center; } @media(--mobile){ width: 100%; min-width: 0px; height: 440px; }")
    ring(c, "Intro ring", "left: 300px; top: -40px; width: 360px; height: 360px; min-width: 360px; @media(--mobile){ display: none; }")
    c.img("Intro main image", "home-intro-glass.jpg", "position: absolute; left: 200px; top: 0px; width: 420px; height: 560px; object-fit: cover;"
          " @media(--mobile){ left: 0px; width: 100%; height: 400px; }")
    c.img("Intro detail image", "home-hero-card.jpg", "position: absolute; left: 0px; top: 320px; width: 300px; height: 340px; object-fit: cover;"
          " border-width: 10px; border-style: solid; border-color: #FFFFFF; @media(--mobile){ display: none; }")
    c.open("e-flexbox", "Intro badge", "position: absolute; left: 470px; top: 520px; width: auto; min-width: 232px; flex-direction: column; gap: 4px; padding: 20px 26px; background-color: var(--on-navy);"
           " @media(--mobile){ left: auto; right: 0px; top: auto; bottom: 0px; padding: 16px 20px; }")
    c.p("Intro badge number", "12", "font-family: var(--on-serif); font-weight: 600; font-size: 44px; line-height: 44px; letter-spacing: -0.3px; color: #FFFFFF;")
    c.p("Intro badge label", "Curated collections", "font-family: var(--on-sans); font-weight: 600; font-size: 11px; letter-spacing: 1.8px; text-transform: uppercase; color: var(--on-gold-soft);")
    c.close("e-flexbox")
    c.close("e-div-block")
    c.close("e-flexbox")
    c.close("e-flexbox")
    return c


# ---------------------------------------------------------------- 03 Collections
TILES = [("Chinaware", "chinaware"), ("Cutlery", "cutlery"), ("Glassware", "glassware"), ("Polycarbonate Drinkware", "polycarbonate"),
         ("Melamine", "melamine"), ("Kitchen", "kitchen"), ("Buffet", "buffet"), ("Bar Items", "bar-items"),
         ("FOH Trolleys", "foh-trolleys"), ("Accessories", "accessories"), ("Table Lamps", "table-lamps"), ("Indian Tableware", "indian-tableware")]


def collections():
    c = C()
    c.open("e-flexbox", "Collections", "background-color: var(--on-cream); align-items: center; gap: 64px; @media(--mobile){ gap: 40px; }", ["on-section"])
    c.open("e-flexbox", "Collections head", "flex-direction: column; align-items: center; gap: 22px; padding: 0px; max-width: 820px;")
    c.eyebrow("Collections eyebrow", "Our collections", center=True)
    c.h("Collections title", "Everything your tables need.", "h2", "text-align: center;", ["on-h2"])
    c.p("Collections intro", "Twelve curated collections covering front of house, back of house and every service moment in between.", "text-align: center; max-width: 640px;", ["on-body"])
    c.close("e-flexbox")
    c.open("e-grid", "Collection grid", "padding: 0px; gap: 20px; grid-template-columns: repeat(4, 1fr); grid-template-rows: repeat(3, auto);"
           " @media(--tablet){ grid-template-columns: repeat(3, 1fr); grid-template-rows: repeat(4, auto); } @media(--mobile){ grid-template-columns: repeat(2, 1fr); grid-template-rows: repeat(6, auto); gap: 12px; }")
    for i, (name, slug) in enumerate(TILES):
        k = f"Tile {name}"
        c.open("e-flexbox", k,
               "flex-direction: column; justify-content: space-between; align-items: flex-start; height: 380px; padding: 26px 28px 27px 28px; overflow: hidden;"
               f" background-image: linear-gradient(180deg, rgba(7,31,68,0) 35%, rgba(7,31,68,0.92) 100%), url({UP}home-tile-{slug}.jpg);"
               " background-size: cover; background-position: center center; background-repeat: no-repeat; transition: opacity 0.3s; &:hover{ opacity: 0.92; }"
               " @media(--tablet){ height: 340px; } @media(--mobile){ height: 230px; padding: 14px 14px 16px 14px; }",
               cfg={"link": link(BASE + "products/#" + ("polycarbonate-drinkware" if slug == "polycarbonate" else slug))})
        c.p(f"{k} number", f"{i+1:02d}", "font-family: var(--on-sans); font-weight: 600; font-size: 12px; letter-spacing: 2px; color: var(--on-gold-soft);")
        c.open("e-flexbox", f"{k} text", "flex-direction: column; gap: 10px; padding: 0px; @media(--mobile){ gap: 6px; }")
        c.p(f"{k} title", name, "font-family: var(--on-serif); font-weight: 600; font-size: 32px; line-height: 34px; letter-spacing: -0.3px; color: #FFFFFF;"
            " @media(--mobile){ font-size: 22px; line-height: 24px; }")
        c.p(f"{k} link", "Explore collection&nbsp;&nbsp;→", "font-family: var(--on-sans); font-weight: 600; font-size: 11px; letter-spacing: 1.6px; text-transform: uppercase; color: rgba(255,255,255,0.85);"
            " @media(--mobile){ font-size: 9px; letter-spacing: 1px; }")
        c.close("e-flexbox")
        c.close("e-flexbox")
    c.close("e-grid")
    c.btn("Collections all", "View all products", BASE + "products/", "on-btn-navy")
    c.close("e-flexbox")
    return c


# ---------------------------------------------------------------- 04 Signature collection
def signature():
    c = C()
    c.open("e-flexbox", "Signature", "background-color: #FFFFFF;", ["on-section"])
    c.open("e-flexbox", "Signature split", "padding: 0px; gap: 0px; align-items: stretch; @media(--tablet){ flex-direction: column; } @media(--mobile){ flex-direction: column; }")
    c.open("e-flexbox", "Signature image",
           f"width: 700px; min-width: 700px; min-height: 640px; padding: 28px; align-items: flex-start; background-image: url({UP}home-signature-buffet.jpg);"
           " background-size: cover; background-position: center center; background-repeat: no-repeat;"
           " @media(--tablet){ width: 100%; min-width: 0px; min-height: 520px; } @media(--mobile){ width: 100%; min-width: 0px; min-height: 300px; padding: 16px; }")
    c.p("Signature tag", "ONORA Collection Exclusive", f"padding: 12px 18px; background-image: {GOLD}; font-family: var(--on-sans); font-weight: 600; font-size: 11px; letter-spacing: 2px; text-transform: uppercase; color: var(--on-navy-deep);"
        " @media(--mobile){ font-size: 10px; letter-spacing: 1.4px; padding: 10px 14px; }")
    c.close("e-flexbox")
    c.open("e-flexbox", "Signature panel",
           "flex-direction: column; justify-content: center; align-items: flex-start; flex-grow: 1; gap: 28px; padding: 64px; background-color: var(--on-navy); overflow: hidden;"
           " @media(--tablet){ padding: 56px 48px; } @media(--mobile){ padding: 40px 24px; gap: 24px; }")
    ring(c, "Signature ring", "left: 300px; top: -140px; width: 420px; height: 420px; min-width: 420px; border-color: rgba(226,187,108,0.35);"
         " @media(--tablet){ left: auto; right: -120px; } @media(--mobile){ left: auto; right: -200px; }")
    c.eyebrow("Signature eyebrow", "Buffet &amp; service", "var(--on-gold-soft)")
    c.h("Signature title", "Designed to impress, built to perform.", "h2",
        "font-size: 52px; line-height: 54px; color: #FFFFFF; max-width: 410px; @media(--tablet){ font-size: 44px; line-height: 48px; } @media(--mobile){ font-size: 36px; line-height: 40px; }", ["on-h2"])
    c.p("Signature body", "Modern elegance with flawless function. Our buffet and service solutions combine beauty and durability for hotels, banquets and luxury dining.",
        "color: rgba(255,255,255,0.78); max-width: 410px;", ["on-body"])
    c.open("e-flexbox", "Signature list", "flex-direction: column; gap: 16px; padding: 0px;")
    for i, t in enumerate(["Display risers, chafers &amp; platters", "Beverage &amp; dessert service", "Built for heavy daily use"]):
        k = f"Signature item {i+1}"
        c.open("e-flexbox", k, "padding: 0px; gap: 14px; align-items: center;")
        c.leaf("e-div-block", f"{k} dash", f"width: 18px; min-width: 18px; height: 2px; padding: 0px; background-image: {GOLD};")
        c.p(f"{k} text", t, "font-family: var(--on-sans); font-weight: 500; font-size: 15px; color: #FFFFFF;")
        c.close("e-flexbox")
    c.close("e-flexbox")
    c.btn("Signature button", "Explore buffet", BASE + "products/#buffet", "on-btn-gold")
    c.close("e-flexbox")
    c.close("e-flexbox")
    c.close("e-flexbox")
    return c


# ---------------------------------------------------------------- 05 Material promise
def material():
    c = C()
    c.open("e-flexbox", "Material promise", "background-color: var(--on-cream); gap: 64px; @media(--mobile){ gap: 40px; }", ["on-section"])
    head_split(c, "Material", "The ONORA material promise", "Crafted from premium-grade materials.",
               "Every product we supply is selected for long-term performance in demanding hospitality environments — and a finish that reflects lasting value.")
    c.open("e-grid", "Promise cards", "padding: 0px; gap: 20px; grid-template-columns: repeat(4, 1fr); grid-template-rows: repeat(1, auto);"
           " @media(--tablet){ grid-template-columns: repeat(2, 1fr); grid-template-rows: repeat(2, auto); } @media(--mobile){ grid-template-columns: repeat(1, 1fr); grid-template-rows: repeat(4, auto); }")
    cards = [("Engineered for heavy-duty use", "Commercial-grade construction that stands up to busy service, dishwashers and daily handling."),
             ("No compromises on quality", "Only proven manufacturers and materials, checked against hospitality standards."),
             ("Precision-crafted finishes", "Glazes, polishes and edges that look refined on the first day and the thousandth."),
             ("Trusted by professionals", "Chosen by hoteliers, chefs and F&amp;B teams who need consistency across every outlet.")]
    for i, (t, b) in enumerate(cards):
        k = f"Promise {i+1}"
        c.open("e-flexbox", k, "flex-direction: column; gap: 20px; padding: 36px; background-color: #FFFFFF; border-width: 1px; border-style: solid; border-color: #E6E0D6; align-items: flex-start;"
               " @media(--mobile){ padding: 28px; }")
        c.open("e-flexbox", f"{k} ring", "width: 56px; min-width: 56px; height: 56px; padding: 0px; border-radius: 50%; border-width: 1.4px; border-style: solid; border-color: #C9962E; justify-content: center; align-items: center;")
        c.p(f"{k} number", f"{i+1:02d}", "font-family: var(--on-serif); font-weight: 600; font-size: 22px; line-height: 22px; letter-spacing: -0.3px; color: var(--on-navy);")
        c.close("e-flexbox")
        c.h(f"{k} title", t, "h3", "line-height: 30px;", ["on-h3"])
        c.p(f"{k} body", b, "font-size: 15px; line-height: 26px;", ["on-body"])
        c.close("e-flexbox")
    c.close("e-grid")
    c.close("e-flexbox")
    return c


# ---------------------------------------------------------------- 06 Brands strip
BRANDS = ["lusini", "stylepoint", "eagle", "tuppa", "vidivi", "was", "gramonty", "trolley-cart", "puri", "belo-inox"]


def brands():
    c = C()
    c.open("e-flexbox", "Brands strip", "background-color: var(--on-navy); gap: 64px; padding-top: 110px; padding-bottom: 110px; overflow: hidden;"
           " @media(--tablet){ padding-top: 88px; padding-bottom: 88px; } @media(--mobile){ padding-top: 64px; padding-bottom: 64px; gap: 40px; }", ["on-section"])
    ring(c, "Brands ring", "left: 1040px; top: -220px; width: 640px; height: 640px; min-width: 640px; border-color: rgba(226,187,108,0.35);"
         " @media(--tablet){ left: auto; right: -320px; } @media(--mobile){ left: auto; right: -420px; }")
    head_split(c, "Brands", "Trusted global brands", "Any global brand you need, at your fingertips.",
               "We represent and source from respected manufacturers across Europe and beyond.",
               eye_color="var(--on-gold-soft)", title_color="#FFFFFF", body_color="rgba(255,255,255,0.75)",
               button=("View all brands", BASE + "brands/", "on-btn-outline-light", "border-color: rgba(255,255,255,0.5);"), left_w=650)
    c.open("e-grid", "Logo wall", "padding: 0px; gap: 0px; grid-template-columns: repeat(5, 1fr); grid-template-rows: repeat(2, auto);"
           " @media(--mobile){ grid-template-columns: repeat(2, 1fr); grid-template-rows: repeat(5, auto); }")
    for i, b in enumerate(BRANDS):
        k = f"Logo {b}"
        c.open("e-flexbox", k, "height: 130px; padding: 0px; justify-content: center; align-items: center; border-width: 1px; border-style: solid; border-color: rgba(255,255,255,0.12);"
               + (" background-color: #FFFFFF;" if b == "belo-inox" else "") + " @media(--mobile){ height: 100px; }", cfg={"link": link(BASE + "brands/")})
        c.img(f"{k} image", f"home-brand-{b}.png", "width: 170px; height: 70px; max-width: 80%; object-fit: contain; @media(--mobile){ height: 56px; }")
        c.close("e-flexbox")
    c.close("e-grid")
    c.close("e-flexbox")
    return c


# ---------------------------------------------------------------- 07 Who we serve
SECTORS = [("Hotels &amp; Resorts", "hotels", "All-day dining, banquets, room service and pool."),
           ("Restaurants", "restaurants", "Fine dining to casual — tableware with character."),
           ("Cafés &amp; Lounges", "cafes", "Durable, beautiful pieces for high turnover."),
           ("Banquets &amp; Catering", "banquets", "Buffet, display and service for every event size.")]


def serve():
    c = C()
    c.open("e-flexbox", "Who we serve", "background-color: #FFFFFF; gap: 64px; @media(--mobile){ gap: 40px; }", ["on-section"])
    head_split(c, "Serve", "Who we serve", "Made for every kind of hospitality.",
               "Whether you run a resort, a fine-dining room or a neighbourhood café, we tailor the range to your service style.")
    c.open("e-grid", "Sector cards", "padding: 0px; gap: 20px; grid-template-columns: repeat(4, 1fr); grid-template-rows: repeat(1, auto);"
           " @media(--tablet){ grid-template-columns: repeat(2, 1fr); grid-template-rows: repeat(2, auto); gap: 40px; } @media(--mobile){ grid-template-columns: repeat(1, 1fr); grid-template-rows: repeat(4, auto); gap: 36px; }")
    for t, slug, d in SECTORS:
        k = f"Sector {slug}"
        c.open("e-flexbox", k, "flex-direction: column; gap: 18px; padding: 0px; align-items: stretch;", cfg={"link": link(BASE + "products/")})
        c.img(f"{k} image", f"home-serve-{slug}.jpg", "width: 100%; height: 360px; object-fit: cover; @media(--mobile){ height: 260px; }")
        c.open("e-flexbox", f"{k} row", "padding: 0px; justify-content: space-between; align-items: center; gap: 12px;")
        c.p(f"{k} title", t, "font-family: var(--on-serif); font-weight: 600; font-size: 28px; line-height: 30px; letter-spacing: -0.3px; color: var(--on-navy); @media(--tablet){ font-size: 26px; }")
        c.p(f"{k} arrow", "→", "font-family: var(--on-sans); font-weight: 500; font-size: 18px; color: var(--on-gold);")
        c.close("e-flexbox")
        c.p(f"{k} desc", d, None, ["on-small"])
        c.close("e-flexbox")
    c.close("e-grid")
    c.close("e-flexbox")
    return c


# ---------------------------------------------------------------- 08 CTA band
def cta():
    c = C()
    c.open("e-flexbox", "CTA band",
           "flex-direction: column; justify-content: center; align-items: center; min-height: 560px; padding: 107px 100px; overflow: hidden;"
           f" background-image: linear-gradient(0deg, rgba(7,31,68,0.84) 0%, rgba(7,31,68,0.84) 100%), url({UP}home-cta-bg.jpg);"
           " background-size: cover; background-position: center center; background-repeat: no-repeat; background-color: #071F44;"
           " @media(--tablet){ padding: 96px 40px; min-height: 0px; } @media(--mobile){ padding: 72px 20px; min-height: 0px; }")
    ring(c, "CTA ring large", "left: 50%; top: -100px; width: 760px; height: 760px; min-width: 760px; transform: translateX(-50%); border-color: rgba(201,150,46,0.4);")
    ring(c, "CTA ring small", "left: 50%; top: 0px; width: 560px; height: 560px; min-width: 560px; transform: translateX(-50%); border-color: rgba(201,150,46,0.4);")
    c.open("e-flexbox", "CTA content", "flex-direction: column; align-items: center; gap: 26px; padding: 0px; max-width: 900px; z-index: 2;")
    c.eyebrow("CTA eyebrow", "Let's work together", "var(--on-gold-soft)", center=True)
    c.h("CTA title", "Ready to elevate your guest experience?", "h2",
        "font-size: 68px; line-height: 70px; color: #FFFFFF; text-align: center; max-width: 860px; @media(--tablet){ font-size: 52px; line-height: 56px; } @media(--mobile){ font-size: 38px; line-height: 42px; }", ["on-h2"])
    c.p("CTA body", "Tell us about your venue and we will curate the right products, brands and quantities — from a single outlet to a full hotel opening.",
        "font-size: 17px; line-height: 29px; color: rgba(255,255,255,0.8); text-align: center; max-width: 640px; @media(--mobile){ font-size: 15px; line-height: 26px; }", ["on-body"])
    c.open("e-flexbox", "CTA buttons", "padding: 0px; width: auto; gap: 16px; justify-content: center; flex-wrap: wrap; @media(--mobile){ flex-direction: column; align-items: stretch; width: 100%; }")
    c.btn("CTA quote", "Request a quote", BASE + "contact-us/", "on-btn-gold")
    c.btn("CTA call", "Call +971 50 203 8005", "tel:+971502038005", "on-btn-outline-light", "border-color: rgba(255,255,255,0.5);")
    c.close("e-flexbox")
    c.close("e-flexbox")
    c.close("e-flexbox")
    return c


SECTIONS = [("01-hero", hero), ("02-intro", intro), ("03-collections", collections), ("04-signature", signature),
            ("05-material-promise", material), ("06-brands-strip", brands), ("07-who-we-serve", serve), ("08-cta-band", cta)]

if __name__ == "__main__":
    for name, fn in SECTIONS:
        p = fn().payload()
        f = os.path.join(OUT, f"home-{name}.json")
        json.dump(p, open(f, "w"), indent=1, ensure_ascii=False)
        print(f)
