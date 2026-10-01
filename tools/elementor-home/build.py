"""Builds the Home page (post 42) on the WordPress site with Elementor V4 atomic widgets.

Usage (dry run by default):
  WP_USER=admin WP_APP_PASSWORD='xxxx xxxx ...' python3 build.py hero stats about brands why mission services cta insights [--live]

The first section listed as FIRST replaces the whole page; the rest are appended in order.
After a live build: publish the document and clear Elementor's CSS cache (DELETE /wp-json/elementor/v1/cache).
"""
import json, sys
from call import call

POST = 42
UP = "https://soharon.co.uk/alkhoory/wp-content/uploads/2026/10/"
IMG = {"subaru": 8, "hero": 10, "about": 11, "bus": 12, "car": 13, "dots": 14, "article": 15, "yutong": 16, "kinglong": 17}
DOTS_URL = UP + "img_6abe4690f1938.png"
HERO_URL = UP + "img_6abe468c5ed43.png"


class B:
    """Tiny builder: collects xml + per-id config/style/classes."""

    def __init__(self):
        self.cfg, self.sty, self.cls = {}, {}, {}

    def el(self, tag, cid, children="", cfg=None, style=None, classes=None):
        if cfg:
            self.cfg[cid] = cfg
        if style:
            self.sty[cid] = style
        if classes:
            self.cls[cid] = classes
        return f'<{tag} configuration-id="{cid}">{children}</{tag}>'

    def heading(self, cid, text, tag="h2", classes=None, style=None):
        return self.el("e-heading", cid, cfg={"tag": tag, "title": text}, classes=classes, style=style)

    def para(self, cid, text, classes=None, style=None, link=None, tag="p"):
        cfg = {"paragraph": text, "tag": tag}
        if link:
            cfg["link"] = {"destination": link, "isTargetBlank": False, "tag": "a"}
        return self.el("e-paragraph", cid, cfg=cfg, classes=classes, style=style)

    def button(self, cid, text, link, classes, style=None):
        return self.el("e-button", cid, cfg={"text": text, "link": {"destination": link, "isTargetBlank": False, "tag": "a"}}, classes=classes, style=style)

    def image(self, cid, key, style=None, classes=None):
        return self.el("e-image", cid, cfg={"image": {"src": {"id": IMG[key]}, "size": "full"}}, style=style, classes=classes)

    def box(self, cid, children, style=None, classes=None, tag="div", link=None, kind="e-flexbox"):
        cfg = {"tag": tag}
        if link:
            cfg["link"] = {"destination": link, "isTargetBlank": False, "tag": "a"}
        return self.el(kind, cid, children, cfg=cfg, style=style, classes=classes)

    def heading_group(self, b, prefix, eyebrow, title, lead=None, center=False, dark=False, title_style=None, lead_style=None):
        align = "align-items: center; text-align: center;" if center else "align-items: flex-start;"
        kids = self.para(f"{prefix} Eyebrow", eyebrow, classes=["ak-eyebrow"], style="color: var(--ak-sky);" if dark else None)
        kids += self.heading(f"{prefix} Title", title, classes=["ak-h2"], style=((title_style or "") + (" color: var(--ak-white);" if dark else "")) or None)
        if lead:
            kids += self.para(f"{prefix} Intro", lead, classes=["ak-lead"], style=((lead_style or "") + (" color: rgba(255, 255, 255, 0.75);" if dark else "")) or None)
        return self.box(f"{prefix} Heading", kids, classes=["ak-heading-group"], style=f"flex-direction: column; {align}")


def run(name, xml, b, dry):
    args = {"post_id": POST, "mode": "replace_children" if name == FIRST else "append", "xml_structure": xml, "element_config": b.cfg, "style": b.sty, "classes": b.cls, "dry_run": dry}
    out = call("elementor-build-composition", args)
    try:
        d = json.loads(out)
    except Exception:
        print(name, "RAW:", out[:3000])
        return None
    keys = list(d.keys())
    print(name, "keys:", keys)
    for k in ("warnings", "errors", "error", "message"):
        if d.get(k):
            print(" ", k, ":", json.dumps(d[k])[:3000])
    return d


# ---------------------------------------------------------------- sections
def hero():
    b = B()
    content = b.para("Hero Eyebrow", "SINCE 1972&nbsp;&nbsp;·&nbsp;&nbsp;AL KHOORY AUTOMOBILES",
                     style="font-family: var(--ak-font-heading); font-weight: 500; font-size: 13px; line-height: 1.4; letter-spacing: 1.04px; color: var(--ak-white); padding: 8px 16px; border-radius: 999px; background-color: rgba(255, 255, 255, 0.12); border: 1px solid rgba(255, 255, 255, 0.25); @media(--mobile) { font-size: 11px; letter-spacing: 0.8px; padding: 7px 12px; }")
    content += b.heading("Hero Title", "Driving Every Journey<br>with Confidence", tag="h1",
                         style="font-family: var(--ak-font-heading); font-weight: 600; font-size: 62px; line-height: 1.12; color: var(--ak-white); @media(--tablet) { font-size: 48px; } @media(--mobile) { font-size: 36px; }")
    content += b.para("Hero Intro", "From premium passenger vehicles to commercial transportation solutions, Al Khoory Automobiles delivers trusted mobility backed by over five decades of automotive excellence.",
                      style="font-family: var(--ak-font-body); font-size: 18px; line-height: 1.65; color: rgba(255, 255, 255, 0.85); max-width: 560px; @media(--mobile) { font-size: 16px; }")
    ctas = b.button("Hero Explore Brands", "Explore Brands", "#brands", ["ak-btn", "ak-btn-light", "ak-btn-arrow-dark"])
    ctas += b.button("Hero Test Drive", "Book a Test Drive", "#contact", ["ak-btn", "ak-btn-outline-light"])
    content += b.box("Hero Buttons", ctas, style="padding: 0; gap: 16px; flex-wrap: wrap; @media(--mobile) { gap: 12px; }")
    inner = b.box("Hero Content", content, style="flex-direction: column; align-items: flex-start; gap: 28px; padding: 0; max-width: 780px;")
    wrap = b.box("Hero Container", inner, classes=["ak-inner"], style="display: flex;")
    xml = b.box("Hero", wrap, tag="section",
                style=f"flex-direction: column; justify-content: center; min-height: 720px; padding: 100px; background-color: var(--ak-navy); background-image: linear-gradient(90deg, rgba(6, 26, 54, 0.92) 0%, rgba(6, 26, 54, 0.6) 45%, rgba(6, 26, 54, 0) 75%), url({HERO_URL}); background-size: cover; background-position: center center; background-repeat: no-repeat; @media(--tablet) {{ min-height: 600px; padding: 80px 40px; background-image: linear-gradient(90deg, rgba(6, 26, 54, 0.92) 0%, rgba(6, 26, 54, 0.78) 60%, rgba(6, 26, 54, 0.4) 100%), url({HERO_URL}); }} @media(--mobile) {{ min-height: 560px; padding: 64px 20px; background-image: linear-gradient(180deg, rgba(6, 26, 54, 0.88) 0%, rgba(6, 26, 54, 0.7) 60%, rgba(6, 26, 54, 0.35) 100%), url({HERO_URL}); }}")
    return xml, b


def stats():
    b = B()
    items = [("50+", "Years of automotive excellence"), ("3", "Exclusive global brands"), ("1976", "Subaru distributor since"), ("8", "Showrooms &amp; service centres")]
    kids = ""
    for i, (num, label) in enumerate(items, 1):
        border = "border-style: solid; border-color: #E6EAF0; border-width: 0px 0px 0px 0px;" if i == 1 else "border-style: solid; border-color: #E6EAF0; border-width: 0px 0px 0px 1px;"
        mob = "@media(--mobile) { border-width: 0px 0px 0px 0px; }" if i in (1, 3) else ""
        s = b.para(f"Stat {i} Number", num, style="font-family: var(--ak-font-heading); font-weight: 600; font-size: 44px; line-height: 1.1; color: var(--ak-blue); @media(--mobile) { font-size: 34px; }")
        s += b.para(f"Stat {i} Label", label, style="font-family: var(--ak-font-body); font-size: 15px; line-height: 1.5; color: var(--ak-muted); text-align: center; @media(--mobile) { font-size: 13px; }")
        kids += b.box(f"Stat {i}", s, style=f"flex: 1 1 0; flex-direction: column; align-items: center; gap: 6px; padding: 6px 12px; {border} {mob}")
    row = b.box("Stats Row", kids, classes=["ak-inner"], style="display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); @media(--mobile) { grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 32px; }")
    xml = b.box("Stats", row, tag="section", style="padding: 56px 100px; background-color: var(--ak-white); border-style: solid; border-color: #E6EAF0; border-width: 0px 0px 1px 0px; justify-content: center; @media(--tablet) { padding: 48px 40px; } @media(--mobile) { padding: 40px 20px; }")
    return xml, b


def about():
    b = B()
    img = b.image("About Image", "about", style="width: 600px; height: 480px; object-fit: cover; border-radius: 20px; flex-shrink: 0; @media(--tablet) { width: 100%; height: 420px; } @media(--mobile) { height: 280px; }")
    head = b.heading_group(b, "About", "TRUSTED SINCE 1972", "Built on Trust.<br>Driven by Innovation.")
    body = b.para("About Text", "For over five decades, Al Khoory Automobiles has been helping individuals, businesses, and government organisations move forward with reliable mobility solutions. From passenger cars to commercial fleets, we deliver vehicles engineered for performance, durability, and long-term value.", classes=["ak-lead"], style="line-height: 1.7;")
    pills = ""
    for i, t in enumerate(["Premium Brands", "Expert Technicians", "Genuine Parts", "Nationwide Support"], 1):
        pills += b.para(f"Pillar {i}", t, classes=["ak-check-item"])
    pillars = b.box("Pillars", pills, kind="e-grid", style="grid-template-columns: repeat(2, minmax(0, 1fr)); grid-template-rows: repeat(2, auto); gap: 16px; column-gap: 24px; padding: 0; width: 100%; @media(--mobile) { grid-template-columns: minmax(0, 1fr); grid-template-rows: repeat(4, auto); }")
    btn = b.button("About Button", "More About Us", "#about", ["ak-btn", "ak-btn-primary", "ak-btn-arrow-white"])
    text = b.box("About Text Column", head + body + pillars + btn, style="flex: 1 1 0; flex-direction: column; align-items: flex-start; gap: 28px; padding: 0; min-width: 0;")
    row = b.box("About Row", img + text, classes=["ak-inner"], style="display: flex; align-items: center; gap: 72px; @media(--tablet) { flex-direction: column; align-items: stretch; gap: 48px; }")
    xml = b.box("About", row, tag="section", classes=["ak-section"], style="background-color: var(--ak-white);")
    return xml, b


def brands():
    b = B()
    head = b.heading_group(b, "Brands", "OUR BRANDS", "Exclusive Distributor of World-Class Brands",
                           "Three globally recognised manufacturers — from spirited passenger cars to luxury coaches and commercial vans.", center=True,
                           title_style="max-width: 900px;", lead_style="max-width: 680px;")
    data = [
        ("Subaru", "subaru", "Exclusive distributor for Dubai and the Northern Emirates. A Japanese marque with a proud rally heritage, known for its Boxer engine and Symmetrical All-Wheel Drive.", "https://www.subaru.ae/", "width: 301px; height: 100px;"),
        ("Yutong", "yutong", "Exclusive UAE distributor for one of the world’s largest bus makers, supplying luxury coaches and electric buses to leading UAE government entities.", "https://www.yutong.ae/", "width: 301px; height: 100px;"),
        ("King Long", "kinglong", "Sole UAE distributor of King Long minivans — 13 to 15-seat passenger vans and 3 to 5-seat panel and half-panel vans for business fleets.", "https://www.kinglong.ae/", "width: 263px; height: 88px;"),
    ]
    cards = ""
    for name, key, text, url, size in data:
        n = name.replace(" ", "")
        logo = b.image(f"{name} Logo", key, style=f"{size} max-width: 100%; object-fit: contain;")
        area = b.box(f"{name} Logo Area", logo, style="width: 100%; height: 140px; padding: 0; justify-content: center; align-items: center; background-color: var(--ak-soft); border-radius: 14px;")
        title = b.heading(f"{name} Name", name, tag="h3", classes=["ak-card-title"], style="font-size: 24px;")
        desc = b.para(f"{name} Description", text, classes=["ak-body"])
        link = b.para(f"{name} Link", "Visit site", classes=["ak-link-arrow"], link=url, style="margin-top: auto;")
        cards += b.box(f"Brand Card {name}", area + title + desc + link, classes=["ak-card"], style="padding: 32px; gap: 20px; min-height: 492px; @media(--tablet) { min-height: 0; }")
    grid = b.box("Brand Cards", cards, kind="e-grid", classes=["ak-inner"], style="grid-template-columns: repeat(3, minmax(0, 1fr)); grid-template-rows: auto; gap: 24px; @media(--tablet) { grid-template-columns: repeat(2, minmax(0, 1fr)); } @media(--mobile) { grid-template-columns: minmax(0, 1fr); }")
    xml = b.box("Our Brands", head + grid, tag="section", classes=["ak-section"], style="background-color: var(--ak-soft);")
    return xml, b


def why():
    b = B()
    head = b.heading_group(b, "Why", "WHY AL KHOORY", "Why Customers Choose Al Khoory",
                           "Everything you need from a single, trusted automotive partner — before, during and long after your purchase.",
                           center=True, dark=True, title_style="max-width: 760px;", lead_style="max-width: 680px;")
    data = [("50+ Years of Excellence", "A trusted automotive partner across the UAE since 1972."),
            ("Exclusive Distributors", "Official distributor of globally recognised brands."),
            ("Certified Service Centres", "Manufacturer-trained technicians using advanced diagnostics."),
            ("Genuine Spare Parts", "Original components that maximise performance and safety.")]
    cards = ""
    for i, (t, d) in enumerate(data, 1):
        icon = b.box(f"Reason {i} Icon", "", kind="e-div-block", classes=["ak-icon-wheel-white"], style="width: 56px; height: 56px; min-width: 56px; padding: 0; border-radius: 28px; background-color: var(--ak-blue);")
        title = b.heading(f"Reason {i} Title", t, tag="h3", classes=["ak-card-title"], style="color: var(--ak-white);")
        desc = b.para(f"Reason {i} Text", d, classes=["ak-body"], style="line-height: 1.65; color: rgba(255, 255, 255, 0.72);")
        cards += b.box(f"Reason {i}", icon + title + desc, classes=["ak-card"], style="gap: 20px; min-height: 264px; background-color: rgba(255, 255, 255, 0.06); border: 1px solid rgba(255, 255, 255, 0.14); &:hover { box-shadow: 0 0 0 0 rgba(0, 0, 0, 0); background-color: rgba(255, 255, 255, 0.1); } @media(--tablet) { min-height: 0; }")
    grid = b.box("Reasons", cards, kind="e-grid", classes=["ak-inner"], style="grid-template-columns: repeat(4, minmax(0, 1fr)); grid-template-rows: auto; gap: 24px; @media(--tablet) { grid-template-columns: repeat(2, minmax(0, 1fr)); } @media(--mobile) { grid-template-columns: minmax(0, 1fr); }")
    xml = b.box("Why Choose Us", head + grid, tag="section", classes=["ak-section"], style="background-color: var(--ak-deep);")
    return xml, b


def mission():
    b = B()
    head = b.heading_group(b, "Purpose", "OUR PURPOSE", "Mission &amp; Vision", center=True)

    def card(prefix, title, sub, text):
        icon = b.box(f"{prefix} Icon", "", kind="e-div-block", classes=["ak-icon-wheel-white"], style="width: 40px; height: 40px; min-width: 40px; padding: 0; border-radius: 20px; background-color: var(--ak-blue);")
        t = b.heading(f"{prefix} Title", title, tag="h3", style="font-family: var(--ak-font-heading); font-weight: 600; font-size: 22px; line-height: 1.3; color: var(--ak-navy);")
        top = b.box(f"{prefix} Title Row", icon + t, style="padding: 0; gap: 14px; align-items: center;")
        rule = b.box(f"{prefix} Divider", "", kind="e-div-block", style="width: 100%; height: 1px; min-width: 0; padding: 0; background-color: #D9DFE8;")
        s = b.heading(f"{prefix} Tagline", sub, tag="h4", style="font-family: var(--ak-font-heading); font-weight: 600; font-size: 19px; line-height: 1.3; color: var(--ak-navy);")
        p = b.para(f"{prefix} Text", text, classes=["ak-body"])
        return b.box(f"{prefix} Card", top + rule + s + p, style="flex-direction: column; align-items: flex-start; gap: 18px; padding: 28px 32px 32px; max-width: 622px; width: 100%; background-color: var(--ak-card-grey); border: 1px solid var(--ak-line); border-radius: 16px; position: relative; z-index: 2;")

    mission_card = card("Mission", "Our Mission", "Driving Trust. Delivering Excellence.", "Our mission is to provide reliable, innovative, and high-quality mobility solutions that exceed customer expectations. Through world-class automotive brands, exceptional after-sales service, and a commitment to integrity, we strive to create lasting relationships while empowering individuals and businesses with confidence on every journey.")
    vision_card = card("Vision", "Our Vision", "Shaping the Future of Mobility.", "Our vision is to be the region’s most trusted automotive partner, recognised for delivering premium vehicles, exceptional customer experiences, and sustainable mobility solutions that inspire progress for generations to come.")

    bus = b.image("Mission Bus Image", "bus", style="position: absolute; left: -654px; top: 50%; transform: translateY(-50%); width: 1294px; max-width: 1294px; height: auto; z-index: 1; @media(--tablet) { position: static; transform: translateY(0px); width: 100%; max-width: 100%; }")
    car = b.image("Vision Car Image", "car", style="position: absolute; left: 640px; top: -216px; width: 1100px; max-width: 1100px; height: auto; z-index: 1; @media(--tablet) { position: static; width: 100%; max-width: 100%; }")
    row1 = b.box("Mission Row", bus + mission_card, style="position: relative; padding: 0; width: 100%; min-height: 486px; justify-content: flex-end; align-items: center; @media(--tablet) { flex-direction: column; min-height: 0px; justify-content: flex-start; align-items: stretch; gap: 24px; } @media(--mobile) { min-height: 0px; justify-content: flex-start; }")
    row2 = b.box("Vision Row", vision_card + car, style="position: relative; padding: 0; width: 100%; justify-content: flex-start; align-items: center; margin-top: -10px; @media(--tablet) { flex-direction: column; margin-top: 0px; align-items: stretch; gap: 24px; }")
    rows = b.box("Mission Vision Rows", row1 + row2, classes=["ak-inner"], style="display: flex; flex-direction: column; gap: 80px; @media(--tablet) { gap: 56px; }")
    xml = b.box("Mission and Vision", head + rows, tag="section", classes=["ak-section"],
                style=f"position: relative; overflow: hidden; gap: 0px; padding: 90px 100px 100px; min-height: 1096px; background-color: var(--ak-white); background-image: linear-gradient(rgba(255, 255, 255, 0.7), rgba(255, 255, 255, 0.7)), url({DOTS_URL}); background-repeat: no-repeat; background-position: right top; background-size: 560px 885px; @media(--tablet) {{ min-height: 0; padding: 72px 40px; gap: 40px; }} @media(--mobile) {{ padding: 56px 20px; gap: 32px; }}")
    return xml, b


def services():
    b = B()
    head = b.heading_group(b, "Services", "WHAT WE DO", "Complete Automotive Services", "From the showroom floor to your hundredth service — one team, one standard.", title_style="max-width: 640px;", lead_style="max-width: 640px;")
    btn = b.button("Services Button", "Book a Service", "#contact", ["ak-btn", "ak-btn-outline-dark"])
    top = b.box("Services Head", head + btn, classes=["ak-inner"], style="display: flex; justify-content: space-between; align-items: flex-end; gap: 24px; @media(--mobile) { flex-direction: column; align-items: flex-start; }")
    data = [("Vehicle Sales", "New Subaru, Yutong and King Long vehicles with flexible purchase options."),
            ("Fleet &amp; Corporate", "Tailored fleet solutions for tourism, logistics, schools and government."),
            ("After-Sales Service", "Certified workshops, scheduled maintenance and advanced diagnostics."),
            ("Warranty &amp; Parts", "Manufacturer warranty support and genuine spare parts across the UAE.")]
    cards = ""
    for i, (t, d) in enumerate(data, 1):
        icon = b.box(f"Service {i} Icon", "", kind="e-div-block", classes=["ak-icon-wheel-blue"], style="width: 52px; height: 52px; min-width: 52px; padding: 0; border-radius: 26px; background-color: var(--ak-icon-bg);")
        title = b.heading(f"Service {i} Title", t, tag="h3", classes=["ak-card-title"])
        desc = b.para(f"Service {i} Text", d, classes=["ak-body"], style="line-height: 1.65;")
        link = b.para(f"Service {i} Link", "Learn more", classes=["ak-link-arrow"], link="#services", style="margin-top: auto;")
        cards += b.box(f"Service {i}", icon + title + desc + link, classes=["ak-card"], style="min-height: 305px; @media(--tablet) { min-height: 0; }")
    grid = b.box("Service Cards", cards, kind="e-grid", classes=["ak-inner"], style="grid-template-columns: repeat(4, minmax(0, 1fr)); grid-template-rows: auto; gap: 24px; @media(--tablet) { grid-template-columns: repeat(2, minmax(0, 1fr)); } @media(--mobile) { grid-template-columns: minmax(0, 1fr); }")
    xml = b.box("Services", top + grid, tag="section", classes=["ak-section"], style="background-color: var(--ak-soft);")
    return xml, b


def cta():
    b = B()
    t = b.heading("CTA Title", "Find the Right Vehicle<br>for Every Journey", classes=["ak-h2"], style="color: var(--ak-white);")
    p = b.para("CTA Text", "Whether you’re looking for a premium passenger car, a commercial fleet, or expert automotive support, our team is ready to help.", classes=["ak-lead"], style="color: rgba(255, 255, 255, 0.85); max-width: 560px;")
    text = b.box("CTA Copy", t + p, style="flex-direction: column; gap: 16px; padding: 0; max-width: 600px;")
    btns = b.button("CTA Explore Brands", "Explore Our Brands", "#brands", ["ak-btn", "ak-btn-light", "ak-btn-arrow-dark"])
    btns += b.button("CTA Contact", "Contact Our Experts", "#contact", ["ak-btn", "ak-btn-outline-light"])
    actions = b.box("CTA Buttons", btns, style="padding: 0; gap: 16px; flex-wrap: wrap;")
    banner = b.box("CTA Banner", text + actions, classes=["ak-inner"], style="display: flex; justify-content: space-between; align-items: center; gap: 40px; padding: 72px; border-radius: 28px; background-image: linear-gradient(90deg, #0A459E 0%, #2F80E8 100%); background-color: var(--ak-blue); @media(--tablet) { flex-direction: column; align-items: flex-start; padding: 56px 40px; } @media(--mobile) { padding: 40px 24px; border-radius: 20px; }")
    xml = b.box("Call to Action", banner, tag="section", classes=["ak-section"], style="padding: 100px 100px 40px; background-color: var(--ak-white); @media(--tablet) { padding: 72px 40px 24px; } @media(--mobile) { padding: 56px 20px 16px; }")
    return xml, b


def insights():
    b = B()
    head = b.heading_group(b, "Insights", "NEWS &amp; EVENTS", "Insights &amp; Updates", "The latest automotive trends, vehicle care tips and company news from Al Khoory Automobiles.", title_style="max-width: 640px;", lead_style="max-width: 640px;")
    btn = b.button("Insights Button", "View All Articles", "#insights", ["ak-btn", "ak-btn-outline-dark"])
    top = b.box("Insights Head", head + btn, classes=["ak-inner"], style="display: flex; justify-content: space-between; align-items: flex-end; gap: 24px; @media(--mobile) { flex-direction: column; align-items: flex-start; }")
    data = [("July 28, 2026&nbsp;&nbsp;&nbsp;·&nbsp;&nbsp;&nbsp;Buying Guide", "Choosing the Right Vehicle for Your Lifestyle", "Selecting the right vehicle starts with understanding your daily needs, performance expectations, and long-term value."),
            ("July 28, 2026&nbsp;&nbsp;&nbsp;·&nbsp;&nbsp;&nbsp;Vehicle Care", "Why Regular Vehicle Maintenance Matters", "Routine servicing improves performance, safety and fuel efficiency — and protects your investment for years."),
            ("July 28, 2026&nbsp;&nbsp;&nbsp;·&nbsp;&nbsp;&nbsp;Fleet", "Fleet Solutions for Modern Businesses", "The right fleet improves operational efficiency while reducing long-term ownership costs across every sector.")]
    cards = ""
    for i, (meta, t, d) in enumerate(data, 1):
        img = b.image(f"Article {i} Image", "article", style="width: 100%; height: 240px; object-fit: cover;")
        m = b.para(f"Article {i} Meta", meta, style="font-family: var(--ak-font-heading); font-weight: 500; font-size: 13px; line-height: 1.4; color: var(--ak-blue);")
        title = b.heading(f"Article {i} Title", t, tag="h3", classes=["ak-card-title"], style="font-size: 21px;")
        desc = b.para(f"Article {i} Text", d, classes=["ak-body"], style="line-height: 1.65;")
        link = b.para(f"Article {i} Link", "Read more", classes=["ak-link-arrow"], link="#insights", style="margin-top: auto;")
        body = b.box(f"Article {i} Body", m + title + desc + link, style="flex: 1 1 auto; flex-direction: column; align-items: flex-start; gap: 14px; padding: 28px 28px 32px; width: 100%;")
        cards += b.box(f"Article {i}", img + body, tag="article", classes=["ak-card"], style="padding: 0; gap: 0; min-height: 527px; @media(--tablet) { min-height: 0; }")
    grid = b.box("Articles", cards, kind="e-grid", classes=["ak-inner"], style="grid-template-columns: repeat(3, minmax(0, 1fr)); grid-template-rows: auto; gap: 24px; @media(--tablet) { grid-template-columns: repeat(2, minmax(0, 1fr)); } @media(--mobile) { grid-template-columns: minmax(0, 1fr); }")
    xml = b.box("Insights", top + grid, tag="section", classes=["ak-section"], style="background-color: var(--ak-white);")
    return xml, b


FIRST = "hero"
SECTIONS = {"hero": hero, "stats": stats, "about": about, "brands": brands, "why": why, "mission": mission, "services": services, "cta": cta, "insights": insights}

if __name__ == "__main__":
    dry = "--live" not in sys.argv
    names = [a for a in sys.argv[1:] if not a.startswith("--")]
    for n in names:
        xml, b = SECTIONS[n]()
        d = run(n, xml, b, dry)
        if d and "--show" in sys.argv:
            print(json.dumps(d)[:6000])
