#!/usr/bin/env python3
"""Generate sections/about-NN-*.json build-composition payloads for the ONORA About page (post 26)."""
import json, os

POST = 26
OUT = "/home/user/alkhoory/onora-site/sections"
BASE = "https://soharon.co.uk/onora/"
UP = "https://soharon.co.uk/onora/wp-content/uploads/2026/10/"
GOLD = "linear-gradient(90deg, #A5740B 0%, #DEAF57 25%, #F1C96A 50%, #BE8826 75%, #A5740B 100%)"
IMG = {"hero": 48, "main": 52, "inset": 51, "buffet": 50, "bar": 49, "cta": 47}
TAB, MOB = "@media(--tablet)", "@media(--mobile)"

SERIF = "font-family: var(--on-serif); font-weight: 600; letter-spacing: -0.3px;"
SANS = "font-family: var(--on-sans);"


def link(url, blank=False):
    return {"destination": url, "isTargetBlank": blank, "tag": "a"}


class B:
    def __init__(self):
        self.x, self.cfg, self.st, self.cl = [], {}, {}, {}

    def open(self, el, cid, style="", classes=None, **cfg):
        self.x.append(f'<{el} configuration-id="{cid}">')
        self._set(cid, style, classes, cfg)

    def close(self, el):
        self.x.append(f"</{el}>")

    def leaf(self, el, cid, style="", classes=None, **cfg):
        self.x.append(f'<{el} configuration-id="{cid}"></{el}>')
        self._set(cid, style, classes, cfg)

    def _set(self, cid, style, classes, cfg):
        assert cid not in self.st and cid not in self.cfg, cid
        if style:
            self.st[cid] = style
        if classes:
            self.cl[cid] = classes
        if cfg:
            self.cfg[cid] = cfg

    def p(self, cid, text, style, **kw):
        self.leaf("e-paragraph", cid, style, paragraph=text, **kw)

    def h(self, cid, text, tag, style, classes=None):
        self.leaf("e-heading", cid, style, classes, title=text, tag=tag)

    def payload(self):
        return {"post_id": POST, "xml_structure": "".join(self.x),
                "element_config": self.cfg, "style": self.st, "classes": self.cl}


def rule(b, cid, w=32):
    b.leaf("e-div-block", cid, f"width: {w}px; min-width: {w}px; height: 2px; padding: 0px; background-image: {GOLD};", ["on-rule"])


def eyebrow(b, pre, text, color="#C9962E", both=False, center=False):
    b.open("e-flexbox", f"{pre} label row", "padding: 0px; width: auto; gap: 14px; align-items: center;" + (" justify-content: center;" if center else ""), ["on-eyebrow-row"])
    rule(b, f"{pre} rule")
    b.p(f"{pre} label", text, f"{SANS} font-weight: 600; font-size: 12px; letter-spacing: 3px; text-transform: uppercase; color: {color}; line-height: 1.2;", classes=["on-eyebrow"])
    if both:
        rule(b, f"{pre} rule 2")
    b.close("e-flexbox")


def ring(b, cid, size, alpha, pos, width="1.2px", extra=""):
    b.leaf("e-div-block", cid, f"position: absolute; {pos} width: {size}px; height: {size}px; min-width: {size}px; padding: 0px; border-radius: 50%;"
           f" border-width: {width}; border-style: solid; border-color: rgba(201,150,46,{alpha}); z-index: 0; {extra}")


H2 = (f"{SERIF} font-size: 60px; line-height: 62px; color: var(--on-navy);"
      f" {TAB}{{ font-size: 46px; line-height: 50px; }} {MOB}{{ font-size: 36px; line-height: 40px; }}")
H2_56 = (f"{SERIF} font-size: 56px; line-height: 58px; color: var(--on-navy);"
         f" {TAB}{{ font-size: 46px; line-height: 50px; }} {MOB}{{ font-size: 36px; line-height: 40px; }}")
BODY16 = f"{SANS} font-weight: 400; font-size: 16px; line-height: 29px; color: var(--on-muted);"
SECTION = (f"flex-direction: column; padding: 120px 100px; gap: 64px; position: relative; overflow: hidden;"
           f" {TAB}{{ padding: 96px 40px; gap: 48px; }} {MOB}{{ padding: 72px 20px; gap: 40px; }}")
GRAD_TEXT = f"background-image: {GOLD}; background-clip: text; color: transparent;"


def hero():
    b = B()
    b.open("e-flexbox", "About hero", f"min-height: 560px; padding: 103px 100px 80px 100px; position: relative; overflow: hidden; flex-direction: column; align-items: flex-start;"
           f" background-color: #071F44; background-image: linear-gradient(90deg, rgba(7,31,68,0.96) 0%, rgba(11,47,98,0.78) 55%, rgba(11,47,98,0.3) 100%), url({UP}about-hero-bg.jpg);"
           f" background-size: cover; background-position: center center; background-repeat: no-repeat;"
           f" {TAB}{{ padding: 88px 40px 72px 40px; min-height: 480px; }} {MOB}{{ padding: 64px 20px 56px 20px; min-height: 0px; }}", tag="section")
    ring(b, "Hero ring outer", 620, 0.45, "top: -120px; right: -160px;", extra=f"{MOB}{{ display: none; }}")
    ring(b, "Hero ring inner", 470, 0.25, "top: -45px; right: -85px;", width="1px", extra=f"{MOB}{{ display: none; }}")
    b.open("e-flexbox", "Hero content", "flex-direction: column; gap: 24px; padding: 0px; width: 780px; max-width: 100%; align-items: flex-start; z-index: 1;")
    b.p("Hero breadcrumb", "Home&nbsp;&nbsp;&nbsp;/&nbsp;&nbsp;&nbsp;About Us", f"{SANS} font-weight: 500; font-size: 12px; letter-spacing: 2px; text-transform: uppercase; color: rgba(255,255,255,0.6); line-height: 1.2;")
    b.h("Hero title", "Elevating hospitality, one table at a time.", "h1",
        f"{SERIF} font-size: 88px; line-height: 88px; color: #FFFFFF; {TAB}{{ font-size: 64px; line-height: 66px; }} {MOB}{{ font-size: 44px; line-height: 48px; }}")
    rule(b, "Hero rule", 90)
    b.p("Hero lead", "A Dubai-based supplier of premium hotel, restaurant and café essentials — bringing trusted international brands to the UAE hospitality industry.",
        f"{SANS} font-weight: 400; font-size: 17px; line-height: 30px; color: rgba(255,255,255,0.82); width: 560px; max-width: 100%; {MOB}{{ font-size: 15px; line-height: 26px; }}")
    b.close("e-flexbox"); b.close("e-flexbox")
    return b


def story():
    b = B()
    b.open("e-flexbox", "Our story", SECTION + " background: #FFFFFF;", tag="section")
    b.open("e-flexbox", "Story row", f"padding: 0px; gap: 96px; align-items: center; overflow: hidden; {TAB}{{ flex-direction: column; gap: 64px; }} {MOB}{{ flex-direction: column; gap: 48px; }}")
    b.open("e-div-block", "Story composition", f"position: relative; width: 560px; min-width: 560px; height: 640px; padding: 0px;"
           f" {MOB}{{ width: 100%; min-width: 0px; height: 470px; }}")
    b.leaf("e-image", "Story main image", f"position: absolute; left: 0px; top: 0px; width: 470px; height: 580px; object-fit: cover; {MOB}{{ width: 86%; height: 400px; }}",
           image={"src": {"id": IMG["main"]}, "size": "full"})
    b.leaf("e-image", "Story inset image", f"position: absolute; left: 310px; top: 390px; width: 230px; height: 230px; object-fit: cover; border-width: 10px; border-style: solid; border-color: #FFFFFF;"
           f" {MOB}{{ left: auto; right: 0px; top: 300px; width: 154px; height: 154px; border-width: 8px; }}",
           image={"src": {"id": IMG["inset"]}, "size": "large"})
    ring(b, "Story ring", 220, 0.6, "left: -60px; top: -60px;")
    b.open("e-flexbox", "Story badge", f"position: absolute; left: 30px; top: 470px; width: auto; flex-direction: column; gap: 4px; padding: 22px 26px; background: var(--on-navy); align-items: flex-start;"
           f" {MOB}{{ left: 16px; top: 240px; padding: 16px 20px; }}")
    b.p("Badge title", "Est. in Dubai", f"{SERIF} font-size: 30px; line-height: 31px; color: #FFFFFF; {MOB}{{ font-size: 24px; line-height: 26px; }}")
    b.p("Badge sub", "Serving the UAE", f"{SANS} font-weight: 600; font-size: 11px; letter-spacing: 2px; text-transform: uppercase; color: var(--on-gold-soft); line-height: 1.2;")
    b.close("e-flexbox"); b.close("e-div-block")
    b.open("e-flexbox", "Story text", f"flex-direction: column; gap: 26px; padding: 0px; flex: 1 1 0px; min-width: 0px; align-items: flex-start; {TAB}{{ flex: 0 0 auto; }} {MOB}{{ flex: 0 0 auto; }}")
    eyebrow(b, "Story", "Who we are")
    b.h("Story title", "A partner behind the finest hospitality experiences.", "h2", H2_56)
    b.p("Story body 1", "ONORA Hotel Supplies was founded with a simple belief: every guest experience is shaped by the details on the table. From our office in Karama, Dubai, we source, curate and deliver premium tableware, glassware, buffet, bar and service equipment for hotels, restaurants, cafés and caterers across the UAE.", BODY16)
    b.p("Story body 2", "Working alongside world-renowned manufacturers from Europe and Asia, we help hospitality teams choose products that look beautiful, perform under pressure and stay within budget — from a single outlet refresh to a full pre-opening package.", BODY16)
    b.open("e-flexbox", "Signature", "padding: 24px 0px 0px 0px; gap: 20px; align-items: center; border-width: 1px 0px 0px 0px; border-style: solid; border-color: var(--on-line);")
    b.leaf("e-div-block", "Signature disc", "width: 56px; min-width: 56px; height: 56px; padding: 0px; border-radius: 50%; background: var(--on-cream); border-width: 1.5px; border-style: solid; border-color: #C9962E;")
    b.open("e-flexbox", "Signature name", "flex-direction: column; gap: 4px; padding: 0px; width: auto;")
    b.p("Signature person", "Jason Dsouza", f"{SERIF} font-size: 24px; line-height: 25px; color: var(--on-navy);")
    b.p("Signature role", "Chief Executive Officer", f"{SANS} font-weight: 600; font-size: 11px; letter-spacing: 2px; text-transform: uppercase; color: #C9962E; line-height: 1.2;")
    for _ in range(5):
        b.close("e-flexbox")
    return b


def stats():
    b = B()
    b.open("e-flexbox", "Stats", f"background: var(--on-cream); padding: 64px 100px; justify-content: space-between; align-items: flex-start; gap: 24px;"
           f" {TAB}{{ padding: 56px 40px; }} {MOB}{{ padding: 48px 20px; flex-wrap: wrap; gap: 32px; }}", tag="section")
    items = [("12", "Product collections"), ("19+", "International brands"), ("360°", "Pre-opening to replenishment"), ("UAE", "Nationwide delivery")]
    for i, (n, label) in enumerate(items, 1):
        if i > 1:
            b.leaf("e-div-block", f"Stat divider {i-1}", f"width: 1px; min-width: 1px; height: 80px; padding: 0px; background: var(--on-line); {MOB}{{ display: none; }}")
        b.open("e-flexbox", f"Stat {i}", f"flex-direction: column; gap: 8px; padding: 0px; width: auto; align-items: flex-start; {MOB}{{ width: calc(50% - 16px); }}")
        b.p(f"Stat {i} number", n, f"{SERIF} letter-spacing: 0px; font-size: 64px; line-height: 64px; {GRAD_TEXT} {TAB}{{ font-size: 52px; line-height: 54px; }} {MOB}{{ font-size: 44px; line-height: 46px; }}")
        b.p(f"Stat {i} label", label, f"{SANS} font-weight: 600; font-size: 12px; letter-spacing: 2px; text-transform: uppercase; color: var(--on-navy); line-height: 1.4; {TAB}{{ font-size: 11px; letter-spacing: 1.5px; }}")
        b.close("e-flexbox")
    b.close("e-flexbox")
    return b


def mission():
    b = B()
    b.open("e-flexbox", "Mission vision", SECTION + " background: #FFFFFF;", tag="section")
    b.open("e-flexbox", "MV head", "flex-direction: column; gap: 22px; padding: 0px; align-items: center;")
    eyebrow(b, "MV", "What drives us", both=True, center=True)
    b.h("MV title", "Purpose in every piece we supply.", "h2", H2 + " text-align: center; width: 820px; max-width: 100%;")
    b.close("e-flexbox")
    b.open("e-flexbox", "MV cards", f"padding: 0px; gap: 24px; align-items: stretch; {MOB}{{ flex-direction: column; }}")
    cards = [("Mission", "01 — Mission", "Our mission", "To equip hospitality businesses with premium, dependable products and genuinely helpful service — so every team can deliver memorable guest experiences with confidence.",
              "var(--on-navy)", "var(--on-gold-soft)", "#FFFFFF", "rgba(255,255,255,0.8)", 0.45),
             ("Vision", "02 — Vision", "Our vision", "To be the most trusted hospitality supply partner in the region, recognised for curated quality, design-led thinking and long-term relationships with the venues we serve.",
              "var(--on-cream)", "#C9962E", "var(--on-navy)", "var(--on-muted)", 0.5)]
    for key, num, title, body, bg, numc, tc, bc, ra in cards:
        c = f"Card {key}"
        b.open("e-flexbox", c, f"flex-direction: column; gap: 24px; padding: 64px 56px; flex: 1 1 0px; min-width: 0px; position: relative; overflow: hidden; background: {bg}; align-items: flex-start;"
               f" {TAB}{{ padding: 48px 36px; }} {MOB}{{ padding: 44px 28px; flex: 0 0 auto; width: 100%; }}")
        ring(b, f"{c} ring", 340, ra, "left: 360px; top: -120px;", extra=f"{TAB}{{ left: auto; right: -140px; }} {MOB}{{ left: auto; right: -160px; }}")
        b.p(f"{c} num", num, f"{SERIF} letter-spacing: 0px; font-size: 22px; line-height: 1.2; color: {numc}; z-index: 1;")
        b.h(f"{c} title", title, "h3", f"{SERIF} font-size: 48px; line-height: 50px; color: {tc}; z-index: 1; {MOB}{{ font-size: 38px; line-height: 42px; }}")
        rule(b, f"{c} rule", 64)
        b.p(f"{c} body", body, f"{SANS} font-weight: 400; font-size: 16px; line-height: 29px; color: {bc}; z-index: 1;")
        b.close("e-flexbox")
    b.close("e-flexbox"); b.close("e-flexbox")
    return b


def head_split(b, pre, eyebrow_text, title, right_text, dark=False, button=None):
    b.open("e-flexbox", f"{pre} head", f"padding: 0px; justify-content: space-between; align-items: flex-end; gap: 40px; z-index: 1;"
           f" {TAB}{{ flex-direction: column; align-items: flex-start; gap: 28px; }} {MOB}{{ flex-direction: column; align-items: flex-start; gap: 24px; }}")
    b.open("e-flexbox", f"{pre} head left", "flex-direction: column; gap: 22px; padding: 0px; width: 700px; max-width: 100%; align-items: flex-start;")
    eyebrow(b, pre, eyebrow_text, color="var(--on-gold-soft)" if dark else "#C9962E")
    b.h(f"{pre} title", title, "h2", H2.replace("color: var(--on-navy);", "color: #FFFFFF;") if dark else H2)
    b.close("e-flexbox")
    b.open("e-flexbox", f"{pre} head right", "flex-direction: column; gap: 24px; padding: 0px; width: 420px; max-width: 100%; align-items: flex-start;")
    b.p(f"{pre} intro", right_text, f"{SANS} font-weight: 400; font-size: 16px; line-height: 28px; color: {'rgba(255,255,255,0.75)' if dark else 'var(--on-muted)'};")
    if button:
        cid, text, url, cls = button
        b.leaf("e-button", cid, "", [cls], text=text, link=link(url))
    b.close("e-flexbox"); b.close("e-flexbox")


def portfolio():
    b = B()
    b.open("e-flexbox", "Portfolio", SECTION + " background: var(--on-cream);", tag="section")
    head_split(b, "Portfolio", "Our portfolio", "Everything a hospitality venue needs.",
               "Twelve curated collections spanning the full guest journey — from the kitchen pass to the poolside bar.",
               button=("Portfolio button", "View all products&nbsp;&nbsp;→", BASE + "products/", "on-btn-outline-navy"))
    b.open("e-flexbox", "Portfolio tiles", f"padding: 0px; gap: 24px; {TAB}{{ flex-wrap: wrap; }} {MOB}{{ flex-direction: column; gap: 20px; }}")
    tiles = [("Tabletop", "Chinaware · Cutlery · Glassware · Melamine", "about-story-inset.jpg"),
             ("Buffet &amp; Service", "Buffet · FOH Trolleys · Accessories · Kitchen", "about-portfolio-buffet.jpg"),
             ("Bar &amp; Ambience", "Bar Items · Drinkware · Table Lamps · Indian", "about-portfolio-bar.jpg")]
    for i, (title, sub, img) in enumerate(tiles, 1):
        t = f"Tile {i}"
        b.open("e-flexbox", t, f"position: relative; overflow: hidden; height: 520px; flex: 1 1 0px; min-width: 0px; padding: 0px;"
               f" background-color: #071F44; background-image: linear-gradient(180deg, rgba(7,31,68,0) 35%, rgba(7,31,68,0.92) 100%), url({UP}{img}); background-size: cover; background-position: center center; background-repeat: no-repeat;"
               f" {TAB}{{ flex: 1 1 calc(50% - 12px); height: 440px; }} {MOB}{{ flex: 0 0 auto; width: 100%; height: 400px; }}",
               link=link(BASE + "products/"))
        b.open("e-flexbox", f"{t} caption", "position: absolute; left: 32px; bottom: 32px; width: 340px; max-width: calc(100% - 64px); flex-direction: column; gap: 12px; padding: 0px; align-items: flex-start;")
        rule(b, f"{t} rule", 48)
        b.h(f"{t} title", title, "h3", f"{SERIF} font-size: 40px; line-height: 42px; color: #FFFFFF; {MOB}{{ font-size: 34px; line-height: 38px; }}")
        b.p(f"{t} sub", sub, f"{SANS} font-weight: 500; font-size: 13px; line-height: 22px; color: rgba(255,255,255,0.8);")
        b.close("e-flexbox"); b.close("e-flexbox")
    b.close("e-flexbox"); b.close("e-flexbox")
    return b


def why():
    b = B()
    b.open("e-flexbox", "Why choose us", SECTION + " background: #FFFFFF;", tag="section")
    b.open("e-flexbox", "Why row", f"padding: 0px; gap: 96px; align-items: flex-start; {TAB}{{ flex-direction: column; gap: 48px; }} {MOB}{{ flex-direction: column; gap: 40px; }}")
    b.open("e-flexbox", "Why intro", "flex-direction: column; gap: 26px; padding: 0px; width: 420px; min-width: 0px; max-width: 100%; flex-shrink: 0; align-items: flex-start;")
    eyebrow(b, "Why", "Why ONORA")
    b.h("Why title", "Six reasons venues choose to work with us.", "h2", H2_56)
    b.p("Why body", "We combine the reach of a global sourcing partner with the attention of a local team that understands UAE hospitality.", BODY16)
    b.leaf("e-button", "Why button", "", ["on-btn-navy"], text="Talk to our team&nbsp;&nbsp;→", link=link(BASE + "contact-us/"))
    b.close("e-flexbox")
    b.open("e-flexbox", "Reasons", f"flex-direction: column; padding: 0px; gap: 0px; flex: 1 1 0px; min-width: 0px; width: 100%; {TAB}{{ flex: 0 0 auto; }} {MOB}{{ flex: 0 0 auto; }}")
    reasons = [("Curated global brands", "Hand-picked manufacturers from Germany, Portugal, Italy, the Czech Republic, India and beyond."),
               ("Hospitality expertise", "We speak the language of F&amp;B — covers, par levels, service styles and concept stories."),
               ("Tailored solutions", "Ranges built around your concept, from boutique cafés to 500-key resorts."),
               ("Reliable, timely delivery", "Organised logistics across the UAE, planned around openings and peak seasons."),
               ("Consistent quality", "Commercial-grade products tested for durability, finish and dishwasher performance."),
               ("Dedicated account support", "One point of contact — from first sample to replenishment orders.")]
    for r in range(3):
        row = f"Reason row {r+1}"
        b.open("e-flexbox", row, f"padding: 36px 0px; gap: 48px; align-items: flex-start; border-width: 0px 0px 1px 0px; border-style: solid; border-color: var(--on-line);"
               f" {MOB}{{ flex-direction: column; gap: 32px; padding: 28px 0px; }}")
        for k in range(2):
            n = r * 2 + k + 1
            title, body = reasons[n - 1]
            it = f"Reason {n}"
            b.open("e-flexbox", it, f"flex-direction: column; gap: 12px; padding: 0px; flex: 1 1 0px; min-width: 0px; align-items: flex-start; {MOB}{{ flex: 0 0 auto; }}")
            b.p(f"{it} num", f"{n:02d}", f"{SERIF} letter-spacing: 0px; font-size: 34px; line-height: 36px; {GRAD_TEXT}")
            b.h(f"{it} title", title, "h3", f"{SERIF} font-size: 28px; line-height: 32px; color: var(--on-navy);")
            b.p(f"{it} body", body, f"{SANS} font-weight: 400; font-size: 15px; line-height: 26px; color: var(--on-muted);")
            b.close("e-flexbox")
        b.close("e-flexbox")
    b.close("e-flexbox"); b.close("e-flexbox"); b.close("e-flexbox")
    return b


def process():
    b = B()
    b.open("e-flexbox", "How we work", SECTION + " background: var(--on-navy);", tag="section")
    ring(b, "Process ring outer", 700, 0.35, "top: -200px; right: -240px;")
    ring(b, "Process ring inner", 520, 0.2, "top: -110px; right: -150px;", width="1px")
    head_split(b, "Process", "How we work", "A simple, considered process.",
               "From the first conversation to the hundredth reorder, we keep things clear and on time.", dark=True)
    b.open("e-flexbox", "Steps", f"padding: 0px; gap: 24px; align-items: stretch; z-index: 1; {TAB}{{ flex-wrap: wrap; }} {MOB}{{ flex-direction: column; gap: 16px; }}")
    steps = [("Consult", "We learn your concept, covers, service style and budget.", "→"),
             ("Curate", "We shortlist products and brands, with samples where needed.", "→"),
             ("Supply", "We confirm, consolidate and deliver on schedule across the UAE.", "→"),
             ("Support", "We handle replenishment, additions and new outlets as you grow.", "✓")]
    for i, (title, body, mark) in enumerate(steps, 1):
        s = f"Step {i}"
        b.open("e-flexbox", s, f"flex-direction: column; gap: 20px; padding: 40px 32px; flex: 1 1 0px; min-width: 0px; align-items: flex-start;"
               f" background: rgba(255,255,255,0.05); border-width: 1px; border-style: solid; border-color: rgba(255,255,255,0.14);"
               f" {TAB}{{ flex: 1 1 calc(50% - 12px); }} {MOB}{{ flex: 0 0 auto; width: 100%; padding: 32px 24px; }}")
        b.open("e-flexbox", f"{s} top", "padding: 0px; justify-content: space-between; align-items: center;")
        b.open("e-flexbox", f"{s} badge", "width: 56px; min-width: 56px; height: 56px; padding: 0px; border-radius: 28px; border-width: 1.2px; border-style: solid; border-color: #A5740B; justify-content: center; align-items: center;")
        b.p(f"{s} num", f"{i:02d}", f"{SERIF} letter-spacing: 0px; font-size: 22px; line-height: 1; color: var(--on-gold-soft);")
        b.close("e-flexbox")
        b.p(f"{s} mark", mark, f"{SANS} font-weight: 500; font-size: 18px; line-height: 1.2; color: var(--on-gold-soft);")
        b.close("e-flexbox")
        b.h(f"{s} title", title, "h3", f"{SERIF} font-size: 36px; line-height: 37px; color: #FFFFFF;")
        b.p(f"{s} body", body, f"{SANS} font-weight: 400; font-size: 15px; line-height: 26px; color: rgba(255,255,255,0.75);")
        b.close("e-flexbox")
    b.close("e-flexbox"); b.close("e-flexbox")
    return b


def cta():
    b = B()
    b.open("e-flexbox", "CTA band", f"min-height: 560px; padding: 107px 100px; position: relative; overflow: hidden; flex-direction: column; align-items: center; justify-content: flex-start;"
           f" background-color: #071F44; background-image: linear-gradient(rgba(7,31,68,0.84), rgba(7,31,68,0.84)), url({UP}about-cta-bg.jpg); background-size: cover; background-position: center center; background-repeat: no-repeat;"
           f" {TAB}{{ padding: 96px 40px; min-height: 0px; }} {MOB}{{ padding: 72px 20px; min-height: 0px; }}", tag="section")
    ring(b, "CTA ring outer", 760, 0.3, "top: -100px; left: 50%; margin-left: -380px;")
    ring(b, "CTA ring inner", 560, 0.18, "top: 0px; left: 50%; margin-left: -280px;", width="1px")
    b.open("e-flexbox", "CTA content", "flex-direction: column; gap: 26px; padding: 0px; width: 900px; max-width: 100%; align-items: center; z-index: 1;")
    eyebrow(b, "CTA", "Let's work together", color="var(--on-gold-soft)", both=True, center=True)
    b.h("CTA title", "Let’s build your next opening together.", "h2",
        f"{SERIF} font-size: 68px; line-height: 70px; color: #FFFFFF; text-align: center; width: 860px; max-width: 100%;"
        f" {TAB}{{ font-size: 52px; line-height: 56px; }} {MOB}{{ font-size: 38px; line-height: 42px; }}")
    b.p("CTA lead", "Tell us about your venue and we will curate the right products, brands and quantities — from a single outlet to a full hotel opening.",
        f"{SANS} font-weight: 400; font-size: 17px; line-height: 29px; color: rgba(255,255,255,0.8); text-align: center; width: 640px; max-width: 100%; {MOB}{{ font-size: 15px; line-height: 26px; }}")
    b.open("e-flexbox", "CTA buttons", f"padding: 0px; width: auto; gap: 16px; justify-content: center; align-items: flex-start; {MOB}{{ flex-direction: column; align-items: stretch; width: 100%; }}")
    b.leaf("e-button", "CTA quote", "", ["on-btn-gold"], text="Request a quote&nbsp;&nbsp;→", link=link(BASE + "contact-us/"))
    b.leaf("e-button", "CTA call", "", ["on-btn-outline-light"], text="Call +971 50 203 8005&nbsp;&nbsp;→", link=link("tel:+971502038005"))
    b.close("e-flexbox"); b.close("e-flexbox"); b.close("e-flexbox")
    return b


SECTIONS = [("01-hero", hero), ("02-our-story", story), ("03-stats", stats), ("04-mission-vision", mission),
            ("05-portfolio", portfolio), ("06-why-choose-us", why), ("07-how-we-work", process), ("08-cta-band", cta)]

if __name__ == "__main__":
    import sys
    dry = "--dry" in sys.argv
    for name, fn in SECTIONS:
        p = fn().payload()
        if dry:
            p["dry_run"] = True
        f = os.path.join(OUT, f"about-{name}.json")
        json.dump(p, open(f, "w"), indent=1, ensure_ascii=False)
        print(f)
