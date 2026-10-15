#!/usr/bin/env python3
"""Generate the Brands page (post 27) section payloads -> onora-site/sections/brands-NN-*.json"""
import json, os

PID = 27
OUT = "/home/user/alkhoory/onora-site/sections"
BASE = "https://soharon.co.uk/onora/"
CONTACT = BASE + "contact-us/"
MEDIA = json.load(open("/home/user/alkhoory/onora-site/tools/media.json"))
GOLD = "linear-gradient(90deg, #A5740B 0%, #DEAF57 25%, #F1C96A 50%, #BE8826 75%, #A5740B 100%)"
NB3 = "&nbsp;&nbsp;&nbsp;"


def mid(name):
    return MEDIA[name]["id"]


def murl(name):
    return MEDIA[name]["url"]


def link(url, blank=False):
    return {"destination": url, "isTargetBlank": blank, "tag": "a"}


def mail(subject):
    return "mailto:jason@onorahs.com?subject=" + subject.replace(" ", "%20").replace("&", "%26")


def ring(st, cid, css):
    st[cid] = ("position: absolute; padding: 0px; min-width: 0px; border-radius: 50%; border-style: solid; z-index: 0; "
               + css)


class P:
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

    def eyebrow(self, pre, text, color=None, center_rule=False):
        self.open("e-flexbox", f"{pre} label", classes=["on-eyebrow-row"], style="position: relative; z-index: 1;")
        self.leaf("e-div-block", f"{pre} rule", classes=["on-rule"])
        self.leaf("e-paragraph", f"{pre} eyebrow", classes=["on-eyebrow"], cfg={"paragraph": text},
                  style=f"color: {color};" if color else None)
        if center_rule:
            self.leaf("e-div-block", f"{pre} rule 2", classes=["on-rule"])
        self.close("e-flexbox")

    def payload(self):
        return {"post_id": PID, "xml_structure": "".join(self.x), "element_config": self.cfg, "style": self.st, "classes": self.cl}


def section_head(p, pre, eyebrow, title, lead):
    p.open("e-flexbox", f"{pre} head", style="padding: 0px; justify-content: space-between; align-items: flex-end; gap: 40px;"
           " @media(--tablet){ flex-direction: column; align-items: flex-start; gap: 24px; } @media(--mobile){ flex-direction: column; align-items: flex-start; gap: 20px; }")
    p.open("e-flexbox", f"{pre} head left", style="flex-direction: column; gap: 22px; padding: 0px; width: 700px; max-width: 100%; align-items: flex-start;")
    p.eyebrow(pre, eyebrow)
    p.leaf("e-heading", f"{pre} title", classes=["on-h2"], cfg={"tag": "h2", "title": title})
    p.close("e-flexbox")
    p.leaf("e-paragraph", f"{pre} lead", classes=["on-body"], cfg={"paragraph": lead}, style="width: 420px; max-width: 100%;")
    p.close("e-flexbox")


# ---------------------------------------------------------------- 01 hero
def hero():
    p = P()
    bg = murl("brands-hero-bg.jpg")
    p.open("e-flexbox", "Page hero", style=(
        "flex-direction: column; justify-content: center; align-items: flex-start; min-height: 560px; padding: 118px 100px 117px 100px; overflow: hidden; gap: 0px;"
        f" background-image: linear-gradient(90deg, rgba(7,31,68,0.96) 0%, rgba(11,47,98,0.78) 55%, rgba(11,47,98,0.3) 100%), url({bg});"
        " background-size: cover; background-position: center center; background-repeat: no-repeat;"
        " @media(--tablet){ min-height: 480px; padding: 96px 40px; } @media(--mobile){ min-height: 0px; padding: 72px 20px; }"))
    ring(p.st, "Hero ring outer", "left: 980px; top: -120px; width: 620px; height: 620px; border-width: 1.2px; border-color: rgba(222,175,87,0.45);"
         " @media(--tablet){ left: auto; right: -320px; } @media(--mobile){ left: auto; right: -420px; top: -200px; }")
    p.leaf("e-div-block", "Hero ring outer")
    ring(p.st, "Hero ring inner", "left: 1055px; top: -45px; width: 470px; height: 470px; border-width: 1px; border-color: rgba(222,175,87,0.25);"
         " @media(--tablet){ left: auto; right: -245px; } @media(--mobile){ display: none; }")
    p.leaf("e-div-block", "Hero ring inner")
    p.open("e-flexbox", "Hero content", style="flex-direction: column; gap: 24px; padding: 0px; width: 780px; max-width: 100%; align-items: flex-start; position: relative; z-index: 1;")
    p.leaf("e-paragraph", "Hero breadcrumb", cfg={"paragraph": f'<a href="{BASE}">Home</a>{NB3}/{NB3}Brands'},
           style="font-family: var(--on-sans); font-weight: 500; font-size: 12px; letter-spacing: 2px; text-transform: uppercase; color: rgba(255,255,255,0.6);")
    p.leaf("e-heading", "Hero title", classes=["on-h1"], cfg={"tag": "h1", "title": "World-class brands, carefully curated."},
           style="letter-spacing: -0.3px; line-height: 88px; @media(--tablet){ line-height: 68px; } @media(--mobile){ line-height: 50px; }")
    p.leaf("e-div-block", "Hero rule", style=f"width: 90px; min-width: 90px; height: 2px; padding: 0px; background-image: {GOLD};")
    p.leaf("e-paragraph", "Hero lead", cfg={"paragraph": "We partner with leading manufacturers from Europe and Asia to bring proven hospitality quality to every venue we serve."},
           style="font-family: var(--on-sans); font-weight: 400; font-size: 17px; line-height: 30px; color: rgba(255,255,255,0.82); width: 560px; max-width: 100%;"
                 " @media(--mobile){ font-size: 15px; line-height: 26px; }")
    p.close("e-flexbox")
    p.close("e-flexbox")
    return p.payload()


# ---------------------------------------------------------------- 02 brand partners
BRANDS = [  # name, desc, logo file, navy?, ring?, catalogue?
    ("Lusini", "Hospitality equipment &amp; tableware", "brands-logo-lusini.png", 1, 1, 1),
    ("Stylepoint", "Porcelain &amp; melamine dinnerware", "brands-logo-stylepoint.png", 1, 1, 1),
    ("Falkenporzellan", "Premium porcelain", "brands-logo-falkenporzellan.png", 1, 1, 1),
    ("Schönhuber Franchi", "Fine bone china, buffet &amp; tableware", "brands-logo-schonhuber-franchi.jpg", 0, 0, 1),
    ("Spal Porcelanas", "Porcelain tableware", "brands-logo-spal-porcelanas.png", 0, 0, 0),
    ("CTB Ceramics", "Ceramic tableware", "brands-logo-ctb-ceramics.png", 0, 0, 0),
    ("Tuppa Stoneware", "Stoneware tableware", "brands-logo-tuppa-stoneware.png", 1, 1, 0),
    ("Chromolite", "Cutlery", "brands-logo-chromolite.png", 0, 0, 1),
    ("Belo Inox", "Stainless-steel cutlery", "brands-logo-belo-inox.png", 0, 0, 1),
    ("Crystal Bohemia", "Crystal glassware", "brands-logo-crystal-bohemia.png", 0, 0, 0),
    ("Vidivi", "Glassware", "brands-logo-vidivi.png", 1, 1, 0),
    ("UnbreakMyGlass", "Unbreakable drinkware", "brands-logo-unbreakmyglass.png", 0, 0, 0),
    ("GraMonty", "Polycarbonate drinkware", "brands-logo-gramonty.png", 1, 1, 1),
    ("Nipco", "Drinkware &amp; tableware", "brands-logo-nipco.png", 1, 0, 1),
    ("WAS Germany", "Kitchen &amp; buffet equipment", "brands-logo-was-germany.png", 1, 1, 0),
    ("Trolley &amp; Cart", "Service trolleys &amp; carts", "brands-logo-trolley-cart.png", 1, 1, 0),
    ("Kolk", "Hospitality lighting", "brands-logo-kolk.png", 0, 0, 1),
    ("Puri Hospitality", "Hospitality solutions", "brands-logo-puri-hospitality.png", 1, 1, 0),
    ("MR International", "Indian tableware", "brands-logo-mr-international.png", 0, 0, 0),
]
CHIPS = ["All brands", "Tableware", "Cutlery", "Glass &amp; drinkware", "Buffet &amp; service", "Kitchen", "Lighting", "Indian tableware"]


def partners():
    p = P()
    p.open("e-flexbox", "Brand partners", classes=["on-section"], style="background: #FFFFFF; gap: 0px;")
    p.open("e-flexbox", "Partners inner", classes=["on-inner"], style="gap: 40px;")
    section_head(p, "Partners", "Our brand partners", "Nineteen names. One standard of excellence.",
                 "Every brand in our portfolio is selected for design, durability and the support it gives commercial kitchens and dining rooms.")
    p.open("e-flexbox", "Filter chips", style="padding: 0px; flex-wrap: wrap; gap: 12px; align-items: flex-start; @media(--mobile){ gap: 8px; }")
    for i, c in enumerate(CHIPS):
        on = i == 0
        p.leaf("e-paragraph", f"Chip {i+1}", cfg={"paragraph": c}, style=(
            "font-family: var(--on-sans); font-weight: 600; font-size: 11px; letter-spacing: 1.6px; text-transform: uppercase; line-height: 13px; padding: 12px 20px;"
            + (" color: #FFFFFF; background: var(--on-navy); border: 1px solid #0B2F62;" if on else
               " color: var(--on-navy); background: #FFFFFF; border: 1px solid #E6E0D6; transition: border-color 0.3s; &:hover{ border-color: #C9962E; }")
            + " @media(--mobile){ padding: 10px 14px; font-size: 10px; letter-spacing: 1.2px; }"))
    p.close("e-flexbox")
    p.open("e-grid", "Brand grid", style="padding: 0px; gap: 24px; grid-template-columns: repeat(4, 1fr); grid-template-rows: auto;"
           " @media(--tablet){ grid-template-columns: repeat(2, 1fr); } @media(--mobile){ grid-template-columns: repeat(1, 1fr); gap: 16px; }")
    for i, (name, desc, logo, navy, rg, cat) in enumerate(BRANDS):
        c = f"Brand {i+1}"
        plain = name.replace("&amp;", "&")
        p.open("e-flexbox", c, style="flex-direction: column; padding: 0px; gap: 0px; background: #FFFFFF; border: 1px solid #E6E0D6; overflow: hidden; align-items: stretch;"
               " transition: box-shadow 0.3s; &:hover{ box-shadow: 0px 18px 40px rgba(11,47,98,0.10); }")
        p.open("e-flexbox", f"{c} logo area", style=f"height: 190px; padding: 0px; justify-content: center; align-items: center; overflow: hidden; background: {'#0B2F62' if navy else '#F3F3F3'};")
        if rg:
            ring(p.st, f"{c} ring", "right: -80px; top: -60px; width: 170px; height: 170px; border-width: 1px; border-color: rgba(222,175,87,0.35);")
            p.leaf("e-div-block", f"{c} ring")
        p.leaf("e-image", f"{c} logo", cfg={"image": {"src": {"id": mid(logo)}, "size": "full"}},
               style="width: 180px; height: 84px; object-fit: contain; position: relative; z-index: 1;")
        p.close("e-flexbox")
        p.open("e-flexbox", f"{c} body", style="flex-direction: column; gap: 8px; padding: 22px 24px 24px 24px; align-items: stretch;")
        p.leaf("e-paragraph", f"{c} name", cfg={"paragraph": name},
               style="font-family: var(--on-serif); font-weight: 600; font-size: 26px; line-height: 30px; letter-spacing: -0.3px; color: var(--on-navy);")
        p.leaf("e-paragraph", f"{c} desc", cfg={"paragraph": desc},
               style="font-family: var(--on-sans); font-weight: 400; font-size: 13px; line-height: 21px; color: var(--on-muted);")
        url = mail(f"Catalogue request - {plain}") if cat else CONTACT
        p.open("e-flexbox", f"{c} foot", cfg={"link": link(url)},
               style="padding: 14px 0px 0px 0px; border-width: 1px 0px 0px 0px; border-style: solid; border-color: #E6E0D6; justify-content: space-between; align-items: center; gap: 12px;")
        p.leaf("e-paragraph", f"{c} cta", cfg={"paragraph": "Catalogue" if cat else "Enquire"},
               style="font-family: var(--on-sans); font-weight: 600; font-size: 11px; letter-spacing: 1.8px; text-transform: uppercase; color: var(--on-navy);")
        p.leaf("e-paragraph", f"{c} arrow", cfg={"paragraph": "↓" if cat else "→"},
               style="font-family: var(--on-sans); font-weight: 500; font-size: 15px; line-height: 18px; color: var(--on-gold);")
        p.close("e-flexbox")
        p.close("e-flexbox")
        p.close("e-flexbox")
    # bespoke card
    c = "Custom sourcing"
    p.open("e-flexbox", c, style="flex-direction: column; gap: 18px; padding: 36px 28px 4px 28px; background: var(--on-navy-deep); overflow: hidden; align-items: flex-start; min-height: 338px; @media(--tablet){ padding: 36px 28px; } @media(--mobile){ padding: 36px 28px; }")
    ring(p.st, f"{c} ring", "right: -88px; top: -80px; width: 260px; height: 260px; border-width: 1.2px; border-color: rgba(222,175,87,0.4);")
    p.leaf("e-div-block", f"{c} ring")
    p.eyebrow("Custom", "Bespoke", color="var(--on-gold-soft)")
    p.leaf("e-paragraph", f"{c} title", cfg={"paragraph": "Looking for another brand?"},
           style="font-family: var(--on-serif); font-weight: 600; font-size: 32px; line-height: 34px; letter-spacing: -0.3px; color: #FFFFFF; position: relative; z-index: 1;")
    p.leaf("e-paragraph", f"{c} text", cfg={"paragraph": "Our sourcing team can locate specific ranges, matching replacements and exclusive lines on request."},
           style="font-family: var(--on-sans); font-weight: 400; font-size: 13px; line-height: 22px; color: rgba(255,255,255,0.75); position: relative; z-index: 1;")
    p.leaf("e-div-block", f"{c} spacer", style="flex-grow: 1; padding: 0px; min-height: 1px;")
    p.leaf("e-button", f"{c} button", classes=["on-btn-gold"], cfg={"text": "Ask us&nbsp;&nbsp;→", "link": link(CONTACT)}, style="position: relative; z-index: 1;")
    p.close("e-flexbox")
    p.close("e-grid")
    p.close("e-flexbox")
    p.close("e-flexbox")
    return p.payload()


# ---------------------------------------------------------------- 03 signature branding
def branding():
    p = P()
    p.open("e-flexbox", "Signature branding", style="padding: 0px; gap: 0px; align-items: stretch; background: var(--on-navy);"
           " @media(--tablet){ flex-direction: column; } @media(--mobile){ flex-direction: column; }")
    p.leaf("e-div-block", "Branding image", style=(
        f"width: 50%; min-height: 845px; padding: 0px; background-image: url({murl('brands-branding-plate.jpg')}); background-size: cover; background-position: center center; background-repeat: no-repeat;"
        " @media(--tablet){ width: 100%; min-height: 520px; } @media(--mobile){ width: 100%; min-height: 360px; }"))
    p.open("e-flexbox", "Branding panel", style="width: 50%; flex-direction: column; justify-content: center; align-items: flex-start; gap: 28px; padding: 100px 100px 100px 88px; overflow: hidden; background: var(--on-navy);"
           " @media(--tablet){ width: 100%; padding: 88px 40px; } @media(--mobile){ width: 100%; padding: 64px 20px; gap: 24px; }")
    ring(p.st, "Branding ring", "right: -140px; top: -180px; width: 520px; height: 520px; border-width: 1.2px; border-color: rgba(222,175,87,0.35); @media(--mobile){ right: -300px; }")
    p.leaf("e-div-block", "Branding ring")
    p.eyebrow("Branding", "Signature branding", color="var(--on-gold-soft)")
    p.leaf("e-heading", "Branding title", cfg={"tag": "h2", "title": "Your logo, beautifully placed on every touchpoint."},
           style="font-family: var(--on-serif); font-weight: 600; font-size: 52px; line-height: 56px; letter-spacing: -0.3px; color: #FFFFFF; position: relative; z-index: 1;"
                 " @media(--tablet){ font-size: 44px; line-height: 48px; } @media(--mobile){ font-size: 36px; line-height: 40px; }")
    p.leaf("e-paragraph", "Branding lead", cfg={"paragraph": "Make your venue unforgettable with custom-branded tableware and service pieces produced with our manufacturing partners."},
           style="font-family: var(--on-sans); font-weight: 400; font-size: 16px; line-height: 28px; color: rgba(255,255,255,0.78); position: relative; z-index: 1;")
    p.open("e-flexbox", "Branding list", style="position: relative; z-index: 1; flex-direction: column; padding: 0px; gap: 0px; align-items: stretch;")
    items = ["Personalised chinaware with back-stamps and rim decals", "Logo-engraved cutlery and glassware",
             "Custom-printed linens, menus and table accessories", "Branded buffet signage and display accents"]
    for i, t in enumerate(items):
        c = f"Branding item {i+1}"
        p.open("e-flexbox", c, style="padding: 16px 0px; gap: 18px; align-items: center; border-width: 0px 0px 1px 0px; border-style: solid; border-color: rgba(255,255,255,0.14);")
        p.leaf("e-paragraph", f"{c} no", cfg={"paragraph": f"0{i+1}"},
               style="font-family: var(--on-serif); font-weight: 600; font-size: 22px; line-height: 27px; color: var(--on-gold-soft);")
        p.leaf("e-paragraph", f"{c} text", cfg={"paragraph": t},
               style="flex-grow: 1; font-family: var(--on-sans); font-weight: 500; font-size: 15px; line-height: 20px; color: #FFFFFF;")
        p.close("e-flexbox")
    p.close("e-flexbox")
    p.leaf("e-button", "Branding button", classes=["on-btn-gold"], cfg={"text": "Start a branding project&nbsp;&nbsp;→", "link": link(CONTACT)},
           style="position: relative; z-index: 1; @media(--mobile){ padding: 16px 20px; letter-spacing: 1.6px; }")
    p.close("e-flexbox")
    p.close("e-flexbox")
    return p.payload()


# ---------------------------------------------------------------- 04 catalogues
CATS = [("Lusini Annual Catalogue", "Lusini", "2025"), ("Stylepoint Collection", "Stylepoint", "2025"),
        ("Stylepoint Raw Premium", "Stylepoint", "2025"), ("Chromolite Cutlery", "Chromolite", "2025"),
        ("Belo Inox Lifestyle", "Belo Inox", "Current"), ("Unity Drinkware", "Nipco", "2025"),
        ("Kolk E-Light", "Kolk", "2024–25"), ("GraMonty Drinkware", "GraMonty", "Current"),
        ("Falkenporzellan Premium Designs", "Falkenporzellan", "Current"), ("Nipco Catalogue", "Nipco", "2025"),
        ("Schönhuber Franchi Fine Bone China", "Schönhuber Franchi", "Current"), ("Schönhuber Franchi Buffet", "Schönhuber Franchi", "Current"),
        ("Schönhuber Franchi Tableware", "Schönhuber Franchi", "Current")]


def catalogues():
    p = P()
    p.open("e-flexbox", "Catalogues", classes=["on-section"], style="background: var(--on-cream); gap: 0px;")
    p.open("e-flexbox", "Catalogues inner", classes=["on-inner"], style="gap: 64px; @media(--mobile){ gap: 40px; }")
    section_head(p, "Catalogues", "Download centre", "Brand catalogues &amp; look-books.",
                 "Browse full ranges, dimensions and specifications. Need pricing? Send us your selection and we will prepare a tailored quote.")
    p.open("e-grid", "Catalogue list", style="padding: 0px; gap: 16px; grid-template-columns: repeat(2, 1fr); grid-template-rows: auto;"
           " @media(--tablet){ grid-template-columns: repeat(1, 1fr); gap: 16px; } @media(--mobile){ grid-template-columns: repeat(1, 1fr); gap: 12px; }")
    for i, (title, brand, year) in enumerate(CATS):
        c = f"Catalogue {i+1}"
        p.open("e-flexbox", c, style="background: #FFFFFF; min-height: 110px; padding: 20px 24px 20px 20px; gap: 22px; align-items: center;"
               " @media(--mobile){ padding: 16px; gap: 14px; flex-wrap: wrap; }")
        p.open("e-flexbox", f"{c} badge", style="width: 56px; min-width: 56px; height: 64px; padding: 0px; background: var(--on-navy); justify-content: center; align-items: center;")
        p.leaf("e-paragraph", f"{c} pdf", cfg={"paragraph": "PDF"},
               style="font-family: var(--on-sans); font-weight: 700; font-size: 11px; letter-spacing: 1.5px; color: var(--on-gold-soft);")
        p.close("e-flexbox")
        p.open("e-flexbox", f"{c} info", style="flex-direction: column; gap: 6px; padding: 0px; flex-grow: 1; flex-basis: 0px; align-items: stretch;"
               " @media(--mobile){ flex-basis: 160px; }")
        p.leaf("e-paragraph", f"{c} title", cfg={"paragraph": title},
               style="font-family: var(--on-serif); font-weight: 600; font-size: 24px; line-height: 28px; letter-spacing: -0.3px; color: var(--on-navy); @media(--mobile){ font-size: 21px; line-height: 25px; }")
        p.leaf("e-paragraph", f"{c} meta", cfg={"paragraph": f"{brand}{NB3}·{NB3}{year}"},
               style="font-family: var(--on-sans); font-weight: 500; font-size: 12px; letter-spacing: 0.6px; color: var(--on-muted);")
        p.close("e-flexbox")
        p.leaf("e-button", f"{c} download", cfg={"text": "Download&nbsp;&nbsp;↓", "link": link(mail(f"Catalogue request - {title}"))},
               style="font-family: var(--on-sans); font-weight: 600; font-size: 11px; letter-spacing: 1.8px; text-transform: uppercase; line-height: 1.2; color: var(--on-navy);"
                     " background: transparent; border: 1px solid #0B2F62; border-radius: 0px; padding: 12px 18px; transition: background-color 0.3s;"
                     " &:hover{ background: #0B2F62; color: #FFFFFF; } @media(--mobile){ margin-left: 70px; }")
        p.close("e-flexbox")
    p.close("e-grid")
    p.close("e-flexbox")
    p.close("e-flexbox")
    return p.payload()


# ---------------------------------------------------------------- 05 CTA band
def cta():
    p = P()
    bg = murl("brands-cta-bg.jpg")
    p.open("e-flexbox", "CTA band", style=(
        "flex-direction: column; align-items: center; justify-content: flex-start; min-height: 560px; padding: 107px 100px 80px 100px; overflow: hidden; gap: 0px;"
        f" background-image: linear-gradient(0deg, rgba(7,31,68,0.84) 0%, rgba(7,31,68,0.84) 100%), url({bg}); background-size: cover; background-position: center center; background-repeat: no-repeat;"
        " @media(--tablet){ min-height: 0px; padding: 96px 40px; } @media(--mobile){ min-height: 0px; padding: 72px 20px; }"))
    ring(p.st, "CTA ring outer", "left: calc(50% - 380px); top: -100px; width: 760px; height: 760px; border-width: 1.2px; border-color: rgba(222,175,87,0.3);")
    p.leaf("e-div-block", "CTA ring outer")
    ring(p.st, "CTA ring inner", "left: calc(50% - 280px); top: 0px; width: 560px; height: 560px; border-width: 1px; border-color: rgba(222,175,87,0.18);")
    p.leaf("e-div-block", "CTA ring inner")
    p.open("e-flexbox", "CTA content", style="flex-direction: column; align-items: center; gap: 26px; padding: 0px; width: 900px; max-width: 100%; position: relative; z-index: 1;")
    p.eyebrow("CTA", "Let's work together", color="var(--on-gold-soft)", center_rule=True)
    p.leaf("e-heading", "CTA title", cfg={"tag": "h2", "title": "Not sure which brand suits your concept?"},
           style="font-family: var(--on-serif); font-weight: 600; font-size: 68px; line-height: 70px; letter-spacing: -0.3px; color: #FFFFFF; text-align: center; max-width: 860px;"
                 " @media(--tablet){ font-size: 52px; line-height: 56px; } @media(--mobile){ font-size: 38px; line-height: 42px; }")
    p.leaf("e-paragraph", "CTA text", cfg={"paragraph": "Share your venue type, covers and style — our team will recommend the right brands and build a tailored product selection for you."},
           style="font-family: var(--on-sans); font-weight: 400; font-size: 17px; line-height: 29px; color: rgba(255,255,255,0.8); text-align: center; max-width: 640px;"
                 " @media(--mobile){ font-size: 15px; line-height: 26px; }")
    p.open("e-flexbox", "CTA buttons", style="padding: 0px; width: auto; gap: 16px; justify-content: center; flex-wrap: wrap;"
           " @media(--mobile){ flex-direction: column; align-items: stretch; width: 100%; }")
    p.leaf("e-button", "CTA quote", classes=["on-btn-gold"], cfg={"text": "Request a quote&nbsp;&nbsp;→", "link": link(CONTACT)})
    p.leaf("e-button", "CTA call", classes=["on-btn-outline-light"], cfg={"text": "Call +971 50 203 8005&nbsp;&nbsp;→", "link": link("tel:+971502038005")},
           style="border-color: rgba(255,255,255,0.5);")
    p.close("e-flexbox")
    p.close("e-flexbox")
    p.close("e-flexbox")
    return p.payload()


if __name__ == "__main__":
    import sys
    dry = "--dry" in sys.argv
    for nn, name, fn in [("01", "hero", hero), ("02", "brand-partners", partners), ("03", "signature-branding", branding),
                         ("04", "catalogues", catalogues), ("05", "cta-band", cta)]:
        pl = fn()
        if dry:
            pl["dry_run"] = True
        json.dump(pl, open(os.path.join(OUT, f"brands-{nn}-{name}.json"), "w"), indent=1, ensure_ascii=False)
        print(nn, name)
